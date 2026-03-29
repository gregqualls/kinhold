# Session Handoff

**Date:** 2026-03-29
**Branch:** `security/audit-and-fixes` (PR #110, pending merge to main)
**Last commit:** `8a52cc7` feat: Google account linking + email verification on registration

## What Was Done This Session

- **Security audit**: Found and fixed 22 vulnerabilities (3 Critical, 6 High, 8 Medium, 5 Low). Biggest: cross-family data access in vault/tasks/rewards/badges, OAuth token leaked in URL, no login rate limiting.
- **Google account linking**: Email/password users can link Google via Settings or via password confirmation when attempting Google sign-in (instead of being rejected).
- **Email verification on registration**: New users get a verification email. Dismissable banner until verified. Existing users grandfathered.
- **41 automated tests**: Security isolation (31), Google link (5), email verification (5). All passing. Model factories created.

## What's Next

1. **Merge PR #110** — Test in Upsun preview environment first (OAuth code exchange flow, rate limiting, Google account linking). Then merge to main.
2. **#107 — Chat child safety** (CRITICAL before Corey's family joins) — Children can currently prompt their way to vault data. Need hard context scoping at the data layer.
3. **#65 — Shopping & grocery lists** — The #1 sticky feature for daily use.

## Blockers or Gotchas

- **SPA auth code exchange**: Google OAuth now uses a code→token exchange instead of token-in-URL. The SPA reads `?code=` from the URL and POSTs to `/api/v1/auth/exchange`. Must be tested end-to-end in the preview environment before merging.
- **DocumentResource bug**: `route('documents.download')` is not defined — vault document uploads work but response serialization fails. Pre-existing bug, low priority.
- **Sanctum token expiry**: Still `null` (never expires) in `config/sanctum.php`. Should be set to ~30 days but needs token refresh logic first.
- **Passport OAuth keys**: `PASSPORT_PRIVATE_KEY` / `PASSPORT_PUBLIC_KEY` are Upsun project-level env vars. If regenerated: use REST API, not CLI (PEM format breaks CLI parsing).
- **Anthropic API key**: Only has Claude 4.x models. Correct default is `claude-sonnet-4-5-20250929`.

## Open Questions

- **Email verification strictness**: Currently soft (banner only). Should vault access eventually require verified email?
- **Vault document encryption at rest**: Uploaded files (PDFs, images) are stored unencrypted on the private disk. Structured data fields are encrypted. Worth addressing before Corey's family joins?
