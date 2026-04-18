<script setup>
import { ref } from 'vue'
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import {
  MagnifyingGlassIcon,
  BellIcon,
  SunIcon,
  MoonIcon,
} from '@heroicons/vue/24/outline'

// ── Dark-mode hex constants ──────────────────────────────────────────────────
const D = {
  surfaceApp:    '#141311',
  surfaceRaised: '#1C1B19',
  surfaceSunken: '#161513',
  inkPrimary:    '#F0EDE9',
  inkSecondary:  '#A09C97',
  inkTertiary:   '#6E6B67',
  borderSubtle:  '#2C2A27',
  borderStrong:  '#403E3A',
  accentLavenderSoft: '#2D2840',
  accentLavenderBold: '#B6A8E6',
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
  borderStrong:  '#BCB8B2',
  accentLavenderSoft: '#EDE9F9',
  accentLavenderBold: '#6856B2',
}

// ── Shadow values ─────────────────────────────────────────────────────────────
const SHADOW_RESTING_LT = '0 1px 2px rgba(28, 20, 10, 0.04), 0 2px 6px rgba(28, 20, 10, 0.05)'
const SHADOW_RESTING_DK = '0 1px 2px rgba(0, 0, 0, 0.30), 0 2px 6px rgba(0, 0, 0, 0.25)'

// ── Nav data ──────────────────────────────────────────────────────────────────
const NAV_ITEMS = [
  { key: 'today',     label: 'Today' },
  { key: 'plan',      label: 'Plan' },
  { key: 'family',    label: 'Family' },
  { key: 'store',     label: 'Store' },
  { key: 'assistant', label: 'Assistant' },
  { key: 'settings',  label: 'Settings' },
]

// For interactive demo — track which item is "active" per variant/mode
const activeA_lt = ref('today')
const activeA_dk = ref('today')
const activeB_lt = ref('today')
const activeB_dk = ref('today')
const activeC_lt = ref('today')
const activeC_dk = ref('today')
</script>

