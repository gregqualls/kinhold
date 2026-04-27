# Kinhold Redesign — Component Roadmap

> The ordered build list for the component library. Each entry is sized for a single smaller-model session (Haiku/Sonnet).
> Work top-to-bottom. Do not skip tiers — later components depend on earlier tokens/primitives.
> When a component is chosen and promoted, tick it off. Update this roadmap if anything shifts.

**Source of truth:**
- Vision & decisions: `docs/design/REDESIGN_BRIEF.md`
- Current brand foundations: `docs/BRAND_GUIDE.md`
- Raw inspiration notes: `docs/design/inspiration-notes.md`

**Branch:** `redesign/visual-overhaul`

**Why this library exists (beyond the redesign):** `/design-system` is a permanent fixture of local development, designed to double as a community contribution reference. Every component page is self-documenting so a fork-and-hack developer can compose new features against the real library. See "Open-source positioning" in `REDESIGN_BRIEF.md`.

---

## Step 0 — Scaffold the design-system workspace

**Session size:** ~1 smaller-model session. No variants to decide — this is pure infrastructure.

**Goal:** create a `/design-system` route in the SPA that lists every component and renders its variants.

**Tasks:**
1. Add a new Vue route at `/design-system` (only visible to authenticated parent users, or gated behind a `VITE_ENABLE_DESIGN_SYSTEM=true` env flag)
2. Layout: left sidebar lists all components (grouped by tier), main area shows the selected component's variants
3. Each component page template supports:
   - Title + short description
   - A light-mode panel and a dark-mode panel rendered side-by-side
   - Responsive preview toggle (375px / 768px / 1280px widths — can use an iframe or a resizable container)
   - Variant columns labeled A / B / C
   - Code snippet preview (optional, nice-to-have)
