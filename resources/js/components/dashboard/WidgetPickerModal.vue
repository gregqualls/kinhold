<template>
  <BaseModal :show="show" :title="step === 'pick' ? 'Add Widget' : 'Configure'" @close="handleClose">
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
            :aria-label="`Add ${wt.name} widget`"
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

    <!-- Step 2: Configure -->
    <div v-else-if="step === 'configure'" class="space-y-5">
      <button
        class="flex items-center gap-1 text-sm text-lavender-500 dark:text-lavender-400 hover:text-wisteria-500 transition-colors"
        @click="step = 'pick'"
      >
        <ArrowLeftIcon class="w-4 h-4" />
        Back
      </button>

      <div class="text-center">
        <component :is="iconFor(selectedType.icon)" class="w-8 h-8 text-wisteria-500 dark:text-wisteria-400 mx-auto mb-2" />
        <h4 class="text-lg font-semibold text-prussian-500 dark:text-lavender-200">{{ selectedType.name }}</h4>
      </div>

      <!-- Title -->
      <div>
        <label class="block text-xs font-medium text-prussian-500 dark:text-lavender-300 mb-1">Title</label>
        <input
          v-model="widgetTitle"
          class="w-full px-3 py-2 text-sm rounded-lg border border-lavender-200 dark:border-prussian-700 bg-white dark:bg-prussian-800 text-prussian-600 dark:text-lavender-200 focus:ring-2 focus:ring-wisteria-400 focus:border-transparent"
          placeholder="Widget title"
        />
      </div>

      <!-- Filtered Tasks config -->
      <template v-if="selectedType.key === 'filtered-tasks'">
        <!-- Tag filter -->
        <div>
          <label class="block text-xs font-medium text-prussian-500 dark:text-lavender-300 mb-1.5">Filter by Tags</label>
          <div v-if="tagsLoading" class="flex gap-2">
            <div v-for="n in 3" :key="n" class="h-7 w-16 bg-lavender-100 dark:bg-prussian-700 rounded-full animate-pulse"></div>
          </div>
          <div v-else class="flex flex-wrap gap-1.5">
            <button
              v-for="tag in availableTags"
              :key="tag.id"
              class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-full border transition-colors"
              :class="selectedTagIds.has(tag.id)
                ? 'border-transparent text-white'
                : 'border-lavender-200 dark:border-prussian-700 text-lavender-600 dark:text-lavender-400 hover:border-wisteria-400'"
              :style="selectedTagIds.has(tag.id) ? { backgroundColor: tag.color } : {}"
              @click="toggleTag(tag.id)"
            >
              {{ tag.name }}
            </button>
            <p v-if="availableTags.length === 0" class="text-xs text-lavender-400 dark:text-lavender-500">No tags created yet</p>
          </div>
        </div>

        <!-- Due date filter -->
        <div>
          <label class="block text-xs font-medium text-prussian-500 dark:text-lavender-300 mb-1">Due Within</label>
          <select
            v-model="dueWithin"
            class="w-full px-3 py-2 text-sm rounded-lg border border-lavender-200 dark:border-prussian-700 bg-white dark:bg-prussian-800 text-prussian-600 dark:text-lavender-200 focus:ring-2 focus:ring-wisteria-400 focus:border-transparent"
          >
            <option value="">Any time</option>
            <option value="today">Today</option>
            <option value="week">This week</option>
            <option value="month">This month</option>
          </select>
        </div>
      </template>

      <!-- Size -->
      <div v-if="sizeOptions.length > 1">
        <label class="block text-xs font-medium text-prussian-500 dark:text-lavender-300 mb-2">Size</label>
        <div class="flex gap-2">
          <button
            v-for="s in sizeOptions"
            :key="s.value"
            class="flex-1 py-3 text-sm font-medium rounded-lg border transition-colors"
            :class="selectedSize === s.value
              ? 'border-wisteria-400 bg-wisteria-50 dark:bg-wisteria-900/20 text-wisteria-700 dark:text-wisteria-300'
              : 'border-lavender-200 dark:border-prussian-700 text-lavender-500 dark:text-lavender-400 hover:border-wisteria-300'"
            @click="selectedSize = s.value"
          >
            {{ s.label }}
          </button>
        </div>
      </div>

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
import { ref, reactive, computed, watch, onMounted } from 'vue'
import BaseModal from '@/components/common/BaseModal.vue'
import { widgetTypesByCategory } from './widgetRegistry'
import {
  HandRaisedIcon,
  ClockIcon,
  CheckCircleIcon,
  UserGroupIcon,
  CalendarDaysIcon,
  TrophyIcon,
  BellIcon,
  GiftIcon,
  ShieldCheckIcon,
  Squares2X2Icon,
  FunnelIcon,
  ArrowLeftIcon,
} from '@heroicons/vue/24/outline'
import api from '@/services/api'

