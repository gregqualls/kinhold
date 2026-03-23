<template>
  <div class="h-full flex flex-col">
    <!-- Header -->
    <div class="px-4 pt-4 pb-2 md:px-6 md:pt-6">
      <div class="flex items-center gap-3">
        <button
          @click="$router.back()"
          class="p-2 -ml-2 text-lavender-400 hover:text-prussian-500 dark:hover:text-lavender-200 hover:bg-lavender-100 dark:hover:bg-prussian-700 rounded-xl transition-colors"
        >
          <ChevronLeftIcon class="w-5 h-5" />
        </button>
        <div class="flex-1 min-w-0">
          <h1 class="text-xl font-bold font-heading text-prussian-500 dark:text-lavender-200 truncate">{{ currentEntry?.title || 'Entry' }}</h1>
          <p v-if="categoryName" class="text-xs text-lavender-500 dark:text-lavender-400 mt-0.5">{{ categoryName }}</p>
        </div>

        <ContextMenu v-if="isParent && currentEntry" :items="entryMenuItems" />
      </div>
    </div>

    <!-- Loading -->
    <div v-if="isLoading" class="flex items-center justify-center py-16">
      <LoadingSpinner size="lg" />
    </div>

    <!-- Entry Content -->
    <div v-else-if="currentEntry" class="flex-1 overflow-y-auto px-4 md:px-6 pb-32 md:pb-6">
      <!-- Data Fields -->
      <div v-if="currentEntry.data && Object.keys(currentEntry.data).length > 0" class="bg-white dark:bg-prussian-800 rounded-xl border border-lavender-200 dark:border-prussian-700 shadow-card overflow-hidden mt-2">
        <div class="px-4 py-3 border-b border-lavender-100 dark:border-prussian-700">
          <h2 class="text-xs font-semibold text-lavender-500 dark:text-lavender-400 uppercase tracking-wider">Information</h2>
        </div>

        <div class="divide-y divide-lavender-50 dark:divide-prussian-700 px-4">
          <SensitiveField
            v-for="(value, key) in currentEntry.data"
            :key="key"
            :label="formatKey(key)"
            :value="value"
            :sensitive="isSensitiveField(key)"
          />
        </div>
      </div>

      <!-- Notes -->
      <div v-if="currentEntry.notes" class="bg-white dark:bg-prussian-800 rounded-xl border border-lavender-200 dark:border-prussian-700 shadow-card overflow-hidden mt-4">
        <div class="px-4 py-3 border-b border-lavender-100 dark:border-prussian-700">
          <h2 class="text-xs font-semibold text-lavender-500 dark:text-lavender-400 uppercase tracking-wider">Notes</h2>
        </div>
        <div class="px-4 py-3">
          <p class="text-sm text-prussian-500 dark:text-lavender-300 whitespace-pre-wrap">{{ currentEntry.notes }}</p>
        </div>
      </div>

      <!-- Documents -->
      <div v-if="currentEntry.documents?.length > 0" class="bg-white dark:bg-prussian-800 rounded-xl border border-lavender-200 dark:border-prussian-700 shadow-card overflow-hidden mt-4">
        <div class="px-4 py-3 border-b border-lavender-100 dark:border-prussian-700">
          <h2 class="text-xs font-semibold text-lavender-500 dark:text-lavender-400 uppercase tracking-wider">Documents</h2>
        </div>
        <div class="divide-y divide-lavender-50 dark:divide-prussian-700">
          <a
            v-for="doc in currentEntry.documents"
            :key="doc.id"
            :href="doc.url"
            target="_blank"
            class="flex items-center gap-3 px-4 py-3 hover:bg-lavender-50 dark:hover:bg-prussian-700 transition-colors"
          >
            <DocumentTextIcon class="w-5 h-5 text-lavender-400 flex-shrink-0" />
            <div class="flex-1 min-w-0">
              <p class="text-sm font-medium text-prussian-500 dark:text-lavender-200 truncate">{{ doc.original_filename || doc.filename }}</p>
              <p class="text-xs text-lavender-400">{{ formatFileSize(doc.size) }}</p>
            </div>
            <ArrowDownTrayIcon class="w-4 h-4 text-lavender-400" />
          </a>
        </div>
      </div>

      <!-- Shared With (Parent Only) -->
      <div v-if="isParent && currentEntry.permissions?.length > 0" class="bg-white dark:bg-prussian-800 rounded-xl border border-lavender-200 dark:border-prussian-700 shadow-card overflow-hidden mt-4">
        <div class="px-4 py-3 border-b border-lavender-100 dark:border-prussian-700">
          <h2 class="text-xs font-semibold text-lavender-500 dark:text-lavender-400 uppercase tracking-wider">Shared With</h2>
        </div>
        <div class="divide-y divide-lavender-50 dark:divide-prussian-700">
          <div
            v-for="perm in currentEntry.permissions"
            :key="perm.user_id"
            class="flex items-center gap-3 px-4 py-3"
          >
            <UserAvatar :user="perm.user" size="sm" />
            <div class="flex-1 min-w-0">
              <p class="text-sm font-medium text-prussian-500 dark:text-lavender-200">{{ perm.user?.name }}</p>
              <p class="text-xs text-lavender-400 capitalize">{{ perm.level }} access</p>
            </div>
            <button
              @click="handleRemovePermission(perm.user_id)"
              class="p-1.5 text-lavender-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
            >
              <XMarkIcon class="w-4 h-4" />
            </button>
          </div>
        </div>
      </div>

      <!-- Metadata -->
      <div class="mt-4 px-1">
        <p class="text-xs text-lavender-400">
          Created {{ formatDate(currentEntry.created_at) }}
          <span v-if="currentEntry.updated_at !== currentEntry.created_at">
            &middot; Updated {{ formatDate(currentEntry.updated_at) }}
          </span>
        </p>
      </div>
    </div>

    <!-- Delete Confirmation -->
    <ConfirmDialog
      :show="showDeleteConfirm"
      title="Delete Entry?"
      :message="`&quot;${currentEntry?.title}&quot; and all its data will be permanently deleted.`"
      confirmText="Delete"
      @confirm="handleDeleteEntry"
      @cancel="showDeleteConfirm = false"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { storeToRefs } from 'pinia'
