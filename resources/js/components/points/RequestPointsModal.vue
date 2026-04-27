<template>
  <KinModalSheet :model-value="show" title="Request Points" size="sm" @close="$emit('close')">
    <div class="space-y-4">
      <KinInput
        v-model.number="points"
        type="number"
        label="Points"
        placeholder="10"
      />

      <KinInput
        v-model="reason"
        type="text"
        label="Reason"
        placeholder="Why do you deserve points?"
      />
    </div>

    <template #actions>
      <div class="flex justify-end gap-2">
        <KinButton variant="ghost" @click="$emit('close')">Cancel</KinButton>
        <KinButton
          variant="primary"
          :disabled="!points || !reason.trim()"
          @click="submit"
        >
          Request
        </KinButton>
      </div>
    </template>
  </KinModalSheet>
</template>

<script setup>
import { ref } from 'vue'
import KinModalSheet from '@/components/design-system/KinModalSheet.vue'
import KinInput from '@/components/design-system/KinInput.vue'
import KinButton from '@/components/design-system/KinButton.vue'

defineProps({
  show: Boolean,
})

const emit = defineEmits(['close', 'submit'])

const points = ref(null)
const reason = ref('')

const submit = () => {
  if (!points.value || !reason.value.trim()) return
  emit('submit', {
    points: points.value,
    reason: reason.value.trim(),
  })
  points.value = null
  reason.value = ''
}
</script>
