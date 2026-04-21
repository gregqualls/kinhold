<!--
  KinPhotoCard — edge-to-edge photo with legibility scrim + overlay title.
  @see /design-system/photo-card  (docs/design/COMPONENT_ROADMAP.md §2.2)
  Props: src, alt, aspect, title, subtitle, as, href, to, loading, interactive
  Slots: #badges, #actions, #overlay, default
-->
<script setup>
import { computed, ref } from 'vue'
import { RouterLink } from 'vue-router'

// ── Props ────────────────────────────────────────────────────────────────────
const props = defineProps({
  /** Photo URL. If omitted or broken, renders a surface-raised fallback. */
  src: {
    type: String,
    default: null,
  },
  /** Alt text for the photo. */
  alt: {
    type: String,
    default: '',
  },
  /**
   * Aspect ratio of the photo area.
   * square   = 1:1
   * video    = 16:9
   * portrait = 3:4
   * tall     = 4:5 (common recipe/restaurant card shape)
   * auto     = content-driven height (use sparingly)
   */
  aspect: {
    type: String,
    default: 'video',
    validator: (v) => ['square', 'video', 'portrait', 'tall', 'auto'].includes(v),
  },
  /** Overlay title rendered bottom-left over the photo. */
  title: {
    type: String,
    default: '',
  },
  /** Overlay secondary line below the title. */
  subtitle: {
    type: String,
    default: '',
  },
  /** Root tag when neither `to` nor `href` is provided. */
  as: {
    type: String,
    default: 'article',
    validator: (v) => ['div', 'article', 'a', 'button'].includes(v),
  },
  /** Renders an <a> root (overrides `as`). */
  href: {
    type: String,
    default: null,
  },
  /** Renders a <RouterLink> root (overrides `href` and `as`). */
  to: {
    type: [String, Object],
    default: null,
  },
  /** Native img loading attribute. */
  loading: {
    type: String,
    default: 'lazy',
    validator: (v) => ['lazy', 'eager'].includes(v),
  },
  /**
   * Enables hover lift + cursor-pointer.
   * Auto-set to true when `href`, `to`, or `as="button"` is used.
   */
  interactive: {
    type: Boolean,
    default: false,
  },
  /**
   * Overlay text size scale. Drives title/subtitle font size + bottom padding.
   * sm = tight grid cards, md = standard, lg = hero cards.
   */
  overlaySize: {
    type: String,
    default: 'md',
    validator: (v) => ['sm', 'md', 'lg'].includes(v),
  },
})

// ── Image error fallback ──────────────────────────────────────────────────────
const imgError = ref(false)

function onImgError() {
  imgError.value = true
}

// ── Show the photo area or the fallback ──────────────────────────────────────
const showPhoto = computed(() => !!props.src && !imgError.value)

// ── Root element resolution (to > href > as) ─────────────────────────────────
const tag = computed(() => {
  if (props.to)   return RouterLink
  if (props.href) return 'a'
  return props.as
})

const extraAttrs = computed(() => {
  if (props.to)   return { to: props.to }
  if (props.href) return { href: props.href }
  return {}
})

// ── Interactive state ────────────────────────────────────────────────────────
const isInteractive = computed(
  () => props.interactive || !!props.to || !!props.href || props.as === 'button'
)

// ── Aspect-ratio class for the photo wrapper ─────────────────────────────────
const aspectClass = computed(() => ({
  square:   'aspect-square',
  video:    'aspect-video',
  portrait: 'aspect-[3/4]',
  tall:     'aspect-[4/5]',
  auto:     '',            // no forced aspect; container is content-sized
}[props.aspect] ?? 'aspect-video'))

// ── Overlay size: padding + title/subtitle typography ────────────────────────
const overlayPaddingClass = computed(() => ({
  sm: 'p-3',
  md: 'p-4',
  lg: 'p-5',
}[props.overlaySize] ?? 'p-4'))

const overlayTitleStyle = computed(() => ({
  sm: 'font-size: 12px; line-height: 1.3;',
  md: 'font-size: 15px; line-height: 1.3;',
  lg: 'font-size: 19px; line-height: 1.3;',
}[props.overlaySize] ?? 'font-size: 15px; line-height: 1.3;'))

const overlaySubtitleStyle = computed(() => ({
  sm: 'font-size: 10px;',
  md: 'font-size: 12px;',
  lg: 'font-size: 13px;',
}[props.overlaySize] ?? 'font-size: 12px;'))
</script>

