# CMS Boilerplate Notes

This folder contains planning notes for a reusable Laravel CMS starter.

## Documents

- [`reusable-libraries.md`](./reusable-libraries.md): third-party packages you can install quickly.
- [`build-it-yourself.md`](./build-it-yourself.md): features better kept as your own code.
- [`install-bundles.md`](./install-bundles.md): copy/paste install commands grouped by phase.
- [`database-setup.md`](./database-setup.md): MySQL-first setup, `.env` mapping, migrations, seeding, and troubleshooting.
- [`dual-admin-architecture.md`](./dual-admin-architecture.md): Filament-first architecture with Inertia-ready adapter boundaries.
- [`admin-panel-switching.md`](./admin-panel-switching.md): env-flag strategy and switch script usage.
- [`decisions/0001-dual-admin-strategy.md`](./decisions/0001-dual-admin-strategy.md): architecture decision record for the dual-admin policy.
- [`filament-user-manual.md`](./filament-user-manual.md): step-by-step guide to run, login, and experiment with the Filament CMS stub.

## Suggested rollout

1. Start with a thin MVP: auth, admin panel, roles/permissions, media library, activity logs.
2. Add content-modeling foundations: slugs, revisions, publishing workflow, SEO fields.
3. Add integrations later: search engine, backups, observability, API docs, multi-tenancy.

## Principles for a reusable template

- Keep domain rules in your own app code, not package internals.
- Prefer packages with active maintenance and clear upgrade paths.
- Wrap package usage in service classes so future swaps are easier.
- Ship sensible defaults, but make everything configurable via config files.


