<script setup>
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import KinMetaTriplet from '@/components/design-system/KinMetaTriplet.vue'
import {
  SparklesIcon, ClockIcon, CalendarDaysIcon, MapPinIcon,
  FireIcon, FlagIcon, StarIcon, CurrencyDollarIcon,
} from '@heroicons/vue/24/outline'

const L = {
  surfaceApp:    '#FAF8F5',
  surfaceRaised: '#FFFFFF',
  inkTertiary:   '#9C9895',
  borderSubtle:  '#E8E4DF',
}
const D = {
  surfaceApp:    '#141311',
  surfaceRaised: '#1C1B19',
  inkTertiary:   '#6E6B67',
  borderSubtle:  '#2C2A27',
}

const allSets = [
  { key: 'recipe', label: 'Recipe', data: [
    { icon: ClockIcon,    label: 'Time',       value: '25 min',  accent: 'mint'     },
    { icon: SparklesIcon, label: 'Difficulty', value: 'Easy',    accent: 'lavender' },
    { icon: FireIcon,     label: 'Calories',   value: '420 cal', accent: 'peach'    },
  ] },
  { key: 'event', label: 'Event', data: [
    { icon: CalendarDaysIcon, label: 'Starts',   value: 'Thu 2pm',        accent: 'lavender' },
    { icon: ClockIcon,        label: 'Duration', value: '30 min',         accent: 'sun'      },
    { icon: MapPinIcon,       label: 'Location', value: 'Lincoln Middle', accent: 'peach'    },
  ] },
  { key: 'task', label: 'Task', data: [
    { icon: CalendarDaysIcon, label: 'Due',      value: 'Tomorrow', accent: 'sun'      },
    { icon: FlagIcon,         label: 'Priority', value: 'High',     accent: 'peach'    },
    { icon: StarIcon,         label: 'Points',   value: '5 pts',    accent: 'lavender' },
  ] },
  { key: 'restaurant', label: 'Restaurant', data: [
    { icon: ClockIcon,          label: 'Hours',    value: '11–10',  accent: 'mint'     },
    { icon: CurrencyDollarIcon, label: 'Price',    value: '$$',     accent: 'sun'      },
    { icon: MapPinIcon,         label: 'Distance', value: '0.8 mi', accent: 'lavender' },
  ] },
]
</script>

<template>
  <ComponentPage
    title="5.13 MetaTriplet"
    description="A row of 3 icon + value pairs that provides at-a-glance metadata beneath a detail page title. Applies universally: recipe (time / difficulty / calories), event (start / duration / location), task (due / priority / points), restaurant (hours / price / distance). One pattern, four contexts."
    status="chosen"
  >
    <section class="mb-16">
      <VariantFrame label="KinMetaTriplet" caption="Pass any 3-item array of { icon, label, value, accent }. Renders the three pairs side-by-side with hairline dividers.">
        <div class="w-full space-y-10">

          <!-- LIGHT PANEL -->
          <div class="rounded-2xl border p-6 space-y-4" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest mb-2" :style="{ color: L.inkTertiary }">Light mode</p>
            <div v-for="set in allSets" :key="set.key" class="rounded-2xl border p-4" :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle }">
              <p class="text-[10px] font-semibold uppercase tracking-widest mb-3" :style="{ color: L.inkTertiary }">{{ set.label }}</p>
              <KinMetaTriplet :items="set.data" />
            </div>
          </div>

          <!-- DARK PANEL -->
          <div class="dark rounded-2xl border p-6 space-y-4" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest mb-2" :style="{ color: D.inkTertiary }">Dark mode</p>
            <div v-for="set in allSets" :key="set.key" class="rounded-2xl border p-4" :style="{ background: D.surfaceRaised, borderColor: D.borderSubtle }">
              <p class="text-[10px] font-semibold uppercase tracking-widest mb-3" :style="{ color: D.inkTertiary }">{{ set.label }}</p>
              <KinMetaTriplet :items="set.data" />
            </div>
          </div>

        </div>
      </VariantFrame>
    </section>
  </ComponentPage>
</template>
