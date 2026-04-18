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
const SHADOW_RESTING_LT = '0 1px 2px rgba(28, 20, 10, 0.04), 0 2px 6px rgba(28, 20, 10, 0.05)'
const SHADOW_HOVER_LT   = '0 4px 8px rgba(28, 20, 10, 0.08), 0 16px 32px rgba(28, 20, 10, 0.10)'
const SHADOW_RESTING_DK = '0 1px 2px rgba(0, 0, 0, 0.30), 0 2px 6px rgba(0, 0, 0, 0.25)'
const SHADOW_HOVER_DK   = '0 4px 8px rgba(0, 0, 0, 0.40), 0 16px 32px rgba(0, 0, 0, 0.30)'

// ── Accent families: light gradient backgrounds ───────────────────────────────
// Used for "row of 4" across all variants — each square gets a different accent.
// Per locked pattern: bg-accent-* token (light) or matching inline gradient (dark).
const ACCENT_LT = {
  lavender: { bg: 'linear-gradient(135deg, #EAE6F8 0%, #D8D0F2 50%, #EAE6F8 100%)', ink: '#6856B2', label: 'Medical',  meta: '12 items' },
  peach:    { bg: 'linear-gradient(135deg, #FCE9E0 0%, #F8D4C4 50%, #FCE9E0 100%)', ink: '#BA562E', label: 'Streak',   meta: '5 days'  },
  mint:     { bg: 'linear-gradient(135deg, #D5F2E8 0%, #BBE8D8 50%, #D5F2E8 100%)', ink: '#2E8A62', label: 'Wellness', meta: '73%'     },
  sun:      { bg: 'linear-gradient(135deg, #FCF3D2 0%, #F5E4A8 50%, #FCF3D2 100%)', ink: '#A2780C', label: 'Reward',   meta: '240 pts' },
}
const ACCENT_DK = {
  lavender: { bg: 'linear-gradient(135deg, #302A48 0%, #4B4368 50%, #302A48 100%)', ink: '#B6A8E6', label: 'Medical',  meta: '12 items' },
  peach:    { bg: 'linear-gradient(135deg, #3E241A 0%, #623E2F 50%, #3E241A 100%)', ink: '#F0A882', label: 'Streak',   meta: '5 days'  },
  mint:     { bg: 'linear-gradient(135deg, #18342A 0%, #2C5444 50%, #18342A 100%)', ink: '#7CD6AE', label: 'Wellness', meta: '73%'     },
  sun:      { bg: 'linear-gradient(135deg, #342C0A 0%, #584A18 50%, #342C0A 100%)', ink: '#E6C452', label: 'Reward',   meta: '240 pts' },
}

// ── Glyph icon paths (Heroicons 24 solid fills) ───────────────────────────────
// Inlined so we avoid all import concerns — same approach as GradientsPage.vue reference.
const GLYPH = {
  // TrophyIcon
  trophy: 'M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z',
  // ShieldCheckIcon
  shieldCheck: 'M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z',
  // FireIcon
  fire: 'M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.6a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48z M12 18a3.75 3.75 0 00.495-7.467 5.99 5.99 0 00-1.925 3.546 5.974 5.974 0 01-2.133-1A3.75 3.75 0 0012 18z',
  // StarIcon
  star: 'M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005z',
  // CheckCircleIcon
  checkCircle: 'M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z',
  // BoltIcon
  bolt: 'M14.615 1.595a.75.75 0 01.359.852L12.982 9.75h7.268a.75.75 0 01.548 1.262l-10.5 11.25a.75.75 0 01-1.272-.71l1.992-7.302H3.818a.75.75 0 01-.548-1.262l10.5-11.25a.75.75 0 01.845-.143z',
  // Row of 4 accent glyphs: lavender=shield, peach=fire, mint=checkCircle, sun=star
}

// Accent-keyed glyphs for the row-of-4
const ACCENT_GLYPHS = {
  lavender: GLYPH.shieldCheck,
  peach:    GLYPH.fire,
  mint:     GLYPH.checkCircle,
  sun:      GLYPH.star,
}
const ACCENT_KEYS = ['lavender', 'peach', 'mint', 'sun']
</script>