<template>
  <ComponentPage
    title="3.1 Top nav (desktop)"
    description="Left-aligned brand + nav pills, full search pill in the middle, utility cluster right. Bar uses a glass (backdrop-blur) treatment — one of the four allowed glass surfaces per tenet #7."
    status="chosen"
  >





    <!-- ══════════════════════════════════════════════════════════════
         VARIANT C — Glass treatment (same layout as B, glass surface)
         Translucent + backdrop-blur + inner top highlight + shadow
         One of four allowed glass surfaces per tenet #7
         ══════════════════════════════════════════════════════════════ -->
    <VariantFrame label="Top nav" caption="Left-aligned brand + nav pills, search in the middle, utility right. Glass (backdrop-blur) surface — floats above content like a sticky header.">
      <div class="w-full space-y-10">

        <!-- LIGHT PANEL C -->
        <div class="rounded-2xl border p-6 space-y-8" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
          <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>

          <!-- 1. Full-width C light -->
          <div>
            <p class="text-[11px] mb-3 font-medium" :style="{ color: L.inkTertiary }">Full bar — glass surface, click any item to toggle active</p>
            <div class="overflow-x-auto">
              <div class="tn-c-lt flex items-center h-16 px-5 gap-3 min-w-[1000px] rounded-2xl">
                <!-- Brand mark -->
                <div class="flex items-center gap-2 shrink-0">
                  <div class="w-7 h-7 rounded-full flex items-center justify-center text-[11px] font-bold"
                       :style="{ background: L.accentLavenderBold, color: '#fff' }">K</div>
                  <span class="font-heading text-[15px] font-semibold tracking-tight" :style="{ color: L.inkPrimary }">kinhold</span>
                </div>

                <!-- Left nav pills on glass -->
                <nav class="flex items-center gap-1 shrink-0">
                  <button
                    v-for="item in NAV_ITEMS"
                    :key="item.key"
                    class="tn-c-lt-item rounded-full px-4 py-1.5 text-[13px] font-medium transition-all"
                    :style="activeC_lt === item.key
                      ? { background: L.inkPrimary, color: L.surfaceRaised }
                      : { color: L.inkSecondary }"
                    @click="activeC_lt = item.key"
                  >{{ item.label }}</button>
                </nav>

                <!-- Search pill on glass -->
                <div class="flex-1 flex justify-center px-4">
                  <div class="flex items-center gap-2 rounded-full px-4 h-9 max-w-sm w-full"
                       :style="{ background: 'rgba(245,242,238,0.70)', backdropFilter: 'blur(8px)', border: `1px solid rgba(255,255,255,0.60)` }">
                    <MagnifyingGlassIcon class="w-4 h-4 shrink-0" :style="{ color: L.inkTertiary }" />
                    <span class="text-[13px]" :style="{ color: L.inkTertiary }">Search Kinhold...</span>
                  </div>
                </div>

                <!-- Utility -->
                <div class="flex items-center gap-1 shrink-0">
                  <button class="tn-c-lt-util rounded-full w-10 h-10 flex items-center justify-center" :style="{ color: L.inkSecondary }">
                    <SunIcon class="w-5 h-5" />
                  </button>
                  <button class="tn-c-lt-util rounded-full w-10 h-10 flex items-center justify-center relative" :style="{ color: L.inkSecondary }">
                    <BellIcon class="w-5 h-5" />
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-red-500 border-2 border-white"></span>
                  </button>
                  <div class="w-8 h-8 rounded-full flex items-center justify-center text-[12px] font-bold ml-1"
                       :style="{ background: L.accentLavenderSoft, color: L.accentLavenderBold }">GQ</div>
                </div>
              </div>
            </div>
          </div>

          <!-- 2. Hover state C light -->
          <div>
            <p class="text-[11px] mb-3 font-medium" :style="{ color: L.inkTertiary }">Hover state on glass</p>
            <div class="flex justify-start">
              <nav class="tn-c-lt flex items-center gap-1 px-3 py-2 rounded-2xl">
                <button class="rounded-full px-4 py-1.5 text-[13px] font-medium"
                        :style="{ background: L.inkPrimary, color: L.surfaceRaised }">Today</button>
                <button class="tn-c-lt-item rounded-full px-4 py-1.5 text-[13px] font-medium"
                        :style="{ color: L.inkSecondary }">Plan</button>
                <button class="rounded-full px-4 py-1.5 text-[13px] font-medium"
                        :style="{ background: 'rgba(245,242,238,0.80)', color: L.inkPrimary }">Family</button>
                <button class="tn-c-lt-item rounded-full px-4 py-1.5 text-[13px] font-medium"
                        :style="{ color: L.inkSecondary }">Store</button>
              </nav>
            </div>
          </div>

          <!-- 3. Active on 3 tabs C light -->
          <div>
            <p class="text-[11px] mb-3 font-medium" :style="{ color: L.inkTertiary }">Active indicator on glass surface</p>
            <div class="space-y-3">
              <div v-for="activeKey in ['today', 'family', 'assistant']" :key="activeKey"
                   class="overflow-x-auto">
                <div class="tn-c-lt flex items-center gap-1 px-3 py-2 rounded-2xl min-w-max">
                  <button
                    v-for="item in NAV_ITEMS"
                    :key="item.key"
                    class="rounded-full px-4 py-1.5 text-[13px] font-medium"
                    :style="activeKey === item.key
                      ? { background: L.inkPrimary, color: L.surfaceRaised }
                      : { color: L.inkSecondary }"
                  >{{ item.label }}</button>
                </div>
              </div>
            </div>
          </div>

          <!-- 4. Glass-over-content demo (light) — unique to C -->
          <div>
            <p class="text-[11px] mb-3 font-medium" :style="{ color: L.inkTertiary }">
              Glass-over-content — blur is visible against the gradient section below the nav
              <span class="italic">(in production: sticky nav floats above scrolling page content)</span>
            </p>
            <div class="overflow-x-auto rounded-2xl overflow-hidden border" :style="{ borderColor: L.borderSubtle }">
              <div class="relative min-w-[1000px]">
                <!-- Fake page content behind the nav -->
                <div class="absolute inset-0"
                     style="background: linear-gradient(135deg, #EDE9F9 0%, #D4EFE4 35%, #FFF4C2 70%, #F9E0D6 100%);">
                  <!-- Some fake text/content so the blur is visible -->
                  <div class="pt-20 px-8 space-y-3 opacity-60">
                    <div class="h-8 rounded-full w-64" style="background: rgba(104,86,178,0.25)"></div>
                    <div class="h-4 rounded-full w-48" style="background: rgba(104,86,178,0.15)"></div>
                    <div class="flex gap-3 mt-6">
                      <div class="h-32 w-48 rounded-2xl" style="background: rgba(255,255,255,0.40)"></div>
                      <div class="h-32 w-48 rounded-2xl" style="background: rgba(255,255,255,0.40)"></div>
                      <div class="h-32 w-48 rounded-2xl" style="background: rgba(255,255,255,0.40)"></div>
                    </div>
                  </div>
                </div>
                <!-- Glass nav overlaid -->
                <div class="relative tn-c-lt flex items-center h-16 px-5 gap-3">
                  <div class="flex items-center gap-2 shrink-0">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-[11px] font-bold"
                         :style="{ background: L.accentLavenderBold, color: '#fff' }">K</div>
                    <span class="font-heading text-[15px] font-semibold tracking-tight" :style="{ color: L.inkPrimary }">kinhold</span>
                  </div>
                  <nav class="flex items-center gap-1 shrink-0">
                    <button
                      v-for="item in NAV_ITEMS"
                      :key="item.key"
                      class="rounded-full px-4 py-1.5 text-[13px] font-medium"
                      :style="item.key === 'today'
                        ? { background: L.inkPrimary, color: L.surfaceRaised }
                        : { color: L.inkSecondary }"
                    >{{ item.label }}</button>
                  </nav>
                  <div class="flex-1 flex justify-center px-4">
                    <div class="flex items-center gap-2 rounded-full px-4 h-9 max-w-sm w-full"
                         style="background: rgba(245,242,238,0.70); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.60);">
                      <MagnifyingGlassIcon class="w-4 h-4 shrink-0" :style="{ color: L.inkTertiary }" />
                      <span class="text-[13px]" :style="{ color: L.inkTertiary }">Search Kinhold...</span>
                    </div>
                  </div>
                  <div class="flex items-center gap-1 shrink-0">
                    <button class="tn-c-lt-util rounded-full w-10 h-10 flex items-center justify-center" :style="{ color: L.inkSecondary }">
                      <SunIcon class="w-5 h-5" />
                    </button>
                    <button class="tn-c-lt-util rounded-full w-10 h-10 flex items-center justify-center relative" :style="{ color: L.inkSecondary }">
                      <BellIcon class="w-5 h-5" />
                      <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-red-500 border-2 border-white"></span>
                    </button>
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-[12px] font-bold ml-1"
                         :style="{ background: L.accentLavenderSoft, color: L.accentLavenderBold }">GQ</div>
                  </div>
                </div>
                <!-- Spacer so panel has height -->
                <div class="h-40"></div>
              </div>
            </div>
          </div>

        </div><!-- /light panel C -->

        <!-- DARK PANEL C -->
        <div class="rounded-2xl border p-6 space-y-8" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
          <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

          <!-- 1. Full-width C dark -->
          <div>
            <p class="text-[11px] mb-3 font-medium" :style="{ color: D.inkTertiary }">Full bar — dark glass, click any item to toggle active</p>
            <div class="overflow-x-auto">
              <div class="tn-c-dk flex items-center h-16 px-5 gap-3 min-w-[1000px] rounded-2xl"
                   :style="{ boxShadow: SHADOW_RESTING_DK }">
                <!-- Brand mark dark -->
                <div class="flex items-center gap-2 shrink-0">
                  <div class="w-7 h-7 rounded-full flex items-center justify-center text-[11px] font-bold"
                       :style="{ background: D.accentLavenderBold, color: '#141311' }">K</div>
                  <span class="font-heading text-[15px] font-semibold tracking-tight" :style="{ color: D.inkPrimary }">kinhold</span>
                </div>

                <!-- Nav pills dark glass -->
                <nav class="flex items-center gap-1 shrink-0">
                  <button
                    v-for="item in NAV_ITEMS"
                    :key="item.key"
                    class="tn-c-dk-item rounded-full px-4 py-1.5 text-[13px] font-medium"
                    :class="activeC_dk === item.key ? 'tn-c-dk-item--active' : ''"
                    @click="activeC_dk = item.key"
                  >{{ item.label }}</button>
                </nav>

                <!-- Search pill dark glass -->
                <div class="flex-1 flex justify-center px-4">
                  <div class="flex items-center gap-2 rounded-full px-4 h-9 max-w-sm w-full"
                       style="background: rgba(20,19,17,0.50); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.08);">
                    <MagnifyingGlassIcon class="w-4 h-4 shrink-0" :style="{ color: D.inkTertiary }" />
                    <span class="text-[13px]" :style="{ color: D.inkTertiary }">Search Kinhold...</span>
                  </div>
                </div>

                <!-- Utility dark -->
                <div class="flex items-center gap-1 shrink-0">
                  <button class="tn-c-dk-util rounded-full w-10 h-10 flex items-center justify-center">
                    <MoonIcon class="w-5 h-5" />
                  </button>
                  <button class="tn-c-dk-util rounded-full w-10 h-10 flex items-center justify-center relative">
                    <BellIcon class="w-5 h-5" />
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-red-500" style="border: 2px solid rgba(28,27,25,0.75);"></span>
                  </button>
                  <div class="w-8 h-8 rounded-full flex items-center justify-center text-[12px] font-bold ml-1"
                       :style="{ background: D.accentLavenderSoft, color: D.accentLavenderBold }">GQ</div>
                </div>
              </div>
            </div>
          </div>

          <!-- 2. Hover state C dark -->
          <div>
            <p class="text-[11px] mb-3 font-medium" :style="{ color: D.inkTertiary }">Hover state on dark glass</p>
            <div class="flex justify-start">
              <nav class="tn-c-dk flex items-center gap-1 px-3 py-2 rounded-2xl">
                <button class="tn-c-dk-item tn-c-dk-item--active rounded-full px-4 py-1.5 text-[13px] font-medium">Today</button>
                <button class="tn-c-dk-item rounded-full px-4 py-1.5 text-[13px] font-medium">Plan</button>
                <button class="rounded-full px-4 py-1.5 text-[13px] font-medium" style="background: #242220; color: #F0EDE9;">Family</button>
                <button class="tn-c-dk-item rounded-full px-4 py-1.5 text-[13px] font-medium">Store</button>
              </nav>
            </div>
          </div>

          <!-- 3. Active on 3 tabs C dark -->
          <div>
            <p class="text-[11px] mb-3 font-medium" :style="{ color: D.inkTertiary }">Active indicator on dark glass</p>
            <div class="space-y-3">
              <div v-for="activeKey in ['today', 'family', 'assistant']" :key="activeKey"
                   class="overflow-x-auto">
                <div class="tn-c-dk flex items-center gap-1 px-3 py-2 rounded-2xl min-w-max">
                  <button
                    v-for="item in NAV_ITEMS"
                    :key="item.key"
                    class="rounded-full px-4 py-1.5 text-[13px] font-medium"
                    :class="activeKey === item.key ? 'tn-c-dk-item--active' : 'tn-c-dk-item'"
                  >{{ item.label }}</button>
                </div>
              </div>
            </div>
          </div>

          <!-- 4. Glass-over-content demo (dark) — unique to C -->
          <div>
            <p class="text-[11px] mb-3 font-medium" :style="{ color: D.inkTertiary }">
              Glass-over-content — blur is visible against the dark gradient below
              <span class="italic">(in production: sticky nav floats above scrolling page content)</span>
            </p>
            <div class="overflow-x-auto rounded-2xl overflow-hidden border" :style="{ borderColor: D.borderSubtle }">
              <div class="relative min-w-[1000px]">
                <!-- Fake dark page content behind nav -->
                <div class="absolute inset-0"
                     style="background: linear-gradient(135deg, #1A1630 0%, #162418 35%, #2D2010 70%, #1A0E18 100%);">
                  <div class="pt-20 px-8 space-y-3 opacity-50">
                    <div class="h-8 rounded-full w-64" style="background: rgba(182,168,230,0.25)"></div>
                    <div class="h-4 rounded-full w-48" style="background: rgba(182,168,230,0.15)"></div>
                    <div class="flex gap-3 mt-6">
                      <div class="h-32 w-48 rounded-2xl" style="background: rgba(255,255,255,0.06)"></div>
                      <div class="h-32 w-48 rounded-2xl" style="background: rgba(255,255,255,0.06)"></div>
                      <div class="h-32 w-48 rounded-2xl" style="background: rgba(255,255,255,0.06)"></div>
                    </div>
                  </div>
                </div>
                <!-- Dark glass nav overlaid -->
                <div class="relative tn-c-dk flex items-center h-16 px-5 gap-3">
                  <div class="flex items-center gap-2 shrink-0">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-[11px] font-bold"
                         :style="{ background: D.accentLavenderBold, color: '#141311' }">K</div>
                    <span class="font-heading text-[15px] font-semibold tracking-tight" :style="{ color: D.inkPrimary }">kinhold</span>
                  </div>
                  <nav class="flex items-center gap-1 shrink-0">
                    <button
                      v-for="item in NAV_ITEMS"
                      :key="item.key"
                      class="rounded-full px-4 py-1.5 text-[13px] font-medium"
                      :class="item.key === 'today' ? 'tn-c-dk-item--active' : 'tn-c-dk-item'"
                    >{{ item.label }}</button>
                  </nav>
                  <div class="flex-1 flex justify-center px-4">
                    <div class="flex items-center gap-2 rounded-full px-4 h-9 max-w-sm w-full"
                         style="background: rgba(20,19,17,0.50); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.08);">
                      <MagnifyingGlassIcon class="w-4 h-4 shrink-0" :style="{ color: D.inkTertiary }" />
                      <span class="text-[13px]" :style="{ color: D.inkTertiary }">Search Kinhold...</span>
                    </div>
                  </div>
                  <div class="flex items-center gap-1 shrink-0">
                    <button class="tn-c-dk-util rounded-full w-10 h-10 flex items-center justify-center">
                      <MoonIcon class="w-5 h-5" />
                    </button>
                    <button class="tn-c-dk-util rounded-full w-10 h-10 flex items-center justify-center relative">
                      <BellIcon class="w-5 h-5" />
                      <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-red-500" style="border: 2px solid rgba(28,27,25,0.75);"></span>
                    </button>
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-[12px] font-bold ml-1"
                         :style="{ background: D.accentLavenderSoft, color: D.accentLavenderBold }">GQ</div>
                  </div>
                </div>
                <!-- Spacer -->
                <div class="h-40"></div>
              </div>
            </div>
          </div>

        </div><!-- /dark panel C -->

      </div>
    </VariantFrame>



  </ComponentPage>
