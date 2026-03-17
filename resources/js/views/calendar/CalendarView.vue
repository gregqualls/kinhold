<template>
  <div class="p-4 md:p-6 max-w-6xl">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-prussian-500 dark:text-lavender-200">Calendar</h1>

      <!-- View Mode Selector -->
      <div class="flex gap-1 bg-lavender-100 dark:bg-prussian-700 rounded-lg p-1">
        <button
          v-for="mode in ['month', 'week', 'day']"
          :key="mode"
          @click="setViewMode(mode)"
          :class="[
            'px-3 py-1.5 rounded-md text-sm font-medium transition-all duration-150',
            viewMode === mode
              ? 'bg-wisteria-600 text-white shadow-sm'
              : 'text-prussian-500 dark:text-lavender-300 hover:text-prussian-500 dark:hover:text-lavender-200 hover:bg-lavender-200 dark:hover:bg-prussian-600',
          ]"
        >
          {{ mode.charAt(0).toUpperCase() + mode.slice(1) }}
        </button>
      </div>
    </div>

    <!-- Navigation -->
    <div class="flex items-center justify-between mb-6 card">
      <button @click="navigatePrev" class="btn-ghost btn-sm rounded-lg">
        <ChevronLeftIcon class="w-5 h-5" />
      </button>

      <div class="flex items-center gap-4">
        <h2 class="text-xl font-semibold text-prussian-500 dark:text-lavender-200">
          {{ navigationTitle }}
        </h2>
        <button
          @click="navigateToToday"
          class="btn-secondary btn-sm"
        >
          Today
        </button>
      </div>

      <button @click="navigateNext" class="btn-ghost btn-sm rounded-lg">
        <ChevronRightIcon class="w-5 h-5" />
      </button>
    </div>

    <!-- Calendar Grid (Month View) -->
    <div v-if="viewMode === 'month'" class="bg-white dark:bg-prussian-800 rounded-card shadow-card border border-lavender-200 dark:border-prussian-700 overflow-hidden">
      <!-- Day headers -->
      <div class="grid grid-cols-7 bg-lavender-100 dark:bg-prussian-700">
        <div
          v-for="day in ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']"
          :key="day"
          class="p-2 text-center font-semibold text-xs text-prussian-400 dark:text-lavender-400 uppercase tracking-wide"
        >
          {{ day }}
        </div>
      </div>

      <!-- Calendar days -->
      <div class="grid grid-cols-7 gap-px bg-lavender-200 dark:bg-prussian-700">
        <div
          v-for="day in calendarDays"
          :key="`${day.year}-${day.month}-${day.day}`"
          :class="[
            'min-h-24 p-2 bg-white dark:bg-prussian-800',
            !day.isCurrentMonth && 'bg-lavender-50 dark:bg-prussian-900',
            day.isToday && 'ring-2 ring-wisteria-400 ring-inset',
          ]"
        >
          <p
            :class="[
              'text-xs font-semibold mb-1',
              day.isToday ? 'text-wisteria-600' : !day.isCurrentMonth ? 'text-lavender-400 dark:text-lavender-500' : 'text-prussian-500 dark:text-lavender-200',
            ]"
          >
            <span
              v-if="day.isToday"
              class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-wisteria-600 text-white text-xs"
            >
              {{ day.day }}
            </span>
            <span v-else>{{ day.day }}</span>
          </p>
          <div class="space-y-1">
            <div
              v-for="event in getEventsForDay(day.date).slice(0, 3)"
              :key="event.id"
              class="text-xs p-1 rounded font-medium truncate cursor-pointer transition-colors"
              :style="eventStyle(event)"
              :title="getCalendarSourceName(event)"
            >
              {{ event.title }}
            </div>
            <div
              v-if="getEventsForDay(day.date).length > 3"
              class="text-xs text-lavender-600 dark:text-lavender-400 font-medium pl-1"
            >
              +{{ getEventsForDay(day.date).length - 3 }} more
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Week View (Time Grid) -->
    <TimeGrid
      v-else-if="viewMode === 'week'"
      :events="weekEvents"
      :days="weekDays"
      :columns="7"
      :hour-height="60"
      :max-height="640"
      :get-event-color="getEventColor"
      :get-calendar-source-name="getCalendarSourceName"
    />

    <!-- Day View (Time Grid) -->
    <TimeGrid
      v-else
      :events="dayEvents"
      :days="[currentMonth]"
      :columns="1"
      :hour-height="72"
      :max-height="700"
      :get-event-color="getEventColor"
      :get-calendar-source-name="getCalendarSourceName"
    />

    <!-- Calendar Legend -->
    <div v-if="(connections || []).length > 0" class="mt-6 card p-4">
      <p class="text-sm font-semibold text-prussian-500 dark:text-lavender-200 mb-3">Calendars</p>
      <div class="flex flex-wrap gap-3">
        <div
          v-for="(conn, idx) in connections"
          :key="conn.id"
          class="flex items-center gap-2"
        >
          <span
            class="w-3 h-3 rounded-full flex-shrink-0"
            :style="{ backgroundColor: conn.color || defaultColors[idx % defaultColors.length] }"
          ></span>
          <span class="text-sm text-prussian-400 dark:text-lavender-400">{{ conn.calendar_name || 'Calendar' }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, watch } from 'vue'
