# Kinhold Redesign Brief

> The synthesized direction from inspiration intake. This document locks the big decisions.
> Reference together with `docs/BRAND_GUIDE.md` (which will be updated incrementally as components land) and `docs/design/COMPONENT_ROADMAP.md` (the ordered build plan).

## Locked decisions

| # | Decision | Choice |
|---|----------|--------|
| 1 | Palette direction | **Warm near-white + soft pastel accents + black anchor**. Dark mode = deep warm-charcoal base with equal design craft. Pastel category colors (lavender, peach, mint, soft yellow) for calendar/task/avatar differentiation. |
| 2 | Typography system | **Sans-only.** Keep Plus Jakarta Sans (headings) + Inter (body) + JetBrains Mono (data). Add a new "Hero Number" tier (96–180px, clamp-driven). No serif display. |
| 3 | Information architecture | **Regroup into 6 nav slots:** Today / Plan / Family / Store / Assistant / Settings. Module-specific views become sub-nav inside each group. |
| 4 | Achievements naming | **"Achievements"** (not Badges, not Milestones). Trophy-case stays. Unearned = greyed-out trophy with arc progress ring; earned = solid trophy, no arc. |
| 5 | Desktop layout | **Simpler by default** — top nav + content. Right utility rail only on data-heavy pages (calendar, tasks, vault, food). |
| 6 | Rollout | **Component library first.** Build a/b/c variants per component; Greg picks; library grows. Then view-by-view refactor using the library. |

## Core design tenets (the constraints every component must respect)

1. **DRY above all.** One modal pattern. One card pattern. One filter pattern. One sheet direction. One button system. Current app has modals from 3 directions, inconsistent filters across modules, calendar spacing broken on mobile — the redesign must not ship until these are unified.

2. **Dark mode = #1 priority alongside DRY.** Greg loves it, his wife hates it. Both modes ship with equal craft. No mechanical inversion. Light = airy, warm, welcoming. Dark = premium, focused, calm. Dark/light toggle is a first-class header element — one click from anywhere.

3. **Mobile-first, verified.** Every component gets designed at 375px before scaling up. Calendar spacing breaking on mobile is a canary — we don't ship anything without phone verification.

4. **Pill is the shape language.** Nav, buttons, chips, toggles, badges, status indicators — all pill-shaped (fully rounded). Cards stay rounded rectangles (~16–20px radius). This is what makes the app read as modern/friendly rather than utilitarian/boxy.

5. **Numbers are heroes.** On any data-heavy surface (points, earnings, streaks, completion, balance), the primary metric gets display-scale typography (96–180px). Currency: integer at full size, decimals at ~60%.

6. **Photos where we have them, gradients where we don't.** Content cards get one of two treatments: (a) edge-to-edge photo with title/meta overlay (recipes, restaurants, events with images); (b) iridescent/gradient fill with embossed glyph (vault entries, tasks, empty events, achievements, any imageless surface). No flat white rectangles.

7. **Glass sparingly, purposefully.** Glassmorphism allowed on exactly four surfaces: app-background ambient gradient, floating nav pill, hero dashboard card, modal/sheet backdrop. Banned everywhere else (lists, form inputs, data tables, event tiles, stat numbers). Must pass WCAG AA contrast and must work identically in dark mode.

8. **AI is a reactive copilot in the margins.** The Assistant is never center-stage unless the user chose the chat screen. Everywhere else, AI appears as: (a) a collapsible right-rail panel on desktop, (b) a slide-up sheet on mobile, (c) the elevated center bottom-nav button that opens the panel. AI-generated content always gets a soft-colored card treatment until the user accepts or edits it.

9. **Purposeful motion.** Fast for high-frequency actions (toggle, search, nav). Deliberate for critical actions (delete, invite, spend points, share vault entry) — half-second confirmation animations build trust. `prefers-reduced-motion` honored from day one + app-level override toggle.

10. **Fluid typography via CSS `clamp()`.** No fixed-breakpoint jumps. Each type token has min/preferred/max. Essential for the hero-number treatment to scale from 375px phone to 27" monitor without breaking.

## Visual language inventory

### Mood anchors
- **Image 1 (fintech "Track")** — iridescent pale gradient background, generous whitespace, pill nav. Informs the ambient background layer.
- **Image 10 (Work OS calendar)** — editorial giant day numbers. Informs the hero-number typography treatment.
- **Image 14 (workout dashboard)** — pastel category chips, stat tiles with deltas, pill top nav with dark/light toggle. Confirms mobile/desktop nav language.
- **Image 7 (OpsPulse)** — blurred organic gradient app background. Applied to: dashboard hero, settings, landing. Used sparingly, never on dense data.

### Confirmed components (to be built as the library)
Every component below must work in light + dark mode, at mobile + desktop, and be DRY (single source of truth).

**Primitives**
- Button (filled / outline / ghost / icon-only; pill-shaped; sm/md/lg)
- Input / Textarea / Search field
- Chip / Tag / Removable chip / Status pill
- Avatar (image / preset icon / initials; with optional presence dot or earning ring)
- Icon system (Heroicons, duotone where it fits)
- Checkbox / Radio / Switch
- Progress bar (horizontal, arc)
- Skeleton loader

