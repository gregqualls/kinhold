<script setup>
import { ref } from 'vue'
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import { CheckIcon, MinusIcon } from '@heroicons/vue/24/outline'

// ── Dark-mode hex constants ─────────────────────────────────────────────────
const D = {
  surfaceApp:     '#141311',
  surfaceRaised:  '#1C1B19',
  surfaceSunken:  '#161513',
  surfaceOverlay: '#242220',  // dark input bg — lighter than page so controls read
  inkPrimary:     '#F0EDE9',
  inkSecondary:   '#A09C97',
  inkTertiary:    '#6E6B67',
  borderSubtle:   '#2C2A27',
  borderStrong:   '#403E3A',
  accentSoft:     '#302A48',
  accentBold:     '#B6A8E6',  // accent-lavender-bold dark
}

// ── Light-mode hex constants ─────────────────────────────────────────────────
const L = {
  surfaceApp:     '#FAF8F5',
  surfaceRaised:  '#FFFFFF',
  surfaceSunken:  '#F5F2EE',
  inkPrimary:     '#1C1C1E',
  inkSecondary:   '#6B6966',
  inkTertiary:    '#9C9895',
  borderSubtle:   '#E8E4DF',
  borderStrong:   '#BCB8B2',
  accentSoft:     '#EAE6F8',
  accentBold:     '#6856B2',  // accent-lavender-bold light
}

// ── Indeterminate refs — JS-only state ─────────────────────────────────────
const cbAIndetermLt  = ref(true)
const cbAIndetermDk  = ref(true)
const cbBIndetermLt  = ref(true)
const cbBIndetermDk  = ref(true)

// ── Checkbox A group (light + dark) ────────────────────────────────────────
const cbAGroupLt = ref([true, false, false, true])  // checked, unchecked, indeterminate (via cbAIndetermLt), disabled-checked
const cbAGroupDk = ref([true, false, false, true])

// ── Checkbox B group ───────────────────────────────────────────────────────
const cbBGroupLt = ref([true, false, false, true])
const cbBGroupDk = ref([true, false, false, true])

// ── Radio A ────────────────────────────────────────────────────────────────
const rdAGroupLt = ref('option1')
const rdAGroupDk = ref('option1')
const rdAHorizLt = ref('all')
const rdAHorizDk = ref('all')

// ── Radio B ────────────────────────────────────────────────────────────────
const rdBGroupLt = ref('option1')
const rdBGroupDk = ref('option1')
const rdBHorizLt = ref('all')
const rdBHorizDk = ref('all')

// ── Switch ─────────────────────────────────────────────────────────────────
const swLt = ref([false, true, false, true])   // [completed, briefing, calendar, sound]
const swDk = ref([false, true, false, true])

const swSettingsLabels = [
  { title: 'Show completed tasks', helper: 'Display finished tasks in your task lists' },
  { title: 'Send morning briefing email', helper: 'Daily summary delivered at 7 AM' },
  { title: 'Share calendar with family', helper: 'All family members can see your events' },
  { title: 'Play success sound on task complete', helper: 'A short chime plays when you check off a task' },
]

const radioOptions = [
  { value: 'option1', label: 'Wash dishes' },
  { value: 'option2', label: 'Feed dog' },
  { value: 'option3', label: 'Pack lunches' },
  { value: 'option4', label: 'Take out trash' },
]

const horizOptions = [
  { value: 'all',      label: 'All' },
  { value: 'mine',     label: 'Mine' },
  { value: 'assigned', label: 'Assigned' },
]

const checkboxTasks = [
  { label: 'Wash dishes' },
  { label: 'Feed dog' },
  { label: 'Pack lunches' },
  { label: 'Take out trash' },
]
</script>

