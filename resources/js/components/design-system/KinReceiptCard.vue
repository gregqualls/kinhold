<!--
  KinReceiptCard — icon checklist of facts with optional header + inline action.
  @see /design-system/receipt-card  (docs/design/COMPONENT_ROADMAP.md §5.12)
  Props: title, titleIcon, accentColor, rows, actionLabel
  Slots: #actions (override default action button), default (custom rows override)
  Emits: action-click

  Row shape: { label, value, icon, iconAccent? }
    iconAccent (optional) overrides the card's accentColor for a specific row's
    icon circle (e.g. priority flag in peach on a mint-themed card).
-->
<script setup>
defineProps({
  title:       { type: String, default: '' },
  titleIcon:   { type: [Object, Function], default: null },
  accentColor: { type: String, default: 'lavender', validator: (v) => ['lavender','peach','mint','sun'].includes(v) },
  rows:        { type: Array, default: () => [] },
  actionLabel: { type: String, default: '' },
})

defineEmits(['action-click'])
</script>

<template>
  <div
    class="kin-receipt-card rounded-[20px] border flex flex-col bg-surface-raised border-border-subtle"
    :class="`kin-receipt-card--${accentColor}`"
  >
    <!-- Header -->
    <div v-if="title" class="px-5 pt-5 pb-4 flex items-center gap-3">
      <div class="kin-receipt-card__title-icon w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0">
        <component v-if="titleIcon" :is="titleIcon" class="w-4 h-4" />
      </div>
      <p class="text-[15px] font-semibold leading-snug text-ink-primary">{{ title }}</p>
    </div>

    <!-- Rows (prop-driven or default slot override) -->
    <slot>
      <div class="divide-y border-border-subtle">
        <div
          v-for="(row, i) in rows"
          :key="i"
          class="px-5 py-3 flex items-center gap-2.5"
        >
          <div
            class="kin-receipt-card__row-icon w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0"
            :class="`kin-receipt-card__row-icon--${row.iconAccent || accentColor}`"
          >
            <component v-if="row.icon" :is="row.icon" class="w-3.5 h-3.5" />
          </div>
          <span class="text-[12px] font-medium uppercase tracking-wider flex-1 text-ink-tertiary">{{ row.label }}</span>
          <span class="text-[14px] font-medium text-ink-primary">{{ row.value }}</span>
        </div>
      </div>
    </slot>

    <!-- Action -->
    <div v-if="actionLabel || $slots.actions" class="px-5 py-4 border-t border-border-subtle">
      <slot name="actions">
        <button
          type="button"
          class="kin-receipt-card__action w-full h-9 rounded-full border text-[13px] font-medium"
          @click="$emit('action-click', $event)"
        >{{ actionLabel }}</button>
      </slot>
    </div>
  </div>
</template>

<style scoped>
.kin-receipt-card { box-shadow: var(--shadow-resting); }

/* Accent-colored icon circles. Each accent maps to soft-bg + bold-fg. */
.kin-receipt-card--lavender .kin-receipt-card__title-icon,
.kin-receipt-card__row-icon--lavender { background: rgb(var(--accent-lavender-soft)); color: rgb(var(--accent-lavender-bold)); }
.kin-receipt-card--peach .kin-receipt-card__title-icon,
.kin-receipt-card__row-icon--peach { background: rgb(var(--accent-peach-soft)); color: rgb(var(--accent-peach-bold)); }
.kin-receipt-card--mint .kin-receipt-card__title-icon,
.kin-receipt-card__row-icon--mint { background: rgb(var(--accent-mint-soft)); color: rgb(var(--accent-mint-bold)); }
.kin-receipt-card--sun .kin-receipt-card__title-icon,
.kin-receipt-card__row-icon--sun { background: rgb(var(--accent-sun-soft)); color: rgb(var(--accent-sun-bold)); }

/* Action button matches card's accent */
.kin-receipt-card__action {
  background: transparent;
  cursor: pointer;
  transition: background-color 150ms;
}
.kin-receipt-card--lavender .kin-receipt-card__action { border-color: rgb(var(--accent-lavender-bold)); color: rgb(var(--accent-lavender-bold)); }
.kin-receipt-card--peach    .kin-receipt-card__action { border-color: rgb(var(--accent-peach-bold));    color: rgb(var(--accent-peach-bold)); }
.kin-receipt-card--mint     .kin-receipt-card__action { border-color: rgb(var(--accent-mint-bold));     color: rgb(var(--accent-mint-bold)); }
.kin-receipt-card--sun      .kin-receipt-card__action { border-color: rgb(var(--accent-sun-bold));      color: rgb(var(--accent-sun-bold)); }

.kin-receipt-card__action:hover { background: rgb(var(--surface-sunken)); }
</style>
