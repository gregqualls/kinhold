<template>
  <aside class="w-64 bg-prussian-500 dark:bg-prussian-900 flex flex-col overflow-hidden">
    <!-- Logo/Family Name -->
    <div class="px-5 py-5 border-b border-prussian-400/30 dark:border-prussian-700/50">
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-xl bg-wisteria-400 flex items-center justify-center shadow-glow">
          <span class="text-white font-bold text-sm">Q</span>
        </div>
        <div>
          <h1 class="text-sm font-bold text-white">{{ familyName }}</h1>
          <p class="text-[10px] text-wisteria-300 uppercase tracking-wider font-medium">Family Hub</p>
        </div>
      </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
      <RouterLink
        v-for="item in filteredNavItems"
        :key="item.name"
        :to="item.path"
        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150"
        :class="isActive(item.path)
          ? 'bg-wisteria-400/20 text-wisteria-300'
          : 'text-lavender-200 hover:bg-prussian-400/40 hover:text-white'"
      >
        <component
          :is="item.icon"
          class="w-5 h-5 flex-shrink-0"
          :class="isActive(item.path) ? 'text-wisteria-400' : 'text-lavender-400'"
        />
        <span>{{ item.label }}</span>
      </RouterLink>
    </nav>

    <!-- User Section -->
    <div class="p-3 border-t border-prussian-400/30 dark:border-prussian-700/50">
      <RouterLink
        to="/settings"
        class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-prussian-400/40 transition-colors"
      >
        <UserAvatar :user="currentUser" size="sm" />
        <div class="flex-1 min-w-0">
          <p class="text-sm font-medium text-white truncate">{{ currentUser?.name }}</p>
          <p class="text-[11px] text-lavender-400 truncate capitalize">{{ currentUser?.family_role || 'member' }}</p>
        </div>
      </RouterLink>
    </div>
  </aside>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
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
} from '@heroicons/vue/24/outline'

const route = useRoute()
const authStore = useAuthStore()
const { family, currentUser, enabledModules } = storeToRefs(authStore)

const familyName = computed(() => family.value?.name || 'Q32 Hub')

const navItems = [
  { label: 'Dashboard', path: '/', icon: HomeIcon, name: 'Dashboard', module: null },
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
