<script setup>
import { ref } from 'vue'
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import {
  EnvelopeIcon,
  MagnifyingGlassIcon,
  XMarkIcon,
  EyeIcon,
  EyeSlashIcon,
  ExclamationCircleIcon,
} from '@heroicons/vue/24/outline'

// ── Dark-mode hex constants (from tokens.css html.dark section) ─────────────
// Dark panel uses these hardcoded values so it always renders dark
// regardless of whether the root theme is light or dark.
const D = {
  surfaceApp:     '#141311',
  surfaceRaised:  '#1C1B19',
  // For borderless inputs on this page we INVERT the "pressed into page"
  // metaphor in dark mode — the page is already near-black, so inputs need
  // to sit RAISED (lighter than page) to read. These values must stay in
  // sync with the .vinput-b-dk styles in the <style scoped> block below.
  surfaceSunken:  '#242220',   // default dark input bg (lighter than page)
  surfaceOverlay: '#242220',
  inkPrimary:     '#F0EDE9',
  inkSecondary:   '#A09C97',
  inkTertiary:    '#8A8680',   // placeholder text on #242220 — brighter for legibility
  borderSubtle:   '#2C2A27',
  borderStrong:   '#403E3A',
  statusFailed:   '#E67070',
  accentBold:     '#B6A8E6',   // accent-lavender-bold dark
  // Hover bg for borderless inset — slightly lighter than default (raised surface gets brighter on hover)
  sunkenHover:    '#2C2A27',
}

// ── Light-mode hex constants ────────────────────────────────────────────────
const L = {
  surfaceApp:     '#FAF8F5',
  surfaceRaised:  '#FFFFFF',
  surfaceSunken:  '#F5F2EE',
  inkPrimary:     '#1C1C1E',
  inkSecondary:   '#6B6966',
  inkTertiary:    '#9C9895',
  borderSubtle:   '#E8E4DF',
  borderStrong:   '#BCB8B2',
  statusFailed:   '#BA4A4A',
  accentBold:     '#6856B2',
  // Hover darken for borderless inset
  sunkenHover:    '#EDEAE5',
}

// ── Reactive state for interactive examples ──────────────────────────────────
// Variant A
const aPasswordVisible    = ref(false)
const aPasswordVisibleDk  = ref(false)
const aSearchVal          = ref('')
const aSearchValDk        = ref('')

// Variant B
const bPasswordVisible    = ref(false)
const bPasswordVisibleDk  = ref(false)
const bSearchVal          = ref('')
const bSearchValDk        = ref('')

// Variant C (search only)
const cSearchVal          = ref('')
const cSearchValDk        = ref('')
</script>

