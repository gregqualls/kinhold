<!--
  KinSidebar — desktop sidebar nav with collapsible state.
  @see /design-system/sidebar  (docs/design/COMPONENT_ROADMAP.md §3.3)
  Props: brand, brandTo, brandHref, items, activeKey, collapsed (v-model)
  Slots: #brand-icon (override gradient square), #user (footer user row)
  Emits: update:collapsed, item-click (key, event)

  Expanded = 240px, Collapsed = 72px. Active item fills with accent-lavender-bold.
  Used on data-heavy views (calendar, tasks, vault, food).
-->
<script setup>
import { computed } from 'vue'
import { RouterLink } from 'vue-router'
import {
  ChevronDoubleLeftIcon,
  ChevronDoubleRightIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  /** Brand wordmark text. */
  brand: { type: String, default: 'Kinhold' },
  /** Optional router target for the brand area. */
  brandTo: { type: [String, Object], default: null },
  /** Optional href for the brand area. */
  brandHref: { type: String, default: null },
  /** Nav items: { key, label, icon, to?, href? }. */
  items: { type: Array, default: () => [] },
  /** Which item's key is active (accent-pill state). */
  activeKey: { type: String, default: null },
  /** Collapsed state — supports v-model:collapsed. */
  collapsed: { type: Boolean, default: false },
})

const emit = defineEmits(['update:collapsed', 'item-click'])

function toggle() {
  emit('update:collapsed', !props.collapsed)
}

// Resolve each item's tag.
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

// Brand wrapper tag.
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
</script>

<template>
  <aside
    class="kin-sidebar flex flex-col flex-shrink-0 bg-surface-raised border-r border-border-subtle"
    :style="{ width: collapsed ? '72px' : '240px' }"
    :aria-label="'Primary navigation'"
    role="navigation"
  >
    <!-- ── Brand ─────────────────────────────────────────────────────── -->
    <component
      :is="brandTag"
      v-bind="brandAttrs"
      class="flex items-center gap-2 px-3 py-4 no-underline"
      :class="collapsed ? 'justify-center' : ''"
    >
      <slot name="brand-icon">
        <div
          class="w-7 h-7 rounded-lg flex-shrink-0"
          style="background: linear-gradient(135deg, #6856B2, #BA562E)"
        ></div>
      </slot>
      <span
        v-if="!collapsed && brand"
        class="font-heading text-[15px] font-semibold tracking-tight whitespace-nowrap text-ink-primary"
      >{{ brand }}</span>
    </component>

    <!-- ── Nav items ─────────────────────────────────────────────────── -->
    <nav class="flex-1 px-2 py-1 overflow-y-auto">
      <ul class="space-y-0.5">
        <li v-for="item in items" :key="item.key">
          <component
            :is="itemTag(item)"
            v-bind="itemAttrs(item)"
            class="kin-sidebar__item w-full flex items-center gap-2 py-2.5 rounded-full text-left"
            :class="[
              collapsed ? 'justify-center px-0' : 'px-3',
              activeKey === item.key && 'kin-sidebar__item--active',
            ]"
            :title="collapsed ? item.label : null"
            :aria-label="collapsed ? item.label : null"
            @click="$emit('item-click', item.key, $event)"
          >
            <component :is="item.icon" class="w-5 h-5 flex-shrink-0" />
            <span
              v-if="!collapsed"
              class="text-[14px] font-medium whitespace-nowrap"
            >{{ item.label }}</span>
          </component>
        </li>
      </ul>
    </nav>

    <!-- ── Collapse toggle ───────────────────────────────────────────── -->
    <div class="border-t border-border-subtle">
      <button
        type="button"
        class="kin-sidebar__item w-full flex items-center justify-center py-2.5 text-ink-tertiary"
        :title="collapsed ? 'Expand sidebar' : 'Collapse sidebar'"
        :aria-label="collapsed ? 'Expand sidebar' : 'Collapse sidebar'"
        :aria-expanded="!collapsed"
        @click="toggle"
      >
        <ChevronDoubleRightIcon v-if="collapsed" class="w-4 h-4" />
        <ChevronDoubleLeftIcon v-else class="w-4 h-4" />
      </button>
    </div>

    <!-- ── User slot (footer) ────────────────────────────────────────── -->
    <div
      v-if="$slots.user"
      class="p-3 flex items-center gap-2 border-t border-border-subtle"
      :class="collapsed ? 'justify-center' : ''"
    >
      <slot name="user" :collapsed="collapsed"></slot>
    </div>
  </aside>
</template>

<style scoped>
/*
  Width transition on expand/collapse. Respects reduced-motion.
*/
.kin-sidebar {
  transition: width var(--duration-deliberate) var(--ease-out-soft);
}

.kin-sidebar__item {
  color: rgb(var(--ink-secondary));
  background: transparent;
  border: none;
  cursor: pointer;
  text-decoration: none;
  transition: background-color 200ms, color 200ms;
}

.kin-sidebar__item:hover {
  background: rgb(var(--surface-sunken));
}

.dark .kin-sidebar__item:hover {
  /* In dark mode the default surface-sunken is nearly identical to
     surface-raised (both deep charcoal). Use surface-overlay for a
     readable hover. */
  background: rgb(var(--surface-overlay));
}

.kin-sidebar__item--active,
.kin-sidebar__item--active:hover {
  background: rgb(var(--accent-lavender-bold));
  color: rgb(var(--ink-inverse));
}

@media (prefers-reduced-motion: reduce) {
  .kin-sidebar,
  .kin-sidebar__item {
    transition: none;
  }
}
</style>
