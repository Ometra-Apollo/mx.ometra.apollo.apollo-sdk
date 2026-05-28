<?php

declare(strict_types=1);

use Ometra\Apollo\Sdk\Modules\Flare\Resources\StationsResource;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../Proteus/RecordingApolloHttpClient.php';

final class FlareStationsRoutesTest extends TestCase
{
    public function testStationsIndexUsesExpectedEndpoint(): void
    {
        $client = new RecordingApolloHttpClient();
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

    public function testStationsShowUsesExpectedEndpoint(): void
    {
        $client = new RecordingApolloHttpClient();
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
}
