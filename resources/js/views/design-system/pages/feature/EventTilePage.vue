<script setup>
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import KinEventTile from '@/components/design-system/KinEventTile.vue'

const L = {
  surfaceApp:    '#FAF8F5',
  inkTertiary:   '#9C9895',
  borderSubtle:  '#E8E4DF',
}
const D = {
  surfaceApp:    '#141311',
  inkTertiary:   '#6E6B67',
  borderSubtle:  '#2C2A27',
}

const WEEK_DAYS = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
</script>

<template>
  <ComponentPage
    title="5.3 EventTile"
    description="The canonical calendar event card. Block (filled pastel tile) for month grid / day row / dashboard, and span (rounded pill) for week-view multi-day events. Handles all source types (task / manual / Google / ICS) and visibility modes (visible / busy / private)."
    status="chosen"
  >
    <section class="mb-16">
      <VariantFrame label="KinEventTile" caption="Block (default) for point-in-time events; span variant for multi-day pills laid into a 7-column week grid.">
        <div class="w-full space-y-10">
          <!-- LIGHT PANEL -->
          <div
            class="rounded-2xl border p-6 space-y-6"
            :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }"
          >
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>

            <div>
              <p class="text-[11px] mb-3 font-medium" :style="{ color: L.inkTertiary }">Block variant — accent colors, avatars, all-day, task, busy, private</p>
              <div class="flex flex-wrap gap-2">
                <KinEventTile width="148px" title="Soccer practice" time="5:00 – 6:30 PM" accent-color="lavender" />
                <KinEventTile
                  width="148px" title="Parent-teacher" time="2:00 – 3:00 PM" accent-color="mint"
                  :avatars="[{ initials: 'M', color: '#6856B2' }, { initials: 'D', color: '#2E8A62' }]"
                />
                <KinEventTile width="148px" title="Emma's birthday" all-day accent-color="sun" />
                <KinEventTile width="148px" title="Science project" time="Due 3:00 PM" accent-color="peach" source="task" />
                <KinEventTile width="148px" title="Busy" time="10:00 – 11:30 AM" visibility="busy" />
                <KinEventTile width="148px" title="Private" time="7:00 – 8:00 PM" visibility="private" />
              </div>
            </div>

            <div>
              <p class="text-[11px] mb-3 font-medium" :style="{ color: L.inkTertiary }">Span variant — week-grid · pills span 1–N of 7 days</p>
              <div class="flex items-center gap-1 mb-2">
                <div class="w-14 shrink-0"></div>
                <div
                  v-for="d in WEEK_DAYS" :key="d" class="text-[10px] font-semibold uppercase tracking-wider text-center"
                  style="width: calc(100% / 7);" :style="{ color: L.inkTertiary }"
                >
                  {{ d }}
                </div>
              </div>
              <div class="space-y-2">
                <div class="flex items-center gap-1">
                  <div class="w-14 shrink-0"></div>
                  <KinEventTile
                    variant="span" title="Family camping trip" accent-color="lavender"
                    :width="`calc(3 * (100% / 7) - 4px)`"
                    :avatars="[{ initials: 'M', color: '#6856B2' }, { initials: 'D', color: '#2E8A62' }, { initials: 'E', color: '#BA562E' }]"
                  />
                </div>
                <div class="flex items-center gap-1">
                  <div class="w-14 shrink-0"></div>
                  <div style="width: calc(100% / 7);" class="shrink-0"></div>
                  <KinEventTile
                    variant="span" title="Science fair project" accent-color="peach" source="task"
                    :width="`calc(3 * (100% / 7) - 4px)`"
                  />
                </div>
                <div class="flex items-center gap-1">
                  <div class="w-14 shrink-0"></div>
                  <div style="width: calc(2 * (100% / 7));" class="shrink-0"></div>
                  <KinEventTile
                    variant="span" title="Emma's birthday" accent-color="sun"
                    :width="`calc(1 * (100% / 7) - 4px)`"
                  />
                </div>
                <div class="flex items-center gap-1">
                  <div class="w-14 shrink-0"></div>
                  <div style="width: calc(3 * (100% / 7));" class="shrink-0"></div>
                  <KinEventTile
                    variant="span" title="Busy" visibility="busy"
                    :width="`calc(2 * (100% / 7) - 4px)`"
                  />
                </div>
                <div class="flex items-center gap-1">
                  <div class="w-14 shrink-0"></div>
                  <div style="width: calc(5 * (100% / 7));" class="shrink-0"></div>
                  <KinEventTile
                    variant="span" title="" visibility="private"
                    :width="`calc(2 * (100% / 7) - 4px)`"
                  />
                </div>
                <div class="flex items-center gap-1">
                  <div class="w-14 shrink-0"></div>
                  <KinEventTile
                    variant="span" title="Meal plan: Mediterranean week" accent-color="mint"
                    :width="`calc(7 * (100% / 7) - 4px)`"
                  />
                </div>
              </div>
            </div>
          </div>

          <!-- DARK PANEL -->
          <div
            class="dark rounded-2xl border p-6 space-y-6"
            :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }"
          >
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

            <div>
              <p class="text-[11px] mb-3 font-medium" :style="{ color: D.inkTertiary }">Block variant</p>
              <div class="flex flex-wrap gap-2">
                <KinEventTile width="148px" title="Soccer practice" time="5:00 – 6:30 PM" accent-color="lavender" />
                <KinEventTile
                  width="148px" title="Parent-teacher" time="2:00 – 3:00 PM" accent-color="mint"
                  :avatars="[{ initials: 'M', color: '#B6A8E6' }, { initials: 'D', color: '#7CD6AE' }]"
                />
                <KinEventTile width="148px" title="Emma's birthday" all-day accent-color="sun" />
                <KinEventTile width="148px" title="Science project" time="Due 3:00 PM" accent-color="peach" source="task" />
                <KinEventTile width="148px" title="Busy" time="10:00 – 11:30 AM" visibility="busy" />
                <KinEventTile width="148px" title="Private" time="7:00 – 8:00 PM" visibility="private" />
              </div>
            </div>

            <div>
              <p class="text-[11px] mb-3 font-medium" :style="{ color: D.inkTertiary }">Span variant — week-grid · pills span 1–N of 7 days</p>
              <div class="flex items-center gap-1 mb-2">
                <div class="w-14 shrink-0"></div>
                <div
                  v-for="d in WEEK_DAYS" :key="d" class="text-[10px] font-semibold uppercase tracking-wider text-center"
                  style="width: calc(100% / 7);" :style="{ color: D.inkTertiary }"
                >
                  {{ d }}
                </div>
              </div>
              <div class="space-y-2">
                <div class="flex items-center gap-1">
                  <div class="w-14 shrink-0"></div>
                  <KinEventTile
                    variant="span" title="Family camping trip" accent-color="lavender"
                    :width="`calc(3 * (100% / 7) - 4px)`"
                    :avatars="[{ initials: 'M', color: '#B6A8E6' }, { initials: 'D', color: '#7CD6AE' }, { initials: 'E', color: '#F0A882' }]"
                  />
                </div>
                <div class="flex items-center gap-1">
                  <div class="w-14 shrink-0"></div>
                  <div style="width: calc(100% / 7);" class="shrink-0"></div>
                  <KinEventTile
                    variant="span" title="Science fair project" accent-color="peach" source="task"
                    :width="`calc(3 * (100% / 7) - 4px)`"
                  />
                </div>
                <div class="flex items-center gap-1">
                  <div class="w-14 shrink-0"></div>
                  <div style="width: calc(2 * (100% / 7));" class="shrink-0"></div>
                  <KinEventTile
                    variant="span" title="Emma's birthday" accent-color="sun"
                    :width="`calc(1 * (100% / 7) - 4px)`"
                  />
                </div>
                <div class="flex items-center gap-1">
                  <div class="w-14 shrink-0"></div>
                  <div style="width: calc(3 * (100% / 7));" class="shrink-0"></div>
                  <KinEventTile
                    variant="span" title="Busy" visibility="busy"
                    :width="`calc(2 * (100% / 7) - 4px)`"
                  />
                </div>
                <div class="flex items-center gap-1">
                  <div class="w-14 shrink-0"></div>
                  <div style="width: calc(5 * (100% / 7));" class="shrink-0"></div>
                  <KinEventTile
                    variant="span" title="" visibility="private"
                    :width="`calc(2 * (100% / 7) - 4px)`"
                  />
                </div>
                <div class="flex items-center gap-1">
                  <div class="w-14 shrink-0"></div>
                  <KinEventTile
                    variant="span" title="Meal plan: Mediterranean week" accent-color="mint"
                    :width="`calc(7 * (100% / 7) - 4px)`"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>
      </VariantFrame>
    </section>
  </ComponentPage>
</template>
