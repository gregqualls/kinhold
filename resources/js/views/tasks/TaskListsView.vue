<template>
  <div class="h-full flex flex-col">
    <!-- Header -->
    <div class="px-4 pt-4 pb-2 md:px-6 md:pt-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-prussian-500 dark:text-lavender-200">Tasks</h1>
          <p class="text-sm text-lavender-500 dark:text-lavender-400 mt-0.5">{{ totalIncomplete }} tasks remaining</p>
        </div>
        <button
          @click="showCreateModal = true"
          class="hidden md:flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-wisteria-600 hover:bg-wisteria-500 rounded-xl transition-colors"
        >
          <PlusIcon class="w-4 h-4" />
          New List
        </button>
      </div>
    </div>

    <!-- Smart Views (Today / Upcoming) -->
    <div class="px-4 md:px-6 py-3">
      <div class="flex gap-2">
        <RouterLink
          to="/tasks/today"
          class="flex-1 flex items-center gap-3 p-3.5 bg-sand-50 dark:bg-sand-900/20 rounded-xl hover:bg-sand-100 dark:hover:bg-sand-900/30 transition-colors"
        >
          <div class="w-9 h-9 rounded-lg bg-sand-200 dark:bg-sand-800 flex items-center justify-center">
            <SunIcon class="w-5 h-5 text-sand-700 dark:text-sand-400" />
          </div>
          <div>
            <p class="text-xs text-sand-700 dark:text-sand-400 font-medium">Today</p>
            <p class="text-lg font-bold text-sand-800 dark:text-sand-300">{{ todayCount }}</p>
          </div>
        </RouterLink>
        <RouterLink
          to="/tasks/upcoming"
          class="flex-1 flex items-center gap-3 p-3.5 bg-wisteria-50 dark:bg-wisteria-900/20 rounded-xl hover:bg-wisteria-100 dark:hover:bg-wisteria-900/30 transition-colors"
        >
          <div class="w-9 h-9 rounded-lg bg-wisteria-200 dark:bg-wisteria-800 flex items-center justify-center">
            <CalendarDaysIcon class="w-5 h-5 text-wisteria-700 dark:text-wisteria-400" />
          </div>
          <div>
            <p class="text-xs text-wisteria-700 dark:text-wisteria-400 font-medium">Upcoming</p>
            <p class="text-lg font-bold text-wisteria-800 dark:text-wisteria-300">{{ upcomingCount }}</p>
          </div>
        </RouterLink>
      </div>
    </div>

    <!-- Divider -->
    <div class="px-4 md:px-6">
      <div class="border-t border-lavender-200 dark:border-prussian-700" />
    </div>

    <!-- Section Label -->
    <div class="px-4 md:px-6 pt-4 pb-2">
      <h2 class="text-xs font-semibold text-lavender-500 dark:text-lavender-400 uppercase tracking-wider">My Lists</h2>
    </div>

    <!-- Loading State -->
    <div v-if="isLoading" class="flex items-center justify-center py-12">
      <LoadingSpinner size="lg" />
    </div>

    <!-- Task Lists -->
    <div v-else class="flex-1 overflow-y-auto px-4 md:px-6 pb-32 md:pb-6">
      <div v-if="taskLists && taskLists.length > 0" class="space-y-2">
        <div
          v-for="list in taskLists"
          :key="list.id"
          class="group flex items-center gap-3 px-4 py-3.5 bg-white dark:bg-prussian-800 rounded-xl shadow-card hover:shadow-card-lg border border-lavender-200 dark:border-prussian-700 hover:border-wisteria-300 dark:hover:border-wisteria-700 cursor-pointer transition-all"
          @click="$router.push(`/tasks/${list.id}`)"
        >
          <!-- Color dot + icon -->
          <div
            class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
            :style="{ backgroundColor: getListColor(list.color, 0.12) }"
          >
            <ListBulletIcon class="w-5 h-5" :style="{ color: getListColor(list.color) }" />
          </div>

          <!-- Info -->
          <div class="flex-1 min-w-0">
            <h3 class="font-semibold text-prussian-500 dark:text-lavender-200 text-sm truncate">{{ list.name }}</h3>
            <p class="text-xs text-lavender-500 dark:text-lavender-400 mt-0.5">
              {{ list.incomplete_tasks_count || 0 }} remaining
              <span v-if="list.tasks_count" class="text-lavender-400 dark:text-lavender-500">
                &middot; {{ list.tasks_count }} total
              </span>
            </p>
          </div>

          <!-- Progress ring -->
          <div v-if="list.tasks_count > 0" class="flex-shrink-0">
            <svg class="w-9 h-9 -rotate-90" viewBox="0 0 36 36">
              <circle cx="18" cy="18" r="14" fill="none" stroke-width="3" class="stroke-lavender-100 dark:stroke-prussian-700" />
              <circle
                cx="18" cy="18" r="14" fill="none" stroke-width="3"
                :stroke="getListColor(list.color)"
                stroke-linecap="round"
                :stroke-dasharray="getProgressDash(list)"
              />
            </svg>
          </div>

          <!-- Context menu -->
          <div class="opacity-0 group-hover:opacity-100 transition-opacity" @click.stop>
            <ContextMenu :items="getListMenuItems(list)" />
          </div>

          <!-- Mobile chevron -->
          <ChevronRightIcon class="w-4 h-4 text-lavender-400 md:hidden flex-shrink-0" />
        </div>
      </div>

      <!-- Empty State -->
      <EmptyState
        v-else
        :icon="ClipboardDocumentListIcon"
        title="No task lists yet"
        description="Create your first list to start organizing tasks."
        actionText="Create a List"
        @action="showCreateModal = true"
      />
    </div>

    <!-- Mobile FAB -->
    <FloatingActionButton @click="showCreateModal = true" />

    <!-- Create/Edit List Modal -->
    <BaseModal
      :show="showCreateModal || !!editingList"
      :title="editingList ? 'Edit List' : 'New List'"
      size="sm"
      @close="closeListModal"
    >
      <form @submit.prevent="handleSaveList" class="space-y-5">
        <div>
          <label class="block text-sm font-medium text-prussian-500 dark:text-lavender-200 mb-1.5">Name</label>
          <input
            ref="listNameInput"
            v-model="listForm.name"
            placeholder="e.g., Grocery, House Projects"
            class="input-base"
            autofocus
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-prussian-500 dark:text-lavender-200 mb-2">Color</label>
          <div class="flex gap-2">
            <button
              v-for="color in listColors"
              :key="color.name"
              type="button"
              @click="listForm.color = color.name"
              class="w-9 h-9 rounded-xl transition-all"
              :class="listForm.color === color.name && 'ring-2 ring-offset-2 scale-110'"
              :style="{ backgroundColor: color.hex, '--tw-ring-color': color.hex }"
            />
          </div>
        </div>

        <div class="flex gap-2 pt-2">
          <button type="button" @click="closeListModal" class="flex-1 btn-secondary btn-md rounded-xl">
            Cancel
          </button>
          <button type="submit" :disabled="!listForm.name?.trim() || savingList" class="flex-1 btn-primary btn-md rounded-xl disabled:opacity-40">
            {{ savingList ? 'Saving...' : (editingList ? 'Update' : 'Create') }}
          </button>
        </div>
      </form>
    </BaseModal>

    <!-- Delete Confirmation -->
    <ConfirmDialog
      :show="!!deletingList"
      title="Delete List?"
      :message="`&quot;${deletingList?.name}&quot; and all its tasks will be permanently deleted.`"
      confirmText="Delete"
      :loading="deletingInProgress"
      @confirm="handleDeleteList"
      @cancel="deletingList = null"
    />

    <!-- Undo Toast -->
    <UndoToast
      :show="!!undoToast"
      :message="undoToast?.message || ''"
      :undoable="true"
      @undo="handleUndo"
      @dismiss="undoToast = null"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import { storeToRefs } from 'pinia'
