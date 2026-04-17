<template>
  <div ref="containerRef">
    <!-- Intra-week pagination (only when fewer days fit than the week has) -->
    <div
      v-if="visibleCount < dates.length"
      class="flex items-center justify-between mb-2 text-xs text-[#9C9895]"
    >
      <button
        class="p-1 rounded hover:bg-[#C4975A]/10 disabled:opacity-30 disabled:cursor-not-allowed transition-colors"
        :disabled="offset === 0"
        aria-label="Previous days"
        @click="shiftBy(-visibleCount)"
      >
        <ChevronLeftIcon class="w-4 h-4" />
      </button>
      <span class="font-medium">
        {{ visibleRangeLabel }}
      </span>
      <button
        class="p-1 rounded hover:bg-[#C4975A]/10 disabled:opacity-30 disabled:cursor-not-allowed transition-colors"
        :disabled="offset + visibleCount >= dates.length"
        aria-label="Next days"
        @click="shiftBy(visibleCount)"
      >
        <ChevronRightIcon class="w-4 h-4" />
      </button>
    </div>

    <div
      class="grid gap-px bg-lavender-200/50 dark:bg-prussian-700/50 rounded-xl overflow-hidden"
      :style="gridStyle"
    >
      <!-- Header row: corner cell + day headers -->
      <div class="bg-white dark:bg-[#1C1C20] p-2"></div>
      <div
        v-for="date in visibleDates"
        :key="'h-' + date"
        class="bg-white dark:bg-[#1C1C20] text-center py-2 px-1"
        :class="[
          isToday(date) ? 'bg-[#C4975A]/5' : '',
          isPast(date) && !isToday(date) ? 'bg-[#F5F2EE]/60 dark:bg-[#252528]/60' : '',
        ]"
      >
        <p
          class="text-[11px] font-semibold uppercase tracking-wider"
          :class="isToday(date)
            ? 'text-[#C4975A]'
            : isPast(date)
              ? 'text-[#9C9895]/60'
              : 'text-[#9C9895]'"
        >
          {{ formatDay(date) }}
        </p>
        <p
          class="text-lg font-bold leading-tight"
          :class="isToday(date)
            ? 'text-[#C4975A]'
            : isPast(date)
              ? 'text-[#9C9895]'
              : 'text-[#1C1C1E] dark:text-[#F0EDE9]'"
        >
          {{ formatDayNumber(date) }}
        </p>
      </div>

      <!-- Slot rows -->
      <template v-for="slot in slots" :key="slot.key">
        <!-- Slot label -->
        <div class="bg-white dark:bg-[#1C1C20] flex items-start gap-1.5 px-3 py-3">
          <component :is="slot.icon" class="w-4 h-4 text-[#9C9895] mt-0.5" />
          <span class="text-xs font-semibold uppercase tracking-wider text-[#9C9895] whitespace-nowrap">
            {{ slot.label }}
          </span>
        </div>

        <!-- Day cells for this slot -->
        <div
          v-for="date in visibleDates"
          :key="slot.key + '-' + date"
          class="bg-white dark:bg-[#1C1C20] p-1.5 min-w-0 transition-opacity"
          :class="[
            isToday(date) ? 'bg-[#C4975A]/[0.02]' : '',
            isPast(date) && !isToday(date) ? 'bg-[#EDE8E0]/70 dark:bg-[#0E0E10]/80 opacity-55 hover:opacity-100' : '',
          ]"
          :data-date="date"
          :data-slot="slot.key"
        >
          <VueDraggable
            v-model="localEntries[date + '/' + slot.key]"
            group="meal-entries"
            :animation="200"
            ghost-class="opacity-20"
            chosen-class="meal-drag-chosen"
            class="min-h-[48px] flex flex-col gap-1 rounded-[10px] border border-dashed border-transparent hover:border-[#E8E4DF] dark:hover:border-[#2E2E32] p-0.5 transition-colors"
            @end="onDragEnd"
          >
            <MealEntryCard
              v-for="entry in localEntries[date + '/' + slot.key]"
              :key="entry.id"
              :entry="entry"
              @click="$emit('entry-click', entry)"
              @delete="$emit('entry-delete', entry)"
            />
          </VueDraggable>
          <button
            class="flex items-center justify-center gap-1 py-1 w-full text-xs text-[#9C9895] hover:text-[#C4975A] hover:bg-[#C4975A]/5 rounded-lg transition-colors"
            @click="$emit('add-entry', date, slot.key)"
          >
            <PlusIcon class="w-3 h-3" />
          </button>
        </div>
      </template>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onBeforeUnmount } from 'vue'
