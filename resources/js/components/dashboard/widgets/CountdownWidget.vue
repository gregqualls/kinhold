<template>
  <div v-if="countdownEvent">
    <CountdownBanner :countdown-event="countdownEvent" />
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useFeaturedEventsStore } from '@/stores/featuredEvents'
import CountdownBanner from '@/components/featured-events/CountdownBanner.vue'

defineProps({
  config: { type: Object, required: true },
})

const featuredEventsStore = useFeaturedEventsStore()

// Fetch countdown if store hasn't loaded yet
if (!featuredEventsStore.countdownEvent) {
  featuredEventsStore.fetchCountdown()
}

const countdownEvent = computed(() => featuredEventsStore.countdownEvent)
</script>