<template>
  <ComponentPage
    title="2.3 GradientCard"
    description="The counterpart to PhotoCard — used when there is no photo. Vault entries, imageless tasks, achievement tiles, empty-state placeholders, hero metric cards on the dashboard. The gradient fill and embossed glyph give the card character when a photo is unavailable. Three variants — pick the treatment that best fits the context."
    status="scaffolded"
  >

    <!-- ══════════════════════════════════════════════════════════════
         VARIANT A — Pale iridescent wash + embossed glyph watermark
         Quiet and airy. The glyph is a watermark (~15% opacity),
         barely there — like an embossed seal on fine stationery.
         Best for vault entries, achievement tiles, any imageless tile
         that needs character without energy.
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-20">
      <VariantFrame label="A" caption="Pale iridescent wash with a quiet embossed glyph watermark — soft, elegant, vault-ready.">
        <div class="w-full space-y-10">

          <!-- ─── LIGHT PANEL A ─────────────────────────────────────── -->
          <div class="rounded-2xl border p-6 space-y-8" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>

            <!-- 1. Single hero card A -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: L.inkTertiary }">Single hero — achievement tile</p>
              <div class="max-w-md mx-auto">
                <div
                  class="gc-a-lt relative rounded-[20px] overflow-hidden cursor-pointer aspect-[4/5]"
                  :style="{ background: L.surfaceRaised, boxShadow: SHADOW_RESTING_LT }"
                  style="background-image: var(--gradient-iridescent-subtle);"
                >
                  <!-- Glyph watermark: 60% card height, bottom-right, 15% opacity -->
                  <svg
                    class="absolute pointer-events-none"
                    style="width: 60%; height: 60%; right: -4%; bottom: -4%; opacity: 0.15; color: #6856B2;"
                    fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"
                  >
                    <path :d="GLYPH.trophy" fill-rule="evenodd" clip-rule="evenodd" />
                  </svg>
                  <!-- Content: title + meta top-left -->
                  <div class="absolute inset-x-0 top-0 p-6 space-y-2">
                    <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Achievement</p>
                    <p class="text-[20px] font-semibold leading-snug" :style="{ color: L.inkPrimary }">Stretch Week Complete</p>
                    <p class="text-[13px]" :style="{ color: L.inkSecondary }">5-day goal streak earned</p>
                  </div>
                  <!-- Earned meta: bottom-left -->
                  <div class="absolute inset-x-0 bottom-0 p-6">
                    <p class="text-[11px] font-medium" :style="{ color: L.inkTertiary }">Earned Apr 14, 2026</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- 2. Row of 4 accent cards A -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: L.inkTertiary }">Row of 4 — accent colors (lavender · peach · mint · sun)</p>
              <div class="grid grid-cols-4 gap-3">
                <div
                  v-for="key in ACCENT_KEYS"
                  :key="key"
                  class="gc-a-lt relative rounded-[20px] overflow-hidden cursor-pointer aspect-square"
                  :style="{ background: L.surfaceRaised, backgroundImage: ACCENT_LT[key].bg, boxShadow: SHADOW_RESTING_LT }"
                >
                  <!-- Glyph watermark -->
                  <svg
                    class="absolute pointer-events-none"
                    style="width: 62%; height: 62%; right: -6%; bottom: -6%; opacity: 0.15;"
                    :style="{ color: ACCENT_LT[key].ink }"
                    fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"
                  >
                    <path :d="ACCENT_GLYPHS[key]" fill-rule="evenodd" clip-rule="evenodd" />
                  </svg>
                  <div class="absolute inset-x-0 top-0 p-3 space-y-0.5">
                    <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: ACCENT_LT[key].ink }">{{ ACCENT_LT[key].label }}</p>
                    <p class="text-[12px]" :style="{ color: L.inkSecondary }">{{ ACCENT_LT[key].meta }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- 3. Empty state A -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: L.inkTertiary }">Empty state — no entries yet</p>
              <div class="max-w-xs">
                <div
                  class="gc-a-lt relative rounded-[20px] overflow-hidden cursor-pointer"
                  :style="{ background: L.surfaceRaised, boxShadow: SHADOW_RESTING_LT }"
                  style="background-image: var(--gradient-iridescent-subtle); min-height: 180px;"
                >
                  <svg
                    class="absolute pointer-events-none"
                    style="width: 55%; height: 55%; right: -2%; bottom: -2%; opacity: 0.12; color: #6856B2;"
                    fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"
                  >
                    <path :d="GLYPH.shieldCheck" fill-rule="evenodd" clip-rule="evenodd" />
                  </svg>
                  <div class="absolute inset-0 flex flex-col items-start justify-center p-6 space-y-3">
                    <div class="space-y-1">
                      <p class="text-[15px] font-semibold" :style="{ color: L.inkPrimary }">No vault entries yet</p>
                      <p class="text-[12px]" :style="{ color: L.inkSecondary }">Store important family documents and info securely.</p>
                    </div>
                    <button
                      class="rounded-full px-4 py-1.5 text-[12px] font-semibold border"
                      :style="{ borderColor: L.borderStrong, color: L.inkPrimary, background: L.surfaceRaised }"
                    >Add your first entry</button>
                  </div>
                </div>
              </div>
            </div>

            <!-- 4. Hero metric card A -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: L.inkTertiary }">Hero metric card — points this week</p>
              <div class="max-w-xs">
                <div
                  class="gc-a-lt relative rounded-[20px] overflow-hidden cursor-pointer"
                  :style="{ background: L.surfaceRaised, boxShadow: SHADOW_RESTING_LT }"
                  style="background-image: var(--gradient-iridescent-subtle); min-height: 200px;"
                >
                  <svg
                    class="absolute pointer-events-none"
                    style="width: 60%; height: 60%; right: -5%; bottom: -5%; opacity: 0.12; color: #6856B2;"
                    fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"
                  >
                    <path :d="GLYPH.star" fill-rule="evenodd" clip-rule="evenodd" />
                  </svg>
                  <div class="absolute inset-0 flex flex-col justify-center p-6 space-y-1">
                    <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Points earned this week</p>
                    <p
                      class="font-heading leading-none tracking-tight"
                      style="font-size: clamp(4rem, 10vw, 11.25rem); letter-spacing: -0.03em;"
                      :style="{ color: L.inkPrimary }"
                    >247</p>
                    <p class="text-[13px]" :style="{ color: L.inkSecondary }">+38 vs last week</p>
                  </div>
                </div>
              </div>
            </div>

          </div><!-- /light panel A -->

          <!-- ─── DARK PANEL A ─────────────────────────────────────── -->
          <div class="rounded-2xl border p-6 space-y-8" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

            <!-- 1. Single hero card A dark -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: D.inkTertiary }">Single hero — achievement tile</p>
              <div class="max-w-md mx-auto">
                <div
                  class="gc-a-dk relative rounded-[20px] overflow-hidden cursor-pointer aspect-[4/5]"
                  :style="{ boxShadow: SHADOW_RESTING_DK }"
                  style="background-color: #1C1B19; background-image:
                    radial-gradient(ellipse 140% 110% at 15% 10%, rgba(104, 86, 178, 0.28), transparent 85%),
                    radial-gradient(ellipse 130% 100% at 85% 90%, rgba(240, 168, 130, 0.24), transparent 85%),
                    radial-gradient(ellipse 110% 90% at 60% 35%, rgba(124, 214, 174, 0.18), transparent 90%),
                    linear-gradient(135deg, rgba(104, 86, 178, 0.06) 0%, rgba(240, 168, 130, 0.06) 100%);"
                >
                  <svg
                    class="absolute pointer-events-none"
                    style="width: 60%; height: 60%; right: -4%; bottom: -4%; opacity: 0.15; color: #B6A8E6;"
                    fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"
                  >
                    <path :d="GLYPH.trophy" fill-rule="evenodd" clip-rule="evenodd" />
                  </svg>
                  <div class="absolute inset-x-0 top-0 p-6 space-y-2">
                    <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Achievement</p>
                    <p class="text-[20px] font-semibold leading-snug" :style="{ color: D.inkPrimary }">Stretch Week Complete</p>
                    <p class="text-[13px]" :style="{ color: D.inkSecondary }">5-day goal streak earned</p>
                  </div>
                  <div class="absolute inset-x-0 bottom-0 p-6">
                    <p class="text-[11px] font-medium" :style="{ color: D.inkTertiary }">Earned Apr 14, 2026</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- 2. Row of 4 accent cards A dark -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: D.inkTertiary }">Row of 4 — accent colors (lavender · peach · mint · sun)</p>
              <div class="grid grid-cols-4 gap-3">
                <div
                  v-for="key in ACCENT_KEYS"
                  :key="key"
                  class="gc-a-dk relative rounded-[20px] overflow-hidden cursor-pointer aspect-square"
                  :style="{ backgroundImage: ACCENT_DK[key].bg, boxShadow: SHADOW_RESTING_DK }"
                  style="background-color: #1C1B19;"
                >
                  <svg
                    class="absolute pointer-events-none"
                    style="width: 62%; height: 62%; right: -6%; bottom: -6%; opacity: 0.15;"
                    :style="{ color: ACCENT_DK[key].ink }"
                    fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"
                  >
                    <path :d="ACCENT_GLYPHS[key]" fill-rule="evenodd" clip-rule="evenodd" />
                  </svg>
                  <div class="absolute inset-x-0 top-0 p-3 space-y-0.5">
                    <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: ACCENT_DK[key].ink }">{{ ACCENT_DK[key].label }}</p>
                    <p class="text-[12px]" :style="{ color: D.inkSecondary }">{{ ACCENT_DK[key].meta }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- 3. Empty state A dark -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: D.inkTertiary }">Empty state — no entries yet</p>
              <div class="max-w-xs">
                <div
                  class="gc-a-dk relative rounded-[20px] overflow-hidden cursor-pointer"
                  :style="{ boxShadow: SHADOW_RESTING_DK }"
                  style="background-color: #1C1B19;
                    background-image:
                      radial-gradient(ellipse 140% 110% at 15% 10%, rgba(104, 86, 178, 0.28), transparent 85%),
                      radial-gradient(ellipse 130% 100% at 85% 90%, rgba(240, 168, 130, 0.24), transparent 85%),
                      linear-gradient(135deg, rgba(104, 86, 178, 0.06) 0%, rgba(240, 168, 130, 0.06) 100%);
                    min-height: 180px;"
                >
                  <svg
                    class="absolute pointer-events-none"
                    style="width: 55%; height: 55%; right: -2%; bottom: -2%; opacity: 0.12; color: #B6A8E6;"
                    fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"
                  >
                    <path :d="GLYPH.shieldCheck" fill-rule="evenodd" clip-rule="evenodd" />
                  </svg>
                  <div class="absolute inset-0 flex flex-col items-start justify-center p-6 space-y-3">
                    <div class="space-y-1">
                      <p class="text-[15px] font-semibold" :style="{ color: D.inkPrimary }">No vault entries yet</p>
                      <p class="text-[12px]" :style="{ color: D.inkSecondary }">Store important family documents and info securely.</p>
                    </div>
                    <button
                      class="rounded-full px-4 py-1.5 text-[12px] font-semibold border"
                      :style="{ borderColor: D.borderStrong, color: D.inkPrimary, background: D.surfaceRaised }"
                    >Add your first entry</button>
                  </div>
                </div>
              </div>
            </div>

            <!-- 4. Hero metric card A dark -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: D.inkTertiary }">Hero metric card — points this week</p>
              <div class="max-w-xs">
                <div
                  class="gc-a-dk relative rounded-[20px] overflow-hidden cursor-pointer"
                  :style="{ boxShadow: SHADOW_RESTING_DK }"
                  style="background-color: #1C1B19;
                    background-image:
                      radial-gradient(ellipse 140% 110% at 15% 10%, rgba(104, 86, 178, 0.28), transparent 85%),
                      radial-gradient(ellipse 130% 100% at 85% 90%, rgba(240, 168, 130, 0.24), transparent 85%),
                      radial-gradient(ellipse 110% 90% at 60% 35%, rgba(124, 214, 174, 0.18), transparent 90%),
                      linear-gradient(135deg, rgba(104, 86, 178, 0.06) 0%, rgba(240, 168, 130, 0.06) 100%);
                    min-height: 200px;"
                >
                  <svg
                    class="absolute pointer-events-none"
                    style="width: 60%; height: 60%; right: -5%; bottom: -5%; opacity: 0.12; color: #B6A8E6;"
                    fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"
                  >
                    <path :d="GLYPH.star" fill-rule="evenodd" clip-rule="evenodd" />
                  </svg>
                  <div class="absolute inset-0 flex flex-col justify-center p-6 space-y-1">
                    <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Points earned this week</p>
                    <p
                      class="font-heading leading-none tracking-tight"
                      style="font-size: clamp(4rem, 10vw, 11.25rem); letter-spacing: -0.03em;"
                      :style="{ color: D.inkPrimary }"
                    >247</p>
                    <p class="text-[13px]" :style="{ color: D.inkSecondary }">+38 vs last week</p>
                  </div>
                </div>
              </div>
            </div>

          </div><!-- /dark panel A -->

        </div>
      </VariantFrame>
    </section>


    <!-- ══════════════════════════════════════════════════════════════
         VARIANT B — Radial gradient with positioned glyph top-left,
         content bottom-left. Diagonal composition creates visual tension.
         Glyph is "decorative" (~30% opacity) — clearly ornamental,
         not competing with the content.
         Best for category landing tiles, vault category cards.
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-20">
      <VariantFrame label="B" caption="Radial gradient centered upper-left, glyph top-left at 30%, content bottom-left — compositional diagonal, category-tile pattern.">
        <div class="w-full space-y-10">

          <!-- ─── LIGHT PANEL B ─────────────────────────────────────── -->
          <div class="rounded-2xl border p-6 space-y-8" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>

            <!-- 1. Single hero card B -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: L.inkTertiary }">Single hero — vault category tile</p>
              <div class="max-w-md mx-auto">
                <div
                  class="gc-b-lt relative rounded-[20px] overflow-hidden cursor-pointer aspect-[4/5]"
                  :style="{ boxShadow: SHADOW_RESTING_LT }"
                  style="background-color: #FFFFFF;
                    background-image: radial-gradient(ellipse 80% 70% at 20% 25%, rgba(234, 230, 248, 0.85), transparent 65%);
                    box-shadow: inset 0 0 40px rgba(104, 86, 178, 0.04), 0 1px 2px rgba(28, 20, 10, 0.04), 0 2px 6px rgba(28, 20, 10, 0.05);"
                >
                  <!-- Glyph: top-left, 28% wide, 30% opacity -->
                  <svg
                    class="absolute pointer-events-none"
                    style="width: 28%; height: 28%; left: 5%; top: 5%; opacity: 0.30; color: #6856B2;"
                    fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"
                  >
                    <path :d="GLYPH.shieldCheck" fill-rule="evenodd" clip-rule="evenodd" />
                  </svg>
                  <!-- Content: bottom-left -->
                  <div class="absolute inset-x-0 bottom-0 p-6 space-y-1">
                    <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Vault · Medical Records</p>
                    <p class="text-[20px] font-semibold leading-snug" :style="{ color: L.inkPrimary }">Health &amp; Medical</p>
                    <p class="text-[13px]" :style="{ color: L.inkSecondary }">12 entries · Updated 2 days ago</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- 2. Row of 4 accent cards B -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: L.inkTertiary }">Row of 4 — accent radial fills</p>
              <div class="grid grid-cols-4 gap-3">
                <div
                  v-for="key in ACCENT_KEYS"
                  :key="key"
                  class="gc-b-lt relative rounded-[20px] overflow-hidden cursor-pointer aspect-square"
                  :style="{
                    boxShadow: SHADOW_RESTING_LT,
                    backgroundColor: '#FFFFFF',
                    backgroundImage: `radial-gradient(ellipse 90% 80% at 20% 25%,
                      ${key === 'lavender' ? 'rgba(234,230,248,0.90)' :
                        key === 'peach'    ? 'rgba(252,233,224,0.90)' :
                        key === 'mint'     ? 'rgba(213,242,232,0.90)' :
                                            'rgba(252,243,210,0.90)'},
                      transparent 65%)`,
                  }"
                >
                  <!-- Glyph top-left, 30% opacity -->
                  <svg
                    class="absolute pointer-events-none"
                    style="width: 34%; height: 34%; left: 5%; top: 5%; opacity: 0.30;"
                    :style="{ color: ACCENT_LT[key].ink }"
                    fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"
                  >
                    <path :d="ACCENT_GLYPHS[key]" fill-rule="evenodd" clip-rule="evenodd" />
                  </svg>
                  <!-- Content: bottom-left -->
                  <div class="absolute inset-x-0 bottom-0 p-3 space-y-0.5">
                    <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: ACCENT_LT[key].ink }">{{ ACCENT_LT[key].label }}</p>
                    <p class="text-[11px]" :style="{ color: L.inkSecondary }">{{ ACCENT_LT[key].meta }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- 3. Empty state B -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: L.inkTertiary }">Empty state — no tasks assigned</p>
              <div class="max-w-xs">
                <div
                  class="gc-b-lt relative rounded-[20px] overflow-hidden cursor-pointer"
                  :style="{ boxShadow: SHADOW_RESTING_LT }"
                  style="background-color: #FFFFFF;
                    background-image: radial-gradient(ellipse 90% 80% at 20% 25%, rgba(213, 242, 232, 0.85), transparent 65%);
                    min-height: 180px;"
                >
                  <svg
                    class="absolute pointer-events-none"
                    style="width: 28%; height: 28%; left: 5%; top: 5%; opacity: 0.30; color: #2E8A62;"
                    fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"
                  >
                    <path :d="GLYPH.checkCircle" fill-rule="evenodd" clip-rule="evenodd" />
                  </svg>
                  <div class="absolute inset-x-0 bottom-0 p-5 space-y-3">
                    <div class="space-y-1">
                      <p class="text-[14px] font-semibold" :style="{ color: L.inkPrimary }">No tasks yet</p>
                      <p class="text-[12px]" :style="{ color: L.inkSecondary }">Add your first task to start tracking.</p>
                    </div>
                    <button
                      class="rounded-full px-4 py-1.5 text-[12px] font-semibold border"
                      :style="{ borderColor: L.borderStrong, color: L.inkPrimary, background: L.surfaceRaised }"
                    >Add a task</button>
                  </div>
                </div>
              </div>
            </div>

            <!-- 4. Hero metric card B -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: L.inkTertiary }">Hero metric card — tasks completed</p>
              <div class="max-w-xs">
                <div
                  class="gc-b-lt relative rounded-[20px] overflow-hidden cursor-pointer"
                  :style="{ boxShadow: SHADOW_RESTING_LT }"
                  style="background-color: #FFFFFF;
                    background-image: radial-gradient(ellipse 90% 80% at 20% 30%, rgba(234, 230, 248, 0.90), transparent 60%);
                    min-height: 200px;"
                >
                  <svg
                    class="absolute pointer-events-none"
                    style="width: 28%; height: 28%; left: 5%; top: 5%; opacity: 0.30; color: #6856B2;"
                    fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"
                  >
                    <path :d="GLYPH.checkCircle" fill-rule="evenodd" clip-rule="evenodd" />
                  </svg>
                  <div class="absolute inset-x-0 bottom-0 p-6 space-y-1">
                    <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Tasks completed this week</p>
                    <p
                      class="font-heading leading-none tracking-tight"
                      style="font-size: clamp(4rem, 10vw, 11.25rem); letter-spacing: -0.03em;"
                      :style="{ color: L.inkPrimary }"
                    >18</p>
                    <p class="text-[13px]" :style="{ color: L.inkSecondary }">+4 vs last week</p>
                  </div>
                </div>
              </div>
            </div>

          </div><!-- /light panel B -->

          <!-- ─── DARK PANEL B ─────────────────────────────────────── -->
          <div class="rounded-2xl border p-6 space-y-8" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

            <!-- 1. Single hero card B dark -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: D.inkTertiary }">Single hero — vault category tile</p>
              <div class="max-w-md mx-auto">
                <div
                  class="gc-b-dk relative rounded-[20px] overflow-hidden cursor-pointer aspect-[4/5]"
                  :style="{ boxShadow: SHADOW_RESTING_DK }"
                  style="background-color: #1C1B19;
                    background-image: radial-gradient(ellipse 80% 70% at 20% 25%, rgba(104, 86, 178, 0.38), transparent 65%);
                    box-shadow: inset 0 0 40px rgba(104, 86, 178, 0.06), 0 1px 2px rgba(0,0,0,0.30), 0 2px 6px rgba(0,0,0,0.25);"
                >
                  <svg
                    class="absolute pointer-events-none"
                    style="width: 28%; height: 28%; left: 5%; top: 5%; opacity: 0.30; color: #B6A8E6;"
                    fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"
                  >
                    <path :d="GLYPH.shieldCheck" fill-rule="evenodd" clip-rule="evenodd" />
                  </svg>
                  <div class="absolute inset-x-0 bottom-0 p-6 space-y-1">
                    <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Vault · Medical Records</p>
                    <p class="text-[20px] font-semibold leading-snug" :style="{ color: D.inkPrimary }">Health &amp; Medical</p>
                    <p class="text-[13px]" :style="{ color: D.inkSecondary }">12 entries · Updated 2 days ago</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- 2. Row of 4 accent cards B dark -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: D.inkTertiary }">Row of 4 — accent radial fills</p>
              <div class="grid grid-cols-4 gap-3">
                <div
                  v-for="key in ACCENT_KEYS"
                  :key="key"
                  class="gc-b-dk relative rounded-[20px] overflow-hidden cursor-pointer aspect-square"
                  :style="{
                    boxShadow: SHADOW_RESTING_DK,
                    backgroundColor: '#1C1B19',
                    backgroundImage: `radial-gradient(ellipse 90% 80% at 20% 25%,
                      ${key === 'lavender' ? 'rgba(104,86,178,0.40)'  :
                        key === 'peach'    ? 'rgba(240,168,130,0.40)' :
                        key === 'mint'     ? 'rgba(124,214,174,0.35)' :
                                            'rgba(230,196,82,0.35)'},
                      transparent 65%)`,
                  }"
                >
                  <svg
                    class="absolute pointer-events-none"
                    style="width: 34%; height: 34%; left: 5%; top: 5%; opacity: 0.30;"
                    :style="{ color: ACCENT_DK[key].ink }"
                    fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"
                  >
                    <path :d="ACCENT_GLYPHS[key]" fill-rule="evenodd" clip-rule="evenodd" />
                  </svg>
                  <div class="absolute inset-x-0 bottom-0 p-3 space-y-0.5">
                    <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: ACCENT_DK[key].ink }">{{ ACCENT_DK[key].label }}</p>
                    <p class="text-[11px]" :style="{ color: D.inkSecondary }">{{ ACCENT_DK[key].meta }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- 3. Empty state B dark -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: D.inkTertiary }">Empty state — no tasks assigned</p>
              <div class="max-w-xs">
                <div
                  class="gc-b-dk relative rounded-[20px] overflow-hidden cursor-pointer"
                  :style="{ boxShadow: SHADOW_RESTING_DK }"
                  style="background-color: #1C1B19;
                    background-image: radial-gradient(ellipse 90% 80% at 20% 25%, rgba(124, 214, 174, 0.35), transparent 65%);
                    min-height: 180px;"
                >
                  <svg
                    class="absolute pointer-events-none"
                    style="width: 28%; height: 28%; left: 5%; top: 5%; opacity: 0.30; color: #7CD6AE;"
                    fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"
                  >
                    <path :d="GLYPH.checkCircle" fill-rule="evenodd" clip-rule="evenodd" />
                  </svg>
                  <div class="absolute inset-x-0 bottom-0 p-5 space-y-3">
                    <div class="space-y-1">
                      <p class="text-[14px] font-semibold" :style="{ color: D.inkPrimary }">No tasks yet</p>
                      <p class="text-[12px]" :style="{ color: D.inkSecondary }">Add your first task to start tracking.</p>
                    </div>
                    <button
                      class="rounded-full px-4 py-1.5 text-[12px] font-semibold border"
                      :style="{ borderColor: D.borderStrong, color: D.inkPrimary, background: D.surfaceRaised }"
                    >Add a task</button>
                  </div>
                </div>
              </div>
            </div>

            <!-- 4. Hero metric card B dark -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: D.inkTertiary }">Hero metric card — tasks completed</p>
              <div class="max-w-xs">
                <div
                  class="gc-b-dk relative rounded-[20px] overflow-hidden cursor-pointer"
                  :style="{ boxShadow: SHADOW_RESTING_DK }"
                  style="background-color: #1C1B19;
                    background-image: radial-gradient(ellipse 90% 80% at 20% 30%, rgba(104, 86, 178, 0.40), transparent 60%);
                    min-height: 200px;"
                >
                  <svg
                    class="absolute pointer-events-none"
                    style="width: 28%; height: 28%; left: 5%; top: 5%; opacity: 0.30; color: #B6A8E6;"
                    fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"
                  >
                    <path :d="GLYPH.checkCircle" fill-rule="evenodd" clip-rule="evenodd" />
                  </svg>
                  <div class="absolute inset-x-0 bottom-0 p-6 space-y-1">
                    <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Tasks completed this week</p>
                    <p
                      class="font-heading leading-none tracking-tight"
                      style="font-size: clamp(4rem, 10vw, 11.25rem); letter-spacing: -0.03em;"
                      :style="{ color: D.inkPrimary }"
                    >18</p>
                    <p class="text-[13px]" :style="{ color: D.inkSecondary }">+4 vs last week</p>
                  </div>
                </div>
              </div>
            </div>

          </div><!-- /dark panel B -->

        </div>
      </VariantFrame>
    </section>


    <!-- ══════════════════════════════════════════════════════════════
         VARIANT C — Angular linear gradient + glyph as texture
         135° sweep across accent tones — energetic, "on fire" vibe.
         Glyph at ~8% opacity, partially off-card, purely textural.
         Large hero typography ("text-hero" scale number) is the focal point.
         Best for streak cards, urgency states, dashboard hero metrics.
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-20">
      <VariantFrame label="C" caption="Angular 135° linear gradient with glyph as off-edge texture (~8%) — energetic, streak-ready, hero-metric scale.">
        <div class="w-full space-y-10">

          <!-- ─── LIGHT PANEL C ─────────────────────────────────────── -->
          <div class="rounded-2xl border p-6 space-y-8" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>

            <!-- 1. Single hero card C — "On Fire" streak -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: L.inkTertiary }">Single hero — on-fire streak card</p>
              <div class="max-w-md mx-auto">
                <div
                  class="gc-c-lt relative rounded-[20px] overflow-hidden cursor-pointer aspect-[4/5]"
                  :style="{ boxShadow: SHADOW_RESTING_LT }"
                  style="background-color: #FFFFFF;
                    background-image:
                      linear-gradient(135deg, rgba(252, 233, 224, 1.0) 0%, rgba(252, 243, 210, 0.90) 45%, rgba(234, 230, 248, 0.70) 100%);"
                >
                  <!-- Glyph: large, partially off top-right edge, 8% opacity — pure texture -->
                  <svg
                    class="absolute pointer-events-none"
                    style="width: 90%; height: 90%; right: -20%; top: -15%; opacity: 0.08; color: #BA562E;"
                    fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"
                  >
                    <path :d="GLYPH.fire" />
                  </svg>
                  <!-- Content: full-card, vertically centered -->
                  <div class="absolute inset-0 flex flex-col justify-center p-6 space-y-2">
                    <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">On Fire</p>
                    <p class="text-[22px] font-semibold leading-snug" :style="{ color: L.inkPrimary }">7-day Streak</p>
                    <p class="text-[14px]" :style="{ color: L.inkSecondary }">Keep it going tomorrow</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- 2. Row of 4 accent cards C -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: L.inkTertiary }">Row of 4 — angular accent fills</p>
              <div class="grid grid-cols-4 gap-3">
                <div
                  v-for="key in ACCENT_KEYS"
                  :key="key"
                  class="gc-c-lt relative rounded-[20px] overflow-hidden cursor-pointer aspect-square"
                  :style="{
                    boxShadow: SHADOW_RESTING_LT,
                    backgroundColor: '#FFFFFF',
                    backgroundImage: key === 'lavender'
                      ? 'linear-gradient(135deg, #EAE6F8 0%, #D8D0F2 50%, #EDE9F9 100%)'
                      : key === 'peach'
                        ? 'linear-gradient(135deg, #FCE9E0 0%, #F8D4C4 50%, #FCEEE8 100%)'
                        : key === 'mint'
                          ? 'linear-gradient(135deg, #D5F2E8 0%, #BBE8D8 50%, #D5F2E8 100%)'
                          : 'linear-gradient(135deg, #FCF3D2 0%, #F5E4A8 50%, #FCF3D2 100%)',
                  }"
                >
                  <!-- Glyph: large, off-edge, ~8% opacity -->
                  <svg
                    class="absolute pointer-events-none"
                    style="width: 85%; height: 85%; right: -18%; top: -18%; opacity: 0.08;"
                    :style="{ color: ACCENT_LT[key].ink }"
                    fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"
                  >
                    <path :d="ACCENT_GLYPHS[key]" fill-rule="evenodd" clip-rule="evenodd" />
                  </svg>
                  <div class="absolute inset-x-0 bottom-0 p-3 space-y-0.5">
                    <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: ACCENT_LT[key].ink }">{{ ACCENT_LT[key].label }}</p>
                    <p class="text-[11px]" :style="{ color: L.inkSecondary }">{{ ACCENT_LT[key].meta }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- 3. Empty state C -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: L.inkTertiary }">Empty state — no rewards redeemed</p>
              <div class="max-w-xs">
                <div
                  class="gc-c-lt relative rounded-[20px] overflow-hidden cursor-pointer"
                  :style="{ boxShadow: SHADOW_RESTING_LT }"
                  style="background-color: #FFFFFF;
                    background-image: linear-gradient(135deg, #FCF3D2 0%, #F5E4A8 50%, #FCE9E0 100%);
                    min-height: 180px;"
                >
                  <svg
                    class="absolute pointer-events-none"
                    style="width: 85%; height: 85%; right: -18%; top: -18%; opacity: 0.08; color: #A2780C;"
                    fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"
                  >
                    <path :d="GLYPH.star" fill-rule="evenodd" clip-rule="evenodd" />
                  </svg>
                  <div class="absolute inset-0 flex flex-col items-start justify-center p-6 space-y-3">
                    <div class="space-y-1">
                      <p class="text-[14px] font-semibold" :style="{ color: L.inkPrimary }">No rewards yet</p>
                      <p class="text-[12px]" :style="{ color: L.inkSecondary }">Earn points to unlock the rewards store.</p>
                    </div>
                    <button
                      class="rounded-full px-4 py-1.5 text-[12px] font-semibold"
                      :style="{ background: L.inkPrimary, color: L.surfaceRaised }"
                    >View rewards</button>
                  </div>
                </div>
              </div>
            </div>

            <!-- 4. Hero metric card C — large number focal point -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: L.inkTertiary }">Hero metric card — points balance (warm gradient)</p>
              <div class="max-w-xs">
                <div
                  class="gc-c-lt relative rounded-[20px] overflow-hidden cursor-pointer"
                  :style="{ boxShadow: SHADOW_RESTING_LT }"
                  style="background-color: #FFFFFF;
                    background-image:
                      linear-gradient(135deg, rgba(252, 233, 224, 1.0) 0%, rgba(252, 243, 210, 0.95) 50%, rgba(234, 230, 248, 0.80) 100%);
                    min-height: 200px;"
                >
                  <!-- Glyph: off-edge texture -->
                  <svg
                    class="absolute pointer-events-none"
                    style="width: 90%; height: 90%; right: -22%; top: -20%; opacity: 0.08; color: #BA562E;"
                    fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"
                  >
                    <path :d="GLYPH.bolt" clip-rule="evenodd" fill-rule="evenodd" />
                  </svg>
                  <div class="absolute inset-0 flex flex-col justify-center p-6 space-y-1">
                    <p
                      class="font-heading leading-none tracking-tight"
                      style="font-size: clamp(4rem, 10vw, 11.25rem); letter-spacing: -0.03em;"
                      :style="{ color: L.inkPrimary }"
                    >247</p>
                    <p class="text-[11px] font-medium uppercase tracking-widest mt-2" :style="{ color: L.inkTertiary }">Points earned this week</p>
                  </div>
                </div>
              </div>
            </div>

          </div><!-- /light panel C -->

          <!-- ─── DARK PANEL C ─────────────────────────────────────── -->
          <div class="rounded-2xl border p-6 space-y-8" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

            <!-- 1. Single hero card C dark -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: D.inkTertiary }">Single hero — on-fire streak card</p>
              <div class="max-w-md mx-auto">
                <div
                  class="gc-c-dk relative rounded-[20px] overflow-hidden cursor-pointer aspect-[4/5]"
                  :style="{ boxShadow: SHADOW_RESTING_DK }"
                  style="background-color: #1C1B19;
                    background-image:
                      linear-gradient(135deg, rgba(240,168,130,0.55) 0%, rgba(230,196,82,0.45) 50%, rgba(182,168,230,0.30) 100%);"
                >
                  <svg
                    class="absolute pointer-events-none"
                    style="width: 90%; height: 90%; right: -20%; top: -15%; opacity: 0.08; color: #F0A882;"
                    fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"
                  >
                    <path :d="GLYPH.fire" />
                  </svg>
                  <div class="absolute inset-0 flex flex-col justify-center p-6 space-y-2">
                    <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">On Fire</p>
                    <p class="text-[22px] font-semibold leading-snug" :style="{ color: D.inkPrimary }">7-day Streak</p>
                    <p class="text-[14px]" :style="{ color: D.inkSecondary }">Keep it going tomorrow</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- 2. Row of 4 accent cards C dark -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: D.inkTertiary }">Row of 4 — angular accent fills</p>
              <div class="grid grid-cols-4 gap-3">
                <div
                  v-for="key in ACCENT_KEYS"
                  :key="key"
                  class="gc-c-dk relative rounded-[20px] overflow-hidden cursor-pointer aspect-square"
                  :style="{
                    boxShadow: SHADOW_RESTING_DK,
                    backgroundColor: '#1C1B19',
                    backgroundImage: key === 'lavender'
                      ? 'linear-gradient(135deg, #302A48 0%, #4B4368 50%, #302A48 100%)'
                      : key === 'peach'
                        ? 'linear-gradient(135deg, #3E241A 0%, #623E2F 50%, #3E241A 100%)'
                        : key === 'mint'
                          ? 'linear-gradient(135deg, #18342A 0%, #2C5444 50%, #18342A 100%)'
                          : 'linear-gradient(135deg, #342C0A 0%, #584A18 50%, #342C0A 100%)',
                  }"
                >
                  <svg
                    class="absolute pointer-events-none"
                    style="width: 85%; height: 85%; right: -18%; top: -18%; opacity: 0.08;"
                    :style="{ color: ACCENT_DK[key].ink }"
                    fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"
                  >
                    <path :d="ACCENT_GLYPHS[key]" fill-rule="evenodd" clip-rule="evenodd" />
                  </svg>
                  <div class="absolute inset-x-0 bottom-0 p-3 space-y-0.5">
                    <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: ACCENT_DK[key].ink }">{{ ACCENT_DK[key].label }}</p>
                    <p class="text-[11px]" :style="{ color: D.inkSecondary }">{{ ACCENT_DK[key].meta }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- 3. Empty state C dark -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: D.inkTertiary }">Empty state — no rewards redeemed</p>
              <div class="max-w-xs">
                <div
                  class="gc-c-dk relative rounded-[20px] overflow-hidden cursor-pointer"
                  :style="{ boxShadow: SHADOW_RESTING_DK }"
                  style="background-color: #1C1B19;
                    background-image: linear-gradient(135deg, #342C0A 0%, #584A18 50%, #3E241A 100%);
                    min-height: 180px;"
                >
                  <svg
                    class="absolute pointer-events-none"
                    style="width: 85%; height: 85%; right: -18%; top: -18%; opacity: 0.08; color: #E6C452;"
                    fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"
                  >
                    <path :d="GLYPH.star" fill-rule="evenodd" clip-rule="evenodd" />
                  </svg>
                  <div class="absolute inset-0 flex flex-col items-start justify-center p-6 space-y-3">
                    <div class="space-y-1">
                      <p class="text-[14px] font-semibold" :style="{ color: D.inkPrimary }">No rewards yet</p>
                      <p class="text-[12px]" :style="{ color: D.inkSecondary }">Earn points to unlock the rewards store.</p>
                    </div>
                    <button
                      class="rounded-full px-4 py-1.5 text-[12px] font-semibold"
                      :style="{ background: D.inkPrimary, color: D.surfaceSunken }"
                    >View rewards</button>
                  </div>
                </div>
              </div>
            </div>

            <!-- 4. Hero metric card C dark — large number focal point -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: D.inkTertiary }">Hero metric card — points balance (warm gradient)</p>
              <div class="max-w-xs">
                <div
                  class="gc-c-dk relative rounded-[20px] overflow-hidden cursor-pointer"
                  :style="{ boxShadow: SHADOW_RESTING_DK }"
                  style="background-color: #1C1B19;
                    background-image:
                      linear-gradient(135deg, rgba(240,168,130,0.55) 0%, rgba(230,196,82,0.45) 50%, rgba(182,168,230,0.30) 100%);
                    min-height: 200px;"
                >
                  <svg
                    class="absolute pointer-events-none"
                    style="width: 90%; height: 90%; right: -22%; top: -20%; opacity: 0.08; color: #F0A882;"
                    fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"
                  >
                    <path :d="GLYPH.bolt" clip-rule="evenodd" fill-rule="evenodd" />
                  </svg>
                  <div class="absolute inset-0 flex flex-col justify-center p-6 space-y-1">
                    <p
                      class="font-heading leading-none tracking-tight"
                      style="font-size: clamp(4rem, 10vw, 11.25rem); letter-spacing: -0.03em;"
                      :style="{ color: D.inkPrimary }"
                    >247</p>
                    <p class="text-[11px] font-medium uppercase tracking-widest mt-2" :style="{ color: D.inkTertiary }">Points earned this week</p>
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
        <h2 class="text-[17px] font-semibold" :style="{ color: L.inkPrimary }">When to use GradientCard</h2>

        <p class="text-[14px]" :style="{ color: L.inkSecondary }">
          GradientCard is always the fallback when a photo isn't available. Every imageless content surface should use one of these treatments — vault entries, tasks without avatars, achievement tiles, empty states, dashboard hero metrics. Never render a blank white rectangle.
        </p>

        <div class="space-y-3 text-[14px]" :style="{ color: L.inkSecondary }">
          <div class="flex gap-3">
            <span class="flex-shrink-0 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold mt-0.5" :style="{ background: L.surfaceSunken, color: L.inkPrimary }">A</span>
            <div>
              <strong :style="{ color: L.inkPrimary }">Quiet elegance:</strong>
              The iridescent-subtle wash is airy and neutral — content can be anything (vault, tasks, achievements). The 15% watermark glyph is barely there. Best for dense grids where the card needs character without demanding attention. Works in any category — lavender/peach/mint/sun accents all feel equally calm.
            </div>
          </div>
          <div class="flex gap-3">
            <span class="flex-shrink-0 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold mt-0.5" :style="{ background: L.surfaceSunken, color: L.inkPrimary }">B</span>
            <div>
              <strong :style="{ color: L.inkPrimary }">Category landing:</strong>
              The radial well creates a directed light source — the glyph top-left and content bottom-left form a natural diagonal. Best for category tiles on list pages (vault categories, task lists, reward types) where one card per category sits at a larger size and the glyph needs to feel like an intentional design choice rather than an afterthought. The 30% decorative glyph is clearly ornamental.
            </div>
          </div>
          <div class="flex gap-3">
            <span class="flex-shrink-0 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold mt-0.5" :style="{ background: L.surfaceSunken, color: L.inkPrimary }">C</span>
            <div>
              <strong :style="{ color: L.inkPrimary }">Energy + hero metrics:</strong>
              The angular sweep gives a kinetic feeling — forward motion, urgency, celebration. Best for streak cards, "on fire" moments, and dashboard hero-number cards where the BIG NUMBER is the entire point. The 8% off-edge glyph is purely texture; you can swap it for any relevant icon (fire, bolt, trophy) without disrupting legibility. The warm iridescent token is a natural pairing.
            </div>
          </div>
        </div>

        <div class="border-t pt-5 space-y-4" :style="{ borderColor: L.borderSubtle }">
          <h3 class="text-[15px] font-semibold" :style="{ color: L.inkPrimary }">Decision criteria</h3>
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-[13px]" :style="{ color: L.inkSecondary }">
            <div class="rounded-xl p-4 space-y-2" :style="{ background: L.surfaceSunken }">
              <p class="font-semibold text-[12px] uppercase tracking-widest" :style="{ color: L.inkTertiary }">Glyph visibility</p>
              <p><strong :style="{ color: L.inkPrimary }">A — Watermark</strong> (~15%) — barely there, emblem-like, never competing</p>
              <p><strong :style="{ color: L.inkPrimary }">B — Decorative</strong> (~30%) — clearly intentional, reads as art direction</p>
              <p><strong :style="{ color: L.inkPrimary }">C — Texture</strong> (~8%) — indistinct, purely pattern, glyph identity is secondary</p>
            </div>
            <div class="rounded-xl p-4 space-y-2" :style="{ background: L.surfaceSunken }">
              <p class="font-semibold text-[12px] uppercase tracking-widest" :style="{ color: L.inkTertiary }">Best context</p>
              <p><strong :style="{ color: L.inkPrimary }">A</strong> — Dense grids, vault entries, achievement tiles, equal-weight tiles</p>
              <p><strong :style="{ color: L.inkPrimary }">B</strong> — Category landing, single featured tile, selection UIs</p>
              <p><strong :style="{ color: L.inkPrimary }">C</strong> — Dashboard hero, streak/urgency state, points balance card</p>
            </div>
            <div class="rounded-xl p-4 space-y-2" :style="{ background: L.surfaceSunken }">
              <p class="font-semibold text-[12px] uppercase tracking-widest" :style="{ color: L.inkTertiary }">Energy level</p>
              <p><strong :style="{ color: L.inkPrimary }">A</strong> — Quietest. Neutral companion to content. Airy.</p>
              <p><strong :style="{ color: L.inkPrimary }">B</strong> — Medium. Directed. Compositional.</p>
              <p><strong :style="{ color: L.inkPrimary }">C</strong> — Most energetic. Forward motion. Celebratory.</p>
            </div>
          </div>
        </div>

        <div class="border-t pt-5" :style="{ borderColor: L.borderSubtle }">
          <p class="text-[15px] font-semibold" :style="{ color: L.inkPrimary }">
            Reply <strong>A</strong>, <strong>B</strong>, or <strong>C</strong> to lock the GradientCard treatment.
          </p>
        </div>
      </div>
    </section>

  </ComponentPage>
