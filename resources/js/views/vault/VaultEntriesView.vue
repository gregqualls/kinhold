<template>
  <div class="h-full flex flex-col">
    <!-- Header -->
    <div class="px-4 pt-4 pb-2 md:px-6 md:pt-6">
      <div class="flex items-center gap-3">
        <button
          class="p-2 -ml-2 text-lavender-400 hover:text-prussian-500 dark:hover:text-lavender-200 hover:bg-lavender-100 dark:hover:bg-prussian-700 rounded-xl transition-colors"
          @click="$router.push('/vault')"
        >
          <ChevronLeftIcon class="w-5 h-5" />
        </button>
        <div class="flex-1 min-w-0">
          <h1 class="text-xl font-bold font-heading text-prussian-500 dark:text-lavender-200 truncate">{{ currentCategory?.name || 'Entries' }}</h1>
          <p v-if="currentCategory?.description" class="text-xs text-lavender-500 dark:text-lavender-400 mt-0.5 truncate">{{ currentCategory.description }}</p>
        </div>
        <button
          v-if="isParent"
          class="hidden md:flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-wisteria-600 hover:bg-wisteria-500 rounded-xl transition-colors"
          @click="showCreateEntry = true"
        >
          <PlusIcon class="w-4 h-4" />
          Add
        </button>
      </div>
    </div>

    <!-- Search -->
    <div class="px-4 md:px-6 py-3">
      <div class="relative">
        <MagnifyingGlassIcon class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-lavender-400" />
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search entries..."
          class="w-full pl-9 pr-4 py-2.5 bg-lavender-50 dark:bg-prussian-700 border border-lavender-200 dark:border-prussian-600 rounded-xl text-sm placeholder-lavender-400 dark:placeholder-lavender-500 text-prussian-500 dark:text-lavender-200 focus:bg-white dark:focus:bg-prussian-800 focus:ring-2 focus:ring-wisteria-400 transition-all outline-none"
        />
      </div>
    </div>

    <!-- Loading -->
    <div v-if="isLoading" class="flex items-center justify-center py-16">
      <LoadingSpinner size="lg" />
    </div>

    <!-- Entries List -->
    <div v-else class="flex-1 overflow-y-auto px-4 md:px-6 pb-32 md:pb-6">
      <div v-if="filteredEntries.length > 0" class="space-y-2">
        <div
          v-for="entry in filteredEntries"
          :key="entry.id"
          class="group flex items-center gap-4 p-4 bg-white dark:bg-prussian-800 rounded-xl shadow-card hover:shadow-card-lg border border-lavender-200 dark:border-prussian-700 hover:border-wisteria-300 dark:hover:border-wisteria-700 cursor-pointer transition-all"
          @click="$router.push(`/vault/entry/${entry.id}`)"
        >
          <!-- Lock icon -->
          <div class="w-10 h-10 rounded-xl bg-lavender-50 dark:bg-prussian-700 flex items-center justify-center flex-shrink-0">
            <LockClosedIcon class="w-5 h-5 text-lavender-400 dark:text-lavender-500" />
          </div>

          <!-- Info -->
          <div class="flex-1 min-w-0">
            <h3 class="font-semibold text-prussian-500 dark:text-lavender-200 text-sm truncate">{{ entry.title }}</h3>
            <p class="text-xs text-lavender-400 dark:text-lavender-500 mt-0.5">
              {{ formatDate(entry.updated_at) }}
              <span v-if="entry.notes" class="ml-1">&middot; Has notes</span>
            </p>
          </div>

          <!-- Context menu -->
          <div class="opacity-0 group-hover:opacity-100 transition-opacity" @click.stop>
            <ContextMenu :items="getEntryMenuItems(entry)" />
          </div>

          <ChevronRightIcon class="w-4 h-4 text-lavender-300 dark:text-lavender-500 flex-shrink-0 md:hidden" />
        </div>
      </div>

      <!-- Empty State -->
      <EmptyState
        v-if="filteredEntries.length === 0 && !isLoading"
        :icon="LockClosedIcon"
        title="No entries yet"
        description="Add your first entry to this category."
        action-text="Add Entry"
        @action="showCreateEntry = true"
      />
    </div>

    <!-- Mobile FAB -->
    <FloatingActionButton v-if="isParent" @click="showCreateEntry = true" />

    <!-- Delete Confirmation -->
    <ConfirmDialog
      :show="!!deletingEntry"
      title="Delete Entry?"
      :message="`&quot;${deletingEntry?.title}&quot; will be permanently deleted.`"
      confirm-text="Delete"
      @confirm="handleDeleteEntry"
      @cancel="deletingEntry = null"
    />

    <!-- Create Entry Modal -->
    <BaseModal
      :show="showCreateEntry"
      title="New Vault Entry"
      size="xl"
      @close="closeCreateModal"
    >
      <form class="space-y-5" @submit.prevent="handleCreateEntry">
        <div>
          <label class="block text-sm font-medium text-prussian-500 dark:text-lavender-200 mb-1.5">Title</label>
          <input
            v-model="entryForm.title"
            placeholder="e.g., Family Doctor, WiFi Info"
            class="input-base"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-prussian-500 dark:text-lavender-200 mb-1.5">Content</label>
          <MarkdownEditor
            v-model="entryForm.body"
            placeholder="Start typing... Use **bold**, *italic*, lists, and more."
          />
        </div>

        <!-- Sensitive Fields (collapsible) -->
        <div>
          <button
            type="button"
            class="flex items-center gap-1.5 text-xs font-medium text-lavender-500 dark:text-lavender-400 hover:text-wisteria-600 dark:hover:text-wisteria-400 transition-colors"
            @click="showSensitiveFields = !showSensitiveFields"
          >
            <LockClosedIcon class="w-3.5 h-3.5" />
            {{ showSensitiveFields ? 'Hide' : 'Add' }} sensitive fields (passwords, SSNs, etc.)
            <ChevronRightIcon class="w-3 h-3 transition-transform" :class="{ 'rotate-90': showSensitiveFields }" />
          </button>

          <div v-if="showSensitiveFields" class="mt-3 space-y-3">
            <div v-for="(field, i) in entryForm.sensitiveFields" :key="i" class="flex gap-2">
              <input
                v-model="field.key"
                placeholder="Label (e.g., Password)"
                class="input-base flex-1"
              />
              <input
                v-model="field.value"
                placeholder="Value"
                type="password"
                class="input-base flex-1"
              />
              <button
                type="button"
                class="p-2 text-lavender-400 hover:text-red-500 transition-colors"
                @click="entryForm.sensitiveFields.splice(i, 1)"
              >
                <XMarkIcon class="w-4 h-4" />
              </button>
            </div>
            <button
              type="button"
              class="flex items-center gap-1.5 text-xs font-medium text-wisteria-600 dark:text-wisteria-400 hover:text-wisteria-500"
              @click="entryForm.sensitiveFields.push({ key: '', value: '' })"
            >
              <PlusIcon class="w-3.5 h-3.5" />
              Add Field
            </button>
          </div>
        </div>

        <div class="flex gap-2 pt-2">
          <button type="button" class="flex-1 btn-secondary btn-md rounded-xl" @click="closeCreateModal">
            Cancel
          </button>
          <button type="submit" :disabled="!entryForm.title?.trim() || savingEntry" class="flex-1 btn-primary btn-md rounded-xl disabled:opacity-40">
            {{ savingEntry ? 'Saving...' : 'Create Entry' }}
          </button>
        </div>
      </form>
    </BaseModal>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { storeToRefs } from 'pinia'
