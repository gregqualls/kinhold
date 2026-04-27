<template>
  <div class="px-6 py-3 flex items-center justify-end gap-4">
    <!-- Dark mode toggle -->
    <button
      type="button"
      class="p-2 rounded-[10px] text-ink-tertiary hover:bg-surface-sunken transition-colors"
      :title="isDark ? 'Switch to light mode' : 'Switch to dark mode'"
      aria-label="Toggle dark mode"
      @click="toggleDarkMode"
    >
      <MoonIcon v-if="!isDark" class="w-5 h-5" />
      <SunIcon v-else class="w-5 h-5" />
    </button>

    <!-- Family avatars -->
    <div class="flex -space-x-1.5">
      <KinAvatar
        v-for="member in displayedMembers"
        :key="member.id"
        :src="member.avatar_url"
        :name="member.name"
        size="sm"
        color="lavender"
        class="ring-2 ring-surface-raised rounded-full"
      />
      <div
        v-if="overflowCount > 0"
        class="w-8 h-8 rounded-full bg-surface-sunken flex items-center justify-center text-xs font-medium text-ink-secondary ring-2 ring-surface-raised"
      >
        +{{ overflowCount }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { storeToRefs } from 'pinia'
import { useAuthStore } from '@/stores/auth'
import { useDarkMode } from '@/composables/useDarkMode'
import KinAvatar from '@/components/design-system/KinAvatar.vue'
import { SunIcon, MoonIcon } from '@heroicons/vue/24/outline'

const authStore = useAuthStore()
const { familyMembers } = storeToRefs(authStore)
const { isDark, toggle: toggleDarkMode } = useDarkMode()

const displayedMembers = computed(() => (familyMembers.value || []).slice(0, 4))
const overflowCount = computed(() => Math.max(0, (familyMembers.value || []).length - 4))
</script>
