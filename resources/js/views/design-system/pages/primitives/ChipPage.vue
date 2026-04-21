<script setup>
import { ref } from 'vue'
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import ModeSplit from '../../shared/ModeSplit.vue'
import KinChip from '@/components/design-system/KinChip.vue'
import {
  CheckIcon,
  ClockIcon,
  PauseIcon,
  ExclamationTriangleIcon,
  InformationCircleIcon,
  StarIcon,
  BellIcon,
} from '@heroicons/vue/24/outline'

// ── Demo data ────────────────────────────────────────────────────────────────

const accentColors = ['lavender', 'peach', 'mint', 'sun', 'neutral']

const categoryChips = [
  { color: 'lavender', label: 'Family' },
  { color: 'peach',    label: 'School' },
  { color: 'mint',     label: 'Kids' },
  { color: 'sun',      label: 'House' },
  { color: 'neutral',  label: 'Other' },
]

const statusChips = [
  { status: 'success', label: 'Completed',  icon: CheckIcon },
  { status: 'pending', label: 'Waiting',    icon: ClockIcon },
  { status: 'paused',  label: 'Snoozed',    icon: PauseIcon },
  { status: 'failed',  label: 'Error',      icon: ExclamationTriangleIcon },
  { status: 'info',    label: 'Info',       icon: InformationCircleIcon },
  { status: 'warning', label: 'Due soon',   icon: ExclamationTriangleIcon },
]

// ── Interactive filter cluster state ─────────────────────────────────────────

const activeFilters = ref(['lavender'])

function toggleFilter(color) {
  const idx = activeFilters.value.indexOf(color)
  if (idx === -1) activeFilters.value.push(color)
  else activeFilters.value.splice(idx, 1)
}

// ── Removable chip visibility ─────────────────────────────────────────────────

const removableVisible = ref(true)
function resetRemovable() { removableVisible.value = true }
</script>

