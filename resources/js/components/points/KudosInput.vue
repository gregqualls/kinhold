<template>
  <div class="flex items-center gap-2">
    <select
      v-model="selectedUser"
      class="input-base text-sm py-2 flex-shrink-0 w-32"
    >
      <option value="">Select...</option>
      <option v-for="member in members" :key="member.id" :value="member.id">
        {{ member.name }}
      </option>
    </select>

    <input
      v-model="reason"
      type="text"
      placeholder="Kudos for..."
      class="input-base text-sm py-2 flex-1"
      @keydown.enter="submit"
    />

    <button
      @click="submit"
      :disabled="!selectedUser || !reason.trim()"
      class="btn-primary btn-sm flex-shrink-0"
    >
      Give Kudos
    </button>
  </div>
</template>

<script setup>
import { ref } from 'vue'

defineProps({
  members: {
    type: Array,
    default: () => [],
  },
})

const emit = defineEmits(['kudos'])

const selectedUser = ref('')
const reason = ref('')

const submit = () => {
  if (!selectedUser.value || !reason.value.trim()) return
  emit('kudos', { userId: selectedUser.value, reason: reason.value.trim() })
  reason.value = ''
}
</script>
