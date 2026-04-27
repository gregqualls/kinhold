<script setup>
import { ref } from 'vue'
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import KinCategoryChipRow from '@/components/design-system/KinCategoryChipRow.vue'
import {
  SparklesIcon,
  StarIcon,
  BookOpenIcon,
  HomeIcon,
  AcademicCapIcon,
  BriefcaseIcon,
  HeartIcon,
  ShoppingCartIcon,
  BeakerIcon,
} from '@heroicons/vue/24/outline'

// ── Panel chrome tokens (Light/Dark hand-rolled wrappers) ────────────────────
const L = {
  surfaceApp: '#FAF8F5', surfaceRaised: '#FFFFFF', surfaceSunken: '#F5F2EE',
  inkPrimary: '#1C1C1E', inkSecondary: '#6B6966', inkTertiary: '#9C9895',
  borderSubtle: '#E8E4DF',
  accents: {
    lavender: { soft: '#EAE6F8', bold: '#6856B2' },
  },
}
const D = {
  surfaceApp: '#141311', surfaceRaised: '#1C1B19',
  inkPrimary: '#F0EDE9', inkSecondary: '#A09C97', inkTertiary: '#6E6B67',
  borderSubtle: '#2C2A27',
}

// ── Category data — 8 to force overflow on narrow viewports ──────────────────
const CATEGORIES = [
  { key: 'family',  label: 'Family',   accent: 'lavender', icon: HeartIcon       },
  { key: 'school',  label: 'School',   accent: 'peach',    icon: AcademicCapIcon },
  { key: 'home',    label: 'Home',     accent: 'mint',     icon: HomeIcon        },
  { key: 'work',    label: 'Work',     accent: 'sun',      icon: BriefcaseIcon   },
  { key: 'reading', label: 'Reading',  accent: 'lavender', icon: BookOpenIcon    },
  { key: 'health',  label: 'Health',   accent: 'mint',     icon: BeakerIcon      },
  { key: 'grocery', label: 'Grocery',  accent: 'sun',      icon: ShoppingCartIcon},
  { key: 'goals',   label: 'Goals',    accent: 'peach',    icon: StarIcon        },
]

// Reactive active-selection per variant × mode
const activeA_lt = ref(['family'])
const activeA_dk = ref(['family'])
const activeB_lt = ref(['family'])
const activeB_dk = ref(['family'])
const activeC_lt = ref(['family'])
const activeC_dk = ref(['family'])
// Hidden-filter state — hidden chip is active → overflow chip would tint
const activeC_lt_hidden = ref(['family', 'reading'])
const activeC_dk_hidden = ref(['family', 'reading'])
</script>

