<script setup>
import ComponentPage  from '../../shared/ComponentPage.vue'
import VariantFrame   from '../../shared/VariantFrame.vue'
import KinAvatar      from '@/components/design-system/KinAvatar.vue'
import { StarIcon, BoltIcon } from '@heroicons/vue/24/solid'

// ── Demo data ────────────────────────────────────────────────────────────────
// Qualls family — each member gets one of the four accent colors.
const FAMILY = [
  { key: 'maya', name: 'Maya Qualls', color: 'lavender', photo: 'https://i.pravatar.cc/160?img=47' },
  { key: 'emma', name: 'Emma Qualls', color: 'peach',    photo: 'https://i.pravatar.cc/160?img=44' },
  { key: 'ava',  name: 'Ava Qualls',  color: 'mint',     photo: 'https://i.pravatar.cc/160?img=45' },
  { key: 'greg', name: 'Greg Qualls', color: 'sun',      photo: 'https://i.pravatar.cc/160?img=12' },
]

const SIZES     = ['xs', 'sm', 'md', 'lg', 'xl']
const ARC_PCTS  = [0, 0.25, 0.5, 0.75, 1.0]
const ARC_SIZES = ['sm', 'md', 'lg', 'xl']

const CONTENT_DEMOS = [
  { label: 'Photo',         src: FAMILY[0].photo, name: FAMILY[0].name, icon: null },
  { label: 'Icon',          src: null,            name: null,           icon: StarIcon },
  { label: 'Initials',      src: null,            name: 'Maya Qualls',  icon: null },
  { label: 'Fallback (?)',  src: null,            name: null,           icon: null },
]

const PRESENCE_DEMOS = [
  { label: 'Online (photo)', src: FAMILY[0].photo, name: FAMILY[0].name, icon: null,     presence: 'online', color: 'lavender' },
  { label: 'Online (icon)',  src: null,            name: null,           icon: BoltIcon,  presence: 'online', color: 'peach'    },
  { label: 'Busy (initials)',src: null,            name: 'Ava Qualls',   icon: null,      presence: 'busy',   color: 'mint'     },
  { label: 'Away',           src: null,            name: 'Greg Qualls',  icon: null,      presence: 'away',   color: 'sun'      },
]

const ACHIEVEMENT_DEMOS = [
  { label: '100% — Earned',     pct: 1.0,  opacity: '1'   },
  { label: '60% — In progress', pct: 0.6,  opacity: '1'   },
  { label: '0% — Not started',  pct: null, opacity: '0.35' },
]
</script>

