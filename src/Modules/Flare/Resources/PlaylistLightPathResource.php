<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Flare\Resources;

use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;

/**
 * Resource for lightpath operations on playlists
 *
 * Provides methods to manage and refresh the lightpath
 * of media items within a specific playlist.
 */
final class PlaylistLightPathResource
{
    /**
     * Initializes the lightpath resource for a playlist
     *
     * @param ApolloHttpClient $client Apollo HTTP client
     * @param string $playlistId The playlist ID
     */
    public function __construct(
        private readonly ApolloHttpClient $client,
        private readonly string $playlistId,
    ) {}

    /**
     * Refreshes the lightpath for a media item
     *
     * Performs a POST request to the refresh-lightpath endpoint
     * to update the lightpath of the specified item.
     *
     * @param string $mediaId The ID of the media item to refresh
     *
     * @return array<array-key, mixed> Server response with the refreshed lightpath
     */
    public function refresh(string $mediaId): array
    {
        return $this->client->applicationRequest(
            'POST',
            'playlists/'.$this->playlistId.'/items/'.rawurlencode($mediaId).'/refresh-lightpath',
        );
    }
}
