<template>
  <div class="px-4">
    <div
      v-if="!isEditing"
      @click="startEditing"
      class="flex items-center gap-3 py-3 text-lavender-400 hover:text-prussian-500 dark:hover:text-lavender-200 cursor-pointer rounded-xl hover:bg-lavender-50 dark:hover:bg-prussian-700 px-4 transition-colors"
    >
      <PlusIcon class="w-5 h-5" />
      <span class="text-sm">Add a task...</span>
    </div>

    <div v-else class="bg-white dark:bg-prussian-800 border border-lavender-200 dark:border-prussian-700 rounded-xl p-4 shadow-card">
      <input
        ref="inputRef"
        v-model="title"
        @keydown.enter.prevent="submit"
        @keydown.escape="cancel"
        placeholder="Task name"
        class="w-full text-sm font-medium text-prussian-500 dark:text-lavender-200 placeholder-lavender-400 outline-none bg-transparent"
      />

      <!-- Quick options row -->
      <div class="flex items-center gap-2 mt-3 flex-wrap">
        <!-- Due date -->
        <label
          class="flex items-center gap-1.5 px-2.5 py-1.5 text-xs rounded-lg cursor-pointer transition-colors"
          :class="dueDate ? 'bg-wisteria-50 dark:bg-wisteria-900/20 text-wisteria-700 dark:text-wisteria-400' : 'bg-lavender-100 dark:bg-prussian-700 text-lavender-600 dark:text-lavender-400 hover:bg-lavender-200 dark:hover:bg-prussian-600'"
        >
          <CalendarIcon class="w-3.5 h-3.5" />
          {{ dueDate ? formatDueDate(dueDate) : 'Date' }}
          <input
            type="date"
            v-model="dueDate"
            class="sr-only"
          />
        </label>

        <!-- Priority -->
        <button
          @click="cyclePriority"
          class="flex items-center gap-1.5 px-2.5 py-1.5 text-xs rounded-lg transition-colors"
          :class="priorityClass"
        >
          <FlagIcon class="w-3.5 h-3.5" />
          {{ priority }}
        </button>

        <!-- Family task toggle -->
        <button
          @click="isFamilyTask = !isFamilyTask"
          class="flex items-center gap-1.5 px-2.5 py-1.5 text-xs rounded-lg transition-colors"
          :class="isFamilyTask ? 'bg-wisteria-50 dark:bg-wisteria-900/20 text-wisteria-700 dark:text-wisteria-400' : 'bg-lavender-100 dark:bg-prussian-700 text-lavender-600 dark:text-lavender-400 hover:bg-lavender-200 dark:hover:bg-prussian-600'"
        >
          <UserGroupIcon class="w-3.5 h-3.5" />
          {{ isFamilyTask ? 'Anyone' : 'Assign' }}
        </button>

        <!-- Tag chips -->
        <button
          v-for="tag in tags"
          :key="tag.id"
          @click="toggleTag(tag.id)"
          class="flex items-center gap-1 px-2 py-1.5 text-xs rounded-lg transition-colors"
          :class="selectedTagIds.includes(tag.id)
            ? 'text-white'
            : 'bg-lavender-100 dark:bg-prussian-700 text-lavender-600 dark:text-lavender-400 hover:bg-lavender-200 dark:hover:bg-prussian-600'"
          :style="selectedTagIds.includes(tag.id) ? { backgroundColor: getTagHex(tag.color) } : {}"
        >
          <span
            v-if="!selectedTagIds.includes(tag.id)"
            class="w-1.5 h-1.5 rounded-full"
            :style="{ backgroundColor: getTagHex(tag.color) }"
          />
          {{ tag.name }}
        </button>

        <div class="flex-1" />

        <!-- Actions -->
        <button
          @click="cancel"
          class="px-3 py-1.5 text-xs text-lavender-500 dark:text-lavender-400 hover:text-prussian-500 dark:hover:text-lavender-200 rounded-lg hover:bg-lavender-100 dark:hover:bg-prussian-700 transition-colors"
        >
          Cancel
        </button>
        <button
          @click="submit"
          :disabled="!title.trim()"
          class="px-3 py-1.5 text-xs font-medium text-white bg-wisteria-600 hover:bg-wisteria-500 rounded-lg disabled:opacity-40 disabled:cursor-not-allowed transition-colors"
        >
          Add Task
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, nextTick, computed } from 'vue'
import { PlusIcon, CalendarIcon, FlagIcon, UserGroupIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  tags: { type: Array, default: () => [] },
})

const emit = defineEmits(['add'])

const colorMap = {
  wisteria: '#7d57a8',
  prussian: '#05204A',
  sand: '#a5a84e',
  red: '#dc2626',
  green: '#059669',
  pink: '#db2777',
}

const getTagHex = (colorName) => colorMap[colorName] || colorName || colorMap.wisteria

const isEditing = ref(false)
const inputRef = ref(null)
const title = ref('')
const dueDate = ref('')
const priority = ref('medium')
const isFamilyTask = ref(false)
const selectedTagIds = ref([])

const startEditing = async () => {
  isEditing.value = true
  await nextTick()
  inputRef.value?.focus()
}

defineExpose({ startEditing })

const cancel = () => {
  isEditing.value = false
  title.value = ''
  dueDate.value = ''
  priority.value = 'medium'
  isFamilyTask.value = false
  selectedTagIds.value = []
}

const toggleTag = (tagId) => {
  const idx = selectedTagIds.value.indexOf(tagId)
  if (idx === -1) {
    selectedTagIds.value.push(tagId)
  } else {
    selectedTagIds.value.splice(idx, 1)
  }
}

const submit = () => {
  if (!title.value.trim()) return
  emit('add', {
    title: title.value.trim(),
    due_date: dueDate.value || null,
    priority: priority.value,
    is_family_task: isFamilyTask.value,
    tag_ids: selectedTagIds.value.length > 0 ? selectedTagIds.value : undefined,
  })
  title.value = ''
  dueDate.value = ''
  isFamilyTask.value = false
  selectedTagIds.value = []
  // Keep editing open for rapid entry
  nextTick(() => inputRef.value?.focus())
}

const cyclePriority = () => {
  const cycle = { low: 'medium', medium: 'high', high: 'low' }
  priority.value = cycle[priority.value]
}

const priorityClass = computed(() => {
  const classes = {
    high: 'bg-red-50 text-red-600',
    medium: 'bg-orange-50 text-orange-600',
    low: 'bg-lavender-100 dark:bg-prussian-700 text-lavender-600 dark:text-lavender-400 hover:bg-lavender-200 dark:hover:bg-prussian-600',
  }
  return classes[priority.value]
})

const formatDueDate = (dateStr) => {
  const d = new Date(dateStr)
  const today = new Date()
  today.setHours(0, 0, 0, 0)
  const tomorrow = new Date(today)
  tomorrow.setDate(tomorrow.getDate() + 1)
  const dueDay = new Date(d)
  dueDay.setHours(0, 0, 0, 0)

  if (dueDay.getTime() === today.getTime()) return 'Today'
  if (dueDay.getTime() === tomorrow.getTime()) return 'Tomorrow'
  return dueDay.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })
}
</script>
