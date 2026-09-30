# Estrategia de pruebas de Apollo SDK

El SDK es una librería PHP/Laravel que consume las API de Suite, Proteus, Pulse, Flare e Ignis. `src/Core/Http/ApolloHttpClient.php` delega autenticación, contexto de usuario/tenant y transporte a Caronte. `src/Modules/*/Resources` construye rutas y payloads; `src/Providers/ApolloServiceProvider.php` registra configuración, componentes y una ruta inbound opcional. El SDK no persiste datos propios.

## Preparación y ejecución

```sh
composer install
composer validate --strict
composer run test
composer run analyse
composer run lint
```

Se requiere PHP `^8.4`. `composer.lock` fija `ometra/caronte-sdk` 8.9.0 y el manifiesto exige `^8.9.0`; compruebe la versión instalada si falla un método heredado de Caronte. `phpunit.xml` descubre `tests/Feature` y `tests/Unit`. La mayoría de pruebas de recursos usa `RecordingApolloHttpClient` para verificar método, ruta, payload, query y modo de autenticación sin red. `ApolloHttpClientTest` usa `Http::fake()` para inspeccionar cabeceras y transporte reales del SDK de Caronte. La prueba del validador de sesión crea un workspace temporal de siete aplicaciones.

## Matriz de cobertura

| Funcionalidad y fuente | Criticidad | Unit | Integration | API | Security | E2E | Estado |
| --- | --- | --- | --- | --- | --- | --- | --- |
| Cabeceras y transporte Caronte (`ApolloHttpClient`) | Crítica | — | `ApolloHttpClientTest` con `Http::fake()` | Requests JSON/raw/multipart | Tenant, grupo, usuario y modo aplicación | Pendiente | Contrato SDK local cubierto; red real pendiente |
| Selección de módulos y configuración (`Apollo`, `ModuleConfigResolver`) | Alta | `ModuleResolutionTest` | Provider tests | — | URL base configurada | Pendiente | Cobertura local; configuración real de consumidores pendiente |
| Proteus media, metadata, directorios y LightPath | Alta | Pruebas de rutas y forma Proteus | — | Método/ruta/payload del cliente | Autenticación de aplicación en transporte | Pendiente | No se prueba respuesta real de Proteus |
| Suite clientes, grupos, usuarios y notificaciones | Alta | Pruebas de forma/rutas Suite | — | Envío/lista/marcado de notificaciones; rutas de aplicaciones | Hereda contexto Caronte | Pendiente | Contrato de notificaciones local; otros errores/variantes pendientes |
| Flare, Pulse e Ignis | Alta | Pruebas de forma/rutas por módulo | — | Métodos/rutas | Hereda contexto Caronte | Pendiente | Productores reales pendientes |
| Ruta inbound Ignis (`IgnisGroupController`, `IgnisGroupContract`) | Alta | — | Provider tests de binding | Registro de ruta | Middleware `tenant_required` declarado | Pendiente | Rechazo real con Caronte pendiente |
| Páginas de error y UI publicada (`ApolloServiceProvider`) | Media | — | Provider tests | — | — | Pendiente | Contratos estructurales, sin navegador |
| Preset de sesión compartida (`bin/validate-apollo-session-config`) | Alta | — | `ApolloSessionConfigCommandTest` | CLI | Igualdad de sesión entre siete apps | Pendiente | Workspace simulado; ambientes reales pendientes |

Las 89 pruebas originales del checkout se ejecutan en menos de un segundo con las dependencias fijadas; dos casos adicionales protegen envío y marcado de notificaciones hacia Lumina. No se calculó cobertura de líneas. La suite contiene pruebas de identidad/documentación que detectan regresiones de contrato, pero no reemplazan pruebas de comportamiento de los servicios productores. No hay jobs ni colas propios del SDK.

## Cómo ampliar las pruebas

Para un recurso nuevo, use el cliente grabador en `tests/Unit/Modules` y compruebe verbo, endpoint, payload, query y modo usuario/aplicación. Para cambios de autenticación, use `Http::fake()` y compruebe cabeceras y ausencia de solicitudes inesperadas; no replique el código productivo en las aserciones. Para una ruta inbound, pruebe registro, binding y middleware y añada una prueba integrada con Caronte y un host Laravel real. Las pruebas deben crear su propio contenedor/fixture y no depender del orden. Las incompatibilidades con servicios externos se registran como **Requiere validación** hasta ejecutar un contrato productor-consumidor.
