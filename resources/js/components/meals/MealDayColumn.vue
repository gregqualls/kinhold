<template>
  <div class="flex flex-col gap-1 min-w-0">
    <!-- Day header -->
    <div
      class="text-center py-1.5 px-1 rounded-lg mb-1"
      :class="isToday ? 'bg-[#C4975A]/10' : ''"
    >
      <p class="text-xs font-medium uppercase tracking-wide" :class="isToday ? 'text-[#C4975A]' : 'text-lavender-500 dark:text-lavender-400'">
        {{ dayLabel }}
      </p>
      <p class="text-base font-bold" :class="isToday ? 'text-[#C4975A]' : 'text-prussian-500 dark:text-lavender-200'">
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
      <div class="flex items-center gap-1 px-1">
        <span class="text-sm">{{ slot.emoji }}</span>
        <span class="text-xs text-lavender-400 dark:text-lavender-500 font-medium">{{ slot.label }}</span>
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
        class="min-h-[40px] flex flex-col gap-1 rounded-lg border border-dashed border-lavender-200 dark:border-prussian-700 p-1 transition-colors"
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
        class="flex items-center justify-center gap-1 py-1 px-2 text-xs text-lavender-400 dark:text-lavender-500 hover:text-[#C4975A] hover:bg-[#C4975A]/5 rounded-lg transition-colors"
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
import { PlusIcon } from '@heroicons/vue/24/outline'
import MealEntryCard from './MealEntryCard.vue'
import { useMealsStore } from '@/stores/meals'

const props = defineProps({
  date: { type: String, required: true },
  entries: { type: Object, default: () => ({ breakfast: [], lunch: [], dinner: [], snack: [] }) },
})

const emit = defineEmits(['add-entry', 'entry-click', 'entry-delete'])

const mealsStore = useMealsStore()

const slots = [
  { key: 'breakfast', label: 'Breakfast', emoji: '🌅' },
  { key: 'lunch', label: 'Lunch', emoji: '☀️' },
  { key: 'dinner', label: 'Dinner', emoji: '🌙' },
  { key: 'snack', label: 'Snack', emoji: '🍎' },
]

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
