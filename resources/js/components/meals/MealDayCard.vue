<template>
  <div
    class="bg-white dark:bg-prussian-800 border rounded-xl overflow-hidden transition-colors"
    :class="isToday ? 'border-[#C4975A]/40' : 'border-lavender-200 dark:border-prussian-700'"
  >
    <!-- Day header (toggle) -->
    <button
      class="w-full flex items-center justify-between px-4 py-3"
      :class="isToday ? 'bg-[#C4975A]/5' : 'hover:bg-lavender-50 dark:hover:bg-prussian-700'"
      @click="isExpanded = !isExpanded"
    >
      <div class="flex items-center gap-3">
        <div class="text-left">
          <p class="text-xs font-medium uppercase tracking-wide" :class="isToday ? 'text-[#C4975A]' : 'text-lavender-500 dark:text-lavender-400'">
            {{ dayLabel }}
          </p>
          <p class="text-lg font-bold leading-tight" :class="isToday ? 'text-[#C4975A]' : 'text-prussian-500 dark:text-lavender-200'">
            {{ dayNumber }}
          </p>
        </div>
        <span v-if="totalEntries > 0" class="text-xs bg-lavender-100 dark:bg-prussian-700 text-lavender-500 dark:text-lavender-400 px-2 py-0.5 rounded-full">
          {{ totalEntries }} meal{{ totalEntries !== 1 ? 's' : '' }}
        </span>
      </div>
      <ChevronDownIcon
        class="w-4 h-4 text-lavender-400 transition-transform"
        :class="{ 'rotate-180': isExpanded }"
      />
    </button>

    <!-- Expanded content -->
    <div v-if="isExpanded" class="px-3 pb-3 space-y-2">
      <div
        v-for="slot in slots"
        :key="slot.key"
        :data-date="date"
        :data-slot="slot.key"
      >
        <!-- Slot label -->
        <div class="flex items-center gap-1 mt-2 mb-1">
          <span class="text-sm">{{ slot.emoji }}</span>
          <span class="text-xs font-medium text-lavender-400 dark:text-lavender-500">{{ slot.label }}</span>
        </div>

        <!-- Drop zone + entries -->
        <VueDraggable
          v-model="localEntries[slot.key]"
          group="meal-entries"
          :animation="200"
          ghost-class="opacity-20"
          chosen-class="ring-2 ring-[#C4975A]/50"
          force-fallback
          class="min-h-[36px] flex flex-col gap-1 rounded-lg border border-dashed border-lavender-200 dark:border-prussian-700 p-1"
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
          class="flex items-center gap-1 mt-1 py-1 px-2 text-xs text-lavender-400 dark:text-lavender-500 hover:text-[#C4975A] hover:bg-[#C4975A]/5 rounded-lg transition-colors w-full"
          @click="$emit('add-entry', date, slot.key)"
        >
          <PlusIcon class="w-3.5 h-3.5" />
          Add to {{ slot.label.toLowerCase() }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { DateTime } from 'luxon'
import { VueDraggable } from 'vue-draggable-plus'
import { ChevronDownIcon, PlusIcon } from '@heroicons/vue/24/outline'
import MealEntryCard from './MealEntryCard.vue'
import { useMealsStore } from '@/stores/meals'

const props = defineProps({
  date: { type: String, required: true },
  entries: { type: Object, default: () => ({ breakfast: [], lunch: [], dinner: [], snack: [] }) },
})

const emit = defineEmits(['add-entry', 'entry-click', 'entry-delete'])

const mealsStore = useMealsStore()

const dt = computed(() => DateTime.fromISO(props.date))
const dayLabel = computed(() => dt.value.toFormat('EEE, MMM d'))
const dayNumber = computed(() => dt.value.toFormat('d'))
const isToday = computed(() => dt.value.hasSame(DateTime.now(), 'day'))

const isExpanded = ref(isToday.value)

const slots = [
  { key: 'breakfast', label: 'Breakfast', emoji: '🌅' },
  { key: 'lunch', label: 'Lunch', emoji: '☀️' },
  { key: 'dinner', label: 'Dinner', emoji: '🌙' },
  { key: 'snack', label: 'Snack', emoji: '🍎' },
]

const totalEntries = computed(() =>
  slots.reduce((sum, s) => sum + (props.entries?.[s.key]?.length || 0), 0)
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
  const toEl = evt.to
  const targetDate = toEl.closest('[data-date]')?.dataset?.date
  const targetSlot = toEl.closest('[data-slot]')?.dataset?.slot
  const entryId = evt.item?.dataset?.entryId

  if (!entryId || !targetDate || !targetSlot) return

  mealsStore.moveEntry(entryId, targetDate, targetSlot)
}
</script>
