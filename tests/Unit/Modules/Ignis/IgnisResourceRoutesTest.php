<?php

declare(strict_types=1);

use Ometra\Apollo\Sdk\Modules\Ignis\Resources\CampaignCollectionResource;
use Ometra\Apollo\Sdk\Modules\Ignis\Resources\CampaignResource;
use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../Proteus/RecordingApolloHttpClient.php';

final class IgnisResourceRoutesTest extends TestCase
{
    public function test_campaign_collection_returns_caronte_envelope(): void
    {
        $client = new RecordingApolloHttpClient(['campaigns' => []]);
        $response = (new CampaignCollectionResource($client, 'group-1'))->index();

        self::assertSame('external-groups/group-1/campaigns', $client->lastRequest['endpoint']);
        self::assertSame([
            'status' => 200,
            'message' => 'ok',
            'data' => ['campaigns' => []],
            'errors' => [],
        ], $response);
    }

    public function test_bound_campaign_uses_expected_endpoint(): void
    {
        $client = new RecordingApolloHttpClient(['id_campaign' => 11]);
        $response = (new CampaignResource($client, 'group-1', 11))->show();

        self::assertSame('external-groups/group-1/campaigns/11', $client->lastRequest['endpoint']);
        self::assertSame([
            'status' => 200,
            'message' => 'ok',
            'data' => ['id_campaign' => 11],
            'errors' => [],
        ], $response);
    }
}
