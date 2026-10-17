# Base44 → Laravel Blade migration

## Decision

Keep Laravel 12 as the authoritative application and convert the uploaded Base44 React UI/UX into Blade + Tailwind CSS + Alpine.js. The Base44 export is the visual source of truth, but the finished application has no Base44 or React runtime dependency.

## Source snapshot

Uploaded Base44 export:
- 156 files
- SHA-256: `bc2f77248754f94cc69d14bc2d8ad59bd3faf3550aac9caf47481750187cfcd4`
- Source UI: React/Vite/Tailwind
- Target UI: Blade/Tailwind/Alpine

## Branch safety

- `archive/pre-base44-2026-10-05` — exact Laravel state before migration.
- `import/base44-2026-10-05` — Base44 source reference.
- `feature/base44-laravel-migration` — Blade conversion branch.

## Architecture

Laravel remains responsible for:
- MySQL schema and domain models
- web authentication and Sanctum mobile authentication
- authorization and permissions
- products, categories, variants and inventory
- carts and wishlists
- checkout and payments
- orders and fulfilment
- measurements and tailoring
- admin and tailor workflows
- uploads, mail, notifications and queues
- localization, SEO and mobile API contracts

Blade/Tailwind/Alpine are responsible for:
- storefront presentation
- account UI
- cart and checkout presentation
- admin UI
- tailor UI
- menus, drawers, galleries, filters, modals and other lightweight interactions

## Conversion rules

1. Preserve the Base44 visual language, spacing, cards, gradients, responsive behavior and navigation.
2. Convert React components into reusable Blade components.
3. Convert React state and simple interactions to Alpine.js.
4. Prefer server-rendered Laravel data over client-side API fetching.
5. Use small fetch/AJAX endpoints only where interaction benefits from it.
6. Keep the current relational Laravel domain instead of reproducing Base44 entities.
7. Keep EN/FA/PS localization and RTL behavior.
8. Keep existing SEO, payment, inventory and mobile contracts unless a verified improvement is required.
9. No `@base44/sdk`, React, React Router or React Query in the final application.
10. Merge only after automated tests, Vite build and MySQL migration/seed checks are green.

## Base44 replacement map

| Base44/React concept | Laravel target |
|---|---|
| React page component | Blade page |
| React shared component | Blade component |
| React state/hooks | Alpine.js or server state |
| React Router | Laravel named routes |
| React Query | Controller/view data or targeted fetch |
| base44.auth | Laravel web auth / Sanctum |
| base44.entities.* | Existing Laravel models/services |
| UploadFile | Laravel Storage |
| SendEmail | Laravel Mail/Notifications |
| base44.functions.invoke | Controllers/services/actions |
| Base44 workflows | Events/listeners/queued jobs |

## Conversion sequence

1. Global shell: announcement, header, mobile navigation, bottom tab bar, footer.
2. Home.
3. Shop, filters and product cards.
4. Product detail and size/measurement dialogs.
5. Cart and wishlist.
6. Authentication and customer account.
7. Checkout and payments.
8. Orders.
9. Measurements and measurement guide.
10. Tailor dashboard.
11. Admin dashboard and management screens.
12. Policies/about/contact/FAQ.
13. Responsive/RTL/accessibility parity audit.
14. Full automated regression and CI.

## Staging migration and final domain cutover gate (October 2026)

**Do not change the live `kabulfit.com` DNS, Apache vhost, or Base44 deployment until all staging gates below pass and the final cutover is explicitly approved.** The Laravel staging host is `https://kabulfit.businessos.af`.

Original Base44 read-only export: 141 products (130 active, 11 inactive), 8 original categories, 739 original product photo references. The guarded Laravel importer archives 730 available images; the other 9 source links were already broken and belonged to inactive products. Keep the export JSON private and preserve the exact original USD prices, localized names, stock, source IDs and media mapping. The five historical non-Base44 sample products remain hidden rather than being advertised as part of the original catalog.

### Original stock and size restoration gate

The verified Base44 export contains 130 active USD-priced products with positive
aggregate stock and the original sizes S/M/L/XL (plus XXL on 25 products).
After deploying the additive `stock_reserved`, `reservation_state`,
`size_options`, and `size_code` migrations to cPanel staging:

1. Back up the existing staging database and imported photos.
2. Run `php artisan kabulfit:sync-base44-sizes <private-local-snapshot.json>`
   without `--commit` to validate all 141 source records.
3. Apply `--commit` only after the dry run succeeds; this changes size choices
   but never source prices, aggregate stock quantities, or media.
4. Verify public image URLs resolve to image MIME types.
5. Run
   `python3 scripts/verify_staging_catalog.py --all-galleries --sample 0 --interval 2 --require-original-parity`
   only on staging. This intentionally fails for missing original sizes,
   zero available stock, mismatched product details, or broken photo URLs.

Full gallery verification is rate-limited and may take time. Re-running the
original full importer after live orders is **not** an acceptable substitute
for a sizes-only backfill, as it can overwrite product stock or catalog edits.

### Staging acceptance checklist

1. Run `python3 scripts/verify_staging_catalog.py --base https://kabulfit.businessos.af --sample 0` to confirm all 130 public products, 8 categories, USD-only money objects, unique source-mapped SKUs, and HTTP 200 images.
2. Verify all 730 archived photos are readable in the *actual PHP/web-server request context*, not just present in the shell. Ensure Apache's document root, PHP-FPM/OPcache, symlinks and route cache point to the active Laravel release. Static `source/` imagery and hashed `catalog/` imagery require independent checks.
3. Manually review EN/FA/PS + RTL home, category, search, product detail, variants, stock, size guidance, measurement entry, cart, addresses, shipping, customer account, and mobile catalog image rendering.
4. Run Laravel CI (PHPUnit, Pint, Composer audit, Vite, MySQL migration/seed), and test checkout/payment integration using test-mode accounts before enabling real payments. Never treat demo Flutter data as production catalog.
5. Keep a verified rollback snapshot of the existing cPanel staging database and archived assets; preserve the legacy Base44 frontend until final acceptance.
6. Only as the **last step**, after full staging acceptance, prepare a separate production cutover plan with backups, DNS/SSL verification, redirect and SEO checks, webhook/queue checks, and documented rollback.

The staging image incident is tracked in GitHub issue #29; a passing backend unit test by itself does **not** prove real cPanel static image delivery.
