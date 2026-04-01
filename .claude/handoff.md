# Session Handoff

**Date:** 2026-04-01
**Branch:** main (PRs #115 and #116 merged)
**Last commit:** 5fb90f7 Merge pull request #116 from gregqualls/chore/open-source-hygiene

## What Was Done This Session
- **Self-hosting infrastructure (PR #115)** — Zero-dependency Docker setup with SQLite, single-container `docker-compose.simple.yml`, `setup-simple.sh` bootstrap script, auto APP_KEY generation, graceful feature degradation (Google OAuth/Calendar/AI Chat hide when not configured), first-boot auto-redirect, comprehensive `SELF-HOSTING.md` guide. Dockerfile bumped to PHP 8.4.
- **Open-source hygiene (PR #116)** — Fixed license from MIT → Elastic License 2.0 everywhere, added CODE_OF_CONDUCT.md, SECURITY.md, GitHub Actions CI (PHPUnit + Vite build on PR/push), PR template. Fixed phpunit.xml for PHPUnit 11, fixed family factory slug uniqueness.
- **Versioning issue created (#117)** — Semantic versioning, GitHub Releases workflow, and self-hosted update notification banner. Greg said "we are close to needing that in place."

## What's Next
1. **Versioning + update notifications (#117)** — Greg flagged this as near-term priority. Semver in app config, git tags, release workflow, self-hosted update check banner.
2. **Multi-tenant audit before Corey signs up** — Verify all controllers/policies enforce family_id scoping.
3. **Phase A: Shopping lists (#65)** — The #1 daily-driver feature across all competitors.

## Blockers or Gotchas
- **PHP 8.4 required** — `composer.lock` has Symfony 8.x packages requiring PHP 8.4+. Dockerfile already updated. Local dev runs PHP 8.5.
- **`php` and `node` not on default PATH in sandbox** — Use `/opt/homebrew/bin/php` and `/opt/homebrew/Cellar/node/25.8.0/bin/node` (or check `.claude/launch.json` for current paths).
- **CI uses SQLite in-memory** — Tests must be SQLite-compatible. Family factory slug uniqueness was fixed for this.
- **DocumentResource bug**: `route('documents.download')` is not defined — vault document uploads work but response serialization fails. Pre-existing, low priority.
- **License is Elastic License 2.0** — not MIT. This was corrected across all files this session.

## Open Questions
- None — clean session.
