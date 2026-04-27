<!--
  KinModalSheet — responsive overlay container.
  @see /design-system/modal-sheet  (docs/design/COMPONENT_ROADMAP.md §4.7)
  Props: modelValue (v-model), title, closable, size, desktopWidth
  Slots: default (body), #actions (footer button pair), #header (override default title row)
  Emits: update:modelValue, close

  Responsive behavior (one rule, two expressions):
    < md breakpoint: bottom sheet — slides up from the bottom with grab handle.
    >= md breakpoint: centered modal — fades in with soft scale.

  Backdrop: rgba(0,0,0,0.48). Click to dismiss unless `:closable="false"`.
  ESC to dismiss. Respects prefers-reduced-motion (no slide/scale, just fade).
-->
<script setup>
import { computed, onMounted, onBeforeUnmount, watch } from 'vue'

const props = defineProps({
  /** v-model open state. */
  modelValue: { type: Boolean, default: false },
  /** Header title. Omit to hide the header (or provide a #header slot). */
  title: { type: String, default: '' },
  /** Allow backdrop-click + ESC + close-button to dismiss. */
  closable: { type: Boolean, default: true },
  /** Desktop modal width. */
  desktopWidth: { type: String, default: '440px' },
  /** Size preset: sm / md / lg — controls desktop width + content padding. */
  size: {
    type: String,
    default: 'md',
    validator: (v) => ['sm', 'md', 'lg'].includes(v),
  },
})

const emit = defineEmits(['update:modelValue', 'close'])

function close() {
  if (!props.closable) return
  emit('update:modelValue', false)
  emit('close')
}

function onBackdrop(e) {
  if (e.target === e.currentTarget) close()
}

function onKeydown(e) {
  if (e.key === 'Escape' && props.modelValue) close()
}

// Body scroll lock while open.
function updateBodyScroll(open) {
  if (typeof document === 'undefined') return
  document.body.style.overflow = open ? 'hidden' : ''
}

watch(() => props.modelValue, (v) => updateBodyScroll(v), { immediate: false })

onMounted(() => {
  window.addEventListener('keydown', onKeydown)
  if (props.modelValue) updateBodyScroll(true)
})
onBeforeUnmount(() => {
  window.removeEventListener('keydown', onKeydown)
  updateBodyScroll(false)
})

const widthStyle = computed(() => {
  if (props.size === 'sm') return { maxWidth: '380px' }
  if (props.size === 'lg') return { maxWidth: '640px' }
  return { maxWidth: props.desktopWidth }
})
</script>

<template>
  <Teleport to="body">
    <Transition name="kin-ms-fade">
      <div
        v-if="modelValue"
        class="kin-ms-root fixed inset-0 z-[100] flex items-end md:items-center md:justify-center"
        role="dialog"
        aria-modal="true"
        :aria-label="title || 'Dialog'"
        @click.self="onBackdrop"
      >
        <!-- Backdrop -->
        <div
          class="kin-ms-backdrop absolute inset-0"
          @click="onBackdrop"
        />

        <!-- Surface -->
        <Transition name="kin-ms-surface" appear>
          <div
            v-if="modelValue"
            class="kin-ms-surface relative w-full md:w-auto"
            :class="[
              'rounded-t-sheet md:rounded-card',
              'bg-surface-overlay',
            ]"
            :style="widthStyle"
          >
            <!-- Mobile grab handle -->
            <div class="md:hidden flex justify-center pt-3 pb-1" aria-hidden="true">
              <div class="w-9 h-1 rounded-full bg-border-strong"></div>
            </div>

            <!-- Header -->
            <slot name="header">
              <div
                v-if="title"
                class="flex items-center justify-between px-5 pt-4 md:pt-5 pb-3 border-b border-border-subtle"
              >
                <p class="text-[14px] font-semibold text-ink-primary">{{ title }}</p>
                <button
                  v-if="closable"
                  type="button"
                  class="kin-ms-close flex items-center justify-center"
                  aria-label="Close"
                  @click="close"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
            </slot>

            <!-- Body -->
            <div class="px-5 py-4 text-ink-secondary text-[13px]">
              <slot />
            </div>

            <!-- Footer (actions) -->
            <div
              v-if="$slots.actions"
              class="px-5 pb-5 pt-1"
            >
              <slot name="actions" />
            </div>
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>
</template>

<style scoped>
.kin-ms-backdrop {
  background: rgba(0, 0, 0, 0.48);
}

.kin-ms-surface {
  box-shadow: var(--shadow-modal);
}

.kin-ms-close {
  width: 28px;
  height: 28px;
  border-radius: 9999px;
  border: none;
  cursor: pointer;
  background: rgb(var(--surface-sunken));
  color: rgb(var(--ink-secondary));
  transition: background-color 150ms, color 150ms;
}
.kin-ms-close:hover {
  background: rgb(var(--surface-raised));
  color: rgb(var(--ink-primary));
}

/* Root fade — for backdrop + host in/out */
.kin-ms-fade-enter-active,
.kin-ms-fade-leave-active {
  transition: opacity var(--duration-sheet) var(--ease-out-soft);
}
.kin-ms-fade-enter-from,
.kin-ms-fade-leave-to {
  opacity: 0;
}

/* Surface motion — slide up on mobile, gentle scale on desktop */
.kin-ms-surface-enter-active,
.kin-ms-surface-leave-active {
  transition: transform var(--duration-sheet) var(--ease-out-soft),
              opacity var(--duration-sheet) var(--ease-out-soft);
}
/* Mobile: slide from bottom */
.kin-ms-surface-enter-from,
.kin-ms-surface-leave-to {
  transform: translateY(30px);
  opacity: 0;
}
/* Desktop: scale+fade (overrides transform via viewport media query) */
@media (min-width: 768px) {
  .kin-ms-surface-enter-from,
  .kin-ms-surface-leave-to {
    transform: scale(0.97);
  }
}

@media (prefers-reduced-motion: reduce) {
  .kin-ms-fade-enter-active,
  .kin-ms-fade-leave-active,
  .kin-ms-surface-enter-active,
  .kin-ms-surface-leave-active {
    transition: opacity 150ms;
  }
  .kin-ms-surface-enter-from,
  .kin-ms-surface-leave-to {
    transform: none;
  }
}
</style>
