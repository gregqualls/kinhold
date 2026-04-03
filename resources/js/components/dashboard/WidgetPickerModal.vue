<template>
  <BaseModal :show="show" :title="step === 'pick' ? 'Add Widget' : 'Configure Widget'" @close="handleClose">
    <!-- Step 1: Pick widget type -->
    <div v-if="step === 'pick'" class="space-y-6">
      <div v-for="(widgets, category) in groupedTypes" :key="category">
        <h3 class="text-xs font-semibold uppercase tracking-wider text-lavender-500 dark:text-lavender-400 mb-2">
          {{ categoryLabel(category) }}
        </h3>
        <div class="grid grid-cols-2 gap-2">
          <button
            v-for="wt in widgets"
            :key="wt.key"
            class="flex flex-col items-center gap-2 p-4 rounded-xl border border-lavender-200 dark:border-prussian-700 hover:border-wisteria-400 dark:hover:border-wisteria-500 hover:bg-wisteria-50 dark:hover:bg-wisteria-900/10 transition-colors text-center"
            @click="pickType(wt)"
          >
            <component
              :is="iconFor(wt.icon)"
              class="w-6 h-6 text-wisteria-500 dark:text-wisteria-400"
            />
            <div>
              <p class="text-sm font-medium text-prussian-600 dark:text-lavender-200">{{ wt.name }}</p>
              <p class="text-[11px] text-lavender-500 dark:text-lavender-400 mt-0.5">{{ wt.description }}</p>
            </div>
          </button>
        </div>
      </div>
    </div>

    <!-- Step 2: Configure widget -->
    <div v-else-if="step === 'configure'" class="space-y-4">
      <!-- Back button -->
      <button
        class="flex items-center gap-1 text-sm text-lavender-500 dark:text-lavender-400 hover:text-wisteria-500 transition-colors"
        @click="step = 'pick'"
      >
        <ArrowLeftIcon class="w-4 h-4" />
        Back to widget types
      </button>

      <!-- Title -->
      <div>
        <label class="block text-xs font-medium text-prussian-500 dark:text-lavender-300 mb-1">Title</label>
        <input
          v-model="draft.title"
          class="w-full px-3 py-2 text-sm rounded-lg border border-lavender-200 dark:border-prussian-700 bg-white dark:bg-prussian-800 text-prussian-600 dark:text-lavender-200 focus:ring-2 focus:ring-wisteria-400 focus:border-transparent"
          placeholder="Widget title"
        />
      </div>

      <!-- Data Source (endpoint) — only for data-driven widgets -->
      <div v-if="needsEndpoint">
        <label class="block text-xs font-medium text-prussian-500 dark:text-lavender-300 mb-1">Data Source</label>
        <select
          v-model="draft.endpoint"
          class="w-full px-3 py-2 text-sm rounded-lg border border-lavender-200 dark:border-prussian-700 bg-white dark:bg-prussian-800 text-prussian-600 dark:text-lavender-200 focus:ring-2 focus:ring-wisteria-400 focus:border-transparent"
        >
          <option v-for="ep in availableEndpoints" :key="ep.value" :value="ep.value">
            {{ ep.label }}
          </option>
        </select>
      </div>

      <!-- Task-specific: filter -->
      <div v-if="draft.endpoint === '/api/v1/tasks'">
        <label class="block text-xs font-medium text-prussian-500 dark:text-lavender-300 mb-1">Show</label>
        <select
          v-model="taskFilter"
          class="w-full px-3 py-2 text-sm rounded-lg border border-lavender-200 dark:border-prussian-700 bg-white dark:bg-prussian-800 text-prussian-600 dark:text-lavender-200 focus:ring-2 focus:ring-wisteria-400 focus:border-transparent"
        >
          <option value="mine">My Tasks</option>
          <option value="family">Open Family Tasks</option>
          <option value="all">All Pending Tasks</option>
        </select>
      </div>

      <!-- Size -->
      <div>
        <label class="block text-xs font-medium text-prussian-500 dark:text-lavender-300 mb-1">Size</label>
        <div class="flex gap-2">
          <button
            v-for="s in sizeOptions"
            :key="s.value"
            class="flex-1 py-2 text-sm font-medium rounded-lg border transition-colors"
            :class="draft.size === s.value
              ? 'border-wisteria-400 bg-wisteria-50 dark:bg-wisteria-900/20 text-wisteria-700 dark:text-wisteria-300'
              : 'border-lavender-200 dark:border-prussian-700 text-lavender-500 dark:text-lavender-400 hover:border-wisteria-300'"
            @click="draft.size = s.value"
          >
            {{ s.label }}
          </button>
        </div>
      </div>

      <!-- Limit -->
      <div v-if="showLimit">
        <label class="block text-xs font-medium text-prussian-500 dark:text-lavender-300 mb-1">Max Items</label>
        <input
          v-model.number="draftLimit"
          type="number"
          min="1"
          max="20"
          class="w-20 px-3 py-2 text-sm rounded-lg border border-lavender-200 dark:border-prussian-700 bg-white dark:bg-prussian-800 text-prussian-600 dark:text-lavender-200 focus:ring-2 focus:ring-wisteria-400 focus:border-transparent"
        />
      </div>

      <!-- Add button -->
      <div class="flex justify-end gap-2 pt-2">
        <button
          class="px-4 py-2 text-sm font-medium rounded-lg text-lavender-600 dark:text-lavender-400 hover:bg-lavender-100 dark:hover:bg-prussian-700 transition-colors"
          @click="step = 'pick'"
        >
          Cancel
        </button>
        <button
          class="px-4 py-2 text-sm font-medium rounded-lg bg-wisteria-600 text-white hover:bg-wisteria-700 transition-colors"
          @click="addWidget"
        >
          Add to Dashboard
        </button>
      </div>
    </div>
  </BaseModal>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import BaseModal from '@/components/common/BaseModal.vue'
