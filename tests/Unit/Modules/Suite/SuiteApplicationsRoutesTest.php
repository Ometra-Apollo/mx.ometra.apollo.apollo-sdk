<?php

declare(strict_types=1);

use Ometra\Apollo\Sdk\Modules\Suite\Resources\ApplicationsResource;
use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../Proteus/RecordingApolloHttpClient.php';

final class SuiteApplicationsRoutesTest extends TestCase
{
    public function test_index_uses_expected_endpoint_and_user_auth(): void
    {
        $client = new RecordingApolloHttpClient;
        $resource = new ApplicationsResource($client);

        $resource->index();

        self::assertSame([
            'auth' => 'user',
            'method' => 'GET',
            'endpoint' => 'users/applications',
            'payload' => [],
            'query' => [],
            'raw' => false,
        ], $client->lastRequest);
    }
}
