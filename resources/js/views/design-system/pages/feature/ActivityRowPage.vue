<script setup>
import { ref } from 'vue'
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import {
  SparklesIcon, CheckCircleIcon, XCircleIcon, ClockIcon, PauseIcon,
  ChevronDownIcon, ChevronRightIcon, HandThumbUpIcon, ChatBubbleLeftIcon,
  ChatBubbleBottomCenterTextIcon, CheckIcon,
  CheckBadgeIcon, BoltIcon, TrashIcon, StarIcon, GiftIcon, TrophyIcon,
} from '@heroicons/vue/24/outline'

// ── Palette ───────────────────────────────────────────────────────────────────
const L = {
  surfaceApp: '#FAF8F5', surfaceRaised: '#FFFFFF', surfaceSunken: '#F5F2EE',
  inkPrimary: '#1C1C1E', inkSecondary: '#6B6966', inkTertiary: '#9C9895', inkInverse: '#FAF8F5',
  borderSubtle: '#E8E4DF', borderStrong: '#BCB8B2',
  accents: {
    lavender: { soft: '#EAE6F8', bold: '#6856B2' },
    peach:    { soft: '#FCE9E0', bold: '#BA562E' },
    mint:     { soft: '#D5F2E8', bold: '#2E8A62' },
    sun:      { soft: '#FCF3D2', bold: '#A2780C' },
  },
  status: {
    success: { soft: '#E1F0E7', bold: '#4D8C6A' },
    pending: { soft: '#E2EBF6', bold: '#486E9C' },
    paused:  { soft: '#F5E8D4', bold: '#BE8230' },
    failed:  { soft: '#F4DADA', bold: '#BA4A4A' },
  },
}
const D = {
  surfaceApp: '#141311', surfaceRaised: '#1C1B19', surfaceSunken: '#161513', surfaceOverlay: '#242220',
  inkPrimary: '#F0EDE9', inkSecondary: '#A09C97', inkTertiary: '#6E6B67', inkInverse: '#1C1C1E',
  borderSubtle: '#2C2A27', borderStrong: '#403E3A',
  accents: {
    lavender: { soft: '#302A48', bold: '#B6A8E6' },
    peach:    { soft: '#3E241A', bold: '#F0A882' },
    mint:     { soft: '#18342A', bold: '#7CD6AE' },
    sun:      { soft: '#342C0A', bold: '#E6C452' },
  },
  status: {
    success: { soft: '#1C3A2A', bold: '#6CC498' },
    pending: { soft: '#1E2E42', bold: '#78A4DC' },
    paused:  { soft: '#3C2F14', bold: '#DCA848' },
    failed:  { soft: '#3C1E1E', bold: '#E67070' },
  },
}

// ── Variant B expand state ─────────────────────────────────────────────────────
const expandedB_lt = ref(true)
const expandedB_dk = ref(true)

// ── Variant A feed data ────────────────────────────────────────────────────────
const feedA = [
  { id: 1, icon: CheckBadgeIcon, iconAccent: 'mint',    title: "Greg completed 'Take out trash'",          meta: '5 pts',  status: 'success', statusLabel: 'Done',    time: '2m ago' },
  { id: 2, icon: BoltIcon,       iconAccent: 'sun',     title: "Emma completed 'Math homework'",           meta: '10 pts', status: 'success', statusLabel: 'Done',    time: '14m ago' },
  { id: 3, icon: ClockIcon,      iconAccent: 'pending', title: "Ava — 'Clean bedroom' assigned",           meta: null,     status: 'pending', statusLabel: 'Pending', time: '1h ago' },
  { id: 4, icon: PauseIcon,      iconAccent: 'paused',  title: "Weekly meal plan — waiting on review",     meta: null,     status: 'paused',  statusLabel: 'Paused',  time: '3h ago' },
  { id: 5, icon: XCircleIcon,    iconAccent: 'failed',  title: "Calendar sync with Google — timed out",   meta: null,     status: 'failed',  statusLabel: 'Failed',  time: '5h ago' },
]

