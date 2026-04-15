# CI/CD Guide

This document describes the GitHub Actions setup for this Laravel 12 + Filament CMS template.

The goal is broad coverage with conservative defaults:

- strong PR checks
- security and supply-chain checks
- automated staging deploys
- approval-gated production deploys

## Workflows added

### 1) CI: `.github/workflows/ci.yml`

Triggers:

- pull requests to `main`
- pushes to `main`
- manual dispatch

What it runs:

- PHP matrix (`8.2`, `8.3`)
- `composer validate --strict`
- dependency install
- `.env` bootstrap and SQLite migration
- `php artisan pint --test`
- `php artisan test`
- frontend build (`npm ci`, `npm run build`)

Purpose:

- catch PHP regressions, formatting issues, migration/test failures, and frontend build breaks before merge.

### 2) Security: `.github/workflows/security.yml`

Triggers:

- pull requests to `main`
- pushes to `main`
- nightly schedule
- manual dispatch

What it runs:

- dependency review on PRs
- `composer audit --locked`
- `npm audit --omit=dev --audit-level=high`
- secret scan with gitleaks
- CodeQL analysis (PHP + JavaScript)

Purpose:

- identify vulnerable dependencies, exposed secrets, and common static-analysis security risks.

### 3) Staging Deploy: `.github/workflows/deploy-staging.yml`

Triggers:

- push to `main`
- manual dispatch

What it does:

- SSH into the staging host
- update code to latest `main`
- run `composer install` and `npm build`
- run `php artisan migrate --force`
- refresh optimization caches
- restart queue workers
- optional health check URL probe

Purpose:

- keep staging aligned with `main` and validate deployability continuously.

### 4) Production Deploy: `.github/workflows/deploy-production.yml`

Triggers:

- manual dispatch with `ref` input (tag recommended)

What it does:

- SSH into production host
- checkout requested ref
- optional backup command (if configured)
- run install/build/migrate/optimize/restart sequence
- optional health check URL probe

Safety controls:

- intended to run behind GitHub Environment approvals (`production` environment)
- manual trigger only

## Required GitHub Secrets

Set these in repository or environment secrets.

### Staging

- `STAGING_HOST`
- `STAGING_USER`
- `STAGING_SSH_KEY`
- `STAGING_PATH`

Optional:

- `STAGING_HEALTHCHECK_URL`

### Production

- `PRODUCTION_HOST`
- `PRODUCTION_USER`
- `PRODUCTION_SSH_KEY`
- `PRODUCTION_PATH`

Optional:

- `PRODUCTION_HEALTHCHECK_URL`
- `PRODUCTION_BACKUP_COMMAND`

## Recommended GitHub Environment configuration

Create two environments:

- `staging`
- `production`

Recommended settings:

- require reviewers for `production`
- restrict `production` deployment to trusted branches/tags
- keep environment-specific secrets at the environment level

## Server prerequisites

The remote server used by deploy workflows should have:

- PHP 8.2+ with needed extensions
- Composer v2
- Node.js + npm (for current server-side build approach)
- writable Laravel directories (`storage`, `bootstrap/cache`)
- queue worker process manager (supervisor/systemd) if queues are used

## Notes about current deploy strategy

This template uses **SSH-based in-place deploys** for simplicity.

That is intentionally easy to customize, but teams can later switch to:

- artifact-based deploys
- Forge/Vapor pipelines
- container/image-based deployment

## Customization ideas

As projects mature, consider:

1. Build artifact once in CI and deploy the same artifact to staging/production.
2. Add smoke tests for `/admin/login` and a dedicated health endpoint.
3. Add rollback scripts and automated rollback on failed health checks.
4. Add migration safety checks for destructive changes.
5. Add notifications (Slack/Teams/Telegram) for deploy status.

## Future extension ideas

These are intentionally optional. They are good next steps once the base CI/CD setup is stable and the project’s hosting/storage strategy becomes clearer.

### CI coverage upgrades

- Add **Larastan / PHPStan** and fail the pipeline on new static analysis issues.
- Add a **database matrix** if you want parity beyond SQLite in CI, for example:
  - SQLite for fast PR feedback
  - MySQL or MariaDB for production-like verification
- Add **browser-level admin smoke tests** later with Laravel Dusk or another E2E tool if Filament workflows become more complex.
- Add **migration safety checks** that flag destructive schema changes for manual review.
- Add **coverage reporting** if the team wants test coverage thresholds.

### Media and disk-related test ideas

This project already has media support and disk-aware behavior, so CI can grow into storage-focused validation later.

Optional future checks:

- Run media tests against multiple disk strategies once a policy is chosen:
  - `public`
  - `s3`
  - split-disk strategies for originals vs conversions
- Add a dedicated test job that overrides `MEDIA_DISK` and verifies uploads still attach correctly.
- Add assertions that `storage:link` assumptions remain valid for local/public deployments.
- Add tests for media conversions and ensure expected conversions are generated for uploaded files.
- Add queue-related tests if conversion generation is moved off sync execution.
- Add future checks for signed/temporary URLs if private media is introduced.

### Optional deployment tests for disks/storage

If this app later adopts S3 or more advanced media storage, consider adding deploy-time smoke checks such as:

- verify the configured disk is reachable before deployment finishes
- verify expected bucket/path/prefix settings exist
- verify one known asset URL resolves after deploy
- verify queue workers can process conversion jobs in the target environment

These should stay optional until the project has a formal storage policy.

### Deployment hardening ideas

- Promote a **single tested artifact** from CI to staging and production instead of rebuilding on each server.
- Add **pre-deploy backup hooks** for database and critical uploads.
- Add **post-deploy rollback hooks** when health checks fail.
- Add **release tagging** so every production deploy maps to a clear Git ref.
- Add **maintenance-mode strategies** for migrations that are not backward-compatible.
- Add separate workflows for:
  - application deploys
  - database migrations
  - one-off maintenance operations

### Operational visibility ideas

- Post workflow results to Slack, Teams, or Telegram.
- Add deploy annotations with commit SHA, branch/tag, and actor.
- Add a health endpoint specifically for CI/CD smoke checks.
- Add scheduled jobs for:
  - nightly full test runs
  - weekly security scans
  - backup restore drills in non-production
- Generate an SBOM if the project needs stronger supply-chain visibility.

### Recommended growth path

If you want to extend this setup gradually, a practical order is:

1. add Larastan
2. add MySQL/MariaDB CI coverage alongside SQLite
3. add media/disk-specific CI scenarios once storage policy is finalized
4. move deploys to artifact promotion
5. add rollback + health-gated deploy automation

### Related docs

- `docs/media-library-implementation.md`
- `docs/media-library-handoff.md`

## Local command parity

These commands mirror what CI enforces:

```bash
composer validate --strict
composer install --no-interaction --prefer-dist --no-progress
cp .env.example .env
mkdir -p database

touch database/database.sqlite
php artisan key:generate
php artisan migrate --force
php artisan pint --test
php artisan test
npm ci
npm run build
```

Use them before opening large PRs to reduce CI churn.

