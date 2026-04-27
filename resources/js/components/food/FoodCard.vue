<!--
  FoodCard — image-focused card for recipes / restaurants.
  Wraps KinPhotoCard with a recipe/restaurant-specific overlay (title + meta + tags).
  Falls back to a per-card gradient when no image is set.
-->
<template>
  <KinPhotoCard
    :src="resolvedSrc"
    :alt="title"
    aspect="tall"
    :fallback-gradient="fallbackGradient"
    :as="as"
    interactive
    overlay-size="sm"
    @click="$emit('click')"
  >
    <!-- Favorite heart in the top-right -->
    <template v-if="showFavorite" #actions>
      <button
        type="button"
        class="w-9 h-9 rounded-full flex items-center justify-center transition-colors backdrop-blur-sm"
        :class="isFavorite
          ? 'bg-status-failed/90 text-white hover:bg-status-failed'
          : 'bg-black/35 text-white/85 hover:bg-black/55'"
        :aria-label="isFavorite ? 'Remove favorite' : 'Mark as favorite'"
        @click.stop="$emit('toggle-favorite')"
      >
        <HeartIconSolid v-if="isFavorite" class="w-4 h-4" />
        <HeartIcon v-else class="w-4 h-4" />
      </button>
    </template>

    <!-- Custom overlay: title + meta row + tag chips -->
    <template #overlay="{ showPhoto }">
      <div
        class="space-y-1.5"
        :class="showPhoto ? 'text-white' : 'text-ink-primary'"
      >
        <h3
          class="text-[15px] font-semibold leading-tight line-clamp-2"
          :style="showPhoto ? 'text-shadow: 0 1px 3px rgba(0,0,0,0.45);' : ''"
        >
          {{ title }}
        </h3>

        <div
          v-if="metaItems.length"
          class="flex items-center gap-3 text-[11px]"
          :class="showPhoto ? 'text-white/85' : 'text-ink-secondary'"
          :style="showPhoto ? 'text-shadow: 0 1px 2px rgba(0,0,0,0.40);' : ''"
        >
          <span
            v-for="(item, i) in metaItems"
            :key="i"
            class="flex items-center gap-1"
          >
            <component :is="item.icon" class="w-3.5 h-3.5" :class="item.iconClass || ''" />
            {{ item.text }}
          </span>
        </div>

        <div v-if="tags.length" class="flex items-center gap-1 flex-wrap">
          <span
            v-for="(tag, i) in tags.slice(0, 3)"
            :key="i"
            class="px-2 py-0.5 text-[10px] font-medium rounded-full backdrop-blur-sm"
            :class="showPhoto ? 'bg-white/25 text-white' : 'bg-surface-raised/80 text-ink-secondary border border-border-subtle'"
          >
            {{ typeof tag === 'string' ? tag : tag.name }}
          </span>
          <span
            v-if="tags.length > 3"
            class="text-[10px]"
            :class="showPhoto ? 'text-white/75' : 'text-ink-tertiary'"
          >
            +{{ tags.length - 3 }}
          </span>
        </div>
      </div>
    </template>
  </KinPhotoCard>
</template>

<script setup>
import { computed } from 'vue'
import { HeartIcon } from '@heroicons/vue/24/outline'
import { HeartIcon as HeartIconSolid } from '@heroicons/vue/24/solid'
import KinPhotoCard from '@/components/design-system/KinPhotoCard.vue'

const props = defineProps({
  title: { type: String, required: true },
  /**
   * Image URL. Accepts:
   *   • Absolute URL (https://… or /images/…)         — used as-is
   *   • Relative storage path (recipes/abc.jpg)        — prefixed with `/storage/`
   */
  imageUrl: { type: String, default: null },
  /**
   * Gradient family for the no-image fallback.
   * One of: iridescent | warm | lavender | peach | mint | sun | cool
   */
  fallbackGradient: { type: String, default: 'warm' },
  isFavorite: { type: Boolean, default: false },
  showFavorite: { type: Boolean, default: true },
  metaItems: { type: Array, default: () => [] },
  tags: { type: Array, default: () => [] },
  as: { type: String, default: 'article' },
})

defineEmits(['click', 'toggle-favorite'])

// Pass-through: absolute URLs (http/https/leading-slash) untouched, anything
// else gets the `/storage/` prefix the Laravel public-disk symlink exposes.
const resolvedSrc = computed(() => {
  const url = props.imageUrl
  if (!url) return null
  if (url.startsWith('http://') || url.startsWith('https://') || url.startsWith('/')) return url
  return `/storage/${url}`
})
</script>
