<script setup>
import { ref } from 'vue'
import ComponentPage from '../../shared/ComponentPage.vue'
import ViewportToggle from '../../shared/ViewportToggle.vue'

// ── Viewport state ───────────────────────────────────────────────────────────
const viewport = ref('full')

function containerStyle(vp) {
  if (vp === 'full') return {}
  return { maxWidth: vp + 'px', margin: '0 auto' }
}

// ── Type scale data ──────────────────────────────────────────────────────────
// clamp expressions and their semantic names for the reference table.
const SCALE = [
  {
    token:       '--text-hero',
    twClass:     'text-hero',
    clamp:       'clamp(4rem, 10vw, 11.25rem)',
    range:       '64–180px',
    lh:          '0.95',
    ls:          '−0.03em',
    fontClass:   'font-heading',
    fontName:    'Plus Jakarta Sans',
    usage:       'Hero numbers — points balance, big stat, daily step count',
  },
  {
    token:       '--text-display',
    twClass:     'text-display',
    clamp:       'clamp(2rem, 4vw, 3.5rem)',
    range:       '32–56px',
    lh:          '1.05',
    ls:          '−0.02em',
    fontClass:   'font-heading',
    fontName:    'Plus Jakarta Sans',
    usage:       'Module hero titles, empty-state headlines, marketing copy',
  },
  {
    token:       '--text-h1',
    twClass:     'text-h1',
    clamp:       'clamp(1.75rem, 2.6vw, 2.5rem)',
    range:       '28–40px',
    lh:          '1.15',
    ls:          '−0.015em',
    fontClass:   'font-heading',
    fontName:    'Plus Jakarta Sans',
    usage:       'Page titles, detail-view headers',
  },
  {
    token:       '--text-h2',
    twClass:     'text-h2',
    clamp:       'clamp(1.375rem, 2vw, 1.875rem)',
    range:       '22–30px',
    lh:          '1.2',
    ls:          '−0.01em',
    fontClass:   'font-heading',
    fontName:    'Plus Jakarta Sans',
    usage:       'Section headings, card titles on data-heavy pages',
  },
  {
    token:       '--text-h3',
    twClass:     'text-h3',
    clamp:       'clamp(1.125rem, 1.5vw, 1.375rem)',
    range:       '18–22px',
    lh:          '1.3',
    ls:          '−0.005em',
    fontClass:   'font-heading',
    fontName:    'Plus Jakarta Sans',
    usage:       'Card titles, list group headers, form section names',
  },
  {
    token:       '--text-h4',
    twClass:     'text-h4',
    clamp:       'clamp(1rem, 1.2vw, 1.125rem)',
    range:       '16–18px',
    lh:          '1.35',
    ls:          '0',
    fontClass:   'font-heading',
    fontName:    'Plus Jakarta Sans',
    usage:       'Sub-section labels, sidebar nav items, badge names',
  },
]

const BODY_SCALE = [
  {
    token:       '--text-body',
    twClass:     'text-body',
    clamp:       'clamp(0.9375rem, 1vw, 1rem)',
    range:       '15–16px',
    lh:          '1.55',
    ls:          '0',
  },
  {
    token:       '--text-body-sm',
    twClass:     'text-body-sm',
    clamp:       'clamp(0.8125rem, 0.9vw, 0.875rem)',
    range:       '13–14px',
    lh:          '1.5',
    ls:          '0',
  },
  {
    token:       '--text-caption',
    twClass:     'text-caption',
    clamp:       'clamp(0.6875rem, 0.8vw, 0.75rem)',
    range:       '11–12px',
    lh:          '1.4',
    ls:          '+0.02em',
  },
]
</script>