import { useVaultStore } from '@/stores/vault'
import { useAuthStore } from '@/stores/auth'
import { useNotification } from '@/composables/useNotification'
import EmptyState from '@/components/common/EmptyState.vue'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import ContextMenu from '@/components/common/ContextMenu.vue'
import ConfirmDialog from '@/components/common/ConfirmDialog.vue'
import BaseModal from '@/components/common/BaseModal.vue'
import FloatingActionButton from '@/components/common/FloatingActionButton.vue'
import MarkdownEditor from '@/components/vault/MarkdownEditor.vue'
import {
  ChevronLeftIcon,
  ChevronRightIcon,
  LockClosedIcon,
  PlusIcon,
  MagnifyingGlassIcon,
  TrashIcon,
  XMarkIcon,
} from '@heroicons/vue/24/outline'

const route = useRoute()
const router = useRouter()
const vaultStore = useVaultStore()
const authStore = useAuthStore()
const { success, error: notifyError } = useNotification()

const { entries, currentCategory, isLoading, categories } = storeToRefs(vaultStore)
const { isParent } = storeToRefs(authStore)

const categorySlug = route.params.categorySlug
const searchQuery = ref('')
const showCreateEntry = ref(false)
const showSensitiveFields = ref(false)
const savingEntry = ref(false)
const deletingEntry = ref(null)

