<template>
  <KinEmptyState :icon="icon" :title="title || ''" :description="description || ''">
    <template v-if="actionText" #cta>
      <KinButton variant="primary" @click="$emit('action')">{{ actionText }}</KinButton>
    </template>
  </KinEmptyState>
</template>

<script setup>
import KinEmptyState from '@/components/design-system/KinEmptyState.vue'
import KinButton from '@/components/design-system/KinButton.vue'

defineProps({
  icon: [Object, Function],
  title: String,
  description: String,
  actionText: String,
})

defineEmits(['action'])
</script>
