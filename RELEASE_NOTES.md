# Release v3.3.0 "Artemis"

**Date:** 2026-06-13  
**Branch:** `main`  
**Package:** `ometra/apollo-sdk`

## Summary

This release adds direct campaign detail retrieval in the Ignis module and updates
the SDK contract documentation accordingly. It is a backward-compatible feature
release focused on route completeness and test-backed behavior. Configuration
continues to be driven by `config/apollo.php` for module base URLs.

## Highlights

- Added `Apollo::ignis()->campaigns()->show($id)` for campaign detail lookup.
- Expanded route-level unit coverage for Ignis campaign endpoints.
- Updated the public API contract to reflect the new campaign detail route.

Module URLs remain configured through `config/apollo.php` using:

- `PROTEUS_BASE_URL`
- `PULSE_BASE_URL`
- `FLARE_BASE_URL`
- `IGNIS_BASE_URL`

## Added

- `CampaignsResource::show(string $id)`:
  calls `GET /api/campaigns/{id}` using application authentication.
- `IgnisResourceRoutesTest::testCampaignShowUsesExpectedEndpoint()` to validate
  route mapping and request shape.

Contextual resource usage remains unchanged, for example:

```php
$images = Apollo::proteus()->media()->index(['type' => 'image']);
```

## Changed

- `docs/api-contract.md` now documents the campaign detail endpoint under
  Ignis Campaigns.

## Fixed

- No explicit bug fix changes in this release.

## Removed

- Nothing removed.

## Deprecated

- Nothing deprecated.

## Security

- No security-related changes.

## Breaking Changes

None in this version. For complete migration history, see
[BREAKING_CHANGES.md](BREAKING_CHANGES.md).

## Full History

For full project history and canonical release details, see
[CHANGELOG.md](CHANGELOG.md).
