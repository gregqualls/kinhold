---
description: Analyze GitHub issues, determine dependencies and parallel-safety, label issues, post planning comments, and output a worktree execution plan so multiple Claude Code agents can work simultaneously without conflicts.
allowed-tools: Bash, Read, Glob, Grep, Agent, Write, Edit, TodoWrite
---

# Issue Planner — Worktree-Parallel Execution Plan

You are an issue planner for the Q32 Hub project. Your job is to analyze all open GitHub issues, determine which can be worked on simultaneously in separate git worktrees, and produce an execution plan that prevents merge conflicts between parallel agents.

## Core Principle

The user runs multiple Claude Code agents in parallel worktrees. Two agents working on issues that modify the same files will create merge conflicts. Your job is to group issues into **conflict-free parallel batches** — within a batch, every issue can be safely assigned to its own worktree. Batches must be executed sequentially (batch 1 before batch 2) because later batches may depend on work from earlier ones.

## Step 1: Gather Context

Read these files to understand the project state:

1. **CLAUDE.md** (project root) — architecture, modules, file structure, current status
2. **docs/ROADMAP.md** — project phases and priorities
3. **CHANGELOG.md** — recent work completed

Then fetch all open GitHub issues:

```bash
gh issue list --state open --limit 100 --json number,title,labels,body,milestone,assignees,createdAt,updatedAt
```

Also fetch any existing `issue-planner` labels to know what's already been tagged:

```bash
gh label list --json name,color,description | jq '[.[] | select(.name | startswith("plan:"))]'
```

## Step 2: Analyze Each Issue

For every open issue, determine:

### A. Issue Type
- `bug` — Something is broken and needs fixing
- `feature` — New functionality
- `improvement` — Enhancement to existing functionality
- `chore` — Maintenance, config, infra, docs

### B. Complexity Estimate
- `XS` — One file, < 30 min (typo fix, config change, copy update)
- `S` — 1-2 files, < 1 hour (simple bug fix, add a label, small UI tweak)
- `M` — 3-5 files, 1-3 hours (new component, API endpoint + frontend, moderate feature)
- `L` — 6-10 files, 3-6 hours (new module, cross-cutting feature, significant refactor)
- `XL` — 10+ files, 6+ hours (new system, major architectural change)

### C. Modules Touched
Map each issue to the specific modules and file areas it will modify. Be specific — this is what prevents merge conflicts. Use these module zones:

