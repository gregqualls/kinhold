<template>
  <div class="h-full flex flex-col overflow-hidden">
    <!-- Search + controls -->
    <div class="px-4 md:px-6 pt-4 pb-2 space-y-3">
      <div class="flex items-center gap-3">
        <div class="flex-1">
          <KinSearch
            v-model="restaurantsStore.searchQuery"
            placeholder="Search restaurants..."
            size="sm"
          />
        </div>
        <!-- View toggle (grid / compact) -->
        <button
          type="button"
          class="flex-shrink-0 p-2.5 rounded-[10px] bg-surface-sunken text-ink-secondary hover:bg-surface-overlay transition-colors"
          :title="viewMode === 'grid' ? 'Switch to compact view' : 'Switch to grid view'"
          :aria-label="viewMode === 'grid' ? 'Switch to compact view' : 'Switch to grid view'"
          @click="toggleViewMode"
        >
          <Squares2X2Icon v-if="viewMode === 'compact'" class="w-4 h-4" />
          <ListBulletIcon v-else class="w-4 h-4" />
        </button>
        <KinButton
          variant="primary"
          size="sm"
          class="hidden md:inline-flex whitespace-nowrap"
          @click="openAddModal"
        >
          <template #leading><PlusIcon class="w-4 h-4" /></template>
          Add Restaurant
        </KinButton>
      </div>

      <!-- Filter row -->
      <div class="flex items-center gap-2 overflow-x-auto scrollbar-hide pb-1">
        <!-- Favorites chip -->
        <KinChip
          variant="filter"
          size="sm"
          :active="showFavoritesOnly"
          class="flex-shrink-0"
          @click="showFavoritesOnly = !showFavoritesOnly"
        >
          <template #leading><HeartIcon class="w-3 h-3" /></template>
          Favorites
        </KinChip>

        <!-- Divider -->
        <div v-if="restaurantTags.length" class="w-px h-4 bg-border-subtle flex-shrink-0"></div>

        <!-- Tag chips -->
        <KinChip
          v-for="tag in restaurantTags"
          :key="tag.id"
          variant="filter"
          size="sm"
          :custom-color="tag.color || '#C4975A'"
          :active="isTagSelected(tag.id)"
          class="flex-shrink-0"
          @click="toggleTagFilter(tag.id)"
        >
          {{ tag.name }}
        </KinChip>
      </div>
    </div>

    <!-- Divider -->
    <div class="px-4 md:px-6">
      <div class="border-t border-border-subtle"></div>
    </div>

    <!-- Content -->
    <div class="flex-1 overflow-y-auto px-4 md:px-6 pb-24 md:pb-8">
      <!-- Loading -->
      <div v-if="restaurantsStore.isLoading" class="flex items-center justify-center py-16">
        <LoadingSpinner size="lg" />
      </div>

      <!-- Empty state -->
      <KinEmptyState
        v-else-if="displayedRestaurants.length === 0"
        :icon="BuildingStorefrontIcon"
        title="No restaurants yet"
        :description="restaurantsStore.searchQuery ? 'No restaurants match your search.' : 'Add your favourite spots to order from or visit.'"
        accent-color="peach"
      >
        <template #cta>
          <KinButton variant="primary" size="sm" @click="openAddModal">Add Restaurant</KinButton>
        </template>
      </KinEmptyState>

      <!-- Grid -->
      <div v-else-if="viewMode === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-2">
        <FoodCard
          v-for="restaurant in displayedRestaurants"
          :key="restaurant.id"
          :title="restaurant.name"
          :image-url="restaurant.image_url"
          :fallback-gradient="restaurantFallbackGradient(restaurant)"
          :is-favorite="!!restaurant.is_favorite"
          :meta-items="restaurantMeta(restaurant)"
          :tags="restaurantCardTags(restaurant)"
          @click="openDetail(restaurant)"
          @toggle-favorite="restaurantsStore.toggleFavorite(restaurant.id)"
        />
      </div>

      <!-- Compact list view -->
      <div v-else class="space-y-1 mt-2">
        <div
          v-for="restaurant in displayedRestaurants"
          :key="restaurant.id"
          class="flex items-center gap-3 px-3 py-2.5 rounded-[10px] bg-surface-raised border border-border-subtle cursor-pointer hover:border-[#C4975A]/40 transition-colors"
          @click="openDetail(restaurant)"
        >
          <!-- Thumbnail -->
          <div
            class="w-12 h-12 rounded-lg overflow-hidden flex-shrink-0 bg-surface-raised"
            :style="restaurant.image_url ? null : restaurantFallbackStyle(restaurant)"
          >
            <img
              v-if="restaurant.image_url"
              :src="resolveImageUrl(restaurant.image_url)"
              :alt="restaurant.name"
              class="w-full h-full object-cover"
            />
          </div>

          <!-- Info -->
          <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-ink-primary truncate">{{ restaurant.name }}</p>
            <div class="flex items-center gap-2 mt-0.5">
              <span v-if="restaurant.address" class="flex items-center gap-1 text-xs text-ink-tertiary truncate">
                <MapPinIcon class="w-3 h-3 flex-shrink-0" />
                {{ restaurant.address.length > 30 ? restaurant.address.slice(0, 30) + '…' : restaurant.address }}
              </span>
              <span
                v-for="tag in (restaurant.tags || []).slice(0, 2)"
                :key="tag.id"
                class="px-1.5 py-0.5 text-[10px] font-medium rounded-full"
                :style="{ backgroundColor: (tag.color || '#C4975A') + '20', color: tag.color || '#C4975A' }"
              >
                {{ tag.name }}
              </span>
            </div>
          </div>

          <!-- Favorite -->
          <div class="flex items-center gap-1 flex-shrink-0">
            <span v-if="restaurant.family_average_rating > 0" class="flex items-center gap-0.5 text-xs text-ink-tertiary">
              <StarIcon class="w-3.5 h-3.5 text-[#C4975A]" />
              {{ Number(restaurant.family_average_rating).toFixed(1) }}
            </span>
            <button
              type="button"
              class="p-1.5 rounded-full transition-colors"
              :class="restaurant.is_favorite ? 'text-status-failed' : 'text-ink-tertiary hover:text-status-failed/80'"
              :aria-label="restaurant.is_favorite ? 'Remove favorite' : 'Mark as favorite'"
              @click.stop="restaurantsStore.toggleFavorite(restaurant.id)"
            >
              <HeartIconSolid v-if="restaurant.is_favorite" class="w-4 h-4" />
              <HeartIcon v-else class="w-4 h-4" />
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Mobile FAB -->
    <FloatingActionButton @click="openAddModal" />

    <!-- Detail slide panel -->
    <SlidePanel :show="!!selectedRestaurant" title="Restaurant Details" @close="selectedRestaurant = null">
      <div v-if="selectedRestaurant" class="p-6 space-y-5">
        <!-- Editable fields -->
        <div class="space-y-3">
          <KinInput v-model="editFields.name" label="Name" placeholder="Restaurant name" size="sm" />
          <KinInput v-model="editFields.address" label="Address" placeholder="Street address" type="text" size="sm" />
          <KinInput v-model="editFields.phone" label="Phone" placeholder="Phone number" type="tel" size="sm" />
          <KinInput v-model="editFields.menu_url" label="Website / Menu URL" placeholder="https://..." type="url" size="sm" />
          <KinInput v-model="editFields.google_maps_url" label="Google Maps URL" placeholder="https://maps.google.com/..." type="url" size="sm" />
          <PhotoUpload v-model="editFields.image_url" label="Photo" :uploader="uploadRestaurantImage" />
        </div>

        <!-- Tags -->
        <TagPicker
          v-model="editFields.tag_ids"
          :tags="foodTags"
          :on-create="createTag"
        />

        <!-- Quick links -->
        <div class="flex flex-wrap gap-2">
          <a
            v-if="editFields.google_maps_url"
            :href="editFields.google_maps_url"
            target="_blank"
            rel="noopener noreferrer"
            class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium bg-surface-sunken text-ink-primary rounded-full hover:bg-surface-sunken/80 transition-colors"
            @click.stop
          >
            <MapPinIcon class="w-3.5 h-3.5" />
            Maps
          </a>
          <a
            v-if="editFields.menu_url"
            :href="editFields.menu_url"
            target="_blank"
            rel="noopener noreferrer"
            class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium bg-surface-sunken text-ink-primary rounded-full hover:bg-surface-sunken/80 transition-colors"
            @click.stop
          >
            <DocumentTextIcon class="w-3.5 h-3.5" />
            Website
          </a>
          <a
            v-if="editFields.phone"
            :href="'tel:' + editFields.phone"
            class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium bg-surface-sunken text-ink-primary rounded-full hover:bg-surface-sunken/80 transition-colors"
            @click.stop
          >
            <PhoneIcon class="w-3.5 h-3.5" />
            Call
          </a>
        </div>

        <!-- Star rating -->
        <div>
          <p class="text-xs font-medium text-ink-tertiary mb-2 uppercase tracking-wide">Your Rating</p>
          <div class="flex items-center gap-1">
            <button
              v-for="n in 5"
              :key="n"
              class="p-0.5 transition-transform hover:scale-110"
              @click="rateRestaurant(n)"
            >
              <StarIcon
                class="w-6 h-6"
                :class="n <= (pendingRating || Math.round(selectedRestaurant.family_average_rating || 0))
                  ? 'text-[#C4975A] fill-current'
                  : 'text-ink-tertiary'"
              />
            </button>
          </div>
        </div>

        <!-- Family notes -->
        <KinTextarea
          v-model="editNotes"
          label="Family Notes"
          :rows="3"
          placeholder="Add notes about this restaurant..."
          size="sm"
        />

        <!-- Save all -->
        <KinButton variant="primary" size="sm" :loading="isDetailSaving" class="w-full" @click="saveDetail">
          Save Changes
        </KinButton>

        <!-- Actions -->
        <div v-if="isParent" class="pt-2 border-t border-border-subtle">
          <button
            class="flex items-center gap-2 text-sm text-status-failed hover:opacity-80 transition-opacity"
            @click="confirmDelete(selectedRestaurant)"
          >
            <TrashIcon class="w-4 h-4" />
            Remove from family
          </button>
        </div>
      </div>
    </SlidePanel>

    <!-- Add/Import modal -->
    <KinModalSheet
      :model-value="showAddModal"
      title="Add Restaurant"
      size="md"
      @update:model-value="(v) => { if (!v) closeAddModal() }"
      @close="closeAddModal"
    >
      <!-- Tab switcher (only before preview) -->
      <div v-if="!previewData" class="flex border-b border-border-subtle mb-5">
        <button
          v-for="tab in addTabs"
          :key="tab.key"
          class="px-4 py-2.5 text-sm font-medium transition-colors relative"
          :class="addTab === tab.key
            ? 'text-[#C4975A]'
            : 'text-ink-tertiary hover:text-ink-primary'"
          @click="addTab = tab.key"
        >
          {{ tab.label }}
          <span v-if="addTab === tab.key" class="absolute bottom-0 left-0 right-0 h-0.5 bg-[#C4975A] rounded-full"></span>
        </button>
      </div>

      <!-- Import: URL input -->
      <div v-if="addTab === 'import' && !previewData" class="px-6 pb-6 space-y-4">
        <KinInput v-model="importUrl" label="Restaurant URL" placeholder="https://www.restaurant-website.com" :error="formErrors.url" size="sm" @keydown.enter.prevent="previewImport" />

        <!-- Error -->
        <div
          v-if="saveError"
          class="flex items-start gap-2 p-3 bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-800/30 rounded-xl"
        >
          <ExclamationCircleIcon class="w-5 h-5 text-status-failed flex-shrink-0 mt-0.5" />
          <div class="text-sm">
            <p class="font-medium text-status-failed">Import failed</p>
            <p class="text-ink-secondary">{{ saveError }}</p>
          </div>
        </div>

        <p class="text-xs text-ink-tertiary">
          Paste the restaurant's website URL. You can edit all details before saving.
        </p>

        <KinButton variant="primary" size="sm" :loading="isImporting" :disabled="!importUrl" class="w-full" @click="previewImport">
          {{ isImporting ? 'Extracting...' : 'Preview Restaurant' }}
        </KinButton>
      </div>

      <!-- Import: loading spinner -->
      <div v-if="isImporting" class="flex flex-col items-center justify-center py-8">
        <LoadingSpinner size="lg" />
        <p class="mt-3 text-sm text-ink-tertiary">Extracting restaurant details...</p>
      </div>

      <!-- Import: preview form (edit before save) -->
      <div v-if="previewData && !isImporting" class="px-6 pb-6 space-y-4">
        <div class="flex items-center gap-2 p-3 bg-[#5B8C6A]/10 border border-[#5B8C6A]/20 rounded-xl">
          <CheckCircleIcon class="w-5 h-5 text-[#5B8C6A] flex-shrink-0" />
          <p class="text-sm text-[#5B8C6A]">
            Details extracted! Review and edit below, then save.
          </p>
        </div>

        <KinInput v-model="form.name" label="Name *" placeholder="Restaurant name" :error="formErrors.name" size="sm" />
        <KinInput v-model="form.address" label="Address" placeholder="Street address" size="sm" />
        <KinInput v-model="form.phone" label="Phone" placeholder="Phone number" size="sm" />
        <KinInput v-model="form.menu_url" label="Website" placeholder="https://..." size="sm" />
        <PhotoUpload v-model="form.image_url" label="Photo" :uploader="uploadRestaurantImage" />
        <TagPicker v-model="form.tag_ids" :tags="foodTags" :on-create="createTag" />

        <div class="flex gap-3">
          <KinButton variant="primary" size="sm" :loading="isSaving" class="flex-1" @click="saveFromPreview">
            Save Restaurant
          </KinButton>
          <KinButton variant="ghost" size="sm" @click="resetPreview">
            Back
          </KinButton>
        </div>
        <p v-if="saveError" class="text-xs text-status-failed">{{ saveError }}</p>
      </div>

      <!-- Manual form -->
      <div v-if="addTab === 'manual' && !previewData" class="px-6 pb-6 space-y-4">
        <KinInput v-model="form.name" label="Name *" placeholder="e.g. Gusto Pizzeria" :error="formErrors.name" size="sm" />
        <KinInput v-model="form.address" label="Address" placeholder="123 Main St" size="sm" />
        <KinInput v-model="form.phone" label="Phone" placeholder="+1 (555) 000-0000" size="sm" />
        <KinInput v-model="form.menu_url" label="Website" placeholder="https://..." size="sm" />
        <PhotoUpload v-model="form.image_url" label="Photo" :uploader="uploadRestaurantImage" />
        <TagPicker v-model="form.tag_ids" :tags="foodTags" :on-create="createTag" />
        <KinButton variant="primary" size="sm" :loading="isSaving" class="w-full" @click="saveManual">
          Add Restaurant
        </KinButton>
        <p v-if="saveError" class="text-xs text-status-failed">{{ saveError }}</p>
      </div>
    </KinModalSheet>

    <!-- Confirm delete -->
    <ConfirmDialog
      :show="!!restaurantToDelete"
      title="Remove Restaurant"
      :message="`Remove ${restaurantToDelete?.name} from your family? This won't affect existing meal plan entries.`"
      confirm-text="Remove"
      variant="danger"
      @confirm="doDelete"
      @cancel="restaurantToDelete = null"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { storeToRefs } from 'pinia'
