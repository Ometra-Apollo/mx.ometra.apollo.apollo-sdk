<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Ignis;

use Ometra\Apollo\Sdk\Core\Config\ModuleConfigResolver;
use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;
use Ometra\Apollo\Sdk\Modules\Ignis\Resources\ExternalGroupResource;

/**
 * Ignis module entrypoint.
 *
 * Exposed resources:
 * - externalGroups()
 */
final class IgnisModule
{
    public function __construct(private readonly ModuleConfigResolver $configResolver) {}

    public function externalGroups(string $externalGroupId): ExternalGroupResource
    {
        return new ExternalGroupResource($this->client(), $externalGroupId);
    }

    private function client(): ApolloHttpClient
    {
        return new ApolloHttpClient($this->configResolver->resolve('ignis')['base_url']);
    }
}
