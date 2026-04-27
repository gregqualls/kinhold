<template>
  <div class="h-full flex flex-col">
    <!-- Header -->
    <div class="px-4 pt-4 pb-2 md:px-6 md:pt-6">
      <h1 class="text-2xl font-bold font-heading text-ink-primary">Meals</h1>
      <p class="text-sm text-ink-tertiary mt-0.5">Recipes, restaurants &amp; meal planning</p>
    </div>

    <!-- Tab bar -->
    <div class="px-4 md:px-6 mt-2">
      <KinTabPillGroup
        v-model:active-key="activeTab"
        :tabs="tabs"
        variant="underline"
        size="md"
      />
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
import KinTabPillGroup from '@/components/design-system/KinTabPillGroup.vue'

const tabs = [
  { key: 'meals',       label: 'Plans' },
  { key: 'recipes',     label: 'Recipes' },
  { key: 'restaurants', label: 'Restaurants' },
]

const activeTab = ref('meals')
</script>
