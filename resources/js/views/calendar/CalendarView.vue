<template>
  <div class="p-4 md:p-6 max-w-6xl">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold font-heading text-prussian-500 dark:text-lavender-200">Calendar</h1>

      <div class="flex items-center gap-2">
        <!-- Add Event Button -->
        <button
          class="btn-primary btn-sm flex items-center gap-1.5"
          @click="openCreateModal()"
        >
          <PlusIcon class="w-4 h-4" />
          <span class="hidden sm:inline">Add Event</span>
        </button>

        <!-- View Mode Selector -->
        <div class="flex gap-1 bg-lavender-100 dark:bg-prussian-700 rounded-lg p-1">
          <button
            v-for="mode in ['month', 'week', 'day']"
            :key="mode"
            :class="[
              'px-3 py-1.5 rounded-md text-sm font-medium transition-all duration-150',
              viewMode === mode
                ? 'bg-wisteria-600 text-white shadow-sm'
                : 'text-prussian-500 dark:text-lavender-300 hover:text-prussian-500 dark:hover:text-lavender-200 hover:bg-lavender-200 dark:hover:bg-prussian-600',
            ]"
            @click="setViewMode(mode)"
          >
            {{ mode.charAt(0).toUpperCase() + mode.slice(1) }}
          </button>
        </div>
      </div>
    </div>

    <!-- Navigation -->
    <div class="flex items-center justify-between mb-6 card">
      <button class="btn-ghost btn-sm rounded-lg" @click="navigatePrev">
        <ChevronLeftIcon class="w-5 h-5" />
      </button>

      <div class="flex items-center gap-4">
        <h2 class="text-xl font-semibold font-heading text-prussian-500 dark:text-lavender-200">
          {{ navigationTitle }}
        </h2>
        <button
          class="btn-secondary btn-sm"
          @click="navigateToToday"
        >
          Today
        </button>
      </div>

      <button class="btn-ghost btn-sm rounded-lg" @click="navigateNext">
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
            'min-h-24 p-2 bg-white dark:bg-prussian-800 cursor-pointer hover:bg-lavender-50 dark:hover:bg-prussian-700 transition-colors',
            !day.isCurrentMonth && 'bg-lavender-50 dark:bg-prussian-900',
            day.isPast && !day.isToday && 'bg-lavender-100/60 dark:bg-prussian-950/70 opacity-55 hover:opacity-100',
            day.isToday && 'ring-2 ring-wisteria-400 ring-inset',
          ]"
          @click="openCreateModal(day.date.toISODate())"
        >
          <p
            :class="[
              'text-xs font-semibold mb-1',
              day.isToday
                ? 'text-wisteria-600'
                : !day.isCurrentMonth
                  ? 'text-lavender-400 dark:text-lavender-500'
                  : day.isPast
                    ? 'text-lavender-400 dark:text-lavender-500'
                    : 'text-prussian-500 dark:text-lavender-200',
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
              @click.stop="handleEventClick(event)"
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
      @event-click="handleEventClick"
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
      @event-click="handleEventClick"
    />

    <!-- Calendar Legend -->
    <div class="mt-6 card p-4">
      <p class="text-sm font-semibold text-prussian-500 dark:text-lavender-200 mb-3">Sources</p>
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
        <!-- Manual events -->
        <div class="flex items-center gap-2">
          <span class="w-3 h-3 rounded-full flex-shrink-0 bg-indigo-500"></span>
          <span class="text-sm text-prussian-400 dark:text-lavender-400">Family Events</span>
        </div>
        <!-- Tasks -->
        <div class="flex items-center gap-2">
          <span class="w-3 h-3 rounded-full flex-shrink-0 border-2 border-dashed" style="border-color: #B38A50"></span>
          <span class="text-sm text-prussian-400 dark:text-lavender-400">Tasks</span>
        </div>
      </div>
    </div>

    <!-- Create/Edit Event Modal -->
    <EventModal
      :show="showEventModal"
      :event="editingEvent"
      :mode="'calendar'"
      :prefill-date="prefillDate"
      @close="closeModal"
      @save="handleSave"
    />

    <!-- Delete Confirmation -->
    <ConfirmDialog
      :show="showDeleteConfirm"
      title="Delete Event"
      :message="`Are you sure you want to delete '${editingEvent?.title}'?`"
      confirm-text="Delete"
      variant="danger"
      @confirm="handleDelete"
      @cancel="showDeleteConfirm = false"
    />
  </div>
