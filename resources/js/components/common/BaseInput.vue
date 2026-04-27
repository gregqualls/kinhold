<template>
  <KinInput
    :model-value="modelValue"
    :label="labelWithRequired"
    :type="type"
    :placeholder="placeholder"
    :disabled="disabled"
    :required="required"
    :error="error"
    @update:model-value="$emit('update:modelValue', $event)"
    @blur="$emit('blur')"
  />
</template>

<script setup>
import { computed } from 'vue'
import KinInput from '@/components/design-system/KinInput.vue'

const props = defineProps({
  label: String,
  type: {
    type: String,
    default: 'text',
  },
  modelValue: {
    type: [String, Number],
    default: '',
  },
  error: String,
  placeholder: String,
  disabled: Boolean,
  required: Boolean,
})

defineEmits(['update:modelValue', 'blur'])

// KinInput renders the required asterisk itself when `required` is true and a label exists.
// Just forward the label string straight through.
const labelWithRequired = computed(() => props.label || '')
</script>
