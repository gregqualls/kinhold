<!--
  KinBottomNav — floating mobile glass pill with 4 nav slots + elevated center FAB.
  @see /design-system/bottom-nav  (docs/design/COMPONENT_ROADMAP.md §3.2)
  Props: items (exactly 4), activeId
  Slots: #fab (the elevated center button — Ask Assistant by convention)
  Emits: item-click (id, event)

  Layout is flex with 5 cells: [item1][item2][FAB-gap][item3][item4].
  The #fab slot is absolute-positioned above the middle cell, lifted 16px,
  overlapping the pill. One of the four allowed glass surfaces (tenet #7).
-->
<script setup>
import { computed } from 'vue'
import { RouterLink } from 'vue-router'

defineEmits(['item-click'])

const props = defineProps({
  /**
   * Nav items — exactly 4 (the 5th slot is the FAB).
   * Shape: { id, label, icon, to?, href? }.
   * `icon` is a Vue component (Heroicon etc).
   */
  items: {
    type: Array,
    required: true,
    validator: (v) => Array.isArray(v) && v.length === 4,
  },
  /** Which item's id is currently active (filled-circle state). */
  activeId: { type: String, default: null },
})

// Resolve each item's root tag (RouterLink / a / button).
function itemTag(item) {
  if (item.to)   return RouterLink
  if (item.href) return 'a'
  return 'button'
}
function itemAttrs(item) {
  if (item.to)   return { to: item.to, 'aria-label': item.label }
  if (item.href) return { href: item.href, 'aria-label': item.label }
  return { type: 'button', 'aria-label': item.label }
}

// Split items into left-pair (0,1) and right-pair (2,3) around the FAB.
const leftItems  = computed(() => props.items.slice(0, 2))
const rightItems = computed(() => props.items.slice(2, 4))
</script>

<template>
  <div
    class="kin-bottom-nav relative flex items-center rounded-full px-2"
    style="height: 60px;"
    role="navigation"
    aria-label="Primary"
  >
    <!-- Left pair -->
    <component
      :is="itemTag(item)"
      v-for="item in leftItems"
      :key="item.id"
      v-bind="itemAttrs(item)"
      class="kin-bn__slot flex-1 flex items-center justify-center"
      @click="$emit('item-click', item.id, $event)"
    >
      <span
        v-if="activeId === item.id"
        class="kin-bn__slot-active w-11 h-11 rounded-full flex items-center justify-center"
      >
        <component :is="item.icon" class="w-[22px] h-[22px]" />
      </span>
      <component
        :is="item.icon"
        v-else
        class="kin-bn__slot-icon w-[22px] h-[22px]"
      />
    </component>

    <!-- Center FAB gap — visually reserved for the absolute-positioned #fab -->
    <div class="flex-1 min-w-[60px]" aria-hidden="true"></div>

    <!-- Right pair -->
    <component
      :is="itemTag(item)"
      v-for="item in rightItems"
      :key="item.id"
      v-bind="itemAttrs(item)"
      class="kin-bn__slot flex-1 flex items-center justify-center"
      @click="$emit('item-click', item.id, $event)"
    >
      <span
        v-if="activeId === item.id"
        class="kin-bn__slot-active w-11 h-11 rounded-full flex items-center justify-center"
      >
        <component :is="item.icon" class="w-[22px] h-[22px]" />
      </span>
      <component
        :is="item.icon"
        v-else
        class="kin-bn__slot-icon w-[22px] h-[22px]"
      />
    </component>

    <!-- FAB slot — absolute-positioned above pill center -->
    <div class="kin-bn__fab absolute left-1/2 -translate-x-1/2 -translate-y-[16px]">
      <slot name="fab"></slot>
    </div>
  </div>
</template>

<style scoped>
/*
  Glass pill — translucent warm-white (light) / warm-charcoal (dark) + blur.
  One of the four allowed glass surfaces (REDESIGN_BRIEF tenet #7).
*/
.kin-bottom-nav {
  background: rgba(255, 255, 255, 0.55);
  backdrop-filter: blur(16px) saturate(140%);
  -webkit-backdrop-filter: blur(16px) saturate(140%);
  border: 1px solid rgba(255, 255, 255, 0.65);
  box-shadow:
    0 1px 1px rgba(255, 255, 255, 0.40) inset,
    0 16px 32px rgba(28, 20, 10, 0.08);
  overflow: visible;
}

.dark .kin-bottom-nav {
  background: rgba(28, 27, 25, 0.60);
  backdrop-filter: blur(18px) saturate(130%);
  -webkit-backdrop-filter: blur(18px) saturate(130%);
  border: 1px solid rgba(255, 255, 255, 0.08);
  box-shadow:
    0 1px 1px rgba(255, 255, 255, 0.06) inset,
    0 16px 32px rgba(0, 0, 0, 0.45);
}

/* Slot buttons */
.kin-bn__slot {
  cursor: pointer;
  background: transparent;
  border: none;
  text-decoration: none;
  transition: opacity 200ms;
}
.kin-bn__slot:hover {
  opacity: 0.70;
}

/* Inactive icon — 50% opacity over ink-primary color */
.kin-bn__slot-icon {
  color: rgb(var(--ink-primary) / 0.50);
}

/* Active icon — filled circle with inverse ink */
.kin-bn__slot-active {
  background: rgb(var(--ink-primary) / 0.85);
  color: rgb(var(--surface-raised));
  transition: background-color 200ms;
}
.dark .kin-bn__slot-active {
  background: rgb(var(--ink-primary) / 0.90);
  color: rgb(var(--surface-app));
}

@media (prefers-reduced-motion: reduce) {
  .kin-bn__slot,
  .kin-bn__slot-active {
    transition: none;
  }
}
</style>
