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
          class="flex-shrink-0 px-3 py-2.5 text-xs font-medium rounded-full transition-colors whitespace-nowrap"
          :class="showFavoritesOnly
            ? 'bg-red-500/10 text-red-600 dark:text-red-400 border border-red-300 dark:border-red-700'
            : 'bg-lavender-100 dark:bg-prussian-700 text-lavender-600 dark:text-lavender-400 hover:bg-lavender-200 dark:hover:bg-prussian-600'"
          @click="showFavoritesOnly = !showFavoritesOnly"
        >
          <span class="flex items-center gap-1">
            <HeartIcon class="w-3.5 h-3.5" />
            Favorites
          </span>
        </button>
        <button
          class="hidden md:flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-[#C4975A] hover:bg-[#D4A96A] rounded-[10px] transition-colors whitespace-nowrap"
          @click="openAddModal"
        >
          <PlusIcon class="w-4 h-4" />
          Add Restaurant
        </button>
      </div>
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
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-2">
        <div
          v-for="restaurant in displayedRestaurants"
          :key="restaurant.id"
          class="bg-white dark:bg-prussian-800 border border-lavender-200 dark:border-prussian-700 rounded-xl p-4 cursor-pointer hover:border-[#C4975A]/50 hover:shadow-sm transition-all"
          @click="openDetail(restaurant)"
        >
          <!-- Name + favorite -->
          <div class="flex items-start justify-between gap-2 mb-1">
            <h3 class="font-semibold text-prussian-500 dark:text-lavender-200 text-sm leading-tight">{{ restaurant.name }}</h3>
            <button
              class="flex-shrink-0 p-1 rounded-full transition-colors"
              :class="restaurant.is_favorite ? 'text-red-500' : 'text-lavender-300 dark:text-prussian-500 hover:text-red-400'"
              @click.stop="restaurantsStore.toggleFavorite(restaurant.id)"
            >
              <HeartIcon class="w-4 h-4" :class="{ 'fill-current': restaurant.is_favorite }" />
            </button>
          </div>

          <!-- Cuisine tag -->
          <p v-if="restaurant.cuisine" class="text-xs text-lavender-500 dark:text-lavender-400 mb-2">{{ restaurant.cuisine }}</p>

          <!-- Address -->
          <p v-if="restaurant.address" class="text-xs text-lavender-400 dark:text-lavender-500 truncate mb-2">{{ restaurant.address }}</p>

          <!-- Star rating -->
          <div class="flex items-center gap-1">
            <template v-for="n in 5" :key="n">
              <StarIcon
                class="w-3.5 h-3.5"
                :class="n <= Math.round(restaurant.family_average_rating || 0)
                  ? 'text-[#C4975A] fill-current'
                  : 'text-lavender-200 dark:text-prussian-600'"
              />
            </template>
            <span v-if="restaurant.family_average_rating" class="text-xs text-lavender-400 dark:text-lavender-500 ml-1">
              {{ Number(restaurant.family_average_rating).toFixed(1) }}
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Mobile FAB -->
    <FloatingActionButton @click="openAddModal" />

    <!-- Detail slide panel -->
    <SlidePanel :show="!!selectedRestaurant" :title="selectedRestaurant?.name || ''" @close="selectedRestaurant = null">
      <div v-if="selectedRestaurant" class="p-6 space-y-5">
        <!-- Meta -->
        <div class="space-y-1">
          <p v-if="selectedRestaurant.cuisine" class="text-sm text-lavender-500 dark:text-lavender-400">{{ selectedRestaurant.cuisine }}</p>
          <p v-if="selectedRestaurant.address" class="text-sm text-prussian-400 dark:text-lavender-300">{{ selectedRestaurant.address }}</p>
          <p v-if="selectedRestaurant.phone" class="text-sm text-prussian-400 dark:text-lavender-300">{{ selectedRestaurant.phone }}</p>
        </div>

        <!-- Links -->
        <div class="flex flex-wrap gap-2">
          <a
            v-if="selectedRestaurant.google_maps_url"
            :href="selectedRestaurant.google_maps_url"
            target="_blank"
            rel="noopener noreferrer"
            class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium bg-lavender-100 dark:bg-prussian-700 text-prussian-500 dark:text-lavender-300 rounded-full hover:bg-lavender-200 dark:hover:bg-prussian-600 transition-colors"
            @click.stop
          >
            <MapPinIcon class="w-3.5 h-3.5" />
            Maps
          </a>
          <a
            v-if="selectedRestaurant.menu_url"
            :href="selectedRestaurant.menu_url"
            target="_blank"
            rel="noopener noreferrer"
            class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium bg-lavender-100 dark:bg-prussian-700 text-prussian-500 dark:text-lavender-300 rounded-full hover:bg-lavender-200 dark:hover:bg-prussian-600 transition-colors"
            @click.stop
          >
            <DocumentTextIcon class="w-3.5 h-3.5" />
            Menu
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

        <!-- Notes -->
        <div>
          <label class="block text-xs font-medium text-lavender-400 dark:text-lavender-500 mb-1.5 uppercase tracking-wide">Family Notes</label>
          <textarea
            v-model="editNotes"
            rows="3"
            class="w-full text-sm bg-lavender-50 dark:bg-prussian-700 border border-lavender-200 dark:border-prussian-600 rounded-[10px] px-3 py-2 text-prussian-500 dark:text-lavender-200 placeholder-lavender-400 focus:outline-none focus:ring-1 focus:ring-[#C4975A]/30 focus:border-[#C4975A] transition-colors resize-none"
            placeholder="Add notes about this restaurant..."
          />
          <button
            class="mt-2 px-4 py-2 text-sm font-medium text-white bg-[#C4975A] hover:bg-[#D4A96A] rounded-[10px] transition-colors"
            @click="saveNotes"
          >
            Save Notes
          </button>
        </div>

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
      <!-- Source tabs -->
      <div class="flex border-b border-lavender-200 dark:border-prussian-700 mb-5">
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
          <span v-if="addTab === tab.key" class="absolute bottom-0 left-0 right-0 h-0.5 bg-[#C4975A] rounded-full" />
        </button>
      </div>

      <!-- Manual form -->
      <div v-if="addTab === 'manual'" class="px-6 pb-6 space-y-4">
        <BaseInput v-model="form.name" label="Name *" placeholder="e.g. Gusto Pizzeria" :error="formErrors.name" />
        <BaseInput v-model="form.cuisine" label="Cuisine" placeholder="e.g. Italian, Mexican..." />
        <BaseInput v-model="form.address" label="Address" placeholder="123 Main St" />
        <BaseInput v-model="form.phone" label="Phone" placeholder="+1 (555) 000-0000" />
        <BaseInput v-model="form.google_maps_url" label="Google Maps URL" placeholder="https://maps.google.com/..." />
        <BaseInput v-model="form.menu_url" label="Menu URL" placeholder="https://..." />
        <div>
          <label class="block text-sm font-medium text-prussian-400 dark:text-lavender-300 mb-1">Notes</label>
          <textarea
            v-model="form.notes"
            rows="2"
            class="w-full text-sm bg-lavender-50 dark:bg-prussian-700 border border-lavender-200 dark:border-prussian-600 rounded-[10px] px-3 py-2 text-prussian-500 dark:text-lavender-200 placeholder-lavender-400 focus:outline-none focus:ring-1 focus:ring-[#C4975A]/30 focus:border-[#C4975A] transition-colors resize-none"
            placeholder="Any notes..."
          />
        </div>
        <BaseButton variant="primary" :loading="isSaving" class="w-full" @click="saveManual">
          Add Restaurant
        </BaseButton>
        <p v-if="saveError" class="text-xs text-red-500">{{ saveError }}</p>
      </div>

      <!-- Import form -->
      <div v-else class="px-6 pb-6 space-y-4">
        <p class="text-sm text-lavender-500 dark:text-lavender-400">Paste a Google Maps URL to auto-import restaurant details.</p>
        <BaseInput v-model="importUrl" label="Google Maps URL" placeholder="https://maps.google.com/..." :error="formErrors.url" />
        <BaseButton variant="primary" :loading="isSaving" class="w-full" @click="saveImport">
          Import Restaurant
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
import { ref, computed, onMounted } from 'vue'
import {
  MagnifyingGlassIcon,
  PlusIcon,
  HeartIcon,
  StarIcon,
  BuildingStorefrontIcon,
  MapPinIcon,
  DocumentTextIcon,
  TrashIcon,
} from '@heroicons/vue/24/outline'
import { useRestaurantsStore } from '@/stores/restaurants'
import { useAuthStore } from '@/stores/auth'
import { useNotification } from '@/composables/useNotification'
import SlidePanel from '@/components/common/SlidePanel.vue'
import BaseModal from '@/components/common/BaseModal.vue'
import BaseInput from '@/components/common/BaseInput.vue'
import BaseButton from '@/components/common/BaseButton.vue'
import FloatingActionButton from '@/components/common/FloatingActionButton.vue'
import EmptyState from '@/components/common/EmptyState.vue'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import ConfirmDialog from '@/components/common/ConfirmDialog.vue'

