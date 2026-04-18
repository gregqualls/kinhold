<script setup>
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import { ArrowPathIcon, BellIcon, EllipsisHorizontalIcon } from '@heroicons/vue/24/outline'

// ── Dark-mode hex constants (from tokens.css html.dark section) ─────────────
// Used in inline styles on dark panels so both panels render correctly
// when the root theme is LIGHT. Light panel uses token classes; dark panel
// uses these hardcoded values. This asymmetry is intentional.
const D = {
  surfaceApp:    '#141311',
  surfaceRaised: '#1C1B19',
  inkPrimary:    '#F0EDE9',
  inkSecondary:  '#A09C97',
  inkTertiary:   '#6E6B67',
  borderSubtle:  '#2C2A27',
  borderStrong:  '#403E3A',
  statusFailed:  '#E67070',
  // Focus ring accent
  focusRing:     '#B6A8E6',
}

// ── Light-mode hex constants (resolved token values) ────────────────────────
const L = {
  surfaceApp:    '#FAF8F5',
  surfaceRaised: '#FFFFFF',
  surfaceSunken: '#F5F2EE',
  inkPrimary:    '#1C1C1E',
  inkSecondary:  '#6B6966',
  inkInverse:    '#FAF8F5',
  borderSubtle:  '#E8E4DF',
  statusFailed:  '#BA4A4A',
  focusRing:     '#6856B2',
  brandGold:     '#C4975A',
}
</script>

