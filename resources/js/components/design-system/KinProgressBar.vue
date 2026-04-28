<!--
  KinProgressBar — horizontal pill progress bar (determinate + indeterminate).
  @see /design-system/progress  (docs/design/COMPONENT_ROADMAP.md §1.6)
  Props: value, color, size, indeterminate, label, showValue
-->
<script setup>
import { computed } from 'vue'

// ── Props ─────────────────────────────────────────────────────────────────────
const props = defineProps({
  /** Progress value. Accepts 0–1 or 0–100; normalized internally to 0–1. */
  value: { type: Number, default: 0 },

  /** Color family. Accents: lavender/peach/mint/sun/neutral. Status: success/pending/paused/failed/info/warning. */
  color: {
    type: String,
    default: 'lavender',
    validator: (v) => [
      'lavender', 'peach', 'mint', 'sun', 'neutral',
      'success', 'pending', 'paused', 'failed', 'info', 'warning',
    ].includes(v),
  },

  /** Track height. sm = 4px, md = 8px, lg = 12px. */
  size: {
    type: String,
    default: 'md',
    validator: (v) => ['sm', 'md', 'lg'].includes(v),
  },

  /** Animated shimmer; ignores value. */
  indeterminate: { type: Boolean, default: false },

  /** Optional label text rendered above-left the bar. */
  label: { type: String, default: '' },

  /** Renders "65%" beside the label (or below if no label). */
  showValue: { type: Boolean, default: false },
})

// ── Normalized value ──────────────────────────────────────────────────────────
// Accepts 0–1 or 0–100. Clamps to [0, 1].
const pct = computed(() => {
  if (props.indeterminate) return 0
  const raw = props.value > 1 ? props.value / 100 : props.value
  return Math.min(1, Math.max(0, raw))
})

const pctDisplay = computed(() => Math.round(pct.value * 100) + '%')

// ── Track height ──────────────────────────────────────────────────────────────
const HEIGHT = { sm: '4px', md: '8px', lg: '12px' }
const trackHeight = computed(() => HEIGHT[props.size] ?? HEIGHT.md)

// ── Color → CSS variable names ────────────────────────────────────────────────
// Accent colors: track=-soft, fill=-bold.
// Status colors: track=surface-sunken (neutral), fill=status-*.
// 'neutral' maps to surface-sunken track + ink-tertiary fill.
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
const FILL_VAR = {
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
const fillVar  = computed(() => FILL_VAR[props.color]  ?? FILL_VAR.lavender)

// ── Inline styles ─────────────────────────────────────────────────────────────
const trackStyle = computed(() => ({
  height: trackHeight.value,
  background: `rgb(var(${trackVar.value}))`,
}))

const fillStyle = computed(() => ({
  width: props.indeterminate ? '40%' : pct.value * 100 + '%',
  height: '100%',
  background: `rgb(var(${fillVar.value}))`,
}))
</script>

<template>
  <div
    class="w-full"
    role="progressbar"
    :aria-valuemin="0"
    :aria-valuemax="100"
    :aria-valuenow="indeterminate ? undefined : Math.round(pct * 100)"
    :aria-label="label || undefined"
    :aria-busy="indeterminate || undefined"
  >
    <!-- Label row -->
    <div
      v-if="label || showValue"
      class="flex items-baseline justify-between mb-1.5"
    >
      <span
        v-if="label"
        class="text-body-sm font-medium text-ink-primary"
      >{{ label }}</span>
      <span
        v-if="showValue && !indeterminate"
        class="text-caption text-ink-secondary tabular-nums"
        :class="!label && 'ml-auto'"
      >{{ pctDisplay }}</span>
    </div>

    <!-- Track -->
    <div
      class="w-full rounded-pill overflow-hidden"
      :style="trackStyle"
    >
      <!-- Determinate fill -->
      <div
        v-if="!indeterminate"
        class="rounded-pill kin-bar-fill"
        :style="fillStyle"
      ></div>
      <!-- Indeterminate shimmer -->
      <div
        v-else
        class="rounded-pill kin-bar-indeterminate"
        :style="fillStyle"
      ></div>
    </div>
  </div>
</template>

<style scoped>
/* ─── Determinate fill — smooth width transition ─────────────────────────── */
.kin-bar-fill {
  transition: width var(--duration-deliberate) var(--ease-out-soft);
}

/* ─── Indeterminate shimmer ──────────────────────────────────────────────── */
@keyframes kin-shimmer {
  from { transform: translateX(-100%); }
  to   { transform: translateX(250%); }
}

.kin-bar-indeterminate {
  width: 40%;
  animation: kin-shimmer 1.5s linear infinite;
}

/* Reduced motion: stop animation, park fill at 40% center */
@media (prefers-reduced-motion: reduce) {
  .kin-bar-fill {
    transition: none;
  }
  .kin-bar-indeterminate {
    animation: none;
    margin-left: 30%;
  }
}
</style>