<template>
  <ComponentPage
    title="1.2 Input · Textarea · Search"
    description="Three form-field variants. A = bordered filled; B = borderless inset; C = pill search. Each shown at light + dark. Pick one and reply A, B, or C."
    status="scaffolded"
  >

    <!-- ══════════════════════════════════════════════════════════════
         VARIANT B — Borderless Inset
         Flat, airy. Border only appears on focus. "Pressed into page" feel.
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="B" caption="Borderless inset — no border at rest, inset shadow, airy Stripe feel">
        <div class="w-full space-y-10">

          <!-- ── LIGHT PANEL B ──────────────────────────────────────── -->
          <div class="rounded-2xl border p-6 space-y-8" style="background:#FAF8F5; border-color:#E8E4DF">
            <p class="text-xs font-semibold uppercase tracking-widest" style="color:#9C9895">Light mode</p>

            <!-- Row 1: Default states -->
            <div>
              <p class="text-xs mb-3" style="color:#9C9895">Text input — default states</p>
              <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                <div class="flex flex-col gap-1">
                  <input type="text" placeholder="Placeholder"
                    class="vinput-b h-10 px-3.5 rounded-[12px] bg-[#F5F2EE] text-[15px] text-[#1C1C1E] placeholder:text-[#9C9895] border-0 outline-none w-full" />
                  <span class="text-[11px]" style="color:#9C9895">empty</span>
                </div>
                <div class="flex flex-col gap-1">
                  <input type="text" placeholder="Hover"
                    class="h-10 px-3.5 rounded-[12px] text-[15px] text-[#1C1C1E] placeholder:text-[#9C9895] border-0 outline-none w-full"
                    style="background:#EDEAE5; box-shadow:inset 0 1px 2px rgba(0,0,0,0.08)" />
                  <span class="text-[11px]" style="color:#9C9895">hover</span>
                </div>
                <div class="flex flex-col gap-1">
                  <input type="text" placeholder="Focused"
                    class="h-10 px-3.5 rounded-[12px] bg-[#F5F2EE] text-[15px] text-[#1C1C1E] placeholder:text-[#9C9895] outline-none w-full"
                    style="border: 1px solid #6856B2; box-shadow: 0 0 0 3px rgba(104,86,178,0.25)" />
                  <span class="text-[11px]" style="color:#9C9895">focused</span>
                </div>
                <div class="flex flex-col gap-1">
                  <input type="text" value="Hello world"
                    class="vinput-b h-10 px-3.5 rounded-[12px] bg-[#F5F2EE] text-[15px] text-[#1C1C1E] border-0 outline-none w-full" />
                  <span class="text-[11px]" style="color:#9C9895">filled</span>
                </div>
                <div class="flex flex-col gap-1">
                  <div class="relative">
                    <ExclamationCircleIcon class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 pointer-events-none" style="color:#BA4A4A" />
                    <input type="text" value="bad@"
                      class="h-10 px-3.5 pr-9 rounded-[12px] text-[15px] text-[#1C1C1E] border-0 outline-none w-full"
                      style="background:#F5F2EE; border: 1px solid #BA4A4A; box-shadow: 0 0 0 3px rgba(186,74,74,0.18)" />
                  </div>
                  <span class="text-[11px]" style="color:#BA4A4A">Email is invalid</span>
                  <span class="text-[11px]" style="color:#9C9895">error</span>
                </div>
                <div class="flex flex-col gap-1">
                  <input type="text" placeholder="Cannot edit" disabled
                    class="h-10 px-3.5 rounded-[12px] bg-[#F5F2EE] text-[15px] text-[#9C9895] border-0 cursor-not-allowed opacity-70 outline-none w-full" />
                  <span class="text-[11px]" style="color:#9C9895">disabled</span>
                </div>
              </div>
            </div>

            <!-- Row 2: Affordances -->
            <div>
              <p class="text-xs mb-3" style="color:#9C9895">Affordances</p>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex flex-col gap-1">
                  <label class="text-[13px] font-medium" style="color:#6B6966">Email address</label>
                  <div class="relative">
                    <EnvelopeIcon class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 pointer-events-none" style="color:#9C9895" />
                    <input type="email" placeholder="you@example.com"
                      class="vinput-b h-10 pl-10 pr-3.5 rounded-[12px] bg-[#F5F2EE] text-[15px] text-[#1C1C1E] placeholder:text-[#9C9895] border-0 outline-none w-full" />
                  </div>
                </div>
                <div class="flex flex-col gap-1">
                  <label class="text-[13px] font-medium" style="color:#6B6966">Password</label>
                  <div class="relative">
                    <input :type="bPasswordVisible ? 'text' : 'password'" value="s3cr3tpwd"
                      class="vinput-b h-10 px-3.5 pr-10 rounded-[12px] bg-[#F5F2EE] text-[15px] text-[#1C1C1E] border-0 outline-none w-full" />
                    <button type="button" @click="bPasswordVisible = !bPasswordVisible"
                      class="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 flex items-center justify-center"
                      style="color:#9C9895">
                      <EyeSlashIcon v-if="bPasswordVisible" class="w-4 h-4" />
                      <EyeIcon v-else class="w-4 h-4" />
                    </button>
                  </div>
                </div>
                <div class="flex flex-col gap-1">
                  <label class="text-[13px] font-medium" style="color:#6B6966">Family name</label>
                  <input type="text" placeholder="The Qualls family"
                    class="vinput-b h-10 px-3.5 rounded-[12px] bg-[#F5F2EE] text-[15px] text-[#1C1C1E] placeholder:text-[#9C9895] border-0 outline-none w-full" />
                  <span class="text-[11px]" style="color:#9C9895">Helper text beneath</span>
                </div>
                <!-- B-style search -->
                <div class="flex flex-col gap-1">
                  <label class="text-[13px] font-medium" style="color:#6B6966">Search (B-style)</label>
                  <div class="relative">
                    <MagnifyingGlassIcon class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 pointer-events-none" style="color:#9C9895" />
                    <input type="search" v-model="bSearchVal" placeholder="Search…"
                      class="vinput-b h-10 pl-10 pr-9 rounded-[12px] bg-[#F5F2EE] text-[15px] text-[#1C1C1E] placeholder:text-[#9C9895] border-0 outline-none w-full" />
                    <button v-if="bSearchVal" type="button" @click="bSearchVal = ''"
                      class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 flex items-center justify-center rounded-full hover:bg-[#E8E4DF]"
                      style="color:#9C9895">
                      <XMarkIcon class="w-3.5 h-3.5" />
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Row 3: Label placement -->
            <div>
              <p class="text-xs mb-3" style="color:#9C9895">Label placement</p>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="flex flex-col gap-1">
                  <label class="text-[13px] font-medium" style="color:#6B6966">Top-aligned label</label>
                  <input type="text" placeholder="Enter value"
                    class="vinput-b h-10 px-3.5 rounded-[12px] bg-[#F5F2EE] text-[15px] text-[#1C1C1E] placeholder:text-[#9C9895] border-0 outline-none w-full" />
                </div>
                <!-- Floating label for B -->
                <div class="flex flex-col gap-1">
                  <div class="vinput-b-float-wrap relative rounded-[12px] bg-[#F5F2EE] h-14" style="box-shadow:inset 0 1px 2px rgba(0,0,0,0.06)">
                    <input id="b-float-light" type="text" placeholder=" "
                      class="vinput-a-float peer absolute inset-0 h-full w-full px-3.5 pt-4 bg-transparent text-[15px] text-[#1C1C1E] outline-none rounded-[12px] border-0" />
                    <label for="b-float-light"
                      class="vinput-b-float-label absolute left-3.5 top-1/2 -translate-y-1/2 text-[15px] pointer-events-none transition-all duration-200 origin-left"
                      style="color:#9C9895">Floating label</label>
                  </div>
                  <span class="text-[11px]" style="color:#9C9895">Focus or type to float</span>
                </div>
                <div class="flex flex-col gap-1">
                  <input type="text" placeholder="Inline — no label"
                    class="vinput-b h-10 px-3.5 rounded-[12px] bg-[#F5F2EE] text-[15px] text-[#1C1C1E] placeholder:text-[#9C9895] border-0 outline-none w-full" />
                  <span class="text-[11px]" style="color:#9C9895">Inline / placeholder-only</span>
                </div>
              </div>
            </div>

            <!-- Row 4: Textarea -->
            <div>
              <p class="text-xs mb-3" style="color:#9C9895">Textarea</p>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="flex flex-col gap-1">
                  <textarea rows="3" placeholder="Write something…"
                    class="vinput-b px-3.5 py-2.5 rounded-[12px] bg-[#F5F2EE] text-[15px] text-[#1C1C1E] placeholder:text-[#9C9895] border-0 outline-none resize-y w-full"></textarea>
                  <span class="text-[11px]" style="color:#9C9895">empty</span>
                </div>
                <div class="flex flex-col gap-1">
                  <textarea rows="3"
                    class="vinput-b px-3.5 py-2.5 rounded-[12px] bg-[#F5F2EE] text-[15px] text-[#1C1C1E] border-0 outline-none resize-y w-full">This is a filled textarea with some text that wraps across multiple lines to demonstrate how the field handles longer content.</textarea>
                  <span class="text-[11px]" style="color:#9C9895">filled</span>
                </div>
                <div class="flex flex-col gap-1">
                  <textarea rows="3"
                    class="px-3.5 py-2.5 rounded-[12px] text-[15px] text-[#1C1C1E] outline-none resize-y w-full"
                    style="background:#F5F2EE; border: 1px solid #BA4A4A; box-shadow: 0 0 0 3px rgba(186,74,74,0.18)">Problem content.</textarea>
                  <span class="text-[11px]" style="color:#BA4A4A">Content is required</span>
                </div>
              </div>
            </div>

            <!-- Row 5: Sizes -->
            <div>
              <p class="text-xs mb-3" style="color:#9C9895">Size scale</p>
              <div class="flex flex-col gap-3">
                <div class="flex items-center gap-3">
                  <input type="text" placeholder="Small — 32px"
                    class="vinput-b h-8 px-3 rounded-[10px] bg-[#F5F2EE] text-[13px] text-[#1C1C1E] placeholder:text-[#9C9895] border-0 outline-none w-64" />
                  <span class="text-[11px]" style="color:#9C9895">sm (h-8)</span>
                </div>
                <div class="flex items-center gap-3">
                  <input type="text" placeholder="Medium — 40px (default)"
                    class="vinput-b h-10 px-3.5 rounded-[12px] bg-[#F5F2EE] text-[15px] text-[#1C1C1E] placeholder:text-[#9C9895] border-0 outline-none w-64" />
                  <span class="text-[11px]" style="color:#9C9895">md (h-10, default)</span>
                </div>
                <div class="flex items-center gap-3">
                  <input type="text" placeholder="Large — 48px"
                    class="vinput-b h-12 px-4 rounded-[12px] bg-[#F5F2EE] text-[16px] text-[#1C1C1E] placeholder:text-[#9C9895] border-0 outline-none w-64" />
                  <span class="text-[11px]" style="color:#9C9895">lg (h-12)</span>
                </div>
              </div>
            </div>

          </div><!-- /light panel B -->

          <!-- ── DARK PANEL B ──────────────────────────────────────── -->
          <div class="rounded-2xl border p-6 space-y-8" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

            <!-- Row 1: Default states -->
            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">Text input — default states</p>
              <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                <div class="flex flex-col gap-1">
                  <input type="text" placeholder="Placeholder"
                    class="vinput-b-dk h-10 px-3.5 rounded-[12px] text-[15px] outline-none w-full" />
                  <span class="text-[11px]" :style="{ color: D.inkTertiary }">empty</span>
                </div>
                <div class="flex flex-col gap-1">
                  <input type="text" placeholder="Hover"
                    class="h-10 px-3.5 rounded-[12px] text-[15px] border-0 outline-none w-full"
                    :style="{ background: D.sunkenHover, color: D.inkPrimary, boxShadow: 'inset 0 1px 0 rgba(255,255,255,0.06)' }" />
                  <span class="text-[11px]" :style="{ color: D.inkTertiary }">hover</span>
                </div>
                <div class="flex flex-col gap-1">
                  <input type="text" placeholder="Focused"
                    class="h-10 px-3.5 rounded-[12px] text-[15px] outline-none w-full"
                    :style="{ background: D.surfaceSunken, color: D.inkPrimary, border: `1px solid ${D.accentBold}`, boxShadow: `0 0 0 3px rgba(182,168,230,0.30)` }" />
                  <span class="text-[11px]" :style="{ color: D.inkTertiary }">focused</span>
                </div>
                <div class="flex flex-col gap-1">
                  <input type="text" value="Hello world"
                    class="vinput-b-dk h-10 px-3.5 rounded-[12px] text-[15px] outline-none w-full" />
                  <span class="text-[11px]" :style="{ color: D.inkTertiary }">filled</span>
                </div>
                <div class="flex flex-col gap-1">
                  <div class="relative">
                    <ExclamationCircleIcon class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 pointer-events-none" :style="{ color: D.statusFailed }" />
                    <input type="text" value="bad@"
                      class="h-10 px-3.5 pr-9 rounded-[12px] text-[15px] border-0 outline-none w-full"
                      :style="{ background: D.surfaceSunken, color: D.inkPrimary, border: `1px solid ${D.statusFailed}`, boxShadow: `0 0 0 3px rgba(230,112,112,0.22)` }" />
                  </div>
                  <span class="text-[11px]" :style="{ color: D.statusFailed }">Email is invalid</span>
                  <span class="text-[11px]" :style="{ color: D.inkTertiary }">error</span>
                </div>
                <div class="flex flex-col gap-1">
                  <input type="text" placeholder="Cannot edit" disabled
                    class="h-10 px-3.5 rounded-[12px] text-[15px] cursor-not-allowed opacity-70 border-0 outline-none w-full"
                    :style="{ background: D.surfaceSunken, color: D.inkTertiary }" />
                  <span class="text-[11px]" :style="{ color: D.inkTertiary }">disabled</span>
                </div>
              </div>
            </div>

            <!-- Row 2: Affordances -->
            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">Affordances</p>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex flex-col gap-1">
                  <label class="text-[13px] font-medium" :style="{ color: D.inkSecondary }">Email address</label>
                  <div class="relative">
                    <EnvelopeIcon class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 pointer-events-none" :style="{ color: D.inkTertiary }" />
                    <input type="email" placeholder="you@example.com"
                      class="vinput-b-dk h-10 pl-10 pr-3.5 rounded-[12px] text-[15px] outline-none w-full" />
                  </div>
                </div>
                <div class="flex flex-col gap-1">
                  <label class="text-[13px] font-medium" :style="{ color: D.inkSecondary }">Password</label>
                  <div class="relative">
                    <input :type="bPasswordVisibleDk ? 'text' : 'password'" value="s3cr3tpwd"
                      class="vinput-b-dk h-10 px-3.5 pr-10 rounded-[12px] text-[15px] outline-none w-full" />
                    <button type="button" @click="bPasswordVisibleDk = !bPasswordVisibleDk"
                      class="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 flex items-center justify-center"
                      :style="{ color: D.inkTertiary }">
                      <EyeSlashIcon v-if="bPasswordVisibleDk" class="w-4 h-4" />
                      <EyeIcon v-else class="w-4 h-4" />
                    </button>
                  </div>
                </div>
                <div class="flex flex-col gap-1">
                  <label class="text-[13px] font-medium" :style="{ color: D.inkSecondary }">Family name</label>
                  <input type="text" placeholder="The Qualls family"
                    class="vinput-b-dk h-10 px-3.5 rounded-[12px] text-[15px] outline-none w-full" />
                  <span class="text-[11px]" :style="{ color: D.inkTertiary }">Helper text beneath</span>
                </div>
                <div class="flex flex-col gap-1">
                  <label class="text-[13px] font-medium" :style="{ color: D.inkSecondary }">Search (B-style)</label>
                  <div class="relative">
                    <MagnifyingGlassIcon class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 pointer-events-none" :style="{ color: D.inkTertiary }" />
                    <input type="search" v-model="bSearchValDk" placeholder="Search…"
                      class="vinput-b-dk h-10 pl-10 pr-9 rounded-[12px] text-[15px] outline-none w-full" />
                    <button v-if="bSearchValDk" type="button" @click="bSearchValDk = ''"
                      class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 flex items-center justify-center rounded-full"
                      :style="{ color: D.inkTertiary }">
                      <XMarkIcon class="w-3.5 h-3.5" />
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Row 3: Label placement -->
            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">Label placement</p>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="flex flex-col gap-1">
                  <label class="text-[13px] font-medium" :style="{ color: D.inkSecondary }">Top-aligned label</label>
                  <input type="text" placeholder="Enter value"
                    class="vinput-b-dk h-10 px-3.5 rounded-[12px] text-[15px] outline-none w-full" />
                </div>
                <div class="flex flex-col gap-1">
                  <div class="vinput-b-float-wrap-dk relative rounded-[12px] h-14"
                    :style="{ background: D.surfaceSunken, boxShadow: 'inset 0 1px 2px rgba(0,0,0,0.18)' }">
                    <input id="b-float-dark" type="text" placeholder=" "
                      class="vinput-a-float peer absolute inset-0 h-full w-full px-3.5 pt-4 bg-transparent text-[15px] outline-none rounded-[12px] border-0"
                      :style="{ color: D.inkPrimary }" />
                    <label for="b-float-dark"
                      class="vinput-b-float-label-dk absolute left-3.5 top-1/2 -translate-y-1/2 text-[15px] pointer-events-none transition-all duration-200 origin-left"
                      :style="{ color: D.inkTertiary }">Floating label</label>
                  </div>
                  <span class="text-[11px]" :style="{ color: D.inkTertiary }">Focus or type to float</span>
                </div>
                <div class="flex flex-col gap-1">
                  <input type="text" placeholder="Inline — no label"
                    class="vinput-b-dk h-10 px-3.5 rounded-[12px] text-[15px] outline-none w-full" />
                  <span class="text-[11px]" :style="{ color: D.inkTertiary }">Inline / placeholder-only</span>
                </div>
              </div>
            </div>

            <!-- Row 4: Textarea -->
            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">Textarea</p>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="flex flex-col gap-1">
                  <textarea rows="3" placeholder="Write something…"
                    class="vinput-b-dk px-3.5 py-2.5 rounded-[12px] text-[15px] outline-none resize-y w-full"></textarea>
                  <span class="text-[11px]" :style="{ color: D.inkTertiary }">empty</span>
                </div>
                <div class="flex flex-col gap-1">
                  <textarea rows="3"
                    class="vinput-b-dk px-3.5 py-2.5 rounded-[12px] text-[15px] outline-none resize-y w-full">This is a filled textarea with some text that wraps across multiple lines.</textarea>
                  <span class="text-[11px]" :style="{ color: D.inkTertiary }">filled</span>
                </div>
                <div class="flex flex-col gap-1">
                  <textarea rows="3"
                    class="px-3.5 py-2.5 rounded-[12px] text-[15px] outline-none resize-y w-full"
                    :style="{ background: D.surfaceSunken, color: D.inkPrimary, border: `1px solid ${D.statusFailed}`, boxShadow: `0 0 0 3px rgba(230,112,112,0.22)` }">Problem content.</textarea>
                  <span class="text-[11px]" :style="{ color: D.statusFailed }">Content is required</span>
                </div>
              </div>
            </div>

            <!-- Row 5: Sizes -->
            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">Size scale</p>
              <div class="flex flex-col gap-3">
                <div class="flex items-center gap-3">
                  <input type="text" placeholder="Small — 32px"
                    class="vinput-b-dk h-8 px-3 rounded-[10px] text-[13px] outline-none w-64" />
                  <span class="text-[11px]" :style="{ color: D.inkTertiary }">sm (h-8)</span>
                </div>
                <div class="flex items-center gap-3">
                  <input type="text" placeholder="Medium — 40px"
                    class="vinput-b-dk h-10 px-3.5 rounded-[12px] text-[15px] outline-none w-64" />
                  <span class="text-[11px]" :style="{ color: D.inkTertiary }">md (h-10, default)</span>
                </div>
                <div class="flex items-center gap-3">
                  <input type="text" placeholder="Large — 48px"
                    class="vinput-b-dk h-12 px-4 rounded-[12px] text-[16px] outline-none w-64" />
                  <span class="text-[11px]" :style="{ color: D.inkTertiary }">lg (h-12)</span>
                </div>
              </div>
            </div>

          </div><!-- /dark panel B -->
        </div>
      </VariantFrame>

      <p class="mt-3 text-body-sm text-ink-secondary px-1">
        Borderless Inset: the sunken fill and inset shadow communicate "field" without any border chrome. Feels lighter and more editorial — great for modern settings forms or search-heavy pages. The border only materializes on focus, so the accent ring gets full attention.
      </p>
    </section>

    <!-- ══════════════════════════════════════════════════════════════
         VARIANT C — Pill Search Field
         rounded-pill. Search inputs ONLY per the brief.
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="C" caption="Pill search — rounded-pill shape, search inputs only">
        <div class="w-full space-y-10">

          <!-- ── LIGHT PANEL C ──────────────────────────────────────── -->
          <div class="rounded-2xl border p-6 space-y-8" style="background:#FAF8F5; border-color:#E8E4DF">
            <p class="text-xs font-semibold uppercase tracking-widest" style="color:#9C9895">Light mode</p>
            <p class="text-[11px] max-w-prose" style="color:#9C9895">
              Pill shape (rounded-9999px) is reserved for search inputs only per the design brief (tenet #4). Text input and textarea below use the A/B treatment for comparison.
            </p>

            <!-- Pill search row -->
            <div>
              <p class="text-xs mb-3" style="color:#9C9895">Pill search — states</p>
              <div class="flex flex-col gap-4">
                <!-- Empty search -->
                <div class="flex flex-col gap-1">
                  <div class="relative w-full max-w-sm">
                    <MagnifyingGlassIcon class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 pointer-events-none" style="color:#9C9895" />
                    <input type="search" placeholder="Search family…"
                      class="vinput-c h-10 pl-11 pr-4 rounded-full bg-[#F5F2EE] text-[15px] text-[#1C1C1E] placeholder:text-[#9C9895] border-0 outline-none w-full" />
                  </div>
                  <span class="text-[11px]" style="color:#9C9895">empty — no border at rest</span>
                </div>
                <!-- Focused search -->
                <div class="flex flex-col gap-1">
                  <div class="relative w-full max-w-sm">
                    <MagnifyingGlassIcon class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 pointer-events-none" style="color:#6856B2" />
                    <input type="search" placeholder="Search family…"
                      class="h-10 pl-11 pr-4 rounded-full bg-[#F5F2EE] text-[15px] text-[#1C1C1E] placeholder:text-[#9C9895] border-0 outline-none w-full"
                      style="border: 1px solid #6856B2; box-shadow: 0 0 0 3px rgba(104,86,178,0.25)" />
                  </div>
                  <span class="text-[11px]" style="color:#9C9895">focused</span>
                </div>
                <!-- Filled with clear button -->
                <div class="flex flex-col gap-1">
                  <div class="relative w-full max-w-sm">
                    <MagnifyingGlassIcon class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 pointer-events-none" style="color:#6B6966" />
                    <input type="search" v-model="cSearchVal" placeholder="Search family…"
                      class="vinput-c h-10 pl-11 pr-10 rounded-full bg-[#F5F2EE] text-[15px] text-[#1C1C1E] placeholder:text-[#9C9895] border-0 outline-none w-full" />
                    <button v-if="cSearchVal" type="button" @click="cSearchVal = ''"
                      class="vinput-c-clear absolute right-3 top-1/2 -translate-y-1/2 w-6 h-6 flex items-center justify-center rounded-full"
                      style="color:#6B6966">
                      <XMarkIcon class="w-3.5 h-3.5" />
                    </button>
                    <!-- Placeholder X to show the affordance even when empty for demo -->
                    <button v-if="!cSearchVal" type="button"
                      class="vinput-c-clear absolute right-3 top-1/2 -translate-y-1/2 w-6 h-6 flex items-center justify-center rounded-full opacity-40 pointer-events-none"
                      style="color:#6B6966">
                      <XMarkIcon class="w-3.5 h-3.5" />
                    </button>
                  </div>
                  <span class="text-[11px]" style="color:#9C9895">filled — type to activate clear button; X shows when filled</span>
                </div>
                <!-- Hover (simulated) -->
                <div class="flex flex-col gap-1">
                  <div class="relative w-full max-w-sm">
                    <MagnifyingGlassIcon class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 pointer-events-none" style="color:#9C9895" />
                    <input type="search" placeholder="Hover state"
                      class="h-10 pl-11 pr-4 rounded-full text-[15px] text-[#1C1C1E] placeholder:text-[#9C9895] border-0 outline-none w-full"
                      style="background:#EDEAE5; box-shadow:inset 0 1px 2px rgba(0,0,0,0.06)" />
                  </div>
                  <span class="text-[11px]" style="color:#9C9895">hover — bg darkens slightly</span>
                </div>
              </div>
            </div>

            <!-- Text inputs use A-style for comparison -->
            <div>
              <p class="text-xs mb-3" style="color:#9C9895">Text input (A-treatment for comparison — pill not used for text fields)</p>
              <div class="flex flex-col gap-3 max-w-sm">
                <input type="text" placeholder="Text field — bordered A"
                  class="vinput-a h-10 px-3.5 rounded-[12px] border border-[#E8E4DF] bg-white text-[15px] text-[#1C1C1E] placeholder:text-[#9C9895] outline-none w-full" />
                <textarea rows="2" placeholder="Textarea — bordered A"
                  class="vinput-a px-3.5 py-2.5 rounded-[12px] border border-[#E8E4DF] bg-white text-[15px] text-[#1C1C1E] placeholder:text-[#9C9895] outline-none resize-y w-full"></textarea>
              </div>
            </div>

          </div><!-- /light panel C -->

          <!-- ── DARK PANEL C ──────────────────────────────────────── -->
          <div class="rounded-2xl border p-6 space-y-8" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

            <!-- Pill search row dark -->
            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">Pill search — states</p>
              <div class="flex flex-col gap-4">
                <div class="flex flex-col gap-1">
                  <div class="relative w-full max-w-sm">
                    <MagnifyingGlassIcon class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 pointer-events-none" :style="{ color: D.inkTertiary }" />
                    <input type="search" placeholder="Search family…"
                      class="vinput-c-dk h-10 pl-11 pr-4 rounded-full text-[15px] outline-none w-full" />
                  </div>
                  <span class="text-[11px]" :style="{ color: D.inkTertiary }">empty</span>
                </div>
                <div class="flex flex-col gap-1">
                  <div class="relative w-full max-w-sm">
                    <MagnifyingGlassIcon class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 pointer-events-none" :style="{ color: D.accentBold }" />
                    <input type="search" placeholder="Search family…"
                      class="h-10 pl-11 pr-4 rounded-full text-[15px] outline-none w-full"
                      :style="{ background: D.surfaceSunken, color: D.inkPrimary, border: `1px solid ${D.accentBold}`, boxShadow: `0 0 0 3px rgba(182,168,230,0.30)` }" />
                  </div>
                  <span class="text-[11px]" :style="{ color: D.inkTertiary }">focused</span>
                </div>
                <div class="flex flex-col gap-1">
                  <div class="relative w-full max-w-sm">
                    <MagnifyingGlassIcon class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 pointer-events-none" :style="{ color: D.inkSecondary }" />
                    <input type="search" v-model="cSearchValDk" placeholder="Search family…"
                      class="vinput-c-dk h-10 pl-11 pr-10 rounded-full text-[15px] outline-none w-full" />
                    <button v-if="cSearchValDk" type="button" @click="cSearchValDk = ''"
                      class="vinput-c-clear-dk absolute right-3 top-1/2 -translate-y-1/2 w-6 h-6 flex items-center justify-center rounded-full"
                      :style="{ color: D.inkSecondary }">
                      <XMarkIcon class="w-3.5 h-3.5" />
                    </button>
                    <button v-if="!cSearchValDk" type="button"
                      class="vinput-c-clear-dk absolute right-3 top-1/2 -translate-y-1/2 w-6 h-6 flex items-center justify-center rounded-full opacity-40 pointer-events-none"
                      :style="{ color: D.inkSecondary }">
                      <XMarkIcon class="w-3.5 h-3.5" />
                    </button>
                  </div>
                  <span class="text-[11px]" :style="{ color: D.inkTertiary }">filled — type to activate clear</span>
                </div>
                <div class="flex flex-col gap-1">
                  <div class="relative w-full max-w-sm">
                    <MagnifyingGlassIcon class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 pointer-events-none" :style="{ color: D.inkTertiary }" />
                    <input type="search" placeholder="Hover state"
                      class="h-10 pl-11 pr-4 rounded-full text-[15px] border-0 outline-none w-full"
                      :style="{ background: D.sunkenHover, color: D.inkPrimary, boxShadow: 'inset 0 1px 2px rgba(0,0,0,0.18)' }" />
                  </div>
                  <span class="text-[11px]" :style="{ color: D.inkTertiary }">hover</span>
                </div>
              </div>
            </div>

            <!-- Text inputs comparison dark -->
            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">Text input (A-treatment for comparison)</p>
              <div class="flex flex-col gap-3 max-w-sm">
                <input type="text" placeholder="Text field — bordered A"
                  class="vinput-a-dk h-10 px-3.5 rounded-[12px] text-[15px] outline-none w-full" />
                <textarea rows="2" placeholder="Textarea — bordered A"
                  class="vinput-a-dk px-3.5 py-2.5 rounded-[12px] text-[15px] outline-none resize-y w-full"></textarea>
              </div>
            </div>

          </div><!-- /dark panel C -->
        </div>
      </VariantFrame>

      <p class="mt-3 text-body-sm text-ink-secondary px-1">
        Pill Search: the full-round pill shape signals "search" instantly and contrasts with the rect/soft-rect inputs used for data entry. The magnifier prefix and conditional clear X make the affordance crystal clear at a glance. Per the brief, this shape is reserved for search only — general text fields use A or B.
      </p>
    </section>

    <!-- ══════════════════════════════════════════════════════════════
         DECISION HELPER
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-8">
      <div class="rounded-2xl border p-6 space-y-4" style="background:#FFFFFF; border-color:#E8E4DF">
        <h2 class="text-[17px] font-semibold" style="color:#1C1C1E">How to pick</h2>
        <ul class="space-y-3 text-[14px]" style="color:#6B6966">
          <li>
            <strong style="color:#1C1C1E">Mobile legibility:</strong>
            A wins slightly — the always-visible border gives a clear tap target even before focus.
            B's no-border rest state requires slightly more scanning on small screens.
            Both pass well; A is safer for family members who are less techy.
          </li>
          <li>
            <strong style="color:#1C1C1E">Focus visibility:</strong>
            Both A and B use the identical 3px lavender focus ring, so keyboard/accessibility parity is equal.
            The difference is the resting state, not the focused state.
          </li>
          <li>
            <strong style="color:#1C1C1E">Error clarity:</strong>
            A = border color change + ring. B = border appears + ring. A's transition is more obvious because
            the border is already visible and just changes color. B's is slightly more dramatic (border
            appears from nothing), which could read as "more attention-grabbing" or "more jarring" depending on taste.
          </li>
          <li>
            <strong style="color:#1C1C1E">Premium feel:</strong>
            B reads as more premium and editorial — the sunken fill gives a "form carved into the surface" quality
            that's closer to Stripe and Linear. A is more utilitarian and universally familiar (closest to what
            most form libraries look like). If Kinhold's audience is mixed (parents who use Google Forms, kids
            who use TikTok), A is the safer choice. If the goal is to feel distinctly premium from day one, B.
          </li>
          <li>
            <strong style="color:#1C1C1E">C (Pill search):</strong>
            Not an either/or — Variant C is the search treatment regardless of which you pick for text inputs.
            A and B can both coexist with C. Your final answer should be "A" or "B" (for text/textarea), with C
            being implicitly chosen for all search inputs.
          </li>
        </ul>
        <div class="pt-2 border-t" style="border-color:#E8E4DF">
          <p class="text-[14px]" style="color:#1C1C1E">
            <strong>Reply "A", "B", or "C"</strong> to lock the input variant.
            ("C" by itself means you prefer the pill treatment extended to all fields, which would override
            the brief's intent — clarify if that's your preference.)
          </p>
        </div>
      </div>
    </section>

  </ComponentPage>
