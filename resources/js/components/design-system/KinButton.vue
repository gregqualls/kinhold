<!--
  KinButton — lifted pill button. Primary / secondary / ghost / danger / accent.
  @see /design-system/button  (docs/design/COMPONENT_ROADMAP.md §1.1)
  Props: variant, size, iconOnly, loading, disabled, type, href, to
  Slots: default, #leading, #trailing
-->
<script setup>
import { computed, getCurrentInstance } from 'vue'
import { RouterLink } from 'vue-router'
import { ArrowPathIcon } from '@heroicons/vue/24/outline'

// ── Props ────────────────────────────────────────────────────────────────────
const props = defineProps({
  variant:  { type: String,           default: 'primary' },   // primary | secondary | ghost | danger | accent
  size:     { type: String,           default: 'md'      },   // sm | md | lg
  iconOnly: { type: Boolean,          default: false     },
  loading:  { type: Boolean,          default: false     },
  disabled: { type: Boolean,          default: false     },
  type:     { type: String,           default: 'button'  },   // button | submit | reset
  href:     { type: String,           default: null      },
  to:       { type: [String, Object], default: null      },
})

// ── Emit ─────────────────────────────────────────────────────────────────────
// No custom emits — native events pass through $attrs automatically.

// ── Derived state ────────────────────────────────────────────────────────────
const isDisabled = computed(() => props.disabled || props.loading)

// Warn in dev if iconOnly but no aria-label provided
if (import.meta.env.DEV) {
  const instance = getCurrentInstance()
  if (props.iconOnly) {
    // Schedule a microtask so attrs are resolved before we check
    Promise.resolve().then(() => {
      const attrs = instance?.proxy?.$attrs ?? {}
      if (!attrs['aria-label'] && !attrs['aria-labelledby']) {
        console.warn(
          '[KinButton] iconOnly=true but no aria-label or aria-labelledby was provided. ' +
          'Screen-reader users will have no accessible name for this button.'
        )
      }
    })
  }
}

// ── Root element resolution ───────────────────────────────────────────────────
const tag = computed(() => {
  if (props.to)   return RouterLink
  if (props.href) return 'a'
  return 'button'
})

// Extra attrs that only apply to <button>
const nativeButtonAttrs = computed(() => {
  if (tag.value !== 'button') return {}
  return {
    type: props.type,
    disabled: isDisabled.value || undefined,
  }
})

// ── CSS class helpers ─────────────────────────────────────────────────────────
// Size tokens ─ height / padding / text
const sizeClasses = computed(() => {
  if (props.iconOnly) {
    return {
      sm: 'w-8  h-8  text-body-sm',
      md: 'w-10 h-10 text-body-sm',
      lg: 'w-12 h-12 text-body-sm',
    }[props.size] ?? 'w-10 h-10 text-body-sm'
  }
  return {
    sm: 'h-8  px-3 text-body-sm',
    md: 'h-10 px-5 text-body-sm',
    lg: 'h-12 px-7 text-body   font-semibold',
  }[props.size] ?? 'h-10 px-5 text-body-sm'
})
</script>

<template>
  <component
    :is="tag"
    v-bind="{ ...nativeButtonAttrs, ...(to ? { to } : {}), ...(href && !to ? { href } : {}) }"
    :aria-busy="loading || undefined"
    :class="[
      // ── base layout ──────────────────────────────────────────────
      'kin-btn',
      // ── variant class (drives scoped CSS gradient + color) ───────
      'kin-btn--' + variant,
      // ── size classes (height / padding / text) ───────────────────
      sizeClasses,
      // ── focus ring ───────────────────────────────────────────────
      'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-accent-lavender-bold',
      // ── disabled state ───────────────────────────────────────────
      isDisabled ? 'opacity-40 cursor-not-allowed pointer-events-none' : 'cursor-pointer',
      // ── loading cursor override ──────────────────────────────────
      loading && !disabled ? 'cursor-wait' : '',
    ]"
  >
    <!-- Leading slot — replaced by spinner when loading -->
    <template v-if="!iconOnly">
      <span v-if="loading" class="flex-shrink-0">
        <ArrowPathIcon class="w-4 h-4 animate-spin" aria-hidden="true" />
      </span>
      <slot v-else name="leading"></slot>
    </template>

    <!-- Default slot -->
    <slot></slot>

    <!-- Trailing slot — hidden when loading or iconOnly -->
    <slot v-if="!iconOnly && !loading" name="trailing"></slot>
  </component>
</template>

