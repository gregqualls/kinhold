<template>
  <div class="flex flex-col h-full">
    <div class="flex items-center justify-between mb-3 flex-shrink-0">
      <h3 class="text-sm font-semibold text-ink-primary flex items-center gap-2">
        <UserGroupIcon class="w-4 h-4 text-accent-lavender-bold" />
        {{ config.title || 'Family Tasks' }}
      </h3>
      <RouterLink
        to="/tasks"
        class="text-xs font-medium text-accent-lavender-bold hover:opacity-80 transition-opacity"
      >
        View All
      </RouterLink>
    </div>

    <div v-if="loading" class="flex-1 min-h-0" :class="columnsClass">
      <KinSkeleton v-for="n in 4" :key="n" shape="rect" :height="'48px'" />
    </div>

    <KinEmptyState
      v-else-if="tasks.length === 0"
      :icon="UserGroupIcon"
      title="No open family tasks"
      size="sm"
      accent-color="lavender"
      class="flex-1"
    />

    <div v-else class="flex-1 min-h-0 overflow-y-auto" :class="columnsClass">
      <div
        v-for="task in tasks"
        :key="task.id"
        class="flex items-start gap-2 p-2 border-b border-border-subtle last:border-b-0 hover:bg-surface-sunken transition-colors"
        :class="{ 'opacity-40': task._justCompleted }"
      >
        <button
          class="mt-0.5 flex-shrink-0 w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all"
          :class="task._justCompleted
            ? 'border-status-success bg-status-success/10'
            : 'border-border-subtle hover:border-accent-lavender-bold'"
          :aria-label="`Mark ${task.title} as complete`"
          @click.stop="toggleTask(task)"
        >
          <CheckIcon v-if="task._justCompleted" class="w-3 h-3 text-status-success" />
        </button>

        <div class="flex-1 min-w-0">
          <p class="text-sm text-ink-secondary truncate">{{ task.title }}</p>
          <div class="flex items-center gap-2 mt-0.5">
            <p v-if="task.due_date" class="text-xs text-ink-tertiary">
              {{ formatDate(task.due_date) }}
            </p>
            <span
              v-if="task.points"
              class="text-[10px] font-semibold font-mono text-accent-lavender-bold"
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
import { UserGroupIcon } from '@heroicons/vue/24/outline'
import { CheckIcon } from '@heroicons/vue/24/solid'
import api from '@/services/api'
import KinSkeleton from '@/components/design-system/KinSkeleton.vue'
import KinEmptyState from '@/components/design-system/KinEmptyState.vue'

const props = defineProps({
  config: { type: Object, required: true },
})

const { data, loading, refresh } = useWidgetData('/api/v1/tasks', {
  is_family_task: true,
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
  if (size === 'md') return 10
  return 5
})

const columnsClass = computed(() => {
  const size = props.config.size || 'sm'
  if (size === 'md') return 'columns-2 gap-x-4 space-y-1 [&>*]:break-inside-avoid'
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
