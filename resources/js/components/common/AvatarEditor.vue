<template>
  <Teleport to="body">
    <Transition name="fade">
      <div
        v-if="show"
        class="fixed inset-0 bg-black/50 z-50 flex items-end md:items-center justify-center p-4"
        @click="$emit('close')"
      >
        <Transition name="slide">
          <div
            v-if="show"
            class="bg-white dark:bg-prussian-800 rounded-t-[12px] md:rounded-[12px] w-full md:max-w-md max-h-[85vh] flex flex-col"
            @click.stop
          >
            <!-- Header -->
            <div class="px-6 py-4 border-b border-lavender-200 dark:border-prussian-700 flex items-center justify-between">
              <h2 class="text-xl font-semibold text-prussian-500 dark:text-lavender-200">Choose Avatar</h2>
              <button
                @click="$emit('close')"
                class="p-2 hover:bg-lavender-100 dark:hover:bg-prussian-700 rounded-lg transition-colors text-lavender-500 dark:text-lavender-400"
                aria-label="Close"
              >
                <XMarkIcon class="w-5 h-5" />
              </button>
            </div>

            <!-- Content -->
            <div class="flex-1 overflow-y-auto p-6">
              <!-- Preview -->
              <div class="flex flex-col items-center mb-6">
                <UserAvatar :user="previewUser" size="xl" />
                <p class="text-sm text-lavender-600 dark:text-lavender-400 mt-2">{{ targetUser?.name }}</p>
              </div>

              <!-- Color Picker -->
              <div class="mb-6">
                <p class="text-xs font-medium text-lavender-600 dark:text-lavender-400 uppercase tracking-wider mb-2">Color</p>
                <div class="flex flex-wrap gap-2 justify-center">
                  <button
                    v-for="color in allColors"
                    :key="color.name"
                    @click="selectColor(color.name)"
                    :disabled="loading || !canChange"
                    :title="color.name"
                    class="w-8 h-8 rounded-full transition-all duration-150 disabled:opacity-50 disabled:cursor-not-allowed"
                    :class="[
                      color.bg,
                      selectedColor === color.name
                        ? 'ring-2 ring-[#C4975A] ring-offset-2 dark:ring-offset-prussian-800 scale-110'
                        : 'hover:scale-110',
                    ]"
                  />
                </div>
              </div>

              <!-- Upload Photo -->
              <div class="mb-6">
                <button
                  @click="triggerUpload"
                  :disabled="loading || !canChange"
                  class="w-full flex items-center justify-center gap-2 px-4 py-3 border-2 border-dashed border-lavender-300 dark:border-prussian-600 rounded-xl text-sm font-medium text-prussian-400 dark:text-lavender-300 hover:border-[#C4975A] hover:text-[#C4975A] dark:hover:border-[#C4975A] dark:hover:text-[#C4975A] transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <CameraIcon class="w-5 h-5" />
                  {{ uploading ? 'Uploading...' : 'Upload Photo' }}
                </button>
                <input
                  ref="fileInput"
                  type="file"
                  accept="image/jpeg,image/png,image/gif,image/webp"
                  class="hidden"
                  @change="handleUpload"
                />
                <p v-if="uploadError" class="text-xs text-red-500 dark:text-red-400 mt-2 text-center">{{ uploadError }}</p>
              </div>

              <!-- Preset Grid -->
              <div v-for="(presets, category) in presetsByCategory" :key="category" class="mb-5">
                <p class="text-xs font-medium text-lavender-600 dark:text-lavender-400 uppercase tracking-wider mb-2">{{ category }}</p>
                <div class="grid grid-cols-5 gap-2">
                  <button
                    v-for="preset in presets"
                    :key="preset.key"
                    @click="selectPreset(preset.key)"
                    :disabled="loading || !canChange"
                    :title="preset.label"
                    class="relative aspect-square rounded-full flex items-center justify-center transition-all duration-150 disabled:opacity-50 disabled:cursor-not-allowed"
                    :class="[
                      isSelected(preset.key)
                        ? 'ring-2 ring-[#C4975A] ring-offset-2 dark:ring-offset-prussian-800'
                        : 'hover:ring-2 hover:ring-lavender-300 dark:hover:ring-prussian-500 hover:ring-offset-2 dark:hover:ring-offset-prussian-800',
                      colorClasses,
                    ]"
                  >
                    <component :is="preset.component" weight="duotone" class="text-white w-1/2 h-1/2" />
                  </button>
                </div>
              </div>

              <!-- Remove Avatar -->
              <button
                v-if="targetUser?.avatar"
                @click="removeAvatar"
                :disabled="loading || !canChange"
                class="w-full text-center text-sm text-red-500 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 py-2 transition-colors disabled:opacity-50"
              >
                {{ removing ? 'Removing...' : 'Remove Avatar' }}
              </button>
            </div>
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { XMarkIcon, CameraIcon } from '@heroicons/vue/24/outline'
import UserAvatar from '@/components/common/UserAvatar.vue'
import { getPresetsByCategory } from '@/components/common/avatarPresets'
import { useFamilyColors } from '@/composables/useFamilyColors'
import api from '@/services/api'

