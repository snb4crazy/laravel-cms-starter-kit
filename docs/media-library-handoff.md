# Media Library Handoff Notes

This is the short operational companion to `docs/media-library-implementation.md`.

Use this file when you need the quick version for handoff, onboarding, or backlog grooming.

---

## Current status

**Status:** safe MVP foundation in place.

What exists today:

- Spatie Media Library is installed and wired into the app.
- Filament’s Spatie media plugin is installed.
- `Post` supports media attachments.
- `cover` works now as the main image collection.
- `gallery` support exists in the model and is now available as an **opt-in admin feature**, default **off**.
- A published `config/media-library.php` now makes project defaults explicit.
- Media upload behavior is covered by automated tests at the model level.

What is still intentionally incomplete:

- no centralized media browser/resource
- no metadata workflow (`alt`, `caption`, `credit`, etc.)
- no finalized production disk/CDN strategy
- no private media policy

---

## Current behavior you can rely on

### Model support

`app/Models/Post.php`:

- implements `HasMedia`
- uses `InteractsWithMedia`
- defines collections:
  - `cover` → single file
  - `gallery` → multiple files
- defines conversions:
  - `thumb`
  - `banner`

### Admin support

`app/Filament/Resources/PostResource.php`:

- `cover` upload is enabled by default
- `cover` image displays in the posts table
- `gallery` field is hidden unless explicitly enabled

### Disk behavior

`config/media-library.php`:

- default media disk is `public`
- can be changed with `MEDIA_DISK`

This keeps the boilerplate simple while remaining customizable.

---

## Feature flags

### Enable post gallery UI

Default:

```dotenv
CMS_POST_GALLERY_ENABLED=false
```

If a project wants gallery uploads in the post form:

```dotenv
CMS_POST_GALLERY_ENABLED=true
```

This is intentionally off by default so the starter does not force a gallery workflow.

---

## Safe defaults added for the boilerplate

The following decisions were made to keep the template conservative:

1. `cover` remains the only enabled media field by default.
2. `gallery` is opt-in, not automatic.
3. published media config mirrors the current runtime instead of changing it.
4. tests validate media attachment/storage behavior without forcing a heavy UI workflow.

---

## Environment variables to know

These are the most relevant media-related env values now:

```dotenv
MEDIA_DISK=public
# MEDIA_QUEUE=
# QUEUE_CONVERSIONS_BY_DEFAULT=true
# QUEUE_CONVERSIONS_AFTER_DB_COMMIT=true
# MEDIA_VERSION_URLS=false
# MEDIA_PREFIX=
# IMAGE_DRIVER=gd
# MEDIA_TEMPORARY_URL_DEFAULT_LIFETIME=5
CMS_POST_GALLERY_ENABLED=false
```

---

## Tests that now protect the feature

Media-specific automated coverage now checks:

- cover uploads attach to posts
- uploaded media is stored on the configured disk
- the `cover` collection enforces single-file replacement
- the `gallery` collection supports multiple items at the model level
- the post create form hides gallery by default
- the gallery field appears only when the feature flag is enabled
- the cover field reads its disk from config instead of being hardcoded

---

## Recommended next steps

If a project needs more media capability, do this in order:

1. decide whether uploads stay on `public` or move to `s3`
2. add metadata fields (`alt`, `caption`, `credit`)
3. expose `gallery` only if the project needs it
4. add a dedicated media manager if editorial reuse matters
5. define public vs private media rules

---

## Files to review first

- `config/media-library.php`
- `config/cms.php`
- `app/Models/Post.php`
- `app/Filament/Resources/PostResource.php`
- `tests/Unit/PostModelTest.php`
- `tests/Feature/PostResourceTest.php`
- `docs/media-library-implementation.md`

