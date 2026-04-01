<template>
  <div class="h-full flex flex-col">
    <!-- Header -->
    <div class="px-4 pt-4 pb-2 md:px-6 md:pt-6">
      <div class="flex items-center gap-3">
        <button
          class="p-2 -ml-2 text-lavender-400 hover:text-prussian-500 dark:hover:text-lavender-200 hover:bg-lavender-100 dark:hover:bg-prussian-700 rounded-xl transition-colors"
          @click="$router.back()"
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
      <!-- Markdown Body -->
      <div
        v-if="entryBody"
        class="bg-white dark:bg-prussian-800 rounded-xl border border-lavender-200 dark:border-prussian-700 shadow-card overflow-hidden mt-2"
      >
        <div class="px-4 py-3 prose-vault" v-html="renderedBody"></div>
      </div>

      <!-- Legacy: flat key/value data (for old entries without body) -->
      <div
        v-else-if="legacyFields && Object.keys(legacyFields).length > 0"
        class="bg-white dark:bg-prussian-800 rounded-xl border border-lavender-200 dark:border-prussian-700 shadow-card overflow-hidden mt-2"
      >
        <div class="px-4 py-3 border-b border-lavender-100 dark:border-prussian-700">
          <h2 class="text-xs font-semibold text-lavender-500 dark:text-lavender-400 uppercase tracking-wider">Information</h2>
        </div>
        <div class="divide-y divide-lavender-50 dark:divide-prussian-700 px-4">
          <SensitiveField
            v-for="(value, key) in legacyFields"
            :key="key"
            :label="formatKey(key)"
            :value="value"
            :sensitive="isSensitiveField(key)"
          />
        </div>
      </div>

      <!-- Sensitive Fields -->
      <div
        v-if="sensitiveFields && Object.keys(sensitiveFields).length > 0"
        class="bg-white dark:bg-prussian-800 rounded-xl border border-lavender-200 dark:border-prussian-700 shadow-card overflow-hidden mt-4"
      >
        <div class="px-4 py-3 border-b border-lavender-100 dark:border-prussian-700 flex items-center gap-2">
          <LockClosedIcon class="w-3.5 h-3.5 text-lavender-400" />
          <h2 class="text-xs font-semibold text-lavender-500 dark:text-lavender-400 uppercase tracking-wider">Sensitive Information</h2>
        </div>
        <div class="divide-y divide-lavender-50 dark:divide-prussian-700 px-4">
          <SensitiveField
            v-for="(value, key) in sensitiveFields"
            :key="key"
            :label="formatKey(key)"
            :value="value"
            :sensitive="true"
          />
        </div>
      </div>

      <!-- Notes (legacy — shown only if present and no body) -->
      <div v-if="currentEntry.notes && !entryBody" class="bg-white dark:bg-prussian-800 rounded-xl border border-lavender-200 dark:border-prussian-700 shadow-card overflow-hidden mt-4">
        <div class="px-4 py-3 border-b border-lavender-100 dark:border-prussian-700">
          <h2 class="text-xs font-semibold text-lavender-500 dark:text-lavender-400 uppercase tracking-wider">Notes</h2>
        </div>
        <div class="px-4 py-3">
          <p class="text-sm text-prussian-500 dark:text-lavender-300 whitespace-pre-wrap">{{ currentEntry.notes }}</p>
        </div>
      </div>

      <!-- Documents -->
      <div v-if="isParent || currentEntry.documents?.length > 0" class="bg-white dark:bg-prussian-800 rounded-xl border border-lavender-200 dark:border-prussian-700 shadow-card overflow-hidden mt-4">
        <div class="px-4 py-3 border-b border-lavender-100 dark:border-prussian-700 flex items-center justify-between">
          <h2 class="text-xs font-semibold text-lavender-500 dark:text-lavender-400 uppercase tracking-wider">Documents</h2>
          <label v-if="isParent" class="flex items-center gap-1.5 text-xs font-medium text-wisteria-600 dark:text-wisteria-400 hover:text-wisteria-500 cursor-pointer transition-colors">
            <ArrowUpTrayIcon class="w-3.5 h-3.5" />
            Upload
            <input
              ref="fileInput"
              type="file"
              class="hidden"
              accept=".pdf,.jpg,.jpeg,.png,.gif,.webp,.doc,.docx,.xls,.xlsx,.txt,.csv"
              @change="handleFileUpload"
            />
          </label>
        </div>
        <div v-if="currentEntry.documents?.length > 0" class="divide-y divide-lavender-50 dark:divide-prussian-700">
          <a
            v-for="doc in currentEntry.documents"
            :key="doc.id"
            :href="doc.download_url"
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
        <div v-else class="px-4 py-3">
          <p class="text-xs text-lavender-400">No documents attached yet.</p>
        </div>
        <div v-if="uploading" class="px-4 py-2 border-t border-lavender-100 dark:border-prussian-700">
          <p class="text-xs text-wisteria-500">Uploading...</p>
        </div>
      </div>

      <!-- Shared With (Parent Only) -->
      <div v-if="isParent" class="bg-white dark:bg-prussian-800 rounded-xl border border-lavender-200 dark:border-prussian-700 shadow-card overflow-hidden mt-4">
        <div class="px-4 py-3 border-b border-lavender-100 dark:border-prussian-700 flex items-center justify-between">
          <h2 class="text-xs font-semibold text-lavender-500 dark:text-lavender-400 uppercase tracking-wider">Shared With</h2>
          <button
            class="flex items-center gap-1.5 text-xs font-medium text-wisteria-600 dark:text-wisteria-400 hover:text-wisteria-500 transition-colors"
            @click="showShareModal = true"
          >
            <ShareIcon class="w-3.5 h-3.5" />
            Share
          </button>
        </div>
        <div v-if="currentEntry.permissions?.length > 0" class="divide-y divide-lavender-50 dark:divide-prussian-700">
          <div
            v-for="perm in currentEntry.permissions"
            :key="perm.user_id"
            class="flex items-center gap-3 px-4 py-3"
          >
            <UserAvatar :user="perm.user" size="sm" />
            <div class="flex-1 min-w-0">
              <p class="text-sm font-medium text-prussian-500 dark:text-lavender-200">{{ perm.user?.name }}</p>
              <p class="text-xs text-lavender-400 capitalize">{{ perm.permission_level }} access</p>
            </div>
            <button
              class="p-1.5 text-lavender-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
              @click="handleRemovePermission(perm.user_id)"
            >
              <XMarkIcon class="w-4 h-4" />
            </button>
          </div>
        </div>
        <div v-else class="px-4 py-3">
          <p class="text-xs text-lavender-400">Not shared with anyone yet.</p>
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
      confirm-text="Delete"
      @confirm="handleDeleteEntry"
      @cancel="showDeleteConfirm = false"
    />

    <!-- Share Modal -->
    <BaseModal
      :show="showShareModal"
      title="Share Entry"
      @close="showShareModal = false"
    >
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-prussian-500 dark:text-lavender-200 mb-1.5">Family Member</label>
          <select v-model="shareForm.userId" class="input-base">
            <option :value="null">Select member...</option>
            <option
              v-for="member in shareableMembers"
              :key="member.id"
              :value="member.id"
            >
              {{ member.name }} ({{ member.family_role }})
            </option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-prussian-500 dark:text-lavender-200 mb-1.5">Permission Level</label>
          <select v-model="shareForm.level" class="input-base">
            <option value="view">View only</option>
            <option value="edit">Can edit</option>
          </select>
        </div>

        <div class="flex gap-2 pt-2">
          <button type="button" class="flex-1 btn-secondary btn-md rounded-xl" @click="showShareModal = false">
            Cancel
          </button>
          <button
            :disabled="!shareForm.userId || sharingEntry"
            class="flex-1 btn-primary btn-md rounded-xl disabled:opacity-40"
            @click="handleShareEntry"
          >
            {{ sharingEntry ? 'Sharing...' : 'Share' }}
          </button>
        </div>
      </div>
    </BaseModal>

    <!-- Edit Entry Modal -->
    <BaseModal
      :show="showEditModal"
      title="Edit Entry"
      size="xl"
      @close="showEditModal = false"
    >
      <form class="space-y-5" @submit.prevent="handleUpdateEntry">
        <div class="flex gap-3">
          <div class="flex-1">
            <label class="block text-sm font-medium text-prussian-500 dark:text-lavender-200 mb-1.5">Title</label>
            <input
              v-model="editForm.title"
              placeholder="Entry title"
              class="input-base"
            />
          </div>
          <div class="w-40">
            <label class="block text-sm font-medium text-prussian-500 dark:text-lavender-200 mb-1.5">Category</label>
            <select v-model="editForm.vault_category_id" class="input-base">
              <option :value="null">Select...</option>
              <option
                v-for="cat in categories"
                :key="cat.id"
                :value="cat.id"
              >
                {{ cat.name }}
              </option>
            </select>
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-prussian-500 dark:text-lavender-200 mb-1.5">Content</label>
          <MarkdownEditor
            v-model="editForm.body"
            placeholder="Start typing..."
          />
        </div>

        <!-- Sensitive Fields (collapsible) -->
        <div>
          <button
            type="button"
            class="flex items-center gap-1.5 text-xs font-medium text-lavender-500 dark:text-lavender-400 hover:text-wisteria-600 dark:hover:text-wisteria-400 transition-colors"
            @click="showEditSensitiveFields = !showEditSensitiveFields"
          >
            <LockClosedIcon class="w-3.5 h-3.5" />
            {{ showEditSensitiveFields ? 'Hide' : 'Show' }} sensitive fields
            <ChevronRightIcon class="w-3 h-3 transition-transform" :class="{ 'rotate-90': showEditSensitiveFields }" />
          </button>

          <div v-if="showEditSensitiveFields" class="mt-3 space-y-3">
            <div v-for="(field, i) in editForm.sensitiveFields" :key="i" class="flex gap-2">
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
                @click="editForm.sensitiveFields.splice(i, 1)"
              >
                <XMarkIcon class="w-4 h-4" />
              </button>
            </div>
            <button
              type="button"
              class="flex items-center gap-1.5 text-xs font-medium text-wisteria-600 dark:text-wisteria-400 hover:text-wisteria-500"
              @click="editForm.sensitiveFields.push({ key: '', value: '' })"
            >
              <PlusIcon class="w-3.5 h-3.5" />
              Add Field
            </button>
          </div>
        </div>

        <div class="flex gap-2 pt-2">
          <button type="button" class="flex-1 btn-secondary btn-md rounded-xl" @click="showEditModal = false">
            Cancel
          </button>
          <button type="submit" :disabled="!editForm.title?.trim() || savingEntry" class="flex-1 btn-primary btn-md rounded-xl disabled:opacity-40">
            {{ savingEntry ? 'Saving...' : 'Save Changes' }}
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
import { marked } from 'marked'
import DOMPurify from 'dompurify'
import { useVaultStore } from '@/stores/vault'
import { useAuthStore } from '@/stores/auth'
import { useNotification } from '@/composables/useNotification'
import SensitiveField from '@/components/vault/SensitiveField.vue'
import MarkdownEditor from '@/components/vault/MarkdownEditor.vue'
import ContextMenu from '@/components/common/ContextMenu.vue'
import ConfirmDialog from '@/components/common/ConfirmDialog.vue'
import BaseModal from '@/components/common/BaseModal.vue'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import UserAvatar from '@/components/common/UserAvatar.vue'
import {
  ChevronLeftIcon,
  ChevronRightIcon,
  DocumentTextIcon,
  ArrowDownTrayIcon,
  ArrowUpTrayIcon,
  LockClosedIcon,
  PencilIcon,
  PlusIcon,
  ShareIcon,
  TrashIcon,
  XMarkIcon,
} from '@heroicons/vue/24/outline'

