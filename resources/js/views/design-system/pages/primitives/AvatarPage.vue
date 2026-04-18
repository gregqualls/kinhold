<script setup>
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'

// ── Dark-mode hex constants (from tokens.css html.dark section) ─────────────
// Dark panel always uses hardcoded hex so it renders dark regardless of root theme.
const D = {
  surfaceApp:    '#141311',
  surfaceRaised: '#1C1B19',
  surfaceSunken: '#161513',
  inkPrimary:    '#F0EDE9',
  inkSecondary:  '#A09C97',
  inkTertiary:   '#6E6B67',
  borderSubtle:  '#2C2A27',

  accents: {
    lavender: { soft: '#302A48', bold: '#B6A8E6' },
    peach:    { soft: '#3E241A', bold: '#F0A882' },
    mint:     { soft: '#18342A', bold: '#7CD6AE' },
    sun:      { soft: '#342C0A', bold: '#E6C452' },
  },

  presenceOnline: '#6CC498',
  presenceBorder: '#141311',
}

// ── Light-mode hex constants ─────────────────────────────────────────────────
const L = {
  surfaceApp:    '#FAF8F5',
  surfaceRaised: '#FFFFFF',
  surfaceSunken: '#F5F2EE',
  inkPrimary:    '#1C1C1E',
  inkSecondary:  '#6B6966',
  inkTertiary:   '#9C9895',
  borderSubtle:  '#E8E4DF',

  accents: {
    lavender: { soft: '#EAE6F8', bold: '#6856B2' },
    peach:    { soft: '#FCE9E0', bold: '#BA562E' },
    mint:     { soft: '#D5F2E8', bold: '#2E8A62' },
    sun:      { soft: '#FCF3D2', bold: '#A2780C' },
  },

  presenceOnline: '#4D8C6A',
  presenceBorder: '#FAF8F5',
}

// ── Size definitions ─────────────────────────────────────────────────────────
const SIZES = [
  { key: 'xs',  px: 24,  label: 'xs',  textSize: '10px',   arcStroke: 2,   presencePx: 6,  presenceBorder: '1px',   dotOffset: '-1.5px' },
  { key: 'sm',  px: 32,  label: 'sm',  textSize: '11px',   arcStroke: 2.5, presencePx: 8,  presenceBorder: '1.5px', dotOffset: '-1px'   },
  { key: 'md',  px: 40,  label: 'md',  textSize: '13px',   arcStroke: 3,   presencePx: 10, presenceBorder: '2px',   dotOffset: '-1px'   },
  { key: 'lg',  px: 56,  label: 'lg',  textSize: '17px',   arcStroke: 3,   presencePx: 12, presenceBorder: '2px',   dotOffset: '-1px'   },
  { key: 'xl',  px: 80,  label: 'xl',  textSize: '22px',   arcStroke: 4,   presencePx: 16, presenceBorder: '2.5px', dotOffset: '-2px'   },
]

// ── Qualls family demo data ──────────────────────────────────────────────────
// Maya → lavender, Emma → peach, Ava → mint, Greg → sun
const FAMILY = [
  { key: 'maya', name: 'Maya', initials: 'MQ', color: 'lavender',
    photo: 'https://i.pravatar.cc/160?img=47' },
  { key: 'emma', name: 'Emma', initials: 'EQ', color: 'peach',
    photo: 'https://i.pravatar.cc/160?img=44' },
  { key: 'ava',  name: 'Ava',  initials: 'AQ', color: 'mint',
    photo: 'https://i.pravatar.cc/160?img=45' },
  { key: 'greg', name: 'Greg', initials: 'GQ', color: 'sun',
    photo: 'https://i.pravatar.cc/160?img=12' },
]

// ── Cluster (5 members — uses photos from FAMILY + one extra) ────────────────
const CLUSTER = [
  ...FAMILY,
  { key: 'extra', name: 'Sam', initials: 'SQ', color: 'lavender',
    photo: 'https://i.pravatar.cc/160?img=32' },
]

// ── Arc SVG math ─────────────────────────────────────────────────────────────
// viewBox is 100×100. We place the circle at cx/cy=50.
// Radius is computed so the stroke (in viewBox units scaled from real px) sits
// fully inside the 100-unit square with a small inset gap.
// We use r=46 (matching the spec example), circumference = 2π×46 ≈ 289.03.
const ARC_R = 46
const ARC_CIRC = 2 * Math.PI * ARC_R  // ≈ 289.03

function arcOffset(pct) {
  // pct 0→1. dashoffset = circ * (1 - pct)
  return ARC_CIRC * (1 - pct)
}

// Arc percentages for the progress scale row
const ARC_PCTS = [0, 0.25, 0.5, 0.75, 1.0]

// Arc sizes for the sizes row (sm / md / lg / xl — skip xs, too small for visible arc)
const ARC_SIZES = SIZES.filter(s => ['sm','md','lg','xl'].includes(s.key))

// Achievement context demo
const ACHIEVEMENT_DEMOS = [
  { label: '100% — Earned', pct: 1.0,  grayscale: false },
  { label: '60% — In progress', pct: 0.6, grayscale: false },
  { label: '0% — Not started',  pct: 0,   grayscale: true  },
]
</script>

