<script setup>
import { ref, computed } from 'vue'
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import {
  CheckIcon,
  SparklesIcon,
  StarIcon,
  BookOpenIcon,
  HomeIcon,
  AcademicCapIcon,
  BriefcaseIcon,
  HeartIcon,
  ShoppingCartIcon,
  BeakerIcon,
  XMarkIcon,
} from '@heroicons/vue/24/outline'

// ── Palette tokens ────────────────────────────────────────────────────────────
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

// ── Category data — 8 to force overflow on narrow viewports ──────────────────
const CATEGORIES = [
  { key: 'family',  label: 'Family',   accent: 'lavender', icon: HeartIcon       },
  { key: 'school',  label: 'School',   accent: 'peach',    icon: AcademicCapIcon },
  { key: 'home',    label: 'Home',     accent: 'mint',     icon: HomeIcon        },
  { key: 'work',    label: 'Work',     accent: 'sun',      icon: BriefcaseIcon   },
  { key: 'reading', label: 'Reading',  accent: 'lavender', icon: BookOpenIcon    },
  { key: 'health',  label: 'Health',   accent: 'mint',     icon: BeakerIcon      },
  { key: 'grocery', label: 'Grocery',  accent: 'sun',      icon: ShoppingCartIcon},
  { key: 'goals',   label: 'Goals',    accent: 'peach',    icon: StarIcon        },
]

// ── Reactive active-selection per variant × light/dark ───────────────────────
const activeA_lt = ref(['family'])
const activeA_dk = ref(['family'])
const activeB_lt = ref(['family'])
const activeB_dk = ref(['family'])
const activeC_lt = ref(['family'])
const activeC_dk = ref(['family'])

function toggleChip(set, key) {
  const idx = set.value.indexOf(key)
  if (idx === -1) set.value.push(key)
  else set.value.splice(idx, 1)
}

// ── Variant C: "+N more" collapsed set ───────────────────────────────────────
// On mobile we show the first 4 chips + overflow chip. Desktop shows all.
const MOBILE_VISIBLE = 4
const C_overflowCount = computed(() => Math.max(0, CATEGORIES.length - MOBILE_VISIBLE))
const C_visibleCategories = computed(() => CATEGORIES.slice(0, MOBILE_VISIBLE))

// ── Chip style helpers (mirrors Tier 1.3 locked pattern) ─────────────────────
function chipStyle(isActive, accent, palette) {
  const P = palette
  if (isActive) {
    return {
      background: P.accents[accent].soft,
      color:      P.accents[accent].bold,
      border:     `1px solid ${P.accents[accent].bold}`,
    }
  }
  return {
    background: P.surfaceRaised,
    color:      P.inkSecondary,
    border:     `1px solid ${P.borderSubtle}`,
  }
}

function ghostChipStyle(palette) {
  return {
    background: 'transparent',
    color:      palette.inkTertiary,
    border:     `1px solid ${palette.borderSubtle}`,
  }
}

function overflowChipStyle(palette) {
  // The "+N more" chip uses a lavender accent to hint it's interactive
  return {
    background: palette.surfaceRaised,
    color:      palette.accents.lavender.bold,
    border:     `1px solid ${palette.accents.lavender.bold}`,
    fontWeight: '600',
  }
}
</script>

