<template>
  <div class="flex flex-col items-center justify-center p-8 text-center">
    <!-- Shopping cart icon -->
    <svg class="w-16 h-16 text-lavender-300 dark:text-prussian-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
      <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
    </svg>
    <h2 class="text-xl font-bold font-heading text-prussian-500 dark:text-lavender-200 mb-2">Create your first list</h2>
    <p class="text-sm text-lavender-500 dark:text-lavender-400 mb-6">Name it after a store, a category, or whatever works for you</p>
    <form class="flex items-center w-full max-w-sm" @submit.prevent="handleCreate">
      <input v-model="storeName" type="text" placeholder="e.g. Costco, Weekly groceries..." class="input-base flex-1 rounded-r-none border-r-0" />
      <button type="submit" :disabled="!storeName.trim()" class="btn-primary rounded-l-none px-5 h-[42px]">Create</button>
    </form>
    <p class="text-xs text-lavender-400 dark:text-lavender-500 mt-3">You can add more lists later</p>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const emit = defineEmits(['create'])

const storeName = ref('')

const handleCreate = () => {
  const name = storeName.value.trim()
  if (!name) return
  emit('create', name)
  storeName.value = ''
}
</script>
