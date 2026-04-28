<script setup>
defineProps({
  title: { type: String, required: true },
  description: { type: String, default: '' },
  status: { type: String, default: 'empty' }, // empty | scaffolded | chosen
})
</script>

<template>
  <div class="p-6 md:p-10 max-w-[1600px] mx-auto">
    <header class="mb-8 flex items-start justify-between gap-6">
      <div>
        <h1 class="text-3xl md:text-4xl font-semibold tracking-tight text-kin-black dark:text-kin-off-white">{{ title }}</h1>
        <p v-if="description" class="mt-2 text-base text-kin-gray-500 dark:text-kin-gray-300 max-w-2xl">{{ description }}</p>
      </div>
      <span
        class="text-xs font-medium px-2.5 py-1 rounded-full whitespace-nowrap"
        :class="{
          'bg-kin-gray-100 text-kin-gray-500 dark:bg-kin-gray-800 dark:text-kin-gray-300': status === 'empty',
          'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300': status === 'scaffolded',
          'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300': status === 'chosen',
        }"
      >{{ status === 'empty' ? 'Awaiting variants' : status === 'scaffolded' ? 'In review' : 'Chosen' }}</span>
    </header>
    <slot></slot>
  </div>
</template>
