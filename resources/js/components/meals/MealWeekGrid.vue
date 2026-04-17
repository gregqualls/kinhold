<template>
  <div class="overflow-x-auto">
    <div
      class="grid gap-px bg-lavender-200/50 dark:bg-prussian-700/50 rounded-xl overflow-hidden"
      :style="gridStyle"
    >
      <!-- Header row: corner cell + day headers -->
      <div class="bg-white dark:bg-[#1C1C20] p-2" />
      <div
        v-for="date in dates"
        :key="'h-' + date"
        class="bg-white dark:bg-[#1C1C20] text-center py-2 px-1"
        :class="isToday(date) ? 'bg-[#C4975A]/5' : ''"
      >
        <p
          class="text-[11px] font-semibold uppercase tracking-wider"
          :class="isToday(date) ? 'text-[#C4975A]' : 'text-[#9C9895]'"
        >
          {{ formatDay(date) }}
        </p>
        <p
          class="text-lg font-bold leading-tight"
          :class="isToday(date) ? 'text-[#C4975A]' : 'text-[#1C1C1E] dark:text-[#F0EDE9]'"
        >
          {{ formatDayNumber(date) }}
        </p>
      </div>

      <!-- Slot rows -->
      <template v-for="slot in slots" :key="slot.key">
        <!-- Slot label (sticky left) -->
        <div class="bg-white dark:bg-[#1C1C20] flex items-start gap-1.5 px-3 py-3 sticky left-0 z-10">
          <component :is="slot.icon" class="w-4 h-4 text-[#9C9895] mt-0.5" />
          <span class="text-xs font-semibold uppercase tracking-wider text-[#9C9895] whitespace-nowrap">
            {{ slot.label }}
          </span>
        </div>

        <!-- Day cells for this slot -->
        <div
          v-for="date in dates"
          :key="slot.key + '-' + date"
          class="bg-white dark:bg-[#1C1C20] p-1.5"
          :class="isToday(date) ? 'bg-[#C4975A]/[0.02]' : ''"
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
import { ref, computed, watch } from 'vue'
import { DateTime } from 'luxon'
import { VueDraggable } from 'vue-draggable-plus'
import { PlusIcon, SunIcon, CloudIcon, MoonIcon, CakeIcon } from '@heroicons/vue/24/outline'
import MealEntryCard from './MealEntryCard.vue'
import { useMealsStore } from '@/stores/meals'

const props = defineProps({
  dates: { type: Array, required: true },
  entriesByDay: { type: Object, required: true },
})

const emit = defineEmits(['add-entry', 'entry-click', 'entry-delete'])

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

const gridStyle = computed(() => ({
  gridTemplateColumns: `120px repeat(${props.dates.length}, minmax(140px, 1fr))`,
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
const formatDay = (date) => DateTime.fromISO(date).toFormat('EEE')
const formatDayNumber = (date) => DateTime.fromISO(date).toFormat('d')

const onDragEnd = (evt) => {
  // data-entry-id may be on evt.item directly, or on a descendant
  const entryId = evt.item?.dataset?.entryId
    || evt.item?.querySelector?.('[data-entry-id]')?.dataset?.entryId

  const targetDate = evt.to?.closest('[data-date]')?.dataset?.date
  const targetSlot = evt.to?.closest('[data-slot]')?.dataset?.slot

  if (!entryId || !targetDate || !targetSlot) {
    console.warn('Drag cancelled: missing data', { entryId, targetDate, targetSlot, item: evt.item })
    return
  }

  const fromDate = evt.from?.closest('[data-date]')?.dataset?.date
  const fromSlot = evt.from?.closest('[data-slot]')?.dataset?.slot
  if (targetDate === fromDate && targetSlot === fromSlot) return

  mealsStore.moveEntry(entryId, targetDate, targetSlot)
}
</script>
