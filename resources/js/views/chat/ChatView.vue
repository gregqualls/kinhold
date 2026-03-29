<template>
  <div class="h-full flex flex-col">
    <!-- Setup Prompt: No API Key -->
    <div v-if="!checkingKey && !hasApiKey" class="flex-1 flex items-center justify-center px-4 md:px-6 py-4">
      <div class="text-center max-w-sm">
        <div class="w-16 h-16 rounded-2xl bg-golden-50 dark:bg-golden-900/20 flex items-center justify-center mx-auto mb-4">
          <Cog6ToothIcon class="w-8 h-8 text-golden-500" />
        </div>
        <h2 class="text-xl font-bold font-heading text-prussian-500 dark:text-lavender-200 mb-2">
          {{ isParent ? 'Set Up AI Chat' : 'Chat Not Available' }}
        </h2>
        <p class="text-sm text-lavender-500 dark:text-lavender-400 mb-6">
          <template v-if="isParent">
            The AI assistant needs an API key to work. Add your Anthropic API key in
            <strong>Settings &gt; API Configuration</strong> to activate this feature.
          </template>
          <template v-else>
            This feature hasn't been set up yet. Ask a parent to configure it in Settings.
          </template>
        </p>
        <router-link
          v-if="isParent"
          to="/settings"
          class="inline-flex items-center gap-2 px-5 py-2.5 bg-wisteria-600 text-white rounded-xl hover:bg-wisteria-500 transition-colors text-sm font-medium"
        >
          Go to Settings
          <ArrowRightIcon class="w-4 h-4" />
        </router-link>
      </div>
    </div>

    <!-- Messages Container -->
    <div v-else-if="!checkingKey" ref="messagesContainer" class="flex-1 overflow-y-auto px-4 md:px-6 py-4">
      <!-- Welcome / Empty State -->
      <div v-if="messages.length === 0 && !loading" class="flex items-center justify-center h-full">
        <div class="text-center max-w-sm">
          <div class="w-16 h-16 rounded-2xl bg-wisteria-50 dark:bg-wisteria-900/20 flex items-center justify-center mx-auto mb-4">
            <SparklesIcon class="w-8 h-8 text-wisteria-500" />
          </div>
          <h2 class="text-xl font-bold font-heading text-prussian-500 dark:text-lavender-200 mb-2">Ask Kinhold Anything</h2>
          <p class="text-sm text-lavender-500 dark:text-lavender-400 mb-8">
            I can help with calendar events, tasks, vault info, and more.
          </p>

          <!-- Suggested Questions -->
          <div class="space-y-2">
            <button
              v-for="(q, idx) in suggestedQuestions"
              :key="idx"
              @click="quickSend(q)"
              class="w-full text-left px-4 py-3 bg-white dark:bg-prussian-800 border border-lavender-200 dark:border-prussian-700 hover:border-wisteria-300 dark:hover:border-wisteria-700 hover:bg-wisteria-50 dark:hover:bg-wisteria-900/10 rounded-xl transition-all text-sm text-prussian-500 dark:text-lavender-200"
            >
              {{ q }}
            </button>
          </div>
        </div>
      </div>

      <!-- Messages -->
      <div v-else class="space-y-4 max-w-2xl mx-auto">
        <div
          v-for="message in messages"
          :key="message.id"
          class="flex gap-3"
          :class="message.sender === 'user' ? 'justify-end' : 'justify-start'"
        >
          <!-- AI Avatar -->
          <div
            v-if="message.sender !== 'user'"
            class="w-8 h-8 rounded-full bg-wisteria-100 dark:bg-wisteria-900/30 flex items-center justify-center flex-shrink-0 mt-1"
          >
            <SparklesIcon class="w-4 h-4 text-wisteria-600 dark:text-wisteria-400" />
          </div>

          <!-- Bubble -->
          <div
            class="max-w-[75%] rounded-2xl px-4 py-3"
            :class="message.sender === 'user'
              ? 'bg-wisteria-600 text-white rounded-br-md'
              : 'bg-lavender-100 dark:bg-prussian-700 text-prussian-500 dark:text-lavender-200 rounded-bl-md'"
          >
            <p class="text-sm whitespace-pre-wrap leading-relaxed">{{ message.text }}</p>
            <p
              class="text-[10px] mt-1.5"
              :class="message.sender === 'user' ? 'text-wisteria-200' : 'text-lavender-400 dark:text-lavender-500'"
            >
              {{ formatTime(message.created_at) }}
            </p>
          </div>

          <!-- User Avatar -->
          <div
            v-if="message.sender === 'user'"
            class="w-8 h-8 rounded-full bg-wisteria-600 flex items-center justify-center flex-shrink-0 mt-1"
          >
            <span class="text-white font-semibold text-xs">
              {{ currentUser?.name?.charAt(0)?.toUpperCase() || '?' }}
            </span>
          </div>
        </div>

        <!-- Typing indicator -->
        <div v-if="loading" class="flex gap-3">
          <div class="w-8 h-8 rounded-full bg-wisteria-100 dark:bg-wisteria-900/30 flex items-center justify-center flex-shrink-0 mt-1">
            <SparklesIcon class="w-4 h-4 text-wisteria-600 dark:text-wisteria-400" />
          </div>
          <div class="bg-lavender-100 dark:bg-prussian-700 rounded-2xl rounded-bl-md px-4 py-3">
            <div class="flex gap-1">
              <span class="w-2 h-2 bg-lavender-400 dark:bg-lavender-500 rounded-full animate-bounce" style="animation-delay: 0ms" />
              <span class="w-2 h-2 bg-lavender-400 dark:bg-lavender-500 rounded-full animate-bounce" style="animation-delay: 150ms" />
              <span class="w-2 h-2 bg-lavender-400 dark:bg-lavender-500 rounded-full animate-bounce" style="animation-delay: 300ms" />
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Input Area -->
    <div v-if="!checkingKey && hasApiKey" class="border-t border-lavender-200 dark:border-prussian-700 bg-white dark:bg-prussian-800 px-4 md:px-6 py-3 pb-safe-bottom">
      <form @submit.prevent="handleSend" class="max-w-2xl mx-auto flex gap-2">
        <input
          v-model="messageInput"
          type="text"
          placeholder="Ask Kinhold..."
          class="flex-1 px-4 py-2.5 bg-lavender-50 dark:bg-prussian-700 border border-lavender-200 dark:border-prussian-600 rounded-xl text-sm placeholder-lavender-400 dark:placeholder-lavender-500 text-prussian-500 dark:text-lavender-200 focus:bg-white dark:focus:bg-prussian-800 focus:ring-2 focus:ring-wisteria-400 transition-all outline-none"
          :disabled="loading"
        />
        <button
          type="submit"
          :disabled="!messageInput.trim() || loading"
          class="p-2.5 rounded-xl bg-wisteria-600 text-white hover:bg-wisteria-500 disabled:opacity-40 disabled:cursor-not-allowed transition-colors"
        >
          <PaperAirplaneIcon class="w-5 h-5" />
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick, watch } from 'vue'
import { storeToRefs } from 'pinia'
import { useChatStore } from '@/stores/chat'
import { useAuthStore } from '@/stores/auth'
import api from '@/services/api'
import {
  SparklesIcon,
  PaperAirplaneIcon,
  Cog6ToothIcon,
  ArrowRightIcon,
} from '@heroicons/vue/24/outline'

