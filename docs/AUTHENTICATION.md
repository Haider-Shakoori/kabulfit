# KabulFit Authentication & Customer Account

Last updated: 2026-09-21

## Architecture

KabulFit uses two first-party authentication modes over one Laravel customer model:

- **Web:** Laravel session authentication with CSRF protection.
- **Flutter/mobile:** Laravel Sanctum personal access tokens sent as Bearer tokens.

Laravel remains authoritative for account status, addresses, verification state and device sessions.

## Web flows

Localized routes under `/{locale}` provide:

- registration;
- login/logout;
- forgot/reset password;
- signed email verification;
- customer account;
- saved address create/update/delete.

All authentication and account pages use `noindex,nofollow`. Login is rate-limited and regenerates the session after authentication. Logout invalidates the session and rotates the CSRF token.

## Mobile token rules

Each mobile authentication request includes a client-generated UUID plus a human-readable device name, platform and optional app version.

A token:

- is issued by Sanctum;
- is stored hashed in the database;
- has the `mobile` ability;
- expires after the configured mobile-token period;
- is named against its device UUID;
- replaces the previous token when the same device logs in again;
- is deleted on logout;
- can be revoked from another authenticated session.

The Flutter client must store the returned plain-text token using platform secure storage and must never log or persist it in ordinary preferences.

## Email verification

Registration dispatches Laravel's verification notification. Verification links are temporary signed URLs carrying the user's preferred locale. Mobile users may request another verification email from the authenticated API; the link currently completes verification on the localized web surface.

## Password resets

Forgot-password responses intentionally do not reveal whether the submitted email exists. Successful password reset revokes all existing API tokens so compromised mobile sessions cannot survive a credential reset.

## Addresses

Addresses belong strictly to one user and use UUIDs as public identifiers. The service layer guarantees that the first address becomes default, selecting a new default clears the previous one, and deleting the default promotes the next address if one exists.

## Production requirements

Production must use HTTPS, `APP_DEBUG=false`, secure session cookies, a real queue/mail transport, protected environment secrets and the configured mobile token lifetime. Mobile applications should handle 401 by discarding stale credentials and returning the user to authentication.
