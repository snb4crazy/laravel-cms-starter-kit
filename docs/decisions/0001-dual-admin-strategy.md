# ADR 0001: Dual Admin Strategy (Filament Default, Inertia Optional)

## Status
Accepted

## Date
2026-04-13

## Context
The CMS template must support projects that prefer either:
- Filament for rapid admin delivery
- Inertia for custom editorial UX

A single reusable boilerplate should avoid locking business logic to one UI stack.

## Decision
1. Use `ADMIN_PANEL` env flag as the default panel selector.
2. Set baseline default to `ADMIN_PANEL=filament`.
3. Keep business logic in shared domain/application layers.
4. Treat Filament and Inertia as thin adapters over shared actions/contracts.
5. Provide `scripts/switch-admin-panel.sh` to switch panel quickly and clear caches.

## Consequences

### Positive
- Faster first delivery with Filament.
- Inertia path remains available without architecture rewrite.
- Lower long-term migration risk between admin stacks.

### Negative
- More initial architectural discipline required.
- Need guardrails to prevent logic drift between adapters.

## Guardrails
- All write operations should use `Application` actions.
- Shared authorization in policies and permission map.
- Shared validation in form requests/DTO validators.

## Implementation notes
- Config contract: `config/admin.php`
- Env default: `.env.example` includes `ADMIN_PANEL=filament`
- Switch helper: `scripts/switch-admin-panel.sh`
- Route scaffolding: `routes/admin/filament.php`, `routes/admin/inertia.php`
- Dynamic loader: `routes/web.php` includes only selected panel routes

## Related docs
- `docs/dual-admin-architecture.md`
- `docs/admin-panel-switching.md`
- `docs/install-bundles.md`

