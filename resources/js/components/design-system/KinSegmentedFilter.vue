<!--
  KinSegmentedFilter — segmented pill for mutually-exclusive filter selection.
  @see /design-system/segmented-filter  (docs/design/COMPONENT_ROADMAP.md §4.2)
  Props: options, activeKey (v-model:active-key), size, disabled
  Slots: (none — options are prop-driven)
  Emits: update:activeKey, change (key, event)

  Locked treatment: single outlined container with ink-filled active pill,
  plain inactive. Counts render as a small inset badge (inverse-tinted on
  active, surface-sunken on inactive).
-->
<script setup>
import { computed } from 'vue'

const props = defineProps({
  /** Option objects: { key, label, count? }. */
  options: { type: Array, required: true },
  /** Currently active option key (supports v-model:active-key). */
  activeKey: { type: [String, Number], default: null },
  /** Size scale. sm = 26px, md = 32px. */
  size: {
    type: String,
    default: 'md',
    validator: (v) => ['sm', 'md'].includes(v),
  },
  /** Disable the whole control. */
  disabled: { type: Boolean, default: false },
  /** Force ease-out transitions regardless of prefers-reduced-motion. Demo use only. */
  forceMotion: { type: Boolean, default: false },
})

const emit = defineEmits(['update:activeKey', 'change'])

function onClick(opt, event) {
  if (props.disabled) return
  emit('update:activeKey', opt.key)
  emit('change', opt.key, event)
}

// Any option with a non-null count triggers the "with badge" layout so labels line up.
const hasCounts = computed(() =>
  props.options.some((o) => o.count !== null && o.count !== undefined)
)

// Is this purely compact single-letter options (D/W/M/Y)? If so, use a fixed
// square width per option instead of padding-based width.
const isCompact = computed(() =>
  props.options.every((o) => (o.label ?? '').length <= 2) && !hasCounts.value
)

// Option classes vary by size + compact-mode.
const optionClass = computed(() => {
  const base = 'kin-seg__option inline-flex items-center rounded-full font-medium transition-colors cursor-pointer'
  const sizeCls = props.size === 'sm' ? 'h-[26px] text-[12px]' : 'h-8 text-[13px]'
  const widthCls = isCompact.value
    ? (props.size === 'sm' ? 'w-8 justify-center' : 'w-9 justify-center')
    : (props.size === 'sm' ? 'px-3' : 'px-3.5')
  const gapCls = hasCounts.value ? 'gap-1.5' : ''
  return [base, sizeCls, widthCls, gapCls].filter(Boolean).join(' ')
})

const badgeClass = 'kin-seg__badge inline-flex items-center justify-center rounded-full h-4 min-w-[1rem] px-1 text-[10px] font-semibold leading-none'
</script>

<template>
  <div
    class="kin-seg inline-flex rounded-full p-0.5"
    :class="[
      disabled && 'kin-seg--disabled',
      forceMotion && 'kin-seg--force-motion',
    ]"
    role="radiogroup"
  >
    <button
      v-for="opt in options"
      :key="opt.key"
      type="button"
      role="radio"
      :aria-checked="activeKey === opt.key"
      :aria-disabled="disabled || null"
      :class="[optionClass, activeKey === opt.key && 'kin-seg__option--active']"
      @click="onClick(opt, $event)"
    >
      {{ opt.label }}
      <span
        v-if="hasCounts && opt.count !== null && opt.count !== undefined"
        :class="badgeClass"
      >{{ opt.count }}</span>
    </button>
  </div>
</template>

<style scoped>
.kin-seg {
  border: 1px solid rgb(var(--border-strong));
  background: rgb(var(--surface-raised));
}

.kin-seg--disabled {
  opacity: 0.40;
  cursor: not-allowed;
}
.kin-seg--disabled .kin-seg__option {
  cursor: not-allowed;
  pointer-events: none;
}

/* Option default (inactive).
   220ms ease-out fill transition per locked spec — matches iOS segmented feel. */
.kin-seg__option {
  background: transparent;
  color: rgb(var(--ink-secondary));
  border: none;
  transition: background-color 220ms cubic-bezier(0.22, 1, 0.36, 1),
              color            220ms cubic-bezier(0.22, 1, 0.36, 1);
}

/* Hover only when not active and not disabled */
.kin-seg:not(.kin-seg--disabled) .kin-seg__option:not(.kin-seg__option--active):hover {
  color: rgb(var(--ink-primary));
}

/* Active */
.kin-seg__option--active {
  background: rgb(var(--ink-primary));
  color: rgb(var(--ink-inverse));
}

/* Badge */
.kin-seg__badge {
  background: rgb(var(--surface-sunken));
  color: rgb(var(--ink-tertiary));
}
.dark .kin-seg__badge {
  background: rgb(var(--surface-overlay));
}
.kin-seg__option--active .kin-seg__badge {
  background: rgb(var(--ink-inverse) / 0.20);
  color: rgb(var(--ink-inverse));
}

/* Respect reduced-motion unless forceMotion is set (demo/preview escape hatch). */
@media (prefers-reduced-motion: reduce) {
  .kin-seg:not(.kin-seg--force-motion) .kin-seg__option {
    transition: none;
  }
}
</style>
