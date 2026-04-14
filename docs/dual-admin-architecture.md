# Dual Admin Architecture (Filament-First, Inertia-Ready)

This document defines how the CMS boilerplate supports both admin approaches:
- `filament` as the default for fast delivery
- `inertia` as a first-class switch when a project needs custom UX

## Decision summary

- Decision: default to `ADMIN_PANEL=filament`.
- Why: it gives a complete back office quickly with less code.
- Trade-off: Filament can be less flexible for highly custom editorial flows.
- Mitigation: keep core CMS logic UI-agnostic and expose it to both adapters.

## Step-by-step architecture plan (with reasoning)

### Step 1: Keep domain and application logic independent of admin UI

Create and maintain these layers:

```text
app/
  Domain/
    Content/
    Media/
    Taxonomy/
    Workflow/
    Seo/
    Settings/
  Application/
    Content/
      Actions/
      DTOs/
    Workflow/
      Actions/
  Infrastructure/
    Persistence/
    Search/
    Media/
  Admin/
    Filament/
    Inertia/
```

Reasoning:
- Your reusable value is business logic, not panel widgets.
- If logic sits in one layer, you can switch UI without rewriting workflows.

### Step 2: Create shared contracts first

Start with interfaces and actions both panels call.

#### Contracts to create first
- `App\Domain\Content\Contracts\ContentRepository`
- `App\Domain\Workflow\Contracts\WorkflowEngine`
- `App\Domain\Media\Contracts\MediaManager`

#### Actions to create first
- `App\Application\Content\Actions\CreateContentAction`
- `App\Application\Content\Actions\UpdateContentAction`
- `App\Application\Content\Actions\PublishContentAction`
- `App\Application\Content\Actions\ArchiveContentAction`
- `App\Application\Media\Actions\AttachMediaAction`

Reasoning:
- Repositories and actions establish a stable API for both adapters.
- This keeps resource controllers and panel pages thin.

### Step 3: Centralize validation and authorization

Use shared validation and policies:
- `app/Http/Requests/*` (or DTO validators)
- `app/Policies/*`
- `spatie/laravel-permission` role/permission map

Reasoning:
- Rules in one place prevent drift between Filament and Inertia behavior.

### Step 4: Add config-driven panel selection

Create `config/admin.php` (already added in this template) and use:
- `ADMIN_PANEL=filament|inertia`
- per-panel flags and path settings

`config/admin.php` format:

```php
<?php

return [
    'default_panel' => env('ADMIN_PANEL', 'filament'),

    'panels' => [
        'filament' => [
            'enabled' => env('ADMIN_FILAMENT_ENABLED', true),
            'path' => env('ADMIN_FILAMENT_PATH', 'admin'),
        ],
        'inertia' => [
            'enabled' => env('ADMIN_INERTIA_ENABLED', true),
            'path' => env('ADMIN_INERTIA_PATH', 'dashboard'),
        ],
    ],
];
```

Reasoning:
- Environment-based selection keeps one boilerplate usable across projects.
- Per-panel path config avoids route collisions.

### Step 5: Wire routes through an admin selector

Recommended route split:

```text
routes/
  admin/
    filament.php
    inertia.php
```

In `routes/web.php`, load only the chosen panel routes based on `config('admin.default_panel')`.

Reasoning:
- Route loading stays explicit and easy to audit.
- Keeps panel bootstrapping isolated and testable.

### Step 6: Keep UI adapters thin

- Filament resources/pages should call application actions.
- Inertia controllers should call the same actions.
- Avoid direct business rules in Filament page classes or JS components.

Reasoning:
- Thin adapters reduce lock-in and cut migration risk.

## Rollout checklists

## Filament-first rollout (recommended default)

1. Install Filament and base CMS packages (`docs/install-bundles.md`).
2. Implement shared domain + actions before adding many Filament resources.
3. Add Filament resources for core entities (pages/posts/media/categories).
4. Add policy checks and role mappings.
5. Keep Inertia admin routes disabled initially.

Why this order:
- You get usable admin quickly while keeping clean extension points.

## Inertia-first rollout (for custom editorial UX)

1. Keep `ADMIN_PANEL=inertia`.
2. Build Inertia pages/controllers over the same actions/contracts.
3. Add only minimal Filament fallback for operations (optional).
4. Reuse shared policies, requests, and permissions.

Why this order:
- You invest in UX where it matters while retaining a backoffice fallback path.

## Switching policy for projects

Project default should be selected with one env flag:
- `.env`: `ADMIN_PANEL=filament` (default)
- switch to `.env`: `ADMIN_PANEL=inertia`

Use the helper script:
- `scripts/switch-admin-panel.sh filament`
- `scripts/switch-admin-panel.sh inertia`

See `docs/admin-panel-switching.md` for exact behavior and dry-run mode.

## Risks and guardrails

- Risk: duplicated business rules in both admin layers.
  - Guardrail: require all writes to go through `Application` actions.
- Risk: route/middleware divergence.
  - Guardrail: shared middleware groups and policy naming conventions.
- Risk: package lock-in over time.
  - Guardrail: keep package APIs behind your own contracts.

## Minimal milestone plan

- Milestone 1: Shared contracts/actions + Filament CRUD for core content.
- Milestone 2: Workflow states + revisions + media conventions.
- Milestone 3: Inertia adapter for high-value editorial flows.
- Milestone 4: Optional full parity for both admin adapters.

