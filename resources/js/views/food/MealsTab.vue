<template>
  <div class="h-full flex flex-col overflow-hidden">
    <!-- Week navigation header -->
    <div class="px-4 md:px-6 pt-4 pb-3 flex items-center gap-2 border-b border-lavender-200 dark:border-prussian-700">
      <button
        class="p-2 rounded-[10px] bg-lavender-100 dark:bg-prussian-700 text-lavender-600 dark:text-lavender-400 hover:bg-lavender-200 dark:hover:bg-prussian-600 transition-colors"
        @click="mealsStore.goToPreviousWeek"
      >
        <ChevronLeftIcon class="w-4 h-4" />
      </button>

      <div class="flex-1 text-center">
        <p class="text-sm font-semibold text-prussian-500 dark:text-lavender-200">{{ weekRangeLabel }}</p>
      </div>

      <button
        class="p-2 rounded-[10px] bg-lavender-100 dark:bg-prussian-700 text-lavender-600 dark:text-lavender-400 hover:bg-lavender-200 dark:hover:bg-prussian-600 transition-colors"
        @click="mealsStore.goToNextWeek"
      >
        <ChevronRightIcon class="w-4 h-4" />
      </button>

      <button
        class="px-3 py-1.5 text-xs font-medium rounded-full transition-colors whitespace-nowrap"
        :class="mealsStore.isCurrentWeek
          ? 'bg-[#C4975A] text-white'
          : 'bg-lavender-100 dark:bg-prussian-700 text-lavender-600 dark:text-lavender-400 hover:bg-lavender-200 dark:hover:bg-prussian-600'"
        @click="mealsStore.goToCurrentWeek"
      >
        This Week
      </button>

      <!-- Generate shopping list -->
      <button
        v-if="mealsStore.currentPlan"
        class="p-2 rounded-[10px] bg-lavender-100 dark:bg-prussian-700 text-lavender-600 dark:text-lavender-400 hover:bg-lavender-200 dark:hover:bg-prussian-600 transition-colors"
        title="Regenerate shopping list from meal plan"
        @click="generateShoppingList"
      >
        <ShoppingCartIcon class="w-4 h-4" />
      </button>
    </div>

    <!-- Loading state -->
    <div v-if="mealsStore.isLoading" class="flex-1 flex items-center justify-center">
      <LoadingSpinner size="lg" />
    </div>

    <!-- No plan yet -->
    <EmptyState
      v-else-if="!mealsStore.currentPlan"
      :icon="CalendarDaysIcon"
      title="No meal plan yet"
      description="Start planning your week by adding meals to each day."
      action-text="Plan this week"
      @action="mealsStore.fetchCurrentWeekPlan"
    />

    <!-- Desktop: 7-column grid with horizontal scroll fallback -->
    <div
      v-else
      class="hidden md:block flex-1 overflow-y-auto overflow-x-auto px-4 md:px-6 py-3"
    >
      <div
        class="grid gap-3 min-w-max"
        :style="{ gridTemplateColumns: 'repeat(7, minmax(160px, 1fr))' }"
      >
        <MealDayColumn
          v-for="date in mealsStore.weekDates"
          :key="date"
          :date="date"
          :entries="mealsStore.entriesByDayAndSlot[date] || {}"
          @add-entry="openEntryPicker"
          @entry-click="openEntryEdit"
          @entry-delete="deleteEntry"
        />
      </div>
    </div>

    <!-- Mobile: vertical day cards -->
    <div class="md:hidden flex-1 overflow-y-auto px-4 py-3 space-y-2 pb-24">
      <MealDayCard
        v-for="date in mealsStore.weekDates"
        :key="date"
        :date="date"
        :entries="mealsStore.entriesByDayAndSlot[date] || {}"
        @add-entry="openEntryPicker"
        @entry-click="openEntryEdit"
        @entry-delete="deleteEntry"
      />
    </div>

    <!-- Entry picker (add) -->
    <MealEntryPicker
      :show="showPicker"
      :target-date="pickerDate"
      :target-slot="pickerSlot"
      @close="showPicker = false"
      @added="onEntryAdded"
    />

    <!-- Entry edit panel -->
    <SlidePanel
      :show="!!editEntry"
      :title="editEntry?.display_title || 'Edit Entry'"
      @close="editEntry = null"
    >
      <div v-if="editEntry" class="p-6 space-y-4">
        <!-- Type badge -->
        <div class="flex items-center gap-2">
          <span
            class="px-2.5 py-1 rounded-full text-xs font-medium"
            :class="typeClasses(editEntry.type)"
          >
            {{ capitalize(editEntry.type) }}
          </span>
          <span class="text-sm text-lavender-500 dark:text-lavender-400">
            {{ slotLabel(editEntry.meal_slot) }} · {{ formatDate(editEntry.date) }}
          </span>
        </div>

        <!-- Servings -->
        <div>
          <label class="block text-xs font-medium text-lavender-400 dark:text-lavender-500 mb-1.5 uppercase tracking-wide">Servings</label>
          <input
            v-model.number="editServings"
            type="number"
            min="1"
            max="20"
            class="w-24 text-sm bg-lavender-50 dark:bg-prussian-700 border border-lavender-200 dark:border-prussian-600 rounded-[10px] px-3 py-2 text-prussian-500 dark:text-lavender-200 focus:outline-none focus:ring-1 focus:ring-[#C4975A]/30 focus:border-[#C4975A] transition-colors"
          />
        </div>

        <!-- Cooks -->
        <div v-if="familyMembers.length > 0">
          <label class="block text-xs font-medium text-lavender-400 dark:text-lavender-500 mb-2 uppercase tracking-wide">Assigned Cooks</label>
          <div class="flex flex-wrap gap-2">
            <button
              v-for="member in familyMembers"
              :key="member.id"
              class="flex items-center gap-1.5 px-2 py-1.5 rounded-full border text-xs font-medium transition-colors"
              :class="editCooks.includes(member.id)
                ? 'bg-[#C4975A]/10 border-[#C4975A]/40 text-[#C4975A]'
                : 'bg-lavender-50 dark:bg-prussian-700 border-lavender-200 dark:border-prussian-600 text-lavender-500 dark:text-lavender-400 hover:border-[#C4975A]/30'"
              @click="toggleEditCook(member.id)"
            >
              <UserAvatar :user="member" size="xs" />
              {{ member.name.split(' ')[0] }}
            </button>
          </div>
        </div>

        <!-- Notes -->
        <div>
          <label class="block text-xs font-medium text-lavender-400 dark:text-lavender-500 mb-1.5 uppercase tracking-wide">Notes</label>
          <textarea
            v-model="editNotes"
            rows="2"
            class="w-full text-sm bg-lavender-50 dark:bg-prussian-700 border border-lavender-200 dark:border-prussian-600 rounded-[10px] px-3 py-2 text-prussian-500 dark:text-lavender-200 placeholder-lavender-400 focus:outline-none focus:ring-1 focus:ring-[#C4975A]/30 focus:border-[#C4975A] transition-colors resize-none"
            placeholder="Any notes..."
          />
        </div>

        <!-- Save -->
        <BaseButton variant="primary" :loading="isEditSaving" class="w-full" @click="saveEdit">
          Save Changes
        </BaseButton>

        <!-- Delete -->
        <button
          class="flex items-center gap-2 text-sm text-red-500 hover:text-red-600 transition-colors w-full justify-center pt-1"
          @click="deleteEntry(editEntry)"
        >
          <TrashIcon class="w-4 h-4" />
          Remove from plan
        </button>
      </div>
    </SlidePanel>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { DateTime } from 'luxon'
