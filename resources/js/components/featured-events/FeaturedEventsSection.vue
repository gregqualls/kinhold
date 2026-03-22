<template>
  <BaseCard class="md:col-span-2 lg:col-span-3" shadow="lg">
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-lg font-semibold text-prussian-500 dark:text-lavender-200 flex items-center gap-2">
        <StarIcon class="w-5 h-5 text-sand-500" />
        Coming Up
      </h2>
      <button
        v-if="isParent"
        @click="showCreateModal = true"
        class="flex items-center gap-1 text-sm font-medium text-wisteria-600 dark:text-wisteria-400 hover:text-wisteria-500 transition-colors"
      >
        <PlusIcon class="w-4 h-4" />
        Add Event
      </button>
    </div>

    <!-- Loading -->
    <div v-if="isLoading" class="flex justify-center py-6">
      <LoadingSpinner size="sm" />
    </div>

    <!-- Events list -->
    <div v-else-if="upcomingEvents.length > 0" class="space-y-3">
      <div
        v-for="event in upcomingEvents.slice(0, 6)"
        :key="event.id"
        class="flex items-center gap-3 p-3 rounded-xl transition-all duration-300"
        :class="isToday(event.event_date)
          ? 'bg-gradient-to-r from-sand-100/80 to-wisteria-100/60 dark:from-sand-900/30 dark:to-wisteria-900/30 ring-1 ring-sand-300/50 dark:ring-sand-700/50 animate-pulse-subtle'
          : 'bg-lavender-50/50 dark:bg-prussian-700/50'"
      >
        <!-- Icon -->
        <div
          class="flex-shrink-0 w-10 h-10 rounded-xl flex items-center justify-center text-lg"
          :class="isToday(event.event_date) ? 'shadow-md' : ''"
          :style="{ backgroundColor: event.color + '20' }"
        >
          {{ event.icon }}
        </div>

        <!-- Info -->
        <div class="flex-1 min-w-0">
          <p class="font-semibold text-prussian-500 dark:text-lavender-200 truncate">
            {{ event.title }}
          </p>
          <div class="flex items-center gap-2 text-xs flex-wrap">
            <span class="text-lavender-600 dark:text-lavender-400">
              {{ formatEventDate(event.event_date) }}
            </span>
            <span v-if="event.event_time" class="text-lavender-500 dark:text-lavender-400">
              {{ formatTime(event.event_time) }}
            </span>
            <span
              v-if="event.recurrence_label"
              class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full bg-wisteria-100/60 dark:bg-wisteria-900/30 text-wisteria-600 dark:text-wisteria-400 text-[10px] font-medium"
            >
              <ArrowPathIcon class="w-2.5 h-2.5" />
              {{ event.recurrence_label }}
            </span>
          </div>
        </div>

        <!-- Countdown badge -->
        <div class="flex-shrink-0 text-right">
          <span
            class="inline-block px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wide"
            :class="countdownClasses(event.event_date)"
          >
            {{ getCountdown(event.event_date) }}
          </span>
        </div>

        <!-- Parent actions -->
        <div v-if="isParent" class="flex-shrink-0 relative">
          <button
            @click.stop="toggleMenu(event.id)"
            class="p-1 rounded-lg hover:bg-lavender-200/50 dark:hover:bg-prussian-600/50 transition-colors text-lavender-400 dark:text-lavender-500"
          >
            <EllipsisVerticalIcon class="w-4 h-4" />
          </button>
          <div
            v-if="openMenuId === event.id"
            class="absolute right-0 top-full mt-1 w-32 bg-white dark:bg-prussian-800 rounded-lg shadow-lg border border-lavender-200 dark:border-prussian-700 z-10 py-1"
          >
            <button
              @click="editEvent(event)"
              class="w-full text-left px-3 py-1.5 text-sm text-prussian-500 dark:text-lavender-200 hover:bg-lavender-50 dark:hover:bg-prussian-700"
            >
              Edit
            </button>
            <button
              @click="confirmDelete(event)"
              class="w-full text-left px-3 py-1.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20"
            >
              Delete
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty state -->
    <EmptyState
      v-else
      :icon="StarIcon"
      title="No upcoming events"
      :description="isParent ? 'Add a featured event to highlight for the family!' : 'Check back soon for upcoming events!'"
    />

    <!-- Create/Edit Modal -->
    <FeaturedEventModal
      :show="showCreateModal || showEditModal"
      :event="editingEvent"
      @close="closeModals"
      @save="handleSave"
    />

    <!-- Delete confirmation -->
    <ConfirmDialog
      :show="showDeleteConfirm"
      title="Delete Event"
      :message="`Are you sure you want to delete '${deletingEvent?.title}'?`"
      confirm-text="Delete"
      variant="danger"
      @confirm="handleDelete"
      @cancel="showDeleteConfirm = false"
    />
  </BaseCard>
