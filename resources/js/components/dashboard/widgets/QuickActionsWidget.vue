<template>
  <div class="grid grid-cols-2 gap-2">
    <RouterLink
      v-for="action in actions"
      :key="action.path"
      :to="action.path"
      class="flex flex-col items-center gap-1.5 p-3 rounded-card bg-surface-raised border border-border-subtle hover:border-accent-lavender-bold/40 hover:shadow-resting transition-all group"
    >
      <div class="w-8 h-8 rounded-full bg-accent-lavender-soft/50 flex items-center justify-center">
        <component
          :is="iconFor(action.icon)"
          class="w-4 h-4 text-accent-lavender-bold"
        />
      </div>
      <span class="text-xs font-medium text-ink-primary text-center mt-1">
        {{ action.label }}
      </span>
    </RouterLink>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import {
  PlusCircleIcon,
  LockClosedIcon,
  CpuChipIcon,
  CalendarIcon,
  TrophyIcon,
  GiftIcon,
  ShieldCheckIcon,
  HomeIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  config: { type: Object, required: true },
})

const actions = computed(() => {
  return props.config.settings?.actions || [
    { label: 'Add Task', icon: 'plus-circle', path: '/tasks' },
    { label: 'Calendar', icon: 'calendar', path: '/calendar' },
    { label: 'Assistant', icon: 'cpu-chip', path: '/chat' },
    { label: 'Vault', icon: 'lock-closed', path: '/vault' },
    { label: 'Points', icon: 'trophy', path: '/points' },
    { label: 'Rewards', icon: 'gift', path: '/points/rewards' },
  ]
})

const iconMap = {
  'plus-circle': PlusCircleIcon,
  'lock-closed': LockClosedIcon,
  'cpu-chip': CpuChipIcon,
  'calendar': CalendarIcon,
  'trophy': TrophyIcon,
  'gift': GiftIcon,
  'shield-check': ShieldCheckIcon,
  'home': HomeIcon,
}

function iconFor(name) {
  return iconMap[name] || PlusCircleIcon
}
</script>
