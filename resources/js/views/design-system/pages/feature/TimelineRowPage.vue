<script setup>
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import KinTimelineRow from '@/components/design-system/KinTimelineRow.vue'
import { SparklesIcon, CakeIcon, MapPinIcon } from '@heroicons/vue/24/outline'

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
    status="chosen"
  >

    <!-- ══════════════════════════════════════════════════════════════
         VARIANT A — Filled soft pill, bold-color text
         rounded-full, accent-soft fill, accent-bold text.
         Label left, avatars right, drag handle far right.
         Simplest and most readable.
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="TimelineRow" caption="Filled soft pill — accent-soft bg · accent-bold text · label left · avatars right · drag handle far right. The single locked TimelineRow shape.">
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
        Soft fill keeps the track from feeling heavy while bold text maintains legibility at every size.
      </p>
    </section>




    <!-- ══════════════════════════════════════════════════════════════
         STACKED ROWS — Feed density example
         4 rows at different spans in the same week track.
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="Stacked" caption="Feed-density stacking — 4 TimelineRows in the same 7-column track at varied spans.">
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
          <h2 class="text-[15px] font-semibold" :style="{ color: L.accents.lavender.bold }">LOCKED — filled soft pill</h2>
        </div>
        <p class="text-[14px] leading-relaxed" :style="{ color: L.inkPrimary }">
          One TimelineRow shape: rounded-full pill with accent-soft fill and accent-bold text, label left, avatars right, drag handle far right. No border (kept clean in dense tracks), no gradient (no ambient progress math to maintain). Reads instantly at every width.
        </p>
        <p class="text-[14px] leading-relaxed" :style="{ color: L.inkSecondary }">
          Used for every multi-day timeline item in the app: meal-plan recipe windows, vacation overlays, reward auction windows, shared task blocks, family-calendar overlays. The accent color comes from the item's category.
        </p>
      </div>
    </section>


    <!-- ══════════════════════════════════════════════════════════════
         USAGE GUIDE
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-8">
      <div class="rounded-2xl border p-6 space-y-4"
           :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle }">
        <h2 class="text-[17px] font-semibold" :style="{ color: L.inkPrimary }">When and how to use TimelineRow</h2>
        <ul class="space-y-3 text-[14px]" :style="{ color: L.inkSecondary }">
          <li>
            <strong :style="{ color: L.inkPrimary }">Default for every timeline context.</strong>
            Meal-plan week view, vacation overlays, reward auction windows, shared task blocks, family-calendar overlays. The accent color (lavender / peach / mint / sun) comes from the item's category.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Contents.</strong>
            Optional icon + label on the left (truncate with ellipsis on narrow), avatars stack on the right (up to 3 visible + count chip beyond), drag handle on the far right for reorderable contexts. Labels can include bidder-count chips or completion indicators inline.
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


    <!-- KIN COMPONENT PREVIEW -->
    <section class="mb-16">
      <VariantFrame label="Kin" caption="KinTimelineRow — proposed extraction. Pill only; parent handles grid positioning.">
        <div class="w-full space-y-10">
          <div class="rounded-2xl border p-6 space-y-3" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest mb-4" :style="{ color: L.inkTertiary }">Light mode — 4 accent colors, icons, avatars, drag handle</p>
            <KinTimelineRow label="Leftover stir-fry · Mon→Wed" accent-color="mint" :icon="CakeIcon" draggable />
            <KinTimelineRow label="Movie night pass · Fri→Sun" accent-color="sun" :avatars="[{initials:'M',color:'#6856B2'},{initials:'D',color:'#2E8A62'},{initials:'E',color:'#BA562E'}]" />
            <KinTimelineRow label="Vacation · Maine" accent-color="lavender" :icon="MapPinIcon" draggable />
            <KinTimelineRow label="Peach week" accent-color="peach" />
          </div>

          <div class="dark rounded-2xl border p-6 space-y-3" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest mb-4" :style="{ color: D.inkTertiary }">Dark mode</p>
            <KinTimelineRow label="Leftover stir-fry · Mon→Wed" accent-color="mint" :icon="CakeIcon" draggable />
            <KinTimelineRow label="Movie night pass · Fri→Sun" accent-color="sun" :avatars="[{initials:'M',color:'#B6A8E6'},{initials:'D',color:'#7CD6AE'},{initials:'E',color:'#F0A882'}]" />
            <KinTimelineRow label="Vacation · Maine" accent-color="lavender" :icon="MapPinIcon" draggable />
            <KinTimelineRow label="Peach week" accent-color="peach" />
          </div>
        </div>
      </VariantFrame>
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
