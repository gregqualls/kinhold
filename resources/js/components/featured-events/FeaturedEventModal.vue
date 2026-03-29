<template>
  <BaseModal :show="show" :title="event ? 'Edit Event' : 'Add Featured Event'" size="md" @close="$emit('close')">
    <form @submit.prevent="handleSubmit" class="space-y-4">
      <!-- Title -->
      <div>
        <label class="block text-sm font-medium text-prussian-500 dark:text-lavender-300 mb-1">
          Title <span class="text-red-500">*</span>
        </label>
        <input
          v-model="form.title"
          type="text"
          placeholder="Birthday, Game Day, Vacation..."
          class="w-full px-3 py-2 rounded-[10px] border border-lavender-300 dark:border-prussian-600 bg-white dark:bg-prussian-700 text-prussian-500 dark:text-lavender-200 placeholder-lavender-400 dark:placeholder-lavender-500 focus:ring-2 focus:ring-wisteria-400 focus:border-transparent"
          required
        />
      </div>

      <!-- Date -->
      <div>
        <label class="block text-sm font-medium text-prussian-500 dark:text-lavender-300 mb-1">
          Date <span class="text-red-500">*</span>
        </label>
        <input
          v-model="form.event_date"
          type="date"
          class="w-full px-3 py-2 rounded-[10px] border border-lavender-300 dark:border-prussian-600 bg-white dark:bg-prussian-700 text-prussian-500 dark:text-lavender-200 focus:ring-2 focus:ring-wisteria-400 focus:border-transparent"
          required
        />
      </div>

      <!-- Time (optional) -->
      <div>
        <label class="block text-sm font-medium text-prussian-500 dark:text-lavender-300 mb-1">
          Time <span class="text-lavender-400 dark:text-lavender-500 text-xs">(optional)</span>
        </label>
        <input
          v-model="form.event_time"
          type="time"
          class="w-full px-3 py-2 rounded-[10px] border border-lavender-300 dark:border-prussian-600 bg-white dark:bg-prussian-700 text-prussian-500 dark:text-lavender-200 focus:ring-2 focus:ring-wisteria-400 focus:border-transparent"
        />
      </div>

      <!-- Recurrence -->
      <div>
        <label class="block text-sm font-medium text-prussian-500 dark:text-lavender-300 mb-1">
          Repeats
        </label>
        <select
          v-model="form.recurrence"
          class="w-full px-3 py-2 rounded-[10px] border border-lavender-300 dark:border-prussian-600 bg-white dark:bg-prussian-700 text-prussian-500 dark:text-lavender-200 focus:ring-2 focus:ring-wisteria-400 focus:border-transparent"
        >
          <option value="none">One-time event</option>
          <option value="yearly">Every year (birthdays, anniversaries)</option>
          <option value="monthly">Every month</option>
          <option value="weekly">Every week</option>
        </select>
      </div>

      <!-- Description (optional) -->
      <div>
        <label class="block text-sm font-medium text-prussian-500 dark:text-lavender-300 mb-1">
          Description <span class="text-lavender-400 dark:text-lavender-500 text-xs">(optional)</span>
        </label>
        <textarea
          v-model="form.description"
          rows="2"
          placeholder="Add details..."
          class="w-full px-3 py-2 rounded-[10px] border border-lavender-300 dark:border-prussian-600 bg-white dark:bg-prussian-700 text-prussian-500 dark:text-lavender-200 placeholder-lavender-400 dark:placeholder-lavender-500 focus:ring-2 focus:ring-wisteria-400 focus:border-transparent resize-none"
        />
      </div>

      <!-- Icon picker -->
      <div>
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
            @click="form.color = color.value"
            class="w-8 h-8 rounded-full transition-all"
            :class="form.color === color.value ? 'ring-2 ring-offset-2 ring-offset-white dark:ring-offset-prussian-800 scale-110' : 'hover:scale-105'"
            :style="{ backgroundColor: color.value, ringColor: color.value }"
            :title="color.label"
          />
        </div>
      </div>

      <!-- Error -->
      <p v-if="error" class="text-sm text-red-600 dark:text-red-400">{{ error }}</p>
    </form>

    <template #footer>
      <button
        @click="$emit('close')"
        class="px-4 py-2 text-sm font-medium text-lavender-600 dark:text-lavender-400 hover:bg-lavender-100 dark:hover:bg-prussian-700 rounded-[10px] transition-colors"
      >
        Cancel
      </button>
      <button
        @click="handleSubmit"
        :disabled="!form.title || !form.event_date || isSaving"
        class="px-4 py-2 text-sm font-medium text-white bg-wisteria-600 hover:bg-wisteria-700 rounded-[10px] transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
      >
        {{ isSaving ? 'Saving...' : (event ? 'Update' : 'Create') }}
      </button>
    </template>
  </BaseModal>
</template>

<script setup>
import { ref, watch } from 'vue'
import BaseModal from '@/components/common/BaseModal.vue'
import IconPicker from '@/components/common/IconPicker.vue'

const props = defineProps({
  show: Boolean,
  event: {
    type: Object,
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
  event_date: '',
  event_time: '',
  icon: 'confetti',
  color: '#8B5CF6',
  recurrence: 'none',
})

const form = ref(defaultForm())
const isSaving = ref(false)
const error = ref(null)

watch(
  () => props.show,
  (newVal) => {
    if (newVal) {
      if (props.event) {
        form.value = {
          title: props.event.title || '',
          description: props.event.description || '',
          event_date: props.event.event_date || '',
          event_time: props.event.event_time || '',
          icon: props.event.icon || 'confetti',
          color: props.event.color || '#8B5CF6',
          recurrence: props.event.recurrence || 'none',
        }
      } else {
        form.value = defaultForm()
      }
      error.value = null
    }
  }
)

const handleSubmit = async () => {
  if (!form.value.title || !form.value.event_date) return

  isSaving.value = true
  error.value = null

  const data = {
    title: form.value.title,
    event_date: form.value.event_date,
    icon: form.value.icon,
    color: form.value.color,
    recurrence: form.value.recurrence,
  }

  if (form.value.description) data.description = form.value.description
  if (form.value.event_time) data.event_time = form.value.event_time

  try {
    emit('save', data)
  } finally {
    isSaving.value = false
  }
}
</script>
