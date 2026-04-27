<template>
  <div class="flex-1 flex flex-col">
    <div class="text-center mb-6">
      <h1 class="text-2xl font-heading font-bold text-ink-primary mb-2">
        Here's What You Can Do
      </h1>
      <p class="text-base text-ink-secondary">
        Your family has these features set up for you.
      </p>
    </div>

    <div class="space-y-3 overflow-y-auto">
      <KinGradientCard
        v-for="feature in accessibleFeatures"
        :key="feature.key"
        :variant="featureVariant(feature.key)"
        padding="sm"
      >
        <div class="flex items-start gap-3">
          <div class="kin-icon-box flex-shrink-0 mt-0.5">
            <component :is="feature.icon" class="w-5 h-5" />
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-ink-primary">{{ feature.name }}</p>
            <p class="text-xs text-ink-secondary mt-1 leading-relaxed">{{ feature.explainer }}</p>
          </div>
        </div>
      </KinGradientCard>

      <!-- Locked features -->
      <KinFlatCard
        v-for="feature in lockedFeatures"
        :key="feature.key"
        padding="sm"
        class="opacity-50"
      >
        <div class="flex items-start gap-3">
          <div class="w-10 h-10 rounded-lg bg-surface-sunken flex items-center justify-center flex-shrink-0 mt-0.5">
            <LockClosedIcon class="w-5 h-5 text-ink-tertiary" />
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-ink-tertiary">{{ feature.name }}</p>
            <p class="text-xs text-ink-tertiary mt-1">Managed by your parents.</p>
          </div>
        </div>
      </KinFlatCard>
    </div>

    <p v-if="accessibleFeatures.length === 0" class="text-sm text-ink-secondary text-center mt-4">
      Your parents haven't set up features yet. They can do this from Settings.
    </p>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useAuthStore } from '@/stores/auth'
import {
  CalendarDaysIcon,
  ClipboardDocumentListIcon,
  TrophyIcon,
  StarIcon,
  ChatBubbleLeftRightIcon,
  LockClosedIcon,
} from '@heroicons/vue/24/outline'
import KinFlatCard from '@/components/design-system/KinFlatCard.vue'
import KinGradientCard from '@/components/design-system/KinGradientCard.vue'

const authStore = useAuthStore()

const variantMap = {
  calendar: 'sun',
  tasks: 'mint',
  points: 'warm',
  badges: 'sun',
  chat: 'cool',
  vault: 'lavender',
}
function featureVariant(key) {
  return variantMap[key] || 'iridescent'
}

const allFeatures = [
  {
    key: 'calendar',
    name: 'Calendar',
    icon: CalendarDaysIcon,
    explainer: 'See your family\'s schedule all in one place. Everyone\'s events show up color-coded so you know who has what going on.',
  },
  {
    key: 'tasks',
    name: 'Tasks',
    icon: ClipboardDocumentListIcon,
    explainer: 'Check what needs to be done, mark tasks complete, and see what\'s assigned to you. Tasks are organized with tags — tap a tag to filter.',
  },
  {
    key: 'points',
    name: 'Points & Rewards',
    icon: TrophyIcon,
    explainer: 'Earn points when you complete tasks. Your points go into a bank you can spend at the rewards store. Check the leaderboard to see where you stand.',
  },
  {
    key: 'badges',
    name: 'Badges',
    icon: StarIcon,
    explainer: 'Unlock badges by hitting milestones — complete tasks, build streaks, earn points. Some badges are hidden until you discover them.',
  },
  {
    key: 'chat',
    name: 'AI Chat',
    icon: ChatBubbleLeftRightIcon,
    explainer: 'Ask the AI about your family\'s schedule, tasks, or stored info. Try things like "What\'s happening this weekend?" or "What tasks are due today?"',
  },
  {
    key: 'vault',
    name: 'Family Vault',
    icon: LockClosedIcon,
    explainer: 'Access important family info that\'s been shared with you — things like wifi passwords, account details, or emergency contacts.',
  },
]

const accessibleFeatures = computed(() => {
  return allFeatures.filter(f => authStore.userCanAccessModule(f.key))
})

const lockedFeatures = computed(() => {
  return allFeatures.filter(f => !authStore.userCanAccessModule(f.key))
})
</script>
