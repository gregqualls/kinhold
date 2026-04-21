<script setup>
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import {
  SparklesIcon, TrophyIcon, StarIcon, HeartIcon, FireIcon,
  AcademicCapIcon, QuestionMarkCircleIcon,
} from '@heroicons/vue/24/solid'
import { SparklesIcon as SparklesOutlineIcon } from '@heroicons/vue/24/outline'
import KinAchievementTile from '@/components/design-system/KinAchievementTile.vue'

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
    status="chosen"
  >

    <!-- ═══════════════════════════════════════════════════════════════

    <!-- ═══════════════════════════════════════════════════════════════
         VARIANT C — Hexagonal badge (Steam-style)
         Icon sits in an 80×80px hex container (SVG clipPath polygon).
         Earned = accent-bold hex fill + inset glow + outer pulse ring.
         In-progress = filled hex + arc ring hugging hex boundary.
         Locked = inkTertiary outline ghost hex.
         ════════════════════════════════════════════════════════════════ -->
    <section class="mb-20">
      <VariantFrame
        label="Hex"
        caption="Hexagonal badge · Steam/console-achievement feel. State is communicated by the hex itself: earned = accent-bold fill + inset glow + outer pulse. In-progress = greyed surfaceSunken hex + inkTertiary icon + colored arc ring. Locked = outline ghost hex. Hidden = dashed outline + ??? glyph."
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
                    style="width: 108px; height: 108px; opacity: 0.40;"
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
              </div>

              <!-- C-L · Earned — Kindness Captain (peach) -->
              <div class="flex flex-col items-center gap-3">
                <div class="relative flex items-center justify-center" style="width: 108px; height: 108px;">
                  <div
                    class="hex-tile absolute"
                    style="width: 108px; height: 108px; opacity: 0.40;"
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
              </div>

              <!-- C-L · In-progress — Meal Planner (mint, 65%) -->
              <div class="flex flex-col items-center gap-3">
                <!-- arc ring sits outside the hex -->
                <div class="relative flex items-center justify-center" style="width: 108px; height: 108px;">
                  <!-- Hex-shaped progress ring (perimeter ≈ 350 for this viewBox) -->
                  <svg
                    width="108" height="108" viewBox="0 0 108 108"
                    class="absolute inset-0"
                    aria-hidden="true"
                  >
                    <polygon points="54,4 104,29 104,79 54,104 4,79 4,29"
                      fill="none" :stroke="L.accents.mint.soft" stroke-width="4" stroke-linejoin="round" />
                    <polygon points="54,4 104,29 104,79 54,104 4,79 4,29"
                      fill="none" :stroke="L.accents.mint.bold"
                      stroke-width="4" stroke-linejoin="round" stroke-linecap="round"
                      stroke-dasharray="324"
                      :stroke-dashoffset="324 * (1 - 0.65)" />
                  </svg>
                  <div
                    class="hex-tile flex items-center justify-center relative z-10"
                    style="width: 80px; height: 80px;"
                    :style="{ background: L.borderSubtle }"
                  >
                    <AcademicCapIcon class="w-9 h-9" :style="{ color: L.inkTertiary }" />
                  </div>
                </div>
                <div class="text-center space-y-0.5">
                  <p class="text-[14px] font-semibold" :style="{ color: L.inkPrimary }">Meal Planner</p>
                  <p class="text-[12px]" :style="{ color: L.inkSecondary }">Plan 20 meals</p>
                  <p class="text-[11px] font-medium" :style="{ color: L.accents.mint.bold }">12/20</p>
                </div>
              </div>

              <!-- C-L · In-progress — Streak Starter (peach, 30%) -->
              <div class="flex flex-col items-center gap-3">
                <div class="relative flex items-center justify-center" style="width: 108px; height: 108px;">
                  <svg
                    width="108" height="108" viewBox="0 0 108 108"
                    class="absolute inset-0"
                    aria-hidden="true"
                  >
                    <polygon points="54,4 104,29 104,79 54,104 4,79 4,29"
                      fill="none" :stroke="L.accents.peach.soft" stroke-width="4" stroke-linejoin="round" />
                    <polygon points="54,4 104,29 104,79 54,104 4,79 4,29"
                      fill="none" :stroke="L.accents.peach.bold"
                      stroke-width="4" stroke-linejoin="round" stroke-linecap="round"
                      stroke-dasharray="324"
                      :stroke-dashoffset="324 * (1 - 0.30)" />
                  </svg>
                  <div
                    class="hex-tile flex items-center justify-center relative z-10"
                    style="width: 80px; height: 80px;"
                    :style="{ background: L.borderSubtle }"
                  >
                    <FireIcon class="w-9 h-9" :style="{ color: L.inkTertiary }" />
                  </div>
                </div>
                <div class="text-center space-y-0.5">
                  <p class="text-[14px] font-semibold" :style="{ color: L.inkPrimary }">Streak Starter</p>
                  <p class="text-[12px]" :style="{ color: L.inkSecondary }">3-day streak, 7 to go</p>
                  <p class="text-[11px] font-medium" :style="{ color: L.accents.peach.bold }">3/10 days</p>
                </div>
              </div>

              <!-- C-L · Locked — Century Club -->
              <div class="flex flex-col items-center gap-3" style="opacity: 0.55;">
                <div style="width: 108px; height: 108px; display: flex; align-items: center; justify-content: center;">
                  <div
                    class="hex-tile flex items-center justify-center"
                    style="width: 80px; height: 80px;"
                    :style="{ background: L.borderSubtle }"
                  >
                    <StarIcon class="w-8 h-8" :style="{ color: L.inkTertiary }" />
                  </div>
                </div>
                <div class="text-center space-y-0.5">
                  <p class="text-[14px] font-semibold" :style="{ color: L.inkTertiary }">Century Club</p>
                  <p class="text-[12px]" :style="{ color: L.inkTertiary }">Complete 100 tasks</p>
                  <p class="text-[11px]" :style="{ color: L.inkTertiary }">Locked</p>
                </div>
              </div>

              <!-- C-L · Hidden — ??? (dashed border ghost) -->
              <div class="flex flex-col items-center gap-3" style="opacity: 0.50;">
                <div style="width: 108px; height: 108px; display: flex; align-items: center; justify-content: center;">
                  <div
                    class="hex-tile flex items-center justify-center"
                    style="width: 80px; height: 80px;"
                    :style="{ background: L.borderSubtle }"
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
                    style="width: 108px; height: 108px; opacity: 0.35;"
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
              </div>

              <!-- C-D · Earned — Kindness Captain -->
              <div class="flex flex-col items-center gap-3">
                <div class="relative flex items-center justify-center" style="width: 108px; height: 108px;">
                  <div
                    class="hex-tile absolute"
                    style="width: 108px; height: 108px; opacity: 0.35;"
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
              </div>

              <!-- C-D · In-progress — Meal Planner (65%) -->
              <div class="flex flex-col items-center gap-3">
                <div class="relative flex items-center justify-center" style="width: 108px; height: 108px;">
                  <svg
                    width="108" height="108" viewBox="0 0 108 108"
                    class="absolute inset-0"
                    aria-hidden="true"
                  >
                    <polygon points="54,4 104,29 104,79 54,104 4,79 4,29"
                      fill="none" :stroke="D.accents.mint.soft" stroke-width="4" stroke-linejoin="round" />
                    <polygon points="54,4 104,29 104,79 54,104 4,79 4,29"
                      fill="none" :stroke="D.accents.mint.bold"
                      stroke-width="4" stroke-linejoin="round" stroke-linecap="round"
                      stroke-dasharray="324"
                      :stroke-dashoffset="324 * (1 - 0.65)" />
                  </svg>
                  <div
                    class="hex-tile flex items-center justify-center relative z-10"
                    style="width: 80px; height: 80px;"
                    :style="{ background: D.borderSubtle }"
                  >
                    <AcademicCapIcon class="w-9 h-9" :style="{ color: D.inkTertiary }" />
                  </div>
                </div>
                <div class="text-center space-y-0.5">
                  <p class="text-[14px] font-semibold" :style="{ color: D.inkPrimary }">Meal Planner</p>
                  <p class="text-[12px]" :style="{ color: D.inkSecondary }">Plan 20 meals</p>
                  <p class="text-[11px] font-medium" :style="{ color: D.accents.mint.bold }">12/20</p>
                </div>
              </div>

              <!-- C-D · In-progress — Streak Starter (30%) -->
              <div class="flex flex-col items-center gap-3">
                <div class="relative flex items-center justify-center" style="width: 108px; height: 108px;">
                  <svg
                    width="108" height="108" viewBox="0 0 108 108"
                    class="absolute inset-0"
                    aria-hidden="true"
                  >
                    <polygon points="54,4 104,29 104,79 54,104 4,79 4,29"
                      fill="none" :stroke="D.accents.peach.soft" stroke-width="4" stroke-linejoin="round" />
                    <polygon points="54,4 104,29 104,79 54,104 4,79 4,29"
                      fill="none" :stroke="D.accents.peach.bold"
                      stroke-width="4" stroke-linejoin="round" stroke-linecap="round"
                      stroke-dasharray="324"
                      :stroke-dashoffset="324 * (1 - 0.30)" />
                  </svg>
                  <div
                    class="hex-tile flex items-center justify-center relative z-10"
                    style="width: 80px; height: 80px;"
                    :style="{ background: D.borderSubtle }"
                  >
                    <FireIcon class="w-9 h-9" :style="{ color: D.inkTertiary }" />
                  </div>
                </div>
                <div class="text-center space-y-0.5">
                  <p class="text-[14px] font-semibold" :style="{ color: D.inkPrimary }">Streak Starter</p>
                  <p class="text-[12px]" :style="{ color: D.inkSecondary }">3-day streak, 7 to go</p>
                  <p class="text-[11px] font-medium" :style="{ color: D.accents.peach.bold }">3/10 days</p>
                </div>
              </div>

              <!-- C-D · Locked — Century Club -->
              <div class="flex flex-col items-center gap-3" style="opacity: 0.50;">
                <div style="width: 108px; height: 108px; display: flex; align-items: center; justify-content: center;">
                  <div
                    class="hex-tile flex items-center justify-center"
                    style="width: 80px; height: 80px;"
                    :style="{ background: D.borderSubtle }"
                  >
                    <StarIcon class="w-8 h-8" :style="{ color: D.inkTertiary }" />
                  </div>
                </div>
                <div class="text-center space-y-0.5">
                  <p class="text-[14px] font-semibold" :style="{ color: D.inkTertiary }">Century Club</p>
                  <p class="text-[12px]" :style="{ color: D.inkTertiary }">Complete 100 tasks</p>
                  <p class="text-[11px]" :style="{ color: D.inkTertiary }">Locked</p>
                </div>
              </div>

              <!-- C-D · Hidden — ??? -->
              <div class="flex flex-col items-center gap-3" style="opacity: 0.45;">
                <div style="width: 108px; height: 108px; display: flex; align-items: center; justify-content: center;">
                  <div
                    class="hex-tile flex items-center justify-center"
                    style="width: 80px; height: 80px;"
                    :style="{ background: D.borderSubtle }"
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
            LOCKED — Hexagonal AchievementTile
          </p>
          <p class="text-sm leading-relaxed" :style="{ color: L.inkPrimary }">
            One shape, three states expressed entirely through the hex itself — no status chips. The hexagonal container imports Steam's badge grammar so viewers read "achievement" before they read the title.
          </p>
          <p class="text-sm leading-relaxed mt-2" :style="{ color: L.inkSecondary }">
            <strong>Earned:</strong> full-color accent-bold hex fill, inset white highlight, outer pulse ring.
            <strong>In-progress:</strong> greyed-out hex (surfaceSunken fill + inkTertiary icon) + <em>colored</em> arc ring around the hex showing progress — color is reserved for what the user hasn't achieved yet.
            <strong>Locked:</strong> transparent hex with borderStrong outline + inkTertiary icon.
            <strong>Hidden:</strong> dashed outline + QuestionMarkCircle glyph. Reveals the real badge on earn.
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
            One hex, four states. Used on Achievements page trophy grids, profile trophy case, celebration modals, and dashboard "just earned" spotlights. Badge density of 6+ per row works fine — the hex shape scales down cleanly to 56px without losing identity.
          </p>
        </div>

        <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-[140px_1fr] gap-3">
          <div>
            <span
              class="inline-flex items-center justify-center h-6 min-w-[1.5rem] px-2 rounded-full text-xs font-semibold"
              :style="{ background: L.inkPrimary, color: L.inkInverse }"
            >Earned</span>
          </div>
          <div class="space-y-1">
            <p class="text-sm" :style="{ color: L.inkSecondary }">
              Accent-bold hex fill + inset white highlight + outer pulse ring at 20% accent-bold opacity. Icon is inkInverse. The pulse ring is a second hex sibling behind the primary one — no animation required, the soft halo reads as "lit up."
            </p>
          </div>
        </div>

        <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-[140px_1fr] gap-3">
          <div>
            <span
              class="inline-flex items-center justify-center h-6 min-w-[1.5rem] px-2 rounded-full text-xs font-semibold"
              :style="{ background: L.inkPrimary, color: L.inkInverse }"
            >In progress</span>
          </div>
          <div class="space-y-1">
            <p class="text-sm" :style="{ color: L.inkSecondary }">
              Hex fill is <code class="text-xs font-mono px-1 rounded" :style="{ background: L.surfaceSunken, color: L.inkPrimary }">surfaceSunken</code> and the icon is <code class="text-xs font-mono px-1 rounded" :style="{ background: L.surfaceSunken, color: L.inkPrimary }">inkTertiary</code> — fully greyed out. The arc ring around the hex stays in the accent-bold color at the progress percentage. Color is reserved for what hasn't happened yet, so the eye tracks progress not identity. Progress-count text (e.g. "12/20") below the tile stays in the accent-bold color as an anchor.
            </p>
          </div>
        </div>

        <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-[140px_1fr] gap-3">
          <div>
            <span
              class="inline-flex items-center justify-center h-6 min-w-[1.5rem] px-2 rounded-full text-xs font-semibold"
              :style="{ background: L.inkPrimary, color: L.inkInverse }"
            >Locked</span>
          </div>
          <div class="space-y-1">
            <p class="text-sm" :style="{ color: L.inkSecondary }">
              Hex filled with <code class="text-xs font-mono px-1 rounded" :style="{ background: L.surfaceSunken, color: L.inkPrimary }">borderSubtle</code> + inkTertiary icon. Whole tile wrapper at 55% opacity. No arc (no progress to show). The tile is deliberately quiet — it's a placeholder.
            </p>
          </div>
        </div>

        <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-[140px_1fr] gap-3">
          <div>
            <span
              class="inline-flex items-center justify-center h-6 min-w-[1.5rem] px-2 rounded-full text-xs font-semibold"
              :style="{ background: L.inkPrimary, color: L.inkInverse }"
            >Hidden</span>
          </div>
          <div class="space-y-1">
            <p class="text-sm" :style="{ color: L.inkSecondary }">
              When <code class="text-xs font-mono px-1 rounded" :style="{ background: L.surfaceSunken, color: L.inkPrimary }">is_hidden = true</code> and not yet earned, render "???" as the title, <code class="text-xs font-mono px-1 rounded" :style="{ background: L.surfaceSunken, color: L.inkPrimary }">QuestionMarkCircleIcon</code> as the glyph, and "Keep playing to unlock" as the description. Uses the same greyed hex as locked — the ??? title + question-mark icon + "Hidden" label do the distinguishing work. Never expose the real name or icon category until earned.
            </p>
          </div>
        </div>
      </div>
    </section>


    <!-- KIN COMPONENT PREVIEW -->
    <section class="mb-16">
      <VariantFrame label="Kin" caption="KinAchievementTile — proposed extraction. 4 states: earned, in-progress, locked, hidden.">
        <div class="w-full space-y-10">
          <div class="rounded-2xl border p-6" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest mb-4" :style="{ color: L.inkTertiary }">Light mode</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
              <KinAchievementTile state="earned" :icon="TrophyIcon" accent-color="sun" title="Week Warrior" description="7 tasks in a row" meta="Earned 2d ago" />
              <KinAchievementTile state="earned" :icon="HeartIcon" accent-color="peach" title="Kindness Captain" description="Gave 10 kudos" meta="Earned 5d ago" />
              <KinAchievementTile state="in-progress" :icon="AcademicCapIcon" accent-color="mint" title="Meal Planner" description="Plan 20 meals" meta="12/20" :progress="0.65" />
              <KinAchievementTile state="in-progress" :icon="FireIcon" accent-color="peach" title="Streak Starter" description="3-day streak, 7 to go" meta="3/10 days" :progress="0.30" />
              <KinAchievementTile state="locked" :icon="StarIcon" accent-color="lavender" title="Century Club" description="Complete 100 tasks" meta="Locked" />
              <KinAchievementTile state="hidden" :icon="QuestionMarkCircleIcon" accent-color="lavender" description="Keep playing to unlock" meta="Hidden" />
            </div>
          </div>
          <div class="dark rounded-2xl border p-6" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest mb-4" :style="{ color: D.inkTertiary }">Dark mode</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
              <KinAchievementTile state="earned" :icon="TrophyIcon" accent-color="sun" title="Week Warrior" description="7 tasks in a row" meta="Earned 2d ago" />
              <KinAchievementTile state="earned" :icon="HeartIcon" accent-color="peach" title="Kindness Captain" description="Gave 10 kudos" meta="Earned 5d ago" />
              <KinAchievementTile state="in-progress" :icon="AcademicCapIcon" accent-color="mint" title="Meal Planner" description="Plan 20 meals" meta="12/20" :progress="0.65" />
              <KinAchievementTile state="in-progress" :icon="FireIcon" accent-color="peach" title="Streak Starter" description="3-day streak, 7 to go" meta="3/10 days" :progress="0.30" />
              <KinAchievementTile state="locked" :icon="StarIcon" accent-color="lavender" title="Century Club" description="Complete 100 tasks" meta="Locked" />
              <KinAchievementTile state="hidden" :icon="QuestionMarkCircleIcon" accent-color="lavender" description="Keep playing to unlock" meta="Hidden" />
            </div>
          </div>
        </div>
      </VariantFrame>
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

@media (prefers-reduced-motion: reduce) {
  .hex-tile {
    animation: none !important;
    transition: none !important;
  }
}
</style>
