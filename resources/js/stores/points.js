import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/services/api'

export const usePointsStore = defineStore('points', () => {
  const bank = ref(0)
  const leaderboard = ref([])
  const leaderboardPeriod = ref('weekly')
  const feed = ref([])
  const feedPagination = ref({ current_page: 1, last_page: 1, total: 0 })
  const rewards = ref([])
  const purchases = ref([])
  const pointRequests = ref([])
  const isLoading = ref(false)
  const error = ref(null)

  const fetchBank = async () => {
    try {
      const response = await api.get('/points/bank')
      bank.value = response.data.bank
    } catch {
      // Bank fetch failed
    }
  }

  const fetchLeaderboard = async () => {
    try {
      const response = await api.get('/points/leaderboard')
      leaderboard.value = response.data.leaderboard
      leaderboardPeriod.value = response.data.period
    } catch {
      // Leaderboard fetch failed
    }
  }

  const fetchFeed = async (page = 1) => {
    isLoading.value = true
    try {
      const response = await api.get('/points/feed', { params: { page } })
      if (page === 1) {
        feed.value = response.data.feed
      } else {
        feed.value = [...feed.value, ...response.data.feed]
      }
      feedPagination.value = response.data.pagination
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch feed'
    } finally {
      isLoading.value = false
    }
  }

  const giveKudos = async (userId, reason) => {
    try {
      const response = await api.post('/points/kudos', { user_id: userId, reason })
      // Refresh feed and bank after kudos
      await Promise.all([fetchFeed(), fetchBank(), fetchLeaderboard()])
      return { success: true, data: response.data }
    } catch (err) {
      return { success: false, error: err.response?.data?.message || 'Failed to give kudos' }
    }
  }

  const deductPoints = async (userId, points, reason) => {
    try {
      const response = await api.post('/points/deduct', { user_id: userId, points, reason })
      await Promise.all([fetchFeed(), fetchBank(), fetchLeaderboard()])
      return { success: true, data: response.data }
    } catch (err) {
      return { success: false, error: err.response?.data?.message || 'Failed to deduct points' }
    }
  }

  const fetchRewards = async () => {
    try {
      const response = await api.get('/rewards')
      rewards.value = response.data.rewards
    } catch {
      // Rewards fetch failed
    }
  }

  const createReward = async (data) => {
    try {
      const response = await api.post('/rewards', data)
      rewards.value.push(response.data.reward)
      return { success: true, reward: response.data.reward }
    } catch (err) {
      return { success: false, error: err.response?.data?.message || 'Failed to create reward' }
    }
  }

  const updateReward = async (rewardId, data) => {
    try {
      const response = await api.put(`/rewards/${rewardId}`, data)
      const idx = rewards.value.findIndex(r => r.id === rewardId)
      if (idx !== -1) rewards.value[idx] = response.data.reward
      return { success: true }
    } catch (err) {
      return { success: false, error: err.response?.data?.message }
    }
  }

  const deleteReward = async (rewardId) => {
    try {
      await api.delete(`/rewards/${rewardId}`)
      rewards.value = rewards.value.filter(r => r.id !== rewardId)
      return { success: true }
    } catch (err) {
      return { success: false, error: err.response?.data?.message }
    }
  }

  const purchaseReward = async (rewardId) => {
    try {
      const response = await api.post(`/rewards/${rewardId}/purchase`)
      bank.value = response.data.new_bank
      await fetchFeed()
      return { success: true, data: response.data }
    } catch (err) {
      return { success: false, error: err.response?.data?.message || 'Failed to purchase reward' }
    }
  }

  const placeBid = async (rewardId, bidAmount) => {
    try {
      const response = await api.post(`/rewards/${rewardId}/bid`, { bid_amount: bidAmount })
      return { success: true, data: response.data }
    } catch (err) {
      return { success: false, error: err.response?.data?.message || 'Failed to place bid' }
    }
  }

  const fetchBids = async (rewardId) => {
    try {
      const response = await api.get(`/rewards/${rewardId}/bids`)
      return { success: true, data: response.data }
    } catch (err) {
      return { success: false, error: err.response?.data?.message || 'Failed to fetch bids' }
    }
  }

  const closeAuction = async (rewardId) => {
    try {
      const response = await api.post(`/rewards/${rewardId}/close-auction`)
      return { success: true, data: response.data }
    } catch (err) {
      return { success: false, error: err.response?.data?.message || 'Failed to close auction' }
    }
  }

  const cancelAuction = async (rewardId) => {
    try {
      const response = await api.post(`/rewards/${rewardId}/cancel-auction`)
      return { success: true, data: response.data }
    } catch (err) {
      return { success: false, error: err.response?.data?.message || 'Failed to cancel auction' }
    }
  }

  const fetchPurchases = async () => {
    try {
      const response = await api.get('/rewards/purchases')
      purchases.value = response.data.purchases
    } catch {
      // Purchases fetch failed
    }
  }

  const fetchPointRequests = async (status = null) => {
    try {
      const params = status ? { status } : {}
      const response = await api.get('/points/requests', { params })
      pointRequests.value = response.data.requests
    } catch {
      // Point requests fetch failed
    }
  }

  const submitPointRequest = async (points, reason) => {
    try {
      const response = await api.post('/points/request', { points, reason })
      pointRequests.value.unshift(response.data.request)
      return { success: true, data: response.data }
    } catch (err) {
      return { success: false, error: err.response?.data?.message || 'Failed to submit request' }
    }
  }

  const approvePointRequest = async (requestId) => {
    try {
      const response = await api.post(`/points/requests/${requestId}/approve`)
      // Remove from local list or update status
      const idx = pointRequests.value.findIndex(r => r.id === requestId)
      if (idx !== -1) pointRequests.value[idx] = response.data.request
      // Refresh feed and bank
      await Promise.all([fetchFeed(), fetchBank(), fetchLeaderboard()])
      return { success: true, data: response.data }
    } catch (err) {
      return { success: false, error: err.response?.data?.message || 'Failed to approve request' }
    }
  }

  const denyPointRequest = async (requestId) => {
    try {
      const response = await api.post(`/points/requests/${requestId}/deny`)
      const idx = pointRequests.value.findIndex(r => r.id === requestId)
      if (idx !== -1) pointRequests.value[idx] = response.data.request
      return { success: true, data: response.data }
    } catch (err) {
      return { success: false, error: err.response?.data?.message || 'Failed to deny request' }
    }
  }

  return {
    bank,
    leaderboard,
    leaderboardPeriod,
    feed,
    feedPagination,
    rewards,
    purchases,
    isLoading,
    error,
    fetchBank,
    fetchLeaderboard,
    fetchFeed,
    giveKudos,
    deductPoints,
    fetchRewards,
    createReward,
    updateReward,
    deleteReward,
    purchaseReward,
    placeBid,
    fetchBids,
    closeAuction,
    cancelAuction,
    fetchPurchases,
    pointRequests,
    fetchPointRequests,
    submitPointRequest,
    approvePointRequest,
    denyPointRequest,
  }
})
