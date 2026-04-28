<script setup>
import { ref } from 'vue'
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import ModeSplit from '../../shared/ModeSplit.vue'
import KinInput from '@/components/design-system/KinInput.vue'
import KinTextarea from '@/components/design-system/KinTextarea.vue'
import KinSearch from '@/components/design-system/KinSearch.vue'
import {
  EnvelopeIcon,
  EyeIcon,
  EyeSlashIcon,
} from '@heroicons/vue/24/outline'

// Interactive state for password-toggle example
const passwordVisible = ref(false)
const searchVal       = ref('')
const searchValFilled = ref('Qualls family')
</script>

<template>
  <ComponentPage
    title="1.2 Input · Textarea · Search"
    description="Three form-field variants. B = borderless inset (chosen for text/textarea). C = pill search (always used for search inputs). Promoted to KinInput.vue, KinTextarea.vue, KinSearch.vue."
    status="chosen"
  >
    <!-- ══════════════════════════════════════════════════════════════
         KININPUT — Borderless inset text field
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="B" caption="KinInput — borderless inset, no border at rest, accent ring on focus">
        <ModeSplit>
          <div class="w-full space-y-10">
            <!-- Default states row -->
            <div>
              <p class="text-caption text-ink-tertiary mb-3">Default states</p>
              <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <div class="flex flex-col gap-1">
                  <KinInput placeholder="Placeholder" />
                  <span class="text-caption text-ink-tertiary">empty</span>
                </div>

                <div class="flex flex-col gap-1">
                  <KinInput model-value="Hello world" />
                  <span class="text-caption text-ink-tertiary">filled</span>
                </div>

                <div class="flex flex-col gap-1">
                  <KinInput
                    model-value="bad@"
                    error="Email is invalid"
                  />
                  <span class="text-caption text-ink-tertiary">error</span>
                </div>

                <div class="flex flex-col gap-1">
                  <KinInput placeholder="Cannot edit" disabled />
                  <span class="text-caption text-ink-tertiary">disabled</span>
                </div>

                <div class="flex flex-col gap-1">
                  <KinInput placeholder="Read only" model-value="Locked value" readonly />
                  <span class="text-caption text-ink-tertiary">readonly</span>
                </div>
              </div>
            </div>

            <!-- Affordances row -->
            <div>
              <p class="text-caption text-ink-tertiary mb-3">Affordances — label / helper / prefix / suffix</p>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Email with prefix icon -->
                <KinInput
                  label="Email address"
                  type="email"
                  placeholder="you@example.com"
                  helper="We'll never share your email."
                >
                  <template #prefix>
                    <EnvelopeIcon class="w-full h-full" />
                  </template>
                </KinInput>

                <!-- Password with suffix toggle -->
                <KinInput
                  label="Password"
                  :type="passwordVisible ? 'text' : 'password'"
                  model-value="s3cr3tpwd"
                >
                  <template #suffix>
                    <button
                      type="button"
                      class="w-full h-full flex items-center justify-center text-ink-tertiary hover:text-ink-secondary"
                      @click="passwordVisible = !passwordVisible"
                    >
                      <EyeSlashIcon v-if="passwordVisible" class="w-full h-full" />
                      <EyeIcon v-else class="w-full h-full" />
                    </button>
                  </template>
                </KinInput>

                <!-- Label + helper -->
                <KinInput
                  label="Family name"
                  placeholder="The Qualls family"
                  helper="Helper text appears here."
                />

                <!-- Error state with label -->
                <KinInput
                  label="Required field"
                  :required="true"
                  model-value="bad-value"
                  error="This field has an error."
                />
              </div>
            </div>

            <!-- Size scale row -->
            <div>
              <p class="text-caption text-ink-tertiary mb-3">Size scale</p>
              <div class="flex flex-col gap-3 max-w-sm">
                <div class="flex items-center gap-3">
                  <KinInput size="sm" placeholder="Small — h-8" class="w-64" />
                  <span class="text-caption text-ink-tertiary">sm</span>
                </div>
                <div class="flex items-center gap-3">
                  <KinInput size="md" placeholder="Medium — h-10 (default)" class="w-64" />
                  <span class="text-caption text-ink-tertiary">md (default)</span>
                </div>
                <div class="flex items-center gap-3">
                  <KinInput size="lg" placeholder="Large — h-12" class="w-64" />
                  <span class="text-caption text-ink-tertiary">lg</span>
                </div>
              </div>
            </div>
          </div>
        </ModeSplit>
      </VariantFrame>

      <p class="mt-3 text-body-sm text-ink-secondary px-1">
        Borderless Inset: sunken fill + inset shadow communicate "field" without border chrome at rest. Stripe/Linear feel — airy and editorial. The accent ring materialises only on focus, so it gets full attention.
      </p>
    </section>

    <!-- ══════════════════════════════════════════════════════════════
         KINTEXTAREA — multi-line, same visual language as KinInput
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="B" caption="KinTextarea — borderless inset, resizable, same token language as KinInput">
        <ModeSplit>
          <div class="w-full space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <!-- Basic -->
              <div class="flex flex-col gap-1">
                <KinTextarea placeholder="Write something…" />
                <span class="text-caption text-ink-tertiary">empty</span>
              </div>

              <!-- Filled -->
              <div class="flex flex-col gap-1">
                <KinTextarea
                  model-value="This is a filled textarea with some text that wraps across multiple lines to demonstrate how the field handles longer content."
                />
                <span class="text-caption text-ink-tertiary">filled</span>
              </div>

              <!-- Error -->
              <div class="flex flex-col gap-1">
                <KinTextarea
                  model-value="Problem content."
                  error="Content is required"
                />
                <span class="text-caption text-ink-tertiary">error</span>
              </div>

              <!-- With label + helper -->
              <div class="flex flex-col gap-1">
                <KinTextarea
                  label="Notes"
                  placeholder="Add any additional notes…"
                  helper="Markdown is supported."
                />
                <span class="text-caption text-ink-tertiary">label + helper</span>
              </div>

              <!-- Taller rows -->
              <div class="flex flex-col gap-1">
                <KinTextarea
                  label="Long description"
                  placeholder="Lots of room to write…"
                  :rows="6"
                />
                <span class="text-caption text-ink-tertiary">rows=6</span>
              </div>

              <!-- Disabled -->
              <div class="flex flex-col gap-1">
                <KinTextarea
                  label="Locked"
                  model-value="You cannot edit this."
                  disabled
                />
                <span class="text-caption text-ink-tertiary">disabled</span>
              </div>
            </div>

            <!-- Size scale -->
            <div>
              <p class="text-caption text-ink-tertiary mb-3">Size scale (affects padding + font, not height — height is rows-driven)</p>
              <div class="flex flex-col gap-3 max-w-sm">
                <div class="flex items-start gap-3">
                  <KinTextarea size="sm" placeholder="Small" :rows="2" class="w-64" />
                  <span class="text-caption text-ink-tertiary mt-2">sm</span>
                </div>
                <div class="flex items-start gap-3">
                  <KinTextarea size="md" placeholder="Medium (default)" :rows="2" class="w-64" />
                  <span class="text-caption text-ink-tertiary mt-2">md</span>
                </div>
                <div class="flex items-start gap-3">
                  <KinTextarea size="lg" placeholder="Large" :rows="2" class="w-64" />
                  <span class="text-caption text-ink-tertiary mt-2">lg</span>
                </div>
              </div>
            </div>
          </div>
        </ModeSplit>
      </VariantFrame>
    </section>

    <!-- ══════════════════════════════════════════════════════════════
         KINSEARCH — pill-shaped search field
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="C" caption="KinSearch — rounded-pill shape, reserved for search inputs only per the design brief">
        <ModeSplit>
          <div class="w-full space-y-8">
            <!-- States row -->
            <div>
              <p class="text-caption text-ink-tertiary mb-3">States</p>
              <div class="flex flex-col gap-4 max-w-sm">
                <div class="flex flex-col gap-1">
                  <KinSearch placeholder="Search family…" />
                  <span class="text-caption text-ink-tertiary">empty — no border at rest</span>
                </div>

                <div class="flex flex-col gap-1">
                  <KinSearch v-model="searchValFilled" placeholder="Search family…" />
                  <span class="text-caption text-ink-tertiary">filled — X clears the field</span>
                </div>

                <div class="flex flex-col gap-1">
                  <KinSearch v-model="searchVal" placeholder="Search family…" />
                  <span class="text-caption text-ink-tertiary">interactive — type to see clear button</span>
                </div>

                <div class="flex flex-col gap-1">
                  <KinSearch placeholder="Disabled" disabled />
                  <span class="text-caption text-ink-tertiary">disabled</span>
                </div>

                <div class="flex flex-col gap-1">
                  <KinSearch model-value="No clear button" :clearable="false" />
                  <span class="text-caption text-ink-tertiary">clearable=false</span>
                </div>
              </div>
            </div>

            <!-- Size scale -->
            <div>
              <p class="text-caption text-ink-tertiary mb-3">Size scale</p>
              <div class="flex flex-col gap-3 max-w-sm">
                <div class="flex items-center gap-3">
                  <KinSearch size="sm" placeholder="Small" class="w-64" />
                  <span class="text-caption text-ink-tertiary">sm</span>
                </div>
                <div class="flex items-center gap-3">
                  <KinSearch size="md" placeholder="Medium (default)" class="w-64" />
                  <span class="text-caption text-ink-tertiary">md</span>
                </div>
                <div class="flex items-center gap-3">
                  <KinSearch size="lg" placeholder="Large" class="w-64" />
                  <span class="text-caption text-ink-tertiary">lg</span>
                </div>
              </div>
            </div>
          </div>
        </ModeSplit>
      </VariantFrame>

      <p class="mt-3 text-body-sm text-ink-secondary px-1">
        Pill Search: the full-round pill shape signals "search" instantly and contrasts with the rect/soft-rect inputs used for data entry. The magnifier prefix and conditional clear X make the affordance crystal clear at a glance. Per the brief, this shape is reserved for search only — all text fields and textareas use the borderless inset treatment above.
      </p>
    </section>
  </ComponentPage>
</template>
