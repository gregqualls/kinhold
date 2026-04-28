<script setup>
import { ref } from 'vue'
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import ModeSplit from '../../shared/ModeSplit.vue'
import KinSelect from '@/components/design-system/KinSelect.vue'

// Interactive demo state
const assignee   = ref('mike')
const recurrence = ref('')
const country    = ref('')
const grouped    = ref('cat')

const familyOptions = [
  { value: 'mike',  label: 'Mike (you)' },
  { value: 'sarah', label: 'Sarah' },
  { value: 'emma',  label: 'Emma' },
  { value: 'jake',  label: 'Jake' },
  { value: 'lily',  label: 'Lily' },
]

const recurrenceOptions = [
  { value: 'daily',   label: 'Every day' },
  { value: 'weekly',  label: 'Every week' },
  { value: 'monthly', label: 'Every month' },
  { value: 'yearly',  label: 'Every year' },
  { value: 'custom',  label: 'Custom (RRULE)' },
]

const groupedOptions = [
  {
    group: 'Pets',
    options: [
      { value: 'cat', label: 'Cat' },
      { value: 'dog', label: 'Dog' },
      { value: 'rabbit', label: 'Rabbit' },
    ],
  },
  {
    group: 'Wild',
    options: [
      { value: 'fox',     label: 'Fox' },
      { value: 'hawk',    label: 'Hawk' },
      { value: 'raccoon', label: 'Raccoon', disabled: true },
    ],
  },
]
</script>

<template>
  <ComponentPage
    title="1.8 Select"
    description="Native <select> wrapped in KinInput's borderless inset look — sunken at rest, accent-lavender ring on focus, raised surface in dark mode. Keeps the OS-native dropdown so accessibility and mobile pickers work out of the box."
    status="chosen"
  >
    <!-- ════════════════════════════════════════════════════════════════
         Default states
         ════════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="Default states" caption="empty / filled / error / disabled">
        <ModeSplit>
          <div class="w-full space-y-10">
            <div>
              <p class="text-caption text-ink-tertiary mb-3">States</p>
              <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <div class="flex flex-col gap-1">
                  <KinSelect
                    placeholder="Choose…"
                    :options="familyOptions"
                  />
                  <span class="text-caption text-ink-tertiary">empty + placeholder</span>
                </div>

                <div class="flex flex-col gap-1">
                  <KinSelect
                    v-model="assignee"
                    :options="familyOptions"
                  />
                  <span class="text-caption text-ink-tertiary">filled (v-model)</span>
                </div>

                <div class="flex flex-col gap-1">
                  <KinSelect
                    placeholder="Choose…"
                    :options="familyOptions"
                    error="Please pick someone."
                  />
                  <span class="text-caption text-ink-tertiary">error</span>
                </div>

                <div class="flex flex-col gap-1">
                  <KinSelect
                    placeholder="Cannot edit"
                    :options="familyOptions"
                    disabled
                  />
                  <span class="text-caption text-ink-tertiary">disabled</span>
                </div>

                <div class="flex flex-col gap-1">
                  <KinSelect
                    placeholder="Read-only"
                    :options="familyOptions"
                    readonly
                  />
                  <span class="text-caption text-ink-tertiary">readonly</span>
                </div>
              </div>
            </div>
          </div>
        </ModeSplit>
      </VariantFrame>
    </section>

    <!-- ════════════════════════════════════════════════════════════════
         With label + helper
         ════════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="Label + helper" caption="paired with label, helper text, and required asterisk">
        <ModeSplit>
          <div class="w-full grid grid-cols-1 md:grid-cols-2 gap-4">
            <KinSelect
              v-model="assignee"
              label="Assignee"
              :options="familyOptions"
              placeholder="Anyone"
              helper="Leave blank to make this an open task."
            />

            <KinSelect
              v-model="recurrence"
              label="Recurrence"
              :options="recurrenceOptions"
              placeholder="Does not repeat"
              :required="true"
            />
          </div>
        </ModeSplit>
      </VariantFrame>
    </section>

    <!-- ════════════════════════════════════════════════════════════════
         Sizes
         ════════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="Sizes" caption="sm / md / lg">
        <ModeSplit>
          <div class="w-full grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <KinSelect
              v-model="country"
              label="Small"
              size="sm"
              placeholder="Choose…"
              :options="['USA', 'UK', 'Canada', 'Australia']"
            />
            <KinSelect
              v-model="country"
              label="Medium (default)"
              size="md"
              placeholder="Choose…"
              :options="['USA', 'UK', 'Canada', 'Australia']"
            />
            <KinSelect
              v-model="country"
              label="Large"
              size="lg"
              placeholder="Choose…"
              :options="['USA', 'UK', 'Canada', 'Australia']"
            />
          </div>
        </ModeSplit>
      </VariantFrame>
    </section>

    <!-- ════════════════════════════════════════════════════════════════
         Grouped options
         ════════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="Grouped (optgroup)" caption="pass [{ group, options: [...] }] to render <optgroup>s; one option can be disabled">
        <ModeSplit>
          <div class="w-full max-w-sm">
            <KinSelect
              v-model="grouped"
              label="Animal"
              :options="groupedOptions"
            />
          </div>
        </ModeSplit>
      </VariantFrame>
    </section>
  </ComponentPage>
</template>
