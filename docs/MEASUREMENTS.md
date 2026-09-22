# Measurements and Custom Tailoring

KabulFit stores all measurement values canonically in centimetres. Web and Flutter clients may display or submit centimetres or inches; conversion occurs server-side.

Measurement definitions are configurable relational records grouped by garment type, localized in English, Dari and Pashto, with minimum/maximum validation ranges and instructions. Customer profiles are reusable and owned by one customer.

Tailoring requests link a product/variant to an owned measurement profile and optional customer notes. Checkout copies the profile values and localized definition names into `order_item_measurements`. Those snapshots are immutable historical order data and do not change when a customer later edits or deletes a reusable profile.

Mobile endpoints live under `/api/v1/{locale}`: definitions, profile CRUD and tailoring request creation. Public identifiers are UUIDs; internal database IDs are not part of the mobile contract.

Batch 8 will add staff assignment/operations around tailoring. Batch 9 will add the tailor-specific authorized dashboard. Batch 14 will consume these APIs in Flutter.
