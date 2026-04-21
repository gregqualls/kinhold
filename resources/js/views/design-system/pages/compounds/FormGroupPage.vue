<script setup>
import { ref } from 'vue'
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import KinFormGroup from '@/components/design-system/KinFormGroup.vue'
import KinInput from '@/components/design-system/KinInput.vue'
import KinTextarea from '@/components/design-system/KinTextarea.vue'
import KinSwitch from '@/components/design-system/KinSwitch.vue'
import {
  SparklesIcon,
  ExclamationCircleIcon,
  CheckCircleIcon,
  ChevronDownIcon,
  EnvelopeIcon,
} from '@heroicons/vue/24/outline'

// Kin component preview reactive state
const kinEmail     = ref('')
const kinPassword  = ref('')
const kinBio       = ref('')
const kinNotifs    = ref(true)
const kinBadEmail  = ref('bad@')

// ── Palette ──────────────────────────────────────────────────────────────────
const L = {
  surfaceApp:    '#FAF8F5',
  surfaceRaised: '#FFFFFF',
  surfaceSunken: '#F5F2EE',
  inkPrimary:    '#1C1C1E',
  inkSecondary:  '#6B6966',
  inkTertiary:   '#9C9895',
  inkInverse:    '#FAF8F5',
  borderSubtle:  '#E8E4DF',
  borderStrong:  '#BCB8B2',
  accents: {
    lavender: { soft: '#EAE6F8', bold: '#6856B2' },
  },
  status: {
    success: { soft: '#E1F0E7', bold: '#4D8C6A' },
    failed:  { soft: '#F4DADA', bold: '#BA4A4A' },
    warning: { soft: '#F8ECCF', bold: '#C48C24' },
  },
}

const D = {
  surfaceApp:     '#141311',
  surfaceRaised:  '#1C1B19',
  surfaceSunken:  '#161513',
  surfaceOverlay: '#242220',
  inkPrimary:     '#F0EDE9',
  inkSecondary:   '#A09C97',
  inkTertiary:    '#6E6B67',
  inkInverse:     '#1C1C1E',
  borderSubtle:   '#2C2A27',
  borderStrong:   '#403E3A',
  accents: {
    lavender: { soft: '#302A48', bold: '#B6A8E6' },
  },
  status: {
    success: { soft: '#1C3A2A', bold: '#6CC498' },
    failed:  { soft: '#3C1E1E', bold: '#E67070' },
    warning: { soft: '#3C340E', bold: '#E6C452' },
  },
}

// ── Reactive field values ────────────────────────────────────────────────────
// Variant A (top-label)
const aLtEmail    = ref('')
const aLtTextarea = ref('')
const aLtSelect   = ref('')
const aLtChecks   = ref([])
const aLtToggle   = ref(false)
const aDkEmail    = ref('')
const aDkTextarea = ref('')
const aDkSelect   = ref('')
const aDkChecks   = ref([])
const aDkToggle   = ref(false)

// Variant B (floating-label)
const bLtEmail    = ref('')
const bLtTextarea = ref('')
const bLtSelect   = ref('')
const bDkEmail    = ref('')
const bDkTextarea = ref('')
const bDkSelect   = ref('')

// Variant C (inline-label)
const cLtEmail    = ref('')
const cLtTextarea = ref('')
const cLtSelect   = ref('')
const cLtChecks   = ref([])
const cLtToggle   = ref(false)
const cDkEmail    = ref('')
const cDkTextarea = ref('')
const cDkSelect   = ref('')
const cDkChecks   = ref([])
const cDkToggle   = ref(false)

// Focus state tracking (for inline demo of focus ring)
const focused = ref({})
function onFocus(k)  { focused.value = { ...focused.value, [k]: true  } }
function onBlur(k)   { focused.value = { ...focused.value, [k]: false } }

// ── Style helpers ─────────────────────────────────────────────────────────────
function inputBase(p, isFocused, state) {
  const border = state === 'error'
    ? p.status.failed.bold
    : state === 'success'
      ? p.status.success.bold
      : isFocused
        ? p.accents.lavender.bold
        : p.borderStrong
  const shadow = isFocused
    ? `0 0 0 3px ${p.accents.lavender.soft}, inset 0 0 0 1px ${p.accents.lavender.bold}`
    : state === 'error'
      ? `0 0 0 3px ${p.status.failed.soft}`
      : state === 'success'
        ? `0 0 0 3px ${p.status.success.soft}`
        : 'none'
  return {
    background: p.surfaceRaised,
    border: `1px solid ${border}`,
    borderRadius: '12px',
    color: p.inkPrimary,
    fontSize: '14px',
    outline: 'none',
    boxShadow: shadow,
    transition: 'border-color 150ms, box-shadow 150ms',
    width: '100%',
  }
}

function inputDisabled(p) {
  return {
    ...inputBase(p, false, null),
    background: p.surfaceSunken,
    color: p.inkTertiary,
    cursor: 'not-allowed',
    opacity: '0.6',
    border: `1px solid ${p.borderSubtle}`,
  }
}

function labelStyle(p) {
  return { color: p.inkPrimary, fontSize: '13px', fontWeight: '500', display: 'block', marginBottom: '4px' }
}

function helperStyle(p, state) {
  const color = state === 'error'
    ? p.status.failed.bold
    : state === 'success'
      ? p.status.success.bold
      : p.inkTertiary
  return { color, fontSize: '12px', marginTop: '4px', display: 'flex', alignItems: 'center', gap: '4px' }
}
</script>

