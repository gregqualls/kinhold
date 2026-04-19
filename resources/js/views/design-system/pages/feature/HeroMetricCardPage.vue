<script setup>
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import {
  SparklesIcon, FireIcon, ArrowUpRightIcon, ArrowRightIcon,
  CalendarDaysIcon, CheckBadgeIcon,
} from '@heroicons/vue/24/outline'

// ── Palette — light ────────────────────────────────────────────────────────
const L = {
  surfaceApp:    '#FAF8F5',
  surfaceRaised: '#FFFFFF',
  surfaceSunken: '#F5F2EE',
  inkPrimary:    '#1C1C1E',
  inkSecondary:  '#6B6966',
  inkTertiary:   '#9C9895',
  inkInverse:    '#FAF8F5',
  borderSubtle:  '#E8E4DF',
  borderStrong:  '#BCB8B2',
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

// ── Palette — dark ─────────────────────────────────────────────────────────
const D = {
  surfaceApp:     '#141311',
  surfaceRaised:  '#1C1B19',
  surfaceSunken:  '#161513',
  surfaceOverlay: '#242220',
  inkPrimary:     '#F0EDE9',
  inkSecondary:   '#A09C97',
  inkTertiary:    '#6E6B67',
  inkInverse:     '#1C1C1E',
  borderSubtle:   '#2C2A27',
  borderStrong:   '#403E3A',
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

// ── Shadows ────────────────────────────────────────────────────────────────
const SHADOW_HERO_LT = '0 4px 12px rgba(28,20,10,0.08), 0 12px 32px rgba(28,20,10,0.06)'
const SHADOW_HERO_DK = '0 4px 12px rgba(0,0,0,0.50), 0 12px 32px rgba(0,0,0,0.40)'

// ── Inline icon paths (Heroicons 24 solid) — no import weight ──────────────
const GLYPH = {
  // SparklesIcon solid path
  sparkles: 'M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z',
  // FireIcon solid path
  fire: 'M12.894 2.553a1 1 0 0 0-1.788 0l-7.5 15A1 1 0 0 0 4.5 19h15a1 1 0 0 0 .894-1.447l-7.5-15ZM15.362 5.214A8.252 8.252 0 0 1 12 21 8.25 8.25 0 0 1 6.038 7.048 8.287 8.287 0 0 1 9 9.6a8.983 8.983 0 0 1 3.361-6.867 8.21 8.21 0 0 1 3 2.48z M12 18a3.75 3.75 0 0 0 .495-7.467 5.99 5.99 0 0 0-1.925 3.546 5.974 5.974 0 0 1-2.133-1A3.75 3.75 0 0 0 12 18z',
  // CalendarDaysIcon solid path (outline-shaped for watermark use)
  calendar: 'M6.75 2.25A.75.75 0 0 1 7.5 3v1.5h9V3A.75.75 0 0 1 18 3v1.5h.75a3 3 0 0 1 3 3v11.25a3 3 0 0 1-3 3H5.25a3 3 0 0 1-3-3V7.5a3 3 0 0 1 3-3H6V3a.75.75 0 0 1 .75-.75Zm13.5 9a1.5 1.5 0 0 0-1.5-1.5H5.25a1.5 1.5 0 0 0-1.5 1.5v7.5a1.5 1.5 0 0 0 1.5 1.5h13.5a1.5 1.5 0 0 0 1.5-1.5v-7.5Z',
  // CheckBadgeIcon solid
  checkBadge: 'M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z',
}

// ── Photo for Variant C ────────────────────────────────────────────────────
// Family dinner photo from Unsplash (stable, no CORS issues)
const PHOTO_FAMILY_DINNER = 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=800'
// Fallback: solid warm gradient (used when img fails to load via onerror placeholder logic)
</script>

<template>
  <ComponentPage
    title="5.2 HeroMetricCard"
    description="The flagship card at the top of module landing pages and the dashboard. Combines a StatTile (huge number) with a visual treatment — iridescent gradient, warm gradient, or edge-to-edge photo. Used for: dashboard greeting, module heroes, streak moments, meal plan photo covers."
    status="chosen"
  >

    <!-- ══════════════════════════════════════════════════════════════
         VARIANT A — Iridescent GradientCard
         Radial gradient anchored top-left: lavender-soft → mint-soft → peach-soft.
         Embossed glyph watermark top-left. StatTile bottom-left.
         Calm, premium, "this is a summary". Baseline hero for dashboard + modules.
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-20">
      <VariantFrame
        label="A"
        caption="Iridescent GradientCard — lavender→mint→peach radial, glyph watermark top-left, StatTile bottom-left. Calm summary hero."
      >
        <div class="w-full space-y-10">

          <!-- ─── LIGHT PANEL A ─────────────────────────────────────── -->
          <div class="rounded-2xl border p-6 space-y-8" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>

            <!-- A1 — Dashboard "Today" flavor (mobile 375px width) -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: L.inkTertiary }">Dashboard · Today — mobile (375 px)</p>
              <div style="max-width: 375px;">
                <div
                  class="hmc-a-lt relative rounded-[28px] overflow-hidden"
                  style="
                    min-height: 180px;
                    background-color: #FFFFFF;
                    background-image: radial-gradient(circle at 15% 15%, #EAE6F8, #D5F2E8 40%, #FCE9E0 85%, #FFFFFF 100%);
                  "
                  :style="{ boxShadow: SHADOW_HERO_LT }"
                >
                  <!-- Glyph watermark: SparklesIcon, 120px, ~10% opacity, top-left -->
                  <svg
                    class="absolute pointer-events-none"
                    style="width: 120px; height: 120px; left: 24px; top: 24px; opacity: 0.10; color: #6856B2;"
                    fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"
                  >
                    <path :d="GLYPH.sparkles" />
                  </svg>

                  <!-- StatTile contents: bottom-left -->
                  <div class="absolute inset-x-0 bottom-0 p-6 flex items-end justify-between gap-4">
                    <div class="space-y-1 min-w-0">
                      <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Today's score</p>
                      <p
                        class="font-semibold leading-none tracking-tight"
                        style="font-size: clamp(3rem, 8vw, 8rem); letter-spacing: -0.03em;"
                        :style="{ color: L.inkPrimary }"
                      >84</p>
                      <!-- Delta chip -->
                      <div class="flex items-center gap-1.5 flex-wrap">
                        <span
                          class="inline-flex items-center gap-0.5 rounded-full px-2 py-0.5 text-[11px] font-semibold"
                          :style="{ background: L.status.success.soft, color: L.status.success.bold }"
                        >
                          <svg viewBox="0 0 12 12" fill="currentColor" class="w-2.5 h-2.5 flex-shrink-0" aria-hidden="true">
                            <path d="M6 2.5l4 4H7v3H5V6.5H2l4-4z" />
                          </svg>
                          +12 pts
                        </span>
                        <span class="text-[11px]" :style="{ color: L.inkTertiary }">vs yesterday</span>
                      </div>
                    </div>
                    <!-- CTA button bottom-right -->
                    <button
                      class="hmc-cta-btn-lt flex-shrink-0 flex items-center gap-1.5 rounded-full px-4 py-2 text-[12px] font-semibold"
                      :style="{
                        background: L.inkPrimary,
                        color: L.inkInverse,
                        boxShadow: '0 2px 6px rgba(28,20,10,0.18)',
                      }"
                    >
                      View details
                      <ArrowRightIcon class="w-3.5 h-3.5 flex-shrink-0" />
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- A2 — Module landing "This week" (desktop width) -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: L.inkTertiary }">Module hero · Tasks — "This week" (desktop width)</p>
              <div style="max-width: 640px;">
                <div
                  class="hmc-a-lt relative rounded-[28px] overflow-hidden"
                  style="
                    min-height: 220px;
                    background-color: #FFFFFF;
                    background-image: radial-gradient(circle at 15% 15%, #EAE6F8, #D5F2E8 40%, #FCE9E0 85%, #FFFFFF 100%);
                  "
                  :style="{ boxShadow: SHADOW_HERO_LT }"
                >
                  <!-- Glyph watermark: CheckBadgeIcon -->
                  <svg
                    class="absolute pointer-events-none"
                    style="width: 160px; height: 160px; left: 28px; top: 28px; opacity: 0.09; color: #2E8A62;"
                    fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"
                  >
                    <path :d="GLYPH.checkBadge" />
                  </svg>

                  <div class="absolute inset-x-0 bottom-0 p-8 flex items-end justify-between gap-6">
                    <div class="space-y-1.5 min-w-0">
                      <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Tasks · Family progress</p>
                      <p
                        class="font-semibold leading-none tracking-tight"
                        style="font-size: clamp(3rem, 8vw, 8rem); letter-spacing: -0.03em;"
                        :style="{ color: L.inkPrimary }"
                      >23</p>
                      <div class="flex items-center gap-2 flex-wrap">
                        <span
                          class="inline-flex items-center gap-0.5 rounded-full px-2 py-0.5 text-[11px] font-semibold"
                          :style="{ background: L.status.success.soft, color: L.status.success.bold }"
                        >
                          <svg viewBox="0 0 12 12" fill="currentColor" class="w-2.5 h-2.5 flex-shrink-0" aria-hidden="true">
                            <path d="M6 2.5l4 4H7v3H5V6.5H2l4-4z" />
                          </svg>
                          +5 this week
                        </span>
                        <span class="text-[12px]" :style="{ color: L.inkSecondary }">tasks completed · 4 remaining</span>
                      </div>
                    </div>
                    <button
                      class="hmc-cta-btn-lt flex-shrink-0 flex items-center gap-1.5 rounded-full px-5 py-2.5 text-[13px] font-semibold"
                      :style="{ background: L.inkPrimary, color: L.inkInverse, boxShadow: '0 2px 6px rgba(28,20,10,0.18)' }"
                    >
                      See all tasks
                      <ArrowRightIcon class="w-3.5 h-3.5 flex-shrink-0" />
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div><!-- /light panel A -->

          <!-- ─── DARK PANEL A ─────────────────────────────────────── -->
          <div class="rounded-2xl border p-6 space-y-8" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

            <!-- A1 dark — Dashboard "Today" mobile -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: D.inkTertiary }">Dashboard · Today — mobile (375 px)</p>
              <div style="max-width: 375px;">
                <div
                  class="hmc-a-dk relative rounded-[28px] overflow-hidden"
                  style="
                    min-height: 180px;
                    background-color: #1C1B19;
                    background-image: radial-gradient(circle at 15% 15%, rgba(104,86,178,0.55), rgba(124,214,174,0.28) 40%, rgba(240,168,130,0.22) 85%, transparent 100%);
                  "
                  :style="{ boxShadow: SHADOW_HERO_DK }"
                >
                  <svg
                    class="absolute pointer-events-none"
                    style="width: 120px; height: 120px; left: 24px; top: 24px; opacity: 0.12; color: #B6A8E6;"
                    fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"
                  >
                    <path :d="GLYPH.sparkles" />
                  </svg>

                  <div class="absolute inset-x-0 bottom-0 p-6 flex items-end justify-between gap-4">
                    <div class="space-y-1 min-w-0">
                      <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Today's score</p>
                      <p
                        class="font-semibold leading-none tracking-tight"
                        style="font-size: clamp(3rem, 8vw, 8rem); letter-spacing: -0.03em;"
                        :style="{ color: D.inkPrimary }"
                      >84</p>
                      <div class="flex items-center gap-1.5 flex-wrap">
                        <span
                          class="inline-flex items-center gap-0.5 rounded-full px-2 py-0.5 text-[11px] font-semibold"
                          :style="{ background: D.status.success.soft, color: D.status.success.bold }"
                        >
                          <svg viewBox="0 0 12 12" fill="currentColor" class="w-2.5 h-2.5 flex-shrink-0" aria-hidden="true">
                            <path d="M6 2.5l4 4H7v3H5V6.5H2l4-4z" />
                          </svg>
                          +12 pts
                        </span>
                        <span class="text-[11px]" :style="{ color: D.inkTertiary }">vs yesterday</span>
                      </div>
                    </div>
                    <button
                      class="hmc-cta-btn-dk flex-shrink-0 flex items-center gap-1.5 rounded-full px-4 py-2 text-[12px] font-semibold"
                      :style="{ background: D.inkPrimary, color: D.inkInverse, boxShadow: '0 2px 6px rgba(0,0,0,0.40)' }"
                    >
                      View details
                      <ArrowRightIcon class="w-3.5 h-3.5 flex-shrink-0" />
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- A2 dark — Module "This week" desktop -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: D.inkTertiary }">Module hero · Tasks — "This week" (desktop width)</p>
              <div style="max-width: 640px;">
                <div
                  class="hmc-a-dk relative rounded-[28px] overflow-hidden"
                  style="
                    min-height: 220px;
                    background-color: #1C1B19;
                    background-image: radial-gradient(circle at 15% 15%, rgba(104,86,178,0.55), rgba(124,214,174,0.28) 40%, rgba(240,168,130,0.22) 85%, transparent 100%);
                  "
                  :style="{ boxShadow: SHADOW_HERO_DK }"
                >
                  <svg
                    class="absolute pointer-events-none"
                    style="width: 160px; height: 160px; left: 28px; top: 28px; opacity: 0.10; color: #7CD6AE;"
                    fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"
                  >
                    <path :d="GLYPH.checkBadge" />
                  </svg>

                  <div class="absolute inset-x-0 bottom-0 p-8 flex items-end justify-between gap-6">
                    <div class="space-y-1.5 min-w-0">
                      <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Tasks · Family progress</p>
                      <p
                        class="font-semibold leading-none tracking-tight"
                        style="font-size: clamp(3rem, 8vw, 8rem); letter-spacing: -0.03em;"
                        :style="{ color: D.inkPrimary }"
                      >23</p>
                      <div class="flex items-center gap-2 flex-wrap">
                        <span
                          class="inline-flex items-center gap-0.5 rounded-full px-2 py-0.5 text-[11px] font-semibold"
                          :style="{ background: D.status.success.soft, color: D.status.success.bold }"
                        >
                          <svg viewBox="0 0 12 12" fill="currentColor" class="w-2.5 h-2.5 flex-shrink-0" aria-hidden="true">
                            <path d="M6 2.5l4 4H7v3H5V6.5H2l4-4z" />
                          </svg>
                          +5 this week
                        </span>
                        <span class="text-[12px]" :style="{ color: D.inkSecondary }">tasks completed · 4 remaining</span>
                      </div>
                    </div>
                    <button
                      class="hmc-cta-btn-dk flex-shrink-0 flex items-center gap-1.5 rounded-full px-5 py-2.5 text-[13px] font-semibold"
                      :style="{ background: D.inkPrimary, color: D.inkInverse, boxShadow: '0 2px 6px rgba(0,0,0,0.40)' }"
                    >
                      See all tasks
                      <ArrowRightIcon class="w-3.5 h-3.5 flex-shrink-0" />
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div><!-- /dark panel A -->

        </div>
      </VariantFrame>
    </section>


    <!-- ══════════════════════════════════════════════════════════════
         VARIANT B — Warm / Fire GradientCard
         Radial gradient: peach-soft → sun-soft → peach-bold (saturated center).
         FireIcon glyph. StatTile contents centered.
         Urgent energy — streaks, "expires soon", countdowns.
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-20">
      <VariantFrame
        label="B"
        caption="Warm / fire GradientCard — peach→sun→deep-peach radial, FireIcon glyph, centered content. High-energy: streaks, countdowns, urgency."
      >
        <div class="w-full space-y-10">

          <!-- ─── LIGHT PANEL B ─────────────────────────────────────── -->
          <div class="rounded-2xl border p-6 space-y-8" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>

            <!-- B1 — Streak card mobile (375px) -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: L.inkTertiary }">Streak · personal best — mobile (375 px)</p>
              <div style="max-width: 375px;">
                <div
                  class="hmc-b-lt relative rounded-[28px] overflow-hidden"
                  style="
                    min-height: 200px;
                    background-color: #FFFFFF;
                    background-image: radial-gradient(circle at 50% 0%, #FCE9E0, #FCF3D2 50%, #BA562E 120%);
                  "
                  :style="{ boxShadow: SHADOW_HERO_LT }"
                >
                  <!-- FireIcon watermark: centered top -->
                  <svg
                    class="absolute pointer-events-none"
                    style="width: 120px; height: 120px; left: 50%; top: -8px; transform: translateX(-50%); opacity: 0.09; color: #BA562E;"
                    fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"
                  >
                    <path d="M12.894 2.553a1 1 0 0 0-1.788 0C9.338 5.116 6.75 8.441 6.75 12a5.25 5.25 0 0 0 10.5 0c0-3.559-2.588-6.884-4.356-9.447ZM10.5 13.5a.75.75 0 0 1 1.5 0 1.5 1.5 0 0 0 3 0 .75.75 0 0 1 1.5 0 3 3 0 0 1-6 0Z" />
                  </svg>

                  <!-- Centered content -->
                  <div class="absolute inset-0 flex flex-col items-center justify-center px-6 py-8 gap-3 text-center">
                    <!-- Fire icon in-content (visible, not watermark) -->
                    <FireIcon class="w-8 h-8 flex-shrink-0" :style="{ color: L.accents.peach.bold }" />
                    <div class="space-y-1">
                      <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: L.accents.peach.bold }">Current streak</p>
                      <p
                        class="font-semibold leading-none tracking-tight"
                        style="font-size: clamp(3rem, 8vw, 8rem); letter-spacing: -0.03em;"
                        :style="{ color: L.inkPrimary }"
                      >12</p>
                      <p class="text-[13px] font-medium" :style="{ color: L.inkPrimary }">days in a row · personal best</p>
                    </div>
                    <p class="text-[12px]" :style="{ color: L.inkSecondary }">Keep it going — don't break the chain</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- B2 — Urgency / countdown desktop (horizontal: content-left + CTA-right, matches A/C desktop rhythm) -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: L.inkTertiary }">Urgent countdown · "Reward expires" — desktop width</p>
              <div style="max-width: 640px;">
                <div
                  class="hmc-b-lt relative rounded-[28px] overflow-hidden"
                  style="
                    min-height: 220px;
                    background-color: #FFFFFF;
                    background-image: radial-gradient(circle at 50% 0%, #FCE9E0, #FCF3D2 50%, #BA562E 120%);
                  "
                  :style="{ boxShadow: SHADOW_HERO_LT }"
                >
                  <!-- Glyph watermark: FireIcon, top-left like A -->
                  <svg
                    class="absolute pointer-events-none"
                    style="width: 160px; height: 160px; left: 28px; top: 28px; opacity: 0.10; color: #BA562E;"
                    fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"
                  >
                    <path d="M12.894 2.553a1 1 0 0 0-1.788 0C9.338 5.116 6.75 8.441 6.75 12a5.25 5.25 0 0 0 10.5 0c0-3.559-2.588-6.884-4.356-9.447ZM10.5 13.5a.75.75 0 0 1 1.5 0 1.5 1.5 0 0 0 3 0 .75.75 0 0 1 1.5 0 3 3 0 0 1-6 0Z" />
                  </svg>

                  <!-- Content anchored bottom: content-left + CTA-right, matches A's desktop layout -->
                  <div class="absolute inset-x-0 bottom-0 p-8 flex items-end justify-between gap-6">
                    <div class="space-y-1.5 min-w-0">
                      <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: L.accents.peach.bold }">Reward expires in</p>
                      <p
                        class="font-semibold leading-none tracking-tight"
                        style="font-size: clamp(3rem, 8vw, 8rem); letter-spacing: -0.03em;"
                        :style="{ color: L.inkPrimary }"
                      >2h</p>
                      <p class="text-[14px] font-medium" :style="{ color: L.inkPrimary }">Movie night · 350 pts to redeem</p>
                    </div>
                    <button
                      class="hmc-cta-btn-lt flex-shrink-0 flex items-center gap-2 rounded-full px-5 py-2.5 text-[13px] font-semibold"
                      :style="{ background: L.accents.peach.bold, color: '#FFFFFF', boxShadow: '0 2px 8px rgba(186,86,46,0.35)' }"
                    >
                      Redeem now
                      <ArrowUpRightIcon class="w-4 h-4 flex-shrink-0" />
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div><!-- /light panel B -->

          <!-- ─── DARK PANEL B ─────────────────────────────────────── -->
          <div class="rounded-2xl border p-6 space-y-8" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

            <!-- B1 dark — Streak mobile -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: D.inkTertiary }">Streak · personal best — mobile (375 px)</p>
              <div style="max-width: 375px;">
                <div
                  class="hmc-b-dk relative rounded-[28px] overflow-hidden"
                  style="
                    min-height: 200px;
                    background-color: #1C1B19;
                    background-image: radial-gradient(circle at 50% 0%, rgba(240,168,130,0.55), rgba(230,196,82,0.30) 50%, rgba(186,86,46,0.55) 120%);
                  "
                  :style="{ boxShadow: SHADOW_HERO_DK }"
                >
                  <svg
                    class="absolute pointer-events-none"
                    style="width: 120px; height: 120px; left: 50%; top: -8px; transform: translateX(-50%); opacity: 0.12; color: #F0A882;"
                    fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"
                  >
                    <path d="M12.894 2.553a1 1 0 0 0-1.788 0C9.338 5.116 6.75 8.441 6.75 12a5.25 5.25 0 0 0 10.5 0c0-3.559-2.588-6.884-4.356-9.447ZM10.5 13.5a.75.75 0 0 1 1.5 0 1.5 1.5 0 0 0 3 0 .75.75 0 0 1 1.5 0 3 3 0 0 1-6 0Z" />
                  </svg>

                  <div class="absolute inset-0 flex flex-col items-center justify-center px-6 py-8 gap-3 text-center">
                    <FireIcon class="w-8 h-8 flex-shrink-0" :style="{ color: D.accents.peach.bold }" />
                    <div class="space-y-1">
                      <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: D.accents.peach.bold }">Current streak</p>
                      <p
                        class="font-semibold leading-none tracking-tight"
                        style="font-size: clamp(3rem, 8vw, 8rem); letter-spacing: -0.03em;"
                        :style="{ color: D.inkPrimary }"
                      >12</p>
                      <p class="text-[13px] font-medium" :style="{ color: D.inkPrimary }">days in a row · personal best</p>
                    </div>
                    <p class="text-[12px]" :style="{ color: D.inkSecondary }">Keep it going — don't break the chain</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- B2 dark — Urgency desktop (horizontal: content-left + CTA-right, matches A/C desktop rhythm) -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: D.inkTertiary }">Urgent countdown · "Reward expires" — desktop width</p>
              <div style="max-width: 640px;">
                <div
                  class="hmc-b-dk relative rounded-[28px] overflow-hidden"
                  style="
                    min-height: 220px;
                    background-color: #1C1B19;
                    background-image: radial-gradient(circle at 50% 0%, rgba(240,168,130,0.55), rgba(230,196,82,0.30) 50%, rgba(186,86,46,0.55) 120%);
                  "
                  :style="{ boxShadow: SHADOW_HERO_DK }"
                >
                  <!-- Glyph watermark: FireIcon, top-left like A -->
                  <svg
                    class="absolute pointer-events-none"
                    style="width: 160px; height: 160px; left: 28px; top: 28px; opacity: 0.12; color: #F0A882;"
                    fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"
                  >
                    <path d="M12.894 2.553a1 1 0 0 0-1.788 0C9.338 5.116 6.75 8.441 6.75 12a5.25 5.25 0 0 0 10.5 0c0-3.559-2.588-6.884-4.356-9.447ZM10.5 13.5a.75.75 0 0 1 1.5 0 1.5 1.5 0 0 0 3 0 .75.75 0 0 1 1.5 0 3 3 0 0 1-6 0Z" />
                  </svg>

                  <!-- Content anchored bottom: content-left + CTA-right, matches A's desktop layout -->
                  <div class="absolute inset-x-0 bottom-0 p-8 flex items-end justify-between gap-6">
                    <div class="space-y-1.5 min-w-0">
                      <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: D.accents.peach.bold }">Reward expires in</p>
                      <p
                        class="font-semibold leading-none tracking-tight"
                        style="font-size: clamp(3rem, 8vw, 8rem); letter-spacing: -0.03em;"
                        :style="{ color: D.inkPrimary }"
                      >2h</p>
                      <p class="text-[14px] font-medium" :style="{ color: D.inkPrimary }">Movie night · 350 pts to redeem</p>
                    </div>
                    <button
                      class="hmc-cta-btn-dk flex-shrink-0 flex items-center gap-2 rounded-full px-5 py-2.5 text-[13px] font-semibold"
                      :style="{ background: D.accents.peach.bold, color: '#1C1C1E', boxShadow: '0 2px 8px rgba(240,168,130,0.30)' }"
                    >
                      Redeem now
                      <ArrowUpRightIcon class="w-4 h-4 flex-shrink-0" />
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div><!-- /dark panel B -->

        </div>
      </VariantFrame>
    </section>


    <!-- ══════════════════════════════════════════════════════════════
         VARIANT C — PhotoCard variant
         Edge-to-edge hero photo + dark scrim gradient from bottom.
         StatTile contents overlaid on scrim, bottom-left.
         Optional CTA button bottom-right.
         Uses Tier 2.2 PhotoCard locked pattern.
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-20">
      <VariantFrame
        label="C"
        caption="PhotoCard variant — edge-to-edge photo, dark scrim, StatTile overlaid bottom-left. Today's meal, upcoming event, family outing."
      >
        <div class="w-full space-y-10">

          <!-- ─── LIGHT PANEL C ─────────────────────────────────────── -->
          <div class="rounded-2xl border p-6 space-y-8" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>

            <!-- C1 — Today's meal plan, mobile (375px) -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: L.inkTertiary }">
                Today's meal plan — mobile (375 px)
                <!-- Alt text note: image must always have descriptive alt; see usage guide -->
              </p>
              <div style="max-width: 375px;">
                <div
                  class="hmc-c-lt relative rounded-[28px] overflow-hidden"
                  style="min-height: 260px; background-color: #2A1F14;"
                  :style="{ boxShadow: SHADOW_HERO_LT }"
                >
                  <!-- Photo — loading="lazy" for real-world use -->
                  <img
                    :src="PHOTO_FAMILY_DINNER"
                    alt="Family dinner with roasted chicken and vegetables on a wooden table"
                    loading="lazy"
                    class="hmc-c-img absolute inset-0 w-full h-full object-cover"
                  />
                  <!-- Scrim: transparent → black, bottom-weighted -->
                  <div class="hmc-scrim absolute inset-0" />

                  <!-- StatTile overlay — bottom-left -->
                  <div class="absolute inset-x-0 bottom-0 p-6 flex items-end justify-between gap-4">
                    <div class="space-y-1 min-w-0">
                      <p class="text-[11px] font-semibold uppercase tracking-widest" style="color: rgba(255,255,255,0.70)">Tonight's dinner</p>
                      <p
                        class="font-semibold leading-tight"
                        style="font-size: clamp(1.125rem, 3.5vw, 1.75rem); letter-spacing: -0.01em; color: #FFFFFF; text-shadow: 0 1px 3px rgba(0,0,0,0.80), 0 2px 8px rgba(0,0,0,0.55);"
                      >Roast chicken with rosemary potatoes</p>
                      <p class="text-[12px]" style="color: rgba(255,255,255,0.80)">Serves 5 · 45 min</p>
                    </div>
                    <button
                      class="hmc-cta-btn-photo flex-shrink-0 flex items-center gap-1.5 rounded-full px-4 py-2 text-[12px] font-semibold"
                    >
                      See plan
                      <ArrowRightIcon class="w-3.5 h-3.5 flex-shrink-0" />
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- C2 — Upcoming family event, desktop width, with hero number -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: L.inkTertiary }">Upcoming event hero — desktop (with StatTile-style number)</p>
              <div style="max-width: 640px;">
                <div
                  class="hmc-c-lt relative rounded-[28px] overflow-hidden"
                  style="min-height: 260px; background-color: #1A1A2E;"
                  :style="{ boxShadow: SHADOW_HERO_LT }"
                >
                  <img
                    :src="PHOTO_FAMILY_DINNER"
                    alt="Family gathering around a table with food and candles"
                    loading="lazy"
                    class="hmc-c-img absolute inset-0 w-full h-full object-cover"
                  />
                  <div class="hmc-scrim absolute inset-0" />

                  <!-- CalendarDaysIcon watermark — subtle, top-right -->
                  <div class="absolute top-6 right-6">
                    <CalendarDaysIcon class="w-7 h-7" style="color: rgba(255,255,255,0.50);" />
                  </div>

                  <div class="absolute inset-x-0 bottom-0 p-8 flex items-end justify-between gap-6">
                    <div class="space-y-1.5 min-w-0">
                      <p class="text-[11px] font-semibold uppercase tracking-widest" style="color: rgba(255,255,255,0.70)">Calendar · Upcoming</p>
                      <!-- Hero number (days away) -->
                      <p
                        class="font-semibold leading-none tracking-tight"
                        style="font-size: clamp(3rem, 8vw, 8rem); letter-spacing: -0.03em; color: #FFFFFF; text-shadow: 0 1px 3px rgba(0,0,0,0.85), 0 2px 12px rgba(0,0,0,0.60);"
                      >3</p>
                      <p class="text-[14px] font-medium" style="color: #FFFFFF; text-shadow: 0 1px 3px rgba(0,0,0,0.80), 0 2px 8px rgba(0,0,0,0.55)">days until Family Game Night</p>
                      <div class="flex items-center gap-2 flex-wrap">
                        <span class="inline-flex items-center gap-0.5 rounded-full px-2.5 py-1 text-[11px] font-semibold" style="background: rgba(255,255,255,0.18); backdrop-filter: blur(8px); color: #FFFFFF; border: 1px solid rgba(255,255,255,0.25);">
                          Sat, Apr 20
                        </span>
                        <span class="text-[12px]" style="color: rgba(255,255,255,0.80)">7:00 PM · Living room</span>
                      </div>
                    </div>
                    <button
                      class="hmc-cta-btn-photo flex-shrink-0 flex items-center gap-2 rounded-full px-5 py-2.5 text-[13px] font-semibold"
                    >
                      View details
                      <ArrowRightIcon class="w-4 h-4 flex-shrink-0" />
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div><!-- /light panel C -->

          <!-- ─── DARK PANEL C ─────────────────────────────────────── -->
          <!-- Dark mode: photo cards are visually identical — the scrim handles contrast.
               The panel background just changes so the card's rounded corners + shadow read correctly. -->
          <div class="rounded-2xl border p-6 space-y-8" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

            <!-- C1 dark — Today's meal mobile -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: D.inkTertiary }">Today's meal plan — mobile (375 px)</p>
              <div style="max-width: 375px;">
                <div
                  class="hmc-c-dk relative rounded-[28px] overflow-hidden"
                  style="min-height: 260px; background-color: #2A1F14;"
                  :style="{ boxShadow: SHADOW_HERO_DK }"
                >
                  <img
                    :src="PHOTO_FAMILY_DINNER"
                    alt="Family dinner with roasted chicken and vegetables on a wooden table"
                    loading="lazy"
                    class="hmc-c-img absolute inset-0 w-full h-full object-cover"
                    style="opacity: 0.85;"
                  />
                  <div class="hmc-scrim absolute inset-0" />
                  <div class="absolute inset-x-0 bottom-0 p-6 flex items-end justify-between gap-4">
                    <div class="space-y-1 min-w-0">
                      <p class="text-[11px] font-semibold uppercase tracking-widest" style="color: rgba(255,255,255,0.70)">Tonight's dinner</p>
                      <p
                        class="font-semibold leading-tight"
                        style="font-size: clamp(1.125rem, 3.5vw, 1.75rem); letter-spacing: -0.01em; color: #FFFFFF; text-shadow: 0 1px 3px rgba(0,0,0,0.80), 0 2px 8px rgba(0,0,0,0.55);"
                      >Roast chicken with rosemary potatoes</p>
                      <p class="text-[12px]" style="color: rgba(255,255,255,0.80)">Serves 5 · 45 min</p>
                    </div>
                    <button
                      class="hmc-cta-btn-photo flex-shrink-0 flex items-center gap-1.5 rounded-full px-4 py-2 text-[12px] font-semibold"
                    >
                      See plan
                      <ArrowRightIcon class="w-3.5 h-3.5 flex-shrink-0" />
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- C2 dark — Upcoming event desktop -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: D.inkTertiary }">Upcoming event hero — desktop</p>
              <div style="max-width: 640px;">
                <div
                  class="hmc-c-dk relative rounded-[28px] overflow-hidden"
                  style="min-height: 260px; background-color: #1A1A2E;"
                  :style="{ boxShadow: SHADOW_HERO_DK }"
                >
                  <img
                    :src="PHOTO_FAMILY_DINNER"
                    alt="Family gathering around a table with food and candles"
                    loading="lazy"
                    class="hmc-c-img absolute inset-0 w-full h-full object-cover"
                    style="opacity: 0.80;"
                  />
                  <div class="hmc-scrim absolute inset-0" />

                  <div class="absolute top-6 right-6">
                    <CalendarDaysIcon class="w-7 h-7" style="color: rgba(255,255,255,0.45);" />
                  </div>

                  <div class="absolute inset-x-0 bottom-0 p-8 flex items-end justify-between gap-6">
                    <div class="space-y-1.5 min-w-0">
                      <p class="text-[11px] font-semibold uppercase tracking-widest" style="color: rgba(255,255,255,0.70)">Calendar · Upcoming</p>
                      <p
                        class="font-semibold leading-none tracking-tight"
                        style="font-size: clamp(3rem, 8vw, 8rem); letter-spacing: -0.03em; color: #FFFFFF; text-shadow: 0 1px 3px rgba(0,0,0,0.85), 0 2px 12px rgba(0,0,0,0.60);"
                      >3</p>
                      <p class="text-[14px] font-medium" style="color: #FFFFFF; text-shadow: 0 1px 3px rgba(0,0,0,0.80), 0 2px 8px rgba(0,0,0,0.55)">days until Family Game Night</p>
                      <div class="flex items-center gap-2 flex-wrap">
                        <span class="inline-flex items-center gap-0.5 rounded-full px-2.5 py-1 text-[11px] font-semibold" style="background: rgba(255,255,255,0.16); backdrop-filter: blur(8px); color: #FFFFFF; border: 1px solid rgba(255,255,255,0.22);">
                          Sat, Apr 20
                        </span>
                        <span class="text-[12px]" style="color: rgba(255,255,255,0.80)">7:00 PM · Living room</span>
                      </div>
                    </div>
                    <button
                      class="hmc-cta-btn-photo flex-shrink-0 flex items-center gap-2 rounded-full px-5 py-2.5 text-[13px] font-semibold"
                    >
                      View details
                      <ArrowRightIcon class="w-4 h-4 flex-shrink-0" />
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div><!-- /dark panel C -->

        </div>
      </VariantFrame>
    </section>


    <!-- ══════════════════════════════════════════════════════════════
         CLAUDE'S PICK
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-12">
      <div
        class="rounded-2xl p-6 space-y-3 flex gap-4 items-start"
        :style="{ background: L.accents.lavender.soft, border: `1px solid ${L.accents.lavender.bold}30` }"
      >
        <SparklesIcon class="w-5 h-5 flex-shrink-0 mt-0.5" :style="{ color: L.accents.lavender.bold }" />
        <div class="space-y-2">
          <h2 class="text-[15px] font-semibold" :style="{ color: L.accents.lavender.bold }">LOCKED — all three variants ship, chosen by context</h2>
          <p class="text-[14px] leading-relaxed" :style="{ color: L.inkPrimary }">
            Unlike most library components, HeroMetricCard ships <strong>all three treatments</strong>. Each serves a different emotional/content purpose, so choosing "one" would limit the component's reach. The variant is a prop — parents pass <code class="text-[12px] font-mono bg-white/60 px-1 rounded">variant="iridescent" | "warm" | "photo"</code> — and every treatment uses the same shape-language, same hero-number sizing, and same corner radius (<code class="text-[12px] font-mono bg-white/60 px-1 rounded">rounded-[28px]</code>). Desktop layouts are unified: content-left, CTA-right, glyph watermark top-left.
          </p>
          <p class="text-[14px] leading-relaxed" :style="{ color: L.inkSecondary }">
            <strong>A — Iridescent</strong> is the default for calm always-on summaries (dashboard greeting, module heroes, empty / loading states). <strong>B — Warm</strong> is reserved for urgency and streak moments (countdowns, expiring rewards, "keep the streak going"); use deliberately so the emotional contrast stays intact. <strong>C — Photo</strong> is for photo-led content (meal plan covers, events with uploaded photos); the two-layer scrim guarantees text legibility no matter what image is behind it. Photo fallback rule: if no image is available, use A — never leave a blank image container.
          </p>
        </div>
      </div>
    </section>


    <!-- ══════════════════════════════════════════════════════════════
         USAGE GUIDE
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-8">
      <div class="rounded-2xl border p-6 space-y-4" :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle }">
        <h2 class="text-[17px] font-semibold" :style="{ color: L.inkPrimary }">When to use HeroMetricCard</h2>

        <ul class="space-y-4 text-[14px]" :style="{ color: L.inkSecondary }">
          <li>
            <strong :style="{ color: L.inkPrimary }">Variant A — calm, always-on summary.</strong>
            Use at the top of the dashboard (greeting + today's score), at the top of module landing pages (calendar "This week", tasks "Family progress", points "Bank balance"), and anywhere you need a premium-feeling summary tile without photo or urgency cues. This is the default hero treatment for Kinhold.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Variant B — streak, urgency, countdown.</strong>
            Reserve for moments where energy and urgency are the message: a streak card on the points page, a countdown card for an expiring reward or upcoming deadline, or any "keep going / act now" promo moment. Use sparingly — overuse kills the emotional contrast.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Variant C — photo-led moments.</strong>
            Use when real content photography is available and load-bearing: today's meal plan cover, a family event with an uploaded photo, a recipe highlight. The photo is the hero; the StatTile content sits on the scrim, never above the fold unprotected.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Photo fallback rule (critical).</strong>
            When no photo is available for a C context (meal plan not yet set, event has no image), fall back to Variant A with the context's natural accent color — do not render a blank, broken, or placeholder-box image. The gradient fill is always a valid and premium substitute.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Hero number sizing.</strong>
            Use <code class="font-mono text-[12px] px-1 py-0.5 rounded" :style="{ background: L.surfaceSunken }">font-size: clamp(3rem, 8vw, 8rem)</code> for the primary stat. This is intentionally smaller than a standalone StatTile because the card's visual context (gradient, glyph, scrim) already provides richness — the number does not need to carry the whole weight alone.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Accessibility: photo alt text.</strong>
            For Variant C, every <code class="font-mono text-[12px] px-1 py-0.5 rounded" :style="{ background: L.surfaceSunken }">&lt;img&gt;</code> must carry a meaningful <code class="font-mono text-[12px] px-1 py-0.5 rounded" :style="{ background: L.surfaceSunken }">alt</code> attribute describing the photo's actual content (not just "photo" or "hero image"). Use <code class="font-mono text-[12px] px-1 py-0.5 rounded" :style="{ background: L.surfaceSunken }">loading="lazy"</code> on all hero images to avoid blocking initial page render.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Corner radius.</strong>
            HeroMetricCard uses <code class="font-mono text-[12px] px-1 py-0.5 rounded" :style="{ background: L.surfaceSunken }">rounded-[28px]</code> — larger than standard FlatCard (16px) or GradientCard (20px) — because the bigger radius signals "this is a primary, featured element" and gives it visual authority at the top of a page.
          </li>
        </ul>
      </div>
    </section>

  </ComponentPage>
</template>

<style scoped>
/* ═══════════════════════════════════════════════════════════════════
   VARIANT A — hover transitions
   ═══════════════════════════════════════════════════════════════════ */
.hmc-a-lt,
.hmc-a-dk {
  transition: box-shadow 220ms cubic-bezier(0.16, 1, 0.3, 1),
              transform   220ms cubic-bezier(0.16, 1, 0.3, 1);
}
.hmc-a-lt:hover { transform: translateY(-3px); box-shadow: 0 6px 16px rgba(28,20,10,0.10), 0 20px 40px rgba(28,20,10,0.08); }
.hmc-a-dk:hover { transform: translateY(-3px); box-shadow: 0 6px 16px rgba(0,0,0,0.60), 0 20px 40px rgba(0,0,0,0.50); }

/* ═══════════════════════════════════════════════════════════════════
   VARIANT B — hover transitions
   ═══════════════════════════════════════════════════════════════════ */
.hmc-b-lt,
.hmc-b-dk {
  transition: box-shadow 220ms cubic-bezier(0.16, 1, 0.3, 1),
              transform   220ms cubic-bezier(0.16, 1, 0.3, 1);
}
.hmc-b-lt:hover { transform: translateY(-3px); box-shadow: 0 6px 16px rgba(28,20,10,0.10), 0 20px 40px rgba(28,20,10,0.08); }
.hmc-b-dk:hover { transform: translateY(-3px); box-shadow: 0 6px 16px rgba(0,0,0,0.60), 0 20px 40px rgba(0,0,0,0.50); }

/* ═══════════════════════════════════════════════════════════════════
   VARIANT C — photo hover + image zoom
   ═══════════════════════════════════════════════════════════════════ */
.hmc-c-lt,
.hmc-c-dk {
  transition: box-shadow 220ms cubic-bezier(0.16, 1, 0.3, 1),
              transform   220ms cubic-bezier(0.16, 1, 0.3, 1);
}
.hmc-c-lt:hover { transform: translateY(-3px); box-shadow: 0 6px 16px rgba(28,20,10,0.10), 0 20px 40px rgba(28,20,10,0.08); }
.hmc-c-dk:hover { transform: translateY(-3px); box-shadow: 0 6px 16px rgba(0,0,0,0.60), 0 20px 40px rgba(0,0,0,0.50); }
.hmc-c-lt:hover .hmc-c-img,
.hmc-c-dk:hover .hmc-c-img { transform: scale(1.04); }
.hmc-c-img {
  transition: transform 420ms cubic-bezier(0.16, 1, 0.3, 1);
}

/* ═══════════════════════════════════════════════════════════════════
   SCRIM — shared between C light and dark
   Two-layer system so text stays legible regardless of the photo:
   (1) subtle full-card darken mutes bright / busy imagery
   (2) stronger bottom-weighted gradient anchors the text area
   The whole scrim reaches up to ~90% of card height so the photo
   still "breathes" at the very top while text below always reads.
   ═══════════════════════════════════════════════════════════════════ */
.hmc-scrim {
  background:
    /* full-card subtle darken */
    linear-gradient(rgba(0, 0, 0, 0.18), rgba(0, 0, 0, 0.18)),
    /* bottom-weighted main scrim */
    linear-gradient(
      to top,
      rgba(0, 0, 0, 0.90) 0%,
      rgba(0, 0, 0, 0.72) 25%,
      rgba(0, 0, 0, 0.45) 50%,
      rgba(0, 0, 0, 0.18) 75%,
      transparent 95%
    );
  pointer-events: none;
}

/* ═══════════════════════════════════════════════════════════════════
   CTA BUTTON — photo variant (white glass, works on any scrim)
   ═══════════════════════════════════════════════════════════════════ */
.hmc-cta-btn-photo {
  background: rgba(255, 255, 255, 0.90);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
  border: 1px solid rgba(255, 255, 255, 0.55);
  color: #1C1C1E;
  transition: background 150ms ease;
}
.hmc-cta-btn-photo:hover {
  background: rgba(255, 255, 255, 1);
}

/* ═══════════════════════════════════════════════════════════════════
   CTA BUTTON — gradient variants (light / dark)
   ═══════════════════════════════════════════════════════════════════ */
.hmc-cta-btn-lt,
.hmc-cta-btn-dk {
  transition: opacity 150ms ease, transform 120ms ease;
}
.hmc-cta-btn-lt:hover,
.hmc-cta-btn-dk:hover { opacity: 0.88; transform: translateY(-1px); }
.hmc-cta-btn-lt:active,
.hmc-cta-btn-dk:active { opacity: 1; transform: translateY(0); }

/* ═══════════════════════════════════════════════════════════════════
   REDUCED MOTION — disable all transforms, keep shadow transitions
   ═══════════════════════════════════════════════════════════════════ */
@media (prefers-reduced-motion: reduce) {
  .hmc-a-lt, .hmc-a-dk,
  .hmc-b-lt, .hmc-b-dk,
  .hmc-c-lt, .hmc-c-dk {
    transition: box-shadow 200ms;
    transform: none !important;
  }
  .hmc-c-lt:hover .hmc-c-img,
  .hmc-c-dk:hover .hmc-c-img {
    transform: none !important;
  }
  .hmc-cta-btn-lt,
  .hmc-cta-btn-dk,
  .hmc-cta-btn-photo {
    transition: opacity 150ms;
    transform: none !important;
  }
}
</style>
