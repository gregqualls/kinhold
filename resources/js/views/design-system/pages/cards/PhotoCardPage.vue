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
    description="Image-led card for recipes, restaurants, events, and any content pairing with a real photo. Three variants — pick the treatment that best fits the context. When a photo is unavailable, always fall back to GradientCard (2.3) rather than rendering with a blank image."
    status="scaffolded"
  >

    <!-- ══════════════════════════════════════════════════════════════
         VARIANT A — Hero photo top (stacked)
         Photo fills the top ~60% with rounded-t corners.
         Content block on white/raised bg below.
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-20">
      <VariantFrame label="A" caption="Hero photo top, content below — stacked, editorial. Text always on its own surface — best legibility, no contrast risk.">
        <div class="w-full space-y-10">

          <!-- LIGHT PANEL A -->
          <div class="rounded-2xl border p-6 space-y-8" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>

            <!-- 1. Single hero card -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: L.inkTertiary }">Single hero — roasted salmon</p>
              <div class="max-w-md mx-auto">
                <div
                  class="photo-card-a-lt rounded-[20px] overflow-hidden cursor-pointer"
                  :style="{ background: L.surfaceRaised, boxShadow: SHADOW_RESTING_LT }"
                >
                  <!-- Photo: 16/10 aspect, top corners rounded via parent overflow-hidden -->
                  <div class="aspect-[16/10] w-full overflow-hidden">
                    <img
                      :src="PHOTOS.salmon"
                      alt="Roasted salmon with lemon-dill butter"
                      class="photo-card-a-img w-full h-full object-cover"
                    />
                  </div>
                  <!-- Content block -->
                  <div class="p-5 space-y-3">
                    <div class="space-y-1">
                      <p class="text-[17px] font-semibold leading-snug" :style="{ color: L.inkPrimary }">Roasted salmon with lemon-dill butter</p>
                      <p class="text-[13px]" :style="{ color: L.inkSecondary }">25 min · Serves 4 · 520 kcal</p>
                    </div>
                    <!-- Action row -->
                    <div class="flex items-center justify-between pt-1">
                      <div class="flex items-center gap-2">
                        <span
                          class="inline-flex items-center rounded-full h-6 px-2.5 text-[11px] font-medium border"
                          :style="{ background: L.accentLavenderSoft, color: L.accentLavenderBold, borderColor: L.accentLavenderBold }"
                        >Dinner</span>
                      </div>
                      <button
                        class="rounded-full px-4 py-1.5 text-[12px] font-semibold"
                        :style="{ background: L.inkPrimary, color: L.surfaceApp }"
                      >View recipe</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- 2. Row of 3 cards -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: L.inkTertiary }">Grid of 3 — recipe cards</p>
              <div class="grid grid-cols-3 gap-4">

                <!-- Burrito -->
                <div
                  class="photo-card-a-lt rounded-[20px] overflow-hidden cursor-pointer"
                  :style="{ background: L.surfaceRaised, boxShadow: SHADOW_RESTING_LT }"
                >
                  <div class="aspect-[4/5] w-full overflow-hidden">
                    <img :src="PHOTOS.burrito" alt="Breakfast burritos" class="photo-card-a-img w-full h-full object-cover" />
                  </div>
                  <div class="p-4 space-y-1">
                    <p class="text-[13px] font-semibold leading-snug" :style="{ color: L.inkPrimary }">Breakfast burritos</p>
                    <p class="text-[11px]" :style="{ color: L.inkSecondary }">15 min · Serves 6</p>
                  </div>
                </div>

                <!-- Thai basil -->
                <div
                  class="photo-card-a-lt rounded-[20px] overflow-hidden cursor-pointer"
                  :style="{ background: L.surfaceRaised, boxShadow: SHADOW_RESTING_LT }"
                >
                  <div class="aspect-[4/5] w-full overflow-hidden">
                    <img :src="PHOTOS.thai" alt="Thai basil chicken" class="photo-card-a-img w-full h-full object-cover" />
                  </div>
                  <div class="p-4 space-y-1">
                    <p class="text-[13px] font-semibold leading-snug" :style="{ color: L.inkPrimary }">Thai basil chicken</p>
                    <p class="text-[11px]" :style="{ color: L.inkSecondary }">20 min · Serves 4</p>
                  </div>
                </div>

                <!-- Oats -->
                <div
                  class="photo-card-a-lt rounded-[20px] overflow-hidden cursor-pointer"
                  :style="{ background: L.surfaceRaised, boxShadow: SHADOW_RESTING_LT }"
                >
                  <div class="aspect-[4/5] w-full overflow-hidden">
                    <img :src="PHOTOS.oats" alt="Blueberry overnight oats" class="photo-card-a-img w-full h-full object-cover" />
                  </div>
                  <div class="p-4 space-y-1">
                    <p class="text-[13px] font-semibold leading-snug" :style="{ color: L.inkPrimary }">Blueberry overnight oats</p>
                    <p class="text-[11px]" :style="{ color: L.inkSecondary }">5 min · Serves 2</p>
                  </div>
                </div>

              </div>
            </div>

            <!-- 3. Card with action footer -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: L.inkTertiary }">With action footer — save + view</p>
              <div class="max-w-xs">
                <div
                  class="photo-card-a-lt rounded-[20px] overflow-hidden cursor-pointer"
                  :style="{ background: L.surfaceRaised, boxShadow: SHADOW_RESTING_LT }"
                >
                  <div class="aspect-[16/10] w-full overflow-hidden">
                    <img :src="PHOTOS.salmon" alt="Roasted salmon" class="photo-card-a-img w-full h-full object-cover" />
                  </div>
                  <div class="p-4 space-y-3">
                    <div class="space-y-0.5">
                      <p class="text-[14px] font-semibold" :style="{ color: L.inkPrimary }">Roasted salmon with lemon-dill butter</p>
                      <p class="text-[11px]" :style="{ color: L.inkSecondary }">25 min · 520 kcal</p>
                    </div>
                    <!-- Divider -->
                    <div class="border-t" :style="{ borderColor: L.borderSubtle }" />
                    <!-- Action row -->
                    <div class="flex items-center gap-2">
                      <button
                        class="flex-1 rounded-full py-2 text-[12px] font-semibold border"
                        :style="{ borderColor: L.borderStrong, color: L.inkPrimary, background: L.surfaceRaised }"
                      >Save</button>
                      <button
                        class="flex-1 rounded-full py-2 text-[12px] font-semibold"
                        :style="{ background: L.inkPrimary, color: L.surfaceApp }"
                      >View recipe</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

          </div><!-- /light panel A -->

          <!-- DARK PANEL A -->
          <div class="rounded-2xl border p-6 space-y-8" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

            <!-- 1. Single hero card dark -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: D.inkTertiary }">Single hero — roasted salmon</p>
              <div class="max-w-md mx-auto">
                <div
                  class="photo-card-a-dk rounded-[20px] overflow-hidden cursor-pointer"
                  :style="{ background: D.surfaceRaised, boxShadow: SHADOW_RESTING_DK }"
                >
                  <div class="aspect-[16/10] w-full overflow-hidden">
                    <img :src="PHOTOS.salmon" alt="Roasted salmon with lemon-dill butter" class="photo-card-a-img w-full h-full object-cover" />
                  </div>
                  <div class="p-5 space-y-3">
                    <div class="space-y-1">
                      <p class="text-[17px] font-semibold leading-snug" :style="{ color: D.inkPrimary }">Roasted salmon with lemon-dill butter</p>
                      <p class="text-[13px]" :style="{ color: D.inkSecondary }">25 min · Serves 4 · 520 kcal</p>
                    </div>
                    <div class="flex items-center justify-between pt-1">
                      <div class="flex items-center gap-2">
                        <span
                          class="inline-flex items-center rounded-full h-6 px-2.5 text-[11px] font-medium border"
                          style="background: #2D2840; color: #B6A8E6; border-color: #B6A8E6"
                        >Dinner</span>
                      </div>
                      <button
                        class="rounded-full px-4 py-1.5 text-[12px] font-semibold"
                        :style="{ background: D.inkPrimary, color: D.surfaceApp }"
                      >View recipe</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- 2. Row of 3 dark -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: D.inkTertiary }">Grid of 3 — recipe cards</p>
              <div class="grid grid-cols-3 gap-4">

                <div
                  class="photo-card-a-dk rounded-[20px] overflow-hidden cursor-pointer"
                  :style="{ background: D.surfaceRaised, boxShadow: SHADOW_RESTING_DK }"
                >
                  <div class="aspect-[4/5] w-full overflow-hidden">
                    <img :src="PHOTOS.burrito" alt="Breakfast burritos" class="photo-card-a-img w-full h-full object-cover" />
                  </div>
                  <div class="p-4 space-y-1">
                    <p class="text-[13px] font-semibold leading-snug" :style="{ color: D.inkPrimary }">Breakfast burritos</p>
                    <p class="text-[11px]" :style="{ color: D.inkSecondary }">15 min · Serves 6</p>
                  </div>
                </div>

                <div
                  class="photo-card-a-dk rounded-[20px] overflow-hidden cursor-pointer"
                  :style="{ background: D.surfaceRaised, boxShadow: SHADOW_RESTING_DK }"
                >
                  <div class="aspect-[4/5] w-full overflow-hidden">
                    <img :src="PHOTOS.thai" alt="Thai basil chicken" class="photo-card-a-img w-full h-full object-cover" />
                  </div>
                  <div class="p-4 space-y-1">
                    <p class="text-[13px] font-semibold leading-snug" :style="{ color: D.inkPrimary }">Thai basil chicken</p>
                    <p class="text-[11px]" :style="{ color: D.inkSecondary }">20 min · Serves 4</p>
                  </div>
                </div>

                <div
                  class="photo-card-a-dk rounded-[20px] overflow-hidden cursor-pointer"
                  :style="{ background: D.surfaceRaised, boxShadow: SHADOW_RESTING_DK }"
                >
                  <div class="aspect-[4/5] w-full overflow-hidden">
                    <img :src="PHOTOS.oats" alt="Blueberry overnight oats" class="photo-card-a-img w-full h-full object-cover" />
                  </div>
                  <div class="p-4 space-y-1">
                    <p class="text-[13px] font-semibold leading-snug" :style="{ color: D.inkPrimary }">Blueberry overnight oats</p>
                    <p class="text-[11px]" :style="{ color: D.inkSecondary }">5 min · Serves 2</p>
                  </div>
                </div>

              </div>
            </div>

            <!-- 3. Action footer dark -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: D.inkTertiary }">With action footer</p>
              <div class="max-w-xs">
                <div
                  class="photo-card-a-dk rounded-[20px] overflow-hidden cursor-pointer"
                  :style="{ background: D.surfaceRaised, boxShadow: SHADOW_RESTING_DK }"
                >
                  <div class="aspect-[16/10] w-full overflow-hidden">
                    <img :src="PHOTOS.salmon" alt="Roasted salmon" class="photo-card-a-img w-full h-full object-cover" />
                  </div>
                  <div class="p-4 space-y-3">
                    <div class="space-y-0.5">
                      <p class="text-[14px] font-semibold" :style="{ color: D.inkPrimary }">Roasted salmon with lemon-dill butter</p>
                      <p class="text-[11px]" :style="{ color: D.inkSecondary }">25 min · 520 kcal</p>
                    </div>
                    <div class="border-t" :style="{ borderColor: D.borderSubtle }" />
                    <div class="flex items-center gap-2">
                      <button
                        class="flex-1 rounded-full py-2 text-[12px] font-semibold border"
                        :style="{ borderColor: D.borderStrong, color: D.inkPrimary, background: D.surfaceRaised }"
                      >Save</button>
                      <button
                        class="flex-1 rounded-full py-2 text-[12px] font-semibold"
                        :style="{ background: D.inkPrimary, color: D.surfaceSunken }"
                      >View recipe</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

          </div><!-- /dark panel A -->

        </div>
      </VariantFrame>
    </section>


    <!-- ══════════════════════════════════════════════════════════════
         VARIANT B — Edge-to-edge photo with bottom overlay
         Photo fills the entire card. Title + meta overlaid on gradient scrim.
         Real-estate / recipe inspiration.
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-20">
      <VariantFrame label="B" caption="Edge-to-edge photo with gradient-scrim overlay — real-estate / recipe pattern. Maximum image impact; text legibility via scrim.">
        <div class="w-full space-y-10">

          <!-- LIGHT PANEL B -->
          <div class="rounded-2xl border p-6 space-y-8" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>

            <!-- 1. Single hero card B -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: L.inkTertiary }">Single hero — roasted salmon</p>
              <div class="max-w-md mx-auto">
                <div
                  class="photo-card-b-lt relative rounded-[20px] overflow-hidden cursor-pointer aspect-[4/5]"
                  :style="{ boxShadow: SHADOW_RESTING_LT }"
                >
                  <!-- Full-bleed photo -->
                  <img
                    :src="PHOTOS.salmon"
                    alt="Roasted salmon with lemon-dill butter"
                    class="photo-card-b-img absolute inset-0 w-full h-full object-cover"
                  />
                  <!-- Gradient scrim -->
                  <div class="photo-card-scrim absolute inset-0" />
                  <!-- Text overlay -->
                  <div class="absolute inset-x-0 bottom-0 p-5 space-y-1">
                    <p
                      class="text-[19px] font-semibold leading-snug"
                      style="color: #FFFFFF; text-shadow: 0 1px 3px rgba(0,0,0,0.40)"
                    >Roasted salmon with lemon-dill butter</p>
                    <p
                      class="text-[13px]"
                      style="color: rgba(255,255,255,0.82); text-shadow: 0 1px 2px rgba(0,0,0,0.30)"
                    >25 min · Serves 4 · 520 kcal</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- 2. Row of 3 cards B -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: L.inkTertiary }">Grid of 3 — recipe cards</p>
              <div class="grid grid-cols-3 gap-4">

                <div
                  class="photo-card-b-lt relative rounded-[20px] overflow-hidden cursor-pointer aspect-[4/5]"
                  :style="{ boxShadow: SHADOW_RESTING_LT }"
                >
                  <img :src="PHOTOS.burrito" alt="Breakfast burritos" class="photo-card-b-img absolute inset-0 w-full h-full object-cover" />
                  <div class="photo-card-scrim absolute inset-0" />
                  <div class="absolute inset-x-0 bottom-0 p-4 space-y-0.5">
                    <p class="text-[13px] font-semibold leading-snug" style="color:#FFFFFF; text-shadow:0 1px 3px rgba(0,0,0,0.40)">Breakfast burritos</p>
                    <p class="text-[11px]" style="color:rgba(255,255,255,0.80); text-shadow:0 1px 2px rgba(0,0,0,0.30)">15 min · Serves 6</p>
                  </div>
                </div>

                <div
                  class="photo-card-b-lt relative rounded-[20px] overflow-hidden cursor-pointer aspect-[4/5]"
                  :style="{ boxShadow: SHADOW_RESTING_LT }"
                >
                  <img :src="PHOTOS.thai" alt="Thai basil chicken" class="photo-card-b-img absolute inset-0 w-full h-full object-cover" />
                  <div class="photo-card-scrim absolute inset-0" />
                  <div class="absolute inset-x-0 bottom-0 p-4 space-y-0.5">
                    <p class="text-[13px] font-semibold leading-snug" style="color:#FFFFFF; text-shadow:0 1px 3px rgba(0,0,0,0.40)">Thai basil chicken</p>
                    <p class="text-[11px]" style="color:rgba(255,255,255,0.80); text-shadow:0 1px 2px rgba(0,0,0,0.30)">20 min · Serves 4</p>
                  </div>
                </div>

                <div
                  class="photo-card-b-lt relative rounded-[20px] overflow-hidden cursor-pointer aspect-[4/5]"
                  :style="{ boxShadow: SHADOW_RESTING_LT }"
                >
                  <img :src="PHOTOS.oats" alt="Blueberry overnight oats" class="photo-card-b-img absolute inset-0 w-full h-full object-cover" />
                  <div class="photo-card-scrim absolute inset-0" />
                  <div class="absolute inset-x-0 bottom-0 p-4 space-y-0.5">
                    <p class="text-[13px] font-semibold leading-snug" style="color:#FFFFFF; text-shadow:0 1px 3px rgba(0,0,0,0.40)">Blueberry overnight oats</p>
                    <p class="text-[11px]" style="color:rgba(255,255,255,0.80); text-shadow:0 1px 2px rgba(0,0,0,0.30)">5 min · Serves 2</p>
                  </div>
                </div>

              </div>
            </div>

            <!-- 3. Card with action pinned bottom-right B -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: L.inkTertiary }">With action button in overlay</p>
              <div class="max-w-xs">
                <div
                  class="photo-card-b-lt relative rounded-[20px] overflow-hidden cursor-pointer aspect-[4/5]"
                  :style="{ boxShadow: SHADOW_RESTING_LT }"
                >
                  <img :src="PHOTOS.salmon" alt="Roasted salmon" class="photo-card-b-img absolute inset-0 w-full h-full object-cover" />
                  <div class="photo-card-scrim absolute inset-0" />
                  <div class="absolute inset-x-0 bottom-0 p-4 space-y-3">
                    <div class="space-y-0.5">
                      <p class="text-[14px] font-semibold leading-snug" style="color:#FFFFFF; text-shadow:0 1px 3px rgba(0,0,0,0.40)">Roasted salmon with lemon-dill butter</p>
                      <p class="text-[11px]" style="color:rgba(255,255,255,0.80)">25 min · 520 kcal</p>
                    </div>
                    <button
                      class="w-full rounded-full py-2 text-[12px] font-semibold"
                      style="background: rgba(255,255,255,0.95); color: #1C1C1E;"
                    >View recipe</button>
                  </div>
                </div>
              </div>
            </div>

          </div><!-- /light panel B -->

          <!-- DARK PANEL B -->
          <div class="rounded-2xl border p-6 space-y-8" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>
            <!-- Note: B/C cards are photo-filled, so dark panel styling is almost identical —
                 only the panel bg and label color change. The scrim + photo are photo-dependent. -->

            <!-- 1. Single hero B dark -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: D.inkTertiary }">Single hero — roasted salmon</p>
              <div class="max-w-md mx-auto">
                <div
                  class="photo-card-b-dk relative rounded-[20px] overflow-hidden cursor-pointer aspect-[4/5]"
                  :style="{ boxShadow: SHADOW_RESTING_DK }"
                >
                  <img :src="PHOTOS.salmon" alt="Roasted salmon with lemon-dill butter" class="photo-card-b-img absolute inset-0 w-full h-full object-cover" />
                  <div class="photo-card-scrim absolute inset-0" />
                  <div class="absolute inset-x-0 bottom-0 p-5 space-y-1">
                    <p class="text-[19px] font-semibold leading-snug" style="color: #FFFFFF; text-shadow: 0 1px 3px rgba(0,0,0,0.50)">Roasted salmon with lemon-dill butter</p>
                    <p class="text-[13px]" style="color: rgba(255,255,255,0.82); text-shadow: 0 1px 2px rgba(0,0,0,0.40)">25 min · Serves 4 · 520 kcal</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- 2. Row of 3 B dark -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: D.inkTertiary }">Grid of 3 — recipe cards</p>
              <div class="grid grid-cols-3 gap-4">

                <div
                  class="photo-card-b-dk relative rounded-[20px] overflow-hidden cursor-pointer aspect-[4/5]"
                  :style="{ boxShadow: SHADOW_RESTING_DK }"
                >
                  <img :src="PHOTOS.burrito" alt="Breakfast burritos" class="photo-card-b-img absolute inset-0 w-full h-full object-cover" />
                  <div class="photo-card-scrim absolute inset-0" />
                  <div class="absolute inset-x-0 bottom-0 p-4 space-y-0.5">
                    <p class="text-[13px] font-semibold leading-snug" style="color:#FFFFFF; text-shadow:0 1px 3px rgba(0,0,0,0.50)">Breakfast burritos</p>
                    <p class="text-[11px]" style="color:rgba(255,255,255,0.80)">15 min · Serves 6</p>
                  </div>
                </div>

                <div
                  class="photo-card-b-dk relative rounded-[20px] overflow-hidden cursor-pointer aspect-[4/5]"
                  :style="{ boxShadow: SHADOW_RESTING_DK }"
                >
                  <img :src="PHOTOS.thai" alt="Thai basil chicken" class="photo-card-b-img absolute inset-0 w-full h-full object-cover" />
                  <div class="photo-card-scrim absolute inset-0" />
                  <div class="absolute inset-x-0 bottom-0 p-4 space-y-0.5">
                    <p class="text-[13px] font-semibold leading-snug" style="color:#FFFFFF; text-shadow:0 1px 3px rgba(0,0,0,0.50)">Thai basil chicken</p>
                    <p class="text-[11px]" style="color:rgba(255,255,255,0.80)">20 min · Serves 4</p>
                  </div>
                </div>

                <div
                  class="photo-card-b-dk relative rounded-[20px] overflow-hidden cursor-pointer aspect-[4/5]"
                  :style="{ boxShadow: SHADOW_RESTING_DK }"
                >
                  <img :src="PHOTOS.oats" alt="Blueberry overnight oats" class="photo-card-b-img absolute inset-0 w-full h-full object-cover" />
                  <div class="photo-card-scrim absolute inset-0" />
                  <div class="absolute inset-x-0 bottom-0 p-4 space-y-0.5">
                    <p class="text-[13px] font-semibold leading-snug" style="color:#FFFFFF; text-shadow:0 1px 3px rgba(0,0,0,0.50)">Blueberry overnight oats</p>
                    <p class="text-[11px]" style="color:rgba(255,255,255,0.80)">5 min · Serves 2</p>
                  </div>
                </div>

              </div>
            </div>

            <!-- 3. Action B dark -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: D.inkTertiary }">With action button in overlay</p>
              <div class="max-w-xs">
                <div
                  class="photo-card-b-dk relative rounded-[20px] overflow-hidden cursor-pointer aspect-[4/5]"
                  :style="{ boxShadow: SHADOW_RESTING_DK }"
                >
                  <img :src="PHOTOS.salmon" alt="Roasted salmon" class="photo-card-b-img absolute inset-0 w-full h-full object-cover" />
                  <div class="photo-card-scrim absolute inset-0" />
                  <div class="absolute inset-x-0 bottom-0 p-4 space-y-3">
                    <div class="space-y-0.5">
                      <p class="text-[14px] font-semibold leading-snug" style="color:#FFFFFF; text-shadow:0 1px 3px rgba(0,0,0,0.50)">Roasted salmon with lemon-dill butter</p>
                      <p class="text-[11px]" style="color:rgba(255,255,255,0.80)">25 min · 520 kcal</p>
                    </div>
                    <button
                      class="w-full rounded-full py-2 text-[12px] font-semibold"
                      style="background: rgba(255,255,255,0.95); color: #1C1C1E;"
                    >View recipe</button>
                  </div>
                </div>
              </div>
            </div>

          </div><!-- /dark panel B -->

        </div>
      </VariantFrame>
    </section>


    <!-- ══════════════════════════════════════════════════════════════
         VARIANT C — Photo-first with chip badges overlaid top-right
         Same edge-to-edge as B, but adds translucent chip badges
         in the top-right corner. Status at a glance.
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-20">
      <VariantFrame label="C" caption="Photo-first with chip badges overlaid top-right — adds instant status/type affordance without leaving the image surface.">
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

            <!-- 3. Multi-chip hero C -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: L.inkTertiary }">Multi-chip stacked — recipe with status + category</p>
              <div class="max-w-xs">
                <div
                  class="photo-card-c-lt relative rounded-[20px] overflow-hidden cursor-pointer aspect-[4/5]"
                  :style="{ boxShadow: SHADOW_RESTING_LT }"
                >
                  <img :src="PHOTOS.salmon" alt="Roasted salmon" class="photo-card-c-img absolute inset-0 w-full h-full object-cover" />
                  <div class="photo-card-scrim absolute inset-0" />
                  <!-- Stacked chip cluster top-right -->
                  <div class="absolute top-4 right-4 flex flex-col items-end gap-1.5">
                    <span class="photo-chip inline-flex items-center gap-1 rounded-full h-6 px-2.5 text-[10px] font-semibold">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3 h-3 flex-shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                      </svg>
                      25 min
                    </span>
                    <span class="photo-chip inline-flex items-center rounded-full h-6 px-2.5 text-[10px] font-semibold">
                      Dinner
                    </span>
                    <span class="photo-chip inline-flex items-center gap-1 rounded-full h-6 px-2.5 text-[10px] font-semibold">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-3 h-3 flex-shrink-0" style="color: #F59E0B">
                        <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd" />
                      </svg>
                      Favorite
                    </span>
                  </div>
                  <div class="absolute inset-x-0 bottom-0 p-4 space-y-3">
                    <div class="space-y-0.5">
                      <p class="text-[14px] font-semibold leading-snug" style="color:#FFFFFF; text-shadow:0 1px 3px rgba(0,0,0,0.40)">Roasted salmon with lemon-dill butter</p>
                      <p class="text-[11px]" style="color:rgba(255,255,255,0.80)">25 min · 520 kcal</p>
                    </div>
                    <button
                      class="w-full rounded-full py-2 text-[12px] font-semibold"
                      style="background: rgba(255,255,255,0.95); color: #1C1C1E;"
                    >View recipe</button>
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

            <!-- 3. Multi-chip C dark -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: D.inkTertiary }">Multi-chip stacked — recipe with status + category</p>
              <div class="max-w-xs">
                <div
                  class="photo-card-c-dk relative rounded-[20px] overflow-hidden cursor-pointer aspect-[4/5]"
                  :style="{ boxShadow: SHADOW_RESTING_DK }"
                >
                  <img :src="PHOTOS.salmon" alt="Roasted salmon" class="photo-card-c-img absolute inset-0 w-full h-full object-cover" />
                  <div class="photo-card-scrim absolute inset-0" />
                  <div class="absolute top-4 right-4 flex flex-col items-end gap-1.5">
                    <span class="photo-chip inline-flex items-center gap-1 rounded-full h-6 px-2.5 text-[10px] font-semibold">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3 h-3 flex-shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                      </svg>
                      25 min
                    </span>
                    <span class="photo-chip inline-flex items-center rounded-full h-6 px-2.5 text-[10px] font-semibold">
                      Dinner
                    </span>
                    <span class="photo-chip inline-flex items-center gap-1 rounded-full h-6 px-2.5 text-[10px] font-semibold">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-3 h-3 flex-shrink-0" style="color: #F59E0B">
                        <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd" />
                      </svg>
                      Favorite
                    </span>
                  </div>
                  <div class="absolute inset-x-0 bottom-0 p-4 space-y-3">
                    <div class="space-y-0.5">
                      <p class="text-[14px] font-semibold leading-snug" style="color:#FFFFFF; text-shadow:0 1px 3px rgba(0,0,0,0.50)">Roasted salmon with lemon-dill butter</p>
                      <p class="text-[11px]" style="color:rgba(255,255,255,0.80)">25 min · 520 kcal</p>
                    </div>
                    <button
                      class="w-full rounded-full py-2 text-[12px] font-semibold"
                      style="background: rgba(255,255,255,0.95); color: #1C1C1E;"
                    >View recipe</button>
                  </div>
                </div>
              </div>
            </div>

          </div><!-- /dark panel C -->

        </div>
      </VariantFrame>
    </section>


    <!-- ══════════════════════════════════════════════════════════════
         USAGE GUIDE + DECISION HELPER
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-8">
      <div class="rounded-2xl border p-6 space-y-6" :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle }">
        <h2 class="text-[17px] font-semibold" :style="{ color: L.inkPrimary }">When to use PhotoCard</h2>

        <ul class="space-y-3 text-[14px]" :style="{ color: L.inkSecondary }">
          <li>
            <strong :style="{ color: L.inkPrimary }">Always paired with a real photo.</strong>
            PhotoCard is for image-led content — recipes, restaurants, events with photos, achievements with custom imagery. The photo is the point.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Missing photo fallback:</strong>
            When a photo is not available, switch to GradientCard (2.3) with the recipe category's accent color. Never render a PhotoCard with a blank or broken image — it degrades to an empty rectangle with unreadable overlay text.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Lazy-load all photos.</strong>
            Add <code class="font-mono text-[12px] px-1 py-0.5 rounded" :style="{ background: L.surfaceSunken }">loading="lazy"</code> on all photo images. Use explicit width/height or aspect-ratio containers to prevent layout shift.
          </li>
        </ul>

        <div class="border-t pt-5 space-y-4" :style="{ borderColor: L.borderSubtle }">
          <h3 class="text-[15px] font-semibold" :style="{ color: L.inkPrimary }">Decision criteria</h3>
          <div class="space-y-3 text-[14px]" :style="{ color: L.inkSecondary }">
            <div class="flex gap-3">
              <span class="flex-shrink-0 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold mt-0.5" :style="{ background: L.surfaceSunken, color: L.inkPrimary }">A</span>
              <div>
                <strong :style="{ color: L.inkPrimary }">Text legibility:</strong> A wins — content lives on its own raised surface. Zero contrast risk regardless of photo content. Best for dense recipe lists or any context where scannable text matters more than image impact.
              </div>
            </div>
            <div class="flex gap-3">
              <span class="flex-shrink-0 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold mt-0.5" :style="{ background: L.surfaceSunken, color: L.inkPrimary }">B</span>
              <div>
                <strong :style="{ color: L.inkPrimary }">Image impact:</strong> B wins — photo dominates the full card surface, feels richest and most editorial. The real-estate / recipe-site pattern. Best for large hero moments (meal plan day cover, restaurant feature, event spotlight) where the photo is the draw.
              </div>
            </div>
            <div class="flex gap-3">
              <span class="flex-shrink-0 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold mt-0.5" :style="{ background: L.surfaceSunken, color: L.inkPrimary }">C</span>
              <div>
                <strong :style="{ color: L.inkPrimary }">Status affordance:</strong> C wins — chip badges communicate state (time, type, favorite, new) before the user reads a word. Best for recipe/restaurant grids where metadata needs to surface without opening the card. Chips use translucent-white so they read on any photo.
              </div>
            </div>
            <div class="pt-1" :style="{ color: L.inkSecondary }">
              <strong :style="{ color: L.inkPrimary }">Grid density note:</strong> Variant A is most layout-stable — the fixed-height text block below the photo keeps card heights consistent across a row even with different title lengths. B and C can vary slightly by photo brightness but stay visually consistent because the scrim normalizes everything.
            </div>
          </div>
        </div>

        <div class="border-t pt-5" :style="{ borderColor: L.borderSubtle }">
          <p class="text-[15px] font-semibold" :style="{ color: L.inkPrimary }">Reply <strong>A</strong>, <strong>B</strong>, or <strong>C</strong> to lock the PhotoCard treatment.</p>
        </div>
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
  background: linear-gradient(
    180deg,
    transparent 0%,
    transparent 45%,
    rgba(0, 0, 0, 0.20) 60%,
    rgba(0, 0, 0, 0.85) 100%
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
  VARIANT A — HOVER TRANSITIONS — LIGHT
  box-shadow + transform + inner img zoom
  ═══════════════════════════════════════════════════════════════════
*/
.photo-card-a-lt {
  transition: box-shadow 200ms cubic-bezier(0.16, 1, 0.3, 1),
              transform 200ms cubic-bezier(0.16, 1, 0.3, 1);
}
.photo-card-a-lt:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(28, 20, 10, 0.08), 0 16px 32px rgba(28, 20, 10, 0.10);
}
.photo-card-a-lt:hover .photo-card-a-img {
  transform: scale(1.03);
}
.photo-card-a-img {
  transition: transform 400ms cubic-bezier(0.16, 1, 0.3, 1);
}

