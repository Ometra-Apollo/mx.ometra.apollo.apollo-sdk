# Apollo SDK integration audit

Reviewed on 2026-07-19 against the active routes and PHP consumers in the
Apollo suite. The detailed verb, URI, payload, authentication, and response
contract for every public resource method remains in `api-contract.md`.

## Consumer matrix

| Consumer | SDK modules used | Authentication contexts | Result |
| --- | --- | --- | --- |
| Aeris | Proteus media and directories | User by default; application for explicit background flows | Valid |
| Flare | Proteus media, metadata, directories and LightPath; Pulse groups | User for web actions; application for hooks, jobs, imports and group sync | Valid after import normalization |
| Ignis | Proteus media, directories and categories; Pulse groups | User for web actions; application for Pulse group synchronization | Valid after removing manual Pulse transport |
| Proteus | None | N/A | Service provider only; no SDK calls found |
| Pulse | Flare stations and playlists; Ignis campaigns; Proteus LightPath | Application for device, cache and cross-service operations | Valid after response normalization |

Ember, Lume, and aeris-client do not consume the PHP package and are outside
this audit. Ember has its own Python Pulse client and must not be migrated to a
PHP-only SDK.

## Verified rules

- Resource methods use the routes recorded in `api-contract.md`; route tests
  cover the HTTP verb, relative URI, query, and payload.
- `asApplication()` is required for jobs, hooks, console imports, device API
  delivery, cache invalidation, and other sessionless work.
- Interactive Laravel requests use the user context unless the target resource
  is application-only by contract.
- Tenant and Caronte authentication headers are constructed by
  `ApolloHttpClient`; consumers must not duplicate them.
- Proteus resources return the Caronte response envelope. Ignis campaign
  methods intentionally return normalized, unwrapped DTO arrays.

## Direct HTTP exceptions and legacy debt

- Direct calls to Caronte and third-party providers remain outside Apollo SDK.
- Aeris retains its isolated `IgnisClient` only for legacy diagnostic commands.
  Its group-content, configuration, analytics, and health URLs are not present
  in the current Ignis application routes, and production application flows do
  not call them. Do not add new consumers; remove the commands in the next
  application-breaking cleanup or replace them after Ignis defines supported
  endpoints.
- Flare's backfill command uses the SDK's delegated-user-token methods for
  directory grants and media lookup; it no longer constructs Apollo headers or
  URLs directly.