import {
  PlusIcon,
  HeartIcon,
  StarIcon,
  BuildingStorefrontIcon,
  MapPinIcon,
  DocumentTextIcon,
  TrashIcon,
  PhoneIcon,
  ExclamationCircleIcon,
  CheckCircleIcon,
  Squares2X2Icon,
  ListBulletIcon,
} from '@heroicons/vue/24/outline'
import { HeartIcon as HeartIconSolid } from '@heroicons/vue/24/solid'
import api from '@/services/api'
import { useRestaurantsStore } from '@/stores/restaurants'
import { useAuthStore } from '@/stores/auth'
import { useNotification } from '@/composables/useNotification'
import FoodCard from '@/components/food/FoodCard.vue'
import PhotoUpload from '@/components/food/PhotoUpload.vue'
import TagPicker from '@/components/food/TagPicker.vue'
import SlidePanel from '@/components/common/SlidePanel.vue'
import FloatingActionButton from '@/components/common/FloatingActionButton.vue'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import ConfirmDialog from '@/components/common/ConfirmDialog.vue'
import KinSearch from '@/components/design-system/KinSearch.vue'
import KinButton from '@/components/design-system/KinButton.vue'
import KinChip from '@/components/design-system/KinChip.vue'
import KinEmptyState from '@/components/design-system/KinEmptyState.vue'
import KinModalSheet from '@/components/design-system/KinModalSheet.vue'
import KinInput from '@/components/design-system/KinInput.vue'
import KinTextarea from '@/components/design-system/KinTextarea.vue'

