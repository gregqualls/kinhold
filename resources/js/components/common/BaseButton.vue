<template>
  <button
    :class="[
      'btn',
      variantClasses,
      sizeClasses,
      {
        'opacity-50 cursor-not-allowed': disabled || loading,
      },
    ]"
    :disabled="disabled || loading"
    @click="$emit('click')"
  >
    <template v-if="loading">
      <LoadingSpinner size="sm" class="mr-2" />
    </template>
    <slot></slot>
  </button>
</template>

<script setup>
import { computed } from 'vue'
import LoadingSpinner from './LoadingSpinner.vue'

const props = defineProps({
  variant: {
    type: String,
    default: 'primary',
    validator: (v) => ['primary', 'secondary', 'danger', 'ghost'].includes(v),
  },
  size: {
    type: String,
    default: 'md',
    validator: (v) => ['sm', 'md', 'lg'].includes(v),
  },
  disabled: Boolean,
  loading: Boolean,
})

defineEmits(['click'])

const variantClasses = computed(() => {
  const variants = {
    primary: 'btn-primary',
    secondary: 'btn-secondary',
    danger: 'btn-danger',
    ghost: 'btn-ghost',
  }
  return variants[props.variant]
})

const sizeClasses = computed(() => {
  const sizes = {
    sm: 'btn-sm',
    md: 'btn-md',
    lg: 'btn-lg',
  }
  return sizes[props.size]
})
</script>
