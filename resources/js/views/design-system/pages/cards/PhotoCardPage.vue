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
  // Button accent — lavender
  accentLavenderSoft: '#EDE9F9',
  accentLavenderBold: '#6856B2',
}

// ── Shadow values ─────────────────────────────────────────────────────────────
const SHADOW_RESTING_LT = '0 1px 2px rgba(28, 20, 10, 0.04), 0 2px 6px rgba(28, 20, 10, 0.05)'
const SHADOW_HOVER_LT   = '0 4px 8px rgba(28, 20, 10, 0.08), 0 16px 32px rgba(28, 20, 10, 0.10)'
const SHADOW_RESTING_DK = '0 1px 2px rgba(0, 0, 0, 0.30), 0 2px 6px rgba(0, 0, 0, 0.25)'
const SHADOW_HOVER_DK   = '0 4px 8px rgba(0, 0, 0, 0.40), 0 16px 32px rgba(0, 0, 0, 0.30)'

// ── Photo sources — picsum.photos with food-themed seeds ─────────────────────
// All at 800×600 for hero, 400×500 for grid cards
const PHOTOS = {
  salmon:  'https://picsum.photos/seed/salmon/800/600',
  burrito: 'https://picsum.photos/seed/burrito/400/500',
  thai:    'https://picsum.photos/seed/thai/400/500',
  oats:    'https://picsum.photos/seed/oats/400/500',
}
</script>

