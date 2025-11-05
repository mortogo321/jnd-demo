# URL Shortener Application - DEMO

A modern, high-performance URL shortener application built with **Laravel 12** (API backend) and **Vue 3** with **TypeScript** (frontend), designed to handle high traffic with Redis caching and optimized database queries.

## ✨ Features

- 🔗 **URL Shortening**: Convert long URLs into short, shareable links
- 📊 **Click Analytics**: Track clicks with IP, user agent, and referer information
- 🔐 **User Authentication**: Secure registration and login with Laravel Sanctum
- 👑 **Admin Dashboard**: Manage all URLs across the platform
- 📱 **Responsive Design**: Works on all devices
- ⚡ **Redis Caching**: High-performance URL lookups
- 🐳 **Fully Dockerized**: Multi-environment support (dev/test/prod)
- 📚 **Auto-Generated API Docs**: Swagger/OpenAPI documentation
- 🎨 **Modern UI**: Tailwind CSS v4 with shadcn-vue components, Radix Vue primitives, and Lucide icons
- ✅ **Type-Safe**: Full TypeScript implementation
- 🧪 **Tested**: Pest for backend testing

## 🛠 Tech Stack

### Backend

- Laravel 12 | Laravel Sanctum | MySQL | Redis | Laravel Pint | L5-Swagger | Pest

### Frontend

- Vue 3 | TypeScript | Vite | Vue Router | Pinia | Axios | Tailwind CSS v4 | shadcn-vue | Radix Vue | Lucide Icons | Prettier | ESLint

### DevOps

- Docker | Docker Compose | Nginx | pnpm (monorepo)

## 📁 Project Structure

```
jnd-demo/
├── server/              # Laravel Backend (API only)
│   ├── app/
│   │   ├── Http/Controllers/Api/  # API Controllers
│   │   ├── Models/                # Eloquent models
│   │   └── Services/              # Business logic
│   ├── database/migrations/       # Database migrations
│   ├── routes/api.php             # API routes
│   ├── Dockerfile                 # Multi-stage Docker
│   ├── .env.development           # Dev environment
│   ├── .env.testing               # Test environment
│   ├── .env.production            # Prod environment (gitignored)
│   └── pint.json                  # Code formatting
│
├── web/                 # Vue Frontend
│   ├── src/
│   │   ├── api/         # API client
│   │   ├── components/  # Vue components
│   │   ├── stores/      # Pinia stores
│   │   ├── types/       # TypeScript types
│   │   └── views/       # Page components
│   ├── Dockerfile       # Multi-stage Docker
│   ├── .env.development           # Dev environment
│   ├── .env.testing               # Test environment
│   ├── .env.production            # Prod environment (gitignored)
│   └── tailwind.config.js
│
├── docker/              # Docker & DevOps
│   ├── compose.development.yml  # Development
│   ├── compose.testing.yml      # Testing
│   ├── compose.production.yml   # Production
│   └── nginx/           # Nginx configs
│
├── package.json         # Root workspace
└── pnpm-workspace.yaml
```

## 🚀 Quick Start

### Prerequisites

- Docker & Docker Compose
- pnpm (v8+)

### Development Setup

1. **Clone the repository**

   ```bash
   git clone https://github.com/mortogo321/jnd-demo.git
   cd jnd-demo
   ```

2. **Environment files are pre-configured**

   - Each project has its own `.env` files:
     - `server/.env.development` and `web/.env.development` (committed, safe defaults)
     - `server/.env.testing` and `web/.env.testing` (committed, safe defaults)
     - `server/.env.production` and `web/.env.production` (gitignored, copy from `.example`)

3. **Start with Docker**

   ```bash
   cd docker
   docker compose -f compose.development.yml up -d
   ```

   The application will automatically:

   - Generate `APP_KEY` if not set
   - Run database migrations (upsert)
   - Seed the database (dev/test environments only)
   - Create SQLite database file if needed

4. **Access the application**

   - Frontend: http://localhost:8000 (served through Nginx)
   - API: http://localhost:8000/api
   - API Docs: http://localhost:8000/api/documentation

   **Default Credentials (Development):**

   - Admin: `admin@example.com` / `password`
   - User: `user@example.com` / `password`

### Stopping Services

```bash
cd docker
docker compose -f compose.development.yml down
```

## 📚 API Endpoints

### Authentication

```
POST   /api/register      Register new user
POST   /api/login         Login
POST   /api/logout        Logout (auth required)
GET    /api/user          Get current user
```

### URL Management

```
GET    /api/urls          List user's URLs
POST   /api/urls          Create shortened URL
GET    /api/urls/{id}     Get URL with analytics
DELETE /api/urls/{id}     Delete URL
```

### Admin (admin only)

```
GET    /api/admin/urls         List all URLs
DELETE /api/admin/urls/{id}    Delete any URL
```

### Public

```
GET    /{code}            Redirect to original URL
```

## 💻 Development

### Code Formatting

```bash
# Backend (Laravel Pint)
cd server && vendor/bin/pint

# Frontend (Prettier)
cd web && pnpm format && pnpm lint
```

### Testing

```bash
# Backend
cd server && php artisan test

# Frontend
cd web && pnpm test
```

## 🏃 Running Environments

Environment files are stored in each project directory (`server/.env.*` and `web/.env.*`). Docker Compose builds use these files via the `ENV_FILE` build argument.

### Development Environment

