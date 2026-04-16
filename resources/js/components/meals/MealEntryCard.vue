<template>
  <div
    class="group flex items-start gap-2 p-2 bg-white dark:bg-[#1C1C20] border border-[#E8E4DF] dark:border-[#2E2E32] rounded-[10px] cursor-pointer hover:border-[#C4975A]/50 hover:shadow-sm transition-all text-xs"
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
      <p class="font-medium text-[#1C1C1E] dark:text-[#F0EDE9] leading-tight truncate">
        {{ entry.display_title }}
      </p>
      <p v-if="entry.effective_servings" class="text-[#9C9895] mt-0.5">
        {{ entry.effective_servings }} servings
      </p>
    </div>

    <!-- Delete -->
    <button
      class="flex-shrink-0 p-0.5 rounded opacity-0 group-hover:opacity-100 text-[#9C9895] hover:text-[#C45B5B] transition-all"
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
    case 'recipe': return 'text-[#C48B3F]'
    case 'restaurant': return 'text-[#5B7B9C]'
    case 'preset': return 'text-[#7B6B9C]'
    default: return 'text-[#9C9895]'
  }
})
</script>
