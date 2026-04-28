<!--
  KinGradientCard — iridescent/tinted gradient card with optional embossed glyph.
  @see /design-system/gradient-card  (docs/design/COMPONENT_ROADMAP.md §2.3)
  Props: variant, glyph, padding, as, href, to, interactive
  Slots: default
-->
<script setup>
import { computed } from 'vue'
import { RouterLink } from 'vue-router'

// ── Props ────────────────────────────────────────────────────────────────────
const props = defineProps({
  /**
   * Gradient fill family.
   *   iridescent — cool opal wash (lavender + mint + peach).
   *   warm        — warm opal wash (peach + sun + lavender blush).
   *   lavender    — single-hue radial tint, lavender family.
   *   peach       — single-hue radial tint, peach/apricot family.
   *   mint        — single-hue radial tint, mint/sage family.
   *   sun         — single-hue radial tint, sun/butter family.
   *   cool        — lavender + mint dual radial (for blue/teal contexts).
   */
  variant: {
    type: String,
    default: 'iridescent',
    validator: (v) => ['iridescent', 'warm', 'lavender', 'peach', 'mint', 'sun', 'cool'].includes(v),
  },

  /**
   * Heroicon (or any SVG component) rendered as a watermark glyph in
   * the upper-left at ~30% opacity. Pass the component reference directly,
   * e.g. `:glyph="ShieldCheckIcon"`. If omitted, no glyph is rendered.
   */
  glyph: {
    type: Object, // Component reference
    default: null,
  },

  /** Overlay content padding (bottom-left anchor). sm = p-3, md = p-5, lg = p-7. */
  padding: {
    type: String,
    default: 'md',
    validator: (v) => ['sm', 'md', 'lg'].includes(v),
  },

  /** Root tag name when neither `to` nor `href` is provided. */
  as: {
    type: String,
    default: 'div',
  },

  /** When set, renders an <a> tag (overrides `as`). */
  href: {
    type: String,
    default: null,
  },

  /** When set, renders a <RouterLink> (overrides `href` and `as`). */
  to: {
    type: [String, Object],
    default: null,
  },

  /**
   * Enables hover lift + cursor-pointer.
   * Also auto-enabled when `to` or `href` is set.
   */
  interactive: {
    type: Boolean,
    default: false,
  },
})

// ── Root element resolution ───────────────────────────────────────────────────
const tag = computed(() => {
  if (props.to)   return RouterLink
  if (props.href) return 'a'
  return props.as
})

// ── Extra attrs for native <a> / RouterLink ──────────────────────────────────
const extraAttrs = computed(() => {
  if (props.to)   return { to: props.to }
  if (props.href) return { href: props.href }
  return {}
})

// ── Whether the card should appear interactive ───────────────────────────────
const isInteractive = computed(() => props.interactive || !!props.to || !!props.href)

// ── Padding classes ──────────────────────────────────────────────────────────
const paddingClass = computed(() => ({
  sm: 'p-3',
  md: 'p-5',
  lg: 'p-7',
}[props.padding] ?? 'p-5'))

// ── Variant → CSS class ──────────────────────────────────────────────────────
// Each variant drives scoped CSS that applies the right gradient + glyph color.
const variantClass = computed(() => `kin-gc--${props.variant}`)
</script>

<template>
  <component
    :is="tag"
    v-bind="extraAttrs"
    :class="[
      // ── Base structure ────────────────────────────────────────────
      'kin-gc',
      'rounded-card',
      'relative overflow-hidden',
      // ── Gradient variant ─────────────────────────────────────────
      variantClass,
      // Note: root has no padding; the overlay slot carries it so the
      // glyph (absolute upper-left) and content (absolute bottom-left)
      // can occupy the full card area.
      // ── Interactive lift ─────────────────────────────────────────
      isInteractive && 'kin-gc--interactive cursor-pointer',
      isInteractive && 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-accent-lavender-bold',
    ]"
  >
    <!-- ── Glyph: top-left decorative watermark ─────────────────────────────── -->
    <!--
      Rendered as a positioned absolute div in the upper-left quadrant so the
      glyph "peeks" artistically — not clipped, not centered.  Width is a %
      of the card (28% on hero tiles, 34% on compact tiles — consumers can
      override via `class` if needed, but the default works well for both).
      The kin-gc--glyph scoped class picks up the right color per variant.
    -->
    <div
      v-if="glyph"
      class="kin-gc--glyph absolute pointer-events-none"
      aria-hidden="true"
    >
      <component :is="glyph" />
    </div>

    <!-- ── Default slot: content anchored bottom-left ───────────────────────── -->
    <!--
      Canonical GradientCard composition: glyph upper-left, content bottom-left.
      The diagonal is the design — no "natural flow" mode. Cards get their
      height from an aspect-ratio class or min-height set on the root
      (via class/style pass-through), then content sits at the bottom.
    -->
    <div
      :class="['absolute inset-x-0 bottom-0 z-10', paddingClass]"
    >
      <slot></slot>
    </div>
  </component>
