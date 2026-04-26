<script setup>
import { ref } from 'vue'
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import KinWeekStrip from '@/components/design-system/KinWeekStrip.vue'

const kinSelectedL = ref(3)
const kinSelectedD = ref(3)
const kinSelectedL2 = ref(3)
const kinSelectedD2 = ref(3)

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

// Mon = index 0, Sun = index 6. Today = index 2 (Wed 14).
const WEEK = [
  { letter: 'Mon', num: 12, events: [] },
  { letter: 'Tue', num: 13, events: ['lavender'] },
  { letter: 'Wed', num: 14, events: ['peach', 'mint'] },
  { letter: 'Thu', num: 15, events: ['lavender'] },
  { letter: 'Fri', num: 16, events: ['sun', 'mint', 'lavender', 'peach'] },
  { letter: 'Sat', num: 17, events: ['mint'] },
  { letter: 'Sun', num: 18, events: [] },
]
const TODAY_INDEX = 2

const TWO_WEEKS = [
  ...WEEK,
  { letter: 'Mon', num: 19, events: ['sun'] },
  { letter: 'Tue', num: 20, events: [] },
  { letter: 'Wed', num: 21, events: ['peach', 'lavender'] },
  { letter: 'Thu', num: 22, events: [] },
  { letter: 'Fri', num: 23, events: ['mint', 'sun'] },
  { letter: 'Sat', num: 24, events: ['lavender', 'peach', 'mint'] },
  { letter: 'Sun', num: 25, events: [] },
]
</script>

<template>
  <ComponentPage
    title="5.4 WeekStrip"
    description="Horizontal day-of-week selector. Day-letter + date pill with event dots beneath. Today is anchored lavender-soft; selected fills lavender-bold; past days dim to 45% and are unreachable."
    status="chosen"
  >
    <section class="mb-16">
      <VariantFrame label="KinWeekStrip" caption="Click any non-past pill to select; past days are disabled. Up to 4 dots per day, 5+ collapses to +N.">
        <div class="w-full space-y-10">

          <!-- LIGHT PANEL -->
          <div class="rounded-2xl border p-6 space-y-6"
               :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode · 7 days</p>
            <KinWeekStrip :days="WEEK" :today-index="TODAY_INDEX" v-model:selected-index="kinSelectedL" />

            <p class="text-xs font-semibold uppercase tracking-widest pt-2" :style="{ color: L.inkTertiary }">Light mode · 14 days (two-week context)</p>
            <div class="overflow-x-auto">
              <KinWeekStrip :days="TWO_WEEKS" :today-index="TODAY_INDEX" v-model:selected-index="kinSelectedL2" />
            </div>
          </div>

          <!-- DARK PANEL -->
          <div class="dark rounded-2xl border p-6 space-y-6"
               :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode · 7 days</p>
            <KinWeekStrip :days="WEEK" :today-index="TODAY_INDEX" v-model:selected-index="kinSelectedD" />

            <p class="text-xs font-semibold uppercase tracking-widest pt-2" :style="{ color: D.inkTertiary }">Dark mode · 14 days</p>
            <div class="overflow-x-auto">
              <KinWeekStrip :days="TWO_WEEKS" :today-index="TODAY_INDEX" v-model:selected-index="kinSelectedD2" />
            </div>
          </div>

        </div>
      </VariantFrame>
    </section>
  </ComponentPage>
</template>

<style scoped>
.overflow-x-auto::-webkit-scrollbar {
  display: none;
}
</style>
