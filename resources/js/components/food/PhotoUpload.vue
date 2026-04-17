<template>
  <div>
    <label v-if="label" class="block text-sm font-medium text-prussian-500 dark:text-lavender-200 mb-1">{{ label }}</label>
    <div
      role="button"
      tabindex="0"
      :aria-label="displayUrl ? `Change ${label || 'photo'}` : `Upload ${label || 'photo'}`"
      class="relative border-2 border-dashed border-lavender-300 dark:border-prussian-600 rounded-xl overflow-hidden cursor-pointer hover:border-[#C4975A] focus:outline-none focus:ring-2 focus:ring-[#C4975A]/40 transition-colors"
      :class="displayUrl ? 'h-40' : 'p-6'"
      @click="$refs.input.click()"
      @keydown.enter.prevent="$refs.input.click()"
      @keydown.space.prevent="$refs.input.click()"
    >
      <img
        v-if="displayUrl"
        :src="displayUrl"
        :alt="label || 'Photo'"
        class="w-full h-full object-cover"
        @error="onImgError"
      />
      <div v-else class="flex flex-col items-center gap-1 text-center">
        <CameraIcon class="w-7 h-7 text-lavender-400 dark:text-lavender-500" />
        <p class="text-sm text-lavender-500 dark:text-lavender-400">
          {{ uploading ? 'Uploading...' : placeholder }}
        </p>
      </div>
      <!-- Replace overlay -->
      <div
        v-if="displayUrl"
        class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity"
      >
        <span class="text-white text-sm font-medium">Change photo</span>
      </div>
    </div>
    <input
      ref="input"
      type="file"
      accept="image/jpeg,image/png,image/webp,image/heic"
      class="hidden"
      @change="handleSelect"
    />
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { CameraIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  modelValue: { type: String, default: null }, // image URL or path
  label: { type: String, default: 'Photo' },
  placeholder: { type: String, default: 'Click to add a photo' },
  // Async function: (File) => Promise<{success: bool, url: string}>
  uploader: { type: Function, required: true },
})

const emit = defineEmits(['update:modelValue'])

const uploading = ref(false)
const localPreview = ref(null)
const imgFailed = ref(false)

// Display priority: local preview (during upload) → prop value → null
const displayUrl = computed(() => {
  if (localPreview.value) return localPreview.value
  if (!props.modelValue || imgFailed.value) return null
  return props.modelValue
})

// Reset img error flag when value changes
watch(() => props.modelValue, () => {
  imgFailed.value = false
})

const onImgError = () => {
  imgFailed.value = true
}

const handleSelect = async (event) => {
  const file = event.target.files?.[0]
  if (!file) return

  // Local preview immediately
  localPreview.value = URL.createObjectURL(file)
  uploading.value = true

  const result = await props.uploader(file)
  uploading.value = false
  localPreview.value = null

  if (result.success) {
    emit('update:modelValue', result.url)
  }
}
</script>
