<template>
  <div>
    <!-- Fixed header -->
    <div class="flex items-center justify-between mb-3">
      <h3 class="text-sm font-semibold text-ink-primary flex items-center gap-2">
        <ShieldCheckIcon class="w-4 h-4 text-accent-lavender-bold" />
        {{ config.title || 'Achievement Badges' }}
      </h3>
      <RouterLink
        to="/badges"
        class="text-xs font-medium text-accent-lavender-bold hover:opacity-80 transition-opacity"
      >
        View All
      </RouterLink>
    </div>

    <!-- Loading: mirrors the real grid shape so there's no layout jump
         on resolve. Each cell is an icon-shaped circle plus a caption bar. -->
    <div v-if="loading" :class="gridClass">
      <div v-for="n in maxDisplay" :key="n" class="flex flex-col items-center">
        <KinSkeleton shape="circle" :width="iconSize === 'md' ? '56px' : '40px'" />
        <KinSkeleton shape="rect" width="80%" height="10px" rounded="2px" class="mt-0.5" />
      </div>
    </div>

    <!-- Empty -->
    <KinEmptyState
      v-else-if="allBadges.length === 0"
      :icon="ShieldCheckIcon"
      title="No badges available"
      size="sm"
      accent-color="lavender"
    />

    <!-- Badge grid -->
    <div v-else>
      <div :class="gridClass">
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
          <p class="text-[9px] text-ink-tertiary mt-0.5 text-center truncate w-full leading-tight">
            {{ badge.is_hidden && !badge.is_earned ? '???' : badge.name }}
          </p>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <p v-if="!loading && earnedCount > 0" class="text-[10px] text-ink-tertiary mt-2 text-center">
      {{ earnedCount }} / {{ allBadges.length }} earned
    </p>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useWidgetData } from '@/composables/useWidgetData'
import BadgeIcon from '@/components/badges/BadgeIcon.vue'
import { ShieldCheckIcon } from '@heroicons/vue/24/outline'
import KinSkeleton from '@/components/design-system/KinSkeleton.vue'
import KinEmptyState from '@/components/design-system/KinEmptyState.vue'

const props = defineProps({
  config: { type: Object, required: true },
})

const { data, loading } = useWidgetData('/api/v1/badges', {})

const allBadges = computed(() => {
  if (!data.value) return []
  const list = data.value.badges || data.value.data || data.value
  if (!Array.isArray(list)) return []
  return [...list].sort((a, b) => {
    if (a.is_earned && !b.is_earned) return -1
    if (!a.is_earned && b.is_earned) return 1
    return 0
  })
})

const earnedCount = computed(() => allBadges.value.filter((b) => b.is_earned).length)

const maxDisplay = computed(() => {
  const size = props.config.size || 'sm'
  if (size === 'md') return 36
  return 24
})

const displayBadges = computed(() => allBadges.value.slice(0, maxDisplay.value))

const gridClass = computed(() => {
  const size = props.config.size || 'sm'
  if (size === 'md') return 'grid grid-cols-8 gap-2'
  return 'grid grid-cols-6 gap-1'
})

const iconSize = computed(() => {
  const size = props.config.size || 'sm'
  if (size === 'md') return 'md'
  return 'sm'
})
</script>
