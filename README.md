# Star Wars Analytics Platform

<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="300" alt="Laravel Logo">
</p>

<p align="center">
  <strong>A comprehensive Star Wars search application with advanced analytics and performance monitoring</strong>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-11-FF2D20?logo=laravel" alt="Laravel 11">
  <img src="https://img.shields.io/badge/Vue.js-3-4FC08D?logo=vue.js" alt="Vue.js 3">
  <img src="https://img.shields.io/badge/TypeScript-5-3178C6?logo=typescript" alt="TypeScript">
  <img src="https://img.shields.io/badge/Docker-20.10+-2496ED?logo=docker" alt="Docker">
  <img src="https://img.shields.io/badge/Redis-7.2-DC382D?logo=redis" alt="Redis">
</p>

## Overview

Star Wars Analytics Platform is a sophisticated web application that combines the power of the Star Wars API (SWAPI) with advanced analytics capabilities. Built with Laravel and Vue.js, it provides real-time search functionality for Star Wars films and characters while collecting comprehensive usage statistics and performance metrics.

### Key Features

🔍 **Advanced Search System**
- Real-time search across Star Wars films and characters
- Intelligent caching for optimal performance
- Detailed film and character information pages
- Type-safe search with comprehensive error handling

📊 **Comprehensive Analytics Dashboard**
- Performance metrics with response time percentiles
- Search quality analysis and effectiveness ratios
- User behavior tracking (devices, browsers, geographic data)
- Content analysis with query patterns and word frequency
- Real-time hourly usage distribution

⚡ **High-Performance Architecture**
- Event-driven search logging system
- Background job processing with Redis queues
- Intelligent caching strategies
- Database optimization with strategic indexing

🐳 **Production-Ready Infrastructure**
- Complete Docker containerization
- Multi-service architecture with Redis
- Automated queue workers and task scheduling
- Comprehensive logging and monitoring

## Technology Stack

### Backend
- **Laravel 11** - PHP web application framework
- **PHP 8.2** - Modern PHP with performance enhancements
- **SQLite** - Lightweight database for development
- **Redis** - Caching and queue management
- **Laravel Queues** - Background job processing
- **Laravel Events** - Event-driven architecture

### Frontend
- **Vue.js 3** - Progressive JavaScript framework
- **TypeScript** - Type-safe JavaScript development
- **Inertia.js** - Modern monolithic applications
- **Tailwind CSS** - Utility-first CSS framework
- **Vite** - Fast build tool with HMR
- **Vue Server Renderer** - Server-side rendering

### Infrastructure & DevOps
- **Docker & Docker Compose** - Containerization
- **Nginx** - Web server and reverse proxy
- **Supervisor** - Process monitoring
- **Redis** - In-memory data store
- **Queue Workers** - Background processing

### Testing & Quality
- **Pest PHP** - Modern testing framework
- **Laravel Breeze** - Authentication scaffolding
- **ESLint & Prettier** - Code formatting and linting
- **Laravel Pint** - PHP code style fixer

## Installation & Setup

### Prerequisites
- Docker and Docker Compose
- Node.js 18+ and npm
- PHP 8.2+ and Composer (for local development)

### Docker Setup (Recommended)

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd <repository-name>
   ```

2. **Build and start the services**
   ```bash
   docker-compose up -d --build
   ```

3. **Access the application**
   - Main application: http://localhost:8000
   - Redis: localhost:6379

The Docker setup includes:
- **App Container**: Laravel application with Nginx and PHP-FPM
- **Redis Container**: Caching and queue backend
- **Queue Worker**: Background job processing
- **Scheduler**: Automated task execution

### Local Development Setup

1. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

2. **Environment configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Database setup**
   ```bash
   touch database/database.sqlite
   php artisan migrate
   ```

4. **Start development services**
   ```bash
   # All services (recommended)
   composer dev
   
   # Or manually:
   php artisan serve
   php artisan queue:work
   npm run dev
   ```

## Advanced Analytics System

### Statistics Collection

The platform automatically collects comprehensive analytics on every search query:

#### Performance Metrics
- **Response Time Analysis**: P50, P90, P95, P99 percentiles
- **Slow Query Detection**: Configurable threshold monitoring
- **Performance Trends**: Historical response time tracking

#### Search Quality Analytics
- **Zero Result Analysis**: Queries returning no results
- **Search Effectiveness**: Success rate calculations
- **Results Distribution**: Statistical breakdown of result counts
- **Average Results per Search**: Performance indicators

#### User Behavior Insights
- **Device Type Detection**: Mobile, tablet, desktop classification
- **Browser Analytics**: Chrome, Firefox, Safari, Edge tracking
- **Search Type Distribution**: Films vs characters preference
- **Geographic Insights**: IP-based location analysis

#### Content Analysis
- **Query Length Distribution**: Character count analysis
- **Word Frequency Analysis**: Most common search terms
- **Search Patterns**: Quoted queries, numeric searches, pattern detection
- **Hourly Usage Distribution**: 24-hour activity patterns

### Data Models

#### SearchQuery Model
```php
// Core tracking fields
'query' => 'Search term',
'type' => 'films|people',
'results_count' => 42,
'response_time_ms' => 156,
'searched_at' => '2024-01-01 12:00:00',