<template>
  <ComponentPage
    title="0.2 Typography scale"
    description="Fluid type scale via CSS clamp() — no fixed-breakpoint jumps. Each token scales continuously from mobile (375px) to large monitor (2000px). Headings pair with Plus Jakarta Sans; body with Inter; data/code with JetBrains Mono."
    status="chosen"
  >

    <!-- ── Viewport toggle ──────────────────────────────────────────────────── -->
    <div class="flex items-center justify-between mb-8">
      <p class="text-sm text-ink-secondary">
        Resize the viewport preview to see clamp() scale live.
      </p>
      <ViewportToggle v-model="viewport" />
    </div>

    <!-- ══════════════════════════════════════════════════════════════════════
         SECTION 1 — HERO NUMBER
         The "numbers are heroes" tenet — 96–180px, plus currency decimal demo
         ══════════════════════════════════════════════════════════════════════ -->
    <section class="mb-14">
      <h2 class="text-xs font-semibold uppercase tracking-widest text-ink-secondary mb-6">
        1 — Hero Number
      </h2>

      <!-- Light + dark panels side by side -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        <!-- Light panel -->
        <div class="rounded-2xl border border-kin-gray-200 bg-kin-ivory overflow-hidden">
          <header class="px-4 py-2 text-xs font-medium uppercase tracking-wider text-kin-gray-500 bg-kin-cream border-b border-kin-gray-200">
            Light
          </header>
          <div class="p-6 md:p-8 overflow-x-auto">
            <div :style="containerStyle(viewport)">
              <!-- Big integer -->
              <div class="mb-6">
                <div class="text-hero font-heading font-bold leading-none tracking-tight text-ink-primary select-none">
                  1,247
                </div>
                <p class="text-caption text-ink-tertiary mt-2 tracking-wide uppercase">
                  Points this week
                </p>
              </div>

              <!-- Currency — integer at full size, decimals at ~60% (text-display) -->
              <div class="flex items-baseline gap-0.5">
                <span class="text-display font-heading font-bold text-ink-primary">$24,891</span>
                <span
                  class="font-heading font-bold text-ink-secondary"
                  style="font-size: calc(var(--text-display) * 0.6); line-height: 1;"
                >.42</span>
              </div>
              <p class="text-caption text-ink-tertiary mt-1 tracking-wide uppercase">
                Family balance — integer full size, decimals 60%
              </p>
            </div>
          </div>
        </div>

        <!-- Dark panel -->
        <div class="rounded-2xl border border-kin-gray-700 bg-kin-bg-dark overflow-hidden dark">
          <header class="px-4 py-2 text-xs font-medium uppercase tracking-wider text-kin-gray-300 bg-kin-surface-dark border-b border-kin-gray-700">
            Dark
          </header>
          <div class="p-6 md:p-8 overflow-x-auto">
            <div :style="containerStyle(viewport)">
              <!-- Big integer -->
              <div class="mb-6">
                <div class="text-hero font-heading font-bold leading-none tracking-tight text-ink-primary select-none">
                  1,247
                </div>
                <p class="text-caption text-ink-tertiary mt-2 tracking-wide uppercase">
                  Points this week
                </p>
              </div>

              <!-- Currency demo -->
              <div class="flex items-baseline gap-0.5">
                <span class="text-display font-heading font-bold text-ink-primary">$24,891</span>
                <span
                  class="font-heading font-bold text-ink-secondary"
                  style="font-size: calc(var(--text-display) * 0.6); line-height: 1;"
                >.42</span>
              </div>
              <p class="text-caption text-ink-tertiary mt-1 tracking-wide uppercase">
                Family balance — integer full size, decimals 60%
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Tenet callout -->
      <div class="mt-4 rounded-xl border border-border-subtle bg-surface-sunken px-4 py-3 text-sm text-ink-secondary">
        <strong class="text-ink-primary">Tenet #5 — Numbers are heroes.</strong>
        On any data-heavy surface (points, earnings, streaks, completion, balance), the primary metric uses
        <code class="font-mono text-xs">text-hero</code> or <code class="font-mono text-xs">text-display</code>.
        For currency: integer at full size, decimals wrapped in a <code class="font-mono text-xs">&lt;span&gt;</code>
        at 60% of the parent size via <code class="font-mono text-xs">calc(var(--text-display) * 0.6)</code>.
      </div>
    </section>


    <!-- ══════════════════════════════════════════════════════════════════════
         SECTION 2 — DISPLAY + H1–H4 HEADINGS
         One example per level, stacked, with token + clamp annotation
         ══════════════════════════════════════════════════════════════════════ -->
    <section class="mb-14">
      <h2 class="text-xs font-semibold uppercase tracking-widest text-ink-secondary mb-6">
        2 — Display + Headings
      </h2>

      <!-- Light + dark panels -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        <div class="rounded-2xl border border-kin-gray-200 bg-kin-ivory overflow-hidden">
          <header class="px-4 py-2 text-xs font-medium uppercase tracking-wider text-kin-gray-500 bg-kin-cream border-b border-kin-gray-200">
            Light
          </header>
          <div class="p-6 md:p-8 space-y-6 overflow-x-auto">
            <div :style="containerStyle(viewport)">
              <div v-for="s in SCALE" :key="s.token" class="flex items-start justify-between gap-4 pb-5 border-b border-border-subtle last:border-0 last:pb-0">
                <div class="min-w-0 flex-1 overflow-hidden">
                  <div
                    :class="[s.twClass, s.fontClass, 'text-ink-primary font-semibold leading-none mb-1 truncate']"
                  >
                    {{ s.token === '--text-display' ? 'Your Week Ahead' :
                       s.token === '--text-h1'      ? 'Family Dashboard' :
                       s.token === '--text-h2'      ? 'This Week\'s Tasks' :
                       s.token === '--text-h3'      ? 'Medical Records' :
                                                      'Vault Entry' }}
                  </div>
                  <p class="text-caption text-ink-tertiary tracking-wide">{{ s.usage }}</p>
                </div>
                <div class="flex-shrink-0 text-right">
                  <code class="block text-caption font-mono text-ink-secondary">{{ s.twClass }}</code>
                  <code class="block text-caption font-mono text-ink-tertiary">{{ s.range }}</code>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="rounded-2xl border border-kin-gray-700 bg-kin-bg-dark overflow-hidden dark">
          <header class="px-4 py-2 text-xs font-medium uppercase tracking-wider text-kin-gray-300 bg-kin-surface-dark border-b border-kin-gray-700">
            Dark
          </header>
          <div class="p-6 md:p-8 space-y-6 overflow-x-auto">
            <div :style="containerStyle(viewport)">
              <div v-for="s in SCALE" :key="s.token" class="flex items-start justify-between gap-4 pb-5 border-b border-border-subtle last:border-0 last:pb-0">
                <div class="min-w-0 flex-1 overflow-hidden">
                  <div
                    :class="[s.twClass, s.fontClass, 'text-ink-primary font-semibold leading-none mb-1 truncate']"
                  >
                    {{ s.token === '--text-display' ? 'Your Week Ahead' :
                       s.token === '--text-h1'      ? 'Family Dashboard' :
                       s.token === '--text-h2'      ? 'This Week\'s Tasks' :
                       s.token === '--text-h3'      ? 'Medical Records' :
                                                      'Vault Entry' }}
                  </div>
                  <p class="text-caption text-ink-tertiary tracking-wide">{{ s.usage }}</p>
                </div>
                <div class="flex-shrink-0 text-right">
                  <code class="block text-caption font-mono text-ink-secondary">{{ s.twClass }}</code>
                  <code class="block text-caption font-mono text-ink-tertiary">{{ s.range }}</code>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Reference table -->
      <div class="mt-4 rounded-xl border border-border-subtle overflow-hidden">
        <table class="w-full text-xs">
          <thead>
            <tr class="bg-surface-sunken border-b border-border-subtle">
              <th class="text-left px-3 py-2 font-semibold text-ink-secondary">Token</th>
              <th class="text-left px-3 py-2 font-semibold text-ink-secondary hidden sm:table-cell">clamp() expression</th>
              <th class="text-left px-3 py-2 font-semibold text-ink-secondary">Range</th>
              <th class="text-left px-3 py-2 font-semibold text-ink-secondary">LH</th>
              <th class="text-left px-3 py-2 font-semibold text-ink-secondary">LS</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(s, i) in SCALE"
              :key="s.token"
              :class="i % 2 === 1 ? 'bg-surface-sunken' : 'bg-surface-raised'"
            >
              <td class="px-3 py-2 font-mono text-ink-primary whitespace-nowrap">{{ s.twClass }}</td>
              <td class="px-3 py-2 font-mono text-ink-tertiary hidden sm:table-cell text-[11px]">{{ s.clamp }}</td>
              <td class="px-3 py-2 text-ink-secondary whitespace-nowrap">{{ s.range }}</td>
              <td class="px-3 py-2 text-ink-secondary">{{ s.lh }}</td>
              <td class="px-3 py-2 text-ink-secondary">{{ s.ls }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>


    <!-- ══════════════════════════════════════════════════════════════════════
         SECTION 3 — BODY SCALE
         body / body-sm / caption — purposeful sample text
         ══════════════════════════════════════════════════════════════════════ -->
    <section class="mb-14">
      <h2 class="text-xs font-semibold uppercase tracking-widest text-ink-secondary mb-6">
        3 — Body Scale
      </h2>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        <div class="rounded-2xl border border-kin-gray-200 bg-kin-ivory overflow-hidden">
          <header class="px-4 py-2 text-xs font-medium uppercase tracking-wider text-kin-gray-500 bg-kin-cream border-b border-kin-gray-200">
            Light
          </header>
          <div class="p-6 md:p-8 overflow-x-auto">
            <div :style="containerStyle(viewport)" class="space-y-5">

              <!-- body -->
              <div>
                <p class="text-body text-ink-primary leading-relaxed">
                  The emergency binder lives in the hall closet, top shelf. It has insurance cards,
                  passports, and the contact list for school. If you ever need to reach the kids' doctor,
                  the number is saved under "Dr. Patel" in the vault's Medical section.
                </p>
                <div class="flex items-center gap-3 mt-2">
                  <code class="text-caption font-mono text-ink-tertiary">text-body</code>
                  <span class="text-caption text-ink-tertiary">15–16px · lh 1.55</span>
                </div>
              </div>

              <div class="h-px bg-border-subtle" />

              <!-- body-sm -->
              <div>
                <p class="text-body-sm text-ink-secondary leading-relaxed">
                  Last updated by Greg on Apr 14. 3 sensitive fields hidden. Tap to reveal.
                  This entry is shared with Emma and Alex — parents only can edit.
                </p>
                <div class="flex items-center gap-3 mt-2">
                  <code class="text-caption font-mono text-ink-tertiary">text-body-sm</code>
                  <span class="text-caption text-ink-tertiary">13–14px · lh 1.5</span>
                </div>
              </div>

              <div class="h-px bg-border-subtle" />

              <!-- caption -->
              <div>
                <p class="text-caption text-ink-tertiary tracking-wide uppercase">
                  Vault · Medical · Updated 2 days ago
                </p>
                <div class="flex items-center gap-3 mt-2">
                  <code class="text-caption font-mono text-ink-tertiary">text-caption</code>
                  <span class="text-caption text-ink-tertiary">11–12px · lh 1.4 · +0.02em</span>
                </div>
              </div>

            </div>
          </div>
        </div>

        <div class="rounded-2xl border border-kin-gray-700 bg-kin-bg-dark overflow-hidden dark">
          <header class="px-4 py-2 text-xs font-medium uppercase tracking-wider text-kin-gray-300 bg-kin-surface-dark border-b border-kin-gray-700">
            Dark
          </header>
          <div class="p-6 md:p-8 overflow-x-auto">
            <div :style="containerStyle(viewport)" class="space-y-5">

              <div>
                <p class="text-body text-ink-primary leading-relaxed">
                  The emergency binder lives in the hall closet, top shelf. It has insurance cards,
                  passports, and the contact list for school. If you ever need to reach the kids' doctor,
                  the number is saved under "Dr. Patel" in the vault's Medical section.
                </p>
                <div class="flex items-center gap-3 mt-2">
                  <code class="text-caption font-mono text-ink-tertiary">text-body</code>
                  <span class="text-caption text-ink-tertiary">15–16px · lh 1.55</span>
                </div>
              </div>

              <div class="h-px bg-border-subtle" />

              <div>
                <p class="text-body-sm text-ink-secondary leading-relaxed">
                  Last updated by Greg on Apr 14. 3 sensitive fields hidden. Tap to reveal.
                  This entry is shared with Emma and Alex — parents only can edit.
                </p>
                <div class="flex items-center gap-3 mt-2">
                  <code class="text-caption font-mono text-ink-tertiary">text-body-sm</code>
                  <span class="text-caption text-ink-tertiary">13–14px · lh 1.5</span>
                </div>
              </div>

              <div class="h-px bg-border-subtle" />

              <div>
                <p class="text-caption text-ink-tertiary tracking-wide uppercase">
                  Vault · Medical · Updated 2 days ago
                </p>
                <div class="flex items-center gap-3 mt-2">
                  <code class="text-caption font-mono text-ink-tertiary">text-caption</code>
                  <span class="text-caption text-ink-tertiary">11–12px · lh 1.4 · +0.02em</span>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>
    </section>


    <!-- ══════════════════════════════════════════════════════════════════════
         SECTION 4 — MONO
         Data values and code snippets
         ══════════════════════════════════════════════════════════════════════ -->
    <section class="mb-14">
      <h2 class="text-xs font-semibold uppercase tracking-widest text-ink-secondary mb-6">
        4 — Monospaced (data &amp; code)
      </h2>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        <div class="rounded-2xl border border-kin-gray-200 bg-kin-ivory overflow-hidden">
          <header class="px-4 py-2 text-xs font-medium uppercase tracking-wider text-kin-gray-500 bg-kin-cream border-b border-kin-gray-200">
            Light
          </header>
          <div class="p-6 md:p-8 overflow-x-auto">
            <div :style="containerStyle(viewport)" class="space-y-4">
              <!-- Large data value -->
              <div>
                <span class="text-h2 font-mono text-ink-primary">$24,891.42</span>
                <p class="text-caption text-ink-tertiary mt-1">
                  <code class="font-mono">text-h2 font-mono</code> — data value, financial display
                </p>
              </div>

              <div class="h-px bg-border-subtle" />

              <!-- Code snippet block -->
              <div class="rounded-xl bg-surface-sunken border border-border-subtle px-4 py-3">
                <p class="text-mono font-mono text-ink-primary">
                  <span class="text-ink-tertiary">--text-hero:</span> clamp(4rem, 10vw, 11.25rem);
                </p>
                <p class="text-mono font-mono text-ink-primary">
                  <span class="text-ink-tertiary">--text-display:</span> clamp(2rem, 4vw, 3.5rem);
                </p>
                <p class="text-mono font-mono text-ink-secondary">
                  <span class="text-ink-tertiary">--text-body:</span>    clamp(0.9375rem, 1vw, 1rem);
                </p>
              </div>
              <code class="text-caption font-mono text-ink-tertiary">
                text-mono font-mono · 13–15px · lh 1.5
              </code>
            </div>
          </div>
        </div>

        <div class="rounded-2xl border border-kin-gray-700 bg-kin-bg-dark overflow-hidden dark">
          <header class="px-4 py-2 text-xs font-medium uppercase tracking-wider text-kin-gray-300 bg-kin-surface-dark border-b border-kin-gray-700">
            Dark
          </header>
          <div class="p-6 md:p-8 overflow-x-auto">
            <div :style="containerStyle(viewport)" class="space-y-4">
              <div>
                <span class="text-h2 font-mono text-ink-primary">$24,891.42</span>
                <p class="text-caption text-ink-tertiary mt-1">
                  <code class="font-mono">text-h2 font-mono</code> — data value, financial display
                </p>
              </div>

              <div class="h-px bg-border-subtle" />

              <div class="rounded-xl bg-surface-sunken border border-border-subtle px-4 py-3">
                <p class="text-mono font-mono text-ink-primary">
                  <span class="text-ink-tertiary">--text-hero:</span> clamp(4rem, 10vw, 11.25rem);
                </p>
                <p class="text-mono font-mono text-ink-primary">
                  <span class="text-ink-tertiary">--text-display:</span> clamp(2rem, 4vw, 3.5rem);
                </p>
                <p class="text-mono font-mono text-ink-secondary">
                  <span class="text-ink-tertiary">--text-body:</span>    clamp(0.9375rem, 1vw, 1rem);
                </p>
              </div>
              <code class="text-caption font-mono text-ink-tertiary">
                text-mono font-mono · 13–15px · lh 1.5
              </code>
            </div>
          </div>
        </div>
      </div>
    </section>


    <!-- ══════════════════════════════════════════════════════════════════════
         SECTION 5 — FONT FAMILY LOCKUP
         Three blocks showing each typeface at a representative size
         ══════════════════════════════════════════════════════════════════════ -->
    <section class="mb-14">
      <h2 class="text-xs font-semibold uppercase tracking-widest text-ink-secondary mb-6">
        5 — Font family lockup
      </h2>

      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

        <!-- Plus Jakarta Sans — heading -->
        <div class="rounded-2xl border border-border-subtle bg-surface-raised p-6 flex flex-col gap-3">
          <div class="text-h2 font-heading font-bold text-ink-primary leading-tight">
            Ag
          </div>
          <div>
            <p class="text-h4 font-heading font-semibold text-ink-primary">Plus Jakarta Sans</p>
            <p class="text-body-sm text-ink-secondary mt-0.5">Headings · Display · Hero numbers</p>
          </div>
          <div class="h-px bg-border-subtle" />
          <p class="text-body-sm text-ink-tertiary">
            Use with <code class="font-mono text-caption">font-heading</code>.
            Pair with <code class="font-mono text-caption">text-hero</code> through
            <code class="font-mono text-caption">text-h4</code>. Tight negative tracking
            at large sizes; neutral at body scale.
          </p>
        </div>

        <!-- Inter — body -->
        <div class="rounded-2xl border border-border-subtle bg-surface-raised p-6 flex flex-col gap-3">
          <div class="text-h2 font-sans font-normal text-ink-primary leading-tight">
            Ag
          </div>
          <div>
            <p class="text-h4 font-sans font-semibold text-ink-primary">Inter</p>
            <p class="text-body-sm text-ink-secondary mt-0.5">Body · Labels · UI text</p>
          </div>
          <div class="h-px bg-border-subtle" />
          <p class="text-body-sm text-ink-tertiary">
            Use with <code class="font-mono text-caption">font-sans</code> (default).
            Pair with <code class="font-mono text-caption">text-body</code>,
            <code class="font-mono text-caption">text-body-sm</code>,
            <code class="font-mono text-caption">text-caption</code>.
            Designed for maximum legibility at reading sizes; no manual tracking needed.
          </p>
        </div>

        <!-- JetBrains Mono — data -->
        <div class="rounded-2xl border border-border-subtle bg-surface-raised p-6 flex flex-col gap-3">
          <div class="text-h2 font-mono font-normal text-ink-primary leading-tight">
            01
          </div>
          <div>
            <p class="text-h4 font-mono font-semibold text-ink-primary">JetBrains Mono</p>
            <p class="text-body-sm text-ink-secondary mt-0.5">Code · Data values · Amounts</p>
          </div>
          <div class="h-px bg-border-subtle" />
          <p class="text-body-sm text-ink-tertiary">
            Use with <code class="font-mono text-caption">font-mono</code>.
            Pair with <code class="font-mono text-caption">text-mono</code> or any size for
            financial figures, token names, and code snippets. Tabular-nums variant
            keeps columns aligned.
          </p>
        </div>

      </div>
    </section>


    <!-- ══════════════════════════════════════════════════════════════════════
         FULL SCALE REFERENCE TABLE
         All 10 tokens in one scannable table
         ══════════════════════════════════════════════════════════════════════ -->
    <section>
      <h2 class="text-xs font-semibold uppercase tracking-widest text-ink-secondary mb-6">
        Full scale reference
      </h2>

      <div class="rounded-xl border border-border-subtle overflow-hidden">
        <table class="w-full text-xs">
          <thead>
            <tr class="bg-surface-sunken border-b border-border-subtle">
              <th class="text-left px-3 py-2 font-semibold text-ink-secondary">Utility</th>
              <th class="text-left px-3 py-2 font-semibold text-ink-secondary hidden md:table-cell">CSS var</th>
              <th class="text-left px-3 py-2 font-semibold text-ink-secondary hidden sm:table-cell">clamp()</th>
              <th class="text-left px-3 py-2 font-semibold text-ink-secondary">Range</th>
              <th class="text-left px-3 py-2 font-semibold text-ink-secondary">LH</th>
              <th class="text-left px-3 py-2 font-semibold text-ink-secondary hidden sm:table-cell">Tracking</th>
              <th class="text-left px-3 py-2 font-semibold text-ink-secondary hidden lg:table-cell">Font</th>
            </tr>
          </thead>
          <tbody>
            <template v-for="(s, i) in [...SCALE, ...BODY_SCALE.map(b => ({ ...b, fontClass: 'font-sans', fontName: 'Inter' })), { token: '--text-mono', twClass: 'text-mono', clamp: 'clamp(0.8125rem, 0.9vw, 0.9375rem)', range: '13–15px', lh: '1.5', ls: '0', fontClass: 'font-mono', fontName: 'JetBrains Mono' }]" :key="s.token">
              <tr :class="i % 2 === 1 ? 'bg-surface-sunken' : 'bg-surface-raised'">
                <td class="px-3 py-2">
                  <code class="font-mono text-ink-primary">{{ s.twClass }}</code>
                </td>
                <td class="px-3 py-2 hidden md:table-cell">
                  <code class="font-mono text-ink-tertiary">{{ s.token }}</code>
                </td>
                <td class="px-3 py-2 font-mono text-ink-tertiary text-[11px] hidden sm:table-cell whitespace-nowrap">
                  {{ s.clamp }}
                </td>
                <td class="px-3 py-2 text-ink-secondary whitespace-nowrap">{{ s.range }}</td>
                <td class="px-3 py-2 text-ink-secondary">{{ s.lh }}</td>
                <td class="px-3 py-2 text-ink-secondary hidden sm:table-cell">{{ s.ls }}</td>
                <td class="px-3 py-2 text-ink-tertiary hidden lg:table-cell">
                  <code class="font-mono text-[11px]">{{ s.fontClass }}</code>
                </td>
              </tr>
            </template>
          </tbody>
        </table>
      </div>

      <p class="mt-4 text-body-sm text-ink-tertiary">
        Tenet #10 — No fixed-breakpoint jumps. Each token scales continuously via
        <code class="font-mono text-caption">clamp(min, preferred-vw, max)</code>.
        The preferred value uses viewport-width units so the step between mobile and desktop is smooth.
        Use the viewport toggle above to verify no awkward jump at any of the five test widths: 320 / 500 / 980 / 1440 / 2000px.
      </p>
    </section>

  </ComponentPage>
</template>