</template>

<style scoped>
/*
  ═══════════════════════════════════════════════════════════════════
  VARIANT A — HOVER TRANSITIONS — LIGHT
  Lift + shadow deepening. No background-image transition (locked rule).
  ═══════════════════════════════════════════════════════════════════
*/
.gc-a-lt {
  transition: box-shadow 200ms cubic-bezier(0.16, 1, 0.3, 1),
              transform   200ms cubic-bezier(0.16, 1, 0.3, 1);
}
.gc-a-lt:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(28, 20, 10, 0.08), 0 16px 32px rgba(28, 20, 10, 0.10);
}

/*
  ═══════════════════════════════════════════════════════════════════
  VARIANT A — HOVER TRANSITIONS — DARK
  ═══════════════════════════════════════════════════════════════════
*/
.gc-a-dk {
  transition: box-shadow 200ms cubic-bezier(0.16, 1, 0.3, 1),
              transform   200ms cubic-bezier(0.16, 1, 0.3, 1);
}
.gc-a-dk:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.40), 0 16px 32px rgba(0, 0, 0, 0.30);
}

/*
  ═══════════════════════════════════════════════════════════════════
  VARIANT B — HOVER TRANSITIONS — LIGHT
  ═══════════════════════════════════════════════════════════════════
*/
.gc-b-lt {
  transition: box-shadow 200ms cubic-bezier(0.16, 1, 0.3, 1),
              transform   200ms cubic-bezier(0.16, 1, 0.3, 1);
}
.gc-b-lt:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(28, 20, 10, 0.08), 0 16px 32px rgba(28, 20, 10, 0.10);
}

