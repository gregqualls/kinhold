<template>
  <div
    class="card p-4 flex flex-col rounded-[12px]"
    :class="{
      'opacity-60': !reward.is_purchasable && !isParent && !isAuction,
      'ring-2 ring-wisteria-300 dark:ring-wisteria-600': isAuction && reward.bidding_open,
    }"
  >
    <!-- Header: icon + type badge + parent actions -->
    <div class="flex items-start justify-between mb-2">
      <div class="flex items-center gap-2">
        <div class="w-7 h-7 text-wisteria-500 dark:text-wisteria-400">
          <IconRenderer v-if="reward.icon" :icon="reward.icon" :size="28" color="currentColor" />
          <GiftIcon v-else class="w-7 h-7" />
        </div>
        <span
          v-if="isAuction"
          class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider"
          :class="reward.bidding_open
            ? 'bg-wisteria-100 text-wisteria-700 dark:bg-wisteria-900/40 dark:text-wisteria-300'
            : 'bg-lavender-100 text-lavender-600 dark:bg-prussian-700 dark:text-lavender-400'"
        >
          {{ reward.bid_end_at ? 'Auction' : 'Live Auction' }}
        </span>
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

      <!-- Expiry / Auction countdown -->
      <span v-if="countdownLabel" class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium" :class="countdownClass">
        {{ countdownLabel }}
      </span>

      <!-- Visibility (parent only) -->
      <span v-if="isParent && visibilityLabel" class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-prussian-100 text-prussian-600 dark:bg-prussian-700 dark:text-lavender-300">
        {{ visibilityLabel }}
      </span>
    </div>

    <!-- Auction info -->
    <div v-if="isAuction && !reward.is_resolved" class="mb-3 space-y-1">
      <div v-if="reward.highest_bid" class="flex justify-between text-xs">
        <span class="text-lavender-500 dark:text-lavender-400">Highest bid</span>
        <span class="font-bold font-mono text-wisteria-600 dark:text-wisteria-400">{{ reward.highest_bid }} pts</span>
      </div>
      <div v-if="reward.total_bids" class="flex justify-between text-xs">
        <span class="text-lavender-500 dark:text-lavender-400">Bids</span>
        <span class="font-medium">{{ reward.total_bids }}</span>
      </div>
      <div v-if="reward.my_bid" class="flex justify-between text-xs">
        <span class="text-golden-600 dark:text-golden-400">Your bid</span>
        <span class="font-bold font-mono text-golden-600 dark:text-golden-400">{{ reward.my_bid }} pts (held)</span>
      </div>
    </div>

    <!-- Footer: cost/action -->
    <div class="flex items-center justify-between mt-auto">
      <span class="text-sm font-bold font-mono text-wisteria-600 dark:text-wisteria-400">
        {{ isAuction ? (reward.min_bid ? `Min: ${reward.min_bid}` : 'Open') : `${reward.point_cost} pts` }}
      </span>

      <!-- Standard reward actions -->
      <template v-if="!isAuction">
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
      </template>

      <!-- Auction actions -->
      <template v-else>
        <div class="flex gap-1.5">
          <button
            v-if="reward.bidding_open"
            class="btn-primary btn-sm"
            @click="$emit('bid', reward)"
          >
            {{ reward.my_bid ? 'Update Bid' : 'Place Bid' }}
          </button>
          <button
            v-if="isParent && reward.bidding_open && !reward.bid_end_at"
            class="btn-ghost btn-sm text-xs"
            @click="$emit('close-auction', reward.id)"
          >
            Close
          </button>
          <button
            v-if="isParent && reward.bidding_open"
            class="btn-ghost btn-sm text-xs text-red-500 hover:text-red-600"
            @click="$emit('cancel-auction', reward.id)"
          >
            Cancel
          </button>
          <span v-if="!reward.bidding_open && reward.is_resolved" class="text-xs font-medium text-green-600 dark:text-green-400 italic">
            Ended
          </span>
          <span v-if="!reward.bidding_open && !reward.is_resolved" class="text-xs font-medium text-lavender-500 dark:text-lavender-400 italic">
            Not started
          </span>
        </div>
      </template>
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

defineEmits(['purchase', 'delete', 'edit', 'bid', 'close-auction', 'cancel-auction'])

const isAuction = computed(() => props.reward.reward_type === 'auction')
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

// Countdown label (expiry for standard, auction end for auctions)
const countdownLabel = computed(() => {
  const dateStr = isAuction.value ? props.reward.bid_end_at : props.reward.expires_at
  if (!dateStr) {
    if (isAuction.value && !props.reward.bid_end_at) return null
    return null
  }
  if (!isAuction.value && props.reward.is_expired) return 'Expired'
  const target = new Date(dateStr)
  const now = new Date()
  const diffMs = target - now
  if (diffMs <= 0) return isAuction.value ? 'Auction ended' : 'Expired'
  const hours = Math.floor(diffMs / (1000 * 60 * 60))
  const days = Math.floor(hours / 24)
  if (hours < 1) return `${Math.ceil(diffMs / (1000 * 60))}m left`
  if (hours < 24) return `${hours}h left`
  if (days === 1) return isAuction.value ? 'Ends tomorrow' : 'Expires tomorrow'
  if (days <= 7) return `${days}d left`
  return `${isAuction.value ? 'Ends' : 'Expires'} ${target.toLocaleDateString()}`
})

const countdownClass = computed(() => {
  const dateStr = isAuction.value ? props.reward.bid_end_at : props.reward.expires_at
  if (!dateStr) return ''
  const target = new Date(dateStr)
  const now = new Date()
  if (target <= now) return 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400'
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
