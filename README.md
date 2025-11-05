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
- 🎨 **Modern UI**: Tailwind CSS with shadcn-vue components
- ✅ **Type-Safe**: Full TypeScript implementation
- 🧪 **Tested**: Pest for backend testing

## 🛠 Tech Stack

### Backend

- Laravel 12 | Laravel Sanctum | MySQL | Redis | Laravel Pint | L5-Swagger | Pest

### Frontend

- Vue 3 | TypeScript | Vite | Vue Router | Pinia | Axios | Tailwind CSS | shadcn-vue | Prettier | ESLint

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
│   └── tailwind.config.js
│
├── docker/              # Docker & DevOps
│   ├── compose.development.yml  # Development
│   ├── compose.testing.yml      # Testing
│   ├── compose.production.yml   # Production
│   └── nginx/           # Nginx configs
│
├── .env.development     # Dev environment variables
├── .env.testing         # Test environment variables
└── .env.production.example  # Production env template
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
   git clone <repository-url>
   cd jnd-demo
   ```

2. **Environment files are pre-configured**
   - `.env.development` - Development environment (already configured)
   - `.env.testing` - Testing environment (already configured)
   - For local Laravel/Vue setup: Copy `server/.env.example` and `web/.env.example`

3. **Start with Docker**
   ```bash
   cd docker
   docker-compose --env-file ../.env.development -f compose.development.yml up -d
   ```

4. **Initialize database** (first time only)
   ```bash
   # Create SQLite database file
   docker exec -it urlshortener-server-dev touch database/database.sqlite

   # Run migrations and seeders
   docker exec -it urlshortener-server-dev php artisan migrate --seed
   docker exec -it urlshortener-server-dev php artisan key:generate
   ```

5. **Access the application**
   - Frontend: http://localhost:5173
   - API: http://localhost:8000
   - API Docs: http://localhost:8000/api/documentation

### Stopping Services

```bash
cd docker
docker-compose -f compose.development.yml down
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

All docker-compose files read from environment files in the project root.

### Development Environment

Uses SQLite for zero-config database setup.

```bash
cd docker
docker-compose --env-file ../.env.development -f compose.development.yml up -d
```

**Services:**
- Vue Frontend: http://localhost:5173
- Laravel API: http://localhost:8000
- Redis: localhost:6379

### Testing Environment

Uses MySQL for realistic testing scenarios.

```bash
cd docker
docker-compose --env-file ../.env.testing -f compose.testing.yml up -d

# Initialize database
docker exec -it urlshortener-server-test php artisan migrate --seed
docker exec -it urlshortener-server-test php artisan key:generate

# Run tests
docker exec -it urlshortener-server-test php artisan test
```

**Services:**
- Vue Frontend: http://localhost:8080
- Laravel API: http://localhost:8001
- MySQL: localhost:3307
- Redis: localhost:6380

### Production Environment

Full production stack with MySQL and Redis.

```bash
# 1. Create production environment file
cp .env.production.example .env.production

# 2. Edit with your secure values
nano .env.production

# 3. Deploy
cd docker
docker-compose --env-file ../.env.production -f compose.production.yml up -d --build

# 4. Initialize
docker exec -it urlshortener-server-prod php artisan migrate --force
```

## 🐳 Docker Environment Variables

Edit environment files (`.env.development`, `.env.testing`, `.env.production`) to customize:

**Application**: `APP_ENV`, `APP_DEBUG`, `APP_KEY`
**MySQL**: `MYSQL_ROOT_PASSWORD`, `MYSQL_DATABASE`, `MYSQL_USER`, `MYSQL_PASSWORD`, `MYSQL_PORT`
**Redis**: `REDIS_HOST`, `REDIS_PORT`, `REDIS_PASSWORD`, `REDIS_PORT_EXPOSED`
**Ports**: `NGINX_PORT`, `NGINX_HTTPS_PORT`, `WEB_PORT`
**URLs**: `API_URL`, `FRONTEND_URL`, `VITE_API_BASE_URL`, `VITE_APP_URL`

