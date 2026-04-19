<script setup>
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import {
  SparklesIcon, CalendarDaysIcon, ClockIcon, MapPinIcon, UserIcon,
  StarIcon, ArrowPathIcon, CheckBadgeIcon, TicketIcon, TrophyIcon,
  FlagIcon, UserGroupIcon, PencilSquareIcon, CheckIcon,
  ArrowTopRightOnSquareIcon, TagIcon,
} from '@heroicons/vue/24/outline'

// ── Palette ───────────────────────────────────────────────────────────────────
const L = {
  surfaceApp: '#FAF8F5', surfaceRaised: '#FFFFFF', surfaceSunken: '#F5F2EE',
  inkPrimary: '#1C1C1E', inkSecondary: '#6B6966', inkTertiary: '#9C9895', inkInverse: '#FAF8F5',
  borderSubtle: '#E8E4DF', borderStrong: '#BCB8B2',
  accents: {
    lavender: { soft: '#EAE6F8', bold: '#6856B2' },
    peach:    { soft: '#FCE9E0', bold: '#BA562E' },
    mint:     { soft: '#D5F2E8', bold: '#2E8A62' },
    sun:      { soft: '#FCF3D2', bold: '#A2780C' },
  },
}
const D = {
  surfaceApp: '#141311', surfaceRaised: '#1C1B19', surfaceSunken: '#161513', surfaceOverlay: '#242220',
  inkPrimary: '#F0EDE9', inkSecondary: '#A09C97', inkTertiary: '#6E6B67', inkInverse: '#1C1C1E',
  borderSubtle: '#2C2A27', borderStrong: '#403E3A',
  accents: {
    lavender: { soft: '#302A48', bold: '#B6A8E6' },
    peach:    { soft: '#3E241A', bold: '#F0A882' },
    mint:     { soft: '#18342A', bold: '#7CD6AE' },
    sun:      { soft: '#342C0A', bold: '#E6C452' },
  },
}

const SH_LT = '0 1px 2px rgba(28,20,10,0.04), 0 2px 6px rgba(28,20,10,0.05)'
const SH_DK = '0 1px 2px rgba(0,0,0,0.30), 0 2px 6px rgba(0,0,0,0.25)'
</script>

