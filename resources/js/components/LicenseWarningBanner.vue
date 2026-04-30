<template>
  <div
    v-if="shouldShow"
    class="bg-amber-50 dark:bg-amber-900/20 border-b border-amber-200 dark:border-amber-800 px-4 py-2 flex items-start sm:items-center justify-between gap-3"
    role="alert"
  >
    <div class="flex items-start sm:items-center gap-2 text-sm text-amber-800 dark:text-amber-300">
      <svg class="w-5 h-5 flex-shrink-0 mt-0.5 sm:mt-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
      </svg>
      <p>
        This self-hosted Kinhold instance has
        <strong>{{ familyCount }} {{ familyCount === 1 ? 'family' : 'families' }}</strong>.
        The Elastic License limits self-hosted instances to a single family.
        We know — you could probably figure out how to silence this banner.
        The right move is to grab a commercial license from <strong>Q Thirty Two</strong> instead.
        <a
          href="https://github.com/gregqualls/kinhold/issues/new?title=Commercial%20license%20inquiry&labels=commercial-license"
          target="_blank"
          rel="noopener"
          class="underline font-medium hover:text-amber-900 dark:hover:text-amber-200"
        >Reach out</a>
        ·
        <a
          href="https://github.com/gregqualls/kinhold/blob/main/LICENSE"
          target="_blank"
          rel="noopener"
          class="underline font-medium hover:text-amber-900 dark:hover:text-amber-200"
        >Read the license</a>
      </p>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()

const license = computed(() => authStore.appConfig?.license)
const shouldShow = computed(() => license.value?.warn === true)
const familyCount = computed(() => license.value?.family_count ?? 0)
</script>
