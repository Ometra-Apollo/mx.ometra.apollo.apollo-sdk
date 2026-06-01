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

$stations = Apollo::flare()->stations()->index(['country' => 'mx']);

$campaigns = Apollo::ignis()->campaigns()->byGroup('group-1');

Apollo::ignis()->contentHits()->report([
    ['content_id' => 'content-1', 'hits' => 10],
]);

$groups = Apollo::pulse()->groups()->index();
```

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

## API

El contrato completo esta en [docs/api-contract.md](docs/api-contract.md).

## Pruebas

```bash
composer test
```

La suite valida identidad Apollo, configuracion modular, autenticacion Caronte, rutas Proteus, ausencia de API flat y limpieza legacy.
