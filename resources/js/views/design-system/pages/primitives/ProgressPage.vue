<script setup>
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'

// ── Dark-mode hex constants ──────────────────────────────────────────────────
const D = {
  surfaceApp:    '#141311',
  surfaceRaised: '#1C1B19',
  surfaceSunken: '#161513',
  inkPrimary:    '#F0EDE9',
  inkSecondary:  '#A09C97',
  inkTertiary:   '#6E6B67',
  borderSubtle:  '#2C2A27',
  borderStrong:  '#403E3A',
  trackBg:       '#2C2A27',  // progress track in dark — lighter than page so it reads
}

// ── Light-mode hex constants ─────────────────────────────────────────────────
const L = {
  surfaceApp:    '#FAF8F5',
  surfaceRaised: '#FFFFFF',
  surfaceSunken: '#F5F2EE',
  inkPrimary:    '#1C1C1E',
  inkSecondary:  '#6B6966',
  inkTertiary:   '#9C9895',
  borderSubtle:  '#E8E4DF',
  trackBg:       '#F5F2EE',  // surface-sunken for track
}

// ── Emphasis color map ───────────────────────────────────────────────────────
// Light bold / dark bold for fill; same track bg as default
const EMPHASIS = {
  lavender: { label: 'Lavender',  ltFill: '#6856B2', dkFill: '#B6A8E6', ltGrad: 'linear-gradient(to right, #9B8FD4, #6856B2)', dkGrad: 'linear-gradient(to right, #D6CCFF, #B6A8E6)' },
  success:  { label: 'Success',   ltFill: '#4D8C6A', dkFill: '#6CC498', ltGrad: 'linear-gradient(to right, #6CC498, #4D8C6A)', dkGrad: 'linear-gradient(to right, #A6F0C6, #6CC498)' },
  warning:  { label: 'Warning',   ltFill: '#C48C24', dkFill: '#E6C452', ltGrad: 'linear-gradient(to right, #E6C452, #C48C24)', dkGrad: 'linear-gradient(to right, #FFE88A, #E6C452)' },
  failed:   { label: 'Failed',    ltFill: '#BA4A4A', dkFill: '#E67070', ltGrad: 'linear-gradient(to right, #E67070, #BA4A4A)', dkGrad: 'linear-gradient(to right, #FFAAAA, #E67070)' },
}

// ── Labeled bars (realistic Kinhold contexts) ────────────────────────────────
const LABELED_BARS = [
  { label: 'Weekly tasks', detail: '8 of 12 completed', pct: 67, display: '67%' },
  { label: 'Points toward next reward', detail: '240 / 500', pct: 48, display: '48%' },
  { label: 'Budget for groceries', detail: '$287 / $400', pct: 72, display: '72%' },
]

// ── Emphasis bars (same 4, with realistic values) ───────────────────────────
const EMPHASIS_BARS = [
  { key: 'lavender', pct: 63,  context: '63%' },
  { key: 'success',  pct: 100, context: '100%' },
  { key: 'warning',  pct: 85,  context: '85%' },
  { key: 'failed',   pct: 92,  context: '92%' },
]

// ── Arc SVG math ─────────────────────────────────────────────────────────────
// viewBox 100×100, r=44, stroke leaves 6-unit inset on each side
const ARC_R = 44
const ARC_CIRC = 2 * Math.PI * ARC_R  // ≈ 276.46

function arcOffset(pct) {
  // pct 0→100. dashoffset = circ * (1 - pct/100)
  return ARC_CIRC * (1 - pct / 100)
}

// Arc progress scale: 0 / 25 / 50 / 75 / 100
const ARC_PCTS = [0, 25, 50, 75, 100]

// Emphasis arcs
const ARC_EMPHASIS = [
  { key: 'lavender', pct: 63,  label: 'Weekly goal',   ltFill: '#6856B2', dkFill: '#B6A8E6' },
  { key: 'success',  pct: 100, label: 'Tasks done',    ltFill: '#4D8C6A', dkFill: '#6CC498' },
  { key: 'warning',  pct: 85,  label: 'Budget used',   ltFill: '#C48C24', dkFill: '#E6C452' },
  { key: 'failed',   pct: 92,  label: 'Urgency',       ltFill: '#BA4A4A', dkFill: '#E67070' },
]
</script>

