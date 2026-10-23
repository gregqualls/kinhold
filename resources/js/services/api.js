import axios from 'axios'

const api = axios.create({
  baseURL: '/api/v1',
  withCredentials: true,
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
  },
})

// Restore auth token from localStorage on init
const savedToken = localStorage.getItem('auth_token')
if (savedToken) {
  api.defaults.headers.common['Authorization'] = `Bearer ${savedToken}`
}

// CSRF: axios already sends the XSRF-TOKEN cookie as X-XSRF-TOKEN on same-origin
// requests. Never add an X-CSRF-TOKEN header here: Laravel checks it before
// X-XSRF-TOKEN, so a stale value (e.g. from a page-load meta tag) wins and 419s.
let csrfRefresh = null

function refreshCsrfCookie() {
  csrfRefresh ??= axios.get('/sanctum/csrf-cookie').finally(() => {
    csrfRefresh = null
  })
  return csrfRefresh
}

api.interceptors.request.use((config) => {
  // Let axios set the correct Content-Type (with boundary) for FormData
  if (config.data instanceof FormData) {
    delete config.headers['Content-Type']
  }

  return config
})

// Response interceptor for error handling
api.interceptors.response.use(
  (response) => response,
  async (error) => {
    // 419 = the session (and its XSRF-TOKEN cookie) expired under a long-lived
    // tab. Concurrent 419s share one refresh so they replay against one session.
    if (error.response?.status === 419 && !error.config?._csrfRetried) {
      try {
        await refreshCsrfCookie()
        error.config._csrfRetried = true
        return api.request(error.config)
      } catch {
        // Fall through to normal rejection if the refresh itself fails.
      }
    }

    if (error.response?.status === 401) {
      // Clear stored token on 401
      localStorage.removeItem('auth_token')
      delete api.defaults.headers.common['Authorization']

      // Only redirect if not already on login page
      if (!window.location.pathname.startsWith('/login')) {
        window.location.href = '/login'
      }
    }

    // 402 = paywalled. Server-side gate (#264). Re-fetch the user payload so
    // the SPA's billing state matches what the server believes; useBillingGate
    // then mounts SubscriptionPaywall on the next tick.
    if (error.response?.status === 402) {
      const skip = error.config?.url?.includes('/user') || error.config?.url?.includes('/billing')
      if (!skip) {
        import('@/stores/auth')
          .then(({ useAuthStore }) => useAuthStore().fetchUser())
          .catch(() => {})
      }
    }

    // Return the error so it can be handled in the store/component
    return Promise.reject(error)
  }
)

export default api
