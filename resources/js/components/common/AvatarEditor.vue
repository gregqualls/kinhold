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
            class="bg-surface-raised rounded-t-card md:rounded-card w-full md:max-w-md max-h-[85vh] flex flex-col shadow-elevated"
            @click.stop
          >
            <!-- Header -->
            <div class="px-6 py-4 border-b border-border-subtle flex items-center justify-between">
              <h2 class="text-xl font-semibold text-ink-primary">Choose Avatar</h2>
              <button
                type="button"
                class="p-2 hover:bg-surface-sunken rounded-lg transition-colors text-ink-tertiary hover:text-ink-primary"
                aria-label="Close"
                @click="$emit('close')"
              >
                <XMarkIcon class="w-5 h-5" />
              </button>
            </div>

            <!-- Content -->
            <div class="flex-1 overflow-y-auto p-6">
              <!-- Preview — uses KinAvatar to match what the rest of the app
                   renders. `previewUser.avatar` is either a real URL, a
                   `phosphor:<key>` preset, or null (initials fallback). -->
              <div class="flex flex-col items-center mb-6">
                <KinAvatar
                  :name="previewUser?.name"
                  :src="previewUser?.avatar"
                  :color="kinAccent"
                  size="xl"
                />
                <p class="text-sm text-ink-secondary mt-2">{{ targetUser?.name }}</p>
              </div>

              <!-- Color Picker — Kin accent families only -->
              <div class="mb-6">
                <p class="text-xs font-medium text-ink-tertiary uppercase tracking-wider mb-2">Color</p>
                <div class="flex flex-wrap gap-3 justify-center">
                  <button
                    v-for="color in KIN_AVATAR_ACCENTS"
                    :key="color.name"
                    type="button"
                    :disabled="loading || !canChange"
                    :title="color.label"
                    class="w-9 h-9 rounded-full transition-all duration-150 disabled:opacity-50 disabled:cursor-not-allowed border-2 border-transparent"
                    :class="[
                      color.bg,
                      kinAccent === color.name
                        ? 'ring-2 ring-offset-2 ring-offset-surface-raised scale-110'
                        : 'hover:scale-110',
                    ]"
                    :style="kinAccent === color.name ? { '--tw-ring-color': color.hex } : {}"
                    @click="selectColor(color.name)"
                  ></button>
                </div>
              </div>

              <!-- Google Photo + Upload Photo -->
              <div class="mb-6 space-y-2">
                <!-- Use Google Photo (only shown if available and not already using it) -->
                <button
                  v-if="targetUser?.google_avatar && !isUsingGoogleAvatar"
                  type="button"
                  :disabled="loading || !canChange"
                  class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-surface-sunken border border-border-subtle rounded-xl text-sm font-medium text-ink-primary hover:border-accent-lavender-bold hover:text-accent-lavender-bold transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                  @click="useGoogleAvatar"
                >
                  <img :src="targetUser.google_avatar" class="w-5 h-5 rounded-full object-cover" alt="" />
                  Use Google Photo
                </button>
                <button
                  type="button"
                  :disabled="loading || !canChange"
                  class="w-full flex items-center justify-center gap-2 px-4 py-3 border-2 border-dashed border-border-subtle rounded-xl text-sm font-medium text-ink-secondary hover:border-accent-lavender-bold hover:text-accent-lavender-bold transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                  @click="triggerUpload"
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
                <p v-if="uploadError" class="text-xs text-status-failed mt-2 text-center">{{ uploadError }}</p>
              </div>

              <!-- Preset Grid -->
              <div v-for="(presets, category) in presetsByCategory" :key="category" class="mb-5">
                <p class="text-xs font-medium text-ink-tertiary uppercase tracking-wider mb-2">{{ category }}</p>
                <div class="grid grid-cols-5 gap-2">
                  <button
                    v-for="preset in presets"
                    :key="preset.key"
                    type="button"
                    :disabled="loading || !canChange"
                    :title="preset.label"
                    class="relative aspect-square rounded-full flex items-center justify-center transition-all duration-150 disabled:opacity-50 disabled:cursor-not-allowed"
                    :class="[
                      isSelected(preset.key)
                        ? 'ring-2 ring-offset-2 ring-offset-surface-raised'
                        : 'hover:ring-2 hover:ring-border-subtle hover:ring-offset-2 hover:ring-offset-surface-raised',
                      colorClasses,
                    ]"
                    :style="isSelected(preset.key) ? { '--tw-ring-color': KIN_AVATAR_ACCENTS.find(c => c.name === kinAccent)?.hex } : {}"
                    @click="selectPreset(preset.key)"
                  >
                    <component :is="preset.component" weight="duotone" :class="['w-1/2 h-1/2', presetIconColor]" />
                  </button>
                </div>
              </div>

              <!-- Remove Avatar -->
              <button
                v-if="targetUser?.avatar"
                :disabled="loading || !canChange"
                class="w-full text-center text-sm text-red-500 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 py-2 transition-colors disabled:opacity-50"
                @click="removeAvatar"
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
import KinAvatar from '@/components/design-system/KinAvatar.vue'
import { getPresetsByCategory } from '@/components/common/avatarPresets'
import { useFamilyColors, KIN_AVATAR_ACCENTS, kinAccentFor } from '@/composables/useFamilyColors'
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

