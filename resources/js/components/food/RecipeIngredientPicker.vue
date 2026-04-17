<template>
  <div>
    <!-- Header: title + select-all + counter -->
    <div class="flex items-center justify-between mb-2 gap-3">
      <div class="flex items-center gap-2 min-w-0">
        <button
          v-if="collapsible"
          class="p-0.5 rounded text-lavender-400 hover:text-prussian-500 dark:hover:text-lavender-200 transition-colors"
          :aria-label="expanded ? 'Collapse' : 'Expand'"
          @click="expanded = !expanded"
        >
          <ChevronDownIcon class="w-4 h-4 transition-transform" :class="{ '-rotate-90': !expanded }" />
        </button>
        <h4 v-if="title" class="text-sm font-medium text-prussian-500 dark:text-lavender-200 truncate">
          {{ title }}
        </h4>
        <span v-if="subtitle" class="text-xs text-lavender-400 dark:text-lavender-500 flex-shrink-0">
          {{ subtitle }}
        </span>
        <span
          v-if="alreadyOnListCount > 0"
          class="flex-shrink-0 inline-flex items-center gap-1 px-1.5 py-0.5 text-[10px] font-medium rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300"
          :title="`${alreadyOnListCount} already on list`"
        >
          <CheckCircleIcon class="w-3 h-3" />
          {{ alreadyOnListCount }}
        </span>
      </div>
      <div class="flex items-center gap-2 flex-shrink-0">
        <span class="text-xs text-lavender-400 dark:text-lavender-500">
          {{ selectedIds.length }}/{{ ingredients.length }}
        </span>
        <button
          class="text-xs text-[#C4975A] hover:underline"
          @click="toggleAll"
        >
          {{ allSelected ? 'None' : 'All' }}
        </button>
      </div>
    </div>

    <!-- Ingredient list -->
    <div v-if="!collapsible || expanded" class="space-y-0.5">
      <p v-if="ingredients.length === 0" class="text-xs text-lavender-400 dark:text-lavender-500 italic px-3 py-2">
        No ingredients on this recipe.
      </p>
      <label
        v-for="ingredient in ingredients"
        :key="ingredient.id"
        class="flex items-center gap-3 px-3 py-2 rounded-lg cursor-pointer transition-colors"
        :class="rowClass(ingredient)"
      >
        <input
          type="checkbox"
          :checked="isSelected(ingredient.id)"
          class="w-4 h-4 rounded border-lavender-300 dark:border-prussian-600 text-[#C4975A] focus:ring-[#C4975A]"
          @change="toggle(ingredient.id)"
        />
        <div class="flex-1 min-w-0 flex items-center gap-2">
          <div class="min-w-0 flex-1">
            <span class="text-sm" :class="ingredient.already_on_list ? 'text-lavender-400 dark:text-lavender-500 line-through decoration-lavender-300 dark:decoration-prussian-500' : 'text-prussian-500 dark:text-lavender-200'">
              {{ ingredient.name }}
            </span>
            <span v-if="ingredient.quantity || ingredient.unit" class="text-xs text-lavender-400 dark:text-lavender-500 ml-1.5">
              {{ [ingredient.quantity, ingredient.unit].filter(Boolean).join(' ') }}
            </span>
            <span v-if="ingredient.is_optional" class="text-xs text-lavender-400 dark:text-lavender-500 ml-1.5 italic">
              (optional)
            </span>
          </div>
          <span
            v-if="ingredient.already_on_list"
            class="flex-shrink-0 text-[10px] font-medium px-1.5 py-0.5 rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300"
          >
            On list
          </span>
        </div>
      </label>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue'
import { ChevronDownIcon, CheckCircleIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  ingredients: { type: Array, default: () => [] },
  modelValue: { type: Array, default: () => [] },
  title: { type: String, default: '' },
  subtitle: { type: String, default: '' },
  collapsible: { type: Boolean, default: false },
  defaultExpanded: { type: Boolean, default: true },
  // When true, sets modelValue to all ingredient IDs whenever the ingredient list changes
  // (excluding any already_on_list items so we don't double-add by default).
  defaultSelectAll: { type: Boolean, default: false },
})

const emit = defineEmits(['update:modelValue'])

const expanded = ref(props.defaultExpanded)
const selectedIds = computed(() => props.modelValue)

const isSelected = (id) => selectedIds.value.includes(id)

const allSelected = computed(() =>
  props.ingredients.length > 0 && selectedIds.value.length === props.ingredients.length
)

const alreadyOnListCount = computed(() =>
  props.ingredients.filter(i => i.already_on_list).length
)

const rowClass = (ingredient) => {
  if (ingredient.already_on_list) {
    return 'opacity-70 hover:opacity-100'
  }
  return isSelected(ingredient.id)
    ? 'bg-[#C4975A]/5 dark:bg-[#C4975A]/10'
    : 'hover:bg-lavender-50 dark:hover:bg-prussian-700'
}

const toggle = (id) => {
  const next = [...selectedIds.value]
  const idx = next.indexOf(id)
  if (idx === -1) next.push(id)
  else next.splice(idx, 1)
  emit('update:modelValue', next)
}

const toggleAll = () => {
  if (allSelected.value) {
    emit('update:modelValue', [])
  } else {
    // Select all — including already-on-list (the user explicitly opted in).
    emit('update:modelValue', props.ingredients.map(i => i.id))
  }
}

if (props.defaultSelectAll) {
  watch(() => props.ingredients, (list) => {
    if (list.length === 0) return
    // Default: select everything that isn't already on the list. The user can
    // still manually re-add an already-listed item by checking the box.
    const next = list.filter(i => !i.already_on_list).map(i => i.id)
    emit('update:modelValue', next)
  }, { immediate: true })
}
</script>
