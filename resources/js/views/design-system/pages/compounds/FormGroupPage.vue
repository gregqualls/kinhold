<script setup>
import { ref } from 'vue'
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import KinFormGroup from '@/components/design-system/KinFormGroup.vue'
import KinInput from '@/components/design-system/KinInput.vue'
import KinTextarea from '@/components/design-system/KinTextarea.vue'
import KinSwitch from '@/components/design-system/KinSwitch.vue'

const kinEmail    = ref('')
const kinPassword = ref('')
const kinBio      = ref('')
const kinNotifs   = ref(true)
const kinBadEmail = ref('bad@')

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
    title="4.10 FormGroup"
    description="Atomic form-field wrapper: label + field slot + helper / error text. Provides the canonical label-on-top convention for every form in the app, with consistent required-asterisk, error styling, and aria wiring."
    status="chosen"
  >
    <section class="mb-16">
      <VariantFrame label="KinFormGroup" caption="Default slot exposes { id, ariaInvalid, ariaDescribedby } so the field inside gets a label-linked id and a11y wiring for free.">
        <div class="w-full space-y-10">
          <!-- LIGHT PANEL -->
          <div
            class="rounded-2xl border p-6 space-y-5"
            :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }"
          >
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>

            <div class="max-w-md space-y-5">
              <KinFormGroup label="Email address" helper="We'll never share it with anyone.">
                <template #default="{ id }">
                  <KinInput :id="id" v-model="kinEmail" type="email" placeholder="you@example.com" />
                </template>
              </KinFormGroup>

              <KinFormGroup label="Password" required>
                <template #default="{ id }">
                  <KinInput :id="id" v-model="kinPassword" type="password" />
                </template>
              </KinFormGroup>

              <KinFormGroup label="Email address" error="That doesn't look like a valid email.">
                <template #default="{ id }">
                  <KinInput :id="id" v-model="kinBadEmail" type="email" error="invalid" />
                </template>
              </KinFormGroup>

              <KinFormGroup label="Bio" helper="A short introduction for your family profile.">
                <template #default="{ id }">
                  <KinTextarea :id="id" v-model="kinBio" :rows="3" placeholder="Tell your family about yourself…" />
                </template>
              </KinFormGroup>

              <KinFormGroup label="Weekly digest" helper="Every Sunday morning at 8am.">
                <KinSwitch v-model="kinNotifs" label="Send me a weekly summary email" />
              </KinFormGroup>

              <KinFormGroup label="Family name" disabled helper="Set during onboarding — contact support to change.">
                <template #default="{ id }">
                  <KinInput :id="id" model-value="The Qualls family" disabled />
                </template>
              </KinFormGroup>
            </div>
          </div>

          <!-- DARK PANEL -->
          <div
            class="dark rounded-2xl border p-6 space-y-5"
            :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }"
          >
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

            <div class="max-w-md space-y-5">
              <KinFormGroup label="Email address" helper="We'll never share it with anyone.">
                <template #default="{ id }">
                  <KinInput :id="id" v-model="kinEmail" type="email" placeholder="you@example.com" />
                </template>
              </KinFormGroup>

              <KinFormGroup label="Password" required>
                <template #default="{ id }">
                  <KinInput :id="id" v-model="kinPassword" type="password" />
                </template>
              </KinFormGroup>

              <KinFormGroup label="Email address" error="That doesn't look like a valid email.">
                <template #default="{ id }">
                  <KinInput :id="id" v-model="kinBadEmail" type="email" error="invalid" />
                </template>
              </KinFormGroup>

              <KinFormGroup label="Bio" helper="A short introduction for your family profile.">
                <template #default="{ id }">
                  <KinTextarea :id="id" v-model="kinBio" :rows="3" placeholder="Tell your family about yourself…" />
                </template>
              </KinFormGroup>

              <KinFormGroup label="Weekly digest" helper="Every Sunday morning at 8am.">
                <KinSwitch v-model="kinNotifs" label="Send me a weekly summary email" />
              </KinFormGroup>

              <KinFormGroup label="Family name" disabled helper="Set during onboarding — contact support to change.">
                <template #default="{ id }">
                  <KinInput :id="id" model-value="The Qualls family" disabled />
                </template>
              </KinFormGroup>
            </div>
          </div>
        </div>
      </VariantFrame>
    </section>
  </ComponentPage>
</template>
