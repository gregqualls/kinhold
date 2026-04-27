<template>
  <div class="flex flex-col h-full">
    <div class="flex items-center justify-between mb-3 flex-shrink-0">
      <h3 class="text-sm font-semibold text-ink-primary flex items-center gap-2">
        <BellIcon class="w-4 h-4 text-accent-lavender-bold" />
        {{ config.title || 'Activity' }}
      </h3>
      <RouterLink
        to="/points"
        class="text-xs font-medium text-accent-lavender-bold hover:opacity-80 transition-opacity"
      >
        View All
      </RouterLink>
    </div>

    <div v-if="loading" class="space-y-3">
      <KinSkeleton v-for="n in 4" :key="n" shape="rect" :height="'40px'" />
    </div>

    <KinEmptyState
      v-else-if="items.length === 0"
      :icon="BellIcon"
      title="No recent activity"
      size="sm"
      accent-color="lavender"
    />

    <div v-else :class="config.size === 'md' ? 'columns-2 gap-x-6 space-y-2 [&>*]:break-inside-avoid flex-1 min-h-0 overflow-y-auto' : 'space-y-2 flex-1 min-h-0 overflow-y-auto'">
      <div
        v-for="item in items"
        :key="item.id"
        class="flex items-start gap-3 p-1.5 rounded-lg"
      >
        <div
          class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0"
          :class="pointsBadgeClass(item.points)"
        >
          {{ item.points > 0 ? '+' : '' }}{{ item.points }}
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-sm text-ink-secondary leading-snug">
            {{ item.description }}
          </p>
          <p class="text-xs text-ink-tertiary mt-0.5">
            {{ item.user?.name?.split(' ')[0] }}
            <span v-if="item.created_at" class="ml-1">{{ formatTime(item.created_at) }}</span>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { DateTime } from 'luxon'
import { useWidgetData } from '@/composables/useWidgetData'
import { BellIcon } from '@heroicons/vue/24/outline'
import KinSkeleton from '@/components/design-system/KinSkeleton.vue'
import KinEmptyState from '@/components/design-system/KinEmptyState.vue'

const props = defineProps({
  config: { type: Object, required: true },
})

const { data, loading } = useWidgetData('/api/v1/points/feed', {})

const items = computed(() => {
  if (!data.value) return []
  const list = data.value.data || data.value.feed || data.value
  if (!Array.isArray(list)) return []
  const limit = props.config.size === 'md' ? 12 : 6
  return list.slice(0, limit)
})

function pointsBadgeClass(points) {
  if (points > 0) return 'bg-status-success/10 text-status-success'
  if (points < 0) return 'bg-status-failed/10 text-status-failed'
  return 'bg-surface-sunken text-ink-tertiary'
}

function formatTime(dateStr) {
  if (!dateStr) return ''
  return DateTime.fromISO(dateStr).toRelative() || ''
}
</script>
