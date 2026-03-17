import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/services/api'

export const useTasksStore = defineStore('tasks', () => {
  const taskLists = ref([])
  const tasks = ref([])
  const currentList = ref(null)
  const isLoading = ref(false)
  const error = ref(null)

  // Computed
  const incompleteTasks = computed(() =>
    tasks.value.filter((task) => !task.completed)
  )

  const overdueTasks = computed(() =>
    tasks.value.filter((task) => {
      if (task.completed) return false
      if (!task.due_date) return false
      return new Date(task.due_date) < new Date()
    })
  )

  const myTasks = computed(() =>
    tasks.value.filter((task) => task.assigned_to_id === null) // Unassigned tasks
  )

  const familyTasks = computed(() =>
    tasks.value.filter((task) => task.assigned_to_id !== null)
  )

  const tasksByList = computed(() => {
    const grouped = {}
    taskLists.value.forEach((list) => {
      grouped[list.id] = tasks.value.filter((task) => task.task_list_id === list.id)
    })
    return grouped
  })

  // Actions
  const fetchTaskLists = async () => {
    isLoading.value = true
    error.value = null

    try {
      const response = await api.get('/task-lists')
      taskLists.value = response.data.task_lists
      return { success: true }
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch task lists'
      return { success: false, error: error.value }
    } finally {
      isLoading.value = false
    }
  }

  const fetchTasks = async (listId, filters = {}) => {
    isLoading.value = true
    error.value = null

    try {
      const params = new URLSearchParams(filters)
      const url = listId ? `/task-lists/${listId}/tasks` : '/tasks'
      const response = await api.get(url, { params })

      tasks.value = response.data.tasks
      if (listId) {
        // If task lists aren't loaded yet, fetch them first
        if (taskLists.value.length === 0) {
          await fetchTaskLists()
        }
        currentList.value = taskLists.value.find((l) => l.id === listId) || null
      }

      return { success: true }
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch tasks'
      return { success: false, error: error.value }
    } finally {
      isLoading.value = false
    }
  }

  const createTask = async (data) => {
    try {
      const listId = data.task_list_id
      const response = await api.post(`/task-lists/${listId}/tasks`, data)
      tasks.value.push(response.data.task)
      return { success: true, task: response.data.task }
    } catch (err) {
      return { success: false, error: err.response?.data?.message }
    }
  }

  const updateTask = async (id, data) => {
    try {
      const response = await api.put(`/tasks/${id}`, data)
      const index = tasks.value.findIndex((t) => t.id === id)
      if (index !== -1) {
        tasks.value[index] = response.data.task
      }
      return { success: true, task: response.data.task }
    } catch (err) {
      return { success: false, error: err.response?.data?.message }
    }
  }

  const deleteTask = async (id) => {
    try {
      await api.delete(`/tasks/${id}`)
      tasks.value = tasks.value.filter((t) => t.id !== id)
      return { success: true }
    } catch (err) {
      return { success: false, error: err.response?.data?.message }
    }
  }

  const toggleComplete = async (id) => {
    const task = tasks.value.find((t) => t.id === id)
    if (!task) return { success: false }

    try {
      const endpoint = task.completed ? `/tasks/${id}/uncomplete` : `/tasks/${id}/complete`
      const response = await api.patch(endpoint)
      task.completed = response.data.task?.completed ?? !task.completed
      task.completed_at = response.data.task?.completed_at ?? null
      return { success: true }
    } catch (err) {
      return { success: false, error: err.response?.data?.message }
    }
  }

  const createTaskList = async (data) => {
    try {
      const response = await api.post('/task-lists', data)
      taskLists.value.push(response.data.task_list)
      return { success: true, list: response.data.task_list }
    } catch (err) {
      return { success: false, error: err.response?.data?.message }
    }
  }

  const updateTaskList = async (id, data) => {
    try {
      const response = await api.put(`/task-lists/${id}`, data)
      const index = taskLists.value.findIndex((l) => l.id === id)
      if (index !== -1) {
        taskLists.value[index] = response.data.task_list
      }
      return { success: true, list: response.data.task_list }
    } catch (err) {
      return { success: false, error: err.response?.data?.message }
    }
  }

  const deleteTaskList = async (id) => {
    try {
      await api.delete(`/task-lists/${id}`)
      taskLists.value = taskLists.value.filter((l) => l.id !== id)
      tasks.value = tasks.value.filter((t) => t.task_list_id !== id)
      return { success: true }
    } catch (err) {
      return { success: false, error: err.response?.data?.message }
    }
  }

  return {
    // State
    taskLists,
    tasks,
    currentList,
    isLoading,
    error,

    // Computed
    incompleteTasks,
    overdueTasks,
    myTasks,
    familyTasks,
    tasksByList,

    // Actions
    fetchTaskLists,
    fetchTasks,
    createTask,
    updateTask,
    deleteTask,
    toggleComplete,
    createTaskList,
    updateTaskList,
    deleteTaskList,
  }
})
