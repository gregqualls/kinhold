import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

// Auth Views — eagerly loaded (first thing users see)
import LoginView from '@/views/auth/LoginView.vue'
import RegisterView from '@/views/auth/RegisterView.vue'

// Public Views
const PrivacyPolicyView = () => import('@/views/PrivacyPolicyView.vue')
const TermsView = () => import('@/views/TermsView.vue')

// Dashboard — eagerly loaded (main entry point after login)
import DashboardView from '@/views/dashboard/DashboardView.vue'

// App Views — lazy-loaded (only loaded when navigated to)
const CalendarView = () => import('@/views/calendar/CalendarView.vue')
const TasksView = () => import('@/views/tasks/TasksView.vue')
const VaultCategoriesView = () => import('@/views/vault/VaultCategoriesView.vue')
const VaultEntriesView = () => import('@/views/vault/VaultEntriesView.vue')
const VaultEntryView = () => import('@/views/vault/VaultEntryView.vue')
const ChatView = () => import('@/views/chat/ChatView.vue')
const SettingsView = () => import('@/views/settings/SettingsView.vue')

// Points, Badges, Rewards — lazy-loaded
const PointsFeedView = () => import('@/views/points/PointsFeedView.vue')
const RewardsView = () => import('@/views/points/RewardsView.vue')
const PointsHistoryView = () => import('@/views/points/PointsHistoryView.vue')
const BadgesView = () => import('@/views/badges/BadgesView.vue')
const OnboardingView = () => import('@/views/onboarding/OnboardingView.vue')

// Food — lazy-loaded
const FoodView = () => import('@/views/food/FoodView.vue')
const RecipeDetailView = () => import('@/views/food/RecipeDetailView.vue')
const ShoppingView = () => import('@/views/food/ShoppingTab.vue')

const NotFoundView = () => import('@/views/NotFoundView.vue')
const DemoView = () => import('@/views/DemoView.vue')

// Design System — always open, accessible as a community reference for
// contributors. Lives outside auth so the component library can be linked to
// from docs, PRs, and local dev without needing a logged-in session.
const DesignSystemView = () => import('@/views/design-system/DesignSystemView.vue')

const routes = [
  { path: '/', redirect: { name: 'Login' } },
  { path: '/privacy', name: 'Privacy', component: PrivacyPolicyView, meta: { isOpen: true } },
  { path: '/terms', name: 'Terms', component: TermsView, meta: { isOpen: true } },
  { path: '/login', name: 'Login', component: LoginView, meta: { requiresGuest: true } },
  { path: '/register', name: 'Register', component: RegisterView, meta: { requiresGuest: true } },
  { path: '/demo', name: 'Demo', component: DemoView, meta: { requiresGuest: true } },
  { path: '/onboarding', name: 'Onboarding', component: OnboardingView, meta: { requiresAuth: true, isOnboarding: true } },
  { path: '/dashboard', name: 'Dashboard', component: DashboardView, meta: { requiresAuth: true } },
  { path: '/calendar', name: 'Calendar', component: CalendarView, meta: { requiresAuth: true, module: 'calendar' } },
  { path: '/tasks', name: 'Tasks', component: TasksView, meta: { requiresAuth: true, module: 'tasks' } },
  { path: '/vault', name: 'VaultCategories', component: VaultCategoriesView, meta: { requiresAuth: true, module: 'vault' } },
  { path: '/vault/:categorySlug', name: 'VaultEntries', component: VaultEntriesView, meta: { requiresAuth: true, module: 'vault' } },
  { path: '/vault/entry/:id', name: 'VaultEntry', component: VaultEntryView, meta: { requiresAuth: true, module: 'vault' } },
  { path: '/chat', name: 'Chat', component: ChatView, meta: { requiresAuth: true, module: 'chat' } },
  { path: '/points', name: 'PointsFeed', component: PointsFeedView, meta: { requiresAuth: true, module: 'points' } },
  { path: '/points/rewards', name: 'Rewards', component: RewardsView, meta: { requiresAuth: true, module: 'points' } },
  { path: '/points/history', name: 'PointsHistory', component: PointsHistoryView, meta: { requiresAuth: true, module: 'points' } },
  { path: '/badges', name: 'Badges', component: BadgesView, meta: { requiresAuth: true, module: 'badges' } },
  { path: '/food', name: 'Food', component: FoodView, meta: { requiresAuth: true, module: 'food' } },
  { path: '/food/recipes/:id', name: 'RecipeDetail', component: RecipeDetailView, meta: { requiresAuth: true, module: 'food' } },
  { path: '/shopping', name: 'Shopping', component: ShoppingView, meta: { requiresAuth: true, module: 'food' } },
  { path: '/settings', name: 'Settings', component: SettingsView, meta: { requiresAuth: true } },
  { path: '/design-system/:slug?', name: 'DesignSystem', component: DesignSystemView, meta: { isOpen: true, isDesignSystem: true } },
  { path: '/:pathMatch(.*)*', name: 'NotFound', component: NotFoundView, meta: { isOpen: true } },
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

  if (!authStore.initialAuthChecked) {
    await authStore.initAuth()
  }

  if (to.meta.isOpen) return next()

  if (to.name === 'Login' && authStore.appConfig?.first_boot) {
    return next({ name: 'Register' })
  }

  if (to.meta.requiresGuest) {
    return authStore.isAuthenticated ? next({ name: 'Dashboard' }) : next()
  }

  if (to.meta.requiresAuth) {
    if (!authStore.isAuthenticated) return next({ name: 'Login' })
    if (!to.meta.isOnboarding && authStore.user && !authStore.user.onboarding_completed_at) {
      return next({ name: 'Onboarding' })
    }
  }

  if (to.meta.requiresParent && !authStore.isParent) {
    return next({ name: 'Dashboard' })
  }

  if (to.meta.module && !authStore.userCanAccessModule(to.meta.module)) {
    return next({ name: 'Dashboard' })
  }

  next()
})

export default router
