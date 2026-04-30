<!--
  MealEntryCard — two render modes:

  • Default (card): image-on-top + title-below, used inside MealWeekGrid where
    cells are tight. Visually consistent with RecipeCard / Restaurant card.
  • compact=true (row): thumbnail on left + title on right, used inside
    MealDaySection (mobile, and the desktop list view). Single-line, thumb-
    tappable, scannable for "what's for dinner today" at a glance.
-->
<template>
  <!-- ── Compact row layout (MealDaySection / mobile) ──────────────────── -->
  <!--
    role="button" + tabindex + Enter/Space handlers make the row keyboard-
    accessible without using a native <button> (which can't legally contain
    the inner delete <button>). Same pattern below for the card variant.
  -->
  <div
    v-if="compact"
    class="group relative cursor-pointer transition-colors rounded-[12px] border border-border-subtle bg-surface-raised hover:border-[#C4975A]/40 flex items-center gap-3 px-2.5 py-2 min-h-[56px] focus:outline-none focus:ring-2 focus:ring-[#C4975A]/40"
    :data-entry-id="entry.id"
    role="button"
    tabindex="0"
    :aria-label="`Edit ${entry.display_title}`"
    @click="$emit('click', entry)"
    @keydown.enter.prevent="$emit('click', entry)"
    @keydown.space.prevent="$emit('click', entry)"
  >
    <!-- Thumbnail / type-icon fallback -->
    <div
      class="w-11 h-11 rounded-[10px] overflow-hidden flex-shrink-0 flex items-center justify-center"
      :style="entry.image_url ? null : compactFallbackStyle"
    >
      <img
        v-if="entry.image_url"
        :src="entry.image_url"
        :alt="entry.display_title"
        class="w-full h-full object-cover"
      />
      <component :is="typeIcon" v-else class="w-5 h-5" :class="typeColor" />
    </div>

    <!-- Title + servings -->
    <div class="flex-1 min-w-0">
      <p class="text-[15px] font-medium text-ink-primary leading-tight line-clamp-1">
        {{ entry.display_title }}
      </p>
      <p v-if="entry.effective_servings" class="text-xs text-ink-tertiary mt-0.5">
        {{ entry.effective_servings }} servings
      </p>
    </div>

    <!-- Cook avatars -->
    <div v-if="cooks.length" class="flex -space-x-1 flex-shrink-0">
      <UserAvatar
        v-for="cook in cooks"
        :key="cook.id"
        :user="cook"
        size="xs"
        class="ring-1 ring-surface-raised"
      />
    </div>

    <!-- Delete (always visible on row — easier on touch than hover-reveal) -->
    <button
      type="button"
      class="w-8 h-8 -mr-1 rounded-full text-ink-tertiary hover:text-status-failed hover:bg-status-failed/10 flex items-center justify-center flex-shrink-0 transition-colors"
      :aria-label="`Remove ${entry.display_title} from meal plan`"
      @click.stop="$emit('delete', entry)"
    >
      <XMarkIcon class="w-4 h-4" />
    </button>
  </div>

  <!-- ── Card layout (MealWeekGrid / desktop dense grid) ────────────────── -->
  <div
    v-else
    class="group relative cursor-pointer transition-shadow rounded-card overflow-hidden border border-border-subtle bg-surface-raised hover:shadow-resting hover:border-[#C4975A]/40 focus:outline-none focus:ring-2 focus:ring-[#C4975A]/40"
    :data-entry-id="entry.id"
    role="button"
    tabindex="0"
    :aria-label="`Edit ${entry.display_title}`"
    @click="$emit('click', entry)"
    @keydown.enter.prevent="$emit('click', entry)"
    @keydown.space.prevent="$emit('click', entry)"
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
  // When true, render a compact row layout (used inside MealDaySection on
  // mobile + desktop list view) instead of the default image-on-top card
  // (used inside MealWeekGrid).
  compact: { type: Boolean, default: false },
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

// Compact-mode thumbnail uses inline gradient (KinPhotoCard's full layout
// is overkill for a 44px square). Mirrors the radial-wash recipes/restaurants
// use in their compact rows so meal entries that ARE recipes look on-brand.
const COMPACT_GRADIENT = {
  peach:    'radial-gradient(ellipse 100% 90% at 30% 20%, rgb(var(--accent-peach-soft) / 0.85) 0%, transparent 70%)',
  lavender: 'radial-gradient(ellipse 100% 90% at 30% 20%, rgb(var(--accent-lavender-soft) / 0.85) 0%, transparent 70%)',
  mint:     'radial-gradient(ellipse 100% 90% at 30% 20%, rgb(var(--accent-mint-soft) / 0.85) 0%, transparent 70%)',
  warm:     'var(--gradient-iridescent-warm)',
}
const compactFallbackStyle = computed(() => ({
  backgroundImage: COMPACT_GRADIENT[fallbackGradient.value] || COMPACT_GRADIENT.warm,
}))
</script>
