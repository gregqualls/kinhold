<template>
  <div class="flex flex-col h-full">
    <!-- Header -->
    <div class="flex items-center justify-between mb-3 flex-shrink-0">
      <h3 class="text-sm font-semibold text-ink-primary flex items-center gap-2">
        <TrophyIcon class="w-4 h-4 text-accent-lavender-bold" />
        {{ config.title || 'Leaderboard' }}
      </h3>
      <RouterLink
        to="/points"
        class="text-xs font-medium text-accent-lavender-bold hover:opacity-80 transition-opacity"
      >
        View Feed
      </RouterLink>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="space-y-2 flex-1">
      <KinSkeleton v-for="n in 4" :key="n" shape="rect" :height="'32px'" rounded="4px" />
    </div>

    <!-- Empty -->
    <KinEmptyState
      v-else-if="leaderboard.length === 0"
      :icon="TrophyIcon"
      title="No activity yet"
      size="sm"
      accent-color="sun"
      class="flex-1"
    />

    <!-- Medium: full animated LeaderboardStrip -->
    <div v-else-if="config.size === 'md'" class="flex-1 min-h-0 overflow-y-auto">
      <LeaderboardStrip :leaderboard="leaderboard" />
    </div>

    <!-- Small: responsive layout — side-by-side on desktop, stacked on mobile -->
    <div v-else class="flex-1 min-h-0 flex flex-col md:flex-row gap-3 md:gap-4">
      <!-- Podium (shared component — keeps trophy/medal styling consistent with /points) -->
      <LeaderboardPodium :entries="leaderboard" class="flex-shrink-0" />

      <!-- Rankings -->
      <div class="flex-1 min-w-0 flex flex-col justify-center space-y-1.5">
        <div
          v-for="(entry, idx) in leaderboard"
          :key="entry.user_id"
          class="flex items-center gap-2 py-0.5"
          :class="{ 'bg-accent-lavender-soft/40 rounded-md px-1': isCurrentUser(entry) }"
        >
          <span class="text-[10px] font-bold w-3 text-center text-ink-tertiary">{{ idx + 1 }}</span>
          <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between">
              <span class="text-xs font-medium truncate" :class="isCurrentUser(entry) ? 'text-accent-lavender-bold' : 'text-ink-secondary'">
                {{ firstName(entry) }}
              </span>
              <span class="text-[10px] font-semibold font-mono text-accent-lavender-bold ml-1 flex-shrink-0">
                {{ entry.total_points }} pts
              </span>
            </div>
            <div class="h-1.5 bg-surface-sunken rounded-full overflow-hidden mt-0.5">
              <div class="h-full rounded-full transition-all duration-700" :class="progressColor(idx)" :style="{ width: progressWidth(entry) }"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useWidgetData } from '@/composables/useWidgetData'
import { TrophyIcon } from '@heroicons/vue/24/solid'
import LeaderboardStrip from '@/components/points/LeaderboardStrip.vue'
import LeaderboardPodium from '@/components/points/LeaderboardPodium.vue'
import KinSkeleton from '@/components/design-system/KinSkeleton.vue'
import KinEmptyState from '@/components/design-system/KinEmptyState.vue'

const props = defineProps({
  config: { type: Object, required: true },
})

const authStore = useAuthStore()
const { data, loading } = useWidgetData('/api/v1/points/leaderboard', {})

const leaderboard = computed(() => {
  if (!data.value) return []
  const list = data.value.leaderboard || data.value.data || data.value
  if (!Array.isArray(list)) return []
  const limit = props.config.size === 'md' ? 10 : 5
  return list.slice(0, limit)
})

const maxPoints = computed(() => Math.max(...leaderboard.value.map((e) => e.total_points), 1))
const firstName = (entry) => entry.user?.name?.split(' ')[0] || '?'
const isCurrentUser = (entry) => entry.user_id === authStore.currentUser?.id
const progressWidth = (entry) => `${Math.max((entry.total_points / maxPoints.value) * 100, 4)}%`
const progressColor = (idx) => {
  if (idx === 0) return 'bg-accent-sun-bold'
  if (idx === 1) return 'bg-accent-lavender-bold'
  if (idx === 2) return 'bg-accent-peach-bold'
  return 'bg-ink-tertiary/50'
}
</script>