<template>
  <ComponentPage
    title="1.1 Button"
    description="Pill-shaped interactive controls. Lifted / tactile treatment: subtle gradient, resting shadow, 3px lift on hover, pressed-in feel on active. Covers primary / secondary / ghost / danger / accent × sm/md/lg × default/hover/active/focus/disabled/loading × icon-only. Promoted to <code>KinButton.vue</code> once confirmed."
    status="chosen"
  >

    <!-- ══════════════════════════════════════════════════════════════
         LIFTED FILLED PILL (the chosen treatment)
         Stripe / Apple / Lovable feel — tactile, invites touch.
         shadow-resting at rest → shadow-hover + 3px lift on hover.
         Primary/Danger use a subtle vertical gradient for depth.
         Active: inset shadow + 3px press-down for a pushed-into-the-page feel.
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-16">
      <VariantFrame label="✓" caption="Lifted filled pill — subtle gradient + shadow lift on hover">

        <div class="w-full space-y-10">

          <!-- ── LIGHT PANEL ─────────────────────────────────────────── -->
          <div
            class="rounded-2xl border p-6 space-y-8"
            style="background:#FAF8F5; border-color:#E8E4DF"
          >
            <p class="text-xs font-semibold uppercase tracking-widest text-ink-tertiary">Light mode</p>

            <!-- Semantic colors -->
            <div>
              <p class="text-caption text-ink-tertiary mb-3">Semantic colors — md size</p>
              <div class="flex flex-wrap gap-3 items-center">
                <!-- Primary: vertical gradient dark-to-darker, shadow-resting → shadow-hover -->
                <button type="button"
                  class="inline-flex items-center gap-1.5 h-10 px-5 rounded-pill text-ink-inverse text-body-sm font-medium
                         shadow-resting hover:shadow-hover active:shadow-resting
                         transition-all duration-quick ease-out-soft
                         hover:-translate-y-[3px] active:translate-y-[3px] active:shadow-[inset_0_2px_3px_rgba(0,0,0,0.5)]
                         focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-accent-lavender-bold"
                  style="background-image: linear-gradient(180deg, #313130 0%, #1C1C1E 100%);">
                  Primary
                </button>
                <!-- Secondary: outline + surface-raised + shadow -->
                <button type="button"
                  class="inline-flex items-center gap-1.5 h-10 px-5 rounded-pill bg-surface-raised text-ink-primary text-body-sm font-medium
                         border border-border-subtle
                         shadow-resting hover:shadow-hover active:shadow-resting
                         transition-all duration-quick ease-out-soft
                         hover:-translate-y-[3px] active:translate-y-[3px] active:shadow-[inset_0_2px_3px_rgba(0,0,0,0.5)]
                         focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-accent-lavender-bold">
                  Secondary
                </button>
                <!-- Ghost -->
                <button type="button"
                  class="inline-flex items-center gap-1.5 h-10 px-5 rounded-pill bg-transparent text-ink-primary text-body-sm font-medium
                         transition-all duration-quick ease-out-soft
                         hover:bg-surface-sunken hover:shadow-resting active:brightness-95
                         focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-accent-lavender-bold">
                  Ghost
                </button>
                <!-- Danger: subtle gradient rose -->
                <button type="button"
                  class="inline-flex items-center gap-1.5 h-10 px-5 rounded-pill text-white text-body-sm font-medium
                         shadow-resting hover:shadow-hover active:shadow-resting
                         transition-all duration-quick ease-out-soft
                         hover:-translate-y-[3px] active:translate-y-[3px] active:shadow-[inset_0_2px_3px_rgba(0,0,0,0.5)]
                         focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-accent-lavender-bold"
                  style="background-image: linear-gradient(180deg, #C96060 0%, #BA4A4A 100%);">
                  Danger
                </button>
                <!-- Accent: brand-gold gradient -->
                <button type="button"
                  class="inline-flex items-center gap-1.5 h-10 px-5 rounded-pill text-ink-primary text-body-sm font-medium
                         shadow-resting hover:shadow-hover active:shadow-resting
                         transition-all duration-quick ease-out-soft
                         hover:-translate-y-[3px] active:translate-y-[3px] active:shadow-[inset_0_2px_3px_rgba(0,0,0,0.5)]
                         focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-accent-lavender-bold"
                  style="background-image: linear-gradient(180deg, #D4A96A 0%, #C4975A 100%);">
                  Accent
                </button>
              </div>
            </div>

            <!-- Sizes -->
            <div>
              <p class="text-caption text-ink-tertiary mb-3">Size scale — primary</p>
              <div class="flex flex-wrap gap-3 items-center">
                <button type="button"
                  class="inline-flex items-center h-8 px-3 rounded-pill text-ink-inverse text-body-sm font-medium
                         shadow-resting hover:shadow-hover active:shadow-resting transition-all duration-quick ease-out-soft hover:-translate-y-[3px] active:translate-y-[3px] active:shadow-[inset_0_2px_3px_rgba(0,0,0,0.5)]"
                  style="background-image: linear-gradient(180deg, #313130 0%, #1C1C1E 100%);">Small</button>
                <button type="button"
                  class="inline-flex items-center h-10 px-5 rounded-pill text-ink-inverse text-body font-medium
                         shadow-resting hover:shadow-hover active:shadow-resting transition-all duration-quick ease-out-soft hover:-translate-y-[3px] active:translate-y-[3px] active:shadow-[inset_0_2px_3px_rgba(0,0,0,0.5)]"
                  style="background-image: linear-gradient(180deg, #313130 0%, #1C1C1E 100%);">Medium</button>
                <button type="button"
                  class="inline-flex items-center h-12 px-7 rounded-pill text-ink-inverse text-body font-semibold
                         shadow-resting hover:shadow-hover active:shadow-resting transition-all duration-quick ease-out-soft hover:-translate-y-[3px] active:translate-y-[3px] active:shadow-[inset_0_2px_3px_rgba(0,0,0,0.5)]"
                  style="background-image: linear-gradient(180deg, #313130 0%, #1C1C1E 100%);">Large</button>
              </div>
            </div>

            <!-- States -->
            <div>
              <p class="text-caption text-ink-tertiary mb-1">States — primary md</p>
              <p class="text-caption text-ink-tertiary mb-3 italic">Focus = the ring shown when a keyboard user tabs to the button. Accessibility requirement — don't remove it.</p>
              <div class="flex flex-wrap gap-3 items-center">
                <div class="flex flex-col items-center gap-1.5">
                  <button type="button" class="inline-flex items-center h-10 px-5 rounded-pill text-ink-inverse text-body-sm font-medium shadow-resting" style="background-image:linear-gradient(180deg,#313130 0%,#1C1C1E 100%)">Default</button>
                  <span class="text-caption text-ink-tertiary">default</span>
                </div>
                <div class="flex flex-col items-center gap-1.5">
                  <button type="button" class="inline-flex items-center h-10 px-5 rounded-pill text-ink-inverse text-body-sm font-medium shadow-elevated" style="background-image:linear-gradient(180deg,#4A4A4F 0%,#2A2A2E 100%); transform:translateY(-3px)">Hover</button>
                  <span class="text-caption text-ink-tertiary">hover</span>
                </div>
                <div class="flex flex-col items-center gap-1.5">
                  <button type="button" class="inline-flex items-center h-10 px-5 rounded-pill text-ink-inverse text-body-sm font-medium" style="background:#000000; transform:translateY(3px); box-shadow:inset 0 2px 3px rgba(0,0,0,0.55)">Active</button>
                  <span class="text-caption text-ink-tertiary">active</span>
                </div>
                <div class="flex flex-col items-center gap-1.5">
                  <button type="button" class="inline-flex items-center h-10 px-5 rounded-pill text-ink-inverse text-body-sm font-medium shadow-resting outline-none ring-2 ring-offset-2" style="background-image:linear-gradient(180deg,#313130 0%,#1C1C1E 100%);--tw-ring-color:#6856B2">Focus</button>
                  <span class="text-caption text-ink-tertiary">focus</span>
                </div>
                <div class="flex flex-col items-center gap-1.5">
                  <button type="button" disabled class="inline-flex items-center h-10 px-5 rounded-pill text-ink-inverse text-body-sm font-medium opacity-40 cursor-not-allowed" style="background-image:linear-gradient(180deg,#313130 0%,#1C1C1E 100%)">Disabled</button>
                  <span class="text-caption text-ink-tertiary">disabled</span>
                </div>
                <div class="flex flex-col items-center gap-1.5">
                  <button type="button" disabled class="inline-flex items-center gap-2 h-10 px-5 rounded-pill text-ink-inverse text-body-sm font-medium shadow-resting cursor-wait" style="background-image:linear-gradient(180deg,#313130 0%,#1C1C1E 100%)">
                    <ArrowPathIcon class="w-4 h-4 animate-spin flex-shrink-0" />Loading…
                  </button>
                  <span class="text-caption text-ink-tertiary">loading</span>
                </div>
              </div>
            </div>

            <!-- Icon-only -->
            <div>
              <p class="text-caption text-ink-tertiary mb-3">Icon-only — sm / md / lg</p>
              <div class="flex flex-wrap gap-3 items-center">
                <button type="button" aria-label="Bell sm"
                  class="inline-flex items-center justify-center w-8 h-8 rounded-pill text-ink-inverse shadow-resting hover:shadow-hover hover:-translate-y-[3px] active:translate-y-[3px] active:shadow-[inset_0_2px_3px_rgba(0,0,0,0.5)] active:shadow-resting transition-all duration-quick ease-out-soft focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-accent-lavender-bold"
                  style="background-image:linear-gradient(180deg,#313130 0%,#1C1C1E 100%)">
                  <BellIcon class="w-4 h-4" />
                </button>
                <button type="button" aria-label="Ellipsis md"
                  class="inline-flex items-center justify-center w-10 h-10 rounded-pill text-ink-inverse shadow-resting hover:shadow-hover hover:-translate-y-[3px] active:translate-y-[3px] active:shadow-[inset_0_2px_3px_rgba(0,0,0,0.5)] active:shadow-resting transition-all duration-quick ease-out-soft focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-accent-lavender-bold"
                  style="background-image:linear-gradient(180deg,#313130 0%,#1C1C1E 100%)">
                  <EllipsisHorizontalIcon class="w-5 h-5" />
                </button>
                <button type="button" aria-label="Ellipsis lg"
                  class="inline-flex items-center justify-center w-12 h-12 rounded-pill text-ink-inverse shadow-resting hover:shadow-hover hover:-translate-y-[3px] active:translate-y-[3px] active:shadow-[inset_0_2px_3px_rgba(0,0,0,0.5)] active:shadow-resting transition-all duration-quick ease-out-soft focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-accent-lavender-bold"
                  style="background-image:linear-gradient(180deg,#313130 0%,#1C1C1E 100%)">
                  <EllipsisHorizontalIcon class="w-5 h-5" />
                </button>
                <button type="button" aria-label="Bell secondary"
                  class="inline-flex items-center justify-center w-10 h-10 rounded-pill bg-surface-raised text-ink-primary border border-border-subtle shadow-resting hover:shadow-hover hover:-translate-y-[3px] active:translate-y-[3px] active:shadow-[inset_0_2px_3px_rgba(0,0,0,0.5)] active:shadow-resting transition-all duration-quick ease-out-soft focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-accent-lavender-bold">
                  <BellIcon class="w-5 h-5" />
                </button>
                <!-- Ghost icon-only -->
                <button type="button" aria-label="Bell ghost"
                  class="inline-flex items-center justify-center w-10 h-10 rounded-pill bg-transparent text-ink-primary transition-all duration-quick ease-out-soft hover:bg-surface-sunken active:brightness-95 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-accent-lavender-bold">
                  <BellIcon class="w-5 h-5" />
                </button>
              </div>
            </div>

          </div><!-- /light panel B -->

          <!-- ── DARK PANEL B ────────────────────────────────────────── -->
          <div
            class="rounded-2xl border p-6 space-y-8"
            :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }"
          >
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

            <!-- Semantic dark -->
            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">Semantic colors — md size</p>
              <div class="flex flex-wrap gap-3 items-center">
                <button type="button" class="vb-dark-btn vb-dark-btn--primary">Primary</button>
                <button type="button" class="vb-dark-btn vb-dark-btn--secondary">Secondary</button>
                <button type="button" class="vb-dark-btn vb-dark-btn--ghost">Ghost</button>
                <button type="button" class="vb-dark-btn vb-dark-btn--danger">Danger</button>
                <button type="button" class="vb-dark-btn vb-dark-btn--accent">Accent</button>
              </div>
            </div>

            <!-- Sizes dark B -->
            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">Size scale — primary</p>
              <div class="flex flex-wrap gap-3 items-center">
                <button type="button" class="vb-dark-btn vb-dark-btn--primary vb-dark-btn--sm">Small</button>
                <button type="button" class="vb-dark-btn vb-dark-btn--primary vb-dark-btn--md">Medium</button>
                <button type="button" class="vb-dark-btn vb-dark-btn--primary vb-dark-btn--lg">Large</button>
              </div>
            </div>

            <!-- States dark B -->
            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">States — primary md</p>
              <div class="flex flex-wrap gap-3 items-center">
                <div class="flex flex-col items-center gap-1.5">
                  <button type="button" style="display:inline-flex;align-items:center;height:40px;padding:0 20px;border-radius:9999px;background-image:linear-gradient(180deg,#F5F2EE 0%,#E8E4DF 100%);color:#1C1C1E;font-size:13px;font-weight:500;border:none;cursor:pointer;box-shadow:0 1px 2px rgba(0,0,0,0.30),0 2px 6px rgba(0,0,0,0.25);">Default</button>
                  <span style="font-size:11px;color:#6E6B67;">default</span>
                </div>
                <div class="flex flex-col items-center gap-1.5">
                  <button type="button" style="display:inline-flex;align-items:center;height:40px;padding:0 20px;border-radius:9999px;background-image:linear-gradient(180deg,#FFFFFF 0%,#F0EDE9 100%);color:#1C1C1E;font-size:13px;font-weight:500;border:none;cursor:pointer;box-shadow:0 4px 8px rgba(0,0,0,0.45),0 16px 32px rgba(0,0,0,0.40);transform:translateY(-3px);">Hover</button>
                  <span style="font-size:11px;color:#6E6B67;">hover</span>
                </div>
                <div class="flex flex-col items-center gap-1.5">
                  <button type="button" style="display:inline-flex;align-items:center;height:40px;padding:0 20px;border-radius:9999px;background:#9E9992;color:#1C1C1E;font-size:13px;font-weight:500;border:none;cursor:pointer;box-shadow:inset 0 2px 3px rgba(0,0,0,0.45);transform:translateY(3px);">Active</button>
                  <span style="font-size:11px;color:#6E6B67;">active</span>
                </div>
                <div class="flex flex-col items-center gap-1.5">
                  <button type="button" style="display:inline-flex;align-items:center;height:40px;padding:0 20px;border-radius:9999px;background-image:linear-gradient(180deg,#F5F2EE 0%,#E8E4DF 100%);color:#1C1C1E;font-size:13px;font-weight:500;border:none;cursor:pointer;box-shadow:0 0 0 2px #141311,0 0 0 4px #B6A8E6;">Focus</button>
                  <span style="font-size:11px;color:#6E6B67;">focus</span>
                </div>
                <div class="flex flex-col items-center gap-1.5">
                  <button type="button" disabled style="display:inline-flex;align-items:center;height:40px;padding:0 20px;border-radius:9999px;background-image:linear-gradient(180deg,#F5F2EE 0%,#E8E4DF 100%);color:#1C1C1E;font-size:13px;font-weight:500;border:none;cursor:not-allowed;opacity:0.35;">Disabled</button>
                  <span style="font-size:11px;color:#6E6B67;">disabled</span>
                </div>
                <div class="flex flex-col items-center gap-1.5">
                  <button type="button" disabled style="display:inline-flex;align-items:center;gap:8px;height:40px;padding:0 20px;border-radius:9999px;background-image:linear-gradient(180deg,#F5F2EE 0%,#E8E4DF 100%);color:#1C1C1E;font-size:13px;font-weight:500;border:none;cursor:wait;box-shadow:0 1px 2px rgba(0,0,0,0.30),0 2px 6px rgba(0,0,0,0.25);">
                    <ArrowPathIcon class="w-4 h-4 animate-spin flex-shrink-0" />Loading…
                  </button>
                  <span style="font-size:11px;color:#6E6B67;">loading</span>
                </div>
              </div>
            </div>

            <!-- Icon-only dark B -->
            <div>
              <p class="text-xs mb-3" :style="{ color: D.inkTertiary }">Icon-only — sm / md / lg</p>
              <div class="flex flex-wrap gap-3 items-center">
                <button type="button" aria-label="Bell sm dark B" class="vb-dark-btn vb-dark-btn--primary vb-dark-btn--icon-sm" style="justify-content:center">
                  <BellIcon class="w-4 h-4" />
                </button>
                <button type="button" aria-label="Ellipsis md dark B" class="vb-dark-btn vb-dark-btn--primary vb-dark-btn--icon-md" style="justify-content:center">
                  <EllipsisHorizontalIcon class="w-5 h-5" />
                </button>
                <button type="button" aria-label="Ellipsis lg dark B" class="vb-dark-btn vb-dark-btn--primary vb-dark-btn--icon-lg" style="justify-content:center">
                  <EllipsisHorizontalIcon class="w-5 h-5" />
                </button>
                <button type="button" aria-label="Bell secondary dark B" class="vb-dark-btn vb-dark-btn--secondary vb-dark-btn--icon-md" style="justify-content:center">
                  <BellIcon class="w-5 h-5" />
                </button>
                <button type="button" aria-label="Bell ghost dark B" class="vb-dark-btn vb-dark-btn--ghost vb-dark-btn--icon-md" style="justify-content:center">
                  <BellIcon class="w-5 h-5" />
                </button>
              </div>
            </div>

          </div><!-- /dark panel B -->

        </div>
      </VariantFrame>

      <p class="mt-3 text-body-sm text-ink-secondary px-1">
        The lift animation gives every press a physical reward — the button meets your finger. Works for both hero CTAs and quieter secondary actions; the gradient + resting shadow reads as tactile rather than bubble-y.
      </p>
    </section>

  </ComponentPage>
