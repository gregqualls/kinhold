<template>
  <!-- Birthday Banner — prominent, with confetti -->
  <Transition name="celebration-slide">
    <div
      v-if="showBirthday && !birthdayDismissed"
      class="relative mb-4 overflow-hidden rounded-[12px] bg-gradient-to-r from-wisteria-500 via-pink-500 to-sand-400 dark:from-wisteria-700 dark:via-pink-700 dark:to-sand-600 p-4 md:p-5 shadow-lg"
    >
      <!-- Confetti overlay -->
      <div class="absolute inset-0 pointer-events-none overflow-hidden">
        <div
          v-for="i in 12"
          :key="'confetti-' + i"
          class="celebration-confetti-piece"
          :style="confettiStyle(i)"
        ></div>
      </div>

      <!-- Content -->
      <div class="relative z-10 flex items-center justify-between gap-3">
        <div class="flex-1 min-w-0">
          <p class="text-white text-lg md:text-xl font-bold leading-tight">
            <template v-if="isMyBirthday">
              Happy Birthday! The whole family celebrates you today!
            </template>
            <template v-else>
              <span v-for="(member, idx) in birthdayMembers" :key="member.id">
                <template v-if="idx > 0 && idx === birthdayMembers.length - 1"> &amp; </template>
                <template v-else-if="idx > 0">, </template>
                Today is {{ member.name.split(' ')[0] }}'s birthday!
              </span>
            </template>
          </p>
          <p class="text-white/80 text-sm mt-1">
            <template v-if="isMyBirthday">
              Enjoy your special day!
            </template>
            <template v-else>
              Send them some love today!
            </template>
          </p>
        </div>

        <!-- Dismiss button -->
        <button
          class="flex-shrink-0 w-8 h-8 rounded-full bg-white/20 hover:bg-white/30 flex items-center justify-center transition-colors"
          aria-label="Dismiss birthday banner"
          @click="dismissBirthday"
        >
          <XMarkIcon class="w-5 h-5 text-white" />
        </button>
      </div>
    </div>
  </Transition>

  <!-- Holiday Banner — subtle, smaller -->
  <Transition name="celebration-slide">
    <div
      v-if="showHoliday && !holidayDismissed"
      class="relative mb-4 overflow-hidden rounded-[12px] bg-surface-sunken border border-border-subtle px-4 py-3 shadow-sm"
    >
      <div class="flex items-center justify-between gap-3">
        <p class="text-sm md:text-base font-semibold text-ink-primary">
          <component :is="holidayIconComponent" class="w-4 h-4 inline-block mr-1 align-text-bottom" />
          {{ holiday.message }}
        </p>

        <button
          class="flex-shrink-0 w-6 h-6 rounded-full hover:bg-surface-overlay flex items-center justify-center transition-colors"
          aria-label="Dismiss holiday banner"
          @click="dismissHoliday"
        >
          <XMarkIcon class="w-4 h-4 text-ink-tertiary" />
        </button>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { ref, computed } from 'vue'
import { XMarkIcon, GiftIcon, MoonIcon } from '@heroicons/vue/24/outline'
import { SparklesIcon, HeartIcon, StarIcon } from '@heroicons/vue/24/solid'

const props = defineProps({
  /** Whether to show the birthday banner */
  showBirthday: {
    type: Boolean,
    default: false,
  },
  /** Whether today is the current user's own birthday */
  isMyBirthday: {
    type: Boolean,
    default: false,
  },
  /** Array of family members whose birthday is today */
  birthdayMembers: {
    type: Array,
    default: () => [],
  },
  /** Whether to show the holiday banner */
  showHoliday: {
    type: Boolean,
    default: false,
  },
  /** Holiday object { emoji, message, name } */
  holiday: {
    type: Object,
    default: null,
  },
})

const holidayIconMap = {
  sparkles: SparklesIcon,
  heart: HeartIcon,
  star: StarIcon,
  moon: MoonIcon,
  gift: GiftIcon,
}

const holidayIconComponent = computed(() => {
  if (!props.holiday?.emoji) return SparklesIcon
  return holidayIconMap[props.holiday.emoji] || SparklesIcon
})

// Session-based dismissal (resets on page reload / new session)
const birthdayDismissed = ref(false)
const holidayDismissed = ref(false)

const dismissBirthday = () => {
  birthdayDismissed.value = true
}

const dismissHoliday = () => {
  holidayDismissed.value = true
}

// Confetti style generator — reuses the same approach as EasterEggs.vue
const confettiColors = ['#ffffff', '#FFD700', '#FF6B6B', '#4ECDC4', '#FF69B4', '#00CED1', '#FFA500', '#7B68EE']
const confettiStyle = (i) => {
  const color = confettiColors[i % confettiColors.length]
  const left = Math.random() * 100
  const delay = Math.random() * 1
  const duration = 1 + Math.random() * 1
  const size = 5 + Math.random() * 5
  const rotation = Math.random() * 360
  return {
    '--cel-color': color,
    left: `${left}%`,
    width: `${size}px`,
    height: `${size * 0.6}px`,
    animationDelay: `${delay}s`,
    animationDuration: `${duration}s`,
    transform: `rotate(${rotation}deg)`,
  }
}
</script>

<style scoped>
/* Confetti falling animation */
@keyframes celebration-confetti-fall {
  0% {
    transform: translateY(-20px) rotate(0deg);
    opacity: 1;
  }
  100% {
    transform: translateY(200px) rotate(720deg);
    opacity: 0;
  }
}

.celebration-confetti-piece {
  position: absolute;
  top: -10px;
  background: var(--cel-color);
  border-radius: 2px;
  animation: celebration-confetti-fall linear infinite;
}

/* Banner slide transition */
.celebration-slide-enter-active {
  transition: all 0.4s ease-out;
}
.celebration-slide-leave-active {
  transition: all 0.3s ease-in;
}
.celebration-slide-enter-from {
  opacity: 0;
  transform: translateY(-20px);
}
.celebration-slide-leave-to {
  opacity: 0;
  transform: translateY(-20px);
}
</style>
