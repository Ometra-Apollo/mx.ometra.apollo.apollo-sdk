# Apollo SDK 6.0.0

Paquete: `ometra/apollo-sdk`
Configuración: `config/apollo.php`
Runtime: PHP 8.4 o posterior

Apollo SDK 6 unifica el contrato de grupos de Ignis con el resto de Apollo. Es una actualización breaking sin aliases de compatibilidad.

## Cambios principales

- `Apollo::ignis()->externalGroups($id)` cambia a `Apollo::ignis()->groups($id)`.
- Las campañas de grupo usan `/api/groups/{groupId}/campaigns`.
- `ExternalGroupResource` se reemplaza por `GroupResource`.
- El método y endpoint anteriores fueron eliminados.

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

$campaigns = Apollo::ignis()
    ->groups($groupId)
    ->campaigns()
    ->index();

$campaign = Apollo::ignis()
    ->groups($groupId)
    ->campaigns($campaignId)
    ->show();
```

Consulta [BREAKING_CHANGES.md](BREAKING_CHANGES.md) para la migración y [docs/api-contract.md](docs/api-contract.md) para el contrato completo.

## Validación

```bash
composer test
composer lint
composer analyse
```
