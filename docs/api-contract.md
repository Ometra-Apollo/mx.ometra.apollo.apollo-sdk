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

## Error Pages

Apollo registers suite-styled HTML fallback pages for Laravel HTTP errors
`401`, `403`, `404`, `419`, `429`, `500`, and `503`. The SDK appends its
`resources/views` directory after the host app view paths, so
`resources/views/errors/{status}.blade.php` in the host application always
wins.

Disable the fallback with:

```env
APOLLO_ERROR_PAGES_ENABLED=false
```

Publish customizable copies with:

```bash
php artisan vendor:publish --tag=apollo-error-pages
```

The aggregate `apollo` publish tag includes both `config/apollo.php` and the
error pages.

## Authentication

Every request is application- or group-authenticated through Caronte:

| Header                | Required    | Source                                                                      |
| --------------------- | ----------- | --------------------------------------------------------------------------- |
| `X-Group-Token`       | Conditional | Sent when Caronte is configured for a group application.                    |
| `X-Application-Token` | Conditional | Sent when no group application is configured; never sent with a group token. |
| `X-Tenant-Id`         | Conditional | Current BeeHive `TenantContext`, when one is active.                         |
| `X-User-Token`        | Conditional | Current Caronte user token when the operation must run as a user.           |

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

| Domain             | Active routes | Current SDK coverage | Missing wrappers |
| ------------------ | ------------: | -------------------: | ---------------- |
| Categories         |             5 |                    5 | None             |
| Directories        |             6 |                    6 | None             |
| Presets            |             5 |                    5 | None             |
| Media              |            14 |                   14 | None             |
| Metadata           |             7 |                    7 | None             |
| Flare stations     |             2 |                    2 | None             |
| Pulse groups       |             1 |                    1 | None             |
| Ignis campaigns    |             1 |                    1 | None             |
| Ignis content hits |             1 |                    1 | None             |

## Categories

| Apollo resource action                                             | HTTP method | URI                    | Auth | Notes                                      |
| ------------------------------------------------------------------ | ----------- | ---------------------- | ---- | ------------------------------------------ |
| `Apollo::proteus()->categories()->index(array $query = [])`        | `GET`       | `/api/categories`      | App  | Query: `filter`, `items_per_page`, `page`. |
| `Apollo::proteus()->categories()->store(array $data)`              | `POST`      | `/api/categories`      | App  | Payload: `key`, `name`.                    |
| `Apollo::proteus()->categories()->show(string $id_category)`                | `GET`       | `/api/categories/{id}` | App  | Tenant-scoped.                             |
| `Apollo::proteus()->categories()->update(string $id_category, array $data)` | `PUT`       | `/api/categories/{id}` | App  | Payload: `name`.                           |
| `Apollo::proteus()->categories()->delete(string $id_category)`              | `DELETE`    | `/api/categories/{id}` | App  | Fails if category has media.               |

## Directories

| Apollo resource action                                               | HTTP method | URI                                    | Auth       | Permission                                          |
| -------------------------------------------------------------------- | ----------- | -------------------------------------- | ---------- | --------------------------------------------------- |
| `Apollo::proteus()->directories()->index(array $query = [])`         | `GET`       | `/api/directories`                     | App + user | Current uploader context.                           |
| `Apollo::proteus()->directories()->create(?string $id_parent = null)` | `GET`       | `/api/directories/create/{parent_id?}` | App + user | Metadata helper for create forms/API clients.       |
| `Apollo::proteus()->directories()->store(array $data)`               | `POST`      | `/api/directories`                     | App + user | Creates directory under current uploader or parent. |
| `Apollo::proteus()->directories()->show(string $id_directory)`                 | `GET`       | `/api/directories/{id}`                | App + user | Requires `READ`.                                    |
| `Apollo::proteus()->directories()->update(string $id_directory, array $data)`  | `PUT`       | `/api/directories/{id}`                | App + user | Requires `WRITE`.                                   |
| `Apollo::proteus()->directories()->delete(string $id_directory)`               | `DELETE`    | `/api/directories/{id}`                | App + user | Requires `DELETE`.                                  |