// ── Variant B feed data ────────────────────────────────────────────────────────
const feedB = [
  {
    id: 1,
    title: 'Added 3 items to grocery list',
    time: '1m ago',
    status: 'success',
    statusLabel: 'Done',
    expanded: true, // the one shown expanded
    trigger: 'User asked: "Add milk, eggs, and bread to the grocery list"',
    steps: [
      { label: 'list_grocery_items — fetched current list (12 items)', done: true },
      { label: 'add_grocery_item — added "Milk" (1 gallon)', done: true },
      { label: 'add_grocery_item — added "Eggs" (1 dozen)', done: true },
      { label: 'add_grocery_item — added "Bread" (1 loaf)', done: true },
    ],
    preview: 'Grocery list updated. 3 items added — Milk, Eggs, Bread. List now has 15 items total.',
  },
  {
    id: 2,
    title: "Found Emma's dentist appointment",
    time: '18m ago',
    status: 'success',
    statusLabel: 'Done',
    expanded: false,
    trigger: '',
    steps: [],
    preview: '',
  },
  {
    id: 3,
    title: 'Awarded 25 kudos points to Ava',
    time: '45m ago',
    status: 'success',
    statusLabel: 'Done',
    expanded: false,
    trigger: '',
    steps: [],
    preview: '',
  },
  {
    id: 4,
    title: 'Meal plan generation — missing ingredients',
    time: '2h ago',
    status: 'failed',
    statusLabel: 'Failed',
    expanded: false,
    trigger: '',
    steps: [],
    preview: '',
  },
  {
    id: 5,
    title: 'Fetching vault entry for car insurance',
    time: '3h ago',
    status: 'pending',
    statusLabel: 'Pending',
    expanded: false,
    trigger: '',
    steps: [],
    preview: '',
  },
]

// ── Variant C feed data ────────────────────────────────────────────────────────
const feedC = [
  {
    id: 1,
    avatar: null, initials: 'EQ', avatarColor: '#6856B2',
    text: 'Emma gave kudos to Ava',
    sub: '"Great job helping with dinner tonight!"',
    meta: '+5 pts',
    metaAccent: 'mint',
    time: '5m ago',
    showActions: true,
  },
  {
    id: 2,
    avatar: null, initials: 'GQ', avatarColor: '#2E8A62',
    text: 'Greg completed "Mow the lawn"',
    sub: null,
    meta: '+15 pts',
    metaAccent: 'sun',
    time: '22m ago',
    showActions: false,
  },
  {
    id: 3,
    avatar: null, initials: 'AQ', avatarColor: '#BA562E',
    text: 'Ava redeemed "Extra screen time"',
    sub: null,
    meta: '−100 pts',
    metaAccent: 'failed',
    time: '1h ago',
    showActions: false,
  },
  {
    id: 4,
    avatar: null, initials: 'EQ', avatarColor: '#A2780C',
    text: 'Emma gave kudos to Greg',
    sub: '"Thanks for fixing my bike!"',
    meta: '+5 pts',
    metaAccent: 'mint',
    time: '2h ago',
    showActions: true,
  },
  {
    id: 5,
    avatar: null, initials: 'GQ', avatarColor: '#486E9C',
    text: 'Greg awarded badge "Streak Master" to Emma',
    sub: null,
    meta: null,
    metaAccent: null,
    time: '4h ago',
    showActions: false,
  },
]

// Status pill helper — maps status key to palette entry
function statusStyle(p, key) {
  return { background: p.status[key].soft, color: p.status[key].bold }
}

function accentStyle(p, key) {
  if (key === 'failed') return { background: p.status.failed.soft, color: p.status.failed.bold }
  if (key === 'pending') return { background: p.status.pending.soft, color: p.status.pending.bold }
  if (key === 'paused') return { background: p.status.paused.soft, color: p.status.paused.bold }
  return { background: p.accents[key].soft, color: p.accents[key].bold }
}
</script>

