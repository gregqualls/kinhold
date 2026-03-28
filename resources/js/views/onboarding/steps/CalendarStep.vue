<template>
  <div class="flex-1 flex flex-col">
    <div class="text-center mb-8">
      <h1 class="text-2xl font-heading font-bold text-kin-black dark:text-kin-off-white mb-2">
        Connect Your Calendar
      </h1>
      <p class="text-base text-kin-gray-500 dark:text-kin-gray-400">
        See everyone's events in one place. Each family member can connect their own calendar.
      </p>
    </div>

    <!-- Already connected -->
    <div v-if="calendarConnected" class="kin-card text-center space-y-3">
      <div class="w-12 h-12 mx-auto rounded-full bg-kin-success/10 flex items-center justify-center">
        <CheckCircleIcon class="w-7 h-7 text-kin-success" />
      </div>
      <p class="text-base font-medium text-kin-black dark:text-kin-off-white">Calendar connected</p>
      <p class="text-sm text-kin-gray-500 dark:text-kin-gray-400">
        Your Google Calendar is synced. Other family members can connect theirs from Settings.
      </p>
    </div>

    <!-- Connect button -->
    <div v-else class="space-y-4">
      <button
        class="w-full kin-card flex items-center gap-4 cursor-pointer hover:border-kin-gold transition-all duration-200"
        :disabled="connecting"
        @click="connectGoogle"
      >
        <div class="w-10 h-10 rounded-lg bg-kin-cream dark:bg-kin-surface-dark-alt flex items-center justify-center flex-shrink-0">
          <svg class="w-5 h-5" viewBox="0 0 24 24">
            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/>
            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
          </svg>
        </div>
        <div class="text-left flex-1">
          <p class="text-base font-medium text-kin-black dark:text-kin-off-white">
            {{ connecting ? 'Connecting...' : 'Connect Google Calendar' }}
          </p>
          <p class="text-sm text-kin-gray-500 dark:text-kin-gray-400">
            Sync your events automatically
          </p>
        </div>
        <ChevronRightIcon class="w-5 h-5 text-kin-gray-400 flex-shrink-0" />
      </button>

      <p v-if="error" class="text-sm text-kin-error text-center">{{ error }}</p>

      <p class="text-xs text-center text-kin-gray-500 dark:text-kin-gray-400">
        We only read your events. You can disconnect anytime from Settings.
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, inject, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useOnboardingStore } from '@/stores/onboarding'
import api from '@/services/api'
import { CheckCircleIcon, ChevronRightIcon } from '@heroicons/vue/24/outline'

const store = useOnboardingStore()
const route = useRoute()
inject('onboarding')

const connecting = ref(false)
const error = ref('')
const calendarConnected = ref(false)

async function connectGoogle() {
  connecting.value = true
  error.value = ''
  try {
    const response = await api.post('/calendar/connect', { origin: 'onboarding' })
    if (response.data.auth_url) {
      window.location.href = response.data.auth_url
    }
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to start calendar connection.'
    connecting.value = false
  }
}

onMounted(() => {
  // Check if returning from OAuth
  if (route.query.calendar_connected) {
    calendarConnected.value = true
  } else if (route.query.calendar_error) {
    error.value = route.query.calendar_error
  } else if (store.status?.calendar_connected) {
    calendarConnected.value = true
  }
})
</script>
