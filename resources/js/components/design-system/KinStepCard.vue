<!--
  KinStepCard — single step in a stepper with connector line.
  @see /design-system/step-card  (docs/design/COMPONENT_ROADMAP.md §5.11)
  Props: number, state, title, body, isLast
  Slots: default (override body)

  State:
    done    — success-bold circle + check + line-through title + "Done" chip
    active  — lavender-bold circle + lavender ring + expanded body visible
    default — outlined circle + number + collapsed

  Wrap multiple KinStepCard instances in a container; the vertical connector
  line descends to the next card (suppressed with `isLast`).
-->
<script setup>
import { CheckIcon, ChevronDownIcon, ChevronUpIcon } from '@heroicons/vue/24/outline'

defineProps({
  number: { type: [Number, String], required: true },
  state:  { type: String, default: 'default', validator: (v) => ['done','active','default'].includes(v) },
  title:  { type: String, required: true },
  body:   { type: String, default: '' },
  isLast: { type: Boolean, default: false },
})
</script>

<template>
  <div class="kin-step-card flex gap-3" :class="`kin-step-card--${state}`">
    <!-- Left: badge + connector -->
    <div class="flex flex-col items-center flex-shrink-0" style="width: 36px;">
      <div class="kin-step-card__badge w-9 h-9 rounded-full flex items-center justify-center text-[16px] font-semibold leading-none flex-shrink-0 z-10">
        <CheckIcon v-if="state === 'done'" class="w-4 h-4" />
        <span v-else>{{ number }}</span>
      </div>
      <div v-if="!isLast" class="kin-step-card__connector flex-1" style="width: 2px; min-height: 24px; margin: 2px 0;"></div>
    </div>

    <!-- Right: card content -->
    <div class="flex-1 min-w-0 pb-3">
      <div class="kin-step-card__card rounded-[20px] border p-5">
        <div class="flex items-start justify-between gap-2">
          <p class="kin-step-card__title text-[15px] font-semibold leading-snug">{{ title }}</p>
          <span
            v-if="state === 'done'"
            class="kin-step-card__chip inline-flex items-center h-5 px-2 rounded-full text-[11px] font-medium flex-shrink-0"
          >Done</span>
          <ChevronUpIcon v-else-if="state === 'active'" class="w-4 h-4 flex-shrink-0 text-ink-tertiary" />
          <ChevronDownIcon v-else class="w-4 h-4 flex-shrink-0 text-ink-tertiary" />
        </div>
        <p v-if="(body || $slots.default) && state !== 'default'" class="kin-step-card__body text-[13px] leading-relaxed mt-2">
          <slot>{{ body }}</slot>
        </p>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Badge by state */
.kin-step-card--done .kin-step-card__badge {
  background: rgb(var(--status-success));
  color: rgb(var(--ink-inverse));
}
.kin-step-card--active .kin-step-card__badge {
  background: rgb(var(--accent-lavender-bold));
  color: rgb(var(--ink-inverse));
  box-shadow: 0 0 0 3px rgb(var(--accent-lavender-soft));
}
.kin-step-card--default .kin-step-card__badge {
  background: rgb(var(--surface-raised));
  color: rgb(var(--ink-tertiary));
  border: 2px solid rgb(var(--border-strong));
}

/* Connector */
.kin-step-card__connector { background: rgb(var(--ink-tertiary)); opacity: 0.35; }
.kin-step-card--done .kin-step-card__connector { background: rgb(var(--status-success)); opacity: 1; }

/* Card surface by state */
.kin-step-card__card {
  background: rgb(var(--surface-raised));
  border-color: rgb(var(--border-subtle));
}
.kin-step-card--done .kin-step-card__card {
  background: rgb(var(--surface-sunken));
}
.kin-step-card--active .kin-step-card__card {
  border-color: rgb(var(--accent-lavender-bold));
  box-shadow: 0 0 0 3px rgb(var(--accent-lavender-soft));
}

/* Title color + strike */
.kin-step-card__title { color: rgb(var(--ink-primary)); }
.kin-step-card--done .kin-step-card__title {
  color: rgb(var(--ink-tertiary));
  text-decoration: line-through;
}
.kin-step-card--active .kin-step-card__title { color: rgb(var(--accent-lavender-bold)); }

.kin-step-card__body { color: rgb(var(--ink-secondary)); }

.kin-step-card__chip {
  background: rgb(var(--status-success) / 0.15);
  color: rgb(var(--status-success));
}
</style>
