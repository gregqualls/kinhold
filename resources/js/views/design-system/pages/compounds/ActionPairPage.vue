<script setup>
import { ref } from 'vue'
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import {
  SparklesIcon,
  CheckIcon,
  TrashIcon,
  ArrowRightIcon,
  PencilSquareIcon,
} from '@heroicons/vue/24/outline'
import KinActionPair from '@/components/design-system/KinActionPair.vue'
import KinButton from '@/components/design-system/KinButton.vue'

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
  status: {
    success: '#4D8C6A', pending: '#486E9C', paused: '#BE8230',
    failed:  '#BA4A4A', info:    '#6856B2', warning: '#C48C24',
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
  status: {
    success: '#6CC498', pending: '#78A4DC', paused: '#DCA848',
    failed:  '#E67070', info:    '#B6A8E6', warning: '#E6C452',
  },
}

// ── Shadow helpers ────────────────────────────────────────────────────────────
const SH_LT  = '0 1px 2px rgba(28,20,10,0.04), 0 2px 6px rgba(28,20,10,0.05)'
const SH_DK  = '0 1px 2px rgba(0,0,0,0.30), 0 2px 6px rgba(0,0,0,0.25)'

// ── Hover state tracking ──────────────────────────────────────────────────────
// One ref per interactive button so hover styles are reactive but lightweight.
// Keys: "light-<section>-<btn>" and "dark-<section>-<btn>"
const hov = ref({})
function onEnter(k) { hov.value = { ...hov.value, [k]: true } }
function onLeave(k) { hov.value = { ...hov.value, [k]: false } }
</script>