<style scoped>
/*
  ═══════════════════════════════════════════════════════════════════
  KinButton — scoped styles for gradient fills and interactive states

  Why scoped CSS instead of Tailwind utilities here:
  • background-image gradients with interactive :hover/:active
    transitions can't be expressed as Tailwind utilities.
  • :active transform + inset shadow combo needs a single rule.
  • Dark-mode gradient stops use `.dark .kin-btn--*` inside scoped
    CSS. Vue compiles this to `.dark .kin-btn--*[data-v-xxxx]` which
    has specificity (0,2,1) vs the base (0,2,0) — dark wins correctly.

  Hex values are allowed ONLY in this block because tokens.css does
  not define per-button-variant gradient stop tokens. Everything
  else (shadow, radius, duration, text color) defers to token
  utilities on the template.
  ═══════════════════════════════════════════════════════════════════
*/

/* ── Base kin-btn class ───────────────────────────────────────────── */
/*
  :where() wraps the selector with 0 specificity so consumer-side
  Tailwind utilities like `hidden`, `md:flex`, etc. can override
  `display` without needing the `!important` modifier. Without this,
  Vue's scoped-CSS data attribute would push specificity above any
  utility class and consumers couldn't hide the button responsively.
*/
:where(.kin-btn) {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  border-radius: var(--radius-pill);
  font-weight: 500;
  user-select: none;
  transition:
    transform var(--duration-quick) var(--ease-out-soft),
    box-shadow var(--duration-quick) var(--ease-out-soft),
    background-image var(--duration-quick) var(--ease-out-soft),
    background-color var(--duration-quick) var(--ease-out-soft);
}

/* ── Lifted hover/active transforms (all gradient variants) ────────── */
/* Reduced-motion: skip transform; shadow changes stay. */
@media (prefers-reduced-motion: no-preference) {
  .kin-btn--primary:not(:disabled):not([aria-busy="true"]):hover,
  .kin-btn--danger:not(:disabled):not([aria-busy="true"]):hover,
  .kin-btn--accent:not(:disabled):not([aria-busy="true"]):hover,
  .kin-btn--secondary:not(:disabled):not([aria-busy="true"]):hover {
    transform: translateY(-3px);
  }
  .kin-btn--primary:not(:disabled):not([aria-busy="true"]):active,
  .kin-btn--danger:not(:disabled):not([aria-busy="true"]):active,
  .kin-btn--accent:not(:disabled):not([aria-busy="true"]):active,
  .kin-btn--secondary:not(:disabled):not([aria-busy="true"]):active {
    transform: translateY(3px);
  }
}

/* Active inset shadow — applies regardless of motion preference */
.kin-btn--primary:not(:disabled):not([aria-busy="true"]):active,
.kin-btn--danger:not(:disabled):not([aria-busy="true"]):active,
.kin-btn--accent:not(:disabled):not([aria-busy="true"]):active,
.kin-btn--secondary:not(:disabled):not([aria-busy="true"]):active {
  box-shadow: inset 0 2px 3px rgba(0, 0, 0, 0.50);
}

