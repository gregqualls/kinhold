<!--
  KinSelect — borderless inset dropdown. Matches KinInput's visual language:
  sunken fill at rest, no border until focus, accent-lavender ring on focus,
  raised surface in dark mode.

  Native <select> under the hood — preserves keyboard navigation, screen-reader
  semantics, mobile native picker UX, and form-submission. Adds a custom chevron
  icon and the Kin token-aligned styling.

  @see /design-system/select  (docs/design/COMPONENT_ROADMAP.md §1.8)
  Props: modelValue, options, label, placeholder, helper, error, disabled,
         required, size, id
  Slots: (none — options are prop-driven for accessibility / a11y consistency)
  Emits: update:modelValue, change

  Options shape:
    String[]                        — value === label
    { value, label, disabled? }[]   — full control
    { group, options: [...] }[]     — optgroup support
-->

<script setup>
import { computed } from 'vue'
import { ChevronDownIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  modelValue: {
    type: [String, Number, Boolean, null],
    default: '',
  },
  /**
   * Options. Accepts:
   *   ['a', 'b']                            — string options (value === label)
   *   [{ value, label, disabled? }]         — flat objects
   *   [{ group: 'Heading', options: [...] }] — grouped (renders <optgroup>)
   */
  options: {
    type: Array,
    default: () => [],
  },
  label: {
    type: String,
    default: '',
  },
  /** Placeholder for the empty state (renders as a disabled "" option). */
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
  required: {
    type: Boolean,
    default: false,
  },
  size: {
    type: String,
    default: 'md',
    validator: (v) => ['sm', 'md', 'lg'].includes(v),
  },
  id: {
    type: String,
    default: '',
  },
})

const emit = defineEmits(['update:modelValue', 'change'])

defineOptions({ inheritAttrs: false })

const _autoId = `kin-select-${Math.random().toString(36).slice(2, 9)}`
const selectId = computed(() => props.id || _autoId)
const helperId = computed(() => `${selectId.value}-help`)

const hasError  = computed(() => Boolean(props.error))
const hasHelper = computed(() => Boolean(props.helper))
const hasLabel  = computed(() => Boolean(props.label))

// Detect grouped vs flat options
const isGrouped = computed(() => {
  return props.options.length > 0 && props.options.every(o => typeof o === 'object' && o !== null && 'options' in o)
})

// Normalize a single option item to { value, label, disabled }
function normalizeOption(opt) {
  if (typeof opt === 'string' || typeof opt === 'number') {
    return { value: opt, label: String(opt), disabled: false }
  }
  return {
    value: opt.value,
    label: opt.label ?? String(opt.value),
    disabled: !!opt.disabled,
  }
}

const flatOptions = computed(() => {
  if (isGrouped.value) return []
  return props.options.map(normalizeOption)
})

const groupedOptions = computed(() => {
  if (!isGrouped.value) return []
  return props.options.map(g => ({
    group: g.group,
    options: (g.options || []).map(normalizeOption),
  }))
})

// Size-to-class map (mirrors KinInput)
const sizeClasses = computed(() => {
  const map = {
    sm: 'h-8  pl-3   pr-8  text-[13px] rounded-[10px]',
    md: 'h-10 pl-3.5 pr-10 text-[15px] rounded-field',
    lg: 'h-12 pl-4   pr-11 text-[16px] rounded-field',
  }
  return map[props.size] ?? map.md
})

const chevronOffset = computed(() => {
  const map = { sm: 'right-2.5', md: 'right-3', lg: 'right-3.5' }
  return map[props.size] ?? map.md
})
const chevronSize = computed(() => {
  const map = { sm: 'w-3.5 h-3.5', md: 'w-4 h-4', lg: 'w-5 h-5' }
  return map[props.size] ?? map.md
})

function onChange(e) {
  emit('update:modelValue', e.target.value)
  emit('change', e.target.value, e)
}
</script>

