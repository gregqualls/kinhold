<template>
  <div>
    <!-- Header with View All link -->
    <div v-if="viewAllPath" class="flex items-center justify-between mb-3 flex-shrink-0">
      <h3 class="text-sm font-semibold text-prussian-500 dark:text-lavender-200">
        {{ config.title }}
      </h3>
      <RouterLink
        :to="viewAllPath"
        class="text-wisteria-600 dark:text-wisteria-400 text-xs font-medium hover:text-wisteria-500"
      >
        View All
      </RouterLink>
    </div>

    <!-- Loading skeleton -->
    <div v-if="loading" class="space-y-3">
      <div v-for="n in 3" :key="n" class="h-10 bg-lavender-100 dark:bg-prussian-700 rounded-lg animate-pulse"></div>
    </div>

    <!-- Empty state -->
    <div v-else-if="displayItems.length === 0" class="text-center py-4">
      <p class="text-sm text-lavender-500 dark:text-lavender-400">
        {{ settings.emptyMessage || 'Nothing to show.' }}
      </p>
    </div>

    <!-- Items — multi-column for tasks at md/lg sizes -->
    <div v-else :class="columnsClass">
      <div
        v-for="item in displayItems"
        :key="item.id"
        class="flex items-start gap-2 p-2 rounded-lg hover:bg-lavender-50 dark:hover:bg-prussian-700/50 transition-colors"
        :class="{ 'opacity-50 line-through': item.completed_at && isCompletable }"
      >
        <!-- Completable checkbox for tasks -->
        <button
          v-if="isCompletable"
          class="mt-0.5 flex-shrink-0 w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all"
          :class="item.completed_at
            ? 'border-green-400 bg-green-100 dark:bg-green-900/30 dark:border-green-600'
            : 'border-lavender-300 dark:border-prussian-500 hover:border-wisteria-400 dark:hover:border-wisteria-400'"
          @click.stop="toggleTask(item)"
        >
          <CheckIcon v-if="item.completed_at" class="w-3 h-3 text-green-600 dark:text-green-400" />
        </button>

        <!-- Color dot for non-task items -->
        <div v-else-if="item.color" class="w-2 h-2 rounded-full mt-1.5 flex-shrink-0" :style="{ backgroundColor: item.color }"></div>
        <div v-else class="w-2 h-2 rounded-full bg-wisteria-400 mt-1.5 flex-shrink-0"></div>

        <!-- Content -->
        <div class="flex-1 min-w-0">
          <p class="text-sm text-prussian-600 dark:text-lavender-200 truncate">
            {{ item.title || item.name || item.summary || item.description }}
          </p>
          <div class="flex items-center gap-2 mt-0.5">
            <p v-if="item.due_date && settings.showDueDate" class="text-xs text-lavender-500 dark:text-lavender-400">
              {{ formatDate(item.due_date) }}
            </p>
            <span
              v-if="item.points && settings.showPoints"
              class="text-[10px] font-semibold font-mono text-wisteria-600 dark:text-wisteria-400"
            >
              +{{ item.points }}
            </span>
            <p v-if="item.point_cost" class="text-xs text-wisteria-500 dark:text-wisteria-400">
              {{ item.point_cost }} pts
            </p>
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
import { CheckIcon } from '@heroicons/vue/24/solid'
import api from '@/services/api'

const props = defineProps({
  config: { type: Object, required: true },
})

const settings = computed(() => props.config.settings || {})
const limit = computed(() => settings.value.limit || 10)
const viewAllPath = computed(() => settings.value.viewAllPath || null)
const isCompletable = computed(() => settings.value.completable === true)

const { data, loading, refresh } = useWidgetData(props.config.endpoint, props.config.params)

// Local override for optimistic toggle
const localOverrides = ref({})

const items = computed(() => {
  if (!data.value) return []
  if (Array.isArray(data.value)) return data.value
  if (data.value.data && Array.isArray(data.value.data)) return data.value.data
  if (data.value.tasks && Array.isArray(data.value.tasks)) return data.value.tasks
  if (data.value.rewards && Array.isArray(data.value.rewards)) return data.value.rewards
  if (data.value.entries && Array.isArray(data.value.entries)) return data.value.entries
  return []
})

const displayItems = computed(() => {
  return items.value.slice(0, limit.value).map((item) => {
    if (localOverrides.value[item.id] !== undefined) {
      return { ...item, completed_at: localOverrides.value[item.id] }
    }
    return item
  })
})

async function toggleTask(item) {
  if (!item.id) return
  const wasCompleted = !!item.completed_at
  // Optimistic update
  localOverrides.value[item.id] = wasCompleted ? null : new Date().toISOString()

  try {
    const endpoint = wasCompleted ? `/tasks/${item.id}/uncomplete` : `/tasks/${item.id}/complete`
    await api.patch(endpoint)
    // Refresh data after a short delay to let the server settle
    setTimeout(() => {
      delete localOverrides.value[item.id]
      refresh()
    }, 500)
  } catch {
    // Revert on error
    delete localOverrides.value[item.id]
  }
}

// Multi-column layout based on widget size
const columnsClass = computed(() => {
  if (!isCompletable.value) return 'space-y-1'
  const size = props.config.size || 'sm'
  if (size === 'lg') return 'grid grid-cols-3 gap-x-4 gap-y-1'
  if (size === 'md') return 'grid grid-cols-2 gap-x-4 gap-y-1'
  return 'space-y-1'
})

function formatDate(dateStr) {
  if (!dateStr) return ''
  const dt = DateTime.fromISO(dateStr)
  const today = DateTime.now().startOf('day')
  const tomorrow = today.plus({ days: 1 })
  if (dt.startOf('day').equals(today)) return 'Today'
  if (dt.startOf('day').equals(tomorrow)) return 'Tomorrow'
  return dt.toLocaleString({ month: 'short', day: 'numeric' })
}
</script>
