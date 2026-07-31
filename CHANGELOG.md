# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

> **This file is the single source of truth for project history.**
> Every future release - whether produced by a human or an AI agent - must update this file
> before merging to `main`.

---

## [6.0.0] - 2026-07-31

Breaking release that aligns Ignis group resources with the shared Apollo
`groups` terminology. No compatibility aliases are included.

### Changed

- Rename the Ignis campaign group entrypoint from `externalGroups()` to `groups()`.
- Send Ignis campaign requests to `/api/groups/{groupId}/campaigns`.

### Removed

- Remove `ExternalGroupResource` and the legacy `/api/external-groups` SDK contract.

## [5.1.1] - 2026-07-20

### Changed

- Document AppMenu's Suite-backed application discovery and current customization points.
- Enable strict types in `SuiteApplicationController`.
- Raise static analysis from PHPStan level 5 to level 7 and add iterable value contracts.
- Align directory application grant revocation with Proteus application authentication.

### Removed

- Remove the unused controller request injection and the obsolete AppMenu
  `APP_NAMES`/`APPS_ORDER` aliases left by the static application list.

## [v5.0.0] - 2026-07-20

Breaking release that reorganizes Apollo around bound resources and domain
verbs. No compatibility aliases are included.

### Changed

- Bind instance IDs at resource selection time for media, directories,
  playlists, station groups, external groups, campaigns and grants.
- Standardize collection actions as `index` and `store`, instance deletion as
  `destroy`, and grant workflows as `request`, `extend` and `revoke`.
- Nest media metadata, LightPath requests, playlist items, station groups,
  Pulse catalogs/cache and Ignis campaigns under their owning resources.
- Return the full Caronte envelope from every JSON operation.
- Let Proteus resolve application directory grants from Caronte tenant and
  application context.
- Require PHP 8.4 or newer and validate the SDK on PHP 8.4 and 8.5 in CI.

### Removed

- Explicit user-token parameters and token-specific method variants.
- Media creation helpers, presets, content hits and wrappers without active
  production consumers.
- Public module configuration accessors and Ignis campaign DTO conversion.
- Directory grant permission updates and explicit directory-grant parameters
  from media operations.

### Migration

- Require `ometra/apollo-sdk ^5.0.0`.
- See `BREAKING_CHANGES.md` and `docs/api-contract.md`.

## [v4.3.0] - 2026-07-19

Backward-compatible release adding cross-service group management and SDK quality gates.

### Added

- Add Flare station group lookup, assignment, detachment, and cache invalidation APIs.
- Add Pulse group catalog and station-cache invalidation APIs.
- Add delegated-user-token support for background Proteus media lookups.
- Add Pint and PHPStan checks for the SDK source.

### Changed

- Standardize SDK source formatting and document the verified consumer integration matrix.

### Breaking Changes

- None.

## [v4.2.0] - 2026-07-16

Feature release centralizing Proteus directory-application grant transport in the SDK.

### Added

- Add typed Proteus directory application grants with `read` and `write` levels.
- Add delegated-user-token support for directory grants created by background processes.
- Allow LightPath URL requests to carry their owning directory application grant.

### Changed

- Document the user, delegated-user, and application authentication contexts for grant operations.

### Breaking Changes

- None.

## [v4.1.0] - 2026-07-16

Feature release adding management helpers for Proteus LightPath grants.

### Added

- Add `Apollo::proteus()->lightPath()` with helpers to extend and delete
  LightPath grants.
- Document LightPath grant identifiers, renewal windows, and application-token
  management.
- Add route, API-shape, and application-authentication coverage for LightPath
  grant operations.

### Changed

- Nothing changed.

### Fixed

- Nothing fixed.

### Removed

- Nothing removed.

### Deprecated

- Nothing deprecated.

### Security

- Nothing changed.

### Breaking Changes

- None.

## [v4.0.0] - 2026-07-15

Major release aligning Apollo's transport and authentication behavior with
Caronte SDK 7.1, correcting the Proteus metadata contract, and hardening the
published frontend components.

