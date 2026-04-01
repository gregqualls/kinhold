<template>
  <button
    class="md:hidden fixed bottom-24 right-5 z-30 w-14 h-14 rounded-full shadow-lg flex items-center justify-center transition-all duration-200 active:scale-95 bg-wisteria-600 text-white hover:bg-wisteria-500 active:bg-wisteria-700"
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

defineEmits(['click'])
</script>
