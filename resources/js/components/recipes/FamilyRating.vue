<template>
  <div class="space-y-3">
    <!-- Average + user rating -->
    <div class="flex items-center gap-4">
      <!-- Stars -->
      <div class="flex items-center gap-0.5" role="group" aria-label="Rate this recipe">
        <button
          v-for="star in 5"
          :key="star"
          class="p-0.5 transition-colors focus:outline-none focus:ring-2 focus:ring-[#C4975A]/30 rounded"
          :aria-label="`Rate ${star} star${star > 1 ? 's' : ''}`"
          @click="handleRate(star)"
          @keydown.arrow-right.prevent="handleRate(Math.min(5, (userRating || 0) + 1))"
          @keydown.arrow-left.prevent="handleRate(Math.max(1, (userRating || 0) - 1))"
        >
          <StarIconSolid
            class="w-6 h-6 transition-colors"
            :class="star <= displayRating ? 'text-[#C4975A]' : 'text-ink-tertiary'"
          />
        </button>
      </div>

      <!-- Average label -->
      <span class="text-sm text-ink-tertiary">
        <template v-if="averageRating > 0">
          {{ averageRating.toFixed(1) }} avg
        </template>
        <template v-else>
          No ratings yet
        </template>
      </span>
    </div>

    <!-- Your rating label -->
    <p v-if="userRating" class="text-xs text-ink-tertiary">
      Your rating: {{ userRating }}/5
    </p>

    <!-- Individual ratings (expandable) -->
    <div v-if="ratings.length > 0">
      <button
        class="text-xs text-ink-tertiary hover:text-ink-primary transition-colors"
        @click="showAll = !showAll"
      >
        {{ showAll ? 'Hide' : 'Show' }} family ratings ({{ ratings.length }})
      </button>
      <div v-if="showAll" class="mt-2 space-y-1.5">
        <div
          v-for="rating in ratings"
          :key="rating.id"
          class="flex items-center gap-2 text-sm"
        >
          <UserAvatar :user="rating.user" size="xs" />
          <span class="text-ink-primary">{{ rating.user?.name }}</span>
          <div class="flex items-center gap-0.5">
            <StarIconSolid
              v-for="s in 5"
              :key="s"
              class="w-3 h-3"
              :class="s <= rating.score ? 'text-[#C4975A]' : 'text-ink-tertiary'"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { StarIcon as StarIconSolid } from '@heroicons/vue/24/solid'
import UserAvatar from '@/components/common/UserAvatar.vue'

const props = defineProps({
  recipeId: { type: String, required: true },
  averageRating: { type: Number, default: 0 },
  userRating: { type: Number, default: null },
  ratings: { type: Array, default: () => [] },
})

const emit = defineEmits(['rate'])

const showAll = ref(false)

const displayRating = computed(() => props.userRating || Math.round(props.averageRating))

const handleRate = (score) => {
  emit('rate', score)
}
</script>