### Changed

- Require `ometra/caronte-sdk ^7.1.0` and inherit its raw-response and multipart transport.
- Send either `X-Group-Token` or `X-Application-Token` according to the canonical Caronte authentication contract, never both.
- Remove duplicated HTTP, header, tenant, and multipart handling from `ApolloHttpClient`.
- Keep the internal frontend route prefix fixed at `/_apollo` so published UI defaults and backend routes cannot diverge.
- Preserve falsey non-null values when serializing `ExternalGroupDTO`.
- Surface directory loading and creation failures in `DirectoryTree` and retain failed folder input for retry.
- Derive the active `AppMenu` application from the current Inertia URL and keep URL construction in one utility.
- Change `MetadataResource::index()` to require a media ID and call
  `GET /media/{mediaId}/metadata`, with an optional query array.
- Use application authentication for Flare station reads.
- Return only `base_url` from module `config()` methods and remove the redundant
  `base_url_env` configuration entries.
- Register publishable assets only when the host application exposes Laravel's
  config and resource path APIs.
- Accept either `type` or `media_type` when hydrating `IgnisCampaignContentDTO`
  and serialize both fields.

### Added

- Tests for Caronte 7.1 authentication, inherited raw and multipart transport,
  stable frontend routes, component error states, and falsey DTO values.

### Removed

- Unused direct AWS S3, database, and Guzzle dependencies.
- The hardcoded `src/Test/DummyGroup.php` fixture from the production autoload tree.

### Fixed

- Correct the Proteus metadata listing endpoint, which previously called the
  global metadata search route instead of the media-scoped route.
- Omit the false `nameStation` query parameter from Flare station detail calls.

### Deprecated

- Nothing deprecated.

### Security

- Group-authenticated requests no longer send an application token alongside
  the group token.

### Breaking Changes

- `MetadataResource::index()` changed from `index(string $key = '')` to
  `index(string $mediaId, array $query = [])`.
- Module `config()` results no longer contain `base_url_env`.
- `apollo.frontend.route_prefix` was removed; SDK frontend routes always use
  `/_apollo`.
- `ometra/caronte-sdk ^7.1.0` is now required.

## [v3.7.0] - 2026-07-13 "Astra"

Enhancement release adding Proteus media thumbnail support and improving
LightPath media URL documentation.

### Added

- `MediaResource::thumbnail(string $id)` helper for retrieving thumbnail images
  from Proteus media assets.
- `README.md` and `docs/api-contract.md` documentation clarifying LightPath URL
  request payload and media thumbnail usage.

### Changed

- Updated `docs/api-contract.md` to document the `Apollo::proteus()->media()->lightPathUrl()`
  payload, response fields, and usage notes.

### Fixed

- No bug fixes in this release.

### Removed

- Nothing removed.

### Deprecated

- Nothing deprecated.

### Security

- No security changes.

### Breaking Changes

- None in `v3.7.0`.

## [v3.4.0] - 2026-06-14 "Hermes"

Compatibility feature release focused on delegated authentication for
application-scoped requests while preserving existing request contracts.

### Added

- `ApolloHttpClient::applicationRequest()` now accepts an optional
  `?string $userToken` argument. When provided, the client forwards it as
  `X-User-Token` alongside application headers.
- Unit coverage in `ApolloHttpClientTest` validating explicit delegated
  `X-User-Token` injection in application requests.

### Changed

- `RecordingApolloHttpClient::applicationRequest()` test double signature now
  mirrors the production client method, including `?string $userToken = null`.

### Fixed

- Internal formatting cleanup in `ApolloHttpClient::userRawRequest()`.

### Removed

- Nothing removed.

### Deprecated

- Nothing deprecated.

### Security

- No security changes.

### Breaking Changes

- None in `v3.4.0`.

## [v3.3.0] - 2026-06-13 "Artemis"

Focused feature release that extends Ignis campaign access with a campaign
detail endpoint and validates route behavior through dedicated unit coverage.

### Added

