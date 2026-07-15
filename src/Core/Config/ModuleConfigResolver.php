<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Core\Config;

final class ModuleConfigResolver
{
    /**
     * @return array{base_url: string}
     */
    public function resolve(string $module): array
    {
        $moduleConfig = (array) config("apollo.modules.{$module}", []);

        return [
            'base_url' => (string) ($moduleConfig['base_url'] ?? ''),
        ];
    }
}