</template>

<style scoped>
/*
  ─────────────────────────────────────────────────────────────────
  VARIANT B — Light panel
  No border at rest. Hover darkens bg. Focus: border + ring.
  ─────────────────────────────────────────────────────────────────
*/
.vinput-b {
  box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.06);
  transition: background-color 200ms, box-shadow 200ms, border-color 200ms;
}
.vinput-b:hover {
  background-color: #EDEAE5;
  box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.08);
}
.vinput-b:focus {
  background-color: #F5F2EE;
  border: 1px solid #6856B2;
  box-shadow: 0 0 0 3px rgba(104, 86, 178, 0.25);
}
textarea.vinput-b {
  box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.06);
  transition: background-color 200ms, box-shadow 200ms;
}
textarea.vinput-b:hover {
  background-color: #EDEAE5;
}
textarea.vinput-b:focus {
  background-color: #F5F2EE;
  border: 1px solid #6856B2;
  box-shadow: 0 0 0 3px rgba(104, 86, 178, 0.25);
}

/*
  ─────────────────────────────────────────────────────────────────
  VARIANT B — Dark panel
  ─────────────────────────────────────────────────────────────────
*/
/*
  Dark-mode inversion of the borderless-inset metaphor:
  In light mode, the input sits pressed INTO the page (sunken, darker than page).
  In dark mode, the page is already near-black — a "darker" input would vanish.
  So dark-mode inputs sit RAISED above the page: clearly lighter bg (#242220)
  and a subtle inner top highlight instead of inset shadow. Same airy/editorial
  feel, but readable against the charcoal ground.
*/
.vinput-b-dk {
  background: #242220;
  color: #F0EDE9;
  border: 1px solid transparent;
  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.04);
  transition: background-color 200ms, box-shadow 200ms, border-color 200ms;
}
.vinput-b-dk::placeholder {
  color: #8A8680;
}
.vinput-b-dk:hover {
  background: #2C2A27;
  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.06);
}
.vinput-b-dk:focus {
  background: #242220;
  border-color: #B6A8E6;
  box-shadow: 0 0 0 3px rgba(182, 168, 230, 0.30);
}
textarea.vinput-b-dk {
  background: #242220;
  color: #F0EDE9;
  border: 1px solid transparent;
  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.04);
}
textarea.vinput-b-dk::placeholder {
  color: #8A8680;
}
textarea.vinput-b-dk:hover {
  background: #2C2A27;
}
textarea.vinput-b-dk:focus {
  border-color: #B6A8E6;
  box-shadow: 0 0 0 3px rgba(182, 168, 230, 0.30);
}