<template>
  <ComponentPage
    title="4.10 FormGroup"
    description="Label + input + helper + error — the atomic form-field unit. Resolves label placement, error display, and validation timing for every form in the app."
    status="chosen"
  >

    <!-- ══════════════════════════════════════════════════════════════════════
         VARIANT A — Top label
         ═══════════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="A" caption="Top label — label above field, helper below. Default for all general forms.">
        <div class="w-full space-y-10">

          <!-- LIGHT PANEL -->
          <div class="rounded-2xl border p-6 space-y-8"
               :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>

            <!-- Text input — default -->
            <div class="space-y-1 max-w-sm">
              <p class="text-[11px] font-medium uppercase tracking-wider mb-3" :style="{ color: L.inkTertiary }">Text input — default</p>
              <label :style="labelStyle(L)">
                Email address <span :style="{ color: L.inkSecondary }">*</span>
              </label>
              <div class="relative">
                <EnvelopeIcon class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 pointer-events-none" :style="{ color: L.inkTertiary }" />
                <input
                  v-model="aLtEmail"
                  type="email"
                  placeholder="you@family.com"
                  :style="{ ...inputBase(L, !!focused['a-lt-email'], null), paddingLeft: '36px', paddingRight: '12px', paddingTop: '9px', paddingBottom: '9px' }"
                  @focus="onFocus('a-lt-email')"
                  @blur="onBlur('a-lt-email')"
                />
              </div>
              <p :style="helperStyle(L, null)">We'll never share this with anyone.</p>
            </div>

            <!-- Focused state -->
            <div class="space-y-1 max-w-sm">
              <p class="text-[11px] font-medium uppercase tracking-wider mb-3" :style="{ color: L.inkTertiary }">Focused state</p>
              <label :style="labelStyle(L)">
                Email address <span :style="{ color: L.inkSecondary }">*</span>
              </label>
              <input
                type="email"
                value="greg@kinhold.app"
                placeholder="you@family.com"
                :style="{ ...inputBase(L, true, null), paddingLeft: '12px', paddingRight: '12px', paddingTop: '9px', paddingBottom: '9px' }"
              />
              <p :style="helperStyle(L, null)">We'll never share this with anyone.</p>
            </div>

            <!-- Error state -->
            <div class="space-y-1 max-w-sm">
              <p class="text-[11px] font-medium uppercase tracking-wider mb-3" :style="{ color: L.inkTertiary }">Error state</p>
              <label :style="labelStyle(L)">
                Email address <span :style="{ color: L.inkSecondary }">*</span>
              </label>
              <input
                type="email"
                value="not-an-email"
                placeholder="you@family.com"
                :style="{ ...inputBase(L, false, 'error'), paddingLeft: '12px', paddingRight: '12px', paddingTop: '9px', paddingBottom: '9px' }"
              />
              <p :style="helperStyle(L, 'error')">
                <ExclamationCircleIcon class="w-3.5 h-3.5 flex-shrink-0" />
                Enter a valid email address.
              </p>
            </div>

            <!-- Success state -->
            <div class="space-y-1 max-w-sm">
              <p class="text-[11px] font-medium uppercase tracking-wider mb-3" :style="{ color: L.inkTertiary }">Success state</p>
              <label :style="labelStyle(L)">
                Email address <span :style="{ color: L.inkSecondary }">*</span>
              </label>
              <input
                type="email"
                value="greg@kinhold.app"
                placeholder="you@family.com"
                :style="{ ...inputBase(L, false, 'success'), paddingLeft: '12px', paddingRight: '12px', paddingTop: '9px', paddingBottom: '9px' }"
              />
              <p :style="helperStyle(L, 'success')">
                <CheckCircleIcon class="w-3.5 h-3.5 flex-shrink-0" />
                Looks good!
              </p>
            </div>

            <!-- Disabled state -->
            <div class="space-y-1 max-w-sm">
              <p class="text-[11px] font-medium uppercase tracking-wider mb-3" :style="{ color: L.inkTertiary }">Disabled state</p>
              <label :style="{ ...labelStyle(L), opacity: '0.5' }">
                Email address
              </label>
              <input
                type="email"
                value="greg@kinhold.app"
                disabled
                :style="{ ...inputDisabled(L), paddingLeft: '12px', paddingRight: '12px', paddingTop: '9px', paddingBottom: '9px' }"
              />
              <p :style="{ ...helperStyle(L, null), opacity: '0.5' }">Cannot be changed.</p>
            </div>

            <!-- Textarea -->
            <div class="space-y-1 max-w-sm">
              <p class="text-[11px] font-medium uppercase tracking-wider mb-3" :style="{ color: L.inkTertiary }">Textarea (3 rows)</p>
              <label :style="labelStyle(L)">Notes</label>
              <textarea
                v-model="aLtTextarea"
                rows="3"
                placeholder="Add a note…"
                :style="{ ...inputBase(L, !!focused['a-lt-ta'], null), padding: '9px 12px', resize: 'vertical', lineHeight: '1.5' }"
                @focus="onFocus('a-lt-ta')"
                @blur="onBlur('a-lt-ta')"
              />
              <p :style="helperStyle(L, null)">Optional. Visible to all family members.</p>
            </div>

            <!-- Select -->
            <div class="space-y-1 max-w-sm">
              <p class="text-[11px] font-medium uppercase tracking-wider mb-3" :style="{ color: L.inkTertiary }">Select with chevron</p>
              <label :style="labelStyle(L)">Priority <span :style="{ color: L.inkSecondary }">*</span></label>
              <div class="relative">
                <select
                  v-model="aLtSelect"
                  :style="{ ...inputBase(L, !!focused['a-lt-sel'], null), padding: '9px 36px 9px 12px', appearance: 'none', cursor: 'pointer' }"
                  @focus="onFocus('a-lt-sel')"
                  @blur="onBlur('a-lt-sel')"
                >
                  <option value="" disabled>Select priority…</option>
                  <option value="low">Low</option>
                  <option value="medium">Medium</option>
                  <option value="high">High</option>
                </select>
                <ChevronDownIcon class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 pointer-events-none" :style="{ color: L.inkTertiary }" />
              </div>
              <p :style="helperStyle(L, null)">Sets notification urgency.</p>
            </div>

            <!-- Checkbox group -->
            <div class="space-y-1 max-w-sm">
              <p class="text-[11px] font-medium uppercase tracking-wider mb-3" :style="{ color: L.inkTertiary }">Checkbox group</p>
              <label :style="labelStyle(L)">Notify me when <span :style="{ color: L.inkSecondary }">*</span></label>
              <div class="space-y-2 mt-2">
                <label v-for="opt in ['Task assigned', 'Task completed', 'New vault entry']" :key="opt"
                       class="flex items-center gap-2.5 cursor-pointer">
                  <input
                    type="checkbox"
                    :value="opt"
                    v-model="aLtChecks"
                    class="w-4 h-4 rounded cursor-pointer"
                    :style="{ accentColor: L.accents.lavender.bold }"
                  />
                  <span :style="{ color: L.inkPrimary, fontSize: '14px' }">{{ opt }}</span>
                </label>
              </div>
              <p :style="helperStyle(L, null)">Choose one or more.</p>
            </div>

            <!-- Toggle row -->
            <div class="space-y-1 max-w-sm">
              <p class="text-[11px] font-medium uppercase tracking-wider mb-3" :style="{ color: L.inkTertiary }">Toggle / switch row</p>
              <div class="flex items-center justify-between rounded-xl border px-4 py-3"
                   :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle }">
                <div>
                  <p :style="{ color: L.inkPrimary, fontSize: '14px', fontWeight: '500' }">Email notifications</p>
                  <p :style="{ color: L.inkTertiary, fontSize: '12px', marginTop: '1px' }">Receive a daily digest.</p>
                </div>
                <button
                  role="switch"
                  :aria-checked="aLtToggle"
                  class="toggle-track"
                  :class="aLtToggle ? 'toggle-on-lt' : 'toggle-off-lt'"
                  @click="aLtToggle = !aLtToggle"
                >
                  <span class="toggle-thumb" :class="aLtToggle ? 'translate-x-[18px]' : 'translate-x-0'" />
                </button>
              </div>
            </div>
          </div><!-- /light -->

          <!-- DARK PANEL -->
          <div class="rounded-2xl border p-6 space-y-8"
               :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

            <!-- Default -->
            <div class="space-y-1 max-w-sm">
              <p class="text-[11px] font-medium uppercase tracking-wider mb-3" :style="{ color: D.inkTertiary }">Text input — default</p>
              <label :style="labelStyle(D)">
                Email address <span :style="{ color: D.inkSecondary }">*</span>
              </label>
              <div class="relative">
                <EnvelopeIcon class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 pointer-events-none" :style="{ color: D.inkTertiary }" />
                <input
                  v-model="aDkEmail"
                  type="email"
                  placeholder="you@family.com"
                  :style="{ ...inputBase(D, !!focused['a-dk-email'], null), paddingLeft: '36px', paddingRight: '12px', paddingTop: '9px', paddingBottom: '9px' }"
                  @focus="onFocus('a-dk-email')"
                  @blur="onBlur('a-dk-email')"
                />
              </div>
              <p :style="helperStyle(D, null)">We'll never share this with anyone.</p>
            </div>

            <!-- Error -->
            <div class="space-y-1 max-w-sm">
              <p class="text-[11px] font-medium uppercase tracking-wider mb-3" :style="{ color: D.inkTertiary }">Error state</p>
              <label :style="labelStyle(D)">
                Email address <span :style="{ color: D.inkSecondary }">*</span>
              </label>
              <input
                type="email"
                value="not-an-email"
                :style="{ ...inputBase(D, false, 'error'), padding: '9px 12px' }"
              />
              <p :style="helperStyle(D, 'error')">
                <ExclamationCircleIcon class="w-3.5 h-3.5 flex-shrink-0" />
                Enter a valid email address.
              </p>
            </div>

            <!-- Success -->
            <div class="space-y-1 max-w-sm">
              <p class="text-[11px] font-medium uppercase tracking-wider mb-3" :style="{ color: D.inkTertiary }">Success state</p>
              <label :style="labelStyle(D)">
                Email address <span :style="{ color: D.inkSecondary }">*</span>
              </label>
              <input
                type="email"
                value="greg@kinhold.app"
                :style="{ ...inputBase(D, false, 'success'), padding: '9px 12px' }"
              />
              <p :style="helperStyle(D, 'success')">
                <CheckCircleIcon class="w-3.5 h-3.5 flex-shrink-0" />
                Looks good!
              </p>
            </div>

            <!-- Disabled -->
            <div class="space-y-1 max-w-sm">
              <p class="text-[11px] font-medium uppercase tracking-wider mb-3" :style="{ color: D.inkTertiary }">Disabled state</p>
              <label :style="{ ...labelStyle(D), opacity: '0.5' }">Email address</label>
              <input
                type="email"
                value="greg@kinhold.app"
                disabled
                :style="{ ...inputDisabled(D), padding: '9px 12px' }"
              />
            </div>

            <!-- Textarea -->
            <div class="space-y-1 max-w-sm">
              <p class="text-[11px] font-medium uppercase tracking-wider mb-3" :style="{ color: D.inkTertiary }">Textarea</p>
              <label :style="labelStyle(D)">Notes</label>
              <textarea
                v-model="aDkTextarea"
                rows="3"
                placeholder="Add a note…"
                :style="{ ...inputBase(D, !!focused['a-dk-ta'], null), padding: '9px 12px', resize: 'vertical', lineHeight: '1.5' }"
                @focus="onFocus('a-dk-ta')"
                @blur="onBlur('a-dk-ta')"
              />
            </div>

            <!-- Select -->
            <div class="space-y-1 max-w-sm">
              <p class="text-[11px] font-medium uppercase tracking-wider mb-3" :style="{ color: D.inkTertiary }">Select</p>
              <label :style="labelStyle(D)">Priority <span :style="{ color: D.inkSecondary }">*</span></label>
              <div class="relative">
                <select
                  v-model="aDkSelect"
                  :style="{ ...inputBase(D, !!focused['a-dk-sel'], null), padding: '9px 36px 9px 12px', appearance: 'none', cursor: 'pointer' }"
                  @focus="onFocus('a-dk-sel')"
                  @blur="onBlur('a-dk-sel')"
                >
                  <option value="" disabled>Select priority…</option>
                  <option value="low">Low</option>
                  <option value="medium">Medium</option>
                  <option value="high">High</option>
                </select>
                <ChevronDownIcon class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 pointer-events-none" :style="{ color: D.inkTertiary }" />
              </div>
            </div>

            <!-- Checkbox group -->
            <div class="space-y-1 max-w-sm">
              <p class="text-[11px] font-medium uppercase tracking-wider mb-3" :style="{ color: D.inkTertiary }">Checkbox group</p>
              <label :style="labelStyle(D)">Notify me when <span :style="{ color: D.inkSecondary }">*</span></label>
              <div class="space-y-2 mt-2">
                <label v-for="opt in ['Task assigned', 'Task completed', 'New vault entry']" :key="opt"
                       class="flex items-center gap-2.5 cursor-pointer">
                  <input
                    type="checkbox"
                    :value="opt"
                    v-model="aDkChecks"
                    class="w-4 h-4 rounded cursor-pointer"
                    :style="{ accentColor: D.accents.lavender.bold }"
                  />
                  <span :style="{ color: D.inkPrimary, fontSize: '14px' }">{{ opt }}</span>
                </label>
              </div>
            </div>

            <!-- Toggle row -->
            <div class="space-y-1 max-w-sm">
              <p class="text-[11px] font-medium uppercase tracking-wider mb-3" :style="{ color: D.inkTertiary }">Toggle row</p>
              <div class="flex items-center justify-between rounded-xl border px-4 py-3"
                   :style="{ background: D.surfaceRaised, borderColor: D.borderSubtle }">
                <div>
                  <p :style="{ color: D.inkPrimary, fontSize: '14px', fontWeight: '500' }">Email notifications</p>
                  <p :style="{ color: D.inkTertiary, fontSize: '12px', marginTop: '1px' }">Receive a daily digest.</p>
                </div>
                <button
                  role="switch"
                  :aria-checked="aDkToggle"
                  class="toggle-track"
                  :class="aDkToggle ? 'toggle-on-dk' : 'toggle-off-dk'"
                  @click="aDkToggle = !aDkToggle"
                >
                  <span class="toggle-thumb" :class="aDkToggle ? 'translate-x-[18px]' : 'translate-x-0'" />
                </button>
              </div>
            </div>
          </div><!-- /dark -->
        </div>
      </VariantFrame>
      <p class="mt-3 text-sm px-1" :style="{ color: L.inkSecondary }">
        Validation strategy: <strong>on-blur</strong> by default (validate when user leaves the field). Switch to <strong>on-submit</strong> for high-cost forms where partial-fill is expected (e.g. long registration or multi-step onboarding). The variant is structural — validation timing is a prop concern, not a layout concern.
      </p>
    </section>


    <!-- ══════════════════════════════════════════════════════════════════════
         VARIANT B — Floating label
         ═══════════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="B" caption="Floating label — animates from inside the field to above the border. Hero moments: login, onboarding.">
        <div class="w-full space-y-10">

          <!-- LIGHT PANEL -->
          <div class="rounded-2xl border p-6 space-y-8"
               :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>

            <!-- Default (empty, unfocused) -->
            <div class="space-y-1 max-w-sm">
              <p class="text-[11px] font-medium uppercase tracking-wider mb-3" :style="{ color: L.inkTertiary }">Text input — default (empty)</p>
              <div class="float-group">
                <input
                  v-model="bLtEmail"
                  type="email"
                  id="b-lt-email"
                  placeholder=" "
                  :style="{ ...inputBase(L, !!focused['b-lt-email'], null), padding: '18px 12px 6px 12px', height: '52px' }"
                  @focus="onFocus('b-lt-email')"
                  @blur="onBlur('b-lt-email')"
                />
                <label
                  for="b-lt-email"
                  class="float-label"
                  :class="(bLtEmail || focused['b-lt-email']) ? 'floated' : ''"
                  :style="{ color: (bLtEmail || focused['b-lt-email']) ? L.accents.lavender.bold : L.inkTertiary }"
                >Email address <span :style="{ color: L.inkSecondary }">*</span></label>
              </div>
              <p :style="helperStyle(L, null)">We'll never share this with anyone.</p>
            </div>

            <!-- Focused / filled -->
            <div class="space-y-1 max-w-sm">
              <p class="text-[11px] font-medium uppercase tracking-wider mb-3" :style="{ color: L.inkTertiary }">Focused + filled</p>
              <div class="float-group">
                <input
                  type="email"
                  id="b-lt-email-filled"
                  placeholder=" "
                  value="greg@kinhold.app"
                  :style="{ ...inputBase(L, true, null), padding: '18px 12px 6px 12px', height: '52px' }"
                />
                <label
                  for="b-lt-email-filled"
                  class="float-label floated"
                  :style="{ color: L.accents.lavender.bold }"
                >Email address <span :style="{ color: L.inkSecondary }">*</span></label>
              </div>
              <p :style="helperStyle(L, null)">We'll never share this with anyone.</p>
            </div>

            <!-- Error state -->
            <div class="space-y-1 max-w-sm">
              <p class="text-[11px] font-medium uppercase tracking-wider mb-3" :style="{ color: L.inkTertiary }">Error state</p>
              <div class="float-group">
                <input
                  type="email"
                  id="b-lt-email-err"
                  placeholder=" "
                  value="not-an-email"
                  :style="{ ...inputBase(L, false, 'error'), padding: '18px 12px 6px 12px', height: '52px' }"
                />
                <label for="b-lt-email-err" class="float-label floated" :style="{ color: L.status.failed.bold }">
                  Email address <span :style="{ color: L.inkSecondary }">*</span>
                </label>
              </div>
              <p :style="helperStyle(L, 'error')">
                <ExclamationCircleIcon class="w-3.5 h-3.5 flex-shrink-0" />
                Enter a valid email address.
              </p>
            </div>

            <!-- Success state -->
            <div class="space-y-1 max-w-sm">
              <p class="text-[11px] font-medium uppercase tracking-wider mb-3" :style="{ color: L.inkTertiary }">Success state</p>
              <div class="float-group">
                <input
                  type="email"
                  id="b-lt-email-ok"
                  placeholder=" "
                  value="greg@kinhold.app"
                  :style="{ ...inputBase(L, false, 'success'), padding: '18px 12px 6px 12px', height: '52px' }"
                />
                <label for="b-lt-email-ok" class="float-label floated" :style="{ color: L.status.success.bold }">
                  Email address <span :style="{ color: L.inkSecondary }">*</span>
                </label>
              </div>
              <p :style="helperStyle(L, 'success')">
                <CheckCircleIcon class="w-3.5 h-3.5 flex-shrink-0" />
                Looks good!
              </p>
            </div>

            <!-- Disabled state -->
            <div class="space-y-1 max-w-sm">
              <p class="text-[11px] font-medium uppercase tracking-wider mb-3" :style="{ color: L.inkTertiary }">Disabled state</p>
              <div class="float-group" style="opacity:0.6">
                <input
                  type="email"
                  id="b-lt-email-dis"
                  placeholder=" "
                  value="greg@kinhold.app"
                  disabled
                  :style="{ ...inputDisabled(L), padding: '18px 12px 6px 12px', height: '52px' }"
                />
                <label for="b-lt-email-dis" class="float-label floated" :style="{ color: L.inkTertiary }">
                  Email address
                </label>
              </div>
            </div>

            <!-- Textarea -->
            <div class="space-y-1 max-w-sm">
              <p class="text-[11px] font-medium uppercase tracking-wider mb-3" :style="{ color: L.inkTertiary }">Textarea (floating label)</p>
              <div class="float-group">
                <textarea
                  v-model="bLtTextarea"
                  id="b-lt-ta"
                  placeholder=" "
                  rows="3"
                  :style="{ ...inputBase(L, !!focused['b-lt-ta'], null), padding: '22px 12px 6px 12px', resize: 'vertical', lineHeight: '1.5' }"
                  @focus="onFocus('b-lt-ta')"
                  @blur="onBlur('b-lt-ta')"
                />
                <label
                  for="b-lt-ta"
                  class="float-label"
                  :class="(bLtTextarea || focused['b-lt-ta']) ? 'floated' : ''"
                  :style="{ color: (bLtTextarea || focused['b-lt-ta']) ? L.accents.lavender.bold : L.inkTertiary }"
                >Notes</label>
              </div>
            </div>

            <!-- Select -->
            <div class="space-y-1 max-w-sm">
              <p class="text-[11px] font-medium uppercase tracking-wider mb-3" :style="{ color: L.inkTertiary }">Select (floating label)</p>
              <div class="float-group">
                <select
                  v-model="bLtSelect"
                  id="b-lt-sel"
                  :style="{ ...inputBase(L, !!focused['b-lt-sel'], null), padding: '18px 36px 6px 12px', height: '52px', appearance: 'none', cursor: 'pointer' }"
                  @focus="onFocus('b-lt-sel')"
                  @blur="onBlur('b-lt-sel')"
                >
                  <option value="" disabled></option>
                  <option value="low">Low</option>
                  <option value="medium">Medium</option>
                  <option value="high">High</option>
                </select>
                <label
                  for="b-lt-sel"
                  class="float-label"
                  :class="(bLtSelect || focused['b-lt-sel']) ? 'floated' : ''"
                  :style="{ color: (bLtSelect || focused['b-lt-sel']) ? L.accents.lavender.bold : L.inkTertiary }"
                >Priority <span :style="{ color: L.inkSecondary }">*</span></label>
                <ChevronDownIcon class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 pointer-events-none" :style="{ color: L.inkTertiary }" />
              </div>
            </div>
          </div><!-- /light -->

          <!-- DARK PANEL -->
          <div class="rounded-2xl border p-6 space-y-8"
               :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

            <!-- Default -->
            <div class="space-y-1 max-w-sm">
              <p class="text-[11px] font-medium uppercase tracking-wider mb-3" :style="{ color: D.inkTertiary }">Text input — default</p>
              <div class="float-group">
                <input
                  v-model="bDkEmail"
                  type="email"
                  id="b-dk-email"
                  placeholder=" "
                  :style="{ ...inputBase(D, !!focused['b-dk-email'], null), padding: '18px 12px 6px 12px', height: '52px' }"
                  @focus="onFocus('b-dk-email')"
                  @blur="onBlur('b-dk-email')"
                />
                <label
                  for="b-dk-email"
                  class="float-label"
                  :class="(bDkEmail || focused['b-dk-email']) ? 'floated' : ''"
                  :style="{ color: (bDkEmail || focused['b-dk-email']) ? D.accents.lavender.bold : D.inkTertiary }"
                >Email address <span :style="{ color: D.inkSecondary }">*</span></label>
              </div>
              <p :style="helperStyle(D, null)">We'll never share this with anyone.</p>
            </div>

            <!-- Error -->
            <div class="space-y-1 max-w-sm">
              <p class="text-[11px] font-medium uppercase tracking-wider mb-3" :style="{ color: D.inkTertiary }">Error state</p>
              <div class="float-group">
                <input
                  type="email"
                  id="b-dk-email-err"
                  placeholder=" "
                  value="not-an-email"
                  :style="{ ...inputBase(D, false, 'error'), padding: '18px 12px 6px 12px', height: '52px' }"
                />
                <label for="b-dk-email-err" class="float-label floated" :style="{ color: D.status.failed.bold }">
                  Email address <span :style="{ color: D.inkSecondary }">*</span>
                </label>
              </div>
              <p :style="helperStyle(D, 'error')">
                <ExclamationCircleIcon class="w-3.5 h-3.5 flex-shrink-0" />
                Enter a valid email address.
              </p>
            </div>

            <!-- Success -->
            <div class="space-y-1 max-w-sm">
              <p class="text-[11px] font-medium uppercase tracking-wider mb-3" :style="{ color: D.inkTertiary }">Success state</p>
              <div class="float-group">
                <input
                  type="email"
                  id="b-dk-email-ok"
                  placeholder=" "
                  value="greg@kinhold.app"
                  :style="{ ...inputBase(D, false, 'success'), padding: '18px 12px 6px 12px', height: '52px' }"
                />
                <label for="b-dk-email-ok" class="float-label floated" :style="{ color: D.status.success.bold }">
                  Email address <span :style="{ color: D.inkSecondary }">*</span>
                </label>
              </div>
              <p :style="helperStyle(D, 'success')">
                <CheckCircleIcon class="w-3.5 h-3.5 flex-shrink-0" />
                Looks good!
              </p>
            </div>

            <!-- Disabled -->
            <div class="space-y-1 max-w-sm">
              <p class="text-[11px] font-medium uppercase tracking-wider mb-3" :style="{ color: D.inkTertiary }">Disabled state</p>
              <div class="float-group" style="opacity:0.6">
                <input
                  type="email"
                  id="b-dk-email-dis"
                  placeholder=" "
                  value="greg@kinhold.app"
                  disabled
                  :style="{ ...inputDisabled(D), padding: '18px 12px 6px 12px', height: '52px' }"
                />
                <label for="b-dk-email-dis" class="float-label floated" :style="{ color: D.inkTertiary }">Email address</label>
              </div>
            </div>

            <!-- Textarea -->
            <div class="space-y-1 max-w-sm">
              <p class="text-[11px] font-medium uppercase tracking-wider mb-3" :style="{ color: D.inkTertiary }">Textarea</p>
              <div class="float-group">
                <textarea
                  v-model="bDkTextarea"
                  id="b-dk-ta"
                  placeholder=" "
                  rows="3"
                  :style="{ ...inputBase(D, !!focused['b-dk-ta'], null), padding: '22px 12px 6px 12px', resize: 'vertical', lineHeight: '1.5' }"
                  @focus="onFocus('b-dk-ta')"
                  @blur="onBlur('b-dk-ta')"
                />
                <label
                  for="b-dk-ta"
                  class="float-label"
                  :class="(bDkTextarea || focused['b-dk-ta']) ? 'floated' : ''"
                  :style="{ color: (bDkTextarea || focused['b-dk-ta']) ? D.accents.lavender.bold : D.inkTertiary }"
                >Notes</label>
              </div>
            </div>

            <!-- Select -->
            <div class="space-y-1 max-w-sm">
              <p class="text-[11px] font-medium uppercase tracking-wider mb-3" :style="{ color: D.inkTertiary }">Select</p>
              <div class="float-group">
                <select
                  v-model="bDkSelect"
                  id="b-dk-sel"
                  :style="{ ...inputBase(D, !!focused['b-dk-sel'], null), padding: '18px 36px 6px 12px', height: '52px', appearance: 'none', cursor: 'pointer' }"
                  @focus="onFocus('b-dk-sel')"
                  @blur="onBlur('b-dk-sel')"
                >
                  <option value="" disabled></option>
                  <option value="low">Low</option>
                  <option value="medium">Medium</option>
                  <option value="high">High</option>
                </select>
                <label
                  for="b-dk-sel"
                  class="float-label"
                  :class="(bDkSelect || focused['b-dk-sel']) ? 'floated' : ''"
                  :style="{ color: (bDkSelect || focused['b-dk-sel']) ? D.accents.lavender.bold : D.inkTertiary }"
                >Priority <span :style="{ color: D.inkSecondary }">*</span></label>
                <ChevronDownIcon class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 pointer-events-none" :style="{ color: D.inkTertiary }" />
              </div>
            </div>
          </div><!-- /dark -->
        </div>
      </VariantFrame>
      <p class="mt-3 text-sm px-1" :style="{ color: L.inkSecondary }">
        Validation strategy: <strong>on-blur</strong> by default. The floating label must never use <code class="text-xs px-1 rounded" :style="{ background: L.surfaceSunken }">placeholder</code> text — the label IS the placeholder. The animation is suppressed when <code class="text-xs px-1 rounded" :style="{ background: L.surfaceSunken }">prefers-reduced-motion: reduce</code> is set.
      </p>
    </section>


    <!-- ══════════════════════════════════════════════════════════════════════
         VARIANT C — Inline label
         ═══════════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="C" caption="Inline label — label left on a fixed column, field fills remaining width. Settings panels. Stacks at <640px.">
        <div class="w-full space-y-10">

          <!-- LIGHT PANEL -->
          <div class="rounded-2xl border p-6 space-y-6"
               :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>

            <!-- Text input — default -->
            <div>
              <p class="text-[11px] font-medium uppercase tracking-wider mb-3" :style="{ color: L.inkTertiary }">Text input — default</p>
              <div class="inline-row">
                <label class="inline-label" :style="labelStyle(L)">
                  Email address <span :style="{ color: L.inkSecondary }">*</span>
                </label>
                <div class="inline-field">
                  <input
                    v-model="cLtEmail"
                    type="email"
                    placeholder="you@family.com"
                    :style="{ ...inputBase(L, !!focused['c-lt-email'], null), padding: '9px 12px' }"
                    @focus="onFocus('c-lt-email')"
                    @blur="onBlur('c-lt-email')"
                  />
                  <p :style="helperStyle(L, null)">We'll never share this with anyone.</p>
                </div>
              </div>
            </div>

            <!-- Focused -->
            <div>
              <p class="text-[11px] font-medium uppercase tracking-wider mb-3" :style="{ color: L.inkTertiary }">Focused state</p>
              <div class="inline-row">
                <label class="inline-label" :style="labelStyle(L)">
                  Email address <span :style="{ color: L.inkSecondary }">*</span>
                </label>
                <div class="inline-field">
                  <input
                    type="email"
                    value="greg@kinhold.app"
                    :style="{ ...inputBase(L, true, null), padding: '9px 12px' }"
                  />
                </div>
              </div>
            </div>

            <!-- Error -->
            <div>
              <p class="text-[11px] font-medium uppercase tracking-wider mb-3" :style="{ color: L.inkTertiary }">Error state</p>
              <div class="inline-row">
                <label class="inline-label" :style="labelStyle(L)">
                  Email address <span :style="{ color: L.inkSecondary }">*</span>
                </label>
                <div class="inline-field">
                  <input
                    type="email"
                    value="not-an-email"
                    :style="{ ...inputBase(L, false, 'error'), padding: '9px 12px' }"
                  />
                  <p :style="helperStyle(L, 'error')">
                    <ExclamationCircleIcon class="w-3.5 h-3.5 flex-shrink-0" />
                    Enter a valid email address.
                  </p>
                </div>
              </div>
            </div>

            <!-- Success -->
            <div>
              <p class="text-[11px] font-medium uppercase tracking-wider mb-3" :style="{ color: L.inkTertiary }">Success state</p>
              <div class="inline-row">
                <label class="inline-label" :style="labelStyle(L)">
                  Email address <span :style="{ color: L.inkSecondary }">*</span>
                </label>
                <div class="inline-field">
                  <input
                    type="email"
                    value="greg@kinhold.app"
                    :style="{ ...inputBase(L, false, 'success'), padding: '9px 12px' }"
                  />
                  <p :style="helperStyle(L, 'success')">
                    <CheckCircleIcon class="w-3.5 h-3.5 flex-shrink-0" />
                    Looks good!
                  </p>
                </div>
              </div>
            </div>

            <!-- Disabled -->
            <div>
              <p class="text-[11px] font-medium uppercase tracking-wider mb-3" :style="{ color: L.inkTertiary }">Disabled state</p>
              <div class="inline-row">
                <label class="inline-label" :style="{ ...labelStyle(L), opacity: '0.5' }">Email address</label>
                <div class="inline-field">
                  <input
                    type="email"
                    value="greg@kinhold.app"
                    disabled
                    :style="{ ...inputDisabled(L), padding: '9px 12px' }"
                  />
                </div>
              </div>
            </div>

            <!-- Textarea -->
            <div>
              <p class="text-[11px] font-medium uppercase tracking-wider mb-3" :style="{ color: L.inkTertiary }">Textarea</p>
              <div class="inline-row">
                <label class="inline-label" :style="{ ...labelStyle(L), paddingTop: '10px' }">Notes</label>
                <div class="inline-field">
                  <textarea
                    v-model="cLtTextarea"
                    rows="3"
                    placeholder="Add a note…"
                    :style="{ ...inputBase(L, !!focused['c-lt-ta'], null), padding: '9px 12px', resize: 'vertical', lineHeight: '1.5' }"
                    @focus="onFocus('c-lt-ta')"
                    @blur="onBlur('c-lt-ta')"
                  />
                </div>
              </div>
            </div>

            <!-- Select -->
            <div>
              <p class="text-[11px] font-medium uppercase tracking-wider mb-3" :style="{ color: L.inkTertiary }">Select</p>
              <div class="inline-row">
                <label class="inline-label" :style="labelStyle(L)">Priority <span :style="{ color: L.inkSecondary }">*</span></label>
                <div class="inline-field relative">
                  <select
                    v-model="cLtSelect"
                    :style="{ ...inputBase(L, !!focused['c-lt-sel'], null), padding: '9px 36px 9px 12px', appearance: 'none', cursor: 'pointer' }"
                    @focus="onFocus('c-lt-sel')"
                    @blur="onBlur('c-lt-sel')"
                  >
                    <option value="" disabled>Select…</option>
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                  </select>
                  <ChevronDownIcon class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 pointer-events-none" :style="{ color: L.inkTertiary }" />
                </div>
              </div>
            </div>

            <!-- Checkbox group -->
            <div>
              <p class="text-[11px] font-medium uppercase tracking-wider mb-3" :style="{ color: L.inkTertiary }">Checkbox group</p>
              <div class="inline-row">
                <label class="inline-label" :style="{ ...labelStyle(L), paddingTop: '2px' }">Notify when</label>
                <div class="inline-field space-y-2">
                  <label v-for="opt in ['Task assigned', 'Task completed', 'New vault entry']" :key="opt"
                         class="flex items-center gap-2.5 cursor-pointer">
                    <input
                      type="checkbox"
                      :value="opt"
                      v-model="cLtChecks"
                      class="w-4 h-4 rounded cursor-pointer"
                      :style="{ accentColor: L.accents.lavender.bold }"
                    />
                    <span :style="{ color: L.inkPrimary, fontSize: '14px' }">{{ opt }}</span>
                  </label>
                </div>
              </div>
            </div>

            <!-- Toggle row -->
            <div>
              <p class="text-[11px] font-medium uppercase tracking-wider mb-3" :style="{ color: L.inkTertiary }">Toggle row</p>
              <div class="inline-row items-center">
                <label class="inline-label" :style="labelStyle(L)">Email digest</label>
                <div class="inline-field flex items-center justify-between">
                  <p :style="{ color: L.inkTertiary, fontSize: '12px' }">Receive a daily digest.</p>
                  <button
                    role="switch"
                    :aria-checked="cLtToggle"
                    class="toggle-track"
                    :class="cLtToggle ? 'toggle-on-lt' : 'toggle-off-lt'"
                    @click="cLtToggle = !cLtToggle"
                  >
                    <span class="toggle-thumb" :class="cLtToggle ? 'translate-x-[18px]' : 'translate-x-0'" />
                  </button>
                </div>
              </div>
            </div>
          </div><!-- /light -->

          <!-- DARK PANEL -->
          <div class="rounded-2xl border p-6 space-y-6"
               :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

            <!-- Default -->
            <div>
              <p class="text-[11px] font-medium uppercase tracking-wider mb-3" :style="{ color: D.inkTertiary }">Text input — default</p>
              <div class="inline-row">
                <label class="inline-label" :style="labelStyle(D)">
                  Email address <span :style="{ color: D.inkSecondary }">*</span>
                </label>
                <div class="inline-field">
                  <input
                    v-model="cDkEmail"
                    type="email"
                    placeholder="you@family.com"
                    :style="{ ...inputBase(D, !!focused['c-dk-email'], null), padding: '9px 12px' }"
                    @focus="onFocus('c-dk-email')"
                    @blur="onBlur('c-dk-email')"
                  />
                  <p :style="helperStyle(D, null)">We'll never share this with anyone.</p>
                </div>
              </div>
            </div>

            <!-- Error -->
            <div>
              <p class="text-[11px] font-medium uppercase tracking-wider mb-3" :style="{ color: D.inkTertiary }">Error state</p>
              <div class="inline-row">
                <label class="inline-label" :style="labelStyle(D)">
                  Email address <span :style="{ color: D.inkSecondary }">*</span>
                </label>
                <div class="inline-field">
                  <input
                    type="email"
                    value="not-an-email"
                    :style="{ ...inputBase(D, false, 'error'), padding: '9px 12px' }"
                  />
                  <p :style="helperStyle(D, 'error')">
                    <ExclamationCircleIcon class="w-3.5 h-3.5 flex-shrink-0" />
                    Enter a valid email address.
                  </p>
                </div>
              </div>
            </div>

            <!-- Success -->
            <div>
              <p class="text-[11px] font-medium uppercase tracking-wider mb-3" :style="{ color: D.inkTertiary }">Success state</p>
              <div class="inline-row">
                <label class="inline-label" :style="labelStyle(D)">
                  Email address <span :style="{ color: D.inkSecondary }">*</span>
                </label>
                <div class="inline-field">
                  <input
                    type="email"
                    value="greg@kinhold.app"
                    :style="{ ...inputBase(D, false, 'success'), padding: '9px 12px' }"
                  />
                  <p :style="helperStyle(D, 'success')">
                    <CheckCircleIcon class="w-3.5 h-3.5 flex-shrink-0" />
                    Looks good!
                  </p>
                </div>
              </div>
            </div>

            <!-- Disabled -->
            <div>
              <p class="text-[11px] font-medium uppercase tracking-wider mb-3" :style="{ color: D.inkTertiary }">Disabled state</p>
              <div class="inline-row">
                <label class="inline-label" :style="{ ...labelStyle(D), opacity: '0.5' }">Email address</label>
                <div class="inline-field">
                  <input
                    type="email"
                    value="greg@kinhold.app"
                    disabled
                    :style="{ ...inputDisabled(D), padding: '9px 12px' }"
                  />
                </div>
              </div>
            </div>

            <!-- Textarea -->
            <div>
              <p class="text-[11px] font-medium uppercase tracking-wider mb-3" :style="{ color: D.inkTertiary }">Textarea</p>
              <div class="inline-row">
                <label class="inline-label" :style="{ ...labelStyle(D), paddingTop: '10px' }">Notes</label>
                <div class="inline-field">
                  <textarea
                    v-model="cDkTextarea"
                    rows="3"
                    placeholder="Add a note…"
                    :style="{ ...inputBase(D, !!focused['c-dk-ta'], null), padding: '9px 12px', resize: 'vertical', lineHeight: '1.5' }"
                    @focus="onFocus('c-dk-ta')"
                    @blur="onBlur('c-dk-ta')"
                  />
                </div>
              </div>
            </div>

            <!-- Select -->
            <div>
              <p class="text-[11px] font-medium uppercase tracking-wider mb-3" :style="{ color: D.inkTertiary }">Select</p>
              <div class="inline-row">
                <label class="inline-label" :style="labelStyle(D)">Priority <span :style="{ color: D.inkSecondary }">*</span></label>
                <div class="inline-field relative">
                  <select
                    v-model="cDkSelect"
                    :style="{ ...inputBase(D, !!focused['c-dk-sel'], null), padding: '9px 36px 9px 12px', appearance: 'none', cursor: 'pointer' }"
                    @focus="onFocus('c-dk-sel')"
                    @blur="onBlur('c-dk-sel')"
                  >
                    <option value="" disabled>Select…</option>
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                  </select>
                  <ChevronDownIcon class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 pointer-events-none" :style="{ color: D.inkTertiary }" />
                </div>
              </div>
            </div>

            <!-- Checkbox group -->
            <div>
              <p class="text-[11px] font-medium uppercase tracking-wider mb-3" :style="{ color: D.inkTertiary }">Checkbox group</p>
              <div class="inline-row">
                <label class="inline-label" :style="{ ...labelStyle(D), paddingTop: '2px' }">Notify when</label>
                <div class="inline-field space-y-2">
                  <label v-for="opt in ['Task assigned', 'Task completed', 'New vault entry']" :key="opt"
                         class="flex items-center gap-2.5 cursor-pointer">
                    <input
                      type="checkbox"
                      :value="opt"
                      v-model="cDkChecks"
                      class="w-4 h-4 rounded cursor-pointer"
                      :style="{ accentColor: D.accents.lavender.bold }"
                    />
                    <span :style="{ color: D.inkPrimary, fontSize: '14px' }">{{ opt }}</span>
                  </label>
                </div>
              </div>
            </div>

            <!-- Toggle row -->
            <div>
              <p class="text-[11px] font-medium uppercase tracking-wider mb-3" :style="{ color: D.inkTertiary }">Toggle row</p>
              <div class="inline-row items-center">
                <label class="inline-label" :style="labelStyle(D)">Email digest</label>
                <div class="inline-field flex items-center justify-between">
                  <p :style="{ color: D.inkTertiary, fontSize: '12px' }">Receive a daily digest.</p>
                  <button
                    role="switch"
                    :aria-checked="cDkToggle"
                    class="toggle-track"
                    :class="cDkToggle ? 'toggle-on-dk' : 'toggle-off-dk'"
                    @click="cDkToggle = !cDkToggle"
                  >
                    <span class="toggle-thumb" :class="cDkToggle ? 'translate-x-[18px]' : 'translate-x-0'" />
                  </button>
                </div>
              </div>
            </div>
          </div><!-- /dark -->
        </div>
      </VariantFrame>
      <p class="mt-3 text-sm px-1" :style="{ color: L.inkSecondary }">
        Validation strategy: <strong>on-blur</strong> by default; use <strong>on-submit</strong> for settings panels where every field is pre-populated and validation noise would feel adversarial. Below 640px the label column collapses to a top-label layout automatically.
      </p>
    </section>


    <!-- ══════════════════════════════════════════════════════════════════════
         CLAUDE'S PICK
         ═══════════════════════════════════════════════════════════════════ -->
    <section class="mb-8">
      <div class="rounded-2xl border p-6 space-y-3"
           :style="{ background: L.accents.lavender.soft, borderColor: L.accents.lavender.bold }">
        <div class="flex items-center gap-2">
          <SparklesIcon class="w-5 h-5 flex-shrink-0" :style="{ color: L.accents.lavender.bold }" />
          <h2 class="text-[15px] font-semibold" :style="{ color: L.accents.lavender.bold }">Claude's pick — Variant A</h2>
        </div>
        <p class="text-[14px] leading-relaxed" :style="{ color: L.inkPrimary }">
          Top-label wins on universality: it handles long labels, wraps gracefully at any viewport, works identically for every field type, and requires zero JS to render correctly on first paint. Every form in Kinhold — registration, add task, vault entry, reward create, settings — maps cleanly to Variant A without special casing.
        </p>
        <p class="text-[14px] leading-relaxed" :style="{ color: L.inkPrimary }">
          Variant B (floating) is the right upgrade for hero surfaces like the login screen and the onboarding wizard where visual polish matters and fields are few. Variant C (inline) belongs exclusively to settings panels and dense data-entry tables where vertical space is at a premium and the user already knows what they're filling in. Defaulting to A and upgrading context-by-context keeps the codebase consistent and the learning curve flat.
        </p>
      </div>
    </section>


    <!-- ══════════════════════════════════════════════════════════════════════
         USAGE GUIDE
         ═══════════════════════════════════════════════════════════════════ -->
    <section class="mb-8">
      <div class="rounded-2xl border p-6 space-y-5"
           :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle }">
        <h2 class="text-[15px] font-semibold" :style="{ color: L.inkPrimary }">Usage guide</h2>

        <div class="space-y-4">
          <div>
            <p class="text-[13px] font-semibold mb-1" :style="{ color: L.inkPrimary }">Variant A — Top label (default)</p>
            <ul class="space-y-1 text-[13px] leading-relaxed list-disc list-inside" :style="{ color: L.inkSecondary }">
              <li>Use for all general forms: register, add task, vault entry, reward create, onboarding steps.</li>
              <li>The label is always outside the field — never double as placeholder text. Required fields get an asterisk in the label.</li>
              <li>Helper text sits below the field at 12px inkTertiary. Error replaces it; success supplements it.</li>
            </ul>
          </div>

          <div>
            <p class="text-[13px] font-semibold mb-1" :style="{ color: L.inkPrimary }">Variant B — Floating label</p>
            <ul class="space-y-1 text-[13px] leading-relaxed list-disc list-inside" :style="{ color: L.inkSecondary }">
              <li>Use for hero moments: login, sign-up hero panel, onboarding "welcome" step.</li>
              <li>The label starts inside the field at inkTertiary and floats to a compact position on focus or fill.</li>
              <li>Never combine floating labels with a separate placeholder — the label is the placeholder.</li>
              <li>Animation transitions are suppressed when <code class="text-[12px] px-1 rounded" :style="{ background: L.borderSubtle }">prefers-reduced-motion: reduce</code> is set.</li>
            </ul>
          </div>

          <div>
            <p class="text-[13px] font-semibold mb-1" :style="{ color: L.inkPrimary }">Variant C — Inline label</p>
            <ul class="space-y-1 text-[13px] leading-relaxed list-disc list-inside" :style="{ color: L.inkSecondary }">
              <li>Use only in settings panels, dense admin tables, or side-by-side edit forms on desktop.</li>
              <li>The label column is fixed at 160px; field fills remaining space. Below 640px it stacks to top-label.</li>
              <li>Avoid in modals or anywhere the container is narrower than 480px in practice.</li>
            </ul>
          </div>

          <div class="rounded-xl p-4 space-y-2"
               :style="{ background: L.surfaceSunken }">
            <p class="text-[13px] font-semibold" :style="{ color: L.inkPrimary }">Validation timing</p>
            <ul class="space-y-1 text-[13px] leading-relaxed list-disc list-inside" :style="{ color: L.inkSecondary }">
              <li><strong>On-blur (default):</strong> Validate each field when the user leaves it. Best for short forms (login, add task). Immediate feedback without red-flagging an empty form on load.</li>
              <li><strong>On-submit:</strong> Validate all fields on form submit. Preferred for high-cost or multi-step forms (onboarding, long vault entries) where partial filling is expected and premature errors would feel punishing.</li>
              <li>Never validate on-keydown for format-sensitive fields (email, phone, SSN) — it fires before the user finishes typing.</li>
            </ul>
          </div>
        </div>
      </div>
    </section>


    <!-- ══════════════════════════════════════════════════════════════════════
         KIN COMPONENT PREVIEW — review below before replacing the bespoke demo
         ═══════════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="Kin" caption="KinFormGroup — proposed extraction. Atomic label + field slot + helper/error. Default slot exposes {id, ariaInvalid, ariaDescribedby}.">
        <div class="w-full space-y-10">

          <!-- LIGHT PANEL -->
          <div class="rounded-2xl border p-6 space-y-5"
               :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
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
          <div class="dark rounded-2xl border p-6 space-y-5"
               :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
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
      <p class="mt-3 text-sm px-1" :style="{ color: L.inkSecondary }">
        Review against the bespoke variants above. Covers label, required, helper, error, disabled, with KinInput / KinTextarea / KinSwitch fields inside.
      </p>
    </section>

  </ComponentPage>
