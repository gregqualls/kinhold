<!--
  KinStatTile — hero-number widget for data-heavy surfaces.
  @see /design-system/stat-tile  (docs/design/COMPONENT_ROADMAP.md §5.1)
  Props: label, value, accentColor, delta, deltaUp, chartData, chartType, ranges, range (v-model:range)
  Slots: (none)
  Emits: update:range

  Every layer below the hero number is opt-in:
    - label (string, always rendered)
    - delta chip (when `delta` prop is set)
    - hero number (required — required prop: `value`)
    - chart (when `chartData` is set — `chartType: line | bars`)
    - range filter (when `ranges` is set — v-model:range picks active)

  Hero number uses container-query units so font-size scales with the card,
  not the viewport (extraction-phase gotcha #4 — viewport vw breaks on narrow
  cards inside wide viewports). Requires `container-type: inline-size` which
  is applied on the card root.
-->
<script setup>
import { computed } from 'vue'
import { ArrowUpRightIcon, ArrowDownRightIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  /** Kicker label rendered above the hero number. */
  label: { type: String, required: true },
  /** The hero number. String or number — rendered as-is; format it yourself. */
  value: { type: [String, Number], required: true },
  /** Accent color family — drives hero number + chart + active filter color. */
  accentColor: {
    type: String,
    default: 'lavender',
    validator: (v) => ['lavender', 'peach', 'mint', 'sun'].includes(v),
  },
  /** Delta chip text (e.g. "+86" or "+12%"). When empty, chip is hidden. */
  delta: { type: String, default: '' },
  /** Direction of the delta — up (success green) or down (failed rose). */
  deltaUp: { type: Boolean, default: true },
  /** Optional 7-point chart data (0–1 normalized). */
  chartData: { type: Array, default: null },
  /** Chart type: 'line' (with fill) or 'bars'. */
  chartType: {
    type: String,
    default: 'line',
    validator: (v) => ['line', 'bars'].includes(v),
  },
  /** Optional time-range filter options (e.g. ['D','W','M','Y']). */
  ranges: { type: Array, default: null },
  /** Active range key (v-model:range). */
  range: { type: String, default: null },
})

defineEmits(['update:range'])

// ── Chart math ───────────────────────────────────────────────────────────────
const CHART_W = 140
const CHART_H = 40

const polylinePoints = computed(() => {
  if (!props.chartData || props.chartType !== 'line') return ''
  const n = props.chartData.length
  return props.chartData
    .map((v, i) => `${(i / (n - 1)) * CHART_W},${CHART_H - v * CHART_H}`)
    .join(' ')
})

const polyFillPath = computed(() => polylinePoints.value + ` ${CHART_W},${CHART_H} 0,${CHART_H}`)

const lastPointY = computed(() => {
  if (!props.chartData) return 0
  return CHART_H - props.chartData[props.chartData.length - 1] * CHART_H
})

const bars = computed(() => {
  if (!props.chartData || props.chartType !== 'bars') return []
  const n = props.chartData.length
  const barW = Math.floor(CHART_W / n) - 3
  return props.chartData.map((v, i) => ({
    x: i * (CHART_W / n) + 1,
    y: CHART_H - v * CHART_H,
    width: barW,
    height: v * CHART_H,
    isLast: i === n - 1,
  }))
})
</script>

