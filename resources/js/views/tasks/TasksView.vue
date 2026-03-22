<template>
  <div class="h-full flex flex-col">
    <!-- Header -->
    <div class="px-4 pt-4 pb-2 md:px-6 md:pt-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-prussian-500 dark:text-lavender-200">Tasks</h1>
          <p class="text-sm text-lavender-500 dark:text-lavender-400 mt-0.5">
            {{ filteredIncompleteTasks.length }} remaining
          </p>
        </div>
      </div>
    </div>

    <!-- Tag filter bar -->
    <div class="px-4 md:px-6 py-2">
      <div class="flex items-center gap-2 overflow-x-auto scrollbar-hide pb-1">
        <!-- All chip -->
        <button
          @click="clearTagFilter"
          class="flex-shrink-0 px-3 py-1.5 text-xs font-medium rounded-full transition-colors whitespace-nowrap"
          :class="selectedTagIds.length === 0
            ? 'bg-prussian-500 dark:bg-wisteria-600 text-white'
            : 'bg-lavender-100 dark:bg-prussian-700 text-lavender-600 dark:text-lavender-400 hover:bg-lavender-200 dark:hover:bg-prussian-600'"
        >
          All
        </button>

        <!-- Tag chips -->
        <button
          v-for="tag in tags"
          :key="tag.id"
          @click="toggleTagFilter(tag.id)"
          class="flex-shrink-0 flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-full transition-colors whitespace-nowrap"
          :class="isTagSelected(tag.id)
            ? 'text-white'
            : 'bg-lavender-100 dark:bg-prussian-700 text-lavender-600 dark:text-lavender-400 hover:bg-lavender-200 dark:hover:bg-prussian-600'"
          :style="isTagSelected(tag.id) ? { backgroundColor: getTagHex(tag.color) } : {}"
        >
          <span
            v-if="!isTagSelected(tag.id)"
            class="w-2 h-2 rounded-full flex-shrink-0"
            :style="{ backgroundColor: getTagHex(tag.color) }"
          />
          {{ tag.name }}
          <span class="opacity-70">{{ tag.incomplete_tasks_count || 0 }}</span>
        </button>

        <!-- Manage tags -->
        <button
          @click="showTagManager = true"
          class="flex-shrink-0 p-1.5 text-lavender-400 hover:text-prussian-500 dark:hover:text-lavender-200 hover:bg-lavender-100 dark:hover:bg-prussian-700 rounded-full transition-colors"
          title="Manage tags"
        >
          <CogIcon class="w-4 h-4" />
        </button>
      </div>
    </div>

    <!-- Divider -->
    <div class="px-4 md:px-6">
      <div class="border-t border-lavender-200 dark:border-prussian-700" />
    </div>

    <!-- Loading -->
    <div v-if="isLoading" class="flex items-center justify-center py-16">
      <LoadingSpinner size="lg" />
    </div>

    <!-- Tasks -->
    <div v-else ref="scrollContainerRef" class="flex-1 overflow-y-auto pb-32 md:pb-6">
      <!-- Quick add -->
      <TaskQuickAdd ref="quickAddRef" :tags="tags" @add="handleQuickAdd" />

      <!-- Incomplete tasks -->
      <div v-if="filteredIncompleteTasks.length > 0" class="mt-2">
        <TransitionGroup name="task-list" tag="div">
          <TaskItem
            v-for="task in filteredIncompleteTasks"
            :key="task.id"
            :task="task"
            @click="openDetail(task)"
            @toggle="handleToggle(task)"
            @edit="openDetail(task)"
            @delete="confirmDelete(task)"
            @update-inline="handleInlineUpdate"
          />
        </TransitionGroup>
      </div>

      <!-- Completed section -->
      <div v-if="filteredCompletedTasks.length > 0" class="mt-4 px-4">
        <button
          @click="showCompleted = !showCompleted"
          class="flex items-center gap-2 text-xs font-medium text-lavender-500 dark:text-lavender-400 hover:text-prussian-500 dark:hover:text-lavender-200 transition-colors"
        >
          <ChevronRightIcon
            class="w-3 h-3 transition-transform"
            :class="showCompleted && 'rotate-90'"
          />
          Completed ({{ filteredCompletedTasks.length }})
        </button>

        <TransitionGroup v-if="showCompleted" name="task-list" tag="div" class="mt-1">
          <TaskItem
            v-for="task in filteredCompletedTasks"
            :key="task.id"
            :task="task"
            @click="openDetail(task)"
            @toggle="handleToggle(task)"
            @edit="openDetail(task)"
            @delete="confirmDelete(task)"
            @update-inline="handleInlineUpdate"
          />
        </TransitionGroup>
      </div>

      <!-- Empty state -->
      <EmptyState
        v-if="tasks.length === 0 && !isLoading"
        :icon="CheckCircleIcon"
        title="No tasks yet"
        description="Add your first task to get started."
      />

      <!-- No matches for filter -->
      <div
        v-if="tasks.length > 0 && filteredIncompleteTasks.length === 0 && filteredCompletedTasks.length === 0 && selectedTagIds.length > 0"
        class="text-center py-12 text-lavender-500 dark:text-lavender-400 text-sm"
      >
        No tasks match the selected tags.
      </div>
    </div>

    <!-- Mobile FAB -->
    <FloatingActionButton @click="focusQuickAdd" />

    <!-- Task Detail Panel -->
    <TaskDetailPanel
      :task="selectedTask"
      :saving="savingTask"
      :tags="tags"
      @save="handleSaveTask"
      @close="selectedTask = null"
      @delete="confirmDelete"
    />

    <!-- Delete Confirmation -->
    <ConfirmDialog
      :show="!!taskToDelete"
      title="Delete Task?"
      :message="`&quot;${taskToDelete?.title}&quot; will be permanently deleted.`"
      confirmText="Delete"
      @confirm="handleDeleteTask"
      @cancel="taskToDelete = null"
    />

    <!-- Undo Toast -->
    <UndoToast
      :show="!!undoAction"
      :message="undoAction?.message || ''"
      :undoable="true"
      @undo="handleUndo"
      @dismiss="undoAction = null"
    />

    <!-- Tag Manager Modal -->
    <BaseModal
      :show="showTagManager"
      title="Manage Tags"
      size="sm"
      @close="showTagManager = false"
    >
      <div class="space-y-4">
        <!-- Existing tags -->
        <div v-if="tags.length > 0" class="space-y-2">
          <div
            v-for="tag in tags"
            :key="tag.id"
            class="flex items-center gap-3 p-2 rounded-lg hover:bg-lavender-50 dark:hover:bg-prussian-700 group"
          >
            <span
              class="w-3 h-3 rounded-full flex-shrink-0"
              :style="{ backgroundColor: getTagHex(tag.color) }"
            />
            <span v-if="editingTag?.id !== tag.id" class="flex-1 text-sm text-prussian-500 dark:text-lavender-200">
              {{ tag.name }}
            </span>
            <input
              v-else
              v-model="editingTag.name"
              @keydown.enter="saveEditTag"
              @keydown.escape="editingTag = null"
              class="flex-1 text-sm text-prussian-500 dark:text-lavender-200 bg-transparent border-b border-wisteria-400 outline-none"
            />
            <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
              <button
                v-if="editingTag?.id !== tag.id"
                @click="editingTag = { id: tag.id, name: tag.name, color: tag.color }"
                class="p-1 text-lavender-400 hover:text-prussian-500 dark:hover:text-lavender-200 rounded"
              >
                <PencilIcon class="w-3.5 h-3.5" />
              </button>
              <button
                v-else
                @click="saveEditTag"
                class="p-1 text-emerald-500 hover:text-emerald-600 rounded"
              >
                <CheckIcon class="w-3.5 h-3.5" />
              </button>
              <button
                @click="handleDeleteTag(tag)"
                class="p-1 text-lavender-400 hover:text-red-500 rounded"
              >
                <TrashIcon class="w-3.5 h-3.5" />
              </button>
            </div>
          </div>
        </div>

        <p v-else class="text-sm text-lavender-500 dark:text-lavender-400 text-center py-4">
          No tags yet
        </p>

        <!-- Add new tag -->
        <div class="border-t border-lavender-200 dark:border-prussian-700 pt-4">
          <form @submit.prevent="handleCreateTag" class="flex gap-2">
            <div class="flex items-center gap-2 flex-1">
              <button
                type="button"
                @click="cycleNewTagColor"
                class="w-6 h-6 rounded-full flex-shrink-0 border-2 border-white dark:border-prussian-700 shadow-sm"
                :style="{ backgroundColor: getTagHex(newTagColor) }"
              />
              <input
                v-model="newTagName"
                placeholder="New tag name..."
                class="flex-1 text-sm text-prussian-500 dark:text-lavender-200 placeholder-lavender-400 outline-none bg-transparent border-b border-lavender-200 dark:border-prussian-600 focus:border-wisteria-400 py-1"
              />
            </div>
            <button
              type="submit"
              :disabled="!newTagName.trim()"
              class="px-3 py-1.5 text-xs font-medium text-white bg-wisteria-600 hover:bg-wisteria-500 rounded-lg disabled:opacity-40 transition-colors"
            >
              Add
            </button>
          </form>
        </div>
      </div>
    </BaseModal>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { storeToRefs } from 'pinia'
