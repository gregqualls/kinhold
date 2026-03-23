<template>
  <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50" @click="$emit('close')"></div>
    <div class="card p-6 w-full max-w-sm relative z-10">
      <h3 class="text-lg font-bold font-heading text-prussian-500 dark:text-lavender-200 mb-4">Request Points</h3>

      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-prussian-400 dark:text-lavender-400 mb-1">Points</label>
          <input v-model.number="points" type="number" min="1" max="1000" class="input-base w-full" placeholder="10" />
        </div>

        <div>
          <label class="block text-sm font-medium text-prussian-400 dark:text-lavender-400 mb-1">Reason</label>
          <input v-model="reason" type="text" class="input-base w-full" placeholder="Why do you deserve points?" />
        </div>
      </div>

      <div class="flex gap-3 mt-6">
        <button @click="$emit('close')" class="btn-ghost flex-1">Cancel</button>
        <button
          @click="submit"
          :disabled="!points || !reason.trim()"
          class="btn-primary flex-1"
        >
          Request
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

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
