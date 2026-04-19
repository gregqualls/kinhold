<script setup>
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import {
  SparklesIcon, BookmarkIcon, ShareIcon, HandThumbUpIcon,
  ArrowTopRightOnSquareIcon, UserPlusIcon, PencilSquareIcon,
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

// ── Author data for each context ──────────────────────────────────────────────
const authors = [
  {
    id: 'recipe',
    initials: 'GK',
    avatarColor: '#BA562E',
    name: "Grandma's Kitchen",
    role: 'Recipe author',
    meta: 'Contributed 12 recipes',
    actionLabel: 'Follow',
    ActionIcon: UserPlusIcon,
  },
  {
    id: 'vault',
    initials: 'GQ',
    avatarColor: '#6856B2',
    name: 'Greg Qualls',
    role: 'Parent',
    meta: 'Created Mar 12',
    actionLabel: 'View profile',
    ActionIcon: ArrowTopRightOnSquareIcon,
  },
  {
    id: 'task',
    initials: 'EQ',
    avatarColor: '#2E8A62',
    name: 'Emma Qualls (12)',
    role: 'Kid',
    meta: 'Created this task',
    actionLabel: 'Edit',
    ActionIcon: PencilSquareIcon,
  },
  {
    id: 'kudos',
    initials: 'AQ',
    avatarColor: '#A2780C',
    name: 'Ava (14)',
    role: 'Sibling',
    meta: 'Gave kudos 2h ago',
    actionLabel: 'Thank',
    ActionIcon: HandThumbUpIcon,
  },
  {
    id: 'ai',
    initials: null, // AI uses sparkle icon, not initials
    avatarColor: null,
    name: 'Kinhold Assistant',
    role: 'Generated',
    meta: '4.2s',
    actionLabel: 'View trace',
    ActionIcon: ArrowTopRightOnSquareIcon,
  },
]
</script>

<template>
  <ComponentPage
    title="5.14 AuthorStrip"
    description="The attribution row for any 'created by' or 'shared by' moment. Avatar + name + role/meta + optional action. Three layout takes: A is a compact inline strip for lists, B is a large-avatar presence row for hero detail pages, and C is a super-compact inline chip for dense feeds."
    status="scaffolded"
  >

    <!-- ═══════════════════════════════════════════════════════════════
         VARIANT A — Inline horizontal strip (compact, single-row)
         32-36px avatar · name + role stacked · action pill far right
         ════════════════════════════════════════════════════════════════ -->
    <section class="mb-20">
      <VariantFrame
        label="A"
        caption="Inline horizontal strip — 32-36px avatar left, name + role stacked middle, outline-pill action far right. Single-row, max density. Default for list pages, task rows, vault entry headers."
      >
        <div class="w-full space-y-10">

          <!-- ── LIGHT PANEL ── -->
          <div class="rounded-2xl border" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <div class="px-5 py-3 border-b" :style="{ borderColor: L.borderSubtle }">
              <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>
            </div>

            <div class="rounded-b-2xl overflow-hidden divide-y" :style="{ background: L.surfaceRaised }">
              <div
                v-for="author in authors"
                :key="author.id"
                class="flex items-center gap-3 px-4 py-2.5"
                :style="{ borderColor: L.borderSubtle }"
              >
                <!-- Avatar 32px -->
                <div
                  class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 text-[11px] font-semibold"
                  :style="author.id === 'ai'
                    ? { background: L.accents.lavender.soft }
                    : { background: author.avatarColor + '22', color: author.avatarColor }"
                >
                  <SparklesIcon
                    v-if="author.id === 'ai'"
                    class="w-4 h-4"
                    :style="{ color: L.accents.lavender.bold }"
                  />
                  <span v-else>{{ author.initials }}</span>
                </div>

                <!-- Name + role -->
                <div class="flex-1 min-w-0">
                  <p class="text-[14px] font-semibold leading-tight truncate" :style="{ color: L.inkPrimary }">
                    {{ author.name }}
                  </p>
                  <p class="text-[12px] leading-tight truncate" :style="{ color: L.inkSecondary }">
                    {{ author.role }}<span v-if="author.meta" :style="{ color: L.inkTertiary }"> · {{ author.meta }}</span>
                  </p>
                </div>

                <!-- Action pill -->
                <button
                  class="inline-flex items-center gap-1.5 h-7 px-3 rounded-full text-[12px] font-medium border flex-shrink-0 transition-colors"
                  :style="{ background: 'transparent', borderColor: L.borderStrong, color: L.inkSecondary }"
                >
                  <component :is="author.ActionIcon" class="w-3.5 h-3.5" />
                  {{ author.actionLabel }}
                </button>
              </div>
            </div>
          </div>

          <!-- ── DARK PANEL ── -->
          <div class="rounded-2xl border" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <div class="px-5 py-3 border-b" :style="{ borderColor: D.borderSubtle }">
              <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>
            </div>

            <div class="rounded-b-2xl overflow-hidden divide-y" :style="{ background: D.surfaceRaised }">
              <div
                v-for="author in authors"
                :key="author.id"
                class="flex items-center gap-3 px-4 py-2.5"
                :style="{ borderColor: D.borderSubtle }"
              >
                <div
                  class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 text-[11px] font-semibold"
                  :style="author.id === 'ai'
                    ? { background: D.accents.lavender.soft }
                    : { background: author.avatarColor + '33', color: author.avatarColor }"
                >
                  <SparklesIcon
                    v-if="author.id === 'ai'"
                    class="w-4 h-4"
                    :style="{ color: D.accents.lavender.bold }"
                  />
                  <span v-else>{{ author.initials }}</span>
                </div>

                <div class="flex-1 min-w-0">
                  <p class="text-[14px] font-semibold leading-tight truncate" :style="{ color: D.inkPrimary }">
                    {{ author.name }}
                  </p>
                  <p class="text-[12px] leading-tight truncate" :style="{ color: D.inkSecondary }">
                    {{ author.role }}<span v-if="author.meta" :style="{ color: D.inkTertiary }"> · {{ author.meta }}</span>
                  </p>
                </div>

                <button
                  class="inline-flex items-center gap-1.5 h-7 px-3 rounded-full text-[12px] font-medium border flex-shrink-0 transition-colors"
                  :style="{ background: 'transparent', borderColor: D.borderStrong, color: D.inkSecondary }"
                >
                  <component :is="author.ActionIcon" class="w-3.5 h-3.5" />
                  {{ author.actionLabel }}
                </button>
              </div>
            </div>
          </div>

          <p class="text-xs" :style="{ color: L.inkTertiary }">
            375px — row height is py-2.5 (fixed). Name + role truncate at narrow widths; action pill is flex-shrink-0 and never drops. AI avatar: lavender-soft bg + SparklesIcon in lavender-bold, no initials.
          </p>
        </div>
      </VariantFrame>
    </section>

    <!-- ═══════════════════════════════════════════════════════════════
         VARIANT B — Large avatar with dual lines + below-right actions
         48-56px avatar · name (15px semibold) · role + meta (13px) · action below
         ════════════════════════════════════════════════════════════════ -->
    <section class="mb-20">
      <VariantFrame
        label="B"
        caption="Large avatar with dual lines — 48-56px avatar, name on line 1 (15px semibold), role + meta on line 2 (13px inkSecondary), action button below-right. More presence for hero detail pages."
      >
        <div class="w-full space-y-10">

          <!-- ── LIGHT PANEL ── -->
          <div class="rounded-2xl border" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <div class="px-5 py-3 border-b" :style="{ borderColor: L.borderSubtle }">
              <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>
            </div>

            <div class="rounded-b-2xl overflow-hidden divide-y" :style="{ background: L.surfaceRaised }">
              <div
                v-for="author in authors"
                :key="author.id"
                class="flex items-start gap-4 px-5 py-4"
                :style="{ borderColor: L.borderSubtle }"
              >
                <!-- Avatar 52px -->
                <div
                  class="w-13 h-13 rounded-full flex items-center justify-center flex-shrink-0 text-[15px] font-semibold"
                  style="width: 52px; height: 52px;"
                  :style="author.id === 'ai'
                    ? { background: L.accents.lavender.soft }
                    : { background: author.avatarColor + '22', color: author.avatarColor }"
                >
                  <SparklesIcon
                    v-if="author.id === 'ai'"
                    class="w-6 h-6"
                    :style="{ color: L.accents.lavender.bold }"
                  />
                  <span v-else>{{ author.initials }}</span>
                </div>

                <!-- Text block + action -->
                <div class="flex-1 min-w-0">
                  <!-- Name -->
                  <p class="text-[15px] font-semibold leading-snug" :style="{ color: L.inkPrimary }">
                    {{ author.name }}
                  </p>
                  <!-- Role + meta -->
                  <p class="text-[13px] mt-0.5" :style="{ color: L.inkSecondary }">
                    {{ author.role }}<span v-if="author.meta" :style="{ color: L.inkTertiary }"> · {{ author.meta }}</span>
                  </p>

                  <!-- Action button below-right -->
                  <div class="flex justify-end mt-3">
                    <button
                      class="inline-flex items-center gap-1.5 h-8 px-4 rounded-full text-[13px] font-medium border transition-colors"
                      :style="{ background: 'transparent', borderColor: L.borderStrong, color: L.inkSecondary }"
                    >
                      <component :is="author.ActionIcon" class="w-4 h-4" />
                      {{ author.actionLabel }}
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- ── DARK PANEL ── -->
          <div class="rounded-2xl border" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <div class="px-5 py-3 border-b" :style="{ borderColor: D.borderSubtle }">
              <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>
            </div>

            <div class="rounded-b-2xl overflow-hidden divide-y" :style="{ background: D.surfaceRaised }">
              <div
                v-for="author in authors"
                :key="author.id"
                class="flex items-start gap-4 px-5 py-4"
                :style="{ borderColor: D.borderSubtle }"
              >
                <div
                  class="rounded-full flex items-center justify-center flex-shrink-0 text-[15px] font-semibold"
                  style="width: 52px; height: 52px;"
                  :style="author.id === 'ai'
                    ? { background: D.accents.lavender.soft }
                    : { background: author.avatarColor + '33', color: author.avatarColor }"
                >
                  <SparklesIcon
                    v-if="author.id === 'ai'"
                    class="w-6 h-6"
                    :style="{ color: D.accents.lavender.bold }"
                  />
                  <span v-else>{{ author.initials }}</span>
                </div>

                <div class="flex-1 min-w-0">
                  <p class="text-[15px] font-semibold leading-snug" :style="{ color: D.inkPrimary }">
                    {{ author.name }}
                  </p>
                  <p class="text-[13px] mt-0.5" :style="{ color: D.inkSecondary }">
                    {{ author.role }}<span v-if="author.meta" :style="{ color: D.inkTertiary }"> · {{ author.meta }}</span>
                  </p>

                  <div class="flex justify-end mt-3">
                    <button
                      class="inline-flex items-center gap-1.5 h-8 px-4 rounded-full text-[13px] font-medium border transition-colors"
                      :style="{ background: 'transparent', borderColor: D.borderStrong, color: D.inkSecondary }"
                    >
                      <component :is="author.ActionIcon" class="w-4 h-4" />
                      {{ author.actionLabel }}
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <p class="text-xs" :style="{ color: L.inkTertiary }">
            375px — avatar is 52px. Action button is right-aligned under the text block. On mobile the full row is 80-96px tall — generous enough for a detail page without being a standalone card. Use at the top or bottom of recipe detail, vault entry, or kudos thread.
          </p>
        </div>
      </VariantFrame>
    </section>

    <!-- ═══════════════════════════════════════════════════════════════
         VARIANT C — Chip-style (super compact inline)
         Full attribution fits in one pill: 20-24px avatar + name + role (parens) + icon action
         ════════════════════════════════════════════════════════════════ -->
    <section class="mb-20">
      <VariantFrame
        label="C"
        caption="Chip-style — entire attribution inside one pill. 20-24px avatar + name + role in parens + inline icon-only action. For dense activity feeds, recipe comments, task sub-rows."
      >
        <div class="w-full space-y-10">

          <!-- ── LIGHT PANEL ── -->
          <div class="rounded-2xl border" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <div class="px-5 py-3 border-b" :style="{ borderColor: L.borderSubtle }">
              <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode · inline chip usage</p>
            </div>

            <div class="rounded-b-2xl p-5 space-y-4" :style="{ background: L.surfaceRaised }">
              <!-- Simulated feed lines — each has an attribution chip -->

              <!-- Recipe context -->
              <div class="flex items-center gap-2 flex-wrap">
                <span class="text-[13px]" :style="{ color: L.inkSecondary }">Pasta Bolognese · Added by</span>
                <span
                  class="inline-flex items-center gap-1.5 h-6 pl-1 pr-2 rounded-full border"
                  :style="{ background: L.surfaceSunken, borderColor: L.borderSubtle }"
                >
                  <!-- Avatar 20px -->
                  <span
                    class="w-5 h-5 rounded-full flex items-center justify-center text-[9px] font-semibold flex-shrink-0"
                    :style="{ background: authors[0].avatarColor + '22', color: authors[0].avatarColor }"
                  >{{ authors[0].initials }}</span>
                  <span class="text-[12px] font-medium" :style="{ color: L.inkPrimary }">
                    {{ authors[0].name }}
                    <span :style="{ color: L.inkTertiary }">(Recipe author)</span>
                  </span>
                  <button class="flex-shrink-0 ml-0.5">
                    <BookmarkIcon class="w-3.5 h-3.5" :style="{ color: L.inkTertiary }" />
                  </button>
                </span>
              </div>

              <!-- Vault context -->
              <div class="flex items-center gap-2 flex-wrap">
                <span class="text-[13px]" :style="{ color: L.inkSecondary }">Car Insurance · Created by</span>
                <span
                  class="inline-flex items-center gap-1.5 h-6 pl-1 pr-2 rounded-full border"
                  :style="{ background: L.surfaceSunken, borderColor: L.borderSubtle }"
                >
                  <span
                    class="w-5 h-5 rounded-full flex items-center justify-center text-[9px] font-semibold flex-shrink-0"
                    :style="{ background: authors[1].avatarColor + '22', color: authors[1].avatarColor }"
                  >{{ authors[1].initials }}</span>
                  <span class="text-[12px] font-medium" :style="{ color: L.inkPrimary }">
                    {{ authors[1].name }}
                    <span :style="{ color: L.inkTertiary }">(Parent)</span>
                  </span>
                  <button class="flex-shrink-0 ml-0.5">
                    <ShareIcon class="w-3.5 h-3.5" :style="{ color: L.inkTertiary }" />
                  </button>
                </span>
              </div>

              <!-- Task context -->
              <div class="flex items-center gap-2 flex-wrap">
                <span class="text-[13px]" :style="{ color: L.inkSecondary }">Clean bedroom · Assigned by</span>
                <span
                  class="inline-flex items-center gap-1.5 h-6 pl-1 pr-2 rounded-full border"
                  :style="{ background: L.surfaceSunken, borderColor: L.borderSubtle }"
                >
                  <span
                    class="w-5 h-5 rounded-full flex items-center justify-center text-[9px] font-semibold flex-shrink-0"
                    :style="{ background: authors[2].avatarColor + '22', color: authors[2].avatarColor }"
                  >{{ authors[2].initials }}</span>
                  <span class="text-[12px] font-medium" :style="{ color: L.inkPrimary }">
                    {{ authors[2].name }}
                    <span :style="{ color: L.inkTertiary }">(Kid)</span>
                  </span>
                  <button class="flex-shrink-0 ml-0.5">
                    <PencilSquareIcon class="w-3.5 h-3.5" :style="{ color: L.inkTertiary }" />
                  </button>
                </span>
              </div>

              <!-- Kudos context -->
              <div class="flex items-center gap-2 flex-wrap">
                <span class="text-[13px]" :style="{ color: L.inkSecondary }">Kudos · Given by</span>
                <span
                  class="inline-flex items-center gap-1.5 h-6 pl-1 pr-2 rounded-full border"
                  :style="{ background: L.surfaceSunken, borderColor: L.borderSubtle }"
                >
                  <span
                    class="w-5 h-5 rounded-full flex items-center justify-center text-[9px] font-semibold flex-shrink-0"
                    :style="{ background: authors[3].avatarColor + '22', color: authors[3].avatarColor }"
                  >{{ authors[3].initials }}</span>
                  <span class="text-[12px] font-medium" :style="{ color: L.inkPrimary }">
                    {{ authors[3].name }}
                    <span :style="{ color: L.inkTertiary }">(Sibling)</span>
                  </span>
                  <button class="flex-shrink-0 ml-0.5">
                    <HandThumbUpIcon class="w-3.5 h-3.5" :style="{ color: L.inkTertiary }" />
                  </button>
                </span>
              </div>

              <!-- AI context -->
              <div class="flex items-center gap-2 flex-wrap">
                <span class="text-[13px]" :style="{ color: L.inkSecondary }">Meal plan suggestion · Generated by</span>
                <span
                  class="inline-flex items-center gap-1.5 h-6 pl-1 pr-2 rounded-full border"
                  :style="{ background: L.accents.lavender.soft, borderColor: '#C0B4E8' }"
                >
                  <span
                    class="w-5 h-5 rounded-full flex items-center justify-center flex-shrink-0"
                    :style="{ background: L.accents.lavender.soft }"
                  >
                    <SparklesIcon class="w-3 h-3" :style="{ color: L.accents.lavender.bold }" />
                  </span>
                  <span class="text-[12px] font-medium" :style="{ color: L.accents.lavender.bold }">
                    Kinhold Assistant
                    <span :style="{ color: L.inkTertiary }">(AI)</span>
                  </span>
                  <button class="flex-shrink-0 ml-0.5">
                    <ArrowTopRightOnSquareIcon class="w-3.5 h-3.5" :style="{ color: L.accents.lavender.bold }" />
                  </button>
                </span>
              </div>
            </div>
          </div>

          <!-- ── DARK PANEL ── -->
          <div class="rounded-2xl border" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <div class="px-5 py-3 border-b" :style="{ borderColor: D.borderSubtle }">
              <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode · inline chip usage</p>
            </div>

            <div class="rounded-b-2xl p-5 space-y-4" :style="{ background: D.surfaceRaised }">

              <!-- Recipe context -->
              <div class="flex items-center gap-2 flex-wrap">
                <span class="text-[13px]" :style="{ color: D.inkSecondary }">Pasta Bolognese · Added by</span>
                <span
                  class="inline-flex items-center gap-1.5 h-6 pl-1 pr-2 rounded-full border"
                  :style="{ background: D.surfaceSunken, borderColor: D.borderSubtle }"
                >
                  <span
                    class="w-5 h-5 rounded-full flex items-center justify-center text-[9px] font-semibold flex-shrink-0"
                    :style="{ background: authors[0].avatarColor + '33', color: authors[0].avatarColor }"
                  >{{ authors[0].initials }}</span>
                  <span class="text-[12px] font-medium" :style="{ color: D.inkPrimary }">
                    {{ authors[0].name }}
                    <span :style="{ color: D.inkTertiary }">(Recipe author)</span>
                  </span>
                  <button class="flex-shrink-0 ml-0.5">
                    <BookmarkIcon class="w-3.5 h-3.5" :style="{ color: D.inkTertiary }" />
                  </button>
                </span>
              </div>

              <!-- Vault context -->
              <div class="flex items-center gap-2 flex-wrap">
                <span class="text-[13px]" :style="{ color: D.inkSecondary }">Car Insurance · Created by</span>
                <span
                  class="inline-flex items-center gap-1.5 h-6 pl-1 pr-2 rounded-full border"
                  :style="{ background: D.surfaceSunken, borderColor: D.borderSubtle }"
                >
                  <span
                    class="w-5 h-5 rounded-full flex items-center justify-center text-[9px] font-semibold flex-shrink-0"
                    :style="{ background: authors[1].avatarColor + '33', color: authors[1].avatarColor }"
                  >{{ authors[1].initials }}</span>
                  <span class="text-[12px] font-medium" :style="{ color: D.inkPrimary }">
                    {{ authors[1].name }}
                    <span :style="{ color: D.inkTertiary }">(Parent)</span>
                  </span>
                  <button class="flex-shrink-0 ml-0.5">
                    <ShareIcon class="w-3.5 h-3.5" :style="{ color: D.inkTertiary }" />
                  </button>
                </span>
              </div>

              <!-- Task context -->
              <div class="flex items-center gap-2 flex-wrap">
                <span class="text-[13px]" :style="{ color: D.inkSecondary }">Clean bedroom · Assigned by</span>
                <span
                  class="inline-flex items-center gap-1.5 h-6 pl-1 pr-2 rounded-full border"
                  :style="{ background: D.surfaceSunken, borderColor: D.borderSubtle }"
                >
                  <span
                    class="w-5 h-5 rounded-full flex items-center justify-center text-[9px] font-semibold flex-shrink-0"
                    :style="{ background: authors[2].avatarColor + '33', color: authors[2].avatarColor }"
                  >{{ authors[2].initials }}</span>
                  <span class="text-[12px] font-medium" :style="{ color: D.inkPrimary }">
                    {{ authors[2].name }}
                    <span :style="{ color: D.inkTertiary }">(Kid)</span>
                  </span>
                  <button class="flex-shrink-0 ml-0.5">
                    <PencilSquareIcon class="w-3.5 h-3.5" :style="{ color: D.inkTertiary }" />
                  </button>
                </span>
              </div>

              <!-- Kudos context -->
              <div class="flex items-center gap-2 flex-wrap">
                <span class="text-[13px]" :style="{ color: D.inkSecondary }">Kudos · Given by</span>
                <span
                  class="inline-flex items-center gap-1.5 h-6 pl-1 pr-2 rounded-full border"
                  :style="{ background: D.surfaceSunken, borderColor: D.borderSubtle }"
                >
                  <span
                    class="w-5 h-5 rounded-full flex items-center justify-center text-[9px] font-semibold flex-shrink-0"
                    :style="{ background: authors[3].avatarColor + '33', color: authors[3].avatarColor }"
                  >{{ authors[3].initials }}</span>
                  <span class="text-[12px] font-medium" :style="{ color: D.inkPrimary }">
                    {{ authors[3].name }}
                    <span :style="{ color: D.inkTertiary }">(Sibling)</span>
                  </span>
                  <button class="flex-shrink-0 ml-0.5">
                    <HandThumbUpIcon class="w-3.5 h-3.5" :style="{ color: D.inkTertiary }" />
                  </button>
                </span>
              </div>

              <!-- AI context -->
              <div class="flex items-center gap-2 flex-wrap">
                <span class="text-[13px]" :style="{ color: D.inkSecondary }">Meal plan suggestion · Generated by</span>
                <span
                  class="inline-flex items-center gap-1.5 h-6 pl-1 pr-2 rounded-full border"
                  :style="{ background: D.accents.lavender.soft, borderColor: D.accents.lavender.bold + '55' }"
                >
                  <span
                    class="w-5 h-5 rounded-full flex items-center justify-center flex-shrink-0"
                    :style="{ background: D.accents.lavender.soft }"
                  >
                    <SparklesIcon class="w-3 h-3" :style="{ color: D.accents.lavender.bold }" />
                  </span>
                  <span class="text-[12px] font-medium" :style="{ color: D.accents.lavender.bold }">
                    Kinhold Assistant
                    <span :style="{ color: D.inkTertiary }">(AI)</span>
                  </span>
                  <button class="flex-shrink-0 ml-0.5">
                    <ArrowTopRightOnSquareIcon class="w-3.5 h-3.5" :style="{ color: D.accents.lavender.bold }" />
                  </button>
                </span>
              </div>
            </div>
          </div>

          <p class="text-xs" :style="{ color: L.inkTertiary }">
            375px — the pill is 24px tall (h-6), avatar 20px. It can wrap naturally via flex-wrap when text content is long. AI chip uses lavender-soft background instead of surfaceSunken to visually distinguish non-human attribution. Icon-only action (no label) keeps the pill as narrow as possible.
          </p>
        </div>
      </VariantFrame>
    </section>

    <!-- ═══════════════════════════════════════════════════════════════
         CLAUDE'S PICK CALLOUT
         ════════════════════════════════════════════════════════════════ -->
    <section class="mb-20">
      <div
        class="rounded-2xl border p-6 flex gap-4 items-start"
        :style="{ background: L.accents.lavender.soft, borderColor: '#C0B4E8' }"
      >
        <SparklesIcon class="w-5 h-5 flex-shrink-0 mt-0.5" :style="{ color: L.accents.lavender.bold }" />
        <div>
          <p class="text-sm font-semibold mb-1" :style="{ color: L.accents.lavender.bold }">
            Claude's pick — Variant A
          </p>
          <p class="text-sm leading-relaxed" :style="{ color: L.inkPrimary }">
            Variant A is the right default because attribution is almost always secondary information — the content (the recipe, the task, the vault entry) is what the user opened the page for. A compact 32px strip with an outlined pill action keeps authorship visible without competing for vertical space. Variant B earns its place on dedicated detail pages where provenance matters (recipe hero, vault entry detail), but promoting it as the default would bloat every list row. Variant C is genuinely useful for the one-liner "Added by Emma" contexts in feeds, but it sacrifices the action label entirely — use it only where the action is obvious from icon alone (bookmark, share) or where interaction is optional.
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
          <p class="text-sm mt-1" :style="{ color: L.inkSecondary }">
            Match the variant to the page density. A for list/card headers, B for hero detail pages, C for inline "Added by" chips in feeds and comments.
          </p>
        </div>

        <!-- Variant A -->
        <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-[150px_1fr] gap-3">
          <div>
            <span
              class="inline-flex items-center justify-center h-6 min-w-[1.5rem] px-2 rounded-full text-xs font-semibold"
              :style="{ background: L.inkPrimary, color: L.inkInverse }"
            >A — Inline strip</span>
          </div>
          <div class="space-y-1">
            <p class="text-sm font-medium" :style="{ color: L.inkPrimary }">Task detail header, vault entry top-bar, recipe list row, kudos card, assistant responses</p>
            <p class="text-sm" :style="{ color: L.inkSecondary }">
              Use as the default attribution pattern. Avatar 32-36px, single row, outlined pill action on the right. The action label should be 1-2 words max (Follow, Edit, View, Thank). Omit the action entirely when the viewer has no permission to act (e.g. child viewing a parent-created vault entry). On mobile the row is py-2.5, roughly 44px tall — comfortably tappable without dominating the screen.
            </p>
          </div>
        </div>

        <!-- Variant B -->
        <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-[150px_1fr] gap-3">
          <div>
            <span
              class="inline-flex items-center justify-center h-6 min-w-[1.5rem] px-2 rounded-full text-xs font-semibold"
              :style="{ background: L.inkPrimary, color: L.inkInverse }"
            >B — Large avatar</span>
          </div>
          <div class="space-y-1">
            <p class="text-sm font-medium" :style="{ color: L.inkPrimary }">Recipe detail page (author block), vault entry full-screen view, kudos thread header</p>
            <p class="text-sm" :style="{ color: L.inkSecondary }">
              Use when authorship is a first-class concern — e.g. the top of a recipe card where "Grandma's Kitchen contributed 12 recipes" is meaningful context, not decoration. The 52px avatar and name at 15px semibold give it enough visual mass to anchor a detail page without a full profile card. The action button lives below-right at 8px spacing so it's reachable without interfering with the text scan path.
            </p>
          </div>
        </div>

        <!-- Variant C -->
        <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-[150px_1fr] gap-3">
          <div>
            <span
              class="inline-flex items-center justify-center h-6 min-w-[1.5rem] px-2 rounded-full text-xs font-semibold"
              :style="{ background: L.inkPrimary, color: L.inkInverse }"
            >C — Inline chip</span>
          </div>
          <div class="space-y-1">
            <p class="text-sm font-medium" :style="{ color: L.inkPrimary }">Activity feed rows, recipe comment attribution, task sub-rows ("Added by Emma"), notification body</p>
            <p class="text-sm" :style="{ color: L.inkSecondary }">
              Use when attribution is a fragment of a longer sentence, not the focal point. The chip is 24px tall and wraps naturally via flex-wrap — it reads as inline text, not a separate UI element. The icon-only action (no label) is appropriate here because the action is always low-stakes (bookmark, share, flag) and the icon is internationally recognisable. AI attribution uses lavender-soft chip background to visually flag non-human authorship at a glance — do not style AI chips the same as human author chips.
            </p>
          </div>
        </div>

        <!-- AI avatar note -->
        <div class="px-6 py-4">
          <p class="text-xs font-semibold uppercase tracking-widest mb-2" :style="{ color: L.inkTertiary }">AI avatar treatment</p>
          <p class="text-sm" :style="{ color: L.inkSecondary }">
            All three variants use a consistent AI avatar: <code class="text-xs font-mono px-1 rounded" :style="{ background: L.surfaceSunken }">lavender.soft</code> background circle with <code class="text-xs font-mono px-1 rounded" :style="{ background: L.surfaceSunken }">SparklesIcon</code> (outline, 24/outline) in <code class="text-xs font-mono px-1 rounded" :style="{ background: L.surfaceSunken }">lavender.bold</code>. No initials — the icon alone identifies the AI. In dark mode, use the dark palette's lavender tokens identically.
          </p>
        </div>
      </div>
    </section>

  </ComponentPage>
</template>