<template>
  <ComponentPage
    title="1.4 Avatar"
    description="Family-aware avatar: photo / preset icon / initials fallback, five sizes (xs–xl). Default is a clean ringless circle — the daily-driver. The arc-ring is an additive affordance applied only where progress communicates state (achievements, weekly task %, badge arcs)."
    status="chosen"
  >
    <!-- ══════════════════════════════════════════════════════════════
         SECTION 1 — BASE AVATAR (clean circle, no ring)
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="Avatar" caption="Clean circle — the default daily-driver">
        <div class="w-full space-y-10">
          <!-- Light panel -->
          <div class="rounded-2xl border border-border-subtle bg-surface-app p-6 space-y-8">
            <p class="text-xs font-semibold uppercase tracking-widest text-ink-tertiary">Light mode</p>

            <!-- Row 1: Size scale (photo) -->
            <div>
              <p class="text-xs mb-3 text-ink-tertiary">Size scale — photo avatar at xs / sm / md / lg / xl</p>
              <div class="flex flex-wrap items-end gap-4">
                <div v-for="sz in SIZES" :key="sz" class="flex flex-col items-center gap-1.5">
                  <KinAvatar :src="FAMILY[0].photo" :name="FAMILY[0].name" :size="sz" color="lavender" />
                  <span class="text-[10px] text-ink-tertiary">{{ sz }}</span>
                </div>
              </div>
            </div>

            <!-- Row 2: Content types at md -->
            <div>
              <p class="text-xs mb-3 text-ink-tertiary">Content types — md, lavender</p>
              <div class="flex flex-wrap items-end gap-4">
                <div v-for="d in CONTENT_DEMOS" :key="d.label" class="flex flex-col items-center gap-1.5">
                  <KinAvatar :src="d.src" :name="d.name" :icon="d.icon" size="md" color="lavender" />
                  <span class="text-[10px] text-ink-tertiary">{{ d.label }}</span>
                </div>
              </div>
            </div>

            <!-- Row 3: Family-color palette (initials, all 4 colors) -->
            <div>
              <p class="text-xs mb-3 text-ink-tertiary">Family-color palette (optional)</p>
              <div class="flex flex-wrap items-end gap-4">
                <div v-for="m in FAMILY" :key="m.key" class="flex flex-col items-center gap-1.5">
                  <KinAvatar :name="m.name" :color="m.color" size="md" />
                  <span class="text-[10px] text-ink-tertiary">{{ m.name.split(' ')[0] }}</span>
                </div>
              </div>
              <p class="mt-2 text-[11px] leading-snug text-ink-tertiary">
                These four illustrate the family-color system. Production uses the full 12-color palette from onboarding.
              </p>
            </div>

            <!-- Row 4: Optional ring -->
            <div>
              <p class="text-xs mb-3 text-ink-tertiary">Optional family-color ring — 2px solid + 2px gap</p>
              <div class="flex flex-wrap items-end gap-4">
                <div v-for="m in FAMILY" :key="m.key" class="flex flex-col items-center gap-1.5">
                  <KinAvatar :name="m.name" :color="m.color" size="md" :ring="true" />
                  <span class="text-[10px] text-ink-tertiary">{{ m.name.split(' ')[0] }}</span>
                </div>
              </div>
              <p class="mt-2 text-[11px] leading-snug text-ink-tertiary">
                Optional treatment — use when a user is actively interacting or for the viewer's own avatar in the nav.
              </p>
            </div>

            <!-- Row 5: Presence dots -->
            <div>
              <p class="text-xs mb-3 text-ink-tertiary">Presence dot — 10px dot, bottom-right, 2px surface-app border</p>
              <div class="flex flex-wrap items-end gap-4">
                <div v-for="d in PRESENCE_DEMOS" :key="d.label" class="flex flex-col items-center gap-1.5">
                  <KinAvatar :src="d.src" :name="d.name" :icon="d.icon" :color="d.color" :presence="d.presence" size="md" />
                  <span class="text-[10px] text-ink-tertiary">{{ d.label }}</span>
                </div>
              </div>
            </div>
          </div><!-- /light -->

          <!-- Dark panel -->
          <div class="dark rounded-2xl border border-border-subtle bg-surface-app p-6 space-y-8">
            <p class="text-xs font-semibold uppercase tracking-widest text-ink-tertiary">Dark mode</p>

            <!-- Size scale dark -->
            <div>
              <p class="text-xs mb-3 text-ink-tertiary">Size scale</p>
              <div class="flex flex-wrap items-end gap-4">
                <div v-for="sz in SIZES" :key="sz" class="flex flex-col items-center gap-1.5">
                  <KinAvatar :src="FAMILY[0].photo" :name="FAMILY[0].name" :size="sz" color="lavender" />
                  <span class="text-[10px] text-ink-tertiary">{{ sz }}</span>
                </div>
              </div>
            </div>

            <!-- Content types dark -->
            <div>
              <p class="text-xs mb-3 text-ink-tertiary">Content types — md, lavender</p>
              <div class="flex flex-wrap items-end gap-4">
                <div v-for="d in CONTENT_DEMOS" :key="d.label" class="flex flex-col items-center gap-1.5">
                  <KinAvatar :src="d.src" :name="d.name" :icon="d.icon" size="md" color="lavender" />
                  <span class="text-[10px] text-ink-tertiary">{{ d.label }}</span>
                </div>
              </div>
            </div>

            <!-- Family-color palette dark -->
            <div>
              <p class="text-xs mb-3 text-ink-tertiary">Family-color palette (optional)</p>
              <div class="flex flex-wrap items-end gap-4">
                <div v-for="m in FAMILY" :key="m.key" class="flex flex-col items-center gap-1.5">
                  <KinAvatar :name="m.name" :color="m.color" size="md" />
                  <span class="text-[10px] text-ink-tertiary">{{ m.name.split(' ')[0] }}</span>
                </div>
              </div>
              <p class="mt-2 text-[11px] leading-snug text-ink-tertiary">
                These four illustrate the family-color system. Production uses the full 12-color palette from onboarding.
              </p>
            </div>

            <!-- Ring dark -->
            <div>
              <p class="text-xs mb-3 text-ink-tertiary">Optional family-color ring</p>
              <div class="flex flex-wrap items-end gap-4">
                <div v-for="m in FAMILY" :key="m.key" class="flex flex-col items-center gap-1.5">
                  <KinAvatar :name="m.name" :color="m.color" size="md" :ring="true" />
                  <span class="text-[10px] text-ink-tertiary">{{ m.name.split(' ')[0] }}</span>
                </div>
              </div>
              <p class="mt-2 text-[11px] leading-snug text-ink-tertiary">
                Optional treatment — use when a user is actively interacting or for the viewer's own avatar in the nav.
              </p>
            </div>

            <!-- Presence dark -->
            <div>
              <p class="text-xs mb-3 text-ink-tertiary">Presence dot</p>
              <div class="flex flex-wrap items-end gap-4">
                <div v-for="d in PRESENCE_DEMOS" :key="d.label" class="flex flex-col items-center gap-1.5">
                  <KinAvatar :src="d.src" :name="d.name" :icon="d.icon" :color="d.color" :presence="d.presence" size="md" />
                  <span class="text-[10px] text-ink-tertiary">{{ d.label }}</span>
                </div>
              </div>
            </div>
          </div><!-- /dark -->
        </div>
      </VariantFrame>

      <p class="mt-3 text-body-sm text-ink-secondary px-1">
        The default avatar carries only the person. Rings, dots, and arcs are additive affordances applied where they communicate something.
      </p>
    </section>

    <!-- ══════════════════════════════════════════════════════════════
         SECTION 2 — ARC AVATAR (achievement / progress state)
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="Arc" caption="Arc progress overlay — achievement rings and progress toward a goal">
        <div class="w-full space-y-10">
          <!-- Light panel -->
          <div class="rounded-2xl border border-border-subtle bg-surface-app p-6 space-y-8">
            <p class="text-xs font-semibold uppercase tracking-widest text-ink-tertiary">Light mode</p>

            <!-- Row 1: Progress scale 0–100% -->
            <div>
              <p class="text-xs mb-3 text-ink-tertiary">Progress scale — 0% / 25% / 50% / 75% / 100% (lavender arc)</p>
              <div class="flex flex-wrap items-end gap-6">
                <div v-for="pct in ARC_PCTS" :key="pct" class="flex flex-col items-center gap-1.5">
                  <KinAvatar :src="FAMILY[0].photo" :name="FAMILY[0].name" size="md" color="lavender" :progress="pct" />
                  <span class="text-[10px] text-ink-tertiary">{{ Math.round(pct * 100) }}%</span>
                </div>
              </div>
              <p class="mt-2 text-[11px] leading-snug text-ink-tertiary">
                At 100% the arc becomes a solid ring (stroke-linecap: butt, no visible gap) — hints at the "earned" state.
              </p>
            </div>

            <!-- Row 2: Arc in family color (65%, initials) -->
            <div>
              <p class="text-xs mb-3 text-ink-tertiary">Arc in family color — 65% fill, initials avatar</p>
              <div class="flex flex-wrap items-end gap-6">
                <div v-for="m in FAMILY" :key="m.key" class="flex flex-col items-center gap-1.5">
                  <KinAvatar :name="m.name" :color="m.color" size="md" :progress="0.65" />
                  <span class="text-[10px] text-ink-tertiary">{{ m.name.split(' ')[0] }}</span>
                </div>
              </div>
              <p class="mt-2 text-[11px] leading-snug text-ink-tertiary">
                Arc can wear the family color for personal progress visualizations.
              </p>
            </div>

            <!-- Row 3: Size scale at 60% -->
            <div>
              <p class="text-xs mb-3 text-ink-tertiary">Sizes — sm / md / lg / xl at 60% fill (arc stroke scales)</p>
              <div class="flex flex-wrap items-end gap-6">
                <div v-for="sz in ARC_SIZES" :key="sz" class="flex flex-col items-center gap-1.5">
                  <KinAvatar :src="FAMILY[0].photo" :name="FAMILY[0].name" :size="sz" color="lavender" :progress="0.6" />
                  <span class="text-[10px] text-ink-tertiary">{{ sz }}</span>
                </div>
              </div>
            </div>

            <!-- Row 4: Achievement context demo -->
            <div>
              <p class="text-xs mb-3 text-ink-tertiary">Achievement context — not started / in-progress / earned</p>
              <div class="flex flex-wrap items-end gap-8">
                <div v-for="d in ACHIEVEMENT_DEMOS" :key="d.label" class="flex flex-col items-center gap-1.5">
                  <div :style="{ opacity: d.opacity }">
                    <KinAvatar
                      :src="d.pct === null ? FAMILY[3].photo : (d.pct === 1.0 ? FAMILY[1].photo : FAMILY[0].photo)"
                      :name="d.pct === null ? FAMILY[3].name : (d.pct === 1.0 ? FAMILY[1].name : FAMILY[0].name)"
                      size="md"
                      color="lavender"
                      :progress="d.pct"
                    />
                  </div>
                  <span class="text-[10px] text-ink-tertiary">{{ d.label }}</span>
                </div>
              </div>
              <p class="mt-2 text-[11px] leading-snug text-ink-tertiary">
                Per the achievement pattern: 0% = not started (no arc, reduced opacity), mid-range = in-progress arc, 100% = solid ring = earned.
              </p>
            </div>
          </div><!-- /light arc -->

          <!-- Dark panel -->
          <div class="dark rounded-2xl border border-border-subtle bg-surface-app p-6 space-y-8">
            <p class="text-xs font-semibold uppercase tracking-widest text-ink-tertiary">Dark mode</p>

            <!-- Progress scale dark -->
            <div>
              <p class="text-xs mb-3 text-ink-tertiary">Progress scale — 0% / 25% / 50% / 75% / 100%</p>
              <div class="flex flex-wrap items-end gap-6">
                <div v-for="pct in ARC_PCTS" :key="pct" class="flex flex-col items-center gap-1.5">
                  <KinAvatar :src="FAMILY[0].photo" :name="FAMILY[0].name" size="md" color="lavender" :progress="pct" />
                  <span class="text-[10px] text-ink-tertiary">{{ Math.round(pct * 100) }}%</span>
                </div>
              </div>
            </div>

            <!-- Arc + family color dark -->
            <div>
              <p class="text-xs mb-3 text-ink-tertiary">Arc in family color — 65% fill</p>
              <div class="flex flex-wrap items-end gap-6">
                <div v-for="m in FAMILY" :key="m.key" class="flex flex-col items-center gap-1.5">
                  <KinAvatar :name="m.name" :color="m.color" size="md" :progress="0.65" />
                  <span class="text-[10px] text-ink-tertiary">{{ m.name.split(' ')[0] }}</span>
                </div>
              </div>
              <p class="mt-2 text-[11px] leading-snug text-ink-tertiary">
                Arc can wear the family color for personal progress visualizations.
              </p>
            </div>

            <!-- Sizes dark -->
            <div>
              <p class="text-xs mb-3 text-ink-tertiary">Sizes — sm / md / lg / xl at 60%</p>
              <div class="flex flex-wrap items-end gap-6">
                <div v-for="sz in ARC_SIZES" :key="sz" class="flex flex-col items-center gap-1.5">
                  <KinAvatar :src="FAMILY[0].photo" :name="FAMILY[0].name" :size="sz" color="lavender" :progress="0.6" />
                  <span class="text-[10px] text-ink-tertiary">{{ sz }}</span>
                </div>
              </div>
            </div>

            <!-- Achievement context dark -->
            <div>
              <p class="text-xs mb-3 text-ink-tertiary">Achievement context — not started / in-progress / earned</p>
              <div class="flex flex-wrap items-end gap-8">
                <div v-for="d in ACHIEVEMENT_DEMOS" :key="d.label" class="flex flex-col items-center gap-1.5">
                  <div :style="{ opacity: d.opacity }">
                    <KinAvatar
                      :src="d.pct === null ? FAMILY[3].photo : (d.pct === 1.0 ? FAMILY[1].photo : FAMILY[0].photo)"
                      :name="d.pct === null ? FAMILY[3].name : (d.pct === 1.0 ? FAMILY[1].name : FAMILY[0].name)"
                      size="md"
                      color="lavender"
                      :progress="d.pct"
                    />
                  </div>
                  <span class="text-[10px] text-ink-tertiary">{{ d.label }}</span>
                </div>
              </div>
              <p class="mt-2 text-[11px] leading-snug text-ink-tertiary">
                Per the achievement pattern: 0% = not started (no arc, reduced opacity), mid-range = in-progress arc, 100% = solid ring = earned.
              </p>
            </div>
          </div><!-- /dark arc -->
        </div>
      </VariantFrame>
    </section>

    <!-- ══════════════════════════════════════════════════════════════
         USAGE GUIDE
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-8">
      <div class="rounded-2xl border border-border-subtle bg-surface-raised p-6 space-y-4">
        <h2 class="text-h4 font-semibold text-ink-primary">When to use which</h2>
        <ul class="space-y-3 text-body-sm text-ink-secondary">
          <li>
            <strong class="text-ink-primary">Avatar (default):</strong>
            Any time a person is represented — task rows, feeds, calendar events, assignment pickers. The workhorse. No ring at rest; the photo or initials carry the identity.
          </li>
          <li>
            <strong class="text-ink-primary">Arc overlay:</strong>
            Only where progress communicates state — in-progress achievements, weekly task completion %, badge arcs. Additive; the avatar itself remains the default. The arc is a second layer applied situationally.
          </li>
        </ul>
      </div>
    </section>
  </ComponentPage>
</template>
