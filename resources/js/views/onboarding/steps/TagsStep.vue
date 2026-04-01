<template>
  <div class="flex-1 flex flex-col">
    <div class="text-center mb-6">
      <h1 class="text-2xl font-heading font-bold text-kin-black dark:text-kin-off-white mb-2">
        Organize With Tags
      </h1>
      <p class="text-base text-kin-gray-500 dark:text-kin-gray-400">
        Tags help you filter and group tasks. Pick some to get started.
      </p>
    </div>

    <!-- How it works -->
    <div class="mb-6 p-4 rounded-xl bg-kin-cream dark:bg-kin-surface-dark border border-kin-border dark:border-kin-border-dark">
      <p class="text-xs text-kin-gray-500 dark:text-kin-gray-400 leading-relaxed">
        Every task can have one or more tags. Use the tag bar at the top of your Tasks page to quickly filter what you're looking at — tap "Groceries" to see your shopping list, "Chores" for household tasks.
      </p>
    </div>

    <div class="grid grid-cols-2 gap-3">
      <button
        v-for="preset in presets"
        :key="preset.name"
        class="p-4 rounded-xl border-2 transition-all duration-200 text-left cursor-pointer"
        :class="isSelected(preset.name)
          ? 'border-kin-gold bg-kin-gold/5 dark:bg-kin-gold/10'
          : 'border-kin-border dark:border-kin-border-dark bg-white dark:bg-kin-surface-dark hover:border-kin-gray-300 dark:hover:border-kin-gray-600'"
        @click="togglePreset(preset.name)"
      >
        <div class="flex items-center gap-2 mb-1">
          <span
            class="w-2.5 h-2.5 rounded-full flex-shrink-0"
            :style="{ backgroundColor: preset.color }"
          ></span>
          <p class="text-sm font-semibold text-kin-black dark:text-kin-off-white">{{ preset.name }}</p>
        </div>
        <p class="text-xs text-kin-gray-500 dark:text-kin-gray-400">{{ preset.description }}</p>
      </button>
    </div>

    <p v-if="error" class="text-sm text-kin-error text-center mt-4">{{ error }}</p>
  </div>
</template>

<script setup>
import { ref, inject } from 'vue'
import { useOnboardingStore } from '@/stores/onboarding'
import api from '@/services/api'

const store = useOnboardingStore()
const { setStepLoading, registerContinue } = inject('onboarding')
const error = ref('')

const presets = [
  { name: 'Groceries', color: '#5B8C6A', description: 'Weekly shopping items' },
  { name: 'Chores', color: '#C48B3F', description: 'Household tasks' },
  { name: 'School', color: '#5B7B9C', description: 'Homework and events' },
  { name: 'House', color: '#C45B5B', description: 'Repairs and projects' },
  { name: 'Meals', color: '#C4975A', description: 'Meal planning and prep' },
  { name: 'Errands', color: '#7B6B9C', description: 'Things to do outside' },
]

function isSelected(name) {
  return store.selectedPresets.has(name)
}

function togglePreset(name) {
  const updated = new Set(store.selectedPresets)
  if (updated.has(name)) {
    updated.delete(name)
  } else {
    updated.add(name)
  }
  store.selectedPresets = updated
}

registerContinue(async () => {
  if (store.selectedPresets.size === 0) return true

  setStepLoading(true)
  error.value = ''
  try {
    for (const presetName of store.selectedPresets) {
      const preset = presets.find(p => p.name === presetName)
      if (preset) {
        await api.post('/tags', {
          name: preset.name,
          color: preset.color,
        })
      }
    }
    return true
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to create tags.'
    return false
  } finally {
    setStepLoading(false)
  }
})
</script>