import { useTasksStore } from '@/stores/tasks'
import { useNotification } from '@/composables/useNotification'
import TaskItem from '@/components/tasks/TaskItem.vue'
import TaskQuickAdd from '@/components/tasks/TaskQuickAdd.vue'
import TaskDetailPanel from '@/components/tasks/TaskDetailPanel.vue'
import ConfirmDialog from '@/components/common/ConfirmDialog.vue'
import FloatingActionButton from '@/components/common/FloatingActionButton.vue'
import UndoToast from '@/components/common/UndoToast.vue'
import EmptyState from '@/components/common/EmptyState.vue'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import BaseModal from '@/components/common/BaseModal.vue'
import {
  ChevronRightIcon,
  CheckCircleIcon,
  CheckIcon,
  CogIcon,
  PencilIcon,
  TrashIcon,
} from '@heroicons/vue/24/outline'

const tasksStore = useTasksStore()
const { success, error: notifyError } = useNotification()

const { tags, tasks, selectedTagIds, isLoading, filteredIncompleteTasks, filteredCompletedTasks } = storeToRefs(tasksStore)

const showCompleted = ref(false)
const selectedTask = ref(null)
const savingTask = ref(false)
const taskToDelete = ref(null)
const undoAction = ref(null)
const showTagManager = ref(false)
const editingTag = ref(null)
const newTagName = ref('')
const newTagColor = ref('wisteria')
const scrollContainerRef = ref(null)
const quickAddRef = ref(null)

