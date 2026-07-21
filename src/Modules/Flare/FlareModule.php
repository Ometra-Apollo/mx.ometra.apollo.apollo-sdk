<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Flare;

use Ometra\Apollo\Sdk\Core\Config\ModuleConfigResolver;
use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;
use Ometra\Apollo\Sdk\Modules\Flare\Resources\PlaylistsResource;
use Ometra\Apollo\Sdk\Modules\Flare\Resources\StationsResource;

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

    public function stations(): StationsResource
    {
        return new StationsResource($this->client());
    }

    public function playlists(string $playlistId): PlaylistsResource
    {
        return new PlaylistsResource($this->client(), $playlistId);
    }

    private function client(): ApolloHttpClient
    {
        if ($this->client === null) {
            $this->client = new ApolloHttpClient(
                $this->configResolver->resolve('flare')['base_url'],
                $this->asApplication,
            );
        }

        return $this->client;
    }
}
