<script setup>
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import KinDayHeader from '@/components/design-system/KinDayHeader.vue'

const L = {
  surfaceApp:    '#FAF8F5',
  surfaceRaised: '#FFFFFF',
  inkTertiary:   '#9C9895',
  borderSubtle:  '#E8E4DF',
}
const D = {
  surfaceApp:    '#141311',
  surfaceRaised: '#1C1B19',
  inkTertiary:   '#6E6B67',
  borderSubtle:  '#2C2A27',
}
</script>

<template>
  <ComponentPage
    title="5.6 DayHeader"
    description="Editorial-scale day-of-month number + tiny day-of-week tag. The day number is the hero — it commands the page and anchors temporal orientation. Used on day view, focus-mode today screen, and meal-plan-day detail."
    status="chosen"
  >
    <section class="mb-16">
      <VariantFrame label="KinDayHeader" caption="Hero number left · thin rule · weekday + month stacked right · optional event-count below-right. sm / md / lg sizes; isToday highlights the number.">
        <div class="w-full space-y-10">
          <!-- LIGHT PANEL -->
          <div class="rounded-2xl border p-8 space-y-10" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>
            <KinDayHeader :day="14" weekday="Friday" month="March 2026" :event-count="3" />
            <KinDayHeader :day="21" weekday="Saturday" month="March 2026" />
            <KinDayHeader :day="14" weekday="Friday" month="March 2026" :event-count="3" :is-today="true" />
            <div class="max-w-[375px] rounded-xl border p-4" :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle }">
              <KinDayHeader :day="14" weekday="Friday" month="March 2026" :event-count="3" size="sm" />
            </div>
            <div class="rounded-xl border p-8 max-w-[640px]" :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle }">
              <KinDayHeader :day="14" weekday="Friday" month="March 2026" :event-count="3" size="lg" />
            </div>
          </div>

          <!-- DARK PANEL -->
          <div class="dark rounded-2xl border p-8 space-y-10" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>
            <KinDayHeader :day="14" weekday="Friday" month="March 2026" :event-count="3" />
            <KinDayHeader :day="14" weekday="Friday" month="March 2026" :event-count="3" :is-today="true" />
            <div class="rounded-xl border p-8 max-w-[640px]" :style="{ background: D.surfaceRaised, borderColor: D.borderSubtle }">
              <KinDayHeader :day="14" weekday="Friday" month="March 2026" :event-count="3" size="lg" />
            </div>
          </div>
        </div>
      </VariantFrame>
    </section>
  </ComponentPage>
</template>
