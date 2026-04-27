<template>
  <div class="p-4 md:p-6 max-w-3xl">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6 gap-3 flex-wrap">
      <div class="flex items-center gap-3">
        <KinButton variant="ghost" size="sm" icon-only aria-label="Back to Points" to="/points">
          <ChevronLeftIcon class="w-5 h-5" />
        </KinButton>
        <h1 class="text-2xl font-bold font-heading text-ink-primary">Rewards</h1>
      </div>
      <div class="flex items-center gap-3">
        <span class="text-sm font-bold font-mono text-accent-lavender-bold">
          {{ pointsStore.bank }} pts
        </span>
        <KinButton v-if="isParent && !showForm" variant="primary" size="sm" @click="openCreateForm">
          <template #leading>
            <PlusIcon class="w-4 h-4" />
          </template>
          Add Reward
        </KinButton>
      </div>
    </div>

    <!-- Create/Edit Form -->
    <RewardForm
      v-if="showForm"
      data-reward-form
      :reward="editingReward"
      :is-editing="!!editingReward"
      @save="handleSave"
      @cancel="closeForm"
    />

    <!-- Search & Filter Toolbar -->
    <div v-if="pointsStore.rewards.length > 0" class="mb-4 space-y-3">
      <!-- Search -->
      <KinSearch
        v-model="searchQuery"
        placeholder="Search rewards..."
        aria-label="Search rewards"
      />

      <!-- Filter chips + Sort -->
      <div class="flex flex-wrap items-center gap-2">
        <KinChip
          v-for="f in filterOptions"
          :key="f.value"
          variant="filter"
          size="sm"
          color="lavender"
          :active="activeFilter === f.value"
          @click="activeFilter = f.value"
        >
          {{ f.label }}
        </KinChip>

        <div class="ml-auto w-44">
          <KinSelect
            v-model="sortBy"
            size="sm"
            :options="sortOptions"
            aria-label="Sort rewards"
          />
        </div>
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
        @bid="openBidModal"
        @close-auction="handleCloseAuction"
        @cancel-auction="handleCancelAuction"
      />
    </div>

    <!-- Bid Modal -->
    <BidModal
      v-if="biddingReward"
      :reward="biddingReward"
      :bank="pointsStore.bank"
      @close="biddingReward = null"
      @bid-placed="handleBidPlaced"
    />

    <!-- Empty states -->
    <KinEmptyState
      v-if="filteredRewards.length === 0 && pointsStore.rewards.length > 0"
      :icon="MagnifyingGlassIcon"
      title="No rewards match your filters"
      description="Try adjusting your search or filters."
      accent-color="lavender"
      size="md"
      class="mt-6"
    />

    <KinEmptyState
      v-if="pointsStore.rewards.length === 0 && !showForm"
      :icon="GiftIcon"
      title="No rewards yet"
      :description="isParent ? 'Create some rewards for your family!' : 'Check back soon — rewards are coming.'"
      accent-color="peach"
      size="md"
      class="mt-6"
    />
  </div>
</template>

<script setup>
import { onMounted, ref, computed, watch, nextTick } from 'vue'
import { storeToRefs } from 'pinia'
import { usePointsStore } from '@/stores/points'
import { useAuthStore } from '@/stores/auth'
import RewardCard from '@/components/points/RewardCard.vue'
import RewardForm from '@/components/points/RewardForm.vue'
import BidModal from '@/components/points/BidModal.vue'
import { ChevronLeftIcon, PlusIcon, GiftIcon, MagnifyingGlassIcon } from '@heroicons/vue/24/outline'
import { useNotification } from '@/composables/useNotification'
import KinButton from '@/components/design-system/KinButton.vue'
import KinChip from '@/components/design-system/KinChip.vue'
import KinSearch from '@/components/design-system/KinSearch.vue'
import KinSelect from '@/components/design-system/KinSelect.vue'
import KinEmptyState from '@/components/design-system/KinEmptyState.vue'

const pointsStore = usePointsStore()
const authStore = useAuthStore()
const { isParent } = storeToRefs(authStore)
const notify = useNotification()

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
  nextTick(() => {
    document.querySelector('[data-reward-form]')?.scrollIntoView({ behavior: 'smooth', block: 'start' })
  })
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
    } else {
      notify.error(result.error || 'Failed to update reward')
    }
  } else {
    const result = await pointsStore.createReward(data)
    if (result.success) {
      closeForm()
    } else {
      notify.error(result.error || 'Failed to create reward')
    }
  }
}

const handlePurchase = async (rewardId) => {
  const result = await pointsStore.purchaseReward(rewardId)
  if (result.success) {
    notify.success(result.data?.message || 'Reward purchased!')
    await pointsStore.fetchRewards()
  } else {
    notify.error(result.error || 'Failed to purchase reward')
  }
}

const handleDelete = async (rewardId) => {
  await pointsStore.deleteReward(rewardId)
}

// Auction handlers
const biddingReward = ref(null)

const openBidModal = (reward) => {
  biddingReward.value = reward
}

const handleBidPlaced = async (data) => {
  biddingReward.value = null
  notify.success(data?.message || 'Bid placed!')
  await Promise.all([pointsStore.fetchRewards(), pointsStore.fetchBank()])
}

const handleCloseAuction = async (rewardId) => {
  const result = await pointsStore.closeAuction(rewardId)
  if (result.success) {
    notify.success(result.data?.message || 'Auction closed!')
    await pointsStore.fetchRewards()
  } else {
    notify.error(result.error || 'Failed to close auction')
  }
}

const handleCancelAuction = async (rewardId) => {
  const result = await pointsStore.cancelAuction(rewardId)
  if (result.success) {
    notify.success(result.data?.message || 'Auction cancelled')
    await Promise.all([pointsStore.fetchRewards(), pointsStore.fetchBank()])
  } else {
    notify.error(result.error || 'Failed to cancel auction')
  }
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

const sortOptions = [
  { value: 'sort_order', label: 'Default' },
  { value: 'price_asc',  label: 'Price: Low → High' },
  { value: 'price_desc', label: 'Price: High → Low' },
  { value: 'name',       label: 'Name A–Z' },
  { value: 'newest',     label: 'Newest' },
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
