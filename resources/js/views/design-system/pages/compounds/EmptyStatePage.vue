<script setup>
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import KinEmptyState from '@/components/design-system/KinEmptyState.vue'
import KinButton from '@/components/design-system/KinButton.vue'
import {
  ClipboardDocumentListIcon,
  LockClosedIcon,
  PlusIcon,
  UserGroupIcon,
  ChatBubbleLeftEllipsisIcon,
} from '@heroicons/vue/24/outline'

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
    title="4.9 EmptyState"
    description="The placeholder shown when a list, board, feed, or vault category is empty. Must feel inviting — not a dead end — and offer a clear path forward."
    status="chosen"
  >
    <section class="mb-16">
      <VariantFrame label="KinEmptyState" caption="Centered iridescent glyph + title + description + CTA slot. Four accent colors, sm and md sizes.">
        <div class="w-full space-y-10">

          <!-- LIGHT PANEL -->
          <div class="rounded-2xl border p-6"
               :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest mb-6" :style="{ color: L.inkTertiary }">Light mode</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="rounded-xl border" :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle }">
                <KinEmptyState
                  :icon="ClipboardDocumentListIcon"
                  title="No tasks yet"
                  description="Add your first task to start tracking what your family needs to do."
                  accent-color="lavender"
                  size="md"
                >
                  <template #cta>
                    <KinButton variant="primary">
                      <template #leading><PlusIcon class="w-4 h-4" /></template>
                      Add task
                    </KinButton>
                  </template>
                </KinEmptyState>
              </div>

              <div class="rounded-xl border" :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle }">
                <KinEmptyState
                  :icon="LockClosedIcon"
                  title="Vault is empty"
                  description="Save important family documents, passwords, and notes here."
                  accent-color="peach"
                  size="md"
                >
                  <template #cta>
                    <KinButton variant="primary">
                      <template #leading><PlusIcon class="w-4 h-4" /></template>
                      Add entry
                    </KinButton>
                  </template>
                </KinEmptyState>
              </div>

              <div class="rounded-xl border" :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle }">
                <KinEmptyState
                  :icon="ChatBubbleLeftEllipsisIcon"
                  title="No messages yet"
                  description="Ask the Kinhold assistant anything about your family's week."
                  accent-color="mint"
                  size="sm"
                />
              </div>

              <div class="rounded-xl border" :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle }">
                <KinEmptyState
                  :icon="UserGroupIcon"
                  title="No family yet"
                  description="Invite parents, kids, and care-givers to get started together."
                  accent-color="sun"
                  size="md"
                >
                  <template #cta>
                    <KinButton variant="primary">Send invites</KinButton>
                  </template>
                </KinEmptyState>
              </div>
            </div>
          </div>

          <!-- DARK PANEL -->
          <div class="dark rounded-2xl border p-6"
               :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest mb-6" :style="{ color: D.inkTertiary }">Dark mode</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="rounded-xl border" :style="{ background: D.surfaceRaised, borderColor: D.borderSubtle }">
                <KinEmptyState
                  :icon="ClipboardDocumentListIcon"
                  title="No tasks yet"
                  description="Add your first task to start tracking what your family needs to do."
                  accent-color="lavender"
                  size="md"
                >
                  <template #cta>
                    <KinButton variant="primary">
                      <template #leading><PlusIcon class="w-4 h-4" /></template>
                      Add task
                    </KinButton>
                  </template>
                </KinEmptyState>
              </div>

              <div class="rounded-xl border" :style="{ background: D.surfaceRaised, borderColor: D.borderSubtle }">
                <KinEmptyState
                  :icon="LockClosedIcon"
                  title="Vault is empty"
                  description="Save important family documents, passwords, and notes here."
                  accent-color="peach"
                  size="md"
                >
                  <template #cta>
                    <KinButton variant="primary">
                      <template #leading><PlusIcon class="w-4 h-4" /></template>
                      Add entry
                    </KinButton>
                  </template>
                </KinEmptyState>
              </div>

              <div class="rounded-xl border" :style="{ background: D.surfaceRaised, borderColor: D.borderSubtle }">
                <KinEmptyState
                  :icon="ChatBubbleLeftEllipsisIcon"
                  title="No messages yet"
                  description="Ask the Kinhold assistant anything about your family's week."
                  accent-color="mint"
                  size="sm"
                />
              </div>

              <div class="rounded-xl border" :style="{ background: D.surfaceRaised, borderColor: D.borderSubtle }">
                <KinEmptyState
                  :icon="UserGroupIcon"
                  title="No family yet"
                  description="Invite parents, kids, and care-givers to get started together."
                  accent-color="sun"
                  size="md"
                >
                  <template #cta>
                    <KinButton variant="primary">Send invites</KinButton>
                  </template>
                </KinEmptyState>
              </div>
            </div>
          </div>

        </div>
      </VariantFrame>
    </section>
  </ComponentPage>
</template>