<template>
  <ComponentPage
    title="4.4 ActionPair"
    description="The canonical two-button decision pattern — a secondary action next to a primary action. Solves the outline/filled, sizing, and gap questions for list rows and decision cards app-wide."
    status="scaffolded"
  >

    <!-- ══════════════════════════════════════════════════════════════════════
         VARIANT A — Equal-width [outline] [filled]
         ═══════════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="A" caption="Equal-width [outline] [filled] — symmetric 50/50 layout">
        <div class="w-full space-y-10">

          <!-- LIGHT PANEL -->
          <div class="rounded-2xl border p-6 space-y-8"
               :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest"
               :style="{ color: L.inkTertiary }">Light mode</p>

            <!-- Affirmative -->
            <div class="space-y-2">
              <p class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: L.inkTertiary }">Affirmative</p>
              <div class="flex gap-2">
                <button
                  class="btn-a-outline-lt flex-1"
                  :style="{
                    height: '40px', borderRadius: '9999px', fontSize: '14px', fontWeight: '500',
                    background: hov['la-aff-sec'] ? L.surfaceSunken : 'transparent',
                    color: L.inkPrimary, border: `1px solid ${L.borderStrong}`,
                    cursor: 'pointer', transition: 'background 150ms',
                  }"
                  @mouseenter="onEnter('la-aff-sec')" @mouseleave="onLeave('la-aff-sec')"
                >Later</button>
                <button
                  class="flex-1"
                  :style="{
                    height: '40px', borderRadius: '9999px', fontSize: '14px', fontWeight: '500',
                    background: L.accents.lavender.bold, color: L.inkInverse,
                    border: 'none', cursor: 'pointer',
                    boxShadow: hov['la-aff-pri'] ? '0 2px 8px rgba(104,86,178,0.35)' : SH_LT,
                    transform: hov['la-aff-pri'] ? 'translateY(-1px)' : 'none',
                    transition: 'box-shadow 150ms, transform 150ms',
                  }"
                  @mouseenter="onEnter('la-aff-pri')" @mouseleave="onLeave('la-aff-pri')"
                >Confirm</button>
              </div>
            </div>

            <!-- Destructive -->
            <div class="space-y-2">
              <p class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: L.inkTertiary }">Destructive</p>
              <div class="flex gap-2">
                <button
                  class="flex-1"
                  :style="{
                    height: '40px', borderRadius: '9999px', fontSize: '14px', fontWeight: '500',
                    background: hov['la-des-sec'] ? L.surfaceSunken : 'transparent',
                    color: L.inkPrimary, border: `1px solid ${L.borderStrong}`,
                    cursor: 'pointer', transition: 'background 150ms',
                  }"
                  @mouseenter="onEnter('la-des-sec')" @mouseleave="onLeave('la-des-sec')"
                >Keep</button>
                <button
                  class="flex-1 flex items-center justify-center gap-1.5"
                  :style="{
                    height: '40px', borderRadius: '9999px', fontSize: '14px', fontWeight: '500',
                    background: L.status.failed, color: L.inkInverse,
                    border: 'none', cursor: 'pointer',
                    boxShadow: hov['la-des-pri'] ? '0 2px 8px rgba(186,74,74,0.35)' : SH_LT,
                    transform: hov['la-des-pri'] ? 'translateY(-1px)' : 'none',
                    transition: 'box-shadow 150ms, transform 150ms',
                  }"
                  @mouseenter="onEnter('la-des-pri')" @mouseleave="onLeave('la-des-pri')"
                >
                  <TrashIcon class="w-4 h-4" />
                  Delete
                </button>
              </div>
            </div>

            <!-- Inside a list row -->
            <div class="space-y-2">
              <p class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: L.inkTertiary }">Inside a list row</p>
              <div class="rounded-2xl border p-4 flex items-center gap-4"
                   :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle, boxShadow: SH_LT }">
                <div class="flex-1 min-w-0">
                  <p class="text-[14px] font-medium truncate" :style="{ color: L.inkPrimary }">Redeem: Movie Night Pass</p>
                  <p class="text-[12px] mt-0.5" :style="{ color: L.inkSecondary }">150 pts · expires Jun 30</p>
                </div>
                <div class="flex gap-2 flex-shrink-0">
                  <button
                    :style="{
                      height: '36px', paddingLeft: '16px', paddingRight: '16px',
                      borderRadius: '9999px', fontSize: '13px', fontWeight: '500',
                      background: hov['la-row-sec'] ? L.surfaceSunken : 'transparent',
                      color: L.inkPrimary, border: `1px solid ${L.borderStrong}`,
                      cursor: 'pointer', transition: 'background 150ms', whiteSpace: 'nowrap',
                    }"
                    @mouseenter="onEnter('la-row-sec')" @mouseleave="onLeave('la-row-sec')"
                  >View</button>
                  <button
                    :style="{
                      height: '36px', paddingLeft: '16px', paddingRight: '16px',
                      borderRadius: '9999px', fontSize: '13px', fontWeight: '500',
                      background: L.accents.lavender.bold, color: L.inkInverse,
                      border: 'none', cursor: 'pointer',
                      boxShadow: hov['la-row-pri'] ? '0 2px 8px rgba(104,86,178,0.35)' : SH_LT,
                      transform: hov['la-row-pri'] ? 'translateY(-1px)' : 'none',
                      transition: 'box-shadow 150ms, transform 150ms', whiteSpace: 'nowrap',
                    }"
                    @mouseenter="onEnter('la-row-pri')" @mouseleave="onLeave('la-row-pri')"
                  >Redeem</button>
                </div>
              </div>
            </div>

            <!-- Loading state -->
            <div class="space-y-2">
              <p class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: L.inkTertiary }">Loading (primary)</p>
              <div class="flex gap-2">
                <button
                  :style="{
                    height: '40px', flex: '1', borderRadius: '9999px', fontSize: '14px', fontWeight: '500',
                    background: 'transparent', color: L.inkPrimary, border: `1px solid ${L.borderStrong}`,
                    cursor: 'default',
                  }"
                >Later</button>
                <button
                  class="flex-1 flex items-center justify-center gap-2"
                  :style="{
                    height: '40px', borderRadius: '9999px', fontSize: '14px', fontWeight: '500',
                    background: L.accents.lavender.bold, color: L.inkInverse,
                    border: 'none', cursor: 'not-allowed', opacity: '0.75', boxShadow: SH_LT,
                  }"
                  disabled
                >
                  <span class="dot-pulse-lt" />
                  Confirming…
                </button>
              </div>
            </div>

            <!-- Disabled state -->
            <div class="space-y-2">
              <p class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: L.inkTertiary }">Disabled (both)</p>
              <div class="flex gap-2">
                <button
                  :style="{
                    height: '40px', flex: '1', borderRadius: '9999px', fontSize: '14px', fontWeight: '500',
                    background: 'transparent', color: L.inkPrimary, border: `1px solid ${L.borderStrong}`,
                    cursor: 'not-allowed', opacity: '0.4',
                  }"
                  disabled
                >Later</button>
                <button
                  :style="{
                    height: '40px', flex: '1', borderRadius: '9999px', fontSize: '14px', fontWeight: '500',
                    background: L.accents.lavender.bold, color: L.inkInverse,
                    border: 'none', cursor: 'not-allowed', opacity: '0.4', boxShadow: 'none',
                  }"
                  disabled
                >Confirm</button>
              </div>
            </div>
          </div><!-- /light -->

          <!-- DARK PANEL -->
          <div class="rounded-2xl border p-6 space-y-8"
               :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest"
               :style="{ color: D.inkTertiary }">Dark mode</p>

            <!-- Affirmative -->
            <div class="space-y-2">
              <p class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: D.inkTertiary }">Affirmative</p>
              <div class="flex gap-2">
                <button
                  class="flex-1"
                  :style="{
                    height: '40px', borderRadius: '9999px', fontSize: '14px', fontWeight: '500',
                    background: hov['da-aff-sec'] ? D.surfaceSunken : 'transparent',
                    color: D.inkPrimary, border: `1px solid ${D.borderStrong}`,
                    cursor: 'pointer', transition: 'background 150ms',
                  }"
                  @mouseenter="onEnter('da-aff-sec')" @mouseleave="onLeave('da-aff-sec')"
                >Later</button>
                <button
                  class="flex-1"
                  :style="{
                    height: '40px', borderRadius: '9999px', fontSize: '14px', fontWeight: '500',
                    background: D.accents.lavender.bold, color: D.inkInverse,
                    border: 'none', cursor: 'pointer',
                    boxShadow: hov['da-aff-pri'] ? '0 2px 8px rgba(182,168,230,0.30)' : SH_DK,
                    transform: hov['da-aff-pri'] ? 'translateY(-1px)' : 'none',
                    transition: 'box-shadow 150ms, transform 150ms',
                  }"
                  @mouseenter="onEnter('da-aff-pri')" @mouseleave="onLeave('da-aff-pri')"
                >Confirm</button>
              </div>
            </div>

            <!-- Destructive -->
            <div class="space-y-2">
              <p class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: D.inkTertiary }">Destructive</p>
              <div class="flex gap-2">
                <button
                  class="flex-1"
                  :style="{
                    height: '40px', borderRadius: '9999px', fontSize: '14px', fontWeight: '500',
                    background: hov['da-des-sec'] ? D.surfaceSunken : 'transparent',
                    color: D.inkPrimary, border: `1px solid ${D.borderStrong}`,
                    cursor: 'pointer', transition: 'background 150ms',
                  }"
                  @mouseenter="onEnter('da-des-sec')" @mouseleave="onLeave('da-des-sec')"
                >Keep</button>
                <button
                  class="flex-1 flex items-center justify-center gap-1.5"
                  :style="{
                    height: '40px', borderRadius: '9999px', fontSize: '14px', fontWeight: '500',
                    background: D.status.failed, color: D.inkInverse,
                    border: 'none', cursor: 'pointer',
                    boxShadow: hov['da-des-pri'] ? '0 2px 8px rgba(230,112,112,0.30)' : SH_DK,
                    transform: hov['da-des-pri'] ? 'translateY(-1px)' : 'none',
                    transition: 'box-shadow 150ms, transform 150ms',
                  }"
                  @mouseenter="onEnter('da-des-pri')" @mouseleave="onLeave('da-des-pri')"
                >
                  <TrashIcon class="w-4 h-4" />
                  Delete
                </button>
              </div>
            </div>

            <!-- Inside a list row -->
            <div class="space-y-2">
              <p class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: D.inkTertiary }">Inside a list row</p>
              <div class="rounded-2xl border p-4 flex items-center gap-4"
                   :style="{ background: D.surfaceRaised, borderColor: D.borderSubtle, boxShadow: SH_DK }">
                <div class="flex-1 min-w-0">
                  <p class="text-[14px] font-medium truncate" :style="{ color: D.inkPrimary }">Redeem: Movie Night Pass</p>
                  <p class="text-[12px] mt-0.5" :style="{ color: D.inkSecondary }">150 pts · expires Jun 30</p>
                </div>
                <div class="flex gap-2 flex-shrink-0">
                  <button
                    :style="{
                      height: '36px', paddingLeft: '16px', paddingRight: '16px',
                      borderRadius: '9999px', fontSize: '13px', fontWeight: '500',
                      background: hov['da-row-sec'] ? D.surfaceSunken : 'transparent',
                      color: D.inkPrimary, border: `1px solid ${D.borderStrong}`,
                      cursor: 'pointer', transition: 'background 150ms', whiteSpace: 'nowrap',
                    }"
                    @mouseenter="onEnter('da-row-sec')" @mouseleave="onLeave('da-row-sec')"
                  >View</button>
                  <button
                    :style="{
                      height: '36px', paddingLeft: '16px', paddingRight: '16px',
                      borderRadius: '9999px', fontSize: '13px', fontWeight: '500',
                      background: D.accents.lavender.bold, color: D.inkInverse,
                      border: 'none', cursor: 'pointer',
                      boxShadow: hov['da-row-pri'] ? '0 2px 8px rgba(182,168,230,0.30)' : SH_DK,
                      transform: hov['da-row-pri'] ? 'translateY(-1px)' : 'none',
                      transition: 'box-shadow 150ms, transform 150ms', whiteSpace: 'nowrap',
                    }"
                    @mouseenter="onEnter('da-row-pri')" @mouseleave="onLeave('da-row-pri')"
                  >Redeem</button>
                </div>
              </div>
            </div>

            <!-- Loading -->
            <div class="space-y-2">
              <p class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: D.inkTertiary }">Loading (primary)</p>
              <div class="flex gap-2">
                <button
                  :style="{
                    height: '40px', flex: '1', borderRadius: '9999px', fontSize: '14px', fontWeight: '500',
                    background: 'transparent', color: D.inkPrimary, border: `1px solid ${D.borderStrong}`,
                    cursor: 'default',
                  }"
                >Later</button>
                <button
                  class="flex-1 flex items-center justify-center gap-2"
                  :style="{
                    height: '40px', borderRadius: '9999px', fontSize: '14px', fontWeight: '500',
                    background: D.accents.lavender.bold, color: D.inkInverse,
                    border: 'none', cursor: 'not-allowed', opacity: '0.75', boxShadow: SH_DK,
                  }"
                  disabled
                >
                  <span class="dot-pulse-dk" />
                  Confirming…
                </button>
              </div>
            </div>

            <!-- Disabled -->
            <div class="space-y-2">
              <p class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: D.inkTertiary }">Disabled (both)</p>
              <div class="flex gap-2">
                <button
                  :style="{
                    height: '40px', flex: '1', borderRadius: '9999px', fontSize: '14px', fontWeight: '500',
                    background: 'transparent', color: D.inkPrimary, border: `1px solid ${D.borderStrong}`,
                    cursor: 'not-allowed', opacity: '0.4',
                  }"
                  disabled
                >Later</button>
                <button
                  :style="{
                    height: '40px', flex: '1', borderRadius: '9999px', fontSize: '14px', fontWeight: '500',
                    background: D.accents.lavender.bold, color: D.inkInverse,
                    border: 'none', cursor: 'not-allowed', opacity: '0.4', boxShadow: 'none',
                  }"
                  disabled
                >Confirm</button>
              </div>
            </div>
          </div><!-- /dark -->
        </div>
      </VariantFrame>
      <p class="mt-3 text-sm px-1" :style="{ color: L.inkSecondary }">
        Equal width gives balanced visual weight — neither button dominates. Best when both actions carry real consequence (e.g. Decline / Accept, Keep / Delete).
      </p>
    </section>


    <!-- ══════════════════════════════════════════════════════════════════════
         VARIANT B — Asymmetric [ghost] [filled]
         ═══════════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="B" caption="Asymmetric [ghost] [filled ~2/3] — confident hierarchy">
        <div class="w-full space-y-10">

          <!-- LIGHT PANEL -->
          <div class="rounded-2xl border p-6 space-y-8"
               :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest"
               :style="{ color: L.inkTertiary }">Light mode</p>

            <!-- Affirmative -->
            <div class="space-y-2">
              <p class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: L.inkTertiary }">Affirmative</p>
              <div class="flex gap-2">
                <button
                  :style="{
                    height: '40px', flex: '1',
                    borderRadius: '9999px', fontSize: '14px', fontWeight: '500',
                    background: hov['lb-aff-sec'] ? L.surfaceSunken : 'transparent',
                    color: L.inkSecondary, border: 'none',
                    cursor: 'pointer', transition: 'background 150ms',
                  }"
                  @mouseenter="onEnter('lb-aff-sec')" @mouseleave="onLeave('lb-aff-sec')"
                >Skip</button>
                <button
                  :style="{
                    height: '40px', flex: '2',
                    borderRadius: '9999px', fontSize: '14px', fontWeight: '500',
                    background: L.accents.lavender.bold, color: L.inkInverse,
                    border: 'none', cursor: 'pointer',
                    boxShadow: hov['lb-aff-pri'] ? '0 2px 8px rgba(104,86,178,0.35)' : SH_LT,
                    transform: hov['lb-aff-pri'] ? 'translateY(-1px)' : 'none',
                    transition: 'box-shadow 150ms, transform 150ms',
                  }"
                  @mouseenter="onEnter('lb-aff-pri')" @mouseleave="onLeave('lb-aff-pri')"
                >Send Kudos</button>
              </div>
            </div>

            <!-- Destructive -->
            <div class="space-y-2">
              <p class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: L.inkTertiary }">Destructive</p>
              <div class="flex gap-2">
                <button
                  :style="{
                    height: '40px', flex: '1',
                    borderRadius: '9999px', fontSize: '14px', fontWeight: '500',
                    background: hov['lb-des-sec'] ? L.surfaceSunken : 'transparent',
                    color: L.inkSecondary, border: 'none',
                    cursor: 'pointer', transition: 'background 150ms',
                  }"
                  @mouseenter="onEnter('lb-des-sec')" @mouseleave="onLeave('lb-des-sec')"
                >Keep</button>
                <button
                  class="flex items-center justify-center gap-1.5"
                  :style="{
                    height: '40px', flex: '2',
                    borderRadius: '9999px', fontSize: '14px', fontWeight: '500',
                    background: L.status.failed, color: L.inkInverse,
                    border: 'none', cursor: 'pointer',
                    boxShadow: hov['lb-des-pri'] ? '0 2px 8px rgba(186,74,74,0.35)' : SH_LT,
                    transform: hov['lb-des-pri'] ? 'translateY(-1px)' : 'none',
                    transition: 'box-shadow 150ms, transform 150ms',
                  }"
                  @mouseenter="onEnter('lb-des-pri')" @mouseleave="onLeave('lb-des-pri')"
                >
                  <TrashIcon class="w-4 h-4" />
                  Delete Forever
                </button>
              </div>
            </div>

            <!-- Inside a list row -->
            <div class="space-y-2">
              <p class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: L.inkTertiary }">Inside a list row</p>
              <div class="rounded-2xl border p-4 flex items-center gap-4"
                   :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle, boxShadow: SH_LT }">
                <div class="flex-1 min-w-0">
                  <p class="text-[14px] font-medium truncate" :style="{ color: L.inkPrimary }">Clean the kitchen</p>
                  <p class="text-[12px] mt-0.5" :style="{ color: L.inkSecondary }">20 pts · assigned to you</p>
                </div>
                <div class="flex gap-2 flex-shrink-0">
                  <button
                    :style="{
                      height: '36px', paddingLeft: '14px', paddingRight: '14px',
                      borderRadius: '9999px', fontSize: '13px', fontWeight: '500',
                      background: hov['lb-row-sec'] ? L.surfaceSunken : 'transparent',
                      color: L.inkSecondary, border: 'none',
                      cursor: 'pointer', transition: 'background 150ms', whiteSpace: 'nowrap',
                    }"
                    @mouseenter="onEnter('lb-row-sec')" @mouseleave="onLeave('lb-row-sec')"
                  >Edit</button>
                  <button
                    class="flex items-center gap-1.5"
                    :style="{
                      height: '36px', paddingLeft: '16px', paddingRight: '16px',
                      borderRadius: '9999px', fontSize: '13px', fontWeight: '500',
                      background: L.accents.lavender.bold, color: L.inkInverse,
                      border: 'none', cursor: 'pointer',
                      boxShadow: hov['lb-row-pri'] ? '0 2px 8px rgba(104,86,178,0.35)' : SH_LT,
                      transform: hov['lb-row-pri'] ? 'translateY(-1px)' : 'none',
                      transition: 'box-shadow 150ms, transform 150ms', whiteSpace: 'nowrap',
                    }"
                    @mouseenter="onEnter('lb-row-pri')" @mouseleave="onLeave('lb-row-pri')"
                  >
                    <CheckIcon class="w-4 h-4" />
                    Complete
                  </button>
                </div>
              </div>
            </div>

            <!-- Loading -->
            <div class="space-y-2">
              <p class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: L.inkTertiary }">Loading (primary)</p>
              <div class="flex gap-2">
                <button
                  :style="{
                    height: '40px', flex: '1',
                    borderRadius: '9999px', fontSize: '14px', fontWeight: '500',
                    background: 'transparent', color: L.inkSecondary, border: 'none',
                    cursor: 'default',
                  }"
                >Skip</button>
                <button
                  class="flex-[2] flex items-center justify-center gap-2"
                  :style="{
                    height: '40px',
                    borderRadius: '9999px', fontSize: '14px', fontWeight: '500',
                    background: L.accents.lavender.bold, color: L.inkInverse,
                    border: 'none', cursor: 'not-allowed', opacity: '0.75', boxShadow: SH_LT,
                  }"
                  disabled
                >
                  <span class="dot-pulse-lt" />
                  Sending…
                </button>
              </div>
            </div>

            <!-- Disabled -->
            <div class="space-y-2">
              <p class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: L.inkTertiary }">Disabled (both)</p>
              <div class="flex gap-2">
                <button
                  :style="{
                    height: '40px', flex: '1',
                    borderRadius: '9999px', fontSize: '14px', fontWeight: '500',
                    background: 'transparent', color: L.inkSecondary, border: 'none',
                    cursor: 'not-allowed', opacity: '0.4',
                  }"
                  disabled
                >Skip</button>
                <button
                  :style="{
                    height: '40px', flex: '2',
                    borderRadius: '9999px', fontSize: '14px', fontWeight: '500',
                    background: L.accents.lavender.bold, color: L.inkInverse,
                    border: 'none', cursor: 'not-allowed', opacity: '0.4', boxShadow: 'none',
                  }"
                  disabled
                >Send Kudos</button>
              </div>
            </div>
          </div><!-- /light -->

          <!-- DARK PANEL -->
          <div class="rounded-2xl border p-6 space-y-8"
               :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest"
               :style="{ color: D.inkTertiary }">Dark mode</p>

            <!-- Affirmative -->
            <div class="space-y-2">
              <p class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: D.inkTertiary }">Affirmative</p>
              <div class="flex gap-2">
                <button
                  :style="{
                    height: '40px', flex: '1',
                    borderRadius: '9999px', fontSize: '14px', fontWeight: '500',
                    background: hov['db-aff-sec'] ? D.surfaceSunken : 'transparent',
                    color: D.inkSecondary, border: 'none',
                    cursor: 'pointer', transition: 'background 150ms',
                  }"
                  @mouseenter="onEnter('db-aff-sec')" @mouseleave="onLeave('db-aff-sec')"
                >Skip</button>
                <button
                  :style="{
                    height: '40px', flex: '2',
                    borderRadius: '9999px', fontSize: '14px', fontWeight: '500',
                    background: D.accents.lavender.bold, color: D.inkInverse,
                    border: 'none', cursor: 'pointer',
                    boxShadow: hov['db-aff-pri'] ? '0 2px 8px rgba(182,168,230,0.30)' : SH_DK,
                    transform: hov['db-aff-pri'] ? 'translateY(-1px)' : 'none',
                    transition: 'box-shadow 150ms, transform 150ms',
                  }"
                  @mouseenter="onEnter('db-aff-pri')" @mouseleave="onLeave('db-aff-pri')"
                >Send Kudos</button>
              </div>
            </div>

            <!-- Destructive -->
            <div class="space-y-2">
              <p class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: D.inkTertiary }">Destructive</p>
              <div class="flex gap-2">
                <button
                  :style="{
                    height: '40px', flex: '1',
                    borderRadius: '9999px', fontSize: '14px', fontWeight: '500',
                    background: hov['db-des-sec'] ? D.surfaceSunken : 'transparent',
                    color: D.inkSecondary, border: 'none',
                    cursor: 'pointer', transition: 'background 150ms',
                  }"
                  @mouseenter="onEnter('db-des-sec')" @mouseleave="onLeave('db-des-sec')"
                >Keep</button>
                <button
                  class="flex items-center justify-center gap-1.5"
                  :style="{
                    height: '40px', flex: '2',
                    borderRadius: '9999px', fontSize: '14px', fontWeight: '500',
                    background: D.status.failed, color: D.inkInverse,
                    border: 'none', cursor: 'pointer',
                    boxShadow: hov['db-des-pri'] ? '0 2px 8px rgba(230,112,112,0.30)' : SH_DK,
                    transform: hov['db-des-pri'] ? 'translateY(-1px)' : 'none',
                    transition: 'box-shadow 150ms, transform 150ms',
                  }"
                  @mouseenter="onEnter('db-des-pri')" @mouseleave="onLeave('db-des-pri')"
                >
                  <TrashIcon class="w-4 h-4" />
                  Delete Forever
                </button>
              </div>
            </div>

            <!-- Inside a list row -->
            <div class="space-y-2">
              <p class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: D.inkTertiary }">Inside a list row</p>
              <div class="rounded-2xl border p-4 flex items-center gap-4"
                   :style="{ background: D.surfaceRaised, borderColor: D.borderSubtle, boxShadow: SH_DK }">
                <div class="flex-1 min-w-0">
                  <p class="text-[14px] font-medium truncate" :style="{ color: D.inkPrimary }">Clean the kitchen</p>
                  <p class="text-[12px] mt-0.5" :style="{ color: D.inkSecondary }">20 pts · assigned to you</p>
                </div>
                <div class="flex gap-2 flex-shrink-0">
                  <button
                    :style="{
                      height: '36px', paddingLeft: '14px', paddingRight: '14px',
                      borderRadius: '9999px', fontSize: '13px', fontWeight: '500',
                      background: hov['db-row-sec'] ? D.surfaceSunken : 'transparent',
                      color: D.inkSecondary, border: 'none',
                      cursor: 'pointer', transition: 'background 150ms', whiteSpace: 'nowrap',
                    }"
                    @mouseenter="onEnter('db-row-sec')" @mouseleave="onLeave('db-row-sec')"
                  >Edit</button>
                  <button
                    class="flex items-center gap-1.5"
                    :style="{
                      height: '36px', paddingLeft: '16px', paddingRight: '16px',
                      borderRadius: '9999px', fontSize: '13px', fontWeight: '500',
                      background: D.accents.lavender.bold, color: D.inkInverse,
                      border: 'none', cursor: 'pointer',
                      boxShadow: hov['db-row-pri'] ? '0 2px 8px rgba(182,168,230,0.30)' : SH_DK,
                      transform: hov['db-row-pri'] ? 'translateY(-1px)' : 'none',
                      transition: 'box-shadow 150ms, transform 150ms', whiteSpace: 'nowrap',
                    }"
                    @mouseenter="onEnter('db-row-pri')" @mouseleave="onLeave('db-row-pri')"
                  >
                    <CheckIcon class="w-4 h-4" />
                    Complete
                  </button>
                </div>
              </div>
            </div>

            <!-- Loading -->
            <div class="space-y-2">
              <p class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: D.inkTertiary }">Loading (primary)</p>
              <div class="flex gap-2">
                <button
                  :style="{
                    height: '40px', flex: '1',
                    borderRadius: '9999px', fontSize: '14px', fontWeight: '500',
                    background: 'transparent', color: D.inkSecondary, border: 'none',
                    cursor: 'default',
                  }"
                >Skip</button>
                <button
                  class="flex-[2] flex items-center justify-center gap-2"
                  :style="{
                    height: '40px',
                    borderRadius: '9999px', fontSize: '14px', fontWeight: '500',
                    background: D.accents.lavender.bold, color: D.inkInverse,
                    border: 'none', cursor: 'not-allowed', opacity: '0.75', boxShadow: SH_DK,
                  }"
                  disabled
                >
                  <span class="dot-pulse-dk" />
                  Sending…
                </button>
              </div>
            </div>

            <!-- Disabled -->
            <div class="space-y-2">
              <p class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: D.inkTertiary }">Disabled (both)</p>
              <div class="flex gap-2">
                <button
                  :style="{
                    height: '40px', flex: '1',
                    borderRadius: '9999px', fontSize: '14px', fontWeight: '500',
                    background: 'transparent', color: D.inkSecondary, border: 'none',
                    cursor: 'not-allowed', opacity: '0.4',
                  }"
                  disabled
                >Skip</button>
                <button
                  :style="{
                    height: '40px', flex: '2',
                    borderRadius: '9999px', fontSize: '14px', fontWeight: '500',
                    background: D.accents.lavender.bold, color: D.inkInverse,
                    border: 'none', cursor: 'not-allowed', opacity: '0.4', boxShadow: 'none',
                  }"
                  disabled
                >Send Kudos</button>
              </div>
            </div>
          </div><!-- /dark -->
        </div>
      </VariantFrame>
      <p class="mt-3 text-sm px-1" :style="{ color: L.inkSecondary }">
        Ghost secondary shrinks to near-invisible, letting the filled primary dominate. Best when you want the user to clearly land on the primary — kudos prompts, onboarding nudges, non-destructive confirmations.
      </p>
    </section>


    <!-- ══════════════════════════════════════════════════════════════════════
         VARIANT C — Responsive stacked-then-inline
         ═══════════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="C" caption="Responsive: stacked on mobile (filled top), side-by-side equal-width on desktop">
        <div class="w-full space-y-10">

          <!-- LIGHT PANEL -->
          <div class="rounded-2xl border p-6 space-y-8"
               :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest"
               :style="{ color: L.inkTertiary }">Light mode</p>

            <!-- Affirmative -->
            <div class="space-y-2">
              <p class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: L.inkTertiary }">Affirmative</p>
              <div class="action-pair-stack">
                <button
                  class="action-pair-primary"
                  :style="{
                    height: '40px', borderRadius: '9999px', fontSize: '14px', fontWeight: '500',
                    background: L.accents.lavender.bold, color: L.inkInverse,
                    border: 'none', cursor: 'pointer',
                    boxShadow: hov['lc-aff-pri'] ? '0 2px 8px rgba(104,86,178,0.35)' : SH_LT,
                    transform: hov['lc-aff-pri'] ? 'translateY(-1px)' : 'none',
                    transition: 'box-shadow 150ms, transform 150ms',
                  }"
                  @mouseenter="onEnter('lc-aff-pri')" @mouseleave="onLeave('lc-aff-pri')"
                >Accept</button>
                <button
                  class="action-pair-secondary"
                  :style="{
                    height: '40px', borderRadius: '9999px', fontSize: '14px', fontWeight: '500',
                    background: hov['lc-aff-sec'] ? L.surfaceSunken : 'transparent',
                    color: L.inkPrimary, border: `1px solid ${L.borderStrong}`,
                    cursor: 'pointer', transition: 'background 150ms',
                  }"
                  @mouseenter="onEnter('lc-aff-sec')" @mouseleave="onLeave('lc-aff-sec')"
                >Decline</button>
              </div>
            </div>

            <!-- Destructive -->
            <div class="space-y-2">
              <p class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: L.inkTertiary }">Destructive</p>
              <div class="action-pair-stack">
                <button
                  class="action-pair-primary flex items-center justify-center gap-1.5"
                  :style="{
                    height: '40px', borderRadius: '9999px', fontSize: '14px', fontWeight: '500',
                    background: L.status.failed, color: L.inkInverse,
                    border: 'none', cursor: 'pointer',
                    boxShadow: hov['lc-des-pri'] ? '0 2px 8px rgba(186,74,74,0.35)' : SH_LT,
                    transform: hov['lc-des-pri'] ? 'translateY(-1px)' : 'none',
                    transition: 'box-shadow 150ms, transform 150ms',
                  }"
                  @mouseenter="onEnter('lc-des-pri')" @mouseleave="onLeave('lc-des-pri')"
                >
                  <TrashIcon class="w-4 h-4" />
                  Delete
                </button>
                <button
                  class="action-pair-secondary"
                  :style="{
                    height: '40px', borderRadius: '9999px', fontSize: '14px', fontWeight: '500',
                    background: hov['lc-des-sec'] ? L.surfaceSunken : 'transparent',
                    color: L.inkPrimary, border: `1px solid ${L.borderStrong}`,
                    cursor: 'pointer', transition: 'background 150ms',
                  }"
                  @mouseenter="onEnter('lc-des-sec')" @mouseleave="onLeave('lc-des-sec')"
                >Keep</button>
              </div>
            </div>

            <!-- Inside a list row -->
            <div class="space-y-2">
              <p class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: L.inkTertiary }">Inside a list row (invitation card)</p>
              <div class="rounded-2xl border p-4 space-y-4"
                   :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle, boxShadow: SH_LT }">
                <div>
                  <p class="text-[14px] font-medium" :style="{ color: L.inkPrimary }">New invitation from Mom</p>
                  <p class="text-[12px] mt-0.5" :style="{ color: L.inkSecondary }">Family movie night · Sat Apr 19 at 7 pm</p>
                </div>
                <div class="action-pair-stack">
                  <button
                    class="action-pair-primary"
                    :style="{
                      height: '38px', borderRadius: '9999px', fontSize: '13px', fontWeight: '500',
                      background: L.accents.lavender.bold, color: L.inkInverse,
                      border: 'none', cursor: 'pointer', boxShadow: SH_LT,
                    }"
                  >Accept</button>
                  <button
                    class="action-pair-secondary"
                    :style="{
                      height: '38px', borderRadius: '9999px', fontSize: '13px', fontWeight: '500',
                      background: 'transparent', color: L.inkPrimary, border: `1px solid ${L.borderStrong}`,
                      cursor: 'pointer',
                    }"
                  >Decline</button>
                </div>
              </div>
            </div>

            <!-- Loading -->
            <div class="space-y-2">
              <p class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: L.inkTertiary }">Loading (primary)</p>
              <div class="action-pair-stack">
                <button
                  class="action-pair-primary flex items-center justify-center gap-2"
                  :style="{
                    height: '40px', borderRadius: '9999px', fontSize: '14px', fontWeight: '500',
                    background: L.accents.lavender.bold, color: L.inkInverse,
                    border: 'none', cursor: 'not-allowed', opacity: '0.75', boxShadow: SH_LT,
                  }"
                  disabled
                >
                  <span class="dot-pulse-lt" />
                  Accepting…
                </button>
                <button
                  class="action-pair-secondary"
                  :style="{
                    height: '40px', borderRadius: '9999px', fontSize: '14px', fontWeight: '500',
                    background: 'transparent', color: L.inkPrimary, border: `1px solid ${L.borderStrong}`,
                    cursor: 'default',
                  }"
                >Decline</button>
              </div>
            </div>

            <!-- Disabled -->
            <div class="space-y-2">
              <p class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: L.inkTertiary }">Disabled (both)</p>
              <div class="action-pair-stack">
                <button
                  class="action-pair-primary"
                  :style="{
                    height: '40px', borderRadius: '9999px', fontSize: '14px', fontWeight: '500',
                    background: L.accents.lavender.bold, color: L.inkInverse,
                    border: 'none', cursor: 'not-allowed', opacity: '0.4', boxShadow: 'none',
                  }"
                  disabled
                >Accept</button>
                <button
                  class="action-pair-secondary"
                  :style="{
                    height: '40px', borderRadius: '9999px', fontSize: '14px', fontWeight: '500',
                    background: 'transparent', color: L.inkPrimary, border: `1px solid ${L.borderStrong}`,
                    cursor: 'not-allowed', opacity: '0.4',
                  }"
                  disabled
                >Decline</button>
              </div>
            </div>
          </div><!-- /light -->

          <!-- DARK PANEL -->
          <div class="rounded-2xl border p-6 space-y-8"
               :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest"
               :style="{ color: D.inkTertiary }">Dark mode</p>

            <!-- Affirmative -->
            <div class="space-y-2">
              <p class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: D.inkTertiary }">Affirmative</p>
              <div class="action-pair-stack">
                <button
                  class="action-pair-primary"
                  :style="{
                    height: '40px', borderRadius: '9999px', fontSize: '14px', fontWeight: '500',
                    background: D.accents.lavender.bold, color: D.inkInverse,
                    border: 'none', cursor: 'pointer',
                    boxShadow: hov['dc-aff-pri'] ? '0 2px 8px rgba(182,168,230,0.30)' : SH_DK,
                    transform: hov['dc-aff-pri'] ? 'translateY(-1px)' : 'none',
                    transition: 'box-shadow 150ms, transform 150ms',
                  }"
                  @mouseenter="onEnter('dc-aff-pri')" @mouseleave="onLeave('dc-aff-pri')"
                >Accept</button>
                <button
                  class="action-pair-secondary"
                  :style="{
                    height: '40px', borderRadius: '9999px', fontSize: '14px', fontWeight: '500',
                    background: hov['dc-aff-sec'] ? D.surfaceSunken : 'transparent',
                    color: D.inkPrimary, border: `1px solid ${D.borderStrong}`,
                    cursor: 'pointer', transition: 'background 150ms',
                  }"
                  @mouseenter="onEnter('dc-aff-sec')" @mouseleave="onLeave('dc-aff-sec')"
                >Decline</button>
              </div>
            </div>

            <!-- Destructive -->
            <div class="space-y-2">
              <p class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: D.inkTertiary }">Destructive</p>
              <div class="action-pair-stack">
                <button
                  class="action-pair-primary flex items-center justify-center gap-1.5"
                  :style="{
                    height: '40px', borderRadius: '9999px', fontSize: '14px', fontWeight: '500',
                    background: D.status.failed, color: D.inkInverse,
                    border: 'none', cursor: 'pointer',
                    boxShadow: hov['dc-des-pri'] ? '0 2px 8px rgba(230,112,112,0.30)' : SH_DK,
                    transform: hov['dc-des-pri'] ? 'translateY(-1px)' : 'none',
                    transition: 'box-shadow 150ms, transform 150ms',
                  }"
                  @mouseenter="onEnter('dc-des-pri')" @mouseleave="onLeave('dc-des-pri')"
                >
                  <TrashIcon class="w-4 h-4" />
                  Delete
                </button>
                <button
                  class="action-pair-secondary"
                  :style="{
                    height: '40px', borderRadius: '9999px', fontSize: '14px', fontWeight: '500',
                    background: hov['dc-des-sec'] ? D.surfaceSunken : 'transparent',
                    color: D.inkPrimary, border: `1px solid ${D.borderStrong}`,
                    cursor: 'pointer', transition: 'background 150ms',
                  }"
                  @mouseenter="onEnter('dc-des-sec')" @mouseleave="onLeave('dc-des-sec')"
                >Keep</button>
              </div>
            </div>

            <!-- Inside a list row -->
            <div class="space-y-2">
              <p class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: D.inkTertiary }">Inside a list row (invitation card)</p>
              <div class="rounded-2xl border p-4 space-y-4"
                   :style="{ background: D.surfaceRaised, borderColor: D.borderSubtle, boxShadow: SH_DK }">
                <div>
                  <p class="text-[14px] font-medium" :style="{ color: D.inkPrimary }">New invitation from Mom</p>
                  <p class="text-[12px] mt-0.5" :style="{ color: D.inkSecondary }">Family movie night · Sat Apr 19 at 7 pm</p>
                </div>
                <div class="action-pair-stack">
                  <button
                    class="action-pair-primary"
                    :style="{
                      height: '38px', borderRadius: '9999px', fontSize: '13px', fontWeight: '500',
                      background: D.accents.lavender.bold, color: D.inkInverse,
                      border: 'none', cursor: 'pointer', boxShadow: SH_DK,
                    }"
                  >Accept</button>
                  <button
                    class="action-pair-secondary"
                    :style="{
                      height: '38px', borderRadius: '9999px', fontSize: '13px', fontWeight: '500',
                      background: 'transparent', color: D.inkPrimary, border: `1px solid ${D.borderStrong}`,
                      cursor: 'pointer',
                    }"
                  >Decline</button>
                </div>
              </div>
            </div>

            <!-- Loading -->
            <div class="space-y-2">
              <p class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: D.inkTertiary }">Loading (primary)</p>
              <div class="action-pair-stack">
                <button
                  class="action-pair-primary flex items-center justify-center gap-2"
                  :style="{
                    height: '40px', borderRadius: '9999px', fontSize: '14px', fontWeight: '500',
                    background: D.accents.lavender.bold, color: D.inkInverse,
                    border: 'none', cursor: 'not-allowed', opacity: '0.75', boxShadow: SH_DK,
                  }"
                  disabled
                >
                  <span class="dot-pulse-dk" />
                  Accepting…
                </button>
                <button
                  class="action-pair-secondary"
                  :style="{
                    height: '40px', borderRadius: '9999px', fontSize: '14px', fontWeight: '500',
                    background: 'transparent', color: D.inkPrimary, border: `1px solid ${D.borderStrong}`,
                    cursor: 'default',
                  }"
                >Decline</button>
              </div>
            </div>

            <!-- Disabled -->
            <div class="space-y-2">
              <p class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: D.inkTertiary }">Disabled (both)</p>
              <div class="action-pair-stack">
                <button
                  class="action-pair-primary"
                  :style="{
                    height: '40px', borderRadius: '9999px', fontSize: '14px', fontWeight: '500',
                    background: D.accents.lavender.bold, color: D.inkInverse,
                    border: 'none', cursor: 'not-allowed', opacity: '0.4', boxShadow: 'none',
                  }"
                  disabled
                >Accept</button>
                <button
                  class="action-pair-secondary"
                  :style="{
                    height: '40px', borderRadius: '9999px', fontSize: '14px', fontWeight: '500',
                    background: 'transparent', color: D.inkPrimary, border: `1px solid ${D.borderStrong}`,
                    cursor: 'not-allowed', opacity: '0.4',
                  }"
                  disabled
                >Decline</button>
              </div>
            </div>
          </div><!-- /dark -->
        </div>
      </VariantFrame>
      <p class="mt-3 text-sm px-1" :style="{ color: L.inkSecondary }">
        Stacked-then-inline is the only variant that handles cards which need to be touch-friendly at 320px without tiny tap targets — ideal for invitation cards, vault share requests, and anything surfaced as a notification-style card on mobile.
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
          <h2 class="text-[15px] font-semibold" :style="{ color: L.accents.lavender.bold }">LOCKED — Variant A primary, Variant B as prop alternative</h2>
        </div>
        <p class="text-[14px] leading-relaxed" :style="{ color: L.inkPrimary }">
          <strong>Variant A (equal-width outline + filled) is the default.</strong> Every decision gets the same surface area, signalling both choices matter while still guiding the eye rightward to the filled primary. Used for the majority of contexts: rewards, tasks, vault approvals, invitations, confirmations.
        </p>
        <p class="text-[14px] leading-relaxed" :style="{ color: L.inkPrimary }">
          <strong>Variant B (asymmetric ghost + filled) is available as a prop variant</strong> — likely <code>variant="confident"</code> or similar — for contexts where the secondary is genuinely low-stakes (Skip, Later, Not now, Maybe) and the interface should feel confident rather than balanced. Consumers pass the variant prop explicitly; the component defaults to A.
        </p>
        <p class="text-[14px] leading-relaxed" :style="{ color: L.inkPrimary }">
          Variant C (responsive stacked-then-inline) is not part of the shipped component — it's a layout concern the parent container handles by adjusting width/stacking, not the ActionPair itself.
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
            <p class="text-[13px] font-semibold mb-1" :style="{ color: L.inkPrimary }">Variant A — Equal-width outline + filled (primary default)</p>
            <ul class="space-y-1 text-[13px] leading-relaxed list-disc list-inside" :style="{ color: L.inkSecondary }">
              <li>The default component shape. Use when both outcomes have real weight: Decline / Accept, Keep / Delete, Ignore / Approve.</li>
              <li>Default token for list rows on desktop where both buttons sit end-aligned inside a horizontal row.</li>
              <li>Keep button labels short (1 word preferred) so the 50/50 split stays tidy at small widths.</li>
            </ul>
          </div>

          <div>
            <p class="text-[13px] font-semibold mb-1" :style="{ color: L.inkPrimary }">Variant B — Asymmetric ghost + filled (available via prop)</p>
            <ul class="space-y-1 text-[13px] leading-relaxed list-disc list-inside" :style="{ color: L.inkSecondary }">
              <li>Passed explicitly as <code>variant="confident"</code> (or similar prop name — TBD during extraction).</li>
              <li>Use when the secondary is a soft escape: Skip, Later, Not now, Maybe.</li>
              <li>Good for kudos prompts, onboarding nudges, and lightweight confirmations where you want the user to feel propelled forward.</li>
              <li>Avoid for destructive pairs — the ghost label must never be the only safe-out in a high-stakes delete flow.</li>
            </ul>
          </div>

          <div>
            <p class="text-[13px] font-semibold mb-1" :style="{ color: L.inkTertiary }">Variant C — Responsive stacked-then-inline (not shipped)</p>
            <ul class="space-y-1 text-[13px] leading-relaxed list-disc list-inside" :style="{ color: L.inkTertiary }">
              <li>Kept in the library as a reference for responsive stacking behaviour.</li>
              <li>Not part of the ActionPair component itself — stacking is a parent-container concern (the card or row that wraps the ActionPair decides whether to stack on narrow viewports).</li>
            </ul>
          </div>

          <div class="rounded-xl p-4 space-y-1"
               :style="{ background: L.surfaceSunken }">
            <p class="text-[13px] font-semibold" :style="{ color: L.inkPrimary }">Hard rules (all variants)</p>
            <ul class="space-y-1 text-[13px] leading-relaxed list-disc list-inside" :style="{ color: L.inkSecondary }">
              <li>Gap between buttons: 8 px (compact rows) or 12 px (standalone). Never wider — they are one decision.</li>
              <li>Secondary is always on the left; primary always on the right (or top when stacked).</li>
              <li>Never use two filled buttons. Never use two ghost buttons in a destructive context.</li>
              <li>Destructive primary must use <code class="text-[12px] px-1 rounded" :style="{ background: L.borderSubtle }">status.failed</code>, not the lavender accent.</li>
            </ul>
          </div>
        </div>
      </div>
    </section>

    <!-- ══════════════════════════════════════════════════════════════════════
         KIN COMPONENT PREVIEW — Variant B (asymmetric ghost + filled, 1:2)
         ═══════════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="Kin" caption="KinActionPair — layout='asymmetric' gives secondary 1/3 (ghost) + primary 2/3 (filled). layout='equal' (default) is 50/50.">
        <div class="w-full space-y-10">

          <!-- LIGHT PANEL -->
          <div class="rounded-2xl border p-6 space-y-6"
               :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>

            <div class="space-y-2">
              <p class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: L.inkTertiary }">Asymmetric · affirmative</p>
              <KinActionPair layout="asymmetric">
                <template #secondary><KinButton variant="ghost">Skip</KinButton></template>
                <template #primary><KinButton variant="primary">Send Kudos</KinButton></template>
              </KinActionPair>
            </div>

            <div class="space-y-2">
              <p class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: L.inkTertiary }">Asymmetric · destructive</p>
              <KinActionPair layout="asymmetric">
                <template #secondary><KinButton variant="ghost">Keep</KinButton></template>
                <template #primary>
                  <KinButton variant="danger">
                    <template #leading><TrashIcon class="w-4 h-4" /></template>
                    Delete Forever
                  </KinButton>
                </template>
              </KinActionPair>
            </div>

            <div class="space-y-2">
              <p class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: L.inkTertiary }">Equal (50/50)</p>
              <KinActionPair layout="equal">
                <template #secondary><KinButton variant="secondary">Cancel</KinButton></template>
                <template #primary><KinButton variant="primary">Save</KinButton></template>
              </KinActionPair>
            </div>
          </div>

          <!-- DARK PANEL -->
          <div class="dark rounded-2xl border p-6 space-y-6"
               :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

            <div class="space-y-2">
              <p class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: D.inkTertiary }">Asymmetric · affirmative</p>
              <KinActionPair layout="asymmetric">
                <template #secondary><KinButton variant="ghost">Skip</KinButton></template>
                <template #primary><KinButton variant="primary">Send Kudos</KinButton></template>
              </KinActionPair>
            </div>

            <div class="space-y-2">
              <p class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: D.inkTertiary }">Asymmetric · destructive</p>
              <KinActionPair layout="asymmetric">
                <template #secondary><KinButton variant="ghost">Keep</KinButton></template>
                <template #primary>
                  <KinButton variant="danger">
                    <template #leading><TrashIcon class="w-4 h-4" /></template>
                    Delete Forever
                  </KinButton>
                </template>
              </KinActionPair>
            </div>

            <div class="space-y-2">
              <p class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: D.inkTertiary }">Equal (50/50)</p>
              <KinActionPair layout="equal">
                <template #secondary><KinButton variant="secondary">Cancel</KinButton></template>
                <template #primary><KinButton variant="primary">Save</KinButton></template>
              </KinActionPair>
            </div>
          </div>

        </div>
      </VariantFrame>
    </section>

  </ComponentPage>
