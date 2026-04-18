<script setup>
import { ref, onUnmounted } from 'vue'
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import {
  SparklesIcon, CheckCircleIcon, InformationCircleIcon,
  ExclamationTriangleIcon, XCircleIcon, XMarkIcon,
} from '@heroicons/vue/24/outline'

// ── Palette ───────────────────────────────────────────────────────────────────
const L = {
  surfaceApp: '#FAF8F5', surfaceRaised: '#FFFFFF', surfaceSunken: '#F5F2EE', surfaceOverlay: '#FFFFFF',
  inkPrimary: '#1C1C1E', inkSecondary: '#6B6966', inkTertiary: '#9C9895', inkInverse: '#FAF8F5',
  borderSubtle: '#E8E4DF', borderStrong: '#BCB8B2',
  status: {
    success: { soft: '#E1F0E7', bold: '#4D8C6A' },
    pending: { soft: '#E2EBF6', bold: '#486E9C' },
    paused:  { soft: '#F5E8D4', bold: '#BE8230' },
    failed:  { soft: '#F4DADA', bold: '#BA4A4A' },
    info:    { soft: '#EAE6F8', bold: '#6856B2' },
    warning: { soft: '#F8ECCF', bold: '#C48C24' },
  },
  accents: { lavender: { soft: '#EAE6F8', bold: '#6856B2' } },
}
const D = {
  surfaceApp: '#141311', surfaceRaised: '#1C1B19', surfaceSunken: '#161513', surfaceOverlay: '#242220',
  inkPrimary: '#F0EDE9', inkSecondary: '#A09C97', inkTertiary: '#6E6B67', inkInverse: '#1C1C1E',
  borderSubtle: '#2C2A27', borderStrong: '#403E3A',
  status: {
    success: { soft: '#1C3A2A', bold: '#6CC498' },
    pending: { soft: '#1E2E42', bold: '#78A4DC' },
    paused:  { soft: '#3C2F14', bold: '#DCA848' },
    failed:  { soft: '#3C1E1E', bold: '#E67070' },
    info:    { soft: '#302A48', bold: '#B6A8E6' },
    warning: { soft: '#3C340E', bold: '#E6C452' },
  },
  accents: { lavender: { soft: '#302A48', bold: '#B6A8E6' } },
}
const SHADOW_TOAST_LT = '0 8px 24px rgba(28,20,10,0.10), 0 2px 4px rgba(28,20,10,0.06)'
const SHADOW_TOAST_DK = '0 8px 24px rgba(0,0,0,0.50), 0 2px 4px rgba(0,0,0,0.30)'

// ── Semantic toast config ─────────────────────────────────────────────────────
const SEMANTICS = [
  { key: 'success',  label: 'Success',  title: 'Task completed',         body: '5 points have been added to your bank.',   action: 'Undo',  Icon: CheckCircleIcon },
  { key: 'info',     label: 'Info',     title: 'Calendar sync finished', body: '12 new events imported from Google.',       action: 'View',  Icon: InformationCircleIcon },
  { key: 'warning',  label: 'Warning',  title: 'Sync partially failed',  body: '3 events could not be imported.',           action: 'Retry', Icon: ExclamationTriangleIcon },
  { key: 'failed',   label: 'Error',    title: 'Action failed',          body: 'Connection timed out. Please try again.',   action: 'Retry', Icon: XCircleIcon },
]

// Map semantic key → palette keys used in L/D
const STATUS_KEY = { success: 'success', info: 'info', warning: 'warning', failed: 'failed' }

// ── Progress bar demo state ───────────────────────────────────────────────────
const progressL = ref(100)
const progressD = ref(100)
const progressRunningL = ref(false)
const progressRunningD = ref(false)
let timerL = null
let timerD = null

function startProgress(which) {
  if (which === 'L') {
    if (progressRunningL.value) return
    progressL.value = 100
    progressRunningL.value = true
    const DURATION = 4000
    const STEP = 50
    timerL = setInterval(() => {
      progressL.value = Math.max(0, progressL.value - (100 / (DURATION / STEP)))
      if (progressL.value <= 0) {
        clearInterval(timerL)
        progressRunningL.value = false
      }
    }, STEP)
  } else {
    if (progressRunningD.value) return
    progressD.value = 100
    progressRunningD.value = true
    const DURATION = 4000
    const STEP = 50
    timerD = setInterval(() => {
      progressD.value = Math.max(0, progressD.value - (100 / (DURATION / STEP)))
      if (progressD.value <= 0) {
        clearInterval(timerD)
        progressRunningD.value = false
      }
    }, STEP)
  }
}

onUnmounted(() => {
  clearInterval(timerL)
  clearInterval(timerD)
})

// ── Inline banner (Variant C) dismiss state ───────────────────────────────────
const bannerDismissed = ref({ L: {}, D: {} })
function dismissBanner(mode, key) {
  bannerDismissed.value[mode] = { ...bannerDismissed.value[mode], [key]: true }
}
function resetBanners() {
  bannerDismissed.value = { L: {}, D: {} }
}

// ── Stacked toast lists ───────────────────────────────────────────────────────
const STACK_TOASTS = [
  { key: 'success', title: 'Chores completed', body: 'Emma earned 10 points.', Icon: CheckCircleIcon },
  { key: 'warning', title: 'Due date today',   body: 'Science project is due tonight.', Icon: ExclamationTriangleIcon },
  { key: 'failed',  title: 'Upload failed',    body: 'Vault document could not be saved.', Icon: XCircleIcon },
]
</script>

