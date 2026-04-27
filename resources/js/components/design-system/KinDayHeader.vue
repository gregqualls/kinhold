<!--
  KinDayHeader — editorial hero day number + stacked day-of-week/month/count.
  @see /design-system/day-header  (docs/design/COMPONENT_ROADMAP.md §5.6)
  Props: day, weekday, month, eventCount, isToday, size
-->
<script setup>
const props = defineProps({
  day:        { type: [Number, String], required: true },
  weekday:    { type: String, required: true },
  month:      { type: String, required: true },
  eventCount: { type: Number, default: null },
  isToday:    { type: Boolean, default: false },
  size:       { type: String, default: 'md', validator: (v) => ['sm','md','lg'].includes(v) },
})
</script>

<template>
  <div class="kin-day-header flex items-center gap-0" :class="`kin-day-header--${size}`">
    <span class="kin-day-header__num font-heading flex-shrink-0 text-ink-primary">{{ day }}</span>
    <div class="kin-day-header__rule flex-shrink-0 self-stretch" />
    <div class="flex flex-col justify-center gap-1 min-w-0">
      <div class="flex items-center gap-2 flex-wrap">
        <span class="kin-day-header__weekday font-semibold uppercase tracking-widest text-ink-primary">{{ weekday }}</span>
        <span v-if="isToday" class="kin-day-header__today-badge inline-flex items-center rounded-full h-6 font-semibold uppercase flex-shrink-0">TODAY</span>
      </div>
      <span class="kin-day-header__month font-semibold uppercase tracking-widest text-ink-tertiary">{{ month }}</span>
      <span v-if="eventCount !== null" class="kin-day-header__count text-ink-tertiary">· {{ eventCount }} event{{ eventCount === 1 ? '' : 's' }}</span>
    </div>
  </div>
</template>

<style scoped>
.kin-day-header__num {
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-weight: 600;
  letter-spacing: -0.02em;
  line-height: 1;
}
.kin-day-header--sm .kin-day-header__num { font-size: 4rem; }
.kin-day-header--md .kin-day-header__num { font-size: clamp(4rem, 12vw, 8rem); }
.kin-day-header--lg .kin-day-header__num { font-size: 10rem; }

.kin-day-header__rule {
  width: 1px;
  min-height: 3rem;
  margin-left: 1rem;
  margin-right: 1rem;
  background: rgb(var(--border-strong));
}
.kin-day-header--sm .kin-day-header__rule { min-height: 2.5rem; margin-left: .75rem; margin-right: .75rem; }
.kin-day-header--lg .kin-day-header__rule { min-height: 4rem; width: 1.5px; margin-left: 1.5rem; margin-right: 1.5rem; }

.kin-day-header__weekday { font-size: 12px; letter-spacing: 0.12em; }
.kin-day-header__month   { font-size: 11px; letter-spacing: 0.12em; }
.kin-day-header__count   { font-size: 11px; }
.kin-day-header--lg .kin-day-header__weekday { font-size: 13px; }
.kin-day-header--lg .kin-day-header__month   { font-size: 14px; }
.kin-day-header--lg .kin-day-header__count   { font-size: 12px; }

.kin-day-header__today-badge {
  font-size: 10px; letter-spacing: 0.08em; padding: 0 10px;
  background: rgb(var(--accent-lavender-soft));
  color: rgb(var(--accent-lavender-bold));
}
</style>
