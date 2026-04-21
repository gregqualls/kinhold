<script setup>
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import KinEventTile from '@/components/design-system/KinEventTile.vue'
import {
  SparklesIcon,
  ClockIcon,
  LockClosedIcon,
  MapPinIcon,
  CheckBadgeIcon,
  CalendarDaysIcon,
} from '@heroicons/vue/24/outline'

// ── Palette ───────────────────────────────────────────────────────────────────
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

// ── Shadow helpers ────────────────────────────────────────────────────────────
const SH_LT = '0 1px 2px rgba(28,20,10,0.04), 0 2px 6px rgba(28,20,10,0.05)'
const SH_DK = '0 1px 2px rgba(0,0,0,0.30), 0 2px 6px rgba(0,0,0,0.25)'

// ── Avatar stubs (initials + colour) ─────────────────────────────────────────
const AVATARS = {
  mom:  { initials: 'M', color: '#6856B2' },
  dad:  { initials: 'D', color: '#2E8A62' },
  emma: { initials: 'E', color: '#BA562E' },
  jack: { initials: 'J', color: '#A2780C' },
}
</script>

<template>
  <ComponentPage
    title="5.3 EventTile"
    description="The canonical calendar event card. Renders in month grid cells, week-view columns, day-view rows, and dashboard 'up next' lists. Handles all source types (task / manual / Google / ICS) and all visibility modes (visible / busy / private)."
    status="chosen"
  >

    <!-- ══════════════════════════════════════════════════════════════
         VARIANT A — Filled pastel
         Solid soft-accent background, bold-accent title, ink meta.
         Rounded-xl (12 px). No left bar. iOS Fantastical mobile feel.
         ══════════════════════════════════════════════════════════════ -->
    <section class="mb-20">
      <VariantFrame
        label="Primary"
        caption="Filled pastel tile — the default EventTile shape. Used for month grid cells, week-view timed blocks, day-view rows, and dashboard 'up next' lists."
      >
        <div class="w-full space-y-10">

          <!-- ─── LIGHT PANEL A ───────────────────────────────────── -->
          <div
            class="rounded-2xl border p-6 space-y-6"
            :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }"
          >
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>

            <!-- Row: mobile (cell-sized) tiles -->
            <div>
              <p class="text-[11px] mb-3 font-medium" :style="{ color: L.inkTertiary }">Mobile — 375 px cell-sized tiles</p>
              <div class="flex flex-wrap gap-2">

                <!-- Title + time — lavender/family -->
                <div
                  class="et-a rounded-xl px-2.5 py-1.5 w-[148px] cursor-pointer"
                  :style="{ background: L.accents.lavender.soft, boxShadow: SH_LT }"
                >
                  <p class="text-[13px] font-semibold leading-tight truncate" :style="{ color: L.accents.lavender.bold }">Soccer practice</p>
                  <p class="text-[11px] font-medium mt-0.5 flex items-center gap-1" :style="{ color: L.inkSecondary }">
                    <ClockIcon class="w-3 h-3 shrink-0" />
                    5:00 – 6:30 PM
                  </p>
                </div>

                <!-- Title + time + 2 avatars — mint/school -->
                <div
                  class="et-a rounded-xl px-2.5 py-1.5 w-[148px] cursor-pointer"
                  :style="{ background: L.accents.mint.soft, boxShadow: SH_LT }"
                >
                  <p class="text-[13px] font-semibold leading-tight truncate" :style="{ color: L.accents.mint.bold }">Parent-teacher</p>
                  <div class="flex items-center justify-between mt-0.5">
                    <p class="text-[11px] font-medium" :style="{ color: L.inkSecondary }">2:00 – 3:00 PM</p>
                    <div class="flex -space-x-1.5">
                      <span
                        v-for="av in [AVATARS.mom, AVATARS.dad]" :key="av.initials"
                        class="inline-flex items-center justify-center w-5 h-5 rounded-full text-[9px] font-bold ring-1 ring-white"
                        :style="{ background: av.color, color: '#fff' }"
                      >{{ av.initials }}</span>
                    </div>
                  </div>
                </div>

                <!-- All-day — sun/house -->
                <div
                  class="et-a rounded-xl px-2.5 py-1.5 w-[148px] cursor-pointer"
                  :style="{ background: L.accents.sun.soft, boxShadow: SH_LT }"
                >
                  <p class="text-[13px] font-semibold leading-tight truncate" :style="{ color: L.accents.sun.bold }">Emma's birthday</p>
                  <p class="text-[11px] font-medium mt-0.5" :style="{ color: L.inkTertiary }">All day</p>
                </div>

                <!-- Task-derived — dashed border -->
                <div
                  class="et-a rounded-xl px-2.5 py-1.5 w-[148px] cursor-pointer"
                  :style="{ background: L.accents.peach.soft, boxShadow: SH_LT, border: `1.5px dashed ${L.accents.peach.bold}` }"
                >
                  <p class="text-[13px] font-semibold leading-tight truncate" :style="{ color: L.accents.peach.bold }">Science project</p>
                  <div class="flex items-center gap-1 mt-0.5">
                    <CheckBadgeIcon class="w-3 h-3 shrink-0" :style="{ color: L.accents.peach.bold }" />
                    <p class="text-[11px] font-medium" :style="{ color: L.inkSecondary }">Due 3:00 PM</p>
                  </div>
                </div>

                <!-- Busy visibility -->
                <div
                  class="et-a rounded-xl px-2.5 py-1.5 w-[148px] cursor-pointer"
                  :style="{ background: L.surfaceSunken, boxShadow: SH_LT, border: `1px solid ${L.borderStrong}` }"
                >
                  <p class="text-[13px] font-semibold leading-tight" :style="{ color: L.inkSecondary }">Busy</p>
                  <p class="text-[11px] font-medium mt-0.5" :style="{ color: L.inkTertiary }">10:00 – 11:30 AM</p>
                </div>

                <!-- Private visibility -->
                <div
                  class="et-a rounded-xl px-2.5 py-1.5 w-[148px] cursor-pointer"
                  :style="{ background: L.surfaceSunken, boxShadow: SH_LT, border: `1px solid ${L.borderStrong}` }"
                >
                  <div class="flex items-center gap-1">
                    <LockClosedIcon class="w-3 h-3 shrink-0" :style="{ color: L.inkTertiary }" />
                    <p class="text-[13px] font-semibold leading-tight" :style="{ color: L.inkSecondary }">Private</p>
                  </div>
                  <p class="text-[11px] font-medium mt-0.5" :style="{ color: L.inkTertiary }">7:00 – 8:00 PM</p>
                </div>

              </div>
            </div>

            <!-- Row: desktop (wider cards) -->
            <div>
              <p class="text-[11px] mb-3 font-medium" :style="{ color: L.inkTertiary }">Desktop — wider card form</p>
              <div class="flex flex-wrap gap-3">

                <!-- Title + time + location -->
                <div
                  class="et-a rounded-xl px-3 py-2.5 w-[240px] cursor-pointer"
                  :style="{ background: L.accents.lavender.soft, boxShadow: SH_LT }"
                >
                  <p class="text-[14px] font-semibold leading-tight" :style="{ color: L.accents.lavender.bold }">Soccer practice</p>
                  <div class="flex items-center gap-1 mt-1" :style="{ color: L.inkSecondary }">
                    <ClockIcon class="w-3.5 h-3.5 shrink-0" />
                    <span class="text-[12px] font-medium">5:00 – 6:30 PM</span>
                  </div>
                  <div class="flex items-center gap-1 mt-0.5" :style="{ color: L.inkTertiary }">
                    <MapPinIcon class="w-3.5 h-3.5 shrink-0" />
                    <span class="text-[12px]">Riverside Fields</span>
                  </div>
                </div>

                <!-- Title + time + 2 avatars -->
                <div
                  class="et-a rounded-xl px-3 py-2.5 w-[240px] cursor-pointer"
                  :style="{ background: L.accents.mint.soft, boxShadow: SH_LT }"
                >
                  <div class="flex items-start justify-between gap-2">
                    <p class="text-[14px] font-semibold leading-tight" :style="{ color: L.accents.mint.bold }">Parent-teacher conf.</p>
                    <div class="flex shrink-0 -space-x-1.5 mt-0.5">
                      <span
                        v-for="av in [AVATARS.mom, AVATARS.dad]" :key="av.initials"
                        class="inline-flex items-center justify-center w-5 h-5 rounded-full text-[9px] font-bold ring-1 ring-white"
                        :style="{ background: av.color, color: '#fff' }"
                      >{{ av.initials }}</span>
                    </div>
                  </div>
                  <div class="flex items-center gap-1 mt-1" :style="{ color: L.inkSecondary }">
                    <ClockIcon class="w-3.5 h-3.5 shrink-0" />
                    <span class="text-[12px] font-medium">2:00 – 3:00 PM · 2 people</span>
                  </div>
                </div>

                <!-- All-day -->
                <div
                  class="et-a rounded-xl px-3 py-2.5 w-[240px] cursor-pointer"
                  :style="{ background: L.accents.sun.soft, boxShadow: SH_LT }"
                >
                  <p class="text-[14px] font-semibold leading-tight" :style="{ color: L.accents.sun.bold }">Emma's birthday</p>
                  <div class="flex items-center gap-1 mt-1">
                    <CalendarDaysIcon class="w-3.5 h-3.5 shrink-0" :style="{ color: L.inkTertiary }" />
                    <span class="text-[12px] font-medium" :style="{ color: L.inkTertiary }">All day · April 22</span>
                  </div>
                </div>

                <!-- Google source (connection-color tint on top of lavender-soft) -->
                <div
                  class="et-a rounded-xl px-3 py-2.5 w-[240px] cursor-pointer"
                  :style="{ background: '#E8F0FE', boxShadow: SH_LT }"
                >
                  <p class="text-[14px] font-semibold leading-tight" style="color: #1A73E8;">Team standup</p>
                  <div class="flex items-center gap-1 mt-1">
                    <ClockIcon class="w-3.5 h-3.5 shrink-0" style="color: #5F6368;" />
                    <span class="text-[12px] font-medium" style="color: #5F6368;">9:00 – 9:30 AM</span>
                  </div>
                  <p class="text-[11px] mt-0.5" style="color: #5F6368;">Google Calendar</p>
                </div>

              </div>
            </div>
          </div><!-- /light panel A -->

          <!-- ─── DARK PANEL A ─────────────────────────────────────── -->
          <div
            class="rounded-2xl border p-6 space-y-6"
            :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }"
          >
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

            <!-- Mobile tiles dark -->
            <div>
              <p class="text-[11px] mb-3 font-medium" :style="{ color: D.inkTertiary }">Mobile — cell-sized tiles</p>
              <div class="flex flex-wrap gap-2">

                <div
                  class="et-a rounded-xl px-2.5 py-1.5 w-[148px] cursor-pointer"
                  :style="{ background: D.accents.lavender.soft, boxShadow: SH_DK }"
                >
                  <p class="text-[13px] font-semibold leading-tight truncate" :style="{ color: D.accents.lavender.bold }">Soccer practice</p>
                  <p class="text-[11px] font-medium mt-0.5 flex items-center gap-1" :style="{ color: D.inkSecondary }">
                    <ClockIcon class="w-3 h-3 shrink-0" />
                    5:00 – 6:30 PM
                  </p>
                </div>

                <div
                  class="et-a rounded-xl px-2.5 py-1.5 w-[148px] cursor-pointer"
                  :style="{ background: D.accents.mint.soft, boxShadow: SH_DK }"
                >
                  <p class="text-[13px] font-semibold leading-tight truncate" :style="{ color: D.accents.mint.bold }">Parent-teacher</p>
                  <div class="flex items-center justify-between mt-0.5">
                    <p class="text-[11px] font-medium" :style="{ color: D.inkSecondary }">2:00 – 3:00 PM</p>
                    <div class="flex -space-x-1.5">
                      <span
                        v-for="av in [AVATARS.mom, AVATARS.dad]" :key="av.initials"
                        class="inline-flex items-center justify-center w-5 h-5 rounded-full text-[9px] font-bold ring-1"
                        :style="{ background: av.color, color: '#fff', '--tw-ring-color': D.surfaceRaised }"
                      >{{ av.initials }}</span>
                    </div>
                  </div>
                </div>

                <div
                  class="et-a rounded-xl px-2.5 py-1.5 w-[148px] cursor-pointer"
                  :style="{ background: D.accents.sun.soft, boxShadow: SH_DK }"
                >
                  <p class="text-[13px] font-semibold leading-tight truncate" :style="{ color: D.accents.sun.bold }">Emma's birthday</p>
                  <p class="text-[11px] font-medium mt-0.5" :style="{ color: D.inkTertiary }">All day</p>
                </div>

                <!-- Task-derived dashed dark -->
                <div
                  class="et-a rounded-xl px-2.5 py-1.5 w-[148px] cursor-pointer"
                  :style="{ background: D.accents.peach.soft, boxShadow: SH_DK, border: `1.5px dashed ${D.accents.peach.bold}` }"
                >
                  <p class="text-[13px] font-semibold leading-tight truncate" :style="{ color: D.accents.peach.bold }">Science project</p>
                  <div class="flex items-center gap-1 mt-0.5">
                    <CheckBadgeIcon class="w-3 h-3 shrink-0" :style="{ color: D.accents.peach.bold }" />
                    <p class="text-[11px] font-medium" :style="{ color: D.inkSecondary }">Due 3:00 PM</p>
                  </div>
                </div>

                <!-- Busy dark -->
                <div
                  class="et-a rounded-xl px-2.5 py-1.5 w-[148px] cursor-pointer"
                  :style="{ background: D.surfaceSunken, boxShadow: SH_DK, border: `1px solid ${D.borderStrong}` }"
                >
                  <p class="text-[13px] font-semibold leading-tight" :style="{ color: D.inkSecondary }">Busy</p>
                  <p class="text-[11px] font-medium mt-0.5" :style="{ color: D.inkTertiary }">10:00 – 11:30 AM</p>
                </div>

                <!-- Private dark -->
                <div
                  class="et-a rounded-xl px-2.5 py-1.5 w-[148px] cursor-pointer"
                  :style="{ background: D.surfaceSunken, boxShadow: SH_DK, border: `1px solid ${D.borderStrong}` }"
                >
                  <div class="flex items-center gap-1">
                    <LockClosedIcon class="w-3 h-3 shrink-0" :style="{ color: D.inkTertiary }" />
                    <p class="text-[13px] font-semibold leading-tight" :style="{ color: D.inkSecondary }">Private</p>
                  </div>
                  <p class="text-[11px] font-medium mt-0.5" :style="{ color: D.inkTertiary }">7:00 – 8:00 PM</p>
                </div>

              </div>
            </div>

            <!-- Desktop tiles dark -->
            <div>
              <p class="text-[11px] mb-3 font-medium" :style="{ color: D.inkTertiary }">Desktop — wider card form</p>
              <div class="flex flex-wrap gap-3">

                <div
                  class="et-a rounded-xl px-3 py-2.5 w-[240px] cursor-pointer"
                  :style="{ background: D.accents.lavender.soft, boxShadow: SH_DK }"
                >
                  <p class="text-[14px] font-semibold leading-tight" :style="{ color: D.accents.lavender.bold }">Soccer practice</p>
                  <div class="flex items-center gap-1 mt-1" :style="{ color: D.inkSecondary }">
                    <ClockIcon class="w-3.5 h-3.5 shrink-0" />
                    <span class="text-[12px] font-medium">5:00 – 6:30 PM</span>
                  </div>
                  <div class="flex items-center gap-1 mt-0.5" :style="{ color: D.inkTertiary }">
                    <MapPinIcon class="w-3.5 h-3.5 shrink-0" />
                    <span class="text-[12px]">Riverside Fields</span>
                  </div>
                </div>

                <div
                  class="et-a rounded-xl px-3 py-2.5 w-[240px] cursor-pointer"
                  :style="{ background: D.accents.mint.soft, boxShadow: SH_DK }"
                >
                  <div class="flex items-start justify-between gap-2">
                    <p class="text-[14px] font-semibold leading-tight" :style="{ color: D.accents.mint.bold }">Parent-teacher conf.</p>
                    <div class="flex shrink-0 -space-x-1.5 mt-0.5">
                      <span
                        v-for="av in [AVATARS.mom, AVATARS.dad]" :key="av.initials"
                        class="inline-flex items-center justify-center w-5 h-5 rounded-full text-[9px] font-bold"
                        :style="{ background: av.color, color: '#fff', outline: `2px solid ${D.surfaceRaised}` }"
                      >{{ av.initials }}</span>
                    </div>
                  </div>
                  <div class="flex items-center gap-1 mt-1" :style="{ color: D.inkSecondary }">
                    <ClockIcon class="w-3.5 h-3.5 shrink-0" />
                    <span class="text-[12px] font-medium">2:00 – 3:00 PM · 2 people</span>
                  </div>
                </div>

                <div
                  class="et-a rounded-xl px-3 py-2.5 w-[240px] cursor-pointer"
                  :style="{ background: D.accents.sun.soft, boxShadow: SH_DK }"
                >
                  <p class="text-[14px] font-semibold leading-tight" :style="{ color: D.accents.sun.bold }">Emma's birthday</p>
                  <div class="flex items-center gap-1 mt-1">
                    <CalendarDaysIcon class="w-3.5 h-3.5 shrink-0" :style="{ color: D.inkTertiary }" />
                    <span class="text-[12px] font-medium" :style="{ color: D.inkTertiary }">All day · April 22</span>
                  </div>
                </div>

                <!-- Google source dark -->
                <div
                  class="et-a rounded-xl px-3 py-2.5 w-[240px] cursor-pointer"
                  :style="{ background: '#1A2845', boxShadow: SH_DK }"
                >
                  <p class="text-[14px] font-semibold leading-tight" style="color: #7AB3FF;">Team standup</p>
                  <div class="flex items-center gap-1 mt-1">
                    <ClockIcon class="w-3.5 h-3.5 shrink-0" style="color: #8A9AC0;" />
                    <span class="text-[12px] font-medium" style="color: #8A9AC0;">9:00 – 9:30 AM</span>
                  </div>
                  <p class="text-[11px] mt-0.5" style="color: #8A9AC0;">Google Calendar</p>
                </div>

              </div>
            </div>

          </div><!-- /dark panel A -->

        </div>
      </VariantFrame>
    </section>




    <!-- ══════════════════════════════════════════════════════════════
         VARIANT C — Pill bar spanning dates
         Elongated pill (rounded-full), soft-accent fill, bold-accent text.
         Avatars right side. Week view / meal plan / auction-window events.
         ══════════════════════════════════════════════════════════════ -->
    <section class="mb-20">
      <VariantFrame
        label="Multi-day"
        caption="Pill variant (prop-opt-in) — rounded-full pill spanning a date range. Used for vacation blocks, meal-plan recipe windows, auction ranges — anywhere duration is the primary message."
      >
        <div class="w-full space-y-10">

          <!-- ─── LIGHT PANEL C ───────────────────────────────────── -->
          <div
            class="rounded-2xl border p-6 space-y-6"
            :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }"
          >
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>

            <!-- Simulated week row grid -->
            <div>
              <p class="text-[11px] mb-3 font-medium" :style="{ color: L.inkTertiary }">Week-view row — multi-day spanning pills</p>
              <div class="space-y-2">

                <!-- Spans Mon–Wed (3 days) -->
                <div class="flex items-center gap-1">
                  <div class="w-14 shrink-0"></div><!-- spacer for day labels -->
                  <div
                    class="et-c flex items-center justify-between rounded-full px-3 py-1 cursor-pointer"
                    style="width: calc(3 * (100% / 7) - 4px);"
                    :style="{ background: L.accents.lavender.soft }"
                  >
                    <span class="text-[12px] font-semibold leading-none truncate" :style="{ color: L.accents.lavender.bold }">Family camping trip</span>
                    <div class="flex shrink-0 -space-x-1 ml-2">
                      <span
                        v-for="av in [AVATARS.mom, AVATARS.dad, AVATARS.emma]" :key="av.initials"
                        class="inline-flex items-center justify-center w-4 h-4 rounded-full text-[8px] font-bold ring-1 ring-white"
                        :style="{ background: av.color, color: '#fff' }"
                      >{{ av.initials }}</span>
                    </div>
                  </div>
                </div>

                <!-- Spans Tue–Thu (task-derived, dashed) -->
                <div class="flex items-center gap-1">
                  <div class="w-14 shrink-0"></div>
                  <div class="w-[calc(100%/7)]"></div><!-- offset 1 day -->
                  <div
                    class="et-c flex items-center rounded-full px-3 py-1 cursor-pointer"
                    style="width: calc(3 * (100% / 7) - 4px);"
                    :style="{ background: L.accents.peach.soft, border: `1.5px dashed ${L.accents.peach.bold}` }"
                  >
                    <CheckBadgeIcon class="w-3 h-3 shrink-0 mr-1.5" :style="{ color: L.accents.peach.bold }" />
                    <span class="text-[12px] font-semibold leading-none truncate" :style="{ color: L.accents.peach.bold }">Science fair project</span>
                  </div>
                </div>

                <!-- All-day single day — sun pill -->
                <div class="flex items-center gap-1">
                  <div class="w-14 shrink-0"></div>
                  <div class="w-[calc(2*100%/7)]"></div><!-- offset 2 days -->
                  <div
                    class="et-c flex items-center rounded-full px-3 py-1 cursor-pointer"
                    style="width: calc(1 * (100% / 7) - 4px);"
                    :style="{ background: L.accents.sun.soft }"
                  >
                    <span class="text-[12px] font-semibold leading-none truncate" :style="{ color: L.accents.sun.bold }">Emma's birthday</span>
                  </div>
                </div>

                <!-- Busy multi-day -->
                <div class="flex items-center gap-1">
                  <div class="w-14 shrink-0"></div>
                  <div class="w-[calc(3*100%/7)]"></div><!-- offset 3 days -->
                  <div
                    class="et-c flex items-center rounded-full px-3 py-1 cursor-pointer"
                    style="width: calc(2 * (100% / 7) - 4px);"
                    :style="{ background: L.surfaceSunken, border: `1px solid ${L.borderStrong}` }"
                  >
                    <span class="text-[12px] font-medium leading-none" :style="{ color: L.inkTertiary }">Busy · 10:00 – 2:00 PM</span>
                  </div>
                </div>

                <!-- Private single day -->
                <div class="flex items-center gap-1">
                  <div class="w-14 shrink-0"></div>
                  <div class="w-[calc(5*100%/7)]"></div><!-- offset 5 days -->
                  <div
                    class="et-c flex items-center gap-1 rounded-full px-3 py-1 cursor-pointer"
                    style="width: calc(2 * (100% / 7) - 4px);"
                    :style="{ background: L.surfaceSunken, border: `1px solid ${L.borderStrong}` }"
                  >
                    <LockClosedIcon class="w-3 h-3 shrink-0" :style="{ color: L.inkTertiary }" />
                    <span class="text-[12px] font-medium leading-none" :style="{ color: L.inkTertiary }">Private</span>
                  </div>
                </div>

              </div>
            </div>

            <!-- Standalone desktop pills (wider) -->
            <div>
              <p class="text-[11px] mb-3 font-medium" :style="{ color: L.inkTertiary }">Desktop — wider standalone pills (day-view / up-next)</p>
              <div class="space-y-2 max-w-lg">

                <div
                  class="et-c flex items-center justify-between rounded-full px-4 py-2 cursor-pointer w-full"
                  :style="{ background: L.accents.lavender.soft }"
                >
                  <div>
                    <span class="text-[13px] font-semibold" :style="{ color: L.accents.lavender.bold }">Soccer practice</span>
                    <span class="text-[12px] font-medium ml-2" :style="{ color: L.inkSecondary }">5:00 – 6:30 PM</span>
                  </div>
                  <div class="flex -space-x-1.5 ml-3 shrink-0">
                    <span
                      v-for="av in [AVATARS.jack, AVATARS.emma]" :key="av.initials"
                      class="inline-flex items-center justify-center w-6 h-6 rounded-full text-[10px] font-bold ring-2 ring-white"
                      :style="{ background: av.color, color: '#fff' }"
                    >{{ av.initials }}</span>
                  </div>
                </div>

                <div
                  class="et-c flex items-center rounded-full px-4 py-2 cursor-pointer w-full"
                  :style="{ background: L.accents.mint.soft, border: `1.5px dashed ${L.accents.mint.bold}` }"
                >
                  <CheckBadgeIcon class="w-4 h-4 shrink-0 mr-2" :style="{ color: L.accents.mint.bold }" />
                  <span class="text-[13px] font-semibold" :style="{ color: L.accents.mint.bold }">Dentist appointment</span>
                  <span class="text-[12px] font-medium ml-2" :style="{ color: L.inkSecondary }">All day</span>
                </div>

                <!-- Google source pill -->
                <div
                  class="et-c flex items-center justify-between rounded-full px-4 py-2 cursor-pointer w-full"
                  style="background: #E8F0FE;"
                >
                  <div>
                    <span class="text-[13px] font-semibold" style="color: #1A73E8;">Team standup</span>
                    <span class="text-[12px] font-medium ml-2" style="color: #5F6368;">9:00 – 9:30 AM · Google</span>
                  </div>
                </div>

              </div>
            </div>

          </div><!-- /light panel C -->

          <!-- ─── DARK PANEL C ─────────────────────────────────────── -->
          <div
            class="rounded-2xl border p-6 space-y-6"
            :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }"
          >
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

            <!-- Week row dark -->
            <div>
              <p class="text-[11px] mb-3 font-medium" :style="{ color: D.inkTertiary }">Week-view row — multi-day spanning pills</p>
              <div class="space-y-2">

                <div class="flex items-center gap-1">
                  <div class="w-14 shrink-0"></div>
                  <div
                    class="et-c flex items-center justify-between rounded-full px-3 py-1 cursor-pointer"
                    style="width: calc(3 * (100% / 7) - 4px);"
                    :style="{ background: D.accents.lavender.soft }"
                  >
                    <span class="text-[12px] font-semibold leading-none truncate" :style="{ color: D.accents.lavender.bold }">Family camping trip</span>
                    <div class="flex shrink-0 -space-x-1 ml-2">
                      <span
                        v-for="av in [AVATARS.mom, AVATARS.dad, AVATARS.emma]" :key="av.initials"
                        class="inline-flex items-center justify-center w-4 h-4 rounded-full text-[8px] font-bold"
                        :style="{ background: av.color, color: '#fff', outline: `1.5px solid ${D.surfaceApp}` }"
                      >{{ av.initials }}</span>
                    </div>
                  </div>
                </div>

                <div class="flex items-center gap-1">
                  <div class="w-14 shrink-0"></div>
                  <div class="w-[calc(100%/7)]"></div>
                  <div
                    class="et-c flex items-center rounded-full px-3 py-1 cursor-pointer"
                    style="width: calc(3 * (100% / 7) - 4px);"
                    :style="{ background: D.accents.peach.soft, border: `1.5px dashed ${D.accents.peach.bold}` }"
                  >
                    <CheckBadgeIcon class="w-3 h-3 shrink-0 mr-1.5" :style="{ color: D.accents.peach.bold }" />
                    <span class="text-[12px] font-semibold leading-none truncate" :style="{ color: D.accents.peach.bold }">Science fair project</span>
                  </div>
                </div>

                <div class="flex items-center gap-1">
                  <div class="w-14 shrink-0"></div>
                  <div class="w-[calc(2*100%/7)]"></div>
                  <div
                    class="et-c flex items-center rounded-full px-3 py-1 cursor-pointer"
                    style="width: calc(1 * (100% / 7) - 4px);"
                    :style="{ background: D.accents.sun.soft }"
                  >
                    <span class="text-[12px] font-semibold leading-none truncate" :style="{ color: D.accents.sun.bold }">Emma's birthday</span>
                  </div>
                </div>

                <div class="flex items-center gap-1">
                  <div class="w-14 shrink-0"></div>
                  <div class="w-[calc(3*100%/7)]"></div>
                  <div
                    class="et-c flex items-center rounded-full px-3 py-1 cursor-pointer"
                    style="width: calc(2 * (100% / 7) - 4px);"
                    :style="{ background: D.surfaceSunken, border: `1px solid ${D.borderStrong}` }"
                  >
                    <span class="text-[12px] font-medium leading-none" :style="{ color: D.inkTertiary }">Busy · 10:00 – 2:00 PM</span>
                  </div>
                </div>

                <div class="flex items-center gap-1">
                  <div class="w-14 shrink-0"></div>
                  <div class="w-[calc(5*100%/7)]"></div>
                  <div
                    class="et-c flex items-center gap-1 rounded-full px-3 py-1 cursor-pointer"
                    style="width: calc(2 * (100% / 7) - 4px);"
                    :style="{ background: D.surfaceSunken, border: `1px solid ${D.borderStrong}` }"
                  >
                    <LockClosedIcon class="w-3 h-3 shrink-0" :style="{ color: D.inkTertiary }" />
                    <span class="text-[12px] font-medium leading-none" :style="{ color: D.inkTertiary }">Private</span>
                  </div>
                </div>

              </div>
            </div>

            <!-- Desktop pills dark -->
            <div>
              <p class="text-[11px] mb-3 font-medium" :style="{ color: D.inkTertiary }">Desktop — wider standalone pills</p>
              <div class="space-y-2 max-w-lg">

                <div
                  class="et-c flex items-center justify-between rounded-full px-4 py-2 cursor-pointer w-full"
                  :style="{ background: D.accents.lavender.soft }"
                >
                  <div>
                    <span class="text-[13px] font-semibold" :style="{ color: D.accents.lavender.bold }">Soccer practice</span>
                    <span class="text-[12px] font-medium ml-2" :style="{ color: D.inkSecondary }">5:00 – 6:30 PM</span>
                  </div>
                  <div class="flex -space-x-1.5 ml-3 shrink-0">
                    <span
                      v-for="av in [AVATARS.jack, AVATARS.emma]" :key="av.initials"
                      class="inline-flex items-center justify-center w-6 h-6 rounded-full text-[10px] font-bold"
                      :style="{ background: av.color, color: '#fff', outline: `2px solid ${D.surfaceApp}` }"
                    >{{ av.initials }}</span>
                  </div>
                </div>

                <div
                  class="et-c flex items-center rounded-full px-4 py-2 cursor-pointer w-full"
                  :style="{ background: D.accents.mint.soft, border: `1.5px dashed ${D.accents.mint.bold}` }"
                >
                  <CheckBadgeIcon class="w-4 h-4 shrink-0 mr-2" :style="{ color: D.accents.mint.bold }" />
                  <span class="text-[13px] font-semibold" :style="{ color: D.accents.mint.bold }">Dentist appointment</span>
                  <span class="text-[12px] font-medium ml-2" :style="{ color: D.inkSecondary }">All day</span>
                </div>

                <!-- Google source pill dark -->
                <div
                  class="et-c flex items-center justify-between rounded-full px-4 py-2 cursor-pointer w-full"
                  style="background: #1A2845;"
                >
                  <div>
                    <span class="text-[13px] font-semibold" style="color: #7AB3FF;">Team standup</span>
                    <span class="text-[12px] font-medium ml-2" style="color: #8A9AC0;">9:00 – 9:30 AM · Google</span>
                  </div>
                </div>

              </div>
            </div>

          </div><!-- /dark panel C -->

        </div>
      </VariantFrame>
    </section>


    <!-- ══════════════════════════════════════════════════════════════
         CLAUDE'S PICK CALLOUT
         ══════════════════════════════════════════════════════════════ -->
    <section class="mb-12">
      <div
        class="rounded-2xl border p-6 flex gap-4 items-start"
        :style="{ background: L.accents.lavender.soft, borderColor: L.accents.lavender.bold + '55' }"
      >
        <SparklesIcon class="w-5 h-5 shrink-0 mt-0.5" :style="{ color: L.accents.lavender.bold }" />
        <div>
          <p class="text-[14px] font-semibold mb-1" :style="{ color: L.accents.lavender.bold }">LOCKED — Variant A primary, Variant C as prop variant</p>
          <p class="text-[13px] leading-relaxed" :style="{ color: L.inkSecondary }">
            <strong>Variant A (filled pastel) is the default.</strong> The soft accent fill reads category-at-a-glance, the warmth fits Kinhold's palette voice, and Busy / Private / task-dashed-border all compose cleanly on top. Use for month grid cells, day-view blocks, and dashboard "up next" lists.
          </p>
          <p class="text-[13px] leading-relaxed mt-2" :style="{ color: L.inkSecondary }">
            <strong>Variant C (elongated pill) is available via prop</strong> — likely <code class="text-[12px] font-mono bg-white/60 px-1 rounded">span="multi-day"</code> — for multi-day events that need to communicate duration in a single glance: vacation blocks, meal-plan recipe windows, reward auction ranges. The pill shape reinforces "this spans time" the same way the filled tile reinforces "this is a point in time."
          </p>
        </div>
      </div>
    </section>


    <!-- ══════════════════════════════════════════════════════════════
         USAGE GUIDE
         ══════════════════════════════════════════════════════════════ -->
    <section class="mb-8">
      <div
        class="rounded-2xl border p-6 space-y-4"
        :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle }"
      >
        <h2 class="text-[17px] font-semibold" :style="{ color: L.inkPrimary }">When to use which variant</h2>

        <ul class="space-y-4 text-[14px]" :style="{ color: L.inkSecondary }">
          <li>
            <strong :style="{ color: L.inkPrimary }">Variant A — single-day events (default).</strong>
            Use for month grid cells, week-view timed blocks, day-view rows, and dashboard "up next" lists. The filled pastel background reads category-at-a-glance and warms up dense grids. This is the shape for anything that happens at a specific time.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Variant C — multi-day spans (prop variant).</strong>
            Use when an event spans days and duration matters visually: vacation blocks, meal-plan recipe windows, reward auction ranges, family-calendar overlays. The elongated pill sits in a dedicated row above the timed area in week view, matching every major calendar-app convention. Pass explicitly via a span prop.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Visibility rules (both variants).</strong>
            "Busy" events suppress the title and show only "Busy" + time. "Private" events show a LockClosedIcon + "Private" label. Both use inkSecondary / inkTertiary on surfaceSunken — never an accent fill — so they read as intentionally redacted.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Task-derived events (both variants).</strong>
            Always add a 1.5 px dashed border in the bold-accent color. The fill continues to use the category accent, but the dashed outline signals "this is a task with a due date" without a separate icon.
          </li>
        </ul>
      </div>
    </section>


    <!-- ══════════════════════════════════════════════════════════════════════
         KIN COMPONENT PREVIEW — review below before replacing the bespoke demo
         ═══════════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="Kin" caption="KinEventTile — proposed extraction. Block (default) + span variants; accent colors + source + visibility handled via props.">
        <div class="w-full space-y-10">

          <!-- LIGHT PANEL -->
          <div class="rounded-2xl border p-6 space-y-6"
               :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>

            <div>
              <p class="text-[11px] mb-3 font-medium" :style="{ color: L.inkTertiary }">Block variant — accent colors, avatars, all-day, task, busy, private</p>
              <div class="flex flex-wrap gap-2">
                <KinEventTile width="148px" title="Soccer practice"  time="5:00 – 6:30 PM"  accent-color="lavender" />
                <KinEventTile width="148px" title="Parent-teacher"   time="2:00 – 3:00 PM"  accent-color="mint"
                              :avatars="[{ initials: 'M', color: '#6856B2' }, { initials: 'D', color: '#2E8A62' }]" />
                <KinEventTile width="148px" title="Emma's birthday"  all-day                accent-color="sun" />
                <KinEventTile width="148px" title="Science project"  time="Due 3:00 PM"     accent-color="peach"  source="task" />
                <KinEventTile width="148px" title="Busy"             time="10:00 – 11:30 AM" visibility="busy" />
                <KinEventTile width="148px" title="Private"          time="7:00 – 8:00 PM"  visibility="private" />
              </div>
            </div>

            <div>
              <p class="text-[11px] mb-3 font-medium" :style="{ color: L.inkTertiary }">Span variant — multi-day pills</p>
              <div class="space-y-2">
                <KinEventTile variant="span" title="Family camping trip" accent-color="lavender"
                              :avatars="[{ initials: 'M', color: '#6856B2' }, { initials: 'D', color: '#2E8A62' }, { initials: 'E', color: '#BA562E' }]" />
                <KinEventTile variant="span" title="Meal plan: Mediterranean week" accent-color="mint" />
                <KinEventTile variant="span" title="Vacation · Maine" accent-color="sun" source="task" />
              </div>
            </div>
          </div>

          <!-- DARK PANEL -->
          <div class="dark rounded-2xl border p-6 space-y-6"
               :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

            <div>
              <p class="text-[11px] mb-3 font-medium" :style="{ color: D.inkTertiary }">Block variant</p>
              <div class="flex flex-wrap gap-2">
                <KinEventTile width="148px" title="Soccer practice"  time="5:00 – 6:30 PM"  accent-color="lavender" />
                <KinEventTile width="148px" title="Parent-teacher"   time="2:00 – 3:00 PM"  accent-color="mint"
                              :avatars="[{ initials: 'M', color: '#B6A8E6' }, { initials: 'D', color: '#7CD6AE' }]" />
                <KinEventTile width="148px" title="Emma's birthday"  all-day                accent-color="sun" />
                <KinEventTile width="148px" title="Science project"  time="Due 3:00 PM"     accent-color="peach"  source="task" />
                <KinEventTile width="148px" title="Busy"             time="10:00 – 11:30 AM" visibility="busy" />
                <KinEventTile width="148px" title="Private"          time="7:00 – 8:00 PM"  visibility="private" />
              </div>
            </div>

            <div>
              <p class="text-[11px] mb-3 font-medium" :style="{ color: D.inkTertiary }">Span variant — multi-day pills</p>
              <div class="space-y-2">
                <KinEventTile variant="span" title="Family camping trip" accent-color="lavender"
                              :avatars="[{ initials: 'M', color: '#B6A8E6' }, { initials: 'D', color: '#7CD6AE' }, { initials: 'E', color: '#F0A882' }]" />
                <KinEventTile variant="span" title="Meal plan: Mediterranean week" accent-color="mint" />
                <KinEventTile variant="span" title="Vacation · Maine" accent-color="sun" source="task" />
              </div>
            </div>
          </div>

        </div>
      </VariantFrame>
      <p class="mt-3 text-sm px-1" :style="{ color: L.inkSecondary }">
        Review against the bespoke variants above. Block is the primary; span is a prop-opt-in variant for multi-day spans.
      </p>
    </section>

  </ComponentPage>
</template>

<style scoped>
/* ─── Variant A hover ──────────────────────────────────────────── */
.et-a {
  transition: filter 150ms ease, transform 150ms ease;
}
.et-a:hover {
  filter: brightness(0.96);
  transform: translateY(-1px);
}

/* ─── Variant C hover ──────────────────────────────────────────── */
.et-c {
  transition: filter 150ms ease, transform 150ms ease;
}
.et-c:hover {
  filter: brightness(0.94);
  transform: translateY(-1px);
}

/* ─── Reduced motion ───────────────────────────────────────────── */
@media (prefers-reduced-motion: reduce) {
  .et-a, .et-b, .et-c {
    transition: none;
    transform: none !important;
  }
}
</style>
