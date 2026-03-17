<template>
  <div
    class="rounded-full flex items-center justify-center font-semibold text-white flex-shrink-0"
    :class="[sizeClasses, colorClasses]"
    :title="user?.name"
  >
    {{ initials }}
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useFamilyColors } from '@/composables/useFamilyColors'

const props = defineProps({
  user: {
    type: Object,
    default: null,
  },
  size: {
    type: String,
    default: 'md',
    validator: (v) => ['xs', 'sm', 'md', 'lg'].includes(v),
  },
})

const { getColorForUser } = useFamilyColors()

const sizeClasses = computed(() => {
  const sizes = {
    xs: 'w-5 h-5 text-[8px]',
    sm: 'w-8 h-8 text-xs',
    md: 'w-10 h-10 text-sm',
    lg: 'w-12 h-12 text-base',
  }
  return sizes[props.size]
})

const initials = computed(() => {
  if (!props.user?.name) return '?'
  return props.user.name
    .split(' ')
    .map((n) => n[0])
    .join('')
    .toUpperCase()
    .slice(0, 2)
})

const colorClasses = computed(() => {
  const color = getColorForUser(props.user?.id, props.user?.name)
  return color.bg
})
</script>
