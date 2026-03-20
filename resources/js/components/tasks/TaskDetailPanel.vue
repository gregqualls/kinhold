<template>
  <SlidePanel :show="!!task" title="Task Details" @close="$emit('close')">
    <div v-if="task" class="p-6 space-y-6">
      <!-- Title -->
      <div>
        <label class="block text-xs font-medium text-lavender-500 dark:text-lavender-400 uppercase tracking-wider mb-1.5">Title</label>
        <input
          v-model="form.title"
          class="w-full text-lg font-semibold text-prussian-500 dark:text-lavender-200 border-0 border-b-2 border-transparent focus:border-wisteria-400 outline-none py-1 bg-transparent transition-colors"
          placeholder="Task title"
        />
      </div>

      <!-- Description -->
      <div>
        <label class="block text-xs font-medium text-lavender-500 dark:text-lavender-400 uppercase tracking-wider mb-1.5">Description</label>
        <textarea
          v-model="form.description"
          rows="3"
          class="w-full text-sm text-prussian-500 dark:text-lavender-200 border border-lavender-200 dark:border-prussian-700 rounded-xl px-3 py-2 bg-white dark:bg-prussian-700 focus:ring-2 focus:ring-wisteria-400 focus:border-transparent outline-none resize-none transition-all"
          placeholder="Add details..."
        />
      </div>

      <!-- Tags -->
      <div>
        <label class="block text-xs font-medium text-lavender-500 dark:text-lavender-400 uppercase tracking-wider mb-2">Tags</label>
        <div class="flex flex-wrap gap-2">
          <button
            v-for="tag in tags"
            :key="tag.id"
            @click="toggleTag(tag.id)"
            class="flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium rounded-full transition-colors"
            :class="form.tag_ids.includes(tag.id)
              ? 'text-white'
              : 'bg-lavender-100 dark:bg-prussian-700 text-lavender-600 dark:text-lavender-400 hover:bg-lavender-200 dark:hover:bg-prussian-600'"
            :style="form.tag_ids.includes(tag.id) ? { backgroundColor: getTagHex(tag.color) } : {}"
          >
            <span
              v-if="!form.tag_ids.includes(tag.id)"
              class="w-2 h-2 rounded-full"
              :style="{ backgroundColor: getTagHex(tag.color) }"
            />
            {{ tag.name }}
          </button>
        </div>
      </div>

      <!-- Priority -->
      <div>
        <label class="block text-xs font-medium text-lavender-500 dark:text-lavender-400 uppercase tracking-wider mb-2">Priority</label>
        <div class="flex gap-2">
          <button
            v-for="p in ['low', 'medium', 'high']"
            :key="p"
            @click="form.priority = p"
            class="flex-1 flex items-center justify-center gap-1.5 py-2.5 rounded-xl text-sm font-medium transition-all"
            :class="form.priority === p ? prioritySelectedClass(p) : 'bg-lavender-50 dark:bg-prussian-700 text-lavender-500 dark:text-lavender-400 hover:bg-lavender-100 dark:hover:bg-prussian-600'"
          >
            <FlagIcon class="w-4 h-4" />
            {{ p.charAt(0).toUpperCase() + p.slice(1) }}
          </button>
        </div>
      </div>

      <!-- Due Date -->
      <div>
        <label class="block text-xs font-medium text-lavender-500 dark:text-lavender-400 uppercase tracking-wider mb-1.5">Due Date</label>
        <input
          v-model="form.due_date"
          type="date"
          class="w-full text-sm text-prussian-500 dark:text-lavender-200 border border-lavender-200 dark:border-prussian-700 rounded-xl px-3 py-2 bg-white dark:bg-prussian-700 focus:ring-2 focus:ring-wisteria-400 focus:border-transparent outline-none transition-all"
        />
      </div>

      <!-- Assignee -->
      <div>
        <label class="block text-xs font-medium text-lavender-500 dark:text-lavender-400 uppercase tracking-wider mb-1.5">Assigned To</label>
        <select
          v-model="form.assigned_to"
          class="w-full text-sm text-prussian-500 dark:text-lavender-200 border border-lavender-200 dark:border-prussian-700 rounded-xl px-3 py-2 bg-white dark:bg-prussian-700 focus:ring-2 focus:ring-wisteria-400 focus:border-transparent outline-none transition-all"
        >
          <option :value="null">Unassigned</option>
          <option
            v-for="member in familyMembers"
            :key="member.id"
            :value="member.id"
          >
            {{ member.name }}
          </option>
        </select>
      </div>

      <!-- Status -->
      <div class="flex items-center justify-between py-3 px-4 bg-lavender-50 dark:bg-prussian-700 rounded-xl">
        <span class="text-sm text-prussian-500 dark:text-lavender-200 font-medium">Completed</span>
        <button
          @click="form.completed = !form.completed"
          class="relative w-11 h-6 rounded-full transition-colors"
          :class="form.completed ? 'bg-emerald-500' : 'bg-lavender-300 dark:bg-prussian-500'"
        >
          <span
            class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform"
            :class="form.completed && 'translate-x-5'"
          />
        </button>
      </div>
    </div>

    <template #footer>
      <div class="flex gap-3 items-center">
        <button
          @click="$emit('delete', task.id)"
          class="px-4 py-2.5 text-sm font-medium text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-xl transition-colors"
        >
          Delete
        </button>
        <div class="flex-1" />

        <!-- Unsaved changes indicator -->
        <Transition name="fade-fast" mode="out-in">
          <span v-if="isDirty && !justSaved" key="dirty" class="text-xs text-sand-600 dark:text-sand-400 font-medium">
            Unsaved changes
          </span>
          <span v-else-if="justSaved" key="saved" class="text-xs text-emerald-600 dark:text-emerald-400 font-medium flex items-center gap-1">
            <CheckCircleIcon class="w-4 h-4" />
            Saved!
          </span>
        </Transition>

        <button
          @click="$emit('close')"
          class="px-4 py-2.5 text-sm font-medium text-lavender-600 dark:text-lavender-400 hover:bg-lavender-100 dark:hover:bg-prussian-700 rounded-xl transition-colors"
        >
          Cancel
        </button>
        <button
          @click="save"
          :disabled="saving || !form.title?.trim()"
          :class="[
            'px-6 py-2.5 text-sm font-medium text-white rounded-xl transition-all',
            isDirty
              ? 'bg-wisteria-600 hover:bg-wisteria-500 shadow-md shadow-wisteria-600/30 scale-[1.02]'
              : 'bg-wisteria-600 hover:bg-wisteria-500',
            (saving || !form.title?.trim()) && 'opacity-40 !shadow-none !scale-100',
          ]"
        >
          {{ saving ? 'Saving...' : 'Save' }}
        </button>
      </div>
    </template>
  </SlidePanel>
