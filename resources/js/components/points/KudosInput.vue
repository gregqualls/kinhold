<template>
  <div class="flex items-center gap-2">
    <select
      v-model="selectedUser"
      class="input-base text-sm py-2 flex-shrink-0 w-32"
    >
      <option value="">Select...</option>
      <option v-for="member in otherMembers" :key="member.id" :value="member.id">
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
import { ref, computed } from 'vue'
import { useAuthStore } from '@/stores/auth'

const props = defineProps({
  members: {
    type: Array,
    default: () => [],
  },
})

const authStore = useAuthStore()

// Filter out the current user so they can't give kudos to themselves
const otherMembers = computed(() =>
  props.members.filter(m => m.id !== authStore.currentUser?.id)
)

const emit = defineEmits(['kudos'])

const selectedUser = ref('')
const reason = ref('')

const submit = () => {
  if (!selectedUser.value || !reason.value.trim()) return
  emit('kudos', { userId: selectedUser.value, reason: reason.value.trim() })
  reason.value = ''
}
</script>
