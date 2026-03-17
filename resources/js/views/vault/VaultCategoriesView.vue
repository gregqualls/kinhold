<template>
  <div class="h-full flex flex-col">
    <!-- Header -->
    <div class="px-4 pt-4 pb-2 md:px-6 md:pt-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-prussian-500 dark:text-lavender-200">Vault</h1>
          <p class="text-sm text-lavender-500 dark:text-lavender-400 mt-0.5">Secure family information</p>
        </div>
        <button
          v-if="isParent"
          @click="showCreateEntry = true"
          class="hidden md:flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-wisteria-600 hover:bg-wisteria-500 rounded-xl transition-colors"
        >
          <PlusIcon class="w-4 h-4" />
          Add Entry
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
          placeholder="Search vault..."
          class="w-full pl-9 pr-4 py-2.5 bg-lavender-50 dark:bg-prussian-700 border border-lavender-200 dark:border-prussian-600 rounded-xl text-sm placeholder-lavender-400 dark:placeholder-lavender-500 text-prussian-500 dark:text-lavender-200 focus:bg-white dark:focus:bg-prussian-800 focus:ring-2 focus:ring-wisteria-400 transition-all outline-none"
        />
      </div>
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
          @click="$router.push(`/vault/${category.slug}`)"
          class="group flex items-center gap-4 p-4 bg-white dark:bg-prussian-800 rounded-xl shadow-card hover:shadow-card-lg border border-lavender-200 dark:border-prussian-700 hover:border-wisteria-300 dark:hover:border-wisteria-700 cursor-pointer transition-all"
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
            <h3 class="font-semibold text-prussian-500 dark:text-lavender-200 text-sm">{{ category.name }}</h3>
            <p class="text-xs text-lavender-500 dark:text-lavender-400 mt-0.5 truncate">{{ category.description }}</p>
          </div>

          <!-- Entry count -->
          <span
            v-if="getEntryCount(category.id) > 0"
            class="text-xs font-medium text-lavender-500 dark:text-lavender-400 bg-lavender-100 dark:bg-prussian-700 px-2 py-0.5 rounded-full"
          >
            {{ getEntryCount(category.id) }}
          </span>

          <ChevronRightIcon class="w-4 h-4 text-lavender-300 dark:text-lavender-500 flex-shrink-0" />
        </div>
      </div>

      <!-- Empty State -->
      <EmptyState
        v-if="!isLoading && categories.length === 0"
        :icon="ShieldCheckIcon"
        title="Vault is empty"
        description="Add categories to organize your family's important information."
        actionText="Add Category"
        @action="showCreateEntry = true"
      />
    </div>

    <!-- Mobile FAB -->
    <FloatingActionButton v-if="isParent" @click="showCreateEntry = true" />

    <!-- Create Entry Modal -->
    <BaseModal
      :show="showCreateEntry"
      title="New Vault Entry"
      @close="showCreateEntry = false"
    >
      <form @submit.prevent="handleCreateEntry" class="space-y-5">
        <div>
          <label class="block text-sm font-medium text-prussian-500 dark:text-lavender-200 mb-1.5">Category</label>
          <select v-model="entryForm.category_id" class="input-base">
            <option :value="null">Select category...</option>
            <option
              v-for="cat in categories"
              :key="cat.id"
              :value="cat.id"
            >
              {{ cat.name }}
            </option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-prussian-500 dark:text-lavender-200 mb-1.5">Title</label>
          <input
            v-model="entryForm.title"
            placeholder="e.g., Health Insurance, Bank Account"
            class="input-base"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-prussian-500 dark:text-lavender-200 mb-1.5">Notes (optional)</label>
          <textarea
            v-model="entryForm.notes"
            rows="2"
            placeholder="Any additional notes..."
            class="input-base resize-none"
          />
        </div>

        <!-- Dynamic fields based on category -->
        <div>
          <label class="block text-sm font-medium text-prussian-500 dark:text-lavender-200 mb-2">Fields</label>
          <div class="space-y-3">
            <div v-for="(field, i) in entryForm.fields" :key="i" class="flex gap-2">
              <input
                v-model="field.key"
                placeholder="Field name"
                class="input-base flex-1"
              />
              <input
                v-model="field.value"
                placeholder="Value"
                class="input-base flex-1"
              />
              <button
                type="button"
                @click="entryForm.fields.splice(i, 1)"
                class="p-2 text-lavender-400 hover:text-red-500 transition-colors"
              >
                <XMarkIcon class="w-4 h-4" />
              </button>
            </div>
            <button
              type="button"
              @click="entryForm.fields.push({ key: '', value: '' })"
              class="flex items-center gap-1.5 text-xs font-medium text-wisteria-600 dark:text-wisteria-400 hover:text-wisteria-500"
            >
              <PlusIcon class="w-3.5 h-3.5" />
              Add Field
            </button>
          </div>
        </div>

        <div class="flex gap-2 pt-2">
          <button type="button" @click="showCreateEntry = false" class="flex-1 btn-secondary btn-md rounded-xl">
            Cancel
          </button>
          <button type="submit" :disabled="!entryForm.title?.trim() || !entryForm.category_id || savingEntry" class="flex-1 btn-primary btn-md rounded-xl disabled:opacity-40">
            {{ savingEntry ? 'Saving...' : 'Create Entry' }}
          </button>
        </div>
      </form>
    </BaseModal>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { storeToRefs } from 'pinia'
import { useVaultStore } from '@/stores/vault'
import { useAuthStore } from '@/stores/auth'
import { useNotification } from '@/composables/useNotification'
import EmptyState from '@/components/common/EmptyState.vue'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import BaseModal from '@/components/common/BaseModal.vue'
import FloatingActionButton from '@/components/common/FloatingActionButton.vue'
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
} from '@heroicons/vue/24/outline'

const vaultStore = useVaultStore()
const authStore = useAuthStore()
const { success, error: notifyError } = useNotification()
const { categories, entries, isLoading } = storeToRefs(vaultStore)
const { isParent } = storeToRefs(authStore)

const searchQuery = ref('')
const showCreateEntry = ref(false)
const savingEntry = ref(false)

const entryForm = ref({
  category_id: null,
  title: '',
  notes: '',
  fields: [{ key: '', value: '' }],
})

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
  }
  return classes[iconType] || 'bg-lavender-50 dark:bg-prussian-700'
}

const getCategoryTextClass = (iconType) => {
  const classes = {
    medical: 'text-red-500',
    financial: 'text-emerald-500',
    legal: 'text-blue-500',
    education: 'text-wisteria-500',
    insurance: 'text-sand-500',
    personal: 'text-pink-500',
  }
  return classes[iconType] || 'text-lavender-500'
}

const handleCreateEntry = async () => {
  if (!entryForm.value.title?.trim() || !entryForm.value.category_id) return
  savingEntry.value = true

  const data = {
    vault_category_id: entryForm.value.category_id,
    title: entryForm.value.title,
    notes: entryForm.value.notes,
    encrypted_data: {},
  }

  // Build encrypted_data from fields
  entryForm.value.fields.forEach((f) => {
    if (f.key?.trim() && f.value?.trim()) {
      data.encrypted_data[f.key.trim()] = f.value.trim()
    }
  })

  const result = await vaultStore.createEntry(data)
  if (result.success) {
    success('Entry created!')
    showCreateEntry.value = false
    entryForm.value = { category_id: null, title: '', notes: '', fields: [{ key: '', value: '' }] }
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
