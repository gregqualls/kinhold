<template>
  <div class="h-screen flex flex-col bg-lavender-50 md:flex-row dark:bg-prussian-900">
    <!-- Sidebar (Desktop) -->
    <Sidebar v-if="!isAuthPage" class="hidden md:flex" />

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
      <!-- Top Bar (Desktop) -->
      <TopBar v-if="!isAuthPage" class="hidden md:flex" />

      <!-- Main Area -->
      <main
        class="flex-1 overflow-y-auto"
        :class="{ 'pb-20 md:pb-0': !isAuthPage }"
      >
        <RouterView v-slot="{ Component, route: viewRoute }">
          <Transition name="page-fade" mode="out-in">
            <component :is="Component" :key="viewRoute.path" />
          </Transition>
        </RouterView>
      </main>
    </div>

    <!-- Bottom Navigation (Mobile) -->
    <BottomNav v-if="!isAuthPage" class="md:hidden" />

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
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import { storeToRefs } from 'pinia'
import { useAuthStore } from '@/stores/auth'
import Sidebar from '@/components/layout/Sidebar.vue'
import TopBar from '@/components/layout/TopBar.vue'
import BottomNav from '@/components/layout/BottomNav.vue'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import { useNotification } from '@/composables/useNotification'
import { useDarkMode } from '@/composables/useDarkMode'

const route = useRoute()
const authStore = useAuthStore()
const { notifications } = useNotification()
const { isLoading } = storeToRefs(authStore)
const { init: initDarkMode } = useDarkMode()
initDarkMode()

const isAuthPage = computed(() => {
  return ['Login', 'Register'].includes(route.name)
})
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