| Module Zone | Key Files/Directories |
|------------|----------------------|
| `auth` | `app/Http/Controllers/Api/V1/AuthController.php`, `resources/js/views/auth/`, `resources/js/stores/auth.js`, auth routes |
| `tasks` | `app/Http/Controllers/Api/V1/TaskController.php`, `app/Models/Task.php`, `resources/js/views/tasks/`, `resources/js/stores/tasks.js`, `resources/js/components/tasks/` |
| `calendar` | `app/Http/Controllers/Api/V1/CalendarController.php`, `resources/js/views/calendar/`, `resources/js/stores/calendar.js`, `resources/js/components/calendar/` |
| `vault` | `app/Http/Controllers/Api/V1/VaultController.php`, `resources/js/views/vault/`, `resources/js/stores/vault.js` |
| `chat` | `app/Http/Controllers/Api/V1/ChatController.php`, `resources/js/views/chat/`, `resources/js/stores/chat.js` |
| `points` | `app/Http/Controllers/Api/V1/PointsController.php`, `resources/js/views/points/`, `resources/js/stores/points.js`, `app/Services/PointsService.php` |
| `badges` | `app/Http/Controllers/Api/V1/BadgeController.php`, `resources/js/views/badges/`, `resources/js/stores/badges.js`, `app/Services/BadgeService.php` |
| `rewards` | `app/Http/Controllers/Api/V1/RewardController.php`, `resources/js/views/points/` (shared with points views) |
| `dashboard` | `resources/js/views/dashboard/`, `resources/js/components/layout/` |
| `settings` | `app/Http/Controllers/Api/V1/SettingsController.php`, `resources/js/views/settings/` |
| `family` | `app/Http/Controllers/Api/V1/FamilyController.php`, `app/Models/Family.php` |
| `layout` | `resources/js/components/layout/` (TopBar, Sidebar, MobileNav) |
| `shared-ui` | `resources/js/components/common/`, `resources/css/app.css`, `tailwind.config.js` |
| `database` | `database/migrations/`, `database/seeders/` |
| `routing` | `routes/api.php`, `resources/js/router/` |
| `config` | `.upsun/`, `config/`, `.env` related |
| `email` | `app/Mail/`, `app/Notifications/`, `config/mail.php` (new module, doesn't exist yet) |
| `new-module` | Issue requires creating an entirely new module not listed above |

**IMPORTANT:** Two issues conflict if they share ANY module zone. Be conservative — if you're unsure whether an issue touches a zone, assume it does. False negatives (missed conflicts) are far worse than false positives (unnecessary sequencing).

### D. Dependencies
Does this issue require another issue to be completed first? Common dependency patterns:
- Bug fixes before features in the same module
- Database migrations before features that use new tables
- Shared UI components before features that consume them
- Auth/permissions before features that rely on access control
- Email infrastructure (#6) before any email-sending features

### E. Priority Score
Calculate a priority score (higher = do first):

| Factor | Points |
|--------|--------|
| Bug | +30 |
| Blocks other issues | +20 per blocked issue |
| XS/S complexity (quick win) | +15 |
| Core infrastructure (email, auth, permissions) | +10 |
| Enhancement to existing module | +5 |
| New standalone feature | +0 |
| XL complexity | -5 |

## Step 3: Build the Execution Plan

### Conflict Detection Algorithm

1. Create a matrix of issue → module zones
2. Two issues CONFLICT if they share any module zone
3. Two issues are PARALLEL-SAFE if they share zero module zones AND neither depends on the other

### Batch Construction

Build batches greedily:

1. Sort all unassigned issues by priority score (descending)
2. Start a new batch
3. For each issue (highest priority first):
   - If it has no dependency on unfinished issues AND it conflicts with zero issues already in this batch → add it to this batch
   - Otherwise → skip it for now
4. When no more issues can be added, finalize this batch
5. Repeat from step 2 with remaining issues
6. Continue until all issues are assigned to a batch

### Recommended Parallelism

Based on the batch, recommend how many worktrees to run:
- If batch has 1 issue → just work on it directly, no worktree needed
- If batch has 2-3 issues → suggest 2-3 worktrees
- If batch has 4+ issues → suggest max 3 worktrees (more gets unwieldy), pick the top 3 by priority

## Step 4: Apply GitHub Labels

Create and apply these labels (create them if they don't exist). Use `gh label create` with `--force` to upsert.

### Labels to Apply

| Label | Color | Meaning |
|-------|-------|---------|
| `plan:batch-1` | `#0E8A16` (green) | First batch — work on these now |
| `plan:batch-2` | `#1D76DB` (blue) | Second batch — after batch 1 merges |
| `plan:batch-3` | `#5319E7` (purple) | Third batch |
| `plan:batch-4` | `#FBCA04` (yellow) | Fourth batch |
| `plan:batch-5` | `#B60205` (red) | Fifth batch or later |
| `plan:parallel-safe` | `#C5DEF5` (light blue) | Can run in parallel with other issues in same batch |
| `plan:sequential` | `#F9D0C4` (light red) | Must run alone or has dependencies |
| `plan:quick-win` | `#0E8A16` (green) | XS or S complexity — fast to complete |
| `plan:needs-infra` | `#D93F0B` (orange) | Requires infrastructure work first (email, OAuth, etc.) |
| `plan:size-XS` | `#EDEDED` (gray) | |
| `plan:size-S` | `#EDEDED` (gray) | |
| `plan:size-M` | `#EDEDED` (gray) | |
| `plan:size-L` | `#EDEDED` (gray) | |
| `plan:size-XL` | `#EDEDED` (gray) | |

### Label Application Rules

1. Remove ALL existing `plan:*` labels from every issue first (clean slate on each run)
2. Apply the new labels based on current analysis
3. This ensures re-runs always reflect the latest state

```bash
# Remove old plan labels from an issue
gh issue edit <number> --remove-label "plan:batch-1,plan:batch-2,plan:batch-3,plan:batch-4,plan:batch-5,plan:parallel-safe,plan:sequential,plan:quick-win,plan:needs-infra,plan:size-XS,plan:size-S,plan:size-M,plan:size-L,plan:size-XL" 2>/dev/null

# Apply new labels
gh issue edit <number> --add-label "plan:batch-1,plan:parallel-safe,plan:size-M"
```

## Step 5: Post/Update Planning Comments

For each issue, post or update a planning comment with the analysis. Use a hidden HTML marker to find and update existing comments instead of creating duplicates.

### Finding Existing Comments

```bash
# Get all comments on an issue, find the one with our marker
gh api repos/{owner}/{repo}/issues/<number>/comments --jq '.[] | select(.body | contains("<!-- issue-planner -->")) | .id'
```

### Comment Format

```markdown
<!-- issue-planner -->
## 🗺️ Issue Planner Analysis

| Field | Value |
|-------|-------|
| **Batch** | `batch-1` — work on this now |
| **Parallel Safety** | ✅ Safe to run alongside: #X, #Y, #Z |
| **Complexity** | `M` (3-5 files, 1-3 hours) |
| **Priority Score** | 45 |
| **Type** | bug |

### Modules Touched
- `tasks` — TaskController, task views, task store
- `dashboard` — dashboard widgets

### Dependencies
- None (or: Requires #6 to be completed first)

### Conflicts With
- #24 (both touch `tasks` module)
- #26 (both touch `dashboard` module)

---
*Auto-generated by issue-planner. Re-run `/issue-planner` to refresh.*
```

### Creating or Updating

```bash
# If comment exists (got an ID above), update it:
gh api repos/{owner}/{repo}/issues/comments/<comment-id> -X PATCH -f body="<comment body>"

# If no existing comment, create one:
gh issue comment <number> --body "<comment body>"
```

## Step 6: Terminal Output

Print a clean, actionable execution plan to the terminal. This is what the user reads to decide what to work on.

### Output Format

```
═══════════════════════════════════════════════════════
  Q32 Hub — Issue Execution Plan
  Generated: 2026-03-22 | Open issues: 20
═══════════════════════════════════════════════════════

📋 BATCH 1 — Start Now (3 parallel worktrees)
───────────────────────────────────────────────────────
  Worktree A: #23 Bug - new task button not responding
    Type: bug | Size: S | Priority: 45 | Modules: tasks

  Worktree B: #2 Validate OAuth for calendar
    Type: chore | Size: M | Priority: 40 | Modules: calendar, config

  Worktree C: #12 Default Badges
    Type: improvement | Size: S | Priority: 35 | Modules: badges, database

  ⚠️  Also in batch 1 but do after the first 3 merge:
    #7 Admin panel on mobile (layout, settings)
    #13 Rewards more prominent (rewards, dashboard)


📋 BATCH 2 — After Batch 1 Merges
───────────────────────────────────────────────────────
  Worktree A: #6 Feature - Emails
    Type: feature | Size: L | Priority: 50 | Modules: email (new), config, database
    ⚠️ Infrastructure — many future issues depend on this

  Worktree B: #24 Add recurring type label to task
    Type: enhancement | Size: S | Priority: 35 | Modules: tasks
    ⚡ Depends on: #23 (bug fix in tasks module)

  Worktree C: #4 Add color themes
    Type: feature | Size: M | Priority: 20 | Modules: shared-ui, settings


📋 BATCH 3 — After Batch 2 Merges
───────────────────────────────────────────────────────
  ...


🔮 BACKLOG — Large/Complex, Schedule When Ready
───────────────────────────────────────────────────────
  #20 Meal Planner (XL — entire new module)
  #16 Manual calendar mode (L — major calendar overhaul)


📊 SUMMARY
───────────────────────────────────────────────────────
  Total issues: 20
  Batches: 4 + backlog
  Quick wins (XS/S): 6
  Bugs to fix first: 1
  Infrastructure needed: 2 (#6 emails, #2 OAuth)

  🚀 Right now, start 3 worktrees:
     claude-code --worktree "fix-task-button" -- "Fix #23"
     claude-code --worktree "oauth-calendar" -- "Fix #2"
     claude-code --worktree "default-badges" -- "Fix #12"
```

## Step 7: Update GitHub Project Board

The project "Q32 Hub — Issue Planner" (project number 1, owner gregqualls) has custom fields: **Batch**, **Parallelism**, and **Size**. After analysis, update each issue's project fields so the board views stay current.

### Get field IDs and option IDs

```bash
gh project field-list 1 --owner gregqualls --format json
```

Parse the JSON to find the field IDs for "Batch", "Parallelism", and "Size", and the option IDs for each value (e.g., "Batch 1", "Parallel Safe", "M").

### Get item IDs

Each issue on the project board has an item ID (different from the issue number). List them:

```bash
gh project item-list 1 --owner gregqualls --format json
```

Match each item to its issue number via the `content.number` field.

### Update fields for each item

```bash
# Set Batch field
gh project item-edit --project-id PROJECT_ID --id ITEM_ID --field-id BATCH_FIELD_ID --single-select-option-id BATCH_1_OPTION_ID

# Set Parallelism field
gh project item-edit --project-id PROJECT_ID --id ITEM_ID --field-id PARALLELISM_FIELD_ID --single-select-option-id PARALLEL_SAFE_OPTION_ID

# Set Size field
gh project item-edit --project-id PROJECT_ID --id ITEM_ID --field-id SIZE_FIELD_ID --single-select-option-id M_OPTION_ID
```

### Handle new issues not yet on the board

If an issue exists on GitHub but isn't on the project board yet, add it first:

```bash
gh project item-add 1 --owner gregqualls --url "https://github.com/gregqualls/q32hub/issues/<number>"
```

Then set its fields as above.

## Important Rules

1. **Re-analyze everything on every run.** Never cache or assume previous analysis is current. Issues may have been closed, new ones opened, or priorities shifted.

2. **Be conservative about conflicts.** If you're unsure whether two issues touch the same files, assume they conflict. A false conflict just means sequential work. A missed conflict means merge hell.

3. **Never post duplicate comments.** Always search for the `<!-- issue-planner -->` marker first and update the existing comment.

4. **Clean labels before applying.** Remove all `plan:*` labels before applying new ones so stale labels don't linger.

5. **Consider the current branch.** Check `git branch` — if there's work in progress on a branch related to an issue, note it in the output so the user knows.

6. **Factor in recently merged PRs.** Check `gh pr list --state merged --limit 5` to see if any issues were just resolved and should be excluded.

7. **Respect the ROADMAP.** If ROADMAP.md says "Phase 3 is calendar" but an issue is a Phase 5 feature, it should be in a later batch regardless of conflict analysis.

8. **Max 3 worktrees recommended.** Even if 5 issues are parallel-safe, recommend only 3 at a time. More than that gets hard to manage, and merge resolution becomes painful.

9. **Bugs before features in the same module.** Always. No exceptions.

10. **The user has ADHD.** Keep the terminal output scannable. Use clear visual hierarchy, not walls of text. Lead with "here's what to do right now" not "here's my analysis."
