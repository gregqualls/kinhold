# Redesign Handoff — 2026-04-21

> Snapshot of the `redesign/visual-overhaul` branch as of commit `cc40f90`.
> Read this when you come back to pick up where we left off. See also:
> - [`COMPONENT_ROADMAP.md`](./COMPONENT_ROADMAP.md) — full build plan + batch order
> - [`REDESIGN_BRIEF.md`](./REDESIGN_BRIEF.md) — the locked design decisions

## Where we are

**Scaffolding phase:** complete (34 demo pages locked).
**Extraction phase:** 34 `Kin*` components extracted into `resources/js/components/design-system/`. Each is a real, reusable Vue SFC.
**Integration phase:** complete — all Kin previews approved and bespoke demos stripped (commit `cc4a0a0`).
**Tier 6 (view refactors):** in progress.
- **6.0 App shell done (2026-04-26)** — Sidebar onto `KinSidebar`, TopBar avatars onto `KinAvatar`. BottomNav stays bespoke (5-slot grouped pattern doesn't map onto `KinBottomNav`'s 4+FAB convention); fragment-root warning fixed.
- **6.1 Dashboard done (2026-04-26).**
- **6.2 Calendar Phase 1 done (2026-04-26)** — KinTabPillGroup view-mode tabs, KinButton actions, full token restyle on month grid + TimeGrid.
- **6.3 Tasks Phase 1 done (2026-04-26)** — KinModalSheet for tag manager, KinInput/KinTextarea/KinSwitch/KinSegmentedFilter/KinButton inside TaskDetailPanel, full token restyle.
- **Phase 2 done (2026-04-27)** — added `KinSelect` to the library (1.8); added `customColor` escape-hatch to `KinChip`; migrated calendar month view to `KinMonthGrid` (dots density, source→accent mapping); swapped `TaskDetailPanel` to `KinModalSheet`; replaced TaskDetailPanel `<select>` fields with `KinSelect`; tag filter chips and calendar legend now use `KinChip` with `customColor`.
- **Phase 3 done (2026-04-27)** — Calendar + Tasks gained the right utility rail (`KinUtilityRail`, desktop ≥`lg`). Calendar Day view now uses `KinDayHeader` size="md" as the centerpiece (editorial-scale day number, TODAY badge, event count). Calendar source filters live in the rail with toggle state piped through `passesSourceFilter`. Tasks tag filter chips moved into the rail (mobile keeps the horizontal strip).
- **6.4 Points + Achievements done (2026-04-27)** — BadgesView grid now uses `KinAchievementTile` directly with state/progress/accent adapters; PointsFeedView + PointsHistoryView use `KinHeroMetricCard` for balance + `KinFlatCard` for sub-sections; RewardsView uses `KinSearch` + `KinChip` filters + `KinSelect` sort; all 3 point modals → `KinModalSheet`; KudosInput → KinSelect+KinInput+KinButton. Deferred: LeaderboardStrip podium, RewardCard auction logic, RewardForm branching form (Phase 2 of 6.4).
- **6.5 Food Phase 1 done (2026-04-27)** — biggest tier (~6,000 LOC). FoodView tab shell → `KinTabPillGroup` underline; each tab (Recipes / Restaurants / Meals / Shopping) refactored with `KinSearch` + `KinChip` filters + `KinButton` actions + `KinEmptyState`; CookLogEntry / RecipeImportModal modals → `KinModalSheet`; ListHeader gets `KinSelect`. Full token sweep across all 19 child components. RecipeCard + Restaurant card + MealEntryCard now use `KinPhotoCard` with per-type gradient fallback. Deferred to Phase 2: RecipeForm (489 LOC), MealEntryPicker (350), MealPlanShoppingModal (344), MealWeekGrid (281).
- **6.6–6.10 Phase 1 done (2026-04-27)** — Vault, Chat, Settings, Onboarding, Auth all refactored in parallel. ~6,300 LOC across 5 view areas. Common deferrals: `BaseModal`/`BaseButton`/`BaseInput` project wrappers (need their own coordinated pass — they're used everywhere), `class="card-lg"` global utility, vault category icon palette, Settings ToggleSwitch dark-mode rows (need thumb slot on KinSwitch or design call).

**Tier 6 is now feature-complete** for the visual overhaul. Phase 2 follow-ups: wrapper components (`BaseModal` / `BaseButton` / `BaseInput`), deferred heavyweights (RecipeForm, MealEntryPicker, MealPlanShoppingModal, MealWeekGrid, LeaderboardStrip, RewardCard, RewardForm, TaskDetailPanel SlidePanel→ModalSheet), and `class="card-lg"` global utility.

- **Dashboard widgets done (2026-04-27)** — final pass. All 11 widgets (WelcomeWidget, PointsSummaryWidget, BadgesWidget, ActivityFeedWidget, FamilyTasksWidget, MyTasksWidget, FilteredTasksWidget, LeaderboardWidget, TodaysScheduleWidget, QuickActionsWidget, RewardsWidget) refactored to match Kin aesthetic. Notable upgrades: PointsSummaryWidget now a small KinHeroMetricCard-style hero, QuickActionsWidget tiles upgraded to proper Kin cards with lavender icon circles, task widgets gained hairline row separators matching the Tasks-view rhythm. BadgesWidget kept `BadgeIcon` (KinAchievementTile too big for dashboard grids).

### Pipeline status

- ✅ Tokens (0.1–0.5) — unchanged, still locked
- ✅ Primitives (1.1–1.7) — Kin components built, demos wired to use them
- ✅ Cards (2.1–2.3) — Kin components built, demos wired
- ✅ Navigation (3.1–3.4) — Kin components built, demos wired
- 🟡 Compounds (4.1–4.10) — Kin components built. Demos in mixed state (see below)
- 🟡 Feature (5.1–5.15) — Kin components built. Demos in mixed state (see below)

## The workflow we evolved to

After a bad experience in Batch 2 (sub-agents rewrote your layouts destructively), we landed on a safer flow for every component:

1. **Build the `Kin<Name>.vue`** based on the locked demo.
2. **Append a "Kin" preview section at the bottom of the demo page** — your bespoke demo stays intact above it.
3. **You review** side-by-side in the preview at `/design-system/<slug>`.
4. **On approval**, the bespoke markup above gets removed, leaving only the Kin-based demo.

The component files are committed; the "Kin" preview sections are committed; no bespoke demo has been removed yet (except for Batches 1–3 which predated this workflow — see notes on those).

## Per-component status

Legend:
- ✅ **Approved** — Kin version is the only demo on the page.
- 🟡 **Preview appended** — Kin preview is below the bespoke demo; awaiting your review.
- ⚠️ **In review (needs revisit)** — Kin component exists but you flagged it as wrong and the demo was reverted.

### Batch 1 — Primitives ×5

Status: ✅ pre-workflow wiring (bespoke demos replaced). Visual polish was confirmed at the time but they haven't been reviewed against the Kin-preview-alongside pattern.

| # | Component | Status |
|---|---|---|
| 1.1 | KinButton | ✅ wired |
| 1.2 | KinInput / KinTextarea / KinSearch | ✅ wired |
| 1.3 | KinChip | ✅ wired |
| 1.4 | KinAvatar | ✅ wired |
| 1.5 | KinCheckbox / KinRadio / KinSwitch | ✅ wired |

### Batch 2 — Primitives + Cards ×5

| # | Component | Status |
|---|---|---|
| 1.6 | KinProgressBar / KinProgressArc | ✅ wired |
| 1.7 | KinSkeleton | ✅ wired (added `--skeleton-base` + `--skeleton-shimmer` tokens) |
| 2.1 | KinFlatCard | ✅ wired (dark-mode hover uses brightness boost) |
| 2.2 | KinPhotoCard | ✅ wired (gradient scrim preserved, `tall` aspect added) |
| 2.3 | KinGradientCard | ✅ wired (toned down: quiet tint + small top-right glyph, not watermark) |

### Batch 3 — Navigation ×4

| # | Component | Status |
|---|---|---|
| 3.1 | KinTopNav | ✅ wired |
| 3.2 | KinBottomNav | ✅ wired |
| 3.3 | KinSidebar | ✅ wired |
| 3.4 | KinUtilityRail | ✅ wired |

### Batch 4 — Compounds ×5

| # | Component | Status |
|---|---|---|
| 4.1 | KinTabPillGroup | ✅ wired (3 variants via prop) |
| 4.2 | KinSegmentedFilter | ⚠️ **In review** — demo reverted to bespoke. You said it didn't look right. Needs revisit. |
| 4.3 | KinCategoryChipRow | ✅ wired (3 overflow modes) |
| 4.4 | KinActionPair | ⚠️ **In review** — demo reverted to bespoke. You said it didn't look right. Needs revisit. |
| 4.5 | KinQuickActions | ✅ wired |

### Batch 5 — Compounds ×5

All have Kin previews appended below your bespoke demos. Awaiting review.

| # | Component | Status |
|---|---|---|
| 4.6 | KinAvatarPicker | 🟡 preview appended |
| 4.7 | KinModalSheet | 🟡 preview appended |
| 4.8 | KinToast | 🟡 preview appended |
| 4.9 | KinEmptyState | 🟡 preview appended |
| 4.10 | KinFormGroup | 🟡 preview appended |

### Batch 6 — Feature ×5

| # | Component | Status |
|---|---|---|
| 5.1 | KinStatTile | 🟡 preview appended (container-query scaled hero number) |
| 5.2 | KinHeroMetricCard | 🟡 preview appended (iridescent / warm / photo variants) |
| 5.3 | KinEventTile | 🟡 preview appended (block + span variants; source/visibility/avatars) |
| 5.4 | KinWeekStrip | 🟡 preview appended |
| 5.5 | KinMonthGrid | 🟡 preview appended (dots + pills density) |

### Batch 7 — Feature ×5

| # | Component | Status |
|---|---|---|
| 5.6 | KinDayHeader | 🟡 preview appended |
| 5.7 | KinTimelineRow | 🟡 preview appended (pill only; parent handles grid) |
| 5.8 | KinAchievementTile | 🟡 preview appended (4 states × 4 accents × hex polygon) |
| 5.9 | KinActivityRow | 🟡 preview appended (conversational variant) |
| 5.10 | KinAIActivityCard | 🟡 preview appended (expandable; v-model:expanded) |

### Batch 8 — Feature ×5

| # | Component | Status |
|---|---|---|
| 5.11 | KinStepCard | 🟡 preview appended (done/active/default + connector) |
| 5.12 | KinReceiptCard | 🟡 preview appended (header + rows + action) |
| 5.13 | KinMetaTriplet | 🟡 preview appended |
| 5.14 | KinAuthorStrip | 🟡 preview appended (AI mode for sparkle avatar) |
| 5.15 | KinHeroPhotoSheet | 🟡 preview appended (reactive `sheetTop` for parallax) |

## What to do when you come back

### 1. Review the 🟡 pages

Walk through each page in `/design-system/` with a Kin preview. If it matches your intent:

- Tell Claude: "approve 4.6" (or 5.1 etc.) → the bespoke demo above gets removed, leaving only the Kin version.

If it doesn't match:

- Tell Claude what's wrong; Claude adjusts the `Kin<Name>.vue` and updates the preview without touching your bespoke demo.

### 2. Revisit 4.2 and 4.4

Both have `status="scaffolded"` and are marked "In review" in the registry. They need another pass. The Kin components exist (`KinSegmentedFilter.vue`, `KinActionPair.vue`) but you weren't happy with the demo rendering.

Specific notes I remember:
- **4.2 SegmentedFilter** — transitions not visible (you have OS reduced-motion on; needs `forceMotion` demo prop) + visual differences I couldn't fully diagnose before reverting.
- **4.4 ActionPair** — layout spacing on Variant B (asymmetric) and the mobile stack in Variant C didn't read right; the asymmetric direction was fixed (justify-between) but you hadn't verified.

### 3. Sanity-check Batches 1–3

Those were wired up before the preview-alongside workflow, so the bespoke demos are already gone. If anything feels off from the original locked design, we can:

- Revert the demo page from `git` (committed originals are at `b657a79`).
- Re-apply the preview-alongside workflow for those too.

No rush on this — they've been rendering fine, just flagged as "not reviewed under the current discipline."

### 4. Tier 6 — view-level integration

Not started. Once the library is fully approved, we start refactoring real app views (Dashboard → Calendar → Tasks → …) to use `Kin*` components. See `COMPONENT_ROADMAP.md` → "Tier 6 — View-level integration" for the suggested view order.

## Key commits to know

- `cc40f90` — this extraction work (34 components + demo wiring)
- `b657a79` — the last pre-extraction commit (all bespoke demos locked)
- Anything between these two is reachable via `git log redesign/visual-overhaul`

## Caveats / loose ends

- **`tokens.css`** changed: `html.dark` → `.dark` and `:root` → `:root, .light`. Enables nested theme scopes (which the preview two-panel trick would need). No production behavior change.
- **`DesignSystemView.vue`** force-removes `html.dark` on mount so the `/design-system` route always renders light at the root. User's global dark-mode pref is untouched when they leave the route.
- **Some Kin components have `forceMotion` props** (KinSkeleton, KinSegmentedFilter) — these exist only so demos animate even when the viewer has OS reduced-motion enabled. Real consumer code should never set this.
- **LF → CRLF line-ending warnings** on every touched file (you're on Windows). Harmless; git's autocrlf is handling it.

## If the preview server breaks

- `php artisan serve` (Laravel) + `npm run dev` (Vite) are the two.
- If Vite goes stale (high CPU, dark mode CSS acts weird), kill and restart.
- The `/design-system` route is always open on localhost — no auth gate in dev.

---

**TL;DR:** 34 components extracted. 25 pages have a "Kin" preview at the bottom awaiting your thumbs-up. 2 pages (4.2, 4.4) need another pass. When you approve a page, the bespoke demo above gets removed and the Kin version becomes the only demo.
