<script setup>
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import KinProgressBar from '@/components/design-system/KinProgressBar.vue'
import KinProgressArc from '@/components/design-system/KinProgressArc.vue'

// ── Light/dark panel chrome only ─────────────────────────────────────────────
// Panel wrappers still use explicit surface/ink values so the hand-rolled
// "Light mode" / "Dark mode" framing is consistent with the rest of the
// design-system (the panel IS the demo surface — it doesn't adopt root theme).
const L = {
  surfaceApp:    '#FAF8F5',
  surfaceRaised: '#FFFFFF',
  inkPrimary:    '#1C1C1E',
  inkSecondary:  '#6B6966',
  inkTertiary:   '#9C9895',
  borderSubtle:  '#E8E4DF',
}
const D = {
  surfaceApp:    '#141311',
  inkPrimary:    '#F0EDE9',
  inkSecondary:  '#A09C97',
  inkTertiary:   '#6E6B67',
  borderSubtle:  '#2C2A27',
}

// ── Demo data ────────────────────────────────────────────────────────────────
const EMPHASIS = {
  lavender: { label: 'Lavender' },
  success:  { label: 'Success'  },
  warning:  { label: 'Warning'  },
  failed:   { label: 'Failed'   },
}

const LABELED_BARS = [
  { label: 'Weekly tasks', detail: '8 of 12 completed', pct: 67 },
  { label: 'Points toward next reward', detail: '240 / 500', pct: 48 },
  { label: 'Budget for groceries', detail: '$287 / $400', pct: 72 },
]

const EMPHASIS_BARS = [
  { key: 'lavender', pct: 63 },
  { key: 'success',  pct: 100 },
  { key: 'warning',  pct: 85 },
  { key: 'failed',   pct: 92 },
]

const ARC_PCTS = [0, 25, 50, 75, 100]

