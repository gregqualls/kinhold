<script setup>
import { ref } from 'vue'
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import KinCheckbox from '@/components/design-system/KinCheckbox.vue'
import KinRadio    from '@/components/design-system/KinRadio.vue'
import KinSwitch   from '@/components/design-system/KinSwitch.vue'

// ── Checkbox demo state ───────────────────────────────────────────────────────
const cbDefault      = ref(false)
const cbChecked      = ref(true)
const cbIndet        = ref(null)        // null = indeterminate
const cbDisabled     = ref(false)
const cbDisabledChk  = ref(true)
const cbWithDesc     = ref(false)
const cbSmUnchecked  = ref(false)
const cbSmChecked    = ref(true)
const cbMdUnchecked  = ref(false)
const cbMdChecked    = ref(true)

// Grouped task list
const tasks = ref([
  { label: 'Wash dishes',   done: true  },
  { label: 'Feed dog',      done: false },
  { label: 'Pack lunches',  done: null  },  // indeterminate
  { label: 'Take out trash', done: false },
])

// ── Radio demo state ──────────────────────────────────────────────────────────
const rdGroup     = ref('option1')
const rdGroupDark = ref('option1')
const rdHoriz     = ref('all')
const rdHorizDark = ref('all')

const radioOptions = [
  { value: 'option1', label: 'Wash dishes'   },
  { value: 'option2', label: 'Feed dog'      },
  { value: 'option3', label: 'Pack lunches'  },
  { value: 'option4', label: 'Take out trash' },
]

const horizOptions = [
  { value: 'all',      label: 'All'      },
  { value: 'mine',     label: 'Mine'     },
  { value: 'assigned', label: 'Assigned' },
]

// ── Switch demo state ─────────────────────────────────────────────────────────
const swOff       = ref(false)
const swOn        = ref(true)
const swSmOff     = ref(false)
const swSmOn      = ref(true)
const swDisOff    = ref(false)
const swDisOn     = ref(true)
const swWithDesc  = ref(false)

const swLavender  = ref(true)
const swPeach     = ref(true)
const swMint      = ref(true)
const swSun       = ref(true)

const swSettings = ref([false, true, false, true])
const swSettingsDark = ref([false, true, false, true])

const swSettingsLabels = [
  { title: 'Show completed tasks',            helper: 'Display finished tasks in your task lists'       },
  { title: 'Send morning briefing email',     helper: 'Daily summary delivered at 7 AM'                },
  { title: 'Share calendar with family',      helper: 'All family members can see your events'         },
  { title: 'Play success sound on complete',  helper: 'A short chime plays when you check off a task'  },
]
</script>

