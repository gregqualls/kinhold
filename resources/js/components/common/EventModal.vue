<template>
  <BaseModal :show="show" :title="modalTitle" size="lg" @close="$emit('close')">
    <form class="space-y-4" @submit.prevent="handleSubmit">
      <!-- Title -->
      <div>
        <label class="block text-sm font-medium text-prussian-500 dark:text-lavender-300 mb-1">
          Title <span class="text-red-500">*</span>
        </label>
        <input
          v-model="form.title"
          type="text"
          :placeholder="mode === 'featured' ? 'Birthday, Game Day, Vacation...' : 'Doctor appointment, Family dinner...'"
          class="input-base"
          required
        />
      </div>

      <!-- All-day toggle (calendar mode only) -->
      <div v-if="mode === 'calendar'" class="flex items-center justify-between">
        <label class="text-sm font-medium text-prussian-500 dark:text-lavender-300">All-day event</label>
        <ToggleSwitch v-model="form.all_day" />
      </div>

      <!-- Date & Time row -->
      <div class="grid gap-3" :class="mode === 'calendar' && !form.all_day ? 'grid-cols-2' : 'grid-cols-1'">
        <div>
          <label class="block text-sm font-medium text-prussian-500 dark:text-lavender-300 mb-1">
            {{ mode === 'calendar' ? 'Start Date' : 'Date' }} <span class="text-red-500">*</span>
          </label>
          <input
            v-model="form.start_date"
            type="date"
            class="input-base"
            required
          />
        </div>
        <div v-if="mode === 'calendar' && !form.all_day">
          <label class="block text-sm font-medium text-prussian-500 dark:text-lavender-300 mb-1">
            Start Time
          </label>
          <input
            v-model="form.start_time"
            type="time"
            class="input-base"
          />
        </div>
      </div>

      <!-- End date/time (calendar mode, non-all-day) -->
      <div v-if="mode === 'calendar' && !form.all_day" class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-sm font-medium text-prussian-500 dark:text-lavender-300 mb-1">
            End Date
          </label>
          <input
            v-model="form.end_date"
            type="date"
            class="input-base"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-prussian-500 dark:text-lavender-300 mb-1">
            End Time
          </label>
          <input
            v-model="form.end_time"
            type="time"
            class="input-base"
          />
        </div>
      </div>

      <!-- Time (featured mode only — optional) -->
      <div v-if="mode === 'featured'">
        <label class="block text-sm font-medium text-prussian-500 dark:text-lavender-300 mb-1">
          Time <span class="text-lavender-400 dark:text-lavender-500 text-xs">(optional)</span>
        </label>
        <input
          v-model="form.event_time"
          type="time"
          class="input-base"
        />
      </div>

      <!-- Location (calendar mode only) -->
      <div v-if="mode === 'calendar'">
        <label class="block text-sm font-medium text-prussian-500 dark:text-lavender-300 mb-1">
          Location <span class="text-lavender-400 dark:text-lavender-500 text-xs">(optional)</span>
        </label>
        <input
          v-model="form.location"
          type="text"
          placeholder="Address or link..."
          class="input-base"
        />
      </div>

      <!-- Recurrence -->
      <div>
        <label class="block text-sm font-medium text-prussian-500 dark:text-lavender-300 mb-1">
          Repeats
        </label>
        <select v-model="form.recurrence" class="input-base">
          <option value="none">One-time event</option>
          <option value="yearly">Every year (birthdays, anniversaries)</option>
          <option value="monthly">Every month</option>
          <option value="weekly">Every week</option>
        </select>
      </div>

      <!-- Visibility (calendar mode only) -->
      <div v-if="mode === 'calendar'">
        <label class="block text-sm font-medium text-prussian-500 dark:text-lavender-300 mb-1">
          Who can see this?
        </label>
        <select v-model="form.visibility" class="input-base">
          <option value="visible">Everyone — full details</option>
          <option value="busy">Everyone — show as busy (no details)</option>
          <option value="private">Only me</option>
        </select>
      </div>

      <!-- Feature this event toggle (calendar mode) -->
      <div v-if="mode === 'calendar'" class="flex items-center justify-between">
        <div>
          <label class="text-sm font-medium text-prussian-500 dark:text-lavender-300">Feature this event</label>
          <p class="text-xs text-lavender-500 dark:text-lavender-400">Show on dashboard with countdown</p>
        </div>
        <ToggleSwitch v-model="form.is_featured" />
      </div>

      <!-- Featured scope (when featured is enabled, or in featured mode) -->
      <div v-if="form.is_featured || mode === 'featured'">
        <label class="block text-sm font-medium text-prussian-500 dark:text-lavender-300 mb-1">
          Feature for
        </label>
        <select v-model="form.featured_scope" class="input-base">
          <option value="family">Whole family</option>
          <option value="personal">Just me</option>
        </select>
      </div>

      <!-- Description -->
      <div>
        <label class="block text-sm font-medium text-prussian-500 dark:text-lavender-300 mb-1">
          Description <span class="text-lavender-400 dark:text-lavender-500 text-xs">(optional)</span>
        </label>
        <textarea
          v-model="form.description"
          rows="2"
          placeholder="Add details..."
          class="input-base resize-none"
        ></textarea>
      </div>

      <!-- Icon picker (featured mode or when featured enabled) -->
      <div v-if="form.is_featured || mode === 'featured'">
        <label class="block text-sm font-medium text-prussian-500 dark:text-lavender-300 mb-2">
          Icon
        </label>
        <IconPicker v-model="form.icon" />
      </div>

      <!-- Color picker -->
      <div>
        <label class="block text-sm font-medium text-prussian-500 dark:text-lavender-300 mb-2">
          Color
        </label>
        <div class="flex flex-wrap gap-2">
          <button
            v-for="color in colorOptions"
            :key="color.value"
            type="button"
            class="w-8 h-8 rounded-full transition-all"
            :class="form.color === color.value ? 'ring-2 ring-offset-2 ring-offset-white dark:ring-offset-prussian-800 scale-110' : 'hover:scale-105'"
            :style="{ backgroundColor: color.value, ringColor: color.value }"
            :title="color.label"
            @click="form.color = color.value"
          ></button>
        </div>
      </div>

      <!-- Error -->
      <p v-if="error" class="text-sm text-red-600 dark:text-red-400">{{ error }}</p>
    </form>

    <template #footer>
      <button
        class="px-4 py-2 text-sm font-medium text-lavender-600 dark:text-lavender-400 hover:bg-lavender-100 dark:hover:bg-prussian-700 rounded-[10px] transition-colors"
        @click="$emit('close')"
      >
        Cancel
      </button>
      <button
        :disabled="!isValid || isSaving"
        class="px-4 py-2 text-sm font-medium text-white bg-wisteria-600 hover:bg-wisteria-700 rounded-[10px] transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
        @click="handleSubmit"
      >
        {{ isSaving ? 'Saving...' : (event ? 'Update' : 'Create') }}
      </button>
    </template>
  </BaseModal>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { DateTime } from 'luxon'
