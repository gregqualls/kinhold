<template>
  <div class="flex-1 flex flex-col">
    <div class="text-center mb-6">
      <h1 class="text-2xl font-heading font-bold text-ink-primary mb-2">
        Choose Your Features
      </h1>
      <p class="text-base text-ink-secondary">
        Control which features are available and who can access them.
      </p>
    </div>

    <div class="space-y-3 overflow-y-auto">
      <KinFlatCard
        v-for="feature in features"
        :key="feature.key"
        padding="sm"
        :class="moduleState[feature.key]?.mode === 'off' ? 'opacity-60' : ''"
      >
        <!-- Header: icon + name + description -->
        <div class="flex items-start gap-3 mb-3">
          <div class="kin-icon-box flex-shrink-0 mt-0.5">
            <component :is="feature.icon" class="w-5 h-5" />
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-ink-primary">{{ feature.name }}</p>
            <p class="text-xs text-ink-secondary mt-0.5">{{ feature.description }}</p>
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
              : 'bg-surface-sunken text-ink-secondary hover:bg-surface-overlay'"
            @click="setMode(feature.key, option.mode)"
          >
            {{ option.label }}
          </button>
        </div>

        <!-- Per-member checkboxes (Custom mode only) -->
        <div v-if="moduleState[feature.key]?.mode === 'users'" class="mt-3 pt-3 border-t border-border-subtle ml-13">
          <p class="text-xs font-medium text-ink-secondary mb-2">Select family members:</p>
          <div class="flex flex-wrap gap-2">
            <div
              v-for="member in familyMembers"
              :key="member.id"
              class="flex items-center gap-2 px-3 py-2 bg-surface-raised rounded-lg text-sm"
            >
              <KinCheckbox
                size="sm"
                :model-value="isMemberSelected(feature.key, member.id)"
                :disabled="(member.family_role || member.role) === 'parent'"
                @update:model-value="toggleMember(feature.key, member.id)"
              />
              <span class="text-ink-primary">{{ member.name }}</span>
              <span
                v-if="(member.family_role || member.role) === 'parent'"
                class="text-xs text-ink-tertiary italic"
              >(always)</span>
            </div>
          </div>
        </div>

        <!-- Mode summary -->
        <p class="text-xs text-ink-tertiary mt-2 ml-13">
          <template v-if="moduleState[feature.key]?.mode === 'all'">All family members can access this.</template>
          <template v-else-if="moduleState[feature.key]?.mode === 'off'">Disabled for everyone.</template>
          <template v-else-if="moduleState[feature.key]?.mode === 'roles'">Parents only.</template>
          <template v-else-if="moduleState[feature.key]?.mode === 'users'">{{ getSelectedNames(feature.key) }}</template>
        </p>
      </KinFlatCard>
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
import KinFlatCard from '@/components/design-system/KinFlatCard.vue'
import KinCheckbox from '@/components/design-system/KinCheckbox.vue'

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
  { mode: 'all', label: 'Everyone', activeClass: 'bg-accent-lavender-bold text-white' },
  { mode: 'roles', label: 'Parents Only', activeClass: 'bg-accent-lavender-bold text-white' },
  { mode: 'users', label: 'Custom', activeClass: 'bg-accent-lavender-bold text-white' },
  { mode: 'off', label: 'Off', activeClass: 'bg-status-failed text-white' },
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
  } catch {
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
