---
name: Dashboard Builder
description: Guide the user through creating a personalized dashboard layout
category: dashboard
icon: squares-2x2
tags:
  - dashboard
  - setup
  - layout
---

# Dashboard Builder Playbook

Help the user create a personalized dashboard by asking about their priorities and building a widget layout.

## Step 1: Get Current Config
Use `manage-dashboard` with action `get` to see what the user currently has.

## Step 2: Ask About Priorities
Ask the user what matters most to them. Suggest options based on their role:

**For parents:**
- Family calendar and upcoming events
- Task management and open family tasks
- Points leaderboard and rewards
- Quick access to vault and assistant

**For kids:**
- My tasks and points balance
- Badges and achievements
- Rewards shop preview
- Leaderboard ranking

## Step 3: Build the Config
Based on their answers, construct a dashboard config:

- Start with `welcome` (lg) at the top
- Add `countdown` (lg) if they care about upcoming events
- Add the most important widgets first (they appear higher on the page)
- Use `md` or `lg` size for primary widgets, `sm` for secondary
- Keep it to 6-10 widgets — don't overwhelm

### Widget Quick Reference

| Type | Best For | Endpoint | Key Settings |
|------|----------|----------|-------------|
| stat | Single number (points, task count) | /api/v1/points/bank | valueKey, icon, suffix |
| list | Tasks, rewards, badges, vault items | /api/v1/tasks | limit, showDueDate, showPoints, completable, viewAllPath, emptyMessage |
| leaderboard | Points ranking | /api/v1/points/leaderboard | limit |
| feed | Activity stream | /api/v1/points/feed | limit |
| calendar-mini | Today's schedule | /api/v1/calendar/events | limit, viewAllPath (params: range=today) |
| quick-actions | Shortcut buttons | null | actions array of {label, icon, path} |
| progress | Completion metrics | /api/v1/tasks | label, valueKey, maxKey |
| countdown | Next event countdown | /api/v1/featured-events/countdown | — |
| welcome | Greeting + celebrations | null | — |

### Common Task List Params
- My tasks: `{ assigned_to: "me", status: "pending" }`
- Open tasks: `{ is_family_task: true, status: "pending" }`
- All pending: `{ status: "pending" }`

## Step 4: Save
Use `manage-dashboard` with action `set` to save the full config. Confirm to the user what you built.