<template>
  <ComponentPage
    title="4.3 CategoryChipRow"
    description="Horizontal row of icon + label chips for category selection. Three layout strategies for overflow behavior — the chip style itself is locked from Tier 1.3."
    status="chosen"
  >

    <!-- ══════════════════════════════════════════════════════════════
         VARIANT A — flex-wrap: all chips visible, wraps on narrow
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="A" caption="Flex-wrap row — all chips visible, wraps to multiple lines on narrow viewports">
        <div class="w-full space-y-10">

          <!-- LIGHT PANEL -->
          <div class="rounded-2xl border p-6 space-y-8"
               :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>

            <!-- Wide example -->
            <div>
              <p class="text-xs mb-3" :style="{ color: L.inkTertiary }">Wide — all 8 categories on one line</p>
              <div class="flex flex-wrap gap-2">
                <button
                  v-for="cat in CATEGORIES" :key="cat.key"
                  type="button"
                  class="chip-base inline-flex items-center gap-1.5 rounded-full h-7 px-3 text-[12px] font-medium transition-all"
                  :style="chipStyle(activeA_lt.includes(cat.key), cat.accent, L)"
                  @click="toggleChip(activeA_lt, cat.key)"
                >
                  <CheckIcon v-if="activeA_lt.includes(cat.key)" class="w-3.5 h-3.5 flex-shrink-0" />
                  <component :is="cat.icon" v-else class="w-3.5 h-3.5 flex-shrink-0" />
                  {{ cat.label }}
                </button>
                <!-- Clear filters terminal chip -->
                <button
                  type="button"
                  class="chip-base inline-flex items-center gap-1 rounded-full h-7 px-3 text-[12px] font-medium transition-all"
                  :style="ghostChipStyle(L)"
                  @click="activeA_lt = []"
                >
                  <XMarkIcon class="w-3.5 h-3.5 flex-shrink-0" />
                  Clear
                </button>
              </div>
            </div>

            <!-- Narrow (320px) example — wraps to 2 lines -->
            <div>
              <p class="text-xs mb-3" :style="{ color: L.inkTertiary }">Narrow (320px) — wraps naturally across two lines</p>
              <div class="max-w-[320px] rounded-xl border p-3"
                   :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle }">
                <div class="flex flex-wrap gap-2">
                  <button
                    v-for="cat in CATEGORIES" :key="cat.key"
                    type="button"
                    class="chip-base inline-flex items-center gap-1.5 rounded-full h-7 px-3 text-[12px] font-medium transition-all"
                    :style="chipStyle(activeA_lt.includes(cat.key), cat.accent, L)"
                    @click="toggleChip(activeA_lt, cat.key)"
                  >
                    <CheckIcon v-if="activeA_lt.includes(cat.key)" class="w-3.5 h-3.5 flex-shrink-0" />
                    <component :is="cat.icon" v-else class="w-3.5 h-3.5 flex-shrink-0" />
                    {{ cat.label }}
                  </button>
                  <button
                    type="button"
                    class="chip-base inline-flex items-center gap-1 rounded-full h-7 px-3 text-[12px] font-medium"
                    :style="ghostChipStyle(L)"
                    @click="activeA_lt = []"
                  >
                    <XMarkIcon class="w-3.5 h-3.5 flex-shrink-0" />
                    Clear
                  </button>
                </div>
              </div>
            </div>
          </div><!-- /light A -->

          <!-- DARK PANEL -->
          <div class="rounded-2xl border p-6 space-y-8"
               :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

            <!-- Wide dark -->
            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">Wide — all 8 categories</p>
              <div class="flex flex-wrap gap-2">
                <button
                  v-for="cat in CATEGORIES" :key="cat.key"
                  type="button"
                  class="chip-base inline-flex items-center gap-1.5 rounded-full h-7 px-3 text-[12px] font-medium transition-all"
                  :style="chipStyle(activeA_dk.includes(cat.key), cat.accent, D)"
                  @click="toggleChip(activeA_dk, cat.key)"
                >
                  <CheckIcon v-if="activeA_dk.includes(cat.key)" class="w-3.5 h-3.5 flex-shrink-0" />
                  <component :is="cat.icon" v-else class="w-3.5 h-3.5 flex-shrink-0" />
                  {{ cat.label }}
                </button>
                <button
                  type="button"
                  class="chip-base inline-flex items-center gap-1 rounded-full h-7 px-3 text-[12px] font-medium"
                  :style="ghostChipStyle(D)"
                  @click="activeA_dk = []"
                >
                  <XMarkIcon class="w-3.5 h-3.5 flex-shrink-0" />
                  Clear
                </button>
              </div>
            </div>

            <!-- Narrow dark -->
            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">Narrow (320px) — wraps naturally</p>
              <div class="max-w-[320px] rounded-xl border p-3"
                   :style="{ background: D.surfaceRaised, borderColor: D.borderSubtle }">
                <div class="flex flex-wrap gap-2">
                  <button
                    v-for="cat in CATEGORIES" :key="cat.key"
                    type="button"
                    class="chip-base inline-flex items-center gap-1.5 rounded-full h-7 px-3 text-[12px] font-medium transition-all"
                    :style="chipStyle(activeA_dk.includes(cat.key), cat.accent, D)"
                    @click="toggleChip(activeA_dk, cat.key)"
                  >
                    <CheckIcon v-if="activeA_dk.includes(cat.key)" class="w-3.5 h-3.5 flex-shrink-0" />
                    <component :is="cat.icon" v-else class="w-3.5 h-3.5 flex-shrink-0" />
                    {{ cat.label }}
                  </button>
                  <button
                    type="button"
                    class="chip-base inline-flex items-center gap-1 rounded-full h-7 px-3 text-[12px] font-medium"
                    :style="ghostChipStyle(D)"
                    @click="activeA_dk = []"
                  >
                    <XMarkIcon class="w-3.5 h-3.5 flex-shrink-0" />
                    Clear
                  </button>
                </div>
              </div>
            </div>
          </div><!-- /dark A -->

        </div>
      </VariantFrame>
      <p class="mt-3 text-sm px-1" :style="{ color: L.inkSecondary }">
        Simplest option. No JS required — just CSS flexbox. Every chip is always reachable, but the row can grow to 2–3 lines on mobile with 8+ categories, making the page feel taller than necessary.
      </p>
    </section>

    <!-- ══════════════════════════════════════════════════════════════
         VARIANT B — overflow-x-auto scroll + fade mask
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="B" caption="Horizontal scroll + right-edge fade mask — single-line, no wrapping">
        <div class="w-full space-y-10">

          <!-- LIGHT PANEL -->
          <div class="rounded-2xl border p-6 space-y-8"
               :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>

            <!-- Wide — barely any overflow -->
            <div>
              <p class="text-xs mb-3" :style="{ color: L.inkTertiary }">Wide — single-line, scrollable (fade mask visible when content overflows)</p>
              <div class="scroll-fade-lt relative overflow-hidden">
                <div class="flex gap-2 overflow-x-auto scroll-container pb-1"
                     style="-webkit-overflow-scrolling: touch; scroll-snap-type: x mandatory;">
                  <button
                    v-for="cat in CATEGORIES" :key="cat.key"
                    type="button"
                    class="chip-base inline-flex items-center gap-1.5 rounded-full h-7 px-3 text-[12px] font-medium flex-shrink-0 transition-all"
                    style="scroll-snap-align: start;"
                    :style="chipStyle(activeB_lt.includes(cat.key), cat.accent, L)"
                    @click="toggleChip(activeB_lt, cat.key)"
                  >
                    <CheckIcon v-if="activeB_lt.includes(cat.key)" class="w-3.5 h-3.5 flex-shrink-0" />
                    <component :is="cat.icon" v-else class="w-3.5 h-3.5 flex-shrink-0" />
                    {{ cat.label }}
                  </button>
                  <button
                    type="button"
                    class="chip-base inline-flex items-center gap-1 rounded-full h-7 px-3 text-[12px] font-medium flex-shrink-0"
                    :style="ghostChipStyle(L)"
                    @click="activeB_lt = []"
                  >
                    <XMarkIcon class="w-3.5 h-3.5 flex-shrink-0" />
                    Clear
                  </button>
                </div>
              </div>
            </div>

            <!-- Narrow — actual horizontal scroll forced by 320px constraint -->
            <div>
              <p class="text-xs mb-3" :style="{ color: L.inkTertiary }">Narrow (320px) — scroll right to reveal hidden chips, fade signals more content</p>
              <div class="max-w-[320px] rounded-xl border p-3"
                   :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle }">
                <div class="scroll-fade-lt relative overflow-hidden">
                  <div class="flex gap-2 overflow-x-auto scroll-container pb-0.5"
                       style="-webkit-overflow-scrolling: touch; scroll-snap-type: x mandatory;">
                    <button
                      v-for="cat in CATEGORIES" :key="cat.key"
                      type="button"
                      class="chip-base inline-flex items-center gap-1.5 rounded-full h-7 px-3 text-[12px] font-medium flex-shrink-0 transition-all"
                      style="scroll-snap-align: start;"
                      :style="chipStyle(activeB_lt.includes(cat.key), cat.accent, L)"
                      @click="toggleChip(activeB_lt, cat.key)"
                    >
                      <CheckIcon v-if="activeB_lt.includes(cat.key)" class="w-3.5 h-3.5 flex-shrink-0" />
                      <component :is="cat.icon" v-else class="w-3.5 h-3.5 flex-shrink-0" />
                      {{ cat.label }}
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div><!-- /light B -->

          <!-- DARK PANEL -->
          <div class="rounded-2xl border p-6 space-y-8"
               :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

            <!-- Wide dark -->
            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">Wide — single-line scroll</p>
              <div class="scroll-fade-dk relative overflow-hidden">
                <div class="flex gap-2 overflow-x-auto scroll-container pb-1"
                     style="-webkit-overflow-scrolling: touch; scroll-snap-type: x mandatory;">
                  <button
                    v-for="cat in CATEGORIES" :key="cat.key"
                    type="button"
                    class="chip-base inline-flex items-center gap-1.5 rounded-full h-7 px-3 text-[12px] font-medium flex-shrink-0 transition-all"
                    style="scroll-snap-align: start;"
                    :style="chipStyle(activeB_dk.includes(cat.key), cat.accent, D)"
                    @click="toggleChip(activeB_dk, cat.key)"
                  >
                    <CheckIcon v-if="activeB_dk.includes(cat.key)" class="w-3.5 h-3.5 flex-shrink-0" />
                    <component :is="cat.icon" v-else class="w-3.5 h-3.5 flex-shrink-0" />
                    {{ cat.label }}
                  </button>
                  <button
                    type="button"
                    class="chip-base inline-flex items-center gap-1 rounded-full h-7 px-3 text-[12px] font-medium flex-shrink-0"
                    :style="ghostChipStyle(D)"
                    @click="activeB_dk = []"
                  >
                    <XMarkIcon class="w-3.5 h-3.5 flex-shrink-0" />
                    Clear
                  </button>
                </div>
              </div>
            </div>

            <!-- Narrow dark -->
            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">Narrow (320px) — horizontal scroll in dark</p>
              <div class="max-w-[320px] rounded-xl border p-3"
                   :style="{ background: D.surfaceRaised, borderColor: D.borderSubtle }">
                <div class="scroll-fade-dk relative overflow-hidden">
                  <div class="flex gap-2 overflow-x-auto scroll-container pb-0.5"
                       style="-webkit-overflow-scrolling: touch; scroll-snap-type: x mandatory;">
                    <button
                      v-for="cat in CATEGORIES" :key="cat.key"
                      type="button"
                      class="chip-base inline-flex items-center gap-1.5 rounded-full h-7 px-3 text-[12px] font-medium flex-shrink-0 transition-all"
                      style="scroll-snap-align: start;"
                      :style="chipStyle(activeB_dk.includes(cat.key), cat.accent, D)"
                      @click="toggleChip(activeB_dk, cat.key)"
                    >
                      <CheckIcon v-if="activeB_dk.includes(cat.key)" class="w-3.5 h-3.5 flex-shrink-0" />
                      <component :is="cat.icon" v-else class="w-3.5 h-3.5 flex-shrink-0" />
                      {{ cat.label }}
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div><!-- /dark B -->

        </div>
      </VariantFrame>
      <p class="mt-3 text-sm px-1" :style="{ color: L.inkSecondary }">
        Keeps the page height compact — no matter how many categories, it's always one chip-row tall. The fade mask signals more content without adding an explicit scroll indicator. Snap-scroll makes flicking through feel intentional on touch.
      </p>
    </section>

    <!-- ══════════════════════════════════════════════════════════════
         VARIANT C — Hybrid: 4 visible on mobile + "+N more" overflow chip
                     Full wrap on desktop
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="C" caption="Hybrid — 4 chips + '+N more' affordance on mobile; full wrap on desktop">
        <div class="w-full space-y-10">

          <!-- LIGHT PANEL -->
          <div class="rounded-2xl border p-6 space-y-8"
               :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>

            <!-- Desktop: full wrap (md+) -->
            <div>
              <p class="text-xs mb-3" :style="{ color: L.inkTertiary }">Desktop — full wrap, all chips accessible</p>
              <div class="flex flex-wrap gap-2">
                <button
                  v-for="cat in CATEGORIES" :key="cat.key"
                  type="button"
                  class="chip-base inline-flex items-center gap-1.5 rounded-full h-7 px-3 text-[12px] font-medium transition-all"
                  :style="chipStyle(activeC_lt.includes(cat.key), cat.accent, L)"
                  @click="toggleChip(activeC_lt, cat.key)"
                >
                  <CheckIcon v-if="activeC_lt.includes(cat.key)" class="w-3.5 h-3.5 flex-shrink-0" />
                  <component :is="cat.icon" v-else class="w-3.5 h-3.5 flex-shrink-0" />
                  {{ cat.label }}
                </button>
                <button
                  type="button"
                  class="chip-base inline-flex items-center gap-1 rounded-full h-7 px-3 text-[12px] font-medium"
                  :style="ghostChipStyle(L)"
                  @click="activeC_lt = []"
                >
                  <XMarkIcon class="w-3.5 h-3.5 flex-shrink-0" />
                  Clear
                </button>
              </div>
            </div>

            <!-- Mobile (320px): only 4 chips + overflow affordance -->
            <div>
              <p class="text-xs mb-3" :style="{ color: L.inkTertiary }">Mobile (320px) — 4 chips visible, "+N more" opens a bottom sheet (wired in production)</p>
              <div class="max-w-[320px] rounded-xl border p-3"
                   :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle }">
                <div class="flex gap-2 flex-nowrap overflow-hidden">
                  <!-- 4 primary chips -->
                  <button
                    v-for="cat in C_visibleCategories" :key="cat.key"
                    type="button"
                    class="chip-base inline-flex items-center gap-1.5 rounded-full h-7 px-3 text-[12px] font-medium flex-shrink-0 transition-all"
                    :style="chipStyle(activeC_lt.includes(cat.key), cat.accent, L)"
                    @click="toggleChip(activeC_lt, cat.key)"
                  >
                    <CheckIcon v-if="activeC_lt.includes(cat.key)" class="w-3.5 h-3.5 flex-shrink-0" />
                    <component :is="cat.icon" v-else class="w-3.5 h-3.5 flex-shrink-0" />
                    {{ cat.label }}
                  </button>
                  <!-- Overflow affordance -->
                  <button
                    v-if="C_overflowCount > 0"
                    type="button"
                    class="chip-overflow inline-flex items-center gap-1 rounded-full h-7 px-3 text-[12px] flex-shrink-0"
                    :style="overflowChipStyle(L)"
                    title="Show all categories"
                  >
                    +{{ C_overflowCount }} more
                  </button>
                </div>
              </div>
            </div>

            <!-- Mobile: with an active selection among the hidden chips -->
            <div>
              <p class="text-xs mb-3" :style="{ color: L.inkTertiary }">Mobile — active selection includes a hidden chip (overflow chip hints at it)</p>
              <div class="max-w-[320px] rounded-xl border p-3"
                   :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle }">
                <div class="flex gap-2 flex-nowrap overflow-hidden">
                  <button
                    v-for="cat in C_visibleCategories" :key="cat.key"
                    type="button"
                    class="chip-base inline-flex items-center gap-1.5 rounded-full h-7 px-3 text-[12px] font-medium flex-shrink-0"
                    :style="chipStyle(activeC_lt.includes(cat.key), cat.accent, L)"
                    @click="toggleChip(activeC_lt, cat.key)"
                  >
                    <CheckIcon v-if="activeC_lt.includes(cat.key)" class="w-3.5 h-3.5 flex-shrink-0" />
                    <component :is="cat.icon" v-else class="w-3.5 h-3.5 flex-shrink-0" />
                    {{ cat.label }}
                  </button>
                  <!-- Overflow chip — lavender tinted to signal a hidden active filter -->
                  <button
                    type="button"
                    class="chip-overflow inline-flex items-center gap-1 rounded-full h-7 px-3 text-[12px] flex-shrink-0"
                    :style="{
                      background: L.accents.lavender.soft,
                      color: L.accents.lavender.bold,
                      border: `1px solid ${L.accents.lavender.bold}`,
                      fontWeight: '600',
                    }"
                    title="Show all categories"
                  >
                    +{{ C_overflowCount }}
                  </button>
                </div>
              </div>
              <p class="mt-1.5 text-xs" :style="{ color: L.inkTertiary }">When a hidden chip is active, the overflow chip adopts the lavender active tint to communicate "there's an active filter you can't see."</p>
            </div>
          </div><!-- /light C -->

          <!-- DARK PANEL -->
          <div class="rounded-2xl border p-6 space-y-8"
               :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

            <!-- Desktop dark -->
            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">Desktop — full wrap</p>
              <div class="flex flex-wrap gap-2">
                <button
                  v-for="cat in CATEGORIES" :key="cat.key"
                  type="button"
                  class="chip-base inline-flex items-center gap-1.5 rounded-full h-7 px-3 text-[12px] font-medium transition-all"
                  :style="chipStyle(activeC_dk.includes(cat.key), cat.accent, D)"
                  @click="toggleChip(activeC_dk, cat.key)"
                >
                  <CheckIcon v-if="activeC_dk.includes(cat.key)" class="w-3.5 h-3.5 flex-shrink-0" />
                  <component :is="cat.icon" v-else class="w-3.5 h-3.5 flex-shrink-0" />
                  {{ cat.label }}
                </button>
                <button
                  type="button"
                  class="chip-base inline-flex items-center gap-1 rounded-full h-7 px-3 text-[12px] font-medium"
                  :style="ghostChipStyle(D)"
                  @click="activeC_dk = []"
                >
                  <XMarkIcon class="w-3.5 h-3.5 flex-shrink-0" />
                  Clear
                </button>
              </div>
            </div>

            <!-- Mobile dark -->
            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">Mobile (320px) — 4 chips + overflow affordance</p>
              <div class="max-w-[320px] rounded-xl border p-3"
                   :style="{ background: D.surfaceRaised, borderColor: D.borderSubtle }">
                <div class="flex gap-2 flex-nowrap overflow-hidden">
                  <button
                    v-for="cat in C_visibleCategories" :key="cat.key"
                    type="button"
                    class="chip-base inline-flex items-center gap-1.5 rounded-full h-7 px-3 text-[12px] font-medium flex-shrink-0 transition-all"
                    :style="chipStyle(activeC_dk.includes(cat.key), cat.accent, D)"
                    @click="toggleChip(activeC_dk, cat.key)"
                  >
                    <CheckIcon v-if="activeC_dk.includes(cat.key)" class="w-3.5 h-3.5 flex-shrink-0" />
                    <component :is="cat.icon" v-else class="w-3.5 h-3.5 flex-shrink-0" />
                    {{ cat.label }}
                  </button>
                  <button
                    v-if="C_overflowCount > 0"
                    type="button"
                    class="chip-overflow inline-flex items-center gap-1 rounded-full h-7 px-3 text-[12px] flex-shrink-0"
                    :style="overflowChipStyle(D)"
                    title="Show all categories"
                  >
                    +{{ C_overflowCount }} more
                  </button>
                </div>
              </div>
            </div>

            <!-- Mobile dark: overflow chip signalling hidden active filter -->
            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">Mobile — overflow chip with hidden active filter signal</p>
              <div class="max-w-[320px] rounded-xl border p-3"
                   :style="{ background: D.surfaceRaised, borderColor: D.borderSubtle }">
                <div class="flex gap-2 flex-nowrap overflow-hidden">
                  <button
                    v-for="cat in C_visibleCategories" :key="cat.key"
                    type="button"
                    class="chip-base inline-flex items-center gap-1.5 rounded-full h-7 px-3 text-[12px] font-medium flex-shrink-0"
                    :style="chipStyle(activeC_dk.includes(cat.key), cat.accent, D)"
                    @click="toggleChip(activeC_dk, cat.key)"
                  >
                    <CheckIcon v-if="activeC_dk.includes(cat.key)" class="w-3.5 h-3.5 flex-shrink-0" />
                    <component :is="cat.icon" v-else class="w-3.5 h-3.5 flex-shrink-0" />
                    {{ cat.label }}
                  </button>
                  <button
                    type="button"
                    class="chip-overflow inline-flex items-center gap-1 rounded-full h-7 px-3 text-[12px] flex-shrink-0"
                    :style="{
                      background: D.accents.lavender.soft,
                      color: D.accents.lavender.bold,
                      border: `1px solid ${D.accents.lavender.bold}`,
                      fontWeight: '600',
                    }"
                    title="Show all categories"
                  >
                    +{{ C_overflowCount }}
                  </button>
                </div>
              </div>
            </div>
          </div><!-- /dark C -->

        </div>
      </VariantFrame>
      <p class="mt-3 text-sm px-1" :style="{ color: L.inkSecondary }">
        Most nuanced option — respects the vertical budget on mobile while giving desktop users the full set. The "+N more" chip doubles as a status indicator: when it adopts an active tint, the user knows something is filtered that they can't currently see, which is a strong affordance for the bottom-sheet reveal.
      </p>
    </section>

    <!-- ══════════════════════════════════════════════════════════════
         CLAUDE'S PICK
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-8">
      <div class="rounded-2xl border p-6 space-y-3"
           :style="{ background: L.accents.lavender.soft, borderColor: L.accents.lavender.bold }">
        <div class="flex items-center gap-2">
          <SparklesIcon class="w-5 h-5" :style="{ color: L.accents.lavender.bold }" />
          <h2 class="text-[15px] font-semibold" :style="{ color: L.accents.lavender.bold }">Claude's pick — Variant B</h2>
        </div>
        <p class="text-[14px] leading-relaxed" :style="{ color: L.inkPrimary }">
          Variant B (horizontal scroll + fade mask) is the right default for Kinhold's mobile-first context. It keeps the chip row to a single predictable line at every viewport width — no wrapping surprises, no vertical budget cost — while the gradient fade communicates "swipe for more" in one silent signal that works without labels or arrows. The snap-scroll makes touch flicking feel deliberate rather than sloppy, and the clear ghost chip anchors the end of the list without disrupting the linear flow. Variant C is better reserved for contexts where the "+N more" bottom sheet is actually wired up — it would feel broken without the modal layer behind it.
        </p>
      </div>
    </section>

    <!-- ══════════════════════════════════════════════════════════════
         USAGE GUIDE
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-8">
      <div class="rounded-2xl border p-6 space-y-4"
           :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle }">
        <h2 class="text-[17px] font-semibold" :style="{ color: L.inkPrimary }">When to use which</h2>
        <ul class="space-y-4 text-[14px]" :style="{ color: L.inkSecondary }">
          <li>
            <strong :style="{ color: L.inkPrimary }">Variant A — Flex-wrap:</strong>
            Best for small, bounded category sets (4–6 chips max) where you know every chip will fit on two lines even at 320px. Good for vault entry type selection, task priority pickers, or any context where the category set is fixed and small. Avoid with 8+ chips — the wrapping creates unpredictable height.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Variant B — Horizontal scroll:</strong>
            The default for feature filter rows — recipe categories, meal plan cuisine filter, store "browse by category", workout category selector. Works with any number of chips without affecting page height. Requires the fade mask and snap-scroll to feel intentional rather than accidental. Ideal when users typically want to scan-and-tap one category at a time.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Variant C — Hybrid with "+N more":</strong>
            Use only when a bottom sheet or modal is wired to back it up. Best for search/filter pages where users may want to multi-select across a large category set (15+) — the overflow chip brings up a full-screen picker. On desktop, falls back gracefully to a full-wrap row. Do not ship the overflow chip as a static affordance with no interaction behind it.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Clear filters chip (ghost variant):</strong>
            Always position at the end of the row, never the beginning. Use the ghost style (transparent bg, subtle border, tertiary text) so it reads as a secondary action and does not compete with the category chips. Only show it when at least one filter is active.
          </li>
        </ul>
      </div>
    </section>

  </ComponentPage>