<template>
  <ComponentPage
    title="2.2 PhotoCard"
    description="Image-led card for recipes, restaurants, events, and any content with a real photo. Edge-to-edge photo + bottom gradient scrim (always present for legibility) + optional translucent chip badges overlaid top-right. When no photo is available, fall back to GradientCard (2.3) rather than rendering with a blank image."
    status="chosen"
  >



    <!-- ══════════════════════════════════════════════════════════════
         VARIANT C — Photo-first with chip badges overlaid top-right
         Same edge-to-edge as B, but adds translucent chip badges
         in the top-right corner. Status at a glance.
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-20">
      <VariantFrame label="PhotoCard" caption="Edge-to-edge photo + gradient scrim + optional top-right chip badges. Scrim handles text legibility on any photo; chips are opt-in per card.">
        <div class="w-full space-y-10">

          <!-- LIGHT PANEL C -->
          <div class="rounded-2xl border p-6 space-y-8" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>

            <!-- 1. Single hero C -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: L.inkTertiary }">Single hero — roasted salmon with status chip</p>
              <div class="max-w-md mx-auto">
                <div
                  class="photo-card-c-lt relative rounded-[20px] overflow-hidden cursor-pointer aspect-[4/5]"
                  :style="{ boxShadow: SHADOW_RESTING_LT }"
                >
                  <img :src="PHOTOS.salmon" alt="Roasted salmon with lemon-dill butter" class="photo-card-c-img absolute inset-0 w-full h-full object-cover" />
                  <div class="photo-card-scrim absolute inset-0" />
                  <!-- Top-right chip cluster -->
                  <div class="absolute top-4 right-4 flex flex-col items-end gap-1.5">
                    <span class="photo-chip inline-flex items-center gap-1.5 rounded-full h-7 px-3 text-[11px] font-semibold">
                      <!-- Clock icon (Heroicon outline, inlined for zero-import weight) -->
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5 flex-shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                      </svg>
                      25 min
                    </span>
                    <span class="photo-chip inline-flex items-center rounded-full h-7 px-3 text-[11px] font-semibold">
                      Dinner
                    </span>
                  </div>
                  <!-- Bottom overlay text -->
                  <div class="absolute inset-x-0 bottom-0 p-5 space-y-1">
                    <p class="text-[19px] font-semibold leading-snug" style="color: #FFFFFF; text-shadow: 0 1px 3px rgba(0,0,0,0.40)">Roasted salmon with lemon-dill butter</p>
                    <p class="text-[13px]" style="color: rgba(255,255,255,0.82)">Serves 4 · 520 kcal</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- 2. Row of 3 C — each with different chip badge -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: L.inkTertiary }">Grid of 3 — different chip types per card</p>
              <div class="grid grid-cols-3 gap-4">

                <!-- Burrito — time chip -->
                <div
                  class="photo-card-c-lt relative rounded-[20px] overflow-hidden cursor-pointer aspect-[4/5]"
                  :style="{ boxShadow: SHADOW_RESTING_LT }"
                >
                  <img :src="PHOTOS.burrito" alt="Breakfast burritos" class="photo-card-c-img absolute inset-0 w-full h-full object-cover" />
                  <div class="photo-card-scrim absolute inset-0" />
                  <div class="absolute top-3 right-3">
                    <span class="photo-chip inline-flex items-center gap-1 rounded-full h-6 px-2.5 text-[10px] font-semibold">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3 h-3 flex-shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                      </svg>
                      15 min
                    </span>
                  </div>
                  <div class="absolute inset-x-0 bottom-0 p-3 space-y-0.5">
                    <p class="text-[12px] font-semibold leading-snug" style="color:#FFFFFF; text-shadow:0 1px 3px rgba(0,0,0,0.40)">Breakfast burritos</p>
                    <p class="text-[10px]" style="color:rgba(255,255,255,0.80)">Serves 6</p>
                  </div>
                </div>

                <!-- Thai — "New" chip -->
                <div
                  class="photo-card-c-lt relative rounded-[20px] overflow-hidden cursor-pointer aspect-[4/5]"
                  :style="{ boxShadow: SHADOW_RESTING_LT }"
                >
                  <img :src="PHOTOS.thai" alt="Thai basil chicken" class="photo-card-c-img absolute inset-0 w-full h-full object-cover" />
                  <div class="photo-card-scrim absolute inset-0" />
                  <div class="absolute top-3 right-3">
                    <span class="photo-chip inline-flex items-center rounded-full h-6 px-2.5 text-[10px] font-semibold">
                      New
                    </span>
                  </div>
                  <div class="absolute inset-x-0 bottom-0 p-3 space-y-0.5">
                    <p class="text-[12px] font-semibold leading-snug" style="color:#FFFFFF; text-shadow:0 1px 3px rgba(0,0,0,0.40)">Thai basil chicken</p>
                    <p class="text-[10px]" style="color:rgba(255,255,255,0.80)">Serves 4</p>
                  </div>
                </div>

                <!-- Oats — "Favorite" chip with star icon -->
                <div
                  class="photo-card-c-lt relative rounded-[20px] overflow-hidden cursor-pointer aspect-[4/5]"
                  :style="{ boxShadow: SHADOW_RESTING_LT }"
                >
                  <img :src="PHOTOS.oats" alt="Blueberry overnight oats" class="photo-card-c-img absolute inset-0 w-full h-full object-cover" />
                  <div class="photo-card-scrim absolute inset-0" />
                  <div class="absolute top-3 right-3">
                    <span class="photo-chip inline-flex items-center gap-1 rounded-full h-6 px-2.5 text-[10px] font-semibold">
                      <!-- Star icon -->
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-3 h-3 flex-shrink-0" style="color: #F59E0B">
                        <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd" />
                      </svg>
                      Favorite
                    </span>
                  </div>
                  <div class="absolute inset-x-0 bottom-0 p-3 space-y-0.5">
                    <p class="text-[12px] font-semibold leading-snug" style="color:#FFFFFF; text-shadow:0 1px 3px rgba(0,0,0,0.40)">Overnight oats</p>
                    <p class="text-[10px]" style="color:rgba(255,255,255,0.80)">Serves 2</p>
                  </div>
                </div>

              </div>
            </div>

            <!-- 3. No-chip treatment — scrim alone handles legibility -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: L.inkTertiary }">No chips — the scrim alone handles legibility when status isn't needed</p>
              <div class="grid grid-cols-3 gap-4">

                <!-- No-chip burrito -->
                <div
                  class="photo-card-c-lt relative rounded-[20px] overflow-hidden cursor-pointer aspect-[4/5]"
                  :style="{ boxShadow: SHADOW_RESTING_LT }"
                >
                  <img :src="PHOTOS.burrito" alt="Breakfast burritos" class="photo-card-c-img absolute inset-0 w-full h-full object-cover" />
                  <div class="photo-card-scrim absolute inset-0" />
                  <div class="absolute inset-x-0 bottom-0 p-3 space-y-0.5">
                    <p class="text-[12px] font-semibold leading-snug" style="color:#FFFFFF; text-shadow:0 1px 3px rgba(0,0,0,0.40)">Breakfast burritos</p>
                    <p class="text-[10px]" style="color:rgba(255,255,255,0.80)">Serves 6 · 15 min</p>
                  </div>
                </div>

                <!-- No-chip thai -->
                <div
                  class="photo-card-c-lt relative rounded-[20px] overflow-hidden cursor-pointer aspect-[4/5]"
                  :style="{ boxShadow: SHADOW_RESTING_LT }"
                >
                  <img :src="PHOTOS.thai" alt="Thai basil chicken" class="photo-card-c-img absolute inset-0 w-full h-full object-cover" />
                  <div class="photo-card-scrim absolute inset-0" />
                  <div class="absolute inset-x-0 bottom-0 p-3 space-y-0.5">
                    <p class="text-[12px] font-semibold leading-snug" style="color:#FFFFFF; text-shadow:0 1px 3px rgba(0,0,0,0.40)">Thai basil chicken</p>
                    <p class="text-[10px]" style="color:rgba(255,255,255,0.80)">Serves 4 · 20 min</p>
                  </div>
                </div>

                <!-- No-chip oats -->
                <div
                  class="photo-card-c-lt relative rounded-[20px] overflow-hidden cursor-pointer aspect-[4/5]"
                  :style="{ boxShadow: SHADOW_RESTING_LT }"
                >
                  <img :src="PHOTOS.oats" alt="Blueberry overnight oats" class="photo-card-c-img absolute inset-0 w-full h-full object-cover" />
                  <div class="photo-card-scrim absolute inset-0" />
                  <div class="absolute inset-x-0 bottom-0 p-3 space-y-0.5">
                    <p class="text-[12px] font-semibold leading-snug" style="color:#FFFFFF; text-shadow:0 1px 3px rgba(0,0,0,0.40)">Blueberry overnight oats</p>
                    <p class="text-[10px]" style="color:rgba(255,255,255,0.80)">Serves 2 · 5 min</p>
                  </div>
                </div>

              </div>
            </div>

          </div><!-- /light panel C -->

          <!-- DARK PANEL C -->
          <div class="rounded-2xl border p-6 space-y-8" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>
            <!-- Chip treatment is identical in dark mode — white translucent chip on photo always works -->

            <!-- 1. Single hero C dark -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: D.inkTertiary }">Single hero — roasted salmon with status chip</p>
              <div class="max-w-md mx-auto">
                <div
                  class="photo-card-c-dk relative rounded-[20px] overflow-hidden cursor-pointer aspect-[4/5]"
                  :style="{ boxShadow: SHADOW_RESTING_DK }"
                >
                  <img :src="PHOTOS.salmon" alt="Roasted salmon with lemon-dill butter" class="photo-card-c-img absolute inset-0 w-full h-full object-cover" />
                  <div class="photo-card-scrim absolute inset-0" />
                  <div class="absolute top-4 right-4 flex flex-col items-end gap-1.5">
                    <span class="photo-chip inline-flex items-center gap-1.5 rounded-full h-7 px-3 text-[11px] font-semibold">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5 flex-shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                      </svg>
                      25 min
                    </span>
                    <span class="photo-chip inline-flex items-center rounded-full h-7 px-3 text-[11px] font-semibold">
                      Dinner
                    </span>
                  </div>
                  <div class="absolute inset-x-0 bottom-0 p-5 space-y-1">
                    <p class="text-[19px] font-semibold leading-snug" style="color: #FFFFFF; text-shadow: 0 1px 3px rgba(0,0,0,0.50)">Roasted salmon with lemon-dill butter</p>
                    <p class="text-[13px]" style="color: rgba(255,255,255,0.82)">Serves 4 · 520 kcal</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- 2. Row of 3 C dark -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: D.inkTertiary }">Grid of 3 — different chip types per card</p>
              <div class="grid grid-cols-3 gap-4">

                <div
                  class="photo-card-c-dk relative rounded-[20px] overflow-hidden cursor-pointer aspect-[4/5]"
                  :style="{ boxShadow: SHADOW_RESTING_DK }"
                >
                  <img :src="PHOTOS.burrito" alt="Breakfast burritos" class="photo-card-c-img absolute inset-0 w-full h-full object-cover" />
                  <div class="photo-card-scrim absolute inset-0" />
                  <div class="absolute top-3 right-3">
                    <span class="photo-chip inline-flex items-center gap-1 rounded-full h-6 px-2.5 text-[10px] font-semibold">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3 h-3 flex-shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                      </svg>
                      15 min
                    </span>
                  </div>
                  <div class="absolute inset-x-0 bottom-0 p-3 space-y-0.5">
                    <p class="text-[12px] font-semibold leading-snug" style="color:#FFFFFF; text-shadow:0 1px 3px rgba(0,0,0,0.50)">Breakfast burritos</p>
                    <p class="text-[10px]" style="color:rgba(255,255,255,0.80)">Serves 6</p>
                  </div>
                </div>

                <div
                  class="photo-card-c-dk relative rounded-[20px] overflow-hidden cursor-pointer aspect-[4/5]"
                  :style="{ boxShadow: SHADOW_RESTING_DK }"
                >
                  <img :src="PHOTOS.thai" alt="Thai basil chicken" class="photo-card-c-img absolute inset-0 w-full h-full object-cover" />
                  <div class="photo-card-scrim absolute inset-0" />
                  <div class="absolute top-3 right-3">
                    <span class="photo-chip inline-flex items-center rounded-full h-6 px-2.5 text-[10px] font-semibold">
                      New
                    </span>
                  </div>
                  <div class="absolute inset-x-0 bottom-0 p-3 space-y-0.5">
                    <p class="text-[12px] font-semibold leading-snug" style="color:#FFFFFF; text-shadow:0 1px 3px rgba(0,0,0,0.50)">Thai basil chicken</p>
                    <p class="text-[10px]" style="color:rgba(255,255,255,0.80)">Serves 4</p>
                  </div>
                </div>

                <div
                  class="photo-card-c-dk relative rounded-[20px] overflow-hidden cursor-pointer aspect-[4/5]"
                  :style="{ boxShadow: SHADOW_RESTING_DK }"
                >
                  <img :src="PHOTOS.oats" alt="Blueberry overnight oats" class="photo-card-c-img absolute inset-0 w-full h-full object-cover" />
                  <div class="photo-card-scrim absolute inset-0" />
                  <div class="absolute top-3 right-3">
                    <span class="photo-chip inline-flex items-center gap-1 rounded-full h-6 px-2.5 text-[10px] font-semibold">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-3 h-3 flex-shrink-0" style="color: #F59E0B">
                        <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd" />
                      </svg>
                      Favorite
                    </span>
                  </div>
                  <div class="absolute inset-x-0 bottom-0 p-3 space-y-0.5">
                    <p class="text-[12px] font-semibold leading-snug" style="color:#FFFFFF; text-shadow:0 1px 3px rgba(0,0,0,0.50)">Overnight oats</p>
                    <p class="text-[10px]" style="color:rgba(255,255,255,0.80)">Serves 2</p>
                  </div>
                </div>

              </div>
            </div>

            <!-- 3. No-chip dark — scrim alone handles legibility -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: D.inkTertiary }">No chips — the scrim alone handles legibility when status isn't needed</p>
              <div class="grid grid-cols-3 gap-4">

                <div
                  class="photo-card-c-dk relative rounded-[20px] overflow-hidden cursor-pointer aspect-[4/5]"
                  :style="{ boxShadow: SHADOW_RESTING_DK }"
                >
                  <img :src="PHOTOS.burrito" alt="Breakfast burritos" class="photo-card-c-img absolute inset-0 w-full h-full object-cover" />
                  <div class="photo-card-scrim absolute inset-0" />
                  <div class="absolute inset-x-0 bottom-0 p-3 space-y-0.5">
                    <p class="text-[12px] font-semibold leading-snug" style="color:#FFFFFF; text-shadow:0 1px 3px rgba(0,0,0,0.40)">Breakfast burritos</p>
                    <p class="text-[10px]" style="color:rgba(255,255,255,0.80)">Serves 6 · 15 min</p>
                  </div>
                </div>

                <div
                  class="photo-card-c-dk relative rounded-[20px] overflow-hidden cursor-pointer aspect-[4/5]"
                  :style="{ boxShadow: SHADOW_RESTING_DK }"
                >
                  <img :src="PHOTOS.thai" alt="Thai basil chicken" class="photo-card-c-img absolute inset-0 w-full h-full object-cover" />
                  <div class="photo-card-scrim absolute inset-0" />
                  <div class="absolute inset-x-0 bottom-0 p-3 space-y-0.5">
                    <p class="text-[12px] font-semibold leading-snug" style="color:#FFFFFF; text-shadow:0 1px 3px rgba(0,0,0,0.40)">Thai basil chicken</p>
                    <p class="text-[10px]" style="color:rgba(255,255,255,0.80)">Serves 4 · 20 min</p>
                  </div>
                </div>

                <div
                  class="photo-card-c-dk relative rounded-[20px] overflow-hidden cursor-pointer aspect-[4/5]"
                  :style="{ boxShadow: SHADOW_RESTING_DK }"
                >
                  <img :src="PHOTOS.oats" alt="Blueberry overnight oats" class="photo-card-c-img absolute inset-0 w-full h-full object-cover" />
                  <div class="photo-card-scrim absolute inset-0" />
                  <div class="absolute inset-x-0 bottom-0 p-3 space-y-0.5">
                    <p class="text-[12px] font-semibold leading-snug" style="color:#FFFFFF; text-shadow:0 1px 3px rgba(0,0,0,0.40)">Blueberry overnight oats</p>
                    <p class="text-[10px]" style="color:rgba(255,255,255,0.80)">Serves 2 · 5 min</p>
                  </div>
                </div>

              </div>
            </div>

          </div><!-- /dark panel C -->

        </div>
      </VariantFrame>
    </section>


    <!-- ══════════════════════════════════════════════════════════════
         USAGE GUIDE
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-8">
      <div class="rounded-2xl border p-6 space-y-3" :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle }">
        <h2 class="text-[17px] font-semibold" :style="{ color: L.inkPrimary }">When to use PhotoCard</h2>

        <ul class="space-y-3 text-[14px]" :style="{ color: L.inkSecondary }">
          <li>
            <strong :style="{ color: L.inkPrimary }">Always paired with a real photo.</strong>
            PhotoCard is for image-led content — recipes, restaurants, events with photos, achievements with custom imagery. The photo is the point.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Scrim is always present.</strong>
            The bottom gradient scrim (transparent top → rgba(0,0,0,0.95) bottom) is non-optional — it guarantees legibility regardless of photo brightness, subject, or complexity. Text in the overlay also wears a <code class="font-mono text-[12px] px-1 py-0.5 rounded" :style="{ background: L.surfaceSunken }">text-shadow</code> for belt-and-suspenders safety on similar-colored photos.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Chips are optional.</strong>
            Use the top-right chip cluster when the card needs to communicate status or type at a glance (time, "New", "Favorite", category). Drop them when the image and title are sufficient — a PhotoCard without chips is equally valid.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Missing photo fallback:</strong>
            When a photo is not available, switch to GradientCard (2.3) with the content category's accent color. Never render a PhotoCard with a blank or broken image.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Lazy-load all photos.</strong>
            Add <code class="font-mono text-[12px] px-1 py-0.5 rounded" :style="{ background: L.surfaceSunken }">loading="lazy"</code> on all photo images. Use explicit width/height or aspect-ratio containers to prevent layout shift.
          </li>
        </ul>
      </div>
    </section>

  </ComponentPage>
