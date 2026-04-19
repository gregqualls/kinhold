<script setup>
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
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
    status="scaffolded"
  >

    <!-- ══════════════════════════════════════════════════════════════
         VARIANT A — Stacked editorial
         Month label ABOVE, huge number, day-of-week BELOW and indented.
         Left-aligned. Editorial magazine feel.
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-20">
      <VariantFrame
        label="A"
        caption="Stacked editorial — month above · hero number · day-of-week below indented. Left-aligned magazine typesetting."
      >
        <div class="w-full space-y-10">

          <!-- ─── LIGHT PANEL A ──────────────────────────────────────── -->
          <div class="rounded-2xl border p-8 space-y-10"
               :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>

            <!-- A Default — Friday Mar 14 · 3 events -->
            <div>
              <p class="text-[11px] mb-6 font-medium" :style="{ color: L.inkTertiary }">Default · Friday, March 14 · 3 events</p>
              <div class="flex flex-col items-start">
                <!-- Month label -->
                <span :style="{ ...MONTH_LABEL_STYLE, color: L.inkTertiary }">MARCH 2026</span>
                <!-- Hero day number -->
                <span
                  class="font-heading dh-hero-num"
                  :style="{ ...HERO_NUM_STYLE, color: L.inkPrimary }"
                >14</span>
                <!-- Day-of-week label — indented 2px to optically align with number's left descender -->
                <div class="flex items-center gap-2" style="margin-top: -0.25rem; padding-left: 2px;">
                  <span :style="{ ...DAY_LABEL_STYLE, color: L.inkTertiary }">FRIDAY</span>
                  <span :style="{ fontSize: '11px', color: L.inkTertiary }">· 3 events</span>
                </div>
              </div>
            </div>

            <!-- A Empty day — Saturday, no events -->
            <div>
              <p class="text-[11px] mb-6 font-medium" :style="{ color: L.inkTertiary }">Empty day · Saturday, March 21 · 0 events</p>
              <div class="flex flex-col items-start">
                <span :style="{ ...MONTH_LABEL_STYLE, color: L.inkTertiary }">MARCH 2026</span>
                <span
                  class="font-heading dh-hero-num"
                  :style="{ ...HERO_NUM_STYLE, color: L.inkPrimary }"
                >21</span>
                <div class="flex items-center gap-2" style="margin-top: -0.25rem; padding-left: 2px;">
                  <span :style="{ ...DAY_LABEL_STYLE, color: L.inkTertiary }">SATURDAY</span>
                  <!-- No events — calm silence, no empty-state treatment -->
                </div>
              </div>
            </div>

            <!-- A With Today badge — Friday Mar 14 -->
            <div>
              <p class="text-[11px] mb-6 font-medium" :style="{ color: L.inkTertiary }">With "Today" badge · Friday, March 14</p>
              <div class="flex flex-col items-start">
                <div class="flex items-center gap-2">
                  <span :style="{ ...MONTH_LABEL_STYLE, color: L.inkTertiary }">MARCH 2026</span>
                  <!-- TODAY badge pill -->
                  <span
                    class="inline-flex items-center rounded-full h-6 font-semibold uppercase"
                    style="font-size: 10px; letter-spacing: 0.08em; padding: 0 10px;"
                    :style="{ background: L.accents.lavender.soft, color: L.accents.lavender.bold }"
                  >TODAY</span>
                </div>
                <span
                  class="font-heading dh-hero-num"
                  :style="{ ...HERO_NUM_STYLE, color: L.inkPrimary }"
                >14</span>
                <div class="flex items-center gap-2" style="margin-top: -0.25rem; padding-left: 2px;">
                  <span :style="{ ...DAY_LABEL_STYLE, color: L.inkTertiary }">FRIDAY</span>
                  <span :style="{ fontSize: '11px', color: L.inkTertiary }">· 3 events</span>
                </div>
              </div>
            </div>

            <!-- A Mobile 375px simulation -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: L.inkTertiary }">Mobile (375px) — number clamps to ~80px</p>
              <div class="max-w-[375px] rounded-xl border p-5"
                   :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle }">
                <div class="flex flex-col items-start">
                  <span :style="{ ...MONTH_LABEL_STYLE, color: L.inkTertiary }">MARCH 2026</span>
                  <span
                    class="font-heading dh-hero-num"
                    :style="{ ...HERO_NUM_STYLE, color: L.inkPrimary }"
                  >14</span>
                  <div class="flex items-center gap-2" style="margin-top: -0.25rem; padding-left: 2px;">
                    <span :style="{ ...DAY_LABEL_STYLE, color: L.inkTertiary }">FRIDAY</span>
                    <span :style="{ fontSize: '11px', color: L.inkTertiary }">· 3 events</span>
                  </div>
                </div>
                <p class="text-[10px] mt-3" :style="{ color: L.inkTertiary }">
                  At 375px the clamp resolves to ~67px (18vw). Number remains legible and dominant.
                </p>
              </div>
            </div>

            <!-- A Desktop 1024px+ simulation -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: L.inkTertiary }">Desktop (1024px+) — number expands toward 14rem (~224px)</p>
              <div class="rounded-xl border p-8" style="max-width: 700px;"
                   :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle }">
                <div class="flex flex-col items-start">
                  <span :style="{ ...MONTH_LABEL_STYLE, color: L.inkTertiary }">MARCH 2026</span>
                  <!-- Force a large static size to simulate desktop clamp upper bound -->
                  <span
                    class="font-heading"
                    style="font-size: 10rem; font-weight: 600; letter-spacing: -0.02em; line-height: 1; font-family: 'Plus Jakarta Sans', sans-serif;"
                    :style="{ color: L.inkPrimary }"
                  >14</span>
                  <div class="flex items-center gap-2" style="margin-top: -0.25rem; padding-left: 2px;">
                    <span :style="{ ...DAY_LABEL_STYLE, color: L.inkTertiary }">FRIDAY</span>
                    <span :style="{ fontSize: '11px', color: L.inkTertiary }">· 3 events</span>
                  </div>
                </div>
                <p class="text-[10px] mt-3" :style="{ color: L.inkTertiary }">
                  At 1024px+, clamp resolves to 184px (18vw). Capped at 14rem (224px) on very wide screens.
                  Number becomes a true editorial landmark.
                </p>
              </div>
            </div>
          </div><!-- /light A -->

          <!-- ─── DARK PANEL A ──────────────────────────────────────── -->
          <div class="rounded-2xl border p-8 space-y-10"
               :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

            <!-- A Default dark -->
            <div>
              <p class="text-[11px] mb-6 font-medium" :style="{ color: D.inkTertiary }">Default · Friday, March 14 · 3 events</p>
              <div class="flex flex-col items-start">
                <span :style="{ ...MONTH_LABEL_STYLE, color: D.inkTertiary }">MARCH 2026</span>
                <span
                  class="font-heading dh-hero-num"
                  :style="{ ...HERO_NUM_STYLE, color: D.inkPrimary }"
                >14</span>
                <div class="flex items-center gap-2" style="margin-top: -0.25rem; padding-left: 2px;">
                  <span :style="{ ...DAY_LABEL_STYLE, color: D.inkTertiary }">FRIDAY</span>
                  <span :style="{ fontSize: '11px', color: D.inkTertiary }">· 3 events</span>
                </div>
              </div>
            </div>

            <!-- A Empty day dark -->
            <div>
              <p class="text-[11px] mb-6 font-medium" :style="{ color: D.inkTertiary }">Empty day · Saturday, March 21</p>
              <div class="flex flex-col items-start">
                <span :style="{ ...MONTH_LABEL_STYLE, color: D.inkTertiary }">MARCH 2026</span>
                <span
                  class="font-heading dh-hero-num"
                  :style="{ ...HERO_NUM_STYLE, color: D.inkPrimary }"
                >21</span>
                <div style="margin-top: -0.25rem; padding-left: 2px;">
                  <span :style="{ ...DAY_LABEL_STYLE, color: D.inkTertiary }">SATURDAY</span>
                </div>
              </div>
            </div>

            <!-- A Today badge dark -->
            <div>
              <p class="text-[11px] mb-6 font-medium" :style="{ color: D.inkTertiary }">With "Today" badge</p>
              <div class="flex flex-col items-start">
                <div class="flex items-center gap-2">
                  <span :style="{ ...MONTH_LABEL_STYLE, color: D.inkTertiary }">MARCH 2026</span>
                  <span
                    class="inline-flex items-center rounded-full h-6 font-semibold uppercase"
                    style="font-size: 10px; letter-spacing: 0.08em; padding: 0 10px;"
                    :style="{ background: D.accents.lavender.soft, color: D.accents.lavender.bold }"
                  >TODAY</span>
                </div>
                <span
                  class="font-heading dh-hero-num"
                  :style="{ ...HERO_NUM_STYLE, color: D.inkPrimary }"
                >14</span>
                <div class="flex items-center gap-2" style="margin-top: -0.25rem; padding-left: 2px;">
                  <span :style="{ ...DAY_LABEL_STYLE, color: D.inkTertiary }">FRIDAY</span>
                  <span :style="{ fontSize: '11px', color: D.inkTertiary }">· 3 events</span>
                </div>
              </div>
            </div>
          </div><!-- /dark A -->

        </div>
      </VariantFrame>
      <p class="mt-3 text-sm px-1" :style="{ color: L.inkSecondary }">
        Variant A uses classic editorial typesetting — the number towers over its context labels,
        flanked by month above and day-of-week below. The vertical rhythm feels like a magazine date
        byline scaled up to fill a phone screen. Best for day-detail pages where the date itself
        deserves moment-of-arrival weight.
      </p>
    </section>


    <!-- ══════════════════════════════════════════════════════════════
         VARIANT B — Inline with context
         Huge number LEFT, day-of-week + month stacked RIGHT separated
         by a thin vertical rule. Optional event-count indicator below-right.
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-20">
      <VariantFrame
        label="B"
        caption="Inline with context — hero number left · thin rule · day+month stacked right · optional event-count below-right."
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
        Variant B keeps the hero number dominant while giving contextual labels a dignified home.
        The thin vertical rule acts as a visual scaffold — it separates "the number" from "what the number means"
        without adding surface weight. The event count below-right is optional and disappears cleanly on empty days.
        Best for inline week-view day headers where horizontal space is available but vertical real estate is constrained.
      </p>
    </section>


    <!-- ══════════════════════════════════════════════════════════════
         VARIANT C — Centered with accent undertone
         Huge number centered, soft lavender radial gradient behind it,
         day-of-week ABOVE the number in small uppercase.
         Ceremonial / today-focus feel.
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-20">
      <VariantFrame
        label="C"
        caption="Centered ceremonial — lavender gradient behind hero number · day-of-week above · ideal for today focus screen."
      >
        <div class="w-full space-y-10">

          <!-- ─── LIGHT PANEL C ──────────────────────────────────────── -->
          <div class="rounded-2xl border p-8 space-y-10"
               :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>

            <!-- C Default — Friday Mar 14 · 3 events -->
            <div>
              <p class="text-[11px] mb-6 font-medium" :style="{ color: L.inkTertiary }">Default · Friday, March 14 · 3 events</p>
              <div class="relative flex flex-col items-center py-6">
                <!-- Soft lavender accent behind the number -->
                <div
                  class="absolute inset-0 rounded-3xl pointer-events-none"
                  style="
                    background: radial-gradient(ellipse 65% 70% at 50% 60%, #EAE6F8 0%, transparent 100%);
                  "
                />
                <!-- Day-of-week label + month — ABOVE number -->
                <div class="relative z-10 flex items-center gap-2 mb-1">
                  <span :style="{ ...DAY_LABEL_STYLE, color: L.inkTertiary }">FRIDAY</span>
                  <span :style="{ fontSize: '11px', color: L.inkTertiary }">· MARCH 14</span>
                </div>
                <!-- Hero number -->
                <span
                  class="relative z-10 font-heading dh-hero-num"
                  :style="{ ...HERO_NUM_STYLE, color: L.inkPrimary }"
                >14</span>
                <!-- Event count below -->
                <span
                  class="relative z-10 mt-1"
                  :style="{ fontSize: '11px', color: L.inkTertiary }"
                >3 events scheduled</span>
              </div>
            </div>

            <!-- C Empty day — Saturday -->
            <div>
              <p class="text-[11px] mb-6 font-medium" :style="{ color: L.inkTertiary }">Empty day · Saturday, March 21 · 0 events</p>
              <div class="relative flex flex-col items-center py-6">
                <!-- Lighter gradient for empty day — still present, just softer -->
                <div
                  class="absolute inset-0 rounded-3xl pointer-events-none"
                  style="
                    background: radial-gradient(ellipse 55% 60% at 50% 60%, #F0EEF8 0%, transparent 100%);
                  "
                />
                <div class="relative z-10 flex items-center gap-2 mb-1">
                  <span :style="{ ...DAY_LABEL_STYLE, color: L.inkTertiary }">SATURDAY</span>
                  <span :style="{ fontSize: '11px', color: L.inkTertiary }">· MARCH 21</span>
                </div>
                <span
                  class="relative z-10 font-heading dh-hero-num"
                  :style="{ ...HERO_NUM_STYLE, color: L.inkPrimary }"
                >21</span>
                <!-- Empty day — calm, no events label, no error treatment -->
              </div>
            </div>

            <!-- C With Today badge -->
            <div>
              <p class="text-[11px] mb-6 font-medium" :style="{ color: L.inkTertiary }">With "Today" badge · Friday, March 14</p>
              <div class="relative flex flex-col items-center py-6">
                <div
                  class="absolute inset-0 rounded-3xl pointer-events-none"
                  style="
                    background: radial-gradient(ellipse 65% 70% at 50% 60%, #EAE6F8 0%, transparent 100%);
                  "
                />
                <div class="relative z-10 flex items-center gap-2 mb-2 flex-wrap justify-center">
                  <span :style="{ ...DAY_LABEL_STYLE, color: L.inkTertiary }">FRIDAY · MARCH 14</span>
                  <span
                    class="inline-flex items-center rounded-full h-6 font-semibold uppercase"
                    style="font-size: 10px; letter-spacing: 0.08em; padding: 0 10px;"
                    :style="{ background: L.accents.lavender.soft, color: L.accents.lavender.bold }"
                  >TODAY</span>
                </div>
                <span
                  class="relative z-10 font-heading dh-hero-num"
                  :style="{ ...HERO_NUM_STYLE, color: L.inkPrimary }"
                >14</span>
                <span
                  class="relative z-10 mt-1"
                  :style="{ fontSize: '11px', color: L.inkTertiary }"
                >3 events scheduled</span>
              </div>
            </div>

            <!-- C Mobile 375px -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: L.inkTertiary }">Mobile (375px)</p>
              <div class="max-w-[375px] rounded-xl border overflow-hidden"
                   :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle }">
                <div class="relative flex flex-col items-center py-5 px-4">
                  <div
                    class="absolute inset-0 pointer-events-none"
                    style="background: radial-gradient(ellipse 70% 65% at 50% 60%, #EAE6F8 0%, transparent 100%);"
                  />
                  <div class="relative z-10 flex items-center gap-2 mb-1">
                    <span :style="{ ...DAY_LABEL_STYLE, color: L.inkTertiary }">FRIDAY · MARCH 14</span>
                  </div>
                  <span
                    class="relative z-10 font-heading dh-hero-num"
                    :style="{ ...HERO_NUM_STYLE, color: L.inkPrimary }"
                  >14</span>
                  <span
                    class="relative z-10 mt-1"
                    :style="{ fontSize: '11px', color: L.inkTertiary }"
                  >3 events scheduled</span>
                </div>
              </div>
            </div>

            <!-- C Desktop 1024px+ -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: L.inkTertiary }">Desktop (1024px+)</p>
              <div class="rounded-xl border overflow-hidden" style="max-width: 560px;"
                   :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle }">
                <div class="relative flex flex-col items-center py-10 px-8">
                  <div
                    class="absolute inset-0 pointer-events-none"
                    style="background: radial-gradient(ellipse 60% 75% at 50% 60%, #EAE6F8 0%, transparent 100%);"
                  />
                  <div class="relative z-10 flex items-center gap-2 mb-1">
                    <span :style="{ ...DAY_LABEL_STYLE, color: L.inkTertiary, fontSize: '13px' }">FRIDAY · MARCH 14</span>
                  </div>
                  <span
                    class="relative z-10 font-heading"
                    style="font-size: 10rem; font-weight: 600; letter-spacing: -0.02em; line-height: 1; font-family: 'Plus Jakarta Sans', sans-serif;"
                    :style="{ color: L.inkPrimary }"
                  >14</span>
                  <span
                    class="relative z-10 mt-1.5"
                    :style="{ fontSize: '12px', color: L.inkTertiary }"
                  >3 events scheduled</span>
                </div>
              </div>
            </div>
          </div><!-- /light C -->

          <!-- ─── DARK PANEL C ──────────────────────────────────────── -->
          <div class="rounded-2xl border p-8 space-y-10"
               :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

            <!-- C Default dark -->
            <div>
              <p class="text-[11px] mb-6 font-medium" :style="{ color: D.inkTertiary }">Default · Friday, March 14 · 3 events</p>
              <div class="relative flex flex-col items-center py-6">
                <div
                  class="absolute inset-0 rounded-3xl pointer-events-none"
                  style="
                    background: radial-gradient(ellipse 65% 70% at 50% 60%, rgba(104,86,178,0.30) 0%, transparent 100%);
                  "
                />
                <div class="relative z-10 flex items-center gap-2 mb-1">
                  <span :style="{ ...DAY_LABEL_STYLE, color: D.inkTertiary }">FRIDAY</span>
                  <span :style="{ fontSize: '11px', color: D.inkTertiary }">· MARCH 14</span>
                </div>
                <span
                  class="relative z-10 font-heading dh-hero-num"
                  :style="{ ...HERO_NUM_STYLE, color: D.inkPrimary }"
                >14</span>
                <span
                  class="relative z-10 mt-1"
                  :style="{ fontSize: '11px', color: D.inkTertiary }"
                >3 events scheduled</span>
              </div>
            </div>

            <!-- C Empty day dark -->
            <div>
              <p class="text-[11px] mb-6 font-medium" :style="{ color: D.inkTertiary }">Empty day · Saturday, March 21</p>
              <div class="relative flex flex-col items-center py-6">
                <div
                  class="absolute inset-0 rounded-3xl pointer-events-none"
                  style="
                    background: radial-gradient(ellipse 55% 60% at 50% 60%, rgba(104,86,178,0.14) 0%, transparent 100%);
                  "
                />
                <div class="relative z-10 flex items-center gap-2 mb-1">
                  <span :style="{ ...DAY_LABEL_STYLE, color: D.inkTertiary }">SATURDAY</span>
                  <span :style="{ fontSize: '11px', color: D.inkTertiary }">· MARCH 21</span>
                </div>
                <span
                  class="relative z-10 font-heading dh-hero-num"
                  :style="{ ...HERO_NUM_STYLE, color: D.inkPrimary }"
                >21</span>
              </div>
            </div>

            <!-- C Today badge dark -->
            <div>
              <p class="text-[11px] mb-6 font-medium" :style="{ color: D.inkTertiary }">With "Today" badge</p>
              <div class="relative flex flex-col items-center py-6">
                <div
                  class="absolute inset-0 rounded-3xl pointer-events-none"
                  style="
                    background: radial-gradient(ellipse 65% 70% at 50% 60%, rgba(104,86,178,0.35) 0%, transparent 100%);
                  "
                />
                <div class="relative z-10 flex items-center gap-2 mb-2 flex-wrap justify-center">
                  <span :style="{ ...DAY_LABEL_STYLE, color: D.inkTertiary }">FRIDAY · MARCH 14</span>
                  <span
                    class="inline-flex items-center rounded-full h-6 font-semibold uppercase"
                    style="font-size: 10px; letter-spacing: 0.08em; padding: 0 10px;"
                    :style="{ background: D.accents.lavender.soft, color: D.accents.lavender.bold }"
                  >TODAY</span>
                </div>
                <span
                  class="relative z-10 font-heading dh-hero-num"
                  :style="{ ...HERO_NUM_STYLE, color: D.inkPrimary }"
                >14</span>
                <span
                  class="relative z-10 mt-1"
                  :style="{ fontSize: '11px', color: D.inkTertiary }"
                >3 events scheduled</span>
              </div>
            </div>
          </div><!-- /dark C -->

        </div>
      </VariantFrame>
      <p class="mt-3 text-sm px-1" :style="{ color: L.inkSecondary }">
        Variant C wraps the day number in a soft lavender halo — the gradient doesn't frame a surface,
        it emanates from behind the number, making it feel like the number itself is glowing.
        The day-of-week label sits above like a subtitle, the number drops as the first word on the page.
        Empty days use a whisper-thin gradient so the space never feels broken or absent — it simply breathes.
        Ideal for the "Today" focus screen where a sense of ceremony and presence is the goal.
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
          <h2 class="text-[15px] font-semibold" :style="{ color: L.accents.lavender.bold }">Claude's pick — Variant C</h2>
          <p class="text-[14px] leading-relaxed" :style="{ color: L.inkPrimary }">
            Variant C best embodies the "numbers are heroes" tenet because it removes every visual competitor
            — the number sits alone at the centre of the screen with only a soft lavender aura behind it,
            as if the day itself is radiating presence. The day label above acts as a whispered annotation,
            not a headline, which preserves the number's editorial authority at any viewport width.
          </p>
          <p class="text-[14px] leading-relaxed" :style="{ color: L.inkSecondary }">
            Variant A is the right choice when the header anchors a vertically-scrolling day-detail list
            (the stacked rhythm reads top-to-bottom naturally). Variant B earns its place in constrained
            horizontal slots like an inline week-view column header where left-alignment and a rule separator
            give orientation without stealing vertical space.
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
            <strong :style="{ color: L.inkPrimary }">Variant A — day-detail pages.</strong>
            Use at the top of a day view where the user has navigated into a specific date and the page
            scrolls downward through that day's events and tasks. The vertical stacking (month → number → day)
            mirrors the reading direction of the content below. Left-align to match a standard content column.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Variant B — inline week-view headers.</strong>
            Use when each day of the week needs a compact date header above a column of events.
            The horizontal number-rule-label layout fits a narrow column without wasting vertical space.
            Drop the event count on narrow columns (&lt;80px) to avoid crowding.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Variant C — "Today" focus screens and meal-plan day detail.</strong>
            Use when the entire screen or a hero panel is devoted to a single day — a dashboard "today" panel,
            a focus-mode entry, or a meal-plan day modal. The centred layout and lavender aura signal arrival
            and presence. The gradient should cover the full panel background so the number appears to float.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Font sizing rule.</strong>
            Always use <code class="font-mono text-[12px] px-1 py-0.5 rounded" :style="{ background: L.surfaceSunken }">font-size: clamp(5rem, 18vw, 14rem)</code>
            with <code class="font-mono text-[12px] px-1 py-0.5 rounded" :style="{ background: L.surfaceSunken }">font-family: 'Plus Jakarta Sans', sans-serif</code>
            applied both as the <code class="font-mono text-[12px] px-1 py-0.5 rounded" :style="{ background: L.surfaceSunken }">font-heading</code>
            class and as an inline style. The inline style is the safety net for dark-panel contexts
            where class inheritance may not resolve correctly.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">TODAY badge placement.</strong>
            In Variant A: beside the month label (above). In Variant B: beside the day-of-week label (right stack).
            In Variant C: beside the day-of-week label above the number. Always
            <code class="font-mono text-[12px] px-1 py-0.5 rounded" :style="{ background: L.surfaceSunken }">h-6 px-2.5 rounded-full</code>,
            bg lavender-soft, text lavender-bold, 10px font-semibold uppercase. Never inside the hero number area.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Empty-day treatment.</strong>
            Do not add any placeholder text ("No events"), error state, or different color for empty days.
            The number is still a valid, complete representation of the date. In Variant C, reduce the gradient
            opacity slightly (~50%) to feel calmer — the day is quiet, not broken.
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
