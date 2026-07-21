<?php

declare(strict_types=1);

use Ometra\Apollo\Sdk\Modules\Flare\Resources\PlaylistItemsResource;
use Ometra\Apollo\Sdk\Modules\Flare\Resources\PlaylistsResource;
use Ometra\Apollo\Sdk\Modules\Flare\Resources\StationGroupResource;
use Ometra\Apollo\Sdk\Modules\Flare\Resources\StationGroupsCollectionResource;
use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../Proteus/RecordingApolloHttpClient.php';

final class FlareStationsRoutesTest extends TestCase
{
    public function test_bound_playlist_and_items_routes(): void
    {
        $client = new RecordingApolloHttpClient;
        $playlist = new PlaylistsResource($client, 'playlist-1');

        $playlist->show();
        self::assertSame('playlists/playlist-1', $client->lastRequest['endpoint']);

        $items = $playlist->items();
        self::assertInstanceOf(PlaylistItemsResource::class, $items);
        $items->index();
        self::assertSame('playlists/playlist-1/items', $client->lastRequest['endpoint']);
    }

    public function test_station_group_routes(): void
    {
        $client = new RecordingApolloHttpClient;
        $group = new StationGroupResource($client, 'group-1');

        $group->show();
        self::assertSame('stations/groups/group-1', $client->lastRequest['endpoint']);
        self::assertSame('GET', $client->lastRequest['method']);

        $group->destroy();
        self::assertSame('DELETE', $client->lastRequest['method']);

        (new StationGroupsCollectionResource($client))->invalidateCache();
        self::assertSame('stations/groups/cache/invalidate', $client->lastRequest['endpoint']);
        self::assertSame('POST', $client->lastRequest['method']);
    }
}
