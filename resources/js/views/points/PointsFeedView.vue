<template>
  <div class="flex lg:h-full">
    <!-- Main column -->
    <div class="flex-1 min-w-0 flex flex-col p-3 md:p-6 gap-3 md:gap-4 lg:overflow-hidden">
      <!-- Header -->
      <div class="flex items-center justify-between gap-3 flex-wrap flex-shrink-0">
        <h1 class="text-lg md:text-2xl font-bold font-heading text-ink-primary">Points</h1>
        <!-- Mobile-only quick links (rail handles this on desktop) -->
        <div class="flex gap-2 lg:hidden">
          <KinButton variant="secondary" size="sm" to="/points/rewards">Rewards</KinButton>
          <KinButton variant="ghost" size="sm" to="/points/history">History</KinButton>
        </div>
      </div>

      <!-- Pending Point Requests (parents only) -->
      <PendingRequests
        v-if="isParent"
        :requests="pointsStore.pointRequests"
        @approve="handleApprove"
        @deny="handleDeny"
      />

      <!-- Quick-give kudos — top of page so it's reachable without scrolling -->
      <KinFlatCard padding="md" class="flex-shrink-0">
        <p class="text-xs text-ink-tertiary uppercase tracking-widest font-semibold mb-2">
          Give Kudos
        </p>
        <KudosInput :members="familyMembers" @kudos="handleKudos" />
      </KinFlatCard>

      <!-- Hero balance card -->
      <KinHeroMetricCard
        variant="iridescent"
        label="Your Balance"
        :value="pointsStore.bank"
        cta-label="Spend"
        cta-to="/points/rewards"
        min-height="180px"
        class="flex-shrink-0"
      />

      <!-- Mobile-only leaderboard (rail handles this on desktop) -->
      <KinFlatCard padding="md" class="lg:hidden flex-shrink-0">
        <p class="text-xs text-ink-tertiary uppercase tracking-widest font-semibold mb-3">
          {{ pointsStore.leaderboardPeriod }} Leaderboard
        </p>
        <LeaderboardStrip :leaderboard="pointsStore.leaderboard" />
      </KinFlatCard>

      <!-- Activity Feed — fills remaining vertical space on desktop, flows on mobile -->
      <KinFlatCard padding="none" class="lg:flex-1 lg:overflow-hidden flex flex-col lg:min-h-0">
        <div class="px-4 pt-3 pb-2 border-b border-border-subtle flex-shrink-0">
          <p class="text-sm font-semibold text-ink-primary">Activity</p>
        </div>

        <div class="lg:flex-1 lg:overflow-y-auto divide-y divide-border-subtle">
          <FeedItem
            v-for="item in pointsStore.feed"
            :key="item.id"
            :item="item"
          />
          <KinEmptyState
            v-if="pointsStore.feed.length === 0 && !pointsStore.isLoading"
            :icon="SparklesIcon"
            title="No activity yet"
            description="Complete tasks or give kudos to get started!"
            accent-color="lavender"
            size="sm"
          />
        </div>
      </KinFlatCard>
    </div>

    <!-- Right utility rail (desktop only) -->
    <KinUtilityRail
      class="hidden lg:flex"
      width="280px"
      :labels="{ 'saved-views': pointsStore.leaderboardPeriod + ' Leaderboard' }"
    >
      <!-- Leaderboard -->
      <template #saved-views>
        <LeaderboardStrip :leaderboard="pointsStore.leaderboard" size="sm" />
      </template>

      <!-- Actions -->
      <template #actions>
        <div class="flex flex-col gap-2 items-stretch">
          <KinButton variant="secondary" size="sm" to="/points/rewards">
            <template #leading>
              <GiftIcon class="w-4 h-4" />
            </template>
            Browse Rewards
          </KinButton>
          <KinButton variant="ghost" size="sm" to="/points/history">
            <template #leading>
              <ClockIcon class="w-4 h-4" />
            </template>
            History
          </KinButton>
          <KinButton
            v-if="!isParent"
            variant="ghost"
            size="sm"
            @click="showRequestModal = true"
          >
            Request Points
          </KinButton>
          <KinButton
            v-if="isParent"
            variant="ghost"
            size="sm"
            @click="showDeductModal = true"
          >
            Deduct Points
          </KinButton>
        </div>
      </template>
    </KinUtilityRail>

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
import KinButton from '@/components/design-system/KinButton.vue'
import KinHeroMetricCard from '@/components/design-system/KinHeroMetricCard.vue'
import KinFlatCard from '@/components/design-system/KinFlatCard.vue'
import KinEmptyState from '@/components/design-system/KinEmptyState.vue'
import KinUtilityRail from '@/components/design-system/KinUtilityRail.vue'
import { SparklesIcon, GiftIcon, ClockIcon } from '@heroicons/vue/24/outline'

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
