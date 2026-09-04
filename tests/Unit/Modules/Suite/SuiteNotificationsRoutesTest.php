<?php

declare(strict_types=1);

use Ometra\Apollo\Sdk\Modules\Suite\Resources\NotificationsResources;
use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../Proteus/RecordingApolloHttpClient.php';

final class SuiteNotificationsRoutesTest extends TestCase
{
    public function test_index_forwards_notification_query_parameters(): void
    {
        $client = new RecordingApolloHttpClient;
        $resource = new NotificationsResources($client);
        $query = [
            'filter' => 'deployment',
            'unread' => '1',
            'items_per_page' => 10,
            'page' => 2,
        ];

        $resource->index($query);

        self::assertSame([
            'auth' => 'user',
            'method' => 'GET',
            'endpoint' => 'notifications',
            'payload' => [],
            'query' => $query,
            'raw' => false,
        ], $client->lastRequest);
    }
}