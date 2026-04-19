<script setup>
import { ref, computed } from 'vue'
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import { SparklesIcon } from '@heroicons/vue/24/outline'

// ── Palettes ─────────────────────────────────────────────────────────────────
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
}

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
}

// ── Calendar data ─────────────────────────────────────────────────────────────
// March 2026 starts on Sunday (day index 0). 31 days.
// Leading days: Feb 22–28 (7 cells before March 1 if we go back one full week)
// But March starts on Sunday so 0 leading days are needed — first cell IS March 1.
// Trailing: April 1–11 to fill 6 rows × 7 = 42 cells total.
const TODAY    = 14  // March 14
const SELECTED = 18  // March 18

const EVENTS = {
  3:  ['lavender'],
  5:  ['peach', 'mint'],
  8:  ['lavender', 'mint', 'peach', 'sun'],
  12: ['sun'],
  14: ['lavender', 'peach'],
  18: ['mint', 'lavender', 'peach'],
  22: ['sun'],
  25: ['lavender', 'mint', 'sun', 'peach', 'lavender', 'mint'],
  28: ['peach'],
}

const DAY_HEADERS = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']

// Build a 42-cell array for a 6-row grid.
// March 2026: starts Sunday, 31 days → 0 leading + 11 trailing = 42
function buildMonthCells() {
  const cells = []
  // No leading days (March 1 is Sunday)
  for (let d = 1; d <= 31; d++) {
    cells.push({ day: d, month: 'current' })
  }
  // Trailing April days to fill to 42
  for (let d = 1; cells.length < 42; d++) {
    cells.push({ day: d, month: 'trailing' })
  }
  return cells
}

const CELLS = buildMonthCells()

// ── Accent color lookup ───────────────────────────────────────────────────────
function accentOf(name, palette, key) {
  return palette.accents[name] ? palette.accents[name][key] : palette.accents.lavender[key]
}
</script>

