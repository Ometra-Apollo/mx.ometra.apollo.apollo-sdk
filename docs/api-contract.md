# Apollo SDK API Contract

Package: `ometra/apollo-sdk`. Configuration file: `config/apollo.php`.

Apollo exposes modules through `Apollo::proteus()`, `Apollo::pulse()`, `Apollo::flare()`, and `Apollo::ignis()`.
Proteus, Pulse, Flare, and Ignis ship concrete resources in this release.

Required module URL environment variables:

- `PROTEUS_BASE_URL`
- `PULSE_BASE_URL`
- `FLARE_BASE_URL`
- `IGNIS_BASE_URL`

Example:

```php
use Ometra\Apollo\Sdk\Facades\Apollo;

$media = Apollo::proteus()->media()->index(['type' => 'image']);
```

## Authentication

Every request must be application-authenticated through Caronte:

| Header | Required | Source |
| --- | --- | --- |
| `X-Application-Token` | Yes | `caronte-sdk` application token. |
| `X-Tenant-Id` | Yes | Current BeeHive `TenantContext`. |
| `X-User-Token` | Conditional | Current Caronte user token when the operation must run as a user. |

`uri_user` is not part of the API contract. Proteus ignores it as a request input.
User-authenticated SDK calls send `X-Tenant-Id` from the active BeeHive `TenantContext`.
Uploads with `UploadedFile` payloads are sent as multipart requests.

## SDK Coverage

| Domain | Active routes | Current SDK coverage | Missing wrappers |
| --- | ---: | ---: | --- |
| Categories | 5 | 5 | None |
| Directories | 6 | 6 | None |
| Presets | 5 | 5 | None |
| Media | 13 | 13 | None |
| Metadata | 7 | 7 | None |
| Flare stations | 2 | 2 | None |
| Pulse groups | 1 | 1 | None |
| Ignis campaigns | 1 | 1 | None |
| Ignis content hits | 1 | 1 | None |

## Categories

| Apollo resource action | HTTP method | URI | Auth | Notes |
| --- | --- | --- | --- | --- |
| `Apollo::proteus()->categories()->index(array $query = [])` | `GET` | `/api/categories` | App | Query: `filter`, `items_per_page`, `page`. |
| `Apollo::proteus()->categories()->store(array $data)` | `POST` | `/api/categories` | App | Payload: `key`, `name`. |
| `Apollo::proteus()->categories()->show(string $id)` | `GET` | `/api/categories/{id}` | App | Tenant-scoped. |
| `Apollo::proteus()->categories()->update(string $id, array $data)` | `PUT` | `/api/categories/{id}` | App | Payload: `name`. |
| `Apollo::proteus()->categories()->delete(string $id)` | `DELETE` | `/api/categories/{id}` | App | Fails if category has media. |

## Directories

| Apollo resource action | HTTP method | URI | Auth | Permission |
| --- | --- | --- | --- | --- |
| `Apollo::proteus()->directories()->index(array $query = [])` | `GET` | `/api/directories` | App + user | Current uploader context. |
| `Apollo::proteus()->directories()->create(?string $parentId = null)` | `GET` | `/api/directories/create/{parent_id?}` | App + user | Metadata helper for create forms/API clients. |
| `Apollo::proteus()->directories()->store(array $data)` | `POST` | `/api/directories` | App + user | Creates directory under current uploader or parent. |
| `Apollo::proteus()->directories()->show(string $id)` | `GET` | `/api/directories/{id}` | App + user | Requires `READ`. |
| `Apollo::proteus()->directories()->update(string $id, array $data)` | `PUT` | `/api/directories/{id}` | App + user | Requires `WRITE`. |
| `Apollo::proteus()->directories()->delete(string $id)` | `DELETE` | `/api/directories/{id}` | App + user | Requires `DELETE`. |

## Presets

| Apollo resource action | HTTP method | URI | Auth | Permission |
| --- | --- | --- | --- | --- |
| `Apollo::proteus()->presets()->index(string $directoryId)` | `GET` | `/api/directories/{id}/presets` | App + user | Requires `READ`. |
| `Apollo::proteus()->presets()->store(string $directoryId, array $data)` | `POST` | `/api/directories/{id}/presets` | App + user | Requires `WRITE`. |
| `Apollo::proteus()->presets()->show(string $directoryId, string $presetId)` | `GET` | `/api/directories/{id}/presets/{preset_id}` | App + user | Requires `READ`. |
| `Apollo::proteus()->presets()->update(string $directoryId, string $presetId, array $data)` | `PUT` | `/api/directories/{id}/presets/{preset_id}` | App + user | Requires `WRITE`. |
| `Apollo::proteus()->presets()->delete(string $directoryId, string $presetId)` | `DELETE` | `/api/directories/{id}/presets/{preset_id}` | App + user | Requires `WRITE`. |

## Media

