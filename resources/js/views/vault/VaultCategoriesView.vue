<template>
  <div class="h-full flex flex-col">
    <!-- Header -->
    <div class="px-4 pt-4 pb-2 md:px-6 md:pt-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold font-heading text-ink-primary">Vault</h1>
          <p class="text-sm text-ink-tertiary mt-0.5">Secure family information</p>
        </div>
        <div v-if="isParent" class="hidden md:flex items-center gap-2">
          <KinButton variant="secondary" size="md" @click="openCategoryModal()">
            <FolderPlusIcon class="w-4 h-4" />
            New Category
          </KinButton>
          <KinButton variant="primary" size="md" @click="showCreateEntry = true">
            <PlusIcon class="w-4 h-4" />
            Add Entry
          </KinButton>
        </div>
      </div>
    </div>

    <!-- Search -->
    <div class="px-4 md:px-6 py-3">
      <KinSearch v-model="searchQuery" placeholder="Search vault..." />
    </div>

    <!-- Loading -->
    <div v-if="isLoading" class="flex items-center justify-center py-16">
      <LoadingSpinner size="lg" />
    </div>

    <!-- Categories -->
    <div v-else class="flex-1 overflow-y-auto px-4 md:px-6 pb-32 md:pb-6">
      <div v-if="filteredCategories.length > 0" class="space-y-2">
        <div
          v-for="category in filteredCategories"
          :key="category.id"
          class="group flex items-center gap-4 p-4 bg-surface-raised rounded-xl shadow-card hover:shadow-card-lg border border-border-subtle hover:border-accent-lavender-bold cursor-pointer transition-all"
          @click="$router.push(`/vault/${category.slug}`)"
        >
          <!-- Icon -->
          <div
            class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0"
            :class="getCategoryBgClass(category.icon)"
          >
            <component
              :is="getCategoryIcon(category.icon)"
              class="w-5 h-5"
              :class="getCategoryTextClass(category.icon)"
            />
          </div>

          <!-- Info -->
          <div class="flex-1 min-w-0">
            <h3 class="font-semibold text-ink-primary text-sm">{{ category.name }}</h3>
            <p class="text-xs text-ink-tertiary mt-0.5 truncate">{{ category.description }}</p>
          </div>

          <!-- Entry count -->
          <span
            v-if="getEntryCount(category.id) > 0"
            class="text-xs font-medium text-ink-tertiary bg-surface-sunken px-2 py-0.5 rounded-full"
          >
            {{ getEntryCount(category.id) }}
          </span>

          <!-- Context menu (parent only) -->
          <div v-if="isParent" class="opacity-0 group-hover:opacity-100 transition-opacity" @click.stop>
            <ContextMenu :items="getCategoryMenuItems(category)" />
          </div>

          <ChevronRightIcon class="w-4 h-4 text-ink-tertiary flex-shrink-0" />
        </div>
      </div>

      <!-- Empty State -->
      <KinEmptyState
        v-if="!isLoading && categories.length === 0"
        :icon="ShieldCheckIcon"
        title="Vault is empty"
        description="Create a category to start organizing your family's information."
      >
        <template #cta>
          <KinButton variant="primary" size="md" @click="openCategoryModal()">New Category</KinButton>
        </template>
      </KinEmptyState>
    </div>

    <!-- Mobile FAB -->
    <FloatingActionButton v-if="isParent" @click="showCreateEntry = true" />

    <!-- Create/Edit Category Modal -->
    <KinModalSheet
      :model-value="showCategoryModal"
      :title="editingCategory ? 'Edit Category' : 'New Category'"
      @close="closeCategoryModal"
    >
      <form class="space-y-5" @submit.prevent="handleSaveCategory">
        <KinInput
          v-model="categoryForm.name"
          label="Name"
          placeholder="e.g., House, Vehicle, School"
        />

        <KinInput
          v-model="categoryForm.description"
          label="Description (optional)"
          placeholder="What goes in this category?"
        />

        <div>
          <label class="block text-sm font-medium text-ink-primary mb-2">Icon</label>
          <div class="flex flex-wrap gap-2">
            <button
              v-for="icon in availableIcons"
              :key="icon.key"
              type="button"
              class="w-10 h-10 rounded-xl flex items-center justify-center transition-all"
              :class="categoryForm.icon === icon.key
                ? 'bg-accent-lavender-soft/40 ring-2 ring-accent-lavender-bold'
                : 'bg-surface-sunken hover:bg-surface-overlay'"
              @click="categoryForm.icon = icon.key"
            >
              <component :is="icon.component" class="w-5 h-5 text-ink-secondary" />
            </button>
          </div>
        </div>

        <div class="flex gap-2 pt-2">
          <KinButton type="button" variant="secondary" size="md" class="flex-1" @click="closeCategoryModal">
            Cancel
          </KinButton>
          <KinButton type="submit" variant="primary" size="md" class="flex-1" :disabled="!categoryForm.name?.trim() || savingCategory">
            {{ savingCategory ? 'Saving...' : (editingCategory ? 'Save Changes' : 'Create Category') }}
          </KinButton>
        </div>
      </form>
    </KinModalSheet>

    <!-- Delete Category Confirmation -->
    <ConfirmDialog
      :show="!!deletingCategory"
      title="Delete Category?"
      :message="`&quot;${deletingCategory?.name}&quot; will be permanently deleted. It must be empty first.`"
      confirm-text="Delete"
      @confirm="handleDeleteCategory"
      @cancel="deletingCategory = null"
    />

    <!-- Create Entry Modal -->
    <KinModalSheet
      :model-value="showCreateEntry"
      title="New Vault Entry"
      size="xl"
      @close="closeCreateModal"
    >
      <form class="space-y-5" @submit.prevent="handleCreateEntry">
        <div class="flex gap-3">
          <KinInput
            v-model="entryForm.title"
            label="Title"
            placeholder="e.g., Family Doctor, WiFi Info"
            class="flex-1"
          />
          <KinSelect
            v-model="entryForm.category_id"
            label="Category"
            class="w-40"
            :options="[{ value: null, label: 'Select...' }, ...categories.map((c) => ({ value: c.id, label: c.name }))]"
          />
        </div>

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
          <KinButton type="submit" variant="primary" size="md" class="flex-1" :disabled="!entryForm.title?.trim() || !entryForm.category_id || savingEntry">
            {{ savingEntry ? 'Saving...' : 'Create Entry' }}
          </KinButton>
        </div>
      </form>
    </KinModalSheet>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { storeToRefs } from 'pinia'
