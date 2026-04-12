import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'
import api from '@/services/api'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const family = ref(null)
  const isAuthenticated = ref(false)
  const isLoading = ref(false)
  const error = ref(null)
  const initialAuthChecked = ref(false)
  const pendingLink = ref(null) // { code, email } when Google OAuth needs password confirmation
  const services = ref(null) // { google_oauth, google_calendar, ai_platform_key, ai_family_key, mail }
  const appConfig = ref(null) // Public config from /api/v1/config (available pre-auth)
  // Computed properties
  const isParent = computed(() => user.value?.role === 'parent')
  const familyMembers = computed(() => family.value?.members || [])
  const currentUser = computed(() => user.value)
  /**
   * Granular module access map from the API.
   * Each key is a module name, value is { mode, roles?, users? }.
   */
  const moduleAccess = computed(() => {
    return family.value?.module_access || {}
  })

  /**
   * Check whether the current user can access a specific module.
   *
   * Parents always have access unless the module is globally 'off'.
   * Backward-compatible: if no module_access data exists, falls back to
   * the legacy boolean settings.modules toggle.
   */
  const userCanAccessModule = (moduleName) => {
    const access = moduleAccess.value[moduleName]
    const currentUserId = user.value?.id
    const currentRole = user.value?.role || user.value?.family_role

    // If we have no granular access data, fall back to legacy
    if (!access) {
      const settings = family.value?.settings || {}
      const modules = settings.modules || {}
      return modules[moduleName] !== false
    }

    const mode = access.mode || 'all'

    if (mode === 'off') return false
    if (mode === 'all') return true

    // Parents always have access when not 'off'
    if (currentRole === 'parent') return true

    if (mode === 'roles') {
      const allowedRoles = access.roles || []
      return allowedRoles.includes(currentRole)
    }

    if (mode === 'users') {
      const allowedUsers = access.users || []
      return allowedUsers.includes(currentUserId)
    }

    return false
  }

  /**
   * Legacy computed — now backed by the granular system.
   * Returns a map of { moduleName: boolean } for the current user.
   */
  const enabledModules = computed(() => {
    const modules = ['calendar', 'tasks', 'vault', 'chat', 'points', 'badges', 'food']
    const result = {}
    for (const mod of modules) {
      result[mod] = userCanAccessModule(mod)
    }
    return result
  })

  const isModuleEnabled = computed(() => (moduleName) => {
    return userCanAccessModule(moduleName)
  })

  const canAssignTasks = computed(() => {
    if (!user.value) return false
    // Parents can always assign tasks to anyone
    if (user.value.role === 'parent') return true

    const settings = family.value?.settings || {}
    const taskAssignment = settings.task_assignment || { mode: 'all', users: [] }
    const mode = taskAssignment.mode || 'all'

    if (mode === 'all') return true
    if (mode === 'parents_only') return false
    if (mode === 'users') return (taskAssignment.users || []).includes(user.value.id)
    return true
  })

  // Actions
  const login = async (email, password) => {
    isLoading.value = true
    error.value = null

    try {
      // Get CSRF cookie first (Sanctum SPA auth)
      await axios.get('/sanctum/csrf-cookie')

      // Login
      const response = await api.post('/login', { email, password })

      // Store token for API authentication
      if (response.data.token) {
        localStorage.setItem('auth_token', response.data.token)
        api.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`
      }

      user.value = response.data.user
      isAuthenticated.value = true

      // Fetch full user + family data
      await fetchUser()

      return { success: true }
    } catch (err) {
      error.value = err.response?.data?.message || 'Login failed'
      return { success: false, error: error.value }
    } finally {
      isLoading.value = false
    }
  }

  const demoLogin = async (member) => {
    isLoading.value = true
    error.value = null

    try {
      await axios.get('/sanctum/csrf-cookie')
      const response = await api.post('/demo-login', { member })

      if (response.data.token) {
        localStorage.setItem('auth_token', response.data.token)
        api.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`
      }

      user.value = response.data.user
      isAuthenticated.value = true
      await fetchUser()

      return { success: true }
    } catch (err) {
      error.value = err.response?.data?.message || 'Demo login failed'
      return { success: false, error: error.value }
    } finally {
      isLoading.value = false
    }
  }

  const register = async (data) => {
    isLoading.value = true
    error.value = null

    try {
      await axios.get('/sanctum/csrf-cookie')
      const response = await api.post('/register', data)

      if (response.data.token) {
        localStorage.setItem('auth_token', response.data.token)
        api.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`
      }

      user.value = response.data.user
      family.value = response.data.family
      isAuthenticated.value = true

      return { success: true }
    } catch (err) {
      error.value = err.response?.data?.message || 'Registration failed'
      return { success: false, error: error.value }
    } finally {
      isLoading.value = false
    }
  }

  const logout = async () => {
    isLoading.value = true

    try {
      await api.post('/logout')
      localStorage.removeItem('auth_token')
      delete api.defaults.headers.common['Authorization']
      user.value = null
      family.value = null
      isAuthenticated.value = false
      error.value = null
    } catch {
      // Logout failed — clear local state anyway
    } finally {
      isLoading.value = false
    }
  }

  const fetchUser = async () => {
    try {
      const response = await api.get('/user')
      user.value = response.data.user
      family.value = response.data.family
      isAuthenticated.value = true
      // Load service availability in the background
      fetchServices()
    } catch {
      isAuthenticated.value = false
      user.value = null
      family.value = null
    }
  }

  const fetchAppConfig = async () => {
    try {
      const { data } = await api.get('/config')
      appConfig.value = data
    } catch {
      // Non-critical — buttons stay visible by default
    }
  }

  // Restore auth from saved token on app init
  const initAuth = async () => {
    // Fetch public config (services availability) — awaited so router guard has appConfig
    await fetchAppConfig()

    // Check for OAuth callback auth code in URL (exchanged for token securely via POST)
    const urlParams = new URLSearchParams(window.location.search)
    const oauthCode = urlParams.get('code')

    if (oauthCode) {
      // Clean URL immediately so the code isn't visible
      window.history.replaceState({}, document.title, window.location.pathname)
      try {
        const response = await api.post('/auth/exchange', { code: oauthCode })
        const oauthToken = response.data.token
        localStorage.setItem('auth_token', oauthToken)
        api.defaults.headers.common['Authorization'] = `Bearer ${oauthToken}`
        await fetchUser()
      } catch {
        error.value = 'Google sign-in failed. Please try again.'
      }
      initialAuthChecked.value = true
      return
    }

    // Check for pending Google account link (user has existing password account)
    const linkPending = urlParams.get('link_pending')
    if (linkPending) {
      const linkEmail = urlParams.get('email') || ''
      pendingLink.value = { code: linkPending, email: decodeURIComponent(linkEmail) }
      window.history.replaceState({}, document.title, window.location.pathname)
      initialAuthChecked.value = true
      return
    }

    const authError = urlParams.get('auth_error')
    if (authError) {
      error.value = decodeURIComponent(authError)
      window.history.replaceState({}, document.title, window.location.pathname)
    }

    const savedToken = localStorage.getItem('auth_token')
    if (savedToken) {
      api.defaults.headers.common['Authorization'] = `Bearer ${savedToken}`
      await fetchUser()
    }
    initialAuthChecked.value = true
  }

  const confirmGoogleLink = async (pendingCode, password) => {
    isLoading.value = true
    try {
      const response = await api.post('/auth/google/confirm-link', {
        pending_code: pendingCode,
        password,
      })
      const token = response.data.token
      localStorage.setItem('auth_token', token)
      api.defaults.headers.common['Authorization'] = `Bearer ${token}`
      pendingLink.value = null
      await fetchUser()
      return { success: true }
    } catch (err) {
      return { success: false, error: err.response?.data?.message || 'Failed to link account' }
    } finally {
      isLoading.value = false
    }
  }

  const fetchServices = async () => {
    try {
      const { data } = await api.get('/settings')
      if (data.settings?.services) {
        services.value = data.settings.services
      }
    } catch {
      // Non-critical — services stay null, UI degrades gracefully
    }
  }

  const isServiceAvailable = computed(() => (serviceName) => {
    if (!services.value) return true // Assume available until loaded
    return !!services.value[serviceName]
  })

  const resendVerification = async () => {
    try {
      await api.post('/email/resend')
      return { success: true }
    } catch (err) {
      return { success: false, error: err.response?.data?.message || 'Failed to send verification email' }
    }
  }

  const updateFamilyName = async (name) => {
    try {
      const response = await api.put('/family', { name })
      family.value = response.data.data
      return { success: true }
    } catch (err) {
      return { success: false, error: err.response?.data?.message }
    }
  }

  // Family member management
  const addFamilyMember = async (memberData) => {
    try {
      const response = await api.post('/family/members', memberData)
      await fetchUser()
      return { success: true, member: response.data.member, message: response.data.message }
    } catch (err) {
      return { success: false, error: err.response?.data?.message || 'Failed to add member' }
    }
  }

  const updateFamilyMember = async (memberId, memberData) => {
    try {
      const response = await api.put(`/family/members/${memberId}`, memberData)
      await fetchUser()
      return { success: true, member: response.data.member }
    } catch (err) {
      return { success: false, error: err.response?.data?.message || 'Failed to update member' }
    }
  }

  const removeFamilyMember = async (memberId) => {
    try {
      await api.delete(`/family/members/${memberId}`)
      await fetchUser()
      return { success: true }
    } catch (err) {
      return { success: false, error: err.response?.data?.message }
    }
  }

  const getInviteCode = async () => {
    try {
      const response = await api.post('/family/invite')
      return { success: true, invite_code: response.data.invite_code }
    } catch (err) {
      return { success: false, error: err.response?.data?.message }
    }
  }

  // Profile switching
  const switchToProfile = async (userId) => {
    try {
      const response = await api.post('/auth/switch-profile', { user_id: userId })

      if (response.data.token) {
        localStorage.setItem('auth_token', response.data.token)
        api.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`
      }

      user.value = response.data.user
      await fetchUser()

      return { success: true, message: response.data.message }
    } catch (err) {
      return { success: false, error: err.response?.data?.message || 'Failed to switch profile' }
    }
  }

  /**
   * Optimistically update the user's avatar in local state, then refresh from server.
   */
  const updateUserAvatar = async (newAvatar) => {
    if (user.value) {
      user.value.avatar = newAvatar
    }
    // Also update the member entry in the family members list
    if (family.value?.members) {
      const member = family.value.members.find((m) => m.id === user.value?.id)
      if (member) member.avatar = newAvatar
    }
    await fetchUser()
  }

  return {
    // State
    user,
    family,
    isAuthenticated,
    isLoading,
    error,
    initialAuthChecked,

    // Computed
    isParent,
    familyMembers,
    currentUser,
    enabledModules,
    isModuleEnabled,
    canAssignTasks,
    moduleAccess,
    userCanAccessModule,

    // Actions
    login,
    demoLogin,
    register,
    logout,
    fetchUser,
    initAuth,
    updateFamilyName,
    addFamilyMember,
    updateFamilyMember,
    removeFamilyMember,
    getInviteCode,
    switchToProfile,
    updateUserAvatar,
    confirmGoogleLink,
    resendVerification,
    pendingLink,
    services,
    isServiceAvailable,
    fetchServices,
    appConfig,
  }
})
