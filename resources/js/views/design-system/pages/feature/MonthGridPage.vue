<script setup>
import { ref } from 'vue'
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import KinMonthGrid from '@/components/design-system/KinMonthGrid.vue'

const L = {
  surfaceApp:    '#FAF8F5',
  inkTertiary:   '#9C9895',
  borderSubtle:  '#E8E4DF',
}
const D = {
  surfaceApp:    '#141311',
  inkTertiary:   '#6E6B67',
  borderSubtle:  '#2C2A27',
}

// March 2026: starts Sunday, 31 days → 0 leading + 11 trailing = 42 cells (6 rows × 7).
const TODAY    = 14
const SELECTED = 18

const EVENTS = {
  3:  ['lavender'],
  5:  ['peach', 'mint'],
  8:  ['lavender', 'mint', 'peach', 'sun'],
  12: ['sun'],
  14: ['lavender', 'peach'],
  18: ['mint', 'lavender', 'peach'],
  22: ['sun'],
  25: ['lavender', 'mint', 'sun', 'peach', 'lavender', 'mint'],
  28: ['peach'],
}

function buildMonthCells() {
  const cells = []
  for (let d = 1; d <= 31; d++) cells.push({ day: d, month: 'current' })
  for (let d = 1; cells.length < 42; d++) cells.push({ day: d, month: 'trailing' })
  return cells
}
const CELLS = buildMonthCells()

const kinSelL      = ref(SELECTED)
const kinSelD      = ref(SELECTED)
const kinSelPillsL = ref(SELECTED)
const kinSelPillsD = ref(SELECTED)
</script>

<template>
  <ComponentPage
    title="5.5 MonthGrid"
    description="7-column month calendar grid. Each cell shows the date number with event indicators. Default 'dots' density shows up to 3 category-colored dots beneath the number; 'pills' density shows up to 2 truncated event titles stacked, for desktop / power-user contexts."
    status="chosen"
  >
    <section class="mb-16">
      <VariantFrame label="KinMonthGrid" caption="Click any current-month cell to select; leading/trailing month days are dimmed and disabled. Density toggle shows the same data as dots or as truncated event pills.">
        <div class="w-full space-y-10">

          <!-- LIGHT PANEL -->
          <div class="rounded-2xl border p-4 md:p-6"
               :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-[10px] font-semibold uppercase tracking-widest mb-4" :style="{ color: L.inkTertiary }">Light mode · dots density (default)</p>
            <KinMonthGrid :cells="CELLS" :events="EVENTS" :today="TODAY" v-model:selected="kinSelL" density="dots" />

            <p class="text-[10px] font-semibold uppercase tracking-widest mt-6 mb-4" :style="{ color: L.inkTertiary }">Light mode · pills density</p>
            <KinMonthGrid :cells="CELLS" :events="EVENTS" :today="TODAY" v-model:selected="kinSelPillsL" density="pills" />
          </div>

          <!-- DARK PANEL -->
          <div class="dark rounded-2xl border p-4 md:p-6"
               :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-[10px] font-semibold uppercase tracking-widest mb-4" :style="{ color: D.inkTertiary }">Dark mode · dots density (default)</p>
            <KinMonthGrid :cells="CELLS" :events="EVENTS" :today="TODAY" v-model:selected="kinSelD" density="dots" />

            <p class="text-[10px] font-semibold uppercase tracking-widest mt-6 mb-4" :style="{ color: D.inkTertiary }">Dark mode · pills density</p>
            <KinMonthGrid :cells="CELLS" :events="EVENTS" :today="TODAY" v-model:selected="kinSelPillsD" density="pills" />
          </div>

        </div>
      </VariantFrame>
    </section>
  </ComponentPage>
</template>
