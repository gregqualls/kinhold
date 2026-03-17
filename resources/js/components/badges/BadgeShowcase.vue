<template>
  <div class="flex gap-3 overflow-x-auto pb-1">
    <div
      v-for="badge in badges"
      :key="badge.id"
      class="flex flex-col items-center flex-shrink-0"
    >
      <BadgeIcon :icon="badge.icon" :color="badge.color" size="sm" />
      <p class="text-[10px] text-lavender-500 dark:text-lavender-400 mt-1 text-center max-w-[60px] truncate">
        {{ badge.name }}
      </p>
    </div>
    <div v-if="badges.length === 0" class="text-xs text-lavender-400 dark:text-lavender-500 py-2">
      No badges earned yet
    </div>
  </div>
</template>

<script setup>
import BadgeIcon from './BadgeIcon.vue'

defineProps({
  badges: {
    type: Array,
    default: () => [],
  },
})
</script>
