<!--
  KinAvatar — circle avatar with photo / initials / icon fallback, optional ring, arc progress, presence dot.
  @see /design-system/avatar  (docs/design/COMPONENT_ROADMAP.md §1.4)
  Props: src, name, icon, color, size, ring, progress, presence
-->
<script setup>
import { computed, ref } from 'vue'

// ── Props ────────────────────────────────────────────────────────────────────
const props = defineProps({
  /** Photo URL. If set and loads successfully, takes priority over everything. */
  src: { type: String, default: null },

  /** Full name — drives initials (up to 2 chars) AND img alt text. */
  name: { type: String, default: null },

  /** Any icon component (Heroicon, Phosphor, etc.) — used when no src and no name. */
  icon: { type: [Object, Function], default: null },

  /** Pastel accent family. Controls bg color for fallback and ring/arc color. */
  color: {
    type: String,
    default: 'lavender',
    validator: (v) => ['lavender', 'peach', 'mint', 'sun', 'gold', 'slate'].includes(v),
  },

  /** Circle diameter. */
  size: {
    type: String,
    default: 'md',
    validator: (v) => ['xs', 'sm', 'md', 'lg', 'xl'].includes(v),
  },

  /**
   * Thin 2px brand-color ring around the avatar, with a small gap.
   * Rendered via double box-shadow: inner gap = surface-app color, outer = accent-bold.
   */
  ring: { type: Boolean, default: false },

  /**
   * Arc progress overlay (0–1 or 0–100). null/undefined = no arc.
   * At 100% the arc becomes a solid closed ring (stroke-linecap: butt).
   */
  progress: { type: Number, default: null },

  /** Presence dot in bottom-right corner. */
  presence: {
    type: String,
    default: null,
    validator: (v) => v === null || ['online', 'busy', 'away'].includes(v),
  },
})

// ── Image error fallback ─────────────────────────────────────────────────────
// When the img fails to load we flip this flag and drop to initials/icon.
const imgFailed = ref(false)
function onImgError() { imgFailed.value = true }

// ── Derived: which content mode to render ───────────────────────────────────
const showPhoto    = computed(() => !!props.src && !imgFailed.value)
const showInitials = computed(() => !showPhoto.value && !!props.name)
const showIcon     = computed(() => !showPhoto.value && !showInitials.value && !!props.icon)
// fallback: question mark — handled inline in template

// ── Initials ─────────────────────────────────────────────────────────────────
const initials = computed(() => {
  if (!props.name) return ''
  const parts = props.name.trim().split(/\s+/)
  if (parts.length === 1) return parts[0].slice(0, 2).toUpperCase()
  return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase()
})

// ── Size config ──────────────────────────────────────────────────────────────
// px = visual diameter of the circle
// textSize = initials font size (px string for inline style)
// arcPad = extra px on each side for the arc stroke (arc outer diameter = px + arcPad*2)
// arcStrokeVB = stroke-width in SVG viewBox units (viewBox is 100x100)
// presencePx = presence dot diameter
// presenceBorder = presence dot border thickness
const SIZE_MAP = {
  xs: { px: 24, textSize: '10px', arcPad: 4, arcStrokeVB: 4.5, presencePx: 6,  presenceBorderPx: 1.5 },
  sm: { px: 32, textSize: '11px', arcPad: 4, arcStrokeVB: 4,   presencePx: 8,  presenceBorderPx: 1.5 },
  md: { px: 40, textSize: '13px', arcPad: 4, arcStrokeVB: 4,   presencePx: 10, presenceBorderPx: 2   },
  lg: { px: 56, textSize: '17px', arcPad: 4, arcStrokeVB: 3.5, presencePx: 12, presenceBorderPx: 2   },
  xl: { px: 80, textSize: '22px', arcPad: 5, arcStrokeVB: 3,   presencePx: 16, presenceBorderPx: 2.5 },
}

const sizeConfig = computed(() => SIZE_MAP[props.size] ?? SIZE_MAP.md)

// ── Outer wrapper dimensions ─────────────────────────────────────────────────
// When arc is active: wrapper = px + arcPad*2 to hold the SVG ring.
// When no arc: wrapper = px exactly.
const wrapperPx = computed(() =>
  hasArc.value
    ? sizeConfig.value.px + sizeConfig.value.arcPad * 2
    : sizeConfig.value.px
)

