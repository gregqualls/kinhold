<template>
  <component
    :is="resolvedIcon"
    :size="size"
    :weight="weight"
  />
</template>

<script setup>
import { computed } from 'vue'
import { presetIconMap } from './presetIcons'
import { PhGift } from '@phosphor-icons/vue'

const props = defineProps({
  icon: {
    type: String,
    default: 'gift',
  },
  size: {
    type: Number,
    default: 20,
  },
  weight: {
    type: String,
    default: 'regular',
  },
})

const resolvedIcon = computed(() => presetIconMap[props.icon] || PhGift)
</script>