const ARC_EMPHASIS = [
  { key: 'lavender', pct: 63,  label: 'Weekly goal' },
  { key: 'success',  pct: 100, label: 'Tasks done'  },
  { key: 'warning',  pct: 85,  label: 'Budget used' },
  { key: 'failed',   pct: 92,  label: 'Urgency'     },
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
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-20">

      <VariantFrame label="Horizontal" caption="Solid accent fill — editorial, matches the lavender system">
        <div class="w-full space-y-10">

          <!-- LIGHT PANEL -->
          <div class="rounded-2xl border p-6 space-y-8" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>

            <!-- 1. Size scale at 65% -->
            <div>
              <p class="text-xs mb-4" :style="{ color: L.inkTertiary }">Sizes — sm / md / lg at 65%</p>
              <div class="space-y-3">
                <div v-for="s in ['sm', 'md', 'lg']" :key="s" class="flex items-center gap-3">
                  <span class="text-[11px] w-5 text-right flex-shrink-0" :style="{ color: L.inkTertiary }">{{ s }}</span>
                  <KinProgressBar class="flex-1" :value="65" :size="s" color="lavender" />
                </div>
              </div>
            </div>

            <!-- 2. Value scale -->
            <div>
              <p class="text-xs mb-4" :style="{ color: L.inkTertiary }">Value scale — 0% / 25% / 50% / 75% / 100% at md</p>
              <div class="space-y-3">
                <div v-for="pct in [0, 25, 50, 75, 100]" :key="pct" class="flex items-center gap-3">
                  <span class="text-[11px] w-7 text-right flex-shrink-0" :style="{ color: L.inkTertiary }">{{ pct }}%</span>
                  <KinProgressBar class="flex-1" :value="pct" size="md" color="lavender" />
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
                  <KinProgressBar class="flex-1" :value="item.pct" size="md" :color="item.key" />
                  <span class="text-[11px] w-8 text-right flex-shrink-0 font-medium" :style="{ color: L.inkPrimary }">{{ item.pct }}%</span>
                </div>
              </div>
            </div>

            <!-- 4. With label -->
            <div>
              <p class="text-xs mb-4" :style="{ color: L.inkTertiary }">With label — realistic Kinhold contexts</p>
              <div class="space-y-5">
                <div v-for="bar in LABELED_BARS" :key="bar.label">
                  <KinProgressBar :value="bar.pct" size="md" color="lavender" :label="bar.label" showValue />
                  <p class="mt-1 text-[11px]" :style="{ color: L.inkTertiary }">{{ bar.detail }}</p>
                </div>
              </div>
            </div>

            <!-- 5. Indeterminate -->
            <div>
              <p class="text-xs mb-4" :style="{ color: L.inkTertiary }">Indeterminate — loading / pending state (animated shimmer)</p>
              <KinProgressBar indeterminate size="md" color="lavender" />
              <p class="mt-2 text-[11px]" :style="{ color: L.inkTertiary }">
                Shimmer slides across the track. On prefers-reduced-motion systems the animation stops and the fill sits static at 40%.
              </p>
            </div>
          </div><!-- /light -->

          <!-- DARK PANEL -->
          <div class="dark rounded-2xl border p-6 space-y-8" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

            <!-- 1. Size scale -->
            <div>
              <p class="text-xs mb-4" :style="{ color: D.inkTertiary }">Sizes — sm / md / lg at 65%</p>
              <div class="space-y-3">
                <div v-for="s in ['sm', 'md', 'lg']" :key="s" class="flex items-center gap-3">
                  <span class="text-[11px] w-5 text-right flex-shrink-0" :style="{ color: D.inkTertiary }">{{ s }}</span>
                  <KinProgressBar class="flex-1" :value="65" :size="s" color="lavender" />
                </div>
              </div>
            </div>

            <!-- 2. Value scale -->
            <div>
              <p class="text-xs mb-4" :style="{ color: D.inkTertiary }">Value scale — 0% / 25% / 50% / 75% / 100% at md</p>
              <div class="space-y-3">
                <div v-for="pct in [0, 25, 50, 75, 100]" :key="pct" class="flex items-center gap-3">
                  <span class="text-[11px] w-7 text-right flex-shrink-0" :style="{ color: D.inkTertiary }">{{ pct }}%</span>
                  <KinProgressBar class="flex-1" :value="pct" size="md" color="lavender" />
                </div>
              </div>
            </div>

            <!-- 3. Emphasis colors -->
            <div>
              <p class="text-xs mb-4" :style="{ color: D.inkTertiary }">Emphasis colors</p>
              <div class="space-y-3">
                <div v-for="item in EMPHASIS_BARS" :key="item.key" class="flex items-center gap-3">
                  <span class="text-[11px] w-16 flex-shrink-0" :style="{ color: D.inkTertiary }">{{ EMPHASIS[item.key].label }}</span>
                  <KinProgressBar class="flex-1" :value="item.pct" size="md" :color="item.key" />
                  <span class="text-[11px] w-8 text-right flex-shrink-0 font-medium" :style="{ color: D.inkPrimary }">{{ item.pct }}%</span>
                </div>
              </div>
            </div>

            <!-- 4. With label -->
            <div>
              <p class="text-xs mb-4" :style="{ color: D.inkTertiary }">With label</p>
              <div class="space-y-5">
                <div v-for="bar in LABELED_BARS" :key="bar.label">
                  <KinProgressBar :value="bar.pct" size="md" color="lavender" :label="bar.label" showValue />
                  <p class="mt-1 text-[11px]" :style="{ color: D.inkTertiary }">{{ bar.detail }}</p>
                </div>
              </div>
            </div>

            <!-- 5. Indeterminate -->
            <div>
              <p class="text-xs mb-4" :style="{ color: D.inkTertiary }">Indeterminate</p>
              <KinProgressBar indeterminate size="md" color="lavender" />
            </div>
          </div><!-- /dark -->
        </div>
      </VariantFrame>

    </section>


    <!-- ══════════════════════════════════════════════════════════════
         SECTION 2 — ARC GAUGE
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-20">
      <VariantFrame label="Arc gauge" caption="Circular gauge for hero moments — weekly goals, budget rings, reward progress">
        <div class="w-full space-y-10">

          <!-- LIGHT PANEL -->
          <div class="rounded-2xl border p-6 space-y-10" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>

            <!-- 1. Size scale -->
            <div>
              <p class="text-xs mb-5" :style="{ color: L.inkTertiary }">Sizes — sm (80px) / md (120px) / lg (180px) at 63%</p>
              <div class="flex flex-wrap items-end gap-8">
                <!-- sm 80 -->
                <div class="flex flex-col items-center gap-2">
                  <KinProgressArc :value="63" :size="80" :thickness="6" color="lavender">
                    <template #center>
                      <span class="font-semibold leading-none" style="font-size:20px; color:#1C1C1E">63<span style="font-size:12px">%</span></span>
                      <span style="font-size:9px; color:#9C9895; margin-top:1px">goal</span>
                    </template>
                  </KinProgressArc>
                  <span class="text-[10px]" :style="{ color: L.inkTertiary }">sm 80px</span>
                </div>
                <!-- md 120 -->
                <div class="flex flex-col items-center gap-2">
                  <KinProgressArc :value="63" :size="120" :thickness="8" color="lavender">
                    <template #center>
                      <span class="font-semibold leading-none" style="font-size:30px; color:#1C1C1E">63<span style="font-size:16px">%</span></span>
                      <span style="font-size:11px; color:#9C9895; margin-top:2px">of weekly goal</span>
                    </template>
                  </KinProgressArc>
                  <span class="text-[10px]" :style="{ color: L.inkTertiary }">md 120px</span>
                </div>
                <!-- lg 180 -->
                <div class="flex flex-col items-center gap-2">
                  <KinProgressArc :value="63" :size="180" :thickness="10" color="lavender">
                    <template #center>
                      <span class="font-semibold leading-none" style="font-size:46px; color:#1C1C1E">63<span style="font-size:24px">%</span></span>
                      <span style="font-size:13px; color:#9C9895; margin-top:3px">of weekly goal</span>
                    </template>
                  </KinProgressArc>
                  <span class="text-[10px]" :style="{ color: L.inkTertiary }">lg 180px</span>
                </div>
              </div>
            </div>

            <!-- 2. Progress scale -->
            <div>
              <p class="text-xs mb-5" :style="{ color: L.inkTertiary }">Progress scale — 0% / 25% / 50% / 75% / 100% at md</p>
              <div class="flex flex-wrap items-end gap-6">
                <div v-for="pct in ARC_PCTS" :key="pct" class="flex flex-col items-center gap-2">
                  <KinProgressArc :value="pct" :size="100" :thickness="8" color="lavender">
                    <template #center>
                      <span class="font-semibold leading-none" style="font-size:26px; color:#1C1C1E">{{ pct }}<span style="font-size:14px">%</span></span>
                    </template>
                  </KinProgressArc>
                  <span class="text-[10px]" :style="{ color: L.inkTertiary }">{{ pct }}%</span>
                </div>
              </div>
              <p class="mt-3 text-[11px]" :style="{ color: L.inkTertiary }">
                At 100%, stroke-linecap switches to "butt" — the arc closes into a seamless solid ring with no end-cap bleed.
              </p>
            </div>

            <!-- 3. Emphasis colors -->
            <div>
              <p class="text-xs mb-5" :style="{ color: L.inkTertiary }">Emphasis colors at md</p>
              <div class="flex flex-wrap items-end gap-6">
                <div v-for="arc in ARC_EMPHASIS" :key="arc.key" class="flex flex-col items-center gap-2">
                  <KinProgressArc :value="arc.pct" :size="100" :thickness="8" :color="arc.key">
                    <template #center>
                      <span class="font-semibold leading-none" style="font-size:26px" :style="{ color: arc.key === 'lavender' ? '#6856B2' : arc.key === 'success' ? '#4D8C6A' : arc.key === 'warning' ? '#C48C24' : '#BA4A4A' }">{{ arc.pct }}<span style="font-size:13px">%</span></span>
                      <span style="font-size:10px; color:#9C9895; margin-top:2px">{{ arc.label }}</span>
                    </template>
                  </KinProgressArc>
                  <span class="text-[10px]" :style="{ color: L.inkTertiary }">{{ EMPHASIS[arc.key].label }}</span>
                </div>
              </div>
            </div>

            <!-- 4. Hero context -->
            <div>
              <p class="text-xs mb-5" :style="{ color: L.inkTertiary }">Hero context — featured dashboard moment (lg)</p>
              <div class="flex flex-col items-center max-w-xs mx-auto text-center gap-4">
                <KinProgressArc :value="63" :size="180" :thickness="10" color="lavender">
                  <template #center>
                    <span class="font-semibold leading-none" style="font-size:46px; color:#1C1C1E">63<span style="font-size:24px">%</span></span>
                    <span style="font-size:13px; color:#9C9895; margin-top:3px">this week</span>
                  </template>
                </KinProgressArc>
                <div>
                  <p class="text-[17px] font-semibold" :style="{ color: L.inkPrimary }">Weekly goal</p>
                  <p class="text-[13px] leading-snug mt-1" :style="{ color: L.inkSecondary }">
                    You're 8 tasks away from completing this week's goal. Keep it up.
                  </p>
                </div>
              </div>
            </div>
          </div><!-- /light -->

          <!-- DARK PANEL -->
          <div class="dark rounded-2xl border p-6 space-y-10" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

            <!-- 1. Size scale -->
            <div>
              <p class="text-xs mb-5" :style="{ color: D.inkTertiary }">Sizes — sm (80px) / md (120px) / lg (180px) at 63%</p>
              <div class="flex flex-wrap items-end gap-8">
                <div class="flex flex-col items-center gap-2">
                  <KinProgressArc :value="63" :size="80" :thickness="6" color="lavender">
                    <template #center>
                      <span class="font-semibold leading-none" style="font-size:20px; color:#F0EDE9">63<span style="font-size:12px">%</span></span>
                      <span style="font-size:9px; color:#6E6B67; margin-top:1px">goal</span>
                    </template>
                  </KinProgressArc>
                  <span class="text-[10px]" :style="{ color: D.inkTertiary }">sm 80px</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <KinProgressArc :value="63" :size="120" :thickness="8" color="lavender">
                    <template #center>
                      <span class="font-semibold leading-none" style="font-size:30px; color:#F0EDE9">63<span style="font-size:16px">%</span></span>
                      <span style="font-size:11px; color:#6E6B67; margin-top:2px">of weekly goal</span>
                    </template>
                  </KinProgressArc>
                  <span class="text-[10px]" :style="{ color: D.inkTertiary }">md 120px</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <KinProgressArc :value="63" :size="180" :thickness="10" color="lavender">
                    <template #center>
                      <span class="font-semibold leading-none" style="font-size:46px; color:#F0EDE9">63<span style="font-size:24px">%</span></span>
                      <span style="font-size:13px; color:#6E6B67; margin-top:3px">of weekly goal</span>
                    </template>
                  </KinProgressArc>
                  <span class="text-[10px]" :style="{ color: D.inkTertiary }">lg 180px</span>
                </div>
              </div>
            </div>

            <!-- 2. Progress scale -->
            <div>
              <p class="text-xs mb-5" :style="{ color: D.inkTertiary }">Progress scale — 0% / 25% / 50% / 75% / 100%</p>
              <div class="flex flex-wrap items-end gap-6">
                <div v-for="pct in ARC_PCTS" :key="pct" class="flex flex-col items-center gap-2">
                  <KinProgressArc :value="pct" :size="100" :thickness="8" color="lavender">
                    <template #center>
                      <span class="font-semibold leading-none" style="font-size:26px; color:#F0EDE9">{{ pct }}<span style="font-size:14px">%</span></span>
                    </template>
                  </KinProgressArc>
                  <span class="text-[10px]" :style="{ color: D.inkTertiary }">{{ pct }}%</span>
                </div>
              </div>
            </div>

            <!-- 3. Emphasis colors -->
            <div>
              <p class="text-xs mb-5" :style="{ color: D.inkTertiary }">Emphasis colors at md</p>
              <div class="flex flex-wrap items-end gap-6">
                <div v-for="arc in ARC_EMPHASIS" :key="arc.key" class="flex flex-col items-center gap-2">
                  <KinProgressArc :value="arc.pct" :size="100" :thickness="8" :color="arc.key">
                    <template #center>
                      <span class="font-semibold leading-none" style="font-size:26px" :style="{ color: arc.key === 'lavender' ? '#B6A8E6' : arc.key === 'success' ? '#6CC498' : arc.key === 'warning' ? '#E6C452' : '#E67070' }">{{ arc.pct }}<span style="font-size:13px">%</span></span>
                      <span style="font-size:10px; color:#6E6B67; margin-top:2px">{{ arc.label }}</span>
                    </template>
                  </KinProgressArc>
                  <span class="text-[10px]" :style="{ color: D.inkTertiary }">{{ EMPHASIS[arc.key].label }}</span>
                </div>
              </div>
            </div>

            <!-- 4. Hero context -->
            <div>
              <p class="text-xs mb-5" :style="{ color: D.inkTertiary }">Hero context — featured dashboard moment (lg)</p>
              <div class="flex flex-col items-center max-w-xs mx-auto text-center gap-4">
                <KinProgressArc :value="63" :size="180" :thickness="10" color="lavender">
                  <template #center>
                    <span class="font-semibold leading-none" style="font-size:46px; color:#F0EDE9">63<span style="font-size:24px">%</span></span>
                    <span style="font-size:13px; color:#6E6B67; margin-top:3px">this week</span>
                  </template>
                </KinProgressArc>
                <div>
                  <p class="text-[17px] font-semibold" :style="{ color: D.inkPrimary }">Weekly goal</p>
                  <p class="text-[13px] leading-snug mt-1" :style="{ color: D.inkSecondary }">
                    You're 8 tasks away from completing this week's goal. Keep it up.
                  </p>
                </div>
              </div>
            </div>
          </div><!-- /dark -->
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
