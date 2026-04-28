<script setup>
import ComponentPage from '../../shared/ComponentPage.vue'

// ── Token reference data ──────────────────────────────────────────────────────
const GLASS_TOKENS = [
  {
    cssVar: '--glass-bg',
    lightValue: 'rgba(255, 255, 255, 0.55)',
    darkValue: 'rgba(28, 27, 25, 0.55)',
    usage: 'Standard surface background — .surface-glass',
  },
  {
    cssVar: '--glass-bg-strong',
    lightValue: 'rgba(255, 255, 255, 0.72)',
    darkValue: 'rgba(28, 27, 25, 0.75)',
    usage: 'High-legibility surface background — .surface-glass-strong',
  },
  {
    cssVar: '--glass-border',
    lightValue: 'rgba(255, 255, 255, 0.65)',
    darkValue: 'rgba(255, 255, 255, 0.08)',
    usage: 'Edge highlight border on both variants',
  },
  {
    cssVar: '--glass-highlight',
    lightValue: 'rgba(255, 255, 255, 0.45)',
    darkValue: 'rgba(255, 255, 255, 0.04)',
    usage: '::before inner top gradient — the "floating pane" quality',
  },
  {
    cssVar: '--glass-blur',
    lightValue: '16px',
    darkValue: '18px',
    usage: 'backdrop-filter blur radius (dark slightly stronger)',
  },
  {
    cssVar: '--glass-saturation',
    lightValue: '140%',
    darkValue: '130%',
    usage: 'backdrop-filter saturate — gives the liquid, vivid-behind-glass quality',
  },
]

const UTILITY_CLASSES = [
  {
    cls: '.surface-glass',
    usage: 'Default translucent pane. Use on ambient overlays, nav pills.',
    note: 'Slightly more see-through — background reads through.',
  },
  {
    cls: '.surface-glass-strong',
    usage: 'High-legibility pane. Use on hero cards, modals, nav where text must be readable.',
    note: 'More opaque — content sits cleanly above the blur.',
  },
]
</script>