<template>
  <ComponentPage
    title="1.3 Chip · Tag · Status pill"
    description="Outlined + soft-tint chip for categories, tags, filters, and feature badges. Paired with a tiny inline status indicator (dot + label) for dense contexts where a full chip would be too much chrome."
    status="chosen"
  >

    <!-- ══════════════════════════════════════════════════════════════
         CHIP — Outlined + soft bg tint
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame
        label="Chip"
        caption="Outlined + soft tint — categories, tags, and filters"
      >
        <ModeSplit>
        <div class="w-full space-y-10">

          <!-- Category chips — all 5 colors × default + active -->
          <div>
            <p class="text-xs font-medium text-ink-tertiary mb-3">Category accents — default</p>
            <div class="flex flex-wrap gap-2">
              <KinChip
                v-for="chip in categoryChips"
                :key="chip.color"
                variant="category"
                :color="chip.color"
              >{{ chip.label }}</KinChip>
            </div>
          </div>

          <div>
            <p class="text-xs font-medium text-ink-tertiary mb-3">Category accents — active (filled)</p>
            <div class="flex flex-wrap gap-2">
              <KinChip
                v-for="chip in categoryChips"
                :key="chip.color"
                variant="category"
                :color="chip.color"
                :active="true"
              >{{ chip.label }}</KinChip>
            </div>
          </div>

          <!-- Filter chips — inactive / active / removable -->
          <div>
            <p class="text-xs font-medium text-ink-tertiary mb-3">Filter cluster — interactive</p>
            <div class="flex flex-wrap gap-2">
              <KinChip
                v-for="chip in categoryChips"
                :key="chip.color"
                variant="filter"
                :color="chip.color"
                :active="activeFilters.includes(chip.color)"
                @click="toggleFilter(chip.color)"
              >
                <template #leading>
                  <CheckIcon
                    v-if="activeFilters.includes(chip.color)"
                    class="w-3.5 h-3.5 flex-shrink-0"
                  />
                </template>
                {{ chip.label }}
              </KinChip>
            </div>
          </div>

          <!-- Filter chip — removable -->
          <div>
            <p class="text-xs font-medium text-ink-tertiary mb-3">Filter — removable</p>
            <div class="flex flex-wrap gap-2 items-center">
              <KinChip
                v-if="removableVisible"
                variant="filter"
                color="peach"
                :removable="true"
                @remove="removableVisible = false"
              >Removable</KinChip>
              <button
                v-else
                class="text-xs text-ink-tertiary hover:text-ink-secondary underline"
                @click="resetRemovable"
              >↩ Reset</button>

              <KinChip variant="filter" color="mint" :removable="true">
                <template #leading>
                  <CheckIcon class="w-3.5 h-3.5 flex-shrink-0" />
                </template>
                Completed
              </KinChip>
            </div>
          </div>

          <!-- Size scale -->
          <div>
            <p class="text-xs font-medium text-ink-tertiary mb-3">Sizes — sm / md</p>
            <div class="flex flex-wrap items-center gap-3">
              <KinChip variant="category" color="lavender" size="sm">sm — 24px</KinChip>
              <KinChip variant="category" color="lavender" size="md">md — 28px</KinChip>
            </div>
          </div>

          <!-- Disabled state -->
          <div>
            <p class="text-xs font-medium text-ink-tertiary mb-3">Disabled</p>
            <div class="flex flex-wrap gap-2">
              <KinChip variant="category" color="lavender" :disabled="true">Disabled</KinChip>
              <KinChip variant="filter"   color="peach"    :disabled="true">Disabled</KinChip>
            </div>
          </div>

          <!-- With leading icon -->
          <div>
            <p class="text-xs font-medium text-ink-tertiary mb-3">With leading icon</p>
            <div class="flex flex-wrap gap-2">
              <KinChip variant="category" color="mint">
                <template #leading><CheckIcon class="w-3.5 h-3.5 flex-shrink-0" /></template>
                Completed
              </KinChip>
              <KinChip variant="category" color="lavender">
                <template #leading><StarIcon class="w-3.5 h-3.5 flex-shrink-0" /></template>
                Family
              </KinChip>
              <KinChip variant="category" color="peach">
                <template #leading><BellIcon class="w-3.5 h-3.5 flex-shrink-0" /></template>
                School
              </KinChip>
            </div>
          </div>

        </div>
        </ModeSplit>
      </VariantFrame>

      <p class="mt-3 text-body-sm text-ink-secondary px-1">
        Outlined + soft tint: the border is the primary affordance signal — you know immediately which are interactive because they have a visible boundary. The off state (grey border, neutral bg) vs on state (color border + fill) creates the clearest binary for filter-chip use cases. Best for "filter bar" contexts like the shopping list, task lists, and search filters.
      </p>
    </section>

    <!-- ══════════════════════════════════════════════════════════════
         INLINE STATUS INDICATOR — Dot + label
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame
        label="Inline"
        caption="Inline status indicator — tiny dot + label for dense rows where a full chip is too much"
      >
        <ModeSplit>
        <div class="w-full space-y-10">

          <!-- All 6 status values -->
          <div>
            <p class="text-xs font-medium text-ink-tertiary mb-3">Status — all 6 values</p>
            <div class="flex flex-wrap gap-2">
              <KinChip
                v-for="s in statusChips"
                :key="s.status"
                variant="status"
                :status="s.status"
              >{{ s.label }}</KinChip>
            </div>
          </div>

          <!-- Small size -->
          <div>
            <p class="text-xs font-medium text-ink-tertiary mb-3">Status — sm size (dense rows)</p>
            <div class="flex flex-wrap gap-2">
              <KinChip
                v-for="s in statusChips"
                :key="s.status"
                variant="status"
                size="sm"
                :status="s.status"
              >{{ s.label }}</KinChip>
            </div>
          </div>

          <!-- Dense task-row context -->
          <div>
            <p class="text-xs font-medium text-ink-tertiary mb-3">Dense context — inline in task rows</p>
            <div class="flex flex-col gap-2 max-w-sm">
              <div class="flex items-center justify-between py-2 px-3 rounded-xl bg-surface-raised border border-border-subtle">
                <span class="text-[13px] font-medium text-ink-primary">Do laundry</span>
                <KinChip variant="status" size="sm" status="success">Done</KinChip>
              </div>
              <div class="flex items-center justify-between py-2 px-3 rounded-xl bg-surface-raised border border-border-subtle">
                <span class="text-[13px] font-medium text-ink-primary">Call school</span>
                <KinChip variant="status" size="sm" status="warning">Due soon</KinChip>
              </div>
              <div class="flex items-center justify-between py-2 px-3 rounded-xl bg-surface-raised border border-border-subtle">
                <span class="text-[13px] font-medium text-ink-primary">Pay electric bill</span>
                <KinChip variant="status" size="sm" status="pending">Pending</KinChip>
              </div>
            </div>
          </div>

          <!-- Disabled -->
          <div>
            <p class="text-xs font-medium text-ink-tertiary mb-3">Disabled</p>
            <div class="flex flex-wrap gap-2">
              <KinChip variant="status" status="success" :disabled="true">Done</KinChip>
            </div>
          </div>

        </div>
        </ModeSplit>
      </VariantFrame>

      <p class="mt-3 text-body-sm text-ink-secondary px-1">
        The inline indicator is <strong>not</strong> a chip variant — it complements the chip. Use it in task rows, activity feeds, calendar event summaries, or shopping list items where a full chip would disrupt the rhythm. The dot vocabulary is instantly learnable — after 2–3 uses, the color signals meaning without reading the label.
      </p>
    </section>

    <!-- ══════════════════════════════════════════════════════════════
         USAGE GUIDE
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-8">
      <div class="rounded-2xl border border-border-subtle p-6 space-y-4 bg-surface-raised">
        <h2 class="text-h4 font-semibold text-ink-primary">When to use which</h2>
        <ul class="space-y-3 text-body-sm text-ink-secondary">
          <li>
            <strong class="text-ink-primary">Chip (outlined + tint):</strong>
            The canonical chip. Use for category tags, recipe labels, vault entry types, filter bars, meal plan categories, and anywhere a capsule-shaped label or filter belongs. Strongest off/on contrast makes it the default filter-chip pattern.
          </li>
          <li>
            <strong class="text-ink-primary">Inline status indicator (dot + label):</strong>
            Not a chip — a lighter affordance for dense contexts. Use inside task rows, activity feeds, calendar event summaries, and shopping list items where a full chip would be visual noise. The dot carries the color; the label stays as text.
          </li>
        </ul>
      </div>
    </section>

  </ComponentPage>
</template>