</template>

<style scoped>
/* ─────────────────────────────────────────────────────────────────
   Interactive classes for Variant B DARK-PANEL buttons.

   The dark panel sits inside a root that may have `html.dark` on or off,
   so we can't rely on token cascade. These classes hardcode the dark-mode
   hex values AND their :hover / :active pseudo-class transitions, which
   inline `style=""` attributes cannot do. Used only inside the Variant B
   dark panel so hovering and clicking feel like they do in the light
   panel.
   ───────────────────────────────────────────────────────────────── */

.vb-dark-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  height: 40px;
  padding: 0 20px;
  border-radius: 9999px;
  font-size: 13px;
  font-weight: 500;
  border: none;
  cursor: pointer;
  transition: transform 200ms cubic-bezier(0.16, 1, 0.3, 1), box-shadow 200ms cubic-bezier(0.16, 1, 0.3, 1), background-image 200ms, background-color 200ms;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.30), 0 2px 6px rgba(0, 0, 0, 0.25);
}

.vb-dark-btn:focus-visible {
  outline: none;
  box-shadow: 0 0 0 2px #141311, 0 0 0 4px #B6A8E6;
}

/* Primary — light fill on dark bg with gradient */
.vb-dark-btn--primary {
  background-image: linear-gradient(180deg, #F5F2EE 0%, #E8E4DF 100%);
  color: #1C1C1E;
}
.vb-dark-btn--primary:hover {
  background-image: linear-gradient(180deg, #FFFFFF 0%, #F0EDE9 100%);
  transform: translateY(-3px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.45), 0 16px 32px rgba(0, 0, 0, 0.40);
}
.vb-dark-btn--primary:active {
  /* flat gradient (same color at both stops) — interpolates cleanly
     from the default gradient without the "disappear" flash that
     `background-image: none` produces. */
  background-image: linear-gradient(180deg, #9E9992 0%, #9E9992 100%);
  transform: translateY(3px);
  box-shadow: inset 0 2px 3px rgba(0, 0, 0, 0.45);
}

/* Secondary — outlined over dark surface */
.vb-dark-btn--secondary {
  background: #1C1B19;
  color: #F0EDE9;
  border: 1px solid #2C2A27;
}
.vb-dark-btn--secondary:hover {
  background: #242220;
  transform: translateY(-3px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.45), 0 16px 32px rgba(0, 0, 0, 0.40);
}
.vb-dark-btn--secondary:active {
  background: #161513;
  transform: translateY(3px);
  box-shadow: inset 0 2px 3px rgba(0, 0, 0, 0.55);
}

/* Ghost — transparent, hover fills with surface-sunken */
.vb-dark-btn--ghost {
  background: transparent;
  color: #F0EDE9;
  box-shadow: none;
}
.vb-dark-btn--ghost:hover {
  background: #242220;
}
.vb-dark-btn--ghost:active {
  background: #161513;
}

/* Danger — gradient rose */
.vb-dark-btn--danger {
  background-image: linear-gradient(180deg, #EC8080 0%, #E67070 100%);
  color: #fff;
}
.vb-dark-btn--danger:hover {
  background-image: linear-gradient(180deg, #F29A9A 0%, #EC8080 100%);
  transform: translateY(-3px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.45), 0 16px 32px rgba(0, 0, 0, 0.40);
}
.vb-dark-btn--danger:active {
  background-image: linear-gradient(180deg, #9E4040 0%, #9E4040 100%);
  transform: translateY(3px);
  box-shadow: inset 0 2px 3px rgba(0, 0, 0, 0.55);
}

/* Accent — gradient gold */
.vb-dark-btn--accent {
  background-image: linear-gradient(180deg, #D4A96A 0%, #C4975A 100%);
  color: #1C1C1E;
}
.vb-dark-btn--accent:hover {
  background-image: linear-gradient(180deg, #E4B97A 0%, #D4A96A 100%);
  transform: translateY(-3px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.45), 0 16px 32px rgba(0, 0, 0, 0.40);
}
.vb-dark-btn--accent:active {
  background-image: linear-gradient(180deg, #8B6A3E 0%, #8B6A3E 100%);
  transform: translateY(3px);
  box-shadow: inset 0 2px 3px rgba(0, 0, 0, 0.55);
}

/* Size variants — override height, padding, font-size on primary */
.vb-dark-btn--sm { height: 32px; padding: 0 12px; font-size: 13px; }
.vb-dark-btn--md { height: 40px; padding: 0 20px; font-size: 15px; }
.vb-dark-btn--lg { height: 48px; padding: 0 28px; font-size: 15px; font-weight: 600; }

/* Icon-only square-pill */
.vb-dark-btn--icon-sm { width: 32px; height: 32px; padding: 0; gap: 0; }
.vb-dark-btn--icon-md { width: 40px; height: 40px; padding: 0; gap: 0; }
.vb-dark-btn--icon-lg { width: 48px; height: 48px; padding: 0; gap: 0; }
</style>
