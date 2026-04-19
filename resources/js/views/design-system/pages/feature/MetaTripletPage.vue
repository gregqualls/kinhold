<script setup>
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import {
  SparklesIcon, ClockIcon, CalendarDaysIcon, MapPinIcon,
  FireIcon, FlagIcon, StarIcon, CurrencyDollarIcon,
} from '@heroicons/vue/24/outline'

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

// ── Meta sets ─────────────────────────────────────────────────────────────────
// Each set: array of 3 { icon, label, value, accent }
const recipeMeta = [
  { icon: ClockIcon,        label: 'Time',       value: '25 min',       accent: 'mint'     },
  { icon: SparklesIcon,     label: 'Difficulty', value: 'Easy',         accent: 'lavender' },
  { icon: FireIcon,         label: 'Calories',   value: '420 cal',      accent: 'peach'    },
]
const eventMeta = [
  { icon: CalendarDaysIcon, label: 'Starts',     value: 'Thu 2pm',      accent: 'lavender' },
  { icon: ClockIcon,        label: 'Duration',   value: '30 min',       accent: 'sun'      },
  { icon: MapPinIcon,       label: 'Location',   value: 'Lincoln Middle', accent: 'peach'  },
]
const taskMeta = [
  { icon: CalendarDaysIcon, label: 'Due',        value: 'Tomorrow',     accent: 'sun'      },
  { icon: FlagIcon,         label: 'Priority',   value: 'High',         accent: 'peach'    },
  { icon: StarIcon,         label: 'Points',     value: '5 pts',        accent: 'lavender' },
]
const restaurantMeta = [
  { icon: ClockIcon,        label: 'Hours',      value: '11–10',        accent: 'mint'     },
  { icon: CurrencyDollarIcon, label: 'Price',    value: '$$',           accent: 'sun'      },
  { icon: MapPinIcon,       label: 'Distance',   value: '0.8 mi',       accent: 'lavender' },
]

const allSets = [
  { key: 'recipe',     label: 'Recipe',     data: recipeMeta     },
  { key: 'event',      label: 'Event',      data: eventMeta      },
  { key: 'task',       label: 'Task',       data: taskMeta       },
  { key: 'restaurant', label: 'Restaurant', data: restaurantMeta },
]
</script>

