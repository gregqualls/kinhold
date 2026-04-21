<!--
  KinAuthorStrip — inline avatar + name/role + meta + action.
  @see /design-system/author-strip  (docs/design/COMPONENT_ROADMAP.md §5.14)
  Props: initials, avatarColor, isAI, name, role, meta, actionLabel, actionIcon
  Slots: #action (override default action button)
  Emits: action-click

  Used on list rows, task headers, vault entry headers, kudos cards,
  AI attribution. `isAI` renders a lavender sparkle avatar instead of initials.
-->
<script setup>
import { SparklesIcon } from '@heroicons/vue/24/outline'

defineProps({
  initials:    { type: String, default: '' },
  avatarColor: { type: String, default: '#6856B2' },
  isAI:        { type: Boolean, default: false },
  name:        { type: String, required: true },
  role:        { type: String, default: '' },
  meta:        { type: String, default: '' },
  actionLabel: { type: String, default: '' },
  actionIcon:  { type: [Object, Function], default: null },
})

defineEmits(['action-click'])
</script>

<template>
  <div class="kin-author-strip flex items-center gap-3 px-4 py-2.5">
    <!-- Avatar -->
    <div
      class="kin-author-strip__avatar w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 text-[11px] font-semibold"
      :class="isAI && 'kin-author-strip__avatar--ai'"
      :style="!isAI ? { background: avatarColor + '22', color: avatarColor } : null"
    >
      <SparklesIcon v-if="isAI" class="w-4 h-4 kin-author-strip__sparkle" />
      <span v-else>{{ initials }}</span>
    </div>

    <!-- Name + role/meta -->
    <div class="flex-1 min-w-0">
      <p class="text-[13px] font-semibold leading-tight truncate text-ink-primary">{{ name }}</p>
      <p v-if="role || meta" class="text-[11px] leading-tight mt-0.5 truncate text-ink-tertiary">
        <span v-if="role">{{ role }}</span>
        <span v-if="role && meta"> · </span>
        <span v-if="meta">{{ meta }}</span>
      </p>
    </div>

    <!-- Action -->
    <slot name="action">
      <button
        v-if="actionLabel"
        type="button"
        class="kin-author-strip__action flex-shrink-0 inline-flex items-center gap-1 rounded-full h-8 px-3 text-[12px] font-medium border"
        @click="$emit('action-click', $event)"
      >
        <component v-if="actionIcon" :is="actionIcon" class="w-3.5 h-3.5" />
        {{ actionLabel }}
      </button>
    </slot>
  </div>
</template>

<style scoped>
.kin-author-strip__avatar--ai {
  background: rgb(var(--accent-lavender-soft));
}
.kin-author-strip__sparkle {
  color: rgb(var(--accent-lavender-bold));
}

.kin-author-strip__action {
  background: transparent;
  border-color: rgb(var(--border-strong));
  color: rgb(var(--ink-primary));
  cursor: pointer;
  transition: background-color 150ms;
}
.kin-author-strip__action:hover { background: rgb(var(--surface-sunken)); }
</style>