const restaurantsStore = useRestaurantsStore()
const { tags, selectedTagIds } = storeToRefs(restaurantsStore)
const authStore = useAuthStore()
const { success: notifySuccess, error: notifyError } = useNotification()

const isParent = computed(() => authStore.user?.family_role === 'parent' || authStore.user?.role === 'parent')

// Filters
const showFavoritesOnly = ref(false)
const displayedRestaurants = computed(() => {
  const list = restaurantsStore.filteredRestaurants
  return showFavoritesOnly.value ? list.filter(r => r.is_favorite) : list
})

// All food-scoped tags (server filters by scope=food).
const foodTags = computed(() => tags.value)
// Filter chips: show every food tag so the user can filter by any of them,
// even if nothing is applied yet.
const restaurantTags = computed(() => foodTags.value)
const isTagSelected = (tagId) => selectedTagIds.value.includes(tagId)
const toggleTagFilter = (tagId) => restaurantsStore.toggleTagFilter(tagId)

watch(selectedTagIds, () => {
  restaurantsStore.fetchRestaurants()
}, { deep: true })

const restaurantMeta = (r) => {
  const items = []
  if (r.address) items.push({ icon: MapPinIcon, text: r.address.length > 30 ? r.address.slice(0, 30) + '...' : r.address })
  if (r.family_average_rating > 0) items.push({ icon: StarIcon, text: Number(r.family_average_rating).toFixed(1), iconClass: 'text-[#C4975A]' })
  if (r.phone) items.push({ icon: PhoneIcon, text: r.phone })
  return items
}

