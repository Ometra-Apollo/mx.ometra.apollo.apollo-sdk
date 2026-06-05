<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Flare;

use Ometra\Apollo\Sdk\Core\Config\ModuleConfigResolver;
use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;
use Ometra\Apollo\Sdk\Modules\Flare\Resources\StationsResource;
use Ometra\Apollo\Sdk\Modules\Flare\Resources\PlaylistsResource;

/**
 * Flare module entrypoint.
 *
 * Exposed resources:
 * - stations()
 * - playlists()
 */
final class FlareModule
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
    /**
     * @return array{base_url_env: string, base_url: string}
     */
    public function config(): array
    {
        return $this->configResolver->resolve('flare');
    }

    public function stations(): StationsResource
    {
        return new StationsResource($this->client());
    }

    public function playlists(): PlaylistsResource
    {
        return new PlaylistsResource($this->client());
    }

    private function client(): ApolloHttpClient
    {
        if ($this->client === null) {
            $this->client = new ApolloHttpClient(
                $this->config()['base_url'],
                $this->asApplication,
            );
        }

        return $this->client;
    }
}
