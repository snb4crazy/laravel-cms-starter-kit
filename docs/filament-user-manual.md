# Filament User Manual (CMS Boilerplate)

This manual explains how to run and use the Filament-based admin that is now scaffolded in this template.

## 1) What is included out of the box

The template already includes:

- Filament admin panel at `/admin`
- `Post` and `Category` resources with CRUD
- dashboard widgets (`PostStatsWidget`, `LatestPostsWidget`)
- custom `Site Settings` page
- demo data seeder with admin user + categories + posts
- permission package (`spatie/laravel-permission`) with `admin` / `editor` roles
- media, activity log, and slugs packages installed

## 2) First-time setup

Run this in project root:

```bash
cd /Users/serhiidymenko/laravel10/cms
php artisan migrate:fresh --seed
php artisan serve
```

Then open:

- App: `http://127.0.0.1:8000`
- Admin: `http://127.0.0.1:8000/admin`

## 3) Demo login

Default seeded admin credentials:

- Email: `admin@example.com`
- Password: `password`

After first login, change this password immediately for shared environments.

## 4) How to use the admin

## Dashboard

Route: `/admin`

You get:

- post count cards (`Total`, `Published`, `Drafts`)
- latest posts table

This is your quick project health overview.

## Categories

Route: `/admin/categories`

Available actions:

- create/edit/delete category
- auto-slug from name
- see posts count per category

Use categories to keep post lists organized and filterable.

## Posts

Route: `/admin/posts`

Available actions:

- create/edit/delete posts
- fields: title, slug, excerpt, content, status, publish date, featured toggle
- assign category
- list filters (status, category, featured, published today)
- quick custom action: `Publish`
- status tabs in list page (`All`, `Drafts`, `Published`, `Archived`)

Suggested workflow:

1. Create draft
2. Review content
3. Set status to `published` (or use `Publish` action)
4. Optionally mark as featured

## Site Settings

Route: `/admin/site-settings`

Currently this is a working **stub page**:

- form UI is functional
- save action shows confirmation notification
- persistence is intentionally left for your project-specific implementation

Recommended next step:

- connect this page to a settings store (for example `spatie/laravel-settings`)

## 5) Architecture notes (important for extension)

- Business logic should stay in your app/domain layers, not in panel widgets.
- Filament resources are currently UI adapters over your models.
- Keep all authorization in policies + role checks.

Main extension files:

- panel config: `app/Providers/Filament/AdminPanelProvider.php`
- resources: `app/Filament/Resources/*`
- widgets: `app/Filament/Widgets/*`
- custom pages: `app/Filament/Pages/*`
- page view: `resources/views/filament/pages/site-settings.blade.php`

## 6) How to add new CMS entity (quick pattern)

Example: add `Page` entity.

1. Create migration + model
2. Add factory + seed sample data
3. Create Filament Resource (`PageResource`)
4. Add list filters, actions, and status workflow if needed
5. Add policy + permissions
6. Add feature tests for list/create/update/delete

## 7) Permission model used in this template

Current behavior:

- demo seeder creates roles: `admin`, `editor`
- demo admin user is assigned `admin`
- `User::canAccessPanel()` checks role or local environment

For production hardening:

- remove local-environment bypass from `canAccessPanel()`
- require explicit admin role access
- add policy coverage for each resource action

## 8) Troubleshooting

## Admin login fails

- verify seeded user exists
- run: `php artisan migrate:fresh --seed`

## Routes or config look stale

```bash
cd /Users/serhiidymenko/laravel10/cms
php artisan optimize:clear
```

## Files/media not visible

```bash
cd /Users/serhiidymenko/laravel10/cms
php artisan storage:link
```

## 9) Playground checklist

Use this as an experimentation path:

1. Add 3-5 new categories
2. Create 10+ posts with mixed statuses
3. Test filtering and status tabs
4. Edit `PostResource` to add one new column + filter
5. Wire `SiteSettings` persistence to a real settings backend
6. Restrict panel to `admin` only

## 10) Related docs

- `docs/install-bundles.md`
- `docs/dual-admin-architecture.md`
- `docs/admin-panel-switching.md`