<template>
  <ComponentPage
    title="5.12 ReceiptCard"
    description="Icon-checklist card with inline action buttons. The 'facts at a glance' pattern — one icon per row, value on the right, optional action. Used for booking confirmations, task detail summaries, reward purchase confirmations, event detail sheets, and recurring-task setup confirmations."
    status="scaffolded"
  >

    <!-- ═══════════════════════════════════════════════════════════════
         VARIANT A — Bordered rows with icon-in-soft-circle
         ════════════════════════════════════════════════════════════════ -->
    <section class="mb-20">
      <VariantFrame
        label="A"
        caption="Bordered rows — each fact row has an icon in a 28px accent-soft circle, label beside it, value right-aligned. Rows separated by 1px borderSubtle divider. Classic receipt look. Ideal for confirmations."
      >
        <div class="w-full space-y-10">

          <!-- ── LIGHT PANEL ── -->
          <div class="rounded-2xl border p-6 space-y-6" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">

              <!-- A · Reward purchase confirmation (light) -->
              <div
                class="rounded-[20px] border flex flex-col"
                :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle, boxShadow: SH_LT }"
              >
                <!-- Header -->
                <div class="px-5 pt-5 pb-4 flex items-center gap-3">
                  <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0" :style="{ background: L.accents.sun.soft }">
                    <TicketIcon class="w-4 h-4" :style="{ color: L.accents.sun.bold }" />
                  </div>
                  <p class="text-[15px] font-semibold leading-snug" :style="{ color: L.inkPrimary }">Movie night pass</p>
                </div>

                <!-- Fact rows -->
                <div class="divide-y" :style="{ borderColor: L.borderSubtle }">
                  <div class="px-5 py-3 flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0" :style="{ background: L.accents.sun.soft }">
                      <StarIcon class="w-3.5 h-3.5" :style="{ color: L.accents.sun.bold }" />
                    </div>
                    <span class="text-[12px] font-medium uppercase tracking-wider flex-1" :style="{ color: L.inkTertiary }">Points spent</span>
                    <span class="text-[14px] font-medium" :style="{ color: L.inkPrimary }">150</span>
                  </div>
                  <div class="px-5 py-3 flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0" :style="{ background: L.accents.sun.soft }">
                      <CalendarDaysIcon class="w-3.5 h-3.5" :style="{ color: L.accents.sun.bold }" />
                    </div>
                    <span class="text-[12px] font-medium uppercase tracking-wider flex-1" :style="{ color: L.inkTertiary }">Purchased</span>
                    <span class="text-[14px] font-medium" :style="{ color: L.inkPrimary }">Today</span>
                  </div>
                  <div class="px-5 py-3 flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0" :style="{ background: L.accents.sun.soft }">
                      <ClockIcon class="w-3.5 h-3.5" :style="{ color: L.accents.sun.bold }" />
                    </div>
                    <span class="text-[12px] font-medium uppercase tracking-wider flex-1" :style="{ color: L.inkTertiary }">Expires</span>
                    <span class="text-[14px] font-medium" :style="{ color: L.inkPrimary }">Apr 30</span>
                  </div>
                  <div class="px-5 py-3 flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0" :style="{ background: L.accents.sun.soft }">
                      <UserIcon class="w-3.5 h-3.5" :style="{ color: L.accents.sun.bold }" />
                    </div>
                    <span class="text-[12px] font-medium uppercase tracking-wider flex-1" :style="{ color: L.inkTertiary }">Redeemed by</span>
                    <span class="text-[14px] font-medium" :style="{ color: L.inkPrimary }">Emma</span>
                  </div>
                </div>

                <!-- Action -->
                <div class="px-5 py-4">
                  <button
                    class="w-full h-9 rounded-full border text-[13px] font-medium transition-colors"
                    :style="{ borderColor: L.accents.sun.bold, color: L.accents.sun.bold, background: 'transparent' }"
                  >View details</button>
                </div>
              </div>

              <!-- A · Task detail (light) -->
              <div
                class="rounded-[20px] border flex flex-col"
                :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle, boxShadow: SH_LT }"
              >
                <div class="px-5 pt-5 pb-4 flex items-center gap-3">
                  <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0" :style="{ background: L.accents.mint.soft }">
                    <CheckBadgeIcon class="w-4 h-4" :style="{ color: L.accents.mint.bold }" />
                  </div>
                  <p class="text-[15px] font-semibold leading-snug" :style="{ color: L.inkPrimary }">Do laundry</p>
                </div>

                <div class="divide-y" :style="{ borderColor: L.borderSubtle }">
                  <div class="px-5 py-3 flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0" :style="{ background: L.accents.mint.soft }">
                      <CalendarDaysIcon class="w-3.5 h-3.5" :style="{ color: L.accents.mint.bold }" />
                    </div>
                    <span class="text-[12px] font-medium uppercase tracking-wider flex-1" :style="{ color: L.inkTertiary }">Due</span>
                    <span class="text-[14px] font-medium" :style="{ color: L.inkPrimary }">Tomorrow 6pm</span>
                  </div>
                  <div class="px-5 py-3 flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0" :style="{ background: L.accents.peach.soft }">
                      <FlagIcon class="w-3.5 h-3.5" :style="{ color: L.accents.peach.bold }" />
                    </div>
                    <span class="text-[12px] font-medium uppercase tracking-wider flex-1" :style="{ color: L.inkTertiary }">Priority</span>
                    <span class="text-[14px] font-medium" :style="{ color: L.accents.peach.bold }">High</span>
                  </div>
                  <div class="px-5 py-3 flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0" :style="{ background: L.accents.sun.soft }">
                      <StarIcon class="w-3.5 h-3.5" :style="{ color: L.accents.sun.bold }" />
                    </div>
                    <span class="text-[12px] font-medium uppercase tracking-wider flex-1" :style="{ color: L.inkTertiary }">Points</span>
                    <span class="text-[14px] font-medium" :style="{ color: L.inkPrimary }">5</span>
                  </div>
                  <div class="px-5 py-3 flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0" :style="{ background: L.accents.mint.soft }">
                      <UserIcon class="w-3.5 h-3.5" :style="{ color: L.accents.mint.bold }" />
                    </div>
                    <span class="text-[12px] font-medium uppercase tracking-wider flex-1" :style="{ color: L.inkTertiary }">Assigned to</span>
                    <span class="text-[14px] font-medium" :style="{ color: L.inkPrimary }">Greg</span>
                  </div>
                  <div class="px-5 py-3 flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0" :style="{ background: L.accents.lavender.soft }">
                      <ArrowPathIcon class="w-3.5 h-3.5" :style="{ color: L.accents.lavender.bold }" />
                    </div>
                    <span class="text-[12px] font-medium uppercase tracking-wider flex-1" :style="{ color: L.inkTertiary }">Recurring</span>
                    <span class="text-[14px] font-medium" :style="{ color: L.inkPrimary }">Weekly</span>
                  </div>
                </div>

                <!-- ActionPair: Edit / Complete -->
                <div class="px-5 py-4 flex gap-2">
                  <button
                    class="flex-1 h-9 rounded-full border text-[13px] font-medium transition-colors flex items-center justify-center gap-1.5"
                    :style="{ borderColor: L.borderStrong, color: L.inkSecondary, background: 'transparent' }"
                  >
                    <PencilSquareIcon class="w-3.5 h-3.5" />
                    Edit
                  </button>
                  <button
                    class="flex-1 h-9 rounded-full text-[13px] font-medium transition-colors flex items-center justify-center gap-1.5"
                    :style="{ background: L.accents.mint.bold, color: L.inkInverse }"
                  >
                    <CheckIcon class="w-3.5 h-3.5" />
                    Complete
                  </button>
                </div>
              </div>

              <!-- A · Event detail (light) -->
              <div
                class="rounded-[20px] border flex flex-col"
                :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle, boxShadow: SH_LT }"
              >
                <div class="px-5 pt-5 pb-4 flex items-center gap-3">
                  <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0" :style="{ background: L.accents.lavender.soft }">
                    <CalendarDaysIcon class="w-4 h-4" :style="{ color: L.accents.lavender.bold }" />
                  </div>
                  <p class="text-[15px] font-semibold leading-snug" :style="{ color: L.inkPrimary }">Parent-teacher · Emma</p>
                </div>

                <div class="divide-y" :style="{ borderColor: L.borderSubtle }">
                  <div class="px-5 py-3 flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0" :style="{ background: L.accents.lavender.soft }">
                      <CalendarDaysIcon class="w-3.5 h-3.5" :style="{ color: L.accents.lavender.bold }" />
                    </div>
                    <span class="text-[12px] font-medium uppercase tracking-wider flex-1" :style="{ color: L.inkTertiary }">When</span>
                    <span class="text-[14px] font-medium" :style="{ color: L.inkPrimary }">Thu 2pm</span>
                  </div>
                  <div class="px-5 py-3 flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0" :style="{ background: L.accents.lavender.soft }">
                      <MapPinIcon class="w-3.5 h-3.5" :style="{ color: L.accents.lavender.bold }" />
                    </div>
                    <span class="text-[12px] font-medium uppercase tracking-wider flex-1" :style="{ color: L.inkTertiary }">Where</span>
                    <span class="text-[14px] font-medium" :style="{ color: L.inkPrimary }">Lincoln Middle</span>
                  </div>
                  <div class="px-5 py-3 flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0" :style="{ background: L.accents.lavender.soft }">
                      <ClockIcon class="w-3.5 h-3.5" :style="{ color: L.accents.lavender.bold }" />
                    </div>
                    <span class="text-[12px] font-medium uppercase tracking-wider flex-1" :style="{ color: L.inkTertiary }">Duration</span>
                    <span class="text-[14px] font-medium" :style="{ color: L.inkPrimary }">30 min</span>
                  </div>
                  <div class="px-5 py-3 flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0" :style="{ background: L.accents.lavender.soft }">
                      <UserGroupIcon class="w-3.5 h-3.5" :style="{ color: L.accents.lavender.bold }" />
                    </div>
                    <span class="text-[12px] font-medium uppercase tracking-wider flex-1" :style="{ color: L.inkTertiary }">Attendees</span>
                    <span class="text-[14px] font-medium" :style="{ color: L.inkPrimary }">2</span>
                  </div>
                </div>

                <div class="px-5 py-4">
                  <button
                    class="w-full h-9 rounded-full border text-[13px] font-medium transition-colors flex items-center justify-center gap-1.5"
                    :style="{ borderColor: L.accents.lavender.bold, color: L.accents.lavender.bold, background: 'transparent' }"
                  >
                    <ArrowTopRightOnSquareIcon class="w-3.5 h-3.5" />
                    Open in Calendar
                  </button>
                </div>
              </div>
            </div>

            <p class="text-[11px]" :style="{ color: L.inkTertiary }">
              375px — cards stack single-column; action buttons span full width; icon circles stay 28px.
            </p>
          </div>

          <!-- ── DARK PANEL ── -->
          <div class="rounded-2xl border p-6 space-y-6" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">

              <!-- A · Reward purchase (dark) -->
              <div
                class="rounded-[20px] border flex flex-col"
                :style="{ background: D.surfaceRaised, borderColor: D.borderSubtle, boxShadow: SH_DK }"
              >
                <div class="px-5 pt-5 pb-4 flex items-center gap-3">
                  <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0" :style="{ background: D.accents.sun.soft }">
                    <TicketIcon class="w-4 h-4" :style="{ color: D.accents.sun.bold }" />
                  </div>
                  <p class="text-[15px] font-semibold leading-snug" :style="{ color: D.inkPrimary }">Movie night pass</p>
                </div>
                <div class="divide-y" :style="{ borderColor: D.borderSubtle }">
                  <div class="px-5 py-3 flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0" :style="{ background: D.accents.sun.soft }">
                      <StarIcon class="w-3.5 h-3.5" :style="{ color: D.accents.sun.bold }" />
                    </div>
                    <span class="text-[12px] font-medium uppercase tracking-wider flex-1" :style="{ color: D.inkTertiary }">Points spent</span>
                    <span class="text-[14px] font-medium" :style="{ color: D.inkPrimary }">150</span>
                  </div>
                  <div class="px-5 py-3 flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0" :style="{ background: D.accents.sun.soft }">
                      <CalendarDaysIcon class="w-3.5 h-3.5" :style="{ color: D.accents.sun.bold }" />
                    </div>
                    <span class="text-[12px] font-medium uppercase tracking-wider flex-1" :style="{ color: D.inkTertiary }">Purchased</span>
                    <span class="text-[14px] font-medium" :style="{ color: D.inkPrimary }">Today</span>
                  </div>
                  <div class="px-5 py-3 flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0" :style="{ background: D.accents.sun.soft }">
                      <ClockIcon class="w-3.5 h-3.5" :style="{ color: D.accents.sun.bold }" />
                    </div>
                    <span class="text-[12px] font-medium uppercase tracking-wider flex-1" :style="{ color: D.inkTertiary }">Expires</span>
                    <span class="text-[14px] font-medium" :style="{ color: D.inkPrimary }">Apr 30</span>
                  </div>
                  <div class="px-5 py-3 flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0" :style="{ background: D.accents.sun.soft }">
                      <UserIcon class="w-3.5 h-3.5" :style="{ color: D.accents.sun.bold }" />
                    </div>
                    <span class="text-[12px] font-medium uppercase tracking-wider flex-1" :style="{ color: D.inkTertiary }">Redeemed by</span>
                    <span class="text-[14px] font-medium" :style="{ color: D.inkPrimary }">Emma</span>
                  </div>
                </div>
                <div class="px-5 py-4">
                  <button
                    class="w-full h-9 rounded-full border text-[13px] font-medium"
                    :style="{ borderColor: D.accents.sun.bold, color: D.accents.sun.bold, background: 'transparent' }"
                  >View details</button>
                </div>
              </div>

              <!-- A · Task detail (dark) -->
              <div
                class="rounded-[20px] border flex flex-col"
                :style="{ background: D.surfaceRaised, borderColor: D.borderSubtle, boxShadow: SH_DK }"
              >
                <div class="px-5 pt-5 pb-4 flex items-center gap-3">
                  <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0" :style="{ background: D.accents.mint.soft }">
                    <CheckBadgeIcon class="w-4 h-4" :style="{ color: D.accents.mint.bold }" />
                  </div>
                  <p class="text-[15px] font-semibold leading-snug" :style="{ color: D.inkPrimary }">Do laundry</p>
                </div>
                <div class="divide-y" :style="{ borderColor: D.borderSubtle }">
                  <div class="px-5 py-3 flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0" :style="{ background: D.accents.mint.soft }">
                      <CalendarDaysIcon class="w-3.5 h-3.5" :style="{ color: D.accents.mint.bold }" />
                    </div>
                    <span class="text-[12px] font-medium uppercase tracking-wider flex-1" :style="{ color: D.inkTertiary }">Due</span>
                    <span class="text-[14px] font-medium" :style="{ color: D.inkPrimary }">Tomorrow 6pm</span>
                  </div>
                  <div class="px-5 py-3 flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0" :style="{ background: D.accents.peach.soft }">
                      <FlagIcon class="w-3.5 h-3.5" :style="{ color: D.accents.peach.bold }" />
                    </div>
                    <span class="text-[12px] font-medium uppercase tracking-wider flex-1" :style="{ color: D.inkTertiary }">Priority</span>
                    <span class="text-[14px] font-medium" :style="{ color: D.accents.peach.bold }">High</span>
                  </div>
                  <div class="px-5 py-3 flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0" :style="{ background: D.accents.sun.soft }">
                      <StarIcon class="w-3.5 h-3.5" :style="{ color: D.accents.sun.bold }" />
                    </div>
                    <span class="text-[12px] font-medium uppercase tracking-wider flex-1" :style="{ color: D.inkTertiary }">Points</span>
                    <span class="text-[14px] font-medium" :style="{ color: D.inkPrimary }">5</span>
                  </div>
                  <div class="px-5 py-3 flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0" :style="{ background: D.accents.mint.soft }">
                      <UserIcon class="w-3.5 h-3.5" :style="{ color: D.accents.mint.bold }" />
                    </div>
                    <span class="text-[12px] font-medium uppercase tracking-wider flex-1" :style="{ color: D.inkTertiary }">Assigned to</span>
                    <span class="text-[14px] font-medium" :style="{ color: D.inkPrimary }">Greg</span>
                  </div>
                  <div class="px-5 py-3 flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0" :style="{ background: D.accents.lavender.soft }">
                      <ArrowPathIcon class="w-3.5 h-3.5" :style="{ color: D.accents.lavender.bold }" />
                    </div>
                    <span class="text-[12px] font-medium uppercase tracking-wider flex-1" :style="{ color: D.inkTertiary }">Recurring</span>
                    <span class="text-[14px] font-medium" :style="{ color: D.inkPrimary }">Weekly</span>
                  </div>
                </div>
                <div class="px-5 py-4 flex gap-2">
                  <button
                    class="flex-1 h-9 rounded-full border text-[13px] font-medium flex items-center justify-center gap-1.5"
                    :style="{ borderColor: D.borderStrong, color: D.inkSecondary, background: 'transparent' }"
                  >
                    <PencilSquareIcon class="w-3.5 h-3.5" />
                    Edit
                  </button>
                  <button
                    class="flex-1 h-9 rounded-full text-[13px] font-medium flex items-center justify-center gap-1.5"
                    :style="{ background: D.accents.mint.bold, color: D.inkInverse }"
                  >
                    <CheckIcon class="w-3.5 h-3.5" />
                    Complete
                  </button>
                </div>
              </div>

              <!-- A · Event detail (dark) -->
              <div
                class="rounded-[20px] border flex flex-col"
                :style="{ background: D.surfaceRaised, borderColor: D.borderSubtle, boxShadow: SH_DK }"
              >
                <div class="px-5 pt-5 pb-4 flex items-center gap-3">
                  <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0" :style="{ background: D.accents.lavender.soft }">
                    <CalendarDaysIcon class="w-4 h-4" :style="{ color: D.accents.lavender.bold }" />
                  </div>
                  <p class="text-[15px] font-semibold leading-snug" :style="{ color: D.inkPrimary }">Parent-teacher · Emma</p>
                </div>
                <div class="divide-y" :style="{ borderColor: D.borderSubtle }">
                  <div class="px-5 py-3 flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0" :style="{ background: D.accents.lavender.soft }">
                      <CalendarDaysIcon class="w-3.5 h-3.5" :style="{ color: D.accents.lavender.bold }" />
                    </div>
                    <span class="text-[12px] font-medium uppercase tracking-wider flex-1" :style="{ color: D.inkTertiary }">When</span>
                    <span class="text-[14px] font-medium" :style="{ color: D.inkPrimary }">Thu 2pm</span>
                  </div>
                  <div class="px-5 py-3 flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0" :style="{ background: D.accents.lavender.soft }">
                      <MapPinIcon class="w-3.5 h-3.5" :style="{ color: D.accents.lavender.bold }" />
                    </div>
                    <span class="text-[12px] font-medium uppercase tracking-wider flex-1" :style="{ color: D.inkTertiary }">Where</span>
                    <span class="text-[14px] font-medium" :style="{ color: D.inkPrimary }">Lincoln Middle</span>
                  </div>
                  <div class="px-5 py-3 flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0" :style="{ background: D.accents.lavender.soft }">
                      <ClockIcon class="w-3.5 h-3.5" :style="{ color: D.accents.lavender.bold }" />
                    </div>
                    <span class="text-[12px] font-medium uppercase tracking-wider flex-1" :style="{ color: D.inkTertiary }">Duration</span>
                    <span class="text-[14px] font-medium" :style="{ color: D.inkPrimary }">30 min</span>
                  </div>
                  <div class="px-5 py-3 flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0" :style="{ background: D.accents.lavender.soft }">
                      <UserGroupIcon class="w-3.5 h-3.5" :style="{ color: D.accents.lavender.bold }" />
                    </div>
                    <span class="text-[12px] font-medium uppercase tracking-wider flex-1" :style="{ color: D.inkTertiary }">Attendees</span>
                    <span class="text-[14px] font-medium" :style="{ color: D.inkPrimary }">2</span>
                  </div>
                </div>
                <div class="px-5 py-4">
                  <button
                    class="w-full h-9 rounded-full border text-[13px] font-medium flex items-center justify-center gap-1.5"
                    :style="{ borderColor: D.accents.lavender.bold, color: D.accents.lavender.bold, background: 'transparent' }"
                  >
                    <ArrowTopRightOnSquareIcon class="w-3.5 h-3.5" />
                    Open in Calendar
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </VariantFrame>
    </section>

    <!-- ═══════════════════════════════════════════════════════════════
         VARIANT B — Flat, iconography-heavy 2-line blocks
         ════════════════════════════════════════════════════════════════ -->
    <section class="mb-20">
      <VariantFrame
        label="B"
        caption="Flat, iconography-heavy — no dividers. Each fact is a 2-line block: small uppercase label on top, large accent-bold value below, icon to the left. More scannable, feels like a hero summary tile."
      >
        <div class="w-full space-y-10">

          <!-- ── LIGHT PANEL ── -->
          <div class="rounded-2xl border p-6 space-y-6" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">

              <!-- B · Reward purchase (light) -->
              <div
                class="rounded-[20px] border p-5 flex flex-col gap-4"
                :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle, boxShadow: SH_LT }"
              >
                <!-- Title row -->
                <div class="flex items-center gap-2.5">
                  <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0" :style="{ background: L.accents.sun.soft }">
                    <TicketIcon class="w-4 h-4" :style="{ color: L.accents.sun.bold }" />
                  </div>
                  <p class="text-[16px] font-semibold" :style="{ color: L.inkPrimary }">Movie night pass</p>
                </div>

                <div class="grid grid-cols-2 gap-3">
                  <div class="flex gap-2 items-start">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5" :style="{ background: L.accents.sun.soft }">
                      <StarIcon class="w-3.5 h-3.5" :style="{ color: L.accents.sun.bold }" />
                    </div>
                    <div>
                      <p class="text-[10px] font-semibold uppercase tracking-wider" :style="{ color: L.inkTertiary }">Points</p>
                      <p class="text-[15px] font-semibold" :style="{ color: L.accents.sun.bold }">150</p>
                    </div>
                  </div>
                  <div class="flex gap-2 items-start">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5" :style="{ background: L.accents.sun.soft }">
                      <CalendarDaysIcon class="w-3.5 h-3.5" :style="{ color: L.accents.sun.bold }" />
                    </div>
                    <div>
                      <p class="text-[10px] font-semibold uppercase tracking-wider" :style="{ color: L.inkTertiary }">Purchased</p>
                      <p class="text-[15px] font-semibold" :style="{ color: L.inkPrimary }">Today</p>
                    </div>
                  </div>
                  <div class="flex gap-2 items-start">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5" :style="{ background: L.accents.sun.soft }">
                      <ClockIcon class="w-3.5 h-3.5" :style="{ color: L.accents.sun.bold }" />
                    </div>
                    <div>
                      <p class="text-[10px] font-semibold uppercase tracking-wider" :style="{ color: L.inkTertiary }">Expires</p>
                      <p class="text-[15px] font-semibold" :style="{ color: L.inkPrimary }">Apr 30</p>
                    </div>
                  </div>
                  <div class="flex gap-2 items-start">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5" :style="{ background: L.accents.sun.soft }">
                      <UserIcon class="w-3.5 h-3.5" :style="{ color: L.accents.sun.bold }" />
                    </div>
                    <div>
                      <p class="text-[10px] font-semibold uppercase tracking-wider" :style="{ color: L.inkTertiary }">Redeemed by</p>
                      <p class="text-[15px] font-semibold" :style="{ color: L.inkPrimary }">Emma</p>
                    </div>
                  </div>
                </div>

                <button
                  class="mt-auto w-full h-9 rounded-full border text-[13px] font-medium"
                  :style="{ borderColor: L.accents.sun.bold, color: L.accents.sun.bold, background: 'transparent' }"
                >View details</button>
              </div>

              <!-- B · Task detail (light) -->
              <div
                class="rounded-[20px] border p-5 flex flex-col gap-4"
                :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle, boxShadow: SH_LT }"
              >
                <div class="flex items-center gap-2.5">
                  <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0" :style="{ background: L.accents.mint.soft }">
                    <CheckBadgeIcon class="w-4 h-4" :style="{ color: L.accents.mint.bold }" />
                  </div>
                  <p class="text-[16px] font-semibold" :style="{ color: L.inkPrimary }">Do laundry</p>
                </div>

                <div class="grid grid-cols-2 gap-3">
                  <div class="flex gap-2 items-start">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5" :style="{ background: L.accents.mint.soft }">
                      <CalendarDaysIcon class="w-3.5 h-3.5" :style="{ color: L.accents.mint.bold }" />
                    </div>
                    <div>
                      <p class="text-[10px] font-semibold uppercase tracking-wider" :style="{ color: L.inkTertiary }">Due</p>
                      <p class="text-[15px] font-semibold" :style="{ color: L.inkPrimary }">Tmrw 6pm</p>
                    </div>
                  </div>
                  <div class="flex gap-2 items-start">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5" :style="{ background: L.accents.peach.soft }">
                      <FlagIcon class="w-3.5 h-3.5" :style="{ color: L.accents.peach.bold }" />
                    </div>
                    <div>
                      <p class="text-[10px] font-semibold uppercase tracking-wider" :style="{ color: L.inkTertiary }">Priority</p>
                      <p class="text-[15px] font-semibold" :style="{ color: L.accents.peach.bold }">High</p>
                    </div>
                  </div>
                  <div class="flex gap-2 items-start">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5" :style="{ background: L.accents.sun.soft }">
                      <StarIcon class="w-3.5 h-3.5" :style="{ color: L.accents.sun.bold }" />
                    </div>
                    <div>
                      <p class="text-[10px] font-semibold uppercase tracking-wider" :style="{ color: L.inkTertiary }">Points</p>
                      <p class="text-[15px] font-semibold" :style="{ color: L.accents.sun.bold }">5</p>
                    </div>
                  </div>
                  <div class="flex gap-2 items-start">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5" :style="{ background: L.accents.lavender.soft }">
                      <ArrowPathIcon class="w-3.5 h-3.5" :style="{ color: L.accents.lavender.bold }" />
                    </div>
                    <div>
                      <p class="text-[10px] font-semibold uppercase tracking-wider" :style="{ color: L.inkTertiary }">Recurring</p>
                      <p class="text-[15px] font-semibold" :style="{ color: L.inkPrimary }">Weekly</p>
                    </div>
                  </div>
                </div>

                <div class="mt-auto flex gap-2">
                  <button
                    class="flex-1 h-9 rounded-full border text-[13px] font-medium flex items-center justify-center gap-1.5"
                    :style="{ borderColor: L.borderStrong, color: L.inkSecondary, background: 'transparent' }"
                  >
                    <PencilSquareIcon class="w-3.5 h-3.5" />
                    Edit
                  </button>
                  <button
                    class="flex-1 h-9 rounded-full text-[13px] font-medium flex items-center justify-center gap-1.5"
                    :style="{ background: L.accents.mint.bold, color: L.inkInverse }"
                  >
                    <CheckIcon class="w-3.5 h-3.5" />
                    Complete
                  </button>
                </div>
              </div>

              <!-- B · Event detail (light) -->
              <div
                class="rounded-[20px] border p-5 flex flex-col gap-4"
                :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle, boxShadow: SH_LT }"
              >
                <div class="flex items-center gap-2.5">
                  <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0" :style="{ background: L.accents.lavender.soft }">
                    <CalendarDaysIcon class="w-4 h-4" :style="{ color: L.accents.lavender.bold }" />
                  </div>
                  <p class="text-[16px] font-semibold" :style="{ color: L.inkPrimary }">Parent-teacher · Emma</p>
                </div>

                <div class="grid grid-cols-2 gap-3">
                  <div class="flex gap-2 items-start">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5" :style="{ background: L.accents.lavender.soft }">
                      <CalendarDaysIcon class="w-3.5 h-3.5" :style="{ color: L.accents.lavender.bold }" />
                    </div>
                    <div>
                      <p class="text-[10px] font-semibold uppercase tracking-wider" :style="{ color: L.inkTertiary }">When</p>
                      <p class="text-[15px] font-semibold" :style="{ color: L.inkPrimary }">Thu 2pm</p>
                    </div>
                  </div>
                  <div class="flex gap-2 items-start">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5" :style="{ background: L.accents.lavender.soft }">
                      <MapPinIcon class="w-3.5 h-3.5" :style="{ color: L.accents.lavender.bold }" />
                    </div>
                    <div>
                      <p class="text-[10px] font-semibold uppercase tracking-wider" :style="{ color: L.inkTertiary }">Where</p>
                      <p class="text-[15px] font-semibold" :style="{ color: L.inkPrimary }">Lincoln Middle</p>
                    </div>
                  </div>
                  <div class="flex gap-2 items-start">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5" :style="{ background: L.accents.lavender.soft }">
                      <ClockIcon class="w-3.5 h-3.5" :style="{ color: L.accents.lavender.bold }" />
                    </div>
                    <div>
                      <p class="text-[10px] font-semibold uppercase tracking-wider" :style="{ color: L.inkTertiary }">Duration</p>
                      <p class="text-[15px] font-semibold" :style="{ color: L.inkPrimary }">30 min</p>
                    </div>
                  </div>
                  <div class="flex gap-2 items-start">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5" :style="{ background: L.accents.lavender.soft }">
                      <UserGroupIcon class="w-3.5 h-3.5" :style="{ color: L.accents.lavender.bold }" />
                    </div>
                    <div>
                      <p class="text-[10px] font-semibold uppercase tracking-wider" :style="{ color: L.inkTertiary }">Attendees</p>
                      <p class="text-[15px] font-semibold" :style="{ color: L.inkPrimary }">2</p>
                    </div>
                  </div>
                </div>

                <button
                  class="mt-auto w-full h-9 rounded-full border text-[13px] font-medium flex items-center justify-center gap-1.5"
                  :style="{ borderColor: L.accents.lavender.bold, color: L.accents.lavender.bold, background: 'transparent' }"
                >
                  <ArrowTopRightOnSquareIcon class="w-3.5 h-3.5" />
                  Open in Calendar
                </button>
              </div>
            </div>
          </div>

          <!-- ── DARK PANEL ── -->
          <div class="rounded-2xl border p-6 space-y-6" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">

              <!-- B · Reward (dark) -->
              <div
                class="rounded-[20px] border p-5 flex flex-col gap-4"
                :style="{ background: D.surfaceRaised, borderColor: D.borderSubtle, boxShadow: SH_DK }"
              >
                <div class="flex items-center gap-2.5">
                  <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0" :style="{ background: D.accents.sun.soft }">
                    <TicketIcon class="w-4 h-4" :style="{ color: D.accents.sun.bold }" />
                  </div>
                  <p class="text-[16px] font-semibold" :style="{ color: D.inkPrimary }">Movie night pass</p>
                </div>
                <div class="grid grid-cols-2 gap-3">
                  <div class="flex gap-2 items-start">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5" :style="{ background: D.accents.sun.soft }">
                      <StarIcon class="w-3.5 h-3.5" :style="{ color: D.accents.sun.bold }" />
                    </div>
                    <div>
                      <p class="text-[10px] font-semibold uppercase tracking-wider" :style="{ color: D.inkTertiary }">Points</p>
                      <p class="text-[15px] font-semibold" :style="{ color: D.accents.sun.bold }">150</p>
                    </div>
                  </div>
                  <div class="flex gap-2 items-start">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5" :style="{ background: D.accents.sun.soft }">
                      <CalendarDaysIcon class="w-3.5 h-3.5" :style="{ color: D.accents.sun.bold }" />
                    </div>
                    <div>
                      <p class="text-[10px] font-semibold uppercase tracking-wider" :style="{ color: D.inkTertiary }">Purchased</p>
                      <p class="text-[15px] font-semibold" :style="{ color: D.inkPrimary }">Today</p>
                    </div>
                  </div>
                  <div class="flex gap-2 items-start">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5" :style="{ background: D.accents.sun.soft }">
                      <ClockIcon class="w-3.5 h-3.5" :style="{ color: D.accents.sun.bold }" />
                    </div>
                    <div>
                      <p class="text-[10px] font-semibold uppercase tracking-wider" :style="{ color: D.inkTertiary }">Expires</p>
                      <p class="text-[15px] font-semibold" :style="{ color: D.inkPrimary }">Apr 30</p>
                    </div>
                  </div>
                  <div class="flex gap-2 items-start">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5" :style="{ background: D.accents.sun.soft }">
                      <UserIcon class="w-3.5 h-3.5" :style="{ color: D.accents.sun.bold }" />
                    </div>
                    <div>
                      <p class="text-[10px] font-semibold uppercase tracking-wider" :style="{ color: D.inkTertiary }">Redeemed by</p>
                      <p class="text-[15px] font-semibold" :style="{ color: D.inkPrimary }">Emma</p>
                    </div>
                  </div>
                </div>
                <button
                  class="mt-auto w-full h-9 rounded-full border text-[13px] font-medium"
                  :style="{ borderColor: D.accents.sun.bold, color: D.accents.sun.bold, background: 'transparent' }"
                >View details</button>
              </div>

              <!-- B · Task (dark) -->
              <div
                class="rounded-[20px] border p-5 flex flex-col gap-4"
                :style="{ background: D.surfaceRaised, borderColor: D.borderSubtle, boxShadow: SH_DK }"
              >
                <div class="flex items-center gap-2.5">
                  <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0" :style="{ background: D.accents.mint.soft }">
                    <CheckBadgeIcon class="w-4 h-4" :style="{ color: D.accents.mint.bold }" />
                  </div>
                  <p class="text-[16px] font-semibold" :style="{ color: D.inkPrimary }">Do laundry</p>
                </div>
                <div class="grid grid-cols-2 gap-3">
                  <div class="flex gap-2 items-start">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5" :style="{ background: D.accents.mint.soft }">
                      <CalendarDaysIcon class="w-3.5 h-3.5" :style="{ color: D.accents.mint.bold }" />
                    </div>
                    <div>
                      <p class="text-[10px] font-semibold uppercase tracking-wider" :style="{ color: D.inkTertiary }">Due</p>
                      <p class="text-[15px] font-semibold" :style="{ color: D.inkPrimary }">Tmrw 6pm</p>
                    </div>
                  </div>
                  <div class="flex gap-2 items-start">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5" :style="{ background: D.accents.peach.soft }">
                      <FlagIcon class="w-3.5 h-3.5" :style="{ color: D.accents.peach.bold }" />
                    </div>
                    <div>
                      <p class="text-[10px] font-semibold uppercase tracking-wider" :style="{ color: D.inkTertiary }">Priority</p>
                      <p class="text-[15px] font-semibold" :style="{ color: D.accents.peach.bold }">High</p>
                    </div>
                  </div>
                  <div class="flex gap-2 items-start">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5" :style="{ background: D.accents.sun.soft }">
                      <StarIcon class="w-3.5 h-3.5" :style="{ color: D.accents.sun.bold }" />
                    </div>
                    <div>
                      <p class="text-[10px] font-semibold uppercase tracking-wider" :style="{ color: D.inkTertiary }">Points</p>
                      <p class="text-[15px] font-semibold" :style="{ color: D.accents.sun.bold }">5</p>
                    </div>
                  </div>
                  <div class="flex gap-2 items-start">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5" :style="{ background: D.accents.lavender.soft }">
                      <ArrowPathIcon class="w-3.5 h-3.5" :style="{ color: D.accents.lavender.bold }" />
                    </div>
                    <div>
                      <p class="text-[10px] font-semibold uppercase tracking-wider" :style="{ color: D.inkTertiary }">Recurring</p>
                      <p class="text-[15px] font-semibold" :style="{ color: D.inkPrimary }">Weekly</p>
                    </div>
                  </div>
                </div>
                <div class="mt-auto flex gap-2">
                  <button
                    class="flex-1 h-9 rounded-full border text-[13px] font-medium flex items-center justify-center gap-1.5"
                    :style="{ borderColor: D.borderStrong, color: D.inkSecondary, background: 'transparent' }"
                  >
                    <PencilSquareIcon class="w-3.5 h-3.5" />
                    Edit
                  </button>
                  <button
                    class="flex-1 h-9 rounded-full text-[13px] font-medium flex items-center justify-center gap-1.5"
                    :style="{ background: D.accents.mint.bold, color: D.inkInverse }"
                  >
                    <CheckIcon class="w-3.5 h-3.5" />
                    Complete
                  </button>
                </div>
              </div>

              <!-- B · Event (dark) -->
              <div
                class="rounded-[20px] border p-5 flex flex-col gap-4"
                :style="{ background: D.surfaceRaised, borderColor: D.borderSubtle, boxShadow: SH_DK }"
              >
                <div class="flex items-center gap-2.5">
                  <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0" :style="{ background: D.accents.lavender.soft }">
                    <CalendarDaysIcon class="w-4 h-4" :style="{ color: D.accents.lavender.bold }" />
                  </div>
                  <p class="text-[16px] font-semibold" :style="{ color: D.inkPrimary }">Parent-teacher · Emma</p>
                </div>
                <div class="grid grid-cols-2 gap-3">
                  <div class="flex gap-2 items-start">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5" :style="{ background: D.accents.lavender.soft }">
                      <CalendarDaysIcon class="w-3.5 h-3.5" :style="{ color: D.accents.lavender.bold }" />
                    </div>
                    <div>
                      <p class="text-[10px] font-semibold uppercase tracking-wider" :style="{ color: D.inkTertiary }">When</p>
                      <p class="text-[15px] font-semibold" :style="{ color: D.inkPrimary }">Thu 2pm</p>
                    </div>
                  </div>
                  <div class="flex gap-2 items-start">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5" :style="{ background: D.accents.lavender.soft }">
                      <MapPinIcon class="w-3.5 h-3.5" :style="{ color: D.accents.lavender.bold }" />
                    </div>
                    <div>
                      <p class="text-[10px] font-semibold uppercase tracking-wider" :style="{ color: D.inkTertiary }">Where</p>
                      <p class="text-[15px] font-semibold" :style="{ color: D.inkPrimary }">Lincoln Middle</p>
                    </div>
                  </div>
                  <div class="flex gap-2 items-start">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5" :style="{ background: D.accents.lavender.soft }">
                      <ClockIcon class="w-3.5 h-3.5" :style="{ color: D.accents.lavender.bold }" />
                    </div>
                    <div>
                      <p class="text-[10px] font-semibold uppercase tracking-wider" :style="{ color: D.inkTertiary }">Duration</p>
                      <p class="text-[15px] font-semibold" :style="{ color: D.inkPrimary }">30 min</p>
                    </div>
                  </div>
                  <div class="flex gap-2 items-start">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5" :style="{ background: D.accents.lavender.soft }">
                      <UserGroupIcon class="w-3.5 h-3.5" :style="{ color: D.accents.lavender.bold }" />
                    </div>
                    <div>
                      <p class="text-[10px] font-semibold uppercase tracking-wider" :style="{ color: D.inkTertiary }">Attendees</p>
                      <p class="text-[15px] font-semibold" :style="{ color: D.inkPrimary }">2</p>
                    </div>
                  </div>
                </div>
                <button
                  class="mt-auto w-full h-9 rounded-full border text-[13px] font-medium flex items-center justify-center gap-1.5"
                  :style="{ borderColor: D.accents.lavender.bold, color: D.accents.lavender.bold, background: 'transparent' }"
                >
                  <ArrowTopRightOnSquareIcon class="w-3.5 h-3.5" />
                  Open in Calendar
                </button>
              </div>
            </div>
          </div>
        </div>
      </VariantFrame>
    </section>

    <!-- ═══════════════════════════════════════════════════════════════
         VARIANT C — Two-column grid of fact pairs
         ════════════════════════════════════════════════════════════════ -->
    <section class="mb-20">
      <VariantFrame
        label="C"
        caption="Two-column grid of fact pairs — facts in a 2-col grid (icon+label left, value right of each column). Actions docked as ActionPair at the bottom. Best for dense receipts with 6+ metadata fields."
      >
        <div class="w-full space-y-10">

          <!-- ── LIGHT PANEL ── -->
          <div class="rounded-2xl border p-6 space-y-6" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">

              <!-- C · Reward purchase (light) -->
              <div
                class="rounded-[20px] border p-5 flex flex-col gap-4"
                :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle, boxShadow: SH_LT }"
              >
                <!-- Header -->
                <div>
                  <div class="flex items-center gap-2 mb-0.5">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0" :style="{ background: L.accents.sun.soft }">
                      <TicketIcon class="w-4 h-4" :style="{ color: L.accents.sun.bold }" />
                    </div>
                    <p class="text-[16px] font-semibold" :style="{ color: L.inkPrimary }">Movie night pass</p>
                  </div>
                  <p class="text-[11px] uppercase tracking-wider font-medium ml-9" :style="{ color: L.inkTertiary }">Purchase confirmation</p>
                </div>

                <!-- 2-column fact grid -->
                <div class="grid grid-cols-2 gap-x-3 gap-y-3">
                  <div class="rounded-xl p-3 flex flex-col gap-1" :style="{ background: L.surfaceSunken }">
                    <div class="flex items-center gap-1.5">
                      <StarIcon class="w-3.5 h-3.5 flex-shrink-0" :style="{ color: L.inkTertiary }" />
                      <span class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: L.inkTertiary }">Points</span>
                    </div>
                    <p class="text-[15px] font-semibold" :style="{ color: L.inkPrimary }">150</p>
                  </div>
                  <div class="rounded-xl p-3 flex flex-col gap-1" :style="{ background: L.surfaceSunken }">
                    <div class="flex items-center gap-1.5">
                      <CalendarDaysIcon class="w-3.5 h-3.5 flex-shrink-0" :style="{ color: L.inkTertiary }" />
                      <span class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: L.inkTertiary }">Purchased</span>
                    </div>
                    <p class="text-[15px] font-semibold" :style="{ color: L.inkPrimary }">Today</p>
                  </div>
                  <div class="rounded-xl p-3 flex flex-col gap-1" :style="{ background: L.surfaceSunken }">
                    <div class="flex items-center gap-1.5">
                      <ClockIcon class="w-3.5 h-3.5 flex-shrink-0" :style="{ color: L.inkTertiary }" />
                      <span class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: L.inkTertiary }">Expires</span>
                    </div>
                    <p class="text-[15px] font-semibold" :style="{ color: L.inkPrimary }">Apr 30</p>
                  </div>
                  <div class="rounded-xl p-3 flex flex-col gap-1" :style="{ background: L.surfaceSunken }">
                    <div class="flex items-center gap-1.5">
                      <UserIcon class="w-3.5 h-3.5 flex-shrink-0" :style="{ color: L.inkTertiary }" />
                      <span class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: L.inkTertiary }">Redeemed by</span>
                    </div>
                    <p class="text-[15px] font-semibold" :style="{ color: L.inkPrimary }">Emma</p>
                  </div>
                </div>

                <!-- Docked action -->
                <div class="pt-1">
                  <button
                    class="w-full h-9 rounded-full border text-[13px] font-medium"
                    :style="{ borderColor: L.accents.sun.bold, color: L.accents.sun.bold, background: 'transparent' }"
                  >View details</button>
                </div>
              </div>

              <!-- C · Task detail (light) -->
              <div
                class="rounded-[20px] border p-5 flex flex-col gap-4"
                :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle, boxShadow: SH_LT }"
              >
                <div>
                  <div class="flex items-center gap-2 mb-0.5">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0" :style="{ background: L.accents.mint.soft }">
                      <CheckBadgeIcon class="w-4 h-4" :style="{ color: L.accents.mint.bold }" />
                    </div>
                    <p class="text-[16px] font-semibold" :style="{ color: L.inkPrimary }">Do laundry</p>
                  </div>
                  <p class="text-[11px] uppercase tracking-wider font-medium ml-9" :style="{ color: L.inkTertiary }">Task · Household</p>
                </div>

                <div class="grid grid-cols-2 gap-x-3 gap-y-3">
                  <div class="rounded-xl p-3 flex flex-col gap-1" :style="{ background: L.surfaceSunken }">
                    <div class="flex items-center gap-1.5">
                      <CalendarDaysIcon class="w-3.5 h-3.5 flex-shrink-0" :style="{ color: L.inkTertiary }" />
                      <span class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: L.inkTertiary }">Due</span>
                    </div>
                    <p class="text-[15px] font-semibold" :style="{ color: L.inkPrimary }">Tmrw 6pm</p>
                  </div>
                  <div class="rounded-xl p-3 flex flex-col gap-1" :style="{ background: L.accents.peach.soft }">
                    <div class="flex items-center gap-1.5">
                      <FlagIcon class="w-3.5 h-3.5 flex-shrink-0" :style="{ color: L.accents.peach.bold }" />
                      <span class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: L.accents.peach.bold }">Priority</span>
                    </div>
                    <p class="text-[15px] font-semibold" :style="{ color: L.accents.peach.bold }">High</p>
                  </div>
                  <div class="rounded-xl p-3 flex flex-col gap-1" :style="{ background: L.surfaceSunken }">
                    <div class="flex items-center gap-1.5">
                      <StarIcon class="w-3.5 h-3.5 flex-shrink-0" :style="{ color: L.inkTertiary }" />
                      <span class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: L.inkTertiary }">Points</span>
                    </div>
                    <p class="text-[15px] font-semibold" :style="{ color: L.accents.sun.bold }">5</p>
                  </div>
                  <div class="rounded-xl p-3 flex flex-col gap-1" :style="{ background: L.surfaceSunken }">
                    <div class="flex items-center gap-1.5">
                      <UserIcon class="w-3.5 h-3.5 flex-shrink-0" :style="{ color: L.inkTertiary }" />
                      <span class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: L.inkTertiary }">Assigned</span>
                    </div>
                    <p class="text-[15px] font-semibold" :style="{ color: L.inkPrimary }">Greg</p>
                  </div>
                  <div class="col-span-2 rounded-xl p-3 flex flex-col gap-1" :style="{ background: L.accents.lavender.soft }">
                    <div class="flex items-center gap-1.5">
                      <ArrowPathIcon class="w-3.5 h-3.5 flex-shrink-0" :style="{ color: L.accents.lavender.bold }" />
                      <span class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: L.accents.lavender.bold }">Recurring</span>
                    </div>
                    <p class="text-[15px] font-semibold" :style="{ color: L.accents.lavender.bold }">Every week</p>
                  </div>
                </div>

                <!-- ActionPair docked -->
                <div class="pt-1 flex gap-2">
                  <button
                    class="flex-1 h-9 rounded-full border text-[13px] font-medium flex items-center justify-center gap-1.5"
                    :style="{ borderColor: L.borderStrong, color: L.inkSecondary, background: 'transparent' }"
                  >
                    <PencilSquareIcon class="w-3.5 h-3.5" />
                    Edit
                  </button>
                  <button
                    class="flex-1 h-9 rounded-full text-[13px] font-medium flex items-center justify-center gap-1.5"
                    :style="{ background: L.accents.mint.bold, color: L.inkInverse }"
                  >
                    <CheckIcon class="w-3.5 h-3.5" />
                    Complete
                  </button>
                </div>
              </div>

              <!-- C · Event detail (light) -->
              <div
                class="rounded-[20px] border p-5 flex flex-col gap-4"
                :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle, boxShadow: SH_LT }"
              >
                <div>
                  <div class="flex items-center gap-2 mb-0.5">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0" :style="{ background: L.accents.lavender.soft }">
                      <CalendarDaysIcon class="w-4 h-4" :style="{ color: L.accents.lavender.bold }" />
                    </div>
                    <p class="text-[16px] font-semibold" :style="{ color: L.inkPrimary }">Parent-teacher · Emma</p>
                  </div>
                  <p class="text-[11px] uppercase tracking-wider font-medium ml-9" :style="{ color: L.inkTertiary }">Calendar event</p>
                </div>

                <div class="grid grid-cols-2 gap-x-3 gap-y-3">
                  <div class="rounded-xl p-3 flex flex-col gap-1" :style="{ background: L.surfaceSunken }">
                    <div class="flex items-center gap-1.5">
                      <CalendarDaysIcon class="w-3.5 h-3.5 flex-shrink-0" :style="{ color: L.inkTertiary }" />
                      <span class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: L.inkTertiary }">When</span>
                    </div>
                    <p class="text-[15px] font-semibold" :style="{ color: L.inkPrimary }">Thu 2pm</p>
                  </div>
                  <div class="rounded-xl p-3 flex flex-col gap-1" :style="{ background: L.surfaceSunken }">
                    <div class="flex items-center gap-1.5">
                      <ClockIcon class="w-3.5 h-3.5 flex-shrink-0" :style="{ color: L.inkTertiary }" />
                      <span class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: L.inkTertiary }">Duration</span>
                    </div>
                    <p class="text-[15px] font-semibold" :style="{ color: L.inkPrimary }">30 min</p>
                  </div>
                  <div class="col-span-2 rounded-xl p-3 flex flex-col gap-1" :style="{ background: L.surfaceSunken }">
                    <div class="flex items-center gap-1.5">
                      <MapPinIcon class="w-3.5 h-3.5 flex-shrink-0" :style="{ color: L.inkTertiary }" />
                      <span class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: L.inkTertiary }">Where</span>
                    </div>
                    <p class="text-[15px] font-semibold" :style="{ color: L.inkPrimary }">Lincoln Middle School</p>
                  </div>
                  <div class="rounded-xl p-3 flex flex-col gap-1" :style="{ background: L.surfaceSunken }">
                    <div class="flex items-center gap-1.5">
                      <UserGroupIcon class="w-3.5 h-3.5 flex-shrink-0" :style="{ color: L.inkTertiary }" />
                      <span class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: L.inkTertiary }">Attendees</span>
                    </div>
                    <p class="text-[15px] font-semibold" :style="{ color: L.inkPrimary }">2</p>
                  </div>
                  <div class="rounded-xl p-3 flex flex-col gap-1" :style="{ background: L.accents.lavender.soft }">
                    <div class="flex items-center gap-1.5">
                      <TagIcon class="w-3.5 h-3.5 flex-shrink-0" :style="{ color: L.accents.lavender.bold }" />
                      <span class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: L.accents.lavender.bold }">Type</span>
                    </div>
                    <p class="text-[15px] font-semibold" :style="{ color: L.accents.lavender.bold }">School</p>
                  </div>
                </div>

                <div class="pt-1">
                  <button
                    class="w-full h-9 rounded-full border text-[13px] font-medium flex items-center justify-center gap-1.5"
                    :style="{ borderColor: L.accents.lavender.bold, color: L.accents.lavender.bold, background: 'transparent' }"
                  >
                    <ArrowTopRightOnSquareIcon class="w-3.5 h-3.5" />
                    Open in Calendar
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- ── DARK PANEL ── -->
          <div class="rounded-2xl border p-6 space-y-6" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">

              <!-- C · Reward (dark) -->
              <div
                class="rounded-[20px] border p-5 flex flex-col gap-4"
                :style="{ background: D.surfaceRaised, borderColor: D.borderSubtle, boxShadow: SH_DK }"
              >
                <div>
                  <div class="flex items-center gap-2 mb-0.5">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0" :style="{ background: D.accents.sun.soft }">
                      <TicketIcon class="w-4 h-4" :style="{ color: D.accents.sun.bold }" />
                    </div>
                    <p class="text-[16px] font-semibold" :style="{ color: D.inkPrimary }">Movie night pass</p>
                  </div>
                  <p class="text-[11px] uppercase tracking-wider font-medium ml-9" :style="{ color: D.inkTertiary }">Purchase confirmation</p>
                </div>
                <div class="grid grid-cols-2 gap-x-3 gap-y-3">
                  <div class="rounded-xl p-3 flex flex-col gap-1" :style="{ background: D.surfaceSunken }">
                    <div class="flex items-center gap-1.5">
                      <StarIcon class="w-3.5 h-3.5 flex-shrink-0" :style="{ color: D.inkTertiary }" />
                      <span class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: D.inkTertiary }">Points</span>
                    </div>
                    <p class="text-[15px] font-semibold" :style="{ color: D.inkPrimary }">150</p>
                  </div>
                  <div class="rounded-xl p-3 flex flex-col gap-1" :style="{ background: D.surfaceSunken }">
                    <div class="flex items-center gap-1.5">
                      <CalendarDaysIcon class="w-3.5 h-3.5 flex-shrink-0" :style="{ color: D.inkTertiary }" />
                      <span class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: D.inkTertiary }">Purchased</span>
                    </div>
                    <p class="text-[15px] font-semibold" :style="{ color: D.inkPrimary }">Today</p>
                  </div>
                  <div class="rounded-xl p-3 flex flex-col gap-1" :style="{ background: D.surfaceSunken }">
                    <div class="flex items-center gap-1.5">
                      <ClockIcon class="w-3.5 h-3.5 flex-shrink-0" :style="{ color: D.inkTertiary }" />
                      <span class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: D.inkTertiary }">Expires</span>
                    </div>
                    <p class="text-[15px] font-semibold" :style="{ color: D.inkPrimary }">Apr 30</p>
                  </div>
                  <div class="rounded-xl p-3 flex flex-col gap-1" :style="{ background: D.surfaceSunken }">
                    <div class="flex items-center gap-1.5">
                      <UserIcon class="w-3.5 h-3.5 flex-shrink-0" :style="{ color: D.inkTertiary }" />
                      <span class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: D.inkTertiary }">Redeemed by</span>
                    </div>
                    <p class="text-[15px] font-semibold" :style="{ color: D.inkPrimary }">Emma</p>
                  </div>
                </div>
                <div class="pt-1">
                  <button
                    class="w-full h-9 rounded-full border text-[13px] font-medium"
                    :style="{ borderColor: D.accents.sun.bold, color: D.accents.sun.bold, background: 'transparent' }"
                  >View details</button>
                </div>
              </div>

              <!-- C · Task (dark) -->
              <div
                class="rounded-[20px] border p-5 flex flex-col gap-4"
                :style="{ background: D.surfaceRaised, borderColor: D.borderSubtle, boxShadow: SH_DK }"
              >
                <div>
                  <div class="flex items-center gap-2 mb-0.5">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0" :style="{ background: D.accents.mint.soft }">
                      <CheckBadgeIcon class="w-4 h-4" :style="{ color: D.accents.mint.bold }" />
                    </div>
                    <p class="text-[16px] font-semibold" :style="{ color: D.inkPrimary }">Do laundry</p>
                  </div>
                  <p class="text-[11px] uppercase tracking-wider font-medium ml-9" :style="{ color: D.inkTertiary }">Task · Household</p>
                </div>
                <div class="grid grid-cols-2 gap-x-3 gap-y-3">
                  <div class="rounded-xl p-3 flex flex-col gap-1" :style="{ background: D.surfaceSunken }">
                    <div class="flex items-center gap-1.5">
                      <CalendarDaysIcon class="w-3.5 h-3.5 flex-shrink-0" :style="{ color: D.inkTertiary }" />
                      <span class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: D.inkTertiary }">Due</span>
                    </div>
                    <p class="text-[15px] font-semibold" :style="{ color: D.inkPrimary }">Tmrw 6pm</p>
                  </div>
                  <div class="rounded-xl p-3 flex flex-col gap-1" :style="{ background: D.accents.peach.soft }">
                    <div class="flex items-center gap-1.5">
                      <FlagIcon class="w-3.5 h-3.5 flex-shrink-0" :style="{ color: D.accents.peach.bold }" />
                      <span class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: D.accents.peach.bold }">Priority</span>
                    </div>
                    <p class="text-[15px] font-semibold" :style="{ color: D.accents.peach.bold }">High</p>
                  </div>
                  <div class="rounded-xl p-3 flex flex-col gap-1" :style="{ background: D.surfaceSunken }">
                    <div class="flex items-center gap-1.5">
                      <StarIcon class="w-3.5 h-3.5 flex-shrink-0" :style="{ color: D.inkTertiary }" />
                      <span class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: D.inkTertiary }">Points</span>
                    </div>
                    <p class="text-[15px] font-semibold" :style="{ color: D.accents.sun.bold }">5</p>
                  </div>
                  <div class="rounded-xl p-3 flex flex-col gap-1" :style="{ background: D.surfaceSunken }">
                    <div class="flex items-center gap-1.5">
                      <UserIcon class="w-3.5 h-3.5 flex-shrink-0" :style="{ color: D.inkTertiary }" />
                      <span class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: D.inkTertiary }">Assigned</span>
                    </div>
                    <p class="text-[15px] font-semibold" :style="{ color: D.inkPrimary }">Greg</p>
                  </div>
                  <div class="col-span-2 rounded-xl p-3 flex flex-col gap-1" :style="{ background: D.accents.lavender.soft }">
                    <div class="flex items-center gap-1.5">
                      <ArrowPathIcon class="w-3.5 h-3.5 flex-shrink-0" :style="{ color: D.accents.lavender.bold }" />
                      <span class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: D.accents.lavender.bold }">Recurring</span>
                    </div>
                    <p class="text-[15px] font-semibold" :style="{ color: D.accents.lavender.bold }">Every week</p>
                  </div>
                </div>
                <div class="pt-1 flex gap-2">
                  <button
                    class="flex-1 h-9 rounded-full border text-[13px] font-medium flex items-center justify-center gap-1.5"
                    :style="{ borderColor: D.borderStrong, color: D.inkSecondary, background: 'transparent' }"
                  >
                    <PencilSquareIcon class="w-3.5 h-3.5" />
                    Edit
                  </button>
                  <button
                    class="flex-1 h-9 rounded-full text-[13px] font-medium flex items-center justify-center gap-1.5"
                    :style="{ background: D.accents.mint.bold, color: D.inkInverse }"
                  >
                    <CheckIcon class="w-3.5 h-3.5" />
                    Complete
                  </button>
                </div>
              </div>

              <!-- C · Event (dark) -->
              <div
                class="rounded-[20px] border p-5 flex flex-col gap-4"
                :style="{ background: D.surfaceRaised, borderColor: D.borderSubtle, boxShadow: SH_DK }"
              >
                <div>
                  <div class="flex items-center gap-2 mb-0.5">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0" :style="{ background: D.accents.lavender.soft }">
                      <CalendarDaysIcon class="w-4 h-4" :style="{ color: D.accents.lavender.bold }" />
                    </div>
                    <p class="text-[16px] font-semibold" :style="{ color: D.inkPrimary }">Parent-teacher · Emma</p>
                  </div>
                  <p class="text-[11px] uppercase tracking-wider font-medium ml-9" :style="{ color: D.inkTertiary }">Calendar event</p>
                </div>
                <div class="grid grid-cols-2 gap-x-3 gap-y-3">
                  <div class="rounded-xl p-3 flex flex-col gap-1" :style="{ background: D.surfaceSunken }">
                    <div class="flex items-center gap-1.5">
                      <CalendarDaysIcon class="w-3.5 h-3.5 flex-shrink-0" :style="{ color: D.inkTertiary }" />
                      <span class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: D.inkTertiary }">When</span>
                    </div>
                    <p class="text-[15px] font-semibold" :style="{ color: D.inkPrimary }">Thu 2pm</p>
                  </div>
                  <div class="rounded-xl p-3 flex flex-col gap-1" :style="{ background: D.surfaceSunken }">
                    <div class="flex items-center gap-1.5">
                      <ClockIcon class="w-3.5 h-3.5 flex-shrink-0" :style="{ color: D.inkTertiary }" />
                      <span class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: D.inkTertiary }">Duration</span>
                    </div>
                    <p class="text-[15px] font-semibold" :style="{ color: D.inkPrimary }">30 min</p>
                  </div>
                  <div class="col-span-2 rounded-xl p-3 flex flex-col gap-1" :style="{ background: D.surfaceSunken }">
                    <div class="flex items-center gap-1.5">
                      <MapPinIcon class="w-3.5 h-3.5 flex-shrink-0" :style="{ color: D.inkTertiary }" />
                      <span class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: D.inkTertiary }">Where</span>
                    </div>
                    <p class="text-[15px] font-semibold" :style="{ color: D.inkPrimary }">Lincoln Middle School</p>
                  </div>
                  <div class="rounded-xl p-3 flex flex-col gap-1" :style="{ background: D.surfaceSunken }">
                    <div class="flex items-center gap-1.5">
                      <UserGroupIcon class="w-3.5 h-3.5 flex-shrink-0" :style="{ color: D.inkTertiary }" />
                      <span class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: D.inkTertiary }">Attendees</span>
                    </div>
                    <p class="text-[15px] font-semibold" :style="{ color: D.inkPrimary }">2</p>
                  </div>
                  <div class="rounded-xl p-3 flex flex-col gap-1" :style="{ background: D.accents.lavender.soft }">
                    <div class="flex items-center gap-1.5">
                      <TagIcon class="w-3.5 h-3.5 flex-shrink-0" :style="{ color: D.accents.lavender.bold }" />
                      <span class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: D.accents.lavender.bold }">Type</span>
                    </div>
                    <p class="text-[15px] font-semibold" :style="{ color: D.accents.lavender.bold }">School</p>
                  </div>
                </div>
                <div class="pt-1">
                  <button
                    class="w-full h-9 rounded-full border text-[13px] font-medium flex items-center justify-center gap-1.5"
                    :style="{ borderColor: D.accents.lavender.bold, color: D.accents.lavender.bold, background: 'transparent' }"
                  >
                    <ArrowTopRightOnSquareIcon class="w-3.5 h-3.5" />
                    Open in Calendar
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </VariantFrame>
    </section>

    <!-- ═══════════════════════════════════════════════════════════════
         CLAUDE'S PICK CALLOUT
         ════════════════════════════════════════════════════════════════ -->
    <section class="mb-20">
      <div
        class="rounded-2xl border p-6 flex gap-4 items-start"
        :style="{ background: L.accents.lavender.soft, borderColor: '#C0B4E8' }"
      >
        <SparklesIcon class="w-5 h-5 flex-shrink-0 mt-0.5" :style="{ color: L.accents.lavender.bold }" />
        <div>
          <p class="text-sm font-semibold mb-1" :style="{ color: L.accents.lavender.bold }">
            Claude's pick — Variant A
          </p>
          <p class="text-sm leading-relaxed" :style="{ color: L.inkPrimary }">
            Variant A earns the default slot because its bordered-row structure is the most legible at all widths — labels stay anchored left, values right-aligned, and the divider lines create a natural reading cadence that mirrors a real receipt. It covers every context in Kinhold (confirmations, detail sheets, event summaries) without any layout overhead, and the optional outlined-pill action sits cleanly below the rows without competing with the facts. Variants B and C shine in specific situations — B when you want visual warmth and a magazine feel on wider cards, C when you have six or more metadata fields and need density without scrolling — but A should be the drop-in default.
          </p>
        </div>
      </div>
    </section>

    <!-- ═══════════════════════════════════════════════════════════════
         USAGE GUIDE
         ════════════════════════════════════════════════════════════════ -->
    <section class="mb-10">
      <div
        class="rounded-2xl border divide-y"
        :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle }"
      >
        <div class="px-6 py-4">
          <h2 class="text-base font-semibold" :style="{ color: L.inkPrimary }">Usage guide</h2>
          <p class="text-sm mt-1" :style="{ color: L.inkSecondary }">
            Map context to variant. All three share the same icon-in-soft-circle and outlined-pill action conventions — they differ only in information density and visual weight.
          </p>
        </div>

        <!-- Variant A row -->
        <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-[140px_1fr] gap-3">
          <div>
            <span
              class="inline-flex items-center justify-center h-6 min-w-[1.5rem] px-2 rounded-full text-xs font-semibold"
              :style="{ background: L.inkPrimary, color: L.inkInverse }"
            >A — Confirmations</span>
          </div>
          <div class="space-y-1">
            <p class="text-sm font-medium" :style="{ color: L.inkPrimary }">Default for all confirmations and detail sheets</p>
            <p class="text-sm" :style="{ color: L.inkSecondary }">
              Reward purchase confirmation, booking summary, event detail sheet, recurring-task setup review. Use whenever the reader needs to scan facts quickly and the number of fields is 3–6. The divider lines give each fact its own visual lane — no crowding, no ambiguity. Preferred for mobile-first contexts at 375px.
            </p>
          </div>
        </div>

        <!-- Variant B row -->
        <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-[140px_1fr] gap-3">
          <div>
            <span
              class="inline-flex items-center justify-center h-6 min-w-[1.5rem] px-2 rounded-full text-xs font-semibold"
              :style="{ background: L.inkPrimary, color: L.inkInverse }"
            >B — Hero summary</span>
          </div>
          <div class="space-y-1">
            <p class="text-sm font-medium" :style="{ color: L.inkPrimary }">Summary tiles and dashboard widgets with visual warmth</p>
            <p class="text-sm" :style="{ color: L.inkSecondary }">
              Use when each fact deserves equal visual emphasis and you want the card to feel like a hero tile rather than a list. Good for reward store purchase receipts displayed in a grid, or a task-detail pop-over where the large accent values carry meaning (priority color, point color). The 2-column fact grid handles 4 facts naturally; extend to 6 by adding more cells.
            </p>
          </div>
        </div>

        <!-- Variant C row -->
        <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-[140px_1fr] gap-3">
          <div>
            <span
              class="inline-flex items-center justify-center h-6 min-w-[1.5rem] px-2 rounded-full text-xs font-semibold"
              :style="{ background: L.inkPrimary, color: L.inkInverse }"
            >C — Dense sheets</span>
          </div>
          <div class="space-y-1">
            <p class="text-sm font-medium" :style="{ color: L.inkPrimary }">Task or event detail sheets with 6+ metadata fields</p>
            <p class="text-sm" :style="{ color: L.inkSecondary }">
              Use when you need density without scrolling — task detail with due date, priority, points, assignee, recurring rule, and list all visible at once, or an event with date, time, duration, location, attendees, and type. The sunken fact cells allow per-field accent overrides (priority cell in peach-soft, recurring cell in lavender-soft) that visually flag important states. ActionPair docks flush to the bottom.
            </p>
          </div>
        </div>

        <!-- Icon mapping note -->
        <div class="px-6 py-4">
          <p class="text-xs font-semibold uppercase tracking-widest mb-3" :style="{ color: L.inkTertiary }">Icon convention by context</p>
          <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs">
            <div class="flex items-center gap-2 rounded-lg px-3 py-2" :style="{ background: L.accents.sun.soft }">
              <TicketIcon class="w-4 h-4 flex-shrink-0" :style="{ color: L.accents.sun.bold }" />
              <span :style="{ color: L.accents.sun.bold }">Rewards — sun</span>
            </div>
            <div class="flex items-center gap-2 rounded-lg px-3 py-2" :style="{ background: L.accents.mint.soft }">
              <CheckBadgeIcon class="w-4 h-4 flex-shrink-0" :style="{ color: L.accents.mint.bold }" />
              <span :style="{ color: L.accents.mint.bold }">Tasks — mint</span>
            </div>
            <div class="flex items-center gap-2 rounded-lg px-3 py-2" :style="{ background: L.accents.lavender.soft }">
              <CalendarDaysIcon class="w-4 h-4 flex-shrink-0" :style="{ color: L.accents.lavender.bold }" />
              <span :style="{ color: L.accents.lavender.bold }">Events — lavender</span>
            </div>
            <div class="flex items-center gap-2 rounded-lg px-3 py-2" :style="{ background: L.accents.peach.soft }">
              <FlagIcon class="w-4 h-4 flex-shrink-0" :style="{ color: L.accents.peach.bold }" />
              <span :style="{ color: L.accents.peach.bold }">Priority / alerts — peach</span>
            </div>
          </div>
        </div>
      </div>
    </section>

  </ComponentPage>
</template>
