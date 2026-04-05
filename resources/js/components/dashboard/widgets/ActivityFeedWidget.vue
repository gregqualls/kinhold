<template>
  <div>
    <div class="flex items-center justify-between mb-3 flex-shrink-0">
      <h3 class="text-sm font-semibold text-prussian-500 dark:text-lavender-200 flex items-center gap-2">
        <BellIcon class="w-4 h-4 text-wisteria-500" />
        {{ config.title || 'Activity' }}
      </h3>
      <RouterLink
        to="/points"
        class="text-wisteria-600 dark:text-wisteria-400 text-xs font-medium hover:text-wisteria-500"
      >
        View All
      </RouterLink>
    </div>

    <div v-if="loading" class="space-y-3">
      <div v-for="n in 4" :key="n" class="h-10 bg-lavender-100 dark:bg-prussian-700 rounded-lg animate-pulse"></div>
    </div>

    <div v-else-if="items.length === 0" class="flex flex-col items-center justify-center py-6">
      <BellIcon class="w-8 h-8 text-lavender-400 dark:text-lavender-500 mb-1" />
      <p class="text-sm text-lavender-500 dark:text-lavender-400">No recent activity</p>
    </div>

    <div v-else class="space-y-2">
      <div
        v-for="item in items"
        :key="item.id"
        class="flex items-start gap-3 p-1.5 rounded-lg"
      >
        <div
          class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0"
          :class="pointsBadgeClass(item.points)"
        >
          {{ item.points > 0 ? '+' : '' }}{{ item.points }}
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-sm text-prussian-600 dark:text-lavender-200 leading-snug">
            {{ item.description }}
          </p>
          <p class="text-xs text-lavender-500 dark:text-lavender-400 mt-0.5">
            {{ item.user?.name?.split(' ')[0] }}
            <span v-if="item.created_at" class="ml-1">{{ formatTime(item.created_at) }}</span>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { DateTime } from 'luxon'
import { useWidgetData } from '@/composables/useWidgetData'
import { BellIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  config: { type: Object, required: true },
})

const { data, loading } = useWidgetData('/api/v1/points/feed', {})

const items = computed(() => {
  if (!data.value) return []
  const list = data.value.data || data.value.feed || data.value
  if (!Array.isArray(list)) return []
  const limit = props.config.size === 'md' ? 12 : 6
  return list.slice(0, limit)
})

function pointsBadgeClass(points) {
  if (points > 0) return 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400'
  if (points < 0) return 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400'
  return 'bg-lavender-100 dark:bg-prussian-700 text-lavender-600 dark:text-lavender-400'
}

function formatTime(dateStr) {
  if (!dateStr) return ''
  return DateTime.fromISO(dateStr).toRelative() || ''
}
</script>
