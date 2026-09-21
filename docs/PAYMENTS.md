# Payments and Checkout

Batch 5 makes Laravel authoritative for cart totals, stock reservations, coupons, shipping, orders and payment state.

## Stripe

The server uses `stripe/stripe-php` and PaymentIntents. Configure `STRIPE_KEY`, `STRIPE_SECRET` and `STRIPE_WEBHOOK_SECRET`. Secrets must never be exposed to the Flutter app or committed.

The checkout API creates an order and PaymentIntent using an idempotency key derived from the order UUID. Clients receive only the publishable integration data and PaymentIntent client secret.

The webhook endpoint is `POST /api/stripe/webhook`. Stripe signature verification is mandatory. Only verified webhook events may transition an order to paid, failed or cancelled. Duplicate provider event IDs are ignored.

Inventory is reserved transactionally when checkout starts. A successful PaymentIntent converts reservations to sold stock exactly once. Failed/cancelled intents release reservations.

## Mobile contract

Flutter uses the same authenticated v1 cart, wishlist and checkout endpoints as the web business domain. The mobile app must not calculate authoritative totals, discounts or stock locally. Batch 14 will integrate Stripe's official Flutter payment UI against this server contract.