<template>
  <ComponentPage
    title="4.8 Toast"
    description="Non-blocking notifications for task completions, point awards, sync results, and errors. Three placement variants cover the full range from glanceable overlay to persistent in-flow confirmation."
    status="scaffolded"
  >

    <!-- ══════════════════════════════════════════════════════════════
         VARIANT A — Bottom-center stack
         Single column, centered, new toasts push older ones up.
         Most thumb-friendly on mobile; least intrusive.
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="A" caption="Bottom-center stack · thumb-friendly · least intrusive">
        <div class="w-full space-y-10">

          <!-- ─── All four semantic variants ──────────────────────── -->
          <div class="space-y-3">
            <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">
              Four semantic variants — Light mode
            </p>
            <!-- Mock viewport frame (light) -->
            <div class="relative rounded-2xl border overflow-hidden"
                 :style="{ background: L.surfaceApp, borderColor: L.borderSubtle, minHeight: '260px' }">
              <!-- Page content stub -->
              <div class="p-4 space-y-2">
                <div class="h-4 w-1/2 rounded" :style="{ background: L.surfaceSunken }" />
                <div class="h-3 w-3/4 rounded" :style="{ background: L.surfaceSunken }" />
                <div class="h-3 w-2/3 rounded" :style="{ background: L.surfaceSunken }" />
              </div>
              <!-- Toast stack (absolute, bottom-center) -->
              <div class="absolute bottom-4 left-0 right-0 flex flex-col items-center gap-2 pointer-events-none px-4">
                <div
                  v-for="s in SEMANTICS"
                  :key="s.key"
                  class="w-full max-w-[360px] rounded-2xl flex overflow-hidden"
                  :style="{ background: L.surfaceOverlay, boxShadow: SHADOW_TOAST_LT, border: `1px solid ${L.borderSubtle}` }"
                >
                  <!-- Left accent bar -->
                  <div class="w-1 flex-shrink-0 self-stretch rounded-l-2xl"
                       :style="{ background: L.status[STATUS_KEY[s.key]].bold }" />
                  <!-- Body -->
                  <div class="flex-1 flex items-start gap-2.5 px-3 py-2.5">
                    <component :is="s.Icon" class="w-5 h-5 flex-shrink-0 mt-0.5"
                               :style="{ color: L.status[STATUS_KEY[s.key]].bold }" />
                    <div class="flex-1 min-w-0">
                      <p class="text-[13px] font-semibold leading-snug" :style="{ color: L.inkPrimary }">{{ s.title }}</p>
                      <p class="text-[12px] mt-0.5 leading-snug" :style="{ color: L.inkSecondary }">{{ s.body }}</p>
                      <button v-if="s.action"
                              class="mt-1.5 text-[12px] font-semibold"
                              :style="{ color: L.status[STATUS_KEY[s.key]].bold }">{{ s.action }}</button>
                    </div>
                    <!-- Close -->
                    <button class="flex-shrink-0 mt-0.5 rounded p-0.5 transition-colors"
                            :style="{ color: L.inkTertiary }">
                      <XMarkIcon class="w-4 h-4" />
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- ─── Same in dark mode ────────────────────────────────── -->
          <div class="space-y-3">
            <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">
              Four semantic variants — Dark mode
            </p>
            <div class="relative rounded-2xl border overflow-hidden"
                 :style="{ background: D.surfaceApp, borderColor: D.borderSubtle, minHeight: '260px' }">
              <div class="p-4 space-y-2">
                <div class="h-4 w-1/2 rounded" :style="{ background: D.surfaceSunken }" />
                <div class="h-3 w-3/4 rounded" :style="{ background: D.surfaceSunken }" />
                <div class="h-3 w-2/3 rounded" :style="{ background: D.surfaceSunken }" />
              </div>
              <div class="absolute bottom-4 left-0 right-0 flex flex-col items-center gap-2 pointer-events-none px-4">
                <div
                  v-for="s in SEMANTICS"
                  :key="s.key"
                  class="w-full max-w-[360px] rounded-2xl flex overflow-hidden"
                  :style="{ background: D.surfaceOverlay, boxShadow: SHADOW_TOAST_DK, border: `1px solid ${D.borderSubtle}` }"
                >
                  <div class="w-1 flex-shrink-0 self-stretch rounded-l-2xl"
                       :style="{ background: D.status[STATUS_KEY[s.key]].bold }" />
                  <div class="flex-1 flex items-start gap-2.5 px-3 py-2.5">
                    <component :is="s.Icon" class="w-5 h-5 flex-shrink-0 mt-0.5"
                               :style="{ color: D.status[STATUS_KEY[s.key]].bold }" />
                    <div class="flex-1 min-w-0">
                      <p class="text-[13px] font-semibold leading-snug" :style="{ color: D.inkPrimary }">{{ s.title }}</p>
                      <p class="text-[12px] mt-0.5 leading-snug" :style="{ color: D.inkSecondary }">{{ s.body }}</p>
                      <button v-if="s.action"
                              class="mt-1.5 text-[12px] font-semibold"
                              :style="{ color: D.status[STATUS_KEY[s.key]].bold }">{{ s.action }}</button>
                    </div>
                    <button class="flex-shrink-0 mt-0.5 rounded p-0.5"
                            :style="{ color: D.inkTertiary }">
                      <XMarkIcon class="w-4 h-4" />
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- ─── Stacked example (3 toasts) ────────────────────────── -->
          <div class="space-y-3">
            <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">
              Stacked — three toasts in sequence (success, warning, error)
            </p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Light stacked -->
              <div class="relative rounded-2xl border overflow-hidden"
                   :style="{ background: L.surfaceApp, borderColor: L.borderSubtle, minHeight: '200px' }">
                <div class="p-4 space-y-2">
                  <div class="h-4 w-1/2 rounded" :style="{ background: L.surfaceSunken }" />
                  <div class="h-3 w-3/4 rounded" :style="{ background: L.surfaceSunken }" />
                </div>
                <div class="absolute bottom-3 left-0 right-0 flex flex-col items-center gap-1.5 px-3">
                  <div
                    v-for="(s, idx) in STACK_TOASTS"
                    :key="s.key"
                    class="w-full max-w-[360px] rounded-2xl flex overflow-hidden"
                    :style="{
                      background: L.surfaceOverlay,
                      boxShadow: SHADOW_TOAST_LT,
                      border: `1px solid ${L.borderSubtle}`,
                      opacity: idx === 0 ? '1' : idx === 1 ? '0.88' : '0.72',
                      transform: `scale(${idx === 0 ? '1' : idx === 1 ? '0.97' : '0.94'})`,
                    }"
                  >
                    <div class="w-1 flex-shrink-0 self-stretch rounded-l-2xl"
                         :style="{ background: L.status[STATUS_KEY[s.key]].bold }" />
                    <div class="flex-1 flex items-center gap-2.5 px-3 py-2">
                      <component :is="s.Icon" class="w-4 h-4 flex-shrink-0"
                                 :style="{ color: L.status[STATUS_KEY[s.key]].bold }" />
                      <div class="flex-1 min-w-0">
                        <p class="text-[12px] font-semibold leading-snug" :style="{ color: L.inkPrimary }">{{ s.title }}</p>
                        <p class="text-[11px] leading-snug" :style="{ color: L.inkSecondary }">{{ s.body }}</p>
                      </div>
                      <button class="flex-shrink-0" :style="{ color: L.inkTertiary }">
                        <XMarkIcon class="w-3.5 h-3.5" />
                      </button>
                    </div>
                  </div>
                </div>
              </div>
              <!-- Dark stacked -->
              <div class="relative rounded-2xl border overflow-hidden"
                   :style="{ background: D.surfaceApp, borderColor: D.borderSubtle, minHeight: '200px' }">
                <div class="p-4 space-y-2">
                  <div class="h-4 w-1/2 rounded" :style="{ background: D.surfaceSunken }" />
                  <div class="h-3 w-3/4 rounded" :style="{ background: D.surfaceSunken }" />
                </div>
                <div class="absolute bottom-3 left-0 right-0 flex flex-col items-center gap-1.5 px-3">
                  <div
                    v-for="(s, idx) in STACK_TOASTS"
                    :key="s.key"
                    class="w-full max-w-[360px] rounded-2xl flex overflow-hidden"
                    :style="{
                      background: D.surfaceOverlay,
                      boxShadow: SHADOW_TOAST_DK,
                      border: `1px solid ${D.borderSubtle}`,
                      opacity: idx === 0 ? '1' : idx === 1 ? '0.88' : '0.72',
                      transform: `scale(${idx === 0 ? '1' : idx === 1 ? '0.97' : '0.94'})`,
                    }"
                  >
                    <div class="w-1 flex-shrink-0 self-stretch rounded-l-2xl"
                         :style="{ background: D.status[STATUS_KEY[s.key]].bold }" />
                    <div class="flex-1 flex items-center gap-2.5 px-3 py-2">
                      <component :is="s.Icon" class="w-4 h-4 flex-shrink-0"
                                 :style="{ color: D.status[STATUS_KEY[s.key]].bold }" />
                      <div class="flex-1 min-w-0">
                        <p class="text-[12px] font-semibold leading-snug" :style="{ color: D.inkPrimary }">{{ s.title }}</p>
                        <p class="text-[11px] leading-snug" :style="{ color: D.inkSecondary }">{{ s.body }}</p>
                      </div>
                      <button class="flex-shrink-0" :style="{ color: D.inkTertiary }">
                        <XMarkIcon class="w-3.5 h-3.5" />
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <p class="text-[11px]" :style="{ color: L.inkTertiary }">
              Oldest toast (bottom) at full opacity and scale. Each subsequent older toast scales to 0.97/0.94 and fades slightly. Max 3 visible; a 4th dismisses the oldest automatically.
            </p>
          </div>

          <!-- ─── Progress bar demo ─────────────────────────────────── -->
          <div class="space-y-3">
            <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">
              With progress bar — timed auto-dismiss (4s countdown)
            </p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Light progress toast -->
              <div class="space-y-2">
                <p class="text-[11px]" :style="{ color: L.inkTertiary }">Light mode</p>
                <div class="rounded-2xl overflow-hidden"
                     :style="{ background: L.surfaceOverlay, boxShadow: SHADOW_TOAST_LT, border: `1px solid ${L.borderSubtle}`, maxWidth: '360px' }">
                  <div class="flex overflow-hidden rounded-t-2xl">
                    <div class="w-1 flex-shrink-0 self-stretch"
                         :style="{ background: L.status.success.bold }" />
                    <div class="flex-1 flex items-start gap-2.5 px-3 pt-2.5 pb-2">
                      <CheckCircleIcon class="w-5 h-5 flex-shrink-0 mt-0.5"
                                       :style="{ color: L.status.success.bold }" />
                      <div class="flex-1 min-w-0">
                        <p class="text-[13px] font-semibold leading-snug" :style="{ color: L.inkPrimary }">Points awarded</p>
                        <p class="text-[12px] mt-0.5 leading-snug" :style="{ color: L.inkSecondary }">Jake earned 15 points for completing chores.</p>
                        <div class="flex items-center justify-between mt-1">
                          <button class="text-[12px] font-semibold" :style="{ color: L.status.success.bold }">Undo</button>
                          <!-- Reduced motion: static label -->
                          <span class="toast-progress-label text-[11px]" :style="{ color: L.inkTertiary }">
                            {{ progressRunningL ? '' : '5s remaining' }}
                          </span>
                        </div>
                      </div>
                      <button class="flex-shrink-0 mt-0.5" :style="{ color: L.inkTertiary }">
                        <XMarkIcon class="w-4 h-4" />
                      </button>
                    </div>
                  </div>
                  <!-- Progress bar strip -->
                  <div class="h-[2px] w-full" :style="{ background: L.borderSubtle }">
                    <div
                      class="toast-progress-bar h-full rounded-full"
                      :style="{
                        width: `${progressL}%`,
                        background: L.status.success.bold,
                        transition: progressRunningL ? 'width 50ms linear' : 'none',
                      }"
                    />
                  </div>
                </div>
                <button
                  class="text-[12px] font-medium px-3 py-1.5 rounded-lg border"
                  :style="{ background: L.surfaceRaised, borderColor: L.borderStrong, color: L.inkSecondary }"
                  @click="startProgress('L')"
                >
                  {{ progressRunningL ? 'Running…' : 'Start 4s demo' }}
                </button>
              </div>

              <!-- Dark progress toast -->
              <div class="space-y-2">
                <p class="text-[11px]" :style="{ color: D.inkTertiary }">Dark mode</p>
                <div class="rounded-2xl overflow-hidden"
                     :style="{ background: D.surfaceOverlay, boxShadow: SHADOW_TOAST_DK, border: `1px solid ${D.borderSubtle}`, maxWidth: '360px' }">
                  <div class="flex overflow-hidden rounded-t-2xl">
                    <div class="w-1 flex-shrink-0 self-stretch"
                         :style="{ background: D.status.success.bold }" />
                    <div class="flex-1 flex items-start gap-2.5 px-3 pt-2.5 pb-2">
                      <CheckCircleIcon class="w-5 h-5 flex-shrink-0 mt-0.5"
                                       :style="{ color: D.status.success.bold }" />
                      <div class="flex-1 min-w-0">
                        <p class="text-[13px] font-semibold leading-snug" :style="{ color: D.inkPrimary }">Points awarded</p>
                        <p class="text-[12px] mt-0.5 leading-snug" :style="{ color: D.inkSecondary }">Jake earned 15 points for completing chores.</p>
                        <div class="flex items-center justify-between mt-1">
                          <button class="text-[12px] font-semibold" :style="{ color: D.status.success.bold }">Undo</button>
                          <span class="toast-progress-label text-[11px]" :style="{ color: D.inkTertiary }">
                            {{ progressRunningD ? '' : '5s remaining' }}
                          </span>
                        </div>
                      </div>
                      <button class="flex-shrink-0 mt-0.5" :style="{ color: D.inkTertiary }">
                        <XMarkIcon class="w-4 h-4" />
                      </button>
                    </div>
                  </div>
                  <div class="h-[2px] w-full" :style="{ background: D.borderSubtle }">
                    <div
                      class="toast-progress-bar h-full rounded-full"
                      :style="{
                        width: `${progressD}%`,
                        background: D.status.success.bold,
                        transition: progressRunningD ? 'width 50ms linear' : 'none',
                      }"
                    />
                  </div>
                </div>
                <button
                  class="text-[12px] font-medium px-3 py-1.5 rounded-lg border"
                  :style="{ background: D.surfaceRaised, borderColor: D.borderStrong, color: D.inkSecondary }"
                  @click="startProgress('D')"
                >
                  {{ progressRunningD ? 'Running…' : 'Start 4s demo' }}
                </button>
              </div>
            </div>
            <p class="text-[11px]" :style="{ color: L.inkTertiary }">
              Progress bar: 2px strip, semantic-bold color, shrinks 100 to 0 over 4s.
              Under <code class="font-mono text-[11px] px-1 py-0.5 rounded" :style="{ background: L.surfaceSunken }">prefers-reduced-motion</code>
              the bar stays static at 100% and shows "5s remaining" text instead.
            </p>
          </div>

          <!-- ─── Mobile 375px mock ──────────────────────────────────── -->
          <div class="space-y-3">
            <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">
              Mobile (375px) mock
            </p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Light mobile -->
              <div class="max-w-[375px]">
                <p class="text-[11px] mb-2" :style="{ color: L.inkTertiary }">Light · 375px</p>
                <div class="relative rounded-2xl border overflow-hidden"
                     :style="{ background: L.surfaceApp, borderColor: L.borderSubtle, height: '220px', width: '375px' }">
                  <div class="p-4 space-y-2">
                    <div class="h-4 w-1/2 rounded" :style="{ background: L.surfaceSunken }" />
                    <div class="h-3 w-3/4 rounded" :style="{ background: L.surfaceSunken }" />
                  </div>
                  <div class="absolute bottom-3 left-3 right-3">
                    <div class="rounded-2xl flex overflow-hidden"
                         :style="{ background: L.surfaceOverlay, boxShadow: SHADOW_TOAST_LT, border: `1px solid ${L.borderSubtle}` }">
                      <div class="w-1 flex-shrink-0 self-stretch rounded-l-2xl"
                           :style="{ background: L.status.success.bold }" />
                      <div class="flex-1 flex items-start gap-2.5 px-3 py-2.5">
                        <CheckCircleIcon class="w-5 h-5 flex-shrink-0 mt-0.5"
                                         :style="{ color: L.status.success.bold }" />
                        <div class="flex-1 min-w-0">
                          <p class="text-[13px] font-semibold" :style="{ color: L.inkPrimary }">Task completed</p>
                          <p class="text-[12px]" :style="{ color: L.inkSecondary }">5 points added to your bank.</p>
                          <button class="mt-1 text-[12px] font-semibold" :style="{ color: L.status.success.bold }">Undo</button>
                        </div>
                        <button :style="{ color: L.inkTertiary }"><XMarkIcon class="w-4 h-4" /></button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!-- Dark mobile -->
              <div class="max-w-[375px]">
                <p class="text-[11px] mb-2" :style="{ color: D.inkTertiary }">Dark · 375px</p>
                <div class="relative rounded-2xl border overflow-hidden"
                     :style="{ background: D.surfaceApp, borderColor: D.borderSubtle, height: '220px', width: '375px' }">
                  <div class="p-4 space-y-2">
                    <div class="h-4 w-1/2 rounded" :style="{ background: D.surfaceSunken }" />
                    <div class="h-3 w-3/4 rounded" :style="{ background: D.surfaceSunken }" />
                  </div>
                  <div class="absolute bottom-3 left-3 right-3">
                    <div class="rounded-2xl flex overflow-hidden"
                         :style="{ background: D.surfaceOverlay, boxShadow: SHADOW_TOAST_DK, border: `1px solid ${D.borderSubtle}` }">
                      <div class="w-1 flex-shrink-0 self-stretch rounded-l-2xl"
                           :style="{ background: D.status.success.bold }" />
                      <div class="flex-1 flex items-start gap-2.5 px-3 py-2.5">
                        <CheckCircleIcon class="w-5 h-5 flex-shrink-0 mt-0.5"
                                         :style="{ color: D.status.success.bold }" />
                        <div class="flex-1 min-w-0">
                          <p class="text-[13px] font-semibold" :style="{ color: D.inkPrimary }">Task completed</p>
                          <p class="text-[12px]" :style="{ color: D.inkSecondary }">5 points added to your bank.</p>
                          <button class="mt-1 text-[12px] font-semibold" :style="{ color: D.status.success.bold }">Undo</button>
                        </div>
                        <button :style="{ color: D.inkTertiary }"><XMarkIcon class="w-4 h-4" /></button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- ─── Desktop 1024px mock ────────────────────────────────── -->
          <div class="space-y-3">
            <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">
              Desktop (1024px) mock — bottom-center toast still centered
            </p>
            <div class="space-y-4">
              <!-- Light desktop -->
              <div>
                <p class="text-[11px] mb-2" :style="{ color: L.inkTertiary }">Light · 1024px (scaled)</p>
                <div class="relative rounded-2xl border overflow-hidden"
                     :style="{ background: L.surfaceApp, borderColor: L.borderSubtle, height: '120px' }">
                  <div class="absolute inset-0 flex items-center justify-center">
                    <div class="text-[11px]" :style="{ color: L.inkTertiary }">Page content</div>
                  </div>
                  <div class="absolute bottom-3 left-0 right-0 flex justify-center px-4">
                    <div class="rounded-2xl flex overflow-hidden"
                         :style="{ background: L.surfaceOverlay, boxShadow: SHADOW_TOAST_LT, border: `1px solid ${L.borderSubtle}`, maxWidth: '420px', width: '100%' }">
                      <div class="w-1 flex-shrink-0 self-stretch rounded-l-2xl"
                           :style="{ background: L.status.info.bold }" />
                      <div class="flex-1 flex items-center gap-2.5 px-3 py-2.5">
                        <InformationCircleIcon class="w-5 h-5 flex-shrink-0"
                                               :style="{ color: L.status.info.bold }" />
                        <div class="flex-1 min-w-0">
                          <p class="text-[13px] font-semibold" :style="{ color: L.inkPrimary }">Calendar sync finished</p>
                          <p class="text-[12px]" :style="{ color: L.inkSecondary }">12 new events imported from Google.</p>
                        </div>
                        <button class="text-[12px] font-semibold mr-2" :style="{ color: L.status.info.bold }">View</button>
                        <button :style="{ color: L.inkTertiary }"><XMarkIcon class="w-4 h-4" /></button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!-- Dark desktop -->
              <div>
                <p class="text-[11px] mb-2" :style="{ color: D.inkTertiary }">Dark · 1024px (scaled)</p>
                <div class="relative rounded-2xl border overflow-hidden"
                     :style="{ background: D.surfaceApp, borderColor: D.borderSubtle, height: '120px' }">
                  <div class="absolute inset-0 flex items-center justify-center">
                    <div class="text-[11px]" :style="{ color: D.inkTertiary }">Page content</div>
                  </div>
                  <div class="absolute bottom-3 left-0 right-0 flex justify-center px-4">
                    <div class="rounded-2xl flex overflow-hidden"
                         :style="{ background: D.surfaceOverlay, boxShadow: SHADOW_TOAST_DK, border: `1px solid ${D.borderSubtle}`, maxWidth: '420px', width: '100%' }">
                      <div class="w-1 flex-shrink-0 self-stretch rounded-l-2xl"
                           :style="{ background: D.status.info.bold }" />
                      <div class="flex-1 flex items-center gap-2.5 px-3 py-2.5">
                        <InformationCircleIcon class="w-5 h-5 flex-shrink-0"
                                               :style="{ color: D.status.info.bold }" />
                        <div class="flex-1 min-w-0">
                          <p class="text-[13px] font-semibold" :style="{ color: D.inkPrimary }">Calendar sync finished</p>
                          <p class="text-[12px]" :style="{ color: D.inkSecondary }">12 new events imported from Google.</p>
                        </div>
                        <button class="text-[12px] font-semibold mr-2" :style="{ color: D.status.info.bold }">View</button>
                        <button :style="{ color: D.inkTertiary }"><XMarkIcon class="w-4 h-4" /></button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>
      </VariantFrame>
      <p class="mt-3 text-sm px-1" :style="{ color: L.inkSecondary }">
        Variant A sits at the bottom-center on all breakpoints, making it the most reachable on mobile (near the thumb) while remaining unobtrusive on desktop. The centered column does not conflict with bottom navigation bars when one is present.
      </p>
    </section>


    <!-- ══════════════════════════════════════════════════════════════
         VARIANT B — Top-right on desktop · top-center on mobile
         Classic notification corner. Responsive: top-right ≥ 1024px,
         top-center < 1024px.
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="B" caption="Top-right desktop · top-center mobile · responsive position">
        <div class="w-full space-y-10">

          <!-- ─── All four semantic variants ──────────────────────── -->
          <div class="space-y-3">
            <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">
              Four semantic variants
            </p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Light -->
              <div>
                <p class="text-[11px] mb-2" :style="{ color: L.inkTertiary }">Light mode</p>
                <div class="relative rounded-2xl border overflow-hidden"
                     :style="{ background: L.surfaceApp, borderColor: L.borderSubtle, minHeight: '280px' }">
                  <div class="p-4 space-y-2">
                    <div class="h-4 w-1/2 rounded" :style="{ background: L.surfaceSunken }" />
                    <div class="h-3 w-3/4 rounded" :style="{ background: L.surfaceSunken }" />
                    <div class="h-3 w-2/3 rounded" :style="{ background: L.surfaceSunken }" />
                  </div>
                  <!-- Top-right stack (desktop position) -->
                  <div class="absolute top-3 right-3 flex flex-col gap-2 w-[220px]">
                    <div
                      v-for="s in SEMANTICS"
                      :key="s.key"
                      class="rounded-2xl flex overflow-hidden"
                      :style="{ background: L.surfaceOverlay, boxShadow: SHADOW_TOAST_LT, border: `1px solid ${L.borderSubtle}` }"
                    >
                      <div class="w-1 flex-shrink-0 self-stretch rounded-l-2xl"
                           :style="{ background: L.status[STATUS_KEY[s.key]].bold }" />
                      <div class="flex-1 flex items-start gap-2 px-2.5 py-2">
                        <component :is="s.Icon" class="w-4 h-4 flex-shrink-0 mt-0.5"
                                   :style="{ color: L.status[STATUS_KEY[s.key]].bold }" />
                        <div class="flex-1 min-w-0">
                          <p class="text-[12px] font-semibold leading-snug" :style="{ color: L.inkPrimary }">{{ s.title }}</p>
                          <p class="text-[11px] mt-0.5 leading-snug" :style="{ color: L.inkSecondary }">{{ s.body }}</p>
                          <button v-if="s.action" class="mt-1 text-[11px] font-semibold"
                                  :style="{ color: L.status[STATUS_KEY[s.key]].bold }">{{ s.action }}</button>
                        </div>
                        <button class="flex-shrink-0 mt-0.5" :style="{ color: L.inkTertiary }">
                          <XMarkIcon class="w-3.5 h-3.5" />
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!-- Dark -->
              <div>
                <p class="text-[11px] mb-2" :style="{ color: D.inkTertiary }">Dark mode</p>
                <div class="relative rounded-2xl border overflow-hidden"
                     :style="{ background: D.surfaceApp, borderColor: D.borderSubtle, minHeight: '280px' }">
                  <div class="p-4 space-y-2">
                    <div class="h-4 w-1/2 rounded" :style="{ background: D.surfaceSunken }" />
                    <div class="h-3 w-3/4 rounded" :style="{ background: D.surfaceSunken }" />
                    <div class="h-3 w-2/3 rounded" :style="{ background: D.surfaceSunken }" />
                  </div>
                  <div class="absolute top-3 right-3 flex flex-col gap-2 w-[220px]">
                    <div
                      v-for="s in SEMANTICS"
                      :key="s.key"
                      class="rounded-2xl flex overflow-hidden"
                      :style="{ background: D.surfaceOverlay, boxShadow: SHADOW_TOAST_DK, border: `1px solid ${D.borderSubtle}` }"
                    >
                      <div class="w-1 flex-shrink-0 self-stretch rounded-l-2xl"
                           :style="{ background: D.status[STATUS_KEY[s.key]].bold }" />
                      <div class="flex-1 flex items-start gap-2 px-2.5 py-2">
                        <component :is="s.Icon" class="w-4 h-4 flex-shrink-0 mt-0.5"
                                   :style="{ color: D.status[STATUS_KEY[s.key]].bold }" />
                        <div class="flex-1 min-w-0">
                          <p class="text-[12px] font-semibold leading-snug" :style="{ color: D.inkPrimary }">{{ s.title }}</p>
                          <p class="text-[11px] mt-0.5 leading-snug" :style="{ color: D.inkSecondary }">{{ s.body }}</p>
                          <button v-if="s.action" class="mt-1 text-[11px] font-semibold"
                                  :style="{ color: D.status[STATUS_KEY[s.key]].bold }">{{ s.action }}</button>
                        </div>
                        <button class="flex-shrink-0 mt-0.5" :style="{ color: D.inkTertiary }">
                          <XMarkIcon class="w-3.5 h-3.5" />
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- ─── Mobile top-center mock ────────────────────────────── -->
          <div class="space-y-3">
            <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">
              Mobile (375px) · top-center
            </p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Light mobile B -->
              <div class="max-w-[375px]">
                <p class="text-[11px] mb-2" :style="{ color: L.inkTertiary }">Light</p>
                <div class="relative rounded-2xl border overflow-hidden"
                     :style="{ background: L.surfaceApp, borderColor: L.borderSubtle, height: '200px', width: '375px' }">
                  <div class="absolute top-3 left-3 right-3 flex flex-col items-center gap-2">
                    <div class="w-full rounded-2xl flex overflow-hidden"
                         :style="{ background: L.surfaceOverlay, boxShadow: SHADOW_TOAST_LT, border: `1px solid ${L.borderSubtle}` }">
                      <div class="w-1 flex-shrink-0 self-stretch rounded-l-2xl"
                           :style="{ background: L.status.warning.bold }" />
                      <div class="flex-1 flex items-start gap-2.5 px-3 py-2.5">
                        <ExclamationTriangleIcon class="w-5 h-5 flex-shrink-0 mt-0.5"
                                                 :style="{ color: L.status.warning.bold }" />
                        <div class="flex-1">
                          <p class="text-[13px] font-semibold" :style="{ color: L.inkPrimary }">Sync partially failed</p>
                          <p class="text-[12px]" :style="{ color: L.inkSecondary }">3 events could not be imported.</p>
                          <button class="mt-1 text-[12px] font-semibold" :style="{ color: L.status.warning.bold }">Retry</button>
                        </div>
                        <button :style="{ color: L.inkTertiary }"><XMarkIcon class="w-4 h-4" /></button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!-- Dark mobile B -->
              <div class="max-w-[375px]">
                <p class="text-[11px] mb-2" :style="{ color: D.inkTertiary }">Dark</p>
                <div class="relative rounded-2xl border overflow-hidden"
                     :style="{ background: D.surfaceApp, borderColor: D.borderSubtle, height: '200px', width: '375px' }">
                  <div class="absolute top-3 left-3 right-3 flex flex-col items-center gap-2">
                    <div class="w-full rounded-2xl flex overflow-hidden"
                         :style="{ background: D.surfaceOverlay, boxShadow: SHADOW_TOAST_DK, border: `1px solid ${D.borderSubtle}` }">
                      <div class="w-1 flex-shrink-0 self-stretch rounded-l-2xl"
                           :style="{ background: D.status.warning.bold }" />
                      <div class="flex-1 flex items-start gap-2.5 px-3 py-2.5">
                        <ExclamationTriangleIcon class="w-5 h-5 flex-shrink-0 mt-0.5"
                                                 :style="{ color: D.status.warning.bold }" />
                        <div class="flex-1">
                          <p class="text-[13px] font-semibold" :style="{ color: D.inkPrimary }">Sync partially failed</p>
                          <p class="text-[12px]" :style="{ color: D.inkSecondary }">3 events could not be imported.</p>
                          <button class="mt-1 text-[12px] font-semibold" :style="{ color: D.status.warning.bold }">Retry</button>
                        </div>
                        <button :style="{ color: D.inkTertiary }"><XMarkIcon class="w-4 h-4" /></button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <p class="text-[11px]" :style="{ color: L.inkTertiary }">
              Mobile shifts from right-edge to top-center to avoid the thumb-unfriendly top-right corner. Desktop retains the muscle-memory position used by OS notification systems.
            </p>
          </div>

          <!-- ─── Stacked 3 (top-right) ──────────────────────────────── -->
          <div class="space-y-3">
            <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">
              Stacked — three toasts, top-right, desktop
            </p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Light -->
              <div>
                <p class="text-[11px] mb-2" :style="{ color: L.inkTertiary }">Light</p>
                <div class="relative rounded-2xl border overflow-hidden"
                     :style="{ background: L.surfaceApp, borderColor: L.borderSubtle, minHeight: '220px' }">
                  <div class="p-4 space-y-2">
                    <div class="h-4 w-1/2 rounded" :style="{ background: L.surfaceSunken }" />
                  </div>
                  <div class="absolute top-3 right-3 flex flex-col gap-1.5 w-[200px]">
                    <div
                      v-for="(s, idx) in STACK_TOASTS"
                      :key="s.key"
                      class="rounded-2xl flex overflow-hidden"
                      :style="{
                        background: L.surfaceOverlay,
                        boxShadow: SHADOW_TOAST_LT,
                        border: `1px solid ${L.borderSubtle}`,
                        opacity: idx === 0 ? '1' : idx === 1 ? '0.88' : '0.72',
                      }"
                    >
                      <div class="w-1 flex-shrink-0 self-stretch rounded-l-2xl"
                           :style="{ background: L.status[STATUS_KEY[s.key]].bold }" />
                      <div class="flex-1 flex items-center gap-2 px-2.5 py-2">
                        <component :is="s.Icon" class="w-4 h-4 flex-shrink-0"
                                   :style="{ color: L.status[STATUS_KEY[s.key]].bold }" />
                        <p class="flex-1 text-[11px] font-semibold" :style="{ color: L.inkPrimary }">{{ s.title }}</p>
                        <button :style="{ color: L.inkTertiary }"><XMarkIcon class="w-3.5 h-3.5" /></button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!-- Dark -->
              <div>
                <p class="text-[11px] mb-2" :style="{ color: D.inkTertiary }">Dark</p>
                <div class="relative rounded-2xl border overflow-hidden"
                     :style="{ background: D.surfaceApp, borderColor: D.borderSubtle, minHeight: '220px' }">
                  <div class="p-4 space-y-2">
                    <div class="h-4 w-1/2 rounded" :style="{ background: D.surfaceSunken }" />
                  </div>
                  <div class="absolute top-3 right-3 flex flex-col gap-1.5 w-[200px]">
                    <div
                      v-for="(s, idx) in STACK_TOASTS"
                      :key="s.key"
                      class="rounded-2xl flex overflow-hidden"
                      :style="{
                        background: D.surfaceOverlay,
                        boxShadow: SHADOW_TOAST_DK,
                        border: `1px solid ${D.borderSubtle}`,
                        opacity: idx === 0 ? '1' : idx === 1 ? '0.88' : '0.72',
                      }"
                    >
                      <div class="w-1 flex-shrink-0 self-stretch rounded-l-2xl"
                           :style="{ background: D.status[STATUS_KEY[s.key]].bold }" />
                      <div class="flex-1 flex items-center gap-2 px-2.5 py-2">
                        <component :is="s.Icon" class="w-4 h-4 flex-shrink-0"
                                   :style="{ color: D.status[STATUS_KEY[s.key]].bold }" />
                        <p class="flex-1 text-[11px] font-semibold" :style="{ color: D.inkPrimary }">{{ s.title }}</p>
                        <button :style="{ color: D.inkTertiary }"><XMarkIcon class="w-3.5 h-3.5" /></button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- ─── Progress bar (Variant B) ─────────────────────────── -->
          <div class="space-y-3">
            <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">
              With progress bar — top-right desktop
            </p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <p class="text-[11px] mb-2" :style="{ color: L.inkTertiary }">Light</p>
                <div class="rounded-2xl overflow-hidden" style="max-width: 280px"
                     :style="{ background: L.surfaceOverlay, boxShadow: SHADOW_TOAST_LT, border: `1px solid ${L.borderSubtle}` }">
                  <div class="flex">
                    <div class="w-1 flex-shrink-0 self-stretch"
                         :style="{ background: L.status.failed.bold }" />
                    <div class="flex-1 flex items-start gap-2 px-3 pt-2.5 pb-2">
                      <XCircleIcon class="w-4 h-4 flex-shrink-0 mt-0.5" :style="{ color: L.status.failed.bold }" />
                      <div class="flex-1 min-w-0">
                        <p class="text-[12px] font-semibold" :style="{ color: L.inkPrimary }">Action failed</p>
                        <p class="text-[11px] mt-0.5" :style="{ color: L.inkSecondary }">Retrying in 5 seconds...</p>
                        <button class="mt-1 text-[11px] font-semibold" :style="{ color: L.status.failed.bold }">Retry now</button>
                      </div>
                      <button :style="{ color: L.inkTertiary }"><XMarkIcon class="w-3.5 h-3.5" /></button>
                    </div>
                  </div>
                  <div class="h-[2px] w-full" :style="{ background: L.borderSubtle }">
                    <div class="toast-progress-bar-b h-full w-[60%] rounded-full"
                         :style="{ background: L.status.failed.bold }" />
                  </div>
                </div>
              </div>
              <div>
                <p class="text-[11px] mb-2" :style="{ color: D.inkTertiary }">Dark</p>
                <div class="rounded-2xl overflow-hidden" style="max-width: 280px"
                     :style="{ background: D.surfaceOverlay, boxShadow: SHADOW_TOAST_DK, border: `1px solid ${D.borderSubtle}` }">
                  <div class="flex">
                    <div class="w-1 flex-shrink-0 self-stretch"
                         :style="{ background: D.status.failed.bold }" />
                    <div class="flex-1 flex items-start gap-2 px-3 pt-2.5 pb-2">
                      <XCircleIcon class="w-4 h-4 flex-shrink-0 mt-0.5" :style="{ color: D.status.failed.bold }" />
                      <div class="flex-1 min-w-0">
                        <p class="text-[12px] font-semibold" :style="{ color: D.inkPrimary }">Action failed</p>
                        <p class="text-[11px] mt-0.5" :style="{ color: D.inkSecondary }">Retrying in 5 seconds...</p>
                        <button class="mt-1 text-[11px] font-semibold" :style="{ color: D.status.failed.bold }">Retry now</button>
                      </div>
                      <button :style="{ color: D.inkTertiary }"><XMarkIcon class="w-3.5 h-3.5" /></button>
                    </div>
                  </div>
                  <div class="h-[2px] w-full" :style="{ background: D.borderSubtle }">
                    <div class="toast-progress-bar-b h-full w-[60%] rounded-full"
                         :style="{ background: D.status.failed.bold }" />
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>
      </VariantFrame>
      <p class="mt-3 text-sm px-1" :style="{ color: L.inkSecondary }">
        Variant B matches the desktop notification corner users already know from macOS and Windows. The responsive reposition to top-center on mobile avoids the notoriously hard-to-reach top-right corner of tall phones.
      </p>
    </section>


    <!-- ══════════════════════════════════════════════════════════════
         VARIANT C — Inline banner
         Full-width, in-flow, persistent, user-dismissable.
         Best for in-flow confirmations (form saved, settings updated).
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="C" caption="Inline banner · full-width · persistent · user-dismissable">
        <div class="w-full space-y-10">

          <!-- ─── All four semantic variants ──────────────────────── -->
          <div class="space-y-4">
            <div class="flex items-center justify-between">
              <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">
                Four semantic variants — Light mode
              </p>
              <button
                class="text-[11px] font-medium px-2 py-1 rounded-lg border"
                :style="{ background: L.surfaceRaised, borderColor: L.borderStrong, color: L.inkSecondary }"
                @click="resetBanners"
              >Reset dismissed</button>
            </div>

            <!-- Light banners -->
            <div class="rounded-2xl border p-4 space-y-2"
                 :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
              <template v-for="s in SEMANTICS" :key="s.key">
                <div
                  v-if="!bannerDismissed.L[s.key]"
                  class="rounded-xl flex overflow-hidden"
                  :style="{ background: L.status[STATUS_KEY[s.key]].soft, border: `1px solid ${L.status[STATUS_KEY[s.key]].bold}` }"
                >
                  <div class="w-1 flex-shrink-0 self-stretch"
                       :style="{ background: L.status[STATUS_KEY[s.key]].bold }" />
                  <div class="flex-1 flex items-center gap-3 px-4 py-3">
                    <component :is="s.Icon" class="w-5 h-5 flex-shrink-0"
                               :style="{ color: L.status[STATUS_KEY[s.key]].bold }" />
                    <div class="flex-1 min-w-0">
                      <span class="text-[13px] font-semibold" :style="{ color: L.inkPrimary }">{{ s.title }}</span>
                      <span class="text-[13px] ml-1.5" :style="{ color: L.inkSecondary }">{{ s.body }}</span>
                    </div>
                    <button v-if="s.action"
                            class="text-[12px] font-semibold flex-shrink-0 mr-3"
                            :style="{ color: L.status[STATUS_KEY[s.key]].bold }">{{ s.action }}</button>
                    <button
                      class="flex-shrink-0 rounded p-0.5"
                      :style="{ color: L.status[STATUS_KEY[s.key]].bold }"
                      @click="dismissBanner('L', s.key)"
                    >
                      <XMarkIcon class="w-4 h-4" />
                    </button>
                  </div>
                </div>
              </template>
            </div>

            <!-- Dark banners -->
            <p class="text-[11px] font-semibold uppercase tracking-widest mt-4" :style="{ color: D.inkTertiary }">
              Dark mode
            </p>
            <div class="rounded-2xl border p-4 space-y-2"
                 :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
              <template v-for="s in SEMANTICS" :key="s.key">
                <div
                  v-if="!bannerDismissed.D[s.key]"
                  class="rounded-xl flex overflow-hidden"
                  :style="{ background: D.status[STATUS_KEY[s.key]].soft, border: `1px solid ${D.status[STATUS_KEY[s.key]].bold}` }"
                >
                  <div class="w-1 flex-shrink-0 self-stretch"
                       :style="{ background: D.status[STATUS_KEY[s.key]].bold }" />
                  <div class="flex-1 flex items-center gap-3 px-4 py-3">
                    <component :is="s.Icon" class="w-5 h-5 flex-shrink-0"
                               :style="{ color: D.status[STATUS_KEY[s.key]].bold }" />
                    <div class="flex-1 min-w-0">
                      <span class="text-[13px] font-semibold" :style="{ color: D.inkPrimary }">{{ s.title }}</span>
                      <span class="text-[13px] ml-1.5" :style="{ color: D.inkSecondary }">{{ s.body }}</span>
                    </div>
                    <button v-if="s.action"
                            class="text-[12px] font-semibold flex-shrink-0 mr-3"
                            :style="{ color: D.status[STATUS_KEY[s.key]].bold }">{{ s.action }}</button>
                    <button
                      class="flex-shrink-0 rounded p-0.5"
                      :style="{ color: D.status[STATUS_KEY[s.key]].bold }"
                      @click="dismissBanner('D', s.key)"
                    >
                      <XMarkIcon class="w-4 h-4" />
                    </button>
                  </div>
                </div>
              </template>
            </div>
          </div>

          <!-- ─── Stacked example ──────────────────────────────────── -->
          <div class="space-y-3">
            <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">
              Stacked inline — three banners in sequence
            </p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Light stacked banners -->
              <div class="space-y-2 rounded-2xl border p-4"
                   :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
                <div
                  v-for="s in STACK_TOASTS"
                  :key="s.key"
                  class="rounded-xl flex overflow-hidden"
                  :style="{ background: L.status[STATUS_KEY[s.key]].soft, border: `1px solid ${L.status[STATUS_KEY[s.key]].bold}` }"
                >
                  <div class="w-1 flex-shrink-0 self-stretch"
                       :style="{ background: L.status[STATUS_KEY[s.key]].bold }" />
                  <div class="flex-1 flex items-center gap-3 px-3 py-2.5">
                    <component :is="s.Icon" class="w-4 h-4 flex-shrink-0"
                               :style="{ color: L.status[STATUS_KEY[s.key]].bold }" />
                    <div class="flex-1 min-w-0">
                      <span class="text-[12px] font-semibold" :style="{ color: L.inkPrimary }">{{ s.title }}</span>
                      <span class="text-[12px] ml-1" :style="{ color: L.inkSecondary }">{{ s.body }}</span>
                    </div>
                    <button :style="{ color: L.status[STATUS_KEY[s.key]].bold }">
                      <XMarkIcon class="w-4 h-4" />
                    </button>
                  </div>
                </div>
              </div>
              <!-- Dark stacked banners -->
              <div class="space-y-2 rounded-2xl border p-4"
                   :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
                <div
                  v-for="s in STACK_TOASTS"
                  :key="s.key"
                  class="rounded-xl flex overflow-hidden"
                  :style="{ background: D.status[STATUS_KEY[s.key]].soft, border: `1px solid ${D.status[STATUS_KEY[s.key]].bold}` }"
                >
                  <div class="w-1 flex-shrink-0 self-stretch"
                       :style="{ background: D.status[STATUS_KEY[s.key]].bold }" />
                  <div class="flex-1 flex items-center gap-3 px-3 py-2.5">
                    <component :is="s.Icon" class="w-4 h-4 flex-shrink-0"
                               :style="{ color: D.status[STATUS_KEY[s.key]].bold }" />
                    <div class="flex-1 min-w-0">
                      <span class="text-[12px] font-semibold" :style="{ color: D.inkPrimary }">{{ s.title }}</span>
                      <span class="text-[12px] ml-1" :style="{ color: D.inkSecondary }">{{ s.body }}</span>
                    </div>
                    <button :style="{ color: D.status[STATUS_KEY[s.key]].bold }">
                      <XMarkIcon class="w-4 h-4" />
                    </button>
                  </div>
                </div>
              </div>
            </div>
            <p class="text-[11px]" :style="{ color: L.inkTertiary }">
              Banners stack vertically in document flow — no z-index juggling. Unlike floating toasts, the page height shifts as banners are added or dismissed.
            </p>
          </div>

          <!-- ─── Mobile 375px / Desktop 1024px mocks ────────────────── -->
          <div class="space-y-3">
            <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">
              Mobile (375px) and Desktop mocks
            </p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Light mobile inline banner -->
              <div>
                <p class="text-[11px] mb-2" :style="{ color: L.inkTertiary }">Light · 375px</p>
                <div class="rounded-2xl border overflow-hidden"
                     :style="{ background: L.surfaceApp, borderColor: L.borderSubtle, width: '375px', maxWidth: '100%' }">
                  <div
                    class="rounded-t-xl flex overflow-hidden"
                    :style="{ background: L.status.success.soft, border: `1px solid ${L.status.success.bold}` }"
                  >
                    <div class="w-1 flex-shrink-0 self-stretch"
                         :style="{ background: L.status.success.bold }" />
                    <div class="flex-1 flex items-center gap-3 px-3 py-3">
                      <CheckCircleIcon class="w-5 h-5 flex-shrink-0" :style="{ color: L.status.success.bold }" />
                      <div class="flex-1 min-w-0">
                        <p class="text-[13px] font-semibold" :style="{ color: L.inkPrimary }">Settings saved</p>
                        <p class="text-[12px]" :style="{ color: L.inkSecondary }">Your preferences have been updated.</p>
                      </div>
                      <button :style="{ color: L.status.success.bold }"><XMarkIcon class="w-4 h-4" /></button>
                    </div>
                  </div>
                  <div class="p-4 space-y-2">
                    <div class="h-4 w-3/4 rounded" :style="{ background: L.surfaceSunken }" />
                    <div class="h-3 w-full rounded" :style="{ background: L.surfaceSunken }" />
                    <div class="h-3 w-2/3 rounded" :style="{ background: L.surfaceSunken }" />
                  </div>
                </div>
              </div>
              <!-- Dark desktop inline banner -->
              <div>
                <p class="text-[11px] mb-2" :style="{ color: D.inkTertiary }">Dark · 1024px</p>
                <div class="rounded-2xl border overflow-hidden"
                     :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
                  <div
                    class="rounded-t-xl flex overflow-hidden"
                    :style="{ background: D.status.success.soft, border: `1px solid ${D.status.success.bold}` }"
                  >
                    <div class="w-1 flex-shrink-0 self-stretch"
                         :style="{ background: D.status.success.bold }" />
                    <div class="flex-1 flex items-center gap-4 px-4 py-3">
                      <CheckCircleIcon class="w-5 h-5 flex-shrink-0" :style="{ color: D.status.success.bold }" />
                      <div class="flex-1 min-w-0">
                        <span class="text-[13px] font-semibold" :style="{ color: D.inkPrimary }">Settings saved.</span>
                        <span class="text-[13px] ml-2" :style="{ color: D.inkSecondary }">Your family preferences have been updated.</span>
                      </div>
                      <button class="text-[12px] font-semibold flex-shrink-0 mr-3" :style="{ color: D.status.success.bold }">View</button>
                      <button :style="{ color: D.status.success.bold }"><XMarkIcon class="w-4 h-4" /></button>
                    </div>
                  </div>
                  <div class="p-4 space-y-2">
                    <div class="h-4 w-1/2 rounded" :style="{ background: D.surfaceSunken }" />
                    <div class="h-3 w-3/4 rounded" :style="{ background: D.surfaceSunken }" />
                    <div class="h-3 w-2/3 rounded" :style="{ background: D.surfaceSunken }" />
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- ─── Banner with progress note ────────────────────────── -->
          <div class="space-y-3">
            <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">
              Inline banner — no auto-dismiss (no progress bar)
            </p>
            <div class="rounded-xl flex overflow-hidden"
                 :style="{ background: L.status.info.soft, border: `1px solid ${L.status.info.bold}`, maxWidth: '600px' }">
              <div class="w-1 flex-shrink-0 self-stretch"
                   :style="{ background: L.status.info.bold }" />
              <div class="flex-1 flex items-center gap-3 px-4 py-3">
                <InformationCircleIcon class="w-5 h-5 flex-shrink-0" :style="{ color: L.status.info.bold }" />
                <p class="flex-1 text-[13px]" :style="{ color: L.inkPrimary }">
                  <strong>Inline banners do not auto-dismiss.</strong> They stay visible until the user clicks X. No progress bar is shown — the persistent nature is the signal.
                </p>
              </div>
            </div>
          </div>

        </div>
      </VariantFrame>
      <p class="mt-3 text-sm px-1" :style="{ color: L.inkSecondary }">
        Variant C is the right choice when confirmation needs to stay visible while the user acts — e.g., a form submitted successfully, a vault entry saved, or an error that requires reading before retrying. It does not auto-dismiss; the soft tinted background is calm enough to not demand attention but clear enough to not be missed.
      </p>
    </section>


    <!-- ══════════════════════════════════════════════════════════════
         CLAUDE'S PICK CALLOUT
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-8">
      <div class="rounded-2xl border p-6 space-y-3"
           :style="{ background: L.accents.lavender.soft, borderColor: L.accents.lavender.bold }">
        <div class="flex items-center gap-2">
          <SparklesIcon class="w-5 h-5" :style="{ color: L.accents.lavender.bold }" />
          <h2 class="text-[15px] font-semibold" :style="{ color: L.accents.lavender.bold }">Claude's pick — Variant A</h2>
        </div>
        <p class="text-[14px] leading-relaxed" :style="{ color: L.inkPrimary }">
          Variant A (bottom-center stack) is the strongest default for Kinhold's family audience. Mobile is the primary form factor, and the bottom-center position places toasts within natural thumb reach on all phones without conflicting with a top navigation bar or status bar. The centered column also avoids the layout asymmetry of top-right stacks on large phone screens, where the right edge is a dead zone. For the rare desktop session, bottom-center remains unobtrusive and familiar — identical to the pattern used by Vercel, Linear, and Figma. Variant B is the better choice for apps where the user is a power desktop user who maps the top-right corner to notifications; Kinhold's family-hub context favors approachability over OS convention.
        </p>
      </div>
    </section>


    <!-- ══════════════════════════════════════════════════════════════
         USAGE GUIDE
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-8">
      <div class="rounded-2xl border p-6 space-y-5"
           :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle }">
        <h2 class="text-[17px] font-semibold" :style="{ color: L.inkPrimary }">When to use which variant</h2>
        <ul class="space-y-4 text-[14px]" :style="{ color: L.inkSecondary }">
          <li>
            <strong :style="{ color: L.inkPrimary }">Variant A — Bottom-center stack (recommended default)</strong>
            Use for all transient feedback: task completed, points awarded, form auto-saved, sync started.
            Auto-dismiss after 4–5 seconds. Limit to 3 visible at once — a fourth arrival should dismiss the oldest.
            Bottom-center on all breakpoints maximises thumb reachability and avoids conflict with fixed top navigation.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Variant B — Top-right desktop / top-center mobile</strong>
            Use when your primary user base is desktop-first and they have strong OS notification muscle memory
            (e.g., a settings-heavy admin interface inside Kinhold). Responsive repositioning to top-center on mobile
            is mandatory — do not show top-right on viewports under 768px.
            Same stacking rules as A: max 3 visible, auto-dismiss in 4–5 seconds.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Variant C — Inline banner</strong>
            Use for persistent, in-flow confirmations where the message must stay visible while the user reads or acts.
            Best examples: vault entry saved, settings updated, onboarding step complete, a recoverable error with explicit next steps.
            Never auto-dismiss inline banners — the absence of a progress bar is the signal of permanence.
            Always provide a dismiss X. Avoid stacking more than 3 banners simultaneously (pre-dismiss the oldest programmatically if needed).
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Auto-dismiss timing</strong>
            Success and info: 4–5 seconds. Warning: 6–8 seconds (requires more reading time). Error: do not auto-dismiss — errors need user acknowledgment. Use the progress bar for success/info/warning only.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Stacking limit</strong>
            Recommend max 3 toasts visible simultaneously for floating variants. Scale oldest to 0.94 and fade to 0.72 opacity to signal recency order. When a 4th arrives, programmatically dismiss the oldest.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Accessibility</strong>
            Toast containers should have <code class="font-mono text-[12px] px-1 py-0.5 rounded" :style="{ background: L.surfaceSunken }">role="status"</code> and
            <code class="font-mono text-[12px] px-1 py-0.5 rounded" :style="{ background: L.surfaceSunken }">aria-live="polite"</code> for success/info/warning.
            Use <code class="font-mono text-[12px] px-1 py-0.5 rounded" :style="{ background: L.surfaceSunken }">aria-live="assertive"</code> for errors only.
            Under <code class="font-mono text-[12px] px-1 py-0.5 rounded" :style="{ background: L.surfaceSunken }">prefers-reduced-motion</code>, disable the progress bar animation and show static "5s remaining" text instead.
          </li>
        </ul>
      </div>
    </section>

  </ComponentPage>
</template>

<style scoped>
/*
  Progress bar — reduced motion: bar stays static, label visible.
  Without reduced motion: bar animates via inline :style transition.
*/
@media (prefers-reduced-motion: reduce) {
  .toast-progress-bar,
  .toast-progress-bar-b {
    transition: none !important;
    width: 100% !important;
  }
  .toast-progress-label {
    display: inline !important;
  }
}
/* Hide label during normal animation (JS handles showing empty string) */
.toast-progress-label {
  display: inline;
}
</style>
