<template>
  <div class="space-y-1">
    <label v-if="label" class="block text-sm font-medium text-prussian-500 dark:text-lavender-200">
      {{ label }}
      <span v-if="required" class="text-red-500">*</span>
    </label>
    <input
      :type="type"
      :value="modelValue"
      :placeholder="placeholder"
      :disabled="disabled"
      :class="[
        'input-base',
        error && 'input-error',
        disabled && 'bg-lavender-50 dark:bg-prussian-700 cursor-not-allowed',
      ]"
      @input="$emit('update:modelValue', $event.target.value)"
      @blur="$emit('blur')"
    />
    <p v-if="error" class="text-xs text-red-600">{{ error }}</p>
  </div>
</template>

<script setup>
defineProps({
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
</script>
