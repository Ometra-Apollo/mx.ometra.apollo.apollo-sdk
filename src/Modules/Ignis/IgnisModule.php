<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Ignis;

use Ometra\Apollo\Sdk\Core\Config\ModuleConfigResolver;
use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;
use Ometra\Apollo\Sdk\Modules\Ignis\Resources\GroupResource;

/**
 * Ignis module entrypoint.
 *
 * Exposed resources:
 * - groups()
 */
final class IgnisModule
{
    public function __construct(private readonly ModuleConfigResolver $configResolver) {}

    public function groups(string $groupId): GroupResource
    {
        return new GroupResource($this->client(), $groupId);
    }

    private function client(): ApolloHttpClient
    {
        return new ApolloHttpClient($this->configResolver->resolve('ignis')['base_url']);
    }
}
