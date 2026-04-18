<script setup>
defineProps({
  label: { type: String, required: true }, // "A" | "B" | "C" | description
  caption: { type: String, default: '' },
  chosen: { type: Boolean, default: false },
})
</script>

<template>
  <article
    class="relative rounded-2xl border border-kin-gray-200 dark:border-kin-gray-700 bg-white dark:bg-kin-surface-dark overflow-hidden transition-shadow"
    :class="{ 'ring-2 ring-emerald-400': chosen }"
  >
    <header class="flex items-center justify-between px-4 py-3 border-b border-kin-gray-100 dark:border-kin-gray-800 bg-kin-gray-50/50 dark:bg-kin-gray-900/50">
      <div class="flex items-center gap-2">
        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-semibold bg-kin-black text-white dark:bg-kin-off-white dark:text-kin-black">{{ label }}</span>
        <p v-if="caption" class="text-sm text-kin-gray-500 dark:text-kin-gray-300">{{ caption }}</p>
      </div>
      <span v-if="chosen" class="text-xs font-medium text-emerald-600 dark:text-emerald-300">Chosen</span>
    </header>
    <div class="p-6 md:p-8 min-h-[200px] flex items-center justify-center">
      <slot />
    </div>
  </article>
</template>
