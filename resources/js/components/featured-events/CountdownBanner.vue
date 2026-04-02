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
      v-if="countdownEvent && !isDismissed && !isPast"
      class="relative overflow-hidden rounded-[12px] p-4 md:p-5 mb-4 md:mb-6"
      :style="bannerStyle"
    >
      <!-- Background shimmer effect -->
      <div class="absolute inset-0 opacity-20 pointer-events-none">
        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/30 to-transparent animate-shimmer"></div>
      </div>

      <div class="relative flex items-center gap-3 md:gap-4">
        <!-- Event icon -->
        <div
          class="flex-shrink-0 w-12 h-12 md:w-14 md:h-14 rounded-xl flex items-center justify-center bg-white/20 backdrop-blur-sm shadow-sm text-white"
          :class="{ 'animate-bounce-gentle': isToday }"
        >
          <IconRenderer :icon="countdownEvent.icon || 'confetti'" :size="28" />
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
            <template v-else>
              {{ countdownText }}
            </template>
          </p>
        </div>

        <!-- Celebration icon for today -->
        <div v-if="isToday" class="flex-shrink-0 animate-bounce-gentle text-white">
          <IconRenderer icon="confetti" :size="32" />
        </div>

        <!-- Parent actions menu -->
        <div v-if="isParent" class="flex-shrink-0 relative">
          <button
            class="p-1.5 rounded-full bg-white/20 hover:bg-white/30 backdrop-blur-sm transition-colors text-white/80 hover:text-white"
            aria-label="Manage countdown"
            @click.stop="showMenu = !showMenu"
          >
            <EllipsisVerticalIcon class="w-4 h-4" />
          </button>
          <div
            v-if="showMenu"
            class="absolute right-0 top-full mt-1 w-40 bg-white dark:bg-prussian-800 rounded-lg shadow-lg border border-lavender-200 dark:border-prussian-700 z-10 py-1"
          >
            <button
              class="w-full text-left px-3 py-1.5 text-sm text-prussian-500 dark:text-lavender-200 hover:bg-lavender-50 dark:hover:bg-prussian-700"
              @click="handleEdit"
            >
              Edit Event
            </button>
            <button
              class="w-full text-left px-3 py-1.5 text-sm text-prussian-500 dark:text-lavender-200 hover:bg-lavender-50 dark:hover:bg-prussian-700"
              @click="handleRemoveCountdown"
            >
              Remove Countdown
            </button>
            <button
              class="w-full text-left px-3 py-1.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20"
              @click="handleDelete"
            >
              Delete Event
            </button>
          </div>
        </div>

        <!-- Dismiss button -->
        <button
          class="flex-shrink-0 p-1.5 rounded-full bg-white/20 hover:bg-white/30 backdrop-blur-sm transition-colors text-white/80 hover:text-white"
          aria-label="Dismiss countdown"
          @click="dismiss"
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

  <!-- Edit Modal -->
  <EventModal
    :show="showEditModal"
    :event="countdownEvent"
    mode="featured"
    @close="showEditModal = false"
    @save="handleSave"
  />

  <!-- Delete Confirmation -->
  <ConfirmDialog
    :show="showDeleteConfirm"
    title="Delete Event"
    :message="`Are you sure you want to delete '${countdownEvent?.title}'?`"
    confirm-text="Delete"
    variant="danger"
    @confirm="confirmDelete"
    @cancel="showDeleteConfirm = false"
  />
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue'
import { DateTime } from 'luxon'
import { XMarkIcon, EllipsisVerticalIcon } from '@heroicons/vue/24/outline'
import IconRenderer from '@/components/common/IconRenderer.vue'
import EventModal from '@/components/common/EventModal.vue'
import ConfirmDialog from '@/components/common/ConfirmDialog.vue'
import { useFeaturedEventsStore } from '@/stores/featuredEvents'
import { useAuthStore } from '@/stores/auth'

