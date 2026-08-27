# Apollo SDK

SDK modular para Laravel/PHP que consume Proteus, Pulse, Flare e Ignis con la autenticación compartida de Caronte.

Requiere PHP 8.4 o posterior.

## Instalación

```bash
composer require ometra/apollo-sdk:^6.0
php artisan vendor:publish --tag=apollo-config
```

La configuración se publica en `config/apollo.php`:

```env
PROTEUS_BASE_URL=https://proteus.example.com/api
PULSE_BASE_URL=https://pulse.example.com/api
FLARE_BASE_URL=https://flare.example.com/api
IGNIS_BASE_URL=https://ignis.example.com/api
```

## Autenticación

Apollo usa el transporte de `ometra/caronte-sdk`. Caronte obtiene el tenant, la aplicación y el usuario actuales; ningún método acepta tokens explícitos.

Las operaciones usan autenticación de usuario por defecto. En jobs o procesos sin sesión, selecciona autenticación de aplicación:

```php
$proteus = Apollo::proteus()->asApplication();

$media = $proteus->media()->index(['type' => 'image']);
$grant = $proteus->lightPath($grantId)->extend(3600);
```

`asApplication()` no concede permisos adicionales. Proteus resuelve el directory application grant que cubre al media usando el tenant y la aplicación de Caronte.

## Proteus

### Media

```php
$items = Apollo::proteus()->media()->index(['type' => 'image']);

$created = Apollo::proteus()->media()->store([
    'directory_id' => $directoryId,
    'media' => [$request->file('image')],
    'metadata' => ['source' => 'apollo'],
]);

$media = Apollo::proteus()->media($mediaId);

$detail = $media->show();
$download = $media->download('mp4');
$thumbnail = $media->thumbnail();
$deleted = $media->destroy();
```

`show()` no admite filtros. `download()` y `thumbnail()` devuelven `Illuminate\Http\Client\Response`; los demás métodos devuelven el envelope JSON de Caronte.

### Metadata

Los valores únicos de una clave se consultan como operación de colección:

```php
$values = Apollo::proteus()->media()->metadata()->values('artist');
```

La metadata editable pertenece a un media:

```php
$metadata = Apollo::proteus()->media($mediaId)->metadata();

$metadata->store(['metadata' => ['artist' => 'Ometra']]);
$metadata->update(['metadata' => ['artist' => 'Apollo']]);

$artist = Apollo::proteus()->media($mediaId)->metadata('artist')->show();
Apollo::proteus()->media($mediaId)->metadata('artist')->destroy();
```

### LightPath

La solicitud es síncrona: Proteus devuelve el grant y la URL ya emitidos.

```php
$response = Apollo::proteus()
    ->media($mediaId)
    ->lightPath()
    ->request(extension: 'mp4', ttlSeconds: 3600);

$grantId = $response['data']['id_lightpath_grant'];

Apollo::proteus()->lightPath($grantId)->extend(3600);
Apollo::proteus()->lightPath($grantId)->revoke();
```

En modo aplicación no se pasa un directory grant: Proteus lo localiza automáticamente.

### Directorios y application grants

```php
use Ometra\Apollo\Sdk\Modules\Proteus\Enums\DirectoryApplicationPermission;

$directories = Apollo::proteus()->directories()->index();
$created = Apollo::proteus()->directories()->store($data);

$directory = Apollo::proteus()->directories($directoryId);
$detail = $directory->show();
$directory->destroy();

$grant = $directory
    ->applicationGrants()
    ->request(
        clientReference: 'flare:playlist:42',
        permission: DirectoryApplicationPermission::READ,
    );

Apollo::proteus()
    ->asApplication()
    ->directories()
    ->applicationGrants($applicationGrantId)
    ->revoke();
```

Crear un grant requiere el usuario actual de Caronte. Revocarlo admite autenticación de aplicación.

### Categorías

```php
Apollo::proteus()->categories()->index();
Apollo::proteus()->categories()->store($data);
```

## Flare

```php
$playlist = Apollo::flare()->playlists($playlistId)->show();
$items = Apollo::flare()->playlists($playlistId)->items()->index();

$group = Apollo::flare()->stations()->groups($groupUri)->show();
Apollo::flare()->stations()->groups($groupUri)->destroy();
Apollo::flare()->stations()->groups()->invalidateCache();
```

## Pulse

```php
$groups = Apollo::pulse()->groups()->index($filters);
$catalog = Apollo::pulse()->groups()->catalog()->index($filters);
Apollo::pulse()->groups()->stationCache()->invalidate($groupUris);
```

## Ignis

```php
$campaigns = Apollo::ignis()
    ->groups($groupId)
    ->campaigns()
    ->index();

$campaign = Apollo::ignis()
    ->groups($groupId)
    ->campaigns($campaignId)
    ->show();
```

Ignis devuelve el envelope completo de Caronte; el SDK no lo desempaqueta ni crea DTOs de campaña.

## Páginas de error

Apollo registra vistas fallback para `401`, `403`, `404`, `419`, `429`, `500` y `503`. Las vistas de la aplicación host conservan prioridad.

```env
APOLLO_ERROR_PAGES_ENABLED=false
```

```bash
php artisan vendor:publish --tag=apollo-error-pages
```

## Componentes compartidos e integración inbound

Los componentes `AppMenu` y `DirectoryTree` se publican con `apollo-app-menu` y `apollo-directory-tree`. Consulta [docs/ui-components.md](docs/ui-components.md).

La ruta inbound opcional de grupos Ignis se habilita con `APOLLO_IGNIS_GROUPS_ENABLED=true`. La aplicación host debe implementar `Ometra\Apollo\Sdk\Contracts\IgnisGroupContract`; su configuración vive en `config/apollo.php`.

## Contrato y migración

- [Contrato HTTP y API pública](docs/api-contract.md)
- [Migración breaking a v5](BREAKING_CHANGES.md)
- [Auditoría de integraciones](docs/integration-audit.md)

## Validación

```bash
composer test
composer lint
composer analyse
```

### Configuración de sesión compartida

Apollo SDK incluye la definición compartida de Aeris, Flare, Ignis, Lume, Proteus y Pulse. Su wrapper usa el validador
genérico proporcionado por Caronte SDK, detecta el workspace padre y evita argumentos repetitivos:

```bash
vendor/bin/validate-apollo-session-config
```

Puede indicarse el workspace cuando no pueda detectarse desde el directorio actual:

```bash
vendor/bin/validate-apollo-session-config --workspace /srv/Ometra-Apollo
```

Los proyectos consumidores también exponen el alias:

```bash
composer apollo:session:validate
```

La definición está en `config/group-session-config.json`. Apollo SDK solo posee ese preset y el wrapper; el parser,
reglas, seguridad y códigos de salida pertenecen a Caronte SDK.
