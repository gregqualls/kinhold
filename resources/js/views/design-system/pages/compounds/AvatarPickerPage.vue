<script setup>
import { ref } from 'vue'
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import KinAvatarPicker from '@/components/design-system/KinAvatarPicker.vue'
import { StarIcon, HeartIcon } from '@heroicons/vue/24/solid'

// Map demo member shape → KinAvatarPicker prop shape:
//   photo    → { src }
//   initials → let KinAvatar generate from `name`
//   icon     → pass the icon component reference
const ICON_MAP = { heart: HeartIcon, star: StarIcon }
function toKinMembers(members) {
  return members.map((m) => {
    const base = { key: m.key, name: m.name, accentColor: m.accentColor }
    if (m.type === 'photo') return { ...base, src: m.photo }
    if (m.type === 'icon')  return { ...base, icon: ICON_MAP[m.icon] }
    return base
  })
}

// ── Palette ───────────────────────────────────────────────────────────────────
const L = {
  surfaceApp: '#FAF8F5', surfaceRaised: '#FFFFFF',
  inkTertiary: '#9C9895', inkSecondary: '#6B6966',
  borderSubtle: '#E8E4DF',
}
const D = {
  surfaceApp: '#141311', surfaceRaised: '#1C1B19',
  inkTertiary: '#6E6B67',
  borderSubtle: '#2C2A27',
}

// ── Member data (5 members: photo / initials / icon variants) ────────────────
const MEMBERS = [
  { key: 'emma', name: 'Emma', type: 'photo',    photo: 'https://i.pravatar.cc/80?img=1',  accentColor: 'peach'    },
  { key: 'maya', name: 'Maya', type: 'initials', initials: 'MQ',                            accentColor: 'lavender' },
  { key: 'greg', name: 'Greg', type: 'photo',    photo: 'https://i.pravatar.cc/80?img=12', accentColor: 'sun'      },
  { key: 'ava',  name: 'Ava',  type: 'icon',     icon: 'heart',                             accentColor: 'mint'     },
  { key: 'sam',  name: 'Sam',  type: 'photo',    photo: 'https://i.pravatar.cc/80?img=5',  accentColor: 'lavender' },
]

// 8 members for overflow demo
const MEMBERS_OVERFLOW = [
  ...MEMBERS,
  { key: 'lee',   name: 'Lee',   type: 'initials', initials: 'LQ',                            accentColor: 'sun'   },
  { key: 'theo',  name: 'Theo',  type: 'icon',     icon: 'star',                              accentColor: 'peach' },
  { key: 'chloe', name: 'Chloe', type: 'photo',    photo: 'https://i.pravatar.cc/80?img=9',  accentColor: 'mint'  },
]

// Reactive state
const activeL = ref('emma')
const activeD = ref('emma')
const multiL  = ref(['emma', 'greg'])
const multiD  = ref(['emma', 'greg'])
</script>

<template>
  <ComponentPage
    title="4.6 AvatarPicker"
    description="Horizontal row of selectable avatars for family-member pickers — task assignment, kudos recipient, vault share, meal plan cook assignment."
    status="chosen"
  >
    <section class="mb-16">
      <VariantFrame label="KinAvatarPicker" caption="Pill-card row · single- or multi-select · md (40px) and lg (56px) sizes · overflow-x-auto for crowded rows.">
        <div class="w-full space-y-10">

          <!-- LIGHT PANEL -->
          <div class="rounded-2xl border p-6 space-y-6"
               :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>

            <div class="space-y-2">
              <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Single-select · md (40px)</p>
              <KinAvatarPicker :members="toKinMembers(MEMBERS)" :active-keys="[activeL]" @update:active-keys="(ks) => activeL = ks[0] || null" size="md" />
            </div>

            <div class="space-y-2">
              <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Single-select · lg (56px)</p>
              <KinAvatarPicker :members="toKinMembers(MEMBERS)" :active-keys="[activeL]" @update:active-keys="(ks) => activeL = ks[0] || null" size="lg" />
            </div>

            <div class="space-y-2">
              <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Multi-select · md (checkmark overlay)</p>
              <KinAvatarPicker :members="toKinMembers(MEMBERS)" v-model:active-keys="multiL" multi size="md" />
            </div>

            <div class="space-y-2">
              <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">8 members · overflow-x-auto in 320px frame</p>
              <div class="max-w-[320px] rounded-xl border p-3"
                   :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle }">
                <div class="overflow-x-auto pb-1" style="scrollbar-width: none; -ms-overflow-style: none;">
                  <div class="w-max">
                    <KinAvatarPicker :members="toKinMembers(MEMBERS_OVERFLOW)" :active-keys="[activeL]" @update:active-keys="(ks) => activeL = ks[0] || null" size="md" />
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- DARK PANEL -->
          <div class="dark rounded-2xl border p-6 space-y-6"
               :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

            <div class="space-y-2">
              <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Single-select · md (40px)</p>
              <KinAvatarPicker :members="toKinMembers(MEMBERS)" :active-keys="[activeD]" @update:active-keys="(ks) => activeD = ks[0] || null" size="md" />
            </div>

            <div class="space-y-2">
              <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Single-select · lg (56px)</p>
              <KinAvatarPicker :members="toKinMembers(MEMBERS)" :active-keys="[activeD]" @update:active-keys="(ks) => activeD = ks[0] || null" size="lg" />
            </div>

            <div class="space-y-2">
              <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Multi-select · md (checkmark overlay)</p>
              <KinAvatarPicker :members="toKinMembers(MEMBERS)" v-model:active-keys="multiD" multi size="md" />
            </div>

            <div class="space-y-2">
              <p class="text-[11px] font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">8 members · overflow-x-auto in 320px frame</p>
              <div class="max-w-[320px] rounded-xl border p-3"
                   :style="{ background: D.surfaceRaised, borderColor: D.borderSubtle }">
                <div class="overflow-x-auto pb-1" style="scrollbar-width: none; -ms-overflow-style: none;">
                  <div class="w-max">
                    <KinAvatarPicker :members="toKinMembers(MEMBERS_OVERFLOW)" :active-keys="[activeD]" @update:active-keys="(ks) => activeD = ks[0] || null" size="md" />
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>
      </VariantFrame>
    </section>
  </ComponentPage>
</template>

<style scoped>
.overflow-x-auto::-webkit-scrollbar {
  display: none;
}
</style>
