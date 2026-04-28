<!--
  KinTextarea — borderless inset multi-line textarea, same visual language
  as KinInput (Variant B). No prefix/suffix slots — textareas don't use them.

  @see /design-system (docs/design/COMPONENT_ROADMAP.md §1.2)
  Props: modelValue, label, placeholder, helper, error, disabled, readonly,
         required, size, id, rows
-->

<script setup>
import { computed } from 'vue'

// ── Props ───────────────────────────────────────────────────────────────────
const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: '',
  },
  label: {
    type: String,
    default: '',
  },
  placeholder: {
    type: String,
    default: '',
  },
  helper: {
    type: String,
    default: '',
  },
  error: {
    type: String,
    default: '',
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  readonly: {
    type: Boolean,
    default: false,
  },
  required: {
    type: Boolean,
    default: false,
  },
  /**
   * size — controls padding, font size, and corner radius.
   * sm = text-[13px] / rounded-[10px] / py-2 px-3
   * md = text-[15px] / rounded-field  / py-2.5 px-3.5  (default)
   * lg = text-[16px] / rounded-field  / py-3 px-4
   */
  size: {
    type: String,
    default: 'md',
    validator: (v) => ['sm', 'md', 'lg'].includes(v),
  },
  /** id — used for <label htmlFor>. Auto-generated if omitted. */
  id: {
    type: String,
    default: '',
  },
  /** rows — number of visible text rows (HTML rows attr). Default 3. */
  rows: {
    type: Number,
    default: 3,
  },
})

// ── Emits ───────────────────────────────────────────────────────────────────
const emit = defineEmits(['update:modelValue'])

// Forward non-prop attrs to <textarea> not the wrapper div
defineOptions({ inheritAttrs: false })

// ── Stable auto-id (Vue 3.4 — no useId()) ──────────────────────────────────
const _autoId  = `kin-textarea-${Math.random().toString(36).slice(2, 9)}`
const inputId  = computed(() => props.id || _autoId)
const helperId = computed(() => `${inputId.value}-help`)

// ── Derived state ────────────────────────────────────────────────────────────
const hasError  = computed(() => Boolean(props.error))
const hasHelper = computed(() => Boolean(props.helper))
const hasLabel  = computed(() => Boolean(props.label))

// ── Size-to-class maps ───────────────────────────────────────────────────────
const sizeClasses = computed(() => {
  const map = {
    sm: 'px-3   py-2   text-[13px] rounded-[10px]',
    md: 'px-3.5 py-2.5 text-[15px] rounded-field',
    lg: 'px-4   py-3   text-[16px] rounded-field',
  }
  return map[props.size] ?? map.md
})

function onInput(e) {
  emit('update:modelValue', e.target.value)
}
</script>

<template>
  <div class="flex flex-col gap-1 w-full">
    <!-- Label -->
    <label
      v-if="hasLabel"
      :for="inputId"
      class="text-[13px] font-medium text-ink-secondary"
    >
      {{ label }}
      <span v-if="required" class="text-status-failed ml-0.5" aria-hidden="true">*</span>
    </label>

    <!-- Textarea -->
    <textarea
      v-bind="$attrs"
      :id="inputId"
      :value="modelValue"
      :placeholder="placeholder"
      :disabled="disabled"
      :readonly="readonly"
      :required="required"
      :rows="rows"
      :aria-invalid="hasError || undefined"
      :aria-describedby="(hasError || hasHelper) ? helperId : undefined"
      :class="[
        'kin-textarea w-full border-0 outline-none resize-y',
        'bg-surface-sunken text-ink-primary placeholder:text-ink-tertiary',
        sizeClasses,
        hasError ? 'kin-textarea--error' : '',
        disabled ? 'cursor-not-allowed opacity-70' : '',
      ]"
      @input="onInput"
    ></textarea>

    <!-- Helper / Error -->
    <p
      v-if="hasError || hasHelper"
      :id="helperId"
      :class="[
        'text-[11px] leading-tight',
        hasError ? 'text-status-failed' : 'text-ink-tertiary',
      ]"
    >
      {{ hasError ? error : helper }}
    </p>
  </div>
</template>

<style scoped>
/*
  ─────────────────────────────────────────────────────────────────
  KinTextarea — Borderless Inset (Variant B)
  Mirrors KinInput interaction states exactly (same token values).
  ─────────────────────────────────────────────────────────────────
*/
.kin-textarea {
  transition:
    background-color var(--duration-quick) var(--ease-out-soft),
    box-shadow       var(--duration-quick) var(--ease-out-soft),
    border-color     var(--duration-quick) var(--ease-out-soft);
  border: 1px solid transparent;
  box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.06);
}

.kin-textarea:hover:not(:disabled):not(:focus) {
  background-color: #EDEAE5;
  box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.08);
}

.kin-textarea:focus {
  border-color: rgb(var(--accent-lavender-bold));
  box-shadow: 0 0 0 3px rgba(104, 86, 178, 0.25);
  background-color: rgb(var(--surface-sunken));
}

.kin-textarea--error {
  border-color: rgb(var(--status-failed)) !important;
  box-shadow: 0 0 0 3px rgba(186, 74, 74, 0.18) !important;
}

/* ── Dark mode ──────────────────────────────────────────────────── */
:global(.dark) .kin-textarea {
  background-color: rgb(var(--surface-overlay));
  color:            rgb(var(--ink-primary));
  border-color:     transparent;
  box-shadow:       inset 0 1px 0 rgba(255, 255, 255, 0.04);
}

:global(.dark) .kin-textarea::placeholder {
  color: #8A8680;
}

:global(.dark) .kin-textarea:hover:not(:disabled):not(:focus) {
  background-color: #2C2A27;
  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.06);
}

:global(.dark) .kin-textarea:focus {
  background-color: rgb(var(--surface-overlay));
  border-color:     rgb(var(--accent-lavender-bold));
  box-shadow:       0 0 0 3px rgba(182, 168, 230, 0.30);
}

:global(.dark) .kin-textarea.kin-textarea--error {
  border-color: rgb(var(--status-failed)) !important;
  box-shadow:   0 0 0 3px rgba(230, 112, 112, 0.22) !important;
}
</style>