</template>

<style scoped>
/*
  ═══════════════════════════════════════════════════════════════════
  GRADIENT SCRIM — shared across all overlay variants (B and C)
  Bottom-weighted so text at the bottom is always legible.
  Applied as a separate absolute div over the photo, under the text.
  ═══════════════════════════════════════════════════════════════════
*/
.photo-card-scrim {
  /* Starts fading in earlier (35%) and ramps to 0.95 black at the bottom
     so similar-colored or complex photos still guarantee text legibility. */
  background: linear-gradient(
    180deg,
    transparent 0%,
    transparent 35%,
    rgba(0, 0, 0, 0.28) 55%,
    rgba(0, 0, 0, 0.70) 82%,
    rgba(0, 0, 0, 0.95) 100%
  );
  pointer-events: none;
}

/*
  ═══════════════════════════════════════════════════════════════════
  CHIP OVERLAY — translucent white, works on any photo (dark or light)
  Backdrop blur adds glass depth without depending on bg color.
  ═══════════════════════════════════════════════════════════════════
*/
.photo-chip {
  background: rgba(255, 255, 255, 0.92);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
  border: 1px solid rgba(255, 255, 255, 0.60);
  color: #1C1C1E;
}

/*
  ═══════════════════════════════════════════════════════════════════
  VARIANT C — HOVER TRANSITIONS — LIGHT
  ═══════════════════════════════════════════════════════════════════
*/
.photo-card-c-lt {
  transition: box-shadow 200ms cubic-bezier(0.16, 1, 0.3, 1),
              transform 200ms cubic-bezier(0.16, 1, 0.3, 1);
}
.photo-card-c-lt:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(28, 20, 10, 0.08), 0 16px 32px rgba(28, 20, 10, 0.10);
}
.photo-card-c-lt:hover .photo-card-c-img {
  transform: scale(1.03);
}
.photo-card-c-img {
  transition: transform 400ms cubic-bezier(0.16, 1, 0.3, 1);
}

