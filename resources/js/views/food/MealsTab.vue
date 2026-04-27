<template>
  <div class="h-full flex flex-col overflow-hidden">
    <!-- Week navigation header (desktop only — mobile has infinite scroll) -->
    <div class="hidden md:flex px-4 md:px-6 pt-4 pb-3 items-center gap-2 border-b border-border-subtle">
      <button
        class="p-2 rounded-[10px] bg-surface-sunken text-ink-secondary hover:bg-surface-overlay transition-colors"
        @click="mealsStore.goToPreviousWeek"
      >
        <ChevronLeftIcon class="w-4 h-4" />
      </button>

      <div class="flex-1 text-center">
        <p class="text-sm font-semibold text-ink-primary">{{ weekRangeLabel }}</p>
      </div>

      <button
        class="p-2 rounded-[10px] bg-surface-sunken text-ink-secondary hover:bg-surface-overlay transition-colors"
        @click="mealsStore.goToNextWeek"
      >
        <ChevronRightIcon class="w-4 h-4" />
      </button>

      <button
        class="px-3 py-1.5 text-xs font-medium rounded-full transition-colors whitespace-nowrap"
        :class="mealsStore.isCurrentWeek
          ? 'bg-[#C4975A] text-white'
          : 'bg-surface-sunken text-ink-secondary hover:bg-surface-overlay'"
        @click="mealsStore.goToCurrentWeek"
      >
        This Week
      </button>

      <!-- Add to shopping list (preview + select ingredients) -->
      <button
        v-if="mealsStore.currentPlan"
        class="p-2 rounded-[10px] bg-surface-sunken text-ink-secondary hover:bg-surface-overlay transition-colors"
        title="Add ingredients to shopping list"
        @click="showShoppingModal = true"
      >
        <ShoppingCartIcon class="w-4 h-4" />
      </button>
    </div>

    <!-- Loading state -->
    <div v-if="mealsStore.isLoading && !mobileEntries.size" class="flex-1 flex items-center justify-center">
      <LoadingSpinner size="lg" />
    </div>

    <!-- No plan yet -->
    <KinEmptyState
      v-else-if="!mealsStore.currentPlan && !mobileEntries.size"
      :icon="CalendarDaysIcon"
      title="No meal plan yet"
      description="Start planning your week by adding meals to each day."
      action-text="Plan this week"
      accent-color="peach"
      size="md"
      @action="mealsStore.fetchCurrentWeekPlan"
    />

    <template v-else>
      <!-- Desktop: transposed grid (slots = rows, days = columns) -->
      <div class="hidden md:block flex-1 overflow-y-auto overflow-x-hidden px-4 md:px-6 py-3">
        <MealWeekGrid
          :dates="mealsStore.weekDates"
          :entries-by-day="mealsStore.entriesByDayAndSlot"
          @add-entry="openEntryPicker"
          @entry-click="openEntryEdit"
          @entry-delete="deleteEntry"
        />
      </div>

      <!-- Mobile: continuous scroll from today -->
      <div
        ref="mobileScrollContainer"
        class="md:hidden flex-1 overflow-y-auto px-4 py-3 space-y-3 pb-24"
        @scroll="onMobileScroll"
      >
        <!-- Load previous days -->
        <button
          class="w-full py-2 px-3 text-xs font-medium text-[#9C9895] hover:text-[#C4975A] bg-[#F5F2EE] dark:bg-[#252528] hover:bg-[#C4975A]/10 rounded-lg transition-colors flex items-center justify-center gap-1.5"
          :disabled="isLoadingPrevious"
          @click="loadPreviousDays"
        >
          <ChevronUpIcon class="w-3.5 h-3.5" />
          {{ isLoadingPrevious ? 'Loading...' : 'Show earlier days' }}
        </button>

        <MealDaySection
          v-for="date in mobileDates"
          :key="date"
          :ref="el => setDayRef(date, el)"
          :date="date"
          :entries="getMobileEntries(date)"
          @add-entry="openEntryPicker"
          @entry-click="openEntryEdit"
          @entry-delete="deleteEntry"
        />
        <div v-if="isLoadingMore" class="flex justify-center py-4">
          <LoadingSpinner size="sm" />
        </div>
      </div>
    </template>

    <!-- Entry picker (add) -->
    <MealEntryPicker
      :show="showPicker"
      :target-date="pickerDate"
      :target-slot="pickerSlot"
      @close="showPicker = false"
      @added="onEntryAdded"
    />

    <!-- Shopping list builder (preview + select ingredients) -->
    <MealPlanShoppingModal
      :show="showShoppingModal"
      :plan-id="mealsStore.currentPlan?.id"
      @close="showShoppingModal = false"
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
          <span class="text-sm text-ink-tertiary">
            {{ slotLabel(editEntry.meal_slot) }} · {{ formatDate(editEntry.date) }}
          </span>
        </div>

        <!-- Restaurant links -->
        <div v-if="editEntry.type === 'restaurant' && editEntry.restaurant" class="space-y-2">
          <a
            v-if="editEntry.restaurant.google_maps_url"
            :href="editEntry.restaurant.google_maps_url"
            target="_blank"
            rel="noopener"
            class="flex items-center gap-2 px-3 py-2.5 rounded-[10px] bg-surface-sunken text-sm text-ink-primary hover:bg-surface-overlay transition-colors"
          >
            <MapPinIcon class="w-4 h-4 text-[#5B7B9C] flex-shrink-0" />
            <span class="flex-1 truncate">Open in Google Maps</span>
            <ArrowTopRightOnSquareIcon class="w-3.5 h-3.5 text-[#9C9895] flex-shrink-0" />
          </a>
          <a
            v-if="editEntry.restaurant.phone"
            :href="'tel:' + editEntry.restaurant.phone"
            class="flex items-center gap-2 px-3 py-2.5 rounded-[10px] bg-surface-sunken text-sm text-ink-primary hover:bg-surface-overlay transition-colors"
          >
            <PhoneIcon class="w-4 h-4 text-[#5B7B9C] flex-shrink-0" />
            <span>{{ editEntry.restaurant.phone }}</span>
          </a>
        </div>

        <!-- Servings -->
        <div>
          <label class="block text-xs font-medium text-ink-tertiary mb-1.5 uppercase tracking-wide">Servings</label>
          <KinInput
            v-model.number="editServings"
            type="number"
            :min="1"
            :max="20"
            class="w-24"
          />
        </div>

        <!-- Cooks -->
        <div v-if="familyMembers.length > 0">
          <label class="block text-xs font-medium text-ink-tertiary mb-2 uppercase tracking-wide">Assigned Cooks</label>
          <div class="flex flex-wrap gap-2">
            <KinChip
              v-for="member in familyMembers"
              :key="member.id"
              variant="filter"
              size="sm"
              :custom-color="'#C4975A'"
              :active="editCooks.includes(member.id)"
              @click="toggleEditCook(member.id)"
            >
              <UserAvatar :user="member" size="xs" />
              {{ member.name.split(' ')[0] }}
            </KinChip>
          </div>
        </div>

        <!-- Notes -->
        <div>
          <label class="block text-xs font-medium text-ink-tertiary mb-1.5 uppercase tracking-wide">Notes</label>
          <KinTextarea
            v-model="editNotes"
            :rows="2"
            placeholder="Any notes..."
          />
        </div>

        <!-- Save -->
        <KinButton variant="primary" size="sm" :loading="isEditSaving" class="w-full" @click="saveEdit">
          Save Changes
        </KinButton>

        <!-- Delete -->
        <button
          class="flex items-center gap-2 text-sm text-status-failed hover:text-status-failed transition-colors w-full justify-center pt-1"
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
import { ref, computed, onMounted, nextTick } from 'vue'
import { DateTime } from 'luxon'
import {
  ChevronLeftIcon,
  ChevronRightIcon,
  ChevronUpIcon,
  CalendarDaysIcon,
  ShoppingCartIcon,
  TrashIcon,
  MapPinIcon,
  PhoneIcon,
  ArrowTopRightOnSquareIcon,
} from '@heroicons/vue/24/outline'
import { useMealsStore } from '@/stores/meals'
import { useAuthStore } from '@/stores/auth'
import { useNotification } from '@/composables/useNotification'
import MealWeekGrid from '@/components/meals/MealWeekGrid.vue'
import MealDaySection from '@/components/meals/MealDaySection.vue'
import MealEntryPicker from '@/components/meals/MealEntryPicker.vue'
import MealPlanShoppingModal from '@/components/meals/MealPlanShoppingModal.vue'
import SlidePanel from '@/components/common/SlidePanel.vue'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import UserAvatar from '@/components/common/UserAvatar.vue'
import KinButton from '@/components/design-system/KinButton.vue'
import KinEmptyState from '@/components/design-system/KinEmptyState.vue'
import KinInput from '@/components/design-system/KinInput.vue'
import KinTextarea from '@/components/design-system/KinTextarea.vue'
import KinChip from '@/components/design-system/KinChip.vue'

