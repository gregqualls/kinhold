<template>
  <div :id="id" class="bg-surface-raised border border-border-subtle rounded-xl shadow-sm mb-6">
    <!-- Collapsible header (standalone accordion mode). -->
    <button
      v-if="collapsible"
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
            <KinChip
              v-if="badge"
              variant="category"
              color="lavender"
              size="sm"
              :interactive="false"
            >
              {{ badge }}
            </KinChip>
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

    <!-- Static header (master-detail mode: no chevron, no toggle). -->
    <div
      v-else
      class="flex items-center justify-between px-5 py-4 md:px-6 border-b border-border-subtle"
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
            <KinChip
              v-if="badge"
              variant="category"
              color="lavender"
              size="sm"
              :interactive="false"
            >
              {{ badge }}
            </KinChip>
          </div>
          <p v-if="description" class="text-sm text-ink-secondary mt-0.5">{{ description }}</p>
        </div>
      </div>

      <div class="flex items-center gap-2 flex-shrink-0 ml-4">
        <slot name="header-actions"></slot>
      </div>
    </div>

    <!-- Body: v-show when collapsible, always visible otherwise. -->
    <div v-show="collapsible ? isOpen : true" class="px-5 py-5 md:px-6 md:py-6">
      <slot></slot>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import { ChevronDownIcon } from '@heroicons/vue/24/outline'
import KinChip from '@/components/design-system/KinChip.vue'

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
  // Set false in master-detail mode: hides the chevron, removes click-to-toggle,
  // and the body is always visible (modelValue is ignored).
  collapsible: {
    type: Boolean,
    default: true,
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

// Hash deep-linking (accordion mode only; master-detail parents own hash routing).
onMounted(async () => {
  if (!props.collapsible) return
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
