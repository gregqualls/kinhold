<template>
  <div class="flex flex-col h-full">
    <div class="flex items-center justify-between mb-3 flex-shrink-0">
      <h3 class="text-sm font-semibold text-prussian-500 dark:text-lavender-200 flex items-center gap-2">
        <CalendarIcon class="w-4 h-4 text-wisteria-600" />
        {{ config.title || "Today's Schedule" }}
      </h3>
      <RouterLink
        to="/calendar"
        class="text-wisteria-600 dark:text-wisteria-400 text-xs font-medium hover:text-wisteria-500"
      >
        View Calendar
      </RouterLink>
    </div>

    <div v-if="loading" class="space-y-2">
      <div v-for="n in 3" :key="n" class="h-10 bg-lavender-100 dark:bg-prussian-700 rounded-lg animate-pulse"></div>
    </div>

    <div v-else-if="events.length === 0" class="flex flex-col items-center justify-center py-6">
      <CalendarIcon class="w-8 h-8 text-lavender-400 dark:text-lavender-500 mb-1" />
      <p class="text-sm text-lavender-500 dark:text-lavender-400">No events today</p>
      <p class="text-xs text-lavender-400 dark:text-lavender-500 mt-0.5">Your calendar is clear!</p>
    </div>

    <div v-else :class="config.size === 'md' ? 'columns-2 gap-x-6 space-y-2 [&>*]:break-inside-avoid' : 'space-y-2'">
      <div
        v-for="event in events"
        :key="event.id"
        class="flex items-start gap-3 pb-2 border-b border-lavender-200 dark:border-prussian-700 last:border-0 last:pb-0"
      >
        <div
          class="w-1.5 self-stretch rounded-full flex-shrink-0"
          :style="{ backgroundColor: event.color || event.user?.color || '#8B5CF6' }"
        ></div>
        <div class="flex-1 min-w-0">
          <p class="text-sm font-medium text-prussian-600 dark:text-lavender-200 truncate">
            {{ event.title || event.summary }}
          </p>
          <div class="flex items-center gap-2 mt-0.5">
            <p class="text-xs text-lavender-500 dark:text-lavender-400">
              <template v-if="event.all_day">All day</template>
              <template v-else-if="event.start_time">{{ formatTime(event.start_time) }}</template>
              <template v-else-if="event.start">{{ formatTime(event.start) }}</template>
            </p>
            <span
              v-if="event.source_label && config.size === 'md'"
              class="text-[10px] text-lavender-400 dark:text-lavender-500"
            >
              {{ event.source_label }}
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { DateTime } from 'luxon'
import { CalendarIcon } from '@heroicons/vue/24/outline'
import { useWidgetData } from '@/composables/useWidgetData'

defineProps({
  config: { type: Object, required: true },
})

const today = DateTime.now()
const { data, loading } = useWidgetData('/api/v1/calendar/events', {
  start: today.toFormat('yyyy-MM-dd'),
  end: today.toFormat('yyyy-MM-dd'),
})

const events = computed(() => {
  if (!data.value) return []
  const list = data.value.events || data.value.data || data.value
  if (!Array.isArray(list)) return []
  return list
})

function formatTime(dateStr) {
  if (!dateStr) return ''
  return DateTime.fromISO(dateStr).toLocaleString(DateTime.TIME_SIMPLE)
}
</script>
