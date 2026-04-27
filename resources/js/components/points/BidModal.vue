<template>
  <KinModalSheet :model-value="true" title="Place Bid" size="sm" @close="$emit('close')">
    <p class="text-xs text-ink-tertiary mb-4">
      {{ reward.title }}
    </p>

    <!-- Current status -->
    <div class="space-y-2 mb-4">
      <div v-if="reward.highest_bid" class="flex justify-between text-sm">
        <span class="text-ink-tertiary">Highest bid</span>
        <span class="font-bold font-mono text-accent-lavender-bold">{{ reward.highest_bid }} pts</span>
      </div>
      <div v-if="reward.min_bid && !reward.highest_bid" class="flex justify-between text-sm">
        <span class="text-ink-tertiary">Minimum bid</span>
        <span class="font-bold font-mono text-accent-lavender-bold">{{ reward.min_bid }} pts</span>
      </div>
      <div v-if="reward.my_bid" class="flex justify-between text-sm">
        <span class="text-ink-tertiary">Your current bid</span>
        <span class="font-bold font-mono text-golden-600 dark:text-golden-400">{{ reward.my_bid }} pts (held)</span>
      </div>
      <div class="flex justify-between text-sm">
        <span class="text-ink-tertiary">Available points</span>
        <span class="font-bold font-mono">{{ availablePoints }} pts</span>
      </div>
      <div class="flex justify-between text-sm">
        <span class="text-ink-tertiary">Total bids</span>
        <span class="font-medium">{{ reward.total_bids || 0 }}</span>
      </div>
    </div>

    <!-- Bid input -->
    <KinInput
      v-model.number="bidAmount"
      type="number"
      label="Your Bid"
      :placeholder="`Min: ${minimumBid} pts`"
      helper="Points will be held until the auction ends. Released if outbid."
      :error="error"
      aria-label="Bid amount"
    />

    <template #actions>
      <div class="flex justify-end gap-2">
        <KinButton variant="ghost" size="sm" @click="$emit('close')">Cancel</KinButton>
        <KinButton
          variant="primary"
          size="sm"
          :disabled="!isValid || submitting"
          :loading="submitting"
          @click="submitBid"
        >
          {{ submitting ? 'Placing...' : (reward.my_bid ? 'Update Bid' : 'Place Bid') }}
        </KinButton>
      </div>
    </template>
  </KinModalSheet>
</template>

<script setup>
import { ref, computed } from 'vue'
import { usePointsStore } from '@/stores/points'
import KinModalSheet from '@/components/design-system/KinModalSheet.vue'
import KinInput from '@/components/design-system/KinInput.vue'
import KinButton from '@/components/design-system/KinButton.vue'

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