<template>
  <ComponentPage
    title="5.13 MetaTriplet"
    description="A row of 3 icon + value pairs that provides at-a-glance metadata beneath a detail page title. Applies universally: recipe (time / difficulty / calories), event (start / duration / location), task (due / priority / points), restaurant (hours / price / distance). One pattern, four contexts."
    status="chosen"
  >


    <!-- ═══════════════════════════════════════════════════════════════
         VARIANT B — Horizontal inline (icon-left, label+value right)
         ════════════════════════════════════════════════════════════════ -->
    <section class="mb-20">
      <VariantFrame
        label="MetaTriplet"
        caption="Horizontal inline — icon on left, label + value stacked right inside each cell. No dividers, gap-8 spacing. The single locked MetaTriplet shape. Works universally: recipe (time/difficulty/calories), event (when/duration/where), task (due/priority/points), restaurant (hours/price/distance)."
      >
        <div class="w-full space-y-10">

          <!-- ── LIGHT PANEL ── -->
          <div class="rounded-2xl border p-6 space-y-6" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>

            <p class="text-[11px]" :style="{ color: L.inkTertiary }">375px — 3-cell row; gap-4 at mobile, gap-8 at desktop.</p>

            <div class="space-y-4">
              <div
                v-for="set in allSets"
                :key="set.key"
                class="rounded-2xl border p-4"
                :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle }"
              >
                <p class="text-[10px] font-semibold uppercase tracking-widest mb-3" :style="{ color: L.inkTertiary }">{{ set.label }}</p>
                <!-- Inline cells — icon left, text right -->
                <div class="flex items-center gap-4 sm:gap-8">
                  <div
                    v-for="(item, idx) in set.data"
                    :key="idx"
                    class="flex items-center gap-2 min-w-0"
                  >
                    <component
                      :is="item.icon"
                      class="w-[18px] h-[18px] flex-shrink-0"
                      :style="{ color: L.accents[item.accent].bold }"
                    />
                    <div class="min-w-0">
                      <p
                        class="text-[10px] font-semibold uppercase tracking-widest leading-none mb-0.5"
                        :style="{ color: L.inkTertiary }"
                      >{{ item.label }}</p>
                      <p
                        class="text-[14px] font-semibold leading-tight truncate"
                        :style="{ color: L.inkPrimary }"
                      >{{ item.value }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <p class="text-[11px]" :style="{ color: L.inkTertiary }">640px+ — cells breathe with gap-8; works well in a hero detail card header region.</p>
          </div>

          <!-- ── DARK PANEL ── -->
          <div class="rounded-2xl border p-6 space-y-6" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

            <div class="space-y-4">
              <div
                v-for="set in allSets"
                :key="set.key"
                class="rounded-2xl border p-4"
                :style="{ background: D.surfaceRaised, borderColor: D.borderSubtle }"
              >
                <p class="text-[10px] font-semibold uppercase tracking-widest mb-3" :style="{ color: D.inkTertiary }">{{ set.label }}</p>
                <div class="flex items-center gap-4 sm:gap-8">
                  <div
                    v-for="(item, idx) in set.data"
                    :key="idx"
                    class="flex items-center gap-2 min-w-0"
                  >
                    <component
                      :is="item.icon"
                      class="w-[18px] h-[18px] flex-shrink-0"
                      :style="{ color: D.accents[item.accent].bold }"
                    />
                    <div class="min-w-0">
                      <p
                        class="text-[10px] font-semibold uppercase tracking-widest leading-none mb-0.5"
                        :style="{ color: D.inkTertiary }"
                      >{{ item.label }}</p>
                      <p
                        class="text-[14px] font-semibold leading-tight truncate"
                        :style="{ color: D.inkPrimary }"
                      >{{ item.value }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </VariantFrame>
    </section>


    <!-- ═══════════════════════════════════════════════════════════════
         CLAUDE'S PICK CALLOUT
         ════════════════════════════════════════════════════════════════ -->
    <section class="mb-20">
      <div
        class="rounded-2xl border p-6 flex gap-4 items-start"
        :style="{
          background: L.accents.lavender.soft,
          borderColor: '#C0B4E8',
        }"
      >
        <SparklesIcon class="w-5 h-5 flex-shrink-0 mt-0.5" :style="{ color: L.accents.lavender.bold }" />
        <div>
          <p class="text-sm font-semibold mb-1" :style="{ color: L.accents.lavender.bold }">
            LOCKED — single shape, no variants
          </p>
          <p class="text-sm leading-relaxed" :style="{ color: L.inkPrimary }">
            Variant B strikes the right balance for detail pages: the icon-left / text-right treatment gives each cell a clear focal hierarchy while the horizontal inline layout lets the triplet sit naturally in a single line beneath a title without requiring dividers or extra chrome. At phone width (375px) the gap narrows gracefully without stacking, keeping the metadata scannable in one eye pass. Variant A is ideal when space is at an absolute premium (list rows, compact cards) and Variant C earns its place on detail pages embedded in narrow sidebars or panels that cannot guarantee a 640px minimum width.
          </p>
        </div>
      </div>
    </section>

    <!-- ═══════════════════════════════════════════════════════════════
         USAGE GUIDE
         ════════════════════════════════════════════════════════════════ -->
    <section class="mb-10">
      <div
        class="rounded-2xl border divide-y"
        :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle }"
      >
        <div class="px-6 py-4">
          <h2 class="text-base font-semibold" :style="{ color: L.inkPrimary }">Usage guide</h2>
          <p class="text-sm mt-1" :style="{ color: L.inkSecondary }">All three variants share the same data shape — only the layout changes. Pick the variant that matches the density of the surrounding surface.</p>
        </div>

        <!-- Variant A row -->
        <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-[160px_1fr] gap-3">
          <div>
            <span
              class="inline-flex items-center justify-center h-6 min-w-[1.5rem] px-2 rounded-full text-xs font-semibold"
              :style="{ background: L.inkPrimary, color: L.inkInverse }"
            >A — Compact row</span>
          </div>
          <div class="space-y-1">
            <p class="text-sm font-medium" :style="{ color: L.inkPrimary }">List rows, card footers, table cells with inline metadata</p>
            <p class="text-sm" :style="{ color: L.inkSecondary }">
              Use when vertical space is scarce and scanning speed matters most. The stacked icon + label + value layout inside each cell compresses cleanly to as little as 80px per cell. Hairline dividers provide separation without adding visual weight. Best in contexts like recipe list rows, task timeline items, or compact event cards.
            </p>
          </div>
        </div>

        <!-- Variant B row -->
        <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-[160px_1fr] gap-3">
          <div>
            <span
              class="inline-flex items-center justify-center h-6 min-w-[1.5rem] px-2 rounded-full text-xs font-semibold"
              :style="{ background: L.inkPrimary, color: L.inkInverse }"
            >B — Hero detail</span>
          </div>
          <div class="space-y-1">
            <p class="text-sm font-medium" :style="{ color: L.inkPrimary }">Full-width detail pages: recipe detail, event detail, task detail</p>
            <p class="text-sm" :style="{ color: L.inkSecondary }">
              Use beneath the hero title on a dedicated detail page where there is breathing room. The icon-left / label-above / value-below arrangement gives each datum its own strong anchor point. The gap-8 spacing on desktop lets the triplet feel like a considered summary, not a stats dump. This is the recommended default for all Kinhold detail views.
            </p>
          </div>
        </div>

        <!-- Variant C row -->
        <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-[160px_1fr] gap-3">
          <div>
            <span
              class="inline-flex items-center justify-center h-6 min-w-[1.5rem] px-2 rounded-full text-xs font-semibold"
              :style="{ background: L.inkPrimary, color: L.inkInverse }"
            >C — Responsive stack</span>
          </div>
          <div class="space-y-1">
            <p class="text-sm font-medium" :style="{ color: L.inkPrimary }">Sidebars, modals, drawer panels, any context with unpredictable width</p>
            <p class="text-sm" :style="{ color: L.inkSecondary }">
              Use when the container width cannot be guaranteed — e.g. inside a detail drawer, a modal body, or a responsive sidebar panel. Each item renders as a full-width row on mobile for maximum legibility; once the viewport reaches 640px the grid collapses to three equal columns. Hairline top-borders between stacked rows replace the vertical dividers of Variant A.
            </p>
          </div>
        </div>

        <!-- Design rules quick-ref -->
        <div class="px-6 py-4">
          <p class="text-xs font-semibold uppercase tracking-widest mb-3" :style="{ color: L.inkTertiary }">Design rules</p>
          <ul class="space-y-1.5">
            <li class="flex items-baseline gap-2 text-sm" :style="{ color: L.inkSecondary }">
              <span class="w-1.5 h-1.5 rounded-full flex-shrink-0 mt-1.5" :style="{ background: L.inkTertiary }" />
              Icon: 16–18px outline Heroicon, colored with the cell's accent bold token.
            </li>
            <li class="flex items-baseline gap-2 text-sm" :style="{ color: L.inkSecondary }">
              <span class="w-1.5 h-1.5 rounded-full flex-shrink-0 mt-1.5" :style="{ background: L.inkTertiary }" />
              Label: 10px uppercase tracking-widest, inkTertiary — never inkSecondary or inkPrimary.
            </li>
            <li class="flex items-baseline gap-2 text-sm" :style="{ color: L.inkSecondary }">
              <span class="w-1.5 h-1.5 rounded-full flex-shrink-0 mt-1.5" :style="{ background: L.inkTertiary }" />
              Value: 14px font-semibold, inkPrimary — the number or short string is the visual star.
            </li>
            <li class="flex items-baseline gap-2 text-sm" :style="{ color: L.inkSecondary }">
              <span class="w-1.5 h-1.5 rounded-full flex-shrink-0 mt-1.5" :style="{ background: L.inkTertiary }" />
              Dividers (A): 1px solid, inkTertiary at 30% opacity — hairline only, never use borderStrong.
            </li>
            <li class="flex items-baseline gap-2 text-sm" :style="{ color: L.inkSecondary }">
              <span class="w-1.5 h-1.5 rounded-full flex-shrink-0 mt-1.5" :style="{ background: L.inkTertiary }" />
              Accent assignment is semantic, not decorative — mint for time/duration, peach for priority/calories, lavender for difficulty/points, sun for price/score.
            </li>
            <li class="flex items-baseline gap-2 text-sm" :style="{ color: L.inkSecondary }">
              <span class="w-1.5 h-1.5 rounded-full flex-shrink-0 mt-1.5" :style="{ background: L.inkTertiary }" />
              Always exactly 3 cells — no 2-cell or 4-cell variants. Extend to a second MetaTriplet row if more data is needed.
            </li>
          </ul>
        </div>
      </div>
    </section>

  </ComponentPage>
</template>