| Apollo resource action | HTTP method | URI | Auth | Permission |
| --- | --- | --- | --- | --- |
| `Apollo::proteus()->media()->index(array $query = [])` | `GET` | `/api/media` | App + user | Tenant-scoped list. |
| `Apollo::proteus()->media()->upload(array $data)` | `POST` | `/api/media` | App + user | Multipart-capable upload. |
| `Apollo::proteus()->media()->create()` | `GET` | `/api/media/create` | App + user | Upload metadata helper. |
| `Apollo::proteus()->media()->tags()` | `GET` | `/api/media/tags` | App + user | Tenant tag list. |
| `Apollo::proteus()->media()->show(string $id)` | `GET` | `/api/media/{id}` | App + user | Requires `READ`. |
| `Apollo::proteus()->media()->delete(string $id)` | `DELETE` | `/api/media/{id}` | App + user | Requires `DELETE`. |
| `Apollo::proteus()->media()->availableFormats(string $id)` | `GET` | `/api/media/{id}/available-formats` | App + user | Requires `READ`. |
| `Apollo::proteus()->media()->setDefaultFormat(string $id, array $data)` | `POST` | `/api/media/{id}/available-formats` | App + user | Requires `WRITE`. |
| `Apollo::proteus()->media()->download(string $id, ?string $ext = null)` | `GET` | `/api/media/{id}/download` | App + user | Requires `READ`. |
| `Apollo::proteus()->media()->transformationOptions(string $id)` | `GET` | `/api/media/{id}/request-transformations` | App + user | Requires `READ`. |
| `Apollo::proteus()->media()->requestTransformations(string $id, array $data)` | `POST` | `/api/media/{id}/request-transformations` | App + user | Requires `WRITE`. |
| `Apollo::proteus()->media()->setMetadata(string $id, array $data)` | `POST` | `/api/media/{id}/set-metadata` | App + user | Requires `WRITE`. |
| `Apollo::proteus()->media()->storeTags(string $id, array $data)` | `POST` | `/api/media/{id}/tags/store` | App + user | Requires `WRITE`. |

## Metadata

| Apollo resource action | HTTP method | URI | Auth | Permission |
| --- | --- | --- | --- | --- |
| `Apollo::proteus()->metadata()->keys(string $key)` | `GET` | `/api/media/metadata/{key}` | App | Tenant metadata key lookup. |
| `Apollo::proteus()->metadata()->values(string $key)` | `GET` | `/api/media/metadata/{key}/values` | App | Tenant metadata values lookup. |
| `Apollo::proteus()->metadata()->index(string $mediaId, array $query = [])` | `GET` | `/api/media/{id}/metadata` | App + user | Requires `READ`. |
| `Apollo::proteus()->metadata()->store(string $mediaId, array $data)` | `POST` | `/api/media/{id}/metadata` | App + user | Requires `WRITE`. |
| `Apollo::proteus()->metadata()->update(string $mediaId, array $data)` | `PUT` | `/api/media/{id}/metadata` | App + user | Requires `WRITE`. |
| `Apollo::proteus()->metadata()->show(string $mediaId, string $key)` | `GET` | `/api/media/{id}/metadata/{key}` | App + user | Requires `READ`. |
| `Apollo::proteus()->metadata()->delete(string $mediaId, string $key)` | `DELETE` | `/api/media/{id}/metadata/{key}` | App + user | Requires `WRITE`. |

## Flare Stations

| Apollo resource action | HTTP method | URI | Auth | Notes |
| --- | --- | --- | --- | --- |
| `Apollo::flare()->stations()->index(array $query = [])` | `GET` | `/api/stations` | App | Query passthrough. |
| `Apollo::flare()->stations()->show(string $id)` | `GET` | `/api/stations/{id}` | App | Station detail lookup. |

## Pulse Groups

| Apollo resource action | HTTP method | URI | Auth | Notes |
| --- | --- | --- | --- | --- |
| `Apollo::pulse()->groups()->index(array $query = [])` | `GET` | `/api/ignis/groups` | App | Bridge endpoint exposed from Pulse module by product requirement. |

## Ignis Campaigns

| Apollo resource action | HTTP method | URI | Auth | Notes |
| --- | --- | --- | --- | --- |
| `Apollo::ignis()->campaigns()->byExternalGroup(string $externalGroupId)` | `GET` | `/api/external-groups/{externalGroupId}/campaigns` | App | Migrated from `getCampaignsByExternalGroup(...)` in `ometra-ignis-client`. |

## Ignis Content Hits

| Apollo resource action | HTTP method | URI | Auth | Notes |
| --- | --- | --- | --- | --- |
| `Apollo::ignis()->contentHits()->report(array $report)` | `POST` | `/api/content-hits` | App | Migrated from `hitReport(...)` in `ometra-ignis-client`. |

## SDK HTTP Client

Apollo SDK uses a shared HTTP client adapter built on the installed
`ometra/caronte-sdk` HTTP client support. The adapter:

1. Extends `CaronteHttpClient`.
2. Resolves module base URLs from `config/apollo.php`.
3. Builds `X-Application-Token` through Caronte's inherited helpers.
4. Uses Caronte's inherited `applicationRequest()` and `userRequest()` header behavior.
