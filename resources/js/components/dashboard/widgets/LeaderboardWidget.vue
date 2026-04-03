<template>
  <div>
    <div v-if="loading" class="space-y-2">
      <div v-for="n in 4" :key="n" class="h-8 bg-lavender-100 dark:bg-prussian-700 rounded animate-pulse"></div>
    </div>

    <div v-else-if="leaderboard.length === 0" class="flex flex-col items-center justify-center h-full py-4">
      <TrophyIcon class="w-8 h-8 text-lavender-400 dark:text-lavender-500 mb-1" />
      <p class="text-sm text-lavender-500 dark:text-lavender-400">No activity yet</p>
    </div>

    <!-- Compact side-by-side layout: podium left, rankings right -->
    <div v-else class="flex gap-4 h-full">
      <!-- Podium (top 3) -->
      <div v-if="topThree.length > 0" class="flex items-end justify-center gap-1 flex-shrink-0 pb-1">
        <!-- 2nd -->
        <div v-if="topThree.length > 1" class="flex flex-col items-center">
          <div class="text-[10px] font-bold text-lavender-500 dark:text-lavender-300 mb-0.5">2nd</div>
          <UserAvatar :user="topThree[1].user" size="sm" />
          <div
            class="w-12 rounded-t-lg mt-1 flex items-center justify-center bg-gradient-to-t from-lavender-300 to-lavender-200 dark:from-prussian-600 dark:to-prussian-500"
            style="height: 28px"
          >
            <span class="text-[10px] font-bold font-mono text-prussian-500 dark:text-lavender-200">{{ topThree[1].total_points }}</span>
          </div>
        </div>
        <!-- 1st -->
        <div class="flex flex-col items-center">
          <TrophyIcon class="w-4 h-4 text-wisteria-400 mb-0.5" />
          <UserAvatar :user="topThree[0].user" size="sm" />
          <div
            class="w-14 rounded-t-lg mt-1 flex items-center justify-center bg-gradient-to-t from-sand-400 to-sand-300 dark:from-sand-700 dark:to-sand-600"
            style="height: 40px"
          >
            <span class="text-xs font-bold font-mono text-prussian-600 dark:text-sand-100">{{ topThree[0].total_points }}</span>
          </div>
        </div>
        <!-- 3rd -->
        <div v-if="topThree.length > 2" class="flex flex-col items-center">
          <div class="text-[10px] font-bold text-lavender-500 dark:text-lavender-300 mb-0.5">3rd</div>
          <UserAvatar :user="topThree[2].user" size="sm" />
          <div
            class="w-12 rounded-t-lg mt-1 flex items-center justify-center bg-gradient-to-t from-amber-200 to-amber-100 dark:from-prussian-700 dark:to-prussian-600"
            style="height: 20px"
          >
            <span class="text-[10px] font-bold font-mono text-amber-800 dark:text-lavender-300">{{ topThree[2].total_points }}</span>
          </div>
        </div>
      </div>

      <!-- Rankings list -->
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
              <span
                class="text-[11px] font-medium truncate"
                :class="isCurrentUser(entry) ? 'text-wisteria-700 dark:text-wisteria-300' : 'text-prussian-500 dark:text-lavender-300'"
              >
                {{ firstName(entry) }}
              </span>
              <span class="text-[10px] font-semibold font-mono text-wisteria-600 dark:text-wisteria-400 ml-1 flex-shrink-0">
                {{ entry.total_points }} pts
              </span>
            </div>
            <div class="h-1 bg-lavender-200 dark:bg-prussian-700 rounded-full overflow-hidden mt-0.5">
              <div
                class="h-full rounded-full transition-all duration-700"
                :class="progressColor(idx)"
                :style="{ width: progressWidth(entry) }"
              ></div>
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

const props = defineProps({
  config: { type: Object, required: true },
})

const authStore = useAuthStore()
const { data, loading } = useWidgetData(props.config.endpoint, props.config.params)

const leaderboard = computed(() => {
  if (!data.value) return []
  const list = data.value.leaderboard || data.value.data || data.value
  if (!Array.isArray(list)) return []
  return list.slice(0, props.config.settings?.limit || 5)
})

const topThree = computed(() => leaderboard.value.slice(0, 3))

const maxPoints = computed(() => {
  if (leaderboard.value.length === 0) return 1
  return Math.max(...leaderboard.value.map((e) => e.total_points), 1)
})

const firstName = (entry) => entry.user?.name?.split(' ')[0] || '?'
const isCurrentUser = (entry) => entry.user_id === authStore.currentUser?.id

const progressWidth = (entry) => {
  const pct = (entry.total_points / maxPoints.value) * 100
  return `${Math.max(pct, 4)}%`
}

const progressColor = (idx) => {
  if (idx === 0) return 'bg-gradient-to-r from-sand-400 to-sand-500'
  if (idx === 1) return 'bg-gradient-to-r from-lavender-400 to-lavender-500'
  if (idx === 2) return 'bg-gradient-to-r from-amber-300 to-amber-400'
  return 'bg-gradient-to-r from-wisteria-300 to-wisteria-400'
}
</script>
