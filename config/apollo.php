<?php

declare(strict_types=1);

$env = static fn (string $key, ?string $default = null): ?string => $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key) ?: $default;

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
];
