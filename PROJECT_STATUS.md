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

Status: **complete and merged to `main` via PR #5**.

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
- PR-triggered CI run `35645610102`: **quality + MySQL smoke both green**.
- PR-triggered CI run `35639706516`: **quality + MySQL smoke both green before merge**.
- PR-triggered CI run `35636598809`: **quality + MySQL smoke both green**.
- PR-triggered CI run `35633833786`: **quality + MySQL smoke both green**.

## Batch 4 — Authentication, customer account + mobile API authentication

Status: **complete and merged to `main` via PR #6**.

Implemented:

- Laravel Sanctum 4.x mobile bearer-token authentication.
- Localized web registration, login/logout, password reset and signed email verification.
- Active-account enforcement and login/API throttling.
- Preferred account locale used by verification and password-reset links.
- Customer account page with noindex/nofollow metadata.
- Relational saved addresses with public UUIDs, ownership checks and exactly-one-default behavior.
- Device-aware mobile sessions with device UUID/name/platform/app-version tracking and per-device revocation.
- Mobile token expiration and same-device token replacement.
- Password reset revokes existing mobile tokens.
- Versioned account/auth/address API endpoints for the future Flutter application.
- Localized English, Dari and Pashto account/auth UI with RTL-compatible responsive forms.
- Automated browser auth, verification, reset, address authorization and mobile token/API tests.
- Authentication/API contract documentation.

Validation evidence from GitHub Actions run `35636248691`:

- PHPUnit: **44 tests, 248 assertions, zero warnings**.
- Laravel Pint: **113 files passed**.
- Composer dependency audit: **no security vulnerability advisories found**.
- Vite production build: **successful**.
- MySQL 8.4: **fresh migration + deterministic seeding successful**.
- PHP syntax checks and Composer validation: **passed**.
- PR-triggered CI: **green before merge**.
- Post-merge `main` CI run `35636887850`: **quality + MySQL smoke both green**.

## Batch 5 — Cart, wishlist, checkout + Stripe platform payments

Status: **complete and merged to `main` via PR #8**.

Implemented:

- Durable authenticated carts and cart items shared by web/mobile business rules.
- Customer wishlists for web and versioned mobile API.
- Integer-minor-unit server-authoritative subtotal, discount, shipping and total calculations.
- Deterministic shipping methods and coupon seed data.
- Transactional checkout with immutable order-item snapshots.
- Variant inventory reservation with row locking and bounded checkout reservation lifetime.
- Scheduled abandoned-checkout cancellation and stock release.
- Stripe PHP SDK integration using PaymentIntents and per-order idempotency keys.
- Stripe Payment Element web flow and mobile-ready PaymentIntent client-secret contract.
- Verified Stripe webhook endpoint; invalid signatures return HTTP 400.
- Deduplicated provider event log and idempotent payment success handling.
- Webhook-authoritative paid/failed/cancelled order state.
- Successful payments convert reservations to sold stock exactly once.
- Failed/cancelled payments release reserved inventory.
- Payment gateway abstraction with Stripe cancellation/refund capability for later admin operations.
- Localized English/Dari/Pashto commerce UI and private noindex cart/checkout/payment pages.
- Automated cart, checkout, payment idempotency, webhook rejection and reservation-expiry tests.
- Payment/checkout operational documentation.

Validation evidence from GitHub Actions run `35639422198`:

- PHPUnit: **49 tests, 271 assertions, zero warnings**.
- Laravel Pint: **137 files passed**.
- Composer dependency audit: **no security vulnerability advisories found**.
- Vite production build: **successful**.
- MySQL 8.4: **fresh migration + deterministic seeding successful**.
- PHP syntax checks and Composer validation: **passed**.

## Batch 6 — Measurements & custom tailoring + mobile measurement API

Status: **complete and merged to `main`; core implementation via PR #9 and audited closure via PR #12. Post-merge `main` CI is green.**

Implemented:

- Configurable relational measurement definitions for Perahan Tunban, dresses and waistcoats.
- Localized English/Dari/Pashto definition names, instructions and lightweight visual measurement guides.
- Canonical centimetre storage with centimetre/inch input and display conversion.
- Server-side required/range/unknown-code validation shared by web and mobile workflows.
- Reusable customer measurement profiles with public UUIDs and exactly-one-default behavior.
- Protected profile lifecycle while a profile is attached to an active tailored cart item.
- Product-level tailoring eligibility and garment measurement type.
- Web customer measurement-profile management with noindex metadata.
- Customer-owned tailoring history/detail views on web and versioned mobile APIs.
- Product-detail “Tailor This Outfit” workflow with compatible profile, variant and customer notes.
- Shared tailored cart lines for Blade and Flutter/API flows.
- Tailored cart-line cancellation propagation and quantity protection.
- Immutable order-item snapshots for measurement values, profile name, tailoring notes and tailoring request UUID; direct model update/delete operations are rejected.
- Checkout changes tailoring request state to ordered; failed/expired checkout cancellation propagates back to tailoring.
- Versioned mobile endpoints for measurement definitions, profile CRUD and tailored-cart creation.
- Product API exposes tailoring availability and garment type.
- Cart API exposes tailoring request/profile metadata without internal database IDs.
- Deterministic measurement seed data and production-safe guide assets.
- Dedicated conversion, validation, ownership, lifecycle, API CRUD/history, noindex, localization and snapshot immutability regression tests.

