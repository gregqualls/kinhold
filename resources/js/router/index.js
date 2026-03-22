import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

// Auth Views
import LoginView from '@/views/auth/LoginView.vue'
import RegisterView from '@/views/auth/RegisterView.vue'

// Public Views
import LandingView from '@/views/LandingView.vue'

// App Views
import DashboardView from '@/views/dashboard/DashboardView.vue'
import CalendarView from '@/views/calendar/CalendarView.vue'
import TasksView from '@/views/tasks/TasksView.vue'
import VaultCategoriesView from '@/views/vault/VaultCategoriesView.vue'
import VaultEntriesView from '@/views/vault/VaultEntriesView.vue'
import VaultEntryView from '@/views/vault/VaultEntryView.vue'
import ChatView from '@/views/chat/ChatView.vue'
import SettingsView from '@/views/settings/SettingsView.vue'

// Points & Badges Views (lazy-loaded)
const PointsFeedView = () => import('@/views/points/PointsFeedView.vue')
const RewardsView = () => import('@/views/points/RewardsView.vue')
const PointsHistoryView = () => import('@/views/points/PointsHistoryView.vue')
const BadgesView = () => import('@/views/badges/BadgesView.vue')

const routes = [
  // Public landing page
  {
    path: '/',
    name: 'Landing',
    component: LandingView,
    meta: { isPublic: true },
  },

  // Auth routes
  {
    path: '/login',
    name: 'Login',
    component: LoginView,
    meta: { requiresGuest: true },
  },
  {
    path: '/register',
    name: 'Register',
    component: RegisterView,
    meta: { requiresGuest: true },
  },

  // App routes
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: DashboardView,
    meta: { requiresAuth: true },
  },
  {
    path: '/calendar',
    name: 'Calendar',
    component: CalendarView,
    meta: { requiresAuth: true, module: 'calendar' },
  },
  {
    path: '/tasks',
    name: 'Tasks',
    component: TasksView,
    meta: { requiresAuth: true, module: 'tasks' },
  },
  {
    path: '/vault',
    name: 'VaultCategories',
    component: VaultCategoriesView,
    meta: { requiresAuth: true, module: 'vault' },
  },
  {
    path: '/vault/:categorySlug',
    name: 'VaultEntries',
    component: VaultEntriesView,
    meta: { requiresAuth: true, module: 'vault' },
  },
  {
    path: '/vault/entry/:id',
    name: 'VaultEntry',
    component: VaultEntryView,
    meta: { requiresAuth: true, module: 'vault' },
  },
  {
    path: '/chat',
    name: 'Chat',
    component: ChatView,
    meta: { requiresAuth: true, module: 'chat' },
  },

  // Points & Rewards
  {
    path: '/points',
    name: 'PointsFeed',
    component: PointsFeedView,
    meta: { requiresAuth: true, module: 'points' },
  },
  {
    path: '/points/rewards',
    name: 'Rewards',
    component: RewardsView,
    meta: { requiresAuth: true, module: 'points' },
  },
  {
    path: '/points/history',
    name: 'PointsHistory',
    component: PointsHistoryView,
    meta: { requiresAuth: true, module: 'points' },
  },

  // Badges
  {
    path: '/badges',
    name: 'Badges',
    component: BadgesView,
    meta: { requiresAuth: true, module: 'badges' },
  },

  // Settings
  {
    path: '/settings',
    name: 'Settings',
    component: SettingsView,
    meta: { requiresAuth: true },
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior() {
    return { top: 0 }
  },
})

// Navigation guards
router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore()

  // Wait for initial auth check to complete before making routing decisions
  if (!authStore.initialAuthChecked) {
    await authStore.initAuth()
  }

  // Public landing page: redirect authenticated users to dashboard
  if (to.meta.isPublic) {
    if (authStore.isAuthenticated) {
      return next({ name: 'Dashboard' })
    }
    return next()
  }

  // Guest-only routes
  if (to.meta.requiresGuest) {
    if (authStore.isAuthenticated) {
      return next({ name: 'Dashboard' })
    }
    return next()
  }

  // Protected routes
  if (to.meta.requiresAuth) {
    if (!authStore.isAuthenticated) {
      return next({ name: 'Login' })
    }
  }

  // Parent-only routes
  if (to.meta.requiresParent) {
    if (!authStore.isParent) {
      return next({ name: 'Dashboard' })
    }
  }

  // Module-gated routes
  if (to.meta.module) {
    const modules = authStore.enabledModules
    if (modules[to.meta.module] === false) {
      return next({ name: 'Dashboard' })
    }
  }

  next()
})

export default router
