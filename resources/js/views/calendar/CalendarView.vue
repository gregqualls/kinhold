<template>
  <div class="flex h-full">
    <!-- Main column -->
    <div class="flex-1 min-w-0 p-3 md:p-6 overflow-y-auto">
      <!-- Header -->
      <div class="flex items-center justify-between mb-2 md:mb-4 gap-3 flex-wrap">
        <h1 class="text-lg md:text-2xl font-bold font-heading text-ink-primary">Calendar</h1>

        <div class="flex items-center gap-2 flex-wrap">
          <!-- View Mode Selector -->
          <KinTabPillGroup
            v-model:active-key="viewMode"
            :tabs="viewModeTabs"
            variant="tinted"
            size="sm"
            @change="setViewMode"
          />
        </div>
      </div>

      <!-- Day-view editorial header (hero day number + week / month) -->
      <div v-if="viewMode === 'day'" class="mb-6 flex items-center gap-3 flex-wrap">
        <KinButton variant="ghost" size="sm" icon-only aria-label="Previous day" @click="navigatePrev">
          <ChevronLeftIcon class="w-5 h-5" />
        </KinButton>
        <KinDayHeader
          :day="currentMonth.day"
          :weekday="currentMonth.toFormat('EEEE')"
          :month="currentMonth.toFormat('MMMM yyyy')"
          :event-count="dayEvents.length"
          :is-today="currentMonth.hasSame(now, 'day')"
          size="md"
          class="flex-1 min-w-0"
        />
        <KinButton variant="ghost" size="sm" icon-only aria-label="Next day" @click="navigateNext">
          <ChevronRightIcon class="w-5 h-5" />
        </KinButton>
      </div>

      <!-- Month/Week navigation bar (compact title + prev/next/today) -->
      <div v-else class="flex items-center justify-between mb-6 px-3 py-2 bg-surface-raised rounded-card border border-border-subtle">
        <KinButton variant="ghost" size="sm" icon-only aria-label="Previous" @click="navigatePrev">
          <ChevronLeftIcon class="w-5 h-5" />
        </KinButton>

        <div class="flex items-center gap-3 flex-wrap justify-center">
          <h2 class="text-lg md:text-xl font-semibold font-heading text-ink-primary">
            {{ navigationTitle }}
          </h2>
          <KinButton variant="secondary" size="sm" @click="navigateToToday">
            Today
          </KinButton>
        </div>

        <KinButton variant="ghost" size="sm" icon-only aria-label="Next" @click="navigateNext">
          <ChevronRightIcon class="w-5 h-5" />
        </KinButton>
      </div>

      <!-- Calendar Grid (Month View) -->
      <div v-if="viewMode === 'month'" class="bg-surface-raised rounded-card shadow-resting border border-border-subtle p-3 md:p-4">
        <KinMonthGrid
          :cells="monthCells"
          :events="filteredMonthEventsMap"
          :event-labels="filteredMonthEventLabels"
          :today="todayInCurrentMonth"
          :selected="null"
          density="pills"
          :max-pills="3"
          @select="(day) => onMonthDaySelect(day)"
        />
      </div>

      <!-- Week View (Time Grid) -->
      <TimeGrid
        v-else-if="viewMode === 'week'"
        :events="filteredWeekEvents"
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
        :events="filteredDayEvents"
        :days="[currentMonth]"
        :columns="1"
        :hour-height="72"
        :max-height="700"
        :get-event-color="getEventColor"
        :get-calendar-source-name="getCalendarSourceName"
        @event-click="handleEventClick"
      />
    </div>

    <!-- Right utility rail (desktop only) -->
    <KinUtilityRail class="hidden lg:flex" width="280px">
      <!-- Mini-month -->
      <template #mini-month>
        <div class="flex items-center justify-between mb-2">
          <button
            type="button"
            class="w-6 h-6 rounded-full flex items-center justify-center text-ink-tertiary hover:bg-surface-sunken hover:text-ink-primary transition-colors"
            aria-label="Previous month"
            @click="navigateMiniMonth(-1)"
          >
            <ChevronLeftIcon class="w-3.5 h-3.5" />
          </button>
          <span class="text-[12px] font-semibold text-ink-primary">{{ currentMonth.toFormat('MMMM yyyy') }}</span>
          <button
            type="button"
            class="w-6 h-6 rounded-full flex items-center justify-center text-ink-tertiary hover:bg-surface-sunken hover:text-ink-primary transition-colors"
            aria-label="Next month"
            @click="navigateMiniMonth(1)"
          >
            <ChevronRightIcon class="w-3.5 h-3.5" />
          </button>
        </div>
        <!-- Day-of-week header -->
        <div class="grid grid-cols-7 mb-1">
          <div
            v-for="d in MINI_DOW"
            :key="d"
            class="text-center text-[9px] font-semibold uppercase text-ink-tertiary"
          >
            {{ d }}
          </div>
        </div>
        <!-- Cells -->
        <div class="grid grid-cols-7 gap-y-0.5">
          <button
            v-for="(cell, idx) in monthCells"
            :key="idx"
            type="button"
            class="flex flex-col items-center justify-start py-0.5 text-[11px] transition-colors"
            :class="cell.month !== 'current' ? 'opacity-30 pointer-events-none' : ''"
            @click="onMonthDaySelect(cell.day)"
          >
            <span
              class="w-6 h-6 rounded-full flex items-center justify-center"
              :class="cell.day === todayInCurrentMonth && cell.month === 'current'
                ? 'bg-accent-lavender-bold text-ink-inverse font-semibold'
                : 'text-ink-primary hover:bg-surface-sunken'"
            >{{ cell.day }}</span>
            <span
              v-if="cell.month === 'current' && filteredMonthEventsMap[cell.day]?.length"
              class="w-[3px] h-[3px] rounded-full mt-0.5"
              :class="cell.day === todayInCurrentMonth ? 'bg-ink-inverse' : 'bg-accent-lavender-bold'"
            ></span>
            <span v-else class="h-[3px] mt-0.5"></span>
          </button>
        </div>
      </template>

      <!-- Filters: per-source toggleable chips -->
      <template #filters>
        <div class="flex flex-wrap gap-1.5">
          <KinChip
            v-for="src in sourceFilters"
            :key="src.key"
            variant="filter"
            size="sm"
            :custom-color="src.color"
            :active="!!sourceFilterState[src.key]"
            @click="toggleSourceFilter(src.key)"
          >
            {{ src.label }}
          </KinChip>
        </div>
      </template>

      <!-- Actions -->
      <template #actions>
        <div class="flex items-center gap-2">
          <KinButton variant="primary" size="sm" @click="openCreateModal()">
            <template #leading>
              <PlusIcon class="w-4 h-4" />
            </template>
            Add Event
          </KinButton>
          <KinButton variant="ghost" size="sm" @click="navigateToToday">
            Today
          </KinButton>
        </div>
      </template>
    </KinUtilityRail>

    <!-- Mobile Add Event FAB-like floating button (since it's no longer in the header on mobile) -->
    <button
      v-if="!showEventModal"
      type="button"
      class="lg:hidden fixed bottom-20 right-4 z-30 w-14 h-14 rounded-full flex items-center justify-center shadow-elevated bg-accent-lavender-bold text-ink-inverse hover:brightness-110 transition-all"
      aria-label="Add event"
      @click="openCreateModal()"
    >
      <PlusIcon class="w-6 h-6" />
    </button>

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
import { computed, ref, reactive, onMounted, watch } from 'vue'
import { DateTime } from 'luxon'
import { storeToRefs } from 'pinia'
import { useCalendarStore } from '@/stores/calendar'
import TimeGrid from '@/components/calendar/TimeGrid.vue'
import EventModal from '@/components/common/EventModal.vue'
import ConfirmDialog from '@/components/common/ConfirmDialog.vue'
import KinButton from '@/components/design-system/KinButton.vue'
import KinTabPillGroup from '@/components/design-system/KinTabPillGroup.vue'
import KinMonthGrid from '@/components/design-system/KinMonthGrid.vue'
import KinChip from '@/components/design-system/KinChip.vue'
import KinUtilityRail from '@/components/design-system/KinUtilityRail.vue'
import KinDayHeader from '@/components/design-system/KinDayHeader.vue'
import {
  ChevronLeftIcon,
  ChevronRightIcon,
  PlusIcon,
} from '@heroicons/vue/24/outline'

const calendarStore = useCalendarStore()

const { currentMonth, viewMode, events, connections } = storeToRefs(calendarStore)

const viewModeTabs = [
  { key: 'month', label: 'Month' },
  { key: 'week',  label: 'Week' },
  { key: 'day',   label: 'Day' },
]

// "now" — used by KinDayHeader for the TODAY badge.
const now = ref(DateTime.now())

// Mini-month day-of-week labels for the rail.
const MINI_DOW = ['S', 'M', 'T', 'W', 'T', 'F', 'S']

const navigateMiniMonth = (delta) => {
  if (delta < 0) calendarStore.navigateMonth('prev')
  else calendarStore.navigateMonth('next')
}

// ── Source filter toggles (in the utility rail) ─────────────────────────────
//
// Three built-in source keys plus one per calendar connection.
// Default: everything on. Toggling a key off filters that source out of the
// month/week/day grids.
const sourceFilters = computed(() => {
  const built = [
    { key: 'manual', label: 'Family Events', color: '#6856B2' },
    { key: 'task',   label: 'Tasks',         color: '#A07A10' },
  ]
  const conn = (connections.value || []).map((c, idx) => ({
    key: `cal:${c.calendar_id || c.id}`,
    label: c.calendar_name || 'Calendar',
    color: c.color || defaultColors[idx % defaultColors.length],
  }))
  return [...built, ...conn]
})

// Default-on map keyed by source key. Initialized lazily as connections arrive.
const sourceFilterState = reactive({})

watch(sourceFilters, (filters) => {
  for (const f of filters) {
    if (sourceFilterState[f.key] === undefined) sourceFilterState[f.key] = true
  }
}, { immediate: true })

const toggleSourceFilter = (key) => {
  sourceFilterState[key] = !sourceFilterState[key]
}

const passesSourceFilter = (event) => {
  if (event.source === 'manual') return !!sourceFilterState.manual
  if (event.source === 'task')   return !!sourceFilterState.task
  if (event.calendar_id) return !!sourceFilterState[`cal:${event.calendar_id}`]
  return true
}

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

// ── KinMonthGrid adapters ───────────────────────────────────────────────────
//
// Map a calendar event to one of the four Kin accent families. Source first,
// then connection-color cycling so each calendar gets a stable but distinct
// accent regardless of its raw hex.
const ACCENT_CYCLE = ['peach', 'mint', 'lavender', 'sun']

const accentForConnectionId = (calId) => {
  const conns = connections.value || []
  const idx = conns.findIndex((c) => c.calendar_id === calId)
  if (idx === -1) return 'lavender'
  return ACCENT_CYCLE[idx % ACCENT_CYCLE.length]
}

const accentForEvent = (event) => {
  if (event.source === 'task') return 'sun'
  if (event.source === 'manual') return 'lavender'
  if (event.calendar_id) return accentForConnectionId(event.calendar_id)
  return 'lavender'
}

// 42-cell array shaped for KinMonthGrid: { day, month: 'current'|'leading'|'trailing' }
const monthCells = computed(() =>
  calendarDays.value.map((d) => ({
    day: d.day,
    month: d.isCurrentMonth
      ? 'current'
      : (d.date < currentMonth.value.startOf('month') ? 'leading' : 'trailing'),
  }))
)

// Map of day-number → array of accent names. Only current-month events.
const monthEventsMap = computed(() => {
  const map = {}
  const monthStart = currentMonth.value.startOf('month')
  const monthEnd = currentMonth.value.endOf('month')
  for (const event of events.value) {
    if (!event.start) continue
    const d = DateTime.fromISO(event.start)
    if (d < monthStart || d > monthEnd) continue
    const day = d.day
    if (!map[day]) map[day] = []
    map[day].push(accentForEvent(event))
  }
  return map
})

// Optional event-title labels per day (KinMonthGrid uses these in pills mode).
// We're using dots mode but pass these along anyway for hover/aria context.
const monthEventLabels = computed(() => {
  const map = {}
  const monthStart = currentMonth.value.startOf('month')
  const monthEnd = currentMonth.value.endOf('month')
  for (const event of events.value) {
    if (!event.start) continue
    const d = DateTime.fromISO(event.start)
    if (d < monthStart || d > monthEnd) continue
    if (!map[d.day]) map[d.day] = []
    map[d.day].push(event.title || 'Event')
  }
  return map
})

const todayInCurrentMonth = computed(() => {
  const now = DateTime.now()
  return now.hasSame(currentMonth.value, 'month') ? now.day : null
})

// Click a day cell (main month grid OR rail mini-month) → drill into that
// day's expanded view. Pure navigation — no edit / create side-effects.
// Creating new events is always done via the rail's Add Event button (or
// the mobile FAB), and editing happens from the Day-view timeline where each
// event block is individually clickable.
const onMonthDaySelect = (day) => {
  currentMonth.value = currentMonth.value.set({ day })
  calendarStore.setViewMode('day')
}

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

// Source-filtered variants of the week / day / month event sets.
const filteredWeekEvents = computed(() => weekEvents.value.filter(passesSourceFilter))
const filteredDayEvents  = computed(() => dayEvents.value.filter(passesSourceFilter))
const filteredMonthEventsMap = computed(() => {
  const map = {}
  const monthStart = currentMonth.value.startOf('month')
  const monthEnd = currentMonth.value.endOf('month')
  for (const event of events.value) {
    if (!event.start) continue
    if (!passesSourceFilter(event)) continue
    const d = DateTime.fromISO(event.start)
    if (d < monthStart || d > monthEnd) continue
    if (!map[d.day]) map[d.day] = []
    map[d.day].push(accentForEvent(event))
  }
  return map
})

const filteredMonthEventLabels = computed(() => {
  const map = {}
  const monthStart = currentMonth.value.startOf('month')
  const monthEnd = currentMonth.value.endOf('month')
  for (const event of events.value) {
    if (!event.start) continue
    if (!passesSourceFilter(event)) continue
    const d = DateTime.fromISO(event.start)
    if (d < monthStart || d > monthEnd) continue
    if (!map[d.day]) map[d.day] = []
    map[d.day].push(event.title || 'Event')
  }
  return map
})

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