const props = defineProps({
  show: Boolean,
  targetUser: {
    type: Object,
    default: null,
  },
  canChange: {
    type: Boolean,
    default: true,
  },
})

const emit = defineEmits(['close', 'updated', 'color-changed'])

const fileInput = ref(null)
const uploading = ref(false)
const removing = ref(false)
const localAvatar = ref(null)
const uploadError = ref(null)

const { getColorForUser, getAllColors } = useFamilyColors()

const loading = computed(() => uploading.value || removing.value)
const localColor = ref(null)

const presetsByCategory = getPresetsByCategory()
const allColors = getAllColors()

const selectedColor = computed(() =>
  localColor.value || props.targetUser?.avatar_color || null
)

const colorClasses = computed(() => {
  const color = getColorForUser(props.targetUser?.id, props.targetUser?.name, selectedColor.value)
  return color.bg
})

const previewUser = computed(() => ({
  ...props.targetUser,
  avatar: localAvatar.value !== null ? localAvatar.value : props.targetUser?.avatar,
  avatar_color: selectedColor.value,
}))

const isSelected = (presetKey) => {
  const avatar = previewUser.value?.avatar
  return avatar === `phosphor:${presetKey}`
}

// Reset local state when modal opens
watch(() => props.show, (val) => {
  if (val) {
    localAvatar.value = null
    localColor.value = null
  }
  if (val) {
    document.addEventListener('keydown', handleEscape)
  } else {
    document.removeEventListener('keydown', handleEscape)
  }
})

const handleEscape = (e) => {
  if (e.key === 'Escape') emit('close')
}

const triggerUpload = () => {
  fileInput.value?.click()
}

const MAX_FILE_SIZE = 5 * 1024 * 1024 // 5MB

const handleUpload = async (event) => {
  const file = event.target.files?.[0]
  if (!file) return

  uploadError.value = null

  if (file.size > MAX_FILE_SIZE) {
    uploadError.value = 'Image must be under 5MB. Try a smaller photo.'
    if (fileInput.value) fileInput.value.value = ''
    return
  }

  uploading.value = true
  try {
    const formData = new FormData()
    formData.append('avatar', file)

    const { data } = await api.post('/user/avatar', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })

    localAvatar.value = data.user.avatar
    emit('updated', data.user.avatar)
  } catch (err) {
    if (err.response?.status === 413) {
      uploadError.value = 'Image too large. Try a smaller photo.'
    } else {
      uploadError.value = 'Upload failed. Try again.'
    }
  } finally {
    uploading.value = false
    if (fileInput.value) fileInput.value.value = ''
  }
}

const selectPreset = async (presetKey) => {
  localAvatar.value = `phosphor:${presetKey}`
  try {
    const { data } = await api.put('/user/avatar/preset', { preset: presetKey })
    emit('updated', data.user.avatar)
  } catch (err) {
    console.error('Preset avatar failed:', err)
    localAvatar.value = null
  }
}

const removeAvatar = async () => {
  removing.value = true
  try {
    const { data } = await api.delete('/user/avatar')
    localAvatar.value = null
    emit('updated', null)
  } catch (err) {
    console.error('Avatar removal failed:', err)
  } finally {
    removing.value = false
  }
}

const selectColor = async (colorName) => {
  localColor.value = colorName
  try {
    await api.patch('/user', { avatar_color: colorName })
    emit('color-changed')
  } catch (err) {
    console.error('Color change failed:', err)
    localColor.value = null
  }
}
</script>
