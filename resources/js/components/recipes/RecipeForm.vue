<template>
  <form class="space-y-6" @submit.prevent="handleSubmit">
    <!-- Basic info -->
    <div class="space-y-4">
      <!-- Title -->
      <div>
        <label class="block text-sm font-medium text-prussian-500 dark:text-lavender-200 mb-1">Title <span class="text-red-500">*</span></label>
        <input
          v-model="form.title"
          type="text"
          required
          placeholder="Recipe name"
          class="input-base"
        />
      </div>

      <!-- Description -->
      <div>
        <label class="block text-sm font-medium text-prussian-500 dark:text-lavender-200 mb-1">Description</label>
        <textarea
          v-model="form.description"
          rows="2"
          placeholder="Brief description"
          class="input-base resize-none"
        ></textarea>
      </div>

      <!-- Row: Servings / Prep / Cook -->
      <div class="grid grid-cols-3 gap-3">
        <div>
          <label class="block text-xs font-medium text-prussian-500 dark:text-lavender-200 mb-1">Servings</label>
          <input
            v-model.number="form.servings"
            type="number"
            min="1"
            max="100"
            placeholder="4"
            class="input-base"
          />
        </div>
        <div>
          <label class="block text-xs font-medium text-prussian-500 dark:text-lavender-200 mb-1">Prep (min)</label>
          <input
            v-model.number="form.prep_time_minutes"
            type="number"
            min="0"
            placeholder="0"
            class="input-base"
          />
        </div>
        <div>
          <label class="block text-xs font-medium text-prussian-500 dark:text-lavender-200 mb-1">Cook (min)</label>
          <input
            v-model.number="form.cook_time_minutes"
            type="number"
            min="0"
            placeholder="0"
            class="input-base"
          />
        </div>
      </div>

      <!-- Source URL (readonly if imported) -->
      <div v-if="form.source_url">
        <label class="block text-sm font-medium text-prussian-500 dark:text-lavender-200 mb-1">Source</label>
        <input
          v-model="form.source_url"
          type="url"
          readonly
          class="input-base bg-lavender-100 dark:bg-prussian-800 cursor-not-allowed"
        />
      </div>

      <!-- Photo -->
      <div>
        <label class="block text-sm font-medium text-prussian-500 dark:text-lavender-200 mb-1">Photo</label>
        <div
          class="relative border-2 border-dashed border-lavender-300 dark:border-prussian-600 rounded-xl overflow-hidden cursor-pointer hover:border-[#C4975A] transition-colors"
          :class="imageDisplayUrl ? 'h-40' : 'p-6'"
          @click="$refs.imageInput.click()"
        >
          <img
            v-if="imageDisplayUrl"
            :src="imageDisplayUrl"
            class="w-full h-full object-cover"
            alt="Recipe photo"
          />
          <div v-else class="flex flex-col items-center gap-1 text-center">
            <CameraIcon class="w-7 h-7 text-lavender-400 dark:text-lavender-500" />
            <p class="text-sm text-lavender-500 dark:text-lavender-400">
              {{ uploadingImage ? 'Uploading...' : 'Click to add a photo' }}
            </p>
          </div>
          <!-- Replace overlay -->
          <div
            v-if="imageDisplayUrl"
            class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity"
          >
            <span class="text-white text-sm font-medium">Change photo</span>
          </div>
        </div>
        <input
          ref="imageInput"
          type="file"
          accept="image/jpeg,image/png,image/webp,image/heic"
          class="hidden"
          @change="handleImageSelect"
        />
      </div>
    </div>

    <!-- Ingredients -->
    <div>
      <div class="flex items-center justify-between mb-2">
        <label class="text-sm font-semibold text-prussian-500 dark:text-lavender-200">Ingredients</label>
        <button
          type="button"
          class="text-xs font-medium text-[#C4975A] hover:text-[#D4A96A] transition-colors"
          @click="addIngredient"
        >
          + Add ingredient
        </button>
      </div>
      <div class="space-y-2">
        <div
          v-for="(ing, index) in form.ingredients"
          :key="index"
          class="flex items-start gap-2 p-3 bg-lavender-50 dark:bg-prussian-700/50 rounded-xl"
        >
          <div class="flex-1 grid grid-cols-12 gap-2">
            <!-- Quantity -->
            <input
              v-model="ing.quantity"
              type="text"
              placeholder="Qty"
              class="input-base col-span-2 text-center"
            />
            <!-- Unit -->
            <input
              v-model="ing.unit"
              type="text"
              placeholder="Unit"
              class="input-base col-span-2"
            />
            <!-- Name -->
            <input
              v-model="ing.name"
              type="text"
              placeholder="Ingredient name"
              required
              class="input-base col-span-5"
            />
            <!-- Preparation -->
            <input
              v-model="ing.preparation"
              type="text"
              placeholder="Prep"
              class="input-base col-span-3"
            />
          </div>
          <!-- Remove -->
          <button
            type="button"
            class="p-1.5 text-lavender-400 hover:text-red-500 transition-colors flex-shrink-0 mt-1"
            aria-label="Remove ingredient"
            @click="removeIngredient(index)"
          >
            <XMarkIcon class="w-4 h-4" />
          </button>
        </div>
      </div>
      <button
        v-if="form.ingredients.length === 0"
        type="button"
        class="w-full mt-2 py-3 text-sm text-lavender-400 dark:text-lavender-500 border border-dashed border-lavender-300 dark:border-prussian-600 rounded-xl hover:border-[#C4975A] hover:text-[#C4975A] transition-colors"
        @click="addIngredient"
      >
        + Add your first ingredient
      </button>
    </div>

    <!-- Instructions -->
    <div>
      <div class="flex items-center justify-between mb-2">
        <label class="text-sm font-semibold text-prussian-500 dark:text-lavender-200">Instructions</label>
        <button
          type="button"
          class="text-xs font-medium text-[#C4975A] hover:text-[#D4A96A] transition-colors"
          @click="addInstruction"
        >
          + Add step
        </button>
      </div>
      <div class="space-y-2">
        <div
          v-for="(step, index) in form.instructions"
          :key="index"
          class="flex items-start gap-2"
        >
          <!-- Step number -->
          <span class="w-7 h-7 rounded-full bg-[#C4975A]/10 text-[#C4975A] text-xs font-semibold flex items-center justify-center flex-shrink-0 mt-1">
            {{ index + 1 }}
          </span>
          <!-- Step text -->
          <textarea
            v-model="form.instructions[index]"
            rows="2"
            placeholder="Describe this step..."
            class="input-base flex-1 resize-none"
          ></textarea>
          <!-- Remove -->
          <button
            type="button"
            class="p-1.5 text-lavender-400 hover:text-red-500 transition-colors flex-shrink-0 mt-1"
            aria-label="Remove step"
            @click="removeInstruction(index)"
          >
            <XMarkIcon class="w-4 h-4" />
          </button>
        </div>
      </div>
      <button
        v-if="form.instructions.length === 0"
        type="button"
        class="w-full mt-2 py-3 text-sm text-lavender-400 dark:text-lavender-500 border border-dashed border-lavender-300 dark:border-prussian-600 rounded-xl hover:border-[#C4975A] hover:text-[#C4975A] transition-colors"
        @click="addInstruction"
      >
        + Add your first step
      </button>
    </div>

    <!-- Tags -->
    <div v-if="allTags.length > 0">
      <label class="block text-sm font-semibold text-prussian-500 dark:text-lavender-200 mb-2">Tags</label>
      <div class="flex flex-wrap gap-2">
        <button
          v-for="tag in recipeTags"
          :key="tag.id"
          type="button"
          class="px-3 py-1.5 text-xs font-medium rounded-full transition-colors"
          :class="form.tag_ids.includes(tag.id)
            ? 'text-white'
            : 'bg-lavender-100 dark:bg-prussian-700 text-lavender-600 dark:text-lavender-400 hover:bg-lavender-200 dark:hover:bg-prussian-600'"
          :style="form.tag_ids.includes(tag.id) ? { backgroundColor: tag.color || '#C4975A' } : {}"
          @click="toggleTag(tag.id)"
        >
          {{ tag.name }}
        </button>
      </div>
    </div>

    <!-- Notes -->
    <div>
      <label class="block text-sm font-medium text-prussian-500 dark:text-lavender-200 mb-1">Notes</label>
      <textarea
        v-model="form.notes"
        rows="3"
        placeholder="Tips, variations, or personal notes..."
        class="input-base resize-none"
      ></textarea>
    </div>

    <!-- Actions -->
    <div class="flex gap-3 justify-end pt-2">
      <button
        type="button"
        class="px-4 py-2.5 text-sm font-medium text-prussian-500 dark:text-lavender-200 bg-lavender-100 dark:bg-prussian-700 hover:bg-lavender-200 dark:hover:bg-prussian-600 rounded-[10px] transition-colors"
        @click="$emit('cancel')"
      >
        Cancel
      </button>
      <button
        type="submit"
        class="px-6 py-2.5 text-sm font-medium text-white bg-[#C4975A] hover:bg-[#D4A96A] rounded-[10px] transition-colors disabled:opacity-50"
        :disabled="!form.title || saving"
      >
        {{ saving ? 'Saving...' : (recipe ? 'Update Recipe' : 'Save Recipe') }}
      </button>
    </div>
  </form>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { storeToRefs } from 'pinia'