</template>

<style scoped>
/* ── Dot-pulse loading indicator ─────────────────────────────────────────── */
.dot-pulse-lt,
.dot-pulse-dk {
  display: inline-block;
  width: 6px;
  height: 6px;
  border-radius: 50%;
  flex-shrink: 0;
  animation: dot-pulse 1.2s ease-in-out infinite;
}

.dot-pulse-lt { background: rgba(250, 248, 245, 0.85); }
.dot-pulse-dk { background: rgba(28, 28, 30, 0.85); }

@keyframes dot-pulse {
  0%, 100% { opacity: 1; transform: scale(1); }
  50%       { opacity: 0.35; transform: scale(0.7); }
}

/* ── Variant C responsive layout ────────────────────────────────────────── */
/* Mobile-first: stacked column, filled (primary) on top */
.action-pair-stack {
  display: flex;
  flex-direction: column;
  gap: 10px;
  width: 100%;
}

.action-pair-primary  { order: 1; width: 100%; }
.action-pair-secondary { order: 2; width: 100%; }

/* At 480 px and above: side-by-side equal-width */
@media (min-width: 480px) {
  .action-pair-stack {
    flex-direction: row;
    gap: 8px;
  }

  .action-pair-primary  { order: 2; flex: 1; width: auto; }
  .action-pair-secondary { order: 1; flex: 1; width: auto; }
}
</style>
