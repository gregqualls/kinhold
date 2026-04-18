<script setup>
import ComponentPage from '../../shared/ComponentPage.vue'

// Pulled from docs/BRAND_GUIDE.md. These are the CURRENT brand colors.
// When 0.1 Color Tokens is finalized, update this file to reflect the new palette.

const LIGHT_NEUTRAL = [
  { name: 'Background', token: '--kin-ivory', hex: '#FAF8F5', usage: 'Page bg, app shell' },
  { name: 'Surface', token: '--kin-white', hex: '#FFFFFF', usage: 'Cards, modals' },
  { name: 'Surface Alt', token: '--kin-cream', hex: '#F5F2EE', usage: 'Secondary surfaces' },
  { name: 'Border', token: '--kin-border', hex: '#E8E4DF', usage: 'Dividers, card borders' },
  { name: 'Text Primary', token: '--kin-black', hex: '#1C1C1E', usage: 'Headings, body' },
  { name: 'Text Secondary', token: '--kin-gray-500', hex: '#6B6966', usage: 'Labels, captions' },
  { name: 'Text Tertiary', token: '--kin-gray-400', hex: '#9C9895', usage: 'Placeholders' },
]

const DARK_NEUTRAL = [
  { name: 'Background', token: '--kin-bg-dark', hex: '#121214', usage: 'Page bg, app shell' },
  { name: 'Surface', token: '--kin-surface-dark', hex: '#1C1C20', usage: 'Cards, modals' },
  { name: 'Surface Alt', token: '--kin-surface-dark-alt', hex: '#252528', usage: 'Secondary surfaces' },
  { name: 'Border', token: '--kin-border-dark', hex: '#2E2E32', usage: 'Dividers, card borders' },
  { name: 'Text Primary', token: '--kin-off-white', hex: '#F0EDE9', usage: 'Headings, body' },
  { name: 'Text Secondary', token: '', hex: '#9C9895', usage: 'Labels, captions' },
  { name: 'Text Tertiary', token: '', hex: '#6B6966', usage: 'Placeholders' },
]

const ACCENTS = [
  { name: 'Gold', token: '--kin-gold', hex: '#C4975A', usage: 'Brand signature' },
  { name: 'Success', token: '--kin-success', hex: '#5B8C6A', usage: 'Completions' },
  { name: 'Warning', token: '--kin-warning', hex: '#C48B3F', usage: 'Alerts' },
  { name: 'Error', token: '--kin-error', hex: '#C45B5B', usage: 'Errors' },
  { name: 'Info', token: '--kin-info', hex: '#5B7B9C', usage: 'Info, links' },
]

const FAMILY = [
  { name: 'Teal', hex: '#5B8C9C' },
  { name: 'Lavender', hex: '#7B6B9C' },
  { name: 'Rose', hex: '#9C5B5B' },
  { name: 'Sage', hex: '#5B9C7B' },
  { name: 'Amber', hex: '#C48B5B' },
  { name: 'Steel', hex: '#5B6B9C' },
  { name: 'Sienna', hex: '#9C7B5B' },
  { name: 'Plum', hex: '#8B5B9C' },
  { name: 'Cyan', hex: '#5B9C9C' },
  { name: 'Olive', hex: '#9C8B5B' },
  { name: 'Berry', hex: '#9C5B7B' },
  { name: 'Forest', hex: '#6B8B5B' },
]
</script>

<template>
  <ComponentPage
    title="0.1 Color Tokens"
    description="Current brand palette (from docs/BRAND_GUIDE.md). This reference gets updated once the final redesign palette lands — category pastel accents and refined dark-mode tokens will be added here."
    status="scaffolded"
  >
    <section class="mb-10">
      <h2 class="text-sm font-semibold uppercase tracking-wider text-kin-gray-500 dark:text-kin-gray-400 mb-4">Neutral · Light mode</h2>
      <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3">
        <div v-for="c in LIGHT_NEUTRAL" :key="c.hex" class="rounded-xl overflow-hidden border border-kin-gray-200 dark:border-kin-gray-700">
          <div class="aspect-square" :style="{ background: c.hex }" />
          <div class="p-3 text-xs">
            <p class="font-semibold">{{ c.name }}</p>
            <p class="font-mono text-[10px] opacity-60 mt-0.5">{{ c.hex }}</p>
            <p class="mt-1 text-kin-gray-500 dark:text-kin-gray-400 leading-tight">{{ c.usage }}</p>
          </div>
        </div>
      </div>
    </section>

    <section class="mb-10">
      <h2 class="text-sm font-semibold uppercase tracking-wider text-kin-gray-500 dark:text-kin-gray-400 mb-4">Neutral · Dark mode</h2>
      <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3">
        <div v-for="c in DARK_NEUTRAL" :key="c.hex" class="rounded-xl overflow-hidden border border-kin-gray-200 dark:border-kin-gray-700">
          <div class="aspect-square" :style="{ background: c.hex }" />
          <div class="p-3 text-xs">
            <p class="font-semibold">{{ c.name }}</p>
            <p class="font-mono text-[10px] opacity-60 mt-0.5">{{ c.hex }}</p>
            <p class="mt-1 text-kin-gray-500 dark:text-kin-gray-400 leading-tight">{{ c.usage }}</p>
          </div>
        </div>
      </div>
    </section>

    <section class="mb-10">
      <h2 class="text-sm font-semibold uppercase tracking-wider text-kin-gray-500 dark:text-kin-gray-400 mb-4">Accents &amp; status</h2>
      <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
        <div v-for="c in ACCENTS" :key="c.hex" class="rounded-xl overflow-hidden border border-kin-gray-200 dark:border-kin-gray-700">
          <div class="aspect-square" :style="{ background: c.hex }" />
          <div class="p-3 text-xs">
            <p class="font-semibold">{{ c.name }}</p>
            <p class="font-mono text-[10px] opacity-60 mt-0.5">{{ c.hex }}</p>
            <p class="mt-1 text-kin-gray-500 dark:text-kin-gray-400 leading-tight">{{ c.usage }}</p>
          </div>
        </div>
      </div>
    </section>

    <section>
      <h2 class="text-sm font-semibold uppercase tracking-wider text-kin-gray-500 dark:text-kin-gray-400 mb-4">Family member palette</h2>
      <div class="grid grid-cols-3 md:grid-cols-6 lg:grid-cols-12 gap-2">
        <div v-for="c in FAMILY" :key="c.hex" class="flex flex-col items-center gap-1">
          <div class="w-12 h-12 rounded-full border-2 border-white dark:border-kin-gray-800 shadow-sm" :style="{ background: c.hex }" />
          <p class="text-[11px] text-center">{{ c.name }}</p>
        </div>
      </div>
    </section>

    <section class="mt-10 rounded-2xl border border-dashed border-kin-gray-300 dark:border-kin-gray-700 p-5 text-sm text-kin-gray-600 dark:text-kin-gray-300">
      <p class="font-semibold text-kin-black dark:text-kin-off-white mb-1">Redesign note</p>
      <p>
        The brief calls for adding <strong>category pastel accents</strong> (lavender, peach, mint, soft yellow)
        alongside this neutral base. When Tier 0.1 is finalized, extend this page with the new accent tokens,
        confirm each one passes WCAG AA against both light and dark surfaces, and update
        <code class="font-mono text-xs">tailwind.config.js</code> + <code class="font-mono text-xs">tokens.css</code>.
      </p>
    </section>
  </ComponentPage>
</template>
