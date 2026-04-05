import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/services/api'

export const useDashboardStore = defineStore('dashboard', () => {
  const config = ref(null)
  const draftConfig = ref(null)
  const editMode = ref(false)
  const loading = ref(false)
  const saving = ref(false)

  /**
   * Active widgets — draft during edit mode, saved config otherwise.
   */
  const widgets = computed(() => {
    const source = editMode.value ? draftConfig.value : config.value
    return source?.widgets || []
  })

  /**
   * Fetch dashboard config from API.
   */
  async function fetchConfig() {
    loading.value = true
    try {
      const { data } = await api.get('/user/dashboard')
      config.value = data.config
    } catch (e) {
      // eslint-disable-next-line no-console
      console.error('Failed to fetch dashboard config:', e)
    } finally {
      loading.value = false
    }
  }

  /**
   * Save dashboard config to API.
   */
  async function saveConfig() {
    const configToSave = editMode.value ? draftConfig.value : config.value
    if (!configToSave) return

    saving.value = true
    try {
      const { data } = await api.put('/user/dashboard', { config: configToSave })
      config.value = data.config
      exitEditMode()
    } catch (e) {
      // eslint-disable-next-line no-console
      console.error('Failed to save dashboard config:', e)
      throw e
    } finally {
      saving.value = false
    }
  }

  /**
   * Enter edit mode — clone config into draftConfig.
   */
  function enterEditMode() {
    draftConfig.value = JSON.parse(JSON.stringify(config.value))
    editMode.value = true
  }

  /**
   * Exit edit mode — discard draft.
   */
  function exitEditMode() {
    editMode.value = false
    draftConfig.value = null
  }

  /**
   * Add a widget to the draft config.
   */
  function addWidget(widgetConfig) {
    if (!draftConfig.value) return
    draftConfig.value.widgets.push(widgetConfig)
  }

  /**
   * Remove a widget from the draft config by ID.
   */
  function removeWidget(id) {
    if (!draftConfig.value) return
    draftConfig.value.widgets = draftConfig.value.widgets.filter((w) => w.id !== id)
  }

  /**
   * Resize a widget in the draft config.
   */
  function resizeWidget(id, newSize) {
    if (!draftConfig.value) return
    const widget = draftConfig.value.widgets.find((w) => w.id === id)
    if (widget) widget.size = newSize
  }

  /**
   * Reorder widgets by moving a widget from one index to another.
   */
  function moveWidget(fromIndex, toIndex) {
    if (!draftConfig.value) return
    const widgets = [...draftConfig.value.widgets]
    const [moved] = widgets.splice(fromIndex, 1)
    widgets.splice(toIndex, 0, moved)
    draftConfig.value = { ...draftConfig.value, widgets }
  }

  /**
   * Replace the entire config (used by AI/MCP).
   */
  function setConfig(newConfig) {
    config.value = newConfig
  }

  return {
    config,
    draftConfig,
    editMode,
    loading,
    saving,
    widgets,
    fetchConfig,
    saveConfig,
    enterEditMode,
    exitEditMode,
    addWidget,
    removeWidget,
    resizeWidget,
    moveWidget,
    setConfig,
  }
})
