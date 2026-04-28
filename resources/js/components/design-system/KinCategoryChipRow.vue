<!--
  KinCategoryChipRow — horizontal icon+label category chip row with overflow strategies.
  @see /design-system/category-chip-row  (docs/design/COMPONENT_ROADMAP.md §4.3)
  Props: categories, activeKeys (v-model), mode, clearable, mobileVisible
  Emits: update:activeKeys, change (key[])

  Category shape: { key, label, accent, icon }
    accent: 'lavender' | 'peach' | 'mint' | 'sun'
    icon:   Vue component (Heroicon etc.)

  Modes:
    wrap    — flex-wrap, all chips visible, wraps to multiple lines
    scroll  — horizontal scroll with flex-shrink-0
    hybrid  — wraps on desktop; mobile shows `mobileVisible` + "+N more" overflow chip

  Multi-select by default. Emits the current active-keys array on every toggle.
-->
<script setup>
import { computed } from 'vue'
import { CheckIcon, XMarkIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  /** Category options: { key, label, accent, icon }. */
  categories: { type: Array, required: true },
  /** Active keys (supports v-model:active-keys). */
  activeKeys: { type: Array, default: () => [] },
  /** Overflow mode. */
  mode: {
    type: String,
    default: 'wrap',
    validator: (v) => ['wrap', 'scroll', 'hybrid'].includes(v),
  },
  /** Show the trailing "Clear" chip that resets activeKeys. */
  clearable: { type: Boolean, default: true },
  /** In hybrid mode on mobile, how many chips to show before "+N more". */
  mobileVisible: { type: Number, default: 4 },
})

const emit = defineEmits(['update:activeKeys', 'change'])

function toggle(key) {
  const curr = props.activeKeys.slice()
  const idx = curr.indexOf(key)
  if (idx === -1) curr.push(key)
  else curr.splice(idx, 1)
  emit('update:activeKeys', curr)
  emit('change', curr)
}

function clearAll() {
  emit('update:activeKeys', [])
  emit('change', [])
}

// Hybrid mobile overflow
const mobileHiddenCount = computed(() =>
  Math.max(0, props.categories.length - props.mobileVisible)
)

const containerClass = computed(() => ({
  wrap:   'flex flex-wrap gap-2',
  scroll: 'flex gap-2 overflow-x-auto pb-1',
  hybrid: 'flex flex-wrap gap-2',   // md+ wraps; mobile overflow handled via responsive class on the chips
}[props.mode]))

function isActive(key) {
  return props.activeKeys.includes(key)
}
</script>

<template>
  <div
    :class="containerClass"
    :style="mode === 'scroll' ? 'scrollbar-width: none; -ms-overflow-style: none;' : null"
    role="group"
  >
    <template v-for="(cat, i) in categories" :key="cat.key">
      <button
        type="button"
        class="kin-cat-chip flex-shrink-0 inline-flex items-center gap-1.5 rounded-full h-7 px-3 text-[12px] font-medium"
        :class="[
          `kin-cat-chip--${cat.accent}`,
          isActive(cat.key) && 'kin-cat-chip--active',
          // Hybrid mode: hide chips beyond mobileVisible on narrow screens.
          mode === 'hybrid' && i >= mobileVisible && 'hidden md:inline-flex',
        ]"
        :aria-pressed="isActive(cat.key)"
        @click="toggle(cat.key)"
      >
        <CheckIcon v-if="isActive(cat.key)" class="w-3.5 h-3.5 flex-shrink-0" />
        <component :is="cat.icon" v-else class="w-3.5 h-3.5 flex-shrink-0" />
        {{ cat.label }}
      </button>
    </template>

    <!-- Hybrid: "+N more" overflow chip (mobile only) -->
    <button
      v-if="mode === 'hybrid' && mobileHiddenCount > 0"
      type="button"
      class="kin-cat-chip kin-cat-chip--overflow flex-shrink-0 inline-flex items-center gap-1 rounded-full h-7 px-3 text-[12px] font-semibold md:hidden"
    >
      +{{ mobileHiddenCount }} more
    </button>

    <!-- Clear chip -->
    <button
      v-if="clearable && activeKeys.length > 0"
      type="button"
      class="kin-cat-chip kin-cat-chip--ghost flex-shrink-0 inline-flex items-center gap-1 rounded-full h-7 px-3 text-[12px] font-medium"
      @click="clearAll"
    >
      <XMarkIcon class="w-3.5 h-3.5 flex-shrink-0" />
      Clear
    </button>
  </div>
</template>

<style scoped>
.kin-cat-chip {
  background: rgb(var(--surface-raised));
  color: rgb(var(--ink-secondary));
  border: 1px solid rgb(var(--border-subtle));
  cursor: pointer;
  transition: background-color 150ms, color 150ms, border-color 150ms;
}

.kin-cat-chip:hover:not(.kin-cat-chip--active) {
  color: rgb(var(--ink-primary));
  border-color: rgb(var(--border-strong));
}

/* Active: accent-soft bg, accent-bold text, accent-bold border */
.kin-cat-chip--active.kin-cat-chip--lavender {
  background: rgb(var(--accent-lavender-soft));
  color:      rgb(var(--accent-lavender-bold));
  border-color: rgb(var(--accent-lavender-bold));
}
.kin-cat-chip--active.kin-cat-chip--peach {
  background: rgb(var(--accent-peach-soft));
  color:      rgb(var(--accent-peach-bold));
  border-color: rgb(var(--accent-peach-bold));
}
.kin-cat-chip--active.kin-cat-chip--mint {
  background: rgb(var(--accent-mint-soft));
  color:      rgb(var(--accent-mint-bold));
  border-color: rgb(var(--accent-mint-bold));
}
.kin-cat-chip--active.kin-cat-chip--sun {
  background: rgb(var(--accent-sun-soft));
  color:      rgb(var(--accent-sun-bold));
  border-color: rgb(var(--accent-sun-bold));
}

/* Ghost (clear chip) */
.kin-cat-chip--ghost {
  background: transparent;
  color: rgb(var(--ink-tertiary));
  border-color: rgb(var(--border-subtle));
}

/* Overflow (+N more) chip */
.kin-cat-chip--overflow {
  background: rgb(var(--surface-raised));
  color: rgb(var(--accent-lavender-bold));
  border: 1px solid rgb(var(--accent-lavender-bold));
}

/* Hide horizontal scrollbar on scroll mode */
.overflow-x-auto::-webkit-scrollbar {
  display: none;
}

@media (prefers-reduced-motion: reduce) {
  .kin-cat-chip { transition: none; }
}
</style>
