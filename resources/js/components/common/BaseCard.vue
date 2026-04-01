<template>
  <div
    class="bg-white dark:bg-prussian-800 rounded-card border border-lavender-200 dark:border-prussian-700 transition-all duration-200"
    :class="[
      paddingClasses,
      shadowClasses,
      {
        'cursor-pointer hover:shadow-card-lg': clickable,
        'active:shadow-card': clickable,
      },
    ]"
    @click="$emit('click')"
  >
    <slot></slot>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  padding: {
    type: String,
    default: 'md',
    validator: (v) => ['sm', 'md', 'lg'].includes(v),
  },
  shadow: {
    type: String,
    default: 'md',
    validator: (v) => ['sm', 'md', 'lg'].includes(v),
  },
  clickable: {
    type: Boolean,
    default: false,
  },
})

defineEmits(['click'])

const paddingClasses = computed(() => {
  const paddings = {
    sm: 'p-3',
    md: 'p-4',
    lg: 'p-6',
  }
  return paddings[props.padding]
})

const shadowClasses = computed(() => {
  const shadows = {
    sm: 'shadow-card',
    md: 'shadow-card',
    lg: 'shadow-card-lg',
  }
  return shadows[props.shadow]
})
</script>
