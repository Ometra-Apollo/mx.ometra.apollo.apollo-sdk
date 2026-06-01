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
 */
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
        return new ApolloHttpClient($this->config()['base_url']);
    }
}
