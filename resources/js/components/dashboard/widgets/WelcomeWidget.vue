<template>
  <div>
    <h1 class="text-xl sm:text-2xl font-semibold font-heading text-ink-primary tracking-tight">
      {{ greeting }}, {{ firstName }}!
    </h1>
    <p class="text-ink-tertiary text-xs mt-1">{{ dateMessage }}</p>

    <!-- Celebration banners -->
    <CelebrationBanner
      v-if="hasBirthdays || isHoliday"
      :show-birthday="hasBirthdays"
      :is-my-birthday="isMyBirthday"
      :birthday-members="birthdayMembers"
      :show-holiday="isHoliday"
      :holiday="todayHoliday"
      class="mt-3"
    />
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { DateTime } from 'luxon'
import { useAuthStore } from '@/stores/auth'
import { useCelebrations } from '@/composables/useCelebrations'
import CelebrationBanner from '@/components/common/CelebrationBanner.vue'

const authStore = useAuthStore()
const { birthdayMembers, hasBirthdays, isMyBirthday, todayHoliday, isHoliday } = useCelebrations()

const firstName = computed(() => authStore.currentUser?.name?.split(' ')[0] || 'there')

const greeting = computed(() => {
  const hour = DateTime.now().hour
  if (hour < 12) return 'Good morning'
  if (hour < 17) return 'Good afternoon'
  return 'Good evening'
})

const dateMessage = computed(() => {
  return DateTime.now().toLocaleString({ weekday: 'long', month: 'long', day: 'numeric' })
})
</script>
