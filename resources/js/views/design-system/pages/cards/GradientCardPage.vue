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
    description="The counterpart to PhotoCard — used when there is no photo. Radial gradient anchored upper-left + ~30%-opacity glyph in the top-left + content bottom-left creates a compositional diagonal. Used for vault entries, imageless tasks, achievement tiles, empty-state placeholders, and hero metric cards."
    status="chosen"
  >

    <!-- ══════════════════════════════════════════════════════════════
         VARIANT B — Radial gradient with positioned glyph top-left,
         content bottom-left. Diagonal composition creates visual tension.
         Glyph is "decorative" (~30% opacity) — clearly ornamental,
         not competing with the content.
         Best for category landing tiles, vault category cards.
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-20">
      <VariantFrame label="GradientCard" caption="Radial gradient anchored upper-left + glyph top-left at 30% + content bottom-left — compositional diagonal, category-tile pattern.">
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
         USAGE GUIDE
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-8">
      <div class="rounded-2xl border p-6 space-y-3" :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle }">
        <h2 class="text-[17px] font-semibold" :style="{ color: L.inkPrimary }">When to use GradientCard</h2>

        <ul class="space-y-3 text-[14px]" :style="{ color: L.inkSecondary }">
          <li>
            <strong :style="{ color: L.inkPrimary }">The imageless counterpart to PhotoCard.</strong>
            Every imageless content surface uses this treatment — vault entries, tasks without avatars, achievement tiles, empty states, dashboard hero metrics. Never render a blank white rectangle.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Radial gradient + positioned glyph creates a diagonal.</strong>
            Light source anchored upper-left, content sits lower-left. The diagonal gives each imageless card a sense of direction and intent — works for both dense grids and single featured tiles.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Swap the glyph to match context.</strong>
            ShieldCheck for vault, Fire for streaks, CheckCircle for wellness/tasks, Star for rewards, Trophy for achievements, Bolt for urgency. The glyph carries identity without competing with the content.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Swap the accent color to match category.</strong>
            Lavender (default / vault / medical), peach (streaks / urgency), mint (wellness / health), sun (rewards / highlights). Family-color avatars pair naturally with same-color gradient cards.
          </li>
        </ul>
      </div>
    </section>

  </ComponentPage>
</template>

<style scoped>
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
  REDUCED MOTION — disable transform, keep shadow transition intact
  ═══════════════════════════════════════════════════════════════════
*/
@media (prefers-reduced-motion: reduce) {
  .gc-b-lt,
  .gc-b-dk {
    transition: box-shadow 200ms;
    transform: none !important;
  }
}
</style>
