<script setup>
import { ref } from 'vue'
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import KinTopNav from '@/components/design-system/KinTopNav.vue'
import {
  MagnifyingGlassIcon,
  BellIcon,
  SunIcon,
  MoonIcon,
} from '@heroicons/vue/24/outline'

// ── Panel chrome tokens (Light/Dark hand-rolled wrappers) ────────────────────
const L = {
  surfaceApp:    '#FAF8F5',
  surfaceRaised: '#FFFFFF',
  surfaceSunken: '#F5F2EE',
  inkPrimary:    '#1C1C1E',
  inkSecondary:  '#6B6966',
  inkTertiary:   '#9C9895',
  borderSubtle:  '#E8E4DF',
  accentLavenderSoft: '#EDE9F9',
  accentLavenderBold: '#6856B2',
}
const D = {
  surfaceApp:    '#141311',
  inkPrimary:    '#F0EDE9',
  inkSecondary:  '#A09C97',
  inkTertiary:   '#6E6B67',
  borderSubtle:  '#2C2A27',
  accentLavenderSoft: '#2D2840',
  accentLavenderBold: '#B6A8E6',
}

const NAV_ITEMS = [
  { key: 'today',     label: 'Today' },
  { key: 'plan',      label: 'Plan' },
  { key: 'family',    label: 'Family' },
  { key: 'store',     label: 'Store' },
  { key: 'assistant', label: 'Assistant' },
  { key: 'settings',  label: 'Settings' },
]

// Interactive: track which item is active per panel so both panels toggle independently.
const activeLight = ref('today')
const activeDark  = ref('today')
</script>