## 🔧 Docker Commands

```bash
# View logs
docker-compose -f compose.development.yml logs -f

# Execute commands
docker exec -it urlshortener-server-dev php artisan migrate
docker exec -it urlshortener-server-dev vendor/bin/pint

# Access database/redis
docker exec -it urlshortener-mysql-test mysql -u urlshortener -p
docker exec -it urlshortener-redis-dev redis-cli

# Rebuild
docker-compose -f compose.development.yml build --no-cache

# Clean up
docker-compose -f compose.development.yml down -v
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
VITE_APP_URL=http://localhost:5173
```

## 📄 License

MIT License

---

**Built with ❤️ using Laravel 12, Vue 3, TypeScript, and Docker**

## 🏗 System Architecture

```
┌─────────────────────────────────────────────────────────────────────┐
│                          CLIENT LAYER                               │
│  ┌───────────────────────────────────────────────────────────────┐  │
│  │            Vue 3 SPA (TypeScript + Tailwind CSS)              │  │
│  │  • Vue Router | Pinia | Axios | shadcn-vue                    │  │
│  │  • Port: 5173 (dev) | 8080 (prod)                             │  │
│  └───────────────────────────────────────────────────────────────┘  │
│                              ↓ HTTPS/HTTP (JSON API)                │
└─────────────────────────────────────────────────────────────────────┘
                               ↓
┌─────────────────────────────────────────────────────────────────────┐
│                        WEB SERVER LAYER                             │
│  ┌───────────────────────────────────────────────────────────────┐  │
│  │  Nginx (Reverse Proxy, SSL Termination, Static Files)        │  │
│  │  • Port: 80 (HTTP) | 443 (HTTPS)                              │  │
│  └───────────────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────────┘
                               ↓
┌─────────────────────────────────────────────────────────────────────┐
│                      APPLICATION LAYER                              │
│  ┌───────────────────────────────────────────────────────────────┐  │
│  │                 Laravel 12 API (PHP 8.2)                      │  │
│  │                                                               │  │
│  │  Routes → Middleware → Controllers → Services → Models        │  │
│  │                                                               │  │
│  │  • Laravel Sanctum (Authentication)                           │  │
│  │  • L5-Swagger (API Documentation)                             │  │
│  │  • Redis Caching (URL lookups)                                │  │
│  │  • Rate Limiting (60 req/min)                                 │  │
│  │  • Port: 9000 (PHP-FPM) | 8000 (exposed)                      │  │
│  └───────────────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────────┘
           ↓                      ↓                    ↓
┌─────────────────────────────────────────────────────────────────────┐
│                          DATA LAYER                                 │
│  ┌──────────────────────────────┐  ┌──────────────────────────────┐ │
│  │          MySQL               │  │          Redis               │ │
│  │                              │  │                              │ │
│  │  Tables:                     │  │  Cache Keys:                 │ │
│  │  • users                     │  │  • url:{code}                │ │
│  │  • shortened_urls            │  │  • sessions                  │ │
│  │  • url_clicks                │  │  • queue                     │ │
│  │  • personal_access_tokens    │  │                              │ │
│  │                              │  │  TTL: 24 hours (URL cache)   │ │
│  │  Port: 3306                  │  │  Port: 6379                  │ │
│  │                              │  │                              │ │
│  │  Dev: SQLite (file-based)    │  │  Persistence: AOF enabled    │ │
│  │  Test/Prod: MySQL container  │  │                              │ │
│  └──────────────────────────────┘  └──────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────────┘
```

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

| Operation | Response Time | Throughput |
|-----------|--------------|------------|
| Redirect (cached) | <10ms | >10,000 req/s |
| Redirect (uncached) | <50ms | >1,000 req/s |
| Create URL | <100ms | >500 req/s |

### Security

✅ Sanctum auth • CSRF protection • SQL injection prevention • XSS protection • Rate limiting • Bcrypt passwords • HTTPS/SSL
