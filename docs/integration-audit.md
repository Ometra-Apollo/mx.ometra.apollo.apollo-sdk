# Auditoría de integraciones Apollo SDK v5

Revisión: 2026-07-20. Paquete: `ometra/apollo-sdk`. Configuración compartida: `config/apollo.php`.

Variables requeridas:

- `PROTEUS_BASE_URL`
- `PULSE_BASE_URL`
- `FLARE_BASE_URL`
- `IGNIS_BASE_URL`

Ejemplo de colección:

```php
Apollo::proteus()->media()->index($filters);
```

## Consumidores

| Repositorio | Módulos | Migración v5 |
| --- | --- | --- |
| Aeris | Proteus media y LightPath | Recursos media ligados; upload con `store`; LightPath con `request` |
| Flare | Proteus media, metadata, directorios y LightPath; Pulse groups | Subrecursos ligados; grants con verbos de dominio; backfill eliminado |
| Ignis | Proteus media, directorios y categorías | Recursos ligados; URL base leída desde configuración |
| Proteus | Transporte y componentes compartidos de Apollo | Resolución automática de grants para media y eliminación de actualización de permisos |
| Pulse | Flare playlists/station groups; Ignis campaigns; Proteus LightPath | Recursos ligados y lectura explícita de `data` del envelope Ignis |

## Reglas verificadas

- Caronte es el único propietario de tokens, tenant y contexto de usuario.
- Los procesos sin sesión seleccionan `asApplication()`.
- Proteus resuelve el directory application grant por tenant, aplicación y cobertura del media.
- Ningún consumidor pasa un grant a `thumbnail()` o a `lightPath()->request()`.
- Las llamadas JSON conservan el envelope; sólo descargas y miniaturas reciben `Response`.
- Los consumidores usan camelCase; snake_case queda limitado a payloads HTTP y datos persistidos propios.
- No existe configuración pública por módulo.

El contrato de rutas está en [api-contract.md](api-contract.md) y la guía breaking en [../BREAKING_CHANGES.md](../BREAKING_CHANGES.md).
