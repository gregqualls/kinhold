<template>
  <div v-if="badges.length > 0" class="flex flex-wrap gap-3">
    <div
      v-for="badge in displayedBadges"
      :key="badge.id"
      class="flex flex-col items-center"
      :style="{ width: itemWidth }"
    >
      <BadgeIcon :icon="badge.icon" :color="badge.color" size="sm" />
      <p class="text-[10px] text-lavender-500 dark:text-lavender-400 mt-1 text-center truncate w-full">
        {{ badge.name }}
      </p>
    </div>
    <div
      v-if="badges.length > maxDisplay"
      class="flex flex-col items-center justify-center"
      :style="{ width: itemWidth }"
    >
      <div class="w-10 h-10 rounded-full bg-lavender-100 dark:bg-prussian-700 flex items-center justify-center">
        <span class="text-xs font-medium text-lavender-500 dark:text-lavender-400">+{{ badges.length - maxDisplay }}</span>
      </div>
    </div>
  </div>
  <div v-else class="text-xs text-lavender-400 dark:text-lavender-500 py-2">
    No badges earned yet
  </div>
</template>

<script setup>
import { computed } from 'vue'
import BadgeIcon from './BadgeIcon.vue'

const props = defineProps({
  badges: {
    type: Array,
    default: () => [],
  },
  maxDisplay: {
    type: Number,
    default: 8,
  },
})

const itemWidth = '52px'

const displayedBadges = computed(() => {
  return props.badges.slice(0, props.maxDisplay)
})
</script>
