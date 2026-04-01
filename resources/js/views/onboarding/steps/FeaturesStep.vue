<template>
  <div class="flex-1 flex flex-col">
    <div class="text-center mb-6">
      <h1 class="text-2xl font-heading font-bold text-kin-black dark:text-kin-off-white mb-2">
        Choose Your Features
      </h1>
      <p class="text-base text-kin-gray-500 dark:text-kin-gray-400">
        Control which features are available and who can access them.
      </p>
    </div>

    <div class="space-y-3 overflow-y-auto">
      <div
        v-for="feature in features"
        :key="feature.key"
        class="p-4 rounded-xl border transition-all duration-200"
        :class="moduleState[feature.key]?.mode === 'off'
          ? 'border-kin-border dark:border-kin-border-dark bg-white/50 dark:bg-kin-surface-dark/50 opacity-60'
          : 'border-kin-gold/30 bg-kin-gold/5 dark:bg-kin-gold/10 dark:border-kin-gold/20'"
      >
        <!-- Header: icon + name + description -->
        <div class="flex items-start gap-3 mb-3">
          <div class="kin-icon-box flex-shrink-0 mt-0.5">
            <component :is="feature.icon" class="w-5 h-5" />
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-kin-black dark:text-kin-off-white">{{ feature.name }}</p>
            <p class="text-xs text-kin-gray-500 dark:text-kin-gray-400 mt-0.5">{{ feature.description }}</p>
          </div>
        </div>

        <!-- Access mode buttons -->
        <div class="flex gap-1.5 ml-13">
          <button
            v-for="option in accessOptions"
            :key="option.mode"
            class="px-2.5 py-1 text-xs font-medium rounded-full transition-colors"
            :class="isActiveMode(feature.key, option.mode)
              ? option.activeClass
              : 'bg-kin-gray-50 dark:bg-kin-gray-800 text-kin-gray-500 dark:text-kin-gray-400 hover:bg-kin-gray-100 dark:hover:bg-kin-gray-700'"
            @click="setMode(feature.key, option.mode)"
          >
            {{ option.label }}
          </button>
        </div>

        <!-- Per-member checkboxes (Custom mode only) -->
        <div v-if="moduleState[feature.key]?.mode === 'users'" class="mt-3 pt-3 border-t border-kin-border dark:border-kin-border-dark ml-13">
          <p class="text-xs font-medium text-kin-gray-500 dark:text-kin-gray-400 mb-2">Select family members:</p>
          <div class="flex flex-wrap gap-2">
            <label
              v-for="member in familyMembers"
              :key="member.id"
              class="flex items-center gap-2 px-3 py-2 bg-white dark:bg-kin-surface-dark rounded-lg cursor-pointer hover:bg-kin-gray-50 dark:hover:bg-kin-gray-800 transition-colors text-sm"
            >
              <input
                type="checkbox"
                :checked="isMemberSelected(feature.key, member.id)"
                class="rounded"
                :disabled="(member.family_role || member.role) === 'parent'"
                @change="toggleMember(feature.key, member.id)"
              />
              <span class="text-kin-black dark:text-kin-off-white">{{ member.name }}</span>
              <span
                v-if="(member.family_role || member.role) === 'parent'"
                class="text-xs text-kin-gray-400 italic"
              >(always)</span>
            </label>
          </div>
        </div>

        <!-- Mode summary -->
        <p class="text-xs text-kin-gray-400 mt-2 ml-13">
          <template v-if="moduleState[feature.key]?.mode === 'all'">All family members can access this.</template>
          <template v-else-if="moduleState[feature.key]?.mode === 'off'">Disabled for everyone.</template>
          <template v-else-if="moduleState[feature.key]?.mode === 'roles'">Parents only.</template>
          <template v-else-if="moduleState[feature.key]?.mode === 'users'">{{ getSelectedNames(feature.key) }}</template>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, computed, inject, onMounted } from 'vue'
import { useOnboardingStore } from '@/stores/onboarding'
import { useAuthStore } from '@/stores/auth'
import api from '@/services/api'
import {
  CalendarDaysIcon,
  ClipboardDocumentListIcon,
  TrophyIcon,
  StarIcon,
  ChatBubbleLeftRightIcon,
  LockClosedIcon,
} from '@heroicons/vue/24/outline'

