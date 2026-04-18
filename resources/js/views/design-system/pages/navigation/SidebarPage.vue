<script setup>
import { ref } from 'vue'
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import {
  HomeIcon,
  CalendarIcon,
  UsersIcon,
  ShoppingBagIcon,
  SparklesIcon,
  Cog6ToothIcon,
  ChevronDoubleLeftIcon,
  ChevronDoubleRightIcon,
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
  accentLavSoft: '#302A48',
  accentLavBold: '#B6A8E6',
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
  accentLavSoft: '#EAE6F8',
  accentLavBold: '#6856B2',
}

// ── Nav data ─────────────────────────────────────────────────────────────────
const NAV_ITEMS = [
  { key: 'today',     label: 'Today',     icon: HomeIcon },
  { key: 'plan',      label: 'Plan',      icon: CalendarIcon },
  { key: 'family',    label: 'Family',    icon: UsersIcon },
  { key: 'store',     label: 'Store',     icon: ShoppingBagIcon },
  { key: 'assistant', label: 'Assistant', icon: SparklesIcon },
  { key: 'settings',  label: 'Settings',  icon: Cog6ToothIcon },
]

// Collapse state — independent per panel so the two sidebars can be toggled separately
const collapsedLt = ref(false)
const collapsedDk = ref(false)
</script>

