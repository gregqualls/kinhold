import { ref, onMounted } from 'vue'
import { DateTime } from 'luxon'
import { useAuthStore } from '@/stores/auth'
import api from '@/services/api'

/**
 * Composable for fetching data from a widget's configured API endpoint.
 *
 * Resolves special param values:
 *  - "me" → current user's UUID
 *  - "today" → today's date range (start/end ISO strings)
 *
 * @param {string|null} endpoint - API endpoint path (relative to /api/v1)
 * @param {Object} params - Query parameters
 * @param {Object} options - { transformKey, autoFetch }
 */
export function useWidgetData(endpoint, params = {}, options = {}) {
  const data = ref(null)
  const loading = ref(false)
  const error = ref(null)

  const authStore = useAuthStore()

  function resolveParams(raw) {
    const resolved = { ...raw }
    const now = DateTime.now()

    for (const [key, value] of Object.entries(resolved)) {
      if (value === 'me') {
        resolved[key] = authStore.currentUser?.id
      } else if (value === 'today') {
        // For date range params, replace with start/end
        delete resolved[key]
        resolved.start = now.toFormat('yyyy-MM-dd')
        resolved.end = now.toFormat('yyyy-MM-dd')
      }
    }

    return resolved
  }

  async function fetch() {
    if (!endpoint) return

    loading.value = true
    error.value = null

    try {
      // Strip /api/v1 prefix if present since api service already has it as baseURL
      const path = endpoint.replace(/^\/api\/v1/, '')
      const resolvedParams = resolveParams(params)

      const response = await api.get(path, { params: resolvedParams })
      let result = response.data

      // Extract nested key if specified (e.g., "balance" from { balance: 150 })
      if (options.transformKey && result && typeof result === 'object') {
        result = result[options.transformKey] ?? result
      }

      data.value = result
    } catch (e) {
      error.value = e.response?.data?.message || e.message || 'Failed to load data'
      data.value = null
    } finally {
      loading.value = false
    }
  }

  if (options.autoFetch !== false) {
    onMounted(fetch)
  }

  return { data, loading, error, refresh: fetch }
}