/* ── PRIMARY ──────────────────────────────────────────────────────── */
.kin-btn--primary {
  background-image: linear-gradient(180deg, #313130 0%, #1C1C1E 100%);
  color: rgb(var(--ink-inverse));
  box-shadow: var(--shadow-resting);
}
.kin-btn--primary:not(:disabled):not([aria-busy="true"]):hover {
  background-image: linear-gradient(180deg, #4A4A4F 0%, #2A2A2E 100%);
  box-shadow: var(--shadow-hover);
}
.kin-btn--primary:not(:disabled):not([aria-busy="true"]):active {
  background-image: linear-gradient(180deg, #000000 0%, #000000 100%);
}

/* PRIMARY — dark mode: inverted to light cream */
.dark .kin-btn--primary {
  background-image: linear-gradient(180deg, #F5F2EE 0%, #E8E4DF 100%);
  color: rgb(var(--ink-inverse));   /* #1C1C1E in dark */
}
.dark .kin-btn--primary:not(:disabled):not([aria-busy="true"]):hover {
  background-image: linear-gradient(180deg, #FFFFFF 0%, #F0EDE9 100%);
  box-shadow: var(--shadow-elevated);
}
.dark .kin-btn--primary:not(:disabled):not([aria-busy="true"]):active {
  background-image: linear-gradient(180deg, #9E9992 0%, #9E9992 100%);
  box-shadow: inset 0 2px 3px rgba(0, 0, 0, 0.45);
}

/* ── SECONDARY ────────────────────────────────────────────────────── */
.kin-btn--secondary {
  background-color: rgb(var(--surface-raised));
  color: rgb(var(--ink-primary));
  border: 1px solid rgb(var(--border-subtle));
  box-shadow: var(--shadow-resting);
}
.kin-btn--secondary:not(:disabled):not([aria-busy="true"]):hover {
  box-shadow: var(--shadow-hover);
}

/* SECONDARY — dark mode: bump contrast so the button doesn't disappear into
   the page background. Resting uses surface-overlay (lighter than surface-raised)
   and a stronger border. Hover bumps another step. */
.dark .kin-btn--secondary {
  background-color: rgb(var(--surface-overlay));   /* #242220 */
  border-color: rgb(var(--border-strong));
}
.dark .kin-btn--secondary:not(:disabled):not([aria-busy="true"]):hover {
  background-color: rgb(var(--ink-tertiary) / 0.18);
  box-shadow: var(--shadow-elevated);
}
.dark .kin-btn--secondary:not(:disabled):not([aria-busy="true"]):active {
  background-color: rgb(var(--surface-sunken));    /* #161513 */
  box-shadow: inset 0 2px 3px rgba(0, 0, 0, 0.55);
}

/* ── GHOST ────────────────────────────────────────────────────────── */
.kin-btn--ghost {
  background: transparent;
  color: rgb(var(--ink-primary));
  box-shadow: none;
}
.kin-btn--ghost:not(:disabled):not([aria-busy="true"]):hover {
  background-color: rgb(var(--surface-sunken));
  box-shadow: var(--shadow-resting);
}
.kin-btn--ghost:not(:disabled):not([aria-busy="true"]):active {
  filter: brightness(0.95);
}

/* GHOST — dark mode: tokens auto-swap via --surface-* */
.dark .kin-btn--ghost:not(:disabled):not([aria-busy="true"]):hover {
  background-color: rgb(var(--surface-overlay));   /* #242220 */
}
.dark .kin-btn--ghost:not(:disabled):not([aria-busy="true"]):active {
  background-color: rgb(var(--surface-sunken));    /* #161513 */
}

/* ── DANGER ───────────────────────────────────────────────────────── */
.kin-btn--danger {
  background-image: linear-gradient(180deg, #C96060 0%, #BA4A4A 100%);
  color: #ffffff;
  box-shadow: var(--shadow-resting);
}
.kin-btn--danger:not(:disabled):not([aria-busy="true"]):hover {
  background-image: linear-gradient(180deg, #D97070 0%, #C96060 100%);
  box-shadow: var(--shadow-hover);
}
.kin-btn--danger:not(:disabled):not([aria-busy="true"]):active {
  background-image: linear-gradient(180deg, #8C3030 0%, #8C3030 100%);
}

/* DANGER — dark mode: lighter rose */
.dark .kin-btn--danger {
  background-image: linear-gradient(180deg, #EC8080 0%, #E67070 100%);
}
.dark .kin-btn--danger:not(:disabled):not([aria-busy="true"]):hover {
  background-image: linear-gradient(180deg, #F29A9A 0%, #EC8080 100%);
  box-shadow: var(--shadow-elevated);
}
.dark .kin-btn--danger:not(:disabled):not([aria-busy="true"]):active {
  background-image: linear-gradient(180deg, #9E4040 0%, #9E4040 100%);
  box-shadow: inset 0 2px 3px rgba(0, 0, 0, 0.55);
}

/* ── ACCENT ───────────────────────────────────────────────────────── */
.kin-btn--accent {
  background-image: linear-gradient(180deg, #D4A96A 0%, #C4975A 100%);
  color: rgb(var(--ink-primary));   /* near-black on gold in light mode */
  box-shadow: var(--shadow-resting);
}
.kin-btn--accent:not(:disabled):not([aria-busy="true"]):hover {
  background-image: linear-gradient(180deg, #E4B97A 0%, #D4A96A 100%);
  box-shadow: var(--shadow-hover);
}
.kin-btn--accent:not(:disabled):not([aria-busy="true"]):active {
  background-image: linear-gradient(180deg, #8B6A3E 0%, #8B6A3E 100%);
}

/* ACCENT — dark mode: same gold (brand constant), force near-black text */
.dark .kin-btn--accent {
  color: #1C1C1E;   /* override ink-primary which is off-white in dark */
}
.dark .kin-btn--accent:not(:disabled):not([aria-busy="true"]):hover {
  background-image: linear-gradient(180deg, #E4B97A 0%, #D4A96A 100%);
  box-shadow: var(--shadow-elevated);
}
.dark .kin-btn--accent:not(:disabled):not([aria-busy="true"]):active {
  background-image: linear-gradient(180deg, #8B6A3E 0%, #8B6A3E 100%);
  box-shadow: inset 0 2px 3px rgba(0, 0, 0, 0.55);
}
</style>
