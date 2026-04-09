---
description: Merge PR after QA — verifies CI, merges, confirms production deploy
model: haiku
---

Merge the current branch's PR after QA is complete. Verifies all gates before merging and confirms the production deployment succeeds.

## Steps

1. **Pre-flight checks:**
   - Verify we're NOT on `main`: "You're on main. Switch to the feature branch to merge its PR."
   - Find the PR: `gh pr view --json number,url,title,state,mergeable,statusCheckRollup`
   - If no PR: "No PR found. Run `/pr` first."
   - If PR is already merged: "PR #<number> is already merged."
   - If PR is not mergeable (conflicts): "PR has merge conflicts. Resolve them first."

2. **Verify CI is green:**
   - `gh pr checks`
   - If any required check is failing: "CI checks are failing. Fix before merging." Show which checks failed.
   - If checks are still running: "CI is still running. Wait for it to complete, then try again."

3. **Verify handoff exists:**
   - Check if `.claude/handoff.md` exists and contains today's date.
   - If missing or stale: "No handoff note for today. Run `/handoff` before merging to capture session context."

4. **Confirm with Greg:**
   - Show a summary:
   ```
   ## Ready to Merge

   **PR:** #<number> — <title>
   **Branch:** <branch> → main
   **CI:** All checks passed ✅
   **Handoff:** Written ✅

   This will squash-merge the PR and deploy to production via Upsun.
   **Proceed with merge?**
   ```
   - Wait for explicit confirmation. NEVER auto-merge.

5. **Execute merge:**
   - `gh pr merge <number> --squash --delete-branch`
   - Squash merge keeps `main` clean with one commit per feature.
   - `--delete-branch` cleans up the remote branch.

6. **Tag release (if version was bumped):**
   - After merge, checkout main and pull: `git checkout main && git pull origin main`
   - Read `config/version.php` to get the current version.
   - Check if a git tag already exists for this version: `git tag -l "v<version>"`
   - If no tag exists AND the PR included a version bump (check the diff for `config/version.php` changes):
     - `git tag v<version> && git push origin v<version>`
     - This triggers the GitHub Actions release workflow which auto-creates a GitHub Release.
     - Report: "Tagged v<version> — GitHub Release will be created automatically."
   - If the tag already exists or no version bump was in the PR, skip silently.

8. **Monitor production deployment:**
   - Wait 10 seconds for Upsun to pick up the merge.
   - Use `list-activity` MCP tool (project_id: `2rozcvqjtjdta`) to find the latest activity on the `main` environment.
   - Check activity status:
     - If `in_progress` or `pending`: "Upsun deployment in progress..."
     - If `complete`: "Production deployed successfully ✅"
     - If `failed`: Use `log-activity` to surface the error.
   - If no activity found after 30 seconds: "No Upsun deploy detected yet. Check the console manually or wait a moment."

9. **Output:**
   ```
   ## Merge Complete

   **PR:** #<number> merged into main ✅
   **Commit:** <squash commit hash>
   **Release:** Tagged v<version> — GitHub Release created (or "No version bump")
   **Production:** Deploying to kinhold.app... (or ✅ Deployed)

   Next steps:
   - Run `/cleanup` to prune local branches and worktrees
   - Production URL: https://kinhold.app
   ```

## Rules

- **ALWAYS confirm with Greg before merging.** Never auto-merge, even if all checks pass.
- Use `--squash` merge to keep main history clean. One commit per feature/fix.
- Use `--delete-branch` to clean up the remote branch after merge.
- If CI is not green, refuse to merge. No exceptions.
- If handoff note is missing, refuse to merge. Context capture is part of the pipeline.
- Don't wait forever for Upsun deploy — check once or twice, report status, move on. Greg can check the console.
- After merge, suggest `/cleanup` but don't run it automatically.
- If merge fails for any reason (conflicts, permissions), show the error and suggest resolution.
