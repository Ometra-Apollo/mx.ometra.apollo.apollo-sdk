# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

> **This file is the single source of truth for project history.**
> Every future release - whether produced by a human or an AI agent - must update this file
> before merging to `main`.

---

## [Unreleased]

### Added

- Suite-styled Laravel HTTP error pages for `401`, `403`, `404`, `419`, `429`,
  `500`, and `503`.
- Automatic error-page fallback registration through the Apollo service
  provider, with `APOLLO_ERROR_PAGES_ENABLED=false` as an opt-out.
- `apollo-error-pages` publish tag for customizable host copies, plus aggregate
  `apollo` publishing for config and error pages.

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
