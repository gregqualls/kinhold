<script setup>
import { ref } from 'vue'
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import KinAIActivityCard from '@/components/design-system/KinAIActivityCard.vue'

const kinExpL = ref(true)
const kinExpD = ref(true)

const L = {
  surfaceApp:    '#FAF8F5',
  inkTertiary:   '#9C9895',
  borderSubtle:  '#E8E4DF',
  accents: { sun: { soft: '#FCF3D2', bold: '#A2780C' } },
}
const D = {
  surfaceApp:    '#141311',
  inkTertiary:   '#6E6B67',
  borderSubtle:  '#2C2A27',
  accents: { sun: { soft: '#342C0A', bold: '#E6C452' } },
}
</script>

<template>
  <ComponentPage
    title="5.10 AIActivityCard"
    description="The surface where the AI's background work becomes visible. Shows what triggered the assistant, which tools ran, the output, and actions to act on the result. Kinhold's AI is a reactive copilot in the margins — this card is how that work surfaces outside of chat."
    status="chosen"
  >
    <section class="mb-16">
      <VariantFrame label="KinAIActivityCard" caption="Click the header to toggle expanded/collapsed. Expanded reveals trigger → steps → output. Status pill (success / pending / failed) + duration on the right.">
        <div class="w-full space-y-10">
          <!-- LIGHT PANEL -->
          <div class="rounded-2xl border p-6 space-y-3" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest mb-4" :style="{ color: L.inkTertiary }">Light mode</p>
            <KinAIActivityCard
              v-model:expanded="kinExpL"
              title="Created task &quot;Take out trash&quot; for Emma"
              status="success"
              duration="1.2s"
              trigger="&quot;Add a task for Emma tomorrow to take out trash&quot;"
              :steps="[
                { label: 'list_users — fetched 5 family members' },
                { label: 'create_task — assigned to Emma, due tomorrow' },
                { label: 'notify_user — push sent to Emma' },
              ]"
            >
              <template #output>
                <div class="flex items-start justify-between gap-2">
                  <div>
                    <p class="text-sm font-semibold text-ink-primary">Take out trash</p>
                    <p class="text-[11px] mt-0.5 text-ink-secondary">Assigned to Emma · Due tomorrow</p>
                  </div>
                  <span class="text-[11px] font-medium px-2 py-0.5 rounded-full flex-shrink-0" :style="{ background: L.accents.sun.soft, color: L.accents.sun.bold }">+5 pts</span>
                </div>
              </template>
            </KinAIActivityCard>
            <KinAIActivityCard title="Fetching vault entry for car insurance" status="pending" duration="3.8s" />
            <KinAIActivityCard title="Meal plan generation failed — missing ingredients" status="failed" duration="4.2s" />
          </div>

          <!-- DARK PANEL -->
          <div class="dark rounded-2xl border p-6 space-y-3" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest mb-4" :style="{ color: D.inkTertiary }">Dark mode</p>
            <KinAIActivityCard
              v-model:expanded="kinExpD"
              title="Created task &quot;Take out trash&quot; for Emma"
              status="success"
              duration="1.2s"
              trigger="&quot;Add a task for Emma tomorrow to take out trash&quot;"
              :steps="[
                { label: 'list_users — fetched 5 family members' },
                { label: 'create_task — assigned to Emma, due tomorrow' },
                { label: 'notify_user — push sent to Emma' },
              ]"
            >
              <template #output>
                <div class="flex items-start justify-between gap-2">
                  <div>
                    <p class="text-sm font-semibold text-ink-primary">Take out trash</p>
                    <p class="text-[11px] mt-0.5 text-ink-secondary">Assigned to Emma · Due tomorrow</p>
                  </div>
                  <span class="text-[11px] font-medium px-2 py-0.5 rounded-full flex-shrink-0" :style="{ background: D.accents.sun.soft, color: D.accents.sun.bold }">+5 pts</span>
                </div>
              </template>
            </KinAIActivityCard>
            <KinAIActivityCard title="Fetching vault entry for car insurance" status="pending" duration="3.8s" />
            <KinAIActivityCard title="Meal plan generation failed — missing ingredients" status="failed" duration="4.2s" />
          </div>
        </div>
      </VariantFrame>
    </section>
  </ComponentPage>
</template>
