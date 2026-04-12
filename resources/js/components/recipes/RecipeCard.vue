<template>
  <div
    class="bg-white dark:bg-[#1C1C20] border border-[#E8E4DF] dark:border-[#2E2E32] rounded-xl overflow-hidden cursor-pointer transition-colors hover:border-[#C4975A]/40"
    @click="$router.push({ name: 'RecipeDetail', params: { id: recipe.id } })"
  >
    <!-- Image -->
    <div class="aspect-[4/3] bg-lavender-100 dark:bg-prussian-700 relative overflow-hidden">
      <img
        v-if="recipe.image_path"
        :src="`/storage/${recipe.image_path}`"
        :alt="recipe.title"
        class="w-full h-full object-cover"
      />
      <div v-else class="w-full h-full flex items-center justify-center">
        <FireIcon class="w-12 h-12 text-lavender-300 dark:text-prussian-600" />
      </div>

      <!-- Favorite heart -->
      <button
        class="absolute top-2 right-2 w-8 h-8 rounded-full flex items-center justify-center transition-colors"
        :class="recipe.is_favorite
          ? 'bg-red-500/90 text-white'
          : 'bg-black/30 text-white/80 hover:bg-black/50'"
        aria-label="Toggle favorite"
        @click.stop="$emit('toggleFavorite', recipe.id)"
      >
        <HeartIconSolid v-if="recipe.is_favorite" class="w-4 h-4" />
        <HeartIcon v-else class="w-4 h-4" />
      </button>
    </div>

    <!-- Content -->
    <div class="p-3">
      <!-- Title -->
      <h3 class="text-sm font-semibold text-prussian-500 dark:text-lavender-200 line-clamp-2 leading-tight">
        {{ recipe.title }}
      </h3>

      <!-- Meta row -->
      <div class="flex items-center gap-3 mt-2 text-xs text-lavender-500 dark:text-lavender-400">
        <span v-if="totalTime" class="flex items-center gap-1">
          <ClockIcon class="w-3.5 h-3.5" />
          {{ formatTime(totalTime) }}
        </span>
        <span v-if="recipe.servings" class="flex items-center gap-1">
          <UsersIcon class="w-3.5 h-3.5" />
          {{ recipe.servings }}
        </span>
        <span v-if="recipe.family_average_rating > 0" class="flex items-center gap-1">
          <StarIcon class="w-3.5 h-3.5 text-[#C4975A]" />
          {{ recipe.family_average_rating }}
        </span>
      </div>

      <!-- Tags -->
      <div v-if="recipe.tags?.length" class="flex items-center gap-1 mt-2 flex-wrap">
        <span
          v-for="tag in visibleTags"
          :key="tag.id"
          class="px-2 py-0.5 text-[10px] font-medium rounded-full bg-lavender-100 dark:bg-prussian-700 text-lavender-600 dark:text-lavender-400"
        >
          {{ tag.name }}
        </span>
        <span
          v-if="recipe.tags.length > 3"
          class="text-[10px] text-lavender-400 dark:text-lavender-500"
        >
          +{{ recipe.tags.length - 3 }}
        </span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import {
  ClockIcon,
  UsersIcon,
  StarIcon,
  HeartIcon,
  FireIcon,
} from '@heroicons/vue/24/outline'
import { HeartIcon as HeartIconSolid } from '@heroicons/vue/24/solid'

const props = defineProps({
  recipe: { type: Object, required: true },
})

defineEmits(['toggleFavorite'])

const totalTime = computed(() => {
  if (props.recipe.total_time_minutes) return props.recipe.total_time_minutes
  const prep = props.recipe.prep_time_minutes || 0
  const cook = props.recipe.cook_time_minutes || 0
  return prep + cook || null
})

const visibleTags = computed(() => (props.recipe.tags || []).slice(0, 3))

const formatTime = (minutes) => {
  if (!minutes) return null
  if (minutes < 60) return `${minutes}m`
  const h = Math.floor(minutes / 60)
  const m = minutes % 60
  return m > 0 ? `${h}h ${m}m` : `${h}h`
}
</script>
