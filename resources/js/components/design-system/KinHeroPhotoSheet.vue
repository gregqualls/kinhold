<!--
  KinHeroPhotoSheet — edge-to-edge photo + sliding content sheet.
  @see /design-system/hero-photo-sheet  (docs/design/COMPONENT_ROADMAP.md §5.15)
  Props: photo, photoAlt, title, sheetTop, backLabel
  Slots: default (sheet body), #actions (top-right frosted buttons), #meta (under title), #back (override back button)
  Emits: back

  Renders a hero-photo-plus-sheet detail view. Photo fills the top portion;
  sheet slides up from the bottom. For parallax/scroll behavior, the consumer
  binds `sheetTop` reactively based on scroll position. The frosted glass
  back/action buttons and grab handle ship inside.
-->
<script setup>
import { ArrowLeftIcon } from '@heroicons/vue/24/outline'

defineProps({
  photo:    { type: String, required: true },
  photoAlt: { type: String, default: '' },
  title:    { type: String, default: '' },
  /** Sheet top position (CSS value like "70%" or "20%"). Bind reactively for scroll-driven parallax. */
  sheetTop: { type: String, default: '60%' },
  backLabel:{ type: String, default: 'Back' },
})

defineEmits(['back'])
</script>

<template>
  <div class="kin-hero-photo-sheet relative overflow-hidden bg-ink-primary">
    <!-- Photo -->
    <div
      class="kin-hero-photo-sheet__photo absolute inset-0"
      :style="{ backgroundImage: `url('${photo}')` }"
      role="img"
      :aria-label="photoAlt"
    >
      <!-- Status bar scrim -->
      <div class="kin-hero-photo-sheet__scrim absolute top-0 left-0 right-0" style="height: 80px;" />

      <!-- Back button (top-left) -->
      <div class="absolute" style="top: 24px; left: 16px;">
        <slot name="back">
          <button
            type="button"
            class="kin-hero-photo-sheet__frost-btn flex items-center justify-center"
            :aria-label="backLabel"
            style="width: 40px; height: 40px;"
            @click="$emit('back')"
          >
            <ArrowLeftIcon class="w-4 h-4" />
          </button>
        </slot>
      </div>

      <!-- Actions (top-right) -->
      <div class="absolute flex gap-2" style="top: 24px; right: 16px;">
        <slot name="actions" />
      </div>
    </div>

    <!-- Sliding sheet -->
    <div
      class="kin-hero-photo-sheet__sheet absolute left-0 right-0 bottom-0 flex flex-col"
      :style="{ top: sheetTop }"
    >
      <!-- Grab handle -->
      <div class="flex justify-center pt-2 pb-1 flex-shrink-0">
        <div class="w-8 h-1 rounded-full kin-hero-photo-sheet__handle" />
      </div>

      <!-- Title -->
      <div class="px-4 pt-1 flex-shrink-0">
        <h2 v-if="title" class="kin-hero-photo-sheet__title font-heading">{{ title }}</h2>
        <div v-if="$slots.meta" class="kin-hero-photo-sheet__meta mt-2">
          <slot name="meta" />
        </div>
      </div>

      <!-- Body -->
      <div class="px-4 pt-3 pb-4 flex-1 overflow-y-auto">
        <slot />
      </div>
    </div>
  </div>
</template>

<style scoped>
.kin-hero-photo-sheet__photo {
  background-size: cover;
  background-position: center;
}

.kin-hero-photo-sheet__scrim {
  background: linear-gradient(to bottom, rgba(0,0,0,0.30), transparent);
}

/* Frosted glass back/action buttons */
.kin-hero-photo-sheet__frost-btn {
  background: rgba(255, 255, 255, 0.88);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  border: 1px solid rgba(255, 255, 255, 0.60);
  border-radius: 9999px;
  color: rgb(var(--ink-primary));
  cursor: pointer;
}

.kin-hero-photo-sheet__sheet {
  background: rgb(var(--surface-raised));
  border-radius: 28px 28px 0 0;
  box-shadow: 0 -8px 32px rgba(0, 0, 0, 0.18);
  transition: top var(--duration-deliberate) var(--ease-out-soft);
}

.kin-hero-photo-sheet__handle {
  background: rgb(var(--ink-tertiary));
}

.kin-hero-photo-sheet__title {
  font-size: 20px;
  font-weight: 700;
  letter-spacing: -0.02em;
  line-height: 1.2;
  color: rgb(var(--ink-primary));
  margin: 0;
}

@media (prefers-reduced-motion: reduce) {
  .kin-hero-photo-sheet__sheet { transition: none; }
}
</style>
