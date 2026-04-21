<script setup>
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import {
  SparklesIcon, CalendarDaysIcon, ClockIcon, MapPinIcon, UserIcon,
  StarIcon, ArrowPathIcon, CheckBadgeIcon, TicketIcon, TrophyIcon,
  FlagIcon, UserGroupIcon, PencilSquareIcon, CheckIcon,
  ArrowTopRightOnSquareIcon, TagIcon,
} from '@heroicons/vue/24/outline'
import KinReceiptCard from '@/components/design-system/KinReceiptCard.vue'

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
    status="chosen"
  >

    <!-- ═══════════════════════════════════════════════════════════════
         VARIANT A — Bordered rows with icon-in-soft-circle
         ════════════════════════════════════════════════════════════════ -->
    <section class="mb-20">
      <VariantFrame
        label="ReceiptCard"
        caption="Bordered rows — icon in 28px accent-soft circle + label + right-aligned value, rows separated by 1px borderSubtle divider. The single locked ReceiptCard shape. Used for confirmations, task detail, event detail, reward purchase."
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
            LOCKED — single shape, no variants
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


    <!-- KIN COMPONENT PREVIEW -->
    <section class="mb-16">
      <VariantFrame label="Kin" caption="KinReceiptCard — proposed extraction. Header + icon rows + optional action.">
        <div class="w-full space-y-10">
          <div class="rounded-2xl border p-6" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest mb-4" :style="{ color: L.inkTertiary }">Light mode</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <KinReceiptCard
                title="Movie night pass"
                :title-icon="TicketIcon"
                accent-color="sun"
                :rows="[
                  { label: 'Points spent', value: '150', icon: StarIcon },
                  { label: 'Purchased',    value: 'Today', icon: CalendarDaysIcon },
                  { label: 'Expires',      value: 'Apr 30', icon: ClockIcon },
                  { label: 'Redeemed by',  value: 'Emma',   icon: UserIcon },
                ]"
                action-label="View details"
              />
              <KinReceiptCard
                title="Do laundry"
                :title-icon="CheckBadgeIcon"
                accent-color="mint"
                :rows="[
                  { label: 'Due',      value: 'Tomorrow 6pm', icon: CalendarDaysIcon },
                  { label: 'Priority', value: 'High',          icon: FlagIcon, iconAccent: 'peach' },
                  { label: 'Points',   value: '5',             icon: StarIcon, iconAccent: 'sun' },
                ]"
                action-label="Mark complete"
              />
            </div>
          </div>

          <div class="dark rounded-2xl border p-6" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest mb-4" :style="{ color: D.inkTertiary }">Dark mode</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <KinReceiptCard
                title="Movie night pass"
                :title-icon="TicketIcon"
                accent-color="sun"
                :rows="[
                  { label: 'Points spent', value: '150', icon: StarIcon },
                  { label: 'Purchased',    value: 'Today', icon: CalendarDaysIcon },
                  { label: 'Expires',      value: 'Apr 30', icon: ClockIcon },
                  { label: 'Redeemed by',  value: 'Emma',   icon: UserIcon },
                ]"
                action-label="View details"
              />
              <KinReceiptCard
                title="Do laundry"
                :title-icon="CheckBadgeIcon"
                accent-color="mint"
                :rows="[
                  { label: 'Due',      value: 'Tomorrow 6pm', icon: CalendarDaysIcon },
                  { label: 'Priority', value: 'High',          icon: FlagIcon, iconAccent: 'peach' },
                  { label: 'Points',   value: '5',             icon: StarIcon, iconAccent: 'sun' },
                ]"
                action-label="Mark complete"
              />
            </div>
          </div>
        </div>
      </VariantFrame>
    </section>

  </ComponentPage>
</template>