import { widgetTypesByCategory } from './widgetRegistry'
import {
  HandRaisedIcon,
  ClockIcon,
  ChartBarIcon,
  ListBulletIcon,
  TrophyIcon,
  BellIcon,
  Squares2X2Icon,
  CalendarDaysIcon,
  ChartPieIcon,
  ShieldCheckIcon,
  GiftIcon,
  ArrowLeftIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  show: { type: Boolean, default: false },
})

const emit = defineEmits(['close', 'add'])

const step = ref('pick')
const selectedType = ref(null)
const draft = ref({})
const taskFilter = ref('mine')
const draftLimit = ref(5)

const groupedTypes = widgetTypesByCategory()

const categoryLabels = {
  special: 'Special',
  data: 'Data & Metrics',
  lists: 'Lists & Feeds',
}

function categoryLabel(key) {
  return categoryLabels[key] || key
}

const iconMap = {
  HandRaisedIcon,
  ClockIcon,
  ChartBarIcon,
  ListBulletIcon,
  TrophyIcon,
  BellIcon,
  Squares2X2Icon,
  CalendarDaysIcon,
  ChartPieIcon,
  ShieldCheckIcon,
  GiftIcon,
}

function iconFor(name) {
  return iconMap[name] || ChartBarIcon
}

// Widgets that don't need configuration — just add with defaults
const quickAddTypes = ['welcome', 'countdown', 'quick-actions']

function pickType(wt) {
  if (quickAddTypes.includes(wt.key)) {
    // Add immediately with defaults
    emit('add', {
      id: crypto.randomUUID(),
      type: wt.key,
      title: wt.name,
      endpoint: wt.defaultConfig.endpoint,
      params: { ...wt.defaultConfig.params },
      size: wt.defaultConfig.size,
      settings: { ...wt.defaultConfig.settings },
    })
    emit('close')
    return
  }

  selectedType.value = wt
  draft.value = {
    type: wt.key,
    title: wt.name,
    endpoint: wt.defaultConfig.endpoint,
    params: { ...wt.defaultConfig.params },
    size: wt.defaultConfig.size,
    settings: { ...wt.defaultConfig.settings },
  }
  draftLimit.value = wt.defaultConfig.settings?.limit || 5
  taskFilter.value = wt.defaultConfig.params?.assigned_to === 'me' ? 'mine'
    : wt.defaultConfig.params?.is_family_task ? 'family' : 'all'
  step.value = 'configure'
}

const needsEndpoint = computed(() => {
  return ['list', 'stat', 'feed', 'progress'].includes(draft.value.type)
})

const showLimit = computed(() => {
  return ['list', 'feed'].includes(draft.value.type)
})

const availableEndpoints = [
  { value: '/api/v1/tasks', label: 'Tasks' },
  { value: '/api/v1/calendar/events', label: 'Calendar Events' },
  { value: '/api/v1/points/bank', label: 'Points Balance' },
  { value: '/api/v1/points/leaderboard', label: 'Leaderboard' },
  { value: '/api/v1/points/feed', label: 'Activity Feed' },
  { value: '/api/v1/rewards', label: 'Rewards' },
  { value: '/api/v1/badges', label: 'Badges' },
  { value: '/api/v1/badges/earned', label: 'Earned Badges' },
  { value: '/api/v1/featured-events', label: 'Featured Events' },
  { value: '/api/v1/vault/categories', label: 'Vault Categories' },
  { value: '/api/v1/vault/entries', label: 'Vault Entries' },
]

const sizeOptions = [
  { value: 'sm', label: 'Small (1 col)' },
  { value: 'md', label: 'Medium (2 col)' },
  { value: 'lg', label: 'Large (full)' },
]

function addWidget() {
  // Build params based on task filter
  let params = { ...draft.value.params }
  let settings = { ...draft.value.settings }

  if (draft.value.endpoint === '/api/v1/tasks') {
    params = { status: 'pending' }
    settings.showDueDate = true
    settings.showPoints = true
    settings.completable = true
    settings.viewAllPath = '/tasks'

    if (taskFilter.value === 'mine') {
      params.assigned_to = 'me'
      settings.emptyMessage = 'No tasks assigned to you.'
    } else if (taskFilter.value === 'family') {
      params.is_family_task = true
      settings.emptyMessage = 'No open family tasks.'
    } else {
      settings.emptyMessage = 'No pending tasks.'
    }
  }

  if (draft.value.endpoint === '/api/v1/calendar/events') {
    params = { range: 'today' }
    settings.viewAllPath = '/calendar'
  }

  settings.limit = draftLimit.value

  emit('add', {
    id: crypto.randomUUID(),
    type: draft.value.type,
    title: draft.value.title,
    endpoint: draft.value.endpoint,
    params,
    size: draft.value.size,
    settings,
  })
  emit('close')
}

function handleClose() {
  step.value = 'pick'
  emit('close')
}

// Reset step when modal opens
watch(() => props.show, (showing) => {
  if (showing) step.value = 'pick'
})
</script>
