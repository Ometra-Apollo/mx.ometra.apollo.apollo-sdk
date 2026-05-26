<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Flare;

use Ometra\Apollo\Sdk\Core\Config\ModuleConfigResolver;

final class FlareModule
{
    public function __construct(private readonly ModuleConfigResolver $configResolver)
    {
    }

    /**
     * @return array{base_url_env: string, base_url: string}
     */
    public function config(): array
    {
        return $this->configResolver->resolve('flare');
    }
}
