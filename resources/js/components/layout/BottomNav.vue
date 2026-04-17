<template>
  <nav
    ref="navRef"
    class="md:hidden bg-prussian-500 dark:bg-prussian-900 border-t border-prussian-400/30 dark:border-prussian-700/50 flex justify-around shrink-0 relative"
    :style="{ paddingBottom: 'max(0px, env(safe-area-inset-bottom))' }"
  >
    <template v-for="(slot, idx) in slots" :key="slot.label">
      <!-- Single-item slot -->
      <RouterLink
        v-if="!slot.children"
        :to="slot.path"
        class="flex flex-col items-center justify-center py-2.5 px-2 flex-1 transition-colors"
        :class="isActive(slot.path) ? 'text-wisteria-400' : 'text-lavender-400'"
      >
        <component
          :is="isActive(slot.path) ? slot.iconSolid : slot.icon"
          class="w-6 h-6"
        />
        <span class="text-[10px] mt-0.5 font-medium">{{ slot.label }}</span>
      </RouterLink>

      <!-- Grouped slot (popover above) -->
      <div v-else class="flex-1 relative flex">
        <button
          type="button"
          class="flex flex-col items-center justify-center py-2.5 px-2 flex-1 transition-colors w-full"
          :class="(slotActive(slot) || openSlotIdx === idx) ? 'text-wisteria-400' : 'text-lavender-400'"
          :aria-expanded="openSlotIdx === idx"
          :aria-label="slot.label"
          @click="toggleSlot(idx)"
        >
          <component
            :is="(slotActive(slot) || openSlotIdx === idx) ? slot.iconSolid : slot.icon"
            class="w-6 h-6"
          />
          <span class="text-[10px] mt-0.5 font-medium flex items-center gap-0.5">
            {{ slot.label }}
            <ChevronUpIcon
              class="w-2.5 h-2.5 transition-transform"
              :class="openSlotIdx === idx ? '' : 'rotate-180 opacity-50'"
            />
          </span>
        </button>

        <Transition name="group-pop">
          <div
            v-if="openSlotIdx === idx"
            class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 z-50 min-w-[160px]"
          >
            <div
              class="bg-prussian-400 dark:bg-prussian-800 border border-prussian-300/40 dark:border-prussian-700/60 rounded-2xl shadow-xl overflow-hidden backdrop-blur-md"
            >
              <RouterLink
                v-for="child in slot.children"
                :key="child.name"
                :to="child.path"
                class="flex items-center gap-3 px-4 py-3 transition-colors"
                :class="isActive(child.path)
                  ? 'bg-wisteria-500/15 text-wisteria-300'
                  : 'text-lavender-200 hover:bg-prussian-500/40 dark:hover:bg-prussian-700/50'"
                @click="openSlotIdx = null"
              >
                <component
                  :is="isActive(child.path) ? child.iconSolid : child.icon"
                  class="w-5 h-5 flex-shrink-0"
                />
                <span class="text-sm font-medium">{{ child.label }}</span>
              </RouterLink>
            </div>
            <!-- Anchor caret -->
            <div class="absolute -bottom-1.5 left-1/2 -translate-x-1/2 w-3 h-3 rotate-45 bg-prussian-400 dark:bg-prussian-800 border-r border-b border-prussian-300/40 dark:border-prussian-700/60"></div>
          </div>
        </Transition>
      </div>
    </template>
  </nav>

  <!-- Backdrop to dismiss popover (under the nav, above the page) -->
  <Teleport to="body">
    <Transition name="group-fade">
      <div
        v-if="openSlotIdx !== null"
        class="fixed inset-0 z-30 md:hidden"
        @click="openSlotIdx = null"
      />
    </Transition>
  </Teleport>
</template>

<script setup>
import { computed, ref, watch, onMounted, onBeforeUnmount } from 'vue'
import { useRoute } from 'vue-router'
import { storeToRefs } from 'pinia'
import { useAuthStore } from '@/stores/auth'
import {
  HomeIcon,
  CalendarIcon,
  CheckCircleIcon,
  ClipboardDocumentListIcon,
  FireIcon,
  CpuChipIcon,
  TrophyIcon,
  ChevronUpIcon,
  CalendarDaysIcon,
} from '@heroicons/vue/24/outline'
import {
  HomeIcon as HomeIconSolid,
  CalendarIcon as CalendarIconSolid,
  CheckCircleIcon as CheckCircleIconSolid,
  ClipboardDocumentListIcon as ClipboardDocumentListIconSolid,
  FireIcon as FireIconSolid,
  CpuChipIcon as CpuChipIconSolid,
  TrophyIcon as TrophyIconSolid,
  CalendarDaysIcon as CalendarDaysIconSolid,
} from '@heroicons/vue/24/solid'
import { PhForkKnife } from '@phosphor-icons/vue'
import { h } from 'vue'

