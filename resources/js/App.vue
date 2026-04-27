<template>
  <div class="h-screen flex flex-col md:flex-row">
    <!-- Sidebar (Desktop) -->
    <Sidebar v-if="!isAuthPage" class="hidden md:flex" />

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
      <!-- Top Bar (Desktop) -->
      <TopBar v-if="!isAuthPage" class="hidden md:flex" />

      <!-- Mobile Header -->
      <div v-if="!isAuthPage" class="md:hidden bg-white dark:bg-prussian-800 px-4 py-2.5 flex items-center justify-between border-b border-lavender-200 dark:border-prussian-700">
        <div class="flex items-center gap-2">
          <img src="/images/logo-100.png" alt="Kinhold" class="w-7 h-7 rounded-lg" />
          <p class="text-sm font-semibold text-prussian-500 dark:text-lavender-200">{{ familyName }}</p>
        </div>
        <div class="flex items-center gap-2">
          <RouterLink
            to="/settings"
            class="p-1.5 rounded-lg text-lavender-500 dark:text-lavender-400 hover:bg-lavender-100 dark:hover:bg-prussian-700 transition-colors"
            aria-label="Settings"
          >
            <Cog6ToothIcon class="w-5 h-5" />
          </RouterLink>
          <RouterLink to="/settings" class="flex items-center">
            <UserAvatar :user="currentUser" size="sm" class="ring-2 ring-lavender-300 dark:ring-prussian-600" />
          </RouterLink>
        </div>
      </div>

      <!-- Email Verification Banner -->
      <div v-if="showVerificationBanner" class="bg-amber-50 dark:bg-amber-900/20 border-b border-amber-200 dark:border-amber-800 px-4 py-2 flex items-center justify-between gap-2">
        <p class="text-sm text-amber-800 dark:text-amber-300">
          Please verify your email address. Check your inbox for a verification link.
        </p>
        <div class="flex items-center gap-2 flex-shrink-0">
          <button :disabled="resendingVerification" class="text-xs font-medium text-amber-700 dark:text-amber-400 hover:underline disabled:opacity-50" @click="handleResendVerification">
            {{ resendingVerification ? 'Sending...' : 'Resend' }}
          </button>
          <button class="text-amber-500 hover:text-amber-700 dark:hover:text-amber-300" @click="dismissVerificationBanner">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
          </button>
        </div>
      </div>

      <!-- Main Area -->
      <main
        class="flex-1 overflow-y-auto min-h-0"
      >
        <RouterView v-slot="{ Component, route: viewRoute }">
          <Transition name="page-fade" mode="out-in">
            <component :is="Component" :key="viewRoute.path" />
          </Transition>
        </RouterView>
      </main>

      <!-- Bottom Navigation (Mobile) -->
      <BottomNav v-if="!isAuthPage" class="md:hidden" />
    </div>

    <!-- Toast Notifications -->
    <TransitionGroup
      name="toast-notify"
      tag="div"
      class="fixed top-4 right-4 z-[90] flex flex-col gap-2 pointer-events-none"
    >
      <div
        v-for="notification in notifications"
        :key="notification.id"
        class="pointer-events-auto px-4 py-3 rounded-xl shadow-lg max-w-sm"
        :class="{
          'bg-emerald-600 text-white': notification.type === 'success',
          'bg-red-600 text-white': notification.type === 'error',
          'bg-wisteria-600 text-white': notification.type === 'info',
          'bg-amber-500 text-white': notification.type === 'warning',
        }"
      >
        <p class="text-sm font-medium">{{ notification.message }}</p>
      </div>
    </TransitionGroup>

    <!-- Loading Overlay -->
    <Transition name="fade">
      <div
        v-if="isLoading"
        class="fixed inset-0 bg-black/30 flex items-center justify-center z-40"
      >
        <LoadingSpinner size="lg" color="white" />
      </div>
    </Transition>

    <!-- Easter Eggs -->
    <EasterEggs v-if="!isAuthPage" ref="easterEggsRef" />
  </div>
</template>

<script setup>
import { computed, ref, provide } from 'vue'
import { useRoute } from 'vue-router'
import { storeToRefs } from 'pinia'
import { useAuthStore } from '@/stores/auth'
import Sidebar from '@/components/layout/Sidebar.vue'
import TopBar from '@/components/layout/TopBar.vue'
import BottomNav from '@/components/layout/BottomNav.vue'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import UserAvatar from '@/components/common/UserAvatar.vue'
import EasterEggs from '@/components/common/EasterEggs.vue'
import { Cog6ToothIcon } from '@heroicons/vue/24/outline'
import { useNotification } from '@/composables/useNotification'
import { useDarkMode } from '@/composables/useDarkMode'
import { useTheme } from '@/composables/useTheme'

const route = useRoute()
const authStore = useAuthStore()
const { notifications } = useNotification()
const { isLoading, currentUser, family } = storeToRefs(authStore)
const familyName = computed(() => family.value?.name || 'Kinhold')
const { init: initDarkMode } = useDarkMode()
const { init: initTheme } = useTheme()
initDarkMode()
initTheme()

const easterEggsRef = ref(null)
provide('easterEggs', easterEggsRef)

// `isAuthPage` doubles as "chromeless" — no sidebar/topbar/bottomnav wraps these routes.
// The design system is chromeless by design: it owns its full viewport with its own sidebar.
const isAuthPage = computed(() => {
  return ['Login', 'Register', 'Privacy', 'Terms', 'Onboarding', 'DesignSystem'].includes(route.name)
})

// Email verification banner
const verificationDismissed = ref(false)
const resendingVerification = ref(false)
const showVerificationBanner = computed(() => {
  if (isAuthPage.value || verificationDismissed.value) return false
  if (authStore.appConfig?.self_hosted) return false
  const u = currentUser.value
  return u && u.email && !u.email_verified_at
})

const dismissVerificationBanner = () => {
  verificationDismissed.value = true
}

const handleResendVerification = async () => {
  resendingVerification.value = true
  await authStore.resendVerification()
  resendingVerification.value = false
}
</script>

<style>
.page-fade-enter-active {
  transition: opacity 0.15s ease;
}
.page-fade-leave-active {
  transition: opacity 0.1s ease;
}
.page-fade-enter-from,
.page-fade-leave-to {
  opacity: 0;
}

.toast-notify-enter-active {
  transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
.toast-notify-leave-active {
  transition: all 0.2s ease-in;
}
.toast-notify-enter-from {
  opacity: 0;
  transform: translateX(20px);
}
.toast-notify-leave-to {
  opacity: 0;
  transform: translateX(20px);
}
</style>
