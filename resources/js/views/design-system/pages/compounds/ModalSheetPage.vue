<script setup>
import { ref } from 'vue'
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import KinModalSheet from '@/components/design-system/KinModalSheet.vue'
import KinButton from '@/components/design-system/KinButton.vue'
import KinActionPair from '@/components/design-system/KinActionPair.vue'

const kinModalOpen = ref(false)

const L = {
  surfaceApp:    '#FAF8F5',
  surfaceSunken: '#F5F2EE',
  inkSecondary:  '#6B6966',
  inkTertiary:   '#9C9895',
  borderSubtle:  '#E8E4DF',
}
</script>

<template>
  <ComponentPage
    title="4.7 Modal · Sheet · Drawer"
    description="The canonical overlay container. Bottom sheet on mobile (<768 px), centered modal at md+. One pattern, two expressions."
    status="chosen"
  >
    <section class="mb-16">
      <VariantFrame label="KinModalSheet" caption="Click the trigger to open. Responsive: bottom sheet on mobile, centered modal at md+. Backdrop / Esc dismiss.">
        <div class="w-full space-y-6">
          <div
            class="rounded-2xl border p-6 space-y-4"
            :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }"
          >
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Trigger — renders real overlay (responsive)</p>
            <div class="flex gap-3">
              <KinButton variant="primary" @click="kinModalOpen = true">Open modal / sheet</KinButton>
            </div>
            <p class="text-[13px]" :style="{ color: L.inkSecondary }">
              At &lt;md (mobile) it slides up from the bottom as a sheet. At ≥md (desktop) it fades + scales into a centered modal. Click the backdrop or press <kbd class="font-mono text-[11px] px-1.5 py-0.5 rounded" :style="{ background: L.surfaceSunken }">Esc</kbd> to dismiss.
            </p>
          </div>
          <p class="text-sm px-1" :style="{ color: L.inkSecondary }">
            Note: the Kin component mounts to <code class="font-mono text-[12px] px-1 py-0.5 rounded" :style="{ background: L.surfaceSunken }">&lt;body&gt;</code> via Teleport, so the overlay covers the whole page — not just the demo panel. That matches the production behavior.
          </p>
        </div>
      </VariantFrame>

      <KinModalSheet
        v-model="kinModalOpen"
        title="Remove this task?"
        size="md"
      >
        <p class="text-[13px] leading-relaxed text-ink-secondary">
          Removing <strong class="text-ink-primary">"Schedule vet appointment"</strong> will delete the task and any reminders attached to it. This cannot be undone.
        </p>
        <template #actions>
          <KinActionPair layout="equal">
            <template #secondary><KinButton variant="secondary" @click="kinModalOpen = false">Keep</KinButton></template>
            <template #primary><KinButton variant="danger" @click="kinModalOpen = false">Delete</KinButton></template>
          </KinActionPair>
        </template>
      </KinModalSheet>
    </section>
  </ComponentPage>
</template>
