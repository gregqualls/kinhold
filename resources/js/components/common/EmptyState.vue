<template>
  <div class="flex flex-col items-center justify-center py-12 px-4">
    <component :is="icon" v-if="icon" class="w-16 h-16 text-lavender-400 dark:text-lavender-500 mb-4" />
    <h3 class="text-lg font-semibold text-prussian-500 dark:text-lavender-200 mb-2">{{ title }}</h3>
    <p class="text-lavender-600 dark:text-lavender-400 text-center mb-6 max-w-sm">{{ description }}</p>
    <BaseButton v-if="actionText" variant="primary" @click="$emit('action')">
      {{ actionText }}
    </BaseButton>
  </div>
</template>

<script setup>
import BaseButton from './BaseButton.vue'

defineProps({
  icon: [Object, Function],
  title: String,
  description: String,
  actionText: String,
})

defineEmits(['action'])
</script>
