<template>
  <button
    class="checkbox-custom"
    role="checkbox"
    :aria-checked="visualChecked"
    :aria-label="visualChecked ? 'Mark as incomplete' : 'Mark as complete'"
    @click.stop="$emit('toggle')"
  >
    <span
      class="checkbox-ring"
      :class="{
        'checked': visualChecked,
        'border-red-400 bg-red-500': visualChecked && priority === 'high',
        'border-orange-400 bg-orange-500': visualChecked && priority === 'medium',
        'border-lavender-400 bg-lavender-400': visualChecked && priority === 'low',
        'border-red-300': !visualChecked && priority === 'high',
        'border-orange-300': !visualChecked && priority === 'medium',
        'border-border-subtle': !visualChecked && (priority === 'low' || !priority),
      }"
    >
      <span class="check-icon">
        <svg class="w-3 h-3" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="2 6 5 9 10 3" />
        </svg>
      </span>
    </span>
  </button>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  checked: Boolean,
  // While the toggle API is in flight, render the *opposite* of `checked`
  // so the user sees instant feedback. The row stays in its current list
  // until the response arrives. (#293)
  pending: Boolean,
  priority: { type: String, default: 'low' },
})

defineEmits(['toggle'])

const visualChecked = computed(() => (props.pending ? !props.checked : props.checked))
</script>
