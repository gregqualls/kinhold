<template>
  <div class="h-full flex flex-col">
    <!-- Setup Prompt: No API Key -->
    <div v-if="!checkingKey && !hasApiKey" class="flex-1 flex items-center justify-center px-4 md:px-6 py-4">
      <KinEmptyState
        :icon="Cog6ToothIcon"
        :title="isParent ? 'Set Up Assistant' : 'Assistant Not Available'"
        :description="isParent
          ? 'The assistant needs an API key to work. Add your Anthropic API key in Settings to activate this feature.'
          : `This feature hasn't been set up yet. Ask a parent to configure it in Settings.`"
        accent-color="sun"
        size="md"
      >
        <template v-if="isParent" #cta>
          <KinButton variant="primary" size="md" to="/settings">
            Go to Settings
            <template #trailing>
              <ArrowRightIcon class="w-4 h-4" />
            </template>
          </KinButton>
        </template>
      </KinEmptyState>
    </div>

    <!-- Messages Container -->
    <div v-else-if="!checkingKey" ref="messagesContainer" class="flex-1 overflow-y-auto px-4 md:px-6 py-4">
      <!-- Welcome / Empty State -->
      <div v-if="messages.length === 0 && !loading" class="flex items-center justify-center h-full">
        <div class="text-center max-w-sm w-full">
          <KinEmptyState
            :icon="CpuChipIcon"
            title="Kinhold Assistant"
            description="Tell me what you need and I'll take care of it — tasks, points, calendar, and more."
            accent-color="lavender"
            size="md"
          />

          <!-- Suggested Actions -->
          <div class="space-y-2 mt-2">
            <button
              v-for="(q, idx) in suggestedQuestions"
              :key="idx"
              class="w-full text-left px-4 py-3 bg-surface-raised border border-border-subtle hover:border-accent-lavender-bold/50 hover:bg-surface-sunken rounded-xl transition-all text-sm text-ink-primary"
              @click="quickSend(q)"
            >
              {{ q }}
            </button>
          </div>

          <!-- Disclaimer -->
          <p class="text-[11px] text-ink-tertiary mt-6">
            The assistant can make mistakes. Always verify important information.
          </p>
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
          <!-- Assistant Avatar -->
          <div
            v-if="message.sender !== 'user'"
            class="w-8 h-8 rounded-full bg-accent-lavender-bold/15 flex items-center justify-center flex-shrink-0 mt-1"
          >
            <CpuChipIcon class="w-4 h-4 text-accent-lavender-bold" />
          </div>

          <!-- Bubble -->
          <div
            class="max-w-[75%] rounded-2xl px-4 py-3"
            :class="message.sender === 'user'
              ? 'bg-accent-lavender-bold text-white rounded-br-md'
              : 'bg-surface-sunken text-ink-primary rounded-bl-md'"
          >
            <!-- User messages: plain text -->
            <p v-if="message.sender === 'user'" class="text-sm whitespace-pre-wrap leading-relaxed">{{ message.text }}</p>
            <!-- Assistant messages: rendered markdown -->
            <div
              v-else
              class="text-sm leading-relaxed prose-assistant"
              v-html="renderMarkdown(message.text)"
            ></div>
            <p
              class="text-[10px] mt-1.5"
              :class="message.sender === 'user' ? 'text-white/70' : 'text-ink-tertiary'"
            >
              {{ formatTime(message.created_at) }}
            </p>
          </div>

          <!-- User Avatar -->
          <div
            v-if="message.sender === 'user'"
            class="w-8 h-8 rounded-full bg-accent-lavender-bold flex items-center justify-center flex-shrink-0 mt-1"
          >
            <span class="text-white font-semibold text-xs">
              {{ currentUser?.name?.charAt(0)?.toUpperCase() || '?' }}
            </span>
          </div>
        </div>

        <!-- Typing indicator -->
        <div v-if="loading" class="flex gap-3">
          <div class="w-8 h-8 rounded-full bg-accent-lavender-bold/15 flex items-center justify-center flex-shrink-0 mt-1">
            <CpuChipIcon class="w-4 h-4 text-accent-lavender-bold" />
          </div>
          <div class="bg-surface-sunken rounded-2xl rounded-bl-md px-4 py-3">
            <div class="flex gap-1">
              <span class="w-2 h-2 bg-ink-tertiary rounded-full animate-bounce" style="animation-delay: 0ms"></span>
              <span class="w-2 h-2 bg-ink-tertiary rounded-full animate-bounce" style="animation-delay: 150ms"></span>
              <span class="w-2 h-2 bg-ink-tertiary rounded-full animate-bounce" style="animation-delay: 300ms"></span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Input Area -->
    <div v-if="!checkingKey && hasApiKey" class="border-t border-border-subtle bg-surface-raised px-4 md:px-6 py-3 pb-safe-bottom">
      <form class="max-w-2xl mx-auto flex gap-2 items-end" @submit.prevent="handleSend">
        <textarea
          ref="chatInput"
          v-model="messageInput"
          rows="1"
          placeholder="Tell Kinhold what to do..."
          class="flex-1 px-4 py-2.5 bg-surface-sunken border border-border-subtle rounded-xl text-sm placeholder-ink-tertiary text-ink-primary focus:bg-surface-raised focus:ring-2 focus:ring-accent-lavender-bold/40 transition-all outline-none resize-none max-h-32 overflow-y-auto"
          :disabled="loading"
          @keydown.enter.exact.prevent="handleSend"
          @input="autoResize"
        ></textarea>
        <KinButton
          variant="primary"
          size="md"
          type="submit"
          icon-only
          aria-label="Send message"
          :disabled="!messageInput.trim() || loading"
        >
          <PaperAirplaneIcon class="w-5 h-5" />
        </KinButton>
      </form>
      <p class="max-w-2xl mx-auto text-[10px] text-ink-tertiary mt-1.5 text-center">
        The assistant can make mistakes. Always verify important information.
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick, watch } from 'vue'
import { storeToRefs } from 'pinia'
import { marked } from 'marked'
import DOMPurify from 'dompurify'
import { useChatStore } from '@/stores/chat'
import { useAuthStore } from '@/stores/auth'
import api from '@/services/api'
import {
  CpuChipIcon,
  PaperAirplaneIcon,
  Cog6ToothIcon,
  ArrowRightIcon,
} from '@heroicons/vue/24/outline'
import KinButton from '@/components/design-system/KinButton.vue'
import KinEmptyState from '@/components/design-system/KinEmptyState.vue'

