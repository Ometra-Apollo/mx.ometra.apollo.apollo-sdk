<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Flare\Resources;

use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;

final class PlaylistsResource
{
    public function __construct(
        private readonly ApolloHttpClient $client,
        private readonly string $playlistId,
    ) {}

    /** @return array<array-key, mixed> */
    public function show(): array
    {
        return $this->client->userRequest('GET', 'playlists/'.$this->playlistId);
    }

    public function items(): PlaylistItemsResource
    {
        return new PlaylistItemsResource($this->client, $this->playlistId);
    }

    public function playlogs(): PlaylistPlaylogsResource
    {
        return new PlaylistPlaylogsResource($this->client, $this->playlistId);
    }
}
