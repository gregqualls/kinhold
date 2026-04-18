<script setup>
import ComponentPage from '../../shared/ComponentPage.vue'

// ── Token reference data ──────────────────────────────────────────────────────
const GRADIENT_TOKENS = [
  {
    cssVar:  '--gradient-iridescent-subtle',
    tw:      'bg-iridescent-subtle',
    usage:   'Vault entries, imageless tasks, achievement tiles, empty states, hero metric cards.',
  },
  {
    cssVar:  '--gradient-iridescent-warm',
    tw:      'bg-iridescent-warm',
    usage:   'Streak cards, urgency moments, "on fire" highlights.',
  },
  {
    cssVar:  '--gradient-ambient',
    tw:      'bg-ambient',
    usage:   'App background on dashboard / settings / landing ONLY. Never on dense data surfaces.',
  },
  {
    cssVar:  '--gradient-accent-lavender',
    tw:      'bg-accent-lavender',
    usage:   'Lavender category card fill, badge tint.',
  },
  {
    cssVar:  '--gradient-accent-peach',
    tw:      'bg-accent-peach',
    usage:   'Peach category card fill, badge tint.',
  },
  {
    cssVar:  '--gradient-accent-mint',
    tw:      'bg-accent-mint',
    usage:   'Mint category card fill, badge tint.',
  },
  {
    cssVar:  '--gradient-accent-sun',
    tw:      'bg-accent-sun',
    usage:   'Sun category card fill, badge tint.',
  },
]

// Category accent card data — for section 3
const ACCENT_CARDS = [
  { tw: 'bg-accent-lavender', label: 'Lavender', inkLight: '#6856B2', inkDark: '#B6A8E6' },
  { tw: 'bg-accent-peach',    label: 'Peach',    inkLight: '#BA562E', inkDark: '#F0A882' },
  { tw: 'bg-accent-mint',     label: 'Mint',     inkLight: '#2E8A62', inkDark: '#7CD6AE' },
  { tw: 'bg-accent-sun',      label: 'Sun',      inkLight: '#A2780C', inkDark: '#E6C452' },
]
</script>

