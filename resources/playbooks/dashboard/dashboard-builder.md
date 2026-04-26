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

Help the user create a personalized dashboard by asking about their priorities.

## Step 1: Get Current Config
Use `manage-dashboard` with action `get` to see current widgets and available types/sizes.

## Step 2: Ask About Priorities
Ask what matters most. Suggest based on role:

**Parents:** Calendar, task management, leaderboard, rewards
**Kids:** My tasks, points balance, badges, rewards shop

## Step 3: Build the Config
Construct a dashboard with version 2. Each widget only needs type and size.

### Widget Reference

| Type | Sizes | Best For |
|------|-------|----------|
| welcome | lg | Greeting (keep at top) |
| countdown | lg | Event countdown (keep near top) |
| my-tasks | sm, md, lg | Personal task list with checkboxes |
| family-tasks | sm, md | Open tasks anyone can grab |
| todays-schedule | sm, md | Today's calendar events |
| points-summary | sm | Compact point balance |
| leaderboard | sm, md | sm: compact podium, md: full animated |
| activity-feed | sm, md | Recent point transactions |
| rewards-shop | sm, md | Reward cards with prices |
| badge-collection | sm, md | Badge grid (earned + locked) |
| quick-actions | sm | Navigation shortcuts |

### Tips
- Start with welcome (lg) and countdown (lg) at top
- Put most important widgets first
- Keep to 8-10 widgets max
- Use md for primary widgets, sm for secondary

## Step 4: Save
Use `manage-dashboard` with action `set`. Confirm what you built.
