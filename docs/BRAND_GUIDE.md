# Kinhold — Brand Guide

> The definitive reference for Kinhold's visual identity, tone, and design system.
> Every component, page, and piece of content should align with this guide.

---

## Brand Essence

**Kinhold** is your family's private hub — calendar, tasks, vault, and AI, all in one place, all under your control.

The name combines **kin** (your people) and **hold** (safekeeping, a stronghold). It communicates family, security, and togetherness without being childish or exclusionary.

### Brand Personality

| Trait | What It Means | What It's NOT |
|-------|--------------|---------------|
| **Premium** | Feels like an Apple product — clean, confident, luxurious | Not corporate or enterprise-y |
| **Warm** | Inviting, human, feels like home | Not cold, sterile, or clinical |
| **Modern** | Current design language — 2026, not 2016 | Not trendy/flashy (no gradients-for-gradients-sake) |
| **Trustworthy** | You'd put your SSN in here without hesitating | Not playful with security |
| **Inclusive** | Works for a family of 3 or 10, ages 5 to 50 | Not "mom with toddlers" energy |

### Design North Stars

These products represent the quality bar and aesthetic direction:

- **Claude.ai** — warm, literary, sophisticated dark mode, generous spacing
- **Linear** — precise, tool-like, confident, dark-mode-first
- **Apple product pages** — premium breathing room, typography-driven hierarchy
- **Notion** — clean and flexible, feels like yours
- **Discord** — multi-age appeal, personalization layers on a mature base

---

## Logo & Wordmark

### Wordmark

The primary brand mark is the **Kinhold wordmark** set in **Plus Jakarta Sans, SemiBold (600)**.

- Use sentence case: **Kinhold** (capital K, lowercase inhold)
- Minimum size: 16px on screen, 12pt in print
- Always surrounded by clear space equal to the height of the "K"
- Never stretch, rotate, add effects, or set in a different typeface

### Logo Mark (Future)

A standalone icon/mark to be designed. Should work at 16x16 favicon size and as an app icon. Direction: geometric, minimal, could reference the "hold" concept (a shield, a container, an embrace) without being literal.

### Logo Colors