<template>
  <ComponentPage
    title="1.6 Progress bar"
    description="Horizontal solid-fill bars for linear progress (task lists, rewards, budgets) + arc gauges for hero moments (dashboard goal rings, weekly stats). Both respect prefers-reduced-motion."
    status="chosen"
  >

    <!-- ══════════════════════════════════════════════════════════════
         SECTION 1 — HORIZONTAL PROGRESS BAR
         Variant A: Solid accent fill
         Variant B: Gradient fill
         Greg picks one after review.
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-20">

      <!-- ─── Horizontal progress bar — solid accent fill ──────────────────────────── -->
      <VariantFrame label="Horizontal" caption="Solid accent fill — editorial, matches the lavender system">
        <div class="w-full space-y-10">

          <!-- LIGHT PANEL A -->
          <div class="rounded-2xl border p-6 space-y-8" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>

            <!-- 1. Size scale at 65% -->
            <div>
              <p class="text-xs mb-4" :style="{ color: L.inkTertiary }">Sizes — sm / md / lg at 65%</p>
              <div class="space-y-3">
                <!-- sm 4px -->
                <div class="flex items-center gap-3">
                  <span class="text-[11px] w-5 text-right flex-shrink-0" :style="{ color: L.inkTertiary }">sm</span>
                  <div class="flex-1 rounded-full overflow-hidden" style="height:4px" :style="{ background: L.trackBg }">
                    <div class="pb-a-lt-fill h-full rounded-full" style="width:65%; background:#6856B2" />
                  </div>
                </div>
                <!-- md 8px -->
                <div class="flex items-center gap-3">
                  <span class="text-[11px] w-5 text-right flex-shrink-0" :style="{ color: L.inkTertiary }">md</span>
                  <div class="flex-1 rounded-full overflow-hidden" style="height:8px" :style="{ background: L.trackBg }">
                    <div class="pb-a-lt-fill h-full rounded-full" style="width:65%; background:#6856B2" />
                  </div>
                </div>
                <!-- lg 12px -->
                <div class="flex items-center gap-3">
                  <span class="text-[11px] w-5 text-right flex-shrink-0" :style="{ color: L.inkTertiary }">lg</span>
                  <div class="flex-1 rounded-full overflow-hidden" style="height:12px" :style="{ background: L.trackBg }">
                    <div class="pb-a-lt-fill h-full rounded-full" style="width:65%; background:#6856B2" />
                  </div>
                </div>
              </div>
            </div>

            <!-- 2. Value scale 0 / 25 / 50 / 75 / 100 -->
            <div>
              <p class="text-xs mb-4" :style="{ color: L.inkTertiary }">Value scale — 0% / 25% / 50% / 75% / 100% at md</p>
              <div class="space-y-3">
                <div v-for="pct in [0, 25, 50, 75, 100]" :key="pct" class="flex items-center gap-3">
                  <span class="text-[11px] w-7 text-right flex-shrink-0" :style="{ color: L.inkTertiary }">{{ pct }}%</span>
                  <div class="flex-1 rounded-full overflow-hidden" style="height:8px" :style="{ background: L.trackBg }">
                    <div
                      class="pb-a-lt-fill h-full rounded-full"
                      :style="{ width: pct + '%', background: '#6856B2' }"
                    />
                  </div>
                </div>
              </div>
              <p class="mt-2 text-[11px]" :style="{ color: L.inkTertiary }">
                0% renders an empty track — no minimum-width fill so zero means zero.
              </p>
            </div>

            <!-- 3. Emphasis colors -->
            <div>
              <p class="text-xs mb-4" :style="{ color: L.inkTertiary }">Emphasis colors — lavender / success / warning / failed at md</p>
              <div class="space-y-3">
                <div v-for="item in EMPHASIS_BARS" :key="item.key" class="flex items-center gap-3">
                  <span class="text-[11px] w-16 flex-shrink-0" :style="{ color: L.inkTertiary }">{{ EMPHASIS[item.key].label }}</span>
                  <div class="flex-1 rounded-full overflow-hidden" style="height:8px" :style="{ background: L.trackBg }">
                    <div
                      class="pb-a-lt-fill h-full rounded-full"
                      :style="{ width: item.pct + '%', background: EMPHASIS[item.key].ltFill }"
                    />
                  </div>
                  <span class="text-[11px] w-8 text-right flex-shrink-0 font-medium" :style="{ color: L.inkPrimary }">{{ item.context }}</span>
                </div>
              </div>
            </div>

            <!-- 4. With label -->
            <div>
              <p class="text-xs mb-4" :style="{ color: L.inkTertiary }">With label — realistic Kinhold contexts</p>
              <div class="space-y-5">
                <div v-for="bar in LABELED_BARS" :key="bar.label">
                  <div class="flex items-baseline justify-between mb-1.5">
                    <span class="text-[13px] font-medium" :style="{ color: L.inkPrimary }">{{ bar.label }}</span>
                    <span class="text-[11px]" :style="{ color: L.inkSecondary }">{{ bar.display }}</span>
                  </div>
                  <div class="w-full rounded-full overflow-hidden" style="height:8px" :style="{ background: L.trackBg }">
                    <div
                      class="pb-a-lt-fill h-full rounded-full"
                      :style="{ width: bar.pct + '%', background: '#6856B2' }"
                    />
                  </div>
                  <p class="mt-1 text-[11px]" :style="{ color: L.inkTertiary }">{{ bar.detail }}</p>
                </div>
              </div>
            </div>

            <!-- 5. Indeterminate -->
            <div>
              <p class="text-xs mb-4" :style="{ color: L.inkTertiary }">Indeterminate — loading / pending state (animated shimmer)</p>
              <div class="w-full rounded-full overflow-hidden" style="height:8px" :style="{ background: L.trackBg }">
                <div class="pb-indeterm-fill-a-lt h-full rounded-full" />
              </div>
              <p class="mt-2 text-[11px]" :style="{ color: L.inkTertiary }">
                Shimmer slides across the track. On prefers-reduced-motion systems the animation stops and the fill sits static at 40%.
              </p>
            </div>
          </div><!-- /light A -->

          <!-- DARK PANEL A -->
          <div class="rounded-2xl border p-6 space-y-8" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

            <!-- 1. Size scale -->
            <div>
              <p class="text-xs mb-4" :style="{ color: D.inkTertiary }">Sizes — sm / md / lg at 65%</p>
              <div class="space-y-3">
                <div v-for="[size, height] in [['sm', 4], ['md', 8], ['lg', 12]]" :key="size" class="flex items-center gap-3">
                  <span class="text-[11px] w-5 text-right flex-shrink-0" :style="{ color: D.inkTertiary }">{{ size }}</span>
                  <div class="flex-1 rounded-full overflow-hidden" :style="{ height: height + 'px', background: D.trackBg }">
                    <div class="pb-a-dk-fill h-full rounded-full" style="width:65%; background:#B6A8E6" />
                  </div>
                </div>
              </div>
            </div>

            <!-- 2. Value scale -->
            <div>
              <p class="text-xs mb-4" :style="{ color: D.inkTertiary }">Value scale — 0% / 25% / 50% / 75% / 100% at md</p>
              <div class="space-y-3">
                <div v-for="pct in [0, 25, 50, 75, 100]" :key="pct" class="flex items-center gap-3">
                  <span class="text-[11px] w-7 text-right flex-shrink-0" :style="{ color: D.inkTertiary }">{{ pct }}%</span>
                  <div class="flex-1 rounded-full overflow-hidden" style="height:8px" :style="{ background: D.trackBg }">
                    <div
                      class="pb-a-dk-fill h-full rounded-full"
                      :style="{ width: pct + '%', background: '#B6A8E6' }"
                    />
                  </div>
                </div>
              </div>
            </div>

            <!-- 3. Emphasis colors -->
            <div>
              <p class="text-xs mb-4" :style="{ color: D.inkTertiary }">Emphasis colors</p>
              <div class="space-y-3">
                <div v-for="item in EMPHASIS_BARS" :key="item.key" class="flex items-center gap-3">
                  <span class="text-[11px] w-16 flex-shrink-0" :style="{ color: D.inkTertiary }">{{ EMPHASIS[item.key].label }}</span>
                  <div class="flex-1 rounded-full overflow-hidden" style="height:8px" :style="{ background: D.trackBg }">
                    <div
                      class="pb-a-dk-fill h-full rounded-full"
                      :style="{ width: item.pct + '%', background: EMPHASIS[item.key].dkFill }"
                    />
                  </div>
                  <span class="text-[11px] w-8 text-right flex-shrink-0 font-medium" :style="{ color: D.inkPrimary }">{{ item.context }}</span>
                </div>
              </div>
            </div>

            <!-- 4. With label -->
            <div>
              <p class="text-xs mb-4" :style="{ color: D.inkTertiary }">With label</p>
              <div class="space-y-5">
                <div v-for="bar in LABELED_BARS" :key="bar.label">
                  <div class="flex items-baseline justify-between mb-1.5">
                    <span class="text-[13px] font-medium" :style="{ color: D.inkPrimary }">{{ bar.label }}</span>
                    <span class="text-[11px]" :style="{ color: D.inkSecondary }">{{ bar.display }}</span>
                  </div>
                  <div class="w-full rounded-full overflow-hidden" style="height:8px" :style="{ background: D.trackBg }">
                    <div
                      class="pb-a-dk-fill h-full rounded-full"
                      :style="{ width: bar.pct + '%', background: '#B6A8E6' }"
                    />
                  </div>
                  <p class="mt-1 text-[11px]" :style="{ color: D.inkTertiary }">{{ bar.detail }}</p>
                </div>
              </div>
            </div>

            <!-- 5. Indeterminate -->
            <div>
              <p class="text-xs mb-4" :style="{ color: D.inkTertiary }">Indeterminate</p>
              <div class="w-full rounded-full overflow-hidden" style="height:8px" :style="{ background: D.trackBg }">
                <div class="pb-indeterm-fill-a-dk h-full rounded-full" />
              </div>
            </div>
          </div><!-- /dark A -->
        </div>
      </VariantFrame>

    </section>


    <!-- ══════════════════════════════════════════════════════════════
         SECTION 2 — ARC GAUGE (single locked treatment)
         Standalone progress arc. Not avatar-bound.
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-20">
      <VariantFrame label="Arc gauge" caption="Circular gauge for hero moments — weekly goals, budget rings, reward progress">
        <div class="w-full space-y-10">

          <!-- LIGHT PANEL Arc -->
          <div class="rounded-2xl border p-6 space-y-10" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>

            <!-- 1. Size scale: sm (80px) / md (120px) / lg (180px) at 63% -->
            <div>
              <p class="text-xs mb-5" :style="{ color: L.inkTertiary }">Sizes — sm (80px) / md (120px) / lg (180px) at 63%</p>
              <div class="flex flex-wrap items-end gap-8">
                <!-- sm -->
                <div class="flex flex-col items-center gap-2">
                  <div class="arc-gauge-lt relative flex-shrink-0" style="width:80px; height:80px">
                    <svg class="absolute inset-0 w-full h-full" style="transform:rotate(-90deg)" viewBox="0 0 100 100">
                      <circle cx="50" cy="50" :r="ARC_R" fill="none" stroke="#F5F2EE" stroke-width="6" />
                      <circle cx="50" cy="50" :r="ARC_R" fill="none" stroke="#6856B2" stroke-width="6"
                        :stroke-dasharray="ARC_CIRC" :stroke-dashoffset="arcOffset(63)"
                        stroke-linecap="round" class="arc-progress" />
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center" style="pointer-events:none">
                      <span class="font-semibold leading-none" style="font-size:20px; color:#1C1C1E">63<span style="font-size:12px">%</span></span>
                      <span style="font-size:9px; color:#9C9895; margin-top:1px">goal</span>
                    </div>
                  </div>
                  <span class="text-[10px]" :style="{ color: L.inkTertiary }">sm 80px</span>
                </div>
                <!-- md -->
                <div class="flex flex-col items-center gap-2">
                  <div class="arc-gauge-lt relative flex-shrink-0" style="width:120px; height:120px">
                    <svg class="absolute inset-0 w-full h-full" style="transform:rotate(-90deg)" viewBox="0 0 100 100">
                      <circle cx="50" cy="50" :r="ARC_R" fill="none" stroke="#F5F2EE" stroke-width="8" />
                      <circle cx="50" cy="50" :r="ARC_R" fill="none" stroke="#6856B2" stroke-width="8"
                        :stroke-dasharray="ARC_CIRC" :stroke-dashoffset="arcOffset(63)"
                        stroke-linecap="round" class="arc-progress" />
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center" style="pointer-events:none">
                      <span class="font-semibold leading-none" style="font-size:30px; color:#1C1C1E">63<span style="font-size:16px">%</span></span>
                      <span style="font-size:11px; color:#9C9895; margin-top:2px">of weekly goal</span>
                    </div>
                  </div>
                  <span class="text-[10px]" :style="{ color: L.inkTertiary }">md 120px</span>
                </div>
                <!-- lg -->
                <div class="flex flex-col items-center gap-2">
                  <div class="arc-gauge-lt relative flex-shrink-0" style="width:180px; height:180px">
                    <svg class="absolute inset-0 w-full h-full" style="transform:rotate(-90deg)" viewBox="0 0 100 100">
                      <circle cx="50" cy="50" :r="ARC_R" fill="none" stroke="#F5F2EE" stroke-width="10" />
                      <circle cx="50" cy="50" :r="ARC_R" fill="none" stroke="#6856B2" stroke-width="10"
                        :stroke-dasharray="ARC_CIRC" :stroke-dashoffset="arcOffset(63)"
                        stroke-linecap="round" class="arc-progress" />
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center" style="pointer-events:none">
                      <span class="font-semibold leading-none" style="font-size:46px; color:#1C1C1E">63<span style="font-size:24px">%</span></span>
                      <span style="font-size:13px; color:#9C9895; margin-top:3px">of weekly goal</span>
                    </div>
                  </div>
                  <span class="text-[10px]" :style="{ color: L.inkTertiary }">lg 180px</span>
                </div>
              </div>
            </div>

            <!-- 2. Progress scale 0 / 25 / 50 / 75 / 100 at md -->
            <div>
              <p class="text-xs mb-5" :style="{ color: L.inkTertiary }">Progress scale — 0% / 25% / 50% / 75% / 100% at md</p>
              <div class="flex flex-wrap items-end gap-6">
                <div v-for="pct in ARC_PCTS" :key="pct" class="flex flex-col items-center gap-2">
                  <div class="arc-gauge-lt relative flex-shrink-0" style="width:100px; height:100px">
                    <svg class="absolute inset-0 w-full h-full" style="transform:rotate(-90deg)" viewBox="0 0 100 100">
                      <circle cx="50" cy="50" :r="ARC_R" fill="none" stroke="#F5F2EE" stroke-width="8" />
                      <circle cx="50" cy="50" :r="ARC_R" fill="none" stroke="#6856B2" stroke-width="8"
                        :stroke-dasharray="ARC_CIRC" :stroke-dashoffset="arcOffset(pct)"
                        :stroke-linecap="pct === 100 ? 'butt' : 'round'"
                        class="arc-progress" />
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center" style="pointer-events:none">
                      <span class="font-semibold leading-none" style="font-size:26px; color:#1C1C1E">{{ pct }}<span style="font-size:14px">%</span></span>
                    </div>
                  </div>
                  <span class="text-[10px]" :style="{ color: L.inkTertiary }">{{ pct }}%</span>
                </div>
              </div>
              <p class="mt-3 text-[11px]" :style="{ color: L.inkTertiary }">
                At 100%, stroke-linecap switches to "butt" — the arc closes into a seamless solid ring with no end-cap bleed.
              </p>
            </div>

            <!-- 3. Emphasis colors at md -->
            <div>
              <p class="text-xs mb-5" :style="{ color: L.inkTertiary }">Emphasis colors at md</p>
              <div class="flex flex-wrap items-end gap-6">
                <div v-for="arc in ARC_EMPHASIS" :key="arc.key" class="flex flex-col items-center gap-2">
                  <div class="arc-gauge-lt relative flex-shrink-0" style="width:100px; height:100px">
                    <svg class="absolute inset-0 w-full h-full" style="transform:rotate(-90deg)" viewBox="0 0 100 100">
                      <circle cx="50" cy="50" :r="ARC_R" fill="none" stroke="#F5F2EE" stroke-width="8" />
                      <circle cx="50" cy="50" :r="ARC_R" fill="none" :stroke="arc.ltFill" stroke-width="8"
                        :stroke-dasharray="ARC_CIRC" :stroke-dashoffset="arcOffset(arc.pct)"
                        :stroke-linecap="arc.pct === 100 ? 'butt' : 'round'"
                        class="arc-progress" />
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center" style="pointer-events:none">
                      <span class="font-semibold leading-none" :style="{ fontSize: '26px', color: arc.ltFill }">{{ arc.pct }}<span style="font-size:13px">%</span></span>
                      <span style="font-size:10px; color:#9C9895; margin-top:2px">{{ arc.label }}</span>
                    </div>
                  </div>
                  <span class="text-[10px]" :style="{ color: L.inkTertiary }">{{ EMPHASIS[arc.key].label }}</span>
                </div>
              </div>
            </div>

            <!-- 4. Hero context (lg, featured presentation) -->
            <div>
              <p class="text-xs mb-5" :style="{ color: L.inkTertiary }">Hero context — featured dashboard moment (lg)</p>
              <div class="flex flex-col items-center max-w-xs mx-auto text-center gap-4">
                <div class="arc-gauge-lt relative flex-shrink-0" style="width:180px; height:180px">
                  <svg class="absolute inset-0 w-full h-full" style="transform:rotate(-90deg)" viewBox="0 0 100 100">
                    <circle cx="50" cy="50" :r="ARC_R" fill="none" stroke="#F5F2EE" stroke-width="10" />
                    <circle cx="50" cy="50" :r="ARC_R" fill="none" stroke="#6856B2" stroke-width="10"
                      :stroke-dasharray="ARC_CIRC" :stroke-dashoffset="arcOffset(63)"
                      stroke-linecap="round" class="arc-progress" />
                  </svg>
                  <div class="absolute inset-0 flex flex-col items-center justify-center" style="pointer-events:none">
                    <span class="font-semibold leading-none" style="font-size:46px; color:#1C1C1E">63<span style="font-size:24px">%</span></span>
                    <span style="font-size:13px; color:#9C9895; margin-top:3px">this week</span>
                  </div>
                </div>
                <div>
                  <p class="text-[17px] font-semibold" :style="{ color: L.inkPrimary }">Weekly goal</p>
                  <p class="text-[13px] leading-snug mt-1" :style="{ color: L.inkSecondary }">
                    You're 8 tasks away from completing this week's goal. Keep it up.
                  </p>
                </div>
              </div>
            </div>
          </div><!-- /light arc -->

          <!-- DARK PANEL Arc -->
          <div class="rounded-2xl border p-6 space-y-10" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

            <!-- 1. Size scale dark -->
            <div>
              <p class="text-xs mb-5" :style="{ color: D.inkTertiary }">Sizes — sm (80px) / md (120px) / lg (180px) at 63%</p>
              <div class="flex flex-wrap items-end gap-8">
                <!-- sm -->
                <div class="flex flex-col items-center gap-2">
                  <div class="arc-gauge-dk relative flex-shrink-0" style="width:80px; height:80px">
                    <svg class="absolute inset-0 w-full h-full" style="transform:rotate(-90deg)" viewBox="0 0 100 100">
                      <circle cx="50" cy="50" :r="ARC_R" fill="none" :stroke="D.trackBg" stroke-width="6" />
                      <circle cx="50" cy="50" :r="ARC_R" fill="none" stroke="#B6A8E6" stroke-width="6"
                        :stroke-dasharray="ARC_CIRC" :stroke-dashoffset="arcOffset(63)"
                        stroke-linecap="round" class="arc-progress" />
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center" style="pointer-events:none">
                      <span class="font-semibold leading-none" style="font-size:20px; color:#F0EDE9">63<span style="font-size:12px">%</span></span>
                      <span style="font-size:9px; color:#6E6B67; margin-top:1px">goal</span>
                    </div>
                  </div>
                  <span class="text-[10px]" :style="{ color: D.inkTertiary }">sm 80px</span>
                </div>
                <!-- md -->
                <div class="flex flex-col items-center gap-2">
                  <div class="arc-gauge-dk relative flex-shrink-0" style="width:120px; height:120px">
                    <svg class="absolute inset-0 w-full h-full" style="transform:rotate(-90deg)" viewBox="0 0 100 100">
                      <circle cx="50" cy="50" :r="ARC_R" fill="none" :stroke="D.trackBg" stroke-width="8" />
                      <circle cx="50" cy="50" :r="ARC_R" fill="none" stroke="#B6A8E6" stroke-width="8"
                        :stroke-dasharray="ARC_CIRC" :stroke-dashoffset="arcOffset(63)"
                        stroke-linecap="round" class="arc-progress" />
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center" style="pointer-events:none">
                      <span class="font-semibold leading-none" style="font-size:30px; color:#F0EDE9">63<span style="font-size:16px">%</span></span>
                      <span style="font-size:11px; color:#6E6B67; margin-top:2px">of weekly goal</span>
                    </div>
                  </div>
                  <span class="text-[10px]" :style="{ color: D.inkTertiary }">md 120px</span>
                </div>
                <!-- lg -->
                <div class="flex flex-col items-center gap-2">
                  <div class="arc-gauge-dk relative flex-shrink-0" style="width:180px; height:180px">
                    <svg class="absolute inset-0 w-full h-full" style="transform:rotate(-90deg)" viewBox="0 0 100 100">
                      <circle cx="50" cy="50" :r="ARC_R" fill="none" :stroke="D.trackBg" stroke-width="10" />
                      <circle cx="50" cy="50" :r="ARC_R" fill="none" stroke="#B6A8E6" stroke-width="10"
                        :stroke-dasharray="ARC_CIRC" :stroke-dashoffset="arcOffset(63)"
                        stroke-linecap="round" class="arc-progress" />
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center" style="pointer-events:none">
                      <span class="font-semibold leading-none" style="font-size:46px; color:#F0EDE9">63<span style="font-size:24px">%</span></span>
                      <span style="font-size:13px; color:#6E6B67; margin-top:3px">of weekly goal</span>
                    </div>
                  </div>
                  <span class="text-[10px]" :style="{ color: D.inkTertiary }">lg 180px</span>
                </div>
              </div>
            </div>

            <!-- 2. Progress scale dark -->
            <div>
              <p class="text-xs mb-5" :style="{ color: D.inkTertiary }">Progress scale — 0% / 25% / 50% / 75% / 100%</p>
              <div class="flex flex-wrap items-end gap-6">
                <div v-for="pct in ARC_PCTS" :key="pct" class="flex flex-col items-center gap-2">
                  <div class="arc-gauge-dk relative flex-shrink-0" style="width:100px; height:100px">
                    <svg class="absolute inset-0 w-full h-full" style="transform:rotate(-90deg)" viewBox="0 0 100 100">
                      <circle cx="50" cy="50" :r="ARC_R" fill="none" :stroke="D.trackBg" stroke-width="8" />
                      <circle cx="50" cy="50" :r="ARC_R" fill="none" stroke="#B6A8E6" stroke-width="8"
                        :stroke-dasharray="ARC_CIRC" :stroke-dashoffset="arcOffset(pct)"
                        :stroke-linecap="pct === 100 ? 'butt' : 'round'"
                        class="arc-progress" />
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center" style="pointer-events:none">
                      <span class="font-semibold leading-none" style="font-size:26px; color:#F0EDE9">{{ pct }}<span style="font-size:14px">%</span></span>
                    </div>
                  </div>
                  <span class="text-[10px]" :style="{ color: D.inkTertiary }">{{ pct }}%</span>
                </div>
              </div>
            </div>

            <!-- 3. Emphasis dark -->
            <div>
              <p class="text-xs mb-5" :style="{ color: D.inkTertiary }">Emphasis colors at md</p>
              <div class="flex flex-wrap items-end gap-6">
                <div v-for="arc in ARC_EMPHASIS" :key="arc.key" class="flex flex-col items-center gap-2">
                  <div class="arc-gauge-dk relative flex-shrink-0" style="width:100px; height:100px">
                    <svg class="absolute inset-0 w-full h-full" style="transform:rotate(-90deg)" viewBox="0 0 100 100">
                      <circle cx="50" cy="50" :r="ARC_R" fill="none" :stroke="D.trackBg" stroke-width="8" />
                      <circle cx="50" cy="50" :r="ARC_R" fill="none" :stroke="arc.dkFill" stroke-width="8"
                        :stroke-dasharray="ARC_CIRC" :stroke-dashoffset="arcOffset(arc.pct)"
                        :stroke-linecap="arc.pct === 100 ? 'butt' : 'round'"
                        class="arc-progress" />
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center" style="pointer-events:none">
                      <span class="font-semibold leading-none" :style="{ fontSize: '26px', color: arc.dkFill }">{{ arc.pct }}<span style="font-size:13px">%</span></span>
                      <span style="font-size:10px; color:#6E6B67; margin-top:2px">{{ arc.label }}</span>
                    </div>
                  </div>
                  <span class="text-[10px]" :style="{ color: D.inkTertiary }">{{ EMPHASIS[arc.key].label }}</span>
                </div>
              </div>
            </div>

            <!-- 4. Hero context dark -->
            <div>
              <p class="text-xs mb-5" :style="{ color: D.inkTertiary }">Hero context — featured dashboard moment (lg)</p>
              <div class="flex flex-col items-center max-w-xs mx-auto text-center gap-4">
                <div class="arc-gauge-dk relative flex-shrink-0" style="width:180px; height:180px">
                  <svg class="absolute inset-0 w-full h-full" style="transform:rotate(-90deg)" viewBox="0 0 100 100">
                    <circle cx="50" cy="50" :r="ARC_R" fill="none" :stroke="D.trackBg" stroke-width="10" />
                    <circle cx="50" cy="50" :r="ARC_R" fill="none" stroke="#B6A8E6" stroke-width="10"
                      :stroke-dasharray="ARC_CIRC" :stroke-dashoffset="arcOffset(63)"
                      stroke-linecap="round" class="arc-progress" />
                  </svg>
                  <div class="absolute inset-0 flex flex-col items-center justify-center" style="pointer-events:none">
                    <span class="font-semibold leading-none" style="font-size:46px; color:#F0EDE9">63<span style="font-size:24px">%</span></span>
                    <span style="font-size:13px; color:#6E6B67; margin-top:3px">this week</span>
                  </div>
                </div>
                <div>
                  <p class="text-[17px] font-semibold" :style="{ color: D.inkPrimary }">Weekly goal</p>
                  <p class="text-[13px] leading-snug mt-1" :style="{ color: D.inkSecondary }">
                    You're 8 tasks away from completing this week's goal. Keep it up.
                  </p>
                </div>
              </div>
            </div>
          </div><!-- /dark arc -->
        </div>
      </VariantFrame>
    </section>


    <!-- ══════════════════════════════════════════════════════════════
         SECTION 3 — USAGE GUIDE
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-8">
      <div class="rounded-2xl border p-6 space-y-4" :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle }">
        <h2 class="text-[17px] font-semibold" :style="{ color: L.inkPrimary }">When to use which</h2>
        <ul class="space-y-3 text-[14px]" :style="{ color: L.inkSecondary }">
          <li>
            <strong :style="{ color: L.inkPrimary }">Horizontal bar:</strong>
            Linear progress through a known quantity. Task completion, form steps, points-toward-reward, budget-used. Default choice for anything where the start and end are fixed. Use md (8px) as the default height — sm for dense rows, lg for featured callouts.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Arc gauge:</strong>
            Circular gauge for hero moments — dashboard "you're 63% there" tiles, weekly-goal rings, monthly-budget donuts. Always pair with a hero number inside the arc. Use sparingly; reserve for key metrics that benefit from extra visual weight. lg (180px) for featured hero tiles, md (120px) for widget grids.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Emphasis colors:</strong>
            Use lavender (default) for general progress. Use success when 100% = completed. Use warning for caution thresholds (85%+ budget, late tasks). Use failed for urgency or overdue states. Don't mix emphasis colors on the same screen without a clear semantic reason.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Indeterminate:</strong>
            Use when the duration is unknown (loading data, syncing calendars, running an AI action). The sliding shimmer conveys activity without implying a specific completion percentage. On reduced-motion systems a static 40% fill replaces the animation — no spinner-style fallback needed.
          </li>
        </ul>
      </div>
    </section>

  </ComponentPage>
