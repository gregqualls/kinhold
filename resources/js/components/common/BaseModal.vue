<template>
  <KinModalSheet
    :model-value="show"
    :title="title"
    :size="kinSize"
    @update:model-value="onUpdateModelValue"
    @close="$emit('close')"
  >
    <slot></slot>
    <template v-if="$slots.footer" #actions>
      <div class="flex gap-3 justify-end">
        <slot name="footer"></slot>
      </div>
    </template>
  </KinModalSheet>
</template>

<script setup>
import { computed } from 'vue'
import KinModalSheet from '@/components/design-system/KinModalSheet.vue'

const props = defineProps({
  show: Boolean,
  title: String,
  size: {
    type: String,
    default: 'md',
    validator: (v) => ['sm', 'md', 'lg', 'xl'].includes(v),
  },
})

const emit = defineEmits(['close'])

// KinModalSheet supports sm/md/lg only — map xl → lg.
const kinSize = computed(() => {
  if (props.size === 'xl') return 'lg'
  return props.size
})

function onUpdateModelValue(val) {
  if (val === false) emit('close')
}
</script>