import { DateTime } from 'luxon'
import { VueDraggable } from 'vue-draggable-plus'
import { PlusIcon, SunIcon, CloudIcon, MoonIcon, CakeIcon, ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/24/outline'
import MealEntryCard from './MealEntryCard.vue'
import { useMealsStore } from '@/stores/meals'

const props = defineProps({
  dates: { type: Array, required: true },
  entriesByDay: { type: Object, required: true },
})

defineEmits(['add-entry', 'entry-click', 'entry-delete'])

const mealsStore = useMealsStore()

const ALL_SLOTS = [
  { key: 'breakfast', label: 'Breakfast', icon: SunIcon },
  { key: 'lunch', label: 'Lunch', icon: CloudIcon },
  { key: 'dinner', label: 'Dinner', icon: MoonIcon },
  { key: 'snack', label: 'Snack', icon: CakeIcon },
]

const slots = computed(() =>
  ALL_SLOTS.filter(s => mealsStore.enabledMealSlots.includes(s.key))
)

// ── Responsive day-count ──
// Decide how many day columns can fit based on the container width.
// Anything narrower than ~140px per column gets dropped, never scrolled.
const LABEL_COL_PX = 120
const MIN_DAY_COL_PX = 140

const containerRef = ref(null)
const containerWidth = ref(0)
let observer = null

onMounted(() => {
  if (!containerRef.value) return
  containerWidth.value = containerRef.value.clientWidth
  observer = new ResizeObserver(entries => {
    for (const entry of entries) {
      containerWidth.value = entry.contentRect.width
    }
  })
  observer.observe(containerRef.value)
})

onBeforeUnmount(() => {
  if (observer && containerRef.value) observer.unobserve(containerRef.value)
  observer = null
})

const visibleCount = computed(() => {
  const total = props.dates.length || 7
  if (containerWidth.value === 0) return total
  const fits = Math.floor((containerWidth.value - LABEL_COL_PX) / MIN_DAY_COL_PX)
  return Math.max(1, Math.min(total, fits))
})

// Window over the week. Auto-anchored to today on mount and when dates change.
const offset = ref(0)

const clampOffset = () => {
  const max = Math.max(0, props.dates.length - visibleCount.value)
  if (offset.value > max) offset.value = max
  if (offset.value < 0) offset.value = 0
}

const visibleDates = computed(() => props.dates.slice(offset.value, offset.value + visibleCount.value))

const visibleRangeLabel = computed(() => {
  if (!visibleDates.value.length) return ''
  const start = DateTime.fromISO(visibleDates.value[0]).toFormat('EEE M/d')
  const end = DateTime.fromISO(visibleDates.value[visibleDates.value.length - 1]).toFormat('EEE M/d')
  if (visibleDates.value.length === 1) return start
  return `${start} – ${end}`
})

const shiftBy = (delta) => {
  offset.value = Math.max(0, Math.min(props.dates.length - visibleCount.value, offset.value + delta))
}

// When the window changes (resize) or the week changes, anchor to today if it's
// in view, otherwise clamp to a valid offset.
const anchorToToday = () => {
  const todayIdx = props.dates.findIndex(d => isToday(d))
  if (todayIdx === -1) {
    clampOffset()
    return
  }
  // Snap so that today is in view.
  if (todayIdx < offset.value || todayIdx >= offset.value + visibleCount.value) {
    offset.value = Math.max(0, Math.min(props.dates.length - visibleCount.value, todayIdx))
  } else {
    clampOffset()
  }
}

watch(() => props.dates, anchorToToday)
watch(visibleCount, anchorToToday)

const gridStyle = computed(() => ({
  gridTemplateColumns: `${LABEL_COL_PX}px repeat(${visibleCount.value}, minmax(0, 1fr))`,
}))

// Flattened local entries keyed by "date/slot" for VueDraggable v-model.
// We mutate in-place (not replace) so vue-draggable-plus keeps its refs.
const localEntries = ref({})

const syncLocalEntries = () => {
  const keysSeen = new Set()
  for (const date of props.dates) {
    const dayData = props.entriesByDay[date] || {}
    for (const slot of ALL_SLOTS) {
      const key = date + '/' + slot.key
      keysSeen.add(key)
      const newArr = [...(dayData[slot.key] || [])]
      const existing = localEntries.value[key]
      // Only replace if contents differ (compare ids + length)
      if (!existing
          || existing.length !== newArr.length
          || existing.some((e, i) => e.id !== newArr[i]?.id)) {
        localEntries.value[key] = newArr
      }
    }
  }
  // Clean up stale keys (date removed from view)
  for (const key of Object.keys(localEntries.value)) {
    if (!keysSeen.has(key)) {
      delete localEntries.value[key]
    }
  }
}

watch(
  () => [props.entriesByDay, props.dates],
  syncLocalEntries,
  { immediate: true, deep: true }
)

// Helpers
const isToday = (date) => DateTime.fromISO(date).hasSame(DateTime.now(), 'day')
const isPast = (date) => DateTime.fromISO(date).startOf('day') < DateTime.now().startOf('day')
const formatDay = (date) => DateTime.fromISO(date).toFormat('EEE')
const formatDayNumber = (date) => DateTime.fromISO(date).toFormat('d')

const onDragEnd = (evt) => {
  // data-entry-id may be on evt.item directly, or on a descendant
  const entryId = evt.item?.dataset?.entryId
    || evt.item?.querySelector?.('[data-entry-id]')?.dataset?.entryId

  const targetDate = evt.to?.closest('[data-date]')?.dataset?.date
  const targetSlot = evt.to?.closest('[data-slot]')?.dataset?.slot

  if (!entryId || !targetDate || !targetSlot) return

  const fromDate = evt.from?.closest('[data-date]')?.dataset?.date
  const fromSlot = evt.from?.closest('[data-slot]')?.dataset?.slot
  if (targetDate === fromDate && targetSlot === fromSlot) return

  mealsStore.moveEntry(entryId, targetDate, targetSlot)
}
</script>