## Presets

| Apollo resource action                                                                     | HTTP method | URI                                         | Auth       | Permission        |
| ------------------------------------------------------------------------------------------ | ----------- | ------------------------------------------- | ---------- | ----------------- |
| `Apollo::proteus()->presets()->index(string $id_directory)`                                 | `GET`       | `/api/directories/{id}/presets`             | App + user | Requires `READ`.  |
| `Apollo::proteus()->presets()->store(string $id_directory, array $data)`                    | `POST`      | `/api/directories/{id}/presets`             | App + user | Requires `WRITE`. |
| `Apollo::proteus()->presets()->show(string $id_directory, string $id_preset)`                | `GET`       | `/api/directories/{id}/presets/{preset_id}` | App + user | Requires `READ`.  |
| `Apollo::proteus()->presets()->update(string $id_directory, string $id_preset, array $data)` | `PUT`       | `/api/directories/{id}/presets/{preset_id}` | App + user | Requires `WRITE`. |
| `Apollo::proteus()->presets()->delete(string $id_directory, string $id_preset)`              | `DELETE`    | `/api/directories/{id}/presets/{preset_id}` | App + user | Requires `WRITE`. |

## Media

| Apollo resource action                                                        | HTTP method | URI                                       | Auth       | Permission                                          |
| ----------------------------------------------------------------------------- | ----------- | ----------------------------------------- | ---------- | --------------------------------------------------- |
| `Apollo::proteus()->media()->index(array $query = [])`                        | `GET`       | `/api/media`                              | App + user | Tenant-scoped list.                                 |
| `Apollo::proteus()->media()->upload(array $data)`                             | `POST`      | `/api/media`                              | App + user | Multipart-capable upload.                           |
| `Apollo::proteus()->media()->create()`                                        | `GET`       | `/api/media/create`                       | App + user | Upload metadata helper.                             |
| `Apollo::proteus()->media()->tags()`                                          | `GET`       | `/api/media/tags`                         | App + user | Tenant tag list.                                    |
| `Apollo::proteus()->media()->show(string $id_media)`                                | `GET`       | `/api/media/{id}`                         | App + user | Requires `READ`.                                    |
| `Apollo::proteus()->media()->delete(string $id_media)`                              | `DELETE`    | `/api/media/{id}`                         | App + user | Requires `DELETE`.                                  |
| `Apollo::proteus()->media()->availableFormats(string $id_media)`                    | `GET`       | `/api/media/{id}/available-formats`       | App + user | Requires `READ`.                                    |
| `Apollo::proteus()->media()->setDefaultFormat(string $id_media, array $data)`       | `POST`      | `/api/media/{id}/available-formats`       | App + user | Requires `WRITE`.                                   |
| `Apollo::proteus()->media()->download(string $id_media, ?string $ext = null)`       | `GET`       | `/api/media/{id}/download`                | App + user | Requires `READ`.                                    |
| `Apollo::proteus()->media()->thumbnail(string $id_media)`                           | `GET`       | `/api/media/{id}/download?ext=thumb`      | App + user | Requires `READ`; thumbnail download response.       |
| `Apollo::proteus()->media()->lightPathUrl(string $id_media, array $options = [])`   | `POST`      | `/api/media/{id}/lightpath-url`           | App + user | Requires `READ`; payload: `ext`, `url_ttl_seconds`. |
| `Apollo::proteus()->lightPath()->extendGrant(string $uuid, int $ttl)`               | `PATCH`     | `/api/lightpath/grants/{uuid}/extend`     | User/App   | Owner or valid same-tenant AppToken/GroupToken.     |
| `Apollo::proteus()->lightPath()->deleteGrant(string $uuid)`                         | `DELETE`    | `/api/lightpath/grants/{uuid}`            | User/App   | Owner or valid same-tenant AppToken/GroupToken.     |
| `Apollo::proteus()->media()->transformationOptions(string $id_media)`               | `GET`       | `/api/media/{id}/request-transformations` | App + user | Requires `READ`.                                    |
| `Apollo::proteus()->media()->requestTransformations(string $id_media, array $data)` | `POST`      | `/api/media/{id}/request-transformations` | App + user | Requires `WRITE`.                                   |
| `Apollo::proteus()->media()->setMetadata(string $id_media, array $data)`            | `POST`      | `/api/media/{id}/set-metadata`            | App + user | Requires `WRITE`.                                   |
| `Apollo::proteus()->media()->storeTags(string $id_media, array $data)`              | `POST`      | `/api/media/{id}/tags/store`              | App + user | Requires `WRITE`.                                   |

