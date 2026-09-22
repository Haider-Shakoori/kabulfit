# Measurements and Custom Tailoring

Batch 6 makes Laravel authoritative for reusable measurements and tailored-cart/order data across the web storefront and future Flutter application.

## Canonical units

All saved and snapshotted measurements are stored in centimetres using fixed decimal database columns. Customers may enter or view centimetres or inches. Conversion happens on the server so web and mobile clients cannot drift into different authoritative values.

## Measurement definitions

Definitions are configurable relational records scoped by garment type. The seeded baseline supports:

- Perahan Tunban — neck, shoulder, chest, sleeve length, shirt length, waist, trouser length and inseam.
- Afghan dress — shoulder, bust, waist, hip, sleeve length and dress length.
- Waistcoat — shoulder, chest, waist and waistcoat length.

Each definition has localized English/Dari/Pashto names and instructions, validation bounds, required state, sort order and guide media.

## Profiles

Customers can maintain reusable profiles from the account area or the versioned API. A profile belongs to exactly one customer and garment type. The first profile automatically becomes the default; selecting another default clears the previous default.

Profiles attached to an active tailored cart line cannot be deleted or changed to a different garment type. Measurement values can still be corrected before checkout; the definitive historical snapshot is made at order creation.

## Tailored cart flow

1. A product explicitly declares whether tailoring is enabled and its measurement garment type.
2. The customer chooses a compatible saved profile and variant.
3. Laravel creates a tailoring request and a dedicated custom cart line.
4. A tailored cart line always has quantity one.
5. Removing the line cancels the ready tailoring request.
6. Checkout snapshots the current measurements, profile name and customer notes into the order item.
7. The tailoring request becomes `ordered`.
8. Payment failure or abandoned-checkout expiry releases inventory and cancels the linked tailoring request.

## Customer history

Authenticated customers have ownership-scoped web and API history for their tailoring requests.

- Web: `/{locale}/tailoring` and `/{locale}/tailoring/{uuid}`.
- API: `/api/v1/{locale}/tailoring` and `/api/v1/{locale}/tailoring/{uuid}`.
- Public responses use request/profile/order UUIDs, product slugs and SKUs rather than database IDs.
- Before checkout, detail views use the current owned measurement profile.
- After checkout, detail views use the immutable order-item measurement snapshot.

## Historical integrity

Order-item measurement snapshots do not depend on the future state of a saved profile. Customers may edit or delete profiles after checkout without mutating historical tailoring instructions. The customer history surface deliberately reads the snapshot after order creation so historical values cannot silently follow later profile edits.

Batch 8 will add administrative tailoring operations and authorization. Batch 9 will add the tailor-specific dashboard. Batch 14 will consume these APIs in Flutter.
