<?php

declare(strict_types=1);

use Ometra\Apollo\Sdk\Modules\Ignis\Resources\CampaignsResource;
use Ometra\Apollo\Sdk\Modules\Ignis\Resources\ContentHitsResource;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../Proteus/RecordingApolloHttpClient.php';

final class IgnisResourceRoutesTest extends TestCase
{
    public function testCampaignsByExternalGroupUsesExpectedEndpoint(): void
    {
        $client = new RecordingApolloHttpClient();
        $resource = new CampaignsResource($client);

        $resource->byExternalGroup('group-1');

        self::assertSame([
            'auth' => 'application',
            'method' => 'GET',
            'endpoint' => 'external-groups/group-1/campaigns',
            'payload' => [],
            'query' => [],
            'raw' => false,
        ], $client->lastRequest);
    }

    public function testCampaignShowUsesExpectedEndpoint(): void
    {
        $client = new RecordingApolloHttpClient([
            'id_campaign' => 11,
            'name' => 'Campaign',
            'dt_start' => '2026-07-15T00:00:00Z',
            'dt_end' => '2026-07-16T00:00:00Z',
            'contents' => [],
        ]);
        $resource = new CampaignsResource($client);

        $resource->show('group-1', 11);

        self::assertSame([
            'auth' => 'application',
            'method' => 'GET',
            'endpoint' => 'external-groups/group-1/campaigns/11',
            'payload' => [],
            'query' => [],
            'raw' => false,
        ], $client->lastRequest);
    }

    public function testContentHitsReportUsesExpectedEndpoint(): void
    {
        $client = new RecordingApolloHttpClient();
        $resource = new ContentHitsResource($client);

        $report = [[
            'content_id' => 'content-1',
            'hits' => 10,
        ]];

        $resource->report($report);

        self::assertSame([
            'auth' => 'application',
            'method' => 'POST',
            'endpoint' => 'content-hits',
            'payload' => $report,
            'query' => [],
            'raw' => false,
        ], $client->lastRequest);
    }
}
