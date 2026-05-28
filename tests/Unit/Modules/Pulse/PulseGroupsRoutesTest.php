<?php

declare(strict_types=1);

use Ometra\Apollo\Sdk\Modules\Pulse\Resources\GroupsResource;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../Proteus/RecordingApolloHttpClient.php';

final class PulseGroupsRoutesTest extends TestCase
{
    public function testGroupsIndexUsesExpectedEndpoint(): void
    {
        $client = new RecordingApolloHttpClient();
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
}
