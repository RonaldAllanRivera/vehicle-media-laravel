# Changelog

All notable changes to this project will be documented in this file.

## [0.3.0] - 2025-11-05
### Added
- Phase 9: Dual data source architecture (API | DB | JSON) with resolver
- Providers: ApiVehicleMediaProvider, DatabaseVehicleMediaProvider, JsonVehicleMediaProvider
- Models & migrations: `vehicles`, `vehicle_images`
- Seeder: VehicleMediaSeeder (2017 Acura ILX sample dataset)
- Public mock JSON endpoint: `GET /mock/vehicle-media/{version}/{year}/{make}/{model}/{trim}`
- Config: `VEHICLE_MEDIA_SOURCE`, `VEHICLE_MEDIA_JSON_BASE`
- Filament page Source selector and integration with resolver
- Documentation: README updates, PLAN Phase 9, HOWTO.md tutorials
- Source field tooltip explaining differences between Local DB, Local JSON, and Live API

### Changed
- VehicleMediaSearch now uses VehicleMediaResolver instead of calling the API client directly
- Improved developer UX for working offline or without API credits

### Fixed
- Guarded grid rendering and removed inline Blade PHP to prevent raw directive output

## [0.2.0] - 2025-11-03
### Added
- VehicleMediaClient service with caching and retry logic
- Filament admin panel with Vehicle Media Search page
- Unit tests for API client
- Feature tests for admin interface
- Configuration for Vehicle Databases API

### Changed
- Updated authentication to use x-AuthKey header
- Improved error handling and user feedback
- Enhanced test coverage

## [0.1.0] - 2025-11-03
- Project initialized (docs only).
- Added PLAN.md, README.md, CHANGELOG.md, and Laravel-oriented .gitignore.
- Defined architecture and phases for Vehicle Media Admin (Laravel 12 + Filament 4).
