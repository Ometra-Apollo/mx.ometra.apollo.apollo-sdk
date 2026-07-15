# Release v4.0.0

## Motivation / Context

This major release aligns Apollo with Caronte SDK 7.1, corrects the public
Proteus metadata contract, and hardens shared frontend components.

## Summary of changes

- Upgraded the transport contract to `ometra/caronte-sdk ^7.1.0`.
- Corrected `MetadataResource::index()` to target media-scoped metadata.
- Enforced mutually exclusive group/application authentication headers.
- Removed unused direct runtime dependencies and the production dummy fixture.
- Stabilized the internal `/_apollo` frontend route contract.
- Improved AppMenu navigation and DirectoryTree error feedback.
- Updated README, API contract, changelog, release notes, and migration guidance.

## Testing checklist

- [x] `composer validate --no-check-publish` passes locally.
- [x] `composer test` passes: 121 tests, 444 assertions.
- [x] Documentation matches the current API and authentication contract.
- [x] Breaking changes and migration steps are documented.

## Risk / Impact

- Medium risk: authentication and transport now follow Caronte 7.1 directly.
- High migration impact for callers of `metadata()->index()` and consumers of
  `base_url_env` or `apollo.frontend.route_prefix`.

## Links

- CHANGELOG: `CHANGELOG.md`
- Breaking changes: `BREAKING_CHANGES.md`
- Release notes: `RELEASE_NOTES.md`
