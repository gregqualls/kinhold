import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/services/api'

export const useBadgesStore = defineStore('badges', () => {
  const badges = ref([])
  const earnedBadges = ref([])
  const isLoading = ref(false)
  const error = ref(null)

  const fetchBadges = async () => {
    isLoading.value = true
    try {
      const response = await api.get('/badges')
      badges.value = response.data.badges
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch badges'
    } finally {
      isLoading.value = false
    }
  }

  const fetchEarned = async () => {
    try {
      const response = await api.get('/badges/earned')
      earnedBadges.value = response.data.badges
    } catch (err) {
      console.error('Failed to fetch earned badges:', err)
    }
  }

  const createBadge = async (data) => {
    try {
      const response = await api.post('/badges', data)
      badges.value.push({ ...response.data.badge, is_earned: false, progress: 0 })
      return { success: true, badge: response.data.badge }
    } catch (err) {
      return { success: false, error: err.response?.data?.message || 'Failed to create badge' }
    }
  }

  const updateBadge = async (badgeId, data) => {
    try {
      const response = await api.put(`/badges/${badgeId}`, data)
      const idx = badges.value.findIndex(b => b.id === badgeId)
      if (idx !== -1) {
        badges.value[idx] = { ...badges.value[idx], ...response.data.badge }
      }
      return { success: true }
    } catch (err) {
      return { success: false, error: err.response?.data?.message }
    }
  }

  const deleteBadge = async (badgeId) => {
    try {
      await api.delete(`/badges/${badgeId}`)
      badges.value = badges.value.filter(b => b.id !== badgeId)
      return { success: true }
    } catch (err) {
      return { success: false, error: err.response?.data?.message }
    }
  }

  const manuallyAward = async (badgeId, userId) => {
    try {
      const response = await api.post(`/badges/${badgeId}/award`, { user_id: userId })
      await fetchBadges()
      return { success: true, message: response.data.message }
    } catch (err) {
      return { success: false, error: err.response?.data?.message }
    }
  }

  const revokeBadge = async (badgeId, userId) => {
    try {
      await api.delete(`/badges/${badgeId}/revoke/${userId}`)
      await fetchBadges()
      return { success: true }
    } catch (err) {
      return { success: false, error: err.response?.data?.message }
    }
  }

  return {
    badges,
    earnedBadges,
    isLoading,
    error,
    fetchBadges,
    fetchEarned,
    createBadge,
    updateBadge,
    deleteBadge,
    manuallyAward,
    revokeBadge,
  }
})
