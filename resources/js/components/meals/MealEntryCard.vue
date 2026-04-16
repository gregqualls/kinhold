<template>
  <div
    class="group flex items-start gap-2 p-2 bg-white dark:bg-prussian-700 border border-lavender-200 dark:border-prussian-600 rounded-lg cursor-pointer hover:border-[#C4975A]/50 hover:shadow-sm transition-all text-xs"
    :data-entry-id="entry.id"
    @click="$emit('click', entry)"
  >
    <!-- Type icon -->
    <component
      :is="typeIcon"
      class="w-3.5 h-3.5 flex-shrink-0 mt-0.5"
      :class="typeColor"
    />

    <!-- Title + meta -->
    <div class="flex-1 min-w-0">
      <p class="font-medium text-prussian-500 dark:text-lavender-200 leading-tight truncate">
        {{ entry.display_title }}
      </p>
      <p v-if="entry.effective_servings" class="text-lavender-400 dark:text-lavender-500 mt-0.5">
        {{ entry.effective_servings }} servings
      </p>
    </div>

    <!-- Delete -->
    <button
      class="flex-shrink-0 p-0.5 rounded opacity-0 group-hover:opacity-100 text-lavender-300 hover:text-red-500 dark:hover:text-red-400 transition-all"
      @click.stop="$emit('delete', entry)"
    >
      <XMarkIcon class="w-3.5 h-3.5" />
    </button>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import {
  FireIcon,
  BuildingStorefrontIcon,
  SparklesIcon,
  PencilIcon,
  XMarkIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  entry: { type: Object, required: true },
})

defineEmits(['click', 'delete'])

const typeIcon = computed(() => {
  switch (props.entry.type) {
    case 'recipe': return FireIcon
    case 'restaurant': return BuildingStorefrontIcon
    case 'preset': return SparklesIcon
    default: return PencilIcon
  }
})

const typeColor = computed(() => {
  switch (props.entry.type) {
    case 'recipe': return 'text-orange-400'
    case 'restaurant': return 'text-blue-400'
    case 'preset': return 'text-purple-400'
    default: return 'text-lavender-400'
  }
})
</script>