<template>
  <ComponentPage
    title="1.4 Avatar"
    description="Family-aware avatar: photo / preset icon / initials fallback, five sizes (xs–xl). Default is a clean ringless circle — the daily-driver. The arc-ring is an additive affordance applied only where progress communicates state (achievements, weekly task %, badge arcs)."
    status="chosen"
  >

    <!-- ══════════════════════════════════════════════════════════════
         SECTION 1 — AVATAR (clean circle, no ring)
         The default daily-driver. Rings/dots/arcs are additive.
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="Avatar" caption="Clean circle — the default daily-driver">
        <div class="w-full space-y-10">

          <!-- ── LIGHT PANEL ───────────────────────────────────────── -->
          <div class="rounded-2xl border p-6 space-y-8" style="background:#FAF8F5; border-color:#E8E4DF">
            <p class="text-xs font-semibold uppercase tracking-widest" style="color:#9C9895">Light mode</p>

            <!-- Row 1: Size scale (photo) -->
            <div>
              <p class="text-xs mb-3" style="color:#9C9895">Size scale — photo avatar at xs / sm / md / lg / xl</p>
              <div class="flex flex-wrap items-end gap-4">
                <div v-for="sz in SIZES" :key="sz.key" class="flex flex-col items-center gap-1.5">
                  <div
                    class="av-lt relative rounded-full overflow-hidden flex-shrink-0"
                    :style="{ width: sz.px + 'px', height: sz.px + 'px' }"
                  >
                    <img :src="FAMILY[0].photo" :alt="FAMILY[0].name" class="w-full h-full object-cover" />
                  </div>
                  <span class="text-[10px]" style="color:#9C9895">{{ sz.label }}</span>
                </div>
              </div>
            </div>

            <!-- Row 2: Content types (photo / preset icon / initials) at md -->
            <div>
              <p class="text-xs mb-3" style="color:#9C9895">Content types — md, lavender family color</p>
              <div class="flex flex-wrap items-end gap-4">
                <!-- Photo -->
                <div class="flex flex-col items-center gap-1.5">
                  <div class="av-lt w-10 h-10 rounded-full overflow-hidden flex-shrink-0">
                    <img :src="FAMILY[0].photo" alt="Maya" class="w-full h-full object-cover" />
                  </div>
                  <span class="text-[10px]" style="color:#9C9895">Photo</span>
                </div>
                <!-- Preset icon (StarIcon path inline) -->
                <div class="flex flex-col items-center gap-1.5">
                  <div class="av-lt w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0"
                    :style="{ background: L.accents.lavender.soft }">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                      :stroke="L.accents.lavender.bold" stroke-width="1.75"
                      stroke-linecap="round" stroke-linejoin="round">
                      <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                    </svg>
                  </div>
                  <span class="text-[10px]" style="color:#9C9895">Preset icon</span>
                </div>
                <!-- Initials -->
                <div class="flex flex-col items-center gap-1.5">
                  <div class="av-lt w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0"
                    :style="{ background: L.accents.lavender.soft }">
                    <span class="font-semibold text-[13px]" :style="{ color: L.accents.lavender.bold }">MQ</span>
                  </div>
                  <span class="text-[10px]" style="color:#9C9895">Initials</span>
                </div>
              </div>
            </div>

            <!-- Row 3: Family-color palette (initials, all 4 colors) -->
            <div>
              <p class="text-xs mb-3" style="color:#9C9895">Family-color palette (optional)</p>
              <div class="flex flex-wrap items-end gap-4">
                <div v-for="m in FAMILY" :key="m.key" class="flex flex-col items-center gap-1.5">
                  <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0"
                    :style="{ background: L.accents[m.color].soft }">
                    <span class="font-semibold text-[13px]" :style="{ color: L.accents[m.color].bold }">{{ m.initials }}</span>
                  </div>
                  <span class="text-[10px]" style="color:#9C9895">{{ m.name }}</span>
                </div>
              </div>
              <p class="mt-2 text-[11px] leading-snug" style="color:#9C9895">
                These four illustrate the family-color system. Production uses the full 12-color palette from onboarding.
              </p>
            </div>

            <!-- Row 4: Optional family-color ring -->
            <div>
              <p class="text-xs mb-3" style="color:#9C9895">Optional family-color ring — 2px solid + 2px gap</p>
              <div class="flex flex-wrap items-end gap-4">
                <div v-for="m in FAMILY" :key="m.key" class="flex flex-col items-center gap-1.5">
                  <!-- gap-ring via double box-shadow: inner gap in surface-app color, outer ring in accent -->
                  <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0"
                    :style="{
                      background: L.accents[m.color].soft,
                      boxShadow: `0 0 0 2px ${L.surfaceApp}, 0 0 0 4px ${L.accents[m.color].bold}`,
                    }">
                    <span class="font-semibold text-[13px]" :style="{ color: L.accents[m.color].bold }">{{ m.initials }}</span>
                  </div>
                  <span class="text-[10px]" style="color:#9C9895">{{ m.name }}</span>
                </div>
              </div>
              <p class="mt-2 text-[11px] leading-snug" style="color:#9C9895">
                Optional treatment — use when a user is actively interacting or for the viewer's own avatar in the nav.
              </p>
            </div>

            <!-- Row 5: Presence dot row -->
            <div>
              <p class="text-xs mb-3" style="color:#9C9895">Presence dot — 10px dot, bottom-right, 2px white border</p>
              <div class="flex flex-wrap items-end gap-4">
                <!-- Photo with dot -->
                <div class="flex flex-col items-center gap-1.5">
                  <div class="av-lt relative w-10 h-10 flex-shrink-0">
                    <div class="w-10 h-10 rounded-full overflow-hidden">
                      <img :src="FAMILY[0].photo" alt="Maya" class="w-full h-full object-cover" />
                    </div>
                    <span class="presence-dot absolute rounded-full"
                      :style="{
                        width: '10px', height: '10px',
                        background: L.presenceOnline,
                        border: `2px solid ${L.presenceBorder}`,
                        bottom: '-1px', right: '-1px',
                      }"></span>
                  </div>
                  <span class="text-[10px]" style="color:#9C9895">Photo</span>
                </div>
                <!-- Preset icon with dot -->
                <div class="flex flex-col items-center gap-1.5">
                  <div class="av-lt relative w-10 h-10 flex-shrink-0">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center"
                      :style="{ background: L.accents.peach.soft }">
                      <!-- BoltIcon path -->
                      <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                        :stroke="L.accents.peach.bold" stroke-width="1.75"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/>
                      </svg>
                    </div>
                    <span class="presence-dot absolute rounded-full"
                      :style="{
                        width: '10px', height: '10px',
                        background: L.presenceOnline,
                        border: `2px solid ${L.presenceBorder}`,
                        bottom: '-1px', right: '-1px',
                      }"></span>
                  </div>
                  <span class="text-[10px]" style="color:#9C9895">Preset icon</span>
                </div>
                <!-- Initials with dot -->
                <div class="flex flex-col items-center gap-1.5">
                  <div class="av-lt relative w-10 h-10 flex-shrink-0">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center"
                      :style="{ background: L.accents.mint.soft }">
                      <span class="font-semibold text-[13px]" :style="{ color: L.accents.mint.bold }">AQ</span>
                    </div>
                    <span class="presence-dot absolute rounded-full"
                      :style="{
                        width: '10px', height: '10px',
                        background: L.presenceOnline,
                        border: `2px solid ${L.presenceBorder}`,
                        bottom: '-1px', right: '-1px',
                      }"></span>
                  </div>
                  <span class="text-[10px]" style="color:#9C9895">Initials</span>
                </div>
              </div>
            </div>

            <!-- Row 6: Stacked cluster (5 avatars) -->
            <div>
              <p class="text-xs mb-3" style="color:#9C9895">Stacked avatars (cluster) — overlap, last avatar is highest z-index</p>
              <div class="flex items-center">
                <div
                  v-for="(m, i) in CLUSTER" :key="m.key"
                  class="av-lt w-10 h-10 rounded-full overflow-hidden flex-shrink-0"
                  :style="{
                    marginLeft: i === 0 ? '0' : '-10px',
                    zIndex: CLUSTER.length - i,
                    outline: `2px solid ${L.surfaceRaised}`,
                    position: 'relative',
                  }"
                >
                  <img :src="m.photo" :alt="m.name" class="w-full h-full object-cover" />
                </div>
                <span class="ml-3 text-[13px] font-medium" style="color:#1C1C1E">5 family members — used in shared-task affordances</span>
              </div>
            </div>
          </div><!-- /light -->

          <!-- ── DARK PANEL ─────────────────────────────────────────── -->
          <div class="rounded-2xl border p-6 space-y-8" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

            <!-- Size scale -->
            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">Size scale</p>
              <div class="flex flex-wrap items-end gap-4">
                <div v-for="sz in SIZES" :key="sz.key" class="flex flex-col items-center gap-1.5">
                  <div class="av-dk relative rounded-full overflow-hidden flex-shrink-0"
                    :style="{ width: sz.px + 'px', height: sz.px + 'px' }">
                    <img :src="FAMILY[0].photo" :alt="FAMILY[0].name" class="w-full h-full object-cover" />
                  </div>
                  <span class="text-[10px]" :style="{ color: D.inkTertiary }">{{ sz.label }}</span>
                </div>
              </div>
            </div>

            <!-- Content types -->
            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">Content types — md, lavender</p>
              <div class="flex flex-wrap items-end gap-4">
                <div class="flex flex-col items-center gap-1.5">
                  <div class="av-dk w-10 h-10 rounded-full overflow-hidden flex-shrink-0">
                    <img :src="FAMILY[0].photo" alt="Maya" class="w-full h-full object-cover" />
                  </div>
                  <span class="text-[10px]" :style="{ color: D.inkTertiary }">Photo</span>
                </div>
                <div class="flex flex-col items-center gap-1.5">
                  <div class="av-dk w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0"
                    :style="{ background: D.accents.lavender.soft }">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                      :stroke="D.accents.lavender.bold" stroke-width="1.75"
                      stroke-linecap="round" stroke-linejoin="round">
                      <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                    </svg>
                  </div>
                  <span class="text-[10px]" :style="{ color: D.inkTertiary }">Preset icon</span>
                </div>
                <div class="flex flex-col items-center gap-1.5">
                  <div class="av-dk w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0"
                    :style="{ background: D.accents.lavender.soft }">
                    <span class="font-semibold text-[13px]" :style="{ color: D.accents.lavender.bold }">MQ</span>
                  </div>
                  <span class="text-[10px]" :style="{ color: D.inkTertiary }">Initials</span>
                </div>
              </div>
            </div>

            <!-- Family-color palette -->
            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">Family-color palette (optional)</p>
              <div class="flex flex-wrap items-end gap-4">
                <div v-for="m in FAMILY" :key="m.key" class="flex flex-col items-center gap-1.5">
                  <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0"
                    :style="{ background: D.accents[m.color].soft }">
                    <span class="font-semibold text-[13px]" :style="{ color: D.accents[m.color].bold }">{{ m.initials }}</span>
                  </div>
                  <span class="text-[10px]" :style="{ color: D.inkTertiary }">{{ m.name }}</span>
                </div>
              </div>
              <p class="mt-2 text-[11px] leading-snug" :style="{ color: D.inkTertiary }">
                These four illustrate the family-color system. Production uses the full 12-color palette from onboarding.
              </p>
            </div>

            <!-- Optional ring -->
            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">Optional family-color ring</p>
              <div class="flex flex-wrap items-end gap-4">
                <div v-for="m in FAMILY" :key="m.key" class="flex flex-col items-center gap-1.5">
                  <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0"
                    :style="{
                      background: D.accents[m.color].soft,
                      boxShadow: `0 0 0 2px ${D.surfaceApp}, 0 0 0 4px ${D.accents[m.color].bold}`,
                    }">
                    <span class="font-semibold text-[13px]" :style="{ color: D.accents[m.color].bold }">{{ m.initials }}</span>
                  </div>
                  <span class="text-[10px]" :style="{ color: D.inkTertiary }">{{ m.name }}</span>
                </div>
              </div>
              <p class="mt-2 text-[11px] leading-snug" :style="{ color: D.inkTertiary }">
                Optional treatment — use when a user is actively interacting or for the viewer's own avatar in the nav.
              </p>
            </div>

            <!-- Presence dot row -->
            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">Presence dot</p>
              <div class="flex flex-wrap items-end gap-4">
                <!-- Photo -->
                <div class="flex flex-col items-center gap-1.5">
                  <div class="av-dk relative w-10 h-10 flex-shrink-0">
                    <div class="w-10 h-10 rounded-full overflow-hidden">
                      <img :src="FAMILY[0].photo" alt="Maya" class="w-full h-full object-cover" />
                    </div>
                    <span class="presence-dot absolute rounded-full"
                      :style="{
                        width: '10px', height: '10px',
                        background: D.presenceOnline,
                        border: `2px solid ${D.presenceBorder}`,
                        bottom: '-1px', right: '-1px',
                      }"></span>
                  </div>
                  <span class="text-[10px]" :style="{ color: D.inkTertiary }">Photo</span>
                </div>
                <!-- Preset icon -->
                <div class="flex flex-col items-center gap-1.5">
                  <div class="av-dk relative w-10 h-10 flex-shrink-0">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center"
                      :style="{ background: D.accents.peach.soft }">
                      <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                        :stroke="D.accents.peach.bold" stroke-width="1.75"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/>
                      </svg>
                    </div>
                    <span class="presence-dot absolute rounded-full"
                      :style="{
                        width: '10px', height: '10px',
                        background: D.presenceOnline,
                        border: `2px solid ${D.presenceBorder}`,
                        bottom: '-1px', right: '-1px',
                      }"></span>
                  </div>
                  <span class="text-[10px]" :style="{ color: D.inkTertiary }">Preset icon</span>
                </div>
                <!-- Initials -->
                <div class="flex flex-col items-center gap-1.5">
                  <div class="av-dk relative w-10 h-10 flex-shrink-0">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center"
                      :style="{ background: D.accents.mint.soft }">
                      <span class="font-semibold text-[13px]" :style="{ color: D.accents.mint.bold }">AQ</span>
                    </div>
                    <span class="presence-dot absolute rounded-full"
                      :style="{
                        width: '10px', height: '10px',
                        background: D.presenceOnline,
                        border: `2px solid ${D.presenceBorder}`,
                        bottom: '-1px', right: '-1px',
                      }"></span>
                  </div>
                  <span class="text-[10px]" :style="{ color: D.inkTertiary }">Initials</span>
                </div>
              </div>
            </div>

            <!-- Stacked cluster -->
            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">Stacked cluster</p>
              <div class="flex items-center">
                <div
                  v-for="(m, i) in CLUSTER" :key="m.key"
                  class="av-dk w-10 h-10 rounded-full overflow-hidden flex-shrink-0"
                  :style="{
                    marginLeft: i === 0 ? '0' : '-10px',
                    zIndex: CLUSTER.length - i,
                    outline: `2px solid ${D.surfaceRaised}`,
                    position: 'relative',
                  }"
                >
                  <img :src="m.photo" :alt="m.name" class="w-full h-full object-cover" />
                </div>
                <span class="ml-3 text-[13px] font-medium" :style="{ color: D.inkPrimary }">5 family members — used in shared-task affordances</span>
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
         Additive SVG arc-ring overlay. Not the default.
         Used: in-progress achievements, weekly task %, badge arcs.
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="Arc" caption="Arc progress overlay — achievement rings and progress toward a goal">
        <div class="w-full space-y-10">

          <!-- ── LIGHT PANEL ───────────────────────────────────────── -->
          <div class="rounded-2xl border p-6 space-y-8" style="background:#FAF8F5; border-color:#E8E4DF">
            <p class="text-xs font-semibold uppercase tracking-widest" style="color:#9C9895">Light mode</p>

            <!-- Row 1: Progress scale 0% / 25% / 50% / 75% / 100% -->
            <div>
              <p class="text-xs mb-3" style="color:#9C9895">Progress scale — 0% / 25% / 50% / 75% / 100% (lavender arc)</p>
              <div class="flex flex-wrap items-end gap-6">
                <div v-for="pct in ARC_PCTS" :key="pct" class="flex flex-col items-center gap-1.5">
                  <!-- Outer wrapper: 40px + padding for arc. Arc is absolutely positioned SVG overlay. -->
                  <div class="arc-avatar-lt relative flex-shrink-0" style="width:48px; height:48px">
                    <!-- Photo inset: 6px from edge to leave room for stroke -->
                    <div class="absolute rounded-full overflow-hidden"
                      style="inset:4px">
                      <img :src="FAMILY[0].photo" alt="Maya" class="w-full h-full object-cover" />
                    </div>
                    <!-- SVG arc overlay -->
                    <svg class="absolute inset-0 w-full h-full" style="transform: rotate(-90deg)" viewBox="0 0 100 100">
                      <!-- Track ring (subtle) -->
                      <circle cx="50" cy="50" r="46" fill="none"
                        stroke="rgba(0,0,0,0.08)" stroke-width="4" />
                      <!-- Progress arc — 100% becomes solid ring (dashoffset≈0, linecap=butt removes gap at 100%) -->
                      <circle
                        cx="50" cy="50" r="46"
                        fill="none"
                        stroke="#6856B2"
                        stroke-width="4"
                        :stroke-dasharray="ARC_CIRC"
                        :stroke-dashoffset="arcOffset(pct)"
                        :stroke-linecap="pct === 1.0 ? 'butt' : 'round'"
                        style="transition: stroke-dashoffset 500ms ease-out"
                      />
                    </svg>
                  </div>
                  <span class="text-[10px]" style="color:#9C9895">{{ Math.round(pct * 100) }}%</span>
                </div>
              </div>
              <p class="mt-2 text-[11px] leading-snug" style="color:#9C9895">
                At 100% the arc becomes a solid ring (stroke-linecap: butt, no visible gap) — hints at the "earned" state.
              </p>
            </div>

            <!-- Row 2: Arc + family color (65% fill, all 4 colors) -->
            <div>
              <p class="text-xs mb-3" style="color:#9C9895">Arc in family color — 65% fill, initials avatar</p>
              <div class="flex flex-wrap items-end gap-6">
                <div v-for="m in FAMILY" :key="m.key" class="flex flex-col items-center gap-1.5">
                  <div class="arc-avatar-lt relative flex-shrink-0" style="width:48px; height:48px">
                    <div class="absolute rounded-full flex items-center justify-center"
                      style="inset:4px"
                      :style="{ background: L.accents[m.color].soft }">
                      <span class="font-semibold text-[13px]" :style="{ color: L.accents[m.color].bold }">{{ m.initials }}</span>
                    </div>
                    <svg class="absolute inset-0 w-full h-full" style="transform: rotate(-90deg)" viewBox="0 0 100 100">
                      <circle cx="50" cy="50" r="46" fill="none" stroke="rgba(0,0,0,0.07)" stroke-width="4" />
                      <circle cx="50" cy="50" r="46" fill="none"
                        :stroke="L.accents[m.color].bold"
                        stroke-width="4"
                        :stroke-dasharray="ARC_CIRC"
                        :stroke-dashoffset="arcOffset(0.65)"
                        stroke-linecap="round"
                        style="transition: stroke-dashoffset 500ms ease-out"
                      />
                    </svg>
                  </div>
                  <span class="text-[10px]" style="color:#9C9895">{{ m.name }}</span>
                </div>
              </div>
              <p class="mt-2 text-[11px] leading-snug" style="color:#9C9895">
                Arc can wear the family color for personal progress visualizations.
              </p>
            </div>

            <!-- Row 3: Sizes (sm / md / lg / xl at 60%) -->
            <div>
              <p class="text-xs mb-3" style="color:#9C9895">Sizes — sm / md / lg / xl at 60% fill (arc stroke scales)</p>
              <div class="flex flex-wrap items-end gap-6">
                <div v-for="sz in ARC_SIZES" :key="sz.key" class="flex flex-col items-center gap-1.5">
                  <!-- Outer wrapper: sizePx + 8px padding for stroke -->
                  <div class="arc-avatar-lt relative flex-shrink-0"
                    :style="{ width: (sz.px + 8) + 'px', height: (sz.px + 8) + 'px' }">
                    <div class="absolute rounded-full overflow-hidden" style="inset:4px">
                      <img :src="FAMILY[0].photo" :alt="FAMILY[0].name" class="w-full h-full object-cover" />
                    </div>
                    <svg class="absolute inset-0 w-full h-full" style="transform: rotate(-90deg)" viewBox="0 0 100 100">
                      <circle cx="50" cy="50" r="46" fill="none" stroke="rgba(0,0,0,0.07)"
                        :stroke-width="sz.arcStroke * (100 / sz.px)" />
                      <circle cx="50" cy="50" r="46" fill="none"
                        stroke="#6856B2"
                        :stroke-width="sz.arcStroke * (100 / sz.px)"
                        :stroke-dasharray="ARC_CIRC"
                        :stroke-dashoffset="arcOffset(0.6)"
                        stroke-linecap="round"
                        style="transition: stroke-dashoffset 500ms ease-out"
                      />
                    </svg>
                  </div>
                  <span class="text-[10px]" style="color:#9C9895">{{ sz.label }}</span>
                </div>
              </div>
            </div>

            <!-- Row 4: Achievement context demo (earned / in-progress / not-started) -->
            <div>
              <p class="text-xs mb-3" style="color:#9C9895">Achievement context — not started / in-progress / earned</p>
              <div class="flex flex-wrap items-end gap-8">
                <!-- 0% — not started (no arc rendered, avatar grayscale) -->
                <div class="flex flex-col items-center gap-1.5">
                  <div class="arc-avatar-lt relative flex-shrink-0" style="width:48px; height:48px">
                    <div class="absolute rounded-full overflow-hidden" style="inset:4px; filter: grayscale(1) opacity(0.4)">
                      <img :src="FAMILY[3].photo" alt="Greg" class="w-full h-full object-cover" />
                    </div>
                    <!-- no arc SVG — 0% = not started -->
                  </div>
                  <span class="text-[10px]" style="color:#9C9895">0% — not started</span>
                </div>
                <!-- 60% — in-progress arc -->
                <div class="flex flex-col items-center gap-1.5">
                  <div class="arc-avatar-lt relative flex-shrink-0" style="width:48px; height:48px">
                    <div class="absolute rounded-full overflow-hidden" style="inset:4px">
                      <img :src="FAMILY[0].photo" alt="Maya" class="w-full h-full object-cover" />
                    </div>
                    <svg class="absolute inset-0 w-full h-full" style="transform: rotate(-90deg)" viewBox="0 0 100 100">
                      <circle cx="50" cy="50" r="46" fill="none" stroke="rgba(0,0,0,0.07)" stroke-width="4" />
                      <circle cx="50" cy="50" r="46" fill="none"
                        stroke="#6856B2" stroke-width="4"
                        :stroke-dasharray="ARC_CIRC"
                        :stroke-dashoffset="arcOffset(0.6)"
                        stroke-linecap="round"
                        style="transition: stroke-dashoffset 500ms ease-out"
                      />
                    </svg>
                  </div>
                  <span class="text-[10px]" style="color:#9C9895">60% — in-progress</span>
                </div>
                <!-- 100% — earned, solid ring -->
                <div class="flex flex-col items-center gap-1.5">
                  <div class="arc-avatar-lt relative flex-shrink-0" style="width:48px; height:48px">
                    <div class="absolute rounded-full overflow-hidden" style="inset:4px">
                      <img :src="FAMILY[1].photo" alt="Emma" class="w-full h-full object-cover" />
                    </div>
                    <svg class="absolute inset-0 w-full h-full" style="transform: rotate(-90deg)" viewBox="0 0 100 100">
                      <!-- At 100%, no track visible — just the solid full ring -->
                      <circle cx="50" cy="50" r="46" fill="none"
                        stroke="#6856B2" stroke-width="4"
                        :stroke-dasharray="ARC_CIRC"
                        :stroke-dashoffset="arcOffset(1.0)"
                        stroke-linecap="butt"
                        style="transition: stroke-dashoffset 500ms ease-out"
                      />
                    </svg>
                  </div>
                  <span class="text-[10px]" style="color:#9C9895">100% — earned</span>
                </div>
              </div>
              <p class="mt-2 text-[11px] leading-snug" style="color:#9C9895">
                Per the achievement pattern: 0% = not started (no arc), mid-range = in-progress arc, 100% = solid ring = earned.
              </p>
            </div>
          </div><!-- /light arc -->

          <!-- ── DARK PANEL ─────────────────────────────────────────── -->
          <div class="rounded-2xl border p-6 space-y-8" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

            <!-- Progress scale dark -->
            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">Progress scale — 0% / 25% / 50% / 75% / 100%</p>
              <div class="flex flex-wrap items-end gap-6">
                <div v-for="pct in ARC_PCTS" :key="pct" class="flex flex-col items-center gap-1.5">
                  <div class="arc-avatar-dk relative flex-shrink-0" style="width:48px; height:48px">
                    <div class="absolute rounded-full overflow-hidden" style="inset:4px">
                      <img :src="FAMILY[0].photo" :alt="FAMILY[0].name" class="w-full h-full object-cover" />
                    </div>
                    <svg class="absolute inset-0 w-full h-full" style="transform: rotate(-90deg)" viewBox="0 0 100 100">
                      <circle cx="50" cy="50" r="46" fill="none"
                        stroke="rgba(255,255,255,0.08)" stroke-width="4" />
                      <circle cx="50" cy="50" r="46" fill="none"
                        :stroke="D.accents.lavender.bold"
                        stroke-width="4"
                        :stroke-dasharray="ARC_CIRC"
                        :stroke-dashoffset="arcOffset(pct)"
                        :stroke-linecap="pct === 1.0 ? 'butt' : 'round'"
                        style="transition: stroke-dashoffset 500ms ease-out"
                      />
                    </svg>
                  </div>
                  <span class="text-[10px]" :style="{ color: D.inkTertiary }">{{ Math.round(pct * 100) }}%</span>
                </div>
              </div>
            </div>

            <!-- Arc + family color dark -->
            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">Arc in family color — 65% fill</p>
              <div class="flex flex-wrap items-end gap-6">
                <div v-for="m in FAMILY" :key="m.key" class="flex flex-col items-center gap-1.5">
                  <div class="arc-avatar-dk relative flex-shrink-0" style="width:48px; height:48px">
                    <div class="absolute rounded-full flex items-center justify-center"
                      style="inset:4px"
                      :style="{ background: D.accents[m.color].soft }">
                      <span class="font-semibold text-[13px]" :style="{ color: D.accents[m.color].bold }">{{ m.initials }}</span>
                    </div>
                    <svg class="absolute inset-0 w-full h-full" style="transform: rotate(-90deg)" viewBox="0 0 100 100">
                      <circle cx="50" cy="50" r="46" fill="none" stroke="rgba(255,255,255,0.06)" stroke-width="4" />
                      <circle cx="50" cy="50" r="46" fill="none"
                        :stroke="D.accents[m.color].bold"
                        stroke-width="4"
                        :stroke-dasharray="ARC_CIRC"
                        :stroke-dashoffset="arcOffset(0.65)"
                        stroke-linecap="round"
                        style="transition: stroke-dashoffset 500ms ease-out"
                      />
                    </svg>
                  </div>
                  <span class="text-[10px]" :style="{ color: D.inkTertiary }">{{ m.name }}</span>
                </div>
              </div>
              <p class="mt-2 text-[11px] leading-snug" :style="{ color: D.inkTertiary }">
                Arc can wear the family color for personal progress visualizations.
              </p>
            </div>

            <!-- Sizes dark -->
            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">Sizes — sm / md / lg / xl at 60%</p>
              <div class="flex flex-wrap items-end gap-6">
                <div v-for="sz in ARC_SIZES" :key="sz.key" class="flex flex-col items-center gap-1.5">
                  <div class="arc-avatar-dk relative flex-shrink-0"
                    :style="{ width: (sz.px + 8) + 'px', height: (sz.px + 8) + 'px' }">
                    <div class="absolute rounded-full overflow-hidden" style="inset:4px">
                      <img :src="FAMILY[0].photo" :alt="FAMILY[0].name" class="w-full h-full object-cover" />
                    </div>
                    <svg class="absolute inset-0 w-full h-full" style="transform: rotate(-90deg)" viewBox="0 0 100 100">
                      <circle cx="50" cy="50" r="46" fill="none"
                        stroke="rgba(255,255,255,0.07)"
                        :stroke-width="sz.arcStroke * (100 / sz.px)" />
                      <circle cx="50" cy="50" r="46" fill="none"
                        :stroke="D.accents.lavender.bold"
                        :stroke-width="sz.arcStroke * (100 / sz.px)"
                        :stroke-dasharray="ARC_CIRC"
                        :stroke-dashoffset="arcOffset(0.6)"
                        stroke-linecap="round"
                        style="transition: stroke-dashoffset 500ms ease-out"
                      />
                    </svg>
                  </div>
                  <span class="text-[10px]" :style="{ color: D.inkTertiary }">{{ sz.label }}</span>
                </div>
              </div>
            </div>

            <!-- Achievement context demo dark -->
            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">Achievement context — not started / in-progress / earned</p>
              <div class="flex flex-wrap items-end gap-8">
                <!-- 0% not started -->
                <div class="flex flex-col items-center gap-1.5">
                  <div class="arc-avatar-dk relative flex-shrink-0" style="width:48px; height:48px">
                    <div class="absolute rounded-full overflow-hidden" style="inset:4px; filter: grayscale(1) opacity(0.3)">
                      <img :src="FAMILY[3].photo" alt="Greg" class="w-full h-full object-cover" />
                    </div>
                  </div>
                  <span class="text-[10px]" :style="{ color: D.inkTertiary }">0% — not started</span>
                </div>
                <!-- 60% in-progress -->
                <div class="flex flex-col items-center gap-1.5">
                  <div class="arc-avatar-dk relative flex-shrink-0" style="width:48px; height:48px">
                    <div class="absolute rounded-full overflow-hidden" style="inset:4px">
                      <img :src="FAMILY[0].photo" alt="Maya" class="w-full h-full object-cover" />
                    </div>
                    <svg class="absolute inset-0 w-full h-full" style="transform: rotate(-90deg)" viewBox="0 0 100 100">
                      <circle cx="50" cy="50" r="46" fill="none" stroke="rgba(255,255,255,0.07)" stroke-width="4" />
                      <circle cx="50" cy="50" r="46" fill="none"
                        :stroke="D.accents.lavender.bold" stroke-width="4"
                        :stroke-dasharray="ARC_CIRC"
                        :stroke-dashoffset="arcOffset(0.6)"
                        stroke-linecap="round"
                        style="transition: stroke-dashoffset 500ms ease-out"
                      />
                    </svg>
                  </div>
                  <span class="text-[10px]" :style="{ color: D.inkTertiary }">60% — in-progress</span>
                </div>
                <!-- 100% earned -->
                <div class="flex flex-col items-center gap-1.5">
                  <div class="arc-avatar-dk relative flex-shrink-0" style="width:48px; height:48px">
                    <div class="absolute rounded-full overflow-hidden" style="inset:4px">
                      <img :src="FAMILY[1].photo" alt="Emma" class="w-full h-full object-cover" />
                    </div>
                    <svg class="absolute inset-0 w-full h-full" style="transform: rotate(-90deg)" viewBox="0 0 100 100">
                      <circle cx="50" cy="50" r="46" fill="none"
                        :stroke="D.accents.lavender.bold" stroke-width="4"
                        :stroke-dasharray="ARC_CIRC"
                        :stroke-dashoffset="arcOffset(1.0)"
                        stroke-linecap="butt"
                        style="transition: stroke-dashoffset 500ms ease-out"
                      />
                    </svg>
                  </div>
                  <span class="text-[10px]" :style="{ color: D.inkTertiary }">100% — earned</span>
                </div>
              </div>
              <p class="mt-2 text-[11px] leading-snug" :style="{ color: D.inkTertiary }">
                Per the achievement pattern: 0% = not started (no arc), mid-range = in-progress arc, 100% = solid ring = earned.
              </p>
            </div>
          </div><!-- /dark arc -->
        </div>
      </VariantFrame>
    </section>

    <!-- ══════════════════════════════════════════════════════════════
         USAGE GUIDE — When to use which
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-8">
      <div class="rounded-2xl border p-6 space-y-4" style="background:#FFFFFF; border-color:#E8E4DF">
        <h2 class="text-[17px] font-semibold" style="color:#1C1C1E">When to use which</h2>
        <ul class="space-y-3 text-[14px]" style="color:#6B6966">
          <li>
            <strong style="color:#1C1C1E">Avatar (default):</strong>
            Any time a person is represented — task rows, feeds, calendar events, assignment pickers. The workhorse. No ring at rest; the photo or initials carry the identity.
          </li>
          <li>
            <strong style="color:#1C1C1E">Arc overlay:</strong>
            Only where progress communicates state — in-progress achievements, weekly task completion %, badge arcs. Additive; the avatar itself remains the default. The arc is a second layer applied situationally.
          </li>
        </ul>
      </div>
    </section>

  </ComponentPage>