import {
  ChevronLeftIcon,
  ChevronRightIcon,
  CalendarDaysIcon,
  ShoppingCartIcon,
  TrashIcon,
} from '@heroicons/vue/24/outline'
import { useMealsStore } from '@/stores/meals'
import { useAuthStore } from '@/stores/auth'
import { useNotification } from '@/composables/useNotification'
import MealDayColumn from '@/components/meals/MealDayColumn.vue'
import MealDayCard from '@/components/meals/MealDayCard.vue'
import MealEntryPicker from '@/components/meals/MealEntryPicker.vue'
import SlidePanel from '@/components/common/SlidePanel.vue'
import BaseButton from '@/components/common/BaseButton.vue'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import EmptyState from '@/components/common/EmptyState.vue'
import UserAvatar from '@/components/common/UserAvatar.vue'

const mealsStore = useMealsStore()
const authStore = useAuthStore()
const { success: notifySuccess, error: notifyError } = useNotification()

const familyMembers = computed(() => authStore.familyMembers || [])

// Week range label
const weekRangeLabel = computed(() => {
  const dates = mealsStore.weekDates
  if (!dates.length) return ''
  const start = DateTime.fromISO(dates[0])
  const end = DateTime.fromISO(dates[dates.length - 1])
  if (start.year === end.year) {
    return `${start.toFormat('MMM d')} – ${end.toFormat('MMM d, yyyy')}`
  }
  return `${start.toFormat('MMM d, yyyy')} – ${end.toFormat('MMM d, yyyy')}`
})

