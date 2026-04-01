<template>
  <div class="milkdown-wrapper" :class="{ 'milkdown-readonly': readonly }">
    <!-- Toolbar -->
    <div v-if="!readonly" class="milkdown-toolbar">
      <button type="button" title="Bold (Ctrl+B)" @click="toggleBold">
        <span class="font-bold">B</span>
      </button>
      <button type="button" title="Italic (Ctrl+I)" @click="toggleItalic">
        <span class="italic">I</span>
      </button>
      <span class="milkdown-toolbar-divider"></span>
      <button type="button" title="Heading 1" @click="toggleHeading(1)">
        <span class="font-bold text-[10px]">H1</span>
      </button>
      <button type="button" title="Heading 2" @click="toggleHeading(2)">
        <span class="font-bold text-[10px]">H2</span>
      </button>
      <button type="button" title="Heading 3" @click="toggleHeading(3)">
        <span class="font-bold text-[10px]">H3</span>
      </button>
      <span class="milkdown-toolbar-divider"></span>
      <button type="button" title="Bullet List" @click="toggleBulletList">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01" /></svg>
      </button>
      <button type="button" title="Numbered List" @click="toggleOrderedList">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h10M7 16h10M3 8V6l1-1h1v3M3 12h2l-2 2h2M3 18h1.5L3 16h2" /></svg>
      </button>
      <span class="milkdown-toolbar-divider"></span>
      <button type="button" title="Code" @click="toggleCode">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m10 20 4-16m4 4 4 4-4 4M6 16l-4-4 4-4" /></svg>
      </button>
      <button type="button" title="Blockquote" @click="toggleBlockquote">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v4a1 1 0 0 0 1 1h3v0a2 2 0 0 1-2 2H4M15 7v4a1 1 0 0 0 1 1h3v0a2 2 0 0 1-2 2h-1" /></svg>
      </button>
      <button type="button" title="Horizontal Rule" @click="insertHr">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M3 12h18" /></svg>
      </button>
    </div>
    <!-- Editor -->
    <div ref="editorRef" class="milkdown-editor-container"></div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, watch } from 'vue'
import { Editor, rootCtx, defaultValueCtx, editorViewOptionsCtx } from '@milkdown/kit/core'
import { commonmark, toggleStrongCommand, toggleEmphasisCommand, wrapInBlockquoteCommand, insertHrCommand } from '@milkdown/kit/preset/commonmark'
import { history } from '@milkdown/kit/plugin/history'
import { clipboard } from '@milkdown/kit/plugin/clipboard'
import { indent } from '@milkdown/kit/plugin/indent'
import { trailing } from '@milkdown/kit/plugin/trailing'
import { listener, listenerCtx } from '@milkdown/kit/plugin/listener'
import { replaceAll, getMarkdown, callCommand } from '@milkdown/kit/utils'

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

const runCommand = (command, payload) => {
  if (!editorInstance) return
  try {
    editorInstance.action(callCommand(command, payload))
  } catch {
    // Command may not be available
  }
}

const toggleBold = () => runCommand(toggleStrongCommand.key)
const toggleItalic = () => runCommand(toggleEmphasisCommand.key)
const toggleBlockquote = () => runCommand(wrapInBlockquoteCommand.key)
const insertHr = () => runCommand(insertHrCommand.key)

const toggleHeading = (level) => {
  if (!editorInstance) return
  // Insert heading by wrapping current line via markdown
  try {
    editorInstance.action((ctx) => {
      const view = ctx.get('editorView')
      if (!view) return
      const { state, dispatch } = view
      const { $from } = state.selection
      const lineStart = $from.start()
      const lineText = $from.parent.textContent
      const prefix = '#'.repeat(level) + ' '

      // Check if line already has this heading level
      const headingMatch = lineText.match(/^(#{1,6})\s/)
      if (headingMatch && headingMatch[1].length === level) {
        // Remove heading
        const tr = state.tr.replaceWith(lineStart, lineStart + headingMatch[0].length, [])
        dispatch(tr)
      } else if (headingMatch) {
        // Replace heading level
        const tr = state.tr.replaceWith(lineStart, lineStart + headingMatch[0].length, state.schema.text(prefix))
        dispatch(tr)
      } else {
        // Add heading
        const tr = state.tr.insertText(prefix, lineStart)
        dispatch(tr)
      }
    })
  } catch {
    // Fallback: just insert markdown
  }
}

const toggleBulletList = () => {
  if (!editorInstance) return
  try {
    editorInstance.action((ctx) => {
      const view = ctx.get('editorView')
      if (!view) return
      const { state, dispatch } = view
      const { $from } = state.selection
      const lineStart = $from.start()
      const lineText = $from.parent.textContent

      if (lineText.startsWith('- ')) {
        const tr = state.tr.replaceWith(lineStart, lineStart + 2, [])
        dispatch(tr)
      } else {
        const tr = state.tr.insertText('- ', lineStart)
        dispatch(tr)
      }
    })
  } catch {
    // fallback
  }
}

const toggleOrderedList = () => {
  if (!editorInstance) return
  try {
    editorInstance.action((ctx) => {
      const view = ctx.get('editorView')
      if (!view) return
      const { state, dispatch } = view
      const { $from } = state.selection
      const lineStart = $from.start()
      const lineText = $from.parent.textContent

      if (/^\d+\.\s/.test(lineText)) {
        const match = lineText.match(/^\d+\.\s/)
        const tr = state.tr.replaceWith(lineStart, lineStart + match[0].length, [])
        dispatch(tr)
      } else {
        const tr = state.tr.insertText('1. ', lineStart)
        dispatch(tr)
      }
    })
  } catch {
    // fallback
  }
}

const toggleCode = () => {
  if (!editorInstance) return
  try {
    editorInstance.action((ctx) => {
      const view = ctx.get('editorView')
      if (!view) return
      const { state, dispatch } = view
      const { from, to } = state.selection

      if (from === to) return
      const selectedText = state.doc.textBetween(from, to)
      const isCode = selectedText.startsWith('`') && selectedText.endsWith('`')
      const newText = isCode ? selectedText.slice(1, -1) : '`' + selectedText + '`'
      const tr = state.tr.replaceWith(from, to, state.schema.text(newText))
      dispatch(tr)
    })
  } catch {
    // fallback
  }
}

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

/* Toolbar */
.milkdown-toolbar {
  @apply flex items-center gap-0.5 px-2 py-1.5 border-b border-lavender-200 dark:border-prussian-600 bg-lavender-50/50 dark:bg-prussian-900/30;
}

.milkdown-toolbar button {
  @apply w-7 h-7 flex items-center justify-center rounded-md text-lavender-500 dark:text-lavender-400 hover:bg-lavender-200 dark:hover:bg-prussian-600 hover:text-prussian-500 dark:hover:text-lavender-200 transition-colors text-xs;
}

.milkdown-toolbar-divider {
  @apply w-px h-4 bg-lavender-200 dark:bg-prussian-600 mx-1;
}

/* Editor */
.milkdown-editor {
  @apply px-4 py-3 text-sm text-prussian-500 dark:text-lavender-200 outline-none min-h-[160px];
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
