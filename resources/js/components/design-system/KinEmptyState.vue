<!--
  KinEmptyState — the "nothing here yet" placeholder.
  @see /design-system/empty-state  (docs/design/COMPONENT_ROADMAP.md §4.9)
  Props: icon, title, description, accentColor, size
  Slots: default (overrides description), #cta (custom CTA markup)

  Centered hero: iridescent glyph in accent-soft circle + title + 1-sentence
  description + single pill CTA. Used on empty task lists, vault categories,
  rewards store, calendar days, chat history, meal plans — anywhere a list
  is empty and the user needs an inviting next step.
-->
<script setup>
import { computed } from 'vue'

const props = defineProps({
  /** Icon component (Heroicon etc). */
  icon: { type: [Object, Function], default: null },
  /** Title — the "nothing here yet" headline. */
  title: { type: String, required: true },
  /** One-sentence description. */
  description: { type: String, default: '' },
  /** Accent color family for the glyph. */
  accentColor: {
    type: String,
    default: 'lavender',
    validator: (v) => ['lavender', 'peach', 'mint', 'sun'].includes(v),
  },
  /** Size scale — sm for inline empty states, md (default), lg for page-level. */
  size: {
    type: String,
    default: 'md',
    validator: (v) => ['sm', 'md', 'lg'].includes(v),
  },
})

const padClass = computed(() => ({
  sm: 'px-6 py-8',
  md: 'px-8 py-16',
  lg: 'px-8 py-20',
}[props.size]))

const glyphClass = computed(() => ({
  sm: 'w-14 h-14',
  md: 'w-20 h-20',
  lg: 'w-24 h-24',
}[props.size]))

const iconClass = computed(() => ({
  sm: 'w-7 h-7',
  md: 'w-10 h-10',
  lg: 'w-12 h-12',
}[props.size]))

const titleClass = computed(() => ({
  sm: 'text-[17px]',
  md: 'text-2xl',
  lg: 'text-3xl',
}[props.size]))
</script>

<template>
  <div
    class="kin-empty flex flex-col items-center text-center"
    :class="[padClass, 'space-y-5']"
  >
    <!-- Iridescent glyph circle -->
    <div
      v-if="icon"
      class="kin-empty__glyph flex-shrink-0 flex items-center justify-center rounded-full"
      :class="[glyphClass, `kin-empty__glyph--${accentColor}`]"
    >
      <component :is="icon" :class="iconClass" />
    </div>

    <!-- Title -->
    <h2
      class="font-heading font-semibold leading-snug text-ink-primary"
      :class="titleClass"
    >{{ title }}</h2>

    <!-- Description (prop OR default slot) -->
    <p
      v-if="description || $slots.default"
      class="text-sm leading-relaxed max-w-[320px] text-ink-secondary"
    >
      <slot>{{ description }}</slot>
    </p>

    <!-- CTA -->
    <div v-if="$slots.cta" class="pt-1">
      <slot name="cta" />
    </div>
  </div>
</template>

<style scoped>
/* Iridescent glyph background — accent-soft tint with a radial wash
   gradient hinting at the category color. */
.kin-empty__glyph {
  background: radial-gradient(
    circle at 30% 30%,
    rgb(var(--accent-lavender-soft) / 0.90) 0%,
    rgb(var(--surface-raised)) 100%
  );
}
.kin-empty__glyph--lavender {
  background: radial-gradient(circle at 30% 30%, rgb(var(--accent-lavender-soft)) 0%, rgb(var(--surface-raised)) 100%);
  color: rgb(var(--accent-lavender-bold));
}
.kin-empty__glyph--peach {
  background: radial-gradient(circle at 30% 30%, rgb(var(--accent-peach-soft)) 0%, rgb(var(--surface-raised)) 100%);
  color: rgb(var(--accent-peach-bold));
}
.kin-empty__glyph--mint {
  background: radial-gradient(circle at 30% 30%, rgb(var(--accent-mint-soft)) 0%, rgb(var(--surface-raised)) 100%);
  color: rgb(var(--accent-mint-bold));
}
.kin-empty__glyph--sun {
  background: radial-gradient(circle at 30% 30%, rgb(var(--accent-sun-soft)) 0%, rgb(var(--surface-raised)) 100%);
  color: rgb(var(--accent-sun-bold));
}
</style>
