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
   * 'gold' is Kinhold's brand accent, reserved for food/meal/shopping
   * surfaces that use brand-gold as their semantic marker.
   */
  color: {
    type: String,
    default: 'neutral',
    validator: v => ['lavender', 'peach', 'mint', 'sun', 'gold', 'neutral'].includes(v),
  },

  /**
   * Escape-hatch for arbitrary brand / category colors that don't map onto
   * the four accent families. Pass a CSS color string (`#7d57a8`, `rgb(...)`).
   * When present, overrides the `color` prop:
   *   - active state: solid customColor background + white text
   *   - inactive state: small dot of customColor + neutral surface bg
   * Use sparingly — accent families are preferred for design consistency.
   */
  customColor: {
    type: String,
    default: null,
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
   * How the active state renders on category + filter variants.
   * - 'solid' (default): color-soft inactive, color-bold active. Every
   *   option reads as part of one coloured group.
   * - 'soft': neutral inactive, color-soft active. "Picked / not picked"
   *   for multi-select sets where most options are usually on.
   * - 'bold': neutral inactive, color-bold active. Toggle grammar for
   *   single-select sets: only the selected pill pops.
   *
   * Ignored by variant="status" (status always renders as a passive label).
   */
  activeTone: {
    type: String,
    default: 'solid',
    validator: v => ['solid', 'soft', 'bold'].includes(v),
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

  /**
   * When false, the chip renders as a non-interactive <span> with no
   * cursor, hover, press, or focus-ring treatment: a passive label.
   * Default true preserves the existing clickable-button behaviour.
   * No effect on variant="status" (always a span) or disabled (also span).
   */
  interactive: {
    type: Boolean,
    default: true,
  },

  /**
   * Status variant only. When true (default), renders a leading dot
   * coloured by the status token. When false, hides the dot and
   * applies a soft-tinted background + bold text in the status colour,
   * so the chip reads as a label-only state marker (e.g. "3 left",
   * "Expired", "Children Only" on reward cards).
   */
  showDot: {
    type: Boolean,
    default: true,
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
  if (!props.interactive) return 'span'
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
  // Brand-gold: single-token family. The soft and border variants synthesise
  // alpha from the base brand-gold via Tailwind's slash-opacity utilities.
  gold: {
    softBg:  'bg-brand-gold/10',
    boldBg:  'bg-brand-gold',
    boldText: 'text-brand-gold',
    border:  'border-brand-gold/40',
  },
}

// Static map so Tailwind JIT picks up the status utilities (template-literal
// class names are invisible to the content scanner).
const STATUS_LABEL_CLASSES = {
  success: 'bg-status-success/10 text-status-success',
  pending: 'bg-status-pending/10 text-status-pending',
  paused:  'bg-status-paused/10  text-status-paused',
  failed:  'bg-status-failed/10  text-status-failed',
  info:    'bg-status-info/10    text-status-info',
  warning: 'bg-status-warning/10 text-status-warning',
}

// Classes for category/filter variants (non-status)
const chipColorClasses = computed(() => {
  if (props.variant === 'status') {
    // Dot variant: no colour on the chip body (the dot carries it).
    if (props.showDot) return ''
    // Label-only variant: soft bg + bold text in the status colour.
    return STATUS_LABEL_CLASSES[props.status] || 'bg-surface-sunken text-ink-tertiary'
  }

  const isNeutral = props.color === 'neutral'
  const isSoftTone = props.activeTone === 'soft'
  const isBoldTone = props.activeTone === 'bold'

  // Soft + bold tones share a neutral inactive state and diverge only on the
  // active side. Only the active-vs-inactive split is written out below.
  if (isSoftTone || isBoldTone) {
    if (!props.active) {
      return 'bg-surface-sunken border border-border-subtle text-ink-tertiary'
    }
    if (isSoftTone) {
      if (props.customColor) {
        // chipCustomStyle supplies bg + border and --kin-tag-color for the text.
        return 'border kin-tag-ink'
      }
      if (isNeutral) {
        return 'bg-ink-primary/10 border border-ink-primary/20 text-ink-primary'
      }
      const a = accentMap[props.color]
      return `${a.softBg} border ${a.border} ${a.boldText}`
    }
    // Bold tone: filled with color-bold + inverse text (toggle grammar).
    if (props.customColor) {
      // chipCustomStyle below fills the bg with customColor; text stays white.
      return 'border text-white'
    }
    if (isNeutral) {
      return 'bg-ink-primary border border-ink-primary text-ink-inverse'
    }
    const a = accentMap[props.color]
    return `${a.boldBg} border ${a.border} text-ink-inverse`
  }

  // customColor short-circuits the accent system — base classes only;
  // background/border/text get applied via `chipCustomStyle` below.
  if (props.customColor) {
    if (props.active) {
      return 'border text-white'
    }
    return 'bg-surface-sunken border border-border-subtle text-ink-secondary'
  }

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

// Inline-style override when customColor is set. Active fills with the color;
// inactive shows a small dot via the `#leading` slot fallback we render below.
// Soft tone synthesises alpha via color-mix so the active state reads as a
// tinted label rather than a filled badge.
const chipCustomStyle = computed(() => {
  if (!props.customColor) return null
  if (props.active) {
    if (props.activeTone === 'soft') {
      return {
        backgroundColor: `color-mix(in srgb, ${props.customColor} 10%, transparent)`,
        borderColor: `color-mix(in srgb, ${props.customColor} 40%, transparent)`,
        '--kin-tag-color': props.customColor,
      }
    }
    return {
      backgroundColor: props.customColor,
      borderColor: props.customColor,
    }
  }
  return null
})

const showCustomDot = computed(() =>
  !!props.customColor && !props.active && props.variant !== 'status'
)

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
  if (!props.interactive) return ''
  return 'kin-pressable cursor-pointer transition-[filter,background-color,border-color,transform] duration-quick ease-out-soft hover:brightness-95 dark:hover:brightness-110 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent-lavender-bold focus-visible:ring-offset-1'
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
    :style="chipCustomStyle"
    v-bind="rootTag === 'button' ? {
      type: 'button',
      disabled: disabled || undefined,
      'aria-pressed': variant === 'filter' ? String(active) : undefined,
    } : {}"
  >
    <!-- Leading slot (icon before label) -->
    <slot name="leading"></slot>

    <!-- Custom-color dot (inactive state) when no leading slot is provided -->
    <span
      v-if="showCustomDot && !$slots.leading"
      :class="['rounded-full flex-shrink-0', dotSizeClass]"
      :style="{ backgroundColor: customColor }"
      aria-hidden="true"
    ></span>

    <!-- Status dot: only rendered for status variant when showDot is true -->
    <span
      v-if="variant === 'status' && showDot"
      :class="['rounded-full flex-shrink-0', dotSizeClass, statusDotClass]"
      aria-hidden="true"
    ></span>

    <!-- Default slot (label text) -->
    <slot></slot>

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
