<!--
  KinInput — borderless inset text field with optional label/helper/error.
  Variant B from the InputPage design system demo: sunken fill + inset shadow
  at rest; no border until focus; accent ring on focus; raised surface in dark
  mode for legibility against the near-black app background.

  @see /design-system (docs/design/COMPONENT_ROADMAP.md §1.2)
  Props: modelValue, label, placeholder, helper, error, disabled, readonly,
         required, size, id, type
  Slots: #prefix, #suffix
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
   * size — controls height, padding, font size, and corner radius.
   * sm = h-8 / text-[13px] / rounded-[10px]
   * md = h-10 / text-[15px] / rounded-field  (default)
   * lg = h-12 / text-[16px] / rounded-field
   */
  size: {
    type: String,
    default: 'md',
    validator: (v) => ['sm', 'md', 'lg'].includes(v),
  },
  /**
   * id — used for <label htmlFor> binding. Auto-generated if not supplied.
   * Stable within a component instance (not reactive to prop changes).
   */
  id: {
    type: String,
    default: '',
  },
  /**
   * type — standard HTML input type. 'password' reveals the native input
   * semantics (masking) — consumers should add their own toggle suffix
   * slot if a visibility toggle is needed.
   */
  type: {
    type: String,
    default: 'text',
  },
})

// ── Emits ───────────────────────────────────────────────────────────────────
const emit = defineEmits(['update:modelValue'])

// ── Inherited attrs passthrough ─────────────────────────────────────────────
// inheritAttrs: false so we can forward $attrs to the <input>, not the wrapper.
defineOptions({ inheritAttrs: false })

// ── Stable auto-id ──────────────────────────────────────────────────────────
// Vue 3.4 doesn't have useId(); use a simple stable random string instead.
const _autoId = `kin-input-${Math.random().toString(36).slice(2, 9)}`
const inputId = computed(() => props.id || _autoId)

// ── Sub-element ids for aria-describedby ────────────────────────────────────
const helperId = computed(() => `${inputId.value}-help`)

// ── Derived state ────────────────────────────────────────────────────────────
const hasError  = computed(() => Boolean(props.error))
const hasHelper = computed(() => Boolean(props.helper))
const hasLabel  = computed(() => Boolean(props.label))
const hasSuffix = computed(() => false) // resolved at template via $slots

// ── Size-to-class maps ───────────────────────────────────────────────────────
// `text-[16px] md:text-[<smaller>]` keeps mobile inputs at 16px so iOS Safari
// doesn't auto-zoom on focus, while desktop keeps its original compact density.
const sizeClasses = computed(() => {
  const map = {
    sm: 'h-8  px-3   text-[16px] md:text-[13px] rounded-[10px]',
    md: 'h-10 px-3.5 text-[16px] md:text-[15px] rounded-field',
    lg: 'h-12 px-4   text-[16px] rounded-field',
  }
  return map[props.size] ?? map.md
})

