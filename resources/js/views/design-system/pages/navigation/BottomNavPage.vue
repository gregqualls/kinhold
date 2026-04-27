<script setup>
import { ref } from 'vue'
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import KinBottomNav from '@/components/design-system/KinBottomNav.vue'
import {
  SunIcon,
  CalendarIcon,
  UsersIcon,
  ShoppingBagIcon,
  SparklesIcon,
} from '@heroicons/vue/24/solid'
import {
  CalendarIcon as CalendarOutline,
  UsersIcon as UsersOutline,
  ShoppingBagIcon as ShoppingBagOutline,
} from '@heroicons/vue/24/outline'

// ── Panel chrome tokens (Light/Dark hand-rolled wrappers) ────────────────────
const L = {
  surfaceApp:    '#FAF8F5',
  surfaceRaised: '#FFFFFF',
  inkPrimary:    '#1C1C1E',
  inkSecondary:  '#6B6966',
  inkTertiary:   '#9C9895',
  borderSubtle:  '#E8E4DF',
}
const D = {
  surfaceApp:    '#141311',
  inkPrimary:    '#F0EDE9',
  inkSecondary:  '#A09C97',
  inkTertiary:   '#6E6B67',
  borderSubtle:  '#2C2A27',
}

// ── Demo photo for glass backdrop ────────────────────────────────────────────
const PHOTO_GLASS = 'https://picsum.photos/seed/salmon/800/600'

// ── Nav items (exactly 4 — FAB occupies the 5th slot) ────────────────────────
// Light-panel items use solid icons for contrast over photo.
const NAV_ITEMS = [
  { id: 'today',  label: 'Today',  icon: SunIcon },
  { id: 'plan',   label: 'Plan',   icon: CalendarOutline },
  { id: 'family', label: 'Family', icon: UsersOutline },
  { id: 'store',  label: 'Store',  icon: ShoppingBagOutline },
]

const activeLight = ref('today')
const activeDark  = ref('today')
</script>

<template>
  <ComponentPage
    title="3.2 Bottom Nav (mobile)"
    description="Floating glass pill with 4 nav slots + elevated center Ask-Assistant FAB. Pill uses backdrop-blur for a premium floating feel over content. FAB makes AI the primary call-to-action (brief tenet #8)."
    status="chosen"
  >

    <section class="mb-20">
      <VariantFrame label="Bottom nav" caption="Glass pill with 4 nav slots + elevated Ask-Assistant FAB. The blur lets content show through — most premium mobile feel.">
        <div class="w-full space-y-10">

          <!-- ══════════════ LIGHT PANEL ══════════════ -->
          <div class="rounded-2xl border p-6 space-y-8" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>

            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: L.inkTertiary }">Glass pill floating over page content — Today active</p>
              <div class="flex justify-center">
                <div class="w-full max-w-[420px]">
                  <!-- Faux mobile — real photo fills the container -->
                  <div class="relative rounded-[28px] overflow-hidden" style="height: 280px;">
                    <img :src="PHOTO_GLASS" alt="glass demo background" class="absolute inset-0 w-full h-full object-cover" />
                    <!-- Light content overlay so page reads 'scrolled' -->
                    <div class="absolute inset-0" style="background: linear-gradient(180deg, rgba(250,248,245,0.50) 0%, rgba(250,248,245,0.25) 60%, rgba(250,248,245,0.10) 100%)"></div>
                    <div class="absolute top-6 left-5 right-5 space-y-2">
                      <div class="h-3 rounded-full w-3/4" style="background: rgba(28,28,30,0.12)"></div>
                      <div class="h-3 rounded-full w-1/2" style="background: rgba(28,28,30,0.08)"></div>
                    </div>

                    <!-- Glass nav pill -->
                    <div class="absolute bottom-3 left-3 right-3">
                      <KinBottomNav
                        :items="NAV_ITEMS"
                        :active-id="activeLight"
                        @item-click="(id) => activeLight = id"
                      >
                        <template #fab>
                          <button
                            type="button"
                            aria-label="Ask Assistant"
                            class="w-[50px] h-[50px] rounded-full flex items-center justify-center transition-transform hover:-translate-y-[2px]"
                            style="background-image: linear-gradient(180deg, #313130 0%, #1C1C1E 100%); box-shadow: 0 4px 8px rgba(28,20,10,0.12), 0 16px 32px rgba(28,20,10,0.18);"
                          >
                            <SparklesIcon class="w-6 h-6" style="color:#FAF8F5" />
                          </button>
                        </template>
                      </KinBottomNav>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div><!-- /light panel -->

          <!-- ══════════════ DARK PANEL ══════════════ -->
          <div class="dark rounded-2xl border p-6 space-y-8" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: D.inkTertiary }">Glass pill over photo — dark glass treatment</p>
              <div class="flex justify-center">
                <div class="w-full max-w-[420px]">
                  <div class="relative rounded-[28px] overflow-hidden" style="height: 280px;">
                    <img :src="PHOTO_GLASS" alt="glass demo background dark" class="absolute inset-0 w-full h-full object-cover" style="filter: brightness(0.65) saturate(0.85);" />
                    <div class="absolute inset-0" style="background: linear-gradient(180deg, rgba(20,19,17,0.35) 0%, rgba(20,19,17,0.15) 60%, rgba(20,19,17,0.05) 100%)"></div>
                    <div class="absolute top-6 left-5 right-5 space-y-2">
                      <div class="h-3 rounded-full w-3/4" style="background: rgba(240,237,233,0.10)"></div>
                      <div class="h-3 rounded-full w-1/2" style="background: rgba(240,237,233,0.07)"></div>
                    </div>

                    <!-- Glass nav dark -->
                    <div class="absolute bottom-3 left-3 right-3">
                      <KinBottomNav
                        :items="NAV_ITEMS"
                        :active-id="activeDark"
                        @item-click="(id) => activeDark = id"
                      >
                        <template #fab>
                          <button
                            type="button"
                            aria-label="Ask Assistant"
                            class="w-[50px] h-[50px] rounded-full flex items-center justify-center transition-transform hover:-translate-y-[2px]"
                            style="background-image: linear-gradient(180deg, #4A4947 0%, #2C2A27 100%); box-shadow: 0 4px 12px rgba(0,0,0,0.55), 0 16px 32px rgba(0,0,0,0.45);"
                          >
                            <SparklesIcon class="w-6 h-6" style="color:#F0EDE9" />
                          </button>
                        </template>
                      </KinBottomNav>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div><!-- /dark panel -->

        </div>
      </VariantFrame>
    </section>

  </ComponentPage>
</template>
