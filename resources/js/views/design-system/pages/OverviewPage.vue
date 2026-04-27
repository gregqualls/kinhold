<script setup>
import { computed } from 'vue'
import ComponentPage from '../shared/ComponentPage.vue'
import { progressByTier, REGISTRY } from '../registry'
import { RouterLink } from 'vue-router'

const progress = computed(() => progressByTier())
const totals = computed(() => {
  const entries = REGISTRY.filter((e) => e.tier !== 'overview')
  const chosen = entries.filter((e) => e.chosen).length
  const scaffolded = entries.filter((e) => e.scaffolded && !e.chosen).length
  return { total: entries.length, chosen, scaffolded, remaining: entries.length - chosen - scaffolded }
})

const nextUp = computed(() => REGISTRY.find((e) => e.tier !== 'overview' && !e.chosen))
</script>

<template>
  <ComponentPage
    title="Design System"
    description="Workspace for Kinhold's redesign. Each component entry opens a page where variants (A/B/C) are built, compared side-by-side in light and dark mode, and promoted to the real library once a winner is chosen."
    status="scaffolded"
  >
    <section class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-10">
      <div class="rounded-2xl border border-kin-gray-200 dark:border-kin-gray-700 bg-white dark:bg-kin-surface-dark p-5">
        <p class="text-xs uppercase tracking-wider text-kin-gray-500 dark:text-kin-gray-400">Chosen</p>
        <p class="mt-2 text-4xl font-semibold tracking-tight text-emerald-600 dark:text-emerald-400">{{ totals.chosen }}</p>
        <p class="mt-1 text-xs text-kin-gray-500 dark:text-kin-gray-400">promoted to library</p>
      </div>
      <div class="rounded-2xl border border-kin-gray-200 dark:border-kin-gray-700 bg-white dark:bg-kin-surface-dark p-5">
        <p class="text-xs uppercase tracking-wider text-kin-gray-500 dark:text-kin-gray-400">In review</p>
        <p class="mt-2 text-4xl font-semibold tracking-tight text-blue-600 dark:text-blue-400">{{ totals.scaffolded }}</p>
        <p class="mt-1 text-xs text-kin-gray-500 dark:text-kin-gray-400">variants waiting for a decision</p>
      </div>
      <div class="rounded-2xl border border-kin-gray-200 dark:border-kin-gray-700 bg-white dark:bg-kin-surface-dark p-5">
        <p class="text-xs uppercase tracking-wider text-kin-gray-500 dark:text-kin-gray-400">Remaining</p>
        <p class="mt-2 text-4xl font-semibold tracking-tight text-kin-black dark:text-kin-off-white">{{ totals.remaining }}</p>
        <p class="mt-1 text-xs text-kin-gray-500 dark:text-kin-gray-400">of {{ totals.total }} total</p>
      </div>
    </section>

    <section class="rounded-2xl border border-kin-gray-200 dark:border-kin-gray-700 bg-white dark:bg-kin-surface-dark overflow-hidden mb-10">
      <header class="px-5 py-4 border-b border-kin-gray-100 dark:border-kin-gray-800">
        <p class="text-sm font-semibold">Tier progress</p>
      </header>
      <ul class="divide-y divide-kin-gray-100 dark:divide-kin-gray-800">
        <li v-for="tier in progress" :key="tier.id" class="px-5 py-3 flex items-center justify-between text-sm">
          <span class="text-kin-gray-700 dark:text-kin-gray-200">{{ tier.label }}</span>
          <span class="font-mono text-xs text-kin-gray-500 dark:text-kin-gray-400">{{ tier.chosen }}/{{ tier.total }} chosen · {{ tier.scaffolded }} in review</span>
        </li>
      </ul>
    </section>

    <section v-if="nextUp" class="rounded-2xl bg-kin-black text-white dark:bg-kin-off-white dark:text-kin-black p-6">
      <p class="text-xs uppercase tracking-wider opacity-60">Next up</p>
      <p class="mt-2 text-2xl font-semibold tracking-tight">{{ nextUp.title }}</p>
      <p class="mt-1 text-sm opacity-80 max-w-2xl">{{ nextUp.description }}</p>
      <RouterLink
        :to="`/design-system/${nextUp.slug}`"
        class="mt-5 inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white text-kin-black text-sm font-medium hover:bg-kin-gray-100 dark:bg-kin-black dark:text-white dark:hover:bg-kin-surface-dark"
      >Open page <span aria-hidden>→</span></RouterLink>
    </section>

    <section class="mt-10 rounded-2xl border border-dashed border-kin-gray-300 dark:border-kin-gray-700 p-6 text-sm leading-relaxed text-kin-gray-600 dark:text-kin-gray-300">
      <p class="font-semibold text-kin-black dark:text-kin-off-white mb-2">How this workspace works</p>
      <ol class="list-decimal pl-5 space-y-1">
        <li>Pick a component in the sidebar (work top-to-bottom through the tiers).</li>
        <li>A smaller-model session builds 2–3 variants inside that component's page.</li>
        <li>Greg opens the page and picks a winner. The chosen flag flips in <code class="font-mono text-xs">registry.js</code>.</li>
        <li>The winner gets promoted to a real Vue component in <code class="font-mono text-xs">resources/js/components/design-system/</code>.</li>
        <li>Once every entry is chosen, view-by-view refactors start with the dashboard.</li>
      </ol>
    </section>
  </ComponentPage>
</template>
