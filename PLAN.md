# Project Plan: Vehicle Media Admin (Laravel 12 + Filament 4)

Goal: Admin can search vehicles by year range, make, model, trim, optional color, and transparent background, fetch 10 images per API call from Vehicle Databases, preview them, and download as ZIP. Deployable on SiteGround (PHP-only), using the TALL stack.

Docs to align: https://vehicledatabases.com/documentation/#vehicle-media-v1 (design a versioned client to support v1/v2 paths)

---

## ✅ Phase 0 — Prerequisites & Decisions (Completed)
- **Stack**: Laravel 12, PHP 8.2+, Filament 4 (Livewire v3), Tailwind CSS (built via Vite locally), SQLite/MySQL for Filament users.
- **Secrets**: `.env` only (API key, base URL, timeouts).
- **SiteGround**: No Node on server. Build assets locally and deploy compiled `public/build`.
- **API**: Versioned client, base URL configurable. Rate-limit, retry, and cache.

Deliverables:
- Composer + NPM toolchains ready locally
- Repo initialized with docs and .gitignore

---

## Phase 1 — Project Setup & Authentication (Completed) ✅
- Created Laravel 12 project in `E:\laragon\www\vehicle-media-laravel`
- Installed and configured Filament 4 with authentication
- Set up Tailwind CSS with Vite for local development
- Created admin user with credentials (email: `jaeron.rivera@gmail.com`)
- Configured environment variables and application settings

Acceptance:
- [x] Admin can log in at `/admin`
- [x] Basic Filament admin panel is accessible
- [x] Environment configuration is properly set up

---

## Phase 2 — API Client Implementation (Completed) ✅
- Implemented `App\Services\VehicleMediaClient` with Laravel HTTP client
- Added configuration in `config/vehicle_media.php`
- Implemented request retries and exponential backoff
- Added response caching with TTL
- Implemented error handling and logging
- Added slugification for URL parameters

Acceptance:
- [x] Unit tests for client methods
- [x] Proper error handling for API responses
- [x] Caching mechanism working as expected
- [x] Test coverage for success and error cases

### Testing
Run the test suite with:
```bash
# Run all tests
php artisan test

# Run only unit tests
php artisan test --testsuite=Unit

# Run only feature tests
php artisan test --testsuite=Feature

# Run with coverage (requires Xdebug)
php artisan test --coverage
- Service: `App\Services\VehicleDatabases\Client` using Laravel Http client.
- Config: `config/vehicle_media.php` with `api_base`, `api_key`, `timeout`, `retries`, `sleep_ms`, `version`.
- Methods:
  - `getMedia(year, make, model, trim, options)` → normalized payload `{status, data: {year, make, model, trim, images: {exterior[], interior[], colors[]}}}`.
  - Support optional `color`, `transparent` flags. Ensure 10-image limit.
- Resilience: retry (exponential backoff), circuit-breaker-ish logging, caching per request key.

Acceptance:
- Unit tests with `Http::fake()` cover success, 4xx, 5xx, timeouts.

---

## Phase 3 — Filament Admin Interface (In Progress) 🚧
- Created `VehicleMediaSearch` Filament page
- Implemented search form with default values
- Added image preview functionality
- Set up error handling and loading states
- Implemented responsive UI with Tailwind CSS

Acceptance:
- [x] Admin can access Vehicle Media Search page
- [x] Form with year, make, model, and trim fields
- [x] Image preview functionality
- [ ] Error handling for API failures
- [ ] Loading states during search

## Phase 4 — Search Orchestrator
- Service: `App\Services\VehicleSearchService` that:
  - Validates inputs.
  - Iterates from `from_year..to_year`, calls client per year (respect delay), aggregates results.
  - Normalizes into DTOs for the UI (media_count, image preview URL, year/make/model/trim).
  - Caching keyed by criteria for faster repeats.

Acceptance:
- Unit tests for range aggregation and caching behavior.

---

## Phase 5 — Filament Admin UI (Planned)
- Create Panel page “Vehicle Media”.
- Filters: from_year, to_year, make, model, trim, color (optional), transparent (toggle), use_cache.
- Table: year, make, model, trim, media_count, actions.
- Actions:
  - View: open modal with tabs (exterior/interior/colors), paginated if needed.
  - Download: queue/stream a ZIP of selected images (default 10).
- UX: Empty states, error toasts, loading indicators.

Acceptance:
- Admin can search Toyota 2018–2022, sees year-bucketed rows, previews images.

---

## Phase 6 — Downloads (ZIP) (Planned)
- `App\Services\MediaDownloadService` (ZipArchive) creates ZIP in `storage/app/tmp/media/{uuid}.zip`.
- Streamed downloads; cleanup job scheduled (e.g., nightly) to purge old zips.
- Optional: allow selecting categories (exterior/interior/colors).

Acceptance:
- ZIP downloads reliably; files cleaned after TTL.

---

## Phase 7 — Testing & Observability (In Progress) 🚧
- [x] Unit tests for VehicleMediaClient
- [x] Feature tests for admin interface
- [ ] Test coverage for all major components
- [ ] Logging and error tracking setup
- [ ] Performance monitoring

### Current Test Coverage
- Unit tests for API client (success/error cases)
- Feature tests for authentication and authorization
- Basic UI component tests

### Next Testing Goals
- [ ] Add more integration tests
- [ ] Implement browser tests
- [ ] Set up continuous integration
- [ ] Add performance benchmarks
- Logging with context (criteria, year, request id, status).
- Feature tests for the Filament page (form submit → table populated → view/download actions).
- Error UX for no results / invalid trim / API rate limits.

Acceptance:
- Test suite green; helpful logs in `storage/logs`.

---

## Phase 8 — Deployment (SiteGround) (Planned)
- Build assets locally: `npm run build` → commit `public/build` (or deploy artifacts).
- Configure `.env`, storage permissions, `php artisan storage:link`.
- Cache config/routes/views, disable debug, set app key.

Acceptance:
- Admin panel accessible; searches and downloads work on SiteGround.

---

## Phase 8 — Nice-to-haves
- Persist search history for admins.
- Rate-limit knobs in settings.
- Color picker fed by API-provided colors.
- Background removal preview when transparent toggle is on (if supported).
