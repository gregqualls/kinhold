---
description: Post-merge cleanup — prune worktrees, delete merged branches, pull main
model: haiku
---

Run post-merge cleanup for the Kinhold repo. This is purely git hygiene — no documentation updates.

## Pre-flight Check

**Before doing anything else**, check if `.claude/handoff.md` exists and was updated today:
- If it exists and today's date is in it → proceed with cleanup.
- If it's missing or stale → tell Greg: "Run `/handoff` first to capture session context, then come back to `/cleanup`." Do NOT proceed without confirmation.

## Context Detection

- Run `git rev-parse --git-dir` and check if the current directory is inside `.claude/worktrees/`.
- If we're inside a worktree, note its name and branch so we handle it correctly.

## Steps

1. **Prune dead worktrees** — `git worktree prune` to clean up stale refs. Then remove leftover directories in `.claude/worktrees/` — but **SKIP the current worktree** if we're inside one. Only remove directories that no longer have a valid worktree reference.
2. **Delete merged branches** — find all local branches fully merged into `main` and delete them (`git branch -d`). Never delete `main` itself. **SKIP the current worktree's branch** if we're inside one — it's still in use.
3. **Delete stale remote tracking refs** — `git fetch --prune`
4. **Pull latest main** — Do this from the main repo, NOT by checking out main in the current worktree. Plain `git pull` silently fails on diverged history, so always fetch first and check explicitly:
   - `git fetch origin --prune` first.
   - Find the main repo path from `git worktree list` (the line tagged `[main]`). If we're already on main directly, that's the current directory.
   - Compare local main to origin/main: `git -C <main-path> rev-list --left-right --count origin/main...main`. Read the two numbers as `<behind>	<ahead>`.
     - `0	0` → already current. Skip.
     - `N	0` (only behind) → fast-forward: `git -C <main-path> merge --ff-only origin/main`.
     - `N	M` with M>0 (diverged) → STOP. Print local-only commits with `git -C <main-path> log origin/main..main --oneline`. Tell Greg: "Local main has M commits not on origin/main. Usually these are squash-merge dupes — when GitHub squashes a PR, the unsquashed commits stay on local main with different SHAs. If that's the case here, safe to `git -C <main-path> reset --hard origin/main`. But if any commit is real local work, push it first." Ask before resetting. Do NOT auto-reset.
   - **NEVER run `git checkout main`** if we're in a worktree — that kills the session.
5. **Report** — show what was cleaned up and confirm the repo state. If we're still in a worktree, confirm it's intact. Remind that if the worktree was removed, the next session should start with `/kickoff`.

## Rules
- Do NOT delete unmerged branches without asking first — list them and ask.
- Do NOT nuke the worktree you're currently sitting in.
- If the worktree's branch IS merged, warn Greg: "Your current worktree's branch is merged. This session will be stranded after cleanup. Start a new session and run `/kickoff`."
