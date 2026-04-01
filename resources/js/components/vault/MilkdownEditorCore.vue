<template>
  <div ref="editorRef" class="milkdown-wrapper" :class="{ 'milkdown-readonly': readonly }"></div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, watch } from 'vue'
import { Editor, rootCtx, defaultValueCtx, editorViewOptionsCtx } from '@milkdown/kit/core'
import { commonmark } from '@milkdown/kit/preset/commonmark'
import { history } from '@milkdown/kit/plugin/history'
import { clipboard } from '@milkdown/kit/plugin/clipboard'
import { indent } from '@milkdown/kit/plugin/indent'
import { trailing } from '@milkdown/kit/plugin/trailing'
import { listener, listenerCtx } from '@milkdown/kit/plugin/listener'
import { replaceAll, getMarkdown } from '@milkdown/kit/utils'

const props = defineProps({
  modelValue: {
    type: String,
    default: '',
  },
  readonly: {
    type: Boolean,
    default: false,
  },
  placeholder: {
    type: String,
    default: 'Start writing...',
  },
})

const emit = defineEmits(['update:modelValue'])

const editorRef = ref(null)
let editorInstance = null
let skipNextUpdate = false

onMounted(async () => {
  if (!editorRef.value) return

  editorInstance = await Editor.make()
    .config((ctx) => {
      ctx.set(defaultValueCtx, props.modelValue || '')
      ctx.set(editorViewOptionsCtx, {
        editable: () => !props.readonly,
        attributes: {
          class: 'milkdown-editor',
          'data-placeholder': props.placeholder,
        },
      })
      ctx.get(listenerCtx).markdownUpdated((_, markdown) => {
        if (skipNextUpdate) {
          skipNextUpdate = false
          return
        }
        emit('update:modelValue', markdown)
      })
    })
    .config((ctx) => {
      ctx.set(rootCtx, editorRef.value)
    })
    .use(commonmark)
    .use(history)
    .use(clipboard)
    .use(indent)
    .use(trailing)
    .use(listener)
    .create()
})

onBeforeUnmount(() => {
  if (editorInstance) {
    editorInstance.destroy()
    editorInstance = null
  }
})

watch(
  () => props.modelValue,
  (newVal) => {
    if (!editorInstance) return
    try {
      const current = editorInstance.action(getMarkdown())
      if (current !== newVal) {
        skipNextUpdate = true
        editorInstance.action(replaceAll(newVal || ''))
      }
    } catch {
      // Editor may not be ready yet
    }
  },
)
</script>

<style>
.milkdown-wrapper {
  @apply rounded-xl border border-lavender-200 dark:border-prussian-600 bg-white dark:bg-prussian-800;
}

.milkdown-wrapper:focus-within {
  @apply ring-2 ring-wisteria-400 border-wisteria-300;
}

.milkdown-readonly {
  @apply border-transparent bg-transparent;
}

.milkdown-readonly:focus-within {
  @apply ring-0 border-transparent;
}

.milkdown-editor {
  @apply px-4 py-3 text-sm text-prussian-500 dark:text-lavender-200 outline-none min-h-[120px];
}

.milkdown-readonly .milkdown-editor {
  @apply px-0 py-0 min-h-0;
}

/* Placeholder */
.milkdown-editor.ProseMirror-empty::before {
  content: attr(data-placeholder);
  @apply text-lavender-400 dark:text-lavender-500 pointer-events-none float-left h-0;
}

/* Typography */
.milkdown-editor h1 { @apply text-xl font-bold font-heading mb-3 mt-4 first:mt-0; }
.milkdown-editor h2 { @apply text-lg font-bold font-heading mb-2 mt-3 first:mt-0; }
.milkdown-editor h3 { @apply text-base font-semibold mb-2 mt-3 first:mt-0; }
.milkdown-editor p { @apply mb-2 last:mb-0 leading-relaxed; }
.milkdown-editor strong { @apply font-semibold; }
.milkdown-editor em { @apply italic; }
.milkdown-editor a { @apply text-wisteria-600 dark:text-wisteria-400 underline; }

/* Lists */
.milkdown-editor ul { @apply list-disc pl-6 mb-2; }
.milkdown-editor ol { @apply list-decimal pl-6 mb-2; }
.milkdown-editor li { @apply mb-1; }
.milkdown-editor li p { @apply mb-0; }

/* Code */
.milkdown-editor code {
  @apply text-xs bg-lavender-100 dark:bg-prussian-700 px-1.5 py-0.5 rounded font-mono;
}
.milkdown-editor pre {
  @apply bg-lavender-50 dark:bg-prussian-900 rounded-lg p-3 mb-2 overflow-x-auto;
}
.milkdown-editor pre code {
  @apply bg-transparent px-0 py-0;
}

/* Blockquote */
.milkdown-editor blockquote {
  @apply border-l-4 border-wisteria-300 dark:border-wisteria-600 pl-4 italic text-lavender-500 dark:text-lavender-400 mb-2;
}

/* Horizontal rule */
.milkdown-editor hr {
  @apply border-lavender-200 dark:border-prussian-600 my-4;
}
</style>
