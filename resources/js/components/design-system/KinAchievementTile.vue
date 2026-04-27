<!--
  KinAchievementTile — hexagonal badge with 4 states.
  @see /design-system/achievement-tile  (docs/design/COMPONENT_ROADMAP.md §5.8)
  Props: state, icon, title, description, meta, accentColor, progress

  States:
    earned      — accent-bold hex fill + inset glow + outer pulse ring
    in-progress — greyed hex + accent arc ring around perimeter (progress 0–1)
    locked      — outline ghost hex, dimmed
    hidden      — dashed outline, "???" title, dimmed

  Hexagonal clip-path uses the same polygon as the bespoke demo.
-->
<script setup>
import { computed } from 'vue'

const props = defineProps({
  state:       { type: String, required: true, validator: (v) => ['earned','in-progress','locked','hidden'].includes(v) },
  icon:        { type: [Object, Function], default: null },
  title:       { type: String, default: '' },
  description: { type: String, default: '' },
  meta:        { type: String, default: '' },
  accentColor: { type: String, default: 'lavender', validator: (v) => ['lavender','peach','mint','sun'].includes(v) },
  /** Progress 0–1 for in-progress state (perimeter ≈ 324 of the 108px hex). */
  progress:    { type: Number, default: 0 },
})

const PERIMETER = 324

const dashOffset = computed(() => PERIMETER * (1 - Math.max(0, Math.min(1, props.progress))))

const displayTitle = computed(() => props.state === 'hidden' ? '???' : props.title)
</script>

<template>
  <div
    class="kin-achievement-tile flex flex-col items-center gap-3"
    :class="[
      `kin-achievement-tile--${state}`,
      `kin-achievement-tile--${accentColor}`,
    ]"
  >
    <div class="relative flex items-center justify-center" style="width: 108px; height: 108px;">
      <!-- Earned: outer pulse ring -->
      <div
        v-if="state === 'earned'"
        class="kin-achievement-tile__pulse hex-tile absolute"
        style="width: 108px; height: 108px;"
      />
      <!-- In-progress: arc around perimeter -->
      <svg
        v-if="state === 'in-progress'"
        width="108" height="108" viewBox="0 0 108 108"
        class="absolute inset-0"
        aria-hidden="true"
      >
        <polygon
          points="54,4 104,29 104,79 54,104 4,79 4,29"
          fill="none" stroke-width="4" stroke-linejoin="round"
          class="kin-achievement-tile__arc-track"
        />
        <polygon
          points="54,4 104,29 104,79 54,104 4,79 4,29"
          fill="none" stroke-width="4" stroke-linejoin="round" stroke-linecap="round"
          :stroke-dasharray="PERIMETER"
          :stroke-dashoffset="dashOffset"
          class="kin-achievement-tile__arc-fill"
        />
      </svg>
      <!-- Hex tile core -->
      <div
        class="kin-achievement-tile__hex hex-tile flex items-center justify-center relative z-10"
        style="width: 80px; height: 80px;"
      >
        <component v-if="icon" :is="icon" class="kin-achievement-tile__icon w-9 h-9" />
      </div>
    </div>
    <div class="text-center space-y-0.5">
      <p class="kin-achievement-tile__title text-[14px] font-semibold">{{ displayTitle }}</p>
      <p v-if="description" class="kin-achievement-tile__desc text-[12px]">{{ description }}</p>
      <p v-if="meta" class="kin-achievement-tile__meta text-[11px] font-medium">{{ meta }}</p>
    </div>
  </div>
</template>

<style scoped>
.hex-tile {
  clip-path: polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%);
}

/* Titles default */
.kin-achievement-tile__title { color: rgb(var(--ink-primary)); }
.kin-achievement-tile__desc  { color: rgb(var(--ink-secondary)); }
.kin-achievement-tile__meta  { color: rgb(var(--ink-tertiary)); }

/* ── EARNED ─────────────────────────────────────────────────────────── */
/* Accent-bold fill + inset glow. */
.kin-achievement-tile--earned.kin-achievement-tile--lavender .kin-achievement-tile__hex {
  background: rgb(var(--accent-lavender-bold));
  box-shadow: inset 0 0 18px rgba(255,255,255,0.22), 0 0 0 3px rgb(var(--accent-lavender-soft));
}
.kin-achievement-tile--earned.kin-achievement-tile--peach .kin-achievement-tile__hex {
  background: rgb(var(--accent-peach-bold));
  box-shadow: inset 0 0 18px rgba(255,255,255,0.22), 0 0 0 3px rgb(var(--accent-peach-soft));
}
.kin-achievement-tile--earned.kin-achievement-tile--mint .kin-achievement-tile__hex {
  background: rgb(var(--accent-mint-bold));
  box-shadow: inset 0 0 18px rgba(255,255,255,0.22), 0 0 0 3px rgb(var(--accent-mint-soft));
}
.kin-achievement-tile--earned.kin-achievement-tile--sun .kin-achievement-tile__hex {
  background: rgb(var(--accent-sun-bold));
  box-shadow: inset 0 0 18px rgba(255,255,255,0.22), 0 0 0 3px rgb(var(--accent-sun-soft));
}
.kin-achievement-tile--earned .kin-achievement-tile__icon { color: rgb(var(--ink-inverse)); }

