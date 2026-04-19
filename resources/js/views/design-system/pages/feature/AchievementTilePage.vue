<script setup>
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import {
  SparklesIcon, TrophyIcon, StarIcon, HeartIcon, FireIcon,
  AcademicCapIcon, QuestionMarkCircleIcon, LockClosedIcon, CheckBadgeIcon,
} from '@heroicons/vue/24/solid'
import { SparklesIcon as SparklesOutlineIcon } from '@heroicons/vue/24/outline'

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
  status: {
    success: { soft: '#E1F0E7', bold: '#4D8C6A' },
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
  status: {
    success: { soft: '#1C3A2A', bold: '#6CC498' },
  },
}

// ── SVG arc progress helpers ──────────────────────────────────────────────────
// Circle radius for the 80×80 icon container arc rings. cx=cy=40, r=32.
const RING_R    = 32
const RING_CIRC = 2 * Math.PI * RING_R  // ≈ 201.06

function arcOffset(pct) {
  return RING_CIRC * (1 - pct)
}

// The six canonical tile definitions used across all three variants
const TILES = {
  earnedA:     { title: 'Week Warrior',    desc: '7 tasks in a row',     sub: 'Earned 2d ago',   accent: 'sun',      icon: 'trophy' },
  earnedB:     { title: 'Kindness Captain',desc: 'Gave 10 kudos',        sub: 'Earned 5d ago',   accent: 'peach',    icon: 'heart'  },
  progressA:   { title: 'Meal Planner',    desc: 'Plan 20 meals',        sub: '12/20',           accent: 'mint',     icon: 'cap',   pct: 0.65 },
  progressB:   { title: 'Streak Starter',  desc: '3-day streak, 7 to go',sub: '3/10 days',       accent: 'peach',    icon: 'fire',  pct: 0.30 },
  locked:      { title: 'Century Club',    desc: 'Complete 100 tasks',   sub: 'Locked',          accent: 'lavender', icon: 'star'   },
  hidden:      { title: '???',             desc: 'Keep playing to unlock',sub: 'Hidden',          accent: null,       icon: 'qmark'  },
}
</script>