<template>
  <ComponentPage
    title="1.5 Checkbox · Radio · Switch"
    description="Selection controls. Minimal neutral fill when checked (near-black / off-white) for both checkbox and radio; Apple-style pastel-accent toggle for switches."
    status="chosen"
  >

    <!-- ═══════════════════════════════════════════════════════════════
         SECTION 1 — CHECKBOX
         ════════════════════════════════════════════════════════════════ -->
    <section class="mb-20">
      <VariantFrame label="Checkbox" caption="Minimal neutral — near-black fill when checked">
        <div class="w-full space-y-10">

          <!-- LIGHT PANEL -->
          <div class="rounded-2xl border border-border-subtle bg-surface-app p-6 space-y-8">
            <p class="text-caption font-semibold uppercase tracking-widest text-ink-tertiary">Light mode</p>

            <!-- States matrix -->
            <div>
              <p class="text-caption text-ink-tertiary mb-3">States — md</p>
              <div class="flex flex-wrap items-start gap-6">
                <div class="flex flex-col items-center gap-2">
                  <KinCheckbox :model-value="false" />
                  <span class="text-[10px] text-ink-tertiary">unchecked</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <KinCheckbox :model-value="true" />
                  <span class="text-[10px] text-ink-tertiary">checked</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <KinCheckbox :model-value="null" />
                  <span class="text-[10px] text-ink-tertiary">indeterminate</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <KinCheckbox :model-value="false" disabled />
                  <span class="text-[10px] text-ink-tertiary">disabled</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <KinCheckbox :model-value="true" disabled />
                  <span class="text-[10px] text-ink-tertiary">dis. checked</span>
                </div>
              </div>
            </div>

            <!-- Size scale -->
            <div>
              <p class="text-caption text-ink-tertiary mb-3">Size scale</p>
              <div class="flex items-start gap-6">
                <div class="flex flex-col items-center gap-2">
                  <KinCheckbox :model-value="true" size="sm" />
                  <span class="text-[10px] text-ink-tertiary">sm 14px</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <KinCheckbox :model-value="true" size="md" />
                  <span class="text-[10px] text-ink-tertiary">md 18px</span>
                </div>
              </div>
            </div>

            <!-- With label -->
            <div>
              <p class="text-caption text-ink-tertiary mb-3">With label — click to toggle</p>
              <div class="flex flex-col gap-3">
                <KinCheckbox v-model="cbDefault" label="Short" />
                <KinCheckbox v-model="cbChecked" label="Medium length label text" />
                <div class="max-w-xs">
                  <KinCheckbox
                    v-model="cbIndet"
                    label="A longer label that wraps to a second line to show checkbox alignment with multi-line text"
                  />
                </div>
              </div>
            </div>

            <!-- With description -->
            <div>
              <p class="text-caption text-ink-tertiary mb-3">With description</p>
              <KinCheckbox
                v-model="cbWithDesc"
                label="Send morning briefing"
                description="Daily summary delivered at 7 AM"
              />
            </div>

            <!-- Grouped list -->
            <div>
              <p class="text-caption text-ink-tertiary mb-3">Grouped task list</p>
              <ul class="space-y-3">
                <li v-for="(task, i) in tasks" :key="i">
                  <KinCheckbox
                    v-model="task.done"
                    :label="task.label"
                  />
                </li>
              </ul>
            </div>
          </div><!-- /light checkbox -->

          <!-- DARK PANEL — wrap in .dark div so tokens flip -->
          <div class="dark rounded-2xl border border-border-subtle bg-surface-app p-6 space-y-8">
            <p class="text-caption font-semibold uppercase tracking-widest text-ink-tertiary">Dark mode</p>

            <!-- States matrix dark -->
            <div>
              <p class="text-caption text-ink-tertiary mb-3">States — md</p>
              <div class="flex flex-wrap items-start gap-6">
                <div class="flex flex-col items-center gap-2">
                  <KinCheckbox :model-value="false" />
                  <span class="text-[10px] text-ink-tertiary">unchecked</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <KinCheckbox :model-value="true" />
                  <span class="text-[10px] text-ink-tertiary">checked</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <KinCheckbox :model-value="null" />
                  <span class="text-[10px] text-ink-tertiary">indeterminate</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <KinCheckbox :model-value="false" disabled />
                  <span class="text-[10px] text-ink-tertiary">disabled</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <KinCheckbox :model-value="true" disabled />
                  <span class="text-[10px] text-ink-tertiary">dis. checked</span>
                </div>
              </div>
            </div>

            <!-- Size scale dark -->
            <div>
              <p class="text-caption text-ink-tertiary mb-3">Size scale</p>
              <div class="flex items-start gap-6">
                <div class="flex flex-col items-center gap-2">
                  <KinCheckbox :model-value="true" size="sm" />
                  <span class="text-[10px] text-ink-tertiary">sm 14px</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <KinCheckbox :model-value="true" size="md" />
                  <span class="text-[10px] text-ink-tertiary">md 18px</span>
                </div>
              </div>
            </div>

            <!-- With label dark -->
            <div>
              <p class="text-caption text-ink-tertiary mb-3">With label + description (interactive)</p>
              <div class="flex flex-col gap-3">
                <KinCheckbox v-model="cbSmUnchecked" label="Unchecked default" />
                <KinCheckbox v-model="cbSmChecked"   label="Checked default" />
                <KinCheckbox
                  v-model="cbWithDesc"
                  label="Send morning briefing"
                  description="Daily summary delivered at 7 AM"
                />
              </div>
            </div>
          </div><!-- /dark checkbox -->

        </div>
      </VariantFrame>
    </section>


    <!-- ═══════════════════════════════════════════════════════════════
         SECTION 2 — RADIO
         ════════════════════════════════════════════════════════════════ -->
    <section class="mb-20">
      <VariantFrame label="Radio" caption="Minimal neutral — ink-primary inner dot when checked">
        <div class="w-full space-y-10">

          <!-- LIGHT PANEL -->
          <div class="rounded-2xl border border-border-subtle bg-surface-app p-6 space-y-8">
            <p class="text-caption font-semibold uppercase tracking-widest text-ink-tertiary">Light mode</p>

            <!-- States matrix -->
            <div>
              <p class="text-caption text-ink-tertiary mb-3">States — md</p>
              <div class="flex flex-wrap items-start gap-6">
                <div class="flex flex-col items-center gap-2">
                  <KinRadio :model-value="'a'" value="b" name="rd-states-lt" label="" />
                  <span class="text-[10px] text-ink-tertiary">unchecked</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <KinRadio :model-value="'a'" value="a" name="rd-states-lt2" label="" />
                  <span class="text-[10px] text-ink-tertiary">checked</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <KinRadio :model-value="'a'" value="b" name="rd-states-lt3" label="" disabled />
                  <span class="text-[10px] text-ink-tertiary">disabled</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <KinRadio :model-value="'a'" value="a" name="rd-states-lt4" label="" disabled />
                  <span class="text-[10px] text-ink-tertiary">dis. checked</span>
                </div>
              </div>
            </div>

            <!-- Size scale -->
            <div>
              <p class="text-caption text-ink-tertiary mb-3">Size scale</p>
              <div class="flex items-start gap-6">
                <div class="flex flex-col items-center gap-2">
                  <KinRadio :model-value="'a'" value="a" name="rd-sz-lt-sm" size="sm" label="" />
                  <span class="text-[10px] text-ink-tertiary">sm 14px</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <KinRadio :model-value="'a'" value="a" name="rd-sz-lt-md" size="md" label="" />
                  <span class="text-[10px] text-ink-tertiary">md 18px</span>
                </div>
              </div>
            </div>

            <!-- Radio group (fieldset) -->
            <div>
              <p class="text-caption text-ink-tertiary mb-3">Radio group — arrow-key navigation</p>
              <fieldset class="border-0 p-0 m-0">
                <legend class="text-body-sm font-medium text-ink-secondary mb-3">Choose a task</legend>
                <ul class="space-y-3">
                  <li v-for="opt in radioOptions" :key="opt.value">
                    <KinRadio
                      v-model="rdGroup"
                      :value="opt.value"
                      :label="opt.label"
                      name="rd-group-lt"
                    />
                  </li>
                </ul>
              </fieldset>
            </div>

            <!-- Horizontal / filter row -->
            <div>
              <p class="text-caption text-ink-tertiary mb-3">Horizontal — compact filter row</p>
              <fieldset class="border-0 p-0 m-0">
                <legend class="sr-only">View filter</legend>
                <div class="flex items-center gap-5">
                  <KinRadio
                    v-for="opt in horizOptions"
                    :key="opt.value"
                    v-model="rdHoriz"
                    :value="opt.value"
                    :label="opt.label"
                    name="rd-horiz-lt"
                  />
                </div>
              </fieldset>
            </div>

            <!-- With description -->
            <div>
              <p class="text-caption text-ink-tertiary mb-3">With description</p>
              <div class="space-y-3">
                <KinRadio
                  v-model="rdGroup"
                  value="option1"
                  label="Wash dishes"
                  description="Rinse and load the dishwasher"
                  name="rd-desc-lt"
                />
                <KinRadio
                  v-model="rdGroup"
                  value="option2"
                  label="Feed dog"
                  description="Morning and evening servings"
                  name="rd-desc-lt"
                />
              </div>
            </div>
          </div><!-- /light radio -->

          <!-- DARK PANEL -->
          <div class="dark rounded-2xl border border-border-subtle bg-surface-app p-6 space-y-8">
            <p class="text-caption font-semibold uppercase tracking-widest text-ink-tertiary">Dark mode</p>

            <!-- States dark -->
            <div>
              <p class="text-caption text-ink-tertiary mb-3">States — md</p>
              <div class="flex flex-wrap items-start gap-6">
                <div class="flex flex-col items-center gap-2">
                  <KinRadio :model-value="'a'" value="b" name="rd-states-dk" label="" />
                  <span class="text-[10px] text-ink-tertiary">unchecked</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <KinRadio :model-value="'a'" value="a" name="rd-states-dk2" label="" />
                  <span class="text-[10px] text-ink-tertiary">checked</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <KinRadio :model-value="'a'" value="b" name="rd-states-dk3" label="" disabled />
                  <span class="text-[10px] text-ink-tertiary">disabled</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <KinRadio :model-value="'a'" value="a" name="rd-states-dk4" label="" disabled />
                  <span class="text-[10px] text-ink-tertiary">dis. checked</span>
                </div>
              </div>
            </div>

            <!-- Radio group dark -->
            <div>
              <p class="text-caption text-ink-tertiary mb-3">Radio group — interactive</p>
              <fieldset class="border-0 p-0 m-0">
                <legend class="text-body-sm font-medium text-ink-secondary mb-3">Choose a task</legend>
                <ul class="space-y-3">
                  <li v-for="opt in radioOptions" :key="opt.value">
                    <KinRadio
                      v-model="rdGroupDark"
                      :value="opt.value"
                      :label="opt.label"
                      name="rd-group-dk"
                    />
                  </li>
                </ul>
              </fieldset>
            </div>

            <!-- Horizontal dark -->
            <div>
              <p class="text-caption text-ink-tertiary mb-3">Horizontal filter row</p>
              <fieldset class="border-0 p-0 m-0">
                <legend class="sr-only">View filter</legend>
                <div class="flex items-center gap-5">
                  <KinRadio
                    v-for="opt in horizOptions"
                    :key="opt.value"
                    v-model="rdHorizDark"
                    :value="opt.value"
                    :label="opt.label"
                    name="rd-horiz-dk"
                  />
                </div>
              </fieldset>
            </div>
          </div><!-- /dark radio -->

        </div>
      </VariantFrame>
    </section>


    <!-- ═══════════════════════════════════════════════════════════════
         SECTION 3 — SWITCH
         ════════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="Switch" caption="Apple-style toggle — pastel-accent on track, smooth 200ms thumb transition">
        <div class="w-full space-y-10">

          <!-- LIGHT PANEL -->
          <div class="rounded-2xl border border-border-subtle bg-surface-app p-6 space-y-8">
            <p class="text-caption font-semibold uppercase tracking-widest text-ink-tertiary">Light mode</p>

            <!-- States row -->
            <div>
              <p class="text-caption text-ink-tertiary mb-3">States — md</p>
              <div class="flex flex-wrap items-start gap-8">
                <div class="flex flex-col items-center gap-2">
                  <KinSwitch v-model="swOff" />
                  <span class="text-[10px] text-ink-tertiary">off</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <KinSwitch v-model="swOn" />
                  <span class="text-[10px] text-ink-tertiary">on</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <KinSwitch :model-value="false" disabled />
                  <span class="text-[10px] text-ink-tertiary">disabled off</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <KinSwitch :model-value="true" disabled />
                  <span class="text-[10px] text-ink-tertiary">disabled on</span>
                </div>
              </div>
            </div>

            <!-- Size scale -->
            <div>
              <p class="text-caption text-ink-tertiary mb-3">Size scale — on state</p>
              <div class="flex items-start gap-8">
                <div class="flex flex-col items-center gap-2">
                  <KinSwitch v-model="swSmOn" size="sm" />
                  <span class="text-[10px] text-ink-tertiary">sm 28×16</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <KinSwitch v-model="swOn" size="md" />
                  <span class="text-[10px] text-ink-tertiary">md 36×22</span>
                </div>
              </div>
            </div>

            <!-- 4 accent colours -->
            <div>
              <p class="text-caption text-ink-tertiary mb-3">Accent colours — all on</p>
              <div class="flex flex-wrap items-start gap-8">
                <div class="flex flex-col items-center gap-2">
                  <KinSwitch v-model="swLavender" color="lavender" />
                  <span class="text-[10px] text-ink-tertiary">lavender</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <KinSwitch v-model="swPeach" color="peach" />
                  <span class="text-[10px] text-ink-tertiary">peach</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <KinSwitch v-model="swMint" color="mint" />
                  <span class="text-[10px] text-ink-tertiary">mint</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <KinSwitch v-model="swSun" color="sun" />
                  <span class="text-[10px] text-ink-tertiary">sun</span>
                </div>
              </div>
            </div>

            <!-- With description -->
            <div>
              <p class="text-caption text-ink-tertiary mb-3">With label + description (toggle widget only)</p>
              <KinSwitch
                v-model="swWithDesc"
                label="Send morning briefing"
                description="Daily summary delivered at 7 AM"
              />
            </div>

            <!-- Settings rows -->
            <div>
              <p class="text-caption text-ink-tertiary mb-3">Settings row context</p>
              <ul class="space-y-0 max-w-sm">
                <li
                  v-for="(setting, i) in swSettingsLabels"
                  :key="i"
                  :class="['py-3', i < swSettingsLabels.length - 1 ? 'border-b border-border-subtle' : '']"
                >
                  <KinSwitch
                    v-model="swSettings[i]"
                    :label="setting.title"
                    :description="setting.helper"
                    color="mint"
                  />
                </li>
              </ul>
            </div>
          </div><!-- /light switch -->

          <!-- DARK PANEL -->
          <div class="dark rounded-2xl border border-border-subtle bg-surface-app p-6 space-y-8">
            <p class="text-caption font-semibold uppercase tracking-widest text-ink-tertiary">Dark mode</p>

            <!-- States dark -->
            <div>
              <p class="text-caption text-ink-tertiary mb-3">States — md</p>
              <div class="flex flex-wrap items-start gap-8">
                <div class="flex flex-col items-center gap-2">
                  <KinSwitch :model-value="false" />
                  <span class="text-[10px] text-ink-tertiary">off</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <KinSwitch :model-value="true" />
                  <span class="text-[10px] text-ink-tertiary">on</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <KinSwitch :model-value="false" disabled />
                  <span class="text-[10px] text-ink-tertiary">disabled off</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <KinSwitch :model-value="true" disabled />
                  <span class="text-[10px] text-ink-tertiary">disabled on</span>
                </div>
              </div>
            </div>

            <!-- 4 accent colours dark -->
            <div>
              <p class="text-caption text-ink-tertiary mb-3">Accent colours — all on</p>
              <div class="flex flex-wrap items-start gap-8">
                <div class="flex flex-col items-center gap-2">
                  <KinSwitch :model-value="true" color="lavender" />
                  <span class="text-[10px] text-ink-tertiary">lavender</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <KinSwitch :model-value="true" color="peach" />
                  <span class="text-[10px] text-ink-tertiary">peach</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <KinSwitch :model-value="true" color="mint" />
                  <span class="text-[10px] text-ink-tertiary">mint</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <KinSwitch :model-value="true" color="sun" />
                  <span class="text-[10px] text-ink-tertiary">sun</span>
                </div>
              </div>
            </div>

            <!-- Settings rows dark -->
            <div>
              <p class="text-caption text-ink-tertiary mb-3">Settings row context</p>
              <ul class="space-y-0 max-w-sm">
                <li
                  v-for="(setting, i) in swSettingsLabels"
                  :key="i"
                  :class="['py-3', i < swSettingsLabels.length - 1 ? 'border-b border-border-subtle' : '']"
                >
                  <KinSwitch
                    v-model="swSettingsDark[i]"
                    :label="setting.title"
                    :description="setting.helper"
                    color="lavender"
                  />
                </li>
              </ul>
            </div>
          </div><!-- /dark switch -->

        </div>
      </VariantFrame>

      <p class="mt-4 text-body-sm text-ink-secondary px-1">
        Use for any binary on/off setting. Prefer over checkbox for settings contexts where the control represents a stateful mode rather than a completion or selection. The 200ms thumb slide makes the transition feel immediate without being jarring. Reduced-motion environments will see an instant jump instead.
      </p>
    </section>

  </ComponentPage>
</template>
