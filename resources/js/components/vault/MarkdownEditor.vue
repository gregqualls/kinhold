<template>
  <MilkdownEditorCore
    :model-value="modelValue"
    :readonly="readonly"
    :placeholder="placeholder"
    @update:model-value="$emit('update:modelValue', $event)"
  />
</template>

<script setup>
import MilkdownEditorCore from './MilkdownEditorCore.vue'

defineProps({
  modelValue: {
    type: String,
    default: '',
  },
  readonly: {
    type: Boolean,
    default: false,
  },
  placeholder: {
    type: String,
    default: 'Start writing...',
  },
})

defineEmits(['update:modelValue'])
</script>
