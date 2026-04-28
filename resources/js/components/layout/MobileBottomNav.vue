<template>
  <div>
    <Teleport to="body">
      <Transition name="group-fade">
        <div v-if="openGroup" class="fixed inset-0 z-20 md:hidden" @click="openGroup = null"></div>
      </Transition>
    </Teleport>

    <Transition name="group-pop">
      <div v-if="openGroup" class="absolute bottom-full left-0 right-0 mb-3 z-40 flex justify-center pointer-events-none">
        <div class="pointer-events-auto bg-surface-raised border border-border-subtle rounded-2xl shadow-xl overflow-hidden min-w-[200px]">
          <RouterLink
            v-for="child in openGroup.children"
            :key="child.key"
            :to="child.to"
            class="flex items-center gap-3 px-4 py-3 text-sm text-ink-primary hover:bg-surface-sunken"
            @click="openGroup = null"
          >
            <component :is="child.icon" class="w-5 h-5" />
            <span>{{ child.label }}</span>
          </RouterLink>
        </div>
      </div>
    </Transition>

    <KinBottomNav :items="slotItems" :active-id="activeId" @item-click="onItemClick">
      <template #fab>
        <RouterLink
          :to="aiReady ? '/chat' : '/dashboard'"
          :aria-label="aiReady ? 'Ask Assistant' : 'Home'"
          class="mobile-fab w-[50px] h-[50px] rounded-full flex items-center justify-center transition-transform hover:-translate-y-[2px]"
        >
          <SparklesIcon v-if="aiReady" class="mobile-fab__icon w-6 h-6" />
          <HomeSolidIcon v-else class="mobile-fab__icon w-6 h-6" />
        </RouterLink>
      </template>
    </KinBottomNav>

    <MoreSheet v-model="moreOpen" :exclude-keys="moreExcludeKeys" />
  </div>
</template>

<script setup>
import { computed, ref, watch, onMounted, onBeforeUnmount, h } from 'vue'
import { useRoute } from 'vue-router'
import { storeToRefs } from 'pinia'
import { useAuthStore } from '@/stores/auth'
import KinBottomNav from '@/components/design-system/KinBottomNav.vue'
import MoreSheet from '@/components/layout/MoreSheet.vue'
import {
  HomeIcon,
  CalendarDaysIcon,
  EllipsisHorizontalIcon,
  TrophyIcon,
} from '@heroicons/vue/24/outline'
import {
  CalendarIcon,
  CheckCircleIcon,
  FireIcon,
  ClipboardDocumentListIcon,
  HomeIcon as HomeSolidIcon,
  SparklesIcon,
} from '@heroicons/vue/24/solid'
import { PhForkKnife } from '@phosphor-icons/vue'

const ForkKnife = (_, { attrs }) => h(PhForkKnife, { weight: 'regular', ...attrs })

const HOME_SLOT = {
  id: 'home',
  label: 'Home',
  icon: HomeIcon,
  to: '/dashboard',
}

const POINTS_SLOT = {
  id: 'points',
  label: 'Points',
  icon: TrophyIcon,
  to: '/points',
  module: 'points',
}

const SLOTS = [
  HOME_SLOT,
  {
    id: 'schedule',
    label: 'Schedule',
    icon: CalendarDaysIcon,
    children: [
      { key: 'calendar', label: 'Calendar', icon: CalendarIcon,    to: '/calendar', module: 'calendar' },
      { key: 'tasks',    label: 'Tasks',    icon: CheckCircleIcon, to: '/tasks',    module: 'tasks' },
    ],
  },
  {
    id: 'meals',
    label: 'Meals',
    icon: ForkKnife,
    children: [
      { key: 'food',     label: 'Food',     icon: FireIcon,                  to: '/food',     module: 'food' },
      { key: 'shopping', label: 'Shopping', icon: ClipboardDocumentListIcon, to: '/shopping', module: 'food' },
    ],
  },
  {
    id: 'more',
    label: 'More',
    icon: EllipsisHorizontalIcon,
  },
]

