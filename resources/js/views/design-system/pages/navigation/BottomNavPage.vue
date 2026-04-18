<script setup>
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'

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
}

// ── Shadow values ─────────────────────────────────────────────────────────────
const SHADOW_ELEVATED_LT = '0 4px 8px rgba(28, 20, 10, 0.08), 0 16px 32px rgba(28, 20, 10, 0.10)'
const SHADOW_ELEVATED_DK = '0 4px 8px rgba(0, 0, 0, 0.40), 0 16px 32px rgba(0, 0, 0, 0.30)'
const SHADOW_FAB_LT      = '0 4px 8px rgba(28, 20, 10, 0.12), 0 16px 32px rgba(28, 20, 10, 0.18)'
const SHADOW_FAB_DK      = '0 4px 12px rgba(0, 0, 0, 0.55), 0 16px 32px rgba(0, 0, 0, 0.45)'

// ── Faux page background colors for demos ────────────────────────────────────
const PAGE_BG_LT = 'linear-gradient(180deg, #E8E4DF 0%, #FAF8F5 100%)'
const PAGE_BG_DK = 'linear-gradient(180deg, #0E0D0C 0%, #141311 100%)'

// ── Photo for glass demo ─────────────────────────────────────────────────────
const PHOTO_GLASS = 'https://picsum.photos/seed/salmon/800/600'

// ── Nav slot definitions ─────────────────────────────────────────────────────
// Variant A: 5 slots (no FAB)
const NAV_SLOTS_A = [
  { id: 'today',     label: 'Today' },
  { id: 'plan',      label: 'Plan' },
  { id: 'family',    label: 'Family' },
  { id: 'store',     label: 'Store' },
  { id: 'assistant', label: 'Assistant' },
]

// Variant B/C: 4 regular slots + 1 center FAB
const NAV_SLOTS_BC = [
  { id: 'today',  label: 'Today' },
  { id: 'plan',   label: 'Plan' },
  { id: 'family', label: 'Family' },
  { id: 'store',  label: 'Store' },
]
</script>