import { useTasksStore } from '@/stores/tasks'
import { useAuthStore } from '@/stores/auth'
import { useNotification } from '@/composables/useNotification'
import BaseModal from '@/components/common/BaseModal.vue'
import EmptyState from '@/components/common/EmptyState.vue'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import ContextMenu from '@/components/common/ContextMenu.vue'
import ConfirmDialog from '@/components/common/ConfirmDialog.vue'
import FloatingActionButton from '@/components/common/FloatingActionButton.vue'
import UndoToast from '@/components/common/UndoToast.vue'
import {
  PlusIcon,
  SunIcon,
  CalendarDaysIcon,
  ListBulletIcon,
  ChevronRightIcon,
  PencilIcon,
  TrashIcon,
  ClipboardDocumentListIcon,
} from '@heroicons/vue/24/outline'

const tasksStore = useTasksStore()
const authStore = useAuthStore()
const { success, error: notifyError } = useNotification()

const { taskLists, isLoading } = storeToRefs(tasksStore)
const { isParent } = storeToRefs(authStore)

const showCreateModal = ref(false)
const editingList = ref(null)
const deletingList = ref(null)
const deletingInProgress = ref(false)
const savingList = ref(false)
const undoToast = ref(null)
const listNameInput = ref(null)

const listForm = ref({
  name: '',
  color: 'wisteria',
})