const restaurantCardTags = (r) => (r.tags || []).map(t => t.name)

// Stable fallback gradient per restaurant — first food tag → accent family;
// otherwise a hash of the name keeps each restaurant card visually distinct.
const RESTAURANT_TAG_TO_GRADIENT = {
  Italian:  'peach',
  Mexican:  'sun',
  Asian:    'mint',
  Chinese:  'mint',
  Japanese: 'mint',
  Indian:   'sun',
  Pizza:    'peach',
  Cafe:     'lavender',
  Bakery:   'peach',
  Diner:    'warm',
  Dessert:  'peach',
  Snack:    'warm',
  Breakfast:'sun',
  Lunch:    'mint',
  Dinner:   'lavender',
}
const RESTAURANT_HASH_GRADIENTS = ['warm', 'lavender', 'peach', 'mint', 'sun', 'cool']
const restaurantFallbackGradient = (r) => {
  const tag = (r.tags || [])[0]
  const tagName = tag?.name
  if (tagName && RESTAURANT_TAG_TO_GRADIENT[tagName]) return RESTAURANT_TAG_TO_GRADIENT[tagName]
  const s = r.name || ''
  let h = 0
  for (let i = 0; i < s.length; i++) h = (h * 31 + s.charCodeAt(i)) | 0
  return RESTAURANT_HASH_GRADIENTS[Math.abs(h) % RESTAURANT_HASH_GRADIENTS.length]
}

