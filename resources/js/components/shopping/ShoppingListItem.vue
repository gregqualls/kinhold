<template>
  <div
    class="flex items-start gap-3 px-3 py-2.5 rounded-[10px] transition-colors"
    :class="item.is_checked
      ? 'bg-surface-sunken opacity-60'
      : 'bg-surface-raised border border-border-subtle'"
  >
    <!-- Checkbox -->
    <button
      class="flex-shrink-0 mt-0.5 w-5 h-5 rounded border-2 flex items-center justify-center transition-colors"
      :class="item.is_checked
        ? 'bg-surface-overlay border-border-subtle'
        : 'border-border-subtle hover:border-[#C4975A]'"
      :aria-label="item.is_checked ? 'Uncheck item' : 'Check item'"
      @click="item.is_checked ? $emit('uncheck', item.id) : $emit('check', item.id)"
    >
      <svg v-if="item.is_checked" class="w-3 h-3 text-white" fill="none" viewBox="0 0 12 12">
        <path d="M2 6l3 3 5-5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
      </svg>
    </button>

    <!-- Content -->
    <div class="flex-1 min-w-0">
      <!-- Line 1: name + quantity + source icons + date badge -->
      <div class="flex items-center gap-2 flex-wrap">
        <span
          class="text-sm font-medium cursor-default"
          :class="item.is_checked
            ? 'line-through text-ink-tertiary'
            : 'text-ink-primary'"
          @dblclick="startEditing"
        >
          <span
            v-if="!editing"
          >{{ item.name }}</span>
          <input
            v-else
            ref="editRef"
            v-model="editName"
            class="input-base py-0 px-1 text-sm w-full"
            @blur="saveEdit"
            @keydown.enter.prevent="saveEdit"
            @keydown.escape="cancelEdit"
          />
        </span>

        <span v-if="item.quantity" class="text-xs text-ink-tertiary">{{ item.quantity }}</span>

        <!-- Source icons (inline SVG — no lucide dependency) -->
        <!-- Link icon for recipe source -->
        <svg v-if="item.source === 'recipe'" class="w-3.5 h-3.5 text-ink-tertiary flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
        </svg>
        <!-- Pin icon for staple source -->
        <svg v-else-if="item.source === 'staple'" class="w-3.5 h-3.5 text-ink-tertiary flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
          <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
        </svg>

        <!-- Needed date badge -->
        <span
          v-if="item.needed_date && daysUntil(item.needed_date) !== null"
          class="text-[10px] font-medium px-1.5 py-0.5 rounded-full"
          :class="daysUntil(item.needed_date) <= 2
            ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400'
            : 'bg-surface-sunken text-ink-secondary'"
        >
          {{ formatDays(daysUntil(item.needed_date)) }}
        </span>
      </div>

      <!-- Line 2: recipe source -->
      <p
        v-if="item.source === 'recipe' && item.source_recipe_name"
        class="text-xs text-ink-tertiary mt-0.5"
      >
        for {{ item.source_recipe_name }}
      </p>
    </div>

    <!-- Recurring toggle -->
    <button
      v-if="canManage"
      class="flex-shrink-0 flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium transition-all"
      :class="item.is_recurring
        ? 'bg-[#C4975A]/15 text-[#C4975A] border border-[#C4975A]/30'
        : 'text-ink-tertiary hover:bg-surface-sunken hover:text-ink-secondary'"
      :title="item.is_recurring
        ? 'This item reappears automatically after you clear bought items. Click to turn off.'
        : 'Make recurring — this item will reappear every time you clear bought items (great for staples like milk, bread, eggs)'"
      @click="emit('toggle-recurring', item.id)"
    >
      <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182" />
      </svg>
      <span v-if="item.is_recurring">Recurring</span>
    </button>

    <!-- Move to another list -->
    <div v-if="canManage && otherLists.length > 0" class="relative flex-shrink-0">
      <button
        class="p-1 rounded-full text-ink-tertiary hover:text-ink-secondary transition-colors"
        title="Move to another list"
        aria-label="Move to another list"
        @click="showMoveMenu = !showMoveMenu"
        @blur="setTimeout(() => showMoveMenu = false, 150)"
      >
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
        </svg>
      </button>
      <div
        v-if="showMoveMenu"
        class="absolute right-0 mt-1 bg-surface-raised border border-border-subtle rounded-lg shadow-lg z-10 py-1 min-w-[140px]"
      >
        <button
          v-for="list in otherLists"
          :key="list.id"
          class="w-full px-3 py-1.5 text-sm text-left text-ink-primary hover:bg-surface-sunken cursor-pointer transition-colors"
          @mousedown.prevent="emit('move-item', { itemId: item.id, targetListId: list.id }); showMoveMenu = false"
        >
          {{ list.name }}
        </button>
      </div>
    </div>

    <!-- Remove button -->
    <button
      class="flex-shrink-0 p-1 rounded-full text-ink-tertiary hover:text-status-failed hover:bg-status-failed/10 transition-colors opacity-0 group-hover:opacity-100 md:opacity-100"
      aria-label="Remove item"
      @click="$emit('remove', item.id)"
    >
      <svg class="w-4 h-4" fill="none" viewBox="0 0 16 16">
        <path d="M4 4l8 8M12 4l-8 8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
      </svg>
    </button>
  </div>
</template>

<script setup>
import { ref, nextTick } from 'vue'

const props = defineProps({
  item: {
    type: Object,
    required: true,
  },
  mode: {
    type: String,
    default: 'shop', // 'shop' | 'preshop'
  },
  otherLists: {
    type: Array,
    default: () => [],
  },
  canManage: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['check', 'uncheck', 'remove', 'update', 'toggle-recurring', 'move-item'])

const showMoveMenu = ref(false)

const editing = ref(false)
const editName = ref('')
const editRef = ref(null)

const startEditing = () => {
  if (props.item.is_checked) return
  editName.value = props.item.name
  editing.value = true
  nextTick(() => editRef.value?.focus())
}

const saveEdit = () => {
  if (!editing.value) return
  const name = editName.value.trim()
  if (name && name !== props.item.name) {
    emit('update', props.item.id, { name })
  }
  editing.value = false
}

const cancelEdit = () => {
  editing.value = false
}

const daysUntil = (dateStr) => {
  if (!dateStr) return null
  const target = new Date(dateStr)
  const today = new Date()
  today.setHours(0, 0, 0, 0)
  target.setHours(0, 0, 0, 0)
  return Math.round((target - today) / (1000 * 60 * 60 * 24))
}

const formatDays = (days) => {
  if (days === null) return ''
  if (days <= 0) return '0d'
  return `${days}d`
}
</script>