// Heroicons don't include fork-and-knife; wrap the Phosphor icon so it matches
// the heroicon component shape (props-driven class).
const ForkKnifeOutline = (props, { attrs }) => h(PhForkKnife, { weight: 'regular', ...attrs })
const ForkKnifeSolid = (props, { attrs }) => h(PhForkKnife, { weight: 'fill', ...attrs })

const route = useRoute()
const authStore = useAuthStore()
const { enabledModules } = storeToRefs(authStore)

// Nav structure. Items with `children` render as a tappable group whose
// children appear in a popover above the bar. Re-order or reshape as needed.
const navStructure = [
  {
    label: 'Home',
    path: '/dashboard',
    icon: HomeIcon,
    iconSolid: HomeIconSolid,
    name: 'Dashboard',
    module: null,
  },
  {
    label: 'Schedule',
    icon: CalendarDaysIcon,
    iconSolid: CalendarDaysIconSolid,
    name: 'Schedule',
    children: [
      { label: 'Calendar', path: '/calendar', icon: CalendarIcon, iconSolid: CalendarIconSolid, name: 'Calendar', module: 'calendar' },
      { label: 'Tasks',    path: '/tasks',    icon: CheckCircleIcon, iconSolid: CheckCircleIconSolid, name: 'Tasks', module: 'tasks' },
    ],
  },
  {
    label: 'Meals',
    icon: ForkKnifeOutline,
    iconSolid: ForkKnifeSolid,
    name: 'Kitchen',
    children: [
      { label: 'Meals',    path: '/food',     icon: FireIcon, iconSolid: FireIconSolid, name: 'Food', module: 'food' },
      { label: 'Shopping', path: '/shopping', icon: ClipboardDocumentListIcon, iconSolid: ClipboardDocumentListIconSolid, name: 'Shopping', module: 'food' },
    ],
  },
  {
    label: 'Points',
    path: '/points',
    icon: TrophyIcon,
    iconSolid: TrophyIconSolid,
    name: 'Points',
    module: 'points',
  },
  {
    label: 'Assistant',
    path: '/chat',
    icon: CpuChipIcon,
    iconSolid: CpuChipIconSolid,
    name: 'Chat',
    module: 'chat',
  },
]

const moduleEnabled = (mod) => !mod || enabledModules.value[mod] !== false

// Resolve modules: drop disabled children; collapse single-child groups; drop
// empty groups entirely.
const slots = computed(() =>
  navStructure
    .map(item => {
      if (!item.children) return moduleEnabled(item.module) ? item : null
      const visible = item.children.filter(c => moduleEnabled(c.module))
      if (visible.length === 0) return null
      if (visible.length === 1) return visible[0]
      return { ...item, children: visible }
    })
    .filter(Boolean)
)

const isActive = (path) => {
  if (!path) return false
  if (path === '/') return route.path === '/'
  return route.path.startsWith(path)
}

const slotActive = (slot) => slot.children?.some(c => isActive(c.path))

// Popover state — index of the open group, or null.
const openSlotIdx = ref(null)
const navRef = ref(null)

const toggleSlot = (idx) => {
  openSlotIdx.value = openSlotIdx.value === idx ? null : idx
}

// Close on route change.
watch(() => route.path, () => {
  openSlotIdx.value = null
})

// Close on Escape for keyboard users.
const onKeydown = (e) => {
  if (e.key === 'Escape' && openSlotIdx.value !== null) {
    openSlotIdx.value = null
  }
}
onMounted(() => document.addEventListener('keydown', onKeydown))
onBeforeUnmount(() => document.removeEventListener('keydown', onKeydown))
</script>

<style scoped>
.group-pop-enter-active,
.group-pop-leave-active {
  transition: opacity 150ms ease, transform 150ms cubic-bezier(0.32, 0.72, 0.24, 1);
}
.group-pop-enter-from,
.group-pop-leave-to {
  opacity: 0;
  transform: translate(-50%, 4px);
}

.group-fade-enter-active,
.group-fade-leave-active {
  transition: opacity 120ms ease;
}
.group-fade-enter-from,
.group-fade-leave-to {
  opacity: 0;
}
</style>
