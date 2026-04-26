<script setup>
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import {
  ShareIcon, BookmarkIcon, ClockIcon, FireIcon, ChartBarIcon,
} from '@heroicons/vue/24/outline'
import KinHeroPhotoSheet from '@/components/design-system/KinHeroPhotoSheet.vue'

const L = {
  surfaceApp:    '#FAF8F5',
  surfaceSunken: '#F5F2EE',
  inkPrimary:    '#1C1C1E',
  inkSecondary:  '#6B6966',
  inkTertiary:   '#9C9895',
  borderSubtle:  '#E8E4DF',
  borderStrong:  '#BCB8B2',
  accents: { peach: { soft: '#FCE9E0', bold: '#BA562E' } },
}
const D = {
  surfaceApp:    '#141311',
  surfaceSunken: '#161513',
  inkPrimary:    '#F0EDE9',
  inkSecondary:  '#A09C97',
  inkTertiary:   '#6E6B67',
  borderSubtle:  '#2C2A27',
  borderStrong:  '#403E3A',
  accents: { peach: { soft: '#3E241A', bold: '#F0A882' } },
}

const HERO_PHOTO_URL = 'https://images.unsplash.com/photo-1546548970-71785318a17b?w=800'
</script>

<template>
  <ComponentPage
    title="5.15 HeroPhotoSheet"
    description="Flagship detail-page treatment — edge-to-edge hero photo with frosted circular back/action buttons floating over it, and a content sheet that slides up from below with a parallax offset. Used for recipe detail, restaurant detail, and any image-led detail view."
    status="chosen"
  >
    <section class="mb-16">
      <VariantFrame label="KinHeroPhotoSheet" caption="Photo + sliding sheet. Bind sheetTop reactively for real parallax. On mount the sheet rests; after scroll it rises to cover most of the photo.">
        <div class="w-full space-y-10">

          <!-- LIGHT PANEL — side-by-side On mount + After scroll -->
          <div class="rounded-2xl border p-6" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest mb-6" :style="{ color: L.inkTertiary }">Light mode — On mount (sheet at 60%) · After scroll (sheet at 20%)</p>
            <div class="flex flex-col sm:flex-row justify-center gap-8 items-start">

              <!-- On mount -->
              <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider text-center mb-3" :style="{ color: L.inkTertiary }">On mount</p>
                <div style="width: 280px; border-radius: 40px; overflow: hidden; aspect-ratio: 9/16; border: 2px solid #BCB8B2; box-shadow: 0 8px 32px rgba(28,20,10,0.18);">
                  <KinHeroPhotoSheet
                    :photo="HERO_PHOTO_URL"
                    photo-alt="Sunday roast dinner"
                    title="Grandma's Sunday Roast"
                    sheet-top="60%"
                  >
                    <template #actions>
                      <button type="button" class="frost-btn"><ShareIcon class="w-4 h-4" style="color: #1C1C1E;" /></button>
                      <button type="button" class="frost-btn"><BookmarkIcon class="w-4 h-4" style="color: #1C1C1E;" /></button>
                    </template>
                    <template #meta>
                      <div class="flex items-center gap-3 text-[11px]" :style="{ color: L.inkSecondary }">
                        <span class="inline-flex items-center gap-1"><ClockIcon class="w-3 h-3" /> 2h 30m</span>
                        <span class="inline-flex items-center gap-1"><FireIcon class="w-3 h-3" /> Medium</span>
                        <span class="inline-flex items-center gap-1"><ChartBarIcon class="w-3 h-3" /> 620 kcal</span>
                      </div>
                    </template>
                  </KinHeroPhotoSheet>
                </div>
              </div>

              <!-- Scroll arrow -->
              <div class="hidden sm:flex items-center self-center" style="padding-top: 60px;">
                <div class="flex flex-col items-center gap-1">
                  <div class="w-px h-10" :style="{ background: L.borderStrong }" />
                  <svg width="12" height="8" viewBox="0 0 12 8" fill="none">
                    <path d="M1 1L6 6L11 1" :stroke="L.inkTertiary" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                  </svg>
                  <p class="text-[9px] font-semibold uppercase tracking-widest mt-1" :style="{ color: L.inkTertiary }">Scroll</p>
                </div>
              </div>

              <!-- After scroll -->
              <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider text-center mb-3" :style="{ color: L.inkTertiary }">After scroll</p>
                <div style="width: 280px; border-radius: 40px; overflow: hidden; aspect-ratio: 9/16; border: 2px solid #BCB8B2; box-shadow: 0 8px 32px rgba(28,20,10,0.18);">
                  <KinHeroPhotoSheet
                    :photo="HERO_PHOTO_URL"
                    photo-alt="Sunday roast dinner"
                    title="Grandma's Sunday Roast"
                    sheet-top="20%"
                  >
                    <template #actions>
                      <button type="button" class="frost-btn"><ShareIcon class="w-4 h-4" style="color: #1C1C1E;" /></button>
                      <button type="button" class="frost-btn"><BookmarkIcon class="w-4 h-4" style="color: #1C1C1E;" /></button>
                    </template>
                    <template #meta>
                      <div class="flex items-center gap-3 text-[11px]" :style="{ color: L.inkSecondary }">
                        <span class="inline-flex items-center gap-1"><ClockIcon class="w-3 h-3" /> 2h 30m</span>
                        <span class="inline-flex items-center gap-1"><FireIcon class="w-3 h-3" /> Medium</span>
                        <span class="inline-flex items-center gap-1"><ChartBarIcon class="w-3 h-3" /> 620 kcal</span>
                      </div>
                    </template>
                    <!-- Author strip -->
                    <div class="flex items-center gap-2 px-2.5 py-2 rounded-xl" :style="{ background: L.surfaceSunken }">
                      <div class="w-7 h-7 rounded-full flex items-center justify-center text-[12px] font-bold flex-shrink-0"
                           :style="{ background: L.accents.peach.soft, color: L.accents.peach.bold }">G</div>
                      <div>
                        <p class="text-[11px] font-semibold m-0" :style="{ color: L.inkPrimary }">Grandma Qualls</p>
                        <p class="text-[10px] m-0" :style="{ color: L.inkTertiary }">Family recipe · 4 generations</p>
                      </div>
                    </div>
                    <p class="text-[11px] leading-relaxed mt-2" :style="{ color: L.inkSecondary }">
                      A slow-roasted leg of lamb with rosemary and garlic, served with roast potatoes and seasonal greens. A family tradition every Sunday since 1962.
                    </p>
                    <button
                      type="button"
                      class="w-full mt-3 py-3 rounded-full border-0 text-[13px] font-semibold cursor-pointer"
                      :style="{ background: L.accents.peach.bold, color: '#FAF8F5' }"
                    >Start cooking</button>
                  </KinHeroPhotoSheet>
                </div>
              </div>
            </div>
          </div>

          <!-- DARK PANEL -->
          <div class="dark rounded-2xl border p-6" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest mb-6" :style="{ color: D.inkTertiary }">Dark mode — On mount · After scroll</p>
            <div class="flex flex-col sm:flex-row justify-center gap-8 items-start">

              <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider text-center mb-3" :style="{ color: D.inkTertiary }">On mount</p>
                <div style="width: 280px; border-radius: 40px; overflow: hidden; aspect-ratio: 9/16; border: 2px solid #2C2A27; box-shadow: 0 8px 32px rgba(0,0,0,0.50);">
                  <KinHeroPhotoSheet
                    :photo="HERO_PHOTO_URL"
                    photo-alt="Sunday roast dinner"
                    title="Grandma's Sunday Roast"
                    sheet-top="60%"
                  >
                    <template #actions>
                      <button type="button" class="frost-btn frost-btn--dark"><ShareIcon class="w-4 h-4" style="color: #F0EDE9;" /></button>
                      <button type="button" class="frost-btn frost-btn--dark"><BookmarkIcon class="w-4 h-4" style="color: #F0EDE9;" /></button>
                    </template>
                    <template #meta>
                      <div class="flex items-center gap-3 text-[11px]" :style="{ color: D.inkSecondary }">
                        <span class="inline-flex items-center gap-1"><ClockIcon class="w-3 h-3" /> 2h 30m</span>
                        <span class="inline-flex items-center gap-1"><FireIcon class="w-3 h-3" /> Medium</span>
                        <span class="inline-flex items-center gap-1"><ChartBarIcon class="w-3 h-3" /> 620 kcal</span>
                      </div>
                    </template>
                  </KinHeroPhotoSheet>
                </div>
              </div>

              <div class="hidden sm:flex items-center self-center" style="padding-top: 60px;">
                <div class="flex flex-col items-center gap-1">
                  <div class="w-px h-10" :style="{ background: D.borderStrong }" />
                  <svg width="12" height="8" viewBox="0 0 12 8" fill="none">
                    <path d="M1 1L6 6L11 1" :stroke="D.inkTertiary" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                  </svg>
                  <p class="text-[9px] font-semibold uppercase tracking-widest mt-1" :style="{ color: D.inkTertiary }">Scroll</p>
                </div>
              </div>

              <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider text-center mb-3" :style="{ color: D.inkTertiary }">After scroll</p>
                <div style="width: 280px; border-radius: 40px; overflow: hidden; aspect-ratio: 9/16; border: 2px solid #2C2A27; box-shadow: 0 8px 32px rgba(0,0,0,0.50);">
                  <KinHeroPhotoSheet
                    :photo="HERO_PHOTO_URL"
                    photo-alt="Sunday roast dinner"
                    title="Grandma's Sunday Roast"
                    sheet-top="20%"
                  >
                    <template #actions>
                      <button type="button" class="frost-btn frost-btn--dark"><ShareIcon class="w-4 h-4" style="color: #F0EDE9;" /></button>
                      <button type="button" class="frost-btn frost-btn--dark"><BookmarkIcon class="w-4 h-4" style="color: #F0EDE9;" /></button>
                    </template>
                    <template #meta>
                      <div class="flex items-center gap-3 text-[11px]" :style="{ color: D.inkSecondary }">
                        <span class="inline-flex items-center gap-1"><ClockIcon class="w-3 h-3" /> 2h 30m</span>
                        <span class="inline-flex items-center gap-1"><FireIcon class="w-3 h-3" /> Medium</span>
                        <span class="inline-flex items-center gap-1"><ChartBarIcon class="w-3 h-3" /> 620 kcal</span>
                      </div>
                    </template>
                    <div class="flex items-center gap-2 px-2.5 py-2 rounded-xl" :style="{ background: D.surfaceSunken }">
                      <div class="w-7 h-7 rounded-full flex items-center justify-center text-[12px] font-bold flex-shrink-0"
                           :style="{ background: D.accents.peach.soft, color: D.accents.peach.bold }">G</div>
                      <div>
                        <p class="text-[11px] font-semibold m-0" :style="{ color: D.inkPrimary }">Grandma Qualls</p>
                        <p class="text-[10px] m-0" :style="{ color: D.inkTertiary }">Family recipe · 4 generations</p>
                      </div>
                    </div>
                    <p class="text-[11px] leading-relaxed mt-2" :style="{ color: D.inkSecondary }">
                      A slow-roasted leg of lamb with rosemary and garlic, served with roast potatoes and seasonal greens. A family tradition every Sunday since 1962.
                    </p>
                    <button
                      type="button"
                      class="w-full mt-3 py-3 rounded-full border-0 text-[13px] font-semibold cursor-pointer"
                      :style="{ background: D.accents.peach.bold, color: '#1C1C1E' }"
                    >Start cooking</button>
                  </KinHeroPhotoSheet>
                </div>
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

<style scoped>
.frost-btn {
  width: 40px;
  height: 40px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: rgba(255, 255, 255, 0.88);
  -webkit-backdrop-filter: blur(12px);
  backdrop-filter: blur(12px);
  border: 1px solid rgba(255, 255, 255, 0.60);
  border-radius: 9999px;
  cursor: pointer;
}
.frost-btn--dark {
  background: rgba(0, 0, 0, 0.42);
  border-color: rgba(255, 255, 255, 0.18);
}
</style>
