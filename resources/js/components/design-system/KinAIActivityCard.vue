<!--
  KinAIActivityCard — expandable AI action card.
  @see /design-system/ai-activity-card  (docs/design/COMPONENT_ROADMAP.md §5.10)
  Props: title, status, duration, trigger, steps, expanded (v-model), outputAccent
  Slots: #output (custom output preview block)
  Emits: update:expanded, dismiss, rerun, view-full

  Status = 'success' | 'failed' | 'pending' | 'info'. Drives status dot + duration chip.
  When `expanded` is true, reveals Trigger / Steps / Output / Actions sections.
-->
<script setup>
import { computed } from 'vue'
import {
  ChevronUpIcon, ChevronDownIcon, SparklesIcon,
  CheckCircleIcon, XMarkIcon, ArrowPathIcon, ArrowTopRightOnSquareIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  title:    { type: String, required: true },
  status:   { type: String, default: 'success', validator: (v) => ['success','failed','pending','info'].includes(v) },
  duration: { type: String, default: '' },
  trigger:  { type: String, default: '' },
  /** Step objects: { label, icon?, duration? } */
  steps:    { type: Array, default: () => [] },
  /** v-model:expanded */
  expanded: { type: Boolean, default: false },
  /** Accent color for the output preview block bg. */
  outputAccent: { type: String, default: 'lavender', validator: (v) => ['lavender','peach','mint','sun'].includes(v) },
})

const emit = defineEmits(['update:expanded', 'dismiss', 'rerun', 'view-full'])

function toggle() { emit('update:expanded', !props.expanded) }

const statusLabel = computed(() => ({
  success: 'Done',
  failed:  'Failed',
  pending: 'Running',
  info:    'Info',
}[props.status]))
</script>

<template>
  <div
    class="kin-ai-act rounded-[20px] border overflow-hidden bg-surface-raised border-border-subtle"
    :class="status === 'pending' && 'kin-ai-act--pending'"
  >
    <!-- Collapsed row (always visible as header) -->
    <button
      type="button"
      class="kin-ai-act__row w-full flex items-center gap-3 px-4 py-3 text-left"
      @click="toggle"
    >
      <span class="kin-ai-act__dot w-2 h-2 rounded-full flex-shrink-0" :class="`kin-ai-act__dot--${status}`"></span>
      <p class="text-sm font-medium flex-1 truncate text-ink-primary">{{ title }}</p>
      <span
        v-if="duration"
        class="kin-ai-act__chip text-[11px] font-medium px-2 py-0.5 rounded-full flex-shrink-0"
        :class="`kin-ai-act__chip--${status}`"
      >{{ duration }} · {{ statusLabel }}</span>
      <ChevronUpIcon v-if="expanded" class="w-4 h-4 flex-shrink-0 text-ink-tertiary" />
      <ChevronDownIcon v-else class="w-4 h-4 flex-shrink-0 text-ink-tertiary" />
    </button>

    <!-- Expanded body -->
    <div v-if="expanded" class="border-t border-border-subtle">
      <div v-if="trigger" class="px-5 pt-5 pb-4 border-b border-border-subtle">
        <p class="text-[11px] font-semibold uppercase tracking-widest mb-2 text-ink-tertiary">Trigger</p>
        <p class="text-sm italic flex items-start gap-2 text-ink-secondary">
          <SparklesIcon class="w-4 h-4 flex-shrink-0 mt-0.5 kin-ai-act__sparkle" />
          {{ trigger }}
        </p>
      </div>

      <div v-if="steps.length" class="px-5 pt-4 pb-4 border-b border-border-subtle">
        <p class="text-[11px] font-semibold uppercase tracking-widest mb-3 text-ink-tertiary">Steps</p>
        <ul class="space-y-2">
          <li v-for="(step, i) in steps" :key="i" class="flex items-center gap-2.5">
            <CheckCircleIcon class="w-4 h-4 flex-shrink-0 kin-ai-act__step-check" />
            <component :is="step.icon" v-if="step.icon" class="w-4 h-4 flex-shrink-0 text-ink-tertiary" />
            <span class="text-sm font-mono flex-1 text-ink-primary">{{ step.label }}</span>
            <span v-if="step.duration" class="text-[11px] text-ink-tertiary">{{ step.duration }}</span>
          </li>
        </ul>
      </div>

      <div v-if="$slots.output" class="px-5 pt-4 pb-4 border-b border-border-subtle">
        <p class="text-[11px] font-semibold uppercase tracking-widest mb-3 text-ink-tertiary">Output</p>
        <div class="kin-ai-act__output rounded-xl px-4 py-3" :class="`kin-ai-act__output--${outputAccent}`">
          <slot name="output"></slot>
        </div>
      </div>

      <div class="px-5 py-3 flex items-center gap-2">
        <button type="button" class="kin-ai-act__btn flex items-center gap-1.5 text-sm font-medium px-3 py-1.5 rounded-lg border" @click="$emit('dismiss')">
          <XMarkIcon class="w-4 h-4" /> Dismiss
        </button>
        <button type="button" class="kin-ai-act__btn flex items-center gap-1.5 text-sm font-medium px-3 py-1.5 rounded-lg border" @click="$emit('rerun')">
          <ArrowPathIcon class="w-4 h-4" /> Re-run
        </button>
        <button type="button" class="kin-ai-act__btn-primary flex items-center gap-1.5 text-sm font-medium px-3 py-1.5 rounded-lg ml-auto" @click="$emit('view-full')">
          <ArrowTopRightOnSquareIcon class="w-4 h-4" /> View full
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.kin-ai-act { box-shadow: var(--shadow-resting); }
.kin-ai-act--pending { border-style: dashed; border-color: rgb(var(--status-pending)); }

.kin-ai-act__row { background: transparent; border: none; cursor: pointer; }

.kin-ai-act__dot--success { background: rgb(var(--status-success)); }
.kin-ai-act__dot--failed  { background: rgb(var(--status-failed)); }
.kin-ai-act__dot--pending { background: rgb(var(--status-pending)); }
.kin-ai-act__dot--info    { background: rgb(var(--status-info)); }

.kin-ai-act__chip--success { background: rgb(var(--status-success) / 0.15); color: rgb(var(--status-success)); }
.kin-ai-act__chip--failed  { background: rgb(var(--status-failed) / 0.15);  color: rgb(var(--status-failed)); }
.kin-ai-act__chip--pending { background: rgb(var(--status-pending) / 0.15); color: rgb(var(--status-pending)); }
.kin-ai-act__chip--info    { background: rgb(var(--status-info) / 0.15);    color: rgb(var(--status-info)); }

.kin-ai-act__sparkle     { color: rgb(var(--accent-lavender-bold)); }
.kin-ai-act__step-check  { color: rgb(var(--status-success)); }

.kin-ai-act__output--lavender { background: rgb(var(--accent-lavender-soft)); }
.kin-ai-act__output--peach    { background: rgb(var(--accent-peach-soft)); }
.kin-ai-act__output--mint     { background: rgb(var(--accent-mint-soft)); }
.kin-ai-act__output--sun      { background: rgb(var(--accent-sun-soft)); }

.kin-ai-act__btn {
  border-color: rgb(var(--border-subtle));
  color: rgb(var(--ink-secondary));
  background: transparent;
  cursor: pointer;
}
.kin-ai-act__btn:hover { background: rgb(var(--surface-sunken)); color: rgb(var(--ink-primary)); }

.kin-ai-act__btn-primary {
  background: rgb(var(--accent-lavender-bold));
  color: rgb(var(--ink-inverse));
  border: none;
  cursor: pointer;
}
</style>
