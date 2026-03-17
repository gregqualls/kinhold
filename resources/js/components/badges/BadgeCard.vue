<template>
  <div
    class="card p-4 flex flex-col items-center text-center transition-all"
    :class="badge.is_earned ? 'ring-1' : 'opacity-75'"
    :style="badge.is_earned ? { borderColor: badge.color + '40' } : {}"
  >
    <BadgeIcon
      :icon="badge.icon"
      :color="badge.color"
      :locked="!badge.is_earned"
      size="lg"
    />

    <h3 class="mt-3 text-sm font-bold text-prussian-500 dark:text-lavender-200">
      {{ badge.name }}
    </h3>
    <p class="text-xs text-lavender-500 dark:text-lavender-400 mt-1">
      {{ badge.description }}
    </p>

    <!-- Progress bar for visible, unearned badges -->
    <div v-if="!badge.is_earned && badge.progress !== null && badge.trigger_threshold" class="w-full mt-3">
      <div class="flex justify-between text-xs text-lavender-400 mb-1">
        <span>{{ badge.progress }}</span>
        <span>{{ badge.trigger_threshold }}</span>
      </div>
      <div class="w-full h-1.5 bg-lavender-200 dark:bg-prussian-700 rounded-full overflow-hidden">
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
    <p v-if="badge.is_earned" class="text-xs text-emerald-500 dark:text-emerald-400 mt-2 font-medium">
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
