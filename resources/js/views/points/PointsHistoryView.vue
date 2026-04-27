<template>
  <div class="p-4 md:p-6 max-w-3xl">
    <!-- Header -->
    <div class="flex items-center gap-3 mb-6">
      <KinButton variant="ghost" size="sm" icon-only aria-label="Back to Points" to="/points">
        <ChevronLeftIcon class="w-5 h-5" />
      </KinButton>
      <h1 class="text-2xl font-bold font-heading text-ink-primary">My Points History</h1>
    </div>

    <!-- Hero balance card -->
    <KinHeroMetricCard
      variant="iridescent"
      label="Total Balance"
      :value="pointsStore.bank"
      min-height="160px"
      class="mb-6"
    />

    <!-- Transaction List -->
    <KinFlatCard padding="none" class="overflow-hidden">
      <div class="divide-y divide-border-subtle">
        <div
          v-for="item in pointsStore.feed"
          :key="item.id"
          class="px-4 py-3 flex items-center justify-between"
        >
          <div class="flex-1 min-w-0">
            <p class="text-sm text-ink-primary truncate">
              {{ item.description }}
            </p>
            <p class="text-xs text-ink-tertiary mt-0.5">
              {{ formatDate(item.created_at) }}
            </p>
          </div>
          <span
            class="text-sm font-bold font-mono ml-3 flex-shrink-0"
            :class="item.points > 0 ? 'text-status-success' : 'text-status-failed'"
          >
            {{ item.points > 0 ? '+' : '' }}{{ item.points }}
          </span>
        </div>

        <KinEmptyState
          v-if="pointsStore.feed.length === 0"
          :icon="ClockIcon"
          title="No transactions yet"
          accent-color="lavender"
          size="sm"
        />
      </div>
    </KinFlatCard>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { usePointsStore } from '@/stores/points'
import { ChevronLeftIcon, ClockIcon } from '@heroicons/vue/24/outline'
import KinButton from '@/components/design-system/KinButton.vue'
import KinHeroMetricCard from '@/components/design-system/KinHeroMetricCard.vue'
import KinFlatCard from '@/components/design-system/KinFlatCard.vue'
import KinEmptyState from '@/components/design-system/KinEmptyState.vue'

const pointsStore = usePointsStore()

const formatDate = (dateStr) => {
  if (!dateStr) return ''
  return new Date(dateStr).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    hour: 'numeric',
    minute: '2-digit',
  })
}

onMounted(async () => {
  await Promise.all([
    pointsStore.fetchBank(),
    pointsStore.fetchFeed(),
  ])
})
</script>
