## Pre-PR check (2026-10-25)

A whole-branch pass before opening the PR: clean gate on HEAD, a high-effort review of `main...HEAD`, a sweep against the house rules, and a browser walkthrough of every touched screen in light and dark mode (dark had not been checked on this branch before).

**Fixed:**

- *Settings rail wiped Vue Router's history state.* `selectSection` called `history.replaceState(null, ...)`; after picking a section, navigating away and pressing Back landed on `/settings` without the hash. Now goes through `router.replace` with `path` and `query` passed explicitly, because a hash-only location drops `?gdpr=1`, which is what keeps paywalled users on export and delete only.
- *Settings sections could render after their gate closed.* The master-detail change had dropped the per-section `v-if` gates (Tasks & Points, Allergens, Billing). Sections now render from a validated `activeSection`, and a watcher moves off a section that becomes hidden and follows a deep link that becomes valid late.
- *Vault masked values chose their font from the hidden value.* Masked text is always mono again; the mono/body heuristic only applies once revealed.
- *Tag-colored text was under WCAG AA.* New `.kin-tag-ink` utility, used by TaskItem tags and KinChip soft-tone custom-color chips. Measured minimum after: 5.4:1 light, 7.5:1 dark.
- *Toggle chips and select cards had no selected state for screen readers.* KinChip filter buttons and KinSelectCard set `aria-pressed`.
- *Points history* no longer drops transactions without a timestamp, and dates from another year show the year.
- MealEntryPicker recipe thumbnail gets `alt=""`. Comments that narrated branch history or used em dashes were rewritten.

**Follow-ups found (pre-existing on main), fixed on `fix/ds-followups`:**

- *KinSelect chevron floated past a narrowed select.* A caller's class went on the inner `<select>` while the chevron sat against the full-width wrapper. Class and style now go on the root; `w-full` applies only when the caller sets no width. This also makes `w-32` / `w-40` take effect for the first time (before, `w-full` silently won), so the Recipes sort and vault Category selects get `flex-shrink-0`.
- *Vault sheets passed `size="xl"`*, which the KinModalSheet validator rejects. Now `lg`.
- *Solid custom-color chips put white text on pale fills* (amber School was about 2:1). The label takes white or ink by contrast; mid-tones where neither reaches 4.5:1 (violet Shopping) get a fill darkened 20% under white. Seeded tags now measure 4.52 to 7.92.
- *A removable KinChip nested its X `<button>` inside the chip `<button>`.* The body is now a `<span>` and the X is the only control.

**Still open (pre-existing, need a call from Greg):**

- The vault Category select shows blank: its first option is `{ value: null, label: 'Select...' }`, and an `<option>` with a `null` value falls back to its text as its value, so a `null` model matches nothing. Depends on whether "no category" is a valid choice.
- KinModalSheet size presets are `max-width` caps on a `md:w-auto` surface, so desktop sheets are content-width and `lg` versus `md` barely changes anything. Making presets real widths touches about 15 modals.

## Polish pass, part 6 (2026-10-25, same branch, still unmerged)

Twelve commits (`8ef1e09` → piece 5) finishing the deferral list. The handshake named six pieces; all six landed. The KinChip API extension (piece 4) got a high-effort code-review that found one visible regression, fixed before commit, plus seven lower-severity notes documented below.

**Landed:**

