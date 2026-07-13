# Apollo SDK

Cliente Laravel/PHP modular para consumir Proteus, Pulse, Flare e Ignis con autenticacion compartida de Caronte.

## Instalacion

```bash
composer require ometra/apollo-sdk
```

Publica la configuracion si necesitas sobrescribir las URLs de modulos:

```bash
php artisan vendor:publish --tag=apollo-config
```

El archivo publicado es `config/apollo.php`.

## Paginas de error

Apollo registra automaticamente paginas HTML para los errores HTTP comunes de
Laravel (`401`, `403`, `404`, `419`, `429`, `500`, `503`). El SDK agrega sus
vistas como fallback, por lo que cualquier archivo local en
`resources/views/errors` mantiene prioridad.

Para deshabilitar este fallback:

```env
APOLLO_ERROR_PAGES_ENABLED=false
```

Si necesitas personalizar las vistas dentro de la app host, publicalas con:

```bash
php artisan vendor:publish --tag=apollo-error-pages
```

Tambien puedes publicar configuracion y paginas de error juntas:

```bash
php artisan vendor:publish --tag=apollo
```

## Configuracion

Apollo solo configura URLs por modulo; la autenticacion sigue viviendo en el SDK de Caronte.

```env
PROTEUS_BASE_URL=https://proteus.example.com/api
PULSE_BASE_URL=https://pulse.example.com/api
FLARE_BASE_URL=https://flare.example.com/api
IGNIS_BASE_URL=https://ignis.example.com/api
```

Las llamadas HTTP usan el contrato de Caronte y agregan, segun el tipo de request:

- `X-Application-Token`
- `X-Group-Token` cuando existe
- `X-User-Token` en llamadas de usuario
- `X-Tenant-Id` desde `TenantContext` cuando existe

## Uso modular

```php
use Ometra\Apollo\Sdk\Facades\Apollo;

$directories = Apollo::proteus()->directories()->index();

$media = Apollo::proteus()->media()->upload([
    'type' => 'image',
    'directory_id' => $directoryId,
    'media' => [$request->file('image')],
    'metadata' => [
        'source' => 'apollo',
    ],
]);

Apollo::proteus()->media()->setMetadata($mediaId, [
    'metadata' => [
        'title' => 'Hero image',
    ],
]);

$images = Apollo::proteus()->media()->index(['type' => 'image']);

$lightPath = Apollo::proteus()->media()->lightPathUrl($mediaId, [
    'ext' => 'mp4',
    'url_ttl_seconds' => 3600,
]);

$stations = Apollo::flare()->stations()->index(['country' => 'mx']);

$campaigns = Apollo::ignis()->campaigns()->byGroup('group-1');

Apollo::ignis()->contentHits()->report([
    ['content_id' => 'content-1', 'hits' => 10],
]);

$groups = Apollo::pulse()->groups()->index();
```

### URLs LightPath para media

LightPath se integra desde el recurso de media de Proteus porque Proteus es la
autoridad de permisos y formatos. El SDK solo solicita una URL temporal; no
valida tokens ni habla con nodos LightPath.

```php
$response = Apollo::proteus()->media()->lightPathUrl($mediaId, [
    'ext' => 'mp4',
    'url_ttl_seconds' => 3600,
]);

$url = $response['data']['url'];
```

Opciones:

- `ext`: formato a entregar. Si se omite, Proteus usa el formato default.
- `url_ttl_seconds`: vigencia de la URL. Proteus aplica su maximo configurado.

### Miniaturas de media

Para solicitar la miniatura de un recurso de media, usa `MediaResource::thumbnail()`.

```php
$response = Apollo::proteus()->media()->thumbnail($mediaId);
```

Esta llamada aprovecha el mismo endpoint de descarga y solicita el formato
`thumb` mediante `ext=thumb`.

