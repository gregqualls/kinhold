<template>
  <div class="p-4 md:p-6 max-w-4xl">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6 gap-3 flex-wrap">
      <h1 class="text-2xl font-bold font-heading text-ink-primary">Achievements</h1>
      <KinButton v-if="isParent" variant="primary" size="sm" @click="showCreateForm = !showCreateForm">
        <template v-if="!showCreateForm" #leading>
          <PlusIcon class="w-4 h-4" />
        </template>
        {{ showCreateForm ? 'Cancel' : 'Create Badge' }}
      </KinButton>
    </div>

    <!-- Create Form (parent only) -->
    <KinFlatCard v-if="showCreateForm" padding="md" class="mb-6">
      <h3 class="text-sm font-semibold text-ink-primary mb-4">New Badge</h3>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <KinInput v-model="newBadge.name" label="Name" placeholder="Badge name" />
        <KinInput v-model="newBadge.description" label="Description" placeholder="What earns this badge?" />

        <KinSelect
          v-model="newBadge.trigger_type"
          label="Trigger Type"
          :options="triggerTypeOptions"
        />

        <KinInput
          v-if="newBadge.trigger_type !== 'custom'"
          v-model.number="newBadge.trigger_threshold"
          label="Threshold"
          type="number"
          min="1"
          placeholder="e.g. 10"
        />

        <div>
          <label class="block text-[13px] font-medium text-ink-secondary mb-2">Icon</label>
          <div class="flex flex-wrap gap-2">
            <button
              v-for="iconName in iconNames"
              :key="iconName"
              type="button"
              class="p-1.5 rounded-lg border transition-colors"
              :class="newBadge.icon === iconName
                ? 'border-accent-lavender-bold bg-accent-lavender-soft/30'
                : 'border-border-subtle hover:border-accent-lavender-bold/60'"
              @click="newBadge.icon = iconName"
            >
              <BadgeIcon :icon="iconName" :color="newBadge.color" size="sm" />
            </button>
          </div>
        </div>

        <div class="sm:col-span-2">
          <label class="block text-[13px] font-medium text-ink-secondary mb-2">Color</label>
          <div class="flex flex-wrap gap-2">
            <button
              v-for="c in badgeColorPalette"
              :key="c.value"
              type="button"
              :title="c.label"
              :aria-label="c.label"
              class="w-8 h-8 rounded-full border-2 transition-all duration-150 flex-shrink-0"
              :class="newBadge.color === c.value
                ? 'ring-2 ring-offset-2 ring-offset-surface-raised scale-110'
                : 'border-transparent hover:scale-110'"
              :style="{
                backgroundColor: c.value,
                borderColor: newBadge.color === c.value ? c.value : 'transparent',
                '--tw-ring-color': c.value,
              }"
              @click="newBadge.color = c.value"
            ></button>
          </div>
        </div>

        <div class="flex items-center gap-2 sm:col-span-2">
          <KinCheckbox v-model="newBadge.is_hidden" />
          <label class="text-sm text-ink-primary cursor-pointer" @click="newBadge.is_hidden = !newBadge.is_hidden">
            Hidden badge (surprise!)
          </label>
        </div>
      </div>

      <div class="flex justify-end mt-5">
        <KinButton
          variant="primary"
          size="sm"
          :disabled="!newBadge.name || !newBadge.description"
          @click="createBadge"
        >
          Create Badge
        </KinButton>
      </div>
    </KinFlatCard>

    <!-- Tabs -->
    <KinTabPillGroup
      v-model:active-key="activeTab"
      :tabs="tabOptions"
      variant="tinted"
      size="sm"
      class="mb-6"
    />

    <!-- Achievements Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-x-4 gap-y-6">
      <KinAchievementTile
        v-for="badge in filteredBadges"
        :key="badge.id"
        :state="stateFor(badge)"
        :icon="iconComponentFor(badge.icon)"
        :title="badge.name"
        :description="badge.description"
        :meta="metaFor(badge)"
        :accent-color="accentColorFor(badge.color)"
        :progress="progressFor(badge)"
      />
    </div>

    <!-- Empty state -->
    <KinEmptyState
      v-if="filteredBadges.length === 0 && !badgesStore.isLoading"
      :icon="ShieldCheckIcon"
      :title="emptyTitle"
      :description="emptyDescription"
      accent-color="lavender"
      size="md"
      class="mt-8"
    />

    <!-- Parent Award Section -->
    <KinFlatCard v-if="isParent && activeTab === 'All'" padding="md" class="mt-8">
      <h3 class="text-sm font-semibold text-ink-primary mb-4">Manually Award Badge</h3>
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
        <KinSelect
          v-model="awardBadgeId"
          label="Badge"
          placeholder="Select badge…"
          :options="awardableBadgeOptions"
        />
        <KinSelect
          v-model="awardUserId"
          label="Member"
          placeholder="Select member…"
          :options="memberOptions"
        />
        <KinButton
          variant="primary"
          size="md"
          :disabled="!awardBadgeId || !awardUserId"
          @click="handleAward"
        >
          Award
        </KinButton>
      </div>
    </KinFlatCard>
  </div>
