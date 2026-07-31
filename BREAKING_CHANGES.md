# Migración a Apollo SDK v5

Apollo SDK v5 es una versión breaking sin aliases. El paquete sigue siendo `ometra/apollo-sdk`, usa `config/apollo.php` y requiere estas URLs:

```env
PROTEUS_BASE_URL=https://proteus.example.com/api
PULSE_BASE_URL=https://pulse.example.com/api
FLARE_BASE_URL=https://flare.example.com/api
IGNIS_BASE_URL=https://ignis.example.com/api
```

Actualiza la dependencia:

```json
"ometra/apollo-sdk": "^5.0.0"
```

## Recursos ligados

Los IDs pasan del método de acción al selector de recurso:

```php
// v5
Apollo::proteus()->media($mediaId)->show();
Apollo::proteus()->media($mediaId)->destroy();
Apollo::proteus()->directories($directoryId)->show();
```

Las colecciones conservan el selector sin ID:

```php
Apollo::proteus()->media()->index($filters);
Apollo::proteus()->media()->store($data);
Apollo::proteus()->directories()->index($filters);
```

## Cambios de nombres

| API 4.x | API 5 |
| --- | --- |
| `media()->upload($data)` | `media()->store($data)` |
| `media()->delete($id)` | `media($id)->destroy()` |
| `media()->lightPathUrl($id, $options)` | `media($id)->lightPath()->request($extension, $ttlSeconds)` |
| `lightPath()->extendGrant($id, $ttl)` | `lightPath($id)->extend($ttl)` |
| `lightPath()->deleteGrant($id)` | `lightPath($id)->revoke()` |
| `directories()->grantApplication(...)` | `directories($id)->applicationGrants()->request(...)` |
| `directories()->revokeApplicationGrant($id)` | `directories()->applicationGrants($id)->revoke()` |

No se conservan los nombres anteriores como aliases.

## Autenticación

Elimina cualquier token explícito de las llamadas. Caronte obtiene el usuario actual. Los procesos sin sesión deben usar `asApplication()` solamente en endpoints que admiten autenticación de aplicación.

Proteus ahora localiza automáticamente el directory application grant para media y LightPath. Elimina `directoryGrantId` de `thumbnail()` y de solicitudes LightPath.

## Metadata

```php
Apollo::proteus()->media()->metadata()->values($key);
Apollo::proteus()->media($mediaId)->metadata()->store($data);
Apollo::proteus()->media($mediaId)->metadata()->update($data);
Apollo::proteus()->media($mediaId)->metadata($key)->show();
Apollo::proteus()->media($mediaId)->metadata($key)->destroy();
```

El acceso de metadata a nivel de módulo y el helper separado de media fueron eliminados.

## Flare, Pulse e Ignis

```php
Apollo::flare()->playlists($playlistId)->items()->index();
Apollo::flare()->stations()->groups($groupUri)->show();

Apollo::pulse()->groups()->catalog()->index($filters);
Apollo::pulse()->groups()->stationCache()->invalidate($groupUris);

Apollo::ignis()->groups($groupId)->campaigns()->index();
Apollo::ignis()->groups($groupId)->campaigns($campaignId)->show();
```

Las campañas Ignis devuelven el envelope Caronte completo. El consumidor debe leer `['data']` cuando necesita solamente el contenido.

## APIs eliminadas

- Helpers de creación y wrappers sin consumidores productivos.
- Presets y content hits.
- Actualización de permisos de directory application grants.
- Configuración pública por módulo.
- DTOs de campañas Ignis.
- Backfill de Flare y su token resuelto almacenado.
- Cualquier variante que acepte o nombre explícitamente un token de usuario.

Ignis no usa un helper de URL de descarga; debe leer `apollo.modules.proteus.base_url` directamente.