import { DateTime } from 'luxon'
import { storeToRefs } from 'pinia'
import { useCalendarStore } from '@/stores/calendar'
import { useAuthStore } from '@/stores/auth'
import EmptyState from '@/components/common/EmptyState.vue'
import TimeGrid from '@/components/calendar/TimeGrid.vue'
import {
  ChevronLeftIcon,
  ChevronRightIcon,
  CalendarIcon,
} from '@heroicons/vue/24/outline'

const calendarStore = useCalendarStore()
const authStore = useAuthStore()

const { currentMonth, viewMode, events, connections } = storeToRefs(calendarStore)
const { familyMembers } = storeToRefs(authStore)

// Default calendar colors to cycle through
const defaultColors = [
  '#1166ee', '#7c49b6', '#0d9488', '#e11d48', '#d97706',
  '#059669', '#0284c7', '#c026d3', '#ea580c', '#65a30d',
]

// Build a color map for calendar sources
const calendarColorMap = computed(() => {
  const map = {}
  ;(connections.value || []).forEach((conn, idx) => {
    const color = conn.color || defaultColors[idx % defaultColors.length]
    if (conn.calendar_name) map[conn.calendar_name] = color
    if (conn.calendar_id) map[conn.calendar_id] = color
  })
  return map
})

// Build a name map so events can show their calendar source
const calendarNameMap = computed(() => {
  const map = {}
  ;(connections.value || []).forEach((conn) => {
    if (conn.calendar_id) map[conn.calendar_id] = conn.calendar_name || 'Calendar'
    if (conn.calendar_name) map[conn.calendar_name] = conn.calendar_name
  })
  return map
})

const getCalendarSourceName = (event) => {
  if (event.calendar_id && calendarNameMap.value[event.calendar_id]) {
    return calendarNameMap.value[event.calendar_id]
  }
  if (event.user?.name) {
    return event.user.name + "'s Calendar"
  }
  return 'Calendar'
}

const getEventColor = (event) => {
  if (event.calendar_id && calendarColorMap.value[event.calendar_id]) {
    return calendarColorMap.value[event.calendar_id]
  }
  if (event.user?.color && event.user.color !== '#1f2937') {
    return event.user.color
  }
  return '#0d51bf'
}

const eventStyle = (event) => {
  const color = getEventColor(event)
  return {
    backgroundColor: color + '18',
    color: color,
    borderLeft: `3px solid ${color}`,
  }
}

const eventCardStyle = (event) => {
  const color = getEventColor(event)
  return {
    backgroundColor: color + '0a',
    borderLeftColor: color,
  }
}

const isDayToday = (day) => {
  return day.hasSame(DateTime.now(), 'day')
}

