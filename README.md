# CMS Boilerplate (Laravel 10)

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

## License

This project is open-sourced under the [MIT license](https://opensource.org/licenses/MIT).
