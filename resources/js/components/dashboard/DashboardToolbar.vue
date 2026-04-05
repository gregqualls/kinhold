<template>
  <div class="sticky top-0 z-20 bg-white/90 dark:bg-prussian-900/90 backdrop-blur-sm border-b border-lavender-200 dark:border-prussian-700 px-4 py-3 -mx-4 md:-mx-6 mb-4 flex items-center justify-between gap-3">
    <div class="flex items-center gap-2">
      <PencilSquareIcon class="w-5 h-5 text-wisteria-500" />
      <span class="text-sm font-semibold text-prussian-500 dark:text-lavender-200">Editing Dashboard</span>
    </div>
    <div class="flex items-center gap-2">
      <button
        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium rounded-lg bg-wisteria-100 dark:bg-wisteria-900/30 text-wisteria-700 dark:text-wisteria-300 hover:bg-wisteria-200 dark:hover:bg-wisteria-900/50 transition-colors"
        @click="$emit('add')"
      >
        <PlusIcon class="w-4 h-4" />
        Add Widget
      </button>
      <button
        class="px-3 py-1.5 text-sm font-medium rounded-lg text-lavender-600 dark:text-lavender-400 hover:bg-lavender-100 dark:hover:bg-prussian-700 transition-colors"
        @click="$emit('cancel')"
      >
        Cancel
      </button>
      <button
        :disabled="saving"
        class="px-3 py-1.5 text-sm font-medium rounded-lg bg-wisteria-600 text-white hover:bg-wisteria-700 disabled:opacity-50 transition-colors"
        @click="$emit('save')"
      >
        {{ saving ? 'Saving...' : 'Save' }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { PencilSquareIcon, PlusIcon } from '@heroicons/vue/24/outline'

defineProps({
  saving: { type: Boolean, default: false },
})

defineEmits(['add', 'cancel', 'save'])
</script>