<template>
  <ComponentPage
    title="3.2 Bottom Nav (mobile)"
    description="Floating glass pill with 4 nav slots + elevated center Ask-Assistant FAB. Pill uses backdrop-blur for a premium floating feel over content. FAB makes AI the primary call-to-action (brief tenet #8)."
    status="chosen"
  >



    <!-- ══════════════════════════════════════════════════════════════
         VARIANT C — Glass pill + elevated center FAB
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-20">
      <VariantFrame label="Bottom nav" caption="Glass pill with 4 nav slots + elevated Ask-Assistant FAB. The blur lets content show through — most premium mobile feel.">
        <div class="w-full space-y-10">

          <!-- LIGHT PANEL C -->
          <div class="rounded-2xl border p-6 space-y-8" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>

            <!-- Default + glass-over-content in one -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: L.inkTertiary }">Glass pill floating over page content — Today active</p>
              <div class="flex justify-center">
                <div class="w-full max-w-[420px]">
                  <!-- Faux mobile — real photo fills the container -->
                  <div class="relative rounded-[28px] overflow-hidden" style="height: 280px;">
                    <img :src="PHOTO_GLASS" alt="glass demo background" class="absolute inset-0 w-full h-full object-cover" />
                    <!-- Light content overlay to simulate page scrolled under nav -->
                    <div class="absolute inset-0" style="background: linear-gradient(180deg, rgba(250,248,245,0.50) 0%, rgba(250,248,245,0.25) 60%, rgba(250,248,245,0.10) 100%)"></div>
                    <!-- Some faux content rows above the nav -->
                    <div class="absolute top-6 left-5 right-5 space-y-2">
                      <div class="h-3 rounded-full w-3/4" style="background: rgba(28,28,30,0.12)"></div>
                      <div class="h-3 rounded-full w-1/2" style="background: rgba(28,28,30,0.08)"></div>
                    </div>

                    <!-- Glass nav pill -->
                    <div class="absolute bottom-3 left-3 right-3">
                      <div class="bn-c-lt relative flex items-center rounded-[9999px] px-2" style="height: 60px;">
                        <!-- Today active -->
                        <button class="bn-c-slot flex-1 flex items-center justify-center">
                          <span class="w-11 h-11 rounded-full flex items-center justify-center" style="background: rgba(28,28,30,0.85);">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-[22px] h-[22px]" style="color:#FAF8F5">
                              <path d="M12 2.25a.75.75 0 0 1 .75.75v2.25a.75.75 0 0 1-1.5 0V3a.75.75 0 0 1 .75-.75ZM7.5 12a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM18.894 6.166a.75.75 0 0 0-1.06-1.06l-1.591 1.59a.75.75 0 1 0 1.06 1.061l1.591-1.59ZM21.75 12a.75.75 0 0 1-.75.75h-2.25a.75.75 0 0 1 0-1.5H21a.75.75 0 0 1 .75.75ZM17.834 18.894a.75.75 0 0 0 1.06-1.06l-1.59-1.591a.75.75 0 1 0-1.061 1.06l1.59 1.591ZM12 18a.75.75 0 0 1 .75.75V21a.75.75 0 0 1-1.5 0v-2.25A.75.75 0 0 1 12 18ZM7.772 19.894a.75.75 0 1 0 1.06-1.06l-1.59-1.591a.75.75 0 0 0-1.061 1.06l1.59 1.591ZM5.25 12a.75.75 0 0 1-.75.75H2.25a.75.75 0 0 1 0-1.5H4.5A.75.75 0 0 1 5.25 12ZM6.166 6.166a.75.75 0 0 0-1.06 1.06l1.59 1.591a.75.75 0 1 0 1.061-1.06l-1.59-1.591Z" />
                            </svg>
                          </span>
                        </button>
                        <!-- Plan inactive -->
                        <button class="bn-c-slot flex-1 flex items-center justify-center">
                          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-[22px] h-[22px]" style="color: rgba(28,28,30,0.50)">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                          </svg>
                        </button>
                        <!-- Center FAB placeholder -->
                        <div class="flex-1 min-w-[60px]"></div>
                        <!-- Family inactive -->
                        <button class="bn-c-slot flex-1 flex items-center justify-center">
                          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-[22px] h-[22px]" style="color: rgba(28,28,30,0.50)">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                          </svg>
                        </button>
                        <!-- Store inactive -->
                        <button class="bn-c-slot flex-1 flex items-center justify-center">
                          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-[22px] h-[22px]" style="color: rgba(28,28,30,0.50)">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007Z" />
                          </svg>
                        </button>
                        <!-- FAB — same gradient as B, sits above glass -->
                        <button class="bn-c-fab absolute left-1/2 -translate-x-1/2 -translate-y-[16px] w-[50px] h-[50px] rounded-full flex items-center justify-center"
                          style="background-image: linear-gradient(180deg, #313130 0%, #1C1C1E 100%); box-shadow: 0 4px 8px rgba(28,20,10,0.12), 0 16px 32px rgba(28,20,10,0.18);">
                          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6" style="color:#FAF8F5">
                            <path fill-rule="evenodd" d="M9 4.5a.75.75 0 0 1 .721.544l.813 2.846a3.75 3.75 0 0 0 2.576 2.576l2.846.813a.75.75 0 0 1 0 1.442l-2.846.813a3.75 3.75 0 0 0-2.576 2.576l-.813 2.846a.75.75 0 0 1-1.442 0l-.813-2.846a3.75 3.75 0 0 0-2.576-2.576l-2.846-.813a.75.75 0 0 1 0-1.442l2.846-.813A3.75 3.75 0 0 0 7.466 7.89l.813-2.846A.75.75 0 0 1 9 4.5ZM18 1.5a.75.75 0 0 1 .728.568l.258 1.036c.236.94.97 1.674 1.91 1.91l1.036.258a.75.75 0 0 1 0 1.456l-1.036.258c-.94.236-1.674.97-1.91 1.91l-.258 1.036a.75.75 0 0 1-1.456 0l-.258-1.036a2.625 2.625 0 0 0-1.91-1.91l-1.036-.258a.75.75 0 0 1 0-1.456l1.036-.258a2.625 2.625 0 0 0 1.91-1.91l.258-1.036A.75.75 0 0 1 18 1.5Z" clip-rule="evenodd" />
                          </svg>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div><!-- /light panel C -->

          <!-- DARK PANEL C -->
          <div class="rounded-2xl border p-6 space-y-8" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
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
                      <div class="bn-c-dk relative flex items-center rounded-[9999px] px-2" style="height: 60px;">
                        <!-- Today active dark -->
                        <button class="bn-c-slot flex-1 flex items-center justify-center">
                          <span class="w-11 h-11 rounded-full flex items-center justify-center" style="background: rgba(240,237,233,0.90);">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-[22px] h-[22px]" style="color:#141311">
                              <path d="M12 2.25a.75.75 0 0 1 .75.75v2.25a.75.75 0 0 1-1.5 0V3a.75.75 0 0 1 .75-.75ZM7.5 12a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM18.894 6.166a.75.75 0 0 0-1.06-1.06l-1.591 1.59a.75.75 0 1 0 1.06 1.061l1.591-1.59ZM21.75 12a.75.75 0 0 1-.75.75h-2.25a.75.75 0 0 1 0-1.5H21a.75.75 0 0 1 .75.75ZM17.834 18.894a.75.75 0 0 0 1.06-1.06l-1.59-1.591a.75.75 0 1 0-1.061 1.06l1.59 1.591ZM12 18a.75.75 0 0 1 .75.75V21a.75.75 0 0 1-1.5 0v-2.25A.75.75 0 0 1 12 18ZM7.772 19.894a.75.75 0 1 0 1.06-1.06l-1.59-1.591a.75.75 0 0 0-1.061 1.06l1.59 1.591ZM5.25 12a.75.75 0 0 1-.75.75H2.25a.75.75 0 0 1 0-1.5H4.5A.75.75 0 0 1 5.25 12ZM6.166 6.166a.75.75 0 0 0-1.06 1.06l1.59 1.591a.75.75 0 1 0 1.061-1.06l-1.59-1.591Z" />
                            </svg>
                          </span>
                        </button>
                        <button class="bn-c-slot flex-1 flex items-center justify-center">
                          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-[22px] h-[22px]" style="color: rgba(240,237,233,0.45)">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                          </svg>
                        </button>
                        <div class="flex-1 min-w-[60px]"></div>
                        <button class="bn-c-slot flex-1 flex items-center justify-center">
                          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-[22px] h-[22px]" style="color: rgba(240,237,233,0.45)">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                          </svg>
                        </button>
                        <button class="bn-c-slot flex-1 flex items-center justify-center">
                          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-[22px] h-[22px]" style="color: rgba(240,237,233,0.45)">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007Z" />
                          </svg>
                        </button>
                        <!-- FAB dark over glass -->
                        <button class="bn-c-fab absolute left-1/2 -translate-x-1/2 -translate-y-[16px] w-[50px] h-[50px] rounded-full flex items-center justify-center"
                          :style="{ backgroundImage: 'linear-gradient(180deg, #4A4947 0%, #2C2A27 100%)', boxShadow: SHADOW_FAB_DK }">
                          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6" style="color:#F0EDE9">
                            <path fill-rule="evenodd" d="M9 4.5a.75.75 0 0 1 .721.544l.813 2.846a3.75 3.75 0 0 0 2.576 2.576l2.846.813a.75.75 0 0 1 0 1.442l-2.846.813a3.75 3.75 0 0 0-2.576 2.576l-.813 2.846a.75.75 0 0 1-1.442 0l-.813-2.846a3.75 3.75 0 0 0-2.576-2.576l-2.846-.813a.75.75 0 0 1 0-1.442l2.846-.813A3.75 3.75 0 0 0 7.466 7.89l.813-2.846A.75.75 0 0 1 9 4.5ZM18 1.5a.75.75 0 0 1 .728.568l.258 1.036c.236.94.97 1.674 1.91 1.91l1.036.258a.75.75 0 0 1 0 1.456l-1.036.258c-.94.236-1.674.97-1.91 1.91l-.258 1.036a.75.75 0 0 1-1.456 0l-.258-1.036a2.625 2.625 0 0 0-1.91-1.91l-1.036-.258a.75.75 0 0 1 0-1.456l1.036-.258a2.625 2.625 0 0 0 1.91-1.91l.258-1.036A.75.75 0 0 1 18 1.5Z" clip-rule="evenodd" />
                          </svg>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div><!-- /dark panel C -->

        </div>
      </VariantFrame>
    </section>



  </ComponentPage>
