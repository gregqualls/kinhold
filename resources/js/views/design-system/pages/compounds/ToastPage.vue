<script setup>
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import KinToast from '@/components/design-system/KinToast.vue'
import KinToastStack, { useToasts } from '@/components/design-system/KinToastStack.vue'
import KinButton from '@/components/design-system/KinButton.vue'

const { push: pushToast } = useToasts()

const L = {
  surfaceApp:    '#FAF8F5',
  surfaceSunken: '#F5F2EE',
  inkSecondary:  '#6B6966',
  inkTertiary:   '#9C9895',
  borderSubtle:  '#E8E4DF',
}
const D = {
  surfaceApp:    '#141311',
  inkTertiary:   '#6E6B67',
  borderSubtle:  '#2C2A27',
}

const KIN_DEMOS = [
  { status: 'success', title: 'Task completed',    body: 'You earned 15 points for finishing early.', actionLabel: 'View' },
  { status: 'info',    title: 'Sync scheduled',    body: 'Calendar sync runs in 5 minutes.' },
  { status: 'warning', title: 'Low stock',         body: 'Only 2 movie night passes left this month.', actionLabel: 'Reorder' },
  { status: 'failed',  title: 'Upload failed',     body: "We couldn't save the vault entry. Check your connection.", actionLabel: 'Retry' },
  { status: 'pending', title: 'Invite sent',       body: "Maya hasn't accepted yet." },
  { status: 'paused',  title: 'Automation paused', body: 'Your weekly digest is on hold.', actionLabel: 'Resume' },
]
</script>

<template>
  <ComponentPage
    title="4.8 Toast"
    description="Non-blocking notifications for task completions, point awards, sync results, and errors. Canonical placement: top-right on desktop, top-center on mobile."
    status="chosen"
  >
    <section class="mb-16">
      <VariantFrame label="KinToast + KinToastStack" caption="Fire a real toast. Auto-dismiss 4.5s · max 3 visible · top-right desktop / top-center mobile.">
        <div class="w-full space-y-6">
          <!-- TRIGGER PANEL -->
          <div
            class="rounded-2xl border p-6 space-y-4"
            :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }"
          >
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Fire a toast</p>
            <div class="flex flex-wrap gap-2">
              <KinButton
                v-for="d in KIN_DEMOS"
                :key="d.status"
                size="sm"
                variant="secondary"
                @click="pushToast(d)"
              >
                {{ d.status }}
              </KinButton>
            </div>
            <p class="text-[13px]" :style="{ color: L.inkSecondary }">
              Each click pushes a real KinToast into KinToastStack. Auto-dismisses after 4.5s; click <kbd class="font-mono text-[11px] px-1.5 py-0.5 rounded" :style="{ background: L.surfaceSunken }">×</kbd> to dismiss early. Max 3 visible — older ones drop off the bottom of the stack.
            </p>
          </div>

          <!-- STATIC GALLERY (light + dark) for visual reference -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div
              class="rounded-2xl border p-6 space-y-3"
              :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }"
            >
              <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light — all 6 statuses</p>
              <KinToast v-for="d in KIN_DEMOS" :key="d.status" :status="d.status" :title="d.title" :body="d.body" :action-label="d.actionLabel" />
            </div>
            <div
              class="dark rounded-2xl border p-6 space-y-3"
              :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }"
            >
              <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark — all 6 statuses</p>
              <KinToast v-for="d in KIN_DEMOS" :key="d.status" :status="d.status" :title="d.title" :body="d.body" :action-label="d.actionLabel" />
            </div>
          </div>
        </div>
      </VariantFrame>

      <!-- The host. Teleports to body, so its overlay covers the whole page. -->
      <KinToastStack />
    </section>
  </ComponentPage>
</template>
