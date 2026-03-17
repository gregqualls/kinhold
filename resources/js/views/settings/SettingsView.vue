<template>
  <div class="p-4 md:p-6 max-w-4xl">
    <!-- Header -->
    <h1 class="text-2xl font-bold text-prussian-500 dark:text-lavender-200 mb-6">Family Settings</h1>

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

    <!-- Family Members -->
    <div class="card-lg mb-6">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-prussian-500 dark:text-lavender-200">Family Members</h2>
        <BaseButton variant="secondary" size="sm" @click="showInviteModal = true">
          <PlusIcon class="w-4 h-4 mr-2" />
          Invite Member
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
              <p class="text-xs text-lavender-700 dark:text-lavender-400">{{ member.email }}</p>
              <p class="text-xs text-lavender-600 dark:text-lavender-400 mt-1">
                <span class="badge badge-primary">{{ member.role }}</span>
              </p>
            </div>
          </div>

          <!-- Remove Button -->
          <button
            v-if="member.id !== currentUser?.id && familyMembers.length > 1"
            @click="removeMember(member.id)"
            class="p-2 hover:bg-red-100 dark:hover:bg-red-900/20 rounded-lg transition-colors"
          >
            <TrashIcon class="w-4 h-4 text-red-600" />
          </button>
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
            <!-- Current user's connected calendars -->
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

            <!-- Not connected state -->
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

            <!-- Reconnect button when already connected (to pick up new calendars) -->
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

            <!-- Other family members' connections -->
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

          <!-- Error message -->
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

          <!-- ICS subscription error -->
          <div v-if="icsError" class="mt-3 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
            <p class="text-sm text-red-700 dark:text-red-300">{{ icsError }}</p>
          </div>

          <!-- Existing ICS subscriptions -->
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

    <!-- Invite Modal -->
    <BaseModal
      :show="showInviteModal"
      title="Invite Family Member"
      @close="showInviteModal = false"
    >
      <form @submit.prevent="handleInvite" class="space-y-4">
        <BaseInput
          v-model="inviteForm.email"
          label="Email Address"
          type="email"
          placeholder="family@example.com"
          required
          :error="inviteErrors.email"
        />

        <div class="flex gap-2 justify-end pt-4">
          <BaseButton variant="ghost" @click="showInviteModal = false">
            Cancel
          </BaseButton>
          <BaseButton variant="primary" :loading="inviting">
            Send Invite
          </BaseButton>
        </div>
      </form>
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
import { PlusIcon, TrashIcon, SunIcon, MoonIcon } from '@heroicons/vue/24/outline'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const calendarStore = useCalendarStore()
const { success, error: notificationError } = useNotification()
const { isDark, toggle: toggleDarkMode } = useDarkMode()

const { family, familyMembers, currentUser } = storeToRefs(authStore)
const { connections } = storeToRefs(calendarStore)

const savingFamily = ref(false)
const savingApi = ref(false)
const savingModules = ref(false)
const inviting = ref(false)
const showInviteModal = ref(false)
const connectingCalendar = ref(false)
const disconnectingCalendar = ref(false)
const subscribingUrl = ref(false)
const calendarError = ref('')
const icsError = ref('')

const icsForm = reactive({
  url: '',
  name: '',
})

// Current user's Google calendar connections (excludes ICS subscriptions)
const userCalendarConnections = computed(() =>
  (connections.value || []).filter((c) => c.user_id === currentUser.value?.id && c.provider !== 'ics')
)

// Other family members' connections
const otherMemberConnections = computed(() =>
  (connections.value || []).filter((c) => c.user_id !== currentUser.value?.id)
)

// ICS URL subscriptions for current user
const icsConnections = computed(() =>
  (connections.value || []).filter((c) => c.user_id === currentUser.value?.id && c.provider === 'ics')
)

const familyForm = reactive({
  name: family.value?.name || '',
})

const familyErrors = reactive({
  name: '',
})

const apiConfig = reactive({
  anthropic_key: '',
  google_calendar_enabled: true,
})

const moduleToggles = reactive({
  calendar: true,
  tasks: true,
  vault: true,
  chat: true,
  points: true,
  badges: true,
})

const leaderboardPeriod = ref('weekly')

const inviteForm = reactive({
  email: '',
})

const inviteErrors = reactive({
  email: '',
})

const availableModules = [
  { id: 'calendar', name: 'Calendar', description: 'View and manage family events' },
  { id: 'tasks', name: 'Tasks', description: 'Create and assign tasks' },
  { id: 'vault', name: 'Family Vault', description: 'Secure information storage' },
  { id: 'chat', name: 'Hub AI', description: 'AI-powered assistant' },
  { id: 'points', name: 'Points & Rewards', description: 'Earn points, give kudos, purchase rewards' },
  { id: 'badges', name: 'Badges', description: 'Achievement badges and milestones' },
]

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

const handleInvite = async () => {
  inviteErrors.email = ''

  if (!inviteForm.email) {
    inviteErrors.email = 'Email is required'
    return
  }

  inviting.value = true

  const result = await authStore.inviteFamilyMember(inviteForm.email)

  if (result.success) {
    success('Invite sent!')
    showInviteModal.value = false
    inviteForm.email = ''
  } else {
    notificationError(result.error)
  }

  inviting.value = false
}

const removeMember = async (memberId) => {
  if (!confirm('Are you sure you want to remove this family member?')) return

  const result = await authStore.removeFamilyMember(memberId)

  if (result.success) {
    success('Member removed!')
  } else {
    notificationError(result.error)
  }
}

const resetApiConfig = () => {
  apiConfig.anthropic_key = ''
}

const handleConnectCalendar = async () => {
  connectingCalendar.value = true
  calendarError.value = ''

  const result = await calendarStore.connect('google')

  if (result.success && result.authUrl) {
    // Redirect user to Google OAuth page
    window.location.href = result.authUrl
  } else {
    calendarError.value = result.error || 'Failed to start Google Calendar connection. Make sure GOOGLE_CLIENT_ID and GOOGLE_CLIENT_SECRET are configured in the server .env file.'
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

const saveModuleSettings = async () => {
  savingModules.value = true
  try {
    await api.put('/settings', {
      modules: { ...moduleToggles },
      leaderboard_period: leaderboardPeriod.value,
    })
    // Refresh family data so enabledModules updates
    await authStore.fetchUser()
    success('Preferences saved!')
  } catch (err) {
    notificationError(err.response?.data?.message || 'Failed to save preferences')
  }
  savingModules.value = false
}

onMounted(async () => {
  familyForm.name = family.value?.name || ''

  // Initialize module toggles from family settings
  const settings = family.value?.settings || {}
  const modules = settings.modules || {}
  moduleToggles.calendar = modules.calendar !== false
  moduleToggles.tasks = modules.tasks !== false
  moduleToggles.vault = modules.vault !== false
  moduleToggles.chat = modules.chat !== false
  moduleToggles.points = modules.points !== false
  moduleToggles.badges = modules.badges !== false
  leaderboardPeriod.value = settings.leaderboard_period || 'weekly'

  // Fetch calendar connections to show current status
  await calendarStore.fetchConnections()

  // Handle OAuth redirect results
  if (route.query.calendar_connected) {
    success('Google Calendar connected successfully!')
    // Clean up URL query params
    router.replace({ path: '/settings' })
  } else if (route.query.calendar_error) {
    calendarError.value = route.query.calendar_error
    router.replace({ path: '/settings' })
  }
})
</script>
