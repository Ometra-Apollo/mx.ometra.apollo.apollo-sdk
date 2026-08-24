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
    private ?ApolloHttpClient $client = null;

    private bool $asApplication = false;

    public function __construct(private readonly ModuleConfigResolver $configResolver) {}

    public function asApplication(): static
    {
        $clone = clone $this;
        $clone->asApplication = true;
        $clone->client = null;

        return $clone;
    }

    public function groups(string $groupId): GroupResource
    {
        return new GroupResource($this->client(), $groupId);
    }

    private function client(): ApolloHttpClient
    {
        if ($this->client === null) {
            $this->client = new ApolloHttpClient(
                $this->configResolver->resolve('ignis')['base_url'],
                $this->asApplication,
            );
        }

        return $this->client;
    }
}
