<template>
  <div class="p-4 md:p-6 max-w-4xl">
    <!-- Header -->
    <h1 class="text-2xl font-bold text-prussian-500 dark:text-lavender-200 mb-6">Family Settings</h1>

    <!-- Switched Session Notice (visible when viewing as a child) -->
    <div v-if="isSwitchedSession" class="card-lg mb-6 border-2 border-wisteria-300 dark:border-wisteria-700">
      <div class="flex items-center gap-3 mb-3">
        <ArrowsRightLeftIcon class="w-5 h-5 text-wisteria-600" />
        <h2 class="text-lg font-semibold text-wisteria-600 dark:text-wisteria-400">Switched Session</h2>
      </div>
      <p class="text-sm text-prussian-500 dark:text-lavender-300 mb-4">
        You're currently viewing as <strong>{{ currentUser?.name }}</strong>.
        To switch back to your parent account, enter your password below.
      </p>
      <form @submit.prevent="handleSwitchBack" class="space-y-3">
        <BaseInput
          v-model="switchBackPassword"
          label="Parent Password"
          type="password"
          placeholder="Enter parent password"
          :error="switchBackError"
        />
        <div class="flex justify-end">
          <BaseButton variant="primary" :loading="switchingBack">
            Switch Back to {{ switchedFrom?.name }}
          </BaseButton>
        </div>
      </form>
    </div>

    <!-- Family Settings -->
    <div class="card-lg mb-6">
      <h2 class="text-lg font-semibold text-prussian-500 dark:text-lavender-200 mb-4">Family Information</h2>

      <form @submit.prevent="updateFamily" class="space-y-4">
        <BaseInput
          v-model="familyForm.name"
          label="Family Name"
          placeholder="The Johnsons"
          :error="familyErrors.name"
        />

        <div class="flex gap-3 justify-end">
          <BaseButton variant="ghost" @click="cancelEditFamily">
            Cancel
          </BaseButton>
          <BaseButton variant="primary" :loading="savingFamily">
            Save Changes
          </BaseButton>
        </div>
      </form>
    </div>

    <!-- Invite Code (parent only) -->
    <div v-if="isParent" class="card-lg mb-6">
      <h2 class="text-lg font-semibold text-prussian-500 dark:text-lavender-200 mb-2">Family Invite Code</h2>
      <p class="text-sm text-lavender-700 dark:text-lavender-400 mb-4">
        Share this code with family members so they can join during registration.
      </p>

      <div class="flex items-center gap-3">
        <div class="flex-1 px-4 py-3 bg-lavender-50 dark:bg-prussian-700 rounded-lg font-mono text-lg tracking-widest text-prussian-500 dark:text-lavender-200 text-center">
          {{ inviteCode || '...' }}
        </div>
        <BaseButton variant="secondary" size="sm" @click="copyInviteCode" :disabled="!inviteCode">
          <ClipboardDocumentIcon class="w-4 h-4 mr-1" />
          {{ copied ? 'Copied!' : 'Copy' }}
        </BaseButton>
      </div>
    </div>

    <!-- Family Members -->
    <div class="card-lg mb-6">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-prussian-500 dark:text-lavender-200">Family Members</h2>
        <BaseButton v-if="isParent" variant="secondary" size="sm" @click="openAddMemberModal">
          <PlusIcon class="w-4 h-4 mr-2" />
          Add Member
        </BaseButton>
      </div>

      <!-- Members List -->
      <div class="space-y-3">
        <div
          v-for="member in familyMembers"
          :key="member.id"
          class="flex items-center justify-between p-4 bg-lavender-50 dark:bg-prussian-700 rounded-lg"
        >
          <div class="flex items-center gap-3">
            <UserAvatar :user="member" size="md" />
            <div>
              <p class="font-semibold text-prussian-500 dark:text-lavender-200">{{ member.name }}</p>
              <p v-if="member.email" class="text-xs text-lavender-700 dark:text-lavender-400">{{ member.email }}</p>
              <p v-else class="text-xs text-lavender-600 dark:text-lavender-500 italic">Managed account</p>
              <div class="flex items-center gap-2 mt-1">
                <span :class="[
                  'text-xs px-2 py-0.5 rounded-full font-medium',
                  member.family_role === 'parent' || member.role === 'parent'
                    ? 'bg-wisteria-100 text-wisteria-700 dark:bg-wisteria-900/30 dark:text-wisteria-300'
                    : 'bg-lavender-200 text-lavender-700 dark:bg-prussian-600 dark:text-lavender-300'
                ]">
                  {{ (member.family_role || member.role) === 'parent' ? 'Parent' : 'Child' }}
                </span>
                <span v-if="member.is_managed" class="text-xs px-2 py-0.5 rounded-full bg-sand-100 text-sand-700 dark:bg-sand-900/30 dark:text-sand-300 font-medium">
                  Managed
                </span>
              </div>
            </div>
          </div>

          <!-- Actions (parent only, not for self) -->
          <div v-if="isParent && member.id !== currentUser?.id" class="flex items-center gap-1">
            <!-- Switch to managed child -->
            <button
              v-if="member.is_managed"
              @click="openSwitchToModal(member)"
              class="p-2 hover:bg-wisteria-100 dark:hover:bg-wisteria-900/20 rounded-lg transition-colors"
              title="Switch to this profile"
            >
              <ArrowsRightLeftIcon class="w-4 h-4 text-wisteria-600 dark:text-wisteria-400" />
            </button>

            <!-- Edit -->
            <button
              @click="openEditMemberModal(member)"
              class="p-2 hover:bg-lavender-100 dark:hover:bg-prussian-600 rounded-lg transition-colors"
              title="Edit member"
            >
              <PencilIcon class="w-4 h-4 text-prussian-400 dark:text-lavender-400" />
            </button>

            <!-- Remove -->
            <button
              @click="confirmRemoveMember(member)"
              class="p-2 hover:bg-red-100 dark:hover:bg-red-900/20 rounded-lg transition-colors"
              title="Remove member"
            >
              <TrashIcon class="w-4 h-4 text-red-600" />
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- API Configuration -->
    <div class="card-lg mb-6">
      <h2 class="text-lg font-semibold text-prussian-500 dark:text-lavender-200 mb-4">API Configuration</h2>

      <div class="space-y-4">
        <div class="p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
          <p class="text-sm text-blue-900 dark:text-blue-200">
            Configure API keys for enhanced features like AI chat and calendar integration.
          </p>
        </div>

        <!-- Anthropic API Key -->
        <div>
          <label class="block text-sm font-medium text-prussian-400 dark:text-lavender-300 mb-2">
            Anthropic API Key (for Hub AI)
          </label>
          <input
            v-model="apiConfig.anthropic_key"
            type="password"
            placeholder="sk-ant-..."
            class="input-base"
          />
          <p class="text-xs text-lavender-700 dark:text-lavender-400 mt-1">
            Keep this secret. Used for family chat AI features.
          </p>
        </div>

        <!-- Google Calendar -->
        <div class="border-t border-lavender-200 dark:border-prussian-700 pt-4">
          <h3 class="font-semibold text-prussian-500 dark:text-lavender-200 mb-3">Google Calendar Sync</h3>
          <p class="text-sm text-lavender-700 dark:text-lavender-400 mb-4">
            Connect your Google Calendar to sync events into the family hub.
          </p>

          <div class="space-y-2">
            <div
              v-for="conn in userCalendarConnections"
              :key="conn.id"
              class="flex items-center justify-between p-3 bg-lavender-50 dark:bg-prussian-700 rounded-lg"
            >
              <div>
                <p class="font-medium text-prussian-500 dark:text-lavender-200">{{ conn.calendar_name || 'Google Calendar' }}</p>
                <p class="text-xs text-lavender-600 dark:text-lavender-400 mt-0.5">{{ currentUser?.name }}</p>
              </div>
              <BaseButton
                variant="ghost"
                size="sm"
                @click="handleDisconnectCalendar(conn.id)"
              >
                Disconnect
              </BaseButton>
            </div>

            <div v-if="userCalendarConnections.length === 0" class="flex items-center justify-between p-3 bg-lavender-50 dark:bg-prussian-700 rounded-lg">
              <div>
                <p class="font-medium text-prussian-500 dark:text-lavender-200">{{ currentUser?.name }}</p>
                <p class="text-xs text-lavender-700 dark:text-lavender-400 mt-0.5">
                  <span class="badge badge-warning">Not Connected</span>
                </p>
              </div>
              <BaseButton
                variant="secondary"
                size="sm"
                :loading="connectingCalendar"
                @click="handleConnectCalendar"
              >
                Connect
              </BaseButton>
            </div>

            <div v-if="userCalendarConnections.length > 0" class="flex justify-end">
              <BaseButton
                variant="secondary"
                size="sm"
                :loading="connectingCalendar"
                @click="handleConnectCalendar"
              >
                Reconnect / Add Calendars
              </BaseButton>
            </div>

            <div
              v-for="conn in otherMemberConnections"
              :key="conn.id"
              class="flex items-center justify-between p-3 bg-lavender-50 dark:bg-prussian-700 rounded-lg"
            >
              <div>
                <p class="font-medium text-prussian-500 dark:text-lavender-200">{{ conn.calendar_name || 'Google Calendar' }}</p>
                <p class="text-xs text-lavender-600 dark:text-lavender-400 mt-0.5">{{ conn.user?.name || 'Family Member' }}</p>
              </div>
            </div>
          </div>

          <div v-if="calendarError" class="mt-3 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
            <p class="text-sm text-red-700 dark:text-red-300">{{ calendarError }}</p>
          </div>
        </div>

        <!-- ICS URL Subscription -->
        <div class="border-t border-lavender-200 dark:border-prussian-700 pt-4">
          <h3 class="font-semibold text-prussian-500 dark:text-lavender-200 mb-3">Subscribe via URL</h3>
          <p class="text-sm text-lavender-700 dark:text-lavender-400 mb-4">
            Add a calendar by pasting its ICS feed URL (works with any .ics calendar link).
          </p>

          <form @submit.prevent="handleSubscribeUrl" class="space-y-3">
            <div>
              <label class="block text-sm font-medium text-prussian-400 dark:text-lavender-300 mb-1">Calendar URL</label>
              <input
                v-model="icsForm.url"
                type="url"
                placeholder="https://example.com/calendar.ics"
                class="input-base"
                required
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-prussian-400 dark:text-lavender-300 mb-1">Calendar Name (optional)</label>
              <input
                v-model="icsForm.name"
                type="text"
                placeholder="My Calendar"
                class="input-base"
              />
              <p class="text-xs text-lavender-600 dark:text-lavender-400 mt-1">If left blank, the name will be auto-detected from the calendar data.</p>
            </div>
            <div class="flex justify-end">
              <BaseButton
                variant="secondary"
                size="sm"
                :loading="subscribingUrl"
              >
                Subscribe
              </BaseButton>
            </div>
          </form>

          <div v-if="icsError" class="mt-3 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
            <p class="text-sm text-red-700 dark:text-red-300">{{ icsError }}</p>
          </div>

          <div v-if="icsConnections.length > 0" class="mt-4 space-y-2">
            <p class="text-sm font-medium text-prussian-400 dark:text-lavender-300">Subscribed Calendars</p>
            <div
              v-for="conn in icsConnections"
              :key="conn.id"
              class="flex items-center justify-between p-3 bg-lavender-50 dark:bg-prussian-700 rounded-lg"
            >
              <div>
                <p class="font-medium text-prussian-500 dark:text-lavender-200">{{ conn.calendar_name || 'ICS Calendar' }}</p>
                <p class="text-xs text-lavender-600 dark:text-lavender-400 mt-0.5">URL subscription</p>
              </div>
              <BaseButton
                variant="ghost"
                size="sm"
                @click="handleDisconnectCalendar(conn.id)"
              >
                Unsubscribe
              </BaseButton>
            </div>
          </div>
        </div>

        <div class="flex gap-3 justify-end pt-4">
          <BaseButton variant="ghost" @click="resetApiConfig">
            Reset
          </BaseButton>
          <BaseButton variant="primary" :loading="savingApi">
            Save Configuration
          </BaseButton>
        </div>
      </div>
    </div>

    <!-- Appearance -->
    <div class="card-lg mb-6">
      <h2 class="text-lg font-semibold text-prussian-500 dark:text-lavender-200 mb-4">Appearance</h2>

      <div class="flex items-center justify-between p-4 bg-lavender-50 dark:bg-prussian-800 rounded-lg">
        <div>
          <p class="font-medium text-prussian-500 dark:text-lavender-200">Dark Mode</p>
          <p class="text-xs text-lavender-700 dark:text-lavender-400 mt-0.5">Switch between light and dark themes</p>
        </div>
        <button
          @click="toggleDarkMode"
          :class="[
            'relative inline-flex h-7 w-12 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-wisteria-400 focus:ring-offset-2',
            isDark ? 'bg-wisteria-500' : 'bg-lavender-300',
          ]"
        >
          <span
            :class="[
              'pointer-events-none inline-block h-6 w-6 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
              isDark ? 'translate-x-5' : 'translate-x-0',
            ]"
          >
            <span class="flex items-center justify-center h-full">
              <MoonIcon v-if="isDark" class="w-3.5 h-3.5 text-wisteria-500" />
              <SunIcon v-else class="w-3.5 h-3.5 text-sand-500" />
            </span>
          </span>
        </button>
      </div>
    </div>

    <!-- Module Toggles -->
    <div class="card-lg mb-6">
      <h2 class="text-lg font-semibold text-prussian-500 dark:text-lavender-200 mb-4">Feature Toggles</h2>

      <div class="space-y-3">
        <label
          v-for="module in availableModules"
          :key="module.id"
          class="flex items-center gap-3 p-4 bg-lavender-50 dark:bg-prussian-700 rounded-lg cursor-pointer hover:bg-lavender-100 dark:hover:bg-prussian-600 transition-colors"
        >
          <input
            v-model="moduleToggles[module.id]"
            type="checkbox"
            class="rounded"
          />
          <div class="flex-1">
            <p class="font-medium text-prussian-500 dark:text-lavender-200">{{ module.name }}</p>
            <p class="text-xs text-lavender-700 dark:text-lavender-400">{{ module.description }}</p>
          </div>
        </label>
      </div>

      <!-- Leaderboard Period -->
      <div v-if="moduleToggles.points" class="mt-4 pt-4 border-t border-lavender-200 dark:border-prussian-700">
        <label class="block text-sm font-medium text-prussian-400 dark:text-lavender-300 mb-2">
          Leaderboard Reset Period
        </label>
        <select v-model="leaderboardPeriod" class="input-base w-full max-w-xs">
          <option value="daily">Daily</option>
          <option value="weekly">Weekly</option>
          <option value="monthly">Monthly</option>
        </select>
        <p class="text-xs text-lavender-600 dark:text-lavender-400 mt-1">
          How often the leaderboard resets. Does not affect point balances.
        </p>
      </div>

      <div class="flex gap-3 justify-end pt-4 border-t border-lavender-200 dark:border-prussian-700 mt-4">
        <BaseButton variant="primary" :loading="savingModules" @click="saveModuleSettings">
          Save Preferences
        </BaseButton>
      </div>
    </div>

    <!-- Add/Edit Member Modal -->
    <BaseModal
      :show="showMemberModal"
      :title="editingMember ? 'Edit Family Member' : 'Add Family Member'"
      @close="closeMemberModal"
    >
      <form @submit.prevent="handleSaveMember" class="space-y-4">
        <BaseInput
          v-model="memberForm.name"
          label="Name"
          placeholder="First name"
          required
          :error="memberErrors.name"
        />

        <BaseInput
          v-model="memberForm.email"
          label="Email (optional for managed accounts)"
          type="email"
          placeholder="email@example.com"
          :error="memberErrors.email"
        />
        <p class="text-xs text-lavender-600 dark:text-lavender-400 -mt-2">
          Leave blank for young kids — creates a managed account you can switch into.
        </p>

        <BaseInput
          v-if="!editingMember && memberForm.email"
          v-model="memberForm.password"
          label="Password (optional)"
          type="password"
          placeholder="Leave blank to set later"
          :error="memberErrors.password"
        />

        <label
          v-if="!editingMember && memberForm.email"
          class="flex items-center gap-3 p-3 bg-lavender-50 dark:bg-prussian-700 rounded-lg cursor-pointer"
        >
          <input v-model="memberForm.sendEmail" type="checkbox" class="rounded" />
          <div>
            <p class="text-sm font-medium text-prussian-500 dark:text-lavender-200">Send welcome email</p>
            <p class="text-xs text-lavender-600 dark:text-lavender-400">Send an email with login instructions</p>
          </div>
        </label>

        <div>
          <label class="block text-sm font-medium text-prussian-400 dark:text-lavender-300 mb-2">Role</label>
          <select v-model="memberForm.role" class="input-base w-full" required>
            <option value="child">Child</option>
            <option value="parent">Parent</option>
          </select>
        </div>

        <BaseInput
          v-model="memberForm.date_of_birth"
          label="Date of Birth (optional)"
          type="date"
        />

        <div class="flex gap-2 justify-end pt-4">
          <BaseButton variant="ghost" @click="closeMemberModal">
            Cancel
          </BaseButton>
          <BaseButton variant="primary" :loading="savingMember">
            {{ editingMember ? 'Save Changes' : 'Add Member' }}
          </BaseButton>
        </div>
      </form>
    </BaseModal>

    <!-- Remove Member Confirm -->
    <BaseModal
      :show="showRemoveConfirm"
      title="Remove Family Member"
      @close="showRemoveConfirm = false"
    >
      <p class="text-prussian-500 dark:text-lavender-300">
        Are you sure you want to remove <strong>{{ removingMember?.name }}</strong> from your family?
      </p>
      <p v-if="removingMember?.is_managed" class="text-sm text-red-600 dark:text-red-400 mt-2">
        This is a managed account and will be permanently deleted.
      </p>
      <p v-else class="text-sm text-lavender-600 dark:text-lavender-400 mt-2">
        Their account will be unlinked from your family but not deleted.
      </p>

      <div class="flex gap-2 justify-end pt-4">
        <BaseButton variant="ghost" @click="showRemoveConfirm = false">
          Cancel
        </BaseButton>
        <BaseButton variant="danger" :loading="removingLoading" @click="handleRemoveMember">
          Remove
        </BaseButton>
      </div>
    </BaseModal>

    <!-- Switch To Child Confirmation Modal -->
    <BaseModal
      :show="showSwitchToModal"
      title="Switch to Child Profile"
      @close="closeSwitchToModal"
    >
      <div class="space-y-3">
        <p class="text-sm text-prussian-500 dark:text-lavender-300">
          You're about to switch this device to <strong>{{ switchingToMember?.name }}</strong>'s profile.
        </p>
        <div class="p-3 bg-sand-50 dark:bg-sand-900/20 border border-sand-200 dark:border-sand-800 rounded-lg">
          <p class="text-sm text-sand-800 dark:text-sand-200 font-medium">What will happen:</p>
          <ul class="text-sm text-sand-700 dark:text-sand-300 mt-1 space-y-1 list-disc list-inside">
            <li>This device will be logged in as {{ switchingToMember?.name }}</li>
            <li>To switch back, go to Settings and enter your parent password</li>
          </ul>
        </div>
      </div>

      <div class="flex gap-2 justify-end pt-4">
        <BaseButton variant="ghost" @click="closeSwitchToModal">
          Cancel
        </BaseButton>
        <BaseButton variant="primary" :loading="switchingTo" @click="handleSwitchToProfile">
          Switch to {{ switchingToMember?.name }}
        </BaseButton>
      </div>
    </BaseModal>
  </div>
