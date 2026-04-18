<script setup>
import { ref } from 'vue'
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import {
  XMarkIcon,
  CheckIcon,
  StarIcon,
  BellIcon,
  ExclamationTriangleIcon,
  ClockIcon,
  PauseIcon,
  InformationCircleIcon,
} from '@heroicons/vue/24/outline'

// ── Dark-mode hex constants (from tokens.css html.dark section) ─────────────
// Dark panel uses hardcoded hex so it always renders dark regardless of root theme.
const D = {
  surfaceApp:    '#141311',
  surfaceRaised: '#1C1B19',
  surfaceSunken: '#161513',
  inkPrimary:    '#F0EDE9',
  inkSecondary:  '#A09C97',
  inkTertiary:   '#6E6B67',
  borderSubtle:  '#2C2A27',

  accents: {
    lavender: { soft: '#302A48', bold: '#B6A8E6' },
    peach:    { soft: '#3E241A', bold: '#F0A882' },
    mint:     { soft: '#18342A', bold: '#7CD6AE' },
    sun:      { soft: '#342C0A', bold: '#E6C452' },
  },

  status: {
    success: '#6CC498',
    pending: '#78A4DC',
    paused:  '#DCA848',
    failed:  '#E67070',
    info:    '#B6A8E6',
    warning: '#E6C452',
  },
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

  accents: {
    lavender: { soft: '#EAE6F8', bold: '#6856B2' },
    peach:    { soft: '#FCE9E0', bold: '#BA562E' },
    mint:     { soft: '#D5F2E8', bold: '#2E8A62' },
    sun:      { soft: '#FCF3D2', bold: '#A2780C' },
  },

  status: {
    success: '#4D8C6A',
    pending: '#486E9C',
    paused:  '#BE8230',
    failed:  '#BA4A4A',
    info:    '#6856B2',
    warning: '#C48C24',
  },
}

// ── Category data ────────────────────────────────────────────────────────────
const categoryAccents = [
  { key: 'lavender', label: 'Family',  icon: StarIcon },
  { key: 'peach',    label: 'School',  icon: BellIcon },
  { key: 'mint',     label: 'Kids',    icon: CheckIcon },
  { key: 'sun',      label: 'House',   icon: StarIcon },
]

// ── Status data ──────────────────────────────────────────────────────────────
const statusChips = [
  { key: 'success', label: 'Completed',  icon: CheckIcon },
  { key: 'pending', label: 'Waiting',    icon: ClockIcon },
  { key: 'paused',  label: 'Snoozed',    icon: PauseIcon },
  { key: 'failed',  label: 'Error',      icon: ExclamationTriangleIcon },
  { key: 'info',    label: 'Info',       icon: InformationCircleIcon },
  { key: 'warning', label: 'Due soon',   icon: ExclamationTriangleIcon },
]

// ── Interactive toggle state ─────────────────────────────────────────────────
// Variant A
const aToggleLightOn  = ref(false)
const aToggleDarkOn   = ref(false)
// Variant B
const bToggleLightOn  = ref(false)
const bToggleDarkOn   = ref(false)
// Filter chip groups — track which are "on"
const aFilterLight  = ref(['lavender'])
const aFilterDark   = ref(['lavender'])
const bFilterLight  = ref(['lavender'])
const bFilterDark   = ref(['lavender'])

function toggleFilter(set, key) {
  const idx = set.value.indexOf(key)
  if (idx === -1) set.value.push(key)
  else set.value.splice(idx, 1)
}
</script>

