<template>
  <div
    v-if="shouldShow"
    class="bg-[#EBF4F8] dark:bg-[#1E3A4D]/40 border-b border-[#1B3A4B]/30 dark:border-[#5A8FA8]/30 px-4 py-2 flex items-start sm:items-center justify-between gap-3"
    role="region"
    aria-label="Enable notifications"
  >
    <div class="flex items-start sm:items-center gap-2 text-sm text-[#1B3A4B] dark:text-[#9FC8D9]">
      <svg class="w-5 h-5 flex-shrink-0 mt-0.5 sm:mt-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
      </svg>
      <p>
        <strong>Turn on notifications</strong> so Kinhold can ping you about
        new tasks and kudos — even when the app isn't open.
      </p>
    </div>
    <div class="flex items-center gap-1.5 flex-shrink-0">
      <button
        type="button"
        :disabled="enabling"
        class="px-2.5 py-1 text-xs font-semibold rounded-md bg-[#1B3A4B] text-white hover:bg-[#15303D] transition-colors disabled:opacity-60"
        @click="enable"
      >
        {{ enabling ? 'Enabling…' : 'Turn on' }}
      </button>
      <button
        type="button"
        class="px-2 py-1 text-xs text-[#1B3A4B]/70 dark:text-[#9FC8D9]/70 hover:text-[#1B3A4B] dark:hover:text-[#9FC8D9] transition-colors"
        aria-label="Dismiss notification prompt"
        @click="dismiss"
      >
        ✕
      </button>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useNotificationsStore } from '@/stores/notifications'
import { isPushSupported, pushPermission, vapidPublicKey } from '@/services/push'

const STORAGE_KEY = 'kinhold_push_prompt_dismissed'
const DISMISS_TTL_MS = 30 * 24 * 60 * 60 * 1000

const auth = useAuthStore()
const notifications = useNotificationsStore()

const dismissed = ref(false)
const enabling = ref(false)
const supported = ref(false)
const vapidConfigured = ref(false)
const permission = ref(typeof Notification !== 'undefined' ? Notification.permission : 'unsupported')

function safeLocalStorage() {
  try {
    return typeof window !== 'undefined' ? window.localStorage : null
  } catch {
    return null
  }
}

function recentlyDismissed() {
  const ls = safeLocalStorage()
  if (!ls) return false
  const raw = ls.getItem(STORAGE_KEY)
  if (!raw) return false
  const stamp = Number(raw)
  if (!Number.isFinite(stamp)) return false
  return Date.now() - stamp < DISMISS_TTL_MS
}

function persistDismissal() {
  const ls = safeLocalStorage()
  if (!ls) return
  try {
    ls.setItem(STORAGE_KEY, String(Date.now()))
  } catch {
    /* quota or privacy mode — fine */
  }
}

const shouldShow = computed(() => {
  if (!auth.isAuthenticated) return false
  if (!supported.value || !vapidConfigured.value) return false
  if (dismissed.value) return false
  if (permission.value !== 'default') return false
  return true
})

async function enable() {
  enabling.value = true
  try {
    await notifications.enablePush()
    permission.value = pushPermission()
    if (permission.value !== 'granted') {
      // user denied — persist dismissal so we don't keep nagging
      persistDismissal()
      dismissed.value = true
    }
  } catch {
    // store has the error message; just persist the dismissal
    persistDismissal()
    dismissed.value = true
  } finally {
    enabling.value = false
  }
}

function dismiss() {
  dismissed.value = true
  persistDismissal()
}

onMounted(() => {
  supported.value = isPushSupported()
  vapidConfigured.value = !!vapidPublicKey()
  permission.value = pushPermission()
  dismissed.value = recentlyDismissed()
})
</script>
