<template>
  <div class="flex items-center justify-between gap-3 py-2">
    <div class="flex-1 min-w-0">
      <p class="text-[10px] font-semibold text-lavender-500 dark:text-lavender-400 uppercase tracking-wider mb-0.5">{{ label }}</p>
      <p
        class="text-sm font-mono truncate"
        :class="revealed ? 'text-prussian-500 dark:text-lavender-200' : 'text-lavender-400 dark:text-lavender-500 select-none'"
      >
        {{ revealed ? value : maskedValue }}
      </p>
    </div>

    <div class="flex items-center gap-1 flex-shrink-0">
      <!-- Reveal toggle -->
      <button
        v-if="sensitive"
        @click="toggleReveal"
        class="p-1.5 text-lavender-400 hover:text-prussian-500 dark:hover:text-lavender-200 hover:bg-lavender-100 dark:hover:bg-prussian-700 rounded-lg transition-colors"
        :title="revealed ? 'Hide' : 'Reveal'"
      >
        <EyeIcon v-if="!revealed" class="w-4 h-4" />
        <EyeSlashIcon v-else class="w-4 h-4" />
      </button>

      <!-- Copy -->
      <button
        @click="copyValue"
        class="p-1.5 rounded-lg transition-all"
        :class="justCopied ? 'text-emerald-600 bg-emerald-50 dark:bg-emerald-900/20' : 'text-lavender-400 hover:text-prussian-500 dark:hover:text-lavender-200 hover:bg-lavender-100 dark:hover:bg-prussian-700'"
        :title="justCopied ? 'Copied!' : 'Copy'"
      >
        <CheckIcon v-if="justCopied" class="w-4 h-4" />
        <ClipboardDocumentIcon v-else class="w-4 h-4" />
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onUnmounted } from 'vue'
import { EyeIcon, EyeSlashIcon, ClipboardDocumentIcon, CheckIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  label: String,
  value: [String, Number],
  sensitive: { type: Boolean, default: false },
})

const revealed = ref(!props.sensitive)
const justCopied = ref(false)
let revealTimer = null
let copyTimer = null
let autoClearTimer = null

const maskedValue = computed(() => {
  const str = String(props.value || '')
  if (str.length <= 4) return '•'.repeat(str.length)
  return '•'.repeat(str.length - 4) + str.slice(-4)
})

const toggleReveal = () => {
  revealed.value = !revealed.value
  clearTimeout(revealTimer)
  if (revealed.value) {
    revealTimer = setTimeout(() => {
      revealed.value = false
    }, 30000)
  }
}

const copyValue = async () => {
  try {
    await navigator.clipboard.writeText(String(props.value || ''))
    justCopied.value = true
    clearTimeout(copyTimer)
    copyTimer = setTimeout(() => {
      justCopied.value = false
    }, 2000)

    // Auto-clear clipboard after 30s for sensitive fields
    if (props.sensitive) {
      clearTimeout(autoClearTimer)
      autoClearTimer = setTimeout(() => {
        navigator.clipboard.writeText('')
      }, 30000)
    }
  } catch {
    // Clipboard not available
  }
}

// Auto-hide on tab blur for sensitive fields
const handleVisibilityChange = () => {
  if (document.hidden && props.sensitive) {
    revealed.value = false
  }
}

if (props.sensitive) {
  document.addEventListener('visibilitychange', handleVisibilityChange)
}

onUnmounted(() => {
  clearTimeout(revealTimer)
  clearTimeout(copyTimer)
  clearTimeout(autoClearTimer)
  document.removeEventListener('visibilitychange', handleVisibilityChange)
})
</script>
