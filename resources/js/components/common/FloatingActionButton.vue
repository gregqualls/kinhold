<template>
  <button
    class="fixed bottom-24 right-5 md:bottom-8 z-30 w-14 h-14 rounded-full shadow-lg flex items-center justify-center transition-all duration-200 active:scale-95 bg-accent-lavender-bold text-white hover:brightness-110 active:brightness-95"
    :class="{ 'md:hidden': mobileOnly }"
    :style="{ paddingBottom: 'env(safe-area-inset-bottom, 0px)' }"
    @click="$emit('click')"
  >
    <slot>
      <PlusIcon class="w-7 h-7" />
    </slot>
  </button>
</template>

<script setup>
import { PlusIcon } from '@heroicons/vue/24/solid'

defineProps({
  mobileOnly: { type: Boolean, default: true },
})

defineEmits(['click'])
</script>
