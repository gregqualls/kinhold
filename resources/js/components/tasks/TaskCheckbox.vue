<template>
  <button
    class="checkbox-custom"
    :class="{
      'checked': checked,
      'border-red-400 bg-red-500': checked && priority === 'high',
      'border-orange-400 bg-orange-500': checked && priority === 'medium',
      'border-lavender-400 bg-lavender-400': checked && priority === 'low',
      'border-red-300': !checked && priority === 'high',
      'border-orange-300': !checked && priority === 'medium',
      'border-lavender-300 dark:border-lavender-500': !checked && (priority === 'low' || !priority),
    }"
    role="checkbox"
    :aria-checked="checked"
    :aria-label="checked ? 'Mark as incomplete' : 'Mark as complete'"
    @click.stop="$emit('toggle')"
  >
    <span class="check-icon">
      <svg class="w-3 h-3" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <polyline points="2 6 5 9 10 3" />
      </svg>
    </span>
  </button>
</template>

<script setup>
defineProps({
  checked: Boolean,
  priority: { type: String, default: 'low' },
})

defineEmits(['toggle'])
</script>
