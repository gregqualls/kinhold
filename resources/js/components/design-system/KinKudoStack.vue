<template>
  <button
    v-if="variant === 'action'"
    type="button"
    class="kin-pressable inline-flex items-center gap-1 min-h-[40px] px-3 rounded-full bg-accent-lavender-soft text-accent-lavender-bold hover:bg-accent-lavender-bold hover:text-white transition-[background-color,color,transform] duration-quick ease-out-soft disabled:opacity-50 disabled:cursor-not-allowed"
    :disabled="disabled"
    :aria-label="ariaLabel"
    @click="$emit('click')"
  >
    <HandThumbUpIcon class="w-4 h-4" />
    <span v-if="count" class="text-xs font-semibold">{{ count }}</span>
  </button>

  <span
    v-else
    class="inline-flex items-center gap-1 min-h-[32px] px-2 rounded-full bg-surface-sunken text-ink-tertiary"
    :aria-label="ariaLabel"
  >
    <component :is="variant === 'stacked' ? HandThumbUpSolidIcon : HandThumbUpIcon" class="w-4 h-4" />
    <span v-if="count" class="text-xs font-semibold">{{ count }}</span>
  </span>
</template>

<script setup>
import { HandThumbUpIcon } from '@heroicons/vue/24/outline'
import { HandThumbUpIcon as HandThumbUpSolidIcon } from '@heroicons/vue/24/solid'

defineProps({
  // 'action':  tappable stack button (lavender active, outline icon)
  // 'stacked': I already +1'd (solid icon, muted pill)
  // 'count':   others +1'd and I can't (outline icon, muted pill)
  variant: {
    type: String,
    required: true,
    validator: (v) => ['action', 'stacked', 'count'].includes(v),
  },
  count: { type: Number, default: 0 },
  disabled: { type: Boolean, default: false },
  ariaLabel: { type: String, default: '' },
})

defineEmits(['click'])
</script>
