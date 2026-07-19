<?php

declare(strict_types=1);

use Ometra\Apollo\Sdk\Modules\Flare\Resources\StationsResource;
use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../Proteus/RecordingApolloHttpClient.php';

final class FlareStationsRoutesTest extends TestCase
{
    public function test_stations_index_uses_expected_endpoint(): void
    {
        $client = new RecordingApolloHttpClient;
        $resource = new StationsResource($client);

        $resource->index(['country' => 'mx']);

        self::assertSame([
            'auth' => 'application',
            'method' => 'GET',
            'endpoint' => 'stations',
            'payload' => [],
            'query' => ['country' => 'mx'],
            'raw' => false,
        ], $client->lastRequest);
    }

    public function test_stations_show_uses_expected_endpoint(): void
    {
        $client = new RecordingApolloHttpClient;
        $resource = new StationsResource($client);

        $resource->show('station-1');

        self::assertSame([
            'auth' => 'application',
            'method' => 'GET',
            'endpoint' => 'stations/station-1',
            'payload' => [],
            'query' => [],
            'raw' => false,
        ], $client->lastRequest);
    }

    public function test_station_group_routes_use_application_authentication(): void
    {
        $client = new RecordingApolloHttpClient;
        $resource = new StationsResource($client);

        $resource->showByGroup('group-1');
        self::assertSame('stations/groups/group-1', $client->lastRequest['endpoint']);
        self::assertSame('GET', $client->lastRequest['method']);

        $resource->assignGroups('12', ['group-1', 'group-2']);
        self::assertSame('stations/12/groups/assign', $client->lastRequest['endpoint']);
        self::assertSame('PUT', $client->lastRequest['method']);
        self::assertSame(['uri_groups' => ['group-1', 'group-2']], $client->lastRequest['payload']);

        $resource->detachGroup('group-1');
        self::assertSame('stations/groups/group-1', $client->lastRequest['endpoint']);
        self::assertSame('DELETE', $client->lastRequest['method']);

        $resource->invalidateGroupCatalogCache();
        self::assertSame('stations/groups/cache/invalidate', $client->lastRequest['endpoint']);
        self::assertSame('POST', $client->lastRequest['method']);
    }
}
