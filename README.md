# KabulFit

Production rebuild of **KabulFit**, a multilingual Afghan fashion e-commerce and made-to-measure platform.

## Stack

- Laravel 12 / PHP 8.3+
- MySQL 8+
- Blade + Tailwind CSS 4
- Alpine.js
- Vite
- Database/Redis-ready cache and queues
- English (`en`), Dari (`fa`) and Pashto (`ps`), including RTL for Dari/Pashto

## Local setup

```bash
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
npm install
npm run build
php artisan serve
```

For active frontend development use `npm run dev` in a second terminal. Configure MySQL credentials in `.env`; automated tests use SQLite in memory while CI also performs a MySQL 8.4 migration/seed smoke test.

## Tests and quality

```bash
vendor/bin/phpunit --display-warnings --fail-on-warning
vendor/bin/pint --test
composer audit
npm run build
```

## Queues and scheduler

Production queue worker example:

```bash
php artisan queue:work --sleep=1 --tries=3 --timeout=90
```

Scheduler cron entry:

```cron
* * * * * cd /path/to/kabulfit && php artisan schedule:run >> /dev/null 2>&1
```

No scheduled domain tasks are enabled yet; this is the production runtime contract for later workflows.

## Production deployment baseline

```bash
composer install --no-dev --prefer-dist --optimize-autoloader
php artisan migrate --force
npm ci && npm run build
php artisan storage:link
php artisan optimize
```

A production lockfile gate remains on the roadmap; until lockfiles are committed, local frontend setup uses `npm install`. Serve the application from `public/`, set `APP_ENV=production`, `APP_DEBUG=false`, HTTPS-only cookies, a production cache/queue backend, and real mail/storage credentials through environment variables. Never commit `.env` or production secrets.

## SEO architecture

Indexable URLs are localized and slug-based (`/{locale}/products/{slug}`, `/{locale}/categories/{slug}`). Canonical, hreflang, Open Graph, Twitter and JSON-LD are rendered server-side. `/robots.txt` blocks private/transactional areas while `/sitemap.xml` includes public foundation URLs. Legacy `/ProductDetail?id=...` mappings use explicit 301 records and unknown IDs return 404.

## Translation workflow

UI strings live in `lang/en`, `lang/fa` and `lang/ps`. Product/category localized content is relational database data. Do not hard-code translated interface strings in Blade when a translation key is appropriate.

## Visual reference and media

The live KabulFit site is the visual/content source of truth. The current reference audit, implementation notes, responsive/accessibility decisions and media constraints are documented in `docs/VISUAL_REFERENCE.md`.

See `PROJECT_STATUS.md` and `ROADMAP.md` for implementation status and batch sequencing.


## Catalog API

The first mobile-facing API contract is documented in `docs/API_CONTRACT.md`. Public catalog routes are versioned under `/api/v1/{locale}` and expose localized categories, collections, products, variants, media, stock and integer-minor-unit money without exposing database primary keys.


## Customer authentication

Web customer authentication is localized under `/{locale}` and includes registration, login/logout, password reset, email verification, account pages and saved addresses. Mobile clients use Laravel Sanctum bearer tokens under `/api/v1/{locale}/auth/*`; mobile tokens are device-bound, revocable and expire according to `KABULFIT_MOBILE_TOKEN_DAYS`.

See `docs/AUTHENTICATION.md` and `docs/API_CONTRACT.md` for the security and client contract.

## Stripe checkout

Set `STRIPE_KEY`, `STRIPE_SECRET` and `STRIPE_WEBHOOK_SECRET`. Configure Stripe to deliver PaymentIntent success/failure/cancellation events to `/api/stripe/webhook`. Keep the Laravel scheduler running so abandoned checkout reservations are released. See `docs/PAYMENTS.md`.

## Measurements and custom tailoring

Reusable measurement profiles support centimetres/inches while Laravel stores canonical centimetre values. Tailored product configurations are attached to cart lines and snapshotted into order items at checkout. See `docs/MEASUREMENTS.md` and `docs/API_CONTRACT.md`.
