<!--
  KinTabPillGroup — pill-row tabs for in-page section switching.
  @see /design-system/tab-pill-group  (docs/design/COMPONENT_ROADMAP.md §4.1)
  Props: tabs, activeKey (v-model:active-key), variant, size
  Slots: (none — tabs are prop-driven)
  Emits: update:activeKey, change (key, event)

  Variants:
    filled    — dark ink fill on active, outlined inactive (strongest weight)
    underline — 2px bar under active, plain inactive (leanest)
    tinted    — lavender-soft fill + lavender-bold text on active (brand accent)

  Each tab supports an optional `count` number that renders as a small badge
  pill. Click emits `change` + `update:activeKey` so consumers can v-model.
  Horizontally scrolls with flex-shrink-0 on narrow viewports.
-->
<script setup>
import { computed } from 'vue'

const props = defineProps({
  /** Tabs: { key, label, count?, to?, href? }. */
  tabs: {
    type: Array,
    required: true,
  },
  /** Currently active tab key (supports v-model:active-key). */
  activeKey: { type: [String, Number], default: null },
  /** Visual variant. */
  variant: {
    type: String,
    default: 'tinted',
    validator: (v) => ['filled', 'underline', 'tinted'].includes(v),
  },
  /** Size scale. */
  size: {
    type: String,
    default: 'md',
    validator: (v) => ['sm', 'md'].includes(v),
  },
})

const emit = defineEmits(['update:activeKey', 'change'])

function onClick(tab, event) {
  emit('update:activeKey', tab.key)
  emit('change', tab.key, event)
}

// The underline variant uses a shared bottom border on the container.
const containerClass = computed(() => ({
  filled:    'flex flex-wrap gap-2',
  underline: 'relative flex gap-1 border-b border-border-subtle',
  tinted:    'flex flex-wrap gap-2',
}[props.variant]))

const heightClass = computed(() => {
  if (props.variant === 'underline') {
    return props.size === 'sm' ? 'h-9' : 'h-9'
  }
  return props.size === 'sm' ? 'h-8' : 'h-8'
})

const paddingClass = computed(() => (props.size === 'sm' ? 'px-3.5' : 'px-4'))

const textSizeClass = computed(() => (props.size === 'sm' ? 'text-[12px]' : 'text-[13px]'))

const badgeSizeClass = computed(() => (props.size === 'sm'
  ? 'text-[10px] px-1 min-w-[16px] h-[16px]'
  : 'text-[11px] px-1.5 min-w-[18px] h-[18px]'))
</script>

<template>
  <div :class="containerClass" role="tablist">
    <button
      v-for="tab in tabs"
      :key="tab.key"
      type="button"
      role="tab"
      :aria-selected="activeKey === tab.key"
      class="kin-tab flex-shrink-0 relative inline-flex items-center gap-1.5 font-medium"
      :class="[
        heightClass,
        paddingClass,
        textSizeClass,
        `kin-tab--${variant}`,
        activeKey === tab.key && 'kin-tab--active',
      ]"
      @click="onClick(tab, $event)"
    >
      {{ tab.label }}
      <span
        v-if="tab.count !== null && tab.count !== undefined"
        class="kin-tab__badge inline-flex items-center justify-center font-semibold rounded-full"
        :class="badgeSizeClass"
      >{{ tab.count }}</span>

      <!-- Underline bar (underline variant only) -->
      <span
        v-if="variant === 'underline' && activeKey === tab.key"
        class="kin-tab__underline absolute bottom-0 left-0 right-0 h-[2px] rounded-t-full"
        aria-hidden="true"
      ></span>
    </button>
  </div>
</template>

<style scoped>
.kin-tab {
  background: transparent;
  border: none;
  cursor: pointer;
  transition: color 150ms, background-color 150ms, border-color 150ms;
}

/* ── FILLED variant ──────────────────────────────────────────────────────── */
/*
  Active: ink-primary bg, inverse text, matching border.
  Inactive: transparent, ink-secondary text, border-strong outline.
  Hover (inactive): ink-primary text + border.
*/
.kin-tab--filled {
  border-radius: 9999px;
  border: 1.5px solid rgb(var(--border-strong));
  color: rgb(var(--ink-secondary));
}
.kin-tab--filled:hover:not(.kin-tab--active) {
  color: rgb(var(--ink-primary));
  border-color: rgb(var(--ink-primary));
}
.kin-tab--filled.kin-tab--active {
  background: rgb(var(--ink-primary));
  color: rgb(var(--ink-inverse));
  border-color: rgb(var(--ink-primary));
}
.kin-tab--filled .kin-tab__badge {
  background: rgb(var(--surface-sunken));
  color: rgb(var(--ink-tertiary));
}
.dark .kin-tab--filled .kin-tab__badge {
  background: rgb(var(--surface-overlay));
}
.kin-tab--filled.kin-tab--active .kin-tab__badge {
  background: rgb(var(--ink-inverse) / 0.18);
  color: rgb(var(--ink-inverse));
}
.dark .kin-tab--filled.kin-tab--active .kin-tab__badge {
  background: rgb(var(--ink-inverse) / 0.20);
}

/* ── UNDERLINE variant ───────────────────────────────────────────────────── */
/*
  Active: ink-primary text + 2px bottom bar.
  Inactive: ink-tertiary text, no bar.
  Hover (inactive): ink-secondary text.
  Count badge: lavender-soft/bold on active, surface-sunken on inactive.
*/
.kin-tab--underline {
  color: rgb(var(--ink-tertiary));
}
.kin-tab--underline:hover:not(.kin-tab--active) {
  color: rgb(var(--ink-secondary));
}
.kin-tab--underline.kin-tab--active {
  color: rgb(var(--ink-primary));
}
.kin-tab--underline .kin-tab__badge {
  background: rgb(var(--surface-sunken));
  color: rgb(var(--ink-tertiary));
}
.dark .kin-tab--underline .kin-tab__badge {
  background: rgb(var(--surface-overlay));
}
.kin-tab--underline.kin-tab--active .kin-tab__badge {
  background: rgb(var(--accent-lavender-soft));
  color: rgb(var(--accent-lavender-bold));
}
.kin-tab__underline {
  background: rgb(var(--ink-primary));
}

/* ── TINTED variant ──────────────────────────────────────────────────────── */
/*
  Active: lavender-soft bg, lavender-bold text.
  Inactive: transparent, ink-secondary text.
  Hover (inactive): lavender-bold text (no fill yet).
  Count badge: lavender-bold fill with inverse text on active.
*/
.kin-tab--tinted {
  border-radius: 9999px;
  border: 1.5px solid transparent;
  color: rgb(var(--ink-secondary));
}
.kin-tab--tinted:hover:not(.kin-tab--active) {
  color: rgb(var(--accent-lavender-bold));
}
.kin-tab--tinted.kin-tab--active {
  background: rgb(var(--accent-lavender-soft));
  color: rgb(var(--accent-lavender-bold));
}
.kin-tab--tinted .kin-tab__badge {
  background: rgb(var(--surface-sunken));
  color: rgb(var(--ink-tertiary));
}
.dark .kin-tab--tinted .kin-tab__badge {
  background: rgb(var(--surface-overlay));
}
.kin-tab--tinted.kin-tab--active .kin-tab__badge {
  background: rgb(var(--accent-lavender-bold));
  color: rgb(var(--surface-raised));
}

@media (prefers-reduced-motion: reduce) {
  .kin-tab { transition: none; }
}
</style>
