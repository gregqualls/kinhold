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

// ── Selected-day state per mode ──────────────────────────────────────────────
const selB_lt  = ref(3)
const selB_dk  = ref(3)
const selB2_lt = ref(3)
const selB2_dk = ref(3)

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
    description="Horizontal day-of-week selector. Single-letter day labels + date number inside a rounded-full pill, with event dots underneath for scannability. Today is always anchored in lavender-soft; selected day fills lavender-bold; past days dim to 45% with cursor-not-allowed to signal they're unreachable."
    status="chosen"
  >

    <!-- ══════════════════════════════════════════════════════════════
         LOCKED — Pill strip with dot-strip + past-date deactivation
         Single-letter day label + date number inside a rounded-full
         pill. Colored event-dot row sits BELOW the pill for at-a-
         glance scannability. Past days render at 45% opacity with
         cursor-not-allowed. Today is anchored in lavender-soft;
         selected day fills lavender-bold with inverse text.
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="WeekStrip" caption="Pill strip with dot row beneath + past-date deactivation — the default week selector everywhere in the app.">
        <div class="w-full space-y-10">

          <!-- LIGHT — 7-day -->
          <div class="rounded-2xl border p-6 space-y-6"
               :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode · 7 days</p>

            <div class="flex items-start justify-between gap-1.5">
              <div v-for="(day, i) in WEEK" :key="i" class="flex flex-col items-center gap-1.5 flex-shrink-0">
                <button
                  class="week-strip-pill flex flex-col items-center justify-center gap-0.5 rounded-full transition-all"
                  style="width: 40px; height: 56px;"
                  :style="{
                    ...(i === TODAY_INDEX
                      ? { background: L.accents.lavender.soft, border: `1.5px solid ${L.accents.lavender.bold}` }
                      : selB_lt === i
                        ? { background: L.accents.lavender.bold, border: `1.5px solid ${L.accents.lavender.bold}` }
                        : { background: 'transparent', border: `1.5px solid ${L.borderSubtle}` }),
                    opacity: i < TODAY_INDEX ? 0.45 : 1,
                    cursor: i < TODAY_INDEX ? 'not-allowed' : 'pointer',
                  }"
                  :disabled="i < TODAY_INDEX"
                  @click="i >= TODAY_INDEX && (selB_lt = i)"
                >
                  <span
                    class="text-[10px] font-semibold uppercase tracking-widest leading-none"
                    :style="selB_lt === i && i !== TODAY_INDEX
                      ? { color: '#FFFFFF' }
                      : i === TODAY_INDEX
                        ? { color: L.accents.lavender.bold }
                        : { color: L.inkTertiary }"
                  >{{ day.letter.slice(0, 1) }}</span>
                  <span
                    class="text-[16px] font-semibold leading-none"
                    :style="selB_lt === i && i !== TODAY_INDEX
                      ? { color: '#FFFFFF' }
                      : i === TODAY_INDEX
                        ? { color: L.accents.lavender.bold }
                        : { color: L.inkPrimary }"
                  >{{ day.num }}</span>
                </button>
                <div class="flex items-center justify-center gap-[3px] h-[6px]" :style="{ opacity: i < TODAY_INDEX ? 0.45 : 1 }">
                  <span v-for="(evt, di) in visibleDots(day.events)" :key="di"
                    class="inline-block w-[5px] h-[5px] rounded-full flex-shrink-0"
                    :style="{ background: dotColor(evt, L) }" />
                  <span v-if="extraCount(day.events) > 0"
                    class="text-[9px] font-bold leading-none"
                    :style="{ color: L.inkTertiary }">+{{ extraCount(day.events) }}</span>
                </div>
              </div>
            </div>

            <p class="text-[11px]" :style="{ color: L.inkTertiary }">
              Past days (Mon · Tue) render at 45% with cursor-not-allowed — clearly unreachable.
              Today (Wed) anchors in lavender-soft; selected (Thu) fills lavender-bold.
              Dots beneath each pill show events at a glance.
            </p>
          </div>

          <!-- DARK — 7-day -->
          <div class="rounded-2xl border p-6 space-y-6"
               :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode · 7 days</p>

            <div class="flex items-start justify-between gap-1.5">
              <div v-for="(day, i) in WEEK" :key="i" class="flex flex-col items-center gap-1.5 flex-shrink-0">
                <button
                  class="week-strip-pill flex flex-col items-center justify-center gap-0.5 rounded-full transition-all"
                  style="width: 40px; height: 56px;"
                  :style="{
                    ...(i === TODAY_INDEX
                      ? { background: D.accents.lavender.soft, border: `1.5px solid ${D.accents.lavender.bold}` }
                      : selB_dk === i
                        ? { background: D.accents.lavender.bold, border: `1.5px solid ${D.accents.lavender.bold}` }
                        : { background: 'transparent', border: `1.5px solid ${D.borderSubtle}` }),
                    opacity: i < TODAY_INDEX ? 0.45 : 1,
                    cursor: i < TODAY_INDEX ? 'not-allowed' : 'pointer',
                  }"
                  :disabled="i < TODAY_INDEX"
                  @click="i >= TODAY_INDEX && (selB_dk = i)"
                >
                  <span
                    class="text-[10px] font-semibold uppercase tracking-widest leading-none"
                    :style="selB_dk === i && i !== TODAY_INDEX
                      ? { color: D.inkInverse }
                      : i === TODAY_INDEX
                        ? { color: D.accents.lavender.bold }
                        : { color: D.inkTertiary }"
                  >{{ day.letter.slice(0, 1) }}</span>
                  <span
                    class="text-[16px] font-semibold leading-none"
                    :style="selB_dk === i && i !== TODAY_INDEX
                      ? { color: D.inkInverse }
                      : i === TODAY_INDEX
                        ? { color: D.accents.lavender.bold }
                        : { color: D.inkPrimary }"
                  >{{ day.num }}</span>
                </button>
                <div class="flex items-center justify-center gap-[3px] h-[6px]" :style="{ opacity: i < TODAY_INDEX ? 0.45 : 1 }">
                  <span v-for="(evt, di) in visibleDots(day.events)" :key="di"
                    class="inline-block w-[5px] h-[5px] rounded-full flex-shrink-0"
                    :style="{ background: dotColor(evt, D) }" />
                  <span v-if="extraCount(day.events) > 0"
                    class="text-[9px] font-bold leading-none"
                    :style="{ color: D.inkTertiary }">+{{ extraCount(day.events) }}</span>
                </div>
              </div>
            </div>

            <p class="text-[11px]" :style="{ color: D.inkTertiary }">
              Same rules, darker palette. Past days at 45% read as deactivated against the deeper ink.
            </p>
          </div>

          <!-- LIGHT — 14-day two-week scrollable -->
          <div class="rounded-2xl border p-6 space-y-4"
               :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode · 14 days (scroll)</p>
            <div class="overflow-x-auto pb-2" style="scrollbar-width: none; -ms-overflow-style: none; scroll-snap-type: x mandatory;">
              <div class="flex gap-1.5" style="min-width: max-content;">
                <div v-for="(day, i) in TWO_WEEKS" :key="i" class="flex flex-col items-center gap-1.5 flex-shrink-0" style="scroll-snap-align: start;">
                  <button
                    class="week-strip-pill flex flex-col items-center justify-center gap-0.5 rounded-full transition-all"
                    style="width: 40px; height: 56px;"
                    :style="{
                      ...(i === TODAY_INDEX
                        ? { background: L.accents.lavender.soft, border: `1.5px solid ${L.accents.lavender.bold}` }
                        : selB2_lt === i
                          ? { background: L.accents.lavender.bold, border: `1.5px solid ${L.accents.lavender.bold}` }
                          : { background: 'transparent', border: `1.5px solid ${L.borderSubtle}` }),
                      opacity: i < TODAY_INDEX ? 0.45 : 1,
                      cursor: i < TODAY_INDEX ? 'not-allowed' : 'pointer',
                    }"
                    :disabled="i < TODAY_INDEX"
                    @click="i >= TODAY_INDEX && (selB2_lt = i)"
                  >
                    <span
                      class="text-[10px] font-semibold uppercase tracking-widest leading-none"
                      :style="selB2_lt === i && i !== TODAY_INDEX
                        ? { color: '#FFFFFF' }
                        : i === TODAY_INDEX
                          ? { color: L.accents.lavender.bold }
                          : { color: L.inkTertiary }"
                    >{{ day.letter.slice(0, 1) }}</span>
                    <span
                      class="text-[16px] font-semibold leading-none"
                      :style="selB2_lt === i && i !== TODAY_INDEX
                        ? { color: '#FFFFFF' }
                        : i === TODAY_INDEX
                          ? { color: L.accents.lavender.bold }
                          : { color: L.inkPrimary }"
                    >{{ day.num }}</span>
                  </button>
                  <div class="flex items-center justify-center gap-[3px] h-[6px]" :style="{ opacity: i < TODAY_INDEX ? 0.45 : 1 }">
                    <span v-for="(evt, di) in visibleDots(day.events)" :key="di"
                      class="inline-block w-[5px] h-[5px] rounded-full flex-shrink-0"
                      :style="{ background: dotColor(evt, L) }" />
                    <span v-if="extraCount(day.events) > 0"
                      class="text-[9px] font-bold leading-none"
                      :style="{ color: L.inkTertiary }">+{{ extraCount(day.events) }}</span>
                  </div>
                </div>
              </div>
            </div>
            <p class="text-[11px]" :style="{ color: L.inkTertiary }">
              14-day variant scrolls horizontally with snap. Past days stay dimmed in the scroll area.
            </p>
          </div>

          <!-- DARK — 14-day two-week scrollable -->
          <div class="rounded-2xl border p-6 space-y-4"
               :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode · 14 days (scroll)</p>
            <div class="overflow-x-auto pb-2" style="scrollbar-width: none; -ms-overflow-style: none; scroll-snap-type: x mandatory;">
              <div class="flex gap-1.5" style="min-width: max-content;">
                <div v-for="(day, i) in TWO_WEEKS" :key="i" class="flex flex-col items-center gap-1.5 flex-shrink-0" style="scroll-snap-align: start;">
                  <button
                    class="week-strip-pill flex flex-col items-center justify-center gap-0.5 rounded-full transition-all"
                    style="width: 40px; height: 56px;"
                    :style="{
                      ...(i === TODAY_INDEX
                        ? { background: D.accents.lavender.soft, border: `1.5px solid ${D.accents.lavender.bold}` }
                        : selB2_dk === i
                          ? { background: D.accents.lavender.bold, border: `1.5px solid ${D.accents.lavender.bold}` }
                          : { background: 'transparent', border: `1.5px solid ${D.borderSubtle}` }),
                      opacity: i < TODAY_INDEX ? 0.45 : 1,
                      cursor: i < TODAY_INDEX ? 'not-allowed' : 'pointer',
                    }"
                    :disabled="i < TODAY_INDEX"
                    @click="i >= TODAY_INDEX && (selB2_dk = i)"
                  >
                    <span
                      class="text-[10px] font-semibold uppercase tracking-widest leading-none"
                      :style="selB2_dk === i && i !== TODAY_INDEX
                        ? { color: D.inkInverse }
                        : i === TODAY_INDEX
                          ? { color: D.accents.lavender.bold }
                          : { color: D.inkTertiary }"
                    >{{ day.letter.slice(0, 1) }}</span>
                    <span
                      class="text-[16px] font-semibold leading-none"
                      :style="selB2_dk === i && i !== TODAY_INDEX
                        ? { color: D.inkInverse }
                        : i === TODAY_INDEX
                          ? { color: D.accents.lavender.bold }
                          : { color: D.inkPrimary }"
                    >{{ day.num }}</span>
                  </button>
                  <div class="flex items-center justify-center gap-[3px] h-[6px]" :style="{ opacity: i < TODAY_INDEX ? 0.45 : 1 }">
                    <span v-for="(evt, di) in visibleDots(day.events)" :key="di"
                      class="inline-block w-[5px] h-[5px] rounded-full flex-shrink-0"
                      :style="{ background: dotColor(evt, D) }" />
                    <span v-if="extraCount(day.events) > 0"
                      class="text-[9px] font-bold leading-none"
                      :style="{ color: D.inkTertiary }">+{{ extraCount(day.events) }}</span>
                  </div>
                </div>
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
              <div class="flex items-start justify-between gap-1">
                <div v-for="(day, i) in WEEK" :key="i" class="flex flex-col items-center gap-1.5 flex-shrink-0">
                  <button
                    class="week-strip-pill flex flex-col items-center justify-center gap-0.5 rounded-full transition-all"
                    style="width: 38px; height: 52px;"
                    :style="{
                      ...(i === TODAY_INDEX
                        ? { background: L.accents.lavender.soft, border: `1.5px solid ${L.accents.lavender.bold}` }
                        : selB_lt === i
                          ? { background: L.accents.lavender.bold, border: `1.5px solid ${L.accents.lavender.bold}` }
                          : { background: 'transparent', border: `1.5px solid ${L.borderSubtle}` }),
                      opacity: i < TODAY_INDEX ? 0.45 : 1,
                      cursor: i < TODAY_INDEX ? 'not-allowed' : 'pointer',
                    }"
                    :disabled="i < TODAY_INDEX"
                    @click="i >= TODAY_INDEX && (selB_lt = i)"
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
                  <div class="flex items-center justify-center gap-[3px] h-[6px]" :style="{ opacity: i < TODAY_INDEX ? 0.45 : 1 }">
                    <span v-for="(evt, di) in visibleDots(day.events)" :key="di"
                      class="inline-block w-[5px] h-[5px] rounded-full flex-shrink-0"
                      :style="{ background: dotColor(evt, L) }" />
                    <span v-if="extraCount(day.events) > 0"
                      class="text-[9px] font-bold leading-none"
                      :style="{ color: L.inkTertiary }">+{{ extraCount(day.events) }}</span>
                  </div>
                </div>
              </div>
            </div>
            <p class="text-[11px] px-1" :style="{ color: L.inkTertiary }">
              At 375px all 7 pills + dot strips sit cleanly. Single letter everywhere — M T W T F S S.
            </p>
          </div>

        </div>
      </VariantFrame>
    </section>

    <!-- ══════════════════════════════════════════════════════════════
         OVERFLOW EVENT DOTS DEMO
         Focused demo: today (Wed) with 4 events, Fri with 5+ overflow.
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="Dots" caption="Event-dot overflow rule — up to 4 colored dots, 5+ collapses to a +N indicator. Same dot treatment used beneath every WeekStrip pill.">
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
          <h2 class="text-[15px] font-semibold" :style="{ color: L.accents.lavender.bold }">LOCKED — WeekStrip (pill + dots + past-date dimming)</h2>
        </div>
        <p class="text-[14px] leading-relaxed" :style="{ color: L.inkPrimary }">
          One WeekStrip, one behavior. Each day is a rounded-full pill (single-letter label + date number), with a colored event-dot row sitting beneath for at-a-glance scannability. Today is always anchored in lavender-soft; selected day fills lavender-bold with inverse text. Past days render at 45% opacity with <code class="text-[12px] font-mono bg-white/60 px-1 rounded">cursor: not-allowed</code> so they read clearly as unreachable.
        </p>
        <p class="text-[14px] leading-relaxed" :style="{ color: L.inkPrimary }">
          Used everywhere a week selector appears: calendar week view, meal plan, task-review day picker, leaderboard period. The 14-day scrollable variant reuses the same pill + dot treatment — past-day dimming carries into scroll area uniformly.
        </p>
      </div>
    </section>


    <!-- ══════════════════════════════════════════════════════════════
         USAGE GUIDE
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-8">
      <div class="rounded-2xl border p-6 space-y-4"
           :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle }">
        <h2 class="text-[17px] font-semibold" :style="{ color: L.inkPrimary }">When and how to use WeekStrip</h2>
        <ul class="space-y-3 text-[14px]" :style="{ color: L.inkSecondary }">
          <li>
            <strong :style="{ color: L.inkPrimary }">Default everywhere a week selector appears</strong> —
            calendar week view, meal plan, task-review day picker, leaderboard period selector.
            At 375px all 7 pills + dots fit comfortably using single-letter labels (M T W T F S S).
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Past-date deactivation</strong> —
            Days before today render at <code class="font-mono text-[12px] px-1 py-0.5 rounded" :style="{ background: L.surfaceSunken }">opacity: 0.45</code> with
            <code class="font-mono text-[12px] px-1 py-0.5 rounded" :style="{ background: L.surfaceSunken }">cursor: not-allowed</code> and the <code class="font-mono text-[12px] px-1 py-0.5 rounded" :style="{ background: L.surfaceSunken }">disabled</code> attribute.
            Dots beneath past-day pills also dim to match, so the whole column reads as unreachable.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Today is always anchored</strong> —
            lavender-soft fill + lavender-bold text. Today never loses its visual anchor even when another day is selected.
            If today happens to be selected, today's treatment takes precedence.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Event dots (under each pill)</strong> —
            Up to 4 colored dots, +N overflow indicator for 5 or more. Colors come from the category accent system
            (lavender / peach / mint / sun). Dots are optional — surfaces that don't care about density can
            omit the dot row to reduce visual weight.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">14-day / two-week mode</strong> —
            Drop into <code class="font-mono text-[12px] px-1 py-0.5 rounded" :style="{ background: L.surfaceSunken }">overflow-x-auto</code>
            + <code class="font-mono text-[12px] px-1 py-0.5 rounded" :style="{ background: L.surfaceSunken }">scroll-snap-type: x mandatory</code>.
            Fixed 40px day slots prevent reflow on selection change. Hide the scrollbar for native-app feel
            (<code class="font-mono text-[12px] px-1 py-0.5 rounded" :style="{ background: L.surfaceSunken }">scrollbar-width: none</code>).
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
