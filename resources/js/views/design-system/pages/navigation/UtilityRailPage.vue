<script setup>
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import {
  BookmarkIcon,
  ArrowPathIcon,
  ArrowTopRightOnSquareIcon,
  ChevronLeftIcon,
  ChevronRightIcon,
} from '@heroicons/vue/24/outline'

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
  // Accents
  lavenderSoft:  '#2D2840',
  lavenderBold:  '#B6A8E6',
  lavenderBorder:'#4A4272',
  peachSoft:     '#3A2820',
  peachBold:     '#E8A87C',
  mintSoft:      '#1A2E26',
  mintBold:      '#6FC49A',
  sunSoft:       '#302A10',
  sunBold:       '#D4B84A',
  // Chip on/off
  chipOnBg:      '#2D2840',
  chipOnText:    '#B6A8E6',
  chipOnBorder:  '#4A4272',
  chipOffBorder: '#2C2A27',
  chipOffText:   '#6E6B67',
  // Status dots
  dotOnline:     '#4D8C6A',
  dotOffline:    '#403E3A',
  dotBorder:     '#1C1B19',
  // Saved views hover bg
  savedHover:    '#2C2A27',
  // Action buttons
  actionHover:   '#2C2A27',
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
  borderStrong:  '#BCB8B2',
  // Accents
  lavenderSoft:  '#EAE6F8',
  lavenderBold:  '#6856B2',
  lavenderBorder:'#C0B4E8',
  peachSoft:     '#FAEEE5',
  peachBold:     '#C4713A',
  mintSoft:      '#E2F4EC',
  mintBold:      '#2E7D55',
  sunSoft:       '#FDF4D8',
  sunBold:       '#A07A10',
  // Chip on/off
  chipOnBg:      'rgba(234, 230, 248, 0.5)',
  chipOnText:    '#6856B2',
  chipOnBorder:  '#6856B2',
  chipOffBorder: '#E8E4DF',
  chipOffText:   '#6B6966',
  // Status dots
  dotOnline:     '#4D8C6A',
  dotOffline:    '#BCB8B2',
  dotBorder:     '#FFFFFF',
  // Saved views hover bg
  savedHover:    '#F5F2EE',
  // Action buttons
  actionHover:   '#F5F2EE',
}

// ── April 2026 mini-month data ───────────────────────────────────────────────
// April 2026: starts on Wednesday (0=Sun … 6=Sat, Wed=3)
const MONTH_LABEL = 'April 2026'
const DAYS_OF_WEEK = ['S', 'M', 'T', 'W', 'T', 'F', 'S']
const APRIL_FIRST_DOW = 3 // Wednesday
const APRIL_DAYS = 30
const TODAY_DAY = 18
const EVENT_DAYS = new Set([3, 8, 14, 17, 22, 28])

// Build a flat array of cells: null = padding, number = day
function buildMonthCells() {
  const cells = []
  for (let i = 0; i < APRIL_FIRST_DOW; i++) cells.push(null)
  for (let d = 1; d <= APRIL_DAYS; d++) cells.push(d)
  // Pad to complete last row
  while (cells.length % 7 !== 0) cells.push(null)
  return cells
}
const MONTH_CELLS = buildMonthCells()

// ── Sample family members ─────────────────────────────────────────────────────
const FAMILY = [
  { initials: 'GQ', name: 'Greg Qualls',  status: 'Online',        online: true,  bg: L.lavenderSoft, text: L.lavenderBold, bgD: D.lavenderSoft, textD: D.lavenderBold },
  { initials: 'MQ', name: 'Maya Qualls',  status: 'Online',        online: true,  bg: L.peachSoft,    text: L.peachBold,    bgD: D.peachSoft,    textD: D.peachBold    },
  { initials: 'EQ', name: 'Emma Qualls',  status: 'Offline · 1h ago', online: false, bg: L.mintSoft,  text: L.mintBold,     bgD: D.mintSoft,     textD: D.mintBold     },
  { initials: 'AQ', name: 'Ava Qualls',   status: 'Offline · 3h ago', online: false, bg: L.sunSoft,   text: L.sunBold,      bgD: D.sunSoft,       textD: D.sunBold      },
  { initials: 'WQ', name: 'Wife Qualls',  status: 'Online',        online: true,  bg: L.lavenderSoft, text: L.lavenderBold, bgD: D.lavenderSoft, textD: D.lavenderBold },
]

// ── Saved views ───────────────────────────────────────────────────────────────
const SAVED_VIEWS = [
  "Just Greg's tasks",
  "This week's events",
  "Kids' homework",
  "Overdue only",
]

// ── Filters ───────────────────────────────────────────────────────────────────
const FILTERS = [
  { label: 'Mine',      on: true  },
  { label: 'Family',    on: false },
  { label: 'Tasks',     on: false },
  { label: 'Busy-only', on: false },
]
</script>

