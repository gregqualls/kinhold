<template>
  <div ref="menuRef" class="relative">
    <!-- Trigger -->
    <button
      class="p-1.5 rounded-lg text-ink-tertiary hover:text-ink-primary hover:bg-surface-sunken transition-colors"
      :class="triggerClass"
      aria-label="More actions"
      @click.stop="toggle"
    >
      <slot name="trigger">
        <EllipsisVerticalIcon class="w-5 h-5" />
      </slot>
    </button>

    <!-- Menu -->
    <Teleport to="body">
      <Transition name="menu">
        <div
          v-if="isOpen"
          class="fixed z-[70]"
          :style="menuPosition"
        >
          <!-- Backdrop -->
          <div class="fixed inset-0" @click="close"></div>

          <!-- Dropdown -->
          <div class="relative bg-surface-raised rounded-[12px] shadow-xl border border-border-subtle py-1 min-w-[180px] overflow-hidden">
            <template v-for="(item, index) in items" :key="index">
              <!-- Divider -->
              <div v-if="item.divider" class="my-1 border-t border-border-subtle"></div>

              <!-- Menu Item -->
              <button
                v-else
                class="w-full flex items-center gap-3 px-4 py-2.5 text-sm transition-colors"
                :class="[
                  item.variant === 'danger'
                    ? 'text-status-failed hover:bg-status-failed/10'
                    : 'text-ink-primary hover:bg-surface-sunken',
                  item.disabled && 'opacity-40 cursor-not-allowed',
                ]"
                :disabled="item.disabled"
                @click="handleAction(item)"
              >
                <component
                  :is="item.icon"
                  v-if="item.icon"
                  class="w-4 h-4 flex-shrink-0"
                />
                <span class="flex-1 text-left">{{ item.label }}</span>
                <span v-if="item.shortcut" class="text-xs text-ink-tertiary">{{ item.shortcut }}</span>
              </button>
            </template>
          </div>
        </div>
      </Transition>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, nextTick, onUnmounted } from 'vue'
import { EllipsisVerticalIcon } from '@heroicons/vue/24/outline'

defineProps({
  items: {
    type: Array,
    required: true,
    // Each item: { label, icon?, action?, variant?, divider?, disabled?, shortcut? }
  },
  triggerClass: String,
})

const emit = defineEmits(['action'])

const menuRef = ref(null)
const isOpen = ref(false)
const menuPosition = ref({})

const toggle = async () => {
  if (isOpen.value) {
    close()
    return
  }

  isOpen.value = true
  await nextTick()
  positionMenu()
}

const close = () => {
  isOpen.value = false
}

const positionMenu = () => {
  if (!menuRef.value) return
  const trigger = menuRef.value.querySelector('button')
  if (!trigger) return

  const rect = trigger.getBoundingClientRect()
  const menuWidth = 180
  const menuHeight = 200

  let left = rect.right - menuWidth
  let top = rect.bottom + 4

  // Keep in viewport
  if (left < 8) left = 8
  if (left + menuWidth > window.innerWidth - 8) left = window.innerWidth - menuWidth - 8
  if (top + menuHeight > window.innerHeight - 8) top = rect.top - menuHeight - 4

  menuPosition.value = {
    left: `${left}px`,
    top: `${top}px`,
  }
}

const handleAction = (item) => {
  if (item.disabled) return
  close()
  if (item.action) {
    item.action()
  }
  emit('action', item)
}

onUnmounted(() => {
  close()
})
</script>

<style scoped>
.menu-enter-active {
  transition: opacity 0.15s ease, transform 0.15s ease;
}
.menu-leave-active {
  transition: opacity 0.1s ease, transform 0.1s ease;
}
.menu-enter-from,
.menu-leave-to {
  opacity: 0;
  transform: scale(0.95) translateY(-4px);
}
</style>
