<template>
  <div class="bg-white dark:bg-prussian-800 rounded-card shadow-card border border-lavender-200 dark:border-prussian-700 overflow-hidden">
    <!-- All-day events -->
    <div v-if="allDayEvents.length > 0" class="border-b border-lavender-200 dark:border-prussian-700 p-3">
      <p class="text-[10px] font-semibold text-lavender-500 dark:text-lavender-400 uppercase tracking-wide mb-2">All Day</p>
      <div class="flex flex-wrap gap-1.5">
        <div
          v-for="event in allDayEvents"
          :key="event.id"
          class="text-xs px-2 py-1 rounded font-medium truncate max-w-48 cursor-pointer hover:opacity-80 transition-opacity"
          :style="eventPillStyle(event)"
          @click.stop="$emit('event-click', event)"
        >
          {{ event.title }}
        </div>
      </div>
    </div>

    <!-- Time grid -->
    <div ref="scrollContainer" class="overflow-y-auto" :style="{ maxHeight: maxHeight + 'px' }">
      <div class="relative" :style="{ height: totalHeight + 'px' }">
        <!-- Hour lines -->
        <div
          v-for="hour in hours"
          :key="hour"
          class="absolute left-0 right-0 border-t border-lavender-100 dark:border-prussian-700/50"
          :style="{ top: getHourOffset(hour) + 'px' }"
        >
          <span class="absolute -top-2.5 left-2 text-[10px] font-medium text-lavender-500 dark:text-lavender-400 bg-white dark:bg-prussian-800 pr-1">
            {{ formatHourLabel(hour) }}
          </span>
        </div>

        <!-- Current time indicator -->
        <div
          v-if="showNowLine"
          class="absolute left-12 right-0 border-t-2 border-wisteria-500 z-20 pointer-events-none"
          :style="{ top: nowOffset + 'px' }"
        >
          <span class="absolute -top-1.5 -left-1.5 w-3 h-3 rounded-full bg-wisteria-500"></span>
        </div>

        <!-- Events container (offset for time labels) -->
        <div class="absolute top-0 bottom-0 left-14 right-2">
          <!-- Day columns -->
          <div class="h-full flex" :class="columns === 1 ? '' : 'gap-px'">
            <div
              v-for="(dayEvents, colIdx) in columnEvents"
              :key="colIdx"
              class="relative flex-1 min-w-0"
              :class="dayLabels[colIdx]?.isPast && !dayLabels[colIdx]?.isToday ? 'bg-lavender-100/60 dark:bg-prussian-950/70 [&>*:not(:first-child)]:opacity-55 hover:[&>*:not(:first-child)]:opacity-100' : ''"
            >
              <!-- Day header for week view -->
              <div
                v-if="columns > 1"
                class="sticky top-0 z-10 bg-white dark:bg-prussian-800 text-center py-1 border-b border-lavender-100 dark:border-prussian-700/50"
                :class="dayLabels[colIdx]?.isPast && !dayLabels[colIdx]?.isToday ? 'bg-lavender-50 dark:bg-prussian-900/60' : ''"
              >
                <p
                  class="text-[10px] font-semibold uppercase"
                  :class="dayLabels[colIdx]?.isPast && !dayLabels[colIdx]?.isToday
                    ? 'text-lavender-400/70 dark:text-lavender-500/70'
                    : 'text-lavender-500 dark:text-lavender-400'"
                >
                  {{ dayLabels[colIdx]?.weekday }}
                </p>
                <p
                  :class="[
                    'text-sm font-bold',
                    dayLabels[colIdx]?.isToday
                      ? 'text-wisteria-600 dark:text-wisteria-400'
                      : dayLabels[colIdx]?.isPast
                        ? 'text-lavender-400 dark:text-lavender-500'
                        : 'text-prussian-500 dark:text-lavender-200',
                  ]"
                >
                  <span
                    v-if="dayLabels[colIdx]?.isToday"
                    class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-wisteria-600 text-white"
                  >
                    {{ dayLabels[colIdx]?.day }}
                  </span>
                  <span v-else>{{ dayLabels[colIdx]?.day }}</span>
                </p>
              </div>

              <!-- Positioned events -->
              <div
                v-for="positioned in positionEvents(dayEvents.timed)"
                :key="positioned.event.id"
                class="absolute rounded-md px-2 py-1 overflow-hidden cursor-pointer transition-shadow hover:shadow-md group"
                :style="{
                  top: positioned.top + 'px',
                  height: Math.max(positioned.height, 20) + 'px',
                  left: positioned.leftPercent + '%',
                  width: positioned.widthPercent + '%',
                  ...eventBlockStyle(positioned.event),
                }"
                :title="positioned.event.title + ' — ' + getCalendarSourceName(positioned.event)"
                @click.stop="$emit('event-click', positioned.event)"
              >
                <p class="text-xs font-semibold truncate leading-tight" :style="{ color: getEventColor(positioned.event) }">
                  {{ positioned.event.title }}
                </p>
                <p v-if="positioned.height >= 36" class="text-[10px] truncate mt-0.5 opacity-75" :style="{ color: getEventColor(positioned.event) }">
                  {{ formatTime(positioned.event.start) }} - {{ formatTime(positioned.event.end) }}
                </p>
                <p v-if="positioned.height >= 52" class="text-[10px] truncate mt-0.5 opacity-60" :style="{ color: getEventColor(positioned.event) }">
                  {{ getCalendarSourceName(positioned.event) }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, onMounted, nextTick } from 'vue'
