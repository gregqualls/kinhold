<script setup>
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import {
  SparklesIcon,
  ClipboardDocumentListIcon,
  LockClosedIcon,
  PlusIcon,
  UserGroupIcon,
  ListBulletIcon,
  ChatBubbleLeftEllipsisIcon,
  ArrowRightIcon,
  ChevronRightIcon,
} from '@heroicons/vue/24/outline'
import { ref } from 'vue'

// ── Palette ──────────────────────────────────────────────────────────────────
const L = {
  surfaceApp:    '#FAF8F5',
  surfaceRaised: '#FFFFFF',
  surfaceSunken: '#F5F2EE',
  inkPrimary:    '#1C1C1E',
  inkSecondary:  '#6B6966',
  inkTertiary:   '#9C9895',
  inkInverse:    '#FAF8F5',
  borderSubtle:  '#E8E4DF',
  borderStrong:  '#BCB8B2',
  accents: {
    lavender: { soft: '#EAE6F8', bold: '#6856B2' },
    peach:    { soft: '#FCE9E0', bold: '#BA562E' },
    mint:     { soft: '#D5F2E8', bold: '#2E8A62' },
    sun:      { soft: '#FCF3D2', bold: '#A2780C' },
  },
}

const D = {
  surfaceApp:     '#141311',
  surfaceRaised:  '#1C1B19',
  surfaceSunken:  '#161513',
  surfaceOverlay: '#242220',
  inkPrimary:     '#F0EDE9',
  inkSecondary:   '#A09C97',
  inkTertiary:    '#6E6B67',
  inkInverse:     '#1C1C1E',
  borderSubtle:   '#2C2A27',
  borderStrong:   '#403E3A',
  accents: {
    lavender: { soft: '#302A48', bold: '#B6A8E6' },
    peach:    { soft: '#3E241A', bold: '#F0A882' },
    mint:     { soft: '#18342A', bold: '#7CD6AE' },
    sun:      { soft: '#342C0A', bold: '#E6C452' },
  },
}

// ── Shadow helpers ────────────────────────────────────────────────────────────
const SH_LT = '0 1px 2px rgba(28,20,10,0.04), 0 2px 6px rgba(28,20,10,0.05)'
const SH_DK = '0 1px 2px rgba(0,0,0,0.30), 0 2px 6px rgba(0,0,0,0.25)'

// ── Hover state ───────────────────────────────────────────────────────────────
const hov = ref({})
function onEnter(k) { hov.value = { ...hov.value, [k]: true } }
function onLeave(k) { hov.value = { ...hov.value, [k]: false } }

// ── Suggested-action rows for Variant B ──────────────────────────────────────
const taskSuggestions = [
  { icon: UserGroupIcon,                 label: 'Create a family chore' },
  { icon: ListBulletIcon,                label: 'Assign from a list template' },
  { icon: ChatBubbleLeftEllipsisIcon,    label: 'Ask assistant to plan my week' },
]
</script>

