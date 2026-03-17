<template>
  <div class="h-full flex flex-col">
    <!-- Header -->
    <div class="px-4 pt-4 pb-2 md:px-6 md:pt-6">
      <div class="flex items-center gap-3">
        <button
          @click="$router.push('/tasks')"
          class="p-2 -ml-2 text-lavender-400 hover:text-prussian-500 dark:hover:text-lavender-200 hover:bg-lavender-100 dark:hover:bg-prussian-700 rounded-xl transition-colors"
        >
          <ChevronLeftIcon class="w-5 h-5" />
        </button>
        <div class="flex-1 min-w-0">
          <h1 class="text-xl font-bold text-prussian-500 dark:text-lavender-200 truncate">{{ currentList?.name || 'Tasks' }}</h1>
          <p v-if="tasks.length > 0" class="text-xs text-lavender-500 dark:text-lavender-400 mt-0.5">
            {{ completedCount }} of {{ tasks.length }} completed
          </p>
        </div>

        <!-- List actions -->
        <ContextMenu v-if="currentList" :items="listMenuItems" />
      </div>

      <!-- Progress bar -->
      <div v-if="tasks.length > 0" class="mt-3">
        <div class="w-full h-1.5 bg-lavender-100 dark:bg-prussian-700 rounded-full overflow-hidden">
          <div
            class="h-full rounded-full transition-all duration-500 ease-out"
            :style="{ width: `${progressPercent}%`, backgroundColor: listColor }"
          />
        </div>
      </div>
    </div>

    <!-- Filter tabs -->
    <div class="px-4 md:px-6 py-2 flex gap-1">
      <button
        v-for="filter in filters"
        :key="filter.key"
        @click="activeFilter = filter.key"
        class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors"
        :class="activeFilter === filter.key
          ? 'bg-prussian-500 dark:bg-wisteria-600 text-white'
          : 'text-lavender-500 dark:text-lavender-400 hover:text-prussian-500 dark:hover:text-lavender-200 hover:bg-lavender-100 dark:hover:bg-prussian-700'"
      >
        {{ filter.label }}
        <span v-if="filter.count > 0" class="ml-1 opacity-60">{{ filter.count }}</span>
      </button>
    </div>

    <!-- Loading -->
    <div v-if="isLoading" class="flex items-center justify-center py-16">
      <LoadingSpinner size="lg" />
    </div>

    <!-- Tasks list -->
    <div v-else class="flex-1 overflow-y-auto pb-32 md:pb-6">
      <!-- Quick add -->
      <TaskQuickAdd @add="handleQuickAdd" />

      <!-- Task items -->
      <div v-if="filteredTasks.length > 0" class="mt-2">
        <TransitionGroup name="task-list" tag="div">
          <TaskItem
            v-for="task in filteredTasks"
            :key="task.id"
            :task="task"
            @click="openDetail(task)"
            @toggle="handleToggle(task)"
            @edit="openDetail(task)"
            @delete="confirmDelete(task)"
          />
        </TransitionGroup>
      </div>

      <!-- Completed section (collapsed) -->
      <div v-if="completedTasks.length > 0 && activeFilter === 'all'" class="mt-4 px-4">
        <button
          @click="showCompleted = !showCompleted"
          class="flex items-center gap-2 text-xs font-medium text-lavender-500 dark:text-lavender-400 hover:text-prussian-500 dark:hover:text-lavender-200 transition-colors"
        >
          <ChevronRightIcon
            class="w-3 h-3 transition-transform"
            :class="showCompleted && 'rotate-90'"
          />
          Completed ({{ completedTasks.length }})
        </button>

        <TransitionGroup v-if="showCompleted" name="task-list" tag="div" class="mt-1">
          <TaskItem
            v-for="task in completedTasks"
            :key="task.id"
            :task="task"
            @click="openDetail(task)"
            @toggle="handleToggle(task)"
            @edit="openDetail(task)"
            @delete="confirmDelete(task)"
          />
        </TransitionGroup>
      </div>

      <!-- Empty state -->
      <EmptyState
        v-if="tasks.length === 0 && !isLoading"
        :icon="CheckCircleIcon"
        title="All clear!"
        description="Add your first task to get started."
      />
    </div>

    <!-- Mobile FAB -->
    <FloatingActionButton @click="focusQuickAdd" />

    <!-- Task Detail Panel -->
    <TaskDetailPanel
      :task="selectedTask"
      :saving="savingTask"
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

    <!-- Undo Toast (for complete/delete) -->
    <UndoToast
      :show="!!undoAction"
      :message="undoAction?.message || ''"
      :undoable="true"
      @undo="handleUndo"
      @dismiss="undoAction = null"
    />

    <!-- Edit List Modal -->
    <BaseModal
      :show="showEditListModal"
      title="Edit List"
      size="sm"
      @close="showEditListModal = false"
    >
      <form @submit.prevent="handleUpdateList" class="space-y-5">
        <div>
          <label class="block text-sm font-medium text-prussian-500 dark:text-lavender-200 mb-1.5">Name</label>
          <input
            v-model="editListForm.name"
            class="input-base"
          />
        </div>
        <div class="flex gap-2 pt-2">
          <button type="button" @click="showEditListModal = false" class="flex-1 btn-secondary btn-md rounded-xl">
            Cancel
          </button>
          <button type="submit" class="flex-1 btn-primary btn-md rounded-xl">
            Save
          </button>
        </div>
      </form>
    </BaseModal>

    <!-- Delete List Confirmation -->
    <ConfirmDialog
      :show="showDeleteListConfirm"
      title="Delete List?"
      :message="`&quot;${currentList?.name}&quot; and all its tasks will be permanently deleted.`"
      confirmText="Delete"
      @confirm="handleDeleteList"
      @cancel="showDeleteListConfirm = false"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { storeToRefs } from 'pinia'
