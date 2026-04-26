<!--
  KinActionPair — the canonical two-button decision pattern.
  @see /design-system/action-pair  (docs/design/COMPONENT_ROADMAP.md §4.4)
  Props: layout ('equal' | 'asymmetric'), align ('start' | 'end' | 'stretch')
  Slots: #secondary (left — outline/ghost), #primary (right — filled)

  Primary sits on the right (rightmost = most forward action, per platform
  conventions). Secondary on the left.

  Layouts:
    equal      — both buttons share the row 50/50 (flex-1 each).
                 Default for full-width decision rows and card footers.
    asymmetric — confident hierarchy: secondary takes 1/3 (ghost),
                 primary takes 2/3 (filled). Both still fill their slot.

  Children are expected to be KinButton instances — this component does not
  style them, only provides the spacing + flex-weight layout convention.
-->
<script setup>
import { computed } from 'vue'

const props = defineProps({
  layout: {
    type: String,
    default: 'equal',
    validator: (v) => ['equal', 'asymmetric'].includes(v),
  },
  /** Horizontal alignment of the pair when align !== 'stretch' on equal layout. */
  align: {
    type: String,
    default: 'stretch',
    validator: (v) => ['start', 'end', 'stretch'].includes(v),
  },
})

const wrapperClass = computed(() => {
  const base = 'flex gap-2 items-center'
  if (props.layout === 'asymmetric' || props.align === 'stretch') return base
  const alignCls = { start: 'justify-start', end: 'justify-end' }[props.align]
  return [base, alignCls].filter(Boolean).join(' ')
})

const secondaryClass = computed(() =>
  props.layout === 'asymmetric' ? 'kin-action-pair__slot--narrow' : 'flex-1'
)
const primaryClass = computed(() =>
  props.layout === 'asymmetric' ? 'kin-action-pair__slot--wide' : 'flex-1'
)
</script>

<template>
  <div :class="wrapperClass">
    <div :class="secondaryClass">
      <slot name="secondary" />
    </div>
    <div :class="primaryClass">
      <slot name="primary" />
    </div>
  </div>
</template>

<style scoped>
.kin-action-pair__slot--narrow { flex: 1 1 0; }
.kin-action-pair__slot--wide   { flex: 2 1 0; }

/* Buttons inside any flex-weighted slot fill the slot. */
:deep(.flex-1 > button),
:deep(.flex-1 > a),
:deep(.kin-action-pair__slot--narrow > button),
:deep(.kin-action-pair__slot--narrow > a),
:deep(.kin-action-pair__slot--wide > button),
:deep(.kin-action-pair__slot--wide > a) {
  width: 100%;
}
</style>
