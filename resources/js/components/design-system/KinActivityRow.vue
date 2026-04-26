<!--
  KinActivityRow — conversational activity feed row.
  @see /design-system/activity-row  (docs/design/COMPONENT_ROADMAP.md §5.9)
  Props: initials, avatarColor, text, sub, meta, metaAccent, time, showActions
  Slots: #actions (override default Nice/Reply buttons)
  Emits: nice, reply

  Layout: avatar (left) · text + inline meta pill · subtext · optional action
  buttons (Nice / Reply) · timestamp (right). Used in points/kudos feeds,
  family activity timelines, social-style events.
-->
<script setup>
import { HandThumbUpIcon, ChatBubbleLeftIcon } from '@heroicons/vue/24/outline'

defineProps({
  initials:    { type: String, default: '' },
  avatarColor: { type: String, default: '#6856B2' },
  text:        { type: String, required: true },
  sub:         { type: String, default: '' },
  meta:        { type: String, default: '' },
  metaAccent:  { type: String, default: 'lavender', validator: (v) => ['lavender','peach','mint','sun','success','failed'].includes(v) },
  time:        { type: String, default: '' },
  /** Show inline Nice + Reply action buttons (for social rows like kudos). */
  showActions: { type: Boolean, default: false },
})

defineEmits(['nice', 'reply'])
</script>

<template>
  <div class="kin-activity-row flex items-start gap-3 py-3">
    <div
      class="kin-activity-row__avatar flex-shrink-0 inline-flex items-center justify-center w-9 h-9 rounded-full text-[13px] font-semibold"
      :style="{ background: avatarColor, color: '#fff' }"
    >{{ initials }}</div>

    <div class="flex-1 min-w-0 space-y-0.5">
      <div class="flex items-baseline gap-2 flex-wrap">
        <p class="text-[13px] font-medium leading-snug text-ink-primary">{{ text }}</p>
        <span
          v-if="meta"
          class="kin-activity-row__meta inline-flex items-center h-5 px-2 rounded-full text-[11px] font-medium"
          :class="`kin-activity-row__meta--${metaAccent}`"
        >{{ meta }}</span>
      </div>

      <p v-if="sub" class="text-[12px] leading-relaxed italic text-ink-secondary">{{ sub }}</p>

      <slot name="actions">
        <div v-if="showActions" class="flex items-center gap-2 pt-1">
          <button
            type="button"
            class="kin-activity-row__action kin-activity-row__action--nice inline-flex items-center gap-1 h-6 px-2.5 rounded-full text-[11px] font-medium border"
            @click="$emit('nice')"
          >
            <HandThumbUpIcon class="w-3 h-3" />
            Nice
          </button>
          <button
            type="button"
            class="kin-activity-row__action kin-activity-row__action--reply inline-flex items-center gap-1 h-6 px-2.5 rounded-full text-[11px] font-medium border"
            @click="$emit('reply')"
          >
            <ChatBubbleLeftIcon class="w-3 h-3" />
            Reply
          </button>
        </div>
      </slot>
    </div>

    <p v-if="time" class="text-[11px] flex-shrink-0 tabular-nums pt-0.5 text-ink-tertiary">{{ time }}</p>
  </div>
</template>

<style scoped>
.kin-activity-row__meta--lavender { background: rgb(var(--accent-lavender-soft)); color: rgb(var(--accent-lavender-bold)); }
.kin-activity-row__meta--peach    { background: rgb(var(--accent-peach-soft));    color: rgb(var(--accent-peach-bold)); }
.kin-activity-row__meta--mint     { background: rgb(var(--accent-mint-soft));     color: rgb(var(--accent-mint-bold)); }
.kin-activity-row__meta--sun      { background: rgb(var(--accent-sun-soft));      color: rgb(var(--accent-sun-bold)); }
.kin-activity-row__meta--success  { background: rgb(var(--status-success) / 0.15); color: rgb(var(--status-success)); }
.kin-activity-row__meta--failed   { background: rgb(var(--status-failed) / 0.15);  color: rgb(var(--status-failed)); }

.kin-activity-row__action {
  cursor: pointer;
  transition: background-color 150ms, border-color 150ms;
}
.kin-activity-row__action--nice {
  background: rgb(var(--accent-mint-soft));
  border-color: rgb(var(--accent-mint-soft));
  color: rgb(var(--accent-mint-bold));
}
.kin-activity-row__action--nice:hover {
  background: rgb(var(--accent-mint-bold) / 0.15);
}
.kin-activity-row__action--reply {
  background: transparent;
  border-color: rgb(var(--border-subtle));
  color: rgb(var(--ink-tertiary));
}
.kin-activity-row__action--reply:hover {
  border-color: rgb(var(--border-strong));
  color: rgb(var(--ink-secondary));
}
</style>