// ── Progress normalization ───────────────────────────────────────────────────
// Accept 0–1 or 0–100; always work in 0–1 internally.
const normalizedProgress = computed(() => {
  if (props.progress === null || props.progress === undefined) return null
  return props.progress > 1 ? props.progress / 100 : props.progress
})

const hasArc = computed(() => normalizedProgress.value !== null)

// ── SVG arc math (viewBox 100×100) ──────────────────────────────────────────
// r=46 leaves just enough room for the stroke without clipping the viewBox edge.
const ARC_R = 46
const ARC_CIRC = 2 * Math.PI * ARC_R  // ≈ 289.03

const arcDashoffset = computed(() => {
  if (!hasArc.value) return ARC_CIRC
  return ARC_CIRC * (1 - normalizedProgress.value)
})

const arcLinecap = computed(() =>
  normalizedProgress.value >= 1 ? 'butt' : 'round'
)

// ── Color → Tailwind utility maps ────────────────────────────────────────────
// 'gold' and 'slate' are extras beyond the 4 standard accent families.
// They don't have accent-* tokens, so we use literal Tailwind utilities here.
const BG_SOFT = {
  lavender: 'bg-accent-lavender-soft',
  peach:    'bg-accent-peach-soft',
  mint:     'bg-accent-mint-soft',
  sun:      'bg-accent-sun-soft',
  gold:     'bg-brand-gold/20',
  slate:    'bg-ink-tertiary/20',
}

const TEXT_BOLD = {
  lavender: 'text-accent-lavender-bold',
  peach:    'text-accent-peach-bold',
  mint:     'text-accent-mint-bold',
  sun:      'text-accent-sun-bold',
  gold:     'text-brand-gold',
  slate:    'text-ink-secondary',
}

// CSS var names for the ring box-shadow (we need raw CSS values, not classes,
// because box-shadow requires two layered values — a gap color and ring color).
// The ring gap color references surface-app; the ring itself references the bold token.
// We use CSS custom properties directly so light/dark swap automatically.
const RING_BOLD_VAR = {
  lavender: '--accent-lavender-bold',
  peach:    '--accent-peach-bold',
  mint:     '--accent-mint-bold',
  sun:      '--accent-sun-bold',
  gold:     '--brand-gold',
  slate:    '--ink-tertiary',
}

const bgSoftClass  = computed(() => BG_SOFT[props.color]  ?? BG_SOFT.lavender)
const textBoldClass = computed(() => TEXT_BOLD[props.color] ?? TEXT_BOLD.lavender)

// Ring box-shadow: 2px gap (surface-app color) + 2px ring (accent-bold color)
const ringStyle = computed(() => {
  if (!props.ring) return {}
  const varName = RING_BOLD_VAR[props.color] ?? RING_BOLD_VAR.lavender
  return {
    boxShadow: `0 0 0 2px rgb(var(--surface-app)), 0 0 0 4px rgb(var(${varName}))`,
  }
})

// ── Presence dot color ────────────────────────────────────────────────────────
const PRESENCE_CLASS = {
  online: 'bg-status-success',
  busy:   'bg-status-failed',
  away:   'bg-status-paused',
}
const presenceClass = computed(() =>
  props.presence ? (PRESENCE_CLASS[props.presence] ?? 'bg-status-paused') : null
)
</script>