const route = useRoute()
const router = useRouter()
const vaultStore = useVaultStore()
const authStore = useAuthStore()
const { success, error: notifyError } = useNotification()

const { currentEntry, isLoading, categories } = storeToRefs(vaultStore)
const { isParent, familyMembers } = storeToRefs(authStore)

const entryId = route.params.id
const showDeleteConfirm = ref(false)
const showEditModal = ref(false)
const showEditSensitiveFields = ref(false)
const savingEntry = ref(false)
const showShareModal = ref(false)
const sharingEntry = ref(false)
const uploading = ref(false)
const fileInput = ref(null)

const shareForm = ref({ userId: null, level: 'view' })

const editForm = ref({
  vault_category_id: null,
  title: '',
  body: '',
  sensitiveFields: [{ key: '', value: '' }],
})

// Parse entry data — supports new format (body + sensitive_fields) and legacy (flat key/value)
const entryBody = computed(() => currentEntry.value?.data?.body || '')
const sensitiveFields = computed(() => currentEntry.value?.data?.sensitive_fields || {})
const legacyFields = computed(() => {
  const data = currentEntry.value?.data
  if (!data || data.body !== undefined) return null
  // Legacy format: flat key/value pairs (no body key)
  return data
})

const renderedBody = computed(() => {
  if (!entryBody.value) return ''
  return DOMPurify.sanitize(marked.parse(entryBody.value))
})