/*
  ═══════════════════════════════════════════════════════════════════
  VARIANT B — HOVER TRANSITIONS — DARK
  ═══════════════════════════════════════════════════════════════════
*/
.gc-b-dk {
  transition: box-shadow 200ms cubic-bezier(0.16, 1, 0.3, 1),
              transform   200ms cubic-bezier(0.16, 1, 0.3, 1);
}
.gc-b-dk:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.40), 0 16px 32px rgba(0, 0, 0, 0.30);
}

/*
  ═══════════════════════════════════════════════════════════════════
  VARIANT C — HOVER TRANSITIONS — LIGHT
  ═══════════════════════════════════════════════════════════════════
*/
.gc-c-lt {
  transition: box-shadow 200ms cubic-bezier(0.16, 1, 0.3, 1),
              transform   200ms cubic-bezier(0.16, 1, 0.3, 1);
}
.gc-c-lt:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(28, 20, 10, 0.08), 0 16px 32px rgba(28, 20, 10, 0.10);
}

/*
  ═══════════════════════════════════════════════════════════════════
  VARIANT C — HOVER TRANSITIONS — DARK
  ═══════════════════════════════════════════════════════════════════
*/
.gc-c-dk {
  transition: box-shadow 200ms cubic-bezier(0.16, 1, 0.3, 1),
              transform   200ms cubic-bezier(0.16, 1, 0.3, 1);
}
.gc-c-dk:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.40), 0 16px 32px rgba(0, 0, 0, 0.30);
}

/*
  ═══════════════════════════════════════════════════════════════════
  REDUCED MOTION — disable transform, keep shadow transition intact
  ═══════════════════════════════════════════════════════════════════
*/
@media (prefers-reduced-motion: reduce) {
  .gc-a-lt,
  .gc-a-dk,
  .gc-b-lt,
  .gc-b-dk,
  .gc-c-lt,
  .gc-c-dk {
    transition: box-shadow 200ms;
    transform: none !important;
  }
}
</style>