### LightPath media URLs

`lightPathUrl()` asks Proteus to mint an opaque CDN URL for a media asset:

```php
$response = Apollo::proteus()->media()->lightPathUrl($id_media, [
    'ext' => 'mp4',
    'url_ttl_seconds' => 3600,
]);
```

Payload fields:

| Field             | Type    | Required | Notes |
| ----------------- | ------- | -------- | ----- | -------------------------------------------------------------------------------- |
| `ext`             | `string | null`    | No    | Requested completed format. When omitted, Proteus uses the media default format. |
| `url_ttl_seconds` | `int    | null`    | No    | URL access TTL. Proteus applies its configured default and maximum.              |

Response data:

| Field            | Type     | Notes                                                          |
| ---------------- | -------- | -------------------------------------------------------------- |
| `id_lightpath_grant` | `string` | UUID used to extend or delete the grant.                       |
| `url`                | `string` | Public URL, usually `https://lightpath.example.com/m/{token}`. |
| `url_expires_at`     | `string` | Access expiration for the opaque token.                        |
| `renewable_from`     | `string` | Start of the renewal window.                                   |
| `renewable_until`    | `string` | End of the post-expiration renewal grace period.               |
| `id_media`           | `string` | Proteus media id.                                              |
| `format`             | `string` | Resolved delivery format.                                      |

The SDK does not expose LightPath node APIs. Nodes validate tokens and fetch
origin bytes directly from Proteus.

## Metadata

| Apollo resource action                                                     | HTTP method | URI                                | Auth       | Permission                     |
| -------------------------------------------------------------------------- | ----------- | ---------------------------------- | ---------- | ------------------------------ |
| `Apollo::proteus()->metadata()->keys(string $key)`                         | `GET`       | `/api/media/metadata/{key}`        | App        | Tenant metadata key lookup.    |
| `Apollo::proteus()->metadata()->values(string $key)`                       | `GET`       | `/api/media/metadata/{key}/values` | App        | Tenant metadata values lookup. |
| `Apollo::proteus()->metadata()->index(string $id_media, array $query = [])` | `GET`       | `/api/media/{id}/metadata`         | App + user | Requires `READ`.               |
| `Apollo::proteus()->metadata()->store(string $id_media, array $data)`       | `POST`      | `/api/media/{id}/metadata`         | App + user | Requires `WRITE`.              |
| `Apollo::proteus()->metadata()->update(string $id_media, array $data)`      | `PUT`       | `/api/media/{id}/metadata`         | App + user | Requires `WRITE`.              |
| `Apollo::proteus()->metadata()->show(string $id_media, string $key)`        | `GET`       | `/api/media/{id}/metadata/{key}`   | App + user | Requires `READ`.               |
| `Apollo::proteus()->metadata()->delete(string $id_media, string $key)`      | `DELETE`    | `/api/media/{id}/metadata/{key}`   | App + user | Requires `WRITE`.              |

## Flare Stations

| Apollo resource action                                  | HTTP method | URI                  | Auth | Notes                  |
| ------------------------------------------------------- | ----------- | -------------------- | ---- | ---------------------- |
| `Apollo::flare()->stations()->index(array $query = [])` | `GET`       | `/api/stations`      | App  | Query passthrough.     |
| `Apollo::flare()->stations()->show(string $id_station)`         | `GET`       | `/api/stations/{id}` | App  | Station detail lookup. |

## Pulse Groups

