<!--
  KinTopNav — desktop glass pill nav. Brand + nav pills + search slot + utility slot.
  @see /design-system/top-nav  (docs/design/COMPONENT_ROADMAP.md §3.1)
  Props: brand, brandTo, brandHref, items, activeKey
  Slots: #brand-icon (overrides default initial-circle), #search, #utility
  Emits: item-click (key, event)

  One of the four allowed glass surfaces (REDESIGN_BRIEF tenet #7).
  Below md breakpoint this should be hidden in real app usage — mobile users
  get KinBottomNav instead. The design-system demo shows it at desktop widths.
-->
<script setup>
import { computed } from 'vue'
import { RouterLink } from 'vue-router'

defineEmits(['item-click'])

const props = defineProps({
  /** Brand wordmark text. */
  brand: { type: String, default: 'Kinhold' },
  /** Optional router target for the brand area. */
  brandTo: { type: [String, Object], default: null },
  /** Optional href for the brand area (used if brandTo is unset). */
  brandHref: { type: String, default: null },
  /** Nav item list. Each: { key, label, to?, href? }. */
  items: {
    type: Array,
    default: () => [],
  },
  /** Which item's key is currently "active" (filled pill). */
  activeKey: { type: String, default: null },
})

// Resolve the brand wrapper element (RouterLink / a / div).
const brandTag = computed(() => {
  if (props.brandTo)   return RouterLink
  if (props.brandHref) return 'a'
  return 'div'
})
const brandAttrs = computed(() => {
  if (props.brandTo)   return { to: props.brandTo }
  if (props.brandHref) return { href: props.brandHref }
  return {}
})

// Resolve each nav item's tag (RouterLink / a / button).
function itemTag(item) {
  if (item.to)   return RouterLink
  if (item.href) return 'a'
  return 'button'
}
function itemAttrs(item) {
  if (item.to)   return { to: item.to }
  if (item.href) return { href: item.href }
  return { type: 'button' }
}

// First character for the default brand initial circle.
const brandInitial = computed(() => (props.brand?.charAt(0) || 'K').toUpperCase())
</script>

<template>
  <div class="kin-top-nav flex items-center h-16 px-5 gap-3 rounded-2xl">
    <!-- ── Brand ─────────────────────────────────────────────────────── -->
    <component
      :is="brandTag"
      v-bind="brandAttrs"
      class="flex items-center gap-2 shrink-0 no-underline"
    >
      <slot name="brand-icon">
        <div class="w-7 h-7 rounded-full flex items-center justify-center text-[11px] font-bold bg-accent-lavender-bold text-white dark:text-surface-app">
          {{ brandInitial }}
        </div>
      </slot>
      <span
        v-if="brand"
        class="font-heading text-[15px] font-semibold tracking-tight text-ink-primary"
      >{{ brand }}</span>
    </component>

    <!-- ── Nav pills ─────────────────────────────────────────────────── -->
    <nav v-if="items.length" class="flex items-center gap-1 shrink-0">
      <component
        :is="itemTag(item)"
        v-for="item in items"
        :key="item.key"
        v-bind="itemAttrs(item)"
        class="kin-top-nav__item rounded-full px-4 py-1.5 text-[13px] font-medium"
        :class="{ 'kin-top-nav__item--active': activeKey === item.key }"
        @click="$emit('item-click', item.key, $event)"
      >{{ item.label }}</component>
    </nav>

    <!-- ── Search (centered) ─────────────────────────────────────────── -->
    <div class="flex-1 flex justify-center px-4">
      <slot name="search" />
    </div>

    <!-- ── Utility cluster (right) ───────────────────────────────────── -->
    <div class="flex items-center gap-1 shrink-0">
      <slot name="utility" />
    </div>
  </div>
</template>

<style scoped>
/*
  Glass pill nav — one of the four allowed glass surfaces (tenet #7).
  Translucent fill + backdrop-blur + subtle bottom border + inner top highlight.
  Surface values hardcoded because glass tokens in tokens.css (§5) cover
  different treatments; these are tuned specifically for the top nav.
*/
.kin-top-nav {
  background: rgba(255, 255, 255, 0.72);
  backdrop-filter: blur(16px) saturate(140%);
  -webkit-backdrop-filter: blur(16px) saturate(140%);
  border: 1px solid rgba(255, 255, 255, 0.65);
  box-shadow:
    var(--shadow-resting),
    inset 0 1px 0 rgba(255, 255, 255, 0.85);
}

.dark .kin-top-nav {
  background: rgba(28, 27, 25, 0.75);
  backdrop-filter: blur(18px) saturate(130%);
  -webkit-backdrop-filter: blur(18px) saturate(130%);
  border: 1px solid rgba(255, 255, 255, 0.08);
  box-shadow:
    var(--shadow-resting),
    inset 0 1px 0 rgba(255, 255, 255, 0.06);
}

/*
  Nav pill items.
  Default: ink-secondary text on transparent bg.
  Hover: surface-sunken translucent bg + ink-primary text.
  Active: ink-primary bg + surface-raised text (auto-inverts per mode
  since both tokens flip via :root/.dark in tokens.css).
*/
.kin-top-nav__item {
  color: rgb(var(--ink-secondary));
  background: transparent;
  border: none;
  cursor: pointer;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  transition: background-color 200ms, color 200ms;
}

.kin-top-nav__item:hover {
  color: rgb(var(--ink-primary));
  background: rgb(var(--surface-sunken) / 0.80);
}

.dark .kin-top-nav__item:hover {
  background: rgb(var(--surface-overlay) / 0.80);
}

.kin-top-nav__item--active,
.kin-top-nav__item--active:hover {
  background: rgb(var(--ink-primary));
  color: rgb(var(--surface-raised));
}

@media (prefers-reduced-motion: reduce) {
  .kin-top-nav__item {
    transition: none;
  }
}
</style>
