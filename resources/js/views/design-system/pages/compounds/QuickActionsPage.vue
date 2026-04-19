<script setup>
import { ref } from 'vue'
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import {
  CalendarDaysIcon, CheckCircleIcon, HandThumbUpIcon, SparklesIcon,
  PlusCircleIcon, ListBulletIcon, ArrowPathIcon, UserPlusIcon,
  ArrowUpTrayIcon, Squares2X2Icon,
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

const SHADOW_RESTING_LT = '0 1px 2px rgba(28, 20, 10, 0.04), 0 2px 6px rgba(28, 20, 10, 0.05)'
const SHADOW_HOVER_LT   = '0 2px 4px rgba(28, 20, 10, 0.06), 0 8px 16px rgba(28, 20, 10, 0.07)'
const SHADOW_RESTING_DK = '0 1px 2px rgba(0, 0, 0, 0.30), 0 2px 6px rgba(0, 0, 0, 0.25)'
const SHADOW_HOVER_DK   = '0 2px 4px rgba(0, 0, 0, 0.40), 0 8px 16px rgba(0, 0, 0, 0.30)'

// ── Action data ───────────────────────────────────────────────────────────────
const ACCENT_KEYS = ['lavender', 'peach', 'mint', 'sun']

const DASHBOARD_ACTIONS = [
  { key: 'event', label: 'Add event',      accent: 'lavender', icon: CalendarDaysIcon,  badge: null,     disabled: false },
  { key: 'task',  label: 'Add task',        accent: 'peach',    icon: CheckCircleIcon,   badge: 3,        disabled: false },
  { key: 'kudos', label: 'Log kudos',       accent: 'mint',     icon: HandThumbUpIcon,   badge: null,     disabled: false },
  { key: 'ask',   label: 'Ask assistant',   accent: 'sun',      icon: SparklesIcon,      badge: null,     disabled: true  },
]

const TASKS_ACTIONS = [
  { key: 'quick',      label: 'Quick task',    accent: 'lavender', icon: PlusCircleIcon,   badge: null, disabled: false },
  { key: 'list',       label: 'New list',      accent: 'peach',    icon: ListBulletIcon,   badge: null, disabled: false },
  { key: 'recurring',  label: 'Recurring',     accent: 'mint',     icon: ArrowPathIcon,    badge: null, disabled: false },
  { key: 'assign',     label: 'Assign',        accent: 'sun',      icon: UserPlusIcon,     badge: null, disabled: false },
  { key: 'import',     label: 'Import',        accent: 'lavender', icon: ArrowUpTrayIcon,  badge: null, disabled: false },
  { key: 'templates',  label: 'Templates',     accent: 'peach',    icon: Squares2X2Icon,   badge: null, disabled: false },
]

// ── Hover state tracking ──────────────────────────────────────────────────────
// Variants A light/dark, B light/dark, C light/dark — each for both grids
const hoveredA_L = ref(null)
const hoveredA_D = ref(null)
const hoveredB_L = ref(null)
const hoveredB_D = ref(null)
const hoveredC_L = ref(null)
const hoveredC_D = ref(null)
</script>

<template>
  <ComponentPage
    title="4.5 QuickActions"
    description="A grid of 4 (or 6) square icon + label tiles for the top of module landing pages — the fastest tap targets for the most common actions. Used on Dashboard, Tasks, Meal Plan, Points, and Vault landing views."
    status="chosen"
  >

    <!-- ══════════════════════════════════════════════════════════════
         VARIANT A — Flat surface-raised squares
         Outline border, icon top (solid 24px, inkPrimary), label below.
         Hover: shadow-resting → shadow-hover + translateY(-2px).
         4-up on desktop, 2-up on mobile.
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="A" caption="Flat surface-raised squares · outline border · icon inkPrimary · hover lift">
        <div class="w-full space-y-10">

          <!-- LIGHT PANEL -->
          <div class="rounded-2xl border p-6 space-y-8"
               :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest"
               :style="{ color: L.inkTertiary }">Light mode</p>

            <!-- 4-up desktop -->
            <div>
              <p class="text-[11px] font-medium mb-3" :style="{ color: L.inkTertiary }">4-up · Dashboard actions</p>
              <div class="grid grid-cols-4 gap-3">
                <button
                  v-for="action in DASHBOARD_ACTIONS"
                  :key="action.key"
                  class="qa-tile-a-lt relative flex flex-col items-center justify-center gap-2 rounded-[20px] p-4 border"
                  style="min-height: 88px;"
                  :style="{
                    background: L.surfaceRaised,
                    borderColor: hoveredA_L === action.key && !action.disabled ? L.borderStrong : L.borderSubtle,
                    boxShadow: hoveredA_L === action.key && !action.disabled ? SHADOW_HOVER_LT : SHADOW_RESTING_LT,
                    transform: hoveredA_L === action.key && !action.disabled ? 'translateY(-2px)' : 'translateY(0)',
                    opacity: action.disabled ? '0.40' : '1',
                    cursor: action.disabled ? 'not-allowed' : 'pointer',
                  }"
                  :disabled="action.disabled"
                  @mouseenter="!action.disabled && (hoveredA_L = action.key)"
                  @mouseleave="hoveredA_L = null"
                >
                  <!-- Badge pill -->
                  <span
                    v-if="action.badge"
                    class="absolute top-2 right-2 flex items-center justify-center rounded-full text-[10px] font-semibold"
                    style="min-width: 18px; height: 18px; padding: 0 5px;"
                    :style="{ background: L.accents.peach.bold, color: '#FFFFFF' }"
                  >{{ action.badge }}</span>

                  <component :is="action.icon" class="w-6 h-6 flex-shrink-0" :style="{ color: L.inkPrimary }" />
                  <span class="text-[12px] font-medium leading-tight text-center" :style="{ color: L.inkPrimary }">{{ action.label }}</span>
                </button>
              </div>
            </div>

            <!-- 6-up desktop -->
            <div>
              <p class="text-[11px] font-medium mb-3" :style="{ color: L.inkTertiary }">6-up · Tasks actions (denser context)</p>
              <div class="grid grid-cols-6 gap-3">
                <button
                  v-for="action in TASKS_ACTIONS"
                  :key="action.key"
                  class="qa-tile-a-lt flex flex-col items-center justify-center gap-2 rounded-[20px] p-3 border"
                  style="min-height: 88px;"
                  :style="{
                    background: L.surfaceRaised,
                    borderColor: L.borderSubtle,
                    boxShadow: SHADOW_RESTING_LT,
                  }"
                >
                  <component :is="action.icon" class="w-6 h-6 flex-shrink-0" :style="{ color: L.inkPrimary }" />
                  <span class="text-[11px] font-medium leading-tight text-center" :style="{ color: L.inkPrimary }">{{ action.label }}</span>
                </button>
              </div>
            </div>

            <!-- Mobile 2-up -->
            <div>
              <p class="text-[11px] font-medium mb-3" :style="{ color: L.inkTertiary }">Mobile 2-up · max-w-[320px] · 4 tiles in 2×2</p>
              <div class="max-w-[320px]">
                <div class="grid grid-cols-2 gap-3">
                  <button
                    v-for="action in DASHBOARD_ACTIONS"
                    :key="action.key"
                    class="qa-tile-a-lt flex flex-col items-center justify-center gap-2 rounded-[20px] p-4 border"
                    style="min-height: 88px;"
                    :style="{
                      background: L.surfaceRaised,
                      borderColor: L.borderSubtle,
                      boxShadow: SHADOW_RESTING_LT,
                      opacity: action.disabled ? '0.40' : '1',
                      cursor: action.disabled ? 'not-allowed' : 'pointer',
                    }"
                    :disabled="action.disabled"
                  >
                    <component :is="action.icon" class="w-6 h-6 flex-shrink-0" :style="{ color: L.inkPrimary }" />
                    <span class="text-[12px] font-medium leading-tight text-center" :style="{ color: L.inkPrimary }">{{ action.label }}</span>
                  </button>
                </div>
              </div>
            </div>

            <p class="text-[11px]" :style="{ color: L.inkTertiary }">
              Hover: border tightens to border-strong + 2px vertical lift + shadow deepens. Disabled at 40% opacity + cursor-not-allowed. Badge: accent-bold pill in top-right corner. 6-up uses 11px labels for density.
            </p>
          </div>

          <!-- DARK PANEL -->
          <div class="rounded-2xl border p-6 space-y-8"
               :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest"
               :style="{ color: D.inkTertiary }">Dark mode</p>

            <!-- 4-up desktop dark -->
            <div>
              <p class="text-[11px] font-medium mb-3" :style="{ color: D.inkTertiary }">4-up · Dashboard actions</p>
              <div class="grid grid-cols-4 gap-3">
                <button
                  v-for="action in DASHBOARD_ACTIONS"
                  :key="action.key"
                  class="qa-tile-a-dk relative flex flex-col items-center justify-center gap-2 rounded-[20px] p-4 border"
                  style="min-height: 88px;"
                  :style="{
                    background: D.surfaceRaised,
                    borderColor: hoveredA_D === action.key && !action.disabled ? D.borderStrong : D.borderSubtle,
                    boxShadow: hoveredA_D === action.key && !action.disabled ? SHADOW_HOVER_DK : SHADOW_RESTING_DK,
                    transform: hoveredA_D === action.key && !action.disabled ? 'translateY(-2px)' : 'translateY(0)',
                    opacity: action.disabled ? '0.40' : '1',
                    cursor: action.disabled ? 'not-allowed' : 'pointer',
                  }"
                  :disabled="action.disabled"
                  @mouseenter="!action.disabled && (hoveredA_D = action.key)"
                  @mouseleave="hoveredA_D = null"
                >
                  <span
                    v-if="action.badge"
                    class="absolute top-2 right-2 flex items-center justify-center rounded-full text-[10px] font-semibold"
                    style="min-width: 18px; height: 18px; padding: 0 5px;"
                    :style="{ background: D.accents.peach.bold, color: D.inkInverse }"
                  >{{ action.badge }}</span>

                  <component :is="action.icon" class="w-6 h-6 flex-shrink-0" :style="{ color: D.inkPrimary }" />
                  <span class="text-[12px] font-medium leading-tight text-center" :style="{ color: D.inkPrimary }">{{ action.label }}</span>
                </button>
              </div>
            </div>

            <!-- 6-up desktop dark -->
            <div>
              <p class="text-[11px] font-medium mb-3" :style="{ color: D.inkTertiary }">6-up · Tasks actions</p>
              <div class="grid grid-cols-6 gap-3">
                <button
                  v-for="action in TASKS_ACTIONS"
                  :key="action.key"
                  class="qa-tile-a-dk flex flex-col items-center justify-center gap-2 rounded-[20px] p-3 border"
                  style="min-height: 88px;"
                  :style="{
                    background: D.surfaceRaised,
                    borderColor: D.borderSubtle,
                    boxShadow: SHADOW_RESTING_DK,
                  }"
                >
                  <component :is="action.icon" class="w-6 h-6 flex-shrink-0" :style="{ color: D.inkPrimary }" />
                  <span class="text-[11px] font-medium leading-tight text-center" :style="{ color: D.inkPrimary }">{{ action.label }}</span>
                </button>
              </div>
            </div>

            <!-- Mobile 2-up dark -->
            <div>
              <p class="text-[11px] font-medium mb-3" :style="{ color: D.inkTertiary }">Mobile 2-up · max-w-[320px]</p>
              <div class="max-w-[320px]">
                <div class="grid grid-cols-2 gap-3">
                  <button
                    v-for="action in DASHBOARD_ACTIONS"
                    :key="action.key"
                    class="qa-tile-a-dk flex flex-col items-center justify-center gap-2 rounded-[20px] p-4 border"
                    style="min-height: 88px;"
                    :style="{
                      background: D.surfaceRaised,
                      borderColor: D.borderSubtle,
                      boxShadow: SHADOW_RESTING_DK,
                      opacity: action.disabled ? '0.40' : '1',
                      cursor: action.disabled ? 'not-allowed' : 'pointer',
                    }"
                    :disabled="action.disabled"
                  >
                    <component :is="action.icon" class="w-6 h-6 flex-shrink-0" :style="{ color: D.inkPrimary }" />
                    <span class="text-[12px] font-medium leading-tight text-center" :style="{ color: D.inkPrimary }">{{ action.label }}</span>
                  </button>
                </div>
              </div>
            </div>

            <p class="text-[11px]" :style="{ color: D.inkTertiary }">
              Dark: surfaceRaised (#1C1B19) tile on surfaceApp (#141311) background. Border subtle (#2C2A27) ≈ barely visible at rest — hover reveals border-strong (#403E3A). Badge uses peach-bold for count salience.
            </p>
          </div>

        </div>
      </VariantFrame>
      <p class="mt-3 text-sm px-1" :style="{ color: L.inkSecondary }">
        Variant A is the neutral workhorse — monochrome, clean, and purely structural. Reads fast on any background without color conflicts. Pairs well with a colourful page header above it.
      </p>
    </section>


    <!-- ══════════════════════════════════════════════════════════════
         VARIANT B — Iridescent GradientCard squares
         Each tile gets a radial gradient from its accent soft color.
         Icon top-left in accent-bold, label bottom-left. No outer border.
         "Hero actions" energy — most visually expressive.
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="B" caption="Iridescent GradientCard squares · radial accent gradient · icon top-left · label bottom-left · no border">
        <div class="w-full space-y-10">

          <!-- LIGHT PANEL -->
          <div class="rounded-2xl border p-6 space-y-8"
               :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest"
               :style="{ color: L.inkTertiary }">Light mode</p>

            <!-- 4-up desktop -->
            <div>
              <p class="text-[11px] font-medium mb-3" :style="{ color: L.inkTertiary }">4-up · Dashboard actions</p>
              <div class="grid grid-cols-4 gap-3">
                <button
                  v-for="action in DASHBOARD_ACTIONS"
                  :key="action.key"
                  class="qa-tile-b-lt relative rounded-[20px] overflow-hidden"
                  style="min-height: 88px; aspect-ratio: 1;"
                  :style="{
                    background: `radial-gradient(circle at 20% 20%, ${L.accents[action.accent].soft}, ${L.surfaceRaised} 70%)`,
                    boxShadow: hoveredB_L === action.key && !action.disabled ? SHADOW_HOVER_LT : SHADOW_RESTING_LT,
                    transform: hoveredB_L === action.key && !action.disabled ? 'translateY(-2px)' : 'translateY(0)',
                    opacity: action.disabled ? '0.40' : '1',
                    cursor: action.disabled ? 'not-allowed' : 'pointer',
                  }"
                  :disabled="action.disabled"
                  @mouseenter="!action.disabled && (hoveredB_L = action.key)"
                  @mouseleave="hoveredB_L = null"
                >
                  <!-- Badge pill -->
                  <span
                    v-if="action.badge"
                    class="absolute top-2 right-2 flex items-center justify-center rounded-full text-[10px] font-semibold"
                    style="min-width: 18px; height: 18px; padding: 0 5px;"
                    :style="{ background: L.accents[action.accent].bold, color: '#FFFFFF' }"
                  >{{ action.badge }}</span>

                  <!-- Icon top-left -->
                  <div class="absolute top-3 left-3">
                    <component :is="action.icon" class="w-6 h-6" :style="{ color: L.accents[action.accent].bold }" />
                  </div>
                  <!-- Label bottom-left -->
                  <div class="absolute bottom-3 left-3 right-3">
                    <span class="text-[12px] font-semibold leading-tight block" :style="{ color: L.accents[action.accent].bold }">{{ action.label }}</span>
                  </div>
                </button>
              </div>
            </div>

            <!-- 6-up desktop -->
            <div>
              <p class="text-[11px] font-medium mb-3" :style="{ color: L.inkTertiary }">6-up · Tasks actions</p>
              <div class="grid grid-cols-6 gap-3">
                <button
                  v-for="(action, i) in TASKS_ACTIONS"
                  :key="action.key"
                  class="qa-tile-b-lt relative rounded-[20px] overflow-hidden"
                  style="min-height: 88px; aspect-ratio: 1;"
                  :style="{
                    background: `radial-gradient(circle at 20% 20%, ${L.accents[ACCENT_KEYS[i % 4]].soft}, ${L.surfaceRaised} 70%)`,
                    boxShadow: SHADOW_RESTING_LT,
                  }"
                >
                  <div class="absolute top-3 left-3">
                    <component :is="action.icon" class="w-5 h-5" :style="{ color: L.accents[ACCENT_KEYS[i % 4]].bold }" />
                  </div>
                  <div class="absolute bottom-3 left-3 right-3">
                    <span class="text-[11px] font-semibold leading-tight block" :style="{ color: L.accents[ACCENT_KEYS[i % 4]].bold }">{{ action.label }}</span>
                  </div>
                </button>
              </div>
            </div>

            <!-- Mobile 2-up -->
            <div>
              <p class="text-[11px] font-medium mb-3" :style="{ color: L.inkTertiary }">Mobile 2-up · max-w-[320px]</p>
              <div class="max-w-[320px]">
                <div class="grid grid-cols-2 gap-3">
                  <button
                    v-for="action in DASHBOARD_ACTIONS"
                    :key="action.key"
                    class="qa-tile-b-lt relative rounded-[20px] overflow-hidden"
                    style="min-height: 100px; aspect-ratio: 1;"
                    :style="{
                      background: `radial-gradient(circle at 20% 20%, ${L.accents[action.accent].soft}, ${L.surfaceRaised} 70%)`,
                      boxShadow: SHADOW_RESTING_LT,
                      opacity: action.disabled ? '0.40' : '1',
                      cursor: action.disabled ? 'not-allowed' : 'pointer',
                    }"
                    :disabled="action.disabled"
                  >
                    <div class="absolute top-4 left-4">
                      <component :is="action.icon" class="w-6 h-6" :style="{ color: L.accents[action.accent].bold }" />
                    </div>
                    <div class="absolute bottom-4 left-4 right-4">
                      <span class="text-[12px] font-semibold leading-tight block" :style="{ color: L.accents[action.accent].bold }">{{ action.label }}</span>
                    </div>
                  </button>
                </div>
              </div>
            </div>

            <p class="text-[11px]" :style="{ color: L.inkTertiary }">
              Gradient: <code style="font-family: monospace; font-size: 10px;">radial-gradient(circle at 20% 20%, accent-soft, surfaceRaised 70%)</code> — subtle wash, not saturated. Icon + label both use accent-bold; no ink-primary text used at all. No border — the shadow creates the card edge.
            </p>
          </div>

          <!-- DARK PANEL -->
          <div class="rounded-2xl border p-6 space-y-8"
               :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest"
               :style="{ color: D.inkTertiary }">Dark mode</p>

            <!-- 4-up desktop dark -->
            <div>
              <p class="text-[11px] font-medium mb-3" :style="{ color: D.inkTertiary }">4-up · Dashboard actions</p>
              <div class="grid grid-cols-4 gap-3">
                <button
                  v-for="action in DASHBOARD_ACTIONS"
                  :key="action.key"
                  class="qa-tile-b-dk relative rounded-[20px] overflow-hidden"
                  style="min-height: 88px; aspect-ratio: 1;"
                  :style="{
                    background: `radial-gradient(circle at 20% 20%, ${D.accents[action.accent].soft}, ${D.surfaceRaised} 70%)`,
                    boxShadow: hoveredB_D === action.key && !action.disabled ? SHADOW_HOVER_DK : SHADOW_RESTING_DK,
                    transform: hoveredB_D === action.key && !action.disabled ? 'translateY(-2px)' : 'translateY(0)',
                    opacity: action.disabled ? '0.40' : '1',
                    cursor: action.disabled ? 'not-allowed' : 'pointer',
                  }"
                  :disabled="action.disabled"
                  @mouseenter="!action.disabled && (hoveredB_D = action.key)"
                  @mouseleave="hoveredB_D = null"
                >
                  <span
                    v-if="action.badge"
                    class="absolute top-2 right-2 flex items-center justify-center rounded-full text-[10px] font-semibold"
                    style="min-width: 18px; height: 18px; padding: 0 5px;"
                    :style="{ background: D.accents[action.accent].bold, color: D.inkInverse }"
                  >{{ action.badge }}</span>

                  <div class="absolute top-3 left-3">
                    <component :is="action.icon" class="w-6 h-6" :style="{ color: D.accents[action.accent].bold }" />
                  </div>
                  <div class="absolute bottom-3 left-3 right-3">
                    <span class="text-[12px] font-semibold leading-tight block" :style="{ color: D.accents[action.accent].bold }">{{ action.label }}</span>
                  </div>
                </button>
              </div>
            </div>

            <!-- 6-up desktop dark -->
            <div>
              <p class="text-[11px] font-medium mb-3" :style="{ color: D.inkTertiary }">6-up · Tasks actions</p>
              <div class="grid grid-cols-6 gap-3">
                <button
                  v-for="(action, i) in TASKS_ACTIONS"
                  :key="action.key"
                  class="qa-tile-b-dk relative rounded-[20px] overflow-hidden"
                  style="min-height: 88px; aspect-ratio: 1;"
                  :style="{
                    background: `radial-gradient(circle at 20% 20%, ${D.accents[ACCENT_KEYS[i % 4]].soft}, ${D.surfaceRaised} 70%)`,
                    boxShadow: SHADOW_RESTING_DK,
                  }"
                >
                  <div class="absolute top-3 left-3">
                    <component :is="action.icon" class="w-5 h-5" :style="{ color: D.accents[ACCENT_KEYS[i % 4]].bold }" />
                  </div>
                  <div class="absolute bottom-3 left-3 right-3">
                    <span class="text-[11px] font-semibold leading-tight block" :style="{ color: D.accents[ACCENT_KEYS[i % 4]].bold }">{{ action.label }}</span>
                  </div>
                </button>
              </div>
            </div>

            <!-- Mobile 2-up dark -->
            <div>
              <p class="text-[11px] font-medium mb-3" :style="{ color: D.inkTertiary }">Mobile 2-up · max-w-[320px]</p>
              <div class="max-w-[320px]">
                <div class="grid grid-cols-2 gap-3">
                  <button
                    v-for="action in DASHBOARD_ACTIONS"
                    :key="action.key"
                    class="qa-tile-b-dk relative rounded-[20px] overflow-hidden"
                    style="min-height: 100px; aspect-ratio: 1;"
                    :style="{
                      background: `radial-gradient(circle at 20% 20%, ${D.accents[action.accent].soft}, ${D.surfaceRaised} 70%)`,
                      boxShadow: SHADOW_RESTING_DK,
                      opacity: action.disabled ? '0.40' : '1',
                      cursor: action.disabled ? 'not-allowed' : 'pointer',
                    }"
                    :disabled="action.disabled"
                  >
                    <div class="absolute top-4 left-4">
                      <component :is="action.icon" class="w-6 h-6" :style="{ color: D.accents[action.accent].bold }" />
                    </div>
                    <div class="absolute bottom-4 left-4 right-4">
                      <span class="text-[12px] font-semibold leading-tight block" :style="{ color: D.accents[action.accent].bold }">{{ action.label }}</span>
                    </div>
                  </button>
                </div>
              </div>
            </div>

            <p class="text-[11px]" :style="{ color: D.inkTertiary }">
              Dark gradient: accent soft colors are already the dark-mode tokens (#302A48 lavender, #3E241A peach, etc.) — deep and rich without glowing. No surfaceRaised border visible; shadow carries all depth.
            </p>
          </div>

        </div>
      </VariantFrame>
      <p class="mt-3 text-sm px-1" :style="{ color: L.inkSecondary }">
        Variant B has the most visual energy — each tile declares its identity through color, making the action palette feel curated and branded. Best when you want the QuickActions row to be a visual centerpiece, not a utility strip.
      </p>
    </section>


    <!-- ══════════════════════════════════════════════════════════════
         VARIANT C — Outlined squares with accent icon + signature strip
         Each tile's accent rotates through the palette.
         Accent appears only as: icon color + 2px bottom-border strip.
         Utilitarian but alive — fast to scan, light visual weight.
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="C" caption="Outlined squares · accent-bold icon + 2px bottom-strip signature · inkPrimary label · utilitarian + alive">
        <div class="w-full space-y-10">

          <!-- LIGHT PANEL -->
          <div class="rounded-2xl border p-6 space-y-8"
               :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest"
               :style="{ color: L.inkTertiary }">Light mode</p>

            <!-- 4-up desktop -->
            <div>
              <p class="text-[11px] font-medium mb-3" :style="{ color: L.inkTertiary }">4-up · Dashboard actions</p>
              <div class="grid grid-cols-4 gap-3">
                <button
                  v-for="action in DASHBOARD_ACTIONS"
                  :key="action.key"
                  class="qa-tile-c-lt relative flex flex-col items-center justify-center gap-2 rounded-[20px] overflow-hidden border"
                  style="min-height: 88px;"
                  :style="{
                    background: hoveredC_L === action.key && !action.disabled ? L.accents[action.accent].soft : L.surfaceRaised,
                    borderColor: hoveredC_L === action.key && !action.disabled ? L.accents[action.accent].bold : L.borderSubtle,
                    boxShadow: hoveredC_L === action.key && !action.disabled ? SHADOW_HOVER_LT : SHADOW_RESTING_LT,
                    transform: hoveredC_L === action.key && !action.disabled ? 'translateY(-2px)' : 'translateY(0)',
                    opacity: action.disabled ? '0.40' : '1',
                    cursor: action.disabled ? 'not-allowed' : 'pointer',
                    paddingBottom: '6px',
                  }"
                  :disabled="action.disabled"
                  @mouseenter="!action.disabled && (hoveredC_L = action.key)"
                  @mouseleave="hoveredC_L = null"
                >
                  <!-- Badge pill -->
                  <span
                    v-if="action.badge"
                    class="absolute top-2 right-2 flex items-center justify-center rounded-full text-[10px] font-semibold"
                    style="min-width: 18px; height: 18px; padding: 0 5px;"
                    :style="{ background: L.accents[action.accent].bold, color: '#FFFFFF' }"
                  >{{ action.badge }}</span>

                  <component :is="action.icon" class="w-6 h-6 flex-shrink-0" :style="{ color: L.accents[action.accent].bold }" />
                  <span class="text-[12px] font-medium leading-tight text-center" :style="{ color: L.inkPrimary }">{{ action.label }}</span>

                  <!-- 2px accent bottom strip (signature) -->
                  <span
                    class="absolute bottom-0 left-0 right-0"
                    style="height: 3px; border-radius: 0 0 20px 20px;"
                    :style="{ background: L.accents[action.accent].bold }"
                  />
                </button>
              </div>
            </div>

            <!-- 6-up desktop -->
            <div>
              <p class="text-[11px] font-medium mb-3" :style="{ color: L.inkTertiary }">6-up · Tasks actions</p>
              <div class="grid grid-cols-6 gap-3">
                <button
                  v-for="(action, i) in TASKS_ACTIONS"
                  :key="action.key"
                  class="qa-tile-c-lt relative flex flex-col items-center justify-center gap-1.5 rounded-[20px] overflow-hidden border"
                  style="min-height: 88px; padding-bottom: 6px;"
                  :style="{
                    background: L.surfaceRaised,
                    borderColor: L.borderSubtle,
                    boxShadow: SHADOW_RESTING_LT,
                  }"
                >
                  <component :is="action.icon" class="w-5 h-5 flex-shrink-0" :style="{ color: L.accents[ACCENT_KEYS[i % 4]].bold }" />
                  <span class="text-[11px] font-medium leading-tight text-center" :style="{ color: L.inkPrimary }">{{ action.label }}</span>
                  <span
                    class="absolute bottom-0 left-0 right-0"
                    style="height: 3px; border-radius: 0 0 20px 20px;"
                    :style="{ background: L.accents[ACCENT_KEYS[i % 4]].bold }"
                  />
                </button>
              </div>
            </div>

            <!-- Mobile 2-up -->
            <div>
              <p class="text-[11px] font-medium mb-3" :style="{ color: L.inkTertiary }">Mobile 2-up · max-w-[320px]</p>
              <div class="max-w-[320px]">
                <div class="grid grid-cols-2 gap-3">
                  <button
                    v-for="action in DASHBOARD_ACTIONS"
                    :key="action.key"
                    class="qa-tile-c-lt relative flex flex-col items-center justify-center gap-2 rounded-[20px] overflow-hidden border"
                    style="min-height: 88px; padding-bottom: 6px;"
                    :style="{
                      background: L.surfaceRaised,
                      borderColor: L.borderSubtle,
                      boxShadow: SHADOW_RESTING_LT,
                      opacity: action.disabled ? '0.40' : '1',
                      cursor: action.disabled ? 'not-allowed' : 'pointer',
                    }"
                    :disabled="action.disabled"
                  >
                    <component :is="action.icon" class="w-6 h-6 flex-shrink-0" :style="{ color: L.accents[action.accent].bold }" />
                    <span class="text-[12px] font-medium leading-tight text-center" :style="{ color: L.inkPrimary }">{{ action.label }}</span>
                    <span
                      class="absolute bottom-0 left-0 right-0"
                      style="height: 3px; border-radius: 0 0 20px 20px;"
                      :style="{ background: L.accents[action.accent].bold }"
                    />
                  </button>
                </div>
              </div>
            </div>

            <p class="text-[11px]" :style="{ color: L.inkTertiary }">
              Accent strip: 3px flush-bottom bar, border-radius follows the card corner. Hover: accent-soft background floods in + border tightens to accent-bold + lift. Reads as "selected" without losing the strip identity. Label stays inkPrimary throughout.
            </p>
          </div>

          <!-- DARK PANEL -->
          <div class="rounded-2xl border p-6 space-y-8"
               :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest"
               :style="{ color: D.inkTertiary }">Dark mode</p>

            <!-- 4-up desktop dark -->
            <div>
              <p class="text-[11px] font-medium mb-3" :style="{ color: D.inkTertiary }">4-up · Dashboard actions</p>
              <div class="grid grid-cols-4 gap-3">
                <button
                  v-for="action in DASHBOARD_ACTIONS"
                  :key="action.key"
                  class="qa-tile-c-dk relative flex flex-col items-center justify-center gap-2 rounded-[20px] overflow-hidden border"
                  style="min-height: 88px; padding-bottom: 6px;"
                  :style="{
                    background: hoveredC_D === action.key && !action.disabled ? D.accents[action.accent].soft : D.surfaceRaised,
                    borderColor: hoveredC_D === action.key && !action.disabled ? D.accents[action.accent].bold : D.borderSubtle,
                    boxShadow: hoveredC_D === action.key && !action.disabled ? SHADOW_HOVER_DK : SHADOW_RESTING_DK,
                    transform: hoveredC_D === action.key && !action.disabled ? 'translateY(-2px)' : 'translateY(0)',
                    opacity: action.disabled ? '0.40' : '1',
                    cursor: action.disabled ? 'not-allowed' : 'pointer',
                  }"
                  :disabled="action.disabled"
                  @mouseenter="!action.disabled && (hoveredC_D = action.key)"
                  @mouseleave="hoveredC_D = null"
                >
                  <span
                    v-if="action.badge"
                    class="absolute top-2 right-2 flex items-center justify-center rounded-full text-[10px] font-semibold"
                    style="min-width: 18px; height: 18px; padding: 0 5px;"
                    :style="{ background: D.accents[action.accent].bold, color: D.inkInverse }"
                  >{{ action.badge }}</span>

                  <component :is="action.icon" class="w-6 h-6 flex-shrink-0" :style="{ color: D.accents[action.accent].bold }" />
                  <span class="text-[12px] font-medium leading-tight text-center" :style="{ color: D.inkPrimary }">{{ action.label }}</span>

                  <span
                    class="absolute bottom-0 left-0 right-0"
                    style="height: 3px; border-radius: 0 0 20px 20px;"
                    :style="{ background: D.accents[action.accent].bold }"
                  />
                </button>
              </div>
            </div>

            <!-- 6-up desktop dark -->
            <div>
              <p class="text-[11px] font-medium mb-3" :style="{ color: D.inkTertiary }">6-up · Tasks actions</p>
              <div class="grid grid-cols-6 gap-3">
                <button
                  v-for="(action, i) in TASKS_ACTIONS"
                  :key="action.key"
                  class="qa-tile-c-dk relative flex flex-col items-center justify-center gap-1.5 rounded-[20px] overflow-hidden border"
                  style="min-height: 88px; padding-bottom: 6px;"
                  :style="{
                    background: D.surfaceRaised,
                    borderColor: D.borderSubtle,
                    boxShadow: SHADOW_RESTING_DK,
                  }"
                >
                  <component :is="action.icon" class="w-5 h-5 flex-shrink-0" :style="{ color: D.accents[ACCENT_KEYS[i % 4]].bold }" />
                  <span class="text-[11px] font-medium leading-tight text-center" :style="{ color: D.inkPrimary }">{{ action.label }}</span>
                  <span
                    class="absolute bottom-0 left-0 right-0"
                    style="height: 3px; border-radius: 0 0 20px 20px;"
                    :style="{ background: D.accents[ACCENT_KEYS[i % 4]].bold }"
                  />
                </button>
              </div>
            </div>

            <!-- Mobile 2-up dark -->
            <div>
              <p class="text-[11px] font-medium mb-3" :style="{ color: D.inkTertiary }">Mobile 2-up · max-w-[320px]</p>
              <div class="max-w-[320px]">
                <div class="grid grid-cols-2 gap-3">
                  <button
                    v-for="action in DASHBOARD_ACTIONS"
                    :key="action.key"
                    class="qa-tile-c-dk relative flex flex-col items-center justify-center gap-2 rounded-[20px] overflow-hidden border"
                    style="min-height: 88px; padding-bottom: 6px;"
                    :style="{
                      background: D.surfaceRaised,
                      borderColor: D.borderSubtle,
                      boxShadow: SHADOW_RESTING_DK,
                      opacity: action.disabled ? '0.40' : '1',
                      cursor: action.disabled ? 'not-allowed' : 'pointer',
                    }"
                    :disabled="action.disabled"
                  >
                    <component :is="action.icon" class="w-6 h-6 flex-shrink-0" :style="{ color: D.accents[action.accent].bold }" />
                    <span class="text-[12px] font-medium leading-tight text-center" :style="{ color: D.inkPrimary }">{{ action.label }}</span>
                    <span
                      class="absolute bottom-0 left-0 right-0"
                      style="height: 3px; border-radius: 0 0 20px 20px;"
                      :style="{ background: D.accents[action.accent].bold }"
                    />
                  </button>
                </div>
              </div>
            </div>

            <p class="text-[11px]" :style="{ color: D.inkTertiary }">
              Dark accent strips glow gently — lavender #B6A8E6, peach #F0A882, mint #7CD6AE, sun #E6C452. Against the dark tile (#1C1B19) these are vivid but not garish. Hover floods in the deep accent-soft background, making the tile feel lit from within.
            </p>
          </div>

        </div>
      </VariantFrame>
      <p class="mt-3 text-sm px-1" :style="{ color: L.inkSecondary }">
        Variant C threads the needle between A's neutrality and B's boldness — the accent strip is a tiny commitment, but each tile is still instantly color-coded. Works especially well when the page already has a lot of color (e.g., the leaderboard or calendar) and you want the actions to contribute accent identity without dominating.
      </p>
    </section>


    <!-- ══════════════════════════════════════════════════════════════
         CLAUDE'S PICK CALLOUT
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-8">
      <div class="rounded-2xl border p-6 space-y-3"
           :style="{ background: L.accents.lavender.soft, borderColor: L.accents.lavender.bold }">
        <div class="flex items-center gap-2">
          <SparklesOutlineIcon class="w-5 h-5" :style="{ color: L.accents.lavender.bold }" />
          <h2 class="text-[15px] font-semibold" :style="{ color: L.accents.lavender.bold }">Claude's pick — Variant C</h2>
        </div>
        <p class="text-[14px] leading-relaxed" :style="{ color: L.inkPrimary }">
          Variant C is the strongest fit for Kinhold's design language: the accent-bold icon + 3px bottom strip gives every tile a distinct color identity without flooding the surface — a calmer commitment than B's full gradient fill but far more alive than A's monochrome treatment.
          The hover state (accent-soft background floods in, border tightens to accent-bold) creates a satisfying "press" feel that rewards interaction without animation overhead.
          On dark the strips read as glowing accents against the near-black tile, which perfectly matches the established GradientCard and badge vocabulary already in the system.
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
            <strong :style="{ color: L.inkPrimary }">Variant A (flat outline, inkPrimary icon)</strong> — Use when the surrounding page is already high-chroma (photo header, GradientCard hero, colorful calendar) and the QuickActions row should feel calm and functional. Also suitable for Settings-style pages where you want zero accent competition. Monochrome tiles scan fastest when users already know what the icons mean.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Variant B (iridescent gradient, icon + label both accent-bold)</strong> — Use when QuickActions IS the hero — e.g., a minimal dashboard that opens to this row front-and-center. Each tile reads as a featured destination, not just a shortcut. Avoid using on pages that already have GradientCards; the two patterns would compete. Best with 4 tiles; 6-up gets tight but works at small text.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Variant C (outline + accent strip + accent icon, recommended)</strong> — Default choice for most Kinhold module landing pages. Accent strip provides color-coding without visual noise. inkPrimary label stays readable in all lighting contexts. The hover state adds delight without relying on JS animation. Works at 4-up and 6-up, and the strip is clearly visible even at mobile 2-up density.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Grid density</strong> — Prefer 4-up for dashboard and primary landing pages (breathing room per tile, easy tap targets). Use 6-up only in task-dense modules where the user is already oriented and wants speed over discoverability. Never go beyond 6 tiles — add a "More" overflow row instead.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Badge affordance</strong> — Use a count badge only when the number is actionable (items awaiting attention, not total counts). Badge sits top-right of the tile. Use the tile's accent-bold color as the badge background for Variant C; use white for Variant A.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Disabled tiles</strong> — Reserve for feature-gated actions (e.g., "Ask assistant" when no API key is configured). Use <code style="font-family: monospace; font-size: 12px;">opacity: 0.40</code> + <code style="font-family: monospace; font-size: 12px;">cursor: not-allowed</code>. Always keep the tile visible (no hiding) so users understand the feature exists but is unavailable.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Tap target minimum</strong> — Tiles must be at least 88×88px on mobile. The 2-up grid on a 320px container gives ~148px wide tiles, safely above the WCAG 44×44 touch target minimum.
          </li>
        </ul>
      </div>
    </section>

  </ComponentPage>
