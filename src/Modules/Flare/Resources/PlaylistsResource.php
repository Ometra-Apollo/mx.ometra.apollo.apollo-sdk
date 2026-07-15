<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Flare\Resources;

use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;

final class PlaylistsResource
{
    public function __construct(private readonly ApolloHttpClient $client) {}


    public function show(string $id_playlist): array
    {
        return $this->client->userRequest('GET', 'playlists/' . $id_playlist);
    }
       public function items(string $id_playlist): array
    {
        return $this->client->userRequest('GET', 'playlists/' . $id_playlist . '/items');
    }
}