import BaseModal from '@/components/common/BaseModal.vue'
import IconPicker from '@/components/common/IconPicker.vue'
import ToggleSwitch from '@/components/common/ToggleSwitch.vue'

const props = defineProps({
  show: Boolean,
  event: {
    type: Object,
    default: null,
  },
  mode: {
    type: String,
    default: 'featured',
    validator: (v) => ['featured', 'calendar'].includes(v),
  },
  prefillDate: {
    type: String,
    default: null,
  },
})

const emit = defineEmits(['close', 'save'])

const colorOptions = [
  { value: '#8B5CF6', label: 'Wisteria' },
  { value: '#D4A843', label: 'Golden Sand' },
  { value: '#EF4444', label: 'Red' },
  { value: '#3B82F6', label: 'Blue' },
  { value: '#10B981', label: 'Green' },
  { value: '#F97316', label: 'Orange' },
  { value: '#EC4899', label: 'Pink' },
  { value: '#14B8A6', label: 'Teal' },
]

const defaultForm = () => ({
  title: '',
  description: '',
  start_date: props.prefillDate || '',
  start_time: '',
  end_date: '',
  end_time: '',
  event_time: '',
  all_day: true,
  location: '',
  recurrence: 'none',
  visibility: 'visible',
  is_featured: false,
  featured_scope: 'family',
  icon: 'confetti',
  color: '#8B5CF6',
})