const categoryName = computed(() => {
  if (!currentEntry.value) return ''
  const catId = currentEntry.value.vault_category_id || currentEntry.value.category?.id
  const cat = categories.value.find((c) => c.id === catId)
  return cat?.name || currentEntry.value.category?.name || ''
})

const entryMenuItems = computed(() => [
  { label: 'Edit', icon: PencilIcon, action: openEditModal },
  { divider: true },
  { label: 'Delete', icon: TrashIcon, variant: 'danger', action: () => { showDeleteConfirm.value = true } },
])

const openEditModal = () => {
  if (!currentEntry.value) return
  const data = currentEntry.value.data || {}
  const catId = currentEntry.value.vault_category_id || currentEntry.value.category?.id

  // Handle both new format (body + sensitive_fields) and legacy (flat kv)
  const isNewFormat = data.body !== undefined
  const body = isNewFormat
    ? (data.body || '')
    : Object.entries(data).map(([key, value]) => `**${formatKey(key)}:** ${value}`).join('\n\n')

  const sensitiveFieldsList = isNewFormat && data.sensitive_fields
    ? Object.entries(data.sensitive_fields).map(([key, value]) => ({ key, value: String(value) }))
    : []

  if (sensitiveFieldsList.length === 0) {
    sensitiveFieldsList.push({ key: '', value: '' })
  }

  editForm.value = {
    vault_category_id: catId || null,
    title: currentEntry.value.title || '',
    body,
    sensitiveFields: sensitiveFieldsList,
  }
  showEditSensitiveFields.value = sensitiveFieldsList.some((f) => f.key.trim())
  showEditModal.value = true
}

