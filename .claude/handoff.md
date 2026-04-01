# Session Handoff

**Date:** 2026-04-01
**Branch:** claude/awesome-feynman (PR #120)
**Last commit:** ef8c32a fix: chat multiline input + WYSIWYG heading buttons

## What Was Done This Session
- **Vault overhaul** — Fixed 9 CRUD bugs, replaced key/value field design with Milkdown WYSIWYG markdown editor + optional sensitive fields. Entries now store markdown body.
- **Category CRUD** — Create, edit, delete custom categories with 10 icon options. Backend, frontend, MCP all wired up. Family-scoped route binding.
- **Kids personal vault** — `is_personal` flag + migration. Children can CRUD their own personal entries. Parents see everything. Policy + MCP enforced.
- **Vault playbooks** — 5 community-contributable `.md` files for AI-guided data entry (house manual, medical, vehicle, school, emergency). 2 new MCP tools (`list-playbooks`, `get-playbook`). Agent system prompt updated.
- **Agent hallucination guard** — Server-side `claimsAction()` check rejects responses that claim actions without tool calls. Logs warning, injects correction message, forces retry. Fixed a real bug where agent was faking vault creation success.
- **Chat UX** — Multiline textarea with Shift+Enter, error messages on failure instead of vanishing messages, 120s timeout for agent loop.
- **Permissions + upload UI** — Share button with family member dropdown, document upload with progress indicator.

## Quality State
- Tests: 45 tests, 90 assertions (pass, 2 deprecations)
- Pint: pass
- Larastan: pass (0 new errors, 209 baselined)
- ESLint: pass (0 errors, 48 warnings — all pre-existing no-unused-vars)
- Build: pass (3175 modules)
- CI: all 3 checks green on PR #120

## What's Next
1. **Merge PR #120** and deploy to production
2. **Vault RAG** (Phase 4) — `SearchVault` MCP tool for keyword search across decrypted entries. Agent can answer "What's our WiFi password?"
3. **Seeder update** — Convert demo vault entries to new markdown body format (currently legacy flat key/value)
4. **Test coverage** — No new tests in this PR. Category CRUD, playbook tools, personal entry policy, and hallucination guard all need test coverage.
5. **Bundle size** — Milkdown added ~380KB gzipped to the main JS chunk. Code-split it into a lazy-loaded chunk for vault routes only.

## Blockers or Gotchas
- Agent hallucination guard uses keyword detection (`claimsAction`) — not perfect. Words like "created" in legitimate responses (e.g., "here's what I created") could trigger false positives after tools were already used. Current guard only triggers when `toolsUsed` is empty, so this should be rare.
- Milkdown GFM plugin (tables) conflicts with commonmark — `RangeError: Duplicate use of selection JSON ID cell`. Tables are disabled for now. Would need to investigate Milkdown issue tracker.
- Pint formats differently on PHP 8.4 (CI) vs PHP 8.5 (local) — watch for `array_indentation` issues.
- `.environment` must NOT be renamed — Upsun auto-sources it during deploy.

## Open Questions
- Should the vault playbooks eventually support a more interactive flow (multiple choice, fill-in-the-blank one at a time)? Greg expressed interest but current implementation asks all questions at once.
- Should the seeded demo data be updated to use the new markdown format? Low priority since real users won't have legacy data.
