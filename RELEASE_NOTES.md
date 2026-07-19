# Release v4.3.0

**Date:** 2026-07-19
**Branch:** `main`
**Package:** `ometra/apollo-sdk`

Apollo SDK 4.3 adds application-authenticated group coordination between Pulse
and Flare while retaining the complete 4.x public API.

## Highlights

- Added Pulse group catalog and station-cache invalidation operations.
- Added Flare station group lookup, assignment, detachment, and cache invalidation operations.
- Added repeatable Pint and PHPStan quality gates.
- Verified SDK usage across Aeris, Flare, Ignis, Proteus, and Pulse.

## Upgrade requirements

- Require `ometra/apollo-sdk ^4.3` after the release tag is available.
- No breaking changes or configuration changes are required.

## Validation

- `composer validate --strict`: valid
- `composer run lint`: pass
- `composer run analyse`: pass
- `composer test`: 133 tests and 485 assertions pass

## Links

- Full API contract: `docs/api-contract.md`
- Consumer audit: `docs/integration-audit.md`

# Release v4.1.0

**Date:** 2026-07-16
**Branch:** `main`
**Package:** `ometra/apollo-sdk`

Apollo SDK 4.1 adds first-class management of Proteus LightPath grants. Callers
can extend a grant's URL lifetime or delete it using its
`id_lightpath_grant`, including from application authentication contexts.

## Highlights

- Added `Apollo::proteus()->lightPath()->extendGrant($grantId, $ttl)`.
- Added `Apollo::proteus()->lightPath()->deleteGrant($grantId)`.
- Documented grant IDs, expiration, and renewal-window response fields.
- Added route, API-shape, and application-authentication test coverage.

## Upgrade requirements

- Require `ometra/apollo-sdk ^4.1`.
- No breaking changes or configuration changes are required.

## Validation

- `composer validate --strict`: valid
- `composer test`: 125 tests and 454 assertions pass

## Links

- Full history: `CHANGELOG.md`

# Release v4.0.0

**Date:** 2026-07-15
**Branch:** `main`
**Package:** `ometra/apollo-sdk`

Apollo 4 delegates raw downloads, multipart uploads, authentication headers,
tenant propagation, retries, and response parsing to Caronte SDK 7.1.
Applications configured with a group now send only `X-Group-Token`; other
applications send `X-Application-Token`.

## Highlights

- Corrected Proteus metadata listing to use the media-scoped endpoint.
- Hardened `DirectoryTree` loading and folder-creation error handling.
- Made `AppMenu` react to Inertia URL changes and centralized app URL building.
- Fixed `ExternalGroupDTO` serialization so falsey non-null values are kept.
- Removed unused AWS S3, database, and Guzzle runtime dependencies.
- Removed the production `DummyGroup` fixture.

## Upgrade requirements

- Require `ometra/apollo-sdk ^4.0` and allow `ometra/caronte-sdk ^7.1`.
- Update metadata listing calls to pass a media ID:

```php
$metadata = Apollo::proteus()->metadata()->index($mediaId, [
    'search' => 'title',
]);
```

- Stop reading `base_url_env` from module `config()` results; use `base_url`.
- Remove `apollo.frontend.route_prefix` overrides. Published frontend components
  and SDK web routes use the fixed `/_apollo` prefix.
- If the Ignis groups route is enabled, bind `IgnisGroupContract` in the host;
  no dummy implementation is shipped.

Module URLs remain configured through `config/apollo.php`:

```dotenv
PROTEUS_BASE_URL=https://proteus.example.com/api
PULSE_BASE_URL=https://pulse.example.com/api
FLARE_BASE_URL=https://flare.example.com/api
IGNIS_BASE_URL=https://ignis.example.com/api
```

Existing modular calls such as `Apollo::proteus()->media()->index()` are
unchanged.

## Validation

- `composer validate --no-check-publish`
- `composer test`: 121 tests and 444 assertions pass

## Links

- Full history: `CHANGELOG.md`
- Migration guidance: `BREAKING_CHANGES.md`

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
