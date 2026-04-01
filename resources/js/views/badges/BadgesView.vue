<template>
  <div class="p-4 md:p-6 max-w-4xl">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold font-heading text-prussian-500 dark:text-lavender-200">Badges</h1>
      <button v-if="isParent" class="btn-primary btn-sm" @click="showCreateForm = !showCreateForm">
        {{ showCreateForm ? 'Cancel' : '+ Create Badge' }}
      </button>
    </div>

    <!-- Create Form (parent only) -->
    <div v-if="showCreateForm" class="card p-4 mb-6">
      <h3 class="text-sm font-semibold text-prussian-500 dark:text-lavender-200 mb-3">New Badge</h3>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <input v-model="newBadge.name" class="input-base" placeholder="Badge name" />
        <input v-model="newBadge.description" class="input-base" placeholder="Description" />

        <div>
          <label class="block text-xs text-lavender-500 mb-1">Trigger Type</label>
          <select v-model="newBadge.trigger_type" class="input-base w-full">
            <option value="custom">Custom (Manual Award)</option>
            <option value="tasks_completed">Tasks Completed</option>
            <option value="points_earned">Points Earned</option>
            <option value="task_streak">Task Streak (Days)</option>
            <option value="kudos_received">Kudos Received</option>
            <option value="kudos_given">Kudos Given</option>
            <option value="rewards_purchased">Rewards Purchased</option>
            <option value="login_streak">Login Streak</option>
          </select>
        </div>

        <div v-if="newBadge.trigger_type !== 'custom'">
          <label class="block text-xs text-lavender-500 mb-1">Threshold</label>
          <input v-model.number="newBadge.trigger_threshold" type="number" min="1" class="input-base w-full" placeholder="e.g. 10" />
        </div>

        <div>
          <label class="block text-xs text-lavender-500 mb-1">Icon</label>
          <div class="flex flex-wrap gap-2">
            <button
              v-for="iconName in iconNames"
              :key="iconName"
              class="p-1.5 rounded-lg border transition-colors"
              :class="newBadge.icon === iconName
                ? 'border-wisteria-400 bg-wisteria-50 dark:bg-wisteria-900/30'
                : 'border-lavender-200 dark:border-prussian-700 hover:border-lavender-300'"
              @click="newBadge.icon = iconName"
            >
              <BadgeIcon :icon="iconName" :color="newBadge.color" size="sm" />
            </button>
          </div>
        </div>

        <div class="sm:col-span-2">
          <label class="block text-xs text-lavender-500 mb-1">Color</label>
          <div class="flex flex-wrap gap-2">
            <button
              v-for="c in badgeColorPalette"
              :key="c.value"
              type="button"
              :title="c.label"
              class="w-8 h-8 rounded-full border-2 transition-all duration-150 flex-shrink-0"
              :class="newBadge.color === c.value
                ? 'ring-2 ring-offset-2 ring-offset-white dark:ring-offset-prussian-800 scale-110'
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

        <div class="flex items-center gap-2">
          <input id="is_hidden" v-model="newBadge.is_hidden" type="checkbox" class="rounded" />
          <label for="is_hidden" class="text-sm text-prussian-500 dark:text-lavender-300">Hidden badge (surprise!)</label>
        </div>
      </div>

      <div class="flex justify-end mt-4">
        <button :disabled="!newBadge.name || !newBadge.description" class="btn-primary btn-sm" @click="createBadge">
          Create Badge
        </button>
      </div>
    </div>

    <!-- Tabs -->
    <div class="flex gap-1 bg-lavender-100 dark:bg-prussian-700 rounded-lg p-1 mb-6 w-fit">
      <button
        v-for="tab in ['All', 'Earned', 'Locked']"
        :key="tab"
        :class="[
          'px-3 py-1.5 rounded-md text-sm font-medium transition-all duration-150',
          activeTab === tab
            ? 'bg-wisteria-600 text-white shadow-sm'
            : 'text-prussian-500 dark:text-lavender-300 hover:bg-lavender-200 dark:hover:bg-prussian-600',
        ]"
        @click="activeTab = tab"
      >
        {{ tab }}
      </button>
    </div>

    <!-- Badges Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
      <BadgeCard
        v-for="badge in filteredBadges"
        :key="badge.id"
        :badge="badge"
      />
    </div>

    <div v-if="filteredBadges.length === 0 && !badgesStore.isLoading" class="card p-8 text-center">
      <p class="text-lavender-500 dark:text-lavender-400">
        {{ activeTab === 'Earned' ? 'No badges earned yet. Keep going!' : activeTab === 'Locked' ? 'All badges unlocked!' : 'No badges available.' }}
      </p>
    </div>

    <!-- Parent Award Section -->
    <div v-if="isParent && activeTab === 'All'" class="mt-8 card p-4">
      <h3 class="text-sm font-semibold text-prussian-500 dark:text-lavender-200 mb-3">Manually Award Badge</h3>
      <div class="flex gap-3 items-end flex-wrap">
        <div>
          <label class="block text-xs text-lavender-500 mb-1">Badge</label>
          <select v-model="awardBadgeId" class="input-base">
            <option value="">Select badge...</option>
            <option v-for="b in badgesStore.badges.filter(b => b.name !== '???')" :key="b.id" :value="b.id">
              {{ b.name }}
            </option>
          </select>
        </div>
        <div>
          <label class="block text-xs text-lavender-500 mb-1">Member</label>
          <select v-model="awardUserId" class="input-base">
            <option value="">Select member...</option>
            <option v-for="m in familyMembers" :key="m.id" :value="m.id">
              {{ m.name }}
            </option>
          </select>
        </div>
        <button :disabled="!awardBadgeId || !awardUserId" class="btn-primary btn-sm" @click="handleAward">
          Award
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { storeToRefs } from 'pinia'
import { useBadgesStore } from '@/stores/badges'
import { useAuthStore } from '@/stores/auth'
import BadgeCard from '@/components/badges/BadgeCard.vue'
import BadgeIcon from '@/components/badges/BadgeIcon.vue'
import { badgeIconNames } from '@/components/badges/badgeIcons'

const badgesStore = useBadgesStore()
const authStore = useAuthStore()
const { isParent, familyMembers } = storeToRefs(authStore)

const activeTab = ref('All')
const showCreateForm = ref(false)
const awardBadgeId = ref('')
const awardUserId = ref('')

const iconNames = badgeIconNames

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