</template>

<style scoped>
/*
  ════════════════════════════════════════════════════════════════════
  KinGradientCard — scoped gradient fills, glyph colors, interactions

  Design principles:
  • Gradients are applied as background-image. background-color is set
    separately to the surface-raised token so the transparent gradient
    wells have a solid base to sit over (without this, they look "blown
    out" — see tokens.css §4 IMPORTANT note).
  • Every accent-family gradient is a single radial well anchored at
    ~20% 25% (upper-left quadrant) that fades to transparent. This gives
    the canonical "light source upper-left / content lower-left" diagonal.
  • Light + dark variants are expressed via `.dark .kin-gc--*` selectors.
    Vue scopes them as `.dark .kin-gc--*[data-v-xxxx]` — specificity 0,2,1
    which correctly beats the base 0,1,1 rule.
  • Gradient tokens from tokens.css are used for iridescent/warm.
    Accent-family gradients (lavender/peach/mint/sun/cool) are composed
    inline from --accent-*-soft tokens since there is no dedicated
    radial gradient token for those families (we must not add to tokens.css).
  • Glyph color is picked up per-variant so the embossed watermark always
    reads as "this card's color family" without being a separate prop.
  ════════════════════════════════════════════════════════════════════
*/

/* ── Base kin-gc class ───────────────────────────────────────────────────── */
.kin-gc {
  background-color: rgb(var(--surface-raised));
  box-shadow: var(--shadow-resting);
}

/* ── Glyph: small, solid, top-right category marker ──────────────────────── */
/*
  Not a watermark. A deliberate small icon in the top-right corner,
  sized 24px and at full opacity in its accent-bold color. Reads as
  "this card's category" without competing with the content.
  Previously was a 30%-opacity decorative wash — toned down so the
  component sits next to FlatCard/PhotoCard as a quiet peer, not a
  loud cousin.
*/
.kin-gc--glyph {
  width: 24px;
  height: 24px;
  right: 16px;
  top: 16px;
  opacity: 1;
  color: rgb(var(--ink-tertiary));
}
.kin-gc--glyph > * {
  width: 100%;
  height: 100%;
}

/* ── IRIDESCENT (default) ───────────────────────────────────────────────── */
/* Barely-there multi-hue wash — the quiet, editorial default. */
.kin-gc--iridescent {
  background-image: var(--gradient-iridescent-subtle);
}
.kin-gc--iridescent .kin-gc--glyph {
  color: rgb(var(--accent-lavender-bold));
}

/* ── WARM ────────────────────────────────────────────────────────────────── */
.kin-gc--warm {
  background-image: var(--gradient-iridescent-warm);
}
.kin-gc--warm .kin-gc--glyph {
  color: rgb(var(--accent-peach-bold));
}

/* ── Accent variants ─────────────────────────────────────────────────────── */
/* All accents: quiet tint anchored upper-left, fading to the base surface.
   Light: -soft token at 0.35 opacity (was 0.85 — too loud alongside FlatCard/PhotoCard).
   Dark:  -bold token at 0.18 opacity (was 0.38 — same reason).
   Category identity is now carried by the small top-right glyph + content
   typography; the gradient is a subtle tint, not a "look at me" wash. */
.kin-gc--lavender {
  background-image: radial-gradient(
    ellipse 90% 80% at 20% 25%,
    rgb(var(--accent-lavender-soft) / 0.35) 0%,
    transparent 65%
  );
}
.dark .kin-gc--lavender {
  background-image: radial-gradient(
    ellipse 90% 80% at 20% 25%,
    rgb(var(--accent-lavender-bold) / 0.18) 0%,
    transparent 65%
  );
}
.kin-gc--lavender .kin-gc--glyph { color: rgb(var(--accent-lavender-bold)); }

