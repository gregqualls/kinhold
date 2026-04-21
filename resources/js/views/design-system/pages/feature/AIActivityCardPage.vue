<script setup>
import { ref } from 'vue'
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import KinAIActivityCard from '@/components/design-system/KinAIActivityCard.vue'

// Kin preview state
const kinExpL = ref(true)
const kinExpD = ref(true)
import {
  SparklesIcon, CheckCircleIcon, XCircleIcon, ClockIcon,
  ChevronDownIcon, ChevronUpIcon, ArrowPathIcon, XMarkIcon,
  ArrowTopRightOnSquareIcon, MagnifyingGlassIcon, PlusCircleIcon,
  BellAlertIcon,
} from '@heroicons/vue/24/outline'

// ── Palette ───────────────────────────────────────────────────────────────────
const L = {
  surfaceApp: '#FAF8F5', surfaceRaised: '#FFFFFF', surfaceSunken: '#F5F2EE',
  inkPrimary: '#1C1C1E', inkSecondary: '#6B6966', inkTertiary: '#9C9895', inkInverse: '#FAF8F5',
  borderSubtle: '#E8E4DF', borderStrong: '#BCB8B2',
  accents: { lavender: { soft: '#EAE6F8', bold: '#6856B2' }, peach: { soft: '#FCE9E0', bold: '#BA562E' }, mint: { soft: '#D5F2E8', bold: '#2E8A62' }, sun: { soft: '#FCF3D2', bold: '#A2780C' } },
  status: {
    success: { soft: '#E1F0E7', bold: '#4D8C6A' },
    pending: { soft: '#E2EBF6', bold: '#486E9C' },
    failed:  { soft: '#F4DADA', bold: '#BA4A4A' },
  },
}
const D = {
  surfaceApp: '#141311', surfaceRaised: '#1C1B19', surfaceSunken: '#161513', surfaceOverlay: '#242220',
  inkPrimary: '#F0EDE9', inkSecondary: '#A09C97', inkTertiary: '#6E6B67', inkInverse: '#1C1C1E',
  borderSubtle: '#2C2A27', borderStrong: '#403E3A',
  accents: { lavender: { soft: '#302A48', bold: '#B6A8E6' }, peach: { soft: '#3E241A', bold: '#F0A882' }, mint: { soft: '#18342A', bold: '#7CD6AE' }, sun: { soft: '#342C0A', bold: '#E6C452' } },
  status: {
    success: { soft: '#1C3A2A', bold: '#6CC498' },
    pending: { soft: '#1E2E42', bold: '#78A4DC' },
    failed:  { soft: '#3C1E1E', bold: '#E67070' },
  },
}

const SH_LT = '0 1px 2px rgba(28,20,10,0.04), 0 2px 6px rgba(28,20,10,0.05)'
const SH_DK = '0 1px 2px rgba(0,0,0,0.30), 0 2px 6px rgba(0,0,0,0.25)'

// Expand state per variant × mode
const expA_lt = ref(true);  const expA_dk = ref(true)
const expB_lt = ref(true);  const expB_dk = ref(true)
const expC_lt = ref(true);  const expC_dk = ref(true)

// Tool-call data used by all variants
const STEPS = [
  { icon: MagnifyingGlassIcon, label: 'search_family_members', dur: '0.18s' },
  { icon: PlusCircleIcon,      label: 'create_task',           dur: '0.31s' },
  { icon: BellAlertIcon,       label: 'notify_user',           dur: '0.24s' },
]
</script>