La URL generada tiene forma `https://lightpath.example.com/m/{token}`. El token
opaco es la unica credencial y quien tenga la URL puede consumirla hasta
`url_expires_at`.

### Uso en jobs y contextos sin usuario

Cuando necesites hacer llamadas desde un job, un comando o cualquier contexto donde no haya sesion de usuario activa, usa `asApplication()`. Esto fuerza a todas las operaciones del modulo a usar autenticacion de aplicacion (sin `X-User-Token`) en lugar de autenticacion de usuario:

```php
// En un job — sin sesion HTTP, sin token de usuario
$proteus = Apollo::proteus()->asApplication();

$proteus->media()->index(['type' => 'audio']);
$proteus->directories()->show($directoryId);
```

Tambien puedes inyectar el entrypoint principal:

```php
use Ometra\Apollo\Sdk\Apollo;

public function __invoke(Apollo $apollo): array
{
    return $apollo->proteus()->media()->index(['type' => 'image']);
}
```

Pulse expone `groups()->index()` sobre el endpoint `ignis/groups`.

## Exposicion de grupos Ignis (inbound, opt-in)

Apollo es solo outbound por defecto. Cuando lo habilitas, el SDK registra una ruta inbound `GET /{prefix}/groups` en la app host para que clientes externos (incluido Pulse) puedan descubrir los grupos de la app. No agrega llamadas HTTP hacia Ignis.

### Habilitar la ruta

La ruta esta deshabilitada por defecto. Habilitala con una variable de entorno:

```env
APOLLO_IGNIS_GROUPS_ENABLED=true
```

O publica la configuracion y edita `config/apollo.php`:

```php
'ignis_groups' => [
    'enabled' => true,
    'implementation' => \Ometra\Apollo\Sdk\Test\DummyGroup::class,
    'route_prefix' => 'api/ignis',
    'middleware' => ['caronte.application:tenant_required'],
],
```

Con `enabled=false` (por defecto) no se registra ninguna ruta y `GET /api/ignis/groups` devuelve `404`.

### Proveer la implementacion del contrato

El SDK trae `Ometra\Apollo\Sdk\Test\DummyGroup` como implementacion runnable que devuelve un grupo de prueba. Para exponer grupos reales, crea una clase que implemente `Ometra\Apollo\Sdk\Contracts\IgnisGroupContract`:

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
                'name' => 'Mi grupo',
                'external_id' => 'grupo-1',
                'media_type' => ['video', 'audio'],
                'play_modifiers' => ['frequency' => 2],
            ]),
        ];
    }
}
```

Apunta `implementation` a tu clase via variable de entorno (sin necesidad de publicar la config):

```env
APOLLO_IGNIS_GROUPS_IMPLEMENTATION=\App\Services\HostGroupProvider
```

O publica la configuracion y edita `config/apollo.php`:

```php
// config/apollo.php (override del host)
'ignis_groups' => [
    'enabled' => true,
    'implementation' => \App\Services\HostGroupProvider::class,
],
```

Si `implementation` queda vacio o la clase no implementa el contrato, el provider lanza `RuntimeException` al arrancar.

### Prefijo de ruta y middleware

El prefijo por defecto es `api/ignis` (la ruta queda en `GET /api/ignis/groups`). Cambialo con `route_prefix`:

```php
'ignis_groups' => [
    'enabled' => true,
    'route_prefix' => 'api/custom/ignis', // GET /api/custom/ignis/groups
],
```

La ruta esta protegida por el middleware `caronte.application:tenant_required`. Las peticiones sin contexto de tenant valido son rechazadas por Caronte (401/403) antes de llegar al controlador. Puedes overridear la pila de middleware con la clave `middleware`.

## API

El contrato completo esta en [docs/api-contract.md](docs/api-contract.md).

## Pruebas

```bash
composer test
```

La suite valida identidad Apollo, configuracion modular, autenticacion Caronte, rutas Proteus, ausencia de API flat y limpieza legacy.