import { DateTime } from 'luxon'

const props = defineProps({
  events: { type: Array, required: true },
  days: { type: Array, required: true },
  columns: { type: Number, default: 1 },
  startHour: { type: Number, default: 6 },
  endHour: { type: Number, default: 23 },
  hourHeight: { type: Number, default: 60 },
  maxHeight: { type: Number, default: 640 },
  getEventColor: { type: Function, required: true },
  getCalendarSourceName: { type: Function, required: true },
})

defineEmits(['event-click'])

const scrollContainer = ref(null)

const hours = computed(() => {
  const h = []
  for (let i = props.startHour; i <= props.endHour; i++) {
    h.push(i)
  }
  return h
})

const totalHeight = computed(() => (props.endHour - props.startHour + 1) * props.hourHeight)

const getHourOffset = (hour) => (hour - props.startHour) * props.hourHeight

const formatHourLabel = (hour) => {
  if (hour === 0 || hour === 24) return '12 AM'
  if (hour === 12) return '12 PM'
  return hour > 12 ? `${hour - 12} PM` : `${hour} AM`
}

const formatTime = (dateStr) => {
  if (!dateStr) return ''
  return DateTime.fromISO(dateStr).toFormat('h:mm a')
}

// Current time line
const showNowLine = computed(() => {
  const now = DateTime.now()
  return props.days.some((d) => d.hasSame(now, 'day'))
})

const nowOffset = computed(() => {
  const now = DateTime.now()
  const minutesSinceStart = (now.hour - props.startHour) * 60 + now.minute
  return (minutesSinceStart / 60) * props.hourHeight
})

// Separate all-day vs timed events per column
const allDayEvents = computed(() => {
  return props.events.filter((e) => e.all_day)
})

const columnEvents = computed(() => {
  return props.days.map((day) => {
    const dateStr = day.toISODate()
    const dayEvts = props.events.filter((e) => {
      if (!e.start) return false
      return DateTime.fromISO(e.start).toISODate() === dateStr
    })
    return {
      timed: dayEvts.filter((e) => !e.all_day),
    }
  })
})

const dayLabels = computed(() => {
  const todayStart = DateTime.now().startOf('day')
  return props.days.map((day) => ({
    weekday: day.toFormat('EEE'),
    day: day.day,
    isToday: day.hasSame(DateTime.now(), 'day'),
    isPast: day.startOf('day') < todayStart,
  }))
})

// Position events with overlap handling
const positionEvents = (events) => {
  if (!events.length) return []

  const positioned = events.map((event) => {
    const start = DateTime.fromISO(event.start)
    const end = event.end ? DateTime.fromISO(event.end) : start.plus({ minutes: 30 })
    const startMinutes = (start.hour - props.startHour) * 60 + start.minute
    const endMinutes = (end.hour - props.startHour) * 60 + end.minute
    const top = (startMinutes / 60) * props.hourHeight
    const height = ((endMinutes - startMinutes) / 60) * props.hourHeight

    return {
      event,
      top: Math.max(top, 0),
      height: Math.max(height, 20),
      startMinutes,
      endMinutes: Math.max(endMinutes, startMinutes + 15),
    }
  })

  // Sort by start time, then by duration (longer first)
  positioned.sort((a, b) => a.startMinutes - b.startMinutes || (b.endMinutes - b.startMinutes) - (a.endMinutes - a.startMinutes))

  // Assign columns for overlapping events
  const columns = []
  for (const item of positioned) {
    let placed = false
    for (let c = 0; c < columns.length; c++) {
      const lastInCol = columns[c][columns[c].length - 1]
      if (item.startMinutes >= lastInCol.endMinutes) {
        columns[c].push(item)
        item.col = c
        placed = true
        break
      }
    }
    if (!placed) {
      item.col = columns.length
      columns.push([item])
    }
  }

  const totalCols = columns.length || 1
  return positioned.map((item) => ({
    ...item,
    leftPercent: (item.col / totalCols) * 100,
    widthPercent: (1 / totalCols) * 100 - 1,
  }))
}

const eventBlockStyle = (event) => {
  const color = props.getEventColor(event)
  return {
    backgroundColor: color + '20',
    borderLeft: `3px solid ${color}`,
  }
}

const eventPillStyle = (event) => {
  const color = props.getEventColor(event)
  return {
    backgroundColor: color + '18',
    color: color,
    borderLeft: `3px solid ${color}`,
  }
}

// Scroll to current time on mount
onMounted(async () => {
  await nextTick()
  if (scrollContainer.value) {
    const now = DateTime.now()
    const scrollTo = ((now.hour - props.startHour - 1) * props.hourHeight)
    scrollContainer.value.scrollTop = Math.max(scrollTo, 0)
  }
})
</script>
