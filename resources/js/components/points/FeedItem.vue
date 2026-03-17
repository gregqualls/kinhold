<template>
  <div class="flex items-start gap-3 py-3">
    <UserAvatar :user="item.user" size="sm" />
    <div class="flex-1 min-w-0">
      <p class="text-sm text-prussian-500 dark:text-lavender-200">
        <span class="font-semibold">{{ item.user?.name }}</span>
        {{ actionText }}
      </p>
      <p v-if="item.description" class="text-xs text-lavender-500 dark:text-lavender-400 mt-0.5 truncate">
        {{ item.description }}
      </p>
      <p class="text-xs text-lavender-400 dark:text-lavender-500 mt-1">
        {{ formatTime(item.created_at) }}
      </p>
    </div>
    <span
      class="text-sm font-bold flex-shrink-0 px-2 py-0.5 rounded-full"
      :class="item.points > 0
        ? 'text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/30'
        : 'text-red-500 dark:text-red-400 bg-red-50 dark:bg-red-900/30'"
    >
      {{ item.points > 0 ? '+' : '' }}{{ item.points }}
    </span>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import UserAvatar from '@/components/common/UserAvatar.vue'

const props = defineProps({
  item: {
    type: Object,
    required: true,
  },
})

const actionText = computed(() => {
  const type = props.item.type
  const awardedBy = props.item.awarded_by_user?.name || props.item.awarded_by?.name

  switch (type) {
    case 'task_completion':
      return 'completed a task'
    case 'task_reversal':
      return 'had task points reversed'
    case 'kudos':
      return awardedBy ? `received kudos from ${awardedBy}` : 'received kudos'
    case 'deduction':
      return awardedBy ? `had points deducted by ${awardedBy}` : 'had points deducted'
    case 'redemption':
      return 'purchased a reward'
    case 'adjustment':
      return 'received a point adjustment'
    default:
      return ''
  }
})

const formatTime = (dateStr) => {
  if (!dateStr) return ''
  const d = new Date(dateStr)
  const now = new Date()
  const diff = now - d
  const mins = Math.floor(diff / 60000)
  if (mins < 1) return 'just now'
  if (mins < 60) return `${mins}m ago`
  const hours = Math.floor(mins / 60)
  if (hours < 24) return `${hours}h ago`
  const days = Math.floor(hours / 24)
  if (days < 7) return `${days}d ago`
  return d.toLocaleDateString()
}
</script>
