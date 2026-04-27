<script setup>
import { ref, watch, onMounted } from 'vue'

const props = defineProps({
  width: { type: Number, default: null }, // px; null = full width
})

const lightRef = ref(null)
const darkRef = ref(null)

function applyDark(el) {
  if (!el) return
  el.classList.add('dark')
  el.classList.remove('light')
}

function applyLight(el) {
  if (!el) return
  el.classList.add('light')
  el.classList.remove('dark')
}

onMounted(() => {
  applyLight(lightRef.value)
  applyDark(darkRef.value)
})
</script>

<template>
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <!-- Light mode panel -->
    <div ref="lightRef" class="rounded-2xl border border-kin-gray-200 bg-kin-ivory overflow-hidden">
      <header class="px-4 py-2 text-xs font-medium uppercase tracking-wider text-kin-gray-500 bg-kin-cream border-b border-kin-gray-200 flex items-center justify-between">
        <span>Light</span>
        <span v-if="width" class="font-mono">{{ width }}px</span>
      </header>
      <div
        class="overflow-x-auto"
      >
        <div
          class="mx-auto"
          :style="width ? { maxWidth: width + 'px' } : {}"
        >
          <div class="p-6">
            <slot />
          </div>
        </div>
      </div>
    </div>

    <!-- Dark mode panel -->
    <div ref="darkRef" class="rounded-2xl border border-kin-gray-700 bg-kin-bg-dark overflow-hidden">
      <header class="px-4 py-2 text-xs font-medium uppercase tracking-wider text-kin-gray-300 bg-kin-surface-dark border-b border-kin-gray-700 flex items-center justify-between">
        <span>Dark</span>
        <span v-if="width" class="font-mono">{{ width }}px</span>
      </header>
      <div
        class="overflow-x-auto"
      >
        <div
          class="mx-auto"
          :style="width ? { maxWidth: width + 'px' } : {}"
        >
          <div class="p-6">
            <slot />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Scope dark mode to the dark panel only — otherwise Tailwind's .dark class from <html> governs everything */
</style>
