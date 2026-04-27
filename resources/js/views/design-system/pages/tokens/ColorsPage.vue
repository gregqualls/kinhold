<script setup>
import ComponentPage from '../../shared/ComponentPage.vue'

// ── Surface & Ink tokens ────────────────────────────────────────────────────
// Each entry: cssVar = token name, tailwind = utility class, hex light + dark, usage

const SURFACES = [
  {
    name: 'App background',
    cssVar: '--surface-app',
    tailwind: 'bg-surface-app',
    lightHex: '#FAF8F5',
    darkHex:  '#141311',
    usage:    'Page shell, app wrapper',
  },
  {
    name: 'Raised surface',
    cssVar: '--surface-raised',
    tailwind: 'bg-surface-raised',
    lightHex: '#FFFFFF',
    darkHex:  '#1C1B19',
    usage:    'Cards, modals, elevated UI',
  },
  {
    name: 'Sunken surface',
    cssVar: '--surface-sunken',
    tailwind: 'bg-surface-sunken',
    lightHex: '#F5F2EE',
    darkHex:  '#161513',
    usage:    'Input fills, sidebar rail, table alt rows',
  },
  {
    name: 'Overlay surface',
    cssVar: '--surface-overlay',
    tailwind: 'bg-surface-overlay',
    lightHex: '#FFFFFF',
    darkHex:  '#242220',
    usage:    'Sheets, modals, drawers',
  },
]

const INK_TOKENS = [
  {
    name: 'Ink primary',
    cssVar: '--ink-primary',
    tailwind: 'text-ink-primary',
    lightHex: '#1C1C1E',
    darkHex:  '#F0EDE9',
    usage:    'Body text, headings, icons',
  },
  {
    name: 'Ink secondary',
    cssVar: '--ink-secondary',
    tailwind: 'text-ink-secondary',
    lightHex: '#6B6966',
    darkHex:  '#A09C97',
    usage:    'Labels, supporting text, metadata',
  },
  {
    name: 'Ink tertiary',
    cssVar: '--ink-tertiary',
    tailwind: 'text-ink-tertiary',
    lightHex: '#9C9895',
    darkHex:  '#6E6B67',
    usage:    'Captions, placeholders, disabled',
  },
  {
    name: 'Ink inverse',
    cssVar: '--ink-inverse',
    tailwind: 'text-ink-inverse',
    lightHex: '#FAF8F5',
    darkHex:  '#1C1C1E',
    usage:    'Text on filled/dark surfaces',
  },
]

const BORDERS = [
  {
    name: 'Border subtle',
    cssVar: '--border-subtle',
    tailwind: 'border-border-subtle',
    lightHex: '#E8E4DF',
    darkHex:  '#2C2A27',
    usage:    'Hairline dividers, card edges',
  },
  {
    name: 'Border strong',
    cssVar: '--border-strong',
    tailwind: 'border-border-strong',
    lightHex: '#BCB8B2',
    darkHex:  '#403E3A',
    usage:    'Active inputs, hover states, focus rings',
  },
]

// ── Category accents ────────────────────────────────────────────────────────
const ACCENTS = [
  {
    family:      'Lavender',
    softCssVar:  '--accent-lavender-soft',
    boldCssVar:  '--accent-lavender-bold',
    softTw:      'bg-accent-lavender-soft',
    boldTw:      'text-accent-lavender-bold',
    lightSoftHex: '#EAE6F8',
    lightBoldHex: '#6856B2',
    darkSoftHex:  '#302A48',
    darkBoldHex:  '#B6A8E6',
    usage:        'Calendar, tasks — purple family',
  },
  {
    family:      'Peach',
    softCssVar:  '--accent-peach-soft',
    boldCssVar:  '--accent-peach-bold',
    softTw:      'bg-accent-peach-soft',
    boldTw:      'text-accent-peach-bold',
    lightSoftHex: '#FCE9E0',
    lightBoldHex: '#BA562E',
    darkSoftHex:  '#3E241A',
    darkBoldHex:  '#F0A882',
    usage:        'Calendar, avatars — warm orange family',
  },
  {
    family:      'Mint',
    softCssVar:  '--accent-mint-soft',
    boldCssVar:  '--accent-mint-bold',
    softTw:      'bg-accent-mint-soft',
    boldTw:      'text-accent-mint-bold',
    lightSoftHex: '#D5F2E8',
    lightBoldHex: '#2E8A62',
    darkSoftHex:  '#18342A',
    darkBoldHex:  '#7CD6AE',
    usage:        'Calendar, tasks — sage-green family',
  },
  {
    family:      'Sun',
    softCssVar:  '--accent-sun-soft',
    boldCssVar:  '--accent-sun-bold',
    softTw:      'bg-accent-sun-soft',
    boldTw:      'text-accent-sun-bold',
    lightSoftHex: '#FCF3D2',
    lightBoldHex: '#A2780C',
    darkSoftHex:  '#342C0A',
    darkBoldHex:  '#E6C452',
    usage:        'Calendar, chips — golden-yellow family',
  },
]