/*
  ─────────────────────────────────────────────────────────────────
  VARIANT C — Pill search, light panel
  ─────────────────────────────────────────────────────────────────
*/
.vinput-c {
  box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.06);
  transition: background-color 200ms, box-shadow 200ms, border-color 200ms;
}
.vinput-c:hover {
  background-color: #EDEAE5;
  box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.08);
}
.vinput-c:focus {
  background-color: #F5F2EE;
  border: 1px solid #6856B2;
  box-shadow: 0 0 0 3px rgba(104, 86, 178, 0.25);
}

/* Clear button hover — sunken fill */
.vinput-c-clear:hover {
  background-color: #F5F2EE;
}

/*
  ─────────────────────────────────────────────────────────────────
  VARIANT C — Pill search, dark panel
  ─────────────────────────────────────────────────────────────────
*/
.vinput-c-dk {
  background: #161513;
  color: #F0EDE9;
  border: 1px solid transparent;
  box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.15);
  transition: background-color 200ms, box-shadow 200ms, border-color 200ms;
}
.vinput-c-dk::placeholder {
  color: #6E6B67;
}
.vinput-c-dk:hover {
  background: #12110F;
}
.vinput-c-dk:focus {
  border-color: #B6A8E6;
  box-shadow: 0 0 0 3px rgba(182, 168, 230, 0.30);
}

