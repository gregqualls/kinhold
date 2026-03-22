<template>
  <div class="p-4 md:p-6 max-w-6xl">
    <!-- Welcome Header -->
    <div class="mb-6">
      <h1 class="text-3xl font-bold text-prussian-500 dark:text-lavender-200">
        {{ greeting }}, {{ currentUser?.name?.split(' ')[0] }}!
      </h1>
      <p class="text-sand-600 dark:text-sand-400">{{ dateMessage }}</p>
    </div>

    <!-- Content Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
      <!-- Today's Events -->
      <BaseCard class="md:col-span-1 lg:col-span-2" shadow="lg">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-lg font-semibold text-prussian-500 dark:text-lavender-200 flex items-center gap-2">
            <CalendarIcon class="w-5 h-5 text-wisteria-600" />
            Today's Events
          </h2>
          <RouterLink to="/calendar" class="text-wisteria-600 dark:text-wisteria-400 text-sm font-medium hover:text-wisteria-500">
            View Calendar
          </RouterLink>
        </div>

        <div v-if="todayEvents.length > 0" class="space-y-2">
          <div
            v-for="event in todayEvents.slice(0, 5)"
            :key="event.id"
            class="flex items-start gap-3 pb-3 border-b border-lavender-200 dark:border-prussian-700 last:border-0 last:pb-0"
          >
            <div class="w-2 h-full bg-wisteria-500 rounded-full mt-1" />
            <div class="flex-1 min-w-0">
              <p class="font-medium text-prussian-500 dark:text-lavender-200 truncate">{{ event.title }}</p>
              <p class="text-xs text-lavender-600 dark:text-lavender-400">{{ formatTime(event.start_date) }}</p>
            </div>
          </div>
        </div>

        <EmptyState
          v-else
          :icon="CalendarIcon"
          title="No events today"
          description="Your calendar is clear!"
        />
      </BaseCard>

      <!-- My Tasks -->
      <BaseCard shadow="lg">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-lg font-semibold text-prussian-500 dark:text-lavender-200 flex items-center gap-2">
            <CheckCircleIcon class="w-5 h-5 text-sand-600" />
            My Tasks
          </h2>
          <RouterLink to="/tasks" class="text-wisteria-600 dark:text-wisteria-400 text-sm font-medium hover:text-wisteria-500">
            All Tasks
          </RouterLink>
        </div>

        <div v-if="myTasks.length > 0" class="space-y-1">
          <div
            v-for="task in myTasks.slice(0, 5)"
            :key="task.id"
            class="flex items-start gap-3 py-2 px-2 rounded-lg hover:bg-lavender-50 dark:hover:bg-prussian-700 transition-colors"
          >
            <button
              @click.stop="toggleTask(task)"
              class="mt-0.5 flex-shrink-0 w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all"
              :class="'border-lavender-300 dark:border-prussian-500 hover:border-wisteria-400 dark:hover:border-wisteria-400'"
            >
            </button>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-medium text-prussian-500 dark:text-lavender-200">
                {{ task.title }}
              </p>
              <div class="flex items-center gap-2 mt-0.5">
                <p v-if="task.due_date" class="text-xs text-lavender-600 dark:text-lavender-400">
                  Due: {{ formatDate(task.due_date) }}
                </p>
                <span v-if="enabledModules.points && task.effective_points" class="text-xs text-wisteria-500 dark:text-wisteria-400 font-medium">
                  {{ task.effective_points }} pts
                </span>
                <span
                  v-if="task.recurrence_label"
                  class="inline-flex items-center gap-1 px-1.5 py-0.5 text-[10px] font-medium rounded-full bg-wisteria-100 text-wisteria-600 dark:bg-wisteria-900/30 dark:text-wisteria-400"
                >
                  <ArrowPathIcon class="w-3 h-3" />
                  {{ task.recurrence_label }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <EmptyState
          v-else
          :icon="CheckCircleIcon"
          title="No tasks assigned"
          description="Check back soon!"
        />
      </BaseCard>

      <!-- Points & Leaderboard -->
      <BaseCard v-if="enabledModules.points" shadow="lg">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-lg font-semibold text-prussian-500 dark:text-lavender-200 flex items-center gap-2">
            <TrophyIcon class="w-5 h-5 text-wisteria-600" />
            Points
          </h2>
          <RouterLink to="/points" class="text-wisteria-600 dark:text-wisteria-400 text-sm font-medium hover:text-wisteria-500">
            View Feed
          </RouterLink>
        </div>

        <div class="mb-3">
          <p class="text-xs text-lavender-500 dark:text-lavender-400 uppercase tracking-wide font-medium">Your Balance</p>
          <p class="text-2xl font-bold text-wisteria-600 dark:text-wisteria-400">{{ pointsStore.bank }} pts</p>
        </div>

        <LeaderboardStrip :leaderboard="pointsStore.leaderboard" />
      </BaseCard>

      <!-- Featured Rewards -->
      <BaseCard v-if="enabledModules.points" shadow="lg">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-lg font-semibold text-prussian-500 dark:text-lavender-200 flex items-center gap-2">
            <GiftIcon class="w-5 h-5 text-sand-500" />
            Rewards Shop
          </h2>
          <RouterLink to="/points/rewards" class="text-wisteria-600 dark:text-wisteria-400 text-sm font-medium hover:text-wisteria-500">
            View All
          </RouterLink>
        </div>

        <FeaturedRewards
          :rewards="pointsStore.rewards"
          :bank="pointsStore.bank"
          :is-parent="isParent"
          @navigate="$router.push('/points/rewards')"
        />
      </BaseCard>

      <!-- Badges Showcase -->
      <BaseCard v-if="enabledModules.badges" shadow="lg">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-lg font-semibold text-prussian-500 dark:text-lavender-200 flex items-center gap-2">
            <ShieldCheckIcon class="w-5 h-5 text-wisteria-600" />
            Badges
          </h2>
          <RouterLink to="/badges" class="text-wisteria-600 dark:text-wisteria-400 text-sm font-medium hover:text-wisteria-500">
            View All
          </RouterLink>
        </div>

        <BadgeShowcase :badges="badgesStore.earnedBadges" />
      </BaseCard>

      <!-- Quick Actions -->
      <BaseCard shadow="lg">
        <h2 class="text-lg font-semibold text-prussian-500 dark:text-lavender-200 mb-4 flex items-center gap-2">
          <SparklesIcon class="w-5 h-5 text-sand-500" />
          Quick Actions
        </h2>

        <div class="space-y-2">
          <RouterLink
            to="/tasks"
            class="flex items-center gap-3 p-3 rounded-lg hover:bg-lavender-100 dark:hover:bg-prussian-700 transition-colors"
          >
            <PlusIcon class="w-5 h-5 text-wisteria-600" />
            <span class="font-medium text-prussian-500 dark:text-lavender-200">Add Task</span>
          </RouterLink>

          <RouterLink
            to="/vault"
            class="flex items-center gap-3 p-3 rounded-lg hover:bg-lavender-100 dark:hover:bg-prussian-700 transition-colors"
          >
            <LockClosedIcon class="w-5 h-5 text-wisteria-600" />
            <span class="font-medium text-prussian-500 dark:text-lavender-200">Access Vault</span>
          </RouterLink>

          <RouterLink
            to="/chat"
            class="flex items-center gap-3 p-3 rounded-lg hover:bg-lavender-100 dark:hover:bg-prussian-700 transition-colors"
          >
            <ChatBubbleLeftIcon class="w-5 h-5 text-wisteria-600" />
            <span class="font-medium text-prussian-500 dark:text-lavender-200">Ask Hub</span>
          </RouterLink>
        </div>
      </BaseCard>

      <!-- Open Tasks (visible to everyone) -->
      <BaseCard v-if="openTasks.length > 0" class="md:col-span-1 lg:col-span-2" shadow="lg">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-lg font-semibold text-prussian-500 dark:text-lavender-200 flex items-center gap-2">
            <UserGroupIcon class="w-5 h-5 text-sand-500" />
            Open Tasks
          </h2>
          <RouterLink to="/tasks" class="text-wisteria-600 dark:text-wisteria-400 text-sm font-medium hover:text-wisteria-500">
            View All
          </RouterLink>
        </div>

        <p class="text-xs text-lavender-500 dark:text-lavender-400 mb-3">Anyone in the family can complete these</p>

        <div class="space-y-1">
          <div
            v-for="task in openTasks.slice(0, 5)"
            :key="task.id"
            class="flex items-start gap-3 py-2 px-2 rounded-lg hover:bg-lavender-50 dark:hover:bg-prussian-700 transition-colors"
          >
            <button
              @click.stop="toggleTask(task)"
              class="mt-0.5 flex-shrink-0 w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all border-lavender-300 dark:border-prussian-500 hover:border-wisteria-400"
            >
            </button>
            <div class="flex-1 min-w-0">
              <p class="font-medium truncate text-prussian-500 dark:text-lavender-200">
                {{ task.title }}
              </p>
              <div class="flex items-center gap-2 mt-0.5">
                <p v-if="task.due_date" class="text-xs text-lavender-600 dark:text-lavender-400">
                  Due: {{ formatDate(task.due_date) }}
                </p>
                <span v-if="enabledModules.points && task.effective_points" class="text-xs text-wisteria-500 dark:text-wisteria-400 font-medium">
                  {{ task.effective_points }} pts
                </span>
                <span
                  v-if="task.recurrence_label"
                  class="inline-flex items-center gap-1 px-1.5 py-0.5 text-[10px] font-medium rounded-full bg-wisteria-100 text-wisteria-600 dark:bg-wisteria-900/30 dark:text-wisteria-400"
                >
                  <ArrowPathIcon class="w-3 h-3" />
                  {{ task.recurrence_label }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </BaseCard>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue'
import { DateTime } from 'luxon'
import { storeToRefs } from 'pinia'
import { useAuthStore } from '@/stores/auth'
import { useCalendarStore } from '@/stores/calendar'
import { useTasksStore } from '@/stores/tasks'
import { usePointsStore } from '@/stores/points'
import { useBadgesStore } from '@/stores/badges'
import BaseCard from '@/components/common/BaseCard.vue'
import EmptyState from '@/components/common/EmptyState.vue'
import LeaderboardStrip from '@/components/points/LeaderboardStrip.vue'
import FeaturedRewards from '@/components/points/FeaturedRewards.vue'
import BadgeShowcase from '@/components/badges/BadgeShowcase.vue'
import {
  ArrowPathIcon,
  CalendarIcon,
  CheckCircleIcon,
  SparklesIcon,
  PlusIcon,
  LockClosedIcon,
  ChatBubbleLeftIcon,
  UserGroupIcon,
  TrophyIcon,
  ShieldCheckIcon,
  GiftIcon,
} from '@heroicons/vue/24/outline'

const authStore = useAuthStore()
const calendarStore = useCalendarStore()
const tasksStore = useTasksStore()
const pointsStore = usePointsStore()
const badgesStore = useBadgesStore()

const { currentUser, isParent, enabledModules } = storeToRefs(authStore)
const { todayEvents } = storeToRefs(calendarStore)
const { tasks } = storeToRefs(tasksStore)

// My Tasks: assigned specifically to me, incomplete
const myTasks = computed(() => {
  const userId = currentUser.value?.id
  return tasks.value.filter((t) => !t.completed_at && !t.is_family_task && t.assigned_to_id === userId)
})

// Open Tasks: family tasks anyone can grab, incomplete
const openTasks = computed(() => {
  return tasks.value.filter((t) => !t.completed_at && t.is_family_task)
})

const toggleTask = async (task) => {
  await tasksStore.toggleComplete(task.id)
}

const now = DateTime.now()

const greeting = computed(() => {
  const hour = now.hour
  if (hour < 12) return 'Good morning'
  if (hour < 18) return 'Good afternoon'
  return 'Good evening'
})

const dateMessage = computed(() => {
  return now.toFormat('EEEE, MMMM d, yyyy')
})

const formatTime = (dateStr) => {
  return DateTime.fromISO(dateStr).toFormat('h:mm a')
}

const formatDate = (dateStr) => {
  return DateTime.fromISO(dateStr).toFormat('MMM d')
}

onMounted(async () => {
  // Fetch today's events
  const endOfDay = now.endOf('day')
  await calendarStore.fetchEvents(now.startOf('day'), endOfDay)

  // Fetch tasks
  await tasksStore.fetchTasks()

  // Fetch points and badges
  if (enabledModules.value.points) {
    pointsStore.fetchBank()
    pointsStore.fetchLeaderboard()
    pointsStore.fetchRewards()
  }
  if (enabledModules.value.badges) {
    badgesStore.fetchEarned()
  }
})
</script>
