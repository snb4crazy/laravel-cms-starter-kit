# Install Bundles (Copy/Paste)

These commands are optional starter bundles for your CMS template.

Default policy for this boilerplate:
- Install Filament first.
- Keep `ADMIN_PANEL=filament` by default.
- Switch to Inertia later with `scripts/switch-admin-panel.sh inertia`.

## MVP bundle

```bash
cd /Users/serhiidymenko/laravel10/cms
composer require filament/filament spatie/laravel-permission spatie/laravel-medialibrary spatie/laravel-activitylog spatie/laravel-sluggable
php artisan migrate
```

## Quality bundle

```bash
cd /Users/serhiidymenko/laravel10/cms
composer require --dev phpstan/phpstan larastan/larastan
```

## Search bundle (pick one engine)

### Scout + Meilisearch

```bash
cd /Users/serhiidymenko/laravel10/cms
composer require laravel/scout meilisearch/meilisearch-php
```

### Scout + Typesense

```bash
cd /Users/serhiidymenko/laravel10/cms
composer require laravel/scout typesense/typesense-php
```

## SEO + sitemap bundle

```bash
cd /Users/serhiidymenko/laravel10/cms
composer require ralphjsmit/laravel-seo spatie/laravel-sitemap
```

## Ops bundle

```bash
cd /Users/serhiidymenko/laravel10/cms
composer require spatie/laravel-backup spatie/laravel-health
```

## Optional Inertia UI foundation bundle

Install this only when a project needs custom admin UX.

```bash
cd /Users/serhiidymenko/laravel10/cms
composer require laravel/breeze --dev
php artisan breeze:install vue
npm install
npm run build
```

## Notes

- Run installs in small batches to keep troubleshooting easy.
- After each bundle, run tests and check migrations.
- Pin versions in production templates once you are happy with the stack.
- For panel switching workflow, see `docs/admin-panel-switching.md`.

