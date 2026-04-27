<script setup>
import { ref } from 'vue'
import ComponentPage from '../../shared/ComponentPage.vue'

// ── Spacing baseline data ─────────────────────────────────────────────────────
// Tailwind default scale — 1 unit = 4px. No custom tokens needed.
const SPACING_STEPS = [
  { tw: 'w-1 h-1',   px: '4px',   label: 'p-1 = 4px' },
  { tw: 'w-2 h-2',   px: '8px',   label: 'p-2 = 8px' },
  { tw: 'w-3 h-3',   px: '12px',  label: 'p-3 = 12px' },
  { tw: 'w-4 h-4',   px: '16px',  label: 'p-4 = 16px' },
  { tw: 'w-6 h-6',   px: '24px',  label: 'p-6 = 24px' },
  { tw: 'w-8 h-8',   px: '32px',  label: 'p-8 = 32px' },
  { tw: 'w-12 h-12', px: '48px',  label: 'p-12 = 48px' },
  { tw: 'w-16 h-16', px: '64px',  label: 'p-16 = 64px' },
]

// ── Radii data ────────────────────────────────────────────────────────────────
const RADII = [
  {
    tw:       'rounded-pill',
    cssVar:   '--radius-pill',
    value:    '9999px',
    usage:    'buttons, chips, nav, status',
    lightBg:  '#EAE6F8',
    darkBg:   '#302A48',
  },
  {
    tw:       'rounded-card',
    cssVar:   '--radius-card',
    value:    '20px',
    usage:    'cards, raised surfaces',
    lightBg:  '#D5F2E8',
    darkBg:   '#18342A',
  },
  {
    tw:       'rounded-sheet',
    cssVar:   '--radius-sheet',
    value:    '28px',
    usage:    'modals, drawers',
    lightBg:  '#FCE9E0',
    darkBg:   '#3E241A',
  },
  {
    tw:       'rounded-field',
    cssVar:   '--radius-field',
    value:    '12px',
    usage:    'inputs',
    lightBg:  '#FCF3D2',
    darkBg:   '#342C0A',
  },
  {
    tw:       'rounded-tile',
    cssVar:   '--radius-tile',
    value:    '16px',
    usage:    'quick-action tiles',
    lightBg:  '#FAF8F5',
    darkBg:   '#1C1B19',
  },
]

// ── Shadows data ──────────────────────────────────────────────────────────────
const SHADOWS = [
  {
    tw:     'shadow-resting',
    cssVar: '--shadow-resting',
    label:  'Resting',
    usage:  'Default card elevation — barely lifted from the page.',
    glass:  false,
  },
  {
    tw:     'shadow-hover',
    cssVar: '--shadow-hover',
    label:  'Hover',
    usage:  'Cards on hover / focus — a clear step up from resting.',
    glass:  false,
  },
  {
    tw:     'shadow-elevated',
    cssVar: '--shadow-elevated',
    label:  'Elevated',
    usage:  'Floating action surfaces, popovers, dropdowns.',
    glass:  false,
  },
  {
    tw:     'shadow-modal',
    cssVar: '--shadow-modal',
    label:  'Modal',
    usage:  'Dialogs, sheets, drawers — maximum elevation.',
    glass:  false,
  },
  {
    tw:     'shadow-glass',
    cssVar: '--shadow-glass',
    label:  'Glass',
    usage:  'Nav pill, hero card, modal backdrop — inset highlight + outer depth.',
    glass:  true,
  },
]

// ── Motion durations data ─────────────────────────────────────────────────────
const DURATIONS = [
  { tw: 'duration-instant',    cssVar: '--duration-instant',    value: '100ms',  label: 'Instant',    desc: 'Micro-feedback: toggle, press highlight' },
  { tw: 'duration-quick',      cssVar: '--duration-quick',      value: '200ms',  label: 'Quick',      desc: 'Hover lift, nav pill switch, chip select' },
  { tw: 'duration-sheet',      cssVar: '--duration-sheet',      value: '500ms',  label: 'Sheet',      desc: 'Bottom-sheet / modal enter + exit' },
  { tw: 'duration-deliberate', cssVar: '--duration-deliberate', value: '700ms',  label: 'Deliberate', desc: 'Delete confirm, point spend, vault reveal' },
]

