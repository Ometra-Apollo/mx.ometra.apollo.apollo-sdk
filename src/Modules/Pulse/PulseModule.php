<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Pulse;

use Ometra\Apollo\Sdk\Core\Config\ModuleConfigResolver;
use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;
use Ometra\Apollo\Sdk\Modules\Pulse\Resources\GroupsResource;

/**
 * Pulse module entrypoint.
 *
 * Exposed resources:
 * - groups()
 */
final class PulseModule
{
    public function __construct(private readonly ModuleConfigResolver $configResolver) {}

    /**
     * @return array{base_url: string}
     */
    public function config(): array
    {
        return $this->configResolver->resolve('pulse');
    }

    public function groups(): GroupsResource
    {
        return new GroupsResource($this->client());
    }

    private function client(): ApolloHttpClient
    {
        return new ApolloHttpClient($this->config()['base_url']);
    }
}
