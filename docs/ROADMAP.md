# Q32 Hub — Feature Roadmap

> Update this file when features move between phases or new ideas are captured.
> Each feature has a status: SCAFFOLDED | IN PROGRESS | COMPLETE | DEFERRED

## Phase 1: MVP — "Make It Work" (Current)

Goal: Get all 5 family members using the app daily. Focus on core functionality, not polish.

| Feature | Status | Notes |
|---------|--------|-------|
| Email/password authentication | SCAFFOLDED | Sanctum, registration with family creation/join |
| Family member management | SCAFFOLDED | Parent invites, role assignment |
| Unified family calendar (read-only) | SCAFFOLDED | Google Calendar aggregation, color-coded per member |
| Task management with tags | COMPLETE | Tag-based filtering replaces lists, inline editing, multi-tag support |
| Family vault with encryption | SCAFFOLDED | Categories, encrypted entries, document uploads |
| Vault permission management | SCAFFOLDED | Parent/child roles + per-item overrides |
| AI chatbot (Claude) | SCAFFOLDED | Optional via API key, queries family data |
| MCP server (full CRUD) | SCAFFOLDED | 26 tools, Sanctum token auth |
| Mobile-first responsive UI | SCAFFOLDED | Bottom nav, cards, touch-friendly |
| Docker Compose local dev | SCAFFOLDED | 5 services, one-command boot |
| Upsun production config | SCAFFOLDED | `.upsun/config.yaml` |

### MVP Milestones
1. [ ] First successful local boot (docker-compose up, migrations, seed data)
2. [ ] Register + login working end-to-end
3. [ ] Create and complete a task from the UI
4. [ ] Add a vault entry, verify encryption, verify child can/can't see it
5. [ ] Connect one Google Calendar, see events in the hub
6. [ ] Send a chatbot message, get a response using family data
7. [ ] Use MCP server from Claude to create a task
8. [ ] All 5 family members have accounts and have used it at least once

## Phase 2: "Make It Good" (Post-MVP)

Goal: Polish the experience, add quality-of-life features that make the family want to keep using it.

| Feature | Status | Notes |
|---------|--------|-------|
| Google OAuth login | DEFERRED | Sign in with Google alongside email/password |
| Two-way calendar sync | DEFERRED | Create/edit events from hub, push to Google Calendar |
| Recurring tasks | COMPLETE | Daily, weekly, monthly recurrence via RRULE + daily artisan command |
| Task categories and filters | COMPLETE | Replaced task lists with tag-based filtering system |
| Dark mode | IN PROGRESS | Toggle in Settings works, CSS architecture fixed, all views support dark:. Still needs: toggle in TopBar/mobile header for quick access |
| Push notifications | DEFERRED | Task reminders, event alerts (PWA or web push) |
| Email digest | DEFERRED | Daily/weekly summary email per family member |
| Vault audit log | DEFERRED | Track who viewed/edited vault entries and when |
| Vault version history | DEFERRED | See previous versions of vault entries |
| Improved chatbot context | DEFERRED | Better retrieval, more data sources, smarter prompts |
| PWA support | DEFERRED | Install to home screen, offline basic access |
| Family activity feed | COMPLETE | Points feed shows all family activity (task completions, kudos, purchases, badges) |

## Phase 3: "Make It Delightful" (Future)

Goal: Features that transform the app from a tool into something the family loves.

| Feature | Status | Notes |
|---------|--------|-------|
| Gamification system | COMPLETE | Points, kudos, leaderboard, badges with auto-triggers, feature toggles |
| Rewards store | COMPLETE | Parent-created prizes purchasable with points (instant purchase) |
| Real-money rewards | DEFERRED | Parents set $ values on tasks, track earnings for kids |
| Meal planning | DEFERRED | Weekly meal calendar, recipe storage |
| Grocery/shopping lists | DEFERRED | Shared lists, check-off items, maybe store aisle organization |
| Family chat/messaging | DEFERRED | In-app messaging, announcements, polls |
| Chore rotation | DEFERRED | Auto-assign rotating chores (dishes, trash, laundry) |
| Budget & expenses | DEFERRED | Family budget tracking, kid allowances, expense categories |
| School schedule integration | DEFERRED | Import school schedules, track assignments/grades |

## Phase 4: "Open Source Growth" (Aspirational)

Goal: Make Q32 Hub useful for any family, not just the Qualls family.

| Feature | Status | Notes |
|---------|--------|-------|
| Multi-family isolation | DEFERRED | Complete data isolation between families on shared instance |
| Plugin/module system | DEFERRED | Community can build and share modules |
| More calendar providers | DEFERRED | Outlook, iCloud, CalDAV |
| Mobile app (React Native) | DEFERRED | Native iOS/Android app using the same API |
| Internationalization (i18n) | DEFERRED | Multi-language support |
| Accessibility audit (a11y) | DEFERRED | WCAG 2.1 AA compliance |
| Two-factor authentication | DEFERRED | TOTP or SMS-based 2FA |
| Passkey/biometric login | DEFERRED | WebAuthn / FIDO2 support |
| Family photo gallery | DEFERRED | Shared photos organized by date/event |
| Emergency info page | DEFERRED | Quick-access emergency contacts, medical alerts, allergies |
| Shared-with-external vault | DEFERRED | Share specific vault entries with doctor, lawyer, school |
| Proactive AI reminders | DEFERRED | Chatbot sends reminders about upcoming events/tasks |
| Webhook integrations | DEFERRED | Connect to IFTTT, Zapier, Home Assistant |

## Ideas Parking Lot

> Dump new ideas here. Don't evaluate or prioritize yet. Review during weekly planning.

- (none yet — Greg will fill this up fast)
