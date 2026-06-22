# Release v3.4.0 "Hermes"

**Date:** 2026-06-14  
**Branch:** `main`  
**Package:** `ometra/apollo-sdk`

Configuration file: `config/apollo.php`. Module URL variables:
`PROTEUS_BASE_URL`, `PULSE_BASE_URL`, `FLARE_BASE_URL`, and `IGNIS_BASE_URL`.
Typical usage remains `Apollo::proteus()->media()->index(...)`.

## Summary

This release introduces delegated user-token support for application-scoped
requests in the Apollo HTTP client. The change is backward compatible and keeps
existing behavior unchanged when no user token is provided.

"Hermes" represents this release focus: fast and reliable request delegation
across service boundaries.

## Highlights

- Added optional delegated user token support in
  `ApolloHttpClient::applicationRequest()`.
- Preserved existing default behavior for application-authenticated requests.
- Added dedicated unit coverage for delegated `X-User-Token` forwarding.

## Unreleased Notes

- Apollo now ships suite-styled Laravel HTTP error pages as an automatic
  fallback. Hosts can disable them with `APOLLO_ERROR_PAGES_ENABLED=false` or
  publish copies with `php artisan vendor:publish --tag=apollo-error-pages`.

## Added

- Optional `?string $userToken = null` parameter in
  `ApolloHttpClient::applicationRequest()`.
- `ApolloHttpClientTest::testApplicationRequestCanIncludeExplicitUserTokenWhenProvided()`
  to validate delegated header behavior.

## Changed

- `RecordingApolloHttpClient::applicationRequest()` now mirrors the production
  signature with the optional user token parameter.

## Fixed

- Minor formatting cleanup in `ApolloHttpClient::userRawRequest()`.

## Removed

- Nothing removed.

## Deprecated

- Nothing deprecated.

## Security

- No security-related changes.

## Breaking Changes

None in this version. For migration history and breakage details, see
[BREAKING_CHANGES.md](BREAKING_CHANGES.md).

## Full History

For the complete canonical history, see [CHANGELOG.md](CHANGELOG.md).