Validation evidence from GitHub Actions run `35645248956`:

- PHPUnit: **60 tests, 329 assertions, zero warnings**.
- Laravel Pint: **159 files passed**.
- Composer dependency audit: **no security vulnerability advisories found**.
- Vite production build: **successful**.
- MySQL 8.4: **fresh migration + deterministic seeding successful**.
- PHP syntax checks and Composer validation: **passed**.

## Batch 7 — Orders, shipping, notifications + mobile event contract

Status: **complete and merged to `main` via PR #14 after green feature-branch and PR CI; post-merge `main` CI is green**.

Implemented:

- Server-authoritative order lifecycle with explicit allowed transitions rather than arbitrary status strings.
- Expanded fulfilment states for processing, ready, shipped, delivered and returned orders.
- Immutable order status history with UUID identifiers, source, notes and timestamps.
- Shipment creation, carrier/service/tracking metadata and validated shipment-state transitions.
- Immutable shipment tracking events with shipped/delivered timestamps.
- Shipment milestones synchronize the customer-facing order lifecycle.
- Queueable order and shipment notifications dispatched after commit.
- English/Dari/Pashto notification copy using each customer's preferred locale.
- Mail plus Laravel database notification channels for customer updates.
- Customer web order history/detail pages with private noindex metadata and RTL-safe localization.
- Versioned mobile order list, detail and dedicated tracking endpoints.
- Mobile order detail includes item snapshots, tailoring snapshots, status history and shipment timelines.
- Ownership enforcement for all web/mobile order and tracking views.
- Resumable mobile customer-event feed intended for future Flutter push integration.
- Public event UUID cursors replace internal database-ID cursors.
- Cross-account event cursors are rejected.
- Stable event types for order creation/status changes and shipment creation/status changes.
- Checkout emits an initial `order.created` customer event.
- Public APIs expose UUIDs/SKUs rather than database primary keys.
- Batch 7 operations documented in `docs/ORDERS_SHIPPING.md` and `docs/API_CONTRACT.md`.

Feature-branch validation from GitHub Actions run `35692548969`:

- PHPUnit: **74 tests, 424 assertions, zero warnings**.
- Laravel Pint: **174 files passed**.
- Composer dependency audit: **no security vulnerability advisories found**.
- Vite production build: **successful**.
- MySQL 8.4: **fresh migration + deterministic seeding successful**.
- PHP syntax checks and Composer validation: **passed**.
- PR-triggered CI run `35692734951`: **quality + MySQL smoke both green**.
- Post-merge `main` CI run `35692831968`: **quality + MySQL smoke both green**.

## Batch 8 — Admin, roles, Stripe operations and audit logging

Status: **complete and merged to `main` via PR #15 after green feature-branch and PR CI; post-merge `main` CI is green**.

Implemented:

- First-party relational RBAC with users, roles, permissions and protected super-admin behavior.
- Laravel policies for products, orders, payments, customers, measurement definitions, tailoring requests, settings, roles and audit logs.
- Permission middleware for the administration boundary.
- Safe first-super-admin bootstrap command with no hard-coded production credentials.
- Custom locale-scoped admin dashboard integrated with the existing KabulFit UI.
- Product/catalog administration including localized content, SEO fields, pricing, stock and tailoring eligibility.
- Order administration using the existing authoritative order lifecycle service.
- Shipment creation/status operations using the existing shipping lifecycle.
- Customer activation, preferred-language and role administration.
- Measurement-definition range/required/active controls and localized instructions.
- Tailoring request administration with safe cancellation only before order placement.
- Stripe payment visibility and provider/webhook payment-event history.
- Full Stripe refund operations routed through `PaymentService` and `PaymentGateway`.
- Refund state propagation into payment/order lifecycle and customer notification flow.
- Settings-backed homepage SEO for English/Dari/Pashto plus contact email.
- Existing public SEO baseline preserved when settings are first seeded.
- Immutable administrative audit logs containing actor, action, subject, metadata, IP/user-agent and timestamp.
- Role-permission management with protected super-admin permission set.
- Dedicated `docs/ADMIN.md` operational/authorization documentation.
- Authorization, bootstrap, role-assignment, SEO-setting, audit immutability, payment event and refund regression tests.

Feature-branch validation from GitHub Actions run `35694202519`:

- PHPUnit: **83 tests, 455 assertions, zero warnings**.
- Laravel Pint: **207 files passed**.
- Composer dependency audit: **no security vulnerability advisories found**.
- Vite production build: **successful**.
- MySQL 8.4: **fresh migration + deterministic seeding successful**.
- PHP syntax checks and Composer validation: **passed**.
- PR-triggered CI run `35694438047`: **quality + MySQL smoke both green**.
- Post-merge `main` CI run `35694542343`: **quality + MySQL smoke both green**.

Batch 9 intentionally remains responsible for the dedicated tailor workspace, tailor assignments and tailor-specific workflow.

## Not yet complete

- Dedicated tailor dashboard and tailor assignment workflow.
- Blog/content CMS.
- Flutter application implementation.
- Real legacy-ID mapping crawl.
- Browser/mobile visual-regression evidence.
- Lighthouse/Core Web Vitals and mobile performance evidence.
- Composer/npm/mobile lockfile and release reproducibility gates.

## Release gate

Batch 8 is fully closed after green feature-branch, PR and post-merge `main` CI. The next implementation gate is Batch 9 — tailor dashboard.
