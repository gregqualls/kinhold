<!--
  KinHeroMetricCard — flagship hero surface for module landing pages.
  @see /design-system/hero-metric-card  (docs/design/COMPONENT_ROADMAP.md §5.2)
  Props: variant, photo, label, value, delta, deltaUp, ctaLabel, ctaHref, ctaTo
  Slots: #cta (override CTA button), #content (override content overlay)
  Emits: cta-click

  Variants:
    iridescent — lavender→mint→peach radial wash. Calm summary hero.
    warm        — peach→coral→amber radial wash. Streak / urgency.
    photo       — edge-to-edge photo + two-layer scrim for legibility.

  Content (label + hero number + delta + CTA) sits bottom-left with the
  CTA right-aligned on md+ screens. Gradient variants are intentionally
  glyph-free — the wash carries the surface, the number carries the data.
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

// Hero number scales by character count so short values ("64") feel huge
// and long ones ("23 days", "6 PM") stay on one line on narrow cards.
const valueFontSize = computed(() => {
  const len = String(props.value).length
  if (len <= 2) return 'clamp(3rem, 16cqw, 8rem)'
  if (len <= 4) return 'clamp(2.5rem, 13cqw, 6.5rem)'
  if (len <= 6) return 'clamp(2rem, 10cqw, 5rem)'
  return 'clamp(1.75rem, 8cqw, 4rem)'
})

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
    class="kin-hmc relative rounded-[28px] overflow-hidden flex flex-col justify-end"
    :class="`kin-hmc--${variant}`"
    :style="{ minHeight }"
  >
    <!-- Photo variant: image + full-coverage scrim, both absolutely positioned behind -->
    <template v-if="variant === 'photo' && photo">
      <img
        :src="photo"
        :alt="photoAlt"
        class="absolute inset-0 w-full h-full object-cover"
        loading="lazy"
      />
      <div class="kin-hmc__scrim absolute inset-0" aria-hidden="true" />
    </template>

    <!-- Content — in normal flow, pushed to bottom of article via justify-end. -->
    <div class="relative p-6 md:p-8 flex flex-col gap-3 md:flex-row md:items-end md:justify-between md:gap-4">
      <slot name="content">
        <div class="space-y-2 min-w-0">
          <p class="kin-hmc__label text-[11px] font-semibold uppercase tracking-widest">{{ label }}</p>
          <p
            class="kin-hmc__value font-semibold leading-none tracking-tight whitespace-nowrap"
            :style="{ fontSize: valueFontSize }"
          >{{ value }}</p>
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

/* Hero number — font size set inline (content-length aware). */
.kin-hmc__value {
  font-family: 'Plus Jakarta Sans', sans-serif;
  letter-spacing: -0.03em;
  color: rgb(var(--ink-primary));
}

.kin-hmc__label {
  color: rgb(var(--ink-tertiary));
}

/* ── IRIDESCENT variant ─────────────────────────────────────────────── */
/* Triple radial wash — two color anchors + accent splash for visual depth.
   Reads as multi-color iridescence on every card width, including narrow mobile. */
.kin-hmc--iridescent {
  background-color: rgb(var(--surface-raised));
  background-image:
    radial-gradient(circle at 15% 20%, rgb(var(--accent-lavender-soft)) 0%, transparent 55%),
    radial-gradient(circle at 95% 85%, rgb(var(--accent-peach-soft)) 0%, transparent 60%),
    radial-gradient(circle at 60% 100%, rgb(var(--accent-mint-soft)) 0%, transparent 55%);
}
.dark .kin-hmc--iridescent {
  background-image:
    radial-gradient(circle at 15% 20%, rgb(var(--accent-lavender-bold) / 0.32) 0%, transparent 55%),
    radial-gradient(circle at 95% 85%, rgb(var(--accent-peach-bold)  / 0.28) 0%, transparent 60%),
    radial-gradient(circle at 60% 100%, rgb(var(--accent-mint-bold)  / 0.22) 0%, transparent 55%);
}

/* ── WARM variant ───────────────────────────────────────────────────── */
.kin-hmc--warm {
  background-color: rgb(var(--surface-raised));
  background-image:
    radial-gradient(circle at 15% 15%, rgb(var(--accent-peach-soft)) 0%, transparent 60%),
    radial-gradient(circle at 90% 90%, rgb(var(--accent-sun-soft))   0%, transparent 60%),
    radial-gradient(circle at 80% 20%, rgb(var(--accent-peach-soft)) 0%, transparent 45%);
}
.dark .kin-hmc--warm {
  background-image:
    radial-gradient(circle at 15% 15%, rgb(var(--accent-peach-bold) / 0.35) 0%, transparent 60%),
    radial-gradient(circle at 90% 90%, rgb(var(--accent-sun-bold)   / 0.28) 0%, transparent 60%),
    radial-gradient(circle at 80% 20%, rgb(var(--accent-peach-bold) / 0.22) 0%, transparent 45%);
}

/* ── PHOTO variant ──────────────────────────────────────────────────── */
/* Full-coverage scrim — uniform darkening so any region can host text.
   Slightly heavier toward the bottom where the content sits. */
.kin-hmc__scrim {
  background: linear-gradient(
    180deg,
    rgba(0, 0, 0, 0.45) 0%,
    rgba(0, 0, 0, 0.55) 50%,
    rgba(0, 0, 0, 0.78) 100%
  );
}
.kin-hmc--photo .kin-hmc__value,
.kin-hmc--photo .kin-hmc__label {
  color: #FFFFFF;
  text-shadow: 0 1px 3px rgba(0, 0, 0, 0.35);
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
