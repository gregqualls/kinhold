<script setup>
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import KinDayHeader from '@/components/design-system/KinDayHeader.vue'
import { SparklesIcon } from '@heroicons/vue/24/outline'

// ── Palette — light ────────────────────────────────────────────────────────
const L = {
  surfaceApp:    '#FAF8F5', surfaceRaised: '#FFFFFF', surfaceSunken: '#F5F2EE',
  inkPrimary:    '#1C1C1E', inkSecondary:  '#6B6966', inkTertiary:   '#9C9895', inkInverse: '#FAF8F5',
  borderSubtle:  '#E8E4DF', borderStrong:  '#BCB8B2',
  accents: {
    lavender: { soft: '#EAE6F8', bold: '#6856B2' },
    peach:    { soft: '#FCE9E0', bold: '#BA562E' },
    mint:     { soft: '#D5F2E8', bold: '#2E8A62' },
    sun:      { soft: '#FCF3D2', bold: '#A2780C' },
  },
}

// ── Palette — dark ─────────────────────────────────────────────────────────
const D = {
  surfaceApp:     '#141311', surfaceRaised: '#1C1B19', surfaceSunken: '#161513', surfaceOverlay: '#242220',
  inkPrimary:     '#F0EDE9', inkSecondary:  '#A09C97', inkTertiary:   '#6E6B67', inkInverse: '#1C1C1E',
  borderSubtle:   '#2C2A27', borderStrong:  '#403E3A',
  accents: {
    lavender: { soft: '#302A48', bold: '#B6A8E6' },
    peach:    { soft: '#3E241A', bold: '#F0A882' },
    mint:     { soft: '#18342A', bold: '#7CD6AE' },
    sun:      { soft: '#342C0A', bold: '#E6C452' },
  },
}

// ── Hero number style (shared across all variants) ─────────────────────────
// clamp(5rem, 18vw, 14rem) as specified. Plus Jakarta Sans weight 600, tracking -0.02em.
const HERO_NUM_STYLE = {
  fontSize: 'clamp(5rem, 18vw, 14rem)',
  fontWeight: '600',
  letterSpacing: '-0.02em',
  lineHeight: '1',
  fontFamily: "'Plus Jakarta Sans', sans-serif",
}

// ── Label styles ────────────────────────────────────────────────────────────
const DAY_LABEL_STYLE = {
  fontSize: '11px',
  fontWeight: '600',
  letterSpacing: '0.12em',
  textTransform: 'uppercase',
  lineHeight: '1',
}

const MONTH_LABEL_STYLE = {
  fontSize: '13px',
  fontWeight: '500',
  letterSpacing: '0.10em',
  textTransform: 'uppercase',
  lineHeight: '1',
}
</script>

