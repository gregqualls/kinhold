<!--
  KinPageHero — shared page header for operate surfaces (Vault, Points,
  Rewards, History, Achievements, Settings, etc.). One primitive, one
  type scale, one spacing rhythm — swap the hand-rolled headers at the
  top of each view for a KinPageHero call.

  Shape slots (all optional except default):
    #eyebrow   — small uppercase label above the title (date, section)
    default    — the title content (interpolation, <em>, icons welcome)
    #subtitle  — small ink-tertiary line under the title
    #actions   — right-aligned action buttons (0..N)

  Props:
    show-back  — render a chevron-left button on the left of the title
    back-label — aria-label for the back button (default "Back")

  Emits:
    back       — fired when the back button is tapped
-->
<script setup>
import { ChevronLeftIcon } from '@heroicons/vue/24/outline'

defineProps({
  showBack: { type: Boolean, default: false },
  backLabel: { type: String, default: 'Back' },
})

const emit = defineEmits(['back'])
</script>

<template>
  <header class="flex items-end justify-between gap-4 mb-4 md:mb-6">
    <div class="min-w-0 flex-1">
      <div
        v-if="$slots.eyebrow || showBack"
        class="flex items-center gap-1.5 mb-1.5 min-h-[20px]"
      >
        <button
          v-if="showBack"
          type="button"
          class="kin-pressable -ml-1 p-1 rounded-md text-ink-tertiary hover:text-ink-primary transition-[color,transform] duration-quick ease-out-soft"
          :aria-label="backLabel"
          @click="emit('back')"
        >
          <ChevronLeftIcon class="w-4 h-4" />
        </button>
        <span
          v-if="$slots.eyebrow"
          class="text-[10px] font-semibold uppercase tracking-wider text-ink-tertiary"
        >
          <slot name="eyebrow"></slot>
        </span>
      </div>

      <h1 class="text-xl sm:text-2xl font-semibold font-heading text-ink-primary tracking-tight leading-tight">
        <slot></slot>
      </h1>

      <p v-if="$slots.subtitle" class="text-xs text-ink-tertiary mt-1">
        <slot name="subtitle"></slot>
      </p>
    </div>

    <div
      v-if="$slots.actions"
      class="flex items-center gap-2 shrink-0 pb-1"
    >
      <slot name="actions"></slot>
    </div>
  </header>
</template>
