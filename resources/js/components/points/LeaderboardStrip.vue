<template>
  <div class="leaderboard-strip">
    <!-- Podium for top 3 -->
    <div v-if="topThree.length > 0" class="flex items-end justify-center gap-1 sm:gap-2 mb-2">
      <!-- 2nd Place — lavender medal tier -->
      <div
        v-if="topThree.length > 1"
        class="flex flex-col items-center animate-slide-up-delayed"
      >
        <div class="relative mb-1">
          <div class="medal-icon text-xs sm:text-sm font-bold text-ink-tertiary">2nd</div>
          <div
            class="relative"
            :class="{ 'ring-2 ring-accent-lavender-bold ring-offset-2 ring-offset-surface-raised rounded-full': isCurrentUser(topThree[1]) }"
          >
            <UserAvatar :user="topThree[1].user" size="sm" />
          </div>
        </div>
        <p class="text-[10px] sm:text-xs font-semibold text-ink-primary truncate max-w-[56px] sm:max-w-[72px] text-center">
          {{ firstName(topThree[1]) }}
        </p>
        <div
          class="podium-bar rounded-t-[12px] mt-1 border border-accent-lavender-bold/20"
          :style="{ height: '52px', width: '60px', background: 'rgb(var(--accent-lavender-bold) / 0.16)' }"
        >
          <span class="podium-points text-sm font-bold font-mono text-accent-lavender-bold">
            {{ topThree[1].total_points }}
          </span>
        </div>
      </div>

      <!-- 1st Place — sun (gold) medal tier -->
      <div
        v-if="topThree.length > 0"
        class="flex flex-col items-center animate-slide-up"
      >
        <div class="relative mb-1">
          <div class="crown-bounce"><TrophyIcon class="w-5 h-5 sm:w-6 sm:h-6 text-accent-sun-bold" /></div>
          <div
            class="relative"
            :class="{ 'ring-2 ring-accent-sun-bold ring-offset-2 ring-offset-surface-raised rounded-full': isCurrentUser(topThree[0]) }"
          >
            <UserAvatar :user="topThree[0].user" size="md" />
          </div>
        </div>
        <p class="text-xs sm:text-sm font-bold text-ink-primary truncate max-w-[64px] sm:max-w-[80px] text-center">
          {{ firstName(topThree[0]) }}
        </p>
        <div
          class="podium-bar rounded-t-[12px] mt-1 relative overflow-hidden border border-accent-sun-bold/30"
          :style="{ height: '80px', width: '68px', background: 'rgb(var(--accent-sun-bold) / 0.20)' }"
        >
          <div class="podium-shimmer"></div>
          <span class="podium-points text-base font-bold font-mono text-accent-sun-bold">
            {{ topThree[0].total_points }}
          </span>
        </div>
      </div>

      <!-- 3rd Place — peach medal tier -->
      <div
        v-if="topThree.length > 2"
        class="flex flex-col items-center animate-slide-up-delayed-2"
      >
        <div class="relative mb-1">
          <div class="medal-icon text-xs sm:text-sm font-bold text-ink-tertiary">3rd</div>
          <div
            class="relative"
            :class="{ 'ring-2 ring-accent-peach-bold ring-offset-2 ring-offset-surface-raised rounded-full': isCurrentUser(topThree[2]) }"
          >
            <UserAvatar :user="topThree[2].user" size="sm" />
          </div>
        </div>
        <p class="text-[10px] sm:text-xs font-semibold text-ink-primary truncate max-w-[56px] sm:max-w-[72px] text-center">
          {{ firstName(topThree[2]) }}
        </p>
        <div
          class="podium-bar rounded-t-[12px] mt-1 border border-accent-peach-bold/20"
          :style="{ height: '36px', width: '60px', background: 'rgb(var(--accent-peach-bold) / 0.16)' }"
        >
          <span class="podium-points text-sm font-bold font-mono text-accent-peach-bold">
            {{ topThree[2].total_points }}
          </span>
        </div>
      </div>
    </div>

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
import UserAvatar from '@/components/common/UserAvatar.vue'

const props = defineProps({
  leaderboard: {
    type: Array,
    default: () => [],
  },
})

const authStore = useAuthStore()

const topThree = computed(() => props.leaderboard.slice(0, 3))

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
// podium read as one coherent visualization. 4th+ uses ink-tertiary at
// reduced alpha so it reads as "below-tier" without competing with the
// medals.
const progressBarColor = (idx) => {
  if (idx === 0) return 'bg-accent-sun-bold'
  if (idx === 1) return 'bg-accent-lavender-bold'
  if (idx === 2) return 'bg-accent-peach-bold'
  return 'bg-ink-tertiary/50'
}
</script>

<style scoped>
.leaderboard-strip {
  --anim-duration: 0.5s;
}

/* Podium bar with centered points */
.podium-bar {
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
}

.podium-points {
  position: relative;
  z-index: 1;
}

/* Gold shimmer effect on 1st place podium */
.podium-shimmer {
  position: absolute;
  inset: 0;
  background: linear-gradient(
    110deg,
    transparent 30%,
    rgba(255, 255, 255, 0.25) 45%,
    transparent 60%
  );
  animation: shimmer 3s ease-in-out infinite;
}

@keyframes shimmer {
  0%, 100% { transform: translateX(-100%); }
  50% { transform: translateX(100%); }
}

/* Crown bounce animation */
.crown-bounce {
  text-align: center;
  animation: crown-bounce 2s ease-in-out infinite;
}

@keyframes crown-bounce {
  0%, 100% { transform: translateY(0) rotate(0deg); }
  25% { transform: translateY(-2px) rotate(-3deg); }
  75% { transform: translateY(-2px) rotate(3deg); }
}

/* Medal icon */
.medal-icon {
  text-align: center;
}

/* Slide up entrance animations */
.animate-slide-up {
  animation: slide-up var(--anim-duration) ease-out both;
}

.animate-slide-up-delayed {
  animation: slide-up var(--anim-duration) ease-out 0.075s both;
}

.animate-slide-up-delayed-2 {
  animation: slide-up var(--anim-duration) ease-out 0.15s both;
}

@keyframes slide-up {
  from {
    opacity: 0;
    transform: translateY(16px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Prefers reduced motion */
@media (prefers-reduced-motion: reduce) {
  .crown-bounce,
  .podium-shimmer {
    animation: none;
  }
  .animate-slide-up,
  .animate-slide-up-delayed,
  .animate-slide-up-delayed-2 {
    animation: none;
    opacity: 1;
  }
}
</style>
