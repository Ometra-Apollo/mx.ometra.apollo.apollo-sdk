<?php

declare(strict_types=1);

use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;
use Ometra\Apollo\Sdk\Modules\Ignis\Resources\CampaignsResource;
use PHPUnit\Framework\TestCase;

final class IgnisCampaignsResourceResponseTest extends TestCase
{
    public function test_by_external_group_returns_only_dto_fields(): void
    {
        $client = new class extends ApolloHttpClient
        {
            public function __construct()
            {
                parent::__construct('https://ignis.test/api');
            }

            public function applicationRequest(string $method, string $endpoint, array $payload = [], array $query = [], ?string $userToken = null): array
            {
                return [
                    'status' => 200,
                    'message' => 'ok',
                    'data' => [[
                        'id_campaign' => 11,
                        'name' => 'Semillas de cambio',
                        'dt_start' => '2026-06-26T06:00:00.000000Z',
                        'dt_end' => '2026-07-08T06:00:00.000000Z',
                        'play_modifiers' => ['frequency' => '3'],
                        'unexpected' => 'drop',
                    ]],
                    'errors' => [],
                ];
            }
        };

        $resource = new CampaignsResource($client);

        self::assertSame([[
            'id_campaign' => 11,
            'name' => 'Semillas de cambio',
            'dt_start' => '2026-06-26T06:00:00.000000Z',
            'dt_end' => '2026-07-08T06:00:00.000000Z',
            'play_modifiers' => ['frequency' => '3'],
        ]], $resource->byExternalGroup('group-1'));
    }

    public function test_show_returns_only_dto_fields(): void
    {
        $client = new class extends ApolloHttpClient
        {
            public function __construct()
            {
                parent::__construct('https://ignis.test/api');
            }

            public function applicationRequest(string $method, string $endpoint, array $payload = [], array $query = [], ?string $userToken = null): array
            {
                return [
                    'status' => 200,
                    'message' => 'ok',
                    'data' => [
                        'id_campaign' => 11,
                        'name' => 'Semillas de cambio',
                        'dt_start' => '2026-06-26T06:00:00.000000Z',
                        'dt_end' => '2026-07-08T06:00:00.000000Z',
                        'contents' => [[
                            'id_media' => '019f1506-363f-7005-a90c-248202fce00d',
                            'type' => 'video',
                            'name' => 'Demo',
                            'duration' => 50,
                            'content_schedules' => [[
                                'dt_start' => '2026-06-29T06:00:00.000000Z',
                                'dt_end' => '2026-07-03T06:00:00.000000Z',
                                'dow' => [1, 4, 5],
                                'unexpected' => true,
                            ]],
                        ]],
                        'unexpected' => 'drop',
                    ],
                    'errors' => [],
                ];
            }
        };

        $resource = new CampaignsResource($client);

        self::assertSame([
            'id_campaign' => 11,
            'name' => 'Semillas de cambio',
            'dt_start' => '2026-06-26T06:00:00.000000Z',
            'dt_end' => '2026-07-08T06:00:00.000000Z',
            'contents' => [[
                'id_media' => '019f1506-363f-7005-a90c-248202fce00d',
                'type' => 'video',
                'media_type' => 'video',
                'ext' => '',
                'name' => 'Demo',
                'content_schedules' => [[
                    'dt_start' => '2026-06-29T06:00:00.000000Z',
                    'dt_end' => '2026-07-03T06:00:00.000000Z',
                    'dow' => ['1', '4', '5'],
                ]],
            ]],
        ], $resource->show('group-1', 11));
    }
}
