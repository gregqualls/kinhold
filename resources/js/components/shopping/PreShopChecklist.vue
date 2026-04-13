<template>
  <div class="flex flex-col h-full overflow-hidden">
    <!-- Header -->
    <div class="flex items-center justify-between px-4 md:px-6 py-3 border-b border-lavender-200 dark:border-prussian-700">
      <div>
        <h3 class="text-sm font-semibold text-prussian-500 dark:text-lavender-200">What do you already have?</h3>
        <p class="text-xs text-lavender-400 dark:text-lavender-500 mt-0.5">Mark items you already own so you only buy what you need.</p>
      </div>
      <button
        class="px-3 py-1.5 text-xs font-medium text-white bg-[#C4975A] hover:bg-[#D4A96A] rounded-[8px] transition-colors"
        @click="$emit('done')"
      >
        Done
      </button>
    </div>

    <!-- Items -->
    <div class="flex-1 overflow-y-auto px-4 md:px-6 py-4 space-y-4">
      <!-- Need it section -->
      <div>
        <div class="flex items-center gap-2 mb-2">
          <h4 class="text-xs font-semibold uppercase tracking-wider text-lavender-500 dark:text-lavender-400">Need it</h4>
          <span class="text-xs bg-lavender-100 dark:bg-prussian-700 text-lavender-600 dark:text-lavender-400 px-1.5 py-0.5 rounded-full font-medium">
            {{ needItems.length }}
          </span>
        </div>
        <div class="space-y-1.5">
          <div
            v-for="item in needItems"
            :key="item.id"
            class="flex items-center gap-3 px-3 py-2.5 bg-white dark:bg-prussian-800 border border-[#E8E4DF] dark:border-prussian-700 rounded-[10px]"
          >
            <div class="flex-1 min-w-0">
              <p class="text-sm text-prussian-500 dark:text-lavender-200">{{ item.name }}</p>
              <p v-if="item.quantity" class="text-xs text-lavender-400 dark:text-lavender-500">{{ item.quantity }}</p>
            </div>
            <button
              class="flex-shrink-0 px-3 py-1.5 text-xs font-medium rounded-[8px] bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 hover:bg-green-200 dark:hover:bg-green-900/50 transition-colors"
              @click="$emit('mark-on-hand', item.id)"
            >
              Have it
            </button>
          </div>
          <p v-if="needItems.length === 0" class="text-sm text-lavender-400 dark:text-lavender-500 text-center py-4">All items accounted for!</p>
        </div>
      </div>

      <!-- Got it section (collapsible) -->
      <details v-if="onHandItems.length > 0" class="group">
        <summary class="flex items-center gap-2 mb-2 cursor-pointer list-none">
          <h4 class="text-xs font-semibold uppercase tracking-wider text-lavender-500 dark:text-lavender-400">Got it</h4>
          <span class="text-xs bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 px-1.5 py-0.5 rounded-full font-medium">
            {{ onHandItems.length }}
          </span>
          <svg class="w-3.5 h-3.5 text-lavender-400 ml-auto transition-transform group-open:rotate-180" fill="none" viewBox="0 0 16 16">
            <path d="M4 6l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
        </summary>
        <div class="space-y-1.5">
          <div
            v-for="item in onHandItems"
            :key="item.id"
            class="flex items-center gap-3 px-3 py-2.5 bg-green-50 dark:bg-green-900/10 border border-green-200 dark:border-green-900/30 rounded-[10px]"
          >
            <div class="flex-1 min-w-0">
              <p class="text-sm text-prussian-500 dark:text-lavender-200 line-through opacity-70">{{ item.name }}</p>
              <p v-if="item.quantity" class="text-xs text-lavender-400 dark:text-lavender-500">{{ item.quantity }}</p>
            </div>
            <button
              class="flex-shrink-0 px-3 py-1.5 text-xs font-medium rounded-[8px] bg-lavender-100 dark:bg-prussian-700 text-lavender-600 dark:text-lavender-400 hover:bg-lavender-200 dark:hover:bg-prussian-600 transition-colors"
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