import { useTasksStore } from '@/stores/tasks'
import { useNotification } from '@/composables/useNotification'
import TaskItem from '@/components/tasks/TaskItem.vue'
import TaskQuickAdd from '@/components/tasks/TaskQuickAdd.vue'
import TaskDetailPanel from '@/components/tasks/TaskDetailPanel.vue'
import ContextMenu from '@/components/common/ContextMenu.vue'
import ConfirmDialog from '@/components/common/ConfirmDialog.vue'
import FloatingActionButton from '@/components/common/FloatingActionButton.vue'
import UndoToast from '@/components/common/UndoToast.vue'
import EmptyState from '@/components/common/EmptyState.vue'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import BaseModal from '@/components/common/BaseModal.vue'
import {
  ChevronLeftIcon,
  ChevronRightIcon,
  CheckCircleIcon,
  PencilIcon,
  TrashIcon,
} from '@heroicons/vue/24/outline'

const route = useRoute()
const router = useRouter()
const tasksStore = useTasksStore()
const { success, error: notifyError } = useNotification()

const { currentList, tasks, isLoading } = storeToRefs(tasksStore)

const listId = computed(() => route.params.listId)
const activeFilter = ref('all')
const showCompleted = ref(false)
const selectedTask = ref(null)
const savingTask = ref(false)
const taskToDelete = ref(null)
const undoAction = ref(null)
const showEditListModal = ref(false)
const showDeleteListConfirm = ref(false)
const editListForm = ref({ name: '' })

// Color for the current list
const colorMap = {
  wisteria: '#7d57a8',
  primary: '#7d57a8',
  prussian: '#05204A',
  sand: '#a5a84e',
  secondary: '#a5a84e',
  accent: '#B497D6',
  red: '#dc2626',
  green: '#059669',
  pink: '#db2777',
}
const listColor = computed(() => colorMap[currentList.value?.color] || colorMap.wisteria)

// Computed
const incompleteTasks = computed(() => tasks.value.filter((t) => !t.completed))
const completedTasks = computed(() => tasks.value.filter((t) => t.completed))
const completedCount = computed(() => completedTasks.value.length)
const progressPercent = computed(() =>
  tasks.value.length > 0 ? (completedCount.value / tasks.value.length) * 100 : 0
)

const filteredTasks = computed(() => {
  switch (activeFilter.value) {
    case 'active': return incompleteTasks.value
    case 'high': return incompleteTasks.value.filter((t) => t.priority === 'high')
    default: return incompleteTasks.value
  }
})

const filters = computed(() => [
  { key: 'all', label: 'All', count: incompleteTasks.value.length },
  { key: 'high', label: 'Priority', count: incompleteTasks.value.filter((t) => t.priority === 'high').length },
])

const listMenuItems = computed(() => [
  { label: 'Edit list', icon: PencilIcon, action: () => {
    editListForm.value.name = currentList.value?.name || ''
    showEditListModal.value = true
  }},
  { divider: true },
  { label: 'Delete list', icon: TrashIcon, variant: 'danger', action: () => {
    showDeleteListConfirm.value = true
  }},
])

// Actions
const handleQuickAdd = async (data) => {
  const result = await tasksStore.createTask({
    ...data,
    task_list_id: listId.value,
  })
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

  const result = await tasksStore.updateTask(selectedTask.value.id, {
    ...formData,
    task_list_id: listId.value,
  })

  if (result.success) {
    success('Task updated!')
    selectedTask.value = null
  } else {
    notifyError(result.error || 'Failed to update task')
  }
  savingTask.value = false
}

const handleToggle = async (task) => {
  const wasCompleted = task.completed
  await tasksStore.toggleComplete(task.id)

  if (!wasCompleted) {
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

const handleUpdateList = async () => {
  if (!currentList.value) return
  const result = await tasksStore.updateTaskList(currentList.value.id, editListForm.value)
  if (result.success) {
    success('List updated!')
    showEditListModal.value = false
  }
}

const handleDeleteList = async () => {
  if (!currentList.value) return
  const result = await tasksStore.deleteTaskList(currentList.value.id)
  if (result.success) {
    router.push('/tasks')
  }
  showDeleteListConfirm.value = false
}

const focusQuickAdd = () => {
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

// Fetch on mount & route change
const loadTasks = async () => {
  if (listId.value) {
    await tasksStore.fetchTasks(listId.value)
  }
}

onMounted(loadTasks)
watch(listId, loadTasks)
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
</style>
