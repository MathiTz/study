# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Build and Development Commands

### Backend (PHP API)

```bash
# Start development
make up                   # Start PostgreSQL database
make serve                # Start PHP dev server on localhost:8000

# Database
make db-shell             # Open psql shell
make db-reset             # Reset database (drop and recreate)

# Testing
make test                 # Run all tests (auto-starts test DB)
make test-unit            # Run unit tests only (no DB)
make test-integration     # Run integration tests
composer test:coverage    # Generate HTML coverage report
```

### Frontend (Vue.js)

```bash
npm install              # Install dependencies
npm run dev              # Start dev server on localhost:5173
npm run build            # Build for production
```

## Architecture Overview

This is a time-tracking application (Toggl-style) with a pure PHP backend and Vue 3 frontend.

### Backend (`/api`)

**No framework** - custom MVC-like structure:

- `public/index.php` - Entry point with all route definitions
- `src/Core/` - Framework core (Router, Request, Response, Database, Session)
- `src/Controllers/` - HTTP handlers (Auth, User, Project, Timer, WorkEntry, Organization, Report)
- `src/Models/` - Data layer with static methods extending base `Model` class
- `src/Services/` - Business logic (AuthService, AuditService, ReportService, InviteService)
- `src/Middleware/` - AuthMiddleware, CorsMiddleware

**Routing pattern:**
```php
$router->group('/api', function ($router) {
    $router->group('/projects', function ($router) {
        $router->get('/', [ProjectController::class, 'index']);
    })->middleware('auth');
});
```

**Database:** PostgreSQL via PDO singleton (`Database::getInstance()`). Migrations in `/migrations/`.

### Frontend (`/web`)

**Vue 3 Composition API** with Pinia state management:

- `stores/` - Pinia stores: `auth.js`, `timer.js`, `projects.js`, `theme.js`
- `composables/useApi.js` - Fetch wrapper with `get()`, `post()`, `put()`, `del()` methods
- `views/` - Page components with lazy loading
- `components/` - Reusable UI components organized by domain

**Routing:** Vue Router 4 with `meta: { auth: true }` for protected routes.

**Styling:** Tailwind CSS with custom component classes in `assets/styles/main.css` (btn-primary, input, card, etc.)

### Key Domain Concepts

- **WorkEntry** - A time-tracked work session with `started_at`, `ended_at`, `description`, `total_paused_seconds`
- **Timer** - Active timer linked to an ongoing WorkEntry (one per user, stored in `active_timers` table)
- **WorkBlock** - Sub-annotations within a WorkEntry for detailed time segmentation
- **Project** - Groups work entries; has members with roles (owner, manager, member)
- **Organization** - Groups projects and users with invite system

### Main Views and Routes

| Route | View | Description |
|-------|------|-------------|
| `/` | HomeView | Main workspace with TimerBar + multi-day calendar timeline |
| `/summary` | SummaryView | Weekly summary dashboard (formerly Dashboard) |
| `/projects` | ProjectsView | List and manage projects |
| `/entries` | WorkEntriesView | List all work entries with filters |
| `/entries/:id` | WorkEntryDetailView | View/edit single work entry details |

### Home Page (Toggl-style UX)

The home page (`/`) is the main workspace featuring:

1. **TimerBar** - Horizontal bar to start/stop timer with project selector and title input
2. **DayCalendar** - Multi-day timeline view with:
   - Column layout: each day is a column, hours are rows (0-24)
   - Filter options: Today, 3 Days, Week
   - Real-time growing blocks for active entries
   - Click on empty space to create manual entry
   - Click on entry to edit (modal with tabs: Edit Current / Switch Task)
   - Red time line across all columns showing current time
   - Past entries shown in gray, active entries with pulse animation
   - Cross-day entries displayed across multiple columns

### API Communication

Frontend proxies `/api` requests to `localhost:8000` via Vite config. All responses are JSON with structure:
```json
{ "data": ..., "message": "..." }
```

Errors return:
```json
{ "error": "...", "errors": { "field": ["messages"] } }
```

### Key API Endpoints

**Timer:**
- `POST /api/timer/start` - Start timer (project_id, title required)
- `POST /api/timer/stop` - Stop current timer
- `POST /api/timer/pause` / `POST /api/timer/resume` - Pause/resume
- `POST /api/timer/transition` - Atomic stop current + start new timer
- `GET /api/timer/current` - Get active timer with elapsed time

**Work Entries:**
- `GET /api/work-entries` - List entries (supports `start_date`, `end_date`, `project_id` filters)
- `POST /api/work-entries` - Create entry (if `ended_at` is null, creates active timer)
- `PUT /api/work-entries/:id` - Update entry (supports `project_id`, `title`, `description`, `started_at`, `ended_at`)
- `DELETE /api/work-entries/:id` - Delete entry

**WorkEntry fields:**
- `project_id` (uuid, required)
- `title` (string, required)
- `description` (string, optional)
- `started_at` (datetime, required)
- `ended_at` (datetime, optional - null means active/running)
