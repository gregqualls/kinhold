<template>
  <div class="flex flex-col h-full">
    <!-- Header -->
    <div class="flex items-center justify-between mb-3 flex-shrink-0">
      <h3 class="text-sm font-semibold text-prussian-500 dark:text-lavender-200 flex items-center gap-2">
        <FunnelIcon class="w-4 h-4 text-wisteria-500" />
        {{ config.title || 'Tasks' }}
      </h3>
      <RouterLink
        to="/tasks"
        class="text-wisteria-600 dark:text-wisteria-400 text-xs font-medium hover:text-wisteria-500"
      >
        View All
      </RouterLink>
    </div>

    <!-- Tag pills (show which tags are filtering) -->
    <div v-if="filterTags.length > 0" class="flex flex-wrap gap-1 mb-2 flex-shrink-0">
      <span
        v-for="tag in filterTags"
        :key="tag.id"
        class="inline-flex items-center px-2 py-0.5 text-[10px] font-medium rounded-full"
        :style="{ backgroundColor: tag.color + '20', color: tag.color }"
      >
        {{ tag.name }}
      </span>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex-1 min-h-0 space-y-2">
      <div v-for="n in 4" :key="n" class="h-12 bg-lavender-100 dark:bg-prussian-700 rounded-lg animate-pulse"></div>
    </div>

    <!-- Empty -->
    <div v-else-if="tasks.length === 0" class="flex-1 flex items-center justify-center">
      <div class="text-center">
        <FunnelIcon class="w-8 h-8 text-lavender-400 dark:text-lavender-500 mx-auto mb-1" />
        <p class="text-sm text-lavender-500 dark:text-lavender-400">No matching tasks</p>
      </div>
    </div>

    <!-- Task list -->
    <div v-else class="flex-1 min-h-0 overflow-y-auto" :class="columnsClass">
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
            <span v-if="task.points" class="text-[10px] font-semibold font-mono text-wisteria-600 dark:text-wisteria-400">
              +{{ task.points }}
            </span>
            <span
              v-if="task.assignee"
              class="text-[10px] text-lavender-400 dark:text-lavender-500"
            >
              {{ task.assignee.name?.split(' ')[0] }}
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, onMounted } from 'vue'
import { DateTime } from 'luxon'
import { useWidgetData } from '@/composables/useWidgetData'
import { FunnelIcon } from '@heroicons/vue/24/outline'
import { CheckIcon } from '@heroicons/vue/24/solid'
import api from '@/services/api'

const props = defineProps({
  config: { type: Object, required: true },
})

const filters = computed(() => props.config.filters || {})

// Build query params from filters
const queryParams = computed(() => {
  const params = { status: 'pending' }

  if (filters.value.tags?.length) {
    params.tags = filters.value.tags.join(',')
  }
  if (filters.value.due_within) {
    const now = DateTime.now()
    params.due_after = now.toFormat('yyyy-MM-dd')
    if (filters.value.due_within === 'today') {
      params.due_before = now.toFormat('yyyy-MM-dd')
    } else if (filters.value.due_within === 'week') {
      params.due_before = now.plus({ days: 7 }).toFormat('yyyy-MM-dd')
    } else if (filters.value.due_within === 'month') {
      params.due_before = now.plus({ months: 1 }).toFormat('yyyy-MM-dd')
    }
  }
  if (filters.value.assigned_to) {
    params.assigned_to = filters.value.assigned_to
  }

  return params
})

const { data, loading, refresh } = useWidgetData('/api/v1/tasks', queryParams.value)

// Fetch tags to show filter pills
const allTags = ref([])
onMounted(async () => {
  try {
    const res = await api.get('/tags', { params: { scope: 'task' } })
    allTags.value = res.data.tags || res.data.data || res.data || []
  } catch (e) {
    // eslint-disable-next-line no-console
    console.warn('Failed to fetch tags for filter pills:', e.message)
  }
})

const filterTags = computed(() => {
  if (!filters.value.tags?.length) return []
  return allTags.value.filter((t) => filters.value.tags.includes(t.id))
})

const completedIds = ref(new Set())

const tasks = computed(() => {
  if (!data.value) return []
  const list = data.value.tasks || data.value.data || data.value
  if (!Array.isArray(list)) return []
  const limit = props.config.size === 'lg' ? 15 : props.config.size === 'md' ? 10 : 5
  return list.slice(0, limit).map((t) => ({
    ...t,
    _justCompleted: completedIds.value.has(t.id),
  }))
})

const columnsClass = computed(() => {
  const size = props.config.size || 'sm'
  if (size === 'lg') return 'columns-3 gap-x-4 space-y-1 [&>*]:break-inside-avoid'
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