const listColors = [
  { name: 'wisteria', hex: '#7d57a8' },
  { name: 'prussian', hex: '#05204A' },
  { name: 'sand', hex: '#a5a84e' },
  { name: 'red', hex: '#dc2626' },
  { name: 'green', hex: '#059669' },
  { name: 'pink', hex: '#db2777' },
]

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

const getListColor = (colorName, alpha) => {
  const hex = colorMap[colorName] || colorMap.wisteria
  if (alpha) {
    const r = parseInt(hex.slice(1, 3), 16)
    const g = parseInt(hex.slice(3, 5), 16)
    const b = parseInt(hex.slice(5, 7), 16)
    return `rgba(${r}, ${g}, ${b}, ${alpha})`
  }
  return hex
}

const getProgressDash = (list) => {
  const circumference = 2 * Math.PI * 14
  const completed = (list.tasks_count || 0) - (list.incomplete_tasks_count || 0)
  const progress = list.tasks_count > 0 ? completed / list.tasks_count : 0
  return `${circumference * progress} ${circumference}`
}

const totalIncomplete = computed(() =>
  (taskLists.value || []).reduce((sum, l) => sum + (l.incomplete_tasks_count || 0), 0)
)

const todayCount = computed(() => 0) // TODO: implement when smart views are ready
const upcomingCount = computed(() => 0) // TODO: implement when smart views are ready

const getListMenuItems = (list) => [
  { label: 'Edit', icon: PencilIcon, action: () => startEditList(list) },
  { divider: true },
  { label: 'Delete', icon: TrashIcon, variant: 'danger', action: () => { deletingList.value = list } },
]

const startEditList = (list) => {
  editingList.value = list
  listForm.value = { name: list.name, color: list.color || 'wisteria' }
  nextTick(() => listNameInput.value?.focus())
}

const closeListModal = () => {
  showCreateModal.value = false
  editingList.value = null
  listForm.value = { name: '', color: 'wisteria' }
}

const handleSaveList = async () => {
  if (!listForm.value.name?.trim()) return
  savingList.value = true

  try {
    if (editingList.value) {
      const result = await tasksStore.updateTaskList(editingList.value.id, listForm.value)
      if (result.success) {
        success('List updated!')
        closeListModal()
      } else {
        notifyError(result.error || 'Failed to update list')
      }
    } else {
      const result = await tasksStore.createTaskList(listForm.value)
      if (result.success) {
        success('List created!')
        closeListModal()
      } else {
        notifyError(result.error || 'Failed to create list')
      }
    }
  } finally {
    savingList.value = false
  }
}

const handleDeleteList = async () => {
  if (!deletingList.value) return
  deletingInProgress.value = true

  const result = await tasksStore.deleteTaskList(deletingList.value.id)
  if (result.success) {
    undoToast.value = { message: `"${deletingList.value.name}" deleted` }
  } else {
    notifyError(result.error || 'Failed to delete list')
  }

  deletingList.value = null
  deletingInProgress.value = false
}

const handleUndo = () => {
  // TODO: implement undo (re-create list)
  undoToast.value = null
}

onMounted(async () => {
  await tasksStore.fetchTaskLists()
})
</script>
