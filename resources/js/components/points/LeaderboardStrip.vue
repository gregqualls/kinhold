<template>
  <div class="leaderboard-strip">
    <!-- Podium for top 3 -->
    <LeaderboardPodium :entries="leaderboard" class="mb-2" />

    <!-- Progress bars for everyone -->
    <div v-if="leaderboard.length > 0" class="space-y-1.5 mt-3">
      <div
        v-for="(entry, idx) in leaderboard"
        :key="entry.user_id"
        class="flex items-center gap-2 px-1 py-0.5 rounded-md transition-colors"
        :class="{
          'bg-accent-lavender-soft/40': isCurrentUser(entry),
        }"
      >
        <span class="text-[10px] font-bold w-4 text-center text-ink-tertiary">
          {{ idx + 1 }}
        </span>
        <div class="flex-1 min-w-0">
          <div class="flex items-center justify-between mb-0.5">
            <span
              class="text-[11px] font-medium truncate"
              :class="isCurrentUser(entry) ? 'text-accent-lavender-bold' : 'text-ink-secondary'"
            >
              {{ firstName(entry) }}
              <span v-if="isCurrentUser(entry)" class="text-[9px] text-accent-lavender-bold">(you)</span>
            </span>
            <span class="text-[10px] font-semibold font-mono text-accent-lavender-bold ml-1 flex-shrink-0">
              {{ entry.total_points }} pts
            </span>
          </div>
          <div class="h-1.5 bg-surface-sunken rounded-full overflow-hidden">
            <div
              class="h-full rounded-full transition-all duration-700 ease-out"
              :class="progressBarColor(idx)"
              :style="{ width: progressWidth(entry) }"
            ></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty state -->
    <div v-if="leaderboard.length === 0" class="text-center py-3">
      <TrophyIcon class="w-8 h-8 text-ink-tertiary mx-auto mb-1" />
      <p class="text-sm text-ink-secondary">
        No activity yet this period
      </p>
      <p class="text-xs text-ink-tertiary mt-0.5">
        Complete tasks to earn points!
      </p>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { TrophyIcon } from '@heroicons/vue/24/solid'
import LeaderboardPodium from '@/components/points/LeaderboardPodium.vue'

const props = defineProps({
  leaderboard: {
    type: Array,
    default: () => [],
  },
})

const authStore = useAuthStore()

const maxPoints = computed(() => {
  if (props.leaderboard.length === 0) return 1
  return Math.max(...props.leaderboard.map((e) => e.total_points), 1)
})

const firstName = (entry) => entry.user?.name?.split(' ')[0] || '?'

const isCurrentUser = (entry) => entry.user_id === authStore.currentUser?.id

const progressWidth = (entry) => {
  const pct = (entry.total_points / maxPoints.value) * 100
  return `${Math.max(pct, 4)}%`
}

// Progress-bar tier colors mirror the podium medal accents
// (sun = 1st, lavender = 2nd, peach = 3rd) so the rankings list and the
// podium read as one coherent visualization.
const progressBarColor = (idx) => {
  if (idx === 0) return 'bg-accent-sun-bold'
  if (idx === 1) return 'bg-accent-lavender-bold'
  if (idx === 2) return 'bg-accent-peach-bold'
  return 'bg-ink-tertiary/50'
}
</script>

