# Kinhold — Coding Conventions & Standards

> Follow these conventions in every session. Update when the team agrees on changes.

## General Principles

- Readability over cleverness
- Consistency over personal preference
- Mobile-first, always
- If in doubt, follow Laravel/Vue community conventions

## PHP / Laravel

### Naming
- **Models:** Singular PascalCase (`Task`, `VaultEntry`, `CalendarConnection`)
- **Controllers:** PascalCase with `Controller` suffix (`TaskController`)
- **Form Requests:** Verb + Model + `Request` (`StoreTaskRequest`, `UpdateVaultEntryRequest`)
- **API Resources:** Model + `Resource` (`TaskResource`, `VaultEntryResource`)
- **Services:** Model/Feature + `Service` (`GoogleCalendarService`, `ChatbotService`)
- **Policies:** Model + `Policy` (`TaskPolicy`, `VaultEntryPolicy`)
- **Enums:** PascalCase, backed string enums (`FamilyRole`, `TaskPriority`)
- **Migrations:** Laravel default: `create_tasks_table`, `add_column_to_table`
- **Database columns:** snake_case (`created_by`, `assigned_to`, `encrypted_data`)
- **Routes:** kebab-case for URL segments (`/task-lists`, `/vault/entries`)

### Patterns
- **Controllers** should be thin — delegate business logic to Services
- **Form Requests** handle all validation — never validate in controllers
- **API Resources** handle all response formatting — never return raw models from controllers
- **Policies** handle all authorization — use `$this->authorize()` in controllers
- **Scopes** on models for common query filters (`Task::incomplete()`, `Task::overdue()`)
- **Enums** for any fixed set of values — never bare strings

### API Conventions
- All routes under `/api/v1/` prefix
- RESTful resource routes: `GET /tasks`, `POST /tasks`, `GET /tasks/{id}`, `PUT /tasks/{id}`, `DELETE /tasks/{id}`
- Non-CRUD actions as verbs: `PATCH /tasks/{id}/toggle`, `POST /calendar/sync`
- Always return JSON with consistent structure:
  ```json
  { "data": { ... } }           // Single resource
  { "data": [ ... ] }           // Collection
  { "message": "..." }          // Action confirmation
  { "errors": { "field": [...] } }  // Validation errors (422)
  ```
- Use HTTP status codes correctly: 200 (ok), 201 (created), 204 (deleted), 401 (unauthenticated), 403 (forbidden), 404 (not found), 422 (validation), 500 (server error)

### Type Safety
- PHP 8.2+ type hints on all method parameters and return types
- PHPDoc blocks for complex types (arrays, collections)
- Backed enums instead of string constants
- `declare(strict_types=1)` at top of every PHP file (when not auto-included)

## Vue 3 / JavaScript

### Naming
- **Components:** PascalCase files and tags (`BaseCard.vue`, `<BaseCard />`)
- **Views (pages):** PascalCase with `View` suffix (`DashboardView.vue`, `LoginView.vue`)
- **Stores:** camelCase files, `use` + Name + `Store` (`useAuthStore`, `useTasksStore`)
- **Composables:** camelCase files, `use` + Name (`useNotification`, `useFamilyColors`)
- **Services:** camelCase files (`api.js`)
- **Props:** camelCase in JS, kebab-case in templates (`modelValue` → `<Input :model-value="x" />`)
- **Events:** camelCase with `emit` (`emit('update:modelValue')`)
- **CSS classes:** Tailwind utilities only — no custom class names unless absolutely necessary

### Patterns
- Always use `<script setup>` syntax — no Options API
- Pinia stores for all shared state — no prop drilling beyond 2 levels
- All API calls go through `services/api.js` — never use `fetch()` or raw `axios` in components
- Composables for reusable logic (notifications, colors, date formatting)
- `v-model` support on custom form components
- Proper loading, error, and empty states on every view
- Skeleton loaders preferred over spinners for initial page loads

### Component Structure (within `<script setup>`)
```javascript
// 1. Imports
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'

// 2. Props and emits
const props = defineProps({ ... })
const emit = defineEmits([ ... ])

// 3. Store references
const authStore = useAuthStore()

// 4. Reactive state
const loading = ref(false)
const items = ref([])

// 5. Computed properties
const filteredItems = computed(() => ...)

// 6. Methods
async function fetchData() { ... }

// 7. Lifecycle hooks
onMounted(() => fetchData())
```

### Responsive Design Rules
- Design for 375px width first (iPhone SE)
- Use Tailwind breakpoints: `sm:` (640px), `md:` (768px), `lg:` (1024px), `xl:` (1280px)
- Bottom navigation visible only below `md:` breakpoint
- Sidebar visible only at `md:` and above
- Cards stack vertically on mobile, grid on desktop
- Minimum touch target: 44px × 44px
- Safe area padding on bottom for notched phones (`pb-safe`)

### Accessibility
- Every interactive element needs focus styles
- Images need alt text
- Form inputs need associated labels
- Color is never the only indicator (always pair with icon or text)
- Minimum contrast ratio: 4.5:1 for text

## Tailwind CSS

### Color Palette (defined in tailwind.config.js)
- `primary` — Warm blue (main actions, active states)
- `secondary` — Warm amber/orange (accents, highlights)
- `family-1` through `family-6` — Distinct colors for family member coding
- Backgrounds: `gray-50` (light bg), `white` (cards), `gray-900` (future dark mode)

### Spacing
- Card padding: `p-4` (mobile), `p-6` (desktop)
- Card gap: `gap-4` (mobile), `gap-6` (desktop)
- Section spacing: `space-y-6`
- Page padding: `px-4` (mobile), `px-6` (desktop)

### Common Patterns
```html
<!-- Card -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">

<!-- Primary button -->
<button class="bg-primary-600 text-white rounded-lg px-4 py-2.5 font-medium hover:bg-primary-700 transition-colors">

<!-- Bottom nav safe area -->
<nav class="fixed bottom-0 inset-x-0 bg-white border-t pb-safe">
```

## Git Conventions

### Branch Naming
- `main` — Production-ready code
- `develop` — Integration branch
- `feature/short-description` — New features
- `fix/short-description` — Bug fixes
- `chore/short-description` — Maintenance (deps, docs, config)

### Commit Messages
- Imperative mood: "Add task completion endpoint" not "Added..."
- Prefix with category: `feat:`, `fix:`, `docs:`, `chore:`, `refactor:`, `test:`
- Keep first line under 72 characters
- Reference issues when applicable: `feat: add task lists (#12)`

### Pull Request Template
- Summary (what and why)
- Test plan
- Screenshots for UI changes

## File Organization

When adding a new feature/module, create files in ALL of these locations:
1. `app/Models/NewModel.php`
2. `app/Http/Controllers/Api/V1/NewModelController.php`
3. `app/Http/Requests/NewModel/StoreRequest.php` + `UpdateRequest.php`
4. `app/Http/Resources/NewModelResource.php`
5. `app/Policies/NewModelPolicy.php`
6. `app/Services/NewModelService.php` (if business logic is complex)
7. `database/migrations/create_new_models_table.php`
8. `routes/api.php` — add routes
9. `resources/js/stores/newModel.js` — Pinia store
10. `resources/js/views/newModel/` — Vue view components
11. `resources/js/components/newModel/` — Vue reusable components
12. `resources/js/router/index.js` — add routes
13. `mcp-server/src/tools/newModel.ts` — MCP tools
14. Update `CLAUDE.md` module list
15. Update `docs/ROADMAP.md` feature status
