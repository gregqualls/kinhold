<!--
  KinSelectCard: a tappable, bordered tile with a check badge in the
  top-right when selected. Wraps the .kin-tile DS rule (border + ring +
  sunken-bg selected state) with the shared .kin-pressable press feel.

  Use for pickers where each option is a card (AI mode, color theme,
  reward type, child mode) rather than a row. For list rows prefer
  .kin-pressable + [data-selected] directly.

  Props: selected, showCheck, padding, align
  Slot:  default (icon, title, description, anything)
  Emits: click
-->
<script setup>
import { computed } from 'vue'

const props = defineProps({
  /** True = render the lavender border + ring + sunken bg + check badge. */
  selected: { type: Boolean, default: false },

  /**
   * Show the lavender check badge in the top-right when selected.
   * Default true; disable when the card already has its own selected
   * affordance (e.g. a colour swatch strip).
   */
  showCheck: { type: Boolean, default: true },

  /** sm = p-3 (dense pickers); md = p-4 (standard). */
  padding: {
    type: String,
    default: 'md',
    validator: (v) => ['sm', 'md'].includes(v),
  },

  /** Content alignment inside the card. */
  align: {
    type: String,
    default: 'start',
    validator: (v) => ['start', 'center'].includes(v),
  },
})

const emit = defineEmits(['click'])

const paddingClass = computed(() => (props.padding === 'sm' ? 'p-3' : 'p-4'))
const alignClass = computed(() => (props.align === 'center' ? 'items-center text-center' : 'items-start text-left'))
</script>

<template>
  <button
    type="button"
    class="kin-pressable kin-tile relative flex flex-col rounded-xl transition-[background-color,border-color,box-shadow,transform] duration-quick ease-out-soft"
    :class="[paddingClass, alignClass]"
    :data-selected="selected || null"
    :aria-pressed="String(selected)"
    @click="emit('click', $event)"
  >
    <span
      v-if="selected && showCheck"
      class="absolute top-2 right-2 w-5 h-5 rounded-full bg-accent-lavender-bold flex items-center justify-center"
      aria-hidden="true"
    >
      <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
      </svg>
    </span>
    <slot></slot>
  </button>
</template>
