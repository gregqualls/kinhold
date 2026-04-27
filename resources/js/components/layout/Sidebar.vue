<template>
  <KinSidebar
    v-model:collapsed="isCollapsed"
    :brand="familyName"
    :items="filteredNavItems"
    :active-key="activeKey"
    class="rounded-r-2xl"
  >
    <template #brand-icon>
      <div
        class="w-7 h-7 rounded-[8px] overflow-hidden cursor-pointer select-none flex-shrink-0"
        @click="onLogoClick"
      >
        <img src="/images/logo-100.png" alt="Kinhold" class="w-full h-full object-cover" />
      </div>
    </template>

    <template #user="{ collapsed }">
      <div class="w-full flex flex-col gap-1">
        <RouterLink
          to="/settings"
          class="flex items-center rounded-[12px] hover:bg-surface-sunken transition-colors"
          :class="collapsed ? 'justify-center py-2 px-0' : 'gap-3 px-2 py-2'"
          :title="collapsed ? currentUser?.name : undefined"
        >
          <UserAvatar :user="currentUser" size="sm" />
          <div v-if="!collapsed" class="flex-1 min-w-0">
            <p class="text-sm font-medium text-ink-primary truncate">{{ currentUser?.name }}</p>
            <p class="text-[11px] text-ink-tertiary truncate capitalize">{{ currentUser?.family_role || 'member' }}</p>
          </div>
        </RouterLink>
        <button
          type="button"
          class="flex items-center w-full rounded-[12px] text-sm text-ink-tertiary hover:bg-status-failed/10 hover:text-status-failed transition-colors"
          :class="collapsed ? 'justify-center py-2 px-0' : 'gap-3 px-2 py-2'"
          :title="collapsed ? 'Sign Out' : undefined"
          @click="handleLogout"
        >
          <ArrowRightOnRectangleIcon class="w-5 h-5 flex-shrink-0" />
          <span v-if="!collapsed">Sign Out</span>
        </button>
      </div>
    </template>
  </KinSidebar>
</template>

<script setup>
import { computed, ref, inject, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { storeToRefs } from 'pinia'
import { useAuthStore } from '@/stores/auth'
import KinSidebar from '@/components/design-system/KinSidebar.vue'
import UserAvatar from '@/components/common/UserAvatar.vue'
import {
  HomeIcon,
  CalendarIcon,
  CheckCircleIcon,
  ClipboardDocumentListIcon,
  LockClosedIcon,
  CpuChipIcon,
  TrophyIcon,
  GiftIcon,
  ShieldCheckIcon,
  FireIcon,
  ArrowRightOnRectangleIcon,
} from '@heroicons/vue/24/outline'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const { family, currentUser, enabledModules } = storeToRefs(authStore)

const isCollapsed = ref(localStorage.getItem('kinhold-sidebar-collapsed') === 'true')

watch(isCollapsed, (v) => {
  localStorage.setItem('kinhold-sidebar-collapsed', v.toString())
})

const handleLogout = async () => {
  await authStore.logout()
  router.push('/login')
}

const familyName = computed(() => family.value?.name || 'Kinhold')

const easterEggsRef = inject('easterEggs', null)
const onLogoClick = () => {
  easterEggsRef?.value?.handleLogoClick()
}

const navItems = [
  { key: 'dashboard', label: 'Dashboard', to: '/dashboard', icon: HomeIcon, module: null },
  { key: 'chat',      label: 'Assistant', to: '/chat',      icon: CpuChipIcon, module: 'chat' },
  { key: 'calendar',  label: 'Calendar',  to: '/calendar',  icon: CalendarIcon, module: 'calendar' },
  { key: 'tasks',     label: 'Tasks',     to: '/tasks',     icon: CheckCircleIcon, module: 'tasks' },
  { key: 'food',      label: 'Meals',     to: '/food',      icon: FireIcon, module: 'food' },
  { key: 'shopping',  label: 'Shopping',  to: '/shopping',  icon: ClipboardDocumentListIcon, module: 'food' },
  { key: 'points',    label: 'Points',    to: '/points',    icon: TrophyIcon, module: 'points' },
  { key: 'rewards',   label: 'Rewards',   to: '/points/rewards', icon: GiftIcon, module: 'points' },
  { key: 'badges',    label: 'Badges',    to: '/badges',    icon: ShieldCheckIcon, module: 'badges' },
  { key: 'vault',     label: 'Vault',     to: '/vault',     icon: LockClosedIcon, module: 'vault' },
]

const filteredNavItems = computed(() =>
  navItems.filter(item => !item.module || enabledModules.value[item.module] !== false)
)

const activeKey = computed(() => {
  const exact = filteredNavItems.value.find(item => item.to === route.path)
  if (exact) return exact.key
  // longest-prefix wins so /points/rewards beats /points
  const prefix = filteredNavItems.value
    .filter(item => route.path.startsWith(item.to))
    .sort((a, b) => b.to.length - a.to.length)[0]
  return prefix?.key ?? null
})
</script>
