<script setup>
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import KinHeroMetricCard from '@/components/design-system/KinHeroMetricCard.vue'

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

const PHOTO_FAMILY_DINNER = 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=800'
</script>

<template>
  <ComponentPage
    title="5.2 HeroMetricCard"
    description="The flagship card at the top of module landing pages and the dashboard. Three variants — iridescent gradient, warm gradient, edge-to-edge photo — chosen by context at the callsite."
    status="chosen"
  >
    <section class="mb-16">
      <VariantFrame label="KinHeroMetricCard" caption="Iridescent (calm summary), warm (streak / urgency), photo (with two-layer scrim). Hero number is container-query scaled.">
        <div class="w-full space-y-10">

          <!-- LIGHT PANEL -->
          <div class="rounded-2xl border p-6 space-y-6"
               :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>

            <div class="space-y-4">
              <p class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: L.inkTertiary }">Iridescent · calm summary</p>
              <KinHeroMetricCard
                variant="iridescent"
                label="Today's score"
                value="64"
                delta="+12"
                cta-label="View"
              />
            </div>

            <div class="space-y-4">
              <p class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: L.inkTertiary }">Warm · streak / urgency</p>
              <KinHeroMetricCard
                variant="warm"
                label="Day streak"
                value="23 days"
                delta="+1 today"
                cta-label="Keep going"
              />
            </div>

            <div class="space-y-4">
              <p class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: L.inkTertiary }">Photo · two-layer scrim</p>
              <KinHeroMetricCard
                variant="photo"
                :photo="PHOTO_FAMILY_DINNER"
                photo-alt="Family dinner"
                label="Tonight's plan"
                value="6 PM"
                delta="3 of 5 confirmed"
                cta-label="Details"
                min-height="260px"
              />
            </div>
          </div>

          <!-- DARK PANEL -->
          <div class="dark rounded-2xl border p-6 space-y-6"
               :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

            <div class="space-y-4">
              <p class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: D.inkTertiary }">Iridescent · calm summary</p>
              <KinHeroMetricCard
                variant="iridescent"
                label="Today's score"
                value="64"
                delta="+12"
                cta-label="View"
              />
            </div>

            <div class="space-y-4">
              <p class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: D.inkTertiary }">Warm · streak / urgency</p>
              <KinHeroMetricCard
                variant="warm"
                label="Day streak"
                value="23 days"
                delta="+1 today"
                cta-label="Keep going"
              />
            </div>

            <div class="space-y-4">
              <p class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: D.inkTertiary }">Photo · two-layer scrim</p>
              <KinHeroMetricCard
                variant="photo"
                :photo="PHOTO_FAMILY_DINNER"
                photo-alt="Family dinner"
                label="Tonight's plan"
                value="6 PM"
                delta="3 of 5 confirmed"
                cta-label="Details"
                min-height="260px"
              />
            </div>
          </div>

        </div>
      </VariantFrame>
    </section>
  </ComponentPage>
</template>