<template>
  <ComponentPage
    title="1.5 Checkbox · Radio · Switch"
    description="Selection controls. Minimal neutral fill when checked (near-black / off-white) for both checkbox and radio; Apple-style pastel-accent toggle for switches."
    status="chosen"
  >

    <!-- ══════════════════════════════════════════════════════════════
         SECTION 1 — CHECKBOX
         Variant A: Minimal neutral filled
         Variant B: Pastel-accent when checked
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-20">

      <!-- ─── VARIANT A — Minimal neutral filled ─────────────────────── -->
      <VariantFrame label="Checkbox" caption="Minimal neutral — near-black fill when checked">
        <div class="w-full space-y-10">

          <!-- LIGHT PANEL A -->
          <div class="rounded-2xl border p-6 space-y-8" style="background:#FAF8F5; border-color:#E8E4DF">
            <p class="text-xs font-semibold uppercase tracking-widest" style="color:#9C9895">Light mode</p>

            <!-- States row at md -->
            <div>
              <p class="text-xs mb-3" style="color:#9C9895">States — md (18px)</p>
              <div class="flex flex-wrap items-center gap-6">
                <!-- unchecked -->
                <div class="flex flex-col items-center gap-2">
                  <label class="cb-a-lt relative inline-flex cursor-pointer">
                    <input type="checkbox" class="peer sr-only" />
                    <span class="cb-visual-a-lt w-[18px] h-[18px] rounded-[5px] flex items-center justify-center flex-shrink-0"
                      style="border: 1.5px solid #BCB8B2; background:#F5F2EE" />
                  </label>
                  <span class="text-[10px]" style="color:#9C9895">unchecked</span>
                </div>
                <!-- hover simulated -->
                <div class="flex flex-col items-center gap-2">
                  <span class="w-[18px] h-[18px] rounded-[5px] flex items-center justify-center flex-shrink-0"
                    style="border: 1.5px solid #6B6966; background:#F5F2EE" />
                  <span class="text-[10px]" style="color:#9C9895">hover</span>
                </div>
                <!-- focused -->
                <div class="flex flex-col items-center gap-2">
                  <span class="w-[18px] h-[18px] rounded-[5px] flex items-center justify-center flex-shrink-0"
                    style="border: 1.5px solid #6856B2; background:#F5F2EE; box-shadow: 0 0 0 3px rgba(104,86,178,0.25)" />
                  <span class="text-[10px]" style="color:#9C9895">focused</span>
                </div>
                <!-- checked -->
                <div class="flex flex-col items-center gap-2">
                  <label class="relative inline-flex cursor-pointer">
                    <input type="checkbox" class="peer sr-only" checked />
                    <span class="cb-a-lt-chk w-[18px] h-[18px] rounded-[5px] flex items-center justify-center flex-shrink-0"
                      style="background:#1C1C1E">
                      <CheckIcon class="w-3 h-3 flex-shrink-0" style="color:#FAF8F5; stroke-width: 2.5" />
                    </span>
                  </label>
                  <span class="text-[10px]" style="color:#9C9895">checked</span>
                </div>
                <!-- indeterminate -->
                <div class="flex flex-col items-center gap-2">
                  <label class="relative inline-flex cursor-pointer">
                    <input type="checkbox" class="peer sr-only" :indeterminate.prop="cbAIndetermLt" />
                    <span class="cb-a-lt-chk w-[18px] h-[18px] rounded-[5px] flex items-center justify-center flex-shrink-0"
                      style="background:#1C1C1E">
                      <MinusIcon class="w-3 h-3 flex-shrink-0" style="color:#FAF8F5; stroke-width: 2.5" />
                    </span>
                  </label>
                  <span class="text-[10px]" style="color:#9C9895">indeterminate</span>
                </div>
                <!-- disabled -->
                <div class="flex flex-col items-center gap-2">
                  <span class="w-[18px] h-[18px] rounded-[5px] opacity-40 cursor-not-allowed"
                    style="border: 1.5px solid #BCB8B2; background:#F5F2EE" />
                  <span class="text-[10px]" style="color:#9C9895">disabled</span>
                </div>
                <!-- disabled + checked -->
                <div class="flex flex-col items-center gap-2">
                  <span class="w-[18px] h-[18px] rounded-[5px] opacity-40 cursor-not-allowed flex items-center justify-center"
                    style="background:#1C1C1E">
                    <CheckIcon class="w-3 h-3 flex-shrink-0" style="color:#FAF8F5; stroke-width: 2.5" />
                  </span>
                  <span class="text-[10px]" style="color:#9C9895">dis. checked</span>
                </div>
              </div>
            </div>

            <!-- Sizes row -->
            <div>
              <p class="text-xs mb-3" style="color:#9C9895">Sizes</p>
              <div class="flex items-center gap-6">
                <div class="flex flex-col items-center gap-2">
                  <span class="w-[14px] h-[14px] rounded-[4px] flex items-center justify-center"
                    style="background:#1C1C1E">
                    <CheckIcon class="w-2.5 h-2.5" style="color:#FAF8F5; stroke-width:2.5" />
                  </span>
                  <span class="text-[10px]" style="color:#9C9895">sm 14px</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <span class="w-[18px] h-[18px] rounded-[5px] flex items-center justify-center"
                    style="background:#1C1C1E">
                    <CheckIcon class="w-3 h-3" style="color:#FAF8F5; stroke-width:2.5" />
                  </span>
                  <span class="text-[10px]" style="color:#9C9895">md 18px</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <span class="w-[22px] h-[22px] rounded-[6px] flex items-center justify-center"
                    style="background:#1C1C1E">
                    <CheckIcon class="w-3.5 h-3.5" style="color:#FAF8F5; stroke-width:2.5" />
                  </span>
                  <span class="text-[10px]" style="color:#9C9895">lg 22px</span>
                </div>
              </div>
            </div>

            <!-- With label row -->
            <div>
              <p class="text-xs mb-3" style="color:#9C9895">With label — click anywhere to toggle</p>
              <div class="flex flex-col gap-3">
                <label class="inline-flex items-start gap-2.5 cursor-pointer select-none">
                  <span class="relative mt-0.5 flex-shrink-0">
                    <input type="checkbox" class="peer sr-only" checked />
                    <span class="cb-a-lt-peer w-[18px] h-[18px] rounded-[5px] flex items-center justify-center"
                      style="border: 1.5px solid #BCB8B2; background:#F5F2EE">
                      <CheckIcon class="w-3 h-3 hidden peer-checked:block" style="color:#FAF8F5; stroke-width:2.5" />
                    </span>
                  </span>
                  <span class="text-[14px]" style="color:#1C1C1E">Short</span>
                </label>
                <label class="inline-flex items-start gap-2.5 cursor-pointer select-none">
                  <span class="relative mt-0.5 flex-shrink-0">
                    <input type="checkbox" class="peer sr-only" />
                    <span class="cb-a-lt-peer w-[18px] h-[18px] rounded-[5px] flex items-center justify-center"
                      style="border: 1.5px solid #BCB8B2; background:#F5F2EE">
                      <CheckIcon class="w-3 h-3 hidden peer-checked:block" style="color:#FAF8F5; stroke-width:2.5" />
                    </span>
                  </span>
                  <span class="text-[14px]" style="color:#1C1C1E">Medium length label text</span>
                </label>
                <label class="inline-flex items-start gap-2.5 cursor-pointer select-none max-w-xs">
                  <span class="relative mt-0.5 flex-shrink-0">
                    <input type="checkbox" class="peer sr-only" />
                    <span class="cb-a-lt-peer w-[18px] h-[18px] rounded-[5px] flex items-center justify-center"
                      style="border: 1.5px solid #BCB8B2; background:#F5F2EE">
                      <CheckIcon class="w-3 h-3 hidden peer-checked:block" style="color:#FAF8F5; stroke-width:2.5" />
                    </span>
                  </span>
                  <span class="text-[14px] leading-snug" style="color:#1C1C1E">A longer label that wraps to a second line to show how the checkbox aligns with multi-line text</span>
                </label>
              </div>
            </div>

            <!-- Grouped list -->
            <div>
              <p class="text-xs mb-3" style="color:#9C9895">Grouped list</p>
              <ul class="space-y-3">
                <li v-for="(task, i) in checkboxTasks" :key="i">
                  <label class="inline-flex items-center gap-2.5 cursor-pointer select-none">
                    <span class="relative flex-shrink-0">
                      <input
                        type="checkbox"
                        class="peer sr-only"
                        :checked="i === 0"
                        :indeterminate.prop="i === 2 ? true : false"
                      />
                      <!-- visual shell — scoped CSS handles peer-checked states -->
                      <span class="cb-a-lt-peer w-[18px] h-[18px] rounded-[5px] flex items-center justify-center"
                        style="border: 1.5px solid #BCB8B2; background:#F5F2EE">
                        <CheckIcon v-if="i === 0" class="w-3 h-3" style="color:#FAF8F5; stroke-width:2.5" />
                        <MinusIcon v-else-if="i === 2" class="w-3 h-3" style="color:#FAF8F5; stroke-width:2.5" />
                      </span>
                    </span>
                    <span class="text-[14px]" :style="{ color: i === 0 ? L.inkSecondary : L.inkPrimary, textDecoration: i === 0 ? 'line-through' : 'none' }">{{ task.label }}</span>
                  </label>
                </li>
              </ul>
            </div>
          </div><!-- /light A -->

          <!-- DARK PANEL A -->
          <div class="rounded-2xl border p-6 space-y-8" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

            <!-- States row -->
            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">States — md (18px)</p>
              <div class="flex flex-wrap items-center gap-6">
                <div class="flex flex-col items-center gap-2">
                  <span class="cb-a-dk w-[18px] h-[18px] rounded-[5px]"
                    :style="{ border: `1.5px solid ${D.borderStrong}`, background: D.surfaceOverlay }" />
                  <span class="text-[10px]" :style="{ color: D.inkTertiary }">unchecked</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <span class="w-[18px] h-[18px] rounded-[5px]"
                    :style="{ border: '1.5px solid #5C5A56', background: D.surfaceOverlay }" />
                  <span class="text-[10px]" :style="{ color: D.inkTertiary }">hover</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <span class="w-[18px] h-[18px] rounded-[5px]"
                    :style="{ border: `1.5px solid ${D.accentBold}`, background: D.surfaceOverlay, boxShadow: '0 0 0 3px rgba(182,168,230,0.30)' }" />
                  <span class="text-[10px]" :style="{ color: D.inkTertiary }">focused</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <span class="cb-a-dk-chk w-[18px] h-[18px] rounded-[5px] flex items-center justify-center"
                    :style="{ background: D.inkPrimary }">
                    <CheckIcon class="w-3 h-3" style="color:#141311; stroke-width:2.5" />
                  </span>
                  <span class="text-[10px]" :style="{ color: D.inkTertiary }">checked</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <span class="cb-a-dk-chk w-[18px] h-[18px] rounded-[5px] flex items-center justify-center"
                    :style="{ background: D.inkPrimary }">
                    <MinusIcon class="w-3 h-3" style="color:#141311; stroke-width:2.5" />
                  </span>
                  <span class="text-[10px]" :style="{ color: D.inkTertiary }">indeterminate</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <span class="w-[18px] h-[18px] rounded-[5px] opacity-40 cursor-not-allowed"
                    :style="{ border: `1.5px solid ${D.borderStrong}`, background: D.surfaceOverlay }" />
                  <span class="text-[10px]" :style="{ color: D.inkTertiary }">disabled</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <span class="w-[18px] h-[18px] rounded-[5px] opacity-40 cursor-not-allowed flex items-center justify-center"
                    :style="{ background: D.inkPrimary }">
                    <CheckIcon class="w-3 h-3" style="color:#141311; stroke-width:2.5" />
                  </span>
                  <span class="text-[10px]" :style="{ color: D.inkTertiary }">dis. checked</span>
                </div>
              </div>
            </div>

            <!-- Sizes -->
            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">Sizes</p>
              <div class="flex items-center gap-6">
                <div class="flex flex-col items-center gap-2">
                  <span class="w-[14px] h-[14px] rounded-[4px] flex items-center justify-center" :style="{ background: D.inkPrimary }">
                    <CheckIcon class="w-2.5 h-2.5" style="color:#141311; stroke-width:2.5" />
                  </span>
                  <span class="text-[10px]" :style="{ color: D.inkTertiary }">sm 14px</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <span class="w-[18px] h-[18px] rounded-[5px] flex items-center justify-center" :style="{ background: D.inkPrimary }">
                    <CheckIcon class="w-3 h-3" style="color:#141311; stroke-width:2.5" />
                  </span>
                  <span class="text-[10px]" :style="{ color: D.inkTertiary }">md 18px</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <span class="w-[22px] h-[22px] rounded-[6px] flex items-center justify-center" :style="{ background: D.inkPrimary }">
                    <CheckIcon class="w-3.5 h-3.5" style="color:#141311; stroke-width:2.5" />
                  </span>
                  <span class="text-[10px]" :style="{ color: D.inkTertiary }">lg 22px</span>
                </div>
              </div>
            </div>

            <!-- With label -->
            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">With label</p>
              <div class="flex flex-col gap-3">
                <label class="inline-flex items-start gap-2.5 cursor-pointer select-none">
                  <span class="relative mt-0.5 flex-shrink-0">
                    <input type="checkbox" class="peer sr-only" checked />
                    <span class="cb-a-dk-peer w-[18px] h-[18px] rounded-[5px] flex items-center justify-center">
                      <CheckIcon class="w-3 h-3 hidden" style="stroke-width:2.5" />
                    </span>
                  </span>
                  <span class="text-[14px]" :style="{ color: D.inkPrimary }">Short</span>
                </label>
                <label class="inline-flex items-start gap-2.5 cursor-pointer select-none">
                  <span class="relative mt-0.5 flex-shrink-0">
                    <input type="checkbox" class="peer sr-only" />
                    <span class="cb-a-dk-peer w-[18px] h-[18px] rounded-[5px] flex items-center justify-center">
                      <CheckIcon class="w-3 h-3 hidden" style="stroke-width:2.5" />
                    </span>
                  </span>
                  <span class="text-[14px]" :style="{ color: D.inkPrimary }">Medium length label text</span>
                </label>
                <label class="inline-flex items-start gap-2.5 cursor-pointer select-none max-w-xs">
                  <span class="relative mt-0.5 flex-shrink-0">
                    <input type="checkbox" class="peer sr-only" />
                    <span class="cb-a-dk-peer w-[18px] h-[18px] rounded-[5px] flex items-center justify-center">
                      <CheckIcon class="w-3 h-3 hidden" style="stroke-width:2.5" />
                    </span>
                  </span>
                  <span class="text-[14px] leading-snug" :style="{ color: D.inkPrimary }">A longer label that wraps to a second line to show how the checkbox aligns with multi-line text</span>
                </label>
              </div>
            </div>

            <!-- Grouped list dark -->
            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">Grouped list</p>
              <ul class="space-y-3">
                <li v-for="(task, i) in checkboxTasks" :key="i">
                  <label class="inline-flex items-center gap-2.5 cursor-pointer select-none">
                    <span class="relative flex-shrink-0">
                      <input type="checkbox" class="peer sr-only" :checked="i === 0" />
                      <span class="cb-a-dk-peer w-[18px] h-[18px] rounded-[5px] flex items-center justify-center">
                        <CheckIcon v-if="i === 0" class="w-3 h-3" style="stroke-width:2.5; color:#141311" />
                        <MinusIcon v-else-if="i === 2" class="w-3 h-3" style="stroke-width:2.5; color:#141311" />
                      </span>
                    </span>
                    <span class="text-[14px]" :style="{ color: i === 0 ? D.inkTertiary : D.inkPrimary, textDecoration: i === 0 ? 'line-through' : 'none' }">{{ task.label }}</span>
                  </label>
                </li>
              </ul>
            </div>
          </div><!-- /dark A -->
        </div>
      </VariantFrame>


    </section>


    <!-- ══════════════════════════════════════════════════════════════
         SECTION 2 — RADIO
         Variant A: Minimal neutral
         Variant B: Pastel-accent when checked
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-20">

      <!-- RADIO VARIANT A -->
      <VariantFrame label="Radio" caption="Minimal neutral — ink-primary inner dot when checked">
        <div class="w-full space-y-10">

          <!-- LIGHT PANEL Radio A -->
          <div class="rounded-2xl border p-6 space-y-8" style="background:#FAF8F5; border-color:#E8E4DF">
            <p class="text-xs font-semibold uppercase tracking-widest" style="color:#9C9895">Light mode</p>

            <!-- States row -->
            <div>
              <p class="text-xs mb-3" style="color:#9C9895">States — md (18px)</p>
              <div class="flex flex-wrap items-center gap-6">
                <div class="flex flex-col items-center gap-2">
                  <span class="rd-a-lt w-[18px] h-[18px] rounded-full" style="border: 1.5px solid #BCB8B2; background:#F5F2EE" />
                  <span class="text-[10px]" style="color:#9C9895">unchecked</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <span class="w-[18px] h-[18px] rounded-full" style="border: 1.5px solid #6B6966; background:#F5F2EE" />
                  <span class="text-[10px]" style="color:#9C9895">hover</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <span class="w-[18px] h-[18px] rounded-full" style="border: 1.5px solid #6856B2; background:#F5F2EE; box-shadow: 0 0 0 3px rgba(104,86,178,0.25)" />
                  <span class="text-[10px]" style="color:#9C9895">focused</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <!-- checked: border-strong + 8px inner dot = ink-primary -->
                  <span class="rd-a-lt-chk w-[18px] h-[18px] rounded-full flex items-center justify-center" style="border: 1.5px solid #BCB8B2; background:#F5F2EE">
                    <span class="w-2 h-2 rounded-full flex-shrink-0" style="background:#1C1C1E" />
                  </span>
                  <span class="text-[10px]" style="color:#9C9895">checked</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <span class="w-[18px] h-[18px] rounded-full opacity-40 cursor-not-allowed" style="border: 1.5px solid #BCB8B2; background:#F5F2EE" />
                  <span class="text-[10px]" style="color:#9C9895">disabled</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <span class="w-[18px] h-[18px] rounded-full opacity-40 cursor-not-allowed flex items-center justify-center" style="border: 1.5px solid #BCB8B2; background:#F5F2EE">
                    <span class="w-2 h-2 rounded-full" style="background:#1C1C1E" />
                  </span>
                  <span class="text-[10px]" style="color:#9C9895">dis. checked</span>
                </div>
              </div>
            </div>

            <!-- Sizes -->
            <div>
              <p class="text-xs mb-3" style="color:#9C9895">Sizes</p>
              <div class="flex items-center gap-6">
                <div class="flex flex-col items-center gap-2">
                  <span class="w-[14px] h-[14px] rounded-full flex items-center justify-center" style="border: 1.5px solid #BCB8B2; background:#F5F2EE">
                    <span class="w-[6px] h-[6px] rounded-full" style="background:#1C1C1E" />
                  </span>
                  <span class="text-[10px]" style="color:#9C9895">sm 14px</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <span class="w-[18px] h-[18px] rounded-full flex items-center justify-center" style="border: 1.5px solid #BCB8B2; background:#F5F2EE">
                    <span class="w-2 h-2 rounded-full" style="background:#1C1C1E" />
                  </span>
                  <span class="text-[10px]" style="color:#9C9895">md 18px</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <span class="w-[22px] h-[22px] rounded-full flex items-center justify-center" style="border: 1.5px solid #BCB8B2; background:#F5F2EE">
                    <span class="w-2.5 h-2.5 rounded-full" style="background:#1C1C1E" />
                  </span>
                  <span class="text-[10px]" style="color:#9C9895">lg 22px</span>
                </div>
              </div>
            </div>

            <!-- Radio group -->
            <div>
              <p class="text-xs mb-3" style="color:#9C9895">Radio group (fieldset / legend — arrow-key navigation)</p>
              <fieldset class="border-0 p-0 m-0">
                <legend class="text-[12px] font-medium mb-3" style="color:#6B6966">Choose a task</legend>
                <ul class="space-y-3">
                  <li v-for="opt in radioOptions" :key="opt.value">
                    <label class="inline-flex items-center gap-2.5 cursor-pointer select-none">
                      <span class="relative flex-shrink-0">
                        <input
                          type="radio"
                          :value="opt.value"
                          v-model="rdAGroupLt"
                          name="rd-a-lt"
                          class="peer sr-only"
                        />
                        <span class="rd-a-lt-peer w-[18px] h-[18px] rounded-full flex items-center justify-center"
                          style="border: 1.5px solid #BCB8B2; background:#F5F2EE">
                          <span class="w-2 h-2 rounded-full flex-shrink-0" style="background:#1C1C1E; opacity:0; transition: opacity 200ms" />
                        </span>
                      </span>
                      <span class="text-[14px]" style="color:#1C1C1E">{{ opt.label }}</span>
                    </label>
                  </li>
                </ul>
              </fieldset>
            </div>

            <!-- Horizontal -->
            <div>
              <p class="text-xs mb-3" style="color:#9C9895">Horizontal variant — compact filter row</p>
              <fieldset class="border-0 p-0 m-0">
                <legend class="sr-only">View filter</legend>
                <div class="flex items-center gap-5">
                  <label v-for="opt in horizOptions" :key="opt.value"
                    class="inline-flex items-center gap-2 cursor-pointer select-none">
                    <span class="relative flex-shrink-0">
                      <input
                        type="radio"
                        :value="opt.value"
                        v-model="rdAHorizLt"
                        name="rd-a-horiz-lt"
                        class="peer sr-only"
                      />
                      <span class="rd-a-lt-peer w-[18px] h-[18px] rounded-full flex items-center justify-center"
                        style="border: 1.5px solid #BCB8B2; background:#F5F2EE">
                        <span class="w-2 h-2 rounded-full" style="background:#1C1C1E; opacity:0; transition: opacity 200ms" />
                      </span>
                    </span>
                    <span class="text-[13px]" style="color:#1C1C1E">{{ opt.label }}</span>
                  </label>
                </div>
              </fieldset>
            </div>
          </div><!-- /light radio A -->

          <!-- DARK PANEL Radio A -->
          <div class="rounded-2xl border p-6 space-y-8" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

            <!-- States -->
            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">States — md (18px)</p>
              <div class="flex flex-wrap items-center gap-6">
                <div class="flex flex-col items-center gap-2">
                  <span class="rd-a-dk w-[18px] h-[18px] rounded-full"
                    :style="{ border: `1.5px solid ${D.borderStrong}`, background: D.surfaceOverlay }" />
                  <span class="text-[10px]" :style="{ color: D.inkTertiary }">unchecked</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <span class="w-[18px] h-[18px] rounded-full"
                    :style="{ border: '1.5px solid #5C5A56', background: D.surfaceOverlay }" />
                  <span class="text-[10px]" :style="{ color: D.inkTertiary }">hover</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <span class="w-[18px] h-[18px] rounded-full"
                    :style="{ border: `1.5px solid ${D.accentBold}`, background: D.surfaceOverlay, boxShadow: '0 0 0 3px rgba(182,168,230,0.30)' }" />
                  <span class="text-[10px]" :style="{ color: D.inkTertiary }">focused</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <span class="rd-a-dk-chk w-[18px] h-[18px] rounded-full flex items-center justify-center"
                    :style="{ border: `1.5px solid ${D.borderStrong}`, background: D.surfaceOverlay }">
                    <span class="w-2 h-2 rounded-full" :style="{ background: D.inkPrimary }" />
                  </span>
                  <span class="text-[10px]" :style="{ color: D.inkTertiary }">checked</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <span class="w-[18px] h-[18px] rounded-full opacity-40 cursor-not-allowed"
                    :style="{ border: `1.5px solid ${D.borderStrong}`, background: D.surfaceOverlay }" />
                  <span class="text-[10px]" :style="{ color: D.inkTertiary }">disabled</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <span class="w-[18px] h-[18px] rounded-full opacity-40 cursor-not-allowed flex items-center justify-center"
                    :style="{ border: `1.5px solid ${D.borderStrong}`, background: D.surfaceOverlay }">
                    <span class="w-2 h-2 rounded-full" :style="{ background: D.inkPrimary }" />
                  </span>
                  <span class="text-[10px]" :style="{ color: D.inkTertiary }">dis. checked</span>
                </div>
              </div>
            </div>

            <!-- Sizes dark -->
            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">Sizes</p>
              <div class="flex items-center gap-6">
                <div class="flex flex-col items-center gap-2">
                  <span class="w-[14px] h-[14px] rounded-full flex items-center justify-center" :style="{ border: `1.5px solid ${D.borderStrong}`, background: D.surfaceOverlay }">
                    <span class="w-[6px] h-[6px] rounded-full" :style="{ background: D.inkPrimary }" />
                  </span>
                  <span class="text-[10px]" :style="{ color: D.inkTertiary }">sm 14px</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <span class="w-[18px] h-[18px] rounded-full flex items-center justify-center" :style="{ border: `1.5px solid ${D.borderStrong}`, background: D.surfaceOverlay }">
                    <span class="w-2 h-2 rounded-full" :style="{ background: D.inkPrimary }" />
                  </span>
                  <span class="text-[10px]" :style="{ color: D.inkTertiary }">md 18px</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <span class="w-[22px] h-[22px] rounded-full flex items-center justify-center" :style="{ border: `1.5px solid ${D.borderStrong}`, background: D.surfaceOverlay }">
                    <span class="w-2.5 h-2.5 rounded-full" :style="{ background: D.inkPrimary }" />
                  </span>
                  <span class="text-[10px]" :style="{ color: D.inkTertiary }">lg 22px</span>
                </div>
              </div>
            </div>

            <!-- Radio group dark -->
            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">Radio group</p>
              <fieldset class="border-0 p-0 m-0">
                <legend class="text-[12px] font-medium mb-3" :style="{ color: D.inkSecondary }">Choose a task</legend>
                <ul class="space-y-3">
                  <li v-for="opt in radioOptions" :key="opt.value">
                    <label class="inline-flex items-center gap-2.5 cursor-pointer select-none">
                      <span class="relative flex-shrink-0">
                        <input
                          type="radio"
                          :value="opt.value"
                          v-model="rdAGroupDk"
                          name="rd-a-dk"
                          class="peer sr-only"
                        />
                        <span class="rd-a-dk-peer w-[18px] h-[18px] rounded-full flex items-center justify-center">
                          <span class="w-2 h-2 rounded-full" style="opacity:0; transition: opacity 200ms" :style="{ background: D.inkPrimary }" />
                        </span>
                      </span>
                      <span class="text-[14px]" :style="{ color: D.inkPrimary }">{{ opt.label }}</span>
                    </label>
                  </li>
                </ul>
              </fieldset>
            </div>

            <!-- Horizontal dark -->
            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">Horizontal variant</p>
              <fieldset class="border-0 p-0 m-0">
                <legend class="sr-only">View filter</legend>
                <div class="flex items-center gap-5">
                  <label v-for="opt in horizOptions" :key="opt.value"
                    class="inline-flex items-center gap-2 cursor-pointer select-none">
                    <span class="relative flex-shrink-0">
                      <input
                        type="radio"
                        :value="opt.value"
                        v-model="rdAHorizDk"
                        name="rd-a-horiz-dk"
                        class="peer sr-only"
                      />
                      <span class="rd-a-dk-peer w-[18px] h-[18px] rounded-full flex items-center justify-center">
                        <span class="w-2 h-2 rounded-full" style="opacity:0; transition: opacity 200ms" :style="{ background: D.inkPrimary }" />
                      </span>
                    </span>
                    <span class="text-[13px]" :style="{ color: D.inkPrimary }">{{ opt.label }}</span>
                  </label>
                </div>
              </fieldset>
            </div>
          </div><!-- /dark radio A -->
        </div>
      </VariantFrame>
    </section>


    <!-- ══════════════════════════════════════════════════════════════
         SECTION 3 — SWITCH (single treatment, no A/B picker)
         Apple-style pastel-accent on-state. Locks automatically.
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="Switch" caption="Apple-style toggle — pastel-accent on track, smooth 200ms thumb transition">
        <div class="w-full space-y-10">

          <!-- LIGHT PANEL switch -->
          <div class="rounded-2xl border p-6 space-y-8" style="background:#FAF8F5; border-color:#E8E4DF">
            <p class="text-xs font-semibold uppercase tracking-widest" style="color:#9C9895">Light mode</p>

            <!-- States row at md -->
            <div>
              <p class="text-xs mb-3" style="color:#9C9895">States — md (36×22px)</p>
              <div class="flex flex-wrap items-center gap-8">
                <!-- off -->
                <div class="flex flex-col items-center gap-2">
                  <label class="sw-lt relative inline-flex cursor-pointer">
                    <input type="checkbox" class="peer sr-only" />
                    <span class="sw-track-lt w-[36px] h-[22px] rounded-full flex-shrink-0 relative"
                      style="background:#E8E4DF; padding: 2px">
                      <span class="sw-thumb-lt absolute top-[2px] left-[2px] w-[18px] h-[18px] rounded-full"
                        style="background:#FFFFFF; box-shadow: 0 1px 3px rgba(0,0,0,0.20), 0 1px 2px rgba(0,0,0,0.10)" />
                    </span>
                  </label>
                  <span class="text-[10px]" style="color:#9C9895">off</span>
                </div>
                <!-- on -->
                <div class="flex flex-col items-center gap-2">
                  <label class="sw-lt relative inline-flex cursor-pointer">
                    <input type="checkbox" class="peer sr-only" checked />
                    <span class="sw-track-lt w-[36px] h-[22px] rounded-full flex-shrink-0 relative"
                      style="background:#6856B2; padding: 2px">
                      <span class="sw-thumb-lt-on absolute top-[2px] right-[2px] w-[18px] h-[18px] rounded-full"
                        style="background:#FFFFFF; box-shadow: 0 1px 3px rgba(0,0,0,0.20), 0 1px 2px rgba(0,0,0,0.10)" />
                    </span>
                  </label>
                  <span class="text-[10px]" style="color:#9C9895">on</span>
                </div>
                <!-- focused -->
                <div class="flex flex-col items-center gap-2">
                  <label class="relative inline-flex cursor-pointer">
                    <input type="checkbox" class="peer sr-only" />
                    <span class="w-[36px] h-[22px] rounded-full flex-shrink-0 relative"
                      style="background:#E8E4DF; padding: 2px; box-shadow: 0 0 0 3px rgba(104,86,178,0.25)">
                      <span class="absolute top-[2px] left-[2px] w-[18px] h-[18px] rounded-full"
                        style="background:#FFFFFF; box-shadow: 0 1px 3px rgba(0,0,0,0.20)" />
                    </span>
                  </label>
                  <span class="text-[10px]" style="color:#9C9895">focused</span>
                </div>
                <!-- disabled off -->
                <div class="flex flex-col items-center gap-2">
                  <label class="relative inline-flex cursor-not-allowed opacity-40">
                    <input type="checkbox" class="peer sr-only" disabled />
                    <span class="w-[36px] h-[22px] rounded-full flex-shrink-0 relative"
                      style="background:#E8E4DF; padding: 2px">
                      <span class="absolute top-[2px] left-[2px] w-[18px] h-[18px] rounded-full"
                        style="background:#FFFFFF; box-shadow: 0 1px 3px rgba(0,0,0,0.15)" />
                    </span>
                  </label>
                  <span class="text-[10px]" style="color:#9C9895">disabled</span>
                </div>
                <!-- disabled on -->
                <div class="flex flex-col items-center gap-2">
                  <label class="relative inline-flex cursor-not-allowed opacity-40">
                    <input type="checkbox" class="peer sr-only" disabled checked />
                    <span class="w-[36px] h-[22px] rounded-full flex-shrink-0 relative"
                      style="background:#6856B2; padding: 2px">
                      <span class="absolute top-[2px] right-[2px] w-[18px] h-[18px] rounded-full"
                        style="background:#FFFFFF; box-shadow: 0 1px 3px rgba(0,0,0,0.15)" />
                    </span>
                  </label>
                  <span class="text-[10px]" style="color:#9C9895">dis. on</span>
                </div>
              </div>
            </div>

            <!-- Sizes -->
            <div>
              <p class="text-xs mb-3" style="color:#9C9895">Sizes — all in on state</p>
              <div class="flex items-center gap-8">
                <!-- sm: 28×16, thumb 12px -->
                <div class="flex flex-col items-center gap-2">
                  <span class="relative inline-block w-[28px] h-[16px] rounded-full flex-shrink-0"
                    style="background:#6856B2; padding: 2px">
                    <span class="absolute top-[2px] right-[2px] w-[12px] h-[12px] rounded-full"
                      style="background:#FFFFFF; box-shadow: 0 1px 2px rgba(0,0,0,0.18)" />
                  </span>
                  <span class="text-[10px]" style="color:#9C9895">sm 28×16</span>
                </div>
                <!-- md: 36×22, thumb 18px -->
                <div class="flex flex-col items-center gap-2">
                  <span class="relative inline-block w-[36px] h-[22px] rounded-full flex-shrink-0"
                    style="background:#6856B2; padding: 2px">
                    <span class="absolute top-[2px] right-[2px] w-[18px] h-[18px] rounded-full"
                      style="background:#FFFFFF; box-shadow: 0 1px 3px rgba(0,0,0,0.20)" />
                  </span>
                  <span class="text-[10px]" style="color:#9C9895">md 36×22</span>
                </div>
                <!-- lg: 48×28, thumb 24px -->
                <div class="flex flex-col items-center gap-2">
                  <span class="relative inline-block w-[48px] h-[28px] rounded-full flex-shrink-0"
                    style="background:#6856B2; padding: 2px">
                    <span class="absolute top-[2px] right-[2px] w-[24px] h-[24px] rounded-full"
                      style="background:#FFFFFF; box-shadow: 0 1px 4px rgba(0,0,0,0.20)" />
                  </span>
                  <span class="text-[10px]" style="color:#9C9895">lg 48×28</span>
                </div>
              </div>
            </div>

            <!-- Settings group -->
            <div>
              <p class="text-xs mb-3" style="color:#9C9895">Settings row context — label left, switch right</p>
              <ul class="space-y-0">
                <li v-for="(setting, i) in swSettingsLabels" :key="i"
                  class="flex items-center justify-between py-3 gap-4"
                  :class="i < swSettingsLabels.length - 1 ? 'border-b' : ''"
                  :style="{ borderColor: L.borderSubtle }">
                  <div class="flex flex-col gap-0.5 min-w-0">
                    <span class="text-[14px] font-medium truncate" style="color:#1C1C1E">{{ setting.title }}</span>
                    <span class="text-[12px] leading-snug" style="color:#9C9895">{{ setting.helper }}</span>
                  </div>
                  <label class="sw-lt relative inline-flex cursor-pointer flex-shrink-0">
                    <input type="checkbox" class="peer sr-only" v-model="swLt[i]" />
                    <span class="sw-track-lt-peer w-[36px] h-[22px] rounded-full flex-shrink-0 relative"
                      style="padding: 2px">
                      <span class="sw-thumb-lt-peer absolute top-[2px] left-[2px] w-[18px] h-[18px] rounded-full"
                        style="background:#FFFFFF; box-shadow: 0 1px 3px rgba(0,0,0,0.20)" />
                    </span>
                  </label>
                </li>
              </ul>
            </div>
          </div><!-- /light switch -->

          <!-- DARK PANEL switch -->
          <div class="rounded-2xl border p-6 space-y-8" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

            <!-- States dark -->
            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">States — md (36×22px)</p>
              <div class="flex flex-wrap items-center gap-8">
                <div class="flex flex-col items-center gap-2">
                  <label class="sw-dk relative inline-flex cursor-pointer">
                    <input type="checkbox" class="peer sr-only" />
                    <span class="sw-track-dk w-[36px] h-[22px] rounded-full flex-shrink-0 relative" style="padding: 2px; background:#2C2A27">
                      <span class="sw-thumb-dk absolute top-[2px] left-[2px] w-[18px] h-[18px] rounded-full"
                        style="background:#F0EDE9; box-shadow: 0 1px 3px rgba(0,0,0,0.40), 0 1px 2px rgba(0,0,0,0.30)" />
                    </span>
                  </label>
                  <span class="text-[10px]" :style="{ color: D.inkTertiary }">off</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <label class="sw-dk relative inline-flex cursor-pointer">
                    <input type="checkbox" class="peer sr-only" checked />
                    <span class="sw-track-dk w-[36px] h-[22px] rounded-full flex-shrink-0 relative" :style="{ padding: '2px', background: D.accentBold }">
                      <span class="sw-thumb-dk-on absolute top-[2px] right-[2px] w-[18px] h-[18px] rounded-full"
                        style="background:#F0EDE9; box-shadow: 0 1px 3px rgba(0,0,0,0.40)" />
                    </span>
                  </label>
                  <span class="text-[10px]" :style="{ color: D.inkTertiary }">on</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <label class="relative inline-flex cursor-pointer">
                    <input type="checkbox" class="peer sr-only" />
                    <span class="w-[36px] h-[22px] rounded-full flex-shrink-0 relative"
                      style="padding: 2px; background:#2C2A27; box-shadow: 0 0 0 3px rgba(182,168,230,0.30)">
                      <span class="absolute top-[2px] left-[2px] w-[18px] h-[18px] rounded-full"
                        style="background:#F0EDE9; box-shadow: 0 1px 3px rgba(0,0,0,0.40)" />
                    </span>
                  </label>
                  <span class="text-[10px]" :style="{ color: D.inkTertiary }">focused</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <label class="relative inline-flex cursor-not-allowed opacity-40">
                    <input type="checkbox" class="peer sr-only" disabled />
                    <span class="w-[36px] h-[22px] rounded-full flex-shrink-0 relative" style="padding: 2px; background:#2C2A27">
                      <span class="absolute top-[2px] left-[2px] w-[18px] h-[18px] rounded-full" style="background:#F0EDE9" />
                    </span>
                  </label>
                  <span class="text-[10px]" :style="{ color: D.inkTertiary }">disabled</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <label class="relative inline-flex cursor-not-allowed opacity-40">
                    <input type="checkbox" class="peer sr-only" disabled checked />
                    <span class="w-[36px] h-[22px] rounded-full flex-shrink-0 relative" :style="{ padding: '2px', background: D.accentBold }">
                      <span class="absolute top-[2px] right-[2px] w-[18px] h-[18px] rounded-full" style="background:#F0EDE9" />
                    </span>
                  </label>
                  <span class="text-[10px]" :style="{ color: D.inkTertiary }">dis. on</span>
                </div>
              </div>
            </div>

            <!-- Sizes dark -->
            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">Sizes — all in on state</p>
              <div class="flex items-center gap-8">
                <div class="flex flex-col items-center gap-2">
                  <span class="relative inline-block w-[28px] h-[16px] rounded-full" :style="{ background: D.accentBold, padding: '2px' }">
                    <span class="absolute top-[2px] right-[2px] w-[12px] h-[12px] rounded-full" style="background:#F0EDE9; box-shadow: 0 1px 2px rgba(0,0,0,0.30)" />
                  </span>
                  <span class="text-[10px]" :style="{ color: D.inkTertiary }">sm 28×16</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <span class="relative inline-block w-[36px] h-[22px] rounded-full" :style="{ background: D.accentBold, padding: '2px' }">
                    <span class="absolute top-[2px] right-[2px] w-[18px] h-[18px] rounded-full" style="background:#F0EDE9; box-shadow: 0 1px 3px rgba(0,0,0,0.40)" />
                  </span>
                  <span class="text-[10px]" :style="{ color: D.inkTertiary }">md 36×22</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <span class="relative inline-block w-[48px] h-[28px] rounded-full" :style="{ background: D.accentBold, padding: '2px' }">
                    <span class="absolute top-[2px] right-[2px] w-[24px] h-[24px] rounded-full" style="background:#F0EDE9; box-shadow: 0 1px 4px rgba(0,0,0,0.40)" />
                  </span>
                  <span class="text-[10px]" :style="{ color: D.inkTertiary }">lg 48×28</span>
                </div>
              </div>
            </div>

            <!-- Settings group dark -->
            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">Settings row context</p>
              <ul class="space-y-0">
                <li v-for="(setting, i) in swSettingsLabels" :key="i"
                  class="flex items-center justify-between py-3 gap-4"
                  :class="i < swSettingsLabels.length - 1 ? 'border-b' : ''"
                  :style="{ borderColor: D.borderSubtle }">
                  <div class="flex flex-col gap-0.5 min-w-0">
                    <span class="text-[14px] font-medium truncate" :style="{ color: D.inkPrimary }">{{ setting.title }}</span>
                    <span class="text-[12px] leading-snug" :style="{ color: D.inkTertiary }">{{ setting.helper }}</span>
                  </div>
                  <label class="sw-dk relative inline-flex cursor-pointer flex-shrink-0">
                    <input type="checkbox" class="peer sr-only" v-model="swDk[i]" />
                    <span class="sw-track-dk-peer w-[36px] h-[22px] rounded-full flex-shrink-0 relative" style="padding: 2px">
                      <span class="sw-thumb-dk-peer absolute top-[2px] left-[2px] w-[18px] h-[18px] rounded-full"
                        style="background:#F0EDE9; box-shadow: 0 1px 3px rgba(0,0,0,0.40)" />
                    </span>
                  </label>
                </li>
              </ul>
            </div>
          </div><!-- /dark switch -->
        </div>
      </VariantFrame>

      <!-- Switch usage guide -->
      <p class="mt-4 text-body-sm text-ink-secondary px-1">
        Use for any binary on/off setting. Prefer over checkbox for settings contexts where the control represents a stateful mode rather than a completion or selection. The 200ms thumb slide makes the transition feel immediate without being jarring. Reduced-motion environments will see an instant jump instead.
      </p>
    </section>

  </ComponentPage>