</template>

<style scoped>
/* ── Floating label ───────────────────────────────────────────────────────── */
.float-group {
  position: relative;
}

.float-label {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 14px;
  font-weight: 400;
  pointer-events: none;
  transition: top 180ms ease, font-size 180ms ease, font-weight 180ms ease;
  white-space: nowrap;
}

/* For textarea, anchor from top instead of center */
textarea ~ .float-label {
  top: 14px;
  transform: none;
}

.float-label.floated {
  top: 8px;
  transform: none;
  font-size: 11px;
  font-weight: 500;
}

/* Respect prefers-reduced-motion */
@media (prefers-reduced-motion: reduce) {
  .float-label {
    transition: none;
  }
}

/* ── Inline label layout (Variant C) ─────────────────────────────────────── */
.inline-row {
  display: flex;
  flex-direction: column;
  gap: 6px;
  align-items: flex-start;
}

.inline-label {
  flex-shrink: 0;
  /* No fixed width in stacked mobile mode */
}

.inline-field {
  width: 100%;
}

/* At 640px+ switch to horizontal inline layout */
@media (min-width: 640px) {
  .inline-row {
    flex-direction: row;
    align-items: flex-start;
    gap: 16px;
  }

  .inline-label {
    width: 160px;
    min-width: 160px;
    padding-top: 10px; /* vertically aligns with input */
  }

  .inline-field {
    flex: 1;
  }
}

/* ── Toggle / switch ─────────────────────────────────────────────────────── */
.toggle-track {
  position: relative;
  display: inline-flex;
  align-items: center;
  width: 42px;
  height: 24px;
  border-radius: 9999px;
  border: none;
  cursor: pointer;
  flex-shrink: 0;
  transition: background 200ms;
  outline: none;
}

.toggle-on-lt  { background: #6856B2; }
.toggle-off-lt { background: #BCB8B2; }
.toggle-on-dk  { background: #B6A8E6; }
.toggle-off-dk { background: #403E3A; }

.toggle-thumb {
  position: absolute;
  left: 3px;
  width: 18px;
  height: 18px;
  border-radius: 50%;
  background: #FFFFFF;
  box-shadow: 0 1px 3px rgba(0,0,0,0.25);
  transition: transform 200ms ease;
}

@media (prefers-reduced-motion: reduce) {
  .toggle-track,
  .toggle-thumb {
    transition: none;
  }
}
</style>