const mealsStore = useMealsStore()
const authStore = useAuthStore()
const { success: notifySuccess, error: notifyError } = useNotification()

const familyMembers = computed(() => authStore.familyMembers || [])

// ── Week range label (desktop) ──
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

// ── Mobile: continuous scroll ──
const mobileScrollContainer = ref(null)
const dayRefs = ref({})
const mobileEntries = ref(new Map()) // Map<weekStart, plan>
const mobileDates = ref([])
const isLoadingMore = ref(false)
const loadedWeekStarts = ref(new Set())
const todayStr = DateTime.now().toISODate()

const setDayRef = (date, el) => {
  if (el) dayRefs.value[date] = el
}

const getMobileEntries = (date) => {
  // Search all loaded plans for entries on this date
  for (const plan of mobileEntries.value.values()) {
    if (plan.entries) {
      const dayEntries = { breakfast: [], lunch: [], dinner: [], snack: [] }
      for (const entry of plan.entries) {
        if (entry.date === date && dayEntries[entry.meal_slot]) {
          dayEntries[entry.meal_slot].push(entry)
        }
      }
      // Return if this plan has any entries for this date
      if (Object.values(dayEntries).some(arr => arr.length > 0)) return dayEntries
    }
  }
  return { breakfast: [], lunch: [], dinner: [], snack: [] }
}

