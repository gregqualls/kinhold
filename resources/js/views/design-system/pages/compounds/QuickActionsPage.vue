<script setup>
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import KinQuickActions from '@/components/design-system/KinQuickActions.vue'
import {
  CalendarDaysIcon, CheckCircleIcon, HandThumbUpIcon, SparklesIcon,
  PlusCircleIcon, ListBulletIcon, ArrowPathIcon, UserPlusIcon,
  ArrowUpTrayIcon, Squares2X2Icon,
} from '@heroicons/vue/24/solid'

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

// ── Action data ───────────────────────────────────────────────────────────────
const DASHBOARD_ACTIONS = [
  { key: 'event', label: 'Add event',    icon: CalendarDaysIcon,  badge: null, disabled: false },
  { key: 'task',  label: 'Add task',     icon: CheckCircleIcon,   badge: 3,    disabled: false },
  { key: 'kudos', label: 'Log kudos',    icon: HandThumbUpIcon,   badge: null, disabled: false },
  { key: 'ask',   label: 'Ask assistant',icon: SparklesIcon,      badge: null, disabled: true  },
]

const TASKS_ACTIONS = [
  { key: 'quick',     label: 'Quick task', icon: PlusCircleIcon,  badge: null, disabled: false },
  { key: 'list',      label: 'New list',   icon: ListBulletIcon,  badge: null, disabled: false },
  { key: 'recurring', label: 'Recurring',  icon: ArrowPathIcon,   badge: null, disabled: false },
  { key: 'assign',    label: 'Assign',     icon: UserPlusIcon,    badge: null, disabled: false },
  { key: 'import',    label: 'Import',     icon: ArrowUpTrayIcon, badge: null, disabled: false },
  { key: 'templates', label: 'Templates',  icon: Squares2X2Icon,  badge: null, disabled: false },
]
</script>

<template>
  <ComponentPage
    title="4.5 QuickActions"
    description="A grid of 4 (or 6) square icon + label tiles for the top of module landing pages — the fastest tap targets for the most common actions. Used on Dashboard, Tasks, Meal Plan, Points, and Vault landing views."
    status="chosen"
  >
    <section class="mb-16">
      <VariantFrame label="QuickActions" caption="Flat surface-raised squares · outline border · icon inkPrimary · hover lift">
        <div class="w-full space-y-10">
          <!-- LIGHT PANEL -->
          <div
            class="rounded-2xl border p-6 space-y-8"
            :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }"
          >
            <p
              class="text-xs font-semibold uppercase tracking-widest"
              :style="{ color: L.inkTertiary }"
            >
              Light mode
            </p>

            <div>
              <p class="text-[11px] font-medium mb-3" :style="{ color: L.inkTertiary }">4-up · Dashboard actions</p>
              <KinQuickActions :actions="DASHBOARD_ACTIONS" :columns="4" :mobile-columns="2" />
            </div>

            <div>
              <p class="text-[11px] font-medium mb-3" :style="{ color: L.inkTertiary }">6-up · Tasks actions (denser context)</p>
              <KinQuickActions :actions="TASKS_ACTIONS" :columns="6" :mobile-columns="2" />
            </div>

            <div>
              <p class="text-[11px] font-medium mb-3" :style="{ color: L.inkTertiary }">Mobile 2-up · max-w-[320px] · 4 tiles in 2×2</p>
              <div class="max-w-[320px]">
                <KinQuickActions :actions="DASHBOARD_ACTIONS" :columns="2" :mobile-columns="2" />
              </div>
            </div>

            <p class="text-[11px]" :style="{ color: L.inkTertiary }">
              Hover: border tightens to border-strong + 2px vertical lift + shadow deepens. Disabled at 40% opacity + cursor-not-allowed. Badge: accent-peach pill in top-right corner.
            </p>
          </div>

          <!-- DARK PANEL -->
          <div
            class="dark rounded-2xl border p-6 space-y-8"
            :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }"
          >
            <p
              class="text-xs font-semibold uppercase tracking-widest"
              :style="{ color: D.inkTertiary }"
            >
              Dark mode
            </p>

            <div>
              <p class="text-[11px] font-medium mb-3" :style="{ color: D.inkTertiary }">4-up · Dashboard actions</p>
              <KinQuickActions :actions="DASHBOARD_ACTIONS" :columns="4" :mobile-columns="2" />
            </div>

            <div>
              <p class="text-[11px] font-medium mb-3" :style="{ color: D.inkTertiary }">6-up · Tasks actions (denser context)</p>
              <KinQuickActions :actions="TASKS_ACTIONS" :columns="6" :mobile-columns="2" />
            </div>

            <div>
              <p class="text-[11px] font-medium mb-3" :style="{ color: D.inkTertiary }">Mobile 2-up · max-w-[320px] · 4 tiles in 2×2</p>
              <div class="max-w-[320px]">
                <KinQuickActions :actions="DASHBOARD_ACTIONS" :columns="2" :mobile-columns="2" />
              </div>
            </div>

            <p class="text-[11px]" :style="{ color: D.inkTertiary }">
              Dark hover brightens to surface-overlay and uses shadow-elevated (shadows read poorly on warm-charcoal without a brightness boost — same treatment as FlatCard).
            </p>
          </div>
        </div>
      </VariantFrame>
    </section>


    <!-- ══════════════════════════════════════════════════════════════
         USAGE GUIDE
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-8">
      <div
        class="rounded-2xl border p-6 space-y-4"
        :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle }"
      >
        <h2 class="text-[17px] font-semibold" :style="{ color: L.inkPrimary }">When to use QuickActions</h2>
        <ul class="space-y-3 text-[14px]" :style="{ color: L.inkSecondary }">
          <li>
            <strong :style="{ color: L.inkPrimary }">Top of module landing pages:</strong>
            Dashboard, Tasks, Meal Plan, Points, Vault. The first thing a user sees is their four most common actions — one tap away from every major create/do path.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">4 actions is the default; 6 is the stretch.</strong>
            Four fits cleanly at every breakpoint. Six works on desktop for contexts with denser utility (Tasks module) but may feel cramped on narrow viewports — in that case, use <code class="font-mono text-[12px] px-1 py-0.5 rounded" :style="{ background: L.surfaceSunken }">mobileColumns="2"</code> or <code class="font-mono text-[12px] px-1 py-0.5 rounded" :style="{ background: L.surfaceSunken }">"3"</code>.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Badges sparingly:</strong>
            Use the peach badge for unread counts or approval queues. Never static numbers — a badge that never changes feels stale.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Disabled tiles:</strong>
            Use for gated features (e.g. "Ask assistant" before API key setup). Opacity + cursor-not-allowed communicates the affordance cleanly without hiding the feature entirely.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Icons are solid at 24px in ink-primary:</strong>
            This keeps the tiles utilitarian rather than decorative. Accent colors belong on GradientCards and chips, not on primary action icons.
          </li>
        </ul>
      </div>
    </section>
  </ComponentPage>
</template>