</template>

<style scoped>
/*
  ═══════════════════════════════════════════════════════════════
  VARIANT A — hover lift transition (light + dark)
  ═══════════════════════════════════════════════════════════════
*/
.qa-tile-a-lt,
.qa-tile-a-dk {
  transition:
    box-shadow 200ms cubic-bezier(0.16, 1, 0.3, 1),
    transform   200ms cubic-bezier(0.16, 1, 0.3, 1),
    border-color 150ms cubic-bezier(0.16, 1, 0.3, 1);
}

/*
  ═══════════════════════════════════════════════════════════════
  VARIANT B — hover lift transition (light + dark)
  Background/gradient managed via inline :style for reactivity.
  ═══════════════════════════════════════════════════════════════
*/
.qa-tile-b-lt,
.qa-tile-b-dk {
  transition:
    box-shadow 200ms cubic-bezier(0.16, 1, 0.3, 1),
    transform   200ms cubic-bezier(0.16, 1, 0.3, 1);
}

/*
  ═══════════════════════════════════════════════════════════════
  VARIANT C — hover lift + background flood transition
  ═══════════════════════════════════════════════════════════════
*/
.qa-tile-c-lt,
.qa-tile-c-dk {
  transition:
    box-shadow       200ms cubic-bezier(0.16, 1, 0.3, 1),
    transform        200ms cubic-bezier(0.16, 1, 0.3, 1),
    background-color 180ms cubic-bezier(0.16, 1, 0.3, 1),
    border-color     150ms cubic-bezier(0.16, 1, 0.3, 1);
}

/*
  ═══════════════════════════════════════════════════════════════
  REDUCED MOTION — instant state changes, no transforms
  ═══════════════════════════════════════════════════════════════
*/
@media (prefers-reduced-motion: reduce) {
  .qa-tile-a-lt,
  .qa-tile-a-dk,
  .qa-tile-b-lt,
  .qa-tile-b-dk,
  .qa-tile-c-lt,
  .qa-tile-c-dk {
    transition: none;
    transform: none !important;
  }
}
</style>
