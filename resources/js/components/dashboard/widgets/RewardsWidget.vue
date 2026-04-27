<template>
  <div>
    <div class="flex items-center justify-between mb-3">
      <h3 class="text-sm font-semibold text-ink-primary flex items-center gap-2">
        <GiftIcon class="w-4 h-4 text-accent-lavender-bold" />
        {{ config.title || 'Rewards Shop' }}
      </h3>
      <RouterLink
        to="/points/rewards"
        class="text-xs font-medium text-accent-lavender-bold hover:opacity-80 transition-opacity"
      >
        View All
      </RouterLink>
    </div>

    <div v-if="loading" :class="gridClass">
      <KinSkeleton v-for="n in displayLimit" :key="n" shape="rect" :height="'96px'" rounded="12px" />
    </div>

    <FeaturedRewards
      v-else
      :rewards="rewards"
      :bank="bank"
      :is-parent="isParent"
      :limit="displayLimit"
      @navigate="$router.push('/points/rewards')"
    />
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { storeToRefs } from 'pinia'
import { useAuthStore } from '@/stores/auth'
import { useWidgetData } from '@/composables/useWidgetData'
import FeaturedRewards from '@/components/points/FeaturedRewards.vue'
import { GiftIcon } from '@heroicons/vue/24/outline'
import KinSkeleton from '@/components/design-system/KinSkeleton.vue'

const props = defineProps({
  config: { type: Object, required: true },
})

const authStore = useAuthStore()
const { isParent } = storeToRefs(authStore)

const { data: rewardsData, loading } = useWidgetData('/api/v1/rewards', {})
const { data: bankData } = useWidgetData('/api/v1/points/bank', {})

const rewards = computed(() => {
  if (!rewardsData.value) return []
  const list = rewardsData.value.rewards || rewardsData.value.data || rewardsData.value
  if (!Array.isArray(list)) return []
  return list
})

const bank = computed(() => {
  if (!bankData.value) return 0
  return bankData.value.bank ?? bankData.value.balance ?? 0
})

// Show more rewards for larger sizes
const displayLimit = computed(() => {
  const size = props.config.size || 'sm'
  if (size === 'lg') return 9
  if (size === 'md') return 6
  return 3
})

const gridClass = computed(() => {
  const size = props.config.size || 'sm'
  if (size === 'lg') return 'grid grid-cols-3 gap-3'
  if (size === 'md') return 'grid grid-cols-3 gap-3'
  return 'grid grid-cols-3 gap-3'
})
</script>
