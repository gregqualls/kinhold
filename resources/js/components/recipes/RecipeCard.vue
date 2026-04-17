<template>
  <FoodCard
    :title="recipe.title"
    :image-url="recipe.image_path ? `/storage/${recipe.image_path}` : null"
    :placeholder-icon="FireIcon"
    :is-favorite="!!recipe.is_favorite"
    :meta-items="metaItems"
    :tags="recipe.tags || []"
    @click="$router.push({ name: 'RecipeDetail', params: { id: recipe.id } })"
    @toggle-favorite="$emit('toggleFavorite', recipe.id)"
  />
</template>

<script setup>
import { computed } from 'vue'
import {
  ClockIcon,
  UsersIcon,
  StarIcon,
  FireIcon,
} from '@heroicons/vue/24/outline'
import FoodCard from '@/components/food/FoodCard.vue'

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

const formatTime = (minutes) => {
  if (!minutes) return null
  if (minutes < 60) return `${minutes}m`
  const h = Math.floor(minutes / 60)
  const m = minutes % 60
  return m > 0 ? `${h}h ${m}m` : `${h}h`
}

const metaItems = computed(() => {
  const items = []
  if (totalTime.value) items.push({ icon: ClockIcon, text: formatTime(totalTime.value) })
  if (props.recipe.servings) items.push({ icon: UsersIcon, text: String(props.recipe.servings) })
  if (props.recipe.family_average_rating > 0) items.push({ icon: StarIcon, text: String(props.recipe.family_average_rating), iconClass: 'text-[#C4975A]' })
  return items
})
</script>