</template>

<script setup>
import { reactive, ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { storeToRefs } from 'pinia'
import { useAuthStore } from '@/stores/auth'
import { useCalendarStore } from '@/stores/calendar'
import api from '@/services/api'
import { useNotification } from '@/composables/useNotification'
import { useDarkMode } from '@/composables/useDarkMode'
import BaseCard from '@/components/common/BaseCard.vue'
import BaseButton from '@/components/common/BaseButton.vue'
import BaseInput from '@/components/common/BaseInput.vue'
import BaseModal from '@/components/common/BaseModal.vue'
import UserAvatar from '@/components/common/UserAvatar.vue'
import {
  PlusIcon,
  TrashIcon,
  SunIcon,
  MoonIcon,
  PencilIcon,
  ClipboardDocumentIcon,
  ArrowsRightLeftIcon,
} from '@heroicons/vue/24/outline'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const calendarStore = useCalendarStore()
const { success, error: notificationError } = useNotification()
const { isDark, toggle: toggleDarkMode } = useDarkMode()

const { family, familyMembers, currentUser, isParent, switchedFrom, isSwitchedSession } = storeToRefs(authStore)
const { connections } = storeToRefs(calendarStore)

// Family form
const savingFamily = ref(false)
const familyForm = reactive({ name: family.value?.name || '' })
const familyErrors = reactive({ name: '' })

// API config
const savingApi = ref(false)
const apiConfig = reactive({ anthropic_key: '', google_calendar_enabled: true })

// Module toggles
const savingModules = ref(false)
const moduleToggles = reactive({
  calendar: true, tasks: true, vault: true, chat: true, points: true, badges: true,
})
const leaderboardPeriod = ref('weekly')

// Invite code
const inviteCode = ref(family.value?.invite_code || '')
const copied = ref(false)

// Calendar
const connectingCalendar = ref(false)
const disconnectingCalendar = ref(false)
const subscribingUrl = ref(false)
const calendarError = ref('')
const icsError = ref('')
const icsForm = reactive({ url: '', name: '' })

const userCalendarConnections = computed(() =>
  (connections.value || []).filter((c) => c.user_id === currentUser.value?.id && c.provider !== 'ics')
)
const otherMemberConnections = computed(() =>
  (connections.value || []).filter((c) => c.user_id !== currentUser.value?.id)
)
const icsConnections = computed(() =>
  (connections.value || []).filter((c) => c.user_id === currentUser.value?.id && c.provider === 'ics')
)

// Member management
const showMemberModal = ref(false)
const editingMember = ref(null)
const savingMember = ref(false)
const memberForm = reactive({ name: '', email: '', password: '', role: 'child', date_of_birth: '', sendEmail: false })
const memberErrors = reactive({ name: '', email: '', password: '' })

// Remove member
const showRemoveConfirm = ref(false)
const removingMember = ref(null)
const removingLoading = ref(false)

// Profile switching
const switchBackPassword = ref('')
const switchBackError = ref('')
const switchingBack = ref(false)
const showSwitchToModal = ref(false)
const switchingToMember = ref(null)
const switchingTo = ref(false)

const availableModules = [
  { id: 'calendar', name: 'Calendar', description: 'View and manage family events' },
  { id: 'tasks', name: 'Tasks', description: 'Create and assign tasks' },
  { id: 'vault', name: 'Family Vault', description: 'Secure information storage' },
  { id: 'chat', name: 'Hub AI', description: 'AI-powered assistant' },
  { id: 'points', name: 'Points & Rewards', description: 'Earn points, give kudos, purchase rewards' },
  { id: 'badges', name: 'Badges', description: 'Achievement badges and milestones' },
]

// ---- Family name ----
const updateFamily = async () => {
  familyErrors.name = ''
  if (!familyForm.name) {
    familyErrors.name = 'Family name is required'
    return
  }
  savingFamily.value = true
  const result = await authStore.updateFamilyName(familyForm.name)
  if (result.success) {
    success('Family name updated!')
  } else {
    notificationError(result.error)
  }
  savingFamily.value = false
}

const cancelEditFamily = () => {
  familyForm.name = family.value?.name || ''
  familyErrors.name = ''
}

// ---- Invite code ----
const loadInviteCode = async () => {
  if (family.value?.invite_code) {
    inviteCode.value = family.value.invite_code
    return
  }
  const result = await authStore.getInviteCode()
  if (result.success) {
    inviteCode.value = result.invite_code
  }
}

const copyInviteCode = async () => {
  try {
    await navigator.clipboard.writeText(inviteCode.value)
    copied.value = true
    setTimeout(() => { copied.value = false }, 2000)
  } catch {
    notificationError('Failed to copy')
  }
}

// ---- Member management ----
const openAddMemberModal = () => {
  editingMember.value = null
  memberForm.name = ''
  memberForm.email = ''
  memberForm.password = ''
  memberForm.role = 'child'
  memberForm.date_of_birth = ''
  memberForm.sendEmail = false
  memberErrors.name = ''
  memberErrors.email = ''
  memberErrors.password = ''
  showMemberModal.value = true
}

const openEditMemberModal = (member) => {
  editingMember.value = member
  memberForm.name = member.name
  memberForm.email = member.email || ''
  memberForm.password = ''
  memberForm.role = member.family_role || member.role || 'child'
  memberForm.date_of_birth = member.date_of_birth || ''
  memberErrors.name = ''
  memberErrors.email = ''
  memberErrors.password = ''
  showMemberModal.value = true
}

const closeMemberModal = () => {
  showMemberModal.value = false
  editingMember.value = null
}

const handleSaveMember = async () => {
  memberErrors.name = ''
  memberErrors.email = ''
  memberErrors.password = ''

  if (!memberForm.name) {
    memberErrors.name = 'Name is required'
    return
  }

  savingMember.value = true

  if (editingMember.value) {
    // Update existing member
    const data = {
      name: memberForm.name,
      role: memberForm.role,
      date_of_birth: memberForm.date_of_birth || null,
    }
    if (memberForm.email) data.email = memberForm.email

    const result = await authStore.updateFamilyMember(editingMember.value.id, data)
    if (result.success) {
      success('Member updated!')
      closeMemberModal()
    } else {
      notificationError(result.error)
    }
  } else {
    // Add new member
    const data = {
      name: memberForm.name,
      role: memberForm.role,
      date_of_birth: memberForm.date_of_birth || null,
    }
    if (memberForm.email) data.email = memberForm.email
    if (memberForm.password) data.password = memberForm.password
    if (memberForm.sendEmail) data.send_email = true

    const result = await authStore.addFamilyMember(data)
    if (result.success) {
      success(result.message || 'Member added!')
      closeMemberModal()
    } else {
      notificationError(result.error)
    }
  }

  savingMember.value = false
}

// ---- Remove member ----
const confirmRemoveMember = (member) => {
  removingMember.value = member
  showRemoveConfirm.value = true
}

const handleRemoveMember = async () => {
  removingLoading.value = true
  const result = await authStore.removeFamilyMember(removingMember.value.id)
  if (result.success) {
    success('Member removed!')
    showRemoveConfirm.value = false
    removingMember.value = null
  } else {
    notificationError(result.error)
  }
  removingLoading.value = false
}

// ---- Profile switching ----
const openSwitchToModal = (member) => {
  switchingToMember.value = member
  switchToPassword.value = ''
  switchToError.value = ''
  showSwitchToModal.value = true
}

const closeSwitchToModal = () => {
  showSwitchToModal.value = false
  switchingToMember.value = null
}

const handleSwitchToProfile = async () => {
  switchingTo.value = true
  const result = await authStore.switchToProfile(switchingToMember.value.id)
  if (result.success) {
    success(result.message)
    closeSwitchToModal()
    router.push('/')
  } else {
    notificationError(result.error)
    closeSwitchToModal()
  }
  switchingTo.value = false
}

const handleSwitchBack = async () => {
  switchBackError.value = ''
  if (!switchBackPassword.value) {
    switchBackError.value = 'Password is required'
    return
  }
  switchingBack.value = true
  const result = await authStore.switchBack(switchBackPassword.value)
  if (result.success) {
    success(result.message)
    switchBackPassword.value = ''
    router.push('/settings')
  } else {
    switchBackError.value = result.error || 'Invalid password'
  }
  switchingBack.value = false
}

// ---- Calendar ----
const handleConnectCalendar = async () => {
  connectingCalendar.value = true
  calendarError.value = ''
  const result = await calendarStore.connect('google')
  if (result.success && result.authUrl) {
    window.location.href = result.authUrl
  } else {
    calendarError.value = result.error || 'Failed to start Google Calendar connection.'
  }
  connectingCalendar.value = false
}

const handleDisconnectCalendar = async (connectionId) => {
  disconnectingCalendar.value = true
  calendarError.value = ''
  const result = await calendarStore.disconnect(connectionId)
  if (result.success) {
    success('Calendar disconnected!')
  } else {
    calendarError.value = result.error || 'Failed to disconnect calendar'
  }
  disconnectingCalendar.value = false
}

const handleSubscribeUrl = async () => {
  subscribingUrl.value = true
  icsError.value = ''
  const result = await calendarStore.subscribeUrl(icsForm.url, icsForm.name || null)
  if (result.success) {
    success(result.message || 'Calendar subscribed!')
    icsForm.url = ''
    icsForm.name = ''
  } else {
    icsError.value = result.error || 'Failed to subscribe to calendar'
  }
  subscribingUrl.value = false
}

const resetApiConfig = () => {
  apiConfig.anthropic_key = ''
}

// ---- Module settings ----
const saveModuleSettings = async () => {
  savingModules.value = true
  try {
    await api.put('/settings', {
      modules: { ...moduleToggles },
      leaderboard_period: leaderboardPeriod.value,
    })
    await authStore.fetchUser()
    success('Preferences saved!')
  } catch (err) {
    notificationError(err.response?.data?.message || 'Failed to save preferences')
  }
  savingModules.value = false
}

// ---- Init ----
onMounted(async () => {
  familyForm.name = family.value?.name || ''

  // Initialize module toggles
  const settings = family.value?.settings || {}
  const modules = settings.modules || {}
  moduleToggles.calendar = modules.calendar !== false
  moduleToggles.tasks = modules.tasks !== false
  moduleToggles.vault = modules.vault !== false
  moduleToggles.chat = modules.chat !== false
  moduleToggles.points = modules.points !== false
  moduleToggles.badges = modules.badges !== false
  leaderboardPeriod.value = settings.leaderboard_period || 'weekly'

  await calendarStore.fetchConnections()

  // Load invite code for parents
  if (isParent.value) {
    await loadInviteCode()
  }

  // Handle OAuth redirect results
  if (route.query.calendar_connected) {
    success('Google Calendar connected successfully!')
    router.replace({ path: '/settings' })
  } else if (route.query.calendar_error) {
    calendarError.value = route.query.calendar_error
    router.replace({ path: '/settings' })
  }
})
</script>
