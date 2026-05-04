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

// Request interceptor to add CSRF token and fix Content-Type for file uploads
api.interceptors.request.use((config) => {
  const token = document.querySelector('meta[name="csrf-token"]')?.content
  if (token) {
    config.headers['X-CSRF-TOKEN'] = token
  }

  // Let axios set the correct Content-Type (with boundary) for FormData
  if (config.data instanceof FormData) {
    delete config.headers['Content-Type']
  }

  return config
})

// Response interceptor for error handling
api.interceptors.response.use(
  (response) => response,
  (error) => {
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
