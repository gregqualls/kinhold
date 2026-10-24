<template>
  <div
    class="kin-pressable group flex items-start gap-3 px-4 py-2.5 border-b border-border-subtle last:border-b-0 hover:bg-surface-sunken transition-[color,background-color,border-color,fill,stroke,transform] duration-150 cursor-pointer"
    :class="{
      'opacity-60': task.completed_at,
    }"
    :data-selected="selected || null"
    @click="handleRowClick"
  >
    <!-- Checkbox: button is its own 40x40 hit target (#293 follow-up).
         `pending` flips the visible check immediately while the API is in flight. -->
    <TaskCheckbox
      :checked="!!task.completed_at"
      :pending="isPendingToggle"
      :priority="task.priority"
      @toggle="$emit('toggle', task.id)"
    />

    <!-- Content -->
    <div class="flex-1 min-w-0">
      <!-- Title (editing happens in TaskDetailPanel; #293 Phase 2 removed
           the dblclick-to-inline-edit path that was dominated by the
           row-click-opens-panel after piece 2 of the polish pass). -->
      <p
        class="text-sm font-medium leading-snug"
        :class="task.completed_at ? 'line-through text-ink-tertiary' : 'text-ink-primary'"
      >
        {{ task.title }}
      </p>

      <!-- Meta row -->
      <div class="flex items-center gap-2 mt-1 flex-wrap">
        <!-- Tags: passive category labels (:interactive="false" renders a span). -->
        <KinChip
          v-for="tag in task.tags"
          :key="tag.id"
          variant="category"
          size="sm"
          :custom-color="getTagHex(tag.color)"
          :interactive="false"
        >
          {{ tag.name }}
        </KinChip>

        <!-- Due date -->
        <span
          v-if="task.due_date"
          class="flex items-center gap-1 text-xs"
          :class="dueDateClass"
        >
          <CalendarIcon class="w-3 h-3" />
          {{ formattedDueDate }}
        </span>

        <!-- Assignee or Family task -->
        <span v-if="task.is_family_task" class="flex items-center gap-1 text-xs text-accent-lavender-bold">
          <UserGroupIcon class="w-3 h-3" />
          Open
        </span>
        <span v-else-if="task.assignee" class="flex items-center gap-1 text-xs text-ink-tertiary">
          <UserAvatar :user="task.assignee" size="xs" />
          {{ task.assignee.name?.split(' ')[0] }}
        </span>

        <!-- Points -->
        <span v-if="task.effective_points" class="text-[10px] font-medium font-mono text-sand-600 dark:text-sand-400">
          {{ task.effective_points }}pts
        </span>

        <!-- Recurring indicator: passive category label. -->
        <KinChip
          v-if="task.recurrence_label || task.recurrence_rule"
          variant="category"
          color="lavender"
          size="sm"
          :interactive="false"
        >
          <template #leading>
            <ArrowPathIcon class="w-3 h-3" />
          </template>
          {{ task.recurrence_label || 'Recurring' }}
        </KinChip>

        <!-- Description indicator -->
        <span v-if="task.description" class="text-xs text-ink-tertiary">
          <DocumentTextIcon class="w-3 h-3" />
        </span>
      </div>
    </div>

    <!-- Priority indicator (click to cycle) -->
    <button
      class="flex-shrink-0 p-1 rounded transition-colors"
      :class="priorityIndicatorClass"
      :title="`Priority: ${task.priority || 'none'} — click to change`"
      :aria-label="`Priority ${task.priority || 'none'}, click to change`"
      @click.stop="cyclePriority"
    >
      <FlagIcon class="w-3.5 h-3.5" />
    </button>

    <!-- Context menu: always visible on touch (no hover), reveal-on-hover on mouse devices (#293). -->
    <div class="opacity-100 pointer-fine:opacity-0 pointer-fine:group-hover:opacity-100 transition-opacity" @click.stop>
      <ContextMenu :items="menuItems" />
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { CalendarIcon, DocumentTextIcon, FlagIcon, PencilIcon, TrashIcon, UserGroupIcon, ArrowPathIcon } from '@heroicons/vue/24/outline'
import { useTasksStore } from '@/stores/tasks'
import TaskCheckbox from './TaskCheckbox.vue'
import ContextMenu from '@/components/common/ContextMenu.vue'
import UserAvatar from '@/components/common/UserAvatar.vue'
import KinChip from '@/components/design-system/KinChip.vue'

const props = defineProps({
  task: { type: Object, required: true },
  selected: { type: Boolean, default: false },
})

const tasksStore = useTasksStore()
const isPendingToggle = computed(() => tasksStore.pendingToggles.has(props.task.id))

const emit = defineEmits(['click', 'toggle', 'edit', 'delete', 'update-inline'])

// Inner interactive children (checkbox, flag, context menu) carry @click.stop
// so a tap on them doesn't also open the panel. Row body opens the detail
// panel on touch and mouse alike. Note: dblclick-to-inline-edit on the title
// is now dominated by row-click-opens-panel; inline editing in that path is
// effectively deprecated and should move into the panel for #293 Phase 2.
const handleRowClick = () => {
  emit('click', props.task)
}

const colorMap = {
  wisteria: '#7d57a8',
  prussian: '#05204A',
  sand: '#a5a84e',
  red: '#dc2626',
  green: '#059669',
  pink: '#db2777',
}

const getTagHex = (colorName) => colorMap[colorName] || colorName || colorMap.wisteria

// Priority cycling (inline edit path only — title edit lives in TaskDetailPanel)
const cyclePriority = () => {
  const cycle = { low: 'medium', medium: 'high', high: 'low' }
  const newPriority = cycle[props.task.priority] || 'medium'
  emit('update-inline', { id: props.task.id, priority: newPriority })
}

const priorityIndicatorClass = computed(() => {
  const classes = {
    high: 'text-status-failed hover:bg-status-failed/10',
    medium: 'text-orange-500 hover:bg-orange-50 dark:hover:bg-orange-900/20',
    low: 'text-ink-tertiary hover:bg-surface-sunken',
  }
  return classes[props.task.priority] || classes.medium
})

const menuItems = computed(() => [
  { label: 'Edit', icon: PencilIcon, action: () => emit('edit', props.task) },
  { divider: true },
  { label: 'Delete', icon: TrashIcon, variant: 'danger', action: () => emit('delete', props.task.id) },
])

const formattedDueDate = computed(() => {
  if (!props.task.due_date) return ''
  const due = new Date(props.task.due_date)
  const today = new Date()
  today.setHours(0, 0, 0, 0)
  const tomorrow = new Date(today)
  tomorrow.setDate(tomorrow.getDate() + 1)
  const nextWeek = new Date(today)
  nextWeek.setDate(nextWeek.getDate() + 7)

  const dueDay = new Date(due)
  dueDay.setHours(0, 0, 0, 0)

  if (dueDay.getTime() === today.getTime()) return 'Today'
  if (dueDay.getTime() === tomorrow.getTime()) return 'Tomorrow'
  if (dueDay < today) {
    const daysAgo = Math.ceil((today.getTime() - dueDay.getTime()) / 86400000)
    return `${daysAgo}d overdue`
  }
  if (dueDay < nextWeek) {
    return dueDay.toLocaleDateString('en-US', { weekday: 'short' })
  }
  return dueDay.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })
})

const dueDateClass = computed(() => {
  if (!props.task.due_date || props.task.completed_at) return 'text-ink-tertiary'
  const due = new Date(props.task.due_date)
  const today = new Date()
  today.setHours(0, 0, 0, 0)
  const dueDay = new Date(due)
  dueDay.setHours(0, 0, 0, 0)

  if (dueDay < today) return 'text-status-failed font-medium'
  if (dueDay.getTime() === today.getTime()) return 'text-sand-500 dark:text-sand-400 font-medium'
  return 'text-ink-tertiary'
})
</script>
