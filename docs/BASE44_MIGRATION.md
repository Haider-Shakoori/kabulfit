# Base44 → Laravel migration

## Decision

Keep Laravel 12 as the authoritative backend and preserve the Base44 React UI as the frontend. Remove all runtime dependence on Base44.

## Source snapshot

Uploaded Base44 export:
- 156 files
- SHA-256: `bc2f77248754f94cc69d14bc2d8ad59bd3faf3550aac9caf47481750187cfcd4`

## Branch safety

- `archive/pre-base44-2026-10-05` — exact Laravel state before migration.
- `import/base44-2026-10-05` — untouched Base44 source reference.
- `feature/base44-laravel-migration` — integration branch.

## Architecture

Laravel owns:
- MySQL schema and domain models
- Sanctum authentication
- authorization and permissions
- products, categories, variants, inventory
- carts and wishlists
- checkout, Stripe and PayPal
- orders and fulfilment
- measurements and tailoring
- admin APIs
- uploads, mail, notifications and queues
- localization/SEO server contracts

React owns:
- storefront UI
- customer account UI
- admin UI
- tailor UI
- interaction state and API consumption

## Base44 replacement map

| Base44 | Laravel |
|---|---|
| base44.auth | Sanctum/session auth |
| base44.entities.Product | Product API |
| base44.entities.Category | Category API |
| base44.entities.CartItem | Cart API |
| base44.entities.Wishlist | Wishlist API |
| base44.entities.Order | Order API |
| base44.entities.Measurement | Measurement API |
| base44.entities.Address | Address API |
| base44.entities.User | User/Admin API |
| base44.entities.ShippingRate | Shipping API |
| base44.integrations.Core.UploadFile | Laravel Storage |
| base44.integrations.Core.SendEmail | Laravel Mail/Notifications |
| base44.functions.invoke | Laravel controller/service endpoints |
| Base44 workflows | Laravel events/listeners/queued jobs |

## Migration sequence

1. Preserve existing Laravel main in archive branch.
2. Preserve untouched Base44 export.
3. Add React/Vite frontend alongside Laravel.
4. Replace @base44/sdk client with Laravel API client.
5. Replace Base44 auth with Sanctum.
6. Wire catalog and product pages.
7. Wire cart/wishlist.
8. Wire checkout and payment flows.
9. Wire account/addresses/orders.
10. Wire measurements/tailoring.
11. Wire admin pages.
12. Wire uploads, notifications and workflows.
13. Preserve EN/FA/PS and RTL.
14. Run PHP, JS, MySQL and browser regression tests.
15. Merge only after CI and migration verification are green.
