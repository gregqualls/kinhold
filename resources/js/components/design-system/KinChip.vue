<!--
  KinChip — pill-shaped chip/tag/status indicator. Categories, filters, statuses.
  @see /design-system/chip  (docs/design/COMPONENT_ROADMAP.md §1.3)
  Props: variant, color, status, size, removable, active, disabled
  Slots: default, #leading
  Emits: remove
-->
<script setup>
import { computed } from 'vue'
import { XMarkIcon } from '@heroicons/vue/24/outline'

// ── Props ────────────────────────────────────────────────────────────────────

const props = defineProps({
  /**
   * 'category' — soft-tint bg + bold text, fills when active
   * 'filter'   — outlined by default, fills when active, optionally removable
   * 'status'   — tiny dot + label, uses status tokens
   */
  variant: {
    type: String,
    default: 'category',
    validator: v => ['category', 'filter', 'status'].includes(v),
  },

  /**
   * Accent color for category/filter variants.
   * 'neutral' uses border-subtle / ink-secondary (off state).
   */
  color: {
    type: String,
    default: 'neutral',
    validator: v => ['lavender', 'peach', 'mint', 'sun', 'neutral'].includes(v),
  },

  /**
   * Status key — only used when variant='status'.
   */
  status: {
    type: String,
    default: null,
    validator: v => v === null || ['success', 'pending', 'paused', 'failed', 'info', 'warning'].includes(v),
  },

  /** Size of the chip pill. */
  size: {
    type: String,
    default: 'md',
    validator: v => ['sm', 'md'].includes(v),
  },

  /**
   * Adds an X remove button inside the chip.
   * Only meaningful on variant='filter'.
   */
  removable: {
    type: Boolean,
    default: false,
  },

  /**
   * Filled/active state for category + filter variants.
   * When true: background fills with bold color, text flips to ink-inverse.
   */
  active: {
    type: Boolean,
    default: false,
  },

  disabled: {
    type: Boolean,
    default: false,
  },
})

// ── Emits ────────────────────────────────────────────────────────────────────

const emit = defineEmits(['remove'])

// ── Root tag decision ────────────────────────────────────────────────────────
// status variant and disabled chips are non-interactive → use <span>.
// category/filter are interactive buttons.

const rootTag = computed(() => {
  if (props.variant === 'status') return 'span'
  if (props.disabled) return 'span'
  return 'button'
})

// ── Size classes ─────────────────────────────────────────────────────────────

const sizeClasses = computed(() =>
  props.size === 'sm'
    ? 'h-6 px-2.5 text-[11px] gap-1'
    : 'h-7 px-3 text-[12px] gap-1.5'
)

const dotSizeClass = computed(() =>
  props.size === 'sm' ? 'w-1.5 h-1.5' : 'w-2 h-2'
)

// ── Color classes — category / filter variants ───────────────────────────────
//
// Default (inactive) state:
//   category: soft bg + bold text + border matching bold color
//   filter:   white/sunken bg + secondary text + subtle border
//
// Active state (active=true):
//   Both: bold bg + inverse text + bold border

const accentMap = {
  lavender: {
    softBg:  'bg-accent-lavender-soft',
    boldBg:  'bg-accent-lavender-bold',
    boldText: 'text-accent-lavender-bold',
    border:  'border-accent-lavender-bold',
  },
  peach: {
    softBg:  'bg-accent-peach-soft',
    boldBg:  'bg-accent-peach-bold',
    boldText: 'text-accent-peach-bold',
    border:  'border-accent-peach-bold',
  },
  mint: {
    softBg:  'bg-accent-mint-soft',
    boldBg:  'bg-accent-mint-bold',
    boldText: 'text-accent-mint-bold',
    border:  'border-accent-mint-bold',
  },
  sun: {
    softBg:  'bg-accent-sun-soft',
    boldBg:  'bg-accent-sun-bold',
    boldText: 'text-accent-sun-bold',
    border:  'border-accent-sun-bold',
  },
}

// Classes for category/filter variants (non-status)
const chipColorClasses = computed(() => {
  if (props.variant === 'status') return ''

  const isNeutral = props.color === 'neutral'

  if (props.active) {
    // Filled active state
    if (isNeutral) {
      return 'bg-ink-primary border border-ink-primary text-ink-inverse'
    }
    const a = accentMap[props.color]
    return `${a.boldBg} border ${a.border} text-ink-inverse`
  }

  // Default / inactive state
  if (props.variant === 'filter') {
    // Filter: outlined, soft neutral bg when inactive
    if (isNeutral) {
      return 'bg-surface-raised border border-border-subtle text-ink-secondary'
    }
    const a = accentMap[props.color]
    return `${a.softBg} border ${a.border} ${a.boldText}`
  }

  // Category: always shows accent tint
  if (isNeutral) {
    return 'bg-surface-sunken border border-border-subtle text-ink-secondary'
  }
  const a = accentMap[props.color]
  return `${a.softBg} border ${a.border} ${a.boldText}`
})

// ── Status dot color class ────────────────────────────────────────────────────

const statusDotClass = computed(() => {
  if (!props.status) return 'bg-ink-tertiary'
  return `bg-status-${props.status}`
})

// ── Remove button padding adjustment ─────────────────────────────────────────
// When removable, shrink right padding since X button provides visual padding

const removablePaddingClass = computed(() =>
  props.removable ? (props.size === 'sm' ? 'pr-0.5' : 'pr-1') : ''
)

// ── Interaction classes ───────────────────────────────────────────────────────

const interactionClasses = computed(() => {
  if (props.disabled) return 'opacity-40 cursor-not-allowed'
  if (props.variant === 'status') return ''
  return 'cursor-pointer transition-[filter,background-color,border-color] duration-quick ease-out-soft hover:brightness-95 dark:hover:brightness-110 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent-lavender-bold focus-visible:ring-offset-1'
})
</script>

<template>
  <component
    :is="rootTag"
    class="inline-flex items-center rounded-pill font-medium select-none"
    :class="[
      sizeClasses,
      chipColorClasses,
      removablePaddingClass,
      interactionClasses,
    ]"
    v-bind="rootTag === 'button' ? { type: 'button', disabled: disabled || undefined } : {}"
  >
    <!-- Leading slot (icon before label) -->
    <slot name="leading" />

    <!-- Status dot — only rendered for status variant -->
    <span
      v-if="variant === 'status'"
      :class="['rounded-full flex-shrink-0', dotSizeClass, statusDotClass]"
      aria-hidden="true"
    />

    <!-- Default slot (label text) -->
    <slot />

    <!-- Remove button — only rendered when removable=true -->
    <button
      v-if="removable && !disabled"
      type="button"
      aria-label="Remove"
      class="ml-0.5 flex items-center justify-center rounded-full flex-shrink-0
             transition-colors duration-instant
             hover:bg-black/10 dark:hover:bg-white/10
             focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-current"
      :class="size === 'sm' ? 'w-4 h-4' : 'w-5 h-5'"
      @click.stop="emit('remove')"
    >
      <XMarkIcon :class="size === 'sm' ? 'w-2.5 h-2.5' : 'w-3 h-3'" />
    </button>
  </component>
</template>
