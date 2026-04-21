<!--
  KinToast — single toast notification unit.
  @see /design-system/toast  (docs/design/COMPONENT_ROADMAP.md §4.8)
  Props: status, title, body, icon, actionLabel, closable
  Slots: default (overrides body), #icon (override default icon)
  Emits: action, close

  Stacking / placement is a consumer concern — wrap one or more KinToast
  instances in a fixed-position container (bottom-center is the canonical
  placement; max 3 visible at once).

  Semantic statuses ship with sensible default icons:
    success → CheckCircleIcon
    info    → InformationCircleIcon
    warning → ExclamationTriangleIcon
    failed  → XCircleIcon
    pending → ClockIcon
    paused  → PauseIcon
-->
<script setup>
import { computed } from 'vue'
import {
  CheckCircleIcon,
  InformationCircleIcon,
  ExclamationTriangleIcon,
  XCircleIcon,
  ClockIcon,
  PauseIcon,
  XMarkIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  /** Semantic status — drives accent bar, icon, and action color. */
  status: {
    type: String,
    default: 'info',
    validator: (v) => ['success', 'info', 'warning', 'failed', 'pending', 'paused'].includes(v),
  },
  /** Title line (bold). */
  title: { type: String, required: true },
  /** Body text (optional). */
  body: { type: String, default: '' },
  /** Custom icon component (overrides status default). */
  icon: { type: [Object, Function], default: null },
  /** Action button label (optional). */
  actionLabel: { type: String, default: '' },
  /** Show the X close button. */
  closable: { type: Boolean, default: true },
})

const emit = defineEmits(['action', 'close'])

const DEFAULT_ICONS = {
  success: CheckCircleIcon,
  info:    InformationCircleIcon,
  warning: ExclamationTriangleIcon,
  failed:  XCircleIcon,
  pending: ClockIcon,
  paused:  PauseIcon,
}

const resolvedIcon = computed(() => props.icon || DEFAULT_ICONS[props.status])
</script>

<template>
  <div
    class="kin-toast w-full max-w-[360px] rounded-2xl flex overflow-hidden"
    role="status"
    aria-live="polite"
  >
    <!-- Left accent bar -->
    <div class="kin-toast__bar w-1 flex-shrink-0 self-stretch" :class="`kin-toast__bar--${status}`" />

    <!-- Body -->
    <div class="flex-1 flex items-start gap-2.5 px-3 py-2.5">
      <component
        :is="resolvedIcon"
        class="w-5 h-5 flex-shrink-0 mt-0.5"
        :class="`kin-toast__icon--${status}`"
      />
      <div class="flex-1 min-w-0">
        <p class="text-[13px] font-semibold leading-snug text-ink-primary">{{ title }}</p>
        <p v-if="body || $slots.default" class="text-[12px] mt-0.5 leading-snug text-ink-secondary">
          <slot>{{ body }}</slot>
        </p>
        <button
          v-if="actionLabel"
          type="button"
          class="kin-toast__action mt-1.5 text-[12px] font-semibold"
          :class="`kin-toast__action--${status}`"
          @click="$emit('action')"
        >{{ actionLabel }}</button>
      </div>
      <button
        v-if="closable"
        type="button"
        class="kin-toast__close flex-shrink-0 mt-0.5 rounded p-0.5"
        aria-label="Close"
        @click="$emit('close')"
      >
        <XMarkIcon class="w-4 h-4" />
      </button>
    </div>
  </div>
</template>

<style scoped>
.kin-toast {
  background: rgb(var(--surface-overlay));
  border: 1px solid rgb(var(--border-subtle));
  box-shadow: var(--shadow-hover);
}

/* Accent bars — status colors */
.kin-toast__bar--success { background: rgb(var(--status-success)); }
.kin-toast__bar--info    { background: rgb(var(--status-info)); }
.kin-toast__bar--warning { background: rgb(var(--status-warning)); }
.kin-toast__bar--failed  { background: rgb(var(--status-failed)); }
.kin-toast__bar--pending { background: rgb(var(--status-pending)); }
.kin-toast__bar--paused  { background: rgb(var(--status-paused)); }

/* Icon colors — match the accent bar */
.kin-toast__icon--success { color: rgb(var(--status-success)); }
.kin-toast__icon--info    { color: rgb(var(--status-info)); }
.kin-toast__icon--warning { color: rgb(var(--status-warning)); }
.kin-toast__icon--failed  { color: rgb(var(--status-failed)); }
.kin-toast__icon--pending { color: rgb(var(--status-pending)); }
.kin-toast__icon--paused  { color: rgb(var(--status-paused)); }

/* Action button — status-colored link */
.kin-toast__action {
  background: transparent;
  border: none;
  cursor: pointer;
  padding: 0;
}
.kin-toast__action--success { color: rgb(var(--status-success)); }
.kin-toast__action--info    { color: rgb(var(--status-info)); }
.kin-toast__action--warning { color: rgb(var(--status-warning)); }
.kin-toast__action--failed  { color: rgb(var(--status-failed)); }
.kin-toast__action--pending { color: rgb(var(--status-pending)); }
.kin-toast__action--paused  { color: rgb(var(--status-paused)); }
.kin-toast__action:hover { text-decoration: underline; }

/* Close button */
.kin-toast__close {
  background: transparent;
  border: none;
  cursor: pointer;
  color: rgb(var(--ink-tertiary));
  transition: color 150ms;
}
.kin-toast__close:hover {
  color: rgb(var(--ink-primary));
}
</style>
