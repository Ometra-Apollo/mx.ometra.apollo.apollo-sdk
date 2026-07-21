# Apollo SDK v5.0.0

Paquete: `ometra/apollo-sdk`
Configuración: `config/apollo.php`

Apollo SDK v5 organiza la API por colecciones, recursos ligados y subrecursos de dominio. Es una actualización breaking sin aliases.

## Principales cambios

- Media y directorios se seleccionan con `media($id)` y `directories($id)`.
- Upload usa `store`; las eliminaciones usan `destroy`.
- LightPath usa `request`, `extend` y `revoke`.
- Directory application grants usan `request` y `revoke` sin tokens explícitos.
- Metadata vive debajo de media; la consulta global conservada es `media()->metadata()->values($key)`.
- Proteus resuelve automáticamente grants de aplicación para media.
- Flare, Pulse e Ignis usan subrecursos ligados coherentes.
- Las campañas Ignis devuelven el envelope Caronte y ya no crean DTOs.
- Se eliminaron APIs sin consumidores productivos y la configuración pública por módulo.

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
$media = Apollo::proteus()->media($mediaId)->show();

$lightPath = Apollo::proteus()
    ->media($mediaId)
    ->lightPath()
    ->request(extension: 'mp4', ttlSeconds: 3600);
```

Consulta [BREAKING_CHANGES.md](BREAKING_CHANGES.md) para migración y [docs/api-contract.md](docs/api-contract.md) para el contrato completo.

## Validación

```bash
composer test
composer lint
composer analyse
```
