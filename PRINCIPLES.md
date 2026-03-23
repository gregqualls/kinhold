# Q32 Hub — Core Product Principles

These principles guide every feature, design decision, and pull request in Q32 Hub. If a change conflicts with these principles, it needs a strong justification before merging.

## 1. Easy for busy parents

If a parent can't figure it out in 30 seconds, it's too complex.

Use progressive disclosure: show the basics first, let power features be discovered over time. A mom checking tasks at a red light shouldn't need a tutorial. Features should have sensible defaults and work immediately — configuration is optional, not required.

## 2. Every family, every age

Most family apps target households with little kids. Q32 Hub works for all of them — toddlers to teenagers to adult children still on the family plan.

A family of three and a family of ten should both feel at home. Gamification motivates a 7-year-old and a 17-year-old differently, so features should scale and adapt rather than assume one family shape. Don't design for the "ideal" family — design for real ones.

## 3. Accessible to everyone

The app must be usable by people with all types of disabilities — visual, motor, cognitive, or otherwise.

Follow WCAG guidelines. Use semantic HTML, proper ARIA labels, sufficient color contrast, keyboard navigation, and screen reader support. Accessibility isn't a nice-to-have or a future phase — it's baked into every component from day one. If it's not accessible, it's not done.

## 4. Secure by design

This app stores SSNs, medical records, and financial data. Treat every feature with that in mind.

Sensitive data is encrypted at rest. The vault uses field-level encryption. Tokens and credentials are never exposed in logs, URLs, or client-side storage. Self-hosting means families own their data — no third party can access it. Security isn't a layer on top; it's the foundation everything is built on.

## 5. Parents are in control

Parents have full visibility and authority over the family's data, features, and access.

Feature toggles let parents enable or disable any module. Per-item permissions control what children can see (especially in the vault). Managed accounts let parents create child profiles without email. Parental controls should be powerful but not burdensome — sane defaults with the ability to lock things down when needed.

## 6. AI-native

AI assistants are first-class users of this app, not an afterthought.

People increasingly manage their lives through AI — asking Claude to add a task, check the calendar, or look up insurance info. Every feature must be fully accessible to AI agents through the MCP server and REST API. If a human can do it in the UI, an AI should be able to do it through the API. The app works great without AI, but it's designed to be exceptional with it.

## 7. API-first

Everything goes through the REST API. The web app and MCP server are equal consumers.

No feature should exist only in the UI or only in the API. This keeps the architecture clean, enables third-party integrations, and means power users can manage their family hub from Claude, scripts, or future mobile apps without waiting for UI work.

## 8. Mobile-first

Design for the phone in a parent's hand, not the desktop in the office.

Every view starts at 375px and scales up. Touch targets are generous. Navigation is thumb-friendly. Desktop gets more space, not a different experience. If it doesn't work well on a phone, it doesn't ship.

## 9. Privacy by default

Your family's data belongs to you.

Q32 Hub is self-hostable, open source, and collects no telemetry. No ads, no tracking, no selling data. The hosted version at family.qthirtytwo.com follows the same rules — we make money from subscriptions, not surveillance. Users should never have to wonder who can see their information.

## 10. Open source, open community

MIT license. Transparent roadmap. Welcoming to contributors.

We build in the open because families deserve to know what software is running in their home. Contributors should find clear documentation, consistent code patterns, and a codebase that's approachable — not a wall of abstraction.

---

## How to use these principles

- **Building a new feature?** Run it through each principle. Does it pass the 30-second test? Is it accessible? Does it work on mobile? Can an AI agent use it? Are parents in control of it?
- **Reviewing a PR?** Check that it doesn't violate any principle. Complexity that doesn't serve busy parents should be simplified or deferred.
- **Prioritizing the roadmap?** Features that reinforce multiple principles get higher priority than features that only serve one.