const store = useOnboardingStore()
const authStore = useAuthStore()
const { setStepLoading, registerContinue } = inject('onboarding')

const familyMembers = computed(() => authStore.family?.members || [])

const features = [
  {
    key: 'calendar',
    name: 'Calendar',
    description: 'View and manage family events from connected calendars.',
    icon: CalendarDaysIcon,
  },
  {
    key: 'tasks',
    name: 'Tasks',
    description: 'Create tasks, organize with tags, assign to family members.',
    icon: ClipboardDocumentListIcon,
  },
  {
    key: 'points',
    name: 'Points & Rewards',
    description: 'Earn points for completing tasks. Points go into a bank that can be spent on rewards you create. Parents can give kudos or deduct points.',
    icon: TrophyIcon,
  },
  {
    key: 'badges',
    name: 'Badges',
    description: 'Achievements that unlock automatically — task streaks, point milestones, and more. Parents can also create and award custom badges.',
    icon: StarIcon,
  },
  {
    key: 'chat',
    name: 'AI Chat',
    description: 'An AI assistant that can answer questions about your calendar, tasks, and vault data.',
    icon: ChatBubbleLeftRightIcon,
  },
  {
    key: 'vault',
    name: 'Family Vault',
    description: 'Encrypted storage for sensitive info — SSNs, insurance, medical records. Parents see everything; children only see what\'s shared with them.',
    icon: LockClosedIcon,
  },
]

const accessOptions = [
  { mode: 'all', label: 'Everyone', activeClass: 'bg-kin-gold text-white' },
  { mode: 'roles', label: 'Parents Only', activeClass: 'bg-kin-gold text-white' },
  { mode: 'users', label: 'Custom', activeClass: 'bg-kin-gold text-white' },
  { mode: 'off', label: 'Off', activeClass: 'bg-kin-error text-white' },
]

const moduleState = reactive({
  calendar: { mode: 'all' },
  tasks: { mode: 'all' },
  points: { mode: 'all' },
  badges: { mode: 'all' },
  chat: { mode: 'all' },
  vault: { mode: 'all' },
})

function isActiveMode(key, mode) {
  return moduleState[key]?.mode === mode
}

function setMode(key, mode) {
  if (mode === 'users') {
    // Pre-select all parents when switching to custom
    const parentIds = familyMembers.value
      .filter(m => (m.family_role || m.role) === 'parent')
      .map(m => m.id)
    moduleState[key] = { mode: 'users', users: [...parentIds] }
  } else if (mode === 'roles') {
    moduleState[key] = { mode: 'roles', roles: ['parent'] }
  } else {
    moduleState[key] = { mode }
  }
}

function isMemberSelected(key, memberId) {
  return (moduleState[key]?.users || []).includes(memberId)
}

function toggleMember(key, memberId) {
  const users = [...(moduleState[key]?.users || [])]
  const idx = users.indexOf(memberId)
  if (idx >= 0) {
    users.splice(idx, 1)
  } else {
    users.push(memberId)
  }
  moduleState[key] = { ...moduleState[key], users }
}

function getSelectedNames(key) {
  const userIds = moduleState[key]?.users || []
  const names = familyMembers.value
    .filter(m => userIds.includes(m.id))
    .map(m => m.name)
  return names.length ? names.join(', ') : 'No members selected (parents always have access).'
}

registerContinue(async () => {
  if (!authStore.isParent) return true

  setStepLoading(true)
  try {
    const module_access = {}
    for (const f of features) {
      const state = moduleState[f.key]
      if (!state) continue
      const rule = { mode: state.mode }
      if (state.mode === 'roles') rule.roles = state.roles || ['parent']
      if (state.mode === 'users') rule.users = state.users || []
      module_access[f.key] = rule
    }
    await api.put('/settings', { module_access })
    return true
  } catch (err) {
    console.error('Failed to save feature access:', err)
    return false
  } finally {
    setStepLoading(false)
  }
})

onMounted(() => {
  // Auto-skip for non-parents
  if (!authStore.isParent) {
    store.nextStep()
    return
  }

  // Pre-fill from current family module_access if available
  const access = authStore.family?.module_access
  if (access) {
    for (const key of Object.keys(moduleState)) {
      if (access[key]) {
        moduleState[key] = { ...access[key] }
      }
    }
  }
})
</script>

<style scoped>
.ml-13 {
  margin-left: 3.25rem;
}
</style>
