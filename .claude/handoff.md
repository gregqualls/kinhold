# Session Handoff

**Date:** 2026-03-31
**Branch:** `main`
**Last commit:** `9071ad2` Merge pull request #111 from gregqualls/worktree-quick-wins

## What Was Done This Session

- **Self-hosting accessibility research** — Analyzed n8n's open-source model and audited all of Kinhold's external service dependencies to understand what blocks home server users.
- **3-sprint implementation plan** — Documented at `.claude/plans/self-hosting-accessibility.md` covering zero-config Docker (SQLite + 2-service compose), graceful feature degradation, and first-boot setup wizard.
- **New architecture principle** — Added "Self-hostable by default — we don't gate features, we gate operational complexity" as principle #5 in CLAUDE.md.
- **GitHub issue #113** — Created with full sprint checklists for tracking implementation.

## What's Next

1. **#113 — Self-hosting accessibility, Sprint 1** — SQLite default, `docker-compose.simple.yml`, database-agnostic entrypoint. Biggest value unlock for open-source users.
2. **#107 — Chat child safety** (CRITICAL before Corey's family joins) — Children can prompt their way to vault data. Need hard context scoping at the data layer.
3. **#65 — Shopping & grocery lists** — The #1 sticky feature for daily use.

## Blockers or Gotchas

- **DocumentResource bug**: `route('documents.download')` is not defined — vault document uploads work but response serialization fails. Pre-existing, low priority.
- **Sanctum token expiry**: Still `null` (never expires). Should be ~30 days but needs token refresh logic first.
- **Passport OAuth keys**: `PASSPORT_PRIVATE_KEY` / `PASSPORT_PUBLIC_KEY` are Upsun project-level env vars. If regenerated: use REST API, not CLI (PEM format breaks CLI parsing).
- **SQLite migration audit needed**: Before implementing Sprint 1 of #113, all 17 migrations need checking for PostgreSQL-specific syntax (`ILIKE`, `jsonb` operators, `INTERVAL`).

## Open Questions

- **Email verification strictness**: Currently soft (banner only). Should vault access eventually require verified email?
- **Vault document encryption at rest**: Uploaded files stored unencrypted on private disk. Worth addressing before Corey's family joins?
- **License**: MIT works for now, but if competitive SaaS hosting becomes a concern, BSL (Business Source License) is the recommended path. Decision deferred.
