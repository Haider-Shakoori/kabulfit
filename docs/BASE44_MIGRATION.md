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
