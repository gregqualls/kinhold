<template>
  <div class="space-y-4">
    <!-- Servings adjuster -->
    <div class="flex items-center gap-3">
      <span class="text-sm font-medium text-ink-primary">Servings:</span>
      <div class="flex items-center gap-2">
        <button
          class="w-7 h-7 rounded-full border border-border-subtle flex items-center justify-center text-ink-tertiary hover:border-[#C4975A] hover:text-[#C4975A] transition-colors"
          aria-label="Decrease servings"
          :disabled="localServings <= 1"
          @click="localServings = Math.max(1, localServings - 1)"
        >
          <MinusIcon class="w-3.5 h-3.5" />
        </button>
        <span class="text-sm font-semibold text-ink-primary w-6 text-center">{{ localServings }}</span>
        <button
          class="w-7 h-7 rounded-full border border-border-subtle flex items-center justify-center text-ink-tertiary hover:border-[#C4975A] hover:text-[#C4975A] transition-colors"
          aria-label="Increase servings"
          @click="localServings++"
        >
          <PlusIcon class="w-3.5 h-3.5" />
        </button>
      </div>
      <span
        v-if="localServings !== originalServings"
        class="text-xs text-ink-tertiary"
      >
        (originally {{ originalServings }})
      </span>
    </div>

    <!-- Ingredient groups -->
    <div v-for="(group, groupName) in groupedIngredients" :key="groupName" class="space-y-1">
      <h4
        v-if="groupName !== '__default__'"
        class="text-xs font-semibold uppercase tracking-wider text-ink-tertiary mt-3 mb-1"
      >
        {{ groupName }}
      </h4>
      <div
        v-for="ingredient in group"
        :key="ingredient.id"
        class="flex items-start gap-3 py-1.5 group"
      >
        <!-- Checkbox for cook-mode -->
        <button
          class="mt-0.5 w-5 h-5 rounded-full border-2 flex-shrink-0 flex items-center justify-center transition-colors"
          :class="checkedIngredients.has(ingredient.id)
            ? 'bg-[#C4975A] border-[#C4975A] text-white'
            : 'border-border-subtle hover:border-[#C4975A]'"
          :aria-label="`Toggle ${ingredient.name}`"
          @click="toggleChecked(ingredient.id)"
        >
          <CheckIcon v-if="checkedIngredients.has(ingredient.id)" class="w-3 h-3" />
        </button>

        <!-- Ingredient text -->
        <span
          class="text-sm leading-relaxed transition-colors"
          :class="checkedIngredients.has(ingredient.id)
            ? 'line-through text-ink-tertiary'
            : 'text-ink-primary'"
        >
          <span v-if="scaledQuantity(ingredient)" class="font-medium">{{ scaledQuantity(ingredient) }}</span>
          <span v-if="ingredient.unit"> {{ ingredient.unit }}</span>
          {{ ingredient.name }}
          <span v-if="ingredient.preparation" class="text-ink-tertiary">, {{ ingredient.preparation }}</span>
          <span v-if="ingredient.is_optional" class="text-ink-tertiary italic"> (optional)</span>
        </span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { MinusIcon, PlusIcon, CheckIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  ingredients: { type: Array, default: () => [] },
  originalServings: { type: Number, default: 4 },
})

const localServings = ref(props.originalServings)
const checkedIngredients = ref(new Set())

const toggleChecked = (id) => {
  if (checkedIngredients.value.has(id)) {
    checkedIngredients.value.delete(id)
  } else {
    checkedIngredients.value.add(id)
  }
  // Trigger reactivity
  checkedIngredients.value = new Set(checkedIngredients.value)
}

const groupedIngredients = computed(() => {
  const groups = {}
  for (const ing of props.ingredients) {
    const key = ing.group_name || '__default__'
    if (!groups[key]) groups[key] = []
    groups[key].push(ing)
  }
  return groups
})

const scaledQuantity = (ingredient) => {
  if (!ingredient.quantity) return null
  const qty = parseFloat(ingredient.quantity)
  if (isNaN(qty)) return ingredient.quantity
  const scaled = qty * (localServings.value / props.originalServings)
  // Clean display: remove trailing zeros
  const formatted = scaled.toFixed(2).replace(/\.?0+$/, '')
  return formatted
}
</script>
