<script setup>
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import ModeSplit from '../../shared/ModeSplit.vue'
import KinButton from '@/components/design-system/KinButton.vue'
import { BellIcon, EllipsisHorizontalIcon } from '@heroicons/vue/24/outline'
</script>

<template>
  <ComponentPage
    title="1.1 Button"
    description="Pill-shaped interactive controls. Lifted / tactile treatment: subtle gradient, resting shadow, 3px lift on hover, pressed-in feel on active. Covers primary / secondary / ghost / danger / accent × sm/md/lg × default/hover/active/focus/disabled/loading × icon-only. Promoted to <code>KinButton.vue</code> once confirmed."
    status="chosen"
  >
    <!-- ══════════════════════════════════════════════════════════════
         LIFTED FILLED PILL (the chosen treatment)
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="✓" caption="Lifted filled pill — subtle gradient + shadow lift on hover">
        <ModeSplit>
          <div class="space-y-8">
            <!-- Semantic colors -->
            <div>
              <p class="text-caption text-ink-tertiary mb-3">Semantic colors — md size</p>
              <div class="flex flex-wrap gap-3 items-center">
                <KinButton variant="primary">Primary</KinButton>
                <KinButton variant="secondary">Secondary</KinButton>
                <KinButton variant="ghost">Ghost</KinButton>
                <KinButton variant="danger">Danger</KinButton>
                <KinButton variant="accent">Accent</KinButton>
              </div>
            </div>

            <!-- Size scale -->
            <div>
              <p class="text-caption text-ink-tertiary mb-3">Size scale — primary</p>
              <div class="flex flex-wrap gap-3 items-center">
                <KinButton variant="primary" size="sm">Small</KinButton>
                <KinButton variant="primary" size="md">Medium</KinButton>
                <KinButton variant="primary" size="lg">Large</KinButton>
              </div>
            </div>

            <!-- States -->
            <div>
              <p class="text-caption text-ink-tertiary mb-1">States — primary md</p>
              <p class="text-caption text-ink-tertiary mb-3 italic">
                Hover and Active are live — interact with the buttons below.
                Focus = the ring shown when a keyboard user tabs to the button. Accessibility requirement — don't remove it.
              </p>
              <div class="flex flex-wrap gap-3 items-center">
                <div class="flex flex-col items-center gap-1.5">
                  <KinButton variant="primary">Default</KinButton>
                  <span class="text-caption text-ink-tertiary">default</span>
                </div>
                <div class="flex flex-col items-center gap-1.5">
                  <KinButton variant="primary" disabled>Disabled</KinButton>
                  <span class="text-caption text-ink-tertiary">disabled</span>
                </div>
                <div class="flex flex-col items-center gap-1.5">
                  <KinButton variant="primary" loading>Loading</KinButton>
                  <span class="text-caption text-ink-tertiary">loading</span>
                </div>
              </div>
            </div>

            <!-- Icon-only -->
            <div>
              <p class="text-caption text-ink-tertiary mb-3">Icon-only — sm / md / lg (primary) + secondary + ghost at md</p>
              <div class="flex flex-wrap gap-3 items-center">
                <KinButton variant="primary" size="sm" :icon-only="true" aria-label="Bell small">
                  <BellIcon class="w-4 h-4" />
                </KinButton>
                <KinButton variant="primary" size="md" :icon-only="true" aria-label="Ellipsis medium">
                  <EllipsisHorizontalIcon class="w-5 h-5" />
                </KinButton>
                <KinButton variant="primary" size="lg" :icon-only="true" aria-label="Ellipsis large">
                  <EllipsisHorizontalIcon class="w-5 h-5" />
                </KinButton>
                <KinButton variant="secondary" size="md" :icon-only="true" aria-label="Bell secondary">
                  <BellIcon class="w-5 h-5" />
                </KinButton>
                <KinButton variant="ghost" size="md" :icon-only="true" aria-label="Bell ghost">
                  <BellIcon class="w-5 h-5" />
                </KinButton>
              </div>
            </div>
          </div>
        </ModeSplit>
      </VariantFrame>

      <p class="mt-3 text-body-sm text-ink-secondary px-1">
        The lift animation gives every press a physical reward — the button meets your finger. Works for both hero CTAs and quieter secondary actions; the gradient + resting shadow reads as tactile rather than bubble-y.
      </p>
    </section>
  </ComponentPage>
</template>
