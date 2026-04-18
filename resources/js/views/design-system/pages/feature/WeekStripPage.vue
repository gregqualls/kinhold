<script setup>
import { ref } from 'vue'
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import { SparklesIcon } from '@heroicons/vue/24/outline'

// ── Palette ───────────────────────────────────────────────────────────────────
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

const D = {
  surfaceApp:    '#141311', surfaceRaised: '#1C1B19', surfaceSunken: '#161513', surfaceOverlay: '#242220',
  inkPrimary:    '#F0EDE9', inkSecondary:  '#A09C97', inkTertiary:   '#6E6B67', inkInverse: '#1C1C1E',
  borderSubtle:  '#2C2A27', borderStrong:  '#403E3A',
  accents: {
    lavender: { soft: '#302A48', bold: '#B6A8E6' },
    peach:    { soft: '#3E241A', bold: '#F0A882' },
    mint:     { soft: '#18342A', bold: '#7CD6AE' },
    sun:      { soft: '#342C0A', bold: '#E6C452' },
  },
}

// ── Week data ─────────────────────────────────────────────────────────────────
// Mon = index 0, Sun = index 6. Today = index 2 (Wed 14). Selected starts at Thu (3).
const WEEK = [
  { letter: 'Mon', num: 12, events: [] },
  { letter: 'Tue', num: 13, events: ['lavender'] },
  { letter: 'Wed', num: 14, events: ['peach', 'mint'] },
  { letter: 'Thu', num: 15, events: ['lavender'] },
  { letter: 'Fri', num: 16, events: ['sun', 'mint', 'lavender', 'peach'] },
  { letter: 'Sat', num: 17, events: ['mint'] },
  { letter: 'Sun', num: 18, events: [] },
]
const TODAY_INDEX = 2

// ── Two-week data (14 days) ───────────────────────────────────────────────────
const TWO_WEEKS = [
  { letter: 'Mon', num: 12, events: [] },
  { letter: 'Tue', num: 13, events: ['lavender'] },
  { letter: 'Wed', num: 14, events: ['peach', 'mint'] },
  { letter: 'Thu', num: 15, events: ['lavender'] },
  { letter: 'Fri', num: 16, events: ['sun', 'mint', 'lavender', 'peach'] },
  { letter: 'Sat', num: 17, events: ['mint'] },
  { letter: 'Sun', num: 18, events: [] },
  { letter: 'Mon', num: 19, events: ['sun'] },
  { letter: 'Tue', num: 20, events: [] },
  { letter: 'Wed', num: 21, events: ['peach', 'lavender'] },
  { letter: 'Thu', num: 22, events: [] },
  { letter: 'Fri', num: 23, events: ['mint', 'sun'] },
  { letter: 'Sat', num: 24, events: ['lavender', 'peach', 'mint'] },
  { letter: 'Sun', num: 25, events: [] },
]

// ── Selected-day state per variant × mode ────────────────────────────────────
const selA_lt  = ref(3)
const selA_dk  = ref(3)
const selA2_lt = ref(3)  // two-week light
const selA2_dk = ref(3)  // two-week dark
const selB_lt  = ref(3)
const selB_dk  = ref(3)
const selB2_lt = ref(3)
const selB2_dk = ref(3)
const selC_lt  = ref(3)
const selC_dk  = ref(3)
const selC2_lt = ref(3)
const selC2_dk = ref(3)

// ── Dot color helpers ─────────────────────────────────────────────────────────
function dotColor(accent, palette) {
  return palette.accents[accent]?.bold ?? palette.inkTertiary
}

// Limit visible dots to 4; the 5th becomes a "+" indicator
function visibleDots(events) {
  return events.slice(0, 4)
}
function extraCount(events) {
  return events.length > 4 ? events.length - 4 : 0
}
</script>

