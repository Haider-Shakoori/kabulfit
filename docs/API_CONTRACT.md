# KabulFit Public API Contract

Last updated: 2026-09-21  
Current version: **v1**

The API is designed for the KabulFit Flutter client while remaining usable by other authorized first-party clients. Laravel is the server-authoritative source for catalog, price and inventory state.

## Base pattern

`/api/v1/{locale}/...`

Supported locales: `en`, `fa`, `ps`.

Public catalog endpoints are read-only and rate-limited.

### Product list

`GET /api/v1/{locale}/catalog/products`

Supported query parameters:

- `q`
- `category` — localized category slug
- `collection` — localized collection slug
- `size` — stable option value code
- `color` — stable option value code
- `embroidery` — stable option value code
- `in_stock=1`
- `price_min_minor`
- `price_max_minor`
- `sort=featured|newest|price_asc|price_desc|name`
- `per_page` — 1–48

The response uses Laravel pagination. Product identity exposed to clients is based on localized slug and SKU; internal database IDs are intentionally omitted.

### Product detail

`GET /api/v1/{locale}/catalog/products/{slug}`

Returns:

- localized content;
- category and collections;
- integer-minor-unit price + currency;
- server-calculated availability;
- media with dimensions and localized alt text;
- active variants;
- stable option/value codes;
- variant price and stock;
- related products.

### Catalog facets

`GET /api/v1/{locale}/catalog/facets`

Returns localized categories, collections and filterable option definitions while keeping machine-stable option/value codes for Flutter state.

## Money

Authoritative monetary values are transmitted as integer minor units:

```json
{
  "price": {
    "minor": 980000,
    "currency": "AFN",
    "formatted": "9,800.00 AFN"
  }
}
```

Clients must never calculate checkout totals from display strings.

## Inventory

Availability is derived from variant inventory:

`available = quantity_on_hand - reserved_quantity`

The public API exposes only calculated available quantity. Inventory mutations are not part of this public read API.

## Compatibility

Breaking field/semantic changes require a new API version or a coordinated mobile release. Additive fields may be introduced within v1 when they do not change existing semantics.
