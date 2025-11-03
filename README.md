# Vehicle Media Admin (Laravel 12 + Filament 4)

Admin tool to search vehicle media by year, make, model, and trim, powered by Vehicle Databases API. Results show previews of vehicle images. Built with Laravel 12 and Filament 4 using the TALL stack.

## Features
- [x] Filament 4 admin panel with authentication
- [x] Vehicle Media Search page with form
- [x] API client with retries, caching, and error handling
- [x] Responsive UI with Tailwind CSS
- [x] Unit and feature tests
- [x] Image previews grouped by category (exterior, interior, colors)
- [x] Configurable API settings via environment variables

## Requirements
- PHP >= 8.2
- Composer
- Node.js (local only, for asset build) – not required on SiteGround
- Database (SQLite/MySQL) for Filament users and logs

## Quickstart (Local: Laragon)
1. Create the project (inside `E:\laragon\www\vehicle-media-laravel`):
   - composer create-project laravel/laravel .
   - php artisan key:generate
2. Install Filament 4:
   - composer require filament/filament:"^4.0" livewire/livewire:"^3.0"
   - php artisan make:filament-user
3. Configure environment:
   - Copy `.env.example` to `.env`
   - Set values below (API key provided by Vehicle Databases):

```
VEHICLE_DB_API_KEY=your_api_key_here
VEHICLE_DB_BASE=https://api.vehicledatabases.com
VEHICLE_DB_VERSION=v1
VEHICLE_DB_TIMEOUT=12
VEHICLE_DB_RETRIES=2
VEHICLE_DB_SLEEP_MS=350
VEHICLE_DB_DEFAULT_IMAGES_PER_CALL=10
```

4. Run migrations and serve:
   - php artisan migrate
   - php artisan serve

## Development Progress

### Phase 1: Setup & Authentication ✅
- [x] Initialize Laravel 12 project
- [x] Install and configure Filament 4
- [x] Set up authentication
- [x] Configure Tailwind CSS with Vite

### Phase 2: Core Functionality ✅
- [x] Implement VehicleMediaClient service
- [x] Create search interface in Filament
- [x] Add image preview functionality
- [x] Implement error handling and loading states

### Phase 3: Testing & Polish
- [ ] Add more test coverage
- [ ] Implement admin user management
- [ ] Add role-based access control
- [ ] Optimize image loading and caching

## Development (Assets)
- Filament ships its own styles for admin. If you add custom pages outside Filament:
  - `npm install`
  - `npm run dev`
- For TailwindCSS, compile locally only; SiteGround will serve built assets from `public/build`.

## Architecture
- `App/Services/VehicleDatabases/Client` – HTTP client (versioned paths, auth header, caching)
- `App/Services/VehicleSearchService` – orchestrates year-range requests, aggregates results
- `App/Http/Livewire` or Filament Page – search form + table + actions (view/download)
- `App/Services/MediaDownloadService` – streams ZIP from URLs and cleans temp files
- Config in `config/vehicle_media.php`

## API Notes
- Base: `https://api.vehicledatabases.com`
- Vehicle Media by YMMT (v1 docs): see https://vehicledatabases.com/documentation/#vehicle-media-v1
- We normalize API responses to `{ status, data: { year, make, model, trim, images: { exterior[], interior[], colors[] }}}`
- Respect rate limits; throttle requests, especially across year ranges

## Testing
- php artisan test
- HTTP fakes for API client (success, 4xx, 5xx, timeouts)

## Deployment (SiteGround)
- Build locally: `npm run build` (optional, for custom UI)
- Upload repo (exclude `node_modules` and dev files)
- Ensure `.env` is set on the server (never commit it)
- php artisan storage:link
- php artisan optimize
- Confirm `public/` as document root (or point a subdirectory domain)

## Security
- Secrets in `.env` only
- Validate and sanitize user inputs on admin forms
- Limit downloads, size, and duration; rotate temp files

## Changelog
See [CHANGELOG.md](CHANGELOG.md)
