<template>
  <div>
    <div class="flex items-center justify-between mb-3">
      <h3 class="text-sm font-semibold text-prussian-500 dark:text-lavender-200 flex items-center gap-2">
        <ShieldCheckIcon class="w-4 h-4 text-wisteria-600" />
        {{ config.title }}
      </h3>
      <RouterLink
        to="/badges"
        class="text-wisteria-600 dark:text-wisteria-400 text-xs font-medium hover:text-wisteria-500"
      >
        View All
      </RouterLink>
    </div>

    <div v-if="loading" class="grid grid-cols-6 gap-1">
      <div v-for="n in 12" :key="n" class="aspect-square bg-lavender-100 dark:bg-prussian-700 rounded animate-pulse"></div>
    </div>

    <div v-else-if="allBadges.length === 0" class="text-center py-3">
      <ShieldCheckIcon class="w-8 h-8 text-lavender-400 dark:text-lavender-500 mx-auto mb-1" />
      <p class="text-xs text-lavender-500 dark:text-lavender-400">No badges available</p>
    </div>

    <div v-else :class="gridClass">
      <div
        v-for="badge in displayBadges"
        :key="badge.id"
        class="flex flex-col items-center"
        :title="badge.is_hidden && !badge.is_earned ? '???' : badge.name"
      >
        <BadgeIcon
          :icon="badge.is_hidden && !badge.is_earned ? 'shield' : badge.icon"
          :color="badge.is_earned ? (badge.color || '#7d57a8') : '#9ca3af'"
          :size="iconSize"
          :locked="!badge.is_earned"
        />
        <p class="text-[9px] text-lavender-500 dark:text-lavender-400 mt-0.5 text-center truncate w-full leading-tight">
          {{ badge.is_hidden && !badge.is_earned ? '???' : badge.name }}
        </p>
      </div>
    </div>

    <p v-if="!loading && earnedCount > 0" class="text-[10px] text-lavender-400 dark:text-lavender-500 mt-2 text-center">
      {{ earnedCount }} / {{ allBadges.length }} earned
    </p>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useWidgetData } from '@/composables/useWidgetData'
import BadgeIcon from '@/components/badges/BadgeIcon.vue'
import { ShieldCheckIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  config: { type: Object, required: true },
})

// Use /api/v1/badges (all badges with earned status) instead of /badges/earned
const { data, loading } = useWidgetData('/api/v1/badges', {})

const allBadges = computed(() => {
  if (!data.value) return []
  const list = data.value.badges || data.value.data || data.value
  if (!Array.isArray(list)) return []
  // Sort: earned first, then unearned
  return [...list].sort((a, b) => {
    if (a.is_earned && !b.is_earned) return -1
    if (!a.is_earned && b.is_earned) return 1
    return 0
  })
})

const earnedCount = computed(() => allBadges.value.filter((b) => b.is_earned).length)

// Show up to 24 badges for sm (6x4), more for larger sizes
const maxDisplay = computed(() => {
  const size = props.config.size || 'sm'
  if (size === 'lg') return 48
  if (size === 'md') return 36
  return 24
})

const displayBadges = computed(() => allBadges.value.slice(0, maxDisplay.value))

const gridClass = computed(() => {
  const size = props.config.size || 'sm'
  if (size === 'lg') return 'grid grid-cols-8 gap-2'
  if (size === 'md') return 'grid grid-cols-6 gap-2'
  return 'grid grid-cols-6 gap-1'
})

const iconSize = computed(() => {
  const size = props.config.size || 'sm'
  if (size === 'lg') return 'md'
  return 'sm'
})
</script>
