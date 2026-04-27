<!--
  MealEntryCard — small "card-in-a-grid-cell" used inside MealWeekGrid.
  Visually consistent with RecipeCard / Restaurant card: image + gradient
  fallback come from KinPhotoCard. Title + cook avatars sit BELOW the photo
  (not overlaid) because the grid cells are tight and the recipe/restaurant
  cards' bottom-overlay text would crowd them.
-->
<template>
  <div
    class="group relative cursor-pointer transition-shadow rounded-card overflow-hidden border border-border-subtle bg-surface-raised hover:shadow-resting hover:border-[#C4975A]/40"
    :data-entry-id="entry.id"
    @click="$emit('click', entry)"
  >
    <!-- Photo (or per-type gradient fallback) -->
    <KinPhotoCard
      :src="entry.image_url"
      :alt="entry.display_title"
      aspect="video"
      :fallback-gradient="fallbackGradient"
      class="!shadow-none !rounded-none border-b border-border-subtle"
    >
      <!-- Delete button — top-right, hover-visible -->
      <template #badges>
        <button
          type="button"
          class="w-5 h-5 rounded-full bg-black/55 text-white/90 flex items-center justify-center opacity-0 group-hover:opacity-100 hover:bg-status-failed transition-all pointer-events-auto"
          :aria-label="`Remove ${entry.display_title} from meal plan`"
          @click.stop="$emit('delete', entry)"
        >
          <XMarkIcon class="w-3 h-3" />
        </button>
      </template>

      <!-- Maps link (restaurants only) — bottom-right -->
      <template v-if="entry.type === 'restaurant' && entry.restaurant?.google_maps_url" #actions>
        <a
          :href="entry.restaurant.google_maps_url"
          target="_blank"
          rel="noopener"
          class="w-5 h-5 rounded-full bg-black/55 text-white/90 flex items-center justify-center hover:bg-[#C4975A] transition-all"
          aria-label="Open in Google Maps"
          @click.stop
        >
          <MapPinIcon class="w-3 h-3" />
        </a>
      </template>

      <!-- Centered type icon when no photo. KinPhotoCard's gradient fallback
           already paints the surface; this overlay adds a small type marker. -->
      <template v-if="!entry.image_url" #overlay>
        <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
          <component :is="typeIcon" class="w-6 h-6" :class="typeColor" />
        </div>
      </template>
    </KinPhotoCard>

    <!-- Content (below photo) -->
    <div class="p-1.5">
      <div class="flex items-start gap-1.5">
        <div class="flex-1 min-w-0">
          <p class="text-xs font-medium text-ink-primary leading-tight line-clamp-2">
            {{ entry.display_title }}
          </p>
          <p v-if="entry.effective_servings" class="text-[10px] text-ink-tertiary mt-0.5">
            {{ entry.effective_servings }} servings
          </p>
        </div>

        <!-- Cook avatars (overlapping, stacked right) -->
        <div v-if="cooks.length" class="flex -space-x-1 flex-shrink-0 mt-0.5">
          <UserAvatar
            v-for="cook in cooks"
            :key="cook.id"
            :user="cook"
            size="xs"
            class="ring-1 ring-surface-raised"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import {
  FireIcon,
  BuildingStorefrontIcon,
  SparklesIcon,
  PencilIcon,
  XMarkIcon,
  MapPinIcon,
} from '@heroicons/vue/24/outline'
import UserAvatar from '@/components/common/UserAvatar.vue'
import KinPhotoCard from '@/components/design-system/KinPhotoCard.vue'
import { useAuthStore } from '@/stores/auth'

const props = defineProps({
  entry: { type: Object, required: true },
})

defineEmits(['click', 'delete'])

const authStore = useAuthStore()

const cooks = computed(() => {
  if (!props.entry.assigned_cooks?.length) return []
  const members = authStore.familyMembers || []
  return props.entry.assigned_cooks
    .map(id => members.find(m => m.id === id))
    .filter(Boolean)
    .slice(0, 3)
})

const typeIcon = computed(() => {
  switch (props.entry.type) {
    case 'recipe':     return FireIcon
    case 'restaurant': return BuildingStorefrontIcon
    case 'preset':     return SparklesIcon
    default:           return PencilIcon
  }
})

const typeColor = computed(() => {
  switch (props.entry.type) {
    case 'recipe':     return 'text-accent-peach-bold'
    case 'restaurant': return 'text-accent-lavender-bold'
    case 'preset':     return 'text-accent-mint-bold'
    default:           return 'text-ink-tertiary'
  }
})

// Per-type gradient fallback — matches the type-icon color family above so
// the gradient + glyph read as a single coherent "this is a recipe / this is
// a restaurant / this is a preset" affordance.
const fallbackGradient = computed(() => {
  switch (props.entry.type) {
    case 'recipe':     return 'peach'
    case 'restaurant': return 'lavender'
    case 'preset':     return 'mint'
    default:           return 'warm'
  }
})
</script>
