<template>
  <div class="h-full flex flex-col overflow-hidden">
    <!-- Search + controls -->
    <div class="px-4 md:px-6 pt-4 pb-2 space-y-3">
      <!-- Search + Add button row -->
      <div class="flex items-center gap-3">
        <div class="flex-1 relative">
          <MagnifyingGlassIcon class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-lavender-400" />
          <input
            v-model="searchInput"
            type="text"
            placeholder="Search recipes..."
            class="w-full pl-9 pr-4 py-2.5 text-sm bg-lavender-50 dark:bg-prussian-700 border border-lavender-200 dark:border-prussian-600 rounded-[10px] text-prussian-500 dark:text-lavender-200 placeholder-lavender-400 dark:placeholder-lavender-500 focus:outline-none focus:ring-1 focus:ring-[#C4975A]/30 focus:border-[#C4975A] transition-colors"
            @input="onSearchInput"
          />
        </div>
        <!-- View toggle -->
        <button
          class="flex-shrink-0 p-2.5 rounded-[10px] transition-colors bg-lavender-100 dark:bg-prussian-700 text-lavender-600 dark:text-lavender-400 hover:bg-lavender-200 dark:hover:bg-prussian-600"
          :title="viewMode === 'grid' ? 'Switch to compact view' : 'Switch to grid view'"
          @click="toggleViewMode"
        >
          <Squares2X2Icon v-if="viewMode === 'compact'" class="w-4 h-4" />
          <ListBulletIcon v-else class="w-4 h-4" />
        </button>
        <button
          class="hidden md:flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-[#C4975A] hover:bg-[#D4A96A] rounded-[10px] transition-colors whitespace-nowrap"
          @click="showAddMenu = !showAddMenu"
        >
          <PlusIcon class="w-4 h-4" />
          Add Recipe
        </button>
      </div>

      <!-- Filter row -->
      <div class="flex items-center gap-2 overflow-x-auto scrollbar-hide pb-1">
        <!-- Sort dropdown -->
        <div class="relative flex-shrink-0">
          <button
            class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-full bg-lavender-100 dark:bg-prussian-700 text-lavender-600 dark:text-lavender-400 hover:bg-lavender-200 dark:hover:bg-prussian-600 transition-colors"
            @click="showSortMenu = !showSortMenu"
          >
            <AdjustmentsHorizontalIcon class="w-3.5 h-3.5" />
            {{ sortLabel }}
            <ChevronDownIcon class="w-3 h-3" />
          </button>
          <div
            v-if="showSortMenu"
            class="absolute top-full left-0 mt-1 bg-white dark:bg-prussian-800 border border-lavender-200 dark:border-prussian-700 rounded-lg shadow-lg z-10 py-1 min-w-[120px]"
          >
            <button
              v-for="opt in sortOptions"
              :key="opt.value"
              class="w-full px-3 py-2 text-xs text-left hover:bg-lavender-50 dark:hover:bg-prussian-700 transition-colors"
              :class="sortBy === opt.value
                ? 'text-[#C4975A] font-medium'
                : 'text-prussian-500 dark:text-lavender-300'"
              @click="setSortBy(opt.value)"
            >
              {{ opt.label }}
            </button>
          </div>
        </div>

        <!-- Favorites chip -->
        <button
          class="flex-shrink-0 px-3 py-1.5 text-xs font-medium rounded-full transition-colors whitespace-nowrap"
          :class="showFavoritesOnly
            ? 'bg-red-500/10 text-red-600 dark:text-red-400 border border-red-300 dark:border-red-700'
            : 'bg-lavender-100 dark:bg-prussian-700 text-lavender-600 dark:text-lavender-400 hover:bg-lavender-200 dark:hover:bg-prussian-600'"
          @click="toggleFavorites"
        >
          <span class="flex items-center gap-1">
            <HeartIcon class="w-3 h-3" />
            Favorites
          </span>
        </button>

        <!-- Divider -->
        <div class="w-px h-4 bg-lavender-200 dark:bg-prussian-700 flex-shrink-0"></div>

        <!-- Tag chips — only show tags used on at least one recipe -->
        <button
          v-for="tag in recipeTags"
          :key="tag.id"
          class="flex-shrink-0 flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-full transition-colors whitespace-nowrap"
          :class="isTagSelected(tag.id)
            ? 'text-white'
            : 'bg-lavender-100 dark:bg-prussian-700 text-lavender-600 dark:text-lavender-400 hover:bg-lavender-200 dark:hover:bg-prussian-600'"
          :style="isTagSelected(tag.id) ? { backgroundColor: tag.color || '#C4975A' } : {}"
          @click="toggleTagFilter(tag.id)"
        >
          <span
            v-if="!isTagSelected(tag.id)"
            class="w-2 h-2 rounded-full flex-shrink-0"
            :style="{ backgroundColor: tag.color || '#C4975A' }"
          ></span>
          {{ tag.name }}
        </button>
      </div>
    </div>

    <!-- Divider -->
    <div class="px-4 md:px-6">
      <div class="border-t border-lavender-200 dark:border-prussian-700"></div>
    </div>

    <!-- Loading -->
    <div v-if="isLoading && recipes.length === 0" class="flex items-center justify-center py-16">
      <LoadingSpinner size="lg" />
    </div>

    <!-- Empty state -->
    <EmptyState
      v-else-if="!isLoading && recipes.length === 0"
      :icon="FireIcon"
      title="No recipes yet"
      description="Add your first recipe to get started. Import from a URL, snap a photo, or create one from scratch."
      action-text="Add Recipe"
      @action="showAddMenu = true"
    />

    <!-- Recipe list -->
    <div v-else class="flex-1 overflow-y-auto px-4 md:px-6 py-4 pb-32 md:pb-6">
      <!-- Grid view -->
      <div v-if="viewMode === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <RecipeCard
          v-for="recipe in recipes"
          :key="recipe.id"
          :recipe="recipe"
          @toggle-favorite="handleToggleFavorite"
        />
      </div>

      <!-- Compact list view -->
      <div v-else class="space-y-1">
        <div
          v-for="recipe in recipes"
          :key="recipe.id"
          class="flex items-center gap-3 px-3 py-2.5 rounded-[10px] bg-white dark:bg-prussian-800 border border-[#E8E4DF] dark:border-prussian-700 cursor-pointer hover:border-[#C4975A]/40 transition-colors"
          @click="$router.push({ name: 'RecipeDetail', params: { id: recipe.id } })"
        >
          <!-- Thumbnail -->
          <div class="w-12 h-12 rounded-lg overflow-hidden flex-shrink-0 bg-lavender-100 dark:bg-prussian-700">
            <img
              v-if="recipe.image_path"
              :src="`/storage/${recipe.image_path}`"
              :alt="recipe.title"
              class="w-full h-full object-cover"
            />
            <div v-else class="w-full h-full flex items-center justify-center">
              <FireIcon class="w-5 h-5 text-lavender-300 dark:text-prussian-600" />
            </div>
          </div>

          <!-- Info -->
          <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-prussian-500 dark:text-lavender-200 truncate">{{ recipe.title }}</p>
            <div class="flex items-center gap-2 mt-0.5">
              <span v-if="compactTime(recipe)" class="flex items-center gap-1 text-xs text-lavender-400 dark:text-lavender-500">
                <ClockIcon class="w-3 h-3" />
                {{ compactTime(recipe) }}
              </span>
              <span
                v-for="tag in (recipe.tags || []).slice(0, 3)"
                :key="tag.id"
                class="px-1.5 py-0.5 text-[10px] font-medium rounded-full"
                :style="{ backgroundColor: (tag.color || '#C4975A') + '20', color: tag.color || '#C4975A' }"
              >
                {{ tag.name }}
              </span>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex items-center gap-1 flex-shrink-0">
            <span v-if="recipe.family_average_rating > 0" class="flex items-center gap-0.5 text-xs text-lavender-400">
              <StarIcon class="w-3.5 h-3.5 text-[#C4975A]" />
              {{ recipe.family_average_rating }}
            </span>
            <button
              class="p-1.5 rounded-full transition-colors"
              :class="recipe.is_favorite ? 'text-red-500' : 'text-lavender-300 dark:text-lavender-600 hover:text-red-400'"
              aria-label="Toggle favorite"
              @click.stop="handleToggleFavorite(recipe.id)"
            >
              <HeartIconSolid v-if="recipe.is_favorite" class="w-4 h-4" />
              <HeartIcon v-else class="w-4 h-4" />
            </button>
          </div>
        </div>
      </div>

      <!-- Load more -->
      <div v-if="hasMore" class="flex justify-center mt-6">
        <button
          class="px-6 py-2.5 text-sm font-medium text-lavender-600 dark:text-lavender-400 bg-lavender-100 dark:bg-prussian-700 hover:bg-lavender-200 dark:hover:bg-prussian-600 rounded-[10px] transition-colors"
          :disabled="isLoading"
          @click="loadMore"
        >
          {{ isLoading ? 'Loading...' : 'Load More' }}
        </button>
      </div>
    </div>

    <!-- Mobile FAB -->
    <FloatingActionButton @click="showAddMenu = true" />

    <!-- Add recipe menu (overlay) -->
    <Teleport to="body">
      <Transition name="fade">
        <div
          v-if="showAddMenu"
          class="fixed inset-0 bg-black/50 z-50 flex items-end md:items-center justify-center p-4"
          @click="showAddMenu = false"
        >
          <div
            class="bg-white dark:bg-prussian-800 rounded-t-[12px] md:rounded-[12px] w-full md:max-w-sm p-6 space-y-2"
            @click.stop
          >
            <h3 class="text-lg font-semibold text-prussian-500 dark:text-lavender-200 mb-4">Add Recipe</h3>
            <button
              class="w-full flex items-center gap-3 px-4 py-3 text-sm text-prussian-500 dark:text-lavender-200 hover:bg-lavender-50 dark:hover:bg-prussian-700 rounded-[10px] transition-colors"
              @click="openManualCreate"
            >
              <PencilSquareIcon class="w-5 h-5 text-lavender-400" />
              Create from scratch
            </button>
            <button
              class="w-full flex items-center gap-3 px-4 py-3 text-sm text-prussian-500 dark:text-lavender-200 hover:bg-lavender-50 dark:hover:bg-prussian-700 rounded-[10px] transition-colors"
              @click="openImportUrl"
            >
              <LinkIcon class="w-5 h-5 text-lavender-400" />
              Import from URL
            </button>
            <button
              class="w-full flex items-center gap-3 px-4 py-3 text-sm text-prussian-500 dark:text-lavender-200 hover:bg-lavender-50 dark:hover:bg-prussian-700 rounded-[10px] transition-colors"
              @click="openImportPhoto"
            >
              <CameraIcon class="w-5 h-5 text-lavender-400" />
              Import from photo
            </button>
          </div>
        </div>
      </Transition>
    </Teleport>

    <!-- Recipe Form Modal (manual create) -->
    <BaseModal :show="showCreateForm" title="New Recipe" size="xl" @close="showCreateForm = false">
      <RecipeForm ref="createFormRef" @save="handleCreateRecipe" @cancel="showCreateForm = false" />
    </BaseModal>

    <!-- Import Modal -->
    <RecipeImportModal
      v-if="showImportModal"
      :show="showImportModal"
      :initial-tab="importTab"
      @saved="handleImportSaved"
      @close="showImportModal = false"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { storeToRefs } from 'pinia'
