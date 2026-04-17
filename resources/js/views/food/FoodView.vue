<template>
  <div class="h-full flex flex-col">
    <!-- Header -->
    <div class="px-4 pt-4 pb-2 md:px-6 md:pt-6">
      <h1 class="text-2xl font-bold font-heading text-prussian-500 dark:text-lavender-200">Meals</h1>
      <p class="text-sm text-lavender-500 dark:text-lavender-400 mt-0.5">Recipes, restaurants &amp; meal planning</p>
    </div>

    <!-- Tab bar -->
    <div class="px-4 md:px-6">
      <div class="flex gap-1 border-b border-lavender-200 dark:border-prussian-700">
        <button
          v-for="tab in tabs"
          :key="tab.key"
          class="px-4 py-2.5 text-sm font-medium transition-colors relative"
          :class="activeTab === tab.key
            ? 'text-[#C4975A]'
            : 'text-lavender-500 dark:text-lavender-400 hover:text-prussian-500 dark:hover:text-lavender-200'"
          @click="activeTab = tab.key"
        >
          {{ tab.label }}
          <span
            v-if="activeTab === tab.key"
            class="absolute bottom-0 left-0 right-0 h-0.5 bg-[#C4975A] rounded-full"
          ></span>
        </button>
      </div>
    </div>

    <!-- Tab content -->
    <div class="flex-1 overflow-hidden">
      <RecipesTab v-if="activeTab === 'recipes'" />
      <RestaurantsTab v-else-if="activeTab === 'restaurants'" />
      <MealsTab v-else-if="activeTab === 'meals'" />
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import RecipesTab from './RecipesTab.vue'
import RestaurantsTab from './RestaurantsTab.vue'
import MealsTab from './MealsTab.vue'

const tabs = [
  { key: 'meals', label: 'Plans' },
  { key: 'recipes', label: 'Recipes' },
  { key: 'restaurants', label: 'Restaurants' },
]

const activeTab = ref('meals')
</script>
