<script setup>
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import {
  SparklesIcon, ArrowLeftIcon, ShareIcon, BookmarkIcon,
  ClockIcon, FireIcon, ChartBarIcon,
} from '@heroicons/vue/24/outline'
import KinHeroPhotoSheet from '@/components/design-system/KinHeroPhotoSheet.vue'

// ── Palette ───────────────────────────────────────────────────────────────────
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
}
const SHADOW_SHEET_LT = '0 -4px 24px rgba(28, 20, 10, 0.08), 0 -8px 40px rgba(28, 20, 10, 0.04)'
const SHADOW_SHEET_DK = '0 -4px 24px rgba(0, 0, 0, 0.50), 0 -8px 40px rgba(0, 0, 0, 0.30)'

// Frosted button styles
const frostBtnLight = {
  width: '40px', height: '40px', borderRadius: '50%',
  background: 'rgba(255,255,255,0.75)',
  backdropFilter: 'blur(12px)',
  WebkitBackdropFilter: 'blur(12px)',
  boxShadow: '0 2px 8px rgba(0,0,0,0.18)',
  display: 'flex', alignItems: 'center', justifyContent: 'center',
  flexShrink: 0,
}
const frostBtnDark = {
  width: '40px', height: '40px', borderRadius: '50%',
  background: 'rgba(28,27,25,0.65)',
  backdropFilter: 'blur(12px)',
  WebkitBackdropFilter: 'blur(12px)',
  boxShadow: '0 2px 8px rgba(0,0,0,0.40)',
  display: 'flex', alignItems: 'center', justifyContent: 'center',
  flexShrink: 0,
}

// Photo fallback gradient (used as CSS background when image fails or as bg-image)
const PHOTO_GRADIENT = 'linear-gradient(160deg, #BA562E 0%, #A2780C 100%)'
const HERO_PHOTO_URL = 'https://images.unsplash.com/photo-1546548970-71785318a17b?w=800'
</script>