1. **RewardsView adopts `KinUtilityRail`** (`8ef1e09`), first new rail consumer since part 1. Desktop 280px right rail (search + filter chips + sort in `#filters`, Add Reward in `#actions`); mobile keeps inline toolbar + FAB. Same `flex lg:h-full` shape Points feed and Tasks already use.
2. **BadgesView award modal** (`aa9e9c3`), the always-visible Manually Award Badge card moves to a `KinModalSheet` triggered by a new "Award Badge" secondary button in the hero. ~30 lines of dead chrome removed.
3. **Chip-storm long tail** (`e3c1180`, `2c05fc1`, `c8fc385`, `87dd842`, `28caa6a`, `268790f`), six commits routing hand-rolled pills through `KinChip` or `KinButton`. Includes two token-drift fixes: ShoppingListItem and RecipeIngredientPicker swap hard-coded Tailwind amber (`bg-amber-100 text-amber-700`) for the DS peach accent; RecipeDetail Total time stays hand-rolled per the brand-gold island rule but trades `#C4975A` for the `bg-brand-gold` token.
4. **Lint cleanup** (`f303bbe`), unused `MagnifyingGlassIcon` import + dead `monthEventsMap` / `monthEventLabels` computeds removed. Clears the pre-existing warnings called out in HANDOFF part 1.
5. **`KinChip` gold accent + soft/bold activeTone** (`1248fe1`), the DS primitive extension that unlocks the brand-gold filter sites. See CHANGELOG for the API shape. Three sites route through the new accent + sub-variants: Settings meal-slots (soft), MealEntryPicker cook picker (soft), ListHeader shopping windows (bold toggle grammar) + Pre-Shop (soft mint).
6. **Soft-tint tag chips** (piece 5), FilteredTasksWidget dashboard filter display, RecipesTab inline tag chips, RestaurantsTab inline tag chips. All three use `KinChip filter active-tone="soft" :custom-color="tag.color" :interactive="false" :active="true"`, which gives the "soft tint + colored text" look the hand-rolls had and matches the TaskItem inline-tag treatment from part 4.

**Code-review findings on piece 4, fixed before commit:**

- *Shopping window inactive flips neutral → soft gold* (correctness). Original hand-rolled toggle grammar had one gold-filled pill against three neutral siblings; routing through `active-tone="solid"` would have made all four pills gold-family. Fixed by adding `active-tone="bold"` to KinChip and routing shopping windows through it.

**Code-review findings on piece 4, kept, worth knowing:**

- *Soft + neutral + active low contrast in dark mode* (correctness, hypothetical). No current call site exercises `color="neutral" active-tone="soft" :active="true"`. If one is added, dark mode may show the active state as a near-invisible wash. Fix would be a brighter alpha on `bg-ink-primary` or a dedicated neutral-active style.
- *Soft + customColor + active returns bare `'border'` class* (correctness, tight coupling). The chip relies on `chipCustomStyle`'s inline border-color to render. If the inline style ever stops winning the cascade, these chips lose their color.
- *Pre-Shop swaps `status-success` for `accent-mint` tokens* (correctness, token drift). Visually similar today; if the two token families diverge in a future palette iteration, Pre-Shop's on-state would drift with mint rather than with success.
- *Soft-active accent duplicates filter-inactive class string* (simplification). Both branches of `chipColorClasses` return the same `${softBg} border ${border} ${boldText}` for the accent-color case. A later change to one branch has to be mirrored to the other.
- *Food filter sites lose `hover:border-brand-gold/30` hover preview* (correctness, UX drift). The old hand-rolled hover was a border-color tint; the new one is `kin-pressable`'s uniform `brightness-95`. Universal to all three food filter sites.
- *Pre-Shop inactive gains a visible border* (correctness, UI drift). The hand-rolled toggle was borderless when off; KinChip inactive filter always carries `border border-border-subtle`.
- *`active-tone` silently ignored by `variant="status"`* (conventions). CLAUDE.md's "define errors out of existence" rule, a validator or dev warning would catch misuse of the prop on the wrong variant.

**Closed by decision, not changing code, with reasons:**

- **`KinUtilityRail` on Vault entries.** Just search + Add; a rail would be sparse. The entries list is thin enough that the inline toolbar is the right shape.
- **`KinUtilityRail` on Food and Shopping.** Both are tabs inside FoodView's `h-full flex-col overflow-hidden` shell; a full-height rail doesn't fit the tab model. If they ever grow out of being tabs, revisit.
- **`AllergenBadge` + `AllergenBadgeRow`.** The dashed-border "unconfirmed" state for AI-tagged allergens has no `KinChip` equivalent. The badge is also semantically tied to the allergen concept (contains / may_contain). Keep it as a dedicated primitive.
- **FoodCard photo-overlay tags.** The `backdrop-blur-sm` + `bg-white/25 text-white` treatment only makes sense when the chip sits over an image. KinChip can't model this without an opt-in overlay mode that would only serve this one call site.
- **FeaturedEventsSection countdown badge.** The "today!" state carries `animate-bounce-gentle` and uses the sand accent for a specific calendar-countdown semantic. KinChip would drop both.

