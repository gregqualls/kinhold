<template>
  <div v-if="featuredRewards.length > 0">
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
      <div
        v-for="reward in featuredRewards"
        :key="reward.id"
        class="relative group flex flex-col items-center text-center p-4 rounded-xl border-2 transition-all duration-200"
        :class="canAfford(reward)
          ? 'border-wisteria-300 dark:border-wisteria-600 bg-gradient-to-b from-wisteria-50 to-white dark:from-wisteria-900/20 dark:to-prussian-800 hover:shadow-lg hover:border-wisteria-400 dark:hover:border-wisteria-500 cursor-pointer'
          : 'border-lavender-200 dark:border-prussian-700 bg-lavender-50/50 dark:bg-prussian-800/50'"
        @click="$emit('navigate')"
      >
        <!-- Icon -->
        <span class="text-3xl mb-2 select-none">{{ reward.icon || '🎁' }}</span>

        <!-- Title -->
        <h4 class="text-sm font-bold text-prussian-500 dark:text-lavender-200 leading-tight mb-1 line-clamp-2">
          {{ reward.title }}
        </h4>

        <!-- Cost badge -->
        <span
          class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold mt-auto"
          :class="canAfford(reward)
            ? 'bg-wisteria-100 text-wisteria-700 dark:bg-wisteria-900/40 dark:text-wisteria-300'
            : 'bg-lavender-100 text-lavender-600 dark:bg-prussian-700 dark:text-lavender-400'"
        >
          <StarIcon class="w-3.5 h-3.5" />
          {{ reward.point_cost }} pts
        </span>

        <!-- Affordable indicator -->
        <span
          v-if="canAfford(reward)"
          class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-green-500 rounded-full flex items-center justify-center shadow-sm"
        >
          <CheckIcon class="w-3 h-3 text-white" />
        </span>
      </div>
    </div>
  </div>

  <div v-else class="text-center py-6">
    <span class="text-3xl mb-2 block">🎁</span>
    <p class="text-sm font-medium text-prussian-500 dark:text-lavender-300 mb-1">No rewards yet</p>
    <p v-if="isParent" class="text-xs text-lavender-500 dark:text-lavender-400 mb-3">
      Create rewards your family can earn with points!
    </p>
    <p v-else class="text-xs text-lavender-500 dark:text-lavender-400">
      Ask a parent to add some rewards to the shop.
    </p>
    <RouterLink
      v-if="isParent"
      to="/points/rewards"
      class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-white bg-wisteria-600 hover:bg-wisteria-500 rounded-lg transition-colors"
    >
      <PlusIcon class="w-3.5 h-3.5" />
      Create Rewards
    </RouterLink>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { RouterLink } from 'vue-router'
import { StarIcon, CheckIcon, PlusIcon } from '@heroicons/vue/24/solid'

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
    default: 3,
  },
  isParent: {
    type: Boolean,
    default: false,
  },
})

defineEmits(['navigate'])

// Show the cheapest active rewards — most attainable and motivating
const featuredRewards = computed(() => {
  return [...props.rewards]
    .sort((a, b) => a.point_cost - b.point_cost)
    .slice(0, props.limit)
})

const canAfford = (reward) => props.bank >= reward.point_cost
</script>
