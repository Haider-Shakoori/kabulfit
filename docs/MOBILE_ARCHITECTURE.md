# KabulFit Mobile Architecture

Last updated: 2026-09-21

## Product direction

KabulFit will ship with a dedicated **Flutter** mobile application for Android and iOS. The mobile app is a client of the existing Laravel platform rather than a second commerce backend.

## Shared platform rules

Laravel remains the single source of truth for:

- customers and authentication;
- localized catalog content;
- categories, collections, media and variants;
- pricing, discounts, coupons and currency calculations;
- inventory and availability;
- carts and checkout state;
- shipping configuration and calculations;
- reusable measurement profiles and order-time measurement snapshots;
- custom-tailoring workflow;
- orders and status history;
- Stripe PaymentIntents, webhook verification, idempotency and refund state;
- reviews, notifications, roles, permissions and audit logs.

The Flutter app must not calculate authoritative prices, trust client-supplied totals, mark payments as successful, mutate inventory independently or duplicate permission rules.

## API contract

Mobile-facing endpoints must be versioned under a stable API namespace. API responses should be:

- localized using the selected app locale;
- pagination-friendly;
- explicit about money in integer minor units plus currency;
- explicit about stock/variant availability;
- optimized for mobile payload size;
- protected by validation, authorization, throttling and idempotency where required;
- covered by feature/contract tests before mobile screens depend on them.

Breaking API changes require versioning or a coordinated mobile release strategy.

## Authentication

The Flutter client will use secure, revocable API authentication with credentials/tokens stored using platform-secure storage. Authentication must support registration, login, logout, password reset/verification and authenticated account endpoints without exposing web session assumptions to the app.

## Stripe

Stripe remains server-authoritative.

The mobile client may use Stripe's official mobile SDK/payment sheet for card entry and supported wallet methods, but:

1. Laravel creates the payment intent using server-calculated order totals.
2. The app receives only the client-side data required by Stripe.
3. Payment completion in the client is not treated as final proof of payment.
4. Signed Stripe webhooks update payment/order state idempotently on the server.
5. The app refreshes the server order/payment state after completion.
6. Refunds and administrative payment actions are controlled from authorized server/admin workflows.

Raw card details must never pass through or be stored by KabulFit servers.

## Mobile product scope

The production app is expected to include:

- branded launch/loading experience;
- home and featured collections;
- categories/collections;
- search, filters and sorting;
- product detail, gallery, variants, stock and reviews;
- wishlist and cart;
- customer authentication/profile;
- saved addresses;
- reusable measurement profiles;
- measurement guide;
- custom-tailoring action from product detail;
- shipping and Stripe checkout;
- order confirmation/history/detail/tracking;
- localized notifications and deep links;
- English, Dari and Pashto, including RTL;
- accessibility, analytics/consent and production error handling.

## Repository strategy

The mobile application should live in its own repository (planned: `kabulfit-mobile`) so Flutter dependencies, mobile CI, signing configuration and app releases are isolated from the Laravel web repository while sharing documented API contracts.

No mobile signing secrets, Stripe secret keys or production environment credentials may be committed to either repository.