/*
  ═══════════════════════════════════════════════════════════════════
  VARIANT A — HOVER TRANSITIONS — DARK
  ═══════════════════════════════════════════════════════════════════
*/
.photo-card-a-dk {
  transition: box-shadow 200ms cubic-bezier(0.16, 1, 0.3, 1),
              transform 200ms cubic-bezier(0.16, 1, 0.3, 1);
}
.photo-card-a-dk:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.40), 0 16px 32px rgba(0, 0, 0, 0.30);
}
.photo-card-a-dk:hover .photo-card-a-img {
  transform: scale(1.03);
}

/*
  ═══════════════════════════════════════════════════════════════════
  VARIANT B — HOVER TRANSITIONS — LIGHT
  ═══════════════════════════════════════════════════════════════════
*/
.photo-card-b-lt {
  transition: box-shadow 200ms cubic-bezier(0.16, 1, 0.3, 1),
              transform 200ms cubic-bezier(0.16, 1, 0.3, 1);
}
.photo-card-b-lt:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(28, 20, 10, 0.08), 0 16px 32px rgba(28, 20, 10, 0.10);
}
.photo-card-b-lt:hover .photo-card-b-img {
  transform: scale(1.03);
}
.photo-card-b-img {
  transition: transform 400ms cubic-bezier(0.16, 1, 0.3, 1);
}

/*
  ═══════════════════════════════════════════════════════════════════
  VARIANT B — HOVER TRANSITIONS — DARK
  ═══════════════════════════════════════════════════════════════════
*/
.photo-card-b-dk {
  transition: box-shadow 200ms cubic-bezier(0.16, 1, 0.3, 1),
              transform 200ms cubic-bezier(0.16, 1, 0.3, 1);
}
.photo-card-b-dk:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.40), 0 16px 32px rgba(0, 0, 0, 0.30);
}
.photo-card-b-dk:hover .photo-card-b-img {
  transform: scale(1.03);
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
