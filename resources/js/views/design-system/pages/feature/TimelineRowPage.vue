<script setup>
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import KinTimelineRow from '@/components/design-system/KinTimelineRow.vue'
import { CakeIcon, MapPinIcon } from '@heroicons/vue/24/outline'

const L = {
  surfaceApp:    '#FAF8F5',
  surfaceSunken: '#F5F2EE',
  inkTertiary:   '#9C9895',
  borderSubtle:  '#E8E4DF',
}
const D = {
  surfaceApp:    '#141311',
  surfaceSunken: '#161513',
  inkTertiary:   '#6E6B67',
  borderSubtle:  '#2C2A27',
}

const DAYS = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
</script>

<template>
  <ComponentPage
    title="5.7 TimelineRow"
    description="Pill-shaped bar spanning a date range inside a 7-column week track. Contains a label and optionally an icon, avatars, and a drag handle. Used in meal plan (recipe spanning days), reward auction windows, family schedule overlay (vacation), and workout blocks. KinTimelineRow renders the pill only — the parent supplies the grid track and absolute positioning."
    status="chosen"
  >
    <section class="mb-16">
      <VariantFrame label="KinTimelineRow" caption="Pill spans N of 7 days via parent's left/right offsets. Accent colors, optional icon, avatars, and drag handle.">
        <div class="w-full space-y-10">
          <!-- LIGHT -->
          <div class="rounded-2xl border p-6 space-y-3" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest mb-2" :style="{ color: L.inkTertiary }">Light mode · 7-day track</p>

            <div class="grid grid-cols-7">
              <div
                v-for="(day, i) in DAYS" :key="i"
                class="text-center text-[10px] font-semibold uppercase tracking-widest pb-1"
                :style="{ color: L.inkTertiary, borderRight: i < 6 ? `1px solid ${L.borderSubtle}` : 'none' }"
              >
                {{ day }}
              </div>
            </div>

            <div class="rounded-xl overflow-hidden border" :style="{ borderColor: L.borderSubtle, background: L.surfaceSunken }">
              <!-- Mon→Wed (cols 1–3) -->
              <div class="relative grid grid-cols-7 py-1 border-b" :style="{ borderColor: L.borderSubtle, minHeight: '44px' }">
                <div v-for="d in 7" :key="d" class="border-r last:border-r-0 h-full" :style="{ borderColor: L.borderSubtle }"></div>
                <div class="absolute inset-y-1" style="left: 4px; right: calc(100% / 7 * 4 + 4px);">
                  <KinTimelineRow label="Leftover stir-fry · Mon→Wed" accent-color="mint" :icon="CakeIcon" draggable />
                </div>
              </div>
              <!-- Fri→Sun (cols 5–7) -->
              <div class="relative grid grid-cols-7 py-1 border-b" :style="{ borderColor: L.borderSubtle, minHeight: '44px' }">
                <div v-for="d in 7" :key="d" class="border-r last:border-r-0 h-full" :style="{ borderColor: L.borderSubtle }"></div>
                <div class="absolute inset-y-1" style="left: calc(100% / 7 * 4 + 4px); right: 4px;">
                  <KinTimelineRow
                    label="Movie night pass · Fri→Sun" accent-color="sun"
                    :avatars="[{initials:'E',color:'#6856B2'},{initials:'J',color:'#2E8A62'},{initials:'L',color:'#A2780C'}]"
                  />
                </div>
              </div>
              <!-- Full week (cols 1–7) -->
              <div class="relative grid grid-cols-7 py-1 border-b" :style="{ borderColor: L.borderSubtle, minHeight: '44px' }">
                <div v-for="d in 7" :key="d" class="border-r last:border-r-0 h-full" :style="{ borderColor: L.borderSubtle }"></div>
                <div class="absolute inset-y-1" style="left: 4px; right: 4px;">
                  <KinTimelineRow
                    label="Vacation · Maine" accent-color="lavender" :icon="MapPinIcon" draggable
                    :avatars="[{initials:'E',color:'#6856B2'},{initials:'G',color:'#BA562E'}]"
                  />
                </div>
              </div>
              <!-- Single day (col 2) -->
              <div class="relative grid grid-cols-7 py-1" :style="{ minHeight: '44px' }">
                <div v-for="d in 7" :key="d" class="border-r last:border-r-0 h-full" :style="{ borderColor: L.borderSubtle }"></div>
                <div class="absolute inset-y-1" style="left: calc(100% / 7 * 1 + 4px); right: calc(100% / 7 * 5 + 4px);">
                  <KinTimelineRow label="Dentist · Tue" accent-color="peach" />
                </div>
              </div>
            </div>
          </div>

          <!-- DARK -->
          <div class="dark rounded-2xl border p-6 space-y-3" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest mb-2" :style="{ color: D.inkTertiary }">Dark mode · 7-day track</p>

            <div class="grid grid-cols-7">
              <div
                v-for="(day, i) in DAYS" :key="i"
                class="text-center text-[10px] font-semibold uppercase tracking-widest pb-1"
                :style="{ color: D.inkTertiary, borderRight: i < 6 ? `1px solid ${D.borderSubtle}` : 'none' }"
              >
                {{ day }}
              </div>
            </div>

            <div class="rounded-xl overflow-hidden border" :style="{ borderColor: D.borderSubtle, background: D.surfaceSunken }">
              <div class="relative grid grid-cols-7 py-1 border-b" :style="{ borderColor: D.borderSubtle, minHeight: '44px' }">
                <div v-for="d in 7" :key="d" class="border-r last:border-r-0 h-full" :style="{ borderColor: D.borderSubtle }"></div>
                <div class="absolute inset-y-1" style="left: 4px; right: calc(100% / 7 * 4 + 4px);">
                  <KinTimelineRow label="Leftover stir-fry · Mon→Wed" accent-color="mint" :icon="CakeIcon" draggable />
                </div>
              </div>
              <div class="relative grid grid-cols-7 py-1 border-b" :style="{ borderColor: D.borderSubtle, minHeight: '44px' }">
                <div v-for="d in 7" :key="d" class="border-r last:border-r-0 h-full" :style="{ borderColor: D.borderSubtle }"></div>
                <div class="absolute inset-y-1" style="left: calc(100% / 7 * 4 + 4px); right: 4px;">
                  <KinTimelineRow
                    label="Movie night pass · Fri→Sun" accent-color="sun"
                    :avatars="[{initials:'E',color:'#B6A8E6'},{initials:'J',color:'#7CD6AE'},{initials:'L',color:'#E6C452'}]"
                  />
                </div>
              </div>
              <div class="relative grid grid-cols-7 py-1 border-b" :style="{ borderColor: D.borderSubtle, minHeight: '44px' }">
                <div v-for="d in 7" :key="d" class="border-r last:border-r-0 h-full" :style="{ borderColor: D.borderSubtle }"></div>
                <div class="absolute inset-y-1" style="left: 4px; right: 4px;">
                  <KinTimelineRow
                    label="Vacation · Maine" accent-color="lavender" :icon="MapPinIcon" draggable
                    :avatars="[{initials:'E',color:'#B6A8E6'},{initials:'G',color:'#F0A882'}]"
                  />
                </div>
              </div>
              <div class="relative grid grid-cols-7 py-1" :style="{ minHeight: '44px' }">
                <div v-for="d in 7" :key="d" class="border-r last:border-r-0 h-full" :style="{ borderColor: D.borderSubtle }"></div>
                <div class="absolute inset-y-1" style="left: calc(100% / 7 * 1 + 4px); right: calc(100% / 7 * 5 + 4px);">
                  <KinTimelineRow label="Dentist · Tue" accent-color="peach" />
                </div>
              </div>
            </div>
          </div>
        </div>
      </VariantFrame>
    </section>
  </ComponentPage>
</template>