// ── Easings data ──────────────────────────────────────────────────────────────
const EASINGS = [
  { tw: 'ease-out-soft',    cssVar: '--ease-out-soft',    label: 'Out soft',    bezier: 'cubic-bezier(0.16, 1, 0.3, 1)',     desc: 'Fast start, decelerates into rest. Preferred for enters.',  animKey: 'outsoft' },
  { tw: 'ease-in-out-soft', cssVar: '--ease-in-out-soft', label: 'In-out soft', bezier: 'cubic-bezier(0.65, 0, 0.35, 1)',    desc: 'Symmetric, smooth handoff. Preferred for slides.',           animKey: 'inoutsoft' },
  { tw: 'ease-spring',      cssVar: '--ease-spring',      label: 'Spring',      bezier: 'cubic-bezier(0.34, 1.56, 0.64, 1)', desc: 'Slight overshoot for lively, playful moments.',              animKey: 'spring' },
]

// ── Motion demo state ─────────────────────────────────────────────────────────
// Each duration button toggles an independent translate demo block.
const demoActive = ref({ instant: false, quick: false, deliberate: false, sheet: false })

function toggleDemo(key) {
  demoActive.value[key] = !demoActive.value[key]
}

// Easing demo uses the same toggle pattern as durations: click toggles transform,
// and the transition property (with each easing's bezier) carries it from one end
// to the other. Click again to reverse. This is more reliable than a one-shot
// keyframe animation and lets the user see the curve in both directions.
const easingActive = ref({ outsoft: false, inoutsoft: false, spring: false })

function triggerEasing(key) {
  easingActive.value[key] = !easingActive.value[key]
}

// Map duration key → literal transition value.
// IMPORTANT: we use literal values (not CSS vars) so the demo renders even when
// the user has prefers-reduced-motion:reduce set at the OS level. The token
// system honors reduced-motion in production; this reference page needs to
// show what the tokens feel like when motion IS allowed. Same reason we use
// literal cubic-bezier strings for the easing demo below.
const EASE_OUT_SOFT_LITERAL = 'cubic-bezier(0.16, 1, 0.3, 1)'
const durationTransitionStyle = {
  instant:    `transform 100ms ${EASE_OUT_SOFT_LITERAL}`,
  quick:      `transform 200ms ${EASE_OUT_SOFT_LITERAL}`,
  sheet:      `transform 500ms ${EASE_OUT_SOFT_LITERAL}`,
  deliberate: `transform 700ms ${EASE_OUT_SOFT_LITERAL}`,
}
</script>

