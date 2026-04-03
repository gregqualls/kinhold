<template>
  <div class="grid grid-cols-2 gap-2">
    <RouterLink
      v-for="action in actions"
      :key="action.path"
      :to="action.path"
      class="flex flex-col items-center gap-1.5 p-3 rounded-xl bg-lavender-50 dark:bg-prussian-700/50 hover:bg-wisteria-50 dark:hover:bg-wisteria-900/20 transition-colors group"
    >
      <component
        :is="iconFor(action.icon)"
        class="w-5 h-5 text-wisteria-500 dark:text-wisteria-400 group-hover:text-wisteria-600 dark:group-hover:text-wisteria-300 transition-colors"
      />
      <span class="text-xs font-medium text-prussian-600 dark:text-lavender-300 text-center">
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
    { label: 'Vault', icon: 'lock-closed', path: '/vault' },
    { label: 'Assistant', icon: 'cpu-chip', path: '/chat' },
    { label: 'Calendar', icon: 'calendar', path: '/calendar' },
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
