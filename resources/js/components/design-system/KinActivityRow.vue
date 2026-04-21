<!--
  KinActivityRow — conversational activity feed row (avatar + sentence + meta + time).
  @see /design-system/activity-row  (docs/design/COMPONENT_ROADMAP.md §5.9)
  Props: initials, avatarColor, text, sub, meta, metaAccent, time
  Slots: #actions (optional inline actions; right-aligned)

  Used in points/kudos feeds, family activity timelines, social-style events.
-->
<script setup>
defineProps({
  initials:    { type: String, default: '' },
  avatarColor: { type: String, default: '#6856B2' },
  text:        { type: String, required: true },
  sub:         { type: String, default: '' },
  meta:        { type: String, default: '' },
  metaAccent:  { type: String, default: 'lavender', validator: (v) => ['lavender','peach','mint','sun','success','failed'].includes(v) },
  time:        { type: String, default: '' },
})
</script>

<template>
  <div class="kin-activity-row flex items-start gap-3 py-3">
    <div
      class="kin-activity-row__avatar flex-shrink-0 inline-flex items-center justify-center w-8 h-8 rounded-full text-[11px] font-bold"
      :style="{ background: avatarColor, color: '#fff' }"
    >{{ initials }}</div>
    <div class="flex-1 min-w-0">
      <p class="text-[13px] leading-snug text-ink-primary">{{ text }}</p>
      <p v-if="sub" class="text-[12px] italic text-ink-secondary mt-0.5">{{ sub }}</p>
      <div class="flex items-center gap-2 mt-1">
        <span
          v-if="meta"
          class="kin-activity-row__meta inline-flex items-center rounded-full h-5 px-2 text-[11px] font-semibold"
          :class="`kin-activity-row__meta--${metaAccent}`"
        >{{ meta }}</span>
        <span v-if="time" class="text-[11px] text-ink-tertiary">{{ time }}</span>
        <slot name="actions" />
      </div>
    </div>
  </div>
</template>

<style scoped>
.kin-activity-row__meta--lavender { background: rgb(var(--accent-lavender-soft)); color: rgb(var(--accent-lavender-bold)); }
.kin-activity-row__meta--peach    { background: rgb(var(--accent-peach-soft));    color: rgb(var(--accent-peach-bold)); }
.kin-activity-row__meta--mint     { background: rgb(var(--accent-mint-soft));     color: rgb(var(--accent-mint-bold)); }
.kin-activity-row__meta--sun      { background: rgb(var(--accent-sun-soft));      color: rgb(var(--accent-sun-bold)); }
.kin-activity-row__meta--success  { background: rgb(var(--status-success) / 0.15); color: rgb(var(--status-success)); }
.kin-activity-row__meta--failed   { background: rgb(var(--status-failed) / 0.15);  color: rgb(var(--status-failed)); }
</style>