const chatStore = useChatStore()
const authStore = useAuthStore()

const { messages, loading } = storeToRefs(chatStore)
const { currentUser } = storeToRefs(authStore)
const isParent = computed(() => authStore.isParent)

const messageInput = ref('')
const messagesContainer = ref(null)
const hasApiKey = ref(true) // assume true until checked to avoid flash
const checkingKey = ref(true)

const suggestedQuestions = [
  "What's on my calendar today?",
  "What tasks do I need to complete?",
  "Who's birthday is coming up?",
  "What's in the family vault?",
]

const formatTime = (dateStr) => {
  if (!dateStr) return ''
  try {
    return new Date(dateStr).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' })
  } catch {
    return ''
  }
}

const scrollToBottom = async () => {
  await nextTick()
  if (messagesContainer.value) {
    messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
  }
}

const handleSend = async () => {
  if (!messageInput.value.trim()) return
  const text = messageInput.value
  messageInput.value = ''
  await chatStore.sendMessage(text)
  scrollToBottom()
}

const quickSend = async (text) => {
  messageInput.value = text
  await handleSend()
}

watch(messages, scrollToBottom, { deep: true })

onMounted(async () => {
  try {
    const { data } = await api.get('/settings')
    hasApiKey.value = data.settings?.ai_mode === 'kinhold' || data.settings?.ai_has_key || false
  } catch {
    // If settings fetch fails, let them try chatting (backend will error gracefully)
    hasApiKey.value = true
  } finally {
    checkingKey.value = false
  }
  scrollToBottom()
})
</script>
