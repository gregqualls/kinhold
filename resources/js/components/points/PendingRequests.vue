<template>
  <div v-if="pendingRequests.length > 0" class="card mb-4">
    <div class="px-4 pt-3 pb-2 border-b border-border-subtle flex items-center justify-between">
      <p class="text-sm font-semibold font-heading text-ink-primary">
        Pending Point Requests
      </p>
      <span class="bg-accent-lavender-bold text-white text-xs font-bold px-2 py-0.5 rounded-full">
        {{ pendingRequests.length }}
      </span>
    </div>

    <div class="divide-y divide-border-subtle">
      <div
        v-for="req in pendingRequests"
        :key="req.id"
        class="px-4 py-3 flex items-center justify-between gap-3"
      >
        <div class="flex-1 min-w-0">
          <p class="text-sm font-medium text-ink-primary truncate">
            {{ req.user?.name }} requested <span class="font-bold font-mono text-accent-lavender-bold">{{ req.points }} pts</span>
          </p>
          <p class="text-xs text-ink-tertiary truncate">{{ req.reason }}</p>
        </div>
        <div class="flex gap-2 shrink-0">
          <button
            class="btn-sm bg-status-success hover:opacity-90 text-white px-3 py-1 rounded-lg text-xs font-medium"
            @click="$emit('approve', req.id)"
          >
            Approve
          </button>
          <button
            class="btn-sm bg-status-failed hover:opacity-90 text-white px-3 py-1 rounded-lg text-xs font-medium"
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