const props = defineProps({
  show: { type: Boolean, default: false },
})

const emit = defineEmits(['close', 'add'])

const step = ref('pick')
const selectedType = ref(null)
const selectedSize = ref('sm')
const widgetTitle = ref('')

// Filtered tasks state
const selectedTagIds = reactive(new Set())
const dueWithin = ref('')
const availableTags = ref([])
const tagsLoading = ref(false)

const groupedTypes = widgetTypesByCategory()

const categoryLabels = {
  general: 'General',
  tasks: 'Tasks',
  calendar: 'Calendar',
  points: 'Points & Gamification',
  rewards: 'Rewards',
  badges: 'Badges',
}

function categoryLabel(key) {
  return categoryLabels[key] || key
}

const iconMap = {
  HandRaisedIcon,
  ClockIcon,
  CheckCircleIcon,
  UserGroupIcon,
  CalendarDaysIcon,
  TrophyIcon,
  BellIcon,
  GiftIcon,
  ShieldCheckIcon,
  Squares2X2Icon,
  FunnelIcon,
}

function iconFor(name) {
  return iconMap[name] || Squares2X2Icon
}

const sizeLabels = { sm: 'Small (1 col)', md: 'Medium (2 col)', lg: 'Large (full)' }

const sizeOptions = computed(() => {
  if (!selectedType.value) return []
  return selectedType.value.supportedSizes.map((s) => ({
    value: s,
    label: sizeLabels[s] || s,
  }))
})

function toggleTag(tagId) {
  if (selectedTagIds.has(tagId)) {
    selectedTagIds.delete(tagId)
  } else {
    selectedTagIds.add(tagId)
  }
}

async function fetchTags() {
  tagsLoading.value = true
  try {
    const res = await api.get('/tags')
    availableTags.value = res.data.tags || res.data.data || res.data || []
  } catch {
    availableTags.value = []
  } finally {
    tagsLoading.value = false
  }
}

function pickType(wt) {
  // Single-size, non-configurable: add immediately
  if (wt.supportedSizes.length === 1 && !wt.configurable) {
    emit('add', {
      id: crypto.randomUUID(),
      type: wt.key,
      size: wt.defaultSize,
    })
    emit('close')
    return
  }

  selectedType.value = wt
  selectedSize.value = wt.defaultSize
  widgetTitle.value = wt.name
  selectedTagIds.clear()
  dueWithin.value = ''
  step.value = 'configure'

  // Fetch tags if this is filtered-tasks
  if (wt.key === 'filtered-tasks') {
    fetchTags()
  }
}

function addWidget() {
  const widget = {
    id: crypto.randomUUID(),
    type: selectedType.value.key,
    size: selectedSize.value,
  }

  if (widgetTitle.value && widgetTitle.value !== selectedType.value.name) {
    widget.title = widgetTitle.value
  }

  // Add filters for filtered-tasks
  if (selectedType.value.key === 'filtered-tasks') {
    const filters = {}
    if (selectedTagIds.size > 0) {
      filters.tags = [...selectedTagIds]
    }
    if (dueWithin.value) {
      filters.due_within = dueWithin.value
    }
    if (Object.keys(filters).length > 0) {
      widget.filters = filters
    }
  }

  emit('add', widget)
  emit('close')
}

function handleClose() {
  step.value = 'pick'
  emit('close')
}

watch(() => props.show, (showing) => {
  if (showing) step.value = 'pick'
})
</script>
