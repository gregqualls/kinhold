<template>
  <div class="flex flex-col h-full">
    <!-- Hero metric -->
    <div class="pb-3 border-b border-border-subtle">
      <p class="text-[10px] font-semibold uppercase tracking-widest text-ink-tertiary">
        {{ config.title || 'Your Points' }}
      </p>
      <KinSkeleton v-if="bankLoading" shape="rect" :width="'120px'" :height="'40px'" rounded="4px" class="mt-1" />
      <p v-else class="text-4xl font-bold font-mono text-ink-primary mt-1">
        {{ balance.toLocaleString() }}<span class="text-base font-normal ml-1 text-ink-tertiary">pts</span>
      </p>
    </div>

    <!-- Recent activity -->
    <div class="flex-1 min-h-0 pt-2">
      <p class="text-[10px] uppercase tracking-wider font-semibold text-ink-tertiary mb-1.5">Recent</p>
      <div v-if="feedLoading" class="space-y-2">
        <KinSkeleton v-for="n in 3" :key="n" shape="rect" :height="'24px'" rounded="4px" />
      </div>
      <div v-else class="space-y-1.5">
        <div
          v-for="item in recentActivity"
          :key="item.id"
          class="flex items-center justify-between gap-2"
        >
          <p class="text-xs text-ink-secondary truncate flex-1">
            {{ item.description }}
          </p>
          <span
            class="text-[10px] font-bold font-mono flex-shrink-0 px-2 py-0.5 rounded-full"
            :class="item.points > 0 ? 'text-status-success bg-status-success/10' : 'text-status-failed bg-status-failed/10'"
          >
            {{ item.points > 0 ? '+' : '' }}{{ item.points }}
          </span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useWidgetData } from '@/composables/useWidgetData'
import KinSkeleton from '@/components/design-system/KinSkeleton.vue'

defineProps({
  config: { type: Object, required: true },
})

const { data: bankData, loading: bankLoading } = useWidgetData('/api/v1/points/bank', {})
const { data: feedData, loading: feedLoading } = useWidgetData('/api/v1/points/feed', {})

const balance = computed(() => {
  if (!bankData.value) return 0
  return bankData.value.bank ?? bankData.value.balance ?? 0
})

const recentActivity = computed(() => {
  if (!feedData.value) return []
  const list = feedData.value.data || feedData.value.feed || feedData.value
  if (!Array.isArray(list)) return []
  return list.slice(0, 4)
})
</script>