<template>
  <ComponentPage
    title="4.3 CategoryChipRow"
    description="Horizontal row of icon + label chips for category selection. Three layout strategies for overflow behavior — the chip style itself is locked from Tier 1.3."
    status="chosen"
  >

    <!-- ══════════════════════════════════════════════════════════════
         VARIANT A — flex-wrap: all chips visible, wraps on narrow
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="A" caption="Flex-wrap row — all chips visible, wraps to multiple lines on narrow viewports">
        <div class="w-full space-y-10">

          <!-- LIGHT PANEL -->
          <div class="rounded-2xl border p-6 space-y-8"
               :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>

            <div>
              <p class="text-xs mb-3" :style="{ color: L.inkTertiary }">Wide — all 8 categories on one line</p>
              <KinCategoryChipRow mode="wrap" :categories="CATEGORIES" v-model:active-keys="activeA_lt" />
            </div>

            <div>
              <p class="text-xs mb-3" :style="{ color: L.inkTertiary }">Narrow (320px) — wraps naturally across two lines</p>
              <div class="max-w-[320px] rounded-xl border p-3"
                   :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle }">
                <KinCategoryChipRow mode="wrap" :categories="CATEGORIES" v-model:active-keys="activeA_lt" />
              </div>
            </div>
          </div>

          <!-- DARK PANEL -->
          <div class="dark rounded-2xl border p-6 space-y-8"
               :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">Wide — all 8 categories</p>
              <KinCategoryChipRow mode="wrap" :categories="CATEGORIES" v-model:active-keys="activeA_dk" />
            </div>

            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">Narrow (320px) — wraps naturally</p>
              <div class="max-w-[320px] rounded-xl border p-3"
                   :style="{ background: D.surfaceRaised, borderColor: D.borderSubtle }">
                <KinCategoryChipRow mode="wrap" :categories="CATEGORIES" v-model:active-keys="activeA_dk" />
              </div>
            </div>
          </div>

        </div>
      </VariantFrame>
      <p class="mt-3 text-sm px-1" :style="{ color: L.inkSecondary }">
        Simplest option. No JS required — just CSS flexbox. Every chip is always reachable, but the row can grow to 2–3 lines on mobile with 8+ categories, making the page feel taller than necessary.
      </p>
    </section>

    <!-- ══════════════════════════════════════════════════════════════
         VARIANT B — overflow-x-auto scroll
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="B" caption="Horizontal scroll — single-line, no wrapping">
        <div class="w-full space-y-10">

          <!-- LIGHT PANEL -->
          <div class="rounded-2xl border p-6 space-y-8"
               :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>

            <div>
              <p class="text-xs mb-3" :style="{ color: L.inkTertiary }">Wide — single-line, scrollable</p>
              <KinCategoryChipRow mode="scroll" :categories="CATEGORIES" v-model:active-keys="activeB_lt" />
            </div>

            <div>
              <p class="text-xs mb-3" :style="{ color: L.inkTertiary }">Narrow (320px) — scroll right to reveal hidden chips</p>
              <div class="max-w-[320px] rounded-xl border p-3"
                   :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle }">
                <KinCategoryChipRow mode="scroll" :clearable="false" :categories="CATEGORIES" v-model:active-keys="activeB_lt" />
              </div>
            </div>
          </div>

          <!-- DARK PANEL -->
          <div class="dark rounded-2xl border p-6 space-y-8"
               :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">Wide — single-line</p>
              <KinCategoryChipRow mode="scroll" :categories="CATEGORIES" v-model:active-keys="activeB_dk" />
            </div>

            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">Narrow (320px) — scroll right</p>
              <div class="max-w-[320px] rounded-xl border p-3"
                   :style="{ background: D.surfaceRaised, borderColor: D.borderSubtle }">
                <KinCategoryChipRow mode="scroll" :clearable="false" :categories="CATEGORIES" v-model:active-keys="activeB_dk" />
              </div>
            </div>
          </div>

        </div>
      </VariantFrame>
      <p class="mt-3 text-sm px-1" :style="{ color: L.inkSecondary }">
        Keeps the page height compact — no matter how many categories, it's always one chip-row tall. Ideal when users typically want to scan-and-tap one category at a time.
      </p>
    </section>

    <!-- ══════════════════════════════════════════════════════════════
         VARIANT C — Hybrid: 4 visible on mobile + "+N more" on overflow
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="C" caption="Hybrid — 4 chips + '+N more' affordance on mobile; full wrap on desktop">
        <div class="w-full space-y-10">

          <!-- LIGHT PANEL -->
          <div class="rounded-2xl border p-6 space-y-8"
               :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>

            <div>
              <p class="text-xs mb-3" :style="{ color: L.inkTertiary }">Desktop — full wrap, all chips accessible</p>
              <KinCategoryChipRow mode="hybrid" :categories="CATEGORIES" v-model:active-keys="activeC_lt" />
            </div>

            <div>
              <p class="text-xs mb-3" :style="{ color: L.inkTertiary }">Mobile (320px) — 4 chips visible, "+N more" opens a bottom sheet (wired in production)</p>
              <div class="max-w-[320px] rounded-xl border p-3"
                   :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle }">
                <KinCategoryChipRow mode="hybrid" :mobile-visible="4" :clearable="false" :categories="CATEGORIES" v-model:active-keys="activeC_lt" />
              </div>
            </div>
          </div>

          <!-- DARK PANEL -->
          <div class="dark rounded-2xl border p-6 space-y-8"
               :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">Desktop — full wrap</p>
              <KinCategoryChipRow mode="hybrid" :categories="CATEGORIES" v-model:active-keys="activeC_dk" />
            </div>

            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">Mobile (320px) — 4 chips + overflow affordance</p>
              <div class="max-w-[320px] rounded-xl border p-3"
                   :style="{ background: D.surfaceRaised, borderColor: D.borderSubtle }">
                <KinCategoryChipRow mode="hybrid" :mobile-visible="4" :clearable="false" :categories="CATEGORIES" v-model:active-keys="activeC_dk" />
              </div>
            </div>
          </div>

        </div>
      </VariantFrame>
      <p class="mt-3 text-sm px-1" :style="{ color: L.inkSecondary }">
        Respects the vertical budget on mobile while giving desktop users the full set. The "+N more" chip needs a bottom-sheet or modal interaction wired behind it to feel complete in production.
      </p>
    </section>

    <!-- ══════════════════════════════════════════════════════════════
         CLAUDE'S PICK
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-8">
      <div class="rounded-2xl border p-6 space-y-3"
           :style="{ background: L.accents.lavender.soft, borderColor: L.accents.lavender.bold }">
        <div class="flex items-center gap-2">
          <SparklesIcon class="w-5 h-5" :style="{ color: L.accents.lavender.bold }" />
          <h2 class="text-[15px] font-semibold" :style="{ color: L.accents.lavender.bold }">Claude's pick — Variant B</h2>
        </div>
        <p class="text-[14px] leading-relaxed" :style="{ color: L.inkPrimary }">
          Variant B (horizontal scroll) is the right default for Kinhold's mobile-first context. It keeps the chip row to a single predictable line at every viewport width — no wrapping surprises, no vertical budget cost — while flex-shrink-0 keeps each chip readable. Variant C is better reserved for contexts where the "+N more" bottom sheet is actually wired up.
        </p>
      </div>
    </section>

    <!-- ══════════════════════════════════════════════════════════════
         USAGE GUIDE
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-8">
      <div class="rounded-2xl border p-6 space-y-4"
           :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle }">
        <h2 class="text-[17px] font-semibold" :style="{ color: L.inkPrimary }">When to use which</h2>
        <ul class="space-y-4 text-[14px]" :style="{ color: L.inkSecondary }">
          <li>
            <strong :style="{ color: L.inkPrimary }">Variant A — Flex-wrap:</strong>
            Best for small, bounded category sets (4–6 chips max) where you know every chip will fit on two lines even at 320px. Good for vault entry type selection, task priority pickers, or any context where the category set is fixed and small. Avoid with 8+ chips — the wrapping creates unpredictable height.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Variant B — Horizontal scroll:</strong>
            The default for feature filter rows — recipe categories, meal plan cuisine filter, store "browse by category", workout category selector. Works with any number of chips without affecting page height. Ideal when users typically want to scan-and-tap one category at a time.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Variant C — Hybrid with "+N more":</strong>
            Use only when a bottom sheet or modal is wired to back it up. Best for search/filter pages where users may want to multi-select across a large category set (15+) — the overflow chip brings up a full-screen picker. On desktop, falls back gracefully to a full-wrap row. Do not ship the overflow chip as a static affordance with no interaction behind it.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Clear filters chip (ghost variant):</strong>
            Auto-appended at the end of the row via <code class="font-mono text-[12px] px-1 py-0.5 rounded" :style="{ background: L.surfaceSunken }">:clearable="true"</code> (default). Only renders when at least one filter is active. Uses the ghost style so it reads as secondary action.
          </li>
        </ul>
      </div>
    </section>

  </ComponentPage>
</template>
