<template>
  <div class="px-4">
    <div
      v-if="!isEditing"
      class="flex items-center gap-3 py-3 text-ink-tertiary hover:text-ink-primary cursor-pointer rounded-xl hover:bg-surface-sunken px-4 transition-colors"
      @click="startEditing"
    >
      <PlusIcon class="w-5 h-5" />
      <span class="text-sm">Add a task...</span>
    </div>

    <div v-else class="bg-surface-raised border border-border-subtle rounded-xl p-4 shadow-card">
      <input
        ref="inputRef"
        v-model="title"
        placeholder="Task name"
        class="w-full text-sm font-medium text-ink-primary placeholder-ink-tertiary outline-none bg-transparent"
        @keydown.enter.prevent="submit"
        @keydown.escape="cancel"
      />

      <!-- Quick options row -->
      <div class="flex items-center gap-2 mt-3 flex-wrap">
        <!-- Due date -->
        <label
          class="flex items-center gap-1.5 px-2.5 py-1.5 text-xs rounded-lg cursor-pointer transition-colors"
          :class="dueDate ? 'bg-accent-lavender-soft/40 text-accent-lavender-bold' : 'bg-surface-sunken text-ink-secondary hover:bg-surface-overlay'"
        >
          <CalendarIcon class="w-3.5 h-3.5" />
          {{ dueDate ? formatDueDate(dueDate) : 'Date' }}
          <input
            v-model="dueDate"
            type="date"
            class="sr-only"
          />
        </label>

        <!-- Priority -->
        <button
          class="flex items-center gap-1.5 px-2.5 py-1.5 text-xs rounded-lg transition-colors"
          :class="priorityClass"
          @click="cyclePriority"
        >
          <FlagIcon class="w-3.5 h-3.5" />
          {{ priority }}
        </button>

        <!-- Family task toggle (hidden for children who can't assign to others) -->
        <button
          v-if="canAssignTasks"
          class="flex items-center gap-1.5 px-2.5 py-1.5 text-xs rounded-lg transition-colors"
          :class="isFamilyTask ? 'bg-accent-lavender-soft/40 text-accent-lavender-bold' : 'bg-surface-sunken text-ink-secondary hover:bg-surface-overlay'"
          @click="isFamilyTask = !isFamilyTask"
        >
          <UserGroupIcon class="w-3.5 h-3.5" />
          {{ isFamilyTask ? 'Anyone' : 'Assign' }}
        </button>

        <!-- Tag chips -->
        <button
          v-for="tag in tags"
          :key="tag.id"
          class="flex items-center gap-1 px-2 py-1.5 text-xs rounded-lg transition-colors"
          :class="selectedTagIds.includes(tag.id)
            ? 'text-white'
            : 'bg-surface-sunken text-ink-secondary hover:bg-surface-overlay'"
          :style="selectedTagIds.includes(tag.id) ? { backgroundColor: getTagHex(tag.color) } : {}"
          @click="toggleTag(tag.id)"
        >
          <span
            v-if="!selectedTagIds.includes(tag.id)"
            class="w-1.5 h-1.5 rounded-full"
            :style="{ backgroundColor: getTagHex(tag.color) }"
          ></span>
          {{ tag.name }}
        </button>

        <div class="flex-1"></div>

        <!-- Actions -->
        <KinButton variant="ghost" size="sm" @click="cancel">
          Cancel
        </KinButton>
        <KinButton
          variant="primary"
          size="sm"
          :disabled="!title.trim()"
          @click="submit"
        >
          Add Task
        </KinButton>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, nextTick, computed } from 'vue'
import { storeToRefs } from 'pinia'
import { useAuthStore } from '@/stores/auth'
import { PlusIcon, CalendarIcon, FlagIcon, UserGroupIcon } from '@heroicons/vue/24/outline'
import KinButton from '@/components/design-system/KinButton.vue'

defineProps({
  tags: { type: Array, default: () => [] },
})

const authStore = useAuthStore()
const { canAssignTasks } = storeToRefs(authStore)

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
    high: 'bg-status-failed/10 text-status-failed',
    medium: 'bg-orange-50 text-orange-600',
    low: 'bg-surface-sunken text-ink-secondary hover:bg-surface-overlay',
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