<template>
  <component
    :is="tag"
    v-bind="extraAttrs"
    :class="[
      // ── Base structure ────────────────────────────────────────────────────
      'kin-photo-card',
      'relative rounded-card overflow-hidden',
      'shadow-resting',
      // ── Interactive lift ─────────────────────────────────────────────────
      isInteractive && 'kin-photo-card--interactive cursor-pointer',
      isInteractive && 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-accent-lavender-bold',
    ]"
  >
    <!-- ── Photo area (or fallback) ──────────────────────────────────────────── -->
    <div
      :class="[
        'relative w-full',
        aspectClass,
        // Fallback appearance when no photo
        !showPhoto && 'bg-surface-raised border border-border-subtle',
      ]"
    >
      <!-- Photo -->
      <img
        v-if="src"
        :src="src"
        :alt="alt"
        :loading="loading"
        class="absolute inset-0 w-full h-full object-cover kin-photo-card__img"
        @error="onImgError"
      />

      <!-- Fallback: no src or broken src -->
      <div
        v-if="!showPhoto"
        class="absolute inset-0 flex items-center justify-center"
        aria-hidden="true"
      >
        <!-- Muted placeholder icon (image glyph) -->
        <svg
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          stroke-width="1"
          stroke="currentColor"
          class="w-10 h-10 text-ink-tertiary opacity-40"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5
               1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0
               0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5
               1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0
               1 1-.75 0 .375.375 0 0 1 .75 0Z"
          />
        </svg>
      </div>

      <!-- Scrim — flat translucent black across the entire card for legibility.
           Always rendered when a photo is present; never when fallback is shown.
           Decision: gradient scrims have weak zones on bright photos; flat 0.45
           is the locked choice per extraction-phase gotcha #5 (§COMPONENT_ROADMAP.md). -->
      <div
        v-if="showPhoto"
        class="kin-photo-card__scrim absolute inset-0 pointer-events-none"
        aria-hidden="true"
      />

      <!-- Badges slot — top-right corner, e.g. status chips, countdown chips -->
      <div
        v-if="$slots.badges"
        class="absolute top-3 right-3 flex flex-col items-end gap-1.5 pointer-events-none"
      >
        <slot name="badges" />
      </div>

      <!-- Actions slot — bottom-right corner, e.g. bookmark, share -->
      <div
        v-if="$slots.actions"
        class="absolute bottom-3 right-3 flex items-center gap-2"
      >
        <slot name="actions" />
      </div>

      <!-- Overlay content — bottom-left title/subtitle or fully custom -->
      <div
        v-if="$slots.overlay || title || subtitle"
        :class="['absolute inset-x-0 bottom-0', overlayPaddingClass]"
      >
        <slot name="overlay">
          <!-- Default overlay: title + subtitle -->
          <p
            v-if="title"
            class="font-semibold text-white"
            :style="overlayTitleStyle + ' text-shadow: 0 1px 3px rgba(0,0,0,0.40);'"
          >{{ title }}</p>
          <p
            v-if="subtitle"
            class="mt-0.5"
            :style="overlaySubtitleStyle + ' color: rgba(255,255,255,0.82);'"
          >{{ subtitle }}</p>
        </slot>
      </div>
    </div>

    <!-- Default slot — optional below-photo content (rare; photo IS usually the card) -->
    <slot />
  </component>
</template>

<style scoped>
/*
  ════════════════════════════════════════════════════════════════
  KinPhotoCard — scrim + interactive states

  Scrim is intentionally a flat rgba, NOT a gradient.
  Gradient scrims have unpredictable weak zones on bright or
  complex photos where white text can become illegible
  (5.2 HeroMetricCard Variant C was the canary).
  The flat 0.45 overlay is the locked extraction-phase decision.

  Interactive hover:
  - translateY(-2px) lift + shadow-hover + img zoom
  - prefers-reduced-motion: skip transforms; shadow only
  ════════════════════════════════════════════════════════════════
*/

/* Gradient scrim — transparent top fading to 95% black at bottom.
   Bottom-weighted so text/chips at the bottom are always legible
   regardless of photo subject or brightness. Text also carries a
   text-shadow for belt-and-suspenders legibility on similar-colored photos. */
.kin-photo-card__scrim {
  background: linear-gradient(
    180deg,
    transparent 0%,
    transparent 35%,
    rgba(0, 0, 0, 0.28) 55%,
    rgba(0, 0, 0, 0.70) 82%,
    rgba(0, 0, 0, 0.95) 100%
  );
}

/* Smooth img scale-in on hover */
.kin-photo-card__img {
  transition: transform var(--duration-deliberate) var(--ease-out-soft);
}

/* ── Interactive card ────────────────────────────────────────── */
.kin-photo-card--interactive {
  transition:
    transform   var(--duration-quick) var(--ease-out-soft),
    box-shadow  var(--duration-quick) var(--ease-out-soft);
}

.kin-photo-card--interactive:hover {
  box-shadow: var(--shadow-hover);
}

@media (prefers-reduced-motion: no-preference) {
  .kin-photo-card--interactive:hover {
    transform: translateY(-2px);
  }

  .kin-photo-card--interactive:active {
    transform: translateY(0px);
    box-shadow: var(--shadow-resting);
  }

  .kin-photo-card--interactive:hover .kin-photo-card__img {
    transform: scale(1.03);
  }
}

/* Reduced-motion: keep shadow transition only, no transforms */
@media (prefers-reduced-motion: reduce) {
  .kin-photo-card--interactive {
    transition: box-shadow var(--duration-quick) var(--ease-out-soft);
  }

  .kin-photo-card__img {
    transition: none;
  }
}
</style>
