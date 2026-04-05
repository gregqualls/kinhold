<template>
  <div class="flex items-center gap-4 h-full">
    <div class="w-12 h-12 rounded-xl bg-wisteria-100 dark:bg-wisteria-900/30 flex items-center justify-center flex-shrink-0">
      <TrophyIcon class="w-6 h-6 text-wisteria-600 dark:text-wisteria-400" />
    </div>
    <div class="min-w-0">
      <p class="text-sm text-lavender-500 dark:text-lavender-400">{{ config.title || 'Points Balance' }}</p>
      <div v-if="loading" class="h-8 w-16 bg-lavender-200 dark:bg-prussian-700 rounded animate-pulse mt-1"></div>
      <p v-else class="text-2xl font-bold font-mono text-prussian-500 dark:text-lavender-200">
        {{ balance.toLocaleString() }}<span class="text-sm font-normal ml-1 text-lavender-500 dark:text-lavender-400">pts</span>
      </p>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useWidgetData } from '@/composables/useWidgetData'
import { TrophyIcon } from '@heroicons/vue/24/solid'

defineProps({
  config: { type: Object, required: true },
})

const { data, loading } = useWidgetData('/api/v1/points/bank', {})

const balance = computed(() => {
  if (!data.value) return 0
  return data.value.bank ?? data.value.balance ?? 0
})
</script>
