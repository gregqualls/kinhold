<script setup>
import { ref } from 'vue'
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import KinSidebar from '@/components/design-system/KinSidebar.vue'
import {
  HomeIcon,
  CalendarIcon,
  UsersIcon,
  ShoppingBagIcon,
  SparklesIcon,
  Cog6ToothIcon,
} from '@heroicons/vue/24/outline'

// ── Panel chrome tokens (Light/Dark hand-rolled wrappers) ────────────────────
const L = {
  surfaceApp:    '#FAF8F5',
  surfaceRaised: '#FFFFFF',
  inkPrimary:    '#1C1C1E',
  inkSecondary:  '#6B6966',
  inkTertiary:   '#9C9895',
  borderSubtle:  '#E8E4DF',
  accentLavSoft: '#EAE6F8',
  accentLavBold: '#6856B2',
}
const D = {
  surfaceApp:    '#141311',
  surfaceRaised: '#1C1B19',
  inkPrimary:    '#F0EDE9',
  inkSecondary:  '#A09C97',
  inkTertiary:   '#6E6B67',
  borderSubtle:  '#2C2A27',
  accentLavSoft: '#302A48',
  accentLavBold: '#B6A8E6',
}

const NAV_ITEMS = [
  { key: 'today',     label: 'Today',     icon: HomeIcon },
  { key: 'plan',      label: 'Plan',      icon: CalendarIcon },
  { key: 'family',    label: 'Family',    icon: UsersIcon },
  { key: 'store',     label: 'Store',     icon: ShoppingBagIcon },
  { key: 'assistant', label: 'Assistant', icon: SparklesIcon },
  { key: 'settings',  label: 'Settings',  icon: Cog6ToothIcon },
]

// Collapse state — independent per panel so the two sidebars toggle separately.
const collapsedLt = ref(false)
const collapsedDk = ref(false)
</script>

<template>
  <ComponentPage
    title="3.3 Sidebar"
    description="Desktop navigation sidebar for data-heavy views — calendar, tasks, vault, food. Active item gets a filled accent-lavender pill. Collapsible to 72px icons-only state via the toggle at the bottom."
    status="chosen"
  >

    <section class="mb-20">
      <VariantFrame label="Sidebar" caption="Accent-pill active item + collapsible. Click the toggle at the bottom of each sidebar to switch between 240px expanded and 72px icons-only.">
        <div class="w-full space-y-10">

          <!-- ══════════════ LIGHT PANEL ══════════════ -->
          <div class="rounded-2xl border p-6 space-y-4" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>
            <p class="text-[11px] font-medium" :style="{ color: L.inkTertiary }">Active: "Today" — click the chevron toggle at the bottom to collapse</p>

            <div class="flex rounded-xl overflow-hidden border" :style="{ borderColor: L.borderSubtle, height: '480px' }">

              <KinSidebar
                brand="kinhold"
                :items="NAV_ITEMS"
                active-key="today"
                v-model:collapsed="collapsedLt"
              >
                <template #user="{ collapsed }">
                  <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0" :style="{ background: L.accentLavSoft }">
                    <span class="text-[11px] font-semibold" :style="{ color: L.accentLavBold }">GQ</span>
                  </div>
                  <div v-if="!collapsed" class="min-w-0">
                    <p class="text-[13px] font-semibold truncate" :style="{ color: L.inkPrimary }">Greg Qualls</p>
                    <p class="text-[11px] truncate" :style="{ color: L.inkSecondary }">Parent · Qualls family</p>
                  </div>
                </template>
              </KinSidebar>

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
          </div><!-- /light panel -->

          <!-- ══════════════ DARK PANEL ══════════════ -->
          <div class="dark rounded-2xl border p-6 space-y-4" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>
            <p class="text-[11px] font-medium" :style="{ color: D.inkTertiary }">Active: "Today" — click the chevron toggle at the bottom to collapse</p>

            <div class="flex rounded-xl overflow-hidden border" :style="{ borderColor: D.borderSubtle, height: '480px' }">

              <KinSidebar
                brand="kinhold"
                :items="NAV_ITEMS"
                active-key="today"
                v-model:collapsed="collapsedDk"
              >
                <template #user="{ collapsed }">
                  <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0" :style="{ background: D.accentLavSoft }">
                    <span class="text-[11px] font-semibold" :style="{ color: D.accentLavBold }">GQ</span>
                  </div>
                  <div v-if="!collapsed" class="min-w-0">
                    <p class="text-[13px] font-semibold truncate" :style="{ color: D.inkPrimary }">Greg Qualls</p>
                    <p class="text-[11px] truncate" :style="{ color: D.inkSecondary }">Parent · Qualls family</p>
                  </div>
                </template>
              </KinSidebar>

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
          </div><!-- /dark panel -->

        </div>
      </VariantFrame>
    </section>

  </ComponentPage>
</template>
