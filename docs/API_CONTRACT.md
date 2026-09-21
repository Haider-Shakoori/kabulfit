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
