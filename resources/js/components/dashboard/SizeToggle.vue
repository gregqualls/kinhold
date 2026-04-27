<template>
  <div v-if="availableSizes.length > 1" class="flex items-center gap-0.5 bg-surface-sunken rounded-md p-0.5">
    <button
      v-for="opt in availableSizes"
      :key="opt.value"
      class="px-1.5 py-0.5 text-[10px] font-medium rounded transition-colors"
      :class="size === opt.value
        ? 'bg-surface-raised text-accent-lavender-bold shadow-sm'
        : 'text-ink-tertiary hover:text-ink-secondary'
      "
      :title="opt.label"
      :aria-label="`Set widget size to ${opt.label}`"
      @click.stop="$emit('resize', opt.value)"
    >
      {{ opt.label }}
    </button>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  size: { type: String, required: true },
  supportedSizes: { type: Array, default: () => ['sm', 'md', 'lg'] },
})

defineEmits(['resize'])

const allSizes = [
  { value: 'sm', label: 'S' },
  { value: 'md', label: 'M' },
  { value: 'lg', label: 'L' },
]

const availableSizes = computed(() =>
  allSizes.filter((s) => props.supportedSizes.includes(s.value)),
)
</script>
