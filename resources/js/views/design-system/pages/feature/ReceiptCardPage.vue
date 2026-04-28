<script setup>
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import {
  CalendarDaysIcon, ClockIcon, UserIcon,
  StarIcon, CheckBadgeIcon, TicketIcon, FlagIcon,
} from '@heroicons/vue/24/outline'
import KinReceiptCard from '@/components/design-system/KinReceiptCard.vue'

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
    title="5.12 ReceiptCard"
    description="Structured detail card with a header, label/value rows, and an optional action button. Used for reward redemption confirmations, task detail summaries, vault entry detail headers, and meal plan recipe summaries."
    status="chosen"
  >
    <section class="mb-16">
      <VariantFrame label="KinReceiptCard" caption="Title + title icon + accent color · label/value rows with optional row icons · optional CTA. Per-row icon accent overrides the card accent.">
        <div class="w-full space-y-10">
          <!-- LIGHT PANEL -->
          <div class="rounded-2xl border p-6" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest mb-4" :style="{ color: L.inkTertiary }">Light mode</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <KinReceiptCard
                title="Movie night pass"
                :title-icon="TicketIcon"
                accent-color="sun"
                :rows="[
                  { label: 'Points spent', value: '150', icon: StarIcon },
                  { label: 'Purchased', value: 'Today', icon: CalendarDaysIcon },
                  { label: 'Expires', value: 'Apr 30', icon: ClockIcon },
                  { label: 'Redeemed by', value: 'Emma', icon: UserIcon },
                ]"
                action-label="View details"
              />
              <KinReceiptCard
                title="Do laundry"
                :title-icon="CheckBadgeIcon"
                accent-color="mint"
                :rows="[
                  { label: 'Due', value: 'Tomorrow 6pm', icon: CalendarDaysIcon },
                  { label: 'Priority', value: 'High', icon: FlagIcon, iconAccent: 'peach' },
                  { label: 'Points', value: '5', icon: StarIcon, iconAccent: 'sun' },
                ]"
                action-label="Mark complete"
              />
            </div>
          </div>

          <!-- DARK PANEL -->
          <div class="dark rounded-2xl border p-6" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest mb-4" :style="{ color: D.inkTertiary }">Dark mode</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <KinReceiptCard
                title="Movie night pass"
                :title-icon="TicketIcon"
                accent-color="sun"
                :rows="[
                  { label: 'Points spent', value: '150', icon: StarIcon },
                  { label: 'Purchased', value: 'Today', icon: CalendarDaysIcon },
                  { label: 'Expires', value: 'Apr 30', icon: ClockIcon },
                  { label: 'Redeemed by', value: 'Emma', icon: UserIcon },
                ]"
                action-label="View details"
              />
              <KinReceiptCard
                title="Do laundry"
                :title-icon="CheckBadgeIcon"
                accent-color="mint"
                :rows="[
                  { label: 'Due', value: 'Tomorrow 6pm', icon: CalendarDaysIcon },
                  { label: 'Priority', value: 'High', icon: FlagIcon, iconAccent: 'peach' },
                  { label: 'Points', value: '5', icon: StarIcon, iconAccent: 'sun' },
                ]"
                action-label="Mark complete"
              />
            </div>
          </div>
        </div>
      </VariantFrame>
    </section>
  </ComponentPage>
</template>
