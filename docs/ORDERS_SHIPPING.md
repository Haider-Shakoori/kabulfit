# Orders, Shipping and Customer Events

Batch 7 makes Laravel authoritative for the customer-visible order lifecycle, shipment tracking, localized queued notifications and the mobile event contract.

## Order lifecycle

Order statuses are controlled by `OrderLifecycleService`; arbitrary status strings are rejected. The supported lifecycle includes payment, processing, fulfilment, delivery, return and refund states.

Every successful status change creates an immutable `order_status_histories` record and a customer event. Stripe payment outcomes use the same lifecycle service rather than bypassing it.

## Shipping

Checkout shipping methods remain configurable database records. Fulfilment creates one or more shipment records with public UUIDs, carrier/service data, optional tracking number/URL and an immutable tracking-event timeline.

Shipment state changes are validated. Shipping progress synchronizes the customer-facing order state for ready/shipped/delivered/returned milestones.

## Notifications

Order and shipment updates use Laravel notifications implementing `ShouldQueue`. Notifications are dispatched after database commit, localized with the customer's preferred `en`, `fa` or `ps` locale, and sent through mail plus the database channel.

## Mobile event contract

Authenticated mobile clients can resume event consumption with an opaque public event UUID:

`GET /api/v1/{locale}/events?after={event_uuid}&limit=50`

The event stream never exposes database primary keys. Event types and payloads are suitable for a future push-notification bridge. Clients should treat unknown additive event types or payload keys as forward-compatible.

## Customer APIs

- `GET /api/v1/{locale}/orders`
- `GET /api/v1/{locale}/orders/{order_uuid}`
- `GET /api/v1/{locale}/orders/{order_uuid}/tracking`
- `GET /api/v1/{locale}/events`

All order, shipment and event resources are ownership-scoped to the authenticated customer and use UUIDs/SKUs rather than database IDs.
