<template>
  <div
    :class="[
      sizeClass,
      editMode ? 'relative group' : '',
    ]"
  >
    <!-- Full-width widgets (welcome, countdown) — no card wrapper, auto height -->
    <template v-if="isAutoHeight">
      <div :class="editMode ? 'ring-2 ring-dashed ring-wisteria-300 dark:ring-wisteria-600 rounded-card p-2' : ''">
        <div v-if="editMode" class="flex items-center justify-between mb-2">
          <div class="flex items-center gap-2">
            <button
              class="drag-handle cursor-grab active:cursor-grabbing p-1 rounded hover:bg-lavender-100 dark:hover:bg-prussian-700"
              aria-label="Drag to reorder widget"
            >
              <Bars3Icon class="w-4 h-4 text-lavender-500" />
            </button>
            <span class="text-xs font-medium text-lavender-500">{{ config.title || widgetName }}</span>
          </div>
          <div class="flex items-center gap-1">
            <SizeToggle :size="config.size" :supported-sizes="supportedSizes" @resize="$emit('resize', $event)" />
            <button
              class="p-1 rounded hover:bg-red-100 dark:hover:bg-red-900/30 text-lavender-400 hover:text-red-500 transition-colors"
              aria-label="Remove widget"
              @click.stop="$emit('remove')"
            >
              <XMarkIcon class="w-4 h-4" />
            </button>
          </div>
        </div>

        <Suspense>
          <component :is="widgetComponent" :config="config" :edit-mode="editMode" />
          <template #fallback>
            <div class="h-12 bg-lavender-100 dark:bg-prussian-700 rounded animate-pulse"></div>
          </template>
        </Suspense>
      </div>
    </template>

    <!-- Regular widgets — BaseCard with dynamic height -->
    <template v-else>
      <BaseCard
        shadow="lg"
        :class="[
          'flex flex-col',
          editMode ? 'ring-2 ring-dashed ring-wisteria-300 dark:ring-wisteria-600' : '',
        ]"
        :style="{ height: widgetHeight }"
      >
        <div v-if="editMode" class="flex items-center justify-between mb-2 -mt-1 flex-shrink-0">
          <button
            class="drag-handle cursor-grab active:cursor-grabbing p-1 rounded hover:bg-lavender-100 dark:hover:bg-prussian-700"
            aria-label="Drag to reorder widget"
          >
            <Bars3Icon class="w-4 h-4 text-lavender-500" />
          </button>
          <div class="flex items-center gap-1">
            <SizeToggle :size="config.size" :supported-sizes="supportedSizes" @resize="$emit('resize', $event)" />
            <button
              class="p-1 rounded hover:bg-red-100 dark:hover:bg-red-900/30 text-lavender-400 hover:text-red-500 transition-colors"
              aria-label="Remove widget"
              @click.stop="$emit('remove')"
            >
              <XMarkIcon class="w-4 h-4" />
            </button>
          </div>
        </div>

        <div class="flex-1 min-h-0 overflow-hidden">
          <Suspense>
            <component :is="widgetComponent" :config="config" :edit-mode="editMode" />
            <template #fallback>
              <div class="h-20 bg-lavender-100 dark:bg-prussian-700 rounded animate-pulse"></div>
            </template>
          </Suspense>
        </div>
      </BaseCard>
    </template>
  </div>
</template>

<script setup>
import { computed, defineAsyncComponent } from 'vue'
import { getWidgetComponent, getWidgetHeight, getSupportedSizes, widgetTypes } from './widgetRegistry'
import BaseCard from '@/components/common/BaseCard.vue'
import SizeToggle from './SizeToggle.vue'
import { Bars3Icon, XMarkIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  config: { type: Object, required: true },
  editMode: { type: Boolean, default: false },
  index: { type: Number, required: true },
})

defineEmits(['remove', 'resize'])

const widgetComponent = computed(() => {
  const loader = getWidgetComponent(props.config.type)
  if (!loader) return null
  return defineAsyncComponent(loader)
})

const widgetName = computed(() => widgetTypes[props.config.type]?.name || props.config.type)

const isAutoHeight = computed(() => {
  const height = getWidgetHeight(props.config.type, props.config.size)
  return height === null
})

const widgetHeight = computed(() => {
  return getWidgetHeight(props.config.type, props.config.size) || '280px'
})

const supportedSizes = computed(() => getSupportedSizes(props.config.type))

const sizeClass = computed(() => {
  const sizes = {
    sm: 'col-span-1',
    md: 'col-span-1 md:col-span-2',
    lg: 'col-span-1 md:col-span-2 lg:col-span-3',
  }
  return sizes[props.config.size] || 'col-span-1'
})
</script>