<template>
  <ComponentPage
    title="4.9 EmptyState"
    description="The placeholder shown when a list, board, feed, or vault category is empty. Must feel inviting — not a dead end — and offer a clear path forward. Used on: empty task list, empty vault category, empty rewards store, empty calendar day, empty chat history, empty meal plan."
    status="chosen"
  >

    <!-- ══════════════════════════════════════════════════════════════════════
         VARIANT A — Centered hero
         ═══════════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="A" caption="Centered hero — iridescent glyph + title + description + single CTA">
        <div class="w-full space-y-10">

          <!-- LIGHT · No Tasks · Mobile width -->
          <div>
            <p class="text-[11px] font-medium uppercase tracking-wider mb-3" :style="{ color: L.inkTertiary }">
              Light — no tasks — mobile (375 px)
            </p>
            <div class="rounded-2xl border mx-auto overflow-hidden"
                 :style="{ background: L.surfaceApp, borderColor: L.borderSubtle, maxWidth: '375px' }">
              <div class="flex flex-col items-center text-center px-8 py-16 space-y-5">
                <!-- Iridescent glyph -->
                <div class="empty-glyph-lt flex-shrink-0 flex items-center justify-center">
                  <ClipboardDocumentListIcon class="w-10 h-10" :style="{ color: L.accents.lavender.bold }" />
                </div>
                <!-- Title -->
                <h2 class="font-heading text-2xl font-semibold leading-snug"
                    :style="{ color: L.inkPrimary }">Nothing on your list</h2>
                <!-- Description -->
                <p class="text-sm leading-relaxed max-w-[260px]"
                   :style="{ color: L.inkSecondary }">
                  Take a breather or add one — your next win is just a task away.
                </p>
                <!-- CTA -->
                <button
                  class="flex items-center gap-2 px-6"
                  :style="{
                    height: '44px', borderRadius: '9999px', fontSize: '15px', fontWeight: '500',
                    background: L.accents.lavender.bold, color: L.inkInverse,
                    border: 'none', cursor: 'pointer',
                    boxShadow: hov['la-lt-task-cta'] ? '0 4px 12px rgba(104,86,178,0.35)' : SH_LT,
                    transform: hov['la-lt-task-cta'] ? 'translateY(-1px)' : 'none',
                    transition: 'box-shadow 150ms, transform 150ms',
                  }"
                  @mouseenter="onEnter('la-lt-task-cta')" @mouseleave="onLeave('la-lt-task-cta')"
                >
                  <PlusIcon class="w-4 h-4" />
                  Add task
                </button>
              </div>
            </div>
          </div>

          <!-- LIGHT · No Tasks · Desktop width -->
          <div>
            <p class="text-[11px] font-medium uppercase tracking-wider mb-3" :style="{ color: L.inkTertiary }">
              Light — no tasks — desktop
            </p>
            <div class="rounded-2xl border overflow-hidden"
                 :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
              <div class="flex flex-col items-center text-center px-8 py-16 space-y-5">
                <div class="empty-glyph-lt flex-shrink-0 flex items-center justify-center">
                  <ClipboardDocumentListIcon class="w-10 h-10" :style="{ color: L.accents.lavender.bold }" />
                </div>
                <h2 class="font-heading text-2xl font-semibold leading-snug"
                    :style="{ color: L.inkPrimary }">Nothing on your list</h2>
                <p class="text-sm leading-relaxed max-w-[320px]"
                   :style="{ color: L.inkSecondary }">
                  Take a breather or add one — your next win is just a task away.
                </p>
                <button
                  class="flex items-center gap-2 px-6"
                  :style="{
                    height: '44px', borderRadius: '9999px', fontSize: '15px', fontWeight: '500',
                    background: L.accents.lavender.bold, color: L.inkInverse,
                    border: 'none', cursor: 'pointer',
                    boxShadow: hov['la-lt-task-desk'] ? '0 4px 12px rgba(104,86,178,0.35)' : SH_LT,
                    transform: hov['la-lt-task-desk'] ? 'translateY(-1px)' : 'none',
                    transition: 'box-shadow 150ms, transform 150ms',
                  }"
                  @mouseenter="onEnter('la-lt-task-desk')" @mouseleave="onLeave('la-lt-task-desk')"
                >
                  <PlusIcon class="w-4 h-4" />
                  Add task
                </button>
              </div>
            </div>
          </div>

          <!-- LIGHT · No Vault Entries · Mobile -->
          <div>
            <p class="text-[11px] font-medium uppercase tracking-wider mb-3" :style="{ color: L.inkTertiary }">
              Light — empty vault — mobile (375 px)
            </p>
            <div class="rounded-2xl border mx-auto overflow-hidden"
                 :style="{ background: L.surfaceApp, borderColor: L.borderSubtle, maxWidth: '375px' }">
              <div class="flex flex-col items-center text-center px-8 py-16 space-y-5">
                <div class="empty-glyph-lt empty-glyph-lt--mint flex-shrink-0 flex items-center justify-center">
                  <LockClosedIcon class="w-10 h-10" :style="{ color: L.accents.mint.bold }" />
                </div>
                <h2 class="font-heading text-2xl font-semibold leading-snug"
                    :style="{ color: L.inkPrimary }">Your vault is empty</h2>
                <p class="text-sm leading-relaxed max-w-[260px]"
                   :style="{ color: L.inkSecondary }">
                  Add your first entry to keep your family's important info safe and close.
                </p>
                <button
                  class="flex items-center gap-2 px-6"
                  :style="{
                    height: '44px', borderRadius: '9999px', fontSize: '15px', fontWeight: '500',
                    background: L.accents.mint.bold, color: L.inkInverse,
                    border: 'none', cursor: 'pointer',
                    boxShadow: hov['la-lt-vault-mob'] ? '0 4px 12px rgba(46,138,98,0.30)' : SH_LT,
                    transform: hov['la-lt-vault-mob'] ? 'translateY(-1px)' : 'none',
                    transition: 'box-shadow 150ms, transform 150ms',
                  }"
                  @mouseenter="onEnter('la-lt-vault-mob')" @mouseleave="onLeave('la-lt-vault-mob')"
                >
                  <PlusIcon class="w-4 h-4" />
                  Add entry
                </button>
              </div>
            </div>
          </div>

          <!-- DARK · No Tasks · Mobile -->
          <div>
            <p class="text-[11px] font-medium uppercase tracking-wider mb-3" :style="{ color: D.inkTertiary }">
              Dark — no tasks — mobile (375 px)
            </p>
            <div class="rounded-2xl border mx-auto overflow-hidden"
                 :style="{ background: D.surfaceApp, borderColor: D.borderSubtle, maxWidth: '375px' }">
              <div class="flex flex-col items-center text-center px-8 py-16 space-y-5">
                <div class="empty-glyph-dk flex-shrink-0 flex items-center justify-center">
                  <ClipboardDocumentListIcon class="w-10 h-10" :style="{ color: D.accents.lavender.bold }" />
                </div>
                <h2 class="font-heading text-2xl font-semibold leading-snug"
                    :style="{ color: D.inkPrimary }">Nothing on your list</h2>
                <p class="text-sm leading-relaxed max-w-[260px]"
                   :style="{ color: D.inkSecondary }">
                  Take a breather or add one — your next win is just a task away.
                </p>
                <button
                  class="flex items-center gap-2 px-6"
                  :style="{
                    height: '44px', borderRadius: '9999px', fontSize: '15px', fontWeight: '500',
                    background: D.accents.lavender.bold, color: D.inkInverse,
                    border: 'none', cursor: 'pointer',
                    boxShadow: hov['la-dk-task-mob'] ? '0 4px 12px rgba(182,168,230,0.30)' : SH_DK,
                    transform: hov['la-dk-task-mob'] ? 'translateY(-1px)' : 'none',
                    transition: 'box-shadow 150ms, transform 150ms',
                  }"
                  @mouseenter="onEnter('la-dk-task-mob')" @mouseleave="onLeave('la-dk-task-mob')"
                >
                  <PlusIcon class="w-4 h-4" />
                  Add task
                </button>
              </div>
            </div>
          </div>

          <!-- DARK · No Tasks · Desktop -->
          <div>
            <p class="text-[11px] font-medium uppercase tracking-wider mb-3" :style="{ color: D.inkTertiary }">
              Dark — no tasks — desktop
            </p>
            <div class="rounded-2xl border overflow-hidden"
                 :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
              <div class="flex flex-col items-center text-center px-8 py-16 space-y-5">
                <div class="empty-glyph-dk flex-shrink-0 flex items-center justify-center">
                  <ClipboardDocumentListIcon class="w-10 h-10" :style="{ color: D.accents.lavender.bold }" />
                </div>
                <h2 class="font-heading text-2xl font-semibold leading-snug"
                    :style="{ color: D.inkPrimary }">Nothing on your list</h2>
                <p class="text-sm leading-relaxed max-w-[320px]"
                   :style="{ color: D.inkSecondary }">
                  Take a breather or add one — your next win is just a task away.
                </p>
                <button
                  class="flex items-center gap-2 px-6"
                  :style="{
                    height: '44px', borderRadius: '9999px', fontSize: '15px', fontWeight: '500',
                    background: D.accents.lavender.bold, color: D.inkInverse,
                    border: 'none', cursor: 'pointer',
                    boxShadow: hov['la-dk-task-desk'] ? '0 4px 12px rgba(182,168,230,0.30)' : SH_DK,
                    transform: hov['la-dk-task-desk'] ? 'translateY(-1px)' : 'none',
                    transition: 'box-shadow 150ms, transform 150ms',
                  }"
                  @mouseenter="onEnter('la-dk-task-desk')" @mouseleave="onLeave('la-dk-task-desk')"
                >
                  <PlusIcon class="w-4 h-4" />
                  Add task
                </button>
              </div>
            </div>
          </div>

          <!-- DARK · Empty Vault · Desktop -->
          <div>
            <p class="text-[11px] font-medium uppercase tracking-wider mb-3" :style="{ color: D.inkTertiary }">
              Dark — empty vault — desktop
            </p>
            <div class="rounded-2xl border overflow-hidden"
                 :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
              <div class="flex flex-col items-center text-center px-8 py-16 space-y-5">
                <div class="empty-glyph-dk empty-glyph-dk--mint flex-shrink-0 flex items-center justify-center">
                  <LockClosedIcon class="w-10 h-10" :style="{ color: D.accents.mint.bold }" />
                </div>
                <h2 class="font-heading text-2xl font-semibold leading-snug"
                    :style="{ color: D.inkPrimary }">Your vault is empty</h2>
                <p class="text-sm leading-relaxed max-w-[320px]"
                   :style="{ color: D.inkSecondary }">
                  Add your first entry to keep your family's important info safe and close.
                </p>
                <button
                  class="flex items-center gap-2 px-6"
                  :style="{
                    height: '44px', borderRadius: '9999px', fontSize: '15px', fontWeight: '500',
                    background: D.accents.mint.bold, color: D.inkInverse,
                    border: 'none', cursor: 'pointer',
                    boxShadow: hov['la-dk-vault-desk'] ? '0 4px 12px rgba(124,214,174,0.25)' : SH_DK,
                    transform: hov['la-dk-vault-desk'] ? 'translateY(-1px)' : 'none',
                    transition: 'box-shadow 150ms, transform 150ms',
                  }"
                  @mouseenter="onEnter('la-dk-vault-desk')" @mouseleave="onLeave('la-dk-vault-desk')"
                >
                  <PlusIcon class="w-4 h-4" />
                  Add entry
                </button>
              </div>
            </div>
          </div>

        </div>
      </VariantFrame>
      <p class="mt-3 text-sm px-1" :style="{ color: L.inkSecondary }">
        The iridescent glyph container announces the context at a glance, while the generous py-16 padding prevents the state from feeling cramped regardless of viewport width.
      </p>
    </section>


    <!-- ══════════════════════════════════════════════════════════════════════
         VARIANT B — Hero + suggested actions
         ═══════════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="B" caption="Hero + suggested actions — 3 quick-start rows below the primary CTA for onboarding moments">
        <div class="w-full space-y-10">

          <!-- LIGHT · No Tasks · Mobile -->
          <div>
            <p class="text-[11px] font-medium uppercase tracking-wider mb-3" :style="{ color: L.inkTertiary }">
              Light — no tasks — mobile (375 px)
            </p>
            <div class="rounded-2xl border mx-auto overflow-hidden"
                 :style="{ background: L.surfaceApp, borderColor: L.borderSubtle, maxWidth: '375px' }">
              <div class="flex flex-col items-center text-center px-6 py-16 space-y-5">
                <div class="empty-glyph-lt flex-shrink-0 flex items-center justify-center">
                  <ClipboardDocumentListIcon class="w-10 h-10" :style="{ color: L.accents.lavender.bold }" />
                </div>
                <h2 class="font-heading text-2xl font-semibold leading-snug"
                    :style="{ color: L.inkPrimary }">Nothing on your list</h2>
                <p class="text-sm leading-relaxed max-w-[260px]"
                   :style="{ color: L.inkSecondary }">
                  Take a breather or add one — your next win is just a task away.
                </p>
                <button
                  class="flex items-center gap-2 px-6"
                  :style="{
                    height: '44px', borderRadius: '9999px', fontSize: '15px', fontWeight: '500',
                    background: L.accents.lavender.bold, color: L.inkInverse,
                    border: 'none', cursor: 'pointer',
                    boxShadow: hov['lb-lt-task-mob-cta'] ? '0 4px 12px rgba(104,86,178,0.35)' : SH_LT,
                    transform: hov['lb-lt-task-mob-cta'] ? 'translateY(-1px)' : 'none',
                    transition: 'box-shadow 150ms, transform 150ms',
                  }"
                  @mouseenter="onEnter('lb-lt-task-mob-cta')" @mouseleave="onLeave('lb-lt-task-mob-cta')"
                >
                  <PlusIcon class="w-4 h-4" />
                  Add task
                </button>

                <!-- Suggested actions -->
                <div class="w-full pt-2 space-y-2">
                  <p class="text-[11px] font-medium uppercase tracking-wider text-left"
                     :style="{ color: L.inkTertiary }">Or get started with</p>
                  <div
                    v-for="(s, i) in taskSuggestions"
                    :key="i"
                    class="rounded-xl p-3 flex items-center gap-3 cursor-pointer"
                    :style="{
                      background: hov['lb-lt-mob-s' + i] ? L.surfaceSunken : L.surfaceRaised,
                      border: `1px solid ${L.borderSubtle}`,
                      transition: 'background 150ms',
                    }"
                    @mouseenter="onEnter('lb-lt-mob-s' + i)" @mouseleave="onLeave('lb-lt-mob-s' + i)"
                  >
                    <span class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center"
                          :style="{ background: L.accents.lavender.soft }">
                      <component :is="s.icon" class="w-4 h-4" :style="{ color: L.accents.lavender.bold }" />
                    </span>
                    <span class="flex-1 text-left text-[13px] font-medium" :style="{ color: L.inkPrimary }">{{ s.label }}</span>
                    <ChevronRightIcon class="w-4 h-4 flex-shrink-0" :style="{ color: L.inkTertiary }" />
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- LIGHT · No Tasks · Desktop -->
          <div>
            <p class="text-[11px] font-medium uppercase tracking-wider mb-3" :style="{ color: L.inkTertiary }">
              Light — no tasks — desktop
            </p>
            <div class="rounded-2xl border overflow-hidden"
                 :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
              <div class="flex flex-col items-center text-center px-8 py-16 space-y-5">
                <div class="empty-glyph-lt flex-shrink-0 flex items-center justify-center">
                  <ClipboardDocumentListIcon class="w-10 h-10" :style="{ color: L.accents.lavender.bold }" />
                </div>
                <h2 class="font-heading text-2xl font-semibold leading-snug"
                    :style="{ color: L.inkPrimary }">Nothing on your list</h2>
                <p class="text-sm leading-relaxed max-w-[320px]"
                   :style="{ color: L.inkSecondary }">
                  Take a breather or add one — your next win is just a task away.
                </p>
                <button
                  class="flex items-center gap-2 px-6"
                  :style="{
                    height: '44px', borderRadius: '9999px', fontSize: '15px', fontWeight: '500',
                    background: L.accents.lavender.bold, color: L.inkInverse,
                    border: 'none', cursor: 'pointer',
                    boxShadow: hov['lb-lt-task-desk-cta'] ? '0 4px 12px rgba(104,86,178,0.35)' : SH_LT,
                    transform: hov['lb-lt-task-desk-cta'] ? 'translateY(-1px)' : 'none',
                    transition: 'box-shadow 150ms, transform 150ms',
                  }"
                  @mouseenter="onEnter('lb-lt-task-desk-cta')" @mouseleave="onLeave('lb-lt-task-desk-cta')"
                >
                  <PlusIcon class="w-4 h-4" />
                  Add task
                </button>

                <!-- Suggested actions — wider max width on desktop -->
                <div class="w-full max-w-[480px] pt-2 space-y-2">
                  <p class="text-[11px] font-medium uppercase tracking-wider text-left"
                     :style="{ color: L.inkTertiary }">Or get started with</p>
                  <div
                    v-for="(s, i) in taskSuggestions"
                    :key="i"
                    class="rounded-xl p-3 flex items-center gap-3 cursor-pointer"
                    :style="{
                      background: hov['lb-lt-desk-s' + i] ? L.surfaceSunken : L.surfaceRaised,
                      border: `1px solid ${L.borderSubtle}`,
                      transition: 'background 150ms',
                    }"
                    @mouseenter="onEnter('lb-lt-desk-s' + i)" @mouseleave="onLeave('lb-lt-desk-s' + i)"
                  >
                    <span class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center"
                          :style="{ background: L.accents.lavender.soft }">
                      <component :is="s.icon" class="w-4 h-4" :style="{ color: L.accents.lavender.bold }" />
                    </span>
                    <span class="flex-1 text-left text-[13px] font-medium" :style="{ color: L.inkPrimary }">{{ s.label }}</span>
                    <ChevronRightIcon class="w-4 h-4 flex-shrink-0" :style="{ color: L.inkTertiary }" />
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- LIGHT · Empty Vault · Desktop -->
          <div>
            <p class="text-[11px] font-medium uppercase tracking-wider mb-3" :style="{ color: L.inkTertiary }">
              Light — empty vault — desktop
            </p>
            <div class="rounded-2xl border overflow-hidden"
                 :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
              <div class="flex flex-col items-center text-center px-8 py-16 space-y-5">
                <div class="empty-glyph-lt empty-glyph-lt--mint flex-shrink-0 flex items-center justify-center">
                  <LockClosedIcon class="w-10 h-10" :style="{ color: L.accents.mint.bold }" />
                </div>
                <h2 class="font-heading text-2xl font-semibold leading-snug"
                    :style="{ color: L.inkPrimary }">Your vault is empty</h2>
                <p class="text-sm leading-relaxed max-w-[320px]"
                   :style="{ color: L.inkSecondary }">
                  Add your first entry to keep your family's important info safe and close.
                </p>
                <button
                  class="flex items-center gap-2 px-6"
                  :style="{
                    height: '44px', borderRadius: '9999px', fontSize: '15px', fontWeight: '500',
                    background: L.accents.mint.bold, color: L.inkInverse,
                    border: 'none', cursor: 'pointer',
                    boxShadow: hov['lb-lt-vault-desk-cta'] ? '0 4px 12px rgba(46,138,98,0.30)' : SH_LT,
                    transform: hov['lb-lt-vault-desk-cta'] ? 'translateY(-1px)' : 'none',
                    transition: 'box-shadow 150ms, transform 150ms',
                  }"
                  @mouseenter="onEnter('lb-lt-vault-desk-cta')" @mouseleave="onLeave('lb-lt-vault-desk-cta')"
                >
                  <PlusIcon class="w-4 h-4" />
                  Add entry
                </button>
              </div>
            </div>
          </div>

          <!-- DARK · No Tasks · Mobile -->
          <div>
            <p class="text-[11px] font-medium uppercase tracking-wider mb-3" :style="{ color: D.inkTertiary }">
              Dark — no tasks — mobile (375 px)
            </p>
            <div class="rounded-2xl border mx-auto overflow-hidden"
                 :style="{ background: D.surfaceApp, borderColor: D.borderSubtle, maxWidth: '375px' }">
              <div class="flex flex-col items-center text-center px-6 py-16 space-y-5">
                <div class="empty-glyph-dk flex-shrink-0 flex items-center justify-center">
                  <ClipboardDocumentListIcon class="w-10 h-10" :style="{ color: D.accents.lavender.bold }" />
                </div>
                <h2 class="font-heading text-2xl font-semibold leading-snug"
                    :style="{ color: D.inkPrimary }">Nothing on your list</h2>
                <p class="text-sm leading-relaxed max-w-[260px]"
                   :style="{ color: D.inkSecondary }">
                  Take a breather or add one — your next win is just a task away.
                </p>
                <button
                  class="flex items-center gap-2 px-6"
                  :style="{
                    height: '44px', borderRadius: '9999px', fontSize: '15px', fontWeight: '500',
                    background: D.accents.lavender.bold, color: D.inkInverse,
                    border: 'none', cursor: 'pointer',
                    boxShadow: hov['lb-dk-task-mob-cta'] ? '0 4px 12px rgba(182,168,230,0.30)' : SH_DK,
                    transform: hov['lb-dk-task-mob-cta'] ? 'translateY(-1px)' : 'none',
                    transition: 'box-shadow 150ms, transform 150ms',
                  }"
                  @mouseenter="onEnter('lb-dk-task-mob-cta')" @mouseleave="onLeave('lb-dk-task-mob-cta')"
                >
                  <PlusIcon class="w-4 h-4" />
                  Add task
                </button>

                <div class="w-full pt-2 space-y-2">
                  <p class="text-[11px] font-medium uppercase tracking-wider text-left"
                     :style="{ color: D.inkTertiary }">Or get started with</p>
                  <div
                    v-for="(s, i) in taskSuggestions"
                    :key="i"
                    class="rounded-xl p-3 flex items-center gap-3 cursor-pointer"
                    :style="{
                      background: hov['lb-dk-mob-s' + i] ? D.surfaceSunken : D.surfaceRaised,
                      border: `1px solid ${D.borderSubtle}`,
                      transition: 'background 150ms',
                    }"
                    @mouseenter="onEnter('lb-dk-mob-s' + i)" @mouseleave="onLeave('lb-dk-mob-s' + i)"
                  >
                    <span class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center"
                          :style="{ background: D.accents.lavender.soft }">
                      <component :is="s.icon" class="w-4 h-4" :style="{ color: D.accents.lavender.bold }" />
                    </span>
                    <span class="flex-1 text-left text-[13px] font-medium" :style="{ color: D.inkPrimary }">{{ s.label }}</span>
                    <ChevronRightIcon class="w-4 h-4 flex-shrink-0" :style="{ color: D.inkTertiary }" />
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- DARK · No Tasks · Desktop -->
          <div>
            <p class="text-[11px] font-medium uppercase tracking-wider mb-3" :style="{ color: D.inkTertiary }">
              Dark — no tasks — desktop
            </p>
            <div class="rounded-2xl border overflow-hidden"
                 :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
              <div class="flex flex-col items-center text-center px-8 py-16 space-y-5">
                <div class="empty-glyph-dk flex-shrink-0 flex items-center justify-center">
                  <ClipboardDocumentListIcon class="w-10 h-10" :style="{ color: D.accents.lavender.bold }" />
                </div>
                <h2 class="font-heading text-2xl font-semibold leading-snug"
                    :style="{ color: D.inkPrimary }">Nothing on your list</h2>
                <p class="text-sm leading-relaxed max-w-[320px]"
                   :style="{ color: D.inkSecondary }">
                  Take a breather or add one — your next win is just a task away.
                </p>
                <button
                  class="flex items-center gap-2 px-6"
                  :style="{
                    height: '44px', borderRadius: '9999px', fontSize: '15px', fontWeight: '500',
                    background: D.accents.lavender.bold, color: D.inkInverse,
                    border: 'none', cursor: 'pointer',
                    boxShadow: hov['lb-dk-task-desk-cta'] ? '0 4px 12px rgba(182,168,230,0.30)' : SH_DK,
                    transform: hov['lb-dk-task-desk-cta'] ? 'translateY(-1px)' : 'none',
                    transition: 'box-shadow 150ms, transform 150ms',
                  }"
                  @mouseenter="onEnter('lb-dk-task-desk-cta')" @mouseleave="onLeave('lb-dk-task-desk-cta')"
                >
                  <PlusIcon class="w-4 h-4" />
                  Add task
                </button>

                <div class="w-full max-w-[480px] pt-2 space-y-2">
                  <p class="text-[11px] font-medium uppercase tracking-wider text-left"
                     :style="{ color: D.inkTertiary }">Or get started with</p>
                  <div
                    v-for="(s, i) in taskSuggestions"
                    :key="i"
                    class="rounded-xl p-3 flex items-center gap-3 cursor-pointer"
                    :style="{
                      background: hov['lb-dk-desk-s' + i] ? D.surfaceSunken : D.surfaceRaised,
                      border: `1px solid ${D.borderSubtle}`,
                      transition: 'background 150ms',
                    }"
                    @mouseenter="onEnter('lb-dk-desk-s' + i)" @mouseleave="onLeave('lb-dk-desk-s' + i)"
                  >
                    <span class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center"
                          :style="{ background: D.accents.lavender.soft }">
                      <component :is="s.icon" class="w-4 h-4" :style="{ color: D.accents.lavender.bold }" />
                    </span>
                    <span class="flex-1 text-left text-[13px] font-medium" :style="{ color: D.inkPrimary }">{{ s.label }}</span>
                    <ChevronRightIcon class="w-4 h-4 flex-shrink-0" :style="{ color: D.inkTertiary }" />
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>
      </VariantFrame>
      <p class="mt-3 text-sm px-1" :style="{ color: L.inkSecondary }">
        The suggested-action rows convert a passive dead end into an active onboarding moment — each row is a concrete first step, reducing blank-slate anxiety for new users.
      </p>
    </section>


    <!-- ══════════════════════════════════════════════════════════════════════
         VARIANT C — Minimal
         ═══════════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="C" caption="Minimal — small outline icon + single sentence, no CTA. For nested/terminal empty states.">
        <div class="w-full space-y-10">

          <!-- LIGHT · No Tasks · Mobile -->
          <div>
            <p class="text-[11px] font-medium uppercase tracking-wider mb-3" :style="{ color: L.inkTertiary }">
              Light — no tasks — mobile (375 px)
            </p>
            <div class="rounded-2xl border mx-auto overflow-hidden"
                 :style="{ background: L.surfaceApp, borderColor: L.borderSubtle, maxWidth: '375px' }">
              <div class="flex flex-col items-center text-center px-8 py-10 space-y-3">
                <ClipboardDocumentListIcon class="w-7 h-7" :style="{ color: L.inkTertiary }" />
                <p class="text-sm leading-relaxed max-w-[240px]"
                   :style="{ color: L.inkTertiary }">
                  Nothing on your list — take a breather or add one.
                </p>
              </div>
            </div>
          </div>

          <!-- LIGHT · No Tasks · Desktop -->
          <div>
            <p class="text-[11px] font-medium uppercase tracking-wider mb-3" :style="{ color: L.inkTertiary }">
              Light — no tasks — desktop
            </p>
            <div class="rounded-2xl border overflow-hidden"
                 :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
              <div class="flex flex-col items-center text-center px-8 py-10 space-y-3">
                <ClipboardDocumentListIcon class="w-7 h-7" :style="{ color: L.inkTertiary }" />
                <p class="text-sm leading-relaxed max-w-[280px]"
                   :style="{ color: L.inkTertiary }">
                  Nothing on your list — take a breather or add one.
                </p>
              </div>
            </div>
          </div>

          <!-- LIGHT · Empty Vault · Mobile -->
          <div>
            <p class="text-[11px] font-medium uppercase tracking-wider mb-3" :style="{ color: L.inkTertiary }">
              Light — empty vault — mobile (375 px)
            </p>
            <div class="rounded-2xl border mx-auto overflow-hidden"
                 :style="{ background: L.surfaceApp, borderColor: L.borderSubtle, maxWidth: '375px' }">
              <div class="flex flex-col items-center text-center px-8 py-10 space-y-3">
                <LockClosedIcon class="w-7 h-7" :style="{ color: L.inkTertiary }" />
                <p class="text-sm leading-relaxed max-w-[240px]"
                   :style="{ color: L.inkTertiary }">
                  Your vault is empty — add your first entry to get started.
                </p>
              </div>
            </div>
          </div>

          <!-- DARK · No Tasks · Mobile -->
          <div>
            <p class="text-[11px] font-medium uppercase tracking-wider mb-3" :style="{ color: D.inkTertiary }">
              Dark — no tasks — mobile (375 px)
            </p>
            <div class="rounded-2xl border mx-auto overflow-hidden"
                 :style="{ background: D.surfaceApp, borderColor: D.borderSubtle, maxWidth: '375px' }">
              <div class="flex flex-col items-center text-center px-8 py-10 space-y-3">
                <ClipboardDocumentListIcon class="w-7 h-7" :style="{ color: D.inkTertiary }" />
                <p class="text-sm leading-relaxed max-w-[240px]"
                   :style="{ color: D.inkTertiary }">
                  Nothing on your list — take a breather or add one.
                </p>
              </div>
            </div>
          </div>

          <!-- DARK · No Tasks · Desktop -->
          <div>
            <p class="text-[11px] font-medium uppercase tracking-wider mb-3" :style="{ color: D.inkTertiary }">
              Dark — no tasks — desktop
            </p>
            <div class="rounded-2xl border overflow-hidden"
                 :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
              <div class="flex flex-col items-center text-center px-8 py-10 space-y-3">
                <ClipboardDocumentListIcon class="w-7 h-7" :style="{ color: D.inkTertiary }" />
                <p class="text-sm leading-relaxed max-w-[280px]"
                   :style="{ color: D.inkTertiary }">
                  Nothing on your list — take a breather or add one.
                </p>
              </div>
            </div>
          </div>

          <!-- DARK · Empty Vault · Desktop -->
          <div>
            <p class="text-[11px] font-medium uppercase tracking-wider mb-3" :style="{ color: D.inkTertiary }">
              Dark — empty vault — desktop
            </p>
            <div class="rounded-2xl border overflow-hidden"
                 :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
              <div class="flex flex-col items-center text-center px-8 py-10 space-y-3">
                <LockClosedIcon class="w-7 h-7" :style="{ color: D.inkTertiary }" />
                <p class="text-sm leading-relaxed max-w-[280px]"
                   :style="{ color: D.inkTertiary }">
                  Your vault is empty — add your first entry to get started.
                </p>
              </div>
            </div>
          </div>

        </div>
      </VariantFrame>
      <p class="mt-3 text-sm px-1" :style="{ color: L.inkSecondary }">
        Variant C whispers rather than shouts. Use it only when the containing view already has a prominent CTA and the empty state itself is a transient sub-panel — e.g. a filter result inside a task list that already has an "Add task" button in the header.
      </p>
    </section>


    <!-- ══════════════════════════════════════════════════════════════════════
         CLAUDE'S PICK
         ═══════════════════════════════════════════════════════════════════ -->
    <section class="mb-8">
      <div class="rounded-2xl border p-6 space-y-3"
           :style="{ background: L.accents.lavender.soft, borderColor: L.accents.lavender.bold }">
        <div class="flex items-center gap-2">
          <SparklesIcon class="w-5 h-5" :style="{ color: L.accents.lavender.bold }" />
          <h2 class="text-[15px] font-semibold" :style="{ color: L.accents.lavender.bold }">Claude's pick — Variant A</h2>
        </div>
        <p class="text-[14px] leading-relaxed" :style="{ color: L.inkPrimary }">
          Variant A is the right default because it handles every context Kinhold throws at it — tasks, vault, rewards, calendar, chat — without demanding that every screen also carry a list of onboarding suggestions. The iridescent glyph container does the heavy lifting: it turns an absence into something visually alive, so the screen never feels broken or neglected.
        </p>
        <p class="text-[14px] leading-relaxed" :style="{ color: L.inkPrimary }">
          Variant B earns its place on the very first visit to any module — when a new family has nothing yet and needs a hand — but becomes noise once a user is past onboarding. Variant C is a background player: useful inside filtered sub-panels or sidebar lists where A or B would be visually overwhelming.
        </p>
      </div>
    </section>


    <!-- ══════════════════════════════════════════════════════════════════════
         USAGE GUIDE
         ═══════════════════════════════════════════════════════════════════ -->
    <section class="mb-8">
      <div class="rounded-2xl border p-6 space-y-5"
           :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle, boxShadow: SH_LT }">
        <h2 class="text-[15px] font-semibold" :style="{ color: L.inkPrimary }">Usage guide</h2>

        <div class="space-y-4">
          <div>
            <p class="text-[13px] font-semibold mb-1" :style="{ color: L.inkPrimary }">Variant A — Centered hero (default)</p>
            <ul class="space-y-1 text-[13px] leading-relaxed list-disc list-inside" :style="{ color: L.inkSecondary }">
              <li>Use as the default empty state across all top-level module views: tasks, vault, calendar, rewards, chat, meal plan.</li>
              <li>Swap the accent color to match the module's context (lavender for tasks, mint for vault, etc.) to reinforce spatial identity.</li>
              <li>Keep the CTA label to 2–3 words maximum. "Add task", "Add entry", "Plan week" — not "Click here to add your first task".</li>
              <li>py-16 is the minimum vertical padding. Never collapse it below py-10 — the glyph needs breathing room to land.</li>
            </ul>
          </div>

          <div>
            <p class="text-[13px] font-semibold mb-1" :style="{ color: L.inkPrimary }">Variant B — Hero + suggested actions (onboarding)</p>
            <ul class="space-y-1 text-[13px] leading-relaxed list-disc list-inside" :style="{ color: L.inkSecondary }">
              <li>Use on the first visit to any module — when the family account has zero items and the user needs concrete first steps.</li>
              <li>Limit to 3 suggestion rows. More than 3 creates a menu, not a nudge.</li>
              <li>Each suggestion row must be an actionable verb phrase, not a feature name: "Create a family chore", not "Task creation".</li>
              <li>Retire Variant B once the user has created at least one item in the module — fall back to Variant A.</li>
            </ul>
          </div>

          <div>
            <p class="text-[13px] font-semibold mb-1" :style="{ color: L.inkPrimary }">Variant C — Minimal (nested / terminal)</p>
            <ul class="space-y-1 text-[13px] leading-relaxed list-disc list-inside" :style="{ color: L.inkSecondary }">
              <li>Use only when the parent container already provides a clear CTA (e.g. a sidebar panel, a filter result, a sub-category list).</li>
              <li>Never use Variant C as the only empty state on a full-page view — the user has nowhere to go.</li>
              <li>The copy should still be human: "Nothing here yet" reads better than "No items found".</li>
              <li>py-10 is the target padding — tight enough to feel like a secondary element, spacious enough not to look broken.</li>
            </ul>
          </div>

          <div class="rounded-xl p-4 space-y-1"
               :style="{ background: L.surfaceSunken }">
            <p class="text-[13px] font-semibold" :style="{ color: L.inkPrimary }">Hard rules (all variants)</p>
            <ul class="space-y-1 text-[13px] leading-relaxed list-disc list-inside" :style="{ color: L.inkSecondary }">
              <li>No emoji in empty state copy. Heroicons only for the glyph container and suggestion rows.</li>
              <li>The iridescent gradient always uses the three-stop lavender → peach → mint progression. Do not reorder the stops.</li>
              <li>CTA button is always pill (rounded-full), always filled — never ghost or outline on an empty state.</li>
              <li>Variant A/B glyph icon should match the module's primary noun (clipboard for tasks, lock for vault, calendar for events).</li>
            </ul>
          </div>
        </div>
      </div>
    </section>

  </ComponentPage>
</template>

<style scoped>
/* ── Iridescent glyph container — light ────────────────────────────────────── */
.empty-glyph-lt {
  width: 96px;
  height: 96px;
  border-radius: 9999px;
  background: linear-gradient(135deg, #EAE6F8, #FCE9E0, #D5F2E8);
  box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.6);
  flex-shrink: 0;
}

/* Mint accent variant (vault, etc.) */
.empty-glyph-lt--mint {
  background: linear-gradient(135deg, #D5F2E8, #EAE6F8, #FCE9E0);
}

/* ── Iridescent glyph container — dark ─────────────────────────────────────── */
.empty-glyph-dk {
  width: 96px;
  height: 96px;
  border-radius: 9999px;
  background: linear-gradient(135deg, #302A48, #3E241A, #18342A);
  box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.08);
  flex-shrink: 0;
}

.empty-glyph-dk--mint {
  background: linear-gradient(135deg, #18342A, #302A48, #3E241A);
}
</style>
