<script setup>
import { ref } from 'vue'
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import { SparklesIcon } from '@heroicons/vue/24/outline'
import KinSegmentedFilter from '@/components/design-system/KinSegmentedFilter.vue'

const kinFeedL = ref('all')
const kinFeedD = ref('all')
const kinTimeL = ref('w')
const kinTimeD = ref('w')

// ── Palette constants ────────────────────────────────────────────────────────

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
    success: '#4D8C6A', pending: '#486E9C', paused: '#BE8230',
    failed: '#BA4A4A', info: '#6856B2', warning: '#C48C24',
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
    success: '#6CC498', pending: '#78A4DC', paused: '#DCA848',
    failed: '#E67070', info: '#B6A8E6', warning: '#E6C452',
  },
}

// ── Segment data ─────────────────────────────────────────────────────────────

const FEED_OPTIONS = [
  { key: 'all',     label: 'All',     count: 42 },
  { key: 'success', label: 'Success', count: 28 },
  { key: 'failed',  label: 'Failed',  count: 7  },
  { key: 'paused',  label: 'Paused',  count: 5  },
]

const TIME_OPTIONS = [
  { key: 'd', label: 'D' },
  { key: 'w', label: 'W' },
  { key: 'm', label: 'M' },
  { key: 'y', label: 'Y' },
]

// ── Reactive active states — per variant × mode × context ────────────────────

// Variant A
const aFeedLt   = ref('all');   const aFeedDk   = ref('all')
const aTimeLt   = ref('w');     const aTimeDk   = ref('w')

// Variant B
const bFeedLt   = ref('all');   const bFeedDk   = ref('all')
const bTimeLt   = ref('w');     const bTimeDk   = ref('w')

// Variant C
const cFeedLt   = ref('all');   const cFeedDk   = ref('all')
const cTimeLt   = ref('w');     const cTimeDk   = ref('w')

// ── Variant B sliding position helpers ───────────────────────────────────────

function activeIndex(options, activeKey) {
  return options.findIndex(o => o.key === activeKey)
}

</script>

