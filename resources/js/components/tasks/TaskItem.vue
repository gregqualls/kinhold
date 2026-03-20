<template>
  <div
    class="group flex items-start gap-3 px-4 py-3 rounded-xl hover:bg-lavender-50 dark:hover:bg-prussian-700 transition-colors cursor-pointer"
    :class="{
      'opacity-60': task.completed,
    }"
    @click="$emit('click', task)"
  >
    <!-- Checkbox -->
    <div class="pt-0.5">
      <TaskCheckbox
        :checked="task.completed"
        :priority="task.priority"
        @toggle="$emit('toggle', task.id)"
      />
    </div>

    <!-- Content -->
    <div class="flex-1 min-w-0">
      <!-- Title -->
      <p
        class="text-sm font-medium leading-snug"
        :class="task.completed ? 'line-through text-lavender-400' : 'text-prussian-500 dark:text-lavender-200'"
      >
        {{ task.title }}
      </p>

      <!-- Meta row -->
      <div class="flex items-center gap-3 mt-1 flex-wrap">
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
        <span v-if="task.is_family_task" class="flex items-center gap-1 text-xs text-wisteria-500 dark:text-wisteria-400">
          <UserGroupIcon class="w-3 h-3" />
          Open
        </span>
        <span v-else-if="task.assignee" class="flex items-center gap-1 text-xs text-lavender-500 dark:text-lavender-400">
          <UserAvatar :user="task.assignee" size="xs" />
          {{ task.assignee.name?.split(' ')[0] }}
        </span>

        <!-- Points -->
        <span v-if="task.effective_points" class="flex items-center gap-1 text-xs text-sand-500 dark:text-sand-400 font-medium">
          {{ task.effective_points }} pts
        </span>

        <!-- Recurring indicator -->
        <span v-if="task.recurrence_rule" class="text-xs text-lavender-400">
          <ArrowPathIcon class="w-3 h-3" />
        </span>

        <!-- Description indicator -->
        <span v-if="task.description" class="text-xs text-lavender-400">
          <DocumentTextIcon class="w-3 h-3" />
        </span>
      </div>
    </div>

    <!-- Context menu -->
    <div class="opacity-0 group-hover:opacity-100 transition-opacity" @click.stop>
      <ContextMenu :items="menuItems" />
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { CalendarIcon, DocumentTextIcon, PencilIcon, TrashIcon, UserGroupIcon, ArrowPathIcon } from '@heroicons/vue/24/outline'
import TaskCheckbox from './TaskCheckbox.vue'
import ContextMenu from '@/components/common/ContextMenu.vue'
import UserAvatar from '@/components/common/UserAvatar.vue'

const props = defineProps({
  task: { type: Object, required: true },
})

const emit = defineEmits(['click', 'toggle', 'edit', 'delete'])

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
  if (!props.task.due_date || props.task.completed) return 'text-lavender-500 dark:text-lavender-400'
  const due = new Date(props.task.due_date)
  const today = new Date()
  today.setHours(0, 0, 0, 0)
  const dueDay = new Date(due)
  dueDay.setHours(0, 0, 0, 0)

  if (dueDay < today) return 'text-red-500 font-medium'
  if (dueDay.getTime() === today.getTime()) return 'text-sand-500 dark:text-sand-400 font-medium'
  return 'text-lavender-500 dark:text-lavender-400'
})
</script>
