<template>
  <SlidePanel
    :show="show"
    :title="panelTitle"
    @close="$emit('close')"
  >
    <!-- Source type tabs -->
    <div class="flex border-b border-lavender-200 dark:border-prussian-700 px-4">
      <button
        v-for="tab in sourceTabs"
        :key="tab.key"
        class="px-3 py-2.5 text-xs font-medium transition-colors relative whitespace-nowrap"
        :class="activeSource === tab.key
          ? 'text-[#C4975A]'
          : 'text-lavender-500 dark:text-lavender-400 hover:text-prussian-500 dark:hover:text-lavender-200'"
        @click="activeSource = tab.key; selectedSource = null"
      >
        {{ tab.label }}
        <span v-if="activeSource === tab.key" class="absolute bottom-0 left-0 right-0 h-0.5 bg-[#C4975A] rounded-full" />
      </button>
    </div>

    <!-- Source selector -->
    <div class="px-4 pt-3 pb-2">
      <!-- Recipe search -->
      <div v-if="activeSource === 'recipe'">
        <div class="relative mb-3">
          <MagnifyingGlassIcon class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-lavender-400" />
          <input
            v-model="recipeSearch"
            type="text"
            placeholder="Search recipes..."
            class="w-full pl-9 pr-4 py-2 text-sm bg-lavender-50 dark:bg-prussian-700 border border-lavender-200 dark:border-prussian-600 rounded-[10px] text-prussian-500 dark:text-lavender-200 placeholder-lavender-400 focus:outline-none focus:ring-1 focus:ring-[#C4975A]/30 focus:border-[#C4975A] transition-colors"
          />
        </div>
        <div v-if="recipesStore.isLoading" class="flex justify-center py-4">
          <LoadingSpinner size="sm" />
        </div>
        <div v-else class="space-y-1 max-h-48 overflow-y-auto">
          <button
            v-for="recipe in filteredRecipes"
            :key="recipe.id"
            class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-left transition-colors"
            :class="selectedSource?.id === recipe.id
              ? 'bg-[#C4975A]/10 border border-[#C4975A]/30'
              : 'hover:bg-lavender-50 dark:hover:bg-prussian-700'"
            @click="selectedSource = recipe"
          >
            <img
              v-if="recipe.photo_url"
              :src="recipe.photo_url"
              class="w-8 h-8 rounded object-cover flex-shrink-0"
            />
            <FireIcon v-else class="w-5 h-5 text-orange-400 flex-shrink-0" />
            <div class="min-w-0">
              <p class="text-sm font-medium text-prussian-500 dark:text-lavender-200 truncate">{{ recipe.title }}</p>
              <p v-if="recipe.prep_time_minutes || recipe.servings" class="text-xs text-lavender-400">
                <span v-if="recipe.prep_time_minutes">{{ recipe.prep_time_minutes }}m</span>
                <span v-if="recipe.prep_time_minutes && recipe.servings"> · </span>
                <span v-if="recipe.servings">{{ recipe.servings }} servings</span>
              </p>
            </div>
            <CheckCircleIcon v-if="selectedSource?.id === recipe.id" class="w-4 h-4 text-[#C4975A] flex-shrink-0 ml-auto" />
          </button>
          <p v-if="filteredRecipes.length === 0" class="text-sm text-lavender-400 dark:text-lavender-500 text-center py-4">
            No recipes found
          </p>
        </div>
      </div>

      <!-- Restaurant search -->
      <div v-else-if="activeSource === 'restaurant'">
        <div class="relative mb-3">
          <MagnifyingGlassIcon class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-lavender-400" />
          <input
            v-model="restaurantSearch"
            type="text"
            placeholder="Search restaurants..."
            class="w-full pl-9 pr-4 py-2 text-sm bg-lavender-50 dark:bg-prussian-700 border border-lavender-200 dark:border-prussian-600 rounded-[10px] text-prussian-500 dark:text-lavender-200 placeholder-lavender-400 focus:outline-none focus:ring-1 focus:ring-[#C4975A]/30 focus:border-[#C4975A] transition-colors"
          />
        </div>
        <div class="space-y-1 max-h-48 overflow-y-auto">
          <button
            v-for="restaurant in filteredRestaurants"
            :key="restaurant.id"
            class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-left transition-colors"
            :class="selectedSource?.id === restaurant.id
              ? 'bg-[#C4975A]/10 border border-[#C4975A]/30'
              : 'hover:bg-lavender-50 dark:hover:bg-prussian-700'"
            @click="selectedSource = restaurant"
          >
            <BuildingStorefrontIcon class="w-5 h-5 text-blue-400 flex-shrink-0" />
            <div class="min-w-0">
              <p class="text-sm font-medium text-prussian-500 dark:text-lavender-200 truncate">{{ restaurant.name }}</p>
              <p v-if="restaurant.cuisine" class="text-xs text-lavender-400">{{ restaurant.cuisine }}</p>
            </div>
            <CheckCircleIcon v-if="selectedSource?.id === restaurant.id" class="w-4 h-4 text-[#C4975A] flex-shrink-0 ml-auto" />
          </button>
          <p v-if="filteredRestaurants.length === 0" class="text-sm text-lavender-400 dark:text-lavender-500 text-center py-4">
            No restaurants yet — add some in the Restaurants tab
          </p>
        </div>
      </div>

      <!-- Presets -->
      <div v-else-if="activeSource === 'preset'">
        <div class="grid grid-cols-2 gap-2 max-h-48 overflow-y-auto">
          <button
            v-for="preset in mealsStore.presets"
            :key="preset.id"
            class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-left transition-colors border"
            :class="selectedSource?.id === preset.id
              ? 'bg-[#C4975A]/10 border-[#C4975A]/40 text-[#C4975A]'
              : 'bg-lavender-50 dark:bg-prussian-700 border-lavender-200 dark:border-prussian-600 text-prussian-500 dark:text-lavender-200 hover:border-[#C4975A]/30'"
            @click="selectedSource = preset"
          >
            <span v-if="preset.icon" class="text-base">{{ preset.icon }}</span>
            <SparklesIcon v-else class="w-4 h-4 text-purple-400" />
            <span class="text-sm font-medium truncate">{{ preset.label }}</span>
          </button>
        </div>
        <p v-if="mealsStore.presets.length === 0" class="text-sm text-lavender-400 dark:text-lavender-500 text-center py-4">
          No presets available
        </p>
      </div>

      <!-- Custom -->
      <div v-else-if="activeSource === 'custom'">
        <BaseInput
          v-model="customTitle"
          label="What's for this meal?"
          placeholder="e.g. Leftovers, BBQ, Snack box..."
          :error="customTitleError"
        />
      </div>
    </div>

    <!-- Divider -->
    <div class="mx-4 border-t border-lavender-200 dark:border-prussian-700 my-2" />

    <!-- Entry details -->
    <div class="px-4 pb-4 space-y-4">
      <!-- Servings -->
      <div>
        <label class="block text-xs font-medium text-lavender-400 dark:text-lavender-500 mb-1.5 uppercase tracking-wide">Servings</label>
        <input
          v-model.number="servings"
          type="number"
          min="1"
          max="20"
          class="w-24 text-sm bg-lavender-50 dark:bg-prussian-700 border border-lavender-200 dark:border-prussian-600 rounded-[10px] px-3 py-2 text-prussian-500 dark:text-lavender-200 focus:outline-none focus:ring-1 focus:ring-[#C4975A]/30 focus:border-[#C4975A] transition-colors"
          placeholder="4"
        />
      </div>

      <!-- Cook assignment -->
      <div v-if="familyMembers.length > 0">
        <label class="block text-xs font-medium text-lavender-400 dark:text-lavender-500 mb-2 uppercase tracking-wide">Assign Cook(s)</label>
        <div class="flex flex-wrap gap-2">
          <button
            v-for="member in familyMembers"
            :key="member.id"
            class="flex items-center gap-1.5 px-2 py-1.5 rounded-full border text-xs font-medium transition-colors"
            :class="selectedCooks.includes(member.id)
              ? 'bg-[#C4975A]/10 border-[#C4975A]/40 text-[#C4975A]'
              : 'bg-lavender-50 dark:bg-prussian-700 border-lavender-200 dark:border-prussian-600 text-lavender-500 dark:text-lavender-400 hover:border-[#C4975A]/30'"
            @click="toggleCook(member.id)"
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
          v-model="notes"
          rows="2"
          class="w-full text-sm bg-lavender-50 dark:bg-prussian-700 border border-lavender-200 dark:border-prussian-600 rounded-[10px] px-3 py-2 text-prussian-500 dark:text-lavender-200 placeholder-lavender-400 focus:outline-none focus:ring-1 focus:ring-[#C4975A]/30 focus:border-[#C4975A] transition-colors resize-none"
          placeholder="Any notes..."
        />
      </div>
    </div>

    <template #footer>
      <div class="flex gap-3">
        <BaseButton variant="secondary" class="flex-1" @click="$emit('close')">Cancel</BaseButton>
        <BaseButton variant="primary" class="flex-1" :loading="isSaving" @click="submit">Add Entry</BaseButton>
      </div>
      <p v-if="submitError" class="text-xs text-red-500 mt-2">{{ submitError }}</p>
    </template>
  </SlidePanel>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { DateTime } from 'luxon'
