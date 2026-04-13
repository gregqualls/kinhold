import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/services/api'

export const useShoppingStore = defineStore('shopping', () => {
  // State
  const lists = ref([])
  const activeList = ref(null)
  const catalogResults = ref([])
  const shoppingWindow = ref('all')
  const isLoading = ref(false)
  const error = ref(null)

  // Computed
  const activeItems = computed(() => activeList.value?.items || [])
  const uncheckedItems = computed(() => activeItems.value.filter((i) => !i.is_checked))
  const checkedItems = computed(() => activeItems.value.filter((i) => i.is_checked))

  const filteredItems = computed(() => {
    if (shoppingWindow.value === 'all') return uncheckedItems.value
    const days = { '2days': 2, '3days': 3, 'week': 7 }[shoppingWindow.value]
    if (!days) return uncheckedItems.value
    const cutoff = new Date()
    cutoff.setDate(cutoff.getDate() + days)
    cutoff.setHours(23, 59, 59, 999)
    return uncheckedItems.value.filter((item) => {
      if (!item.needed_date) return true
      const needed = new Date(item.needed_date)
      return needed <= cutoff
    })
  })

  const filteredItemsByCategory = computed(() => {
    const groups = {}
    for (const item of filteredItems.value) {
      const cat = item.category || 'Uncategorized'
      if (!groups[cat]) groups[cat] = []
      groups[cat].push(item)
    }
    return groups
  })

  const recurringItems = computed(() => activeItems.value.filter((i) => i.is_recurring === true))
  const itemCount = computed(() => activeItems.value.length)
  const checkedCount = computed(() => checkedItems.value.length)

  // ── Shopping Lists ──

  const fetchLists = async () => {
    isLoading.value = true
    error.value = null
    try {
      const response = await api.get('/shopping/lists')
      lists.value = response.data.data
      if (lists.value.length > 0 && !activeList.value) {
        await fetchList(lists.value[0].id)
      }
      return { success: true }
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch shopping lists'
      return { success: false, error: error.value }
    } finally {
      isLoading.value = false
    }
  }

  const fetchList = async (listId) => {
    try {
      const response = await api.get(`/shopping/lists/${listId}`)
      activeList.value = response.data.list
      return { success: true, list: response.data.list }
    } catch (err) {
      return { success: false, error: err.response?.data?.message || 'Failed to fetch list' }
    }
  }

  const createList = async (storeName) => {
    try {
      const response = await api.post('/shopping/lists', { name: storeName, store_name: storeName })
      const list = response.data.list
      lists.value.unshift(list)
      await fetchList(list.id)
      return { success: true, list }
    } catch (err) {
      return { success: false, error: err.response?.data?.message || 'Failed to create list' }
    }
  }

  // ── Shopping Items ──

  const addItem = async (listId, data) => {
    try {
      const response = await api.post(`/shopping/lists/${listId}/items`, data)
      const item = response.data.item
      if (activeList.value?.id === listId) {
        if (!activeList.value.items) activeList.value.items = []
        activeList.value.items.push(item)
      }
      return { success: true, item }
    } catch (err) {
      return { success: false, error: err.response?.data?.message || 'Failed to add item' }
    }
  }

  const updateItem = async (itemId, data) => {
    try {
      const response = await api.put(`/shopping/items/${itemId}`, data)
      const updated = response.data.item
      if (activeList.value?.items) {
        const idx = activeList.value.items.findIndex((i) => i.id === itemId)
        if (idx !== -1) activeList.value.items[idx] = updated
      }
      return { success: true, item: updated }
    } catch (err) {
      return { success: false, error: err.response?.data?.message || 'Failed to update item' }
    }
  }

  const removeItem = async (itemId) => {
    try {
      await api.delete(`/shopping/items/${itemId}`)
      if (activeList.value?.items) {
        activeList.value.items = activeList.value.items.filter((i) => i.id !== itemId)
      }
      return { success: true }
    } catch (err) {
      return { success: false, error: err.response?.data?.message || 'Failed to remove item' }
    }
  }

  const checkItem = async (itemId) => {
    const item = activeList.value?.items?.find((i) => i.id === itemId)
    if (!item) return { success: false }
    const previous = item.is_checked
    item.is_checked = true
    try {
      const response = await api.patch(`/shopping/items/${itemId}/check`)
      const idx = activeList.value.items.findIndex((i) => i.id === itemId)
      if (idx !== -1) activeList.value.items[idx] = response.data.item
      return { success: true }
    } catch (err) {
      item.is_checked = previous
      return { success: false, error: err.response?.data?.message || 'Failed to check item' }
    }
  }

  const uncheckItem = async (itemId) => {
    const item = activeList.value?.items?.find((i) => i.id === itemId)
    if (!item) return { success: false }
    const previous = item.is_checked
    item.is_checked = false
    try {
      const response = await api.patch(`/shopping/items/${itemId}/uncheck`)
      const idx = activeList.value.items.findIndex((i) => i.id === itemId)
      if (idx !== -1) activeList.value.items[idx] = response.data.item
      return { success: true }
    } catch (err) {
      item.is_checked = previous
      return { success: false, error: err.response?.data?.message || 'Failed to uncheck item' }
    }
  }

  const markOnHand = async (itemId) => {
    try {
      const response = await api.patch(`/shopping/items/${itemId}/on-hand`)
      const updated = response.data.item
      if (activeList.value?.items) {
        const idx = activeList.value.items.findIndex((i) => i.id === itemId)
        if (idx !== -1) activeList.value.items[idx] = updated
      }
      return { success: true, item: updated }
    } catch (err) {
      return { success: false, error: err.response?.data?.message || 'Failed to mark on hand' }
    }
  }

  const clearOnHand = async (itemId) => {
    try {
      const response = await api.patch(`/shopping/items/${itemId}/need`)
      const updated = response.data.item
      if (activeList.value?.items) {
        const idx = activeList.value.items.findIndex((i) => i.id === itemId)
        if (idx !== -1) activeList.value.items[idx] = updated
      }
      return { success: true, item: updated }
    } catch (err) {
      return { success: false, error: err.response?.data?.message || 'Failed to clear on hand' }
    }
  }

  const clearChecked = async (listId) => {
    try {
      const response = await api.post(`/shopping/lists/${listId}/clear-checked`)
      activeList.value = response.data.list
      return { success: true, data: response.data }
    } catch (err) {
      return { success: false, error: err.response?.data?.message || 'Failed to clear checked items' }
    }
  }

  const moveItem = async (itemId, targetListId) => {
    try {
      await api.post(`/shopping/items/${itemId}/move`, { target_list_id: targetListId })
      if (activeList.value?.items) {
        activeList.value.items = activeList.value.items.filter((i) => i.id !== itemId)
      }
      return { success: true }
    } catch (err) {
      return { success: false, error: err.response?.data?.message || 'Failed to move item' }
    }
  }

  const toggleRecurring = async (itemId) => {
    try {
      const response = await api.patch(`/shopping/items/${itemId}/toggle-recurring`)
      const updated = response.data.item
      if (activeList.value?.items) {
        const idx = activeList.value.items.findIndex((i) => i.id === itemId)
        if (idx !== -1) activeList.value.items[idx] = updated
      }
      return { success: true, item: updated }
    } catch (err) {
      return { success: false, error: err.response?.data?.message || 'Failed to toggle recurring' }
    }
  }

  const addRecipeToList = async (listId, recipeId, ingredientIds = null) => {
    try {
      const payload = { recipe_id: recipeId }
      if (ingredientIds) payload.ingredient_ids = ingredientIds
      await api.post(`/shopping/lists/${listId}/add-recipe`, payload)
      await fetchList(listId)
      return { success: true }
    } catch (err) {
      return { success: false, error: err.response?.data?.message || 'Failed to add recipe to list' }
    }
  }

  // ── Product Catalog ──

  const searchCatalog = async (query) => {
    if (!query || query.length < 2) {
      catalogResults.value = []
      return { success: true }
    }
    try {
      const response = await api.get('/shopping/catalog/search', { params: { q: query } })
      catalogResults.value = response.data.results
      return { success: true }
    } catch (err) {
      return { success: false, error: err.response?.data?.message || 'Failed to search catalog' }
    }
  }

  // ── List management ──

  const renameList = async (listId, name) => {
    try {
      const response = await api.put(`/shopping/lists/${listId}`, { name, store_name: name })
      const updated = response.data.list
      const idx = lists.value.findIndex((l) => l.id === listId)
      if (idx !== -1) lists.value[idx] = { ...lists.value[idx], ...updated }
      if (activeList.value?.id === listId) {
        activeList.value = { ...activeList.value, name: updated.name, store_name: updated.store_name }
      }
      return { success: true }
    } catch (err) {
      return { success: false, error: err.response?.data?.message || 'Failed to rename list' }
    }
  }

  const deleteList = async (listId) => {
    try {
      await api.delete(`/shopping/lists/${listId}`)
      lists.value = lists.value.filter((l) => l.id !== listId)
      if (activeList.value?.id === listId) {
        activeList.value = lists.value[0] || null
        if (activeList.value) {
          await fetchList(activeList.value.id)
        }
      }
      return { success: true }
    } catch (err) {
      return { success: false, error: err.response?.data?.message || 'Failed to delete list' }
    }
  }

  // ── Local-only ──

  const setShoppingWindow = (value) => {
    shoppingWindow.value = value
  }

  return {
    // State
    lists,
    activeList,
    catalogResults,
    shoppingWindow,
    isLoading,
    error,

    // Computed
    activeItems,
    uncheckedItems,
    checkedItems,
    filteredItems,
    filteredItemsByCategory,
    recurringItems,
    itemCount,
    checkedCount,

    // Lists
    fetchLists,
    fetchList,
    createList,
    renameList,
    deleteList,

    // Items
    addItem,
    updateItem,
    removeItem,
    checkItem,
    uncheckItem,
    markOnHand,
    clearOnHand,
    clearChecked,
    moveItem,
    toggleRecurring,
    addRecipeToList,

    // Catalog
    searchCatalog,

    // Local
    setShoppingWindow,
  }
})
