<!--
  KinWeekStrip — horizontal day-of-week pill selector with event dots.
  @see /design-system/week-strip  (docs/design/COMPONENT_ROADMAP.md §5.4)
  Props: days, todayIndex, selectedIndex (v-model), maxDots
  Emits: update:selectedIndex, select (index, day)

  Day shape: { letter, num, events: [accentColor] }
    letter  — day-of-week label; only the first character is shown.
    num     — date number.
    events  — array of accent names ('lavender'|'peach'|'mint'|'sun') for dots.

  Today pill  — lavender-soft bg + lavender-bold border + bold number.
  Selected   — lavender-bold fill + inverse text.
  Past days  — 45% opacity, cursor-not-allowed, not clickable.
  Event dots — up to `maxDots` (default 4); overflow shows "+N".
-->
<script setup>
import { computed } from 'vue'

const props = defineProps({
  /** Day objects. */
  days: { type: Array, required: true },
  /** Index of "today" — past days render at 45% opacity. */
  todayIndex: { type: Number, default: 0 },
  /** Currently selected day index (v-model:selectedIndex). */
  selectedIndex: { type: Number, default: null },
  /** Max dots shown per day before collapsing to "+N". */
  maxDots: { type: Number, default: 4 },
})

const emit = defineEmits(['update:selectedIndex', 'select'])

function onClick(i, day) {
  if (i < props.todayIndex) return
  emit('update:selectedIndex', i)
  emit('select', i, day)
}

function isPast(i)    { return i < props.todayIndex }
function isToday(i)   { return i === props.todayIndex }
function isSelected(i){ return props.selectedIndex === i && !isToday(i) }

function visibleDots(events) {
  return (events || []).slice(0, props.maxDots)
}
function extraCount(events) {
  const n = events?.length ?? 0
  return n > props.maxDots ? n - props.maxDots : 0
}
</script>

<template>
  <div class="kin-week-strip flex items-start justify-between gap-1.5">
    <div
      v-for="(day, i) in days"
      :key="i"
      class="flex flex-col items-center gap-1.5 flex-shrink-0"
    >
      <button
        type="button"
        class="kin-week-strip__pill flex flex-col items-center justify-center gap-0.5 rounded-full transition-all"
        :class="[
          isToday(i) && 'kin-week-strip__pill--today',
          isSelected(i) && 'kin-week-strip__pill--selected',
          isPast(i) && 'kin-week-strip__pill--past',
        ]"
        :disabled="isPast(i)"
        :aria-label="`${day.letter} ${day.num}`"
        :aria-pressed="isSelected(i) || isToday(i)"
        @click="onClick(i, day)"
      >
        <span class="kin-week-strip__letter text-[10px] font-semibold uppercase tracking-widest leading-none">{{ day.letter.charAt(0) }}</span>
        <span class="kin-week-strip__num text-[16px] font-semibold leading-none">{{ day.num }}</span>
      </button>
      <div
        class="flex items-center justify-center gap-[3px] h-[6px]"
        :class="isPast(i) && 'opacity-45'"
      >
        <span
          v-for="(evt, di) in visibleDots(day.events)"
          :key="di"
          class="inline-block w-[5px] h-[5px] rounded-full flex-shrink-0"
          :class="`kin-week-strip__dot--${evt}`"
        ></span>
        <span
          v-if="extraCount(day.events) > 0"
          class="kin-week-strip__overflow text-[9px] font-bold leading-none"
        >+{{ extraCount(day.events) }}</span>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Pill container sizing */
.kin-week-strip__pill {
  width: 40px;
  height: 56px;
  background: transparent;
  border: 1.5px solid rgb(var(--border-subtle));
  cursor: pointer;
}

.kin-week-strip__letter { color: rgb(var(--ink-tertiary)); }
.kin-week-strip__num    { color: rgb(var(--ink-primary)); }

/* Today — lavender-soft fill + lavender-bold border */
.kin-week-strip__pill--today {
  background: rgb(var(--accent-lavender-soft));
  border-color: rgb(var(--accent-lavender-bold));
}
.kin-week-strip__pill--today .kin-week-strip__letter,
.kin-week-strip__pill--today .kin-week-strip__num {
  color: rgb(var(--accent-lavender-bold));
}

/* Selected (not today) — lavender-bold fill + inverse text */
.kin-week-strip__pill--selected {
  background: rgb(var(--accent-lavender-bold));
  border-color: rgb(var(--accent-lavender-bold));
}
.kin-week-strip__pill--selected .kin-week-strip__letter,
.kin-week-strip__pill--selected .kin-week-strip__num {
  color: #FFFFFF;
}
.dark .kin-week-strip__pill--selected .kin-week-strip__letter,
.dark .kin-week-strip__pill--selected .kin-week-strip__num {
  color: rgb(var(--surface-app));
}

/* Past — 45% opacity + cursor-not-allowed */
.kin-week-strip__pill--past {
  opacity: 0.45;
  cursor: not-allowed;
}

/* Event dots — accent colors */
.kin-week-strip__dot--lavender { background: rgb(var(--accent-lavender-bold)); }
.kin-week-strip__dot--peach    { background: rgb(var(--accent-peach-bold)); }
.kin-week-strip__dot--mint     { background: rgb(var(--accent-mint-bold)); }
.kin-week-strip__dot--sun      { background: rgb(var(--accent-sun-bold)); }

.kin-week-strip__overflow {
  color: rgb(var(--ink-tertiary));
}

@media (prefers-reduced-motion: reduce) {
  .kin-week-strip__pill { transition: none; }
}
</style>
