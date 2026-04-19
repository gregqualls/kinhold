<script setup>
import { ref } from 'vue'
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import { SparklesIcon, XMarkIcon } from '@heroicons/vue/24/outline'

// ── Palette ──────────────────────────────────────────────────────────────────
const L = {
  surfaceApp:    '#FAF8F5',
  surfaceRaised: '#FFFFFF',
  surfaceSunken: '#F5F2EE',
  surfaceOverlay: '#FFFFFF',
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
  status: { success: '#4D8C6A', pending: '#486E9C', failed: '#BA4A4A' },
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
  status: { success: '#6CC498', pending: '#78A4DC', failed: '#E67070' },
}

const SHADOW_MODAL_LT = '0 20px 60px rgba(28,20,10,0.18), 0 4px 12px rgba(28,20,10,0.10)'
const SHADOW_MODAL_DK = '0 20px 60px rgba(0,0,0,0.60), 0 4px 12px rgba(0,0,0,0.40)'
</script>

<template>
  <ComponentPage
    title="4.7 Modal · Sheet · Drawer"
    description="The canonical overlay container. Locks one direction per breakpoint to eliminate the existing multi-direction inconsistency across the app. Arguably the most consequential pattern decision in Tier 4."
    status="chosen"
  >

    <!-- ══════════════════════════════════════════════════════════════════════
         VARIANT A — Responsive: bottom-sheet mobile / centered modal desktop
         ═══════════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame
        label="A"
        caption="Responsive — bottom-sheet on mobile (<768 px), centered modal on desktop. One rule, two expressions."
      >
        <div class="w-full space-y-10">

          <!-- LIGHT PANEL -->
          <div class="rounded-2xl border p-6 space-y-8"
               :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>

            <!-- Two sub-panels side by side: mobile + desktop -->
            <div class="flex flex-col md:flex-row gap-8 items-start">

              <!-- Mobile mock: 375 px phone frame showing bottom sheet -->
              <div class="flex-1 space-y-2">
                <p class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: L.inkTertiary }">Mobile — 375 px · Bottom sheet</p>
                <!-- phone frame -->
                <div class="relative mx-auto overflow-hidden"
                     style="width: 260px; height: 400px; border-radius: 24px; background: #E8E4DF; border: 2px solid #BCB8B2;">
                  <!-- mock page content behind -->
                  <div class="absolute inset-0 p-4" :style="{ background: L.surfaceApp }">
                    <div class="h-4 rounded-full mb-3 w-3/4" :style="{ background: L.borderSubtle }"></div>
                    <div class="h-3 rounded-full mb-2 w-full" :style="{ background: L.borderSubtle }"></div>
                    <div class="h-3 rounded-full mb-2 w-5/6" :style="{ background: L.borderSubtle }"></div>
                    <div class="h-3 rounded-full mb-4 w-2/3" :style="{ background: L.borderSubtle }"></div>
                    <div class="h-20 rounded-xl mb-3" :style="{ background: L.surfaceRaised, border: `1px solid ${L.borderSubtle}` }"></div>
                    <div class="h-20 rounded-xl opacity-60" :style="{ background: L.surfaceRaised, border: `1px solid ${L.borderSubtle}` }"></div>
                  </div>

                  <!-- Stacking indicator: second sheet slightly behind -->
                  <div class="absolute left-0 right-0 bottom-0" style="height: 200px;">
                    <div class="absolute left-2 right-2 bottom-0 opacity-40"
                         style="height: 184px; border-radius: 28px 28px 0 0; background: #FFFFFF; box-shadow: 0 -4px 16px rgba(28,20,10,0.10);">
                    </div>
                    <!-- Active sheet -->
                    <div class="absolute left-0 right-0 bottom-0"
                         style="height: 192px; border-radius: 28px 28px 0 0;"
                         :style="{ background: L.surfaceOverlay, boxShadow: SHADOW_MODAL_LT }">
                      <!-- grab handle -->
                      <div class="mx-auto mt-3" style="width: 36px; height: 4px; border-radius: 9999px;"
                           :style="{ background: L.borderStrong }"></div>
                      <!-- content -->
                      <div class="px-4 pt-3 pb-3">
                        <p class="text-[13px] font-semibold mb-1" :style="{ color: L.inkPrimary }">Share vault entry</p>
                        <p class="text-[11px] mb-3" :style="{ color: L.inkSecondary }">Choose a family member to share with.</p>
                        <!-- mini form field -->
                        <div class="rounded-xl border mb-3 px-2 py-1.5"
                             :style="{ background: L.surfaceSunken, borderColor: L.borderSubtle }">
                          <p class="text-[10px]" :style="{ color: L.inkTertiary }">Recipient</p>
                          <p class="text-[11px] font-medium" :style="{ color: L.inkSecondary }">Select family member…</p>
                        </div>
                        <!-- action pair -->
                        <div class="flex gap-2">
                          <button class="flex-1 text-[11px] font-medium"
                                  style="height: 32px; border-radius: 9999px;"
                                  :style="{ background: 'transparent', color: L.inkPrimary, border: `1px solid ${L.borderStrong}` }">
                            Cancel
                          </button>
                          <button class="flex-1 text-[11px] font-medium"
                                  style="height: 32px; border-radius: 9999px;"
                                  :style="{ background: L.accents.lavender.bold, color: L.inkInverse, border: 'none' }">
                            Share
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- backdrop overlay -->
                  <div class="absolute inset-0" style="background: rgba(0,0,0,0.48); pointer-events: none; border-radius: 22px;"></div>
                  <!-- re-punch sheet through backdrop — rendered after backdrop so it's on top -->
                  <div class="absolute left-0 right-0 bottom-0" style="height: 192px; border-radius: 28px 28px 0 0; pointer-events: none;"
                       :style="{ background: L.surfaceOverlay }">
                    <div class="mx-auto mt-3" style="width: 36px; height: 4px; border-radius: 9999px;"
                         :style="{ background: L.borderStrong }"></div>
                    <div class="px-4 pt-3 pb-3">
                      <p class="text-[13px] font-semibold mb-1" :style="{ color: L.inkPrimary }">Share vault entry</p>
                      <p class="text-[11px] mb-3" :style="{ color: L.inkSecondary }">Choose a family member to share with.</p>
                      <div class="rounded-xl border mb-3 px-2 py-1.5"
                           :style="{ background: L.surfaceSunken, borderColor: L.borderSubtle }">
                        <p class="text-[10px]" :style="{ color: L.inkTertiary }">Recipient</p>
                        <p class="text-[11px] font-medium" :style="{ color: L.inkSecondary }">Select family member…</p>
                      </div>
                      <div class="flex gap-2">
                        <button class="flex-1 text-[11px] font-medium"
                                style="height: 32px; border-radius: 9999px;"
                                :style="{ background: 'transparent', color: L.inkPrimary, border: `1px solid ${L.borderStrong}` }">
                          Cancel
                        </button>
                        <button class="flex-1 text-[11px] font-medium"
                                style="height: 32px; border-radius: 9999px;"
                                :style="{ background: L.accents.lavender.bold, color: L.inkInverse, border: 'none' }">
                          Share
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Desktop mock: 1024 px frame showing centered modal -->
              <div class="flex-[2] space-y-2">
                <p class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: L.inkTertiary }">Desktop — 1024 px · Centered modal</p>
                <!-- desktop frame -->
                <div class="relative overflow-hidden"
                     style="height: 340px; border-radius: 16px; border: 2px solid #BCB8B2;"
                     :style="{ background: L.surfaceApp }">
                  <!-- mock page content -->
                  <div class="absolute inset-0 p-5">
                    <div class="flex gap-3 mb-4">
                      <div class="h-3 rounded-full w-48" :style="{ background: L.borderSubtle }"></div>
                      <div class="h-3 rounded-full w-24 ml-auto" :style="{ background: L.borderSubtle }"></div>
                    </div>
                    <div class="grid grid-cols-3 gap-3">
                      <div class="h-24 rounded-2xl" :style="{ background: L.surfaceRaised, border: `1px solid ${L.borderSubtle}` }"></div>
                      <div class="h-24 rounded-2xl opacity-70" :style="{ background: L.surfaceRaised, border: `1px solid ${L.borderSubtle}` }"></div>
                      <div class="h-24 rounded-2xl opacity-40" :style="{ background: L.surfaceRaised, border: `1px solid ${L.borderSubtle}` }"></div>
                    </div>
                  </div>
                  <!-- backdrop -->
                  <div class="absolute inset-0" style="background: rgba(0,0,0,0.48); border-radius: 14px;"></div>
                  <!-- centered modal surface -->
                  <div class="absolute inset-x-0 top-1/2 mx-auto"
                       style="width: 360px; transform: translateY(-50%); border-radius: 28px; left: 50%; margin-left: -180px;"
                       :style="{ background: L.surfaceOverlay, boxShadow: SHADOW_MODAL_LT }">
                    <!-- header row -->
                    <div class="flex items-center justify-between px-5 pt-5 pb-3"
                         :style="{ borderBottom: `1px solid ${L.borderSubtle}` }">
                      <p class="text-[14px] font-semibold" :style="{ color: L.inkPrimary }">Share vault entry</p>
                      <button style="width: 28px; height: 28px; border-radius: 9999px; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center;"
                              :style="{ background: L.surfaceSunken, color: L.inkSecondary }">
                        <XMarkIcon style="width: 14px; height: 14px;" />
                      </button>
                    </div>
                    <!-- body -->
                    <div class="px-5 py-4 space-y-3">
                      <p class="text-[12px]" :style="{ color: L.inkSecondary }">
                        Choose which family member can view this entry. They will receive a notification.
                      </p>
                      <!-- input -->
                      <div class="rounded-xl border px-3 py-2"
                           :style="{ background: L.surfaceSunken, borderColor: L.borderSubtle }">
                        <p class="text-[10px] mb-0.5" :style="{ color: L.inkTertiary }">Name or entry title</p>
                        <p class="text-[12px]" :style="{ color: L.inkSecondary }">Health Insurance Card</p>
                      </div>
                      <!-- select -->
                      <div class="rounded-xl border px-3 py-2"
                           :style="{ background: L.surfaceSunken, borderColor: L.borderSubtle }">
                        <p class="text-[10px] mb-0.5" :style="{ color: L.inkTertiary }">Recipient</p>
                        <p class="text-[12px]" :style="{ color: L.inkSecondary }">Select family member…</p>
                      </div>
                    </div>
                    <!-- footer action pair -->
                    <div class="flex gap-2 px-5 pb-5">
                      <button class="flex-1 text-[12px] font-medium"
                              style="height: 36px; border-radius: 9999px;"
                              :style="{ background: 'transparent', color: L.inkPrimary, border: `1px solid ${L.borderStrong}` }">
                        Cancel
                      </button>
                      <button class="flex-1 text-[12px] font-medium"
                              style="height: 36px; border-radius: 9999px;"
                              :style="{ background: L.accents.lavender.bold, color: L.inkInverse, border: 'none', boxShadow: SHADOW_MODAL_LT }">
                        Share entry
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div><!-- /light -->

          <!-- DARK PANEL -->
          <div class="rounded-2xl border p-6 space-y-8"
               :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

            <div class="flex flex-col md:flex-row gap-8 items-start">

              <!-- Mobile mock dark -->
              <div class="flex-1 space-y-2">
                <p class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: D.inkTertiary }">Mobile — 375 px · Bottom sheet</p>
                <div class="relative mx-auto overflow-hidden"
                     style="width: 260px; height: 400px; border-radius: 24px;"
                     :style="{ background: D.surfaceApp, border: `2px solid ${D.borderStrong}` }">
                  <!-- page content -->
                  <div class="absolute inset-0 p-4">
                    <div class="h-4 rounded-full mb-3 w-3/4" :style="{ background: D.borderSubtle }"></div>
                    <div class="h-3 rounded-full mb-2 w-full" :style="{ background: D.borderSubtle }"></div>
                    <div class="h-3 rounded-full mb-2 w-5/6" :style="{ background: D.borderSubtle }"></div>
                    <div class="h-3 rounded-full mb-4 w-2/3" :style="{ background: D.borderSubtle }"></div>
                    <div class="h-20 rounded-xl mb-3" :style="{ background: D.surfaceRaised, border: `1px solid ${D.borderSubtle}` }"></div>
                    <div class="h-20 rounded-xl opacity-60" :style="{ background: D.surfaceRaised, border: `1px solid ${D.borderSubtle}` }"></div>
                  </div>
                  <!-- stacking indicator -->
                  <div class="absolute left-2 right-2 bottom-0 opacity-40"
                       style="height: 184px; border-radius: 28px 28px 0 0;"
                       :style="{ background: D.surfaceOverlay }"></div>
                  <!-- backdrop -->
                  <div class="absolute inset-0" style="background: rgba(0,0,0,0.70); pointer-events: none; border-radius: 22px;"></div>
                  <!-- active sheet -->
                  <div class="absolute left-0 right-0 bottom-0" style="height: 192px; border-radius: 28px 28px 0 0;"
                       :style="{ background: D.surfaceOverlay, boxShadow: SHADOW_MODAL_DK }">
                    <div class="mx-auto mt-3" style="width: 36px; height: 4px; border-radius: 9999px;"
                         :style="{ background: D.borderStrong }"></div>
                    <div class="px-4 pt-3 pb-3">
                      <p class="text-[13px] font-semibold mb-1" :style="{ color: D.inkPrimary }">Share vault entry</p>
                      <p class="text-[11px] mb-3" :style="{ color: D.inkSecondary }">Choose a family member to share with.</p>
                      <div class="rounded-xl border mb-3 px-2 py-1.5"
                           :style="{ background: D.surfaceSunken, borderColor: D.borderSubtle }">
                        <p class="text-[10px]" :style="{ color: D.inkTertiary }">Recipient</p>
                        <p class="text-[11px] font-medium" :style="{ color: D.inkSecondary }">Select family member…</p>
                      </div>
                      <div class="flex gap-2">
                        <button class="flex-1 text-[11px] font-medium"
                                style="height: 32px; border-radius: 9999px;"
                                :style="{ background: 'transparent', color: D.inkPrimary, border: `1px solid ${D.borderStrong}` }">
                          Cancel
                        </button>
                        <button class="flex-1 text-[11px] font-medium"
                                style="height: 32px; border-radius: 9999px;"
                                :style="{ background: D.accents.lavender.bold, color: D.inkInverse, border: 'none' }">
                          Share
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Desktop mock dark -->
              <div class="flex-[2] space-y-2">
                <p class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: D.inkTertiary }">Desktop — 1024 px · Centered modal</p>
                <div class="relative overflow-hidden"
                     style="height: 340px; border-radius: 16px;"
                     :style="{ background: D.surfaceApp, border: `2px solid ${D.borderStrong}` }">
                  <!-- page bg grid -->
                  <div class="absolute inset-0 p-5">
                    <div class="flex gap-3 mb-4">
                      <div class="h-3 rounded-full w-48" :style="{ background: D.borderSubtle }"></div>
                      <div class="h-3 rounded-full w-24 ml-auto" :style="{ background: D.borderSubtle }"></div>
                    </div>
                    <div class="grid grid-cols-3 gap-3">
                      <div class="h-24 rounded-2xl" :style="{ background: D.surfaceRaised, border: `1px solid ${D.borderSubtle}` }"></div>
                      <div class="h-24 rounded-2xl opacity-70" :style="{ background: D.surfaceRaised, border: `1px solid ${D.borderSubtle}` }"></div>
                      <div class="h-24 rounded-2xl opacity-40" :style="{ background: D.surfaceRaised, border: `1px solid ${D.borderSubtle}` }"></div>
                    </div>
                  </div>
                  <!-- backdrop -->
                  <div class="absolute inset-0" style="background: rgba(0,0,0,0.70); border-radius: 14px;"></div>
                  <!-- centered modal -->
                  <div class="absolute inset-x-0 top-1/2 mx-auto"
                       style="width: 360px; transform: translateY(-50%); border-radius: 28px; left: 50%; margin-left: -180px;"
                       :style="{ background: D.surfaceOverlay, boxShadow: SHADOW_MODAL_DK }">
                    <div class="flex items-center justify-between px-5 pt-5 pb-3"
                         :style="{ borderBottom: `1px solid ${D.borderSubtle}` }">
                      <p class="text-[14px] font-semibold" :style="{ color: D.inkPrimary }">Share vault entry</p>
                      <button style="width: 28px; height: 28px; border-radius: 9999px; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center;"
                              :style="{ background: D.surfaceSunken, color: D.inkSecondary }">
                        <XMarkIcon style="width: 14px; height: 14px;" />
                      </button>
                    </div>
                    <div class="px-5 py-4 space-y-3">
                      <p class="text-[12px]" :style="{ color: D.inkSecondary }">
                        Choose which family member can view this entry. They will receive a notification.
                      </p>
                      <div class="rounded-xl border px-3 py-2"
                           :style="{ background: D.surfaceSunken, borderColor: D.borderSubtle }">
                        <p class="text-[10px] mb-0.5" :style="{ color: D.inkTertiary }">Name or entry title</p>
                        <p class="text-[12px]" :style="{ color: D.inkSecondary }">Health Insurance Card</p>
                      </div>
                      <div class="rounded-xl border px-3 py-2"
                           :style="{ background: D.surfaceSunken, borderColor: D.borderSubtle }">
                        <p class="text-[10px] mb-0.5" :style="{ color: D.inkTertiary }">Recipient</p>
                        <p class="text-[12px]" :style="{ color: D.inkSecondary }">Select family member…</p>
                      </div>
                    </div>
                    <div class="flex gap-2 px-5 pb-5">
                      <button class="flex-1 text-[12px] font-medium"
                              style="height: 36px; border-radius: 9999px;"
                              :style="{ background: 'transparent', color: D.inkPrimary, border: `1px solid ${D.borderStrong}` }">
                        Cancel
                      </button>
                      <button class="flex-1 text-[12px] font-medium"
                              style="height: 36px; border-radius: 9999px;"
                              :style="{ background: D.accents.lavender.bold, color: D.inkInverse, border: 'none' }">
                        Share entry
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div><!-- /dark -->
        </div>
      </VariantFrame>
      <p class="mt-3 text-sm px-1" :style="{ color: L.inkSecondary }">
        One component, one decision: phone users get the thumb-friendly bottom sheet; desktop users get the attention-focused centered dialog. The responsive shift is a single CSS breakpoint — no conditional rendering needed.
      </p>
    </section>


    <!-- ══════════════════════════════════════════════════════════════════════
         VARIANT B — Always bottom sheet (even on desktop, max-width ~640 px)
         ═══════════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="B" caption="Always bottom-sheet — anchored to viewport bottom at all sizes, max-width 640 px, centered horizontally on desktop.">
        <div class="w-full space-y-10">

          <!-- LIGHT PANEL -->
          <div class="rounded-2xl border p-6 space-y-8"
               :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>

            <div class="flex flex-col md:flex-row gap-8 items-start">

              <!-- Mobile mock -->
              <div class="flex-1 space-y-2">
                <p class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: L.inkTertiary }">Mobile — full-width sheet</p>
                <div class="relative mx-auto overflow-hidden"
                     style="width: 260px; height: 400px; border-radius: 24px; border: 2px solid #BCB8B2;"
                     :style="{ background: L.surfaceApp }">
                  <div class="absolute inset-0 p-4">
                    <div class="h-4 rounded-full mb-3 w-3/4" :style="{ background: L.borderSubtle }"></div>
                    <div class="h-3 rounded-full mb-2 w-full" :style="{ background: L.borderSubtle }"></div>
                    <div class="h-3 rounded-full mb-4 w-5/6" :style="{ background: L.borderSubtle }"></div>
                    <div class="h-20 rounded-xl mb-3" :style="{ background: L.surfaceRaised, border: `1px solid ${L.borderSubtle}` }"></div>
                    <div class="h-20 rounded-xl opacity-60" :style="{ background: L.surfaceRaised, border: `1px solid ${L.borderSubtle}` }"></div>
                  </div>
                  <!-- stacking indicator -->
                  <div class="absolute left-2 right-2 bottom-0 opacity-40"
                       style="height: 184px; border-radius: 28px 28px 0 0;"
                       :style="{ background: L.surfaceOverlay }"></div>
                  <!-- backdrop -->
                  <div class="absolute inset-0" style="background: rgba(0,0,0,0.48); border-radius: 22px;"></div>
                  <!-- sheet -->
                  <div class="absolute left-0 right-0 bottom-0" style="height: 192px; border-radius: 28px 28px 0 0;"
                       :style="{ background: L.surfaceOverlay, boxShadow: SHADOW_MODAL_LT }">
                    <div class="mx-auto mt-3" style="width: 36px; height: 4px; border-radius: 9999px;"
                         :style="{ background: L.borderStrong }"></div>
                    <div class="px-4 pt-3 pb-3">
                      <p class="text-[13px] font-semibold mb-1" :style="{ color: L.inkPrimary }">Share vault entry</p>
                      <p class="text-[11px] mb-3" :style="{ color: L.inkSecondary }">Choose a family member to share with.</p>
                      <div class="rounded-xl border mb-3 px-2 py-1.5"
                           :style="{ background: L.surfaceSunken, borderColor: L.borderSubtle }">
                        <p class="text-[10px]" :style="{ color: L.inkTertiary }">Recipient</p>
                        <p class="text-[11px] font-medium" :style="{ color: L.inkSecondary }">Select family member…</p>
                      </div>
                      <div class="flex gap-2">
                        <button class="flex-1 text-[11px] font-medium"
                                style="height: 32px; border-radius: 9999px;"
                                :style="{ background: 'transparent', color: L.inkPrimary, border: `1px solid ${L.borderStrong}` }">Cancel</button>
                        <button class="flex-1 text-[11px] font-medium"
                                style="height: 32px; border-radius: 9999px;"
                                :style="{ background: L.accents.lavender.bold, color: L.inkInverse, border: 'none' }">Share</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Desktop mock: wider frame, sheet centered at bottom, max-width ~640px -->
              <div class="flex-[2] space-y-2">
                <p class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: L.inkTertiary }">Desktop — max-width 640 px sheet, bottom-anchored</p>
                <div class="relative overflow-hidden"
                     style="height: 340px; border-radius: 16px; border: 2px solid #BCB8B2;"
                     :style="{ background: L.surfaceApp }">
                  <div class="absolute inset-0 p-5">
                    <div class="flex gap-3 mb-4">
                      <div class="h-3 rounded-full w-48" :style="{ background: L.borderSubtle }"></div>
                      <div class="h-3 rounded-full w-24 ml-auto" :style="{ background: L.borderSubtle }"></div>
                    </div>
                    <div class="grid grid-cols-3 gap-3">
                      <div class="h-24 rounded-2xl" :style="{ background: L.surfaceRaised, border: `1px solid ${L.borderSubtle}` }"></div>
                      <div class="h-24 rounded-2xl opacity-70" :style="{ background: L.surfaceRaised, border: `1px solid ${L.borderSubtle}` }"></div>
                      <div class="h-24 rounded-2xl opacity-40" :style="{ background: L.surfaceRaised, border: `1px solid ${L.borderSubtle}` }"></div>
                    </div>
                  </div>
                  <!-- backdrop -->
                  <div class="absolute inset-0" style="background: rgba(0,0,0,0.48); border-radius: 14px;"></div>
                  <!-- stacking indicator: slightly narrower sheet behind -->
                  <div class="absolute bottom-0 opacity-35"
                       style="width: 340px; height: 176px; border-radius: 28px 28px 0 0; left: 50%; transform: translateX(-50%);"
                       :style="{ background: L.surfaceOverlay }"></div>
                  <!-- active sheet centered, max-w ~360px representation -->
                  <div class="absolute bottom-0"
                       style="width: 360px; height: 188px; border-radius: 28px 28px 0 0; left: 50%; transform: translateX(-50%);"
                       :style="{ background: L.surfaceOverlay, boxShadow: SHADOW_MODAL_LT }">
                    <div class="mx-auto mt-3" style="width: 36px; height: 4px; border-radius: 9999px;"
                         :style="{ background: L.borderStrong }"></div>
                    <div class="px-5 pt-3 pb-3">
                      <p class="text-[13px] font-semibold mb-1" :style="{ color: L.inkPrimary }">Share vault entry</p>
                      <p class="text-[11px] mb-3" :style="{ color: L.inkSecondary }">Choose which family member can view this entry.</p>
                      <div class="rounded-xl border mb-3 px-3 py-1.5"
                           :style="{ background: L.surfaceSunken, borderColor: L.borderSubtle }">
                        <p class="text-[10px]" :style="{ color: L.inkTertiary }">Recipient</p>
                        <p class="text-[11px]" :style="{ color: L.inkSecondary }">Select family member…</p>
                      </div>
                      <div class="flex gap-2">
                        <button class="flex-1 text-[11px] font-medium"
                                style="height: 32px; border-radius: 9999px;"
                                :style="{ background: 'transparent', color: L.inkPrimary, border: `1px solid ${L.borderStrong}` }">Cancel</button>
                        <button class="flex-1 text-[11px] font-medium"
                                style="height: 32px; border-radius: 9999px;"
                                :style="{ background: L.accents.lavender.bold, color: L.inkInverse, border: 'none' }">Share entry</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div><!-- /light -->

          <!-- DARK PANEL -->
          <div class="rounded-2xl border p-6 space-y-8"
               :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

            <div class="flex flex-col md:flex-row gap-8 items-start">
              <!-- Mobile mock dark -->
              <div class="flex-1 space-y-2">
                <p class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: D.inkTertiary }">Mobile — full-width sheet</p>
                <div class="relative mx-auto overflow-hidden"
                     style="width: 260px; height: 400px; border-radius: 24px;"
                     :style="{ background: D.surfaceApp, border: `2px solid ${D.borderStrong}` }">
                  <div class="absolute inset-0 p-4">
                    <div class="h-4 rounded-full mb-3 w-3/4" :style="{ background: D.borderSubtle }"></div>
                    <div class="h-3 rounded-full mb-2 w-full" :style="{ background: D.borderSubtle }"></div>
                    <div class="h-3 rounded-full mb-4 w-5/6" :style="{ background: D.borderSubtle }"></div>
                    <div class="h-20 rounded-xl mb-3" :style="{ background: D.surfaceRaised, border: `1px solid ${D.borderSubtle}` }"></div>
                    <div class="h-20 rounded-xl opacity-60" :style="{ background: D.surfaceRaised, border: `1px solid ${D.borderSubtle}` }"></div>
                  </div>
                  <div class="absolute left-2 right-2 bottom-0 opacity-40"
                       style="height: 184px; border-radius: 28px 28px 0 0;"
                       :style="{ background: D.surfaceOverlay }"></div>
                  <div class="absolute inset-0" style="background: rgba(0,0,0,0.70); border-radius: 22px;"></div>
                  <div class="absolute left-0 right-0 bottom-0" style="height: 192px; border-radius: 28px 28px 0 0;"
                       :style="{ background: D.surfaceOverlay, boxShadow: SHADOW_MODAL_DK }">
                    <div class="mx-auto mt-3" style="width: 36px; height: 4px; border-radius: 9999px;"
                         :style="{ background: D.borderStrong }"></div>
                    <div class="px-4 pt-3 pb-3">
                      <p class="text-[13px] font-semibold mb-1" :style="{ color: D.inkPrimary }">Share vault entry</p>
                      <p class="text-[11px] mb-3" :style="{ color: D.inkSecondary }">Choose a family member to share with.</p>
                      <div class="rounded-xl border mb-3 px-2 py-1.5"
                           :style="{ background: D.surfaceSunken, borderColor: D.borderSubtle }">
                        <p class="text-[10px]" :style="{ color: D.inkTertiary }">Recipient</p>
                        <p class="text-[11px] font-medium" :style="{ color: D.inkSecondary }">Select family member…</p>
                      </div>
                      <div class="flex gap-2">
                        <button class="flex-1 text-[11px] font-medium"
                                style="height: 32px; border-radius: 9999px;"
                                :style="{ background: 'transparent', color: D.inkPrimary, border: `1px solid ${D.borderStrong}` }">Cancel</button>
                        <button class="flex-1 text-[11px] font-medium"
                                style="height: 32px; border-radius: 9999px;"
                                :style="{ background: D.accents.lavender.bold, color: D.inkInverse, border: 'none' }">Share</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Desktop mock dark -->
              <div class="flex-[2] space-y-2">
                <p class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: D.inkTertiary }">Desktop — max-width 640 px sheet, bottom-anchored</p>
                <div class="relative overflow-hidden"
                     style="height: 340px; border-radius: 16px;"
                     :style="{ background: D.surfaceApp, border: `2px solid ${D.borderStrong}` }">
                  <div class="absolute inset-0 p-5">
                    <div class="flex gap-3 mb-4">
                      <div class="h-3 rounded-full w-48" :style="{ background: D.borderSubtle }"></div>
                      <div class="h-3 rounded-full w-24 ml-auto" :style="{ background: D.borderSubtle }"></div>
                    </div>
                    <div class="grid grid-cols-3 gap-3">
                      <div class="h-24 rounded-2xl" :style="{ background: D.surfaceRaised, border: `1px solid ${D.borderSubtle}` }"></div>
                      <div class="h-24 rounded-2xl opacity-70" :style="{ background: D.surfaceRaised, border: `1px solid ${D.borderSubtle}` }"></div>
                      <div class="h-24 rounded-2xl opacity-40" :style="{ background: D.surfaceRaised, border: `1px solid ${D.borderSubtle}` }"></div>
                    </div>
                  </div>
                  <div class="absolute inset-0" style="background: rgba(0,0,0,0.70); border-radius: 14px;"></div>
                  <div class="absolute bottom-0 opacity-35"
                       style="width: 340px; height: 176px; border-radius: 28px 28px 0 0; left: 50%; transform: translateX(-50%);"
                       :style="{ background: D.surfaceOverlay }"></div>
                  <div class="absolute bottom-0"
                       style="width: 360px; height: 188px; border-radius: 28px 28px 0 0; left: 50%; transform: translateX(-50%);"
                       :style="{ background: D.surfaceOverlay, boxShadow: SHADOW_MODAL_DK }">
                    <div class="mx-auto mt-3" style="width: 36px; height: 4px; border-radius: 9999px;"
                         :style="{ background: D.borderStrong }"></div>
                    <div class="px-5 pt-3 pb-3">
                      <p class="text-[13px] font-semibold mb-1" :style="{ color: D.inkPrimary }">Share vault entry</p>
                      <p class="text-[11px] mb-3" :style="{ color: D.inkSecondary }">Choose which family member can view this entry.</p>
                      <div class="rounded-xl border mb-3 px-3 py-1.5"
                           :style="{ background: D.surfaceSunken, borderColor: D.borderSubtle }">
                        <p class="text-[10px]" :style="{ color: D.inkTertiary }">Recipient</p>
                        <p class="text-[11px]" :style="{ color: D.inkSecondary }">Select family member…</p>
                      </div>
                      <div class="flex gap-2">
                        <button class="flex-1 text-[11px] font-medium"
                                style="height: 32px; border-radius: 9999px;"
                                :style="{ background: 'transparent', color: D.inkPrimary, border: `1px solid ${D.borderStrong}` }">Cancel</button>
                        <button class="flex-1 text-[11px] font-medium"
                                style="height: 32px; border-radius: 9999px;"
                                :style="{ background: D.accents.lavender.bold, color: D.inkInverse, border: 'none' }">Share entry</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div><!-- /dark -->
        </div>
      </VariantFrame>
      <p class="mt-3 text-sm px-1" :style="{ color: L.inkSecondary }">
        Consistent muscle memory at the cost of desktop ergonomics. Works well for apps that are phone-first in practice but can become awkward for forms with many fields — the sheet height ceiling forces truncation or scrolling on desktop.
      </p>
    </section>


    <!-- ══════════════════════════════════════════════════════════════════════
         VARIANT C — Always centered modal, backdrop-blur glass treatment
         ═══════════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="C" caption="Always centered modal — backdrop-blur glass backdrop at all sizes. Desktop-native feel.">
        <div class="w-full space-y-10">

          <!-- LIGHT PANEL -->
          <div class="rounded-2xl border p-6 space-y-8"
               :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>

            <div class="flex flex-col md:flex-row gap-8 items-start">

              <!-- Mobile mock: centered modal even on small screen -->
              <div class="flex-1 space-y-2">
                <p class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: L.inkTertiary }">Mobile — 375 px · Centered modal</p>
                <div class="relative mx-auto overflow-hidden"
                     style="width: 260px; height: 400px; border-radius: 24px; border: 2px solid #BCB8B2;"
                     :style="{ background: L.surfaceApp }">
                  <!-- page bg -->
                  <div class="absolute inset-0 p-4">
                    <div class="h-4 rounded-full mb-3 w-3/4" :style="{ background: L.borderSubtle }"></div>
                    <div class="h-3 rounded-full mb-2 w-full" :style="{ background: L.borderSubtle }"></div>
                    <div class="h-3 rounded-full mb-4 w-2/3" :style="{ background: L.borderSubtle }"></div>
                    <div class="h-16 rounded-xl mb-3" :style="{ background: L.surfaceRaised, border: `1px solid ${L.borderSubtle}` }"></div>
                    <div class="h-16 rounded-xl opacity-60" :style="{ background: L.surfaceRaised, border: `1px solid ${L.borderSubtle}` }"></div>
                  </div>
                  <!-- backdrop blur glass -->
                  <div class="absolute inset-0 var-c-backdrop-lt" style="border-radius: 22px;"></div>
                  <!-- stacking indicator: slightly behind modal -->
                  <div class="absolute top-1/2 left-1/2" style="width: 196px; transform: translate(-50%, calc(-50% + 6px)); border-radius: 28px; opacity: 0.45;"
                       :style="{ background: L.surfaceOverlay, height: '196px' }"></div>
                  <!-- active modal -->
                  <div class="absolute top-1/2 left-1/2" style="width: 204px; transform: translate(-50%, -50%); border-radius: 28px;"
                       :style="{ background: L.surfaceOverlay, boxShadow: SHADOW_MODAL_LT }">
                    <!-- close button top-right -->
                    <div class="flex items-center justify-between px-4 pt-4 pb-2"
                         :style="{ borderBottom: `1px solid ${L.borderSubtle}` }">
                      <p class="text-[11px] font-semibold" :style="{ color: L.inkPrimary }">Share vault entry</p>
                      <button style="width: 22px; height: 22px; border-radius: 9999px; border: none; display: flex; align-items: center; justify-content: center;"
                              :style="{ background: L.surfaceSunken, color: L.inkSecondary }">
                        <XMarkIcon style="width: 11px; height: 11px;" />
                      </button>
                    </div>
                    <div class="px-4 py-3">
                      <p class="text-[10px] mb-3" :style="{ color: L.inkSecondary }">Choose a family member to share with.</p>
                      <div class="rounded-xl border mb-3 px-2 py-1.5"
                           :style="{ background: L.surfaceSunken, borderColor: L.borderSubtle }">
                        <p class="text-[9px]" :style="{ color: L.inkTertiary }">Recipient</p>
                        <p class="text-[10px]" :style="{ color: L.inkSecondary }">Select member…</p>
                      </div>
                      <div class="flex gap-1.5">
                        <button class="flex-1 text-[10px] font-medium"
                                style="height: 28px; border-radius: 9999px;"
                                :style="{ background: 'transparent', color: L.inkPrimary, border: `1px solid ${L.borderStrong}` }">Cancel</button>
                        <button class="flex-1 text-[10px] font-medium"
                                style="height: 28px; border-radius: 9999px;"
                                :style="{ background: L.accents.lavender.bold, color: L.inkInverse, border: 'none' }">Share</button>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- note about X reachability -->
                <p class="text-[11px] leading-snug" :style="{ color: L.inkTertiary }">
                  X button requires thumb stretch on mobile — top-right is hard to reach one-handed at 375 px.
                </p>
              </div>

              <!-- Desktop mock: centered glass modal -->
              <div class="flex-[2] space-y-2">
                <p class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: L.inkTertiary }">Desktop — 1024 px · Glass-backdrop centered modal</p>
                <div class="relative overflow-hidden"
                     style="height: 340px; border-radius: 16px; border: 2px solid #BCB8B2;"
                     :style="{ background: L.surfaceApp }">
                  <div class="absolute inset-0 p-5">
                    <div class="flex gap-3 mb-4">
                      <div class="h-3 rounded-full w-48" :style="{ background: L.borderSubtle }"></div>
                      <div class="h-3 rounded-full w-24 ml-auto" :style="{ background: L.borderSubtle }"></div>
                    </div>
                    <div class="grid grid-cols-3 gap-3">
                      <div class="h-24 rounded-2xl" :style="{ background: L.surfaceRaised, border: `1px solid ${L.borderSubtle}` }"></div>
                      <div class="h-24 rounded-2xl opacity-70" :style="{ background: L.surfaceRaised, border: `1px solid ${L.borderSubtle}` }"></div>
                      <div class="h-24 rounded-2xl opacity-40" :style="{ background: L.surfaceRaised, border: `1px solid ${L.borderSubtle}` }"></div>
                    </div>
                  </div>
                  <!-- glass backdrop -->
                  <div class="absolute inset-0 var-c-backdrop-lt" style="border-radius: 14px;"></div>
                  <!-- stacking indicator -->
                  <div class="absolute top-1/2 left-1/2 opacity-40"
                       style="width: 348px; height: 216px; border-radius: 28px; transform: translate(-50%, calc(-50% + 8px));"
                       :style="{ background: L.surfaceOverlay }"></div>
                  <!-- active glass modal -->
                  <div class="absolute top-1/2 left-1/2"
                       style="width: 360px; border-radius: 28px; transform: translate(-50%, -50%);"
                       :style="{ background: L.surfaceOverlay, boxShadow: SHADOW_MODAL_LT }">
                    <div class="flex items-center justify-between px-5 pt-5 pb-3"
                         :style="{ borderBottom: `1px solid ${L.borderSubtle}` }">
                      <p class="text-[14px] font-semibold" :style="{ color: L.inkPrimary }">Share vault entry</p>
                      <button style="width: 28px; height: 28px; border-radius: 9999px; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center;"
                              :style="{ background: L.surfaceSunken, color: L.inkSecondary }">
                        <XMarkIcon style="width: 14px; height: 14px;" />
                      </button>
                    </div>
                    <div class="px-5 py-4 space-y-3">
                      <p class="text-[12px]" :style="{ color: L.inkSecondary }">
                        Choose which family member can view this entry. They will receive a notification.
                      </p>
                      <div class="rounded-xl border px-3 py-2"
                           :style="{ background: L.surfaceSunken, borderColor: L.borderSubtle }">
                        <p class="text-[10px] mb-0.5" :style="{ color: L.inkTertiary }">Name or entry title</p>
                        <p class="text-[12px]" :style="{ color: L.inkSecondary }">Health Insurance Card</p>
                      </div>
                      <div class="rounded-xl border px-3 py-2"
                           :style="{ background: L.surfaceSunken, borderColor: L.borderSubtle }">
                        <p class="text-[10px] mb-0.5" :style="{ color: L.inkTertiary }">Recipient</p>
                        <p class="text-[12px]" :style="{ color: L.inkSecondary }">Select family member…</p>
                      </div>
                    </div>
                    <div class="flex gap-2 px-5 pb-5">
                      <button class="flex-1 text-[12px] font-medium"
                              style="height: 36px; border-radius: 9999px;"
                              :style="{ background: 'transparent', color: L.inkPrimary, border: `1px solid ${L.borderStrong}` }">
                        Cancel
                      </button>
                      <button class="flex-1 text-[12px] font-medium"
                              style="height: 36px; border-radius: 9999px;"
                              :style="{ background: L.accents.lavender.bold, color: L.inkInverse, border: 'none' }">
                        Share entry
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div><!-- /light -->

          <!-- DARK PANEL -->
          <div class="rounded-2xl border p-6 space-y-8"
               :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

            <div class="flex flex-col md:flex-row gap-8 items-start">
              <!-- Mobile mock dark -->
              <div class="flex-1 space-y-2">
                <p class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: D.inkTertiary }">Mobile — 375 px · Centered modal</p>
                <div class="relative mx-auto overflow-hidden"
                     style="width: 260px; height: 400px; border-radius: 24px;"
                     :style="{ background: D.surfaceApp, border: `2px solid ${D.borderStrong}` }">
                  <div class="absolute inset-0 p-4">
                    <div class="h-4 rounded-full mb-3 w-3/4" :style="{ background: D.borderSubtle }"></div>
                    <div class="h-3 rounded-full mb-2 w-full" :style="{ background: D.borderSubtle }"></div>
                    <div class="h-3 rounded-full mb-4 w-2/3" :style="{ background: D.borderSubtle }"></div>
                    <div class="h-16 rounded-xl mb-3" :style="{ background: D.surfaceRaised, border: `1px solid ${D.borderSubtle}` }"></div>
                    <div class="h-16 rounded-xl opacity-60" :style="{ background: D.surfaceRaised, border: `1px solid ${D.borderSubtle}` }"></div>
                  </div>
                  <div class="absolute inset-0 var-c-backdrop-dk" style="border-radius: 22px;"></div>
                  <div class="absolute top-1/2 left-1/2" style="width: 196px; transform: translate(-50%, calc(-50% + 6px)); border-radius: 28px; opacity: 0.45;"
                       :style="{ background: D.surfaceOverlay, height: '196px' }"></div>
                  <div class="absolute top-1/2 left-1/2" style="width: 204px; transform: translate(-50%, -50%); border-radius: 28px;"
                       :style="{ background: D.surfaceOverlay, boxShadow: SHADOW_MODAL_DK }">
                    <div class="flex items-center justify-between px-4 pt-4 pb-2"
                         :style="{ borderBottom: `1px solid ${D.borderSubtle}` }">
                      <p class="text-[11px] font-semibold" :style="{ color: D.inkPrimary }">Share vault entry</p>
                      <button style="width: 22px; height: 22px; border-radius: 9999px; border: none; display: flex; align-items: center; justify-content: center;"
                              :style="{ background: D.surfaceSunken, color: D.inkSecondary }">
                        <XMarkIcon style="width: 11px; height: 11px;" />
                      </button>
                    </div>
                    <div class="px-4 py-3">
                      <p class="text-[10px] mb-3" :style="{ color: D.inkSecondary }">Choose a family member to share with.</p>
                      <div class="rounded-xl border mb-3 px-2 py-1.5"
                           :style="{ background: D.surfaceSunken, borderColor: D.borderSubtle }">
                        <p class="text-[9px]" :style="{ color: D.inkTertiary }">Recipient</p>
                        <p class="text-[10px]" :style="{ color: D.inkSecondary }">Select member…</p>
                      </div>
                      <div class="flex gap-1.5">
                        <button class="flex-1 text-[10px] font-medium"
                                style="height: 28px; border-radius: 9999px;"
                                :style="{ background: 'transparent', color: D.inkPrimary, border: `1px solid ${D.borderStrong}` }">Cancel</button>
                        <button class="flex-1 text-[10px] font-medium"
                                style="height: 28px; border-radius: 9999px;"
                                :style="{ background: D.accents.lavender.bold, color: D.inkInverse, border: 'none' }">Share</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Desktop mock dark -->
              <div class="flex-[2] space-y-2">
                <p class="text-[11px] font-medium uppercase tracking-wider" :style="{ color: D.inkTertiary }">Desktop — 1024 px · Glass-backdrop centered modal</p>
                <div class="relative overflow-hidden"
                     style="height: 340px; border-radius: 16px;"
                     :style="{ background: D.surfaceApp, border: `2px solid ${D.borderStrong}` }">
                  <div class="absolute inset-0 p-5">
                    <div class="flex gap-3 mb-4">
                      <div class="h-3 rounded-full w-48" :style="{ background: D.borderSubtle }"></div>
                      <div class="h-3 rounded-full w-24 ml-auto" :style="{ background: D.borderSubtle }"></div>
                    </div>
                    <div class="grid grid-cols-3 gap-3">
                      <div class="h-24 rounded-2xl" :style="{ background: D.surfaceRaised, border: `1px solid ${D.borderSubtle}` }"></div>
                      <div class="h-24 rounded-2xl opacity-70" :style="{ background: D.surfaceRaised, border: `1px solid ${D.borderSubtle}` }"></div>
                      <div class="h-24 rounded-2xl opacity-40" :style="{ background: D.surfaceRaised, border: `1px solid ${D.borderSubtle}` }"></div>
                    </div>
                  </div>
                  <div class="absolute inset-0 var-c-backdrop-dk" style="border-radius: 14px;"></div>
                  <div class="absolute top-1/2 left-1/2 opacity-40"
                       style="width: 348px; height: 216px; border-radius: 28px; transform: translate(-50%, calc(-50% + 8px));"
                       :style="{ background: D.surfaceOverlay }"></div>
                  <div class="absolute top-1/2 left-1/2"
                       style="width: 360px; border-radius: 28px; transform: translate(-50%, -50%);"
                       :style="{ background: D.surfaceOverlay, boxShadow: SHADOW_MODAL_DK }">
                    <div class="flex items-center justify-between px-5 pt-5 pb-3"
                         :style="{ borderBottom: `1px solid ${D.borderSubtle}` }">
                      <p class="text-[14px] font-semibold" :style="{ color: D.inkPrimary }">Share vault entry</p>
                      <button style="width: 28px; height: 28px; border-radius: 9999px; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center;"
                              :style="{ background: D.surfaceSunken, color: D.inkSecondary }">
                        <XMarkIcon style="width: 14px; height: 14px;" />
                      </button>
                    </div>
                    <div class="px-5 py-4 space-y-3">
                      <p class="text-[12px]" :style="{ color: D.inkSecondary }">
                        Choose which family member can view this entry. They will receive a notification.
                      </p>
                      <div class="rounded-xl border px-3 py-2"
                           :style="{ background: D.surfaceSunken, borderColor: D.borderSubtle }">
                        <p class="text-[10px] mb-0.5" :style="{ color: D.inkTertiary }">Name or entry title</p>
                        <p class="text-[12px]" :style="{ color: D.inkSecondary }">Health Insurance Card</p>
                      </div>
                      <div class="rounded-xl border px-3 py-2"
                           :style="{ background: D.surfaceSunken, borderColor: D.borderSubtle }">
                        <p class="text-[10px] mb-0.5" :style="{ color: D.inkTertiary }">Recipient</p>
                        <p class="text-[12px]" :style="{ color: D.inkSecondary }">Select family member…</p>
                      </div>
                    </div>
                    <div class="flex gap-2 px-5 pb-5">
                      <button class="flex-1 text-[12px] font-medium"
                              style="height: 36px; border-radius: 9999px;"
                              :style="{ background: 'transparent', color: D.inkPrimary, border: `1px solid ${D.borderStrong}` }">
                        Cancel
                      </button>
                      <button class="flex-1 text-[12px] font-medium"
                              style="height: 36px; border-radius: 9999px;"
                              :style="{ background: D.accents.lavender.bold, color: D.inkInverse, border: 'none' }">
                        Share entry
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div><!-- /dark -->
        </div>
      </VariantFrame>
      <p class="mt-3 text-sm px-1" :style="{ color: L.inkSecondary }">
        The blur glass backdrop looks polished on desktop and in screenshots, but on mobile the centered layout forces the X button to the top-right corner — a thumb stretch zone. Works best for low-frequency, high-importance modals (confirm delete, security prompts) where deliberate interaction is expected.
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
          Variant A directly fulfills the brief's core directive — one direction per breakpoint — while giving each context its native-feeling pattern: phone users get the thumb-reachable bottom sheet, desktop users get the focused centered dialog. The behavior shift is a single CSS media query in the real component, not a conditional component swap.
        </p>
        <p class="text-[14px] leading-relaxed" :style="{ color: L.inkPrimary }">
          Choosing Variant A becomes a project-wide rule: every existing modal that opens from the wrong direction on any breakpoint must be migrated. The payoff is that future components (task creation, vault edit, reward purchase) inherit the correct overlay direction automatically from the shared component — no per-feature decisions needed.
        </p>
      </div>
    </section>


    <!-- ══════════════════════════════════════════════════════════════════════
         USAGE GUIDE
         ═══════════════════════════════════════════════════════════════════ -->
    <section class="mb-8">
      <div class="rounded-2xl border p-6 space-y-5"
           :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle, boxShadow: SHADOW_MODAL_LT }">
        <h2 class="text-[15px] font-semibold" :style="{ color: L.inkPrimary }">Usage guide</h2>

        <!-- Core tenet -->
        <div class="rounded-xl p-4"
             :style="{ background: L.accents.sun.soft, border: `1px solid ${L.accents.sun.bold}` }">
          <p class="text-[13px] font-semibold mb-1" :style="{ color: L.accents.sun.bold }">Tenet: one overlay direction per breakpoint</p>
          <p class="text-[13px] leading-relaxed" :style="{ color: L.inkPrimary }">
            Whichever variant Greg picks becomes the single source of truth for all overlays in Kinhold. Any existing modal that opens from a different direction at that breakpoint gets migrated — no exceptions. Inconsistency here is the #1 cause of the "jarring popup" feeling the redesign is solving.
          </p>
        </div>

        <div class="space-y-4">
          <div>
            <p class="text-[13px] font-semibold mb-1" :style="{ color: L.inkPrimary }">Variant A — Responsive (recommended)</p>
            <ul class="space-y-1 text-[13px] leading-relaxed list-disc list-inside" :style="{ color: L.inkSecondary }">
              <li>Bottom sheet below 768 px. Centered modal at 768 px and above.</li>
              <li>Best for Kinhold — the app is used on phones but also reviewed on desktop by parents.</li>
              <li>Grab handle always shown on mobile sheet; X button always shown on desktop modal.</li>
              <li>No X button on mobile sheet — dismiss by swiping down or tapping the backdrop.</li>
            </ul>
          </div>

          <div>
            <p class="text-[13px] font-semibold mb-1" :style="{ color: L.inkPrimary }">Variant B — Always sheet</p>
            <ul class="space-y-1 text-[13px] leading-relaxed list-disc list-inside" :style="{ color: L.inkSecondary }">
              <li>Choose only if the app is genuinely phone-only and desktop usage is rare.</li>
              <li>Max-width 640 px centered horizontally on large screens — avoids full-stretch awkwardness.</li>
              <li>Avoid for forms with more than 4 fields — sheet height creates scroll debt on desktop.</li>
            </ul>
          </div>

          <div>
            <p class="text-[13px] font-semibold mb-1" :style="{ color: L.inkPrimary }">Variant C — Always centered modal</p>
            <ul class="space-y-1 text-[13px] leading-relaxed list-disc list-inside" :style="{ color: L.inkSecondary }">
              <li>Backdrop blur (8 px) applies at all sizes. Requires GPU composite layer — test on lower-end devices.</li>
              <li>Best for confirmations requiring deliberate intent: delete account, security prompts, payment confirmation.</li>
              <li>X button is in the top-right corner at all sizes — accessibility concern on mobile (thumb stretch). Always provide backdrop-tap-to-dismiss as a fallback.</li>
            </ul>
          </div>

          <div class="rounded-xl p-4 space-y-1"
               :style="{ background: L.surfaceSunken }">
            <p class="text-[13px] font-semibold" :style="{ color: L.inkPrimary }">Hard rules (all variants)</p>
            <ul class="space-y-1 text-[13px] leading-relaxed list-disc list-inside" :style="{ color: L.inkSecondary }">
              <li>Sheet corner radius: <code class="text-[12px] px-1 rounded" :style="{ background: L.borderSubtle }">28px 28px 0 0</code> (top corners only). Modal: <code class="text-[12px] px-1 rounded" :style="{ background: L.borderSubtle }">border-radius: 28px</code> all corners.</li>
              <li>Grab handle: 36 × 4 px pill, centered, 12 px from top, <code class="text-[12px] px-1 rounded" :style="{ background: L.borderSubtle }">borderStrong</code> color.</li>
              <li>Backdrop: <code class="text-[12px] px-1 rounded" :style="{ background: L.borderSubtle }">rgba(0,0,0,0.48)</code> light, <code class="text-[12px] px-1 rounded" :style="{ background: L.borderSubtle }">rgba(0,0,0,0.70)</code> dark. Variant C adds <code class="text-[12px] px-1 rounded" :style="{ background: L.borderSubtle }">backdrop-filter: blur(8px)</code>.</li>
              <li>Stacking: second sheet rendered at reduced opacity directly beneath — always shown, never interactive.</li>
              <li>Surface: <code class="text-[12px] px-1 rounded" :style="{ background: L.borderSubtle }">surfaceOverlay</code> token. Never use <code class="text-[12px] px-1 rounded" :style="{ background: L.borderSubtle }">surfaceRaised</code> — overlays need their own token to support future frosted-glass treatment independently.</li>
              <li>Use <code class="text-[12px] px-1 rounded" :style="{ background: L.borderSubtle }">position: fixed</code> in production. In this design-system demo, mocks use <code class="text-[12px] px-1 rounded" :style="{ background: L.borderSubtle }">position: absolute</code> inside bounded phone-frame containers to avoid covering the page.</li>
              <li>Motion: enter via <code class="text-[12px] px-1 rounded" :style="{ background: L.borderSubtle }">translateY(100%)</code> → <code class="text-[12px] px-1 rounded" :style="{ background: L.borderSubtle }">translateY(0)</code> (sheet) or <code class="text-[12px] px-1 rounded" :style="{ background: L.borderSubtle }">scale(0.96)</code> → <code class="text-[12px] px-1 rounded" :style="{ background: L.borderSubtle }">scale(1)</code> (modal), 280 ms ease-out. Wrap in <code class="text-[12px] px-1 rounded" :style="{ background: L.borderSubtle }">@media (prefers-reduced-motion: reduce)</code> to skip animations for users who opt out — show/hide instantly.</li>
            </ul>
          </div>
        </div>
      </div>
    </section>

  </ComponentPage>
</template>

<style scoped>
/* ── Variant C glass backdrop ─────────────────────────────────────────────── */
/* Light: semi-transparent + blur */
.var-c-backdrop-lt {
  background: rgba(0, 0, 0, 0.48);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
}

/* Dark: heavier dim + blur */
.var-c-backdrop-dk {
  background: rgba(0, 0, 0, 0.70);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
}
</style>
