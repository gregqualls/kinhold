<script setup>
import { ref } from 'vue'
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import KinTabPillGroup from '@/components/design-system/KinTabPillGroup.vue'
import { SparklesIcon } from '@heroicons/vue/24/outline'

// ── Panel chrome tokens (Light/Dark hand-rolled wrappers) ────────────────────
const L = {
  surfaceApp:    '#FAF8F5', surfaceRaised: '#FFFFFF', surfaceSunken: '#F5F2EE',
  inkPrimary:    '#1C1C1E', inkSecondary:  '#6B6966', inkTertiary:   '#9C9895', inkInverse: '#FAF8F5',
  borderSubtle:  '#E8E4DF',
  accents: {
    lavender: { soft: '#EAE6F8', bold: '#6856B2' },
  },
}
const D = {
  surfaceApp:    '#141311', surfaceRaised: '#1C1B19',
  inkPrimary:    '#F0EDE9', inkSecondary:  '#A09C97', inkTertiary:   '#6E6B67', inkInverse: '#1C1C1E',
  borderSubtle:  '#2C2A27',
  accents: {
    lavender: { soft: '#302A48', bold: '#B6A8E6' },
  },
}

// ── Tab data ──────────────────────────────────────────────────────────────────
const TABS_CORE = [
  { key: 'overview',     label: 'Overview',     count: null },
  { key: 'ingredients',  label: 'Ingredients',  count: 12   },
  { key: 'notes',        label: 'Notes',        count: 3    },
  { key: 'history',      label: 'History',      count: null },
]

const TABS_OVERFLOW = [
  { key: 'overview',     label: 'Overview',     count: null },
  { key: 'ingredients',  label: 'Ingredients',  count: 12   },
  { key: 'steps',        label: 'Steps',        count: 8    },
  { key: 'notes',        label: 'Notes',        count: 3    },
  { key: 'nutrition',    label: 'Nutrition',    count: null },
  { key: 'history',      label: 'History',      count: null },
  { key: 'related',      label: 'Related',      count: 5    },
]

// Active-tab state per demo
const activeA_L = ref('overview')
const activeA_D = ref('overview')
const activeB_L = ref('overview')
const activeB_D = ref('overview')
const activeC_L = ref('overview')
const activeC_D = ref('overview')
const activeMob_A = ref('overview')
const activeMob_B = ref('overview')
const activeMob_C = ref('overview')
</script>

