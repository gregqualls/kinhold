<script setup>
import { ref } from 'vue'
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import KinStatTile from '@/components/design-system/KinStatTile.vue'

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

const RANGES = ['D', 'W', 'M', 'Y']
const rangePointsL = ref('W')
const rangePointsD = ref('W')
const rangeTasksL  = ref('M')
const rangeTasksD  = ref('M')
const rangeStreakL = ref('W')
const rangeStreakD = ref('W')

const chartPoints = [0.30, 0.45, 0.38, 0.60, 0.52, 0.75, 0.88]
const chartTasks  = [0.55, 0.62, 0.58, 0.70, 0.65, 0.80, 0.84]
const chartStreak = [0.10, 0.20, 0.30, 0.42, 0.55, 0.70, 0.85]
</script>

<template>
  <ComponentPage
    title="5.1 StatTile"
    description="The hero-number component. Every data-heavy surface in Kinhold — points bank, task completion, streak, meal progress, leaderboard — uses StatTile as its centrepiece. Hero number scales with both the card size (container queries) and the value's character count."
    status="chosen"
  >
    <section class="mb-16">
      <VariantFrame label="KinStatTile" caption="Every layer below the hero number is opt-in (label + range + delta + chart). Hero font size scales with character count for visual balance.">
        <div class="w-full space-y-10">

          <!-- LIGHT PANEL -->
          <div class="rounded-2xl border p-6 space-y-6"
               :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode — full (label + range + delta + hero + chart)</p>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
              <KinStatTile
                label="Points Earned" value="1,248" accent-color="sun"
                delta="+86" :delta-up="true"
                :chart-data="chartPoints" chart-type="line"
                :ranges="RANGES" v-model:range="rangePointsL"
              />
              <KinStatTile
                label="Tasks Done" value="84%" accent-color="mint"
                delta="+12%" :delta-up="true"
                :chart-data="chartTasks" chart-type="bars"
                :ranges="RANGES" v-model:range="rangeTasksL"
              />
              <KinStatTile
                label="Day Streak" value="23" accent-color="peach"
                delta="+3" :delta-up="true"
                :chart-data="chartStreak" chart-type="line"
                :ranges="RANGES" v-model:range="rangeStreakL"
              />
            </div>

            <p class="text-xs font-semibold uppercase tracking-widest pt-4" :style="{ color: L.inkTertiary }">Layers removed — label + hero only</p>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
              <KinStatTile label="Members" value="5" accent-color="lavender" />
              <KinStatTile label="This Week" value="$287" accent-color="mint" delta="-$12" :delta-up="false" />
              <KinStatTile label="Leader" value="Greg" accent-color="sun" />
            </div>
          </div>

          <!-- DARK PANEL -->
          <div class="dark rounded-2xl border p-6 space-y-6"
               :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode — full</p>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
              <KinStatTile
                label="Points Earned" value="1,248" accent-color="sun"
                delta="+86" :delta-up="true"
                :chart-data="chartPoints" chart-type="line"
                :ranges="RANGES" v-model:range="rangePointsD"
              />
              <KinStatTile
                label="Tasks Done" value="84%" accent-color="mint"
                delta="+12%" :delta-up="true"
                :chart-data="chartTasks" chart-type="bars"
                :ranges="RANGES" v-model:range="rangeTasksD"
              />
              <KinStatTile
                label="Day Streak" value="23" accent-color="peach"
                delta="+3" :delta-up="true"
                :chart-data="chartStreak" chart-type="line"
                :ranges="RANGES" v-model:range="rangeStreakD"
              />
            </div>

            <p class="text-xs font-semibold uppercase tracking-widest pt-4" :style="{ color: D.inkTertiary }">Layers removed — label + hero only</p>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
              <KinStatTile label="Members" value="5" accent-color="lavender" />
              <KinStatTile label="This Week" value="$287" accent-color="mint" delta="-$12" :delta-up="false" />
              <KinStatTile label="Leader" value="Greg" accent-color="sun" />
            </div>
          </div>

        </div>
      </VariantFrame>
    </section>
  </ComponentPage>
</template>