</template>

<style scoped>

/* ═══════════════════════════════════════════════════════════════════
   VARIANT C — GLASS LIGHT
   ═══════════════════════════════════════════════════════════════════ */
.bn-c-lt {
  background: rgba(255, 255, 255, 0.72);
  backdrop-filter: blur(16px) saturate(140%);
  -webkit-backdrop-filter: blur(16px) saturate(140%);
  border: 1px solid rgba(255, 255, 255, 0.65);
  box-shadow: 0 1px 1px rgba(255,255,255,0.40) inset, 0 16px 32px rgba(28,20,10,0.08);
  overflow: visible;
  transition: box-shadow 200ms cubic-bezier(0.16, 1, 0.3, 1);
}
.bn-c-slot {
  cursor: pointer;
  transition: opacity 200ms cubic-bezier(0.16, 1, 0.3, 1);
}
.bn-c-slot:hover {
  opacity: 0.70;
}

/* ═══════════════════════════════════════════════════════════════════
   VARIANT C — GLASS DARK
   ═══════════════════════════════════════════════════════════════════ */
.bn-c-dk {
  background: rgba(28, 27, 25, 0.75);
  backdrop-filter: blur(18px) saturate(130%);
  -webkit-backdrop-filter: blur(18px) saturate(130%);
  border: 1px solid rgba(255, 255, 255, 0.08);
  box-shadow: 0 1px 1px rgba(255,255,255,0.06) inset, 0 16px 32px rgba(0,0,0,0.45);
  overflow: visible;
  transition: box-shadow 200ms cubic-bezier(0.16, 1, 0.3, 1);
}

/* ═══════════════════════════════════════════════════════════════════
   VARIANT C — FAB (shared light/dark, same base class)
   ═══════════════════════════════════════════════════════════════════ */
.bn-c-fab {
  cursor: pointer;
  transition: transform 200ms cubic-bezier(0.16, 1, 0.3, 1),
              box-shadow 200ms cubic-bezier(0.16, 1, 0.3, 1);
}
.bn-c-fab:hover {
  transform: translateX(-50%) translateY(-22px);
}

/* ═══════════════════════════════════════════════════════════════════
   REDUCED MOTION
   ═══════════════════════════════════════════════════════════════════ */
@media (prefers-reduced-motion: reduce) {
  .bn-a-lt, .bn-a-dk,
  .bn-b-lt, .bn-b-dk,
  .bn-c-lt, .bn-c-dk,
  .bn-b-fab-lt, .bn-b-fab-dk, .bn-c-fab,
  .bn-a-slot, .bn-b-slot, .bn-c-slot {
    transition: none;
  }
  .bn-b-fab-lt:hover,
  .bn-b-fab-dk:hover,
  .bn-c-fab:hover {
    transform: translateX(-50%) translateY(-16px);
  }
}
</style>
