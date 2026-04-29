import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/services/api'

const emptyUsage = () => ({
  count: 0,
  limit: null,
  remaining: null,
  reset_at: null,
  enforced: false,
  plan: null,
})

export const useChatStore = defineStore('chat', () => {
  const messages = ref([])
  const loading = ref(false)
  const error = ref(null)
  const conversationId = ref(null)
  const usage = ref(emptyUsage())
  const limitReached = ref(false)

  const usagePercent = computed(() => {
    if (!usage.value.enforced || !usage.value.limit) return 0
    return Math.min(100, Math.round((usage.value.count / usage.value.limit) * 100))
  })

  // Apply a usage payload from the API. The 80%-warning toast lives in
  // ChatView.vue (watch on `usage`) — keeping all toast/UI side-effects out
  // of the store avoids cross-module-instance issues with composables.
  const applyUsage = (payload) => {
    if (!payload) return
    usage.value = payload
    limitReached.value = payload.enforced && payload.remaining <= 0
  }

  const sendMessage = async (text) => {
    loading.value = true
    error.value = null

    const optimisticId = Date.now()

    try {
      messages.value.push({
        id: optimisticId,
        text,
        sender: 'user',
        created_at: new Date().toISOString(),
      })

      const response = await api.post('/chat', {
        message: text,
      }, {
        timeout: 120000,
      })

      messages.value = messages.value.filter((m) => m.id !== optimisticId)

      const normalize = (msg) => ({
        id: msg.id,
        text: msg.message ?? msg.text,
        sender: msg.role ?? msg.sender,
        created_at: msg.created_at,
      })

      if (response.data.user_message) {
        messages.value.push(normalize(response.data.user_message))
      }
      if (response.data.assistant_message) {
        messages.value.push(normalize(response.data.assistant_message))
      }
      applyUsage(response.data.usage)
      return { success: true }
    } catch (err) {
      // 429 = daily-limit lockout. Body still contains a usage payload so the
      // chip and the lockout card both light up correctly.
      if (err.response?.status === 429 && err.response.data?.error === 'usage_limit_reached') {
        // Roll back the optimistic user message — the request was rejected
        // before the server stored it.
        messages.value = messages.value.filter((m) => m.id !== optimisticId)
        applyUsage(err.response.data.usage)
        limitReached.value = true
        error.value = err.response.data.message || 'Daily limit reached'
        return { success: false, error: error.value, limitReached: true }
      }

      error.value = err.response?.data?.message || 'Failed to send message'
      messages.value.push({
        id: Date.now() + 1,
        text: `Something went wrong: ${error.value}. Please try again.`,
        sender: 'assistant',
        created_at: new Date().toISOString(),
        isError: true,
      })
      return { success: false, error: error.value }
    } finally {
      loading.value = false
    }
  }

  const fetchHistory = async (_conversationId) => {
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
      applyUsage(response.data.usage)
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
    usage.value = emptyUsage()
    limitReached.value = false
  }

  return {
    // State
    messages,
    loading,
    error,
    conversationId,
    usage,
    limitReached,

    // Computed
    usagePercent,

    // Actions
    sendMessage,
    fetchHistory,
    clearChat,
  }
})
