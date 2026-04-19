<script setup>
import { ref } from 'vue'
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import { SparklesIcon, CheckIcon, ChevronDownIcon, ChevronUpIcon } from '@heroicons/vue/24/outline'

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
  status: { success: { soft: '#E1F0E7', bold: '#4D8C6A' } },
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
  status: { success: { soft: '#1C3A2A', bold: '#6CC498' } },
}

// ── Variant A/C step data (recipe instructions) ───────────────────────────────
const stepsAC = [
  {
    n: 1,
    state: 'done', // completed
    title: 'Prep the vegetables',
    body: 'Dice the onion and bell pepper into roughly equal pieces — about 1 cm cubes. Mince the garlic finely so it cooks evenly. Pat the zucchini dry with a paper towel to prevent steaming in the pan.',
    hasList: false,
  },
  {
    n: 2,
    state: 'active', // current
    title: 'Build the base flavor',
    body: 'Heat olive oil in a wide skillet over medium-high heat until shimmering. Add the onion and cook 3–4 minutes until soft and translucent, then add garlic and cook 60 seconds more until fragrant.',
    hasList: true,
    list: ['2 tbsp olive oil', '1 medium yellow onion', '3 cloves garlic, minced'],
  },
  {
    n: 3,
    state: 'default',
    title: 'Add protein and simmer',
    body: 'Stir in the diced tomatoes, chickpeas, and spice blend. Reduce heat to low, cover, and simmer 15 minutes so the flavors meld. Taste and adjust salt before serving.',
    hasList: false,
  },
]

// ── Variant B step data (onboarding) ─────────────────────────────────────────
const stepsB = [
  {
    n: 1,
    state: 'done',
    title: 'Create your family',
    body: 'Give your family a name and invite code. This becomes your private workspace — only people with the invite code can join. You can change both at any time from Settings.',
    hasList: false,
  },
  {
    n: 2,
    state: 'active',
    title: 'Connect your calendar',
    body: 'Link Google Calendar so your family events appear alongside tasks and reminders. Kinhold requests read-only access by default — you can upgrade to two-way sync whenever you\'re ready.',
    hasList: false,
  },
  {
    n: 3,
    state: 'default',
    title: 'Invite your family members',
    body: 'Share the invite code with your partner and kids. Each member sets up their own profile and role. Parents get full access; kids get a focused view tailored to their age.',
    hasList: false,
  },
]
</script>