// Extended analytics fields
'user_agent' => 'Browser information',
'ip_address' => 'User IP address',
'session_id' => 'Session identifier',
'country_code' => 'US',
'device_type' => 'Mobile|Tablet|Desktop',
'browser' => 'Chrome|Firefox|Safari|Edge|Opera',
'has_results' => true,
'referrer' => 'Source URL',
'query_metadata' => '{"filters": [], "sorting": "name"}'
```

### Background Processing

Statistics computation runs automatically via:
- **Scheduled Jobs**: `ComputeSearchStatistics` runs every 5 minutes
- **Queue Workers**: Process intensive analytics in background
- **Caching Strategy**: Results cached for 7 days with smart invalidation

## API Documentation

### Search Endpoints

#### Search Films and Characters
```http
GET /api/search?query={query}&type={films|people}
```

**Parameters:**
- `query` (required): Search query string
- `type` (required): Search type - 'films' or 'people'

**Response:**
```json
{
  "data": [
    {
      "uid": "1",
      "properties": {
        "title": "A New Hope",
        "episode_id": 4,
        "director": "George Lucas",
        "producer": "Gary Kurtz, Rick McCallum",
        "release_date": "1977-05-25"
      }
    }
  ]
}
```

#### Get Film Details
```http
GET /api/films/{id}
```

#### Get Character Details
```http
GET /api/people/{id}
```

### Web Routes

- `/` - Main search interface
- `/films/{id}` - Film details page
- `/people/{id}` - Character details page
- `/statistics` - Analytics dashboard

## Development Workflow

### Available Scripts

```bash
# Development with all services
composer dev

# Build frontend assets
npm run build

# Development with hot reload
npm run dev

# Code formatting
npm run lint
vendor/bin/pint

# Testing
vendor/bin/pest
```

### Code Quality Tools

- **ESLint**: JavaScript/TypeScript linting
- **Prettier**: Code formatting with import organization
- **Laravel Pint**: PHP code style fixing
- **TypeScript**: Static type checking

### Event-Driven Architecture

```php
// Search events automatically trigger analytics
Event::dispatch(new SearchPerformed($query, $type, $results, $responseTime));

// Listeners handle data collection
class LogSearchQuery
{
    public function handle(SearchPerformed $event)
    {
        SearchQuery::create([
            'query' => $event->query,
            'type' => $event->type,
            'results_count' => $event->resultsCount,
            'response_time_ms' => $event->responseTime,
            // ... additional tracking data
        ]);
    }
}
```

## Infrastructure & Deployment

### Docker Services Architecture

```yaml
services:
  app:          # Main Laravel application with Nginx
  redis:        # Cache and queue backend
  queue-worker: # Background job processing
  scheduler:    # Automated task execution
```

### Environment Variables

Key configuration options:
- `APP_ENV`: Application environment
- `QUEUE_CONNECTION=redis`: Queue backend
- `CACHE_STORE=redis`: Cache backend
- `DB_CONNECTION=sqlite`: Database driver

### Performance Optimizations

- **Database Indexing**: Strategic indexes for analytics queries
- **Query Optimization**: Chunked processing for large datasets
- **Caching Strategy**: Multi-layered caching with Redis
- **User Agent Caching**: Parsed browser data caching
- **Memory Management**: Efficient collection processing

### Monitoring & Logging

- **Application Logs**: Comprehensive Laravel logging
- **Performance Tracking**: Response time monitoring
- **Error Handling**: Graceful failure recovery
- **Docker Logging**: Container-level log management

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Make your changes with proper testing
4. Commit your changes (`git commit -m 'Add amazing feature'`)
5. Push to the branch (`git push origin feature/amazing-feature`)
6. Open a Pull Request

### Development Standards
- Follow PSR-12 coding standards
- Use TypeScript for frontend development
- Follow Vue.js 3 Composition API patterns
- Document all public methods and classes

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

<p align="center">
  Built with ❤️ using Laravel, Vue.js, and the Star Wars API
</p>