<template>
  <!--
    Root wrapper: always `position: relative` to anchor the presence dot.
    Size is set via inline style (exact px) rather than Tailwind w-*/h-* classes
    because the px values are dynamic (arc adds padding).
    flex-shrink-0 prevents the avatar from squashing in flex containers.
  -->
  <div
    class="relative inline-flex flex-shrink-0 items-center justify-center"
    :style="{ width: wrapperPx + 'px', height: wrapperPx + 'px' }"
  >

    <!-- ── Photo / initials / icon circle ──────────────────────────────────── -->
    <!--
      When arc is active the circle is inset by arcPad from the wrapper edge.
      `absolute` + `inset-[arcPad]` achieves this without an extra wrapper div.
      When no arc the circle fills the wrapper exactly (inset: 0).
    -->
    <div
      :class="[
        'rounded-full overflow-hidden',
        'transition-transform duration-quick ease-out-soft',
        'hover:scale-[1.04]',
        // Background only for non-photo modes (photo clips to overflow-hidden)
        !showPhoto && bgSoftClass,
        // Flex center for initials and icon
        !showPhoto && 'flex items-center justify-center',
      ]"
      :style="{
        position: hasArc ? 'absolute' : 'relative',
        inset: hasArc ? sizeConfig.arcPad + 'px' : undefined,
        width: hasArc ? undefined : sizeConfig.px + 'px',
        height: hasArc ? undefined : sizeConfig.px + 'px',
        ...(hasArc ? {} : ringStyle),
      }"
    >
      <!-- Photo -->
      <img
        v-if="showPhoto"
        :src="src"
        :alt="name || 'Avatar'"
        class="w-full h-full object-cover"
        @error="onImgError"
      />

      <!-- Initials -->
      <span
        v-else-if="showInitials"
        :class="['font-semibold select-none', textBoldClass]"
        :style="{ fontSize: sizeConfig.textSize }"
        aria-hidden="true"
      >{{ initials }}</span>

      <!-- Icon component -->
      <component
        v-else-if="showIcon"
        :is="icon"
        :class="['shrink-0', textBoldClass]"
        :style="{ width: Math.round(sizeConfig.px * 0.55) + 'px', height: Math.round(sizeConfig.px * 0.55) + 'px' }"
        aria-hidden="true"
      />

      <!-- Ultimate fallback: question mark -->
      <span
        v-else
        :class="['font-semibold select-none', textBoldClass]"
        :style="{ fontSize: sizeConfig.textSize }"
        aria-hidden="true"
      >?</span>
    </div>

    <!-- ── Arc overlay (SVG) ───────────────────────────────────────────────── -->
    <!--
      The SVG fills the entire wrapper (absolute inset-0).
      viewBox is 100×100; circle at cx/cy=50, r=46.
      Circumference ≈ 289.03; dashoffset drives the fill amount.
      Rotate -90deg so the arc starts at the top (12 o'clock).
      Arc stroke color uses currentColor → set text-accent-*-bold on the SVG.
      Ring style applied here when arc is active (can't apply to inner div in that case).
    -->
    <svg
      v-if="hasArc"
      class="absolute inset-0 w-full h-full"
      :class="textBoldClass"
      viewBox="0 0 100 100"
      style="transform: rotate(-90deg)"
      aria-hidden="true"
    >
      <!-- Track ring (very subtle) -->
      <circle
        cx="50" cy="50" :r="ARC_R"
        fill="none"
        class="text-ink-primary/[0.07]"
        stroke="currentColor"
        stroke-width="4"
      />
      <!-- Progress arc -->
      <circle
        cx="50" cy="50" :r="ARC_R"
        fill="none"
        stroke="currentColor"
        stroke-width="4"
        :stroke-dasharray="ARC_CIRC"
        :stroke-dashoffset="arcDashoffset"
        :stroke-linecap="arcLinecap"
        style="transition: stroke-dashoffset var(--duration-deliberate) var(--ease-out-soft)"
      />
    </svg>

    <!-- ── Ring overlay when arc is active ────────────────────────────────── -->
    <!--
      When arc is active, ringStyle can't be on the inner div (it would appear
      behind the arc stroke). We apply it as an absolutely-positioned inset circle
      instead, drawn as an outline ring using box-shadow.
    -->
    <div
      v-if="ring && hasArc"
      class="absolute rounded-full pointer-events-none"
      :style="{
        inset: sizeConfig.arcPad + 'px',
        ...ringStyle,
      }"
    />

    <!-- ── Presence dot ────────────────────────────────────────────────────── -->
    <!--
      Positioned at bottom-right corner of the WRAPPER.
      Border uses surface-app color for the gap between dot and avatar edge.
      The dot sits on top of the arc (z-index: 10).
    -->
    <span
      v-if="presence"
      class="absolute rounded-full block"
      :class="presenceClass"
      :style="{
        width:  sizeConfig.presencePx + 'px',
        height: sizeConfig.presencePx + 'px',
        border: sizeConfig.presenceBorderPx + 'px solid rgb(var(--surface-app))',
        bottom: hasArc ? (sizeConfig.arcPad - 1) + 'px' : '-1px',
        right:  hasArc ? (sizeConfig.arcPad - 1) + 'px' : '-1px',
        zIndex: 10,
      }"
    />
  </div>
</template>
