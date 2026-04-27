<!--
  KinFormGroup — atomic form field container (label + field slot + helper/error).
  @see /design-system/form-group  (docs/design/COMPONENT_ROADMAP.md §4.10)
  Props: label, helper, error, required, disabled, id
  Slots: default (field — KinInput, KinTextarea, select, KinSwitch, or custom)

  Provides the canonical label-on-top + helper-below convention for every form
  in the app. Use this around any field that needs a label — even composite
  fields like a KinAvatarPicker used for "assign to" selection.

  If you're using KinInput/KinTextarea with a single value, you can pass
  label/helper/error directly to those components — FormGroup is for when
  the field has no built-in label support (select, switch, radio group,
  custom composites) or when you want one consistent wrapper across a form.
-->
<script setup>
import { computed } from 'vue'
import { ExclamationCircleIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  /** Field label (rendered above the field). */
  label: { type: String, default: '' },
  /** Helper text (below field). Overridden by error. */
  helper: { type: String, default: '' },
  /** Error text (below field, red). Takes precedence over helper. */
  error: { type: String, default: '' },
  /** Mark the field as required (appends red asterisk). */
  required: { type: Boolean, default: false },
  /** Disable the whole group (dims label + helper). */
  disabled: { type: Boolean, default: false },
  /** Optional id to link label → field. If omitted, auto-generated. */
  id: { type: String, default: null },
})

const fieldId = computed(() => props.id || `kin-fg-${Math.random().toString(36).slice(2, 9)}`)

// Give the slot access to the generated id + aria-describedby for the field inside.
const slotProps = computed(() => ({
  id: fieldId.value,
  ariaInvalid: !!props.error,
  ariaDescribedby: (props.helper || props.error) ? `${fieldId.value}-helper` : undefined,
}))
</script>

<template>
  <div class="kin-form-group space-y-1" :class="disabled && 'kin-form-group--disabled'">
    <!-- Label -->
    <label
      v-if="label"
      :for="fieldId"
      class="kin-form-group__label text-[13px] font-medium text-ink-primary block"
    >
      {{ label }}<span v-if="required" class="kin-form-group__required ml-0.5">*</span>
    </label>

    <!-- Field slot (pass id + aria attrs down) -->
    <slot v-bind="slotProps" />

    <!-- Helper / error text -->
    <p
      v-if="error || helper"
      :id="`${fieldId}-helper`"
      class="kin-form-group__helper text-[12px] flex items-center gap-1"
      :class="error ? 'kin-form-group__helper--error' : 'kin-form-group__helper--default'"
      role="status"
    >
      <ExclamationCircleIcon v-if="error" class="w-3.5 h-3.5 flex-shrink-0" />
      <span>{{ error || helper }}</span>
    </p>
  </div>
</template>

<style scoped>
.kin-form-group--disabled .kin-form-group__label,
.kin-form-group--disabled .kin-form-group__helper {
  opacity: 0.5;
}

.kin-form-group__required {
  color: rgb(var(--status-failed));
}

.kin-form-group__helper--default {
  color: rgb(var(--ink-tertiary));
}
.kin-form-group__helper--error {
  color: rgb(var(--status-failed));
}
</style>
