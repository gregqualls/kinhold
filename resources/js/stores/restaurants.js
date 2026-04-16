import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/services/api'

export const useRestaurantsStore = defineStore('restaurants', () => {
  // State
  const restaurants = ref([])
  const currentRestaurant = ref(null)
  const isLoading = ref(false)
  const error = ref(null)
  const searchQuery = ref('')

  // Computed
  const filteredRestaurants = computed(() => {
    if (!searchQuery.value) return restaurants.value
    const q = searchQuery.value.toLowerCase()
    return restaurants.value.filter(r =>
      r.name?.toLowerCase().includes(q) ||
      r.cuisine?.toLowerCase().includes(q) ||
      r.address?.toLowerCase().includes(q)
    )
  })

  const favoriteRestaurants = computed(() =>
    restaurants.value.filter(r => r.is_favorite)
  )

  // ── CRUD ──

  const fetchRestaurants = async () => {
    isLoading.value = true
    error.value = null
    try {
      const response = await api.get('/restaurants')
      restaurants.value = response.data.restaurants ?? []
      return { success: true }
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch restaurants'
      return { success: false, error: error.value }
    } finally {
      isLoading.value = false
    }
  }

  const fetchRestaurant = async (id) => {
    try {
      const response = await api.get(`/restaurants/${id}`)
      currentRestaurant.value = response.data.restaurant
      return { success: true, data: response.data.restaurant }
    } catch (err) {
      return { success: false, error: err.response?.data?.message || 'Failed to fetch restaurant' }
    }
  }

  const createRestaurant = async (data) => {
    try {
      const response = await api.post('/restaurants', data)
      restaurants.value.unshift(response.data.restaurant)
      return { success: true, data: response.data.restaurant }
    } catch (err) {
      return { success: false, error: err.response?.data?.message || 'Failed to create restaurant' }
    }
  }

  const importRestaurant = async (url) => {
    try {
      const response = await api.post('/restaurants/import', { url })
      restaurants.value.unshift(response.data.restaurant)
      return { success: true, data: response.data.restaurant }
    } catch (err) {
      return { success: false, error: err.response?.data?.message || 'Failed to import restaurant' }
    }
  }

  const updateRestaurant = async (id, data) => {
    try {
      const response = await api.put(`/restaurants/${id}`, data)
      const updated = response.data.restaurant
      const idx = restaurants.value.findIndex(r => r.id === id)
      if (idx !== -1) restaurants.value[idx] = updated
      if (currentRestaurant.value?.id === id) currentRestaurant.value = updated
      return { success: true, data: updated }
    } catch (err) {
      return { success: false, error: err.response?.data?.message || 'Failed to update restaurant' }
    }
  }

  const deleteRestaurant = async (id) => {
    const snapshot = [...restaurants.value]
    restaurants.value = restaurants.value.filter(r => r.id !== id)
    if (currentRestaurant.value?.id === id) currentRestaurant.value = null

    try {
      await api.delete(`/restaurants/${id}`)
      return { success: true }
    } catch (err) {
      restaurants.value = snapshot
      return { success: false, error: err.response?.data?.message || 'Failed to delete restaurant' }
    }
  }

  const rateRestaurant = async (id, score) => {
    const idx = restaurants.value.findIndex(r => r.id === id)
    const prevRating = restaurants.value[idx]?.family_average_rating

    // Optimistic: update display rating
    if (idx !== -1) {
      restaurants.value[idx] = { ...restaurants.value[idx], family_average_rating: score }
    }

    try {
      await api.post(`/restaurants/${id}/rate`, { score })
      // Refresh to get true average
      await fetchRestaurant(id)
      const updated = currentRestaurant.value
      if (idx !== -1 && updated) restaurants.value[idx] = updated
      return { success: true }
    } catch (err) {
      if (idx !== -1) {
        restaurants.value[idx] = { ...restaurants.value[idx], family_average_rating: prevRating }
      }
      return { success: false, error: err.response?.data?.message || 'Failed to rate restaurant' }
    }
  }

  const toggleFavorite = async (id) => {
    const idx = restaurants.value.findIndex(r => r.id === id)
    if (idx === -1) return { success: false }
    const prev = restaurants.value[idx].is_favorite
    restaurants.value[idx] = { ...restaurants.value[idx], is_favorite: !prev }

    try {
      const response = await api.put(`/restaurants/${id}`, { is_favorite: !prev })
      restaurants.value[idx] = response.data.restaurant
      return { success: true }
    } catch (err) {
      restaurants.value[idx] = { ...restaurants.value[idx], is_favorite: prev }
      return { success: false, error: err.response?.data?.message || 'Failed to update favorite' }
    }
  }

  return {
    // State
    restaurants,
    currentRestaurant,
    isLoading,
    error,
    searchQuery,
    // Computed
    filteredRestaurants,
    favoriteRestaurants,
    // Actions
    fetchRestaurants,
    fetchRestaurant,
    createRestaurant,
    importRestaurant,
    updateRestaurant,
    deleteRestaurant,
    rateRestaurant,
    toggleFavorite,
  }
})
