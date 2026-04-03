<template>
  <div class="flex flex-col items-center gap-3 py-2">
    <!-- Progress ring -->
    <div class="relative w-20 h-20">
      <svg class="w-20 h-20 -rotate-90" viewBox="0 0 80 80">
        <!-- Background ring -->
        <circle
          cx="40" cy="40" r="34"
          fill="none"
          stroke-width="8"
          class="stroke-lavender-200 dark:stroke-prussian-700"
        />
        <!-- Progress ring -->
        <circle
          cx="40" cy="40" r="34"
          fill="none"
          stroke-width="8"
          stroke-linecap="round"
          class="stroke-wisteria-500 dark:stroke-wisteria-400 transition-all duration-700 ease-out"
          :stroke-dasharray="circumference"
          :stroke-dashoffset="dashOffset"
        />
      </svg>
      <div class="absolute inset-0 flex items-center justify-center">
        <span class="text-lg font-bold font-mono text-prussian-500 dark:text-lavender-200">
          {{ loading ? '—' : percentage }}%
        </span>
      </div>
    </div>

    <p class="text-sm text-lavender-500 dark:text-lavender-400 text-center">
      {{ config.settings?.label || config.title }}
    </p>

    <p v-if="!loading && current !== null" class="text-xs text-lavender-400 dark:text-lavender-500">
      {{ current }} / {{ max }}
    </p>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useWidgetData } from '@/composables/useWidgetData'

const props = defineProps({
  config: { type: Object, required: true },
})

const settings = computed(() => props.config.settings || {})

const { data, loading } = useWidgetData(props.config.endpoint, props.config.params)

const current = computed(() => {
  if (!data.value) return 0
  const key = settings.value.valueKey || 'completed'
  return data.value[key] ?? 0
})

const max = computed(() => {
  if (!data.value) return 1
  const key = settings.value.maxKey || 'total'
  return data.value[key] ?? 1
})

const percentage = computed(() => {
  if (max.value === 0) return 0
  return Math.round((current.value / max.value) * 100)
})

const circumference = 2 * Math.PI * 34 // ~213.6
const dashOffset = computed(() => {
  return circumference - (percentage.value / 100) * circumference
})
</script>