</template>

<style scoped>
/*
  ─────────────────────────────────────────────────────────────────
  AVATAR — Light panel hover
  Subtle scale on the circle
  ─────────────────────────────────────────────────────────────────
*/
.av-lt {
  transition: transform 200ms;
}
.av-lt:hover {
  transform: scale(1.04);
}

/*
  ─────────────────────────────────────────────────────────────────
  AVATAR — Dark panel hover
  Scale + brightness lift
  ─────────────────────────────────────────────────────────────────
*/
.av-dk {
  transition: transform 200ms, filter 200ms;
}
.av-dk:hover {
  transform: scale(1.04);
  filter: brightness(1.08);
}

/*
  ─────────────────────────────────────────────────────────────────
  ARC AVATAR — Light panel
  The outer wrapper is the arc container. Inner photo is inset.
  ─────────────────────────────────────────────────────────────────
*/
.arc-avatar-lt {
  transition: transform 200ms;
}
.arc-avatar-lt:hover {
  transform: scale(1.04);
}

/*
  ─────────────────────────────────────────────────────────────────
  ARC AVATAR — Dark panel
  ─────────────────────────────────────────────────────────────────
*/
.arc-avatar-dk {
  transition: transform 200ms, filter 200ms;
}
.arc-avatar-dk:hover {
  transform: scale(1.04);
  filter: brightness(1.08);
}

/*
  ─────────────────────────────────────────────────────────────────
  PRESENCE DOT — always in front
  ─────────────────────────────────────────────────────────────────
*/
.presence-dot {
  display: block;
  z-index: 10;
}
</style>
