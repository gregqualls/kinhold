<template>
  <div class="flex items-center gap-3 overflow-x-auto pb-1">
    <div
      v-for="(entry, idx) in leaderboard"
      :key="entry.user_id"
      class="flex items-center gap-2 flex-shrink-0"
    >
      <div class="relative">
        <UserAvatar :user="entry.user" size="sm" />
        <span
          v-if="idx < 3"
          class="absolute -top-1 -right-1 w-4 h-4 rounded-full text-[9px] font-bold flex items-center justify-center text-white"
          :class="{
            'bg-golden-500': idx === 0,
            'bg-lavender-400': idx === 1,
            'bg-amber-600': idx === 2,
          }"
        >
          {{ idx + 1 }}
        </span>
      </div>
      <div class="text-sm">
        <p class="font-medium text-prussian-500 dark:text-lavender-200 leading-tight">
          {{ entry.user?.name?.split(' ')[0] }}
        </p>
        <p class="text-xs text-wisteria-600 dark:text-wisteria-400 font-semibold">
          {{ entry.total_points }} pts
        </p>
      </div>
    </div>
    <div v-if="leaderboard.length === 0" class="text-sm text-lavender-500 dark:text-lavender-400">
      No activity yet this period
    </div>
  </div>
</template>

<script setup>
import UserAvatar from '@/components/common/UserAvatar.vue'

defineProps({
  leaderboard: {
    type: Array,
    default: () => [],
  },
})
</script>
