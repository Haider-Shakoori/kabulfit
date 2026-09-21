# KabulFit Project Status

Last updated: 2026-09-21

## Repository audit

The repository began with only `README.md` on `main`; no application framework, migrations, tests, CI, or existing implementation needed preservation. Batch work therefore starts from a clean Laravel 12 architecture.

## Reference-site audit summary

The current KabulFit website remains the visual/functional reference. Public discovery currently exposes generic routes including `ProductDetail`, cart, checkout, account, measurements, orders, tailor dashboard and several admin pages. Much of the crawled metadata is generic. The rebuild intentionally preserves the KabulFit content/brand direction while replacing those crawl/index patterns with localized semantic URLs, explicit index control and server-rendered metadata.

Reference homepage structure confirmed: Authentic Afghan Elegance hero; custom sizing/global shipping/quality assurances; category discovery; featured products; Afghan culture/story; measurement CTA; editorial content around Afghan clothing, Gand e Afghani/Perahan Tunban, embroidery and custom tailoring; customer proof; Kabul/Dubai contact positioning.

## Batch 1 — Foundation

Status: **implemented on feature branch; CI validation required before completion**.

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

Not claimed complete in Batch 1:

- Authentication/customer account.
- Cart, wishlist and checkout.
- Payment providers.
- Product media/variants/inventory depth.
- Measurement profiles and tailoring workflow.
- Admin/tailor dashboards and authorization matrix.
- Orders/shipping/notifications.
- Blog/content CMS.
- Real legacy-ID mapping crawl.
- Exact final visual-fidelity pass with production images/fonts.
- Lighthouse/browser evidence and final performance report.
- Composer/npm lockfiles (must be committed before production release).

## Release gate

Batch 1 is not complete until GitHub Actions is green. Nothing should merge into `main` while CI is failing.