<template>
  <ComponentPage
    title="5.8 AchievementTile"
    description="Trophy-case tile with three data-driven states: locked, in-progress, and earned. Three surface treatments — Flat (A), Iridescent Gradient (B), and Hexagonal Steam-style badge (C) — each driving the same state machine. Hidden achievements render as '???' until earned."
    status="scaffolded"
  >

    <!-- ═══════════════════════════════════════════════════════════════
         VARIANT A — Flat surface with accent icon
         surfaceRaised card, 64px trophy icon centered, title + desc below.
         Locked = inkTertiary icon + 40% opacity + "Locked" chip.
         In-progress = inkTertiary icon + accent-bold arc ring.
         Earned = accent-bold icon + tiny glow + "Earned" chip in success green.
         ════════════════════════════════════════════════════════════════ -->
    <section class="mb-20">
      <VariantFrame
        label="A"
        caption="Flat surface with accent icon — rounded-[20px] surfaceRaised card. Locked = ghost. In-progress = arc ring. Earned = accent icon + glow chip. Safest everyday default."
      >
        <div class="w-full space-y-10">

          <!-- ── LIGHT PANEL ── -->
          <div class="rounded-2xl border p-6 space-y-4" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>

            <!-- 2 rows × 3 cols — all six canonical tiles -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

              <!-- A-L · Earned — Week Warrior (sun / trophy) -->
              <div
                class="relative rounded-[20px] border flex flex-col items-center gap-3 px-5 py-7"
                :style="{
                  background: L.surfaceRaised,
                  borderColor: L.accents.sun.bold + '30',
                  boxShadow: `0 0 24px ${L.accents.sun.soft}, 0 2px 8px rgba(162,120,12,0.12)`,
                }"
              >
                <div
                  class="w-16 h-16 rounded-full flex items-center justify-center"
                  :style="{ background: L.accents.sun.soft }"
                >
                  <TrophyIcon class="w-9 h-9" :style="{ color: L.accents.sun.bold }" />
                </div>
                <div class="text-center space-y-0.5">
                  <p class="text-[14px] font-semibold" :style="{ color: L.inkPrimary }">Week Warrior</p>
                  <p class="text-[12px]" :style="{ color: L.inkSecondary }">7 tasks in a row</p>
                  <p class="text-[11px]" :style="{ color: L.inkTertiary }">Earned 2d ago</p>
                </div>
                <span
                  class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-medium"
                  :style="{ background: L.status.success.soft, color: L.status.success.bold }"
                >
                  <CheckBadgeIcon class="w-3.5 h-3.5" />
                  Earned
                </span>
              </div>

              <!-- A-L · Earned — Kindness Captain (peach / heart) -->
              <div
                class="relative rounded-[20px] border flex flex-col items-center gap-3 px-5 py-7"
                :style="{
                  background: L.surfaceRaised,
                  borderColor: L.accents.peach.bold + '30',
                  boxShadow: `0 0 24px ${L.accents.peach.soft}, 0 2px 8px rgba(186,86,46,0.10)`,
                }"
              >
                <div
                  class="w-16 h-16 rounded-full flex items-center justify-center"
                  :style="{ background: L.accents.peach.soft }"
                >
                  <HeartIcon class="w-9 h-9" :style="{ color: L.accents.peach.bold }" />
                </div>
                <div class="text-center space-y-0.5">
                  <p class="text-[14px] font-semibold" :style="{ color: L.inkPrimary }">Kindness Captain</p>
                  <p class="text-[12px]" :style="{ color: L.inkSecondary }">Gave 10 kudos</p>
                  <p class="text-[11px]" :style="{ color: L.inkTertiary }">Earned 5d ago</p>
                </div>
                <span
                  class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-medium"
                  :style="{ background: L.status.success.soft, color: L.status.success.bold }"
                >
                  <CheckBadgeIcon class="w-3.5 h-3.5" />
                  Earned
                </span>
              </div>

              <!-- A-L · In-progress — Meal Planner (mint / cap, 65%) -->
              <div
                class="relative rounded-[20px] border flex flex-col items-center gap-3 px-5 py-7"
                :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle, boxShadow: '0 1px 2px rgba(28,20,10,0.04), 0 2px 6px rgba(28,20,10,0.05)' }"
              >
                <div class="relative w-20 h-20 flex items-center justify-center">
                  <svg
                    width="80" height="80" viewBox="0 0 80 80"
                    class="absolute inset-0" style="transform: rotate(-90deg);"
                    aria-hidden="true"
                  >
                    <circle cx="40" cy="40" :r="RING_R" fill="none" :stroke="L.accents.mint.soft" stroke-width="3" />
                    <circle
                      cx="40" cy="40" :r="RING_R" fill="none"
                      :stroke="L.accents.mint.bold" stroke-width="3" stroke-linecap="round"
                      :stroke-dasharray="RING_CIRC" :stroke-dashoffset="arcOffset(0.65)"
                    />
                  </svg>
                  <AcademicCapIcon class="w-9 h-9 relative z-10" style="filter: grayscale(0.5);" :style="{ color: L.inkTertiary }" />
                </div>
                <div class="text-center space-y-0.5">
                  <p class="text-[14px] font-semibold" :style="{ color: L.inkPrimary }">Meal Planner</p>
                  <p class="text-[12px]" :style="{ color: L.inkSecondary }">Plan 20 meals</p>
                  <p class="text-[11px] font-medium" :style="{ color: L.accents.mint.bold }">12/20</p>
                </div>
                <span
                  class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-medium"
                  :style="{ background: L.accents.mint.soft, color: L.accents.mint.bold }"
                >
                  In progress
                </span>
              </div>

              <!-- A-L · In-progress — Streak Starter (peach / fire, 30%) -->
              <div
                class="relative rounded-[20px] border flex flex-col items-center gap-3 px-5 py-7"
                :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle, boxShadow: '0 1px 2px rgba(28,20,10,0.04), 0 2px 6px rgba(28,20,10,0.05)' }"
              >
                <div class="relative w-20 h-20 flex items-center justify-center">
                  <svg
                    width="80" height="80" viewBox="0 0 80 80"
                    class="absolute inset-0" style="transform: rotate(-90deg);"
                    aria-hidden="true"
                  >
                    <circle cx="40" cy="40" :r="RING_R" fill="none" :stroke="L.accents.peach.soft" stroke-width="3" />
                    <circle
                      cx="40" cy="40" :r="RING_R" fill="none"
                      :stroke="L.accents.peach.bold" stroke-width="3" stroke-linecap="round"
                      :stroke-dasharray="RING_CIRC" :stroke-dashoffset="arcOffset(0.30)"
                    />
                  </svg>
                  <FireIcon class="w-9 h-9 relative z-10" style="filter: grayscale(0.5);" :style="{ color: L.inkTertiary }" />
                </div>
                <div class="text-center space-y-0.5">
                  <p class="text-[14px] font-semibold" :style="{ color: L.inkPrimary }">Streak Starter</p>
                  <p class="text-[12px]" :style="{ color: L.inkSecondary }">3-day streak, 7 to go</p>
                  <p class="text-[11px] font-medium" :style="{ color: L.accents.peach.bold }">3/10 days</p>
                </div>
                <span
                  class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-medium"
                  :style="{ background: L.accents.peach.soft, color: L.accents.peach.bold }"
                >
                  In progress
                </span>
              </div>

              <!-- A-L · Locked — Century Club (lavender / star) -->
              <div
                class="relative rounded-[20px] border flex flex-col items-center gap-3 px-5 py-7"
                :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle, boxShadow: '0 1px 2px rgba(28,20,10,0.04), 0 2px 6px rgba(28,20,10,0.05)', opacity: 0.7 }"
              >
                <div
                  class="w-16 h-16 rounded-full flex items-center justify-center"
                  style="filter: grayscale(1);"
                  :style="{ background: L.surfaceSunken }"
                >
                  <StarIcon class="w-9 h-9" :style="{ color: L.inkTertiary }" />
                </div>
                <div class="text-center space-y-0.5">
                  <p class="text-[14px] font-semibold" :style="{ color: L.inkTertiary }">Century Club</p>
                  <p class="text-[12px]" :style="{ color: L.inkTertiary }">Complete 100 tasks</p>
                  <p class="text-[11px]" :style="{ color: L.inkTertiary }">Locked</p>
                </div>
                <span
                  class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-medium"
                  :style="{ background: L.surfaceSunken, color: L.inkTertiary, border: `1px solid ${L.borderSubtle}` }"
                >
                  <LockClosedIcon class="w-3 h-3" />
                  Locked
                </span>
              </div>

              <!-- A-L · Hidden — ??? -->
              <div
                class="relative rounded-[20px] border flex flex-col items-center gap-3 px-5 py-7"
                :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle, borderStyle: 'dashed', opacity: 0.6 }"
              >
                <div
                  class="w-16 h-16 rounded-full flex items-center justify-center"
                  :style="{ background: L.surfaceSunken }"
                >
                  <QuestionMarkCircleIcon class="w-9 h-9" :style="{ color: L.inkTertiary }" />
                </div>
                <div class="text-center space-y-0.5">
                  <p class="text-[15px] font-semibold tracking-widest" :style="{ color: L.inkTertiary }">???</p>
                  <p class="text-[12px]" :style="{ color: L.inkTertiary }">Keep playing to unlock</p>
                  <p class="text-[11px]" :style="{ color: L.inkTertiary }">Hidden</p>
                </div>
              </div>

            </div>
          </div>

          <!-- ── DARK PANEL ── -->
          <div class="rounded-2xl border p-6 space-y-4" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

              <!-- A-D · Earned — Week Warrior -->
              <div
                class="relative rounded-[20px] border flex flex-col items-center gap-3 px-5 py-7"
                :style="{
                  background: D.surfaceRaised,
                  borderColor: D.accents.sun.bold + '40',
                  boxShadow: `0 0 24px ${D.accents.sun.soft}, 0 2px 8px rgba(230,196,82,0.15)`,
                }"
              >
                <div
                  class="w-16 h-16 rounded-full flex items-center justify-center"
                  :style="{ background: D.accents.sun.soft }"
                >
                  <TrophyIcon class="w-9 h-9" :style="{ color: D.accents.sun.bold }" />
                </div>
                <div class="text-center space-y-0.5">
                  <p class="text-[14px] font-semibold" :style="{ color: D.inkPrimary }">Week Warrior</p>
                  <p class="text-[12px]" :style="{ color: D.inkSecondary }">7 tasks in a row</p>
                  <p class="text-[11px]" :style="{ color: D.inkTertiary }">Earned 2d ago</p>
                </div>
                <span
                  class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-medium"
                  :style="{ background: D.status.success.soft, color: D.status.success.bold }"
                >
                  <CheckBadgeIcon class="w-3.5 h-3.5" />
                  Earned
                </span>
              </div>

              <!-- A-D · Earned — Kindness Captain -->
              <div
                class="relative rounded-[20px] border flex flex-col items-center gap-3 px-5 py-7"
                :style="{
                  background: D.surfaceRaised,
                  borderColor: D.accents.peach.bold + '40',
                  boxShadow: `0 0 24px ${D.accents.peach.soft}, 0 2px 8px rgba(240,168,130,0.12)`,
                }"
              >
                <div
                  class="w-16 h-16 rounded-full flex items-center justify-center"
                  :style="{ background: D.accents.peach.soft }"
                >
                  <HeartIcon class="w-9 h-9" :style="{ color: D.accents.peach.bold }" />
                </div>
                <div class="text-center space-y-0.5">
                  <p class="text-[14px] font-semibold" :style="{ color: D.inkPrimary }">Kindness Captain</p>
                  <p class="text-[12px]" :style="{ color: D.inkSecondary }">Gave 10 kudos</p>
                  <p class="text-[11px]" :style="{ color: D.inkTertiary }">Earned 5d ago</p>
                </div>
                <span
                  class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-medium"
                  :style="{ background: D.status.success.soft, color: D.status.success.bold }"
                >
                  <CheckBadgeIcon class="w-3.5 h-3.5" />
                  Earned
                </span>
              </div>

              <!-- A-D · In-progress — Meal Planner (65%) -->
              <div
                class="relative rounded-[20px] border flex flex-col items-center gap-3 px-5 py-7"
                :style="{ background: D.surfaceRaised, borderColor: D.borderSubtle, boxShadow: '0 1px 2px rgba(0,0,0,0.30), 0 2px 6px rgba(0,0,0,0.25)' }"
              >
                <div class="relative w-20 h-20 flex items-center justify-center">
                  <svg
                    width="80" height="80" viewBox="0 0 80 80"
                    class="absolute inset-0" style="transform: rotate(-90deg);"
                    aria-hidden="true"
                  >
                    <circle cx="40" cy="40" :r="RING_R" fill="none" :stroke="D.accents.mint.soft" stroke-width="3" />
                    <circle
                      cx="40" cy="40" :r="RING_R" fill="none"
                      :stroke="D.accents.mint.bold" stroke-width="3" stroke-linecap="round"
                      :stroke-dasharray="RING_CIRC" :stroke-dashoffset="arcOffset(0.65)"
                    />
                  </svg>
                  <AcademicCapIcon class="w-9 h-9 relative z-10" style="filter: grayscale(0.5);" :style="{ color: D.inkTertiary }" />
                </div>
                <div class="text-center space-y-0.5">
                  <p class="text-[14px] font-semibold" :style="{ color: D.inkPrimary }">Meal Planner</p>
                  <p class="text-[12px]" :style="{ color: D.inkSecondary }">Plan 20 meals</p>
                  <p class="text-[11px] font-medium" :style="{ color: D.accents.mint.bold }">12/20</p>
                </div>
                <span
                  class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-medium"
                  :style="{ background: D.accents.mint.soft, color: D.accents.mint.bold }"
                >
                  In progress
                </span>
              </div>

              <!-- A-D · In-progress — Streak Starter (30%) -->
              <div
                class="relative rounded-[20px] border flex flex-col items-center gap-3 px-5 py-7"
                :style="{ background: D.surfaceRaised, borderColor: D.borderSubtle, boxShadow: '0 1px 2px rgba(0,0,0,0.30), 0 2px 6px rgba(0,0,0,0.25)' }"
              >
                <div class="relative w-20 h-20 flex items-center justify-center">
                  <svg
                    width="80" height="80" viewBox="0 0 80 80"
                    class="absolute inset-0" style="transform: rotate(-90deg);"
                    aria-hidden="true"
                  >
                    <circle cx="40" cy="40" :r="RING_R" fill="none" :stroke="D.accents.peach.soft" stroke-width="3" />
                    <circle
                      cx="40" cy="40" :r="RING_R" fill="none"
                      :stroke="D.accents.peach.bold" stroke-width="3" stroke-linecap="round"
                      :stroke-dasharray="RING_CIRC" :stroke-dashoffset="arcOffset(0.30)"
                    />
                  </svg>
                  <FireIcon class="w-9 h-9 relative z-10" style="filter: grayscale(0.5);" :style="{ color: D.inkTertiary }" />
                </div>
                <div class="text-center space-y-0.5">
                  <p class="text-[14px] font-semibold" :style="{ color: D.inkPrimary }">Streak Starter</p>
                  <p class="text-[12px]" :style="{ color: D.inkSecondary }">3-day streak, 7 to go</p>
                  <p class="text-[11px] font-medium" :style="{ color: D.accents.peach.bold }">3/10 days</p>
                </div>
                <span
                  class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-medium"
                  :style="{ background: D.accents.peach.soft, color: D.accents.peach.bold }"
                >
                  In progress
                </span>
              </div>

              <!-- A-D · Locked — Century Club -->
              <div
                class="relative rounded-[20px] border flex flex-col items-center gap-3 px-5 py-7"
                :style="{ background: D.surfaceRaised, borderColor: D.borderSubtle, boxShadow: '0 1px 2px rgba(0,0,0,0.30), 0 2px 6px rgba(0,0,0,0.25)', opacity: 0.6 }"
              >
                <div
                  class="w-16 h-16 rounded-full flex items-center justify-center"
                  style="filter: grayscale(1);"
                  :style="{ background: D.surfaceSunken }"
                >
                  <StarIcon class="w-9 h-9" :style="{ color: D.inkTertiary }" />
                </div>
                <div class="text-center space-y-0.5">
                  <p class="text-[14px] font-semibold" :style="{ color: D.inkTertiary }">Century Club</p>
                  <p class="text-[12px]" :style="{ color: D.inkTertiary }">Complete 100 tasks</p>
                  <p class="text-[11px]" :style="{ color: D.inkTertiary }">Locked</p>
                </div>
                <span
                  class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-medium"
                  :style="{ background: D.surfaceSunken, color: D.inkTertiary, border: `1px solid ${D.borderSubtle}` }"
                >
                  <LockClosedIcon class="w-3 h-3" />
                  Locked
                </span>
              </div>

              <!-- A-D · Hidden — ??? -->
              <div
                class="relative rounded-[20px] border flex flex-col items-center gap-3 px-5 py-7"
                :style="{ background: D.surfaceRaised, borderColor: D.borderSubtle, borderStyle: 'dashed', opacity: 0.55 }"
              >
                <div
                  class="w-16 h-16 rounded-full flex items-center justify-center"
                  :style="{ background: D.surfaceSunken }"
                >
                  <QuestionMarkCircleIcon class="w-9 h-9" :style="{ color: D.inkTertiary }" />
                </div>
                <div class="text-center space-y-0.5">
                  <p class="text-[15px] font-semibold tracking-widest" :style="{ color: D.inkTertiary }">???</p>
                  <p class="text-[12px]" :style="{ color: D.inkTertiary }">Keep playing to unlock</p>
                  <p class="text-[11px]" :style="{ color: D.inkTertiary }">Hidden</p>
                </div>
              </div>

            </div>
          </div>
        </div>
      </VariantFrame>
    </section>

    <!-- ═══════════════════════════════════════════════════════════════
         VARIANT B — Iridescent GradientCard treatment
         Earned = GradientCard language (radial accent gradient + embossed glyph).
         Locked = flat surface-sunken with grey icon.
         In-progress = flat surface with accent arc.
         ════════════════════════════════════════════════════════════════ -->
    <section class="mb-20">
      <VariantFrame
        label="B"
        caption="Iridescent GradientCard — earned state uses radial accent gradient + embossed glyph feel. Makes earned feel like a reward; unearned stays quiet. Best for Achievements landing hero."
      >
        <div class="w-full space-y-10">

          <!-- ── LIGHT PANEL ── -->
          <div class="rounded-2xl border p-6 space-y-4" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

              <!-- B-L · Earned — Week Warrior (sun gradient) -->
              <div
                class="relative rounded-[20px] overflow-hidden flex flex-col items-center gap-3 px-5 py-8"
                style="
                  background-color: #FFFBF0;
                  background-image: radial-gradient(ellipse 80% 70% at 50% 25%, rgba(252,243,210,0.95), transparent 68%);
                  box-shadow: 0 1px 2px rgba(28,20,10,0.04), 0 2px 6px rgba(28,20,10,0.05), inset 0 0 40px rgba(162,120,12,0.07);
                "
              >
                <div
                  class="relative w-16 h-16 rounded-full flex items-center justify-center"
                  :style="{
                    background: L.accents.sun.soft,
                    boxShadow: `0 0 0 6px rgba(162,120,12,0.12), 0 0 20px rgba(162,120,12,0.18)`,
                  }"
                >
                  <TrophyIcon class="w-9 h-9" :style="{ color: L.accents.sun.bold }" />
                </div>
                <div class="text-center space-y-0.5">
                  <p class="text-[14px] font-semibold" :style="{ color: L.inkPrimary }">Week Warrior</p>
                  <p class="text-[12px]" :style="{ color: L.inkSecondary }">7 tasks in a row</p>
                  <p class="text-[11px]" :style="{ color: L.inkTertiary }">Earned 2d ago</p>
                </div>
                <span
                  class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-medium"
                  :style="{ background: L.status.success.soft, color: L.status.success.bold }"
                >
                  <CheckBadgeIcon class="w-3.5 h-3.5" />
                  Earned
                </span>
              </div>

              <!-- B-L · Earned — Kindness Captain (peach gradient) -->
              <div
                class="relative rounded-[20px] overflow-hidden flex flex-col items-center gap-3 px-5 py-8"
                style="
                  background-color: #FFF7F4;
                  background-image: radial-gradient(ellipse 80% 70% at 50% 25%, rgba(252,233,224,0.92), transparent 68%);
                  box-shadow: 0 1px 2px rgba(28,20,10,0.04), 0 2px 6px rgba(28,20,10,0.05), inset 0 0 40px rgba(186,86,46,0.05);
                "
              >
                <div
                  class="relative w-16 h-16 rounded-full flex items-center justify-center"
                  :style="{
                    background: L.accents.peach.soft,
                    boxShadow: `0 0 0 6px rgba(186,86,46,0.10), 0 0 20px rgba(186,86,46,0.14)`,
                  }"
                >
                  <HeartIcon class="w-9 h-9" :style="{ color: L.accents.peach.bold }" />
                </div>
                <div class="text-center space-y-0.5">
                  <p class="text-[14px] font-semibold" :style="{ color: L.inkPrimary }">Kindness Captain</p>
                  <p class="text-[12px]" :style="{ color: L.inkSecondary }">Gave 10 kudos</p>
                  <p class="text-[11px]" :style="{ color: L.inkTertiary }">Earned 5d ago</p>
                </div>
                <span
                  class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-medium"
                  :style="{ background: L.status.success.soft, color: L.status.success.bold }"
                >
                  <CheckBadgeIcon class="w-3.5 h-3.5" />
                  Earned
                </span>
              </div>

              <!-- B-L · In-progress — Meal Planner (flat + mint arc, 65%) -->
              <div
                class="relative rounded-[20px] overflow-hidden flex flex-col items-center gap-3 px-5 py-8"
                :style="{
                  background: L.surfaceRaised,
                  boxShadow: '0 1px 2px rgba(28,20,10,0.04), 0 2px 6px rgba(28,20,10,0.05)',
                }"
              >
                <div class="relative w-20 h-20 flex items-center justify-center">
                  <svg
                    width="80" height="80" viewBox="0 0 80 80"
                    class="absolute inset-0" style="transform: rotate(-90deg);"
                    aria-hidden="true"
                  >
                    <circle cx="40" cy="40" :r="RING_R" fill="none" :stroke="L.accents.mint.soft" stroke-width="3" />
                    <circle
                      cx="40" cy="40" :r="RING_R" fill="none"
                      :stroke="L.accents.mint.bold" stroke-width="3" stroke-linecap="round"
                      :stroke-dasharray="RING_CIRC" :stroke-dashoffset="arcOffset(0.65)"
                    />
                  </svg>
                  <AcademicCapIcon class="w-9 h-9 relative z-10" style="filter: grayscale(0.5);" :style="{ color: L.inkTertiary }" />
                </div>
                <div class="text-center space-y-0.5">
                  <p class="text-[14px] font-semibold" :style="{ color: L.inkPrimary }">Meal Planner</p>
                  <p class="text-[12px]" :style="{ color: L.inkSecondary }">Plan 20 meals</p>
                  <p class="text-[11px] font-medium" :style="{ color: L.accents.mint.bold }">12/20</p>
                </div>
                <span
                  class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-medium"
                  :style="{ background: L.accents.mint.soft, color: L.accents.mint.bold }"
                >
                  In progress
                </span>
              </div>

              <!-- B-L · In-progress — Streak Starter (flat + peach arc, 30%) -->
              <div
                class="relative rounded-[20px] overflow-hidden flex flex-col items-center gap-3 px-5 py-8"
                :style="{
                  background: L.surfaceRaised,
                  boxShadow: '0 1px 2px rgba(28,20,10,0.04), 0 2px 6px rgba(28,20,10,0.05)',
                }"
              >
                <div class="relative w-20 h-20 flex items-center justify-center">
                  <svg
                    width="80" height="80" viewBox="0 0 80 80"
                    class="absolute inset-0" style="transform: rotate(-90deg);"
                    aria-hidden="true"
                  >
                    <circle cx="40" cy="40" :r="RING_R" fill="none" :stroke="L.accents.peach.soft" stroke-width="3" />
                    <circle
                      cx="40" cy="40" :r="RING_R" fill="none"
                      :stroke="L.accents.peach.bold" stroke-width="3" stroke-linecap="round"
                      :stroke-dasharray="RING_CIRC" :stroke-dashoffset="arcOffset(0.30)"
                    />
                  </svg>
                  <FireIcon class="w-9 h-9 relative z-10" style="filter: grayscale(0.5);" :style="{ color: L.inkTertiary }" />
                </div>
                <div class="text-center space-y-0.5">
                  <p class="text-[14px] font-semibold" :style="{ color: L.inkPrimary }">Streak Starter</p>
                  <p class="text-[12px]" :style="{ color: L.inkSecondary }">3-day streak, 7 to go</p>
                  <p class="text-[11px] font-medium" :style="{ color: L.accents.peach.bold }">3/10 days</p>
                </div>
                <span
                  class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-medium"
                  :style="{ background: L.accents.peach.soft, color: L.accents.peach.bold }"
                >
                  In progress
                </span>
              </div>

              <!-- B-L · Locked — Century Club (surface-sunken, grey icon) -->
              <div
                class="relative rounded-[20px] overflow-hidden flex flex-col items-center gap-3 px-5 py-8"
                :style="{
                  background: L.surfaceSunken,
                  opacity: 0.7,
                  boxShadow: '0 1px 2px rgba(28,20,10,0.04)',
                }"
              >
                <div
                  class="w-16 h-16 rounded-full flex items-center justify-center"
                  style="filter: grayscale(1);"
                  :style="{ background: L.borderSubtle }"
                >
                  <StarIcon class="w-9 h-9" :style="{ color: L.inkTertiary }" />
                </div>
                <div class="text-center space-y-0.5">
                  <p class="text-[14px] font-semibold" :style="{ color: L.inkTertiary }">Century Club</p>
                  <p class="text-[12px]" :style="{ color: L.inkTertiary }">Complete 100 tasks</p>
                  <p class="text-[11px]" :style="{ color: L.inkTertiary }">Locked</p>
                </div>
                <span
                  class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-medium"
                  :style="{ background: L.surfaceRaised, color: L.inkTertiary, border: `1px solid ${L.borderSubtle}` }"
                >
                  <LockClosedIcon class="w-3 h-3" />
                  Locked
                </span>
              </div>

              <!-- B-L · Hidden — ??? (surface-sunken, fully quiet) -->
              <div
                class="relative rounded-[20px] overflow-hidden flex flex-col items-center gap-3 px-5 py-8"
                :style="{
                  background: L.surfaceSunken,
                  opacity: 0.55,
                }"
              >
                <div
                  class="w-16 h-16 rounded-full flex items-center justify-center"
                  :style="{ background: L.borderSubtle }"
                >
                  <QuestionMarkCircleIcon class="w-9 h-9" :style="{ color: L.inkTertiary }" />
                </div>
                <div class="text-center space-y-0.5">
                  <p class="text-[15px] font-semibold tracking-widest" :style="{ color: L.inkTertiary }">???</p>
                  <p class="text-[12px]" :style="{ color: L.inkTertiary }">Keep playing to unlock</p>
                  <p class="text-[11px]" :style="{ color: L.inkTertiary }">Hidden</p>
                </div>
              </div>

            </div>
          </div>

          <!-- ── DARK PANEL ── -->
          <div class="rounded-2xl border p-6 space-y-4" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

              <!-- B-D · Earned — Week Warrior -->
              <div
                class="relative rounded-[20px] overflow-hidden flex flex-col items-center gap-3 px-5 py-8"
                style="
                  background-color: #1C1B19;
                  background-image: radial-gradient(ellipse 80% 70% at 50% 25%, rgba(230,196,82,0.28), transparent 65%);
                  box-shadow: 0 1px 2px rgba(0,0,0,0.30), 0 2px 6px rgba(0,0,0,0.25), inset 0 0 40px rgba(230,196,82,0.05);
                "
              >
                <div
                  class="relative w-16 h-16 rounded-full flex items-center justify-center"
                  :style="{
                    background: D.accents.sun.soft,
                    boxShadow: `0 0 0 6px rgba(230,196,82,0.10), 0 0 20px rgba(230,196,82,0.20)`,
                  }"
                >
                  <TrophyIcon class="w-9 h-9" :style="{ color: D.accents.sun.bold }" />
                </div>
                <div class="text-center space-y-0.5">
                  <p class="text-[14px] font-semibold" :style="{ color: D.inkPrimary }">Week Warrior</p>
                  <p class="text-[12px]" :style="{ color: D.inkSecondary }">7 tasks in a row</p>
                  <p class="text-[11px]" :style="{ color: D.inkTertiary }">Earned 2d ago</p>
                </div>
                <span
                  class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-medium"
                  :style="{ background: D.status.success.soft, color: D.status.success.bold }"
                >
                  <CheckBadgeIcon class="w-3.5 h-3.5" />
                  Earned
                </span>
              </div>

              <!-- B-D · Earned — Kindness Captain -->
              <div
                class="relative rounded-[20px] overflow-hidden flex flex-col items-center gap-3 px-5 py-8"
                style="
                  background-color: #1C1B19;
                  background-image: radial-gradient(ellipse 80% 70% at 50% 25%, rgba(240,168,130,0.22), transparent 65%);
                  box-shadow: 0 1px 2px rgba(0,0,0,0.30), 0 2px 6px rgba(0,0,0,0.25), inset 0 0 40px rgba(240,168,130,0.04);
                "
              >
                <div
                  class="relative w-16 h-16 rounded-full flex items-center justify-center"
                  :style="{
                    background: D.accents.peach.soft,
                    boxShadow: `0 0 0 6px rgba(240,168,130,0.08), 0 0 20px rgba(240,168,130,0.16)`,
                  }"
                >
                  <HeartIcon class="w-9 h-9" :style="{ color: D.accents.peach.bold }" />
                </div>
                <div class="text-center space-y-0.5">
                  <p class="text-[14px] font-semibold" :style="{ color: D.inkPrimary }">Kindness Captain</p>
                  <p class="text-[12px]" :style="{ color: D.inkSecondary }">Gave 10 kudos</p>
                  <p class="text-[11px]" :style="{ color: D.inkTertiary }">Earned 5d ago</p>
                </div>
                <span
                  class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-medium"
                  :style="{ background: D.status.success.soft, color: D.status.success.bold }"
                >
                  <CheckBadgeIcon class="w-3.5 h-3.5" />
                  Earned
                </span>
              </div>

              <!-- B-D · In-progress — Meal Planner (65%) -->
              <div
                class="relative rounded-[20px] overflow-hidden flex flex-col items-center gap-3 px-5 py-8"
                :style="{ background: D.surfaceRaised, boxShadow: '0 1px 2px rgba(0,0,0,0.30), 0 2px 6px rgba(0,0,0,0.25)' }"
              >
                <div class="relative w-20 h-20 flex items-center justify-center">
                  <svg
                    width="80" height="80" viewBox="0 0 80 80"
                    class="absolute inset-0" style="transform: rotate(-90deg);"
                    aria-hidden="true"
                  >
                    <circle cx="40" cy="40" :r="RING_R" fill="none" :stroke="D.accents.mint.soft" stroke-width="3" />
                    <circle
                      cx="40" cy="40" :r="RING_R" fill="none"
                      :stroke="D.accents.mint.bold" stroke-width="3" stroke-linecap="round"
                      :stroke-dasharray="RING_CIRC" :stroke-dashoffset="arcOffset(0.65)"
                    />
                  </svg>
                  <AcademicCapIcon class="w-9 h-9 relative z-10" style="filter: grayscale(0.5);" :style="{ color: D.inkTertiary }" />
                </div>
                <div class="text-center space-y-0.5">
                  <p class="text-[14px] font-semibold" :style="{ color: D.inkPrimary }">Meal Planner</p>
                  <p class="text-[12px]" :style="{ color: D.inkSecondary }">Plan 20 meals</p>
                  <p class="text-[11px] font-medium" :style="{ color: D.accents.mint.bold }">12/20</p>
                </div>
                <span
                  class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-medium"
                  :style="{ background: D.accents.mint.soft, color: D.accents.mint.bold }"
                >
                  In progress
                </span>
              </div>

              <!-- B-D · In-progress — Streak Starter (30%) -->
              <div
                class="relative rounded-[20px] overflow-hidden flex flex-col items-center gap-3 px-5 py-8"
                :style="{ background: D.surfaceRaised, boxShadow: '0 1px 2px rgba(0,0,0,0.30), 0 2px 6px rgba(0,0,0,0.25)' }"
              >
                <div class="relative w-20 h-20 flex items-center justify-center">
                  <svg
                    width="80" height="80" viewBox="0 0 80 80"
                    class="absolute inset-0" style="transform: rotate(-90deg);"
                    aria-hidden="true"
                  >
                    <circle cx="40" cy="40" :r="RING_R" fill="none" :stroke="D.accents.peach.soft" stroke-width="3" />
                    <circle
                      cx="40" cy="40" :r="RING_R" fill="none"
                      :stroke="D.accents.peach.bold" stroke-width="3" stroke-linecap="round"
                      :stroke-dasharray="RING_CIRC" :stroke-dashoffset="arcOffset(0.30)"
                    />
                  </svg>
                  <FireIcon class="w-9 h-9 relative z-10" style="filter: grayscale(0.5);" :style="{ color: D.inkTertiary }" />
                </div>
                <div class="text-center space-y-0.5">
                  <p class="text-[14px] font-semibold" :style="{ color: D.inkPrimary }">Streak Starter</p>
                  <p class="text-[12px]" :style="{ color: D.inkSecondary }">3-day streak, 7 to go</p>
                  <p class="text-[11px] font-medium" :style="{ color: D.accents.peach.bold }">3/10 days</p>
                </div>
                <span
                  class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-medium"
                  :style="{ background: D.accents.peach.soft, color: D.accents.peach.bold }"
                >
                  In progress
                </span>
              </div>

              <!-- B-D · Locked — Century Club -->
              <div
                class="relative rounded-[20px] overflow-hidden flex flex-col items-center gap-3 px-5 py-8"
                :style="{
                  background: D.surfaceSunken,
                  opacity: 0.6,
                  boxShadow: '0 1px 2px rgba(0,0,0,0.30)',
                }"
              >
                <div
                  class="w-16 h-16 rounded-full flex items-center justify-center"
                  style="filter: grayscale(1);"
                  :style="{ background: D.borderSubtle }"
                >
                  <StarIcon class="w-9 h-9" :style="{ color: D.inkTertiary }" />
                </div>
                <div class="text-center space-y-0.5">
                  <p class="text-[14px] font-semibold" :style="{ color: D.inkTertiary }">Century Club</p>
                  <p class="text-[12px]" :style="{ color: D.inkTertiary }">Complete 100 tasks</p>
                  <p class="text-[11px]" :style="{ color: D.inkTertiary }">Locked</p>
                </div>
                <span
                  class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-medium"
                  :style="{ background: D.surfaceRaised, color: D.inkTertiary, border: `1px solid ${D.borderSubtle}` }"
                >
                  <LockClosedIcon class="w-3 h-3" />
                  Locked
                </span>
              </div>

              <!-- B-D · Hidden — ??? -->
              <div
                class="relative rounded-[20px] overflow-hidden flex flex-col items-center gap-3 px-5 py-8"
                :style="{
                  background: D.surfaceSunken,
                  opacity: 0.50,
                }"
              >
                <div
                  class="w-16 h-16 rounded-full flex items-center justify-center"
                  :style="{ background: D.borderSubtle }"
                >
                  <QuestionMarkCircleIcon class="w-9 h-9" :style="{ color: D.inkTertiary }" />
                </div>
                <div class="text-center space-y-0.5">
                  <p class="text-[15px] font-semibold tracking-widest" :style="{ color: D.inkTertiary }">???</p>
                  <p class="text-[12px]" :style="{ color: D.inkTertiary }">Keep playing to unlock</p>
                  <p class="text-[11px]" :style="{ color: D.inkTertiary }">Hidden</p>
                </div>
              </div>

            </div>
          </div>
        </div>
      </VariantFrame>
    </section>

    <!-- ═══════════════════════════════════════════════════════════════
         VARIANT C — Hexagonal badge (Steam-style)
         Icon sits in an 80×80px hex container (SVG clipPath polygon).
         Earned = accent-bold hex fill + inset glow + outer pulse ring.
         In-progress = filled hex + arc ring hugging hex boundary.
         Locked = inkTertiary outline ghost hex.
         ════════════════════════════════════════════════════════════════ -->
    <section class="mb-20">
      <VariantFrame
        label="C"
        caption="Hexagonal badge — Steam/console-achievement feel. 80×80px hex container with SVG clip. Earned = accent fill + inset glow. In-progress = tinted fill + arc ring. Locked = ghost outline. Profile trophy-case hero."
      >
        <div class="w-full space-y-10">

          <!-- ── LIGHT PANEL ── -->
          <div class="rounded-2xl border p-6 space-y-4" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

              <!-- C-L · Earned — Week Warrior (sun) -->
              <div class="flex flex-col items-center gap-3">
                <!-- outer pulse ring -->
                <div class="relative flex items-center justify-center" style="width: 108px; height: 108px;">
                  <div
                    class="hex-tile absolute"
                    style="width: 108px; height: 108px; opacity: 0.20;"
                    :style="{ background: L.accents.sun.bold }"
                  />
                  <div
                    class="hex-tile flex items-center justify-center relative z-10"
                    style="width: 80px; height: 80px;"
                    :style="{
                      background: L.accents.sun.bold,
                      boxShadow: `inset 0 0 18px rgba(255,255,255,0.22), 0 0 0 3px ${L.accents.sun.soft}`,
                    }"
                  >
                    <TrophyIcon class="w-9 h-9" :style="{ color: L.inkInverse }" />
                  </div>
                </div>
                <div class="text-center space-y-0.5">
                  <p class="text-[14px] font-semibold" :style="{ color: L.inkPrimary }">Week Warrior</p>
                  <p class="text-[12px]" :style="{ color: L.inkSecondary }">7 tasks in a row</p>
                  <p class="text-[11px]" :style="{ color: L.inkTertiary }">Earned 2d ago</p>
                </div>
                <span
                  class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-medium"
                  :style="{ background: L.status.success.soft, color: L.status.success.bold }"
                >
                  <CheckBadgeIcon class="w-3.5 h-3.5" />
                  Earned
                </span>
              </div>

              <!-- C-L · Earned — Kindness Captain (peach) -->
              <div class="flex flex-col items-center gap-3">
                <div class="relative flex items-center justify-center" style="width: 108px; height: 108px;">
                  <div
                    class="hex-tile absolute"
                    style="width: 108px; height: 108px; opacity: 0.20;"
                    :style="{ background: L.accents.peach.bold }"
                  />
                  <div
                    class="hex-tile flex items-center justify-center relative z-10"
                    style="width: 80px; height: 80px;"
                    :style="{
                      background: L.accents.peach.bold,
                      boxShadow: `inset 0 0 18px rgba(255,255,255,0.18), 0 0 0 3px ${L.accents.peach.soft}`,
                    }"
                  >
                    <HeartIcon class="w-9 h-9" :style="{ color: L.inkInverse }" />
                  </div>
                </div>
                <div class="text-center space-y-0.5">
                  <p class="text-[14px] font-semibold" :style="{ color: L.inkPrimary }">Kindness Captain</p>
                  <p class="text-[12px]" :style="{ color: L.inkSecondary }">Gave 10 kudos</p>
                  <p class="text-[11px]" :style="{ color: L.inkTertiary }">Earned 5d ago</p>
                </div>
                <span
                  class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-medium"
                  :style="{ background: L.status.success.soft, color: L.status.success.bold }"
                >
                  <CheckBadgeIcon class="w-3.5 h-3.5" />
                  Earned
                </span>
              </div>

              <!-- C-L · In-progress — Meal Planner (mint, 65%) -->
              <div class="flex flex-col items-center gap-3">
                <!-- arc ring sits outside the hex -->
                <div class="relative flex items-center justify-center" style="width: 108px; height: 108px;">
                  <svg
                    width="108" height="108" viewBox="0 0 108 108"
                    class="absolute inset-0" style="transform: rotate(-90deg);"
                    aria-hidden="true"
                  >
                    <circle cx="54" cy="54" r="48" fill="none" :stroke="L.accents.mint.soft" stroke-width="4" />
                    <circle
                      cx="54" cy="54" r="48" fill="none"
                      :stroke="L.accents.mint.bold" stroke-width="4" stroke-linecap="round"
                      :stroke-dasharray="2 * Math.PI * 48"
                      :stroke-dashoffset="2 * Math.PI * 48 * (1 - 0.65)"
                    />
                  </svg>
                  <div
                    class="hex-tile flex items-center justify-center relative z-10"
                    style="width: 80px; height: 80px;"
                    :style="{ background: L.accents.mint.soft }"
                  >
                    <AcademicCapIcon class="w-9 h-9" style="filter: grayscale(0.4);" :style="{ color: L.accents.mint.bold }" />
                  </div>
                </div>
                <div class="text-center space-y-0.5">
                  <p class="text-[14px] font-semibold" :style="{ color: L.inkPrimary }">Meal Planner</p>
                  <p class="text-[12px]" :style="{ color: L.inkSecondary }">Plan 20 meals</p>
                  <p class="text-[11px] font-medium" :style="{ color: L.accents.mint.bold }">12/20</p>
                </div>
                <span
                  class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-medium"
                  :style="{ background: L.accents.mint.soft, color: L.accents.mint.bold }"
                >
                  In progress
                </span>
              </div>

              <!-- C-L · In-progress — Streak Starter (peach, 30%) -->
              <div class="flex flex-col items-center gap-3">
                <div class="relative flex items-center justify-center" style="width: 108px; height: 108px;">
                  <svg
                    width="108" height="108" viewBox="0 0 108 108"
                    class="absolute inset-0" style="transform: rotate(-90deg);"
                    aria-hidden="true"
                  >
                    <circle cx="54" cy="54" r="48" fill="none" :stroke="L.accents.peach.soft" stroke-width="4" />
                    <circle
                      cx="54" cy="54" r="48" fill="none"
                      :stroke="L.accents.peach.bold" stroke-width="4" stroke-linecap="round"
                      :stroke-dasharray="2 * Math.PI * 48"
                      :stroke-dashoffset="2 * Math.PI * 48 * (1 - 0.30)"
                    />
                  </svg>
                  <div
                    class="hex-tile flex items-center justify-center relative z-10"
                    style="width: 80px; height: 80px;"
                    :style="{ background: L.accents.peach.soft }"
                  >
                    <FireIcon class="w-9 h-9" style="filter: grayscale(0.4);" :style="{ color: L.accents.peach.bold }" />
                  </div>
                </div>
                <div class="text-center space-y-0.5">
                  <p class="text-[14px] font-semibold" :style="{ color: L.inkPrimary }">Streak Starter</p>
                  <p class="text-[12px]" :style="{ color: L.inkSecondary }">3-day streak, 7 to go</p>
                  <p class="text-[11px] font-medium" :style="{ color: L.accents.peach.bold }">3/10 days</p>
                </div>
                <span
                  class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-medium"
                  :style="{ background: L.accents.peach.soft, color: L.accents.peach.bold }"
                >
                  In progress
                </span>
              </div>

              <!-- C-L · Locked — Century Club -->
              <div class="flex flex-col items-center gap-3" style="opacity: 0.55;">
                <div style="width: 108px; height: 108px; display: flex; align-items: center; justify-content: center;">
                  <div
                    class="hex-tile flex items-center justify-center"
                    style="width: 80px; height: 80px; background: transparent;"
                    :style="{ outline: `2px solid ${L.borderStrong}` }"
                  >
                    <StarIcon class="w-8 h-8" :style="{ color: L.inkTertiary }" />
                  </div>
                </div>
                <div class="text-center space-y-0.5">
                  <p class="text-[14px] font-semibold" :style="{ color: L.inkTertiary }">Century Club</p>
                  <p class="text-[12px]" :style="{ color: L.inkTertiary }">Complete 100 tasks</p>
                  <p class="text-[11px]" :style="{ color: L.inkTertiary }">Locked</p>
                </div>
                <span
                  class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-medium"
                  :style="{ background: L.surfaceSunken, color: L.inkTertiary, border: `1px solid ${L.borderSubtle}` }"
                >
                  <LockClosedIcon class="w-3 h-3" />
                  Locked
                </span>
              </div>

              <!-- C-L · Hidden — ??? (dashed border ghost) -->
              <div class="flex flex-col items-center gap-3" style="opacity: 0.50;">
                <div style="width: 108px; height: 108px; display: flex; align-items: center; justify-content: center;">
                  <div
                    class="hex-tile-dashed flex items-center justify-center"
                    style="width: 80px; height: 80px;"
                    :style="{ background: L.surfaceSunken }"
                  >
                    <QuestionMarkCircleIcon class="w-8 h-8" :style="{ color: L.inkTertiary }" />
                  </div>
                </div>
                <div class="text-center space-y-0.5">
                  <p class="text-[15px] font-semibold tracking-widest" :style="{ color: L.inkTertiary }">???</p>
                  <p class="text-[12px]" :style="{ color: L.inkTertiary }">Keep playing to unlock</p>
                  <p class="text-[11px]" :style="{ color: L.inkTertiary }">Hidden</p>
                </div>
              </div>

            </div>
          </div>

          <!-- ── DARK PANEL ── -->
          <div class="rounded-2xl border p-6 space-y-4" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

              <!-- C-D · Earned — Week Warrior -->
              <div class="flex flex-col items-center gap-3">
                <div class="relative flex items-center justify-center" style="width: 108px; height: 108px;">
                  <div
                    class="hex-tile absolute"
                    style="width: 108px; height: 108px; opacity: 0.18;"
                    :style="{ background: D.accents.sun.bold }"
                  />
                  <div
                    class="hex-tile flex items-center justify-center relative z-10"
                    style="width: 80px; height: 80px;"
                    :style="{
                      background: D.accents.sun.bold,
                      boxShadow: `inset 0 0 18px rgba(255,255,255,0.10), 0 0 0 3px ${D.accents.sun.soft}, 0 0 28px rgba(230,196,82,0.22)`,
                    }"
                  >
                    <TrophyIcon class="w-9 h-9" :style="{ color: D.inkInverse }" />
                  </div>
                </div>
                <div class="text-center space-y-0.5">
                  <p class="text-[14px] font-semibold" :style="{ color: D.inkPrimary }">Week Warrior</p>
                  <p class="text-[12px]" :style="{ color: D.inkSecondary }">7 tasks in a row</p>
                  <p class="text-[11px]" :style="{ color: D.inkTertiary }">Earned 2d ago</p>
                </div>
                <span
                  class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-medium"
                  :style="{ background: D.status.success.soft, color: D.status.success.bold }"
                >
                  <CheckBadgeIcon class="w-3.5 h-3.5" />
                  Earned
                </span>
              </div>

              <!-- C-D · Earned — Kindness Captain -->
              <div class="flex flex-col items-center gap-3">
                <div class="relative flex items-center justify-center" style="width: 108px; height: 108px;">
                  <div
                    class="hex-tile absolute"
                    style="width: 108px; height: 108px; opacity: 0.18;"
                    :style="{ background: D.accents.peach.bold }"
                  />
                  <div
                    class="hex-tile flex items-center justify-center relative z-10"
                    style="width: 80px; height: 80px;"
                    :style="{
                      background: D.accents.peach.bold,
                      boxShadow: `inset 0 0 18px rgba(255,255,255,0.08), 0 0 0 3px ${D.accents.peach.soft}, 0 0 28px rgba(240,168,130,0.20)`,
                    }"
                  >
                    <HeartIcon class="w-9 h-9" :style="{ color: D.inkInverse }" />
                  </div>
                </div>
                <div class="text-center space-y-0.5">
                  <p class="text-[14px] font-semibold" :style="{ color: D.inkPrimary }">Kindness Captain</p>
                  <p class="text-[12px]" :style="{ color: D.inkSecondary }">Gave 10 kudos</p>
                  <p class="text-[11px]" :style="{ color: D.inkTertiary }">Earned 5d ago</p>
                </div>
                <span
                  class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-medium"
                  :style="{ background: D.status.success.soft, color: D.status.success.bold }"
                >
                  <CheckBadgeIcon class="w-3.5 h-3.5" />
                  Earned
                </span>
              </div>

              <!-- C-D · In-progress — Meal Planner (65%) -->
              <div class="flex flex-col items-center gap-3">
                <div class="relative flex items-center justify-center" style="width: 108px; height: 108px;">
                  <svg
                    width="108" height="108" viewBox="0 0 108 108"
                    class="absolute inset-0" style="transform: rotate(-90deg);"
                    aria-hidden="true"
                  >
                    <circle cx="54" cy="54" r="48" fill="none" :stroke="D.accents.mint.soft" stroke-width="4" />
                    <circle
                      cx="54" cy="54" r="48" fill="none"
                      :stroke="D.accents.mint.bold" stroke-width="4" stroke-linecap="round"
                      :stroke-dasharray="2 * Math.PI * 48"
                      :stroke-dashoffset="2 * Math.PI * 48 * (1 - 0.65)"
                    />
                  </svg>
                  <div
                    class="hex-tile flex items-center justify-center relative z-10"
                    style="width: 80px; height: 80px;"
                    :style="{ background: D.accents.mint.soft }"
                  >
                    <AcademicCapIcon class="w-9 h-9" style="filter: grayscale(0.4);" :style="{ color: D.accents.mint.bold }" />
                  </div>
                </div>
                <div class="text-center space-y-0.5">
                  <p class="text-[14px] font-semibold" :style="{ color: D.inkPrimary }">Meal Planner</p>
                  <p class="text-[12px]" :style="{ color: D.inkSecondary }">Plan 20 meals</p>
                  <p class="text-[11px] font-medium" :style="{ color: D.accents.mint.bold }">12/20</p>
                </div>
                <span
                  class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-medium"
                  :style="{ background: D.accents.mint.soft, color: D.accents.mint.bold }"
                >
                  In progress
                </span>
              </div>

              <!-- C-D · In-progress — Streak Starter (30%) -->
              <div class="flex flex-col items-center gap-3">
                <div class="relative flex items-center justify-center" style="width: 108px; height: 108px;">
                  <svg
                    width="108" height="108" viewBox="0 0 108 108"
                    class="absolute inset-0" style="transform: rotate(-90deg);"
                    aria-hidden="true"
                  >
                    <circle cx="54" cy="54" r="48" fill="none" :stroke="D.accents.peach.soft" stroke-width="4" />
                    <circle
                      cx="54" cy="54" r="48" fill="none"
                      :stroke="D.accents.peach.bold" stroke-width="4" stroke-linecap="round"
                      :stroke-dasharray="2 * Math.PI * 48"
                      :stroke-dashoffset="2 * Math.PI * 48 * (1 - 0.30)"
                    />
                  </svg>
                  <div
                    class="hex-tile flex items-center justify-center relative z-10"
                    style="width: 80px; height: 80px;"
                    :style="{ background: D.accents.peach.soft }"
                  >
                    <FireIcon class="w-9 h-9" style="filter: grayscale(0.4);" :style="{ color: D.accents.peach.bold }" />
                  </div>
                </div>
                <div class="text-center space-y-0.5">
                  <p class="text-[14px] font-semibold" :style="{ color: D.inkPrimary }">Streak Starter</p>
                  <p class="text-[12px]" :style="{ color: D.inkSecondary }">3-day streak, 7 to go</p>
                  <p class="text-[11px] font-medium" :style="{ color: D.accents.peach.bold }">3/10 days</p>
                </div>
                <span
                  class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-medium"
                  :style="{ background: D.accents.peach.soft, color: D.accents.peach.bold }"
                >
                  In progress
                </span>
              </div>

              <!-- C-D · Locked — Century Club -->
              <div class="flex flex-col items-center gap-3" style="opacity: 0.50;">
                <div style="width: 108px; height: 108px; display: flex; align-items: center; justify-content: center;">
                  <div
                    class="hex-tile flex items-center justify-center"
                    style="width: 80px; height: 80px; background: transparent;"
                    :style="{ outline: `2px solid ${D.borderStrong}` }"
                  >
                    <StarIcon class="w-8 h-8" :style="{ color: D.inkTertiary }" />
                  </div>
                </div>
                <div class="text-center space-y-0.5">
                  <p class="text-[14px] font-semibold" :style="{ color: D.inkTertiary }">Century Club</p>
                  <p class="text-[12px]" :style="{ color: D.inkTertiary }">Complete 100 tasks</p>
                  <p class="text-[11px]" :style="{ color: D.inkTertiary }">Locked</p>
                </div>
                <span
                  class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-medium"
                  :style="{ background: D.surfaceSunken, color: D.inkTertiary, border: `1px solid ${D.borderSubtle}` }"
                >
                  <LockClosedIcon class="w-3 h-3" />
                  Locked
                </span>
              </div>

              <!-- C-D · Hidden — ??? -->
              <div class="flex flex-col items-center gap-3" style="opacity: 0.45;">
                <div style="width: 108px; height: 108px; display: flex; align-items: center; justify-content: center;">
                  <div
                    class="hex-tile-dashed flex items-center justify-center"
                    style="width: 80px; height: 80px;"
                    :style="{ background: D.surfaceSunken }"
                  >
                    <QuestionMarkCircleIcon class="w-8 h-8" :style="{ color: D.inkTertiary }" />
                  </div>
                </div>
                <div class="text-center space-y-0.5">
                  <p class="text-[15px] font-semibold tracking-widest" :style="{ color: D.inkTertiary }">???</p>
                  <p class="text-[12px]" :style="{ color: D.inkTertiary }">Keep playing to unlock</p>
                  <p class="text-[11px]" :style="{ color: D.inkTertiary }">Hidden</p>
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
        <SparklesOutlineIcon class="w-5 h-5 flex-shrink-0 mt-0.5" :style="{ color: L.accents.lavender.bold }" />
        <div>
          <p class="text-sm font-semibold mb-1" :style="{ color: L.accents.lavender.bold }">
            Claude's pick — Variant C
          </p>
          <p class="text-sm leading-relaxed" :style="{ color: L.inkPrimary }">
            The hexagonal container is doing semantic work that circles and rectangles can't: it imports Steam's established badge grammar so players read "achievement" before they read the title. The arc ring hugging the hex boundary at in-progress creates the tightest possible progress composition — no separate element needed. Reserve A for everyday feed/list views where the flat surface keeps visual cost low; use B on the Achievements landing page hero where a single "just earned" badge deserves the gradient spotlight; reach for C wherever gamification is the primary story — profile trophy case, dedicated Achievements grid, or a celebration modal.
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
            All three variants share one state machine: <strong>locked</strong> (grey/ghost, reduced opacity), <strong>in-progress</strong> (greyscale or tinted glyph + accent arc ring + count label), <strong>earned</strong> (full-accent glyph, no arc, "Earned" success chip). Hidden achievements always render as "???" with <code class="text-xs font-mono px-1 rounded" :style="{ background: L.surfaceSunken, color: L.inkPrimary }">QuestionMarkCircleIcon</code> until earned, regardless of variant.
          </p>
        </div>

        <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-[140px_1fr] gap-3">
          <div>
            <span
              class="inline-flex items-center justify-center h-6 min-w-[1.5rem] px-2 rounded-full text-xs font-semibold"
              :style="{ background: L.inkPrimary, color: L.inkInverse }"
            >A — Flat surface</span>
          </div>
          <div class="space-y-1">
            <p class="text-sm font-medium" :style="{ color: L.inkPrimary }">Everyday feed tiles, task-completion toasts, dense lists of 8+ badges</p>
            <p class="text-sm" :style="{ color: L.inkSecondary }">
              Lowest visual cost — the flat surfaceRaised card keeps the focus on the badge metadata (title, description, progress). The accent-colored glow on the earned state is subtle enough not to compete with surrounding content. Safe default for any context where badge density is higher than two per row.
            </p>
          </div>
        </div>

        <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-[140px_1fr] gap-3">
          <div>
            <span
              class="inline-flex items-center justify-center h-6 min-w-[1.5rem] px-2 rounded-full text-xs font-semibold"
              :style="{ background: L.inkPrimary, color: L.inkInverse }"
            >B — Gradient card</span>
          </div>
          <div class="space-y-1">
            <p class="text-sm font-medium" :style="{ color: L.inkPrimary }">Achievements landing hero, dashboard "just earned" spotlight, profile header</p>
            <p class="text-sm" :style="{ color: L.inkSecondary }">
              The iridescent radial gradient makes earned feel like a collectible and unearned feels appropriately quiet (surface-sunken, no gradient). Best when you want to feature one or two badges prominently rather than scan a whole grid. The contrast between earned (glowing gradient) and locked (flat sunken) is maximally clear in this treatment.
            </p>
          </div>
        </div>

        <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-[140px_1fr] gap-3">
          <div>
            <span
              class="inline-flex items-center justify-center h-6 min-w-[1.5rem] px-2 rounded-full text-xs font-semibold"
              :style="{ background: L.inkPrimary, color: L.inkInverse }"
            >C — Hex badge</span>
          </div>
          <div class="space-y-1">
            <p class="text-sm font-medium" :style="{ color: L.inkPrimary }">Achievements page trophy grid, profile trophy case, celebration modal</p>
            <p class="text-sm" :style="{ color: L.inkSecondary }">
              The hexagonal shape borrows Steam's badge grammar — recognizable as an "achievement" before any label is read. The arc ring hugging the hex boundary at in-progress and the outer pulse-ring on earned are the highest-drama moments in this component set. The SVG clip-path means the hex bleeds correctly into any background — always verify in both light and dark before shipping. Use the dashed outline variant (hex-tile-dashed) for hidden badges so they read as structurally distinct from regular-locked tiles.
            </p>
          </div>
        </div>

        <div class="px-6 py-4">
          <p class="text-xs font-semibold uppercase tracking-widest mb-2" :style="{ color: L.inkTertiary }">Hidden badge mechanic</p>
          <p class="text-sm" :style="{ color: L.inkSecondary }">
            When <code class="text-xs font-mono px-1 rounded" :style="{ background: L.surfaceSunken, color: L.inkPrimary }">is_hidden = true</code> and not yet earned: always render "???" as the title, <code class="text-xs font-mono px-1 rounded" :style="{ background: L.surfaceSunken, color: L.inkPrimary }">QuestionMarkCircleIcon</code> as the glyph, and "Keep playing to unlock" as the description. Never expose the real name, icon category, or progress count until earned. In Variant C, use the dashed hex border (.hex-tile-dashed) to visually separate hidden-locked from regular-locked.
          </p>
        </div>
      </div>
    </section>

  </ComponentPage>
</template>

<style scoped>
/*
  Hexagonal clip-path — flat-top orientation.
  Polygon: 6 points at 60° intervals starting 12-o'clock.
  Applied via .hex-tile on any sized element.
*/
.hex-tile {
  clip-path: polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%);
}

/*
  Dashed-border hex for hidden badges — rendered as a box with
  dashed border that is then clip-pathed to hex shape.
  The clip-path eats the border corners, so the dashes approximate
  the hex edge without a separate SVG stroke.
*/
.hex-tile-dashed {
  clip-path: polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%);
  border: 2px dashed currentColor;
  opacity: 0.6;
}

@media (prefers-reduced-motion: reduce) {
  .hex-tile,
  .hex-tile-dashed {
    animation: none !important;
    transition: none !important;
  }
}
</style>
