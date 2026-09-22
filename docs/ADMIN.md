# Administration, RBAC, Stripe Operations and Audit Logging

Batch 8 adds KabulFit's first-party administration and governance layer without introducing a second business-rule stack.

## Administration

Admin routes are locale-scoped under `/{locale}/admin`, require an authenticated active user and require the `admin.access` permission. Individual controllers additionally authorize their model actions through Laravel policies.

The administration area covers:

- dashboard metrics and recent operations;
- localized product content, pricing, SEO and tailoring eligibility;
- order lifecycle and shipment operations through `OrderLifecycleService`;
- customer activation, preferred locale and role assignment;
- measurement-definition ranges and localized instructions;
- tailoring request inspection and safe cancellation before ordering;
- Stripe payment visibility, provider/webhook event history and eligible full refunds;
- homepage SEO/contact settings;
- role/permission configuration;
- immutable administrative audit history.

Batch 9 remains responsible for the dedicated tailor workspace and tailor assignment workflow.

## Roles and permissions

KabulFit uses first-party relational RBAC tables. Seeded roles are:

- `super-admin`
- `administrator`
- `operations`
- `catalog-manager`
- `support`
- `tailor`

The super-admin role is protected and bypasses individual policy checks. The tailor role currently receives only the future `tailoring.work` capability; it does not receive broad admin access.

There are no hard-coded production administrator credentials. After creating a normal user through the supported authentication flow, an operator can grant the initial role from the server CLI:

`php artisan admin:grant-super user@example.com`

## Audit logging

Administrative mutations write immutable `audit_logs` rows containing:

- public audit UUID;
- actor;
- action;
- optional subject type/id;
- structured before/after or action metadata;
- source IP and user agent where available;
- creation timestamp.

Audit models reject update/delete operations at the application layer.

## Stripe refunds and payment audit trail

The admin controller does not call Stripe directly. Refunds pass through `PaymentService` and the existing `PaymentGateway` abstraction.

Only users with `payments.refund` may refund. The current admin UI intentionally supports full refunds of succeeded payments. Stripe idempotency remains in `StripePaymentGateway`.

Successful refunds:

1. create a payment event;
2. mark the payment and order payment state refunded;
3. transition the order through `OrderLifecycleService`;
4. notify the customer through the existing order notification workflow;
5. create an immutable admin audit entry.

The payment detail page also exposes the existing Stripe webhook/payment event trail for operational debugging. No card details or secrets are stored in audit records.

## Settings and SEO

Homepage title/description values for English, Dari and Pashto plus the public contact email are stored in the settings table and cached briefly by `SiteSettings`. Admin updates invalidate the relevant cache key immediately.
