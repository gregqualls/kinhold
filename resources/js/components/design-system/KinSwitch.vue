<!--
  KinSwitch — Apple-style pastel-accent toggle.
  @see /design-system/selection  (docs/design/COMPONENT_ROADMAP.md §1.5)
  Props: modelValue, label, description, disabled, size, color, id
  Emits: update:modelValue
-->
<script setup>
import { computed } from 'vue'

// ── Props ────────────────────────────────────────────────────────────────────
const props = defineProps({
  modelValue:  { type: Boolean, default: false  },
  label:       { type: String,  default: ''     },
  description: { type: String,  default: ''     },
  disabled:    { type: Boolean, default: false  },
  /**
   * sm = 28×16px track, 12px thumb
   * md = 36×22px track, 18px thumb  (default)
   */
  size:        { type: String,  default: 'md',   validator: (v) => ['sm', 'md'].includes(v) },
  /**
   * Accent color applied to track when ON.
   * lavender | peach | mint | sun  (default: mint)
   */
  color:       { type: String,  default: 'mint', validator: (v) => ['lavender', 'peach', 'mint', 'sun'].includes(v) },
  id:          { type: String,  default: ''      },
})

// ── Emits ────────────────────────────────────────────────────────────────────
const emit = defineEmits(['update:modelValue'])

// ── Stable auto-id ───────────────────────────────────────────────────────────
const _autoId = `kin-sw-${Math.random().toString(36).slice(2, 9)}`
const inputId = computed(() => props.id || _autoId)
const descId  = computed(() => `${inputId.value}-desc`)

// ── Size maps ─────────────────────────────────────────────────────────────────
const sizeMap = computed(() => {
  if (props.size === 'sm') {
    return {
      track: 'w-[28px] h-[16px]',
      thumb: 'w-[12px] h-[12px]',
      // translateX = track width - thumb width - 2×padding(2px) = 28 - 12 - 4 = 12
      translate: '12px',
    }
  }
  return {
    track: 'w-[36px] h-[22px]',
    thumb: 'w-[18px] h-[18px]',
    // translateX = 36 - 18 - 4 = 14
    translate: '14px',
  }
})

// ── Color → CSS variable map ──────────────────────────────────────────────────
// Maps the color prop to the accent-bold CSS token name (track fill when on).
// Using data attributes on the track element so the scoped CSS can target them
// without computing arbitrary inline styles.
const colorAttr = computed(() => props.color)

// ── Handlers ──────────────────────────────────────────────────────────────────
function toggle() {
  !props.disabled && emit('update:modelValue', !props.modelValue)
}
</script>

<template>
  <label
    :for="inputId"
    :class="[
      'inline-flex items-center justify-between gap-3 select-none',
      // When there is a label, allow the whole row to be a click target.
      // Without a label the component is just the toggle widget itself.
      label || description ? 'w-full' : '',
      disabled ? 'cursor-not-allowed opacity-40' : 'cursor-pointer',
    ]"
  >
    <!-- Label + description (left side when present) -->
    <span v-if="label || description" class="flex flex-col gap-0.5 min-w-0">
      <span v-if="label" class="text-body-sm font-medium text-ink-primary truncate">{{ label }}</span>
      <span
        v-if="description"
        :id="descId"
        class="text-caption text-ink-tertiary leading-snug"
      >{{ description }}</span>
    </span>

    <!-- Toggle widget (right side) -->
    <span class="relative flex-shrink-0">
      <!-- Hidden native input -->
      <input
        :id="inputId"
        type="checkbox"
        class="peer sr-only"
        :checked="modelValue"
        :disabled="disabled"
        :aria-describedby="description ? descId : undefined"
        @change="toggle"
      />

      <!-- Track -->
      <span
        :class="['kin-sw-track rounded-full relative block', sizeMap.track]"
        :data-color="colorAttr"
        style="padding: 2px"
      >
        <!-- Thumb -->
        <span
          :class="[
            'kin-sw-thumb absolute top-[2px] left-[2px] rounded-full',
            sizeMap.thumb,
          ]"
          :style="{ '--sw-translate': sizeMap.translate }"
        ></span>
      </span>
    </span>
  </label>
</template>

<style scoped>
/*
  ═══════════════════════════════════════════════════════════════════
  KinSwitch — track + thumb interactive states

  Architecture:
  - Hidden peer input drives track bg (via peer:checked selector)
  - Thumb translates using --sw-translate CSS var (set inline per size)
  - Four accent colours (lavender / peach / mint / sun) are handled
    via [data-color] attribute on the track element.
  - Dark mode handled by the token vars automatically — no extra selectors.
  - prefers-reduced-motion is handled by tokens.css zeroing --duration-quick.
  ═══════════════════════════════════════════════════════════════════
*/

/* ── Track — off state ─────────────────────────────────────────── */
.kin-sw-track {
  background-color: rgb(var(--border-subtle));
  transition: background-color var(--duration-quick) var(--ease-out-soft),
              box-shadow        var(--duration-quick) var(--ease-out-soft);
}

/* Dark off */
.dark .kin-sw-track {
  background-color: rgb(var(--surface-overlay));
}

/* ── Track — on state, per colour ──────────────────────────────── */
.peer:checked ~ .kin-sw-track[data-color="lavender"] {
  background-color: rgb(var(--accent-lavender-bold));
}
.peer:checked ~ .kin-sw-track[data-color="peach"] {
  background-color: rgb(var(--accent-peach-bold));
}
.peer:checked ~ .kin-sw-track[data-color="mint"] {
  background-color: rgb(var(--accent-mint-bold));
}
.peer:checked ~ .kin-sw-track[data-color="sun"] {
  background-color: rgb(var(--accent-sun-bold));
}

/* ── Focus ring ────────────────────────────────────────────────── */
.peer:focus-visible ~ .kin-sw-track {
  box-shadow: 0 0 0 3px rgb(var(--accent-lavender-bold) / 0.25);
}

/* ── Hover ──────────────────────────────────────────────────────── */
label:not(.cursor-not-allowed):hover .kin-sw-track {
  filter: brightness(0.94);
}
.dark label:not(.cursor-not-allowed):hover .kin-sw-track {
  filter: brightness(1.12);
}

/* ── Thumb ──────────────────────────────────────────────────────── */
.kin-sw-thumb {
  background-color: rgb(var(--surface-raised));     /* white in light, warm-white in dark */
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.20), 0 1px 2px rgba(0, 0, 0, 0.10);
  transition: transform var(--duration-quick) var(--ease-out-soft);
}

/* Dark thumb gets deeper shadow for contrast on charcoal track */
.dark .kin-sw-thumb {
  background-color: rgb(var(--ink-primary));        /* off-white in dark (#F0EDE9) */
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.40), 0 1px 2px rgba(0, 0, 0, 0.30);
}

/* ── Thumb — slide to right when checked ───────────────────────── */
.peer:checked ~ .kin-sw-track .kin-sw-thumb {
  transform: translateX(var(--sw-translate));
}
</style>
