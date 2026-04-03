<template>
  <div class="card p-4 mb-6">
    <h3 class="text-sm font-semibold text-prussian-500 dark:text-lavender-200 mb-3">
      {{ isEditing ? 'Edit Reward' : 'New Reward' }}
    </h3>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
      <!-- Title -->
      <input v-model="form.title" class="input-base" placeholder="Reward name" aria-label="Reward name" />

      <!-- Point Cost -->
      <input v-model.number="form.point_cost" type="number" min="1" class="input-base" placeholder="Point cost" aria-label="Point cost" />

      <!-- Description -->
      <input v-model="form.description" class="input-base sm:col-span-2" placeholder="Description (optional)" aria-label="Description" />

      <!-- Icon -->
      <div class="sm:col-span-2">
        <label class="block text-xs font-medium text-prussian-500 dark:text-lavender-300 mb-1.5">Icon</label>
        <IconPicker v-model="form.icon" />
      </div>

      <!-- Reward Type -->
      <div>
        <label class="block text-xs font-medium text-prussian-500 dark:text-lavender-300 mb-1.5">Type</label>
        <select v-model="form.reward_type" class="input-base">
          <option value="standard">Standard</option>
          <option value="auction">Auction</option>
        </select>
      </div>

      <!-- Quantity (standard only) -->
      <div v-if="form.reward_type !== 'auction'">
        <label class="block text-xs font-medium text-prussian-500 dark:text-lavender-300 mb-1.5">
          Quantity
          <span class="text-lavender-400 font-normal">(blank = unlimited)</span>
        </label>
        <input v-model.number="form.quantity" type="number" min="0" class="input-base" placeholder="Unlimited" />
      </div>

      <!-- Expiration -->
      <div>
        <label class="block text-xs font-medium text-prussian-500 dark:text-lavender-300 mb-1.5">
          Expires
          <span class="text-lavender-400 font-normal">(optional)</span>
        </label>
        <input v-model="form.expires_at" type="datetime-local" class="input-base" />
      </div>

      <!-- Auction fields -->
      <template v-if="form.reward_type === 'auction'">
        <div>
          <label class="block text-xs font-medium text-prussian-500 dark:text-lavender-300 mb-1.5">
            Minimum Bid
            <span class="text-lavender-400 font-normal">(optional)</span>
          </label>
          <input v-model.number="form.min_bid" type="number" min="1" class="input-base" placeholder="No minimum" />
        </div>

        <div>
          <label class="block text-xs font-medium text-prussian-500 dark:text-lavender-300 mb-1.5">Auction Close Mode</label>
          <select v-model="auctionCloseMode" class="input-base">
            <option value="timed">Timed (auto-close)</option>
            <option value="parent_called">Parent-Called (manual)</option>
          </select>
        </div>

        <template v-if="auctionCloseMode === 'timed'">
          <div>
            <label class="block text-xs font-medium text-prussian-500 dark:text-lavender-300 mb-1.5">Bidding Opens</label>
            <input v-model="form.bid_start_at" type="datetime-local" class="input-base" />
          </div>
          <div>
            <label class="block text-xs font-medium text-prussian-500 dark:text-lavender-300 mb-1.5">Bidding Ends</label>
            <input v-model="form.bid_end_at" type="datetime-local" class="input-base" />
          </div>
        </template>
      </template>

      <!-- Visibility -->
      <div>
        <label class="block text-xs font-medium text-prussian-500 dark:text-lavender-300 mb-1.5">Visibility</label>
        <select v-model="form.visibility" class="input-base">
          <option value="everyone">Everyone</option>
          <option value="parent_only">Parents Only</option>
          <option value="child_only">Children Only</option>
          <option value="specific">Specific People</option>
        </select>
      </div>

      <!-- Age Range -->
      <div class="flex gap-2">
        <div class="flex-1">
          <label class="block text-xs font-medium text-prussian-500 dark:text-lavender-300 mb-1.5">Min Age</label>
          <input v-model.number="form.min_age" type="number" min="0" max="99" class="input-base" placeholder="Any" />
        </div>
        <div class="flex-1">
          <label class="block text-xs font-medium text-prussian-500 dark:text-lavender-300 mb-1.5">Max Age</label>
          <input v-model.number="form.max_age" type="number" min="0" max="99" class="input-base" placeholder="Any" />
        </div>
      </div>

      <!-- Specific People Picker (when visibility = 'specific') -->
      <div v-if="form.visibility === 'specific'" class="sm:col-span-2">
        <label class="block text-xs font-medium text-prussian-500 dark:text-lavender-300 mb-1.5">Who can see this?</label>
        <div class="flex flex-wrap gap-2">
          <label
            v-for="member in familyMembers"
            :key="member.id"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium cursor-pointer transition-colors"
            :class="isSelected(member.id)
              ? 'bg-wisteria-100 text-wisteria-700 dark:bg-wisteria-900/40 dark:text-wisteria-300 ring-1 ring-wisteria-300 dark:ring-wisteria-600'
              : 'bg-lavender-100 text-lavender-600 dark:bg-prussian-700 dark:text-lavender-400 hover:bg-lavender-200 dark:hover:bg-prussian-600'"
          >
            <input
              type="checkbox"
              :value="member.id"
              :checked="isSelected(member.id)"
              class="sr-only"
              @change="toggleMember(member.id)"
            />
            {{ member.name }}
          </label>
        </div>
      </div>
    </div>

    <div class="flex items-center justify-end gap-2 mt-3">
      <button class="btn-ghost btn-sm" @click="$emit('cancel')">Cancel</button>
      <button :disabled="!isValid" class="btn-primary btn-sm" @click="save">
        {{ isEditing ? 'Save Changes' : 'Create Reward' }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { storeToRefs } from 'pinia'
import { useAuthStore } from '@/stores/auth'
import IconPicker from '@/components/common/IconPicker.vue'

const props = defineProps({
  reward: {
    type: Object,
    default: null,
  },
  isEditing: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['save', 'cancel'])

const authStore = useAuthStore()
const { familyMembers } = storeToRefs(authStore)

const defaultForm = () => ({
  title: '',
  description: '',
  point_cost: null,
  icon: '',
  quantity: null,
  expires_at: '',
  visibility: 'everyone',
  visible_to: [],
  min_age: null,
  max_age: null,
  reward_type: 'standard',
  min_bid: null,
  bid_start_at: '',
  bid_end_at: '',
})

const form = ref(defaultForm())
const auctionCloseMode = ref('timed')

// Populate form when editing
watch(() => props.reward, (reward) => {
  if (reward) {
    form.value = {
      title: reward.title || '',
      description: reward.description || '',
      point_cost: reward.point_cost || null,
      icon: reward.icon || '',
      quantity: reward.quantity,
      expires_at: reward.expires_at ? new Date(reward.expires_at).toISOString().slice(0, 16) : '',
      visibility: reward.visibility || 'everyone',
      visible_to: reward.visible_to || [],
      min_age: reward.min_age,
      max_age: reward.max_age,
      reward_type: reward.reward_type || 'standard',
      min_bid: reward.min_bid,
      bid_start_at: reward.bid_start_at ? new Date(reward.bid_start_at).toISOString().slice(0, 16) : '',
      bid_end_at: reward.bid_end_at ? new Date(reward.bid_end_at).toISOString().slice(0, 16) : '',
    }
    auctionCloseMode.value = reward.bid_end_at ? 'timed' : 'parent_called'
  } else {
    form.value = defaultForm()
    auctionCloseMode.value = 'timed'
  }
}, { immediate: true })

const isValid = computed(() => {
  if (!form.value.title) return false
  // Auctions don't need point_cost (it's the display price / starting price)
  if (form.value.reward_type === 'auction') return true
  return form.value.point_cost > 0
})

const isSelected = (memberId) => (form.value.visible_to || []).includes(memberId)

const toggleMember = (memberId) => {
  if (!form.value.visible_to) form.value.visible_to = []
  const idx = form.value.visible_to.indexOf(memberId)
  if (idx >= 0) {
    form.value.visible_to.splice(idx, 1)
  } else {
    form.value.visible_to.push(memberId)
  }
}

const save = () => {
  const data = { ...form.value }
  // Clean up nullish values
  if (!data.quantity && data.quantity !== 0) data.quantity = null
  if (!data.expires_at) data.expires_at = null
  if (!data.min_age && data.min_age !== 0) data.min_age = null
  if (!data.max_age && data.max_age !== 0) data.max_age = null
  if (data.visibility !== 'specific') data.visible_to = null

  // Auction fields
  if (data.reward_type === 'auction') {
    if (!data.point_cost) data.point_cost = 0 // Auctions don't require a price
    data.quantity = 1 // Auctions are always quantity 1
    if (!data.min_bid) data.min_bid = null
    if (auctionCloseMode.value === 'parent_called') {
      data.bid_start_at = null
      data.bid_end_at = null
    } else {
      if (!data.bid_start_at) data.bid_start_at = null
      if (!data.bid_end_at) data.bid_end_at = null
    }
  } else {
    data.min_bid = null
    data.bid_start_at = null
    data.bid_end_at = null
  }

  emit('save', data)
}
</script>
