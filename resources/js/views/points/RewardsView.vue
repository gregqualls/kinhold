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
        <button v-if="isParent" class="btn-primary btn-sm" @click="showCreateForm = !showCreateForm">
          {{ showCreateForm ? 'Cancel' : '+ Add Reward' }}
        </button>
      </div>
    </div>

    <!-- Create Form (parent only) -->
    <div v-if="showCreateForm" class="card p-4 mb-6">
      <h3 class="text-sm font-semibold text-prussian-500 dark:text-lavender-200 mb-3">New Reward</h3>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <input v-model="newReward.title" class="input-base" placeholder="Reward name" />
        <input v-model.number="newReward.point_cost" type="number" min="1" class="input-base" placeholder="Point cost" />
        <input v-model="newReward.description" class="input-base sm:col-span-2" placeholder="Description (optional)" />
        <div class="sm:col-span-2">
          <label class="block text-xs font-medium text-prussian-500 dark:text-lavender-300 mb-1.5">Icon</label>
          <IconPicker v-model="newReward.icon" />
        </div>
      </div>
      <div class="flex justify-end mt-3">
        <button :disabled="!newReward.title || !newReward.point_cost" class="btn-primary btn-sm" @click="createReward">
          Create Reward
        </button>
      </div>
    </div>

    <!-- Rewards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
      <RewardCard
        v-for="reward in pointsStore.rewards"
        :key="reward.id"
        :reward="reward"
        :bank="pointsStore.bank"
        :is-parent="isParent"
        @purchase="handlePurchase"
        @delete="handleDelete"
      />
    </div>

    <div v-if="pointsStore.rewards.length === 0" class="card p-8 text-center">
      <p class="text-lavender-500 dark:text-lavender-400">No rewards yet.</p>
      <p v-if="isParent" class="text-sm text-lavender-400 mt-1">Create some rewards for your family!</p>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { storeToRefs } from 'pinia'
import { usePointsStore } from '@/stores/points'
import { useAuthStore } from '@/stores/auth'
import RewardCard from '@/components/points/RewardCard.vue'
import IconPicker from '@/components/common/IconPicker.vue'
import { ChevronLeftIcon } from '@heroicons/vue/24/outline'

const pointsStore = usePointsStore()
const authStore = useAuthStore()
const { isParent } = storeToRefs(authStore)

const showCreateForm = ref(false)
const newReward = ref({ title: '', description: '', point_cost: null, icon: '' })

const createReward = async () => {
  const result = await pointsStore.createReward(newReward.value)
  if (result.success) {
    newReward.value = { title: '', description: '', point_cost: null, icon: '' }
    showCreateForm.value = false
  }
}

const handlePurchase = async (rewardId) => {
  await pointsStore.purchaseReward(rewardId)
}

const handleDelete = async (rewardId) => {
  await pointsStore.deleteReward(rewardId)
}

onMounted(async () => {
  await Promise.all([
    pointsStore.fetchRewards(),
    pointsStore.fetchBank(),
  ])
})
</script>
