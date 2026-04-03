<template>
  <div>
    <div v-if="loading" class="space-y-3">
      <div v-for="n in 3" :key="n" class="h-12 bg-lavender-100 dark:bg-prussian-700 rounded-lg animate-pulse"></div>
    </div>

    <div v-else-if="items.length === 0" class="text-center py-4">
      <p class="text-sm text-lavender-500 dark:text-lavender-400">No recent activity.</p>
    </div>

    <div v-else class="space-y-2 max-h-64 overflow-y-auto">
      <div
        v-for="item in items"
        :key="item.id"
        class="flex items-start gap-3 p-2 rounded-lg"
      >
        <div class="flex-shrink-0 mt-0.5">
          <div
            class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold"
            :class="pointsBadgeClass(item.points)"
          >
            {{ item.points > 0 ? '+' : '' }}{{ item.points }}
          </div>
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-sm text-prussian-600 dark:text-lavender-200">
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

const props = defineProps({
  config: { type: Object, required: true },
})

const { data, loading } = useWidgetData(props.config.endpoint, props.config.params)

const items = computed(() => {
  if (!data.value) return []
  const list = data.value.data || data.value.feed || data.value
  if (!Array.isArray(list)) return []
  const limit = props.config.settings?.limit || 10
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
