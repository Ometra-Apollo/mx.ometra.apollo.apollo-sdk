<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Flare\Resources;

use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;

final class PlaylistsResource
{
    public function __construct(private readonly ApolloHttpClient $client) {}


    public function show(string $id): array
    {
        return $this->client->userRequest('GET', 'playlists/' . $id);
    }
       public function items(string $id): array
    {
        return $this->client->userRequest('GET', 'playlists/' . $id . '/items');
    }
}
