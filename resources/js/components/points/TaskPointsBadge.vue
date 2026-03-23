<template>
  <span
    v-if="points"
    class="inline-flex items-center gap-0.5 text-xs font-semibold font-mono px-1.5 py-0.5 rounded-full bg-wisteria-100 dark:bg-wisteria-900/30 text-wisteria-600 dark:text-wisteria-400"
  >
    {{ points }} pts
  </span>
</template>

<script setup>
defineProps({
  points: {
    type: Number,
    default: null,
  },
})
</script>
