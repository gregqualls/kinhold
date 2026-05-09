<template>
  <div class="flex-1 flex flex-col">
    <div class="text-center mb-6">
      <h1 class="text-2xl font-heading font-bold text-ink-primary mb-2">
        Add Your Family
      </h1>
      <p class="text-base text-ink-secondary">
        Add family members now, or share the invite code so they can join later.
      </p>
    </div>

    <!-- Reassurance: optional step (#254) -->
    <KinFlatCard padding="sm" class="mb-4 bg-surface-sunken">
      <p class="text-xs text-ink-secondary leading-relaxed">
        You can do this anytime — many families fill in their calendar, tasks, and recipes first so the rest of the household joins a hub that already feels like home. Skip this step if you'd rather come back to it.
      </p>
    </KinFlatCard>

    <!-- Family members — single source of truth from authStore.family.members
         (#260). Includes both members already on the family at step entry and
         any added during this session, since addMember() calls fetchUser()
         which refreshes the auth store. Excludes the current user. -->
    <div v-if="existingMembers.length > 0" class="mb-4 space-y-2">
      <p class="text-xs font-semibold uppercase tracking-wide text-ink-tertiary">Family members</p>
      <KinFlatCard
        v-for="member in existingMembers"
        :key="member.id"
        padding="sm"
        class="bg-surface-sunken"
      >
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-semibold text-white flex-shrink-0" :style="{ backgroundColor: '#7B6B9C' }">
            {{ member.name.charAt(0).toUpperCase() }}
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-ink-primary">{{ member.name }}</p>
            <p class="text-xs text-ink-secondary">{{ (member.family_role || member.role) === 'parent' ? 'Parent' : 'Child' }}{{ member.is_managed ? ' (managed)' : '' }}</p>
          </div>
          <CheckCircleIcon class="w-5 h-5 text-status-success flex-shrink-0" />
        </div>
      </KinFlatCard>
    </div>

    <!-- Add member form -->
    <div v-if="isParent && !showingInviteCode" class="space-y-3 mb-4">
      <div class="flex gap-2">
        <div class="flex-1">
          <KinInput
            v-model="memberName"
            type="text"
            placeholder="Name"
            @keyup.enter="addMember"
          />
        </div>
        <div class="w-28">
          <KinSelect
            v-model="memberRole"
            :options="[
              { value: 'child', label: 'Child' },
              { value: 'parent', label: 'Parent' },
            ]"
          />
        </div>
      </div>

      <KinInput
        v-model="memberEmail"
        type="email"
        placeholder="Email (optional — leave blank for managed account)"
      />

      <div class="flex gap-2">
        <KinButton
          variant="primary"
          class="flex-1"
          :disabled="!memberName || addingMember"
          :loading="addingMember"
          @click="addMember"
        >
          {{ addingMember ? 'Adding...' : 'Add Member' }}
        </KinButton>
      </div>

      <p v-if="addError" class="text-sm text-status-failed">{{ addError }}</p>
    </div>

    <!-- Toggle to invite code -->
    <div class="mt-auto">
      <KinButton
        variant="ghost"
        class="w-full"
        @click="showingInviteCode = !showingInviteCode"
      >
        {{ showingInviteCode ? 'Add members directly instead' : 'Or share an invite code' }}
      </KinButton>

      <!-- Invite Code -->
      <div v-if="showingInviteCode && inviteCode" class="mt-2">
        <KinFlatCard padding="sm" class="bg-surface-sunken">
          <div class="flex items-center justify-center gap-3">
            <span class="font-mono text-xl tracking-[0.2em] text-ink-primary font-semibold">
              {{ inviteCode }}
            </span>
            <KinButton
              variant="ghost"
              size="sm"
              icon-only
              :aria-label="copied ? 'Copied' : 'Copy invite code'"
              @click="copyCode"
            >
              <ClipboardDocumentCheckIcon v-if="copied" class="w-5 h-5 text-status-success" />
              <ClipboardDocumentIcon v-else class="w-5 h-5" />
            </KinButton>
          </div>
        </KinFlatCard>
        <p v-if="copied" class="text-center text-sm text-status-success mt-2">Copied to clipboard</p>
        <p class="text-xs text-center text-ink-secondary mt-2">
          Family members enter this code when they register.
        </p>
      </div>

      <!-- Non-parent view -->
      <KinFlatCard v-if="!isParent" padding="md" class="text-center mt-2">
        <p class="text-sm text-ink-secondary">
          Ask a parent to add family members or share the invite code.
        </p>
      </KinFlatCard>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, inject } from 'vue'
import { useOnboardingStore } from '@/stores/onboarding'
import { useAuthStore } from '@/stores/auth'
import api from '@/services/api'
import { ClipboardDocumentIcon, ClipboardDocumentCheckIcon, CheckCircleIcon } from '@heroicons/vue/24/outline'
import KinInput from '@/components/design-system/KinInput.vue'
import KinSelect from '@/components/design-system/KinSelect.vue'
import KinButton from '@/components/design-system/KinButton.vue'
import KinFlatCard from '@/components/design-system/KinFlatCard.vue'

const store = useOnboardingStore()
const authStore = useAuthStore()
inject('onboarding')

const isParent = authStore.isParent
const inviteCode = computed(() => store.status?.invite_code || authStore.family?.invite_code)
const copied = ref(false)
const showingInviteCode = ref(false)

// Members already on the family — shown when restarting onboarding so the user
// sees state, not a blank slate (#260). Excludes the current user.
const existingMembers = computed(() => {
  const members = authStore.family?.members || []
  return members.filter(m => m.id !== authStore.user?.id)
})

const memberName = ref('')
const memberEmail = ref('')
const memberRole = ref('child')
const addingMember = ref(false)
const addError = ref('')

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
    memberName.value = ''
    memberEmail.value = ''
    memberRole.value = 'child'
    // Refresh auth store so the new member appears in `existingMembers`.
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