**Compound patterns**
- Card: `PhotoCard` (hero photo variant) + `GradientCard` (iridescent-fill variant) + `FlatCard` (standard)
- Tab pill group
- Segmented pill filter
- Category chip row
- Paired action row (`[outline] [filled]`)
- Quick action grid (4 square buttons)
- Avatar quick-picker row
- Modal / Bottom sheet / Drawer (one direction per breakpoint)
- Toast / Notification
- Empty state
- Form group with validation

**Navigation**
- Top nav pill bar (desktop) with active filled pill + light/dark toggle
- Bottom floating pill nav (mobile) with elevated center Ask-Assistant FAB
- Sidebar with morph-out active state (when applicable)
- Right utility rail (data-heavy pages only)

**Feature-specific**
- Stat tile (hero number + label + delta chip + micro chart)
- Calendar event tile (color-bar left edge, title, time, avatars)
- Calendar week strip + month grid with colored event dots
- Hero metric card (gradient variant / iridescent variant / photo variant)
- Timeline row (pill bar spanning dates with avatars)
- Achievement tile (locked / in-progress w/ arc / earned)
- Activity feed row (for AI actions, point events, task history)
- AI Activity expandable card (trigger + steps + status pill + reply preview)
- Review step card (numbered, collapsible)
- Receipt card (icon checklist + inline action buttons)
- Meta triplet row (icon + value × 3)
- Author strip (avatar + name + role + action)

### Status vocabulary (standardized)
| State | Color token | Usage |
|-------|-------------|-------|
| Success | sage green | Completed tasks, successful AI runs, earned achievements |
| Pending | slate blue | Awaiting action, scheduled, in-flight |
| Paused | warm amber | Paused automations, snoozed reminders |
| Failed | muted rose | Errors, failed AI calls, destructive confirmations |
| Info | muted lavender | Informational, neutral |
| Warning | warm amber | Approaching deadlines, low stock, expiring |

## Deltas from current `docs/BRAND_GUIDE.md`

Current brand guide is close but needs these updates as the library lands:

1. **Accent color:** current spec has "Muted Gold #C4975A" as primary. Proposal: keep gold as the brand signature (logo, marketing), but make **category pastel accents** (lavender / peach / mint / soft yellow) the primary in-app accent palette. Gold used sparingly on key brand moments, not as the default button color. *Decision to confirm when we build the Button component.*
2. **Type scale:** add "Hero Number" tier at 96–180px (clamp-driven). Migrate existing scale to use `clamp()` for all tokens.
3. **Border radius:** buttons should be pill-shaped (`rounded-full`), not 10px. Current guide has them at 10px.
4. **Shadow/elevation:** currently undefined in detail. Add shadow tokens for: resting card, hover, modal, popover, glass surface.
5. **Motion tokens:** currently undefined. Add duration + easing tokens and `prefers-reduced-motion` variants.
6. **Glass/backdrop-blur tokens:** new addition for the 4 allowed surfaces.
7. **Gradient tokens:** new addition for the iridescent card fills and ambient background.

## Open-source positioning (why this workflow matters beyond the redesign)

The component library isn't just scaffolding for our own redesign — it's a **first-class contributor asset for the open-source project**. Every component exposed in the `/design-system` workspace is code a community developer can copy, compose, or extend without having to reverse-engineer our conventions from the app.

Rules this imposes on the library:
1. **`/design-system` stays available in local development forever.** It is not a throwaway tool that gets removed after the redesign lands. Post-launch, it remains the canonical reference for contributors.
2. **Each component's page is self-documenting.** Title, description, usage rules, and variants are all on the page. A developer landing cold should understand what the component does and when to use it without reading other files.
3. **Chosen variants live in `resources/js/components/design-system/`** with a short header comment explaining purpose, props, and which `/design-system/:slug` page owns them.
4. **Gating in production is conservative:** the route stays open on local dev by default. Any production gating (env flag, parent-only, logged-in only) gets added deliberately when we merge the redesign to `main`. A good default is "open on localhost, gated on prod" — contributors running `./setup-simple.sh` should see the library without any extra config.
5. **The roadmap file doubles as an RFC-style contribution guide.** When someone wants to add a new component, they PR a roadmap entry first, then build variants on a new page, then promote to the library — same workflow we use internally.

This is listed as decision #7 in the implicit brief and should be surfaced in CONTRIBUTING.md once the library has a few components shipped.

## Workflow for building the library

1. **Scaffold infrastructure (one session, smaller model):** create a `/design-system` route in the SPA with a sidebar listing every component. Each component page shows its variants side-by-side in light and dark mode at 375px / 768px / 1280px widths. This is the shared workspace.
2. **Per-component session (smaller model):**
   - Read the roadmap entry for the next component
   - Build 2–3 variants on that component's page
   - Report back with screenshots / URLs
   - Greg picks one variant
   - The chosen variant gets promoted to a real Vue component in `resources/js/components/design-system/`
   - The roadmap gets ticked off, brief gets updated if anything shifts
   - Single commit, PR optional (feature branch is `redesign/visual-overhaul`)
3. **Repeat until the library is complete.**
4. **View-by-view refactor (smaller model, batch):** once the library exists, refactor each view to use the new components. One view per session. Start with dashboard.

See `docs/design/COMPONENT_ROADMAP.md` for the ordered build list and per-component specs.
