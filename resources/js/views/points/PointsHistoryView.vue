<template>
  <div class="p-3 md:p-6 max-w-3xl">
    <KinPageHero show-back @back="$router.push('/points')">
      My Points History
    </KinPageHero>

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
      <KinEmptyState
        v-if="pointsStore.feed.length === 0"
        :icon="ClockIcon"
        title="No transactions yet"
        accent-color="lavender"
        size="sm"
      />
      <div v-else>
        <section v-for="group in groupedFeed" :key="group.key">
          <h2 class="px-4 pt-4 pb-1 text-[10px] font-semibold uppercase tracking-wider text-ink-tertiary">
            {{ group.label }}
          </h2>
          <div class="divide-y divide-border-subtle">
            <div
              v-for="item in group.items"
              :key="item.id"
              class="px-4 py-3 flex items-center justify-between"
            >
              <div class="flex-1 min-w-0">
                <p class="text-sm text-ink-primary truncate">{{ item.description }}</p>
                <p class="text-xs text-ink-tertiary mt-0.5">{{ formatTime(item.created_at) }}</p>
              </div>
              <span
                class="text-sm font-bold font-mono ml-3 flex-shrink-0"
                :class="item.points > 0 ? 'text-status-success' : 'text-status-failed'"
              >
                {{ item.points > 0 ? '+' : '' }}{{ item.points }}
              </span>
            </div>
          </div>
        </section>
      </div>
    </KinFlatCard>
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue'
import { usePointsStore } from '@/stores/points'
import { ClockIcon } from '@heroicons/vue/24/outline'
import KinPageHero from '@/components/design-system/KinPageHero.vue'
import KinHeroMetricCard from '@/components/design-system/KinHeroMetricCard.vue'
import KinFlatCard from '@/components/design-system/KinFlatCard.vue'
import KinEmptyState from '@/components/design-system/KinEmptyState.vue'

const pointsStore = usePointsStore()

const formatTime = (dateStr) => {
  if (!dateStr) return ''
  return new Date(dateStr).toLocaleTimeString('en-US', {
    hour: 'numeric',
    minute: '2-digit',
  })
}

// Group the feed by day, keeping the store's newest-first order. Every
// transaction lands in a group (an undated one gets its own) so the list
// always adds up to the balance above it.
const groupedFeed = computed(() => {
  const now = new Date()
  const today = now.toDateString()
  const yd = new Date(now)
  yd.setDate(yd.getDate() - 1)
  const yesterday = yd.toDateString()

  const dayLabel = (d, key) => {
    if (key === today) return 'Today'
    if (key === yesterday) return 'Yesterday'
    return d.toLocaleDateString('en-US', {
      weekday: 'short',
      month: 'short',
      day: 'numeric',
      ...(d.getFullYear() !== now.getFullYear() && { year: 'numeric' }),
    })
  }

  const groups = []
  const seen = new Map()
  for (const item of pointsStore.feed) {
    const d = item.created_at ? new Date(item.created_at) : null
    const key = d ? d.toDateString() : 'undated'
    let group = seen.get(key)
    if (!group) {
      group = { key, label: d ? dayLabel(d, key) : 'Undated', items: [] }
      seen.set(key, group)
      groups.push(group)
    }
    group.items.push(item)
  }
  return groups
})

onMounted(async () => {
  await Promise.all([
    pointsStore.fetchBank(),
    pointsStore.fetchFeed(),
  ])
})
</script>
