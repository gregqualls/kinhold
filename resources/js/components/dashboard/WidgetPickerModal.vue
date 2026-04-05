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

    <!-- Step 2: Configure size (only for multi-size widgets) -->
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
        <p class="text-sm text-lavender-500 dark:text-lavender-400">{{ selectedType.description }}</p>
      </div>

      <div>
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
import { ref, computed, watch } from 'vue'
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
  ArrowLeftIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  show: { type: Boolean, default: false },
})

const emit = defineEmits(['close', 'add'])

const step = ref('pick')
const selectedType = ref(null)
const selectedSize = ref('sm')

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

function pickType(wt) {
  // Single-size widgets: add immediately
  if (wt.supportedSizes.length === 1) {
    emit('add', {
      id: crypto.randomUUID(),
      type: wt.key,
      size: wt.defaultSize,
    })
    emit('close')
    return
  }

  // Multi-size: show configure step
  selectedType.value = wt
  selectedSize.value = wt.defaultSize
  step.value = 'configure'
}

function addWidget() {
  emit('add', {
    id: crypto.randomUUID(),
    type: selectedType.value.key,
    size: selectedSize.value,
  })
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
