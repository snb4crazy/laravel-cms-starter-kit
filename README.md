# CMS Boilerplate (Laravel 10)

[![CI](https://github.com/snb4crazy/laravel-cms-starter-kit/actions/workflows/ci.yml/badge.svg?branch=main)](https://github.com/snb4crazy/laravel-cms-starter-kit/actions/workflows/ci.yml)
[![Security](https://github.com/snb4crazy/laravel-cms-starter-kit/actions/workflows/security.yml/badge.svg?branch=main)](https://github.com/snb4crazy/laravel-cms-starter-kit/actions/workflows/security.yml)
[![Deploy Staging](https://github.com/snb4crazy/laravel-cms-starter-kit/actions/workflows/deploy-staging.yml/badge.svg?branch=main)](https://github.com/snb4crazy/laravel-cms-starter-kit/actions/workflows/deploy-staging.yml)
[![Deploy Production](https://github.com/snb4crazy/laravel-cms-starter-kit/actions/workflows/deploy-production.yml/badge.svg)](https://github.com/snb4crazy/laravel-cms-starter-kit/actions/workflows/deploy-production.yml)

Reusable CMS starter built on Laravel, with a **Filament-first admin** and an **Inertia-ready architecture** for projects that need a custom panel later.

## What this template gives you

- Filament admin panel at `/admin`
- ready CRUD examples (`Post`, `Category`)
- dashboard widgets to showcase stats, trends, activity, and quick links
- Media Library integration for post cover images
- role/permission baseline (`admin`, `editor`)
- docs-first setup with architecture decisions and rollout guides

## Quick start

```bash
cp .env.example .env
composer install
npm install
php artisan key:generate
php artisan migrate:fresh --seed
php artisan storage:link
php artisan serve
```

Open:

- App: `http://127.0.0.1:8000`
- Admin: `http://127.0.0.1:8000/admin`

Demo admin credentials (from seeder):

- Email: `admin@example.com`
- Password: `password`

## Admin panel strategy

- default is controlled by `ADMIN_PANEL` in `.env`
- supported values: `filament`, `inertia`
- config file: `config/admin.php`
- switch helper script: `scripts/switch-admin-panel.sh`

Use the detailed guide before switching:

- `docs/admin-panel-switching.md`
- `docs/dual-admin-architecture.md`

## Documentation map

Start here:

- `docs/README.md` - **CMS Boilerplate Notes** index

Core setup and usage:

- `docs/database-setup.md` - database setup (MySQL-first, env mapping, migrate/seed)
- `docs/filament-user-manual.md` - using and extending the admin panel
- `docs/media-library-implementation.md` - detailed media architecture and roadmap
- `docs/media-library-handoff.md` - practical media defaults and handoff notes
- `docs/ci-cd.md` - GitHub Actions workflows and deployment guidance

Architecture and planning:

- `docs/reusable-libraries.md` - recommended external packages
- `docs/build-it-yourself.md` - features to keep in app code
- `docs/install-bundles.md` - install phases and command bundles
- `docs/dual-admin-architecture.md` - Filament-first + Inertia-ready boundaries
- `docs/decisions/0001-dual-admin-strategy.md` - ADR for admin strategy

## Notes for template reuse

- keep this repo as a clean baseline, then branch per project
- keep domain logic outside panel code so either admin adapter can evolve
- commit source files for Filament, not generated assets under `public/css/filament` and `public/js/filament`

## Project status and roadmap

- **Current default**: Filament-first CMS stub is ready for local development and demos.
- **CI/CD baseline**: CI, security, staging deploy, and production deploy workflows are present under `.github/workflows`.
- **Architecture**: dual-admin policy is documented, with `ADMIN_PANEL` support for Filament/Inertia switching.
- **Near-term roadmap**: expand feature tests around media and content workflows, and harden production auth/permissions.
- **Maintenance note**: workflows are configured for the `main` branch; align your default branch naming when creating new repos.

## Create new project from this template

Option 1 (GitHub UI):

1. Click **Use this template** on the repository page.
2. Create your new repository.
3. Clone it and run the setup commands below.

Option 2 (`gh` CLI):

```bash
gh repo create your-org/your-new-cms --template snb4crazy/laravel-cms-starter-kit --private --clone
cd your-new-cms
cp .env.example .env
composer install
npm install
php artisan key:generate
php artisan migrate:fresh --seed
php artisan storage:link
php artisan serve
```

Recommended first project customization:

1. Set `APP_NAME`, `APP_URL`, and database variables in `.env`.
2. Decide admin mode with `ADMIN_PANEL=filament` or `ADMIN_PANEL=inertia`.
3. Review docs in `docs/README.md` and complete `docs/database-setup.md`.
4. Replace seeded credentials and rotate any local/testing defaults before sharing environments.

## License

This project is open-sourced under the [MIT license](https://opensource.org/licenses/MIT).
