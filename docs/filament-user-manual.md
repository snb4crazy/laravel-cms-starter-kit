# Filament User Manual (CMS Boilerplate)

This manual explains how to run and use the Filament-based admin that is now scaffolded in this template.

## 1) What is included out of the box

The template already includes:

- Filament admin panel at `/admin`
- `Post` and `Category` resources with CRUD
- showcase dashboard widgets (stats, content health, trend chart, recent posts, activity, quick links)
- custom `Site Settings` page
- demo data seeder with admin user + categories + posts
- permission package (`spatie/laravel-permission`) with `admin` / `editor` roles
- media, activity log, and slugs packages installed

## 2) First-time setup

Configure your local database first (MySQL/PostgreSQL/SQLite):

- `docs/database-setup.md`

Run this in project root:

```bash
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

- built-in account panel (`AccountWidget`)
- built-in Filament info block (`FilamentInfoWidget`)
- `PostStatsWidget`: main post KPIs (`Total`, `Published`, `Drafts`, `Archived`)
- `ContentHealthWidget`: uncategorized posts, featured posts, empty categories, stale content
- `PublishingTrendChartWidget`: chart of content creation over time with range filters
- `LatestPostsWidget`: latest content table with category + status
- `CategoryOverviewWidget`: category table with post counts and publish snapshot
- `RecentActivityWidget`: recent model activity from `activity_log`
- `QuickLinksWidget`: shortcut cards for demo flows and useful entry points

This is a Filament showcase dashboard meant to demonstrate multiple widget types at once.

### How to extend, update, or remove dashboard widgets

Main registration file:

- `app/Providers/Filament/AdminPanelProvider.php`

Current dashboard widget files:

- `app/Filament/Widgets/PostStatsWidget.php`
- `app/Filament/Widgets/ContentHealthWidget.php`
- `app/Filament/Widgets/PublishingTrendChartWidget.php`
- `app/Filament/Widgets/LatestPostsWidget.php`
- `app/Filament/Widgets/CategoryOverviewWidget.php`
- `app/Filament/Widgets/RecentActivityWidget.php`
- `app/Filament/Widgets/QuickLinksWidget.php`
- `resources/views/filament/widgets/quick-links-widget.blade.php`

How to customize:

1. **Reorder widgets**: move class names in `AdminPanelProvider::panel()->widgets([...])` and/or change each widget's `protected static ?int $sort` value.
2. **Remove widgets**: delete the widget class from the `->widgets([...])` array.
3. **Change queries**: edit the Eloquent queries inside each widget class.
4. **Change layout width**: update `$columnSpan` in the widget class.
5. **Change labels, icons, chart filters, descriptions**: edit the widget class methods directly.
6. **Add a new widget**: create a new class in `app/Filament/Widgets/` and register it in `AdminPanelProvider`.

Recommended demo tweaks:

- swap `RecentActivityWidget` to your own audit/event model later if needed
- replace `QuickLinksWidget` shortcuts with project-specific actions
- turn off `FilamentInfoWidget` once the project moves from demo to production

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

Current behavior:

- form UI is functional
- settings persist in cache for demo purposes
- `maintenance_mode` affects frontend routes wrapped in the maintenance middleware
- admins can still access the panel while maintenance mode is enabled

Recommended next step:

- replace cache-based persistence with a real settings store (for example `spatie/laravel-settings`)
- apply the maintenance middleware to any additional public routes you add later

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
- custom widget view: `resources/views/filament/widgets/quick-links-widget.blade.php`

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
php artisan optimize:clear
```

## Files/media not visible

```bash
php artisan storage:link
```

## 9) Playground checklist

Use this as an experimentation path:

1. Add 3-5 new categories
2. Create 10+ posts with mixed statuses
3. Test filtering and status tabs
4. Edit `PostResource` to add one new column + filter
5. Replace cached `SiteSettings` values with a real settings backend
6. Add or remove one dashboard widget to fit a real project
7. Restrict panel to `admin` only

## 10) Related docs

- `docs/install-bundles.md`
- `docs/database-setup.md`
- `docs/dual-admin-architecture.md`
- `docs/admin-panel-switching.md`

## 11) Git policy: source vs generated Filament files

Use this policy in all projects based on this template.

Commit (source of truth):

- `app/Providers/Filament/AdminPanelProvider.php`
- `app/Filament/*`
- `resources/views/filament/*`
- related config, models, migrations, seeders

Do not commit (generated assets):

- `public/css/filament/*`
- `public/js/filament/*`

If these generated files were committed before, untrack them once:

```bash
git rm -r --cached public/css/filament public/js/filament
git add .gitignore
git commit -m "chore: stop tracking generated filament assets"
```

Deployment/build note:

- Run `php artisan filament:assets` during build/release so assets are regenerated on the target environment.

