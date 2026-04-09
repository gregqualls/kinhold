# Session Handoff

**Date:** 2026-04-09
**Branch:** feature/117-142-versioning-docker (PR #146 open)
**Last commit:** `85366d5 feat: versioning, update notifications, Docker polish (#117, #142)`

## What Was Done This Session
- **Merged robots.txt fix** from previous session's stale branch into main, cleaned up stale worktree
- **Versioning (#117):** `config/version.php` (v1.0.0 default, overridable via `APP_VERSION`). `UpdateCheckService` polls GitHub Releases API once/day (24h cache, 5s timeout, fail-safe null on errors, `DISABLE_UPDATE_CHECK=true` opt-out). Version + update status wired into `/api/v1/config` (public) and MCP `get-settings`. New "About Kinhold" section in Settings — parent view shows version, license, update banner (dismissible per-version via localStorage) + GitHub/releases/website links; child view shows version only.
- **GitHub Actions release workflow (#117):** `.github/workflows/release.yml` triggers on `v*` tag push, auto-creates Release with `softprops/action-gh-release@v2` and `generate_release_notes: true`.
- **Docker polish (#142):** `.env.docker-simple` now defaults to `APP_ENV=production`, `APP_DEBUG=false`, `SESSION_DRIVER=database`. Created `.dockerignore` (excludes .git, node_modules, vendor, tests, docs, dev tooling). Added `DISABLE_UPDATE_CHECK` to both env files.
- **PR #146 open:** https://github.com/gregqualls/kinhold/pull/146 — CI running.

## Quality State
- **Tests:** 60 tests, 118 assertions — PASS (2 deprecations, not blocking)
- **Pint:** PASS
- **Larastan:** PASS (0 errors)
- **ESLint:** PASS (0 issues)
- **Build:** PASS (3198 modules, 2.79s)

## What's Next
1. **Merge PR #146** — CI should be green. Run `/merge` to close out Day 2.
2. **Tag v1.0.0** — After merge, run `git tag v1.0.0 && git push origin v1.0.0` to trigger the first GitHub Release via the new release workflow.
3. **Day 3 — Demo experience (#124, #126, #143):** Refresh seed data (demo family outdated), fix demo email verification banner bug, add "Install on your server" CTA banner. All P1 for the v1.0.0 launch (deadline April 11).

## Blockers or Gotchas
- **v1.0.0 Launch deadline is April 11** — 2 days away. Day 3 issues (#124, #126, #143) need to ship tomorrow.
- **Update check hits public config endpoint** — The `UpdateCheckService::getStatus()` is called on every `/api/v1/config` request (unauthenticated). Cache is 24h so it's only a real HTTP call once per day, but the first request of the day has a 5s timeout. Acceptable for now, could move to Settings-only if it becomes a problem.
- **No `v1.0.0` tag exists yet** — The release workflow won't fire until someone pushes a tag. Do this right after merging PR #146.

## Open Questions
- None — Day 2 fully executed. Just needs merge + tag.