<template>
  <ComponentPage
    title="3.4 Right utility rail"
    description="Right-side rail for data-heavy desktop pages (Calendar, Tasks, Vault, Food). Contains: mini-month, quick filters, presence list, saved views, and secondary actions. Collapses to a slide-up bottom sheet on mobile. Single locked treatment."
    status="chosen"
  >

    <!-- ══════════════════════════════════════════════════════════════
         SECTION 1 — FULL RAIL DEMO
         280px-wide rail beside a sample "main content" area.
         Includes all 5 content sections.
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-20">
      <VariantFrame
        label="Utility rail"
        caption="Right-side rail for data-heavy pages — mini-month, filters, presence, saved views, actions"
      >
        <div class="w-full space-y-10">

          <!-- LIGHT PANEL -->
          <div class="rounded-2xl border p-6 space-y-4" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>
            <p class="text-[12px]" :style="{ color: L.inkTertiary }">Desktop layout — main content + 280px right rail</p>

            <!-- Layout container -->
            <div class="flex rounded-2xl border overflow-hidden" style="min-height: 520px" :style="{ borderColor: L.borderSubtle }">

              <!-- Main content area -->
              <div class="flex-1 p-6" :style="{ background: L.surfaceRaised }">
                <p class="text-[11px] font-semibold uppercase tracking-widest mb-4" :style="{ color: L.inkTertiary }">Calendar</p>
                <p class="text-[22px] font-semibold" :style="{ color: L.inkPrimary }">April 2026</p>
                <p class="text-[13px] mt-1" :style="{ color: L.inkSecondary }">14 events this month · 3 today</p>
                <!-- Dummy content rows -->
                <div class="mt-6 space-y-2">
                  <div v-for="i in 5" :key="i" class="h-10 rounded-xl flex items-center px-3 gap-3"
                    :style="{ background: L.surfaceSunken }">
                    <div class="w-2.5 h-2.5 rounded-full flex-shrink-0" :style="{ background: L.lavenderBold, opacity: 0.6 + i * 0.05 }" />
                    <div class="h-2.5 rounded-full flex-1" :style="{ background: L.borderSubtle, maxWidth: (50 + i * 8) + '%' }" />
                  </div>
                </div>
              </div>

              <!-- Rail — 280px -->
              <div class="flex-shrink-0 flex flex-col border-l p-4 space-y-5"
                style="width: 280px"
                :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle }">

                <!-- Section 1: Mini-month -->
                <div>
                  <p class="text-[10px] font-semibold uppercase tracking-widest mb-3" :style="{ color: L.inkTertiary }">This month</p>

                  <!-- Month nav header -->
                  <div class="flex items-center justify-between mb-2">
                    <button class="w-6 h-6 rounded-full flex items-center justify-center ur-lt-action" :style="{ color: L.inkTertiary }">
                      <ChevronLeftIcon class="w-3.5 h-3.5" />
                    </button>
                    <span class="text-[12px] font-semibold" :style="{ color: L.inkPrimary }">{{ MONTH_LABEL }}</span>
                    <button class="w-6 h-6 rounded-full flex items-center justify-center ur-lt-action" :style="{ color: L.inkTertiary }">
                      <ChevronRightIcon class="w-3.5 h-3.5" />
                    </button>
                  </div>

                  <!-- Day-of-week header -->
                  <div class="grid grid-cols-7 mb-1">
                    <div v-for="d in DAYS_OF_WEEK" :key="d"
                      class="text-center text-[9px] font-semibold uppercase"
                      :style="{ color: L.inkTertiary }">{{ d }}</div>
                  </div>

                  <!-- Day cells -->
                  <div class="grid grid-cols-7 gap-y-0.5">
                    <div v-for="(cell, idx) in MONTH_CELLS" :key="idx"
                      class="flex flex-col items-center">
                      <div
                        v-if="cell !== null"
                        class="w-7 h-7 rounded-full flex items-center justify-center text-[11px] cursor-pointer ur-lt-day"
                        :class="{ 'font-semibold': cell === TODAY_DAY }"
                        :style="cell === TODAY_DAY
                          ? { background: L.lavenderBold, color: '#FFFFFF' }
                          : { color: L.inkPrimary }"
                      >{{ cell }}</div>
                      <div v-else class="w-7 h-7" />
                      <!-- Event dot -->
                      <div v-if="cell !== null && EVENT_DAYS.has(cell)"
                        class="w-[3px] h-[3px] rounded-full -mt-0.5 mb-0.5"
                        :style="{ background: cell === TODAY_DAY ? '#FFFFFF' : L.lavenderBold }" />
                      <div v-else class="h-[3px] mb-0.5" />
                    </div>
                  </div>
                </div>

                <!-- Section 2: Filters -->
                <div class="pt-5 border-t" :style="{ borderColor: L.borderSubtle }">
                  <p class="text-[10px] font-semibold uppercase tracking-widest mb-2.5" :style="{ color: L.inkTertiary }">Filters</p>
                  <div class="flex flex-wrap gap-1.5">
                    <button
                      v-for="f in FILTERS" :key="f.label"
                      class="inline-flex items-center gap-1.5 h-6 px-2.5 rounded-full text-[11px] font-medium transition-colors"
                      :style="f.on
                        ? { border: `1px solid ${L.chipOnBorder}`, background: L.chipOnBg, color: L.chipOnText }
                        : { border: `1px solid ${L.chipOffBorder}`, background: 'transparent', color: L.chipOffText }"
                    >{{ f.label }}</button>
                  </div>
                </div>

                <!-- Section 3: Presence -->
                <div class="pt-5 border-t" :style="{ borderColor: L.borderSubtle }">
                  <p class="text-[10px] font-semibold uppercase tracking-widest mb-2.5" :style="{ color: L.inkTertiary }">Who's around</p>
                  <div class="space-y-2">
                    <div v-for="m in FAMILY" :key="m.initials" class="flex items-center gap-2.5">
                      <div class="relative flex-shrink-0">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center"
                          :style="{ background: m.bg }">
                          <span class="text-[10px] font-bold" :style="{ color: m.text }">{{ m.initials }}</span>
                        </div>
                        <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 rounded-full border-2"
                          :style="{ background: m.online ? L.dotOnline : L.dotOffline, borderColor: L.dotBorder }" />
                      </div>
                      <div class="flex-1 min-w-0">
                        <p class="text-[12px] font-medium truncate" :style="{ color: L.inkPrimary }">{{ m.name }}</p>
                        <p class="text-[10px]" :style="{ color: L.inkTertiary }">{{ m.status }}</p>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Section 4: Saved views -->
                <div class="pt-5 border-t" :style="{ borderColor: L.borderSubtle }">
                  <p class="text-[10px] font-semibold uppercase tracking-widest mb-2" :style="{ color: L.inkTertiary }">Saved views</p>
                  <div class="space-y-0.5">
                    <button
                      v-for="v in SAVED_VIEWS" :key="v"
                      class="w-full flex items-center gap-2 px-2 py-1.5 rounded-lg text-left ur-lt-saved"
                    >
                      <BookmarkIcon class="w-3.5 h-3.5 flex-shrink-0" :style="{ color: L.inkTertiary }" />
                      <span class="text-[12px]" :style="{ color: L.inkPrimary }">{{ v }}</span>
                    </button>
                  </div>
                </div>

                <!-- Section 5: Actions (push to bottom) -->
                <div class="pt-5 border-t mt-auto" :style="{ borderColor: L.borderSubtle }">
                  <p class="text-[10px] font-semibold uppercase tracking-widest mb-2.5" :style="{ color: L.inkTertiary }">Actions</p>
                  <div class="flex items-center gap-1.5">
                    <button class="w-8 h-8 rounded-full flex items-center justify-center ur-lt-action" :style="{ color: L.inkTertiary }">
                      <ArrowPathIcon class="w-4 h-4" />
                    </button>
                    <button class="w-8 h-8 rounded-full flex items-center justify-center ur-lt-action" :style="{ color: L.inkTertiary }">
                      <ArrowTopRightOnSquareIcon class="w-4 h-4" />
                    </button>
                  </div>
                </div>

              </div><!-- /light rail -->
            </div><!-- /light layout -->
          </div><!-- /light panel -->


          <!-- DARK PANEL -->
          <div class="rounded-2xl border p-6 space-y-4" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>
            <p class="text-[12px]" :style="{ color: D.inkTertiary }">Desktop layout — main content + 280px right rail</p>

            <!-- Layout container -->
            <div class="flex rounded-2xl border overflow-hidden" style="min-height: 520px" :style="{ borderColor: D.borderSubtle }">

              <!-- Main content area -->
              <div class="flex-1 p-6" :style="{ background: D.surfaceRaised }">
                <p class="text-[11px] font-semibold uppercase tracking-widest mb-4" :style="{ color: D.inkTertiary }">Calendar</p>
                <p class="text-[22px] font-semibold" :style="{ color: D.inkPrimary }">April 2026</p>
                <p class="text-[13px] mt-1" :style="{ color: D.inkSecondary }">14 events this month · 3 today</p>
                <div class="mt-6 space-y-2">
                  <div v-for="i in 5" :key="i" class="h-10 rounded-xl flex items-center px-3 gap-3"
                    :style="{ background: D.surfaceSunken }">
                    <div class="w-2.5 h-2.5 rounded-full flex-shrink-0" :style="{ background: D.lavenderBold, opacity: 0.5 + i * 0.05 }" />
                    <div class="h-2.5 rounded-full flex-1" :style="{ background: D.borderSubtle, maxWidth: (50 + i * 8) + '%' }" />
                  </div>
                </div>
              </div>

              <!-- Rail — 280px dark -->
              <div class="flex-shrink-0 flex flex-col border-l p-4 space-y-5"
                style="width: 280px"
                :style="{ background: D.surfaceRaised, borderColor: D.borderSubtle }">

                <!-- Section 1: Mini-month -->
                <div>
                  <p class="text-[10px] font-semibold uppercase tracking-widest mb-3" :style="{ color: D.inkTertiary }">This month</p>
                  <div class="flex items-center justify-between mb-2">
                    <button class="w-6 h-6 rounded-full flex items-center justify-center ur-dk-action" :style="{ color: D.inkTertiary }">
                      <ChevronLeftIcon class="w-3.5 h-3.5" />
                    </button>
                    <span class="text-[12px] font-semibold" :style="{ color: D.inkPrimary }">{{ MONTH_LABEL }}</span>
                    <button class="w-6 h-6 rounded-full flex items-center justify-center ur-dk-action" :style="{ color: D.inkTertiary }">
                      <ChevronRightIcon class="w-3.5 h-3.5" />
                    </button>
                  </div>
                  <div class="grid grid-cols-7 mb-1">
                    <div v-for="d in DAYS_OF_WEEK" :key="d"
                      class="text-center text-[9px] font-semibold uppercase"
                      :style="{ color: D.inkTertiary }">{{ d }}</div>
                  </div>
                  <div class="grid grid-cols-7 gap-y-0.5">
                    <div v-for="(cell, idx) in MONTH_CELLS" :key="idx" class="flex flex-col items-center">
                      <div
                        v-if="cell !== null"
                        class="w-7 h-7 rounded-full flex items-center justify-center text-[11px] cursor-pointer ur-dk-day"
                        :class="{ 'font-semibold': cell === TODAY_DAY }"
                        :style="cell === TODAY_DAY
                          ? { background: D.lavenderBold, color: '#141311' }
                          : { color: D.inkPrimary }"
                      >{{ cell }}</div>
                      <div v-else class="w-7 h-7" />
                      <div v-if="cell !== null && EVENT_DAYS.has(cell)"
                        class="w-[3px] h-[3px] rounded-full -mt-0.5 mb-0.5"
                        :style="{ background: cell === TODAY_DAY ? '#141311' : D.lavenderBold }" />
                      <div v-else class="h-[3px] mb-0.5" />
                    </div>
                  </div>
                </div>

                <!-- Section 2: Filters -->
                <div class="pt-5 border-t" :style="{ borderColor: D.borderSubtle }">
                  <p class="text-[10px] font-semibold uppercase tracking-widest mb-2.5" :style="{ color: D.inkTertiary }">Filters</p>
                  <div class="flex flex-wrap gap-1.5">
                    <button
                      v-for="f in FILTERS" :key="f.label"
                      class="inline-flex items-center gap-1.5 h-6 px-2.5 rounded-full text-[11px] font-medium transition-colors"
                      :style="f.on
                        ? { border: `1px solid ${D.chipOnBorder}`, background: D.chipOnBg, color: D.chipOnText }
                        : { border: `1px solid ${D.chipOffBorder}`, background: 'transparent', color: D.chipOffText }"
                    >{{ f.label }}</button>
                  </div>
                </div>

                <!-- Section 3: Presence -->
                <div class="pt-5 border-t" :style="{ borderColor: D.borderSubtle }">
                  <p class="text-[10px] font-semibold uppercase tracking-widest mb-2.5" :style="{ color: D.inkTertiary }">Who's around</p>
                  <div class="space-y-2">
                    <div v-for="m in FAMILY" :key="m.initials" class="flex items-center gap-2.5">
                      <div class="relative flex-shrink-0">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center"
                          :style="{ background: m.bgD }">
                          <span class="text-[10px] font-bold" :style="{ color: m.textD }">{{ m.initials }}</span>
                        </div>
                        <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 rounded-full border-2"
                          :style="{ background: m.online ? D.dotOnline : D.dotOffline, borderColor: D.dotBorder }" />
                      </div>
                      <div class="flex-1 min-w-0">
                        <p class="text-[12px] font-medium truncate" :style="{ color: D.inkPrimary }">{{ m.name }}</p>
                        <p class="text-[10px]" :style="{ color: D.inkTertiary }">{{ m.status }}</p>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Section 4: Saved views -->
                <div class="pt-5 border-t" :style="{ borderColor: D.borderSubtle }">
                  <p class="text-[10px] font-semibold uppercase tracking-widest mb-2" :style="{ color: D.inkTertiary }">Saved views</p>
                  <div class="space-y-0.5">
                    <button
                      v-for="v in SAVED_VIEWS" :key="v"
                      class="w-full flex items-center gap-2 px-2 py-1.5 rounded-lg text-left ur-dk-saved"
                    >
                      <BookmarkIcon class="w-3.5 h-3.5 flex-shrink-0" :style="{ color: D.inkTertiary }" />
                      <span class="text-[12px]" :style="{ color: D.inkPrimary }">{{ v }}</span>
                    </button>
                  </div>
                </div>

                <!-- Section 5: Actions -->
                <div class="pt-5 border-t mt-auto" :style="{ borderColor: D.borderSubtle }">
                  <p class="text-[10px] font-semibold uppercase tracking-widest mb-2.5" :style="{ color: D.inkTertiary }">Actions</p>
                  <div class="flex items-center gap-1.5">
                    <button class="w-8 h-8 rounded-full flex items-center justify-center ur-dk-action" :style="{ color: D.inkTertiary }">
                      <ArrowPathIcon class="w-4 h-4" />
                    </button>
                    <button class="w-8 h-8 rounded-full flex items-center justify-center ur-dk-action" :style="{ color: D.inkTertiary }">
                      <ArrowTopRightOnSquareIcon class="w-4 h-4" />
                    </button>
                  </div>
                </div>

              </div><!-- /dark rail -->
            </div><!-- /dark layout -->
          </div><!-- /dark panel -->

        </div>
      </VariantFrame>
    </section>


    <!-- ══════════════════════════════════════════════════════════════
         SECTION 2 — MINI-MONTH BREAKDOWN
         Larger-scale view of the mini-month, event dots clearly visible.
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-20">
      <VariantFrame
        label="Mini-month"
        caption="April 2026 — today highlighted, event dots on days 3 / 8 / 14 / 17 / 22 / 28"
      >
        <div class="w-full space-y-10">

          <!-- LIGHT -->
          <div class="rounded-2xl border p-6 space-y-4" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>
            <p class="text-[12px]" :style="{ color: L.inkTertiary }">Month nav + grid at larger scale. Today = lavender fill. Event days = 3px dot below number.</p>

            <div class="max-w-[320px]">
              <!-- Month nav -->
              <div class="flex items-center justify-between mb-3">
                <button class="w-8 h-8 rounded-full flex items-center justify-center ur-lt-action" :style="{ color: L.inkSecondary }">
                  <ChevronLeftIcon class="w-4 h-4" />
                </button>
                <span class="text-[14px] font-semibold" :style="{ color: L.inkPrimary }">{{ MONTH_LABEL }}</span>
                <button class="w-8 h-8 rounded-full flex items-center justify-center ur-lt-action" :style="{ color: L.inkSecondary }">
                  <ChevronRightIcon class="w-4 h-4" />
                </button>
              </div>

              <!-- Day-of-week header -->
              <div class="grid grid-cols-7 mb-1">
                <div v-for="d in DAYS_OF_WEEK" :key="d"
                  class="text-center text-[10px] font-semibold uppercase"
                  :style="{ color: L.inkTertiary }">{{ d }}</div>
              </div>

              <!-- Day cells (larger — w-9 h-9) -->
              <div class="grid grid-cols-7 gap-y-1">
                <div v-for="(cell, idx) in MONTH_CELLS" :key="idx" class="flex flex-col items-center">
                  <div
                    v-if="cell !== null"
                    class="w-9 h-9 rounded-full flex items-center justify-center text-[13px] cursor-pointer ur-lt-day-lg"
                    :class="{ 'font-semibold': cell === TODAY_DAY }"
                    :style="cell === TODAY_DAY
                      ? { background: L.lavenderBold, color: '#FFFFFF' }
                      : { color: L.inkPrimary }"
                  >{{ cell }}</div>
                  <div v-else class="w-9 h-9" />
                  <!-- Event dot -->
                  <div v-if="cell !== null && EVENT_DAYS.has(cell)"
                    class="w-[4px] h-[4px] rounded-full -mt-1 mb-1"
                    :style="{ background: cell === TODAY_DAY ? '#FFFFFF' : L.lavenderBold }" />
                  <div v-else class="h-[4px] mb-1" />
                </div>
              </div>
            </div>

            <!-- Legend -->
            <div class="flex items-center gap-6 pt-2">
              <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full flex items-center justify-center text-[11px] font-semibold"
                  :style="{ background: L.lavenderBold, color: '#FFFFFF' }">18</div>
                <span class="text-[11px]" :style="{ color: L.inkSecondary }">Today</span>
              </div>
              <div class="flex items-center gap-2">
                <div class="flex flex-col items-center">
                  <div class="w-7 h-7 rounded-full flex items-center justify-center text-[11px]"
                    :style="{ color: L.inkPrimary }">8</div>
                  <div class="w-[4px] h-[4px] rounded-full -mt-1" :style="{ background: L.lavenderBold }" />
                </div>
                <span class="text-[11px]" :style="{ color: L.inkSecondary }">Has events</span>
              </div>
            </div>
          </div><!-- /light mini-month -->

          <!-- DARK -->
          <div class="rounded-2xl border p-6 space-y-4" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>
            <p class="text-[12px]" :style="{ color: D.inkTertiary }">Same month — lavender-bold on dark uses #B6A8E6 fill with charcoal text.</p>

            <div class="max-w-[320px]">
              <div class="flex items-center justify-between mb-3">
                <button class="w-8 h-8 rounded-full flex items-center justify-center ur-dk-action" :style="{ color: D.inkSecondary }">
                  <ChevronLeftIcon class="w-4 h-4" />
                </button>
                <span class="text-[14px] font-semibold" :style="{ color: D.inkPrimary }">{{ MONTH_LABEL }}</span>
                <button class="w-8 h-8 rounded-full flex items-center justify-center ur-dk-action" :style="{ color: D.inkSecondary }">
                  <ChevronRightIcon class="w-4 h-4" />
                </button>
              </div>
              <div class="grid grid-cols-7 mb-1">
                <div v-for="d in DAYS_OF_WEEK" :key="d"
                  class="text-center text-[10px] font-semibold uppercase"
                  :style="{ color: D.inkTertiary }">{{ d }}</div>
              </div>
              <div class="grid grid-cols-7 gap-y-1">
                <div v-for="(cell, idx) in MONTH_CELLS" :key="idx" class="flex flex-col items-center">
                  <div
                    v-if="cell !== null"
                    class="w-9 h-9 rounded-full flex items-center justify-center text-[13px] cursor-pointer ur-dk-day-lg"
                    :class="{ 'font-semibold': cell === TODAY_DAY }"
                    :style="cell === TODAY_DAY
                      ? { background: D.lavenderBold, color: '#141311' }
                      : { color: D.inkPrimary }"
                  >{{ cell }}</div>
                  <div v-else class="w-9 h-9" />
                  <div v-if="cell !== null && EVENT_DAYS.has(cell)"
                    class="w-[4px] h-[4px] rounded-full -mt-1 mb-1"
                    :style="{ background: cell === TODAY_DAY ? '#141311' : D.lavenderBold }" />
                  <div v-else class="h-[4px] mb-1" />
                </div>
              </div>
            </div>

            <div class="flex items-center gap-6 pt-2">
              <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full flex items-center justify-center text-[11px] font-semibold"
                  :style="{ background: D.lavenderBold, color: '#141311' }">18</div>
                <span class="text-[11px]" :style="{ color: D.inkSecondary }">Today</span>
              </div>
              <div class="flex items-center gap-2">
                <div class="flex flex-col items-center">
                  <div class="w-7 h-7 rounded-full flex items-center justify-center text-[11px]"
                    :style="{ color: D.inkPrimary }">8</div>
                  <div class="w-[4px] h-[4px] rounded-full -mt-1" :style="{ background: D.lavenderBold }" />
                </div>
                <span class="text-[11px]" :style="{ color: D.inkSecondary }">Has events</span>
              </div>
            </div>
          </div><!-- /dark mini-month -->

        </div>
      </VariantFrame>
    </section>


    <!-- ══════════════════════════════════════════════════════════════
         SECTION 3 — FILTER CHIP ROW BREAKDOWN
         Chip 1.3 outlined + soft-tint treatment. Calendar context.
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-20">
      <VariantFrame
        label="Filter chips"
        caption="Outlined + soft-tint chips (Chip 1.3 treatment) — active = lavender fill + border, off = subtle border + muted text"
      >
        <div class="w-full space-y-10">

          <!-- LIGHT -->
          <div class="rounded-2xl border p-6 space-y-6" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>
            <p class="text-[12px]" :style="{ color: L.inkTertiary }">Calendar context. "Mine" is active. Tap to toggle.</p>

            <!-- Filters row -->
            <div class="flex flex-wrap gap-2">
              <button
                v-for="f in FILTERS" :key="f.label"
                class="inline-flex items-center gap-1.5 h-7 px-3 rounded-full text-[12px] font-medium transition-colors"
                :style="f.on
                  ? { border: `1px solid ${L.chipOnBorder}`, background: L.chipOnBg, color: L.chipOnText }
                  : { border: `1px solid ${L.chipOffBorder}`, background: 'transparent', color: L.chipOffText }"
              >{{ f.label }}</button>
            </div>

            <!-- Annotation -->
            <div class="flex gap-6 pt-2">
              <div class="flex items-center gap-2">
                <button class="inline-flex items-center h-7 px-3 rounded-full text-[12px] font-medium"
                  :style="{ border: `1px solid ${L.chipOnBorder}`, background: L.chipOnBg, color: L.chipOnText }">
                  Mine
                </button>
                <span class="text-[11px]" :style="{ color: L.inkSecondary }">On — lavender outline + 50% tint bg</span>
              </div>
              <div class="flex items-center gap-2">
                <button class="inline-flex items-center h-7 px-3 rounded-full text-[12px] font-medium"
                  :style="{ border: `1px solid ${L.chipOffBorder}`, background: 'transparent', color: L.chipOffText }">
                  Family
                </button>
                <span class="text-[11px]" :style="{ color: L.inkSecondary }">Off — border-subtle + ink-tertiary</span>
              </div>
            </div>
          </div><!-- /light filters -->

          <!-- DARK -->
          <div class="rounded-2xl border p-6 space-y-6" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>
            <p class="text-[12px]" :style="{ color: D.inkTertiary }">Same chip logic — active = #2D2840 bg + #4A4272 border + #B6A8E6 text.</p>

            <div class="flex flex-wrap gap-2">
              <button
                v-for="f in FILTERS" :key="f.label"
                class="inline-flex items-center gap-1.5 h-7 px-3 rounded-full text-[12px] font-medium transition-colors"
                :style="f.on
                  ? { border: `1px solid ${D.chipOnBorder}`, background: D.chipOnBg, color: D.chipOnText }
                  : { border: `1px solid ${D.chipOffBorder}`, background: 'transparent', color: D.chipOffText }"
              >{{ f.label }}</button>
            </div>

            <div class="flex gap-6 pt-2">
              <div class="flex items-center gap-2">
                <button class="inline-flex items-center h-7 px-3 rounded-full text-[12px] font-medium"
                  :style="{ border: `1px solid ${D.chipOnBorder}`, background: D.chipOnBg, color: D.chipOnText }">
                  Mine
                </button>
                <span class="text-[11px]" :style="{ color: D.inkSecondary }">On — dark lavender bg + border</span>
              </div>
              <div class="flex items-center gap-2">
                <button class="inline-flex items-center h-7 px-3 rounded-full text-[12px] font-medium"
                  :style="{ border: `1px solid ${D.chipOffBorder}`, background: 'transparent', color: D.chipOffText }">
                  Family
                </button>
                <span class="text-[11px]" :style="{ color: D.inkSecondary }">Off — border-subtle + ink-tertiary</span>
              </div>
            </div>
          </div><!-- /dark filters -->

        </div>
      </VariantFrame>
    </section>


    <!-- ══════════════════════════════════════════════════════════════
         SECTION 4 — PRESENCE LIST BREAKDOWN
         5 family members, online/offline status dots.
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-20">
      <VariantFrame
        label="Presence list"
        caption="Who's around — 32px avatar + name + status text + online (green) / offline (muted) dot"
      >
        <div class="w-full space-y-10">

          <!-- LIGHT -->
          <div class="rounded-2xl border p-6 space-y-4" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>
            <p class="text-[12px]" :style="{ color: L.inkTertiary }">Green dot = online. Muted gray = offline. Each family member gets their pastel avatar color.</p>

            <div class="max-w-xs space-y-3">
              <div v-for="m in FAMILY" :key="m.initials" class="flex items-center gap-3">
                <div class="relative flex-shrink-0">
                  <div class="w-9 h-9 rounded-full flex items-center justify-center"
                    :style="{ background: m.bg }">
                    <span class="text-[11px] font-bold" :style="{ color: m.text }">{{ m.initials }}</span>
                  </div>
                  <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full border-2"
                    :style="{ background: m.online ? L.dotOnline : L.dotOffline, borderColor: L.dotBorder }" />
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-[13px] font-medium truncate" :style="{ color: L.inkPrimary }">{{ m.name }}</p>
                  <p class="text-[11px]" :style="{ color: m.online ? L.dotOnline : L.inkTertiary }">{{ m.status }}</p>
                </div>
                <!-- Status badge pill -->
                <div class="flex-shrink-0 flex items-center gap-1">
                  <div class="w-1.5 h-1.5 rounded-full"
                    :style="{ background: m.online ? L.dotOnline : L.dotOffline }" />
                  <span class="text-[10px]" :style="{ color: m.online ? L.dotOnline : L.inkTertiary }">
                    {{ m.online ? 'Now' : 'Away' }}
                  </span>
                </div>
              </div>
            </div>
          </div><!-- /light presence -->

          <!-- DARK -->
          <div class="rounded-2xl border p-6 space-y-4" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>
            <p class="text-[12px]" :style="{ color: D.inkTertiary }">Same list — border of status dot matches dark surface (#1C1B19) for crisp separation.</p>

            <div class="max-w-xs space-y-3">
              <div v-for="m in FAMILY" :key="m.initials" class="flex items-center gap-3">
                <div class="relative flex-shrink-0">
                  <div class="w-9 h-9 rounded-full flex items-center justify-center"
                    :style="{ background: m.bgD }">
                    <span class="text-[11px] font-bold" :style="{ color: m.textD }">{{ m.initials }}</span>
                  </div>
                  <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full border-2"
                    :style="{ background: m.online ? D.dotOnline : D.dotOffline, borderColor: D.dotBorder }" />
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-[13px] font-medium truncate" :style="{ color: D.inkPrimary }">{{ m.name }}</p>
                  <p class="text-[11px]" :style="{ color: m.online ? D.dotOnline : D.inkTertiary }">{{ m.status }}</p>
                </div>
                <div class="flex-shrink-0 flex items-center gap-1">
                  <div class="w-1.5 h-1.5 rounded-full"
                    :style="{ background: m.online ? D.dotOnline : D.dotOffline }" />
                  <span class="text-[10px]" :style="{ color: m.online ? D.dotOnline : D.inkTertiary }">
                    {{ m.online ? 'Now' : 'Away' }}
                  </span>
                </div>
              </div>
            </div>
          </div><!-- /dark presence -->

        </div>
      </VariantFrame>
    </section>


    <!-- ══════════════════════════════════════════════════════════════
         SECTION 5 — SAVED VIEWS LIST BREAKDOWN
         Bookmark icon + view name + hover state.
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-20">
      <VariantFrame
        label="Saved views"
        caption="Saved filter combos — bookmark icon, view name, surface-sunken hover state"
      >
        <div class="w-full space-y-10">

          <!-- LIGHT -->
          <div class="rounded-2xl border p-6 space-y-4" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>
            <p class="text-[12px]" :style="{ color: L.inkTertiary }">Hover a row to see the surface-sunken bg transition (150ms ease-out).</p>

            <div class="max-w-xs space-y-0.5">
              <button
                v-for="v in SAVED_VIEWS" :key="v"
                class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-left ur-lt-saved-lg"
              >
                <BookmarkIcon class="w-4 h-4 flex-shrink-0" :style="{ color: L.inkTertiary }" />
                <span class="text-[13px] flex-1" :style="{ color: L.inkPrimary }">{{ v }}</span>
              </button>
            </div>

            <p class="text-[11px]" :style="{ color: L.inkTertiary }">
              Hover bg: <code class="font-mono text-[10px] px-1 py-0.5 rounded" :style="{ background: L.surfaceSunken }">surface-sunken #F5F2EE</code> · transition 150ms cubic-bezier(0.16, 1, 0.3, 1)
            </p>
          </div><!-- /light saved -->

          <!-- DARK -->
          <div class="rounded-2xl border p-6 space-y-4" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>
            <p class="text-[12px]" :style="{ color: D.inkTertiary }">Hover bg is border-subtle (#2C2A27) — clearly lighter than the rail surface (#1C1B19).</p>

            <div class="max-w-xs space-y-0.5">
              <button
                v-for="v in SAVED_VIEWS" :key="v"
                class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-left ur-dk-saved-lg"
              >
                <BookmarkIcon class="w-4 h-4 flex-shrink-0" :style="{ color: D.inkTertiary }" />
                <span class="text-[13px] flex-1" :style="{ color: D.inkPrimary }">{{ v }}</span>
              </button>
            </div>

            <p class="text-[11px]" :style="{ color: D.inkTertiary }">
              Hover bg: <code class="font-mono text-[10px] px-1 py-0.5 rounded" :style="{ background: D.borderSubtle, color: D.inkSecondary }">border-subtle #2C2A27</code> · 150ms spring
            </p>
          </div><!-- /dark saved -->

        </div>
      </VariantFrame>
    </section>


    <!-- ══════════════════════════════════════════════════════════════
         SECTION 6 — MOBILE SLIDE-UP SHEET
         375px faux viewport. Rail as bottom sheet with drag handle.
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-20">
      <VariantFrame
        label="Mobile slide-up sheet"
        caption="Rail on screens &lt; 768px — slides up from bottom as a sheet with 28px top radius and drag handle"
      >
        <div class="w-full space-y-10">

          <!-- LIGHT -->
          <div class="rounded-2xl border p-6 space-y-4" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode — mobile (375px)</p>
            <p class="text-[12px]" :style="{ color: L.inkTertiary }">
              Triggered by a "More" button in the main content area. Bottom sheet slides up with 28px top-radius + drag handle.
              Mini-month stays full-width. Presence list condenses to avatar-only row on very small heights.
            </p>

            <!-- Faux mobile viewport + sheet -->
            <div class="max-w-[375px] mx-auto" style="height: 540px; position: relative; overflow: hidden; border-radius: 20px; border: 1px solid"
              :style="{ borderColor: L.borderSubtle }">

              <!-- Faux main content behind sheet -->
              <div class="absolute inset-0 p-4" :style="{ background: L.surfaceRaised }">
                <p class="text-[13px] font-semibold" :style="{ color: L.inkPrimary }">Calendar</p>
                <p class="text-[11px] mt-0.5" :style="{ color: L.inkTertiary }">April 2026 · 14 events</p>
              </div>

              <!-- Slide-up sheet — positioned to simulate partially slid-up state -->
              <div
                class="absolute bottom-0 left-0 right-0"
                style="border-radius: 28px 28px 0 0; min-height: 480px; box-shadow: 0 -4px 24px rgba(28, 20, 10, 0.12)"
                :style="{ background: L.surfaceRaised }"
              >
                <!-- Drag handle -->
                <div class="flex justify-center pt-3 pb-1">
                  <div class="w-10 h-1 rounded-full" :style="{ background: L.borderSubtle }" />
                </div>

                <!-- Sheet contents -->
                <div class="px-5 pb-5 space-y-5" style="overflow-y: auto; max-height: 440px">

                  <!-- Mini-month — full width on mobile -->
                  <div>
                    <p class="text-[10px] font-semibold uppercase tracking-widest mb-2.5" :style="{ color: L.inkTertiary }">This month</p>
                    <div class="flex items-center justify-between mb-2">
                      <button class="w-7 h-7 rounded-full flex items-center justify-center" :style="{ color: L.inkSecondary }">
                        <ChevronLeftIcon class="w-3.5 h-3.5" />
                      </button>
                      <span class="text-[12px] font-semibold" :style="{ color: L.inkPrimary }">{{ MONTH_LABEL }}</span>
                      <button class="w-7 h-7 rounded-full flex items-center justify-center" :style="{ color: L.inkSecondary }">
                        <ChevronRightIcon class="w-3.5 h-3.5" />
                      </button>
                    </div>
                    <div class="grid grid-cols-7 mb-1">
                      <div v-for="d in DAYS_OF_WEEK" :key="d" class="text-center text-[9px] font-semibold uppercase" :style="{ color: L.inkTertiary }">{{ d }}</div>
                    </div>
                    <div class="grid grid-cols-7 gap-y-0.5">
                      <div v-for="(cell, idx) in MONTH_CELLS" :key="idx" class="flex flex-col items-center">
                        <div v-if="cell !== null" class="w-8 h-8 rounded-full flex items-center justify-center text-[11px]"
                          :class="{ 'font-semibold': cell === TODAY_DAY }"
                          :style="cell === TODAY_DAY
                            ? { background: L.lavenderBold, color: '#FFFFFF' }
                            : { color: L.inkPrimary }">{{ cell }}</div>
                        <div v-else class="w-8 h-8" />
                        <div v-if="cell !== null && EVENT_DAYS.has(cell)"
                          class="w-[3px] h-[3px] rounded-full -mt-0.5 mb-0.5"
                          :style="{ background: cell === TODAY_DAY ? '#FFFFFF' : L.lavenderBold }" />
                        <div v-else class="h-[3px] mb-0.5" />
                      </div>
                    </div>
                  </div>

                  <!-- Filters — horizontal scroll on mobile -->
                  <div class="border-t pt-4" :style="{ borderColor: L.borderSubtle }">
                    <p class="text-[10px] font-semibold uppercase tracking-widest mb-2.5" :style="{ color: L.inkTertiary }">Filters</p>
                    <div class="flex gap-2 overflow-x-auto pb-1" style="scrollbar-width: none">
                      <button v-for="f in FILTERS" :key="f.label"
                        class="inline-flex flex-shrink-0 items-center h-7 px-3 rounded-full text-[12px] font-medium"
                        :style="f.on
                          ? { border: `1px solid ${L.chipOnBorder}`, background: L.chipOnBg, color: L.chipOnText }
                          : { border: `1px solid ${L.chipOffBorder}`, background: 'transparent', color: L.chipOffText }">
                        {{ f.label }}
                      </button>
                    </div>
                  </div>

                  <!-- Presence — avatar-only compact row on mobile -->
                  <div class="border-t pt-4" :style="{ borderColor: L.borderSubtle }">
                    <p class="text-[10px] font-semibold uppercase tracking-widest mb-2.5" :style="{ color: L.inkTertiary }">Who's around</p>
                    <div class="flex items-center gap-2">
                      <div v-for="m in FAMILY" :key="m.initials" class="relative flex-shrink-0">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center"
                          :style="{ background: m.bg }">
                          <span class="text-[10px] font-bold" :style="{ color: m.text }">{{ m.initials }}</span>
                        </div>
                        <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 rounded-full border-2"
                          :style="{ background: m.online ? L.dotOnline : L.dotOffline, borderColor: L.dotBorder }" />
                      </div>
                    </div>
                  </div>

                  <!-- Saved views -->
                  <div class="border-t pt-4" :style="{ borderColor: L.borderSubtle }">
                    <p class="text-[10px] font-semibold uppercase tracking-widest mb-2" :style="{ color: L.inkTertiary }">Saved views</p>
                    <div class="space-y-1">
                      <button v-for="v in SAVED_VIEWS" :key="v"
                        class="w-full flex items-center gap-2.5 px-2 py-2 rounded-xl text-left ur-lt-saved"
                      >
                        <BookmarkIcon class="w-4 h-4 flex-shrink-0" :style="{ color: L.inkTertiary }" />
                        <span class="text-[13px]" :style="{ color: L.inkPrimary }">{{ v }}</span>
                      </button>
                    </div>
                  </div>

                  <!-- Actions -->
                  <div class="border-t pt-4" :style="{ borderColor: L.borderSubtle }">
                    <p class="text-[10px] font-semibold uppercase tracking-widest mb-2.5" :style="{ color: L.inkTertiary }">Actions</p>
                    <div class="flex gap-2">
                      <button class="w-9 h-9 rounded-full flex items-center justify-center ur-lt-action" :style="{ color: L.inkTertiary }">
                        <ArrowPathIcon class="w-4 h-4" />
                      </button>
                      <button class="w-9 h-9 rounded-full flex items-center justify-center ur-lt-action" :style="{ color: L.inkTertiary }">
                        <ArrowTopRightOnSquareIcon class="w-4 h-4" />
                      </button>
                    </div>
                  </div>

                </div><!-- /sheet contents -->
              </div><!-- /sheet -->
            </div><!-- /faux mobile -->
          </div><!-- /light mobile -->


          <!-- DARK -->
          <div class="rounded-2xl border p-6 space-y-4" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode — mobile (375px)</p>
            <p class="text-[12px]" :style="{ color: D.inkTertiary }">
              Dark sheet surface is #1C1B19 (surface-raised) — clearly lighter than #141311 app bg. Drag handle uses border-subtle (#2C2A27).
            </p>

            <div class="max-w-[375px] mx-auto" style="height: 540px; position: relative; overflow: hidden; border-radius: 20px; border: 1px solid"
              :style="{ borderColor: D.borderSubtle }">

              <!-- Faux main content -->
              <div class="absolute inset-0 p-4" :style="{ background: D.surfaceApp }">
                <p class="text-[13px] font-semibold" :style="{ color: D.inkPrimary }">Calendar</p>
                <p class="text-[11px] mt-0.5" :style="{ color: D.inkTertiary }">April 2026 · 14 events</p>
              </div>

              <!-- Slide-up sheet -->
              <div
                class="absolute bottom-0 left-0 right-0"
                style="border-radius: 28px 28px 0 0; min-height: 480px; box-shadow: 0 -4px 24px rgba(0, 0, 0, 0.40)"
                :style="{ background: D.surfaceRaised }"
              >
                <!-- Drag handle -->
                <div class="flex justify-center pt-3 pb-1">
                  <div class="w-10 h-1 rounded-full" :style="{ background: D.borderSubtle }" />
                </div>

                <!-- Sheet contents -->
                <div class="px-5 pb-5 space-y-5" style="overflow-y: auto; max-height: 440px">

                  <!-- Mini-month -->
                  <div>
                    <p class="text-[10px] font-semibold uppercase tracking-widest mb-2.5" :style="{ color: D.inkTertiary }">This month</p>
                    <div class="flex items-center justify-between mb-2">
                      <button class="w-7 h-7 rounded-full flex items-center justify-center" :style="{ color: D.inkSecondary }">
                        <ChevronLeftIcon class="w-3.5 h-3.5" />
                      </button>
                      <span class="text-[12px] font-semibold" :style="{ color: D.inkPrimary }">{{ MONTH_LABEL }}</span>
                      <button class="w-7 h-7 rounded-full flex items-center justify-center" :style="{ color: D.inkSecondary }">
                        <ChevronRightIcon class="w-3.5 h-3.5" />
                      </button>
                    </div>
                    <div class="grid grid-cols-7 mb-1">
                      <div v-for="d in DAYS_OF_WEEK" :key="d" class="text-center text-[9px] font-semibold uppercase" :style="{ color: D.inkTertiary }">{{ d }}</div>
                    </div>
                    <div class="grid grid-cols-7 gap-y-0.5">
                      <div v-for="(cell, idx) in MONTH_CELLS" :key="idx" class="flex flex-col items-center">
                        <div v-if="cell !== null" class="w-8 h-8 rounded-full flex items-center justify-center text-[11px]"
                          :class="{ 'font-semibold': cell === TODAY_DAY }"
                          :style="cell === TODAY_DAY
                            ? { background: D.lavenderBold, color: '#141311' }
                            : { color: D.inkPrimary }">{{ cell }}</div>
                        <div v-else class="w-8 h-8" />
                        <div v-if="cell !== null && EVENT_DAYS.has(cell)"
                          class="w-[3px] h-[3px] rounded-full -mt-0.5 mb-0.5"
                          :style="{ background: cell === TODAY_DAY ? '#141311' : D.lavenderBold }" />
                        <div v-else class="h-[3px] mb-0.5" />
                      </div>
                    </div>
                  </div>

                  <!-- Filters -->
                  <div class="border-t pt-4" :style="{ borderColor: D.borderSubtle }">
                    <p class="text-[10px] font-semibold uppercase tracking-widest mb-2.5" :style="{ color: D.inkTertiary }">Filters</p>
                    <div class="flex gap-2 overflow-x-auto pb-1" style="scrollbar-width: none">
                      <button v-for="f in FILTERS" :key="f.label"
                        class="inline-flex flex-shrink-0 items-center h-7 px-3 rounded-full text-[12px] font-medium"
                        :style="f.on
                          ? { border: `1px solid ${D.chipOnBorder}`, background: D.chipOnBg, color: D.chipOnText }
                          : { border: `1px solid ${D.chipOffBorder}`, background: 'transparent', color: D.chipOffText }">
                        {{ f.label }}
                      </button>
                    </div>
                  </div>

                  <!-- Presence -->
                  <div class="border-t pt-4" :style="{ borderColor: D.borderSubtle }">
                    <p class="text-[10px] font-semibold uppercase tracking-widest mb-2.5" :style="{ color: D.inkTertiary }">Who's around</p>
                    <div class="flex items-center gap-2">
                      <div v-for="m in FAMILY" :key="m.initials" class="relative flex-shrink-0">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center"
                          :style="{ background: m.bgD }">
                          <span class="text-[10px] font-bold" :style="{ color: m.textD }">{{ m.initials }}</span>
                        </div>
                        <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 rounded-full border-2"
                          :style="{ background: m.online ? D.dotOnline : D.dotOffline, borderColor: D.dotBorder }" />
                      </div>
                    </div>
                  </div>

                  <!-- Saved views -->
                  <div class="border-t pt-4" :style="{ borderColor: D.borderSubtle }">
                    <p class="text-[10px] font-semibold uppercase tracking-widest mb-2" :style="{ color: D.inkTertiary }">Saved views</p>
                    <div class="space-y-1">
                      <button v-for="v in SAVED_VIEWS" :key="v"
                        class="w-full flex items-center gap-2.5 px-2 py-2 rounded-xl text-left ur-dk-saved"
                      >
                        <BookmarkIcon class="w-4 h-4 flex-shrink-0" :style="{ color: D.inkTertiary }" />
                        <span class="text-[13px]" :style="{ color: D.inkPrimary }">{{ v }}</span>
                      </button>
                    </div>
                  </div>

                  <!-- Actions -->
                  <div class="border-t pt-4" :style="{ borderColor: D.borderSubtle }">
                    <p class="text-[10px] font-semibold uppercase tracking-widest mb-2.5" :style="{ color: D.inkTertiary }">Actions</p>
                    <div class="flex gap-2">
                      <button class="w-9 h-9 rounded-full flex items-center justify-center ur-dk-action" :style="{ color: D.inkTertiary }">
                        <ArrowPathIcon class="w-4 h-4" />
                      </button>
                      <button class="w-9 h-9 rounded-full flex items-center justify-center ur-dk-action" :style="{ color: D.inkTertiary }">
                        <ArrowTopRightOnSquareIcon class="w-4 h-4" />
                      </button>
                    </div>
                  </div>

                </div><!-- /dark sheet contents -->
              </div><!-- /dark sheet -->
            </div><!-- /dark faux mobile -->
          </div><!-- /dark mobile -->

        </div>
      </VariantFrame>
    </section>


    <!-- ══════════════════════════════════════════════════════════════
         USAGE GUIDE
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-8">
      <div class="rounded-2xl border p-6 space-y-4" :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle }">
        <h2 class="text-[17px] font-semibold" :style="{ color: L.inkPrimary }">Right utility rail — usage rules</h2>
        <ul class="space-y-3 text-[14px]" :style="{ color: L.inkSecondary }">
          <li>
            <strong :style="{ color: L.inkPrimary }">Desktop data-heavy pages only.</strong>
            Not a default layout element. The rail appears exclusively on: Calendar, Tasks, Vault, and the Food module.
            Other pages (Dashboard, Settings, Chat, Auth) have no rail.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Rail collapses to slide-up sheet on mobile.</strong>
            On screens &lt; 768px the rail is hidden. A small "More" button in the main content area triggers a bottom sheet
            that slides up from the bottom edge. Same contents, repositioned for touch.
            Sheet uses <code class="font-mono text-[12px] px-1 py-0.5 rounded" :style="{ background: L.surfaceSunken }">border-radius: 28px 28px 0 0</code>
            and a drag handle.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Contents are per-module.</strong>
            The rail adapts its sections to context:
            Calendar → mini-month + filters + presence + saved views;
            Tasks → quick filters + saved views + presence;
            Vault → recent entries + filters;
            Food → week strip + filters.
            The visual shape (280px width, section dividers, title caps) is constant.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Rail width is fixed at 280px.</strong>
            Never flex-grow. Main content area takes remaining space. Left sidebar (3.3) + main content + right rail = 3-column layout on wide screens.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Filters use Chip 1.3 outlined treatment.</strong>
            Active chip: lavender border + 50% tint bg. Off chip: border-subtle + ink-tertiary.
            Horizontal scroll on mobile to avoid wrapping.
          </li>
        </ul>
      </div>
    </section>

  </ComponentPage>
</template>

<style scoped>
/*
  ═══════════════════════════════════════════════════════════════
  LIGHT — interactive hover states
  All transitions: 150ms cubic-bezier(0.16, 1, 0.3, 1)
  ═══════════════════════════════════════════════════════════════
*/

/* Action icon buttons (chevrons, refresh, export) */
.ur-lt-action {
  transition: background-color 150ms cubic-bezier(0.16, 1, 0.3, 1);
}
.ur-lt-action:hover {
  background-color: #F5F2EE; /* surface-sunken */
}

/* Day cells — sm (28px) in the full rail */
.ur-lt-day {
  transition: background-color 150ms cubic-bezier(0.16, 1, 0.3, 1);
}
.ur-lt-day:hover {
  background-color: #F5F2EE;
}

/* Day cells — lg (36px) in the breakdown section */
.ur-lt-day-lg {
  transition: background-color 150ms cubic-bezier(0.16, 1, 0.3, 1);
}
.ur-lt-day-lg:hover {
  background-color: #F5F2EE;
}

/* Saved view rows — sm in rail */
.ur-lt-saved {
  transition: background-color 150ms cubic-bezier(0.16, 1, 0.3, 1);
}
.ur-lt-saved:hover {
  background-color: #F5F2EE;
}

/* Saved view rows — lg in breakdown */
.ur-lt-saved-lg {
  transition: background-color 150ms cubic-bezier(0.16, 1, 0.3, 1);
}
.ur-lt-saved-lg:hover {
  background-color: #F5F2EE;
}

/*
  ═══════════════════════════════════════════════════════════════
  DARK — interactive hover states
  Hover bgs are LIGHTER than the rail surface (#1C1B19)
  so they read against the dark panel.
  ═══════════════════════════════════════════════════════════════
*/

.ur-dk-action {
  transition: background-color 150ms cubic-bezier(0.16, 1, 0.3, 1);
}
.ur-dk-action:hover {
  background-color: #2C2A27; /* border-subtle — lighter than surface-raised */
}

.ur-dk-day {
  transition: background-color 150ms cubic-bezier(0.16, 1, 0.3, 1);
}
.ur-dk-day:hover {
  background-color: #2C2A27;
}

.ur-dk-day-lg {
  transition: background-color 150ms cubic-bezier(0.16, 1, 0.3, 1);
}
.ur-dk-day-lg:hover {
  background-color: #2C2A27;
}

.ur-dk-saved {
  transition: background-color 150ms cubic-bezier(0.16, 1, 0.3, 1);
}
.ur-dk-saved:hover {
  background-color: #2C2A27;
}

.ur-dk-saved-lg {
  transition: background-color 150ms cubic-bezier(0.16, 1, 0.3, 1);
}
.ur-dk-saved-lg:hover {
  background-color: #2C2A27;
}

/*
  ═══════════════════════════════════════════════════════════════
  REDUCED MOTION
  Strip all transitions. Hover still changes bg — no animation.
  ═══════════════════════════════════════════════════════════════
*/
@media (prefers-reduced-motion: reduce) {
  .ur-lt-action,
  .ur-lt-day,
  .ur-lt-day-lg,
  .ur-lt-saved,
  .ur-lt-saved-lg,
  .ur-dk-action,
  .ur-dk-day,
  .ur-dk-day-lg,
  .ur-dk-saved,
  .ur-dk-saved-lg {
    transition: none;
  }
}
</style>