<template>
  <ComponentPage
    title="5.6 DayHeader"
    description="Editorial-scale day-of-month number + tiny day-of-week tag. The day number is the hero — it commands the page and anchors temporal orientation. Used on day view, focus-mode today screen, and meal-plan-day detail."
    status="chosen"
  >
    <!-- ══════════════════════════════════════════════════════════════
         VARIANT B — Inline with context
         Huge number LEFT, day-of-week + month stacked RIGHT separated
         by a thin vertical rule. Optional event-count indicator below-right.
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-20">
      <VariantFrame
        label="DayHeader"
        caption="Inline with context — hero number left · thin rule · day + month stacked right · optional event-count below-right. The single locked DayHeader shape."
      >
        <div class="w-full space-y-10">

          <!-- ─── LIGHT PANEL B ──────────────────────────────────────── -->
          <div class="rounded-2xl border p-8 space-y-10"
               :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>

            <!-- B Default — Friday Mar 14 · 3 events -->
            <div>
              <p class="text-[11px] mb-6 font-medium" :style="{ color: L.inkTertiary }">Default · Friday, March 14 · 3 events</p>
              <div class="flex items-center gap-0">
                <!-- Hero number -->
                <span
                  class="font-heading dh-hero-num flex-shrink-0"
                  :style="{ ...HERO_NUM_STYLE, color: L.inkPrimary }"
                >14</span>
                <!-- Thin vertical rule -->
                <div
                  class="flex-shrink-0 self-stretch mx-4"
                  style="width: 1px; min-height: 3rem;"
                  :style="{ background: L.borderStrong }"
                />
                <!-- Right: day + month stacked + event count -->
                <div class="flex flex-col justify-center gap-1 min-w-0">
                  <span :style="{ ...DAY_LABEL_STYLE, color: L.inkPrimary }">FRIDAY</span>
                  <span :style="{ ...MONTH_LABEL_STYLE, color: L.inkTertiary }">MARCH 2026</span>
                  <span :style="{ fontSize: '11px', color: L.inkTertiary, marginTop: '2px' }">· 3 events</span>
                </div>
              </div>
            </div>

            <!-- B Empty day — Saturday, no events -->
            <div>
              <p class="text-[11px] mb-6 font-medium" :style="{ color: L.inkTertiary }">Empty day · Saturday, March 21 · 0 events</p>
              <div class="flex items-center gap-0">
                <span
                  class="font-heading dh-hero-num flex-shrink-0"
                  :style="{ ...HERO_NUM_STYLE, color: L.inkPrimary }"
                >21</span>
                <div
                  class="flex-shrink-0 self-stretch mx-4"
                  style="width: 1px; min-height: 3rem;"
                  :style="{ background: L.borderSubtle }"
                />
                <div class="flex flex-col justify-center gap-1 min-w-0">
                  <span :style="{ ...DAY_LABEL_STYLE, color: L.inkPrimary }">SATURDAY</span>
                  <span :style="{ ...MONTH_LABEL_STYLE, color: L.inkTertiary }">MARCH 2026</span>
                  <!-- No event count — calm, no placeholder -->
                </div>
              </div>
            </div>

            <!-- B With Today badge -->
            <div>
              <p class="text-[11px] mb-6 font-medium" :style="{ color: L.inkTertiary }">With "Today" badge · Friday, March 14</p>
              <div class="flex items-center gap-0">
                <span
                  class="font-heading dh-hero-num flex-shrink-0"
                  :style="{ ...HERO_NUM_STYLE, color: L.inkPrimary }"
                >14</span>
                <div
                  class="flex-shrink-0 self-stretch mx-4"
                  style="width: 1px; min-height: 3rem;"
                  :style="{ background: L.borderStrong }"
                />
                <div class="flex flex-col justify-center gap-1.5 min-w-0">
                  <div class="flex items-center gap-2 flex-wrap">
                    <span :style="{ ...DAY_LABEL_STYLE, color: L.inkPrimary }">FRIDAY</span>
                    <span
                      class="inline-flex items-center rounded-full h-6 font-semibold uppercase flex-shrink-0"
                      style="font-size: 10px; letter-spacing: 0.08em; padding: 0 10px;"
                      :style="{ background: L.accents.lavender.soft, color: L.accents.lavender.bold }"
                    >TODAY</span>
                  </div>
                  <span :style="{ ...MONTH_LABEL_STYLE, color: L.inkTertiary }">MARCH 2026</span>
                  <span :style="{ fontSize: '11px', color: L.inkTertiary }">· 3 events</span>
                </div>
              </div>
            </div>

            <!-- B Mobile 375px -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: L.inkTertiary }">Mobile (375px)</p>
              <div class="max-w-[375px] rounded-xl border p-4"
                   :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle }">
                <div class="flex items-center gap-0">
                  <span
                    class="font-heading dh-hero-num flex-shrink-0"
                    :style="{ ...HERO_NUM_STYLE, color: L.inkPrimary }"
                  >14</span>
                  <div
                    class="flex-shrink-0 self-stretch mx-3"
                    style="width: 1px; min-height: 2.5rem;"
                    :style="{ background: L.borderStrong }"
                  />
                  <div class="flex flex-col justify-center gap-1 min-w-0">
                    <span :style="{ ...DAY_LABEL_STYLE, color: L.inkPrimary }">FRIDAY</span>
                    <span :style="{ ...MONTH_LABEL_STYLE, color: L.inkTertiary }">MARCH 2026</span>
                    <span :style="{ fontSize: '11px', color: L.inkTertiary }">· 3 events</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- B Desktop 1024px+ -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: L.inkTertiary }">Desktop (1024px+)</p>
              <div class="rounded-xl border p-8" style="max-width: 640px;"
                   :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle }">
                <div class="flex items-center gap-0">
                  <span
                    class="font-heading flex-shrink-0"
                    style="font-size: 10rem; font-weight: 600; letter-spacing: -0.02em; line-height: 1; font-family: 'Plus Jakarta Sans', sans-serif;"
                    :style="{ color: L.inkPrimary }"
                  >14</span>
                  <div
                    class="flex-shrink-0 self-stretch mx-6"
                    style="width: 1.5px; min-height: 4rem;"
                    :style="{ background: L.borderStrong }"
                  />
                  <div class="flex flex-col justify-center gap-1.5 min-w-0">
                    <span :style="{ ...DAY_LABEL_STYLE, color: L.inkPrimary, fontSize: '13px' }">FRIDAY</span>
                    <span :style="{ ...MONTH_LABEL_STYLE, color: L.inkTertiary, fontSize: '14px' }">MARCH 2026</span>
                    <span :style="{ fontSize: '12px', color: L.inkTertiary }">· 3 events</span>
                  </div>
                </div>
              </div>
            </div>
          </div><!-- /light B -->

          <!-- ─── DARK PANEL B ──────────────────────────────────────── -->
          <div class="rounded-2xl border p-8 space-y-10"
               :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

            <!-- B Default dark -->
            <div>
              <p class="text-[11px] mb-6 font-medium" :style="{ color: D.inkTertiary }">Default · Friday, March 14 · 3 events</p>
              <div class="flex items-center gap-0">
                <span
                  class="font-heading dh-hero-num flex-shrink-0"
                  :style="{ ...HERO_NUM_STYLE, color: D.inkPrimary }"
                >14</span>
                <div
                  class="flex-shrink-0 self-stretch mx-4"
                  style="width: 1px; min-height: 3rem;"
                  :style="{ background: D.borderStrong }"
                />
                <div class="flex flex-col justify-center gap-1 min-w-0">
                  <span :style="{ ...DAY_LABEL_STYLE, color: D.inkPrimary }">FRIDAY</span>
                  <span :style="{ ...MONTH_LABEL_STYLE, color: D.inkTertiary }">MARCH 2026</span>
                  <span :style="{ fontSize: '11px', color: D.inkTertiary, marginTop: '2px' }">· 3 events</span>
                </div>
              </div>
            </div>

            <!-- B Empty day dark -->
            <div>
              <p class="text-[11px] mb-6 font-medium" :style="{ color: D.inkTertiary }">Empty day · Saturday, March 21</p>
              <div class="flex items-center gap-0">
                <span
                  class="font-heading dh-hero-num flex-shrink-0"
                  :style="{ ...HERO_NUM_STYLE, color: D.inkPrimary }"
                >21</span>
                <div
                  class="flex-shrink-0 self-stretch mx-4"
                  style="width: 1px; min-height: 3rem;"
                  :style="{ background: D.borderSubtle }"
                />
                <div class="flex flex-col justify-center gap-1 min-w-0">
                  <span :style="{ ...DAY_LABEL_STYLE, color: D.inkPrimary }">SATURDAY</span>
                  <span :style="{ ...MONTH_LABEL_STYLE, color: D.inkTertiary }">MARCH 2026</span>
                </div>
              </div>
            </div>

            <!-- B Today badge dark -->
            <div>
              <p class="text-[11px] mb-6 font-medium" :style="{ color: D.inkTertiary }">With "Today" badge</p>
              <div class="flex items-center gap-0">
                <span
                  class="font-heading dh-hero-num flex-shrink-0"
                  :style="{ ...HERO_NUM_STYLE, color: D.inkPrimary }"
                >14</span>
                <div
                  class="flex-shrink-0 self-stretch mx-4"
                  style="width: 1px; min-height: 3rem;"
                  :style="{ background: D.borderStrong }"
                />
                <div class="flex flex-col justify-center gap-1.5 min-w-0">
                  <div class="flex items-center gap-2 flex-wrap">
                    <span :style="{ ...DAY_LABEL_STYLE, color: D.inkPrimary }">FRIDAY</span>
                    <span
                      class="inline-flex items-center rounded-full h-6 font-semibold uppercase flex-shrink-0"
                      style="font-size: 10px; letter-spacing: 0.08em; padding: 0 10px;"
                      :style="{ background: D.accents.lavender.soft, color: D.accents.lavender.bold }"
                    >TODAY</span>
                  </div>
                  <span :style="{ ...MONTH_LABEL_STYLE, color: D.inkTertiary }">MARCH 2026</span>
                  <span :style="{ fontSize: '11px', color: D.inkTertiary }">· 3 events</span>
                </div>
              </div>
            </div>
          </div><!-- /dark B -->

        </div>
      </VariantFrame>
      <p class="mt-3 text-sm px-1" :style="{ color: L.inkSecondary }">
        The hero number stays dominant while contextual labels get a dignified home on the right.
        The thin vertical rule separates "the number" from "what the number means" without adding surface weight.
        Event count below-right is optional and disappears on empty days.
      </p>
    </section>



    <!-- ══════════════════════════════════════════════════════════════
         CLAUDE'S PICK CALLOUT
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-12">
      <div
        class="rounded-2xl p-6 space-y-3 flex gap-4 items-start"
        :style="{ background: L.accents.lavender.soft, border: `1px solid ${L.accents.lavender.bold}30` }"
      >
        <SparklesIcon class="w-5 h-5 flex-shrink-0 mt-0.5" :style="{ color: L.accents.lavender.bold }" />
        <div class="space-y-2">
          <h2 class="text-[15px] font-semibold" :style="{ color: L.accents.lavender.bold }">LOCKED — inline-with-context DayHeader</h2>
          <p class="text-[14px] leading-relaxed" :style="{ color: L.inkPrimary }">
            Hero number on the left, a thin vertical rule, then day + month stacked on the right with an optional event-count beneath. The number stays the hero while contextual labels get a structured home. Used everywhere a DayHeader appears — day-detail pages, week-view column headers, meal-plan day modals, focus screens.
          </p>
          <p class="text-[14px] leading-relaxed" :style="{ color: L.inkSecondary }">
            Scales fluidly via <code class="text-[12px] font-mono bg-white/60 px-1 rounded">clamp(5rem, 18vw, 14rem)</code> — 80px on mobile, 220px on wide desktop. The vertical rule ornament and right-side label stack shrink proportionally, so the composition reads correctly at every width.
          </p>
        </div>
      </div>
    </section>


    <!-- ══════════════════════════════════════════════════════════════
         USAGE GUIDE
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-8">
      <div class="rounded-2xl border p-6 space-y-4"
           :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle }">
        <h2 class="text-[17px] font-semibold" :style="{ color: L.inkPrimary }">When to use DayHeader</h2>
        <ul class="space-y-4 text-[14px]" :style="{ color: L.inkSecondary }">
          <li>
            <strong :style="{ color: L.inkPrimary }">Default everywhere a DayHeader appears.</strong>
            Day-detail pages, week-view column headers (compact form), meal-plan day modals, focus-mode today screens. One shape, different surrounding surfaces.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Font sizing rule.</strong>
            Always use <code class="font-mono text-[12px] px-1 py-0.5 rounded" :style="{ background: L.surfaceSunken }">font-size: clamp(5rem, 18vw, 14rem)</code>
            with <code class="font-mono text-[12px] px-1 py-0.5 rounded" :style="{ background: L.surfaceSunken }">font-family: 'Plus Jakarta Sans', sans-serif</code>
            applied both as the <code class="font-mono text-[12px] px-1 py-0.5 rounded" :style="{ background: L.surfaceSunken }">font-heading</code>
            class and as an inline style. The inline style is the safety net for dark-panel contexts where class inheritance may not resolve correctly.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">TODAY badge placement.</strong>
            Beside the day-of-week label in the right stack. Always
            <code class="font-mono text-[12px] px-1 py-0.5 rounded" :style="{ background: L.surfaceSunken }">h-6 px-2.5 rounded-full</code>,
            bg lavender-soft, text lavender-bold, 10px font-semibold uppercase. Never inside the hero number area.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Empty-day treatment.</strong>
            Do not add placeholder text ("No events"), error states, or different colors for empty days. The number is still a valid, complete representation of the date — simply omit the event-count line.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">No bounding surface.</strong>
            DayHeader is typography-first. Do not wrap it in a card surface with background fill or border
            unless the host layout requires it (e.g. a modal or a panel within a larger card). The number
            should feel like it lives directly on the page, not boxed inside a container.
          </li>
        </ul>
      </div>
    </section>


    <!-- KIN COMPONENT PREVIEW -->
    <section class="mb-16">
      <VariantFrame label="Kin" caption="KinDayHeader — proposed extraction. Hero number + vertical rule + stacked weekday/month/count.">
        <div class="w-full space-y-10">
          <div class="rounded-2xl border p-8 space-y-10" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>
            <KinDayHeader :day="14" weekday="Friday" month="March 2026" :event-count="3" />
            <KinDayHeader :day="21" weekday="Saturday" month="March 2026" />
            <KinDayHeader :day="14" weekday="Friday" month="March 2026" :event-count="3" :is-today="true" />
            <div class="max-w-[375px] rounded-xl border p-4" :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle }">
              <KinDayHeader :day="14" weekday="Friday" month="March 2026" :event-count="3" size="sm" />
            </div>
            <div class="rounded-xl border p-8 max-w-[640px]" :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle }">
              <KinDayHeader :day="14" weekday="Friday" month="March 2026" :event-count="3" size="lg" />
            </div>
          </div>

          <div class="dark rounded-2xl border p-8 space-y-10" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>
            <KinDayHeader :day="14" weekday="Friday" month="March 2026" :event-count="3" />
            <KinDayHeader :day="14" weekday="Friday" month="March 2026" :event-count="3" :is-today="true" />
            <div class="rounded-xl border p-8 max-w-[640px]" :style="{ background: D.surfaceRaised, borderColor: D.borderSubtle }">
              <KinDayHeader :day="14" weekday="Friday" month="March 2026" :event-count="3" size="lg" />
            </div>
          </div>
        </div>
      </VariantFrame>
    </section>

  </ComponentPage>
</template>

<style scoped>
/* ─────────────────────────────────────────────────────────────────────────────
   Hero number — ensure Plus Jakarta Sans loads even in scoped dark panel context.
   The class + inline style combo is the intentional dual-safety approach.
   ─────────────────────────────────────────────────────────────────────────────*/
.dh-hero-num {
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: clamp(5rem, 18vw, 14rem);
  font-weight: 600;
  letter-spacing: -0.02em;
  line-height: 1;
  display: block;
}

/* Reduced motion — no transforms used in this component, but guard against
   any future animation additions. */
@media (prefers-reduced-motion: reduce) {
  .dh-hero-num {
    transition: none;
  }
}
</style>