</template>

<style scoped>

/*
  ═══════════════════════════════════════════════════════════════════
  VARIANT C — LIGHT GLASS
  One of four allowed glass surfaces (tenet #7)
  ═══════════════════════════════════════════════════════════════════
*/
.tn-c-lt {
  background: rgba(255, 255, 255, 0.72);
  backdrop-filter: blur(16px) saturate(140%);
  -webkit-backdrop-filter: blur(16px) saturate(140%);
  border-bottom: 1px solid rgba(255, 255, 255, 0.65);
  box-shadow: 0 1px 2px rgba(28, 20, 10, 0.04), 0 2px 6px rgba(28, 20, 10, 0.05),
              inset 0 1px 0 rgba(255, 255, 255, 0.85);
}
.tn-c-lt-item {
  color: #6B6966;
  transition: background-color 200ms, color 200ms;
}
.tn-c-lt-item:hover {
  color: #1C1C1E;
  background: rgba(245, 242, 238, 0.80);
}
.tn-c-lt-util {
  color: #6B6966;
  transition: background-color 200ms, color 200ms;
}
.tn-c-lt-util:hover {
  background: rgba(245, 242, 238, 0.80);
  color: #1C1C1E;
}

/*
  ═══════════════════════════════════════════════════════════════════
  VARIANT C — DARK GLASS
  Translucent warm-dark + strong blur + inner highlight
  ═══════════════════════════════════════════════════════════════════
*/
.tn-c-dk {
  background: rgba(28, 27, 25, 0.75);
  backdrop-filter: blur(18px) saturate(130%);
  -webkit-backdrop-filter: blur(18px) saturate(130%);
  border-bottom: 1px solid rgba(255, 255, 255, 0.08);
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.30), 0 2px 6px rgba(0, 0, 0, 0.25),
              inset 0 1px 0 rgba(255, 255, 255, 0.06);
}
.tn-c-dk-item {
  color: #A09C97;
  transition: background-color 200ms, color 200ms;
}
.tn-c-dk-item:hover {
  color: #F0EDE9;
  background: rgba(36, 34, 32, 0.80);
}
.tn-c-dk-item--active {
  background: #F0EDE9;
  color: #141311;
}
.tn-c-dk-util {
  color: #A09C97;
  transition: background-color 200ms, color 200ms;
}
.tn-c-dk-util:hover {
  background: rgba(36, 34, 32, 0.80);
  color: #F0EDE9;
}

/*
  ═══════════════════════════════════════════════════════════════════
  REDUCED MOTION — collapse motion, keep hover color changes
  Greg has reduced-motion preference — all transitions are 200ms
  already (quick not instant) but transforms are suppressed here.
  ═══════════════════════════════════════════════════════════════════
*/
@media (prefers-reduced-motion: reduce) {
  .tn-a-lt-item,
  .tn-a-dk-item,
  .tn-b-lt-item,
  .tn-b-dk-item,
  .tn-c-lt-item,
  .tn-c-dk-item,
  .tn-a-lt-util,
  .tn-a-dk-util,
  .tn-b-lt-util,
  .tn-b-dk-util,
  .tn-c-lt-util,
  .tn-c-dk-util {
    transition: none;
  }
}
</style>