</template>

<style scoped>
/*
  ═══════════════════════════════════════════════════════════════
  CHECKBOX A — LIGHT PANEL
  Native input is sr-only. Visual shell uses .cb-a-lt-peer class
  with peer-* modifiers for checked/focus/hover states.
  ═══════════════════════════════════════════════════════════════
*/

/* Unchecked idle shell */
.cb-a-lt-peer {
  border: 1.5px solid #BCB8B2;
  background: #F5F2EE;
  transition: border-color 200ms, background-color 200ms, box-shadow 200ms;
}

/* Hover — border darkens */
.peer:hover + .cb-a-lt-peer,
label:hover .cb-a-lt-peer {
  border-color: #6B6966;
}

/* Focus ring */
.peer:focus-visible + .cb-a-lt-peer {
  border-color: #6856B2;
  box-shadow: 0 0 0 3px rgba(104, 86, 178, 0.25);
}

/* Checked — near-black fill */
.peer:checked + .cb-a-lt-peer {
  background: #1C1C1E;
  border-color: #1C1C1E;
}

/* Indeterminate — same as checked (icon set by template) */
.peer:indeterminate + .cb-a-lt-peer {
  background: #1C1C1E;
  border-color: #1C1C1E;
}

/* Interactive state example (standalone) */
.cb-a-lt:hover .cb-visual-a-lt {
  border-color: #6B6966;
}
.cb-a-lt-chk {
  transition: transform 200ms;
}
.cb-a-lt-chk:hover {
  filter: brightness(1.15);
}