**Running tally of DS additions on this branch:** `KinChip` interactive + showDot + gold + activeTone (soft, bold), `KinSelectCard`, `KinPageHero`, `KinKudoStack`, `SettingsSection collapsible`, `SettingsNavRail`. Six new primitives, two prop-level extensions.

**Where decisions live:** this handoff is the ledger. `CHANGELOG.md` has the per-part account. `REDESIGN_BRIEF.md`'s locked tenets 4 (pill shape), 5 (hero numerals), 6 (gradients) stay preserved.

---

## Polish pass, part 5 (2026-10-25, same branch, still unmerged)

Seven commits (`0c869c1` → `8ae7a86`) finishing the small-and-medium deferral items from part 4. Shipped per piece, gate green, review clean, in-browser verified where visible. Branch is still `polish/honour-the-system`, unmerged.

**Landed:**

1. **`KinKudoStack` primitive** (`7399851`), three variants (`action`, `stacked`, `count`) encapsulate FeedItem's hand-rolled kudo widgets. 30 lines of markup → 15 call-site lines; two Heroicon imports move into the primitive.
2. **Vault Documents + Leaderboard empty states** (`0c869c1`), route through `KinEmptyState` (lavender paperclip for Vault, sun-accent trophy for the leaderboard). "Rewards auctions" from HANDOFF part 4's note turned out to be RewardCard's inline "No bids yet" in a 192px sidebar, too small for the primitive, left as a plain label.
3. **Settings module-access pills** (`3825cff`), 8 identical buttons across two sections (Tasks & Points, Feature Access) become `KinChip variant="filter"`. Visible change: active "Off" state loses its red fill for uniform lavender; the four options read as peers now.
4. **Settings MCP Connection status + PointsSummaryWidget +/-N pill** (`3825cff`), route through `KinChip status`.
5. **Settings Family Members Parent/Child/Managed badges + WidgetPickerModal tag filter** (`804115d`), `KinChip category` with lavender/neutral/peach for the former, `KinChip filter` with `customColor` for the latter.
6. **FeaturedEventsSection recurrence + PreShopChecklist counts** (`9d1b2fb`), `KinChip category` and `status`.
7. **RewardForm member picker + MealDaySection meal count** (`76cfa1d`), RewardForm drops the label-wraps-hidden-checkbox pattern (and pre-DS wisteria colors) for `KinChip filter`; MealDaySection trades hard-coded hex colors for the DS neutral token.
8. **Dead `TaskPointsBadge.vue` deleted** (`9d1b2fb`), no callers anywhere; only mentioned in CHANGELOG.
9. **BadgesWidget loading state** (`8ae7a86`), the "picked by eye" 40px skeleton becomes per-cell icon-shaped circles plus caption bars, following the same `gridClass` + `maxDisplay` as the real content. No layout jump on resolve.

