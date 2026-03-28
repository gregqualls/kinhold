---
description: Post-merge cleanup — prune worktrees, delete merged branches, pull main
---

Run post-merge cleanup for the Kinhold repo:

1. **Prune worktrees** — `git worktree prune` and remove any leftover directories in `.claude/worktrees/`
2. **Delete merged branches** — find all local branches fully merged into `main` and delete them (`git branch -d`). Never delete `main` itself.
3. **Delete stale remote tracking refs** — `git fetch --prune`
4. **Pull latest main** — `git checkout main && git pull`
5. **Report** — show what was cleaned up and confirm the repo is in a clean state.

Do NOT delete unmerged branches without asking first — list them and ask.