<template>
  <div
    class="kin-stat-tile rounded-card border border-border-subtle bg-surface-raised flex flex-col gap-3 py-6 px-6"
    :class="`kin-stat-tile--${accentColor}`"
  >
    <!-- Row: label + optional range filter -->
    <div class="flex items-center justify-between gap-2">
      <p class="text-[11px] font-semibold uppercase tracking-widest text-ink-tertiary">{{ label }}</p>
      <div
        v-if="ranges"
        class="kin-stat-tile__ranges flex items-center rounded-lg overflow-hidden border border-border-subtle bg-surface-sunken text-[10px] font-medium"
      >
        <button
          v-for="r in ranges"
          :key="r"
          type="button"
          class="kin-stat-tile__range-btn px-1.5 py-0.5 transition-colors"
          :class="range === r && 'kin-stat-tile__range-btn--active'"
          @click="$emit('update:range', r)"
        >{{ r }}</button>
      </div>
    </div>

    <!-- Delta chip -->
    <div v-if="delta" class="flex items-center gap-2">
      <span
        class="kin-stat-tile__delta inline-flex items-center gap-1 h-6 px-2.5 rounded-full text-[11px] font-medium"
        :class="deltaUp ? 'kin-stat-tile__delta--up' : 'kin-stat-tile__delta--down'"
      >
        <ArrowUpRightIcon   v-if="deltaUp"  class="w-3 h-3" />
        <ArrowDownRightIcon v-else           class="w-3 h-3" />
        {{ delta }}
      </span>
    </div>

    <!-- Hero number — container-query-scaled via cqw units -->
    <p class="kin-stat-tile__value leading-none font-semibold tracking-tighter">{{ value }}</p>

    <!-- Optional chart -->
    <svg
      v-if="chartData"
      viewBox="0 0 140 40"
      class="kin-stat-tile__chart w-full mt-1"
      style="height: 36px;"
      aria-hidden="true"
    >
      <!-- Line chart: fill under + stroke on top + end-point circle -->
      <template v-if="chartType === 'line'">
        <polyline
          :points="polyFillPath"
          class="kin-stat-tile__chart-fill"
          stroke="none"
          opacity="0.7"
        />
        <polyline
          :points="polylinePoints"
          class="kin-stat-tile__chart-stroke"
          stroke-width="2"
          stroke-linejoin="round"
          stroke-linecap="round"
          fill="none"
        />
        <circle
          cx="140"
          :cy="lastPointY"
          r="3"
          class="kin-stat-tile__chart-dot"
        />
      </template>
      <!-- Bar chart: rounded bars, last bar bold -->
      <template v-else-if="chartType === 'bars'">
        <rect
          v-for="(bar, i) in bars"
          :key="i"
          :x="bar.x"
          :y="bar.y"
          :width="bar.width"
          :height="bar.height"
          rx="2"
          :class="bar.isLast ? 'kin-stat-tile__bar--last' : 'kin-stat-tile__bar'"
        />
      </template>
    </svg>
  </div>
</template>

<style scoped>
.kin-stat-tile {
  box-shadow: var(--shadow-resting);
  container-type: inline-size;  /* hero number uses cqw units */
}

/* Hero number — container-query scaled.
   30cqw at card width ≈ 200px → 60px, scales up with container. */
.kin-stat-tile__value {
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: clamp(2rem, 30cqw, 6.5rem);
  letter-spacing: -0.02em;
}

/* Accent color drives hero number + chart + active filter. */
.kin-stat-tile--lavender .kin-stat-tile__value,
.kin-stat-tile--lavender .kin-stat-tile__chart-fill   { color: rgb(var(--accent-lavender-bold)); fill: rgb(var(--accent-lavender-soft)); }
.kin-stat-tile--lavender .kin-stat-tile__chart-stroke { stroke: rgb(var(--accent-lavender-bold)); }
.kin-stat-tile--lavender .kin-stat-tile__chart-dot    { fill: rgb(var(--accent-lavender-bold)); }
.kin-stat-tile--lavender .kin-stat-tile__bar          { fill: rgb(var(--accent-lavender-soft)); }
.kin-stat-tile--lavender .kin-stat-tile__bar--last    { fill: rgb(var(--accent-lavender-bold)); }
.kin-stat-tile--lavender .kin-stat-tile__range-btn--active { background: rgb(var(--accent-lavender-bold)); color: rgb(var(--ink-inverse)); }