// ── View mode (grid / compact) — same toggle pattern as RecipesTab ────────────
const viewMode = ref(localStorage.getItem('kinhold-restaurant-view') || 'grid')
const toggleViewMode = () => {
  viewMode.value = viewMode.value === 'grid' ? 'compact' : 'grid'
  localStorage.setItem('kinhold-restaurant-view', viewMode.value)
}

// Pass-through for absolute URLs; storage prefix for relative paths.
const resolveImageUrl = (path) => {
  if (!path) return null
  if (path.startsWith('http://') || path.startsWith('https://') || path.startsWith('/')) return path
  return `/storage/${path}`
}

// Inline gradient CSS for the compact-row thumbnail. Same wash math as
// KinPhotoCard's scoped fallback styles.
const RESTAURANT_FALLBACK_BG = {
  iridescent: 'var(--gradient-iridescent-subtle)',
  warm:       'var(--gradient-iridescent-warm)',
  lavender:   'radial-gradient(ellipse 100% 90% at 30% 20%, rgb(var(--accent-lavender-soft) / 0.85) 0%, transparent 70%)',
  peach:      'radial-gradient(ellipse 100% 90% at 30% 20%, rgb(var(--accent-peach-soft) / 0.85) 0%, transparent 70%)',
  mint:       'radial-gradient(ellipse 100% 90% at 30% 20%, rgb(var(--accent-mint-soft) / 0.85) 0%, transparent 70%)',
  sun:        'radial-gradient(ellipse 100% 90% at 30% 20%, rgb(var(--accent-sun-soft) / 0.85) 0%, transparent 70%)',
  cool:       'radial-gradient(ellipse 80% 70% at 18% 20%, rgb(var(--accent-lavender-soft) / 0.80) 0%, transparent 70%), radial-gradient(ellipse 70% 60% at 82% 80%, rgb(var(--accent-mint-soft) / 0.80) 0%, transparent 70%)',
}
const restaurantFallbackStyle = (r) => ({
  backgroundImage: RESTAURANT_FALLBACK_BG[restaurantFallbackGradient(r)] ?? RESTAURANT_FALLBACK_BG.warm,
})

