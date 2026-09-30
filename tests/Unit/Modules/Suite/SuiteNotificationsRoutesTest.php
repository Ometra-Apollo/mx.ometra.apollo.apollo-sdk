<?php

declare(strict_types=1);

use Ometra\Apollo\Sdk\Modules\Suite\Resources\NotificationsResources;
use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../Proteus/RecordingApolloHttpClient.php';

final class SuiteNotificationsRoutesTest extends TestCase
{
    public function test_send_forwards_recipients_and_payload_to_the_suite(): void
    {
        $client = new RecordingApolloHttpClient;
        $resource = new NotificationsResources($client);
        $payload = [
            'users' => ['user-a'],
            'groups' => [],
            'title' => 'Aviso',
            'description' => 'Contenido',
            'excluded' => [],
        ];

        $resource->send($payload);

        self::assertSame([
            'auth' => 'user',
            'method' => 'POST',
            'endpoint' => 'notifications',
            'payload' => $payload,
            'query' => [],
            'raw' => false,
        ], $client->lastRequest);
    }

    public function test_read_operations_use_the_matching_notification_routes(): void
    {
        $client = new RecordingApolloHttpClient;
        $resource = new NotificationsResources($client);

        $resource->read(42);
        self::assertSame('notifications/42/read', $client->lastRequest['endpoint']);
        self::assertSame('POST', $client->lastRequest['method']);

        $resource->readAll();
        self::assertSame('notifications/read-all', $client->lastRequest['endpoint']);
        self::assertSame('POST', $client->lastRequest['method']);
    }

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