<template>
  <ComponentPage
    title="5.10 AIActivityCard"
    description="The surface where the AI's background work becomes visible. Shows what triggered the assistant, which tools ran, the output, and actions to act on the result. Kinhold's AI is a reactive copilot in the margins — this card is how that work surfaces outside of chat."
    status="chosen"
  >

    <!-- ═══════════════════════════════════════════════════════════════
         VARIANT A — Stacked rail
         ════════════════════════════════════════════════════════════════ -->
    <section class="mb-20">
      <VariantFrame label="AIActivityCard" caption="Stacked rail — collapsed row with chevron; expanded pushes Trigger → Steps → Output → Actions in a clean magazine column. The single locked AIActivityCard shape.">
        <div class="w-full space-y-10">

          <!-- ── LIGHT PANEL ── -->
          <div class="rounded-2xl border p-6 space-y-6" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>

            <div class="space-y-3 max-w-[560px]">

              <!-- COLLAPSED ROW -->
              <div
                class="rounded-[20px] border px-4 py-3 flex items-center gap-3 cursor-pointer select-none"
                :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle, boxShadow: SH_LT }"
                @click="expA_lt = !expA_lt"
              >
                <!-- status dot -->
                <span class="w-2 h-2 rounded-full flex-shrink-0" :style="{ background: L.status.success.bold }" />
                <!-- title -->
                <p class="text-sm font-medium flex-1 truncate" :style="{ color: L.inkPrimary }">
                  Created task "Take out trash" for Emma
                </p>
                <!-- duration chip -->
                <span
                  class="text-[11px] font-medium px-2 py-0.5 rounded-full flex-shrink-0"
                  :style="{ background: L.status.success.soft, color: L.status.success.bold }"
                >1.2s · Done</span>
                <!-- chevron -->
                <ChevronUpIcon v-if="expA_lt" class="w-4 h-4 flex-shrink-0" :style="{ color: L.inkTertiary }" />
                <ChevronDownIcon v-else class="w-4 h-4 flex-shrink-0" :style="{ color: L.inkTertiary }" />
              </div>

              <!-- EXPANDED STATE -->
              <div
                v-if="expA_lt"
                class="rounded-[20px] border overflow-hidden"
                :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle, boxShadow: SH_LT }"
              >
                <!-- Trigger -->
                <div class="px-5 pt-5 pb-4 border-b" :style="{ borderColor: L.borderSubtle }">
                  <p class="text-[11px] font-semibold uppercase tracking-widest mb-2" :style="{ color: L.inkTertiary }">Trigger</p>
                  <p class="text-sm italic flex items-start gap-2" :style="{ color: L.inkSecondary }">
                    <SparklesIcon class="w-4 h-4 flex-shrink-0 mt-0.5" :style="{ color: L.accents.lavender.bold }" />
                    "Add a task for Emma tomorrow to take out trash"
                  </p>
                </div>

                <!-- Steps -->
                <div class="px-5 pt-4 pb-4 border-b" :style="{ borderColor: L.borderSubtle }">
                  <p class="text-[11px] font-semibold uppercase tracking-widest mb-3" :style="{ color: L.inkTertiary }">Steps</p>
                  <ul class="space-y-2">
                    <li v-for="step in STEPS" :key="step.label" class="flex items-center gap-2.5">
                      <CheckCircleIcon class="w-4 h-4 flex-shrink-0" :style="{ color: L.status.success.bold }" />
                      <component :is="step.icon" class="w-4 h-4 flex-shrink-0" :style="{ color: L.inkTertiary }" />
                      <span class="text-sm font-mono flex-1" :style="{ color: L.inkPrimary }">{{ step.label }}</span>
                      <span class="text-[11px]" :style="{ color: L.inkTertiary }">{{ step.dur }}</span>
                    </li>
                  </ul>
                </div>

                <!-- Output preview -->
                <div class="px-5 pt-4 pb-4 border-b" :style="{ borderColor: L.borderSubtle }">
                  <p class="text-[11px] font-semibold uppercase tracking-widest mb-3" :style="{ color: L.inkTertiary }">Output</p>
                  <div class="rounded-xl px-4 py-3" :style="{ background: L.accents.lavender.soft }">
                    <div class="flex items-start justify-between gap-2">
                      <div>
                        <p class="text-sm font-semibold" :style="{ color: L.inkPrimary }">Take out trash</p>
                        <p class="text-[11px] mt-0.5" :style="{ color: L.inkSecondary }">Assigned to Emma · Due tomorrow</p>
                      </div>
                      <span
                        class="text-[11px] font-medium px-2 py-0.5 rounded-full flex-shrink-0"
                        :style="{ background: L.accents.sun.soft, color: L.accents.sun.bold }"
                      >+5 pts</span>
                    </div>
                  </div>
                </div>

                <!-- Actions row -->
                <div class="px-5 py-3 flex items-center gap-2">
                  <button
                    class="flex items-center gap-1.5 text-sm font-medium px-3 py-1.5 rounded-lg border transition-colors"
                    :style="{ borderColor: L.borderSubtle, color: L.inkSecondary, background: 'transparent' }"
                  >
                    <XMarkIcon class="w-4 h-4" />
                    Dismiss
                  </button>
                  <button
                    class="flex items-center gap-1.5 text-sm font-medium px-3 py-1.5 rounded-lg border transition-colors"
                    :style="{ borderColor: L.borderSubtle, color: L.inkSecondary, background: 'transparent' }"
                  >
                    <ArrowPathIcon class="w-4 h-4" />
                    Re-run
                  </button>
                  <button
                    class="flex items-center gap-1.5 text-sm font-medium px-3 py-1.5 rounded-lg ml-auto"
                    :style="{ background: L.accents.lavender.bold, color: L.inkInverse }"
                  >
                    <ArrowTopRightOnSquareIcon class="w-4 h-4" />
                    View full
                  </button>
                </div>
              </div>

              <!-- IN-PROGRESS STATE -->
              <div
                class="rounded-[20px] border px-4 py-3 flex items-center gap-3"
                style="border-style: dashed;"
                :style="{ background: L.surfaceRaised, borderColor: L.status.pending.bold }"
              >
                <!-- spinner -->
                <svg class="w-4 h-4 flex-shrink-0 animate-spin" viewBox="0 0 24 24" fill="none" :style="{ color: L.status.pending.bold }">
                  <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" stroke-dasharray="31.4" stroke-dashoffset="10" stroke-linecap="round" />
                </svg>
                <p class="text-sm font-medium flex-1" :style="{ color: L.inkPrimary }">Scheduling family movie night…</p>
                <span
                  class="text-[11px] font-medium px-2 py-0.5 rounded-full flex-shrink-0"
                  :style="{ background: L.status.pending.soft, color: L.status.pending.bold }"
                >Running · 2 done · 1 in progress</span>
              </div>

              <!-- FAILED STATE -->
              <div
                class="rounded-[20px] border px-4 py-3 flex items-center gap-3"
                :style="{ background: L.surfaceRaised, borderColor: L.status.failed.bold }"
              >
                <XCircleIcon class="w-4 h-4 flex-shrink-0" :style="{ color: L.status.failed.bold }" />
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-medium truncate" :style="{ color: L.inkPrimary }">Add event "Book club" to calendar</p>
                  <p class="text-[11px] mt-0.5" :style="{ color: L.status.failed.bold }">Calendar connection not authorised — reconnect Google Calendar in Settings.</p>
                </div>
                <span
                  class="text-[11px] font-medium px-2 py-0.5 rounded-full flex-shrink-0"
                  :style="{ background: L.status.failed.soft, color: L.status.failed.bold }"
                >Failed</span>
              </div>

            </div>
          </div>

          <!-- ── DARK PANEL ── -->
          <div class="rounded-2xl border p-6 space-y-6" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

            <div class="space-y-3 max-w-[560px]">

              <!-- COLLAPSED ROW -->
              <div
                class="rounded-[20px] border px-4 py-3 flex items-center gap-3 cursor-pointer select-none"
                :style="{ background: D.surfaceRaised, borderColor: D.borderSubtle, boxShadow: SH_DK }"
                @click="expA_dk = !expA_dk"
              >
                <span class="w-2 h-2 rounded-full flex-shrink-0" :style="{ background: D.status.success.bold }" />
                <p class="text-sm font-medium flex-1 truncate" :style="{ color: D.inkPrimary }">
                  Created task "Take out trash" for Emma
                </p>
                <span
                  class="text-[11px] font-medium px-2 py-0.5 rounded-full flex-shrink-0"
                  :style="{ background: D.status.success.soft, color: D.status.success.bold }"
                >1.2s · Done</span>
                <ChevronUpIcon v-if="expA_dk" class="w-4 h-4 flex-shrink-0" :style="{ color: D.inkTertiary }" />
                <ChevronDownIcon v-else class="w-4 h-4 flex-shrink-0" :style="{ color: D.inkTertiary }" />
              </div>

              <!-- EXPANDED STATE -->
              <div
                v-if="expA_dk"
                class="rounded-[20px] border overflow-hidden"
                :style="{ background: D.surfaceRaised, borderColor: D.borderSubtle, boxShadow: SH_DK }"
              >
                <div class="px-5 pt-5 pb-4 border-b" :style="{ borderColor: D.borderSubtle }">
                  <p class="text-[11px] font-semibold uppercase tracking-widest mb-2" :style="{ color: D.inkTertiary }">Trigger</p>
                  <p class="text-sm italic flex items-start gap-2" :style="{ color: D.inkSecondary }">
                    <SparklesIcon class="w-4 h-4 flex-shrink-0 mt-0.5" :style="{ color: D.accents.lavender.bold }" />
                    "Add a task for Emma tomorrow to take out trash"
                  </p>
                </div>

                <div class="px-5 pt-4 pb-4 border-b" :style="{ borderColor: D.borderSubtle }">
                  <p class="text-[11px] font-semibold uppercase tracking-widest mb-3" :style="{ color: D.inkTertiary }">Steps</p>
                  <ul class="space-y-2">
                    <li v-for="step in STEPS" :key="step.label" class="flex items-center gap-2.5">
                      <CheckCircleIcon class="w-4 h-4 flex-shrink-0" :style="{ color: D.status.success.bold }" />
                      <component :is="step.icon" class="w-4 h-4 flex-shrink-0" :style="{ color: D.inkTertiary }" />
                      <span class="text-sm font-mono flex-1" :style="{ color: D.inkPrimary }">{{ step.label }}</span>
                      <span class="text-[11px]" :style="{ color: D.inkTertiary }">{{ step.dur }}</span>
                    </li>
                  </ul>
                </div>

                <div class="px-5 pt-4 pb-4 border-b" :style="{ borderColor: D.borderSubtle }">
                  <p class="text-[11px] font-semibold uppercase tracking-widest mb-3" :style="{ color: D.inkTertiary }">Output</p>
                  <div class="rounded-xl px-4 py-3" :style="{ background: D.accents.lavender.soft }">
                    <div class="flex items-start justify-between gap-2">
                      <div>
                        <p class="text-sm font-semibold" :style="{ color: D.inkPrimary }">Take out trash</p>
                        <p class="text-[11px] mt-0.5" :style="{ color: D.inkSecondary }">Assigned to Emma · Due tomorrow</p>
                      </div>
                      <span
                        class="text-[11px] font-medium px-2 py-0.5 rounded-full flex-shrink-0"
                        :style="{ background: D.accents.sun.soft, color: D.accents.sun.bold }"
                      >+5 pts</span>
                    </div>
                  </div>
                </div>

                <div class="px-5 py-3 flex items-center gap-2">
                  <button
                    class="flex items-center gap-1.5 text-sm font-medium px-3 py-1.5 rounded-lg border transition-colors"
                    :style="{ borderColor: D.borderSubtle, color: D.inkSecondary, background: 'transparent' }"
                  >
                    <XMarkIcon class="w-4 h-4" />
                    Dismiss
                  </button>
                  <button
                    class="flex items-center gap-1.5 text-sm font-medium px-3 py-1.5 rounded-lg border transition-colors"
                    :style="{ borderColor: D.borderSubtle, color: D.inkSecondary, background: 'transparent' }"
                  >
                    <ArrowPathIcon class="w-4 h-4" />
                    Re-run
                  </button>
                  <button
                    class="flex items-center gap-1.5 text-sm font-medium px-3 py-1.5 rounded-lg ml-auto"
                    :style="{ background: D.accents.lavender.bold, color: D.inkInverse }"
                  >
                    <ArrowTopRightOnSquareIcon class="w-4 h-4" />
                    View full
                  </button>
                </div>
              </div>

              <!-- IN-PROGRESS STATE -->
              <div
                class="rounded-[20px] border px-4 py-3 flex items-center gap-3"
                style="border-style: dashed;"
                :style="{ background: D.surfaceRaised, borderColor: D.status.pending.bold }"
              >
                <svg class="w-4 h-4 flex-shrink-0 animate-spin" viewBox="0 0 24 24" fill="none" :style="{ color: D.status.pending.bold }">
                  <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" stroke-dasharray="31.4" stroke-dashoffset="10" stroke-linecap="round" />
                </svg>
                <p class="text-sm font-medium flex-1" :style="{ color: D.inkPrimary }">Scheduling family movie night…</p>
                <span
                  class="text-[11px] font-medium px-2 py-0.5 rounded-full flex-shrink-0"
                  :style="{ background: D.status.pending.soft, color: D.status.pending.bold }"
                >Running · 2 done · 1 in progress</span>
              </div>

              <!-- FAILED STATE -->
              <div
                class="rounded-[20px] border px-4 py-3 flex items-center gap-3"
                :style="{ background: D.surfaceRaised, borderColor: D.status.failed.bold }"
              >
                <XCircleIcon class="w-4 h-4 flex-shrink-0" :style="{ color: D.status.failed.bold }" />
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-medium truncate" :style="{ color: D.inkPrimary }">Add event "Book club" to calendar</p>
                  <p class="text-[11px] mt-0.5" :style="{ color: D.status.failed.bold }">Calendar connection not authorised — reconnect Google Calendar in Settings.</p>
                </div>
                <span
                  class="text-[11px] font-medium px-2 py-0.5 rounded-full flex-shrink-0"
                  :style="{ background: D.status.failed.soft, color: D.status.failed.bold }"
                >Failed</span>
              </div>

            </div>
          </div>
        </div>
      </VariantFrame>
    </section>

    <!-- ═══════════════════════════════════════════════════════════════
         CLAUDE'S PICK CALLOUT
         ════════════════════════════════════════════════════════════════ -->
    <section class="mb-20">
      <div
        class="rounded-2xl border p-6 flex gap-4 items-start"
        :style="{ background: L.accents.lavender.soft, borderColor: '#C0B4E8' }"
      >
        <SparklesIcon class="w-5 h-5 flex-shrink-0 mt-0.5" :style="{ color: L.accents.lavender.bold }" />
        <div>
          <p class="text-sm font-semibold mb-1" :style="{ color: L.accents.lavender.bold }">
            LOCKED — single shape, no variants
          </p>
          <p class="text-sm leading-relaxed" :style="{ color: L.inkPrimary }">
            Variant A is the right default for Kinhold's feed because the AI is a "reactive copilot in the margins" — its work surfaces as lightweight rows that collapse into the stream without demanding attention. The stacked layout makes every state (collapsed, expanded, in-progress, failed) scannable in a single scroll, and the lavender-soft output card immediately signals "AI-generated, review this" without requiring a second surface. Variants B and C are additive: B unlocks density for right-rail panels where horizontal space is plentiful, and C earns its complexity only in developer or power-user audit contexts where step-by-step traceability matters more than scan speed.
          </p>
        </div>
      </div>
    </section>

    <!-- ═══════════════════════════════════════════════════════════════
         USAGE GUIDE
         ════════════════════════════════════════════════════════════════ -->
    <section class="mb-10">
      <div
        class="rounded-2xl border divide-y"
        :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle }"
      >
        <div class="px-6 py-4">
          <h2 class="text-base font-semibold" :style="{ color: L.inkPrimary }">Usage guide</h2>
          <p class="text-sm mt-1" :style="{ color: L.inkSecondary }">
            Three progressive layouts for the same data — choose the one that fits the surface's available space and the user's need for detail.
          </p>
        </div>

        <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-[140px_1fr] gap-3">
          <div>
            <span
              class="inline-flex items-center justify-center h-6 px-2 rounded-full text-xs font-semibold"
              :style="{ background: L.inkPrimary, color: L.inkInverse }"
            >A — Default feed</span>
          </div>
          <div class="space-y-1">
            <p class="text-sm font-medium" :style="{ color: L.inkPrimary }">Activity feed, dashboard notifications, chat sidebar</p>
            <p class="text-sm" :style="{ color: L.inkSecondary }">
              Use for any vertically-stacked list of AI activity where the user scans rather than drills in. The collapsed row is the most common state — expand only on demand. Works at 375px with no layout changes.
            </p>
          </div>
        </div>

        <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-[140px_1fr] gap-3">
          <div>
            <span
              class="inline-flex items-center justify-center h-6 px-2 rounded-full text-xs font-semibold"
              :style="{ background: L.inkPrimary, color: L.inkInverse }"
            >B — Right-rail panel</span>
          </div>
          <div class="space-y-1">
            <p class="text-sm font-medium" :style="{ color: L.inkPrimary }">AI assistant sidebar, contextual copilot drawer</p>
            <p class="text-sm" :style="{ color: L.inkSecondary }">
              Use when the card lives in a right-rail panel 320px+ wide and you want the user to see steps and output simultaneously without scrolling. The 60/40 split collapses automatically to a single column below the md breakpoint.
            </p>
          </div>
        </div>

        <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-[140px_1fr] gap-3">
          <div>
            <span
              class="inline-flex items-center justify-center h-6 px-2 rounded-full text-xs font-semibold"
              :style="{ background: L.inkPrimary, color: L.inkInverse }"
            >C — Audit / trace</span>
          </div>
          <div class="space-y-1">
            <p class="text-sm font-medium" :style="{ color: L.inkPrimary }">MCP activity log, developer debug panel, parent oversight view</p>
            <p class="text-sm" :style="{ color: L.inkSecondary }">
              Use when each step in the tool-call chain needs to be legible as an ordered event — e.g. a parent reviewing what the AI did on behalf of a child, or a developer debugging an MCP tool sequence. The timeline connector line makes sequence and causality explicit at a glance.
            </p>
          </div>
        </div>

        <div class="px-6 py-4">
          <p class="text-xs font-semibold uppercase tracking-widest mb-2" :style="{ color: L.inkTertiary }">State colour convention</p>
          <div class="flex flex-wrap gap-3">
            <div
              v-for="(entry, key) in L.status"
              :key="key"
              class="flex items-center gap-2 rounded-lg px-3 py-2"
              :style="{ background: entry.soft }"
            >
              <div class="w-2.5 h-2.5 rounded-full flex-shrink-0" :style="{ background: entry.bold }" />
              <span class="text-xs font-medium capitalize" :style="{ color: entry.bold }">
                {{ key === 'success' ? 'Done' : key === 'pending' ? 'In progress' : 'Failed' }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </section>


    <!-- KIN COMPONENT PREVIEW -->
    <section class="mb-16">
      <VariantFrame label="Kin" caption="KinAIActivityCard — proposed extraction. Click the header to toggle expanded/collapsed.">
        <div class="w-full space-y-10">
          <div class="rounded-2xl border p-6 space-y-3" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest mb-4" :style="{ color: L.inkTertiary }">Light mode</p>
            <KinAIActivityCard
              title="Created task &quot;Take out trash&quot; for Emma"
              status="success"
              duration="1.2s"
              trigger="&quot;Add a task for Emma tomorrow to take out trash&quot;"
              :steps="[
                { label: 'list_users — fetched 5 family members' },
                { label: 'create_task — assigned to Emma, due tomorrow' },
                { label: 'notify_user — push sent to Emma' },
              ]"
              v-model:expanded="kinExpL"
            >
              <template #output>
                <div class="flex items-start justify-between gap-2">
                  <div>
                    <p class="text-sm font-semibold text-ink-primary">Take out trash</p>
                    <p class="text-[11px] mt-0.5 text-ink-secondary">Assigned to Emma · Due tomorrow</p>
                  </div>
                  <span class="text-[11px] font-medium px-2 py-0.5 rounded-full flex-shrink-0" :style="{ background: L.accents.sun.soft, color: L.accents.sun.bold }">+5 pts</span>
                </div>
              </template>
            </KinAIActivityCard>
            <KinAIActivityCard title="Fetching vault entry for car insurance" status="pending" duration="3.8s" />
            <KinAIActivityCard title="Meal plan generation failed — missing ingredients" status="failed" duration="4.2s" />
          </div>

          <div class="dark rounded-2xl border p-6 space-y-3" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest mb-4" :style="{ color: D.inkTertiary }">Dark mode</p>
            <KinAIActivityCard
              title="Created task &quot;Take out trash&quot; for Emma"
              status="success"
              duration="1.2s"
              trigger="&quot;Add a task for Emma tomorrow to take out trash&quot;"
              :steps="[
                { label: 'list_users — fetched 5 family members' },
                { label: 'create_task — assigned to Emma, due tomorrow' },
                { label: 'notify_user — push sent to Emma' },
              ]"
              v-model:expanded="kinExpD"
            >
              <template #output>
                <div class="flex items-start justify-between gap-2">
                  <div>
                    <p class="text-sm font-semibold text-ink-primary">Take out trash</p>
                    <p class="text-[11px] mt-0.5 text-ink-secondary">Assigned to Emma · Due tomorrow</p>
                  </div>
                  <span class="text-[11px] font-medium px-2 py-0.5 rounded-full flex-shrink-0" :style="{ background: D.accents.sun.soft, color: D.accents.sun.bold }">+5 pts</span>
                </div>
              </template>
            </KinAIActivityCard>
            <KinAIActivityCard title="Fetching vault entry for car insurance" status="pending" duration="3.8s" />
            <KinAIActivityCard title="Meal plan generation failed — missing ingredients" status="failed" duration="4.2s" />
          </div>
        </div>
      </VariantFrame>
    </section>

  </ComponentPage>
</template>
