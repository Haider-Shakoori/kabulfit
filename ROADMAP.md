# KabulFit Production Roadmap

Work is delivered in reviewable production-sized batches. Every batch must update `PROJECT_STATUS.md`, run relevant tests, pass formatting/build/security checks and remain unmerged until CI is green.

1. **Foundation, localization and SEO primitives** — Laravel 12 scaffold, `en/fa/ps`, RTL, design tokens, catalog foundation, robots/sitemap, legacy redirect architecture, tests and CI.
2. **Reference visual-fidelity system** — complete live-site visual inventory, exact reusable components/tokens, responsive header/footer/homepage, production media strategy and accessibility pass.
3. **Catalog domain** — categories/collections, media, variants, sizes/colors, inventory, search/filter/sort, related/recently viewed products, complete Product/Offer/Breadcrumb/ItemList schema.
4. **Authentication and customer account** — registration/login/reset/verification, addresses, order history shell, noindex controls, throttling and account authorization.
5. **Cart, wishlist and checkout** — durable cart, decimal-safe totals, coupons architecture, shipping calculation, checkout state machine and payment interfaces without fake gateways.
6. **Measurements and custom tailoring** — configurable measurement definitions, cm/inch conversion, reusable profiles, order-time snapshots, validation, guides and tailoring CTA integration.
7. **Orders, shipping and notifications** — transactional order lifecycle, status history, queueable localized emails, shipping configuration and tracking architecture.
8. **Admin, roles and audit logging** — dashboard, catalog/content/order/customer management, roles/permissions/policies, settings/SEO controls, audit logs and authorization tests.
9. **Tailor dashboard** — scoped assignments, garment/measurement views, notes, statuses and audit trail with strict authorization.
10. **Content/SEO CMS and legacy migration** — pages/blog, localized editorial metadata/schema/internal linking, sitemap indexes, full old-URL inventory and verified 301 mappings.
11. **Performance/media hardening** — AVIF/WebP responsive pipeline, caching/query tuning, CDN readiness, asset budgets, N+1 review, queue/scheduler deployment hardening.
12. **E2E, accessibility and responsive QA** — browser tests for critical journeys, WCAG 2.2 AA review and target viewport regression checks from 320px through 1920px.
13. **Production-readiness audit** — fresh install, full tests, dependency audits, Lighthouse/Core Web Vitals evidence, deployment/runbooks/backups/logging/error handling and final acceptance report.
