---
description: Single-command quality gate — runs all automated checks and reports pass/fail
model: haiku
---

Run all quality checks in one shot. Output a single pass/fail results table.

## Steps

1. **Detect context** — Run `git branch --show-current` to determine if we're on a feature branch or `main`.

2. **Determine scope** — If on a feature branch, get changed files vs main:
   - `git diff --name-only main...HEAD` for committed changes
   - `git diff --name-only` for uncommitted changes
   - Combine both lists, deduplicate

3. **Run checks in this order:**

   ### Check 1: PHP Syntax
   - Run `php -l` on each changed `.php` file (or all `app/` files if on main)
   - Pass if all files parse without errors

   ### Check 2: PHP Formatting (Pint)
   - Run `./vendor/bin/pint --test`
   - Pass if no formatting issues found
   - If fail, note: "Run `/fix` to auto-fix"

   ### Check 3: Static Analysis (Larastan)
   - Run `./vendor/bin/phpstan analyse --memory-limit=512M`
   - Pass if no errors at configured level
   - Warn if baseline errors exist but no new ones

   ### Check 4: Tests (PHPUnit)
   - Run `./vendor/bin/phpunit`
   - Pass if all tests pass
   - Show test count and assertion count

   ### Check 5: Frontend Build (Vite)
   - Run `npx vite build 2>&1`
   - Pass if build completes with exit code 0
   - Show module count on success

   ### Check 6: Frontend Lint (ESLint)
   - Run `npx eslint resources/js/` on changed `.js` and `.vue` files (or full `resources/js/` on main)
   - Pass if no errors (warnings are OK)
   - If fail, note: "Run `/fix` to auto-fix"

   ### Check 7: Dependency Audit
   - Run `composer audit 2>&1` to check PHP dependencies for known vulnerabilities
   - Run `npm audit --omit=dev 2>&1` to check Node production dependencies
   - Pass if no vulnerabilities found
   - Warn if only low/moderate severity. Fail if high/critical severity.
   - Show count by severity level

   ### Check 8: Test Coverage
   - Run `./vendor/bin/phpunit --coverage-text --coverage-filter=app 2>&1` (requires Xdebug or PCOV)
   - If coverage tool is not available, run `./vendor/bin/phpunit` normally and show `⏭ SKIP` for coverage with note: "Install pcov (`pecl install pcov`) for coverage reports"
   - Extract overall line coverage percentage from output
   - Pass if coverage >= 40% (starting threshold — increase over time)
   - Warn if coverage is between 20-40%
   - Fail if coverage < 20%
   - Show coverage % and note the threshold
   - **Note:** This replaces Check 4 (PHPUnit). Don't run PHPUnit twice — coverage run includes test execution.

   ### Check 9: Bundle Size
   - After Vite build (Check 5), check the output for total bundle size
   - Parse the Vite build output for JS and CSS asset sizes
   - Warn if any single JS chunk exceeds 500KB (gzipped)
   - Warn if total JS exceeds 1MB (gzipped)
   - Show the largest chunks with sizes
   - This is informational — helps catch accidental large dependency additions

   ### Check 10: Accessibility (basic)
   - Scan changed `.vue` files for common accessibility issues:
     - `<img>` tags without `alt` attribute
     - `<button>` or `<a>` elements without accessible text (empty or icon-only without `aria-label`)
     - Form `<input>` elements without associated `<label>` or `aria-label`
     - Click handlers on non-interactive elements (`<div @click>` without `role` and `tabindex`)
   - Pass if no issues found
   - Warn with specific file:line for each issue
   - This is a basic scan, not a full WCAG audit — catches the most common mistakes

4. **Output results table:**

```
## Quality Check Results

| #  | Check              | Status  | Details                         |
|----|-------------------|---------|----------------------------------|
| 1  | PHP Syntax         | ✅ PASS | 12 files checked                |
| 2  | Pint (formatting)  | ❌ FAIL | 3 files need formatting         |
| 3  | Larastan (analysis)| ✅ PASS | 0 errors                        |
| 4  | PHPUnit (tests)    | ✅ PASS | 45 tests, 128 assertions        |
| 5  | Vite (build)       | ✅ PASS | 791 modules                     |
| 6  | ESLint (lint)      | ⚠️ WARN | 2 warnings, 0 errors            |
| 7  | Dependency Audit   | ✅ PASS | 0 vulnerabilities               |
| 8  | Test Coverage      | ⚠️ WARN | 35% line coverage (target: 40%) |
| 9  | Bundle Size        | ✅ PASS | 287KB JS (largest chunk: 142KB) |
| 10 | Accessibility      | ⚠️ WARN | 2 images missing alt text       |

**Overall: 7/10 passed, 1 failed, 2 warnings**
Run `/fix` to auto-fix formatting and lint issues.
```

## Rules

- Always run ALL 10 checks, even if one fails early. Greg needs the full picture.
- Use `⚠️ WARN` for warnings-only results (no errors but has warnings). These don't count as failures for the `/pr` gate.
- If a tool is missing (e.g., phpstan not installed, pcov not available), show `⏭ SKIP` with install instructions.
- On a feature branch, prefer checking only changed files for PHP syntax, ESLint, and Accessibility (faster feedback). Run full suite for PHPUnit, Pint, Larastan, and Vite.
- Show wall-clock time for the full run at the bottom.
- Do NOT fix anything — that's `/fix`'s job. This is diagnosis only.
- **Hard gates (block `/pr`):** Checks 1-6 must pass (PHP syntax, Pint, Larastan, PHPUnit, Vite, ESLint).
- **Soft gates (warn only):** Checks 7-10 are informational — dependency audit with high/critical vulns is the exception (that's a hard fail).
- If ALL hard gates pass, output: **"All checks passed. Ready for `/pr`."** (even if soft gates have warnings)
- If ANY hard gate fails, output: **"Checks failed. Fix issues before creating a PR."**
- Coverage threshold starts at 40% — as the test suite grows, this should be raised. Note the target in output.