import { useRecipesStore } from '@/stores/recipes'
import { useNotification } from '@/composables/useNotification'
import RecipeCard from '@/components/recipes/RecipeCard.vue'
import RecipeForm from '@/components/recipes/RecipeForm.vue'
import RecipeImportModal from '@/components/recipes/RecipeImportModal.vue'
import BaseModal from '@/components/common/BaseModal.vue'
import EmptyState from '@/components/common/EmptyState.vue'
import FloatingActionButton from '@/components/common/FloatingActionButton.vue'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import {
  MagnifyingGlassIcon,
  PlusIcon,
  HeartIcon,
  AdjustmentsHorizontalIcon,
  ChevronDownIcon,
  PencilSquareIcon,
  LinkIcon,
  CameraIcon,
  FireIcon,
  Squares2X2Icon,
  ListBulletIcon,
  ClockIcon,
  StarIcon,
} from '@heroicons/vue/24/outline'
import { HeartIcon as HeartIconSolid } from '@heroicons/vue/24/solid'

const recipesStore = useRecipesStore()
const { recipes, tags, isLoading, hasMore, searchQuery, sortBy, selectedTagIds, showFavoritesOnly } = storeToRefs(recipesStore)
const { success, error: notifyError } = useNotification()

const searchInput = ref('')
const showSortMenu = ref(false)
const showAddMenu = ref(false)
const showCreateForm = ref(false)
const showImportModal = ref(false)
const importTab = ref('url')
const viewMode = ref(localStorage.getItem('kinhold-recipe-view') || 'grid')

