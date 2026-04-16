<template>
  <div class="flex flex-col gap-1.5 min-w-0">
    <!-- Day header -->
    <div
      class="text-center py-2 px-1 rounded-xl mb-0.5"
      :class="isToday ? 'bg-[#C4975A]/10' : ''"
    >
      <p class="text-[11px] font-semibold uppercase tracking-wider" :class="isToday ? 'text-[#C4975A]' : 'text-[#9C9895]'">
        {{ dayLabel }}
      </p>
      <p class="text-lg font-bold leading-tight" :class="isToday ? 'text-[#C4975A]' : 'text-[#1C1C1E] dark:text-[#F0EDE9]'">
        {{ dayNumber }}
      </p>
    </div>

    <!-- Meal slots -->
    <div
      v-for="slot in slots"
      :key="slot.key"
      class="flex flex-col gap-1"
      :data-date="date"
      :data-slot="slot.key"
    >
      <!-- Slot label -->
      <div class="flex items-center gap-1.5 px-1 pt-1">
        <component :is="slot.icon" class="w-3.5 h-3.5 text-[#9C9895]" />
        <span class="text-[11px] font-semibold uppercase tracking-wider text-[#9C9895]">{{ slot.label }}</span>
      </div>

      <!-- Drop zone + entries -->
      <VueDraggable
        v-model="localEntries[slot.key]"
        group="meal-entries"
        :animation="200"
        ghost-class="opacity-20"
        chosen-class="ring-2 ring-[#C4975A]/50"
        force-fallback
        fallback-class="shadow-xl opacity-90"
        class="min-h-[36px] flex flex-col gap-1 rounded-[10px] border border-dashed border-[#E8E4DF] dark:border-[#2E2E32] p-1 transition-colors"
        :class="{ 'border-[#C4975A]/40 bg-[#C4975A]/5': isDraggingOver }"
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
        class="flex items-center justify-center gap-1 py-1 px-2 text-xs text-[#9C9895] hover:text-[#C4975A] hover:bg-[#C4975A]/5 rounded-lg transition-colors"
        @click="$emit('add-entry', date, slot.key)"
      >
        <PlusIcon class="w-3.5 h-3.5" />
        Add
      </button>
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

const dt = computed(() => DateTime.fromISO(props.date))
const dayLabel = computed(() => dt.value.toFormat('EEE'))
const dayNumber = computed(() => dt.value.toFormat('d'))
const isToday = computed(() => dt.value.hasSame(DateTime.now(), 'day'))
const isDraggingOver = ref(false)

// Local copies of entries per slot for VueDraggable v-model
const localEntries = ref({
  breakfast: [],
  lunch: [],
  dinner: [],
  snack: [],
})

// Sync from prop
watch(() => props.entries, (val) => {
  localEntries.value = {
    breakfast: [...(val?.breakfast || [])],
    lunch: [...(val?.lunch || [])],
    dinner: [...(val?.dinner || [])],
    snack: [...(val?.snack || [])],
  }
}, { immediate: true, deep: true })

const onDragEnd = (evt) => {
  const toEl = evt.to
  const targetDate = toEl.closest('[data-date]')?.dataset?.date
  const targetSlot = toEl.closest('[data-slot]')?.dataset?.slot
  const entryId = evt.item?.dataset?.entryId

  if (!entryId || !targetDate || !targetSlot) return
  if (targetDate === props.date && targetSlot === evt.from?.closest('[data-slot]')?.dataset?.slot) return

  mealsStore.moveEntry(entryId, targetDate, targetSlot)
}
</script>