const FILL_PRIORITY = [
  { id: 'points',   label: 'Points',   icon: null, to: '/points',   module: 'points' },
  { id: 'vault',    label: 'Vault',    icon: null, to: '/vault',    module: 'vault' },
  { id: 'settings', label: 'Settings', icon: null, to: '/settings', module: null },
]

const route = useRoute()
const authStore = useAuthStore()
const { enabledModules, aiReady } = storeToRefs(authStore)

const openGroup = ref(null)
const moreOpen = ref(false)

const moduleEnabled = (mod) => !mod || enabledModules.value[mod] !== false

const slotItems = computed(() => {
  const base = SLOTS.map((slot) => {
    if (slot.id !== 'home') return slot
    if (aiReady.value) return slot
    if (moduleEnabled(POINTS_SLOT.module)) return POINTS_SLOT
    return slot
  })

  const resolved = base.map((slot) => {
    if (!slot.children) return slot

    const visible = slot.children.filter((c) => moduleEnabled(c.module))
    if (visible.length === 0) return null
    if (visible.length === 1) return { id: slot.id, label: visible[0].label, icon: visible[0].icon, to: visible[0].to }
    return { ...slot, children: visible }
  }).filter(Boolean)

  if (resolved.length < 4) {
    for (const fill of FILL_PRIORITY) {
      if (resolved.length >= 4) break
      if (!moduleEnabled(fill.module)) continue
      if (resolved.some((s) => s.id === fill.id)) continue
      resolved.splice(resolved.length - 1, 0, fill)
    }
  }

  return resolved.slice(0, 4)
})

const moreExcludeKeys = computed(() =>
  slotItems.value.map((s) => s.id)
)

const activeId = computed(() => {
  const candidates = []
  for (const slot of slotItems.value) {
    if (slot.to && route.path.startsWith(slot.to)) {
      candidates.push({ id: slot.id, len: slot.to.length })
    }
    if (slot.children) {
      for (const child of slot.children) {
        if (route.path.startsWith(child.to)) {
          candidates.push({ id: slot.id, len: child.to.length })
        }
      }
    }
  }
  if (!candidates.length) return null
  return candidates.sort((a, b) => b.len - a.len)[0].id
})

const onItemClick = (id) => {
  if (id === 'more') {
    moreOpen.value = true
    openGroup.value = null
    return
  }
  const slot = slotItems.value.find((s) => s.id === id)
  if (slot?.children) {
    openGroup.value = openGroup.value?.id === id ? null : slot
  } else {
    openGroup.value = null
  }
}

watch(() => route.path, () => {
  openGroup.value = null
})

const onKeydown = (e) => {
  if (e.key === 'Escape') openGroup.value = null
}
onMounted(() => document.addEventListener('keydown', onKeydown))
onBeforeUnmount(() => document.removeEventListener('keydown', onKeydown))
</script>

<style scoped>
/*
  FAB — dark gradient pill matching the design-system demo at
  /design-system/bottom-nav. Hex values are bespoke to this surface
  (warm-charcoal gradient + warm-white icon) and don't have token
  equivalents in tokens.css. Mirrors BottomNavPage.vue's example FAB.
*/
.mobile-fab {
  background-image: linear-gradient(180deg, #313130 0%, #1C1C1E 100%);
  box-shadow:
    0 4px 8px rgba(28, 20, 10, 0.12),
    0 16px 32px rgba(28, 20, 10, 0.18);
}

.mobile-fab__icon {
  color: #FAF8F5;
}

.group-pop-enter-active,
.group-pop-leave-active {
  transition: opacity 150ms ease, transform 150ms cubic-bezier(0.32, 0.72, 0.24, 1);
}
.group-pop-enter-from,
.group-pop-leave-to {
  opacity: 0;
  transform: translateY(4px);
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