/* Earned pulse ring */
.kin-achievement-tile--earned.kin-achievement-tile--lavender .kin-achievement-tile__pulse { background: rgb(var(--accent-lavender-bold)); opacity: 0.40; }
.kin-achievement-tile--earned.kin-achievement-tile--peach    .kin-achievement-tile__pulse { background: rgb(var(--accent-peach-bold));    opacity: 0.40; }
.kin-achievement-tile--earned.kin-achievement-tile--mint     .kin-achievement-tile__pulse { background: rgb(var(--accent-mint-bold));     opacity: 0.40; }
.kin-achievement-tile--earned.kin-achievement-tile--sun      .kin-achievement-tile__pulse { background: rgb(var(--accent-sun-bold));      opacity: 0.40; }

/* ── IN-PROGRESS ────────────────────────────────────────────────────── */
.kin-achievement-tile--in-progress .kin-achievement-tile__hex {
  background: rgb(var(--border-subtle));
}
.kin-achievement-tile--in-progress .kin-achievement-tile__icon { color: rgb(var(--ink-tertiary)); }

.kin-achievement-tile--in-progress.kin-achievement-tile--lavender .kin-achievement-tile__arc-track { stroke: rgb(var(--accent-lavender-soft)); }
.kin-achievement-tile--in-progress.kin-achievement-tile--lavender .kin-achievement-tile__arc-fill  { stroke: rgb(var(--accent-lavender-bold)); }
.kin-achievement-tile--in-progress.kin-achievement-tile--lavender .kin-achievement-tile__meta      { color: rgb(var(--accent-lavender-bold)); }

.kin-achievement-tile--in-progress.kin-achievement-tile--peach .kin-achievement-tile__arc-track { stroke: rgb(var(--accent-peach-soft)); }
.kin-achievement-tile--in-progress.kin-achievement-tile--peach .kin-achievement-tile__arc-fill  { stroke: rgb(var(--accent-peach-bold)); }
.kin-achievement-tile--in-progress.kin-achievement-tile--peach .kin-achievement-tile__meta      { color: rgb(var(--accent-peach-bold)); }

.kin-achievement-tile--in-progress.kin-achievement-tile--mint .kin-achievement-tile__arc-track { stroke: rgb(var(--accent-mint-soft)); }
.kin-achievement-tile--in-progress.kin-achievement-tile--mint .kin-achievement-tile__arc-fill  { stroke: rgb(var(--accent-mint-bold)); }
.kin-achievement-tile--in-progress.kin-achievement-tile--mint .kin-achievement-tile__meta      { color: rgb(var(--accent-mint-bold)); }

.kin-achievement-tile--in-progress.kin-achievement-tile--sun .kin-achievement-tile__arc-track { stroke: rgb(var(--accent-sun-soft)); }
.kin-achievement-tile--in-progress.kin-achievement-tile--sun .kin-achievement-tile__arc-fill  { stroke: rgb(var(--accent-sun-bold)); }
.kin-achievement-tile--in-progress.kin-achievement-tile--sun .kin-achievement-tile__meta      { color: rgb(var(--accent-sun-bold)); }

/* ── LOCKED ─────────────────────────────────────────────────────────── */
.kin-achievement-tile--locked { opacity: 0.55; }
.kin-achievement-tile--locked .kin-achievement-tile__hex { background: rgb(var(--border-subtle)); }
.kin-achievement-tile--locked .kin-achievement-tile__icon,
.kin-achievement-tile--locked .kin-achievement-tile__title,
.kin-achievement-tile--locked .kin-achievement-tile__desc,
.kin-achievement-tile--locked .kin-achievement-tile__meta { color: rgb(var(--ink-tertiary)); }

/* ── HIDDEN ─────────────────────────────────────────────────────────── */
.kin-achievement-tile--hidden { opacity: 0.50; }
.kin-achievement-tile--hidden .kin-achievement-tile__hex { background: rgb(var(--border-subtle)); }
.kin-achievement-tile--hidden .kin-achievement-tile__icon,
.kin-achievement-tile--hidden .kin-achievement-tile__title,
.kin-achievement-tile--hidden .kin-achievement-tile__desc,
.kin-achievement-tile--hidden .kin-achievement-tile__meta { color: rgb(var(--ink-tertiary)); }
.kin-achievement-tile--hidden .kin-achievement-tile__title { letter-spacing: 0.12em; }

@media (prefers-reduced-motion: reduce) {
  .hex-tile { animation: none !important; transition: none !important; }
}
</style>