const handleUpdateEntry = async () => {
  if (!editForm.value.title?.trim()) return
  savingEntry.value = true

  const sensitiveFieldsObj = {}
  editForm.value.sensitiveFields.forEach((f) => {
    if (f.key?.trim() && f.value?.trim()) {
      sensitiveFieldsObj[f.key.trim()] = f.value.trim()
    }
  })

  const payload = {
    vault_category_id: editForm.value.vault_category_id,
    title: editForm.value.title,
    data: {
      body: editForm.value.body || '',
      sensitive_fields: Object.keys(sensitiveFieldsObj).length > 0 ? sensitiveFieldsObj : undefined,
    },
  }

  const result = await vaultStore.updateEntry(entryId, payload)
  if (result.success) {
    success('Entry updated!')
    showEditModal.value = false
    await vaultStore.fetchEntry(entryId)
  } else {
    notifyError(result.error || 'Failed to update entry')
  }
  savingEntry.value = false
}

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

// Members who don't already have permission on this entry
const shareableMembers = computed(() => {
  const existingUserIds = (currentEntry.value?.permissions || []).map((p) => p.user_id)
  return (familyMembers.value || []).filter((m) => !existingUserIds.includes(m.id))
})

const handleShareEntry = async () => {
  if (!shareForm.value.userId) return
  sharingEntry.value = true
  const result = await vaultStore.grantPermission(entryId, shareForm.value.userId, shareForm.value.level)
  if (result.success) {
    success('Entry shared!')
    showShareModal.value = false
    shareForm.value = { userId: null, level: 'view' }
  } else {
    notifyError(result.error || 'Failed to share')
  }
  sharingEntry.value = false
}