<template>
  <ComponentPage
    title="0.4 Gradient tokens"
    description="Iridescent card fills and ambient backgrounds. Seven gradient tokens — each a single CSS variable that swaps automatically between light and dark. Gradients are for atmosphere and warmth, not decoration. Tenet #6: photos where we have them, gradients where we don't."
    status="chosen"
  >

    <!-- ══════════════════════════════════════════════════
         SECTION 1 — AMBIENT BACKGROUND
         Full-width panel using bg-ambient in both modes.
         A sample card demonstrates how content reads over it.
         ═══════════════════════════════════════════════════ -->
    <section class="mb-14">
      <h2 class="text-xs font-semibold uppercase tracking-widest text-ink-secondary mb-2">
        1 — Ambient background
      </h2>
      <p class="text-body-sm text-ink-tertiary mb-1">
        The OpsPulse / Image 7 mood — very pale, multi-hue organic wash that suggests atmosphere without demanding attention.
      </p>
      <p class="text-caption text-ink-tertiary mb-6">
        <strong class="font-semibold text-ink-secondary">Rule:</strong>
        Use ONLY as app background on dashboard / settings / landing. Never on dense data surfaces.
      </p>

      <!-- Light mode panel -->
      <div
        class="rounded-2xl overflow-hidden mb-4"
        style="border: 1px solid #E8E4DF"
      >
        <div class="px-4 pt-4 pb-1">
          <p class="text-xs font-medium text-ink-tertiary">Light mode</p>
        </div>
        <!-- The ambient bg itself — min 200px tall, full width -->
        <!-- bg-ambient sets background-image; bg-surface-app sets the base background-color -->
        <div
          class="bg-ambient bg-surface-app min-h-[220px] flex items-center justify-center p-8"
        >
          <!-- Sample hero metric card sitting over the ambient bg -->
          <div
            class="bg-surface-raised shadow-resting rounded-card p-6 w-full max-w-md overflow-hidden"
          >
            <p class="text-caption text-ink-tertiary mb-1 font-medium uppercase tracking-widest">Points balance</p>
            <p class="text-hero font-heading text-ink-primary leading-none tracking-tight">1,247</p>
            <p class="text-body-sm text-ink-secondary mt-2">+38 this week</p>
          </div>
        </div>
      </div>

      <!-- Dark mode panel -->
      <div
        class="rounded-2xl overflow-hidden"
        style="border: 1px solid #2C2A27"
      >
        <div class="px-4 pt-4 pb-1" style="background:#141311">
          <p class="text-xs font-medium" style="color:#6E6B67">Dark mode</p>
        </div>
        <!-- Literal dark ambient gradient — html.dark is not active on this page so we inline it -->
        <div>
          <div
            class="min-h-[220px] flex items-center justify-center p-8"
            style="background:
              radial-gradient(ellipse 80% 60% at 20% 10%, rgba(72, 110, 156, 0.18), transparent 55%),
              radial-gradient(ellipse 70% 50% at 85% 25%, rgba(104, 86, 178, 0.15), transparent 55%),
              radial-gradient(ellipse 60% 50% at 70% 90%, rgba(190, 130, 48, 0.10), transparent 55%),
              #141311"
          >
            <!-- We use an inline style here because we can't toggle html.dark inside the page -->
            <!-- Instead render the dark gradient literally -->
            <div
              class="rounded-card p-6 w-full max-w-md overflow-hidden shadow-resting"
              style="background:#1C1B19"
            >
              <p
                class="text-caption font-medium uppercase tracking-widest mb-1"
                style="color:#6E6B67"
              >Points balance</p>
              <p
                class="text-hero font-heading leading-none tracking-tight"
                style="color:#F0EDE9"
              >1,247</p>
              <p
                class="text-body-sm mt-2"
                style="color:#A09C97"
              >+38 this week</p>
            </div>
          </div>
        </div>
      </div>

      <p class="text-caption text-ink-tertiary mt-3">
        Token: <code class="font-mono">bg-ambient</code> / <code class="font-mono">--gradient-ambient</code>.
        The light and dark values swap via <code class="font-mono">:root</code> and <code class="font-mono">html.dark</code> automatically.
      </p>
    </section>


    <!-- ══════════════════════════════════════════════════
         SECTION 2 — IRIDESCENT CARD FILLS
         Two cards side by side: subtle + warm.
         ═══════════════════════════════════════════════════ -->
    <section class="mb-14">
      <h2 class="text-xs font-semibold uppercase tracking-widest text-ink-secondary mb-2">
        2 — Iridescent card fills
      </h2>
      <p class="text-body-sm text-ink-tertiary mb-6">
        Two sibling gradients built as layered radial wells of pastel hues fading to transparent over the card's base surface — the opalescent fintech-card look. "Subtle" is the default for any imageless content surface; "Warm" is reserved for energetic moments (streaks, urgency, on-fire states).
      </p>
      <p class="text-caption text-ink-tertiary mb-6">
        <strong class="font-semibold text-ink-secondary">Usage:</strong>
        pair with <code class="font-mono">bg-surface-raised</code> so the card's white (light) or warm-charcoal (dark) base shows through the transparent negative space — that's what creates the opal feel.
      </p>

      <!-- Light mode panel -->
      <div
        class="rounded-2xl border border-border-subtle p-6 mb-4"
        style="background:#FAF8F5"
      >
        <p class="text-xs font-medium text-ink-tertiary mb-5">Light mode</p>
        <div class="flex flex-col sm:flex-row gap-4">

          <!-- Subtle card — token-based: gradient wells layer over bg-surface-raised.
               Renders correctly when root theme is light. Toggle the theme to verify. -->
          <div class="flex flex-col gap-2 flex-1">
            <div
              class="bg-iridescent-subtle bg-surface-raised rounded-card shadow-resting p-6 relative overflow-hidden"
              style="min-height: 140px"
            >
              <!-- Embossed glyph watermark -->
              <svg
                class="absolute right-3 bottom-3 opacity-10"
                style="width:72px; height:72px; color:#6856B2"
                fill="currentColor" viewBox="0 0 24 24"
                aria-hidden="true"
              >
                <!-- Sparkles icon -->
                <path d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z" />
              </svg>
              <p class="text-body-sm font-semibold font-heading text-ink-primary mb-1 relative">Today's Goal</p>
              <p class="text-caption text-ink-secondary relative">3 tasks · 2 events</p>
            </div>
            <code class="text-caption font-mono text-ink-tertiary">bg-iridescent-subtle</code>
          </div>

          <!-- Warm card — token-based: gradient wells layer over bg-surface-raised.
               Renders correctly when root theme is light. Toggle the theme to verify. -->
          <div class="flex flex-col gap-2 flex-1">
            <div
              class="bg-iridescent-warm bg-surface-raised rounded-card shadow-resting p-6 relative overflow-hidden"
              style="min-height: 140px"
            >
              <!-- Embossed glyph watermark -->
              <svg
                class="absolute right-3 bottom-3 opacity-10"
                style="width:72px; height:72px; color:#BA562E"
                fill="currentColor" viewBox="0 0 24 24"
                aria-hidden="true"
              >
                <!-- Fire / bolt icon -->
                <path fill-rule="evenodd" d="M14.615 1.595a.75.75 0 01.359.852L12.982 9.75h7.268a.75.75 0 01.548 1.262l-10.5 11.25a.75.75 0 01-1.272-.71l1.992-7.302H3.818a.75.75 0 01-.548-1.262l10.5-11.25a.75.75 0 01.845-.143z" clip-rule="evenodd" />
              </svg>
              <p class="text-body-sm font-semibold font-heading text-ink-primary mb-1 relative">On Fire</p>
              <p class="text-caption text-ink-secondary relative">5-day streak</p>
            </div>
            <code class="text-caption font-mono text-ink-tertiary">bg-iridescent-warm</code>
          </div>

        </div>
      </div>

      <!-- Dark mode panel — inline styles to simulate dark ground without toggling html.dark -->
      <div
        class="rounded-2xl border p-6"
        style="background:#141311; border-color:#2C2A27"
      >
        <p class="text-xs font-medium mb-5" style="color:#6E6B67">Dark mode</p>
        <div class="flex flex-col sm:flex-row gap-4">

          <!-- Subtle card (dark) — oversized wells + faint linear base to kill splotch gaps -->
          <div class="flex flex-col gap-2 flex-1">
            <div
              class="rounded-card shadow-resting p-6 relative overflow-hidden"
              style="min-height:140px; background-color:#1C1B19; background-image:
                radial-gradient(ellipse 140% 110% at 15% 10%, rgba(104,  86, 178, 0.28), transparent 85%),
                radial-gradient(ellipse 130% 100% at 85% 90%, rgba(240, 168, 130, 0.24), transparent 85%),
                radial-gradient(ellipse 110%  90% at 60% 35%, rgba(124, 214, 174, 0.18), transparent 90%),
                linear-gradient(135deg, rgba(104,  86, 178, 0.06) 0%, rgba(240, 168, 130, 0.06) 100%);"
            >
              <svg
                class="absolute right-3 bottom-3 opacity-[0.12]"
                style="width:72px; height:72px; color:#B6A8E6"
                fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"
              >
                <path d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z" />
              </svg>
              <p class="text-body-sm font-semibold font-heading relative" style="color:#F0EDE9; margin-bottom:4px">Today's Goal</p>
              <p class="text-caption relative" style="color:#A09C97">3 tasks · 2 events</p>
            </div>
            <code class="text-caption font-mono" style="color:#6E6B67">bg-iridescent-subtle (dark)</code>
          </div>

          <!-- Warm card (dark) — oversized wells + faint linear base to kill splotch gaps -->
          <div class="flex flex-col gap-2 flex-1">
            <div
              class="rounded-card shadow-resting p-6 relative overflow-hidden"
              style="min-height:140px; background-color:#1C1B19; background-image:
                radial-gradient(ellipse 140% 110% at 15% 15%, rgba(240, 168, 130, 0.32), transparent 85%),
                radial-gradient(ellipse 130% 100% at 85% 65%, rgba(230, 196,  82, 0.25), transparent 85%),
                radial-gradient(ellipse 110%  90% at 55% 90%, rgba(182, 168, 230, 0.20), transparent 90%),
                linear-gradient(135deg, rgba(240, 168, 130, 0.07) 0%, rgba(230, 196,  82, 0.07) 100%);"
            >
              <svg
                class="absolute right-3 bottom-3 opacity-[0.12]"
                style="width:72px; height:72px; color:#F0A882"
                fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"
              >
                <path fill-rule="evenodd" d="M14.615 1.595a.75.75 0 01.359.852L12.982 9.75h7.268a.75.75 0 01.548 1.262l-10.5 11.25a.75.75 0 01-1.272-.71l1.992-7.302H3.818a.75.75 0 01-.548-1.262l10.5-11.25a.75.75 0 01.845-.143z" clip-rule="evenodd" />
              </svg>
              <p class="text-body-sm font-semibold font-heading relative" style="color:#F0EDE9; margin-bottom:4px">On Fire</p>
              <p class="text-caption relative" style="color:#A09C97">5-day streak</p>
            </div>
            <code class="text-caption font-mono" style="color:#6E6B67">bg-iridescent-warm (dark)</code>
          </div>

        </div>
      </div>
    </section>


    <!-- ══════════════════════════════════════════════════
         SECTION 3 — CATEGORY ACCENT GRADIENTS
         Four small cards: Lavender / Peach / Mint / Sun.
         ═══════════════════════════════════════════════════ -->
    <section class="mb-14">
      <h2 class="text-xs font-semibold uppercase tracking-widest text-ink-secondary mb-2">
        3 — Category accent gradients
      </h2>
      <p class="text-body-sm text-ink-tertiary mb-6">
        Single-hue helpers for GradientCard fills, category landing surfaces, and badge tints. Subtle: they should feel like a coloured whisper, not a spotlight.
      </p>

      <!-- Light mode panel -->
      <div
        class="rounded-2xl border border-border-subtle p-6 mb-4"
        style="background:#FAF8F5"
      >
        <p class="text-xs font-medium text-ink-tertiary mb-5">Light mode</p>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
          <div
            v-for="card in ACCENT_CARDS"
            :key="card.label"
            class="flex flex-col gap-2"
          >
            <div
              :class="[card.tw, 'rounded-card', 'shadow-resting']"
              class="flex items-end p-3"
              style="height: 100px"
            >
              <span
                class="text-body-sm font-semibold font-heading"
                :style="{ color: card.inkLight }"
              >{{ card.label }}</span>
            </div>
            <code class="text-[10px] font-mono text-ink-tertiary">bg-accent-{{ card.label.toLowerCase() }}</code>
          </div>
        </div>
      </div>

      <!-- Dark mode panel -->
      <div
        class="rounded-2xl border p-6"
        style="background:#141311; border-color:#2C2A27"
      >
        <p class="text-xs font-medium mb-5" style="color:#6E6B67">Dark mode</p>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">

          <!-- Lavender dark -->
          <div class="flex flex-col gap-2">
            <div
              class="rounded-card shadow-resting flex items-end p-3"
              style="height:100px; background: linear-gradient(135deg, #302A48 0%, #4B4368 50%, #302A48 100%)"
            >
              <span class="text-body-sm font-semibold font-heading" style="color:#B6A8E6">Lavender</span>
            </div>
            <code class="text-[10px] font-mono" style="color:#6E6B67">bg-accent-lavender (dark)</code>
          </div>

          <!-- Peach dark -->
          <div class="flex flex-col gap-2">
            <div
              class="rounded-card shadow-resting flex items-end p-3"
              style="height:100px; background: linear-gradient(135deg, #3E241A 0%, #623E2F 50%, #3E241A 100%)"
            >
              <span class="text-body-sm font-semibold font-heading" style="color:#F0A882">Peach</span>
            </div>
            <code class="text-[10px] font-mono" style="color:#6E6B67">bg-accent-peach (dark)</code>
          </div>

          <!-- Mint dark -->
          <div class="flex flex-col gap-2">
            <div
              class="rounded-card shadow-resting flex items-end p-3"
              style="height:100px; background: linear-gradient(135deg, #18342A 0%, #2C5444 50%, #18342A 100%)"
            >
              <span class="text-body-sm font-semibold font-heading" style="color:#7CD6AE">Mint</span>
            </div>
            <code class="text-[10px] font-mono" style="color:#6E6B67">bg-accent-mint (dark)</code>
          </div>

          <!-- Sun dark -->
          <div class="flex flex-col gap-2">
            <div
              class="rounded-card shadow-resting flex items-end p-3"
              style="height:100px; background: linear-gradient(135deg, #342C0A 0%, #584A18 50%, #342C0A 100%)"
            >
              <span class="text-body-sm font-semibold font-heading" style="color:#E6C452">Sun</span>
            </div>
            <code class="text-[10px] font-mono" style="color:#6E6B67">bg-accent-sun (dark)</code>
          </div>

        </div>
      </div>
    </section>


    <!-- ══════════════════════════════════════════════════
         SECTION 4 — TOKEN REFERENCE TABLE
         All 7 tokens, usage, and a gradient chip preview.
         ═══════════════════════════════════════════════════ -->
    <section class="mb-6">
      <h2 class="text-xs font-semibold uppercase tracking-widest text-ink-secondary mb-2">
        4 — Token reference
      </h2>
      <p class="text-body-sm text-ink-tertiary mb-5">
        All seven gradient tokens. Each is a single CSS variable — value swaps between
        <code class="font-mono text-caption">:root</code> (light) and
        <code class="font-mono text-caption">html.dark</code> (dark) automatically.
      </p>

      <div class="rounded-xl border border-border-subtle overflow-x-auto">
        <table class="w-full text-xs min-w-[540px]">
          <thead>
            <tr class="bg-surface-sunken border-b border-border-subtle">
              <th class="text-left px-3 py-2 font-semibold text-ink-secondary w-8">Preview</th>
              <th class="text-left px-3 py-2 font-semibold text-ink-secondary">CSS var</th>
              <th class="text-left px-3 py-2 font-semibold text-ink-secondary">Tailwind utility</th>
              <th class="text-left px-3 py-2 font-semibold text-ink-secondary hidden sm:table-cell">Intended use</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(token, i) in GRADIENT_TOKENS"
              :key="token.cssVar"
              :class="i % 2 === 1 ? 'bg-surface-sunken' : 'bg-surface-raised'"
            >
              <!-- Gradient chip -->
              <td class="px-3 py-2">
                <div
                  :class="[token.tw]"
                  class="rounded w-8 h-5 border border-border-subtle flex-shrink-0"
                ></div>
              </td>
              <td class="px-3 py-2 font-mono text-ink-tertiary text-[11px] whitespace-nowrap">{{ token.cssVar }}</td>
              <td class="px-3 py-2 font-mono text-ink-primary whitespace-nowrap">{{ token.tw }}</td>
              <td class="px-3 py-2 text-ink-tertiary leading-snug hidden sm:table-cell">{{ token.usage }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Implementation notes -->
      <div class="mt-4 rounded-xl border border-border-subtle bg-surface-sunken px-4 py-3 text-body-sm text-ink-secondary">
        <strong class="text-ink-primary">Tenet #6 — Photos where we have them, gradients where we don't.</strong>
        Content cards get one of two treatments: (a) edge-to-edge photo with title/meta overlay for recipes,
        restaurants, and events with images; (b) iridescent/gradient fill with embossed glyph for vault
        entries, tasks, empty events, achievements, and any imageless surface. No flat white rectangles.
        The ambient gradient (<code class="font-mono text-caption">bg-ambient</code>) is the sole exception
        to the card rule — it lives only on the app shell background.
      </div>
    </section>

  </ComponentPage>
</template>

<style scoped>
/*
  Dark-mode ambient preview panel.
  Since html.dark is on the root element and we're inside a light-mode
  design-system page, we simulate the dark ambient bg with a literal
  inline style in the template. Nothing scoped needed.
*/
</style>
