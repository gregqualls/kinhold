<!--
  KinUtilityRail — right-side data rail for desktop-heavy pages.
  @see /design-system/utility-rail  (docs/design/COMPONENT_ROADMAP.md §3.4)
  Props: width, labels (override default section titles)
  Slots:
    #mini-month, #filters, #presence, #saved-views, #actions
    (plus a generic default slot after actions for anything custom)

  Each section auto-renders a uppercase kicker label + divider line above
  it (except the first visible one). The `#actions` slot is auto-pushed
  to the bottom via mt-auto so actions always sit at the rail's footer.
  Skip any slot you don't need — its divider + label disappear too.

  Collapses to a bottom sheet on mobile is a consumer concern (wrap this
  in a responsive container or render a different component on small
  breakpoints). This component just renders the rail itself.
-->
<script setup>
import { computed, useSlots } from 'vue'

const props = defineProps({
  /** Rail width. Default 280px matches the spec. */
  width: { type: String, default: '280px' },
  /** Override the default section labels. */
  labels: {
    type: Object,
    default: () => ({}),
  },
})

const DEFAULT_LABELS = {
  'mini-month':  'This month',
  'filters':     'Filters',
  'presence':    "Who's around",
  'saved-views': 'Saved views',
  'actions':     'Actions',
}

const labelFor = (slotName) =>
  props.labels[slotName] ?? DEFAULT_LABELS[slotName]

// Order of conventional sections.
const SECTIONS = ['mini-month', 'filters', 'presence', 'saved-views', 'actions']

const slots = useSlots()
const visibleSections = computed(() =>
  SECTIONS.filter((s) => !!slots[s])
)
</script>

<template>
  <aside
    class="kin-utility-rail flex-shrink-0 flex flex-col border-l border-border-subtle bg-surface-raised p-4 overflow-y-auto"
    :style="{ width }"
  >
    <template v-for="(slotName, i) in visibleSections" :key="slotName">
      <div
        :class="[
          // First visible section: no top border.
          i === 0 ? '' : 'pt-5 border-t border-border-subtle',
          // Actions always pushes to bottom.
          slotName === 'actions' ? 'mt-auto' : '',
          // Space below each section (except between-section gap comes from pt-5).
          i === 0 ? 'space-y-0' : '',
        ]"
      >
        <p class="text-[10px] font-semibold uppercase tracking-widest mb-2.5 text-ink-tertiary">
          {{ labelFor(slotName) }}
        </p>
        <slot :name="slotName" />
      </div>
    </template>

    <!-- Generic default slot for anything custom (rare). -->
    <slot />
  </aside>
</template>

<style scoped>
/* Inter-section vertical rhythm: space-y-5 between siblings.
   Can't use Tailwind space-y utility directly on a v-for with template
   fragment, so set the spacing via first-of-type logic on kin-utility-rail
   children. */
.kin-utility-rail > * + * {
  margin-top: 1.25rem;   /* same as space-y-5 */
}
</style>