const getWeekStartForDate = (dateStr) => {
  const dt = DateTime.fromISO(dateStr)
  const weekStartDay = authStore.family?.settings?.week_start_day || 'monday'
  if (weekStartDay === 'sunday') {
    const weekday = dt.weekday
    return dt.minus({ days: weekday % 7 }).startOf('day')
  }
  return dt.startOf('week')
}

const loadWeek = async (weekStart) => {
  const key = weekStart.toISODate()
  if (loadedWeekStarts.value.has(key)) return
  loadedWeekStarts.value.add(key)

  const result = await mealsStore.fetchWeekPlanRaw(key)
  if (result.success && result.plan) {
    mobileEntries.value.set(key, result.plan)
  }
}

// Earliest date the user wants visible. Defaults to today so past days from the
// current week are hidden until they tap "Show earlier days".
const mobileStartDate = ref(todayStr)
const isLoadingPrevious = ref(false)

const buildMobileDates = () => {
  // Sort all loaded week starts and build a flat date list, filtering out
  // anything before mobileStartDate so we don't render past days by default.
  const sortedWeeks = Array.from(loadedWeekStarts.value).sort()
  const dates = []
  for (const weekStart of sortedWeeks) {
    const start = DateTime.fromISO(weekStart)
    for (let i = 0; i < 7; i++) {
      const d = start.plus({ days: i }).toISODate()
      if (d >= mobileStartDate.value) dates.push(d)
    }
  }
  mobileDates.value = dates
}

// Reveal the previous 7 days. If those days fall in a week we haven't fetched
// yet, load it first. Preserves scroll position so today doesn't jump.
const loadPreviousDays = async () => {
  if (isLoadingPrevious.value) return
  isLoadingPrevious.value = true

  const newStart = DateTime.fromISO(mobileStartDate.value).minus({ days: 7 }).toISODate()
  const neededWeekStart = getWeekStartForDate(newStart)
  await loadWeek(neededWeekStart)

  // Capture scroll height before mutating dates so we can compensate.
  const el = mobileScrollContainer.value
  const prevHeight = el?.scrollHeight ?? 0

  mobileStartDate.value = newStart
  buildMobileDates()

  await nextTick()
  if (el) {
    const delta = el.scrollHeight - prevHeight
    el.scrollTop += delta
  }
  isLoadingPrevious.value = false
}

const loadNextWeek = async () => {
  if (isLoadingMore.value) return
  isLoadingMore.value = true

  const lastDate = mobileDates.value[mobileDates.value.length - 1]
  const nextWeekStart = lastDate
    ? getWeekStartForDate(DateTime.fromISO(lastDate).plus({ days: 1 }).toISODate())
    : getWeekStartForDate(todayStr)

  await loadWeek(nextWeekStart)
  buildMobileDates()
  isLoadingMore.value = false
}

const onMobileScroll = (e) => {
  const el = e.target
  if (el.scrollHeight - el.scrollTop - el.clientHeight < 300) {
    loadNextWeek()
  }
}

const scrollToToday = () => {
  nextTick(() => {
    const todayEl = dayRefs.value[todayStr]
    if (todayEl?.$el) {
      todayEl.$el.scrollIntoView({ behavior: 'smooth', block: 'start' })
    }
  })
}

// ── Entry picker ──
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

// ── Entry edit ──
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

// ── Shopping list ──
const showShoppingModal = ref(false)

// ── Helpers ──
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
    default: return 'bg-surface-sunken text-ink-secondary'
  }
}

// ── Init ──
onMounted(async () => {
  await mealsStore.fetchCurrentWeekPlan()
  mealsStore.fetchPresets()

  // Initialize mobile scroll with current week + next week
  if (mealsStore.currentPlan) {
    const currentWeekStart = getWeekStartForDate(todayStr)
    mobileEntries.value.set(currentWeekStart.toISODate(), mealsStore.currentPlan)
    loadedWeekStarts.value.add(currentWeekStart.toISODate())
    buildMobileDates()

    // Auto-scroll to today
    scrollToToday()
  }
})
</script>
