<script setup>
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import KinActivityRow from '@/components/design-system/KinActivityRow.vue'

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
    title="5.9 ActivityRow"
    description="Conversational feed row — avatar left, text + optional subtext, meta chip + relative time on the right. Social rows (kudos, badge awards) get inline Nice / Reply action buttons; mechanical events (task completions, redemptions) are read-only."
    status="chosen"
  >
    <section class="mb-16">
      <VariantFrame label="KinActivityRow" caption="Avatar · text + meta chip · optional sub line · optional Nice/Reply actions · relative time on the right.">
        <div class="w-full space-y-10">
          <!-- LIGHT PANEL -->
          <div class="rounded-2xl border p-6" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest mb-4" :style="{ color: L.inkTertiary }">Light mode</p>
            <div class="divide-y" :style="{ borderColor: L.borderSubtle }">
              <KinActivityRow initials="EQ" avatar-color="#6856B2" text="Emma gave kudos to Ava" sub="&quot;Great job helping with dinner tonight!&quot;" meta="+5 pts" meta-accent="mint" time="5m ago" show-actions />
              <KinActivityRow initials="GQ" avatar-color="#2E8A62" text="Greg completed &quot;Mow the lawn&quot;" meta="+15 pts" meta-accent="sun" time="22m ago" />
              <KinActivityRow initials="AQ" avatar-color="#BA562E" text="Ava redeemed &quot;Extra screen time&quot;" meta="−100 pts" meta-accent="failed" time="1h ago" />
              <KinActivityRow initials="EQ" avatar-color="#A2780C" text="Emma gave kudos to Greg" sub="&quot;Thanks for fixing my bike!&quot;" meta="+5 pts" meta-accent="mint" time="2h ago" show-actions />
              <KinActivityRow initials="GQ" avatar-color="#486E9C" text="Greg awarded badge &quot;Streak Master&quot; to Emma" meta="Achievement" meta-accent="lavender" time="4h ago" />
            </div>
          </div>

          <!-- DARK PANEL -->
          <div class="dark rounded-2xl border p-6" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest mb-4" :style="{ color: D.inkTertiary }">Dark mode</p>
            <div class="divide-y" :style="{ borderColor: D.borderSubtle }">
              <KinActivityRow initials="EQ" avatar-color="#B6A8E6" text="Emma gave kudos to Ava" sub="&quot;Great job helping with dinner tonight!&quot;" meta="+5 pts" meta-accent="mint" time="5m ago" show-actions />
              <KinActivityRow initials="GQ" avatar-color="#7CD6AE" text="Greg completed &quot;Mow the lawn&quot;" meta="+15 pts" meta-accent="sun" time="22m ago" />
              <KinActivityRow initials="AQ" avatar-color="#F0A882" text="Ava redeemed &quot;Extra screen time&quot;" meta="−100 pts" meta-accent="failed" time="1h ago" />
              <KinActivityRow initials="EQ" avatar-color="#E6C452" text="Emma gave kudos to Greg" sub="&quot;Thanks for fixing my bike!&quot;" meta="+5 pts" meta-accent="mint" time="2h ago" show-actions />
              <KinActivityRow initials="GQ" avatar-color="#78A4DC" text="Greg awarded badge &quot;Streak Master&quot; to Emma" meta="Achievement" meta-accent="lavender" time="4h ago" />
            </div>
          </div>
        </div>
      </VariantFrame>
    </section>
  </ComponentPage>
</template>
