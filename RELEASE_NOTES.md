# Release v3.0.0 "Apollo"

**Date:** 2026-05-08
**Branch:** `remake` → `main`
**Package:** `ometra/apollo-sdk`

---

## Summary

Apollo is the clean-cut modular SDK migration. The package now uses `config/apollo.php`, exposes the Apollo entrypoint, and delegates authentication to `caronte-sdk`.

Proteus API domains are available through contextual module resources, for example `Apollo::proteus()->media()->index(...)`. Pulse, Flare, and Ignis are placeholders until their endpoint contracts are defined.

---

## Highlights

- **Full Proteus coverage** – 36 actions across 5 domains with zero missing endpoints.
- **Caronte-native auth** – `X-Application-Token`, `X-User-Token`, and `X-Tenant-Id` are
  assembled automatically from `caronte-sdk`; no more manual token configuration.
- **Multipart auto-detection** – payloads containing `UploadedFile` instances are
  transparently converted to multipart requests.
- **BeeHive tenant resolution** – `TenantContext` is resolved at request time; no static
  config required.
- **PHPUnit test suite** – module test doubles capture requests for assertion without hitting the network.
- **Zero DB footprint** – migrations removed; the SDK is now a pure HTTP client.
- **PHP 8.2+ and Laravel 12** – modern baseline with strict types throughout.

---

## Added

- `ApolloHttpClient` — low-level HTTP client (extends `CaronteHttpClient`).
- Proteus module resources: media, metadata, categories, directories, presets.
- Apollo main class with module accessors: `proteus()`, `pulse()`, `flare()`, `ignis()`.
- `Facades\Apollo` static facade.
- `ApolloServiceProvider` (singleton registration, config publishing).
- `docs/api-contract.md` — endpoint contract reference.
- Full PHPUnit test suite under `tests/`.
- PHPDoc on all public and protected API methods.

## Changed

- `composer.json` — updated dependencies (PHP 8.2, Laravel 12, Guzzle 7.9, Caronte SDK 4.0).
- `config/apollo.php` — module URL configuration for `PROTEUS_BASE_URL`, `PULSE_BASE_URL`, `FLARE_BASE_URL`, and `IGNIS_BASE_URL`.
- `README.md` — rewritten with installation, configuration, auth, and usage sections.

## Removed

- Legacy root entrypoint, facade, provider, config, API wrappers, exception, `BaseApiService`, `DownloadMedia`, `PayloadFormatting` partials.
- DB migrations.
- Old guides (`IMPLEMENTATION_GUIDE.md`, `PROTEUS_APPS_GUIDE.md`).

---

## Breaking Changes

This is a **major version** bump. Consumers of v1.x must migrate. See
[BREAKING_CHANGES.md](BREAKING_CHANGES.md) for step-by-step migration guidance.

---

## Full History

See [CHANGELOG.md](CHANGELOG.md) for the complete project history.