</template>

<script setup>
import { reactive, watch, ref, computed } from 'vue'
import { storeToRefs } from 'pinia'
import { useAuthStore } from '@/stores/auth'
import { FlagIcon, CheckCircleIcon } from '@heroicons/vue/24/outline'
import SlidePanel from '@/components/common/SlidePanel.vue'

const props = defineProps({
  task: Object,
  saving: Boolean,
  tags: { type: Array, default: () => [] },
})

const emit = defineEmits(['save', 'close', 'delete'])

const authStore = useAuthStore()
const { familyMembers } = storeToRefs(authStore)

const justSaved = ref(false)
let savedTimer = null

const colorMap = {
  wisteria: '#7d57a8',
  prussian: '#05204A',
  sand: '#a5a84e',
  red: '#dc2626',
  green: '#059669',
  pink: '#db2777',
}

const getTagHex = (colorName) => colorMap[colorName] || colorName || colorMap.wisteria

const form = reactive({
  title: '',
  description: '',
  priority: 'medium',
  due_date: '',
  assigned_to: null,
  completed: false,
  tag_ids: [],
})

const originalValues = ref({})

const isDirty = computed(() => {
  if (!props.task) return false
  return (
    form.title !== originalValues.value.title ||
    form.description !== originalValues.value.description ||
    form.priority !== originalValues.value.priority ||
    form.due_date !== originalValues.value.due_date ||
    form.assigned_to !== originalValues.value.assigned_to ||
    form.completed !== originalValues.value.completed ||
    JSON.stringify([...form.tag_ids].sort()) !== JSON.stringify([...originalValues.value.tag_ids].sort())
  )
})

const toggleTag = (tagId) => {
  const idx = form.tag_ids.indexOf(tagId)
  if (idx === -1) {
    form.tag_ids.push(tagId)
  } else {
    form.tag_ids.splice(idx, 1)
  }
}

watch(() => props.task, (t) => {
  justSaved.value = false
  if (t) {
    form.title = t.title || ''
    form.description = t.description || ''
    form.priority = t.priority || 'medium'
    form.due_date = t.due_date || ''
    form.assigned_to = t.assignee?.id || null
    form.completed = !!t.completed_at
    form.tag_ids = (t.tags || []).map((tag) => tag.id)
    originalValues.value = {
      title: form.title,
      description: form.description,
      priority: form.priority,
      due_date: form.due_date,
      assigned_to: form.assigned_to,
      completed: form.completed,
      tag_ids: [...form.tag_ids],
    }
  }
}, { immediate: true })

const save = () => {
  emit('save', { ...form })
  originalValues.value = { ...form, tag_ids: [...form.tag_ids] }
  justSaved.value = true
  clearTimeout(savedTimer)
  savedTimer = setTimeout(() => {
    justSaved.value = false
  }, 2000)
}

const prioritySelectedClass = (p) => {
  const classes = {
    high: 'bg-red-100 text-red-700 ring-1 ring-red-200',
    medium: 'bg-orange-100 text-orange-700 ring-1 ring-orange-200',
    low: 'bg-lavender-200 dark:bg-prussian-600 text-prussian-500 dark:text-lavender-200 ring-1 ring-lavender-300 dark:ring-prussian-500',
  }
  return classes[p]
}
</script>

<style scoped>
.fade-fast-enter-active {
  transition: opacity 0.2s ease;
}
.fade-fast-leave-active {
  transition: opacity 0.15s ease;
}
.fade-fast-enter-from,
.fade-fast-leave-to {
  opacity: 0;
}
</style>