import { useVaultStore } from '@/stores/vault'
import { useAuthStore } from '@/stores/auth'
import { useNotification } from '@/composables/useNotification'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import ConfirmDialog from '@/components/common/ConfirmDialog.vue'
import ContextMenu from '@/components/common/ContextMenu.vue'
import FloatingActionButton from '@/components/common/FloatingActionButton.vue'
import MarkdownEditor from '@/components/vault/MarkdownEditor.vue'
import KinButton from '@/components/design-system/KinButton.vue'
import KinInput from '@/components/design-system/KinInput.vue'
import KinSelect from '@/components/design-system/KinSelect.vue'
import KinSearch from '@/components/design-system/KinSearch.vue'
import KinModalSheet from '@/components/design-system/KinModalSheet.vue'
import KinEmptyState from '@/components/design-system/KinEmptyState.vue'
import {
  PlusIcon,
  MagnifyingGlassIcon,
  ChevronRightIcon,
  XMarkIcon,
  HeartIcon,
  CreditCardIcon,
  DocumentTextIcon,
  AcademicCapIcon,
  LockClosedIcon,
  ShieldCheckIcon,
  IdentificationIcon,
  HomeIcon,
  TruckIcon,
  WrenchScrewdriverIcon,
  PencilIcon,
  TrashIcon,
  FolderPlusIcon,
} from '@heroicons/vue/24/outline'

const vaultStore = useVaultStore()
const authStore = useAuthStore()
const { success, error: notifyError } = useNotification()
const { categories, entries, isLoading } = storeToRefs(vaultStore)
const { isParent } = storeToRefs(authStore)

const searchQuery = ref('')
const showCreateEntry = ref(false)
const showSensitiveFields = ref(false)
const savingEntry = ref(false)

// Category modal state
const showCategoryModal = ref(false)
const editingCategory = ref(null)
const deletingCategory = ref(null)
const savingCategory = ref(false)

const categoryForm = ref({ name: '', icon: 'lock', description: '' })

const availableIcons = [
  { key: 'heart', label: 'Medical', component: HeartIcon },
  { key: 'dollar-sign', label: 'Financial', component: CreditCardIcon },
  { key: 'shield', label: 'Insurance', component: ShieldCheckIcon },
  { key: 'briefcase', label: 'Legal', component: DocumentTextIcon },
  { key: 'book', label: 'Education', component: AcademicCapIcon },
  { key: 'lock', label: 'Personal', component: LockClosedIcon },
  { key: 'home', label: 'House', component: HomeIcon },
  { key: 'truck', label: 'Vehicle', component: TruckIcon },
  { key: 'wrench', label: 'Tools', component: WrenchScrewdriverIcon },
  { key: 'id', label: 'Identity', component: IdentificationIcon },
]

const defaultEntryForm = () => ({
  category_id: null,
  title: '',
  body: '',
  sensitiveFields: [{ key: '', value: '' }],
})

const entryForm = ref(defaultEntryForm())

const filteredCategories = computed(() =>
  (categories.value || []).filter((cat) =>
    cat.name.toLowerCase().includes(searchQuery.value.toLowerCase())
  )
)

const getEntryCount = (categoryId) =>
  (entries.value || []).filter((e) => e.vault_category_id === categoryId || e.category_id === categoryId).length