const form = ref(defaultForm())
const isSaving = ref(false)
const error = ref(null)

const modalTitle = computed(() => {
  if (props.event) return 'Edit Event'
  return props.mode === 'featured' ? 'Add Featured Event' : 'Add Event'
})

const isValid = computed(() => {
  return form.value.title && form.value.start_date
})

watch(
  () => props.show,
  (newVal) => {
    if (newVal) {
      if (props.event) {
        populateFromEvent(props.event)
      } else {
        form.value = defaultForm()
        if (props.prefillDate) {
          form.value.start_date = props.prefillDate
        }
      }
      error.value = null
    }
  }
)

function populateFromEvent(event) {
  if (props.mode === 'featured') {
    // Featured event shape (from FeaturedEventResource)
    form.value = {
      title: event.title || '',
      description: event.description || '',
      start_date: event.event_date || '',
      start_time: '',
      end_date: '',
      end_time: '',
      event_time: event.event_time || '',
      all_day: true,
      location: '',
      recurrence: event.recurrence || 'none',
      visibility: 'visible',
      is_featured: true,
      featured_scope: event.featured_scope || 'family',
      icon: event.icon || 'confetti',
      color: event.color || '#8B5CF6',
    }
  } else {
    // Calendar event shape (from CalendarController events response)
    const startDt = event.start ? DateTime.fromISO(event.start) : null
    const endDt = event.end ? DateTime.fromISO(event.end) : null

    form.value = {
      title: event.title || '',
      description: event.description || '',
      start_date: startDt ? startDt.toISODate() : '',
      start_time: event.all_day ? '' : (startDt ? startDt.toFormat('HH:mm') : ''),
      end_date: endDt ? endDt.toISODate() : '',
      end_time: event.all_day ? '' : (endDt ? endDt.toFormat('HH:mm') : ''),
      event_time: '',
      all_day: event.all_day ?? true,
      location: event.location || '',
      recurrence: event.recurrence || 'none',
      visibility: event.visibility || 'visible',
      is_featured: !!event.featured_scope,
      featured_scope: event.featured_scope || 'family',
      icon: event.icon || 'confetti',
      color: event.user?.color || event.color || '#8B5CF6',
    }
  }
}

const handleSubmit = () => {
  if (!isValid.value) return

  isSaving.value = true
  error.value = null

  try {
    if (props.mode === 'featured') {
      // Emit featured event shape
      const data = {
        title: form.value.title,
        event_date: form.value.start_date,
        icon: form.value.icon,
        color: form.value.color,
        recurrence: form.value.recurrence,
        featured_scope: form.value.featured_scope,
      }
      if (form.value.description) data.description = form.value.description
      if (form.value.event_time) data.event_time = form.value.event_time
      emit('save', data)
    } else {
      // Emit calendar event shape
      let startTime, endTime

      if (form.value.all_day) {
        startTime = form.value.start_date + 'T00:00:00'
        endTime = form.value.end_date ? form.value.end_date + 'T23:59:59' : null
      } else {
        startTime = form.value.start_date + 'T' + (form.value.start_time || '00:00') + ':00'
        endTime = form.value.end_date
          ? form.value.end_date + 'T' + (form.value.end_time || '23:59') + ':00'
          : null
      }

      const data = {
        title: form.value.title,
        start_time: startTime,
        end_time: endTime,
        all_day: form.value.all_day,
        color: form.value.color,
        recurrence: form.value.recurrence,
        visibility: form.value.visibility,
      }

      if (form.value.description) data.description = form.value.description
      if (form.value.location) data.location = form.value.location

      // Featured settings
      if (form.value.is_featured) {
        data.featured_scope = form.value.featured_scope
        data.icon = form.value.icon
      } else {
        data.featured_scope = null
      }

      emit('save', data)
    }
  } finally {
    isSaving.value = false
  }
}
</script>