<template>
  <ComponentPage
    title="4.2 SegmentedFilter"
    description="Segmented pill control for mutually-exclusive filter selection — activity feeds, time-range pickers, leaderboard period selectors, and notification filters."
    status="scaffolded"
  >
    <!-- ═══════════════════════════════════════════════════════════════════
         VARIANT A — LOCKED: Filled active · plain inactive · single outlined container
         Ease-out fill transition (220ms cubic-bezier(0.22, 1, 0.36, 1)) on the
         active pill's background + text color. Respects prefers-reduced-motion.
         ════════════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="A" caption="Filled active · plain inactive · single outlined container · ease-out fill transition (LOCKED)">
        <div class="w-full space-y-10">
          <!-- LIGHT PANEL A -->
          <div class="rounded-2xl border p-6 space-y-8" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>

            <!-- Activity feed — md (32px), with count badges -->
            <div>
              <p class="text-xs mb-3" :style="{ color: L.inkTertiary }">Activity feed filter — md (32px) with counts</p>
              <div
                class="inline-flex rounded-full p-0.5"
                :style="{ border: `1px solid ${L.borderStrong}`, background: L.surfaceRaised }"
              >
                <button
                  v-for="opt in FEED_OPTIONS"
                  :key="opt.key"
                  type="button"
                  class="seg-a-lt inline-flex items-center gap-1.5 rounded-full h-8 px-3.5 text-[13px] font-medium transition-all"
                  :style="aFeedLt === opt.key
                    ? { background: L.inkPrimary, color: L.inkInverse }
                    : { background: 'transparent', color: L.inkSecondary }"
                  @click="aFeedLt = opt.key"
                >
                  {{ opt.label }}
                  <span
                    class="inline-flex items-center justify-center rounded-full h-4 min-w-[1rem] px-1 text-[10px] font-semibold leading-none"
                    :style="aFeedLt === opt.key
                      ? { background: 'rgba(250,248,245,0.20)', color: L.inkInverse }
                      : { background: L.surfaceSunken, color: L.inkTertiary }"
                  >{{ opt.count }}</span>
                </button>
              </div>
            </div>

            <!-- Activity feed — sm (26px), no counts -->
            <div>
              <p class="text-xs mb-3" :style="{ color: L.inkTertiary }">Activity feed filter — sm (26px), no counts</p>
              <div
                class="inline-flex rounded-full p-0.5"
                :style="{ border: `1px solid ${L.borderStrong}`, background: L.surfaceRaised }"
              >
                <button
                  v-for="opt in FEED_OPTIONS"
                  :key="opt.key"
                  type="button"
                  class="seg-a-lt inline-flex items-center rounded-full h-[26px] px-3 text-[12px] font-medium transition-all"
                  :style="aFeedLt === opt.key
                    ? { background: L.inkPrimary, color: L.inkInverse }
                    : { background: 'transparent', color: L.inkSecondary }"
                  @click="aFeedLt = opt.key"
                >
                  {{ opt.label }}
                </button>
              </div>
            </div>

            <!-- Time range — md -->
            <div>
              <p class="text-xs mb-3" :style="{ color: L.inkTertiary }">Time range selector — md, compact labels</p>
              <div
                class="inline-flex rounded-full p-0.5"
                :style="{ border: `1px solid ${L.borderStrong}`, background: L.surfaceRaised }"
              >
                <button
                  v-for="opt in TIME_OPTIONS"
                  :key="opt.key"
                  type="button"
                  class="seg-a-lt inline-flex items-center justify-center rounded-full w-9 h-8 text-[13px] font-semibold transition-all"
                  :style="aTimeLt === opt.key
                    ? { background: L.inkPrimary, color: L.inkInverse }
                    : { background: 'transparent', color: L.inkSecondary }"
                  @click="aTimeLt = opt.key"
                >
                  {{ opt.label }}
                </button>
              </div>
            </div>

            <!-- Time range — sm -->
            <div>
              <p class="text-xs mb-3" :style="{ color: L.inkTertiary }">Time range selector — sm (26px)</p>
              <div
                class="inline-flex rounded-full p-0.5"
                :style="{ border: `1px solid ${L.borderStrong}`, background: L.surfaceRaised }"
              >
                <button
                  v-for="opt in TIME_OPTIONS"
                  :key="opt.key"
                  type="button"
                  class="seg-a-lt inline-flex items-center justify-center rounded-full w-8 h-[26px] text-[12px] font-semibold transition-all"
                  :style="aTimeLt === opt.key
                    ? { background: L.inkPrimary, color: L.inkInverse }
                    : { background: 'transparent', color: L.inkSecondary }"
                  @click="aTimeLt = opt.key"
                >
                  {{ opt.label }}
                </button>
              </div>
            </div>

            <!-- Disabled state -->
            <div>
              <p class="text-xs mb-3" :style="{ color: L.inkTertiary }">Disabled</p>
              <div
                class="inline-flex rounded-full p-0.5 opacity-40 cursor-not-allowed"
                :style="{ border: `1px solid ${L.borderStrong}`, background: L.surfaceRaised }"
              >
                <span
                  v-for="opt in FEED_OPTIONS"
                  :key="opt.key"
                  class="inline-flex items-center rounded-full h-8 px-3.5 text-[13px] font-medium"
                  :style="opt.key === 'all'
                    ? { background: L.inkPrimary, color: L.inkInverse }
                    : { background: 'transparent', color: L.inkSecondary }"
                >{{ opt.label }}</span>
              </div>
            </div>
          </div><!-- /light panel A -->

          <!-- DARK PANEL A -->
          <div class="rounded-2xl border p-6 space-y-8" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

            <!-- Activity feed dark — md with counts -->
            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">Activity feed filter — md (32px) with counts</p>
              <div
                class="inline-flex rounded-full p-0.5"
                :style="{ border: `1px solid ${D.borderStrong}`, background: D.surfaceRaised }"
              >
                <button
                  v-for="opt in FEED_OPTIONS"
                  :key="opt.key"
                  type="button"
                  class="seg-a-dk inline-flex items-center gap-1.5 rounded-full h-8 px-3.5 text-[13px] font-medium transition-all"
                  :style="aFeedDk === opt.key
                    ? { background: D.inkPrimary, color: D.inkInverse }
                    : { background: 'transparent', color: D.inkSecondary }"
                  @click="aFeedDk = opt.key"
                >
                  {{ opt.label }}
                  <span
                    class="inline-flex items-center justify-center rounded-full h-4 min-w-[1rem] px-1 text-[10px] font-semibold leading-none"
                    :style="aFeedDk === opt.key
                      ? { background: 'rgba(20,19,17,0.25)', color: D.inkInverse }
                      : { background: D.surfaceSunken, color: D.inkTertiary }"
                  >{{ opt.count }}</span>
                </button>
              </div>
            </div>

            <!-- Activity feed dark — sm no counts -->
            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">Activity feed filter — sm (26px), no counts</p>
              <div
                class="inline-flex rounded-full p-0.5"
                :style="{ border: `1px solid ${D.borderStrong}`, background: D.surfaceRaised }"
              >
                <button
                  v-for="opt in FEED_OPTIONS"
                  :key="opt.key"
                  type="button"
                  class="seg-a-dk inline-flex items-center rounded-full h-[26px] px-3 text-[12px] font-medium transition-all"
                  :style="aFeedDk === opt.key
                    ? { background: D.inkPrimary, color: D.inkInverse }
                    : { background: 'transparent', color: D.inkSecondary }"
                  @click="aFeedDk = opt.key"
                >
                  {{ opt.label }}
                </button>
              </div>
            </div>

            <!-- Time range dark — md -->
            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">Time range selector — md</p>
              <div
                class="inline-flex rounded-full p-0.5"
                :style="{ border: `1px solid ${D.borderStrong}`, background: D.surfaceRaised }"
              >
                <button
                  v-for="opt in TIME_OPTIONS"
                  :key="opt.key"
                  type="button"
                  class="seg-a-dk inline-flex items-center justify-center rounded-full w-9 h-8 text-[13px] font-semibold transition-all"
                  :style="aTimeDk === opt.key
                    ? { background: D.inkPrimary, color: D.inkInverse }
                    : { background: 'transparent', color: D.inkSecondary }"
                  @click="aTimeDk = opt.key"
                >
                  {{ opt.label }}
                </button>
              </div>
            </div>

            <!-- Time range dark — sm -->
            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">Time range selector — sm (26px)</p>
              <div
                class="inline-flex rounded-full p-0.5"
                :style="{ border: `1px solid ${D.borderStrong}`, background: D.surfaceRaised }"
              >
                <button
                  v-for="opt in TIME_OPTIONS"
                  :key="opt.key"
                  type="button"
                  class="seg-a-dk inline-flex items-center justify-center rounded-full w-8 h-[26px] text-[12px] font-semibold transition-all"
                  :style="aTimeDk === opt.key
                    ? { background: D.inkPrimary, color: D.inkInverse }
                    : { background: 'transparent', color: D.inkSecondary }"
                  @click="aTimeDk = opt.key"
                >
                  {{ opt.label }}
                </button>
              </div>
            </div>

            <!-- Disabled dark -->
            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">Disabled</p>
              <div
                class="inline-flex rounded-full p-0.5 opacity-40 cursor-not-allowed"
                :style="{ border: `1px solid ${D.borderStrong}`, background: D.surfaceRaised }"
              >
                <span
                  v-for="opt in FEED_OPTIONS"
                  :key="opt.key"
                  class="inline-flex items-center rounded-full h-8 px-3.5 text-[13px] font-medium"
                  :style="opt.key === 'all'
                    ? { background: D.inkPrimary, color: D.inkInverse }
                    : { background: 'transparent', color: D.inkSecondary }"
                >{{ opt.label }}</span>
              </div>
            </div>
          </div><!-- /dark panel A -->
        </div>
      </VariantFrame>
      <p class="mt-3 text-sm px-1" :style="{ color: L.inkSecondary }">
        Simple and trustworthy — the shared outer border reads as a single cohesive control. Active ink fill is the clearest possible affordance with zero motion cost. Best for accessibility-first contexts where motion budgets are tight.
      </p>
    </section>


    <!-- ═══════════════════════════════════════════════════════════════════
         VARIANT B — Pill container with sliding active background (iOS style)
         Active background physically moves between segments via CSS transform.
         Respects prefers-reduced-motion.
         ════════════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="B" caption="Sliding active background (iOS segmented control) · monochrome ink indicator · ease-out 200ms">
        <div class="w-full space-y-10">
          <!-- LIGHT PANEL B -->
          <div class="rounded-2xl border p-6 space-y-8" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>

            <!-- Activity feed — md with counts -->
            <div>
              <p class="text-xs mb-3" :style="{ color: L.inkTertiary }">Activity feed filter — md (32px) with counts</p>
              <div
                class="relative inline-flex rounded-full p-0.5"
                :style="{ background: L.surfaceSunken, border: `1px solid ${L.borderSubtle}` }"
              >
                <!-- Sliding indicator -->
                <span
                  aria-hidden="true"
                  class="b-slider absolute top-0.5 bottom-0.5 rounded-full"
                  :style="{
                    width: `calc(${100 / FEED_OPTIONS.length}% - 2px)`,
                    left: '2px',
                    transform: `translateX(calc(${activeIndex(FEED_OPTIONS, bFeedLt)} * 100% + ${activeIndex(FEED_OPTIONS, bFeedLt)} * 2px))`,
                    background: L.surfaceRaised,
                    boxShadow: '0 1px 3px rgba(28,20,10,0.12), 0 1px 2px rgba(28,20,10,0.08)',
                  }"
                ></span>
                <!-- Buttons (above slider) -->
                <button
                  v-for="opt in FEED_OPTIONS"
                  :key="opt.key"
                  type="button"
                  class="relative z-10 seg-b-lt inline-flex items-center gap-1.5 rounded-full h-8 px-3.5 text-[13px] font-medium"
                  :style="bFeedLt === opt.key
                    ? { color: L.inkPrimary }
                    : { color: L.inkSecondary }"
                  @click="bFeedLt = opt.key"
                >
                  {{ opt.label }}
                  <span
                    class="inline-flex items-center justify-center rounded-full h-4 min-w-[1rem] px-1 text-[10px] font-semibold leading-none"
                    :style="bFeedLt === opt.key
                      ? { background: L.surfaceSunken, color: L.inkPrimary }
                      : { background: 'transparent', color: L.inkTertiary }"
                  >{{ opt.count }}</span>
                </button>
              </div>
            </div>

            <!-- Activity feed — sm no counts -->
            <div>
              <p class="text-xs mb-3" :style="{ color: L.inkTertiary }">Activity feed filter — sm (26px), no counts</p>
              <div
                class="relative inline-flex rounded-full p-0.5"
                :style="{ background: L.surfaceSunken, border: `1px solid ${L.borderSubtle}` }"
              >
                <span
                  aria-hidden="true"
                  class="b-slider absolute top-0.5 bottom-0.5 rounded-full"
                  :style="{
                    width: `calc(${100 / FEED_OPTIONS.length}% - 2px)`,
                    left: '2px',
                    transform: `translateX(calc(${activeIndex(FEED_OPTIONS, bFeedLt)} * 100% + ${activeIndex(FEED_OPTIONS, bFeedLt)} * 2px))`,
                    background: L.surfaceRaised,
                    boxShadow: '0 1px 3px rgba(28,20,10,0.12), 0 1px 2px rgba(28,20,10,0.08)',
                  }"
                ></span>
                <button
                  v-for="opt in FEED_OPTIONS"
                  :key="opt.key"
                  type="button"
                  class="relative z-10 seg-b-lt inline-flex items-center rounded-full h-[26px] px-3 text-[12px] font-medium"
                  :style="bFeedLt === opt.key ? { color: L.inkPrimary } : { color: L.inkSecondary }"
                  @click="bFeedLt = opt.key"
                >
                  {{ opt.label }}
                </button>
              </div>
            </div>

            <!-- Time range — md -->
            <div>
              <p class="text-xs mb-3" :style="{ color: L.inkTertiary }">Time range selector — md, compact labels</p>
              <div
                class="relative inline-flex rounded-full p-0.5"
                :style="{ background: L.surfaceSunken, border: `1px solid ${L.borderSubtle}` }"
              >
                <span
                  aria-hidden="true"
                  class="b-slider absolute top-0.5 bottom-0.5 rounded-full"
                  :style="{
                    width: `calc(${100 / TIME_OPTIONS.length}% - 2px)`,
                    left: '2px',
                    transform: `translateX(calc(${activeIndex(TIME_OPTIONS, bTimeLt)} * 100% + ${activeIndex(TIME_OPTIONS, bTimeLt)} * 2px))`,
                    background: L.surfaceRaised,
                    boxShadow: '0 1px 3px rgba(28,20,10,0.12), 0 1px 2px rgba(28,20,10,0.08)',
                  }"
                ></span>
                <button
                  v-for="opt in TIME_OPTIONS"
                  :key="opt.key"
                  type="button"
                  class="relative z-10 seg-b-lt inline-flex items-center justify-center rounded-full w-9 h-8 text-[13px] font-semibold"
                  :style="bTimeLt === opt.key ? { color: L.inkPrimary } : { color: L.inkSecondary }"
                  @click="bTimeLt = opt.key"
                >
                  {{ opt.label }}
                </button>
              </div>
            </div>

            <!-- Time range — sm -->
            <div>
              <p class="text-xs mb-3" :style="{ color: L.inkTertiary }">Time range selector — sm (26px)</p>
              <div
                class="relative inline-flex rounded-full p-0.5"
                :style="{ background: L.surfaceSunken, border: `1px solid ${L.borderSubtle}` }"
              >
                <span
                  aria-hidden="true"
                  class="b-slider absolute top-0.5 bottom-0.5 rounded-full"
                  :style="{
                    width: `calc(${100 / TIME_OPTIONS.length}% - 2px)`,
                    left: '2px',
                    transform: `translateX(calc(${activeIndex(TIME_OPTIONS, bTimeLt)} * 100% + ${activeIndex(TIME_OPTIONS, bTimeLt)} * 2px))`,
                    background: L.surfaceRaised,
                    boxShadow: '0 1px 3px rgba(28,20,10,0.12), 0 1px 2px rgba(28,20,10,0.08)',
                  }"
                ></span>
                <button
                  v-for="opt in TIME_OPTIONS"
                  :key="opt.key"
                  type="button"
                  class="relative z-10 seg-b-lt inline-flex items-center justify-center rounded-full w-8 h-[26px] text-[12px] font-semibold"
                  :style="bTimeLt === opt.key ? { color: L.inkPrimary } : { color: L.inkSecondary }"
                  @click="bTimeLt = opt.key"
                >
                  {{ opt.label }}
                </button>
              </div>
            </div>

            <!-- Disabled -->
            <div>
              <p class="text-xs mb-3" :style="{ color: L.inkTertiary }">Disabled</p>
              <div
                class="relative inline-flex rounded-full p-0.5 opacity-40 cursor-not-allowed"
                :style="{ background: L.surfaceSunken, border: `1px solid ${L.borderSubtle}` }"
              >
                <span
                  aria-hidden="true"
                  class="absolute top-0.5 bottom-0.5 rounded-full"
                  :style="{
                    width: `calc(${100 / FEED_OPTIONS.length}% - 2px)`,
                    left: '2px',
                    background: L.surfaceRaised,
                    boxShadow: '0 1px 3px rgba(28,20,10,0.12)',
                  }"
                ></span>
                <span
                  v-for="opt in FEED_OPTIONS"
                  :key="opt.key"
                  class="relative z-10 inline-flex items-center rounded-full h-8 px-3.5 text-[13px] font-medium"
                  :style="opt.key === 'all' ? { color: L.inkPrimary } : { color: L.inkSecondary }"
                >{{ opt.label }}</span>
              </div>
            </div>
          </div><!-- /light panel B -->

          <!-- DARK PANEL B -->
          <div class="rounded-2xl border p-6 space-y-8" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

            <!-- Activity feed dark — md with counts -->
            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">Activity feed filter — md (32px) with counts</p>
              <div
                class="relative inline-flex rounded-full p-0.5"
                :style="{ background: D.surfaceSunken, border: `1px solid ${D.borderSubtle}` }"
              >
                <span
                  aria-hidden="true"
                  class="b-slider absolute top-0.5 bottom-0.5 rounded-full"
                  :style="{
                    width: `calc(${100 / FEED_OPTIONS.length}% - 2px)`,
                    left: '2px',
                    transform: `translateX(calc(${activeIndex(FEED_OPTIONS, bFeedDk)} * 100% + ${activeIndex(FEED_OPTIONS, bFeedDk)} * 2px))`,
                    background: D.surfaceOverlay,
                    boxShadow: '0 1px 3px rgba(0,0,0,0.35), 0 1px 2px rgba(0,0,0,0.25)',
                  }"
                ></span>
                <button
                  v-for="opt in FEED_OPTIONS"
                  :key="opt.key"
                  type="button"
                  class="relative z-10 seg-b-dk inline-flex items-center gap-1.5 rounded-full h-8 px-3.5 text-[13px] font-medium"
                  :style="bFeedDk === opt.key ? { color: D.inkPrimary } : { color: D.inkSecondary }"
                  @click="bFeedDk = opt.key"
                >
                  {{ opt.label }}
                  <span
                    class="inline-flex items-center justify-center rounded-full h-4 min-w-[1rem] px-1 text-[10px] font-semibold leading-none"
                    :style="bFeedDk === opt.key
                      ? { background: D.surfaceSunken, color: D.inkPrimary }
                      : { background: 'transparent', color: D.inkTertiary }"
                  >{{ opt.count }}</span>
                </button>
              </div>
            </div>

            <!-- Activity feed dark — sm no counts -->
            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">Activity feed filter — sm (26px), no counts</p>
              <div
                class="relative inline-flex rounded-full p-0.5"
                :style="{ background: D.surfaceSunken, border: `1px solid ${D.borderSubtle}` }"
              >
                <span
                  aria-hidden="true"
                  class="b-slider absolute top-0.5 bottom-0.5 rounded-full"
                  :style="{
                    width: `calc(${100 / FEED_OPTIONS.length}% - 2px)`,
                    left: '2px',
                    transform: `translateX(calc(${activeIndex(FEED_OPTIONS, bFeedDk)} * 100% + ${activeIndex(FEED_OPTIONS, bFeedDk)} * 2px))`,
                    background: D.surfaceOverlay,
                    boxShadow: '0 1px 3px rgba(0,0,0,0.35)',
                  }"
                ></span>
                <button
                  v-for="opt in FEED_OPTIONS"
                  :key="opt.key"
                  type="button"
                  class="relative z-10 seg-b-dk inline-flex items-center rounded-full h-[26px] px-3 text-[12px] font-medium"
                  :style="bFeedDk === opt.key ? { color: D.inkPrimary } : { color: D.inkSecondary }"
                  @click="bFeedDk = opt.key"
                >
                  {{ opt.label }}
                </button>
              </div>
            </div>

            <!-- Time range dark — md -->
            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">Time range selector — md</p>
              <div
                class="relative inline-flex rounded-full p-0.5"
                :style="{ background: D.surfaceSunken, border: `1px solid ${D.borderSubtle}` }"
              >
                <span
                  aria-hidden="true"
                  class="b-slider absolute top-0.5 bottom-0.5 rounded-full"
                  :style="{
                    width: `calc(${100 / TIME_OPTIONS.length}% - 2px)`,
                    left: '2px',
                    transform: `translateX(calc(${activeIndex(TIME_OPTIONS, bTimeDk)} * 100% + ${activeIndex(TIME_OPTIONS, bTimeDk)} * 2px))`,
                    background: D.surfaceOverlay,
                    boxShadow: '0 1px 3px rgba(0,0,0,0.35)',
                  }"
                ></span>
                <button
                  v-for="opt in TIME_OPTIONS"
                  :key="opt.key"
                  type="button"
                  class="relative z-10 seg-b-dk inline-flex items-center justify-center rounded-full w-9 h-8 text-[13px] font-semibold"
                  :style="bTimeDk === opt.key ? { color: D.inkPrimary } : { color: D.inkSecondary }"
                  @click="bTimeDk = opt.key"
                >
                  {{ opt.label }}
                </button>
              </div>
            </div>

            <!-- Time range dark — sm -->
            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">Time range selector — sm (26px)</p>
              <div
                class="relative inline-flex rounded-full p-0.5"
                :style="{ background: D.surfaceSunken, border: `1px solid ${D.borderSubtle}` }"
              >
                <span
                  aria-hidden="true"
                  class="b-slider absolute top-0.5 bottom-0.5 rounded-full"
                  :style="{
                    width: `calc(${100 / TIME_OPTIONS.length}% - 2px)`,
                    left: '2px',
                    transform: `translateX(calc(${activeIndex(TIME_OPTIONS, bTimeDk)} * 100% + ${activeIndex(TIME_OPTIONS, bTimeDk)} * 2px))`,
                    background: D.surfaceOverlay,
                    boxShadow: '0 1px 3px rgba(0,0,0,0.35)',
                  }"
                ></span>
                <button
                  v-for="opt in TIME_OPTIONS"
                  :key="opt.key"
                  type="button"
                  class="relative z-10 seg-b-dk inline-flex items-center justify-center rounded-full w-8 h-[26px] text-[12px] font-semibold"
                  :style="bTimeDk === opt.key ? { color: D.inkPrimary } : { color: D.inkSecondary }"
                  @click="bTimeDk = opt.key"
                >
                  {{ opt.label }}
                </button>
              </div>
            </div>

            <!-- Disabled dark -->
            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">Disabled</p>
              <div
                class="relative inline-flex rounded-full p-0.5 opacity-40 cursor-not-allowed"
                :style="{ background: D.surfaceSunken, border: `1px solid ${D.borderSubtle}` }"
              >
                <span
                  aria-hidden="true"
                  class="absolute top-0.5 bottom-0.5 rounded-full"
                  :style="{
                    width: `calc(${100 / FEED_OPTIONS.length}% - 2px)`,
                    left: '2px',
                    background: D.surfaceOverlay,
                  }"
                ></span>
                <span
                  v-for="opt in FEED_OPTIONS"
                  :key="opt.key"
                  class="relative z-10 inline-flex items-center rounded-full h-8 px-3.5 text-[13px] font-medium"
                  :style="opt.key === 'all' ? { color: D.inkPrimary } : { color: D.inkSecondary }"
                >{{ opt.label }}</span>
              </div>
            </div>
          </div><!-- /dark panel B -->
        </div>
      </VariantFrame>
      <p class="mt-3 text-sm px-1" :style="{ color: L.inkSecondary }">
        The sliding pill gives the control a premium, tactile feel without color complexity — the monochrome ink indicator reads cleanly on both light and dark surfaces. Ideal for the leaderboard period selector and calendar time-range control where the physical motion reinforces "moving through time."
      </p>
    </section>


    <!-- ═══════════════════════════════════════════════════════════════════
         VARIANT C — Outlined-pill-per-option row (gap between pills)
         Each option is its own individually rounded-full pill. Active fills
         with accent-bold + inkInverse text. More visual separation than A/B.
         ════════════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="C" caption="Individual outlined pills per option · gap between · active = accent-bold fill + inverse text">
        <div class="w-full space-y-10">
          <!-- LIGHT PANEL C -->
          <div class="rounded-2xl border p-6 space-y-8" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>

            <!-- Activity feed — md with counts (accent-lavender for active) -->
            <div>
              <p class="text-xs mb-3" :style="{ color: L.inkTertiary }">Activity feed filter — md (32px) with counts · lavender accent</p>
              <div class="flex flex-wrap gap-2">
                <button
                  v-for="opt in FEED_OPTIONS"
                  :key="opt.key"
                  type="button"
                  class="seg-c-lt inline-flex items-center gap-1.5 rounded-full h-8 px-3.5 text-[13px] font-medium transition-all"
                  :style="cFeedLt === opt.key
                    ? { background: L.accents.lavender.bold, color: L.inkInverse, border: `1px solid ${L.accents.lavender.bold}` }
                    : { background: L.surfaceRaised, color: L.inkSecondary, border: `1px solid ${L.borderStrong}` }"
                  @click="cFeedLt = opt.key"
                >
                  {{ opt.label }}
                  <span
                    class="inline-flex items-center justify-center rounded-full h-4 min-w-[1rem] px-1 text-[10px] font-semibold leading-none"
                    :style="cFeedLt === opt.key
                      ? { background: 'rgba(250,248,245,0.25)', color: L.inkInverse }
                      : { background: L.surfaceSunken, color: L.inkTertiary }"
                  >{{ opt.count }}</span>
                </button>
              </div>
            </div>

            <!-- Activity feed — sm no counts -->
            <div>
              <p class="text-xs mb-3" :style="{ color: L.inkTertiary }">Activity feed filter — sm (26px), no counts</p>
              <div class="flex flex-wrap gap-1.5">
                <button
                  v-for="opt in FEED_OPTIONS"
                  :key="opt.key"
                  type="button"
                  class="seg-c-lt inline-flex items-center rounded-full h-[26px] px-3 text-[12px] font-medium transition-all"
                  :style="cFeedLt === opt.key
                    ? { background: L.accents.lavender.bold, color: L.inkInverse, border: `1px solid ${L.accents.lavender.bold}` }
                    : { background: L.surfaceRaised, color: L.inkSecondary, border: `1px solid ${L.borderStrong}` }"
                  @click="cFeedLt = opt.key"
                >
                  {{ opt.label }}
                </button>
              </div>
            </div>

            <!-- Time range — md (mint accent, square-ish) -->
            <div>
              <p class="text-xs mb-3" :style="{ color: L.inkTertiary }">Time range selector — md · mint accent</p>
              <div class="flex gap-1.5">
                <button
                  v-for="opt in TIME_OPTIONS"
                  :key="opt.key"
                  type="button"
                  class="seg-c-lt inline-flex items-center justify-center rounded-full w-9 h-8 text-[13px] font-semibold transition-all"
                  :style="cTimeLt === opt.key
                    ? { background: L.accents.mint.bold, color: L.inkInverse, border: `1px solid ${L.accents.mint.bold}` }
                    : { background: L.surfaceRaised, color: L.inkSecondary, border: `1px solid ${L.borderStrong}` }"
                  @click="cTimeLt = opt.key"
                >
                  {{ opt.label }}
                </button>
              </div>
            </div>

            <!-- Time range — sm -->
            <div>
              <p class="text-xs mb-3" :style="{ color: L.inkTertiary }">Time range selector — sm (26px)</p>
              <div class="flex gap-1.5">
                <button
                  v-for="opt in TIME_OPTIONS"
                  :key="opt.key"
                  type="button"
                  class="seg-c-lt inline-flex items-center justify-center rounded-full w-8 h-[26px] text-[12px] font-semibold transition-all"
                  :style="cTimeLt === opt.key
                    ? { background: L.accents.mint.bold, color: L.inkInverse, border: `1px solid ${L.accents.mint.bold}` }
                    : { background: L.surfaceRaised, color: L.inkSecondary, border: `1px solid ${L.borderStrong}` }"
                  @click="cTimeLt = opt.key"
                >
                  {{ opt.label }}
                </button>
              </div>
            </div>

            <!-- Disabled -->
            <div>
              <p class="text-xs mb-3" :style="{ color: L.inkTertiary }">Disabled</p>
              <div class="flex flex-wrap gap-2 opacity-40 cursor-not-allowed">
                <span
                  v-for="opt in FEED_OPTIONS"
                  :key="opt.key"
                  class="inline-flex items-center rounded-full h-8 px-3.5 text-[13px] font-medium"
                  :style="opt.key === 'all'
                    ? { background: L.accents.lavender.bold, color: L.inkInverse, border: `1px solid ${L.accents.lavender.bold}` }
                    : { background: L.surfaceRaised, color: L.inkSecondary, border: `1px solid ${L.borderStrong}` }"
                >{{ opt.label }}</span>
              </div>
            </div>
          </div><!-- /light panel C -->

          <!-- DARK PANEL C -->
          <div class="rounded-2xl border p-6 space-y-8" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

            <!-- Activity feed dark — md with counts -->
            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">Activity feed filter — md (32px) with counts · lavender accent</p>
              <div class="flex flex-wrap gap-2">
                <button
                  v-for="opt in FEED_OPTIONS"
                  :key="opt.key"
                  type="button"
                  class="seg-c-dk inline-flex items-center gap-1.5 rounded-full h-8 px-3.5 text-[13px] font-medium transition-all"
                  :style="cFeedDk === opt.key
                    ? { background: D.accents.lavender.bold, color: D.inkInverse, border: `1px solid ${D.accents.lavender.bold}` }
                    : { background: D.surfaceRaised, color: D.inkSecondary, border: `1px solid ${D.borderStrong}` }"
                  @click="cFeedDk = opt.key"
                >
                  {{ opt.label }}
                  <span
                    class="inline-flex items-center justify-center rounded-full h-4 min-w-[1rem] px-1 text-[10px] font-semibold leading-none"
                    :style="cFeedDk === opt.key
                      ? { background: 'rgba(20,19,17,0.25)', color: D.inkInverse }
                      : { background: D.surfaceSunken, color: D.inkTertiary }"
                  >{{ opt.count }}</span>
                </button>
              </div>
            </div>

            <!-- Activity feed dark — sm no counts -->
            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">Activity feed filter — sm (26px), no counts</p>
              <div class="flex flex-wrap gap-1.5">
                <button
                  v-for="opt in FEED_OPTIONS"
                  :key="opt.key"
                  type="button"
                  class="seg-c-dk inline-flex items-center rounded-full h-[26px] px-3 text-[12px] font-medium transition-all"
                  :style="cFeedDk === opt.key
                    ? { background: D.accents.lavender.bold, color: D.inkInverse, border: `1px solid ${D.accents.lavender.bold}` }
                    : { background: D.surfaceRaised, color: D.inkSecondary, border: `1px solid ${D.borderStrong}` }"
                  @click="cFeedDk = opt.key"
                >
                  {{ opt.label }}
                </button>
              </div>
            </div>

            <!-- Time range dark — md -->
            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">Time range selector — md · mint accent</p>
              <div class="flex gap-1.5">
                <button
                  v-for="opt in TIME_OPTIONS"
                  :key="opt.key"
                  type="button"
                  class="seg-c-dk inline-flex items-center justify-center rounded-full w-9 h-8 text-[13px] font-semibold transition-all"
                  :style="cTimeDk === opt.key
                    ? { background: D.accents.mint.bold, color: D.inkInverse, border: `1px solid ${D.accents.mint.bold}` }
                    : { background: D.surfaceRaised, color: D.inkSecondary, border: `1px solid ${D.borderStrong}` }"
                  @click="cTimeDk = opt.key"
                >
                  {{ opt.label }}
                </button>
              </div>
            </div>

            <!-- Time range dark — sm -->
            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">Time range selector — sm (26px)</p>
              <div class="flex gap-1.5">
                <button
                  v-for="opt in TIME_OPTIONS"
                  :key="opt.key"
                  type="button"
                  class="seg-c-dk inline-flex items-center justify-center rounded-full w-8 h-[26px] text-[12px] font-semibold transition-all"
                  :style="cTimeDk === opt.key
                    ? { background: D.accents.mint.bold, color: D.inkInverse, border: `1px solid ${D.accents.mint.bold}` }
                    : { background: D.surfaceRaised, color: D.inkSecondary, border: `1px solid ${D.borderStrong}` }"
                  @click="cTimeDk = opt.key"
                >
                  {{ opt.label }}
                </button>
              </div>
            </div>

            <!-- Disabled dark -->
            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">Disabled</p>
              <div class="flex flex-wrap gap-2 opacity-40 cursor-not-allowed">
                <span
                  v-for="opt in FEED_OPTIONS"
                  :key="opt.key"
                  class="inline-flex items-center rounded-full h-8 px-3.5 text-[13px] font-medium"
                  :style="opt.key === 'all'
                    ? { background: D.accents.lavender.bold, color: D.inkInverse, border: `1px solid ${D.accents.lavender.bold}` }
                    : { background: D.surfaceRaised, color: D.inkSecondary, border: `1px solid ${D.borderStrong}` }"
                >{{ opt.label }}</span>
              </div>
            </div>
          </div><!-- /dark panel C -->
        </div>
      </VariantFrame>
      <p class="mt-3 text-sm px-1" :style="{ color: L.inkSecondary }">
        Individual pills break the "single control" metaphor in exchange for more expressive use of the accent palette. Well-suited for notification type filters or category selectors where each option deserves its own accent color identity, but slightly heavy for compact time-range pickers.
      </p>
    </section>


    <!-- ═══════════════════════════════════════════════════════════════════
         CLAUDE'S PICK
         ════════════════════════════════════════════════════════════════════ -->
    <section class="mb-8">
      <div
        class="rounded-2xl border p-6 space-y-3"
        :style="{ background: L.accents.lavender.soft, borderColor: L.accents.lavender.bold }"
      >
        <div class="flex items-center gap-2">
          <SparklesIcon class="w-5 h-5" :style="{ color: L.accents.lavender.bold }" />
          <h2 class="text-[15px] font-semibold" :style="{ color: L.accents.lavender.bold }">Claude's pick — Variant B</h2>
        </div>
        <p class="text-[14px] leading-relaxed" :style="{ color: L.inkPrimary }">
          Variant B earns the pick because it delivers a premium, tactile interaction at zero color cost — the monochrome ink indicator translates perfectly between light and dark surfaces without any per-surface palette logic. The sliding animation reinforces the "moving between options" mental model without being distracting, and when <code class="text-[13px] px-1 rounded" :style="{ background: L.accents.lavender.bold + '20' }">prefers-reduced-motion</code> is on, the position update is still immediately clear. On mobile at 320px, four segments fill the container cleanly because each button is flex-weighted equally — no wrapping risk that Variant C's gap approach can suffer.
        </p>
      </div>
    </section>


    <!-- ═══════════════════════════════════════════════════════════════════
         USAGE GUIDE
         ════════════════════════════════════════════════════════════════════ -->
    <section class="mb-8">
      <div
        class="rounded-2xl border p-6 space-y-4"
        :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle }"
      >
        <h2 class="text-[17px] font-semibold" :style="{ color: L.inkPrimary }">When to use which</h2>
        <ul class="space-y-4 text-[14px]" :style="{ color: L.inkSecondary }">
          <li>
            <strong :style="{ color: L.inkPrimary }">Variant A — Filled active with ease-out fill (LOCKED):</strong>
            The Kinhold standard. The active segment's background + text color transition over 220ms with a cubic-bezier ease-out (0.22, 1, 0.36, 1) so the fill settles rather than snaps. The shared outer border communicates "one control" while the fill transition conveys "selection changed." Use everywhere: activity feeds, time-range selectors, leaderboard periods, notification filters. Respects <code>prefers-reduced-motion</code> via CSS media query — the transition is suppressed entirely when the user has reduced-motion enabled.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Variant B — Sliding pill (reference):</strong>
            iOS-style sliding background indicator. Demonstrated for comparison but not chosen — the separate slider element adds DOM complexity without clear benefit over the locked Variant A for Kinhold's use cases.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Variant C — Individual pills (reference):</strong>
            Gapped individual pills. Demonstrated for comparison but not chosen — the gapped row feels like "multiple independent chips" rather than "one segmented control."
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Count badges:</strong>
            Use sparingly — only on the "All" + first-level filters where the count genuinely changes a decision (e.g., "I won't bother looking if there are 0 failures"). Hide when all counts are zero.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Sizes:</strong>
            md (32px) for top-of-section filters; sm (26px) for inline-row or secondary filters inside a card. Never mix sizes within one control.
          </li>
        </ul>
      </div>
    </section>

    <!-- ══════════════════════════════════════════════════════════════════════
         KIN COMPONENT PREVIEW — Variant A's look + B's sliding motion
         ═══════════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="Kin" caption="KinSegmentedFilter — outlined container · ink-filled active pill that *slides* between options. forceMotion is on so you can see the slide regardless of OS reduced-motion preference.">
        <div class="w-full space-y-10">
          <!-- LIGHT PANEL -->
          <div
            class="rounded-2xl border p-6 space-y-6"
            :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }"
          >
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>

            <div class="space-y-2">
              <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Feed filter — md · with counts</p>
              <KinSegmentedFilter v-model:active-key="kinFeedL" :options="FEED_OPTIONS" force-motion />
            </div>

            <div class="space-y-2">
              <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Time range — sm · compact</p>
              <KinSegmentedFilter v-model:active-key="kinTimeL" :options="TIME_OPTIONS" size="sm" force-motion />
            </div>

            <div class="space-y-2">
              <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Disabled</p>
              <KinSegmentedFilter :options="FEED_OPTIONS" :active-key="kinFeedL" disabled />
            </div>
          </div>

          <!-- DARK PANEL -->
          <div
            class="dark rounded-2xl border p-6 space-y-6"
            :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }"
          >
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

            <div class="space-y-2">
              <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Feed filter — md · with counts</p>
              <KinSegmentedFilter v-model:active-key="kinFeedD" :options="FEED_OPTIONS" force-motion />
            </div>

            <div class="space-y-2">
              <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Time range — sm · compact</p>
              <KinSegmentedFilter v-model:active-key="kinTimeD" :options="TIME_OPTIONS" size="sm" force-motion />
            </div>
          </div>
        </div>
      </VariantFrame>
    </section>
  </ComponentPage>