<template>
  <ComponentPage
    title="5.9 ActivityRow"
    description="The row component that backs every feed in the app — points activity feed, AI activity feed, task history, notifications drawer, vault access log. Three shapes for three information densities: compact single-line, expandable AI trace, and conversational social."
    status="scaffolded"
  >

    <!-- ═══════════════════════════════════════════════════════════════
         VARIANT A — Compact single-line
         ════════════════════════════════════════════════════════════════ -->
    <section class="mb-20">
      <VariantFrame label="A" caption="Compact — single-line feed row. Icon-left · title-middle · status pill · timestamp-right. Maximum density for notifications, task history, vault access log.">
        <div class="w-full space-y-10">

          <!-- ── LIGHT PANEL ── -->
          <div class="rounded-2xl border" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <div class="px-5 py-3 border-b" :style="{ borderColor: L.borderSubtle }">
              <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>
            </div>

            <!-- Feed rows -->
            <div
              class="rounded-b-2xl overflow-hidden divide-y"
              :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle }"
            >
              <div
                v-for="row in feedA"
                :key="row.id"
                class="flex items-center gap-3 px-4 py-3"
              >
                <!-- Icon container -->
                <div
                  class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0"
                  :style="accentStyle(L, row.iconAccent)"
                >
                  <component :is="row.icon" class="w-3.5 h-3.5" />
                </div>

                <!-- Title + optional meta -->
                <div class="flex-1 min-w-0">
                  <p
                    class="text-[13px] font-medium truncate"
                    :style="{ color: L.inkPrimary }"
                  >
                    {{ row.title }}
                    <span
                      v-if="row.meta"
                      class="font-normal ml-1"
                      :style="{ color: L.inkTertiary }"
                    >· {{ row.meta }}</span>
                  </p>
                </div>

                <!-- Status pill -->
                <span
                  class="inline-flex items-center gap-1 h-5 px-2 rounded-full text-[11px] font-medium flex-shrink-0"
                  :style="statusStyle(L, row.status)"
                >
                  <span class="w-1.5 h-1.5 rounded-full" :style="{ background: L.status[row.status].bold }" />
                  {{ row.statusLabel }}
                </span>

                <!-- Timestamp -->
                <p
                  class="text-[11px] flex-shrink-0 tabular-nums"
                  :style="{ color: L.inkTertiary }"
                >{{ row.time }}</p>
              </div>

              <!-- Empty state (shown after rows as a demo) -->
              <div class="flex flex-col items-center gap-2 py-8 opacity-50">
                <ChatBubbleBottomCenterTextIcon class="w-6 h-6" :style="{ color: L.inkTertiary }" />
                <p class="text-sm" :style="{ color: L.inkTertiary }">Nothing yet</p>
              </div>
            </div>
          </div>

          <!-- ── DARK PANEL ── -->
          <div class="rounded-2xl border" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <div class="px-5 py-3 border-b" :style="{ borderColor: D.borderSubtle }">
              <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>
            </div>

            <div
              class="rounded-b-2xl overflow-hidden divide-y"
              :style="{ background: D.surfaceRaised, divideColor: D.borderSubtle }"
            >
              <div
                v-for="row in feedA"
                :key="row.id"
                class="flex items-center gap-3 px-4 py-3"
                :style="{ borderColor: D.borderSubtle }"
              >
                <div
                  class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0"
                  :style="accentStyle(D, row.iconAccent)"
                >
                  <component :is="row.icon" class="w-3.5 h-3.5" />
                </div>

                <div class="flex-1 min-w-0">
                  <p
                    class="text-[13px] font-medium truncate"
                    :style="{ color: D.inkPrimary }"
                  >
                    {{ row.title }}
                    <span
                      v-if="row.meta"
                      class="font-normal ml-1"
                      :style="{ color: D.inkTertiary }"
                    >· {{ row.meta }}</span>
                  </p>
                </div>

                <span
                  class="inline-flex items-center gap-1 h-5 px-2 rounded-full text-[11px] font-medium flex-shrink-0"
                  :style="statusStyle(D, row.status)"
                >
                  <span class="w-1.5 h-1.5 rounded-full" :style="{ background: D.status[row.status].bold }" />
                  {{ row.statusLabel }}
                </span>

                <p
                  class="text-[11px] flex-shrink-0 tabular-nums"
                  :style="{ color: D.inkTertiary }"
                >{{ row.time }}</p>
              </div>

              <!-- Empty state -->
              <div class="flex flex-col items-center gap-2 py-8 opacity-40">
                <ChatBubbleBottomCenterTextIcon class="w-6 h-6" :style="{ color: D.inkTertiary }" />
                <p class="text-sm" :style="{ color: D.inkTertiary }">Nothing yet</p>
              </div>
            </div>
          </div>

          <p class="text-xs" :style="{ color: L.inkTertiary }">
            375px — row height is py-3 (fixed). Title truncates with ellipsis at narrow widths. Status pill and timestamp are flex-shrink-0 and never drop.
          </p>
        </div>
      </VariantFrame>
    </section>

    <!-- ═══════════════════════════════════════════════════════════════
         VARIANT B — Expandable (AI activity / audit trail)
         ════════════════════════════════════════════════════════════════ -->
    <section class="mb-20">
      <VariantFrame label="B" caption="Expandable — collapsed looks like Variant A; tap chevron to reveal trigger query, tool-call steps, and a preview card. Designed for the AI activity feed and audit trails.">
        <div class="w-full space-y-10">

          <!-- ── LIGHT PANEL ── -->
          <div class="rounded-2xl border" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <div class="px-5 py-3 border-b" :style="{ borderColor: L.borderSubtle }">
              <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>
            </div>

            <div
              class="rounded-b-2xl overflow-hidden divide-y"
              :style="{ background: L.surfaceRaised }"
            >
              <div
                v-for="(row, idx) in feedB"
                :key="row.id"
                :style="{ borderColor: L.borderSubtle }"
              >
                <!-- Collapsed / header row — always visible -->
                <button
                  class="w-full flex items-center gap-3 px-4 py-3 text-left transition-colors"
                  :style="{ background: idx === 0 && expandedB_lt ? L.surfaceSunken : 'transparent' }"
                  @click="idx === 0 ? (expandedB_lt = !expandedB_lt) : null"
                >
                  <!-- Chevron (only first row is interactive in demo) -->
                  <div class="flex-shrink-0 transition-transform duration-200" :style="{ transform: idx === 0 && expandedB_lt ? 'rotate(180deg)' : 'rotate(0deg)' }">
                    <ChevronDownIcon v-if="idx === 0" class="w-3.5 h-3.5" :style="{ color: L.inkTertiary }" />
                    <ChevronRightIcon v-else class="w-3.5 h-3.5 opacity-0" :style="{ color: L.inkTertiary }" />
                  </div>

                  <!-- Icon -->
                  <div
                    class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0"
                    :style="statusStyle(L, row.status)"
                  >
                    <CheckCircleIcon v-if="row.status === 'success'" class="w-3.5 h-3.5" />
                    <XCircleIcon v-else-if="row.status === 'failed'" class="w-3.5 h-3.5" />
                    <ClockIcon v-else class="w-3.5 h-3.5" />
                  </div>

                  <!-- Title -->
                  <div class="flex-1 min-w-0">
                    <p class="text-[13px] font-medium truncate" :style="{ color: L.inkPrimary }">{{ row.title }}</p>
                  </div>

                  <!-- Status pill -->
                  <span
                    class="inline-flex items-center gap-1 h-5 px-2 rounded-full text-[11px] font-medium flex-shrink-0"
                    :style="statusStyle(L, row.status)"
                  >
                    <span class="w-1.5 h-1.5 rounded-full" :style="{ background: L.status[row.status].bold }" />
                    {{ row.statusLabel }}
                  </span>

                  <!-- Timestamp -->
                  <p class="text-[11px] flex-shrink-0 tabular-nums" :style="{ color: L.inkTertiary }">{{ row.time }}</p>
                </button>

                <!-- Expanded detail block — only row 0 in demo -->
                <div
                  v-if="idx === 0 && expandedB_lt"
                  class="ml-[52px] mr-4 mb-3 rounded-xl p-4 space-y-3"
                  :style="{ background: L.surfaceSunken, border: `1px solid ${L.borderSubtle}` }"
                >
                  <!-- Trigger line -->
                  <div class="flex items-start gap-2">
                    <ChatBubbleLeftIcon class="w-4 h-4 flex-shrink-0 mt-0.5" :style="{ color: L.inkTertiary }" />
                    <p class="text-[12px] leading-relaxed" :style="{ color: L.inkSecondary }">
                      <span class="font-semibold" :style="{ color: L.inkPrimary }">Trigger:</span>
                      {{ row.trigger }}
                    </p>
                  </div>

                  <!-- Tool-call steps -->
                  <div class="space-y-1.5 pl-6">
                    <div
                      v-for="(step, si) in row.steps"
                      :key="si"
                      class="flex items-center gap-2"
                    >
                      <CheckIcon class="w-3.5 h-3.5 flex-shrink-0" :style="{ color: L.status.success.bold }" />
                      <p class="text-[11px]" :style="{ color: L.inkSecondary }">{{ step.label }}</p>
                    </div>
                  </div>

                  <!-- Preview card -->
                  <div
                    class="rounded-lg px-3 py-2.5 border"
                    :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle }"
                  >
                    <p class="text-[11px] font-semibold uppercase tracking-widest mb-1" :style="{ color: L.inkTertiary }">Response preview</p>
                    <p class="text-[12px] leading-relaxed" :style="{ color: L.inkPrimary }">{{ row.preview }}</p>
                  </div>
                </div>
              </div>

              <!-- Empty state -->
              <div class="flex flex-col items-center gap-2 py-8 opacity-50">
                <ChatBubbleBottomCenterTextIcon class="w-6 h-6" :style="{ color: L.inkTertiary }" />
                <p class="text-sm" :style="{ color: L.inkTertiary }">Nothing yet</p>
              </div>
            </div>
          </div>

          <!-- ── DARK PANEL ── -->
          <div class="rounded-2xl border" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <div class="px-5 py-3 border-b" :style="{ borderColor: D.borderSubtle }">
              <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>
            </div>

            <div
              class="rounded-b-2xl overflow-hidden divide-y"
              :style="{ background: D.surfaceRaised }"
            >
              <div
                v-for="(row, idx) in feedB"
                :key="row.id"
                :style="{ borderColor: D.borderSubtle }"
              >
                <button
                  class="w-full flex items-center gap-3 px-4 py-3 text-left transition-colors"
                  :style="{ background: idx === 0 && expandedB_dk ? D.surfaceSunken : 'transparent' }"
                  @click="idx === 0 ? (expandedB_dk = !expandedB_dk) : null"
                >
                  <div class="flex-shrink-0 transition-transform duration-200" :style="{ transform: idx === 0 && expandedB_dk ? 'rotate(180deg)' : 'rotate(0deg)' }">
                    <ChevronDownIcon v-if="idx === 0" class="w-3.5 h-3.5" :style="{ color: D.inkTertiary }" />
                    <ChevronRightIcon v-else class="w-3.5 h-3.5 opacity-0" :style="{ color: D.inkTertiary }" />
                  </div>

                  <div
                    class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0"
                    :style="statusStyle(D, row.status)"
                  >
                    <CheckCircleIcon v-if="row.status === 'success'" class="w-3.5 h-3.5" />
                    <XCircleIcon v-else-if="row.status === 'failed'" class="w-3.5 h-3.5" />
                    <ClockIcon v-else class="w-3.5 h-3.5" />
                  </div>

                  <div class="flex-1 min-w-0">
                    <p class="text-[13px] font-medium truncate" :style="{ color: D.inkPrimary }">{{ row.title }}</p>
                  </div>

                  <span
                    class="inline-flex items-center gap-1 h-5 px-2 rounded-full text-[11px] font-medium flex-shrink-0"
                    :style="statusStyle(D, row.status)"
                  >
                    <span class="w-1.5 h-1.5 rounded-full" :style="{ background: D.status[row.status].bold }" />
                    {{ row.statusLabel }}
                  </span>

                  <p class="text-[11px] flex-shrink-0 tabular-nums" :style="{ color: D.inkTertiary }">{{ row.time }}</p>
                </button>

                <!-- Expanded detail block -->
                <div
                  v-if="idx === 0 && expandedB_dk"
                  class="ml-[52px] mr-4 mb-3 rounded-xl p-4 space-y-3"
                  :style="{ background: D.surfaceSunken, border: `1px solid ${D.borderSubtle}` }"
                >
                  <div class="flex items-start gap-2">
                    <ChatBubbleLeftIcon class="w-4 h-4 flex-shrink-0 mt-0.5" :style="{ color: D.inkTertiary }" />
                    <p class="text-[12px] leading-relaxed" :style="{ color: D.inkSecondary }">
                      <span class="font-semibold" :style="{ color: D.inkPrimary }">Trigger:</span>
                      {{ row.trigger }}
                    </p>
                  </div>

                  <div class="space-y-1.5 pl-6">
                    <div
                      v-for="(step, si) in row.steps"
                      :key="si"
                      class="flex items-center gap-2"
                    >
                      <CheckIcon class="w-3.5 h-3.5 flex-shrink-0" :style="{ color: D.status.success.bold }" />
                      <p class="text-[11px]" :style="{ color: D.inkSecondary }">{{ step.label }}</p>
                    </div>
                  </div>

                  <div
                    class="rounded-lg px-3 py-2.5 border"
                    :style="{ background: D.surfaceRaised, borderColor: D.borderSubtle }"
                  >
                    <p class="text-[11px] font-semibold uppercase tracking-widest mb-1" :style="{ color: D.inkTertiary }">Response preview</p>
                    <p class="text-[12px] leading-relaxed" :style="{ color: D.inkPrimary }">{{ row.preview }}</p>
                  </div>
                </div>
              </div>

              <!-- Empty state -->
              <div class="flex flex-col items-center gap-2 py-8 opacity-40">
                <ChatBubbleBottomCenterTextIcon class="w-6 h-6" :style="{ color: D.inkTertiary }" />
                <p class="text-sm" :style="{ color: D.inkTertiary }">Nothing yet</p>
              </div>
            </div>
          </div>

          <p class="text-xs" :style="{ color: L.inkTertiary }">
            First row is live — tap the chevron to collapse/expand. The detail block indents 52px (icon width + gap) to align with the title column. On mobile, the preview card goes full-width within the indent.
          </p>
        </div>
      </VariantFrame>
    </section>

    <!-- ═══════════════════════════════════════════════════════════════
         VARIANT C — Conversational (avatar + text + actions)
         ════════════════════════════════════════════════════════════════ -->
    <section class="mb-20">
      <VariantFrame label="C" caption="Conversational — avatar-left, text + optional subtext, inline action buttons for kudos-style rows. Used in the points activity feed and social events feed.">
        <div class="w-full space-y-10">

          <!-- ── LIGHT PANEL ── -->
          <div class="rounded-2xl border" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <div class="px-5 py-3 border-b" :style="{ borderColor: L.borderSubtle }">
              <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>
            </div>

            <div
              class="rounded-b-2xl overflow-hidden divide-y"
              :style="{ background: L.surfaceRaised }"
            >
              <div
                v-for="row in feedC"
                :key="row.id"
                class="flex items-start gap-3 px-4 py-3"
                :style="{ borderColor: L.borderSubtle }"
              >
                <!-- Avatar circle -->
                <div
                  class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0 text-[13px] font-semibold"
                  :style="{ background: row.avatarColor + '22', color: row.avatarColor }"
                >
                  {{ row.initials }}
                </div>

                <!-- Content -->
                <div class="flex-1 min-w-0 space-y-0.5">
                  <!-- Main text + points badge inline -->
                  <div class="flex items-baseline gap-2 flex-wrap">
                    <p class="text-[13px] font-medium leading-snug" :style="{ color: L.inkPrimary }">{{ row.text }}</p>
                    <span
                      v-if="row.meta"
                      class="inline-flex items-center h-5 px-2 rounded-full text-[11px] font-medium"
                      :style="row.metaAccent === 'failed'
                        ? { background: L.status.failed.soft, color: L.status.failed.bold }
                        : { background: L.accents[row.metaAccent]?.soft ?? L.accents.mint.soft, color: L.accents[row.metaAccent]?.bold ?? L.accents.mint.bold }"
                    >{{ row.meta }}</span>
                  </div>

                  <!-- Optional subtext (kudos reason) -->
                  <p
                    v-if="row.sub"
                    class="text-[12px] leading-relaxed italic"
                    :style="{ color: L.inkSecondary }"
                  >{{ row.sub }}</p>

                  <!-- Inline action buttons -->
                  <div v-if="row.showActions" class="flex items-center gap-2 pt-1">
                    <button
                      class="inline-flex items-center gap-1 h-6 px-2.5 rounded-full text-[11px] font-medium border transition-colors"
                      :style="{ background: L.accents.mint.soft, borderColor: L.accents.mint.soft, color: L.accents.mint.bold }"
                    >
                      <HandThumbUpIcon class="w-3 h-3" />
                      Nice
                    </button>
                    <button
                      class="inline-flex items-center gap-1 h-6 px-2.5 rounded-full text-[11px] font-medium border transition-colors"
                      :style="{ background: 'transparent', borderColor: L.borderSubtle, color: L.inkTertiary }"
                    >
                      <ChatBubbleLeftIcon class="w-3 h-3" />
                      Reply
                    </button>
                  </div>
                </div>

                <!-- Timestamp -->
                <p class="text-[11px] flex-shrink-0 tabular-nums pt-0.5" :style="{ color: L.inkTertiary }">{{ row.time }}</p>
              </div>

              <!-- Empty state -->
              <div class="flex flex-col items-center gap-2 py-8 opacity-50">
                <ChatBubbleBottomCenterTextIcon class="w-6 h-6" :style="{ color: L.inkTertiary }" />
                <p class="text-sm" :style="{ color: L.inkTertiary }">Nothing yet</p>
              </div>
            </div>
          </div>

          <!-- ── DARK PANEL ── -->
          <div class="rounded-2xl border" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <div class="px-5 py-3 border-b" :style="{ borderColor: D.borderSubtle }">
              <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>
            </div>

            <div
              class="rounded-b-2xl overflow-hidden divide-y"
              :style="{ background: D.surfaceRaised }"
            >
              <div
                v-for="row in feedC"
                :key="row.id"
                class="flex items-start gap-3 px-4 py-3"
                :style="{ borderColor: D.borderSubtle }"
              >
                <div
                  class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0 text-[13px] font-semibold"
                  :style="{ background: row.avatarColor + '33', color: row.avatarColor }"
                >
                  {{ row.initials }}
                </div>

                <div class="flex-1 min-w-0 space-y-0.5">
                  <div class="flex items-baseline gap-2 flex-wrap">
                    <p class="text-[13px] font-medium leading-snug" :style="{ color: D.inkPrimary }">{{ row.text }}</p>
                    <span
                      v-if="row.meta"
                      class="inline-flex items-center h-5 px-2 rounded-full text-[11px] font-medium"
                      :style="row.metaAccent === 'failed'
                        ? { background: D.status.failed.soft, color: D.status.failed.bold }
                        : { background: D.accents[row.metaAccent]?.soft ?? D.accents.mint.soft, color: D.accents[row.metaAccent]?.bold ?? D.accents.mint.bold }"
                    >{{ row.meta }}</span>
                  </div>

                  <p
                    v-if="row.sub"
                    class="text-[12px] leading-relaxed italic"
                    :style="{ color: D.inkSecondary }"
                  >{{ row.sub }}</p>

                  <div v-if="row.showActions" class="flex items-center gap-2 pt-1">
                    <button
                      class="inline-flex items-center gap-1 h-6 px-2.5 rounded-full text-[11px] font-medium border transition-colors"
                      :style="{ background: D.accents.mint.soft, borderColor: D.accents.mint.soft, color: D.accents.mint.bold }"
                    >
                      <HandThumbUpIcon class="w-3 h-3" />
                      Nice
                    </button>
                    <button
                      class="inline-flex items-center gap-1 h-6 px-2.5 rounded-full text-[11px] font-medium border transition-colors"
                      :style="{ background: 'transparent', borderColor: D.borderSubtle, color: D.inkTertiary }"
                    >
                      <ChatBubbleLeftIcon class="w-3 h-3" />
                      Reply
                    </button>
                  </div>
                </div>

                <p class="text-[11px] flex-shrink-0 tabular-nums pt-0.5" :style="{ color: D.inkTertiary }">{{ row.time }}</p>
              </div>

              <!-- Empty state -->
              <div class="flex flex-col items-center gap-2 py-8 opacity-40">
                <ChatBubbleBottomCenterTextIcon class="w-6 h-6" :style="{ color: D.inkTertiary }" />
                <p class="text-sm" :style="{ color: D.inkTertiary }">Nothing yet</p>
              </div>
            </div>
          </div>

          <p class="text-xs" :style="{ color: L.inkTertiary }">
            Avatar is 36px (w-9); initials derived from display name. Action buttons appear only on social rows (kudos, badges) — task completions and redemptions are read-only. At 375px the meta pill wraps below the title thanks to flex-wrap.
          </p>
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
            Claude's pick — Variant C
          </p>
          <p class="text-sm leading-relaxed" :style="{ color: L.inkPrimary }">
            The points activity feed is the emotional core of Kinhold's gamification loop — kudos, completions, and redemptions are inherently social moments, and Variant C's avatar + text + subtext structure gives each event the human weight it deserves. The inline "Nice" and "Reply" actions keep interaction costs near zero without a full comment thread, and the points badge inlined next to the event title means the reward is always visible in context rather than buried in a separate column. Variants A and B are mechanical (system events, AI traces); C is for moments that involve people being kind to each other, and that distinction should show in the UI.
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
            Pick the variant that matches the nature of the event. System events get A; AI traces get B; human moments get C.
          </p>
        </div>

        <!-- Variant A -->
        <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-[140px_1fr] gap-3">
          <div>
            <span
              class="inline-flex items-center justify-center h-6 min-w-[1.5rem] px-2 rounded-full text-xs font-semibold"
              :style="{ background: L.inkPrimary, color: L.inkInverse }"
            >A — Compact</span>
          </div>
          <div class="space-y-1">
            <p class="text-sm font-medium" :style="{ color: L.inkPrimary }">Notifications drawer, task history, vault access log, calendar sync events</p>
            <p class="text-sm" :style="{ color: L.inkSecondary }">
              Use when the event is a system action with a clear status — task toggled, file uploaded, sync ran. The status pill (Done / Failed / Pending / Paused) carries the most important signal at a glance. Title truncates at narrow widths; timestamp is always right-aligned. Maximum density: fits 8–10 events in a phone viewport without scrolling.
            </p>
          </div>
        </div>

        <!-- Variant B -->
        <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-[140px_1fr] gap-3">
          <div>
            <span
              class="inline-flex items-center justify-center h-6 min-w-[1.5rem] px-2 rounded-full text-xs font-semibold"
              :style="{ background: L.inkPrimary, color: L.inkInverse }"
            >B — Expandable</span>
          </div>
          <div class="space-y-1">
            <p class="text-sm font-medium" :style="{ color: L.inkPrimary }">AI activity feed, audit trail, multi-step background job logs</p>
            <p class="text-sm" :style="{ color: L.inkSecondary }">
              Use for events that have a causal chain — user trigger, tool calls, result. Collapsed state is identical to Variant A so the feed stays scannable; the chevron signals "there's more here." The expanded block should always show: (1) the trigger/prompt, (2) each tool call with a check mark, (3) a preview of the final response. Keep the preview card to 1–2 sentences; link to full conversation if more is needed.
            </p>
          </div>
        </div>

        <!-- Variant C -->
        <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-[140px_1fr] gap-3">
          <div>
            <span
              class="inline-flex items-center justify-center h-6 min-w-[1.5rem] px-2 rounded-full text-xs font-semibold"
              :style="{ background: L.inkPrimary, color: L.inkInverse }"
            >C — Conversational</span>
          </div>
          <div class="space-y-1">
            <p class="text-sm font-medium" :style="{ color: L.inkPrimary }">Points activity feed, kudos log, badge award events, reward redemptions</p>
            <p class="text-sm" :style="{ color: L.inkSecondary }">
              Use when a person is the subject, not a system. The avatar makes authorship visible at a glance. Subtext is optional — only show when there is meaningful context (e.g. kudos reason). Action buttons (Nice / Reply) should appear only on social events (kudos, badge awards), not on mechanical completions or redemptions. The points pill inlines with the title so the reward is always contextual.
            </p>
          </div>
        </div>

        <!-- Empty state note -->
        <div class="px-6 py-4">
          <p class="text-xs font-semibold uppercase tracking-widest mb-2" :style="{ color: L.inkTertiary }">Empty state</p>
          <p class="text-sm" :style="{ color: L.inkSecondary }">
            All three variants use the same empty state: <code class="text-xs font-mono px-1 rounded" :style="{ background: L.surfaceSunken }">ChatBubbleBottomCenterTextIcon</code> centred in the feed container with "Nothing yet" in inkTertiary. Keep it brief — the feed will fill quickly once the family is active.
          </p>
        </div>
      </div>
    </section>

  </ComponentPage>
</template>