<template>
  <div class="flex flex-col gap-1 w-full">

    <!-- Label -->
    <label
      v-if="hasLabel"
      :for="selectId"
      class="text-[13px] font-medium text-ink-secondary"
    >
      {{ label }}
      <span v-if="required" class="text-status-failed ml-0.5" aria-hidden="true">*</span>
    </label>

    <!-- Field wrapper (anchors the chevron) -->
    <div class="relative w-full">
      <select
        v-bind="$attrs"
        :id="selectId"
        :value="modelValue"
        :disabled="disabled"
        :required="required"
        :aria-invalid="hasError || undefined"
        :aria-describedby="(hasError || hasHelper) ? helperId : undefined"
        :class="[
          'kin-select w-full appearance-none border-0 outline-none bg-surface-sunken text-ink-primary cursor-pointer',
          sizeClasses,
          hasError ? 'kin-select--error' : '',
          disabled ? 'cursor-not-allowed opacity-70' : '',
          (modelValue === '' || modelValue === null || modelValue === undefined) && placeholder
            ? 'kin-select--placeholder'
            : '',
        ]"
        @change="onChange"
      >
        <!-- Placeholder (empty disabled option, only when placeholder is set) -->
        <option v-if="placeholder" value="" disabled>{{ placeholder }}</option>

        <!-- Flat options -->
        <template v-if="!isGrouped">
          <option
            v-for="opt in flatOptions"
            :key="String(opt.value)"
            :value="opt.value"
            :disabled="opt.disabled"
          >{{ opt.label }}</option>
        </template>

        <!-- Grouped options -->
        <template v-else>
          <optgroup
            v-for="g in groupedOptions"
            :key="g.group"
            :label="g.group"
          >
            <option
              v-for="opt in g.options"
              :key="String(opt.value)"
              :value="opt.value"
              :disabled="opt.disabled"
            >{{ opt.label }}</option>
          </optgroup>
        </template>
      </select>

      <!-- Chevron (purely decorative; <select> handles input itself) -->
      <span
        :class="[
          'absolute top-1/2 -translate-y-1/2 pointer-events-none flex items-center text-ink-tertiary',
          chevronOffset,
          chevronSize,
        ]"
        aria-hidden="true"
      >
        <ChevronDownIcon :class="chevronSize" />
      </span>
    </div>

    <!-- Helper / error message -->
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
  KinSelect mirrors KinInput's borderless-inset treatment.

  Light mode:
    Resting:  surface-sunken bg, inset shadow, no border
    Hover:    bg darkens slightly
    Focus:    accent-lavender border + 3px ring

  Dark mode — inverted ("raised", not "sunken"):
    Resting:  surface-overlay bg, inset highlight (suggests lift)
    Hover:    bumped one step
    Focus:    dark accent-lavender ring

  Native <select>'s default chevron is suppressed via `appearance: none` on the
  template class. Our custom ChevronDownIcon sits in an absolutely-positioned
  span with `pointer-events-none` so the click area still belongs to the
  underlying <select>.
*/
.kin-select {
  transition:
    background-color var(--duration-quick) var(--ease-out-soft),
    box-shadow       var(--duration-quick) var(--ease-out-soft),
    border-color     var(--duration-quick) var(--ease-out-soft);
  border: 1px solid transparent;
  box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.06);
}

.kin-select:hover:not(:disabled):not(:focus) {
  background-color: #EDEAE5;
  box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.08);
}

.kin-select:focus {
  border-color: rgb(var(--accent-lavender-bold));
  box-shadow: 0 0 0 3px rgba(104, 86, 178, 0.25);
  background-color: rgb(var(--surface-sunken));
}

/* Placeholder state — render the dim ink-tertiary look so the empty state is visually distinct */
.kin-select--placeholder {
  color: rgb(var(--ink-tertiary));
}

/* Error state */
.kin-select--error {
  border-color: rgb(var(--status-failed)) !important;
  box-shadow: 0 0 0 3px rgba(186, 74, 74, 0.18) !important;
}

/* Dark mode — raised surface */
:global(.dark) .kin-select {
  background-color: rgb(var(--surface-overlay));
  color:            rgb(var(--ink-primary));
  border-color:     transparent;
  box-shadow:       inset 0 1px 0 rgba(255, 255, 255, 0.04);
}

:global(.dark) .kin-select:hover:not(:disabled):not(:focus) {
  background-color: #2C2A27;
  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.06);
}

:global(.dark) .kin-select:focus {
  background-color: rgb(var(--surface-overlay));
  border-color:     rgb(var(--accent-lavender-bold));
  box-shadow:       0 0 0 3px rgba(182, 168, 230, 0.30);
}

:global(.dark) .kin-select.kin-select--error {
  border-color: rgb(var(--status-failed)) !important;
  box-shadow:   0 0 0 3px rgba(230, 112, 112, 0.22) !important;
}

/* Native option dropdown styling — browsers vary; keep this minimal */
.kin-select option {
  background: rgb(var(--surface-raised));
  color: rgb(var(--ink-primary));
}
:global(.dark) .kin-select option {
  background: rgb(var(--surface-overlay));
}
</style>
