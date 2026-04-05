<template>
  <div class="flex flex-col h-full">
    <!-- Header -->
    <div class="flex items-center justify-between mb-3 flex-shrink-0">
      <h3 class="text-sm font-semibold text-prussian-500 dark:text-lavender-200 flex items-center gap-2">
        <TrophyIcon class="w-4 h-4 text-wisteria-600" />
        {{ config.title || 'Leaderboard' }}
      </h3>
      <RouterLink
        to="/points"
        class="text-wisteria-600 dark:text-wisteria-400 text-xs font-medium hover:text-wisteria-500"
      >
        View Feed
      </RouterLink>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="space-y-2 flex-1">
      <div v-for="n in 4" :key="n" class="h-8 bg-lavender-100 dark:bg-prussian-700 rounded animate-pulse"></div>
    </div>

    <!-- Empty -->
    <div v-else-if="leaderboard.length === 0" class="flex-1 flex items-center justify-center">
      <div class="text-center">
        <TrophyIcon class="w-8 h-8 text-lavender-400 dark:text-lavender-500 mx-auto mb-1" />
        <p class="text-sm text-lavender-500 dark:text-lavender-400">No activity yet</p>
      </div>
    </div>

    <!-- Medium: full animated LeaderboardStrip -->
    <div v-else-if="config.size === 'md'" class="flex-1 min-h-0 overflow-y-auto">
      <LeaderboardStrip :leaderboard="leaderboard" />
    </div>

    <!-- Small: responsive layout — side-by-side on desktop, stacked on mobile -->
    <div v-else class="flex-1 min-h-0 flex flex-col md:flex-row gap-3 md:gap-4">
      <!-- Podium -->
      <div v-if="topThree.length > 0" class="flex items-end justify-center gap-1 flex-shrink-0">
        <div v-if="topThree.length > 1" class="flex flex-col items-center">
          <div class="text-[10px] font-bold text-lavender-500 dark:text-lavender-300 mb-0.5">2nd</div>
          <UserAvatar :user="topThree[1].user" size="sm" />
          <div class="w-12 rounded-t-lg mt-1 flex items-center justify-center bg-gradient-to-t from-lavender-300 to-lavender-200 dark:from-prussian-600 dark:to-prussian-500" style="height: 28px">
            <span class="text-[10px] font-bold font-mono text-prussian-500 dark:text-lavender-200">{{ topThree[1].total_points }}</span>
          </div>
        </div>
        <div class="flex flex-col items-center">
          <TrophyIcon class="w-4 h-4 text-wisteria-400 mb-0.5" />
          <UserAvatar :user="topThree[0].user" size="sm" />
          <div class="w-14 rounded-t-lg mt-1 flex items-center justify-center bg-gradient-to-t from-sand-400 to-sand-300 dark:from-sand-700 dark:to-sand-600" style="height: 40px">
            <span class="text-xs font-bold font-mono text-prussian-600 dark:text-sand-100">{{ topThree[0].total_points }}</span>
          </div>
        </div>
        <div v-if="topThree.length > 2" class="flex flex-col items-center">
          <div class="text-[10px] font-bold text-lavender-500 dark:text-lavender-300 mb-0.5">3rd</div>
          <UserAvatar :user="topThree[2].user" size="sm" />
          <div class="w-12 rounded-t-lg mt-1 flex items-center justify-center bg-gradient-to-t from-amber-200 to-amber-100 dark:from-prussian-700 dark:to-prussian-600" style="height: 20px">
            <span class="text-[10px] font-bold font-mono text-amber-800 dark:text-lavender-300">{{ topThree[2].total_points }}</span>
          </div>
        </div>
      </div>

      <!-- Rankings -->
      <div class="flex-1 min-w-0 flex flex-col justify-center space-y-1.5">
        <div
          v-for="(entry, idx) in leaderboard"
          :key="entry.user_id"
          class="flex items-center gap-2 py-0.5"
          :class="{ 'bg-wisteria-50/50 dark:bg-wisteria-900/10 rounded-md px-1': isCurrentUser(entry) }"
        >
          <span class="text-[10px] font-bold w-3 text-center text-lavender-500 dark:text-lavender-400">{{ idx + 1 }}</span>
          <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between">
              <span class="text-xs font-medium truncate" :class="isCurrentUser(entry) ? 'text-wisteria-700 dark:text-wisteria-300' : 'text-prussian-500 dark:text-lavender-300'">
                {{ firstName(entry) }}
              </span>
              <span class="text-[10px] font-semibold font-mono text-wisteria-600 dark:text-wisteria-400 ml-1 flex-shrink-0">
                {{ entry.total_points }} pts
              </span>
            </div>
            <div class="h-1.5 bg-lavender-200 dark:bg-prussian-700 rounded-full overflow-hidden mt-0.5">
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
import UserAvatar from '@/components/common/UserAvatar.vue'
import LeaderboardStrip from '@/components/points/LeaderboardStrip.vue'

const props = defineProps({
  config: { type: Object, required: true },
})

const authStore = useAuthStore()
const { data, loading } = useWidgetData('/api/v1/points/leaderboard', {})

const leaderboard = computed(() => {
  if (!data.value) return []
  const list = data.value.leaderboard || data.value.data || data.value
  return Array.isArray(list) ? list.slice(0, 5) : []
})

const topThree = computed(() => leaderboard.value.slice(0, 3))
const maxPoints = computed(() => Math.max(...leaderboard.value.map((e) => e.total_points), 1))
const firstName = (entry) => entry.user?.name?.split(' ')[0] || '?'
const isCurrentUser = (entry) => entry.user_id === authStore.currentUser?.id
const progressWidth = (entry) => `${Math.max((entry.total_points / maxPoints.value) * 100, 4)}%`
const progressColor = (idx) => {
  if (idx === 0) return 'bg-gradient-to-r from-sand-400 to-sand-500'
  if (idx === 1) return 'bg-gradient-to-r from-lavender-400 to-lavender-500'
  if (idx === 2) return 'bg-gradient-to-r from-amber-300 to-amber-400'
  return 'bg-gradient-to-r from-wisteria-300 to-wisteria-400'
}
</script>