/* Clear button hover dark */
.vinput-c-clear-dk:hover {
  background-color: #242220;
}

/*
  ─────────────────────────────────────────────────────────────────
  FLOATING LABEL — Light (works for both A and B light panels)
  The `peer` input has `placeholder=" "` (single space) so
  :not(:placeholder-shown) fires when the input has a real value.
  ─────────────────────────────────────────────────────────────────
*/
.vinput-b-float-label {
  color: #9C9895;
  transition: transform 200ms ease, font-size 200ms ease, color 200ms ease;
  transform-origin: left center;
}
.vinput-a-float:focus + .vinput-b-float-label,
.vinput-a-float:not(:placeholder-shown) + .vinput-b-float-label {
  transform: translateY(-17px) scale(0.78);
  color: #6856B2;
}

/*
  ─────────────────────────────────────────────────────────────────
  FLOATING LABEL — Dark B panel
  ─────────────────────────────────────────────────────────────────
*/
.vinput-b-float-label-dk {
  color: #6E6B67;
  transition: transform 200ms ease, font-size 200ms ease, color 200ms ease;
  transform-origin: left center;
}
.vinput-a-float:focus + .vinput-b-float-label-dk,
.vinput-a-float:not(:placeholder-shown) + .vinput-b-float-label-dk {
  transform: translateY(-17px) scale(0.78);
  color: #B6A8E6;
}

/*
  ─────────────────────────────────────────────────────────────────
  FOCUS on wrapper containers for floating-label variants
  (A-light and B-light have bordered/sunken wrappers)
  ─────────────────────────────────────────────────────────────────
*/
.vinput-b-float-wrap:focus-within {
  border: 1px solid #6856B2 !important;
  box-shadow: 0 0 0 3px rgba(104, 86, 178, 0.25);
}
.vinput-b-float-wrap-dk:focus-within {
  border: 1px solid #B6A8E6 !important;
  box-shadow: 0 0 0 3px rgba(182, 168, 230, 0.30);
}
</style>
