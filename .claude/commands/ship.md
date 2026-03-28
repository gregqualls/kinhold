---
description: Pre-merge audit — checks if a feature is ready to ship
---

Run the Kinhold ship checklist against the current branch. Compare what changed in this branch vs `main` and audit each item:

1. **API endpoints** — Check if new routes were added in `routes/api.php`. If yes, confirm controllers exist and work.
2. **Vue frontend** — Check if new Vue components/views were added. If yes, confirm they render (build must pass).
3. **MCP server tools** — Check if new API routes exist that don't have matching MCP tools in `mcp-server/src/tools/`. Flag any gaps.
4. **Pinia stores** — Check if new frontend features have corresponding stores.
5. **Database seeder** — Check if new models/tables were added. If yes, verify `database/seeders/` covers them with demo data.
6. **Landing page** — Remind that any new user-facing feature needs a landing page update.
7. **README** — Check if the README feature list mentions the new feature.
8. **ROADMAP.md** — Check if the feature is tracked in the roadmap.
9. **CHANGELOG.md** — Check if this work is logged in the changelog.
10. **Tests** — Check if new test files were added for the new code.

Output a checklist with pass/fail/warning for each item. Be specific about what's missing.
