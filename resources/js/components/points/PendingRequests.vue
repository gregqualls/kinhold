<template>
  <div v-if="pendingRequests.length > 0" class="card mb-4">
    <div class="px-4 pt-3 pb-2 border-b border-lavender-200 dark:border-prussian-700 flex items-center justify-between">
      <p class="text-sm font-semibold font-heading text-prussian-500 dark:text-lavender-200">
        Pending Point Requests
      </p>
      <span class="bg-wisteria-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">
        {{ pendingRequests.length }}
      </span>
    </div>

    <div class="divide-y divide-lavender-100 dark:divide-prussian-700">
      <div
        v-for="req in pendingRequests"
        :key="req.id"
        class="px-4 py-3 flex items-center justify-between gap-3"
      >
        <div class="flex-1 min-w-0">
          <p class="text-sm font-medium text-prussian-500 dark:text-lavender-200 truncate">
            {{ req.user?.name }} requested <span class="font-bold font-mono text-wisteria-600 dark:text-wisteria-400">{{ req.points }} pts</span>
          </p>
          <p class="text-xs text-lavender-500 dark:text-lavender-400 truncate">{{ req.reason }}</p>
        </div>
        <div class="flex gap-2 shrink-0">
          <button
            class="btn-sm bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-lg text-xs font-medium"
            @click="$emit('approve', req.id)"
          >
            Approve
          </button>
          <button
            class="btn-sm bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-lg text-xs font-medium"
            @click="$emit('deny', req.id)"
          >
            Deny
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  requests: {
    type: Array,
    default: () => [],
  },
})

defineEmits(['approve', 'deny'])

const pendingRequests = computed(() =>
  props.requests.filter(r => r.status === 'pending')
)
</script>
