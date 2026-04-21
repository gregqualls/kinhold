<script setup>
import { ref } from 'vue'
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import KinStatTile from '@/components/design-system/KinStatTile.vue'
import {
  SparklesIcon, ArrowUpRightIcon, ArrowDownRightIcon, FireIcon,
} from '@heroicons/vue/24/outline'

// ── Palette ───────────────────────────────────────────────────────────────────
const L = {
  surfaceApp: '#FAF8F5', surfaceRaised: '#FFFFFF', surfaceSunken: '#F5F2EE',
  inkPrimary: '#1C1C1E', inkSecondary: '#6B6966', inkTertiary: '#9C9895', inkInverse: '#FAF8F5',
  borderSubtle: '#E8E4DF', borderStrong: '#BCB8B2',
  accents: {
    lavender: { soft: '#EAE6F8', bold: '#6856B2' },
    peach:    { soft: '#FCE9E0', bold: '#BA562E' },
    mint:     { soft: '#D5F2E8', bold: '#2E8A62' },
    sun:      { soft: '#FCF3D2', bold: '#A2780C' },
  },
  status: {
    success: { soft: '#E1F0E7', bold: '#4D8C6A' },
    failed:  { soft: '#F4DADA', bold: '#BA4A4A' },
  },
}
const D = {
  surfaceApp: '#141311', surfaceRaised: '#1C1B19', surfaceSunken: '#161513', surfaceOverlay: '#242220',
  inkPrimary: '#F0EDE9', inkSecondary: '#A09C97', inkTertiary: '#6E6B67', inkInverse: '#1C1C1E',
  borderSubtle: '#2C2A27', borderStrong: '#403E3A',
  accents: {
    lavender: { soft: '#302A48', bold: '#B6A8E6' },
    peach:    { soft: '#3E241A', bold: '#F0A882' },
    mint:     { soft: '#18342A', bold: '#7CD6AE' },
    sun:      { soft: '#342C0A', bold: '#E6C452' },
  },
  status: {
    success: { soft: '#1C3A2A', bold: '#6CC498' },
    failed:  { soft: '#3C1E1E', bold: '#E67070' },
  },
}
const SHADOW_RESTING_LT = '0 1px 2px rgba(28,20,10,0.04), 0 2px 6px rgba(28,20,10,0.05)'
const SHADOW_RESTING_DK = '0 1px 2px rgba(0,0,0,0.30), 0 2px 6px rgba(0,0,0,0.25)'

// ── Variant C — time range filter state ───────────────────────────────────────
// Compact D/W/M/Y labels keep the filter inside the card's top-right corner
// at every breakpoint. Tooltips on hover would expose full names in production.
const RANGES = ['D', 'W', 'M', 'Y']
const rangePointsL  = ref('W')
const rangePointsD  = ref('W')
const rangeTasksL   = ref('M')
const rangeTasksD   = ref('M')
const rangeStreakL  = ref('W')
const rangeStreakD  = ref('W')

// ── Micro chart data sets ─────────────────────────────────────────────────────
// 7 data points, normalised 0–1; rendered as SVG polyline / bars
const chartPoints = [0.30, 0.45, 0.38, 0.60, 0.52, 0.75, 0.88]
const chartTasks  = [0.55, 0.62, 0.58, 0.70, 0.65, 0.80, 0.84]
const chartStreak = [0.10, 0.20, 0.30, 0.42, 0.55, 0.70, 0.85]

// Convert normalised array to SVG polyline points string
// viewBox width=140 height=40; each point is spaced 140/(n-1) apart
function toPolyline(data) {
  const W = 140, H = 40, n = data.length
  return data
    .map((v, i) => `${(i / (n - 1)) * W},${H - v * H}`)
    .join(' ')
}

// Convert to SVG bar rects — returns array of { x, width, height, y }
function toBars(data) {
  const W = 140, H = 40, n = data.length
  const barW = Math.floor(W / n) - 3
  return data.map((v, i) => ({
    x: i * (W / n) + 1,
    y: H - v * H,
    width: barW,
    height: v * H,
  }))
}
</script>

