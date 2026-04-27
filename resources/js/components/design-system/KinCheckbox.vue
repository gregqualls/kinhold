<!--
  KinCheckbox — minimal neutral fill when checked.
  @see /design-system/selection  (docs/design/COMPONENT_ROADMAP.md §1.5)
  Props: modelValue, label, description, disabled, size, id
  Emits: update:modelValue
-->
<script setup>
import { computed } from 'vue'
import { CheckIcon, MinusIcon } from '@heroicons/vue/24/outline'

// ── Props ────────────────────────────────────────────────────────────────────
const props = defineProps({
  /** true = checked; false = unchecked; null = indeterminate */
  modelValue:  { default: false   },
  label:       { type: String,           default: ''      },
  description: { type: String,           default: ''      },
  disabled:    { type: Boolean,          default: false   },
  /** sm = 14px box  |  md = 18px box (default) */
  size:        { type: String,           default: 'md',   validator: (v) => ['sm', 'md'].includes(v) },
  id:          { type: String,           default: ''      },
})

// ── Emits ────────────────────────────────────────────────────────────────────
const emit = defineEmits(['update:modelValue'])

// ── Stable auto-id ───────────────────────────────────────────────────────────
const _autoId = `kin-cb-${Math.random().toString(36).slice(2, 9)}`
const inputId = computed(() => props.id || _autoId)
const descId  = computed(() => `${inputId.value}-desc`)

// ── Derived state ─────────────────────────────────────────────────────────────
const isIndeterminate = computed(() => props.modelValue === null || props.modelValue === undefined)
const isChecked       = computed(() => Boolean(props.modelValue) && !isIndeterminate.value)

// ── Size maps ─────────────────────────────────────────────────────────────────
const boxSize = computed(() => props.size === 'sm'
  ? { box: 'w-[14px] h-[14px]', icon: 'w-2.5 h-2.5', radius: 'rounded-[4px]' }
  : { box: 'w-[18px] h-[18px]', icon: 'w-3 h-3',     radius: 'rounded-[5px]' }
)

// ── Handlers ──────────────────────────────────────────────────────────────────
function toggle() {
  !props.disabled && emit('update:modelValue', !isChecked.value)
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
        type="checkbox"
        class="peer sr-only"
        :checked="isChecked"
        :indeterminate.prop="isIndeterminate"
        :disabled="disabled"
        :aria-describedby="description ? descId : undefined"
        @change="toggle"
      />

      <!-- Visual box -->
      <span
        :class="[
          'kin-cb-visual flex items-center justify-center flex-shrink-0',
          boxSize.box,
          boxSize.radius,
        ]"
      >
        <CheckIcon
          v-if="isChecked"
          :class="['flex-shrink-0 text-ink-inverse', boxSize.icon]"
          aria-hidden="true"
          style="stroke-width: 2.5"
        />
        <MinusIcon
          v-else-if="isIndeterminate"
          :class="['flex-shrink-0 text-ink-inverse', boxSize.icon]"
          aria-hidden="true"
          style="stroke-width: 2.5"
        />
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
  KinCheckbox — visual box states

  Pattern: sibling peer-* selectors on the hidden input
  drive the visual box styles. No inline hex — all values
  reference the token CSS custom properties.
  ═══════════════════════════════════════════════════════
*/

/* ── Unchecked / idle ───────────────────────────────── */
.kin-cb-visual {
  border: 1.5px solid rgb(var(--border-strong));
  background-color: rgb(var(--surface-sunken));
  transition:
    border-color var(--duration-quick) var(--ease-out-soft),
    background-color var(--duration-quick) var(--ease-out-soft),
    box-shadow var(--duration-quick) var(--ease-out-soft);
}

/* ── Hover ───────────────────────────────────────────── */
label:not(.cursor-not-allowed):hover .kin-cb-visual {
  border-color: rgb(var(--ink-secondary));
}

/* ── Focus ring (keyboard) ───────────────────────────── */
.peer:focus-visible + .kin-cb-visual {
  border-color: rgb(var(--accent-lavender-bold));
  box-shadow: 0 0 0 3px rgb(var(--accent-lavender-bold) / 0.25);
}

/* ── Checked / indeterminate — neutral ink fill ─────── */
.peer:checked + .kin-cb-visual,
.peer:indeterminate + .kin-cb-visual {
  background-color: rgb(var(--ink-primary));
  border-color: rgb(var(--ink-primary));
}

/* Focus ring when checked */
.peer:checked:focus-visible + .kin-cb-visual,
.peer:indeterminate:focus-visible + .kin-cb-visual {
  box-shadow: 0 0 0 3px rgb(var(--accent-lavender-bold) / 0.25);
}
</style>
