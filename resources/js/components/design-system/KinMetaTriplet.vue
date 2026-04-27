<!--
  KinMetaTriplet — inline row of icon + label+value cells.
  @see /design-system/meta-triplet  (docs/design/COMPONENT_ROADMAP.md §5.13)
  Props: items
  Slots: (none)

  Item shape: { label, value, icon, accent? }
    accent: 'lavender' | 'peach' | 'mint' | 'sun' (default 'lavender') — tints the icon.

  Horizontal inline cells — icon on left, label + value stacked right.
  gap-4 mobile, gap-8 desktop.
-->
<script setup>
defineProps({
  items: { type: Array, required: true },
})
</script>

<template>
  <div class="kin-meta-triplet flex items-center gap-4 sm:gap-8">
    <div
      v-for="(item, idx) in items"
      :key="idx"
      class="flex items-center gap-2 min-w-0"
    >
      <component
        :is="item.icon"
        class="kin-meta-triplet__icon w-[18px] h-[18px] flex-shrink-0"
        :class="`kin-meta-triplet__icon--${item.accent || 'lavender'}`"
      />
      <div class="min-w-0">
        <p class="text-[10px] font-semibold uppercase tracking-widest leading-none mb-0.5 text-ink-tertiary">{{ item.label }}</p>
        <p class="text-[14px] font-semibold leading-tight truncate text-ink-primary">{{ item.value }}</p>
      </div>
    </div>
  </div>
</template>

<style scoped>
.kin-meta-triplet__icon--lavender { color: rgb(var(--accent-lavender-bold)); }
.kin-meta-triplet__icon--peach    { color: rgb(var(--accent-peach-bold)); }
.kin-meta-triplet__icon--mint     { color: rgb(var(--accent-mint-bold)); }
.kin-meta-triplet__icon--sun      { color: rgb(var(--accent-sun-bold)); }
</style>
