# Admin Panel Switching Guide

This template uses one environment flag to choose the default admin panel.

- `ADMIN_PANEL=filament` (default)
- `ADMIN_PANEL=inertia`

## Why an env flag

- Different projects from the same boilerplate can choose different admin stacks.
- No code rewrite is needed to change the default panel.
- CI, staging, and production can keep independent panel selection.

## Helper script

Use the script in `scripts/switch-admin-panel.sh`.

### Dry run (safe preview)

```bash
cd /Users/serhiidymenko/laravel10/cms
scripts/switch-admin-panel.sh inertia --dry-run
```

### Real switch to Filament

```bash
cd /Users/serhiidymenko/laravel10/cms
scripts/switch-admin-panel.sh filament
```

### Real switch to Inertia

```bash
cd /Users/serhiidymenko/laravel10/cms
scripts/switch-admin-panel.sh inertia
```

## What the script does

1. Ensures `.env` exists (copies from `.env.example` if needed).
2. Updates or appends `ADMIN_PANEL=<value>` in `.env`.
3. Clears config and route caches:
   - `php artisan config:clear`
   - `php artisan route:clear`

Reasoning:
- Cache clear avoids stale panel settings after an env change.

## Expected usage policy

- New projects: start with `filament` for quick delivery.
- Switch to `inertia` when custom editorial UX is required.
- Keep shared actions/contracts/policies unchanged regardless of panel.

## Troubleshooting

- If routes still look wrong, run:

```bash
cd /Users/serhiidymenko/laravel10/cms
php artisan optimize:clear
```

- If the app fails after switching, verify:
  - relevant packages are installed for the selected panel,
  - route files for that panel exist,
  - middleware and auth guards are configured.

