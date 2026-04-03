/**
 * Widget Registry — maps type keys to Vue components and metadata.
 *
 * Each widget type has:
 *  - component: async import function (code-split)
 *  - name: display name for the widget picker
 *  - description: short description
 *  - icon: Heroicon component name
 *  - category: grouping for the widget picker
 *  - defaultConfig: sensible defaults when adding from the picker
 */

export const widgetTypes = {
  'welcome': {
    component: () => import('./widgets/WelcomeWidget.vue'),
    name: 'Welcome',
    description: 'Greeting with date and celebrations',
    icon: 'HandRaisedIcon',
    category: 'special',
    defaultConfig: {
      endpoint: null,
      params: {},
      size: 'lg',
      settings: {},
    },
  },
  'countdown': {
    component: () => import('./widgets/CountdownWidget.vue'),
    name: 'Countdown',
    description: 'Countdown to next featured event',
    icon: 'ClockIcon',
    category: 'special',
    defaultConfig: {
      endpoint: '/api/v1/featured-events/countdown',
      params: {},
      size: 'lg',
      settings: {},
    },
  },
  'stat': {
    component: () => import('./widgets/StatWidget.vue'),
    name: 'Stat Card',
    description: 'Single metric with icon and label',
    icon: 'ChartBarIcon',
    category: 'data',
    defaultConfig: {
      endpoint: '/api/v1/points/bank',
      params: {},
      size: 'sm',
      settings: { valueKey: 'bank', icon: 'trophy', suffix: 'pts' },
    },
  },
  'list': {
    component: () => import('./widgets/ListWidget.vue'),
    name: 'List',
    description: 'Scrollable list of items from any endpoint',
    icon: 'ListBulletIcon',
    category: 'lists',
    defaultConfig: {
      endpoint: '/api/v1/tasks',
      params: { assigned_to: 'me', status: 'pending' },
      size: 'sm',
      settings: { limit: 5, viewAllPath: '/tasks', emptyMessage: 'Nothing to show.' },
    },
  },
  'leaderboard': {
    component: () => import('./widgets/LeaderboardWidget.vue'),
    name: 'Leaderboard',
    description: 'Points leaderboard with podium',
    icon: 'TrophyIcon',
    category: 'lists',
    defaultConfig: {
      endpoint: '/api/v1/points/leaderboard',
      params: {},
      size: 'sm',
      settings: { limit: 5 },
    },
  },
  'feed': {
    component: () => import('./widgets/FeedWidget.vue'),
    name: 'Activity Feed',
    description: 'Chronological activity stream',
    icon: 'BellIcon',
    category: 'lists',
    defaultConfig: {
      endpoint: '/api/v1/points/feed',
      params: {},
      size: 'md',
      settings: { limit: 10 },
    },
  },
  'quick-actions': {
    component: () => import('./widgets/QuickActionsWidget.vue'),
    name: 'Quick Actions',
    description: 'Grid of shortcut buttons',
    icon: 'Squares2X2Icon',
    category: 'special',
    defaultConfig: {
      endpoint: null,
      params: {},
      size: 'sm',
      settings: {
        actions: [
          { label: 'Add Task', icon: 'plus-circle', path: '/tasks' },
          { label: 'Vault', icon: 'lock-closed', path: '/vault' },
          { label: 'Assistant', icon: 'cpu-chip', path: '/chat' },
          { label: 'Calendar', icon: 'calendar', path: '/calendar' },
        ],
      },
    },
  },
  'calendar-mini': {
    component: () => import('./widgets/CalendarMiniWidget.vue'),
    name: "Today's Schedule",
    description: "Compact view of today's events",
    icon: 'CalendarDaysIcon',
    category: 'special',
    defaultConfig: {
      endpoint: '/api/v1/calendar/events',
      params: { range: 'today' },
      size: 'md',
      settings: { limit: 5, viewAllPath: '/calendar' },
    },
  },
  'progress': {
    component: () => import('./widgets/ProgressWidget.vue'),
    name: 'Progress',
    description: 'Progress bar or ring for a metric',
    icon: 'ChartPieIcon',
    category: 'data',
    defaultConfig: {
      endpoint: '/api/v1/tasks',
      params: {},
      size: 'sm',
      settings: { label: 'Task Completion', valueKey: 'completed', maxKey: 'total' },
    },
  },
  'badges': {
    component: () => import('./widgets/BadgesWidget.vue'),
    name: 'Badges',
    description: 'Recent earned badges with hex icons',
    icon: 'ShieldCheckIcon',
    category: 'special',
    defaultConfig: {
      endpoint: '/api/v1/badges/earned',
      params: {},
      size: 'sm',
      settings: {},
    },
  },
  'rewards': {
    component: () => import('./widgets/RewardsWidget.vue'),
    name: 'Rewards',
    description: 'Featured rewards from the shop',
    icon: 'GiftIcon',
    category: 'special',
    defaultConfig: {
      endpoint: '/api/v1/rewards',
      params: {},
      size: 'sm',
      settings: {},
    },
  },
}

/**
 * Get the async Vue component for a widget type.
 */
export function getWidgetComponent(type) {
  return widgetTypes[type]?.component || null
}

/**
 * Get metadata for a widget type.
 */
export function getWidgetMeta(type) {
  const { component: _component, ...meta } = widgetTypes[type] || {}
  return meta
}

/**
 * Get all widget types as an array with their keys.
 */
export function allWidgetTypes() {
  return Object.entries(widgetTypes).map(([key, value]) => ({
    key,
    ...value,
  }))
}

/**
 * Get widget types grouped by category.
 */
export function widgetTypesByCategory() {
  const grouped = {}
  for (const [key, value] of Object.entries(widgetTypes)) {
    const cat = value.category || 'other'
    if (!grouped[cat]) grouped[cat] = []
    grouped[cat].push({ key, ...value })
  }
  return grouped
}