<template>
  <ComponentPage
    title="5.5 MonthGrid"
    description="7-column month calendar grid. Each cell shows the date number with event indicators. Default treatment shows up to 3 category-colored dots beneath the number; the mini-pills variant (opt-in via prop) shows up to 2 truncated event titles stacked for desktop / power-user contexts."
    status="chosen"
  >

    <!-- ══════════════════════════════════════════════════════════════
         VARIANT A — Dots beneath number
         iOS Calendar mobile pattern. Cleanest for dense months.
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-20">
      <VariantFrame
        label="Primary"
        caption="Dots beneath number — the default MonthGrid. Up to 3 category-colored dots per cell, +N overflow for denser days. Works at every width, mobile to desktop."
      >
        <div class="w-full space-y-10">

          <!-- LIGHT PANEL -->
          <div class="rounded-2xl border p-4 md:p-6" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-[10px] font-semibold uppercase tracking-widest mb-4" :style="{ color: L.inkTertiary }">Light mode — March 2026</p>

            <!-- Day header row -->
            <div class="grid grid-cols-7 gap-1 mb-1">
              <div
                v-for="h in DAY_HEADERS"
                :key="h"
                class="text-center text-[10px] font-semibold uppercase tracking-wider py-1"
                :style="{ color: L.inkTertiary }"
              >{{ h }}</div>
            </div>

            <!-- Calendar cells -->
            <div class="grid grid-cols-7 gap-1">
              <div
                v-for="(cell, idx) in CELLS"
                :key="idx"
                class="relative rounded-lg flex flex-col items-center pt-1.5 pb-1.5 min-h-[42px] md:min-h-[52px]"
                :style="{
                  background: cell.day === SELECTED && cell.month === 'current'
                    ? L.accents.lavender.soft
                    : L.surfaceRaised,
                  border: cell.day === SELECTED && cell.month === 'current'
                    ? '2px solid ' + L.accents.lavender.bold
                    : '1px solid ' + L.borderSubtle,
                  opacity: cell.month !== 'current' ? 0.4 : 1,
                }"
              >
                <!-- Date number / today circle -->
                <div
                  class="w-7 h-7 flex items-center justify-center rounded-full text-[13px] font-semibold leading-none flex-shrink-0"
                  :style="{
                    background: cell.day === TODAY && cell.month === 'current'
                      ? L.accents.lavender.bold
                      : 'transparent',
                    color: cell.day === TODAY && cell.month === 'current'
                      ? L.inkInverse
                      : L.inkPrimary,
                  }"
                >{{ cell.day }}</div>

                <!-- Dots row — only for current-month days with events -->
                <div
                  v-if="cell.month === 'current' && EVENTS[cell.day]"
                  class="flex items-center gap-[3px] mt-1 h-[6px]"
                >
                  <span
                    v-for="(color, ci) in EVENTS[cell.day].slice(0, 3)"
                    :key="ci"
                    class="rounded-full flex-shrink-0"
                    style="width: 5px; height: 5px;"
                    :style="{ background: accentOf(color, L, 'bold') }"
                  />
                  <span
                    v-if="EVENTS[cell.day].length > 3"
                    class="text-[8px] font-semibold leading-none"
                    :style="{ color: L.inkTertiary }"
                  >+{{ EVENTS[cell.day].length - 3 }}</span>
                </div>
              </div>
            </div>
          </div><!-- /light A -->

          <!-- DARK PANEL -->
          <div class="rounded-2xl border p-4 md:p-6" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-[10px] font-semibold uppercase tracking-widest mb-4" :style="{ color: D.inkTertiary }">Dark mode — March 2026</p>

            <!-- Day header row -->
            <div class="grid grid-cols-7 gap-1 mb-1">
              <div
                v-for="h in DAY_HEADERS"
                :key="h"
                class="text-center text-[10px] font-semibold uppercase tracking-wider py-1"
                :style="{ color: D.inkTertiary }"
              >{{ h }}</div>
            </div>

            <!-- Calendar cells -->
            <div class="grid grid-cols-7 gap-1">
              <div
                v-for="(cell, idx) in CELLS"
                :key="idx"
                class="relative rounded-lg flex flex-col items-center pt-1.5 pb-1.5 min-h-[42px] md:min-h-[52px]"
                :style="{
                  background: cell.day === SELECTED && cell.month === 'current'
                    ? D.accents.lavender.soft
                    : D.surfaceRaised,
                  border: cell.day === SELECTED && cell.month === 'current'
                    ? '2px solid ' + D.accents.lavender.bold
                    : '1px solid ' + D.borderSubtle,
                  opacity: cell.month !== 'current' ? 0.35 : 1,
                }"
              >
                <div
                  class="w-7 h-7 flex items-center justify-center rounded-full text-[13px] font-semibold leading-none flex-shrink-0"
                  :style="{
                    background: cell.day === TODAY && cell.month === 'current'
                      ? D.accents.lavender.bold
                      : 'transparent',
                    color: cell.day === TODAY && cell.month === 'current'
                      ? D.inkInverse
                      : D.inkPrimary,
                  }"
                >{{ cell.day }}</div>

                <div
                  v-if="cell.month === 'current' && EVENTS[cell.day]"
                  class="flex items-center gap-[3px] mt-1 h-[6px]"
                >
                  <span
                    v-for="(color, ci) in EVENTS[cell.day].slice(0, 3)"
                    :key="ci"
                    class="rounded-full flex-shrink-0"
                    style="width: 5px; height: 5px;"
                    :style="{ background: accentOf(color, D, 'bold') }"
                  />
                  <span
                    v-if="EVENTS[cell.day].length > 3"
                    class="text-[8px] font-semibold leading-none"
                    :style="{ color: D.inkTertiary }"
                  >+{{ EVENTS[cell.day].length - 3 }}</span>
                </div>
              </div>
            </div>
          </div><!-- /dark A -->

        </div>
      </VariantFrame>
    </section>


    <!-- ══════════════════════════════════════════════════════════════
         VARIANT B — Mini event pills inline
         Google Calendar compact pattern. Informative for desktop.
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-20">
      <VariantFrame
        label="Pills"
        caption="Mini event pills (prop variant) — up to 2 soft-tinted pills with truncated titles beneath the date number, overflow becomes '+N more'. Opt-in via prop for power-user / planner desktop contexts where identifying events by name at a glance matters."
      >
        <div class="w-full space-y-10">

          <!-- LIGHT PANEL -->
          <div class="rounded-2xl border p-4 md:p-6" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-[10px] font-semibold uppercase tracking-widest mb-4" :style="{ color: L.inkTertiary }">Light mode — March 2026</p>

            <div class="grid grid-cols-7 gap-1 mb-1">
              <div
                v-for="h in DAY_HEADERS"
                :key="h"
                class="text-center text-[10px] font-semibold uppercase tracking-wider py-1"
                :style="{ color: L.inkTertiary }"
              >{{ h }}</div>
            </div>

            <div class="grid grid-cols-7 gap-1">
              <div
                v-for="(cell, idx) in CELLS"
                :key="idx"
                class="relative rounded-lg flex flex-col pt-1.5 px-1 pb-1.5 min-h-[56px] md:min-h-[72px]"
                :style="{
                  background: cell.day === SELECTED && cell.month === 'current'
                    ? L.accents.lavender.soft
                    : L.surfaceRaised,
                  border: cell.day === SELECTED && cell.month === 'current'
                    ? '2px solid ' + L.accents.lavender.bold
                    : '1px solid ' + L.borderSubtle,
                  opacity: cell.month !== 'current' ? 0.4 : 1,
                }"
              >
                <!-- Date number -->
                <div class="flex items-center justify-center mb-1">
                  <div
                    class="w-6 h-6 md:w-7 md:h-7 flex items-center justify-center rounded-full text-[11px] md:text-[13px] font-semibold leading-none"
                    :style="{
                      background: cell.day === TODAY && cell.month === 'current'
                        ? L.accents.lavender.bold
                        : 'transparent',
                      color: cell.day === TODAY && cell.month === 'current'
                        ? L.inkInverse
                        : L.inkPrimary,
                    }"
                  >{{ cell.day }}</div>
                </div>

                <!-- Pills (max 2) -->
                <template v-if="cell.month === 'current' && EVENTS[cell.day]">
                  <div
                    v-for="(color, ci) in EVENTS[cell.day].slice(0, 2)"
                    :key="ci"
                    class="rounded-[3px] px-1 mb-[2px] text-[9px] md:text-[10px] font-semibold truncate leading-[14px]"
                    :style="{
                      background: accentOf(color, L, 'soft'),
                      color: accentOf(color, L, 'bold'),
                    }"
                  >{{ color }}</div>
                  <div
                    v-if="EVENTS[cell.day].length > 2"
                    class="text-[9px] md:text-[10px] font-medium leading-[14px] px-1"
                    :style="{ color: L.inkTertiary }"
                  >+{{ EVENTS[cell.day].length - 2 }} more</div>
                </template>
              </div>
            </div>
          </div><!-- /light B -->

          <!-- DARK PANEL -->
          <div class="rounded-2xl border p-4 md:p-6" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-[10px] font-semibold uppercase tracking-widest mb-4" :style="{ color: D.inkTertiary }">Dark mode — March 2026</p>

            <div class="grid grid-cols-7 gap-1 mb-1">
              <div
                v-for="h in DAY_HEADERS"
                :key="h"
                class="text-center text-[10px] font-semibold uppercase tracking-wider py-1"
                :style="{ color: D.inkTertiary }"
              >{{ h }}</div>
            </div>

            <div class="grid grid-cols-7 gap-1">
              <div
                v-for="(cell, idx) in CELLS"
                :key="idx"
                class="relative rounded-lg flex flex-col pt-1.5 px-1 pb-1.5 min-h-[56px] md:min-h-[72px]"
                :style="{
                  background: cell.day === SELECTED && cell.month === 'current'
                    ? D.accents.lavender.soft
                    : D.surfaceRaised,
                  border: cell.day === SELECTED && cell.month === 'current'
                    ? '2px solid ' + D.accents.lavender.bold
                    : '1px solid ' + D.borderSubtle,
                  opacity: cell.month !== 'current' ? 0.35 : 1,
                }"
              >
                <div class="flex items-center justify-center mb-1">
                  <div
                    class="w-6 h-6 md:w-7 md:h-7 flex items-center justify-center rounded-full text-[11px] md:text-[13px] font-semibold leading-none"
                    :style="{
                      background: cell.day === TODAY && cell.month === 'current'
                        ? D.accents.lavender.bold
                        : 'transparent',
                      color: cell.day === TODAY && cell.month === 'current'
                        ? D.inkInverse
                        : D.inkPrimary,
                    }"
                  >{{ cell.day }}</div>
                </div>

                <template v-if="cell.month === 'current' && EVENTS[cell.day]">
                  <div
                    v-for="(color, ci) in EVENTS[cell.day].slice(0, 2)"
                    :key="ci"
                    class="rounded-[3px] px-1 mb-[2px] text-[9px] md:text-[10px] font-semibold truncate leading-[14px]"
                    :style="{
                      background: accentOf(color, D, 'soft'),
                      color: accentOf(color, D, 'bold'),
                    }"
                  >{{ color }}</div>
                  <div
                    v-if="EVENTS[cell.day].length > 2"
                    class="text-[9px] md:text-[10px] font-medium leading-[14px] px-1"
                    :style="{ color: D.inkTertiary }"
                  >+{{ EVENTS[cell.day].length - 2 }} more</div>
                </template>
              </div>
            </div>
          </div><!-- /dark B -->

        </div>
      </VariantFrame>
    </section>


    <!-- ══════════════════════════════════════════════════════════════
         CLAUDE'S PICK CALLOUT
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-20">
      <div
        class="rounded-2xl border p-6 flex gap-4"
        :style="{
          background: L.accents.lavender.soft,
          borderColor: L.accents.lavender.bold + '44',
        }"
      >
        <SparklesIcon class="w-5 h-5 flex-shrink-0 mt-0.5" :style="{ color: L.accents.lavender.bold }" />
        <div>
          <p class="text-[14px] font-semibold mb-1" :style="{ color: L.inkPrimary }">
            LOCKED — Dots primary, Pills as prop variant
          </p>
          <p class="text-[13px] leading-relaxed" :style="{ color: L.inkSecondary }">
            <strong>Dots (primary)</strong> is the default for every MonthGrid in the app. Cells stay compact at 375px, dot colors map to the category accent system, and the "+N" overflow resolves busy days gracefully at every screen size.
          </p>
          <p class="text-[13px] leading-relaxed mt-2" :style="{ color: L.inkSecondary }">
            <strong>Pills (prop variant)</strong> — likely <code class="text-[12px] font-mono bg-white/60 px-1 rounded">density="pills"</code> — swaps the dot row for up to 2 truncated event-title pills. Reserved for desktop / planner contexts where identifying events by name at a glance matters. Same component, more vertical cell height.
          </p>
        </div>
      </div>
    </section>


    <!-- ══════════════════════════════════════════════════════════════
         USAGE GUIDE
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-8">
      <div class="rounded-2xl border p-6 space-y-4" :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle }">
        <h2 class="text-[17px] font-semibold" :style="{ color: L.inkPrimary }">When and how to use MonthGrid</h2>
        <ul class="space-y-3 text-[14px]" :style="{ color: L.inkSecondary }">
          <li>
            <strong :style="{ color: L.inkPrimary }">Primary (Dots) — the default.</strong>
            Use on the primary family calendar month view, especially on mobile. Cells stay compact (42–44px minimum), dot colors map to the category accent system, and readability is preserved even with 6+ events. Safest choice whenever cell height is constrained.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Pills (prop variant).</strong>
            Pass <code class="font-mono text-[12px] px-1 py-0.5 rounded" :style="{ background: L.surfaceSunken }">density="pills"</code> when the grid has vertical room (56px+ cells) and users benefit from seeing event titles directly — desktop planner view, weekly summary panels, recipe schedules. Limit to 2 pills per cell; overflow becomes "+N more".
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Today + Selected states</strong> apply in both modes: today gets a solid lavender-bold filled circle (36px / 28px), selected cell gets a 2px lavender-bold ring. Leading/trailing month days are dimmed to 35–40% opacity.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Mobile floor:</strong>
            Dots variant min-height 42px, Pills variant 56px (needs pill room). Never go below 40px or tap targets become unreliable on iOS.
          </li>
        </ul>
      </div>
    </section>

  </ComponentPage>
</template>
