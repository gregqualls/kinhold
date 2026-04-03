<template>
  <div class="flex items-center gap-0.5 bg-lavender-100 dark:bg-prussian-700 rounded-md p-0.5">
    <button
      v-for="opt in sizes"
      :key="opt.value"
      class="px-1.5 py-0.5 text-[10px] font-medium rounded transition-colors"
      :class="size === opt.value
        ? 'bg-white dark:bg-prussian-600 text-wisteria-600 dark:text-wisteria-400 shadow-sm'
        : 'text-lavender-500 dark:text-lavender-400 hover:text-prussian-600 dark:hover:text-lavender-300'
      "
      :title="opt.label"
      @click.stop="$emit('resize', opt.value)"
    >
      {{ opt.label }}
    </button>
  </div>
</template>

<script setup>
defineProps({
  size: { type: String, required: true },
})

defineEmits(['resize'])

const sizes = [
  { value: 'sm', label: 'S' },
  { value: 'md', label: 'M' },
  { value: 'lg', label: 'L' },
]
</script>