const toggleViewMode = () => {
  viewMode.value = viewMode.value === 'grid' ? 'compact' : 'grid'
  localStorage.setItem('kinhold-recipe-view', viewMode.value)
}

const compactTime = (recipe) => {
  const total = recipe.total_time_minutes || (recipe.prep_time_minutes || 0) + (recipe.cook_time_minutes || 0)
  if (!total) return null
  if (total < 60) return `${total}m`
  const h = Math.floor(total / 60)
  const m = total % 60
  return m > 0 ? `${h}h ${m}m` : `${h}h`
}

let searchDebounce = null

const sortOptions = [
  { value: 'recent', label: 'Recent' },
  { value: 'alpha', label: 'A-Z' },
  { value: 'rating', label: 'Top Rated' },
]

const sortLabel = computed(() => {
  const opt = sortOptions.find((o) => o.value === sortBy.value)
  return opt?.label || 'Recent'
})

// Server returns only food-scoped tags. Show all of them as filter chips so
// the user can filter even before any recipe is tagged.
const recipeTags = computed(() => tags.value)

const isTagSelected = (tagId) => selectedTagIds.value.includes(tagId)

const onSearchInput = () => {
  clearTimeout(searchDebounce)
  searchDebounce = setTimeout(() => {
    searchQuery.value = searchInput.value
    recipesStore.fetchRecipes()
  }, 300)
}

