<template>
  <div class="flex flex-col h-full overflow-hidden">
    <!-- Header -->
    <div class="flex items-center justify-between px-4 md:px-6 py-3 border-b border-border-subtle">
      <div>
        <h3 class="text-sm font-semibold text-ink-primary">What do you already have?</h3>
        <p class="text-xs text-ink-tertiary mt-0.5">Mark items you already own so you only buy what you need.</p>
      </div>
      <KinButton variant="primary" size="sm" @click="$emit('done')">Done</KinButton>
    </div>

    <!-- Items -->
    <div class="flex-1 overflow-y-auto px-4 md:px-6 py-4 space-y-4">
      <!-- Need it section -->
      <div>
        <div class="flex items-center gap-2 mb-2">
          <h4 class="text-xs font-semibold uppercase tracking-wider text-ink-tertiary">Need it</h4>
          <span class="text-xs bg-surface-sunken text-ink-secondary px-1.5 py-0.5 rounded-full font-medium">
            {{ needItems.length }}
          </span>
        </div>
        <div class="space-y-1.5">
          <div
            v-for="item in needItems"
            :key="item.id"
            class="flex items-center gap-3 px-3 py-2.5 bg-surface-raised border border-border-subtle rounded-[10px]"
          >
            <div class="flex-1 min-w-0">
              <p class="text-sm text-ink-primary">{{ item.name }}</p>
              <p v-if="item.quantity" class="text-xs text-ink-tertiary">{{ item.quantity }}</p>
            </div>
            <button
              class="flex-shrink-0 px-3 py-1.5 text-xs font-medium rounded-[8px] bg-status-success/10 text-status-success hover:bg-status-success/20 transition-colors"
              @click="$emit('mark-on-hand', item.id)"
            >
              Have it
            </button>
          </div>
          <p v-if="needItems.length === 0" class="text-sm text-ink-tertiary text-center py-4">All items accounted for!</p>
        </div>
      </div>

      <!-- Got it section (collapsible) -->
      <details v-if="onHandItems.length > 0" class="group">
        <summary class="flex items-center gap-2 mb-2 cursor-pointer list-none">
          <h4 class="text-xs font-semibold uppercase tracking-wider text-ink-tertiary">Got it</h4>
          <span class="text-xs bg-status-success/10 text-status-success px-1.5 py-0.5 rounded-full font-medium">
            {{ onHandItems.length }}
          </span>
          <svg class="w-3.5 h-3.5 text-ink-tertiary ml-auto transition-transform group-open:rotate-180" fill="none" viewBox="0 0 16 16">
            <path d="M4 6l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
        </summary>
        <div class="space-y-1.5">
          <div
            v-for="item in onHandItems"
            :key="item.id"
            class="flex items-center gap-3 px-3 py-2.5 bg-status-success/10 border border-status-success/20 rounded-[10px]"
          >
            <div class="flex-1 min-w-0">
              <p class="text-sm text-ink-primary line-through opacity-70">{{ item.name }}</p>
              <p v-if="item.quantity" class="text-xs text-ink-tertiary">{{ item.quantity }}</p>
            </div>
            <button
              class="flex-shrink-0 px-3 py-1.5 text-xs font-medium rounded-[8px] bg-surface-sunken text-ink-secondary hover:bg-surface-overlay transition-colors"
              @click="$emit('clear-on-hand', item.id)"
            >
              Need it
            </button>
          </div>
        </div>
      </details>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import KinButton from '@/components/design-system/KinButton.vue'

const props = defineProps({
  items: {
    type: Array,
    default: () => [],
  },
  shoppingWindow: {
    type: String,
    default: 'all',
  },
})

defineEmits(['mark-on-hand', 'clear-on-hand', 'done'])

const isWithinWindow = (item) => {
  if (props.shoppingWindow === 'all') return true
  if (!item.needed_date) return true
  const days = { '2days': 2, '3days': 3, 'week': 7 }[props.shoppingWindow]
  if (!days) return true
  const needed = new Date(item.needed_date)
  const cutoff = new Date()
  cutoff.setDate(cutoff.getDate() + days)
  cutoff.setHours(23, 59, 59, 999)
  return needed <= cutoff
}

const windowFiltered = computed(() => props.items.filter(isWithinWindow))
const needItems = computed(() => windowFiltered.value.filter((i) => !i.has_on_hand))
const onHandItems = computed(() => windowFiltered.value.filter((i) => i.has_on_hand))
</script>
