<template>
  <div
    :class="[
      sizeClass,
      editMode ? 'relative group' : '',
    ]"
  >
    <!-- Full-width widgets (welcome, countdown) — no card wrapper, auto height -->
    <template v-if="isAutoHeight">
      <div :class="editMode ? 'ring-2 ring-dashed ring-accent-lavender-bold/60 rounded-card p-2' : ''">
        <div v-if="editMode" class="flex items-center justify-between mb-2">
          <div class="flex items-center gap-2">
            <button
              class="drag-handle cursor-grab active:cursor-grabbing p-1 rounded hover:bg-surface-sunken"
              aria-label="Drag to reorder widget"
            >
              <Bars3Icon class="w-4 h-4 text-ink-tertiary" />
            </button>
            <span class="text-xs font-medium text-ink-tertiary">{{ config.title || widgetName }}</span>
          </div>
          <div class="flex items-center gap-1">
            <SizeToggle :size="config.size" :supported-sizes="supportedSizes" @resize="$emit('resize', $event)" />
            <KinButton
              variant="ghost"
              size="sm"
              icon-only
              aria-label="Remove widget"
              @click.stop="$emit('remove')"
            >
              <XMarkIcon class="w-4 h-4" />
            </KinButton>
          </div>
        </div>

        <Suspense>
          <component :is="widgetComponent" :config="config" :edit-mode="editMode" />
          <template #fallback>
            <KinSkeleton shape="rect" height="48px" />
          </template>
        </Suspense>
      </div>
    </template>

    <!-- Regular widgets — KinFlatCard with dynamic height -->
    <template v-else>
      <KinFlatCard
        padding="sm"
        :class="[
          'flex flex-col',
          editMode ? 'ring-2 ring-dashed ring-accent-lavender-bold/60' : '',
        ]"
        :style="{ height: widgetHeight }"
      >
        <div v-if="editMode" class="flex items-center justify-between mb-2 -mt-1 flex-shrink-0">
          <button
            class="drag-handle cursor-grab active:cursor-grabbing p-1 rounded hover:bg-surface-sunken"
            aria-label="Drag to reorder widget"
          >
            <Bars3Icon class="w-4 h-4 text-ink-tertiary" />
          </button>
          <div class="flex items-center gap-1">
            <SizeToggle :size="config.size" :supported-sizes="supportedSizes" @resize="$emit('resize', $event)" />
            <KinButton
              variant="ghost"
              size="sm"
              icon-only
              aria-label="Remove widget"
              @click.stop="$emit('remove')"
            >
              <XMarkIcon class="w-4 h-4" />
            </KinButton>
          </div>
        </div>

        <div class="flex-1 min-h-0 overflow-hidden">
          <Suspense>
            <component :is="widgetComponent" :config="config" :edit-mode="editMode" />
            <template #fallback>
              <KinSkeleton shape="rect" height="80px" />
            </template>
          </Suspense>
        </div>
      </KinFlatCard>
    </template>
  </div>
</template>

<script setup>
import { computed, defineAsyncComponent } from 'vue'
import { getWidgetComponent, getWidgetHeight, getSupportedSizes, widgetTypes } from './widgetRegistry'
import KinFlatCard from '@/components/design-system/KinFlatCard.vue'
import KinButton from '@/components/design-system/KinButton.vue'
import KinSkeleton from '@/components/design-system/KinSkeleton.vue'
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