const props = defineProps({
  countdownEvent: {
    type: Object,
    default: null,
  },
})

const featuredEventsStore = useFeaturedEventsStore()
const authStore = useAuthStore()

const isParent = computed(() => authStore.currentUser?.family_role === 'parent')

const isDismissed = ref(false)
const showMenu = ref(false)
const showEditModal = ref(false)
const showDeleteConfirm = ref(false)
const now = ref(DateTime.now())
let intervalId = null

// Persist dismiss state in localStorage keyed by event ID
const dismissKey = computed(() =>
  props.countdownEvent ? `countdown-dismissed-${props.countdownEvent.id}` : null
)

onMounted(() => {
  // Restore dismiss state (if event is already available at mount time)
  if (dismissKey.value && localStorage.getItem(dismissKey.value) === 'true') {
    isDismissed.value = true
  }

  intervalId = setInterval(() => {
    now.value = DateTime.now()
  }, 60_000)
})

onUnmounted(() => {
  if (intervalId) {
    clearInterval(intervalId)
  }
})

// Restore dismiss state when countdownEvent arrives async (prop may be null at mount)
watch(
  dismissKey,
  (newKey) => {
    if (newKey && localStorage.getItem(newKey) === 'true') {
      isDismissed.value = true
    }
  },
  { immediate: true }
)

// When countdown event changes to a DIFFERENT event (not initial load), reset dismiss
watch(
  () => props.countdownEvent?.id,
  (newId, oldId) => {
    if (oldId && newId && newId !== oldId) {
      // Clear old dismiss — a genuinely new countdown was set
      localStorage.removeItem(`countdown-dismissed-${oldId}`)
      isDismissed.value = false
    }
  }
)

const targetDateTime = computed(() => {
  if (!props.countdownEvent) return null
  const date = DateTime.fromISO(props.countdownEvent.event_date)
  if (props.countdownEvent.event_time) {
    const [h, m] = props.countdownEvent.event_time.split(':')
    return date.set({ hour: parseInt(h), minute: parseInt(m), second: 0 })
  }
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
  if (dismissKey.value) {
    localStorage.setItem(dismissKey.value, 'true')
  }
}

const handleEdit = () => {
  showMenu.value = false
  showEditModal.value = true
}

const handleRemoveCountdown = async () => {
  showMenu.value = false
  if (props.countdownEvent) {
    await featuredEventsStore.setCountdown(props.countdownEvent.id)
  }
}

const handleDelete = () => {
  showMenu.value = false
  showDeleteConfirm.value = true
}

const handleSave = async (eventData) => {
  if (props.countdownEvent?.id) {
    await featuredEventsStore.updateEvent(props.countdownEvent.id, eventData)
  }
  showEditModal.value = false
}

const confirmDelete = async () => {
  if (props.countdownEvent) {
    await featuredEventsStore.deleteEvent(props.countdownEvent.id)
  }
  showDeleteConfirm.value = false
}

const formatEventDate = (dateStr) => {
  return DateTime.fromISO(dateStr).toFormat('EEEE, MMMM d, yyyy')
}

const formatTime = (timeStr) => {
  const [h, m] = timeStr.split(':')
  return DateTime.fromObject({ hour: parseInt(h), minute: parseInt(m) }).toFormat('h:mm a')
}

function adjustColor(hex, amount) {
  let r = parseInt(hex.slice(1, 3), 16)
  let g = parseInt(hex.slice(3, 5), 16)
  let b = parseInt(hex.slice(5, 7), 16)
  r = Math.max(0, Math.min(255, r + amount))
  g = Math.max(0, Math.min(255, g + amount))
  b = Math.max(0, Math.min(255, b + amount))
  return `#${r.toString(16).padStart(2, '0')}${g.toString(16).padStart(2, '0')}${b.toString(16).padStart(2, '0')}`
}

// Close menu on click outside
if (typeof document !== 'undefined') {
  document.addEventListener('click', () => {
    showMenu.value = false
  })
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
