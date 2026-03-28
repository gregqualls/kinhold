<template>
  <div class="flex-1 flex flex-col">
    <div class="text-center mb-6">
      <h1 class="text-2xl font-heading font-bold text-kin-black dark:text-kin-off-white mb-2">
        Add Your Family
      </h1>
      <p class="text-base text-kin-gray-500 dark:text-kin-gray-400">
        Add family members now, or share the invite code so they can join later.
      </p>
    </div>

    <!-- Added members list -->
    <div v-if="addedMembers.length > 0" class="mb-4 space-y-2">
      <div
        v-for="member in addedMembers"
        :key="member.name"
        class="flex items-center gap-3 px-4 py-3 rounded-xl bg-kin-cream dark:bg-kin-surface-dark border border-kin-border dark:border-kin-border-dark"
      >
        <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-semibold text-white flex-shrink-0" :style="{ backgroundColor: '#C4975A' }">
          {{ member.name.charAt(0).toUpperCase() }}
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-sm font-medium text-kin-black dark:text-kin-off-white">{{ member.name }}</p>
          <p class="text-xs text-kin-gray-500 dark:text-kin-gray-400">{{ member.role === 'parent' ? 'Parent' : 'Child' }}{{ member.managed ? ' (managed)' : '' }}</p>
        </div>
        <CheckCircleIcon class="w-5 h-5 text-kin-success flex-shrink-0" />
      </div>
    </div>

    <!-- Add member form -->
    <div v-if="isParent && !showingInviteCode" class="space-y-3 mb-4">
      <div class="flex gap-2">
        <input
          v-model="memberName"
          type="text"
          class="kin-input flex-1"
          placeholder="Name"
          @keyup.enter="addMember"
        />
        <select v-model="memberRole" class="kin-input w-28">
          <option value="child">Child</option>
          <option value="parent">Parent</option>
        </select>
      </div>

      <input
        v-model="memberEmail"
        type="email"
        class="kin-input"
        placeholder="Email (optional — leave blank for managed account)"
      />

      <div class="flex gap-2">
        <button
          class="kin-btn-primary flex-1"
          :disabled="!memberName || addingMember"
          @click="addMember"
        >
          {{ addingMember ? 'Adding...' : 'Add Member' }}
        </button>
      </div>

      <p v-if="addError" class="text-sm text-kin-error">{{ addError }}</p>
    </div>

    <!-- Toggle to invite code -->
    <div class="mt-auto">
      <button
        class="w-full text-center text-sm text-kin-gray-500 dark:text-kin-gray-400 hover:text-kin-gold transition-colors py-2"
        @click="showingInviteCode = !showingInviteCode"
      >
        {{ showingInviteCode ? 'Add members directly instead' : 'Or share an invite code' }}
      </button>

      <!-- Invite Code -->
      <div v-if="showingInviteCode && inviteCode" class="mt-2">
        <div class="flex items-center justify-center gap-3 p-4 bg-kin-cream dark:bg-kin-surface-dark rounded-xl border border-kin-border dark:border-kin-border-dark">
          <span class="font-mono text-xl tracking-[0.2em] text-kin-black dark:text-kin-off-white font-semibold">
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
        <p class="text-xs text-center text-kin-gray-500 dark:text-kin-gray-400 mt-2">
          Family members enter this code when they register.
        </p>
      </div>

      <!-- Non-parent view -->
      <div v-if="!isParent" class="kin-card text-center mt-2">
        <p class="text-sm text-kin-gray-500 dark:text-kin-gray-400">
          Ask a parent to add family members or share the invite code.
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, inject } from 'vue'
import { useOnboardingStore } from '@/stores/onboarding'
import { useAuthStore } from '@/stores/auth'
import api from '@/services/api'
import { ClipboardDocumentIcon, ClipboardDocumentCheckIcon, CheckCircleIcon } from '@heroicons/vue/24/outline'

const store = useOnboardingStore()
const authStore = useAuthStore()
inject('onboarding')

const isParent = authStore.isParent
const inviteCode = computed(() => store.status?.invite_code || authStore.family?.invite_code)
const copied = ref(false)
const showingInviteCode = ref(false)

const memberName = ref('')
const memberEmail = ref('')
const memberRole = ref('child')
const addingMember = ref(false)
const addError = ref('')
const addedMembers = reactive([])

async function addMember() {
  if (!memberName.value) return
  addingMember.value = true
  addError.value = ''

  try {
    await api.post('/family/members', {
      name: memberName.value,
      email: memberEmail.value || undefined,
      role: memberRole.value,
    })
    addedMembers.push({
      name: memberName.value,
      role: memberRole.value,
      managed: !memberEmail.value,
    })
    memberName.value = ''
    memberEmail.value = ''
    memberRole.value = 'child'
    // Refresh auth store to get updated family members
    await authStore.fetchUser()
  } catch (err) {
    addError.value = err.response?.data?.message || 'Failed to add member.'
  } finally {
    addingMember.value = false
  }
}

async function copyCode() {
  try {
    await navigator.clipboard.writeText(inviteCode.value)
    copied.value = true
    setTimeout(() => { copied.value = false }, 2000)
  } catch {
    // Fallback for older browsers
  }
}
</script>
