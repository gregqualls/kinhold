<!--
  KinSegmentedFilter — segmented pill for mutually-exclusive filter selection.
  @see /design-system/segmented-filter  (docs/design/COMPONENT_ROADMAP.md §4.2)
  Props: options, activeKey (v-model:active-key), size, disabled
  Slots: (none — options are prop-driven)
  Emits: update:activeKey, change (key, event)

  Outlined container · ink-filled active pill that *slides* between options.
  Active option label becomes ink-inverse via CSS while the pill is over it.
  Counts render as a small inset badge (inverse-tinted on active).
-->
<script setup>
import { computed, ref, watch, onMounted, onBeforeUnmount, nextTick } from 'vue'

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
  /** Force motion regardless of prefers-reduced-motion. Demo use only. */
  forceMotion: { type: Boolean, default: false },
})

const emit = defineEmits(['update:activeKey', 'change'])

function onClick(opt, event) {
  if (props.disabled) return
  emit('update:activeKey', opt.key)
  emit('change', opt.key, event)
}

const hasCounts = computed(() =>
  props.options.some((o) => o.count !== null && o.count !== undefined)
)
const isCompact = computed(() =>
  props.options.every((o) => (o.label ?? '').length <= 2) && !hasCounts.value
)

const optionClass = computed(() => {
  const base = 'kin-seg__option relative inline-flex items-center justify-center rounded-full font-medium cursor-pointer'
  const sizeCls = props.size === 'sm' ? 'h-[26px] text-[12px]' : 'h-8 text-[13px]'
  const widthCls = isCompact.value
    ? (props.size === 'sm' ? 'w-8' : 'w-9')
    : (props.size === 'sm' ? 'px-3' : 'px-3.5')
  const gapCls = hasCounts.value ? 'gap-1.5' : ''
  return [base, sizeCls, widthCls, gapCls].filter(Boolean).join(' ')
})

// ── Sliding indicator math ─────────────────────────────────────────────────
const containerEl = ref(null)
const buttonRefs = ref([])
function setBtnRef(el, idx) { buttonRefs.value[idx] = el }

const indicatorStyle = ref({ opacity: 0, transform: 'translateX(0)', width: '0px' })

function recalc() {
  const container = containerEl.value
  const idx = props.options.findIndex((o) => o.key === props.activeKey)
  const btn = buttonRefs.value[idx]
  if (!container || !btn) {
    indicatorStyle.value = { ...indicatorStyle.value, opacity: 0 }
    return
  }
  // offsetLeft is relative to the offsetParent, which is the container (relative).
  const left = btn.offsetLeft
  const width = btn.offsetWidth
  indicatorStyle.value = {
    opacity: 1,
    transform: `translateX(${left}px)`,
    width: `${width}px`,
  }
}

let ro = null
onMounted(() => {
  nextTick(recalc)
  if (typeof ResizeObserver !== 'undefined' && containerEl.value) {
    ro = new ResizeObserver(() => recalc())
    ro.observe(containerEl.value)
  }
})
onBeforeUnmount(() => { ro?.disconnect() })
watch(() => [props.activeKey, props.options], () => nextTick(recalc), { deep: true })
</script>

<template>
  <div
    ref="containerEl"
    class="kin-seg relative inline-flex rounded-full p-0.5"
    :class="[
      disabled && 'kin-seg--disabled',
      forceMotion && 'kin-seg--force-motion',
    ]"
    role="radiogroup"
  >
    <!-- Sliding indicator -->
    <span
      class="kin-seg__indicator absolute top-0.5 bottom-0.5 rounded-full pointer-events-none"
      :style="indicatorStyle"
      aria-hidden="true"
    ></span>

    <button
      v-for="(opt, i) in options"
      :key="opt.key"
      :ref="(el) => setBtnRef(el, i)"
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
        class="kin-seg__badge inline-flex items-center justify-center rounded-full h-4 min-w-[1rem] px-1 text-[10px] font-semibold leading-none"
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

/* Sliding ink-filled pill behind the active option. */
.kin-seg__indicator {
  left: 0;
  background: rgb(var(--ink-primary));
  transition: transform 280ms cubic-bezier(0.22, 1, 0.36, 1),
              width     280ms cubic-bezier(0.22, 1, 0.36, 1),
              opacity   180ms ease-out;
  z-index: 0;
}

/* Option default (inactive). */
.kin-seg__option {
  background: transparent;
  color: rgb(var(--ink-secondary));
  border: none;
  z-index: 1;
  /* Color transitions in sync with the indicator slide. */
  transition: color 220ms cubic-bezier(0.22, 1, 0.36, 1);
}

.kin-seg:not(.kin-seg--disabled) .kin-seg__option:not(.kin-seg__option--active):hover {
  color: rgb(var(--ink-primary));
}

/* Active label color (the indicator provides the background). */
.kin-seg__option--active {
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

@media (prefers-reduced-motion: reduce) {
  .kin-seg:not(.kin-seg--force-motion) .kin-seg__indicator,
  .kin-seg:not(.kin-seg--force-motion) .kin-seg__option {
    transition: none;
  }
}
</style>
