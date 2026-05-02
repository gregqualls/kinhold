import { defineStore } from 'pinia'
import { computed, ref } from 'vue'
import api from '../services/api'

const DEFAULT_STORAGE = () => ({
  used_bytes: 0,
  included_bytes: 5 * 1024 ** 3,
  overage_gb: 0,
  overage_cents: 0,
  over_limit: false,
  last_calculated_at: null,
})

const DEFAULT_SUMMARY = () => ({
  plan: 'none',
  status: null,
  on_trial: false,
  on_grace_period: false,
  cancelled: false,
  trial_ends_at: null,
  ends_at: null,
  trial_eligible: false,
  trial_days: 0,
  base_price_cents: 0,
  payment_method: null,
  storage: DEFAULT_STORAGE(),
})

export const useBillingStore = defineStore('billing', () => {
  const summary = ref(DEFAULT_SUMMARY())
  const loading = ref(false)
  const lastError = ref('')

  const isSubscribed = computed(() => summary.value.plan === 'base' && !!summary.value.status)
  const isOnTrial = computed(() => !!summary.value.on_trial)
  const isOnGracePeriod = computed(() => !!summary.value.on_grace_period)
  const isCancelled = computed(() => !!summary.value.cancelled)

  function mergeSummary(data) {
    const incoming = data || {}
    return {
      ...DEFAULT_SUMMARY(),
      ...incoming,
      storage: { ...DEFAULT_STORAGE(), ...(incoming.storage || {}) },
    }
  }

  async function fetch() {
    loading.value = true
    lastError.value = ''
    try {
      const { data } = await api.get('/billing/current')
      summary.value = mergeSummary(data)
    } catch (e) {
      lastError.value = e?.response?.data?.message || 'Failed to load billing.'
      throw e
    } finally {
      loading.value = false
    }
  }

  async function startCheckout(returnUrls) {
    loading.value = true
    lastError.value = ''
    try {
      const { data } = await api.post('/billing/checkout-session', {
        success_url: returnUrls.success,
        cancel_url: returnUrls.cancel,
      })
      window.location.assign(data.url)
    } catch (e) {
      lastError.value = e?.response?.data?.message || 'Failed to start checkout.'
      loading.value = false
      throw e
    }
  }

  async function openPortal(returnUrl) {
    loading.value = true
    lastError.value = ''
    try {
      const { data } = await api.post('/billing/portal-session', {
        return_url: returnUrl,
      })
      window.location.assign(data.url)
    } catch (e) {
      lastError.value = e?.response?.data?.message || 'Failed to open billing portal.'
      loading.value = false
      throw e
    }
  }

  async function cancel() {
    loading.value = true
    lastError.value = ''
    try {
      const { data } = await api.post('/billing/cancel')
      summary.value = mergeSummary(data)
    } catch (e) {
      lastError.value = e?.response?.data?.message || 'Failed to cancel subscription.'
      throw e
    } finally {
      loading.value = false
    }
  }

  async function resume() {
    loading.value = true
    lastError.value = ''
    try {
      const { data } = await api.post('/billing/resume')
      summary.value = mergeSummary(data)
    } catch (e) {
      lastError.value = e?.response?.data?.message || 'Failed to resume subscription.'
      throw e
    } finally {
      loading.value = false
    }
  }

  return {
    summary,
    loading,
    lastError,
    isSubscribed,
    isOnTrial,
    isOnGracePeriod,
    isCancelled,
    fetch,
    startCheckout,
    openPortal,
    cancel,
    resume,
  }
})
