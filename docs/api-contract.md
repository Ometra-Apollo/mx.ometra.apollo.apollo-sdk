# Apollo SDK API Contract

Package: `ometra/apollo-sdk`. Configuration file: `config/apollo.php`.

Apollo exposes modules through `Apollo::proteus()`, `Apollo::pulse()`, `Apollo::flare()`, and `Apollo::ignis()`.
Proteus, Pulse, Flare, and Ignis ship concrete resources in this release.

Apollo is outbound-only by default. When the host opts in via `apollo.ignis_groups.enabled`, the SDK also EXPOSES an inbound `GET /{prefix}/groups` route on the host application so external clients (including Pulse) can discover the host's groups. See [Ignis Groups Exposure (Inbound)](#ignis-groups-exposure-inbound).

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

### Application-only mode

Some endpoints (e.g. `Categories`, `Metadata keys/values`, `Flare stations`) already use application-only authentication and never send `X-User-Token`. Other endpoints normally run as the current user, which requires an active Caronte session.

When you need to call user-scoped endpoints from a background job or any context without a user session, switch the module to application-only mode with `asApplication()`:

```php
$proteus = Apollo::proteus()->asApplication();
$proteus->media()->index(['type' => 'audio']);
```

In this mode, every call that would normally use `userRequest()` is transparently downgraded to `applicationRequest()`. The module instance is immutable: `asApplication()` returns a clone and leaves the original untouched.

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
| `Apollo::ignis()->campaigns()->show(string $id)` | `GET` | `/api/campaigns/{id}` | App | Campaign detail lookup for Pulse IoT campaign detail endpoint. |

## Ignis Content Hits

| Apollo resource action | HTTP method | URI | Auth | Notes |
| --- | --- | --- | --- | --- |
| `Apollo::ignis()->contentHits()->report(array $report)` | `POST` | `/api/content-hits` | App | Migrated from `hitReport(...)` in `ometra-ignis-client`. |

## Ignis Groups Exposure (Inbound)

Apollo SDK is outbound-only by default. When the host enables the opt-in `ignis_groups` config block, the SDK registers an INBOUND route on the host application that exposes the host's groups to external clients (including Pulse's outbound `groups()->index()`). No outbound HTTP to Ignis is added by this surface.

### Route

| HTTP method | URI (default) | Configurable prefix | Auth | Enabled by default |
| --- | --- | --- | --- | --- |
| `GET` | `/api/ignis/groups` | `apollo.ignis_groups.route_prefix` | `caronte.application:tenant_required` | No (`apollo.ignis_groups.enabled` defaults to `false`) |

The route is registered only when `apollo.ignis_groups.enabled` is `true`. With the default config (`false`), no groups route is registered and `GET /api/ignis/groups` returns `404`.

### Response

The response body is a raw JSON array (no wrapper) of `ExternalGroupDTO::toArray()` shapes. `play_modifiers` is omitted from each entry when `null`.

```json
[
    {
        "name": "Test Group",
        "external_id": "test_external_id",
        "media_type": ["video"],
        "provider_id": "my-app"
    }
]
```

| Field | Type | Notes |
| --- | --- | --- |
| `name` | string | Group display name. |
| `external_id` | string | Host-external group identifier. |
| `media_type` | string[] | Normalized to `MediaTypeEnum` values (`video`, `audio`, `image`). |
| `provider_id` | string | Auto-set to `Str::slug(config('app.name'))`. |
| `play_modifiers` | object\|null | Optional playback modifiers. Absent when `null`. |

### Authentication

The route is protected by the `caronte.application:tenant_required` middleware alias. Requests without valid tenant credentials are rejected by Caronte (typically `401`/`403` per Caronte behavior) before reaching the controller.

### Configuration

All keys live under `apollo.ignis_groups` in `config/apollo.php`:

| Key | Type | Default | Description |
| --- | --- | --- | --- |
| `enabled` | bool | `env('APOLLO_IGNIS_GROUPS_ENABLED', false)` | Enables the inbound groups route. `false` = no route registered. |
| `implementation` | class-string | `env('APOLLO_IGNIS_GROUPS_IMPLEMENTATION', \Ometra\Apollo\Sdk\Test\DummyGroup::class)` | Concrete `IgnisGroupContract` implementation. Must implement the contract or the provider throws `RuntimeException` at boot. |
| `route_prefix` | string | `api/ignis` | URI prefix for the route. Override to mount under a custom path (e.g. `api/custom/ignis`). |
| `middleware` | string[] | `['caronte.application:tenant_required']` | Route middleware stack. Override to add/remove middleware. |

### Host Responsibility

The host MUST provide a concrete implementation of `Ometra\Apollo\Sdk\Contracts\IgnisGroupContract` and set `apollo.ignis_groups.implementation` to its class string. The SDK ships `Ometra\Apollo\Sdk\Test\DummyGroup` as a runnable default that returns one test group; override it via the `APOLLO_IGNIS_GROUPS_IMPLEMENTATION` env var or in the host config to expose real groups.

```php
// config/apollo.php (host override)
'ignis_groups' => [
    'enabled' => true,
    'implementation' => \App\Services\HostGroupProvider::class,
],
```

```php
namespace App\Services;

use Ometra\Apollo\Sdk\Contracts\IgnisGroupContract;
use Ometra\Apollo\Sdk\DTO\ExternalGroupDTO;

final class HostGroupProvider implements IgnisGroupContract
{
    public function getGroups(): array
    {
        return [
            ExternalGroupDTO::fromArray([
                'name' => 'Host group',
                'external_id' => 'host-1',
                'media_type' => ['video', 'audio'],
                'play_modifiers' => ['frequency' => 2],
            ]),
        ];
    }
}
```

## SDK HTTP Client

Apollo SDK uses a shared HTTP client adapter built on the installed
`ometra/caronte-sdk` HTTP client support. The adapter:

1. Extends `CaronteHttpClient`.
2. Resolves module base URLs from `config/apollo.php`.
3. Builds `X-Application-Token` through Caronte's inherited helpers.
4. Uses Caronte's inherited `applicationRequest()` and `userRequest()` header behavior.