<template>
  <ComponentPage
    title="5.15 HeroPhotoSheet"
    description="Flagship detail-page treatment — edge-to-edge hero photo with frosted circular back/action buttons floating over it, and a content sheet that slides up from below with a parallax offset. Used for recipe detail, restaurant detail, any image-led detail view. Note: would also be great for the Rewards store detail pages — reward cover photo as hero, redeem/details in the sheet."
    status="chosen"
  >

    <!-- ═══════════════════════════════════════════════════════════════
         VARIANT C — Parallax-scroll treatment (static mock: 2 states)
         Left: initial view (photo full). Right: scrolled view (sheet risen).
         ════════════════════════════════════════════════════════════════ -->
    <section class="mb-20">
      <VariantFrame
        label="HeroPhotoSheet"
        caption="Parallax scroll — photo translates up at ~50% the scroll rate while the content sheet covers it as the user reads down. Two static states shown side-by-side: initial (photo full) and scrolled (sheet covers photo). Magazine-page immersion for image-led detail pages. The single locked HeroPhotoSheet shape."
      >
        <div class="w-full space-y-10">

          <!-- ── LIGHT PANEL ── -->
          <div class="rounded-2xl border p-6 space-y-6" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest mb-4" :style="{ color: L.inkTertiary }">Light mode — initial state (left) · scrolled state (right)</p>

            <div class="flex flex-col sm:flex-row justify-center gap-8 items-start">

              <!-- STATE 1: Initial — photo fully visible -->
              <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider text-center mb-3" :style="{ color: L.inkTertiary }">On mount</p>
                <div
                  style="
                    width: 280px; border-radius: 40px; overflow: hidden;
                    aspect-ratio: 9/16; border: 2px solid #BCB8B2;
                    position: relative; background: #1C1C1E;
                    box-shadow: 0 8px 32px rgba(28,20,10,0.18);
                  "
                >
                  <!-- Photo fills entire frame (parallax: translateY(0)) -->
                  <div
                    style="
                      position: absolute; top: 0; left: 0; right: 0; bottom: 0;
                      background-image: url('https://images.unsplash.com/photo-1546548970-71785318a17b?w=800');
                      background-size: cover; background-position: center;
                      transform: translateY(0);
                    "
                  >
                    <!-- Status bar scrim -->
                    <div style="position: absolute; top: 0; left: 0; right: 0; height: 80px; background: linear-gradient(to bottom, rgba(0,0,0,0.30), transparent);" />
                    <!-- Back btn -->
                    <div style="position: absolute; top: 40px; left: 16px;">
                      <div :style="frostBtnLight" style="width: 36px; height: 36px;">
                        <ArrowLeftIcon style="width: 16px; height: 16px; color: #1C1C1E;" />
                      </div>
                    </div>
                    <!-- Action buttons -->
                    <div style="position: absolute; top: 40px; right: 16px; display: flex; gap: 8px;">
                      <div :style="frostBtnLight" style="width: 36px; height: 36px;">
                        <ShareIcon style="width: 16px; height: 16px; color: #1C1C1E;" />
                      </div>
                      <div :style="frostBtnLight" style="width: 36px; height: 36px;">
                        <BookmarkIcon style="width: 16px; height: 16px; color: #1C1C1E;" />
                      </div>
                    </div>
                  </div>

                  <!-- Sheet resting position: only peek at bottom -->
                  <div
                    :style="{
                      position: 'absolute',
                      top: '70%',
                      left: '0', right: '0', bottom: '0',
                      background: L.surfaceRaised,
                      borderRadius: '28px 28px 0 0',
                      boxShadow: SHADOW_SHEET_LT,
                    }"
                  >
                    <div style="display: flex; justify-content: center; padding-top: 8px; padding-bottom: 4px;">
                      <div style="width: 32px; height: 4px; border-radius: 99px; background: #9C9895;" />
                    </div>
                    <div style="padding: 8px 16px 0;">
                      <h2
                        style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 16px; font-weight: 700; line-height: 1.2; letter-spacing: -0.02em; margin: 0;"
                        :style="{ color: L.inkPrimary }"
                      >Grandma's Sunday Roast</h2>
                      <div style="display: flex; align-items: center; gap: 10px; margin-top: 5px;">
                        <div style="display: flex; align-items: center; gap: 3px;">
                          <ClockIcon style="width: 11px; height: 11px;" :style="{ color: L.inkTertiary }" />
                          <span style="font-size: 10px;" :style="{ color: L.inkSecondary }">2h 30m</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 3px;">
                          <FireIcon style="width: 11px; height: 11px;" :style="{ color: L.inkTertiary }" />
                          <span style="font-size: 10px;" :style="{ color: L.inkSecondary }">Medium</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 3px;">
                          <ChartBarIcon style="width: 11px; height: 11px;" :style="{ color: L.inkTertiary }" />
                          <span style="font-size: 10px;" :style="{ color: L.inkSecondary }">620 kcal</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Scroll arrow indicator -->
              <div class="flex items-center self-center" style="padding-top: 60px;">
                <div style="display: flex; flex-direction: column; align-items: center; gap: 4px;">
                  <div style="width: 1px; height: 40px;" :style="{ background: L.borderStrong }" />
                  <svg width="12" height="8" viewBox="0 0 12 8" fill="none">
                    <path d="M1 1L6 6L11 1" :stroke="L.inkTertiary" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                  <p style="font-size: 9px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em; writing-mode: horizontal-tb; margin-top: 4px;" :style="{ color: L.inkTertiary }">Scroll</p>
                </div>
              </div>

              <!-- STATE 2: Scrolled — sheet covers most of photo -->
              <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider text-center mb-3" :style="{ color: L.inkTertiary }">After scroll</p>
                <div
                  style="
                    width: 280px; border-radius: 40px; overflow: hidden;
                    aspect-ratio: 9/16; border: 2px solid #BCB8B2;
                    position: relative; background: #1C1C1E;
                    box-shadow: 0 8px 32px rgba(28,20,10,0.18);
                  "
                >
                  <!-- Photo shifted up (parallax translateY at half scroll rate, roughly) -->
                  <div
                    style="
                      position: absolute; top: -80px; left: 0; right: 0; height: 60%;
                      background-image: url('https://images.unsplash.com/photo-1546548970-71785318a17b?w=800');
                      background-size: cover; background-position: center;
                      transform: translateY(-20px);
                    "
                  >
                    <div style="position: absolute; top: 0; left: 0; right: 0; height: 80px; background: linear-gradient(to bottom, rgba(0,0,0,0.30), transparent);" />
                    <div style="position: absolute; top: 40px; left: 16px;">
                      <div :style="frostBtnLight" style="width: 36px; height: 36px;">
                        <ArrowLeftIcon style="width: 16px; height: 16px; color: #1C1C1E;" />
                      </div>
                    </div>
                    <div style="position: absolute; top: 40px; right: 16px; display: flex; gap: 8px;">
                      <div :style="frostBtnLight" style="width: 36px; height: 36px;">
                        <ShareIcon style="width: 16px; height: 16px; color: #1C1C1E;" />
                      </div>
                      <div :style="frostBtnLight" style="width: 36px; height: 36px;">
                        <BookmarkIcon style="width: 16px; height: 16px; color: #1C1C1E;" />
                      </div>
                    </div>
                  </div>

                  <!-- Sheet risen — covers ~80% of frame -->
                  <div
                    :style="{
                      position: 'absolute',
                      top: '20%',
                      left: '0', right: '0', bottom: '0',
                      background: L.surfaceRaised,
                      borderRadius: '28px 28px 0 0',
                      boxShadow: SHADOW_SHEET_LT,
                      display: 'flex', flexDirection: 'column',
                      overflow: 'hidden',
                    }"
                  >
                    <div style="display: flex; justify-content: center; padding-top: 8px; padding-bottom: 4px; flex-shrink: 0;">
                      <div style="width: 32px; height: 4px; border-radius: 99px; background: #9C9895;" />
                    </div>
                    <div style="padding: 8px 16px 16px; flex: 1; display: flex; flex-direction: column; gap: 8px; overflow: hidden;">
                      <h2
                        style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 17px; font-weight: 700; line-height: 1.2; letter-spacing: -0.02em; margin: 0;"
                        :style="{ color: L.inkPrimary }"
                      >Grandma's Sunday Roast</h2>
                      <div style="display: flex; align-items: center; gap: 10px;">
                        <div style="display: flex; align-items: center; gap: 3px;">
                          <ClockIcon style="width: 11px; height: 11px;" :style="{ color: L.inkTertiary }" />
                          <span style="font-size: 10px;" :style="{ color: L.inkSecondary }">2h 30m</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 3px;">
                          <FireIcon style="width: 11px; height: 11px;" :style="{ color: L.inkTertiary }" />
                          <span style="font-size: 10px;" :style="{ color: L.inkSecondary }">Medium</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 3px;">
                          <ChartBarIcon style="width: 11px; height: 11px;" :style="{ color: L.inkTertiary }" />
                          <span style="font-size: 10px;" :style="{ color: L.inkSecondary }">620 kcal</span>
                        </div>
                      </div>
                      <!-- Author strip -->
                      <div style="display: flex; align-items: center; gap: 8px; padding: 8px 10px; border-radius: 12px;" :style="{ background: L.surfaceSunken }">
                        <div
                          style="width: 26px; height: 26px; border-radius: 50%; flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700;"
                          :style="{ background: L.accents.peach.soft, color: L.accents.peach.bold }"
                        >G</div>
                        <div>
                          <p style="font-size: 11px; font-weight: 600; margin: 0;" :style="{ color: L.inkPrimary }">Grandma Qualls</p>
                          <p style="font-size: 10px; margin: 0;" :style="{ color: L.inkTertiary }">Family recipe · 4 generations</p>
                        </div>
                      </div>
                      <!-- Description preview (only visible once scrolled) -->
                      <p style="font-size: 11px; line-height: 1.5; margin: 0;" :style="{ color: L.inkSecondary }">
                        A slow-roasted leg of lamb with rosemary and garlic, served with roast potatoes and seasonal greens. A family tradition every Sunday since 1962.
                      </p>
                      <div style="flex: 1;" />
                      <button
                        style="width: 100%; padding: 12px; border-radius: 99px; border: none; font-size: 13px; font-weight: 600; cursor: pointer;"
                        :style="{ background: L.accents.peach.bold, color: '#FAF8F5' }"
                      >Start cooking</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <p class="text-[11px] mt-2" :style="{ color: L.inkTertiary }">
              In production: wire scroll listener on the content container. Photo translateY = scrollTop * 0.5 (half-rate parallax). Sheet translateY = 0 (normal scroll). Result: photo "stays behind" as sheet rises over it.
            </p>
          </div>

          <!-- ── DARK PANEL ── -->
          <div class="rounded-2xl border p-6 space-y-6" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest mb-4" :style="{ color: D.inkTertiary }">Dark mode — initial state (left) · scrolled state (right)</p>

            <div class="flex flex-col sm:flex-row justify-center gap-8 items-start">

              <!-- STATE 1: Initial -->
              <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider text-center mb-3" :style="{ color: D.inkTertiary }">On mount</p>
                <div
                  style="
                    width: 280px; border-radius: 40px; overflow: hidden;
                    aspect-ratio: 9/16; border: 2px solid #2C2A27;
                    position: relative; background: #1C1C1E;
                    box-shadow: 0 8px 32px rgba(0,0,0,0.50);
                  "
                >
                  <div
                    style="
                      position: absolute; top: 0; left: 0; right: 0; bottom: 0;
                      background-image: url('https://images.unsplash.com/photo-1546548970-71785318a17b?w=800');
                      background-size: cover; background-position: center;
                    "
                  >
                    <div style="position: absolute; top: 0; left: 0; right: 0; height: 80px; background: linear-gradient(to bottom, rgba(0,0,0,0.45), transparent);" />
                    <div style="position: absolute; top: 40px; left: 16px;">
                      <div :style="frostBtnDark" style="width: 36px; height: 36px;">
                        <ArrowLeftIcon style="width: 16px; height: 16px; color: #F0EDE9;" />
                      </div>
                    </div>
                    <div style="position: absolute; top: 40px; right: 16px; display: flex; gap: 8px;">
                      <div :style="frostBtnDark" style="width: 36px; height: 36px;">
                        <ShareIcon style="width: 16px; height: 16px; color: #F0EDE9;" />
                      </div>
                      <div :style="frostBtnDark" style="width: 36px; height: 36px;">
                        <BookmarkIcon style="width: 16px; height: 16px; color: #F0EDE9;" />
                      </div>
                    </div>
                  </div>
                  <div
                    :style="{
                      position: 'absolute', top: '70%', left: '0', right: '0', bottom: '0',
                      background: D.surfaceRaised, borderRadius: '28px 28px 0 0',
                      boxShadow: SHADOW_SHEET_DK,
                    }"
                  >
                    <div style="display: flex; justify-content: center; padding-top: 8px; padding-bottom: 4px;">
                      <div style="width: 32px; height: 4px; border-radius: 99px; background: #6E6B67;" />
                    </div>
                    <div style="padding: 8px 16px 0;">
                      <h2
                        style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 16px; font-weight: 700; line-height: 1.2; letter-spacing: -0.02em; margin: 0;"
                        :style="{ color: D.inkPrimary }"
                      >Grandma's Sunday Roast</h2>
                      <div style="display: flex; align-items: center; gap: 10px; margin-top: 5px;">
                        <div style="display: flex; align-items: center; gap: 3px;">
                          <ClockIcon style="width: 11px; height: 11px;" :style="{ color: D.inkTertiary }" />
                          <span style="font-size: 10px;" :style="{ color: D.inkSecondary }">2h 30m</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 3px;">
                          <FireIcon style="width: 11px; height: 11px;" :style="{ color: D.inkTertiary }" />
                          <span style="font-size: 10px;" :style="{ color: D.inkSecondary }">Medium</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Scroll arrow -->
              <div class="flex items-center self-center" style="padding-top: 60px;">
                <div style="display: flex; flex-direction: column; align-items: center; gap: 4px;">
                  <div style="width: 1px; height: 40px;" :style="{ background: D.borderStrong }" />
                  <svg width="12" height="8" viewBox="0 0 12 8" fill="none">
                    <path d="M1 1L6 6L11 1" :stroke="D.inkTertiary" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                  <p style="font-size: 9px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em; margin-top: 4px;" :style="{ color: D.inkTertiary }">Scroll</p>
                </div>
              </div>

              <!-- STATE 2: Scrolled -->
              <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider text-center mb-3" :style="{ color: D.inkTertiary }">After scroll</p>
                <div
                  style="
                    width: 280px; border-radius: 40px; overflow: hidden;
                    aspect-ratio: 9/16; border: 2px solid #2C2A27;
                    position: relative; background: #1C1C1E;
                    box-shadow: 0 8px 32px rgba(0,0,0,0.50);
                  "
                >
                  <div
                    style="
                      position: absolute; top: -80px; left: 0; right: 0; height: 60%;
                      background-image: url('https://images.unsplash.com/photo-1546548970-71785318a17b?w=800');
                      background-size: cover; background-position: center;
                      transform: translateY(-20px);
                    "
                  >
                    <div style="position: absolute; top: 0; left: 0; right: 0; height: 80px; background: linear-gradient(to bottom, rgba(0,0,0,0.45), transparent);" />
                    <div style="position: absolute; top: 40px; left: 16px;">
                      <div :style="frostBtnDark" style="width: 36px; height: 36px;">
                        <ArrowLeftIcon style="width: 16px; height: 16px; color: #F0EDE9;" />
                      </div>
                    </div>
                    <div style="position: absolute; top: 40px; right: 16px; display: flex; gap: 8px;">
                      <div :style="frostBtnDark" style="width: 36px; height: 36px;">
                        <ShareIcon style="width: 16px; height: 16px; color: #F0EDE9;" />
                      </div>
                      <div :style="frostBtnDark" style="width: 36px; height: 36px;">
                        <BookmarkIcon style="width: 16px; height: 16px; color: #F0EDE9;" />
                      </div>
                    </div>
                  </div>
                  <div
                    :style="{
                      position: 'absolute', top: '20%', left: '0', right: '0', bottom: '0',
                      background: D.surfaceRaised, borderRadius: '28px 28px 0 0',
                      boxShadow: SHADOW_SHEET_DK,
                      display: 'flex', flexDirection: 'column', overflow: 'hidden',
                    }"
                  >
                    <div style="display: flex; justify-content: center; padding-top: 8px; padding-bottom: 4px; flex-shrink: 0;">
                      <div style="width: 32px; height: 4px; border-radius: 99px; background: #6E6B67;" />
                    </div>
                    <div style="padding: 8px 16px 16px; flex: 1; display: flex; flex-direction: column; gap: 8px; overflow: hidden;">
                      <h2
                        style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 17px; font-weight: 700; line-height: 1.2; letter-spacing: -0.02em; margin: 0;"
                        :style="{ color: D.inkPrimary }"
                      >Grandma's Sunday Roast</h2>
                      <div style="display: flex; align-items: center; gap: 10px;">
                        <div style="display: flex; align-items: center; gap: 3px;">
                          <ClockIcon style="width: 11px; height: 11px;" :style="{ color: D.inkTertiary }" />
                          <span style="font-size: 10px;" :style="{ color: D.inkSecondary }">2h 30m</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 3px;">
                          <FireIcon style="width: 11px; height: 11px;" :style="{ color: D.inkTertiary }" />
                          <span style="font-size: 10px;" :style="{ color: D.inkSecondary }">Medium</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 3px;">
                          <ChartBarIcon style="width: 11px; height: 11px;" :style="{ color: D.inkTertiary }" />
                          <span style="font-size: 10px;" :style="{ color: D.inkSecondary }">620 kcal</span>
                        </div>
                      </div>
                      <div style="display: flex; align-items: center; gap: 8px; padding: 8px 10px; border-radius: 12px;" :style="{ background: D.surfaceSunken }">
                        <div
                          style="width: 26px; height: 26px; border-radius: 50%; flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700;"
                          :style="{ background: D.accents.peach.soft, color: D.accents.peach.bold }"
                        >G</div>
                        <div>
                          <p style="font-size: 11px; font-weight: 600; margin: 0;" :style="{ color: D.inkPrimary }">Grandma Qualls</p>
                          <p style="font-size: 10px; margin: 0;" :style="{ color: D.inkTertiary }">Family recipe · 4 generations</p>
                        </div>
                      </div>
                      <p style="font-size: 11px; line-height: 1.5; margin: 0;" :style="{ color: D.inkSecondary }">
                        A slow-roasted leg of lamb with rosemary and garlic, served with roast potatoes and seasonal greens. A family tradition every Sunday since 1962.
                      </p>
                      <div style="flex: 1;" />
                      <button
                        style="width: 100%; padding: 12px; border-radius: 99px; border: none; font-size: 13px; font-weight: 600; cursor: pointer;"
                        :style="{ background: D.accents.peach.bold, color: D.inkInverse }"
                      >Start cooking</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <p class="text-[11px] mt-2" :style="{ color: D.inkTertiary }">
              Implementation: attach scroll listener on sheet container. On scroll, apply transform: translateY(scrollTop * -0.5) to the photo div. Sheet scrolls normally. Gives the illusion that the photo recedes as content rises.
            </p>
          </div>
        </div>
      </VariantFrame>
    </section>

    <!-- ═══════════════════════════════════════════════════════════════
         CLAUDE'S PICK CALLOUT
         ════════════════════════════════════════════════════════════════ -->
    <section class="mb-20">
      <div
        class="rounded-2xl border p-6 flex gap-4 items-start"
        :style="{ background: L.accents.lavender.soft, borderColor: '#C0B4E8' }"
      >
        <SparklesIcon class="w-5 h-5 flex-shrink-0 mt-0.5" :style="{ color: L.accents.lavender.bold }" />
        <div>
          <p class="text-sm font-semibold mb-1" :style="{ color: L.accents.lavender.bold }">
            LOCKED — Parallax-scroll HeroPhotoSheet
          </p>
          <p class="text-sm leading-relaxed" :style="{ color: L.inkPrimary }">
            The photo translates up at ~50% the scroll rate while the content sheet covers it as the user reads down. Magazine-page immersion for image-led detail pages. Used on recipe detail, restaurant detail, and any image-led view.
          </p>
          <p class="text-sm leading-relaxed mt-2" :style="{ color: L.inkSecondary }">
            <strong>Also a great fit for the Rewards store</strong> — each reward with a cover photo could use this treatment on its detail page. Photo as the hero, reward name + point cost + description + the "Redeem" button in the sliding sheet. Worth revisiting when the Rewards module gets its redesign pass.
          </p>
        </div>
      </div>
    </section>

    <!-- ═══════════════════════════════════════════════════════════════
         USAGE GUIDE
         ════════════════════════════════════════════════════════════════ -->
    <section class="mb-10">
      <div
        class="rounded-2xl border divide-y"
        :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle }"
      >
        <div class="px-6 py-4">
          <h2 class="text-base font-semibold" :style="{ color: L.inkPrimary }">Usage guide</h2>
          <p class="text-sm mt-1" :style="{ color: L.inkSecondary }">
            Map contexts to variants. All three share the same frosted-button chrome and content-sheet content model — only the photo/sheet relationship changes.
          </p>
        </div>

        <!-- Variant A -->
        <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-[140px_1fr] gap-3">
          <div>
            <span
              class="inline-flex items-center justify-center h-6 min-w-[1.5rem] px-2 rounded-full text-xs font-semibold"
              :style="{ background: L.inkPrimary, color: L.inkInverse }"
            >A — Overlap</span>
          </div>
          <div class="space-y-1">
            <p class="text-sm font-medium" :style="{ color: L.inkPrimary }">Recipe detail, restaurant detail, event with cover photo</p>
            <p class="text-sm" :style="{ color: L.inkSecondary }">
              The iOS-native default. Photo occupies the top ~55% of the viewport; the sheet slides up 24px into the photo so its rounded top corners appear to float within the image. Use whenever the photo is the primary identity signal — recipe hero shot, restaurant exterior, family event cover. The frosted buttons are always present over the photo; they should not reposition when the sheet rises.
            </p>
          </div>
        </div>

        <!-- Variant B -->
        <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-[140px_1fr] gap-3">
          <div>
            <span
              class="inline-flex items-center justify-center h-6 min-w-[1.5rem] px-2 rounded-full text-xs font-semibold"
              :style="{ background: L.inkPrimary, color: L.inkInverse }"
            >B — 60/40 Split</span>
          </div>
          <div class="space-y-1">
            <p class="text-sm font-medium" :style="{ color: L.inkPrimary }">Content-heavy detail views, desktop/tablet layouts</p>
            <p class="text-sm" :style="{ color: L.inkSecondary }">
              Clean horizontal divide — no overlap. Preferred on tablet and desktop where the screen is wide enough that the overlap trick feels less dramatic and more like a misalignment. Also use for detail pages with long content below the fold: ingredient lists, directions, full descriptions. The tag badge overlaid on the photo bottom-left is optional but helps orient the user to the content category.
            </p>
          </div>
        </div>

        <!-- Variant C -->
        <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-[140px_1fr] gap-3">
          <div>
            <span
              class="inline-flex items-center justify-center h-6 min-w-[1.5rem] px-2 rounded-full text-xs font-semibold"
              :style="{ background: L.inkPrimary, color: L.inkInverse }"
            >C — Parallax</span>
          </div>
          <div class="space-y-1">
            <p class="text-sm font-medium" :style="{ color: L.inkPrimary }">Launch screens, flagship features, hero moments</p>
            <p class="text-sm" :style="{ color: L.inkSecondary }">
              Most ambitious treatment. Photo is full-viewport on mount; as the user scrolls, the sheet rises and the photo moves at half the scroll rate, creating a depth illusion. Best for contexts where discovery delight matters: the first time a user opens the Meal Plan feature, a highlighted family outing with a great cover photo, or a featured recipe on the home feed. Implementation requires a scroll-linked animation: <code class="text-xs font-mono px-1 rounded" :style="{ background: L.surfaceSunken }">photo.style.transform = translateY(scrollTop * -0.5)</code> on each scroll tick.
            </p>
          </div>
        </div>

        <!-- Design tokens note -->
        <div class="px-6 py-4">
          <p class="text-xs font-semibold uppercase tracking-widest mb-3" :style="{ color: L.inkTertiary }">Design token checklist</p>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div
              v-for="item in [
                { label: 'Sheet top radius', value: '28px (rounded-t-[28px])' },
                { label: 'Sheet bottom radius', value: '0 — fills to screen edge' },
                { label: 'Grab handle', value: '36×4px pill, inkTertiary' },
                { label: 'Frosted button', value: '40×40 rounded-full, blur(12px)' },
                { label: 'Photo object-fit', value: 'cover, portrait-leaning aspect' },
                { label: 'Overlap (Variant A)', value: '24px sheet into photo' },
              ]"
              :key="item.label"
              class="flex items-start gap-3 py-2 px-3 rounded-xl"
              :style="{ background: L.surfaceSunken }"
            >
              <span class="text-xs font-semibold flex-shrink-0 mt-0.5 w-28" :style="{ color: L.inkTertiary }">{{ item.label }}</span>
              <span class="text-xs font-mono" :style="{ color: L.inkPrimary }">{{ item.value }}</span>
            </div>
          </div>
        </div>
      </div>
    </section>


    <!-- KIN COMPONENT PREVIEW -->
    <section class="mb-16">
      <VariantFrame label="Kin" caption="KinHeroPhotoSheet — proposed extraction. Photo + sliding sheet. Bind sheetTop reactively for real parallax.">
        <div class="w-full space-y-10">
          <div class="rounded-2xl border p-6" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest mb-4" :style="{ color: L.inkTertiary }">Light mode — resting state (sheet at 60%)</p>
            <div class="flex justify-center">
              <div style="width: 280px; border-radius: 40px; overflow: hidden; aspect-ratio: 9/16; border: 2px solid #BCB8B2; box-shadow: 0 8px 32px rgba(28,20,10,0.18);">
                <KinHeroPhotoSheet
                  photo="https://images.unsplash.com/photo-1546548970-71785318a17b?w=800"
                  photo-alt="Sunday roast dinner"
                  title="Grandma's Sunday Roast"
                  sheet-top="60%"
                >
                  <template #actions>
                    <button type="button" class="kin-hero-photo-sheet__frost-btn flex items-center justify-center" style="width: 40px; height: 40px; background: rgba(255,255,255,0.88); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.60); border-radius: 9999px;">
                      <ShareIcon class="w-4 h-4" style="color: #1C1C1E;" />
                    </button>
                    <button type="button" class="kin-hero-photo-sheet__frost-btn flex items-center justify-center" style="width: 40px; height: 40px; background: rgba(255,255,255,0.88); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.60); border-radius: 9999px;">
                      <BookmarkIcon class="w-4 h-4" style="color: #1C1C1E;" />
                    </button>
                  </template>
                  <template #meta>
                    <div class="flex items-center gap-3 text-[11px]" :style="{ color: L.inkSecondary }">
                      <span class="inline-flex items-center gap-1"><ClockIcon class="w-3 h-3" /> 2h 30m</span>
                      <span class="inline-flex items-center gap-1"><FireIcon class="w-3 h-3" /> Medium</span>
                      <span class="inline-flex items-center gap-1"><ChartBarIcon class="w-3 h-3" /> 620 kcal</span>
                    </div>
                  </template>
                  <p class="text-[12px] leading-relaxed" :style="{ color: L.inkSecondary }">
                    A slow-roasted beef shoulder with root vegetables, roasted potatoes, and proper Yorkshire pudding — the one dish that pulls the whole family to the table on Sunday afternoon.
                  </p>
                </KinHeroPhotoSheet>
              </div>
            </div>

            <p class="text-xs font-semibold uppercase tracking-widest mt-8 mb-4" :style="{ color: L.inkTertiary }">Scrolled state (sheet at 20%)</p>
            <div class="flex justify-center">
              <div style="width: 280px; border-radius: 40px; overflow: hidden; aspect-ratio: 9/16; border: 2px solid #BCB8B2; box-shadow: 0 8px 32px rgba(28,20,10,0.18);">
                <KinHeroPhotoSheet
                  photo="https://images.unsplash.com/photo-1546548970-71785318a17b?w=800"
                  photo-alt="Sunday roast dinner"
                  title="Grandma's Sunday Roast"
                  sheet-top="20%"
                >
                  <template #meta>
                    <div class="flex items-center gap-3 text-[11px]" :style="{ color: L.inkSecondary }">
                      <span class="inline-flex items-center gap-1"><ClockIcon class="w-3 h-3" /> 2h 30m</span>
                      <span class="inline-flex items-center gap-1"><FireIcon class="w-3 h-3" /> Medium</span>
                      <span class="inline-flex items-center gap-1"><ChartBarIcon class="w-3 h-3" /> 620 kcal</span>
                    </div>
                  </template>
                  <p class="text-[12px] leading-relaxed" :style="{ color: L.inkSecondary }">
                    A slow-roasted beef shoulder with root vegetables, roasted potatoes, and proper Yorkshire pudding — the one dish that pulls the whole family to the table on Sunday afternoon.
                  </p>
                  <p class="text-[12px] leading-relaxed mt-3" :style="{ color: L.inkSecondary }">
                    Serves 6. Prep 30 min, cook 2 hours. Rest the beef for 20 minutes before carving — this is the step most people skip and it's the difference between a great roast and a perfect one.
                  </p>
                </KinHeroPhotoSheet>
              </div>
            </div>
          </div>
        </div>
      </VariantFrame>
      <p class="mt-3 text-sm px-1" :style="{ color: L.inkSecondary }">
        The sheet top position is a reactive prop — bind it to scroll position in the consuming view to drive parallax.
      </p>
    </section>

  </ComponentPage>
</template>
