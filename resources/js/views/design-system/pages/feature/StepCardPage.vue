<script setup>
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import KinStepCard from '@/components/design-system/KinStepCard.vue'

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
    title="5.11 StepCard"
    description="Numbered step card for onboarding flows, multi-step setup, and progressive disclosure. Three states (done / active / default) and an auto-suppressed connector line between cards. Used on the onboarding wizard, vault playbook intros, and any sequenced setup."
    status="chosen"
  >
    <section class="mb-16">
      <VariantFrame label="KinStepCard" caption="Stack multiple cards; the connector line is auto-suppressed on the last card via the isLast prop. States: done · active · default.">
        <div class="w-full space-y-10">
          <!-- LIGHT PANEL -->
          <div class="rounded-2xl border p-5" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest mb-4" :style="{ color: L.inkTertiary }">Light mode</p>
            <KinStepCard :number="1" state="done" title="Create your family" body="Give your family a name and invite code. This becomes your private workspace." />
            <KinStepCard :number="2" state="active" title="Connect your calendar" body="Link Google Calendar so family events appear alongside tasks and reminders." />
            <KinStepCard :number="3" state="default" title="Invite your family members" body="Share the invite code with your partner and kids." :is-last="true" />
          </div>

          <!-- DARK PANEL -->
          <div class="dark rounded-2xl border p-5" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest mb-4" :style="{ color: D.inkTertiary }">Dark mode</p>
            <KinStepCard :number="1" state="done" title="Create your family" body="Give your family a name and invite code. This becomes your private workspace." />
            <KinStepCard :number="2" state="active" title="Connect your calendar" body="Link Google Calendar so family events appear alongside tasks and reminders." />
            <KinStepCard :number="3" state="default" title="Invite your family members" body="Share the invite code with your partner and kids." :is-last="true" />
          </div>
        </div>
      </VariantFrame>
    </section>
  </ComponentPage>
</template>
