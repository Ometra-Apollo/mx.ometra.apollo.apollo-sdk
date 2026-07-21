<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Flare\Resources;

use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;

final class PlaylistItemsResource
{
    public function __construct(
        private readonly ApolloHttpClient $client,
        private readonly string $playlistId,
    ) {}

    /** @return array<array-key, mixed> */
    public function index(): array
    {
        return $this->client->userRequest('GET', 'playlists/'.$this->playlistId.'/items');
    }
}