</template>

<script setup>
import { ref, computed } from 'vue'
import { DateTime } from 'luxon'
import { storeToRefs } from 'pinia'
import { useFeaturedEventsStore } from '@/stores/featuredEvents'
import BaseCard from '@/components/common/BaseCard.vue'
import EmptyState from '@/components/common/EmptyState.vue'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import ConfirmDialog from '@/components/common/ConfirmDialog.vue'
import FeaturedEventModal from './FeaturedEventModal.vue'
import { StarIcon, PlusIcon, EllipsisVerticalIcon, ArrowPathIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  isParent: Boolean,
})

const store = useFeaturedEventsStore()
const { upcomingEvents, isLoading } = storeToRefs(store)
const { getCountdown } = store

const showCreateModal = ref(false)
const showEditModal = ref(false)
const editingEvent = ref(null)
const showDeleteConfirm = ref(false)
const deletingEvent = ref(null)
const openMenuId = ref(null)

const isToday = (dateStr) => {
  return DateTime.fromISO(dateStr).hasSame(DateTime.now(), 'day')
}

const formatEventDate = (dateStr) => {
  return DateTime.fromISO(dateStr).toFormat('EEE, MMM d')
}

const formatTime = (timeStr) => {
  // timeStr is "HH:mm"
  const [h, m] = timeStr.split(':')
  return DateTime.fromObject({ hour: parseInt(h), minute: parseInt(m) }).toFormat('h:mm a')
}

const countdownClasses = (dateStr) => {
  const countdown = getCountdown(dateStr)
  if (countdown === 'today!') {
    return 'bg-sand-200 text-sand-800 dark:bg-sand-800/40 dark:text-sand-300 animate-bounce-gentle'
  }
  if (countdown === 'tomorrow') {
    return 'bg-wisteria-100 text-wisteria-700 dark:bg-wisteria-900/40 dark:text-wisteria-300'
  }
  return 'bg-lavender-100 text-lavender-700 dark:bg-prussian-600 dark:text-lavender-300'
}

const toggleMenu = (id) => {
  openMenuId.value = openMenuId.value === id ? null : id
}

const editEvent = (event) => {
  editingEvent.value = { ...event }
  showEditModal.value = true
  openMenuId.value = null
}

const confirmDelete = (event) => {
  deletingEvent.value = event
  showDeleteConfirm.value = true
  openMenuId.value = null
}

const closeModals = () => {
  showCreateModal.value = false
  showEditModal.value = false
  editingEvent.value = null
}

const handleSave = async (eventData) => {
  try {
    if (editingEvent.value?.id) {
      await store.updateEvent(editingEvent.value.id, eventData)
    } else {
      await store.createEvent(eventData)
    }
    closeModals()
  } catch {
    // error is set in the store
  }
}

const handleDelete = async () => {
  if (!deletingEvent.value) return
  try {
    await store.deleteEvent(deletingEvent.value.id)
  } catch {
    // error is set in the store
  }
  showDeleteConfirm.value = false
  deletingEvent.value = null
}

// Close menu on click outside
if (typeof document !== 'undefined') {
  document.addEventListener('click', () => {
    openMenuId.value = null
  })
}
</script>
