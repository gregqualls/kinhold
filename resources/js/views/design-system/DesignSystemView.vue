<script setup>
import { computed, defineAsyncComponent, onBeforeUnmount, onMounted, ref, watch } from 'vue'
import { useRoute, useRouter, RouterLink } from 'vue-router'
import { REGISTRY, TIERS, findBySlug, progressByTier } from './registry'
import { useDarkMode } from '@/composables/useDarkMode'

const route = useRoute()
const router = useRouter()

const activeSlug = computed(() => route.params.slug || 'overview')
const activeEntry = computed(() => findBySlug(activeSlug.value))

const sidebarOpen = ref(false)

// Dynamically load the component page for the active slug.
// If the page hasn't been scaffolded yet, we show a friendly placeholder.
const pageModules = import.meta.glob('./pages/**/*.vue')

const activePage = computed(() => {
  const entry = activeEntry.value
  if (!entry) return null
  const path = entry.pagePath.replace(/^\.\//, './')
  const loader = pageModules[path]
  if (!loader) return null
  return defineAsyncComponent(loader)
})

const groupedTiers = computed(() => TIERS.map((tier) => ({
  ...tier,
  entries: REGISTRY.filter((e) => e.tier === tier.id),
})))

const progress = computed(() => progressByTier())

watch(activeSlug, () => {
  sidebarOpen.value = false
})

// Design-system always shows light + dark side-by-side via ModeSplit, so the
// root theme is forced to light while this view is mounted. The user's global
// dark-mode preference (localStorage) is untouched — it reapplies on exit.
const { isDark } = useDarkMode()
const root = document.documentElement
onMounted(() => root.classList.remove('dark'))
onBeforeUnmount(() => {
  if (isDark.value) root.classList.add('dark')
})
</script>

<template>
  <div class="h-screen flex flex-col md:flex-row bg-kin-ivory dark:bg-kin-bg-dark text-kin-black dark:text-kin-off-white font-sans">
    <!-- Mobile header -->
    <header class="md:hidden flex items-center justify-between px-4 py-3 border-b border-kin-gray-200 dark:border-kin-gray-800 bg-white dark:bg-kin-surface-dark">
      <button
        type="button"
        class="p-2 -ml-2 text-kin-black dark:text-kin-off-white"
        aria-label="Toggle component list"
        @click="sidebarOpen = !sidebarOpen"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
      </button>
      <p class="text-sm font-semibold">Design System</p>
      <span class="w-5" aria-hidden="true"></span>
    </header>

    <!-- Sidebar -->
    <aside
      :class="[
        'md:w-72 md:flex-shrink-0 border-r border-kin-gray-200 dark:border-kin-gray-800 bg-white dark:bg-kin-surface-dark overflow-y-auto',
        sidebarOpen ? 'block' : 'hidden md:block',
      ]"
    >
      <div class="p-5 border-b border-kin-gray-200 dark:border-kin-gray-800">
        <RouterLink to="/design-system" class="block">
          <p class="text-xs uppercase tracking-wider text-kin-gray-500 dark:text-kin-gray-400">Kinhold</p>
          <p class="text-lg font-semibold tracking-tight">Design System</p>
        </RouterLink>
        <div class="mt-4">
          <a
            href="/docs/design/COMPONENT_ROADMAP.md"
            target="_blank"
            class="text-xs text-kin-gray-500 underline-offset-2 hover:underline"
          >Roadmap</a>
        </div>
      </div>

      <nav class="p-3 space-y-5">
        <section v-for="tier in groupedTiers" :key="tier.id">
          <p class="px-2 py-1 text-[11px] font-semibold uppercase tracking-wider text-kin-gray-500 dark:text-kin-gray-400">{{ tier.label }}</p>
          <ul class="mt-1 space-y-0.5">
            <li v-for="entry in tier.entries" :key="entry.slug">
              <RouterLink
                :to="entry.slug === 'overview' ? '/design-system' : `/design-system/${entry.slug}`"
                class="flex items-center justify-between gap-2 px-2 py-1.5 rounded-lg text-sm transition-colors"
                :class="activeSlug === entry.slug
                  ? 'bg-kin-gray-100 dark:bg-kin-gray-800 text-kin-black dark:text-kin-off-white font-medium'
                  : 'text-kin-gray-500 dark:text-kin-gray-300 hover:bg-kin-gray-50 dark:hover:bg-kin-gray-800/70 hover:text-kin-black dark:hover:text-kin-off-white'"
              >
                <span class="truncate">{{ entry.title }}</span>
                <span
                  v-if="entry.chosen"
                  class="w-1.5 h-1.5 rounded-full bg-emerald-500"
                  aria-label="Chosen"
                ></span>
                <span
                  v-else-if="entry.scaffolded"
                  class="w-1.5 h-1.5 rounded-full bg-blue-500"
                  aria-label="Scaffolded"
                ></span>
              </RouterLink>
            </li>
          </ul>
        </section>
      </nav>

      <footer class="p-5 border-t border-kin-gray-200 dark:border-kin-gray-800 text-xs text-kin-gray-500 dark:text-kin-gray-400 space-y-1">
        <p v-for="tier in progress" :key="tier.id">
          <span class="font-medium">{{ tier.label.replace(/^Tier \d+ — /, '') }}:</span>
          {{ tier.chosen }}/{{ tier.total }} chosen
        </p>
      </footer>
    </aside>

    <!-- Main area -->
    <main class="flex-1 overflow-y-auto">
      <component :is="activePage" v-if="activePage" />
      <div v-else class="p-10">
        <h1 class="text-2xl font-semibold mb-2">Component page not yet scaffolded</h1>
        <p class="text-kin-gray-500 dark:text-kin-gray-300">
          Create <code class="font-mono text-sm">resources/js/views/design-system{{ activeEntry?.pagePath.replace(/^\./, '') }}</code>
          to add variants for <strong>{{ activeEntry?.title }}</strong>.
        </p>
      </div>
    </main>
  </div>
</template>
