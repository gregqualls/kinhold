<template>
  <div class="card p-4 flex flex-col">
    <div class="flex items-start justify-between mb-2">
      <div class="text-2xl">{{ reward.icon || '🎁' }}</div>
      <span
        v-if="isParent"
        class="text-xs text-lavender-500 dark:text-lavender-400 cursor-pointer hover:text-red-500"
        @click="$emit('delete', reward.id)"
      >
        Delete
      </span>
    </div>

    <h3 class="text-sm font-bold text-prussian-500 dark:text-lavender-200 mb-1">
      {{ reward.title }}
    </h3>
    <p v-if="reward.description" class="text-xs text-lavender-500 dark:text-lavender-400 mb-3 flex-1">
      {{ reward.description }}
    </p>

    <div class="flex items-center justify-between mt-auto">
      <span class="text-sm font-bold text-wisteria-600 dark:text-wisteria-400">
        {{ reward.point_cost }} pts
      </span>
      <button
        @click="$emit('purchase', reward.id)"
        :disabled="!canAfford"
        class="btn-sm"
        :class="canAfford ? 'btn-primary' : 'btn-ghost opacity-50 cursor-not-allowed'"
      >
        {{ canAfford ? 'Purchase' : 'Need more pts' }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  reward: {
    type: Object,
    required: true,
  },
  bank: {
    type: Number,
    default: 0,
  },
  isParent: {
    type: Boolean,
    default: false,
  },
})

defineEmits(['purchase', 'delete'])

const canAfford = computed(() => props.bank >= props.reward.point_cost)
</script>
