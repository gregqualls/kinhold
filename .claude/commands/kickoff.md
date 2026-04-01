---
description: Start-of-session briefing — reads all context and presents what's next
---

Run the Kinhold session kickoff. Read all relevant context and present a concise briefing so we can start working immediately.

## Steps

1. **Read the handoff note** — Check if `.claude/handoff.md` exists. If it does, read it for the previous session's context (what was done, what's next, blockers, open questions).

2. **Read latest CHANGELOG entry** — Read the top entry from `CHANGELOG.md` to understand the most recent work.

3. **Check git state** — Run:
   - `git status` — any uncommitted changes or untracked files?
   - `git log --oneline -5` — last 5 commits
   - `git branch` — current branch (should be `main` after cleanup)

4. **Scan ROADMAP** — Read `docs/ROADMAP.md` to understand current priorities and phases.

5. **GitHub pulse** — Pull the current state from GitHub. This is a primary tracking system, not optional:

   **Milestones** (use the API — `gh milestone` doesn't exist):
   - `gh api repos/gregqualls/kinhold/milestones --jq '.[] | {title, due_on, open_issues, closed_issues, state}'`
   - Show progress as fraction + percentage (e.g., "Phase 0: Foundations — 8/10 done (80%)")
   - Flag any milestones past due or within 7 days of deadline
   - Highlight the current milestone (lowest phase with open issues)

   **Current milestone issues** — show the remaining issues for the active milestone:
   - `gh api 'repos/gregqualls/kinhold/issues?milestone=<number>&state=open' --jq '.[] | {number, title, labels: [.labels[].name]}'`
   - Milestone numbers: Phase 0=1, Phase A=2, Phase B=3, Phase C=4, Phase D=5, Phase E=6

   **Open PRs:**
   - `gh pr list --state open`
   - Note any that need review or are stale

   **Issues by priority batch:**
   - `gh issue list --state open --label "plan:batch-2"` — current priority batch
   - `gh issue list --state open --label "high-impact"` — high-impact items across all batches
   - Show the next actionable issues (current batch first, then next batch)

   **Recently closed:**
   - `gh issue list --state closed --limit 5` — what was just shipped (momentum check)

6. **Present the briefing** — Output a structured summary:

```
## Session Briefing — YYYY-MM-DD

**Repo:** Clean on `main` at <commit>
**Last session:** <1-line summary from handoff/changelog>

### Milestones
- **Phase 0: Foundations** — 8/10 done (80%) ← active
- Phase A: Make It Sticky — 0/3 done
- (show all open milestones with progress bars)

### Remaining in Current Milestone
- [ ] #63 Onboarding wizard (size-L, high-impact)
- [ ] #76 Claude connector (size-L, high-impact)
- (pull from API, show actual open issues for the active milestone)

### Current Batch (batch-2)
- (issues labeled plan:batch-2 that are still open)

### Up Next (batch-3)
- (issues labeled plan:batch-3, for context on what's coming)

### Open PRs
- <list or "None">

### Recently Shipped
- #XX <title> (closed <date>)

### Blockers / Context
- <anything from handoff blockers/open questions>
- <any git state issues>

**What do you want to focus on?**
```

7. **Quality tool health check** — Verify the dev pipeline tools are in place:
   - Does `pint.json` exist? (PHP formatting)
   - Does `phpstan.neon` exist? (static analysis)
   - Does `eslint.config.js` exist? (JS/Vue linting)
   - If any are missing, flag: "Quality tool config missing — run `/check` may fail."

8. **Stale worktree detection** — Run `git worktree list` and check `.claude/worktrees/` for any stale worktrees from previous sessions. If found, suggest: "Stale worktrees detected. Run `/prune-worktrees` to clean up."

9. **Branch creation offer** — After presenting the briefing and priorities:
   - Identify the next priority issue (from current batch or highest priority open issue)
   - Offer: "Want to start on **#<number> — <title>**? I'll create `feature/<number>-<slug>` from latest main."
   - If Greg says yes:
     - `git checkout main && git pull origin main`
     - `git checkout -b feature/<number>-<slug>`
     - Confirm: "Branch created. Ready to code."
   - If Greg wants a worktree instead, create one with `git worktree add .claude/worktrees/<slug> -b feature/<number>-<slug>`

## Rules
- Do NOT start working on anything. This is a briefing, not an action (except branch creation if Greg opts in).
- If there's no handoff note, that's fine — fall back to CHANGELOG + ROADMAP + git state.
- Keep the briefing to one screen. Don't dump entire files.
- Always end by asking Greg what he wants to focus on.
- If the repo is dirty (uncommitted changes, wrong branch), flag it prominently.
- The branch creation offer is optional — Greg may want to pick a different issue or just explore.
