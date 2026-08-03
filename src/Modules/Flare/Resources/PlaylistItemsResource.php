<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Flare\Resources;

use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;

/**
 * Resource for managing items within a playlist
 *
 * Provides methods to retrieve and manage media items
 * that belong to a specific playlist.
 */
final class PlaylistItemsResource
{
    /**
     * Initializes the playlist items resource
     *
     * @param ApolloHttpClient $client Apollo HTTP client
     * @param string $playlistId The playlist ID
     */
    public function __construct(
        private readonly ApolloHttpClient $client,
        private readonly string $playlistId,
    ) {}

    /**
     * Retrieves all items in the playlist
     *
     * @return array<array-key, mixed> List of media items in the playlist
     */
    public function index(): array
    {
        return $this->client->userRequest('GET', 'playlists/'.$this->playlistId.'/items');
    }

    /**
     * Accesses lightpath operations for playlist items
     *
     * @return PlaylistLightPathResource Resource for lightpath operations
     */
    public function lightpath(): PlaylistLightPathResource
    {
        return new PlaylistLightPathResource($this->client, $this->playlistId);
    }
}