import { useRecipesStore } from '@/stores/recipes'
import { XMarkIcon, CameraIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  recipe: { type: Object, default: null },
  initialData: { type: Object, default: null },
})

const emit = defineEmits(['save', 'cancel'])

const recipesStore = useRecipesStore()
const { tags: allTags } = storeToRefs(recipesStore)

// Show tags that have been used on a recipe, have no tasks (recipe-specific),
// or are already applied to this recipe (edit mode). Hides task-only tags.
const recipeTags = computed(() => {
  return allTags.value.filter(
    (t) => (t.recipes_count ?? 0) > 0 || (t.tasks_count ?? 0) === 0 || form.tag_ids.includes(t.id)
  )
})

const saving = ref(false)
const uploadingImage = ref(false)
const imageDisplayUrl = ref(null)

const createEmptyForm = () => ({
  title: '',
  description: '',
  servings: 4,
  prep_time_minutes: null,
  cook_time_minutes: null,
  source_url: '',
  source_type: 'manual',
  image_path: null,
  notes: '',
  ingredients: [],
  instructions: [],
  tag_ids: [],
})

const form = reactive(createEmptyForm())

const populateFromRecipe = (recipe) => {
  form.title = recipe.title || ''
  form.description = recipe.description || ''
  form.servings = recipe.servings || 4
  form.prep_time_minutes = recipe.prep_time_minutes || null
  form.cook_time_minutes = recipe.cook_time_minutes || null
  form.source_url = recipe.source_url || ''
  form.source_type = recipe.source_type || 'manual'
  form.image_path = recipe.image_path || null
  form.notes = recipe.notes || ''
  if (recipe.image_path) {
    imageDisplayUrl.value = `/storage/${recipe.image_path}`
  }
  form.ingredients = (recipe.ingredients || []).map((ing) => ({
    name: ing.name || '',
    quantity: ing.quantity?.toString() || '',
    unit: ing.unit || '',
    preparation: ing.preparation || '',
    group_name: ing.group_name || '',
    is_optional: ing.is_optional || false,
  }))
  form.instructions = (recipe.instructions || []).map((step) =>
    typeof step === 'string' ? step : (step.text || '')
  )
  form.tag_ids = (recipe.tags || []).map((t) => t.id)
}

