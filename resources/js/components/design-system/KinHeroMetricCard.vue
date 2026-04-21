<!--
  KinHeroMetricCard — flagship hero surface for module landing pages.
  @see /design-system/hero-metric-card  (docs/design/COMPONENT_ROADMAP.md §5.2)
  Props: variant, glyph, photo, label, value, delta, deltaUp, ctaLabel, ctaHref, ctaTo
  Slots: #cta (override CTA button), #content (override content overlay)
  Emits: cta-click

  Variants:
    iridescent — lavender→mint→peach radial wash. Calm summary hero.
    warm        — peach→coral→amber radial wash. Streak / urgency.
    photo       — edge-to-edge photo + two-layer scrim for legibility.

  Gradient variants use a glyph watermark (upper-left, ~10% opacity).
  Photo variant ignores the glyph.
  Content (label + hero number + delta + CTA) sits bottom-left with the
  CTA right-aligned on md+ screens.
-->
<script setup>
import { computed } from 'vue'
import { RouterLink } from 'vue-router'
import { ArrowUpRightIcon, ArrowDownRightIcon, ArrowRightIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  /** Visual variant. */
  variant: {
    type: String,
    default: 'iridescent',
    validator: (v) => ['iridescent', 'warm', 'photo'].includes(v),
  },
  /** Glyph component for gradient variants (ignored when variant="photo"). */
  glyph: { type: [Object, Function], default: null },
  /** Photo URL (required when variant="photo"). */
  photo: { type: String, default: null },
  /** Alt text for photo variant. */
  photoAlt: { type: String, default: '' },
  /** Kicker label above the hero number. */
  label: { type: String, required: true },
  /** Hero number (string or number). */
  value: { type: [String, Number], required: true },
  /** Delta chip text. */
  delta: { type: String, default: '' },
  /** Delta direction (up = success, down = failed). */
  deltaUp: { type: Boolean, default: true },
  /** CTA label — omit to hide the default CTA button. */
  ctaLabel: { type: String, default: '' },
  /** CTA href (renders <a>). */
  ctaHref: { type: String, default: null },
  /** CTA to (RouterLink). */
  ctaTo: { type: [String, Object], default: null },
  /** Minimum height of the card — useful for content-light cases. */
  minHeight: { type: String, default: '200px' },
})

const emit = defineEmits(['cta-click'])

const ctaTag = computed(() => {
  if (props.ctaTo) return RouterLink
  if (props.ctaHref) return 'a'
  return 'button'
})
const ctaAttrs = computed(() => {
  if (props.ctaTo) return { to: props.ctaTo }
  if (props.ctaHref) return { href: props.ctaHref }
  return { type: 'button' }
})
</script>

<template>
  <article
    class="kin-hmc relative rounded-[28px] overflow-hidden"
    :class="`kin-hmc--${variant}`"
    :style="{ minHeight }"
  >
    <!-- Photo variant: image + two-layer scrim -->
    <template v-if="variant === 'photo' && photo">
      <img
        :src="photo"
        :alt="photoAlt"
        class="absolute inset-0 w-full h-full object-cover"
        loading="lazy"
      />
      <div class="kin-hmc__scrim absolute inset-0" aria-hidden="true" />
    </template>

    <!-- Gradient variants: glyph watermark top-left (if provided) -->
    <component
      v-if="variant !== 'photo' && glyph"
      :is="glyph"
      class="kin-hmc__glyph absolute pointer-events-none"
      aria-hidden="true"
    />

    <!-- Content overlay — bottom-left with optional right-aligned CTA -->
    <div class="absolute inset-x-0 bottom-0 p-6 md:p-8 flex items-end justify-between gap-4">
      <slot name="content">
        <div class="space-y-2 min-w-0">
          <p class="kin-hmc__label text-[11px] font-semibold uppercase tracking-widest">{{ label }}</p>
          <p class="kin-hmc__value font-semibold leading-none tracking-tight">{{ value }}</p>
          <div v-if="delta" class="flex items-center gap-2 pt-1">
            <span
              class="kin-hmc__delta inline-flex items-center gap-1 h-6 px-2.5 rounded-full text-[11px] font-medium"
              :class="deltaUp ? 'kin-hmc__delta--up' : 'kin-hmc__delta--down'"
            >
              <ArrowUpRightIcon   v-if="deltaUp"  class="w-3 h-3" />
              <ArrowDownRightIcon v-else           class="w-3 h-3" />
              {{ delta }}
            </span>
          </div>
        </div>
      </slot>

      <slot name="cta">
        <component
          v-if="ctaLabel"
          :is="ctaTag"
          v-bind="ctaAttrs"
          class="kin-hmc__cta shrink-0 inline-flex items-center gap-1.5 h-10 px-4 rounded-full text-[13px] font-medium"
          @click="$emit('cta-click', $event)"
        >
          {{ ctaLabel }}
          <ArrowRightIcon class="w-4 h-4" />
        </component>
      </slot>
    </div>
  </article>