</template>

<style scoped>
/*
  ─────────────────────────────────────────────────────────────────
  VARIANT A — LOCKED treatment
  Ease-out fill transition: the active pill's background + text color
  transition over 220ms with a cubic-bezier ease-out curve so the
  fill settles in smoothly rather than snapping. Matches the rest of
  Kinhold's "purposeful motion" language (fast, deliberate, settled).
  ─────────────────────────────────────────────────────────────────
*/
.seg-a-lt {
  transition:
    background-color 220ms cubic-bezier(0.22, 1, 0.36, 1),
    color 220ms cubic-bezier(0.22, 1, 0.36, 1);
}
.seg-a-lt:hover { filter: brightness(0.96); }
.seg-a-dk {
  transition:
    background-color 220ms cubic-bezier(0.22, 1, 0.36, 1),
    color 220ms cubic-bezier(0.22, 1, 0.36, 1);
}
.seg-a-dk:hover { filter: brightness(1.1); }

/*
  ─────────────────────────────────────────────────────────────────
  VARIANT B — sliding indicator
  The position is driven by inline :style transform computed in JS.
  We just declare the transition here so it picks it up automatically.
  ─────────────────────────────────────────────────────────────────
*/
.b-slider {
  transition: transform 200ms cubic-bezier(0.2, 0.8, 0.2, 1);
}

.seg-b-lt { transition: color 150ms; }
.seg-b-lt:hover { opacity: 0.85; }
.seg-b-dk { transition: color 150ms; }
.seg-b-dk:hover { opacity: 0.85; }

/*
  ─────────────────────────────────────────────────────────────────
  VARIANT C — individual pill hover
  ─────────────────────────────────────────────────────────────────
*/
.seg-c-lt { transition: background-color 150ms, border-color 150ms, color 150ms; }
.seg-c-lt:hover { filter: brightness(0.97); }
.seg-c-dk { transition: background-color 150ms, border-color 150ms, color 150ms; }
.seg-c-dk:hover { filter: brightness(1.1); }

/*
  ─────────────────────────────────────────────────────────────────
  REDUCED MOTION — suppress the slider transform; position still
  jumps instantly which is perfectly clear for navigation.
  ─────────────────────────────────────────────────────────────────
*/
@media (prefers-reduced-motion: reduce) {
  .b-slider {
    transition: none;
  }
  .seg-a-lt,
  .seg-a-dk,
  .seg-b-lt,
  .seg-b-dk,
  .seg-c-lt,
  .seg-c-dk {
    transition: none;
  }
}
</style>
