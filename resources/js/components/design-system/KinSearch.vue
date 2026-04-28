<!--
  KinSearch — pill-shaped search field (Variant C).
  rounded-pill shape reserved for search inputs only per the design brief.
  Integrated leading magnifying-glass icon. Clearable X button when value
  is non-empty and :clearable="true" (default).

  @see /design-system (docs/design/COMPONENT_ROADMAP.md §1.2)
  Props: modelValue, placeholder, clearable, disabled, size
-->

<script setup>
import { computed } from 'vue'
import { MagnifyingGlassIcon, XMarkIcon } from '@heroicons/vue/24/outline'

// ── Props ───────────────────────────────────────────────────────────────────
const props = defineProps({
  modelValue: {
    type: String,
    default: '',
  },
  placeholder: {
    type: String,
    default: 'Search\u2026',   // 'Search…'
  },
  /**
   * clearable — show an X button when the field has a value.
   * Emits update:modelValue = '' on click.
   */
  clearable: {
    type: Boolean,
    default: true,
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  /**
   * size — sm / md (default) / lg
   * sm = h-8  / text-[13px] / icon w-3.5
   * md = h-10 / text-[15px] / icon w-4
   * lg = h-12 / text-[16px] / icon w-5
   */
  size: {
    type: String,
    default: 'md',
    validator: (v) => ['sm', 'md', 'lg'].includes(v),
  },
})

// ── Emits ───────────────────────────────────────────────────────────────────
const emit = defineEmits(['update:modelValue'])

// Forward non-prop attrs to the <input> not the wrapper
defineOptions({ inheritAttrs: false })

// ── Derived state ────────────────────────────────────────────────────────────
const hasValue     = computed(() => Boolean(props.modelValue))
const showClear    = computed(() => props.clearable && hasValue.value && !props.disabled)

// ── Size maps ────────────────────────────────────────────────────────────────
// Pill shape: always rounded-pill (h-full rounded-full)
const sizeClasses = computed(() => {
  const map = {
    sm: 'h-8  pl-9  text-[13px]',
    md: 'h-10 pl-11 text-[15px]',
    lg: 'h-12 pl-12 text-[16px]',
  }
  return map[props.size] ?? map.md
})

// Right padding: when clearable+filled show pr for X button, else normal
const prClass = computed(() => {
  if (!props.clearable) {
    return props.size === 'sm' ? 'pr-3' : 'pr-4'
  }
  // Always leave room for potential X button so text doesn't jump
  const map = { sm: 'pr-8', md: 'pr-10', lg: 'pr-11' }
  return map[props.size] ?? map.md
})

// Icon sizing
const iconSize = computed(() => {
  const map = { sm: 'w-3.5 h-3.5', md: 'w-4 h-4', lg: 'w-5 h-5' }
  return map[props.size] ?? map.md
})

// Icon horizontal positions
const leadIconPos  = computed(() => {
  const map = { sm: 'left-3',  md: 'left-4',  lg: 'left-4' }
  return map[props.size] ?? map.md
})
const clearIconPos = computed(() => {
  const map = { sm: 'right-2.5', md: 'right-3', lg: 'right-3.5' }
  return map[props.size] ?? map.md
})
const clearBtnSize = computed(() => {
  const map = { sm: 'w-5 h-5', md: 'w-6 h-6', lg: 'w-6 h-6' }
  return map[props.size] ?? map.md
})

function onInput(e) {
  emit('update:modelValue', e.target.value)
}
function onClear() {
  emit('update:modelValue', '')
}
</script>

<template>
  <div class="relative w-full" :class="disabled ? 'opacity-70' : ''">
    <!-- Leading search icon — color shifts on focus via CSS -->
    <span
      :class="[
        'kin-search-icon',
        'absolute top-1/2 -translate-y-1/2 pointer-events-none flex items-center',
        leadIconPos,
        iconSize,
        'text-ink-tertiary',
      ]"
      aria-hidden="true"
    >
      <MagnifyingGlassIcon class="w-full h-full" />
    </span>

    <!-- Search input — pill shape -->
    <input
      v-bind="$attrs"
      type="search"
      :value="modelValue"
      :placeholder="placeholder"
      :disabled="disabled"
      :class="[
        'kin-search w-full rounded-pill border-0 outline-none',
        'bg-surface-sunken text-ink-primary placeholder:text-ink-tertiary',
        sizeClasses,
        prClass,
        disabled ? 'cursor-not-allowed' : '',
      ]"
      @input="onInput"
    />

    <!-- Clear button — only when clearable and value is present -->
    <button
      v-if="showClear"
      type="button"
      :class="[
        'kin-search-clear',
        'absolute top-1/2 -translate-y-1/2 flex items-center justify-center rounded-full',
        clearIconPos,
        clearBtnSize,
        'text-ink-secondary',
      ]"
      aria-label="Clear search"
      @click="onClear"
    >
      <XMarkIcon :class="iconSize" />
    </button>
  </div>
