<template>
  <div>
    <label class="block text-sm font-semibold text-prussian-500 dark:text-lavender-200 mb-2">{{ label }}</label>

    <div class="flex flex-wrap gap-2">
      <button
        v-for="tag in tags"
        :key="tag.id"
        type="button"
        class="px-3 py-1.5 text-xs font-medium rounded-full transition-colors"
        :class="isSelected(tag.id)
          ? 'text-white'
          : 'bg-lavender-100 dark:bg-prussian-700 text-lavender-600 dark:text-lavender-400 hover:bg-lavender-200 dark:hover:bg-prussian-600'"
        :style="isSelected(tag.id) ? { backgroundColor: tag.color || '#C4975A' } : {}"
        @click="toggle(tag.id)"
      >
        {{ tag.name }}
      </button>

      <!-- Inline create -->
      <div v-if="isCreating" class="flex items-center gap-1.5 bg-lavender-50 dark:bg-prussian-700 rounded-full pl-3 pr-1 py-1">
        <input
          ref="createInput"
          v-model="newTagName"
          type="text"
          class="bg-transparent text-xs text-prussian-500 dark:text-lavender-200 placeholder-lavender-400 focus:outline-none w-28"
          placeholder="New tag name"
          @keydown.enter.prevent="confirmCreate"
          @keydown.escape="cancelCreate"
        />
        <button
          type="button"
          class="p-1 rounded-full text-[#5B8C6A] hover:bg-[#5B8C6A]/10 disabled:opacity-40 disabled:cursor-not-allowed"
          :disabled="!newTagName.trim() || isSubmitting"
          aria-label="Add tag"
          @click="confirmCreate"
        >
          <CheckIcon class="w-3.5 h-3.5" />
        </button>
        <button
          type="button"
          class="p-1 rounded-full text-lavender-500 hover:bg-lavender-200 dark:hover:bg-prussian-600"
          aria-label="Cancel"
          @click="cancelCreate"
        >
          <XMarkIcon class="w-3.5 h-3.5" />
        </button>
      </div>

      <button
        v-else-if="onCreate"
        type="button"
        class="flex items-center gap-1 px-3 py-1.5 text-xs font-medium rounded-full border border-dashed border-lavender-300 dark:border-prussian-600 text-lavender-500 dark:text-lavender-400 hover:border-[#C4975A] hover:text-[#C4975A] transition-colors"
        @click="startCreate"
      >
        <PlusIcon class="w-3 h-3" />
        Add tag
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, nextTick } from 'vue'
import { PlusIcon, CheckIcon, XMarkIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  modelValue: { type: Array, default: () => [] },
  tags: { type: Array, default: () => [] },
  label: { type: String, default: 'Tags' },
  // Async function: ({ name, color }) => { success: bool, tag: { id, name, ... } }
  onCreate: { type: Function, default: null },
})

const emit = defineEmits(['update:modelValue'])

const isCreating = ref(false)
const newTagName = ref('')
const isSubmitting = ref(false)
const createInput = ref(null)

const isSelected = (id) => props.modelValue.includes(id)

const toggle = (id) => {
  const next = [...props.modelValue]
  const idx = next.indexOf(id)
  if (idx === -1) next.push(id)
  else next.splice(idx, 1)
  emit('update:modelValue', next)
}

const startCreate = async () => {
  isCreating.value = true
  newTagName.value = ''
  await nextTick()
  createInput.value?.focus()
}

const cancelCreate = () => {
  isCreating.value = false
  newTagName.value = ''
}

const confirmCreate = async () => {
  const name = newTagName.value.trim()
  if (!name || isSubmitting.value || !props.onCreate) return

  isSubmitting.value = true
  try {
    const result = await props.onCreate({ name, color: '#C4975A' })
    if (result?.success && result.tag?.id && !props.modelValue.includes(result.tag.id)) {
      emit('update:modelValue', [...props.modelValue, result.tag.id])
    }
  } finally {
    isSubmitting.value = false
    isCreating.value = false
    newTagName.value = ''
  }
}
</script>
