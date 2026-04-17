<template>
  <Teleport to="body">
    <Transition name="fade">
      <div
        v-if="show"
        class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
        @click.self="$emit('close')"
      >
        <div class="bg-white dark:bg-prussian-800 rounded-xl shadow-xl w-full max-w-lg max-h-[85vh] flex flex-col">
          <!-- Header -->
          <div class="flex items-center justify-between px-6 pt-5 pb-3 border-b border-lavender-200 dark:border-prussian-700">
            <h3 class="text-lg font-bold font-heading text-prussian-500 dark:text-lavender-200">Add to Shopping List</h3>
            <button
              class="p-1 rounded text-lavender-400 hover:text-prussian-500 dark:hover:text-lavender-200 transition-colors"
              aria-label="Close"
              @click="$emit('close')"
            >
              <XMarkIcon class="w-5 h-5" />
            </button>
          </div>

          <!-- Controls -->
          <div class="px-6 pt-4 pb-3 border-b border-lavender-200 dark:border-prussian-700 space-y-4">
            <!-- Days picker -->
            <div>
              <label class="block text-xs font-medium text-lavender-400 dark:text-lavender-500 mb-2 uppercase tracking-wide">
                Shop for the next
              </label>
              <div class="flex flex-wrap gap-2">
                <button
                  v-for="opt in dayOptions"
                  :key="opt.value"
                  class="px-3 py-1.5 text-xs font-medium rounded-full transition-colors"
                  :class="days === opt.value
                    ? 'bg-[#C4975A] text-white'
                    : 'bg-lavender-100 dark:bg-prussian-700 text-lavender-600 dark:text-lavender-400 hover:bg-lavender-200 dark:hover:bg-prussian-600'"
                  @click="days = opt.value"
                >
                  {{ opt.label }}
                </button>
              </div>
              <p v-if="rangeLabel" class="text-xs text-lavender-400 dark:text-lavender-500 mt-2">
                {{ rangeLabel }}
              </p>
            </div>

            <!-- Shopping list selector -->
            <div>
              <label class="block text-xs font-medium text-lavender-400 dark:text-lavender-500 mb-2 uppercase tracking-wide">
                Add to list
              </label>
              <div class="flex items-center gap-2">
                <select
                  v-model="targetListId"
                  class="flex-1 text-sm bg-lavender-50 dark:bg-prussian-700 border border-lavender-200 dark:border-prussian-600 rounded-[10px] px-3 py-2 text-prussian-500 dark:text-lavender-200 focus:outline-none focus:ring-1 focus:ring-[#C4975A]/30 focus:border-[#C4975A] transition-colors"
                >
                  <option v-if="lists.length === 0" :value="null">Auto-create from meal plan</option>
                  <option
                    v-for="list in lists"
                    :key="list.id"
                    :value="list.id"
                  >
                    {{ list.name }}
                  </option>
                </select>
                <button
                  v-if="!isCreatingList"
                  class="text-xs text-[#C4975A] hover:underline whitespace-nowrap"
                  @click="isCreatingList = true"
                >
                  + New list
                </button>
              </div>
              <p v-if="lists.length === 0 && !isCreatingList" class="text-[11px] text-lavender-400 dark:text-lavender-500 mt-1.5">
                No shopping lists yet — items will land in a new "{{ defaultListName }}" list.
              </p>

              <!-- Inline new-list input -->
              <div v-if="isCreatingList" class="mt-2 flex items-center gap-2">
                <input
                  ref="newListInput"
                  v-model="newListName"
                  type="text"
                  placeholder="e.g. Trader Joe's"
                  class="flex-1 text-sm bg-lavender-50 dark:bg-prussian-700 border border-lavender-200 dark:border-prussian-600 rounded-[10px] px-3 py-2 text-prussian-500 dark:text-lavender-200 focus:outline-none focus:ring-1 focus:ring-[#C4975A]/30 focus:border-[#C4975A]"
                  @keydown.enter.prevent="createList"
                  @keydown.escape="cancelCreateList"
                />
                <button
                  class="text-xs px-3 py-2 rounded-[10px] bg-[#C4975A] text-white hover:bg-[#D4A96A] disabled:opacity-50"
                  :disabled="!newListName.trim() || isSavingList"
                  @click="createList"
                >
                  Create
                </button>
                <button class="text-xs text-lavender-500" @click="cancelCreateList">Cancel</button>
              </div>
            </div>
          </div>

          <!-- Body -->
          <div class="flex-1 overflow-y-auto px-6 py-4">
            <div v-if="isLoading" class="flex items-center justify-center py-8">
              <LoadingSpinner size="md" />
            </div>

            <div v-else-if="entries.length === 0" class="py-8 text-center">
              <p class="text-sm text-lavender-500 dark:text-lavender-400">
                No recipes scheduled in this range.
              </p>
              <p class="text-xs text-lavender-400 dark:text-lavender-500 mt-1">
                Add some meals to your plan or extend the range.
              </p>
            </div>

            <div v-else class="space-y-5">
              <div
                v-for="entry in entries"
                :key="entry.entry_id"
                class="border border-lavender-200 dark:border-prussian-700 rounded-lg p-3"
              >
                <RecipeIngredientPicker
                  v-model="selectionsByEntry[entry.entry_id]"
                  :ingredients="entry.ingredients"
                  :title="entry.recipe.title"
                  :subtitle="formatEntryMeta(entry)"
                  collapsible
                  :default-expanded="false"
                />
              </div>
            </div>
          </div>

          <!-- Footer -->
          <div class="px-6 py-4 border-t border-lavender-200 dark:border-prussian-700">
            <div class="flex items-center justify-between mb-3 text-xs text-lavender-500 dark:text-lavender-400">
              <span>{{ totalSelected }} item{{ totalSelected === 1 ? '' : 's' }} from {{ entriesWithSelection }} recipe{{ entriesWithSelection === 1 ? '' : 's' }}</span>
              <button
                v-if="entries.length"
                class="text-xs text-[#C4975A] hover:underline"
                @click="toggleSelectAll"
              >
                {{ allSelected ? 'Deselect all' : 'Select all' }}
              </button>
            </div>
            <div class="flex gap-2">
              <button class="btn-secondary flex-1" @click="$emit('close')">Cancel</button>
              <button
                class="btn-primary flex-1"
                :disabled="totalSelected === 0 || isSaving"
                @click="handleAdd"
              >
                {{ isSaving ? 'Adding…' : `Add ${totalSelected} item${totalSelected === 1 ? '' : 's'}` }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, computed, watch, nextTick } from 'vue'