<template>
  <ComponentPage
    title="1.3 Chip · Tag · Status pill"
    description="Outlined + soft-tint chip for categories, tags, filters, and feature badges. Paired below with a tiny inline status indicator (dot + label) for dense contexts where a full chip would be too much chrome."
    status="chosen"
  >

    <!-- ══════════════════════════════════════════════════════════════
         CHIP — Outlined + soft bg tint (the chosen chip pattern)
         Structured, filter-chip-first. Border is the primary signal.
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="Chip" caption="Outlined + soft tint — the primary chip treatment for categories, tags, and filters">
        <div class="w-full space-y-10">

          <!-- ── LIGHT PANEL B ─────────────────────────────────────── -->
          <div class="rounded-2xl border p-6 space-y-8" style="background:#FAF8F5; border-color:#E8E4DF">
            <p class="text-xs font-semibold uppercase tracking-widest" style="color:#9C9895">Light mode</p>

            <!-- Row 1: Category accents -->
            <div>
              <p class="text-xs mb-3" style="color:#9C9895">Category accents — 1px border + soft bg tint</p>
              <div class="flex flex-wrap gap-2">
                <span
                  v-for="cat in categoryAccents" :key="cat.key"
                  class="chip-b-lt inline-flex items-center gap-1 rounded-full h-7 px-3 text-[12px] font-medium"
                  :style="{
                    background: L.accents[cat.key].soft + 'BF',
                    color: L.accents[cat.key].bold,
                    border: `1px solid ${L.accents[cat.key].bold}`,
                  }"
                >{{ cat.label }}</span>
              </div>
            </div>

            <!-- Row 2: Status -->
            <div>
              <p class="text-xs mb-3" style="color:#9C9895">Status</p>
              <div class="flex flex-wrap gap-2">
                <span
                  v-for="s in statusChips" :key="s.key"
                  class="inline-flex items-center gap-1.5 rounded-full h-7 px-3 text-[12px] font-medium"
                  :style="{
                    background: L.surfaceRaised,
                    color: L.status[s.key],
                    border: `1px solid ${L.status[s.key]}`,
                  }"
                >
                  <component :is="s.icon" class="w-3.5 h-3.5 flex-shrink-0" />
                  {{ s.label }}
                </span>
              </div>
            </div>

            <!-- Row 3: Sizes -->
            <div>
              <p class="text-xs mb-3" style="color:#9C9895">Sizes</p>
              <div class="flex flex-wrap items-center gap-3">
                <span class="chip-b-lt inline-flex items-center rounded-full h-6 px-2.5 text-[11px] font-medium"
                  style="background: rgba(234,230,248,0.75); color:#6856B2; border: 1px solid #6856B2">sm — 24px</span>
                <span class="chip-b-lt inline-flex items-center rounded-full h-7 px-3 text-[12px] font-medium"
                  style="background: rgba(234,230,248,0.75); color:#6856B2; border: 1px solid #6856B2">md — 28px</span>
              </div>
            </div>

            <!-- Row 4: Affordances -->
            <div>
              <p class="text-xs mb-3" style="color:#9C9895">Affordances</p>
              <div class="flex flex-wrap gap-3 items-center">
                <!-- Static tag -->
                <span class="chip-b-lt inline-flex items-center rounded-full h-7 px-3 text-[12px] font-medium"
                  style="background: rgba(234,230,248,0.75); color:#6856B2; border: 1px solid #6856B2">Static tag</span>

                <!-- Removable -->
                <span class="chip-b-lt inline-flex items-center gap-1 rounded-full h-7 pl-3 pr-1 text-[12px] font-medium"
                  style="background: rgba(252,233,224,0.75); color:#BA562E; border: 1px solid #BA562E">
                  Removable
                  <button type="button" aria-label="Remove"
                    class="chip-b-remove-lt ml-0.5 w-5 h-5 flex items-center justify-center rounded-full flex-shrink-0"
                    style="color:#BA562E">
                    <XMarkIcon class="w-3 h-3" />
                  </button>
                </span>

                <!-- Icon-left -->
                <span class="chip-b-lt inline-flex items-center gap-1.5 rounded-full h-7 px-3 text-[12px] font-medium"
                  style="background: rgba(213,242,232,0.75); color:#2E8A62; border: 1px solid #2E8A62">
                  <CheckIcon class="w-3.5 h-3.5 flex-shrink-0" />
                  Completed
                </span>

                <!-- Toggle off/on side by side -->
                <button type="button" role="switch" :aria-pressed="bToggleLightOn"
                  @click="bToggleLightOn = !bToggleLightOn"
                  class="chip-b-lt inline-flex items-center gap-1.5 rounded-full h-7 px-3 text-[12px] font-medium transition-all"
                  :style="bToggleLightOn
                    ? { background: L.accents.sun.soft, color: L.accents.sun.bold, border: `1px solid ${L.accents.sun.bold}` }
                    : { background: L.surfaceRaised, color: L.inkSecondary, border: `1px solid ${L.borderSubtle}` }"
                >
                  <CheckIcon v-if="bToggleLightOn" class="w-3.5 h-3.5 flex-shrink-0" />
                  {{ bToggleLightOn ? 'On' : 'Off' }}
                </button>
              </div>
            </div>

            <!-- Row 5: Filter cluster (selected vs unselected) -->
            <div>
              <p class="text-xs mb-3" style="color:#9C9895">Filter cluster — border differentiates off/on clearly</p>
              <div class="flex flex-wrap gap-2">
                <button
                  v-for="cat in categoryAccents" :key="cat.key"
                  type="button" role="switch" :aria-pressed="bFilterLight.includes(cat.key)"
                  @click="toggleFilter(bFilterLight, cat.key)"
                  class="chip-b-lt inline-flex items-center gap-1.5 rounded-full h-7 px-3 text-[12px] font-medium transition-all"
                  :style="bFilterLight.includes(cat.key)
                    ? { background: L.accents[cat.key].soft, color: L.accents[cat.key].bold, border: `1px solid ${L.accents[cat.key].bold}` }
                    : { background: L.surfaceRaised, color: L.inkSecondary, border: `1px solid ${L.borderSubtle}` }"
                >
                  <CheckIcon v-if="bFilterLight.includes(cat.key)" class="w-3.5 h-3.5 flex-shrink-0" />
                  {{ cat.label }}
                </button>
              </div>

              <!-- Disabled -->
              <div class="mt-3 flex flex-wrap gap-2">
                <span class="inline-flex items-center rounded-full h-7 px-3 text-[12px] font-medium opacity-40 cursor-not-allowed"
                  style="background: rgba(234,230,248,0.75); color:#6856B2; border: 1px solid #6856B2">Disabled</span>
                <span class="inline-flex items-center rounded-full h-7 px-3 text-[12px] font-medium opacity-40 cursor-not-allowed"
                  style="background: rgba(252,233,224,0.75); color:#BA562E; border: 1px solid #BA562E">Disabled</span>
              </div>
            </div>

            <!-- Cluster -->
            <div>
              <p class="text-xs mb-3" style="color:#9C9895">Cluster — tag picker row</p>
              <div class="flex flex-wrap gap-2">
                <span class="chip-b-lt inline-flex items-center rounded-full h-7 px-3 text-[12px] font-medium" style="background:rgba(234,230,248,0.75); color:#6856B2; border:1px solid #6856B2">Family</span>
                <span class="chip-b-lt inline-flex items-center rounded-full h-7 px-3 text-[12px] font-medium" style="background:rgba(252,233,224,0.75); color:#BA562E; border:1px solid #BA562E">School</span>
                <span class="chip-b-lt inline-flex items-center rounded-full h-7 px-3 text-[12px] font-medium" style="background:rgba(213,242,232,0.75); color:#2E8A62; border:1px solid #2E8A62">Kids</span>
                <span class="chip-b-lt inline-flex items-center rounded-full h-7 px-3 text-[12px] font-medium" style="background:rgba(252,243,210,0.75); color:#A2780C; border:1px solid #A2780C">House</span>
                <span class="chip-b-lt inline-flex items-center gap-1 rounded-full h-7 px-3 text-[12px] font-medium" style="background:rgba(234,230,248,0.75); color:#6856B2; border:1px solid #6856B2">
                  <CheckIcon class="w-3.5 h-3.5" />Groceries
                </span>
              </div>
            </div>

          </div><!-- /light panel B -->

          <!-- ── DARK PANEL B ─────────────────────────────────────── -->
          <div class="rounded-2xl border p-6 space-y-8" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

            <!-- Row 1: Category accents dark -->
            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">Category accents</p>
              <div class="flex flex-wrap gap-2">
                <span
                  v-for="cat in categoryAccents" :key="cat.key"
                  class="chip-b-dk inline-flex items-center gap-1 rounded-full h-7 px-3 text-[12px] font-medium"
                  :style="{
                    background: D.accents[cat.key].soft,
                    color: D.accents[cat.key].bold,
                    border: `1px solid ${D.accents[cat.key].bold}`,
                  }"
                >{{ cat.label }}</span>
              </div>
            </div>

            <!-- Row 2: Status dark -->
            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">Status</p>
              <div class="flex flex-wrap gap-2">
                <span
                  v-for="s in statusChips" :key="s.key"
                  class="inline-flex items-center gap-1.5 rounded-full h-7 px-3 text-[12px] font-medium"
                  :style="{
                    background: D.surfaceRaised,
                    color: D.status[s.key],
                    border: `1px solid ${D.status[s.key]}`,
                  }"
                >
                  <component :is="s.icon" class="w-3.5 h-3.5 flex-shrink-0" />
                  {{ s.label }}
                </span>
              </div>
            </div>

            <!-- Row 3: Sizes dark -->
            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">Sizes</p>
              <div class="flex flex-wrap items-center gap-3">
                <span class="chip-b-dk inline-flex items-center rounded-full h-6 px-2.5 text-[11px] font-medium"
                  :style="{ background: D.accents.lavender.soft, color: D.accents.lavender.bold, border: `1px solid ${D.accents.lavender.bold}` }">sm — 24px</span>
                <span class="chip-b-dk inline-flex items-center rounded-full h-7 px-3 text-[12px] font-medium"
                  :style="{ background: D.accents.lavender.soft, color: D.accents.lavender.bold, border: `1px solid ${D.accents.lavender.bold}` }">md — 28px</span>
              </div>
            </div>

            <!-- Row 4: Affordances dark -->
            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">Affordances</p>
              <div class="flex flex-wrap gap-3 items-center">
                <!-- Static tag -->
                <span class="chip-b-dk inline-flex items-center rounded-full h-7 px-3 text-[12px] font-medium"
                  :style="{ background: D.accents.lavender.soft, color: D.accents.lavender.bold, border: `1px solid ${D.accents.lavender.bold}` }">Static tag</span>

                <!-- Removable -->
                <span class="chip-b-dk inline-flex items-center gap-1 rounded-full h-7 pl-3 pr-1 text-[12px] font-medium"
                  :style="{ background: D.accents.peach.soft, color: D.accents.peach.bold, border: `1px solid ${D.accents.peach.bold}` }">
                  Removable
                  <button type="button" aria-label="Remove"
                    class="chip-b-remove-dk ml-0.5 w-5 h-5 flex items-center justify-center rounded-full flex-shrink-0"
                    :style="{ color: D.accents.peach.bold }">
                    <XMarkIcon class="w-3 h-3" />
                  </button>
                </span>

                <!-- Icon-left -->
                <span class="chip-b-dk inline-flex items-center gap-1.5 rounded-full h-7 px-3 text-[12px] font-medium"
                  :style="{ background: D.accents.mint.soft, color: D.accents.mint.bold, border: `1px solid ${D.accents.mint.bold}` }">
                  <CheckIcon class="w-3.5 h-3.5 flex-shrink-0" />
                  Completed
                </span>

                <!-- Toggle dark -->
                <button type="button" role="switch" :aria-pressed="bToggleDarkOn"
                  @click="bToggleDarkOn = !bToggleDarkOn"
                  class="chip-b-dk inline-flex items-center gap-1.5 rounded-full h-7 px-3 text-[12px] font-medium transition-all"
                  :style="bToggleDarkOn
                    ? { background: D.accents.sun.soft, color: D.accents.sun.bold, border: `1px solid ${D.accents.sun.bold}` }
                    : { background: D.surfaceSunken, color: D.inkSecondary, border: `1px solid ${D.borderSubtle}` }"
                >
                  <CheckIcon v-if="bToggleDarkOn" class="w-3.5 h-3.5 flex-shrink-0" />
                  {{ bToggleDarkOn ? 'On' : 'Off' }}
                </button>
              </div>
            </div>

            <!-- Row 5: Filter cluster dark -->
            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">Filter cluster</p>
              <div class="flex flex-wrap gap-2">
                <button
                  v-for="cat in categoryAccents" :key="cat.key"
                  type="button" role="switch" :aria-pressed="bFilterDark.includes(cat.key)"
                  @click="toggleFilter(bFilterDark, cat.key)"
                  class="chip-b-dk inline-flex items-center gap-1.5 rounded-full h-7 px-3 text-[12px] font-medium transition-all"
                  :style="bFilterDark.includes(cat.key)
                    ? { background: D.accents[cat.key].soft, color: D.accents[cat.key].bold, border: `1px solid ${D.accents[cat.key].bold}` }
                    : { background: D.surfaceSunken, color: D.inkSecondary, border: `1px solid ${D.borderSubtle}` }"
                >
                  <CheckIcon v-if="bFilterDark.includes(cat.key)" class="w-3.5 h-3.5 flex-shrink-0" />
                  {{ cat.label }}
                </button>
              </div>

              <!-- Disabled dark -->
              <div class="mt-3 flex flex-wrap gap-2">
                <span class="inline-flex items-center rounded-full h-7 px-3 text-[12px] font-medium opacity-40 cursor-not-allowed"
                  :style="{ background: D.accents.lavender.soft, color: D.accents.lavender.bold, border: `1px solid ${D.accents.lavender.bold}` }">Disabled</span>
                <span class="inline-flex items-center rounded-full h-7 px-3 text-[12px] font-medium opacity-40 cursor-not-allowed"
                  :style="{ background: D.accents.peach.soft, color: D.accents.peach.bold, border: `1px solid ${D.accents.peach.bold}` }">Disabled</span>
              </div>
            </div>

            <!-- Cluster dark -->
            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">Cluster — tag picker row</p>
              <div class="flex flex-wrap gap-2">
                <span class="chip-b-dk inline-flex items-center rounded-full h-7 px-3 text-[12px] font-medium" :style="{ background: D.accents.lavender.soft, color: D.accents.lavender.bold, border: `1px solid ${D.accents.lavender.bold}` }">Family</span>
                <span class="chip-b-dk inline-flex items-center rounded-full h-7 px-3 text-[12px] font-medium" :style="{ background: D.accents.peach.soft, color: D.accents.peach.bold, border: `1px solid ${D.accents.peach.bold}` }">School</span>
                <span class="chip-b-dk inline-flex items-center rounded-full h-7 px-3 text-[12px] font-medium" :style="{ background: D.accents.mint.soft, color: D.accents.mint.bold, border: `1px solid ${D.accents.mint.bold}` }">Kids</span>
                <span class="chip-b-dk inline-flex items-center rounded-full h-7 px-3 text-[12px] font-medium" :style="{ background: D.accents.sun.soft, color: D.accents.sun.bold, border: `1px solid ${D.accents.sun.bold}` }">House</span>
                <span class="chip-b-dk inline-flex items-center gap-1 rounded-full h-7 px-3 text-[12px] font-medium" :style="{ background: D.accents.lavender.soft, color: D.accents.lavender.bold, border: `1px solid ${D.accents.lavender.bold}` }">
                  <CheckIcon class="w-3.5 h-3.5" />Groceries
                </span>
              </div>
            </div>

          </div><!-- /dark panel B -->
        </div>
      </VariantFrame>

      <p class="mt-3 text-body-sm text-ink-secondary px-1">
        Outlined + soft tint: the border is the primary affordance signal — you know immediately which are interactive because they have a visible boundary. The off state (grey border, neutral bg) vs on state (color border + fill) creates the clearest binary for filter-chip use cases. Best for "filter bar" contexts like the shopping list, task lists, and search filters.
      </p>
    </section>

    <!-- ══════════════════════════════════════════════════════════════
         INLINE STATUS INDICATOR — Dot + label
         Not a chip variant. A tiny status affordance for dense contexts
         (task rows, event tiles, activity feed) where a full chip would
         be too much chrome. Dot carries the color; the label renders in
         ink-primary so it reads as text, not a capsule.
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="Inline" caption="Inline status indicator — tiny dot + label for dense rows where a full chip is too much">
        <div class="w-full space-y-10">

          <!-- ── LIGHT PANEL C ─────────────────────────────────────── -->
          <div class="rounded-2xl border p-6 space-y-8" style="background:#FAF8F5; border-color:#E8E4DF">
            <p class="text-xs font-semibold uppercase tracking-widest" style="color:#9C9895">Light mode</p>

            <!-- Row 1: Category accents (dot = bold color, label = ink) -->
            <div>
              <p class="text-xs mb-3" style="color:#9C9895">Category accents — dot carries the color</p>
              <div class="flex flex-wrap gap-3">
                <span
                  v-for="cat in categoryAccents" :key="cat.key"
                  class="chip-c-lt inline-flex items-center gap-1.5 rounded-full h-7 px-3 text-[12px]"
                  style="background: transparent; color: #1C1C1E"
                >
                  <span class="w-2 h-2 rounded-full flex-shrink-0"
                    :style="{ background: L.accents[cat.key].bold }"></span>
                  {{ cat.label }}
                </span>
              </div>
              <p class="mt-2 text-[11px]" style="color:#9C9895">Transparent bg — blends into parent surface. Hover adds a subtle tint.</p>
            </div>

            <!-- Row 1b: With bg-sunken contained feel -->
            <div>
              <p class="text-xs mb-3" style="color:#9C9895">Category accents — contained (bg-sunken surface)</p>
              <div class="flex flex-wrap gap-2">
                <span
                  v-for="cat in categoryAccents" :key="cat.key"
                  class="chip-c-lt inline-flex items-center gap-1.5 rounded-full h-7 px-3 text-[12px]"
                  :style="{ background: L.surfaceSunken, color: L.inkPrimary }"
                >
                  <span class="w-2 h-2 rounded-full flex-shrink-0"
                    :style="{ background: L.accents[cat.key].bold }"></span>
                  {{ cat.label }}
                </span>
              </div>
            </div>

            <!-- Row 2: Status — dot uses status color directly -->
            <div>
              <p class="text-xs mb-3" style="color:#9C9895">Status — the dot vocabulary</p>
              <div class="flex flex-wrap gap-2">
                <span
                  v-for="s in statusChips" :key="s.key"
                  class="chip-c-lt inline-flex items-center gap-1.5 rounded-full h-7 px-3 text-[12px]"
                  :style="{ background: L.surfaceSunken, color: L.inkPrimary }"
                >
                  <span class="w-2 h-2 rounded-full flex-shrink-0"
                    :style="{ background: L.status[s.key] }"></span>
                  {{ s.label }}
                </span>
              </div>
            </div>

            <!-- Row 3: Sizes -->
            <div>
              <p class="text-xs mb-3" style="color:#9C9895">Sizes</p>
              <div class="flex flex-wrap items-center gap-3">
                <span class="chip-c-lt inline-flex items-center gap-1 rounded-full h-6 px-2.5 text-[11px]"
                  :style="{ background: L.surfaceSunken, color: L.inkPrimary }">
                  <span class="w-1.5 h-1.5 rounded-full flex-shrink-0" style="background:#6856B2"></span>
                  sm — 24px
                </span>
                <span class="chip-c-lt inline-flex items-center gap-1.5 rounded-full h-7 px-3 text-[12px]"
                  :style="{ background: L.surfaceSunken, color: L.inkPrimary }">
                  <span class="w-2 h-2 rounded-full flex-shrink-0" style="background:#6856B2"></span>
                  md — 28px
                </span>
              </div>
            </div>

            <!-- Row 4: Affordances -->
            <div>
              <p class="text-xs mb-3" style="color:#9C9895">Affordances</p>
              <div class="flex flex-wrap gap-3 items-center">
                <!-- Static dot + label (transparent bg) -->
                <span class="chip-c-lt inline-flex items-center gap-1.5 rounded-full h-7 px-3 text-[12px]"
                  style="background:transparent; color:#1C1C1E">
                  <span class="w-2 h-2 rounded-full flex-shrink-0" style="background:#6856B2"></span>
                  Static
                </span>

                <!-- Removable -->
                <span class="chip-c-lt inline-flex items-center gap-1.5 rounded-full h-7 pl-3 pr-1 text-[12px]"
                  :style="{ background: L.surfaceSunken, color: L.inkPrimary }">
                  <span class="w-2 h-2 rounded-full flex-shrink-0" style="background:#BA562E"></span>
                  Removable
                  <button type="button" aria-label="Remove"
                    class="chip-c-remove-lt ml-1 w-5 h-5 flex items-center justify-center rounded-full flex-shrink-0"
                    :style="{ color: L.inkSecondary }">
                    <XMarkIcon class="w-3 h-3" />
                  </button>
                </span>

                <!-- Icon-left variant (icon replaces dot) -->
                <span class="chip-c-lt inline-flex items-center gap-1.5 rounded-full h-7 px-3 text-[12px]"
                  :style="{ background: L.surfaceSunken, color: L.inkPrimary }">
                  <CheckIcon class="w-3.5 h-3.5 flex-shrink-0" style="color:#2E8A62" />
                  Completed
                </span>

                <!-- Interactive toggle -->
                <button type="button"
                  class="chip-c-lt inline-flex items-center gap-1.5 rounded-full h-7 px-3 text-[12px] transition-all"
                  :style="{ background: L.surfaceSunken, color: L.inkPrimary }">
                  <span class="w-2 h-2 rounded-full flex-shrink-0" style="background:#A2780C"></span>
                  House
                </button>
              </div>
            </div>

            <!-- Row 5: Dense status bar use case -->
            <div>
              <p class="text-xs mb-3" style="color:#9C9895">Dense status context — inline in a task row</p>
              <div class="flex flex-col gap-2 max-w-sm">
                <div class="flex items-center justify-between py-2 px-3 rounded-xl" style="background:#FFFFFF; border:1px solid #E8E4DF">
                  <span class="text-[13px] font-medium" style="color:#1C1C1E">Do laundry</span>
                  <span class="chip-c-lt inline-flex items-center gap-1.5 h-6 px-2.5 rounded-full text-[11px]" style="background:#F5F2EE; color:#1C1C1E">
                    <span class="w-1.5 h-1.5 rounded-full flex-shrink-0" style="background:#4D8C6A"></span>
                    Done
                  </span>
                </div>
                <div class="flex items-center justify-between py-2 px-3 rounded-xl" style="background:#FFFFFF; border:1px solid #E8E4DF">
                  <span class="text-[13px] font-medium" style="color:#1C1C1E">Call school</span>
                  <span class="chip-c-lt inline-flex items-center gap-1.5 h-6 px-2.5 rounded-full text-[11px]" style="background:#F5F2EE; color:#1C1C1E">
                    <span class="w-1.5 h-1.5 rounded-full flex-shrink-0" style="background:#C48C24"></span>
                    Due soon
                  </span>
                </div>
                <div class="flex items-center justify-between py-2 px-3 rounded-xl" style="background:#FFFFFF; border:1px solid #E8E4DF">
                  <span class="text-[13px] font-medium" style="color:#1C1C1E">Pay electric bill</span>
                  <span class="chip-c-lt inline-flex items-center gap-1.5 h-6 px-2.5 rounded-full text-[11px]" style="background:#F5F2EE; color:#1C1C1E">
                    <span class="w-1.5 h-1.5 rounded-full flex-shrink-0" style="background:#486E9C"></span>
                    Pending
                  </span>
                </div>
              </div>
            </div>

            <!-- Disabled -->
            <div class="flex flex-wrap gap-2">
              <span class="inline-flex items-center gap-1.5 rounded-full h-7 px-3 text-[12px] opacity-40 cursor-not-allowed"
                :style="{ background: L.surfaceSunken, color: L.inkPrimary }">
                <span class="w-2 h-2 rounded-full" style="background:#6856B2"></span>
                Disabled
              </span>
            </div>

          </div><!-- /light panel C -->

          <!-- ── DARK PANEL C ─────────────────────────────────────── -->
          <div class="rounded-2xl border p-6 space-y-8" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

            <!-- Row 1: Category accents transparent dark -->
            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">Category accents — transparent</p>
              <div class="flex flex-wrap gap-3">
                <span
                  v-for="cat in categoryAccents" :key="cat.key"
                  class="chip-c-dk inline-flex items-center gap-1.5 rounded-full h-7 px-3 text-[12px]"
                  :style="{ background: 'transparent', color: D.inkPrimary }"
                >
                  <span class="w-2 h-2 rounded-full flex-shrink-0"
                    :style="{ background: D.accents[cat.key].bold }"></span>
                  {{ cat.label }}
                </span>
              </div>
            </div>

            <!-- Row 1b: Contained dark -->
            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">Category accents — contained</p>
              <div class="flex flex-wrap gap-2">
                <span
                  v-for="cat in categoryAccents" :key="cat.key"
                  class="chip-c-dk inline-flex items-center gap-1.5 rounded-full h-7 px-3 text-[12px]"
                  :style="{ background: D.surfaceRaised, color: D.inkPrimary }"
                >
                  <span class="w-2 h-2 rounded-full flex-shrink-0"
                    :style="{ background: D.accents[cat.key].bold }"></span>
                  {{ cat.label }}
                </span>
              </div>
            </div>

            <!-- Row 2: Status dark -->
            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">Status</p>
              <div class="flex flex-wrap gap-2">
                <span
                  v-for="s in statusChips" :key="s.key"
                  class="chip-c-dk inline-flex items-center gap-1.5 rounded-full h-7 px-3 text-[12px]"
                  :style="{ background: D.surfaceRaised, color: D.inkPrimary }"
                >
                  <span class="w-2 h-2 rounded-full flex-shrink-0"
                    :style="{ background: D.status[s.key] }"></span>
                  {{ s.label }}
                </span>
              </div>
            </div>

            <!-- Row 3: Sizes dark -->
            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">Sizes</p>
              <div class="flex flex-wrap items-center gap-3">
                <span class="chip-c-dk inline-flex items-center gap-1 rounded-full h-6 px-2.5 text-[11px]"
                  :style="{ background: D.surfaceRaised, color: D.inkPrimary }">
                  <span class="w-1.5 h-1.5 rounded-full flex-shrink-0" :style="{ background: D.accents.lavender.bold }"></span>
                  sm — 24px
                </span>
                <span class="chip-c-dk inline-flex items-center gap-1.5 rounded-full h-7 px-3 text-[12px]"
                  :style="{ background: D.surfaceRaised, color: D.inkPrimary }">
                  <span class="w-2 h-2 rounded-full flex-shrink-0" :style="{ background: D.accents.lavender.bold }"></span>
                  md — 28px
                </span>
              </div>
            </div>

            <!-- Row 4: Affordances dark -->
            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">Affordances</p>
              <div class="flex flex-wrap gap-3 items-center">
                <span class="chip-c-dk inline-flex items-center gap-1.5 rounded-full h-7 px-3 text-[12px]"
                  :style="{ background: 'transparent', color: D.inkPrimary }">
                  <span class="w-2 h-2 rounded-full flex-shrink-0" :style="{ background: D.accents.lavender.bold }"></span>
                  Static
                </span>

                <span class="chip-c-dk inline-flex items-center gap-1.5 rounded-full h-7 pl-3 pr-1 text-[12px]"
                  :style="{ background: D.surfaceRaised, color: D.inkPrimary }">
                  <span class="w-2 h-2 rounded-full flex-shrink-0" :style="{ background: D.accents.peach.bold }"></span>
                  Removable
                  <button type="button" aria-label="Remove"
                    class="chip-c-remove-dk ml-1 w-5 h-5 flex items-center justify-center rounded-full flex-shrink-0"
                    :style="{ color: D.inkSecondary }">
                    <XMarkIcon class="w-3 h-3" />
                  </button>
                </span>

                <span class="chip-c-dk inline-flex items-center gap-1.5 rounded-full h-7 px-3 text-[12px]"
                  :style="{ background: D.surfaceRaised, color: D.inkPrimary }">
                  <CheckIcon class="w-3.5 h-3.5 flex-shrink-0" :style="{ color: D.accents.mint.bold }" />
                  Completed
                </span>
              </div>
            </div>

            <!-- Row 5: Dense status bar dark -->
            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">Dense status context — inline in task row</p>
              <div class="flex flex-col gap-2 max-w-sm">
                <div class="flex items-center justify-between py-2 px-3 rounded-xl"
                  :style="{ background: D.surfaceRaised, border: `1px solid ${D.borderSubtle}` }">
                  <span class="text-[13px] font-medium" :style="{ color: D.inkPrimary }">Do laundry</span>
                  <span class="chip-c-dk inline-flex items-center gap-1.5 h-6 px-2.5 rounded-full text-[11px]"
                    :style="{ background: D.surfaceSunken, color: D.inkPrimary }">
                    <span class="w-1.5 h-1.5 rounded-full flex-shrink-0" :style="{ background: D.status.success }"></span>
                    Done
                  </span>
                </div>
                <div class="flex items-center justify-between py-2 px-3 rounded-xl"
                  :style="{ background: D.surfaceRaised, border: `1px solid ${D.borderSubtle}` }">
                  <span class="text-[13px] font-medium" :style="{ color: D.inkPrimary }">Call school</span>
                  <span class="chip-c-dk inline-flex items-center gap-1.5 h-6 px-2.5 rounded-full text-[11px]"
                    :style="{ background: D.surfaceSunken, color: D.inkPrimary }">
                    <span class="w-1.5 h-1.5 rounded-full flex-shrink-0" :style="{ background: D.status.warning }"></span>
                    Due soon
                  </span>
                </div>
                <div class="flex items-center justify-between py-2 px-3 rounded-xl"
                  :style="{ background: D.surfaceRaised, border: `1px solid ${D.borderSubtle}` }">
                  <span class="text-[13px] font-medium" :style="{ color: D.inkPrimary }">Pay electric bill</span>
                  <span class="chip-c-dk inline-flex items-center gap-1.5 h-6 px-2.5 rounded-full text-[11px]"
                    :style="{ background: D.surfaceSunken, color: D.inkPrimary }">
                    <span class="w-1.5 h-1.5 rounded-full flex-shrink-0" :style="{ background: D.status.pending }"></span>
                    Pending
                  </span>
                </div>
              </div>
            </div>

            <!-- Disabled dark -->
            <div class="flex flex-wrap gap-2">
              <span class="inline-flex items-center gap-1.5 rounded-full h-7 px-3 text-[12px] opacity-40 cursor-not-allowed"
                :style="{ background: D.surfaceRaised, color: D.inkPrimary }">
                <span class="w-2 h-2 rounded-full" :style="{ background: D.accents.lavender.bold }"></span>
                Disabled
              </span>
            </div>

          </div><!-- /dark panel C -->
        </div>
      </VariantFrame>

      <p class="mt-3 text-body-sm text-ink-secondary px-1">
        The inline indicator is <strong>not</strong> a chip variant — it complements the chip. Use it in task rows, activity feeds, calendar event summaries, or shopping list items where a full chip would disrupt the rhythm. The dot vocabulary is instantly learnable — after 2–3 uses, the color signals meaning without reading the label. For more explicit status contexts, the dot can be replaced by a Heroicon.
      </p>
    </section>

    <!-- ══════════════════════════════════════════════════════════════
         USAGE GUIDE — When to reach for each
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-8">
      <div class="rounded-2xl border p-6 space-y-4" style="background:#FFFFFF; border-color:#E8E4DF">
        <h2 class="text-[17px] font-semibold" style="color:#1C1C1E">When to use which</h2>
        <ul class="space-y-3 text-[14px]" style="color:#6B6966">
          <li>
            <strong style="color:#1C1C1E">Chip (outlined + tint):</strong>
            The canonical chip. Use for category tags, recipe labels, vault entry types, filter bars, meal plan categories, and anywhere a capsule-shaped label or filter belongs. Strongest off/on contrast makes it the default filter-chip pattern.
          </li>
          <li>
            <strong style="color:#1C1C1E">Inline status indicator (dot + label):</strong>
            Not a chip — a lighter affordance for dense contexts. Use inside task rows, activity feeds, calendar event summaries, and shopping list items where a full chip would be visual noise. The dot carries the color; the label stays as text.
          </li>
        </ul>
      </div>
    </section>

  </ComponentPage>
