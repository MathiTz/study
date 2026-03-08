# TrackNote

A time tracking application with notes and reporting capabilities.

## Features

- Timer with start/pause/resume/stop functionality
- Manual time entry
- Work blocks with notes (markdown support planned)
- Projects and Organizations
- Role-based access control (admin, manager, member)
- Reports with daily/weekly/monthly/yearly breakdowns
- CSV export
- Audit logging for all actions
- Dark/Light theme support

## Tech Stack

- **Backend**: PHP 8.2+ (pure, no framework)
- **Frontend**: Vue.js 3 with Composition API
- **Database**: PostgreSQL 15+
- **Styling**: Tailwind CSS
- **State Management**: Pinia

## Project Structure

```
track-note/
├── api/                    # Backend PHP
│   ├── public/             # Entry point
│   ├── src/
│   │   ├── Controllers/    # HTTP Controllers
│   │   ├── Models/         # Data Models
│   │   ├── Services/       # Business Logic
│   │   ├── Middleware/     # HTTP Middleware
│   │   ├── Core/           # Framework Core
│   │   └── Helpers/        # Utility Functions
│   ├── config/             # Configuration files
│   ├── migrations/         # Database migrations
│   └── tests/              # PHPUnit tests
│
└── web/                    # Frontend Vue.js
    ├── src/
    │   ├── components/     # Vue components
    │   ├── views/          # Page components
    │   ├── composables/    # Composition API utilities
    │   ├── stores/         # Pinia stores
    │   ├── router/         # Vue Router config
    │   └── assets/         # CSS and static files
    └── index.html
```

## Getting Started

### Prerequisites

- PHP 8.2+
- Docker & Docker Compose
- Node.js 18+
- Composer

### Backend Setup

1. Navigate to the API directory:
   ```bash
   cd api
   ```

2. Install dependencies and setup environment:
   ```bash
   make install
   ```

3. Start the database (PostgreSQL via Docker):
   ```bash
   make up
   ```
   This will start PostgreSQL and automatically run the migrations.

4. Start the PHP development server:
   ```bash
   make serve
   ```

#### Available Make Commands

```bash
make help       # Show all available commands
make up         # Start database containers
make down       # Stop database containers
make restart    # Restart database containers
make logs       # Show database logs
make db-shell   # Open psql shell
make db-reset   # Reset database (drop and recreate)
make test       # Run tests
make serve      # Start PHP development server
make install    # Install dependencies and setup environment
```

#### Manual Setup (without Make)

If you prefer not to use Make:

```bash
# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Start database
docker compose up -d

# Start PHP server
php -S localhost:8000 -t public
```

### Frontend Setup

1. Navigate to the web directory:
   ```bash
   cd web
   ```

2. Install dependencies:
   ```bash
   npm install
   ```

3. Start the development server:
   ```bash
   npm run dev
   ```

4. Open http://localhost:5173 in your browser

### Running Tests

```bash
cd api
composer test
```

## API Endpoints

### Authentication
- `POST /api/auth/register` - Create account
- `POST /api/auth/login` - Login
- `POST /api/auth/logout` - Logout
- `GET /api/auth/me` - Get current user

### Projects
- `GET /api/projects` - List projects
- `POST /api/projects` - Create project
- `GET /api/projects/:id` - Get project
- `PUT /api/projects/:id` - Update project
- `DELETE /api/projects/:id` - Archive project

### Timer
- `GET /api/timer/current` - Get active timer
- `POST /api/timer/start` - Start timer
- `POST /api/timer/pause` - Pause timer
- `POST /api/timer/resume` - Resume timer
- `POST /api/timer/stop` - Stop timer

### Work Entries
- `GET /api/work-entries` - List entries
- `POST /api/work-entries` - Create manual entry
- `GET /api/work-entries/:id` - Get entry
- `PUT /api/work-entries/:id` - Update entry
- `DELETE /api/work-entries/:id` - Delete entry

### Reports
- `GET /api/reports/summary` - Get time summary
- `GET /api/reports/by-project` - Time by project
- `GET /api/reports/by-user` - Time by user (admin)
- `GET /api/reports/export` - Export CSV

## Environment Variables

### Backend (.env)
```
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_HOST=localhost
DB_PORT=5432
DB_DATABASE=tracknote
DB_USERNAME=postgres
DB_PASSWORD=postgres

CORS_ORIGINS=http://localhost:5173
```

## Docker Services

The `docker-compose.yml` in the `api/` directory provides:

| Service   | Port | Description                    |
|-----------|------|--------------------------------|
| db        | 5432 | PostgreSQL (development)       |
| db_test   | 5433 | PostgreSQL (testing)           |

Both databases are automatically initialized with the schema from `migrations/`.

## License

Proprietary
