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
php artisan test
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

No scheduled domain tasks are enabled in Batch 1; this is the production runtime contract for later workflows.

## Production deployment baseline

```bash
composer install --no-dev --prefer-dist --optimize-autoloader
php artisan migrate --force
npm ci && npm run build   # use once lockfile is committed; until then npm install && npm run build
php artisan storage:link
php artisan optimize
```

Serve the application from `public/`, set `APP_ENV=production`, `APP_DEBUG=false`, HTTPS-only cookies, a production cache/queue backend, and real mail/storage credentials through environment variables. Never commit `.env` or production secrets.

## SEO architecture

Indexable URLs are localized and slug-based (`/{locale}/products/{slug}`, `/{locale}/categories/{slug}`). Canonical, hreflang, Open Graph, Twitter and JSON-LD are rendered server-side. `/robots.txt` blocks private/transactional areas while `/sitemap.xml` includes public foundation URLs. Legacy `/ProductDetail?id=...` mappings use explicit 301 records and unknown IDs return 404.

## Translation workflow

UI strings live in `lang/en`, `lang/fa` and `lang/ps`. Product/category localized content is relational database data. Do not hard-code translated interface strings in Blade when a translation key is appropriate.

See `PROJECT_STATUS.md` and `ROADMAP.md` for implementation status and batch sequencing.
