---
description: CI + Upsun preview checker — verifies PR is ready for manual QA
model: haiku
---

Check the CI pipeline status and Upsun preview environment for the current branch's PR. Tells Greg whether everything is green and what to manually test.

## Steps

1. **Find the PR** for the current branch:
   - `gh pr view --json number,url,title,headRefName,state,statusCheckRollup`
   - If no PR exists: "No PR found for this branch. Run `/pr` first."

2. **Check CI status:**
   - `gh pr checks`
   - Parse each check: name, status (pass/fail/pending), URL
   - Show a summary table:

   ```
   ### CI Status
   | Check      | Status     | Details           |
   |------------|-----------|-------------------|
   | Tests      | ✅ Passed  |                   |
   | Frontend   | ✅ Passed  |                   |
   | Lint       | ⏳ Running | Started 2m ago    |
   ```

   - If any check is still running, note the elapsed time.
   - If any check failed, fetch the log: `gh run view <run-id> --log-failed` and show the relevant error.

3. **Check Upsun preview environment:**
   - Determine the Upsun environment name from the branch. Upsun creates preview environments from PR branches.
   - Use `list-environment` MCP tool (project_id: `2rozcvqjtjdta`) to find environments.
   - Look for an environment matching the branch name or PR number.
   - If found:
     - Use `info-environment` to check status (active, building, inactive)
     - Use `urls-environment` to get the preview URL
     - If building, check `list-activity` for the latest deploy activity and its status
   - If not found: "No Upsun preview environment found. It may still be provisioning — Upsun auto-creates preview environments for PRs. Check again in a few minutes."

4. **Surface errors** if anything failed:
   - CI failure: Show the specific failing test or build error
   - Upsun deploy failure: Use `logs-environment` to get the deploy log and show the error

5. **Generate QA checklist** based on changed files:
   - `git diff --name-only main...HEAD` to see what was touched
   - Map changed files to manual test areas:
     - `app/Http/Controllers/Api/V1/Task*` or `resources/js/views/tasks/` → "Test task CRUD (create, edit, complete, delete)"
     - `app/Http/Controllers/Api/V1/Vault*` or `resources/js/views/vault/` → "Test vault entries (create, view, mask/unmask)"
     - `app/Http/Controllers/Api/V1/Calendar*` or `resources/js/views/calendar/` → "Test calendar views (month, week, day)"
     - `resources/js/components/` → "Check affected components render correctly"
     - `resources/js/stores/` → "Verify store state updates correctly"
     - `app/Mcp/` → "Test MCP tools via Claude Desktop"
     - `tailwind.config.js` or `app.css` → "Check styling and dark mode"
     - `database/migrations/` → "Verify migration runs cleanly"
   - Always include: "Test on mobile (375px)" and "Check dark mode"

6. **Output status:**

   ```
   ## QA Status — PR #<number>

   ### CI: ✅ All checks passed
   (or ❌ 1 check failed — see details above)

   ### Upsun Preview: ✅ Deployed
   **URL:** https://<branch>-<hash>.upsun.app
   (or ⏳ Building... or ❌ Deploy failed)

   ### Manual QA Checklist
   Based on your changes, test the following:
   - [ ] Test task CRUD (create, edit, complete, delete)
   - [ ] Check dark mode on affected views
   - [ ] Test on mobile (375px)

   **Status: READY FOR QA** ✅
   (or **NOT READY** ❌ — fix CI/deploy issues first)

   After QA, run `/handoff` to capture session notes, then `/merge`.
   ```

## Rules

- This is read-only — never modify code, push, or merge.
- If CI is still running, say so clearly and suggest waiting or re-running `/qa` in a few minutes.
- If Upsun preview isn't found, don't panic — it can take a few minutes to provision. Give instructions to check again.
- The QA checklist should be specific to what changed, not a generic list. If only backend changed, don't ask to check dark mode.
- Always end with the next step recommendation based on status.
- Use Upsun project ID `2rozcvqjtjdta` for all API calls.