- `CampaignsResource::show(string $id)` in Ignis for campaign detail retrieval
  via `GET /api/campaigns/{id}`.
- Route test coverage for the new campaign detail endpoint in
  `IgnisResourceRoutesTest`.

### Changed

- `docs/api-contract.md` updated to include the new Ignis campaign detail route
  in the SDK coverage contract.

### Fixed

- No functional regressions fixed in this release.

### Removed

- Nothing removed.

### Deprecated

- Nothing deprecated.

### Security

- No security changes.

### Breaking Changes

- None in `v3.3.0`.

## [v2.0.0] - 2026-05-08 "Helios"

Apollo SDK migration: the package is now `ometra/apollo-sdk`, configured by
`config/apollo.php`, and consumed exclusively through the modular API
`Apollo::proteus()->media()->index(...)`. Module URLs are `PROTEUS_BASE_URL`,
`PULSE_BASE_URL`, `FLARE_BASE_URL`, and `IGNIS_BASE_URL`.

Complete ground-up rewrite of the SDK. The old architecture (`BaseApiService`,
legacy HTTP client, DB migrations, Partials) has been replaced with a focused
HTTP-client layer built on top of `caronte-sdk`'s `CaronteHttpClient`.

### Added

- **`ApolloHttpClient`** – low-level HTTP client extending `CaronteHttpClient`.
  Handles URL assembly, multipart detection, query-string building, retry
  configuration, and `X-User-Token` / `X-Application-Token` / `X-Tenant-Id`
  header injection.
- **Proteus module resources** – contextual actions under media, metadata,
  categories, directories, and presets.
- **`ApolloServiceProvider`** – registers the Apollo entrypoint and publishes
  `config/apollo.php` under the `apollo-config` tag.
- **`Facades\Apollo`** – static facade bound to the Apollo entrypoint.
- **`docs/api-contract.md`** – machine-readable contract listing every endpoint,
  auth requirements, permissions, and SDK coverage table.
- **PHPUnit test suite** (`tests/`) covering Apollo identity, modules, Proteus
  resources, docs, and legacy removal.
- **`phpunit.xml`** configuration file.
- **`.editorconfig`** and **`.gitattributes`** project-level config files.
- **PHPDoc** on all public and protected methods across every API class.

### Changed

- `composer.json` updated to `ometra/apollo-sdk`; requires `php ^8.2`,
  `illuminate/{database,http,routing,support} ^12.0`,
  `guzzlehttp/guzzle ^7.9`, `ometra/caronte-sdk ^4.0`,
  `league/flysystem-aws-s3-v3 ^3.0`.
- `config/apollo.php` owns module base URLs for Proteus, Pulse, Flare, and Ignis.
- `README.md` rewritten in Spanish; documents installation, configuration,
  auth headers, tenant context setup, and usage examples.

### Removed

- `src/BaseApiService.php` – monolithic HTTP service class.
- `src/Partials/DownloadMedia.php` – extracted partial.
- `src/Partials/PayloadFormatting.php` – extracted partial.
- Legacy root facade, provider, config, API wrappers, and exception classes.
- `database/migrations/` – DB migrations are no longer part of this SDK.
- `IMPLEMENTATION_GUIDE.md` – replaced by `docs/api-contract.md` and `README.md`.
- `PROTEUS_APPS_GUIDE.md` – application management is no longer SDK's concern.
- `LICENSE` file removed (project is now under org-level licensing).

### Breaking Changes

See [BREAKING_CHANGES.md](BREAKING_CHANGES.md) for full migration guidance.

- `BaseApiService` and legacy flat facade access are gone. All API interaction
  now goes through module resources.
- DB migrations are no longer published by the service provider.
- The legacy client is replaced by `ApolloHttpClient` (different namespace and API).
- Module base URLs live under `config/apollo.php`.
- Legacy app-token variables and request user URI parameters are not used.
  Authentication is handled entirely by `caronte-sdk`.

---

<!-- Future releases: prepend a new ## [vX.Y.Z] section above this line. -->
