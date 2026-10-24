<template>
  <nav class="flex flex-col gap-1" :aria-label="label">
    <button
      v-for="section in sections"
      :key="section.key"
      type="button"
      class="kin-pressable flex items-center gap-3 px-3 py-2.5 rounded-lg text-left text-sm font-medium text-ink-primary hover:bg-surface-sunken transition-[color,background-color,border-color,fill,stroke,transform] duration-150"
      :data-selected="section.key === modelValue || null"
      :aria-current="section.key === modelValue ? 'true' : undefined"
      @click="$emit('update:modelValue', section.key)"
    >
      <component
        :is="section.icon"
        v-if="section.icon"
        class="w-5 h-5 flex-shrink-0"
        :class="section.key === modelValue ? 'text-accent-lavender-bold' : 'text-ink-tertiary'"
      />
      <span class="flex-1 min-w-0 truncate">{{ section.title }}</span>
      <span
        v-if="section.badge"
        class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-accent-lavender-soft/40 text-accent-lavender-bold whitespace-nowrap"
      >
        {{ section.badge }}
      </span>
    </button>
  </nav>
</template>

<script setup>
defineProps({
  sections: {
    type: Array,
    required: true,
    // Each: { key: String, title: String, icon?: Component, badge?: String }
  },
  modelValue: {
    type: String,
    default: null,
  },
  label: {
    type: String,
    default: 'Settings sections',
  },
})

defineEmits(['update:modelValue'])
</script>
