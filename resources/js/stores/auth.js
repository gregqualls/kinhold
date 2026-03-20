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
  // Computed properties
  const isParent = computed(() => user.value?.role === 'parent')
  const familyMembers = computed(() => family.value?.members || [])
  const currentUser = computed(() => user.value)
  const enabledModules = computed(() => {
    const settings = family.value?.settings || {}
    const modules = settings.modules || {}
    return {
      calendar: modules.calendar !== false,
      tasks: modules.tasks !== false,
      vault: modules.vault !== false,
      chat: modules.chat !== false,
      points: modules.points !== false,
      badges: modules.badges !== false,
    }
  })
  const isModuleEnabled = computed(() => (moduleName) => {
    return enabledModules.value[moduleName] !== false
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
    } catch (err) {
      console.error('Logout error:', err)
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
    } catch (err) {
      isAuthenticated.value = false
      user.value = null
      family.value = null
    }
  }

  // Restore auth from saved token on app init
  const initAuth = async () => {
    // Check for OAuth callback token in URL
    const urlParams = new URLSearchParams(window.location.search)
    const oauthToken = urlParams.get('token')

    if (oauthToken) {
      localStorage.setItem('auth_token', oauthToken)
      api.defaults.headers.common['Authorization'] = `Bearer ${oauthToken}`
      const cleanUrl = window.location.pathname
      window.history.replaceState({}, document.title, cleanUrl)
      await fetchUser()
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

    // Actions
    login,
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
  }
})
