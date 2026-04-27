<template>
  <div class="flex flex-wrap gap-1.5">
    <button
      v-for="key in presetIconKeys"
      :key="key"
      type="button"
      class="w-9 h-9 rounded-lg flex items-center justify-center transition-all"
      :class="modelValue === key
        ? 'bg-accent-lavender-soft/40 ring-2 ring-accent-lavender-bold scale-110 text-accent-lavender-bold'
        : 'bg-surface-sunken hover:bg-surface-overlay text-ink-secondary'"
      :title="key"
      @click="$emit('update:modelValue', key)"
    >
      <component :is="iconComponent(key)" :size="18" weight="regular" />
    </button>
  </div>
</template>

<script setup>
import { presetIconMap, presetIconKeys } from './presetIcons'

defineProps({
  modelValue: {
    type: String,
    default: '',
  },
})

defineEmits(['update:modelValue'])

const iconComponent = (key) => presetIconMap[key]
</script>