/*
  ═══════════════════════════════════════════════════════════════
  CHECKBOX A — DARK PANEL
  ═══════════════════════════════════════════════════════════════
*/

.cb-a-dk {
  border: 1.5px solid #403E3A;
  background: #242220;
  transition: border-color 200ms, box-shadow 200ms;
}
.cb-a-dk:hover {
  border-color: #5C5A56;
}

.cb-a-dk-peer {
  border: 1.5px solid #403E3A;
  background: #242220;
  transition: border-color 200ms, background-color 200ms, box-shadow 200ms;
}
label:hover .cb-a-dk-peer {
  border-color: #5C5A56;
}
.peer:focus-visible + .cb-a-dk-peer {
  border-color: #B6A8E6;
  box-shadow: 0 0 0 3px rgba(182, 168, 230, 0.30);
}
.peer:checked + .cb-a-dk-peer {
  background: #F0EDE9;
  border-color: #F0EDE9;
}
.peer:indeterminate + .cb-a-dk-peer {
  background: #F0EDE9;
  border-color: #F0EDE9;
}

.cb-a-dk-chk {
  transition: filter 200ms;
}
.cb-a-dk-chk:hover {
  filter: brightness(1.08);
}


/*
  ═══════════════════════════════════════════════════════════════
  RADIO A — LIGHT PANEL
  ═══════════════════════════════════════════════════════════════
*/

