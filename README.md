# Vehicle Media Admin (Laravel 12 + Filament 4)

Admin tool to search vehicle media by year range, make, model, trim, optional color, and transparent background, powered by Vehicle Databases API. Results show previews and allow ZIP downloads. Built for PHP-only hosting (SiteGround) using the TALL stack.

## Features
- Filament 4 admin panel (TALL stack)
- Search by From/To Year (per-year API requests)
- Filters: Make, Model, Trim, Color (optional), Transparent background (optional)
- Exactly 10 images per API call (configurable cap)
- Image previews grouped by category (exterior, interior, colors) when available
- Download images as ZIP (streamed), temp cleanup
- Robust API client with retries, throttling, and caching
- All secrets in `.env`

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

## Development (Assets)
- Filament ships its own styles for admin. If you add custom pages outside Filament:
  - npm install
  - npm run dev
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
