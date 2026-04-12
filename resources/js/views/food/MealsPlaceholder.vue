<template>
  <EmptyState
    :icon="CalendarDaysIcon"
    title="Meal Planning Coming Soon"
    description="Plan your weekly meals, auto-generate shopping lists, and keep your family fed without the stress."
  />
</template>

<script setup>
import { CalendarDaysIcon } from '@heroicons/vue/24/outline'
import EmptyState from '@/components/common/EmptyState.vue'
</script>