<template>
  <ComponentPage
    title="3.1 Top nav (desktop)"
    description="Left-aligned brand + nav pills, full search pill in the middle, utility cluster right. Bar uses a glass (backdrop-blur) treatment — one of the four allowed glass surfaces per tenet #7."
    status="chosen"
  >

    <VariantFrame label="Top nav" caption="Left-aligned brand + nav pills, search in the middle, utility right. Glass (backdrop-blur) surface — floats above content like a sticky header.">
      <div class="w-full space-y-10">

        <!-- ══════════════ LIGHT PANEL ══════════════ -->
        <div class="rounded-2xl border p-6 space-y-8" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
          <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>

          <!-- 1. Full-width, interactive -->
          <div>
            <p class="text-[11px] mb-3 font-medium" :style="{ color: L.inkTertiary }">Full bar — click any item to toggle active</p>
            <div class="overflow-x-auto">
              <div class="min-w-[1000px]">
                <KinTopNav
                  brand="kinhold"
                  :items="NAV_ITEMS"
                  :active-key="activeLight"
                  @item-click="(key) => activeLight = key"
                >
                  <template #search>
                    <div class="flex items-center gap-2 rounded-full px-4 h-9 max-w-sm w-full"
                         style="background: rgba(245,242,238,0.70); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.60);">
                      <MagnifyingGlassIcon class="w-4 h-4 shrink-0" :style="{ color: L.inkTertiary }" />
                      <span class="text-[13px]" :style="{ color: L.inkTertiary }">Search Kinhold...</span>
                    </div>
                  </template>
                  <template #utility>
                    <button class="kin-util rounded-full w-10 h-10 flex items-center justify-center">
                      <SunIcon class="w-5 h-5" />
                    </button>
                    <button class="kin-util rounded-full w-10 h-10 flex items-center justify-center relative">
                      <BellIcon class="w-5 h-5" />
                      <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-red-500 border-2 border-white"></span>
                    </button>
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-[12px] font-bold ml-1"
                         :style="{ background: L.accentLavenderSoft, color: L.accentLavenderBold }">GQ</div>
                  </template>
                </KinTopNav>
              </div>
            </div>
          </div>

          <!-- 2. Active on 3 tabs (static previews) -->
          <div>
            <p class="text-[11px] mb-3 font-medium" :style="{ color: L.inkTertiary }">Active indicator on glass surface</p>
            <div class="space-y-3">
              <div v-for="activeKey in ['today', 'family', 'assistant']" :key="activeKey"
                   class="overflow-x-auto">
                <div class="min-w-max">
                  <KinTopNav
                    brand="kinhold"
                    :items="NAV_ITEMS"
                    :active-key="activeKey"
                  >
                    <template #utility>
                      <div class="w-8 h-8 rounded-full flex items-center justify-center text-[12px] font-bold"
                           :style="{ background: L.accentLavenderSoft, color: L.accentLavenderBold }">GQ</div>
                    </template>
                  </KinTopNav>
                </div>
              </div>
            </div>
          </div>

          <!-- 3. Glass-over-content demo (light) -->
          <div>
            <p class="text-[11px] mb-3 font-medium" :style="{ color: L.inkTertiary }">
              Glass-over-content — blur is visible against the gradient section below the nav
              <span class="italic">(in production: sticky nav floats above scrolling page content)</span>
            </p>
            <div class="overflow-x-auto rounded-2xl overflow-hidden border" :style="{ borderColor: L.borderSubtle }">
              <div class="relative min-w-[1000px]">
                <div class="absolute inset-0"
                     style="background: linear-gradient(135deg, #EDE9F9 0%, #D4EFE4 35%, #FFF4C2 70%, #F9E0D6 100%);">
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
                <div class="relative">
                  <KinTopNav
                    brand="kinhold"
                    :items="NAV_ITEMS"
                    active-key="today"
                  >
                    <template #search>
                      <div class="flex items-center gap-2 rounded-full px-4 h-9 max-w-sm w-full"
                           style="background: rgba(245,242,238,0.70); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.60);">
                        <MagnifyingGlassIcon class="w-4 h-4 shrink-0" :style="{ color: L.inkTertiary }" />
                        <span class="text-[13px]" :style="{ color: L.inkTertiary }">Search Kinhold...</span>
                      </div>
                    </template>
                    <template #utility>
                      <button class="kin-util rounded-full w-10 h-10 flex items-center justify-center">
                        <SunIcon class="w-5 h-5" />
                      </button>
                      <button class="kin-util rounded-full w-10 h-10 flex items-center justify-center relative">
                        <BellIcon class="w-5 h-5" />
                        <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-red-500 border-2 border-white"></span>
                      </button>
                      <div class="w-8 h-8 rounded-full flex items-center justify-center text-[12px] font-bold ml-1"
                           :style="{ background: L.accentLavenderSoft, color: L.accentLavenderBold }">GQ</div>
                    </template>
                  </KinTopNav>
                </div>
                <div class="h-40"></div>
              </div>
            </div>
          </div>

        </div><!-- /light panel -->

        <!-- ══════════════ DARK PANEL ══════════════ -->
        <div class="dark rounded-2xl border p-6 space-y-8" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
          <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

          <!-- 1. Full-width, interactive -->
          <div>
            <p class="text-[11px] mb-3 font-medium" :style="{ color: D.inkTertiary }">Full bar — click any item to toggle active</p>
            <div class="overflow-x-auto">
              <div class="min-w-[1000px]">
                <KinTopNav
                  brand="kinhold"
                  :items="NAV_ITEMS"
                  :active-key="activeDark"
                  @item-click="(key) => activeDark = key"
                >
                  <template #search>
                    <div class="flex items-center gap-2 rounded-full px-4 h-9 max-w-sm w-full"
                         style="background: rgba(20,19,17,0.50); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.08);">
                      <MagnifyingGlassIcon class="w-4 h-4 shrink-0" :style="{ color: D.inkTertiary }" />
                      <span class="text-[13px]" :style="{ color: D.inkTertiary }">Search Kinhold...</span>
                    </div>
                  </template>
                  <template #utility>
                    <button class="kin-util rounded-full w-10 h-10 flex items-center justify-center">
                      <MoonIcon class="w-5 h-5" />
                    </button>
                    <button class="kin-util rounded-full w-10 h-10 flex items-center justify-center relative">
                      <BellIcon class="w-5 h-5" />
                      <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-red-500" style="border: 2px solid rgba(28,27,25,0.75);"></span>
                    </button>
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-[12px] font-bold ml-1"
                         :style="{ background: D.accentLavenderSoft, color: D.accentLavenderBold }">GQ</div>
                  </template>
                </KinTopNav>
              </div>
            </div>
          </div>

          <!-- 2. Active on 3 tabs (static previews) -->
          <div>
            <p class="text-[11px] mb-3 font-medium" :style="{ color: D.inkTertiary }">Active indicator on dark glass</p>
            <div class="space-y-3">
              <div v-for="activeKey in ['today', 'family', 'assistant']" :key="activeKey"
                   class="overflow-x-auto">
                <div class="min-w-max">
                  <KinTopNav
                    brand="kinhold"
                    :items="NAV_ITEMS"
                    :active-key="activeKey"
                  >
                    <template #utility>
                      <div class="w-8 h-8 rounded-full flex items-center justify-center text-[12px] font-bold"
                           :style="{ background: D.accentLavenderSoft, color: D.accentLavenderBold }">GQ</div>
                    </template>
                  </KinTopNav>
                </div>
              </div>
            </div>
          </div>

          <!-- 3. Glass-over-content demo (dark) -->
          <div>
            <p class="text-[11px] mb-3 font-medium" :style="{ color: D.inkTertiary }">
              Glass-over-content — blur is visible against the dark gradient below
              <span class="italic">(in production: sticky nav floats above scrolling page content)</span>
            </p>
            <div class="overflow-x-auto rounded-2xl overflow-hidden border" :style="{ borderColor: D.borderSubtle }">
              <div class="relative min-w-[1000px]">
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
                <div class="relative">
                  <KinTopNav
                    brand="kinhold"
                    :items="NAV_ITEMS"
                    active-key="today"
                  >
                    <template #search>
                      <div class="flex items-center gap-2 rounded-full px-4 h-9 max-w-sm w-full"
                           style="background: rgba(20,19,17,0.50); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.08);">
                        <MagnifyingGlassIcon class="w-4 h-4 shrink-0" :style="{ color: D.inkTertiary }" />
                        <span class="text-[13px]" :style="{ color: D.inkTertiary }">Search Kinhold...</span>
                      </div>
                    </template>
                    <template #utility>
                      <button class="kin-util rounded-full w-10 h-10 flex items-center justify-center">
                        <MoonIcon class="w-5 h-5" />
                      </button>
                      <button class="kin-util rounded-full w-10 h-10 flex items-center justify-center relative">
                        <BellIcon class="w-5 h-5" />
                        <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-red-500" style="border: 2px solid rgba(28,27,25,0.75);"></span>
                      </button>
                      <div class="w-8 h-8 rounded-full flex items-center justify-center text-[12px] font-bold ml-1"
                           :style="{ background: D.accentLavenderSoft, color: D.accentLavenderBold }">GQ</div>
                    </template>
                  </KinTopNav>
                </div>
                <div class="h-40"></div>
              </div>
            </div>
          </div>

        </div><!-- /dark panel -->

      </div>
    </VariantFrame>

  </ComponentPage>
</template>

<style scoped>
/* Utility icon buttons inside #utility slot — hover treatment matches nav pills.
   Lives in the demo page because consumers may want different utility styling
   (KinButton icon-only is another valid choice). */
.kin-util {
  color: rgb(var(--ink-secondary));
  background: transparent;
  border: none;
  cursor: pointer;
  transition: background-color 200ms, color 200ms;
}
.kin-util:hover {
  color: rgb(var(--ink-primary));
  background: rgb(var(--surface-sunken) / 0.80);
}
.dark .kin-util:hover {
  background: rgb(var(--surface-overlay) / 0.80);
}

@media (prefers-reduced-motion: reduce) {
  .kin-util { transition: none; }
}
</style>
