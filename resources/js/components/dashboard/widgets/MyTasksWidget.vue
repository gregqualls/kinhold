<template>
  <div>
    <div class="flex items-center justify-between mb-3 flex-shrink-0">
      <h3 class="text-sm font-semibold text-prussian-500 dark:text-lavender-200 flex items-center gap-2">
        <CheckCircleIcon class="w-4 h-4 text-sand-600" />
        {{ config.title || 'My Tasks' }}
      </h3>
      <RouterLink
        to="/tasks"
        class="text-wisteria-600 dark:text-wisteria-400 text-xs font-medium hover:text-wisteria-500"
      >
        View All
      </RouterLink>
    </div>

    <div v-if="loading" :class="columnsClass">
      <div v-for="n in 4" :key="n" class="h-12 bg-lavender-100 dark:bg-prussian-700 rounded-lg animate-pulse"></div>
    </div>

    <div v-else-if="tasks.length === 0" class="flex flex-col items-center justify-center py-6">
      <CheckCircleIcon class="w-8 h-8 text-lavender-400 dark:text-lavender-500 mb-1" />
      <p class="text-sm text-lavender-500 dark:text-lavender-400">All caught up!</p>
    </div>

    <div v-else :class="columnsClass">
      <div
        v-for="task in tasks"
        :key="task.id"
        class="flex items-start gap-2 p-2 rounded-lg hover:bg-lavender-50 dark:hover:bg-prussian-700/50 transition-colors"
        :class="{ 'opacity-40': task._justCompleted }"
      >
        <button
          class="mt-0.5 flex-shrink-0 w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all"
          :class="task._justCompleted
            ? 'border-green-400 bg-green-100 dark:bg-green-900/30 dark:border-green-600'
            : 'border-lavender-300 dark:border-prussian-500 hover:border-wisteria-400 dark:hover:border-wisteria-400'"
          :aria-label="`Mark ${task.title} as complete`"
          @click.stop="toggleTask(task)"
        >
          <CheckIcon v-if="task._justCompleted" class="w-3 h-3 text-green-600 dark:text-green-400" />
        </button>

        <div class="flex-1 min-w-0">
          <p class="text-sm text-prussian-600 dark:text-lavender-200 truncate">{{ task.title }}</p>
          <div class="flex items-center gap-2 mt-0.5">
            <p v-if="task.due_date" class="text-xs text-lavender-500 dark:text-lavender-400">
              {{ formatDate(task.due_date) }}
            </p>
            <span
              v-if="task.points"
              class="text-[10px] font-semibold font-mono text-wisteria-600 dark:text-wisteria-400"
            >
              +{{ task.points }}
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import { DateTime } from 'luxon'
import { useWidgetData } from '@/composables/useWidgetData'
import { CheckCircleIcon, CheckIcon } from '@heroicons/vue/24/solid'
import api from '@/services/api'

const props = defineProps({
  config: { type: Object, required: true },
})

const { data, loading, refresh } = useWidgetData('/api/v1/tasks', {
  assigned_to: 'me',
  status: 'pending',
})

const completedIds = ref(new Set())

const tasks = computed(() => {
  if (!data.value) return []
  const list = data.value.tasks || data.value.data || data.value
  if (!Array.isArray(list)) return []
  return list.slice(0, taskLimit.value).map((t) => ({
    ...t,
    _justCompleted: completedIds.value.has(t.id),
  }))
})

const taskLimit = computed(() => {
  const size = props.config.size || 'sm'
  if (size === 'lg') return 15
  if (size === 'md') return 10
  return 5
})

const columnsClass = computed(() => {
  const size = props.config.size || 'sm'
  if (size === 'lg') return 'grid grid-cols-3 gap-x-4 gap-y-1'
  if (size === 'md') return 'grid grid-cols-2 gap-x-4 gap-y-1'
  return 'space-y-1'
})

async function toggleTask(task) {
  completedIds.value.add(task.id)
  try {
    await api.patch(`/tasks/${task.id}/complete`)
    setTimeout(() => {
      completedIds.value.delete(task.id)
      refresh()
    }, 600)
  } catch {
    completedIds.value.delete(task.id)
  }
}

function formatDate(dateStr) {
  if (!dateStr) return ''
  const dt = DateTime.fromISO(dateStr)
  const today = DateTime.now().startOf('day')
  if (dt.startOf('day').equals(today)) return 'Today'
  if (dt.startOf('day').equals(today.plus({ days: 1 }))) return 'Tomorrow'
  return dt.toLocaleString({ month: 'short', day: 'numeric' })
}
</script>
