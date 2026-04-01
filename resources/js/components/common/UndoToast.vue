<template>
  <Teleport to="body">
    <Transition name="toast">
      <div
        v-if="show"
        class="fixed bottom-28 md:bottom-8 left-1/2 -translate-x-1/2 z-[80] bg-prussian-500 dark:bg-prussian-700 text-white px-5 py-3 rounded-[12px] shadow-xl flex items-center gap-4 min-w-[280px]"
      >
        <span class="text-sm flex-1">{{ message }}</span>
        <button
          v-if="undoable"
          class="text-sm font-semibold text-wisteria-300 hover:text-wisteria-200 uppercase tracking-wide"
          @click="$emit('undo')"
        >
          Undo
        </button>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { watch } from 'vue'

const props = defineProps({
  show: Boolean,
  message: { type: String, default: '' },
  undoable: { type: Boolean, default: false },
  duration: { type: Number, default: 3000 },
})

const emit = defineEmits(['undo', 'dismiss'])

let timer = null

watch(() => props.show, (val) => {
  if (val) {
    clearTimeout(timer)
    timer = setTimeout(() => {
      emit('dismiss')
    }, props.duration)
  }
})
</script>

<style scoped>
.toast-enter-active {
  transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
.toast-leave-active {
  transition: all 0.2s ease-in;
}
.toast-enter-from {
  opacity: 0;
  transform: translate(-50%, 20px);
}
.toast-leave-to {
  opacity: 0;
  transform: translate(-50%, 20px);
}
</style>
