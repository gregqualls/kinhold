---
description: Auto-fix formatting and lint issues — runs Pint + ESLint fix, then re-checks
model: haiku
---

Automatically fix common code quality issues (formatting, lint errors) and verify the fixes resolved them.

## Steps

1. **Show what will be fixed** — Run diagnostics first:
   - `./vendor/bin/pint --test` to see PHP formatting issues
   - `npx eslint resources/js/` to see JS/Vue lint issues
   - Report counts: "Found 3 PHP formatting issues and 5 ESLint issues."

2. **Fix PHP formatting:**
   - Run `./vendor/bin/pint`
   - Capture output showing which files were fixed
   - Report: "Fixed formatting in 3 files: ..."

3. **Fix JS/Vue lint issues:**
   - Run `npx eslint resources/js/ --fix`
   - Capture output showing which files were fixed
   - Report: "Fixed lint issues in 5 files: ..."
   - Note: ESLint `--fix` only fixes auto-fixable rules. Manual fixes may still be needed.

4. **Re-run checks** to verify:
   - Run `./vendor/bin/pint --test` — should now pass
   - Run `npx eslint resources/js/` — check if errors remain (warnings are OK)
   - If issues remain that couldn't be auto-fixed, list them with file:line so Greg can fix manually.

5. **Output:**

   ```
   ## Auto-Fix Results

   ### PHP Formatting (Pint)
   ✅ Fixed 3 files:
   - app/Http/Controllers/Api/V1/TaskController.php
   - app/Services/PointService.php
   - app/Models/Task.php

   ### JS/Vue Lint (ESLint)
   ✅ Fixed 5 files (2 issues remain — manual fix needed):
   - resources/js/views/tasks/TaskList.vue (fixed)
   - resources/js/stores/tasks.js (fixed)
   ⚠️ Manual fix needed:
   - resources/js/components/tasks/TaskCard.vue:42 — 'no-unused-vars': Remove unused `tempData`

   ### Verification
   - Pint: ✅ PASS
   - ESLint: ⚠️ 1 error remaining (manual fix needed)

   Changes are staged but NOT committed. Review them with `git diff` before committing.
   ```

## Rules

- **Never commit automatically.** Leave changes staged for Greg to review.
- Run diagnostics BEFORE fixing so Greg sees what will change.
- Always re-check after fixing to verify the fixes worked.
- If ESLint can't auto-fix something, show the specific error with file:line.
- Don't touch anything beyond Pint and ESLint — no manual code changes, no refactoring.
- If both tools report zero issues initially: "Nothing to fix — code is already clean."
- This command is safe to run anytime — it only modifies formatting and auto-fixable lint issues.
