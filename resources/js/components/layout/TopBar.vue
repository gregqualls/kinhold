<template>
  <div class="bg-white/80 dark:bg-prussian-800/80 backdrop-blur-sm px-6 py-3 flex items-center justify-between border-b border-lavender-200 dark:border-prussian-700">
    <!-- Left: Page title -->
    <h1 class="text-lg font-semibold text-prussian-500 dark:text-lavender-200">{{ pageTitle }}</h1>

    <!-- Right: Dark mode toggle + Family avatars -->
    <div class="flex items-center gap-4">
      <!-- Dark mode toggle -->
      <button
        @click="toggleDarkMode"
        class="p-2 rounded-lg text-lavender-500 dark:text-lavender-400 hover:bg-lavender-100 dark:hover:bg-prussian-700 transition-colors"
        :title="isDark ? 'Switch to light mode' : 'Switch to dark mode'"
      >
        <MoonIcon v-if="!isDark" class="w-5 h-5" />
        <SunIcon v-else class="w-5 h-5" />
      </button>

      <div class="flex -space-x-1.5">
        <UserAvatar
          v-for="member in (familyMembers || []).slice(0, 4)"
          :key="member.id"
          :user="member"
          size="sm"
          class="ring-2 ring-white dark:ring-prussian-800"
        />
        <div
          v-if="(familyMembers || []).length > 4"
          class="w-8 h-8 rounded-full bg-lavender-200 dark:bg-prussian-600 flex items-center justify-center text-xs font-medium text-prussian-500 dark:text-lavender-200 ring-2 ring-white dark:ring-prussian-800"
        >
          +{{ familyMembers.length - 4 }}
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import { storeToRefs } from 'pinia'
import { useAuthStore } from '@/stores/auth'
import { useDarkMode } from '@/composables/useDarkMode'
import UserAvatar from '@/components/common/UserAvatar.vue'
import { SunIcon, MoonIcon } from '@heroicons/vue/24/outline'

const route = useRoute()
const authStore = useAuthStore()
const { familyMembers } = storeToRefs(authStore)
const { isDark, toggle: toggleDarkMode } = useDarkMode()

const pageTitle = computed(() => {
  const titles = {
    Dashboard: 'Dashboard',
    Calendar: 'Calendar',
    TaskLists: 'Tasks',
    TaskListDetail: 'Tasks',
    VaultCategories: 'Vault',
    VaultEntries: 'Vault',
    VaultEntry: 'Vault',
    Chat: 'Chat',
    PointsFeed: 'Points',
    Rewards: 'Rewards',
    PointsHistory: 'Points History',
    Badges: 'Badges',
    Settings: 'Settings',
  }
  return titles[route.name] || 'Q32 Hub'
})
</script>
