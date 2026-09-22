# KabulFit Public API Contract

Current version: **v1**  
Last updated: 2026-09-21

The public catalog API is designed for the future Flutter application and other first-party KabulFit clients. Laravel remains authoritative for catalog state, money, stock and later checkout/payment state.

## Base routes

All catalog endpoints are locale-scoped:

- `GET /api/v1/{locale}/catalog`
- `GET /api/v1/{locale}/products/{slug}`
- `GET /api/v1/{locale}/categories`
- `GET /api/v1/{locale}/collections`

Supported locales are `en`, `fa` and `ps`.

## Catalog filters

`GET /api/v1/{locale}/catalog` supports:

- `q`
- `category` localized slug
- `collection` localized slug
- `size` size code
- `color` localized slug
- `min_price` decimal display amount
- `max_price` decimal display amount
- `in_stock=1`
- `sort=featured|newest|price_asc|price_desc`
- `per_page` from 1 to 48

Validation failures return HTTP 422 JSON responses.

## Public identifiers

Public responses intentionally avoid database primary keys. Stable client identifiers are:

- product localized slug and SKU;
- variant SKU;
- category localized slug;
- collection localized slug;
- color code/localized slug;
- size code.

## Money

Money is returned with integer minor units and ISO currency, for example:

```json
{
  "price": {
    "minor": 650000,
    "currency": "AFN",
    "formatted": "6,500.00 AFN"
  }
}
```

Clients must not use formatted strings for calculations.

## Inventory

Variant inventory is authoritative. Each variant exposes an available quantity calculated as on-hand minus reserved quantity. Product availability is the sum of active variant availability. The legacy product-level stock field remains only as a fallback for products that have no variants.

## Media

Media responses include URL, localized alt text and intrinsic width/height. Batch 3 uses an original deterministic demo SVG until production product photography is imported through the later media/admin workflow.

## Rate limiting

The public catalog API is limited to 120 requests per minute per source IP. Later authenticated/customer endpoints will receive separate policies.

## Compatibility

Breaking response changes require a new API version or a coordinated mobile release. Additive fields may be introduced within v1 when they do not change existing semantics.


## Customer authentication and account

Mobile authentication uses Laravel Sanctum bearer tokens. The client stores the returned plain-text token only in platform-secure storage; Laravel stores only its hash.

Public auth endpoints:

- `POST /api/v1/{locale}/auth/register`
- `POST /api/v1/{locale}/auth/login`
- `POST /api/v1/{locale}/auth/forgot-password`
- `POST /api/v1/{locale}/auth/reset-password`

Registration/login accept `device_uuid`, `device_name`, `platform=android|ios`, and optional `app_version`. Reauthenticating the same device replaces its previous token.

Authenticated bearer-token endpoints:

- `GET /api/v1/{locale}/account`
- `POST /api/v1/{locale}/auth/logout`
- `POST /api/v1/{locale}/auth/email/verification-notification`
- `DELETE /api/v1/{locale}/auth/devices/{device_uuid}`
- `GET|POST /api/v1/{locale}/account/addresses`
- `PUT|DELETE /api/v1/{locale}/account/addresses/{address_uuid}`

Account/address responses intentionally use public UUIDs and do not expose database primary keys. Password reset revokes existing API tokens. Email verification links are locale-aware signed web URLs.

## Cart, wishlist and checkout

Authenticated web/mobile commerce uses the same Laravel domain rules.

- `GET /api/v1/{locale}/cart`
- `POST /api/v1/{locale}/cart/items`
- `PUT|DELETE /api/v1/{locale}/cart/items/{item}`
- `GET|POST /api/v1/{locale}/wishlist`
- `DELETE /api/v1/{locale}/wishlist/{product_slug}`
- `POST /api/v1/{locale}/checkout`
- `GET /api/v1/{locale}/orders/{order_uuid}`

Checkout accepts a saved address UUID, shipping-method code and optional coupon. The server recalculates prices, discounts, shipping and stock; clients must never submit authoritative totals.

The checkout response contains an order UUID/number and Stripe PaymentIntent client secret. Payment completion is not authoritative until the signed Stripe webhook is processed.

Stock is reserved while payment is pending. Abandoned checkout reservations expire on the configured schedule and are released.


## Measurements and custom tailoring

Measurement definitions are public read-only reference data because product screens and the Flutter measurement guide need them before profile editing. Customer profiles and tailoring actions require authenticated active-user access.

- `GET /api/v1/{locale}/measurements/definitions?garment_type={type}&unit={cm|in}`
- `GET /api/v1/{locale}/measurement-profiles`
- `POST /api/v1/{locale}/measurement-profiles`
- `PUT /api/v1/{locale}/measurement-profiles/{profile_uuid}`
- `DELETE /api/v1/{locale}/measurement-profiles/{profile_uuid}`
- `GET /api/v1/{locale}/tailoring`
- `GET /api/v1/{locale}/tailoring/{tailoring_uuid}`
- `POST /api/v1/{locale}/tailoring`

Supported garment types currently are `perahan_tunban`, `dress`, and `waistcoat`.

Measurement values are stored canonically in centimetres. API clients may submit and display centimetres or inches; validation ranges are converted server-side. Flutter must not persist a separate authoritative conversion model.

A tailoring request requires a product that explicitly supports tailoring and a saved profile whose garment type matches the product. Creating a tailoring request also creates the custom cart line. Each tailored cart line has quantity one.

At checkout, Laravel snapshots measurement codes/names/centimetre values, profile name and tailoring notes into the order item. Later profile changes or deletion cannot change historical order measurements. Snapshot models reject direct update/delete operations so order-time measurements remain application-layer immutable.

Tailoring history endpoints are ownership-scoped to the authenticated customer and expose UUIDs/SKUs/slugs rather than internal database IDs. Detail responses return the immutable order-time snapshot once an order exists; before checkout they reflect the customer's current saved profile.


## Orders, shipping, notifications and mobile events

Authenticated order and tracking endpoints are ownership-scoped and use public UUIDs. Database primary keys are never part of the v1 contract.

- `GET /api/v1/{locale}/orders`
- `GET /api/v1/{locale}/orders/{order_uuid}`
- `GET /api/v1/{locale}/orders/{order_uuid}/tracking`
- `GET /api/v1/{locale}/events?after={event_uuid}&limit={1..100}`

Order detail responses include server-authoritative totals, immutable order-item snapshots, status history and shipment tracking events. Shipment responses expose shipment/event UUIDs, carrier/service data and tracking information without internal IDs.

The mobile event feed uses the previous public event UUID as an opaque cursor. The response returns `meta.next_cursor` as another UUID and `meta.has_more`. A cursor owned by another customer is rejected. Clients must not interpret cursors as sequence numbers.

Stable event types currently include:

- `order.created`
- `order.status_changed`
- `shipment.created`
- `shipment.status_changed`

Event payloads are additive and intended to feed the future Flutter push-notification bridge. Mobile clients should ignore unknown additive payload fields and event types they do not yet handle.

Order and shipment customer notifications are queued after transaction commit, localized using the customer's preferred English, Dari or Pashto locale, and persisted through Laravel's database notification channel in addition to mail.