.kin-stat-tile--peach .kin-stat-tile__value,
.kin-stat-tile--peach .kin-stat-tile__chart-fill   { color: rgb(var(--accent-peach-bold)); fill: rgb(var(--accent-peach-soft)); }
.kin-stat-tile--peach .kin-stat-tile__chart-stroke { stroke: rgb(var(--accent-peach-bold)); }
.kin-stat-tile--peach .kin-stat-tile__chart-dot    { fill: rgb(var(--accent-peach-bold)); }
.kin-stat-tile--peach .kin-stat-tile__bar          { fill: rgb(var(--accent-peach-soft)); }
.kin-stat-tile--peach .kin-stat-tile__bar--last    { fill: rgb(var(--accent-peach-bold)); }
.kin-stat-tile--peach .kin-stat-tile__range-btn--active { background: rgb(var(--accent-peach-bold)); color: rgb(var(--ink-inverse)); }

.kin-stat-tile--mint .kin-stat-tile__value,
.kin-stat-tile--mint .kin-stat-tile__chart-fill   { color: rgb(var(--accent-mint-bold)); fill: rgb(var(--accent-mint-soft)); }
.kin-stat-tile--mint .kin-stat-tile__chart-stroke { stroke: rgb(var(--accent-mint-bold)); }
.kin-stat-tile--mint .kin-stat-tile__chart-dot    { fill: rgb(var(--accent-mint-bold)); }
.kin-stat-tile--mint .kin-stat-tile__bar          { fill: rgb(var(--accent-mint-soft)); }
.kin-stat-tile--mint .kin-stat-tile__bar--last    { fill: rgb(var(--accent-mint-bold)); }
.kin-stat-tile--mint .kin-stat-tile__range-btn--active { background: rgb(var(--accent-mint-bold)); color: rgb(var(--ink-inverse)); }

.kin-stat-tile--sun .kin-stat-tile__value,
.kin-stat-tile--sun .kin-stat-tile__chart-fill   { color: rgb(var(--accent-sun-bold)); fill: rgb(var(--accent-sun-soft)); }
.kin-stat-tile--sun .kin-stat-tile__chart-stroke { stroke: rgb(var(--accent-sun-bold)); }
.kin-stat-tile--sun .kin-stat-tile__chart-dot    { fill: rgb(var(--accent-sun-bold)); }
.kin-stat-tile--sun .kin-stat-tile__bar          { fill: rgb(var(--accent-sun-soft)); }
.kin-stat-tile--sun .kin-stat-tile__bar--last    { fill: rgb(var(--accent-sun-bold)); }
.kin-stat-tile--sun .kin-stat-tile__range-btn--active { background: rgb(var(--accent-sun-bold)); color: rgb(var(--ink-inverse)); }

/* Apply accent color to hero number via color: inherit won't work;
   use the `.kin-stat-tile--X .kin-stat-tile__value` selectors above
   to set color directly. */
.kin-stat-tile__value { color: currentColor; }

/* Override: the kin-stat-tile__value inherits from its parent; the
   accent-specific rules above set color on the tile root. */
.kin-stat-tile--lavender { color: rgb(var(--accent-lavender-bold)); }
.kin-stat-tile--peach    { color: rgb(var(--accent-peach-bold)); }
.kin-stat-tile--mint     { color: rgb(var(--accent-mint-bold)); }
.kin-stat-tile--sun      { color: rgb(var(--accent-sun-bold)); }

/* But reset non-hero text back to default tokens. */
.kin-stat-tile > div p,
.kin-stat-tile > div span:not(.kin-stat-tile__delta) {
  /* labels + delta handled by their own classes */
}

/* Range filter buttons — inactive */
.kin-stat-tile__range-btn {
  background: transparent;
  color: rgb(var(--ink-tertiary));
  border: none;
  cursor: pointer;
  transition: background-color 150ms, color 150ms;
}

/* Delta chip colors */
.kin-stat-tile__delta--up {
  background: rgb(var(--status-success) / 0.15);
  color: rgb(var(--status-success));
}
.kin-stat-tile__delta--down {
  background: rgb(var(--status-failed) / 0.15);
  color: rgb(var(--status-failed));
}

@media (prefers-reduced-motion: reduce) {
  .kin-stat-tile__range-btn { transition: none; }
}
</style>