// Configure marked for safe, compact output
marked.setOptions({
  breaks: true,
  gfm: true,
})

const chatStore = useChatStore()
const authStore = useAuthStore()

const { messages, loading } = storeToRefs(chatStore)
const { currentUser } = storeToRefs(authStore)
const isParent = computed(() => authStore.isParent)

const messageInput = ref('')
const messagesContainer = ref(null)
const chatInput = ref(null)
const hasApiKey = ref(true) // assume true until checked to avoid flash
const checkingKey = ref(true)

const suggestedQuestions = [
  "What tasks are due this week?",
  "Create a task to buy groceries by Friday",
  "Check the leaderboard",
  "Give kudos to someone for helping out",
]

const renderMarkdown = (text) => {
  if (!text) return ''
  return DOMPurify.sanitize(marked.parse(text))
}

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

const autoResize = () => {
  if (!chatInput.value) return
  chatInput.value.style.height = 'auto'
  chatInput.value.style.height = chatInput.value.scrollHeight + 'px'
}

const handleSend = async () => {
  if (!messageInput.value.trim()) return
  const text = messageInput.value
  messageInput.value = ''
  if (chatInput.value) chatInput.value.style.height = 'auto'
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

<style scoped>
/* Markdown prose styles for assistant messages */
.prose-assistant :deep(h1),
.prose-assistant :deep(h2),
.prose-assistant :deep(h3) {
  font-weight: 700;
  margin-top: 0.75em;
  margin-bottom: 0.25em;
  line-height: 1.3;
}
.prose-assistant :deep(h1) { font-size: 1.1em; }
.prose-assistant :deep(h2) { font-size: 1.05em; }
.prose-assistant :deep(h3) { font-size: 1em; }
.prose-assistant :deep(p) { margin-bottom: 0.5em; }
.prose-assistant :deep(p:last-child) { margin-bottom: 0; }
.prose-assistant :deep(ul),
.prose-assistant :deep(ol) {
  padding-left: 1.25em;
  margin-bottom: 0.5em;
}
.prose-assistant :deep(li) { margin-bottom: 0.15em; }
.prose-assistant :deep(strong) { font-weight: 600; }
.prose-assistant :deep(hr) {
  border: none;
  border-top: 1px solid currentColor;
  opacity: 0.2;
  margin: 0.75em 0;
}
.prose-assistant :deep(code) {
  font-size: 0.9em;
  padding: 0.1em 0.3em;
  border-radius: 0.25em;
  background: rgba(0, 0, 0, 0.06);
}
</style>