const restaurantsStore = useRestaurantsStore()
const authStore = useAuthStore()
const { success: notifySuccess, error: notifyError } = useNotification()

const isParent = computed(() => authStore.user?.family_role === 'parent' || authStore.user?.role === 'parent')

// Filters
const showFavoritesOnly = ref(false)
const displayedRestaurants = computed(() => {
  const list = restaurantsStore.filteredRestaurants
  return showFavoritesOnly.value ? list.filter(r => r.is_favorite) : list
})

// Detail panel
const selectedRestaurant = ref(null)
const editNotes = ref('')
const pendingRating = ref(null)

const openDetail = (restaurant) => {
  selectedRestaurant.value = restaurant
  editNotes.value = restaurant.family_notes || restaurant.notes || ''
  pendingRating.value = null
}

const rateRestaurant = async (score) => {
  pendingRating.value = score
  const result = await restaurantsStore.rateRestaurant(selectedRestaurant.value.id, score)
  if (result.success) {
    // Refresh the selected restaurant from the store
    const updated = restaurantsStore.restaurants.find(r => r.id === selectedRestaurant.value.id)
    if (updated) selectedRestaurant.value = updated
    notifySuccess('Rating saved', 3000)
  } else {
    notifyError(result.error || 'Failed to save rating', 4000)
    pendingRating.value = null
  }
}