- **Light backgrounds:** Use the near-black text color (#1C1C1E)
- **Dark backgrounds:** Use the warm off-white (#FAF8F5)
- **Accent usage:** The gold accent (#C4975A) may be used for the logo mark (not the wordmark)

---

## Color System

### Philosophy

Kinhold's palette is **restrained and warm**. Premium means knowing when NOT to add color. The palette should feel like a high-end hotel lobby — neutral, sophisticated, with intentional pops of warmth.

### Core Palette

#### Light Mode

| Role | Color | Hex | Usage |
|------|-------|-----|-------|
| **Background** | Warm Ivory | `#FAF8F5` | Page backgrounds, app shell |
| **Surface** | Warm White | `#FFFFFF` | Cards, modals, elevated surfaces |
| **Surface Alt** | Soft Cream | `#F5F2EE` | Secondary surfaces, table rows, sidebar |
| **Border** | Warm Gray | `#E8E4DF` | Card borders, dividers (subtle) |
| **Text Primary** | Near Black | `#1C1C1E` | Headings, body text |
| **Text Secondary** | Warm Gray | `#6B6966` | Labels, captions, metadata |
| **Text Tertiary** | Light Gray | `#9C9895` | Placeholders, disabled text |

#### Dark Mode

| Role | Color | Hex | Usage |
|------|-------|-----|-------|
| **Background** | Rich Black | `#121214` | Page backgrounds, app shell |
| **Surface** | Dark Elevated | `#1C1C20` | Cards, modals, elevated surfaces |
| **Surface Alt** | Dark Subtle | `#252528` | Secondary surfaces, table rows, sidebar |
| **Border** | Dark Border | `#2E2E32` | Card borders, dividers (subtle) |
| **Text Primary** | Warm Off-White | `#F0EDE9` | Headings, body text |
| **Text Secondary** | Muted Gray | `#9C9895` | Labels, captions, metadata |
| **Text Tertiary** | Dark Gray | `#6B6966` | Placeholders, disabled text |

#### Accent Colors

| Role | Color | Hex | Usage |
|------|-------|-----|-------|
| **Gold (Primary Accent)** | Muted Gold | `#C4975A` | Primary CTAs, active states, brand signature |
| **Gold Hover** | Light Gold | `#D4A96A` | Button hover states |
| **Gold Subtle** | Faded Gold | `#C4975A1A` | Gold tinted backgrounds (10% opacity) |
| **Success** | Sage Green | `#5B8C6A` | Completions, positive states |
| **Warning** | Warm Amber | `#C48B3F` | Alerts, approaching deadlines |
| **Error** | Muted Rose | `#C45B5B` | Errors, destructive actions |
| **Info** | Slate Blue | `#5B7B9C` | Informational, links |

#### Family Member Colors

Each family member selects their own accent color for calendar events, avatars, and task assignments. Provide a curated palette of 12 options that work in both light and dark mode with sufficient contrast:

| Color | Hex | Name |
|-------|-----|------|
| `#5B8C9C` | Teal | |
| `#7B6B9C` | Lavender | |
| `#9C5B5B` | Rose | |
| `#5B9C7B` | Sage | |
| `#C48B5B` | Amber | |
| `#5B6B9C` | Steel Blue | |
| `#9C7B5B` | Sienna | |
| `#8B5B9C` | Plum | |
| `#5B9C9C` | Cyan | |
| `#9C8B5B` | Olive | |
| `#9C5B7B` | Berry | |
| `#6B8B5B` | Forest | |

These should be muted enough to feel premium (not crayon-bright) but distinct enough to tell apart at a glance.

---

## Typography

### Font Stack

| Role | Font | Weight | Fallback |
|------|------|--------|----------|
| **Headings** | Plus Jakarta Sans | 600 (SemiBold), 700 (Bold) | system-ui, sans-serif |
| **Body** | Inter | 400 (Regular), 500 (Medium), 600 (SemiBold) | system-ui, sans-serif |
| **Monospace** | JetBrains Mono | 400 (Regular), 500 (Medium) | ui-monospace, monospace |

All three are available as variable fonts on Google Fonts (free, SIL Open Font License).

### Why These Fonts

- **Plus Jakarta Sans** — geometric warmth with subtle personality. The word "Kinhold" set in SemiBold has a confident, distinctive feel. Premium without being cold. More character than Inter, less quirky than Outfit.
- **Inter** — the industry standard for screen readability. Designed for UI at small sizes. Highest x-height ratio of popular sans-serifs. Pairs seamlessly with Plus Jakarta Sans because they share geometric DNA.
- **JetBrains Mono** — modern monospace for vault data, masked fields (SSN: •••-••-1234), and structured data. Looks clean, not "hacker terminal."

### Type Scale

Use a modular scale based on 1.25 ratio (Major Third):

| Level | Size | Weight | Font | Line Height | Usage |
|-------|------|--------|------|-------------|-------|
| **Display** | 36px / 2.25rem | 700 | Plus Jakarta Sans | 1.2 | Hero headings, landing page |
| **H1** | 30px / 1.875rem | 700 | Plus Jakarta Sans | 1.25 | Page titles |
| **H2** | 24px / 1.5rem | 600 | Plus Jakarta Sans | 1.3 | Section headings |
| **H3** | 20px / 1.25rem | 600 | Plus Jakarta Sans | 1.35 | Card titles, subsections |
| **H4** | 16px / 1rem | 600 | Plus Jakarta Sans | 1.4 | Small headings, labels |
| **Body** | 16px / 1rem | 400 | Inter | 1.6 | Default body text |
| **Body Small** | 14px / 0.875rem | 400 | Inter | 1.5 | Secondary text, table cells |
| **Caption** | 12px / 0.75rem | 500 | Inter | 1.4 | Timestamps, metadata, hints |
| **Mono** | 14px / 0.875rem | 400 | JetBrains Mono | 1.5 | Vault fields, data, codes |

### Typography Rules

1. **Never use all-caps for body text.** Small caps or uppercase is acceptable for labels and badges only.
2. **Headings use Plus Jakarta Sans. Everything else uses Inter.** Don't mix them.
3. **Minimum body text size on mobile: 16px.** This prevents iOS zoom on input focus and ensures readability.
4. **Line length:** Aim for 60-75 characters per line on desktop. Use `max-w-prose` in Tailwind.

---

## Spacing & Layout

### Spacing Scale

Use Tailwind's default spacing scale (4px base). Preferred values:

| Token | Size | Usage |
|-------|------|-------|
| `space-1` | 4px | Tight gaps (icon + label) |
| `space-2` | 8px | Inner padding (badges, tags) |
| `space-3` | 12px | Input padding, small card padding |
| `space-4` | 16px | Standard card padding, section gaps |
| `space-6` | 24px | Card padding (desktop), between sections |
| `space-8` | 32px | Between major sections |
| `space-12` | 48px | Page-level vertical rhythm |
| `space-16` | 64px | Hero spacing, large separations |

### Layout Principles

1. **Generous padding.** When in doubt, add more space. Premium = breathing room.
2. **Card-based UI.** Content lives in cards with rounded corners and subtle borders.
3. **Max content width:** 1280px (`max-w-7xl`) for the main content area.
4. **Mobile-first breakpoints:** 375px → 640px → 768px → 1024px → 1280px.

### Border Radius

| Element | Radius | Tailwind |
|---------|--------|----------|
| **Cards** | 12px | `rounded-xl` |
| **Buttons** | 10px | `rounded-[10px]` |
| **Inputs** | 10px | `rounded-[10px]` |
| **Badges/Tags** | 9999px (pill) | `rounded-full` |
| **Avatars** | 9999px (circle) | `rounded-full` |
| **Modals** | 16px | `rounded-2xl` |
| **Tooltips** | 8px | `rounded-lg` |

---

## Components

### Buttons

**Primary (Gold accent):**
- Background: `#C4975A`, text: `#FFFFFF`
- Hover: `#D4A96A`
- Active: `#B38A50`
- Padding: `12px 24px` (h-12, px-6)
- Font: Inter SemiBold (600), 14px
- Rounded: 10px
- Subtle shadow on hover, smooth 150ms transition

**Secondary (Outlined):**
- Background: transparent, border: 1px solid current border color
- Text: primary text color
- Hover: surface-alt background
- Same sizing as primary

**Ghost (Text only):**
- No background, no border
- Text: secondary text color
- Hover: subtle background tint

**Destructive:**
- Background: `#C45B5B`, text: `#FFFFFF`
- Only for irreversible actions (delete, remove)
- Always requires confirmation

### Cards

- Background: surface color
- Border: 1px solid border color
- Border-radius: 12px (`rounded-xl`)
- Padding: 16px mobile, 24px desktop
- No drop shadow by default (shadow on hover or for elevated cards only)
- Dark mode: slightly lighter surface, same subtle border

### Inputs

- Background: surface-alt color
- Border: 1px solid border color
- Border-radius: 10px
- Padding: 12px 16px
- Focus: gold accent border, subtle gold glow (`ring-1 ring-[#C4975A]/30`)
- Font: Inter Regular, 16px (prevents iOS zoom)
- Labels above input, Inter Medium 14px, secondary text color

### Avatars

- Circle, always
- Sizes: 24px (inline), 32px (list items), 40px (cards), 64px (profile)
- Border: 2px solid with the family member's chosen color
- Fallback: first letter of name on a tinted background of their color

---

## Iconography

- Use **Heroicons** (outline style, 24px) for navigation and actions
- Stroke width: 1.5px (the default outline weight)
- Color: secondary text color, primary text color on hover/active
- Do NOT use filled icons except for active navigation states
- Do NOT use emoji as icons (except in user-generated content)
- Badge icons: geometric SVG, 20 preset shapes (existing system)

---

## Motion & Animation

### Philosophy

Motion should feel **intentional and premium** — like a luxury car door closing, not a bouncy ball.

### Timing

| Type | Duration | Easing | Usage |
|------|----------|--------|-------|
| **Micro** | 100-150ms | ease-out | Button hover, toggle, focus ring |
| **Standard** | 200-250ms | ease-in-out | Card expansion, dropdown open, tab switch |
| **Emphasis** | 300-400ms | ease-in-out | Modal open/close, page transitions |
| **Celebration** | 500-800ms | spring | Badge earned, task completed, points awarded |

### Rules

1. **No animation without purpose.** Every motion should communicate a state change.
2. **Celebration moments are earned.** Badge unlocks and point milestones get the most expressive animations — these are the "cool" moments for teens.
3. **Security actions feel deliberate.** Vault access, sensitive data reveal — use the "Standard" timing. Don't make it feel instant. A brief pause communicates "this is important."
4. **Reduce motion:** Respect `prefers-reduced-motion`. All animations must degrade to instant state changes.

---

## Gamification Design

### The Rule: Steam, Not Stickers

Gamification should feel like **Xbox Achievements or Steam badges** — not a kindergarten reward chart. A 14-year-old should think the points system is cool. A parent should find it useful.

### Points Display

- Show point values in **Inter SemiBold**, not giant flashy numbers
- Use the gold accent sparingly for point-related UI
- Point totals use **JetBrains Mono** for the numeric value (gives a "score" feel)
- Subtle "+" animation when points are earned, not fireworks

### Badges

- Hexagonal frame (existing system)
- Muted glow using the badge's accent color at low opacity
- Hidden badges show as a subtle "?" hexagon — mysterious, not childish
- Earned badge animation: scale up from 0 with a satisfying spring ease, brief glow pulse

### Leaderboard

- Clean table/list layout
- Rank number in monospace
- Current user's row subtly highlighted
- No crown emojis, no podium illustrations — just clean typography and the user's avatar + color

### Rewards Store

- Card grid layout
- Each reward is a clean card with icon, title, point cost
- "Purchase" button uses the gold accent
- Confirmation uses a modal, not instant purchase (deliberate feel)

---

## Tone of Voice

### Writing Principles

1. **Clear over clever.** A busy parent scanning at a red light should instantly understand.
2. **Warm but not cute.** "Your tasks for today" not "Let's crush those tasks! 💪"
3. **Confident, not salesy.** "Encrypted at rest" not "Fort Knox-level security!!!"
4. **Inclusive language.** "Family member" not "mom" or "kid." "Parent" when role-specific.
5. **No jargon to end users.** "Your data is encrypted" not "AES-256 encryption at rest."

### UI Copy Examples

| Context | Do | Don't |
|---------|-------|---------|
| Empty task list | "No tasks yet. Add one to get started." | "Wow, nothing to do! 🎉" |
| Task completed | "Done. +5 points." | "Awesome job! You're a superstar! ⭐" |
| Badge earned | "New badge: Early Bird" | "🏆 CONGRATS!!! You earned a badge!!!" |
| Vault empty | "Your vault is empty. Start by adding important family information." | "Nothing here yet! Let's fill it up! 📦" |
| Error state | "Something went wrong. Try again." | "Oops! That didn't work 😅" |
| Onboarding | "Welcome to Kinhold." | "Welcome to the family! 🎊🎉👨‍👩‍👧‍👦" |

### Brand Voice in Different Contexts

| Context | Tone | Example |
|---------|------|---------|
| **Marketing / Landing page** | Confident, aspirational | "Your family's data, in your hands." |
| **Onboarding** | Helpful, calm | "Let's set up your family. It takes about 2 minutes." |
| **In-app UI** | Minimal, functional | "3 tasks due today" |
| **Error / empty states** | Honest, supportive | "No events this week. Enjoy the quiet." |
| **Achievement / celebration** | Understated satisfaction | "Badge unlocked: Consistent" |
| **Security / vault** | Direct, trustworthy | "This data is encrypted and only visible to you." |

---

## Dark Mode

### Philosophy

Dark mode isn't an afterthought — it's the **primary mode for many users** (especially teenagers). It should look arguably better than light mode.

### Rules

1. **Never use pure black (#000000).** Use rich near-black (#121214) for depth.
2. **Never use pure white (#FFFFFF) for text.** Use warm off-white (#F0EDE9).
3. **Maintain the same visual hierarchy.** Elevated surfaces are lighter, not darker.
4. **The gold accent stays the same** across both modes — it's the brand constant.
5. **Test every component in dark mode first.** If it looks good in dark, it'll look good in light.
6. **Images and illustrations** should work in both modes. Avoid hard white backgrounds in assets.

### Elevation in Dark Mode

| Level | Color | Usage |
|-------|-------|-------|
| **Base** | `#121214` | Page background |
| **Level 1** | `#1C1C20` | Cards, primary surfaces |
| **Level 2** | `#252528` | Modals, dropdowns, popovers |
| **Level 3** | `#2E2E32` | Tooltips, nested cards |

---

## Accessibility

### Requirements (Non-Negotiable)

1. **WCAG 2.1 AA minimum** for all text contrast ratios (4.5:1 body text, 3:1 large text)
2. **All interactive elements** keyboard-navigable with visible focus indicators
3. **Semantic HTML** — use proper heading hierarchy, button vs link, form labels
4. **ARIA labels** on all icon-only buttons and interactive elements
5. **Screen reader testing** on major flows (onboarding, tasks, vault)
6. **`prefers-reduced-motion`** respected — all animations must degrade gracefully
7. **`prefers-color-scheme`** detected for initial theme selection
8. **Touch targets:** minimum 44x44px on mobile
9. **Never convey information through color alone** — always pair with text, icon, or pattern

### Focus Indicators

- Use a 2px gold accent ring (`ring-2 ring-[#C4975A]`) with 2px offset
- Visible in both light and dark modes
- Never remove focus outlines — restyle them to match the brand

---

## Future: Theme System

The architecture should support user-selectable themes in the future. Plan for:

1. **System themes:** Light and Dark (ship with these)
2. **Accent color selection:** Per-user accent color (ship with this — family member colors)
3. **Mood presets (future):** Pre-built color schemes like:
   - *Default* — the warm premium look described in this guide
   - *Focus* — higher contrast, fewer colors, minimal distractions
   - *Cozy* — warmer tones, softer contrasts, more rounded
   - *Playful* — brighter accents, more animation, fun for younger kids
4. **Implementation:** CSS custom properties (variables) for all color tokens. Theme switching changes the variable values, not the component classes.

---

## File Naming & Assets

| Asset | Format | Location |
|-------|--------|----------|
| Wordmark (light bg) | SVG + PNG | `/public/brand/kinhold-wordmark-dark.svg` |
| Wordmark (dark bg) | SVG + PNG | `/public/brand/kinhold-wordmark-light.svg` |
| Logo mark | SVG + PNG | `/public/brand/kinhold-mark.svg` |
| Favicon | ICO + SVG | `/public/favicon.ico`, `/public/favicon.svg` |
| App icon | PNG (512x512) | `/public/brand/kinhold-icon-512.png` |
| Social card | PNG (1200x630) | `/public/brand/kinhold-og.png` |

---

## Quick Reference: Design Checklist

Before shipping any component or page, verify:

- [ ] Works in both light and dark mode
- [ ] Uses only colors from this guide (no one-off hex values)
- [ ] Typography follows the type scale (no arbitrary sizes)
- [ ] Minimum 44px touch targets on mobile
- [ ] Keyboard navigable with visible focus indicators
- [ ] No information conveyed by color alone
- [ ] Animations respect `prefers-reduced-motion`
- [ ] Card padding is 16px mobile, 24px desktop
- [ ] Generous spacing — when in doubt, add more
- [ ] Feels premium — would your wife approve?
