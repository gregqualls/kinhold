<template>
  <div class="flex-1 flex flex-col">
    <div class="text-center mb-8">
      <h1 class="text-2xl font-heading font-bold text-kin-black dark:text-kin-off-white mb-2">
        {{ isParent ? 'Welcome to Kinhold' : `Welcome to ${familyName || 'Kinhold'}` }}
      </h1>
      <p class="text-base text-kin-gray-500 dark:text-kin-gray-400">
        {{ isParent ? "Let's set up your family hub. This takes about 2 minutes." : "Just a couple of quick things to get you set up." }}
      </p>
    </div>

    <div class="space-y-5">
      <div>
        <label class="block text-sm font-medium text-kin-gray-500 dark:text-kin-gray-400 mb-1.5">
          Family Name
        </label>
        <input
          v-model="familyName"
          type="text"
          class="kin-input"
          placeholder="The Qualls Family"
          :disabled="!isParent"
        />
      </div>

      <div>
        <label class="block text-sm font-medium text-kin-gray-500 dark:text-kin-gray-400 mb-1.5">
          Your Timezone
        </label>
        <select v-model="timezone" class="kin-input">
          <option v-for="tz in commonTimezones" :key="tz" :value="tz">{{ formatTimezone(tz) }}</option>
        </select>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, inject, onMounted } from 'vue'
import { useOnboardingStore } from '@/stores/onboarding'
import { useAuthStore } from '@/stores/auth'
import api from '@/services/api'

const store = useOnboardingStore()
const authStore = useAuthStore()
const { setStepLoading, registerContinue } = inject('onboarding')

const isParent = authStore.isParent
const familyName = ref('')
const timezone = ref(Intl.DateTimeFormat().resolvedOptions().timeZone)

const commonTimezones = [
  'America/New_York',
  'America/Chicago',
  'America/Denver',
  'America/Los_Angeles',
  'America/Anchorage',
  'Pacific/Honolulu',
  'America/Phoenix',
  'America/Toronto',
  'America/Vancouver',
  'Europe/London',
  'Europe/Paris',
  'Europe/Berlin',
  'Asia/Tokyo',
  'Asia/Shanghai',
  'Australia/Sydney',
]

function formatTimezone(tz) {
  try {
    const offset = new Intl.DateTimeFormat('en-US', { timeZone: tz, timeZoneName: 'shortOffset' })
      .formatToParts(new Date())
      .find(p => p.type === 'timeZoneName')?.value || ''
    return `${tz.replace(/_/g, ' ').replace('/', ' / ')} (${offset})`
  } catch {
    return tz
  }
}

registerContinue(async () => {
  setStepLoading(true)
  try {
    if (isParent && familyName.value && familyName.value !== store.status?.family_name) {
      await api.put('/family', { name: familyName.value })
    }
    if (timezone.value) {
      await api.patch('/user', { timezone: timezone.value })
    }
    return true
  } catch {
    return false
  } finally {
    setStepLoading(false)
  }
})

onMounted(() => {
  if (store.status) {
    familyName.value = store.status.family_name || ''
    timezone.value = store.status.timezone || Intl.DateTimeFormat().resolvedOptions().timeZone
  }
})
</script>
