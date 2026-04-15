# What to Build Yourself in a Reusable CMS Template

Use packages for generic plumbing. Build your own code for product-specific behavior.

## 1) Content domain model

Build custom models and rules for:
- Pages, posts, categories, tags, authors
- Flexible content blocks (hero, rich text, gallery, CTA, FAQ)
- Navigation and menu structure
- Internal linking rules

Why custom: this reflects your product and business language.

## 2) Editorial workflow

Implement your own states and policies:
- Draft -> Review -> Approved -> Scheduled -> Published
- Role-specific transitions and approvals
- Scheduled publishing/unpublishing jobs
- Revision history + restore flow

Why custom: editorial processes vary a lot across teams.

## 3) Multi-site / tenant conventions

Even with a package, keep your own conventions:
- Site/tenant model and resolution strategy
- Theme selection per site
- Shared vs tenant-specific content rules
- Data isolation boundaries and policy checks

Why custom: tenancy architecture is a core product decision.

## 4) SEO rules and content governance

Use package primitives, but own your logic:
- Canonical URL policy
- Auto title/description fallback strategy
- Robots and noindex rules
- Redirect strategy for slug/path changes

Why custom: SEO strategy is business-specific and evolves often.

## 5) Public rendering layer

Build your own front-end delivery strategy:
- Blade-only, Inertia SPA, or headless API approach
- Component library for shared content blocks
- Cache strategy per page type
- Preview URLs and secure preview mode

Why custom: rendering and caching trade-offs are project-specific.

## 6) Internal architecture for reusability

Create a modular app structure:
- `app/Domain/*` for business logic
- `app/Application/*` for use cases/actions
- `app/Infrastructure/*` for external integrations
- `app/Support/*` for shared helpers

Add your own conventions for:
- Base CRUD actions
- Form request patterns
- Policy/permission naming
- Event names and listener contracts

## 7) Boilerplate features worth owning

- Base admin dashboard widgets (content stats, publishing queue)
- Shared table/form components for your CMS entities
- Seeders for demo content and default roles
- Opinionated test factories and feature test helpers
- Upgrade checklist for moving between Laravel major versions

## MVP vs later (build-yourself roadmap)

### MVP (first weeks)
- Content entities and relationships
- Slugs + route binding
- Basic workflow (Draft/Published)
- Roles and permission mapping to policies
- Media attachment conventions

### Later (after first real project)
- Full approval workflow and revisions UI
- Multi-site support
- Advanced SEO automation
- Structured block editor UX improvements
- Import/export tools and migration scripts

## Keep this template maintainable

- Document decisions in ADR-style notes under `docs/decisions/`.
- Favor composition over inheritance in reusable modules.
- Keep third-party package usage thin and replaceable.
- Write feature tests for each reusable module before extracting it to template defaults.

