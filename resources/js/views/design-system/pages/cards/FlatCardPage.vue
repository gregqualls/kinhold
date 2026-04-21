<script setup>
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import KinFlatCard from '@/components/design-system/KinFlatCard.vue'

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
  accentLavenderSoft: '#EDE9F9',
  accentLavenderBold: '#6856B2',
  chipBg:        '#EDE9F9',
  chipText:      '#6856B2',
  chipBorder:    '#C0B4E8',
}
const D = {
  surfaceApp:    '#141311',
  inkPrimary:    '#F0EDE9',
  inkSecondary:  '#A09C97',
  inkTertiary:   '#6E6B67',
  borderSubtle:  '#2C2A27',
  borderStrong:  '#403E3A',
  accentLavenderSoft: '#2D2840',
  accentLavenderBold: '#B6A8E6',
  chipBg:        '#2D2840',
  chipText:      '#B6A8E6',
  chipBorder:    '#4A4272',
}

// Hover preview — a class forces the hovered visual state on a static card.
// The class has explicit light + dark rules so dark-mode's brightness-boost
// treatment (surface-overlay + shadow-elevated) is visible in the preview.
</script>

<template>
  <ComponentPage
    title="2.1 FlatCard"
    description="The workhorse card — white/raised surface, subtle border, soft shadow. Quiet by default. Used for form groups, list rows on desktop, settings panels, and any content chunk that needs a clear neutral bounding box. Single canonical treatment, three padding sizes."
    status="chosen"
  >

    <!-- ══════════════════════════════════════════════════════════════
         SECTION 1 — PADDING SCALE
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-20">
      <VariantFrame label="Padding" caption="Three canonical padding sizes — sm for dense, md default, lg for hero moments">
        <div class="w-full space-y-10">

          <!-- LIGHT PANEL -->
          <div class="rounded-2xl border p-6 space-y-6" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>

            <div class="space-y-4">

              <!-- sm · 16px — dense list row context -->
              <div class="flex items-stretch gap-4">
                <div class="flex-shrink-0 w-14 flex flex-col items-start justify-center gap-0.5">
                  <span class="text-[11px] font-semibold" :style="{ color: L.inkTertiary }">sm</span>
                  <span class="text-[10px] font-mono" :style="{ color: L.inkTertiary }">16px</span>
                </div>
                <KinFlatCard padding="sm" class="flex-1">
                  <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5 min-w-0 flex-1">
                      <div class="flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center text-[9px] font-bold" :style="{ background: L.accentLavenderSoft, color: L.accentLavenderBold }">MQ</div>
                      <div class="min-w-0 flex-1">
                        <p class="text-[13px] font-semibold truncate" :style="{ color: L.inkPrimary }">Pack lunches</p>
                        <p class="text-[11px] truncate" :style="{ color: L.inkTertiary }">Due today</p>
                      </div>
                    </div>
                    <button class="flex-shrink-0 rounded-full px-2.5 py-0.5 text-[11px] font-semibold border" :style="{ background: L.accentLavenderSoft, borderColor: L.chipBorder, color: L.accentLavenderBold }">Open</button>
                  </div>
                </KinFlatCard>
              </div>

              <!-- md · 24px — standard info container -->
              <div class="flex items-stretch gap-4">
                <div class="flex-shrink-0 w-14 flex flex-col items-start justify-center gap-0.5">
                  <span class="text-[11px] font-semibold" :style="{ color: L.inkTertiary }">md</span>
                  <span class="text-[10px] font-mono" :style="{ color: L.inkTertiary }">24px</span>
                </div>
                <KinFlatCard padding="md" class="flex-1">
                  <div class="flex items-start justify-between gap-4">
                    <div class="flex-1 space-y-1.5">
                      <p class="text-[15px] font-semibold" :style="{ color: L.inkPrimary }">Weekly summary</p>
                      <p class="text-[13px] leading-relaxed" :style="{ color: L.inkSecondary }">The everyday default. Most cards in the app — settings panels, form groups, vault entries — use this padding.</p>
                    </div>
                    <button class="flex-shrink-0 rounded-full px-3 py-1 text-[12px] font-semibold border" :style="{ background: L.accentLavenderSoft, borderColor: L.chipBorder, color: L.accentLavenderBold }">View</button>
                  </div>
                </KinFlatCard>
              </div>

              <!-- lg · 32px — hero profile card -->
              <div class="flex items-stretch gap-4">
                <div class="flex-shrink-0 w-14 flex flex-col items-start justify-center gap-0.5">
                  <span class="text-[11px] font-semibold" :style="{ color: L.inkTertiary }">lg</span>
                  <span class="text-[10px] font-mono" :style="{ color: L.inkTertiary }">32px</span>
                </div>
                <KinFlatCard padding="lg" class="flex-1">
                  <div class="flex items-start justify-between gap-5">
                    <div class="flex items-start gap-4 flex-1">
                      <div class="flex-shrink-0 w-14 h-14 rounded-full flex items-center justify-center text-[18px] font-bold" :style="{ background: L.accentLavenderSoft, color: L.accentLavenderBold }">MQ</div>
                      <div class="flex-1 space-y-2">
                        <p class="text-[20px] font-semibold leading-tight" :style="{ color: L.inkPrimary }">Maya Qualls</p>
                        <p class="text-[14px] leading-relaxed" :style="{ color: L.inkSecondary }">Hero moments — profile headers, featured callouts, empty-state CTAs. Generous padding gives the content room to breathe.</p>
                      </div>
                    </div>
                    <button class="flex-shrink-0 rounded-full px-4 py-1.5 text-[13px] font-semibold border" :style="{ background: L.accentLavenderSoft, borderColor: L.chipBorder, color: L.accentLavenderBold }">Profile</button>
                  </div>
                </KinFlatCard>
              </div>

            </div>
          </div><!-- /light padding -->

          <!-- DARK PANEL -->
          <div class="dark rounded-2xl border p-6 space-y-6" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

            <div class="space-y-4">

              <!-- sm · 16px — dense list row -->
              <div class="flex items-stretch gap-4">
                <div class="flex-shrink-0 w-14 flex flex-col items-start justify-center gap-0.5">
                  <span class="text-[11px] font-semibold" :style="{ color: D.inkTertiary }">sm</span>
                  <span class="text-[10px] font-mono" :style="{ color: D.inkTertiary }">16px</span>
                </div>
                <KinFlatCard padding="sm" class="flex-1">
                  <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5 min-w-0 flex-1">
                      <div class="flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center text-[9px] font-bold" :style="{ background: D.accentLavenderSoft, color: D.accentLavenderBold }">MQ</div>
                      <div class="min-w-0 flex-1">
                        <p class="text-[13px] font-semibold truncate" :style="{ color: D.inkPrimary }">Pack lunches</p>
                        <p class="text-[11px] truncate" :style="{ color: D.inkTertiary }">Due today</p>
                      </div>
                    </div>
                    <button class="flex-shrink-0 rounded-full px-2.5 py-0.5 text-[11px] font-semibold border" :style="{ background: D.accentLavenderSoft, borderColor: D.chipBorder, color: D.accentLavenderBold }">Open</button>
                  </div>
                </KinFlatCard>
              </div>

              <!-- md · 24px — standard info container -->
              <div class="flex items-stretch gap-4">
                <div class="flex-shrink-0 w-14 flex flex-col items-start justify-center gap-0.5">
                  <span class="text-[11px] font-semibold" :style="{ color: D.inkTertiary }">md</span>
                  <span class="text-[10px] font-mono" :style="{ color: D.inkTertiary }">24px</span>
                </div>
                <KinFlatCard padding="md" class="flex-1">
                  <div class="flex items-start justify-between gap-4">
                    <div class="flex-1 space-y-1.5">
                      <p class="text-[15px] font-semibold" :style="{ color: D.inkPrimary }">Weekly summary</p>
                      <p class="text-[13px] leading-relaxed" :style="{ color: D.inkSecondary }">The everyday default. Most cards in the app — settings panels, form groups, vault entries — use this padding.</p>
                    </div>
                    <button class="flex-shrink-0 rounded-full px-3 py-1 text-[12px] font-semibold border" :style="{ background: D.accentLavenderSoft, borderColor: D.chipBorder, color: D.accentLavenderBold }">View</button>
                  </div>
                </KinFlatCard>
              </div>

              <!-- lg · 32px — hero profile card -->
              <div class="flex items-stretch gap-4">
                <div class="flex-shrink-0 w-14 flex flex-col items-start justify-center gap-0.5">
                  <span class="text-[11px] font-semibold" :style="{ color: D.inkTertiary }">lg</span>
                  <span class="text-[10px] font-mono" :style="{ color: D.inkTertiary }">32px</span>
                </div>
                <KinFlatCard padding="lg" class="flex-1">
                  <div class="flex items-start justify-between gap-5">
                    <div class="flex items-start gap-4 flex-1">
                      <div class="flex-shrink-0 w-14 h-14 rounded-full flex items-center justify-center text-[18px] font-bold" :style="{ background: D.accentLavenderSoft, color: D.accentLavenderBold }">MQ</div>
                      <div class="flex-1 space-y-2">
                        <p class="text-[20px] font-semibold leading-tight" :style="{ color: D.inkPrimary }">Maya Qualls</p>
                        <p class="text-[14px] leading-relaxed" :style="{ color: D.inkSecondary }">Hero moments — profile headers, featured callouts, empty-state CTAs. Generous padding gives the content room to breathe.</p>
                      </div>
                    </div>
                    <button class="flex-shrink-0 rounded-full px-4 py-1.5 text-[13px] font-semibold border" :style="{ background: D.accentLavenderSoft, borderColor: D.chipBorder, color: D.accentLavenderBold }">Profile</button>
                  </div>
                </KinFlatCard>
              </div>

            </div>
          </div><!-- /dark padding -->

        </div>
      </VariantFrame>
    </section>


    <!-- ══════════════════════════════════════════════════════════════
         SECTION 2 — INTERACTIVE STATES
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-20">
      <VariantFrame label="States" caption="Default resting shadow, hover lifts to shadow-hover with 1px translate">
        <div class="w-full space-y-10">

          <!-- LIGHT PANEL -->
          <div class="rounded-2xl border p-6" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest mb-6" :style="{ color: L.inkTertiary }">Light mode</p>

            <div class="grid grid-cols-3 gap-4">

              <!-- 1. Default -->
              <div class="flex flex-col gap-3">
                <KinFlatCard padding="md">
                  <p class="text-[14px] font-semibold mb-1" :style="{ color: L.inkPrimary }">Default</p>
                  <p class="text-[12px] leading-relaxed" :style="{ color: L.inkSecondary }">Resting shadow, no lift applied.</p>
                </KinFlatCard>
                <p class="text-[11px] text-center font-medium" :style="{ color: L.inkTertiary }">default</p>
              </div>

              <!-- 2. Hover pre-applied (static preview) -->
              <div class="flex flex-col gap-3">
                <KinFlatCard padding="md" class="hover-preview">
                  <p class="text-[14px] font-semibold mb-1" :style="{ color: L.inkPrimary }">Hover state</p>
                  <p class="text-[12px] leading-relaxed" :style="{ color: L.inkSecondary }">shadow-hover + 1px lift, static preview.</p>
                </KinFlatCard>
                <p class="text-[11px] text-center font-medium" :style="{ color: L.inkTertiary }">hover</p>
              </div>

              <!-- 3. Interactive live -->
              <div class="flex flex-col gap-3">
                <KinFlatCard padding="md" interactive>
                  <p class="text-[14px] font-semibold mb-1" :style="{ color: L.inkPrimary }">Hover me</p>
                  <p class="text-[12px] leading-relaxed" :style="{ color: L.inkSecondary }">Real transition — try hovering this card.</p>
                </KinFlatCard>
                <p class="text-[11px] text-center font-medium" :style="{ color: L.inkTertiary }">hover me</p>
              </div>

            </div>
          </div><!-- /light states -->

          <!-- DARK PANEL -->
          <div class="dark rounded-2xl border p-6" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest mb-6" :style="{ color: D.inkTertiary }">Dark mode</p>

            <div class="grid grid-cols-3 gap-4">

              <!-- 1. Default -->
              <div class="flex flex-col gap-3">
                <KinFlatCard padding="md">
                  <p class="text-[14px] font-semibold mb-1" :style="{ color: D.inkPrimary }">Default</p>
                  <p class="text-[12px] leading-relaxed" :style="{ color: D.inkSecondary }">Resting shadow, no lift applied.</p>
                </KinFlatCard>
                <p class="text-[11px] text-center font-medium" :style="{ color: D.inkTertiary }">default</p>
              </div>

              <!-- 2. Hover pre-applied (static preview) -->
              <div class="flex flex-col gap-3">
                <KinFlatCard padding="md" class="hover-preview">
                  <p class="text-[14px] font-semibold mb-1" :style="{ color: D.inkPrimary }">Hover state</p>
                  <p class="text-[12px] leading-relaxed" :style="{ color: D.inkSecondary }">shadow-hover + 1px lift, static preview.</p>
                </KinFlatCard>
                <p class="text-[11px] text-center font-medium" :style="{ color: D.inkTertiary }">hover</p>
              </div>

              <!-- 3. Interactive live -->
              <div class="flex flex-col gap-3">
                <KinFlatCard padding="md" interactive>
                  <p class="text-[14px] font-semibold mb-1" :style="{ color: D.inkPrimary }">Hover me</p>
                  <p class="text-[12px] leading-relaxed" :style="{ color: D.inkSecondary }">Real transition — try hovering this card.</p>
                </KinFlatCard>
                <p class="text-[11px] text-center font-medium" :style="{ color: D.inkTertiary }">hover me</p>
              </div>

            </div>
          </div><!-- /dark states -->

        </div>
      </VariantFrame>
    </section>


    <!-- ══════════════════════════════════════════════════════════════
         SECTION 3 — COMPOSITION EXAMPLES
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-20">
      <VariantFrame label="Composition" caption="Realistic uses — form groups, list rows, settings sections">
        <div class="w-full space-y-10">

          <!-- LIGHT PANEL -->
          <div class="rounded-2xl border p-6 space-y-6" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>

            <!-- 1. Form group -->
            <div>
              <p class="text-[11px] mb-3 font-medium" :style="{ color: L.inkTertiary }">Form group — FlatCard as a settings container</p>
              <KinFlatCard padding="md">
                <div class="space-y-5">
                  <p class="text-[14px] font-semibold" :style="{ color: L.inkPrimary }">Notification preferences</p>

                  <!-- Switch row 1 -->
                  <div class="flex items-center justify-between gap-4">
                    <div class="flex-1">
                      <p class="text-[13px] font-medium" :style="{ color: L.inkPrimary }">Show completed tasks</p>
                      <p class="text-[11px]" :style="{ color: L.inkTertiary }">Display finished tasks in your task lists</p>
                    </div>
                    <label class="relative inline-flex cursor-pointer flex-shrink-0">
                      <input type="checkbox" class="peer sr-only" checked />
                      <span class="relative w-[36px] h-[22px] rounded-full block" style="background: #6856B2">
                        <span class="absolute top-[2px] right-[2px] w-[18px] h-[18px] rounded-full block" style="background: #FFFFFF; box-shadow: 0 1px 3px rgba(0,0,0,0.20)" />
                      </span>
                    </label>
                  </div>

                  <!-- Divider -->
                  <div class="border-t" :style="{ borderColor: L.borderSubtle }" />

                  <!-- Switch row 2 -->
                  <div class="flex items-center justify-between gap-4">
                    <div class="flex-1">
                      <p class="text-[13px] font-medium" :style="{ color: L.inkPrimary }">Push notifications</p>
                      <p class="text-[11px]" :style="{ color: L.inkTertiary }">Receive alerts for family activity</p>
                    </div>
                    <label class="relative inline-flex cursor-pointer flex-shrink-0">
                      <input type="checkbox" class="peer sr-only" />
                      <span class="relative w-[36px] h-[22px] rounded-full block" :style="{ background: L.borderStrong }">
                        <span class="absolute top-[2px] left-[2px] w-[18px] h-[18px] rounded-full block" style="background: #FFFFFF; box-shadow: 0 1px 3px rgba(0,0,0,0.20)" />
                      </span>
                    </label>
                  </div>
                </div>
              </KinFlatCard>
            </div>

            <!-- 2. List row card -->
            <div>
              <p class="text-[11px] mb-3 font-medium" :style="{ color: L.inkTertiary }">List row — FlatCard as a compact task row</p>
              <KinFlatCard padding="none" class="px-5 py-4">
                <div class="flex items-center gap-4">
                  <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-[11px] font-bold" :style="{ background: L.accentLavenderSoft, color: L.accentLavenderBold }">MQ</div>
                  <div class="flex-1 min-w-0">
                    <p class="text-[14px] font-semibold truncate" :style="{ color: L.inkPrimary }">Schedule vet appointment</p>
                    <p class="text-[11px]" :style="{ color: L.inkTertiary }">Due Friday · Assigned to Maya</p>
                  </div>
                  <div class="flex-shrink-0 rounded-full px-3 py-1 text-[11px] font-semibold border" :style="{ background: L.chipBg, color: L.chipText, borderColor: L.chipBorder }">In progress</div>
                </div>
              </KinFlatCard>
            </div>

            <!-- 3. Stat summary card -->
            <div>
              <p class="text-[11px] mb-3 font-medium" :style="{ color: L.inkTertiary }">Stat summary — FlatCard as a dashboard metric</p>
              <KinFlatCard padding="md">
                <p class="text-[12px] font-medium mb-2" :style="{ color: L.inkSecondary }">Points earned this week</p>
                <p class="font-heading" style="font-size: clamp(2rem, 4vw, 3.5rem); line-height: 1; letter-spacing: -0.02em" :style="{ color: L.inkPrimary }">247</p>
              </KinFlatCard>
            </div>

          </div><!-- /light composition -->

          <!-- DARK PANEL -->
          <div class="dark rounded-2xl border p-6 space-y-6" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

            <!-- 1. Form group -->
            <div>
              <p class="text-[11px] mb-3 font-medium" :style="{ color: D.inkTertiary }">Form group — FlatCard as a settings container</p>
              <KinFlatCard padding="md">
                <div class="space-y-5">
                  <p class="text-[14px] font-semibold" :style="{ color: D.inkPrimary }">Notification preferences</p>
                  <div class="flex items-center justify-between gap-4">
                    <div class="flex-1">
                      <p class="text-[13px] font-medium" :style="{ color: D.inkPrimary }">Show completed tasks</p>
                      <p class="text-[11px]" :style="{ color: D.inkTertiary }">Display finished tasks in your task lists</p>
                    </div>
                    <label class="relative inline-flex cursor-pointer flex-shrink-0">
                      <input type="checkbox" class="peer sr-only" checked />
                      <span class="relative w-[36px] h-[22px] rounded-full block" style="background: #B6A8E6">
                        <span class="absolute top-[2px] right-[2px] w-[18px] h-[18px] rounded-full block" style="background: #FFFFFF; box-shadow: 0 1px 3px rgba(0,0,0,0.40)" />
                      </span>
                    </label>
                  </div>
                  <div class="border-t" :style="{ borderColor: D.borderSubtle }" />
                  <div class="flex items-center justify-between gap-4">
                    <div class="flex-1">
                      <p class="text-[13px] font-medium" :style="{ color: D.inkPrimary }">Push notifications</p>
                      <p class="text-[11px]" :style="{ color: D.inkTertiary }">Receive alerts for family activity</p>
                    </div>
                    <label class="relative inline-flex cursor-pointer flex-shrink-0">
                      <input type="checkbox" class="peer sr-only" />
                      <span class="relative w-[36px] h-[22px] rounded-full block" :style="{ background: D.borderStrong }">
                        <span class="absolute top-[2px] left-[2px] w-[18px] h-[18px] rounded-full block" style="background: #FFFFFF; box-shadow: 0 1px 3px rgba(0,0,0,0.40)" />
                      </span>
                    </label>
                  </div>
                </div>
              </KinFlatCard>
            </div>

            <!-- 2. List row card -->
            <div>
              <p class="text-[11px] mb-3 font-medium" :style="{ color: D.inkTertiary }">List row — FlatCard as a compact task row</p>
              <KinFlatCard padding="none" class="px-5 py-4">
                <div class="flex items-center gap-4">
                  <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-[11px] font-bold" :style="{ background: D.accentLavenderSoft, color: D.accentLavenderBold }">MQ</div>
                  <div class="flex-1 min-w-0">
                    <p class="text-[14px] font-semibold truncate" :style="{ color: D.inkPrimary }">Schedule vet appointment</p>
                    <p class="text-[11px]" :style="{ color: D.inkTertiary }">Due Friday · Assigned to Maya</p>
                  </div>
                  <div class="flex-shrink-0 rounded-full px-3 py-1 text-[11px] font-semibold border" :style="{ background: D.chipBg, color: D.chipText, borderColor: D.chipBorder }">In progress</div>
                </div>
              </KinFlatCard>
            </div>

            <!-- 3. Stat summary card -->
            <div>
              <p class="text-[11px] mb-3 font-medium" :style="{ color: D.inkTertiary }">Stat summary — FlatCard as a dashboard metric</p>
              <KinFlatCard padding="md">
                <p class="text-[12px] font-medium mb-2" :style="{ color: D.inkSecondary }">Points earned this week</p>
                <p class="font-heading" style="font-size: clamp(2rem, 4vw, 3.5rem); line-height: 1; letter-spacing: -0.02em" :style="{ color: D.inkPrimary }">247</p>
              </KinFlatCard>
            </div>

          </div><!-- /dark composition -->

        </div>
      </VariantFrame>
    </section>


    <!-- ══════════════════════════════════════════════════════════════
         SECTION 4 — USAGE GUIDE
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-8">
      <div class="rounded-2xl border p-6 space-y-4" :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle }">
        <h2 class="text-[17px] font-semibold" :style="{ color: L.inkPrimary }">When to use FlatCard</h2>
        <ul class="space-y-3 text-[14px]" :style="{ color: L.inkSecondary }">
          <li>
            <strong :style="{ color: L.inkPrimary }">FlatCard is the workhorse</strong> — default container for form sections, settings blocks, list rows, data tables, and any UI chunk that doesn't demand a hero treatment.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Not for image-led content</strong> — use PhotoCard (2.2) when you have a photo or color that should carry the card.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Not for imageless hero metrics</strong> — use GradientCard (2.3) when a card is a dashboard focal point and needs more visual energy.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Padding choice:</strong> <code class="font-mono text-[12px] px-1 py-0.5 rounded" :style="{ background: L.surfaceSunken }">sm</code> for dense list/task contexts, <code class="font-mono text-[12px] px-1 py-0.5 rounded" :style="{ background: L.surfaceSunken }">md</code> for most UI (settings, forms, containers), <code class="font-mono text-[12px] px-1 py-0.5 rounded" :style="{ background: L.surfaceSunken }">lg</code> for profile cards and standalone hero moments.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Interactive cards get hover lift;</strong> non-interactive (read-only summaries) stay flat.
          </li>
        </ul>
      </div>
    </section>

  </ComponentPage>
</template>
<style scoped>
/* Hover preview — forces hovered visual state on a static card in the States section.
   Light: shadow-hover + 1px lift.
   Dark: brightness boost (surface-overlay) + shadow-elevated + 1px lift, matching KinFlatCard's dark hover. */
.hover-preview {
  box-shadow: var(--shadow-hover) !important;
  transform: translateY(-1px);
}
.dark .hover-preview {
  background-color: rgb(var(--surface-overlay)) !important;
  box-shadow: var(--shadow-elevated) !important;
}
</style>