import { DateTime } from 'luxon'
import { XMarkIcon } from '@heroicons/vue/24/outline'
import { useMealsStore } from '@/stores/meals'
import { useShoppingStore } from '@/stores/shopping'
import { useNotification } from '@/composables/useNotification'
import RecipeIngredientPicker from '@/components/food/RecipeIngredientPicker.vue'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'

const props = defineProps({
  show: { type: Boolean, default: false },
  planId: { type: String, default: null },
})

const emit = defineEmits(['close', 'added'])

const mealsStore = useMealsStore()
const shoppingStore = useShoppingStore()
const { success, error: notifyError } = useNotification()

const dayOptions = [
  { value: 1, label: 'Today' },
  { value: 3, label: '3 days' },
  { value: 5, label: '5 days' },
  { value: 7, label: '1 week' },
  { value: 14, label: '2 weeks' },
  { value: 30, label: '1 month' },
]

const days = ref(7)
const targetListId = ref(null)
const isLoading = ref(false)
const isSaving = ref(false)
const entries = ref([])
const range = ref({ start: null, end: null })
// Map<entry_id, ingredient_ids[]>
const selectionsByEntry = ref({})

// New-list inline create
const lists = computed(() => shoppingStore.lists)
const isCreatingList = ref(false)
const newListName = ref('')
const newListInput = ref(null)
const isSavingList = ref(false)

const defaultListName = computed(() => {
  if (!range.value.start) return 'Groceries'
  return `${DateTime.fromISO(range.value.start).toFormat('MMM d')} Groceries`
})

const rangeLabel = computed(() => {
  if (!range.value.start) return ''
  const start = DateTime.fromISO(range.value.start).toFormat('EEE, MMM d')
  const end = DateTime.fromISO(range.value.end).toFormat('EEE, MMM d')
  return range.value.start === range.value.end ? start : `${start} – ${end}`
})

