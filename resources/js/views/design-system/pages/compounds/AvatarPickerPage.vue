<script setup>
import { ref, computed } from 'vue'
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import {
  SparklesIcon, CheckIcon, StarIcon, HeartIcon,
} from '@heroicons/vue/24/solid'
import { SparklesIcon as SparklesOutlineIcon } from '@heroicons/vue/24/outline'

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
}

// ── Member data (5 members: photo, initials, icon variants) ──────────────────
const MEMBERS = [
  {
    key: 'emma',
    name: 'Emma',
    type: 'photo',
    photo: 'https://i.pravatar.cc/80?img=1',
    accentColor: 'peach',
  },
  {
    key: 'maya',
    name: 'Maya',
    type: 'initials',
    initials: 'MQ',
    accentColor: 'lavender',
  },
  {
    key: 'greg',
    name: 'Greg',
    type: 'photo',
    photo: 'https://i.pravatar.cc/80?img=12',
    accentColor: 'sun',
  },
  {
    key: 'ava',
    name: 'Ava',
    type: 'icon',
    icon: 'heart',
    accentColor: 'mint',
  },
  {
    key: 'sam',
    name: 'Sam',
    type: 'photo',
    photo: 'https://i.pravatar.cc/80?img=5',
    accentColor: 'lavender',
  },
]

// 8 members for overflow demo
const MEMBERS_OVERFLOW = [
  ...MEMBERS,
  { key: 'lee',   name: 'Lee',   type: 'initials', initials: 'LQ', accentColor: 'sun'     },
  { key: 'theo',  name: 'Theo',  type: 'icon',     icon: 'star',   accentColor: 'peach'   },
  { key: 'chloe', name: 'Chloe', type: 'photo',    photo: 'https://i.pravatar.cc/80?img=9', accentColor: 'mint' },
]

// ── Sizes ─────────────────────────────────────────────────────────────────────
const SIZE_MD = 40
const SIZE_LG = 56
const FONT_MD = '13px'
const FONT_LG = '17px'
const ICON_MD = 18
const ICON_LG = 24

// ── Reactive state ────────────────────────────────────────────────────────────
// Variant A — single-select
const activeA_L  = ref('emma')
const activeA_D  = ref('emma')
// Variant A — multi-select
const multiA_L   = ref(['emma', 'greg'])
const multiA_D   = ref(['emma', 'greg'])
// Variant B — single-select
const activeB_L  = ref('emma')
const activeB_D  = ref('emma')
// Variant B — multi-select
const multiB_L   = ref(['emma', 'greg'])
const multiB_D   = ref(['emma', 'greg'])
// Variant C — single-select
const activeC_L  = ref('emma')
const activeC_D  = ref('emma')
// Variant C — multi-select
const multiC_L   = ref(['emma', 'greg'])
const multiC_D   = ref(['emma', 'greg'])

// ── Helpers ───────────────────────────────────────────────────────────────────
function toggleMulti(arr, key) {
  const i = arr.indexOf(key)
  i === -1 ? arr.push(key) : arr.splice(i, 1)
}

function accentSoft(member, palette) {
  return palette.accents[member.accentColor].soft
}
function accentBold(member, palette) {
  return palette.accents[member.accentColor].bold
}

// For Variant C stack overflow — show first N, then "+X" chip
const STACK_VISIBLE = 5
const stackOverflowCount = computed(() => Math.max(0, MEMBERS_OVERFLOW.length - STACK_VISIBLE))
</script>

