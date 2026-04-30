<template>
  <div
    v-if="shouldShow"
    class="bg-[#FAF5EE] dark:bg-[#3A2E1F]/40 border-b border-[#C4975A]/40 dark:border-[#C4975A]/30 px-4 py-2 flex items-start sm:items-center justify-between gap-3"
    role="region"
    aria-label="Install Kinhold"
  >
    <div class="flex items-start sm:items-center gap-2 text-sm text-[#5A4423] dark:text-[#E0C58A]">
      <svg class="w-5 h-5 flex-shrink-0 mt-0.5 sm:mt-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v12m0 0l-4-4m4 4l4-4M4 20h16" />
      </svg>
      <p>
        <template v-if="mode === 'android'">
          <strong>Install Kinhold</strong> for a faster, full-screen experience.
        </template>
        <template v-else>
          <strong>Add Kinhold to your home screen.</strong>
          Tap the
          <svg class="inline w-4 h-4 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
          </svg>
          share icon, then "Add to Home Screen".
        </template>
      </p>
    </div>
    <div class="flex items-center gap-1.5 flex-shrink-0">
      <button
        v-if="mode === 'android'"
        class="px-2.5 py-1 text-xs font-semibold rounded-md bg-[#C4975A] text-white hover:bg-[#B38A50] transition-colors"
        @click="install"
      >
        Install
      </button>
      <button
        type="button"
        class="px-2 py-1 text-xs text-[#5A4423]/70 dark:text-[#E0C58A]/70 hover:text-[#5A4423] dark:hover:text-[#E0C58A] transition-colors"
        aria-label="Dismiss install prompt"
        @click="dismiss"
      >
        ✕
      </button>
    </div>
  </div>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue'

const STORAGE_KEY = 'kinhold_install_prompt_dismissed'
// 30-day cool-down after dismiss; user can re-trigger by waiting it out.
const DISMISS_TTL_MS = 30 * 24 * 60 * 60 * 1000

const deferredPrompt = ref(null)
const isAndroidEligible = ref(false)
const isIosEligible = ref(false)

function safeLocalStorage() {
  try {
    return typeof window !== 'undefined' ? window.localStorage : null
  } catch {
    return null
  }
}

function isStandalone() {
  if (typeof window === 'undefined') return false
  if (window.matchMedia?.('(display-mode: standalone)').matches) return true
  // iOS Safari pre-PWA flag
  return window.navigator?.standalone === true
}

function isIosSafari() {
  if (typeof window === 'undefined') return false
  const ua = window.navigator?.userAgent || ''
  const isIOS = /iPad|iPhone|iPod/.test(ua) && !window.MSStream
  // Exclude Chrome/Firefox/Edge on iOS — those wrap WebKit but don't support add-to-home in the same way
  const isSafari = /Safari/.test(ua) && !/CriOS|FxiOS|EdgiOS/.test(ua)
  return isIOS && isSafari
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
    /* quota or privacy mode — fine, banner will reappear next session */
  }
}

const mode = computed(() => {
  if (isAndroidEligible.value) return 'android'
  if (isIosEligible.value) return 'ios'
  return null
})

const shouldShow = computed(() => mode.value !== null)

function onBeforeInstallPrompt(event) {
  event.preventDefault()
  if (isStandalone() || recentlyDismissed()) return
  deferredPrompt.value = event
  isAndroidEligible.value = true
}

function onAppInstalled() {
  // Hide immediately; persist so we don't pester
  isAndroidEligible.value = false
  isIosEligible.value = false
  persistDismissal()
}

async function install() {
  const event = deferredPrompt.value
  if (!event) return
  try {
    event.prompt()
    await event.userChoice
  } catch {
    /* user cancelled — fall through to dismissal */
  }
  deferredPrompt.value = null
  isAndroidEligible.value = false
  persistDismissal()
}

function dismiss() {
  isAndroidEligible.value = false
  isIosEligible.value = false
  persistDismissal()
}

onMounted(() => {
  if (typeof window === 'undefined') return
  if (isStandalone() || recentlyDismissed()) return

  window.addEventListener('beforeinstallprompt', onBeforeInstallPrompt)
  window.addEventListener('appinstalled', onAppInstalled)

  if (isIosSafari()) {
    isIosEligible.value = true
  }
})

onBeforeUnmount(() => {
  if (typeof window === 'undefined') return
  window.removeEventListener('beforeinstallprompt', onBeforeInstallPrompt)
  window.removeEventListener('appinstalled', onAppInstalled)
})
</script>
