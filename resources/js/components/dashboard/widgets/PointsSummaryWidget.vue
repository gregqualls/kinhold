<template>
  <div class="flex flex-col h-full">
    <!-- Balance section -->
    <div class="flex items-center gap-4 pb-3 border-b border-lavender-200 dark:border-prussian-700">
      <div class="w-12 h-12 rounded-xl bg-wisteria-100 dark:bg-wisteria-900/30 flex items-center justify-center flex-shrink-0">
        <TrophyIcon class="w-6 h-6 text-wisteria-600 dark:text-wisteria-400" />
      </div>
      <div class="min-w-0">
        <p class="text-xs text-lavender-500 dark:text-lavender-400">{{ config.title || 'Points Balance' }}</p>
        <div v-if="bankLoading" class="h-8 w-16 bg-lavender-200 dark:bg-prussian-700 rounded animate-pulse mt-1"></div>
        <p v-else class="text-2xl font-bold font-mono text-prussian-500 dark:text-lavender-200">
          {{ balance.toLocaleString() }}<span class="text-sm font-normal ml-1 text-lavender-500 dark:text-lavender-400">pts</span>
        </p>
      </div>
    </div>

    <!-- Recent activity -->
    <div class="flex-1 min-h-0 pt-2">
      <p class="text-[10px] uppercase tracking-wider font-semibold text-lavender-400 dark:text-lavender-500 mb-1.5">Recent</p>
      <div v-if="feedLoading" class="space-y-2">
        <div v-for="n in 3" :key="n" class="h-6 bg-lavender-100 dark:bg-prussian-700 rounded animate-pulse"></div>
      </div>
      <div v-else class="space-y-1.5">
        <div
          v-for="item in recentActivity"
          :key="item.id"
          class="flex items-center justify-between gap-2"
        >
          <p class="text-xs text-prussian-500 dark:text-lavender-300 truncate flex-1">
            {{ item.description }}
          </p>
          <span
            class="text-[10px] font-bold font-mono flex-shrink-0"
            :class="item.points > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-500 dark:text-red-400'"
          >
            {{ item.points > 0 ? '+' : '' }}{{ item.points }}
          </span>
        </div>
      </div>
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

const { data: bankData, loading: bankLoading } = useWidgetData('/api/v1/points/bank', {})
const { data: feedData, loading: feedLoading } = useWidgetData('/api/v1/points/feed', {})

const balance = computed(() => {
  if (!bankData.value) return 0
  return bankData.value.bank ?? bankData.value.balance ?? 0
})

const recentActivity = computed(() => {
  if (!feedData.value) return []
  const list = feedData.value.data || feedData.value.feed || feedData.value
  if (!Array.isArray(list)) return []
  return list.slice(0, 4)
})
</script>