const navigationTitle = computed(() => {
  if (viewMode.value === 'month') {
    return currentMonth.value.toFormat('MMMM yyyy')
  } else if (viewMode.value === 'week') {
    const start = currentMonth.value.startOf('week')
    const end = start.plus({ days: 6 })
    if (start.month === end.month) {
      return `${start.toFormat('MMMM d')} - ${end.toFormat('d, yyyy')}`
    }
    return `${start.toFormat('MMM d')} - ${end.toFormat('MMM d, yyyy')}`
  } else {
    return currentMonth.value.toFormat('EEEE, MMMM d, yyyy')
  }
})

const calendarDays = computed(() => {
  const firstDay = currentMonth.value.startOf('month')
  const lastDay = currentMonth.value.endOf('month')
  const startDate = firstDay.startOf('week')
  const endDate = lastDay.endOf('week')

  const days = []
  let current = startDate

  while (current <= endDate) {
    const isCurrentMonth = current.month === currentMonth.value.month
    const today = current.hasSame(DateTime.now(), 'day')

    days.push({
      date: current,
      day: current.day,
      month: current.month,
      year: current.year,
      isCurrentMonth,
      isToday: today,
    })

    current = current.plus({ days: 1 })
  }

  return days
})

const weekDays = computed(() => {
  const start = currentMonth.value.startOf('week')
  const days = []

  for (let i = 0; i < 7; i++) {
    days.push(start.plus({ days: i }))
  }

  return days
})

const weekEvents = computed(() => {
  const start = currentMonth.value.startOf('week')
  const end = start.plus({ days: 6 }).endOf('day')
  return events.value.filter((e) => {
    if (!e.start) return false
    const d = DateTime.fromISO(e.start)
    return d >= start && d <= end
  })
})

const dayEvents = computed(() => {
  const dateStr = currentMonth.value.toISODate()
  return events.value.filter((e) => {
    if (!e.start) return false
    return DateTime.fromISO(e.start).toISODate() === dateStr
  })
})

const getEventsForDay = (day) => {
  const dateStr = day.toISODate ? day.toISODate() : day.toFormat('yyyy-MM-dd')
  return events.value.filter((event) => {
    const startField = event.start
    if (!startField) return false
    const eventDate = DateTime.fromISO(startField).toISODate()
    return eventDate === dateStr
  })
}

const formatTime = (dateStr) => {
  if (!dateStr) return ''
  return DateTime.fromISO(dateStr).toFormat('h:mm a')
}

const setViewMode = (mode) => {
  calendarStore.setViewMode(mode)
}

const navigatePrev = () => {
  if (viewMode.value === 'month') {
    calendarStore.navigateMonth('prev')
  } else if (viewMode.value === 'week') {
    calendarStore.navigateWeek('prev')
  } else {
    calendarStore.navigateDay('prev')
  }
}

const navigateNext = () => {
  if (viewMode.value === 'month') {
    calendarStore.navigateMonth('next')
  } else if (viewMode.value === 'week') {
    calendarStore.navigateWeek('next')
  } else {
    calendarStore.navigateDay('next')
  }
}

const navigateToToday = () => {
  calendarStore.navigateToToday()
}

// Re-fetch events when the view period changes
const fetchCurrentPeriodEvents = async () => {
  let start, end
  if (viewMode.value === 'month') {
    start = currentMonth.value.startOf('month').startOf('week')
    end = currentMonth.value.endOf('month').endOf('week')
  } else if (viewMode.value === 'week') {
    start = currentMonth.value.startOf('week')
    end = start.plus({ days: 6 })
  } else {
    start = currentMonth.value.startOf('day')
    end = currentMonth.value.plus({ days: 1 }).startOf('day')
  }
  await calendarStore.fetchEvents(start, end)
}

watch([currentMonth, viewMode], () => {
  fetchCurrentPeriodEvents()
})

onMounted(async () => {
  await calendarStore.fetchConnections()
  await fetchCurrentPeriodEvents()
})
</script>
