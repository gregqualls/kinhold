<template>
  <div class="flex flex-wrap gap-1.5">
    <button
      v-for="key in presetIconKeys"
      :key="key"
      type="button"
      class="w-9 h-9 rounded-lg flex items-center justify-center transition-all"
      :class="modelValue === key
        ? 'bg-wisteria-100 dark:bg-wisteria-900/40 ring-2 ring-wisteria-400 scale-110 text-wisteria-600 dark:text-wisteria-400'
        : 'bg-lavender-50 dark:bg-prussian-700 hover:bg-lavender-100 dark:hover:bg-prussian-600 text-prussian-400 dark:text-lavender-400'"
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
