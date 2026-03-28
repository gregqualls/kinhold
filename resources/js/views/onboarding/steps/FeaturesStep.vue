<template>
  <div class="flex-1 flex flex-col">
    <div class="text-center mb-8">
      <h1 class="text-2xl font-heading font-bold text-kin-black dark:text-kin-off-white mb-2">
        Choose Your Features
      </h1>
      <p class="text-base text-kin-gray-500 dark:text-kin-gray-400">
        Enable what your family needs. You can change these anytime in Settings.
      </p>
    </div>

    <div class="space-y-3">
      <div
        v-for="feature in features"
        :key="feature.key"
        class="p-4 rounded-xl border transition-all duration-200"
        :class="feature.enabled
          ? 'border-kin-gold/30 bg-kin-gold/5 dark:bg-kin-gold/10 dark:border-kin-gold/20'
          : 'border-kin-border dark:border-kin-border-dark bg-white dark:bg-kin-surface-dark'"
      >
        <div class="flex items-center gap-4">
          <div class="kin-icon-box flex-shrink-0">
            <component :is="feature.icon" class="w-5 h-5" />
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-kin-black dark:text-kin-off-white">{{ feature.name }}</p>
          </div>
          <button
            class="relative w-11 h-6 rounded-full transition-colors duration-200 flex-shrink-0 focus:outline-none focus:ring-2 focus:ring-kin-gold/30 focus:ring-offset-2 dark:focus:ring-offset-kin-bg-dark"
            :class="feature.enabled ? 'bg-kin-gold' : 'bg-kin-gray-200 dark:bg-kin-gray-700'"
            role="switch"
            :aria-checked="feature.enabled"
            :aria-label="`Toggle ${feature.name}`"
            @click="feature.enabled = !feature.enabled"
          >
            <span
              class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform duration-200"
              :class="feature.enabled ? 'translate-x-5' : 'translate-x-0'"
            />
          </button>
        </div>
        <p class="text-xs text-kin-gray-500 dark:text-kin-gray-400 mt-2 ml-14">{{ feature.description }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, inject, onMounted } from 'vue'
import { useOnboardingStore } from '@/stores/onboarding'
import { useAuthStore } from '@/stores/auth'
import api from '@/services/api'
import {
  TrophyIcon,
  StarIcon,
  ChatBubbleLeftRightIcon,
  LockClosedIcon,
} from '@heroicons/vue/24/outline'

const store = useOnboardingStore()
const authStore = useAuthStore()
const { setStepLoading, registerContinue } = inject('onboarding')

const features = reactive([
  {
    key: 'points',
    name: 'Points & Rewards',
    description: 'Family members earn points when they complete tasks. Points go into a bank that can be spent on rewards you create — like screen time, a treat, or a day off chores. Parents can also give kudos (+1 point with a reason) or deduct points.',
    icon: TrophyIcon,
    enabled: true,
  },
  {
    key: 'badges',
    name: 'Badges',
    description: 'Achievements that unlock automatically — complete 10 tasks, earn 100 points, hit a 7-day streak. Hidden badges show as "???" until earned. Parents can also create custom badges and award them manually.',
    icon: StarIcon,
    enabled: true,
  },
  {
    key: 'chat',
    name: 'AI Chat',
    description: 'An AI assistant that knows your calendar, tasks, and vault. Ask things like "What\'s for dinner Tuesday?" or "What tasks are due this week?" You can add your own API key in Settings.',
    icon: ChatBubbleLeftRightIcon,
    enabled: true,
  },
  {
    key: 'vault',
    name: 'Family Vault',
    description: 'Store sensitive family info — SSNs, insurance policies, medical records, school logins. Everything is encrypted. Parents see all entries; children only see what\'s shared with them.',
    icon: LockClosedIcon,
    enabled: true,
  },
])

registerContinue(async () => {
  // Only parents can toggle modules
  if (!authStore.isParent) return true

  setStepLoading(true)
  try {
    const modules = {}
    features.forEach(f => {
      modules[f.key] = f.enabled
    })
    await api.put('/settings', { modules })
    return true
  } catch (err) {
    console.error('Failed to save feature toggles:', err)
    return false
  } finally {
    setStepLoading(false)
  }
})

onMounted(() => {
  // Auto-skip for non-parents
  if (!authStore.isParent) {
    store.nextStep()
    return
  }

  // Pre-fill from status
  if (store.status?.modules) {
    features.forEach(f => {
      if (store.status.modules[f.key] !== undefined) {
        f.enabled = store.status.modules[f.key]
      }
    })
  }
})
</script>