const { getColorForUser } = useFamilyColors()

const loading = computed(() => uploading.value || removing.value)
const localColor = ref(null)

const presetsByCategory = getPresetsByCategory()
// Bold accent text class — drives icon color on the preset tiles so the
// icon contrasts against its soft-tinted background.
const presetIconColor = computed(() => ({
  lavender: 'text-accent-lavender-bold',
  peach:    'text-accent-peach-bold',
  mint:     'text-accent-mint-bold',
  sun:      'text-accent-sun-bold',
}[kinAccent.value] ?? 'text-accent-lavender-bold'))

const selectedColor = computed(() =>
  localColor.value || props.targetUser?.avatar_color || null
)

// Resolved Kin accent family for the currently-previewed color. Drives both
// the preview KinAvatar tint and the preset-grid background tint.
const kinAccent = computed(() => kinAccentFor(selectedColor.value))

const colorClasses = computed(() => {
  // Used by the preset grid to tint the icon background. Map the Kin accent
  // family to its `-soft` token class so each preset tile carries the
  // selected family's wash.
  return KIN_AVATAR_ACCENTS.find((c) => c.name === kinAccent.value)?.bg ?? 'bg-accent-lavender-soft'
})

const previewUser = computed(() => ({
  ...props.targetUser,
  avatar: localAvatar.value !== null ? localAvatar.value : props.targetUser?.avatar,
  avatar_color: selectedColor.value,
}))

const isUsingGoogleAvatar = computed(() => {
  const current = previewUser.value?.avatar
  return current === props.targetUser?.google_avatar
})

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
    if (props.targetUser?.id) formData.append('user_id', props.targetUser.id)

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
    const payload = { preset: presetKey }
    if (props.targetUser?.id) payload.user_id = props.targetUser.id
    const { data } = await api.put('/user/avatar/preset', payload)
    emit('updated', data.user.avatar)
  } catch {
    localAvatar.value = null
  }
}

const removeAvatar = async () => {
  removing.value = true
  try {
    const params = props.targetUser?.id ? { data: { user_id: props.targetUser.id } } : {}
    await api.delete('/user/avatar', params)
    localAvatar.value = null
    emit('updated', null)
  } catch {
    // Avatar removal failed — silently ignore
  } finally {
    removing.value = false
  }
}

const useGoogleAvatar = async () => {
  const googleUrl = props.targetUser?.google_avatar
  if (!googleUrl) return

  localAvatar.value = googleUrl
  try {
    const payload = props.targetUser?.id ? { user_id: props.targetUser.id } : {}
    const { data } = await api.post('/user/avatar/google', payload)
    emit('updated', data.user.avatar)
  } catch {
    localAvatar.value = null
  }
}

const selectColor = async (colorName) => {
  localColor.value = colorName
  try {
    const payload = { avatar_color: colorName }
    if (props.targetUser?.id) payload.user_id = props.targetUser.id
    await api.patch('/user', payload)
    emit('color-changed')
  } catch {
    localColor.value = null
  }
}
</script>