const populateFromImportPreview = (data) => {
  form.title = data.title || ''
  form.description = data.description || ''
  form.servings = data.servings || 4
  form.prep_time_minutes = data.prep_time || data.prep_time_minutes || null
  form.cook_time_minutes = data.cook_time || data.cook_time_minutes || null
  form.source_url = data.source_url || ''
  form.source_type = data.source_type || 'url'
  form.image_path = data.image_path || null
  if (data.image_path) {
    imageDisplayUrl.value = `/storage/${data.image_path}`
  }
  form.ingredients = (data.ingredients || []).map((ing) => ({
    name: ing.name || '',
    quantity: ing.quantity?.toString() || '',
    unit: ing.unit || '',
    preparation: ing.preparation || '',
    group_name: '',
    is_optional: false,
  }))
  form.instructions = (data.instructions || []).map((step) =>
    typeof step === 'string' ? step : (step.text || '')
  )
  form.tag_ids = []
}

// ── Ingredient actions ──

const addIngredient = () => {
  form.ingredients.push({ name: '', quantity: '', unit: '', preparation: '', group_name: '', is_optional: false })
}

const removeIngredient = (index) => {
  form.ingredients.splice(index, 1)
}

// ── Instruction actions ──

const addInstruction = () => {
  form.instructions.push('')
}