</template>

<script setup>
import { computed, onMounted, ref, h } from 'vue'
import { storeToRefs } from 'pinia'
import { useBadgesStore } from '@/stores/badges'
import { useAuthStore } from '@/stores/auth'
import BadgeIcon from '@/components/badges/BadgeIcon.vue'
import { badgeIconNames, badgeIconPaths } from '@/components/badges/badgeIcons'
import KinButton from '@/components/design-system/KinButton.vue'
import KinInput from '@/components/design-system/KinInput.vue'
import KinSelect from '@/components/design-system/KinSelect.vue'
import KinCheckbox from '@/components/design-system/KinCheckbox.vue'
import KinFlatCard from '@/components/design-system/KinFlatCard.vue'
import KinTabPillGroup from '@/components/design-system/KinTabPillGroup.vue'
import KinAchievementTile from '@/components/design-system/KinAchievementTile.vue'
import KinEmptyState from '@/components/design-system/KinEmptyState.vue'
import { PlusIcon, ShieldCheckIcon } from '@heroicons/vue/24/outline'

const badgesStore = useBadgesStore()
const authStore = useAuthStore()
const { isParent, familyMembers } = storeToRefs(authStore)

const activeTab = ref('All')
const showCreateForm = ref(false)
const awardBadgeId = ref('')
const awardUserId = ref('')

const iconNames = badgeIconNames

const tabOptions = [
  { key: 'All', label: 'All' },
  { key: 'Earned', label: 'Earned' },
  { key: 'Locked', label: 'Locked' },
]

const triggerTypeOptions = [
  { value: 'custom',            label: 'Custom (Manual Award)' },
  { value: 'tasks_completed',   label: 'Tasks Completed' },
  { value: 'points_earned',     label: 'Points Earned' },
  { value: 'task_streak',       label: 'Task Streak (Days)' },
  { value: 'kudos_received',    label: 'Kudos Received' },
  { value: 'kudos_given',       label: 'Kudos Given' },
  { value: 'rewards_purchased', label: 'Rewards Purchased' },
  { value: 'login_streak',      label: 'Login Streak' },
]

const badgeColorPalette = [
  { value: '#7d57a8', label: 'Wisteria' },
  { value: '#9b59b6', label: 'Amethyst' },
  { value: '#8e44ad', label: 'Purple' },
  { value: '#3498db', label: 'Blue' },
  { value: '#1c3d5a', label: 'Prussian Blue' },
  { value: '#2ecc71', label: 'Emerald' },
  { value: '#27ae60', label: 'Green' },
  { value: '#1abc9c', label: 'Turquoise' },
  { value: '#e74c3c', label: 'Red' },
  { value: '#e67e22', label: 'Orange' },
  { value: '#d4a23a', label: 'Golden Sand' },
  { value: '#f1c40f', label: 'Yellow' },
  { value: '#e91e63', label: 'Pink' },
  { value: '#00bcd4', label: 'Cyan' },
  { value: '#607d8b', label: 'Slate' },
  { value: '#34495e', label: 'Dark Slate' },
]

