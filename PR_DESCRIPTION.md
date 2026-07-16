# Release v4.1.0

## Motivation / Context

This feature release adds first-class Proteus LightPath grant management.

## Summary of changes

- Added `Apollo::proteus()->lightPath()` as the LightPath grant resource.
- Added helpers for extending and deleting grants by UUID.
- Supported user, group, and application authentication contexts.
- Documented grant IDs, expiration, and renewal windows.
- Added route, API-shape, and authentication coverage.

## Testing checklist

- [x] `composer validate --strict` passes locally.
- [x] `composer test` passes locally: 125 tests, 454 assertions.
- [x] Documentation matches the current API and authentication contract.
- [x] No breaking changes are introduced.

## Risk / Impact

- Low risk: the release adds a standalone resource and does not alter existing
  resource behavior.
- No migration is required for existing callers.

## Links

- CHANGELOG: `CHANGELOG.md`
- Breaking changes: `BREAKING_CHANGES.md`
- Release notes: `RELEASE_NOTES.md`
