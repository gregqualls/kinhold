---
description: Interactive pipeline guide — detects where you are and suggests the next step
---

Figure out where Greg is in the development pipeline and guide him to the next step. No memorization required — just run `/playbook`.

## Steps

1. **Detect current state** by checking these signals (in order):

   **Signal checks:**
   - `git branch --show-current` → Are we on `main` or a feature branch?
   - `git status --porcelain` → Any uncommitted changes?
   - `git log main..HEAD --oneline 2>/dev/null` → Any commits on this branch beyond main?
   - `gh pr view --json number,state,statusCheckRollup 2>/dev/null` → Does a PR exist? Is CI passing?
   - Check if `.claude/handoff.md` exists and has today's date → Has handoff been written?

2. **Determine pipeline position** based on signals:

   | State | Signals | Position | Next Step |
   |-------|---------|----------|-----------|
   | Fresh session on main | branch=main, clean | START | `/kickoff` |
   | Ready to branch | branch=main, kickoff done | PLAN | Choose an issue and create branch |
   | Coding | feature branch, uncommitted changes | CODE | Keep coding, then `/review` |
   | Code complete | feature branch, clean, commits ahead of main | REVIEW | `/review` |
   | Reviewed | review done (check conversation) | CHECK | `/check` |
   | Checks passed | checks passed | PR | `/pr` |
   | PR created | PR exists, CI running/passed | QA | `/qa` |
   | QA ready | CI green, Upsun deployed | QA | Manual testing, then `/handoff` |
   | Handoff done | handoff.md has today's date | MERGE | `/merge` |
   | Merged | PR merged, on main | CLEANUP | `/cleanup` |

3. **Show pipeline position** with visual indicator:

   ```
   ## Pipeline — feature/65-shopping-lists

   /kickoff ✅ → code ✅ → /review ✅ → /check ⬅ YOU ARE HERE → /pr → /qa → /handoff → /merge → /cleanup

   **Current step: Quality Checks**
   Run `/check` to verify all automated quality gates pass.

   Quick status:
   - Branch: feature/65-shopping-lists (4 commits ahead of main)
   - Changes: Clean working tree
   - PR: Not created yet
   - Handoff: Not written

   **Run `/check` now?**
   ```

4. **Handle edge cases:**
   - If on main with uncommitted changes: "You have uncommitted changes on main. Stash or commit them before starting a new feature."
   - If on a feature branch with no commits: "You're on a feature branch but haven't made any changes yet. Start coding!"
   - If PR exists but CI failed: "CI failed on your PR. Check the errors with `/qa` or fix and push."
   - If stuck between steps: Use best judgment based on signals and suggest the most logical next step.

5. **If Greg says yes**, run the suggested command immediately.

## The Full Pipeline

For reference, here's the complete development lifecycle:

```
┌─────────────────────────────────────────────────────────────────┐
│                    Kinhold Development Pipeline                  │
├─────────┬───────────────────────────────────────────────────────┤
│ START   │ /kickoff — Session briefing + branch creation offer   │
│ CODE    │ Write code (Claude Code assists)                      │
│ REVIEW  │ /review — Catch design/security/convention issues     │
│ CHECK   │ /check — Automated quality gates (lint, test, build)  │
│ PR      │ /pr — Create PR with linked issues + quality gate     │
│ QA      │ /qa — CI status + Upsun preview + manual test list   │
│ HANDOFF │ /handoff — Capture session context + quality snapshot │
│ MERGE   │ /merge — Squash merge + confirm production deploy    │
│ CLEANUP │ /cleanup — Prune branches + worktrees                │
├─────────┴───────────────────────────────────────────────────────┤
│ UTILS   │ /fix — Auto-fix lint/format issues                   │
│         │ /ship — Optional comprehensive pre-merge audit       │
│         │ /issue-planner — Sprint planning + parallel batches   │
│         │ /prune-worktrees — Clean stale worktrees              │
└─────────────────────────────────────────────────────────────────┘
```

## Rules

- This is a GUIDE, not a gate. Greg can always run individual commands directly or skip steps.
- Never skip steps in the suggestion — if `/check` hasn't run, don't suggest `/pr`.
- If a step fails, stay on that step and explain what to fix.
- Keep the output to ONE SCREEN. Don't dump details — that's what the individual commands do.
- Always show the full pipeline with position marker so Greg sees the big picture.
- If this is clearly a session start (on main, clean), just suggest `/kickoff` without the full pipeline visual.
- Be opinionated — don't list all possible options. Tell Greg what to do next.