.rd-a-lt {
  transition: border-color 200ms, box-shadow 200ms;
}
.rd-a-lt:hover {
  border-color: #6B6966;
}

.rd-a-lt-peer {
  border: 1.5px solid #BCB8B2;
  background: #F5F2EE;
  transition: border-color 200ms, box-shadow 200ms;
}
label:hover .rd-a-lt-peer {
  border-color: #6B6966;
}
.peer:focus-visible + .rd-a-lt-peer {
  border-color: #6856B2;
  box-shadow: 0 0 0 3px rgba(104, 86, 178, 0.25);
}
/* Show inner dot when checked */
.peer:checked + .rd-a-lt-peer > span {
  opacity: 1;
}

.rd-a-lt-chk {
  transition: box-shadow 200ms;
}
.rd-a-lt-chk:hover {
  box-shadow: 0 0 0 2px rgba(28, 28, 30, 0.12);
}


/*
  ═══════════════════════════════════════════════════════════════
  RADIO A — DARK PANEL
  ═══════════════════════════════════════════════════════════════
*/

.rd-a-dk {
  transition: border-color 200ms, box-shadow 200ms;
}
.rd-a-dk:hover {
  border-color: #5C5A56;
}

.rd-a-dk-peer {
  border: 1.5px solid #403E3A;
  background: #242220;
  transition: border-color 200ms, box-shadow 200ms;
}
label:hover .rd-a-dk-peer {
  border-color: #5C5A56;
}
.peer:focus-visible + .rd-a-dk-peer {
  border-color: #B6A8E6;
  box-shadow: 0 0 0 3px rgba(182, 168, 230, 0.30);
}
.peer:checked + .rd-a-dk-peer > span {
  opacity: 1;
}

