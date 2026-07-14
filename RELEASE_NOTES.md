# Release v3.7.0 "Astra"

**Date:** 2026-07-13  
**Branch:** `main`  
**Package:** `ometra/apollo-sdk`

Apollo SDK enhancement release adding Proteus media thumbnail access and
clarifying LightPath media URL usage in the public contract.

## Summary

This release extends the Proteus media resource with direct thumbnail retrieval
support and updates the public documentation so integrators can reliably
request LightPath URLs for media assets.

## Highlights

- Added `MediaResource::thumbnail(string $id)` for retrieving thumbnail images.
- Documented `Apollo::proteus()->media()->lightPathUrl()` payload and response
  contract in `docs/api-contract.md`.
- Updated `README.md` with LightPath media URL usage examples.

## Added

- `MediaResource::thumbnail(string $id)` helper.
- LightPath media URL and thumbnail documentation in `README.md`.
- API contract coverage for LightPath URL request payload and response fields in
  `docs/api-contract.md`.

## Changed

- Expanded `docs/api-contract.md` to detail LightPath media URL behavior.

## Fixed

- No bug fixes in this release.

## Removed

- Nothing removed.

## Deprecated

- Nothing deprecated.

## Security

- No security-related changes.

## Breaking Changes

None in this release.

## Links

- Full history: `CHANGELOG.md`
- Migration guidance: `BREAKING_CHANGES.md`