const tagColorOptions = ['wisteria', 'prussian', 'sand', 'red', 'green', 'pink']
const colorMap = {
  wisteria: '#7d57a8',
  prussian: '#05204A',
  sand: '#a5a84e',
  red: '#dc2626',
  green: '#059669',
  pink: '#db2777',
}

const getTagHex = (colorName) => colorMap[colorName] || colorName || colorMap.wisteria
const isTagSelected = (tagId) => selectedTagIds.value.includes(tagId)
const { toggleTagFilter, clearTagFilter } = tasksStore

const cycleNewTagColor = () => {
  const idx = tagColorOptions.indexOf(newTagColor.value)
  newTagColor.value = tagColorOptions[(idx + 1) % tagColorOptions.length]
}

// Task actions
const handleQuickAdd = async (data) => {
  const result = await tasksStore.createTask(data)
  if (result.success) {
    success('Task added!')
  } else {
    notifyError(result.error || 'Failed to add task')
  }
}

const openDetail = (task) => {
  selectedTask.value = { ...task }
}

const handleSaveTask = async (formData) => {
  if (!selectedTask.value) return
  savingTask.value = true

  const result = await tasksStore.updateTask(selectedTask.value.id, formData)
  if (result.success) {
    success('Task updated!')
    selectedTask.value = null
  } else {
    notifyError(result.error || 'Failed to update task')
  }
  savingTask.value = false
}

const handleInlineUpdate = async ({ id, ...data }) => {
  const result = await tasksStore.updateTask(id, data)
  if (!result.success) {
    notifyError(result.error || 'Failed to update task')
  }
}

const handleToggle = async (task) => {
  const wasCompleted = !!task.completed_at
  const result = await tasksStore.toggleComplete(task.id)

  if (result.success && !wasCompleted) {
    undoAction.value = {
      message: `"${task.title}" completed`,
      taskId: task.id,
      type: 'complete',
    }
  }
}

const confirmDelete = (taskOrId) => {
  const task = typeof taskOrId === 'object' ? taskOrId : tasks.value.find((t) => t.id === taskOrId)
  if (task) {
    taskToDelete.value = task
    selectedTask.value = null
  }
}

const handleDeleteTask = async () => {
  if (!taskToDelete.value) return
  const result = await tasksStore.deleteTask(taskToDelete.value.id)
  if (result.success) {
    undoAction.value = { message: `"${taskToDelete.value.title}" deleted` }
  } else {
    notifyError(result.error || 'Failed to delete task')
  }
  taskToDelete.value = null
}

const handleUndo = async () => {
  if (undoAction.value?.type === 'complete' && undoAction.value?.taskId) {
    await tasksStore.toggleComplete(undoAction.value.taskId)
  }
  undoAction.value = null
}

// Tag management
const handleCreateTag = async () => {
  if (!newTagName.value.trim()) return
  const result = await tasksStore.createTag({
    name: newTagName.value.trim(),
    color: newTagColor.value,
  })
  if (result.success) {
    newTagName.value = ''
    success('Tag created!')
  } else {
    notifyError(result.error || 'Failed to create tag')
  }
}

const saveEditTag = async () => {
  if (!editingTag.value || !editingTag.value.name.trim()) return
  await tasksStore.updateTag(editingTag.value.id, {
    name: editingTag.value.name.trim(),
    color: editingTag.value.color,
  })
  editingTag.value = null
}

const handleDeleteTag = async (tag) => {
  const result = await tasksStore.deleteTag(tag.id)
  if (result.success) {
    success(`"${tag.name}" tag deleted`)
  }
}

const focusQuickAdd = () => {
  scrollContainerRef.value?.scrollTo({ top: 0, behavior: 'smooth' })
  quickAddRef.value?.startEditing()
}

onMounted(async () => {
  await Promise.all([tasksStore.fetchTags(), tasksStore.fetchTasks()])
})
</script>

<style scoped>
.task-list-enter-active {
  transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
.task-list-leave-active {
  transition: all 0.2s ease-in;
}
.task-list-enter-from {
  opacity: 0;
  transform: translateY(-8px);
}
.task-list-leave-to {
  opacity: 0;
  transform: translateX(20px);
}
.task-list-move {
  transition: transform 0.3s ease;
}
.scrollbar-hide {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
.scrollbar-hide::-webkit-scrollbar {
  display: none;
}
</style>