import {
  MagnifyingGlassIcon,
  FireIcon,
  BuildingStorefrontIcon,
  SparklesIcon,
  CheckCircleIcon,
} from '@heroicons/vue/24/outline'
import { useRecipesStore } from '@/stores/recipes'
import { useRestaurantsStore } from '@/stores/restaurants'
import { useMealsStore } from '@/stores/meals'
import { useAuthStore } from '@/stores/auth'
import SlidePanel from '@/components/common/SlidePanel.vue'
import BaseInput from '@/components/common/BaseInput.vue'
import BaseButton from '@/components/common/BaseButton.vue'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import UserAvatar from '@/components/common/UserAvatar.vue'

const props = defineProps({
  show: Boolean,
  targetDate: { type: String, default: null },
  targetSlot: { type: String, default: null },
})

const emit = defineEmits(['close', 'added'])

const recipesStore = useRecipesStore()
const restaurantsStore = useRestaurantsStore()
const mealsStore = useMealsStore()
const authStore = useAuthStore()

const familyMembers = computed(() => authStore.familyMembers || [])

const slotLabel = (slot) => {
  const map = { breakfast: 'Breakfast', lunch: 'Lunch', dinner: 'Dinner', snack: 'Snack' }
  return map[slot] || slot
}

const panelTitle = computed(() => {
  if (props.targetDate && props.targetSlot) {
    try {
      const dt = DateTime.fromISO(props.targetDate)
      return `Add to ${slotLabel(props.targetSlot)} — ${dt.toFormat('EEE, MMM d')}`
    } catch {
      return `Add to ${slotLabel(props.targetSlot)}`
    }
  }
  return 'Add Meal Entry'
})

