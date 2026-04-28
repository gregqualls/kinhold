<!--
  KinEventTile — calendar event card.
  @see /design-system/event-tile  (docs/design/COMPONENT_ROADMAP.md §5.3)
  Props: title, time, allDay, accentColor, source, visibility, avatars,
         location, variant, width
  Slots: #meta (override meta line), default (override entire content)

  Variants:
    block  — filled pastel rounded-xl tile (default; month grid, day row, dashboard)
    span   — elongated rounded-full pill (week-view multi-day, meal plan window)

  Sources — drives border treatment:
    manual / google / ics — solid border-less tile
    task                   — dashed border in accent-bold (task has due-date)

  Visibility — privacy override of accent styling:
    visible   — shows real content with accent color
    busy      — neutral surface-sunken + "Busy" placeholder title
    private   — neutral + lock icon + "Private" placeholder title
-->
<script setup>
import { computed } from 'vue'
import { ClockIcon, LockClosedIcon, CheckBadgeIcon, MapPinIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  /** Event title. */
  title: { type: String, default: '' },
  /** Time range e.g. "5:00 – 6:30 PM". Hide with all-day. */
  time: { type: String, default: '' },
  /** All-day event — replaces time with "All day". */
  allDay: { type: Boolean, default: false },
  /** Accent color family. */
  accentColor: {
    type: String,
    default: 'lavender',
    validator: (v) => ['lavender', 'peach', 'mint', 'sun'].includes(v),
  },
  /** Source type — `task` gets a dashed border + badge icon. */
  source: {
    type: String,
    default: 'manual',
    validator: (v) => ['manual', 'task', 'google', 'ics'].includes(v),
  },
  /** Visibility — `busy` and `private` override accent styling. */
  visibility: {
    type: String,
    default: 'visible',
    validator: (v) => ['visible', 'busy', 'private'].includes(v),
  },
  /** Avatars at right edge: [{ initials, color }]. */
  avatars: { type: Array, default: () => [] },
  /** Location string — renders below time if present. */
  location: { type: String, default: '' },
  /** Visual variant. */
  variant: {
    type: String,
    default: 'block',
    validator: (v) => ['block', 'span'].includes(v),
  },
  /** Width override — useful in grid cells. CSS value. */
  width: { type: String, default: null },
})

const displayTitle = computed(() => {
  if (props.visibility === 'busy')    return 'Busy'
  if (props.visibility === 'private') return 'Private'
  return props.title
})

const displayTime = computed(() => props.allDay ? 'All day' : props.time)

const isNeutral = computed(() =>
  props.visibility === 'busy' || props.visibility === 'private'
)
</script>

<template>
  <div
    class="kin-event-tile cursor-pointer"
    :class="[
      variant === 'span' ? 'rounded-full px-3 py-1' : 'rounded-xl px-2.5 py-1.5',
      isNeutral ? 'kin-event-tile--neutral' : `kin-event-tile--${accentColor}`,
      source === 'task' && !isNeutral && 'kin-event-tile--task',
    ]"
    :style="width ? { width } : null"
  >
    <!-- Span variant is single-line: title + (optional avatars) -->
    <template v-if="variant === 'span'">
      <div class="flex items-center justify-between gap-2">
        <span class="kin-event-tile__title text-[12px] font-semibold leading-none truncate">
          <LockClosedIcon v-if="visibility === 'private'" class="w-3 h-3 inline-block mr-1 -mt-0.5" />
          {{ displayTitle }}
        </span>
        <div v-if="avatars.length" class="flex -space-x-1 shrink-0 ml-2">
          <span
            v-for="(av, i) in avatars"
            :key="i"
            class="inline-flex items-center justify-center w-4 h-4 rounded-full text-[8px] font-bold ring-1 ring-white"
            :style="{ background: av.color, color: '#fff' }"
          >{{ av.initials }}</span>
        </div>
      </div>
    </template>

    <!-- Block variant: title on top, meta row below -->
    <template v-else>
      <div class="flex items-center gap-1">
        <LockClosedIcon v-if="visibility === 'private'" class="w-3 h-3 shrink-0 kin-event-tile__title" />
        <p class="kin-event-tile__title text-[13px] font-semibold leading-tight truncate">{{ displayTitle }}</p>
      </div>

      <slot name="meta">
        <div class="flex items-center justify-between gap-2 mt-0.5">
          <p
            v-if="displayTime || source === 'task' || location"
            class="kin-event-tile__meta text-[11px] font-medium flex items-center gap-1 min-w-0"
          >
            <ClockIcon v-if="!allDay && source !== 'task' && time" class="w-3 h-3 shrink-0" />
            <CheckBadgeIcon v-if="source === 'task'" class="w-3 h-3 shrink-0 kin-event-tile__task-badge" />
            <span class="truncate">{{ displayTime }}</span>
          </p>
          <div v-if="avatars.length" class="flex -space-x-1.5 shrink-0">
            <span
              v-for="(av, i) in avatars"
              :key="i"
              class="inline-flex items-center justify-center w-5 h-5 rounded-full text-[9px] font-bold ring-1 ring-white"
              :style="{ background: av.color, color: '#fff' }"
            >{{ av.initials }}</span>
          </div>
        </div>
        <p v-if="location && !isNeutral" class="kin-event-tile__meta text-[11px] font-medium flex items-center gap-1 mt-0.5 truncate">
          <MapPinIcon class="w-3 h-3 shrink-0" />
          <span class="truncate">{{ location }}</span>
        </p>
      </slot>
    </template>
  </div>