// ── Status tokens ────────────────────────────────────────────────────────────
const STATUS = [
  {
    name: 'Success',
    cssVar: '--status-success',
    tailwind: 'bg-status-success',
    lightHex: '#4D8C6A',
    darkHex:  '#6CC498',
    desc: 'Completed tasks, earned achievements, successful AI runs',
  },
  {
    name: 'Pending',
    cssVar: '--status-pending',
    tailwind: 'bg-status-pending',
    lightHex: '#486E9C',
    darkHex:  '#78A4DC',
    desc: 'Awaiting action, in-flight, scheduled',
  },
  {
    name: 'Paused',
    cssVar: '--status-paused',
    tailwind: 'bg-status-paused',
    lightHex: '#BE8230',
    darkHex:  '#DCA848',
    desc: 'Paused automations, snoozed reminders',
  },
  {
    name: 'Failed',
    cssVar: '--status-failed',
    tailwind: 'bg-status-failed',
    lightHex: '#BA4A4A',
    darkHex:  '#E67070',
    desc: 'Errors, failed AI calls, destructive confirmations',
  },
  {
    name: 'Info',
    cssVar: '--status-info',
    tailwind: 'bg-status-info',
    lightHex: '#6856B2',
    darkHex:  '#B6A8E6',
    desc: 'Informational, neutral — shares lavender-bold',
  },
  {
    name: 'Warning',
    cssVar: '--status-warning',
    tailwind: 'bg-status-warning',
    lightHex: '#C48C24',
    darkHex:  '#E6C452',
    desc: 'Approaching deadlines, low stock, expiring',
  },
]

// Whether the status pill needs dark text (for light backgrounds in dark mode)
function statusTextClass(isDark, hex) {
  // For dark mode, all status colors are light enough to use dark text for contrast
  // In light mode all are dark enough to use white text
  return isDark ? 'text-ink-inverse' : 'text-white'
}

// ── Brand gold ───────────────────────────────────────────────────────────────
const BRAND_GOLD = { hex: '#C4975A', cssVar: '--brand-gold', tailwind: 'bg-brand-gold' }

// ── Shared swatch helpers ─────────────────────────────────────────────────────
// Inline hex used for literal color chips since Tailwind JIT needs static class names;
// we use :style for the swatch fills.
</script>