</template>

<style scoped>
/*
  ─────────────────────────────────────────────────────────────────
  KinSearch — Pill Search (Variant C)

  Light mode:
    • Resting:  bg = surface-sunken (#F5F2EE), no border, subtle inset shadow
    • Hover:    bg = #EDEAE5 (slightly darker)
    • Focus:    accent-lavender-bold border + 3-px ring; search icon turns bold

  Dark mode:
    • Resting:  bg = surface-sunken (#161513 — slightly below raised; pill
                search sits pressed-in even in dark, unlike KinInput which
                raises. Keeps visual distinction between text fields and
                search fields).
    • Hover:    bg = #12110F (slightly darker)
    • Focus:    accent-lavender-bold dark border + ring

  Pill shape is via rounded-pill (var(--radius-pill) = 9999px in tokens.css).
  ─────────────────────────────────────────────────────────────────
*/
.kin-search {
  transition:
    background-color var(--duration-quick) var(--ease-out-soft),
    box-shadow       var(--duration-quick) var(--ease-out-soft),
    border-color     var(--duration-quick) var(--ease-out-soft);
  border: 1px solid transparent;
  /* Suppress browser default search-cancel button */
  -webkit-appearance: none;
  appearance: none;
  box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.06);
}

/* Remove browser ::-webkit-search-cancel-button to avoid double-X */
.kin-search::-webkit-search-cancel-button {
  -webkit-appearance: none;
  appearance: none;
}

.kin-search:hover:not(:disabled):not(:focus) {
  background-color: #EDEAE5;
  box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.08);
}

.kin-search:focus {
  border-color: rgb(var(--accent-lavender-bold));
  box-shadow: 0 0 0 3px rgba(104, 86, 178, 0.25);
  background-color: rgb(var(--surface-sunken));
}

/* Search icon turns accent on focus (sibling selector via parent :focus-within) */
.kin-search:focus ~ .kin-search-icon,
.kin-search-icon:has(~ .kin-search:focus) {
  color: rgb(var(--accent-lavender-bold));
}

/* Clear button hover */
.kin-search-clear:hover {
  background-color: rgb(var(--surface-sunken));
}

/* ── Dark mode ──────────────────────────────────────────────────── */
/*
  Pill search in dark uses surface-sunken (#161513) — intentionally
  sunken (below surface-raised #1C1B19) to distinguish it visually
  from KinInput's raised treatment. The "search" metaphor = pressing
  in; the "text field" metaphor in dark = lifting out. Both patterns
  can coexist in the same form.
*/
:global(.dark) .kin-search {
  background-color: rgb(var(--surface-sunken));   /* #161513 */
  color:            rgb(var(--ink-primary));
  border-color:     transparent;
  box-shadow:       inset 0 1px 2px rgba(0, 0, 0, 0.15);
}

:global(.dark) .kin-search::placeholder {
  color: #6E6B67;
}

:global(.dark) .kin-search:hover:not(:disabled):not(:focus) {
  background-color: #12110F;
  box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.20);
}

:global(.dark) .kin-search:focus {
  background-color: rgb(var(--surface-sunken));
  border-color:     rgb(var(--accent-lavender-bold));
  box-shadow:       0 0 0 3px rgba(182, 168, 230, 0.30);
}

:global(.dark) .kin-search-clear:hover {
  background-color: rgb(var(--surface-overlay));
}
</style>
