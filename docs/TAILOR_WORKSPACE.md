# Tailor Workspace

Batch 9 adds a dedicated production workspace for KabulFit tailors without weakening the customer measurement or order-history guarantees established in earlier batches.

## Domain boundary

Customer tailoring requests continue to be created by the existing measurement/tailoring service. A tailor assignment is a separate operational record created only after:

- the tailoring request has reached the ordered state;
- the related order payment is successful;
- checkout has created an immutable order-item measurement snapshot; and
- the assignee is an active user with the `tailoring.work` permission.

Tailors never depend on the customer's mutable saved measurement profile while producing an ordered garment. The workspace reads the immutable `order_item_measurements` snapshot captured at checkout.

## Authorization

The seeded `tailor` role has only `tailoring.work`. It does not grant administration access.

Administrators with `tailoring.manage` can assign or reassign eligible tailoring requests. Tailor web/API queries are always scoped to the authenticated assignee. Another tailor's assignment is not returned by the staff API and is forbidden by the web policy.

Public/staff contracts expose UUIDs, SKUs and order numbers. Internal database IDs are not part of the web/mobile API contract.

## Assignment lifecycle

One active operational assignment record exists per tailoring request. Supported states are:

1. `assigned`
2. `accepted`
3. `in_progress`
4. `fitting`
5. `completed`
6. `cancelled`

Allowed transitions are server-authoritative:

- assigned → accepted | cancelled
- accepted → in_progress | cancelled
- in_progress → fitting | completed | cancelled
- fitting → in_progress | completed | cancelled
- completed/cancelled are terminal

Reassignment is an administrator operation. It resets the operational status to assigned but preserves the append-only event/audit history. Completed work cannot be reassigned.

## Notes and auditability

Tailor notes are append-only workmanship records. Assignment events are also append-only and capture assignment, reassignment, status changes and note creation.

Administrative audit logging records assignment creation/reassignment, status changes and note creation with public UUID metadata, actor, request context and timestamp.

## Web workspace

Locale-scoped routes:

- `GET /{locale}/tailor`
- `GET /{locale}/tailor/assignments/{assignment_uuid}`
- `POST /{locale}/tailor/assignments/{assignment_uuid}/status`
- `POST /{locale}/tailor/assignments/{assignment_uuid}/notes`

English, Dari and Pashto are supported through the same application shell; Dari and Pashto render RTL.

## Authorized staff API

The v1 staff API is intentionally reusable by a future authorized Flutter tailor client:

- `GET /api/v1/{locale}/tailor/assignments`
- `GET /api/v1/{locale}/tailor/assignments/{assignment_uuid}`
- `POST /api/v1/{locale}/tailor/assignments/{assignment_uuid}/status`
- `POST /api/v1/{locale}/tailor/assignments/{assignment_uuid}/notes`

Authentication uses the existing Sanctum bearer-token mechanism. The same Laravel assignment service enforces transitions for web and API callers.
