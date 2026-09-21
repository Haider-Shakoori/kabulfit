# KabulFit Project Status

Last updated: 2026-09-21

## Repository audit

The repository began with only `README.md` on `main`; no application framework, migrations, tests, CI, or existing implementation needed preservation. The rebuild therefore started from a clean Laravel 12 architecture.

## Reference-site audit summary

The current KabulFit website remains the visual/functional reference. Public discovery exposes generic routes including `ProductDetail`, cart, checkout, account, measurements, orders, tailor dashboard and several admin pages. Much of the crawler-visible metadata is generic. The rebuild preserves the KabulFit content/brand direction while replacing weak crawl/index patterns with localized semantic URLs, explicit index control and server-rendered metadata.

Reference homepage structure confirmed: Authentic Afghan Elegance hero; custom sizing/global shipping/quality assurances; category discovery; featured products; Afghan culture/story; measurement CTA; editorial content around Afghan clothing, Gand e Afghani/Perahan Tunban, embroidery and custom tailoring; customer proof; Kabul/Dubai contact positioning.

## Batch 1 — Foundation

Status: **complete and merged to `main` via PR #1**.

Implemented:

- Laravel 12 / PHP 8.3+ scaffold and MySQL-first environment defaults.
- Blade + Tailwind CSS + Alpine.js + Vite foundation.
- Central KabulFit CSS design tokens using the required `--brand-*` variables.
- Responsive public shell, accessible skip link/focus behavior/mobile navigation/reduced-motion handling.
- `en`, `fa`, `ps` locale prefixes and automatic RTL for Dari/Pashto.
- Relational categories/products plus translation tables and integer minor-unit monetary storage.
- Deterministic Afghan-fashion catalog seed data.
- Localized homepage, shop, category and server-rendered product foundation.
- Canonical, hreflang/x-default, Open Graph, Twitter metadata and JSON-LD foundation.
- Dynamic `robots.txt` and public sitemap.
- Explicit legacy-product redirect table and 301 controller; unknown mappings return 404.
- Baseline security headers and custom 404 response.
- Automated localization, SEO, redirect and security-header tests.
- GitHub Actions quality/frontend/security checks plus MySQL 8.4 migration/seed smoke test.

Validation evidence:

- PHPUnit: **12 tests, 52 assertions, zero warnings** using `--fail-on-warning`.
- Laravel Pint: **48 files passed**.
- Composer dependency audit: **no security vulnerability advisories found**.
- Vite production build: **successful**.
- MySQL 8.4: **fresh migration + deterministic seeding successful**.
- PR-triggered CI: **green before merge**.

## Batch 2 — Reference visual fidelity and responsive component system

Status: **complete and merged to `main` via PR #2**.

Implemented:

- Live homepage content/section inventory documented in `docs/VISUAL_REFERENCE.md`.
- Refined reusable KabulFit design system using centralized green, burgundy, gold and warm-neutral tokens.
- Branded SVG mark plus original lightweight textile/craftsmanship SVG assets.
- Responsive sticky header, locale switcher and keyboard-friendly mobile navigation.
- Live-site homepage hierarchy preserved: hero, three assurances, categories, featured catalog, culture story, measurement CTA, editorial content, testimonials and contact positioning.
- Live reference copy restored for shipping, story, measurement and customer-proof sections.
- Customer-facing developer placeholder language removed from the homepage.
- Reusable icon, brand and product-card components.
- Fluid layout rules for desktop, tablet and required small mobile widths, plus logical RTL properties.
- Accessibility refinements: skip link, landmarks, focus states, minimum targets, semantic figures/quotes, explicit image dimensions and reduced-motion behavior.
- Media strategy documented without inventing stock photography or fake production assets.
- Automated visual-structure/token/accessibility markup tests added.

Validation evidence:

- PHPUnit: **16 tests, 92 assertions, zero warnings**.
- Laravel Pint: **49 files passed**.
- Composer dependency audit: **no security vulnerability advisories found**.
- Vite production build: **successful**.
- MySQL 8.4: **fresh migration + deterministic seeding successful**.
- PR-triggered quality + MySQL CI: **green before merge**.

Known Batch 2 limitation:

The available crawler exposes content and page inventory but not the original CSS bundle, reliable raw image asset URLs or browser pixel screenshots. The design tokens are therefore centralized for efficient correction, but **pixel-perfect parity is not yet claimed**. Browser visual-regression comparison remains required before final acceptance.

## Mobile application decision

KabulFit now explicitly includes a dedicated Flutter mobile application for Android and iOS. The roadmap has been expanded from 13 to **16 batches**.

Key architectural decisions:

- Laravel remains the shared server-authoritative commerce backend for web and mobile.
- Batches 3–8 must provide stable versioned APIs for the mobile capabilities they own.
- Stripe is implemented server-first in Batch 5 and surfaced through the official mobile payment flow in Batch 14; webhook-confirmed server state remains authoritative.
- Mobile-specific implementation begins with Batch 12 after shared domain/API hardening.
- The planned mobile repository is `kabulfit-mobile`.
- Mobile supports English, Dari and Pashto with RTL, the same KabulFit visual identity, customer account, shopping, wishlist/cart, reusable measurements, custom tailoring, Stripe checkout, orders/tracking and notifications.
- Detailed architecture is documented in `docs/MOBILE_ARCHITECTURE.md`.

## Batch 3 — Catalog domain + mobile catalog API

Status: **implementation validated green on `feat/batch-03-catalog-domain-v2`; final PR gate pending**.

Implemented:

- Relational localized collections and collection-product ordering.
- Sizes, localized colors, product variants and variant-level inventory with reserved-stock accounting.
- Product media records with localized alt text and intrinsic dimensions.
- Deterministic demo media and expanded Afghan-fashion seed catalog including Accessories.
- Server-rendered search, category, collection, size, color, price, stock and sort filters.
- Clean localized collection URLs.
- Related products and session-based recently viewed products.
- Product pages with media gallery, collection tags, variant/stock options and related/recent sections.
- Product/Offer/BreadcrumbList and listing ItemList/BreadcrumbList structured-data expansion.
- Versioned `/api/v1/{locale}` catalog, product, category and collection endpoints for the future Flutter app.
- Public API identifiers use slugs/SKUs instead of database primary keys.
- API money uses integer minor units plus ISO currency.
- Public catalog API rate limiting and contract documentation.
- Automated catalog domain, filter, API, recently-viewed and SEO tests.

Validation evidence from GitHub Actions run `35633550158`:

- PHPUnit: **30 tests, 168 assertions, zero warnings**.
- Laravel Pint: **76 files passed**.
- Composer dependency audit: **no security vulnerability advisories found**.
- Vite production build: **successful**.
- MySQL 8.4: **fresh migration + deterministic seeding successful**.
- PHP syntax checks and Composer validation: **passed**.

## Not yet complete

- Authentication/customer account and mobile API authentication.
- Cart, wishlist, checkout and Stripe platform implementation.
- Measurement profiles and tailoring workflow.
- Orders/shipping/notifications.
- Admin/tailor dashboards and authorization matrix.
- Blog/content CMS.
- Flutter application implementation.
- Real legacy-ID mapping crawl.
- Browser/mobile visual-regression evidence.
- Lighthouse/Core Web Vitals and mobile performance evidence.
- Composer/npm/mobile lockfile and release reproducibility gates.

## Release gate

Batch 3 implementation CI is green. The final documentation commit and PR-triggered CI must also remain green before merge.