// Tag creation (shared with form + detail)
const createTag = async ({ name, color }) => {
  try {
    const response = await api.post('/tags', { name, color, scope: 'food' })
    const newTag = response.data.tag
    // Refresh tag list so counts update.
    await restaurantsStore.fetchTags()
    return { success: true, tag: newTag }
  } catch (err) {
    notifyError(err.response?.data?.message || 'Failed to create tag', 4000)
    return { success: false }
  }
}

// Detail panel
const selectedRestaurant = ref(null)
const editFields = ref({ name: '', address: '', phone: '', google_maps_url: '', menu_url: '', image_url: '', tag_ids: [] })
const editNotes = ref('')
const pendingRating = ref(null)
const isDetailSaving = ref(false)

const openDetail = (restaurant) => {
  selectedRestaurant.value = restaurant
  editFields.value = {
    name: restaurant.name || '',
    address: restaurant.address || '',
    phone: restaurant.phone || '',
    google_maps_url: restaurant.google_maps_url || '',
    menu_url: restaurant.menu_url || '',
    image_url: restaurant.image_url || '',
    tag_ids: (restaurant.tags || []).map(t => t.id),
  }
  editNotes.value = restaurant.family_notes || restaurant.notes || ''
  pendingRating.value = null
}

const rateRestaurant = async (score) => {
  pendingRating.value = score
  const result = await restaurantsStore.rateRestaurant(selectedRestaurant.value.id, score)
  if (result.success) {
    const updated = restaurantsStore.restaurants.find(r => r.id === selectedRestaurant.value.id)
    if (updated) selectedRestaurant.value = updated
    notifySuccess('Rating saved', 3000)
  } else {
    notifyError(result.error || 'Failed to save rating', 4000)
    pendingRating.value = null
  }
}

const saveDetail = async () => {
  isDetailSaving.value = true
  const payload = {
    ...editFields.value,
    notes: editNotes.value,
  }
  const result = await restaurantsStore.updateRestaurant(selectedRestaurant.value.id, payload)
  isDetailSaving.value = false
  if (result.success) {
    selectedRestaurant.value = result.data
    await restaurantsStore.fetchTags()
    notifySuccess('Restaurant saved', 3000)
  } else {
    notifyError(result.error || 'Failed to save', 4000)
  }
}