<template>
  <ComponentPage
    title="4.6 AvatarPicker"
    description="Horizontal row of selectable avatars for family-member pickers — task assignment, kudos recipient, vault share, meal plan cook assignment. Three layout variants for different density and space constraints."
    status="chosen"
  >

    <!-- ══════════════════════════════════════════════════════════════
         VARIANT A — Plain circle row with names beneath
         Active: 2px lavender-bold ring + bold name.
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="A" caption="Circle row + name label · lavender ring on active · flex-wrap overflow">
        <div class="w-full space-y-10">

          <!-- ── LIGHT ── -->
          <div class="rounded-2xl border p-6 space-y-8"
               :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>

            <!-- Single-select, md -->
            <div class="space-y-3">
              <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Single-select — md (40px)</p>
              <div class="flex flex-wrap gap-4">
                <button
                  v-for="m in MEMBERS"
                  :key="m.key"
                  class="avatar-btn-a flex flex-col items-center gap-1.5"
                  @click="activeA_L = m.key"
                >
                  <!-- Circle -->
                  <span
                    class="relative flex items-center justify-center rounded-full overflow-hidden flex-shrink-0 transition-all"
                    :style="{
                      width: SIZE_MD + 'px',
                      height: SIZE_MD + 'px',
                      outline: activeA_L === m.key ? `2px solid ${L.accents.lavender.bold}` : '2px solid transparent',
                      outlineOffset: '2px',
                      background: m.type !== 'photo' ? accentSoft(m, L) : 'transparent',
                    }"
                  >
                    <img v-if="m.type === 'photo'" :src="m.photo" :alt="m.name"
                         class="w-full h-full object-cover" />
                    <span v-else-if="m.type === 'initials'"
                          class="font-semibold select-none"
                          :style="{ fontSize: FONT_MD, color: accentBold(m, L) }">
                      {{ m.initials }}
                    </span>
                    <HeartIcon v-else-if="m.icon === 'heart'"
                               :style="{ width: ICON_MD + 'px', height: ICON_MD + 'px', color: accentBold(m, L) }" />
                    <StarIcon v-else-if="m.icon === 'star'"
                              :style="{ width: ICON_MD + 'px', height: ICON_MD + 'px', color: accentBold(m, L) }" />
                    <SparklesIcon v-else
                                  :style="{ width: ICON_MD + 'px', height: ICON_MD + 'px', color: accentBold(m, L) }" />
                  </span>
                  <!-- Name -->
                  <span class="text-[11px] leading-none transition-all"
                        :style="{
                          color: activeA_L === m.key ? L.inkPrimary : L.inkTertiary,
                          fontWeight: activeA_L === m.key ? '600' : '400',
                        }">
                    {{ m.name }}
                  </span>
                </button>
              </div>
            </div>

            <!-- Single-select, lg -->
            <div class="space-y-3">
              <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Single-select — lg (56px)</p>
              <div class="flex flex-wrap gap-5">
                <button
                  v-for="m in MEMBERS"
                  :key="m.key"
                  class="avatar-btn-a flex flex-col items-center gap-2"
                  @click="activeA_L = m.key"
                >
                  <span
                    class="relative flex items-center justify-center rounded-full overflow-hidden flex-shrink-0 transition-all"
                    :style="{
                      width: SIZE_LG + 'px',
                      height: SIZE_LG + 'px',
                      outline: activeA_L === m.key ? `2px solid ${L.accents.lavender.bold}` : '2px solid transparent',
                      outlineOffset: '2px',
                      background: m.type !== 'photo' ? accentSoft(m, L) : 'transparent',
                    }"
                  >
                    <img v-if="m.type === 'photo'" :src="m.photo" :alt="m.name"
                         class="w-full h-full object-cover" />
                    <span v-else-if="m.type === 'initials'"
                          class="font-semibold select-none"
                          :style="{ fontSize: FONT_LG, color: accentBold(m, L) }">
                      {{ m.initials }}
                    </span>
                    <HeartIcon v-else-if="m.icon === 'heart'"
                               :style="{ width: ICON_LG + 'px', height: ICON_LG + 'px', color: accentBold(m, L) }" />
                    <StarIcon v-else-if="m.icon === 'star'"
                              :style="{ width: ICON_LG + 'px', height: ICON_LG + 'px', color: accentBold(m, L) }" />
                    <SparklesIcon v-else
                                  :style="{ width: ICON_LG + 'px', height: ICON_LG + 'px', color: accentBold(m, L) }" />
                  </span>
                  <span class="text-[12px] leading-none transition-all"
                        :style="{
                          color: activeA_L === m.key ? L.inkPrimary : L.inkTertiary,
                          fontWeight: activeA_L === m.key ? '600' : '400',
                        }">
                    {{ m.name }}
                  </span>
                </button>
              </div>
            </div>

            <!-- Multi-select, md -->
            <div class="space-y-3">
              <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Multi-select — md (checkmark overlay)</p>
              <div class="flex flex-wrap gap-4">
                <button
                  v-for="m in MEMBERS"
                  :key="m.key"
                  class="avatar-btn-a flex flex-col items-center gap-1.5"
                  @click="toggleMulti(multiA_L, m.key)"
                >
                  <span
                    class="relative flex items-center justify-center rounded-full overflow-hidden flex-shrink-0 transition-all"
                    :style="{
                      width: SIZE_MD + 'px',
                      height: SIZE_MD + 'px',
                      outline: multiA_L.includes(m.key) ? `2px solid ${L.accents.lavender.bold}` : '2px solid transparent',
                      outlineOffset: '2px',
                      background: m.type !== 'photo' ? accentSoft(m, L) : 'transparent',
                    }"
                  >
                    <img v-if="m.type === 'photo'" :src="m.photo" :alt="m.name"
                         class="w-full h-full object-cover" />
                    <span v-else-if="m.type === 'initials'"
                          class="font-semibold select-none"
                          :style="{ fontSize: FONT_MD, color: accentBold(m, L) }">
                      {{ m.initials }}
                    </span>
                    <HeartIcon v-else-if="m.icon === 'heart'"
                               :style="{ width: ICON_MD + 'px', height: ICON_MD + 'px', color: accentBold(m, L) }" />
                    <StarIcon v-else-if="m.icon === 'star'"
                              :style="{ width: ICON_MD + 'px', height: ICON_MD + 'px', color: accentBold(m, L) }" />
                    <SparklesIcon v-else
                                  :style="{ width: ICON_MD + 'px', height: ICON_MD + 'px', color: accentBold(m, L) }" />
                    <!-- Checkmark overlay -->
                    <span v-if="multiA_L.includes(m.key)"
                          class="absolute inset-0 flex items-center justify-center rounded-full"
                          :style="{ background: `${L.accents.lavender.bold}CC` }">
                      <CheckIcon :style="{ width: '18px', height: '18px', color: '#FFFFFF' }" />
                    </span>
                  </span>
                  <span class="text-[11px] leading-none transition-all"
                        :style="{
                          color: multiA_L.includes(m.key) ? L.inkPrimary : L.inkTertiary,
                          fontWeight: multiA_L.includes(m.key) ? '600' : '400',
                        }">
                    {{ m.name }}
                  </span>
                </button>
              </div>
            </div>

            <!-- Disabled state -->
            <div class="space-y-3">
              <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Disabled avatar (3rd member)</p>
              <div class="flex flex-wrap gap-4">
                <button
                  v-for="(m, idx) in MEMBERS"
                  :key="m.key"
                  class="flex flex-col items-center gap-1.5"
                  :class="idx === 2 ? 'cursor-not-allowed' : 'avatar-btn-a'"
                  :disabled="idx === 2"
                  @click="idx !== 2 && (activeA_L = m.key)"
                >
                  <span
                    class="relative flex items-center justify-center rounded-full overflow-hidden flex-shrink-0"
                    :style="{
                      width: SIZE_MD + 'px',
                      height: SIZE_MD + 'px',
                      outline: (idx !== 2 && activeA_L === m.key) ? `2px solid ${L.accents.lavender.bold}` : '2px solid transparent',
                      outlineOffset: '2px',
                      background: m.type !== 'photo' ? accentSoft(m, L) : 'transparent',
                      filter: idx === 2 ? 'grayscale(1)' : 'none',
                      opacity: idx === 2 ? '0.4' : '1',
                    }"
                  >
                    <img v-if="m.type === 'photo'" :src="m.photo" :alt="m.name"
                         class="w-full h-full object-cover" />
                    <span v-else-if="m.type === 'initials'"
                          class="font-semibold select-none"
                          :style="{ fontSize: FONT_MD, color: accentBold(m, L) }">
                      {{ m.initials }}
                    </span>
                    <HeartIcon v-else-if="m.icon === 'heart'"
                               :style="{ width: ICON_MD + 'px', height: ICON_MD + 'px', color: accentBold(m, L) }" />
                    <StarIcon v-else-if="m.icon === 'star'"
                              :style="{ width: ICON_MD + 'px', height: ICON_MD + 'px', color: accentBold(m, L) }" />
                    <SparklesIcon v-else
                                  :style="{ width: ICON_MD + 'px', height: ICON_MD + 'px', color: accentBold(m, L) }" />
                  </span>
                  <span class="text-[11px] leading-none"
                        :style="{
                          color: idx === 2 ? L.inkTertiary : (activeA_L === m.key ? L.inkPrimary : L.inkTertiary),
                          fontWeight: (idx !== 2 && activeA_L === m.key) ? '600' : '400',
                          opacity: idx === 2 ? '0.4' : '1',
                        }">
                    {{ m.name }}
                  </span>
                </button>
              </div>
            </div>

            <!-- 320px overflow demo -->
            <div class="space-y-2">
              <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">8 members · max-w-[320px] · flex-wrap overflow</p>
              <div class="max-w-[320px] rounded-xl border p-3"
                   :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle }">
                <div class="flex flex-wrap gap-3">
                  <button
                    v-for="m in MEMBERS_OVERFLOW"
                    :key="m.key"
                    class="avatar-btn-a flex flex-col items-center gap-1"
                    @click="activeA_L = m.key"
                  >
                    <span
                      class="relative flex items-center justify-center rounded-full overflow-hidden flex-shrink-0 transition-all"
                      :style="{
                        width: '36px', height: '36px',
                        outline: activeA_L === m.key ? `2px solid ${L.accents.lavender.bold}` : '2px solid transparent',
                        outlineOffset: '2px',
                        background: m.type !== 'photo' ? accentSoft(m, L) : 'transparent',
                      }"
                    >
                      <img v-if="m.type === 'photo'" :src="m.photo" :alt="m.name"
                           class="w-full h-full object-cover" />
                      <span v-else-if="m.type === 'initials'"
                            class="font-semibold select-none text-[11px]"
                            :style="{ color: accentBold(m, L) }">
                        {{ m.initials }}
                      </span>
                      <HeartIcon v-else-if="m.icon === 'heart'" style="width:15px;height:15px;"
                                 :style="{ color: accentBold(m, L) }" />
                      <StarIcon v-else-if="m.icon === 'star'" style="width:15px;height:15px;"
                                :style="{ color: accentBold(m, L) }" />
                      <SparklesIcon v-else style="width:15px;height:15px;"
                                    :style="{ color: accentBold(m, L) }" />
                    </span>
                    <span class="text-[10px] leading-none"
                          :style="{
                            color: activeA_L === m.key ? L.inkPrimary : L.inkTertiary,
                            fontWeight: activeA_L === m.key ? '600' : '400',
                          }">
                      {{ m.name }}
                    </span>
                  </button>
                </div>
              </div>
            </div>

            <p class="text-[11px]" :style="{ color: L.inkTertiary }">
              Variant A uses flex-wrap so avatars flow naturally onto a second row rather than scrolling horizontally.
              Best for contexts where vertical space is available and the full roster needs to be visible at a glance.
            </p>
          </div>

          <!-- ── DARK ── -->
          <div class="rounded-2xl border p-6 space-y-8"
               :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

            <!-- Single-select, md -->
            <div class="space-y-3">
              <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Single-select — md (40px)</p>
              <div class="flex flex-wrap gap-4">
                <button
                  v-for="m in MEMBERS"
                  :key="m.key"
                  class="avatar-btn-a flex flex-col items-center gap-1.5"
                  @click="activeA_D = m.key"
                >
                  <span
                    class="relative flex items-center justify-center rounded-full overflow-hidden flex-shrink-0 transition-all"
                    :style="{
                      width: SIZE_MD + 'px',
                      height: SIZE_MD + 'px',
                      outline: activeA_D === m.key ? `2px solid ${D.accents.lavender.bold}` : '2px solid transparent',
                      outlineOffset: '2px',
                      background: m.type !== 'photo' ? accentSoft(m, D) : 'transparent',
                    }"
                  >
                    <img v-if="m.type === 'photo'" :src="m.photo" :alt="m.name"
                         class="w-full h-full object-cover" />
                    <span v-else-if="m.type === 'initials'"
                          class="font-semibold select-none"
                          :style="{ fontSize: FONT_MD, color: accentBold(m, D) }">
                      {{ m.initials }}
                    </span>
                    <HeartIcon v-else-if="m.icon === 'heart'"
                               :style="{ width: ICON_MD + 'px', height: ICON_MD + 'px', color: accentBold(m, D) }" />
                    <StarIcon v-else-if="m.icon === 'star'"
                              :style="{ width: ICON_MD + 'px', height: ICON_MD + 'px', color: accentBold(m, D) }" />
                    <SparklesIcon v-else
                                  :style="{ width: ICON_MD + 'px', height: ICON_MD + 'px', color: accentBold(m, D) }" />
                  </span>
                  <span class="text-[11px] leading-none transition-all"
                        :style="{
                          color: activeA_D === m.key ? D.inkPrimary : D.inkTertiary,
                          fontWeight: activeA_D === m.key ? '600' : '400',
                        }">
                    {{ m.name }}
                  </span>
                </button>
              </div>
            </div>

            <!-- Multi-select, md -->
            <div class="space-y-3">
              <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Multi-select — md (checkmark overlay)</p>
              <div class="flex flex-wrap gap-4">
                <button
                  v-for="m in MEMBERS"
                  :key="m.key"
                  class="avatar-btn-a flex flex-col items-center gap-1.5"
                  @click="toggleMulti(multiA_D, m.key)"
                >
                  <span
                    class="relative flex items-center justify-center rounded-full overflow-hidden flex-shrink-0 transition-all"
                    :style="{
                      width: SIZE_MD + 'px',
                      height: SIZE_MD + 'px',
                      outline: multiA_D.includes(m.key) ? `2px solid ${D.accents.lavender.bold}` : '2px solid transparent',
                      outlineOffset: '2px',
                      background: m.type !== 'photo' ? accentSoft(m, D) : 'transparent',
                    }"
                  >
                    <img v-if="m.type === 'photo'" :src="m.photo" :alt="m.name"
                         class="w-full h-full object-cover" />
                    <span v-else-if="m.type === 'initials'"
                          class="font-semibold select-none"
                          :style="{ fontSize: FONT_MD, color: accentBold(m, D) }">
                      {{ m.initials }}
                    </span>
                    <HeartIcon v-else-if="m.icon === 'heart'"
                               :style="{ width: ICON_MD + 'px', height: ICON_MD + 'px', color: accentBold(m, D) }" />
                    <StarIcon v-else-if="m.icon === 'star'"
                              :style="{ width: ICON_MD + 'px', height: ICON_MD + 'px', color: accentBold(m, D) }" />
                    <SparklesIcon v-else
                                  :style="{ width: ICON_MD + 'px', height: ICON_MD + 'px', color: accentBold(m, D) }" />
                    <!-- Checkmark overlay -->
                    <span v-if="multiA_D.includes(m.key)"
                          class="absolute inset-0 flex items-center justify-center rounded-full"
                          :style="{ background: `${D.accents.lavender.bold}CC` }">
                      <CheckIcon :style="{ width: '18px', height: '18px', color: D.inkInverse }" />
                    </span>
                  </span>
                  <span class="text-[11px] leading-none transition-all"
                        :style="{
                          color: multiA_D.includes(m.key) ? D.inkPrimary : D.inkTertiary,
                          fontWeight: multiA_D.includes(m.key) ? '600' : '400',
                        }">
                    {{ m.name }}
                  </span>
                </button>
              </div>
            </div>

            <p class="text-[11px]" :style="{ color: D.inkTertiary }">
              Dark ring: #B6A8E6 (lavender-bold) — vivid enough against the dark bg without glowing.
              Checkmark overlay uses the same bold at 80% opacity with dark inverse check.
            </p>
          </div>

        </div>
      </VariantFrame>
      <p class="mt-3 text-sm px-1" :style="{ color: L.inkSecondary }">
        Variant A is the most scannable — each member gets a dedicated vertical slot with name always visible.
        Works best on forms and detail pages where vertical space is available (task creation, vault share modal).
      </p>
    </section>


    <!-- ══════════════════════════════════════════════════════════════
         VARIANT B — Pill-shaped cards (avatar + name inline)
         Active: accent-soft fill + accent-bold text on pill.
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="B" caption="Rounded-full pill · avatar + name inline · overflow-x-auto on narrow width">
        <div class="w-full space-y-10">

          <!-- ── LIGHT ── -->
          <div class="rounded-2xl border p-6 space-y-8"
               :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>

            <!-- Single-select, md -->
            <div class="space-y-3">
              <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Single-select — md (40px avatar)</p>
              <div class="flex gap-2 flex-wrap">
                <button
                  v-for="m in MEMBERS"
                  :key="m.key"
                  class="avatar-pill-b inline-flex items-center gap-2 px-2 pr-3 h-12 rounded-full transition-all"
                  :style="activeB_L === m.key
                    ? { background: L.accents.lavender.soft, border: `1.5px solid ${L.accents.lavender.bold}` }
                    : { background: L.surfaceRaised, border: `1.5px solid ${L.borderSubtle}` }"
                  @click="activeB_L = m.key"
                >
                  <!-- Avatar circle inside pill -->
                  <span
                    class="flex items-center justify-center rounded-full overflow-hidden flex-shrink-0"
                    :style="{
                      width: SIZE_MD + 'px',
                      height: SIZE_MD + 'px',
                      background: m.type !== 'photo' ? accentSoft(m, L) : 'transparent',
                    }"
                  >
                    <img v-if="m.type === 'photo'" :src="m.photo" :alt="m.name"
                         class="w-full h-full object-cover" />
                    <span v-else-if="m.type === 'initials'"
                          class="font-semibold select-none"
                          :style="{ fontSize: FONT_MD, color: accentBold(m, L) }">
                      {{ m.initials }}
                    </span>
                    <HeartIcon v-else-if="m.icon === 'heart'"
                               :style="{ width: ICON_MD + 'px', height: ICON_MD + 'px', color: accentBold(m, L) }" />
                    <StarIcon v-else-if="m.icon === 'star'"
                              :style="{ width: ICON_MD + 'px', height: ICON_MD + 'px', color: accentBold(m, L) }" />
                    <SparklesIcon v-else
                                  :style="{ width: ICON_MD + 'px', height: ICON_MD + 'px', color: accentBold(m, L) }" />
                  </span>
                  <span class="text-[13px] font-medium leading-none"
                        :style="{ color: activeB_L === m.key ? L.accents.lavender.bold : L.inkSecondary }">
                    {{ m.name }}
                  </span>
                </button>
              </div>
            </div>

            <!-- Single-select, lg -->
            <div class="space-y-3">
              <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Single-select — lg (56px avatar)</p>
              <div class="flex gap-2 flex-wrap">
                <button
                  v-for="m in MEMBERS"
                  :key="m.key"
                  class="avatar-pill-b inline-flex items-center gap-2.5 px-2 pr-4 h-[68px] rounded-full transition-all"
                  :style="activeB_L === m.key
                    ? { background: L.accents.lavender.soft, border: `1.5px solid ${L.accents.lavender.bold}` }
                    : { background: L.surfaceRaised, border: `1.5px solid ${L.borderSubtle}` }"
                  @click="activeB_L = m.key"
                >
                  <span
                    class="flex items-center justify-center rounded-full overflow-hidden flex-shrink-0"
                    :style="{
                      width: SIZE_LG + 'px',
                      height: SIZE_LG + 'px',
                      background: m.type !== 'photo' ? accentSoft(m, L) : 'transparent',
                    }"
                  >
                    <img v-if="m.type === 'photo'" :src="m.photo" :alt="m.name"
                         class="w-full h-full object-cover" />
                    <span v-else-if="m.type === 'initials'"
                          class="font-semibold select-none"
                          :style="{ fontSize: FONT_LG, color: accentBold(m, L) }">
                      {{ m.initials }}
                    </span>
                    <HeartIcon v-else-if="m.icon === 'heart'"
                               :style="{ width: ICON_LG + 'px', height: ICON_LG + 'px', color: accentBold(m, L) }" />
                    <StarIcon v-else-if="m.icon === 'star'"
                              :style="{ width: ICON_LG + 'px', height: ICON_LG + 'px', color: accentBold(m, L) }" />
                    <SparklesIcon v-else
                                  :style="{ width: ICON_LG + 'px', height: ICON_LG + 'px', color: accentBold(m, L) }" />
                  </span>
                  <span class="text-[15px] font-medium leading-none"
                        :style="{ color: activeB_L === m.key ? L.accents.lavender.bold : L.inkSecondary }">
                    {{ m.name }}
                  </span>
                </button>
              </div>
            </div>

            <!-- Multi-select, md -->
            <div class="space-y-3">
              <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Multi-select — md (checkmark overlay)</p>
              <div class="flex gap-2 flex-wrap">
                <button
                  v-for="m in MEMBERS"
                  :key="m.key"
                  class="avatar-pill-b inline-flex items-center gap-2 px-2 pr-3 h-12 rounded-full transition-all"
                  :style="multiB_L.includes(m.key)
                    ? { background: L.accents.lavender.soft, border: `1.5px solid ${L.accents.lavender.bold}` }
                    : { background: L.surfaceRaised, border: `1.5px solid ${L.borderSubtle}` }"
                  @click="toggleMulti(multiB_L, m.key)"
                >
                  <span
                    class="relative flex items-center justify-center rounded-full overflow-hidden flex-shrink-0"
                    :style="{
                      width: SIZE_MD + 'px',
                      height: SIZE_MD + 'px',
                      background: m.type !== 'photo' ? accentSoft(m, L) : 'transparent',
                    }"
                  >
                    <img v-if="m.type === 'photo'" :src="m.photo" :alt="m.name"
                         class="w-full h-full object-cover" />
                    <span v-else-if="m.type === 'initials'"
                          class="font-semibold select-none"
                          :style="{ fontSize: FONT_MD, color: accentBold(m, L) }">
                      {{ m.initials }}
                    </span>
                    <HeartIcon v-else-if="m.icon === 'heart'"
                               :style="{ width: ICON_MD + 'px', height: ICON_MD + 'px', color: accentBold(m, L) }" />
                    <StarIcon v-else-if="m.icon === 'star'"
                              :style="{ width: ICON_MD + 'px', height: ICON_MD + 'px', color: accentBold(m, L) }" />
                    <SparklesIcon v-else
                                  :style="{ width: ICON_MD + 'px', height: ICON_MD + 'px', color: accentBold(m, L) }" />
                    <!-- Checkmark overlay on the circle only -->
                    <span v-if="multiB_L.includes(m.key)"
                          class="absolute inset-0 flex items-center justify-center rounded-full"
                          :style="{ background: `${L.accents.lavender.bold}CC` }">
                      <CheckIcon :style="{ width: '16px', height: '16px', color: '#FFFFFF' }" />
                    </span>
                  </span>
                  <span class="text-[13px] font-medium leading-none"
                        :style="{ color: multiB_L.includes(m.key) ? L.accents.lavender.bold : L.inkSecondary }">
                    {{ m.name }}
                  </span>
                </button>
              </div>
            </div>

            <!-- Disabled state -->
            <div class="space-y-3">
              <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Disabled avatar (3rd member)</p>
              <div class="flex gap-2 flex-wrap">
                <button
                  v-for="(m, idx) in MEMBERS"
                  :key="m.key"
                  class="inline-flex items-center gap-2 px-2 pr-3 h-12 rounded-full transition-all"
                  :class="idx !== 2 ? 'avatar-pill-b' : 'cursor-not-allowed'"
                  :disabled="idx === 2"
                  :style="idx === 2
                    ? { background: L.surfaceRaised, border: `1.5px solid ${L.borderSubtle}`, filter: 'grayscale(1)', opacity: '0.4' }
                    : activeB_L === m.key
                      ? { background: L.accents.lavender.soft, border: `1.5px solid ${L.accents.lavender.bold}` }
                      : { background: L.surfaceRaised, border: `1.5px solid ${L.borderSubtle}` }"
                  @click="idx !== 2 && (activeB_L = m.key)"
                >
                  <span
                    class="flex items-center justify-center rounded-full overflow-hidden flex-shrink-0"
                    :style="{
                      width: SIZE_MD + 'px',
                      height: SIZE_MD + 'px',
                      background: m.type !== 'photo' ? accentSoft(m, L) : 'transparent',
                    }"
                  >
                    <img v-if="m.type === 'photo'" :src="m.photo" :alt="m.name"
                         class="w-full h-full object-cover" />
                    <span v-else-if="m.type === 'initials'"
                          class="font-semibold select-none"
                          :style="{ fontSize: FONT_MD, color: accentBold(m, L) }">
                      {{ m.initials }}
                    </span>
                    <HeartIcon v-else-if="m.icon === 'heart'"
                               :style="{ width: ICON_MD + 'px', height: ICON_MD + 'px', color: accentBold(m, L) }" />
                    <StarIcon v-else-if="m.icon === 'star'"
                              :style="{ width: ICON_MD + 'px', height: ICON_MD + 'px', color: accentBold(m, L) }" />
                    <SparklesIcon v-else
                                  :style="{ width: ICON_MD + 'px', height: ICON_MD + 'px', color: accentBold(m, L) }" />
                  </span>
                  <span class="text-[13px] font-medium leading-none"
                        :style="{ color: idx === 2 ? L.inkTertiary : (activeB_L === m.key ? L.accents.lavender.bold : L.inkSecondary) }">
                    {{ m.name }}
                  </span>
                </button>
              </div>
            </div>

            <!-- 320px overflow demo -->
            <div class="space-y-2">
              <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">8 members · max-w-[320px] · overflow-x-auto</p>
              <div class="max-w-[320px] rounded-xl border p-3"
                   :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle }">
                <div class="flex gap-2 overflow-x-auto pb-1" style="scrollbar-width: none; -ms-overflow-style: none;">
                  <button
                    v-for="m in MEMBERS_OVERFLOW"
                    :key="m.key"
                    class="avatar-pill-b flex-shrink-0 inline-flex items-center gap-1.5 px-1.5 pr-2.5 h-9 rounded-full transition-all"
                    :style="activeB_L === m.key
                      ? { background: L.accents.lavender.soft, border: `1.5px solid ${L.accents.lavender.bold}` }
                      : { background: L.surfaceRaised, border: `1.5px solid ${L.borderSubtle}` }"
                    @click="activeB_L = m.key"
                  >
                    <span
                      class="flex items-center justify-center rounded-full overflow-hidden flex-shrink-0"
                      :style="{
                        width: '28px', height: '28px',
                        background: m.type !== 'photo' ? accentSoft(m, L) : 'transparent',
                      }"
                    >
                      <img v-if="m.type === 'photo'" :src="m.photo" :alt="m.name"
                           class="w-full h-full object-cover" />
                      <span v-else-if="m.type === 'initials'"
                            class="font-semibold select-none text-[10px]"
                            :style="{ color: accentBold(m, L) }">
                        {{ m.initials }}
                      </span>
                      <HeartIcon v-else-if="m.icon === 'heart'" style="width:12px;height:12px;"
                                 :style="{ color: accentBold(m, L) }" />
                      <StarIcon v-else-if="m.icon === 'star'" style="width:12px;height:12px;"
                                :style="{ color: accentBold(m, L) }" />
                      <SparklesIcon v-else style="width:12px;height:12px;"
                                    :style="{ color: accentBold(m, L) }" />
                    </span>
                    <span class="text-[12px] font-medium leading-none whitespace-nowrap"
                          :style="{ color: activeB_L === m.key ? L.accents.lavender.bold : L.inkSecondary }">
                      {{ m.name }}
                    </span>
                  </button>
                </div>
              </div>
            </div>

            <p class="text-[11px]" :style="{ color: L.inkTertiary }">
              Variant B uses pill language (rounded-full) consistent with TagChipRow and TabPillGroup.
              The fill on active signals the selection state with generous tap target — great for mobile.
            </p>
          </div>

          <!-- ── DARK ── -->
          <div class="rounded-2xl border p-6 space-y-8"
               :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

            <!-- Single-select, md -->
            <div class="space-y-3">
              <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Single-select — md (40px avatar)</p>
              <div class="flex gap-2 flex-wrap">
                <button
                  v-for="m in MEMBERS"
                  :key="m.key"
                  class="avatar-pill-b-dk inline-flex items-center gap-2 px-2 pr-3 h-12 rounded-full transition-all"
                  :style="activeB_D === m.key
                    ? { background: D.accents.lavender.soft, border: `1.5px solid ${D.accents.lavender.bold}` }
                    : { background: D.surfaceRaised, border: `1.5px solid ${D.borderSubtle}` }"
                  @click="activeB_D = m.key"
                >
                  <span
                    class="flex items-center justify-center rounded-full overflow-hidden flex-shrink-0"
                    :style="{
                      width: SIZE_MD + 'px',
                      height: SIZE_MD + 'px',
                      background: m.type !== 'photo' ? accentSoft(m, D) : 'transparent',
                    }"
                  >
                    <img v-if="m.type === 'photo'" :src="m.photo" :alt="m.name"
                         class="w-full h-full object-cover" />
                    <span v-else-if="m.type === 'initials'"
                          class="font-semibold select-none"
                          :style="{ fontSize: FONT_MD, color: accentBold(m, D) }">
                      {{ m.initials }}
                    </span>
                    <HeartIcon v-else-if="m.icon === 'heart'"
                               :style="{ width: ICON_MD + 'px', height: ICON_MD + 'px', color: accentBold(m, D) }" />
                    <StarIcon v-else-if="m.icon === 'star'"
                              :style="{ width: ICON_MD + 'px', height: ICON_MD + 'px', color: accentBold(m, D) }" />
                    <SparklesIcon v-else
                                  :style="{ width: ICON_MD + 'px', height: ICON_MD + 'px', color: accentBold(m, D) }" />
                  </span>
                  <span class="text-[13px] font-medium leading-none"
                        :style="{ color: activeB_D === m.key ? D.accents.lavender.bold : D.inkSecondary }">
                    {{ m.name }}
                  </span>
                </button>
              </div>
            </div>

            <!-- Multi-select, md -->
            <div class="space-y-3">
              <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Multi-select — md (checkmark overlay)</p>
              <div class="flex gap-2 flex-wrap">
                <button
                  v-for="m in MEMBERS"
                  :key="m.key"
                  class="avatar-pill-b-dk inline-flex items-center gap-2 px-2 pr-3 h-12 rounded-full transition-all"
                  :style="multiB_D.includes(m.key)
                    ? { background: D.accents.lavender.soft, border: `1.5px solid ${D.accents.lavender.bold}` }
                    : { background: D.surfaceRaised, border: `1.5px solid ${D.borderSubtle}` }"
                  @click="toggleMulti(multiB_D, m.key)"
                >
                  <span
                    class="relative flex items-center justify-center rounded-full overflow-hidden flex-shrink-0"
                    :style="{
                      width: SIZE_MD + 'px',
                      height: SIZE_MD + 'px',
                      background: m.type !== 'photo' ? accentSoft(m, D) : 'transparent',
                    }"
                  >
                    <img v-if="m.type === 'photo'" :src="m.photo" :alt="m.name"
                         class="w-full h-full object-cover" />
                    <span v-else-if="m.type === 'initials'"
                          class="font-semibold select-none"
                          :style="{ fontSize: FONT_MD, color: accentBold(m, D) }">
                      {{ m.initials }}
                    </span>
                    <HeartIcon v-else-if="m.icon === 'heart'"
                               :style="{ width: ICON_MD + 'px', height: ICON_MD + 'px', color: accentBold(m, D) }" />
                    <StarIcon v-else-if="m.icon === 'star'"
                              :style="{ width: ICON_MD + 'px', height: ICON_MD + 'px', color: accentBold(m, D) }" />
                    <SparklesIcon v-else
                                  :style="{ width: ICON_MD + 'px', height: ICON_MD + 'px', color: accentBold(m, D) }" />
                    <span v-if="multiB_D.includes(m.key)"
                          class="absolute inset-0 flex items-center justify-center rounded-full"
                          :style="{ background: `${D.accents.lavender.bold}CC` }">
                      <CheckIcon :style="{ width: '16px', height: '16px', color: D.inkInverse }" />
                    </span>
                  </span>
                  <span class="text-[13px] font-medium leading-none"
                        :style="{ color: multiB_D.includes(m.key) ? D.accents.lavender.bold : D.inkSecondary }">
                    {{ m.name }}
                  </span>
                </button>
              </div>
            </div>

            <p class="text-[11px]" :style="{ color: D.inkTertiary }">
              Dark: #302A48 pill fill (lavender-soft) + #B6A8E6 text on active.
              Larger touch target vs Variant A makes this preferable on mobile assignment flows.
            </p>
          </div>

        </div>
      </VariantFrame>
      <p class="mt-3 text-sm px-1" :style="{ color: L.inkSecondary }">
        Variant B gives each member a generous pill-shaped tap target and keeps the name always visible inline.
        Ideal for task assignment and kudos modals where up to 6 members fit in a single scrollable row.
      </p>
    </section>


    <!-- ══════════════════════════════════════════════════════════════
         VARIANT C — Compact overlapping stack
         Avatars overlap ~30%. Selected pops forward with ring + name.
         Overflow: "+N" trailing chip.
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="C" caption="Overlapping avatar stack · selected pops forward + name tooltip · +N overflow chip">
        <div class="w-full space-y-10">

          <!-- ── LIGHT ── -->
          <div class="rounded-2xl border p-6 space-y-8"
               :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>

            <!-- Single-select, md -->
            <div class="space-y-3">
              <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Single-select — md (40px) · click to reveal name</p>
              <div class="flex items-center gap-3">
                <!-- Stack -->
                <div class="relative flex items-center" style="height: 48px;">
                  <button
                    v-for="(m, idx) in MEMBERS"
                    :key="m.key"
                    class="avatar-stack-btn absolute flex items-center justify-center rounded-full overflow-hidden flex-shrink-0 transition-all"
                    :style="{
                      width: SIZE_MD + 'px',
                      height: SIZE_MD + 'px',
                      left: (idx * 28) + 'px',
                      zIndex: activeC_L === m.key ? 20 : (10 - idx),
                      outline: activeC_L === m.key ? `2px solid ${L.accents.lavender.bold}` : `2px solid ${L.surfaceApp}`,
                      outlineOffset: '1px',
                      background: m.type !== 'photo' ? accentSoft(m, L) : 'transparent',
                      transform: activeC_L === m.key ? 'translateY(-4px) scale(1.08)' : 'translateY(0) scale(1)',
                    }"
                    @click="activeC_L = m.key"
                  >
                    <img v-if="m.type === 'photo'" :src="m.photo" :alt="m.name"
                         class="w-full h-full object-cover" />
                    <span v-else-if="m.type === 'initials'"
                          class="font-semibold select-none"
                          :style="{ fontSize: FONT_MD, color: accentBold(m, L) }">
                      {{ m.initials }}
                    </span>
                    <HeartIcon v-else-if="m.icon === 'heart'"
                               :style="{ width: ICON_MD + 'px', height: ICON_MD + 'px', color: accentBold(m, L) }" />
                    <StarIcon v-else-if="m.icon === 'star'"
                              :style="{ width: ICON_MD + 'px', height: ICON_MD + 'px', color: accentBold(m, L) }" />
                    <SparklesIcon v-else
                                  :style="{ width: ICON_MD + 'px', height: ICON_MD + 'px', color: accentBold(m, L) }" />
                  </button>
                  <!-- Spacer so the row takes width -->
                  <span :style="{ display: 'block', width: (MEMBERS.length * 28 + 12) + 'px' }" />
                </div>
                <!-- Revealed name for selected -->
                <transition name="fade-slide">
                  <span
                    v-if="activeC_L"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[13px] font-semibold"
                    :style="{ background: L.accents.lavender.soft, color: L.accents.lavender.bold }"
                  >
                    {{ MEMBERS.find(m => m.key === activeC_L)?.name }}
                  </span>
                </transition>
              </div>
            </div>

            <!-- Single-select, lg -->
            <div class="space-y-3">
              <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Single-select — lg (56px)</p>
              <div class="flex items-center gap-4">
                <div class="relative flex items-center" style="height: 64px;">
                  <button
                    v-for="(m, idx) in MEMBERS"
                    :key="m.key"
                    class="avatar-stack-btn absolute flex items-center justify-center rounded-full overflow-hidden flex-shrink-0 transition-all"
                    :style="{
                      width: SIZE_LG + 'px',
                      height: SIZE_LG + 'px',
                      left: (idx * 40) + 'px',
                      zIndex: activeC_L === m.key ? 20 : (10 - idx),
                      outline: activeC_L === m.key ? `2px solid ${L.accents.lavender.bold}` : `2px solid ${L.surfaceApp}`,
                      outlineOffset: '1px',
                      background: m.type !== 'photo' ? accentSoft(m, L) : 'transparent',
                      transform: activeC_L === m.key ? 'translateY(-5px) scale(1.07)' : 'translateY(0) scale(1)',
                    }"
                    @click="activeC_L = m.key"
                  >
                    <img v-if="m.type === 'photo'" :src="m.photo" :alt="m.name"
                         class="w-full h-full object-cover" />
                    <span v-else-if="m.type === 'initials'"
                          class="font-semibold select-none"
                          :style="{ fontSize: FONT_LG, color: accentBold(m, L) }">
                      {{ m.initials }}
                    </span>
                    <HeartIcon v-else-if="m.icon === 'heart'"
                               :style="{ width: ICON_LG + 'px', height: ICON_LG + 'px', color: accentBold(m, L) }" />
                    <StarIcon v-else-if="m.icon === 'star'"
                              :style="{ width: ICON_LG + 'px', height: ICON_LG + 'px', color: accentBold(m, L) }" />
                    <SparklesIcon v-else
                                  :style="{ width: ICON_LG + 'px', height: ICON_LG + 'px', color: accentBold(m, L) }" />
                  </button>
                  <span :style="{ display: 'block', width: (MEMBERS.length * 40 + 16) + 'px' }" />
                </div>
                <transition name="fade-slide">
                  <span
                    v-if="activeC_L"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-full text-[14px] font-semibold"
                    :style="{ background: L.accents.lavender.soft, color: L.accents.lavender.bold }"
                  >
                    {{ MEMBERS.find(m => m.key === activeC_L)?.name }}
                  </span>
                </transition>
              </div>
            </div>

            <!-- Multi-select, md -->
            <div class="space-y-3">
              <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Multi-select — md (checkmark overlay on selected)</p>
              <div class="flex items-center gap-3">
                <div class="relative flex items-center" style="height: 48px;">
                  <button
                    v-for="(m, idx) in MEMBERS"
                    :key="m.key"
                    class="avatar-stack-btn absolute flex items-center justify-center rounded-full overflow-hidden flex-shrink-0 transition-all"
                    :style="{
                      width: SIZE_MD + 'px',
                      height: SIZE_MD + 'px',
                      left: (idx * 28) + 'px',
                      zIndex: multiC_L.includes(m.key) ? (20 + idx) : (10 - idx),
                      outline: multiC_L.includes(m.key) ? `2px solid ${L.accents.lavender.bold}` : `2px solid ${L.surfaceApp}`,
                      outlineOffset: '1px',
                      background: m.type !== 'photo' ? accentSoft(m, L) : 'transparent',
                      transform: multiC_L.includes(m.key) ? 'translateY(-4px) scale(1.08)' : 'translateY(0) scale(1)',
                    }"
                    @click="toggleMulti(multiC_L, m.key)"
                  >
                    <img v-if="m.type === 'photo'" :src="m.photo" :alt="m.name"
                         class="w-full h-full object-cover" />
                    <span v-else-if="m.type === 'initials'"
                          class="font-semibold select-none"
                          :style="{ fontSize: FONT_MD, color: accentBold(m, L) }">
                      {{ m.initials }}
                    </span>
                    <HeartIcon v-else-if="m.icon === 'heart'"
                               :style="{ width: ICON_MD + 'px', height: ICON_MD + 'px', color: accentBold(m, L) }" />
                    <StarIcon v-else-if="m.icon === 'star'"
                              :style="{ width: ICON_MD + 'px', height: ICON_MD + 'px', color: accentBold(m, L) }" />
                    <SparklesIcon v-else
                                  :style="{ width: ICON_MD + 'px', height: ICON_MD + 'px', color: accentBold(m, L) }" />
                    <span v-if="multiC_L.includes(m.key)"
                          class="absolute inset-0 flex items-center justify-center rounded-full"
                          :style="{ background: `${L.accents.lavender.bold}CC` }">
                      <CheckIcon :style="{ width: '18px', height: '18px', color: '#FFFFFF' }" />
                    </span>
                  </button>
                  <span :style="{ display: 'block', width: (MEMBERS.length * 28 + 12) + 'px' }" />
                </div>
                <span class="text-[12px]" :style="{ color: L.inkTertiary }">
                  {{ multiC_L.length }} selected
                </span>
              </div>
            </div>

            <!-- Disabled state -->
            <div class="space-y-3">
              <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Disabled avatar (3rd member)</p>
              <div class="flex items-center gap-3">
                <div class="relative flex items-center" style="height: 48px;">
                  <button
                    v-for="(m, idx) in MEMBERS"
                    :key="m.key"
                    class="absolute flex items-center justify-center rounded-full overflow-hidden flex-shrink-0 transition-all"
                    :class="idx !== 2 ? 'avatar-stack-btn' : 'cursor-not-allowed'"
                    :disabled="idx === 2"
                    :style="{
                      width: SIZE_MD + 'px',
                      height: SIZE_MD + 'px',
                      left: (idx * 28) + 'px',
                      zIndex: (idx !== 2 && activeC_L === m.key) ? 20 : (10 - idx),
                      outline: (idx !== 2 && activeC_L === m.key) ? `2px solid ${L.accents.lavender.bold}` : `2px solid ${L.surfaceApp}`,
                      outlineOffset: '1px',
                      background: m.type !== 'photo' ? accentSoft(m, L) : 'transparent',
                      filter: idx === 2 ? 'grayscale(1)' : 'none',
                      opacity: idx === 2 ? '0.4' : '1',
                      transform: (idx !== 2 && activeC_L === m.key) ? 'translateY(-4px) scale(1.08)' : 'translateY(0) scale(1)',
                    }"
                    @click="idx !== 2 && (activeC_L = m.key)"
                  >
                    <img v-if="m.type === 'photo'" :src="m.photo" :alt="m.name"
                         class="w-full h-full object-cover" />
                    <span v-else-if="m.type === 'initials'"
                          class="font-semibold select-none"
                          :style="{ fontSize: FONT_MD, color: accentBold(m, L) }">
                      {{ m.initials }}
                    </span>
                    <HeartIcon v-else-if="m.icon === 'heart'"
                               :style="{ width: ICON_MD + 'px', height: ICON_MD + 'px', color: accentBold(m, L) }" />
                    <StarIcon v-else-if="m.icon === 'star'"
                              :style="{ width: ICON_MD + 'px', height: ICON_MD + 'px', color: accentBold(m, L) }" />
                    <SparklesIcon v-else
                                  :style="{ width: ICON_MD + 'px', height: ICON_MD + 'px', color: accentBold(m, L) }" />
                  </button>
                  <span :style="{ display: 'block', width: (MEMBERS.length * 28 + 12) + 'px' }" />
                </div>
              </div>
            </div>

            <!-- 320px overflow demo with +N chip -->
            <div class="space-y-2">
              <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">8 members · max-w-[320px] · first 5 visible + +3 chip</p>
              <div class="max-w-[320px] rounded-xl border p-3"
                   :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle }">
                <div class="flex items-center gap-3">
                  <div class="relative flex items-center" style="height: 48px;">
                    <button
                      v-for="(m, idx) in MEMBERS_OVERFLOW.slice(0, STACK_VISIBLE)"
                      :key="m.key"
                      class="avatar-stack-btn absolute flex items-center justify-center rounded-full overflow-hidden flex-shrink-0 transition-all"
                      :style="{
                        width: SIZE_MD + 'px',
                        height: SIZE_MD + 'px',
                        left: (idx * 28) + 'px',
                        zIndex: activeC_L === m.key ? 20 : (10 - idx),
                        outline: activeC_L === m.key ? `2px solid ${L.accents.lavender.bold}` : `2px solid ${L.surfaceRaised}`,
                        outlineOffset: '1px',
                        background: m.type !== 'photo' ? accentSoft(m, L) : 'transparent',
                        transform: activeC_L === m.key ? 'translateY(-4px) scale(1.08)' : 'translateY(0) scale(1)',
                      }"
                      @click="activeC_L = m.key"
                    >
                      <img v-if="m.type === 'photo'" :src="m.photo" :alt="m.name"
                           class="w-full h-full object-cover" />
                      <span v-else-if="m.type === 'initials'"
                            class="font-semibold select-none"
                            :style="{ fontSize: FONT_MD, color: accentBold(m, L) }">
                        {{ m.initials }}
                      </span>
                      <HeartIcon v-else-if="m.icon === 'heart'"
                                 :style="{ width: ICON_MD + 'px', height: ICON_MD + 'px', color: accentBold(m, L) }" />
                      <StarIcon v-else-if="m.icon === 'star'"
                                :style="{ width: ICON_MD + 'px', height: ICON_MD + 'px', color: accentBold(m, L) }" />
                      <SparklesIcon v-else
                                    :style="{ width: ICON_MD + 'px', height: ICON_MD + 'px', color: accentBold(m, L) }" />
                    </button>
                    <!-- +N chip -->
                    <span
                      v-if="stackOverflowCount > 0"
                      class="absolute flex items-center justify-center rounded-full text-[11px] font-semibold"
                      :style="{
                        width: SIZE_MD + 'px',
                        height: SIZE_MD + 'px',
                        left: (STACK_VISIBLE * 28) + 'px',
                        zIndex: 5,
                        background: L.borderStrong,
                        color: L.inkPrimary,
                        outline: `2px solid ${L.surfaceRaised}`,
                        outlineOffset: '1px',
                      }"
                    >+{{ stackOverflowCount }}</span>
                    <span :style="{ display: 'block', width: ((STACK_VISIBLE + 1) * 28 + 12) + 'px' }" />
                  </div>
                  <transition name="fade-slide">
                    <span
                      v-if="activeC_L"
                      class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[12px] font-semibold"
                      :style="{ background: L.accents.lavender.soft, color: L.accents.lavender.bold }"
                    >
                      {{ MEMBERS_OVERFLOW.slice(0, STACK_VISIBLE).find(m => m.key === activeC_L)?.name ?? 'More...' }}
                    </span>
                  </transition>
                </div>
              </div>
            </div>

            <p class="text-[11px]" :style="{ color: L.inkTertiary }">
              Stack overlap = 30% (28px offset for 40px avatar). Selected avatar pops up 4px + scales 1.08x.
              Name chip appears to the right of the stack on selection, fades in smoothly.
              +N chip uses border-strong fill — neutral, not accent — so it reads as overflow, not a member.
            </p>
          </div>

          <!-- ── DARK ── -->
          <div class="rounded-2xl border p-6 space-y-8"
               :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

            <!-- Single-select, md -->
            <div class="space-y-3">
              <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Single-select — md (40px) · click to reveal name</p>
              <div class="flex items-center gap-3">
                <div class="relative flex items-center" style="height: 48px;">
                  <button
                    v-for="(m, idx) in MEMBERS"
                    :key="m.key"
                    class="avatar-stack-btn absolute flex items-center justify-center rounded-full overflow-hidden flex-shrink-0 transition-all"
                    :style="{
                      width: SIZE_MD + 'px',
                      height: SIZE_MD + 'px',
                      left: (idx * 28) + 'px',
                      zIndex: activeC_D === m.key ? 20 : (10 - idx),
                      outline: activeC_D === m.key ? `2px solid ${D.accents.lavender.bold}` : `2px solid ${D.surfaceApp}`,
                      outlineOffset: '1px',
                      background: m.type !== 'photo' ? accentSoft(m, D) : 'transparent',
                      transform: activeC_D === m.key ? 'translateY(-4px) scale(1.08)' : 'translateY(0) scale(1)',
                    }"
                    @click="activeC_D = m.key"
                  >
                    <img v-if="m.type === 'photo'" :src="m.photo" :alt="m.name"
                         class="w-full h-full object-cover" />
                    <span v-else-if="m.type === 'initials'"
                          class="font-semibold select-none"
                          :style="{ fontSize: FONT_MD, color: accentBold(m, D) }">
                      {{ m.initials }}
                    </span>
                    <HeartIcon v-else-if="m.icon === 'heart'"
                               :style="{ width: ICON_MD + 'px', height: ICON_MD + 'px', color: accentBold(m, D) }" />
                    <StarIcon v-else-if="m.icon === 'star'"
                              :style="{ width: ICON_MD + 'px', height: ICON_MD + 'px', color: accentBold(m, D) }" />
                    <SparklesIcon v-else
                                  :style="{ width: ICON_MD + 'px', height: ICON_MD + 'px', color: accentBold(m, D) }" />
                  </button>
                  <span :style="{ display: 'block', width: (MEMBERS.length * 28 + 12) + 'px' }" />
                </div>
                <transition name="fade-slide">
                  <span
                    v-if="activeC_D"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[13px] font-semibold"
                    :style="{ background: D.accents.lavender.soft, color: D.accents.lavender.bold }"
                  >
                    {{ MEMBERS.find(m => m.key === activeC_D)?.name }}
                  </span>
                </transition>
              </div>
            </div>

            <!-- Multi-select, md -->
            <div class="space-y-3">
              <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Multi-select — md (checkmark overlay)</p>
              <div class="flex items-center gap-3">
                <div class="relative flex items-center" style="height: 48px;">
                  <button
                    v-for="(m, idx) in MEMBERS"
                    :key="m.key"
                    class="avatar-stack-btn absolute flex items-center justify-center rounded-full overflow-hidden flex-shrink-0 transition-all"
                    :style="{
                      width: SIZE_MD + 'px',
                      height: SIZE_MD + 'px',
                      left: (idx * 28) + 'px',
                      zIndex: multiC_D.includes(m.key) ? (20 + idx) : (10 - idx),
                      outline: multiC_D.includes(m.key) ? `2px solid ${D.accents.lavender.bold}` : `2px solid ${D.surfaceApp}`,
                      outlineOffset: '1px',
                      background: m.type !== 'photo' ? accentSoft(m, D) : 'transparent',
                      transform: multiC_D.includes(m.key) ? 'translateY(-4px) scale(1.08)' : 'translateY(0) scale(1)',
                    }"
                    @click="toggleMulti(multiC_D, m.key)"
                  >
                    <img v-if="m.type === 'photo'" :src="m.photo" :alt="m.name"
                         class="w-full h-full object-cover" />
                    <span v-else-if="m.type === 'initials'"
                          class="font-semibold select-none"
                          :style="{ fontSize: FONT_MD, color: accentBold(m, D) }">
                      {{ m.initials }}
                    </span>
                    <HeartIcon v-else-if="m.icon === 'heart'"
                               :style="{ width: ICON_MD + 'px', height: ICON_MD + 'px', color: accentBold(m, D) }" />
                    <StarIcon v-else-if="m.icon === 'star'"
                              :style="{ width: ICON_MD + 'px', height: ICON_MD + 'px', color: accentBold(m, D) }" />
                    <SparklesIcon v-else
                                  :style="{ width: ICON_MD + 'px', height: ICON_MD + 'px', color: accentBold(m, D) }" />
                    <span v-if="multiC_D.includes(m.key)"
                          class="absolute inset-0 flex items-center justify-center rounded-full"
                          :style="{ background: `${D.accents.lavender.bold}CC` }">
                      <CheckIcon :style="{ width: '18px', height: '18px', color: D.inkInverse }" />
                    </span>
                  </button>
                  <span :style="{ display: 'block', width: (MEMBERS.length * 28 + 12) + 'px' }" />
                </div>
                <span class="text-[12px]" :style="{ color: D.inkTertiary }">
                  {{ multiC_D.length }} selected
                </span>
              </div>
            </div>

            <!-- Dark +N overflow demo -->
            <div class="space-y-2">
              <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">8 members · +3 chip (dark)</p>
              <div class="max-w-[320px] rounded-xl border p-3"
                   :style="{ background: D.surfaceRaised, borderColor: D.borderSubtle }">
                <div class="flex items-center gap-3">
                  <div class="relative flex items-center" style="height: 48px;">
                    <button
                      v-for="(m, idx) in MEMBERS_OVERFLOW.slice(0, STACK_VISIBLE)"
                      :key="m.key"
                      class="avatar-stack-btn absolute flex items-center justify-center rounded-full overflow-hidden flex-shrink-0 transition-all"
                      :style="{
                        width: SIZE_MD + 'px',
                        height: SIZE_MD + 'px',
                        left: (idx * 28) + 'px',
                        zIndex: activeC_D === m.key ? 20 : (10 - idx),
                        outline: activeC_D === m.key ? `2px solid ${D.accents.lavender.bold}` : `2px solid ${D.surfaceRaised}`,
                        outlineOffset: '1px',
                        background: m.type !== 'photo' ? accentSoft(m, D) : 'transparent',
                        transform: activeC_D === m.key ? 'translateY(-4px) scale(1.08)' : 'translateY(0) scale(1)',
                      }"
                      @click="activeC_D = m.key"
                    >
                      <img v-if="m.type === 'photo'" :src="m.photo" :alt="m.name"
                           class="w-full h-full object-cover" />
                      <span v-else-if="m.type === 'initials'"
                            class="font-semibold select-none"
                            :style="{ fontSize: FONT_MD, color: accentBold(m, D) }">
                        {{ m.initials }}
                      </span>
                      <HeartIcon v-else-if="m.icon === 'heart'"
                                 :style="{ width: ICON_MD + 'px', height: ICON_MD + 'px', color: accentBold(m, D) }" />
                      <StarIcon v-else-if="m.icon === 'star'"
                                :style="{ width: ICON_MD + 'px', height: ICON_MD + 'px', color: accentBold(m, D) }" />
                      <SparklesIcon v-else
                                    :style="{ width: ICON_MD + 'px', height: ICON_MD + 'px', color: accentBold(m, D) }" />
                    </button>
                    <!-- +N chip dark -->
                    <span
                      v-if="stackOverflowCount > 0"
                      class="absolute flex items-center justify-center rounded-full text-[11px] font-semibold"
                      :style="{
                        width: SIZE_MD + 'px',
                        height: SIZE_MD + 'px',
                        left: (STACK_VISIBLE * 28) + 'px',
                        zIndex: 5,
                        background: D.borderStrong,
                        color: D.inkPrimary,
                        outline: `2px solid ${D.surfaceRaised}`,
                        outlineOffset: '1px',
                      }"
                    >+{{ stackOverflowCount }}</span>
                    <span :style="{ display: 'block', width: ((STACK_VISIBLE + 1) * 28 + 12) + 'px' }" />
                  </div>
                  <transition name="fade-slide">
                    <span
                      v-if="activeC_D"
                      class="inline-flex items-center px-2.5 py-1 rounded-full text-[12px] font-semibold"
                      :style="{ background: D.accents.lavender.soft, color: D.accents.lavender.bold }"
                    >
                      {{ MEMBERS_OVERFLOW.slice(0, STACK_VISIBLE).find(m => m.key === activeC_D)?.name ?? 'More...' }}
                    </span>
                  </transition>
                </div>
              </div>
            </div>

            <p class="text-[11px]" :style="{ color: D.inkTertiary }">
              Dark: separator ring uses D.surfaceApp (#141311) so avatars feel naturally separated without a visible border.
              Active ring: #B6A8E6 — vivid on the dark background. The +3 chip uses border-strong (#403E3A) fill — neutral, not accent.
            </p>
          </div>

        </div>
      </VariantFrame>
      <p class="mt-3 text-sm px-1" :style="{ color: L.inkSecondary }">
        Variant C is the most space-efficient — 5 avatars in a compact stack take less horizontal space than
        any other variant. Best for header/toolbar contexts (meal plan cook slot, event attendees inline).
        The +N chip cleanly communicates overflow without hiding state.
      </p>
    </section>


    <!-- ══════════════════════════════════════════════════════════════
         CLAUDE'S PICK CALLOUT
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-8">
      <div class="rounded-2xl border p-6 space-y-3"
           :style="{ background: L.accents.lavender.soft, borderColor: L.accents.lavender.bold }">
        <div class="flex items-center gap-2">
          <SparklesOutlineIcon class="w-5 h-5" :style="{ color: L.accents.lavender.bold }" />
          <h2 class="text-[15px] font-semibold" :style="{ color: L.accents.lavender.bold }">Claude's pick — Variant B</h2>
        </div>
        <p class="text-[14px] leading-relaxed" :style="{ color: L.inkPrimary }">
          Variant B (pill card row) is the strongest fit for Kinhold's family-member pickers because it speaks
          the same pill language already established in TabPillGroup, CategoryChipRow, and the reward visibility
          chips — giving the selection row instant visual coherence without new design vocabulary.
          The generous pill tap target (h-12, ~48px touch area) is mobile-first by default, making it
          reliable across task assignment, kudos, and vault share modals without size adjustments.
          Dark parity is effortless: the lavender-soft/bold pair from the token set inverts cleanly to
          #302A48 fill + #B6A8E6 text — no mechanical inversion required, co-equal to the light rendering.
        </p>
      </div>
    </section>


    <!-- ══════════════════════════════════════════════════════════════
         USAGE GUIDE
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-8">
      <div class="rounded-2xl border p-6 space-y-4"
           :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle }">
        <h2 class="text-[17px] font-semibold" :style="{ color: L.inkPrimary }">When to use which variant</h2>
        <ul class="space-y-3 text-[14px]" :style="{ color: L.inkSecondary }">
          <li>
            <strong :style="{ color: L.inkPrimary }">Variant A (circle row + labels)</strong> — Use when names need to always be visible
            and vertical space is available: task creation forms, vault share modal, any sheet where
            the family roster is the primary content. Flex-wrap handles overflow naturally — no scroll
            jank on 320px. Avoid in toolbars or inline header slots where vertical space is scarce.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Variant B (pill cards, recommended)</strong> — Default choice for any member picker.
            Pill shape matches the existing chip/tab vocabulary. Generous tap target. Single-row with
            overflow-x-auto keeps the layout stable regardless of how many members are in the family.
            Use lg (56px) size for invitation cards or onboarding flows where the picker is a hero element.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Variant C (overlap stack)</strong> — Use in space-constrained inline contexts:
            meal plan cook assignment in a calendar cell, event attendee list in a card header,
            any place where you want to show "who's involved" compactly and one-tap selection is enough.
            The +N chip signals there are more members than shown; tapping it should open a full Variant A/B sheet.
            Not suitable for multi-select heavy flows — selecting multiple from a stack is harder to confirm at a glance.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Single-select vs multi-select</strong> — Use single-select (ring only) for
            assignment to one person (task assignee, meal cook). Use multi-select (checkmark overlay) for
            sharing and team contexts (vault share, event attendees, kudos recipients).
            The checkmark overlay communicates "added" semantics clearly across all three variants.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Size guidance</strong> — md (40px) is the default for all inline and form contexts.
            lg (56px) is reserved for hero pickers on invitation and profile screens where the
            member identity deserves emphasis. Never mix sizes within a single picker row.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Disabled state</strong> — Apply grayscale + opacity-40 to avatars that cannot be selected
            (e.g. the current user in a kudos picker, a deactivated family member).
            Always preserve the member's slot in the row to avoid layout shift when the disabled state changes.
          </li>
        </ul>
      </div>
    </section>

  </ComponentPage>
