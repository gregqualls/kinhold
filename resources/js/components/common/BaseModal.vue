<template>
  <Teleport to="body">
    <Transition name="fade">
      <div
        v-if="show"
        class="fixed inset-0 bg-black/50 z-50 flex items-end md:items-center justify-center p-4"
        @click="closeModal"
      >
        <Transition name="slide">
          <div
            v-if="show"
            class="bg-white dark:bg-prussian-800 rounded-t-2xl md:rounded-2xl w-full max-h-screen md:max-h-96 flex flex-col"
            :class="sizeClasses"
            @click.stop
          >
            <!-- Header -->
            <div v-if="title" class="px-6 py-4 border-b border-lavender-200 dark:border-prussian-700 flex items-center justify-between">
              <h2 class="text-xl font-semibold text-prussian-500 dark:text-lavender-200">{{ title }}</h2>
              <button
                @click="closeModal"
                class="p-2 hover:bg-lavender-100 dark:hover:bg-prussian-700 rounded-lg transition-colors text-lavender-500 dark:text-lavender-400"
                aria-label="Close modal"
              >
                <XMarkIcon class="w-5 h-5" />
              </button>
            </div>

            <!-- Content -->
            <div class="flex-1 overflow-y-auto p-6">
              <slot />
            </div>

            <!-- Footer -->
            <div v-if="$slots.footer" class="px-6 py-4 border-t border-lavender-200 dark:border-prussian-700 flex gap-3 justify-end">
              <slot name="footer" />
            </div>
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { computed, watch } from 'vue'
import { XMarkIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  show: Boolean,
  title: String,
  size: {
    type: String,
    default: 'md',
    validator: (v) => ['sm', 'md', 'lg'].includes(v),
  },
})

const emit = defineEmits(['close'])

const sizeClasses = computed(() => {
  const sizes = {
    sm: 'md:w-96',
    md: 'md:w-full md:max-w-md',
    lg: 'md:w-full md:max-w-lg',
  }
  return sizes[props.size]
})

const closeModal = () => {
  emit('close')
}

// Close on escape key
watch(
  () => props.show,
  (newVal) => {
    if (newVal) {
      document.addEventListener('keydown', handleEscape)
    } else {
      document.removeEventListener('keydown', handleEscape)
    }
  }
)

const handleEscape = (e) => {
  if (e.key === 'Escape') {
    closeModal()
  }
}
</script>
