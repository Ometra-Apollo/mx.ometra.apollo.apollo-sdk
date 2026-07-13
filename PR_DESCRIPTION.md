# Release v3.7.0 - Astra

## Motivation / Context

This release ships a new Proteus media thumbnail helper for Apollo and improves
LightPath media URL documentation so integrators can request thumbnails and
opaque delivery URLs with confidence.

## Summary of changes

- Added `MediaResource::thumbnail(string $id)` for thumbnail retrieval.
- Updated `README.md` with LightPath media URL and thumbnail usage examples.
- Updated `docs/api-contract.md` with thumbnail and LightPath URL request/response
  documentation.
- Updated `CHANGELOG.md` with the `v3.7.0` release entry.
- Updated `BREAKING_CHANGES.md` to add a `v3.7.0` section confirming there are
  no breaking changes.
- Updated `RELEASE_NOTES.md` for the `v3.7.0` release.
- Added route coverage in
  `tests/Unit/Modules/Proteus/ProteusResourceRoutesTest.php` for the new
  `thumbnail()` helper.

## Testing checklist

- [ ] `phpunit` passes locally.
- [ ] Documentation updates are correct and coherent.
- [ ] `CHANGELOG.md` accurately reflects the release.
- [ ] No breaking changes were introduced.

## Risk / Impact

- Low risk: this release adds backward-compatible helper functionality and
  documentation only.
- No public API removal or behavioral breaking changes are included.

## Links

- CHANGELOG: `CHANGELOG.md`
- Breaking changes: `BREAKING_CHANGES.md`
- Release notes: `RELEASE_NOTES.md`
