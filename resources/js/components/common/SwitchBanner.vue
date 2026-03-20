<template>
  <div
    v-if="isSwitchedSession"
    class="bg-wisteria-600 text-white px-4 py-2 flex items-center justify-between text-sm z-50"
  >
    <div class="flex items-center gap-2">
      <ArrowsRightLeftIcon class="w-4 h-4" />
      <span>Viewing as <strong>{{ currentUser?.name }}</strong></span>
    </div>
    <button
      @click="showSwitchBackModal = true"
      class="px-3 py-1 bg-white/20 hover:bg-white/30 rounded-lg font-medium transition-colors"
    >
      Switch Back
    </button>

    <!-- Switch Back Modal -->
    <BaseModal
      :show="showSwitchBackModal"
      title="Switch Back to Parent"
      @close="showSwitchBackModal = false"
    >
      <p class="text-sm text-prussian-500 dark:text-lavender-300 mb-4">
        Enter your password to switch back to <strong>{{ switchedFrom?.name }}</strong>.
      </p>
      <form @submit.prevent="handleSwitchBack">
        <BaseInput
          v-model="password"
          label="Parent Password"
          type="password"
          placeholder="Enter password"
          :error="errorMsg"
        />

        <div class="flex gap-2 justify-end pt-4">
          <BaseButton variant="ghost" @click="showSwitchBackModal = false">
            Cancel
          </BaseButton>
          <BaseButton variant="primary" :loading="loading">
            Switch Back
          </BaseButton>
        </div>
      </form>
    </BaseModal>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { storeToRefs } from 'pinia'
import { useAuthStore } from '@/stores/auth'
import { useNotification } from '@/composables/useNotification'
import BaseModal from '@/components/common/BaseModal.vue'
import BaseInput from '@/components/common/BaseInput.vue'
import BaseButton from '@/components/common/BaseButton.vue'
import { ArrowsRightLeftIcon } from '@heroicons/vue/24/outline'

const router = useRouter()
const authStore = useAuthStore()
const { success, error: notificationError } = useNotification()
const { currentUser, switchedFrom, isSwitchedSession } = storeToRefs(authStore)

const showSwitchBackModal = ref(false)
const password = ref('')
const errorMsg = ref('')
const loading = ref(false)

const handleSwitchBack = async () => {
  errorMsg.value = ''
  if (!password.value) {
    errorMsg.value = 'Password is required'
    return
  }
  loading.value = true
  const result = await authStore.switchBack(password.value)
  if (result.success) {
    success(result.message)
    showSwitchBackModal.value = false
    password.value = ''
    router.push('/')
  } else {
    errorMsg.value = result.error || 'Invalid password'
  }
  loading.value = false
}
</script>