</template>

<style scoped>
/* ── Base chip transitions ──────────────────────────────────────────────────── */
.chip-base {
  transition: background-color 150ms ease, border-color 150ms ease, color 150ms ease;
  cursor: pointer;
}
.chip-base:hover {
  filter: brightness(0.96);
}
.chip-base:active {
  transform: scale(0.97);
}

/* ── Overflow chip ──────────────────────────────────────────────────────────── */
.chip-overflow {
  transition: background-color 150ms ease, border-color 150ms ease;
  cursor: pointer;
  letter-spacing: -0.01em;
}
.chip-overflow:hover {
  filter: brightness(0.93);
}
.chip-overflow:active {
  transform: scale(0.97);
}

/* ── Scrollable container — hide scrollbar but keep it functional ───────────── */
.scroll-container {
  scrollbar-width: none;      /* Firefox */
  -ms-overflow-style: none;   /* IE/Edge */
}
.scroll-container::-webkit-scrollbar {
  display: none;              /* Chrome/Safari */
}

/* ── Light fade mask — soft right-edge gradient ────────────────────────────── */
.scroll-fade-lt .scroll-container {
  /* mask-image fades the scroll container into the panel bg */
  -webkit-mask-image: linear-gradient(to right, black 85%, transparent 100%);
          mask-image: linear-gradient(to right, black 85%, transparent 100%);
}

/* ── Dark fade mask — same gradient, different terminal color ───────────────── */
.scroll-fade-dk .scroll-container {
  -webkit-mask-image: linear-gradient(to right, black 85%, transparent 100%);
          mask-image: linear-gradient(to right, black 85%, transparent 100%);
}
</style>
