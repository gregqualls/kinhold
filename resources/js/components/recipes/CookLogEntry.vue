<template>
  <KinModalSheet
    :model-value="show"
    title="Log a Cook"
    size="sm"
    @update:model-value="(v) => !v && $emit('close')"
  >
    <form class="space-y-4" @submit.prevent="handleSubmit">
      <KinInput
        v-model="form.cooked_at"
        type="date"
        label="Date"
        required
      />

      <KinInput
        v-model.number="form.servings_made"
        type="number"
        label="Servings made"
        :min="1"
        placeholder="Optional"
      />

      <KinTextarea
        v-model="form.notes"
        label="Notes"
        :rows="3"
        placeholder="How did it turn out? Any changes you made?"
      />
    </form>

    <template #actions>
      <KinButton variant="secondary" @click="$emit('close')">Cancel</KinButton>
      <KinButton variant="primary" :loading="saving" @click="handleSubmit">
        {{ saving ? 'Saving...' : 'Save' }}
      </KinButton>
    </template>
  </KinModalSheet>
</template>

<script setup>
import { ref, reactive } from 'vue'
import KinModalSheet from '@/components/design-system/KinModalSheet.vue'
import KinInput from '@/components/design-system/KinInput.vue'
import KinTextarea from '@/components/design-system/KinTextarea.vue'
import KinButton from '@/components/design-system/KinButton.vue'

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
