<template>
  <div
    class="card p-4 flex flex-col rounded-[12px]"
    :class="{ 'opacity-60': !reward.is_purchasable && !isParent }"
  >
    <!-- Header: icon + parent actions -->
    <div class="flex items-start justify-between mb-2">
      <div class="w-7 h-7 text-wisteria-500 dark:text-wisteria-400">
        <IconRenderer v-if="reward.icon" :icon="reward.icon" :size="28" color="currentColor" />
        <GiftIcon v-else class="w-7 h-7" />
      </div>
      <div v-if="isParent" class="flex items-center gap-2">
        <span
          class="text-xs text-lavender-500 dark:text-lavender-400 cursor-pointer hover:text-wisteria-500"
          @click="$emit('edit', reward)"
        >
          Edit
        </span>
        <span
          class="text-xs text-lavender-500 dark:text-lavender-400 cursor-pointer hover:text-red-500"
          @click="$emit('delete', reward.id)"
        >
          Delete
        </span>
      </div>
    </div>

    <!-- Title -->
    <h3 class="text-sm font-bold text-prussian-500 dark:text-lavender-200 mb-1">
      {{ reward.title }}
    </h3>

    <!-- Description -->
    <p v-if="reward.description" class="text-xs text-lavender-500 dark:text-lavender-400 mb-2 flex-1">
      {{ reward.description }}
    </p>

    <!-- Status badges -->
    <div class="flex flex-wrap gap-1.5 mb-3">
      <!-- Stock -->
      <span v-if="stockLabel" class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium" :class="stockClass">
        {{ stockLabel }}
      </span>

      <!-- Expiry -->
      <span v-if="expiryLabel" class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium" :class="expiryClass">
        {{ expiryLabel }}
      </span>

      <!-- Visibility (parent only) -->
      <span v-if="isParent && visibilityLabel" class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-prussian-100 text-prussian-600 dark:bg-prussian-700 dark:text-lavender-300">
        {{ visibilityLabel }}
      </span>
    </div>

    <!-- Footer: cost + purchase -->
    <div class="flex items-center justify-between mt-auto">
      <span class="text-sm font-bold font-mono text-wisteria-600 dark:text-wisteria-400">
        {{ reward.point_cost }} pts
      </span>
      <button
        v-if="reward.is_purchasable !== false"
        :disabled="!canAfford"
        class="btn-sm"
        :class="canAfford ? 'btn-primary' : 'btn-ghost opacity-50 cursor-not-allowed'"
        @click="$emit('purchase', reward.id)"
      >
        {{ canAfford ? 'Purchase' : 'Need more pts' }}
      </button>
      <span v-else class="text-xs font-medium text-lavender-500 dark:text-lavender-400 italic">
        {{ reward.is_expired ? 'Expired' : 'Sold Out' }}
      </span>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { GiftIcon } from '@heroicons/vue/24/outline'
import IconRenderer from '@/components/common/IconRenderer.vue'

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

defineEmits(['purchase', 'delete', 'edit'])

const canAfford = computed(() => props.bank >= props.reward.point_cost)

// Stock label
const stockLabel = computed(() => {
  const remaining = props.reward.remaining_stock
  if (remaining === null || remaining === undefined) return null
  if (remaining === 0) return 'Sold Out'
  return `${remaining} left`
})

const stockClass = computed(() => {
  const remaining = props.reward.remaining_stock
  if (remaining === 0) return 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400'
  if (remaining !== null && remaining <= 3) return 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400'
  return 'bg-lavender-100 text-lavender-600 dark:bg-prussian-700 dark:text-lavender-300'
})

// Expiry label
const expiryLabel = computed(() => {
  if (!props.reward.expires_at) return null
  if (props.reward.is_expired) return 'Expired'
  const expDate = new Date(props.reward.expires_at)
  const now = new Date()
  const daysLeft = Math.ceil((expDate - now) / (1000 * 60 * 60 * 24))
  if (daysLeft <= 0) return 'Expired'
  if (daysLeft === 1) return 'Expires tomorrow'
  if (daysLeft <= 7) return `${daysLeft}d left`
  return `Expires ${expDate.toLocaleDateString()}`
})

const expiryClass = computed(() => {
  if (props.reward.is_expired) return 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400'
  return 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400'
})

// Visibility label (parent-only context)
const visibilityLabel = computed(() => {
  const v = props.reward.visibility
  if (!v || v === 'everyone') return null
  if (v === 'parent_only') return 'Parents Only'
  if (v === 'child_only') return 'Children Only'
  if (v === 'specific' && props.reward.visible_to_names?.length) {
    return `For ${props.reward.visible_to_names.join(', ')}`
  }
  if (v === 'specific') return 'Specific People'
  return null
})
</script>
