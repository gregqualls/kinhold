<template>
  <BaseModal :show="show" title="Import Recipe" size="xl" @close="handleClose">
    <!-- Tab switcher (only before preview) -->
    <div v-if="!previewData" class="flex gap-1 mb-4 border-b border-lavender-200 dark:border-prussian-700 -mx-6 px-6">
      <button
        class="px-4 py-2.5 text-sm font-medium transition-colors relative"
        :class="activeTab === 'url'
          ? 'text-[#C4975A]'
          : 'text-lavender-500 dark:text-lavender-400 hover:text-prussian-500 dark:hover:text-lavender-200'"
        @click="activeTab = 'url'"
      >
        From URL
        <span v-if="activeTab === 'url'" class="absolute bottom-0 left-0 right-0 h-0.5 bg-[#C4975A] rounded-full"></span>
      </button>
      <button
        class="px-4 py-2.5 text-sm font-medium transition-colors relative"
        :class="activeTab === 'photo'
          ? 'text-[#C4975A]'
          : 'text-lavender-500 dark:text-lavender-400 hover:text-prussian-500 dark:hover:text-lavender-200'"
        @click="activeTab = 'photo'"
      >
        From Photo
        <span v-if="activeTab === 'photo'" class="absolute bottom-0 left-0 right-0 h-0.5 bg-[#C4975A] rounded-full"></span>
      </button>
    </div>

    <!-- URL import tab -->
    <div v-if="activeTab === 'url' && !previewData">
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-prussian-500 dark:text-lavender-200 mb-1">Recipe URL</label>
          <input
            v-model="importUrl"
            type="url"
            placeholder="https://example.com/recipe/..."
            class="input-base"
            @keydown.enter.prevent="handleImportUrl"
          />
        </div>

        <!-- Duplicate warning -->
        <div
          v-if="duplicateWarning"
          class="flex items-start gap-2 p-3 bg-[#C48B3F]/10 border border-[#C48B3F]/20 rounded-xl"
        >
          <ExclamationTriangleIcon class="w-5 h-5 text-[#C48B3F] flex-shrink-0 mt-0.5" />
          <div class="text-sm">
            <p class="font-medium text-[#C48B3F]">Duplicate detected</p>
            <p class="text-lavender-600 dark:text-lavender-400">
              You already have "{{ duplicateWarning.title }}" imported from this URL.
              You can still save it as a new recipe.
            </p>
          </div>
        </div>

        <!-- Error -->
        <div
          v-if="importError"
          class="flex items-start gap-2 p-3 bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-800/30 rounded-xl"
        >
          <ExclamationCircleIcon class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" />
          <div class="text-sm">
            <p class="font-medium text-red-600 dark:text-red-400">Import failed</p>
            <p class="text-lavender-600 dark:text-lavender-400">{{ importError }}</p>
          </div>
        </div>

        <p class="text-xs text-lavender-400 dark:text-lavender-500">
          Most recipe sites import for free. Pages without structured data use AI (counts toward API usage).
        </p>

        <div class="flex gap-3">
          <button
            class="px-6 py-2.5 text-sm font-medium text-white bg-[#C4975A] hover:bg-[#D4A96A] rounded-[10px] transition-colors disabled:opacity-50"
            :disabled="!importUrl || importing"
            @click="handleImportUrl"
          >
            {{ importing ? 'Importing...' : 'Preview Recipe' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Photo import tab -->
    <div v-if="activeTab === 'photo' && !previewData">
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-prussian-500 dark:text-lavender-200 mb-1">Recipe Photo</label>
          <div
            class="border-2 border-dashed border-lavender-300 dark:border-prussian-600 rounded-xl p-8 text-center hover:border-[#C4975A] transition-colors cursor-pointer"
            @click="$refs.photoInput.click()"
          >
            <CameraIcon class="w-8 h-8 mx-auto text-lavender-400 dark:text-lavender-500 mb-2" />
            <p class="text-sm text-lavender-500 dark:text-lavender-400">
              {{ selectedFile ? selectedFile.name : 'Click to upload a photo of a recipe' }}
            </p>
            <p class="text-xs text-lavender-400 dark:text-lavender-500 mt-1">JPEG, PNG, or HEIC · Max 10 MB · Uses AI (counts toward API usage)</p>
          </div>
          <input
            ref="photoInput"
            type="file"
            accept="image/jpeg,image/png,image/heic,image/heif"
            class="hidden"
            @change="handleFileSelect"
          />
        </div>

        <!-- Error -->
        <div
          v-if="importError"
          class="flex items-start gap-2 p-3 bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-800/30 rounded-xl"
        >
          <ExclamationCircleIcon class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" />
          <div class="text-sm">
            <p class="font-medium text-red-600 dark:text-red-400">Import failed</p>
            <p class="text-lavender-600 dark:text-lavender-400">{{ importError }}</p>
          </div>
        </div>

        <div class="flex gap-3">
          <button
            class="px-6 py-2.5 text-sm font-medium text-white bg-[#C4975A] hover:bg-[#D4A96A] rounded-[10px] transition-colors disabled:opacity-50"
            :disabled="!selectedFile || importing"
            @click="handleImportPhoto"
          >
            {{ importing ? 'Analyzing...' : 'Preview Recipe' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Loading overlay -->
    <div v-if="importing" class="flex flex-col items-center justify-center py-8">
      <LoadingSpinner size="lg" />
      <p class="mt-3 text-sm text-lavender-500 dark:text-lavender-400">
        {{ activeTab === 'url' ? 'Extracting recipe data...' : 'Analyzing photo with AI...' }}
      </p>
    </div>

    <!-- Preview form — editable before saving -->
    <div v-if="previewData && !importing">
      <div class="flex items-center gap-2 mb-4 p-3 bg-[#5B8C6A]/10 border border-[#5B8C6A]/20 rounded-xl">
        <CheckCircleIcon class="w-5 h-5 text-[#5B8C6A] flex-shrink-0" />
        <p class="text-sm text-[#5B8C6A]">
          Recipe extracted! Review and edit below, then save.
        </p>
      </div>
      <RecipeForm
        ref="importFormRef"
        :initial-data="previewData"
        @save="handleSaveImported"
        @cancel="resetPreview"
      />
    </div>
  </BaseModal>
</template>

<script setup>
import { ref, watch } from 'vue'
import { useRecipesStore } from '@/stores/recipes'
import { useNotification } from '@/composables/useNotification'
import BaseModal from '@/components/common/BaseModal.vue'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import RecipeForm from './RecipeForm.vue'
import {
  CameraIcon,
  ExclamationTriangleIcon,
  ExclamationCircleIcon,
  CheckCircleIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  show: { type: Boolean, default: false },
  initialTab: { type: String, default: 'url' },
})

const emit = defineEmits(['saved', 'close'])

const recipesStore = useRecipesStore()
const { error: notifyError } = useNotification()

const activeTab = ref(props.initialTab)
const importUrl = ref('')
const selectedFile = ref(null)
const importing = ref(false)
const importError = ref(null)
const previewData = ref(null)
const duplicateWarning = ref(null)
const importFormRef = ref(null)

watch(() => props.initialTab, (tab) => {
  activeTab.value = tab
})

const handleImportUrl = async () => {
  if (!importUrl.value) return
  importing.value = true
  importError.value = null
  duplicateWarning.value = null

  const result = await recipesStore.importFromUrl(importUrl.value)
  importing.value = false

  if (result.success) {
    const data = result.preview
    // Check for duplicate
    if (data.duplicate_of) {
      duplicateWarning.value = { id: data.duplicate_of, title: data.duplicate_title }
    }
    // Add the source URL to the preview data
    previewData.value = { ...data, source_url: importUrl.value, source_type: 'url' }
  } else {
    importError.value = result.error
  }
}

const handleFileSelect = (event) => {
  const file = event.target.files?.[0]
  if (file) {
    selectedFile.value = file
    importError.value = null
  }
}

const handleImportPhoto = async () => {
  if (!selectedFile.value) return
  importing.value = true
  importError.value = null

  const result = await recipesStore.importFromPhoto(selectedFile.value)
  importing.value = false

  if (result.success) {
    previewData.value = { ...result.preview, source_type: 'photo' }
  } else {
    importError.value = result.error
  }
}

const handleSaveImported = async (formData) => {
  const result = await recipesStore.createRecipe(formData)
  if (result.success) {
    emit('saved')
  } else {
    notifyError(result.error)
    if (importFormRef.value) importFormRef.value.saving = false
  }
}

const resetPreview = () => {
  previewData.value = null
  duplicateWarning.value = null
}

const handleClose = () => {
  importUrl.value = ''
  selectedFile.value = null
  importing.value = false
  importError.value = null
  previewData.value = null
  duplicateWarning.value = null
  emit('close')
}
</script>
