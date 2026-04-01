---
description: Pre-merge audit — comprehensive readiness check (optional, use anytime)
---

Run a comprehensive audit of the current branch to verify it's ready to ship. This is an OPTIONAL command — the main pipeline (`/check` → `/pr` → `/qa` → `/handoff` → `/merge`) covers the required gates. Use `/ship` when you want extra confidence before a big merge.

## Steps

1. **Run `/check`** — Execute all automated quality gates first.
   - If ANY check fails, stop immediately: **"DO NOT SHIP — quality checks failed."**
   - Show the check results table.

2. **Auto-verify** — Programmatically check these items by comparing `git diff --name-only main...HEAD`:

   ### Routes → Controllers
   - Parse `routes/api.php` for new route definitions
   - Verify each new route has a matching controller method in `app/Http/Controllers/Api/V1/`
   - ✅ PASS if all routes have controllers, ❌ FAIL if any are missing

   ### Models → Seeders
   - Check `app/Models/` for new model files
   - Verify `database/seeders/` has seeder coverage for new models
   - ✅ PASS if covered, ⚠️ WARN if missing (not all models need seeders)

   ### API → MCP Tools
   - Check for new API endpoints in `routes/api.php`
   - Verify matching MCP tools exist in `app/Mcp/Tools/`
   - ✅ PASS if covered, ❌ FAIL if new endpoints lack MCP tools (per project principles)

   ### Controllers → Tests
   - Check for new controllers or controller methods
   - Verify test files exist in `tests/Feature/` or `tests/Unit/`
   - ✅ PASS if covered, ⚠️ WARN if missing

   ### Documentation
   - Check if `CHANGELOG.md` has an entry for today/this session
   - Check if `docs/ROADMAP.md` reflects the feature status
   - ✅ PASS if updated, ⚠️ WARN if missing

3. **Manual verify** — Ask Greg about items that can't be automated:
   - "Did you test on mobile (375px)?"
   - "Does dark mode look right?"
   - "Did you QA in the Upsun preview?"

4. **Output verdict:**

```
## Ship Audit — feature/65-shopping-lists

### Automated Checks
| Check                  | Status  | Details                         |
|-----------------------|---------|----------------------------------|
| Quality gates (/check) | ✅ PASS | 6/6 checks passed               |
| Routes → Controllers   | ✅ PASS | 3 new routes, all have controllers |
| Models → Seeders       | ⚠️ WARN | ShoppingItem model has no seeder |
| API → MCP Tools        | ❌ FAIL | /api/v1/shopping-lists has no MCP tool |
| Controllers → Tests    | ✅ PASS | 2 new test files                |
| Documentation          | ✅ PASS | CHANGELOG + ROADMAP updated     |

### Manual Checks
- [ ] Mobile testing (375px)
- [ ] Dark mode
- [ ] Upsun preview QA

### Verdict: ⚠️ SHIP WITH CAUTION
1 failure (MCP tool missing), 1 warning (seeder missing).
Fix the MCP tool gap before merging, or acknowledge the gap.
```

## Verdict Rules
- **SHIP** ✅ — All auto-checks pass, manual checks confirmed
- **SHIP WITH CAUTION** ⚠️ — Warnings exist but no failures
- **DO NOT SHIP** ❌ — Quality checks failed OR critical auto-checks failed (routes, MCP tools)

## Rules
- Run ALL checks, even if one fails early. Greg needs the full picture.
- The MCP tool check uses `app/Mcp/Tools/` (Laravel-native), NOT the legacy `mcp-server/` directory.
- Be specific about what's missing — file names, route paths, tool names.
- This command is optional in the pipeline. `/check` + `/pr` + `/qa` cover the required gates.
- Don't fix anything — this is audit only. Suggest commands to fix issues (e.g., "Run `/fix` for formatting").
