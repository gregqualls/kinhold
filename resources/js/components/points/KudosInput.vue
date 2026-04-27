<template>
  <div class="flex items-center gap-2">
    <div class="flex-shrink-0 w-32">
      <KinSelect
        v-model="selectedUser"
        placeholder="Select..."
        :options="memberOptions"
      />
    </div>

    <div class="flex-1">
      <KinInput
        v-model="reason"
        type="text"
        placeholder="Kudos for..."
        @keydown.enter="submit"
      />
    </div>

    <KinButton
      variant="primary"
      size="sm"
      :disabled="!selectedUser || !reason.trim()"
      class="flex-shrink-0"
      @click="submit"
    >
      Give Kudos
    </KinButton>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useAuthStore } from '@/stores/auth'
import KinInput from '@/components/design-system/KinInput.vue'
import KinSelect from '@/components/design-system/KinSelect.vue'
import KinButton from '@/components/design-system/KinButton.vue'

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

const memberOptions = computed(() =>
  otherMembers.value.map((m) => ({ value: m.id, label: m.name }))
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
