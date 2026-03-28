<template>
  <div class="flex-1 flex flex-col items-center justify-center text-center">
    <!-- Success Icon -->
    <div class="w-16 h-16 rounded-full bg-kin-success/10 flex items-center justify-center mb-6">
      <CheckIcon class="w-8 h-8 text-kin-success" />
    </div>

    <h1 class="text-2xl font-heading font-bold text-kin-black dark:text-kin-off-white mb-2">
      You're all set
    </h1>
    <p class="text-base text-kin-gray-500 dark:text-kin-gray-400 max-w-xs">
      Your family hub is ready. You can always adjust settings later.
    </p>

    <!-- Summary -->
    <div class="mt-8 w-full space-y-2">
      <div
        v-for="item in summaryItems"
        :key="item.label"
        class="flex items-center gap-3 px-4 py-2.5 rounded-lg bg-kin-cream dark:bg-kin-surface-dark"
      >
        <CheckCircleIcon v-if="item.done" class="w-5 h-5 text-kin-success flex-shrink-0" />
        <MinusCircleIcon v-else class="w-5 h-5 text-kin-gray-400 flex-shrink-0" />
        <span class="text-sm text-kin-black dark:text-kin-off-white">{{ item.label }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useOnboardingStore } from '@/stores/onboarding'
import { CheckIcon, CheckCircleIcon, MinusCircleIcon } from '@heroicons/vue/24/outline'

const store = useOnboardingStore()

const summaryItems = computed(() => [
  {
    label: 'Family created',
    done: true,
  },
  {
    label: 'Calendar connected',
    done: store.status?.calendar_connected || false,
  },
  {
    label: store.selectedPresets.size > 0
      ? `${store.selectedPresets.size} tag${store.selectedPresets.size > 1 ? 's' : ''} created`
      : 'Tags',
    done: store.selectedPresets.size > 0,
  },
])
</script>
