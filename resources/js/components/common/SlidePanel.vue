<template>
  <Teleport to="body">
    <Transition name="fade">
      <div
        v-if="show"
        class="fixed inset-0 bg-black/30 z-50"
        @click="$emit('close')"
      />
    </Transition>

    <Transition name="slide-panel">
      <div
        v-if="show"
        class="fixed inset-y-0 right-0 z-50 w-full sm:w-[420px] md:w-[480px] bg-white dark:bg-prussian-800 shadow-2xl flex flex-col"
        @click.stop
      >
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-lavender-200 dark:border-prussian-700">
          <h2 class="text-lg font-semibold text-prussian-500 dark:text-lavender-200">{{ title }}</h2>
          <button
            @click="$emit('close')"
            class="p-2 -mr-2 text-lavender-400 hover:text-prussian-500 dark:hover:text-lavender-200 hover:bg-lavender-100 dark:hover:bg-prussian-700 rounded-lg transition-colors"
          >
            <XMarkIcon class="w-5 h-5" />
          </button>
        </div>

        <!-- Content -->
        <div class="flex-1 overflow-y-auto">
          <slot />
        </div>

        <!-- Footer -->
        <div v-if="$slots.footer" class="border-t border-lavender-200 dark:border-prussian-700 px-6 py-4">
          <slot name="footer" />
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { watch, onUnmounted } from 'vue'
import { XMarkIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  show: Boolean,
  title: { type: String, default: '' },
})

const emit = defineEmits(['close'])

const handleEscape = (e) => {
  if (e.key === 'Escape') emit('close')
}

watch(() => props.show, (val) => {
  if (val) {
    document.addEventListener('keydown', handleEscape)
    document.body.style.overflow = 'hidden'
  } else {
    document.removeEventListener('keydown', handleEscape)
    document.body.style.overflow = ''
  }
})

onUnmounted(() => {
  document.removeEventListener('keydown', handleEscape)
  document.body.style.overflow = ''
})
</script>

<style scoped>
.slide-panel-enter-active {
  transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
.slide-panel-leave-active {
  transition: transform 0.2s ease-in;
}
.slide-panel-enter-from,
.slide-panel-leave-to {
  transform: translateX(100%);
}
</style>