</template>

<style scoped>
/*
  ═══════════════════════════════════════════════════════════════
  HORIZONTAL BAR A — LIGHT
  Solid fill, smooth width transition
  ═══════════════════════════════════════════════════════════════
*/
.pb-a-lt-fill {
  transition: width 500ms cubic-bezier(0.16, 1, 0.3, 1);
}

/*
  ═══════════════════════════════════════════════════════════════
  HORIZONTAL BAR A — DARK
  ═══════════════════════════════════════════════════════════════
*/
.pb-a-dk-fill {
  transition: width 500ms cubic-bezier(0.16, 1, 0.3, 1);
}

/*
  ═══════════════════════════════════════════════════════════════
  INDETERMINATE — Variant A, Light
  Solid lavender shimmer, 1.5s linear loop
  ═══════════════════════════════════════════════════════════════
*/
@keyframes pb-shimmer {
  from { transform: translateX(-100%); }
  to   { transform: translateX(250%); }
}

.pb-indeterm-fill-a-lt {
  width: 40%;
  background: #6856B2;
  animation: pb-shimmer 1.5s linear infinite;
}

@media (prefers-reduced-motion: reduce) {
  .pb-indeterm-fill-a-lt {
    animation: none;
    margin-left: 30%;  /* static mid position */
  }
}

