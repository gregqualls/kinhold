<!--
  KinAvatarPicker — pill cards with circle avatar + inline name for member selection.
  @see /design-system/avatar-picker  (docs/design/COMPONENT_ROADMAP.md §4.6)
  Props: members, activeKeys (v-model), multi, size
  Emits: update:activeKeys, change (keys)

  Member shape: { key, name, label?, src?, icon?, accentColor, disabled? }
    `name`   — full name; drives KinAvatar initials generation (first letters of each word).
    `label`  — pill display text; defaults to `name` if omitted. Use when you want
               e.g. "Maya Qualls" initials on the avatar but "Maya" on the pill.
    `src`    — photo URL (optional).
    `icon`   — Vue component (optional — renders when no src and no initials desired).
    accentColor: 'lavender' | 'peach' | 'mint' | 'sun'.

  Active pill: accent-lavender-soft bg + accent-lavender-bold border + bold text.
  In multi-select mode, a checkmark overlays the avatar circle.

  Used for task assignment, kudos recipient, vault share, meal plan cook assignment.
-->
<script setup>
import { computed } from 'vue'
import KinAvatar from './KinAvatar.vue'
import { CheckIcon } from '@heroicons/vue/24/solid'

const props = defineProps({
  /** Member objects. */
  members: { type: Array, required: true },
  /** Selected keys (supports v-model:active-keys). Array always, even for single-select. */
  activeKeys: { type: Array, default: () => [] },
  /** Multi-select mode. When false, clicking a member replaces the selection. */
  multi: { type: Boolean, default: false },
  /** Size: md (40px avatar, h-12 pill) or lg (56px avatar, h-[68px] pill). */
  size: {
    type: String,
    default: 'md',
    validator: (v) => ['md', 'lg'].includes(v),
  },
})

const emit = defineEmits(['update:activeKeys', 'change'])

function onClick(member) {
  if (member.disabled) return
  let next
  if (props.multi) {
    next = props.activeKeys.slice()
    const idx = next.indexOf(member.key)
    if (idx === -1) next.push(member.key)
    else next.splice(idx, 1)
  } else {
    next = [member.key]
  }
  emit('update:activeKeys', next)
  emit('change', next)
}

function isActive(member) {
  return props.activeKeys.includes(member.key)
}

// Sizing lookup — pill height, avatar size, text size, gap.
const layout = computed(() => props.size === 'lg'
  ? { pillClass: 'h-[68px] pl-2 pr-4 gap-2.5', avatarSize: 'lg', textClass: 'text-[15px]' }
  : { pillClass: 'h-12 pl-2 pr-3 gap-2',       avatarSize: 'md', textClass: 'text-[13px]' }
)
</script>

<template>
  <div class="flex gap-2 flex-wrap" role="group">
    <button
      v-for="m in members"
      :key="m.key"
      type="button"
      :aria-pressed="isActive(m)"
      :disabled="m.disabled"
      class="kin-avatar-picker__pill inline-flex items-center rounded-full"
      :class="[
        layout.pillClass,
        isActive(m) && 'kin-avatar-picker__pill--active',
        m.disabled && 'kin-avatar-picker__pill--disabled',
      ]"
      @click="onClick(m)"
    >
      <span class="relative flex items-center justify-center">
        <KinAvatar
          :src="m.src"
          :name="m.name"
          :icon="m.icon"
          :color="m.accentColor"
          :size="layout.avatarSize"
        />
        <!-- Multi-select checkmark overlay -->
        <span
          v-if="multi && isActive(m)"
          class="kin-avatar-picker__check absolute inset-0 flex items-center justify-center rounded-full"
          aria-hidden="true"
        >
          <CheckIcon class="w-4 h-4 text-white" />
        </span>
      </span>
      <span class="font-medium leading-none kin-avatar-picker__name" :class="layout.textClass">
        {{ m.label ?? m.name }}
      </span>
    </button>
  </div>
</template>

<style scoped>
.kin-avatar-picker__pill {
  background: rgb(var(--surface-raised));
  border: 1.5px solid rgb(var(--border-subtle));
  color: rgb(var(--ink-secondary));
  cursor: pointer;
  transition: background-color 150ms, border-color 150ms, color 150ms;
}

.kin-avatar-picker__pill:hover:not(.kin-avatar-picker__pill--disabled):not(.kin-avatar-picker__pill--active) {
  border-color: rgb(var(--border-strong));
  color: rgb(var(--ink-primary));
}

.kin-avatar-picker__pill--active {
  background: rgb(var(--accent-lavender-soft));
  border-color: rgb(var(--accent-lavender-bold));
  color: rgb(var(--accent-lavender-bold));
}

.kin-avatar-picker__pill--disabled {
  opacity: 0.4;
  filter: grayscale(1);
  cursor: not-allowed;
}

.kin-avatar-picker__check {
  background: rgb(var(--accent-lavender-bold) / 0.80);
}

.kin-avatar-picker__name {
  color: inherit;
}

@media (prefers-reduced-motion: reduce) {
  .kin-avatar-picker__pill { transition: none; }
}
</style>