</template>

<style scoped>
.kin-event-tile {
  box-shadow: var(--shadow-resting);
  transition: transform 150ms, box-shadow 150ms;
}
@media (prefers-reduced-motion: no-preference) {
  .kin-event-tile:hover { transform: translateY(-1px); box-shadow: var(--shadow-hover); }
}

/* Meta row + icon share ink-secondary by default */
.kin-event-tile__meta {
  color: rgb(var(--ink-secondary));
}

/* Accent variants — soft bg, bold title */
.kin-event-tile--lavender { background: rgb(var(--accent-lavender-soft)); }
.kin-event-tile--lavender .kin-event-tile__title { color: rgb(var(--accent-lavender-bold)); }
.kin-event-tile--peach    { background: rgb(var(--accent-peach-soft)); }
.kin-event-tile--peach    .kin-event-tile__title { color: rgb(var(--accent-peach-bold)); }
.kin-event-tile--mint     { background: rgb(var(--accent-mint-soft)); }
.kin-event-tile--mint     .kin-event-tile__title { color: rgb(var(--accent-mint-bold)); }
.kin-event-tile--sun      { background: rgb(var(--accent-sun-soft)); }
.kin-event-tile--sun      .kin-event-tile__title { color: rgb(var(--accent-sun-bold)); }

/* Task source — dashed border in matching accent */
.kin-event-tile--task.kin-event-tile--lavender { border: 1.5px dashed rgb(var(--accent-lavender-bold)); }
.kin-event-tile--task.kin-event-tile--peach    { border: 1.5px dashed rgb(var(--accent-peach-bold)); }
.kin-event-tile--task.kin-event-tile--mint     { border: 1.5px dashed rgb(var(--accent-mint-bold)); }
.kin-event-tile--task.kin-event-tile--sun      { border: 1.5px dashed rgb(var(--accent-sun-bold)); }
/* Task badge icon matches title color */
.kin-event-tile--lavender .kin-event-tile__task-badge { color: rgb(var(--accent-lavender-bold)); }
.kin-event-tile--peach    .kin-event-tile__task-badge { color: rgb(var(--accent-peach-bold)); }
.kin-event-tile--mint     .kin-event-tile__task-badge { color: rgb(var(--accent-mint-bold)); }
.kin-event-tile--sun      .kin-event-tile__task-badge { color: rgb(var(--accent-sun-bold)); }

/* Neutral — busy / private */
.kin-event-tile--neutral {
  background: rgb(var(--surface-sunken));
  border: 1px solid rgb(var(--border-strong));
}
.kin-event-tile--neutral .kin-event-tile__title { color: rgb(var(--ink-secondary)); }
.kin-event-tile--neutral .kin-event-tile__meta  { color: rgb(var(--ink-tertiary)); }
</style>
