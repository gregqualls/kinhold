<!--
  KinTimelineRow — soft-pill bar spanning a date range.
  @see /design-system/timeline-row  (docs/design/COMPONENT_ROADMAP.md §5.7)
  Props: label, accentColor, icon, avatars, draggable
  Slots: default (overrides label)

  Renders the pill ONLY. Positioning (grid column span, date offset) is a
  consumer concern — drop this inside an absolute-positioned div inside the
  grid row. Accent-soft bg, accent-bold text.
-->
<script setup>
defineProps({
  label:       { type: String, default: '' },
  accentColor: { type: String, default: 'lavender', validator: (v) => ['lavender','peach','mint','sun'].includes(v) },
  icon:        { type: [Object, Function], default: null },
  avatars:     { type: Array, default: () => [] },
  draggable:   { type: Boolean, default: false },
})
</script>

<template>
  <div
    class="kin-timeline-row flex items-center w-full rounded-full px-3 gap-2"
    :class="`kin-timeline-row--${accentColor}`"
    style="min-height: 32px;"
  >
    <component v-if="icon" :is="icon" class="w-3.5 h-3.5 flex-shrink-0 kin-timeline-row__icon" />
    <span class="kin-timeline-row__label text-[13px] font-medium leading-none truncate flex-1">
      <slot>{{ label }}</slot>
    </span>
    <div v-if="avatars.length" class="flex items-center flex-shrink-0 -space-x-1">
      <span
        v-for="(av, i) in avatars"
        :key="i"
        class="inline-flex items-center justify-center w-5 h-5 rounded-full text-[9px] font-bold ring-1 ring-white"
        :style="{ background: av.color, color: '#fff' }"
      >{{ av.initials }}</span>
    </div>
    <div
      v-if="draggable"
      class="kin-timeline-row__handle flex-shrink-0 flex flex-col gap-[3px] cursor-grab opacity-60"
      style="padding: 2px;"
      aria-label="Drag handle"
    >
      <div class="flex gap-[3px]"><span class="w-[3px] h-[3px] rounded-full" /><span class="w-[3px] h-[3px] rounded-full" /></div>
      <div class="flex gap-[3px]"><span class="w-[3px] h-[3px] rounded-full" /><span class="w-[3px] h-[3px] rounded-full" /></div>
    </div>
  </div>
</template>

<style scoped>
.kin-timeline-row--lavender { background: rgb(var(--accent-lavender-soft)); }
.kin-timeline-row--lavender .kin-timeline-row__label,
.kin-timeline-row--lavender .kin-timeline-row__icon,
.kin-timeline-row--lavender .kin-timeline-row__handle span { color: rgb(var(--accent-lavender-bold)); background-color: currentColor; }
.kin-timeline-row--lavender .kin-timeline-row__label,
.kin-timeline-row--lavender .kin-timeline-row__icon { background-color: transparent; }

.kin-timeline-row--peach { background: rgb(var(--accent-peach-soft)); }
.kin-timeline-row--peach .kin-timeline-row__label,
.kin-timeline-row--peach .kin-timeline-row__icon,
.kin-timeline-row--peach .kin-timeline-row__handle span { color: rgb(var(--accent-peach-bold)); background-color: currentColor; }
.kin-timeline-row--peach .kin-timeline-row__label,
.kin-timeline-row--peach .kin-timeline-row__icon { background-color: transparent; }

.kin-timeline-row--mint { background: rgb(var(--accent-mint-soft)); }
.kin-timeline-row--mint .kin-timeline-row__label,
.kin-timeline-row--mint .kin-timeline-row__icon,
.kin-timeline-row--mint .kin-timeline-row__handle span { color: rgb(var(--accent-mint-bold)); background-color: currentColor; }
.kin-timeline-row--mint .kin-timeline-row__label,
.kin-timeline-row--mint .kin-timeline-row__icon { background-color: transparent; }

.kin-timeline-row--sun { background: rgb(var(--accent-sun-soft)); }
.kin-timeline-row--sun .kin-timeline-row__label,
.kin-timeline-row--sun .kin-timeline-row__icon,
.kin-timeline-row--sun .kin-timeline-row__handle span { color: rgb(var(--accent-sun-bold)); background-color: currentColor; }
.kin-timeline-row--sun .kin-timeline-row__label,
.kin-timeline-row--sun .kin-timeline-row__icon { background-color: transparent; }
</style>