/*
  ═══════════════════════════════════════════════════════════════════
  VARIANT C — HOVER TRANSITIONS — DARK
  ═══════════════════════════════════════════════════════════════════
*/
.photo-card-c-dk {
  transition: box-shadow 200ms cubic-bezier(0.16, 1, 0.3, 1),
              transform 200ms cubic-bezier(0.16, 1, 0.3, 1);
}
.photo-card-c-dk:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.40), 0 16px 32px rgba(0, 0, 0, 0.30);
}
.photo-card-c-dk:hover .photo-card-c-img {
  transform: scale(1.03);
}

/*
  ═══════════════════════════════════════════════════════════════════
  REDUCED MOTION — disable all transform + shadow transitions
  Hover still works visually via box-shadow but without movement.
  ═══════════════════════════════════════════════════════════════════
*/
@media (prefers-reduced-motion: reduce) {
  .photo-card-a-lt,
  .photo-card-a-dk,
  .photo-card-b-lt,
  .photo-card-b-dk,
  .photo-card-c-lt,
  .photo-card-c-dk {
    transition: box-shadow 200ms;
    transform: none !important;
  }
  .photo-card-a-lt:hover .photo-card-a-img,
  .photo-card-a-dk:hover .photo-card-a-img,
  .photo-card-b-lt:hover .photo-card-b-img,
  .photo-card-b-dk:hover .photo-card-b-img,
  .photo-card-c-lt:hover .photo-card-c-img,
  .photo-card-c-dk:hover .photo-card-c-img {
    transform: none !important;
  }
}
</style>
