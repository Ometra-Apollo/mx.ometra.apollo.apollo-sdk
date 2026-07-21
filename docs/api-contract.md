# Contrato público de Apollo SDK v5

Paquete: `ometra/apollo-sdk`. Configuración: `config/apollo.php`.

## Convenciones

- `recurso()` representa una colección; `recurso($id)` representa una instancia.
- `index` lista, `store` crea, `show` consulta, `destroy` elimina.
- Las solicitudes de dominio usan `request`, `extend` y `revoke`.
- Los nombres PHP usan camelCase. El protocolo HTTP conserva snake_case.
- Todo método JSON devuelve el envelope de Caronte: `status`, `message`, `data` y `errors`.
- `download()` y `thumbnail()` devuelven `Illuminate\Http\Client\Response` sin parsear.
- No existen aliases de APIs anteriores ni parámetros de token.

Variables de URL:

```env
PROTEUS_BASE_URL=https://proteus.example.com/api
PULSE_BASE_URL=https://pulse.example.com/api
FLARE_BASE_URL=https://flare.example.com/api
IGNIS_BASE_URL=https://ignis.example.com/api
```

## Autenticación

Caronte genera `X-Application-Token` o `X-Group-Token`, incorpora `X-Tenant-Id` y obtiene `X-User-Token` del contexto activo cuando la operación requiere usuario. Apollo nunca recibe un token como argumento.

`asApplication()` obliga al módulo a usar autenticación de aplicación:

```php
Apollo::proteus()->asApplication()->media($mediaId)->show();
```

Proteus resuelve automáticamente el directory application grant aplicable al media para `show`, `download`, `thumbnail` y `lightPath()->request()`.

## Proteus

```php
Apollo::proteus()->media()->index($filters);
```

### Media y metadata

| API PHP | HTTP | URI | Retorno |
| --- | --- | --- | --- |
| `media()->index($filters)` | GET | `/api/media` | Envelope |
| `media()->store($data)` | POST | `/api/media` | Envelope |
| `media($id)->show()` | GET | `/api/media/{id}` | Envelope |
| `media($id)->destroy()` | DELETE | `/api/media/{id}` | Envelope |
| `media($id)->download($extension)` | GET | `/api/media/{id}/download?ext={extension}` | Response |
| `media($id)->thumbnail()` | GET | `/api/media/{id}/download?ext=thumb` | Response |
| `media()->metadata()->values($key)` | GET | `/api/media/metadata/{key}/values` | Envelope |
| `media($id)->metadata()->store($data)` | POST | `/api/media/{id}/metadata` | Envelope |
| `media($id)->metadata()->update($data)` | PUT | `/api/media/{id}/metadata` | Envelope |
| `media($id)->metadata($key)->show()` | GET | `/api/media/{id}/metadata/{key}` | Envelope |
| `media($id)->metadata($key)->destroy()` | DELETE | `/api/media/{id}/metadata/{key}` | Envelope |

`store()` de metadata combina valores editables; `update()` los reemplaza. `values()` devuelve valores únicos de una clave para filtros y autocompletado.

### LightPath

| API PHP | HTTP | URI | Payload HTTP |
| --- | --- | --- | --- |
| `media($id)->lightPath()->request($extension, $ttlSeconds)` | POST | `/api/media/{id}/lightpath-url` | `ext`, `url_ttl_seconds` |
| `lightPath($grantId)->extend($ttlSeconds)` | PATCH | `/api/lightpath/grants/{grantId}/extend` | `url_ttl_seconds` |
| `lightPath($grantId)->revoke()` | DELETE | `/api/lightpath/grants/{grantId}` | — |

`request()` devuelve sincrónicamente `id_lightpath_grant`, `url`, formato y vigencias dentro de `data`. En autenticación de aplicación, Proteus obtiene el grant de directorio por tenant, aplicación y cobertura del media.

### Directorios y application grants

| API PHP | HTTP | URI | Auth |
| --- | --- | --- | --- |
| `directories()->index($filters)` | GET | `/api/directories` | Usuario/App |
| `directories()->store($data)` | POST | `/api/directories` | Usuario/App |
| `directories($id)->show()` | GET | `/api/directories/{id}` | Usuario/App |
| `directories($id)->destroy()` | DELETE | `/api/directories/{id}` | Usuario/App |
| `directories($id)->applicationGrants()->request($clientReference, $permission)` | POST | `/api/directories/{id}/application-grants` | Usuario |
| `directories()->applicationGrants($grantId)->revoke()` | DELETE | `/api/directories/application-grants/{grantId}` | Usuario/App |

Los campos HTTP de solicitud son `client_reference` y `permission`. `DirectoryApplicationPermission` limita el permiso a los valores soportados por Proteus.

### Categorías

| API PHP | HTTP | URI |
| --- | --- | --- |
| `categories()->index($filters)` | GET | `/api/categories` |
| `categories()->store($data)` | POST | `/api/categories` |

## Flare

| API PHP | HTTP | URI |
| --- | --- | --- |
| `playlists($id)->show()` | GET | `/api/playlists/{id}` |
| `playlists($id)->items()->index()` | GET | `/api/playlists/{id}/items` |
| `stations()->groups($groupUri)->show()` | GET | `/api/stations/groups/{groupUri}` |
| `stations()->groups($groupUri)->destroy()` | DELETE | `/api/stations/groups/{groupUri}` |
| `stations()->groups()->invalidateCache()` | POST | `/api/stations/groups/cache/invalidate` |

## Pulse

| API PHP | HTTP | URI |
| --- | --- | --- |
| `groups()->index($filters)` | GET | `/api/ignis/groups` |
| `groups()->catalog()->index($filters)` | GET | `/api/groups/catalog` |
| `groups()->stationCache()->invalidate($groupUris)` | POST | `/api/groups/station-cache/invalidate` |

El último método convierte `$groupUris` al campo HTTP `uri_groups`.

## Ignis

| API PHP | HTTP | URI |
| --- | --- | --- |
| `externalGroups($externalGroupId)->campaigns()->index()` | GET | `/api/external-groups/{externalGroupId}/campaigns` |
| `externalGroups($externalGroupId)->campaigns($campaignId)->show()` | GET | `/api/external-groups/{externalGroupId}/campaigns/{campaignId}` |

Ambas operaciones devuelven el envelope de Caronte sin DTOs ni desempaquetado especial.

## Ruta inbound de grupos Ignis

Apollo es outbound por defecto. Con `APOLLO_IGNIS_GROUPS_ENABLED=true`, la aplicación host expone `GET /api/ignis/groups`, protegida por `caronte.application:tenant_required`. La aplicación debe enlazar `Ometra\Apollo\Sdk\Contracts\IgnisGroupContract`.

La ruta y middleware se configuran en `config/apollo.php` bajo `ignis_groups`.

## Páginas de error

Las vistas fallback se desactivan con `APOLLO_ERROR_PAGES_ENABLED=false` y se publican con:

```bash
php artisan vendor:publish --tag=apollo-error-pages
```
