<template>
  <div
    class="bg-surface-raised border border-border-subtle rounded-card p-4 flex flex-col items-center text-center transition-all"
    :class="badge.is_earned ? '' : 'opacity-60'"
  >
    <BadgeIcon
      :icon="badge.icon"
      :color="badge.color"
      :locked="!badge.is_earned"
      size="lg"
    />

    <h3 class="mt-3 text-sm font-bold text-ink-primary">
      {{ badge.name }}
    </h3>
    <p class="text-xs text-ink-tertiary mt-1">
      {{ badge.description }}
    </p>

    <!-- Progress bar for visible, unearned badges -->
    <div v-if="!badge.is_earned && badge.progress !== null && badge.trigger_threshold" class="w-full mt-3">
      <div class="flex justify-between text-xs text-ink-tertiary mb-1">
        <span>{{ badge.progress }}</span>
        <span>{{ badge.trigger_threshold }}</span>
      </div>
      <div class="w-full h-1.5 bg-surface-sunken rounded-full overflow-hidden">
        <div
          class="h-full rounded-full transition-all"
          :style="{
            width: Math.min(100, (badge.progress / badge.trigger_threshold) * 100) + '%',
            backgroundColor: badge.color,
          }"
        ></div>
      </div>
    </div>

    <!-- Earned indicator -->
    <p v-if="badge.is_earned" class="text-xs text-status-success mt-2 font-medium">
      Earned!
    </p>
  </div>
</template>

<script setup>
import BadgeIcon from './BadgeIcon.vue'

defineProps({
  badge: {
    type: Object,
    required: true,
  },
})
</script>
