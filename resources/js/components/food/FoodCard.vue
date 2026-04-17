<template>
  <div
    class="bg-white dark:bg-[#1C1C20] border border-[#E8E4DF] dark:border-[#2E2E32] rounded-xl overflow-hidden cursor-pointer transition-colors hover:border-[#C4975A]/40"
    @click="$emit('click')"
  >
    <!-- Image -->
    <div class="aspect-[4/3] bg-lavender-100 dark:bg-prussian-700 relative overflow-hidden">
      <img
        v-if="imageUrl && !imgError"
        :src="imageUrl"
        :alt="title"
        class="w-full h-full object-cover"
        @error="imgError = true"
      />
      <div v-else class="w-full h-full flex items-center justify-center">
        <component :is="placeholderIcon" class="w-12 h-12 text-lavender-300 dark:text-prussian-600" />
      </div>

      <!-- Favorite heart -->
      <button
        v-if="showFavorite"
        class="absolute top-2 right-2 w-8 h-8 rounded-full flex items-center justify-center transition-colors"
        :class="isFavorite
          ? 'bg-red-500/90 text-white'
          : 'bg-black/30 text-white/80 hover:bg-black/50'"
        aria-label="Toggle favorite"
        @click.stop="$emit('toggle-favorite')"
      >
        <HeartIconSolid v-if="isFavorite" class="w-4 h-4" />
        <HeartIcon v-else class="w-4 h-4" />
      </button>
    </div>

    <!-- Content -->
    <div class="p-3">
      <!-- Title -->
      <h3 class="text-sm font-semibold text-prussian-500 dark:text-lavender-200 line-clamp-2 leading-tight">
        {{ title }}
      </h3>

      <!-- Meta row -->
      <div v-if="metaItems.length" class="flex items-center gap-3 mt-2 text-xs text-lavender-500 dark:text-lavender-400">
        <span
          v-for="(item, i) in metaItems"
          :key="i"
          class="flex items-center gap-1"
        >
          <component :is="item.icon" class="w-3.5 h-3.5" :class="item.iconClass || ''" />
          {{ item.text }}
        </span>
      </div>

      <!-- Tags / pills -->
      <div v-if="tags.length" class="flex items-center gap-1 mt-2 flex-wrap">
        <span
          v-for="(tag, i) in tags.slice(0, 3)"
          :key="i"
          class="px-2 py-0.5 text-[10px] font-medium rounded-full bg-lavender-100 dark:bg-prussian-700 text-lavender-600 dark:text-lavender-400"
        >
          {{ typeof tag === 'string' ? tag : tag.name }}
        </span>
        <span
          v-if="tags.length > 3"
          class="text-[10px] text-lavender-400 dark:text-lavender-500"
        >
          +{{ tags.length - 3 }}
        </span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { HeartIcon } from '@heroicons/vue/24/outline'
import { HeartIcon as HeartIconSolid } from '@heroicons/vue/24/solid'

const props = defineProps({
  title: { type: String, required: true },
  imageUrl: { type: String, default: null },
  placeholderIcon: { type: [Object, Function], required: true },
  isFavorite: { type: Boolean, default: false },
  showFavorite: { type: Boolean, default: true },
  metaItems: { type: Array, default: () => [] },
  tags: { type: Array, default: () => [] },
})

defineEmits(['click', 'toggle-favorite'])

const imgError = ref(false)
watch(() => props.imageUrl, () => { imgError.value = false })
</script>
