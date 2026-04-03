---
description: Automated PR creation — runs quality gates, links issues, fills template
model: sonnet
---

Create a pull request for the current branch with all the right metadata. Refuses to create a PR if quality checks fail.

## Steps

1. **Pre-flight checks:**
   - Verify we're NOT on `main` — refuse if so: "Switch to a feature branch first."
   - Run `git status` — warn if there are uncommitted changes: "You have uncommitted changes. Commit or stash them first."
   - Check if a PR already exists for this branch: `gh pr view --json number,url 2>/dev/null` — if so, show the URL and ask if Greg wants to update it instead.

2. **Extract issue number** from the branch name:
   - Pattern: `feature/<number>-<slug>`, `fix/<number>-<slug>`, `chore/<number>-<slug>`
   - Example: `feature/65-shopping-lists` → issue #65
   - If no number found, ask Greg which issue this relates to (or none).

3. **Run `/check`** as a quality gate:
   - If ANY check fails (not warnings), stop and show: "Quality checks failed. Fix issues before creating a PR."
   - Show the check results table so Greg sees what failed.
   - Suggest `/fix` for auto-fixable issues.

4. **Verify tests exist:**
   - Check if any new test files were added in this branch: `git diff --name-only main...HEAD -- tests/`
   - If no new tests and new controllers/services were added, WARN: "No new tests found. Consider adding tests for new functionality."
   - This is a warning, not a blocker — Greg decides.

5. **Build the PR:**

   **Title:** Generate from branch name and issue title (if linked):
   - Fetch issue title: `gh issue view <number> --json title --jq .title`
   - Format: `<type>: <issue title>` (e.g., "feat: add shopping list module (#65)")
   - Type mapping: `feature/` → `feat`, `fix/` → `fix`, `chore/` → `chore`, `refactor/` → `refactor`

   **Body:** Use the existing PR template format:
   ```
   ## Summary
   - <3-5 bullet points summarizing changes based on git diff>

   ## Linked Issues
   Closes #<number>

   ## Test Plan
   - [ ] <checklist based on what changed>

   ## Checklist
   - [x] Tested locally
   - [ ] Mobile-first (375px)
   - [ ] Dark mode support
   - [x] No credentials committed
   - [ ] API changes documented
   ```

   **Labels:** Check if the issue has `plan:batch-*` or size labels and apply them to the PR.

   **Milestone:** If the issue is in a milestone, assign the PR to the same milestone.

6. **Push and create:**
   - Push the branch if not already pushed: `git push -u origin <branch>`
   - Create the PR: `gh pr create --title "<title>" --body "<body>" --label "<labels>" --milestone "<milestone>"`
   - If issue is linked, the `Closes #N` in the body auto-links it.

7. **Output:**
   ```
   ## PR Created

   **PR:** #<number> — <title>
   **URL:** <github url>
   **Linked Issue:** #<number>
   **CI:** Running... (check status with `/qa`)

   Next step: Run `/qa` to check CI status and get the Upsun preview URL.
   ```

## Rules

- Never create a PR if `/check` fails. Quality gates are non-negotiable.
- Always push the branch before creating the PR.
- Always include `Closes #N` in the body if there's a linked issue — this auto-closes the issue on merge.
- Use squash merge format in the title (conventional commits: feat/fix/chore/refactor).
- Don't over-describe in the summary — let the diff speak. 3-5 bullets max.
- If Greg wants to create a PR despite failing checks, he can use `gh pr create` directly. This skill enforces the pipeline.
- Show the PR URL at the end so Greg can open it in the browser.
