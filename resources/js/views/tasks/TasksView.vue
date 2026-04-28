<template>
  <div class="h-full flex">
    <!-- Main column -->
    <div class="flex-1 min-w-0 flex flex-col">
      <!-- Header -->
      <div class="px-4 pt-3 pb-1 md:px-6 md:pt-6 md:pb-2">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-lg md:text-2xl font-bold font-heading text-ink-primary">Tasks</h1>
            <p class="hidden md:block text-sm text-ink-tertiary mt-0.5">
              {{ filteredIncompleteTasks.length }} remaining
            </p>
          </div>
        </div>
      </div>

      <!-- Tag filter bar (mobile-only — desktop moves to the rail) -->
      <div class="px-4 md:px-6 py-2 lg:hidden">
        <div class="flex items-center gap-2 overflow-x-auto scrollbar-hide pb-1">
          <!-- All chip -->
          <KinChip
            variant="filter"
            size="sm"
            color="neutral"
            :active="selectedTagIds.length === 0"
            class="flex-shrink-0 whitespace-nowrap"
            @click="clearTagFilter"
          >
            All
          </KinChip>

          <!-- Tag chips with dynamic hex colors via customColor escape-hatch -->
          <KinChip
            v-for="tag in tags"
            :key="tag.id"
            variant="filter"
            size="sm"
            :custom-color="getTagHex(tag.color)"
            :active="isTagSelected(tag.id)"
            class="flex-shrink-0 whitespace-nowrap"
            @click="toggleTagFilter(tag.id)"
          >
            {{ tag.name }}
            <span class="opacity-70 ml-1">{{ tag.incomplete_tasks_count || 0 }}</span>
          </KinChip>

          <!-- Manage tags -->
          <button
            type="button"
            class="flex-shrink-0 p-1.5 text-ink-tertiary hover:text-ink-primary hover:bg-surface-sunken rounded-full transition-colors"
            title="Manage tags"
            aria-label="Manage tags"
            @click="showTagManager = true"
          >
            <CogIcon class="w-4 h-4" />
          </button>
        </div>
      </div>

      <!-- Divider -->
      <div class="px-4 md:px-6">
        <div class="border-t border-border-subtle"></div>
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
        <KinFlatCard
          v-if="filteredIncompleteTasks.length > 0"
          padding="none"
          class="mx-4 md:mx-6 mt-3 overflow-hidden"
        >
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
        </KinFlatCard>

        <!-- Completed section -->
        <div v-if="filteredCompletedTasks.length > 0" class="mt-5 px-4 md:px-6">
          <button
            type="button"
            class="flex items-center gap-2 text-xs font-medium text-ink-tertiary hover:text-ink-primary transition-colors mb-2"
            @click="showCompleted = !showCompleted"
          >
            <ChevronRightIcon
              class="w-3 h-3 transition-transform"
              :class="showCompleted && 'rotate-90'"
            />
            Completed ({{ filteredCompletedTasks.length }})
          </button>

          <KinFlatCard v-if="showCompleted" padding="none" class="overflow-hidden">
            <TransitionGroup name="task-list" tag="div">
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
          </KinFlatCard>
        </div>

        <!-- Empty state -->
        <KinEmptyState
          v-if="tasks.length === 0 && !isLoading"
          :icon="CheckCircleIcon"
          title="No tasks yet"
          description="Add your first task to get started."
          accent-color="mint"
          size="md"
        />

        <!-- No matches for filter -->
        <div
          v-if="tasks.length > 0 && filteredIncompleteTasks.length === 0 && filteredCompletedTasks.length === 0 && selectedTagIds.length > 0"
          class="text-center py-12 text-ink-tertiary text-sm"
        >
          No tasks match the selected tags.
        </div>
      </div>
    </div><!-- /main column -->

    <!-- Right utility rail (desktop only) -->
    <KinUtilityRail class="hidden lg:flex" width="280px">
      <template #filters>
        <div class="flex flex-col gap-1.5 items-start">
          <KinChip
            variant="filter"
            size="sm"
            color="neutral"
            :active="selectedTagIds.length === 0"
            @click="clearTagFilter"
          >
            All
          </KinChip>
          <KinChip
            v-for="tag in tags"
            :key="tag.id"
            variant="filter"
            size="sm"
            :custom-color="getTagHex(tag.color)"
            :active="isTagSelected(tag.id)"
            @click="toggleTagFilter(tag.id)"
          >
            {{ tag.name }}
            <span class="opacity-70 ml-1">{{ tag.incomplete_tasks_count || 0 }}</span>
          </KinChip>
        </div>
      </template>

      <template #actions>
        <div class="flex items-center gap-2">
          <KinButton variant="primary" size="sm" @click="focusQuickAdd">
            <template #leading>
              <PlusIcon class="w-4 h-4" />
            </template>
            Add Task
          </KinButton>
          <KinButton
            variant="ghost"
            size="sm"
            icon-only
            aria-label="Manage tags"
            title="Manage tags"
            @click="showTagManager = true"
          >
            <CogIcon class="w-4 h-4" />
          </KinButton>
        </div>
      </template>
    </KinUtilityRail>

    <!-- Mobile FAB -->
    <FloatingActionButton class="lg:hidden" @click="focusQuickAdd" />

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
      confirm-text="Delete"
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
    <KinModalSheet
      :model-value="showTagManager"
      title="Manage Tags"
      size="sm"
      @update:model-value="(v) => !v && (showTagManager = false)"
    >
      <div class="space-y-4">
        <!-- Existing tags -->
        <div v-if="tags.length > 0" class="space-y-2">
          <div
            v-for="tag in tags"
            :key="tag.id"
            class="flex items-center gap-3 p-2 rounded-lg hover:bg-surface-sunken group"
          >
            <span
              class="w-3 h-3 rounded-full flex-shrink-0"
              :style="{ backgroundColor: getTagHex(tag.color) }"
            ></span>
            <span v-if="editingTag?.id !== tag.id" class="flex-1 text-sm text-ink-primary">
              {{ tag.name }}
            </span>
            <input
              v-else
              v-model="editingTag.name"
              class="flex-1 text-sm text-ink-primary bg-transparent border-b border-accent-lavender-bold outline-none"
              @keydown.enter="saveEditTag"
              @keydown.escape="editingTag = null"
            />
            <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
              <button
                v-if="editingTag?.id !== tag.id"
                type="button"
                class="p-1 text-ink-tertiary hover:text-ink-primary rounded"
                aria-label="Edit tag"
                @click="editingTag = { id: tag.id, name: tag.name, color: tag.color }"
              >
                <PencilIcon class="w-3.5 h-3.5" />
              </button>
              <button
                v-else
                type="button"
                class="p-1 text-status-success hover:opacity-80 rounded"
                aria-label="Save tag"
                @click="saveEditTag"
              >
                <CheckIcon class="w-3.5 h-3.5" />
              </button>
              <button
                type="button"
                class="p-1 text-ink-tertiary hover:text-status-failed rounded"
                aria-label="Delete tag"
                @click="handleDeleteTag(tag)"
              >
                <TrashIcon class="w-3.5 h-3.5" />
              </button>
            </div>
          </div>
        </div>

        <p v-else class="text-sm text-ink-tertiary text-center py-4">
          No tags yet
        </p>

        <!-- Add new tag -->
        <div class="border-t border-border-subtle pt-4">
          <form class="flex gap-2" @submit.prevent="handleCreateTag">
            <div class="flex items-center gap-2 flex-1">
              <button
                type="button"
                class="w-6 h-6 rounded-full flex-shrink-0 border-2 border-surface-raised shadow-resting"
                :style="{ backgroundColor: getTagHex(newTagColor) }"
                aria-label="Cycle tag color"
                @click="cycleNewTagColor"
              ></button>
              <input
                v-model="newTagName"
                placeholder="New tag name..."
                class="flex-1 text-sm text-ink-primary placeholder:text-ink-tertiary outline-none bg-transparent border-b border-border-subtle focus:border-accent-lavender-bold py-1"
              />
            </div>
            <KinButton
              type="submit"
              variant="primary"
              size="sm"
              :disabled="!newTagName.trim()"
            >
              Add
            </KinButton>
          </form>
        </div>
      </div>
    </KinModalSheet>
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
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import KinModalSheet from '@/components/design-system/KinModalSheet.vue'
import KinButton from '@/components/design-system/KinButton.vue'
import KinEmptyState from '@/components/design-system/KinEmptyState.vue'
import KinChip from '@/components/design-system/KinChip.vue'
import KinUtilityRail from '@/components/design-system/KinUtilityRail.vue'
import KinFlatCard from '@/components/design-system/KinFlatCard.vue'
import { PlusIcon } from '@heroicons/vue/24/outline'
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