<template>
  <ComponentPage
    title="3.3 Sidebar"
    description="Desktop navigation sidebar for data-heavy views — calendar, tasks, vault, food. Active item gets a filled accent-lavender pill. Collapsible to 72px icons-only state via the toggle at the bottom."
    status="chosen"
  >

    <!-- ══════════════════════════════════════════════════════════════
         VARIANT A — Plain sidebar with accent-filled pill
         ══════════════════════════════════════════════════════════════ -->
    <section class="mb-20">
      <VariantFrame label="Sidebar" caption="Accent-pill active item + collapsible. Click the toggle at the bottom of each sidebar to switch between 240px expanded and 72px icons-only.">
        <div class="w-full space-y-10">

          <!-- LIGHT PANEL -->
          <div class="rounded-2xl border p-6 space-y-4" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>
            <p class="text-[11px] font-medium" :style="{ color: L.inkTertiary }">Active: "Today" — click the chevron toggle at the bottom to collapse</p>

            <!-- Sidebar + content flex row -->
            <div class="flex rounded-xl overflow-hidden border" :style="{ borderColor: L.borderSubtle, height: '480px' }">

              <!-- Sidebar — light (reactive width) -->
              <div
                class="sb-container flex flex-col flex-shrink-0"
                :style="{ width: collapsedLt ? '72px' : '240px', background: '#FFFFFF', borderRight: '1px solid #E8E4DF' }"
              >

                <!-- Brand mark -->
                <div class="flex items-center gap-2 px-3 py-4" :class="collapsedLt ? 'justify-center' : ''">
                  <div class="w-7 h-7 rounded-lg flex-shrink-0" style="background: linear-gradient(135deg, #6856B2, #BA562E)"></div>
                  <span v-if="!collapsedLt" class="text-[15px] font-semibold whitespace-nowrap" :style="{ color: L.inkPrimary, fontFamily: '\'Plus Jakarta Sans\', sans-serif' }">kinhold</span>
                </div>

                <!-- Nav items -->
                <nav class="flex-1 px-2 py-1">
                  <ul class="space-y-0.5">
                    <li v-for="item in NAV_ITEMS" :key="item.key">
                      <button
                        class="sb-a-lt-item w-full flex items-center gap-2 py-2.5 rounded-full text-left"
                        :class="collapsedLt ? 'justify-center px-0' : 'px-3'"
                        :title="collapsedLt ? item.label : ''"
                        :style="item.key === 'today'
                          ? { background: L.accentLavBold, color: '#FAF8F5' }
                          : { color: L.inkSecondary }"
                      >
                        <component :is="item.icon" class="w-5 h-5 flex-shrink-0" />
                        <span v-if="!collapsedLt" class="text-[14px] font-medium whitespace-nowrap">{{ item.label }}</span>
                      </button>
                    </li>
                  </ul>
                </nav>

                <!-- Collapse toggle -->
                <div class="border-t" :style="{ borderColor: L.borderSubtle }">
                  <button
                    class="sb-a-lt-item w-full flex items-center justify-center py-2.5"
                    :style="{ color: L.inkTertiary }"
                    :title="collapsedLt ? 'Expand sidebar' : 'Collapse sidebar'"
                    @click="collapsedLt = !collapsedLt"
                  >
                    <ChevronDoubleRightIcon v-if="collapsedLt" class="w-4 h-4" />
                    <ChevronDoubleLeftIcon v-else class="w-4 h-4" />
                  </button>
                </div>

                <!-- User row -->
                <div class="p-3 flex items-center gap-2 border-t" :class="collapsedLt ? 'justify-center' : ''" :style="{ borderColor: L.borderSubtle }">
                  <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0" :style="{ background: L.accentLavSoft }">
                    <span class="text-[11px] font-semibold" :style="{ color: L.accentLavBold }">GQ</span>
                  </div>
                  <div v-if="!collapsedLt" class="min-w-0">
                    <p class="text-[13px] font-semibold truncate" :style="{ color: L.inkPrimary }">Greg Qualls</p>
                    <p class="text-[11px] truncate" :style="{ color: L.inkSecondary }">Parent · Qualls family</p>
                  </div>
                </div>
              </div>

              <!-- Simulated content area -->
              <div class="flex-1 p-8" :style="{ background: L.surfaceApp }">
                <p class="text-[11px] font-semibold uppercase tracking-widest mb-4" :style="{ color: L.inkTertiary }">Simulated content area</p>
                <p class="text-[22px] font-semibold" :style="{ color: L.inkPrimary }">Today</p>
                <p class="text-[14px] mt-1" :style="{ color: L.inkSecondary }">Saturday, April 18 — Qualls family</p>
                <div class="mt-6 space-y-3">
                  <div class="h-10 rounded-xl" :style="{ background: L.surfaceRaised, border: '1px solid ' + L.borderSubtle }"></div>
                  <div class="h-10 rounded-xl" :style="{ background: L.surfaceRaised, border: '1px solid ' + L.borderSubtle }"></div>
                  <div class="h-10 rounded-xl" :style="{ background: L.surfaceRaised, border: '1px solid ' + L.borderSubtle }"></div>
                </div>
              </div>
            </div>
          </div>

          <!-- DARK PANEL -->
          <div class="rounded-2xl border p-6 space-y-4" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>
            <p class="text-[11px] font-medium" :style="{ color: D.inkTertiary }">Active: "Today" — click the chevron toggle at the bottom to collapse</p>

            <div class="flex rounded-xl overflow-hidden border" :style="{ borderColor: D.borderSubtle, height: '480px' }">

              <!-- Sidebar — dark (reactive width) -->
              <div
                class="sb-container flex flex-col flex-shrink-0"
                :style="{ width: collapsedDk ? '72px' : '240px', background: '#1C1B19', borderRight: '1px solid #2C2A27' }"
              >

                <div class="flex items-center gap-2 px-3 py-4" :class="collapsedDk ? 'justify-center' : ''">
                  <div class="w-7 h-7 rounded-lg flex-shrink-0" style="background: linear-gradient(135deg, #6856B2, #BA562E)"></div>
                  <span v-if="!collapsedDk" class="text-[15px] font-semibold whitespace-nowrap" :style="{ color: D.inkPrimary }">kinhold</span>
                </div>

                <nav class="flex-1 px-2 py-1">
                  <ul class="space-y-0.5">
                    <li v-for="item in NAV_ITEMS" :key="item.key">
                      <button
                        class="sb-a-dk-item w-full flex items-center gap-2 py-2.5 rounded-full text-left"
                        :class="collapsedDk ? 'justify-center px-0' : 'px-3'"
                        :title="collapsedDk ? item.label : ''"
                        :style="item.key === 'today'
                          ? { background: D.accentLavBold, color: '#1C1C1E' }
                          : { color: D.inkSecondary }"
                      >
                        <component :is="item.icon" class="w-5 h-5 flex-shrink-0" />
                        <span v-if="!collapsedDk" class="text-[14px] font-medium whitespace-nowrap">{{ item.label }}</span>
                      </button>
                    </li>
                  </ul>
                </nav>

                <div class="border-t" :style="{ borderColor: D.borderSubtle }">
                  <button
                    class="sb-a-dk-item w-full flex items-center justify-center py-2.5"
                    :style="{ color: D.inkTertiary }"
                    :title="collapsedDk ? 'Expand sidebar' : 'Collapse sidebar'"
                    @click="collapsedDk = !collapsedDk"
                  >
                    <ChevronDoubleRightIcon v-if="collapsedDk" class="w-4 h-4" />
                    <ChevronDoubleLeftIcon v-else class="w-4 h-4" />
                  </button>
                </div>

                <div class="p-3 flex items-center gap-2 border-t" :class="collapsedDk ? 'justify-center' : ''" :style="{ borderColor: D.borderSubtle }">
                  <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0" :style="{ background: D.accentLavSoft }">
                    <span class="text-[11px] font-semibold" :style="{ color: D.accentLavBold }">GQ</span>
                  </div>
                  <div v-if="!collapsedDk" class="min-w-0">
                    <p class="text-[13px] font-semibold truncate" :style="{ color: D.inkPrimary }">Greg Qualls</p>
                    <p class="text-[11px] truncate" :style="{ color: D.inkSecondary }">Parent · Qualls family</p>
                  </div>
                </div>
              </div>

              <!-- Simulated content area dark -->
              <div class="flex-1 p-8" :style="{ background: D.surfaceApp }">
                <p class="text-[11px] font-semibold uppercase tracking-widest mb-4" :style="{ color: D.inkTertiary }">Simulated content area</p>
                <p class="text-[22px] font-semibold" :style="{ color: D.inkPrimary }">Today</p>
                <p class="text-[14px] mt-1" :style="{ color: D.inkSecondary }">Saturday, April 18 — Qualls family</p>
                <div class="mt-6 space-y-3">
                  <div class="h-10 rounded-xl" :style="{ background: D.surfaceRaised, border: '1px solid ' + D.borderSubtle }"></div>
                  <div class="h-10 rounded-xl" :style="{ background: D.surfaceRaised, border: '1px solid ' + D.borderSubtle }"></div>
                  <div class="h-10 rounded-xl" :style="{ background: D.surfaceRaised, border: '1px solid ' + D.borderSubtle }"></div>
                </div>
              </div>
            </div>
          </div>

        </div>
      </VariantFrame>
    </section>





  </ComponentPage>
