---
description: Remove stale worktrees — keeps the current one if you're in one
---

Clean up Claude Code worktrees that have accumulated across sessions.

## Steps

1. **Detect context** — Check if the current working directory is inside `.claude/worktrees/`. If so, note the current worktree name so we don't touch it.

2. **List all worktrees** — Run `git worktree list` and list all directories in `.claude/worktrees/`. Cross-reference to find:
   - **Active (current session):** the worktree we're sitting in, if any — NEVER remove
   - **Stale (git says prunable):** git has lost track of them — safe to remove
   - **Orphaned directories:** folders in `.claude/worktrees/` with no matching git worktree — safe to remove
   - **Inactive but valid:** worktrees git still tracks that aren't the current one — these are from old sessions

3. **Auto-remove safe targets:**
   - Run `git worktree prune` to clean stale refs
   - Delete orphaned directories (no git worktree ref, not current)
   - For inactive-but-valid worktrees: remove them with `git worktree remove <path>`. If they have uncommitted changes, list them and ask before forcing removal.

4. **Clean up branches** — For each removed worktree, delete its `claude/*` branch if it's fully merged into main. List any unmerged branches and ask what to do.

5. **Report** — Show what was removed, what was kept (and why), and the current state.

## Rules
- NEVER remove the worktree you're currently inside — that kills the session.
- NEVER force-remove a worktree with uncommitted changes without asking.
- NEVER delete unmerged branches without asking.
- This is safe to run at the start of any session as housekeeping.
