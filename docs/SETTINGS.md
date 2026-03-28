# Settings Architecture

> Reference for how settings are stored, displayed, and extended in Kinhold.

## Overview

The Settings page (`resources/js/views/settings/SettingsView.vue`) is organized into collapsible sections for parents and a flat card layout for children. Parents see 7 sections; children see only their profile, appearance, and notifications.

### Components

| Component | Path | Purpose |
|-----------|------|---------|
| `SettingsSection` | `resources/js/components/settings/SettingsSection.vue` | Collapsible card wrapper with title, description, icon, chevron |
| `ToggleSwitch` | `resources/js/components/common/ToggleSwitch.vue` | Unified toggle switch with label/description row and v-model |

## Section Layout (Parent View)

| # | ID | Title | What's Inside |
|---|----|-------|---------------|
| 1 | `family` | Family | Family name, invite code, members list, setup wizard |
| 2 | `tasks-points` | Tasks & Points | Tasks/points access, default points, task assignment, leaderboard period, kudos cost |
| 3 | `ai-integrations` | AI & Integrations | AI provider/key, MCP token, Google Calendar, ICS subscriptions |
| 4 | `feature-access` | Feature Access | Module access for Calendar, Vault, Chat, Badges |
| 5 | `appearance` | Appearance | Dark mode toggle, color theme picker |
| 6 | `notifications` | Notifications | Email notification toggles |
| 7 | `profiles-avatars` | Profiles & Avatars | Children avatar permission toggle |

Sections can be deep-linked via URL hash (e.g., `/settings#ai-integrations`).

## Settings Storage Map

| Setting | Storage | API Endpoint | Save Behavior | Scope |
|---------|---------|-------------|---------------|-------|
| Family name | `families.name` | `PUT /family` (via authStore) | Form submit | Family |
| Invite code | `families.invite_code` | `GET /family/invite-code` | Read-only | Family |
| Module access | `families.settings.module_access` (JSON) | `PUT /settings` | Batch save | Family |
| Leaderboard period | `families.settings.leaderboard_period` | `PUT /settings` | Batch save | Family |
| Kudos cost | `families.settings.kudos_cost_enabled` | `PUT /settings` | Batch save | Family |
| Default points (low/med/high) | `families.settings.default_points_*` | `PUT /settings` | Batch save | Family |
| Task assignment | `families.settings.task_assignment` (JSON) | `PUT /settings` | Batch save | Family |
| AI provider/key/model | `families.settings.ai_*` (key encrypted) | `PUT /settings` | Batch save | Family |
| Children can change avatar | `families.settings.children_can_change_avatar` | `PUT /settings` | Immediate | Family |
| Dark mode | `localStorage` via `useDarkMode` | None | Client-side | Per-browser |
| Color theme | `localStorage` via `useTheme` | None | Client-side | Per-browser |
| Email preferences | `users.email_preferences` (JSON) | `PUT /settings/email-preferences` | Batch save | Per-user |
| MCP token | `personal_access_tokens` table | `POST/DELETE /mcp/token` | Immediate | Per-user |
| Calendar connections | `calendar_connections` table | Via calendarStore | Immediate | Per-user |

### Backend Details

- **Database:** The `families` table has a `settings` JSON column (`$casts = ['settings' => 'array']` on the Family model).
- **Controller:** `app/Http/Controllers/Api/V1/SettingsController.php` handles `GET /settings` and `PUT /settings`.
- **Encryption:** The `ai_api_key` is encrypted at rest using Laravel's `encrypt()`/`decrypt()`.

## Adding a New Setting

1. **Backend:** Add the key to `SettingsController`'s validation and read/write logic
2. **Frontend state:** Add a `ref()` or `reactive()` in SettingsView's script section
3. **Frontend UI:** Add the control to the appropriate section in the template
4. **Initialize:** Load the value in `onMounted` from `family.value.settings`
5. **Save:** Either add to an existing batch save function or create a new one
6. **Document:** Add a row to the storage map table above

## ToggleSwitch API

```vue
<!-- With label (renders full row) -->
<ToggleSwitch
  v-model="myValue"
  label="Enable feature"
  description="Optional help text"
/>

<!-- Standalone (just the switch) -->
<ToggleSwitch v-model="myValue" />

<!-- With custom thumb content (e.g., dark mode icons) -->
<ToggleSwitch v-model="isDark">
  <template #thumb>
    <MoonIcon v-if="isDark" class="w-3.5 h-3.5 text-wisteria-500" />
    <SunIcon v-else class="w-3.5 h-3.5 text-sand-500" />
  </template>
</ToggleSwitch>
```

**Props:** `modelValue` (Boolean, required), `label` (String), `description` (String), `disabled` (Boolean), `size` ('sm' | 'md')

## SettingsSection API

```vue
<SettingsSection
  id="my-section"
  title="Section Title"
  description="Optional subtitle"
  :icon="SomeHeroIcon"
  badge="Parent"
  :model-value="expandedSections.has('my-section')"
  @update:model-value="val => toggleSection('my-section', val)"
>
  <!-- Section content here -->
</SettingsSection>
```

**Props:** `id` (String, required), `title` (String, required), `description` (String), `icon` (Component), `badge` (String), `defaultOpen` (Boolean), `modelValue` (Boolean)

**Slots:** `default` (body content), `header-actions` (right side of header, before chevron)