<template>
  <ComponentPage
    title="5.4 WeekStrip"
    description="Horizontal day-of-week selector. Seven days (or up to 14 for two-week view) — day-of-week letter, date number, and optional event dots. Used on calendar week view, meal plan week view, task review, and leaderboard period selector."
    status="scaffolded"
  >

    <!-- ══════════════════════════════════════════════════════════════
         VARIANT A — Compact centered column per day
         Day letter on top, date number in a circle, dot strip below.
         Today = solid accent-bold circle. Selected = 2px lavender ring.
         Feels like iOS Calendar widget.
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="A" caption="Compact column — letter / circled number / dot strip. Today = filled circle. Selected = lavender ring.">
        <div class="w-full space-y-10">

          <!-- LIGHT — 7-day -->
          <div class="rounded-2xl border p-6 space-y-6"
               :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode · 7 days</p>

            <div class="flex items-end justify-between gap-1">
              <button
                v-for="(day, i) in WEEK"
                :key="i"
                class="week-strip-btn flex flex-col items-center gap-1 min-w-0 flex-1 py-2"
                @click="selA_lt = i"
              >
                <!-- Day letter -->
                <span
                  class="text-[10px] font-semibold uppercase tracking-widest leading-none"
                  :style="{ color: i === TODAY_INDEX ? L.accents.lavender.bold : L.inkTertiary }"
                >{{ day.letter.slice(0, 1) }}</span>

                <!-- Date number — circle treatment -->
                <span
                  class="inline-flex items-center justify-center w-8 h-8 rounded-full text-[15px] font-semibold leading-none transition-all"
                  :style="i === TODAY_INDEX
                    ? { background: L.accents.lavender.bold, color: '#FFFFFF', boxShadow: 'none' }
                    : selA_lt === i
                      ? { background: 'transparent', color: L.inkPrimary, boxShadow: `0 0 0 2px ${L.accents.lavender.bold}` }
                      : { background: 'transparent', color: L.inkPrimary }"
                >{{ day.num }}</span>

                <!-- Event dot strip -->
                <div class="flex items-center gap-[3px] h-[6px]">
                  <span
                    v-for="(evt, di) in visibleDots(day.events)"
                    :key="di"
                    class="inline-block w-[5px] h-[5px] rounded-full flex-shrink-0"
                    :style="{ background: dotColor(evt, L) }"
                  />
                  <span
                    v-if="extraCount(day.events) > 0"
                    class="text-[9px] font-bold leading-none"
                    :style="{ color: L.inkTertiary }"
                  >+{{ extraCount(day.events) }}</span>
                </div>
              </button>
            </div>

            <p class="text-[11px]" :style="{ color: L.inkTertiary }">
              Today (Wed) = filled lavender circle. Selected (Thu) = 2px lavender ring. Fri has 4 dots + overflow.
              Click any day to change selection.
            </p>
          </div>

          <!-- DARK — 7-day -->
          <div class="rounded-2xl border p-6 space-y-6"
               :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode · 7 days</p>

            <div class="flex items-end justify-between gap-1">
              <button
                v-for="(day, i) in WEEK"
                :key="i"
                class="week-strip-btn flex flex-col items-center gap-1 min-w-0 flex-1 py-2"
                @click="selA_dk = i"
              >
                <span
                  class="text-[10px] font-semibold uppercase tracking-widest leading-none"
                  :style="{ color: i === TODAY_INDEX ? D.accents.lavender.bold : D.inkTertiary }"
                >{{ day.letter.slice(0, 1) }}</span>

                <span
                  class="inline-flex items-center justify-center w-8 h-8 rounded-full text-[15px] font-semibold leading-none transition-all"
                  :style="i === TODAY_INDEX
                    ? { background: D.accents.lavender.bold, color: D.inkInverse }
                    : selA_dk === i
                      ? { background: 'transparent', color: D.inkPrimary, boxShadow: `0 0 0 2px ${D.accents.lavender.bold}` }
                      : { background: 'transparent', color: D.inkPrimary }"
                >{{ day.num }}</span>

                <div class="flex items-center gap-[3px] h-[6px]">
                  <span
                    v-for="(evt, di) in visibleDots(day.events)"
                    :key="di"
                    class="inline-block w-[5px] h-[5px] rounded-full flex-shrink-0"
                    :style="{ background: dotColor(evt, D) }"
                  />
                  <span
                    v-if="extraCount(day.events) > 0"
                    class="text-[9px] font-bold leading-none"
                    :style="{ color: D.inkTertiary }"
                  >+{{ extraCount(day.events) }}</span>
                </div>
              </button>
            </div>

            <p class="text-[11px]" :style="{ color: D.inkTertiary }">
              Dark today: #B6A8E6 filled circle with charcoal text — maintains the lavender-bold brand anchor.
              Selected ring uses the same bold value at full opacity for clear distinction.
            </p>
          </div>

          <!-- LIGHT — 14-day two-week scrollable -->
          <div class="rounded-2xl border p-6 space-y-4"
               :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode · 14 days (scroll)</p>
            <div class="overflow-x-auto pb-2" style="scrollbar-width: none; -ms-overflow-style: none; scroll-snap-type: x mandatory;">
              <div class="flex gap-1" style="min-width: max-content;">
                <button
                  v-for="(day, i) in TWO_WEEKS"
                  :key="i"
                  class="week-strip-btn flex flex-col items-center gap-1 py-2 flex-shrink-0"
                  style="width: 40px; scroll-snap-align: start;"
                  @click="selA2_lt = i"
                >
                  <span
                    class="text-[10px] font-semibold uppercase tracking-widest leading-none"
                    :style="{ color: i === TODAY_INDEX ? L.accents.lavender.bold : L.inkTertiary }"
                  >{{ day.letter.slice(0, 1) }}</span>
                  <span
                    class="inline-flex items-center justify-center w-8 h-8 rounded-full text-[15px] font-semibold leading-none transition-all"
                    :style="i === TODAY_INDEX
                      ? { background: L.accents.lavender.bold, color: '#FFFFFF' }
                      : selA2_lt === i
                        ? { background: 'transparent', color: L.inkPrimary, boxShadow: `0 0 0 2px ${L.accents.lavender.bold}` }
                        : { background: 'transparent', color: L.inkPrimary }"
                  >{{ day.num }}</span>
                  <div class="flex items-center gap-[3px] h-[6px]">
                    <span
                      v-for="(evt, di) in visibleDots(day.events)"
                      :key="di"
                      class="inline-block w-[5px] h-[5px] rounded-full flex-shrink-0"
                      :style="{ background: dotColor(evt, L) }"
                    />
                    <span
                      v-if="extraCount(day.events) > 0"
                      class="text-[9px] font-bold leading-none"
                      :style="{ color: L.inkTertiary }"
                    >+{{ extraCount(day.events) }}</span>
                  </div>
                </button>
              </div>
            </div>
            <p class="text-[11px]" :style="{ color: L.inkTertiary }">
              14-day variant uses overflow-x-auto + scroll-snap-type: x mandatory. Each slot fixed 40px wide.
              Swipe / drag to reveal week 2.
            </p>
          </div>

          <!-- DARK — 14-day two-week scrollable -->
          <div class="rounded-2xl border p-6 space-y-4"
               :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode · 14 days (scroll)</p>
            <div class="overflow-x-auto pb-2" style="scrollbar-width: none; -ms-overflow-style: none; scroll-snap-type: x mandatory;">
              <div class="flex gap-1" style="min-width: max-content;">
                <button
                  v-for="(day, i) in TWO_WEEKS"
                  :key="i"
                  class="week-strip-btn flex flex-col items-center gap-1 py-2 flex-shrink-0"
                  style="width: 40px; scroll-snap-align: start;"
                  @click="selA2_dk = i"
                >
                  <span
                    class="text-[10px] font-semibold uppercase tracking-widest leading-none"
                    :style="{ color: i === TODAY_INDEX ? D.accents.lavender.bold : D.inkTertiary }"
                  >{{ day.letter.slice(0, 1) }}</span>
                  <span
                    class="inline-flex items-center justify-center w-8 h-8 rounded-full text-[15px] font-semibold leading-none transition-all"
                    :style="i === TODAY_INDEX
                      ? { background: D.accents.lavender.bold, color: D.inkInverse }
                      : selA2_dk === i
                        ? { background: 'transparent', color: D.inkPrimary, boxShadow: `0 0 0 2px ${D.accents.lavender.bold}` }
                        : { background: 'transparent', color: D.inkPrimary }"
                  >{{ day.num }}</span>
                  <div class="flex items-center gap-[3px] h-[6px]">
                    <span
                      v-for="(evt, di) in visibleDots(day.events)"
                      :key="di"
                      class="inline-block w-[5px] h-[5px] rounded-full flex-shrink-0"
                      :style="{ background: dotColor(evt, D) }"
                    />
                    <span
                      v-if="extraCount(day.events) > 0"
                      class="text-[9px] font-bold leading-none"
                      :style="{ color: D.inkTertiary }"
                    >+{{ extraCount(day.events) }}</span>
                  </div>
                </button>
              </div>
            </div>
          </div>

          <!-- Mobile 375px simulation -->
          <div class="space-y-3">
            <p class="text-[11px] font-semibold uppercase tracking-widest px-1" :style="{ color: L.inkTertiary }">
              Mobile (375px) — 7 days fill width
            </p>
            <div class="max-w-[375px] rounded-2xl border p-4"
                 :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle }">
              <div class="flex items-end justify-between gap-0.5">
                <button
                  v-for="(day, i) in WEEK"
                  :key="i"
                  class="week-strip-btn flex flex-col items-center gap-0.5 min-w-0 flex-1 py-1.5"
                  @click="selA_lt = i"
                >
                  <span
                    class="text-[9px] font-semibold uppercase tracking-widest leading-none"
                    :style="{ color: i === TODAY_INDEX ? L.accents.lavender.bold : L.inkTertiary }"
                  >{{ day.letter.slice(0, 1) }}</span>
                  <span
                    class="inline-flex items-center justify-center w-7 h-7 rounded-full text-[14px] font-semibold leading-none transition-all"
                    :style="i === TODAY_INDEX
                      ? { background: L.accents.lavender.bold, color: '#FFFFFF' }
                      : selA_lt === i
                        ? { background: 'transparent', color: L.inkPrimary, boxShadow: `0 0 0 2px ${L.accents.lavender.bold}` }
                        : { background: 'transparent', color: L.inkPrimary }"
                  >{{ day.num }}</span>
                  <div class="flex items-center gap-[2px] h-[5px]">
                    <span
                      v-for="(evt, di) in visibleDots(day.events).slice(0, 3)"
                      :key="di"
                      class="inline-block w-[4px] h-[4px] rounded-full flex-shrink-0"
                      :style="{ background: dotColor(evt, L) }"
                    />
                  </div>
                </button>
              </div>
            </div>
            <p class="text-[11px] px-1" :style="{ color: L.inkTertiary }">
              On 375px all 7 days fit without cramping — slots flex-1, circles 28px, dots capped at 3 to avoid overflow.
            </p>
          </div>

        </div>
      </VariantFrame>
      <p class="mt-3 text-sm px-1" :style="{ color: L.inkSecondary }">
        Variant A is the most information-dense take — the dot strip makes event presence scannable at a glance
        without entering the day. Ideal for calendar week view headers and task review pickers where minimalism
        and content-focus are the priority.
      </p>
    </section>


    <!-- ══════════════════════════════════════════════════════════════
         VARIANT B — Pill-style (confirmed language)
         Each day is a rounded-full pill (h-14 × w-10).
         Two-line content: day letter top, date number bottom.
         Active = filled lavender-bold pill + inverse text.
         Inactive = transparent with subtle border.
         The most on-brand with Kinhold's pill shape language.
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="B" caption="Pill-style — rounded-full h-14 w-10 · filled active · transparent inactive · Kinhold's native pill language">
        <div class="w-full space-y-10">

          <!-- LIGHT — 7-day -->
          <div class="rounded-2xl border p-6 space-y-6"
               :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode · 7 days</p>

            <div class="flex items-center justify-between gap-1.5">
              <button
                v-for="(day, i) in WEEK"
                :key="i"
                class="week-strip-pill flex flex-col items-center justify-center gap-0.5 rounded-full flex-shrink-0 transition-all"
                style="width: 40px; height: 56px;"
                :style="i === TODAY_INDEX
                  ? { background: L.accents.lavender.soft, border: `1.5px solid ${L.accents.lavender.bold}` }
                  : selB_lt === i
                    ? { background: L.accents.lavender.bold, border: `1.5px solid ${L.accents.lavender.bold}` }
                    : { background: 'transparent', border: `1.5px solid ${L.borderSubtle}` }"
                @click="selB_lt = i"
              >
                <!-- Day letter -->
                <span
                  class="text-[10px] font-semibold uppercase tracking-widest leading-none"
                  :style="selB_lt === i && i !== TODAY_INDEX
                    ? { color: '#FFFFFF' }
                    : i === TODAY_INDEX
                      ? { color: L.accents.lavender.bold }
                      : { color: L.inkTertiary }"
                >{{ day.letter.slice(0, 3) }}</span>
                <!-- Date number -->
                <span
                  class="text-[16px] font-semibold leading-none"
                  :style="selB_lt === i && i !== TODAY_INDEX
                    ? { color: '#FFFFFF' }
                    : i === TODAY_INDEX
                      ? { color: L.accents.lavender.bold }
                      : { color: L.inkPrimary }"
                >{{ day.num }}</span>
              </button>
            </div>

            <p class="text-[11px]" :style="{ color: L.inkTertiary }">
              Today (Wed) = lavender-soft fill + lavender-bold text, always visible as an anchor.
              Selected (Thu) = filled lavender-bold pill with white inverse text.
              Inactive = transparent with border-subtle outline.
            </p>
          </div>

          <!-- DARK — 7-day -->
          <div class="rounded-2xl border p-6 space-y-6"
               :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode · 7 days</p>

            <div class="flex items-center justify-between gap-1.5">
              <button
                v-for="(day, i) in WEEK"
                :key="i"
                class="week-strip-pill flex flex-col items-center justify-center gap-0.5 rounded-full flex-shrink-0 transition-all"
                style="width: 40px; height: 56px;"
                :style="i === TODAY_INDEX
                  ? { background: D.accents.lavender.soft, border: `1.5px solid ${D.accents.lavender.bold}` }
                  : selB_dk === i
                    ? { background: D.accents.lavender.bold, border: `1.5px solid ${D.accents.lavender.bold}` }
                    : { background: 'transparent', border: `1.5px solid ${D.borderSubtle}` }"
                @click="selB_dk = i"
              >
                <span
                  class="text-[10px] font-semibold uppercase tracking-widest leading-none"
                  :style="selB_dk === i && i !== TODAY_INDEX
                    ? { color: D.inkInverse }
                    : i === TODAY_INDEX
                      ? { color: D.accents.lavender.bold }
                      : { color: D.inkTertiary }"
                >{{ day.letter.slice(0, 3) }}</span>
                <span
                  class="text-[16px] font-semibold leading-none"
                  :style="selB_dk === i && i !== TODAY_INDEX
                    ? { color: D.inkInverse }
                    : i === TODAY_INDEX
                      ? { color: D.accents.lavender.bold }
                      : { color: D.inkPrimary }"
                >{{ day.num }}</span>
              </button>
            </div>

            <p class="text-[11px]" :style="{ color: D.inkTertiary }">
              Dark selected: deep-lavender bold (#B6A8E6) fill inverts to charcoal (#1C1C1E) text.
              Today soft-fill (#302A48) with #B6A8E6 text — calm but unmistakable.
            </p>
          </div>

          <!-- LIGHT — 14-day two-week scrollable -->
          <div class="rounded-2xl border p-6 space-y-4"
               :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode · 14 days (scroll)</p>
            <div class="overflow-x-auto pb-2" style="scrollbar-width: none; -ms-overflow-style: none; scroll-snap-type: x mandatory;">
              <div class="flex gap-1.5" style="min-width: max-content;">
                <button
                  v-for="(day, i) in TWO_WEEKS"
                  :key="i"
                  class="week-strip-pill flex flex-col items-center justify-center gap-0.5 rounded-full flex-shrink-0 transition-all"
                  style="width: 40px; height: 56px; scroll-snap-align: start;"
                  :style="i === TODAY_INDEX
                    ? { background: L.accents.lavender.soft, border: `1.5px solid ${L.accents.lavender.bold}` }
                    : selB2_lt === i
                      ? { background: L.accents.lavender.bold, border: `1.5px solid ${L.accents.lavender.bold}` }
                      : { background: 'transparent', border: `1.5px solid ${L.borderSubtle}` }"
                  @click="selB2_lt = i"
                >
                  <span
                    class="text-[10px] font-semibold uppercase tracking-widest leading-none"
                    :style="selB2_lt === i && i !== TODAY_INDEX
                      ? { color: '#FFFFFF' }
                      : i === TODAY_INDEX
                        ? { color: L.accents.lavender.bold }
                        : { color: L.inkTertiary }"
                  >{{ day.letter.slice(0, 3) }}</span>
                  <span
                    class="text-[16px] font-semibold leading-none"
                    :style="selB2_lt === i && i !== TODAY_INDEX
                      ? { color: '#FFFFFF' }
                      : i === TODAY_INDEX
                        ? { color: L.accents.lavender.bold }
                        : { color: L.inkPrimary }"
                  >{{ day.num }}</span>
                </button>
              </div>
            </div>
            <p class="text-[11px]" :style="{ color: L.inkTertiary }">
              14-day pills scroll horizontally with snap. Each pill 40px wide, gap 6px.
            </p>
          </div>

          <!-- DARK — 14-day two-week scrollable -->
          <div class="rounded-2xl border p-6 space-y-4"
               :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode · 14 days (scroll)</p>
            <div class="overflow-x-auto pb-2" style="scrollbar-width: none; -ms-overflow-style: none; scroll-snap-type: x mandatory;">
              <div class="flex gap-1.5" style="min-width: max-content;">
                <button
                  v-for="(day, i) in TWO_WEEKS"
                  :key="i"
                  class="week-strip-pill flex flex-col items-center justify-center gap-0.5 rounded-full flex-shrink-0 transition-all"
                  style="width: 40px; height: 56px; scroll-snap-align: start;"
                  :style="i === TODAY_INDEX
                    ? { background: D.accents.lavender.soft, border: `1.5px solid ${D.accents.lavender.bold}` }
                    : selB2_dk === i
                      ? { background: D.accents.lavender.bold, border: `1.5px solid ${D.accents.lavender.bold}` }
                      : { background: 'transparent', border: `1.5px solid ${D.borderSubtle}` }"
                  @click="selB2_dk = i"
                >
                  <span
                    class="text-[10px] font-semibold uppercase tracking-widest leading-none"
                    :style="selB2_dk === i && i !== TODAY_INDEX
                      ? { color: D.inkInverse }
                      : i === TODAY_INDEX
                        ? { color: D.accents.lavender.bold }
                        : { color: D.inkTertiary }"
                  >{{ day.letter.slice(0, 3) }}</span>
                  <span
                    class="text-[16px] font-semibold leading-none"
                    :style="selB2_dk === i && i !== TODAY_INDEX
                      ? { color: D.inkInverse }
                      : i === TODAY_INDEX
                        ? { color: D.accents.lavender.bold }
                        : { color: D.inkPrimary }"
                  >{{ day.num }}</span>
                </button>
              </div>
            </div>
          </div>

          <!-- Mobile 375px simulation -->
          <div class="space-y-3">
            <p class="text-[11px] font-semibold uppercase tracking-widest px-1" :style="{ color: L.inkTertiary }">
              Mobile (375px) — 7 days fill width
            </p>
            <div class="max-w-[375px] rounded-2xl border p-4"
                 :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle }">
              <div class="flex items-center justify-between gap-1">
                <button
                  v-for="(day, i) in WEEK"
                  :key="i"
                  class="week-strip-pill flex flex-col items-center justify-center gap-0.5 rounded-full flex-shrink-0 transition-all"
                  style="width: 38px; height: 52px;"
                  :style="i === TODAY_INDEX
                    ? { background: L.accents.lavender.soft, border: `1.5px solid ${L.accents.lavender.bold}` }
                    : selB_lt === i
                      ? { background: L.accents.lavender.bold, border: `1.5px solid ${L.accents.lavender.bold}` }
                      : { background: 'transparent', border: `1.5px solid ${L.borderSubtle}` }"
                  @click="selB_lt = i"
                >
                  <span
                    class="text-[9px] font-semibold uppercase tracking-widest leading-none"
                    :style="selB_lt === i && i !== TODAY_INDEX
                      ? { color: '#FFFFFF' }
                      : i === TODAY_INDEX
                        ? { color: L.accents.lavender.bold }
                        : { color: L.inkTertiary }"
                  >{{ day.letter.slice(0, 1) }}</span>
                  <span
                    class="text-[15px] font-semibold leading-none"
                    :style="selB_lt === i && i !== TODAY_INDEX
                      ? { color: '#FFFFFF' }
                      : i === TODAY_INDEX
                        ? { color: L.accents.lavender.bold }
                        : { color: L.inkPrimary }"
                  >{{ day.num }}</span>
                </button>
              </div>
            </div>
            <p class="text-[11px] px-1" :style="{ color: L.inkTertiary }">
              At 375px with gap-1, all 7 pills sit comfortably — no cramping. Single letter on mobile vs 3-letter on desktop.
            </p>
          </div>

        </div>
      </VariantFrame>
      <p class="mt-3 text-sm px-1" :style="{ color: L.inkSecondary }">
        Variant B uses Kinhold's native pill shape vocabulary — the same rounded-full forms already present
        in buttons, chips, and tab groups. The filled active pill reads instantly and needs no supplemental
        ring or underline. Clean and versatile enough to appear in any context where event dots aren't needed.
      </p>
    </section>


    <!-- ══════════════════════════════════════════════════════════════
         VARIANT C — Card-style pills with event dots INSIDE
         Wider rounded-xl pills (w-12, radius 16px, not full-round).
         Content: day letter + number + up to 3 colored event dots.
         Great for meal planner / denser info contexts.
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="C" caption="Card-style pill — rounded-xl w-12 · dots inside each pill · best for event-density surfaces">
        <div class="w-full space-y-10">

          <!-- LIGHT — 7-day -->
          <div class="rounded-2xl border p-6 space-y-6"
               :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode · 7 days</p>

            <div class="flex items-end justify-between gap-1.5">
              <button
                v-for="(day, i) in WEEK"
                :key="i"
                class="week-strip-card flex flex-col items-center justify-start pt-2.5 pb-2 gap-1 rounded-xl flex-shrink-0 transition-all"
                style="width: 44px; min-height: 70px;"
                :style="i === TODAY_INDEX
                  ? { background: L.accents.lavender.soft, border: `1.5px solid ${L.accents.lavender.bold}` }
                  : selC_lt === i
                    ? { background: L.accents.lavender.bold, border: `1.5px solid ${L.accents.lavender.bold}` }
                    : { background: L.surfaceRaised, border: `1.5px solid ${L.borderSubtle}` }"
                @click="selC_lt = i"
              >
                <!-- Day letter -->
                <span
                  class="text-[10px] font-semibold uppercase tracking-widest leading-none"
                  :style="selC_lt === i && i !== TODAY_INDEX
                    ? { color: 'rgba(255,255,255,0.75)' }
                    : i === TODAY_INDEX
                      ? { color: L.accents.lavender.bold }
                      : { color: L.inkTertiary }"
                >{{ day.letter.slice(0, 3) }}</span>
                <!-- Date number -->
                <span
                  class="text-[15px] font-semibold leading-none"
                  :style="selC_lt === i && i !== TODAY_INDEX
                    ? { color: '#FFFFFF' }
                    : i === TODAY_INDEX
                      ? { color: L.accents.lavender.bold }
                      : { color: L.inkPrimary }"
                >{{ day.num }}</span>
                <!-- Event dots inside the pill -->
                <div class="flex flex-col items-center gap-[3px]">
                  <div class="flex items-center gap-[3px]">
                    <span
                      v-for="(evt, di) in visibleDots(day.events).slice(0, 2)"
                      :key="di"
                      class="inline-block w-[5px] h-[5px] rounded-full flex-shrink-0"
                      :style="{ background: selC_lt === i && i !== TODAY_INDEX ? 'rgba(255,255,255,0.7)' : dotColor(evt, L) }"
                    />
                  </div>
                  <div class="flex items-center gap-[3px]">
                    <span
                      v-for="(evt, di) in visibleDots(day.events).slice(2, 4)"
                      :key="di"
                      class="inline-block w-[5px] h-[5px] rounded-full flex-shrink-0"
                      :style="{ background: selC_lt === i && i !== TODAY_INDEX ? 'rgba(255,255,255,0.7)' : dotColor(evt, L) }"
                    />
                    <span
                      v-if="extraCount(day.events) > 0"
                      class="text-[8px] font-bold leading-none"
                      :style="{ color: selC_lt === i && i !== TODAY_INDEX ? 'rgba(255,255,255,0.75)' : L.inkTertiary }"
                    >+{{ extraCount(day.events) }}</span>
                  </div>
                </div>
              </button>
            </div>

            <p class="text-[11px]" :style="{ color: L.inkTertiary }">
              Dots live inside each card — coloured by category. On active lavender fill they become semi-transparent
              white for legibility. Fri has 4 dots (2×2 grid) — packed but scannable.
            </p>
          </div>

          <!-- DARK — 7-day -->
          <div class="rounded-2xl border p-6 space-y-6"
               :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode · 7 days</p>

            <div class="flex items-end justify-between gap-1.5">
              <button
                v-for="(day, i) in WEEK"
                :key="i"
                class="week-strip-card flex flex-col items-center justify-start pt-2.5 pb-2 gap-1 rounded-xl flex-shrink-0 transition-all"
                style="width: 44px; min-height: 70px;"
                :style="i === TODAY_INDEX
                  ? { background: D.accents.lavender.soft, border: `1.5px solid ${D.accents.lavender.bold}` }
                  : selC_dk === i
                    ? { background: D.accents.lavender.bold, border: `1.5px solid ${D.accents.lavender.bold}` }
                    : { background: D.surfaceRaised, border: `1.5px solid ${D.borderSubtle}` }"
                @click="selC_dk = i"
              >
                <span
                  class="text-[10px] font-semibold uppercase tracking-widest leading-none"
                  :style="selC_dk === i && i !== TODAY_INDEX
                    ? { color: D.inkInverse, opacity: '0.75' }
                    : i === TODAY_INDEX
                      ? { color: D.accents.lavender.bold }
                      : { color: D.inkTertiary }"
                >{{ day.letter.slice(0, 3) }}</span>
                <span
                  class="text-[15px] font-semibold leading-none"
                  :style="selC_dk === i && i !== TODAY_INDEX
                    ? { color: D.inkInverse }
                    : i === TODAY_INDEX
                      ? { color: D.accents.lavender.bold }
                      : { color: D.inkPrimary }"
                >{{ day.num }}</span>
                <div class="flex flex-col items-center gap-[3px]">
                  <div class="flex items-center gap-[3px]">
                    <span
                      v-for="(evt, di) in visibleDots(day.events).slice(0, 2)"
                      :key="di"
                      class="inline-block w-[5px] h-[5px] rounded-full flex-shrink-0"
                      :style="{ background: selC_dk === i && i !== TODAY_INDEX ? 'rgba(28,28,30,0.6)' : dotColor(evt, D) }"
                    />
                  </div>
                  <div class="flex items-center gap-[3px]">
                    <span
                      v-for="(evt, di) in visibleDots(day.events).slice(2, 4)"
                      :key="di"
                      class="inline-block w-[5px] h-[5px] rounded-full flex-shrink-0"
                      :style="{ background: selC_dk === i && i !== TODAY_INDEX ? 'rgba(28,28,30,0.6)' : dotColor(evt, D) }"
                    />
                    <span
                      v-if="extraCount(day.events) > 0"
                      class="text-[8px] font-bold leading-none"
                      :style="{ color: selC_dk === i && i !== TODAY_INDEX ? D.inkInverse : D.inkTertiary }"
                    >+{{ extraCount(day.events) }}</span>
                  </div>
                </div>
              </button>
            </div>

            <p class="text-[11px]" :style="{ color: D.inkTertiary }">
              Dark: surface-raised cards (#1C1B19) give subtle lift from app bg. Active charcoal dots on lavender fill.
            </p>
          </div>

          <!-- LIGHT — 14-day two-week scrollable -->
          <div class="rounded-2xl border p-6 space-y-4"
               :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode · 14 days (scroll)</p>
            <div class="overflow-x-auto pb-2" style="scrollbar-width: none; -ms-overflow-style: none; scroll-snap-type: x mandatory;">
              <div class="flex gap-1.5" style="min-width: max-content;">
                <button
                  v-for="(day, i) in TWO_WEEKS"
                  :key="i"
                  class="week-strip-card flex flex-col items-center justify-start pt-2.5 pb-2 gap-1 rounded-xl flex-shrink-0 transition-all"
                  style="width: 44px; min-height: 70px; scroll-snap-align: start;"
                  :style="i === TODAY_INDEX
                    ? { background: L.accents.lavender.soft, border: `1.5px solid ${L.accents.lavender.bold}` }
                    : selC2_lt === i
                      ? { background: L.accents.lavender.bold, border: `1.5px solid ${L.accents.lavender.bold}` }
                      : { background: L.surfaceRaised, border: `1.5px solid ${L.borderSubtle}` }"
                  @click="selC2_lt = i"
                >
                  <span
                    class="text-[10px] font-semibold uppercase tracking-widest leading-none"
                    :style="selC2_lt === i && i !== TODAY_INDEX
                      ? { color: 'rgba(255,255,255,0.75)' }
                      : i === TODAY_INDEX
                        ? { color: L.accents.lavender.bold }
                        : { color: L.inkTertiary }"
                  >{{ day.letter.slice(0, 3) }}</span>
                  <span
                    class="text-[15px] font-semibold leading-none"
                    :style="selC2_lt === i && i !== TODAY_INDEX
                      ? { color: '#FFFFFF' }
                      : i === TODAY_INDEX
                        ? { color: L.accents.lavender.bold }
                        : { color: L.inkPrimary }"
                  >{{ day.num }}</span>
                  <div class="flex items-center gap-[3px]">
                    <span
                      v-for="(evt, di) in visibleDots(day.events).slice(0, 3)"
                      :key="di"
                      class="inline-block w-[4px] h-[4px] rounded-full flex-shrink-0"
                      :style="{ background: selC2_lt === i && i !== TODAY_INDEX ? 'rgba(255,255,255,0.7)' : dotColor(evt, L) }"
                    />
                  </div>
                </button>
              </div>
            </div>
          </div>

          <!-- DARK — 14-day two-week scrollable -->
          <div class="rounded-2xl border p-6 space-y-4"
               :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode · 14 days (scroll)</p>
            <div class="overflow-x-auto pb-2" style="scrollbar-width: none; -ms-overflow-style: none; scroll-snap-type: x mandatory;">
              <div class="flex gap-1.5" style="min-width: max-content;">
                <button
                  v-for="(day, i) in TWO_WEEKS"
                  :key="i"
                  class="week-strip-card flex flex-col items-center justify-start pt-2.5 pb-2 gap-1 rounded-xl flex-shrink-0 transition-all"
                  style="width: 44px; min-height: 70px; scroll-snap-align: start;"
                  :style="i === TODAY_INDEX
                    ? { background: D.accents.lavender.soft, border: `1.5px solid ${D.accents.lavender.bold}` }
                    : selC2_dk === i
                      ? { background: D.accents.lavender.bold, border: `1.5px solid ${D.accents.lavender.bold}` }
                      : { background: D.surfaceRaised, border: `1.5px solid ${D.borderSubtle}` }"
                  @click="selC2_dk = i"
                >
                  <span
                    class="text-[10px] font-semibold uppercase tracking-widest leading-none"
                    :style="selC2_dk === i && i !== TODAY_INDEX
                      ? { color: D.inkInverse, opacity: '0.75' }
                      : i === TODAY_INDEX
                        ? { color: D.accents.lavender.bold }
                        : { color: D.inkTertiary }"
                  >{{ day.letter.slice(0, 3) }}</span>
                  <span
                    class="text-[15px] font-semibold leading-none"
                    :style="selC2_dk === i && i !== TODAY_INDEX
                      ? { color: D.inkInverse }
                      : i === TODAY_INDEX
                        ? { color: D.accents.lavender.bold }
                        : { color: D.inkPrimary }"
                  >{{ day.num }}</span>
                  <div class="flex items-center gap-[3px]">
                    <span
                      v-for="(evt, di) in visibleDots(day.events).slice(0, 3)"
                      :key="di"
                      class="inline-block w-[4px] h-[4px] rounded-full flex-shrink-0"
                      :style="{ background: selC2_dk === i && i !== TODAY_INDEX ? 'rgba(28,28,30,0.6)' : dotColor(evt, D) }"
                    />
                  </div>
                </button>
              </div>
            </div>
          </div>

          <!-- Mobile 375px simulation -->
          <div class="space-y-3">
            <p class="text-[11px] font-semibold uppercase tracking-widest px-1" :style="{ color: L.inkTertiary }">
              Mobile (375px) — 7 days fill width
            </p>
            <div class="max-w-[375px] rounded-2xl border p-4"
                 :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle }">
              <div class="flex items-end justify-between gap-1">
                <button
                  v-for="(day, i) in WEEK"
                  :key="i"
                  class="week-strip-card flex flex-col items-center justify-start pt-2 pb-1.5 gap-0.5 rounded-xl flex-shrink-0 transition-all"
                  style="width: 42px; min-height: 66px;"
                  :style="i === TODAY_INDEX
                    ? { background: L.accents.lavender.soft, border: `1.5px solid ${L.accents.lavender.bold}` }
                    : selC_lt === i
                      ? { background: L.accents.lavender.bold, border: `1.5px solid ${L.accents.lavender.bold}` }
                      : { background: L.surfaceRaised, border: `1.5px solid ${L.borderSubtle}` }"
                  @click="selC_lt = i"
                >
                  <span
                    class="text-[9px] font-semibold uppercase tracking-widest leading-none"
                    :style="selC_lt === i && i !== TODAY_INDEX
                      ? { color: 'rgba(255,255,255,0.75)' }
                      : i === TODAY_INDEX
                        ? { color: L.accents.lavender.bold }
                        : { color: L.inkTertiary }"
                  >{{ day.letter.slice(0, 1) }}</span>
                  <span
                    class="text-[14px] font-semibold leading-none"
                    :style="selC_lt === i && i !== TODAY_INDEX
                      ? { color: '#FFFFFF' }
                      : i === TODAY_INDEX
                        ? { color: L.accents.lavender.bold }
                        : { color: L.inkPrimary }"
                  >{{ day.num }}</span>
                  <div class="flex items-center gap-[2px]">
                    <span
                      v-for="(evt, di) in visibleDots(day.events).slice(0, 2)"
                      :key="di"
                      class="inline-block w-[4px] h-[4px] rounded-full flex-shrink-0"
                      :style="{ background: selC_lt === i && i !== TODAY_INDEX ? 'rgba(255,255,255,0.7)' : dotColor(evt, L) }"
                    />
                  </div>
                </button>
              </div>
            </div>
            <p class="text-[11px] px-1" :style="{ color: L.inkTertiary }">
              Card variant at 375px: dots capped at 2, single letter. Still fits 7 days with gap-1.
            </p>
          </div>

        </div>
      </VariantFrame>
      <p class="mt-3 text-sm px-1" :style="{ color: L.inkSecondary }">
        Variant C packs the most information into the strip — category dots inside each card let users judge
        busyness at a glance without opening a day. The rounded-xl (not full-round) shape gives it a
        "card" rather than "pill" feel, which suits the event-density surfaces it's designed for.
      </p>
    </section>


    <!-- ══════════════════════════════════════════════════════════════
         OVERFLOW EVENT DOTS DEMO
         Focused demo: today (Wed) with 4 events, Fri with 5+ overflow.
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="Dots" caption="Event dot overflow — 4 dots max, 5th+ becomes a + indicator. Variant A dot strip shown.">
        <div class="w-full space-y-8">

          <p class="text-[13px]" :style="{ color: L.inkSecondary }">
            All variants cap visible dots at <strong :style="{ color: L.inkPrimary }">4</strong>. A 5th event or more
            shows a <code class="font-mono text-[12px] px-1 py-0.5 rounded" :style="{ background: L.surfaceSunken }">+N</code> suffix
            in ink-tertiary. Below: full week with manufactured overflow scenarios.
          </p>

          <!-- Overflow demo week — Variant A dot strip style -->
          <div class="rounded-2xl border p-6"
               :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest mb-4" :style="{ color: L.inkTertiary }">
              Light — dot overflow states
            </p>
            <div class="flex items-end justify-between gap-1 mb-4">
              <div
                v-for="(day, i) in WEEK"
                :key="i"
                class="flex flex-col items-center gap-1 min-w-0 flex-1 py-2"
              >
                <span class="text-[10px] font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">
                  {{ day.letter.slice(0, 1) }}
                </span>
                <span
                  class="inline-flex items-center justify-center w-8 h-8 rounded-full text-[15px] font-semibold"
                  :style="i === TODAY_INDEX
                    ? { background: L.accents.lavender.bold, color: '#FFFFFF' }
                    : { color: L.inkPrimary }"
                >{{ day.num }}</span>
                <div class="flex items-center gap-[3px] h-[6px]">
                  <span
                    v-for="(evt, di) in visibleDots(day.events)"
                    :key="di"
                    class="inline-block w-[5px] h-[5px] rounded-full flex-shrink-0"
                    :style="{ background: dotColor(evt, L) }"
                  />
                  <span
                    v-if="extraCount(day.events) > 0"
                    class="text-[9px] font-bold"
                    :style="{ color: L.inkTertiary }"
                  >+{{ extraCount(day.events) }}</span>
                </div>
              </div>
            </div>

            <!-- Annotation row -->
            <div class="flex justify-between gap-1">
              <div v-for="(day, i) in WEEK" :key="i" class="flex-1 text-center">
                <p class="text-[9px] leading-tight" :style="{ color: L.inkTertiary }">
                  {{ day.events.length }} event{{ day.events.length !== 1 ? 's' : '' }}
                </p>
              </div>
            </div>

            <p class="text-[11px] mt-4" :style="{ color: L.inkTertiary }">
              Mon / Sun = 0 events = no dots rendered (empty strip). Fri = 4 events = dots fill max. Any event
              beyond 4 collapses into the +N suffix. The suffix uses ink-tertiary so it doesn't compete with
              the colored dots.
            </p>
          </div>

          <!-- Dark overflow demo -->
          <div class="rounded-2xl border p-6"
               :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest mb-4" :style="{ color: D.inkTertiary }">
              Dark — dot overflow states
            </p>
            <div class="flex items-end justify-between gap-1 mb-4">
              <div
                v-for="(day, i) in WEEK"
                :key="i"
                class="flex flex-col items-center gap-1 min-w-0 flex-1 py-2"
              >
                <span class="text-[10px] font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">
                  {{ day.letter.slice(0, 1) }}
                </span>
                <span
                  class="inline-flex items-center justify-center w-8 h-8 rounded-full text-[15px] font-semibold"
                  :style="i === TODAY_INDEX
                    ? { background: D.accents.lavender.bold, color: D.inkInverse }
                    : { color: D.inkPrimary }"
                >{{ day.num }}</span>
                <div class="flex items-center gap-[3px] h-[6px]">
                  <span
                    v-for="(evt, di) in visibleDots(day.events)"
                    :key="di"
                    class="inline-block w-[5px] h-[5px] rounded-full flex-shrink-0"
                    :style="{ background: dotColor(evt, D) }"
                  />
                  <span
                    v-if="extraCount(day.events) > 0"
                    class="text-[9px] font-bold"
                    :style="{ color: D.inkTertiary }"
                  >+{{ extraCount(day.events) }}</span>
                </div>
              </div>
            </div>
          </div>

        </div>
      </VariantFrame>
    </section>


    <!-- ══════════════════════════════════════════════════════════════
         CLAUDE'S PICK CALLOUT
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-8">
      <div class="rounded-2xl border p-6 space-y-3"
           :style="{ background: L.accents.lavender.soft, borderColor: L.accents.lavender.bold }">
        <div class="flex items-center gap-2">
          <SparklesIcon class="w-5 h-5 flex-shrink-0" :style="{ color: L.accents.lavender.bold }" />
          <h2 class="text-[15px] font-semibold" :style="{ color: L.accents.lavender.bold }">Claude's pick — Variant B</h2>
        </div>
        <p class="text-[14px] leading-relaxed" :style="{ color: L.inkPrimary }">
          Variant B is the strongest match for Kinhold's brief because it speaks the app's confirmed pill shape
          language — the same rounded-full forms already used in buttons, category chips, and tab groups. The
          filled lavender-bold active state pulls from the brand accent without introducing any new shape
          vocabulary, meaning it slots into calendar week view, meal plan week picker, and leaderboard selectors
          with zero visual friction. Where event density matters (meal planner), pair it with Variant C instead.
          Variant A is reserved for content-focused contexts where the strip must stay invisible until needed.
        </p>
      </div>
    </section>


    <!-- ══════════════════════════════════════════════════════════════
         USAGE GUIDE
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-8">
      <div class="rounded-2xl border p-6 space-y-4"
           :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle }">
        <h2 class="text-[17px] font-semibold" :style="{ color: L.inkPrimary }">When to use which variant</h2>
        <ul class="space-y-3 text-[14px]" :style="{ color: L.inkSecondary }">
          <li>
            <strong :style="{ color: L.inkPrimary }">Variant A (compact column + external dot strip)</strong> —
            Use when the strip must be visually minimal and content lives below the strip (a full month grid,
            a task list). The dots sit outside the day shape so they never inflate height.
            Best for: calendar week header, task review day picker. On mobile cap dots at 3.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Variant B (pill-style — recommended default)</strong> —
            Use everywhere that event dots are not required. Clean pill shape aligns with Kinhold's broader
            button and chip vocabulary. The lavender-bold fill on selected is unmistakable and on-brand.
            Best for: calendar week view, leaderboard period picker, any date range selector.
            At 375px all 7 pills fit comfortably using single-letter day abbreviations.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Variant C (card-style pill with dots inside)</strong> —
            Use on event-density surfaces where knowing whether a day has events matters before tapping.
            The wider rounded-xl card affords a 2-row dot grid (up to 4 dots) inside the pill. Slower to scan
            than B but richer in information.
            Best for: meal plan week view (each day shows meal category dots), family calendar dense view.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">14-day / two-week mode</strong> —
            All variants support it via <code class="font-mono text-[12px] px-1 py-0.5 rounded" :style="{ background: L.surfaceSunken }">overflow-x-auto</code>
            + <code class="font-mono text-[12px] px-1 py-0.5 rounded" :style="{ background: L.surfaceSunken }">scroll-snap-type: x mandatory</code>.
            Fixed-width day slots (40–44px) prevent layout reflow when selection changes.
            Always hide the scrollbar (<code class="font-mono text-[12px] px-1 py-0.5 rounded" :style="{ background: L.surfaceSunken }">scrollbar-width: none</code>).
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Today vs Selected distinction</strong> —
            Today is a constant visual anchor — it never loses its filled/accent treatment even when another
            day is selected. Selected is an overlay state. If today happens to be selected, the today style
            takes precedence in all three variants.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Event dot colors</strong> —
            Map accent colors to categories: lavender = tasks/personal, mint = meal/food,
            peach = family/shared, sun = financial/appointments. Dots use the accent bold value so they
            remain legible on both light surface-raised and dark surface-raised backgrounds.
          </li>
        </ul>
      </div>
    </section>

  </ComponentPage>
</template>

<style scoped>
/* ──────────────────────────────────────────────────────────────
   Shared transition for all day slot buttons
   ──────────────────────────────────────────────────────────────*/
.week-strip-btn,
.week-strip-pill,
.week-strip-card {
  cursor: pointer;
  transition:
    background-color 140ms cubic-bezier(0.16, 1, 0.3, 1),
    border-color 140ms cubic-bezier(0.16, 1, 0.3, 1),
    box-shadow 140ms cubic-bezier(0.16, 1, 0.3, 1),
    color 140ms cubic-bezier(0.16, 1, 0.3, 1);
}

/* Hover lift for Variant B and C pills */
.week-strip-pill:hover,
.week-strip-card:hover {
  opacity: 0.85;
}

/* Hide webkit scrollbar on all overflow containers */
.overflow-x-auto::-webkit-scrollbar {
  display: none;
}

/* Reduced motion */
@media (prefers-reduced-motion: reduce) {
  .week-strip-btn,
  .week-strip-pill,
  .week-strip-card {
    transition: none;
  }
}
</style>