import { useVaultStore } from '@/stores/vault'
import { useAuthStore } from '@/stores/auth'
import { useNotification } from '@/composables/useNotification'
import SensitiveField from '@/components/vault/SensitiveField.vue'
import ContextMenu from '@/components/common/ContextMenu.vue'
import ConfirmDialog from '@/components/common/ConfirmDialog.vue'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import UserAvatar from '@/components/common/UserAvatar.vue'
import {
  ChevronLeftIcon,
  DocumentTextIcon,
  ArrowDownTrayIcon,
  PencilIcon,
  TrashIcon,
  XMarkIcon,
} from '@heroicons/vue/24/outline'

const route = useRoute()
const router = useRouter()
const vaultStore = useVaultStore()
const authStore = useAuthStore()
const { success, error: notifyError } = useNotification()

const { currentEntry, isLoading, categories } = storeToRefs(vaultStore)
const { isParent } = storeToRefs(authStore)

const entryId = route.params.id
const showDeleteConfirm = ref(false)

const categoryName = computed(() => {
  if (!currentEntry.value) return ''
  const catId = currentEntry.value.vault_category_id || currentEntry.value.category_id
  const cat = categories.value.find((c) => c.id === catId)
  return cat?.name || ''
})

const entryMenuItems = computed(() => [
  { label: 'Edit', icon: PencilIcon, action: () => { /* TODO */ } },
  { divider: true },
  { label: 'Delete', icon: TrashIcon, variant: 'danger', action: () => { showDeleteConfirm.value = true } },
])

const isSensitiveField = (key) => {
  const sensitivePatterns = ['ssn', 'password', 'pin', 'account', 'routing', 'secret', 'key', 'card', 'cvv', 'security']
  return sensitivePatterns.some((p) => key.toLowerCase().includes(p))
}

const formatKey = (key) =>
  key.replace(/_/g, ' ').split(' ').map((w) => w.charAt(0).toUpperCase() + w.slice(1)).join(' ')

const formatDate = (dateStr) => {
  if (!dateStr) return ''
  return new Date(dateStr).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
}

const formatFileSize = (bytes) => {
  if (!bytes) return ''
  const k = 1024
  const sizes = ['B', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i]
}

const handleDeleteEntry = async () => {
  const result = await vaultStore.deleteEntry(entryId)
  if (result.success) {
    success('Entry deleted!')
    router.push('/vault')
  } else {
    notifyError(result.error || 'Failed to delete')
  }
  showDeleteConfirm.value = false
}

const handleRemovePermission = async (userId) => {
  const result = await vaultStore.revokePermission(entryId, userId)
  if (result.success) {
    success('Access removed!')
  }
}

onMounted(async () => {
  if (categories.value.length === 0) {
    await vaultStore.fetchCategories()
  }
  await vaultStore.fetchEntry(entryId)
})
</script>
