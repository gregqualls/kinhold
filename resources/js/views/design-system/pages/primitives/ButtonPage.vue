<script setup>
import { ref } from 'vue'
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import ModeSplit from '../../shared/ModeSplit.vue'
import ViewportToggle from '../../shared/ViewportToggle.vue'

// Placeholder variants. Smaller-model session will build real A/B/C buttons here
// per the brief: pill-shaped, with filled / outline / ghost / danger semantics,
// sm/md/lg sizes, and icon-only square variant.

const width = ref('full')
const widthValue = ref(null)

function setWidth(v) {
  width.value = v
  widthValue.value = v === 'full' ? null : Number(v)
}
</script>

<template>
  <ComponentPage
    title="1.1 Button"
    description="Pill-shaped buttons for every action. Variants should cover filled / outline / ghost / danger, sizes sm/md/lg, plus an icon-only square variant. Decision: which treatment feels premium without reading as bubble-y?"
    status="scaffolded"
  >
    <div class="flex items-center justify-between gap-3 mb-6">
      <p class="text-sm text-kin-gray-500 dark:text-kin-gray-400">Preview at</p>
      <ViewportToggle :model-value="width" @update:model-value="setWidth" />
    </div>

    <div class="space-y-6">
      <VariantFrame label="A" caption="Flat filled pill — no shadow, solid color">
        <ModeSplit :width="widthValue">
          <div class="flex flex-wrap items-center gap-3">
            <button type="button" class="px-5 py-2.5 rounded-full bg-kin-black text-white text-sm font-medium hover:bg-kin-gray-700 transition-colors">Primary</button>
            <button type="button" class="px-5 py-2.5 rounded-full border border-kin-gray-300 bg-transparent text-kin-black dark:text-kin-off-white text-sm font-medium hover:bg-kin-gray-100 dark:hover:bg-kin-gray-800 transition-colors">Secondary</button>
            <button type="button" class="px-5 py-2.5 rounded-full bg-transparent text-kin-gray-600 dark:text-kin-gray-300 text-sm font-medium hover:bg-kin-gray-100 dark:hover:bg-kin-gray-800 transition-colors">Ghost</button>
            <button type="button" class="px-5 py-2.5 rounded-full bg-kin-error text-white text-sm font-medium hover:opacity-90 transition-opacity">Danger</button>
          </div>
        </ModeSplit>
      </VariantFrame>

      <VariantFrame label="B" caption="Lifted filled pill — subtle gradient + resting shadow">
        <ModeSplit :width="widthValue">
          <div class="flex flex-wrap items-center gap-3">
            <button type="button" class="px-5 py-2.5 rounded-full text-white text-sm font-medium shadow-card hover:shadow-card-lg transition-shadow" style="background-image: linear-gradient(180deg, #2A2A2E 0%, #1C1C1E 100%);">Primary</button>
            <button type="button" class="px-5 py-2.5 rounded-full border border-kin-gray-200 dark:border-kin-gray-700 bg-white dark:bg-kin-surface-dark text-kin-black dark:text-kin-off-white text-sm font-medium shadow-card hover:shadow-card-lg transition-shadow">Secondary</button>
            <button type="button" class="px-5 py-2.5 rounded-full bg-transparent text-kin-gray-600 dark:text-kin-gray-300 text-sm font-medium hover:bg-kin-gray-100 dark:hover:bg-kin-gray-800 transition-colors">Ghost</button>
            <button type="button" class="px-5 py-2.5 rounded-full text-white text-sm font-medium shadow-card hover:shadow-card-lg transition-shadow" style="background-image: linear-gradient(180deg, #D87070 0%, #C45B5B 100%);">Danger</button>
          </div>
        </ModeSplit>
      </VariantFrame>

      <VariantFrame label="C" caption="Glassy pill — soft inner highlight, resting shadow">
        <ModeSplit :width="widthValue">
          <div class="flex flex-wrap items-center gap-3">
            <button type="button" class="relative px-5 py-2.5 rounded-full bg-kin-black text-white text-sm font-medium shadow-card overflow-hidden hover:shadow-card-lg transition-shadow">
              <span class="relative z-10">Primary</span>
              <span class="pointer-events-none absolute inset-x-1 top-0.5 h-1/2 rounded-full bg-white/10" aria-hidden />
            </button>
            <button type="button" class="relative px-5 py-2.5 rounded-full bg-white dark:bg-kin-surface-dark text-kin-black dark:text-kin-off-white text-sm font-medium border border-kin-gray-200 dark:border-kin-gray-700 shadow-card overflow-hidden hover:shadow-card-lg transition-shadow">
              <span class="relative z-10">Secondary</span>
              <span class="pointer-events-none absolute inset-x-1 top-0.5 h-1/2 rounded-full bg-white/40 dark:bg-white/5" aria-hidden />
            </button>
            <button type="button" class="px-5 py-2.5 rounded-full bg-transparent text-kin-gray-600 dark:text-kin-gray-300 text-sm font-medium hover:bg-kin-gray-100 dark:hover:bg-kin-gray-800 transition-colors">Ghost</button>
            <button type="button" class="relative px-5 py-2.5 rounded-full bg-kin-error text-white text-sm font-medium shadow-card overflow-hidden hover:shadow-card-lg transition-shadow">
              <span class="relative z-10">Danger</span>
              <span class="pointer-events-none absolute inset-x-1 top-0.5 h-1/2 rounded-full bg-white/20" aria-hidden />
            </button>
          </div>
        </ModeSplit>
      </VariantFrame>
    </div>

    <section class="mt-10 rounded-2xl border border-dashed border-kin-gray-300 dark:border-kin-gray-700 p-5 text-sm text-kin-gray-600 dark:text-kin-gray-300">
      <p class="font-semibold text-kin-black dark:text-kin-off-white mb-1">Next session TODOs</p>
      <ul class="list-disc pl-5 space-y-1">
        <li>Replace these starter variants with real candidates tuned to the final palette</li>
        <li>Add size variants (sm 32h / md 40h / lg 52h) inside the chosen variant once picked</li>
        <li>Add icon-left, icon-right, and icon-only square variants</li>
        <li>Add disabled + loading states</li>
        <li>Verify hover / focus / active accessibility</li>
      </ul>
    </section>
  </ComponentPage>
</template>
