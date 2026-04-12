<template>
  <EmptyState
    :icon="ShoppingCartIcon"
    title="Shopping Lists Coming Soon"
    description="Auto-generated from your meal plan, plus staples and manual items. Check off as you shop."
  />
</template>

<script setup>
import { ShoppingCartIcon } from '@heroicons/vue/24/outline'
import EmptyState from '@/components/common/EmptyState.vue'
</script>