Uses SQLite for zero-config database setup. Redis with password protection. **Migrations and seeders run automatically on container start.**

```bash
cd docker
docker compose -f compose.development.yml up -d
```

**Services:**

- Application: http://localhost:8000 (Nginx serves both frontend & API)
- Vite Dev Server: http://localhost:5173 (hot-reload for development)
- Laravel API: http://localhost:8000/api
- Redis: localhost:6379 (password: `dev_redis_password`)

**Default Users:**

- Admin: `admin@example.com` / `password` (has access to Admin Panel)
- User: `user@example.com` / `password` (regular user access)

**Note:** The entrypoint script automatically runs migrations and seeders on startup, so no manual database initialization is needed.

### Testing Environment

Uses MySQL for realistic testing scenarios. All services use testing configuration. **Migrations and seeders run automatically on container start.**

```bash
cd docker
docker compose -f compose.testing.yml up -d

# Run tests
docker compose exec server php artisan test
```

**Services:**

- Vue Frontend: http://localhost:8080
- Laravel API: http://localhost:8001
- MySQL: localhost:3307 (password: `test_password`)
- Redis: localhost:6380 (password: `test_redis_password`)

**Note:** The entrypoint script automatically waits for MySQL, generates APP_KEY, runs migrations, and seeds the database on startup.

### Production Environment

Full production stack with MySQL and Redis. **Important**: Update passwords in `server/.env.production` and `web/.env.production` before deploying! **Migrations run automatically on container start** (no seeders in production).

```bash
# 1. Copy and edit production environment files
cp server/.env.production.example server/.env.production
cp web/.env.production.example web/.env.production
nano server/.env.production  # Update passwords and domains
nano web/.env.production      # Update API URLs

# 2. Deploy
cd docker
docker compose -f compose.production.yml up -d --build
```

**Note:** The entrypoint script automatically:

- Waits for MySQL connection
- Generates APP_KEY if not set
- Runs database migrations
- Caches config, routes, and views for optimal performance

## 🐳 Docker Environment Variables

Each environment has its own `.env` files in `server/` and `web/` directories:

**Server Environment Variables** (`server/.env.*`):

- `APP_ENV`, `APP_DEBUG`, `APP_KEY`, `APP_URL`
- `DB_CONNECTION`, `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- `MYSQL_ROOT_PASSWORD`, `MYSQL_DATABASE`, `MYSQL_USER`, `MYSQL_PASSWORD`
- `REDIS_HOST`, `REDIS_PORT`, `REDIS_PASSWORD`
- `SANCTUM_STATEFUL_DOMAINS`, `FRONTEND_URL`

**Web Environment Variables** (`web/.env.*`):

- `VITE_API_BASE_URL` - Backend API URL
- `VITE_APP_URL` - Frontend application URL

## 🔧 Docker Commands

```bash
# View logs
docker compose -f compose.development.yml logs -f

# Execute commands (no container names, use service names)
docker compose exec server php artisan migrate
docker compose exec server vendor/bin/pint

# Access database/redis
docker compose exec mysql mysql -u urlshortener -p
docker compose exec redis redis-cli -a dev_redis_password

# Rebuild
docker compose -f compose.development.yml build --no-cache

# Clean up
docker compose -f compose.development.yml down -v
```

## ⚡ Performance Optimizations

### Backend

- ✅ Redis caching for URL lookups (24h TTL)
- ✅ Database indexing on short_code
- ✅ Eager loading to prevent N+1 queries
- ✅ API resource classes for efficient serialization

### Frontend

- ✅ Code splitting with vendor chunks
- ✅ Lazy loading for routes
- ✅ Asset optimization
- ✅ CDN ready

## 📝 Environment Variables

### Backend (.env)

```env
APP_ENV=local
DB_CONNECTION=sqlite
REDIS_HOST=redis
SHORT_URL_LENGTH=6
RATE_LIMIT_PER_MINUTE=60
```

### Frontend (.env)

```env
VITE_API_BASE_URL=http://localhost:8000
VITE_APP_URL=http://localhost:8000
```

**Note:** `VITE_APP_URL` should point to port 8000 (where Nginx serves the app) for correct short URL generation.

## 📄 License

MIT License

---

### Key Data Flows

#### URL Shortening

```
User → Vue → API (/api/urls POST)
         → Sanctum Auth → Validation
         → Generate short_code → Save MySQL
         → Cache Redis (24h) → Return URL
```

#### URL Redirect (High Performance)

```
User clicks → API (/{code})
          → Redis cache check ⚡ (<10ms)
              ├─ HIT → Redirect
              └─ MISS → MySQL → Cache → Redirect
          → Log click (async)
```

### Database Schema

**users**: id, name, email, password, is_admin, timestamps
**shortened_urls**: id, user_id (FK), original_url, short_code (unique, indexed), clicks, timestamps
**url_clicks**: id, shortened_url_id (FK), ip_address, user_agent, referer, created_at

### Performance

| Operation           | Response Time | Throughput    |
| ------------------- | ------------- | ------------- |
| Redirect (cached)   | <10ms         | >10,000 req/s |
| Redirect (uncached) | <50ms         | >1,000 req/s  |
| Create URL          | <100ms        | >500 req/s    |

### Security

✅ Sanctum auth • CSRF protection • SQL injection prevention • XSS protection • Rate limiting • Bcrypt passwords • HTTPS/SSL