<template>
  <ComponentPage
    title="0.3 Spacing · Radii · Shadow · Motion"
    description="Foundation primitives: spacing uses Tailwind defaults (no custom tokens). Radii, shadows, and motion are fully tokenized in tokens.css and mapped to Tailwind utilities. All motion respects prefers-reduced-motion automatically."
    status="chosen"
  >

    <!-- ══════════════════════════════════════════════
         SECTION 1 — SPACING BASELINE
         Visual 4px-grid demo, no custom tokens
         ══════════════════════════════════════════════ -->
    <section class="mb-14">
      <h2 class="text-xs font-semibold uppercase tracking-widest text-ink-secondary mb-2">
        1 — Spacing baseline
      </h2>
      <p class="text-body-sm text-ink-tertiary mb-6">
        Tailwind defaults. 1 unit = 4px. No custom tokens — the 4px baseline is already universal.
      </p>

      <!-- Light panel -->
      <div class="rounded-2xl border border-border-subtle p-6 mb-4" style="background:#FAF8F5">
        <p class="text-xs font-medium text-ink-tertiary mb-5">Light mode</p>
        <div class="flex flex-wrap items-end gap-6">
          <div
            v-for="s in SPACING_STEPS"
            :key="s.px"
            class="flex flex-col items-center gap-2"
          >
            <!-- Square scaled by Tailwind size class -->
            <div
              :class="[s.tw, 'rounded bg-accent-lavender-bold opacity-80 flex-shrink-0']"
            ></div>
            <code class="text-[10px] font-mono text-ink-tertiary text-center leading-tight whitespace-nowrap">
              {{ s.px }}
            </code>
          </div>
        </div>
        <p class="text-caption text-ink-tertiary mt-6">
          These squares are sized with standard Tailwind spacing utilities (<code class="font-mono">w-1</code> through <code class="font-mono">w-16</code>). Padding, margins, gaps — all draw from this same 4px grid.
        </p>
      </div>

      <!-- Dark panel -->
      <div class="rounded-2xl border p-6" style="background:#141311; border-color:#2C2A27">
        <p class="text-xs font-medium mb-5" style="color:#6E6B67">Dark mode</p>
        <div class="flex flex-wrap items-end gap-6">
          <div
            v-for="s in SPACING_STEPS"
            :key="s.px"
            class="flex flex-col items-center gap-2"
          >
            <div
              :class="[s.tw, 'rounded flex-shrink-0']"
              style="background: rgb(182 168 230 / 0.7)"
            ></div>
            <code class="text-[10px] font-mono text-center leading-tight whitespace-nowrap" style="color:#6E6B67">
              {{ s.px }}
            </code>
          </div>
        </div>
      </div>
    </section>


    <!-- ══════════════════════════════════════════════
         SECTION 2 — RADII
         Five side-by-side rounded rectangles, ModeSplit
         ══════════════════════════════════════════════ -->
    <section class="mb-14">
      <h2 class="text-xs font-semibold uppercase tracking-widest text-ink-secondary mb-2">
        2 — Radii
      </h2>
      <p class="text-body-sm text-ink-tertiary mb-6">
        Pill is the primary shape language for interactive chrome. Cards, sheets, and fields use progressively softer rectangles.
      </p>

      <!-- Light panel -->
      <div class="rounded-2xl border border-border-subtle p-6 mb-4" style="background:#FAF8F5">
        <p class="text-xs font-medium text-ink-tertiary mb-5">Light mode</p>
        <div class="flex flex-wrap gap-4">
          <div
            v-for="r in RADII"
            :key="r.tw"
            class="flex flex-col items-center gap-3"
          >
            <!-- Radius swatch: 120×80 block with shadow-resting to show corner clearly -->
            <div
              :class="[r.tw]"
              class="w-28 h-20 shadow-resting flex items-center justify-center"
              :style="{ background: r.lightBg }"
            >
              <code class="text-[10px] font-mono text-center px-1" style="color:#6B6966">{{ r.value }}</code>
            </div>
            <div class="text-center">
              <code class="block text-[11px] font-mono text-ink-primary font-medium">{{ r.tw }}</code>
              <span class="block text-[10px] text-ink-tertiary max-w-[7rem] leading-tight">{{ r.usage }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Dark panel -->
      <div class="rounded-2xl border p-6" style="background:#141311; border-color:#2C2A27">
        <p class="text-xs font-medium mb-5" style="color:#6E6B67">Dark mode</p>
        <div class="flex flex-wrap gap-4">
          <div
            v-for="r in RADII"
            :key="r.tw"
            class="flex flex-col items-center gap-3"
          >
            <div
              :class="[r.tw]"
              class="w-28 h-20 shadow-resting flex items-center justify-center"
              :style="{ background: r.darkBg }"
            >
              <code class="text-[10px] font-mono text-center px-1" style="color:#A09C97">{{ r.value }}</code>
            </div>
            <div class="text-center">
              <code class="block text-[11px] font-mono font-medium" style="color:#F0EDE9">{{ r.tw }}</code>
              <span class="block text-[10px] max-w-[7rem] leading-tight" style="color:#6E6B67">{{ r.usage }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Token reference table -->
      <div class="mt-4 rounded-xl border border-border-subtle overflow-hidden">
        <table class="w-full text-xs">
          <thead>
            <tr class="bg-surface-sunken border-b border-border-subtle">
              <th class="text-left px-3 py-2 font-semibold text-ink-secondary">Utility</th>
              <th class="text-left px-3 py-2 font-semibold text-ink-secondary">CSS var</th>
              <th class="text-left px-3 py-2 font-semibold text-ink-secondary">Value</th>
              <th class="text-left px-3 py-2 font-semibold text-ink-secondary hidden sm:table-cell">Usage</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(r, i) in RADII"
              :key="r.tw"
              :class="i % 2 === 1 ? 'bg-surface-sunken' : 'bg-surface-raised'"
            >
              <td class="px-3 py-2 font-mono text-ink-primary">{{ r.tw }}</td>
              <td class="px-3 py-2 font-mono text-ink-tertiary text-[11px]">{{ r.cssVar }}</td>
              <td class="px-3 py-2 font-mono text-ink-secondary">{{ r.value }}</td>
              <td class="px-3 py-2 text-ink-tertiary hidden sm:table-cell">{{ r.usage }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>


    <!-- ══════════════════════════════════════════════
         SECTION 3 — SHADOWS
         Five cards, light + dark side by side
         ══════════════════════════════════════════════ -->
    <section class="mb-14">
      <h2 class="text-xs font-semibold uppercase tracking-widest text-ink-secondary mb-2">
        3 — Shadows
      </h2>
      <p class="text-body-sm text-ink-tertiary mb-6">
        Light-mode shadows use warm brown-tinted rgba — they read naturally on the warm-ivory surface.
        Dark-mode shadows are significantly deeper (not inverted) to create visible elevation against near-black.
      </p>

      <!-- Light panel -->
      <div class="rounded-2xl border border-border-subtle p-6 mb-4" style="background:#FAF8F5">
        <p class="text-xs font-medium text-ink-tertiary mb-6">Light mode</p>
        <div class="flex flex-wrap gap-6">
          <div
            v-for="s in SHADOWS"
            :key="s.tw"
            class="flex flex-col items-center gap-3"
          >
            <!-- Glass swatch: needs a colorful background so backdrop-blur has something to filter -->
            <template v-if="s.glass">
              <div
                class="relative w-32 h-24 rounded-card overflow-hidden flex items-center justify-center"
                style="background: linear-gradient(135deg, #d8c8f0 0%, #f7c8b0 33%, #b8e8d0 66%, #f5e8a0 100%)"
              >
                <div
                  :class="[s.tw]"
                  class="absolute inset-2 rounded-card flex items-center justify-center backdrop-blur-lg border border-white/50"
                  style="background: rgba(255,255,255,0.42)"
                >
                  <code class="text-[10px] font-mono text-center px-1" style="color:#5a5560">{{ s.label }}</code>
                </div>
              </div>
            </template>
            <template v-else>
              <div
                :class="[s.tw, 'rounded-card']"
                class="w-32 h-24 flex items-center justify-center"
                style="background:#FFFFFF"
              >
                <code class="text-[10px] font-mono text-center px-1" style="color:#9C9895">{{ s.label }}</code>
              </div>
            </template>
            <div class="text-center max-w-[8rem]">
              <code class="block text-[11px] font-mono text-ink-primary font-medium">{{ s.tw }}</code>
              <span class="block text-[10px] text-ink-tertiary leading-tight">{{ s.usage }}</span>
            </div>
            <!-- Caption for the glass swatch only -->
            <p v-if="s.glass" class="text-[10px] text-ink-tertiary leading-snug max-w-[8rem] text-center">
              Glass requires a colorful or photographic background to read — Tier 0.5 will codify the full <code class="font-mono">.surface-glass</code> utility (blur + translucent bg + shadow + inner hairline).
            </p>
          </div>
        </div>
      </div>

      <!-- Dark panel -->
      <div class="rounded-2xl border p-6" style="background:#141311; border-color:#2C2A27">
        <p class="text-xs font-medium mb-6" style="color:#6E6B67">Dark mode</p>
        <div class="flex flex-wrap gap-6">
          <div
            v-for="s in SHADOWS"
            :key="s.tw"
            class="flex flex-col items-center gap-3"
          >
            <!-- Glass swatch dark: colorful gradient behind the translucent glass card -->
            <template v-if="s.glass">
              <div
                class="relative w-32 h-24 rounded-card overflow-hidden flex items-center justify-center"
                style="background: linear-gradient(135deg, #4a3870 0%, #6b3030 33%, #1a4a38 66%, #4a3c10 100%)"
              >
                <div
                  :class="[s.tw]"
                  class="absolute inset-2 rounded-card flex items-center justify-center backdrop-blur-lg border"
                  style="background: rgba(28,27,25,0.45); border-color: rgba(255,255,255,0.12)"
                >
                  <code class="text-[10px] font-mono text-center px-1" style="color:#b0adb8">{{ s.label }}</code>
                </div>
              </div>
            </template>
            <template v-else>
              <div
                :class="[s.tw, 'rounded-card']"
                class="w-32 h-24 flex items-center justify-center"
                style="background:#1C1B19"
              >
                <code class="text-[10px] font-mono text-center px-1" style="color:#6E6B67">{{ s.label }}</code>
              </div>
            </template>
            <div class="text-center max-w-[8rem]">
              <code class="block text-[11px] font-mono font-medium" style="color:#F0EDE9">{{ s.tw }}</code>
              <span class="block text-[10px] leading-tight" style="color:#6E6B67">{{ s.usage }}</span>
            </div>
            <!-- Caption for the glass swatch only -->
            <p v-if="s.glass" class="text-[10px] leading-snug max-w-[8rem] text-center" style="color:#6E6B67">
              Glass requires a colorful or photographic background to read — Tier 0.5 will codify the full <code class="font-mono">.surface-glass</code> utility (blur + translucent bg + shadow + inner hairline).
            </p>
          </div>
        </div>
      </div>

      <!-- Token table -->
      <div class="mt-4 rounded-xl border border-border-subtle overflow-hidden">
        <table class="w-full text-xs">
          <thead>
            <tr class="bg-surface-sunken border-b border-border-subtle">
              <th class="text-left px-3 py-2 font-semibold text-ink-secondary">Utility</th>
              <th class="text-left px-3 py-2 font-semibold text-ink-secondary">CSS var</th>
              <th class="text-left px-3 py-2 font-semibold text-ink-secondary hidden sm:table-cell">Usage</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(s, i) in SHADOWS"
              :key="s.tw"
              :class="i % 2 === 1 ? 'bg-surface-sunken' : 'bg-surface-raised'"
            >
              <td class="px-3 py-2 font-mono text-ink-primary">{{ s.tw }}</td>
              <td class="px-3 py-2 font-mono text-ink-tertiary text-[11px]">{{ s.cssVar }}</td>
              <td class="px-3 py-2 text-ink-tertiary hidden sm:table-cell">{{ s.usage }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>


    <!-- ══════════════════════════════════════════════
         SECTION 4 — MOTION
         Interactive duration demo + easing demos + table
         ══════════════════════════════════════════════ -->
    <section class="mb-6">
      <h2 class="text-xs font-semibold uppercase tracking-widest text-ink-secondary mb-2">
        4 — Motion
      </h2>
      <p class="text-body-sm text-ink-tertiary mb-6">
        Click each button to see the transition at that speed. The demos below use literal duration
        and easing values so the tokens can always be evaluated — in production, components consume
        <code class="font-mono text-caption">var(--duration-*)</code> and honor
        <code class="font-mono text-caption">prefers-reduced-motion: reduce</code>.
      </p>

      <!-- 4a — Duration demo buttons -->
      <div class="rounded-2xl border border-border-subtle p-6 bg-surface-raised mb-6">
        <p class="text-xs font-medium text-ink-secondary mb-5">Duration tokens — click to toggle</p>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          <div
            v-for="d in DURATIONS"
            :key="d.tw"
            class="flex flex-col gap-3"
          >
            <!-- Track — fixed width so the pill travels a visible ~180px distance -->
            <div class="relative h-12 w-[220px] bg-surface-sunken rounded-tile overflow-hidden border border-border-subtle">
              <!-- Sliding pill: active → translate 172px (220px track − 32px pill − 16px padding)
                   Transition is applied as inline CSS with a literal ms value so this demo stays
                   demonstrative even when prefers-reduced-motion:reduce is active at the OS level. -->
              <div
                class="absolute top-1/2 w-8 h-8 rounded-pill bg-accent-lavender-bold"
                :style="{
                  transition: durationTransitionStyle[d.tw.replace('duration-', '')],
                  transform: `translateX(${demoActive[d.tw.replace('duration-', '')] ? '172px' : '8px'}) translateY(-50%)`,
                }"
              ></div>
            </div>

            <!-- Toggle button -->
            <button
              type="button"
              class="rounded-pill px-3 py-1.5 text-xs font-semibold bg-surface-sunken text-ink-secondary hover:bg-surface-raised border border-border-subtle transition-colors duration-quick"
              @click="toggleDemo(d.tw.replace('duration-', ''))"
            >
              {{ d.label }} · {{ d.value }}
            </button>

            <p class="text-caption text-ink-tertiary leading-snug">{{ d.desc }}</p>
          </div>
        </div>
      </div>

      <!-- 4b — Easing demo -->
      <div class="rounded-2xl border border-border-subtle p-6 bg-surface-raised mb-6">
        <p class="text-xs font-medium text-ink-secondary mb-5">
          Easing tokens — click to toggle a 500ms slide. Click again to reverse.
        </p>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
          <div
            v-for="e in EASINGS"
            :key="e.tw"
            class="flex flex-col gap-3"
          >
            <!-- Track — same fixed width as duration demo so visuals are consistent.
                 Easing demo uses the same toggle-transition pattern as the duration demo,
                 swapping the bezier per pill. Literal 500ms so it renders even under
                 prefers-reduced-motion. -->
            <div class="relative h-12 w-[220px] bg-surface-sunken rounded-tile overflow-hidden border border-border-subtle">
              <div
                class="absolute top-1/2 w-8 h-8 rounded-pill bg-accent-peach-bold"
                :style="{
                  transition: `transform 500ms ${e.bezier}`,
                  transform: `translateX(${easingActive[e.animKey] ? '172px' : '8px'}) translateY(-50%)`,
                }"
              ></div>
            </div>

            <button
              type="button"
              class="rounded-pill px-3 py-1.5 text-xs font-semibold bg-surface-sunken text-ink-secondary hover:bg-surface-raised border border-border-subtle transition-colors duration-quick"
              @click="triggerEasing(e.animKey)"
            >
              {{ e.label }}
            </button>

            <div>
              <code class="block text-[11px] font-mono text-ink-primary mb-0.5">{{ e.tw }}</code>
              <p class="text-caption text-ink-tertiary leading-snug">{{ e.desc }}</p>
            </div>
          </div>
        </div>

        <!-- Reduced motion note -->
        <div class="mt-5 rounded-xl border border-border-subtle bg-surface-sunken px-4 py-3 text-body-sm text-ink-secondary">
          <strong class="text-ink-primary">Tenet #9 — Purposeful motion.</strong>
          In production, all <code class="font-mono text-caption">--duration-*</code> tokens resolve
          to <code class="font-mono text-caption">0ms</code> when
          <code class="font-mono text-caption">prefers-reduced-motion: reduce</code> is active, so
          components snap instantly for keyboard and assistive-technology users. Easing functions
          are left intact so state-change semantics continue to work. The demos above intentionally
          use literal durations so the motion design can be evaluated here regardless of OS setting.
        </div>
      </div>

      <!-- 4c — Full token reference table -->
      <div class="rounded-xl border border-border-subtle overflow-hidden">
        <table class="w-full text-xs">
          <thead>
            <tr class="bg-surface-sunken border-b border-border-subtle">
              <th class="text-left px-3 py-2 font-semibold text-ink-secondary">Token</th>
              <th class="text-left px-3 py-2 font-semibold text-ink-secondary">CSS var</th>
              <th class="text-left px-3 py-2 font-semibold text-ink-secondary">Value</th>
              <th class="text-left px-3 py-2 font-semibold text-ink-secondary hidden sm:table-cell">Usage</th>
            </tr>
          </thead>
          <tbody>
            <!-- Duration rows -->
            <tr class="bg-surface-sunken border-b border-border-subtle">
              <td colspan="4" class="px-3 py-1.5 text-[10px] font-semibold uppercase tracking-wider text-ink-tertiary">
                Durations
              </td>
            </tr>
            <tr
              v-for="(d, i) in DURATIONS"
              :key="d.cssVar"
              :class="i % 2 === 1 ? 'bg-surface-sunken' : 'bg-surface-raised'"
            >
              <td class="px-3 py-2 font-mono text-ink-primary">{{ d.tw }}</td>
              <td class="px-3 py-2 font-mono text-ink-tertiary text-[11px]">{{ d.cssVar }}</td>
              <td class="px-3 py-2 font-mono text-ink-secondary">{{ d.value }}</td>
              <td class="px-3 py-2 text-ink-tertiary hidden sm:table-cell">{{ d.desc }}</td>
            </tr>

            <!-- Easing rows -->
            <tr class="bg-surface-sunken border-b border-border-subtle border-t border-border-subtle">
              <td colspan="4" class="px-3 py-1.5 text-[10px] font-semibold uppercase tracking-wider text-ink-tertiary">
                Easings
              </td>
            </tr>
            <tr
              v-for="(e, i) in EASINGS"
              :key="e.cssVar"
              :class="i % 2 === 1 ? 'bg-surface-sunken' : 'bg-surface-raised'"
            >
              <td class="px-3 py-2 font-mono text-ink-primary">{{ e.tw }}</td>
              <td class="px-3 py-2 font-mono text-ink-tertiary text-[11px]">{{ e.cssVar }}</td>
              <td class="px-3 py-2 font-mono text-ink-secondary text-[11px]">{{ e.bezier }}</td>
              <td class="px-3 py-2 text-ink-tertiary hidden sm:table-cell">{{ e.desc }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>

  </ComponentPage>
</template>