.rd-a-dk-chk {
  transition: box-shadow 200ms;
}


/*
  ═══════════════════════════════════════════════════════════════
  SWITCH — LIGHT PANEL
  Thumb translates on checked. Track changes bg.
  Using Tailwind peer + scoped CSS for the animated version.
  ═══════════════════════════════════════════════════════════════
*/

/* Animated thumb — light panel */
.sw-thumb-lt {
  transition: transform 200ms cubic-bezier(0.4, 0, 0.2, 1);
}
.sw-thumb-lt-on {
  /* Already right-aligned in markup for the static "on" demo */
}

/* Interactive settings-row switch light */
.sw-track-lt-peer {
  background: #E8E4DF;
  transition: background-color 200ms;
}
.peer:checked + .sw-track-lt-peer {
  background: #6856B2;
}
.sw-thumb-lt-peer {
  transition: transform 200ms cubic-bezier(0.4, 0, 0.2, 1);
}
.peer:checked + .sw-track-lt-peer .sw-thumb-lt-peer {
  transform: translateX(14px);  /* 36 - 18 - 4 (2px padding each side) */
}
/* focus ring on the track wrapper */
.peer:focus-visible + .sw-track-lt-peer {
  box-shadow: 0 0 0 3px rgba(104, 86, 178, 0.25);
}

/* hover: slight track bg lift */
.sw-lt:hover .sw-track-lt {
  filter: brightness(0.94);
}


/*
  ═══════════════════════════════════════════════════════════════
  SWITCH — DARK PANEL
  ═══════════════════════════════════════════════════════════════
*/

.sw-thumb-dk {
  transition: transform 200ms cubic-bezier(0.4, 0, 0.2, 1);
}
.sw-thumb-dk-on {
  /* Static demo — already right-aligned */
}

/* Interactive settings-row switch dark */
.sw-track-dk-peer {
  background: #2C2A27;
  transition: background-color 200ms;
}
.peer:checked + .sw-track-dk-peer {
  background: #B6A8E6;
}
.sw-thumb-dk-peer {
  transition: transform 200ms cubic-bezier(0.4, 0, 0.2, 1);
}
.peer:checked + .sw-track-dk-peer .sw-thumb-dk-peer {
  transform: translateX(14px);
}
.peer:focus-visible + .sw-track-dk-peer {
  box-shadow: 0 0 0 3px rgba(182, 168, 230, 0.30);
}

.sw-dk:hover .sw-track-dk {
  filter: brightness(1.12);
}
</style>
