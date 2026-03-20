# Family Member Management & Child Accounts Plan

## Summary
Replace the broken invite-email flow with a proper **user management system**. Three features:

1. **Direct member management** — Parents add/edit/remove family members directly
2. **Invite code display** — Show the family code prominently so parents can share it verbally
3. **Managed child accounts** — For kids without email, parent creates their account and can "switch" into it. Switching back requires parent password.

---

## Feature 1: Family Member Management (Backend)

### Migration: `add_managed_account_fields_to_users_table`
- Make `email` nullable (for managed child accounts)
- Make `password` nullable (managed accounts don't have passwords)
- Add `is_managed` boolean default false (parent-managed account flag)
- Add `managed_by` UUID nullable FK to users (which parent created this account)
- Drop the unique index on email, replace with unique index that allows nulls

### API: `POST /api/v1/family/members` — Add family member (parent only)
- Request fields: `name` (required), `email` (optional), `password` (optional), `role` (parent/child), `date_of_birth` (optional)
- If no email provided: creates a managed account (`is_managed = true`, `managed_by = auth user`)
- If email provided but no password: generates a random password (they can reset later)
- Validation: if email provided, must be unique

### API: `PUT /api/v1/family/members/{user}` — Update member (parent only)
- Can update: name, email, role, date_of_birth
- Cannot demote yourself from parent

### API: `DELETE /api/v1/family/members/{user}` — Remove member (already exists, verify it works)

### FamilyPolicy updates
- Add `addMember`, `updateMember`, `removeMember` methods (all parent-only)

---

## Feature 2: Invite Code Display (Frontend)

### In SettingsView.vue — Family Members section:
- Show invite code in a prominent card with copy-to-clipboard button
- Text: "Share this code with family members so they can join during registration"
- Only visible to parents

---

## Feature 3: Profile Switching (for managed child accounts)

### API: `POST /api/v1/auth/switch-profile` — Switch to managed child
- Request: `user_id` (the child to switch to)
- Validates: child must be `is_managed = true` and in same family
- Validates: current user must be parent
- Creates a new Sanctum token for the child account
- Stores the parent's user ID in the token metadata (so we know who switched)
- Returns new token + child's user data

### API: `POST /api/v1/auth/switch-back` — Switch back to parent
- Request: `password` (parent's password required)
- Validates: current session was created via switch (has parent reference)
- Validates: password matches the parent's password
- Revokes child token, creates new parent token
- Returns parent token + parent user data

### Frontend: Auth store additions
- `switchToProfile(userId)` action
- `switchBack(password)` action
- `switchedFrom` ref — tracks if we're in a switched session
- `isSwitchedSession` computed

### Frontend: UI indicators
- When in switched session, show a banner at top: "Viewing as [Child Name] — Switch Back"
- "Switch Back" opens a modal asking for parent password
- In Settings > Family Members, show "Switch to" button next to managed children

---

## File Changes Summary

| File | Change |
|------|--------|
| `database/migrations/new` | Add `is_managed`, `managed_by`, make email/password nullable |
| `app/Http/Controllers/Api/V1/FamilyController.php` | Add `addMember`, `updateMember`, `removeMember` methods |
| `app/Http/Controllers/Api/V1/AuthController.php` | Add `switchProfile`, `switchBack` methods |
| `app/Policies/FamilyPolicy.php` | Add new policy methods |
| `app/Models/User.php` | Add `managedBy`, `managedChildren` relationships, `isManaged()` |
| `routes/api.php` | Add new routes |
| `resources/js/stores/auth.js` | Add member management + profile switching actions |
| `resources/js/views/settings/SettingsView.vue` | Redesign Family Members section |
| `resources/js/components/common/SwitchBanner.vue` | New — switched session banner |
| `resources/js/App.vue` or layout | Include SwitchBanner |

---

## Implementation Order
1. Migration (make email/password nullable, add managed fields)
2. Backend: Family member CRUD endpoints
3. Backend: Profile switching endpoints
4. Frontend: Redesign Settings > Family Members with add/edit/invite code
5. Frontend: Profile switching UI (banner + switch back modal)
6. Test the flow end-to-end