**Stopped at (next session's starting point):**

- **`KinUtilityRail` cross-screen promotion**, Vault, Rewards, Food, Shopping, Settings, Achievements each need a per-screen call on what fills the right rail. Settings grew its own left-rail from part 4 (master-detail layout), which doesn't change this question for the other six. This is the biggest remaining piece and needs a design conversation per screen.
- **Three brand-gold filter chrome sites, consolidated for a future piece:**
  - `Settings` Visible Meal Slots filter (meal planner slot selection)
  - `ListHeader` shopping-window pills (All / This Week / etc.)
  - `MealEntryPicker` Cook assignment buttons
  All three use the same `bg-brand-gold/10 border-brand-gold/40 text-brand-gold` active state and the same soft-tint-plus-colored-text treatment TaskItem settled on in part 4. Routing through `KinChip filter customColor` would flip them to filled-bg active state, a visible regression. The right fix is either (a) a new `KinChip filter` sub-variant that mirrors the TaskItem-style soft-tint active treatment, or (b) accept these as a deliberate "food/meal-planner uses brand-gold" visual island and leave them. Needs a call.
- **`FilteredTasksWidget` tag pills**, the dashboard's active-filter chrome (soft tinted bg + same-color text). Same argument as above; the look doesn't map onto any current `KinChip` variant. If sub-variant (a) above lands, this one can migrate too.

**Known limitations after this part:**

- `RewardForm` member picker now reads as "name, button" to screen readers rather than "name, checkbox, checked/unchecked". The toggle state is visible via the chip's active fill. If a11y feedback flags this, add an optional `aria-pressed` prop to `KinChip` variant="filter" and set it from `active`.
- `BadgesWidget` loading state now renders `maxDisplay` skeleton cells (24 at sm, 36 at md). More flashing on load than the previous 12, but matches the real cell count so the shape of "what's coming" is honest. Pull back to 12 if the loading state feels busy.
- `WidgetPickerModal` tag filter chips now render inactive tags with a small color dot (KinChip filter customColor grammar) where the previous hand-roll was outlined with no dot. Minor DS-consistency win over the previous spelling.

**Where decisions live:** this handoff is the ledger. `CHANGELOG.md` has the per-part account. `REDESIGN_BRIEF.md`'s locked tenets 4 (pill shape), 5 (hero numerals), 6 (gradients) stay preserved.

---

## Polish pass, part 4 (2026-10-25, same branch, still unmerged)

Three commits (`3a2818f` → `882bcd0`) finishing the design-call items part 3 could only knock the small items off of. Each one shipped alone and settled before the next started, gate green, review clean, verified in-browser. Branch is still `polish/honour-the-system`, still unmerged.

**Landed:**

1. **Settings master-detail layout** (`3a2818f`), Rail (sticky at md+) plus one-section-at-a-time detail replaces the 11-section accordion. New `SettingsNavRail` primitive. `SettingsSection` gains a `collapsible: false` mode. Hash routing with `replaceState` and a `watch(route.hash)` so Vue Router's component-reuse-on-hash-change still updates the detail pane. Mobile pushes into detail with a back arrow. Desktop lands on first visible section. GDPR mode honored. See CHANGELOG for the full account.
2. **Dashboard cards sized to content** (`846935d`), `widgetRegistry.heights` map replaced by a `cardless: true` flag on Welcome and Countdown. Every other widget sizes to its own content; the carded wrapper is a plain `KinFlatCard` with no inline height. Eight widgets lost their `h-full / flex-1 min-h-0 / overflow-y-auto` scaffolding (every widget self-caps via `taskLimit` / `slice`). Grid gets `items-start` so a short card next to a tall one doesn't stretch. 11 files, 55 added / 85 removed.
3. **TaskItem meta row inline with middots** (`882bcd0`), Six-chip meta row collapsed to `Chores · Open · 10pt · Every Tuesday`. Tags keep their color as text, due date and recurrence keep semantic shades, assignee is first name only (avatar stays in the detail panel), icons dropped (names carry the meaning), description indicator dropped (row-click opens the panel). Six unused imports pruned.

**Stopped at (next session's starting point):**

- **`KinUtilityRail` cross-screen promotion**, Vault, Rewards, Food, Shopping, Settings, Achievements each need a per-screen call on what fills the right rail (recent activity, quick filters, kin cards). Settings now has its own left rail from piece 1, that's a different animal and doesn't change this question.
- **`KinEmptyState` adoption on remaining sites**, Points right rail, Vault entry Documents section, Rewards auctions empty state. Each needs illustration + CTA copy decisions.
- **`KinKudoStack` primitive**, extracts FeedItem's stack button + stacked / count-only badges. New primitive from scratch; the hand-rolls work fine until there's a second consumer.
- **Broader `rounded-full` sweep**, Dashboard widgets, calendar legends, settings. Carried from parts 1-3.
- **Dashboard widget skeleton heights**, BadgesWidget's loading skeletons went from `100%` (filling the old fixed-height parent) to a fixed `40px`. That's a sensible number but was picked by eye, not measured against the real badge icon size. If skeletons feel off during a slow-load observation, revisit.

**Known limitations after this part:**

- `data-selected` on `SettingsNavRail` rows is used in place of `aria-selected` since the nav items are plain `<button>`s in a `<nav>` rather than a listbox. `aria-current="true"` is set for the selected item, which is the right ARIA for navigation. OK as-is.
- Settings form state is lost on section switch (the section's `<form>` unmounts via `v-if`). Matches the iOS Settings pattern, if this surfaces as a complaint, swap `v-if` for `v-show` in the detail area (performance cost: all 11 sections mounted at once; could be substantial for AI & Integrations which fetches MCP tokens).
- Dashboard: `widgetHeight` and `getWidgetHeight` were the API consumers for the `heights` map. Both are removed. If a future custom widget wants a fixed card height, the registry would need `maxHeight` reintroduced plus a per-widget `max-h-[Xpx] overflow-y-auto` wrapper in DashboardWidget. Not needed today since every widget self-caps.
- TaskItem: tags render as inline colored text. *Corrected in the pre-PR pass:* the claim that the tag colors all passed contrast was wrong. Measured, raw tag colors ran 2.15 to 3.77:1 in light mode and 3.5 to 4.0:1 in dark (prussian 1.06:1 in dark), under the 4.5:1 AA bar for 12px text. The pre-PR pass added `.kin-tag-ink`, which mixes the tag color toward ink per theme; see the pre-PR section above.

**Where decisions live:** this handoff is the ledger. `CHANGELOG.md` has the per-part account. `REDESIGN_BRIEF.md`'s locked tenets 4 (pill shape), 5 (hero numerals), 6 (gradients) stay preserved.

---

## Polish pass, part 3 (2026-10-24, same branch, still unmerged)

Twelve commits (`dae826f` → `ccfa1ff`) picking up the audit's remaining design-call items. The deferral list from part 2 ("Stopped at", "Still deferred") drove the plan. Everything landed on `polish/honour-the-system`; nothing pushed, nothing merged.

**DS primitives + extensions (first, so screens could route through them):**

1. **`KinChip` `interactive` prop.** A passive label renders as `<span>` instead of `<button>`, dropping its tab stop. TaskItem's tag + recurring chips lose the `tabindex="-1"` hack, the first task row went from 5 tab stops to 3.
2. **`KinChip` `showDot` + status-tint grammar.** Status variant can hide the leading dot and apply a soft-tinted bg + bold text in the status colour, via a static `STATUS_LABEL_CLASSES` map so Tailwind JIT picks up `bg-status-{token}/10 text-status-{token}`. This unblocked the state-badge sites (RewardCard, FeedItem) part 2 stopped at.
3. **`KinSelectCard` primitive.** New wrapper for the `.kin-pressable .kin-tile` tile pattern. Props `selected`, `showCheck`, `padding` (sm/md), `align` (start/center). Four Settings sites (AI mode × 2, AI provider, color theme) each shrunk from ~15 lines to 7-10.
4. **`KinPageHero` primitive.** Shared page header with optional back arrow + eyebrow + subtitle + right-aligned action slot. Default type scale matches the WelcomeWidget polish from part 1 (`text-xl sm:text-2xl font-semibold tracking-tight`). Six screens adopted: VaultEntryView, VaultCategoriesView, VaultEntriesView, BadgesView, SettingsView, PointsHistoryView. Food, Shopping, Chat, Points main stay custom (their chrome doesn't model cleanly). *Superseded 2026-10-25:* the hero now renders in the desktop TopBar, and Tasks, Calendar, Points, Rewards, Meals and RecipeDetail adopted it too; Shopping and Chat still have no page title.

**Chip-storm cleanup acting on the new primitives:**

5. **RewardCard** stock / expiry / visibility pills all route through `KinChip variant="status" show-dot="false" :interactive="false"`. `stockClass`/`expiryClass` renamed to `*Status` returning semantic status keys (failed / warning / info).
6. **FeedItem points pill** routes the same way. Stack button + stacked / count-only badges stayed hand-rolled with `.kin-pressable` + per-property transition, they want hover colour-swaps `KinChip filter` doesn't model. Noted below for a future `KinKudoStack` primitive.

**Per-screen polish (small items the user picked next):**

7. **Rewards grid density**, `grid-cols-1 sm:2 lg:3` → `… xl:4 2xl:5`. Main content stops leaving ~55% of a 2560px screen empty.
8. **Food empty-week**, the 28 "+ Add" slot grid is replaced on desktop by a single centred `KinEmptyState` (peach `CalendarDaysIcon`, "No meals planned this week" title, "Plan the week" KinButton CTA). `isWeekEmpty = computed(() => !mealsStore.currentPlan?.entries?.length)`. Mobile keeps infinite scroll from today.
9. **Points history date groups**, `groupedFeed` computed in `PointsHistoryView.vue:76` buckets by `d.toDateString()`, labels "Today" / "Yesterday" / "Weekday, Mon D"; row subtitle switched from `formatDate` (month+day+time) to `formatTime` (hour:minute only). Section headers use `text-[10px] font-semibold uppercase tracking-wider text-ink-tertiary`.
10. **TaskItem dblclick inline-edit retired properly.** Part 2's note said this path was dominated by row-click-opens-panel; this piece removed the `@dblclick.stop`, the `<input>` branch, `startEditTitle`/`saveTitle`/`cancelEditTitle`, the `isEditingTitle`/`editTitle`/`titleInput` refs, and the `ref`/`nextTick` imports. 46 deletions, 11 additions. Row title is now a plain `<p>`; editing happens in TaskDetailPanel.

**Gate + review.** `scripts/check.sh` ran green on each piece (86-89s). Code-review low-effort reported zero findings on 11 of 12 (the ToggleSwitch swap in piece 1 of the session had 3 cosmetic findings addressed before commit).

**Stopped at (next session's starting point):**

- **Settings master-detail layout**, the biggest UX change on the deferral list. Single-column accordion → two-pane list/detail on desktop. Needs a design conversation (list grouping, which setting lands in which pane, mobile fallback), not just a refactor.
- **Dashboard cards sized to their content**, the audit's 50%-empty My Tasks and Today's Schedule call. 11 widgets to audit per-size. Dedicated-session work.
- **`KinUtilityRail` cross-screen promotion**, Vault, Rewards, Food, Shopping, Settings, Achievements each need a per-screen call on what fills the right rail (recent activity, quick filters, kin cards).
- **`KinEmptyState` adoption on remaining sites**, Points right rail, Vault entry Documents section, Rewards auctions empty state. Each needs illustration + CTA copy decisions.
- **TaskItem metadata inline with middots**, the audit's strongest call, deferred since part 1. `Chores · 10pt · Every Tuesday` style replacing the chip-plus-meta row. Medium restructure (changes TaskItem shape, not just styling).
- **`KinKudoStack` primitive**, extracts FeedItem's stack button + stacked / count-only badges. Deferred because it's not a routing of what exists, it's a new primitive from scratch; the hand-rolls work fine until there's a second consumer.
- **Broader `rounded-full` sweep**, Dashboard widgets, calendar legends, settings. Carried from part 1.

**Where decisions live:** this handoff is the ledger. `CHANGELOG.md` has the per-session summary. `REDESIGN_BRIEF.md`'s locked tenets 4 (pill shape), 5 (hero numerals), 6 (gradients) stay preserved.

---

## Polish pass, part 2 (2026-10-24, same branch, still unmerged)

Six more commits (`c5b7813` → `6c4be83`) acting on the audit of the remaining 12 routes. The DS additions from part 1 (`.kin-pressable`, `[data-selected]`, chip routing via `KinChip`) hadn't propagated to Vault, Chat, Points, Rewards, Achievements, Food, Shopping, Settings. This pass closes the gap where the fix is DS adoption; stops where it would need design decisions.

**Landed (6 commits):**

1. **Delete `ToggleSwitch`, route to `KinSwitch`.** All four call sites (SettingsView dark-mode × 2, EventModal all_day + is_featured) use `KinSwitch color="lavender"`. Dark-mode toggle loses the sun/moon-in-thumb icon, `KinSwitch` has no thumb slot by design.
2. **Tokenise brand gold.** 71 `[#C4975A]` sites → `brand-gold` across 19 files. Pure refactor.
3. **`.kin-pressable` + per-property transitions.** Vault categories/entries, Chat suggestions + textarea, BadgeCard card + progress bar, FeaturedEventsSection row. Each site chosen because it was a clickable surface or a transition-all site the audit flagged.
4. **`.kin-tile` DS rule** collapses four Settings selected-tile duplicates (AI mode × 2, AI provider, color theme) into one shared `[data-selected="true"]` grammar. Row-grammar `::before` bar is overridden to `content: none` on tiles so the row style doesn't bleed onto bordered cards.
5. **Vault entry typography.** `SensitiveField` now opts into mono only for data (URLs, domains, phone/zip patterns via regex); prose values use the body font.
6. **Partial chip-storm cleanup.** Only ShoppingListItem + AddItemInput recurring toggles. Honest stop, see below.

**Stopped at (next session's starting point):**

- **FeedItem (Points feed) +1 badges, stack button, points pill.** Four hand-rolled chip visuals per row, but they're functional micro-widgets (stack toggle, count badges, status pill), not category chips. `KinChip variant="status"` always renders a leading dot which these use-cases don't want. Needs either a dot-less `KinChip status` variant or a new `KinStatusBadge` primitive. Design call.
- **RewardCard stock/expiry/visibility pills.** Same issue: three state badges per card with dynamic colour per state (red/amber/lavender/prussian). The audit's recommendation to route through `KinChip variant="status"` would add dots and change padding. Needs the same primitive decision.
- **Chat suggestion buttons → `KinButton`.** They already got `.kin-pressable` in part 1 piece 3; converting to `KinButton` is a question of API fit (do the four large text-left buttons want `KinButton variant="secondary"` or stay as hand-rolls with the DS classes). Low priority given the press-feel is in place.

**Still deferred from audit4 (not touched this session):**

- `KinPageHero` primitive (nine screens, eight hero shapes)
- Promote `KinUtilityRail` cross-screen (five more screens could earn their right column)
- `KinEmptyState` adoption (Food empty week, Points right rail, Vault entry Documents, Rewards auctions)
- Settings master-detail layout (currently single-column wasting ~55% of 1440)
- Food empty-week replacement (28 "+ Add" slots → one empty state)
- Points history date-group headers
- Rewards grid 3 → 4-5 columns at 1440 + per-tile button sizing
- Broader `grep -rn "rounded-full"` sweep (Dashboard widgets, calendar legends, settings), the TaskItem chip pass taught this but it never shipped to the rest

**Known limitations after both passes:**

- `monthEventLabels` prop on `KinMonthGrid` is dead code when `density="dots"` (set in part 1 piece 5). Pre-existing ESLint warnings on it and `monthEventsMap` in `CalendarView.vue` remain unresolved; both will go when either the prop is removed or the dots mode is confirmed permanent.
- `MagnifyingGlassIcon` unused-vars warning in `VaultCategoriesView.vue` is pre-existing, not introduced by this pass.
- `ToggleSwitch` is deleted, but if anyone has an outdated Vite build cached in their browser they'll see a one-time "Failed to reload ToggleSwitch.vue" console error. Hard-refresh clears it.

**Where decisions live:** `CHANGELOG.md` has the per-session summary. `REDESIGN_BRIEF.md`'s locked tenets 4 (pill shape), 5 (hero numerals), 6 (gradients) were preserved against audit moves that would have broken them.

---

## Polish pass, part 1 (2026-10-24, branch `polish/honour-the-system`, unmerged)

Six-commit pass acting on the two real-app audits (frontend-design, emil-design-eng). The thesis both lenses converged on: Kin* exists, feature views bypass it. Each piece honours the system rather than redesigning it. Gate green on each. Nothing pushed; branch waits for review.

**Landed:**

1. `.kin-pressable` utility (`resources/css/app.css`, `KinChip.vue`, `TaskItem.vue`, `KinMonthGrid.vue`, `CalendarView.vue`). Quieter sibling of KinButton's lifted/pressed treatment. `translateY(1px)` on `:active`, gated on `prefers-reduced-motion`. Also folded in the FAB `transition: all` → per-property list (originally earmarked for piece 6).
2. Shared selected grammar at the DS layer (`app.css`, `TaskItem.vue`, `TasksView.vue`). `[data-selected="true"]` on `.kin-pressable` → 2px lavender bar + 4% ink wash. Task-row click now works on touch too (gate lifted; `@click.stop` on inner children prevents double-fire).
3. Task-row chips routed through `KinChip` (`TaskItem.vue`). Four hand-rolls deleted. Tags use `customColor`; recurring uses `color="lavender"`. `tabindex="-1"` on both.
4. Sidebar active state quieted (`KinSidebar.vue`). `bg-accent-lavender-bold text-ink-inverse` → `bg-accent-lavender-soft text-ink-primary` + font-weight 600. Pill kept per tenet 4.
5. Density pass (`WelcomeWidget.vue`, `TaskItem.vue`, `CalendarView.vue`). Hero `text-3xl` → `text-2xl` semibold tracking-tight; task row `py-3.5` → `py-2.5`; calendar `density="pills"` → `"dots"` with max-dots=3.
6. Housekeeping (`DashboardView.vue`, `TaskItem.vue`, `CalendarView.vue`). Drag ghost on token shadow; priority flag has `:aria-label`; mini-month today indicator → 2px ring so main-grid filled circle stays the one "today" marker on the screen.

**Deferred (not blocking, each a clean follow-up):**

- **Dashboard cards sized to their content**, the audit noted My Tasks and Today's Schedule cards sit 50% empty. Fixing this means per-widget height audits across the 11 widgets. The sprawling piece was called out in the handshake as the most likely to stop partway; left for a focused dedicated session.
- **Task-row metadata collapse to inline-text with middots**, the audit's "chip storm" fix had two halves. Piece 3 routed chips through `KinChip` (visual quieting); the full collapse of status/points/cadence/assignee into `Chores · 10pt · Every Tuesday` style inline text is Phase 2.
- **Broader `grep -rn "rounded-full"` sweep** for other chip hand-rolls in dashboard widgets, calendar legends, settings. Piece 3 scoped to TaskItem only. The DS still has consumers routing around it.
- **Non-interactive variant on `KinChip`**, current status variant is a span but renders no colors. A clean `interactive: false` prop on category variant would let TaskItem drop the `tabindex="-1"` hack.
- **Dblclick-inline-edit on task titles** is now effectively dominated by the row-click-opens-panel. In-code note at `TaskItem.vue:131-137` for #293 Phase 2 to route edits through the panel's title field.

**Known limitations after this pass:**

- `data-selected` is used instead of `aria-selected` because the task row is a plain `<div>` with no listbox role. If the Tasks screen gains proper listbox semantics later, swap back to `aria-selected` and the DS selected-grammar CSS updates with it.
- `monthEventLabels` prop on KinMonthGrid is dead when `density="dots"`. The existing ESLint warnings on `monthEventsMap` / `monthEventLabels` as unused vars in `CalendarView.vue:485/502` are pre-existing (both passed to KinMonthGrid). Not introduced by this pass; can be cleaned up when the computed stops being used.
- Mini-month dots on the utility rail are 3×3px, borderline visible. If this surfaces as a complaint, bump to 4px and lavender-bold outlines on event-heavy days.

**Where decisions live:** this section is the ledger. `CHANGELOG.md` has the per-session summary. `REDESIGN_BRIEF.md`'s locked tenets 4 (pill shape) and 6 (gradients over flat white) were deliberately preserved against audit moves that would have broken them.

---

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
