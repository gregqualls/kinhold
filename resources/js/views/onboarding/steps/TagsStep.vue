<template>
  <div class="flex-1 flex flex-col">
    <div class="text-center mb-6">
      <h1 class="text-2xl font-heading font-bold text-ink-primary mb-2">
        Organize Tasks With Tags
      </h1>
      <p class="text-base text-ink-secondary">
        Tags help you filter and group tasks. Pick some to get started.
      </p>
    </div>

    <!-- How it works -->
    <KinFlatCard padding="sm" class="mb-6 bg-surface-sunken">
      <p class="text-xs text-ink-secondary leading-relaxed">
        Every task can have one or more tags. Use the tag bar at the top of your Tasks page to quickly filter what you're looking at — tap "Groceries" to see your shopping list, "Chores" for household tasks.
      </p>
    </KinFlatCard>

    <div class="grid grid-cols-2 gap-3">
      <button
        v-for="preset in presets"
        :key="preset.name"
        class="p-4 rounded-card border-2 transition-all duration-200 text-left cursor-pointer"
        :class="isSelected(preset.name)
          ? 'border-accent-lavender-bold bg-accent-lavender-soft/40'
          : 'border-border-subtle bg-surface-raised hover:border-border-strong'"
        @click="togglePreset(preset.name)"
      >
        <div class="flex items-center gap-2 mb-1">
          <span
            class="w-2.5 h-2.5 rounded-full flex-shrink-0"
            :style="{ backgroundColor: preset.color }"
          ></span>
          <p class="text-sm font-semibold text-ink-primary">{{ preset.name }}</p>
        </div>
        <p class="text-xs text-ink-secondary">{{ preset.description }}</p>
      </button>
    </div>

    <p v-if="error" class="text-sm text-status-failed text-center mt-4">{{ error }}</p>
  </div>
</template>

<script setup>
import { ref, inject, onMounted } from 'vue'
import { useOnboardingStore } from '@/stores/onboarding'
import api from '@/services/api'
import KinFlatCard from '@/components/design-system/KinFlatCard.vue'

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

// Track tags that already exist on the family so we (a) don't try to create
// duplicates on Continue and (b) show them as selected in the UI when the user
// re-runs onboarding from settings (#260).
const existingTagNames = ref(new Set())

function isSelected(name) {
  return store.selectedPresets.has(name) || existingTagNames.value.has(name)
}

function togglePreset(name) {
  // Tags that already exist on the family are read-only — toggling them off
  // would imply deletion, which onboarding shouldn't do.
  if (existingTagNames.value.has(name)) return

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
      // Skip presets that already exist on the family (re-running onboarding).
      if (existingTagNames.value.has(presetName)) continue
      const preset = presets.find(p => p.name === presetName)
      if (preset) {
        await api.post('/tags', {
          name: preset.name,
          color: preset.color,
          scope: 'task',
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

onMounted(async () => {
  try {
    const res = await api.get('/tags', { params: { scope: 'task' } })
    const tags = res.data?.data || res.data || []
    existingTagNames.value = new Set(tags.map(t => t.name))
  } catch {
    // Non-fatal — leave the existing-tag set empty and let the user start fresh.
  }
})
</script>
