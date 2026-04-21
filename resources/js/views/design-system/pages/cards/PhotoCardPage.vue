<script setup>
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import KinPhotoCard from '@/components/design-system/KinPhotoCard.vue'

// ── Panel chrome tokens (Light/Dark hand-rolled wrappers) ────────────────────
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
const D = {
  surfaceApp:    '#141311',
  inkPrimary:    '#F0EDE9',
  inkSecondary:  '#A09C97',
  inkTertiary:   '#6E6B67',
  borderSubtle:  '#2C2A27',
}

// ── Photo sources — picsum.photos with food-themed seeds ─────────────────────
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
         PHOTOCARD — Edge-to-edge photo + gradient scrim + optional chips
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-20">
      <VariantFrame label="PhotoCard" caption="Edge-to-edge photo + gradient scrim + optional top-right chip badges. Scrim handles text legibility on any photo; chips are opt-in per card.">
        <div class="w-full space-y-10">

          <!-- LIGHT PANEL -->
          <div class="rounded-2xl border p-6 space-y-8" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>

            <!-- 1. Single hero -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: L.inkTertiary }">Single hero — roasted salmon with status chip</p>
              <div class="max-w-md mx-auto">
                <KinPhotoCard
                  :src="PHOTOS.salmon"
                  alt="Roasted salmon with lemon-dill butter"
                  aspect="tall"
                  title="Roasted salmon with lemon-dill butter"
                  subtitle="Serves 4 · 520 kcal"
                  overlaySize="lg"
                  interactive
                >
                  <template #badges>
                    <span class="photo-chip inline-flex items-center gap-1.5 rounded-full h-7 px-3 text-[11px] font-semibold">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5 flex-shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                      </svg>
                      25 min
                    </span>
                    <span class="photo-chip inline-flex items-center rounded-full h-7 px-3 text-[11px] font-semibold">
                      Dinner
                    </span>
                  </template>
                </KinPhotoCard>
              </div>
            </div>

            <!-- 2. Row of 3 — each with different chip badge -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: L.inkTertiary }">Grid of 3 — different chip types per card</p>
              <div class="grid grid-cols-3 gap-4">

                <!-- Burrito — time chip -->
                <KinPhotoCard
                  :src="PHOTOS.burrito"
                  alt="Breakfast burritos"
                  aspect="tall"
                  title="Breakfast burritos"
                  subtitle="Serves 6"
                  overlaySize="sm"
                  interactive
                >
                  <template #badges>
                    <span class="photo-chip inline-flex items-center gap-1 rounded-full h-6 px-2.5 text-[10px] font-semibold">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3 h-3 flex-shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                      </svg>
                      15 min
                    </span>
                  </template>
                </KinPhotoCard>

                <!-- Thai — "New" chip -->
                <KinPhotoCard
                  :src="PHOTOS.thai"
                  alt="Thai basil chicken"
                  aspect="tall"
                  title="Thai basil chicken"
                  subtitle="Serves 4"
                  overlaySize="sm"
                  interactive
                >
                  <template #badges>
                    <span class="photo-chip inline-flex items-center rounded-full h-6 px-2.5 text-[10px] font-semibold">
                      New
                    </span>
                  </template>
                </KinPhotoCard>

                <!-- Oats — "Favorite" chip with star icon -->
                <KinPhotoCard
                  :src="PHOTOS.oats"
                  alt="Blueberry overnight oats"
                  aspect="tall"
                  title="Overnight oats"
                  subtitle="Serves 2"
                  overlaySize="sm"
                  interactive
                >
                  <template #badges>
                    <span class="photo-chip inline-flex items-center gap-1 rounded-full h-6 px-2.5 text-[10px] font-semibold">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-3 h-3 flex-shrink-0" style="color: #F59E0B">
                        <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd" />
                      </svg>
                      Favorite
                    </span>
                  </template>
                </KinPhotoCard>

              </div>
            </div>

            <!-- 3. No-chip treatment — scrim alone handles legibility -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: L.inkTertiary }">No chips — the scrim alone handles legibility when status isn't needed</p>
              <div class="grid grid-cols-3 gap-4">
                <KinPhotoCard :src="PHOTOS.burrito" alt="Breakfast burritos" aspect="tall" title="Breakfast burritos" subtitle="Serves 6 · 15 min" overlaySize="sm" interactive />
                <KinPhotoCard :src="PHOTOS.thai" alt="Thai basil chicken" aspect="tall" title="Thai basil chicken" subtitle="Serves 4 · 20 min" overlaySize="sm" interactive />
                <KinPhotoCard :src="PHOTOS.oats" alt="Blueberry overnight oats" aspect="tall" title="Blueberry overnight oats" subtitle="Serves 2 · 5 min" overlaySize="sm" interactive />
              </div>
            </div>

          </div><!-- /light panel -->

          <!-- DARK PANEL -->
          <div class="dark rounded-2xl border p-6 space-y-8" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>
            <!-- Chip treatment is identical in dark mode — white translucent chip on photo always works -->

            <!-- 1. Single hero dark -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: D.inkTertiary }">Single hero — roasted salmon with status chip</p>
              <div class="max-w-md mx-auto">
                <KinPhotoCard
                  :src="PHOTOS.salmon"
                  alt="Roasted salmon with lemon-dill butter"
                  aspect="tall"
                  title="Roasted salmon with lemon-dill butter"
                  subtitle="Serves 4 · 520 kcal"
                  overlaySize="lg"
                  interactive
                >
                  <template #badges>
                    <span class="photo-chip inline-flex items-center gap-1.5 rounded-full h-7 px-3 text-[11px] font-semibold">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5 flex-shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                      </svg>
                      25 min
                    </span>
                    <span class="photo-chip inline-flex items-center rounded-full h-7 px-3 text-[11px] font-semibold">
                      Dinner
                    </span>
                  </template>
                </KinPhotoCard>
              </div>
            </div>

            <!-- 2. Row of 3 dark -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: D.inkTertiary }">Grid of 3 — different chip types per card</p>
              <div class="grid grid-cols-3 gap-4">

                <KinPhotoCard :src="PHOTOS.burrito" alt="Breakfast burritos" aspect="tall" title="Breakfast burritos" subtitle="Serves 6" overlaySize="sm" interactive>
                  <template #badges>
                    <span class="photo-chip inline-flex items-center gap-1 rounded-full h-6 px-2.5 text-[10px] font-semibold">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3 h-3 flex-shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                      </svg>
                      15 min
                    </span>
                  </template>
                </KinPhotoCard>

                <KinPhotoCard :src="PHOTOS.thai" alt="Thai basil chicken" aspect="tall" title="Thai basil chicken" subtitle="Serves 4" overlaySize="sm" interactive>
                  <template #badges>
                    <span class="photo-chip inline-flex items-center rounded-full h-6 px-2.5 text-[10px] font-semibold">
                      New
                    </span>
                  </template>
                </KinPhotoCard>

                <KinPhotoCard :src="PHOTOS.oats" alt="Blueberry overnight oats" aspect="tall" title="Overnight oats" subtitle="Serves 2" overlaySize="sm" interactive>
                  <template #badges>
                    <span class="photo-chip inline-flex items-center gap-1 rounded-full h-6 px-2.5 text-[10px] font-semibold">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-3 h-3 flex-shrink-0" style="color: #F59E0B">
                        <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd" />
                      </svg>
                      Favorite
                    </span>
                  </template>
                </KinPhotoCard>

              </div>
            </div>

            <!-- 3. No-chip dark -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: D.inkTertiary }">No chips — the scrim alone handles legibility when status isn't needed</p>
              <div class="grid grid-cols-3 gap-4">
                <KinPhotoCard :src="PHOTOS.burrito" alt="Breakfast burritos" aspect="tall" title="Breakfast burritos" subtitle="Serves 6 · 15 min" overlaySize="sm" interactive />
                <KinPhotoCard :src="PHOTOS.thai" alt="Thai basil chicken" aspect="tall" title="Thai basil chicken" subtitle="Serves 4 · 20 min" overlaySize="sm" interactive />
                <KinPhotoCard :src="PHOTOS.oats" alt="Blueberry overnight oats" aspect="tall" title="Blueberry overnight oats" subtitle="Serves 2 · 5 min" overlaySize="sm" interactive />
              </div>
            </div>

          </div><!-- /dark panel -->

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
            Use the top-right chip cluster (via <code class="font-mono text-[12px] px-1 py-0.5 rounded" :style="{ background: L.surfaceSunken }">#badges</code> slot) when the card needs to communicate status or type at a glance (time, "New", "Favorite", category). Drop them when the image and title are sufficient — a PhotoCard without chips is equally valid.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Missing photo fallback:</strong>
            When a photo is not available, switch to GradientCard (2.3) with the content category's accent color. Never render a PhotoCard with a blank or broken image.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Lazy-load all photos.</strong>
            The component defaults to <code class="font-mono text-[12px] px-1 py-0.5 rounded" :style="{ background: L.surfaceSunken }">loading="lazy"</code>. Aspect-ratio containers prevent layout shift.
          </li>
        </ul>
      </div>
    </section>

  </ComponentPage>
</template>

<style scoped>
/*
  ═══════════════════════════════════════════════════════════════════
  CHIP OVERLAY — translucent white, works on any photo (dark or light)
  Backdrop blur adds glass depth without depending on bg color.
  Scoped to the demo page; consumers in real app views can use KinChip
  or any custom chip element inside the #badges slot.
  ═══════════════════════════════════════════════════════════════════
*/
.photo-chip {
  background: rgba(255, 255, 255, 0.92);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
  border: 1px solid rgba(255, 255, 255, 0.60);
  color: #1C1C1E;
}
</style>
