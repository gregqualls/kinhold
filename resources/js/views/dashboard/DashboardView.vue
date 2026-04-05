<template>
  <div class="p-4 md:p-6 max-w-6xl">
    <!-- Edit mode toolbar -->
    <DashboardToolbar
      v-if="dashboardStore.editMode"
      :saving="dashboardStore.saving"
      @add="showPicker = true"
      @cancel="dashboardStore.exitEditMode()"
      @save="saveDashboard"
    />

    <!-- Edit button (shown when not editing) -->
    <div v-else class="flex justify-end mb-2">
      <button
        class="p-2 rounded-lg text-lavender-400 hover:text-wisteria-500 hover:bg-lavender-100 dark:hover:bg-prussian-700 transition-colors"
        title="Edit Dashboard"
        @click="dashboardStore.enterEditMode()"
      >
        <PencilSquareIcon class="w-5 h-5" />
      </button>
    </div>

    <!-- Loading skeleton -->
    <div v-if="dashboardStore.loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6 items-start">
      <div v-for="n in 6" :key="n" class="h-32 bg-lavender-100 dark:bg-prussian-700 rounded-card animate-pulse"></div>
    </div>

    <!-- Widget grid -->
    <div v-else ref="gridRef" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
      <DashboardWidget
        v-for="(widget, index) in dashboardStore.widgets"
        :key="widget.id"
        :data-id="widget.id"
        :config="widget"
        :edit-mode="dashboardStore.editMode"
        :index="index"
        @remove="dashboardStore.removeWidget(widget.id)"
        @resize="(size) => dashboardStore.resizeWidget(widget.id, size)"
      />
    </div>

    <!-- Empty state (no widgets) -->
    <div
      v-if="!dashboardStore.loading && dashboardStore.widgets.length === 0"
      class="text-center py-12"
    >
      <Squares2X2Icon class="w-12 h-12 text-lavender-400 dark:text-lavender-500 mx-auto mb-3" />
      <h3 class="text-lg font-semibold text-prussian-500 dark:text-lavender-200 mb-1">No widgets yet</h3>
      <p class="text-sm text-lavender-500 dark:text-lavender-400 mb-4">
        Add widgets to customize your dashboard, or ask the Assistant to build one for you.
      </p>
      <button
        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-wisteria-600 text-white hover:bg-wisteria-700 transition-colors"
        @click="dashboardStore.enterEditMode(); showPicker = true"
      >
        <PlusIcon class="w-4 h-4" />
        Add Widget
      </button>
    </div>

    <!-- Widget picker modal -->
    <WidgetPickerModal
      :show="showPicker"
      @close="showPicker = false"
      @add="onAddWidget"
    />
  </div>
</template>

<script setup>
import { ref, watch, nextTick, onMounted, onBeforeUnmount } from 'vue'
import Sortable from 'sortablejs'
import { useDashboardStore } from '@/stores/dashboard'
import { useFeaturedEventsStore } from '@/stores/featuredEvents'
import DashboardWidget from '@/components/dashboard/DashboardWidget.vue'
import DashboardToolbar from '@/components/dashboard/DashboardToolbar.vue'
import WidgetPickerModal from '@/components/dashboard/WidgetPickerModal.vue'
import { useNotification } from '@/composables/useNotification'
import { PencilSquareIcon, PlusIcon, Squares2X2Icon } from '@heroicons/vue/24/outline'

const dashboardStore = useDashboardStore()
const featuredEventsStore = useFeaturedEventsStore()
const showPicker = ref(false)
const gridRef = ref(null)
let sortableInstance = null

async function onAddWidget(widgetConfig) {
  dashboardStore.addWidget(widgetConfig)
  // Scroll to the newly added widget after Vue renders it
  await nextTick()
  if (gridRef.value) {
    const lastChild = gridRef.value.lastElementChild
    if (lastChild) {
      lastChild.scrollIntoView({ behavior: 'smooth', block: 'center' })
    }
  }
}

const { success: notifySuccess, error: notifyError } = useNotification()

async function saveDashboard() {
  try {
    await dashboardStore.saveConfig()
    notifySuccess('Dashboard saved')
  } catch {
    notifyError('Failed to save dashboard. Please try again.')
  }
}

function initSortable() {
  if (!gridRef.value) return
  sortableInstance = Sortable.create(gridRef.value, {
    animation: 250,
    handle: '.drag-handle',
    ghostClass: 'sortable-ghost',
    chosenClass: 'sortable-chosen',
    dragClass: 'sortable-drag',
    forceFallback: true,
    fallbackClass: 'sortable-fallback',
    fallbackOnBody: true,
    onEnd(evt) {
      if (evt.oldIndex !== evt.newIndex) {
        dashboardStore.moveWidget(evt.oldIndex, evt.newIndex)
      }
    },
  })
}

function destroySortable() {
  if (sortableInstance) {
    sortableInstance.destroy()
    sortableInstance = null
  }
}

// Toggle sortable when edit mode changes
watch(() => dashboardStore.editMode, async (editing) => {
  destroySortable()
  if (editing) {
    await nextTick()
    initSortable()
  }
})

onBeforeUnmount(() => {
  destroySortable()
})

onMounted(async () => {
  await dashboardStore.fetchConfig()
  featuredEventsStore.fetchEvents()
  featuredEventsStore.fetchCountdown()
})
</script>

<style scoped>
.sortable-ghost {
  opacity: 0.2;
}

.sortable-chosen {
  opacity: 0.8;
}

.sortable-fallback {
  opacity: 0.9;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
  border-radius: 12px;
}
</style>
