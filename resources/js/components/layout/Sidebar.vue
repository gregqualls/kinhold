<template>
  <aside
    class="bg-prussian-500 dark:bg-prussian-900 flex flex-col overflow-hidden rounded-r-2xl transition-all duration-300"
    :class="isCollapsed ? 'w-[72px]' : 'w-64'"
  >
    <!-- Logo/Family Name -->
    <div class="px-3 py-5" :class="isCollapsed ? 'flex justify-center' : 'px-5'">
      <div class="flex items-center" :class="isCollapsed ? 'justify-center' : 'gap-3'">
        <div
          class="w-9 h-9 rounded-[10px] overflow-hidden cursor-pointer select-none flex-shrink-0"
          @click="onLogoClick"
        >
          <img src="/images/logo-100.png" alt="Kinhold" class="w-full h-full object-cover" />
        </div>
        <div v-if="!isCollapsed" class="min-w-0">
          <h1 class="text-sm font-heading font-bold text-white truncate">{{ familyName }}</h1>
          <p class="text-[10px] text-wisteria-300 uppercase tracking-wider font-medium">Family Hub</p>
        </div>
      </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto px-2 py-2 space-y-1" :class="isCollapsed ? 'px-2' : 'px-3'">
      <RouterLink
        v-for="item in filteredNavItems"
        :key="item.name"
        :to="item.path"
        class="flex items-center rounded-[12px] text-sm font-medium transition-all duration-150"
        :class="[
          isCollapsed ? 'justify-center px-0 py-2.5' : 'gap-3 px-3 py-2.5',
          isActive(item.path)
            ? 'bg-wisteria-400/15 text-wisteria-400'
            : 'text-lavender-200 hover:bg-prussian-400/40 hover:text-white'
        ]"
        :title="isCollapsed ? item.label : undefined"
      >
        <component
          :is="item.icon"
          class="w-5 h-5 flex-shrink-0"
          :class="isActive(item.path) ? 'text-wisteria-400' : 'text-lavender-400'"
        />
        <span v-if="!isCollapsed">{{ item.label }}</span>
      </RouterLink>
    </nav>

    <!-- Collapse Toggle -->
    <div class="px-2 py-2" :class="isCollapsed ? 'flex justify-center' : 'px-3'">
      <button
        @click="toggleCollapsed"
        class="flex items-center justify-center w-full py-2 rounded-[10px] text-lavender-400 hover:bg-prussian-400/40 hover:text-white transition-colors"
        :class="isCollapsed ? 'px-0' : 'gap-2 px-3'"
        :title="isCollapsed ? 'Expand sidebar' : 'Collapse sidebar'"
      >
        <ChevronDoubleLeftIcon v-if="!isCollapsed" class="w-4 h-4" />
        <ChevronDoubleRightIcon v-else class="w-4 h-4" />
        <span v-if="!isCollapsed" class="text-xs">Collapse</span>
      </button>
    </div>

    <!-- User Section -->
    <div class="p-2 border-t border-prussian-400/20 dark:border-prussian-700/50" :class="isCollapsed ? 'px-2' : 'p-3'">
      <RouterLink
        to="/settings"
        class="flex items-center rounded-[12px] hover:bg-prussian-400/40 transition-colors"
        :class="isCollapsed ? 'justify-center py-2.5 px-0' : 'gap-3 px-3 py-2.5'"
        :title="isCollapsed ? currentUser?.name : undefined"
      >
        <UserAvatar :user="currentUser" size="sm" class="flex-shrink-0" />
        <div v-if="!isCollapsed" class="flex-1 min-w-0">
          <p class="text-sm font-medium text-white truncate">{{ currentUser?.name }}</p>
          <p class="text-[11px] text-lavender-400 truncate capitalize">{{ currentUser?.family_role || 'member' }}</p>
        </div>
      </RouterLink>
      <button
        @click="handleLogout"
        class="flex items-center mt-1 w-full rounded-[12px] text-sm text-lavender-400 hover:bg-red-500/20 hover:text-red-300 transition-colors"
        :class="isCollapsed ? 'justify-center py-2 px-0' : 'gap-3 px-3 py-2'"
        :title="isCollapsed ? 'Sign Out' : undefined"
      >
        <ArrowRightOnRectangleIcon class="w-5 h-5 flex-shrink-0" />
        <span v-if="!isCollapsed">Sign Out</span>
      </button>
    </div>
  </aside>
</template>

<script setup>
import { computed, ref, inject } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { storeToRefs } from 'pinia'
import { useAuthStore } from '@/stores/auth'
import UserAvatar from '@/components/common/UserAvatar.vue'
import {
  HomeIcon,
  CalendarIcon,
  CheckCircleIcon,
  LockClosedIcon,
  ChatBubbleLeftIcon,
  TrophyIcon,
  ShieldCheckIcon,
  ArrowRightOnRectangleIcon,
  ChevronDoubleLeftIcon,
  ChevronDoubleRightIcon,
} from '@heroicons/vue/24/outline'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const { family, currentUser, enabledModules } = storeToRefs(authStore)

// Collapse state with localStorage persistence
const isCollapsed = ref(localStorage.getItem('kinhold-sidebar-collapsed') === 'true')

const toggleCollapsed = () => {
  isCollapsed.value = !isCollapsed.value
  localStorage.setItem('kinhold-sidebar-collapsed', isCollapsed.value.toString())
}

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
  { label: 'Dashboard', path: '/dashboard', icon: HomeIcon, name: 'Dashboard', module: null },
  { label: 'Calendar', path: '/calendar', icon: CalendarIcon, name: 'Calendar', module: 'calendar' },
  { label: 'Tasks', path: '/tasks', icon: CheckCircleIcon, name: 'Tasks', module: 'tasks' },
  { label: 'Vault', path: '/vault', icon: LockClosedIcon, name: 'Vault', module: 'vault' },
  { label: 'Chat', path: '/chat', icon: ChatBubbleLeftIcon, name: 'Chat', module: 'chat' },
  { label: 'Points', path: '/points', icon: TrophyIcon, name: 'Points', module: 'points' },
  { label: 'Badges', path: '/badges', icon: ShieldCheckIcon, name: 'Badges', module: 'badges' },
]

const filteredNavItems = computed(() => {
  return navItems.filter(item => {
    if (!item.module) return true
    return enabledModules.value[item.module] !== false
  })
})

const isActive = (path) => {
  if (path === '/') return route.path === '/'
  return route.path.startsWith(path)
}
</script>
