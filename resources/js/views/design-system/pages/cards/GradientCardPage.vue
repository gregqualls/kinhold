<script setup>
import { h } from 'vue'
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import KinGradientCard from '@/components/design-system/KinGradientCard.vue'
import {
  ShieldCheckIcon,
  FireIcon,
  CheckCircleIcon,
  StarIcon,
} from '@heroicons/vue/24/solid'

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
  borderStrong:  '#403E3A',
}

// Row-of-4 accent demo data.
// inkVar is the CSS variable name used for the kicker label color so it
// flips light/dark via tokens.css automatically.
const ACCENTS = [
  { key: 'lavender', glyph: ShieldCheckIcon,  inkVar: '--accent-lavender-bold', label: 'Medical',  meta: '12 items' },
  { key: 'peach',    glyph: FireIcon,         inkVar: '--accent-peach-bold',    label: 'Streak',   meta: '5 days'  },
  { key: 'mint',     glyph: CheckCircleIcon,  inkVar: '--accent-mint-bold',     label: 'Wellness', meta: '73%'     },
  { key: 'sun',      glyph: StarIcon,         inkVar: '--accent-sun-bold',      label: 'Reward',   meta: '240 pts' },
]
</script>

<template>
  <ComponentPage
    title="2.3 GradientCard"
    description="The quiet counterpart to PhotoCard — for imageless content. Subtle iridescent wash (or low-intensity accent tint) + optional small category glyph top-right + content bottom-left. Sits next to FlatCard and PhotoCard as a peer, not a decorative feature. Used for vault entries, imageless tasks, empty-state placeholders, and hero metric cards."
    status="chosen"
  >
    <!-- ══════════════════════════════════════════════════════════════
         GRADIENTCARD — Radial gradient + glyph upper-left + content bottom-left
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-20">
      <VariantFrame label="GradientCard" caption="Quiet ambient wash + small top-right category glyph + content bottom-left. Category is a hint, not a headline.">
        <div class="w-full space-y-10">
          <!-- ─── LIGHT PANEL ─────────────────────────────────────── -->
          <div class="rounded-2xl border p-6 space-y-8" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>

            <!-- 1. Single hero — vault category tile -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: L.inkTertiary }">Single hero — vault category tile</p>
              <div class="max-w-md mx-auto">
                <KinGradientCard
                  variant="lavender"
                  :glyph="ShieldCheckIcon"
                  padding="md"
                  interactive
                  class="aspect-[4/5]"
                >
                  <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Vault · Medical Records</p>
                  <p class="text-[20px] font-semibold leading-snug" :style="{ color: L.inkPrimary }">Health &amp; Medical</p>
                  <p class="text-[13px]" :style="{ color: L.inkSecondary }">12 entries · Updated 2 days ago</p>
                </KinGradientCard>
              </div>
            </div>

            <!-- 2. Row of 4 accent cards -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: L.inkTertiary }">Row of 4 — accent radial fills</p>
              <div class="grid grid-cols-4 gap-3">
                <KinGradientCard
                  v-for="item in ACCENTS"
                  :key="item.key"
                  :variant="item.key"
                  :glyph="item.glyph"
                  padding="sm"
                  interactive
                  class="aspect-square"
                >
                  <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: `rgb(var(${item.inkVar}))` }">{{ item.label }}</p>
                  <p class="text-[11px]" :style="{ color: L.inkSecondary }">{{ item.meta }}</p>
                </KinGradientCard>
              </div>
            </div>

            <!-- 3. Empty state -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: L.inkTertiary }">Empty state — no tasks assigned</p>
              <div class="max-w-xs">
                <KinGradientCard
                  variant="mint"
                  :glyph="CheckCircleIcon"
                  padding="md"
                  interactive
                  style="min-height: 180px"
                >
                  <div class="space-y-3">
                    <div class="space-y-1">
                      <p class="text-[14px] font-semibold" :style="{ color: L.inkPrimary }">No tasks yet</p>
                      <p class="text-[12px]" :style="{ color: L.inkSecondary }">Add your first task to start tracking.</p>
                    </div>
                    <button
                      class="rounded-full px-4 py-1.5 text-[12px] font-semibold border"
                      :style="{ borderColor: L.borderStrong, color: L.inkPrimary, background: L.surfaceRaised }"
                    >
                      Add a task
                    </button>
                  </div>
                </KinGradientCard>
              </div>
            </div>

            <!-- 4. Hero metric card -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: L.inkTertiary }">Hero metric card — tasks completed</p>
              <div class="max-w-xs">
                <KinGradientCard
                  variant="lavender"
                  :glyph="CheckCircleIcon"
                  padding="md"
                  interactive
                  style="min-height: 200px"
                >
                  <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Tasks completed this week</p>
                  <p
                    class="font-heading leading-none tracking-tight"
                    style="font-size: clamp(4rem, 10vw, 11.25rem); letter-spacing: -0.03em;"
                    :style="{ color: L.inkPrimary }"
                  >
                    18
                  </p>
                  <p class="text-[13px]" :style="{ color: L.inkSecondary }">+4 vs last week</p>
                </KinGradientCard>
              </div>
            </div>
          </div><!-- /light panel -->

          <!-- ─── DARK PANEL ─────────────────────────────────────── -->
          <div class="dark rounded-2xl border p-6 space-y-8" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

            <!-- 1. Single hero dark -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: D.inkTertiary }">Single hero — vault category tile</p>
              <div class="max-w-md mx-auto">
                <KinGradientCard
                  variant="lavender"
                  :glyph="ShieldCheckIcon"
                  padding="md"
                  interactive
                  class="aspect-[4/5]"
                >
                  <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Vault · Medical Records</p>
                  <p class="text-[20px] font-semibold leading-snug" :style="{ color: D.inkPrimary }">Health &amp; Medical</p>
                  <p class="text-[13px]" :style="{ color: D.inkSecondary }">12 entries · Updated 2 days ago</p>
                </KinGradientCard>
              </div>
            </div>

            <!-- 2. Row of 4 accent cards dark -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: D.inkTertiary }">Row of 4 — accent radial fills</p>
              <div class="grid grid-cols-4 gap-3">
                <KinGradientCard
                  v-for="item in ACCENTS"
                  :key="item.key"
                  :variant="item.key"
                  :glyph="item.glyph"
                  padding="sm"
                  interactive
                  class="aspect-square"
                >
                  <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: `rgb(var(${item.inkVar}))` }">{{ item.label }}</p>
                  <p class="text-[11px]" :style="{ color: D.inkSecondary }">{{ item.meta }}</p>
                </KinGradientCard>
              </div>
            </div>

            <!-- 3. Empty state dark -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: D.inkTertiary }">Empty state — no tasks assigned</p>
              <div class="max-w-xs">
                <KinGradientCard
                  variant="mint"
                  :glyph="CheckCircleIcon"
                  padding="md"
                  interactive
                  style="min-height: 180px"
                >
                  <div class="space-y-3">
                    <div class="space-y-1">
                      <p class="text-[14px] font-semibold" :style="{ color: D.inkPrimary }">No tasks yet</p>
                      <p class="text-[12px]" :style="{ color: D.inkSecondary }">Add your first task to start tracking.</p>
                    </div>
                    <button
                      class="rounded-full px-4 py-1.5 text-[12px] font-semibold border"
                      :style="{ borderColor: D.borderStrong, color: D.inkPrimary, background: 'transparent' }"
                    >
                      Add a task
                    </button>
                  </div>
                </KinGradientCard>
              </div>
            </div>

            <!-- 4. Hero metric card dark -->
            <div>
              <p class="text-[11px] mb-4 font-medium" :style="{ color: D.inkTertiary }">Hero metric card — tasks completed</p>
              <div class="max-w-xs">
                <KinGradientCard
                  variant="lavender"
                  :glyph="CheckCircleIcon"
                  padding="md"
                  interactive
                  style="min-height: 200px"
                >
                  <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Tasks completed this week</p>
                  <p
                    class="font-heading leading-none tracking-tight"
                    style="font-size: clamp(4rem, 10vw, 11.25rem); letter-spacing: -0.03em;"
                    :style="{ color: D.inkPrimary }"
                  >
                    18
                  </p>
                  <p class="text-[13px]" :style="{ color: D.inkSecondary }">+4 vs last week</p>
                </KinGradientCard>
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
        <h2 class="text-[17px] font-semibold" :style="{ color: L.inkPrimary }">When to use GradientCard</h2>

        <ul class="space-y-3 text-[14px]" :style="{ color: L.inkSecondary }">
          <li>
            <strong :style="{ color: L.inkPrimary }">The quiet imageless card.</strong>
            For vault entries, tasks without avatars, empty states, and dashboard hero metrics. Reads as a peer of FlatCard and PhotoCard — a subtle tint is the only difference. Never a blank white rectangle; also never a loud decorative surface.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Iridescent by default, accent variants for category hints.</strong>
            Start with <code class="font-mono text-[12px] px-1 py-0.5 rounded" :style="{ background: L.surfaceSunken }">variant="iridescent"</code> — a barely-there multi-hue wash. Use an accent variant (lavender / peach / mint / sun) only when the card represents a specific category, and keep in mind the tint is deliberately low-intensity (0.35 opacity).
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Glyph is a small category marker, not a watermark.</strong>
            24px top-right, full opacity in the accent-bold color. Pick one that signals the card's role: ShieldCheck for vault, Fire for streaks, CheckCircle for wellness/tasks, Star for rewards. Omit the glyph entirely when the title alone is enough.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Diagonal composition via content position.</strong>
            Content sits bottom-left. The glyph top-right + content bottom-left creates a subtle diagonal without requiring decorative weight.
          </li>
        </ul>
      </div>
    </section>
  </ComponentPage>
</template>
