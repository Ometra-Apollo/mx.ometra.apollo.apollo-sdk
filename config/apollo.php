<?php

declare(strict_types=1);

$env = static fn(string $key, ?string $default = null): ?string => $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key) ?: $default;

return [
    'modules' => [
        'proteus' => [
            'base_url_env' => 'PROTEUS_BASE_URL',
            'base_url' => $env('PROTEUS_BASE_URL'),
        ],
        'pulse' => [
            'base_url_env' => 'PULSE_BASE_URL',
            'base_url' => $env('PULSE_BASE_URL'),
        ],
        'flare' => [
            'base_url_env' => 'FLARE_BASE_URL',
            'base_url' => $env('FLARE_BASE_URL'),
        ],
        'ignis' => [
            'base_url_env' => 'IGNIS_BASE_URL',
            'base_url' => $env('IGNIS_BASE_URL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Ignis Groups Exposure (opt-in inbound route)
    |--------------------------------------------------------------------------
    |
    | When enabled, the SDK registers an inbound `GET /{prefix}/groups` route on
    | the host application. The route resolves a host-provided
    | `Ometra\Apollo\Sdk\Contracts\IgnisGroupContract` implementation from the
    | container and returns standardized `ExternalGroupDTO[]` JSON. The route is
    | protected by the `caronte.application:tenant_required` middleware alias.
    |
    | Note: the middleware alias is intentionally lowercase. Do not introduce
    | any uppercase Caronte-prefixed env vars in this file.
    |
    */

    'ignis_groups' => [
        'enabled' => env('APOLLO_IGNIS_GROUPS_ENABLED', false),
        'implementation' => env('APOLLO_IGNIS_GROUPS_IMPLEMENTATION', \Ometra\Apollo\Sdk\Test\DummyGroup::class),
        'route_prefix' => 'api/ignis',
        'middleware' => ['caronte.application:tenant_required'],
    ],
];
