# Apollo SDK 6.1.0

Paquete: `ometra/apollo-sdk`
Configuración: `config/apollo.php`
Runtime: PHP 8.4 o posterior

Apollo SDK 6.1 agrega operaciones delegadas de Proteus sin cambios incompatibles.

## Cambios principales

- Las solicitudes de grants de aplicaciones de directorio pueden indicar una
  aplicación destino mediante `targetApplicationId`.
- `MediaCollectionResource::storeWithDirectoryGrant()` permite subir archivos
  usando un grant delegado de una aplicación de directorio.

## Configuración requerida

```env
PROTEUS_BASE_URL=https://proteus.example.com/api
PULSE_BASE_URL=https://pulse.example.com/api
FLARE_BASE_URL=https://flare.example.com/api
IGNIS_BASE_URL=https://ignis.example.com/api
```

## Ejemplo

```php
$items = Apollo::proteus()->media()->index($filters);

$grant = Apollo::proteus()
    ->directories($directoryId)
    ->applicationGrants()
    ->request($clientReference, $permission, $targetApplicationId);

$media = Apollo::proteus()->media()->storeWithDirectoryGrant(
    $file,
    $directoryId,
    $directoryApplicationGrantId,
    $metadata,
);
```

Consulta [docs/api-contract.md](docs/api-contract.md) para el contrato completo.

## Validación

```bash
composer test
composer lint
composer analyse
```
