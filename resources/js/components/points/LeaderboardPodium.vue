<template>
  <div v-if="topThree.length > 0" class="flex items-end justify-center gap-1 sm:gap-2">
    <!-- 2nd Place — lavender medal tier -->
    <div
      v-if="topThree.length > 1"
      class="flex flex-col items-center animate-slide-up-delayed"
      :aria-label="`2nd place: ${firstName(topThree[1])}, ${topThree[1].total_points} points`"
    >
      <div class="text-[10px] sm:text-xs font-bold text-ink-tertiary mb-1" aria-hidden="true">2nd</div>
      <div
        class="relative mb-1"
        :class="{ 'ring-2 ring-accent-lavender-bold ring-offset-2 ring-offset-surface-raised rounded-full': isCurrentUser(topThree[1]) }"
      >
        <UserAvatar :user="topThree[1].user" size="sm" />
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

    <!-- 1st Place — sun (gold) medal tier with trophy crown -->
    <div
      class="flex flex-col items-center animate-slide-up"
      :aria-label="`1st place: ${firstName(topThree[0])}, ${topThree[0].total_points} points`"
    >
      <div class="relative mb-1 mt-3 sm:mt-4">
        <div
          class="relative"
          :class="{ 'ring-2 ring-accent-sun-bold ring-offset-2 ring-offset-surface-raised rounded-full': isCurrentUser(topThree[0]) }"
        >
          <UserAvatar :user="topThree[0].user" size="md" />
          <div class="absolute -top-3 sm:-top-4 left-1/2 -translate-x-1/2 pointer-events-none">
            <div class="crown-bounce">
              <TrophyIcon class="w-5 h-5 sm:w-6 sm:h-6 text-accent-sun-bold drop-shadow-sm" />
            </div>
          </div>
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
      :aria-label="`3rd place: ${firstName(topThree[2])}, ${topThree[2].total_points} points`"
    >
      <div class="text-[10px] sm:text-xs font-bold text-ink-tertiary mb-1" aria-hidden="true">3rd</div>
      <div
        class="relative mb-1"
        :class="{ 'ring-2 ring-accent-peach-bold ring-offset-2 ring-offset-surface-raised rounded-full': isCurrentUser(topThree[2]) }"
      >
        <UserAvatar :user="topThree[2].user" size="sm" />
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
</template>

<script setup>
import { computed } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { TrophyIcon } from '@heroicons/vue/24/solid'
import UserAvatar from '@/components/common/UserAvatar.vue'

const props = defineProps({
  entries: {
    type: Array,
    default: () => [],
  },
})

const authStore = useAuthStore()

const topThree = computed(() => props.entries.slice(0, 3))

const firstName = (entry) => entry.user?.name?.split(' ')[0] || '?'

const isCurrentUser = (entry) => entry.user_id === authStore.currentUser?.id
</script>

<style scoped>
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

.crown-bounce {
  animation: crown-bounce 2s ease-in-out infinite;
}

@keyframes crown-bounce {
  0%, 100% { transform: translateY(0) rotate(0deg); }
  25% { transform: translateY(-2px) rotate(-3deg); }
  75% { transform: translateY(-2px) rotate(3deg); }
}

.animate-slide-up {
  animation: slide-up 0.5s ease-out both;
}

.animate-slide-up-delayed {
  animation: slide-up 0.5s ease-out 0.075s both;
}

.animate-slide-up-delayed-2 {
  animation: slide-up 0.5s ease-out 0.15s both;
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
