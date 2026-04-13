<template>
  <div class="relative">
    <div class="flex items-center gap-2">
      <div class="flex-1 relative">
        <input
          ref="inputRef"
          v-model="itemName"
          type="text"
          placeholder="Add item..."
          class="input-base w-full text-sm"
          autocomplete="off"
          @input="onInput"
          @keydown.enter.prevent="submitItem"
          @keydown.escape="clearSuggestions"
          @focus="showDropdown = itemName.length > 0 && catalogResults.length > 0"
        />

        <!-- Autocomplete dropdown -->
        <div
          v-if="showDropdown && catalogResults.length > 0"
          class="absolute top-full left-0 right-0 mt-1 bg-white dark:bg-prussian-800 border border-lavender-200 dark:border-prussian-700 rounded-[10px] shadow-lg z-20 py-1 max-h-48 overflow-y-auto"
        >
          <button
            v-for="result in catalogResults"
            :key="result.name"
            class="w-full px-3 py-2 text-left hover:bg-lavender-50 dark:hover:bg-prussian-700 transition-colors flex items-center justify-between gap-2"
            @mousedown.prevent="selectSuggestion(result)"
          >
            <span class="text-sm text-prussian-500 dark:text-lavender-200">{{ result.name }}</span>
            <span v-if="result.category" class="text-xs text-lavender-400 dark:text-lavender-500 flex-shrink-0">{{ result.category }}</span>
          </button>
        </div>
      </div>

      <!-- Quantity input (shown when name is typed) -->
      <input
        v-if="itemName.length > 0"
        v-model="itemQty"
        type="text"
        placeholder="Qty"
        class="input-base w-20 text-sm flex-shrink-0"
        @keydown.enter.prevent="submitItem"
      />

      <!-- Recurring toggle -->
      <button
        v-if="itemName.length > 0"
        type="button"
        class="flex-shrink-0 flex items-center gap-1 px-2 py-1.5 rounded-lg text-xs font-medium transition-all"
        :class="isRecurring
          ? 'bg-[#C4975A]/15 text-[#C4975A] border border-[#C4975A]/30'
          : 'text-lavender-400 dark:text-lavender-500 hover:bg-lavender-100 dark:hover:bg-prussian-700 border border-transparent'"
        :title="isRecurring
          ? 'Recurring is ON — this item will reappear every time you clear bought items'
          : 'Make recurring — great for staples like milk, bread, eggs that you always need'"
        @click="isRecurring = !isRecurring"
      >
        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182" />
        </svg>
        <span>{{ isRecurring ? 'Recurring' : 'Repeat' }}</span>
      </button>

      <button
        class="flex-shrink-0 px-4 py-2.5 text-sm font-medium text-white bg-[#C4975A] hover:bg-[#D4A96A] rounded-[10px] transition-colors disabled:opacity-50"
        :disabled="!itemName.trim()"
        @click="submitItem"
      >
        Add
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({
  listId: {
    type: String,
    required: true,
  },
  catalogResults: {
    type: Array,
    default: () => [],
  },
})

const emit = defineEmits(['item-added', 'search'])

const inputRef = ref(null)
const itemName = ref('')
const itemQty = ref('')
const itemCategory = ref(null)
const isRecurring = ref(false)
const showDropdown = ref(false)
let searchDebounce = null

const onInput = () => {
  itemCategory.value = null
  showDropdown.value = true
  clearTimeout(searchDebounce)
  searchDebounce = setTimeout(() => {
    emit('search', itemName.value)
  }, 300)
}

const selectSuggestion = (result) => {
  itemName.value = result.name
  itemCategory.value = result.category || null
  showDropdown.value = false
}

const clearSuggestions = () => {
  showDropdown.value = false
}

const submitItem = () => {
  const name = itemName.value.trim()
  if (!name) return

  const data = { name }
  if (itemCategory.value) data.category = itemCategory.value
  if (itemQty.value.trim()) data.quantity = itemQty.value.trim()
  if (isRecurring.value) data.is_recurring = true

  emit('item-added', data)

  clearTimeout(searchDebounce)
  itemName.value = ''
  itemQty.value = ''
  itemCategory.value = null
  isRecurring.value = false
  showDropdown.value = false

  inputRef.value?.focus()
}
</script>
