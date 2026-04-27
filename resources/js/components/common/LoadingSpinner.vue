<template>
  <div
    class="animate-spin rounded-full border-2 border-border-subtle flex-shrink-0"
    :class="[sizeClasses, colorClasses]"
    :style="{ borderTopColor: `var(--spinner-color)` }"
  ></div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  size: {
    type: String,
    default: 'md',
    validator: (v) => ['sm', 'md', 'lg'].includes(v),
  },
  color: {
    type: String,
    default: 'primary',
  },
})

const sizeClasses = computed(() => {
  const sizes = {
    sm: 'w-4 h-4',
    md: 'w-8 h-8',
    lg: 'w-12 h-12',
  }
  return sizes[props.size]
})

const colorClasses = computed(() => {
  const colors = {
    primary: 'border-accent-lavender-soft',
    white: 'border-white/30',
    gray: 'border-border-subtle',
  }
  return colors[props.color] || colors.primary
})
</script>
