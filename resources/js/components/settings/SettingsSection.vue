<template>
  <div :id="id" class="bg-surface-raised border border-border-subtle rounded-xl shadow-sm mb-6">
    <!-- Header (always visible) -->
    <button
      type="button"
      class="w-full flex items-center justify-between px-5 py-4 md:px-6 text-left cursor-pointer select-none rounded-xl hover:bg-surface-sunken dark:hover:bg-surface-raised/80 transition-colors"
      @click="toggleOpen"
    >
      <div class="flex items-center gap-3 min-w-0">
        <component
          :is="icon"
          v-if="icon"
          class="w-5 h-5 text-accent-lavender-bold flex-shrink-0"
        />
        <div class="min-w-0">
          <div class="flex items-center gap-2">
            <h2 class="text-lg font-semibold font-heading text-ink-primary truncate">{{ title }}</h2>
            <span
              v-if="badge"
              class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-accent-lavender-soft/40 text-accent-lavender-bold dark:bg-accent-lavender-soft/40 dark:text-accent-lavender-bold whitespace-nowrap"
            >
              {{ badge }}
            </span>
          </div>
          <p v-if="description" class="text-sm text-ink-secondary mt-0.5">{{ description }}</p>
        </div>
      </div>

      <div class="flex items-center gap-2 flex-shrink-0 ml-4">
        <slot name="header-actions"></slot>
        <ChevronDownIcon
          :class="[
            'w-5 h-5 text-ink-tertiary transition-transform duration-200',
            isOpen ? 'rotate-180' : '',
          ]"
        />
      </div>
    </button>

    <!-- Body (collapsible) -->
    <div v-show="isOpen" class="px-5 pb-5 md:px-6 md:pb-6">
      <slot></slot>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import { ChevronDownIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  id: {
    type: String,
    required: true,
  },
  title: {
    type: String,
    required: true,
  },
  description: {
    type: String,
    default: '',
  },
  icon: {
    type: [Object, Function],
    default: null,
  },
  badge: {
    type: String,
    default: '',
  },
  defaultOpen: {
    type: Boolean,
    default: false,
  },
  modelValue: {
    type: Boolean,
    default: undefined,
  },
})

const emit = defineEmits(['update:modelValue'])

// Internal state (used when modelValue is not provided)
const internalOpen = ref(props.defaultOpen)

// Controlled vs uncontrolled
const isControlled = computed(() => props.modelValue !== undefined)
const isOpen = computed(() => isControlled.value ? props.modelValue : internalOpen.value)

const toggleOpen = () => {
  const newValue = !isOpen.value
  if (isControlled.value) {
    emit('update:modelValue', newValue)
  } else {
    internalOpen.value = newValue
  }
}

// Hash deep-linking
onMounted(async () => {
  const hash = window.location.hash.slice(1)
  if (hash === props.id) {
    if (isControlled.value) {
      emit('update:modelValue', true)
    } else {
      internalOpen.value = true
    }
    await nextTick()
    document.getElementById(props.id)?.scrollIntoView({ behavior: 'smooth', block: 'start' })
  }
})
</script>
