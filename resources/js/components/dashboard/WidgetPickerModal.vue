<template>
  <KinModalSheet
    :model-value="show"
    :title="step === 'pick' ? 'Add Widget' : 'Configure'"
    size="md"
    @update:model-value="(v) => !v && handleClose()"
  >
    <!-- Step 1: Pick widget type -->
    <div v-if="step === 'pick'" class="space-y-6">
      <div v-for="(widgets, category) in groupedTypes" :key="category">
        <h3 class="text-xs font-semibold uppercase tracking-wider text-ink-tertiary mb-2">
          {{ categoryLabel(category) }}
        </h3>
        <div class="grid grid-cols-2 gap-2">
          <button
            v-for="wt in widgets"
            :key="wt.key"
            class="flex flex-col items-center gap-2 p-4 rounded-xl border border-border-subtle hover:border-accent-lavender-bold hover:bg-accent-lavender-soft/30 transition-colors text-center"
            :aria-label="`Add ${wt.name} widget`"
            @click="pickType(wt)"
          >
            <component
              :is="iconFor(wt.icon)"
              class="w-6 h-6 text-accent-lavender-bold"
            />
            <div>
              <p class="text-sm font-medium text-ink-primary">{{ wt.name }}</p>
              <p class="text-[11px] text-ink-tertiary mt-0.5">{{ wt.description }}</p>
            </div>
          </button>
        </div>
      </div>
    </div>

    <!-- Step 2: Configure -->
    <div v-else-if="step === 'configure'" class="space-y-5">
      <KinButton variant="ghost" size="sm" @click="step = 'pick'">
        <template #leading>
          <ArrowLeftIcon class="w-4 h-4" />
        </template>
        Back
      </KinButton>

      <div class="text-center">
        <component :is="iconFor(selectedType.icon)" class="w-8 h-8 text-accent-lavender-bold mx-auto mb-2" />
        <h4 class="text-lg font-semibold text-ink-primary">{{ selectedType.name }}</h4>
      </div>

      <!-- Title -->
      <KinInput
        v-model="widgetTitle"
        label="Title"
        size="sm"
        placeholder="Widget title"
      />

      <!-- Filtered Tasks config -->
      <template v-if="selectedType.key === 'filtered-tasks'">
        <!-- Tag filter -->
        <KinFormGroup label="Filter by Tags">
          <div v-if="tagsLoading" class="flex gap-2">
            <KinSkeleton v-for="n in 3" :key="n" shape="pill" width="64px" />
          </div>
          <p v-else-if="tagsError" class="text-xs text-status-failed">Failed to load tags. Try again later.</p>
          <div v-else class="flex flex-wrap gap-1.5">
            <button
              v-for="tag in availableTags"
              :key="tag.id"
              class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-full border transition-colors"
              :class="selectedTagIds.has(tag.id)
                ? 'border-transparent text-white'
                : 'border-border-subtle text-ink-secondary hover:border-accent-lavender-bold'"
              :style="selectedTagIds.has(tag.id) ? { backgroundColor: tag.color } : {}"
              @click="toggleTag(tag.id)"
            >
              {{ tag.name }}
            </button>
            <p v-if="availableTags.length === 0" class="text-xs text-ink-tertiary">No tags created yet</p>
          </div>
        </KinFormGroup>

        <!-- Due date filter -->
        <KinFormGroup label="Due Within">
          <select
            v-model="dueWithin"
            class="w-full h-8 px-3 text-[13px] rounded-[10px] border-0 bg-surface-sunken text-ink-primary focus:outline-none focus:ring-2 focus:ring-accent-lavender-bold"
          >
            <option value="">Any time</option>
            <option value="today">Today</option>
            <option value="week">This week</option>
            <option value="month">This month</option>
          </select>
        </KinFormGroup>
      </template>

      <!-- Size -->
      <KinFormGroup v-if="sizeOptions.length > 1" label="Size">
        <div class="flex gap-2">
          <button
            v-for="s in sizeOptions"
            :key="s.value"
            class="flex-1 py-3 text-sm font-medium rounded-lg border transition-colors"
            :class="selectedSize === s.value
              ? 'border-accent-lavender-bold bg-accent-lavender-soft/30 text-accent-lavender-bold'
              : 'border-border-subtle text-ink-secondary hover:border-accent-lavender-bold'"
            @click="selectedSize = s.value"
          >
            {{ s.label }}
          </button>
        </div>
      </KinFormGroup>
    </div>

    <template v-if="step === 'configure'" #actions>
      <div class="flex justify-end gap-2">
        <KinButton variant="ghost" size="sm" @click="step = 'pick'">Cancel</KinButton>
        <KinButton variant="primary" size="sm" @click="addWidget">Add to Dashboard</KinButton>
      </div>
    </template>
  </KinModalSheet>
</template>

<script setup>
import { ref, reactive, computed, watch } from 'vue'
import KinModalSheet from '@/components/design-system/KinModalSheet.vue'
import KinButton from '@/components/design-system/KinButton.vue'
import KinInput from '@/components/design-system/KinInput.vue'
import KinFormGroup from '@/components/design-system/KinFormGroup.vue'
import KinSkeleton from '@/components/design-system/KinSkeleton.vue'
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
  badges: 'Achievement Badges',
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

const tagsError = ref(false)

async function fetchTags() {
  tagsLoading.value = true
  tagsError.value = false
  try {
    const res = await api.get('/tags', { params: { scope: 'task' } })
    availableTags.value = res.data.tags || res.data.data || res.data || []
  } catch {
    availableTags.value = []
    tagsError.value = true
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
