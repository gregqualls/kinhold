<template>
  <KinModalSheet
    :model-value="!!task"
    title="Task Details"
    size="md"
    @update:model-value="(v) => !v && $emit('close')"
  >
    <div v-if="task" class="space-y-5">
      <!-- Title -->
      <KinInput
        v-model="form.title"
        label="Title"
        placeholder="Task title"
      />

      <!-- Description -->
      <KinTextarea
        v-model="form.description"
        label="Description"
        :rows="3"
        placeholder="Add details..."
      />

      <!-- Tags -->
      <div>
        <label class="block text-xs font-medium text-ink-tertiary uppercase tracking-wider mb-2">Tags</label>
        <div class="flex flex-wrap gap-2">
          <KinChip
            v-for="tag in tags"
            :key="tag.id"
            variant="filter"
            size="sm"
            :custom-color="getTagHex(tag.color)"
            :active="form.tag_ids.includes(tag.id)"
            @click="toggleTag(tag.id)"
          >
            {{ tag.name }}
          </KinChip>
        </div>
      </div>

      <!-- Priority -->
      <div>
        <label class="block text-xs font-medium text-ink-tertiary uppercase tracking-wider mb-2">Priority</label>
        <KinSegmentedFilter
          :options="priorityOptions"
          :active-key="form.priority"
          @update:active-key="form.priority = $event"
        />
      </div>

      <!-- Due Date -->
      <KinInput
        v-model="form.due_date"
        label="Due Date"
        type="date"
      />

      <!-- Family Task (open to anyone) -->
      <div class="py-3 px-4 bg-surface-sunken rounded-xl">
        <KinSwitch
          v-model="form.is_family_task"
          label="Open to Anyone"
          description="Any family member can claim and complete this task"
          color="lavender"
        />
      </div>

      <!-- Assignee (hidden when open to anyone) -->
      <KinSelect
        v-if="!form.is_family_task"
        v-model="form.assigned_to"
        label="Assigned To"
        :options="assigneeOptions"
      />

      <!-- Points (only for parents — children can't set point values) -->
      <div v-if="enabledModules.points && isParent">
        <label class="block text-xs font-medium text-ink-tertiary uppercase tracking-wider mb-1.5">Points</label>
        <div class="flex items-center gap-3">
          <KinInput
            v-model.number="form.points"
            type="number"
            min="0"
            placeholder="Auto (based on priority)"
          />
          <span class="text-xs text-ink-tertiary whitespace-nowrap">
            Earns: {{ form.points || defaultPoints[form.priority] || 10 }} pts
          </span>
        </div>
      </div>

      <!-- Recurring Task -->
      <div>
        <KinSelect
          v-model="form.recurrence_preset"
          label="Repeat"
          :options="recurrencePresetOptions"
        />

        <!-- Custom RRULE input -->
        <div v-if="form.recurrence_preset === 'custom'" class="mt-2">
          <KinInput
            v-model="form.recurrence_rule"
            type="text"
            placeholder="e.g. FREQ=WEEKLY;BYDAY=TU,TH"
          />
        </div>

        <!-- Recurrence end date -->
        <div v-if="form.recurrence_preset" class="mt-2">
          <KinInput
            v-model="form.recurrence_end"
            type="date"
            label="Repeat until (optional)"
          />
        </div>
      </div>

      <!-- Status -->
      <div class="py-3 px-4 bg-surface-sunken rounded-xl">
        <KinSwitch
          v-model="form.completed"
          label="Completed"
          color="mint"
        />
      </div>
    </div>

    <template #actions>
      <div class="flex gap-3 items-center">
        <KinButton variant="danger" size="md" @click="$emit('delete', task.id)">
          Delete
        </KinButton>
        <div class="flex-1"></div>

        <!-- Unsaved changes indicator -->
        <Transition name="fade-fast" mode="out-in">
          <span v-if="isDirty && !justSaved" key="dirty" class="text-xs text-ink-tertiary font-medium">
            Unsaved changes
          </span>
          <span v-else-if="justSaved" key="saved" class="text-xs text-status-success font-medium flex items-center gap-1">
            <CheckCircleIcon class="w-4 h-4" />
            Saved!
          </span>
        </Transition>

        <KinButton variant="ghost" size="md" @click="$emit('close')">
          Cancel
        </KinButton>
        <KinButton
          variant="primary"
          size="md"
          :disabled="saving || !form.title?.trim()"
          @click="save"
        >
          {{ saving ? 'Saving...' : 'Save' }}
        </KinButton>
      </div>
    </template>
  </KinModalSheet>
</template>

<script setup>
import { reactive, watch, ref, computed } from 'vue'
import { storeToRefs } from 'pinia'
import { useAuthStore } from '@/stores/auth'
import { CheckCircleIcon } from '@heroicons/vue/24/outline'
import KinModalSheet from '@/components/design-system/KinModalSheet.vue'
import KinButton from '@/components/design-system/KinButton.vue'
import KinInput from '@/components/design-system/KinInput.vue'
import KinTextarea from '@/components/design-system/KinTextarea.vue'
import KinSwitch from '@/components/design-system/KinSwitch.vue'
import KinSegmentedFilter from '@/components/design-system/KinSegmentedFilter.vue'
import KinSelect from '@/components/design-system/KinSelect.vue'
import KinChip from '@/components/design-system/KinChip.vue'

const props = defineProps({
  task: Object,
  saving: Boolean,
  tags: { type: Array, default: () => [] },
})

const emit = defineEmits(['save', 'close', 'delete'])

const authStore = useAuthStore()
const { familyMembers, enabledModules, isParent, canAssignTasks, currentUser } = storeToRefs(authStore)

const justSaved = ref(false)
let savedTimer = null

const defaultPoints = { low: 5, medium: 10, high: 20 }

const priorityOptions = [
  { key: 'low', label: 'Low' },
  { key: 'medium', label: 'Medium' },
  { key: 'high', label: 'High' },
]

// If user can't assign tasks to others, only show themselves as an option
const assignableMembers = computed(() => {
  if (canAssignTasks.value) return familyMembers.value
  return familyMembers.value.filter((m) => m.id === currentUser.value?.id)
})

const assigneeOptions = computed(() => [
  { value: null, label: 'Unassigned' },
  ...assignableMembers.value.map((m) => ({ value: m.id, label: m.name })),
])

const recurrencePresetOptions = [
  { value: '', label: 'Does not repeat' },
  { value: 'FREQ=DAILY', label: 'Every day' },
  { value: 'FREQ=WEEKLY;BYDAY=MO', label: 'Every Monday' },
  { value: 'FREQ=WEEKLY;BYDAY=TU', label: 'Every Tuesday' },
  { value: 'FREQ=WEEKLY;BYDAY=WE', label: 'Every Wednesday' },
  { value: 'FREQ=WEEKLY;BYDAY=TH', label: 'Every Thursday' },
  { value: 'FREQ=WEEKLY;BYDAY=FR', label: 'Every Friday' },
  { value: 'FREQ=WEEKLY;BYDAY=SA', label: 'Every Saturday' },
  { value: 'FREQ=WEEKLY;BYDAY=SU', label: 'Every Sunday' },
  { value: 'FREQ=MONTHLY', label: 'Every month' },
  { value: 'custom', label: 'Custom RRULE...' },
]

const colorMap = {
  wisteria: '#7d57a8',
  prussian: '#05204A',
  sand: '#a5a84e',
  red: '#dc2626',
  green: '#059669',
  pink: '#db2777',
}

const getTagHex = (colorName) => colorMap[colorName] || colorName || colorMap.wisteria

// Map an RRULE string to a preset value, or 'custom' if not matching
const recurrenceToPreset = (rule) => {
  if (!rule) return ''
  const presets = [
    'FREQ=DAILY',
    'FREQ=WEEKLY;BYDAY=MO',
    'FREQ=WEEKLY;BYDAY=TU',
    'FREQ=WEEKLY;BYDAY=WE',
    'FREQ=WEEKLY;BYDAY=TH',
    'FREQ=WEEKLY;BYDAY=FR',
    'FREQ=WEEKLY;BYDAY=SA',
    'FREQ=WEEKLY;BYDAY=SU',
    'FREQ=MONTHLY',
  ]
  return presets.includes(rule) ? rule : 'custom'
}

const form = reactive({
  title: '',
  description: '',
  priority: 'medium',
  due_date: '',
  assigned_to: null,
  is_family_task: false,
  points: null,
  recurrence_preset: '',
  recurrence_rule: '',
  recurrence_end: '',
  completed: false,
  tag_ids: [],
})

const originalValues = ref({})

const isDirty = computed(() => {
  if (!props.task) return false
  const o = originalValues.value
  return (
    form.title !== o.title ||
    form.description !== o.description ||
    form.priority !== o.priority ||
    form.due_date !== o.due_date ||
    form.assigned_to !== o.assigned_to ||
    form.is_family_task !== o.is_family_task ||
    form.points !== o.points ||
    form.recurrence_preset !== o.recurrence_preset ||
    form.recurrence_rule !== o.recurrence_rule ||
    form.recurrence_end !== o.recurrence_end ||
    form.completed !== o.completed ||
    JSON.stringify([...form.tag_ids].sort()) !== JSON.stringify([...(o.tag_ids || [])].sort())
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
    form.is_family_task = !!t.is_family_task
    form.points = t.points ?? null
    form.recurrence_preset = recurrenceToPreset(t.recurrence_rule)
    form.recurrence_rule = t.recurrence_rule || ''
    form.recurrence_end = t.recurrence_end || ''
    form.completed = !!t.completed_at
    form.tag_ids = (t.tags || []).map((tag) => tag.id)
    originalValues.value = {
      title: form.title,
      description: form.description,
      priority: form.priority,
      due_date: form.due_date,
      assigned_to: form.assigned_to,
      is_family_task: form.is_family_task,
      points: form.points,
      recurrence_preset: form.recurrence_preset,
      recurrence_rule: form.recurrence_rule,
      recurrence_end: form.recurrence_end,
      completed: form.completed,
      tag_ids: [...form.tag_ids],
    }
  }
}, { immediate: true })

const save = () => {
  // Compute the actual recurrence_rule from preset or custom input
  const recurrence_rule = form.recurrence_preset === 'custom'
    ? form.recurrence_rule
    : form.recurrence_preset || null

  const payload = {
    title: form.title,
    description: form.description,
    priority: form.priority,
    due_date: form.due_date || null,
    assigned_to: form.is_family_task ? null : (form.assigned_to || null),
    is_family_task: form.is_family_task,
    points: form.points || null,
    recurrence_rule,
    recurrence_end: form.recurrence_end || null,
    completed: form.completed,
    tag_ids: form.tag_ids,
  }

  emit('save', payload)
  originalValues.value = { ...form, tag_ids: [...form.tag_ids] }
  justSaved.value = true
  clearTimeout(savedTimer)
  savedTimer = setTimeout(() => {
    justSaved.value = false
  }, 2000)
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