</template>

<style scoped>
/*
  ─────────────────────────────────────────────────────────────────
  VARIANT B — Light panel
  Hover: bg fills to full -soft from translucent start
  ─────────────────────────────────────────────────────────────────
*/
.chip-b-lt {
  transition: background-color 200ms, border-color 200ms;
}
.chip-b-lt:hover {
  filter: brightness(0.96);
}
.chip-b-remove-lt:hover {
  background-color: rgba(0, 0, 0, 0.08);
}

/*
  ─────────────────────────────────────────────────────────────────
  VARIANT B — Dark panel
  ─────────────────────────────────────────────────────────────────
*/
.chip-b-dk {
  transition: filter 200ms, border-color 200ms;
}
.chip-b-dk:hover {
  filter: brightness(1.12);
}
.chip-b-remove-dk:hover {
  background-color: rgba(255, 255, 255, 0.10);
}

/*
  ─────────────────────────────────────────────────────────────────
  VARIANT C — Light panel
  Hover: transparent chips get a soft sunken bg tint
  ─────────────────────────────────────────────────────────────────
*/
.chip-c-lt {
  transition: background-color 200ms;
}
.chip-c-lt:hover {
  background-color: #F5F2EE !important;
}
.chip-c-remove-lt:hover {
  background-color: rgba(0, 0, 0, 0.07);
}

/*
  ─────────────────────────────────────────────────────────────────
  VARIANT C — Dark panel
  Hover: chips get a slightly lighter raised bg
  ─────────────────────────────────────────────────────────────────
*/
.chip-c-dk {
  transition: background-color 200ms;
}
.chip-c-dk:hover {
  background-color: #242220 !important;
}
.chip-c-remove-dk:hover {
  background-color: rgba(255, 255, 255, 0.10);
}
</style>
