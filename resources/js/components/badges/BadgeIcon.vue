<template>
  <div
    class="relative inline-flex items-center justify-center"
    :class="sizeClass"
  >
    <!-- Hexagon background -->
    <svg :width="svgSize" :height="svgSize" viewBox="0 0 100 100" class="absolute inset-0">
      <polygon
        points="50,3 95,25 95,75 50,97 5,75 5,25"
        :fill="locked ? '#9ca3af' : color + '20'"
        :stroke="locked ? '#d1d5db' : color"
        stroke-width="2"
        :class="{ 'opacity-40': locked }"
      />
    </svg>

    <!-- Icon -->
    <svg
      :width="iconSize"
      :height="iconSize"
      viewBox="0 0 24 24"
      fill="none"
      :stroke="locked ? '#9ca3af' : color"
      stroke-width="1.5"
      stroke-linecap="round"
      stroke-linejoin="round"
      class="relative z-10"
      :class="{ 'opacity-40': locked }"
    >
      <path :d="iconPath" />
    </svg>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { badgeIconPaths } from './badgeIcons'

const props = defineProps({
  icon: {
    type: String,
    default: 'trophy',
  },
  color: {
    type: String,
    default: '#7d57a8',
  },
  size: {
    type: String,
    default: 'md',
    validator: (v) => ['sm', 'md', 'lg'].includes(v),
  },
  locked: {
    type: Boolean,
    default: false,
  },
})

const sizeClass = computed(() => ({
  sm: 'w-10 h-10',
  md: 'w-14 h-14',
  lg: 'w-20 h-20',
}[props.size]))

const svgSize = computed(() => ({
  sm: 40,
  md: 56,
  lg: 80,
}[props.size]))

const iconSize = computed(() => ({
  sm: 16,
  md: 22,
  lg: 32,
}[props.size]))

const iconPath = computed(() => {
  return badgeIconPaths[props.icon] || badgeIconPaths.trophy
})
</script>