const setSortBy = (value) => {
  sortBy.value = value
  showSortMenu.value = false
  recipesStore.fetchRecipes()
}

const toggleFavorites = () => {
  showFavoritesOnly.value = !showFavoritesOnly.value
  recipesStore.fetchRecipes()
}

// Close sort menu on click outside
const closeSortMenu = () => {
  if (showSortMenu.value) showSortMenu.value = false
}

watch(showSortMenu, (val) => {
  if (val) {
    setTimeout(() => document.addEventListener('click', closeSortMenu, { once: true }), 0)
  }
})

const handleToggleFavorite = async (id) => {
  const result = await recipesStore.toggleFavorite(id)
  if (!result.success) notifyError(result.error)
}

const loadMore = () => {
  const nextPage = recipesStore.pagination.current_page + 1
  recipesStore.fetchRecipes({ page: nextPage })
}

// Tag filter triggers refetch
watch(selectedTagIds, () => {
  recipesStore.fetchRecipes()
}, { deep: true })

// ── Add recipe flows ──

const openManualCreate = () => {
  showAddMenu.value = false
  showCreateForm.value = true
}

const openImportUrl = () => {
  showAddMenu.value = false
  importTab.value = 'url'
  showImportModal.value = true
}

const openImportPhoto = () => {
  showAddMenu.value = false
  importTab.value = 'photo'
  showImportModal.value = true
}

const createFormRef = ref(null)

const handleCreateRecipe = async (formData) => {
  const result = await recipesStore.createRecipe(formData)
  if (result.success) {
    showCreateForm.value = false
    success('Recipe created!')
  } else {
    notifyError(result.error)
    if (createFormRef.value) createFormRef.value.saving = false
  }
}

const handleImportSaved = () => {
  showImportModal.value = false
  success('Recipe imported!')
}

onMounted(() => {
  recipesStore.fetchRecipes()
  recipesStore.fetchTags()
})
</script>
