<!--
  KinMonthGrid — 7-column month calendar grid.
  @see /design-system/month-grid  (docs/design/COMPONENT_ROADMAP.md §5.5)
  Props: cells, events, today, selected (v-model), density, maxDots, maxPills,
         dayHeaders, eventLabels
  Emits: update:selected, select (day, cell)

  Cell shape: { day, month: 'current' | 'leading' | 'trailing' }
    Non-current cells dim to 40% and don't receive events.
  events map: { [dayNumber]: [accentColor, accentColor, ...] }
    Accent colors: 'lavender' | 'peach' | 'mint' | 'sun'
  eventLabels map (optional, pills mode only): { [dayNumber]: [label, label] }

  Density:
    dots  — up to `maxDots` (default 3) accent dots beneath the number + "+N"
    pills — up to `maxPills` (default 2) truncated title pills + "+N more"
-->
<script setup>
const props = defineProps({
  /** 42-cell array (6 rows × 7 cols). */
  cells: { type: Array, required: true },
  /** Events map keyed by day number → array of accent colors. */
  events: { type: Object, default: () => ({}) },
  /** Event title labels (pills mode). Falls back to accent name if absent. */
  eventLabels: { type: Object, default: () => ({}) },
  /** Today's date number (current month). */
  today: { type: Number, default: null },
  /** Currently selected date number (v-model). */
  selected: { type: Number, default: null },
  /** Density mode. */
  density: {
    type: String,
    default: 'dots',
    validator: (v) => ['dots', 'pills'].includes(v),
  },
  /** Max dots per cell. */
  maxDots:  { type: Number, default: 3 },
  /** Max pills per cell. */
  maxPills: { type: Number, default: 2 },
  /** Day-of-week headers. */
  dayHeaders: { type: Array, default: () => ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] },
})

defineEmits(['update:selected', 'select'])

function isCurrent(c) { return c.month === 'current' }
function isToday(c)   { return isCurrent(c) && c.day === props.today }
function isSelected(c){ return isCurrent(c) && c.day === props.selected }
function dayEvents(c) { return isCurrent(c) ? (props.events[c.day] || []) : [] }
function dayLabels(c) {
  if (!isCurrent(c)) return []
  const labels = props.eventLabels[c.day]
  if (labels) return labels
  return dayEvents(c)   // fallback to accent name as label
}
</script>

<template>
  <div class="kin-month-grid">
    <!-- Day header row -->
    <div class="grid grid-cols-7 gap-1 mb-1">
      <div
        v-for="h in dayHeaders"
        :key="h"
        class="text-center text-[10px] font-semibold uppercase tracking-wider py-1 text-ink-tertiary"
      >{{ h }}</div>
    </div>

    <!-- Calendar cells -->
    <div class="grid grid-cols-7 gap-1">
      <button
        v-for="(cell, idx) in cells"
        :key="idx"
        type="button"
        class="kin-month-grid__cell relative rounded-lg flex flex-col items-center pt-1.5 pb-1.5"
        :class="[
          density === 'pills' ? 'px-1 min-h-[56px] md:min-h-[72px]' : 'min-h-[42px] md:min-h-[52px]',
          isCurrent(cell) && isSelected(cell) && 'kin-month-grid__cell--selected',
          !isCurrent(cell) && 'kin-month-grid__cell--offmonth',
        ]"
        :disabled="!isCurrent(cell)"
        @click="isCurrent(cell) && ($emit('update:selected', cell.day), $emit('select', cell.day, cell))"
      >
        <!-- Date number / today circle -->
        <div
          class="kin-month-grid__num w-7 h-7 flex items-center justify-center rounded-full text-[13px] font-semibold leading-none flex-shrink-0"
          :class="isToday(cell) && 'kin-month-grid__num--today'"
        >{{ cell.day }}</div>

        <!-- Dots density -->
        <div
          v-if="density === 'dots' && dayEvents(cell).length"
          class="flex items-center gap-[3px] mt-1 h-[6px]"
        >
          <span
            v-for="(color, ci) in dayEvents(cell).slice(0, maxDots)"
            :key="ci"
            class="rounded-full flex-shrink-0"
            style="width: 5px; height: 5px;"
            :class="`kin-month-grid__dot--${color}`"
          />
          <span
            v-if="dayEvents(cell).length > maxDots"
            class="text-[8px] font-semibold leading-none text-ink-tertiary"
          >+{{ dayEvents(cell).length - maxDots }}</span>
        </div>

        <!-- Pills density -->
        <template v-else-if="density === 'pills' && dayEvents(cell).length">
          <div class="w-full flex-1 flex flex-col mt-0.5">
            <div
              v-for="(color, pi) in dayEvents(cell).slice(0, maxPills)"
              :key="pi"
              class="rounded-[3px] px-1 mb-[2px] text-[9px] md:text-[10px] font-semibold truncate leading-[14px]"
              :class="`kin-month-grid__pill--${color}`"
            >{{ dayLabels(cell)[pi] ?? color }}</div>
            <div
              v-if="dayEvents(cell).length > maxPills"
              class="text-[9px] md:text-[10px] font-medium leading-[14px] px-1 text-ink-tertiary"
            >+{{ dayEvents(cell).length - maxPills }} more</div>
          </div>
        </template>
      </button>
    </div>
  </div>
</template>

<style scoped>
.kin-month-grid__cell {
  background: rgb(var(--surface-raised));
  border: 1px solid rgb(var(--border-subtle));
  cursor: pointer;
  transition: background-color 120ms, border-color 120ms;
}
.kin-month-grid__cell:hover:not(:disabled):not(.kin-month-grid__cell--selected) {
  background: rgb(var(--surface-sunken));
}

.kin-month-grid__cell--selected {
  background: rgb(var(--accent-lavender-soft));
  border: 2px solid rgb(var(--accent-lavender-bold));
}

.kin-month-grid__cell--offmonth {
  opacity: 0.4;
  cursor: not-allowed;
}
.dark .kin-month-grid__cell--offmonth { opacity: 0.35; }

.kin-month-grid__num {
  color: rgb(var(--ink-primary));
}

.kin-month-grid__num--today {
  background: rgb(var(--accent-lavender-bold));
  color: rgb(var(--ink-inverse));
}

/* Dot colors — accent bold */
.kin-month-grid__dot--lavender { background: rgb(var(--accent-lavender-bold)); }
.kin-month-grid__dot--peach    { background: rgb(var(--accent-peach-bold)); }
.kin-month-grid__dot--mint     { background: rgb(var(--accent-mint-bold)); }
.kin-month-grid__dot--sun      { background: rgb(var(--accent-sun-bold)); }

/* Pill colors — accent soft bg + accent bold text */
.kin-month-grid__pill--lavender { background: rgb(var(--accent-lavender-soft)); color: rgb(var(--accent-lavender-bold)); }
.kin-month-grid__pill--peach    { background: rgb(var(--accent-peach-soft));    color: rgb(var(--accent-peach-bold)); }
.kin-month-grid__pill--mint     { background: rgb(var(--accent-mint-soft));     color: rgb(var(--accent-mint-bold)); }
.kin-month-grid__pill--sun      { background: rgb(var(--accent-sun-soft));      color: rgb(var(--accent-sun-bold)); }

@media (prefers-reduced-motion: reduce) {
  .kin-month-grid__cell { transition: none; }
}
</style>