const newBadge = ref({
  name: '',
  description: '',
  icon: 'trophy',
  color: '#7d57a8',
  trigger_type: 'custom',
  trigger_threshold: null,
  is_hidden: false,
})

const filteredBadges = computed(() => {
  const badges = badgesStore.badges
  if (activeTab.value === 'Earned') return badges.filter(b => b.is_earned)
  if (activeTab.value === 'Locked') return badges.filter(b => !b.is_earned)
  return badges
})

const awardableBadgeOptions = computed(() =>
  badgesStore.badges
    .filter((b) => b.name !== '???')
    .map((b) => ({ value: b.id, label: b.name }))
)

const memberOptions = computed(() =>
  (familyMembers.value || []).map((m) => ({ value: m.id, label: m.name }))
)

const emptyTitle = computed(() => {
  if (activeTab.value === 'Earned') return 'No badges earned yet'
  if (activeTab.value === 'Locked') return 'All badges unlocked!'
  return 'No badges available'
})

const emptyDescription = computed(() => {
  if (activeTab.value === 'Earned') return 'Keep at it — your first achievement is waiting.'
  if (activeTab.value === 'Locked') return 'Nice work — you\'ve earned every available badge.'
  return ''
})

// ── KinAchievementTile adapters ─────────────────────────────────────────────

const stateFor = (badge) => {
  if (badge.is_earned) return 'earned'
  if (badge.name === '???' || badge.is_hidden) return 'hidden'
  if (badge.trigger_threshold && (badge.progress || 0) > 0) return 'in-progress'
  return 'locked'
}

const progressFor = (badge) => {
  if (!badge.trigger_threshold) return 0
  return Math.max(0, Math.min(1, (badge.progress || 0) / badge.trigger_threshold))
}

const metaFor = (badge) => {
  if (badge.is_earned) return badge.earned_at_label || 'Earned'
  if (badge.trigger_threshold && badge.progress != null) {
    return `${badge.progress} / ${badge.trigger_threshold}`
  }
  return ''
}

// Map a hex color to one of the four Kin accent families. Hue-based bucketing.
const accentColorFor = (hex) => {
  if (!hex) return 'lavender'
  const r = parseInt(hex.slice(1, 3), 16)
  const g = parseInt(hex.slice(3, 5), 16)
  const b = parseInt(hex.slice(5, 7), 16)
  // Crude family detection — fall back to lavender for cool / unknown.
  if (r > g && r > b && Math.abs(g - b) < 60) return 'peach'   // red / pink / orange
  if (g > r && g > b) return 'mint'                              // green / turquoise
  if (r > 200 && g > 150 && b < 120) return 'sun'                // yellow / gold / sand
  return 'lavender'                                              // purple / blue / slate
}

// Memoised functional icon components — KinAchievementTile expects a component
// reference. We render just the inner SVG path; the tile applies the hex shell
// and color via currentColor.
const iconComponentCache = new Map()
const iconComponentFor = (iconName) => {
  if (iconComponentCache.has(iconName)) return iconComponentCache.get(iconName)
  const path = badgeIconPaths[iconName] || badgeIconPaths.trophy
  const Comp = {
    name: `BadgeIcon_${iconName}`,
    render() {
      return h('svg', {
        viewBox: '0 0 24 24',
        fill: 'none',
        stroke: 'currentColor',
        'stroke-width': 1.5,
        'stroke-linecap': 'round',
        'stroke-linejoin': 'round',
      }, [h('path', { d: path })])
    },
  }
  iconComponentCache.set(iconName, Comp)
  return Comp
}

const createBadge = async () => {
  const result = await badgesStore.createBadge(newBadge.value)
  if (result.success) {
    newBadge.value = { name: '', description: '', icon: 'trophy', color: '#7d57a8', trigger_type: 'custom', trigger_threshold: null, is_hidden: false }
    showCreateForm.value = false
  }
}

const handleAward = async () => {
  if (!awardBadgeId.value || !awardUserId.value) return
  await badgesStore.manuallyAward(awardBadgeId.value, awardUserId.value)
  awardBadgeId.value = ''
  awardUserId.value = ''
}

onMounted(() => {
  badgesStore.fetchBadges()
})
</script>