</template>

<style scoped>
/*
  ═══════════════════════════════════════════════════════════════════
  VARIANT A — LIGHT
  ═══════════════════════════════════════════════════════════════════
*/
.sb-container {
  transition: width 250ms cubic-bezier(0.16, 1, 0.3, 1);
}
.sb-a-lt-item {
  transition: background-color 200ms cubic-bezier(0.16, 1, 0.3, 1),
              color 200ms cubic-bezier(0.16, 1, 0.3, 1);
}
.sb-a-lt-item:hover {
  background: #F5F2EE;
}

/*
  ═══════════════════════════════════════════════════════════════════
  VARIANT A — DARK
  ═══════════════════════════════════════════════════════════════════
*/
.sb-a-dk-item {
  transition: background-color 200ms cubic-bezier(0.16, 1, 0.3, 1),
              color 200ms cubic-bezier(0.16, 1, 0.3, 1);
}
.sb-a-dk-item:hover {
  background: #161513;
}



/*
  ═══════════════════════════════════════════════════════════════════
  REDUCED MOTION — snap all transitions
  ═══════════════════════════════════════════════════════════════════
*/
@media (prefers-reduced-motion: reduce) {
  .sb-container,
  .sb-a-lt-item,
  .sb-a-dk-item {
    transition: none;
  }
}
</style>