const saveNotes = async () => {
  const result = await restaurantsStore.updateRestaurant(selectedRestaurant.value.id, { notes: editNotes.value })
  if (result.success) {
    selectedRestaurant.value = result.data
    notifySuccess('Notes saved', 3000)
  } else {
    notifyError(result.error || 'Failed to save notes', 4000)
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
const addTab = ref('manual')
const addTabs = [
  { key: 'manual', label: 'Manual' },
  { key: 'import', label: 'Import from URL' },
]
const form = ref({ name: '', cuisine: '', address: '', phone: '', google_maps_url: '', menu_url: '', notes: '' })
const importUrl = ref('')
const formErrors = ref({})
const saveError = ref('')
const isSaving = ref(false)

const openAddModal = () => {
  form.value = { name: '', cuisine: '', address: '', phone: '', google_maps_url: '', menu_url: '', notes: '' }
  importUrl.value = ''
  formErrors.value = {}
  saveError.value = ''
  addTab.value = 'manual'
  showAddModal.value = true
}

const closeAddModal = () => {
  showAddModal.value = false
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
    notifySuccess('Restaurant added', 3000)
  } else {
    saveError.value = result.error || 'Failed to add restaurant'
  }
}

const saveImport = async () => {
  formErrors.value = {}
  saveError.value = ''
  if (!importUrl.value.trim()) {
    formErrors.value.url = 'URL is required'
    return
  }
  isSaving.value = true
  const result = await restaurantsStore.importRestaurant(importUrl.value)
  isSaving.value = false
  if (result.success) {
    closeAddModal()
    notifySuccess('Restaurant imported', 3000)
  } else {
    saveError.value = result.error || 'Failed to import restaurant'
  }
}

onMounted(() => {
  restaurantsStore.fetchRestaurants()
})
</script>
