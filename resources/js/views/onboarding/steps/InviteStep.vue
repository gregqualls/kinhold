<template>
  <div class="flex-1 flex flex-col">
    <div class="text-center mb-8">
      <h1 class="text-2xl font-heading font-bold text-kin-black dark:text-kin-off-white mb-2">
        Invite Your Family
      </h1>
      <p class="text-base text-kin-gray-500 dark:text-kin-gray-400">
        Share this code so others can join your family during registration.
      </p>
    </div>

    <!-- Invite Code -->
    <div v-if="inviteCode" class="mb-6">
      <div class="flex items-center justify-center gap-3 p-4 bg-kin-cream dark:bg-kin-surface-dark rounded-xl border border-kin-border dark:border-kin-border-dark">
        <span class="font-mono text-2xl tracking-[0.25em] text-kin-black dark:text-kin-off-white font-semibold">
          {{ inviteCode }}
        </span>
        <button
          class="p-2 rounded-lg text-kin-gray-500 hover:text-kin-gold hover:bg-kin-gold/10 transition-colors"
          :aria-label="copied ? 'Copied' : 'Copy invite code'"
          @click="copyCode"
        >
          <ClipboardDocumentCheckIcon v-if="copied" class="w-5 h-5 text-kin-success" />
          <ClipboardDocumentIcon v-else class="w-5 h-5" />
        </button>
      </div>
      <p v-if="copied" class="text-center text-sm text-kin-success mt-2">Copied to clipboard</p>
    </div>

    <!-- Email Invite -->
    <div v-if="isParent" class="space-y-3">
      <p class="text-sm font-medium text-kin-gray-500 dark:text-kin-gray-400">Or send an email invite</p>
      <div class="flex gap-2">
        <input
          v-model="email"
          type="email"
          class="kin-input flex-1"
          placeholder="family@example.com"
          @keyup.enter="sendInvite"
        />
        <button
          class="kin-btn-secondary whitespace-nowrap"
          :disabled="sendingInvite || !email"
          @click="sendInvite"
        >
          {{ sendingInvite ? 'Sending...' : 'Send' }}
        </button>
      </div>
      <p v-if="inviteSent" class="text-sm text-kin-success">Invite sent to {{ lastSentEmail }}</p>
      <p v-if="inviteError" class="text-sm text-kin-error">{{ inviteError }}</p>
    </div>

    <!-- Non-parent message -->
    <div v-else class="kin-card text-center">
      <p class="text-sm text-kin-gray-500 dark:text-kin-gray-400">
        Ask a parent to share the invite code with other family members.
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, inject, onMounted } from 'vue'
import { useOnboardingStore } from '@/stores/onboarding'
import { useAuthStore } from '@/stores/auth'
import api from '@/services/api'
import { ClipboardDocumentIcon, ClipboardDocumentCheckIcon } from '@heroicons/vue/24/outline'

const store = useOnboardingStore()
const authStore = useAuthStore()
inject('onboarding')

const isParent = authStore.isParent
const inviteCode = computed(() => store.status?.invite_code || authStore.family?.invite_code)
const copied = ref(false)
const email = ref('')
const sendingInvite = ref(false)
const inviteSent = ref(false)
const inviteError = ref('')
const lastSentEmail = ref('')

async function copyCode() {
  try {
    await navigator.clipboard.writeText(inviteCode.value)
    copied.value = true
    setTimeout(() => { copied.value = false }, 2000)
  } catch {
    // Fallback for older browsers
  }
}

async function sendInvite() {
  if (!email.value) return
  sendingInvite.value = true
  inviteError.value = ''
  inviteSent.value = false

  try {
    await api.post('/family/invite', { email: email.value })
    lastSentEmail.value = email.value
    inviteSent.value = true
    email.value = ''
  } catch (err) {
    inviteError.value = err.response?.data?.message || 'Failed to send invite.'
  } finally {
    sendingInvite.value = false
  }
}

onMounted(() => {
  // No special init needed
})
</script>