// Prefix slot shifts left padding to accommodate icon
const prefixPadding = computed(() => {
  const map = { sm: 'pl-8', md: 'pl-10', lg: 'pl-11' }
  return map[props.size] ?? map.md
})
const suffixPadding = computed(() => {
  const map = { sm: 'pr-8', md: 'pr-10', lg: 'pr-11' }
  return map[props.size] ?? map.md
})
const iconSize = computed(() => {
  const map = { sm: 'w-3.5 h-3.5', md: 'w-4 h-4', lg: 'w-5 h-5' }
  return map[props.size] ?? map.md
})
const iconOffset = computed(() => {
  const map = { sm: 'left-2.5', md: 'left-3', lg: 'left-3.5' }
  return map[props.size] ?? map.md
})
const iconOffsetRight = computed(() => {
  const map = { sm: 'right-2.5', md: 'right-3', lg: 'right-3.5' }
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

    <!-- Field wrapper (for prefix/suffix positioning) -->
    <div class="relative w-full">
      <!-- Prefix slot -->
      <span
        v-if="$slots.prefix"
        :class="[
          'absolute top-1/2 -translate-y-1/2 pointer-events-none flex items-center',
          iconOffset,
          iconSize,
          'text-ink-tertiary',
        ]"
        aria-hidden="true"
      >
        <slot name="prefix"></slot>
      </span>

      <!-- The <input> element -->
      <input
        v-bind="$attrs"
        :id="inputId"
        :type="type"
        :value="modelValue"
        :placeholder="placeholder"
        :disabled="disabled"
        :readonly="readonly"
        :required="required"
        :aria-invalid="hasError || undefined"
        :aria-describedby="(hasError || hasHelper) ? helperId : undefined"
        :class="[
          'kin-input w-full border-0 outline-none bg-surface-sunken text-ink-primary',
          'placeholder:text-ink-tertiary',
          sizeClasses,
          $slots.prefix ? prefixPadding : '',
          $slots.suffix ? suffixPadding : '',
          hasError
            ? 'kin-input--error'
            : '',
          disabled
            ? 'cursor-not-allowed opacity-70'
            : '',
        ]"
        @input="onInput"
      />

      <!-- Suffix slot -->
      <span
        v-if="$slots.suffix"
        :class="[
          'absolute top-1/2 -translate-y-1/2 flex items-center',
          iconOffsetRight,
          iconSize,
          'text-ink-tertiary',
        ]"
        aria-hidden="true"
      >
        <slot name="suffix"></slot>
      </span>
    </div><!-- /field wrapper -->

    <!-- Helper / Error message -->
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
  KinInput — Borderless Inset (Variant B)

  Light mode:
    • Resting:  bg = surface-sunken (#F5F2EE), inset shadow, no border
    • Hover:    bg darkens slightly (#EDEAE5)
    • Focus:    accent-lavender-bold border + 3-px lavender ring

  Dark mode — inverted metaphor ("raised" not "sunken"):
    • The page is near-black; a darker input would vanish.
    • Inputs sit RAISED (surface-overlay #242220 > app #141311).
    • Inset highlight replaces inset shadow (suggests lift, not press).
    • Focus: accent-lavender-bold dark ring (#B6A8E6 + rgba glow).

  Transitions respect prefers-reduced-motion via --duration-quick = 0ms.
  ─────────────────────────────────────────────────────────────────
*/
.kin-input {
  transition:
    background-color var(--duration-quick) var(--ease-out-soft),
    box-shadow       var(--duration-quick) var(--ease-out-soft),
    border-color     var(--duration-quick) var(--ease-out-soft);
  /* Resting: no border, subtle inset shadow presses field into page */
  border: 1px solid transparent;
  box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.06);
}

.kin-input:hover:not(:disabled):not(:focus) {
  background-color: #EDEAE5;
  box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.08);
}

.kin-input:focus {
  /* accent-lavender-bold light: #6856B2 */
  border-color: rgb(var(--accent-lavender-bold));
  box-shadow: 0 0 0 3px rgba(104, 86, 178, 0.25);
  background-color: rgb(var(--surface-sunken));
}

/* Error state — red border + ring */
.kin-input--error {
  border-color: rgb(var(--status-failed)) !important;
  box-shadow: 0 0 0 3px rgba(186, 74, 74, 0.18) !important;
}

/* ── Dark mode ──────────────────────────────────────────────────── */
/* Raised dark inputs (surface-overlay = #242220 sits above surface-app #141311) */
:global(.dark) .kin-input {
  background-color: rgb(var(--surface-overlay));   /* #242220 — raised */
  color:            rgb(var(--ink-primary));
  border-color:     transparent;
  box-shadow:       inset 0 1px 0 rgba(255, 255, 255, 0.04);
}

:global(.dark) .kin-input::placeholder {
  /* Slightly brighter than ink-tertiary for legibility on raised dark bg */
  color: #8A8680;
}

:global(.dark) .kin-input:hover:not(:disabled):not(:focus) {
  background-color: #2C2A27;
  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.06);
}

:global(.dark) .kin-input:focus {
  /* accent-lavender-bold dark: #B6A8E6 */
  background-color: rgb(var(--surface-overlay));
  border-color:     rgb(var(--accent-lavender-bold));
  box-shadow:       0 0 0 3px rgba(182, 168, 230, 0.30);
}

:global(.dark) .kin-input.kin-input--error {
  border-color: rgb(var(--status-failed)) !important;
  box-shadow:   0 0 0 3px rgba(230, 112, 112, 0.22) !important;
}
</style>
