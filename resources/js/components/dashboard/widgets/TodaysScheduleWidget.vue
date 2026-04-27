<template>
  <div class="flex flex-col h-full">
    <div class="flex items-center justify-between mb-3 flex-shrink-0">
      <h3 class="text-sm font-semibold text-ink-primary flex items-center gap-2">
        <CalendarIcon class="w-4 h-4 text-accent-lavender-bold" />
        {{ config.title || "Today's Schedule" }}
      </h3>
      <RouterLink
        to="/calendar"
        class="text-xs font-medium text-accent-lavender-bold hover:opacity-80 transition-opacity"
      >
        View Calendar
      </RouterLink>
    </div>

    <div v-if="loading" class="space-y-2">
      <KinSkeleton v-for="n in 3" :key="n" shape="rect" :height="'40px'" />
    </div>

    <KinEmptyState
      v-else-if="events.length === 0"
      :icon="CalendarIcon"
      title="No events today"
      description="Your calendar is clear!"
      size="sm"
      accent-color="lavender"
    />

    <div v-else :class="config.size === 'md' ? 'columns-2 gap-x-6 space-y-2 [&>*]:break-inside-avoid' : 'space-y-2'">
      <div
        v-for="event in events"
        :key="event.id"
        class="flex items-start gap-3 pb-2 border-b border-border-subtle last:border-0 last:pb-0"
      >
        <div
          class="w-1.5 self-stretch rounded-full flex-shrink-0"
          :style="{ backgroundColor: event.color || event.user?.color || '#8B5CF6' }"
        ></div>
        <div class="flex-1 min-w-0">
          <p class="text-sm font-medium text-ink-secondary truncate">
            {{ event.title || event.summary }}
          </p>
          <div class="flex items-center gap-2 mt-0.5">
            <p class="text-xs text-ink-tertiary">
              <template v-if="event.all_day">All day</template>
              <template v-else-if="event.start_time">{{ formatTime(event.start_time) }}</template>
              <template v-else-if="event.start">{{ formatTime(event.start) }}</template>
            </p>
            <span
              v-if="event.source_label && config.size === 'md'"
              class="text-[10px] text-ink-tertiary"
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
import KinSkeleton from '@/components/design-system/KinSkeleton.vue'
import KinEmptyState from '@/components/design-system/KinEmptyState.vue'

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