// Delete
const restaurantToDelete = ref(null)
const confirmDelete = (restaurant) => {
  restaurantToDelete.value = restaurant
}
const doDelete = async () => {
  const id = restaurantToDelete.value.id
  restaurantToDelete.value = null
  selectedRestaurant.value = null
  const result = await restaurantsStore.deleteRestaurant(id)
  if (result.success) {
    notifySuccess('Restaurant removed', 3000)
  } else {
    notifyError(result.error || 'Failed to remove restaurant', 4000)
  }
}

// Add/Import modal
const showAddModal = ref(false)
const addTab = ref('import')
const addTabs = [
  { key: 'import', label: 'From URL' },
  { key: 'manual', label: 'Manual' },
]
const emptyForm = () => ({ name: '', address: '', phone: '', google_maps_url: '', menu_url: '', image_url: '', tag_ids: [] })

const uploadRestaurantImage = async (file) => {
  return await restaurantsStore.uploadImage(file)
}
const form = ref(emptyForm())
const importUrl = ref('')
const formErrors = ref({})
const saveError = ref('')
const isSaving = ref(false)
const isImporting = ref(false)
const previewData = ref(null)

const openAddModal = () => {
  form.value = emptyForm()
  importUrl.value = ''
  formErrors.value = {}
  saveError.value = ''
  previewData.value = null
  addTab.value = 'import'
  showAddModal.value = true
}

const closeAddModal = () => {
  showAddModal.value = false
  previewData.value = null
}

const resetPreview = () => {
  previewData.value = null
  saveError.value = ''
}

const previewImport = async () => {
  if (!importUrl.value.trim()) {
    formErrors.value.url = 'URL is required'
    return
  }
  formErrors.value = {}
  saveError.value = ''
  isImporting.value = true

  const result = await restaurantsStore.previewImport(importUrl.value)
  isImporting.value = false

  if (result.success) {
    const data = result.preview
    form.value = {
      name: data.name || '',
      address: data.address || '',
      phone: data.phone || '',
      google_maps_url: data.google_maps_url || '',
      menu_url: data.menu_url || '',
      image_url: data.image_url || '',
      tag_ids: [],
    }
    // Auto-create/attach food tags for any cuisines the importer picked up.
    if (Array.isArray(data.cuisines) && data.cuisines.length) {
      for (const name of data.cuisines) {
        const existing = restaurantsStore.tags.find(
          t => t.name.toLowerCase() === name.toLowerCase()
        )
        if (existing) {
          form.value.tag_ids.push(existing.id)
        } else {
          const created = await createTag({ name, color: '#C4975A' })
          if (created?.success && created.tag?.id) {
            form.value.tag_ids.push(created.tag.id)
          }
        }
      }
    }
    previewData.value = data
  } else {
    saveError.value = result.error || 'Failed to extract restaurant data'
  }
}

const saveFromPreview = async () => {
  formErrors.value = {}
  saveError.value = ''
  if (!form.value.name.trim()) {
    formErrors.value.name = 'Name is required'
    return
  }
  isSaving.value = true
  const result = await restaurantsStore.createRestaurant(form.value)
  isSaving.value = false
  if (result.success) {
    closeAddModal()
    await restaurantsStore.fetchTags()
    notifySuccess('Restaurant added', 3000)
  } else {
    saveError.value = result.error || 'Failed to save restaurant'
  }
}

const saveManual = async () => {
  formErrors.value = {}
  saveError.value = ''
  if (!form.value.name.trim()) {
    formErrors.value.name = 'Name is required'
    return
  }
  isSaving.value = true
  const result = await restaurantsStore.createRestaurant(form.value)
  isSaving.value = false
  if (result.success) {
    closeAddModal()
    await restaurantsStore.fetchTags()
    notifySuccess('Restaurant added', 3000)
  } else {
    saveError.value = result.error || 'Failed to add restaurant'
  }
}

onMounted(() => {
  restaurantsStore.fetchRestaurants()
  restaurantsStore.fetchTags()
})
</script>
