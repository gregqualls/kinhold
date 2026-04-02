<template>
  <BaseModal :show="show" title="Try the Demo" size="lg" @close="$emit('close')">
    <p class="text-sm kin-muted mb-5">
      Meet the Johnsons! Pick a family member to explore Kinhold from their perspective.
    </p>

    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
      <button
        v-for="member in members"
        :key="member.key"
        :disabled="loadingMember !== null"
        class="relative flex flex-col items-center gap-2 p-4 rounded-xl border border-lavender-200 dark:border-prussian-700 hover:border-wisteria-400 dark:hover:border-wisteria-500 hover:bg-lavender-50 dark:hover:bg-prussian-750 transition-all text-left disabled:opacity-50 disabled:cursor-not-allowed"
        @click="handleSelect(member.key)"
      >
        <!-- Loading overlay -->
        <div v-if="loadingMember === member.key" class="absolute inset-0 flex items-center justify-center bg-white/60 dark:bg-prussian-800/60 rounded-xl">
          <LoadingSpinner size="sm" />
        </div>

        <!-- Avatar circle -->
        <div
          class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-lg"
          :style="{ backgroundColor: member.color }"
        >
          {{ member.name[0] }}
        </div>

        <!-- Name & role -->
        <div class="text-center">
          <div class="font-semibold text-sm text-prussian-500 dark:text-lavender-200">{{ member.name }}</div>
          <span
            class="inline-block mt-1 text-xs px-2 py-0.5 rounded-full"
            :class="member.role === 'Parent' ? 'bg-wisteria-100 text-wisteria-700 dark:bg-wisteria-900/30 dark:text-wisteria-300' : 'bg-sand-100 text-sand-700 dark:bg-sand-900/30 dark:text-sand-300'"
          >
            {{ member.role }}
          </span>
          <div class="text-xs kin-muted mt-1">{{ member.description }}</div>
        </div>
      </button>
    </div>

    <p v-if="errorMsg" class="mt-4 text-sm text-red-600 dark:text-red-400 text-center">{{ errorMsg }}</p>
  </BaseModal>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import BaseModal from '@/components/common/BaseModal.vue'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'

defineProps({
  show: Boolean,
})

const emit = defineEmits(['close'])

const router = useRouter()
const authStore = useAuthStore()

const loadingMember = ref(null)
const errorMsg = ref('')

const members = [
  { key: 'mike', name: 'Mike', role: 'Parent', description: 'Dad', color: '#5B8C9C' },
  { key: 'sarah', name: 'Sarah', role: 'Parent', description: 'Mom', color: '#8B5B9C' },
  { key: 'emma', name: 'Emma', role: 'Teen', description: 'Age 16', color: '#9C5B7B' },
  { key: 'jake', name: 'Jake', role: 'Kid', description: 'Age 13', color: '#5B6B9C' },
  { key: 'lily', name: 'Lily', role: 'Kid', description: 'Age 9', color: '#5B9C7B' },
]

const handleSelect = async (key) => {
  loadingMember.value = key
  errorMsg.value = ''

  const result = await authStore.demoLogin(key)

  if (result.success) {
    emit('close')
    router.push({ name: 'Dashboard' })
  } else {
    errorMsg.value = result.error || 'Something went wrong. Please try again.'
  }

  loadingMember.value = null
}
</script>