</template>

<style scoped>
.kin-hmc {
  box-shadow: var(--shadow-hover);
  container-type: inline-size;
}

/* Hero number uses container-query units so it scales with the card. */
.kin-hmc__value {
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: clamp(3rem, 12cqw, 8rem);
  letter-spacing: -0.03em;
  color: rgb(var(--ink-primary));
}

.kin-hmc__label {
  color: rgb(var(--ink-tertiary));
}

/* ── IRIDESCENT variant ─────────────────────────────────────────────── */
.kin-hmc--iridescent {
  background-color: rgb(var(--surface-raised));
  background-image: radial-gradient(
    circle at 15% 15%,
    rgb(var(--accent-lavender-soft)) 0%,
    rgb(var(--accent-mint-soft)) 40%,
    rgb(var(--accent-peach-soft)) 85%,
    rgb(var(--surface-raised)) 100%
  );
}
.dark .kin-hmc--iridescent {
  background-image: radial-gradient(
    circle at 15% 15%,
    rgb(var(--accent-lavender-bold) / 0.25) 0%,
    rgb(var(--accent-mint-bold) / 0.18) 45%,
    rgb(var(--accent-peach-bold) / 0.18) 85%,
    rgb(var(--surface-raised)) 100%
  );
}

/* ── WARM variant ───────────────────────────────────────────────────── */
.kin-hmc--warm {
  background-color: rgb(var(--surface-raised));
  background-image: radial-gradient(
    circle at 15% 15%,
    rgb(var(--accent-peach-soft)) 0%,
    rgb(var(--accent-sun-soft)) 45%,
    rgb(var(--surface-raised)) 100%
  );
}
.dark .kin-hmc--warm {
  background-image: radial-gradient(
    circle at 15% 15%,
    rgb(var(--accent-peach-bold) / 0.30) 0%,
    rgb(var(--accent-sun-bold) / 0.22) 50%,
    rgb(var(--surface-raised)) 100%
  );
}

/* ── PHOTO variant ──────────────────────────────────────────────────── */
/* Two-layer scrim for guaranteed legibility — bottom half darkens to 0.70. */
.kin-hmc__scrim {
  background:
    linear-gradient(180deg, transparent 0%, transparent 30%, rgba(0, 0, 0, 0.25) 55%, rgba(0, 0, 0, 0.70) 85%, rgba(0, 0, 0, 0.92) 100%);
}
.kin-hmc--photo .kin-hmc__value,
.kin-hmc--photo .kin-hmc__label {
  color: #FFFFFF;
  text-shadow: 0 1px 3px rgba(0, 0, 0, 0.35);
}

/* ── Glyph watermark ────────────────────────────────────────────────── */
.kin-hmc__glyph {
  width: 120px;
  height: 120px;
  left: 24px;
  top: 24px;
  opacity: 0.10;
  color: rgb(var(--accent-lavender-bold));
}
.kin-hmc--warm .kin-hmc__glyph {
  color: rgb(var(--accent-peach-bold));
  opacity: 0.14;
}

/* ── Delta chip ─────────────────────────────────────────────────────── */
.kin-hmc__delta--up {
  background: rgb(var(--status-success) / 0.15);
  color: rgb(var(--status-success));
}
.kin-hmc__delta--down {
  background: rgb(var(--status-failed) / 0.15);
  color: rgb(var(--status-failed));
}
/* On photo variant, delta gets a frosted treatment for legibility */
.kin-hmc--photo .kin-hmc__delta--up,
.kin-hmc--photo .kin-hmc__delta--down {
  background: rgba(255, 255, 255, 0.18);
  color: #FFFFFF;
  backdrop-filter: blur(8px);
}

/* ── CTA button ─────────────────────────────────────────────────────── */
.kin-hmc__cta {
  background: rgb(var(--ink-primary));
  color: rgb(var(--ink-inverse));
  border: none;
  cursor: pointer;
  text-decoration: none;
  transition: transform 150ms, box-shadow 150ms;
  box-shadow: var(--shadow-resting);
}
.kin-hmc__cta:hover {
  box-shadow: var(--shadow-hover);
}
@media (prefers-reduced-motion: no-preference) {
  .kin-hmc__cta:hover { transform: translateY(-1px); }
}

.kin-hmc--photo .kin-hmc__cta {
  background: rgba(255, 255, 255, 0.92);
  color: rgb(var(--ink-primary));
  backdrop-filter: blur(8px);
}
</style>