// Entry picker
const showPicker = ref(false)
const pickerDate = ref(null)
const pickerSlot = ref(null)

const openEntryPicker = (date, slot) => {
  pickerDate.value = date
  pickerSlot.value = slot
  showPicker.value = true
}

const onEntryAdded = () => {
  notifySuccess('Meal added to plan', 3000)
}

// Entry edit
const editEntry = ref(null)
const editServings = ref(null)
const editCooks = ref([])
const editNotes = ref('')
const isEditSaving = ref(false)

const openEntryEdit = (entry) => {
  editEntry.value = entry
  editServings.value = entry.servings || entry.effective_servings || null
  editCooks.value = [...(entry.assigned_cooks || [])]
  editNotes.value = entry.notes || ''
}

const toggleEditCook = (userId) => {
  const idx = editCooks.value.indexOf(userId)
  if (idx === -1) {
    editCooks.value.push(userId)
  } else {
    editCooks.value.splice(idx, 1)
  }
}

const saveEdit = async () => {
  if (!editEntry.value) return
  isEditSaving.value = true
  const result = await mealsStore.updateEntry(editEntry.value.id, {
    servings: editServings.value || null,
    assigned_cooks: editCooks.value,
    notes: editNotes.value.trim() || null,
  })
  isEditSaving.value = false
  if (result.success) {
    editEntry.value = null
    notifySuccess('Entry updated', 3000)
  } else {
    notifyError(result.error || 'Failed to update entry', 4000)
  }
}

const deleteEntry = async (entry) => {
  editEntry.value = null
  const result = await mealsStore.removeEntry(entry.id)
  if (result.success) {
    notifySuccess('Meal removed', 3000)
  } else {
    notifyError(result.error || 'Failed to remove meal', 4000)
  }
}

// Shopping list
const generateShoppingList = async () => {
  const result = await mealsStore.generateShoppingList(mealsStore.currentPlan.id)
  if (result.success) {
    notifySuccess('Shopping list updated', 3000)
  } else {
    notifyError(result.error || 'Failed to update shopping list', 4000)
  }
}

// Helpers
const slotLabel = (slot) => {
  const map = { breakfast: 'Breakfast', lunch: 'Lunch', dinner: 'Dinner', snack: 'Snack' }
  return map[slot] || slot
}

const capitalize = (str) => str ? str.charAt(0).toUpperCase() + str.slice(1) : ''

const formatDate = (dateStr) => {
  try {
    return DateTime.fromISO(dateStr).toFormat('EEE, MMM d')
  } catch {
    return dateStr
  }
}

const typeClasses = (type) => {
  switch (type) {
    case 'recipe': return 'bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400'
    case 'restaurant': return 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400'
    case 'preset': return 'bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400'
    default: return 'bg-lavender-100 dark:bg-prussian-700 text-lavender-600 dark:text-lavender-400'
  }
}

onMounted(async () => {
  await mealsStore.fetchCurrentWeekPlan()
  mealsStore.fetchPresets()
})
</script>