<template>
  <ComponentPage
    title="5.1 StatTile"
    description="The hero-number component. Every data-heavy surface in Kinhold — points bank, task completion, streak, meal progress, leaderboard — uses StatTile as its centrepiece. Numbers are heroes; everything else is supporting cast."
    status="chosen"
  >


    <!-- ═══════════════════════════════════════════════════════════════
         LOCKED — Full StatTile with all layers
         Hero number + delta chip + label + optional chart + optional
         time-range filter. Every layer below the hero number is an
         opt-in prop — see "Prop configurations" section further down
         for the same component with layers removed.
         ════════════════════════════════════════════════════════════════ -->
    <section class="mb-20">
      <VariantFrame label="Full" caption="All layers — hero number + label + delta chip + micro chart + D/W/M/Y time-range filter.">
        <div class="w-full space-y-10">

          <!-- ── LIGHT PANEL ── -->
          <div class="rounded-2xl border p-6 space-y-6" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

              <!-- Points bank with range filter -->
              <div
                class="stat-card-root rounded-[20px] border flex flex-col gap-3 py-6 px-6"
                :style="{
                  background: L.surfaceRaised,
                  borderColor: L.borderSubtle,
                  boxShadow: SHADOW_RESTING_LT,
                }"
              >
                <!-- Row: label + filter -->
                <div class="flex items-center justify-between gap-2">
                  <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Points Earned</p>
                  <!-- Segmented range control -->
                  <div
                    class="flex items-center rounded-lg overflow-hidden border text-[10px] font-medium"
                    :style="{ borderColor: L.borderSubtle, background: L.surfaceSunken }"
                  >
                    <button
                      v-for="r in RANGES"
                      :key="r"
                      class="px-1.5 py-0.5 transition-colors"
                      :style="rangePointsL === r
                        ? { background: L.accents.sun.bold, color: L.inkInverse }
                        : { background: 'transparent', color: L.inkTertiary }"
                      @click="rangePointsL = r"
                    >{{ r }}</button>
                  </div>
                </div>
                <!-- Delta -->
                <div class="flex items-center gap-2">
                  <span
                    class="inline-flex items-center gap-1 h-6 px-2.5 rounded-full text-[11px] font-medium"
                    :style="{ background: L.status.success.soft, color: L.status.success.bold }"
                  >
                    <ArrowUpRightIcon class="w-3 h-3" />
                    +86
                  </span>
                </div>
                <p
                  class="leading-none font-semibold tracking-tighter"
                  style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: clamp(2rem, 30cqw, 6.5rem); letter-spacing: -0.02em;"
                  :style="{ color: L.accents.sun.bold }"
                >1,248</p>
                <svg viewBox="0 0 140 40" class="w-full mt-1" style="height: 36px;" fill="none" aria-hidden="true">
                  <polyline
                    :points="toPolyline(chartPoints) + ' 140,40 0,40'"
                    :fill="L.accents.sun.soft"
                    stroke="none"
                    opacity="0.7"
                  />
                  <polyline
                    :points="toPolyline(chartPoints)"
                    :stroke="L.accents.sun.bold"
                    stroke-width="2"
                    stroke-linejoin="round"
                    stroke-linecap="round"
                    fill="none"
                  />
                  <circle cx="140" :cy="40 - chartPoints[6] * 40" r="3" :fill="L.accents.sun.bold" />
                </svg>
              </div>

              <!-- Task completion with range filter -->
              <div
                class="stat-card-root rounded-[20px] border flex flex-col gap-3 py-6 px-6"
                :style="{
                  background: L.surfaceRaised,
                  borderColor: L.borderSubtle,
                  boxShadow: SHADOW_RESTING_LT,
                }"
              >
                <div class="flex items-center justify-between gap-2">
                  <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Tasks Done</p>
                  <div
                    class="flex items-center rounded-lg overflow-hidden border text-[10px] font-medium"
                    :style="{ borderColor: L.borderSubtle, background: L.surfaceSunken }"
                  >
                    <button
                      v-for="r in RANGES"
                      :key="r"
                      class="px-1.5 py-0.5 transition-colors"
                      :style="rangeTasksL === r
                        ? { background: L.accents.mint.bold, color: L.inkInverse }
                        : { background: 'transparent', color: L.inkTertiary }"
                      @click="rangeTasksL = r"
                    >{{ r }}</button>
                  </div>
                </div>
                <div class="flex items-center gap-2">
                  <span
                    class="inline-flex items-center gap-1 h-6 px-2.5 rounded-full text-[11px] font-medium"
                    :style="{ background: L.status.success.soft, color: L.status.success.bold }"
                  >
                    <ArrowUpRightIcon class="w-3 h-3" />
                    +12%
                  </span>
                </div>
                <p
                  class="leading-none font-semibold tracking-tighter"
                  style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: clamp(2rem, 30cqw, 6.5rem); letter-spacing: -0.02em;"
                  :style="{ color: L.accents.mint.bold }"
                >84%</p>
                <svg viewBox="0 0 140 40" class="w-full mt-1" style="height: 36px;" aria-hidden="true">
                  <rect
                    v-for="(bar, i) in toBars(chartTasks)"
                    :key="i"
                    :x="bar.x"
                    :y="bar.y"
                    :width="bar.width"
                    :height="bar.height"
                    rx="2"
                    :fill="i === chartTasks.length - 1 ? L.accents.mint.bold : L.accents.mint.soft"
                  />
                </svg>
              </div>

              <!-- Day streak with range filter -->
              <div
                class="stat-card-root rounded-[20px] border flex flex-col gap-3 py-6 px-6"
                :style="{
                  background: L.surfaceRaised,
                  borderColor: L.borderSubtle,
                  boxShadow: SHADOW_RESTING_LT,
                }"
              >
                <div class="flex items-center justify-between gap-2">
                  <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Day Streak</p>
                  <div
                    class="flex items-center rounded-lg overflow-hidden border text-[10px] font-medium"
                    :style="{ borderColor: L.borderSubtle, background: L.surfaceSunken }"
                  >
                    <button
                      v-for="r in RANGES"
                      :key="r"
                      class="px-1.5 py-0.5 transition-colors"
                      :style="rangeStreakL === r
                        ? { background: L.accents.peach.bold, color: L.inkInverse }
                        : { background: 'transparent', color: L.inkTertiary }"
                      @click="rangeStreakL = r"
                    >{{ r }}</button>
                  </div>
                </div>
                <div class="flex items-center gap-2">
                  <span
                    class="inline-flex items-center gap-1 h-6 px-2.5 rounded-full text-[11px] font-medium"
                    :style="{ background: L.accents.peach.soft, color: L.accents.peach.bold }"
                  >
                    <FireIcon class="w-3 h-3" />
                    Personal best
                  </span>
                </div>
                <p
                  class="leading-none font-semibold tracking-tighter"
                  style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: clamp(2rem, 30cqw, 6.5rem); letter-spacing: -0.02em;"
                  :style="{ color: L.accents.peach.bold }"
                >12</p>
                <svg viewBox="0 0 140 40" class="w-full mt-1" style="height: 36px;" aria-hidden="true">
                  <rect
                    v-for="(bar, i) in toBars(chartStreak)"
                    :key="i"
                    :x="bar.x"
                    :y="bar.y"
                    :width="bar.width"
                    :height="bar.height"
                    rx="2"
                    :fill="i === chartStreak.length - 1 ? L.accents.peach.bold : L.accents.peach.soft"
                  />
                </svg>
              </div>
            </div>
          </div>

          <!-- ── DARK PANEL ── -->
          <div class="rounded-2xl border p-6 space-y-6" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

              <!-- Points bank -->
              <div
                class="stat-card-root rounded-[20px] border flex flex-col gap-3 py-6 px-6"
                :style="{
                  background: D.surfaceRaised,
                  borderColor: D.borderSubtle,
                  boxShadow: SHADOW_RESTING_DK,
                }"
              >
                <div class="flex items-center justify-between gap-2">
                  <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Points Earned</p>
                  <div
                    class="flex items-center rounded-lg overflow-hidden border text-[10px] font-medium"
                    :style="{ borderColor: D.borderSubtle, background: D.surfaceSunken }"
                  >
                    <button
                      v-for="r in RANGES"
                      :key="r"
                      class="px-1.5 py-0.5 transition-colors"
                      :style="rangePointsD === r
                        ? { background: D.accents.sun.bold, color: D.inkInverse }
                        : { background: 'transparent', color: D.inkTertiary }"
                      @click="rangePointsD = r"
                    >{{ r }}</button>
                  </div>
                </div>
                <div class="flex items-center gap-2">
                  <span
                    class="inline-flex items-center gap-1 h-6 px-2.5 rounded-full text-[11px] font-medium"
                    :style="{ background: D.status.success.soft, color: D.status.success.bold }"
                  >
                    <ArrowUpRightIcon class="w-3 h-3" />
                    +86
                  </span>
                </div>
                <p
                  class="leading-none font-semibold tracking-tighter"
                  style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: clamp(2rem, 30cqw, 6.5rem); letter-spacing: -0.02em;"
                  :style="{ color: D.accents.sun.bold }"
                >1,248</p>
                <svg viewBox="0 0 140 40" class="w-full mt-1" style="height: 36px;" fill="none" aria-hidden="true">
                  <polyline
                    :points="toPolyline(chartPoints) + ' 140,40 0,40'"
                    :fill="D.accents.sun.soft"
                    stroke="none"
                    opacity="0.5"
                  />
                  <polyline
                    :points="toPolyline(chartPoints)"
                    :stroke="D.accents.sun.bold"
                    stroke-width="2"
                    stroke-linejoin="round"
                    stroke-linecap="round"
                    fill="none"
                  />
                  <circle cx="140" :cy="40 - chartPoints[6] * 40" r="3" :fill="D.accents.sun.bold" />
                </svg>
              </div>

              <!-- Task completion -->
              <div
                class="stat-card-root rounded-[20px] border flex flex-col gap-3 py-6 px-6"
                :style="{
                  background: D.surfaceRaised,
                  borderColor: D.borderSubtle,
                  boxShadow: SHADOW_RESTING_DK,
                }"
              >
                <div class="flex items-center justify-between gap-2">
                  <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Tasks Done</p>
                  <div
                    class="flex items-center rounded-lg overflow-hidden border text-[10px] font-medium"
                    :style="{ borderColor: D.borderSubtle, background: D.surfaceSunken }"
                  >
                    <button
                      v-for="r in RANGES"
                      :key="r"
                      class="px-1.5 py-0.5 transition-colors"
                      :style="rangeTasksD === r
                        ? { background: D.accents.mint.bold, color: D.inkInverse }
                        : { background: 'transparent', color: D.inkTertiary }"
                      @click="rangeTasksD = r"
                    >{{ r }}</button>
                  </div>
                </div>
                <div class="flex items-center gap-2">
                  <span
                    class="inline-flex items-center gap-1 h-6 px-2.5 rounded-full text-[11px] font-medium"
                    :style="{ background: D.status.success.soft, color: D.status.success.bold }"
                  >
                    <ArrowUpRightIcon class="w-3 h-3" />
                    +12%
                  </span>
                </div>
                <p
                  class="leading-none font-semibold tracking-tighter"
                  style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: clamp(2rem, 30cqw, 6.5rem); letter-spacing: -0.02em;"
                  :style="{ color: D.accents.mint.bold }"
                >84%</p>
                <svg viewBox="0 0 140 40" class="w-full mt-1" style="height: 36px;" aria-hidden="true">
                  <rect
                    v-for="(bar, i) in toBars(chartTasks)"
                    :key="i"
                    :x="bar.x"
                    :y="bar.y"
                    :width="bar.width"
                    :height="bar.height"
                    rx="2"
                    :fill="i === chartTasks.length - 1 ? D.accents.mint.bold : D.accents.mint.soft"
                  />
                </svg>
              </div>

              <!-- Day streak -->
              <div
                class="stat-card-root rounded-[20px] border flex flex-col gap-3 py-6 px-6"
                :style="{
                  background: D.surfaceRaised,
                  borderColor: D.borderSubtle,
                  boxShadow: SHADOW_RESTING_DK,
                }"
              >
                <div class="flex items-center justify-between gap-2">
                  <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Day Streak</p>
                  <div
                    class="flex items-center rounded-lg overflow-hidden border text-[10px] font-medium"
                    :style="{ borderColor: D.borderSubtle, background: D.surfaceSunken }"
                  >
                    <button
                      v-for="r in RANGES"
                      :key="r"
                      class="px-1.5 py-0.5 transition-colors"
                      :style="rangeStreakD === r
                        ? { background: D.accents.peach.bold, color: D.inkInverse }
                        : { background: 'transparent', color: D.inkTertiary }"
                      @click="rangeStreakD = r"
                    >{{ r }}</button>
                  </div>
                </div>
                <div class="flex items-center gap-2">
                  <span
                    class="inline-flex items-center gap-1 h-6 px-2.5 rounded-full text-[11px] font-medium"
                    :style="{ background: D.accents.peach.soft, color: D.accents.peach.bold }"
                  >
                    <FireIcon class="w-3 h-3" />
                    Personal best
                  </span>
                </div>
                <p
                  class="leading-none font-semibold tracking-tighter"
                  style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: clamp(2rem, 30cqw, 6.5rem); letter-spacing: -0.02em;"
                  :style="{ color: D.accents.peach.bold }"
                >12</p>
                <svg viewBox="0 0 140 40" class="w-full mt-1" style="height: 36px;" aria-hidden="true">
                  <rect
                    v-for="(bar, i) in toBars(chartStreak)"
                    :key="i"
                    :x="bar.x"
                    :y="bar.y"
                    :width="bar.width"
                    :height="bar.height"
                    rx="2"
                    :fill="i === chartStreak.length - 1 ? D.accents.peach.bold : D.accents.peach.soft"
                  />
                </svg>
              </div>
            </div>
          </div>

          <!-- Mobile note -->
          <p class="text-xs" :style="{ color: L.inkTertiary }">
            At 375px the segmented filter uses 10px text + 1.5/0.5 padding — fits 4 segments without overflow. Hero number clamps to 3rem.
          </p>
        </div>
      </VariantFrame>
    </section>

    <!-- ═══════════════════════════════════════════════════════════════
         PROP CONFIGURATIONS — same component, layers toggled off
         Demonstrates the opt-in-prop model. The full-featured tile
         above is the canonical shape; the tiles below are what you
         get by passing chart={false} and/or filter={false}.
         ════════════════════════════════════════════════════════════════ -->
    <section class="mb-20">
      <VariantFrame label="Configs" caption="Same component · prop combinations — chart and filter are opt-in. Three configurations cover dashboard tiles, leaderboard rows, and analytics widgets.">
        <div class="w-full space-y-10">

          <!-- ── LIGHT PANEL ── -->
          <div class="rounded-2xl border p-6 space-y-6" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

              <!-- CONFIG 1: Minimal — no chart, no filter -->
              <div class="flex flex-col gap-3">
                <div
                  class="stat-card-root rounded-[20px] border flex flex-col gap-3 py-6 px-6"
                  :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle, boxShadow: SHADOW_RESTING_LT }"
                >
                  <div class="flex items-start justify-between gap-2">
                    <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Points Earned</p>
                    <span
                      class="inline-flex items-center gap-1 h-6 px-2.5 rounded-full text-[11px] font-medium flex-shrink-0"
                      :style="{ background: L.status.success.soft, color: L.status.success.bold }"
                    >
                      <ArrowUpRightIcon class="w-3 h-3" />
                      +86
                    </span>
                  </div>
                  <p
                    class="leading-none font-semibold tracking-tighter"
                    style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: clamp(2rem, 30cqw, 6.5rem); letter-spacing: -0.02em;"
                    :style="{ color: L.accents.sun.bold }"
                  >1,248</p>
                </div>
                <p class="text-[11px] text-center" :style="{ color: L.inkTertiary }">Dashboard tile — number only</p>
              </div>

              <!-- CONFIG 2: With chart, no filter -->
              <div class="flex flex-col gap-3">
                <div
                  class="stat-card-root rounded-[20px] border flex flex-col gap-3 py-6 px-6"
                  :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle, boxShadow: SHADOW_RESTING_LT }"
                >
                  <div class="flex items-start justify-between gap-2">
                    <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Points Earned</p>
                    <span
                      class="inline-flex items-center gap-1 h-6 px-2.5 rounded-full text-[11px] font-medium flex-shrink-0"
                      :style="{ background: L.status.success.soft, color: L.status.success.bold }"
                    >
                      <ArrowUpRightIcon class="w-3 h-3" />
                      +86
                    </span>
                  </div>
                  <p
                    class="leading-none font-semibold tracking-tighter"
                    style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: clamp(2rem, 30cqw, 6.5rem); letter-spacing: -0.02em;"
                    :style="{ color: L.accents.sun.bold }"
                  >1,248</p>
                  <svg viewBox="0 0 140 40" preserveAspectRatio="none" class="w-full h-10 block">
                    <polyline
                      :points="toPolyline(chartPoints)"
                      fill="none"
                      :stroke="L.accents.sun.bold"
                      stroke-width="2"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    />
                    <circle :cx="140" :cy="40 - chartPoints[chartPoints.length - 1] * 40" r="2.5" :fill="L.accents.sun.bold" />
                  </svg>
                </div>
                <p class="text-[11px] text-center" :style="{ color: L.inkTertiary }">Leaderboard row — with chart</p>
              </div>

              <!-- CONFIG 3: Full (chart + filter) -->
              <div class="flex flex-col gap-3">
                <div
                  class="stat-card-root rounded-[20px] border flex flex-col gap-3 py-6 px-6"
                  :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle, boxShadow: SHADOW_RESTING_LT }"
                >
                  <div class="flex items-start justify-between gap-2">
                    <div>
                      <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Points Earned</p>
                      <span
                        class="inline-flex items-center gap-1 h-6 px-2.5 rounded-full text-[11px] font-medium mt-1"
                        :style="{ background: L.status.success.soft, color: L.status.success.bold }"
                      >
                        <ArrowUpRightIcon class="w-3 h-3" />
                        +86
                      </span>
                    </div>
                    <div class="inline-flex rounded-full p-0.5 flex-shrink-0" :style="{ border: `1px solid ${L.borderStrong}`, background: L.surfaceRaised }">
                      <button
                        v-for="r in RANGES"
                        :key="r"
                        class="text-[10px] font-semibold w-6 h-5 rounded-full transition-all"
                        :style="rangePointsL === r
                          ? { background: L.inkPrimary, color: L.inkInverse }
                          : { background: 'transparent', color: L.inkSecondary }"
                        @click="rangePointsL = r"
                      >{{ r }}</button>
                    </div>
                  </div>
                  <p
                    class="leading-none font-semibold tracking-tighter"
                    style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: clamp(2rem, 30cqw, 6.5rem); letter-spacing: -0.02em;"
                    :style="{ color: L.accents.sun.bold }"
                  >1,248</p>
                  <svg viewBox="0 0 140 40" preserveAspectRatio="none" class="w-full h-10 block">
                    <polyline
                      :points="toPolyline(chartPoints)"
                      fill="none"
                      :stroke="L.accents.sun.bold"
                      stroke-width="2"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    />
                    <circle :cx="140" :cy="40 - chartPoints[chartPoints.length - 1] * 40" r="2.5" :fill="L.accents.sun.bold" />
                  </svg>
                </div>
                <p class="text-[11px] text-center" :style="{ color: L.inkTertiary }">Full widget — chart + filter</p>
              </div>
            </div>
          </div>

          <!-- ── DARK PANEL ── -->
          <div class="rounded-2xl border p-6 space-y-6" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

              <!-- CONFIG 1: Minimal -->
              <div class="flex flex-col gap-3">
                <div
                  class="stat-card-root rounded-[20px] border flex flex-col gap-3 py-6 px-6"
                  :style="{ background: D.surfaceRaised, borderColor: D.borderSubtle, boxShadow: SHADOW_RESTING_DK }"
                >
                  <div class="flex items-start justify-between gap-2">
                    <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Points Earned</p>
                    <span
                      class="inline-flex items-center gap-1 h-6 px-2.5 rounded-full text-[11px] font-medium flex-shrink-0"
                      :style="{ background: D.status.success.soft, color: D.status.success.bold }"
                    >
                      <ArrowUpRightIcon class="w-3 h-3" />
                      +86
                    </span>
                  </div>
                  <p
                    class="leading-none font-semibold tracking-tighter"
                    style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: clamp(2rem, 30cqw, 6.5rem); letter-spacing: -0.02em;"
                    :style="{ color: D.accents.sun.bold }"
                  >1,248</p>
                </div>
                <p class="text-[11px] text-center" :style="{ color: D.inkTertiary }">Dashboard tile — number only</p>
              </div>

              <!-- CONFIG 2: With chart -->
              <div class="flex flex-col gap-3">
                <div
                  class="stat-card-root rounded-[20px] border flex flex-col gap-3 py-6 px-6"
                  :style="{ background: D.surfaceRaised, borderColor: D.borderSubtle, boxShadow: SHADOW_RESTING_DK }"
                >
                  <div class="flex items-start justify-between gap-2">
                    <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Points Earned</p>
                    <span
                      class="inline-flex items-center gap-1 h-6 px-2.5 rounded-full text-[11px] font-medium flex-shrink-0"
                      :style="{ background: D.status.success.soft, color: D.status.success.bold }"
                    >
                      <ArrowUpRightIcon class="w-3 h-3" />
                      +86
                    </span>
                  </div>
                  <p
                    class="leading-none font-semibold tracking-tighter"
                    style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: clamp(2rem, 30cqw, 6.5rem); letter-spacing: -0.02em;"
                    :style="{ color: D.accents.sun.bold }"
                  >1,248</p>
                  <svg viewBox="0 0 140 40" preserveAspectRatio="none" class="w-full h-10 block">
                    <polyline
                      :points="toPolyline(chartPoints)"
                      fill="none"
                      :stroke="D.accents.sun.bold"
                      stroke-width="2"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    />
                    <circle :cx="140" :cy="40 - chartPoints[chartPoints.length - 1] * 40" r="2.5" :fill="D.accents.sun.bold" />
                  </svg>
                </div>
                <p class="text-[11px] text-center" :style="{ color: D.inkTertiary }">Leaderboard row — with chart</p>
              </div>

              <!-- CONFIG 3: Full -->
              <div class="flex flex-col gap-3">
                <div
                  class="stat-card-root rounded-[20px] border flex flex-col gap-3 py-6 px-6"
                  :style="{ background: D.surfaceRaised, borderColor: D.borderSubtle, boxShadow: SHADOW_RESTING_DK }"
                >
                  <div class="flex items-start justify-between gap-2">
                    <div>
                      <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Points Earned</p>
                      <span
                        class="inline-flex items-center gap-1 h-6 px-2.5 rounded-full text-[11px] font-medium mt-1"
                        :style="{ background: D.status.success.soft, color: D.status.success.bold }"
                      >
                        <ArrowUpRightIcon class="w-3 h-3" />
                        +86
                      </span>
                    </div>
                    <div class="inline-flex rounded-full p-0.5 flex-shrink-0" :style="{ border: `1px solid ${D.borderStrong}`, background: D.surfaceRaised }">
                      <button
                        v-for="r in RANGES"
                        :key="r"
                        class="text-[10px] font-semibold w-6 h-5 rounded-full transition-all"
                        :style="rangePointsD === r
                          ? { background: D.inkPrimary, color: D.inkInverse }
                          : { background: 'transparent', color: D.inkSecondary }"
                        @click="rangePointsD = r"
                      >{{ r }}</button>
                    </div>
                  </div>
                  <p
                    class="leading-none font-semibold tracking-tighter"
                    style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: clamp(2rem, 30cqw, 6.5rem); letter-spacing: -0.02em;"
                    :style="{ color: D.accents.sun.bold }"
                  >1,248</p>
                  <svg viewBox="0 0 140 40" preserveAspectRatio="none" class="w-full h-10 block">
                    <polyline
                      :points="toPolyline(chartPoints)"
                      fill="none"
                      :stroke="D.accents.sun.bold"
                      stroke-width="2"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    />
                    <circle :cx="140" :cy="40 - chartPoints[chartPoints.length - 1] * 40" r="2.5" :fill="D.accents.sun.bold" />
                  </svg>
                </div>
                <p class="text-[11px] text-center" :style="{ color: D.inkTertiary }">Full widget — chart + filter</p>
              </div>
            </div>
          </div>

        </div>
      </VariantFrame>
    </section>

    <!-- ═══════════════════════════════════════════════════════════════
         CLAUDE'S PICK CALLOUT
         ════════════════════════════════════════════════════════════════ -->
    <section class="mb-20">
      <div
        class="rounded-2xl border p-6 flex gap-4 items-start"
        :style="{
          background: L.accents.lavender.soft,
          borderColor: '#C0B4E8',
        }"
      >
        <SparklesIcon class="w-5 h-5 flex-shrink-0 mt-0.5" :style="{ color: L.accents.lavender.bold }" />
        <div>
          <p class="text-sm font-semibold mb-1" :style="{ color: L.accents.lavender.bold }">
            LOCKED — single component with opt-in layers
          </p>
          <p class="text-sm leading-relaxed mb-2" :style="{ color: L.inkPrimary }">
            <strong>"Numbers are heroes" brief, fully realised.</strong> The hero number dominates every tile at every breakpoint via container queries (<code class="text-xs font-mono bg-white/60 px-1 rounded">font-size: clamp(2rem, 30cqw, 6.5rem)</code>) — sized to its own card, not the viewport. Hero stays hero in a 4-up desktop row or a phone column.
          </p>
          <p class="text-sm leading-relaxed" :style="{ color: L.inkPrimary }">
            <strong>One component covers three jobs via opt-in props.</strong>
            Pass no chart + no filter → compact dashboard tile.
            Pass chart, skip filter → leaderboard row / comparison grid.
            Pass both → full analytics widget. See the "Configs" section above for all three rendered side-by-side. Time-range filter uses compact <code class="text-xs font-mono bg-white/60 px-1 rounded">D / W / M / Y</code> labels so it fits cleanly in the card's top-right at every width.
          </p>
        </div>
      </div>
    </section>

    <!-- ═══════════════════════════════════════════════════════════════
         USAGE GUIDE
         ════════════════════════════════════════════════════════════════ -->
    <section class="mb-10">
      <div
        class="rounded-2xl border divide-y"
        :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle }"
      >
        <div class="px-6 py-4">
          <h2 class="text-base font-semibold" :style="{ color: L.inkPrimary }">Usage guide</h2>
          <p class="text-sm mt-1" :style="{ color: L.inkSecondary }">Map context to prop configuration. Always choose the leanest set of props that serves the information need.</p>
        </div>

        <!-- Config 1: Minimal -->
        <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-[160px_1fr] gap-3">
          <div>
            <span
              class="inline-flex items-center justify-center h-6 min-w-[1.5rem] px-2 rounded-full text-xs font-semibold font-mono"
              :style="{ background: L.inkPrimary, color: L.inkInverse }"
            >no chart, no filter</span>
          </div>
          <div class="space-y-1">
            <p class="text-sm font-medium" :style="{ color: L.inkPrimary }">Dashboard hero widget, above-the-fold spotlights</p>
            <p class="text-sm" :style="{ color: L.inkSecondary }">
              Use when the number alone tells the story and historical trend is not needed — e.g. today's points balance, current streak count, or the currency tile in a rewards summary. The currency variant (integer + decimals at 60% scale) lives here.
            </p>
          </div>
        </div>

        <!-- Config 2: Chart only -->
        <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-[160px_1fr] gap-3">
          <div>
            <span
              class="inline-flex items-center justify-center h-6 min-w-[1.5rem] px-2 rounded-full text-xs font-semibold font-mono"
              :style="{ background: L.inkPrimary, color: L.inkInverse }"
            >chart, no filter</span>
          </div>
          <div class="space-y-1">
            <p class="text-sm font-medium" :style="{ color: L.inkPrimary }">Leaderboard rows, side-by-side comparison grids</p>
            <p class="text-sm" :style="{ color: L.inkSecondary }">
              Add the chart when trend reinforces the number — e.g. a leaderboard row showing a member's points alongside their weekly trajectory, or a 3-up completion grid on the tasks summary page. The micro chart is decorative-informational: no axes, no interaction, just direction.
            </p>
          </div>
        </div>

        <!-- Config 3: Full -->
        <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-[160px_1fr] gap-3">
          <div>
            <span
              class="inline-flex items-center justify-center h-6 min-w-[1.5rem] px-2 rounded-full text-xs font-semibold font-mono"
              :style="{ background: L.inkPrimary, color: L.inkInverse }"
            >chart + filter</span>
          </div>
          <div class="space-y-1">
            <p class="text-sm font-medium" :style="{ color: L.inkPrimary }">Points analytics page, family stats dashboard, admin widgets</p>
            <p class="text-sm" :style="{ color: L.inkSecondary }">
              Use on dedicated data pages where the user explicitly wants to explore over time — points earned this month vs this year, task completion rate by period, streak history. The filter must be wired to real data in production; the micro chart re-renders to match the selected period.
            </p>
          </div>
        </div>

        <!-- Accent / accent mapping note -->
        <div class="px-6 py-4">
          <p class="text-xs font-semibold uppercase tracking-widest mb-2" :style="{ color: L.inkTertiary }">Accent mapping convention</p>
          <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
            <div
              v-for="(entry, key) in L.accents"
              :key="key"
              class="flex items-center gap-2 rounded-lg px-3 py-2"
              :style="{ background: entry.soft }"
            >
              <div class="w-3 h-3 rounded-full flex-shrink-0" :style="{ background: entry.bold }" />
              <span class="text-xs font-medium capitalize" :style="{ color: entry.bold }">{{ key }}</span>
              <span class="text-[10px] ml-auto" :style="{ color: entry.bold, opacity: 0.7 }">
                {{ key === 'sun' ? 'Points' : key === 'mint' ? 'Tasks' : key === 'peach' ? 'Streak' : 'Currency/Rewards' }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </section>


    <!-- ══════════════════════════════════════════════════════════════════════
         KIN COMPONENT PREVIEW — review below before replacing the bespoke demo
         ═══════════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="Kin" caption="KinStatTile — proposed extraction. Hero number is container-query scaled (30cqw).">
        <div class="w-full space-y-10">

          <!-- LIGHT PANEL — Full configuration (all layers) -->
          <div class="rounded-2xl border p-6 space-y-6"
               :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode — full (label + range + delta + hero + chart)</p>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
              <KinStatTile
                label="Points Earned" value="1,248" accent-color="sun"
                delta="+86" :delta-up="true"
                :chart-data="chartPoints" chart-type="line"
                :ranges="RANGES" v-model:range="rangePointsL"
              />
              <KinStatTile
                label="Tasks Done" value="84%" accent-color="mint"
                delta="+12%" :delta-up="true"
                :chart-data="chartTasks" chart-type="bars"
                :ranges="RANGES" v-model:range="rangeTasksL"
              />
              <KinStatTile
                label="Day Streak" value="23" accent-color="peach"
                delta="+3" :delta-up="true"
                :chart-data="chartStreak" chart-type="line"
                :ranges="RANGES" v-model:range="rangeStreakL"
              />
            </div>

            <p class="text-xs font-semibold uppercase tracking-widest pt-4" :style="{ color: L.inkTertiary }">Layers removed — label + hero only</p>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
              <KinStatTile label="Members" value="5" accent-color="lavender" />
              <KinStatTile label="This Week" value="$287" accent-color="mint" delta="-$12" :delta-up="false" />
              <KinStatTile label="Leader" value="Greg" accent-color="sun" />
            </div>
          </div>

          <!-- DARK PANEL -->
          <div class="dark rounded-2xl border p-6 space-y-6"
               :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode — full</p>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
              <KinStatTile
                label="Points Earned" value="1,248" accent-color="sun"
                delta="+86" :delta-up="true"
                :chart-data="chartPoints" chart-type="line"
                :ranges="RANGES" v-model:range="rangePointsD"
              />
              <KinStatTile
                label="Tasks Done" value="84%" accent-color="mint"
                delta="+12%" :delta-up="true"
                :chart-data="chartTasks" chart-type="bars"
                :ranges="RANGES" v-model:range="rangeTasksD"
              />
              <KinStatTile
                label="Day Streak" value="23" accent-color="peach"
                delta="+3" :delta-up="true"
                :chart-data="chartStreak" chart-type="line"
                :ranges="RANGES" v-model:range="rangeStreakD"
              />
            </div>

            <p class="text-xs font-semibold uppercase tracking-widest pt-4" :style="{ color: D.inkTertiary }">Layers removed — label + hero only</p>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
              <KinStatTile label="Members" value="5" accent-color="lavender" />
              <KinStatTile label="This Week" value="$287" accent-color="mint" delta="-$12" :delta-up="false" />
              <KinStatTile label="Leader" value="Greg" accent-color="sun" />
            </div>
          </div>

        </div>
      </VariantFrame>
      <p class="mt-3 text-sm px-1" :style="{ color: L.inkSecondary }">
        Review against the bespoke variants above. Every layer below the hero number is opt-in — drop the range filter, chart, or delta props to reveal only what's needed.
      </p>
    </section>

  </ComponentPage>
</template>

<style scoped>
/*
  Each StatTile card is a CSS containment context so the hero number
  scales to the card's width via `cqw` units rather than the viewport.
  This fixes the "big screen, small card" clipping problem — a 4-up
  desktop row of ~220px cards used to blow through the card bounds
  when the viewport was wide. With container queries, the hero number
  sizes itself to its own container.
*/
.stat-card-root {
  container-type: inline-size;
  container-name: stat-card;
}
</style>