4. Add a "Chosen" badge affordance on each variant so we can mark what's been picked
5. Placeholder pages for every component in the roadmap below (empty variants — they'll be filled in per-session)

**Deliverable:** a working `/design-system` route with empty component pages, ready to receive variants.

**Gating policy:** open by default (no auth, no role gate) so contributors cloning the repo and running `./setup-simple.sh` or `npm run dev` see the library immediately. Revisit gating when the redesign merges to `main` — at that point, consider an env flag for production builds but keep it fully open on localhost.

---

## Tier 0 — Foundation tokens (no variant decisions; one canonical set each)

These are the bedrock. Build them once, right. No a/b/c options — we lock them based on the brief.

### 0.1 Color tokens (light + dark)
**Files:** `resources/css/tokens.css` + update `tailwind.config.js`

- Define CSS custom properties for every color in `BRAND_GUIDE.md` under `:root` (light) and `.dark` (dark)
- Light + dark palettes get equal craft (no mechanical inversion — the dark tokens are authored, not derived)
- Export them as Tailwind theme extensions so classes like `bg-surface`, `text-primary`, `border-subtle` work
- Add category pastel accents: `accent-lavender`, `accent-peach`, `accent-mint`, `accent-sun` (each with a `-soft` variant for backgrounds and a `-bold` variant for emphasis)
- Add status tokens: `status-success`, `status-pending`, `status-paused`, `status-failed`, `status-info`, `status-warning`

### 0.2 Typography scale (fluid via `clamp()`)
**Files:** `resources/css/tokens.css` + Tailwind config

- Define `--text-hero`, `--text-display`, `--text-h1`, `--text-h2`, `--text-h3`, `--text-h4`, `--text-body`, `--text-body-sm`, `--text-caption`, `--text-mono` — each as a `clamp(min, preferred, max)`
- Hero Number tier: `clamp(4rem, 10vw, 11.25rem)` (64–180px)
- Display: `clamp(2rem, 4vw, 3.5rem)` (32–56px)
- All others scale proportionally, tested at 320/500/980/1440/2000 widths
- Map to Tailwind utility classes: `text-hero`, `text-display`, etc.

### 0.3 Spacing, radii, shadow, motion tokens
**Files:** `resources/css/tokens.css` + Tailwind config

- **Spacing:** keep Tailwind's default 4px scale
- **Radii:** `rounded-pill` (9999px) for buttons/chips/nav, `rounded-card` (20px) for cards, `rounded-sheet` (28px) for modals/sheets
- **Shadow:** `shadow-resting`, `shadow-hover`, `shadow-elevated`, `shadow-modal`, `shadow-glass` (each tuned for light + dark)
- **Motion:**
  - Durations: `duration-instant` (100ms), `duration-quick` (200ms), `duration-deliberate` (500ms), `duration-sheet` (350ms)
  - Easings: `ease-out-soft`, `ease-in-out-soft`, `ease-spring`
  - All motion wrapped in `@media (prefers-reduced-motion: reduce)` → duration 0 or reduced

### 0.4 Gradient tokens (iridescent + ambient background)
**Files:** `resources/css/tokens.css`

- `--gradient-iridescent-subtle` — pale lavender → mint → peach, for card fills
- `--gradient-iridescent-warm` — peach → coral → amber, for energetic/streak cards
- `--gradient-ambient-light` — very pale multi-hue blur for app background in light mode
- `--gradient-ambient-dark` — deep warm-charcoal with subtle warm-blue bleed for dark mode
- Each expressible as a single CSS variable so components can reference them without reinventing

### 0.5 Glass/backdrop-blur utilities
- `.surface-glass` — backdrop-blur + translucent bg + subtle inner highlight; variants for light and dark
- Document in `/design-system` exactly the 4 allowed surfaces: app bg, nav pill, hero card, modal backdrop

---

## Tier 1 — Primitives (a/b/c variants, Greg picks)

These are the atoms. Every component gets 2–3 variants shown side-by-side. Small, fast decisions.

### 1.1 Button
Variants to explore:
- **A:** filled pill, solid color fill, no shadow, flat press state
- **B:** filled pill with subtle gradient + shadow-resting, lifts on hover
- **C:** filled pill with a soft inner highlight (glassy), shadow-resting
- All three in: primary (gold or category-accent), secondary (outline), ghost (transparent), danger (status-failed)
- Sizes: sm (32h), md (40h), lg (52h)
- Icon-only square variant with same radii

**Decision criteria:** which feels premium without being bubble-y?

### 1.2 Input / Textarea / Search field
Variants:
- **A:** flat filled with border-subtle, focuses to accent ring
- **B:** flat filled, no border until focus, subtle inset shadow
- **C:** rounded-pill shape (only for search fields specifically)
- Support: prefix/suffix icon, clear button, error state, helper text, label placement (top / floating)

### 1.3 Chip / Tag / Status pill
Variants:
- **A:** solid-fill pastel bg + darker text (category pastels)
- **B:** outlined pill with soft bg tint
- **C:** status variants — small dot + label + optional close X for removable filter chips
- Sizes: sm / md
- Used everywhere: category tags, status, removable filters, feature badges

### 1.4 Avatar
Variants:
- **A:** circle photo, no ring
- **B:** circle photo with a thin brand-color ring (family-member color)
- **C:** circle with an arc progress ring around it (for in-progress achievements)
- Fallbacks: initials with pastel bg, preset icon with colored bg
- Sizes: xs/sm/md/lg/xl

### 1.5 Checkbox / Radio / Switch
Variants per control:
- **A:** minimal, neutral filled
- **B:** pastel-accent when checked, subtle animation
- **C:** Apple-style toggle for switches specifically
- Must be accessible (keyboard, screen reader, reduced motion)

### 1.6 Progress bar (horizontal + arc)
Variants:
- **A:** horizontal pill with solid accent fill
- **B:** horizontal pill with gradient fill
- **C:** arc / half-ring for gauges and achievement rings
- Support: determinate, indeterminate (animated), sizes sm/md/lg

### 1.7 Skeleton loader
Single canonical variant — shimmer animation that respects `prefers-reduced-motion` (falls back to static muted bg). Multiple shapes: text-line, circle-avatar, card, list-row.

---

## Tier 2 — Cards (the defining visual pattern)

Cards are the workhorse. Three distinct flavors; all three get built as separate components so consumers pick explicitly.

### 2.1 FlatCard
Plain card, subtle border, shadow-resting. Used for form groups, list rows on desktop, settings panels. One canonical look — maybe padding variants (sm/md/lg).

### 2.2 PhotoCard
Variants:
- **A:** hero photo top (~60% of card), title + meta below on white/surface bg
- **B:** edge-to-edge photo with title + meta overlaid at the bottom (real estate / recipe pattern)
- **C:** photo-first with chip badges overlaid top-right (for status/countdown)
- Must handle: missing photo gracefully (auto-fallback to GradientCard), aspect ratios, lazy loading

### 2.3 GradientCard (iridescent fill)
Variants:
- **A:** pale iridescent wash, embossed glyph (app icon or category icon) as watermark
- **B:** radial gradient centered, glyph top-left, content bottom-left
- **C:** angular linear gradient (more energetic), glyph as subtle texture
- Used for: vault entries, imageless tasks, achievement tiles when a user earns one, empty-state placeholders, and hero metric cards on the dashboard

---

## Tier 3 — Navigation

### 3.1 Top nav (desktop)
Variants:
- **A:** pill nav bar centered at top with brand mark left, active item filled black
- **B:** pill nav left-aligned, with search in the middle, actions on the right
- **C:** same as B but with a subtle backdrop-blur glass treatment
- Must include: light/dark toggle pill, search pill, notification pill, avatar pill, active-module indicator

### 3.2 Bottom nav (mobile)
Variants:
- **A:** floating pill with 5 evenly-spaced icon+label slots, active filled
- **B:** floating pill with 4 slots + elevated center Ask-Assistant FAB (sparkle icon)
- **C:** B plus a subtle glass background treatment
- Decision: probably B or C. Elevated center FAB = Ask Assistant is canonical per the brief.

### 3.3 Sidebar (data-heavy desktop views)
Variants:
- **A:** plain sidebar with accent-filled pill for active item
- **B:** sidebar with morph-out active state (active item's white surface extends into content area, image 6 pattern)
- **C:** sidebar with collapsed / expanded states + group dividers
- Used on: calendar, tasks, vault, food module

### 3.4 Right utility rail
Single variant — pattern for data-heavy pages. Contains: mini-month / quick filters / presence list / saved views / secondary actions. Collapses to a slide-up sheet on mobile.

---

## Tier 4 — Compound patterns

### 4.1 Tab pill group (`<TabPillGroup>`)
Variants:
- **A:** filled active pill, outlined inactive pills (recipe detail pattern)
- **B:** underline active, plain inactive (cleaner, less visual weight)
- **C:** bg-tinted active, transparent inactive
- Used: recipe detail, restaurant detail, vault entry sections, family profile tabs, food module top nav

### 4.2 Segmented pill filter (`<SegmentedFilter>`)
Variants:
- **A:** filled active, plain inactive (All / Success / Failed / Paused pattern)
- **B:** pill container with sliding active background (iOS segmented control style)
- Used: activity feed, notifications, time-range selectors on charts

### 4.3 Category chip row (`<CategoryChipRow>`)
Icon + label pill row with active state (Hanwik "Just for you" / workout categories pattern). Horizontal scroll on mobile if overflow. Single canonical variant — decision is only the chip itself (see 1.3).

### 4.4 Paired action row (`<ActionPair>`)
`[outline] [filled]` — canonical two-button decision pattern. Single variant. Used on every list row with two actions (view/redeem, edit/complete, decline/accept).

### 4.5 Quick action grid (`<QuickActions>`)
4 (or 6) square icon+label buttons in a row. Single canonical variant. Used on landing pages (dashboard, tasks, meal plan).

### 4.6 Avatar quick-picker (`<AvatarPicker>`)
Horizontal row of avatars with names. Single variant. Used for task assignment, kudos recipient, vault share, meal planner.

### 4.7 Modal / Bottom sheet / Drawer
Variants:
- **A:** iOS-style bottom sheet on mobile, centered modal on desktop (one direction per breakpoint — solves the "modals from 3 places" complaint)
- **B:** always bottom sheet (even on desktop)
- **C:** always centered modal with backdrop blur
- Must handle: stacking (sheet inside sheet), drag-to-dismiss on mobile, ESC/click-backdrop close on desktop
- Canonical directive: **only one direction per breakpoint.** Any existing component that pops a center modal or right drawer gets updated to use this.

### 4.8 Toast / Notification
Variants:
- **A:** bottom-center toast stack
- **B:** top-right toast stack (desktop) / top-center on mobile
- **C:** inline banner (for persistent notifications)
- Semantic variants: success / info / warning / error

### 4.9 Empty state
Variants:
- **A:** centered illustration (iridescent glyph) + title + description + CTA
- **B:** same as A but with suggested-actions list beneath (quick-start)
- **C:** minimal — icon + single sentence, no CTA (for very terminal empties)

### 4.10 Form group
Label placement (top / floating / inline), error state, helper text, validation timing (on blur / on submit). Single canonical pattern, no variants — decide once.

---

## Tier 5 — Feature-specific compounds

### 5.1 Stat tile (`<StatTile>`)
The hero-number component. Variants:
- **A:** huge number + label + delta chip (up/down)
- **B:** A plus a micro bar/line chart at the bottom
- **C:** B plus a filter dropdown (time range)
- Used: dashboard widgets, points bank summary, task completion summary, meal plan progress

### 5.2 Hero metric card
Variants:
- **A:** GradientCard iridescent variant with stat tile contents
- **B:** GradientCard warm/fire variant for streaks/urgency
- **C:** PhotoCard variant (today's meal plan, upcoming event)
- Used: dashboard top, module landing heroes

### 5.3 Calendar event tile (`<EventTile>`)
Variants:
- **A:** filled pastel bg, title + time + avatars (image 8 mobile pattern)
- **B:** dark tile with left-edge color bar (image 9 desktop dark pattern)
- **C:** pill bar spanning dates (for week view + meal plan timeline)
- Must handle: source types (task/manual/Google/ICS), visibility (visible/busy/private)

### 5.4 Calendar week strip (`<WeekStrip>`)
Horizontal scroll of day-of-week + date with active-day solid circle. Single canonical variant. Used: calendar week view, meal plan, task review week, leaderboard period.

### 5.5 Calendar month grid (`<MonthGrid>`)
Clean grid with colored event dots under each date. Single variant. Today highlighted with solid accent circle, selected date with outline.

### 5.6 Day header with hero numeric (`<DayHeader>`)
Editorial-scale day number + tiny day-of-week tag (image 10 pattern). Fluid scale via `clamp()`. Single variant.

### 5.7 Timeline row (`<TimelineRow>`)
Pill bar spanning date range with avatars + label inside + drag handle. Single variant. Used: meal plan, workout schedule equivalent, reward auction windows, family schedule overlay.

### 5.8 Achievement tile (`<AchievementTile>`)
Three states (data-driven, single component):
- `locked` — trophy icon greyed out, no arc
- `in-progress` — trophy greyed out + arc progress ring around the icon
- `earned` — trophy in full color/accent, no arc, subtle glow

Single canonical variant — the state machine is the design.

### 5.9 Activity feed row (`<ActivityRow>`)
Variants:
- **A:** compact — icon + title + status pill + timestamp
- **B:** expandable — compact row that expands to show trigger/steps/preview (AI activity pattern from image 16)
- **C:** conversational — avatar + text + time (point transactions, kudos feed)
- Used: points activity feed, AI activity feed, task history, notifications drawer

### 5.10 AI Activity card (`<AIActivityCard>`)
Specialized activity row. Expanded state shows:
- Trigger (what the user asked)
- Execution time
- Step-by-step tool calls (checkmark list)
- AI output preview in a pastel-bg card
- Actions: dismiss / re-run / view full conversation

Single variant.

### 5.11 Review step card (`<StepCard>`)
Numbered + collapsible + title + body. Single variant. Used: recipe instructions, onboarding wizard, vault playbook guides, meal plan cook flow.

### 5.12 Receipt card (`<ReceiptCard>`)
Icon checklist of facts + inline outline action buttons. Single variant. Used: booking confirmation, task detail, reward purchase, event detail.

### 5.13 Meta triplet (`<MetaTriplet>`)
Icon + value × 3 with dividers. Single variant. Used: recipe time/difficulty/calories, event start/end/location, task due/priority/points, restaurant hours/price/distance.

### 5.14 Author strip (`<AuthorStrip>`)
Avatar + name + role + action button. Single variant. Used: recipe author, vault entry creator, task creator, kudos giver.

### 5.15 Hero photo sheet (`<HeroPhotoSheet>`)
Edge-to-edge photo + frosted circular back/action buttons + sliding content sheet. Single variant. Used: recipe detail, restaurant detail, any image-led detail page.

---

## Extraction phase (MUST happen before Tier 6)

The design-system pages (`resources/js/views/design-system/pages/**/*.vue`) are **self-contained demos**, not shared source. Each page inlines its markup, hex constants (`const L = {...}, const D = {...}`), and sometimes CSS. Nothing in `resources/js/components/**` imports from them. Before view refactors can happen, every locked component must be **extracted** into a real reusable Vue component under `resources/js/components/design-system/`.

**Why this is a separate phase:**
- You can't refactor a view against a library that doesn't exist yet. Skipping this step and jumping to view refactors would mean copy-pasting markup from demo pages into views — the opposite of DRY.
- Extraction forces a clean prop API to be defined once, cleanly, instead of re-invented inside every view refactor.
- After extraction, a tweak in `resources/js/components/design-system/StatTile.vue` updates both the design-system page AND every view using it. That's the whole point.

**Process per component:**
1. Read the locked design-system page (e.g. `pages/feature/StatTilePage.vue`).
2. Extract the core component to `resources/js/components/design-system/<Name>.vue` with a clean prop API.
3. Refactor the design-system page to import the new component and render it with varied props — the page becomes a demo/documentation wrapper, not a markup source.
4. Verify both the design-system page and a quick render in an app context still look right (mobile + desktop, light + dark).
5. Commit.

**Source of truth:** `resources/js/views/design-system/registry.js` is the canonical list of what's locked. Use it to know which components to extract and in what order.

**Convention changes during extraction:**
- **Drop the inline hex constants.** The demo pages use `const L = {...}, const D = {...}` with inline `:style` bindings so the two-panel Light/Dark preview renders correctly regardless of the `html.dark` state on the root. Real components don't have this problem — they render inside the app's actual theme context — so they should use normal Tailwind `dark:` utilities and the CSS custom properties already defined in `resources/css/tokens.css` (`bg-surface-raised`, `text-ink-primary`, `dark:bg-surface-raised`, etc.).
- **Prop-driven, not data-driven.** Design-system pages often include arrays of example data baked into the page (`const CATEGORIES = [...]`, `const feedC = [...]`). The extracted component should accept single-item props (or an array prop at most) — not hardcode example data. Example data moves up into the design-system page that renders the component.

**Gotchas learned during scaffolding (bake these into every sub-agent prompt):**
1. In Vue `<template>`, refs are accessed WITHOUT `.value`. Only `<script setup>` code uses `.value`.
2. Vue event handlers do NOT support `if` statements — use a ternary or `cond && (expr)`. Example: `@click="!disabled && (active = key)"` not `@click="if (!disabled) active = key"`.
3. Do NOT set `:chosen="true"` on `VariantFrame` — that's Greg's locked-marker, not a "picked" marker. Use the `status="chosen"` prop on `ComponentPage` for the overall lock signal instead.
4. For hero numbers that need to scale to their container (not the viewport), use CSS container queries — `container-type: inline-size` on the card + `cqw` units in the `clamp()`. Viewport `vw` units break on narrow cards inside wide viewports (5.1 StatTile was the canary).
5. For text overlaying photos, use a flat translucent darken `rgba(0, 0, 0, 0.45)` across the whole card. Gradient scrims have unpredictable weak zones where bright photo highlights defeat white text (5.2 HeroMetricCard Variant C was the canary).
6. SVG clip-path hex tiles can't have dashed borders (the clip cuts the dashes into vertical slivers). Use fill differences to distinguish states instead.

**Batching approach (same pattern used for scaffolding):**
Sonnet sub-agents in parallel, 5 components per batch. Greg verifies each batch and commits before the next starts. Propose the prop API for the first component (1.1 Button) as the template, get approval, then roll.

Suggested batch order (extraction):
- Batch 1: primitives 1.1–1.5 (Button, Input, Chip, Avatar, Selection)
- Batch 2: primitives 1.6–1.7 + cards 2.1–2.3 (Progress, Skeleton, FlatCard, PhotoCard, GradientCard)
- Batch 3: navigation 3.1–3.4 (TopNav, BottomNav, Sidebar, UtilityRail)
- Batch 4: compounds 4.1–4.5 (TabPillGroup, SegmentedFilter, CategoryChipRow, ActionPair, QuickActions)
- Batch 5: compounds 4.6–4.10 (AvatarPicker, Modal/Sheet, Toast, EmptyState, FormGroup)
- Batch 6: feature 5.1–5.5 (StatTile, HeroMetricCard, EventTile, WeekStrip, MonthGrid)
- Batch 7: feature 5.6–5.10 (DayHeader, TimelineRow, AchievementTile, ActivityRow, AIActivityCard)
- Batch 8: feature 5.11–5.15 (StepCard, ReceiptCard, MetaTriplet, AuthorStrip, HeroPhotoSheet)

---

## Tier 6 — View-level integration (after extraction is complete)

Once the library is built AND every component has been extracted into `resources/js/components/design-system/`, a smaller model does view-by-view refactors. One view per session.

**Suggested order:**
1. Dashboard (highest impact, highest daily use)
2. Calendar (biggest UX pain point — mobile spacing issue)
3. Tasks
4. Points + Achievements (big gamification moment)
5. Food (recipes / restaurants / meal plan / shopping)
6. Vault
7. Chat / AI Assistant (now with Activity feed patterns)
8. Settings
9. Onboarding wizard
10. Auth (login / register)

Each view-refactor session:
- Inventories the current view's components
- Maps each to its library equivalent
- Rewrites the view using library components only
- Verifies in preview at mobile + desktop, light + dark
- Updates screenshots in `docs/screenshots/` if kept
- Commits

---

## Progress tracking

When a component is chosen and promoted, tick it here. Add commit hash.

### Step 0
- [ ] 0.1 Scaffold `/design-system` route

### Tier 0 — Foundation tokens
- [ ] 0.1 Color tokens
- [ ] 0.2 Typography scale
- [ ] 0.3 Spacing/radii/shadow/motion
- [ ] 0.4 Gradient tokens
- [ ] 0.5 Glass utilities

### Tier 1 — Primitives
- [ ] 1.1 Button
- [ ] 1.2 Input / Textarea / Search
- [ ] 1.3 Chip / Tag / Status pill (gained `customColor` escape-hatch 2026-04-27)
- [ ] 1.4 Avatar
- [ ] 1.5 Checkbox / Radio / Switch
- [ ] 1.6 Progress bar
- [ ] 1.7 Skeleton loader
- [x] 1.8 Select (added 2026-04-27)

### Tier 2 — Cards
- [ ] 2.1 FlatCard
- [ ] 2.2 PhotoCard
- [ ] 2.3 GradientCard

### Tier 3 — Navigation
- [ ] 3.1 Top nav
- [ ] 3.2 Bottom nav
- [ ] 3.3 Sidebar
- [ ] 3.4 Right utility rail

### Tier 4 — Compound patterns
- [ ] 4.1 TabPillGroup
- [ ] 4.2 SegmentedFilter
- [ ] 4.3 CategoryChipRow
- [ ] 4.4 ActionPair
- [ ] 4.5 QuickActions
- [ ] 4.6 AvatarPicker
- [ ] 4.7 Modal / Sheet / Drawer
- [ ] 4.8 Toast
- [ ] 4.9 EmptyState
- [ ] 4.10 FormGroup

### Tier 5 — Feature-specific
- [ ] 5.1 StatTile
- [ ] 5.2 HeroMetricCard
- [ ] 5.3 EventTile
- [ ] 5.4 WeekStrip
- [ ] 5.5 MonthGrid
- [ ] 5.6 DayHeader
- [ ] 5.7 TimelineRow
- [ ] 5.8 AchievementTile
- [ ] 5.9 ActivityRow
- [ ] 5.10 AIActivityCard
- [ ] 5.11 StepCard
- [ ] 5.12 ReceiptCard
- [ ] 5.13 MetaTriplet
- [ ] 5.14 AuthorStrip
- [ ] 5.15 HeroPhotoSheet

### Tier 6 — View integration
- [x] 6.0 App shell — Sidebar + TopBar + BottomNav (2026-04-26)
- [x] 6.1 Dashboard (2026-04-26)
- [x] 6.2 Calendar — Phase 1 + Phase 2 + Phase 3 (2026-04-27; KinMonthGrid pills, utility rail, KinDayHeader)
- [x] 6.3 Tasks — Phase 1 + Phase 2 + Phase 3 (2026-04-27; KinModalSheet + KinSelect + utility rail + card-wrapped rows)
- [x] 6.4 Points + Achievements (2026-04-27; KinAchievementTile, KinHeroMetricCard, KinSearch, modals → KinModalSheet)
- [x] 6.5 Food — Phase 1 (2026-04-27; FoodView shell + 4 tabs + 19 components token-swept + KinPhotoCard recipe/restaurant/meal-entry cards; RecipeForm/MealEntryPicker/MealPlanShoppingModal/MealWeekGrid deferred to Phase 2)
- [x] 6.6 Vault — Phase 1 (2026-04-27; categories/entries/entry views + Kin form/modal swaps; encryption + Milkdown content typography deferred)
- [x] 6.7 Chat — Phase 1 (2026-04-27; empty states + composer Send → KinButton; bespoke message bubbles deferred)
- [x] 6.8 Settings — Phase 1 (2026-04-27; ~340 token swaps + KinInput/KinSelect/KinSwitch swaps; BaseModal × 5 + ToggleSwitch dark-mode rows deferred)
- [x] 6.9 Onboarding — Phase 1 (2026-04-27; 8 steps refactored; KinGradientCard for FeaturesExplainerStep)
- [x] 6.10 Auth — Phase 1 (2026-04-27; LoginView + RegisterView wrapped in KinFlatCard with KinInput/KinButton/KinCheckbox)
