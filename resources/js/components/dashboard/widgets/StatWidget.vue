<template>
  <div class="flex items-center gap-4">
    <div
      class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
      :class="iconBg"
    >
      <component :is="iconComponent" class="w-6 h-6" :class="iconColor" />
    </div>
    <div class="min-w-0">
      <p class="text-sm text-lavender-500 dark:text-lavender-400 truncate">{{ config.title }}</p>
      <div v-if="loading" class="h-8 w-16 bg-lavender-200 dark:bg-prussian-700 rounded animate-pulse mt-1"></div>
      <p v-else class="text-2xl font-bold font-mono text-prussian-500 dark:text-lavender-200">
        {{ displayValue }}<span v-if="suffix" class="text-sm font-normal ml-1 text-lavender-500 dark:text-lavender-400">{{ suffix }}</span>
      </p>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useWidgetData } from '@/composables/useWidgetData'
import {
  TrophyIcon,
  CheckCircleIcon,
  CalendarIcon,
  StarIcon,
  CurrencyDollarIcon,
} from '@heroicons/vue/24/solid'

const props = defineProps({
  config: { type: Object, required: true },
})

const settings = computed(() => props.config.settings || {})
const suffix = computed(() => settings.value.suffix || '')

const { data, loading } = useWidgetData(
  props.config.endpoint,
  props.config.params,
  { transformKey: settings.value.valueKey },
)

const displayValue = computed(() => {
  if (data.value === null || data.value === undefined) return '—'
  if (typeof data.value === 'number') return data.value.toLocaleString()
  return data.value
})

const iconMap = {
  trophy: TrophyIcon,
  check: CheckCircleIcon,
  calendar: CalendarIcon,
  star: StarIcon,
  dollar: CurrencyDollarIcon,
}

const iconComponent = computed(() => iconMap[settings.value.icon] || TrophyIcon)

const iconBg = 'bg-wisteria-100 dark:bg-wisteria-900/30'
const iconColor = 'text-wisteria-600 dark:text-wisteria-400'
</script>
