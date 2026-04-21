<!--
  KinSkeleton — shimmer loading placeholder. Text / circle / pill / field / card / rect shapes.
  @see /design-system/skeleton  (docs/design/COMPONENT_ROADMAP.md §1.7)
  Props: shape, width, height, lines, rounded
-->
<script setup>
import { computed } from 'vue'

// ── Props ─────────────────────────────────────────────────────────────────────
const props = defineProps({
  /** Shape preset */
  shape: {
    type: String,
    default: 'text',
    validator: (v) => ['text', 'circle', 'pill', 'field', 'card', 'rect'].includes(v),
  },
  /** CSS width value (string) or px number. Applies to all shapes. */
  width: {
    type: [String, Number],
    default: null,
  },
  /** CSS height value (string) or px number. Only used for 'rect'. */
  height: {
    type: [String, Number],
    default: null,
  },
  /** Number of stacked text lines. Only used when shape='text'. */
  lines: {
    type: Number,
    default: 1,
  },
  /** Optional border-radius override (CSS value). */
  rounded: {
    type: String,
    default: null,
  },
  /** Optional shimmer delay in ms (for stagger effects in composed skeletons). */
  delay: {
    type: Number,
    default: 0,
  },
  /** Force shimmer animation regardless of prefers-reduced-motion. Demo/preview use only. */
  forceMotion: {
    type: Boolean,
    default: false,
  },
})

// ── Helpers ───────────────────────────────────────────────────────────────────
/** Coerce a prop value to a CSS string; null → use shape preset. */
function toCss(val) {
  if (val === null || val === undefined) return null
  return typeof val === 'number' ? `${val}px` : val
}

const cssWidth  = computed(() => toCss(props.width))
const cssHeight = computed(() => toCss(props.height))

// ── Per-shape preset styles ───────────────────────────────────────────────────
const shapeStyle = computed(() => {
  const s = props.shape
  const w = cssWidth.value
  const h = cssHeight.value
  const r = props.rounded

  switch (s) {
    case 'text':
      return {
        height:       '1em',
        width:        w ?? '100%',
        borderRadius: r ?? '4px',
      }

    case 'circle': {
      const size = w ?? '40px'
      return {
        width:        size,
        height:       size,
        borderRadius: r ?? '9999px',
        flexShrink:   '0',
      }
    }

    case 'pill':
      return {
        height:       '30px',
        width:        w ?? '100%',
        borderRadius: r ?? '9999px',
      }

    case 'field':
      return {
        height:       '40px',
        width:        w ?? '100%',
        borderRadius: r ?? 'var(--radius-field)',
      }

    case 'card':
      return {
        height:       '120px',
        width:        w ?? '100%',
        borderRadius: r ?? 'var(--radius-card)',
      }

    case 'rect':
      return {
        height:       h ?? '64px',
        width:        w ?? '100%',
        borderRadius: r ?? '6px',
      }

    default:
      return {}
  }
})

// For multi-line text, build an array of width values that create a
// natural paragraph feel (last line is ~70% to avoid a perfectly flush end).
const lineWidths = computed(() => {
  const count = Math.max(1, Math.round(props.lines))
  if (count === 1) return ['100%']

  const widths = []
  for (let i = 0; i < count; i++) {
    if (i === count - 1) {
      // Last line: 65–75% — uses deterministic variation based on position
      widths.push('70%')
    } else if (i % 3 === 1) {
      widths.push('88%')
    } else {
      widths.push('100%')
    }
  }
  return widths
})

const isMultiLine = computed(() => props.shape === 'text' && props.lines > 1)

// Delay style — merged into shapeStyle via spread at the callsite.
const delayStyle = computed(() => (
  props.delay ? { animationDelay: `${props.delay}ms` } : {}
))
</script>

<template>
  <!-- Multi-line text: wrapper holds the aria roles; inner lines are presentational -->
  <div
    v-if="isMultiLine"
    role="status"
    aria-busy="true"
    aria-live="polite"
    class="flex flex-col gap-2"
  >
    <span class="sr-only">Loading…</span>
    <div
      v-for="(lw, i) in lineWidths"
      :key="i"
      class="kin-skeleton"
      :class="{ 'kin-skeleton--force-motion': forceMotion }"
      aria-hidden="true"
      :style="{ ...shapeStyle, ...delayStyle, width: cssWidth ?? lw }"
    />
  </div>

  <!-- Single element: one shimmer block -->
  <div
    v-else
    class="kin-skeleton"
    :class="{ 'kin-skeleton--force-motion': forceMotion }"
    role="status"
    aria-busy="true"
    aria-live="polite"
    :style="{ ...shapeStyle, ...delayStyle }"
  >
    <span class="sr-only">Loading…</span>
  </div>
</template>

<style scoped>
/*
  ══════════════════════════════════════════════════════════
  KinSkeleton shimmer

  Technique: background-position slides the gradient left → right
  over 1.8s. The gradient is a translucent white/black strip
  laid on top of the surface-sunken token color — so the shimmer
  adapts to whatever theme is active without mode-specific classes.

  Shimmer chrome colors (hardcoded — minor exception per spec):
  • Light: rgba(0,0,0,0.04)   — barely-visible dark strip
  • Dark:  rgba(255,255,255,0.06) — barely-visible light strip
  Both are small enough to read as "a slightly lighter version of
  the surface" rather than a distinct color, which is the goal.
  ══════════════════════════════════════════════════════════
*/
@keyframes kin-shimmer {
  from { background-position: -200% 0; }
  to   { background-position: 200% 0; }
}

/* Skeleton base + shimmer — both tokens flip via :root/.dark in tokens.css */
.kin-skeleton {
  display: block;
  background-color: rgb(var(--skeleton-base));
  background-image: linear-gradient(
    90deg,
    rgb(var(--skeleton-base)) 0%,
    rgb(var(--skeleton-shimmer)) 50%,
    rgb(var(--skeleton-base)) 100%
  );
  background-size: 200% 100%;
  background-repeat: no-repeat;
  background-position: -200% 0;
  animation: kin-shimmer 1.8s linear infinite;
}

/* Reduced-motion: static muted fill, no animation.
   `forceMotion` prop opts out — for design-system demos so the shimmer
   is visible even when the viewer has reduced-motion enabled OS-wide. */
@media (prefers-reduced-motion: reduce) {
  .kin-skeleton:not(.kin-skeleton--force-motion) {
    animation: none;
    background-image: none;
  }
}
</style>