const sourceTabs = [
  { key: 'recipe', label: '🍳 Recipe' },
  { key: 'restaurant', label: '🏢 Restaurant' },
  { key: 'preset', label: '✨ Preset' },
  { key: 'custom', label: '✏️ Custom' },
]

const activeSource = ref('recipe')
const selectedSource = ref(null)
const recipeSearch = ref('')
const restaurantSearch = ref('')
const customTitle = ref('')
const customTitleError = ref('')
const servings = ref(null)
const selectedCooks = ref([])
const notes = ref('')
const isSaving = ref(false)
const submitError = ref('')

// Filter recipes by search
const filteredRecipes = computed(() => {
  const q = recipeSearch.value.toLowerCase()
  if (!q) return recipesStore.recipes
  return recipesStore.recipes.filter(r => r.title?.toLowerCase().includes(q))
})

// Filter restaurants by search
const filteredRestaurants = computed(() => {
  const q = restaurantSearch.value.toLowerCase()
  if (!q) return restaurantsStore.restaurants
  return restaurantsStore.restaurants.filter(r => r.name?.toLowerCase().includes(q))
})

const toggleCook = (userId) => {
  const idx = selectedCooks.value.indexOf(userId)
  if (idx === -1) {
    selectedCooks.value.push(userId)
  } else {
    selectedCooks.value.splice(idx, 1)
  }
}

// Reset on open
watch(() => props.show, (val) => {
  if (val) {
    activeSource.value = 'recipe'
    selectedSource.value = null
    recipeSearch.value = ''
    restaurantSearch.value = ''
    customTitle.value = ''
    customTitleError.value = ''
    servings.value = null
    selectedCooks.value = []
    notes.value = ''
    submitError.value = ''

    // Ensure recipes are loaded
    if (recipesStore.recipes.length === 0) {
      recipesStore.fetchRecipes()
    }
  }
})

const submit = async () => {
  submitError.value = ''
  customTitleError.value = ''

  const data = {}

  if (activeSource.value === 'recipe') {
    if (!selectedSource.value) { submitError.value = 'Please select a recipe'; return }
    data.recipe_id = selectedSource.value.id
  } else if (activeSource.value === 'restaurant') {
    if (!selectedSource.value) { submitError.value = 'Please select a restaurant'; return }
    data.restaurant_id = selectedSource.value.id
  } else if (activeSource.value === 'preset') {
    if (!selectedSource.value) { submitError.value = 'Please select a preset'; return }
    data.meal_preset_id = selectedSource.value.id
  } else if (activeSource.value === 'custom') {
    if (!customTitle.value.trim()) { customTitleError.value = 'Title is required'; return }
    data.custom_title = customTitle.value.trim()
  }

  if (props.targetDate) data.date = props.targetDate
  if (props.targetSlot) data.meal_slot = props.targetSlot
  if (servings.value && servings.value > 0) data.servings = servings.value
  if (selectedCooks.value.length > 0) data.assigned_cooks = selectedCooks.value
  if (notes.value.trim()) data.notes = notes.value.trim()

  isSaving.value = true
  const result = await mealsStore.addEntry(mealsStore.currentPlan?.id, data)
  isSaving.value = false

  if (result.success) {
    emit('added')
    emit('close')
  } else {
    submitError.value = result.error || 'Failed to add entry'
  }
}
</script>