<template>
  <ComponentPage
    title="4.1 TabPillGroup"
    description="Pill-row tabs for in-page sections. Three variants explore fill weight, underline minimalism, and tinted accent. Used in recipe detail, restaurant detail, vault entries, family profile, and food module top nav."
    status="chosen"
  >
    <!-- ══════════════════════════════════════════════════════════════
         VARIANT A — Filled active pill
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="A" caption="Filled active pill · outlined inactive · count badge support">
        <div class="w-full space-y-10">
          <!-- LIGHT PANEL -->
          <div
            class="rounded-2xl border p-6 space-y-6"
            :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }"
          >
            <p
              class="text-xs font-semibold uppercase tracking-widest"
              :style="{ color: L.inkTertiary }"
            >
              Light mode
            </p>

            <KinTabPillGroup
              v-model:active-key="activeA_L"
              variant="filled"
              :tabs="TABS_CORE"
            />

            <!-- Content placeholder -->
            <div class="rounded-xl p-4" :style="{ background: L.surfaceRaised, border: `1px solid ${L.borderSubtle}` }">
              <p class="text-[13px]" :style="{ color: L.inkSecondary }">
                Showing: <strong :style="{ color: L.inkPrimary }">{{ TABS_CORE.find(t => t.key === activeA_L)?.label }}</strong>
              </p>
            </div>

            <p class="text-[11px]" :style="{ color: L.inkTertiary }">
              Hover: border shifts to border-strong + ink-primary text. Active: solid ink fill, inverse text.
              Counts: semi-transparent white badge on active, surface-sunken on inactive.
            </p>
          </div>

          <!-- DARK PANEL -->
          <div
            class="dark rounded-2xl border p-6 space-y-6"
            :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }"
          >
            <p
              class="text-xs font-semibold uppercase tracking-widest"
              :style="{ color: D.inkTertiary }"
            >
              Dark mode
            </p>

            <KinTabPillGroup
              v-model:active-key="activeA_D"
              variant="filled"
              :tabs="TABS_CORE"
            />

            <div class="rounded-xl p-4" :style="{ background: D.surfaceRaised, border: `1px solid ${D.borderSubtle}` }">
              <p class="text-[13px]" :style="{ color: D.inkSecondary }">
                Showing: <strong :style="{ color: D.inkPrimary }">{{ TABS_CORE.find(t => t.key === activeA_D)?.label }}</strong>
              </p>
            </div>

            <p class="text-[11px]" :style="{ color: D.inkTertiary }">
              Dark active: #F0EDE9 fill with charcoal text — high contrast, no ambiguity.
              Badge on inactive uses surface-overlay (#242220) for subtle separation.
            </p>
          </div>
        </div>
      </VariantFrame>
      <p class="mt-3 text-sm px-1" :style="{ color: L.inkSecondary }">
        Variant A has the strongest visual weight — the ink-filled pill is unmissable and mirrors the
        existing recipe-detail pattern in the app. Best when tabs drive distinct full-section switches.
      </p>
    </section>


    <!-- ══════════════════════════════════════════════════════════════
         VARIANT B — Underline active
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="B" caption="Underline active · plain inactive · zero fill chrome">
        <div class="w-full space-y-10">
          <!-- LIGHT PANEL -->
          <div
            class="rounded-2xl border p-6 space-y-6"
            :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }"
          >
            <p
              class="text-xs font-semibold uppercase tracking-widest"
              :style="{ color: L.inkTertiary }"
            >
              Light mode
            </p>

            <KinTabPillGroup
              v-model:active-key="activeB_L"
              variant="underline"
              :tabs="TABS_CORE"
            />

            <div class="rounded-xl p-4" :style="{ background: L.surfaceRaised, border: `1px solid ${L.borderSubtle}` }">
              <p class="text-[13px]" :style="{ color: L.inkSecondary }">
                Showing: <strong :style="{ color: L.inkPrimary }">{{ TABS_CORE.find(t => t.key === activeB_L)?.label }}</strong>
              </p>
            </div>

            <p class="text-[11px]" :style="{ color: L.inkTertiary }">
              Hover: ink-secondary text (from tertiary). Active: 2px bottom bar — rounded top, flush with border.
              Count badge uses lavender-soft tint on active for a light accent.
            </p>
          </div>

          <!-- DARK PANEL -->
          <div
            class="dark rounded-2xl border p-6 space-y-6"
            :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }"
          >
            <p
              class="text-xs font-semibold uppercase tracking-widest"
              :style="{ color: D.inkTertiary }"
            >
              Dark mode
            </p>

            <KinTabPillGroup
              v-model:active-key="activeB_D"
              variant="underline"
              :tabs="TABS_CORE"
            />

            <div class="rounded-xl p-4" :style="{ background: D.surfaceRaised, border: `1px solid ${D.borderSubtle}` }">
              <p class="text-[13px]" :style="{ color: D.inkSecondary }">
                Showing: <strong :style="{ color: D.inkPrimary }">{{ TABS_CORE.find(t => t.key === activeB_D)?.label }}</strong>
              </p>
            </div>

            <p class="text-[11px]" :style="{ color: D.inkTertiary }">
              Dark underline uses #F0EDE9 (ink-primary) bar — stands out cleanly without glowing.
              Count badge on active: dark-lavender-soft fill with lavender-bold text.
            </p>
          </div>
        </div>
      </VariantFrame>
      <p class="mt-3 text-sm px-1" :style="{ color: L.inkSecondary }">
        Variant B is the leanest option — no filled shapes, just a hairline underline. Ideal when the
        tab group sits inside a card or modal where competing chrome would feel heavy.
      </p>
    </section>


    <!-- ══════════════════════════════════════════════════════════════
         VARIANT C — Tinted active pill
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="C" caption="Tinted active pill · transparent inactive · Kinhold lavender accent">
        <div class="w-full space-y-10">
          <!-- LIGHT PANEL -->
          <div
            class="rounded-2xl border p-6 space-y-6"
            :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }"
          >
            <p
              class="text-xs font-semibold uppercase tracking-widest"
              :style="{ color: L.inkTertiary }"
            >
              Light mode
            </p>

            <KinTabPillGroup
              v-model:active-key="activeC_L"
              variant="tinted"
              :tabs="TABS_CORE"
            />

            <div class="rounded-xl p-4" :style="{ background: L.surfaceRaised, border: `1px solid ${L.borderSubtle}` }">
              <p class="text-[13px]" :style="{ color: L.inkSecondary }">
                Showing: <strong :style="{ color: L.accents.lavender.bold }">{{ TABS_CORE.find(t => t.key === activeC_L)?.label }}</strong>
              </p>
            </div>

            <p class="text-[11px]" :style="{ color: L.inkTertiary }">
              Hover: shifts from ink-tertiary to lavender-bold text (no fill). Active: lavender-soft fill,
              lavender-bold text. Count badge inverts to lavender-bold fill with white text on active.
            </p>
          </div>

          <!-- DARK PANEL -->
          <div
            class="dark rounded-2xl border p-6 space-y-6"
            :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }"
          >
            <p
              class="text-xs font-semibold uppercase tracking-widest"
              :style="{ color: D.inkTertiary }"
            >
              Dark mode
            </p>

            <KinTabPillGroup
              v-model:active-key="activeC_D"
              variant="tinted"
              :tabs="TABS_CORE"
            />

            <div class="rounded-xl p-4" :style="{ background: D.surfaceRaised, border: `1px solid ${D.borderSubtle}` }">
              <p class="text-[13px]" :style="{ color: D.inkSecondary }">
                Showing: <strong :style="{ color: D.accents.lavender.bold }">{{ TABS_CORE.find(t => t.key === activeC_D)?.label }}</strong>
              </p>
            </div>

            <p class="text-[11px]" :style="{ color: D.inkTertiary }">
              Dark active: #302A48 fill + #B6A8E6 text — the lavender-soft/bold pair from the token set.
              Count badge uses #B6A8E6 fill with charcoal text — intentionally strong pop.
            </p>
          </div>
        </div>
      </VariantFrame>
      <p class="mt-3 text-sm px-1" :style="{ color: L.inkSecondary }">
        Variant C uses Kinhold's brand lavender accent directly — the fill is soft enough to be calm,
        the text is bold enough to be unmistakable. Pairs well with lavender category chips elsewhere on the page.
      </p>
    </section>


    <!-- ══════════════════════════════════════════════════════════════
         MOBILE OVERFLOW DEMO (375px)
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="Mobile" caption="375px viewport · 7 tabs · overflow-x-auto — scrolls horizontally without wrapping">
        <div class="w-full space-y-8">
          <p class="text-[12px]" :style="{ color: L.inkTertiary }">
            Each variant on a 375px-equivalent container. Tabs are <code class="font-mono text-[11px] px-1 py-0.5 rounded" :style="{ background: L.surfaceSunken }">flex-shrink-0</code>
            so they never compress. The row uses <code class="font-mono text-[11px] px-1 py-0.5 rounded" :style="{ background: L.surfaceSunken }">overflow-x-auto; scrollbar-width: none</code>
            for clean invisible scroll on mobile browsers.
          </p>

          <!-- ── Variant A mobile ────────────────────────────────── -->
          <div class="space-y-2">
            <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Variant A — filled pill</p>
            <div
              class="max-w-[375px] rounded-2xl border p-4 space-y-4"
              :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }"
            >
              <div class="overflow-x-auto pb-1" style="scrollbar-width: none; -ms-overflow-style: none;">
                <KinTabPillGroup
                  v-model:active-key="activeMob_A"
                  variant="filled"
                  size="sm"
                  :tabs="TABS_OVERFLOW"
                />
              </div>
              <div class="rounded-xl px-3 py-2" :style="{ background: L.surfaceRaised, border: `1px solid ${L.borderSubtle}` }">
                <p class="text-[12px]" :style="{ color: L.inkSecondary }">
                  Active: <strong :style="{ color: L.inkPrimary }">{{ TABS_OVERFLOW.find(t => t.key === activeMob_A)?.label }}</strong>
                </p>
              </div>
            </div>
          </div>

          <!-- ── Variant B mobile ────────────────────────────────── -->
          <div class="space-y-2">
            <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Variant B — underline</p>
            <div
              class="max-w-[375px] rounded-2xl border p-4 space-y-4"
              :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }"
            >
              <div class="overflow-x-auto pb-0" style="scrollbar-width: none; -ms-overflow-style: none;">
                <KinTabPillGroup
                  v-model:active-key="activeMob_B"
                  variant="underline"
                  size="sm"
                  :tabs="TABS_OVERFLOW"
                />
              </div>
              <div class="rounded-xl px-3 py-2" :style="{ background: L.surfaceRaised, border: `1px solid ${L.borderSubtle}` }">
                <p class="text-[12px]" :style="{ color: L.inkSecondary }">
                  Active: <strong :style="{ color: L.inkPrimary }">{{ TABS_OVERFLOW.find(t => t.key === activeMob_B)?.label }}</strong>
                </p>
              </div>
            </div>
          </div>

          <!-- ── Variant C mobile ────────────────────────────────── -->
          <div class="space-y-2">
            <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Variant C — tinted accent</p>
            <div
              class="max-w-[375px] rounded-2xl border p-4 space-y-4"
              :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }"
            >
              <div class="overflow-x-auto pb-1" style="scrollbar-width: none; -ms-overflow-style: none;">
                <KinTabPillGroup
                  v-model:active-key="activeMob_C"
                  variant="tinted"
                  size="sm"
                  :tabs="TABS_OVERFLOW"
                />
              </div>
              <div class="rounded-xl px-3 py-2" :style="{ background: L.surfaceRaised, border: `1px solid ${L.borderSubtle}` }">
                <p class="text-[12px]" :style="{ color: L.inkSecondary }">
                  Active: <strong :style="{ color: L.accents.lavender.bold }">{{ TABS_OVERFLOW.find(t => t.key === activeMob_C)?.label }}</strong>
                </p>
              </div>
            </div>
          </div>
        </div>
      </VariantFrame>
    </section>


    <!-- ══════════════════════════════════════════════════════════════
         CLAUDE'S PICK CALLOUT
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-8">
      <div
        class="rounded-2xl border p-6 space-y-3"
        :style="{ background: L.accents.lavender.soft, borderColor: L.accents.lavender.bold }"
      >
        <div class="flex items-center gap-2">
          <SparklesIcon class="w-5 h-5" :style="{ color: L.accents.lavender.bold }" />
          <h2 class="text-[15px] font-semibold" :style="{ color: L.accents.lavender.bold }">Claude's pick — Variant C</h2>
        </div>
        <p class="text-[14px] leading-relaxed" :style="{ color: L.inkPrimary }">
          Variant C (tinted lavender) is the strongest fit for Kinhold because it speaks the brand's
          own accent language — the same lavender-soft/bold pair used across chips, badges, and the
          active sidebar item — giving the tab row visual continuity without heavy ink weight.
          On dark it reads beautifully with zero mechanical inversion: the deep-plum soft fill
          (#302A48) is calm while the #B6A8E6 label is unmistakably active.
          Pill shape, co-equal dark mode, and invisible overflow scroll on mobile all hold.
        </p>
      </div>
    </section>


    <!-- ══════════════════════════════════════════════════════════════
         USAGE GUIDE
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-8">
      <div
        class="rounded-2xl border p-6 space-y-4"
        :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle }"
      >
        <h2 class="text-[17px] font-semibold" :style="{ color: L.inkPrimary }">When to use which variant</h2>
        <ul class="space-y-3 text-[14px]" :style="{ color: L.inkSecondary }">
          <li>
            <strong :style="{ color: L.inkPrimary }">Variant A (filled ink pill)</strong> — Use when the page background is already
            richly chromatic (photo hero, gradient card) and you need maximum contrast for the active tab.
            Strong visual weight suits primary module navigation (recipe detail, food module top nav).
            Avoid stacking with other heavy ink elements on the same horizontal band.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Variant B (underline)</strong> — Use inside cards, modals, or sheets where
            available vertical space is limited and adding filled pill shapes would feel cluttered.
            Ideal for vault entry sections and family profile sub-tabs that sit inside a FlatCard
            container. The shared bottom border provides free structural separation.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Variant C (tinted lavender, recommended)</strong> — Default choice for most Kinhold
            pages. The soft fill stays below the visual threshold of a button but is clearly distinct
            from inactive tabs. Counts use the bold lavender fill, reinforcing at-a-glance orientation.
            Works on both app-bg and raised-surface backgrounds without adjustment.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Count badges</strong> — Always show counts that are actionable or informative
            (ingredient count, note count). Hide counts on structural tabs like "Overview" or "History"
            unless they contain a pending-action number worth surfacing.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Mobile overflow</strong> — Wrap the component in an <code class="font-mono text-[12px] px-1 py-0.5 rounded" :style="{ background: L.surfaceSunken }">overflow-x-auto</code>
            container. The component itself uses <code class="font-mono text-[12px] px-1 py-0.5 rounded" :style="{ background: L.surfaceSunken }">flex-shrink-0</code> on each tab.
            Never wrap — a wrapped pill row changes height dynamically and shifts layout on tab change.
          </li>
        </ul>
      </div>
    </section>
  </ComponentPage>
</template>

<style scoped>
/* Hide the horizontal scrollbar on mobile overflow containers. */
.overflow-x-auto::-webkit-scrollbar {
  display: none;
}
</style>
