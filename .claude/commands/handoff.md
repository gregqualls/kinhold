---
description: End-of-session handoff — captures context for the next session before cleanup
---

Write a session handoff note so the next Claude session can pick up without a cold start.

## What to Capture

Read the current state and write `.claude/handoff.md` with this structure:

```markdown
# Session Handoff

**Date:** YYYY-MM-DD
**Branch:** (current branch or "main" if already merged)
**Last commit:** (short hash + message)

## What Was Done This Session
(Summarize the work completed — bullet points, 3-5 items max)

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

1. **Check CHANGELOG.md** — If the current session's work isn't logged yet, update CHANGELOG.md first. Ask Greg what was accomplished if unclear.
2. **Check ROADMAP.md** — If any features moved status, update the roadmap.
3. **Check CLAUDE.md** — If any architectural decisions changed (new modules, tech stack changes, schema changes), update CLAUDE.md.
4. **Write `.claude/handoff.md`** — Overwrite the previous handoff with the current session's context.
5. **Confirm with Greg** — Show the handoff note and ask if anything is missing before cleanup.

## Rules
- Always update CHANGELOG before writing the handoff. The handoff references the changelog, not the other way around.
- Keep the handoff concise — it's a briefing, not a novel.
- The "What's Next" section is the most important part. Be specific. "Continue UI work" is bad. "Implement shopping list module (issue #65)" is good.
- This command does NOT do any git operations. That's /cleanup's job.
- If Greg says "just clean up" without running this first, /cleanup should remind him.
