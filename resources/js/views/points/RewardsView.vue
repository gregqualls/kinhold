<template>
  <div class="p-4 md:p-6 max-w-3xl">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center gap-3">
        <RouterLink to="/points" class="btn-ghost btn-sm rounded-lg">
          <ChevronLeftIcon class="w-5 h-5" />
        </RouterLink>
        <h1 class="text-2xl font-bold font-heading text-prussian-500 dark:text-lavender-200">Rewards</h1>
      </div>
      <div class="flex items-center gap-3">
        <span class="text-sm font-bold font-mono text-wisteria-600 dark:text-wisteria-400">
          {{ pointsStore.bank }} pts
        </span>
        <button v-if="isParent && !showForm" class="btn-primary btn-sm" @click="openCreateForm">
          + Add Reward
        </button>
      </div>
    </div>

    <!-- Create/Edit Form -->
    <RewardForm
      v-if="showForm"
      :reward="editingReward"
      :is-editing="!!editingReward"
      @save="handleSave"
      @cancel="closeForm"
    />

    <!-- Search & Filter Toolbar -->
    <div v-if="pointsStore.rewards.length > 0" class="mb-4 space-y-3">
      <!-- Search -->
      <input
        v-model="searchQuery"
        type="text"
        class="input-base w-full"
        placeholder="Search rewards..."
        aria-label="Search rewards"
      />

      <!-- Filter chips + Sort -->
      <div class="flex flex-wrap items-center gap-2">
        <button
          v-for="f in filterOptions"
          :key="f.value"
          class="px-3 py-1 rounded-full text-xs font-medium transition-colors"
          :class="activeFilter === f.value
            ? 'bg-wisteria-100 text-wisteria-700 dark:bg-wisteria-900/40 dark:text-wisteria-300 ring-1 ring-wisteria-300 dark:ring-wisteria-600'
            : 'bg-lavender-100 text-lavender-600 dark:bg-prussian-700 dark:text-lavender-400 hover:bg-lavender-200 dark:hover:bg-prussian-600'"
          @click="activeFilter = f.value"
        >
          {{ f.label }}
        </button>

        <select v-model="sortBy" class="input-base ml-auto text-xs py-1 w-auto">
          <option value="sort_order">Default</option>
          <option value="price_asc">Price: Low → High</option>
          <option value="price_desc">Price: High → Low</option>
          <option value="name">Name A-Z</option>
          <option value="newest">Newest</option>
        </select>
      </div>
    </div>

    <!-- Rewards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
      <RewardCard
        v-for="reward in filteredRewards"
        :key="reward.id"
        :reward="reward"
        :bank="pointsStore.bank"
        :is-parent="isParent"
        @purchase="handlePurchase"
        @edit="openEditForm"
        @delete="handleDelete"
      />
    </div>

    <!-- Empty state -->
    <div v-if="filteredRewards.length === 0 && pointsStore.rewards.length > 0" class="card p-8 text-center">
      <p class="text-lavender-500 dark:text-lavender-400">No rewards match your filters.</p>
    </div>

    <div v-if="pointsStore.rewards.length === 0 && !showForm" class="card p-8 text-center">
      <p class="text-lavender-500 dark:text-lavender-400">No rewards yet.</p>
      <p v-if="isParent" class="text-sm text-lavender-400 mt-1">Create some rewards for your family!</p>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref, computed, watch } from 'vue'
import { storeToRefs } from 'pinia'
import { usePointsStore } from '@/stores/points'
import { useAuthStore } from '@/stores/auth'
import RewardCard from '@/components/points/RewardCard.vue'
import RewardForm from '@/components/points/RewardForm.vue'
import { ChevronLeftIcon } from '@heroicons/vue/24/outline'

const pointsStore = usePointsStore()
const authStore = useAuthStore()
const { isParent } = storeToRefs(authStore)

// Form state
const showForm = ref(false)
const editingReward = ref(null)

const openCreateForm = () => {
  editingReward.value = null
  showForm.value = true
}

const openEditForm = (reward) => {
  editingReward.value = reward
  showForm.value = true
}

const closeForm = () => {
  showForm.value = false
  editingReward.value = null
}

const handleSave = async (data) => {
  if (editingReward.value) {
    const result = await pointsStore.updateReward(editingReward.value.id, data)
    if (result.success) {
      closeForm()
      await pointsStore.fetchRewards()
    }
  } else {
    const result = await pointsStore.createReward(data)
    if (result.success) closeForm()
  }
}

const handlePurchase = async (rewardId) => {
  const result = await pointsStore.purchaseReward(rewardId)
  if (result.success) {
    // Refresh rewards to get updated stock counts
    await pointsStore.fetchRewards()
  }
}

const handleDelete = async (rewardId) => {
  await pointsStore.deleteReward(rewardId)
}

// Search, filter, sort state
const searchQuery = ref('')
const activeFilter = ref(localStorage.getItem('rewards_filter') || 'all')
const sortBy = ref(localStorage.getItem('rewards_sort') || 'sort_order')

// Persist preferences
watch(activeFilter, (v) => localStorage.setItem('rewards_filter', v))
watch(sortBy, (v) => localStorage.setItem('rewards_sort', v))

const filterOptions = [
  { label: 'All', value: 'all' },
  { label: 'Affordable', value: 'affordable' },
  { label: 'Available', value: 'available' },
]

const filteredRewards = computed(() => {
  let list = [...pointsStore.rewards]

  // Search
  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase()
    list = list.filter((r) =>
      r.title.toLowerCase().includes(q) ||
      (r.description && r.description.toLowerCase().includes(q)),
    )
  }

  // Filter
  if (activeFilter.value === 'affordable') {
    list = list.filter((r) => pointsStore.bank >= r.point_cost)
  } else if (activeFilter.value === 'available') {
    list = list.filter((r) => r.is_purchasable !== false)
  }

  // Sort
  if (sortBy.value === 'price_asc') {
    list.sort((a, b) => a.point_cost - b.point_cost)
  } else if (sortBy.value === 'price_desc') {
    list.sort((a, b) => b.point_cost - a.point_cost)
  } else if (sortBy.value === 'name') {
    list.sort((a, b) => a.title.localeCompare(b.title))
  } else if (sortBy.value === 'newest') {
    list.sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
  } else {
    // Default: sort_order then price
    list.sort((a, b) => (a.sort_order - b.sort_order) || (a.point_cost - b.point_cost))
  }

  return list
})

onMounted(async () => {
  await Promise.all([
    pointsStore.fetchRewards(),
    pointsStore.fetchBank(),
  ])
})
</script>
