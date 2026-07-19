<?php

declare(strict_types=1);

use Ometra\Apollo\Sdk\Modules\Pulse\Resources\GroupsResource;
use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../Proteus/RecordingApolloHttpClient.php';

final class PulseGroupsRoutesTest extends TestCase
{
    public function test_groups_index_uses_expected_endpoint(): void
    {
        $client = new RecordingApolloHttpClient;
        $resource = new GroupsResource($client);

        $resource->index(['active' => true]);

        self::assertSame([
            'auth' => 'application',
            'method' => 'GET',
            'endpoint' => 'ignis/groups',
            'payload' => [],
            'query' => ['active' => true],
            'raw' => false,
        ], $client->lastRequest);
    }

    public function test_group_catalog_and_cache_invalidation_use_expected_endpoints(): void
    {
        $client = new RecordingApolloHttpClient;
        $resource = new GroupsResource($client);

        $resource->catalog(['media_type' => 'audio']);
        self::assertSame('groups/catalog', $client->lastRequest['endpoint']);
        self::assertSame(['media_type' => 'audio'], $client->lastRequest['query']);

        $resource->invalidateStationCache(['group-1']);
        self::assertSame('groups/station-cache/invalidate', $client->lastRequest['endpoint']);
        self::assertSame(['uri_groups' => ['group-1']], $client->lastRequest['payload']);
    }
}