const totalSelected = computed(() =>
  Object.values(selectionsByEntry.value).reduce((acc, ids) => acc + (ids?.length || 0), 0)
)

const entriesWithSelection = computed(() =>
  Object.values(selectionsByEntry.value).filter(ids => ids?.length > 0).length
)

const allSelected = computed(() => {
  if (entries.value.length === 0) return false
  return entries.value.every(e => (selectionsByEntry.value[e.entry_id]?.length || 0) === e.ingredients.length)
})

const toggleSelectAll = () => {
  const next = {}
  if (allSelected.value) {
    entries.value.forEach(e => { next[e.entry_id] = [] })
  } else {
    entries.value.forEach(e => { next[e.entry_id] = e.ingredients.map(i => i.id) })
  }
  selectionsByEntry.value = next
}

const formatEntryMeta = (entry) => {
  const date = DateTime.fromISO(entry.date).toFormat('EEE M/d')
  const slot = entry.meal_slot ? entry.meal_slot.charAt(0).toUpperCase() + entry.meal_slot.slice(1) : ''
  return slot ? `${slot} · ${date}` : date
}

const loadPreview = async () => {
  if (!props.planId) return
  isLoading.value = true
  const result = await mealsStore.previewShoppingList(props.planId, {
    days: days.value,
    shoppingListId: targetListId.value,
  })
  isLoading.value = false

  if (!result.success) {
    notifyError(result.error || 'Failed to load preview', 4000)
    entries.value = []
    return
  }

  entries.value = result.entries
  range.value = result.range
  // Default: every ingredient that's NOT already on the list pre-selected.
  const next = {}
  entries.value.forEach(e => {
    next[e.entry_id] = e.ingredients.filter(i => !i.already_on_list).map(i => i.id)
  })
  selectionsByEntry.value = next
}

const handleAdd = async () => {
  const selections = Object.entries(selectionsByEntry.value)
    .filter(([, ids]) => ids?.length > 0)
    .map(([entry_id, ingredient_ids]) => {
      const entry = entries.value.find(e => e.entry_id === entry_id)
      const allIds = entry?.ingredients.map(i => i.id) ?? []
      const isAll = ingredient_ids.length === allIds.length
      return { entry_id, ingredient_ids: isAll ? null : ingredient_ids }
    })

  if (selections.length === 0) return

  isSaving.value = true
  const result = await mealsStore.addSelectionsToShoppingList(props.planId, selections, targetListId.value)
  isSaving.value = false

  if (result.success) {
    success(`Added ${result.added_count} item${result.added_count === 1 ? '' : 's'} to shopping list`)
    // Refresh shopping store so the Shopping tab reflects the new items.
    shoppingStore.fetchLists()
    emit('added', result)
    emit('close')
  } else {
    notifyError(result.error || 'Failed to add to shopping list', 4000)
  }
}

const createList = async () => {
  const name = newListName.value.trim()
  if (!name || isSavingList.value) return
  isSavingList.value = true
  const result = await shoppingStore.createList(name)
  isSavingList.value = false
  if (result.success) {
    // Try to find the new list in the store (createList typically refreshes lists.value).
    const created = shoppingStore.lists.find(l => l.name === name)
    if (created) targetListId.value = created.id
    isCreatingList.value = false
    newListName.value = ''
  } else {
    notifyError(result.error || 'Failed to create list', 4000)
  }
}

const cancelCreateList = () => {
  isCreatingList.value = false
  newListName.value = ''
}

watch(isCreatingList, async (val) => {
  if (val) {
    await nextTick()
    newListInput.value?.focus()
  }
})

// When modal opens: ensure shopping lists are loaded, default the selector.
watch(() => props.show, async (val) => {
  if (!val) return
  if (lists.value.length === 0) await shoppingStore.fetchLists()
  if (!targetListId.value && lists.value.length > 0) {
    targetListId.value = lists.value[0].id
  }
})

// Re-load preview when modal opens, days change, or target list changes.
watch(() => [props.show, days.value, props.planId, targetListId.value], ([show]) => {
  if (show) loadPreview()
}, { immediate: true })
</script>
