<template>
  <Teleport to="body">
    <Transition name="fade">
      <div
        v-if="show"
        class="fixed inset-0 bg-black/50 z-[60] flex items-center justify-center p-4"
        @click="$emit('cancel')"
      >
        <Transition name="scale">
          <div
            v-if="show"
            class="bg-white dark:bg-prussian-800 rounded-[12px] w-full max-w-sm shadow-xl"
            @click.stop
          >
            <!-- Icon -->
            <div class="pt-6 px-6 flex justify-center">
              <div
                class="w-12 h-12 rounded-full flex items-center justify-center"
                :class="iconBgClass"
              >
                <ExclamationTriangleIcon v-if="variant === 'danger'" class="w-6 h-6 text-red-600" />
                <InformationCircleIcon v-else class="w-6 h-6 text-wisteria-600" />
              </div>
            </div>

            <!-- Content -->
            <div class="px-6 pt-4 pb-2 text-center">
              <h3 class="text-lg font-semibold text-prussian-500 dark:text-lavender-200 mb-1">{{ title }}</h3>
              <p class="text-sm text-lavender-600 dark:text-lavender-400">{{ message }}</p>
            </div>

            <!-- Actions -->
            <div class="px-6 pb-6 pt-4 flex gap-3">
              <button
                @click="$emit('cancel')"
                class="flex-1 px-4 py-2.5 text-sm font-medium text-prussian-500 dark:text-lavender-200 bg-lavender-100 dark:bg-prussian-700 hover:bg-lavender-200 dark:hover:bg-prussian-600 rounded-[10px] transition-colors"
              >
                {{ cancelText }}
              </button>
              <button
                @click="$emit('confirm')"
                class="flex-1 px-4 py-2.5 text-sm font-medium text-white rounded-[10px] transition-colors"
                :class="confirmBtnClass"
                :disabled="loading"
              >
                <span v-if="loading" class="flex items-center justify-center gap-2">
                  <svg class="animate-spin w-4 h-4" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" />
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                  </svg>
                  Working...
                </span>
                <span v-else>{{ confirmText }}</span>
              </button>
            </div>
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { computed } from 'vue'
import { ExclamationTriangleIcon, InformationCircleIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  show: Boolean,
  title: { type: String, default: 'Are you sure?' },
  message: { type: String, default: 'This action cannot be undone.' },
  confirmText: { type: String, default: 'Confirm' },
  cancelText: { type: String, default: 'Cancel' },
  variant: { type: String, default: 'danger', validator: (v) => ['danger', 'info'].includes(v) },
  loading: Boolean,
})

defineEmits(['confirm', 'cancel'])

const iconBgClass = computed(() =>
  props.variant === 'danger' ? 'bg-red-100 dark:bg-red-900/30' : 'bg-wisteria-100 dark:bg-wisteria-900/30'
)

const confirmBtnClass = computed(() =>
  props.variant === 'danger'
    ? 'bg-red-600 hover:bg-red-700'
    : 'bg-wisteria-600 hover:bg-wisteria-500'
)
</script>
