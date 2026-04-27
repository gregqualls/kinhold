<!--
  KinQuickActions — grid of square icon+label quick-action tiles.
  @see /design-system/quick-actions  (docs/design/COMPONENT_ROADMAP.md §4.5)
  Props: actions, columns, mobileColumns
  Emits: action-click (key, event)

  Action shape: { key, label, icon, badge?, disabled?, to?, href? }
    icon:    Vue component (Heroicon etc). Rendered at 24px in ink-primary.
    badge:   Optional small pill (top-right).
    to/href: Optional polymorphic route/link.

  Used on module landing pages (Dashboard, Tasks, Meal Plan, Points, Vault).
  Default layout: 4-up desktop, 2-up mobile. Override via props.
-->
<script setup>
import { computed } from 'vue'
import { RouterLink } from 'vue-router'

const props = defineProps({
  /** Action objects. */
  actions: { type: Array, required: true },
  /** Columns at md+ breakpoint (desktop). */
  columns: { type: Number, default: 4 },
  /** Columns below md (mobile). */
  mobileColumns: { type: Number, default: 2 },
})

const emit = defineEmits(['action-click'])

function tag(a) {
  if (a.disabled) return 'button'
  if (a.to)      return RouterLink
  if (a.href)    return 'a'
  return 'button'
}
function attrs(a) {
  if (a.disabled)     return { type: 'button', disabled: true }
  if (a.to)           return { to: a.to }
  if (a.href)         return { href: a.href }
  return { type: 'button' }
}

// Tailwind needs whole class strings to purge-pick them, so we map via lookup.
const colsClass = computed(() => {
  const mobile = {
    1: 'grid-cols-1', 2: 'grid-cols-2', 3: 'grid-cols-3',
    4: 'grid-cols-4', 5: 'grid-cols-5', 6: 'grid-cols-6',
  }[props.mobileColumns] ?? 'grid-cols-2'
  const desktop = {
    1: 'md:grid-cols-1', 2: 'md:grid-cols-2', 3: 'md:grid-cols-3',
    4: 'md:grid-cols-4', 5: 'md:grid-cols-5', 6: 'md:grid-cols-6',
  }[props.columns] ?? 'md:grid-cols-4'
  return `${mobile} ${desktop}`
})
</script>

<template>
  <div class="grid gap-3" :class="colsClass">
    <component
      :is="tag(action)"
      v-for="action in actions"
      :key="action.key"
      v-bind="attrs(action)"
      class="kin-qa relative flex flex-col items-center justify-center gap-2 rounded-card p-4 border bg-surface-raised border-border-subtle"
      :class="[
        action.disabled && 'kin-qa--disabled',
        !action.disabled && 'kin-qa--interactive',
      ]"
      style="min-height: 88px;"
      @click="!action.disabled && $emit('action-click', action.key, $event)"
    >
      <span
        v-if="action.badge !== null && action.badge !== undefined"
        class="kin-qa__badge absolute top-2 right-2 flex items-center justify-center rounded-full text-[10px] font-semibold"
        style="min-width: 18px; height: 18px; padding: 0 5px;"
      >{{ action.badge }}</span>

      <component :is="action.icon" class="w-6 h-6 flex-shrink-0 text-ink-primary" />
      <span class="text-[12px] font-medium leading-tight text-center text-ink-primary">{{ action.label }}</span>
    </component>
  </div>
</template>

<style scoped>
.kin-qa {
  box-shadow: var(--shadow-resting);
  transition: transform var(--duration-quick) var(--ease-out-soft),
              box-shadow var(--duration-quick) var(--ease-out-soft),
              border-color var(--duration-quick) var(--ease-out-soft);
  text-decoration: none;
}

.kin-qa--interactive {
  cursor: pointer;
}

.kin-qa--interactive:hover {
  border-color: rgb(var(--border-strong));
  box-shadow: var(--shadow-hover);
}

.dark .kin-qa--interactive:hover {
  background: rgb(var(--surface-overlay));
  box-shadow: var(--shadow-elevated);
}

@media (prefers-reduced-motion: no-preference) {
  .kin-qa--interactive:hover {
    transform: translateY(-2px);
  }
  .kin-qa--interactive:active {
    transform: translateY(0);
    box-shadow: var(--shadow-resting);
  }
}

.kin-qa--disabled {
  opacity: 0.40;
  cursor: not-allowed;
}

.kin-qa__badge {
  background: rgb(var(--accent-peach-bold));
  color: #FFFFFF;
}
</style>
