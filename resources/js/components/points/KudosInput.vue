<template>
  <div class="flex items-center gap-2">
    <!--
      Recipient picker — circular avatar button that opens a modal sheet.
      Replaces the previous <KinSelect> dropdown which ate ~128px of horizontal
      space on mobile and forced the text input to truncate to "Kud" before the
      placeholder ran out of room.
    -->
    <button
      type="button"
      class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center transition-colors focus:outline-none focus:ring-2 focus:ring-accent-lavender-bold/40"
      :class="selectedMember
        ? 'bg-transparent'
        : 'bg-surface-sunken text-ink-tertiary hover:bg-surface-overlay border border-dashed border-border-subtle'"
      :aria-label="selectedMember ? `Recipient: ${selectedMember.name}. Tap to change` : 'Select recipient'"
      @click="openPicker"
    >
      <UserAvatar v-if="selectedMember" :user="selectedMember" size="sm" />
      <UserPlusIcon v-else class="w-5 h-5" />
    </button>

    <div class="flex-1 min-w-0">
      <KinInput
        ref="reasonInputRef"
        v-model="reason"
        type="text"
        :placeholder="selectedMember ? `Kudos for ${selectedMember.name.split(' ')[0]}…` : 'Pick a recipient first'"
        :disabled="!selectedMember"
        @keydown.enter="submit"
      />
    </div>

    <KinButton
      variant="primary"
      size="sm"
      :disabled="!selectedUser || !reason.trim()"
      class="flex-shrink-0"
      @click="submit"
    >
      Give
    </KinButton>
  </div>

  <!-- Recipient picker modal -->
  <Teleport to="body">
    <Transition name="kudos-picker-fade">
      <div
        v-if="showPicker"
        class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/40 backdrop-blur-sm"
        role="dialog"
        aria-modal="true"
        aria-label="Select kudos recipient"
        tabindex="-1"
        @click.self="closePicker"
        @keydown.esc.stop.prevent="closePicker"
      >
        <div
          class="w-full sm:max-w-sm bg-surface-raised rounded-t-2xl sm:rounded-2xl shadow-2xl overflow-hidden pb-safe-bottom"
          @click.stop
        >
          <div class="px-5 pt-5 pb-3 border-b border-border-subtle flex items-center justify-between">
            <h3 class="text-base font-semibold text-ink-primary">Give kudos to…</h3>
            <button
              ref="closeButtonRef"
              type="button"
              class="w-9 h-9 -mr-2 rounded-full flex items-center justify-center text-ink-tertiary hover:text-ink-primary hover:bg-surface-sunken transition-colors"
              aria-label="Close recipient picker"
              @click="closePicker"
            >
              <XMarkIcon class="w-5 h-5" />
            </button>
          </div>

          <div class="max-h-[60vh] overflow-y-auto py-1">
            <button
              v-for="member in otherMembers"
              :key="member.id"
              type="button"
              class="w-full flex items-center gap-3 px-5 py-3 hover:bg-surface-sunken transition-colors text-left"
              @click="selectMember(member)"
            >
              <UserAvatar :user="member" size="md" />
              <span class="flex-1 text-base font-medium text-ink-primary">{{ member.name }}</span>
              <CheckIcon v-if="selectedUser === member.id" class="w-5 h-5 text-accent-lavender-bold" />
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, computed, nextTick } from 'vue'
import { UserPlusIcon, XMarkIcon, CheckIcon } from '@heroicons/vue/24/outline'
import { useAuthStore } from '@/stores/auth'
import KinInput from '@/components/design-system/KinInput.vue'
import KinButton from '@/components/design-system/KinButton.vue'
import UserAvatar from '@/components/common/UserAvatar.vue'

const props = defineProps({
  members: {
    type: Array,
    default: () => [],
  },
})

const authStore = useAuthStore()

// Filter out the current user so they can't give kudos to themselves
const otherMembers = computed(() =>
  props.members.filter(m => m.id !== authStore.currentUser?.id)
)

const emit = defineEmits(['kudos'])

const selectedUser = ref('')
const reason = ref('')
const showPicker = ref(false)

// Refs for programmatic focus management. reasonInputRef points at the
// KinInput *component* (so we read .$el and reach into its native <input>);
// closeButtonRef points at the modal's close button so we can land focus
// inside the dialog when it opens (a11y: focus-trap entry).
const reasonInputRef = ref(null)
const closeButtonRef = ref(null)

const selectedMember = computed(() =>
  otherMembers.value.find(m => m.id === selectedUser.value) || null
)

const openPicker = () => {
  showPicker.value = true
  nextTick(() => {
    closeButtonRef.value?.focus()
  })
}

const closePicker = () => {
  showPicker.value = false
}

const focusReasonInput = () => {
  // KinInput is a wrapper component — reach its underlying <input>.
  const wrapper = reasonInputRef.value?.$el
  wrapper?.querySelector?.('input')?.focus()
}

const selectMember = (member) => {
  selectedUser.value = member.id
  showPicker.value = false
  // Auto-focus the reason input so the user can start typing immediately —
  // they came here to give kudos, the recipient was just step one.
  nextTick(focusReasonInput)
}

const submit = () => {
  if (!selectedUser.value || !reason.value.trim()) return
  emit('kudos', { userId: selectedUser.value, reason: reason.value.trim() })
  reason.value = ''
}
</script>

<style scoped>
.kudos-picker-fade-enter-active,
.kudos-picker-fade-leave-active {
  transition: opacity 180ms ease;
}
.kudos-picker-fade-enter-from,
.kudos-picker-fade-leave-to {
  opacity: 0;
}
.kudos-picker-fade-enter-active > div,
.kudos-picker-fade-leave-active > div {
  transition: transform 220ms cubic-bezier(0.32, 0.72, 0.24, 1);
}
.kudos-picker-fade-enter-from > div,
.kudos-picker-fade-leave-to > div {
  transform: translateY(20px);
}
</style>
