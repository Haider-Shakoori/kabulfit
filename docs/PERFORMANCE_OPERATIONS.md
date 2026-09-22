# Performance, Media and Operations

Batch 11 hardens the Laravel platform before the Flutter implementation begins.

## Responsive product media

Raster product media can have generated 320, 640, 960 and 1280 pixel derivatives. WebP is required when GD supports the source image; AVIF is generated when PHP/GD exposes AVIF encoding.

Generate synchronously during maintenance:

`php artisan media:generate-responsive --sync`

Or enqueue generation for the configured queue worker:

`php artisan media:generate-responsive`

Use `--force` to rebuild existing derivatives.

The original image remains the fallback. Blade storefront images use `<picture>` with AVIF then WebP sources when available. API media objects expose the same responsive srcset strings additively.

Derivative files use `KABULFIT_MEDIA_DERIVATIVE_DISK`. The default public disk works with `storage:link`; S3-compatible storage can be selected for CDN-backed deployments. Original `asset()` URLs honor Laravel's `ASSET_URL`, while storage disks honor their configured URL/AWS URL.

Production PHP needs GD with WebP support; AVIF support is strongly recommended.

## Catalog/query caching

Catalog filter reference data is cached per locale for a short TTL. Public API category and collection reference queries are cached similarly. Inventory and checkout-authoritative state are not placed behind long-lived application caches.

The feature suite places a bounded-query ceiling on the seeded catalog request so relation changes that introduce obvious N+1 behavior fail CI.

## API HTTP caching

All versioned API responses pass through the API cache-header middleware.

For anonymous successful GET/HEAD responses:

`Cache-Control: public, max-age=60, stale-while-revalidate=300`

For authenticated GET/HEAD responses:

`Cache-Control: private, no-cache`

Mutating requests return `Cache-Control: no-store`.

Successful GET responses receive an ETag. Clients may send `If-None-Match` and receive HTTP 304 when the representation is unchanged.

## Mobile payload controls

Growing customer lists use bounded pagination. Product detail supports optional `include=` expansions so Flutter can request only collections, media, variants and/or tailoring data it needs for a screen.

No existing v1 response field is removed when `include` is omitted.

## Frontend budgets

CI enforces raw production-build budgets:

- individual JS asset: 180 KiB maximum;
- individual CSS asset: 140 KiB maximum;
- combined JS + CSS: 360 KiB maximum.

These are guardrails, not a Lighthouse substitute. Cross-platform Lighthouse/Core Web Vitals evidence remains part of the later QA/release gates.

## Queue and scheduler

Production must use an asynchronous queue connection and a durable/shared cache store.

Run a queue worker under Supervisor, systemd, a container orchestrator, or the platform's managed worker facility. A typical worker command is:

`php artisan queue:work --sleep=1 --tries=3 --timeout=120`

Run Laravel's scheduler every minute from exactly one deployment scheduler:

`* * * * * cd /path/to/kabulfit && php artisan schedule:run >> /dev/null 2>&1`

Scheduled operations include abandoned-checkout expiry, responsive-media generation dispatch and failed-job pruning. Single-server mutexes and overlap protection are enabled.

## Deployment cache/readiness

After environment configuration and migrations:

`php artisan storage:link`

`php artisan optimize`

`php artisan ops:readiness --strict`

The strict readiness check requires debug mode off, HTTPS APP_URL, asynchronous queues, a durable cache store, media-disk configuration, and built configuration/route caches.

CI also proves the application can build configuration and route caches and resolve the production scheduler.
