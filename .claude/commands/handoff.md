---
description: End-of-session handoff — captures context for the next session before cleanup
model: sonnet
---

Write a session handoff note so the next Claude session can pick up without a cold start. Runs BEFORE merge — capture context while you still have it.

## What to Capture

Read the current state and write `.claude/handoff.md` with this structure:

```markdown
# Session Handoff

**Date:** YYYY-MM-DD
**Branch:** (current branch or "main" if already merged)
**Last commit:** (short hash + message)

## What Was Done This Session
(Summarize the work completed — bullet points, 3-5 items max)

## Quality State
- Tests: X tests, Y assertions (pass/fail)
- Pint: pass/fail
- Larastan: pass/fail (N errors)
- ESLint: pass/fail (N errors, M warnings)
- Build: pass/fail (N modules)

## What's Next
(The most important 1-3 things to work on next session, in priority order.
Pull from the ROADMAP, open GitHub issues, or anything Greg mentioned.)

## Blockers or Gotchas
(Anything the next session needs to know — broken tests, pending PRs,
env issues, decisions that need Greg's input, etc. "None" is fine.)

## Open Questions
(Anything unresolved from this session that Greg should weigh in on.)
```

## Steps

1. **Run quality snapshot** — Silently run the quality checks to capture current state:
   - `./vendor/bin/phpunit` — capture test count, assertion count, pass/fail
   - `./vendor/bin/pint --test` — pass/fail
   - `./vendor/bin/phpstan analyse --memory-limit=512M` — error count
   - `npx eslint resources/js/` — error/warning count
   - `npx vite build` — module count, pass/fail
   - Don't block the handoff if any check fails — just record the state.

2. **Check CHANGELOG.md** — If the current session's work isn't logged yet, update CHANGELOG.md first. Ask Greg what was accomplished if unclear.

3. **Check ROADMAP.md** — If any features moved status, update the roadmap.

4. **Check CLAUDE.md** — If any architectural decisions changed (new modules, tech stack changes, schema changes), update CLAUDE.md.

5. **Write `.claude/handoff.md`** — Overwrite the previous handoff with the current session's context. Include the Quality State section from step 1.

6. **Confirm with Greg** — Show the handoff note and ask if anything is missing.

## Rules
- Always update CHANGELOG before writing the handoff. The handoff references the changelog, not the other way around.
- Keep the handoff concise — it's a briefing, not a novel.
- The "What's Next" section is the most important part. Be specific. "Continue UI work" is bad. "Implement shopping list module (issue #65)" is good.
- The Quality State section is informational — don't block the handoff on failing checks. Just record what's happening.
- This command does NOT do any git operations. That's /cleanup's job.
- This command runs BEFORE merge. Don't assume the PR is merged.
- If Greg says "just clean up" without running this first, /cleanup should remind him.
