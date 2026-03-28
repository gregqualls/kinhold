<template>
  <div
    v-if="label"
    class="flex items-center justify-between p-4 bg-lavender-50 dark:bg-prussian-700 rounded-lg"
    :class="{ 'opacity-50 cursor-not-allowed': disabled }"
  >
    <div class="flex-1 mr-4">
      <p class="font-medium text-prussian-500 dark:text-lavender-200">{{ label }}</p>
      <p v-if="description" class="text-xs text-lavender-700 dark:text-lavender-400 mt-0.5">{{ description }}</p>
    </div>
    <button
      type="button"
      role="switch"
      :aria-checked="modelValue"
      :disabled="disabled"
      @click="toggle"
      :class="[
        'relative inline-flex shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-wisteria-400 focus:ring-offset-2 dark:focus:ring-offset-prussian-700',
        modelValue ? 'bg-wisteria-500' : 'bg-lavender-300 dark:bg-prussian-600',
        disabled ? 'cursor-not-allowed' : '',
        size === 'sm' ? 'h-6 w-11' : 'h-7 w-12',
      ]"
    >
      <span
        :class="[
          'pointer-events-none inline-flex items-center justify-center transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
          size === 'sm'
            ? (modelValue ? 'translate-x-5 h-4 w-4' : 'translate-x-0.5 h-4 w-4')
            : (modelValue ? 'translate-x-5 h-6 w-6' : 'translate-x-0 h-6 w-6'),
        ]"
      >
        <slot name="thumb" />
      </span>
    </button>
  </div>

  <button
    v-else
    type="button"
    role="switch"
    :aria-checked="modelValue"
    :disabled="disabled"
    @click="toggle"
    :class="[
      'relative inline-flex shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-wisteria-400 focus:ring-offset-2 dark:focus:ring-offset-prussian-700',
      modelValue ? 'bg-wisteria-500' : 'bg-lavender-300 dark:bg-prussian-600',
      disabled ? 'cursor-not-allowed opacity-50' : '',
      size === 'sm' ? 'h-6 w-11' : 'h-7 w-12',
    ]"
  >
    <span
      :class="[
        'pointer-events-none inline-flex items-center justify-center transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
        size === 'sm'
          ? (modelValue ? 'translate-x-5 h-4 w-4' : 'translate-x-0.5 h-4 w-4')
          : (modelValue ? 'translate-x-5 h-6 w-6' : 'translate-x-0 h-6 w-6'),
      ]"
    >
      <slot name="thumb" />
    </span>
  </button>
</template>

<script setup>
const props = defineProps({
  modelValue: {
    type: Boolean,
    required: true,
  },
  label: {
    type: String,
    default: '',
  },
  description: {
    type: String,
    default: '',
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  size: {
    type: String,
    default: 'md',
    validator: (v) => ['sm', 'md'].includes(v),
  },
})

const emit = defineEmits(['update:modelValue'])

const toggle = () => {
  emit('update:modelValue', !props.modelValue)
}
</script>
