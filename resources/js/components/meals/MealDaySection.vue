<template>
  <div
    class="bg-white dark:bg-[#1C1C20] border rounded-xl overflow-hidden"
    :class="[
      isToday ? 'border-[#C4975A]/40' : 'border-[#E8E4DF] dark:border-[#2E2E32]',
      isPast && !isToday ? 'opacity-55 hover:opacity-100 transition-opacity' : '',
    ]"
  >
    <!-- Day header -->
    <div
      class="flex items-center justify-between px-4 py-3"
      :class="[
        isToday ? 'bg-[#C4975A]/5' : '',
        isPast && !isToday ? 'bg-[#F5F2EE]/40 dark:bg-[#252528]/40' : '',
      ]"
    >
      <div class="flex items-center gap-2">
        <p
          class="text-sm font-semibold"
          :class="isToday
            ? 'text-[#C4975A]'
            : isPast
              ? 'text-[#9C9895]'
              : 'text-[#1C1C1E] dark:text-[#F0EDE9]'"
        >
          {{ dayLabel }}
        </p>
        <span
          v-if="isToday"
          class="text-[10px] font-bold uppercase tracking-wider bg-[#C4975A] text-white px-1.5 py-0.5 rounded-full"
        >
          Today
        </span>
      </div>
      <span v-if="totalEntries > 0" class="text-xs bg-[#F5F2EE] dark:bg-[#252528] text-[#6B6966] dark:text-[#9C9895] px-2 py-0.5 rounded-full">
        {{ totalEntries }} meal{{ totalEntries !== 1 ? 's' : '' }}
      </span>
    </div>

    <!-- Meal slots (always expanded) -->
    <div class="px-4 pb-4 space-y-3">
      <div
        v-for="slot in slots"
        :key="slot.key"
        :data-date="date"
        :data-slot="slot.key"
      >
        <!-- Slot label -->
        <div class="flex items-center gap-1.5 mt-2 mb-1.5">
          <component :is="slot.icon" class="w-3.5 h-3.5 text-[#9C9895]" />
          <span class="text-[11px] font-semibold uppercase tracking-wider text-[#9C9895]">{{ slot.label }}</span>
        </div>

        <!-- Drop zone + entries -->
        <VueDraggable
          v-model="localEntries[slot.key]"
          group="meal-entries"
          :animation="200"
          ghost-class="opacity-20"
          chosen-class="meal-drag-chosen"
          class="min-h-[36px] flex flex-col gap-1 rounded-[10px] border border-dashed border-[#E8E4DF] dark:border-[#2E2E32] p-1"
          @end="onDragEnd"
        >
          <MealEntryCard
            v-for="entry in localEntries[slot.key]"
            :key="entry.id"
            :entry="entry"
            @click="$emit('entry-click', entry)"
            @delete="$emit('entry-delete', entry)"
          />
        </VueDraggable>

        <!-- Add button -->
        <button
          class="flex items-center gap-1 mt-1 py-1.5 px-2 text-xs text-[#9C9895] hover:text-[#C4975A] hover:bg-[#C4975A]/5 rounded-lg transition-colors w-full"
          @click="$emit('add-entry', date, slot.key)"
        >
          <PlusIcon class="w-3.5 h-3.5" />
          Add
        </button>
      </div>
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
  date: { type: String, required: true },
  entries: { type: Object, default: () => ({ breakfast: [], lunch: [], dinner: [], snack: [] }) },
})

defineEmits(['add-entry', 'entry-click', 'entry-delete'])

const mealsStore = useMealsStore()

const dt = computed(() => DateTime.fromISO(props.date))
const dayLabel = computed(() => dt.value.toFormat('EEEE, MMM d'))
const isToday = computed(() => dt.value.hasSame(DateTime.now(), 'day'))
const isPast = computed(() => dt.value.startOf('day') < DateTime.now().startOf('day'))

const ALL_SLOTS = [
  { key: 'breakfast', label: 'Breakfast', icon: SunIcon },
  { key: 'lunch', label: 'Lunch', icon: CloudIcon },
  { key: 'dinner', label: 'Dinner', icon: MoonIcon },
  { key: 'snack', label: 'Snack', icon: CakeIcon },
]

const slots = computed(() =>
  ALL_SLOTS.filter(s => mealsStore.enabledMealSlots.includes(s.key))
)

const totalEntries = computed(() =>
  slots.value.reduce((sum, s) => sum + (props.entries?.[s.key]?.length || 0), 0)
)

const localEntries = ref({
  breakfast: [],
  lunch: [],
  dinner: [],
  snack: [],
})

watch(() => props.entries, (val) => {
  localEntries.value = {
    breakfast: [...(val?.breakfast || [])],
    lunch: [...(val?.lunch || [])],
    dinner: [...(val?.dinner || [])],
    snack: [...(val?.snack || [])],
  }
}, { immediate: true, deep: true })

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
