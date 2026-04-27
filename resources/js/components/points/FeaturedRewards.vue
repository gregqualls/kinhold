<!--
  FeaturedRewards — small list of the cheapest purchasable rewards, surfaced
  on the dashboard as a vertical stack of rows. Each row carries a colored
  gradient swatch (the same per-reward fallback gradient used everywhere else),
  the title, and the cost pill — so the row reads at a glance without a tiny
  thumbnail-card needing to do double-duty.
-->
<template>
  <div v-if="featuredRewards.length > 0" class="space-y-1">
    <button
      v-for="reward in featuredRewards"
      :key="reward.id"
      type="button"
      class="group w-full flex items-center gap-3 px-3 py-2.5 rounded-[12px] bg-surface-raised border border-border-subtle hover:border-accent-lavender-bold/40 hover:bg-surface-sunken/60 transition-all text-left"
      @click="$emit('navigate')"
    >
      <!-- Gradient swatch + icon -->
      <div
        class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 relative overflow-hidden"
        :style="rewardSwatchStyle(reward)"
      >
        <span class="text-accent-lavender-bold relative">
          <IconRenderer v-if="reward.icon" :icon="reward.icon" :size="20" color="currentColor" />
          <GiftIcon v-else class="w-5 h-5" />
        </span>
      </div>

      <!-- Title (1-line truncate) -->
      <p class="flex-1 min-w-0 text-sm font-semibold text-ink-primary truncate">
        {{ reward.title }}
      </p>

      <!-- Cost pill — affordable variant carries the lavender accent -->
      <span
        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold flex-shrink-0"
        :class="canAfford(reward)
          ? 'bg-accent-lavender-soft/60 text-accent-lavender-bold'
          : 'bg-surface-sunken text-ink-tertiary'"
      >
        <StarIcon class="w-3 h-3" />
        {{ reward.point_cost }} pts
      </span>
    </button>
  </div>

  <KinEmptyState
    v-else
    :icon="GiftIcon"
    title="No rewards yet"
    :description="isParent ? 'Create rewards your family can earn with points!' : 'Ask a parent to add some rewards to the shop.'"
    accent-color="peach"
    size="sm"
  >
    <template v-if="isParent" #cta>
      <KinButton variant="primary" size="sm" to="/points/rewards">
        <template #leading>
          <PlusIcon class="w-4 h-4" />
        </template>
        Create Rewards
      </KinButton>
    </template>
  </KinEmptyState>
</template>

<script setup>
import { computed } from 'vue'
import { StarIcon, PlusIcon } from '@heroicons/vue/24/solid'
import { GiftIcon } from '@heroicons/vue/24/outline'
import IconRenderer from '@/components/common/IconRenderer.vue'
import KinEmptyState from '@/components/design-system/KinEmptyState.vue'
import KinButton from '@/components/design-system/KinButton.vue'

const props = defineProps({
  rewards: {
    type: Array,
    default: () => [],
  },
  bank: {
    type: Number,
    default: 0,
  },
  limit: {
    type: Number,
    default: 4,
  },
  isParent: {
    type: Boolean,
    default: false,
  },
})

defineEmits(['navigate'])

// Show the cheapest purchasable rewards — most attainable and motivating
const featuredRewards = computed(() => {
  return [...props.rewards]
    .filter((r) => r.is_purchasable !== false)
    .sort((a, b) => a.point_cost - b.point_cost)
    .slice(0, props.limit)
})

const canAfford = (reward) => props.bank >= reward.point_cost

// Stable per-reward gradient swatch — hashes the title across the four accent
// families so the row is visually identifiable without a real thumbnail.
const SWATCH_BG = {
  warm:     'var(--gradient-iridescent-warm)',
  lavender: 'radial-gradient(circle at 30% 30%, rgb(var(--accent-lavender-soft) / 0.95) 0%, rgb(var(--accent-lavender-soft) / 0.55) 100%)',
  peach:    'radial-gradient(circle at 30% 30%, rgb(var(--accent-peach-soft) / 0.95) 0%, rgb(var(--accent-peach-soft) / 0.55) 100%)',
  mint:     'radial-gradient(circle at 30% 30%, rgb(var(--accent-mint-soft) / 0.95) 0%, rgb(var(--accent-mint-soft) / 0.55) 100%)',
  sun:      'radial-gradient(circle at 30% 30%, rgb(var(--accent-sun-soft) / 0.95) 0%, rgb(var(--accent-sun-soft) / 0.55) 100%)',
  cool:     'radial-gradient(circle at 30% 30%, rgb(var(--accent-mint-soft) / 0.85) 0%, rgb(var(--accent-lavender-soft) / 0.55) 100%)',
}
const SWATCH_FAMILIES = ['warm', 'lavender', 'peach', 'mint', 'sun', 'cool']
const rewardSwatchStyle = (reward) => {
  const s = reward.title || ''
  let h = 0
  for (let i = 0; i < s.length; i++) h = (h * 31 + s.charCodeAt(i)) | 0
  const family = SWATCH_FAMILIES[Math.abs(h) % SWATCH_FAMILIES.length]
  return { backgroundImage: SWATCH_BG[family] }
}
</script>
