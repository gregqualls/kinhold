<template>
  <div
    class="group flex items-start gap-3 px-4 py-3 rounded-xl hover:bg-lavender-50 dark:hover:bg-prussian-700 transition-colors cursor-pointer"
    :class="{
      'opacity-60': task.completed_at,
    }"
    @click="$emit('click', task)"
  >
    <!-- Checkbox -->
    <div class="pt-0.5" @click.stop>
      <TaskCheckbox
        :checked="!!task.completed_at"
        :priority="task.priority"
        @toggle="$emit('toggle', task.id)"
      />
    </div>

    <!-- Content -->
    <div class="flex-1 min-w-0">
      <!-- Title (inline editable on double-click) -->
      <div v-if="!isEditingTitle" @dblclick.stop="startEditTitle">
        <p
          class="text-sm font-medium leading-snug"
          :class="task.completed_at ? 'line-through text-lavender-400' : 'text-prussian-500 dark:text-lavender-200'"
        >
          {{ task.title }}
        </p>
      </div>
      <input
        v-else
        ref="titleInput"
        v-model="editTitle"
        @keydown.enter="saveTitle"
        @keydown.escape="cancelEditTitle"
        @blur="saveTitle"
        @click.stop
        class="w-full text-sm font-medium text-prussian-500 dark:text-lavender-200 bg-transparent border-b-2 border-wisteria-400 outline-none py-0"
      />

      <!-- Meta row -->
      <div class="flex items-center gap-2 mt-1 flex-wrap">
        <!-- Tags -->
        <span
          v-for="tag in task.tags"
          :key="tag.id"
          class="inline-flex items-center gap-1 px-1.5 py-0.5 text-[10px] font-medium rounded-full"
          :style="{ backgroundColor: getTagHex(tag.color) + '20', color: getTagHex(tag.color) }"
        >
          <span class="w-1.5 h-1.5 rounded-full" :style="{ backgroundColor: getTagHex(tag.color) }" />
          {{ tag.name }}
        </span>

        <!-- Due date -->
        <span
          v-if="task.due_date"
          class="flex items-center gap-1 text-xs"
          :class="dueDateClass"
        >
          <CalendarIcon class="w-3 h-3" />
          {{ formattedDueDate }}
        </span>

        <!-- Assignee -->
        <span v-if="task.assignee" class="flex items-center gap-1 text-xs text-lavender-500 dark:text-lavender-400">
          <UserAvatar :user="task.assignee" size="xs" />
          {{ task.assignee.name?.split(' ')[0] }}
        </span>

        <!-- Description indicator -->
        <span v-if="task.description" class="text-xs text-lavender-400">
          <DocumentTextIcon class="w-3 h-3" />
        </span>

        <!-- Points -->
        <span v-if="task.effective_points" class="text-[10px] font-medium text-sand-600 dark:text-sand-400">
          {{ task.effective_points }}pts
        </span>
      </div>
    </div>

    <!-- Priority indicator (click to cycle) -->
    <button
      @click.stop="cyclePriority"
      class="flex-shrink-0 p-1 rounded transition-colors"
      :class="priorityIndicatorClass"
      title="Click to change priority"
    >
      <FlagIcon class="w-3.5 h-3.5" />
    </button>

    <!-- Context menu -->
    <div class="opacity-0 group-hover:opacity-100 transition-opacity" @click.stop>
      <ContextMenu :items="menuItems" />
    </div>
  </div>
</template>

<script setup>
import { computed, ref, nextTick } from 'vue'
import { CalendarIcon, DocumentTextIcon, FlagIcon, PencilIcon, TrashIcon } from '@heroicons/vue/24/outline'
import TaskCheckbox from './TaskCheckbox.vue'
import ContextMenu from '@/components/common/ContextMenu.vue'
import UserAvatar from '@/components/common/UserAvatar.vue'

const props = defineProps({
  task: { type: Object, required: true },
})

const emit = defineEmits(['click', 'toggle', 'edit', 'delete', 'update-inline'])

const colorMap = {
  wisteria: '#7d57a8',
  prussian: '#05204A',
  sand: '#a5a84e',
  red: '#dc2626',
  green: '#059669',
  pink: '#db2777',
}

const getTagHex = (colorName) => colorMap[colorName] || colorName || colorMap.wisteria

// Inline title editing
const isEditingTitle = ref(false)
const editTitle = ref('')
const titleInput = ref(null)

const startEditTitle = async () => {
  editTitle.value = props.task.title
  isEditingTitle.value = true
  await nextTick()
  titleInput.value?.focus()
}

const saveTitle = () => {
  if (!isEditingTitle.value) return
  const trimmed = editTitle.value.trim()
  if (trimmed && trimmed !== props.task.title) {
    emit('update-inline', { id: props.task.id, title: trimmed })
  }
  isEditingTitle.value = false
}

const cancelEditTitle = () => {
  isEditingTitle.value = false
}

// Priority cycling
const cyclePriority = () => {
  const cycle = { low: 'medium', medium: 'high', high: 'low' }
  const newPriority = cycle[props.task.priority] || 'medium'
  emit('update-inline', { id: props.task.id, priority: newPriority })
}

const priorityIndicatorClass = computed(() => {
  const classes = {
    high: 'text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20',
    medium: 'text-orange-500 hover:bg-orange-50 dark:hover:bg-orange-900/20',
    low: 'text-lavender-400 hover:bg-lavender-100 dark:hover:bg-prussian-600',
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
  if (!props.task.due_date || props.task.completed_at) return 'text-lavender-500 dark:text-lavender-400'
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
