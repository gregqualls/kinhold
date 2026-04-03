<template>
  <!-- STANDARD REWARD CARD -->
  <div
    v-if="!isAuction"
    class="card p-4 flex flex-col rounded-[12px]"
    :class="{ 'opacity-60': !reward.is_purchasable && !isParent }"
  >
    <div class="flex items-start justify-between mb-2">
      <div class="w-7 h-7 text-wisteria-500 dark:text-wisteria-400">
        <IconRenderer v-if="reward.icon" :icon="reward.icon" :size="28" color="currentColor" />
        <GiftIcon v-else class="w-7 h-7" />
      </div>
      <div v-if="isParent" class="flex items-center gap-2">
        <span class="text-xs text-lavender-500 dark:text-lavender-400 cursor-pointer hover:text-wisteria-500" @click="$emit('edit', reward)">Edit</span>
        <span class="text-xs text-lavender-500 dark:text-lavender-400 cursor-pointer hover:text-red-500" @click="$emit('delete', reward.id)">Delete</span>
      </div>
    </div>

    <h3 class="text-sm font-bold text-prussian-500 dark:text-lavender-200 mb-1">{{ reward.title }}</h3>
    <p v-if="reward.description" class="text-xs text-lavender-500 dark:text-lavender-400 mb-2 flex-1">{{ reward.description }}</p>

    <div class="flex flex-wrap gap-1.5 mb-3">
      <span v-if="stockLabel" class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium" :class="stockClass">{{ stockLabel }}</span>
      <span v-if="expiryLabel" class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium" :class="expiryClass">{{ expiryLabel }}</span>
      <span v-if="isParent && visibilityLabel" class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-prussian-100 text-prussian-600 dark:bg-prussian-700 dark:text-lavender-300">{{ visibilityLabel }}</span>
    </div>

    <div class="flex items-center justify-between mt-auto">
      <span class="text-sm font-bold font-mono text-wisteria-600 dark:text-wisteria-400">{{ reward.point_cost }} pts</span>
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

  <!-- AUCTION CARD — distinct layout -->
  <div
    v-else
    class="card rounded-[12px] overflow-hidden sm:col-span-2"
    :class="{ 'opacity-60': reward.is_resolved }"
  >
    <!-- Auction header bar -->
    <div
      class="flex items-center justify-between px-4 py-2"
      :class="reward.bidding_open
        ? 'bg-wisteria-100 dark:bg-wisteria-900/30'
        : 'bg-lavender-100 dark:bg-prussian-700'"
    >
      <div class="flex items-center gap-2">
        <span
          class="text-[10px] font-bold uppercase tracking-wider"
          :class="reward.bidding_open
            ? 'text-wisteria-700 dark:text-wisteria-300'
            : 'text-lavender-600 dark:text-lavender-400'"
        >
          {{ reward.bid_end_at ? 'Timed Auction' : 'Live Auction' }}
        </span>
        <span v-if="countdownLabel" class="text-[10px] font-medium text-lavender-600 dark:text-lavender-400">
          · {{ countdownLabel }}
        </span>
      </div>
      <div v-if="isParent" class="flex items-center gap-2">
        <span class="text-xs text-lavender-500 dark:text-lavender-400 cursor-pointer hover:text-wisteria-500" @click="$emit('edit', reward)">Edit</span>
        <span class="text-xs text-lavender-500 dark:text-lavender-400 cursor-pointer hover:text-red-500" @click="$emit('delete', reward.id)">Delete</span>
      </div>
    </div>

    <!-- Body: two-column on sm+ -->
    <div class="p-4">
      <div class="flex flex-col sm:flex-row sm:gap-6">
        <!-- Left: reward info -->
        <div class="flex-1 min-w-0 mb-3 sm:mb-0">
          <div class="flex items-center gap-2 mb-1">
            <div class="w-6 h-6 text-wisteria-500 dark:text-wisteria-400 shrink-0">
              <IconRenderer v-if="reward.icon" :icon="reward.icon" :size="24" color="currentColor" />
              <GiftIcon v-else class="w-6 h-6" />
            </div>
            <h3 class="text-sm font-bold text-prussian-500 dark:text-lavender-200 truncate">{{ reward.title }}</h3>
          </div>
          <p v-if="reward.description" class="text-xs text-lavender-500 dark:text-lavender-400 mb-2 line-clamp-2">{{ reward.description }}</p>
          <div class="flex flex-wrap gap-1.5">
            <span v-if="isParent && visibilityLabel" class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-prussian-100 text-prussian-600 dark:bg-prussian-700 dark:text-lavender-300">{{ visibilityLabel }}</span>
            <span v-if="stockLabel" class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium" :class="stockClass">{{ stockLabel }}</span>
          </div>
        </div>

        <!-- Right: bid stats -->
        <div v-if="!reward.is_resolved" class="sm:w-44 shrink-0 space-y-1.5 bg-lavender-50 dark:bg-prussian-800/50 rounded-lg p-3">
          <div v-if="reward.highest_bid" class="flex justify-between text-xs">
            <span class="text-lavender-500 dark:text-lavender-400">High bid</span>
            <span class="font-bold font-mono text-wisteria-600 dark:text-wisteria-400">{{ reward.highest_bid }} pts</span>
          </div>
          <div v-else class="text-xs text-lavender-400 dark:text-lavender-500 text-center py-1">No bids yet</div>
          <div v-if="reward.total_bids" class="flex justify-between text-xs">
            <span class="text-lavender-500 dark:text-lavender-400">Total bids</span>
            <span class="font-medium text-prussian-500 dark:text-lavender-300">{{ reward.total_bids }}</span>
          </div>
          <div v-if="reward.min_bid && !reward.highest_bid" class="flex justify-between text-xs">
            <span class="text-lavender-500 dark:text-lavender-400">Min bid</span>
            <span class="font-medium text-prussian-500 dark:text-lavender-300">{{ reward.min_bid }} pts</span>
          </div>
          <div v-if="reward.my_bid" class="flex justify-between text-xs pt-1.5 border-t border-lavender-200 dark:border-prussian-600">
            <span class="text-golden-600 dark:text-golden-400">Your bid</span>
            <span class="font-bold font-mono text-golden-600 dark:text-golden-400">{{ reward.my_bid }} pts</span>
          </div>
        </div>

        <!-- Resolved state -->
        <div v-else class="sm:w-44 shrink-0 flex items-center justify-center bg-lavender-50 dark:bg-prussian-800/50 rounded-lg p-3">
          <span class="text-sm font-medium text-lavender-500 dark:text-lavender-400">Auction ended</span>
        </div>
      </div>

      <!-- Action bar -->
      <div v-if="reward.bidding_open" class="flex items-center justify-between mt-4 pt-3 border-t border-lavender-100 dark:border-prussian-700">
        <button class="btn-primary btn-sm" @click="$emit('bid', reward)">
          {{ reward.my_bid ? 'Update Bid' : 'Place Bid' }}
        </button>
        <div v-if="isParent" class="flex items-center gap-3">
          <button
            v-if="!reward.bid_end_at"
            class="text-xs font-medium text-wisteria-600 dark:text-wisteria-400 hover:text-wisteria-700 dark:hover:text-wisteria-300"
            @click="$emit('close-auction', reward.id)"
          >
            Close Auction
          </button>
          <button
            class="text-xs font-medium text-red-500 hover:text-red-600 dark:text-red-400 dark:hover:text-red-300"
            @click="$emit('cancel-auction', reward.id)"
          >
            Cancel
          </button>
        </div>
      </div>
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

// Expiry label (standard rewards only)
const expiryLabel = computed(() => {
  if (isAuction.value || !props.reward.expires_at) return null
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

// Countdown label (auction end time)
const countdownLabel = computed(() => {
  if (!isAuction.value) return null
  if (!props.reward.bid_end_at) return props.reward.bidding_open ? 'Open until closed' : null
  const target = new Date(props.reward.bid_end_at)
  const now = new Date()
  const diffMs = target - now
  if (diffMs <= 0) return 'Ended'
  const hours = Math.floor(diffMs / (1000 * 60 * 60))
  const days = Math.floor(hours / 24)
  if (hours < 1) return `${Math.ceil(diffMs / (1000 * 60))}m left`
  if (hours < 24) return `${hours}h left`
  if (days === 1) return 'Ends tomorrow'
  if (days <= 7) return `${days}d left`
  return `Ends ${target.toLocaleDateString()}`
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