const getCategoryIcon = (iconType) => {
  const icons = {
    medical: HeartIcon,
    financial: CreditCardIcon,
    legal: DocumentTextIcon,
    education: AcademicCapIcon,
    insurance: ShieldCheckIcon,
    personal: IdentificationIcon,
    heart: HeartIcon,
    'dollar-sign': CreditCardIcon,
    shield: ShieldCheckIcon,
    briefcase: DocumentTextIcon,
    book: AcademicCapIcon,
    lock: LockClosedIcon,
    home: HomeIcon,
    truck: TruckIcon,
    wrench: WrenchScrewdriverIcon,
    id: IdentificationIcon,
  }
  return icons[iconType] || LockClosedIcon
}

const getCategoryBgClass = (iconType) => {
  const classes = {
    medical: 'bg-red-50 dark:bg-red-900/20',
    financial: 'bg-emerald-50 dark:bg-emerald-900/20',
    legal: 'bg-blue-50 dark:bg-blue-900/20',
    education: 'bg-wisteria-50 dark:bg-wisteria-900/20',
    insurance: 'bg-sand-50 dark:bg-sand-900/20',
    personal: 'bg-pink-50 dark:bg-pink-900/20',
    heart: 'bg-red-50 dark:bg-red-900/20',
    'dollar-sign': 'bg-emerald-50 dark:bg-emerald-900/20',
    shield: 'bg-sand-50 dark:bg-sand-900/20',
    briefcase: 'bg-blue-50 dark:bg-blue-900/20',
    book: 'bg-wisteria-50 dark:bg-wisteria-900/20',
    lock: 'bg-surface-sunken',
    home: 'bg-amber-50 dark:bg-amber-900/20',
    truck: 'bg-cyan-50 dark:bg-cyan-900/20',
    wrench: 'bg-orange-50 dark:bg-orange-900/20',
    id: 'bg-pink-50 dark:bg-pink-900/20',
  }
  return classes[iconType] || 'bg-surface-sunken'
}

const getCategoryTextClass = (iconType) => {
  const classes = {
    medical: 'text-red-500',
    financial: 'text-emerald-500',
    legal: 'text-blue-500',
    education: 'text-wisteria-500',
    insurance: 'text-sand-500',
    personal: 'text-pink-500',
    heart: 'text-red-500',
    'dollar-sign': 'text-emerald-500',
    shield: 'text-sand-500',
    briefcase: 'text-blue-500',
    book: 'text-wisteria-500',
    lock: 'text-ink-tertiary',
    home: 'text-amber-500',
    truck: 'text-cyan-500',
    wrench: 'text-orange-500',
    id: 'text-pink-500',
  }
  return classes[iconType] || 'text-ink-tertiary'
}

const getCategoryMenuItems = (category) => [
  { label: 'Edit', icon: PencilIcon, action: () => openCategoryModal(category) },
  { divider: true },
  { label: 'Delete', icon: TrashIcon, variant: 'danger', action: () => { deletingCategory.value = category } },
]

const openCategoryModal = (category = null) => {
  editingCategory.value = category
  categoryForm.value = category
    ? { name: category.name, icon: category.icon || 'lock', description: category.description || '' }
    : { name: '', icon: 'lock', description: '' }
  showCategoryModal.value = true
}

const closeCategoryModal = () => {
  showCategoryModal.value = false
  editingCategory.value = null
  categoryForm.value = { name: '', icon: 'lock', description: '' }
}

const handleSaveCategory = async () => {
  if (!categoryForm.value.name?.trim()) return
  savingCategory.value = true

  const payload = {
    name: categoryForm.value.name,
    icon: categoryForm.value.icon,
    description: categoryForm.value.description || null,
  }

  const result = editingCategory.value
    ? await vaultStore.updateCategory(editingCategory.value.id, payload)
    : await vaultStore.createCategory(payload)

  if (result.success) {
    success(editingCategory.value ? 'Category updated!' : 'Category created!')
    closeCategoryModal()
  } else {
    notifyError(result.error || 'Failed to save category')
  }
  savingCategory.value = false
}

const handleDeleteCategory = async () => {
  if (!deletingCategory.value) return
  const result = await vaultStore.deleteCategory(deletingCategory.value.id)
  if (result.success) {
    success('Category deleted!')
  } else {
    notifyError(result.error || 'Failed to delete category')
  }
  deletingCategory.value = null
}

const closeCreateModal = () => {
  showCreateEntry.value = false
  showSensitiveFields.value = false
  entryForm.value = defaultEntryForm()
}

const handleCreateEntry = async () => {
  if (!entryForm.value.title?.trim() || !entryForm.value.category_id) return
  savingEntry.value = true

  const sensitiveFields = {}
  entryForm.value.sensitiveFields.forEach((f) => {
    if (f.key?.trim() && f.value?.trim()) {
      sensitiveFields[f.key.trim()] = f.value.trim()
    }
  })

  const payload = {
    vault_category_id: entryForm.value.category_id,
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

onMounted(async () => {
  await vaultStore.fetchCategories()
  await vaultStore.fetchEntries()
})
</script>
