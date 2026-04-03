<template>
  <div>
    <div v-if="viewAllPath" class="flex items-center justify-between mb-3">
      <h3 class="text-sm font-semibold text-prussian-500 dark:text-lavender-200 flex items-center gap-2">
        <CalendarIcon class="w-4 h-4 text-wisteria-600" />
        {{ config.title }}
      </h3>
      <RouterLink
        :to="viewAllPath"
        class="text-wisteria-600 dark:text-wisteria-400 text-xs font-medium hover:text-wisteria-500"
      >
        View Calendar
      </RouterLink>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="space-y-2">
      <div v-for="n in 3" :key="n" class="h-10 bg-lavender-100 dark:bg-prussian-700 rounded-lg animate-pulse"></div>
    </div>

    <!-- Empty -->
    <div v-else-if="events.length === 0" class="text-center py-4">
      <CalendarIcon class="w-8 h-8 text-lavender-400 dark:text-lavender-500 mx-auto mb-1" />
      <p class="text-sm text-lavender-500 dark:text-lavender-400">No events today</p>
    </div>

    <!-- Events list -->
    <div v-else class="space-y-2">
      <div
        v-for="event in events.slice(0, limit)"
        :key="event.id"
        class="flex items-start gap-3 pb-2 border-b border-lavender-200 dark:border-prussian-700 last:border-0 last:pb-0"
      >
        <div
          class="w-2 h-full rounded-full mt-1 flex-shrink-0"
          :style="{ backgroundColor: event.color || event.user?.color || '#8B5CF6' }"
        ></div>
        <div class="flex-1 min-w-0">
          <p class="text-sm font-medium text-prussian-600 dark:text-lavender-200 truncate">
            {{ event.title || event.summary }}
          </p>
          <p class="text-xs text-lavender-500 dark:text-lavender-400 mt-0.5">
            <template v-if="event.all_day">All day</template>
            <template v-else-if="event.start_time">{{ formatTime(event.start_time) }}</template>
            <template v-else-if="event.start">{{ formatTime(event.start) }}</template>
          </p>
        </div>
        <span
          v-if="event.source_label"
          class="text-[10px] text-lavender-400 dark:text-lavender-500 flex-shrink-0 mt-0.5"
        >
          {{ event.source_label }}
        </span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { DateTime } from 'luxon'
import { CalendarIcon } from '@heroicons/vue/24/outline'
import { useWidgetData } from '@/composables/useWidgetData'

const props = defineProps({
  config: { type: Object, required: true },
})

const settings = computed(() => props.config.settings || {})
const limit = computed(() => settings.value.limit || 5)
const viewAllPath = computed(() => settings.value.viewAllPath || null)

const { data, loading } = useWidgetData(props.config.endpoint, props.config.params)

const events = computed(() => {
  if (!data.value) return []
  const list = data.value.events || data.value.data || data.value
  if (!Array.isArray(list)) return []
  return list
})

function formatTime(dateStr) {
  if (!dateStr) return ''
  const dt = DateTime.fromISO(dateStr)
  return dt.toLocaleString(DateTime.TIME_SIMPLE)
}
</script>
