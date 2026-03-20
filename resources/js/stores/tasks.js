import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/services/api'

export const useTasksStore = defineStore('tasks', () => {
  const tags = ref([])
  const tasks = ref([])
  const selectedTagIds = ref([])
  const isLoading = ref(false)
  const error = ref(null)

  // Computed
  const incompleteTasks = computed(() =>
    tasks.value.filter((task) => !task.completed_at)
  )

  const completedTasks = computed(() =>
    tasks.value.filter((task) => !!task.completed_at)
  )

  const overdueTasks = computed(() =>
    tasks.value.filter((task) => {
      if (task.completed_at) return false
      if (!task.due_date) return false
      return new Date(task.due_date) < new Date()
    })
  )

  const myTasks = computed(() =>
    incompleteTasks.value.filter((task) => !task.assignee)
  )

  const familyTasks = computed(() =>
    incompleteTasks.value.filter((task) => task.assignee)
  )

  const filteredTasks = computed(() => {
    if (selectedTagIds.value.length === 0) return tasks.value
    return tasks.value.filter((task) => {
      const taskTagIds = (task.tags || []).map((t) => t.id)
      return selectedTagIds.value.some((id) => taskTagIds.includes(id))
    })
  })

  const filteredIncompleteTasks = computed(() =>
    filteredTasks.value.filter((t) => !t.completed_at)
  )

  const filteredCompletedTasks = computed(() =>
    filteredTasks.value.filter((t) => !!t.completed_at)
  )

  // Tag actions
  const fetchTags = async () => {
    try {
      const response = await api.get('/tags')
      tags.value = response.data.tags
      return { success: true }
    } catch (err) {
      return { success: false, error: err.response?.data?.message || 'Failed to fetch tags' }
    }
  }

  const createTag = async (data) => {
    try {
      const response = await api.post('/tags', data)
      tags.value.push(response.data.tag)
      return { success: true, tag: response.data.tag }
    } catch (err) {
      return { success: false, error: err.response?.data?.message }
    }
  }

  const updateTag = async (id, data) => {
    try {
      const response = await api.put(`/tags/${id}`, data)
      const index = tags.value.findIndex((t) => t.id === id)
      if (index !== -1) {
        tags.value[index] = response.data.tag
      }
      return { success: true, tag: response.data.tag }
    } catch (err) {
      return { success: false, error: err.response?.data?.message }
    }
  }

  const deleteTag = async (id) => {
    try {
      await api.delete(`/tags/${id}`)
      tags.value = tags.value.filter((t) => t.id !== id)
      selectedTagIds.value = selectedTagIds.value.filter((tid) => tid !== id)
      return { success: true }
    } catch (err) {
      return { success: false, error: err.response?.data?.message }
    }
  }

  const toggleTagFilter = (tagId) => {
    const idx = selectedTagIds.value.indexOf(tagId)
    if (idx === -1) {
      selectedTagIds.value.push(tagId)
    } else {
      selectedTagIds.value.splice(idx, 1)
    }
  }

  const clearTagFilter = () => {
    selectedTagIds.value = []
  }

  // Task actions
  const fetchTasks = async (filters = {}) => {
    isLoading.value = true
    error.value = null

    try {
      const params = new URLSearchParams(filters)
      const response = await api.get('/tasks', { params })
      tasks.value = response.data.tasks
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
      const response = await api.post('/tasks', data)
      tasks.value.unshift(response.data.task)
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
      const endpoint = task.completed_at ? `/tasks/${id}/uncomplete` : `/tasks/${id}/complete`
      const response = await api.patch(endpoint)
      const index = tasks.value.findIndex((t) => t.id === id)
      if (index !== -1) {
        tasks.value[index] = response.data.task
      }
      return { success: true, data: response.data }
    } catch (err) {
      return { success: false, error: err.response?.data?.message }
    }
  }

  return {
    // State
    tags,
    tasks,
    selectedTagIds,
    isLoading,
    error,

    // Computed
    incompleteTasks,
    completedTasks,
    overdueTasks,
    myTasks,
    familyTasks,
    filteredTasks,
    filteredIncompleteTasks,
    filteredCompletedTasks,

    // Tag actions
    fetchTags,
    createTag,
    updateTag,
    deleteTag,
    toggleTagFilter,
    clearTagFilter,

    // Task actions
    fetchTasks,
    createTask,
    updateTask,
    deleteTask,
    toggleComplete,
  }
})
