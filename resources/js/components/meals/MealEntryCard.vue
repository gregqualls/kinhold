<template>
  <div
    class="group relative bg-white dark:bg-[#1C1C20] border border-[#E8E4DF] dark:border-[#2E2E32] rounded-[10px] overflow-hidden cursor-pointer hover:border-[#C4975A]/50 hover:shadow-sm transition-all"
    :data-entry-id="entry.id"
    @click="$emit('click', entry)"
  >
    <!-- Image (or placeholder icon fills) -->
    <div class="aspect-video bg-lavender-100 dark:bg-prussian-700 relative overflow-hidden">
      <img
        v-if="entry.image_url && !imgFailed"
        :src="entry.image_url"
        :alt="entry.display_title"
        class="w-full h-full object-cover"
        @error="imgFailed = true"
      />
      <div v-else class="w-full h-full flex items-center justify-center">
        <component :is="typeIcon" class="w-6 h-6" :class="typeColor" />
      </div>

      <!-- Delete button (hover-visible overlay) -->
      <button
        class="absolute top-1 right-1 w-5 h-5 rounded-full bg-black/50 text-white/90 flex items-center justify-center opacity-0 group-hover:opacity-100 hover:bg-red-500 transition-all"
        :aria-label="`Remove ${entry.display_title} from meal plan`"
        @click.stop="$emit('delete', entry)"
      >
        <XMarkIcon class="w-3 h-3" />
      </button>

      <!-- Maps link (restaurants only, bottom-right) -->
      <a
        v-if="entry.type === 'restaurant' && entry.restaurant?.google_maps_url"
        :href="entry.restaurant.google_maps_url"
        target="_blank"
        rel="noopener"
        class="absolute bottom-1 right-1 w-5 h-5 rounded-full bg-black/50 text-white/90 flex items-center justify-center hover:bg-[#C4975A] transition-all"
        aria-label="Open in Google Maps"
        @click.stop
      >
        <MapPinIcon class="w-3 h-3" />
      </a>
    </div>

    <!-- Content -->
    <div class="p-1.5">
      <div class="flex items-start gap-1.5">
        <div class="flex-1 min-w-0">
          <p class="text-xs font-medium text-[#1C1C1E] dark:text-[#F0EDE9] leading-tight line-clamp-2">
            {{ entry.display_title }}
          </p>
          <p v-if="entry.effective_servings" class="text-[10px] text-[#9C9895] mt-0.5">
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
            class="ring-1 ring-white dark:ring-[#1C1C20]"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import {
  FireIcon,
  BuildingStorefrontIcon,
  SparklesIcon,
  PencilIcon,
  XMarkIcon,
  MapPinIcon,
} from '@heroicons/vue/24/outline'
import UserAvatar from '@/components/common/UserAvatar.vue'
import { useAuthStore } from '@/stores/auth'

const props = defineProps({
  entry: { type: Object, required: true },
})

defineEmits(['click', 'delete'])

const authStore = useAuthStore()
const imgFailed = ref(false)

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
    case 'recipe': return FireIcon
    case 'restaurant': return BuildingStorefrontIcon
    case 'preset': return SparklesIcon
    default: return PencilIcon
  }
})

const typeColor = computed(() => {
  switch (props.entry.type) {
    case 'recipe': return 'text-[#C48B3F]'
    case 'restaurant': return 'text-[#5B7B9C]'
    case 'preset': return 'text-[#7B6B9C]'
    default: return 'text-[#9C9895]'
  }
})
</script>
