import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/services/api'

export const useVaultStore = defineStore('vault', () => {
  const categories = ref([])
  const entries = ref([])
  const currentEntry = ref(null)
  const currentCategory = ref(null)
  const isLoading = ref(false)
  const error = ref(null)

  // Computed
  const entriesByCategory = computed(() => {
    const grouped = {}
    categories.value.forEach((cat) => {
      grouped[cat.id] = entries.value.filter((e) => e.category_id === cat.id)
    })
    return grouped
  })

  const accessibleEntries = computed(() =>
    entries.value.filter((entry) => entry.can_view)
  )

  // Actions
  const fetchCategories = async () => {
    isLoading.value = true
    error.value = null

    try {
      const response = await api.get('/vault/categories')
      categories.value = response.data.categories
      return { success: true }
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch categories'
      return { success: false, error: error.value }
    } finally {
      isLoading.value = false
    }
  }

  const fetchEntries = async (categoryId = null) => {
    isLoading.value = true
    error.value = null

    try {
      const url = categoryId ? `/vault/categories/${categoryId}/entries` : '/vault/entries'
      const response = await api.get(url)

      entries.value = response.data.entries

      if (categoryId) {
        currentCategory.value = categories.value.find((c) => c.id === categoryId)
      }

      return { success: true }
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch entries'
      return { success: false, error: error.value }
    } finally {
      isLoading.value = false
    }
  }

  const fetchEntry = async (id) => {
    isLoading.value = true
    error.value = null

    try {
      const response = await api.get(`/vault/entries/${id}`)
      currentEntry.value = response.data.entry
      return { success: true }
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch entry'
      return { success: false, error: error.value }
    } finally {
      isLoading.value = false
    }
  }

  const createEntry = async (data) => {
    try {
      const response = await api.post('/vault/entries', data)
      entries.value.push(response.data.entry)
      return { success: true, entry: response.data.entry }
    } catch (err) {
      return { success: false, error: err.response?.data?.message }
    }
  }

  const updateEntry = async (id, data) => {
    try {
      const response = await api.put(`/vault/entries/${id}`, data)
      const index = entries.value.findIndex((e) => e.id === id)
      if (index !== -1) {
        entries.value[index] = response.data.entry
      }
      currentEntry.value = response.data.entry
      return { success: true, entry: response.data.entry }
    } catch (err) {
      return { success: false, error: err.response?.data?.message }
    }
  }

  const deleteEntry = async (id) => {
    try {
      await api.delete(`/vault/entries/${id}`)
      entries.value = entries.value.filter((e) => e.id !== id)
      if (currentEntry.value?.id === id) {
        currentEntry.value = null
      }
      return { success: true }
    } catch (err) {
      return { success: false, error: err.response?.data?.message }
    }
  }

  const grantPermission = async (entryId, userId, level = 'view') => {
    try {
      await api.post(`/vault/entries/${entryId}/permissions`, {
        user_id: userId,
        level,
      })
      await fetchEntry(entryId)
      return { success: true }
    } catch (err) {
      return { success: false, error: err.response?.data?.message }
    }
  }

  const revokePermission = async (entryId, userId) => {
    try {
      await api.delete(`/vault/entries/${entryId}/permissions/${userId}`)
      await fetchEntry(entryId)
      return { success: true }
    } catch (err) {
      return { success: false, error: err.response?.data?.message }
    }
  }

  const uploadDocument = async (entryId, file) => {
    try {
      const formData = new FormData()
      formData.append('file', file)

      const response = await api.post(`/vault/entries/${entryId}/documents`, formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      })

      await fetchEntry(entryId)
      return { success: true, document: response.data.document }
    } catch (err) {
      return { success: false, error: err.response?.data?.message }
    }
  }

  const deleteDocument = async (docId) => {
    try {
      await api.delete(`/vault/documents/${docId}`)
      if (currentEntry.value) {
        currentEntry.value.documents = currentEntry.value.documents.filter(
          (d) => d.id !== docId
        )
      }
      return { success: true }
    } catch (err) {
      return { success: false, error: err.response?.data?.message }
    }
  }

  return {
    // State
    categories,
    entries,
    currentEntry,
    currentCategory,
    isLoading,
    error,

    // Computed
    entriesByCategory,
    accessibleEntries,

    // Actions
    fetchCategories,
    fetchEntries,
    fetchEntry,
    createEntry,
    updateEntry,
    deleteEntry,
    grantPermission,
    revokePermission,
    uploadDocument,
    deleteDocument,
  }
})
