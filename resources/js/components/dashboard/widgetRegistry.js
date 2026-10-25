/**
 * Widget Registry — maps type keys to purpose-built Vue components.
 *
 * Each widget type has:
 *  - component: async import (code-split)
 *  - name: display name
 *  - description: short description for picker
 *  - icon: Heroicon name for picker
 *  - category: grouping for picker
 *  - supportedSizes: which sizes this widget renders well at
 *  - cardless: true for widgets rendered without a KinFlatCard wrapper
 *    (welcome, countdown). Carded widgets size to their content.
 *  - defaultSize: size to use when adding from picker
 */

export const widgetTypes = {
  'welcome': {
    component: () => import('./widgets/WelcomeWidget.vue'),
    name: 'Welcome',
    description: 'Greeting with date and celebrations',
    icon: 'HandRaisedIcon',
    category: 'general',
    supportedSizes: ['lg'],
    cardless: true,
    defaultSize: 'lg',
  },
  'countdown': {
    component: () => import('./widgets/CountdownWidget.vue'),
    name: 'Countdown',
    description: 'Countdown to next featured event',
    icon: 'ClockIcon',
    category: 'general',
    supportedSizes: ['lg'],
    cardless: true,
    defaultSize: 'lg',
  },
  'my-tasks': {
    component: () => import('./widgets/MyTasksWidget.vue'),
    name: 'My Tasks',
    description: 'Your assigned tasks with checkboxes',
    icon: 'CheckCircleIcon',
    category: 'tasks',
    supportedSizes: ['sm', 'md', 'lg'],
    defaultSize: 'sm',
  },
  'family-tasks': {
    component: () => import('./widgets/FamilyTasksWidget.vue'),
    name: 'Family Tasks',
    description: 'Open tasks anyone can complete',
    icon: 'UserGroupIcon',
    category: 'tasks',
    supportedSizes: ['sm', 'md'],
    defaultSize: 'md',
  },
  'todays-schedule': {
    component: () => import('./widgets/TodaysScheduleWidget.vue'),
    name: "Today's Schedule",
    description: "Today's events with times",
    icon: 'CalendarDaysIcon',
    category: 'calendar',
    supportedSizes: ['sm', 'md'],
    defaultSize: 'md',
  },
  'points-summary': {
    component: () => import('./widgets/PointsSummaryWidget.vue'),
    name: 'Points Balance',
    description: 'Your current point balance',
    icon: 'TrophyIcon',
    category: 'points',
    supportedSizes: ['sm'],
    defaultSize: 'sm',
  },
  'leaderboard': {
    component: () => import('./widgets/LeaderboardWidget.vue'),
    name: 'Leaderboard',
    description: 'Family points ranking with podium',
    icon: 'TrophyIcon',
    category: 'points',
    supportedSizes: ['sm', 'md'],
    defaultSize: 'sm',
  },
  'activity-feed': {
    component: () => import('./widgets/ActivityFeedWidget.vue'),
    name: 'Activity Feed',
    description: 'Recent point transactions',
    icon: 'BellIcon',
    category: 'points',
    supportedSizes: ['sm', 'md'],
    defaultSize: 'sm',
  },
  'rewards-shop': {
    component: () => import('./widgets/RewardsWidget.vue'),
    name: 'Rewards Shop',
    description: 'Featured rewards with prices',
    icon: 'GiftIcon',
    category: 'rewards',
    supportedSizes: ['sm', 'md'],
    defaultSize: 'sm',
  },
  'badge-collection': {
    component: () => import('./widgets/BadgesWidget.vue'),
    name: 'Achievement Badges',
    description: 'Badge collection with earned status',
    icon: 'ShieldCheckIcon',
    category: 'badges',
    supportedSizes: ['sm', 'md'],
    defaultSize: 'sm',
  },
  'filtered-tasks': {
    component: () => import('./widgets/FilteredTasksWidget.vue'),
    name: 'Filtered Tasks',
    description: 'Tasks filtered by tags and date range',
    icon: 'FunnelIcon',
    category: 'tasks',
    supportedSizes: ['sm', 'md', 'lg'],
    defaultSize: 'sm',
    configurable: true,
  },
  'quick-actions': {
    component: () => import('./widgets/QuickActionsWidget.vue'),
    name: 'Quick Actions',
    description: 'Navigation shortcuts',
    icon: 'Squares2X2Icon',
    category: 'general',
    supportedSizes: ['sm'],
    defaultSize: 'sm',
  },
}

/**
 * Get the async Vue component for a widget type.
 */
export function getWidgetComponent(type) {
  return widgetTypes[type]?.component || null
}

/**
 * Returns true for widgets that render without the KinFlatCard wrapper
 * (welcome, countdown). All other widgets size to their content.
 */
export function isCardlessWidget(type) {
  return !!widgetTypes[type]?.cardless
}

/**
 * Get supported sizes for a widget type.
 */
export function getSupportedSizes(type) {
  return widgetTypes[type]?.supportedSizes || ['sm']
}

/**
 * Get all widget types as an array with their keys (for picker).
 */
export function allWidgetTypes() {
  return Object.entries(widgetTypes).map(([key, value]) => ({
    key,
    ...value,
  }))
}

/**
 * Get widget types grouped by category (for picker).
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
