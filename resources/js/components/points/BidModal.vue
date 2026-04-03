<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" @click.self="$emit('close')">
    <div class="card p-5 w-full max-w-sm mx-4">
      <h3 class="text-lg font-bold text-prussian-500 dark:text-lavender-200 mb-1">
        Place Bid
      </h3>
      <p class="text-xs text-lavender-500 dark:text-lavender-400 mb-4">
        {{ reward.title }}
      </p>

      <!-- Current status -->
      <div class="space-y-2 mb-4">
        <div v-if="reward.highest_bid" class="flex justify-between text-sm">
          <span class="text-lavender-500 dark:text-lavender-400">Highest bid</span>
          <span class="font-bold font-mono text-wisteria-600 dark:text-wisteria-400">{{ reward.highest_bid }} pts</span>
        </div>
        <div v-if="reward.min_bid && !reward.highest_bid" class="flex justify-between text-sm">
          <span class="text-lavender-500 dark:text-lavender-400">Minimum bid</span>
          <span class="font-bold font-mono text-wisteria-600 dark:text-wisteria-400">{{ reward.min_bid }} pts</span>
        </div>
        <div v-if="reward.my_bid" class="flex justify-between text-sm">
          <span class="text-lavender-500 dark:text-lavender-400">Your current bid</span>
          <span class="font-bold font-mono text-golden-600 dark:text-golden-400">{{ reward.my_bid }} pts (held)</span>
        </div>
        <div class="flex justify-between text-sm">
          <span class="text-lavender-500 dark:text-lavender-400">Available points</span>
          <span class="font-bold font-mono">{{ availablePoints }} pts</span>
        </div>
        <div class="flex justify-between text-sm">
          <span class="text-lavender-500 dark:text-lavender-400">Total bids</span>
          <span class="font-medium">{{ reward.total_bids || 0 }}</span>
        </div>
      </div>

      <!-- Bid input -->
      <div class="mb-4">
        <label class="block text-xs font-medium text-prussian-500 dark:text-lavender-300 mb-1.5">Your Bid</label>
        <input
          v-model.number="bidAmount"
          type="number"
          :min="minimumBid"
          class="input-base w-full"
          :placeholder="`Min: ${minimumBid} pts`"
          aria-label="Bid amount"
        />
        <p class="text-[10px] text-lavender-400 dark:text-lavender-500 mt-1">
          Points will be held until the auction ends. Released if outbid.
        </p>
      </div>

      <!-- Error -->
      <p v-if="error" class="text-xs text-red-500 mb-3">{{ error }}</p>

      <!-- Actions -->
      <div class="flex justify-end gap-2">
        <button class="btn-ghost btn-sm" @click="$emit('close')">Cancel</button>
        <button
          :disabled="!isValid || submitting"
          class="btn-primary btn-sm"
          @click="submitBid"
        >
          {{ submitting ? 'Placing...' : (reward.my_bid ? 'Update Bid' : 'Place Bid') }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { usePointsStore } from '@/stores/points'

const props = defineProps({
  reward: {
    type: Object,
    required: true,
  },
  bank: {
    type: Number,
    default: 0,
  },
})

const emit = defineEmits(['close', 'bid-placed'])

const pointsStore = usePointsStore()
const bidAmount = ref(null)
const error = ref('')
const submitting = ref(false)

const minimumBid = computed(() => {
  if (props.reward.highest_bid) return props.reward.highest_bid + 1
  return props.reward.min_bid || 1
})

// Available = bank minus holds, but add back current bid hold if updating
const availablePoints = computed(() => {
  const myHold = props.reward.my_bid || 0
  return props.bank - pointsStore.rewards
    .filter((r) => r.my_bid && r.id !== props.reward.id)
    .reduce((sum, r) => sum + r.my_bid, 0) + myHold
})

const isValid = computed(() => {
  return bidAmount.value && bidAmount.value >= minimumBid.value && bidAmount.value <= availablePoints.value
})

const submitBid = async () => {
  error.value = ''
  submitting.value = true

  const result = await pointsStore.placeBid(props.reward.id, bidAmount.value)

  if (result.success) {
    emit('bid-placed', result.data)
  } else {
    error.value = result.error
  }

  submitting.value = false
}
</script>
