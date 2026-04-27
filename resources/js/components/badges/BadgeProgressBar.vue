<template>
  <div class="w-full">
    <div class="flex justify-between text-xs text-ink-tertiary mb-1">
      <span>{{ progress }} / {{ threshold }} {{ label }}</span>
      <span>{{ Math.round((progress / threshold) * 100) }}%</span>
    </div>
    <div class="w-full h-2 bg-surface-sunken rounded-full overflow-hidden">
      <div
        class="h-full rounded-full transition-all duration-200"
        :style="{
          width: Math.min(100, (progress / threshold) * 100) + '%',
          backgroundColor: color,
        }"
      ></div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  progress: { type: Number, default: 0 },
  threshold: { type: Number, default: 1 },
  color: { type: String, default: '#7d57a8' },
  label: { type: String, default: '' },
})
</script>
