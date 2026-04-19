/**
 * Design System Component Registry
 *
 * Source of truth for the component library. Each entry becomes:
 *  - A sidebar nav item in /design-system
 *  - A routable slug (/design-system/:slug)
 *  - A dynamic import of the component's page file
 *
 * When a smaller-model session tackles a component, it creates the file at `pagePath`
 * and flips `scaffolded: true`. When the variant is chosen and promoted to a real
 * library component, it flips `chosen: true` and sets `chosenVariant`.
 *
 * Keep this file in sync with docs/design/COMPONENT_ROADMAP.md.
 */

export const TIERS = [
  { id: 'overview', label: 'Overview' },
  { id: 'tokens', label: 'Tier 0 — Tokens' },
  { id: 'primitives', label: 'Tier 1 — Primitives' },
  { id: 'cards', label: 'Tier 2 — Cards' },
  { id: 'navigation', label: 'Tier 3 — Navigation' },
  { id: 'compounds', label: 'Tier 4 — Compound patterns' },
  { id: 'feature', label: 'Tier 5 — Feature-specific' },
]

export const REGISTRY = [
  // Overview — the landing page
  { slug: 'overview', tier: 'overview', title: 'Overview', description: 'Library progress and how this works.', pagePath: './pages/OverviewPage.vue', scaffolded: true, chosen: false },

  // Tier 0 — Tokens (no variants; reference-only once defined)
  { slug: 'colors', tier: 'tokens', title: '0.1 Color tokens', description: 'Light + dark palettes, category accents, status colors.', pagePath: './pages/tokens/ColorsPage.vue', scaffolded: true, chosen: true },
  { slug: 'typography', tier: 'tokens', title: '0.2 Typography scale', description: 'Fluid type scale via clamp() — hero through caption.', pagePath: './pages/tokens/TypographyPage.vue', scaffolded: true, chosen: true },
  { slug: 'spacing', tier: 'tokens', title: '0.3 Spacing · radii · shadow · motion', description: 'Layout primitives and motion tokens.', pagePath: './pages/tokens/SpacingPage.vue', scaffolded: true, chosen: true },
  { slug: 'gradients', tier: 'tokens', title: '0.4 Gradient tokens', description: 'Iridescent card fills and ambient backgrounds.', pagePath: './pages/tokens/GradientsPage.vue', scaffolded: true, chosen: true },
  { slug: 'glass', tier: 'tokens', title: '0.5 Glass utilities', description: 'The four allowed glass surfaces.', pagePath: './pages/tokens/GlassPage.vue', scaffolded: true, chosen: true },

  // Tier 1 — Primitives
  { slug: 'button', tier: 'primitives', title: '1.1 Button', description: 'Lifted pill — gradient fill, resting shadow, 3px lift on hover, press-in on active.', pagePath: './pages/primitives/ButtonPage.vue', scaffolded: true, chosen: true },
  { slug: 'input', tier: 'primitives', title: '1.2 Input · Textarea · Search', description: 'Borderless inset fields + pill search. Dark inputs sit raised above the page for legibility.', pagePath: './pages/primitives/InputPage.vue', scaffolded: true, chosen: true },
  { slug: 'chip', tier: 'primitives', title: '1.3 Chip · Tag · Status pill', description: 'Outlined + soft-tint chip (categories / filters) + inline status indicator (dense rows).', pagePath: './pages/primitives/ChipPage.vue', scaffolded: true, chosen: true },
  { slug: 'avatar', tier: 'primitives', title: '1.4 Avatar', description: 'Clean circle default + arc progress overlay for achievement/progress state.', pagePath: './pages/primitives/AvatarPage.vue', scaffolded: true, chosen: true },
  { slug: 'selection', tier: 'primitives', title: '1.5 Checkbox · Radio · Switch', description: 'Minimal neutral fill for checkbox + radio; Apple-style pastel-accent toggle for switch.', pagePath: './pages/primitives/SelectionPage.vue', scaffolded: true, chosen: true },
  { slug: 'progress', tier: 'primitives', title: '1.6 Progress bar', description: 'Horizontal solid-fill bars + standalone arc gauge for hero moments.', pagePath: './pages/primitives/ProgressPage.vue', scaffolded: true, chosen: true },
  { slug: 'skeleton', tier: 'primitives', title: '1.7 Skeleton loader', description: 'Shimmer placeholders (text line, circle, pill, field, card block) with 1.8s linear shimmer and prefers-reduced-motion static fallback.', pagePath: './pages/primitives/SkeletonPage.vue', scaffolded: true, chosen: true },

  // Tier 2 — Cards
  { slug: 'flat-card', tier: 'cards', title: '2.1 FlatCard', description: 'Standard rounded-corner card with subtle border.', pagePath: './pages/cards/FlatCardPage.vue', scaffolded: true, chosen: true },
  { slug: 'photo-card', tier: 'cards', title: '2.2 PhotoCard', description: 'Edge-to-edge photo + gradient scrim (always on) + optional translucent chip badges.', pagePath: './pages/cards/PhotoCardPage.vue', scaffolded: true, chosen: true },
  { slug: 'gradient-card', tier: 'cards', title: '2.3 GradientCard', description: 'Radial gradient anchored upper-left + positioned glyph + diagonal content layout.', pagePath: './pages/cards/GradientCardPage.vue', scaffolded: true, chosen: true },

  // Tier 3 — Navigation
  { slug: 'top-nav', tier: 'navigation', title: '3.1 Top nav (desktop)', description: 'Glass pill nav: brand left + nav pills + search mid + utility right. Backdrop-blur surface.', pagePath: './pages/navigation/TopNavPage.vue', scaffolded: true, chosen: true },
  { slug: 'bottom-nav', tier: 'navigation', title: '3.2 Bottom nav (mobile)', description: 'Glass pill with 4 icons-only slots + elevated Ask-Assistant FAB.', pagePath: './pages/navigation/BottomNavPage.vue', scaffolded: true, chosen: true },
  { slug: 'sidebar', tier: 'navigation', title: '3.3 Sidebar', description: 'Accent-pill active item. Collapsible to 72px icons-only via bottom toggle.', pagePath: './pages/navigation/SidebarPage.vue', scaffolded: true, chosen: true },
  { slug: 'utility-rail', tier: 'navigation', title: '3.4 Right utility rail', description: 'Locked single treatment. Mini-month + filters + presence + saved views + actions. Collapses to mobile slide-up sheet.', pagePath: './pages/navigation/UtilityRailPage.vue', scaffolded: true, chosen: true },

  // Tier 4 — Compound patterns
  { slug: 'tab-pill-group', tier: 'compounds', title: '4.1 TabPillGroup', description: 'Filled active pill + outlined inactive. Used in recipe detail, vault sections, profile tabs.', pagePath: './pages/compounds/TabPillGroupPage.vue', scaffolded: true, chosen: true },
  { slug: 'segmented-filter', tier: 'compounds', title: '4.2 SegmentedFilter', description: 'Filled active inside an outlined container, with ease-out fill transition on selection.', pagePath: './pages/compounds/SegmentedFilterPage.vue', scaffolded: true, chosen: true },
  { slug: 'category-chip-row', tier: 'compounds', title: '4.3 CategoryChipRow', description: 'Hybrid layout — wrapped row on desktop, 4–5 visible chips + "+N more" overflow chip on mobile.', pagePath: './pages/compounds/CategoryChipRowPage.vue', scaffolded: true, chosen: true },
  { slug: 'action-pair', tier: 'compounds', title: '4.4 ActionPair', description: 'Equal-width [outline] [filled] is the primary pattern. Asymmetric [ghost] [filled] available as a variant for confident hierarchies.', pagePath: './pages/compounds/ActionPairPage.vue', scaffolded: true, chosen: true },
  { slug: 'quick-actions', tier: 'compounds', title: '4.5 QuickActions', description: 'Flat surface-raised squares with outline border + hover lift. 4-up desktop / 2-up mobile grid.', pagePath: './pages/compounds/QuickActionsPage.vue', scaffolded: true, chosen: true },
  { slug: 'avatar-picker', tier: 'compounds', title: '4.6 AvatarPicker', description: 'Pill-shaped cards (avatar + name inline in a rounded-full pill). Active fills accent-soft/bold.', pagePath: './pages/compounds/AvatarPickerPage.vue', scaffolded: true, chosen: true },
  { slug: 'modal-sheet', tier: 'compounds', title: '4.7 Modal · Sheet · Drawer', description: 'Responsive: iOS-style bottom sheet on mobile, centered modal on desktop. One direction per mode.', pagePath: './pages/compounds/ModalSheetPage.vue', scaffolded: true, chosen: true },
  { slug: 'toast', tier: 'compounds', title: '4.8 Toast', description: 'Bottom-center stack with semantic accent bar (success / info / warning / error). Max 3 visible.', pagePath: './pages/compounds/ToastPage.vue', scaffolded: true, chosen: true },
  { slug: 'empty-state', tier: 'compounds', title: '4.9 EmptyState', description: 'Centered hero — iridescent glyph + title + 1-sentence description + single pill CTA.', pagePath: './pages/compounds/EmptyStatePage.vue', scaffolded: true, chosen: true },
  { slug: 'form-group', tier: 'compounds', title: '4.10 FormGroup', description: 'Top label + input + helper below. Single canonical pattern across every form in the app.', pagePath: './pages/compounds/FormGroupPage.vue', scaffolded: true, chosen: true },

  // Tier 5 — Feature-specific
  { slug: 'stat-tile', tier: 'feature', title: '5.1 StatTile', description: 'Hero number (container-query scaled) + label + delta chip. Optional chart and optional time-range filter (D/W/M/Y) are opt-in props — same component covers dashboard, leaderboard, and analytics widgets.', pagePath: './pages/feature/StatTilePage.vue', scaffolded: true, chosen: true },
  { slug: 'hero-metric-card', tier: 'feature', title: '5.2 HeroMetricCard', description: 'All three variants ship, chosen by context — iridescent (calm summary), warm (urgency/streak), photo (with two-layer scrim for guaranteed legibility). Unified content-left / CTA-right desktop layout.', pagePath: './pages/feature/HeroMetricCardPage.vue', scaffolded: true, chosen: true },
  { slug: 'event-tile', tier: 'feature', title: '5.3 EventTile', description: 'Filled pastel tile is the primary shape (single-day events). Multi-day pill is a prop variant for spans. Handles source types (task/manual/Google/ICS) and visibility (visible/busy/private).', pagePath: './pages/feature/EventTilePage.vue', scaffolded: true, chosen: true },
  { slug: 'week-strip', tier: 'feature', title: '5.4 WeekStrip', description: 'Pill strip with single-letter day labels, event dots beneath each pill, and past-date deactivation (45% + cursor-not-allowed). Today anchors in lavender-soft; selected fills lavender-bold.', pagePath: './pages/feature/WeekStripPage.vue', scaffolded: true, chosen: true },
  { slug: 'month-grid', tier: 'feature', title: '5.5 MonthGrid', description: 'Month calendar with colored event dots.', pagePath: './pages/feature/MonthGridPage.vue', scaffolded: true, chosen: false },
  { slug: 'day-header', tier: 'feature', title: '5.6 DayHeader', description: 'Editorial-scale day number.', pagePath: './pages/feature/DayHeaderPage.vue', scaffolded: true, chosen: false },
  { slug: 'timeline-row', tier: 'feature', title: '5.7 TimelineRow', description: 'Pill bar spanning dates with avatars.', pagePath: './pages/feature/TimelineRowPage.vue', scaffolded: true, chosen: false },
  { slug: 'achievement-tile', tier: 'feature', title: '5.8 AchievementTile', description: 'Locked / in-progress / earned states.', pagePath: './pages/feature/AchievementTilePage.vue', scaffolded: true, chosen: false },
  { slug: 'activity-row', tier: 'feature', title: '5.9 ActivityRow', description: 'Feed row for points, tasks, history.', pagePath: './pages/feature/ActivityRowPage.vue', scaffolded: true, chosen: false },
  { slug: 'ai-activity-card', tier: 'feature', title: '5.10 AIActivityCard', description: 'Expandable AI run card with tool steps and preview.', pagePath: './pages/feature/AIActivityCardPage.vue', scaffolded: true, chosen: false },
  { slug: 'step-card', tier: 'feature', title: '5.11 StepCard', description: 'Numbered collapsible step — recipes, onboarding, playbooks.', pagePath: './pages/feature/StepCardPage.vue', scaffolded: true, chosen: false },
  { slug: 'receipt-card', tier: 'feature', title: '5.12 ReceiptCard', description: 'Icon-checklist card with inline actions.', pagePath: './pages/feature/ReceiptCardPage.vue', scaffolded: true, chosen: false },
  { slug: 'meta-triplet', tier: 'feature', title: '5.13 MetaTriplet', description: 'Icon + value × 3 row.', pagePath: './pages/feature/MetaTripletPage.vue', scaffolded: true, chosen: false },
  { slug: 'author-strip', tier: 'feature', title: '5.14 AuthorStrip', description: 'Avatar + name + role + action.', pagePath: './pages/feature/AuthorStripPage.vue', scaffolded: true, chosen: false },
  { slug: 'hero-photo-sheet', tier: 'feature', title: '5.15 HeroPhotoSheet', description: 'Edge-to-edge photo + sliding content sheet.', pagePath: './pages/feature/HeroPhotoSheetPage.vue', scaffolded: true, chosen: false },
]

export function findBySlug(slug) {
  return REGISTRY.find((entry) => entry.slug === slug) || null
}

export function groupByTier() {
  return TIERS.map((tier) => ({
    ...tier,
    entries: REGISTRY.filter((entry) => entry.tier === tier.id),
  }))
}

export function progressByTier() {
  return TIERS.filter((t) => t.id !== 'overview').map((tier) => {
    const entries = REGISTRY.filter((e) => e.tier === tier.id)
    const chosen = entries.filter((e) => e.chosen).length
    const scaffolded = entries.filter((e) => e.scaffolded && !e.chosen).length
    return { ...tier, total: entries.length, chosen, scaffolded, remaining: entries.length - chosen - scaffolded }
  })
}
