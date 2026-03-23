<template>
  <div class="p-4 md:p-6 max-w-3xl">
    <!-- Header -->
    <div class="flex items-center gap-3 mb-6">
      <RouterLink to="/points" class="btn-ghost btn-sm rounded-lg">
        <ChevronLeftIcon class="w-5 h-5" />
      </RouterLink>
      <h1 class="text-2xl font-bold font-heading text-prussian-500 dark:text-lavender-200">My Points History</h1>
    </div>

    <!-- Balance Card -->
    <div class="card p-4 mb-6 flex items-center justify-between">
      <div>
        <p class="text-xs text-lavender-500 dark:text-lavender-400 uppercase tracking-wide font-medium">Total Balance</p>
        <p class="text-3xl font-bold font-mono text-wisteria-600 dark:text-wisteria-400">{{ pointsStore.bank }}</p>
      </div>
    </div>

    <!-- Transaction List -->
    <div class="card divide-y divide-lavender-100 dark:divide-prussian-700">
      <div
        v-for="item in pointsStore.feed"
        :key="item.id"
        class="px-4 py-3 flex items-center justify-between"
      >
        <div class="flex-1 min-w-0">
          <p class="text-sm text-prussian-500 dark:text-lavender-200 truncate">
            {{ item.description }}
          </p>
          <p class="text-xs text-lavender-400 dark:text-lavender-500 mt-0.5">
            {{ formatDate(item.created_at) }}
          </p>
        </div>
        <span
          class="text-sm font-bold font-mono ml-3 flex-shrink-0"
          :class="item.points > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-500 dark:text-red-400'"
        >
          {{ item.points > 0 ? '+' : '' }}{{ item.points }}
        </span>
      </div>

      <div v-if="pointsStore.feed.length === 0" class="p-8 text-center text-lavender-500 dark:text-lavender-400 text-sm">
        No transactions yet.
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { usePointsStore } from '@/stores/points'
import { ChevronLeftIcon } from '@heroicons/vue/24/outline'

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