const entryForm = ref({
  title: '',
  body: '',
  sensitiveFields: [{ key: '', value: '' }],
})

const filteredEntries = computed(() =>
  (entries.value || []).filter((entry) => {
    const matchesSearch = entry.title?.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchesCategory = currentCategory.value
      ? (entry.vault_category_id === currentCategory.value.id || entry.category_id === currentCategory.value.id)
      : true
    return matchesSearch && matchesCategory
  })
)

const formatDate = (dateStr) => {
  if (!dateStr) return ''
  const d = new Date(dateStr)
  const now = new Date()
  const diff = now - d
  if (diff < 60000) return 'just now'
  if (diff < 3600000) return `${Math.floor(diff / 60000)}m ago`
  if (diff < 86400000) return `${Math.floor(diff / 3600000)}h ago`
  return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })
}

const getEntryMenuItems = (entry) => [
  { label: 'View', icon: LockClosedIcon, action: () => router.push(`/vault/entry/${entry.id}`) },
  { divider: true },
  { label: 'Delete', icon: TrashIcon, variant: 'danger', action: () => { deletingEntry.value = entry } },
]

const closeCreateModal = () => {
  showCreateEntry.value = false
  showSensitiveFields.value = false
  entryForm.value = { title: '', body: '', sensitiveFields: [{ key: '', value: '' }] }
}

const handleCreateEntry = async () => {
  if (!entryForm.value.title?.trim() || !currentCategory.value) return
  savingEntry.value = true

  const sensitiveFields = {}
  entryForm.value.sensitiveFields.forEach((f) => {
    if (f.key?.trim() && f.value?.trim()) {
      sensitiveFields[f.key.trim()] = f.value.trim()
    }
  })

  const payload = {
    vault_category_id: currentCategory.value.id,
    title: entryForm.value.title,
    data: {
      body: entryForm.value.body || '',
      sensitive_fields: Object.keys(sensitiveFields).length > 0 ? sensitiveFields : undefined,
    },
  }

  const result = await vaultStore.createEntry(payload)
  if (result.success) {
    success('Entry created!')
    closeCreateModal()
  } else {
    notifyError(result.error || 'Failed to create entry')
  }
  savingEntry.value = false
}

const handleDeleteEntry = async () => {
  if (!deletingEntry.value) return
  const result = await vaultStore.deleteEntry(deletingEntry.value.id)
  if (result.success) {
    success('Entry deleted!')
  } else {
    notifyError(result.error || 'Failed to delete')
  }
  deletingEntry.value = null
}

onMounted(async () => {
  if (categories.value.length === 0) {
    await vaultStore.fetchCategories()
  }
  const category = categories.value.find((c) => c.slug === categorySlug)
  if (category) {
    vaultStore.currentCategory = category
  }
  await vaultStore.fetchEntries()
})
</script>
