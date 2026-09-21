# KabulFit Project Status

Last updated: 2026-09-21

## Repository audit

The repository began with only `README.md` on `main`; no application framework, migrations, tests, CI, or existing implementation needed preservation. The rebuild therefore started from a clean Laravel 12 architecture.

## Reference-site audit summary

The current KabulFit website remains the visual/functional reference. Public discovery exposes generic routes including `ProductDetail`, cart, checkout, account, measurements, orders, tailor dashboard and several admin pages. Much of the crawler-visible metadata is generic. The rebuild preserves the KabulFit content/brand direction while replacing weak crawl/index patterns with localized semantic URLs, explicit index control and server-rendered metadata.

Reference homepage structure confirmed: Authentic Afghan Elegance hero; custom sizing/global shipping/quality assurances; category discovery; featured products; Afghan culture/story; measurement CTA; editorial content around Afghan clothing, Gand e Afghani/Perahan Tunban, embroidery and custom tailoring; customer proof; Kabul/Dubai contact positioning.

## Batch 1 — Foundation

Status: **complete and merged to `main` via PR #1**.

Validation baseline: Laravel 12/PHP 8.3+, multilingual SSR/SEO foundation, deterministic seed, security headers and green CI.

## Batch 2 — Reference visual fidelity and responsive component system

Status: **complete and merged to `main` via PR #2**.

Validation baseline: 16 tests / 92 assertions / zero warnings, Pint clean, Composer audit clean, Vite production build green and MySQL 8.4 migration/seed green.

Known limitation retained for final QA: pixel-level browser comparison with the public reference remains required.

## Batch 3 — Catalog domain + mobile catalog API

Status: **implemented on `feat/batch-03-catalog-domain`; CI validation pending**.

Implemented:

- Normalized collections and localized collection metadata/slugs.
- Relational product media with dimensions, primary ordering and localized alt text.
- Generic filterable product options and localized values for size, color and embroidery.
- Product variants with SKU-level pricing and option combinations.
- Variant inventory records with on-hand, reserved and calculated available stock.
- Deterministic categories for Men, Women, Kids and Accessories.
- Deterministic New Arrivals, Wedding Edit and Heritage Essentials collections.
- Search, category, collection, size, color, embroidery, price-range, stock-only and sorting filters.
- Server-rendered shop/category/collection filters using the same query service as the API.
- Clean localized collection URLs.
- Related products and session-based recently viewed products.
- Product media/variant/inventory presentation on product pages.
- Product/Offer/BreadcrumbList/ItemList JSON-LD and collection sitemap entries.
- Faceted/search pages marked `noindex,follow` to prevent index bloat.
- `/api/v1/{locale}/catalog/products`, product detail and catalog facets endpoints.
- Mobile API exposes localized slugs/SKUs, integer minor-unit money, calculated availability, media dimensions/alt text and stable option/value codes without public database IDs.
- Public API crawl blocking in `robots.txt`.
- API contract documented in `docs/API_CONTRACT.md`.
- Batch-specific catalog/API regression tests added.

## Mobile application decision

KabulFit includes a dedicated Flutter mobile application for Android and iOS. Laravel remains the shared server-authoritative commerce backend. Batches 3–8 provide stable versioned API contracts; Stripe is server-first in Batch 5 and surfaced through the official mobile payment flow in Batch 14. Detailed architecture is documented in `docs/MOBILE_ARCHITECTURE.md`.

## Not yet complete

- Authentication/customer account and mobile API authentication.
- Wishlist/cart/checkout and Stripe platform payments.
- Measurement profiles and tailoring workflow.
- Orders/shipping/notifications.
- Admin/tailor dashboards and authorization matrix.
- Blog/content CMS.
- Flutter application implementation.
- Real legacy-ID migration inventory.
- Production catalog photography and responsive AVIF/WebP derivatives.
- Browser/mobile visual-regression evidence.
- Lighthouse/Core Web Vitals and mobile performance evidence.
- Final lockfile/release reproducibility gates.

## Release gate

Batch 3 must not merge until feature-branch CI and PR-triggered CI are green.
