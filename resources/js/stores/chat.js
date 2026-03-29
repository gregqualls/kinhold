import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/services/api'

export const useChatStore = defineStore('chat', () => {
  const messages = ref([])
  const loading = ref(false)
  const error = ref(null)
  const conversationId = ref(null)

  // Actions
  const sendMessage = async (text) => {
    loading.value = true
    error.value = null

    const optimisticId = Date.now()

    try {
      // Add user message optimistically
      messages.value.push({
        id: optimisticId,
        text,
        sender: 'user',
        created_at: new Date().toISOString(),
      })

      // Send to API
      const response = await api.post('/chat', {
        message: text,
      })

      // Remove optimistic message and add real one with AI response
      messages.value = messages.value.filter((m) => m.id !== optimisticId)

      // Normalize API shape (role/message) → display shape (sender/text)
      const normalize = (msg) => ({
        id: msg.id,
        text: msg.message ?? msg.text,
        sender: msg.role ?? msg.sender,
        created_at: msg.created_at,
      })

      // Add the actual user message and AI response
      if (response.data.user_message) {
        messages.value.push(normalize(response.data.user_message))
      }
      if (response.data.assistant_message) {
        messages.value.push(normalize(response.data.assistant_message))
      }
      return { success: true }
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to send message'
      // Remove optimistic message on error
      messages.value = messages.value.filter((m) => m.id !== optimisticId)
      return { success: false, error: error.value }
    } finally {
      loading.value = false
    }
  }

  const fetchHistory = async (conversationId) => {
    loading.value = true
    error.value = null

    try {
      const response = await api.get('/chat/history')
      messages.value = (response.data.messages || []).map((msg) => ({
        id: msg.id,
        text: msg.message ?? msg.text,
        sender: msg.role ?? msg.sender,
        created_at: msg.created_at,
      }))
      return { success: true }
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch chat history'
      return { success: false, error: error.value }
    } finally {
      loading.value = false
    }
  }

  const clearChat = async () => {
    messages.value = []
    conversationId.value = null
    error.value = null
  }

  return {
    // State
    messages,
    loading,
    error,
    conversationId,

    // Actions
    sendMessage,
    fetchHistory,
    clearChat,
  }
})