/*
  ═══════════════════════════════════════════════════════════════
  INDETERMINATE — Variant A, Dark
  ═══════════════════════════════════════════════════════════════
*/
.pb-indeterm-fill-a-dk {
  width: 40%;
  background: #B6A8E6;
  animation: pb-shimmer 1.5s linear infinite;
}

@media (prefers-reduced-motion: reduce) {
  .pb-indeterm-fill-a-dk {
    animation: none;
    margin-left: 30%;
  }
}
/*
  ═══════════════════════════════════════════════════════════════
  ARC GAUGE — Light panel hover
  ═══════════════════════════════════════════════════════════════
*/
.arc-gauge-lt {
  transition: transform 200ms;
}
.arc-gauge-lt:hover {
  transform: scale(1.03);
}

/*
  ═══════════════════════════════════════════════════════════════
  ARC GAUGE — Dark panel hover
  ═══════════════════════════════════════════════════════════════
*/
.arc-gauge-dk {
  transition: transform 200ms, filter 200ms;
}
.arc-gauge-dk:hover {
  transform: scale(1.03);
  filter: brightness(1.1);
}

/*
  ═══════════════════════════════════════════════════════════════
  ARC PROGRESS — stroke-dashoffset transition (800ms ease-out)
  Applied on the progress circle in both light and dark panels.
  ═══════════════════════════════════════════════════════════════
*/
.arc-progress {
  transition: stroke-dashoffset 800ms cubic-bezier(0.16, 1, 0.3, 1);
}

@media (prefers-reduced-motion: reduce) {
  .arc-progress {
    transition: none;
  }
}
</style>
