<script setup>
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import {
  TrophyIcon, StarIcon, HeartIcon, FireIcon,
  AcademicCapIcon, QuestionMarkCircleIcon,
} from '@heroicons/vue/24/solid'
import KinAchievementTile from '@/components/design-system/KinAchievementTile.vue'

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
</script>

<template>
  <ComponentPage
    title="5.8 AchievementTile"
    description="Hexagonal achievement / badge tile. Four states: earned, in-progress (with arc ring), locked, hidden (mystery icon). Used on the badges page, in the activity feed, and surfaced as toast content when an achievement unlocks."
    status="chosen"
  >
    <section class="mb-16">
      <VariantFrame label="KinAchievementTile" caption="4 states × 4 accents · hexagonal polygon · arc ring shows progress when state='in-progress'.">
        <div class="w-full space-y-10">
          <!-- LIGHT PANEL -->
          <div class="rounded-2xl border p-6" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest mb-4" :style="{ color: L.inkTertiary }">Light mode</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
              <KinAchievementTile state="earned" :icon="TrophyIcon" accent-color="sun" title="Week Warrior" description="7 tasks in a row" meta="Earned 2d ago" />
              <KinAchievementTile state="earned" :icon="HeartIcon" accent-color="peach" title="Kindness Captain" description="Gave 10 kudos" meta="Earned 5d ago" />
              <KinAchievementTile state="in-progress" :icon="AcademicCapIcon" accent-color="mint" title="Meal Planner" description="Plan 20 meals" meta="12/20" :progress="0.65" />
              <KinAchievementTile state="in-progress" :icon="FireIcon" accent-color="peach" title="Streak Starter" description="3-day streak, 7 to go" meta="3/10 days" :progress="0.30" />
              <KinAchievementTile state="locked" :icon="StarIcon" accent-color="lavender" title="Century Club" description="Complete 100 tasks" meta="Locked" />
              <KinAchievementTile state="hidden" :icon="QuestionMarkCircleIcon" accent-color="lavender" description="Keep playing to unlock" meta="Hidden" />
            </div>
          </div>

          <!-- DARK PANEL -->
          <div class="dark rounded-2xl border p-6" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest mb-4" :style="{ color: D.inkTertiary }">Dark mode</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
              <KinAchievementTile state="earned" :icon="TrophyIcon" accent-color="sun" title="Week Warrior" description="7 tasks in a row" meta="Earned 2d ago" />
              <KinAchievementTile state="earned" :icon="HeartIcon" accent-color="peach" title="Kindness Captain" description="Gave 10 kudos" meta="Earned 5d ago" />
              <KinAchievementTile state="in-progress" :icon="AcademicCapIcon" accent-color="mint" title="Meal Planner" description="Plan 20 meals" meta="12/20" :progress="0.65" />
              <KinAchievementTile state="in-progress" :icon="FireIcon" accent-color="peach" title="Streak Starter" description="3-day streak, 7 to go" meta="3/10 days" :progress="0.30" />
              <KinAchievementTile state="locked" :icon="StarIcon" accent-color="lavender" title="Century Club" description="Complete 100 tasks" meta="Locked" />
              <KinAchievementTile state="hidden" :icon="QuestionMarkCircleIcon" accent-color="lavender" description="Keep playing to unlock" meta="Hidden" />
            </div>
          </div>
        </div>
      </VariantFrame>
    </section>
  </ComponentPage>
</template>
