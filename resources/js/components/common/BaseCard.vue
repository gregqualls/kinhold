<template>
  <KinFlatCard
    :padding="padding"
    :interactive="clickable"
    @click="$emit('click')"
  >
    <slot></slot>
  </KinFlatCard>
</template>

<script setup>
import KinFlatCard from '@/components/design-system/KinFlatCard.vue'

defineProps({
  padding: {
    type: String,
    default: 'md',
    validator: (v) => ['sm', 'md', 'lg'].includes(v),
  },
  // Kept for API compatibility — KinFlatCard manages its own shadow tokens.
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
</script>
