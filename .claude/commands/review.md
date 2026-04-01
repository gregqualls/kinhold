---
description: Code review analysis — catches design, security, and convention issues before PR
---

Analyze the current branch's changes against `main` and report issues by category. This is a READ-ONLY analysis — suggest fixes but don't make changes.

## Steps

1. **Get the diff** — Run `git diff main...HEAD` to see all changes on this branch. Also run `git diff` for any uncommitted changes. Combine for the full picture.

2. **Get changed files** — Run `git diff --name-only main...HEAD` to know which files were touched.

3. **Read the full content** of each changed file (not just the diff) to understand context.

4. **Analyze by category:**

### Category 1: Security (severity: BLOCK)
Scan for:
- **Missing family_id scoping** — Any new controller query that doesn't filter by `$user->family_id` or use a Policy
- **Missing policies** — New models or controller actions without corresponding Policy checks (`$this->authorize()` or `Gate::authorize()`)
- **Exposed secrets** — API keys, tokens, passwords in code (not `.env`)
- **SQL injection** — Raw DB queries without parameter binding
- **Mass assignment** — Models without `$fillable` or `$guarded`
- **XSS vectors** — `v-html` with user-supplied data
- **CSRF** — New routes missing middleware
- **File upload risks** — Missing file type/size validation on uploads

### Category 2: Architecture (severity: WARN)
Scan for:
- **API-first violations** — Frontend directly accessing DB or bypassing the API
- **Missing Pinia store** — New frontend feature without a corresponding store
- **Missing MCP tool** — New API endpoint without MCP tool coverage (check `app/Mcp/Tools/`)
- **Business logic in controllers** — Complex logic that should be in a Service class
- **N+1 query risks** — Eloquent relations loaded in loops without `with()`

### Category 3: Conventions (severity: WARN)
Scan for:
- **Options API** — Vue components using `export default {}` instead of `<script setup>`
- **Missing dark mode** — New UI components without `dark:` Tailwind classes
- **Non-mobile-first** — Breakpoint classes without mobile base (e.g., `md:flex` without base `hidden`)
- **Custom CSS** — Inline styles or custom CSS classes instead of Tailwind utilities
- **Naming violations** — Controllers not `PascalCase`, stores not `use{Name}Store`, routes not kebab-case

### Category 4: Completeness (severity: INFO)
Scan for:
- **Missing tests** — New controllers/services without test coverage
- **Missing seeders** — New models without demo data in seeders
- **Missing CHANGELOG** — No entry in CHANGELOG.md for this work
- **Missing ROADMAP update** — Feature shipped but ROADMAP not updated
- **Missing migration** — New model fields without corresponding migration

### Category 5: Performance (severity: WARN)
Scan for:
- **N+1 queries** — Eloquent relations loaded inside loops without `with()` eager loading
- **Missing pagination** — Controller actions returning unbounded collections without `->paginate()`
- **Heavy imports** — Large libraries imported in frontend when a lighter alternative exists (e.g., full lodash vs. individual functions)
- **Missing indexes** — New migrations adding columns used in `where()` or `orderBy()` without an index
- **Synchronous blocking** — Long-running operations in request cycle that should be queued (email sending, API calls, file processing)
- **Unoptimized queries** — `SELECT *` patterns, missing `select()` on queries returning many columns

### Category 6: Accessibility (severity: WARN)
Scan for:
- **Missing alt text** — `<img>` tags without `alt` attribute
- **Non-semantic HTML** — `<div>` used for clickable elements instead of `<button>` or `<a>`
- **Missing ARIA labels** — Icon-only buttons without `aria-label` or `sr-only` text
- **Missing form labels** — `<input>` without associated `<label>` or `aria-label`
- **Contrast concerns** — Custom color classes that may not meet WCAG AA (flag for manual review)
- **Missing keyboard navigation** — Interactive elements without `tabindex` or keyboard event handlers
- **Missing focus styles** — Custom components that override or remove `focus:` ring styles

### Category 7: Dependencies (severity: INFO)
Scan for:
- **New packages** — Check `composer.json` and `package.json` diffs for additions
- **Justification** — Flag new dependencies and ask if they're justified vs. a simpler approach
- **Known vulnerabilities** — If a new dependency is added, note that `/check` will audit it
- **License compatibility** — Flag any new dependency with a license incompatible with Elastic License 2.0 (GPL, AGPL)

5. **Output the review:**

```
## Code Review — branch: feature/65-shopping-lists

### 🔴 BLOCK (must fix before PR)
1. **[Security]** `ShoppingListController@index` queries without family_id scoping
   → `app/Http/Controllers/Api/V1/ShoppingListController.php:42`
   → Fix: Add `where('family_id', $user->family_id)` or use Policy

### 🟡 WARN (should fix)
2. **[Architecture]** New `/api/v1/shopping-lists` endpoint has no MCP tool
   → Create tool in `app/Mcp/Tools/`
3. **[Conventions]** `ShoppingList.vue` missing dark mode classes on card container
   → `resources/js/views/shopping/ShoppingList.vue:15`

### 🔵 INFO (consider)
4. **[Completeness]** No tests for ShoppingListController
5. **[Completeness]** CHANGELOG.md not updated

**Summary: 1 blocker, 2 warnings, 2 info items**
```

## Rules

- Read-only — do NOT modify any files. That's the developer's job (or `/fix` for auto-fixable issues).
- Always read the FULL file for changed files, not just the diff. Context matters for detecting missing patterns.
- Be specific — include file paths and line numbers for every finding.
- Security issues are always BLOCK severity. No exceptions.
- Don't be pedantic — skip nitpicks. Focus on things that would cause bugs, security issues, or maintenance problems.
- If no issues found, say so clearly: "No issues found. Code looks good for PR."
- Cross-reference `CLAUDE.md` conventions and `docs/CONVENTIONS.md` for project-specific rules.
- Check `app/Policies/` to verify new resources have authorization coverage.
- Check `app/Mcp/Tools/` to verify API coverage — every API endpoint should have an MCP tool (per project principles).
