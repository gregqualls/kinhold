<template>
  <nav
    class="bg-prussian-500 dark:bg-prussian-900 border-t border-prussian-400/30 dark:border-prussian-700/50 flex justify-around shrink-0"
    :style="{ paddingBottom: 'max(0px, env(safe-area-inset-bottom))' }"
  >
    <RouterLink
      v-for="item in filteredNavItems"
      :key="item.name"
      :to="item.path"
      class="flex flex-col items-center justify-center py-2.5 px-3 flex-1 transition-colors"
      :class="{
        'text-wisteria-400': isActive(item.path),
        'text-lavender-400': !isActive(item.path),
      }"
    >
      <component
        :is="isActive(item.path) ? item.iconSolid : item.icon"
        class="w-6 h-6"
      />
      <span class="text-[10px] mt-0.5 font-medium">{{ item.label }}</span>
    </RouterLink>
  </nav>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import { storeToRefs } from 'pinia'
import { useAuthStore } from '@/stores/auth'
import {
  HomeIcon,
  CalendarIcon,
  CheckCircleIcon,
  LockClosedIcon,
  CpuChipIcon,
  TrophyIcon,
} from '@heroicons/vue/24/outline'
import {
  HomeIcon as HomeIconSolid,
  CalendarIcon as CalendarIconSolid,
  CheckCircleIcon as CheckCircleIconSolid,
  LockClosedIcon as LockClosedIconSolid,
  CpuChipIcon as CpuChipIconSolid,
  TrophyIcon as TrophyIconSolid,
} from '@heroicons/vue/24/solid'

const route = useRoute()
const authStore = useAuthStore()
const { enabledModules } = storeToRefs(authStore)

const navItems = [
  { label: 'Home', path: '/dashboard', icon: HomeIcon, iconSolid: HomeIconSolid, name: 'Dashboard', module: null },
  { label: 'Calendar', path: '/calendar', icon: CalendarIcon, iconSolid: CalendarIconSolid, name: 'Calendar', module: 'calendar' },
  { label: 'Tasks', path: '/tasks', icon: CheckCircleIcon, iconSolid: CheckCircleIconSolid, name: 'Tasks', module: 'tasks' },
  { label: 'Points', path: '/points', icon: TrophyIcon, iconSolid: TrophyIconSolid, name: 'Points', module: 'points' },
  { label: 'Assistant', path: '/chat', icon: CpuChipIcon, iconSolid: CpuChipIconSolid, name: 'Chat', module: 'chat' },
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