const removeInstruction = (index) => {
  form.instructions.splice(index, 1)
}

// ── Image upload ──

const handleImageSelect = async (event) => {
  const file = event.target.files?.[0]
  if (!file) return

  // Show local preview immediately
  imageDisplayUrl.value = URL.createObjectURL(file)
  uploadingImage.value = true

  const result = await recipesStore.uploadImage(file)
  uploadingImage.value = false

  if (result.success) {
    form.image_path = result.imagePath
  } else {
    // Revert preview on failure
    imageDisplayUrl.value = form.image_path ? `/storage/${form.image_path}` : null
  }
}

// ── Tag toggle ──

const toggleTag = (tagId) => {
  const idx = form.tag_ids.indexOf(tagId)
  if (idx === -1) {
    form.tag_ids.push(tagId)
  } else {
    form.tag_ids.splice(idx, 1)
  }
}

// ── Submit ──

const handleSubmit = () => {
  if (!form.title) return
  saving.value = true

  // Build the payload matching the API schema
  const payload = {
    title: form.title,
    description: form.description || null,
    servings: form.servings || 4,
    prep_time_minutes: form.prep_time_minutes || null,
    cook_time_minutes: form.cook_time_minutes || null,
    source_url: form.source_url || null,
    source_type: form.source_type || 'manual',
    image_path: form.image_path || null,
    notes: form.notes || null,
    ingredients: form.ingredients
      .filter((ing) => ing.name.trim())
      .map((ing, idx) => ({
        name: ing.name.trim(),
        quantity: ing.quantity || null,
        unit: ing.unit || null,
        preparation: ing.preparation || null,
        group_name: ing.group_name || null,
        is_optional: ing.is_optional || false,
        sort_order: idx,
      })),
    instructions: form.instructions
      .filter((s) => s.trim())
      .map((text, idx) => ({ step: idx + 1, text: text.trim() })),
    tag_ids: form.tag_ids,
  }

  emit('save', payload)
  // Parent is responsible for resetting saving state
}

// Watch for external data changes
watch(() => props.initialData, (data) => {
  if (data) populateFromImportPreview(data)
}, { immediate: true })

onMounted(() => {
  if (props.recipe) {
    populateFromRecipe(props.recipe)
  } else if (props.initialData) {
    populateFromImportPreview(props.initialData)
  }

  // Ensure tags are loaded
  if (allTags.value.length === 0) {
    recipesStore.fetchTags()
  }
})

defineExpose({ saving })
</script>
