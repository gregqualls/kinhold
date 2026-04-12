<template>
  <BaseModal :show="show" title="Log a Cook" size="sm" @close="$emit('close')">
    <form class="space-y-4" @submit.prevent="handleSubmit">
      <!-- Date -->
      <div>
        <label class="block text-sm font-medium text-prussian-500 dark:text-lavender-200 mb-1">Date</label>
        <input
          v-model="form.cooked_at"
          type="date"
          required
          class="w-full px-4 py-2.5 text-sm bg-lavender-50 dark:bg-prussian-700 border border-lavender-200 dark:border-prussian-600 rounded-[10px] text-prussian-500 dark:text-lavender-200 focus:outline-none focus:ring-1 focus:ring-[#C4975A]/30 focus:border-[#C4975A]"
        />
      </div>

      <!-- Servings made -->
      <div>
        <label class="block text-sm font-medium text-prussian-500 dark:text-lavender-200 mb-1">Servings made</label>
        <input
          v-model.number="form.servings_made"
          type="number"
          min="1"
          placeholder="Optional"
          class="w-full px-4 py-2.5 text-sm bg-lavender-50 dark:bg-prussian-700 border border-lavender-200 dark:border-prussian-600 rounded-[10px] text-prussian-500 dark:text-lavender-200 placeholder-lavender-400 focus:outline-none focus:ring-1 focus:ring-[#C4975A]/30 focus:border-[#C4975A]"
        />
      </div>

      <!-- Notes -->
      <div>
        <label class="block text-sm font-medium text-prussian-500 dark:text-lavender-200 mb-1">Notes</label>
        <textarea
          v-model="form.notes"
          rows="3"
          placeholder="How did it turn out? Any changes you made?"
          class="w-full px-4 py-2.5 text-sm bg-lavender-50 dark:bg-prussian-700 border border-lavender-200 dark:border-prussian-600 rounded-[10px] text-prussian-500 dark:text-lavender-200 placeholder-lavender-400 focus:outline-none focus:ring-1 focus:ring-[#C4975A]/30 focus:border-[#C4975A] resize-none"
        ></textarea>
      </div>
    </form>

    <template #footer>
      <button
        class="px-4 py-2.5 text-sm font-medium text-prussian-500 dark:text-lavender-200 bg-lavender-100 dark:bg-prussian-700 hover:bg-lavender-200 dark:hover:bg-prussian-600 rounded-[10px] transition-colors"
        @click="$emit('close')"
      >
        Cancel
      </button>
      <button
        class="px-4 py-2.5 text-sm font-medium text-white bg-[#C4975A] hover:bg-[#D4A96A] rounded-[10px] transition-colors"
        :disabled="saving"
        @click="handleSubmit"
      >
        {{ saving ? 'Saving...' : 'Save' }}
      </button>
    </template>
  </BaseModal>
</template>

<script setup>
import { ref, reactive } from 'vue'
import BaseModal from '@/components/common/BaseModal.vue'

defineProps({
  show: { type: Boolean, default: false },
  recipeId: { type: String, required: true },
})

const emit = defineEmits(['saved', 'close'])

const today = new Date().toISOString().split('T')[0]
const saving = ref(false)

const form = reactive({
  cooked_at: today,
  servings_made: null,
  notes: '',
})

const handleSubmit = () => {
  if (!form.cooked_at) return
  saving.value = true
  emit('saved', { ...form })
}

const resetForm = () => {
  form.cooked_at = today
  form.servings_made = null
  form.notes = ''
  saving.value = false
}

defineExpose({ resetForm })
</script>
