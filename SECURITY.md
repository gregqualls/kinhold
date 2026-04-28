# Security Policy

## Reporting a Vulnerability

If you discover a security vulnerability in Kinhold, please report it responsibly. **Do not open a public GitHub issue.**

### How to Report

Email **glqualls@gmail.com** with:

- A description of the vulnerability
- Steps to reproduce it
- The potential impact
- Any suggested fix (optional)

### What to Expect

- **Acknowledgment** within 48 hours
- **Status update** within 7 days
- **Fix timeline** depends on severity — critical issues are prioritized immediately

### Scope

The following are in scope:

- Authentication and authorization bypasses
- Cross-family data access (tenant isolation)
- Vault encryption weaknesses
- SQL injection, XSS, CSRF
- MCP server authorization issues
- API endpoint security

The following are out of scope:

- Denial of service attacks
- Social engineering
- Vulnerabilities in third-party dependencies (report these upstream)
- Issues in development/demo environments

## Supported Versions

| Version | Supported |
|---------|-----------|
| Latest `main` | Yes |
| Older commits | No |

Kinhold is pre-1.0 — we recommend always running the latest version.

## Application Security Practices

- Vault sensitive fields are encrypted at rest using Laravel's encryption
- All API endpoints are scoped to the authenticated user's family
- MCP tools enforce the same authorization policies as the REST API
- Sanctum handles API authentication (cookie SPA + bearer token MCP)
- Role-based access control (parent/child) with per-item vault permissions
- Security test suite covers cross-family isolation across all controllers

## Repository Security Practices

We treat the GitHub repository itself as part of the security surface. The following protections are enabled on `gregqualls/kinhold`:

### Secret leak prevention

- **GitHub secret scanning** — flags known-provider tokens (Anthropic, Google, AWS, Stripe, Resend, etc.) anywhere in history.
- **Push protection** — blocks pushes that contain recognized secret patterns *before* they reach the remote. Devs see a rejection at `git push` time.
- **Validity checks / non-provider patterns** — enabled where available, to catch generic high-entropy strings (e.g. Laravel `APP_KEY=base64:...`) that don't match a provider signature.
- **`.gitignore` hardening** — `.env`, `.env.backup`, `.env.production`, `.env.bak*`, `*.env.bak`, `.env.local`, `.env.*.local` are all ignored. Never commit `.env` files of any kind.

### Dependency security

- **Dependabot alerts** — notifies on CVEs in our dependencies.
- **Dependabot security updates** — auto-opens PRs that patch vulnerable dependencies.
- **Dependabot version updates** — weekly grouped PRs for non-security updates across Composer, npm, GitHub Actions, and Docker base images. Config: [`.github/dependabot.yml`](.github/dependabot.yml).
- **Grouped security updates** — minor/patch fixes are bundled into one PR per ecosystem to keep noise low.

### Code analysis

- **CodeQL static analysis** — scans PHP and JS on every PR for OWASP-class issues (XSS, SQLi, path traversal, etc.).
- **CI quality gate** — `.github/workflows/ci.yml` runs PHPUnit, Pint, PHPStan, ESLint, and Vite build on every PR. Required to pass before merge.

### Branch protection

- `main` requires PRs (no direct pushes).
- Force-push and branch deletion are disabled on `main`.
- Pre-commit hook at [`scripts/hooks/pre-commit`](scripts/hooks/pre-commit) catches formatting and syntax issues locally — install with `git config core.hooksPath scripts/hooks`.

### Vulnerability reporting

- **Private vulnerability reporting** is enabled — security researchers can report issues privately via GitHub's UI without opening a public issue.

## If you accidentally commit a secret

1. **Rotate the secret immediately** — assume it is compromised. For `APP_KEY`, this also invalidates encrypted data (sessions, vault fields, OAuth tokens) so plan for downtime.
2. **Remove the file** in a new commit and push.
3. **For full history purge** (if the repo is public): use `git filter-repo --path <file> --invert-paths --force` on a `--mirror` clone, then force-push branches and tags. Restore branch protection afterward. Note that GitHub may keep the old commit SHA reachable by direct URL for a short window — contact GitHub Support to expedite removal if needed.
4. **Tell maintainers** so we can audit downstream impact (forks, deploy logs, etc.).

## For self-hosted operators

If you run your own Kinhold instance, you are responsible for:

- Generating a unique `APP_KEY` (`php artisan key:generate`) and never sharing it.
- Keeping your `.env` out of version control.
- Setting `APP_DEBUG=false` in production — debug mode leaks stack traces and config.
- Using HTTPS for all traffic (Upsun, Cloudflare, or your reverse proxy handles this).
- Rotating credentials if a contributor's machine is compromised.
- Subscribing to repo releases so you get patched promptly.
