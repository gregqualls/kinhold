<!--
  KinRadio — minimal neutral fill when checked (inner ink dot).
  @see /design-system/selection  (docs/design/COMPONENT_ROADMAP.md §1.5)
  Props: modelValue, value, name, label, description, disabled, size, id
  Emits: update:modelValue
-->
<script setup>
import { computed } from 'vue'

// ── Props ────────────────────────────────────────────────────────────────────
const props = defineProps({
  /** The currently selected value for the group (bind v-model on parent) */
  modelValue:  { type: [String, Number, Boolean, Object], default: null  },
  /** The value this radio represents — when modelValue === value, it's checked */
  value:       { type: [String, Number, Boolean, Object], required: true },
  /** name attribute required for radio grouping + arrow-key navigation */
  name:        { type: String,  required: true  },
  label:       { type: String,  default: ''     },
  description: { type: String,  default: ''     },
  disabled:    { type: Boolean, default: false  },
  /** sm = 14px circle  |  md = 18px circle (default) */
  size:        { type: String,  default: 'md',  validator: (v) => ['sm', 'md'].includes(v) },
  id:          { type: String,  default: ''     },
})

// ── Emits ────────────────────────────────────────────────────────────────────
const emit = defineEmits(['update:modelValue'])

// ── Stable auto-id ───────────────────────────────────────────────────────────
const _autoId = `kin-rd-${Math.random().toString(36).slice(2, 9)}`
const inputId = computed(() => props.id || _autoId)
const descId  = computed(() => `${inputId.value}-desc`)

// ── Derived state ─────────────────────────────────────────────────────────────
const isChecked = computed(() => props.modelValue === props.value)

// ── Size maps ─────────────────────────────────────────────────────────────────
const sizeMap = computed(() => props.size === 'sm'
  ? { outer: 'w-[14px] h-[14px]', dot: 'w-[6px] h-[6px]' }
  : { outer: 'w-[18px] h-[18px]', dot: 'w-2 h-2'         }
)

// ── Handlers ──────────────────────────────────────────────────────────────────
function select() {
  !props.disabled && emit('update:modelValue', props.value)
}
</script>

<template>
  <label
    :for="inputId"
    :class="[
      'inline-flex items-start gap-2.5 select-none',
      disabled ? 'cursor-not-allowed opacity-40' : 'cursor-pointer',
    ]"
  >
    <!-- Hidden native input (sr-only) — accessibility anchor -->
    <span class="relative mt-0.5 flex-shrink-0">
      <input
        :id="inputId"
        type="radio"
        class="peer sr-only"
        :name="name"
        :value="value"
        :checked="isChecked"
        :disabled="disabled"
        :aria-describedby="description ? descId : undefined"
        @change="select"
      />

      <!-- Visual circle -->
      <span
        :class="[
          'kin-rd-visual flex items-center justify-center flex-shrink-0 rounded-full',
          sizeMap.outer,
        ]"
      >
        <!-- Inner dot — visible when checked -->
        <span
          :class="[
            'kin-rd-dot rounded-full flex-shrink-0',
            sizeMap.dot,
          ]"
        ></span>
      </span>
    </span>

    <!-- Label + description -->
    <span v-if="label || description" class="flex flex-col gap-0.5">
      <span v-if="label" class="text-body-sm text-ink-primary leading-snug">{{ label }}</span>
      <span
        v-if="description"
        :id="descId"
        class="text-caption text-ink-tertiary leading-snug"
      >{{ description }}</span>
    </span>
  </label>
</template>

<style scoped>
/*
  ═══════════════════════════════════════════════════════
  KinRadio — visual circle + inner dot states

  The outer ring stays neutral at all times. The inner dot
  fades in on :checked using opacity — no layout shift.
  ═══════════════════════════════════════════════════════
*/

/* ── Unchecked / idle ───────────────────────────────── */
.kin-rd-visual {
  border: 1.5px solid rgb(var(--border-strong));
  background-color: rgb(var(--surface-sunken));
  transition:
    border-color var(--duration-quick) var(--ease-out-soft),
    box-shadow var(--duration-quick) var(--ease-out-soft);
}

/* ── Inner dot ───────────────────────────────────────── */
.kin-rd-dot {
  background-color: rgb(var(--ink-primary));
  opacity: 0;
  transition: opacity var(--duration-quick) var(--ease-out-soft);
}

/* ── Hover ───────────────────────────────────────────── */
label:not(.cursor-not-allowed):hover .kin-rd-visual {
  border-color: rgb(var(--ink-secondary));
}

/* ── Focus ring (keyboard) ───────────────────────────── */
.peer:focus-visible + .kin-rd-visual {
  border-color: rgb(var(--accent-lavender-bold));
  box-shadow: 0 0 0 3px rgb(var(--accent-lavender-bold) / 0.25);
}

/* ── Checked — show inner dot ────────────────────────── */
.peer:checked + .kin-rd-visual > .kin-rd-dot {
  opacity: 1;
}

/* Focus ring when checked */
.peer:checked:focus-visible + .kin-rd-visual {
  box-shadow: 0 0 0 3px rgb(var(--accent-lavender-bold) / 0.25);
}
</style>
