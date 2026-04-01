# Session Handoff

**Date:** 2026-04-01
**Branch:** feature/106-chat-to-agent
**Last commit:** ea99f82 fix: task tag sync fails due to UUID pivot table

## What Was Done This Session
- Replaced freeform chatbot with MCP-powered agent — natural language → Claude tool_use → executes 18 MCP tools → returns formatted results
- Built AgentService (orchestration loop) + ToolRegistry (schema mapping + execution) + AnthropicProvider.askWithTools()
- Renamed Chat → Assistant across all navigation with CpuChipIcon, markdown rendering, accuracy disclaimer, clarifying questions
- Fixed pre-existing task tag sync bug (UUID pivot table missing model)
- Closed 4 stale issues (#113, #108, #107, #109), repurposed #106 with agent vision
- Removed dead ChatbotService

## Quality State
- Tests: 45 tests, 90 assertions (pass, 2 deprecations)
- Pint: pass
- Larastan: pass (0 new errors, 198 baselined — down from 213 after removing ChatbotService)
- ESLint: pass (0 errors, 49 warnings — all pre-existing no-unused-vars)
- Build: pass (2344 modules)
- CI: all 4 checks green on PR #119

## What's Next
1. **Versioning + GitHub Releases** (Issue #117) — semantic versioning, release workflow, self-hosted update notifications
2. **Vault rethink** — Greg is considering RAG for vault data so the agent can reason over it, possibly integrating an OSS project. Undecided.
3. **Address ESLint warnings** — 49 unused vars/imports to clean up
4. **Address PHPStan baseline** — chip away at 198 baselined errors over time

## Blockers or Gotchas
- Agent only supports Anthropic (tool_use is provider-specific). Families using OpenAI/Google BYOK keys won't get the agent — they'll get a RuntimeException. May need a graceful fallback or message.
- `.environment` must NOT be renamed — Upsun auto-sources it during deploy
- Pint formats differently on PHP 8.4 (CI) vs PHP 8.5 (local) — watch for `array_indentation` issues

## Open Questions
- Greg is rethinking the vault — should the agent eventually access vault data via RAG? Separate issue needed when ready.
