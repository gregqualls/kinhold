<template>
  <transition
    enter-active-class="transition duration-300 ease-out"
    enter-from-class="opacity-0 -translate-y-2"
    enter-to-class="opacity-100 translate-y-0"
    leave-active-class="transition duration-200 ease-in"
    leave-from-class="opacity-100 translate-y-0"
    leave-to-class="opacity-0 -translate-y-2"
  >
    <div
      v-if="countdownEvent && !isDismissed"
      class="relative overflow-hidden rounded-2xl p-4 md:p-5 mb-4 md:mb-6"
      :style="bannerStyle"
    >
      <!-- Background shimmer effect -->
      <div class="absolute inset-0 opacity-20 pointer-events-none">
        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/30 to-transparent animate-shimmer" />
      </div>

      <div class="relative flex items-center gap-3 md:gap-4">
        <!-- Event icon -->
        <div
          class="flex-shrink-0 w-12 h-12 md:w-14 md:h-14 rounded-xl flex items-center justify-center text-2xl md:text-3xl bg-white/20 backdrop-blur-sm shadow-sm"
          :class="{ 'animate-bounce-gentle': isToday }"
        >
          {{ countdownEvent.icon }}
        </div>

        <!-- Countdown content -->
        <div class="flex-1 min-w-0">
          <p class="text-sm md:text-base font-bold text-white truncate">
            {{ countdownEvent.title }}
          </p>
          <p class="text-lg md:text-xl font-extrabold text-white/95 leading-tight">
            <template v-if="isToday">
              {{ countdownEvent.title }} is TODAY!
            </template>
            <template v-else-if="isPast">
              {{ countdownEvent.title }} has passed
            </template>
            <template v-else>
              {{ countdownText }}
            </template>
          </p>
        </div>

        <!-- Celebration particles for today -->
        <div v-if="isToday" class="flex-shrink-0 text-2xl md:text-3xl animate-bounce-gentle">
          &#127881;
        </div>

        <!-- Dismiss button -->
        <button
          @click="dismiss"
          class="flex-shrink-0 p-1.5 rounded-full bg-white/20 hover:bg-white/30 backdrop-blur-sm transition-colors text-white/80 hover:text-white"
          aria-label="Dismiss countdown"
        >
          <XMarkIcon class="w-4 h-4" />
        </button>
      </div>

      <!-- Date line below countdown -->
      <div class="relative mt-1 ml-[60px] md:ml-[72px]">
        <p class="text-xs text-white/70">
          {{ formatEventDate(countdownEvent.event_date) }}
          <span v-if="countdownEvent.event_time"> at {{ formatTime(countdownEvent.event_time) }}</span>
        </p>
      </div>
    </div>
  </transition>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { DateTime } from 'luxon'
import { XMarkIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  countdownEvent: {
    type: Object,
    default: null,
  },
})

const isDismissed = ref(false)
const now = ref(DateTime.now())
let intervalId = null

onMounted(() => {
  // Update every 60 seconds for the live countdown
  intervalId = setInterval(() => {
    now.value = DateTime.now()
  }, 60_000)
})

onUnmounted(() => {
  if (intervalId) {
    clearInterval(intervalId)
  }
})

const targetDateTime = computed(() => {
  if (!props.countdownEvent) return null
  const date = DateTime.fromISO(props.countdownEvent.event_date)
  if (props.countdownEvent.event_time) {
    const [h, m] = props.countdownEvent.event_time.split(':')
    return date.set({ hour: parseInt(h), minute: parseInt(m), second: 0 })
  }
  // No time set — count down to midnight (start of the day)
  return date.startOf('day')
})

const isToday = computed(() => {
  if (!targetDateTime.value) return false
  return targetDateTime.value.hasSame(now.value, 'day')
})

const isPast = computed(() => {
  if (!targetDateTime.value) return false
  return targetDateTime.value < now.value && !isToday.value
})

const countdownText = computed(() => {
  if (!targetDateTime.value) return ''
  const diff = targetDateTime.value.diff(now.value, ['days', 'hours', 'minutes']).toObject()
  const days = Math.floor(diff.days || 0)
  const hours = Math.floor(diff.hours || 0)
  const minutes = Math.max(0, Math.floor(diff.minutes || 0))

  const parts = []
  if (days > 0) parts.push(`${days} day${days !== 1 ? 's' : ''}`)
  if (hours > 0) parts.push(`${hours} hr${hours !== 1 ? 's' : ''}`)
  if (days === 0 && minutes > 0) parts.push(`${minutes} min${minutes !== 1 ? 's' : ''}`)
  // If only days remain, still show hours for more detail
  if (parts.length === 0) parts.push('less than a minute')

  return parts.join(', ') + ' to go!'
})

const bannerStyle = computed(() => {
  const color = props.countdownEvent?.color || '#8B5CF6'
  return {
    background: `linear-gradient(135deg, ${color}, ${adjustColor(color, -30)})`,
  }
})

const dismiss = () => {
  isDismissed.value = true
}

const formatEventDate = (dateStr) => {
  return DateTime.fromISO(dateStr).toFormat('EEEE, MMMM d, yyyy')
}

const formatTime = (timeStr) => {
  const [h, m] = timeStr.split(':')
  return DateTime.fromObject({ hour: parseInt(h), minute: parseInt(m) }).toFormat('h:mm a')
}

/**
 * Darken or lighten a hex color by an amount.
 */
function adjustColor(hex, amount) {
  let r = parseInt(hex.slice(1, 3), 16)
  let g = parseInt(hex.slice(3, 5), 16)
  let b = parseInt(hex.slice(5, 7), 16)
  r = Math.max(0, Math.min(255, r + amount))
  g = Math.max(0, Math.min(255, g + amount))
  b = Math.max(0, Math.min(255, b + amount))
  return `#${r.toString(16).padStart(2, '0')}${g.toString(16).padStart(2, '0')}${b.toString(16).padStart(2, '0')}`
}
</script>

<style scoped>
@keyframes shimmer {
  0% {
    transform: translateX(-100%);
  }
  100% {
    transform: translateX(100%);
  }
}

.animate-shimmer {
  animation: shimmer 3s ease-in-out infinite;
}

@keyframes bounce-gentle {
  0%, 100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-4px);
  }
}

.animate-bounce-gentle {
  animation: bounce-gentle 2s ease-in-out infinite;
}
</style>