<template>
  <ComponentPage
    title="0.1 Color Tokens"
    description="Redesign token layer — warm near-white light / deep warm-charcoal dark. Both modes authored independently. Use these tokens in new redesign components; existing kin-*/prussian-*/wisteria-* tokens remain available during migration."
    status="chosen"
  >

    <!-- ══════════════════════════════════════════════
         SECTION 1 — SURFACES & INK
         Light and dark panels rendered side by side
         ══════════════════════════════════════════════ -->
    <section class="mb-12">
      <h2 class="text-xs font-semibold uppercase tracking-widest text-ink-secondary mb-6">
        1 — Surfaces &amp; Ink
      </h2>

      <!-- Light panel -->
      <div class="mb-8">
        <p class="text-xs font-medium text-ink-tertiary mb-3">Light mode</p>
        <div class="rounded-2xl border border-border-subtle p-5 space-y-3" style="background:#FAF8F5">

          <!-- Surface swatches row -->
          <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div v-for="s in SURFACES" :key="s.cssVar"
                 class="rounded-xl overflow-hidden border"
                 style="border-color:#E8E4DF">
              <!-- Swatch chip -->
              <div class="h-14 flex items-end px-2.5 pb-1.5 relative"
                   :style="{ background: s.lightHex, border: s.lightHex === '#FFFFFF' ? '1px solid #E8E4DF' : 'none' }">
                <!-- ink-primary text sample on this surface -->
                <span class="text-[10px] font-medium truncate" style="color:#1C1C1E">{{ s.name }}</span>
              </div>
              <div class="px-2.5 py-2 text-[11px]" style="background:#FFFFFF">
                <code class="font-mono text-[10px] opacity-70 block">{{ s.cssVar }}</code>
                <code class="font-mono text-[10px] opacity-50 block">{{ s.tailwind }}</code>
                <span class="font-mono text-[10px]" style="color:#6B6966">{{ s.lightHex }}</span>
              </div>
            </div>
          </div>

          <!-- Ink + border swatches row -->
          <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-3">
            <div v-for="t in INK_TOKENS" :key="t.cssVar"
                 class="rounded-xl overflow-hidden border"
                 style="border-color:#E8E4DF">
              <div class="h-10 flex items-center px-3"
                   :style="{ background: t.lightHex }">
                <span class="text-[10px] font-medium truncate"
                      :style="{ color: t.name === 'Ink inverse' ? '#1C1C1E' : '#FAF8F5' }">Aa</span>
              </div>
              <div class="px-2.5 py-2 text-[11px]" style="background:#FFFFFF">
                <p class="font-semibold leading-tight" style="color:#1C1C1E">{{ t.name }}</p>
                <code class="font-mono text-[10px] opacity-60">{{ t.lightHex }}</code>
              </div>
            </div>
            <div v-for="b in BORDERS" :key="b.cssVar"
                 class="rounded-xl overflow-hidden border"
                 style="border-color:#E8E4DF">
              <div class="h-10 flex items-center px-3"
                   :style="{ background: '#FAF8F5', borderBottom: `3px solid ${b.lightHex}` }">
              </div>
              <div class="px-2.5 py-2 text-[11px]" style="background:#FFFFFF">
                <p class="font-semibold leading-tight" style="color:#1C1C1E">{{ b.name }}</p>
                <code class="font-mono text-[10px] opacity-60">{{ b.lightHex }}</code>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Dark panel -->
      <div>
        <p class="text-xs font-medium text-ink-tertiary mb-3">Dark mode</p>
        <div class="rounded-2xl border p-5 space-y-3" style="background:#141311; border-color:#2C2A27">

          <!-- Surface swatches row -->
          <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div v-for="s in SURFACES" :key="s.cssVar"
                 class="rounded-xl overflow-hidden"
                 style="border: 1px solid #2C2A27">
              <div class="h-14 flex items-end px-2.5 pb-1.5"
                   :style="{ background: s.darkHex }">
                <span class="text-[10px] font-medium truncate" style="color:#F0EDE9">{{ s.name }}</span>
              </div>
              <div class="px-2.5 py-2 text-[11px]" style="background:#1C1B19">
                <code class="font-mono text-[10px] block" style="color:#A09C97; opacity:0.8">{{ s.cssVar }}</code>
                <span class="font-mono text-[10px]" style="color:#6E6B67">{{ s.darkHex }}</span>
              </div>
            </div>
          </div>

          <!-- Ink + border swatches row -->
          <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-3">
            <div v-for="t in INK_TOKENS" :key="t.cssVar"
                 class="rounded-xl overflow-hidden"
                 style="border: 1px solid #2C2A27">
              <div class="h-10 flex items-center px-3"
                   :style="{ background: t.darkHex }">
                <span class="text-[10px] font-medium truncate"
                      :style="{ color: t.name === 'Ink inverse' ? '#F0EDE9' : '#141311' }">Aa</span>
              </div>
              <div class="px-2.5 py-2 text-[11px]" style="background:#1C1B19">
                <p class="font-semibold leading-tight" style="color:#F0EDE9">{{ t.name }}</p>
                <code class="font-mono text-[10px]" style="color:#6E6B67">{{ t.darkHex }}</code>
              </div>
            </div>
            <div v-for="b in BORDERS" :key="b.cssVar"
                 class="rounded-xl overflow-hidden"
                 style="border: 1px solid #2C2A27">
              <div class="h-10 flex items-center px-3"
                   :style="{ background: '#141311', borderBottom: `3px solid ${b.darkHex}` }">
              </div>
              <div class="px-2.5 py-2 text-[11px]" style="background:#1C1B19">
                <p class="font-semibold leading-tight" style="color:#F0EDE9">{{ b.name }}</p>
                <code class="font-mono text-[10px]" style="color:#6E6B67">{{ b.darkHex }}</code>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ══════════════════════════════════════════════
         SECTION 2 — CATEGORY ACCENTS
         4 pastel families, -soft bg + -bold text
         ══════════════════════════════════════════════ -->
    <section class="mb-12">
      <h2 class="text-xs font-semibold uppercase tracking-widest text-ink-secondary mb-6">
        2 — Category Accents (pastel pairs)
      </h2>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div v-for="a in ACCENTS" :key="a.family" class="rounded-2xl overflow-hidden border border-border-subtle">
          <!-- Light row -->
          <div class="flex items-stretch">
            <!-- Soft chip preview -->
            <div class="flex-1 p-4 flex flex-col gap-2" :style="{ background: a.lightSoftHex }">
              <span class="text-xs font-semibold" :style="{ color: a.lightBoldHex }">
                {{ a.family }} — Light
              </span>
              <!-- Chip demo -->
              <span class="inline-flex items-center gap-1.5 self-start rounded-full px-2.5 py-0.5 text-xs font-medium"
                    :style="{ background: a.lightSoftHex, color: a.lightBoldHex, border: `1px solid ${a.lightBoldHex}20` }">
                <span class="w-1.5 h-1.5 rounded-full inline-block" :style="{ background: a.lightBoldHex }"></span>
                {{ a.family }}
              </span>
              <p class="text-[10px] mt-1" :style="{ color: a.lightBoldHex }">{{ a.usage }}</p>
            </div>
            <!-- Token info -->
            <div class="w-36 p-3 text-[10px] bg-surface-raised border-l border-border-subtle space-y-1">
              <div>
                <p class="opacity-50 mb-0.5">soft bg</p>
                <code class="font-mono">{{ a.softCssVar }}</code>
                <br><code class="font-mono opacity-60">{{ a.lightSoftHex }}</code>
              </div>
              <div>
                <p class="opacity-50 mb-0.5">bold text</p>
                <code class="font-mono">{{ a.boldCssVar }}</code>
                <br><code class="font-mono opacity-60">{{ a.lightBoldHex }}</code>
              </div>
            </div>
          </div>
          <!-- Dark row -->
          <div class="flex items-stretch border-t border-border-subtle">
            <div class="flex-1 p-4 flex flex-col gap-2" :style="{ background: a.darkSoftHex }">
              <span class="text-xs font-semibold" :style="{ color: a.darkBoldHex }">
                {{ a.family }} — Dark
              </span>
              <span class="inline-flex items-center gap-1.5 self-start rounded-full px-2.5 py-0.5 text-xs font-medium"
                    :style="{ background: a.darkSoftHex, color: a.darkBoldHex, border: `1px solid ${a.darkBoldHex}30` }">
                <span class="w-1.5 h-1.5 rounded-full inline-block" :style="{ background: a.darkBoldHex }"></span>
                {{ a.family }}
              </span>
            </div>
            <div class="w-36 p-3 text-[10px] border-l border-border-subtle space-y-1" style="background:#1C1B19; color:#A09C97">
              <div>
                <p class="opacity-50 mb-0.5">soft bg</p>
                <code class="font-mono">{{ a.darkSoftHex }}</code>
              </div>
              <div>
                <p class="opacity-50 mb-0.5">bold text</p>
                <code class="font-mono">{{ a.darkBoldHex }}</code>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ══════════════════════════════════════════════
         SECTION 3 — STATUS TOKENS
         Row of pills, light + dark panels
         ══════════════════════════════════════════════ -->
    <section class="mb-12">
      <h2 class="text-xs font-semibold uppercase tracking-widest text-ink-secondary mb-6">
        3 — Status tokens
      </h2>

      <!-- Light panel -->
      <div class="rounded-2xl border border-border-subtle p-5 mb-4" style="background:#FAF8F5">
        <p class="text-[11px] font-medium mb-3" style="color:#9C9895">Light mode</p>
        <div class="flex flex-wrap gap-3">
          <div v-for="s in STATUS" :key="s.name" class="flex flex-col gap-1.5 items-start">
            <!-- Filled pill -->
            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold text-white"
                  :style="{ background: s.lightHex }">
              {{ s.name }}
            </span>
            <!-- Tinted bg + colored text -->
            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium"
                  :style="{ background: s.lightHex + '1A', color: s.lightHex }">
              {{ s.name }}
            </span>
            <code class="text-[10px] font-mono opacity-60" style="color:#6B6966">{{ s.lightHex }}</code>
          </div>
        </div>
      </div>

      <!-- Dark panel -->
      <div class="rounded-2xl border p-5" style="background:#141311; border-color:#2C2A27">
        <p class="text-[11px] font-medium mb-3" style="color:#6E6B67">Dark mode</p>
        <div class="flex flex-wrap gap-3">
          <div v-for="s in STATUS" :key="s.name" class="flex flex-col gap-1.5 items-start">
            <!-- Filled pill — dark text for legibility on lighter dark-mode status colors -->
            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold"
                  :style="{ background: s.darkHex, color: '#141311' }">
              {{ s.name }}
            </span>
            <!-- Tinted bg + colored text -->
            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium"
                  :style="{ background: s.darkHex + '22', color: s.darkHex }">
              {{ s.name }}
            </span>
            <code class="text-[10px] font-mono opacity-60" style="color:#6E6B67">{{ s.darkHex }}</code>
          </div>
        </div>
      </div>

      <!-- Status token reference table -->
      <div class="mt-4 rounded-xl border border-border-subtle overflow-hidden">
        <table class="w-full text-xs">
          <thead>
            <tr class="border-b border-border-subtle bg-surface-sunken">
              <th class="text-left px-3 py-2 font-semibold text-ink-secondary">Token</th>
              <th class="text-left px-3 py-2 font-semibold text-ink-secondary">Light hex</th>
              <th class="text-left px-3 py-2 font-semibold text-ink-secondary">Dark hex</th>
              <th class="text-left px-3 py-2 font-semibold text-ink-secondary hidden sm:table-cell">Usage</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(s, i) in STATUS" :key="s.name"
                :class="i % 2 === 1 ? 'bg-surface-sunken' : 'bg-surface-raised'">
              <td class="px-3 py-2 font-mono text-ink-primary">{{ s.cssVar }}</td>
              <td class="px-3 py-2">
                <span class="inline-flex items-center gap-1.5">
                  <span class="w-3 h-3 rounded-full inline-block flex-shrink-0" :style="{ background: s.lightHex }"></span>
                  <code class="font-mono text-ink-secondary">{{ s.lightHex }}</code>
                </span>
              </td>
              <td class="px-3 py-2">
                <span class="inline-flex items-center gap-1.5">
                  <span class="w-3 h-3 rounded-full inline-block flex-shrink-0" :style="{ background: s.darkHex }"></span>
                  <code class="font-mono text-ink-secondary">{{ s.darkHex }}</code>
                </span>
              </td>
              <td class="px-3 py-2 text-ink-tertiary hidden sm:table-cell">{{ s.desc }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>

    <!-- ══════════════════════════════════════════════
         SECTION 4 — BRAND GOLD
         Single swatch — logo/marketing only
         ══════════════════════════════════════════════ -->
    <section class="mb-12">
      <h2 class="text-xs font-semibold uppercase tracking-widest text-ink-secondary mb-6">
        4 — Brand Gold
      </h2>

      <div class="flex items-start gap-4 rounded-2xl border border-border-subtle p-5 bg-surface-raised">
        <!-- Swatch -->
        <div class="w-16 h-16 rounded-xl flex-shrink-0 shadow-sm" style="background:#C4975A"></div>
        <!-- Info -->
        <div>
          <p class="font-semibold text-ink-primary">Muted Gold — <code class="font-mono text-sm">#C4975A</code></p>
          <p class="text-sm text-ink-secondary mt-1">
            <code class="font-mono text-xs">--brand-gold</code> · <code class="font-mono text-xs">bg-brand-gold</code>
          </p>
          <p class="text-sm text-ink-secondary mt-2 max-w-prose">
            Brand constant — same hex in both light and dark mode. Used for the logo mark, marketing moments, and
            focus rings. <strong class="text-ink-primary">Not the default button color in the new library</strong> —
            in-app emphasis uses category accent tokens instead. Gold is the brand signature, not a utility accent.
          </p>
        </div>
      </div>
    </section>

    <!-- ══════════════════════════════════════════════
         SECTION 5 — LEGACY PALETTE REFERENCE
         Collapsed note pointing to BRAND_GUIDE.md
         ══════════════════════════════════════════════ -->
    <section>
      <h2 class="text-xs font-semibold uppercase tracking-widest text-ink-secondary mb-4">
        5 — Legacy palette (migration reference)
      </h2>

      <details class="rounded-2xl border border-dashed border-border-strong">
        <summary class="px-5 py-4 cursor-pointer text-sm font-medium text-ink-secondary select-none hover:text-ink-primary transition-colors">
          Legacy tokens — still active during migration (click to expand)
        </summary>
        <div class="px-5 pb-5 pt-2 text-sm text-ink-secondary space-y-3">
          <p>
            The following token namespaces remain fully active. All existing app components use them.
            Do not remove or rename them until the full view-by-view refactor (Tier 6) is complete.
          </p>
          <div class="rounded-xl border border-border-subtle overflow-hidden">
            <table class="w-full text-xs">
              <thead>
                <tr class="bg-surface-sunken border-b border-border-subtle">
                  <th class="text-left px-3 py-2 font-semibold text-ink-secondary">Namespace</th>
                  <th class="text-left px-3 py-2 font-semibold text-ink-secondary">Scale</th>
                  <th class="text-left px-3 py-2 font-semibold text-ink-secondary hidden sm:table-cell">Usage</th>
                </tr>
              </thead>
              <tbody>
                <tr class="border-b border-border-subtle">
                  <td class="px-3 py-2 font-mono text-ink-primary">prussian-*</td>
                  <td class="px-3 py-2 text-ink-secondary">50–950</td>
                  <td class="px-3 py-2 text-ink-tertiary hidden sm:table-cell">Dark surfaces, sidebar, nav bg</td>
                </tr>
                <tr class="bg-surface-sunken border-b border-border-subtle">
                  <td class="px-3 py-2 font-mono text-ink-primary">wisteria-*</td>
                  <td class="px-3 py-2 text-ink-secondary">50–950</td>
                  <td class="px-3 py-2 text-ink-tertiary hidden sm:table-cell">Muted gold accent (current buttons)</td>
                </tr>
                <tr class="border-b border-border-subtle">
                  <td class="px-3 py-2 font-mono text-ink-primary">lavender-*</td>
                  <td class="px-3 py-2 text-ink-secondary">50–950</td>
                  <td class="px-3 py-2 text-ink-tertiary hidden sm:table-cell">Warm ivory/cream surfaces</td>
                </tr>
                <tr class="bg-surface-sunken border-b border-border-subtle">
                  <td class="px-3 py-2 font-mono text-ink-primary">sand-*</td>
                  <td class="px-3 py-2 text-ink-secondary">50–950</td>
                  <td class="px-3 py-2 text-ink-tertiary hidden sm:table-cell">Warm amber secondary accent</td>
                </tr>
                <tr class="border-b border-border-subtle">
                  <td class="px-3 py-2 font-mono text-ink-primary">ink-50…ink-950</td>
                  <td class="px-3 py-2 text-ink-secondary">50–950</td>
                  <td class="px-3 py-2 text-ink-tertiary hidden sm:table-cell">Near-black text scale</td>
                </tr>
                <tr class="bg-surface-sunken">
                  <td class="px-3 py-2 font-mono text-ink-primary">kin-*</td>
                  <td class="px-3 py-2 text-ink-secondary">hardcoded</td>
                  <td class="px-3 py-2 text-ink-tertiary hidden sm:table-cell">Brand palette: ivory, gold, success, error etc.</td>
                </tr>
              </tbody>
            </table>
          </div>
          <p class="text-ink-tertiary">
            Full reference: <code class="font-mono text-xs">docs/BRAND_GUIDE.md</code>
          </p>
        </div>
      </details>
    </section>

  </ComponentPage>
</template>
