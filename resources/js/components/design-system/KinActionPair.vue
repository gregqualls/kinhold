<!--
  KinActionPair — the canonical two-button decision pattern.
  @see /design-system/action-pair  (docs/design/COMPONENT_ROADMAP.md §4.4)
  Props: layout ('equal' | 'asymmetric'), align ('start' | 'end' | 'stretch')
  Slots: #secondary (left — outline/ghost), #primary (right — filled)

  Primary sits on the right (rightmost = most forward action, per platform
  conventions). Secondary on the left. Variants:
    equal      — both buttons expand to fill (flex-1). Default for full-width
                 decision rows and card footers.
    asymmetric — secondary is ghost/compact, primary holds intrinsic width.
                 Used inside list rows where actions share space with content.

  Children are expected to be KinButton instances — this component does not
  style them, only provides the spacing + flex layout convention.
-->
<script setup>
import { computed } from 'vue'

const props = defineProps({
  /** Layout strategy. */
  layout: {
    type: String,
    default: 'equal',
    validator: (v) => ['equal', 'asymmetric'].includes(v),
  },
  /** Horizontal alignment of the pair. Only applies when the pair doesn't fill its container (asymmetric + no flex-1). */
  align: {
    type: String,
    default: 'stretch',
    validator: (v) => ['start', 'end', 'stretch'].includes(v),
  },
})

const wrapperClass = computed(() => {
  const base = 'flex gap-2'
  // Asymmetric layouts span the container with ghost pushed left + primary right.
  if (props.layout === 'asymmetric') {
    return `${base} justify-between items-center`
  }
  // Equal layouts respect the align prop.
  const alignCls = {
    start:   'justify-start',
    end:     'justify-end',
    stretch: '',
  }[props.align]
  return [base, alignCls].filter(Boolean).join(' ')
})

const childClass = computed(() =>
  props.layout === 'equal' ? 'flex-1' : ''
)
</script>

<template>
  <div :class="wrapperClass">
    <div :class="childClass">
      <slot name="secondary" />
    </div>
    <div :class="childClass">
      <slot name="primary" />
    </div>
  </div>
</template>

<style scoped>
/* When slot children are KinButton and flex-1 is applied to this wrapper,
   the button must also fill its wrapper. KinButton is an inline-flex root
   so we apply width: 100% to any button-like element nested inside. */
:deep(.flex-1 > button),
:deep(.flex-1 > a) {
  width: 100%;
}
</style>