const handleFileUpload = async (event) => {
  const file = event.target.files?.[0]
  if (!file) return
  uploading.value = true
  const result = await vaultStore.uploadDocument(entryId, file)
  if (result.success) {
    success('Document uploaded!')
  } else {
    notifyError(result.error || 'Failed to upload')
  }
  uploading.value = false
  if (fileInput.value) fileInput.value.value = ''
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

<style>
/* Rendered markdown body styling */
.prose-vault h1 { @apply text-xl font-bold font-heading mb-3 mt-4 first:mt-0 text-prussian-500 dark:text-lavender-200; }
.prose-vault h2 { @apply text-lg font-bold font-heading mb-2 mt-3 first:mt-0 text-prussian-500 dark:text-lavender-200; }
.prose-vault h3 { @apply text-base font-semibold mb-2 mt-3 first:mt-0 text-prussian-500 dark:text-lavender-200; }
.prose-vault p { @apply mb-2 last:mb-0 leading-relaxed text-sm text-prussian-500 dark:text-lavender-300; }
.prose-vault strong { @apply font-semibold text-prussian-500 dark:text-lavender-200; }
.prose-vault em { @apply italic; }
.prose-vault a { @apply text-wisteria-600 dark:text-wisteria-400 underline; }
.prose-vault ul { @apply list-disc pl-6 mb-2 text-sm text-prussian-500 dark:text-lavender-300; }
.prose-vault ol { @apply list-decimal pl-6 mb-2 text-sm text-prussian-500 dark:text-lavender-300; }
.prose-vault li { @apply mb-1; }
.prose-vault code { @apply text-xs bg-lavender-100 dark:bg-prussian-700 px-1.5 py-0.5 rounded font-mono; }
.prose-vault pre { @apply bg-lavender-50 dark:bg-prussian-900 rounded-lg p-3 mb-2 overflow-x-auto; }
.prose-vault pre code { @apply bg-transparent px-0 py-0; }
.prose-vault table { @apply w-full border-collapse mb-2; }
.prose-vault th { @apply bg-lavender-50 dark:bg-prussian-700 text-left px-3 py-2 text-xs font-semibold uppercase tracking-wider border border-lavender-200 dark:border-prussian-600; }
.prose-vault td { @apply px-3 py-2 text-sm border border-lavender-200 dark:border-prussian-600; }
.prose-vault blockquote { @apply border-l-4 border-wisteria-300 dark:border-wisteria-600 pl-4 italic text-lavender-500 dark:text-lavender-400 mb-2; }
.prose-vault hr { @apply border-lavender-200 dark:border-prussian-600 my-4; }
</style>