</template>

<script setup>
import { computed, ref, onMounted, watch } from 'vue'
import { DateTime } from 'luxon'
import { storeToRefs } from 'pinia'
import { useCalendarStore } from '@/stores/calendar'
import TimeGrid from '@/components/calendar/TimeGrid.vue'
import EventModal from '@/components/common/EventModal.vue'
import ConfirmDialog from '@/components/common/ConfirmDialog.vue'
import {
  ChevronLeftIcon,
  ChevronRightIcon,
  PlusIcon,
} from '@heroicons/vue/24/outline'

const calendarStore = useCalendarStore()

const { currentMonth, viewMode, events, connections } = storeToRefs(calendarStore)

// Event modal state
const showEventModal = ref(false)
const editingEvent = ref(null)
const prefillDate = ref(null)
const showDeleteConfirm = ref(false)

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

const calendarNameMap = computed(() => {
  const map = {}
  ;(connections.value || []).forEach((conn) => {
    if (conn.calendar_id) map[conn.calendar_id] = conn.calendar_name || 'Calendar'
    if (conn.calendar_name) map[conn.calendar_name] = conn.calendar_name
  })
  return map
})

const getCalendarSourceName = (event) => {
  if (event.source === 'task') return 'Task'
  if (event.source === 'manual') return 'Family Event'
  if (event.calendar_id && calendarNameMap.value[event.calendar_id]) {
    return calendarNameMap.value[event.calendar_id]
  }
  if (event.user?.name) {
    return event.user.name + "'s Calendar"
  }
  return 'Calendar'
}

const getEventColor = (event) => {
  if (event.source === 'task') return event.user?.color || '#B38A50'
  if (event.source === 'manual') return event.user?.color || '#6366f1'
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
  const isTask = event.source === 'task'
  return {
    backgroundColor: color + '18',
    color: color,
    borderLeft: isTask ? `3px dashed ${color}` : `3px solid ${color}`,
  }
}

const openCreateModal = (dateStr = null) => {
  editingEvent.value = null
  prefillDate.value = dateStr
  showEventModal.value = true
}

const handleEventClick = (event) => {
  // Only manual events are editable
  if (event.source !== 'manual') return

  editingEvent.value = event
  prefillDate.value = null
  showEventModal.value = true
}

const closeModal = () => {
  showEventModal.value = false
  editingEvent.value = null
  prefillDate.value = null
}

const handleSave = async (eventData) => {
  try {
    if (editingEvent.value?.id) {
      await calendarStore.updateEvent(editingEvent.value.id, eventData)
    } else {
      await calendarStore.createEvent(eventData)
    }
    closeModal()
  } catch {
    // error handled by store
  }
}

const handleDelete = async () => {
  if (!editingEvent.value?.id) return
  try {
    await calendarStore.deleteEvent(editingEvent.value.id)
    closeModal()
  } catch {
    // error handled by store
  }
  showDeleteConfirm.value = false
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

  const todayStart = DateTime.now().startOf('day')

  while (current <= endDate) {
    const isCurrentMonth = current.month === currentMonth.value.month
    const today = current.hasSame(DateTime.now(), 'day')
    const isPast = current.startOf('day') < todayStart

    days.push({
      date: current,
      day: current.day,
      month: current.month,
      year: current.year,
      isCurrentMonth,
      isToday: today,
      isPast,
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
