<!--
  KinFlatCard — workhorse card with subtle border + resting shadow.
  @see /design-system/flat-card  (docs/design/COMPONENT_ROADMAP.md §2.1)
  Props: padding, interactive, as, href, to
  Slots: default, #header, #footer
-->
<script setup>
import { computed } from 'vue'
import { RouterLink } from 'vue-router'

// ── Props ────────────────────────────────────────────────────────────────────
const props = defineProps({
  /** Inner padding size. sm = 16px (p-4), md = 24px (p-6), lg = 32px (p-8). */
  padding: {
    type: String,
    default: 'md',
    validator: (v) => ['none', 'sm', 'md', 'lg'].includes(v),
  },
  /** Enables hover lift + cursor-pointer. Also auto-enabled when `to` or `href` is set. */
  interactive: {
    type: Boolean,
    default: false,
  },
  /** Root tag name when neither `to` nor `href` is provided. */
  as: {
    type: String,
    default: 'div',
  },
  /** When set, renders an <a> tag (overrides `as`). */
  href: {
    type: String,
    default: null,
  },
  /** When set, renders a <RouterLink> (overrides `href` and `as`). */
  to: {
    type: [String, Object],
    default: null,
  },
})

// ── Root element resolution ───────────────────────────────────────────────────
const tag = computed(() => {
  if (props.to)   return RouterLink
  if (props.href) return 'a'
  return props.as
})

// ── Extra attrs for native <a> / <button> ────────────────────────────────────
const extraAttrs = computed(() => {
  if (props.to)   return { to: props.to }
  if (props.href) return { href: props.href }
  return {}
})

// ── Whether the card should appear interactive ───────────────────────────────
const isInteractive = computed(() => props.interactive || !!props.to || !!props.href)

// ── Padding classes ──────────────────────────────────────────────────────────
const paddingClass = computed(() => ({
  none: '',
  sm:   'p-4',
  md:   'p-6',
  lg:   'p-8',
}[props.padding] ?? 'p-6'))

// Negative-margin horizontal bleed for header/footer separators.
// Must match the padding on each side so the border spans full card width.
const bleedClass = computed(() => ({
  none: '-mx-0',
  sm:   '-mx-4',
  md:   '-mx-6',
  lg:   '-mx-8',
}[props.padding] ?? '-mx-6'))

// Padding re-applied inside header/footer so content aligns with body content.
const headerFooterPaddingClass = computed(() => ({
  none: 'px-0',
  sm:   'px-4',
  md:   'px-6',
  lg:   'px-8',
}[props.padding] ?? 'px-6'))
</script>

<template>
  <component
    :is="tag"
    v-bind="extraAttrs"
    :class="[
      // ── Base card structure ───────────────────────────────────────────────
      'rounded-card',
      'bg-surface-raised',
      'border border-border-subtle',
      'shadow-resting',
      // ── Body padding (only when no header/footer slot is used directly) ──
      // We always apply padding to the root; header/footer use bleed to escape it.
      paddingClass,
      // ── Interactive lift ─────────────────────────────────────────────────
      isInteractive && 'kin-flat-card--interactive cursor-pointer',
      // ── focus ring for keyboard navigation ───────────────────────────────
      isInteractive && 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-accent-lavender-bold',
    ]"
  >
    <!-- ── Header slot ────────────────────────────────────────────────────── -->
    <!--
      The header bleeds to card edges by using negative horizontal margins
      equal to the card's own padding, then re-applies horizontal padding
      inside so text/content stays aligned.
    -->
    <div
      v-if="$slots.header"
      :class="[
        bleedClass,
        headerFooterPaddingClass,
        'pb-4 mb-4 border-b border-border-subtle',
        // Compensate for top padding lost by bleed — keep visual balance.
        '-mt-0',
      ]"
    >
      <slot name="header"></slot>
    </div>

    <!-- ── Default slot ───────────────────────────────────────────────────── -->
    <slot></slot>

    <!-- ── Footer slot ────────────────────────────────────────────────────── -->
    <div
      v-if="$slots.footer"
      :class="[
        bleedClass,
        headerFooterPaddingClass,
        'pt-4 mt-4 border-t border-border-subtle',
      ]"
    >
      <slot name="footer"></slot>
    </div>
  </component>
</template>

<style scoped>
/*
  ════════════════════════════════════════════════════════════════
  KinFlatCard — interactive hover/active states

  Why scoped CSS here (not pure Tailwind utilities)?
  • hover:shadow-hover and hover:-translate-y-[2px] are Tailwind
    utilities, but the transition shorthand referencing CSS vars
    is cleaner in scoped CSS to avoid a long class string.
  • active:translate-y-0 override is expressed here for clarity.
  • prefers-reduced-motion disables transform only; shadow change
    stays so hover state is still visually communicated.
  ════════════════════════════════════════════════════════════════
*/
.kin-flat-card--interactive {
  transition:
    transform   var(--duration-quick) var(--ease-out-soft),
    box-shadow  var(--duration-quick) var(--ease-out-soft);
}

.kin-flat-card--interactive:hover {
  box-shadow: var(--shadow-hover);
}

/* Dark mode: shadows read poorly on warm-charcoal, so also lift the surface
   (raised → overlay) and use the deeper elevated shadow. */
.dark .kin-flat-card--interactive:hover {
  background-color: rgb(var(--surface-overlay));
  box-shadow: var(--shadow-elevated);
}

@media (prefers-reduced-motion: no-preference) {
  .kin-flat-card--interactive:hover {
    transform: translateY(-1px);
  }

  .kin-flat-card--interactive:active {
    transform: translateY(0px);
    box-shadow: var(--shadow-resting);
  }

  .dark .kin-flat-card--interactive:active {
    background-color: rgb(var(--surface-raised));
    box-shadow: var(--shadow-resting);
  }
}

/*
  Reduced motion: keep shadow transition but kill transform.
  State changes (hover/active) still communicate via shadow alone.
*/
@media (prefers-reduced-motion: reduce) {
  .kin-flat-card--interactive {
    transition: box-shadow var(--duration-quick) var(--ease-out-soft);
  }
}
</style>
