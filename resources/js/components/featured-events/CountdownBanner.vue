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
      class="kin-countdown relative overflow-hidden rounded-card border border-border-subtle p-4 md:p-5 mb-4 md:mb-6 bg-surface-raised"
    >
      <!-- Background shimmer effect -->
      <div class="absolute inset-0 opacity-30 pointer-events-none">
        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/40 to-transparent animate-shimmer"></div>
      </div>

      <div class="relative flex items-center gap-3 md:gap-4">
        <!-- Event icon -->
        <div
          class="kin-countdown__icon flex-shrink-0 w-12 h-12 md:w-14 md:h-14 rounded-xl flex items-center justify-center bg-accent-peach-soft text-accent-peach-bold shadow-resting"
          :class="{ 'animate-bounce-gentle': isToday }"
        >
          <IconRenderer :icon="countdownEvent.icon || 'confetti'" :size="28" />
        </div>

        <!-- Countdown content. Text colors are pinned (kin-countdown__title /
             __value / __date) because the card itself is a "light pastel
             island" in both themes — see scoped styles. -->
        <div class="flex-1 min-w-0">
          <p class="kin-countdown__title text-sm md:text-base font-semibold truncate">
            {{ countdownEvent.title }}
          </p>
          <p class="kin-countdown__value text-lg md:text-xl font-extrabold leading-tight">
            <template v-if="isToday">
              {{ countdownEvent.title }} is TODAY!
            </template>
            <template v-else>
              {{ countdownText }}
            </template>
          </p>
        </div>

        <!-- Celebration icon for today -->
        <div v-if="isToday" class="flex-shrink-0 animate-bounce-gentle text-accent-sun-bold">
          <IconRenderer icon="confetti" :size="32" />
        </div>

        <!-- Parent actions menu -->
        <div v-if="isParent" class="flex-shrink-0 relative">
          <button
            type="button"
            class="kin-countdown__action p-1.5 rounded-full transition-colors"
            aria-label="Manage countdown"
            @click.stop="showMenu = !showMenu"
          >
            <EllipsisVerticalIcon class="w-4 h-4" />
          </button>
          <div
            v-if="showMenu"
            class="absolute right-0 top-full mt-1 w-40 bg-surface-raised rounded-lg shadow-elevated border border-border-subtle z-10 py-1"
          >
            <button
              type="button"
              class="w-full text-left px-3 py-1.5 text-sm text-ink-primary hover:bg-surface-sunken"
              @click="handleEdit"
            >
              Edit Event
            </button>
            <button
              type="button"
              class="w-full text-left px-3 py-1.5 text-sm text-ink-primary hover:bg-surface-sunken"
              @click="handleRemoveCountdown"
            >
              Remove Countdown
            </button>
            <button
              type="button"
              class="w-full text-left px-3 py-1.5 text-sm text-status-failed hover:bg-status-failed/10"
              @click="handleDelete"
            >
              Delete Event
            </button>
          </div>
        </div>

        <!-- Dismiss button -->
        <button
          type="button"
          class="flex-shrink-0 p-1.5 rounded-full bg-surface-sunken hover:bg-surface-overlay text-ink-secondary hover:text-ink-primary transition-colors"
          aria-label="Dismiss countdown"
          @click="dismiss"
        >
          <XMarkIcon class="w-4 h-4" />
        </button>
      </div>

      <!-- Date line below countdown -->
      <div class="relative mt-1 ml-[60px] md:ml-[72px]">
        <p class="kin-countdown__date text-xs">
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

// Close menu on click outside
if (typeof document !== 'undefined') {
  document.addEventListener('click', () => {
    showMenu.value = false
  })
}
</script>

<style scoped>
/*
  Warm iridescent gradient — same wash KinGradientCard variant="warm" uses.
  This card is a "lightness island" — it always reads as a pastel-warm
  surface with dark text, in both themes. That's because the gradient is
  inherently bright (peach + sun overlays) and dark text on the warm wash
  is the readable combo regardless of the surrounding theme.
*/
.kin-countdown {
  /* Light/cream base in both themes so the warm overlays land on a bright
     surface. In dark mode this means the card visibly "lifts" from the
     deep charcoal page like a celebration card should. */
  background-color: #FBF6EE;
  background-image: var(--gradient-iridescent-warm);
}

/* Pinned text colors — dark on light wash, regardless of theme. */
.kin-countdown__title { color: rgba(28, 28, 30, 0.65); }
.kin-countdown__value { color: #1C1C1E; }
.kin-countdown__date  { color: rgba(28, 28, 30, 0.55); }

/* Border + icon shadow tweaks for the pinned-light surface in dark mode. */
.dark .kin-countdown {
  border-color: rgba(0, 0, 0, 0.10);
}

/* Dismiss / menu buttons — pinned to a translucent dark fill so they read
   on the warm pastel base in both themes. */
.kin-countdown__action {
  background-color: rgba(28, 28, 30, 0.08);
  color: rgba(28, 28, 30, 0.65);
}
.kin-countdown__action:hover {
  background-color: rgba(28, 28, 30, 0.16);
  color: #1C1C1E;
}

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
