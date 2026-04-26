# Session Handoff

**Date:** 2026-04-26
**Branch:** `redesign/visual-overhaul`
**Last session work:** Tier 4 + Tier 5 component reviews + integration; both ⚠️ revisits cleared.

## What Was Done This Session

### Context-cost reduction (pre-redesign)
- **Disconnected unused claude.ai connectors** (Gmail, Google Calendar, GoDaddy/Domains) — they were riding in deferred-tools every turn for no use. Done in the Claude desktop app's Connectors panel.
- **Slimmed CLAUDE.md from 344 → 56 lines** (commit `b08cdb8`). Module deep-dives, DB schema, API routes, file layout, dev setup all moved to `docs/REFERENCE.md`. Aspirational features dropped (already in `docs/ROADMAP.md`).
- **Switched local DB from Postgres → SQLite** to unblock dev on Greg's Windows laptop without Docker. `.env.bak.pgsql` saved as a local backup; the live `.env` now uses sqlite + file cache/session/sync queue. Migrations + demo seed run cleanly.

### Design-system Tier 4 + Tier 5 integration (this is the bulk)
For each Kin component below, we walked through the demo page, fixed any issues Greg flagged, then **stripped the bespoke variants** so only the Kin demo remains (per the established workflow). Every page now stands at ~50–220 lines vs the original 400–1500.

**Tier 4 Compounds — all integrated:**
- ✅ 4.6 KinAvatarPicker
- ✅ 4.7 KinModalSheet
- ✅ 4.8 KinToast — **new component:** `KinToastStack` + `useToasts()` composable. Canonical placement is now top-right desktop / top-center mobile (was bottom-center in the docstring; updated). Auto-dismiss at 4.5s, max 3 visible, sliding stack with TransitionGroup.
- ✅ 4.9 KinEmptyState
- ✅ 4.10 KinFormGroup
- ✅ 4.2 KinSegmentedFilter — **fixed:** added a sliding ink-pill indicator that translates between options (was just a fade transition). Variant A's outlined-container look + Variant B's slide motion. Buttons stay fixed in place; an absolute-positioned indicator slides behind via `offsetLeft / offsetWidth` measurement + cubic-bezier transform.
- ✅ 4.4 KinActionPair — **fixed:** `layout="asymmetric"` now uses `flex: 1` (secondary) + `flex: 2` (primary) so the ghost button takes 1/3 and the filled primary takes 2/3 — matches the bespoke Variant B that Greg picked.

**Tier 5 Feature — all integrated:**
- ✅ 5.1 KinStatTile — **fixed:** hero font-size now scales by character count (60cqw at 1 char → 30cqw at 5+ chars) so "5" feels as big as "1,248". Range filter row no longer clips with `flex-shrink: 0` on the pill group.
- ✅ 5.2 KinHeroMetricCard — **major revision:** removed the glyph watermark (Greg said too busy), made the iridescent + warm gradients into 3-radial multi-color washes (was single-source), changed the photo-variant scrim from bottom-only to full-coverage darkening, added content-length-aware hero font sizing, switched layout from absolute-positioned content to in-flow `flex flex-col justify-end` so the article grows with content instead of clipping it on mobile.
- ✅ 5.3 KinEventTile — **fixed:** Kin demo now lays span pills into a real 7-column week grid with day headers + offset spacers (was just a flat list of full-width pills).
- ✅ 5.4 KinWeekStrip
- ✅ 5.5 KinMonthGrid
- ✅ 5.6 KinDayHeader
- ✅ 5.7 KinTimelineRow — **fixed:** demo now uses 7-column grid with absolute-positioned bars (was flat pills).
- ✅ 5.8 KinAchievementTile
- ✅ 5.9 KinActivityRow — **added:** `showActions` prop renders inline Nice (mint pill) + Reply (transparent bordered) buttons on social rows (kudos, badges). Mechanical events stay read-only.
- ✅ 5.10 KinAIActivityCard
- ✅ 5.11 KinStepCard
- ✅ 5.12 KinReceiptCard
- ✅ 5.13 KinMetaTriplet
- ✅ 5.14 KinAuthorStrip — **expanded:** demo now shows all 5 author types (Recipe, Vault, Task, Kudos, AI). Component bumped to 14/12px typography and the role/meta line uses the two-tone treatment (role in inkSecondary, meta in inkTertiary).
- ✅ 5.15 KinHeroPhotoSheet — **fixed:** added `w-full h-full` to the article root so the absolute children don't collapse. Demo redone as side-by-side phone-frame mocks (On mount @ 60% sheet vs After scroll @ 20%) with author strip + Start cooking CTA in the scrolled state.

### KinButton dark-mode contrast fix
- `.kin-btn--secondary` in dark mode now uses `surface-overlay` (#242220) bg + `border-strong` so it doesn't disappear into the page background. Was using `surface-raised` (#1C1B19) which blended into `surface-app` (#141311).

## Quality State

- **No console errors** observed across any of the integrated demo pages.
- All demo pages navigate cleanly via Vue Router.
- Vite + Laravel servers run via `.claude/launch.json` (`npm run dev` + `php artisan serve`). Both healthy.

## What's Next — Tier 6 (View-level integration)

Tier 6 is the actual app refactor. The component library is stable; now apply it to real views.

Suggested order (per `docs/design/COMPONENT_ROADMAP.md`):
1. **Dashboard** — biggest visual payoff. Uses HeroMetricCard, StatTile, ActivityRow, AIActivityCard, EventTile.
2. **Calendar** — DayHeader, WeekStrip, MonthGrid, EventTile, TimelineRow.
3. **Tasks** — TaskList row needs ActionPair + EventTile.
4. **Vault** — AuthorStrip on entries, EmptyState in empty categories, FormGroup in editor.
5. **Chat** — already partly there.
6. **Onboarding** — StepCard.

For each view, the workflow is:
1. Open the existing `views/<name>/*.vue`.
2. Identify which Kin* components map to current bespoke markup.
3. Replace bespoke → Kin component-by-component, keeping store/data wiring intact.
4. Verify in browser (logged-in via demo seed accounts).
5. Commit per-view: `refactor(<view>): use Kin* components`.

## Blockers or Gotchas

- **Local dev is on SQLite.** `php artisan migrate` + `db:seed` already ran. To switch back to Postgres, restore `.env.bak.pgsql`. Postgres is **not** running on this Windows laptop; SQLite is the practical choice for now.
- **Postgres preference for prod is unchanged** — production on Upsun still uses Postgres. SQLite is local-dev-only.
- **`.env.bak.pgsql` is gitignored-by-not-tracked-yet.** Don't commit it.
- **`CLAUDE.local.md`** is intentionally not tracked.
- **`KinToastStack` registers a shared `_toasts` ref at module scope.** Multiple consumers share the queue, which is what we want for app-wide toasts. Just be aware when writing tests.
- **Dark-mode visual bug to watch for:** any other component using `surface-raised` as resting bg + `border-subtle` border may have the same low-contrast issue we hit on KinButton secondary. Worth a sweep during Tier 6.

## Open Questions

- None outstanding. The component library is locked. Tier 6 begins with Dashboard.
