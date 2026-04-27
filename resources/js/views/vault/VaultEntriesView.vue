<template>
  <div class="h-full flex flex-col">
    <!-- Header -->
    <div class="px-4 pt-4 pb-2 md:px-6 md:pt-6">
      <div class="flex items-center gap-3">
        <button
          class="p-2 -ml-2 text-ink-tertiary hover:text-ink-primary hover:bg-surface-sunken rounded-xl transition-colors"
          @click="$router.push('/vault')"
        >
          <ChevronLeftIcon class="w-5 h-5" />
        </button>
        <div class="flex-1 min-w-0">
          <h1 class="text-xl font-bold font-heading text-ink-primary truncate">{{ currentCategory?.name || 'Entries' }}</h1>
          <p v-if="currentCategory?.description" class="text-xs text-ink-tertiary mt-0.5 truncate">{{ currentCategory.description }}</p>
        </div>
        <KinButton
          v-if="isParent"
          variant="primary"
          size="md"
          class="hidden md:flex"
          @click="showCreateEntry = true"
        >
          <PlusIcon class="w-4 h-4" />
          Add
        </KinButton>
      </div>
    </div>

    <!-- Search -->
    <div class="px-4 md:px-6 py-3">
      <KinSearch v-model="searchQuery" placeholder="Search entries..." />
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
          class="group flex items-center gap-4 p-4 bg-surface-raised rounded-xl shadow-card hover:shadow-card-lg border border-border-subtle hover:border-accent-lavender-bold cursor-pointer transition-all"
          @click="$router.push(`/vault/entry/${entry.id}`)"
        >
          <!-- Lock icon -->
          <div class="w-10 h-10 rounded-xl bg-surface-sunken flex items-center justify-center flex-shrink-0">
            <LockClosedIcon class="w-5 h-5 text-ink-tertiary" />
          </div>

          <!-- Info -->
          <div class="flex-1 min-w-0">
            <h3 class="font-semibold text-ink-primary text-sm truncate">{{ entry.title }}</h3>
            <p class="text-xs text-ink-tertiary mt-0.5">
              {{ formatDate(entry.updated_at) }}
              <span v-if="entry.notes" class="ml-1">&middot; Has notes</span>
            </p>
          </div>

          <!-- Context menu -->
          <div class="opacity-0 group-hover:opacity-100 transition-opacity" @click.stop>
            <ContextMenu :items="getEntryMenuItems(entry)" />
          </div>

          <ChevronRightIcon class="w-4 h-4 text-ink-tertiary flex-shrink-0 md:hidden" />
        </div>
      </div>

      <!-- Empty State -->
      <KinEmptyState
        v-if="filteredEntries.length === 0 && !isLoading"
        :icon="LockClosedIcon"
        title="No entries yet"
        description="Add your first entry to this category."
      >
        <template #cta>
          <KinButton variant="primary" size="md" @click="showCreateEntry = true">Add Entry</KinButton>
        </template>
      </KinEmptyState>
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
    <KinModalSheet
      :model-value="showCreateEntry"
      title="New Vault Entry"
      size="xl"
      @close="closeCreateModal"
    >
      <form class="space-y-5" @submit.prevent="handleCreateEntry">
        <KinInput
          v-model="entryForm.title"
          label="Title"
          placeholder="e.g., Family Doctor, WiFi Info"
        />

        <div>
          <label class="block text-sm font-medium text-ink-primary mb-1.5">Content</label>
          <MarkdownEditor
            v-model="entryForm.body"
            placeholder="Start typing... Use **bold**, *italic*, lists, and more."
          />
        </div>

        <!-- Sensitive Fields (collapsible) -->
        <div>
          <button
            type="button"
            class="flex items-center gap-1.5 text-xs font-medium text-ink-tertiary hover:text-accent-lavender-bold transition-colors"
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
                class="p-2 text-ink-tertiary hover:text-status-failed transition-colors"
                @click="entryForm.sensitiveFields.splice(i, 1)"
              >
                <XMarkIcon class="w-4 h-4" />
              </button>
            </div>
            <button
              type="button"
              class="flex items-center gap-1.5 text-xs font-medium text-accent-lavender-bold hover:opacity-80"
              @click="entryForm.sensitiveFields.push({ key: '', value: '' })"
            >
              <PlusIcon class="w-3.5 h-3.5" />
              Add Field
            </button>
          </div>
        </div>

        <div class="flex gap-2 pt-2">
          <KinButton type="button" variant="secondary" size="md" class="flex-1" @click="closeCreateModal">
            Cancel
          </KinButton>
          <KinButton type="submit" variant="primary" size="md" class="flex-1" :disabled="!entryForm.title?.trim() || savingEntry">
            {{ savingEntry ? 'Saving...' : 'Create Entry' }}
          </KinButton>
        </div>
      </form>
    </KinModalSheet>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { storeToRefs } from 'pinia'
import { useVaultStore } from '@/stores/vault'
import { useAuthStore } from '@/stores/auth'
import { useNotification } from '@/composables/useNotification'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import ContextMenu from '@/components/common/ContextMenu.vue'
import ConfirmDialog from '@/components/common/ConfirmDialog.vue'
import FloatingActionButton from '@/components/common/FloatingActionButton.vue'
import MarkdownEditor from '@/components/vault/MarkdownEditor.vue'
import KinButton from '@/components/design-system/KinButton.vue'
import KinInput from '@/components/design-system/KinInput.vue'
import KinSearch from '@/components/design-system/KinSearch.vue'
import KinModalSheet from '@/components/design-system/KinModalSheet.vue'
import KinEmptyState from '@/components/design-system/KinEmptyState.vue'
import {
  ChevronLeftIcon,
  ChevronRightIcon,
  LockClosedIcon,
  PlusIcon,
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
