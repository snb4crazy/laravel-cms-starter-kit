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

