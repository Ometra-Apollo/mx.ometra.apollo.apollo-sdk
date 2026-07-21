<?php

declare(strict_types=1);

use Ometra\Apollo\Sdk\Modules\Pulse\Resources\GroupsResource;
use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../Proteus/RecordingApolloHttpClient.php';

final class PulseGroupsRoutesTest extends TestCase
{
    public function test_group_resources_use_expected_endpoints(): void
    {
        $client = new RecordingApolloHttpClient;
        $groups = new GroupsResource($client);

        $groups->index(['media_type' => 'audio']);
        self::assertSame('ignis/groups', $client->lastRequest['endpoint']);

        $groups->catalog()->index(['media_type' => 'audio']);
        self::assertSame('groups/catalog', $client->lastRequest['endpoint']);
        self::assertSame(['media_type' => 'audio'], $client->lastRequest['query']);

        $groups->stationCache()->invalidate(['group-1']);
        self::assertSame('groups/station-cache/invalidate', $client->lastRequest['endpoint']);
        self::assertSame(['uri_groups' => ['group-1']], $client->lastRequest['payload']);
    }
}
