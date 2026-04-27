<!--
  KinProgressArc — standalone arc/ring gauge (determinate only).
  @see /design-system/progress  (docs/design/COMPONENT_ROADMAP.md §1.6)
  Props: value, size, thickness, color, label, showValue
  Slots: #center — overrides label/showValue when provided
-->
<script setup>
import { computed } from 'vue'

// ── Props ─────────────────────────────────────────────────────────────────────
const props = defineProps({
  /** Progress value. Accepts 0–1 or 0–100; normalized internally. */
  value: { type: Number, default: 0 },

  /** Pixel diameter of the outer SVG square. */
  size: { type: Number, default: 96 },

  /** Stroke width in SVG viewBox units (viewBox is 100×100). */
  thickness: { type: Number, default: 8 },

  /** Color family. Accents: lavender/peach/mint/sun/neutral. Status: success/pending/paused/failed/info/warning. */
  color: {
    type: String,
    default: 'lavender',
    validator: (v) => [
      'lavender', 'peach', 'mint', 'sun', 'neutral',
      'success', 'pending', 'paused', 'failed', 'info', 'warning',
    ].includes(v),
  },

  /** Text rendered centered inside the ring (overridden by #center slot). */
  label: { type: String, default: '' },

  /** Renders the numeric percentage as the centered label (overridden by #center slot). */
  showValue: { type: Boolean, default: false },
})

// ── Normalized value ──────────────────────────────────────────────────────────
const pct = computed(() => {
  const raw = props.value > 1 ? props.value / 100 : props.value
  return Math.min(1, Math.max(0, raw))
})

const pctInt = computed(() => Math.round(pct.value * 100))

// ── SVG arc math (viewBox 100×100) ────────────────────────────────────────────
// r is calculated so the stroke just fits inside the viewBox.
// half-stroke inset from each edge: (100 - thickness) / 2
const arcR = computed(() => (100 - props.thickness) / 2)
const arcCirc = computed(() => 2 * Math.PI * arcR.value)
const arcOffset = computed(() => arcCirc.value * (1 - pct.value))

// At 100% use butt linecap so the arc closes into a seamless ring.
const linecap = computed(() => pct.value >= 1 ? 'butt' : 'round')

// ── Color → CSS variable names ────────────────────────────────────────────────
// Accent: track=-soft, arc=-bold. Status: track=surface-sunken, arc=status-*.
const TRACK_VAR = {
  lavender: '--accent-lavender-soft',
  peach:    '--accent-peach-soft',
  mint:     '--accent-mint-soft',
  sun:      '--accent-sun-soft',
  neutral:  '--surface-sunken',
  success:  '--surface-sunken',
  pending:  '--surface-sunken',
  paused:   '--surface-sunken',
  failed:   '--surface-sunken',
  info:     '--surface-sunken',
  warning:  '--surface-sunken',
}
const ARC_VAR = {
  lavender: '--accent-lavender-bold',
  peach:    '--accent-peach-bold',
  mint:     '--accent-mint-bold',
  sun:      '--accent-sun-bold',
  neutral:  '--ink-tertiary',
  success:  '--status-success',
  pending:  '--status-pending',
  paused:   '--status-paused',
  failed:   '--status-failed',
  info:     '--status-info',
  warning:  '--status-warning',
}

const trackVar = computed(() => TRACK_VAR[props.color] ?? TRACK_VAR.lavender)
const arcVar   = computed(() => ARC_VAR[props.color]   ?? ARC_VAR.lavender)

const trackColor = computed(() => `rgb(var(${trackVar.value}))`)
const arcColor   = computed(() => `rgb(var(${arcVar.value}))`)

// ── Center label font size — scales with diameter ────────────────────────────
// Rough heuristic: ~28% of diameter for the number, ~18% for sub-label.
const valueFontSize  = computed(() => Math.round(props.size * 0.28) + 'px')
const labelFontSize  = computed(() => Math.round(props.size * 0.12) + 'px')
</script>

<template>
  <div
    class="relative inline-flex items-center justify-center flex-shrink-0"
    :style="{ width: size + 'px', height: size + 'px' }"
    role="progressbar"
    :aria-valuemin="0"
    :aria-valuemax="100"
    :aria-valuenow="pctInt"
    :aria-label="label || undefined"
  >
    <!-- SVG ring: rotated -90deg so progress starts at 12 o'clock -->
    <svg
      class="absolute inset-0 w-full h-full"
      viewBox="0 0 100 100"
      style="transform: rotate(-90deg)"
      aria-hidden="true"
    >
      <!-- Background track -->
      <circle
        cx="50" cy="50"
        :r="arcR"
        fill="none"
        :stroke="trackColor"
        :stroke-width="thickness"
      />
      <!-- Progress arc -->
      <circle
        cx="50" cy="50"
        :r="arcR"
        fill="none"
        :stroke="arcColor"
        :stroke-width="thickness"
        :stroke-dasharray="arcCirc"
        :stroke-dashoffset="arcOffset"
        :stroke-linecap="linecap"
        class="kin-arc-progress"
      />
    </svg>

    <!-- Center content: slot > showValue > label -->
    <div
      class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none"
    >
      <slot name="center">
        <!-- showValue: large number with % suffix -->
        <template v-if="showValue">
          <span
            class="font-semibold leading-none text-ink-primary tabular-nums"
            :style="{ fontSize: valueFontSize }"
          >{{ pctInt }}<span :style="{ fontSize: labelFontSize }">%</span></span>
          <span
            v-if="label"
            class="text-ink-tertiary leading-none mt-1"
            :style="{ fontSize: labelFontSize }"
          >{{ label }}</span>
        </template>
        <!-- label only -->
        <span
          v-else-if="label"
          class="text-ink-secondary text-center px-2 leading-tight"
          :style="{ fontSize: labelFontSize }"
        >{{ label }}</span>
      </slot>
    </div>
  </div>
</template>

<style scoped>
/* ─── Arc dashoffset transition ──────────────────────────────────────────── */
.kin-arc-progress {
  transition: stroke-dashoffset var(--duration-deliberate) var(--ease-out-soft);
}

@media (prefers-reduced-motion: reduce) {
  .kin-arc-progress {
    transition: none;
  }
}
</style>
