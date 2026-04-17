<template>
  <div class="h-full flex flex-col overflow-hidden">
    <!-- Search + controls -->
    <div class="px-4 md:px-6 pt-4 pb-2 space-y-3">
      <div class="flex items-center gap-3">
        <div class="flex-1 relative">
          <MagnifyingGlassIcon class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-lavender-400" />
          <input
            v-model="restaurantsStore.searchQuery"
            type="text"
            placeholder="Search restaurants..."
            class="w-full pl-9 pr-4 py-2.5 text-sm bg-lavender-50 dark:bg-prussian-700 border border-lavender-200 dark:border-prussian-600 rounded-[10px] text-prussian-500 dark:text-lavender-200 placeholder-lavender-400 dark:placeholder-lavender-500 focus:outline-none focus:ring-1 focus:ring-[#C4975A]/30 focus:border-[#C4975A] transition-colors"
          />
        </div>
        <button
          class="hidden md:flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-[#C4975A] hover:bg-[#D4A96A] rounded-[10px] transition-colors whitespace-nowrap"
          @click="openAddModal"
        >
          <PlusIcon class="w-4 h-4" />
          Add Restaurant
        </button>
      </div>

      <!-- Filter row -->
      <div class="flex items-center gap-2 overflow-x-auto scrollbar-hide pb-1">
        <!-- Favorites chip -->
        <button
          class="flex-shrink-0 px-3 py-1.5 text-xs font-medium rounded-full transition-colors whitespace-nowrap"
          :class="showFavoritesOnly
            ? 'bg-red-500/10 text-red-600 dark:text-red-400 border border-red-300 dark:border-red-700'
            : 'bg-lavender-100 dark:bg-prussian-700 text-lavender-600 dark:text-lavender-400 hover:bg-lavender-200 dark:hover:bg-prussian-600'"
          @click="showFavoritesOnly = !showFavoritesOnly"
        >
          <span class="flex items-center gap-1">
            <HeartIcon class="w-3 h-3" />
            Favorites
          </span>
        </button>

        <!-- Divider -->
        <div v-if="restaurantTags.length" class="w-px h-4 bg-lavender-200 dark:bg-prussian-700 flex-shrink-0"></div>

        <!-- Tag chips -->
        <button
          v-for="tag in restaurantTags"
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

    <!-- Content -->
    <div class="flex-1 overflow-y-auto px-4 md:px-6 pb-24 md:pb-8">
      <!-- Loading -->
      <div v-if="restaurantsStore.isLoading" class="flex items-center justify-center py-16">
        <LoadingSpinner size="lg" />
      </div>

      <!-- Empty state -->
      <EmptyState
        v-else-if="displayedRestaurants.length === 0"
        :icon="BuildingStorefrontIcon"
        title="No restaurants yet"
        :description="restaurantsStore.searchQuery ? 'No restaurants match your search.' : 'Add your favourite spots to order from or visit.'"
        action-text="Add Restaurant"
        @action="openAddModal"
      />

      <!-- Grid -->
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-2">
        <FoodCard
          v-for="restaurant in displayedRestaurants"
          :key="restaurant.id"
          :title="restaurant.name"
          :image-url="restaurant.image_url"
          :placeholder-icon="BuildingStorefrontIcon"
          :is-favorite="!!restaurant.is_favorite"
          :meta-items="restaurantMeta(restaurant)"
          :tags="restaurantCardTags(restaurant)"
          @click="openDetail(restaurant)"
          @toggle-favorite="restaurantsStore.toggleFavorite(restaurant.id)"
        />
      </div>
    </div>

    <!-- Mobile FAB -->
    <FloatingActionButton @click="openAddModal" />

    <!-- Detail slide panel -->
    <SlidePanel :show="!!selectedRestaurant" title="Restaurant Details" @close="selectedRestaurant = null">
      <div v-if="selectedRestaurant" class="p-6 space-y-5">
        <!-- Editable fields -->
        <div class="space-y-3">
          <div>
            <label class="block text-xs font-medium text-lavender-400 dark:text-lavender-500 mb-1.5 uppercase tracking-wide">Name</label>
            <input
              v-model="editFields.name"
              type="text"
              class="w-full text-sm bg-lavender-50 dark:bg-prussian-700 border border-lavender-200 dark:border-prussian-600 rounded-[10px] px-3 py-2 text-prussian-500 dark:text-lavender-200 focus:outline-none focus:ring-1 focus:ring-[#C4975A]/30 focus:border-[#C4975A] transition-colors"
              placeholder="Restaurant name"
            />
          </div>
          <div>
            <label class="block text-xs font-medium text-lavender-400 dark:text-lavender-500 mb-1.5 uppercase tracking-wide">Cuisine</label>
            <input
              v-model="editFields.cuisine"
              type="text"
              class="w-full text-sm bg-lavender-50 dark:bg-prussian-700 border border-lavender-200 dark:border-prussian-600 rounded-[10px] px-3 py-2 text-prussian-500 dark:text-lavender-200 focus:outline-none focus:ring-1 focus:ring-[#C4975A]/30 focus:border-[#C4975A] transition-colors"
              placeholder="e.g. Italian, Chinese, Mexican"
            />
          </div>
          <div>
            <label class="block text-xs font-medium text-lavender-400 dark:text-lavender-500 mb-1.5 uppercase tracking-wide">Address</label>
            <input
              v-model="editFields.address"
              type="text"
              class="w-full text-sm bg-lavender-50 dark:bg-prussian-700 border border-lavender-200 dark:border-prussian-600 rounded-[10px] px-3 py-2 text-prussian-500 dark:text-lavender-200 focus:outline-none focus:ring-1 focus:ring-[#C4975A]/30 focus:border-[#C4975A] transition-colors"
              placeholder="Street address"
            />
          </div>
          <div>
            <label class="block text-xs font-medium text-lavender-400 dark:text-lavender-500 mb-1.5 uppercase tracking-wide">Phone</label>
            <input
              v-model="editFields.phone"
              type="tel"
              class="w-full text-sm bg-lavender-50 dark:bg-prussian-700 border border-lavender-200 dark:border-prussian-600 rounded-[10px] px-3 py-2 text-prussian-500 dark:text-lavender-200 focus:outline-none focus:ring-1 focus:ring-[#C4975A]/30 focus:border-[#C4975A] transition-colors"
              placeholder="Phone number"
            />
          </div>
          <div>
            <label class="block text-xs font-medium text-lavender-400 dark:text-lavender-500 mb-1.5 uppercase tracking-wide">Website / Menu URL</label>
            <input
              v-model="editFields.menu_url"
              type="url"
              class="w-full text-sm bg-lavender-50 dark:bg-prussian-700 border border-lavender-200 dark:border-prussian-600 rounded-[10px] px-3 py-2 text-prussian-500 dark:text-lavender-200 focus:outline-none focus:ring-1 focus:ring-[#C4975A]/30 focus:border-[#C4975A] transition-colors"
              placeholder="https://..."
            />
          </div>
          <div>
            <label class="block text-xs font-medium text-lavender-400 dark:text-lavender-500 mb-1.5 uppercase tracking-wide">Google Maps URL</label>
            <input
              v-model="editFields.google_maps_url"
              type="url"
              class="w-full text-sm bg-lavender-50 dark:bg-prussian-700 border border-lavender-200 dark:border-prussian-600 rounded-[10px] px-3 py-2 text-prussian-500 dark:text-lavender-200 focus:outline-none focus:ring-1 focus:ring-[#C4975A]/30 focus:border-[#C4975A] transition-colors"
              placeholder="https://maps.google.com/..."
            />
          </div>
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
            class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium bg-lavender-100 dark:bg-prussian-700 text-prussian-500 dark:text-lavender-300 rounded-full hover:bg-lavender-200 dark:hover:bg-prussian-600 transition-colors"
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
            class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium bg-lavender-100 dark:bg-prussian-700 text-prussian-500 dark:text-lavender-300 rounded-full hover:bg-lavender-200 dark:hover:bg-prussian-600 transition-colors"
            @click.stop
          >
            <DocumentTextIcon class="w-3.5 h-3.5" />
            Website
          </a>
          <a
            v-if="editFields.phone"
            :href="'tel:' + editFields.phone"
            class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium bg-lavender-100 dark:bg-prussian-700 text-prussian-500 dark:text-lavender-300 rounded-full hover:bg-lavender-200 dark:hover:bg-prussian-600 transition-colors"
            @click.stop
          >
            <PhoneIcon class="w-3.5 h-3.5" />
            Call
          </a>
        </div>

        <!-- Star rating -->
        <div>
          <p class="text-xs font-medium text-lavender-400 dark:text-lavender-500 mb-2 uppercase tracking-wide">Your Rating</p>
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
                  : 'text-lavender-200 dark:text-prussian-600'"
              />
            </button>
          </div>
        </div>

        <!-- Family notes -->
        <div>
          <label class="block text-xs font-medium text-lavender-400 dark:text-lavender-500 mb-1.5 uppercase tracking-wide">Family Notes</label>
          <textarea
            v-model="editNotes"
            rows="3"
            class="w-full text-sm bg-lavender-50 dark:bg-prussian-700 border border-lavender-200 dark:border-prussian-600 rounded-[10px] px-3 py-2 text-prussian-500 dark:text-lavender-200 placeholder-lavender-400 focus:outline-none focus:ring-1 focus:ring-[#C4975A]/30 focus:border-[#C4975A] transition-colors resize-none"
            placeholder="Add notes about this restaurant..."
          ></textarea>
        </div>

        <!-- Save all -->
        <BaseButton variant="primary" :loading="isDetailSaving" class="w-full" @click="saveDetail">
          Save Changes
        </BaseButton>

        <!-- Actions -->
        <div v-if="isParent" class="pt-2 border-t border-lavender-200 dark:border-prussian-700">
          <button
            class="flex items-center gap-2 text-sm text-red-500 hover:text-red-600 transition-colors"
            @click="confirmDelete(selectedRestaurant)"
          >
            <TrashIcon class="w-4 h-4" />
            Remove from family
          </button>
        </div>
      </div>
    </SlidePanel>

    <!-- Add/Import modal -->
    <BaseModal :show="showAddModal" title="Add Restaurant" size="md" @close="closeAddModal">
      <!-- Tab switcher (only before preview) -->
      <div v-if="!previewData" class="flex border-b border-lavender-200 dark:border-prussian-700 mb-5">
        <button
          v-for="tab in addTabs"
          :key="tab.key"
          class="px-4 py-2.5 text-sm font-medium transition-colors relative"
          :class="addTab === tab.key
            ? 'text-[#C4975A]'
            : 'text-lavender-500 dark:text-lavender-400 hover:text-prussian-500 dark:hover:text-lavender-200'"
          @click="addTab = tab.key"
        >
          {{ tab.label }}
          <span v-if="addTab === tab.key" class="absolute bottom-0 left-0 right-0 h-0.5 bg-[#C4975A] rounded-full"></span>
        </button>
      </div>

      <!-- Import: URL input -->
      <div v-if="addTab === 'import' && !previewData" class="px-6 pb-6 space-y-4">
        <BaseInput v-model="importUrl" label="Restaurant URL" placeholder="https://www.restaurant-website.com" :error="formErrors.url" @keydown.enter.prevent="previewImport" />

        <!-- Error -->
        <div
          v-if="saveError"
          class="flex items-start gap-2 p-3 bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-800/30 rounded-xl"
        >
          <ExclamationCircleIcon class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" />
          <div class="text-sm">
            <p class="font-medium text-red-600 dark:text-red-400">Import failed</p>
            <p class="text-lavender-600 dark:text-lavender-400">{{ saveError }}</p>
          </div>
        </div>

        <p class="text-xs text-lavender-400 dark:text-lavender-500">
          Paste the restaurant's website URL. You can edit all details before saving.
        </p>

        <BaseButton variant="primary" :loading="isImporting" :disabled="!importUrl" class="w-full" @click="previewImport">
          {{ isImporting ? 'Extracting...' : 'Preview Restaurant' }}
        </BaseButton>
      </div>

      <!-- Import: loading spinner -->
      <div v-if="isImporting" class="flex flex-col items-center justify-center py-8">
        <LoadingSpinner size="lg" />
        <p class="mt-3 text-sm text-lavender-500 dark:text-lavender-400">Extracting restaurant details...</p>
      </div>

      <!-- Import: preview form (edit before save) -->
      <div v-if="previewData && !isImporting" class="px-6 pb-6 space-y-4">
        <div class="flex items-center gap-2 p-3 bg-[#5B8C6A]/10 border border-[#5B8C6A]/20 rounded-xl">
          <CheckCircleIcon class="w-5 h-5 text-[#5B8C6A] flex-shrink-0" />
          <p class="text-sm text-[#5B8C6A]">
            Details extracted! Review and edit below, then save.
          </p>
        </div>

        <BaseInput v-model="form.name" label="Name *" placeholder="Restaurant name" :error="formErrors.name" />
        <BaseInput v-model="form.cuisine" label="Cuisine" placeholder="e.g. Italian, Chinese..." />
        <BaseInput v-model="form.address" label="Address" placeholder="Street address" />
        <BaseInput v-model="form.phone" label="Phone" placeholder="Phone number" />
        <BaseInput v-model="form.menu_url" label="Website" placeholder="https://..." />
        <PhotoUpload v-model="form.image_url" label="Photo" :uploader="uploadRestaurantImage" />
        <TagPicker v-model="form.tag_ids" :tags="foodTags" :on-create="createTag" />

        <div class="flex gap-3">
          <BaseButton variant="primary" :loading="isSaving" class="flex-1" @click="saveFromPreview">
            Save Restaurant
          </BaseButton>
          <button
            class="px-4 py-2 text-sm text-lavender-500 dark:text-lavender-400 hover:text-prussian-500 dark:hover:text-lavender-200 transition-colors"
            @click="resetPreview"
          >
            Back
          </button>
        </div>
        <p v-if="saveError" class="text-xs text-red-500">{{ saveError }}</p>
      </div>

      <!-- Manual form -->
      <div v-if="addTab === 'manual' && !previewData" class="px-6 pb-6 space-y-4">
        <BaseInput v-model="form.name" label="Name *" placeholder="e.g. Gusto Pizzeria" :error="formErrors.name" />
        <BaseInput v-model="form.cuisine" label="Cuisine" placeholder="e.g. Italian, Mexican..." />
        <BaseInput v-model="form.address" label="Address" placeholder="123 Main St" />
        <BaseInput v-model="form.phone" label="Phone" placeholder="+1 (555) 000-0000" />
        <BaseInput v-model="form.menu_url" label="Website" placeholder="https://..." />
        <PhotoUpload v-model="form.image_url" label="Photo" :uploader="uploadRestaurantImage" />
        <TagPicker v-model="form.tag_ids" :tags="foodTags" :on-create="createTag" />
        <BaseButton variant="primary" :loading="isSaving" class="w-full" @click="saveManual">
          Add Restaurant
        </BaseButton>
        <p v-if="saveError" class="text-xs text-red-500">{{ saveError }}</p>
      </div>
    </BaseModal>

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
  MagnifyingGlassIcon,
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
} from '@heroicons/vue/24/outline'
import api from '@/services/api'
import { useRestaurantsStore } from '@/stores/restaurants'
import { useAuthStore } from '@/stores/auth'
import { useNotification } from '@/composables/useNotification'
import FoodCard from '@/components/food/FoodCard.vue'
import PhotoUpload from '@/components/food/PhotoUpload.vue'
import TagPicker from '@/components/food/TagPicker.vue'
import SlidePanel from '@/components/common/SlidePanel.vue'
import BaseModal from '@/components/common/BaseModal.vue'
import BaseInput from '@/components/common/BaseInput.vue'
import BaseButton from '@/components/common/BaseButton.vue'
import FloatingActionButton from '@/components/common/FloatingActionButton.vue'
import EmptyState from '@/components/common/EmptyState.vue'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import ConfirmDialog from '@/components/common/ConfirmDialog.vue'

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

const restaurantCardTags = (r) => {
  const tagNames = (r.tags || []).map(t => t.name)
  if (r.cuisine && !tagNames.includes(r.cuisine)) tagNames.unshift(r.cuisine)
  return tagNames
}

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
const editFields = ref({ name: '', cuisine: '', address: '', phone: '', google_maps_url: '', menu_url: '', image_url: '', tag_ids: [] })
const editNotes = ref('')
const pendingRating = ref(null)
const isDetailSaving = ref(false)

const openDetail = (restaurant) => {
  selectedRestaurant.value = restaurant
  editFields.value = {
    name: restaurant.name || '',
    cuisine: restaurant.cuisine || '',
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
const emptyForm = () => ({ name: '', cuisine: '', address: '', phone: '', google_maps_url: '', menu_url: '', image_url: '', tag_ids: [] })

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
      cuisine: data.cuisine || '',
      address: data.address || '',
      phone: data.phone || '',
      google_maps_url: data.google_maps_url || '',
      menu_url: data.menu_url || '',
      image_url: data.image_url || '',
      tag_ids: [],
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
