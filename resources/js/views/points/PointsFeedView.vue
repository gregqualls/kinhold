<template>
  <div class="p-4 md:p-6 max-w-3xl flex flex-col h-full">
    <!-- Header -->
    <div class="flex items-center justify-between mb-4">
      <h1 class="text-2xl font-bold text-prussian-500 dark:text-lavender-200">Points</h1>
      <div class="flex gap-2">
        <RouterLink to="/points/rewards" class="btn-secondary btn-sm">Rewards</RouterLink>
        <RouterLink to="/points/history" class="btn-ghost btn-sm">History</RouterLink>
      </div>
    </div>

    <!-- Pending Point Requests (parents only) -->
    <PendingRequests
      v-if="isParent"
      :requests="pointsStore.pointRequests"
      @approve="handleApprove"
      @deny="handleDeny"
    />

    <!-- Bank Balance + Leaderboard -->
    <div class="card p-4 mb-4">
      <div class="flex items-center justify-between mb-3">
        <div>
          <p class="text-xs text-lavender-500 dark:text-lavender-400 uppercase tracking-wide font-medium">Your Balance</p>
          <p class="text-3xl font-bold text-wisteria-600 dark:text-wisteria-400">{{ pointsStore.bank }}</p>
        </div>
        <div class="text-right">
          <p class="text-xs text-lavender-500 dark:text-lavender-400 uppercase tracking-wide font-medium mb-1">
            {{ pointsStore.leaderboardPeriod }} Leaderboard
          </p>
        </div>
      </div>
      <LeaderboardStrip :leaderboard="pointsStore.leaderboard" />
    </div>

    <!-- Activity Feed -->
    <div class="card flex-1 overflow-hidden flex flex-col">
      <div class="px-4 pt-3 pb-2 border-b border-lavender-200 dark:border-prussian-700">
        <p class="text-sm font-semibold text-prussian-500 dark:text-lavender-200">Activity</p>
      </div>

      <div class="flex-1 overflow-y-auto px-4 divide-y divide-lavender-100 dark:divide-prussian-700">
        <FeedItem
          v-for="item in pointsStore.feed"
          :key="item.id"
          :item="item"
        />
        <div v-if="pointsStore.feed.length === 0 && !pointsStore.isLoading" class="py-8 text-center text-lavender-500 dark:text-lavender-400 text-sm">
          No activity yet. Complete tasks or give kudos to get started!
        </div>
      </div>

      <!-- Kudos Input -->
      <div class="p-3 border-t border-lavender-200 dark:border-prussian-700 bg-lavender-50 dark:bg-prussian-900">
        <KudosInput :members="familyMembers" @kudos="handleKudos" />
        <div class="mt-2 flex justify-end gap-3">
          <button v-if="!isParent" @click="showRequestModal = true" class="text-xs text-wisteria-600 hover:text-wisteria-700 dark:text-wisteria-400 dark:hover:text-wisteria-300 font-medium">
            Request Points
          </button>
          <button v-if="isParent" @click="showDeductModal = true" class="text-xs text-red-500 hover:text-red-600 font-medium">
            Deduct Points
          </button>
        </div>
      </div>
    </div>

    <!-- Deduct Modal -->
    <DeductModal
      :show="showDeductModal"
      :members="familyMembers"
      @close="showDeductModal = false"
      @deduct="handleDeduct"
    />

    <!-- Request Points Modal -->
    <RequestPointsModal
      :show="showRequestModal"
      @close="showRequestModal = false"
      @submit="handleRequestPoints"
    />
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { storeToRefs } from 'pinia'
import { usePointsStore } from '@/stores/points'
import { useAuthStore } from '@/stores/auth'
import LeaderboardStrip from '@/components/points/LeaderboardStrip.vue'
import FeedItem from '@/components/points/FeedItem.vue'
import KudosInput from '@/components/points/KudosInput.vue'
import DeductModal from '@/components/points/DeductModal.vue'
import RequestPointsModal from '@/components/points/RequestPointsModal.vue'
import PendingRequests from '@/components/points/PendingRequests.vue'

const pointsStore = usePointsStore()
const authStore = useAuthStore()
const { isParent, familyMembers } = storeToRefs(authStore)

const showDeductModal = ref(false)
const showRequestModal = ref(false)

const handleKudos = async ({ userId, reason }) => {
  await pointsStore.giveKudos(userId, reason)
}

const handleDeduct = async ({ userId, points, reason }) => {
  await pointsStore.deductPoints(userId, points, reason)
  showDeductModal.value = false
}

const handleRequestPoints = async ({ points, reason }) => {
  await pointsStore.submitPointRequest(points, reason)
  showRequestModal.value = false
}

const handleApprove = async (requestId) => {
  await pointsStore.approvePointRequest(requestId)
}

const handleDeny = async (requestId) => {
  await pointsStore.denyPointRequest(requestId)
}

onMounted(async () => {
  await Promise.all([
    pointsStore.fetchBank(),
    pointsStore.fetchLeaderboard(),
    pointsStore.fetchFeed(),
    pointsStore.fetchPointRequests(),
  ])
})
</script>