</template>

<style scoped>
/* ── Variant A button hover ─────────────────────────────────────────────────── */
.avatar-btn-a {
  transition: opacity 120ms ease;
}
.avatar-btn-a:hover {
  opacity: 0.85;
}

/* ── Variant B pill hover ───────────────────────────────────────────────────── */
.avatar-pill-b {
  transition: border-color 150ms cubic-bezier(0.16, 1, 0.3, 1),
              background-color 150ms cubic-bezier(0.16, 1, 0.3, 1);
}
.avatar-pill-b:hover {
  border-color: #BCB8B2 !important; /* borderStrong */
}

.avatar-pill-b-dk {
  transition: border-color 150ms cubic-bezier(0.16, 1, 0.3, 1),
              background-color 150ms cubic-bezier(0.16, 1, 0.3, 1);
}
.avatar-pill-b-dk:hover {
  border-color: #403E3A !important; /* D.borderStrong */
}

/* ── Variant C stack button ─────────────────────────────────────────────────── */
.avatar-stack-btn {
  transition: transform 200ms cubic-bezier(0.34, 1.56, 0.64, 1),
              outline-color 150ms ease,
              z-index 0ms;
}
.avatar-stack-btn:hover {
  transform: translateY(-2px) scale(1.04);
}

/* ── Fade-slide transition for revealed name chip ───────────────────────────── */
.fade-slide-enter-active {
  transition: opacity 180ms ease, transform 180ms cubic-bezier(0.16, 1, 0.3, 1);
}
.fade-slide-leave-active {
  transition: opacity 120ms ease, transform 120ms ease;
}
.fade-slide-enter-from {
  opacity: 0;
  transform: translateX(-6px);
}
.fade-slide-leave-to {
  opacity: 0;
  transform: translateX(-4px);
}

/* ── Hide webkit scrollbar on overflow rows ─────────────────────────────────── */
.overflow-x-auto::-webkit-scrollbar {
  display: none;
}

/* ── Reduced motion ─────────────────────────────────────────────────────────── */
@media (prefers-reduced-motion: reduce) {
  .avatar-btn-a,
  .avatar-pill-b,
  .avatar-pill-b-dk,
  .avatar-stack-btn {
    transition: none;
  }
  .fade-slide-enter-active,
  .fade-slide-leave-active {
    transition: none;
  }
}
</style>
