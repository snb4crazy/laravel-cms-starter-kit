# Reusable Libraries for a Laravel CMS Boilerplate

This list is opinionated for Laravel 12 and a fresh app.

## Core starter picks (recommended first)

### 1) Admin UI
- **filament/filament**: fast admin panel, forms, tables, resources.
- Why: fastest path to a usable CMS back office.
- Alternative: custom admin with Inertia + Vue/React if you need full control.
- Boilerplate policy: keep Filament as default and switch via `ADMIN_PANEL` when a project requires Inertia.

### 2) Roles and permissions
- **spatie/laravel-permission**: roles, permissions, guards, middleware support.
- Why: battle-tested and easy to connect to admin panels.

### 3) Media management
- **spatie/laravel-medialibrary**: file uploads, image conversions, storage drivers.
- Why: standard solution for CMS image/file handling.

### 4) Activity/audit log
- **spatie/laravel-activitylog**: track who changed what.
- Why: very useful for editorial teams and debugging.

### 5) Slugs
- **spatie/laravel-sluggable**: stable slugs from title fields.
- Why: common CMS need; avoids repeating logic.

### 6) Settings/config in DB
- **spatie/laravel-settings** (or equivalent package): app and site settings with typed DTOs.
- Why: allows admin-managed global settings.

## Add when needed (phase 2)

### Search
- **laravel/scout** + engine:
  - **typesense/typesense-php** (or Typesense server)
  - **meilisearch/meilisearch-php**
- Why: simple abstraction; can start DB search first and upgrade later.

### SEO and sitemap
- **ralphjsmit/laravel-seo** (or **artesaos/seotools**) for meta tags.
- **spatie/laravel-sitemap** for sitemap generation.

### Backups and uptime
- **spatie/laravel-backup** for scheduled backups.
- **spatie/laravel-health** for health checks.

### API/Auth for external clients
- **laravel/sanctum** for token/cookie auth.
- **knuckleswtf/scribe** for API documentation.

### HTTP and integrations
- **spatie/laravel-webhook-client** if you consume webhooks.
- **spatie/laravel-rate-limited-job-middleware** for safer queues/integrations.

## Dev quality and team workflow

### Code quality
- **laravel/pint** (already included)
- **phpstan/phpstan** + **larastan/larastan** for static analysis
- Optional: **rector/rector** for automated refactors

### Testing
- **phpunit/phpunit** (already included)
- Optional: **pestphp/pest** for more expressive tests
- **fakerphp/faker** (already included)

### Local dev and debugging
- **laravel/telescope** for request/job/event debugging
- **barryvdh/laravel-debugbar** for local profiling
- **spatie/laravel-ignition** if needed for exception UX (usually default in Laravel stack)

## Suggested install bundles

### MVP bundle
- filament/filament
- spatie/laravel-permission
- spatie/laravel-medialibrary
- spatie/laravel-activitylog
- spatie/laravel-sluggable

### Growth bundle
- laravel/scout + meilisearch or typesense
- spatie/laravel-sitemap
- ralphjsmit/laravel-seo (or artesaos/seotools)
- spatie/laravel-backup
- phpstan/phpstan + larastan/larastan

## Notes for reusability

- Keep package-specific code behind your own interfaces/services.
- Put package config into `config/*.php` and keep defaults conservative.
- Add feature toggles so projects can disable modules they do not need.
- Prefer migration-safe patterns (do not hard-couple project data to package internals).

