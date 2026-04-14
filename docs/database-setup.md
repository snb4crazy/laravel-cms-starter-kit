# Database Setup Guide

This guide explains how to configure the CMS boilerplate database for local development.

Default local example used in this doc:

- `DB_CONNECTION=mysql`
- `DB_DATABASE=cms-filament`
- `DB_USERNAME=filament`
- `DB_PASSWORD=filament`

## 1) Pick your database driver in `.env`

Open `.env` and set the DB values for your local environment.

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cms-filament
DB_USERNAME=filament
DB_PASSWORD=filament
```

If you are changing DB values after first boot, clear cached config:

```bash
cd /Users/serhiidymenko/laravel10/cms
php artisan optimize:clear
```

## 2) MySQL setup (create DB + user + permissions)

Login as a MySQL admin user (for example `root`) and run:

```sql
CREATE DATABASE IF NOT EXISTS `cms-filament`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

CREATE USER IF NOT EXISTS 'filament'@'localhost' IDENTIFIED BY 'filament';
GRANT ALL PRIVILEGES ON `cms-filament`.* TO 'filament'@'localhost';
FLUSH PRIVILEGES;
```

If your app connects over TCP (`127.0.0.1`) and your MySQL setup requires it, also add:

```sql
CREATE USER IF NOT EXISTS 'filament'@'127.0.0.1' IDENTIFIED BY 'filament';
GRANT ALL PRIVILEGES ON `cms-filament`.* TO 'filament'@'127.0.0.1';
FLUSH PRIVILEGES;
```

## 3) Run migrations and seed demo data

Run one of these flows:

- First install or keep existing data structure:

```bash
cd /Users/serhiidymenko/laravel10/cms
php artisan migrate
php artisan db:seed
```

- Reset DB and reseed from scratch:

```bash
cd /Users/serhiidymenko/laravel10/cms
php artisan migrate:fresh --seed
```

`DatabaseSeeder` calls `CmsDemoSeeder`, which creates:

- admin user
- roles (`admin`, `editor`)
- sample categories and posts

## 4) Demo admin credentials and how to change them

Current seeded credentials (development only):

- Email: `admin@example.com`
- Password: `password`

They are defined in `database/seeders/CmsDemoSeeder.php`.

To use your own test credentials:

1. Edit the values in `CmsDemoSeeder`.
2. Re-run `php artisan migrate:fresh --seed`.

For shared/staging environments, always change seeded defaults immediately.

## 5) Other DB engines (quick notes)

### PostgreSQL

Use this `.env` pattern:

```dotenv
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=cms_filament
DB_USERNAME=filament
DB_PASSWORD=filament
```

Example SQL:

```sql
CREATE DATABASE cms_filament;
CREATE USER filament WITH ENCRYPTED PASSWORD 'filament';
GRANT ALL PRIVILEGES ON DATABASE cms_filament TO filament;
```

### SQLite

Create the file and use:

```bash
cd /Users/serhiidymenko/laravel10/cms
touch database/database.sqlite
```

```dotenv
DB_CONNECTION=sqlite
DB_DATABASE=/Users/serhiidymenko/laravel10/cms/database/database.sqlite
```

Then run migrations and seeding as usual.

## 6) Troubleshooting

- `SQLSTATE[HY000] [1049] Unknown database`: database name in `.env` does not exist.
- `Access denied for user`: user/password/host grant mismatch (`localhost` vs `127.0.0.1`).
- `.env` changes are ignored: run `php artisan optimize:clear`.
- Seeder errors on roles/tables: run migrations before seeding.

## Related docs

- `docs/filament-user-manual.md`
- `docs/install-bundles.md`
- `docs/admin-panel-switching.md`


