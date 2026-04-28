<script setup>
defineProps({
  modelValue: { type: [Number, String], default: 'full' },
})
defineEmits(['update:modelValue'])

const WIDTHS = [
  { label: 'Mobile', value: 375 },
  { label: 'Tablet', value: 768 },
  { label: 'Desktop', value: 1280 },
  { label: 'Full', value: 'full' },
]
</script>

<template>
  <div class="inline-flex items-center gap-1 p-1 rounded-full bg-kin-gray-100 dark:bg-kin-gray-800">
    <button
      v-for="w in WIDTHS"
      :key="w.value"
      type="button"
      class="px-3 py-1.5 text-xs font-medium rounded-full transition-colors"
      :class="modelValue === w.value
        ? 'bg-white text-kin-black shadow-sm dark:bg-kin-off-white dark:text-kin-black'
        : 'text-kin-gray-500 hover:text-kin-black dark:text-kin-gray-300 dark:hover:text-kin-off-white'"
      @click="$emit('update:modelValue', w.value)"
    >
      {{ w.label }}<span v-if="w.value !== 'full'" class="ml-1 font-mono opacity-60">{{ w.value }}</span>
    </button>
  </div>
</template>