<template>
  <ComponentPage
    title="5.11 StepCard"
    description="Numbered, collapsible step card. Used for recipe instructions, onboarding wizard steps, vault playbook guides, meal-plan cook flow, and tutorial-style walkthroughs anywhere in the app."
    status="chosen"
  >

    <!-- ═══════════════════════════════════════════════════════════════
         VARIANT C — Stepper with connector
         ════════════════════════════════════════════════════════════════ -->
    <section class="mb-20">
      <VariantFrame
        label="StepCard"
        caption="Stepper with connector — number badge + title/body, 2px vertical line descending from each circle to the next creates a 'you are here in a sequence' affordance. The single locked StepCard shape. Used in onboarding, vault playbooks, recipe instructions, meal-plan cook flow."
      >
        <div class="w-full space-y-10">

          <!-- ── LIGHT PANEL ── -->
          <div class="rounded-2xl border" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <div class="px-5 py-3 border-b" :style="{ borderColor: L.borderSubtle }">
              <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>
            </div>

            <div class="p-5">
              <!-- Sequence of 3 steps with connector lines -->
              <div
                v-for="(step, idx) in stepsAC"
                :key="step.n"
                class="flex gap-0"
              >
                <!-- Left column: badge + connector line -->
                <div class="flex flex-col items-center" style="width: 36px; flex-shrink: 0; margin-right: 12px;">
                  <!-- Circle badge -->
                  <div
                    class="w-9 h-9 rounded-full flex items-center justify-center text-[16px] font-semibold leading-none flex-shrink-0 z-10"
                    :style="step.state === 'done'
                      ? { background: L.status.success.bold, color: L.inkInverse }
                      : step.state === 'active'
                        ? { background: L.accents.lavender.bold, color: L.inkInverse, boxShadow: `0 0 0 3px ${L.accents.lavender.soft}` }
                        : { background: L.surfaceRaised, color: L.inkTertiary, border: `2px solid ${L.borderStrong}` }"
                  >
                    <CheckIcon v-if="step.state === 'done'" class="w-4 h-4" />
                    <span v-else>{{ step.n }}</span>
                  </div>

                  <!-- Connector line — rendered for all steps except the last -->
                  <div
                    v-if="idx < stepsAC.length - 1"
                    class="flex-1"
                    :style="{
                      width: '2px',
                      minHeight: '24px',
                      background: idx === 0 ? L.status.success.bold : L.inkTertiary,
                      opacity: idx === 0 ? '1' : '0.35',
                      margin: '2px 0',
                    }"
                  />
                </div>

                <!-- Right column: card content -->
                <div class="flex-1 min-w-0 pb-3">
                  <div
                    class="rounded-[20px] border p-5"
                    :style="step.state === 'done'
                      ? { background: L.surfaceSunken, borderColor: L.borderSubtle }
                      : step.state === 'active'
                        ? { background: L.surfaceRaised, borderColor: L.accents.lavender.bold, boxShadow: `0 0 0 3px ${L.accents.lavender.soft}` }
                        : { background: L.surfaceRaised, borderColor: L.borderSubtle }"
                  >
                    <!-- Header row -->
                    <div class="flex items-start justify-between gap-2">
                      <p
                        class="text-[15px] font-semibold leading-snug"
                        :class="step.state === 'done' ? 'line-through' : ''"
                        :style="step.state === 'done'
                          ? { color: L.inkTertiary }
                          : step.state === 'active'
                            ? { color: L.accents.lavender.bold }
                            : { color: L.inkPrimary }"
                      >{{ step.title }}</p>

                      <!-- Status chip / chevron -->
                      <span
                        v-if="step.state === 'done'"
                        class="inline-flex items-center h-5 px-2 rounded-full text-[11px] font-medium flex-shrink-0"
                        :style="{ background: L.status.success.soft, color: L.status.success.bold }"
                      >Done</span>
                      <ChevronUpIcon
                        v-else-if="step.state === 'active'"
                        class="w-4 h-4 flex-shrink-0"
                        :style="{ color: L.inkTertiary }"
                      />
                      <ChevronDownIcon
                        v-else
                        class="w-4 h-4 flex-shrink-0"
                        :style="{ color: L.inkTertiary }"
                      />
                    </div>

                    <!-- Body — only for expanded (active) and completed -->
                    <div v-if="step.state !== 'default'">
                      <p
                        class="text-[14px] leading-relaxed mt-2"
                        :class="step.state === 'done' ? 'line-through opacity-50' : ''"
                        :style="{ color: step.state === 'done' ? L.inkTertiary : L.inkSecondary }"
                      >{{ step.body }}</p>

                      <!-- Optional inline list (active step only) -->
                      <ul v-if="step.state === 'active' && step.hasList" class="mt-3 space-y-1.5">
                        <li
                          v-for="item in step.list"
                          :key="item"
                          class="flex items-center gap-2 text-[13px]"
                          :style="{ color: L.inkSecondary }"
                        >
                          <span class="w-1.5 h-1.5 rounded-full flex-shrink-0" :style="{ background: L.accents.lavender.bold }" />
                          {{ item }}
                        </li>
                      </ul>
                    </div>
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

            <div class="p-5">
              <div
                v-for="(step, idx) in stepsAC"
                :key="step.n"
                class="flex gap-0"
              >
                <!-- Left column: badge + connector line -->
                <div class="flex flex-col items-center" style="width: 36px; flex-shrink: 0; margin-right: 12px;">
                  <div
                    class="w-9 h-9 rounded-full flex items-center justify-center text-[16px] font-semibold leading-none flex-shrink-0 z-10"
                    :style="step.state === 'done'
                      ? { background: D.status.success.bold, color: D.inkInverse }
                      : step.state === 'active'
                        ? { background: D.accents.lavender.bold, color: D.inkInverse, boxShadow: `0 0 0 3px ${D.accents.lavender.soft}` }
                        : { background: D.surfaceRaised, color: D.inkTertiary, border: `2px solid ${D.borderStrong}` }"
                  >
                    <CheckIcon v-if="step.state === 'done'" class="w-4 h-4" />
                    <span v-else>{{ step.n }}</span>
                  </div>

                  <div
                    v-if="idx < stepsAC.length - 1"
                    class="flex-1"
                    :style="{
                      width: '2px',
                      minHeight: '24px',
                      background: idx === 0 ? D.status.success.bold : D.inkTertiary,
                      opacity: idx === 0 ? '1' : '0.35',
                      margin: '2px 0',
                    }"
                  />
                </div>

                <!-- Right column -->
                <div class="flex-1 min-w-0 pb-3">
                  <div
                    class="rounded-[20px] border p-5"
                    :style="step.state === 'done'
                      ? { background: D.surfaceSunken, borderColor: D.borderSubtle }
                      : step.state === 'active'
                        ? { background: D.surfaceRaised, borderColor: D.accents.lavender.bold, boxShadow: `0 0 0 3px ${D.accents.lavender.soft}` }
                        : { background: D.surfaceRaised, borderColor: D.borderSubtle }"
                  >
                    <div class="flex items-start justify-between gap-2">
                      <p
                        class="text-[15px] font-semibold leading-snug"
                        :class="step.state === 'done' ? 'line-through' : ''"
                        :style="step.state === 'done'
                          ? { color: D.inkTertiary }
                          : step.state === 'active'
                            ? { color: D.accents.lavender.bold }
                            : { color: D.inkPrimary }"
                      >{{ step.title }}</p>

                      <span
                        v-if="step.state === 'done'"
                        class="inline-flex items-center h-5 px-2 rounded-full text-[11px] font-medium flex-shrink-0"
                        :style="{ background: D.status.success.soft, color: D.status.success.bold }"
                      >Done</span>
                      <ChevronUpIcon
                        v-else-if="step.state === 'active'"
                        class="w-4 h-4 flex-shrink-0"
                        :style="{ color: D.inkTertiary }"
                      />
                      <ChevronDownIcon
                        v-else
                        class="w-4 h-4 flex-shrink-0"
                        :style="{ color: D.inkTertiary }"
                      />
                    </div>

                    <div v-if="step.state !== 'default'">
                      <p
                        class="text-[14px] leading-relaxed mt-2"
                        :class="step.state === 'done' ? 'line-through opacity-50' : ''"
                        :style="{ color: step.state === 'done' ? D.inkTertiary : D.inkSecondary }"
                      >{{ step.body }}</p>

                      <ul v-if="step.state === 'active' && step.hasList" class="mt-3 space-y-1.5">
                        <li
                          v-for="item in step.list"
                          :key="item"
                          class="flex items-center gap-2 text-[13px]"
                          :style="{ color: D.inkSecondary }"
                        >
                          <span class="w-1.5 h-1.5 rounded-full flex-shrink-0" :style="{ background: D.accents.lavender.bold }" />
                          {{ item }}
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <p class="text-xs" :style="{ color: L.inkTertiary }">
            375px — connector line is 2px wide and stretches between circles regardless of card height. Completed connector uses status.success.bold; upcoming connectors use inkTertiary at 35% opacity. The line is inside a flex column so it always stretches to fill the gap.
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
            LOCKED — single shape, no variants
          </p>
          <p class="text-sm leading-relaxed" :style="{ color: L.inkPrimary }">
            The connector line in Variant C does something neither A nor B can: it makes sequence feel spatial. When a user is mid-recipe or mid-playbook, the line visually anchors where they are relative to where they came from and where they are going — the completed green line above, the faint upcoming line below. This "progress spine" is especially valuable on mobile where the whole flow is not visible at once. Variant A is cleaner for short recipe card lists but loses orientation on longer flows; Variant B earns its place only on fullscreen onboarding moments. Variant C scales gracefully across all three use cases and should be the default implementation.
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
            Match the variant to the context's need for spatial orientation and visual weight.
          </p>
        </div>

        <!-- Variant A -->
        <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-[160px_1fr] gap-3">
          <div>
            <span
              class="inline-flex items-center justify-center h-6 min-w-[1.5rem] px-2 rounded-full text-xs font-semibold"
              :style="{ background: L.inkPrimary, color: L.inkInverse }"
            >A — Inline badge</span>
          </div>
          <div class="space-y-1">
            <p class="text-sm font-medium" :style="{ color: L.inkPrimary }">Recipe instructions, short task checklists, vault entry sub-steps</p>
            <p class="text-sm" :style="{ color: L.inkSecondary }">
              Use when steps are linear but the list is short (3–6 items) and each card needs to stand alone. The 36px badge is compact enough to fit a kitchen-counter phone viewport. Collapse to the title-only row when a step is not yet relevant to reduce cognitive load. This is the most common variant — default to it unless there is a strong reason for the others.
            </p>
          </div>
        </div>

        <!-- Variant B -->
        <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-[160px_1fr] gap-3">
          <div>
            <span
              class="inline-flex items-center justify-center h-6 min-w-[1.5rem] px-2 rounded-full text-xs font-semibold"
              :style="{ background: L.inkPrimary, color: L.inkInverse }"
            >B — Hero number</span>
          </div>
          <div class="space-y-1">
            <p class="text-sm font-medium" :style="{ color: L.inkPrimary }">Ceremonial onboarding (first-run wizard), vault playbook cover steps</p>
            <p class="text-sm" :style="{ color: L.inkSecondary }">
              Reserve Variant B for moments that deserve visual weight — first launch, a new major module unlock, or the cover step of a vault playbook where Greg wants to set tone before diving in. The hero number communicates "this is a big step" without words. Do not use for everyday task flows; the large number will feel oversized and slow in routine contexts.
            </p>
          </div>
        </div>

        <!-- Variant C -->
        <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-[160px_1fr] gap-3">
          <div>
            <span
              class="inline-flex items-center justify-center h-6 min-w-[1.5rem] px-2 rounded-full text-xs font-semibold"
              :style="{ background: L.inkPrimary, color: L.inkInverse }"
            >C — Stepper</span>
          </div>
          <div class="space-y-1">
            <p class="text-sm font-medium" :style="{ color: L.inkPrimary }">Multi-step wizards, vault playbook guides, meal-plan cook flow, onboarding with 4+ steps</p>
            <p class="text-sm" :style="{ color: L.inkSecondary }">
              Use whenever there are 4 or more steps and the user needs to track progress across them. The connector line is the key signal — completed steps get a solid green spine, upcoming steps get a faint inkTertiary line. Only render the connector between consecutive circles, not after the last step. On mobile, the user scrolls through the spine and always has a visual thread back to where they came from.
            </p>
          </div>
        </div>

        <!-- States reference -->
        <div class="px-6 py-4">
          <p class="text-xs font-semibold uppercase tracking-widest mb-3" :style="{ color: L.inkTertiary }">Step states</p>
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
              <p class="text-sm font-medium mb-1" :style="{ color: L.inkPrimary }">Default (upcoming)</p>
              <p class="text-sm" :style="{ color: L.inkSecondary }">
                Number badge with unfilled border, inkTertiary text. Collapsed to title only. Connector line at 35% opacity.
              </p>
            </div>
            <div>
              <p class="text-sm font-medium mb-1" :style="{ color: L.inkPrimary }">Active (current)</p>
              <p class="text-sm" :style="{ color: L.inkSecondary }">
                Lavender-bold fill badge with 3px lavender-soft ring. Title in lavender-bold. Card gets lavender border + ring. Expanded body visible.
              </p>
            </div>
            <div>
              <p class="text-sm font-medium mb-1" :style="{ color: L.inkPrimary }">Completed</p>
              <p class="text-sm" :style="{ color: L.inkSecondary }">
                Check icon in success-bold badge. Sunken card surface. Title strikethrough + inkTertiary. Body faded at 50% opacity. "Done" pill top-right. Connector line in success-bold.
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

  </ComponentPage>
</template>
