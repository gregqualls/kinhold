<script setup>
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

// ── Day labels for track header ───────────────────────────────────────────────
const DAYS = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']

// ── Avatars ───────────────────────────────────────────────────────────────────
const BIDDER_AVATARS = [
  { initials: 'E', color: '#6856B2' },
  { initials: 'J', color: '#2E8A62' },
  { initials: 'L', color: '#A2780C' },
]
const VACATION_AVATARS = [
  { initials: 'E', color: '#6856B2' },
  { initials: 'G', color: '#BA562E' },
]
const STACKED_ROWS = [
  { label: 'Leftover stir-fry', colStart: 1, colSpan: 3, accent: 'mint',     icon: true },
  { label: 'Movie night pass',  colStart: 5, colSpan: 3, accent: 'sun',      icon: false, bids: true },
  { label: 'Emma & Greg · Spring break', colStart: 1, colSpan: 7, accent: 'lavender', avatars: VACATION_AVATARS },
  { label: 'Dentist',           colStart: 2, colSpan: 1, accent: 'peach',    icon: false },
]
</script>

<template>
  <ComponentPage
    title="5.7 TimelineRow"
    description="Pill-shaped bar spanning a date range inside a 7-column week track. Contains a label and optionally avatars and a drag handle. Used in meal plan (recipe spanning days), reward auction windows, family schedule overlay (vacation), and workout blocks."
    status="scaffolded"
  >

    <!-- ══════════════════════════════════════════════════════════════
         VARIANT A — Filled soft pill, bold-color text
         rounded-full, accent-soft fill, accent-bold text.
         Label left, avatars right, drag handle far right.
         Simplest and most readable.
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="A" caption="Filled soft pill — accent-soft bg · accent-bold text · label left · avatars right · drag handle far right">
        <div class="w-full space-y-10">

          <!-- ── LIGHT ── -->
          <div class="rounded-2xl border p-6 space-y-8"
               :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode · Desktop</p>

            <!-- Week track: 7 columns with day labels + dividers -->
            <div class="space-y-3">

              <!-- Day header row -->
              <div class="grid grid-cols-7">
                <div v-for="(day, i) in DAYS" :key="i"
                     class="text-center text-[10px] font-semibold uppercase tracking-widest pb-1"
                     :style="{ color: L.inkTertiary, borderRight: i < 6 ? `1px solid ${L.borderSubtle}` : 'none' }">
                  {{ day }}
                </div>
              </div>

              <!-- Track rows -->
              <div class="rounded-xl overflow-hidden border" :style="{ borderColor: L.borderSubtle, background: L.surfaceSunken }">

                <!-- Row 1: Meal plan — Leftover stir-fry · Mon→Wed (cols 1–3) -->
                <div class="relative grid grid-cols-7 py-1 border-b" :style="{ borderColor: L.borderSubtle, minHeight: '44px' }">
                  <div v-for="d in 7" :key="d"
                       class="border-r last:border-r-0 h-full"
                       :style="{ borderColor: L.borderSubtle }" />
                  <!-- Bar sits inside col 1–3 -->
                  <div class="absolute inset-y-1 flex items-stretch"
                       style="grid-column: 1 / span 3; left: 4px; right: calc((100% / 7 * 4) + 4px);">
                    <div class="flex items-center w-full rounded-full px-3 gap-2"
                         :style="{ background: L.accents.mint.soft, minHeight: '32px' }">
                      <!-- Fork icon (inline SVG, Heroicon style) -->
                      <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" :style="{ color: L.accents.mint.bold }">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8.25v-1.5m0 1.5c-1.355 0-2.697.056-4.024.166C6.845 8.51 6 9.473 6 10.608v2.513m6-4.871c1.355 0 2.697.056 4.024.166C17.155 8.51 18 9.473 18 10.608v2.513M15 21H9m6 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.872a2.25 2.25 0 0 0-2.006 1.23L9.75 21M15 21h-.75M9 21H9" />
                      </svg>
                      <span class="text-[13px] font-medium leading-none truncate flex-1"
                            :style="{ color: L.accents.mint.bold }">
                        Leftover stir-fry · Mon→Wed
                      </span>
                      <!-- Drag handle (2×2 dot) -->
                      <div class="flex-shrink-0 flex flex-col gap-[3px] cursor-grab opacity-60"
                           style="padding: 2px;">
                        <div class="flex gap-[3px]">
                          <span class="w-[3px] h-[3px] rounded-full" :style="{ background: L.accents.mint.bold }" />
                          <span class="w-[3px] h-[3px] rounded-full" :style="{ background: L.accents.mint.bold }" />
                        </div>
                        <div class="flex gap-[3px]">
                          <span class="w-[3px] h-[3px] rounded-full" :style="{ background: L.accents.mint.bold }" />
                          <span class="w-[3px] h-[3px] rounded-full" :style="{ background: L.accents.mint.bold }" />
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Row 2: Auction — Movie night pass · Fri→Sun (cols 5–7) -->
                <div class="relative grid grid-cols-7 py-1 border-b" :style="{ borderColor: L.borderSubtle, minHeight: '44px' }">
                  <div v-for="d in 7" :key="d"
                       class="border-r last:border-r-0 h-full"
                       :style="{ borderColor: L.borderSubtle }" />
                  <div class="absolute inset-y-1 flex items-stretch"
                       style="left: calc(100% / 7 * 4 + 4px); right: 4px;">
                    <div class="flex items-center w-full rounded-full px-3 gap-2"
                         :style="{ background: L.accents.sun.soft, minHeight: '32px' }">
                      <span class="text-[13px] font-medium leading-none truncate flex-1"
                            :style="{ color: L.accents.sun.bold }">
                        Movie night pass · Fri→Sun
                      </span>
                      <!-- Bidder avatars -->
                      <div class="flex items-center flex-shrink-0">
                        <span
                          v-for="(av, i) in BIDDER_AVATARS"
                          :key="i"
                          class="inline-flex items-center justify-center rounded-full text-[9px] font-bold flex-shrink-0"
                          :style="{
                            width: '20px', height: '20px',
                            background: av.color, color: '#FFFFFF',
                            marginLeft: i === 0 ? '0' : '-6px',
                            boxShadow: `0 0 0 1.5px ${L.accents.sun.soft}`,
                            zIndex: 3 - i,
                          }"
                        >{{ av.initials }}</span>
                      </div>
                      <!-- 3 bids chip -->
                      <span class="flex-shrink-0 text-[10px] font-semibold px-1.5 py-0.5 rounded-full"
                            :style="{ background: L.accents.sun.bold, color: L.inkInverse }">
                        3 bids
                      </span>
                      <!-- Drag handle -->
                      <div class="flex-shrink-0 flex flex-col gap-[3px] cursor-grab opacity-60" style="padding: 2px;">
                        <div class="flex gap-[3px]">
                          <span class="w-[3px] h-[3px] rounded-full" :style="{ background: L.accents.sun.bold }" />
                          <span class="w-[3px] h-[3px] rounded-full" :style="{ background: L.accents.sun.bold }" />
                        </div>
                        <div class="flex gap-[3px]">
                          <span class="w-[3px] h-[3px] rounded-full" :style="{ background: L.accents.sun.bold }" />
                          <span class="w-[3px] h-[3px] rounded-full" :style="{ background: L.accents.sun.bold }" />
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Row 3: Vacation — Emma & Greg · Spring break (full 7 cols) -->
                <div class="relative grid grid-cols-7 py-1 border-b" :style="{ borderColor: L.borderSubtle, minHeight: '44px' }">
                  <div v-for="d in 7" :key="d"
                       class="border-r last:border-r-0 h-full"
                       :style="{ borderColor: L.borderSubtle }" />
                  <div class="absolute inset-y-1 flex items-stretch"
                       style="left: 4px; right: 4px;">
                    <div class="flex items-center w-full rounded-full px-3 gap-2"
                         :style="{ background: L.accents.lavender.soft, minHeight: '32px' }">
                      <span class="text-[13px] font-medium leading-none truncate flex-1"
                            :style="{ color: L.accents.lavender.bold }">
                        Emma &amp; Greg · Spring break
                      </span>
                      <div class="flex items-center flex-shrink-0">
                        <span
                          v-for="(av, i) in VACATION_AVATARS"
                          :key="i"
                          class="inline-flex items-center justify-center rounded-full text-[9px] font-bold flex-shrink-0"
                          :style="{
                            width: '20px', height: '20px',
                            background: av.color, color: '#FFFFFF',
                            marginLeft: i === 0 ? '0' : '-6px',
                            boxShadow: `0 0 0 1.5px ${L.accents.lavender.soft}`,
                            zIndex: 2 - i,
                          }"
                        >{{ av.initials }}</span>
                      </div>
                      <!-- Drag handle -->
                      <div class="flex-shrink-0 flex flex-col gap-[3px] cursor-grab opacity-60" style="padding: 2px;">
                        <div class="flex gap-[3px]">
                          <span class="w-[3px] h-[3px] rounded-full" :style="{ background: L.accents.lavender.bold }" />
                          <span class="w-[3px] h-[3px] rounded-full" :style="{ background: L.accents.lavender.bold }" />
                        </div>
                        <div class="flex gap-[3px]">
                          <span class="w-[3px] h-[3px] rounded-full" :style="{ background: L.accents.lavender.bold }" />
                          <span class="w-[3px] h-[3px] rounded-full" :style="{ background: L.accents.lavender.bold }" />
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Row 4: Single day — Dentist · Tue (col 2) -->
                <div class="relative grid grid-cols-7 py-1" :style="{ minHeight: '44px' }">
                  <div v-for="d in 7" :key="d"
                       class="border-r last:border-r-0 h-full"
                       :style="{ borderColor: L.borderSubtle }" />
                  <div class="absolute inset-y-1 flex items-stretch"
                       style="left: calc(100% / 7 * 1 + 4px); right: calc(100% / 7 * 5 + 4px);">
                    <div class="flex items-center w-full rounded-full px-3 gap-2"
                         :style="{ background: L.accents.peach.soft, minHeight: '32px' }">
                      <span class="text-[13px] font-medium leading-none truncate flex-1"
                            :style="{ color: L.accents.peach.bold }">
                        Dentist · Tue
                      </span>
                    </div>
                  </div>
                </div>

              </div><!-- /track -->
            </div><!-- /space-y-3 -->

            <p class="text-[11px]" :style="{ color: L.inkTertiary }">
              Grid-column spans: mint (cols 1–3), sun (cols 5–7), lavender (full 7), peach (col 2 only).
              Bars use <code class="font-mono px-1 rounded" :style="{ background: L.surfaceSunken }">left/right %</code> anchored to column offsets.
              Drag handle = 2×2 dot grid at ink-tertiary opacity-0.6.
            </p>

            <!-- Mobile 375px mock -->
            <div class="space-y-2">
              <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Mobile (375px mock)</p>
              <div class="max-w-[375px] rounded-xl border overflow-hidden" :style="{ borderColor: L.borderSubtle, background: L.surfaceSunken }">
                <!-- Day header -->
                <div class="grid grid-cols-7 border-b" :style="{ borderColor: L.borderSubtle }">
                  <div v-for="(day, i) in DAYS" :key="i"
                       class="text-center text-[9px] font-semibold uppercase py-1"
                       :style="{ color: L.inkTertiary, borderRight: i < 6 ? `1px solid ${L.borderSubtle}` : 'none' }">
                    {{ day.slice(0,1) }}
                  </div>
                </div>
                <!-- Meal plan row — truncates label -->
                <div class="relative grid grid-cols-7 py-1" :style="{ minHeight: '36px' }">
                  <div v-for="d in 7" :key="d" class="border-r last:border-r-0 h-full"
                       :style="{ borderColor: L.borderSubtle }" />
                  <div class="absolute inset-y-1" style="left: 3px; right: calc(100% / 7 * 4 + 3px);">
                    <div class="flex items-center w-full h-full rounded-full px-2 gap-1"
                         :style="{ background: L.accents.mint.soft }">
                      <span class="text-[11px] font-medium leading-none truncate"
                            :style="{ color: L.accents.mint.bold }">
                        Leftover stir-fry
                      </span>
                    </div>
                  </div>
                </div>
                <!-- Auction row -->
                <div class="relative grid grid-cols-7 py-1" :style="{ minHeight: '36px' }">
                  <div v-for="d in 7" :key="d" class="border-r last:border-r-0 h-full"
                       :style="{ borderColor: L.borderSubtle }" />
                  <div class="absolute inset-y-1" style="left: calc(100% / 7 * 4 + 3px); right: 3px;">
                    <div class="flex items-center w-full h-full rounded-full px-2 gap-1"
                         :style="{ background: L.accents.sun.soft }">
                      <span class="text-[11px] font-medium leading-none truncate flex-1"
                            :style="{ color: L.accents.sun.bold }">
                        Movie night
                      </span>
                      <span class="flex-shrink-0 text-[9px] font-semibold px-1 py-0.5 rounded-full"
                            :style="{ background: L.accents.sun.bold, color: L.inkInverse }">3</span>
                    </div>
                  </div>
                </div>
              </div>
              <p class="text-[11px]" :style="{ color: L.inkTertiary }">
                At 375px labels truncate with ellipsis, avatar stacks collapse to a count chip, drag handles are hidden.
              </p>
            </div>

          </div><!-- /light -->

          <!-- ── DARK ── -->
          <div class="rounded-2xl border p-6 space-y-8"
               :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode · Desktop</p>

            <div class="space-y-3">
              <div class="grid grid-cols-7">
                <div v-for="(day, i) in DAYS" :key="i"
                     class="text-center text-[10px] font-semibold uppercase tracking-widest pb-1"
                     :style="{ color: D.inkTertiary, borderRight: i < 6 ? `1px solid ${D.borderSubtle}` : 'none' }">
                  {{ day }}
                </div>
              </div>

              <div class="rounded-xl overflow-hidden border" :style="{ borderColor: D.borderSubtle, background: D.surfaceSunken }">

                <!-- Meal plan -->
                <div class="relative grid grid-cols-7 py-1 border-b" :style="{ borderColor: D.borderSubtle, minHeight: '44px' }">
                  <div v-for="d in 7" :key="d" class="border-r last:border-r-0 h-full" :style="{ borderColor: D.borderSubtle }" />
                  <div class="absolute inset-y-1 flex items-stretch" style="left: 4px; right: calc(100% / 7 * 4 + 4px);">
                    <div class="flex items-center w-full rounded-full px-3 gap-2" :style="{ background: D.accents.mint.soft, minHeight: '32px' }">
                      <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" :style="{ color: D.accents.mint.bold }">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8.25v-1.5m0 1.5c-1.355 0-2.697.056-4.024.166C6.845 8.51 6 9.473 6 10.608v2.513m6-4.871c1.355 0 2.697.056 4.024.166C17.155 8.51 18 9.473 18 10.608v2.513M15 21H9m6 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.872a2.25 2.25 0 0 0-2.006 1.23L9.75 21M15 21h-.75M9 21H9" />
                      </svg>
                      <span class="text-[13px] font-medium leading-none truncate flex-1" :style="{ color: D.accents.mint.bold }">
                        Leftover stir-fry · Mon→Wed
                      </span>
                      <div class="flex-shrink-0 flex flex-col gap-[3px] cursor-grab" style="padding: 2px; opacity: 0.6;">
                        <div class="flex gap-[3px]">
                          <span class="w-[3px] h-[3px] rounded-full" :style="{ background: D.accents.mint.bold }" />
                          <span class="w-[3px] h-[3px] rounded-full" :style="{ background: D.accents.mint.bold }" />
                        </div>
                        <div class="flex gap-[3px]">
                          <span class="w-[3px] h-[3px] rounded-full" :style="{ background: D.accents.mint.bold }" />
                          <span class="w-[3px] h-[3px] rounded-full" :style="{ background: D.accents.mint.bold }" />
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Auction -->
                <div class="relative grid grid-cols-7 py-1 border-b" :style="{ borderColor: D.borderSubtle, minHeight: '44px' }">
                  <div v-for="d in 7" :key="d" class="border-r last:border-r-0 h-full" :style="{ borderColor: D.borderSubtle }" />
                  <div class="absolute inset-y-1 flex items-stretch" style="left: calc(100% / 7 * 4 + 4px); right: 4px;">
                    <div class="flex items-center w-full rounded-full px-3 gap-2" :style="{ background: D.accents.sun.soft, minHeight: '32px' }">
                      <span class="text-[13px] font-medium leading-none truncate flex-1" :style="{ color: D.accents.sun.bold }">
                        Movie night pass · Fri→Sun
                      </span>
                      <div class="flex items-center flex-shrink-0">
                        <span v-for="(av, i) in BIDDER_AVATARS" :key="i"
                              class="inline-flex items-center justify-center rounded-full text-[9px] font-bold flex-shrink-0"
                              :style="{ width: '20px', height: '20px', background: av.color, color: '#FFFFFF', marginLeft: i === 0 ? '0' : '-6px', boxShadow: `0 0 0 1.5px ${D.accents.sun.soft}`, zIndex: 3 - i }">
                          {{ av.initials }}
                        </span>
                      </div>
                      <span class="flex-shrink-0 text-[10px] font-semibold px-1.5 py-0.5 rounded-full"
                            :style="{ background: D.accents.sun.bold, color: D.inkInverse }">
                        3 bids
                      </span>
                      <div class="flex-shrink-0 flex flex-col gap-[3px] cursor-grab" style="padding: 2px; opacity: 0.6;">
                        <div class="flex gap-[3px]">
                          <span class="w-[3px] h-[3px] rounded-full" :style="{ background: D.accents.sun.bold }" />
                          <span class="w-[3px] h-[3px] rounded-full" :style="{ background: D.accents.sun.bold }" />
                        </div>
                        <div class="flex gap-[3px]">
                          <span class="w-[3px] h-[3px] rounded-full" :style="{ background: D.accents.sun.bold }" />
                          <span class="w-[3px] h-[3px] rounded-full" :style="{ background: D.accents.sun.bold }" />
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Vacation -->
                <div class="relative grid grid-cols-7 py-1 border-b" :style="{ borderColor: D.borderSubtle, minHeight: '44px' }">
                  <div v-for="d in 7" :key="d" class="border-r last:border-r-0 h-full" :style="{ borderColor: D.borderSubtle }" />
                  <div class="absolute inset-y-1 flex items-stretch" style="left: 4px; right: 4px;">
                    <div class="flex items-center w-full rounded-full px-3 gap-2" :style="{ background: D.accents.lavender.soft, minHeight: '32px' }">
                      <span class="text-[13px] font-medium leading-none truncate flex-1" :style="{ color: D.accents.lavender.bold }">
                        Emma &amp; Greg · Spring break
                      </span>
                      <div class="flex items-center flex-shrink-0">
                        <span v-for="(av, i) in VACATION_AVATARS" :key="i"
                              class="inline-flex items-center justify-center rounded-full text-[9px] font-bold flex-shrink-0"
                              :style="{ width: '20px', height: '20px', background: av.color, color: '#FFFFFF', marginLeft: i === 0 ? '0' : '-6px', boxShadow: `0 0 0 1.5px ${D.accents.lavender.soft}`, zIndex: 2 - i }">
                          {{ av.initials }}
                        </span>
                      </div>
                      <div class="flex-shrink-0 flex flex-col gap-[3px] cursor-grab" style="padding: 2px; opacity: 0.6;">
                        <div class="flex gap-[3px]">
                          <span class="w-[3px] h-[3px] rounded-full" :style="{ background: D.accents.lavender.bold }" />
                          <span class="w-[3px] h-[3px] rounded-full" :style="{ background: D.accents.lavender.bold }" />
                        </div>
                        <div class="flex gap-[3px]">
                          <span class="w-[3px] h-[3px] rounded-full" :style="{ background: D.accents.lavender.bold }" />
                          <span class="w-[3px] h-[3px] rounded-full" :style="{ background: D.accents.lavender.bold }" />
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Dentist single day -->
                <div class="relative grid grid-cols-7 py-1" :style="{ minHeight: '44px' }">
                  <div v-for="d in 7" :key="d" class="border-r last:border-r-0 h-full" :style="{ borderColor: D.borderSubtle }" />
                  <div class="absolute inset-y-1 flex items-stretch"
                       style="left: calc(100% / 7 * 1 + 4px); right: calc(100% / 7 * 5 + 4px);">
                    <div class="flex items-center w-full rounded-full px-3" :style="{ background: D.accents.peach.soft, minHeight: '32px' }">
                      <span class="text-[13px] font-medium leading-none truncate" :style="{ color: D.accents.peach.bold }">
                        Dentist · Tue
                      </span>
                    </div>
                  </div>
                </div>

              </div>
            </div>

            <p class="text-[11px]" :style="{ color: D.inkTertiary }">
              Dark: accent-soft fills shift to dark-tinted variants (e.g. lavender-soft = #302A48).
              Accent-bold text stays high-contrast against those dark fills. Ring on avatars matches pill bg.
            </p>
          </div>

        </div>
      </VariantFrame>
      <p class="mt-3 text-sm px-1" :style="{ color: L.inkSecondary }">
        Variant A is the clearest at a glance — soft fill keeps the track from feeling heavy while bold text
        maintains legibility. Best default for most timeline contexts.
      </p>
    </section>


    <!-- ══════════════════════════════════════════════════════════════
         VARIANT B — Outlined soft-fill pill
         Same as A but with a 1.5px accent-bold border.
         Stronger edge definition for stacked rows.
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="B" caption="Outlined soft-fill pill — 1.5px accent-bold border adds edge definition when rows stack vertically">
        <div class="w-full space-y-10">

          <!-- ── LIGHT ── -->
          <div class="rounded-2xl border p-6 space-y-8"
               :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode · Desktop</p>

            <div class="space-y-3">
              <div class="grid grid-cols-7">
                <div v-for="(day, i) in DAYS" :key="i"
                     class="text-center text-[10px] font-semibold uppercase tracking-widest pb-1"
                     :style="{ color: L.inkTertiary, borderRight: i < 6 ? `1px solid ${L.borderSubtle}` : 'none' }">
                  {{ day }}
                </div>
              </div>

              <div class="rounded-xl overflow-hidden border" :style="{ borderColor: L.borderSubtle, background: L.surfaceSunken }">

                <!-- Meal plan -->
                <div class="relative grid grid-cols-7 py-1 border-b" :style="{ borderColor: L.borderSubtle, minHeight: '44px' }">
                  <div v-for="d in 7" :key="d" class="border-r last:border-r-0 h-full" :style="{ borderColor: L.borderSubtle }" />
                  <div class="absolute inset-y-1 flex items-stretch" style="left: 4px; right: calc(100% / 7 * 4 + 4px);">
                    <div class="flex items-center w-full rounded-full px-3 gap-2"
                         :style="{ background: L.accents.mint.soft, border: `1.5px solid ${L.accents.mint.bold}`, minHeight: '32px' }">
                      <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" :style="{ color: L.accents.mint.bold }">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8.25v-1.5m0 1.5c-1.355 0-2.697.056-4.024.166C6.845 8.51 6 9.473 6 10.608v2.513m6-4.871c1.355 0 2.697.056 4.024.166C17.155 8.51 18 9.473 18 10.608v2.513M15 21H9m6 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.872a2.25 2.25 0 0 0-2.006 1.23L9.75 21M15 21h-.75M9 21H9" />
                      </svg>
                      <span class="text-[13px] font-medium leading-none truncate flex-1" :style="{ color: L.accents.mint.bold }">
                        Leftover stir-fry · Mon→Wed
                      </span>
                      <div class="flex-shrink-0 flex flex-col gap-[3px] cursor-grab" style="padding: 2px; opacity: 0.6;">
                        <div class="flex gap-[3px]">
                          <span class="w-[3px] h-[3px] rounded-full" :style="{ background: L.accents.mint.bold }" />
                          <span class="w-[3px] h-[3px] rounded-full" :style="{ background: L.accents.mint.bold }" />
                        </div>
                        <div class="flex gap-[3px]">
                          <span class="w-[3px] h-[3px] rounded-full" :style="{ background: L.accents.mint.bold }" />
                          <span class="w-[3px] h-[3px] rounded-full" :style="{ background: L.accents.mint.bold }" />
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Auction -->
                <div class="relative grid grid-cols-7 py-1 border-b" :style="{ borderColor: L.borderSubtle, minHeight: '44px' }">
                  <div v-for="d in 7" :key="d" class="border-r last:border-r-0 h-full" :style="{ borderColor: L.borderSubtle }" />
                  <div class="absolute inset-y-1 flex items-stretch" style="left: calc(100% / 7 * 4 + 4px); right: 4px;">
                    <div class="flex items-center w-full rounded-full px-3 gap-2"
                         :style="{ background: L.accents.sun.soft, border: `1.5px solid ${L.accents.sun.bold}`, minHeight: '32px' }">
                      <span class="text-[13px] font-medium leading-none truncate flex-1" :style="{ color: L.accents.sun.bold }">
                        Movie night pass · Fri→Sun
                      </span>
                      <div class="flex items-center flex-shrink-0">
                        <span v-for="(av, i) in BIDDER_AVATARS" :key="i"
                              class="inline-flex items-center justify-center rounded-full text-[9px] font-bold flex-shrink-0"
                              :style="{ width: '20px', height: '20px', background: av.color, color: '#FFFFFF', marginLeft: i === 0 ? '0' : '-6px', boxShadow: `0 0 0 1.5px ${L.accents.sun.soft}`, zIndex: 3 - i }">
                          {{ av.initials }}
                        </span>
                      </div>
                      <span class="flex-shrink-0 text-[10px] font-semibold px-1.5 py-0.5 rounded-full"
                            :style="{ background: L.accents.sun.bold, color: L.inkInverse }">3 bids</span>
                      <div class="flex-shrink-0 flex flex-col gap-[3px] cursor-grab" style="padding: 2px; opacity: 0.6;">
                        <div class="flex gap-[3px]">
                          <span class="w-[3px] h-[3px] rounded-full" :style="{ background: L.accents.sun.bold }" />
                          <span class="w-[3px] h-[3px] rounded-full" :style="{ background: L.accents.sun.bold }" />
                        </div>
                        <div class="flex gap-[3px]">
                          <span class="w-[3px] h-[3px] rounded-full" :style="{ background: L.accents.sun.bold }" />
                          <span class="w-[3px] h-[3px] rounded-full" :style="{ background: L.accents.sun.bold }" />
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Vacation -->
                <div class="relative grid grid-cols-7 py-1 border-b" :style="{ borderColor: L.borderSubtle, minHeight: '44px' }">
                  <div v-for="d in 7" :key="d" class="border-r last:border-r-0 h-full" :style="{ borderColor: L.borderSubtle }" />
                  <div class="absolute inset-y-1 flex items-stretch" style="left: 4px; right: 4px;">
                    <div class="flex items-center w-full rounded-full px-3 gap-2"
                         :style="{ background: L.accents.lavender.soft, border: `1.5px solid ${L.accents.lavender.bold}`, minHeight: '32px' }">
                      <span class="text-[13px] font-medium leading-none truncate flex-1" :style="{ color: L.accents.lavender.bold }">
                        Emma &amp; Greg · Spring break
                      </span>
                      <div class="flex items-center flex-shrink-0">
                        <span v-for="(av, i) in VACATION_AVATARS" :key="i"
                              class="inline-flex items-center justify-center rounded-full text-[9px] font-bold flex-shrink-0"
                              :style="{ width: '20px', height: '20px', background: av.color, color: '#FFFFFF', marginLeft: i === 0 ? '0' : '-6px', boxShadow: `0 0 0 1.5px ${L.accents.lavender.soft}`, zIndex: 2 - i }">
                          {{ av.initials }}
                        </span>
                      </div>
                      <div class="flex-shrink-0 flex flex-col gap-[3px] cursor-grab" style="padding: 2px; opacity: 0.6;">
                        <div class="flex gap-[3px]">
                          <span class="w-[3px] h-[3px] rounded-full" :style="{ background: L.accents.lavender.bold }" />
                          <span class="w-[3px] h-[3px] rounded-full" :style="{ background: L.accents.lavender.bold }" />
                        </div>
                        <div class="flex gap-[3px]">
                          <span class="w-[3px] h-[3px] rounded-full" :style="{ background: L.accents.lavender.bold }" />
                          <span class="w-[3px] h-[3px] rounded-full" :style="{ background: L.accents.lavender.bold }" />
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Dentist -->
                <div class="relative grid grid-cols-7 py-1" :style="{ minHeight: '44px' }">
                  <div v-for="d in 7" :key="d" class="border-r last:border-r-0 h-full" :style="{ borderColor: L.borderSubtle }" />
                  <div class="absolute inset-y-1 flex items-stretch"
                       style="left: calc(100% / 7 * 1 + 4px); right: calc(100% / 7 * 5 + 4px);">
                    <div class="flex items-center w-full rounded-full px-3"
                         :style="{ background: L.accents.peach.soft, border: `1.5px solid ${L.accents.peach.bold}`, minHeight: '32px' }">
                      <span class="text-[13px] font-medium leading-none truncate" :style="{ color: L.accents.peach.bold }">
                        Dentist · Tue
                      </span>
                    </div>
                  </div>
                </div>

              </div>
            </div>

            <p class="text-[11px]" :style="{ color: L.inkTertiary }">
              The 1.5px border sharpens each row's bounding edge — helpful when rows sit directly adjacent.
              The border is accent-bold at full opacity, same color as the text.
            </p>

            <!-- Mobile -->
            <div class="space-y-2">
              <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Mobile (375px mock)</p>
              <div class="max-w-[375px] rounded-xl border overflow-hidden" :style="{ borderColor: L.borderSubtle, background: L.surfaceSunken }">
                <div class="grid grid-cols-7 border-b" :style="{ borderColor: L.borderSubtle }">
                  <div v-for="(day, i) in DAYS" :key="i"
                       class="text-center text-[9px] font-semibold uppercase py-1"
                       :style="{ color: L.inkTertiary, borderRight: i < 6 ? `1px solid ${L.borderSubtle}` : 'none' }">
                    {{ day.slice(0,1) }}
                  </div>
                </div>
                <div class="relative grid grid-cols-7 py-1 border-b" :style="{ borderColor: L.borderSubtle, minHeight: '36px' }">
                  <div v-for="d in 7" :key="d" class="border-r last:border-r-0 h-full" :style="{ borderColor: L.borderSubtle }" />
                  <div class="absolute inset-y-1" style="left: 3px; right: calc(100% / 7 * 4 + 3px);">
                    <div class="flex items-center w-full h-full rounded-full px-2"
                         :style="{ background: L.accents.mint.soft, border: `1.5px solid ${L.accents.mint.bold}` }">
                      <span class="text-[11px] font-medium leading-none truncate" :style="{ color: L.accents.mint.bold }">
                        Leftover stir-fry
                      </span>
                    </div>
                  </div>
                </div>
                <div class="relative grid grid-cols-7 py-1" :style="{ minHeight: '36px' }">
                  <div v-for="d in 7" :key="d" class="border-r last:border-r-0 h-full" :style="{ borderColor: L.borderSubtle }" />
                  <div class="absolute inset-y-1" style="left: calc(100% / 7 * 4 + 3px); right: 3px;">
                    <div class="flex items-center w-full h-full rounded-full px-2 gap-1"
                         :style="{ background: L.accents.sun.soft, border: `1.5px solid ${L.accents.sun.bold}` }">
                      <span class="text-[11px] font-medium leading-none truncate flex-1" :style="{ color: L.accents.sun.bold }">
                        Movie night
                      </span>
                      <span class="flex-shrink-0 text-[9px] font-semibold px-1 py-0.5 rounded-full"
                            :style="{ background: L.accents.sun.bold, color: L.inkInverse }">3</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

          </div><!-- /light -->

          <!-- ── DARK ── -->
          <div class="rounded-2xl border p-6 space-y-8"
               :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode · Desktop</p>

            <div class="space-y-3">
              <div class="grid grid-cols-7">
                <div v-for="(day, i) in DAYS" :key="i"
                     class="text-center text-[10px] font-semibold uppercase tracking-widest pb-1"
                     :style="{ color: D.inkTertiary, borderRight: i < 6 ? `1px solid ${D.borderSubtle}` : 'none' }">
                  {{ day }}
                </div>
              </div>

              <div class="rounded-xl overflow-hidden border" :style="{ borderColor: D.borderSubtle, background: D.surfaceSunken }">

                <!-- Meal plan -->
                <div class="relative grid grid-cols-7 py-1 border-b" :style="{ borderColor: D.borderSubtle, minHeight: '44px' }">
                  <div v-for="d in 7" :key="d" class="border-r last:border-r-0 h-full" :style="{ borderColor: D.borderSubtle }" />
                  <div class="absolute inset-y-1 flex items-stretch" style="left: 4px; right: calc(100% / 7 * 4 + 4px);">
                    <div class="flex items-center w-full rounded-full px-3 gap-2"
                         :style="{ background: D.accents.mint.soft, border: `1.5px solid ${D.accents.mint.bold}`, minHeight: '32px' }">
                      <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" :style="{ color: D.accents.mint.bold }">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8.25v-1.5m0 1.5c-1.355 0-2.697.056-4.024.166C6.845 8.51 6 9.473 6 10.608v2.513m6-4.871c1.355 0 2.697.056 4.024.166C17.155 8.51 18 9.473 18 10.608v2.513M15 21H9m6 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.872a2.25 2.25 0 0 0-2.006 1.23L9.75 21M15 21h-.75M9 21H9" />
                      </svg>
                      <span class="text-[13px] font-medium leading-none truncate flex-1" :style="{ color: D.accents.mint.bold }">
                        Leftover stir-fry · Mon→Wed
                      </span>
                      <div class="flex-shrink-0 flex flex-col gap-[3px] cursor-grab" style="padding: 2px; opacity: 0.6;">
                        <div class="flex gap-[3px]"><span class="w-[3px] h-[3px] rounded-full" :style="{ background: D.accents.mint.bold }" /><span class="w-[3px] h-[3px] rounded-full" :style="{ background: D.accents.mint.bold }" /></div>
                        <div class="flex gap-[3px]"><span class="w-[3px] h-[3px] rounded-full" :style="{ background: D.accents.mint.bold }" /><span class="w-[3px] h-[3px] rounded-full" :style="{ background: D.accents.mint.bold }" /></div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Auction -->
                <div class="relative grid grid-cols-7 py-1 border-b" :style="{ borderColor: D.borderSubtle, minHeight: '44px' }">
                  <div v-for="d in 7" :key="d" class="border-r last:border-r-0 h-full" :style="{ borderColor: D.borderSubtle }" />
                  <div class="absolute inset-y-1 flex items-stretch" style="left: calc(100% / 7 * 4 + 4px); right: 4px;">
                    <div class="flex items-center w-full rounded-full px-3 gap-2"
                         :style="{ background: D.accents.sun.soft, border: `1.5px solid ${D.accents.sun.bold}`, minHeight: '32px' }">
                      <span class="text-[13px] font-medium leading-none truncate flex-1" :style="{ color: D.accents.sun.bold }">
                        Movie night pass · Fri→Sun
                      </span>
                      <div class="flex items-center flex-shrink-0">
                        <span v-for="(av, i) in BIDDER_AVATARS" :key="i"
                              class="inline-flex items-center justify-center rounded-full text-[9px] font-bold flex-shrink-0"
                              :style="{ width: '20px', height: '20px', background: av.color, color: '#FFFFFF', marginLeft: i === 0 ? '0' : '-6px', boxShadow: `0 0 0 1.5px ${D.accents.sun.soft}`, zIndex: 3 - i }">
                          {{ av.initials }}
                        </span>
                      </div>
                      <span class="flex-shrink-0 text-[10px] font-semibold px-1.5 py-0.5 rounded-full"
                            :style="{ background: D.accents.sun.bold, color: D.inkInverse }">3 bids</span>
                      <div class="flex-shrink-0 flex flex-col gap-[3px] cursor-grab" style="padding: 2px; opacity: 0.6;">
                        <div class="flex gap-[3px]"><span class="w-[3px] h-[3px] rounded-full" :style="{ background: D.accents.sun.bold }" /><span class="w-[3px] h-[3px] rounded-full" :style="{ background: D.accents.sun.bold }" /></div>
                        <div class="flex gap-[3px]"><span class="w-[3px] h-[3px] rounded-full" :style="{ background: D.accents.sun.bold }" /><span class="w-[3px] h-[3px] rounded-full" :style="{ background: D.accents.sun.bold }" /></div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Vacation -->
                <div class="relative grid grid-cols-7 py-1 border-b" :style="{ borderColor: D.borderSubtle, minHeight: '44px' }">
                  <div v-for="d in 7" :key="d" class="border-r last:border-r-0 h-full" :style="{ borderColor: D.borderSubtle }" />
                  <div class="absolute inset-y-1 flex items-stretch" style="left: 4px; right: 4px;">
                    <div class="flex items-center w-full rounded-full px-3 gap-2"
                         :style="{ background: D.accents.lavender.soft, border: `1.5px solid ${D.accents.lavender.bold}`, minHeight: '32px' }">
                      <span class="text-[13px] font-medium leading-none truncate flex-1" :style="{ color: D.accents.lavender.bold }">
                        Emma &amp; Greg · Spring break
                      </span>
                      <div class="flex items-center flex-shrink-0">
                        <span v-for="(av, i) in VACATION_AVATARS" :key="i"
                              class="inline-flex items-center justify-center rounded-full text-[9px] font-bold flex-shrink-0"
                              :style="{ width: '20px', height: '20px', background: av.color, color: '#FFFFFF', marginLeft: i === 0 ? '0' : '-6px', boxShadow: `0 0 0 1.5px ${D.accents.lavender.soft}`, zIndex: 2 - i }">
                          {{ av.initials }}
                        </span>
                      </div>
                      <div class="flex-shrink-0 flex flex-col gap-[3px] cursor-grab" style="padding: 2px; opacity: 0.6;">
                        <div class="flex gap-[3px]"><span class="w-[3px] h-[3px] rounded-full" :style="{ background: D.accents.lavender.bold }" /><span class="w-[3px] h-[3px] rounded-full" :style="{ background: D.accents.lavender.bold }" /></div>
                        <div class="flex gap-[3px]"><span class="w-[3px] h-[3px] rounded-full" :style="{ background: D.accents.lavender.bold }" /><span class="w-[3px] h-[3px] rounded-full" :style="{ background: D.accents.lavender.bold }" /></div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Dentist -->
                <div class="relative grid grid-cols-7 py-1" :style="{ minHeight: '44px' }">
                  <div v-for="d in 7" :key="d" class="border-r last:border-r-0 h-full" :style="{ borderColor: D.borderSubtle }" />
                  <div class="absolute inset-y-1 flex items-stretch"
                       style="left: calc(100% / 7 * 1 + 4px); right: calc(100% / 7 * 5 + 4px);">
                    <div class="flex items-center w-full rounded-full px-3"
                         :style="{ background: D.accents.peach.soft, border: `1.5px solid ${D.accents.peach.bold}`, minHeight: '32px' }">
                      <span class="text-[13px] font-medium leading-none truncate" :style="{ color: D.accents.peach.bold }">
                        Dentist · Tue
                      </span>
                    </div>
                  </div>
                </div>

              </div>
            </div>

            <p class="text-[11px]" :style="{ color: D.inkTertiary }">
              Dark mode: borders are accent-bold at full opacity. The dark-tinted soft fills (#302A48 etc.)
              have enough contrast against the track background (#161513) that the border adds just enough edge.
            </p>
          </div>

        </div>
      </VariantFrame>
      <p class="mt-3 text-sm px-1" :style="{ color: L.inkSecondary }">
        Variant B earns its extra border when rows stack tightly — the crisp 1.5px line prevents adjacent
        rows from visually merging at the track boundary. Otherwise identical to A in information hierarchy.
      </p>
    </section>


    <!-- ══════════════════════════════════════════════════════════════
         VARIANT C — Gradient pill with embedded progress indicator
         Horizontal gradient (accent-soft left → accent-bold 20% right).
         Inner progress fill shows how far into the range we are.
         Great for "in-progress" timeline items.
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="C" caption="Gradient pill with progress — soft→bold gradient · darker inner fill shows elapsed portion · ambient progress feedback">
        <div class="w-full space-y-10">

          <!-- ── LIGHT ── -->
          <div class="rounded-2xl border p-6 space-y-8"
               :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode · Desktop</p>

            <div class="space-y-3">
              <div class="grid grid-cols-7">
                <div v-for="(day, i) in DAYS" :key="i"
                     class="text-center text-[10px] font-semibold uppercase tracking-widest pb-1"
                     :style="{ color: L.inkTertiary, borderRight: i < 6 ? `1px solid ${L.borderSubtle}` : 'none' }">
                  {{ day }}
                </div>
              </div>

              <div class="rounded-xl overflow-hidden border" :style="{ borderColor: L.borderSubtle, background: L.surfaceSunken }">

                <!-- Meal plan — progress at ~50% (1.5 of 3 days elapsed) -->
                <div class="relative grid grid-cols-7 py-1 border-b" :style="{ borderColor: L.borderSubtle, minHeight: '44px' }">
                  <div v-for="d in 7" :key="d" class="border-r last:border-r-0 h-full" :style="{ borderColor: L.borderSubtle }" />
                  <div class="absolute inset-y-1 flex items-stretch rounded-full overflow-hidden"
                       style="left: 4px; right: calc(100% / 7 * 4 + 4px);">
                    <!-- Gradient base -->
                    <div class="relative flex items-center w-full rounded-full overflow-hidden"
                         :style="{ background: `linear-gradient(to right, ${L.accents.mint.soft}, rgba(46,138,98,0.2))`, minHeight: '32px' }">
                      <!-- Progress fill: ~50% -->
                      <div class="absolute left-0 top-0 bottom-0 rounded-l-full"
                           style="width: 50%;"
                           :style="{ background: 'rgba(46,138,98,0.18)' }" />
                      <!-- Content over gradient -->
                      <div class="relative flex items-center w-full px-3 gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" :style="{ color: L.accents.mint.bold }">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M12 8.25v-1.5m0 1.5c-1.355 0-2.697.056-4.024.166C6.845 8.51 6 9.473 6 10.608v2.513m6-4.871c1.355 0 2.697.056 4.024.166C17.155 8.51 18 9.473 18 10.608v2.513M15 21H9m6 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.872a2.25 2.25 0 0 0-2.006 1.23L9.75 21M15 21h-.75M9 21H9" />
                        </svg>
                        <span class="text-[13px] font-medium leading-none truncate flex-1" :style="{ color: L.accents.mint.bold }">
                          Leftover stir-fry · Mon→Wed
                        </span>
                        <div class="flex-shrink-0 flex flex-col gap-[3px] cursor-grab" style="padding: 2px; opacity: 0.6;">
                          <div class="flex gap-[3px]"><span class="w-[3px] h-[3px] rounded-full" :style="{ background: L.accents.mint.bold }" /><span class="w-[3px] h-[3px] rounded-full" :style="{ background: L.accents.mint.bold }" /></div>
                          <div class="flex gap-[3px]"><span class="w-[3px] h-[3px] rounded-full" :style="{ background: L.accents.mint.bold }" /><span class="w-[3px] h-[3px] rounded-full" :style="{ background: L.accents.mint.bold }" /></div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Auction — progress at ~67% (2 of 3 days elapsed) -->
                <div class="relative grid grid-cols-7 py-1 border-b" :style="{ borderColor: L.borderSubtle, minHeight: '44px' }">
                  <div v-for="d in 7" :key="d" class="border-r last:border-r-0 h-full" :style="{ borderColor: L.borderSubtle }" />
                  <div class="absolute inset-y-1 flex items-stretch rounded-full overflow-hidden"
                       style="left: calc(100% / 7 * 4 + 4px); right: 4px;">
                    <div class="relative flex items-center w-full rounded-full overflow-hidden"
                         :style="{ background: `linear-gradient(to right, ${L.accents.sun.soft}, rgba(162,120,12,0.2))`, minHeight: '32px' }">
                      <div class="absolute left-0 top-0 bottom-0 rounded-l-full"
                           style="width: 67%;"
                           :style="{ background: 'rgba(162,120,12,0.18)' }" />
                      <div class="relative flex items-center w-full px-3 gap-2">
                        <span class="text-[13px] font-medium leading-none truncate flex-1" :style="{ color: L.accents.sun.bold }">
                          Movie night pass · Fri→Sun
                        </span>
                        <div class="flex items-center flex-shrink-0">
                          <span v-for="(av, i) in BIDDER_AVATARS" :key="i"
                                class="inline-flex items-center justify-center rounded-full text-[9px] font-bold flex-shrink-0"
                                :style="{ width: '20px', height: '20px', background: av.color, color: '#FFFFFF', marginLeft: i === 0 ? '0' : '-6px', boxShadow: `0 0 0 1.5px ${L.accents.sun.soft}`, zIndex: 3 - i }">
                            {{ av.initials }}
                          </span>
                        </div>
                        <span class="flex-shrink-0 text-[10px] font-semibold px-1.5 py-0.5 rounded-full"
                              :style="{ background: L.accents.sun.bold, color: L.inkInverse }">3 bids</span>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Vacation — not yet started (0% progress) -->
                <div class="relative grid grid-cols-7 py-1 border-b" :style="{ borderColor: L.borderSubtle, minHeight: '44px' }">
                  <div v-for="d in 7" :key="d" class="border-r last:border-r-0 h-full" :style="{ borderColor: L.borderSubtle }" />
                  <div class="absolute inset-y-1 flex items-stretch rounded-full overflow-hidden"
                       style="left: 4px; right: 4px;">
                    <div class="relative flex items-center w-full rounded-full overflow-hidden"
                         :style="{ background: `linear-gradient(to right, ${L.accents.lavender.soft}, rgba(104,86,178,0.2))`, minHeight: '32px' }">
                      <div class="relative flex items-center w-full px-3 gap-2">
                        <span class="text-[13px] font-medium leading-none truncate flex-1" :style="{ color: L.accents.lavender.bold }">
                          Emma &amp; Greg · Spring break
                        </span>
                        <div class="flex items-center flex-shrink-0">
                          <span v-for="(av, i) in VACATION_AVATARS" :key="i"
                                class="inline-flex items-center justify-center rounded-full text-[9px] font-bold flex-shrink-0"
                                :style="{ width: '20px', height: '20px', background: av.color, color: '#FFFFFF', marginLeft: i === 0 ? '0' : '-6px', boxShadow: `0 0 0 1.5px ${L.accents.lavender.soft}`, zIndex: 2 - i }">
                            {{ av.initials }}
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Dentist — completed (100% progress) -->
                <div class="relative grid grid-cols-7 py-1" :style="{ minHeight: '44px' }">
                  <div v-for="d in 7" :key="d" class="border-r last:border-r-0 h-full" :style="{ borderColor: L.borderSubtle }" />
                  <div class="absolute inset-y-1 flex items-stretch rounded-full overflow-hidden"
                       style="left: calc(100% / 7 * 1 + 4px); right: calc(100% / 7 * 5 + 4px);">
                    <div class="relative flex items-center w-full rounded-full overflow-hidden"
                         :style="{ background: `linear-gradient(to right, ${L.accents.peach.soft}, rgba(186,86,46,0.2))`, minHeight: '32px' }">
                      <div class="absolute left-0 top-0 bottom-0 rounded-full"
                           style="width: 100%;"
                           :style="{ background: 'rgba(186,86,46,0.18)' }" />
                      <div class="relative flex items-center w-full px-3">
                        <span class="text-[13px] font-medium leading-none truncate" :style="{ color: L.accents.peach.bold }">
                          Dentist · Tue
                        </span>
                      </div>
                    </div>
                  </div>
                </div>

              </div>
            </div>

            <p class="text-[11px]" :style="{ color: L.inkTertiary }">
              Progress fill: a slightly darker rgba layer (opacity ~0.18) from the left edge.
              Gradient base: soft color left to 20%-opacity bold right — keeps the pill from looking flat.
              No-progress (vacation) = gradient only. Completed (dentist) = full fill.
            </p>

            <!-- Mobile -->
            <div class="space-y-2">
              <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Mobile (375px mock)</p>
              <div class="max-w-[375px] rounded-xl border overflow-hidden" :style="{ borderColor: L.borderSubtle, background: L.surfaceSunken }">
                <div class="grid grid-cols-7 border-b" :style="{ borderColor: L.borderSubtle }">
                  <div v-for="(day, i) in DAYS" :key="i"
                       class="text-center text-[9px] font-semibold uppercase py-1"
                       :style="{ color: L.inkTertiary, borderRight: i < 6 ? `1px solid ${L.borderSubtle}` : 'none' }">
                    {{ day.slice(0,1) }}
                  </div>
                </div>
                <div class="relative grid grid-cols-7 py-1 border-b" :style="{ borderColor: L.borderSubtle, minHeight: '36px' }">
                  <div v-for="d in 7" :key="d" class="border-r last:border-r-0 h-full" :style="{ borderColor: L.borderSubtle }" />
                  <div class="absolute inset-y-1 rounded-full overflow-hidden" style="left: 3px; right: calc(100% / 7 * 4 + 3px);">
                    <div class="relative flex items-center w-full h-full rounded-full overflow-hidden"
                         :style="{ background: `linear-gradient(to right, ${L.accents.mint.soft}, rgba(46,138,98,0.2))` }">
                      <div class="absolute left-0 top-0 bottom-0 rounded-l-full" style="width: 50%;" :style="{ background: 'rgba(46,138,98,0.18)' }" />
                      <span class="relative text-[11px] font-medium leading-none truncate px-2" :style="{ color: L.accents.mint.bold }">Leftover stir-fry</span>
                    </div>
                  </div>
                </div>
                <div class="relative grid grid-cols-7 py-1" :style="{ minHeight: '36px' }">
                  <div v-for="d in 7" :key="d" class="border-r last:border-r-0 h-full" :style="{ borderColor: L.borderSubtle }" />
                  <div class="absolute inset-y-1 rounded-full overflow-hidden" style="left: calc(100% / 7 * 4 + 3px); right: 3px;">
                    <div class="relative flex items-center w-full h-full rounded-full overflow-hidden"
                         :style="{ background: `linear-gradient(to right, ${L.accents.sun.soft}, rgba(162,120,12,0.2))` }">
                      <div class="absolute left-0 top-0 bottom-0 rounded-l-full" style="width: 67%;" :style="{ background: 'rgba(162,120,12,0.18)' }" />
                      <span class="relative text-[11px] font-medium leading-none truncate flex-1 px-2" :style="{ color: L.accents.sun.bold }">Movie night</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

          </div><!-- /light -->

          <!-- ── DARK ── -->
          <div class="rounded-2xl border p-6 space-y-8"
               :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode · Desktop</p>

            <div class="space-y-3">
              <div class="grid grid-cols-7">
                <div v-for="(day, i) in DAYS" :key="i"
                     class="text-center text-[10px] font-semibold uppercase tracking-widest pb-1"
                     :style="{ color: D.inkTertiary, borderRight: i < 6 ? `1px solid ${D.borderSubtle}` : 'none' }">
                  {{ day }}
                </div>
              </div>

              <div class="rounded-xl overflow-hidden border" :style="{ borderColor: D.borderSubtle, background: D.surfaceSunken }">

                <!-- Meal plan -->
                <div class="relative grid grid-cols-7 py-1 border-b" :style="{ borderColor: D.borderSubtle, minHeight: '44px' }">
                  <div v-for="d in 7" :key="d" class="border-r last:border-r-0 h-full" :style="{ borderColor: D.borderSubtle }" />
                  <div class="absolute inset-y-1 rounded-full overflow-hidden" style="left: 4px; right: calc(100% / 7 * 4 + 4px);">
                    <div class="relative flex items-center w-full h-full rounded-full overflow-hidden"
                         :style="{ background: `linear-gradient(to right, ${D.accents.mint.soft}, rgba(124,214,174,0.15))`, minHeight: '32px' }">
                      <div class="absolute left-0 top-0 bottom-0 rounded-l-full" style="width: 50%;" :style="{ background: 'rgba(124,214,174,0.12)' }" />
                      <div class="relative flex items-center w-full px-3 gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" :style="{ color: D.accents.mint.bold }">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M12 8.25v-1.5m0 1.5c-1.355 0-2.697.056-4.024.166C6.845 8.51 6 9.473 6 10.608v2.513m6-4.871c1.355 0 2.697.056 4.024.166C17.155 8.51 18 9.473 18 10.608v2.513M15 21H9m6 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.872a2.25 2.25 0 0 0-2.006 1.23L9.75 21M15 21h-.75M9 21H9" />
                        </svg>
                        <span class="text-[13px] font-medium leading-none truncate flex-1" :style="{ color: D.accents.mint.bold }">
                          Leftover stir-fry · Mon→Wed
                        </span>
                        <div class="flex-shrink-0 flex flex-col gap-[3px] cursor-grab" style="padding: 2px; opacity: 0.6;">
                          <div class="flex gap-[3px]"><span class="w-[3px] h-[3px] rounded-full" :style="{ background: D.accents.mint.bold }" /><span class="w-[3px] h-[3px] rounded-full" :style="{ background: D.accents.mint.bold }" /></div>
                          <div class="flex gap-[3px]"><span class="w-[3px] h-[3px] rounded-full" :style="{ background: D.accents.mint.bold }" /><span class="w-[3px] h-[3px] rounded-full" :style="{ background: D.accents.mint.bold }" /></div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Auction -->
                <div class="relative grid grid-cols-7 py-1 border-b" :style="{ borderColor: D.borderSubtle, minHeight: '44px' }">
                  <div v-for="d in 7" :key="d" class="border-r last:border-r-0 h-full" :style="{ borderColor: D.borderSubtle }" />
                  <div class="absolute inset-y-1 rounded-full overflow-hidden" style="left: calc(100% / 7 * 4 + 4px); right: 4px;">
                    <div class="relative flex items-center w-full h-full rounded-full overflow-hidden"
                         :style="{ background: `linear-gradient(to right, ${D.accents.sun.soft}, rgba(230,196,82,0.15))`, minHeight: '32px' }">
                      <div class="absolute left-0 top-0 bottom-0 rounded-l-full" style="width: 67%;" :style="{ background: 'rgba(230,196,82,0.12)' }" />
                      <div class="relative flex items-center w-full px-3 gap-2">
                        <span class="text-[13px] font-medium leading-none truncate flex-1" :style="{ color: D.accents.sun.bold }">
                          Movie night pass · Fri→Sun
                        </span>
                        <div class="flex items-center flex-shrink-0">
                          <span v-for="(av, i) in BIDDER_AVATARS" :key="i"
                                class="inline-flex items-center justify-center rounded-full text-[9px] font-bold flex-shrink-0"
                                :style="{ width: '20px', height: '20px', background: av.color, color: '#FFFFFF', marginLeft: i === 0 ? '0' : '-6px', boxShadow: `0 0 0 1.5px ${D.accents.sun.soft}`, zIndex: 3 - i }">
                            {{ av.initials }}
                          </span>
                        </div>
                        <span class="flex-shrink-0 text-[10px] font-semibold px-1.5 py-0.5 rounded-full"
                              :style="{ background: D.accents.sun.bold, color: D.inkInverse }">3 bids</span>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Vacation -->
                <div class="relative grid grid-cols-7 py-1 border-b" :style="{ borderColor: D.borderSubtle, minHeight: '44px' }">
                  <div v-for="d in 7" :key="d" class="border-r last:border-r-0 h-full" :style="{ borderColor: D.borderSubtle }" />
                  <div class="absolute inset-y-1 rounded-full overflow-hidden" style="left: 4px; right: 4px;">
                    <div class="relative flex items-center w-full h-full rounded-full overflow-hidden"
                         :style="{ background: `linear-gradient(to right, ${D.accents.lavender.soft}, rgba(182,168,230,0.15))`, minHeight: '32px' }">
                      <div class="relative flex items-center w-full px-3 gap-2">
                        <span class="text-[13px] font-medium leading-none truncate flex-1" :style="{ color: D.accents.lavender.bold }">
                          Emma &amp; Greg · Spring break
                        </span>
                        <div class="flex items-center flex-shrink-0">
                          <span v-for="(av, i) in VACATION_AVATARS" :key="i"
                                class="inline-flex items-center justify-center rounded-full text-[9px] font-bold flex-shrink-0"
                                :style="{ width: '20px', height: '20px', background: av.color, color: '#FFFFFF', marginLeft: i === 0 ? '0' : '-6px', boxShadow: `0 0 0 1.5px ${D.accents.lavender.soft}`, zIndex: 2 - i }">
                            {{ av.initials }}
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Dentist -->
                <div class="relative grid grid-cols-7 py-1" :style="{ minHeight: '44px' }">
                  <div v-for="d in 7" :key="d" class="border-r last:border-r-0 h-full" :style="{ borderColor: D.borderSubtle }" />
                  <div class="absolute inset-y-1 rounded-full overflow-hidden"
                       style="left: calc(100% / 7 * 1 + 4px); right: calc(100% / 7 * 5 + 4px);">
                    <div class="relative flex items-center w-full h-full rounded-full overflow-hidden"
                         :style="{ background: `linear-gradient(to right, ${D.accents.peach.soft}, rgba(240,168,130,0.15))`, minHeight: '32px' }">
                      <div class="absolute inset-0 rounded-full" :style="{ background: 'rgba(240,168,130,0.12)' }" />
                      <div class="relative flex items-center w-full px-3">
                        <span class="text-[13px] font-medium leading-none truncate" :style="{ color: D.accents.peach.bold }">
                          Dentist · Tue
                        </span>
                      </div>
                    </div>
                  </div>
                </div>

              </div>
            </div>

            <p class="text-[11px]" :style="{ color: D.inkTertiary }">
              Dark gradient: same technique but rgba opacities dialed back (0.15/0.12) so the progress layer
              reads as a subtle depth shift rather than a stark fill against the dark surface.
            </p>
          </div>

        </div>
      </VariantFrame>
      <p class="mt-3 text-sm px-1" :style="{ color: L.inkSecondary }">
        Variant C brings ambient progress awareness — the user can feel how far into a block they are without
        a hard percentage number. Ideal for meal plans currently in progress, active auction countdowns, and
        ongoing events like vacation ranges or training blocks.
      </p>
    </section>


    <!-- ══════════════════════════════════════════════════════════════
         STACKED ROWS — Feed density example (Variant A)
         4 rows at different spans in the same week track.
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="Stacked" caption="Feed-density stacking — 4 rows in the same 7-column track at varied spans (Variant A style)">
        <div class="w-full space-y-8">

          <!-- Light stacked -->
          <div class="rounded-2xl border p-6 space-y-4"
               :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode · stacked rows</p>

            <div class="space-y-2">
              <!-- Day header -->
              <div class="grid grid-cols-7">
                <div v-for="(day, i) in DAYS" :key="i"
                     class="text-center text-[10px] font-semibold uppercase tracking-widest pb-1"
                     :style="{ color: L.inkTertiary, borderRight: i < 6 ? `1px solid ${L.borderSubtle}` : 'none' }">
                  {{ day }}
                </div>
              </div>

              <!-- Track with 4 stacked rows -->
              <div class="rounded-xl overflow-hidden border" :style="{ borderColor: L.borderSubtle, background: L.surfaceSunken }">

                <!-- Row 1: Leftover stir-fry Mon–Wed -->
                <div class="relative grid grid-cols-7 py-1 border-b" :style="{ borderColor: L.borderSubtle, minHeight: '40px' }">
                  <div v-for="d in 7" :key="d" class="border-r last:border-r-0 h-full" :style="{ borderColor: L.borderSubtle }" />
                  <div class="absolute inset-y-1 flex items-stretch" style="left: 4px; right: calc(100% / 7 * 4 + 4px);">
                    <div class="flex items-center w-full rounded-full px-3 gap-2" :style="{ background: L.accents.mint.soft, minHeight: '28px' }">
                      <span class="text-[12px] font-medium leading-none truncate" :style="{ color: L.accents.mint.bold }">Leftover stir-fry · Mon→Wed</span>
                    </div>
                  </div>
                </div>

                <!-- Row 2: Movie night pass Fri–Sun -->
                <div class="relative grid grid-cols-7 py-1 border-b" :style="{ borderColor: L.borderSubtle, minHeight: '40px' }">
                  <div v-for="d in 7" :key="d" class="border-r last:border-r-0 h-full" :style="{ borderColor: L.borderSubtle }" />
                  <div class="absolute inset-y-1 flex items-stretch" style="left: calc(100% / 7 * 4 + 4px); right: 4px;">
                    <div class="flex items-center w-full rounded-full px-3 gap-1" :style="{ background: L.accents.sun.soft, minHeight: '28px' }">
                      <span class="text-[12px] font-medium leading-none truncate flex-1" :style="{ color: L.accents.sun.bold }">Movie night pass</span>
                      <span class="flex-shrink-0 text-[9px] font-semibold px-1.5 py-0.5 rounded-full" :style="{ background: L.accents.sun.bold, color: L.inkInverse }">3</span>
                    </div>
                  </div>
                </div>

                <!-- Row 3: Spring break (full week) -->
                <div class="relative grid grid-cols-7 py-1 border-b" :style="{ borderColor: L.borderSubtle, minHeight: '40px' }">
                  <div v-for="d in 7" :key="d" class="border-r last:border-r-0 h-full" :style="{ borderColor: L.borderSubtle }" />
                  <div class="absolute inset-y-1 flex items-stretch" style="left: 4px; right: 4px;">
                    <div class="flex items-center w-full rounded-full px-3 gap-2" :style="{ background: L.accents.lavender.soft, minHeight: '28px' }">
                      <span class="text-[12px] font-medium leading-none truncate flex-1" :style="{ color: L.accents.lavender.bold }">Emma &amp; Greg · Spring break</span>
                      <div class="flex items-center flex-shrink-0">
                        <span v-for="(av, i) in VACATION_AVATARS" :key="i"
                              class="inline-flex items-center justify-center rounded-full text-[9px] font-bold flex-shrink-0"
                              :style="{ width: '18px', height: '18px', background: av.color, color: '#FFFFFF', marginLeft: i === 0 ? '0' : '-5px', boxShadow: `0 0 0 1.5px ${L.accents.lavender.soft}`, zIndex: 2 - i }">
                          {{ av.initials }}
                        </span>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Row 4: Dentist Tue -->
                <div class="relative grid grid-cols-7 py-1" :style="{ minHeight: '40px' }">
                  <div v-for="d in 7" :key="d" class="border-r last:border-r-0 h-full" :style="{ borderColor: L.borderSubtle }" />
                  <div class="absolute inset-y-1 flex items-stretch"
                       style="left: calc(100% / 7 * 1 + 4px); right: calc(100% / 7 * 5 + 4px);">
                    <div class="flex items-center w-full rounded-full px-3" :style="{ background: L.accents.peach.soft, minHeight: '28px' }">
                      <span class="text-[12px] font-medium leading-none truncate" :style="{ color: L.accents.peach.bold }">Dentist</span>
                    </div>
                  </div>
                </div>

              </div>
            </div>

            <p class="text-[11px]" :style="{ color: L.inkTertiary }">
              Rows at standard spacing (py-1 + minHeight: 40px). Track background is surface-sunken.
              Day dividers (1px borderSubtle) remain visible through every row's vertical gutters.
            </p>
          </div>

          <!-- Dark stacked -->
          <div class="rounded-2xl border p-6 space-y-4"
               :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode · stacked rows</p>

            <div class="space-y-2">
              <div class="grid grid-cols-7">
                <div v-for="(day, i) in DAYS" :key="i"
                     class="text-center text-[10px] font-semibold uppercase tracking-widest pb-1"
                     :style="{ color: D.inkTertiary, borderRight: i < 6 ? `1px solid ${D.borderSubtle}` : 'none' }">
                  {{ day }}
                </div>
              </div>

              <div class="rounded-xl overflow-hidden border" :style="{ borderColor: D.borderSubtle, background: D.surfaceSunken }">

                <div class="relative grid grid-cols-7 py-1 border-b" :style="{ borderColor: D.borderSubtle, minHeight: '40px' }">
                  <div v-for="d in 7" :key="d" class="border-r last:border-r-0 h-full" :style="{ borderColor: D.borderSubtle }" />
                  <div class="absolute inset-y-1 flex items-stretch" style="left: 4px; right: calc(100% / 7 * 4 + 4px);">
                    <div class="flex items-center w-full rounded-full px-3 gap-2" :style="{ background: D.accents.mint.soft, minHeight: '28px' }">
                      <span class="text-[12px] font-medium leading-none truncate" :style="{ color: D.accents.mint.bold }">Leftover stir-fry · Mon→Wed</span>
                    </div>
                  </div>
                </div>

                <div class="relative grid grid-cols-7 py-1 border-b" :style="{ borderColor: D.borderSubtle, minHeight: '40px' }">
                  <div v-for="d in 7" :key="d" class="border-r last:border-r-0 h-full" :style="{ borderColor: D.borderSubtle }" />
                  <div class="absolute inset-y-1 flex items-stretch" style="left: calc(100% / 7 * 4 + 4px); right: 4px;">
                    <div class="flex items-center w-full rounded-full px-3 gap-1" :style="{ background: D.accents.sun.soft, minHeight: '28px' }">
                      <span class="text-[12px] font-medium leading-none truncate flex-1" :style="{ color: D.accents.sun.bold }">Movie night pass</span>
                      <span class="flex-shrink-0 text-[9px] font-semibold px-1.5 py-0.5 rounded-full" :style="{ background: D.accents.sun.bold, color: D.inkInverse }">3</span>
                    </div>
                  </div>
                </div>

                <div class="relative grid grid-cols-7 py-1 border-b" :style="{ borderColor: D.borderSubtle, minHeight: '40px' }">
                  <div v-for="d in 7" :key="d" class="border-r last:border-r-0 h-full" :style="{ borderColor: D.borderSubtle }" />
                  <div class="absolute inset-y-1 flex items-stretch" style="left: 4px; right: 4px;">
                    <div class="flex items-center w-full rounded-full px-3 gap-2" :style="{ background: D.accents.lavender.soft, minHeight: '28px' }">
                      <span class="text-[12px] font-medium leading-none truncate flex-1" :style="{ color: D.accents.lavender.bold }">Emma &amp; Greg · Spring break</span>
                      <div class="flex items-center flex-shrink-0">
                        <span v-for="(av, i) in VACATION_AVATARS" :key="i"
                              class="inline-flex items-center justify-center rounded-full text-[9px] font-bold flex-shrink-0"
                              :style="{ width: '18px', height: '18px', background: av.color, color: '#FFFFFF', marginLeft: i === 0 ? '0' : '-5px', boxShadow: `0 0 0 1.5px ${D.accents.lavender.soft}`, zIndex: 2 - i }">
                          {{ av.initials }}
                        </span>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="relative grid grid-cols-7 py-1" :style="{ minHeight: '40px' }">
                  <div v-for="d in 7" :key="d" class="border-r last:border-r-0 h-full" :style="{ borderColor: D.borderSubtle }" />
                  <div class="absolute inset-y-1 flex items-stretch"
                       style="left: calc(100% / 7 * 1 + 4px); right: calc(100% / 7 * 5 + 4px);">
                    <div class="flex items-center w-full rounded-full px-3" :style="{ background: D.accents.peach.soft, minHeight: '28px' }">
                      <span class="text-[12px] font-medium leading-none truncate" :style="{ color: D.accents.peach.bold }">Dentist</span>
                    </div>
                  </div>
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
          <h2 class="text-[15px] font-semibold" :style="{ color: L.accents.lavender.bold }">Claude's pick — Variant A</h2>
        </div>
        <p class="text-[14px] leading-relaxed" :style="{ color: L.inkPrimary }">
          Variant A is the most versatile starting point — the accent-soft fill is gentle enough not to
          overwhelm a dense week track while the accent-bold text stays crisp at 13px. It reads instantly
          without any supplemental border (B) or gradient computation (C), making it the right default for
          meal plan rows, calendar overlays, and vacation blocks alike. Reach for B when rows stack tightly
          and borders sharpen the visual separators; reach for C when an item is actively in-progress and
          ambient elapsed-time feedback adds real user value.
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
            <strong :style="{ color: L.inkPrimary }">Variant A (default — soft fill, bold text)</strong> —
            Use for all new timeline contexts unless there is a specific reason for B or C. The accent-soft fill
            is visually light but the accent-bold label is easy to read. Supports label, icon, avatars, bids chip,
            and drag handle without feeling cluttered. Best for: meal plan week view, vacation overlays, shared
            task blocks.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Variant B (outlined — add when stacking)</strong> —
            Add the 1.5px accent-bold border when multiple rows occupy the same track and risk merging visually.
            The border is the same color as the text, so it reinforces the accent rather than introducing a new
            color. Switch to B globally on a given track once you have 3+ rows stacking there.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Variant C (gradient + progress — for in-progress items)</strong> —
            Use when the item has a known start and end date and the user benefits from seeing elapsed progress
            without a hard number. The gradient direction (light left → slightly bolder right) mirrors forward
            time movement. The inner progress fill is a subtle rgba darkening — not a separate element — so it
            degrades gracefully on narrow (1-column) rows where the fill is barely visible.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Span positioning</strong> —
            Bars use <code class="font-mono text-[12px] px-1 py-0.5 rounded" :style="{ background: L.surfaceSunken }">left/right</code>
            values computed from column offsets (e.g. <code class="font-mono text-[12px] px-1 py-0.5 rounded" :style="{ background: L.surfaceSunken }">calc(100%/7*4 + 4px)</code>).
            The +4px / -4px gutters keep bars from touching column dividers.
            For CSS-grid-based implementations use
            <code class="font-mono text-[12px] px-1 py-0.5 rounded" :style="{ background: L.surfaceSunken }">grid-column: 2 / span 3</code>.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Mobile (375px)</strong> —
            Labels truncate with ellipsis at narrow widths. Avatar stacks collapse to a count badge (show 0–2 avatars
            + count chip beyond). Drag handles hide on touch viewports — use long-press to trigger drag mode instead.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Drag handle (2×2 dots)</strong> —
            Always placed at the far right. Two rows of two 3px dots, gap-[3px], inkTertiary / accent-bold at
            opacity 0.6. Use <code class="font-mono text-[12px] px-1 py-0.5 rounded" :style="{ background: L.surfaceSunken }">cursor-grab</code>
            on desktop. Hide on mobile. The handle doubles as a resize handle for the bar's right edge in drag mode.
          </li>
        </ul>
      </div>
    </section>

  </ComponentPage>
</template>

<style scoped>
/* Smooth hover feedback on timeline bars */
.timeline-bar-hover {
  transition: opacity 120ms ease;
}
.timeline-bar-hover:hover {
  opacity: 0.88;
}

/* Reduced motion */
@media (prefers-reduced-motion: reduce) {
  * {
    transition: none !important;
  }
}
</style>