<template>
  <ComponentPage
    title="0.5 Glass utilities"
    description="Glassmorphism — purposeful and constrained. Two utility classes (.surface-glass and .surface-glass-strong) backed by six CSS custom properties. Both classes inherit border-radius, so they pair naturally with rounded-pill, rounded-card, and rounded-sheet."
    status="chosen"
  >
    <!-- ══════════════════════════════════════════════
         INTRO / TENET CALLOUT
         ══════════════════════════════════════════════ -->
    <section class="mb-14">
      <!-- Tenet #7 info banner -->
      <div
        class="rounded-card px-5 py-4 mb-8 border"
        style="background: rgba(104, 86, 178, 0.06); border-color: rgba(104, 86, 178, 0.20);"
      >
        <p class="text-body-sm font-semibold text-ink-primary mb-1" style="color: #6856B2">
          Tenet #7 — Glass sparingly, purposefully.
        </p>
        <p class="text-body-sm text-ink-secondary leading-relaxed">
          Glass is used on exactly four surfaces:
          <strong class="text-ink-primary">(1)</strong> the app-background ambient layer,
          <strong class="text-ink-primary">(2)</strong> the floating nav pill,
          <strong class="text-ink-primary">(3)</strong> the hero dashboard card,
          <strong class="text-ink-primary">(4)</strong> modal / sheet backdrops.
          Banned everywhere else.
          This page codifies <code class="font-mono text-caption">.surface-glass</code> and
          <code class="font-mono text-caption">.surface-glass-strong</code> so components
          consume one source of truth — never raw <code class="font-mono text-caption">backdrop-blur</code> utilities.
        </p>
      </div>
    </section>


    <!-- ══════════════════════════════════════════════
         SECTION 1 — STANDARD vs STRONG
         Side-by-side over iridescent-subtle background
         ══════════════════════════════════════════════ -->
    <section class="mb-14">
      <h2 class="text-xs font-semibold uppercase tracking-widest text-ink-secondary mb-2">
        1 — Standard vs Strong
      </h2>
      <p class="text-body-sm text-ink-tertiary mb-6">
        Both variants over a colourful background so the frosted effect is visible.
        Strong is more opaque — use it when text legibility is critical.
      </p>

      <!-- Light mode panel -->
      <div
        class="rounded-2xl overflow-hidden border mb-4"
        style="border-color: #E8E4DF"
      >
        <div class="px-4 pt-4 pb-1" style="background:#FAF8F5">
          <p class="text-xs font-medium text-ink-tertiary">Light mode</p>
        </div>
        <!-- Iridescent bg so glass has something to blur -->
        <div
          class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-6 items-start"
          style="background: linear-gradient(135deg, #EAE6F8 0%, #D9EEF2 35%, #D5F2E8 65%, #FCE9E0 100%)"
        >
          <!-- Standard -->
          <div class="flex flex-col gap-2">
            <div class="surface-glass rounded-card p-6">
              <p class="text-h4 font-heading text-ink-primary font-semibold mb-1">Standard glass</p>
              <p class="text-body-sm text-ink-secondary">More see-through. The gradient behind bleeds through. Best for decorative surfaces and overlays where the blur effect itself is the feature.</p>
            </div>
            <code class="text-caption font-mono text-ink-tertiary">.surface-glass</code>
          </div>

          <!-- Strong -->
          <div class="flex flex-col gap-2">
            <div class="surface-glass-strong rounded-card p-6">
              <p class="text-h4 font-heading text-ink-primary font-semibold mb-1">Strong glass</p>
              <p class="text-body-sm text-ink-secondary">More opaque. Content sits clearly above the blur. Use when nav labels, hero numbers, or dialog text must be readable in one pass.</p>
              <p class="text-caption text-ink-tertiary mt-2">Less see-through — higher legibility.</p>
            </div>
            <code class="text-caption font-mono text-ink-tertiary">.surface-glass-strong</code>
          </div>
        </div>
      </div>

      <!-- Dark mode panel -->
      <div
        class="rounded-2xl overflow-hidden border"
        style="border-color: #2C2A27"
      >
        <div class="px-4 pt-4 pb-1" style="background:#141311">
          <p class="text-xs font-medium" style="color:#6E6B67">Dark mode — simulated (inline dark glass values)</p>
        </div>
        <!-- Dark iridescent bg -->
        <div
          class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-6 items-start"
          style="background: linear-gradient(135deg, #302A48 0%, #1D3230 38%, #18342A 62%, #3E241A 100%)"
        >
          <!-- Standard dark -->
          <div class="flex flex-col gap-2">
            <div
              class="rounded-card p-6"
              style="
                background-color: rgba(28, 27, 25, 0.55);
                backdrop-filter: blur(18px) saturate(130%);
                -webkit-backdrop-filter: blur(18px) saturate(130%);
                border: 1px solid rgba(255, 255, 255, 0.08);
                box-shadow: var(--shadow-glass);
                position: relative;
              "
            >
              <p class="text-h4 font-heading font-semibold mb-1" style="color:#F0EDE9">Standard glass</p>
              <p class="text-body-sm" style="color:#A09C97">Warm smoky pane. The iridescent gradient behind it glows subtly through.</p>
            </div>
            <code class="text-caption font-mono" style="color:#6E6B67">.surface-glass</code>
          </div>

          <!-- Strong dark -->
          <div class="flex flex-col gap-2">
            <div
              class="rounded-card p-6"
              style="
                background-color: rgba(28, 27, 25, 0.75);
                backdrop-filter: blur(18px) saturate(130%);
                -webkit-backdrop-filter: blur(18px) saturate(130%);
                border: 1px solid rgba(255, 255, 255, 0.08);
                box-shadow: var(--shadow-glass);
                position: relative;
              "
            >
              <p class="text-h4 font-heading font-semibold mb-1" style="color:#F0EDE9">Strong glass</p>
              <p class="text-body-sm" style="color:#A09C97">Deep, premium pane. More coverage over the background — text is clearly readable.</p>
              <p class="text-caption mt-2" style="color:#6E6B67">Higher opacity → higher legibility.</p>
            </div>
            <code class="text-caption font-mono" style="color:#6E6B67">.surface-glass-strong</code>
          </div>
        </div>
      </div>
    </section>


    <!-- ══════════════════════════════════════════════
         SECTION 2 — THE FOUR ALLOWED SURFACES
         Each demo panel has a colorful/gradient bg
         ══════════════════════════════════════════════ -->
    <section class="mb-14">
      <h2 class="text-xs font-semibold uppercase tracking-widest text-ink-secondary mb-2">
        2 — The four allowed surfaces
      </h2>
      <p class="text-body-sm text-ink-tertiary mb-6">
        Glass must always have something colourful behind it. Each demo below shows the correct
        pairing of surface + background context.
      </p>

      <!-- 2a — App background ambient overlay -->
      <div class="mb-8">
        <p class="text-body-sm font-semibold text-ink-primary mb-1">a) App-background ambient overlay</p>
        <p class="text-caption text-ink-tertiary mb-3">
          The ambient gradient IS the app background. Glass cards float on it. This is the most
          decorative use — the gradient reads through the surface as warm atmosphere.
        </p>

        <!-- Light -->
        <div
          class="rounded-2xl overflow-hidden border mb-3"
          style="border-color: #E8E4DF"
        >
          <div class="px-4 pt-3 pb-1" style="background:#FAF8F5">
            <p class="text-xs font-medium text-ink-tertiary">Light mode — ambient background</p>
          </div>
          <div
            class="min-h-[200px] flex items-center justify-center p-8"
            style="background:
              radial-gradient(ellipse 80% 60% at 20% 10%, rgba(234, 230, 248, 0.55), transparent 50%),
              radial-gradient(ellipse 70% 50% at 85% 20%, rgba(252, 233, 224, 0.45), transparent 55%),
              radial-gradient(ellipse 60% 50% at 70% 90%, rgba(213, 242, 232, 0.40), transparent 50%),
              #FAF8F5"
          >
            <div class="surface-glass rounded-sheet p-6 w-full max-w-xs">
              <p class="text-caption text-ink-tertiary uppercase tracking-widest font-medium mb-1">Family Hub</p>
              <p class="text-h3 font-heading text-ink-primary font-semibold">Good morning, Greg.</p>
              <p class="text-body-sm text-ink-secondary mt-1">3 tasks · 2 events today</p>
            </div>
          </div>
        </div>

        <!-- Dark -->
        <div
          class="rounded-2xl overflow-hidden border"
          style="border-color: #2C2A27"
        >
          <div class="px-4 pt-3 pb-1" style="background:#141311">
            <p class="text-xs font-medium" style="color:#6E6B67">Dark mode</p>
          </div>
          <div
            class="min-h-[200px] flex items-center justify-center p-8"
            style="background:
              radial-gradient(ellipse 80% 60% at 20% 10%, rgba(72, 110, 156, 0.18), transparent 55%),
              radial-gradient(ellipse 70% 50% at 85% 25%, rgba(104, 86, 178, 0.15), transparent 55%),
              radial-gradient(ellipse 60% 50% at 70% 90%, rgba(190, 130, 48, 0.10), transparent 55%),
              #141311"
          >
            <div
              class="rounded-sheet p-6 w-full max-w-xs"
              style="
                background-color: rgba(28, 27, 25, 0.55);
                backdrop-filter: blur(18px) saturate(130%);
                -webkit-backdrop-filter: blur(18px) saturate(130%);
                border: 1px solid rgba(255, 255, 255, 0.08);
                box-shadow: var(--shadow-glass);
              "
            >
              <p class="text-caption uppercase tracking-widest font-medium mb-1" style="color:#6E6B67">Family Hub</p>
              <p class="text-h3 font-heading font-semibold" style="color:#F0EDE9">Good morning, Greg.</p>
              <p class="text-body-sm mt-1" style="color:#A09C97">3 tasks · 2 events today</p>
            </div>
          </div>
        </div>
        <p class="text-caption text-ink-tertiary mt-2">
          The ambient gradient IS the app background. Glass appears as cards floating on it.
        </p>
      </div>

      <!-- 2b — Floating nav pill -->
      <div class="mb-8">
        <p class="text-body-sm font-semibold text-ink-primary mb-1">b) Floating nav pill</p>
        <p class="text-caption text-ink-tertiary mb-3">
          Bottom or top nav pill. Use <code class="font-mono">.surface-glass-strong</code> here —
          the labels and icons must be legible while the pill floats above page content.
        </p>

        <!-- Light -->
        <div
          class="rounded-2xl overflow-hidden border mb-3"
          style="border-color: #E8E4DF"
        >
          <div class="px-4 pt-3 pb-1" style="background:#FAF8F5">
            <p class="text-xs font-medium text-ink-tertiary">Light mode — nav pill</p>
          </div>
          <div
            class="min-h-[120px] flex items-end justify-center p-6"
            style="background: linear-gradient(135deg, #EAE6F8 0%, #D9EEF2 35%, #D5F2E8 65%, #FCE9E0 100%)"
          >
            <div class="surface-glass-strong rounded-pill px-2 py-1.5 flex items-center gap-1">
              <!-- Active slot -->
              <div class="flex flex-col items-center gap-1 px-3 py-1.5 rounded-pill" style="background: #1C1C1E">
                <svg class="w-5 h-5" style="color:#FAF8F5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                </svg>
                <span class="text-[10px] font-medium" style="color:#FAF8F5">Today</span>
              </div>
              <!-- Inactive slots -->
              <div class="flex flex-col items-center gap-1 px-3 py-1.5">
                <svg class="w-5 h-5 text-ink-secondary" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                </svg>
                <span class="text-[10px] font-medium text-ink-tertiary">Plan</span>
              </div>
              <div class="flex flex-col items-center gap-1 px-3 py-1.5">
                <svg class="w-5 h-5 text-ink-secondary" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z" />
                </svg>
                <span class="text-[10px] font-medium text-ink-tertiary">Ask</span>
              </div>
              <div class="flex flex-col items-center gap-1 px-3 py-1.5">
                <svg class="w-5 h-5 text-ink-secondary" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>
                <span class="text-[10px] font-medium text-ink-tertiary">Me</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Dark -->
        <div
          class="rounded-2xl overflow-hidden border"
          style="border-color: #2C2A27"
        >
          <div class="px-4 pt-3 pb-1" style="background:#141311">
            <p class="text-xs font-medium" style="color:#6E6B67">Dark mode</p>
          </div>
          <div
            class="min-h-[120px] flex items-end justify-center p-6"
            style="background: linear-gradient(135deg, #302A48 0%, #1D3230 38%, #18342A 62%, #3E241A 100%)"
          >
            <div
              class="rounded-pill px-2 py-1.5 flex items-center gap-1"
              style="
                background-color: rgba(28, 27, 25, 0.75);
                backdrop-filter: blur(18px) saturate(130%);
                -webkit-backdrop-filter: blur(18px) saturate(130%);
                border: 1px solid rgba(255, 255, 255, 0.08);
                box-shadow: var(--shadow-glass);
              "
            >
              <div class="flex flex-col items-center gap-1 px-3 py-1.5 rounded-pill" style="background: #F0EDE9">
                <svg class="w-5 h-5" style="color:#1C1C1E" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                </svg>
                <span class="text-[10px] font-medium" style="color:#1C1C1E">Today</span>
              </div>
              <div class="flex flex-col items-center gap-1 px-3 py-1.5">
                <svg class="w-5 h-5" style="color:#6E6B67" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                </svg>
                <span class="text-[10px] font-medium" style="color:#6E6B67">Plan</span>
              </div>
              <div class="flex flex-col items-center gap-1 px-3 py-1.5">
                <svg class="w-5 h-5" style="color:#6E6B67" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z" />
                </svg>
                <span class="text-[10px] font-medium" style="color:#6E6B67">Ask</span>
              </div>
              <div class="flex flex-col items-center gap-1 px-3 py-1.5">
                <svg class="w-5 h-5" style="color:#6E6B67" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>
                <span class="text-[10px] font-medium" style="color:#6E6B67">Me</span>
              </div>
            </div>
          </div>
        </div>
        <p class="text-caption text-ink-tertiary mt-2">
          Bottom nav / top nav pattern — strong variant for legibility while moving above page content.
        </p>
      </div>

      <!-- 2c — Hero dashboard card -->
      <div class="mb-8">
        <p class="text-body-sm font-semibold text-ink-primary mb-1">c) Hero dashboard card</p>
        <p class="text-caption text-ink-tertiary mb-3">
          The featured metric card on the dashboard hero. Use
          <code class="font-mono">.surface-glass-strong</code> so hero numbers read clearly.
        </p>

        <!-- Light -->
        <div
          class="rounded-2xl overflow-hidden border mb-3"
          style="border-color: #E8E4DF"
        >
          <div class="px-4 pt-3 pb-1" style="background:#FAF8F5">
            <p class="text-xs font-medium text-ink-tertiary">Light mode</p>
          </div>
          <div
            class="min-h-[220px] flex items-center justify-center p-8"
            style="background:
              radial-gradient(ellipse 80% 60% at 20% 10%, rgba(234, 230, 248, 0.55), transparent 50%),
              radial-gradient(ellipse 70% 50% at 85% 20%, rgba(252, 233, 224, 0.45), transparent 55%),
              radial-gradient(ellipse 60% 50% at 70% 90%, rgba(213, 242, 232, 0.40), transparent 50%),
              #FAF8F5"
          >
            <div class="surface-glass-strong rounded-card p-8 w-full max-w-xs">
              <p class="text-caption text-ink-tertiary uppercase tracking-widest font-medium mb-2">Points balance</p>
              <p class="text-hero font-heading text-ink-primary leading-none tracking-tight" style="font-size: clamp(3rem, 8vw, 5rem)">1,247</p>
              <p class="text-body-sm text-ink-secondary mt-3">+38 this week</p>
            </div>
          </div>
        </div>

        <!-- Dark -->
        <div
          class="rounded-2xl overflow-hidden border"
          style="border-color: #2C2A27"
        >
          <div class="px-4 pt-3 pb-1" style="background:#141311">
            <p class="text-xs font-medium" style="color:#6E6B67">Dark mode</p>
          </div>
          <div
            class="min-h-[220px] flex items-center justify-center p-8"
            style="background:
              radial-gradient(ellipse 80% 60% at 20% 10%, rgba(72, 110, 156, 0.18), transparent 55%),
              radial-gradient(ellipse 70% 50% at 85% 25%, rgba(104, 86, 178, 0.15), transparent 55%),
              radial-gradient(ellipse 60% 50% at 70% 90%, rgba(190, 130, 48, 0.10), transparent 55%),
              #141311"
          >
            <div
              class="rounded-card p-8 w-full max-w-xs"
              style="
                background-color: rgba(28, 27, 25, 0.75);
                backdrop-filter: blur(18px) saturate(130%);
                -webkit-backdrop-filter: blur(18px) saturate(130%);
                border: 1px solid rgba(255, 255, 255, 0.08);
                box-shadow: var(--shadow-glass);
              "
            >
              <p class="text-caption uppercase tracking-widest font-medium mb-2" style="color:#6E6B67">Points balance</p>
              <p class="font-heading leading-none tracking-tight" style="font-size: clamp(3rem, 8vw, 5rem); color:#F0EDE9">1,247</p>
              <p class="text-body-sm mt-3" style="color:#A09C97">+38 this week</p>
            </div>
          </div>
        </div>
        <p class="text-caption text-ink-tertiary mt-2">
          Dashboard hero — content sits on a subtle ambient wash. Strong variant keeps hero numbers crisp.
        </p>
      </div>

      <!-- 2d — Modal backdrop -->
      <div class="mb-2">
        <p class="text-body-sm font-semibold text-ink-primary mb-1">d) Modal / sheet backdrop</p>
        <p class="text-caption text-ink-tertiary mb-3">
          The dialog surface itself uses glass. The darkened overlay behind it is standard (semi-opaque black) —
          that scrim handles focus isolation, not glass.
        </p>

        <!-- Light -->
        <div
          class="rounded-2xl overflow-hidden border mb-3"
          style="border-color: #E8E4DF"
        >
          <div class="px-4 pt-3 pb-1" style="background:#FAF8F5">
            <p class="text-xs font-medium text-ink-tertiary">Light mode</p>
          </div>
          <!-- Iridescent "page content" behind the backdrop -->
          <div
            class="relative min-h-[280px] flex items-center justify-center p-6"
            style="background: linear-gradient(135deg, #EAE6F8 0%, #D9EEF2 35%, #D5F2E8 65%, #FCE9E0 100%)"
          >
            <!-- Standard darkened backdrop overlay -->
            <div class="absolute inset-0" style="background: rgba(0,0,0,0.38)"></div>
            <!-- Glass dialog -->
            <div class="surface-glass-strong rounded-sheet p-6 max-w-sm w-full relative z-10">
              <p class="text-h4 font-heading text-ink-primary font-semibold mb-2">Spend 50 points?</p>
              <p class="text-body-sm text-ink-secondary mb-5">
                Redeeming <strong class="text-ink-primary">Movie Night</strong> will deduct 50 points from your balance.
              </p>
              <div class="flex gap-3 justify-end">
                <button type="button" class="rounded-pill px-4 py-2 text-body-sm font-medium text-ink-secondary border border-border-subtle bg-surface-raised hover:bg-surface-sunken transition-colors duration-quick">
                  Cancel
                </button>
                <button type="button" class="rounded-pill px-4 py-2 text-body-sm font-medium text-ink-inverse" style="background:#1C1C1E">
                  Confirm
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Dark -->
        <div
          class="rounded-2xl overflow-hidden border"
          style="border-color: #2C2A27"
        >
          <div class="px-4 pt-3 pb-1" style="background:#141311">
            <p class="text-xs font-medium" style="color:#6E6B67">Dark mode</p>
          </div>
          <div
            class="relative min-h-[280px] flex items-center justify-center p-6"
            style="background: linear-gradient(135deg, #302A48 0%, #1D3230 38%, #18342A 62%, #3E241A 100%)"
          >
            <div class="absolute inset-0" style="background: rgba(0,0,0,0.50)"></div>
            <div
              class="rounded-sheet p-6 max-w-sm w-full relative z-10"
              style="
                background-color: rgba(28, 27, 25, 0.75);
                backdrop-filter: blur(18px) saturate(130%);
                -webkit-backdrop-filter: blur(18px) saturate(130%);
                border: 1px solid rgba(255, 255, 255, 0.08);
                box-shadow: var(--shadow-glass);
              "
            >
              <p class="text-h4 font-heading font-semibold mb-2" style="color:#F0EDE9">Spend 50 points?</p>
              <p class="text-body-sm mb-5" style="color:#A09C97">
                Redeeming <strong style="color:#F0EDE9">Movie Night</strong> will deduct 50 points from your balance.
              </p>
              <div class="flex gap-3 justify-end">
                <button type="button" class="rounded-pill px-4 py-2 text-body-sm font-medium transition-colors duration-quick" style="color:#A09C97; border: 1px solid rgba(255,255,255,0.08); background: rgba(255,255,255,0.04)">
                  Cancel
                </button>
                <button type="button" class="rounded-pill px-4 py-2 text-body-sm font-medium" style="background:#F0EDE9; color:#1C1C1E">
                  Confirm
                </button>
              </div>
            </div>
          </div>
        </div>
        <p class="text-caption text-ink-tertiary mt-2">
          Sheets and modals use glass for the dialog surface. The darkened overlay (40–50% black scrim)
          remains standard — it handles focus isolation. Glass handles the dialog surface.
        </p>
      </div>
    </section>


    <!-- ══════════════════════════════════════════════
         SECTION 3 — BANNED SURFACES
         Three "DON'T" examples
         ══════════════════════════════════════════════ -->
    <section class="mb-14">
      <h2 class="text-xs font-semibold uppercase tracking-widest text-ink-secondary mb-2">
        3 — Banned surfaces
      </h2>
      <p class="text-body-sm text-ink-tertiary mb-6">
        Glass on dense data or small controls murks the content. These three examples show why
        the constraint exists — each one reads worse than a plain surface would.
      </p>

      <div
        class="rounded-2xl border border-border-subtle p-6"
        style="background:#FAF8F5"
      >
        <p class="text-xs font-medium text-ink-tertiary mb-5">
          The background gradient is intentional — it shows how badly glass degrades legibility on small controls.
        </p>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
          <!-- DON'T 1: Form input -->
          <div class="flex flex-col gap-3">
            <div class="relative">
              <!-- Red X badge -->
              <div class="absolute -top-2 -right-2 z-10 w-6 h-6 rounded-full flex items-center justify-center text-white text-xs font-bold" style="background:#BA4A4A">
                ✕
              </div>
              <!-- Glass input — terrible -->
              <div
                class="rounded-field px-3.5 py-2.5 w-full"
                style="
                  background-color: rgba(255, 255, 255, 0.55);
                  backdrop-filter: blur(16px) saturate(140%);
                  -webkit-backdrop-filter: blur(16px) saturate(140%);
                  border: 1px solid rgba(255, 255, 255, 0.65);
                  box-shadow: var(--shadow-glass);
                  background-image: linear-gradient(135deg, #EAE6F8 0%, #D9EEF2 35%, #D5F2E8 65%, #FCE9E0 100%);
                "
              >
                <span class="text-body-sm" style="color: rgba(28,28,30,0.6)">Enter email address…</span>
              </div>
            </div>
            <div>
              <p class="text-caption font-semibold text-ink-primary mb-0.5">Form input</p>
              <p class="text-caption text-ink-tertiary leading-snug">
                The focus ring, label, and placeholder compete with the blur behind the field. Dense form layouts become unreadable.
              </p>
            </div>
          </div>

          <!-- DON'T 2: Data table row -->
          <div class="flex flex-col gap-3">
            <div class="relative">
              <div class="absolute -top-2 -right-2 z-10 w-6 h-6 rounded-full flex items-center justify-center text-white text-xs font-bold" style="background:#BA4A4A">
                ✕
              </div>
              <!-- Glass table row — terrible -->
              <div
                class="rounded-field px-3 py-2.5 w-full"
                style="
                  background-color: rgba(255, 255, 255, 0.55);
                  backdrop-filter: blur(16px) saturate(140%);
                  -webkit-backdrop-filter: blur(16px) saturate(140%);
                  border: 1px solid rgba(255, 255, 255, 0.65);
                  box-shadow: var(--shadow-glass);
                  background-image: linear-gradient(135deg, #D9EEF2 0%, #FCE9E0 100%);
                "
              >
                <div class="flex items-center justify-between gap-2">
                  <span class="text-body-sm font-medium" style="color: rgba(28,28,30,0.55)">Buy groceries</span>
                  <span class="text-caption rounded-full px-2 py-0.5" style="background: rgba(104,86,178,0.3); color: rgba(104,86,178,0.6)">Medium</span>
                </div>
              </div>
            </div>
            <div>
              <p class="text-caption font-semibold text-ink-primary mb-0.5">Data-table row</p>
              <p class="text-caption text-ink-tertiary leading-snug">
                When every row is glass, the blur stacks and the whole list becomes visual noise. Status pills lose their clear color signal.
              </p>
            </div>
          </div>

          <!-- DON'T 3: Calendar event tile -->
          <div class="flex flex-col gap-3">
            <div class="relative">
              <div class="absolute -top-2 -right-2 z-10 w-6 h-6 rounded-full flex items-center justify-center text-white text-xs font-bold" style="background:#BA4A4A">
                ✕
              </div>
              <!-- Glass event tile — terrible -->
              <div
                class="rounded-tile px-3 py-2.5 w-full border-l-4"
                style="
                  background-color: rgba(255, 255, 255, 0.55);
                  backdrop-filter: blur(16px) saturate(140%);
                  -webkit-backdrop-filter: blur(16px) saturate(140%);
                  border: 1px solid rgba(255, 255, 255, 0.65);
                  border-left: 4px solid rgba(104,86,178,0.4);
                  box-shadow: var(--shadow-glass);
                  background-image: linear-gradient(135deg, #FCE9E0 0%, #D5F2E8 100%);
                "
              >
                <p class="text-body-sm font-medium" style="color: rgba(28,28,30,0.55)">Soccer practice</p>
                <p class="text-caption" style="color: rgba(107,105,102,0.5)">3:30 – 5:00 PM</p>
              </div>
            </div>
            <div>
              <p class="text-caption font-semibold text-ink-primary mb-0.5">Calendar event tile</p>
              <p class="text-caption text-ink-tertiary leading-snug">
                The left-edge color bar (the key identifier for event source) washes out. Time and title are harder to scan against the blurred content.
              </p>
            </div>
          </div>
        </div>

        <div class="mt-6 rounded-xl border border-border-subtle bg-surface-raised px-4 py-3 text-body-sm text-ink-secondary">
          <strong class="text-ink-primary">Rule of thumb:</strong>
          If the element is repeated in a list, a grid, or a table — no glass.
          Glass reads as special and floating. Repeated glass reads as chaos.
        </div>
      </div>
    </section>


    <!-- ══════════════════════════════════════════════
         SECTION 4 — TOKEN REFERENCE TABLE
         ══════════════════════════════════════════════ -->
    <section class="mb-6">
      <h2 class="text-xs font-semibold uppercase tracking-widest text-ink-secondary mb-2">
        4 — Token reference
      </h2>
      <p class="text-body-sm text-ink-tertiary mb-5">
        Six CSS custom properties backing two utility classes.
        Values swap between <code class="font-mono text-caption">:root</code> (light) and
        <code class="font-mono text-caption">html.dark</code> (dark) automatically.
      </p>

      <div class="rounded-xl border border-border-subtle overflow-x-auto">
        <table class="w-full text-xs min-w-[500px]">
          <thead>
            <tr class="bg-surface-sunken border-b border-border-subtle">
              <th class="text-left px-3 py-2 font-semibold text-ink-secondary">CSS var</th>
              <th class="text-left px-3 py-2 font-semibold text-ink-secondary">Light value</th>
              <th class="text-left px-3 py-2 font-semibold text-ink-secondary">Dark value</th>
              <th class="text-left px-3 py-2 font-semibold text-ink-secondary hidden sm:table-cell">Usage</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(token, i) in GLASS_TOKENS"
              :key="token.cssVar"
              :class="i % 2 === 1 ? 'bg-surface-sunken' : 'bg-surface-raised'"
            >
              <td class="px-3 py-2 font-mono text-ink-primary text-[11px] whitespace-nowrap">{{ token.cssVar }}</td>
              <td class="px-3 py-2 font-mono text-ink-tertiary text-[11px] whitespace-nowrap">{{ token.lightValue }}</td>
              <td class="px-3 py-2 font-mono text-ink-tertiary text-[11px] whitespace-nowrap">{{ token.darkValue }}</td>
              <td class="px-3 py-2 text-ink-tertiary leading-snug hidden sm:table-cell">{{ token.usage }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Utility class table -->
      <div class="mt-4 rounded-xl border border-border-subtle overflow-x-auto">
        <table class="w-full text-xs min-w-[400px]">
          <thead>
            <tr class="bg-surface-sunken border-b border-border-subtle">
              <th class="text-left px-3 py-2 font-semibold text-ink-secondary">Utility class</th>
              <th class="text-left px-3 py-2 font-semibold text-ink-secondary hidden sm:table-cell">Usage</th>
              <th class="text-left px-3 py-2 font-semibold text-ink-secondary">Note</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(u, i) in UTILITY_CLASSES"
              :key="u.cls"
              :class="i % 2 === 1 ? 'bg-surface-sunken' : 'bg-surface-raised'"
            >
              <td class="px-3 py-2 font-mono text-ink-primary whitespace-nowrap">{{ u.cls }}</td>
              <td class="px-3 py-2 text-ink-tertiary leading-snug hidden sm:table-cell">{{ u.usage }}</td>
              <td class="px-3 py-2 text-ink-tertiary leading-snug">{{ u.note }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Implementation note -->
      <div class="mt-4 rounded-xl border border-border-subtle bg-surface-sunken px-4 py-3 text-body-sm text-ink-secondary">
        <strong class="text-ink-primary">border-radius inheritance:</strong>
        The <code class="font-mono text-caption">::before</code> highlight pseudo-element uses
        <code class="font-mono text-caption">border-radius: inherit</code>, so any
        <code class="font-mono text-caption">rounded-*</code> class you add to the surface element
        automatically shapes the inner top highlight.
        <code class="font-mono text-caption">rounded-pill</code>,
        <code class="font-mono text-caption">rounded-card</code>, and
        <code class="font-mono text-caption">rounded-sheet</code> all work without extra overrides.
        <br /><br />
        <strong class="text-ink-primary">-webkit-backdrop-filter:</strong>
        Both variants include the <code class="font-mono text-caption">-webkit-</code> prefix for
        full Safari support. Never use raw <code class="font-mono text-caption">backdrop-filter</code>
        utilities from Tailwind — use these classes instead.
      </div>
    </section>
  </ComponentPage>
</template>

<style scoped>
/* No scoped styles needed — all visual work is done via utility classes and inline styles. */
</style>