.kin-gc--peach {
  background-image: radial-gradient(
    ellipse 90% 80% at 20% 25%,
    rgb(var(--accent-peach-soft) / 0.35) 0%,
    transparent 65%
  );
}
.dark .kin-gc--peach {
  background-image: radial-gradient(
    ellipse 90% 80% at 20% 25%,
    rgb(var(--accent-peach-bold) / 0.18) 0%,
    transparent 65%
  );
}
.kin-gc--peach .kin-gc--glyph { color: rgb(var(--accent-peach-bold)); }

.kin-gc--mint {
  background-image: radial-gradient(
    ellipse 90% 80% at 20% 25%,
    rgb(var(--accent-mint-soft) / 0.35) 0%,
    transparent 65%
  );
}
.dark .kin-gc--mint {
  background-image: radial-gradient(
    ellipse 90% 80% at 20% 25%,
    rgb(var(--accent-mint-bold) / 0.18) 0%,
    transparent 65%
  );
}
.kin-gc--mint .kin-gc--glyph { color: rgb(var(--accent-mint-bold)); }

.kin-gc--sun {
  background-image: radial-gradient(
    ellipse 90% 80% at 20% 25%,
    rgb(var(--accent-sun-soft) / 0.35) 0%,
    transparent 65%
  );
}
.dark .kin-gc--sun {
  background-image: radial-gradient(
    ellipse 90% 80% at 20% 25%,
    rgb(var(--accent-sun-bold) / 0.18) 0%,
    transparent 65%
  );
}
.kin-gc--sun .kin-gc--glyph { color: rgb(var(--accent-sun-bold)); }

/* ── COOL ────────────────────────────────────────────────────────────────── */
/* Dual-hue: lavender upper-left + mint lower-right, both at low intensity. */
.kin-gc--cool {
  background-image:
    radial-gradient(
      ellipse 80% 70% at 18% 20%,
      rgb(var(--accent-lavender-soft) / 0.35) 0%,
      transparent 65%
    ),
    radial-gradient(
      ellipse 70% 60% at 82% 80%,
      rgb(var(--accent-mint-soft) / 0.35) 0%,
      transparent 65%
    );
}
.dark .kin-gc--cool {
  background-image:
    radial-gradient(
      ellipse 80% 70% at 18% 20%,
      rgb(var(--accent-lavender-bold) / 0.18) 0%,
      transparent 65%
    ),
    radial-gradient(
      ellipse 70% 60% at 82% 80%,
      rgb(var(--accent-mint-bold) / 0.18) 0%,
      transparent 65%
    );
}
.kin-gc--cool .kin-gc--glyph { color: rgb(var(--accent-lavender-bold)); }

/* ══════════════════════════════════════════════════════════════════════════
   DARK MODE
   Same structure; --accent-* and --gradient-* tokens auto-swap to their
   dark values via .dark on a parent element. The only manual override is
   the glyph opacity — dark bold colors at 0.30 can be too saturated, so
   we keep them at 0.28 to match the iridescent family.
   ══════════════════════════════════════════════════════════════════════════ */

/*
  In dark mode: --surface-raised is #1C1B19 (deep warm charcoal). The
  transparent-stop of the radial gradients sits over that base, giving the
  "deep gemstone glow" look described in tokens.css §4.
  No explicit .dark .kin-gc--* overrides are needed — the CSS custom
  properties inherited from the .dark parent class handle everything.
  Exception: the iridescent gradient vars already define their own dark
  values in tokens.css so those swap automatically too.
*/

/* ── INTERACTIVE lift state ─────────────────────────────────────────────── */
.kin-gc--interactive {
  transition:
    transform   var(--duration-quick) var(--ease-out-soft),
    box-shadow  var(--duration-quick) var(--ease-out-soft);
}

.kin-gc--interactive:hover {
  box-shadow: var(--shadow-hover);
}

@media (prefers-reduced-motion: no-preference) {
  .kin-gc--interactive:hover {
    transform: translateY(-2px);
  }

  .kin-gc--interactive:active {
    transform: translateY(0px);
    box-shadow: var(--shadow-resting);
  }
}

@media (prefers-reduced-motion: reduce) {
  .kin-gc--interactive {
    transition: box-shadow var(--duration-quick) var(--ease-out-soft);
  }
}
</style>
