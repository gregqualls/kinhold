<!--
  KinPageHero: shared page header for every app surface. One type scale and
  one spacing rhythm for the top of every view.

  From md up it renders inside the desktop TopBar (#kin-topbar-title) on a
  single line, so the page body starts with content instead of a title band.
  Below md the TopBar is hidden and the hero stays in the page, stacked.

  Shape slots (all optional except default):
    #eyebrow:   small uppercase label above the title (date, section)
    default:    the title content (interpolation, <em>, icons welcome)
    #subtitle:  small ink-tertiary line under the title
    #actions:   right-aligned action buttons (0..N)

  Props:
    show-back:  render a chevron-left button on the left of the title
    back-label: aria-label for the back button (default "Back")

  Emits:
    back:       fired when the back button is tapped
-->
<script setup>
import { onBeforeUnmount, ref } from 'vue'
import { ChevronLeftIcon } from '@heroicons/vue/24/outline'

defineProps({
  showBack: { type: Boolean, default: false },
  backLabel: { type: String, default: 'Back' },
})

const emit = defineEmits(['back'])

// Matches the md breakpoint TopBar uses for `hidden md:flex`.
const desktopQuery = window.matchMedia('(min-width: 768px)')
const isDesktop = ref(desktopQuery.matches)
const onViewportChange = (event) => { isDesktop.value = event.matches }
desktopQuery.addEventListener('change', onViewportChange)
onBeforeUnmount(() => desktopQuery.removeEventListener('change', onViewportChange))
</script>

<template>
  <Teleport to="#kin-topbar-title" defer :disabled="!isDesktop">
    <header class="flex items-end justify-between gap-4 mb-4 md:mb-0 md:items-center">
      <div class="min-w-0 flex-1 flex flex-col md:flex-row md:items-baseline md:gap-2">
        <div
          v-if="$slots.eyebrow || showBack"
          class="flex items-center gap-1.5 mb-1.5 min-h-[20px] md:mb-0 md:self-center shrink-0"
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

        <h1 class="text-xl sm:text-2xl md:text-xl lg:text-2xl font-semibold font-heading text-ink-primary tracking-tight leading-tight md:shrink-0 md:max-w-full md:truncate">
          <slot></slot>
        </h1>

        <p v-if="$slots.subtitle" class="text-xs text-ink-tertiary mt-1 md:mt-0 md:truncate">
          <slot name="subtitle"></slot>
        </p>
      </div>

      <div
        v-if="$slots.actions"
        class="flex items-center gap-2 shrink-0 pb-1 md:pb-0"
      >
        <slot name="actions"></slot>
      </div>
    </header>
  </Teleport>
</template>
