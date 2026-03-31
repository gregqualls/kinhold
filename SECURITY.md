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

## Security Practices

- Vault sensitive fields are encrypted at rest using Laravel's encryption
- All API endpoints are scoped to the authenticated user's family
- MCP tools enforce the same authorization policies as the REST API
- Sanctum handles API authentication (cookie SPA + bearer token MCP)
- Role-based access control (parent/child) with per-item vault permissions
- Security test suite covers cross-family isolation across all controllers
