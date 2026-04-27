<template>
  <div class="bg-surface-raised border-b border-border-subtle px-4 py-3 space-y-2.5">
    <!-- Row 1: Store selector + List actions + New List -->
    <div class="flex items-center justify-between gap-3">
      <div class="flex items-center gap-2 min-w-0">
        <!-- Store icon -->
        <svg class="w-5 h-5 flex-shrink-0 text-[#C4975A]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72l1.189-1.19A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72M6.75 18h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .414.336.75.75.75z" />
        </svg>

        <!-- Single list: show name. Multiple: dropdown -->
        <span
          v-if="lists.length === 1"
          class="text-sm font-semibold text-ink-primary truncate"
        >
          {{ activeList?.name }}
        </span>
        <KinSelect
          v-else
          :model-value="activeList?.id"
          :options="listOptions"
          size="sm"
          class="min-w-0"
          @update:model-value="(v) => $emit('select-list', v)"
        />

        <!-- List actions menu (rename / delete) -->
        <div v-if="activeList" class="relative">
          <button
            class="p-1 rounded-md text-ink-tertiary hover:text-ink-primary hover:bg-surface-sunken transition-colors"
            title="List options"
            aria-label="List options"
            @click="showListMenu = !showListMenu"
          >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 12.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 18.75a.75.75 0 110-1.5.75.75 0 010 1.5z" />
            </svg>
          </button>

          <!-- Dropdown menu -->
          <div
            v-if="showListMenu"
            class="absolute left-0 top-full mt-1 bg-surface-raised border border-border-subtle rounded-lg shadow-lg z-20 py-1 min-w-[140px]"
          >
            <button
              class="w-full text-left px-3 py-2 text-sm text-ink-primary hover:bg-surface-sunken transition-colors"
              @click="startRename"
            >
              Rename
            </button>
            <button
              v-if="lists.length > 1"
              class="w-full text-left px-3 py-2 text-sm text-status-failed hover:bg-status-failed/10 transition-colors"
              @click="handleDeleteRequest"
            >
              Delete list
            </button>
          </div>
        </div>
      </div>

      <KinButton
        variant="secondary"
        size="sm"
        class="flex-shrink-0"
        @click="$emit('new-list')"
      >
        + New List
      </KinButton>
    </div>

    <!-- Rename inline input (replaces row 1 content when active) -->
    <div v-if="isRenaming" class="flex items-center gap-2">
      <input
        ref="renameInput"
        v-model="renameName"
        type="text"
        class="input-base flex-1 text-sm py-1 px-2"
        @keydown.enter="saveRename"
        @keydown.escape="cancelRename"
      />
      <KinButton variant="primary" size="sm" @click="saveRename">Save</KinButton>
      <KinButton variant="secondary" size="sm" @click="cancelRename">Cancel</KinButton>
    </div>

    <!-- Row 2: Window pills + Pre-Shop + Clear -->
    <div class="flex items-center justify-between gap-2">
      <!-- Shopping window pills -->
      <div class="flex items-center gap-1 overflow-x-auto">
        <button
          v-for="w in windows"
          :key="w.value"
          class="px-2.5 py-1 text-xs rounded-full whitespace-nowrap transition-colors"
          :class="shoppingWindow === w.value
            ? 'bg-[#C4975A] text-white'
            : 'bg-surface-sunken text-ink-secondary hover:bg-surface-overlay'"
          @click="$emit('set-window', w.value)"
        >
          {{ w.label }}
        </button>
      </div>

      <div class="flex items-center gap-1.5 flex-shrink-0">
        <!-- Pre-Shop toggle -->
        <button
          class="px-2.5 py-1 text-xs rounded-full transition-colors"
          :class="preShopMode
            ? 'bg-status-success/10 text-status-success'
            : 'bg-surface-sunken text-ink-secondary hover:bg-surface-overlay'"
          @click="$emit('toggle-preshop')"
        >
          Pre-Shop
        </button>

        <!-- Clear Checked button -->
        <button
          v-if="checkedCount > 0"
          class="flex items-center gap-1 px-2.5 py-1 text-xs rounded-full bg-surface-sunken text-ink-secondary hover:bg-status-failed/10 hover:text-status-failed transition-colors"
          @click="$emit('clear-checked')"
        >
          <!-- Trash icon -->
          <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
          </svg>
          <span>Clear {{ checkedCount }}</span>
        </button>
      </div>
    </div>

    <!-- Delete confirmation -->
    <Teleport to="body">
      <Transition name="fade">
        <div v-if="showDeleteConfirm" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" @click.self="showDeleteConfirm = false">
          <div class="bg-surface-raised rounded-xl shadow-xl p-6 w-full max-w-sm">
            <h3 class="text-lg font-bold font-heading text-ink-primary mb-2">Delete list?</h3>
            <p class="text-sm text-ink-tertiary mb-4">
              "{{ activeList?.name }}" and all its items will be permanently removed.
            </p>
            <div class="flex justify-end gap-2">
              <KinButton variant="secondary" @click="showDeleteConfirm = false">Cancel</KinButton>
              <KinButton variant="danger" @click="confirmDelete">Delete</KinButton>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, nextTick } from 'vue'
import KinSelect from '@/components/design-system/KinSelect.vue'
import KinButton from '@/components/design-system/KinButton.vue'

const props = defineProps({
  lists: {
    type: Array,
    required: true,
  },
  activeList: {
    type: Object,
    default: null,
  },
  preShopMode: {
    type: Boolean,
    default: false,
  },
  shoppingWindow: {
    type: String,
    default: 'all',
  },
  checkedCount: {
    type: Number,
    default: 0,
  },
})

const emit = defineEmits(['select-list', 'new-list', 'toggle-preshop', 'set-window', 'clear-checked', 'rename-list', 'delete-list'])

const windows = [
  { value: 'all', label: 'All' },
  { value: '2days', label: 'Next 2d' },
  { value: '3days', label: 'Next 3d' },
  { value: 'week', label: 'This week' },
]

const listOptions = computed(() => props.lists.map(l => ({ value: l.id, label: l.name })))

// List menu
const showListMenu = ref(false)

// Rename
const isRenaming = ref(false)
const renameName = ref('')
const renameInput = ref(null)

function startRename() {
  showListMenu.value = false
  renameName.value = props.activeList?.name || ''
  isRenaming.value = true
  nextTick(() => {
    renameInput.value?.focus()
    renameInput.value?.select()
  })
}

function saveRename() {
  const trimmed = renameName.value.trim()
  if (trimmed && trimmed !== props.activeList?.name) {
    emit('rename-list', { listId: props.activeList.id, name: trimmed })
  }
  isRenaming.value = false
}

function cancelRename() {
  isRenaming.value = false
}

// Delete
const showDeleteConfirm = ref(false)

function handleDeleteRequest() {
  showListMenu.value = false
  showDeleteConfirm.value = true
}

function confirmDelete() {
  emit('delete-list', props.activeList.id)
  showDeleteConfirm.value = false
}

</script>
