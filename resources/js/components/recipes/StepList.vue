<template>
  <div class="space-y-4">
    <div
      v-for="(step, index) in normalizedSteps"
      :key="index"
      class="flex gap-3 group cursor-pointer"
      @click="toggleStep(index)"
    >
      <!-- Step number -->
      <div
        class="w-7 h-7 rounded-full flex-shrink-0 flex items-center justify-center text-xs font-semibold transition-colors"
        :class="completedSteps.has(index)
          ? 'bg-[#5B8C6A] text-white'
          : 'bg-[#C4975A]/10 text-[#C4975A]'"
      >
        <CheckIcon v-if="completedSteps.has(index)" class="w-3.5 h-3.5" />
        <span v-else>{{ index + 1 }}</span>
      </div>

      <!-- Step text -->
      <p
        class="text-sm leading-relaxed pt-0.5 transition-colors"
        :class="completedSteps.has(index)
          ? 'line-through text-lavender-400 dark:text-lavender-500'
          : 'text-prussian-500 dark:text-lavender-200'"
      >
        {{ step }}
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { CheckIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  instructions: { type: Array, default: () => [] },
})

const completedSteps = ref(new Set())

const normalizedSteps = computed(() => {
  return props.instructions.map((step) => {
    if (typeof step === 'string') return step
    return step.text || step.step || ''
  })
})

const toggleStep = (index) => {
  if (completedSteps.value.has(index)) {
    completedSteps.value.delete(index)
  } else {
    completedSteps.value.add(index)
  }
  completedSteps.value = new Set(completedSteps.value)
}
</script>