| Apollo resource action                                | HTTP method | URI                 | Auth | Notes                                                             |
| ----------------------------------------------------- | ----------- | ------------------- | ---- | ----------------------------------------------------------------- |
| `Apollo::pulse()->groups()->index(array $query = [])` | `GET`       | `/api/ignis/groups` | App  | Bridge endpoint exposed from Pulse module by product requirement. |

## Ignis Campaigns

| Apollo resource action | HTTP method | URI | Auth | Notes |
| --- | --- | --- | --- | --- |
| `Apollo::ignis()->campaigns()->byExternalGroup(string $id_externalGroup)` | `GET` | `/api/external-groups/{externalGroupId}/campaigns` | App | Returns the unwrapped `data` array from Ignis, without the `status/message/errors` envelope. |
| `Apollo::ignis()->campaigns()->show(string $id_externalGroup, int $id_campaign)` | `GET` | `/api/external-groups/{externalGroupId}/campaigns/{campaignId}` | App | Returns the unwrapped campaign detail from Ignis, without the `status/message/errors` envelope. |

## Ignis Content Hits

| Apollo resource action                                  | HTTP method | URI                 | Auth | Notes                                                    |
| ------------------------------------------------------- | ----------- | ------------------- | ---- | -------------------------------------------------------- |
| `Apollo::ignis()->contentHits()->report(array $report)` | `POST`      | `/api/content-hits` | App  | Migrated from `hitReport(...)` in `ometra-ignis-client`. |

## Ignis Groups Exposure (Inbound)

Apollo SDK is outbound-only by default. When the host enables the opt-in `ignis_groups` config block, the SDK registers an INBOUND route on the host application that exposes the host's groups to external clients (including Pulse's outbound `groups()->index()`). No outbound HTTP to Ignis is added by this surface.

### Route

| HTTP method | URI (default)       | Configurable prefix                | Auth                                  | Enabled by default                                     |
| ----------- | ------------------- | ---------------------------------- | ------------------------------------- | ------------------------------------------------------ |
| `GET`       | `/api/ignis/groups` | `apollo.ignis_groups.route_prefix` | `caronte.application:tenant_required` | No (`apollo.ignis_groups.enabled` defaults to `false`) |

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

| Field            | Type         | Notes                                                             |
| ---------------- | ------------ | ----------------------------------------------------------------- |
| `name`           | string       | Group display name.                                               |
| `external_id`    | string       | Host-external group identifier.                                   |
| `media_type`     | string[]     | Normalized to `MediaTypeEnum` values (`video`, `audio`, `image`). |
| `provider_id`    | string       | Auto-set to `Str::slug(config('app.name'))`.                      |
| `play_modifiers` | object\|null | Optional playback modifiers. Absent when `null`.                  |

### Authentication

The route is protected by the `caronte.application:tenant_required` middleware alias. Requests without valid tenant credentials are rejected by Caronte (typically `401`/`403` per Caronte behavior) before reaching the controller.

### Configuration

All keys live under `apollo.ignis_groups` in `config/apollo.php`:

| Key | Type | Default | Description |
| --- | --- | --- | --- |
| `enabled` | bool | `env('APOLLO_IGNIS_GROUPS_ENABLED', false)` | Enables the inbound groups route. `false` = no route registered. |
| `route_prefix` | string | `api/ignis` | URI prefix for the route. Override to mount under a custom path (e.g. `api/custom/ignis`). |
| `middleware` | string[] | `['caronte.application:tenant_required']` | Route middleware stack. Override to add/remove middleware. |

### Host Responsibility

When `APOLLO_IGNIS_GROUPS_ENABLED=true`, the host MUST bind `Ometra\Apollo\Sdk\Contracts\IgnisGroupContract` to its concrete group provider. The SDK fails at boot when the route is enabled without that binding, so it does not expose dummy data by accident.

```php
use App\Services\HostGroupProvider;
use Ometra\Apollo\Sdk\Contracts\IgnisGroupContract;

$this->app->bind(IgnisGroupContract::class, HostGroupProvider::class);
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
3. Builds application or group authentication through Caronte's inherited helpers.
4. Uses Caronte's inherited application, user, raw-response, and multipart behavior.
