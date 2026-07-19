<?php

declare(strict_types=1);

use Ometra\Apollo\Sdk\DTO\IgnisCampaignDetailDTO;
use Ometra\Apollo\Sdk\DTO\IgnisCampaignDTO;
use Ometra\Apollo\Sdk\DTO\IgnisCampaignPlayModifiersDTO;
use PHPUnit\Framework\TestCase;

final class IgnisCampaignDTOTest extends TestCase
{
    public function test_campaign_list_dto_filters_unexpected_fields(): void
    {
        $dto = IgnisCampaignDTO::fromArray([
            'id_campaign' => 11,
            'name' => 'Semillas de cambio',
            'dt_start' => '2026-06-26T06:00:00.000000Z',
            'dt_end' => '2026-07-08T06:00:00.000000Z',
            'play_modifiers' => ['frequency' => '3'],
            'status' => 'active',
            'extra' => 'ignore me',
        ]);

        self::assertSame([
            'id_campaign' => 11,
            'name' => 'Semillas de cambio',
            'dt_start' => '2026-06-26T06:00:00.000000Z',
            'dt_end' => '2026-07-08T06:00:00.000000Z',
            'play_modifiers' => ['frequency' => '3'],
        ], $dto->toArray());
    }

    public function test_campaign_detail_dto_filters_unexpected_nested_fields(): void
    {
        $dto = IgnisCampaignDetailDTO::fromArray([
            'id_campaign' => 11,
            'name' => 'Semillas de cambio',
            'dt_start' => '2026-06-26T06:00:00.000000Z',
            'dt_end' => '2026-07-08T06:00:00.000000Z',
            'contents' => [[
                'id_media' => '019f1506-363f-7005-a90c-248202fce00d',
                'type' => 'video',
                'media_type' => 'video',
                'ext' => 'mp4',
                'name' => 'Demo',
                'size' => 123,
                'content_schedules' => [[
                    'dt_start' => '2026-06-29T06:00:00.000000Z',
                    'dt_end' => '2026-07-03T06:00:00.000000Z',
                    'dow' => [1, 4, 5],
                    'timezone' => 'UTC',
                ]],
            ]],
            'client' => ['id' => 99],
        ]);

        self::assertSame([
            'id_campaign' => 11,
            'name' => 'Semillas de cambio',
            'dt_start' => '2026-06-26T06:00:00.000000Z',
            'dt_end' => '2026-07-08T06:00:00.000000Z',
            'contents' => [[
                'id_media' => '019f1506-363f-7005-a90c-248202fce00d',
                'type' => 'video',
                'media_type' => 'video',
                'ext' => 'mp4',
                'name' => 'Demo',
                'content_schedules' => [[
                    'dt_start' => '2026-06-29T06:00:00.000000Z',
                    'dt_end' => '2026-07-03T06:00:00.000000Z',
                    'dow' => ['1', '4', '5'],
                ]],
            ]],
        ], $dto->toArray());
    }

    public function test_campaign_play_modifiers_dto_filters_unexpected_fields(): void
    {
        $dto = IgnisCampaignPlayModifiersDTO::fromArray([
            'frequency' => 10,
            'unexpected' => 'ignore me',
        ]);

        self::assertSame([
            'frequency' => '10',
        ], $dto->toArray());
    }
}
